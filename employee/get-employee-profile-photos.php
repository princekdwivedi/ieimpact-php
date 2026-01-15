<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	$bypassRateLimitar  = 1;
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");

	$ID					=	0;
	$ext				=	"";
	$fileName			=	"";
	if(isset($_GET['ID']) && isset($_GET['ext']))
	{
		$ID				=	$_GET['ID'];
		$ext			=	$_GET['ext'];
	}

	if(!$s_hasManagerAccess)
	{
		if($ID			!= $s_employeeId)
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}
	}
	
	$baseEmployeeId		=	base64_encode($ID);
	$md5EmployeeId		=	md5($ID);
	$fileName			=	$baseEmployeeId."_".$md5EmployeeId.".".$ext;

	$filePath			=	"/WebFiles/files/employee-images/".$fileName;
		
	$contentType		=	"";
	if($ext				==	"gif")
	{
		$contentType	=	"image/gif";
	}
	elseif($ext			==	"jpg")
	{
		$contentType	=	"image/jpeg";
	}
	elseif($ext			==	"jpeg")
	{
		$contentType	=	"image/jpeg";
	}
	elseif($ext			==	"png")
	{
		$contentType	=	"image/png";
	}
	
    header('Content-Type: '.$contentType);
    echo(file_get_contents($filePath));

?>