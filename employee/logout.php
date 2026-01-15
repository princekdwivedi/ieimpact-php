<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	session_destroy();
	if(isset($_SESSION['employeeId']))
	{
		if(isset($_SESSION['employeeLoginSessionTrackId']))
		{
			
			dbQuery("UPDATE employee_login_track SET loginOutDate='".CURRENT_DATE_INDIA."',loginOutTime='".CURRENT_TIME_INDIA."',loginOutIP='".VISITOR_IP_ADDRESS."' WHERE trackId='".$_SESSION['employeeLoginSessionTrackId']."' AND employeeId='".$_SESSION['employeeId']."'");

			dbQuery("DELETE FROM track_employee_active_on_website WHERE employeeId='".$_SESSION['employeeId']."'");
			
			unset($_SESSION['employeeLoginSessionTrackId']);
		}
		unset($_SESSION['employeeId']);
	}
	
	if(isset($_SESSION['employeeName']))
	{
		unset($_SESSION['employeeName']);
	}
	if(isset($_SESSION['employeeEmail']))
	{
		unset($_SESSION['employeeEmail']);
	}
	if(isset($_SESSION['hasManagerAccess']))
	{
		unset($_SESSION['hasManagerAccess']);
	}
	if(isset($_SESSION['departmentId']))
	{
		unset($_SESSION['departmentId']);
	}
	if(isset($_SESSION['hasPdfAccess']))
	{
		unset($_SESSION['hasPdfAccess']);
	}
	if(isset($_SESSION['isInBreak']))
	{
		unset($_SESSION['isInBreak']);
	}
	if(isset($_SESSION['breakId']))
	{
		unset($_SESSION['breakId']);
	}
	if(isset($_SESSION['iasHavingAllQaAccess']))
	{
		unset($_SESSION['iasHavingAllQaAccess']);
	}
	if(isset($_SESSION['hasAdminAccess']))
	{
		unset($_SESSION['hasAdminAccess']);
	}
	if(isset($_SESSION['showQuestionnaire']))
	{
		unset($_SESSION['showQuestionnaire']);
	}
	if(isset($_SESSION['isHavingVerifyAccess']))
	{
		unset($_SESSION['isHavingVerifyAccess']);
	}
	if(isset($_SESSION['pageViewedTime']))
	{
		unset($_SESSION['pageViewedTime']);
	}
	if(isset($_SESSION['maxSearchMemberOrderId']))
	{
		unset($_SESSION['maxSearchMemberOrderId']);
	}
	if(isset($_SESSION['maxSearchMemberOrderFileId']))
	{
		unset($_SESSION['maxSearchMemberOrderFileId']);
	}
	if(isset($_SESSION['maxSearchMtEmployeeWorkId']))
	{
		unset($_SESSION['maxSearchMtEmployeeWorkId']);
	}
	if(isset($_SESSION['maxSearchEmployeeAttendenceId']))
	{
		unset($_SESSION['maxSearchEmployeeAttendenceId']);
	}
	if(isset($_SESSION['maxSearchEmployeeOrderId']))
	{
		unset($_SESSION['maxSearchEmployeeOrderId']);
	}



	//session_destroy();

	ob_clean();
	header("Location: ".SITE_URL_EMPLOYEES);
	exit();
?>