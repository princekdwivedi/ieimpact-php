<style>
	span.dropt {border-bottom: thin dotted; background: #ffeedd;}
	span.dropt:hover {text-decoration: none; background: #ffffff; z-index: 6; }
	span.dropt span {position: absolute; left: -9999px;
	  margin: 20px 0 0 0px; padding: 3px 3px 3px 3px;
	  border-style:solid; border-color:black; border-width:1px; z-index: 6;}
	span.dropt:hover span {left: 2%; background: #ffffff;} 
	span.dropt span {position: absolute; left: -9999px;
	  margin: 4px 0 0 0px; padding: 3px 3px 3px 3px; 
	  border-style:solid; border-color:black; border-width:1px;}
	span.dropt:hover span {margin: 20px 0 0 170px; background: #ffffff; z-index:6;}
</style>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/validate.js"></script>
<script type="text/javascript">
function setFocus()
{
	form1 = document.loginEmployee;
	form1.loginId.focus();
}
function resetField()
{
	form1 = document.loginEmployee;
	form1.loginId.value			=	"";
	form1.loginEmail.value		=	"";
	form1.password.value		=	"";
	form1.rememberPass.checked	=	false;;
	form1.calSum.value			=	"";
	return false;
}
function checkValidLogin()
{
	//return;
	form1 = document.loginEmployee;
	if(form1.loginEmail.value	==	"")
	{
		alert("Please enter your email.");
		form1.loginEmail.focus();
		return false;
	}
	if(form1.loginEmail.value != "")
	{
		if(isEmail(form1.loginEmail.value) == false)
		{
			alert("Entered email is invalid.");
			form1.loginEmail.focus();
			return false;
		}
	}
	if(form1.password.value == "")
	{
		alert("Enter your password.");
		form1.password.focus();
		return false;
	}
	if(form1.securityToken.value  == "" || form1.securityToken.value == "0")
	{
		alert("Please enter security token.");
		form1.securityToken.focus();
		return false;
	}
}
function forgotSecurityToken()
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/get-security-token.php";
	prop = "toolbar=no,scrollbars=yes,width=500,height=300,top=50,left=100";
	window.open(path,'',prop);
}

function forgotPassword()
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/forgot-password.php";
	prop = "toolbar=no,scrollbars=yes,width=500,height=300,top=50,left=100";
	window.open(path,'',prop);
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
function setFocus()
{
   form1	=	document.loginEmployee;
   form1.loginId.focus();
}
window.onload=setFocus;
</script>
<form name="loginEmployee" action="" method="POST" onsubmit="return checkValidLogin();">
<table>
	<tr height="50"><td></td></tr>
</table>
<table width="580" cellpadding="0" cellspacing="0" border="0" align="center">
<tr><td height="50"></td></tr>
<tr>
	<td background="<?php echo SITE_URL;?>/images/login1.jpg" WIDTH="179" HEIGHT="265" BORDER="0" ALT=""></td>
	<td background="<?php echo SITE_URL;?>/images/login-bg.jpg" height="265" width="401" style="border-top:1px solid #CDC7B7;border-right:1px solid #CDC7B7;border-bottom:1px solid #CDC7B7" valign="top" align="center">
	<table width="95%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td class="formlink">&nbsp;&nbsp;Employee Login Area</td>
	</tr>
	<tr>
		<td height="15"></td>
	</tr>
	<tr>
		<td><IMG SRC="<?php echo SITE_URL;?>/images/shade-line.jpg" WIDTH="363" HEIGHT="10" BORDER="0" ALT=""></td>
	</tr>
	<tr>
		<td height="15"></td>
	</tr>
	<tr>
		<td colspan="3">
			<?php
				if(!empty($error) && $error == 4)
				{
			?>
				<tr>
					<td colspan="3" align="left">
						<?php 
							echo "<font class='error'>For your security, we have locked your account due to too many attempts to Log In. Please contact ieIMPACT to unlock your account.</font>&nbsp;";
						?>
						<span class="dropt" title="" style="cursor:pointer;">More Info<span style="width:500px;">After a limited number of failed attempts to sign in to ieIMPACT, you will be temporarily locked out from trying to sign in. When your account is locked, you will not be able to sign in - even with the correct password. This lock lasts about an hour and will then clear on its own.</span></span>
					</td>
				</tr>
				<tr>
					<td height="8"></td>
				</tr>
			<?php
				}
				else
				{
					if(!empty($loginError) || !empty($errorMsg))
					{
				?>
				<tr>
					<td colspan="3" align="left">
						<?php 
							echo $errorMsg;
							echo "<font class='error'>&nbsp;".$loginError."</font>";
						?>
					</td>
				</tr>
				<?php
					}
				}
			?>
		</td>
	</tr>
	<tr>
		<td align="center">
		<table width="99%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td class="formlink1" width="35%">Email</td>
			<td width="2%"><B>:</B></td>
			<td>
				<input type="text" size="35" name="loginEmail" value="<?php echo $rememberEmail;?>" maxlength="100" class="input">
			</td>
		</tr>
		<tr>
			<td colspan="3" height="5"></td>
		</tr>
		<tr>
			<td style="display:none;">Security Captcha Code</td>
			<td class="smalltext2" style="display:none;">:</td>
			<td style="display:none;">
				<input type="text" name="securityCaptcha" value="" size="26" maxlength="150" style="display:none;" />
			</td>
		</tr>
		<tr>
			<td colspan="3" height="2"></td>
		</tr>
		<tr>
			<td style="display:none;">Login From City</td>
			<td class="smalltext2" style="display:none;">:</td>
			<td style="display:none;">
				<input type="text" name="loginFromEmpCityName" value="chandigarh" size="26" maxlength="150" style="display:none;" />
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td class="formlink1">Password</td>
			<td><B>:</B></td>
			<td>
				<input type="password" name="password" size="15" value="<?php echo $rememberEmployeePass;?>" class="input">&nbsp;&nbsp;(<a onclick="javascript:forgotPassword()" class="link_style11" style="cursor:pointer;">Forgot Password</a>?)
			</td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td class="smalltext2">
				<input type="checkbox" name="rememberCheckPass" value="1" <?php echo $pwdChecked;?>>&nbsp;Remember me in this computer
			</td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
		<tr>
			<td class="formlink1">Security Token</td>
			<td><B>:</B></td>
			<td class="smalltext2">
				<input type="text" size="3" name="securityToken" value="<?php echo $rememberSecurityToken;?>" class="input" maxlength="5" onKeyPress="return checkForNumber();">&nbsp;<font style="font-size:11px;font-family:verdana;font-color:#000000">(<a onclick="forgotSecurityToken()" class="link_style11" style="cursor:pointer;">How to get it</a>?)</font>
			</td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
		<?php
			if($showCaptcha	==	1)
			{
		?>
		<tr>
			<td colspan="3">
				<?php
					 echo recaptcha_get_html($publickey);
				?>
			</td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
		<?php
			}
		?>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td>
				<input type="image" name="login" SRC="<?php echo SITE_URL;?>/images/login.jpg" WIDTH="92" HEIGHT="21" BORDER="0" ALT="">
				<input type="hidden" name="showCaptcha" value ="<?php echo $showCaptcha;?>">
				<input type="hidden" name="formsubmitted" value ="1">&nbsp;
				<img src="<?php echo SITE_URL;?>/images/reset.jpg" WIDTH="92" HEIGHT="21" BORDER="0" ALT="" onClick ='return resetField();'>
			</td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<table>
	<tr height="100"><td></td></tr>
</table>
</form>
<script type="text/javascript">setFocus();</script>