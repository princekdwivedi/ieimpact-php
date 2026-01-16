<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/orders.php");
	
	$employeeObj	= new employee();
	$orderObj		= new orders();
	
	// Get orderId from request parameter
	$orderId = 0;
	if(isset($_GET['orderId']) || isset($_POST['orderId']))
	{
		$orderId = isset($_GET['orderId']) ? (int)$_GET['orderId'] : (int)$_POST['orderId'];
	}
	
	if(empty($orderId))
	{
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Order ID is required'));
		exit();
	}

	// Get memberId from order_all_files or members_orders table
	$memberId = 0;
	$query = "SELECT DISTINCT memberId FROM order_all_files WHERE orderId=$orderId AND isDeleted=0 LIMIT 1";
	$result = dbQuery($query);
	if(mysqli_num_rows($result))
	{
		$row = mysqli_fetch_assoc($result);
		$memberId = $row['memberId'];
	}
	else
	{
		// Try members_orders table
		$query = "SELECT memberId FROM members_orders WHERE orderId=$orderId AND isVirtualDeleted=0 LIMIT 1";
		$result = dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$row = mysqli_fetch_assoc($result);
			$memberId = $row['memberId'];
		}
		else
		{
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => 'Order not found'));
			exit();
		}
	}
	
	// Get all files for this order (uploadingFor=1 means customer uploaded files)
	$query = "SELECT order_all_files.*, folderId 
			  FROM order_all_files 
			  INNER JOIN members ON order_all_files.memberId=members.memberId 
			  WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." 
			  AND order_all_files.orderId=$orderId 
			  AND order_all_files.memberId=$memberId
			  AND order_all_files.isDeleted=0 
			  AND uploadingFor=1
			  ORDER BY fileId ASC";
	
	$result = dbQuery($query);
	
	$files = array();
	$resultPath = "";
	
	if(mysqli_num_rows($result))
	{
		$firstFile = true;
		while($row = mysqli_fetch_assoc($result))
		{
			$filePath = $row['excatFileNameInServer'];
			
			// Remove /home/ieimpact from path if present
			if(function_exists('stringReplace'))
			{
				$filePath = stringReplace("/home/ieimpact", "", $filePath);
			}
			else
			{
				$filePath = str_replace("/home/ieimpact", "", $filePath);
			}
			
			// Extract directory path (resultPath) from first file
			if($firstFile)
			{
				$resultPath = dirname($filePath);
				$firstFile = false;
			}
			
			// Add file to array
			$files[] = array(
				'filename' => $filePath
			);
		}
	}
	
	if(empty($files))
	{
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'No files found for this order'));
		exit();
	}
	
	// Prepare API request data
	$apiData = array(
		'resultPath' => $resultPath."/ocrFiles",
		'files' => $files
	);
	// print_r($apiData); die("dgdfgdf");
	// Call third-party API
	$apiUrl = 'https://ocrdev.ieimpact.com/file-reader/extract-files';
	
	$ch = curl_init($apiUrl);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiData));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Content-Length: ' . strlen(json_encode($apiData))
	));
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	
	$response = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$curlError = curl_error($ch);
	curl_close($ch);
	
	// Prepare response data
	$responseData = json_decode($response, true);
	$isSuccess = false;
	$responseMessage = '';
	$jobId = null;
	
	if($curlError)
	{
		$isSuccess = false;
		$responseMessage = 'API request failed: ' . $curlError;
		$finalResponse = array(
			'success' => false,
			'message' => $responseMessage,
			'request_data' => $apiData
		);
	}
	else
	{
		// HTTP 200 and 201 are both success codes (201 = Created)
		if($httpCode == 200 || $httpCode == 201)
		{
			// Extract jobId from response first
			if($responseData && is_array($responseData))
			{
				// Try different possible keys for jobId
				if(isset($responseData['jobId']))
				{
					$jobId = $responseData['jobId'];
				}
			}
			
			// Check if response indicates success (even if HTTP code is 201)
			$responseSuccess = isset($responseData['success']) ? $responseData['success'] : true;
			$responseStatus = isset($responseData['status']) ? $responseData['status'] : null;
			
			// If we have a jobId, it means processing was initiated successfully
			if($jobId || ($responseSuccess && ($responseStatus == 'pending' || $responseStatus == 'processing')))
			{
				$isSuccess = true;
				$responseMessage = isset($responseData['message']) ? $responseData['message'] : 'Files processing initiated';
			}
			else
			{
				$isSuccess = $responseSuccess;
				$responseMessage = isset($responseData['message']) ? $responseData['message'] : 'API request completed';
			}
			
			$finalResponse = array(
				'success' => $isSuccess,
				'message' => $responseMessage,
				'response' => $responseData,
				'request_data' => $apiData,
				'jobId' => $jobId
			);
		}
		else
		{
			// For other HTTP codes, still try to extract jobId in case it's in the response
			if($responseData && is_array($responseData))
			{
				if(isset($responseData['jobId']))
				{
					$jobId = $responseData['jobId'];
				}
			}
			
			// If we have jobId, treat as success (processing initiated)
			if($jobId)
			{
				$isSuccess = true;
				$responseMessage = isset($responseData['message']) ? $responseData['message'] : 'Files processing initiated';
				$finalResponse = array(
					'success' => true,
					'message' => $responseMessage,
					'response' => $responseData,
					'request_data' => $apiData,
					'jobId' => $jobId
				);
			}
			else
			{
				$isSuccess = false;
				$responseMessage = 'API returned error code: ' . $httpCode;
				$finalResponse = array(
					'success' => false,
					'message' => $responseMessage,
					'response' => $responseData,
					'request_data' => $apiData
				);
			}
		}
	}
	
	// Save status to file in resultPath
	if(!empty($resultPath))
	{
		$ocrFilesPath = $resultPath . '/ocrFiles';
		$statusFile = $ocrFilesPath . '/ocr-processing-status.json';
		
		// If jobId is still null, try to extract it from response one more time
		if(empty($jobId) && $responseData && is_array($responseData))
		{
			if(isset($responseData['jobId']))
			{
				$jobId = $responseData['jobId'];
			}
		}
		
		$statusData = array(
			'orderId' => $orderId,
			'success' => $isSuccess,
			'message' => $responseMessage,
			'httpCode' => $httpCode,
			'curlError' => $curlError ? $curlError : null,
			'timestamp' => date('Y-m-d H:i:s'),
			'response' => $responseData,
			'rawResponse' => $response,
			'jobId' => $jobId
		);
		
		// Ensure resultPath directory exists
		if(!is_dir($resultPath))
		{
			@mkdir($resultPath, 0755, true);
		}
		
		// Check if parent directory is writable (we need this to create subdirectories)
		if(!is_writable($resultPath))
		{
			// Try to make parent writable
			@chmod($resultPath, 0755);
		}
		
		// If ocrFiles directory exists but is not writable, try to fix it or recreate it
		if(is_dir($ocrFilesPath))
		{
			if(!is_writable($ocrFilesPath))
			{
				// Try to change permissions
				@chmod($ocrFilesPath, 0755);
				
				// If still not writable and parent is writable, try to remove and recreate
				if(!is_writable($ocrFilesPath) && is_writable($resultPath))
				{
					// Remove existing directory and recreate it (this ensures correct ownership)
					@rmdir($ocrFilesPath);
					@mkdir($ocrFilesPath, 0755, true);
				}
			}
		}
		else
		{
			// Create ocrFiles directory if it doesn't exist
			@mkdir($ocrFilesPath, 0755, true);
		}
		
		// Final check - if still not writable, try writing to parent directory instead
		$targetDir = $ocrFilesPath;
		if(!is_writable($ocrFilesPath))
		{
			// Fallback: write to resultPath instead of ocrFiles subdirectory
			$targetDir = $resultPath;
			$statusFile = $resultPath . '/ocr-processing-status.json';
		}
		
		// Ensure target directory is writable
		if(!is_writable($targetDir))
		{
			@chmod($targetDir, 0755);
		}
		
		// Prepare JSON data
		$jsonData = json_encode($statusData, JSON_PRETTY_PRINT);
		
		// Write status file - create if doesn't exist, overwrite if exists
		$writeResult = false;
		if(is_writable($targetDir))
		{
			// Try to write the file
			$writeResult = @file_put_contents($statusFile, $jsonData, LOCK_EX);
			
			// If file doesn't exist and write failed, try creating it first
			if($writeResult === false && !file_exists($statusFile))
			{
				// Try to create an empty file first
				$tempFile = @fopen($statusFile, 'w');
				if($tempFile !== false)
				{
					@fclose($tempFile);
					// Now try writing again
					$writeResult = @file_put_contents($statusFile, $jsonData, LOCK_EX);
				}
			}
			
			// Set file permissions after writing
			if($writeResult !== false && file_exists($statusFile))
			{
				@chmod($statusFile, 0644);
			}
		}
		
		// If file write failed, try to get more details
		if($writeResult === false)
		{
			$errorDetails = array(
				'file' => $statusFile,
				'file_exists' => file_exists($statusFile) ? 'yes' : 'no',
				'dir_exists' => is_dir($targetDir) ? 'yes' : 'no',
				'dir_writable' => is_writable($targetDir) ? 'yes' : 'no',
				'dir_permissions' => is_dir($targetDir) ? substr(sprintf('%o', fileperms($targetDir)), -4) : 'N/A',
				'parent_writable' => is_writable($resultPath) ? 'yes' : 'no',
				'disk_free_space' => disk_free_space($resultPath)
			);
			error_log("Failed to write OCR status file. Details: " . json_encode($errorDetails));
		}
	}
	
	// Return response
	header('Content-Type: application/json');
	echo json_encode($finalResponse);
	exit();
?>
