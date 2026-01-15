<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	$bypassRateLimitar  = 1;
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

	if(isset($_GET['memberId']) && isset($_GET['orderId']) && isset($_GET['messageId']) && isset($_GET['isNewSystem']))
	{
		$memberId				=	$_GET['memberId'];
		$orderId				=	$_GET['orderId'];
		$messageId				=	$_GET['messageId'];
		$isNewSystem			=	$_GET['isNewSystem'];
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$query								=	"SELECT * from members_employee_messages WHERE orderId=$orderId AND memberId=$memberId AND messageId=$messageId AND hasMessageFiles=1 AND isDeleted=0 AND isVirtualDeleted=0";
	$result								=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		$row							=	mysqli_fetch_assoc($result);
		$fileName						=	$row['fileName'];
		$fileExtension					=	$row['fileExtension'];
		$fileSize						=	$row['fileSize'];

		if($isNewSystem					== 1)
		{
			if($result1					=	$orderObj->getOrdereMessageFile($orderId,$messageId,3,7))
			{
				$row1					=	mysqli_fetch_assoc($result1);
				$fileName				=	stripslashes($row1['uploadingFileName']);
				$fileExtension			=	$row1['uploadingFileExt'];
				$downloadPath			=	$row1['excatFileNameInServer'];
				$downloadPath           =   stringReplace("/home/ieimpact", "", $downloadPath);

				$downloadFileName		=	$fileName.".".$fileExtension;
			}
		}
		else
		{
			$messageFilePath			=	SITE_ROOT_FILES."/files/messages/";
			$downloadFileName			=	$fileName.".".$fileExtension;

			$downloadPath				=	$messageFilePath.$messageId."_".$fileName.".".$fileExtension;
		}

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