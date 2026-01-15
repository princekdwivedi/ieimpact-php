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
			$filePath = str_replace("/home/ieimpact", "", $filePath);
			
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
	
	// Return response
	header('Content-Type: application/json');
	
	if($curlError)
	{
		echo json_encode(array(
			'success' => false,
			'message' => 'API request failed: ' . $curlError,
			'request_data' => $apiData
		));
	}
	else
	{
		$responseData = json_decode($response, true);
		if($httpCode == 200)
		{
			echo json_encode(array(
				'success' => true,
				'message' => 'Files processed successfully',
				'response' => $responseData,
				'request_data' => $apiData
			));
		}
		else
		{
			echo json_encode(array(
				'success' => false,
				'message' => 'API returned error code: ' . $httpCode,
				'response' => $responseData,
				'request_data' => $apiData
			));
		}
	}
	exit();
?>
