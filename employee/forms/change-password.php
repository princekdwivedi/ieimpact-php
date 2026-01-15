<script type="text/javascript">
function checkPassword()
{
	form1	=	document.changePassword;
	if(form1.newPassword.value	==	"")
	{
		alert("Please type new password !!");
		form1.newPassword.focus();
		return false;
	}
	if(form1.newPassword.value.length < 5)
	{
		alert("Your new password is too short !!");
		form1.newPassword.focus();
		return false;
	}
	if(form1.reNewPassword.value	==	"")
	{
		alert("Please re-type new password !!");
		form1.reNewPassword.focus();
		return false;
	}
	if(form1.newPassword.value != form1.reNewPassword.value)
	{
		alert("New password and re-typed new password does not match !!");
		form1.reNewPassword.focus();
		return false;
	}
}
function addEditMemberProfilePhoto(flag)
{
	path			=	"<?php echo SITE_URL_EMPLOYEES;?>/add-edit-profile-photo.php?P="+flag;
	properties	=	"height=360,width=440,top=150,left=250,scrollbars=yes,top=100,left=200";
	it			=	window.open(path,'',properties);
}
</script>
<form name="changePassword" action="" method="POST" onsubmit="return checkPassword();">
<table cellpadding="3" cellspacing="2" width="98%" border="0" align="center">
	
	<tr>
		<td class="smalltext2" width="50%" >
			Type Your New Password
		</td>
		<td  class="smalltext2" width="2%" >
			:
		</td>
		<td>
			<input type="password" name="newPassword" value="" size="15" maxlength="20">
		</td>
	</tr>
	<tr>
		<td class="smalltext2">
			Re-Type Your New Password
		</td>
		<td  class="smalltext2">
			:
		</td>
		<td>
			<input type="password" name="reNewPassword" value="" size="15" maxlength="20">
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