<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>
<script type="text/javascript">
function checkValidMessage()
{
	form1	=	document.messageToEmployee;
	if(form1.subject.value == "")
	{
		alert("Please Enter Message Subject.");
		form1.subject.focus();
		return false;
	}
	if(form1.message.value == "")
	{
		alert("Please Enter Message.");
		form1.message.focus();
		return false;
	}

	var confirmation = window.confirm("Please confirm the department before sending the message?");
	if(confirmation == true)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function showEmployee(flag)
{
	if(flag  == 1)
	{
		document.getElementById('displayAllEmployee').style.display = 'none';
		document.getElementById('displayMtEmployee').style.display  = 'inline';
		document.getElementById('displayRevEmployee').style.display = 'none';
		document.getElementById('displayPdfEmployee').style.display = 'none';
	}
	else if(flag  == 2) 
	{
		document.getElementById('displayAllEmployee').style.display = 'none';
		document.getElementById('displayMtEmployee').style.display  = 'none';
		document.getElementById('displayRevEmployee').style.display = 'inline';
		document.getElementById('displayPdfEmployee').style.display = 'none';
	}
	else if(flag  == 3) 
	{
		document.getElementById('displayAllEmployee').style.display = 'none';
		document.getElementById('displayMtEmployee').style.display  = 'none';
		document.getElementById('displayRevEmployee').style.display = 'none';
		document.getElementById('displayPdfEmployee').style.display = 'inline';
	}
	else if(flag  == "")
	{
		document.getElementById('displayAllEmployee').style.display = 'inline';
		document.getElementById('displayMtEmployee').style.display  = 'none';
		document.getElementById('displayRevEmployee').style.display = 'none';
		document.getElementById('displayPdfEmployee').style.display = 'none';
	}
}
</script>
<form name="messageToEmployee" method='POST' action="" onsubmit="return checkValidMessage();">
<table width="98%" border="0" align="center" cellpadding="4" cellspacing="2" valign="top">
	<tr>
		<td colspan="6" class="error">
			<?php echo $errorMsg;?>
		</td>
	</tr>
	<tr>
		<td width="24%" class="smalltext2" valign="top"><b>SEND THIS MESSAGE TO </b></td>
		<td width="2%" class="smalltext2"  valign="top"><b>:</b></td>
		<td colspan="4"  valign="top">
			<select name="pdfEmployeeId[]"  multiple style='height:200px;'>
				<option value="">All Employee</option>
				<?php
					if($result	=	$employeeObj->getAllPdfEmployees())
					{
						while($row	=	mysqli_fetch_assoc($result))
						{
							$t_employeeId	=	$row['employeeId'];
							$firstName		=	$row['firstName'];
							$lastName		=	$row['lastName'];
							$employeeName	=	$firstName." ".$lastName;

							$select			=	"";
							if(in_array($t_employeeId,$a_employeeId))
							{
								$select		=	"selected";
							}
							echo "<option value='$t_employeeId' $select>".ucwords($employeeName)."</option>";
						}	
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
		<td class="smalltext1" colspan="5">
			[Use Ctrl+Select to select multiple employees]
		</td>
	</tr>
	<tr>
		<td width="20%" class="smalltext2"><b>EMAIL SUBJECT</b></td>
		<td width="2%" class="smalltext2"><b>:</b></td>
		<td colspan="4">
			<input type="text" name="subject" value="<?php echo $subject;?>" size="86" maxlength="150">
		</td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top"><b>MESSAGE</b></td>
		<td class="smalltext2"  valign="top"><b>:</b></td>
		<td colspan="4"  valign="top">
			<textarea name="message" cols="85" rows="12"><?php echo $message;?></textarea>
		</td>
	</tr>
	<!--<tr>
		<td colspan="8" class="error"><input type="checkbox" name="sendSMS" value="1"><b>CLICK HERE TO SEND THIS MESSAGE AS SMS TO EMPLOYEE MOBILE</b></td>
	</tr>-->
	<tr>
		<td colspan="2"></td>
		<td colspan="6">
			<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
			<input type='hidden' name='formSubmitted' value='1'>
		</td>
	</tr>
</table>
</form>
