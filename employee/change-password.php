<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");

	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	."/includes/common-array.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");
	$employeeObj				=	new employee();
	$validator					=	new validate();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	$form						=	SITE_ROOT_EMPLOYEES  . "/forms/change-password.php";
?>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td width="30%" class='title'>Change Password</td>
		<td class='title'>Profile Image</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<?php
		if(isset($_SESSION['forceResetPassword']))
		{
			
	?>
	<tr>
		<td class='error'>Please change your password. It is for your account safety. Do change your password regularly.</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<?php
			unset($_SESSION['forceResetPassword']);
		}
		if(isset($_SESSION['success']))
		{
			
	?>
	<tr>
		<td class='title' align="center">
		 SUCCESSFULLY CHANGED PASSWORD
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<?php
			unset($_SESSION['forceResetPassword']);
		}
		if(isset($_GET['success']) && !isset($_SESSION['success']))
		{
			
	?>
	<tr>
		<td class='title' align="center">
		 SUCCESSFULLY CHANGED PASSWORD
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td  valign="top">
<?php
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		$validator ->checkField($newPassword,"","Please type new password !!");
		$validator ->checkField($reNewPassword,"","Please re-type new password !!");
		if(!empty($newPassword))
		{
			$passwordLength	=	strlen($newPassword);
			if($passwordLength < 5)
			{
				$validator ->setError("Your new password is too short !!");
			}
		}
		if(!empty($reNewPassword) && $newPassword != $reNewPassword)
		{
			$validator ->setError("New password and re-typed new password does not match !!");
		}
		$dataValid	 =	$validator ->isDataValid();
		if($dataValid)
		{
			$options 	    =   array('cost' => 12);
			$newPassword    =	password_hash($newPassword, PASSWORD_BCRYPT, $options);
			
			dbQuery("UPDATE employee_details SET password='$newPassword',passwordChangeOn='".CURRENT_DATE_INDIA."',passwordChangeTime='".CURRENT_TIME_INDIA."' WHERE employeeId=$s_employeeId");

			dbQuery("DELETE FROM employee_trusted_devices WHERE employeeId=$s_employeeId");

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES ."/change-password.php?success=1");
			exit();
		}
		
	}
	
	include($form);
?>
		</td>
		<td valign="top">
			<?php
				if(!empty($hasProfilePhoto) && !empty($profilePhotoExt))
				{
					$baseEmployeeId			=	base64_encode($s_employeeId);
					$md5EmployeeId			=	md5($s_employeeId);
					$displayEmployeeImage	=	$baseEmployeeId."_".$md5EmployeeId.".$profilePhotoExt";
			?>
			<img src="<?php echo SITE_URL_EMPLOYEES;?>/get-employee-profile-photos.php?ID=<?php echo $s_employeeId;?>&ext=<?php echo $profilePhotoExt;?>" border="0" title="Change Profile Photo - <?php echo $s_employeeName;?>" oncontextmenu="return false;" onclick='addEditMemberProfilePhoto(2)' style="cursor:pointer;">
			<br><a onclick='addEditMemberProfilePhoto(2)' class='link_style6' style="cursor:pointer;" title="Add Profile Photo">Change Your Photo</a></center>
			<?php
				}
				else
				{
			?>
			<center><img src="<?php echo SITE_URL;?>/images/member.jpeg" border="0" title="<?php echo $s_employeeName;?>">
			<br><a onclick='addEditMemberProfilePhoto(1)' class='link_style6' style="cursor:pointer;" title="Add Profile Photo">Add Photo</a></center>
			<?php
				}
			?> 
		</td>
	</tr>
</table>
<?php
	
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>