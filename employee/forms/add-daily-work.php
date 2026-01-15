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
 function showProperties(flag)
 {
	if(flag  == 4 || flag  == 5)
	{
		document.getElementById('showProperties').style.display = 'inline';
		document.getElementById('hideProperties').style.display = 'none';
	}
	else 
	{
		document.getElementById('showProperties').style.display = 'none';
		document.getElementById('hideProperties').style.display = 'inline';
	}
 }
 function deleteWork(workId,rec,search,date)
 {
	var confirmation = window.confirm("Are you sure to delete these lines?");
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/add-edit-employee-report.php?workId="+workId+"&rec="+rec+"&search="+search+"&date="+date+"&isDelete=1";
	}
 }
</script>
<form  name='addWork' method='POST' action="" onsubmit="return checkValidWork();">
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='textstyle3' valign="top" colspan="3"><b><?php echo $text;?> Lines</a></td>
	</tr>
	<?php 
		if(!empty($errorMsg))
		{
			echo "<tr><td colspan='3'><font size='3' color='red'>$errorMsg</td></tr>";
		}
	
	?>
	<tr>
		<td width="25%" class="textstyle1">Platform</td>
		<td width="2%" class="textstyle1">:</td>
		<td>
			<?php
				$url2	=	SITE_URL_EMPLOYEES. "/get-platform-customer.php?parentId=";
			?>
			<select name="platform"  onchange="commonFunc('<?php echo $url2?>','displayCustomer',this.value);showProperties(this.value);" class='form_text_email'>
				<option value="">Select</option>
				<?php
					if($result = $employeeObj->getPlatformByDepartment(1))
					{
						while($row	=	mysql_fetch_assoc($result))
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
		<td class="textstyle1">Client</td>
		<td class="textstyle1">:</td>
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
			<div  id="hideProperties" style="display:<?php echo $display;?>">
			<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
				<tr>
					<td width="25%">&nbsp;</td>
					<td width="2%">&nbsp;</td>
					<td width="10%" class="smalltext2" valign="top">DSP/Direct</td>
					<td width="14%" class="smalltext2"  valign="top">N-DSP/Indirect/Pended</td>
					<td class="smalltext2"  valign="top">USER ID</td>
				</tr>
				<tr>
					<td class="textstyle1" valign="top">Transcription (SINGLE)</td>
					<td class="textstyle1" valign="top">:</td>
					<td class="smalltext2" valign="top">
						<input type="text" name="transcriptionLinesEntered" value="<?php echo $transcriptionLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();" class='form_text_email' style="width:80px" onCopy="return false" onDrag="return false" onDrop="return false" onPaste="return false" autocomplete=off>
					</td>
					<td valign="top">
						<input type="text" name="indirectTranscriptionLinesEntered" value="<?php echo $indirectTranscriptionLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();" class='form_text_email' style="width:80px"  onCopy="return false" onDrag="return false" onDrop="return false" onPaste="return false" autocomplete=off>
					</td>
					<td valign="top"> 
						<input type="text" name="transcriptionUserId" value="<?php echo $transcriptionUserId;?>" size="10" maxlength="30" class='form_text_email' style="width:80px">
					</td>
				</tr>
				<tr>
					<td align='center' colspan="5">&nbsp;</td>
				</tr>
				<tr>
					<td class="textstyle1" valign="top">VRE</td>
					<td class="textstyle1" valign="top">:</td>
					<td valign="top">
						<input type="text" name="vreLinesEntered" value="<?php echo $vreLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();" class='form_text_email' style="width:80px"  onCopy="return false" onDrag="return false" onDrop="return false" onPaste="return false" autocomplete=off>&nbsp;&nbsp;
					</td>
					<td valign="top">
						<input type="text" name="indirectVreLinesEntered" value="<?php echo $indirectVreLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();" class='form_text_email' style="width:80px"  onCopy="return false" onDrag="return false" onDrop="return false" onPaste="return false" autocomplete=off>
					</td>
					<td valign="top">
						<input type="text" name="vreUserId" value="<?php echo $vreUserId;?>" size="10" maxlength="30" class='form_text_email' style="width:80px">
					</td>
				</tr>
				<tr>
					<td align='center' colspan="5">&nbsp;</td>
				</tr>
				<tr>
					<td class="textstyle1" valign="top">QA</td>
					<td class="textstyle1" valign="top">:</td>
					<td valign="top">
						<input type="text" name="qaLinesEntered" value="<?php echo $qaLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();" class='form_text_email' style="width:80px"  onCopy="return false" onDrag="return false" onDrop="return false" onPaste="return false" autocomplete=off>&nbsp;&nbsp;
					</td>
					<td valign="top">
						<input type="text" name="indirectQaLinesEntered" value="<?php echo $indirectQaLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();" class='form_text_email' style="width:80px"  onCopy="return false" onDrag="return false" onDrop="return false" onPaste="return false" autocomplete=off>
					</td>
					<td valign="top">
						<input type="text" name="qaUserId" value="<?php echo $qaUserId;?>" size="10" maxlength="30" class='form_text_email' style="width:80px">
					</td>
				</tr>
				<tr>
					<td align='center' colspan="4">&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td class="smalltext2" valign="top">Transcription</td>
					<td class="smalltext2"  valign="top">VRE</td>
					<td class="smalltext2"  valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td class="textstyle1" valign="top">Night shift lines</td>
					<td class="textstyle1" valign="top">:</td>
					<td>
						<input type="text" name="auditLinesEntered" value="<?php echo $auditLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();" class='form_text_email' style="width:80px"  onCopy="return false" onDrag="return false" onDrop="return false" onPaste="return false" autocomplete=off>&nbsp;&nbsp;
					</td>
					<td valign="top">
						<input type="text" name="indirectAuditLinesEntered" value="<?php echo $indirectAuditLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();" class='form_text_email' style="width:80px"  onCopy="return false" onDrag="return false" onDrop="return false" onPaste="return false" autocomplete=off>
					</td>
					<td valign="top">
						<input type="text" name="auditUserId" value="<?php echo $auditUserId;?>" size="10" maxlength="30" class='form_text_email' style="width:80px">
					</td>
				</tr>
				<tr>
					<td align='center' colspan="4">&nbsp;</td>
				</tr>
			</table>
		 </div>
		</td>
	</tr>
	<!--<tr>
		<td colspan="3">
			<div  id="showProperties" style="display:<?php echo $display1;?>">
			<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
				<tr>
					<td width="20%">&nbsp;</td>
					<td width="2%">&nbsp;</td>
					<td width="15%" class="title">LEVEL1</td>
					<td  class="title">LEVEL2</td>
				</tr>
				<tr>
					<td class="title">Direct</td>
					<td class="title">:</td>
					<td class="smalltext2">
						<input type="text" name="directLevel1" value="<?php echo $directLevel1;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
					</td>
					<td>
						<input type="text" name="directLevel2" value="<?php echo $directLevel2;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
					</td>
				</tr>
				<tr>
					<td align='center' colspan="4">&nbsp;</td>
				</tr>
				<tr>
					<td class="title">Indirect</td>
					<td class="title">:</td>
					<td>
						<input type="text" name="indirectLevel1" value="<?php echo $indirectLevel1;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">&nbsp;&nbsp;
					</td>
					<td>
						<input type="text" name="indirectLevel2" value="<?php echo $indirectLevel2;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
					</td>
				</tr>
				<tr>
					<td align='center' colspan="4">&nbsp;</td>
				</tr>
				<tr>
					<td class="title">QA</td>
					<td class="title">:</td>
					<td>
						<input type="text" name="qaLevel1" value="<?php echo $qaLevel1;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">&nbsp;&nbsp;
					</td>
					<td>
						<input type="text" name="qaLevel2" value="<?php echo $qaLevel2;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
					</td>
				</tr>
				<tr>
					<td align='center' colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td class="title">Post Audit</td>
					<td class="title">:</td>
					<td>
						<input type="text" name="auditLevel1" value="<?php echo $auditLevel1;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">&nbsp;&nbsp;
					</td>
					<td>
						<input type="text" name="auditLevel2" value="<?php echo $auditLevel2;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
					</td>
				</tr>
				<tr>
					<td align='center' colspan="3">&nbsp;</td>
				</tr>
			</table>
			</div>
		</td>
	</tr>-->
	<tr>
		<td class="textstyle1" valign="top">Comments</td>
		<td class="textstyle1" valign="top">:</td>
		<td>
			<textarea name="comments" rows="5" cols="45" class="form_textarea" style="width:370px;height:120px;"><?php echo $comments;?></textarea>
		</td>
	</tr>
	<tr>
		<td align='center' colspan="3">&nbsp;</td>
	</tr>
	<?php
		if(empty($datewiseID))
		{
	?>
	<tr>
		<td class="textstyle1" valign="top">Work Added For</td>
		<td class="textstyle1" valign="top">:</td>
		<td class="smalltext2">
			<?php
				foreach($a_workDoneDates as $k=>$v)
				{

					$checked		=	"";
					if($k			==	$chooseWorkAddedOn)
					{
						$checked	=	"checked";
					}
			?>
			<input type="radio" name="chooseWorkAddedOn" value="<?php echo $k;?>" <?php echo $checked;?>>&nbsp;<?php echo $v;?>
			<?php
				}
			?>
		</td>
	</tr>
	<tr>
		<td align='center' colspan="3">&nbsp;</td>
	</tr>
	<?php
		}			
	?>
	<tr>
		<td align='center' colspan="2">&nbsp;</td>
		<td>
			<input type='image' name='submit' src='<?php echo SITE_URL;?>/images/submit.jpg'>
			<!--<img src='<?php echo SITE_URL;?>/images/cancel.jpg' onClick="history.back()" value="Cancel">-->
			<input type='hidden' value='1' name='formSubmitted'>
			<?php
				if(!empty($date) && !empty($employeeId) && !empty($workId))
				{
			?>
				<input type="button" name="delete" value="Delete " onClick="deleteWork(<?php echo $workId;?>,0,'<?php echo $employeeName;?>','<?php echo $date;?>')" style="cursor:pointer;">
			<?php
				}
			?>
		</td>
	</tr>


</table>
</form>