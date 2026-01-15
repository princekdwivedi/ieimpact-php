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
	
}



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
			<td class="formlink">&nbsp;&nbsp;
				<?php
					if(!empty($showFaceBookLogin)){
						echo "Welcome ".$fullName;
					}
					else{
						echo "Validate Your Email To Login";
					}
				?>
				
			</td>
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
		<?php
			if(!empty($errorMsg)){
		?>
		<tr>
			<td colspan="3" align="left">
				<?php 
					echo "<font class='error'>&nbsp;".$errorMsg."</font>";
				?>
			</td>
		</tr>
		<?php
			}
			if(empty($showFaceBookLogin)){
		?>
			<tr>
				<td align="center">
				<table width="99%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="formlink1" width="35%">Email</td>
					<td width="2%"><B>:</B></td>
					<td>
						<input type="text" size="30" name="email" value="<?php echo $email;?>" maxlength="100" class="input">
					</td>
				</tr>
				<tr>
					<td colspan="3" height="5"></td>
				</tr>		
				<tr>
					<td colspan="3">
						<table width="100%" align="center" border="0">
							<tr>
								<td align="right">
							
									<div class="g-recaptcha" data-sitekey="<?php echo GOOGLE_RECAPTCHA_SECRET_KEY; ?>"></div>
									<script type="text/javascript"
											src="https://www.google.com/recaptcha/api.js?hl=en">
									</script>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="10"></td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
					<td>
						<input type="image" name="login" SRC="<?php echo SITE_URL;?>/images/login.jpg" WIDTH="92" HEIGHT="21" BORDER="0" ALT="">
						<input type="hidden" name="formsubmitted" value ="1">
					</td>
				</tr>
				<tr>
					<td height="10"></td>
				</tr>
				</table>
				</td>
			</tr>
			<?php
				}
				else{
			?>	
			<tr>
				<td colspan="3">		
					<a href="<?php echo htmlspecialchars($facebook_login_url); ?>"><img SRC="<?php echo SITE_URL;?>/images/facebook-login-button.jpg" WIDTH="400" BORDER="0" ALT="" title="Sign in with Facebook"></a>
				</tr>
			</tr>
			<?php
				}
			?>
		</table>
		</td>
	</tr>
</table>
<table>
	<tr height="100"><td></td></tr>
</table>
</form>
