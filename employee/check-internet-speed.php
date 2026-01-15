<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES .  "/includes/new-top.php");
	include(SITE_ROOT_EMPLOYEES .  "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES .  "/classes/employee.php");
	include(SITE_ROOT			.  "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/check-pdf-login.php");
	include(SITE_ROOT_MEMBERS	.  "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	.  "/classes/orders.php");
	include(SITE_ROOT			.  "/classes/validate-fields.php");
	include(SITE_ROOT			.  "/classes/common.php");
	include(SITE_ROOT			.  "/classes/email-templates.php");
    $emailObj					=  new emails();
	$employeeObj				=  new employee();

	$kb=1024;
	echo "Streaming $kb Kb...<!-";
	flush();
	$time = explode(" ",microtime());
	$start = $time[0] + $time[1];
	for($x=0;$x<$kb;$x++){
	    echo str_pad('', 1024, '.');
	    flush();
	}
	$time   = explode(" ",microtime());
	$finish = $time[0] + $time[1];
	$deltat = $finish - $start;
	$kb     = round($kb / $deltat, 3);
	$mb 	= round($kb / 1000, 2);
	echo "-> Test finished in $deltat seconds. Your speed is ". $kb."KBPS/Seconds or ".$mb. "MBPS/Seconds";

	if(!empty($kb)){

		if(empty($mb)){
			$mb =	0;
		}

		$query	=	"SELECT employeeId FROM employee_internet_speed WHERE employeeId=$s_employeeId";
		$result =	dbQuery($query);
		if(mysqli_num_rows($result)){
			dbQuery("UPDATE employee_internet_speed SET mbps='$mb',kbps='$kb',addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."' WHERE employeeId=$s_employeeId");
		}
		else{
			dbQuery("INSERT INTO employee_internet_speed SET employeeId=$s_employeeId,mbps='$mb',kbps='$kb',addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
		}
	}

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>
