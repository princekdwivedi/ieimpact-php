<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES .   "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES .   "/classes/employee.php");
	include(SITE_ROOT			.	"/includes/send-mail.php");
	$employeeObj				=	new employee();
	$verificationCode			=	"";
	$employeeId					=	0;
	if(isset($_GET['code']))
	{
		$verificationCode	=	$_GET['code'];
		$query				=	"SELECT * FROM employee_details WHERE verificationCode='$verificationCode' AND isSentActivationMail=1 AND isActive=0";
		$result		=	mysql_query($query);
		if(mysql_num_rows($result))
		{
			$row			=	mysql_fetch_assoc($result);
			$employeeId		=	$row['employeeId'];
			$lastName		=	stripslashes($row['lastName']);
			$firstName		=	stripslashes($row['firstName']);
			$email			=	stripslashes($row['email']);
			$employeeName	=	$firstName." ".$lastName;
			$employeeName	=	ucwords($employeeName);
			
			dbQuery("UPDATE employee_details SET isActive=1,verifiedAccountOn='".CURRENT_DATE_INDIA."' WHERE employeeId=$employeeId AND isActive=0");

			$_SESSION['employeeId']   =	$employeeId;
			$_SESSION['employeeName'] =	$employeeName;

			$from			=	$email;
			$fromName		=	$employeeName;
			$toEmail		=	"hr@ieimpact.com";

			$templateId		=	EMPLOYEE_SENDING_ADMIN_ACTIVE_ACCOUNT;
			$mailSubject	=	$employeeName." activated employee account in ieIMPACT";

			$a_templateData	=	array("{employeeName}"=>$employeeName,"{employeeEmail}"=>$email);

			sendTemplateMail($from, $fromName, $toEmail, $mailSubject, $templateId, $a_templateData);

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/employee-details.php");
			exit();
		}
		else
		{
			ob_clean();
			header("Location:".SITE_URL_EMPLOYEES);
			exit();
		}
	}
	else
	{
		ob_clean();
		header("Location:".SITE_URL_EMPLOYEES);
		exit();
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>