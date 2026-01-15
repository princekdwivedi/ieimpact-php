<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT			. "/includes/send-mail.php");
	$employeeObj				=	new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");

	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$subject			=	"";
	$title				=	"";
	$message			=	"";
	$employeeId			=	0;
	$errorMsg			=	"";
	$a_employeeId		=	array();
	$joiningQuery		=	"";
	$a_allExistingEmployees	=	$employeeObj->getAllMtEmployeesWithMobile();

	$form				=	SITE_ROOT_EMPLOYEES."/forms/send-emails-to-employee.php";
?>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td colspan="2" class='title5'><b>SEND EMAILS TO EMPLOYEES</b>&nbsp;&nbsp;(<img src="<?php echo SITE_URL;?>/images/blinking-new.gif"><a href="<?php echo SITE_URL_EMPLOYEES;?>/email-sms-notice-common-page.php" class="nextLink">Send Notice-Email-SMS Together</a>)</td>
	</tr>
</table>
<?php
	if(isset($_GET['success']))
	{
		echo "<center><br><font class='error'><b>Successfully Sent Message !!</b></font><br></center>";
	}
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		///pr($_REQUEST);
		//die();
		$subject	=	trim($subject);
		$message	=	trim($message);
		$smsMessage	=	$message;
		
		if(isset($_REQUEST['pdfEmployeeId']))
		{
			$pdfEmployeeId		=	$_REQUEST['pdfEmployeeId'];
			if(!empty($pdfEmployeeId))
			{
				$a_employeeId	=	$pdfEmployeeId;
			}
		}
		
		if(empty($subject))
		{
			$errorMsg .=	"Please Enter Email Subject.<br>";
		}
		if(empty($message))
		{
			$errorMsg .=	"Please Enter Message.<br>";
		}
		if(empty($errorMsg))
		{
			if(!empty($a_employeeId))
			{
				if(!in_array("0",$a_employeeId))
				{
					$getEmployee=	implode(",",$a_employeeId);
					$andClause	=	" AND employee_details.employeeId IN ($getEmployee)";
				}
				else
				{
					$andClause	=	"";
				}
			}
			else
			{
				$andClause	=	"";
			}
			$from			=	"hr@ieimpact.com";
			$fromName		=	"HR ieIMPACT ";
			$mailSubject	=	$subject;
			$templateId		=	ADMINISTRATOR_SENDING_EMAIL_EMPLOYEES;
			$query			=	"SELECT firstName,lastName,email FROM employee_details".$joiningQuery." WHERE employee_details.isActive=1 AND email <> '' AND employee_details.hasPdfAccess=1".$andClause." ORDER BY firstName";
			$result			=	mydbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row		=	mysqli_fetch_assoc($result))
				{
					$firstName		=	$row['firstName'];
					$lastName		=	$row['lastName'];
					$employeeEmail	=	$row['email'];

					$employeeName	=	$firstName." ".$lastName;
					$employeeName	=	ucwords($employeeName);

					$a_templateData	=	array("{employeeName}"=>$employeeName,"{message}"=>$message);

					sendTemplateMail($from, $fromName, $employeeEmail, $mailSubject, $templateId, $a_templateData);
				}
			}
		}
		
		ob_clean();
		header("Location:".SITE_URL_EMPLOYEES."/send-emails-to-employees.php?success=1");
		exit();

	}
	include($form);
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>