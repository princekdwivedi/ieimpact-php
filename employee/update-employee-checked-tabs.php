<?php
	//session_start();
	Header("Cache-Control: must-revalidate");
	$offset = 60 * 60 * 24 * 3;
	$ExpStr = "Expires: Thu, 29 Oct 1998 17:04:19 GMT";
	require_once("../root.php");
	session_start();

	include(SITE_ROOT_EMPLOYEES . "/includes/check-site-maintanence.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	$employeeObj				=  new employee();
	$orderObj					=  new orders();
	$orderId					=  0;
	$tabId						=  0;
	
	if(isset($_GET['tabId']) && isset($_GET['orderId']))
	{
		$orderId				=  (int)$_GET['orderId'];
		$tabId					=  (int)$_GET['tabId'];

		$a_clickedOrdersAllTabs	=  $orderObj->getEmployeesClickedTabs($orderId,$s_employeeId);

		if(!empty($orderId) && !empty($tabId) && !in_array($tabId,$a_clickedOrdersAllTabs))
		{
			$orderObj->updateEmployeesClickedTabs($orderId,$tabId,$s_employeeId);
		}
	}
?>