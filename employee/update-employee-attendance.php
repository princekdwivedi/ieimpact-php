<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT			. "/classes/email-track-reading.php");
	
	$employeeObj				= new employee();
	$showForm					= false;
	$title						= "Update Employee Attendance";

	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$employeeId									=	0;
	$attendanceForDate							=	"";
	$currentDateStatus							=	"Absent";
	$presentStatus								=	0;
	$isHavingDateAttendance						=	0;

	if(isset($_GET['employeeId']) && isset($_GET['date']))
	{
		$employeeId								=	(int)$_GET['employeeId'];
		$attendanceForDate						=	$_GET['date'];

		if(!empty($employeeId) && !empty($attendanceForDate))
		{
			$query		=	"SELECT * FROM employee_details WHERE employeeId=$employeeId";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result)){

				$row				=	mysqli_fetch_assoc($result);
				$employeeName		=	$row['fullName'];
				$isShiftTimeAdded	=	$row['isShiftTimeAdded'];
				$shiftFrom			=	$row['shiftFrom'];
				$shiftTo			=	$row['shiftTo'];

				$showForm							=	true;
				$title								=   "Update Attendance of - ".$employeeName." on ".showDate($attendanceForDate);

				list($pY,$pM,$pD)	=	explode("-",$attendanceForDate);

				$nonLeadingZeroMonth				=	$pM;
				if($pM < 10 && strlen($pM) > 1)
				{
					$nonLeadingZeroMonth			=	substr($pM,1);
				}

				$query								=	"SELECT * FROM employee_attendence where employeeId=$employeeId and loginDate='$attendanceForDate' ORDER BY attendenceId DESC LIMIT 1";
				$result				=	dbQuery($query);
				if(mysqli_num_rows($result))
				{
					$row							=	mysqli_fetch_assoc($result);
					$isLogin						=	$row['isLogin'];
					$onLeave						=	$row['onLeave'];
					$isHavingDateAttendance			=	$row['attendenceId'];
					$isForLateAttendance			=	$row['isForLateAttendance'];
					$isForNotLogout			        =	$row['isForNotLogout'];

					$leaveText						=	"";
					if($isForLateAttendance == 1){
						$leaveText					=	"&nbsp;(Late Attendance)";
					}
					elseif($isForNotLogout == 1){
						$leaveText					=	"&nbsp;(Not Logout)";
					}


					if($onLeave						==	1)
					{
						$currentDateStatus			=	"Leave".$leaveText;
						$presentStatus				=	3;
					}
					elseif($onLeave					==	2)
					{
						$currentDateStatus			=	"Half-Day";
						$presentStatus				=	2;
					}
					else
					{
						$currentDateStatus			=	"Present";
						$presentStatus				=	1;
					}
				}
				else
				{
					$sundayText						=    date("l",strtotime($attendanceForDate));
					if($sundayText					==   "Sunday")
					{
						$currentDateStatus			=	"Sunday";
						$presentStatus				=	5;
					}
				}
			}
		}
	}

	$totalDaysInMonth	=	$a_daysInMonth[$nonLeadingZeroMonth];

	
?>
<html>
<head>
<TITLE><?php echo $title;?></TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
<body>
<script type="text/javascript">
	function reflectChange()
	{
		window.opener.location.reload();
	}
	function validSubmit()
	{
		form1	=	document.updateAttendance;

		var confirmation = window.confirm("Are You Sure?");
		if(confirmation == true)
		{
			form1.submit();
		}
		else
		{
			return false;
		}
	}
