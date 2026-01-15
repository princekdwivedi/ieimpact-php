<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	$employeeObj				=  new employee();
	$memberObj					=  new members();
	$orderObj					=  new orders();

	$path						=	"";
	$downloadPath				=	"";
	$downloadFileName			=	"";
	$mimeTypeField				=	"";
	$performedTask				=	"";

	if(isset($_GET['ID']) && isset($_GET['t']))
	{
		$orderId			=	(int)$_GET['ID'];
		$orderType			=	$_GET['t'];
		if(!empty($orderId) && $orderType == "RTF")
		{
			$query	=	"SELECT memberId,ratingFileExt,ratingFileName,ratingFileFileSize,ratingFileMimeType FROM members_orders WHERE orderId=$orderId AND hasUploadedRatingFile=1 AND rateGiven <> 0";
			$result	=	dbQuery($query);
			if(mysql_num_rows($result))
			{
				$row					=	mysql_fetch_assoc($result);
				$ratingFileName			=	$row['ratingFileName'];
				$ratingFileExt			=	$row['ratingFileExt'];
				$ratingFileFileSize		=	$row['ratingFileFileSize'];
				$mimeTypeField			=	$row['ratingFileMimeType'];
				$memberId				=	$row['memberId'];

				$customerFolderId		= @mysql_result(dbQuery("SELECT folderId FROM members WHERE memberId=$memberId"),0);
				
				$downloadFileName		=	"rating_".$orderId."_".$ratingFileName.".".$ratingFileExt;

				$rating_file_path		=	SITE_ROOT_FILES."/files/otherFiles/$customerFolderId/";

				$downloadPath			=	$rating_file_path.$downloadFileName;
			}
			else
			{
				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES);
				exit();
			}
		}
		else
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}

	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
		
	}
	$performedTask	=	"Downloading Customer Rating File";
	$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);

	//echo $downloadPath;
	
	//die();
	if(file_exists($downloadPath))
	{

		//echo "<br>".$downloadPath;
		//header('Content-Description: File Transfer');
		//header("Content-Type: " . $mimeTypeField);
		//header("Content-Length: " .(string)(filesize($downloadPath)) );
		//header('Content-Disposition: attachment; filename="'.basename($downloadFileName).'"');
		//header("Content-Transfer-Encoding: binary\n");

		//  readfile($downloadPath); // outputs the content of the file
		//echo file_get_contents($downloadPath);

		//------
		header("Content-Type: " . $mimeTypeField);
		header("Content-Length: " .(string)(filesize($downloadPath)) );
		header('Content-Disposition: attachment; filename="'.basename($downloadFileName).'"');
		header("Content-Transfer-Encoding: binary\n");

		//  readfile($downloadPath); // outputs the content of the file
		//echo file_get_contents($downloadPath);




	  // If it's a large file, readfile might not be able to do it in one go, so:
	  $chunksize = 1 * (1024 * 1024); // how many bytes per chunk

	  $handle = fopen($downloadPath, 'rb');
	  $buffer = '';
	  while (!feof($handle))
	  {
		$buffer = fread($handle, $chunksize);
		echo $buffer;
		ob_flush();
		flush();
	  }
	  fclose($handle);
	  exit();
 }
 else
 {
	echo "<br><br><center><b>Sorry No Files Available !!<br><br>Please contact us at support@ieimpact.com</b></center></br>";
 }
?>
