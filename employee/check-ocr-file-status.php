<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	
	$orderId = 0;
	$jobId = null;
	if(isset($_GET['orderId']))
	{
		$orderId = (int)$_GET['orderId'];
	}
	if(isset($_GET['jobId']))
	{
		$jobId = $_GET['jobId'];
	}
	
	if(empty($orderId))
	{
		header('Content-Type: application/json');
		echo json_encode(array('exists' => false, 'message' => 'Order ID is required'));
		exit();
	}
	
	$query = "SELECT * FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND orderId=$orderId AND isDeleted=0 LIMIT 1";
	$result = mysqli_query($db_conn, $query);
	
	$fileExists = false;
	$statusData = null;
	$statusResponse = null;
	
	if(mysqli_num_rows($result))
	{
		$row = mysqli_fetch_assoc($result);
		$downloadPath = $row['excatFileNameInServer'];
		
		// Match the exact pattern from display-multiple-files1.php
		// Use stringReplace if available, otherwise use str_replace
		if(function_exists('stringReplace'))
		{
			$downloadPath = stringReplace("/home/ieimpact", "", $downloadPath);
		}
		else
		{
			$downloadPath = str_replace("/home/ieimpact", "", $downloadPath);
		}
		
		$downloadFileName = "extracted-data.pdf";
		$downloadPathInfo = pathinfo($downloadPath);
		$ocrFilePath = $downloadPathInfo['dirname'] . "/ocrFiles/$downloadFileName";
		$statusFilePath = $downloadPathInfo['dirname'] . "/ocrFiles/ocr-processing-status.json";
		
		// Check status file for jobId if not provided
		if(empty($jobId) && file_exists($statusFilePath))
		{
			$statusContent = @file_get_contents($statusFilePath);
			if($statusContent)
			{
				$statusData = json_decode($statusContent, true);
				if(isset($statusData['jobId']) && !empty($statusData['jobId']))
				{
					$jobId = $statusData['jobId'];
				}
			}
		}
		
		// If jobId exists, check status API
		if(!empty($jobId))
		{
			$statusApiUrl = 'https://ocrdev.ieimpact.com/file-reader/extract-files/status/' . $jobId;
			$ch = curl_init($statusApiUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			
			$statusResponseRaw = curl_exec($ch);
			$statusHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$statusCurlError = curl_error($ch);
			curl_close($ch);
			
			if(!$statusCurlError && $statusHttpCode == 200)
			{
				$statusResponse = json_decode($statusResponseRaw, true);
				
				// Check if status indicates completion
				if($statusResponse && is_array($statusResponse))
				{
					$status = isset($statusResponse['status']) ? $statusResponse['status'] : 
							 (isset($statusResponse['data']['status']) ? $statusResponse['data']['status'] : null);
					
					// If status is 'completed' or 'success', check if file exists
					if(in_array(strtolower($status), array('completed', 'success', 'done', 'finished')))
					{
						if(file_exists($ocrFilePath))
						{
							$fileExists = true;
						}
					}
					// If status is 'failed' or 'error', mark as not existing
					elseif(in_array(strtolower($status), array('failed', 'error', 'cancelled')))
					{
						$fileExists = false;
					}
				}
			}
		}
		else
		{
			// No jobId, just check if file exists
			if(file_exists($ocrFilePath))
			{
				$fileExists = true;
			}
		}
	}
	
	header('Content-Type: application/json');
	$response = array(
		'exists' => $fileExists,
		'status' => $statusResponse
	);
	
	if($statusResponse)
	{
		$response['statusData'] = $statusResponse;
	}
	
	echo json_encode($response);
	exit();
?>