</script>
<center>
	<?php
		if($showForm)
		{
			if(isset($_REQUEST['formSubmitted']))
			{
				extract($_REQUEST);

				$isHalfDay				=	0;
				$mainLoginStatus		=	1;
				if($changeStatusInto	==	2)
				{
					$isHalfDay			=	1;
					$mainLoginStatus	=	2;
				}
				
				if(empty($isHavingDateAttendance))
				{
					//dbQuery("INSERT INTO employee_attendence SET onLeave=$mainLoginStatus,employeeId=$employeeId,loginDate='$attendanceForDate'");

					if($changeStatusInto	 == 1){
						///////////////// MARKED AS PRESENT ////////////////
						dbQuery("INSERT INTO employee_attendence SET employeeId=$employeeId,isLogin=1,loginDate='".$attendanceForDate."',loginTime='".$shiftFrom."',loginIP='".VISITOR_IP_ADDRESS."',isTransferred=1,isAdminAdded=1");

						$t_attendenceId	=	mysqli_insert_id($db_conn);

						@dbQuery("INSERT INTO temp_corn_employee_attendance SET attendenceId=$t_attendenceId,isLogin=1,employeeId=$employeeId,loginDate='".$attendanceForDate."',loginTime='".$shiftFrom."'");

						$attendanceMarkedAs		=	1;//Present
						$isAHalfDay				=	0;
						$absentPresent			=	1;
					}
					elseif($changeStatusInto == 2){
						///////////////// MARKED AS HALF DAY ////////////////
						dbQuery("INSERT INTO employee_attendence SET employeeId=$employeeId,isLogin=1,loginDate='".$attendanceForDate."',loginTime='".$shiftFrom."',loginIP='".VISITOR_IP_ADDRESS."',isTransferred=1,isAdminAdded=1");

						$t_attendenceId	=	mysqli_insert_id($db_conn);

						@dbQuery("INSERT INTO temp_corn_employee_attendance SET attendenceId=$t_attendenceId,isLogin=1,employeeId=$employeeId,loginDate='".$attendanceForDate."',loginTime='".$shiftFrom."'");


						$attendanceMarkedAs		=	2;//Half Day
						$isAHalfDay				=	1;
						$absentPresent			=	2;
					}
					elseif($changeStatusInto == 3){
						///////////////// MARKED AS ABSENT ////////////////
						dbQuery("INSERT INTO employee_attendence SET employeeId=$employeeId,onLeave=1,loginDate='".$nowDateIndia."',loginTime='".$nowTimeIndia."',loginIP='".VISITOR_IP_ADDRESS."',isTransferred=1,isLogin=0,isForLateAttendance=1,isMarkedAbsent=1");

						$t_attendenceId	=	mysqli_insert_id($db_conn);

						@dbQuery("INSERT INTO temp_corn_employee_attendance SET attendenceId=$t_attendenceId,isLogin=0,onLeave=1,employeeId=$employeeId,loginDate='".$nowDateIndia."',loginTime='".$nowTimeIndia."',isForLateAttendance=1,isMarkedAbsent=1");

						$attendanceMarkedAs		=	3;//Abesnt
						$isAHalfDay				=	0;
						$absentPresent			=	2;
					}
				}
				else
				{
					//dbQuery("UPDATE employee_attendence SET onLeave=$mainLoginStatus,isLogin=0 WHERE attendenceId=$isHavingDateAttendance AND employeeId=$employeeId AND loginDate='$attendanceForDate'");

					if($changeStatusInto	 == 1){
						///////////////// MARKED AS PRESENT ////////////////
						@dbQuery("UPDATE employee_attendence SET onLeave=0,isForNotLogout=0,isLogin=1,isMarkedAbsent=0,isAdminAdded=1 WHERE attendenceId=$isHavingDateAttendance AND employeeId=$employeeId");

						@dbQuery("UPDATE temp_corn_employee_attendance SET isLogin=1,onLeave=0,isForNotLogout=0,isMarkedAbsent=0 WHERE attendenceId=$isHavingDateAttendance AND employeeId=$employeeId");

						$attendanceMarkedAs		=	1;//Present
						$isAHalfDay				=	0;
						$absentPresent			=	1;
					}
					elseif($changeStatusInto == 2){
						///////////////// MARKED AS HALF DAY ////////////////
						@dbQuery("UPDATE employee_attendence SET onLeave=0,isForNotLogout=0,isLogin=1,isMarkedAbsent=0,isAdminAdded=1 WHERE attendenceId=$isHavingDateAttendance AND employeeId=$employeeId");

						@dbQuery("UPDATE temp_corn_employee_attendance SET isLogin=1,onLeave=0,isForNotLogout=0,isMarkedAbsent=0 WHERE attendenceId=$isHavingDateAttendance AND employeeId=$employeeId");

						$attendanceMarkedAs		=	2;//Half Day
						$isAHalfDay				=	1;
						$absentPresent			=	2;
					}
					elseif($changeStatusInto == 3){
						///////////////// MARKED AS ABSENT ////////////////
						@dbQuery("UPDATE employee_attendence SET onLeave=1,isForNotLogout=1,isLogin=0,isMarkedAbsent=0,isAdminAdded=1 WHERE attendenceId=$isHavingDateAttendance AND employeeId=$employeeId");

						@dbQuery("UPDATE temp_corn_employee_attendance SET isLogin=0,onLeave=1,isForNotLogout=1,isMarkedAbsent=0 WHERE attendenceId=$isHavingDateAttendance AND employeeId=$employeeId");

						$attendanceMarkedAs		=	3;//Abesnt
						$isAHalfDay				=	0;
						$absentPresent			=	2;
					}

					
				}
				$employeeObj->updateEmployeeAttendanceTracking($employeeId,$attendanceMarkedAs,$employeeName,$totalDaysInMonth,$pD,$pM,$pY,$isAHalfDay,$absentPresent);	

				$employeeObj->updateEmployeePresentAbsent($employeeId,$nonLeadingZeroMonth,$pY);

				$_SESSION['showChangedEmployeeColore']	=	$employeeId;

				echo "<script type='text/javascript'>reflectChange();</script>";

				echo "<script type='text/javascript'>window.close();</script>";
			}
	?>
			<form name="updateAttendance" action="" method="POST" onsubmit="return validSubmit();">
				<table width="100%" align="center" border="0" cellspacing="2" cellspacing="2">
					<tr>
						<td colspan="3" class="textstyle1"><b>Update Employee Attendance</b></td>
					</tr>
					<tr>
						<td height="8"></td>
					</tr>
					<tr>
						<td width="24%" class="smalltext2">
							<b>Employee Name</b>
						</td>
						<td width="2%" class="smalltext2">
							<b>:</b>
						</td>
						<td class="title">
							<?php echo $employeeName;?>
						</td>
					</tr>
					<tr>
						<td height="8"></td>
					</tr>
					<tr>
						<td class="smalltext2">
							<b>Attendance Date</b>
						</td>
						<td class="smalltext2">
							<b>:</b>
						</td>
						<td class="title">
							<?php echo showDate($attendanceForDate);?>
						</td>
					</tr>
					<tr>
						<td height="8"></td>
					</tr>
					<tr>
						<td class="smalltext2">
							<b>Current Status</b>
						</td>
						<td class="smalltext2">
							<b>:</b>
						</td>
						<td class="title">
							<?php echo $currentDateStatus;?>
						</td>
					</tr>
					<tr>
						<td height="8"></td>
					</tr>
					<tr>
						<td class="smalltext2">
							<b>Change Status Into</b>
						</td>
						<td class="smalltext2">
							<b>:</b>
						</td>
						<td class="title">
							<select name="changeStatusInto">
							    <option value="1">Present</option>								
								<option value="2">Half Leave</option>	
								<option value="3">Full Leave</option>
							</select>
						</td>
					</tr>
					<tr>
						<td height="8"></td>
					</tr>
					<tr>
						<td colspan="2"></td>
						<td>
							<input type="image" name="submit" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
							<input type='hidden' name='formSubmitted' value='1'>
						</td>
					</tr>
				</table>
			</form>
	<?php
		}
		else
		{
			echo "<table width='90%' align='center' border='1' height='100'><tr><td align='center' align='center' class='error'><b>Oops..invalid page!!</b></td></tr></table>";
		}
	?>

<br><br>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
</center>
</body>
</html>

