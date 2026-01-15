<script type="text/javascript" src="<?php echo SITE_URL;?>/script/validate.js"></script>
<script type="text/javascript">
function checkValidEmail()
{
	//return;
	form1		=	document.forgotEmail;
	if(form1.email.value == "")
	{
		alert("Please enter your email.");
		form1.email.focus();
		return false;
	}
	if(form1.email.value != "")
	{
		if(isEmail(form1.email.value) == false)
		{
			alert("Your email is invalid.");
			form1.email.focus();
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

<form name="forgotEmail" action="" method="POST"  onsubmit="return checkValidEmail();">
<table width="100%" align="center" border="0" cellpadding="3" cellspacing="0">
	<tr>
		<td colspan="6"><font class="heading5"><b><?php echo $formTopText;?></b></font></td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td width="8%" class="fronttext1"><b>Email</b></td>
		<td width="1%" class="fronttext1"><b>:</b></td>
		<td>
			<input type="text" name="email" value="<?php echo $email;?>" size="25" maxlength="80" style="border:1px solid #333333">
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
		<td>
			<input type="submit" name="submit" value="Submit Your Email">
			<input type="hidden" name="formSubmitted" value="1">
		</td>
	</tr>
</table>
</form>