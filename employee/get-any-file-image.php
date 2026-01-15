<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");

	$employeeObj				=  new employee();
	$memberObj					=  new members();
	$orderObj					=  new orders();

	
	$memberId					=	0;
	$orderId					=	"";
	$messageId					=	"";
	$isNewSystem				=	0;
	$downloadPath				=	"";
	$fileExtension				=	"";

	if(isset($_GET['fileId']))
	{
		$fileId				=	$_GET['fileId'];
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$query								=	"SELECT * from order_all_files WHERE fileId=$fileId";
	$result								=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		$row							=	mysqli_fetch_assoc($result);
		$fileName						=	$row['uploadingFileName'];
		$fileExtension					=	$row['uploadingFileExt'];
		$fileSize						=	$row['uploadingFileSize'];
		$excatFileNameInServer    		=	$row['excatFileNameInServer'];
		$excatFileNameInServer          =   stringReplace("/home/ieimpact", "", $excatFileNameInServer);

		$downloadFileName			    =	$fileName.".".$fileExtension;

		$downloadPath				    =	$excatFileNameInServer;
	}

	
	if(file_exists($downloadPath) && !empty($fileExtension))
	{

		$exactFileSize				=	@filesize($downloadPath);
		if($exactFileSize > 0 && $exactFileSize < "3145728")
		{
			$contentType			=	"";
			if($fileExtension		==	"gif")
			{
				$contentType		=	"image/gif";
			}
			elseif($fileExtension	==	"jpg")
			{
				$contentType		=	"image/jpeg";
			}
			elseif($fileExtension	==	"jpeg")
			{
				$contentType		=	"image/jpeg";
			}
			elseif($fileExtension	==	"png")
			{
				$contentType		=	"image/png";
			}
			elseif($fileExtension	==	"bmp")
			{
				$contentType		=	"image/bmp";
			}

			if(!empty($contentType))
			{
				header('Content-Type: '.$contentType);
				echo(file_get_contents($downloadPath));
			}
		}
	}

?>