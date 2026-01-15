<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$employeeImagePath		=	SITE_ROOT_FILES."/files/employee-images/";
	$employeeImageUrl		=	SITE_URL."/files/employee-images/";
	$employeeId				=	0;

	if(isset($_GET['ID']))
	{
		$employeeId		=	(int)$_GET['ID'];
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$query					=	"SELECT employeeId,hasProfilePhoto,profilePhotoExt,profilePhotoType,profilePhotoSize,fullName FROM employee_details WHERE employeeId=$employeeId AND isActive=1 AND hasProfilePhoto=1";
	$result	=	dbQuery($query);
	if(mysql_num_rows($result))
	{
		$row				=	mysql_fetch_assoc($result);
		$hasProfilePhoto	=	$row['hasProfilePhoto'];
		$profilePhotoExt	=	$row['profilePhotoExt'];
		$profilePhotoType	=	$row['profilePhotoType'];
		$profilePhotoSize	=	$row['profilePhotoSize'];
		$fullName			=	$row['fullName'];
		
		$baseEmployeeId		=	base64_encode($employeeId);
		$md5EmployeeId		=	md5($employeeId);
		$fileName			=	$baseEmployeeId."_".$md5EmployeeId.".$profilePhotoExt";

		$downloadFileName	=	$fullName.".".$profilePhotoExt;

		$downloadPath		=	$employeeImagePath.$fileName;
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	//die();


	if(file_exists($downloadPath))
	{		
		$exactFileSize		=	@filesize($downloadPath);
		if($exactFileSize > 0 && $exactFileSize < "104857600")
		{

			//echo "<br>".$downloadPath;
			 header("Cache-Control: maxage=1"); // Age is in seconds.
			 header("Pragma: public");
		   // header("Content-Type: application/force-download");

			header("Content-Type: " . $profilePhotoType);
			header("Content-Length: " .(string)(filesize($downloadPath)) );
			header('Content-Disposition: attachment; filename="'.basename($downloadFileName).'"');
			header("Content-Transfer-Encoding: binary\n");

		  //readfile($downloadPath); // outputs the content of the file
		  //echo file_get_contents($downloadPath);
		  // If it's a large file, readfile might not be able to do it in one go, so:
			$chunksize = 1 * (1024 * 1024); // how many bytes per chunk

		   	$handle		= fopen($downloadPath, 'rb');
			$buffer     = '';
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

		}
		else
		{
			include(SITE_ROOT_EMPLOYEES   .   "/includes/file-not-found-page.php");
		}
	}
	else
	{
		include(SITE_ROOT_EMPLOYEES   .   "/includes/file-not-found-page.php");
	}
?>