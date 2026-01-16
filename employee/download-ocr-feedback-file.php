<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	
	$fileId = '';
	if(isset($_GET['fileId']))
	{
		$fileId = $_GET['fileId'];
	}
	
	if(empty($fileId))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	
	// Decode fileId to get id and savedName
	$decoded = base64_decode($fileId);
	$parts = explode('_', $decoded, 2);
	
	if(count($parts) != 2)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	
	$feedbackId = (int)$parts[0];
	$savedFileName = $parts[1];
	
	// Get feedback record
	$query = "SELECT feedbackFiles, orderId FROM ocr_data_feedback WHERE id = $feedbackId LIMIT 1";
	$result = dbQuery($query);
	
	if(!mysqli_num_rows($result))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	
	$row = mysqli_fetch_assoc($result);
	$feedbackFiles = json_decode($row['feedbackFiles'], true);
	$orderId = $row['orderId'];
	
	// Find the file in the feedback files array
	$filePath = '';
	$originalFileName = '';
	$mimeType = '';
	
	if(!empty($feedbackFiles) && is_array($feedbackFiles))
	{
		foreach($feedbackFiles as $file)
		{
			if(isset($file['savedName']) && $file['savedName'] == $savedFileName)
			{
				$filePath = isset($file['path']) ? $file['path'] : '';
				$originalFileName = isset($file['originalName']) ? $file['originalName'] : $savedFileName;
				$mimeType = isset($file['type']) ? $file['type'] : 'application/octet-stream';
				break;
			}
		}
	}
	
	if(empty($filePath) || !file_exists($filePath))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	
	// Verify user has access to this order (optional security check)
	// You can add additional checks here if needed
	
	// Get file size
	$fileSize = @filesize($filePath);
	
	if($fileSize === false || $fileSize == 0)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	
	// Set headers for file download
	header("Cache-Control: maxage=1");
	header("Pragma: public");
	header("Content-Type: " . $mimeType);
	header("Content-Length: " . (string)$fileSize);
	header('Content-Disposition: attachment; filename="' . basename($originalFileName) . '"');
	header("Content-Transfer-Encoding: binary\n");
	
	// Read and output file
	$chunksize = 1 * (1024 * 1024); // 1MB chunks
	$handle = fopen($filePath, 'rb');
	
	if($handle)
	{
		while (!feof($handle))
		{
			$buffer = fread($handle, $chunksize);
			if(!($buffer))
			{
				break;
			}
			echo $buffer;
			ob_flush();
			flush();
		}
		fclose($handle);
	}
	
	exit();
?>
