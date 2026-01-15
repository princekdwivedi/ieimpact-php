<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>

<script type="text/javascript">
function checkValidWork()
{
	form1	=	document.addWork;
	if(form1.platform.value	==	"")
	{
		alert("Please Select A Platform.");
		form1.platform.focus();
		return false;
	}
	if(form1.customerId.value	==	"")
	{
		alert("Please Select A Client.");
		form1.customerId.focus();
		return false;
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
<form  name='addWork' method='POST' action="" onsubmit="return checkValidWork();">
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='title' valign="top" colspan="3"><?php echo $headingText;?> Work</td>
	</tr>
	<?php 
		if(!empty($errorMsg))
		{
			echo "<tr><td colspan='3'><font size='3' color='red'>$errorMsg</td></tr>";
		}
	
	?>
	<tr>
		<td width="42%" class="title1">Platform</td>
		<td width="2%" class="title1">:</td>
		<td>
			<?php
				$url2	=	SITE_URL_EMPLOYEES. "/get-platform-customer.php?parentId=";
			?>
			<select name="platform"  onchange="commonFunc('<?php echo $url2?>','displayCustomer',this.value);showProperties(this.value);" class='form_text_email'>
				<option value="">Select</option>
				<?php
					if($result = $employeeObj->getPlatformByDepartment(1))
					{
						while($row			=	mysql_fetch_assoc($result))
						{
							$t_parentId		=	$row['platfromId'];
							$t_parentName	=	$row['name'];

							$select		 =	"";
							if($t_parentId == $platform)
							{
								$select	 =	"selected";
							}
							echo "<option value='$t_parentId' $select>$t_parentName</option>";
						}
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td align='center' colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td class="title1">Client</td>
		<td class="title1">:</td>
		<td>
			<div id="displayCustomer">
			<select name="customerId" class='form_text_email'>
				<option value="">Select</option>
				<?php
					if(!empty($platform) && !empty($customerId))
					{
						if($result = $employeeObj->getPlatformClients($platform))
						{
							while($row	=	mysql_fetch_assoc($result))
							{
								$t_customerId	=	$row['customerId'];
								$customerName	=	$row['name'];

								$select		 =	"";
								if($customerId == $t_customerId)
								{
									$select	 =	"selected";
								}
								
								echo "<option value='$t_customerId' $select>$customerName</option>";
							}
						}
					}
				?>
			</select>
			</div>
		</td>
	</tr>
	<tr>
		<td align='center' colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">
			<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
				<tr>
					<td width="42%">&nbsp;</td>
					<td width="2%">&nbsp;</td>
					<td width="15%" class="title1" valign="top">DSP/Direct</td>
					<td width="20%" class="title2"  valign="top">N-DSP/Indirect/Pended</td>
					<td class="title2"  valign="top">USER ID</td>
				</tr>
				<tr>
					<td class="title1" valign="top">Transcription (SINGLE)</td>
					<td class="title1" valign="top">:</td>
					<td class="smalltext2" valign="top">
						<input type="text" name="transcriptionLinesEntered" value="<?php echo $transcriptionLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();" class='form_text_email' style="width:80px">
					</td>
					<td valign="top">
						<input type="text" name="indirectTranscriptionLinesEntered" value="<?php echo $indirectTranscriptionLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();" class='form_text_email' style="width:80px">
					</td>
					<td valign="top"> 
						<input type="text" name="transcriptionUserId" value="<?php echo $transcriptionUserId;?>" size="10" maxlength="30" class='form_text_email' style="width:80px">
					</td>
				</tr>
				<tr>
					<td align='center' colspan="5">&nbsp;</td>
				</tr>
				<tr>
					<td class="title1" valign="top">VRE</td>
					<td class="title1" valign="top">:</td>
					<td valign="top">
						<input type="text" name="vreLinesEntered" value="<?php echo $vreLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();" class='form_text_email' style="width:80px">&nbsp;&nbsp;
					</td>
					<td valign="top">
						<input type="text" name="indirectVreLinesEntered" value="<?php echo $indirectVreLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();" class='form_text_email' style="width:80px">
					</td>
					<td valign="top">
						<input type="text" name="vreUserId" value="<?php echo $vreUserId;?>" size="10" maxlength="30" class='form_text_email' style="width:80px">
					</td>
				</tr>
				<tr>
					<td align='center' colspan="5">&nbsp;</td>
				</tr>
				<tr>
					<td class="title1" valign="top">QA</td>
					<td class="title1" valign="top">:</td>
					<td valign="top">
						<input type="text" name="qaLinesEntered" value="<?php echo $qaLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();" class='form_text_email' style="width:80px">&nbsp;&nbsp;
					</td>
					<td valign="top">
						<input type="text" name="indirectQaLinesEntered" value="<?php echo $indirectQaLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();" class='form_text_email' style="width:80px">
					</td>
					<td valign="top">
						<input type="text" name="qaUserId" value="<?php echo $qaUserId;?>" size="10" maxlength="30" class='form_text_email' style="width:80px">
					</td>
				</tr>
				<tr>
					<td align='center' colspan="4">&nbsp;</td>
				</tr>
				<tr>
					<td class="title1" valign="top">Lines pended for Blanks/Technical Issues</td>
					<td class="title1" valign="top">:</td>
					<td>
						<input type="text" name="auditLinesEntered" value="<?php echo $auditLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();" class='form_text_email' style="width:80px">&nbsp;&nbsp;
					</td>
					<td valign="top">
						<input type="text" name="indirectAuditLinesEntered" value="<?php echo $indirectAuditLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();" class='form_text_email' style="width:80px">
					</td>
					<td valign="top">
						<input type="text" name="auditUserId" value="<?php echo $auditUserId;?>" size="10" maxlength="30" class='form_text_email' style="width:80px">
					</td>
				</tr>
				<tr>
					<td align='center' colspan="4">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="title1" valign="top">Comments</td>
		<td class="title1" valign="top">:</td>
		<td>
			<textarea name="comments" class="form_textarea" style="width:370px;height:120px;"><?php echo $comments;?></textarea>
		</td>
	</tr>
	<tr>
		<td align='center' colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td align='center' colspan="2">&nbsp;</td>
		<td>
			<input type='image' name='submit' src='<?php echo SITE_URL;?>/images/submit.jpg'>
			<input type='hidden' value='1' name='formSubmitted'>
		</td>
	</tr>
</table>
</form>