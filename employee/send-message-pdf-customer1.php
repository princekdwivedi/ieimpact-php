<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES .  "/includes/check-login.php");

	if(isset($_GET['orderId']) && isset($_GET['customerId']))
	{
		$orderId		=	$_GET['orderId'];
		$customerId		=	$_GET['customerId'];

		$extraUrl		=	"";

		if(isset($_GET['vmsg']) && !empty($s_isHavingVerifyAccess))
		{
			$msgId		=	$_GET['vmsg'];
			if(!empty($msgId))
			{
				$extraUrl=	"&vmsg=".$msgId;
			}
		}
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/send-message-pdf-customer.php?orderId=$orderId&customerId=$customerId".$extraUrl."#sendMessages");
		exit();
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
		exit();
	}

?>