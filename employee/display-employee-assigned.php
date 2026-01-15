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
	$employeeObj				= new employee();

	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	

	if(isset($_GET['employeeId'])){
		$employeeId			=	(int)$_GET['employeeId'];

		if(!empty($employeeId)){

			$totalAssigned	=	$employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as TotalOrders FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND assignToEmployee='$nowDateIndia' AND status=1 AND acceptedBy=$employeeId","TotalOrders");
			
			if(!empty($totalAssigned)){
					////////////////////////// DISPLAY VIEW LINK ///////////////////
					echo "(<a onclick='displayAssignPopUp($employeeId)' class='link_style21' style='cursor:pointer;'>View Assigned</a>)";
			}
			
		}
	}


?>