<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	
	header('Content-Type: application/json');
	
	$orderId = 0;
	if(isset($_GET['orderId']))
	{
		$orderId = (int)$_GET['orderId'];
	}
	
	if(empty($orderId))
	{
		echo json_encode(array('success' => false, 'message' => 'Order ID is required'));
		exit();
	}
	
	// Fetch feedback for this order
	$feedbackQuery = "SELECT f.*, e.firstName, e.lastName 
					  FROM ocr_data_feedback f 
					  LEFT JOIN employee_details e ON f.userId = e.employeeId 
					  WHERE f.orderId = $orderId 
					  ORDER BY f.addedOn DESC, f.addedTime DESC";
	$feedbackResult = dbQuery($feedbackQuery);
	$feedbackCount = mysqli_num_rows($feedbackResult);
	
	$feedbackList = array();
	
	if($feedbackCount > 0)
	{
		while($feedbackRow = mysqli_fetch_assoc($feedbackResult))
		{
			$feedbackText = stripslashes($feedbackRow['feedbackText']);
			$feedbackFiles = json_decode($feedbackRow['feedbackFiles'], true);
			$employeeName = 'Unknown User';
			if(!empty($feedbackRow['firstName']) || !empty($feedbackRow['lastName']))
			{
				$employeeName = trim(($feedbackRow['firstName'] ?? '') . ' ' . ($feedbackRow['lastName'] ?? ''));
			}
			$feedbackDate = $feedbackRow['addedOn'];
			$feedbackTime = $feedbackRow['addedTime'];
			$feedbackDateTime = $feedbackDate . ' ' . $feedbackTime;
			
			$fileList = array();
			if(!empty($feedbackFiles) && is_array($feedbackFiles))
			{
				foreach($feedbackFiles as $file)
				{
					if(isset($file['path']) && file_exists($file['path']))
					{
						$fileId = base64_encode($feedbackRow['id'] . '_' . $file['savedName']);
						$fileLink = SITE_URL_EMPLOYEES . "/download-ocr-feedback-file.php?fileId=" . urlencode($fileId);
						
						$fileList[] = array(
							'originalName' => $file['originalName'],
							'size' => getFileSize($file['size']),
							'downloadUrl' => $fileLink
						);
					}
				}
			}
			
			$feedbackList[] = array(
				'feedbackId' => $feedbackRow['id'],
				'employeeName' => $employeeName,
				'feedbackText' => $feedbackText,
				'dateTime' => date('M d, Y h:i A', strtotime($feedbackDateTime)),
				'files' => $fileList
			);
		}
	}
	
	echo json_encode(array(
		'success' => true,
		'feedback' => $feedbackList,
		'count' => $feedbackCount
	));
	exit();
?>
