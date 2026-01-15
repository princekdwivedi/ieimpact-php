<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	ini_set('display_errors', 1);
	$docTitle					=	"New Employee Registration";
	include(SITE_ROOT_EMPLOYEES .   "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES .   "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES .   "/classes/employee.php");
	include(SITE_ROOT			.   "/classes/validate-fields.php");

	if(isset($_SESSION['isValidationRegsitartionDone'])){
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/registration.php");
		exit();
	}

	$errorMsg 			=	"";

	if(isset($_POST['passFormSubmit'])){

		$validatePass 	=	trim($_POST['validatePass']);
		if(empty($validatePass)){
			$errorMsg   =	"Please enter password.";
		}
		elseif($validatePass != UNLOCK_REGISTRATION_PAGE){
			$errorMsg   =	"Please enter a valid password.";
		}
		else{
			$_SESSION['isValidationRegsitartionDone'] = time();

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/registration.php");
			exit();
		}
	}

?>
<script type="text/javascript">
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


function check()
{
	//return;
	form1	=	document.valRegistration;
	if(form1.validatePass.value	==	"" || form1.validatePass.value	==	" " || form1.validatePass.value	==	"0" || form1.validatePass.value	==	"  ")
	{
		alert("Please enter password.");
		form1.validatePass.focus();
		return false;
	}
}
</script>
<br /><br /><br /><br /><br />
<form name="valRegistration" action="" method="POST" enctype="" onSubmit="return check();">
	<table align='center' cellpadding="3" cellspacing="1" border="0" width="27%" style="border:1px solid #033A61">
		<tr height="20">
			<td bgcolor="#EBEEF5" colspan="3"><font class="heading1">Unlock Employee Registration Form</font></td>
		</tr>
		<tr>	
			<td height='10'></td>
		</tr>
		<?php
			if(!empty($errorMsg)){
				echo "<tr><td colspan='3' class='error'>".$errorMsg."</td></tr>";
			}
		?>
		<tr>	
			<td width="40%" class="text5">Enter Password :</td>
			<td width="18%">
				<input type="password" name="validatePass" value="" class="textbox2" maxlength="10" size="10" onkeypress="return checkForNumber();" autocomplete="off">
			</td>
			<td>
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='passFormSubmit' value='1'>
			</td>
		</tr>
		<tr>	
			<td height='10'></td>
		</tr>
    </table>
</form>
<br /><br /><br /><br /><br />
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>