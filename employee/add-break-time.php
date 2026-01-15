<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	."/includes/common-array.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");
	$employeeObj				=	new employee();
	$validator					=	new validate();
	$breakId					=   0;
	$errorMsg					=	"";
	$breakTakingFor				=	"";
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!empty($s_isInBreak) || !empty($s_breakId))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/complete-break-time.php");
		exit();
	}
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		$breakTakingFor			=	trim($breakTakingFor);
		$breakTakingFor			=	makeDBSafe($breakTakingFor);
		if(empty($breakTakingFor))
		{
			$errorMsg			=	"Please Enter Break Reason";
		}
		if(empty($errorMsg))
		{
			dbQuery("INSERT INTO employee_breaks SET employeeId=$s_employeeId,breakTakingFor='$breakTakingFor',breakDate='".CURRENT_DATE_INDIA."',breakTime='".CURRENT_TIME_INDIA."'");

			$breakId			=	mysql_insert_id();
			dbQuery("UPDATE employee_details SET isInBreak=1 WHERE employeeId=$s_employeeId AND isInBreak=0");

			$_SESSION['isInBreak']   =	1;
			$_SESSION['breakId']	 =	$breakId;

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/complete-break-time.php");
			exit();
		}
	}
?>
<script type="text/javascript">
function validBreak()
{
	//return;
	form1	=	document.addBreakTime;
	if(form1.breakTakingFor.value ==	"")
	{
		alert("Please Enter Break Reason !!");
		form1.breakTakingFor.focus();
		return false;
	}
}
</script>
<form name="addBreakTime" action="" method="POST" onsubmit="return validBreak();">
<table width="98%" border="0" align="center" cellpadding="4" cellspacing="2" valign="top">
	<tr>
		<td colspan="3" class="textstyle1"><b>ADD BREAK TIME</b></td>
	</tr>
	<tr>
		<td colspan="3" class="error"><b><?php echo $errorMsg;?></b></td>
	</tr>
	<tr>
		<td colspan="3" class="textstyle1"><b>Break Reason</b></td>
	</tr>
	<tr>
		<td colspan="3">
			<textarea name="breakTakingFor" rows="5" cols="45"><?php echo $breakTakingFor;?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="3" class="textstyle">[Your break time will start soon afetr submitting the form]</td>
	</tr>
	<tr>
		<td colspan="3">
			<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
			<input type='hidden' name='formSubmitted' value='1'>
		</td>
	</tr>
</table>
</form>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>