<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");

	$employeeObj				=	new employee();
	if(!$s_hasManagerAccess)
	{
		echo "<script>window.close();</script>";
	}
	$employeeId		=	0;
	$leaveId		=	0;
	$type			=	0;
	$text			=	"";
	$approvedReason	=	"";
	$rejectReason	=	"";
	$submitText		=	"";
	$reason			=	"";

	if(isset($_GET['employeeId']) && isset($_GET['leaveId']) && isset($_GET['type']))
	{
		$employeeId		=	$_GET['employeeId'];
		$leaveId		=	$_GET['leaveId'];
		$type			=	$_GET['type'];

		$query			=	"SELECT employee_leave_applied.*,fullName FROM employee_leave_applied INNER JOIN employee_details ON employee_leave_applied.employeeId=employee_details.employeeId WHERE leaveId=$leaveId AND employee_leave_applied.employeeId=$employeeId";
		$result		=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$row				=	mysqli_fetch_assoc($result);
			$leaveType			=	$row['leaveType'];
			$leaveFrom			=	$row['leaveFrom'];
			$leaveTo			=	$row['leaveTo'];
			$leaveDays			=	$row['leaveDays'];
			$rejectReason		=	$row['rejectReason'];
			$approvedReason		=	$row['approvedReason'];
			$fullName			=	stripslashes($row['fullName']);

			if($leaveDays == 1)
			{
				$a_leaveFromToDate[]	=	$leaveFrom;
			}
			elseif($leaveDays > 1)
			{
				$a_leaveFromToDate		=	datesBetweenTwoDates($leaveFrom,$leaveTo);
			}
			if($type == 1)
			{
				$text			=	"REJECT EMPLOYEE LEAVE";
				$submitText		=	"REJECT LEAVE";
				$reason			=	$rejectReason;
			}
			elseif($type == 2)
			{
				$text			=	"APPROVE EMPLOYEE LEAVE";
				$submitText		=	"APPROVE LEAVE";
				$reason			=	$approvedReason;
			}
		}
		else
		{
			echo "<script>window.close();</script>";
		}
	}
	else
	{
		echo "<script>window.close();</script>";
	}


	$attendanceMarkedAs		=	3;
	$isAHalfDay				=	0;

	if($leaveType			==	2)
	{
		$attendanceMarkedAs	=	2;//Half Day
		$isAHalfDay			=	1;
	}
