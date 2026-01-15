<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES		. "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES		. "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES		. "/includes/check-pdf-login.php");	
	include(SITE_ROOT_EMPLOYEES		. "/classes/employee.php");
	include(SITE_ROOT				. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		. "/includes/common-array.php");	
	include(SITE_ROOT				. "/includes/send-mail.php");
	$employeeObj					= new employee();
	$employeeId						= 0;
	$errorMsg						= "";

	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	if(isset($_GET['ID']))
	{
		$employeeId					=	$_GET['ID'];
		$query						=	"SELECT * FROM employee_details WHERE employeeId=$employeeId AND isActive=1";
		$result						=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$row					=	mysqli_fetch_assoc($result);
			$fullName				=	stripslashes($row['fullName']);
			$email					=	stripslashes($row['email']);
		}
	}
?>
<html>
<head>
<TITLE></TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
<script type="text/javascript">
	function reflectChange()
	{
		parent.location.reload();
	}
</script>
</head>
	<body>
		<?php
			if(isset($_REQUEST['formSubmitted'])){
				extract($_REQUEST);
				//pr($_REQUEST);
				
				$newPassword	=	trim($newPassword);
				$reNewPassword	=	trim($reNewPassword);
				$sendingPassword=	$newPassword;

				if(!empty($newPassword) && !empty($reNewPassword) && $newPassword == $reNewPassword){
					$options 	    =   array('cost' => 12);
			        $newPassword    =	password_hash($newPassword, PASSWORD_BCRYPT, $options);
		
					dbQuery("UPDATE employee_details SET password='$newPassword',passwordChangeOn='".CURRENT_DATE_INDIA."',passwordChangeTime='".CURRENT_TIME_INDIA."' WHERE employeeId=$employeeId");

					if(isset($_POST['isSendPassword'])){
						$from			=	"hr@ieimpact.com";
						$fromName		=	"HR ieIMPACT ";
		

						$templateId		=	ADMINISTRATOR_SENDING_EMAIL_EMPLOYEES;
						$mailSubject	=	"Changed employee area password";
						$message		=	"We have temporarily change your password. Your new password is : <b>".$sendingPassword."</b>. Please change your password and set according to your convenience you logged in.";

						$a_templateData	=	array("{employeeName}"=>$fullName,"{message}"=>$message);

						sendTemplateMail($from, $fromName, $email, $mailSubject, $templateId, $a_templateData);
					}
					echo "<center><font class='smalltext23'><b>Successfully changed password for the employee.</b></font></center>";
					echo "<script type='text/javascript'>reflectChange();</script>";
				}
				else{
					$errorMsg	=	"Oops seems something is missing, please retry.";
				}
				
			}
		?>
		<script type='text/javascript'>
			function validPass(){
				form1	=	document.changeEmppass;
				if(form1.newPassword.value	==	"")
				{
					alert("Please type new password.");
					form1.newPassword.focus();
					return false;
				}
				if(form1.newPassword.value.length < 5)
				{
					alert("Your new password is too short.");
					form1.newPassword.focus();
					return false;
				}
				if(form1.reNewPassword.value	==	"")
				{
					alert("Please re-type new password.");
					form1.reNewPassword.focus();
					return false;
				}
				if(form1.newPassword.value != form1.reNewPassword.value)
				{
					alert("New password and re-typed new password does not match.");
					form1.reNewPassword.focus();
					return false;
				}
			}
		</script>
		<form name="changeEmppass" action="" method="POST" onsubmit="return validPass();">
			<table cellpadding="3" cellspacing="2" width="98%" border="0" align="center">
				<tr>
					<td colspan="3" class="smalltext23"><b>Change Password For - <?php echo $fullName;?></b></td>
				</tr>
				<?php
					if(!empty($errorMsg)){
				?>
				<tr>
					<td colspan="3" class="error"><b><?php echo $errorMsg;?></b></td>
				</tr>
				<?php
					}
				?>
				<tr>
					<td class="smalltext23" width="30%" >
						Type New Password
					</td>
					<td  class="smalltext23" width="2%" >
						:
					</td>
					<td>
						<input type="password" name="newPassword" value="" size="15" maxlength="20" style="border:1px solid #333333;">
					</td>
				</tr>
				<tr>
					<td class="smalltext23">
						Re-Type New Password
					</td>
					<td  class="smalltext23">
						:
					</td>
					<td>
						<input type="password" name="reNewPassword" value="" size="15" maxlength="20" style="border:1px solid #333333;">
					</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
					<td class="smalltext23">
						<input type="checkbox" name="isSendPassword" value="1">Send an email to employee with new password.
					</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
					<td>
						<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
						<input type='hidden' name='formSubmitted' value='1'>
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>