<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	
	$orderId = 0;
	if(isset($_GET['orderId']))
	{
		$orderId = (int)$_GET['orderId'];
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
		
		// Check if file exists - matching display-multiple-files1.php pattern
		if(file_exists($ocrFilePath))
		{
			$fileExists = true;
		}
	}
	
	header('Content-Type: application/json');
	echo json_encode(array('exists' => $fileExists));
	exit();
?>
