<?php
	///////////////////START OF SENDING EMAIL BLOCK//////////////

	if(!empty($a_mainManagerEmail))
	{
		$loginArea			=	$failLoginMessage." in employee area";	
		if($isPdfEmployee	==	1)
		{
			$loginArea		=	$failLoginMessage." in PDF Employee area";
		}

		
		foreach($a_mainManagerEmail as $k=>$value)
		{
			list($managerEmail,$managerName)	=	explode("|",$value);

			$a_templateSubject	=	array("{loginSubject}"=>$loginArea,"{failedEmail}"=>$loginEmail);

			$a_templateData		=	array("{name}"=>$managerName,"{loginArea}"=>$loginArea,"{failError}"=>$failLoginMessage,"{failEmail}"=>$loginEmail,"{failPassword}"=>$failLoginPassword,"{securityToken}"=>$securityToken,"{failDate}"=>showDate($nowDateIndia),"{failTime}"=>$nowTimeIndia,"{failEstDate}"=>showDate($customer_zone_date),"{failEstTime}"=>$customer_zone_time,"{failIP}"=>$employeeLoginFromIP,"{failIPCity}"=>$employeeLoginIpCity,"{failIPRegion}"=>$employeeLoginIpRegion,"{failIPCountry}"=>$employeeLoginIpCountry,"{failIPIsp}"=>$employeeLoginIpISP);

			$uniqueTemplateName	=	"TEMPLATE_SENDING_FAIL_LOGIN_INFORMATION";
			$toEmail			=	$managerEmail;

			include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
		}
	}

	///////////////////END OF SENDING EMAIL BLOCK////////////////
?>