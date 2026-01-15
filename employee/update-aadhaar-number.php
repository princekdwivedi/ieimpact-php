<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES		.   "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/common-array.php");
	$employeeObj					=	new employee();
	include(SITE_ROOT_EMPLOYEES		.   "/includes/set-variables.php");
	$errorMsg		=	"";

	if(isset($_REQUEST['formSubmitted'])){
		extract($_REQUEST);

		
		$aadhaarNumber =	 trim($aadhaarNumber);
		if(empty($aadhaarNumber)){
			$errorMsg   =    "Please enter your aadhaar number.<br />";
		}
		elseif(!is_numeric($aadhaarNumber)){
			$errorMsg  .=    "Please enter a valid aadhaar number with only digits.<br />";
		}
		elseif(strlen($aadhaarNumber) < 10){
			$errorMsg  .=    "Please enter a valid aadhaar number.<br />";
		}
		else{
			$isExistAadhaar	=	@mysql_result(dbQuery("SELECT aadhaarNumber FROM employee_details WHERE aadhaarNumber=$aadhaarNumber AND employeeId <> $s_employeeId ORDER BY employeeId DESC LIMIT 1"),0);
			if(!empty($isExistAadhaar)){
				$errorMsg  .=    "This aadhaar number is already added by other employee.<br />";
			}
		}
		if(empty($errorMsg)){
			
			dbQuery("UPDATE employee_details SET aadhaarNumber=$aadhaarNumber WHERE employeeId = $s_employeeId");

			$_SESSION['successUpdateAadhar']	=	1;

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/update-aadhaar-number.php");
			exit();
		}
	}
?>
<script type="text/javascript">
	function checkValidAadhaar()
	{
		form1	=	document.addAadhaar;
		if(form1.aadhaarNumber.value	==	"" || form1.aadhaarNumber.value	==	" " || form1.aadhaarNumber.value	==	"0")
		{
			alert("Please enter your aadhaar number.");
			form1.aadhaarNumber.focus();
			return false;
		}
		else{
			if(form1.aadhaarNumber.value.length < 10){
				alert("Please enter a valid aadhaar number.");
				form1.aadhaarNumber.focus();
				return false;
			}
		}
	}

	function checkForNumber()
	{
		k = (document.all)?event.keyCode : arguments.callee.caller.arguments[0].which;
		if(k == 8 || k== 0)
		{
			return true;
		}
		if(k >= 48 && k <= 57 )
		{
			return true;
		}
		else
		{
			return false;
		}
	 }
</script>
<form  name='addAadhaar' method='POST' action="" onsubmit="return checkValidAadhaar();">
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='textstyle3' valign="top" colspan="3"><b>Update Your Aadhaar Number</a></td>
	</tr>
	<?php 
		if(isset($_SESSION['successUpdateAadhar']) && $_SESSION['successUpdateAadhar'] == 1)
		{
			echo "<tr><td colspan='4'><font size='3' color='red'>Successfully updated Aadhaar Number.</td></tr>";

			unset($_SESSION['successUpdateAadhar']);
		}	
		if(!empty($errorMsg))
		{
			echo "<tr><td colspan='4'><font size='3' color='red'>$errorMsg</td></tr>";
		}	
	?>
	<tr>
		<td width="17%" class="textstyle1">Enter Your Aadhaar Number</td>
		<td width="2%" class="textstyle1">:</td>
		<td width="15%">
			<input type="text" name="aadhaarNumber" value="<?php echo $aadhaarNumber;?>"  maxlength="13" onKeyPress="return checkForNumber();" class='form_text_email' style="width:160px">
		</td>
		<td>
			<input type='image' name='submit' src='<?php echo SITE_URL;?>/images/submit.jpg'>
			<input type='hidden' value='1' name='formSubmitted'>
		</td>
	</tr>
	<tr>
		<td height="100"></td>
	</tr>
</table>
</form>
<?php	
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>