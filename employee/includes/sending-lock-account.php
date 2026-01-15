<?php
	$query					=	"SELECT fullName,email FROM employee_details WHERE employeeId=$loginId AND isActive=1";
	$result					=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		$row				=	mysqli_fetch_assoc($result);
		$employeeName		=	stripslashes($row['fullName']);
		$employeeEmail		=	stripslashes($row['email']);

		if(!empty($employeeName) && !empty($employeeEmail))
		{
			include(SITE_ROOT			.   "/includes/send-mail.php");

			$dateTime					=	showDate(CURRENT_DATE_INDIA)." ".showTimeFormat(CURRENT_TIME_INDIA);
		
			$userLocation				=	"";
			if(!empty($employeeLoginIpCity))
			{
				$userLocation		   .=	$employeeLoginIpCity.", ";
			}
			if(!empty($employeeLoginIpRegion))
			{
				$userLocation		   .=	$employeeLoginIpRegion.", ";
			}
			if(!empty($employeeLoginIpCountry))
			{
				$userLocation		   .=	$employeeLoginIpCountry;
			}
			if(!empty($employeeLoginIpZipCode))
			{
				$userLocation		   .=	"&nbsp;(".$employeeLoginIpZipCode.")";
			}

			$a_templateData		=	array("{employeeName}"=>$employeeName,"{dateTime}"=>$dateTime,"{computerMobile}"=>$deviceType,"{userOs}"=>$userOs,"{userBrowser}"=>$userBowser,"{userIP}"=>$employeeLoginFromIP,"{userLocation}"=>$userLocation);

			$uniqueTemplateName	=	"TEMPLATE_SENDING_EMPLOYEE_SUSPICIOUS _SIGN_IN";
			$toEmail			=	$employeeEmail;

			include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

			$sendingToEmailName = $employeeName." - ".$employeeEmail;

			$a_templateData		=	array("{employeeName}"=>$employeeName,"{dateTime}"=>$dateTime,"{computerMobile}"=>$deviceType,"{userOs}"=>$userOs,"{userBrowser}"=>$userBowser,"{userIP}"=>$employeeLoginFromIP,"{userLocation}"=>$userLocation);

			$uniqueTemplateName	=	"TEMPLATE_SENDING_EMPLOYEE_SUSPICIOUS _SIGN_IN";
			$toEmail			=	"gaurabsiva1@gmail.com";

			include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
		}
	}
?>