<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");
	$employeeObj				=	new employee();
	$validator					=	new validate();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");

	$leaveId					=	0;
	
	$checked					=	"checked";
	$checked1					=	"";

	$display					=	"";
	$display1					=	"";
	$display2					=	"none";

	$forDate					=	"";
	$t_forDate					=	"";
	$fromDate					=	"";
	$t_fromDate					=	"";

	$toDate						=	"";
	$t_toDate					=	"";
	$leaveReason				=	"";
	$emergencyNo				=	"";
	$leaveDays					=	1;
	$leaveType					=	1;
	$maxLeavecanApplyFrom		=	getNextGivenDate($nowDateIndia,1);
	$maxLeavecanApply			=	getNextGivenDate($nowDateIndia,60);

	$form						=	SITE_ROOT_EMPLOYEES."/forms/add-edit-leave.php";
?>
<table cellpadding="3" cellspacing="2" width="98%" border="0" align="center">
	<tr>
		<td>
			<font class="heading">APPLY LEAVE ONLINE</font><br>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
</table>
<?php
	if(isset($_GET['success']))
	{
?>
	<table cellpadding="3" cellspacing="2" width="98%" border="0" align="center">
		<tr>
			<td>
				<font class="title">You have successfully applied for leave.<br>After grant by manager, you wil receive confirmations.</font><br>
			</td>
		</tr>
		<tr>
			<td height="80"></td>
		</tr>
	</table>
<?php
	}
	elseif(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		
		$leaveReason	=	makeDBSafe($leaveReason);
		if($leaveType	== 1)
		{
			if($leaveDays	== 1)
			{
				
				if(empty($forDate))
				{
					$validator ->setError("Please Select Leave Date.");
				}
				else{
					 list($date,$month,$year)=    explode("-",$forDate);
					 $t_fromDate	=	$year."-".$month."-".$date;
					 if($t_fromDate <  $maxLeavecanApplyFrom){
					 	$validator ->setError("You can apply leave from ".showDate($maxLeavecanApplyFrom).".");
					 }
					 elseif($t_fromDate > $maxLeavecanApply){
					    $validator ->setError("You can apply leave upto ".showDate($maxLeavecanApply).".");
					 }
				}
			}
			else
			{
				$display2	=	"";
				$display1	=	"none";
				if(empty($fromDate))
				{
					$validator ->setError("Please Select Leave From Date.");
				}
				if(empty($toDate))
				{
					$validator ->setError("Please Select Leave To Date.");
				}
				if(!empty($fromDate) && !empty($toDate))
				{
					 list($date,$month,$year)=    explode("-",$fromDate);
					 $t_fromDate	=	$year."-".$month."-".$date;

					 list($tdate,$tmonth,$tyear)=    explode("-",$toDate);
					 $t_toDate		=	$tyear."-".$tmonth."-".$tdate;

					 if($t_fromDate > $t_toDate){
					 	 $validator ->setError("Please check from and to date.");
					 }
					 elseif($t_fromDate <  $maxLeavecanApplyFrom){
					 	$validator ->setError("You can apply leave from ".showDate($maxLeavecanApplyFrom).".");
					 }
					 elseif($t_toDate > $maxLeavecanApply){
					    $validator ->setError("You can apply leave upto ".showDate($maxLeavecanApply).".");
					 }
					
				}
			}
		}
		else
		{
			$checked					=	"";
			$checked1					=	"checked";

			$display					=	"none";
			$display1					=	"";
			$display2					=	"none";
			if(empty($forDate))
			{
				$validator ->setError("Please Select Leave Date.");
			}
			else
			{
				 list($fdate,$fmonth,$fyear)=    explode("-",$forDate);
				 $t_forDate	=	$fyear."-".$fmonth."-".$fdate;

				 //$isAlreadyMarkedAttention  =   $employeeObj->getSingleQueryResult("SELECT attendenceId FROM employee_attendence WHERE employeeId=$s_employeeId AND isLogin=1 AND loginDate='$t_forDate'","attendenceId");
				 $isAlreadyMarkedAttention = 0;

				 if(!empty($isAlreadyMarkedAttention)){
				 	$validator ->setError("You cannot marked it as half day once you marked your attendance.");
				 }
				 elseif($t_forDate > $maxLeavecanApply){
					$validator ->setError("You can apply leave upto ".showDate($maxLeavecanApply).".");
				 }
			}
		}
		$validator ->checkField($leaveReason,"","Please Enter Leave Reason.");
		$dataValid	 =	$validator ->isDataValid();
		if($dataValid)
		{
			if($leaveType	== 1)
			{
				if($leaveDays	== 1)
				{
					list($fdate,$fmonth,$fyear)=    explode("-",$forDate);
					$leaveFrom	=	$fyear."-".$fmonth."-".$fdate;
					$leaveTo	=	"0000-00-00";
				}
				else
				{
					$leaveFrom	=	 $t_fromDate;
					$leaveTo	=	  $t_toDate;
				}
			}
			else
			{
				$leaveDays		=	1;
				$leaveFrom		=	$t_forDate;
				$leaveTo		=	"0000-00-00";
			}

			$optionQuery		=	" SET employeeId=$s_employeeId,leaveDays=$leaveDays,leaveType=$leaveType,leaveFrom='$leaveFrom',leaveTo='$leaveTo',leaveReason='$leaveReason',emergencyNo='$emergencyNo',appliedOn='".CURRENT_DATE_INDIA."',leaveAppliedTime='".CURRENT_TIME_INDIA."'";
			if(empty($leaveId))
			{
				$query	=	"INSERT INTO employee_leave_applied".$optionQuery;
				dbQuery($query);
			}
			else
			{
				$query	=	"UPDATE employee_leave_applied".$optionQuery." WHERE leaveId=$leaveId";
				dbQuery($query);
			}
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES ."/apply-leave.php?success=1");
			exit();
		}
		else
		{
			echo $errorMsg	 =	$validator ->getErrors();
			include($form);
		}
	}
	else
	{
		include($form);
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>