?>
<html>
<head>
<title><?php echo $text;?></title>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
<body>
	<script type="text/javascript">
		function reflectChange()
		{
			window.opener.location.reload();
		}
	</script>
	<center>
		<?php
			if(isset($_POST['formSubmitted']))
			{
				$reason		=	$_POST['reason'];
				if(!empty($reason))
				{
					$reason	=	makeDBSafe($reason);
				}
				if($type == 1)
				{
					if(!empty($a_leaveFromToDate))
					{
						foreach($a_leaveFromToDate as $k=>$loginDate)
						{
							$existingAttendenceId	=	$employeeObj->getSingleQueryResult("SELECT attendenceId FROM employee_attendence WHERE attendenceId > ".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID." AND employeeId=$employeeId AND loginDate='$loginDate'","attendenceId");

							if(!empty($existingAttendenceId))
							{
								$hasLogin	=	$employeeObj->getSingleQueryResult("SELECT isLogin FROM employee_attendence WHERE attendenceId > ".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID." AND attendenceId=$existingAttendenceId AND employeeId=$employeeId AND loginDate='$loginDate'","isLogin");

								if($hasLogin == 1)
								{
									dbQuery("UPDATE employee_attendence SET onLeave=0 WHERE attendenceId=$existingAttendenceId AND employeeId=$employeeId");

									$existing_id	=	$employeeObj->getSingleQueryResult("SELECT attendenceId FROM temp_corn_employee_attendance WHERE attendenceId=$existingAttendenceId AND employeeId=$employeeId","attendenceId");
									if(empty($existing_id)){
										
										@dbQuery("INSERT INTO temp_corn_employee_attendance SET attendenceId=$existingAttendenceId,employeeId=$employeeId,loginDate='$loginDate',onLeave=0");
									}
									else{
										@dbQuery("UPDATE temp_corn_employee_attendance SET onLeave=0 WHERE attendenceId=$existing_id AND employeeId=$employeeId");
									}
								}
								else
								{
									dbQuery("DELETE FROM employee_attendence WHERE attendenceId=$existingAttendenceId AND employeeId=$employeeId");

									@dbQuery("DELETE FROM temp_corn_employee_attendance WHERE attendenceId=$existingAttendenceId AND employeeId=$employeeId");
								}
								//*************************** UPDATE TRACKING tABLE *************//
		
								list($leaveY,$leaveM,$leaveD)=	explode("-",$loginDate);

								$nonLeadingZeroMonth		=	$leaveM;
								if($leaveM < 10 && strlen($leaveM) > 1)
								{
									$nonLeadingZeroMonth	=	substr($leaveM,1);
								}

								$totalDaysInMonth	=	$a_daysInMonth[$nonLeadingZeroMonth];

								$employeeObj->updateEmployeeAttendanceTracking($employeeId,0,$fullName,$totalDaysInMonth,$leaveD,$leaveM,$leaveY,0,2);	

							}
								
						}
						
						dbQuery("UPDATE employee_leave_applied SET approvedByManager=$s_employeeId,approvedStatus=1,rejectReason='$reason',approvedOn='".CURRENT_DATE_INDIA."',leaveApprovedTime='".CURRENT_TIME_INDIA."' WHERE leaveId=$leaveId AND employeeId=$employeeId");
					}
				}
				elseif($type == 2)
				{
					
					foreach($a_leaveFromToDate as $k=>$loginDate)
					{
						$existingAttendenceId	=	$employeeObj->getSingleQueryResult("SELECT attendenceId FROM employee_attendence WHERE attendenceId > ".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID." AND employeeId=$employeeId AND isLogin=1 AND loginDate='$loginDate'","attendenceId");

						if(empty($existingAttendenceId))
						{
							dbQuery("INSERT INTO employee_attendence SET onLeave=$leaveType,employeeId=$employeeId,loginDate='$loginDate',isTransferred=1");

							$attendenceId = mysqli_insert_id($db_conn);

							@dbQuery("INSERT INTO temp_corn_employee_attendance SET attendenceId=$attendenceId,employeeId=$employeeId,loginDate='$loginDate',onLeave=$leaveType");
						}
						else
						{
							dbQuery("UPDATE employee_attendence SET onLeave=$leaveType WHERE attendenceId=$existingAttendenceId AND employeeId=$employeeId AND loginDate='$loginDate'");

							$existing_id	=	$employeeObj->getSingleQueryResult("SELECT attendenceId FROM temp_corn_employee_attendance WHERE attendenceId=$existingAttendenceId AND employeeId=$employeeId","attendenceId");
							if(empty($existing_id)){
								
								@dbQuery("INSERT INTO temp_corn_employee_attendance SET attendenceId=$existingAttendenceId,employeeId=$employeeId,loginDate='$loginDate',onLeave=$leaveType");
							}
							else{
								@dbQuery("UPDATE temp_corn_employee_attendance SET onLeave=$leaveType WHERE attendenceId=$existing_id AND employeeId=$employeeId");
							}
						}

						//*************************** UPDATE TRACKING tABLE *************//
						list($leaveY,$leaveM,$leaveD)=	explode("-",$loginDate);

						$nonLeadingZeroMonth		 =	$leaveM;
						if($leaveM < 10 && strlen($leaveM) > 1)
						{
							$nonLeadingZeroMonth	 =	substr($leaveM,1);
						}

						$totalDaysInMonth			=	$a_daysInMonth[$nonLeadingZeroMonth];

						$employeeObj->updateEmployeeAttendanceTracking($employeeId,$attendanceMarkedAs,$fullName,$totalDaysInMonth,$leaveD,$leaveM,$leaveY,$isAHalfDay,2);	
					}
					
					dbQuery("UPDATE employee_leave_applied SET approvedByManager=$s_employeeId,approvedStatus=2,approvedReason='$reason',approvedOn='".CURRENT_DATE_INDIA."',leaveApprovedTime='".CURRENT_TIME_INDIA."' WHERE leaveId=$leaveId AND employeeId=$employeeId");
				}

				echo "<script type='text/javascript'>reflectChange();</script>";

				echo "<script>window.close();</script>";
			}
		?>
		<form name="addEditLeave" action="" method="POST">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td class="smalltext2">
						<b><?php echo $text;?></b>
					</td>
				</tr>
				<tr>
					<td class="textstyle">
						<b>ENTER <?php echo $text;?> REASON </b>(If Any)
					</td>
				</tr>
				<tr>
					<td>
						<textarea name="reason" cols="35" rows="5" style="border: 2px solid #333333"><?php echo $reason;?></textarea>
					</td>
				</tr>
				<tr>
					<td height="8"></td>
				</tr>
				<tr>
					<td>
						<input type="submit" name="submit" value="<?php echo $submitText;?>" border="0">
						<input type='hidden' name='formSubmitted' value='1'>
					</td>
				</tr>
			</table>
		</form>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
	</center>
</body>
</html>