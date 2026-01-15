<?php
	///////////////////START OF SENDING EMAIL BLOCK//////////////
    $a_templateSubject	=	array("{name}" => $_SESSION['employeeName']);


	$a_templateData		=	array("{name}"=>"Hemant Jindal","{empName}"=>$_SESSION['employeeName'],"{failEmail}"=>$loginEmail,"{failPassword}"=>$failLoginPassword,"{securityToken}"=>$securityToken,"{failDate}"=>showDate($nowDateIndia),"{failTime}"=>$nowTimeIndia,"{failEstDate}"=>showDate($customer_zone_date),"{failEstTime}"=>$customer_zone_time,"{failIP}"=>$employeeLoginFromIP,"{failIPCity}"=>$employeeLoginIpCity,"{failIPRegion}"=>$employeeLoginIpRegion,"{failIPCountry}"=>$employeeLoginIpCountry,"{failIPIsp}"=>$employeeLoginIpISP);

	$toEmail			=	"hemant@ieimpact.net";
	//$toEmail			=	"gaurabsiva1@gmail.com";
	$managerEmployeeFromBcc	=   "hr@ieimpact.com,dilber@ieimpact.com";
	$uniqueTemplateName	=	"TEMPLATE_SENDING_SUCCES_EMPLOYEE_LOGIN_OUTSIDE";

	include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
	

	///////////////////END OF SENDING EMAIL BLOCK////////////////
?>