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
	
	header('Content-Type: application/json');
	
	$employeeObj = new employee();
	$orderObj = new orders();
	
	$orderId = 0;
	$userId = $s_employeeId;
	$feedbackText = '';
	$feedbackFiles = array();
	
	// Get orderId from POST
	if(isset($_POST['orderId']))
	{
		$orderId = (int)$_POST['orderId'];
	}
	
	// Get feedback text
	if(isset($_POST['feedbackText']))
	{
		$feedbackText = trim($_POST['feedbackText']);
	}
	
	if(empty($orderId))
	{
		echo json_encode(array('success' => false, 'message' => 'Order ID is required'));
		exit();
	}
	
	// Check if feedback feature is enabled for this order (development mode - only for specific order)
	$allowedOrderId = 587801;
	$allowedCustomerId = 2437;
	
	// Get customerId from order
	$customerId = 0;
	$customerQuery = "SELECT memberId FROM members_orders WHERE orderId=$orderId AND isVirtualDeleted=0 LIMIT 1";
	$customerResult = dbQuery($customerQuery);
	if(mysqli_num_rows($customerResult))
	{
		$customerRow = mysqli_fetch_assoc($customerResult);
		$customerId = $customerRow['memberId'];
	}
	
	// Validate if feedback is allowed for this order
	if($orderId != $allowedOrderId || $customerId != $allowedCustomerId)
	{
		echo json_encode(array('success' => false, 'message' => 'Feedback feature is currently under development and only available for specific orders.'));
		exit();
	}
	
	if(empty($userId))
	{
		echo json_encode(array('success' => false, 'message' => 'User ID is required. Please login again.'));
		exit();
	}
	
	if(empty($feedbackText))
	{
		echo json_encode(array('success' => false, 'message' => 'Feedback text is required.'));
		exit();
	}
	
	// Get order file path to determine feedback files directory
	$query = "SELECT * FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND orderId=$orderId AND isDeleted=0 LIMIT 1";
	$result = dbQuery($query);
	
	$feedbackFilesPath = '';
	$feedbackFilesJson = array();
	
	if(mysqli_num_rows($result))
	{
		$row = mysqli_fetch_assoc($result);
		$downloadPath = $row['excatFileNameInServer'];
		
		// Remove /home/ieimpact from path if present
		if(function_exists('stringReplace'))
		{
			$downloadPath = stringReplace("/home/ieimpact", "", $downloadPath);
		}
		else
		{
			$downloadPath = str_replace("/home/ieimpact", "", $downloadPath);
		}
		
		$downloadPathInfo = pathinfo($downloadPath);
		$feedbackFilesPath = $downloadPathInfo['dirname'] . "/ocrFiles/feedbackFiles";
		
		// Create feedback files directory if it doesn't exist
		if(!is_dir($feedbackFilesPath))
		{
			@mkdir($feedbackFilesPath, 0755, true);
		}
		
		// Handle file uploads
		if(isset($_FILES['feedbackFiles']) && !empty($_FILES['feedbackFiles']['name'][0]))
		{
			$uploadedFiles = $_FILES['feedbackFiles'];
			$fileCount = count($uploadedFiles['name']);
			
			// Validate maximum 10 files
			$maxFiles = 10;
			if($fileCount > $maxFiles)
			{
				echo json_encode(array('success' => false, 'message' => 'Maximum ' . $maxFiles . ' files are allowed. Please select ' . $maxFiles . ' or fewer files.'));
				exit();
			}
			
			for($i = 0; $i < $fileCount; $i++)
			{
				if($uploadedFiles['error'][$i] == UPLOAD_ERR_OK)
				{
					$fileName = $uploadedFiles['name'][$i];
					$fileSize = $uploadedFiles['size'][$i];
					$fileTmpName = $uploadedFiles['tmp_name'][$i];
					$fileType = $uploadedFiles['type'][$i];
					
					// Get file extension
					$fileExt = '';
					$pos = strrpos($fileName, '.');
					if($pos !== false)
					{
						$fileExt = substr($fileName, $pos + 1);
					}
					
					// Generate unique filename
					$uniqueFileName = time() . '_' . $userId . '_' . $orderId . '_' . ($i + 1) . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
					$destPath = $feedbackFilesPath . '/' . $uniqueFileName;
					
					// Move uploaded file
					if(@move_uploaded_file($fileTmpName, $destPath))
					{
						$feedbackFilesJson[] = array(
							'originalName' => $fileName,
							'savedName' => $uniqueFileName,
							'size' => $fileSize,
							'type' => $fileType,
							'ext' => $fileExt,
							'path' => $destPath
						);
					}
				}
			}
		}
	}
	else
	{
		echo json_encode(array('success' => false, 'message' => 'Order not found'));
		exit();
	}
	
	// Prepare feedback data
	$feedbackFilesJsonString = json_encode($feedbackFilesJson);
	$feedbackFilesJsonStringSafe = mysqli_real_escape_string($db_conn, $feedbackFilesJsonString);
	$feedbackTextSafe = makeDBSafe($feedbackText);
	
	// Insert into database
	$insertQuery = "INSERT INTO ocr_data_feedback SET 
		userId = $userId,
		orderId = $orderId,
		feedbackText = '$feedbackTextSafe',
		feedbackFiles = '$feedbackFilesJsonStringSafe',
		addedOn = '".CURRENT_DATE_INDIA."',
		addedTime = '".CURRENT_TIME_INDIA."',
		addedFromIp = '".VISITOR_IP_ADDRESS."'";
	
	$insertResult = dbQuery($insertQuery);
	
	if($insertResult)
	{
		echo json_encode(array(
			'success' => true,
			'message' => 'Feedback submitted successfully.',
			'filesCount' => count($feedbackFilesJson)
		));
	}
	else
	{
		echo json_encode(array('success' => false, 'message' => 'Failed to save feedback. Please try again.'));
	}
	
	exit();
?>
