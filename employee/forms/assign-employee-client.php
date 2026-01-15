<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>
<script type="text/javascript">
function showClients(flag)
{

	var x = document.getElementsByName('platform['+flag+']');
	var c = x[0].checked;

	if(c == true)
	{
		document.getElementById('showHide'+flag).style.display = 'inline';
	}
	else
	{
		document.getElementById('showHide'+flag).style.display = 'none';
	}
}
function checkValidShift()
{
	form1	=	document.addShift;
	if(form1.departmentId.value	==	"")
	{
		alert("Please select a department !!");
		form1.departmentId.focus();
		return false;
	}
	if(form1.shiftId.value	==	"")
	{
		alert("Please select a shift !!");
		form1.shiftId.focus();
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
<form  name='addShift' method='POST' action="" onsubmit="return checkValidShift();">
	<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
		<tr>
			<td width="25%" class="title">Department</td>
			<td width="2%" class="title">:</td>
			<td>
				<select name="departmentId">
					<option value="">Select</option>
					<?php
						foreach($a_department as $key=>$value)
						{
							$select	=	"";
							if($key	==	$departmentId)
							{
								$select	=	"selected";
							}

							echo "<option value='$key' $select>$value</option>";
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="title">Shift</td>
			<td class="title">:</td>
			<td>
				<select name="shiftId">
					<option value="">Select</option>
					<?php
						foreach($a_shift as $key=>$value)
						{
							$select	=	"";
							if($key	==	$shiftId)
							{
								$select	=	"selected";
							}

							echo "<option value='$key' $select>$value</option>";
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="title" valign="top">Select Platform</td>
			<td class="title" valign="top">:</td>
			<td class='smalltext2'>
				<?php
					foreach($a_platform as $key=>$value)
					{
						$checked	=	"";
						if(array_key_exists($key,$a_employeePlatform))
						{
							$checked	=	"checked";
						}
						echo "<input type='checkbox' name='platform[$key]' value='1' onclick='javascript:showClients($key)' $checked><b>$value</b>&nbsp;&nbsp;";
					}
				?>
			</td>
		</tr>
		<tr>
			<td colspan='3' valign="top">
				<div  id="showHide1" style="display:<?php echo $displayDictaphone;?>">
					<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" valign="top">
					<tr>
						<td width="25%" class="title"  valign="top">Select Dictaphone Client</td>
						<td width="2%" class="title"  valign="top">:</td>
						<td>
							<?php
								foreach($a_platform1 as $key=>$value)
								{
									
									$checked	=	"";
									if(array_key_exists($key,$a_dictaphoneClients))
									{
										$checked	=	"checked";
									}
									echo "<input type='checkbox' name='dictaphone[$key]' value='$key' $checked>$value&nbsp;&nbsp;";
								}
							?>
						</td>
					</tr>
					</table>
				</div>
				<br>
				<div  id="showHide2" style="display:<?php echo $displayEscription;?>">
					<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" valign="top">
					<tr>
						<td width="25%" class="title"  valign="top">Select Escription Client</td>
						<td width="2%" class="title"  valign="top">:</td>
						<td>
							<?php
								foreach($a_platform2 as $key=>$value)
								{
									$checked	=	"";
									if(array_key_exists($key,$a_escriptionClients))
									{
										$checked	=	"checked";
									}
									echo "<input type='checkbox' name='escription[$key]' value='$key' $checked>$value&nbsp;&nbsp;";
								}
							?>
						</td>
					</tr>
					</table>
				</div>
				<br>
				<div  id="showHide3" style="display:<?php echo $displayNetcare;?>">
					<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" valign="top">
					<tr>
						<td width="25%" class="title"  valign="top">Select Netcare Client</td>
						<td width="2%" class="title"  valign="top">:</td>
						<td>
							<?php
								foreach($a_platform3 as $key=>$value)
								{
									$checked	=	"";
									if(array_key_exists($key,$a_netcareClients))
									{
										$checked	=	"checked";
									}
									echo "<input type='checkbox' name='netcare[$key]' value='$key' $checked>$value&nbsp;&nbsp;";
								}
							?>
						</td>
					</tr>
					</table>
				</div>
				<br>
				<div  id="showHide4" style="display:<?php echo $displayProperties;?>">
					<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" valign="top">
					<tr>
						<td width="25%" class="title"  valign="top">Select Properties Client</td>
						<td width="2%" class="title"  valign="top">:</td>
						<td>
							<?php
								foreach($a_platform4 as $key=>$value)
								{
									$checked	=	"";
									if(array_key_exists($key,$a_propertiesClients))
									{
										$checked	=	"checked";
									}
									echo "<input type='checkbox' name='properties[$key]' value='$key' $checked>$value&nbsp;&nbsp;";
								}
							?>
						</td>
					</tr>
					</table>
				</div>
				<br>
				<div  id="showHide5" style="display:<?php echo $displayPdfReports;?>">
					<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" valign="top">
					<tr>
						<td width="25%" class="title"  valign="top">Select PDF Reports Client</td>
						<td width="2%" class="title"  valign="top">:</td>
						<td>
							<?php
								foreach($a_platform5 as $key=>$value)
								{
									$checked	=	"";
									if(array_key_exists($key,$a_pdfClients))
									{
										$checked	=	"checked";
									}
									echo "<input type='checkbox' name='pdf[$key]' value='$key' $checked>$value&nbsp;&nbsp;";
								}
							?>
						</td>
					</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
	<br>
	<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" valign="top">
		<tr>
			<td width="45%" valign="top">
				<table width="100%" border="0" align="center" cellpadding="0" cellspacing="2" valign="top" style="border:1px solid #e4e4e4">
					<tr>
						<td colspan="4" class="title1">
							ENTER RATES FOR Dictaphone,Escription,Netcare,
						</td>
					</tr>
					<tr>
						<td colspan="4">
							<hr size="1" width="100%" color="#bebebe">
						</td>
					</tr>
					<tr>
						<td width="50%">&nbsp;</td>
						<td width="1%">&nbsp;</td>
						<td width="25%" class="title1">DSP</td>
						<td class="title1">N-DSP</td>
					</tr>
					<tr>
						<td align='center' colspan="4">&nbsp;</td>
					</tr>
					<tr>
						<td class="title1">Transcription (SINGLE)</td>
						<td class="title1">:</td>
						<td class="smalltext2">
							<input type="text" name="directTranscriptionRate" value="<?php echo $directTranscriptionRate;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
						</td>
						<td>
							<input type="text" name="indirectTranscriptionRate" value="<?php echo $indirectTranscriptionRate;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
						</td>
					</tr>
					<tr>
						<td align='center' colspan="4">&nbsp;</td>
					</tr>
					<tr>
						<td class="title1">VRE</td>
						<td class="title1">:</td>
						<td class="smalltext2">
							<input type="text" name="directVreRate" value="<?php echo $directVreRate;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
						</td>
						<td>
							<input type="text" name="indirectVreRate" value="<?php echo $indirectVreRate;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
						</td>
					</tr>
					<tr>
						<td align='center' colspan="4">&nbsp;</td>
					</tr>
					<tr>
						<td class="title1">QA</td>
						<td class="title1">:</td>
						<td class="smalltext2">
							<input type="text" name="directQaRate" value="<?php echo $directQaRate;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
						</td>
						<td>
							<input type="text" name="indirectQaRate" value="<?php echo $indirectQaRate;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
						</td>
					</tr>
					<tr>
						<td align='center' colspan="4">&nbsp;</td>
					</tr>
					<tr>
						<td class="title1">PostAudit</td>
						<td class="title1">:</td>
						<td class="smalltext2">
							<input type="text" name="directAuditRate" value="<?php echo $directAuditRate;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
						</td>
						<td>
							<input type="text" name="indirectAuditRate" value="<?php echo $indirectAuditRate;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
						</td>
					</tr>
					<tr>
						<td align='center' colspan="4">&nbsp;</td>
					</tr>
				</table>
			</td>
			<td width="10%">&nbsp;</td>
			<td  valign="top">
				<table width="100%" border="0" align="center" cellpadding="0" cellspacing="2" valign="top" style="border:1px solid #e4e4e4">
					<tr>
						<td colspan="3" class="title1">
							ENTER RATES FOR Properties,PDF Reports
						</td>
					</tr>
					<tr>
						<td colspan="4">
							<hr size="1" width="100%" color="#bebebe">
						</td>
					</tr>
					<tr>
						<td width="50%">&nbsp;</td>
						<td width="1%">&nbsp;</td>
						<td width="25%" class="title1">LEVEL1</td>
						<td class="title1">LEVEL2</td>
					</tr>
					<tr>
						<td align='center' colspan="4">&nbsp;</td>
					</tr>
					<tr>
						<td class="title1">Direct</td>
						<td class="title1">:</td>
						<td class="smalltext2">
							<input type="text" name="directLevel1Rate" value="<?php echo $directLevel1Rate;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
						</td>
						<td>
							<input type="text" name="directLevel2Rate" value="<?php echo $directLevel2Rate;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
						</td>
					</tr>
					<tr>
						<td align='center' colspan="4">&nbsp;</td>
					</tr>
					<tr>
						<td class="title1">Indirect</td>
						<td class="title1">:</td>
						<td class="smalltext2">
							<input type="text" name="indirectLevel1Rate" value="<?php echo $indirectLevel1Rate;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
						</td>
						<td>
							<input type="text" name="indirectLevel2Rate" value="<?php echo $indirectLevel2Rate;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
						</td>
					</tr>
					<tr>
						<td align='center' colspan="4">&nbsp;</td>
					</tr>
					<tr>
						<td class="title1">QA</td>
						<td class="title1">:</td>
						<td class="smalltext2">
							<input type="text" name="qaLevel1Rate" value="<?php echo $qaLevel1Rate;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
						</td>
						<td>
							<input type="text" name="qaLevel2Rate" value="<?php echo $qaLevel2Rate;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
						</td>
					</tr>
					<tr>
						<td align='center' colspan="4">&nbsp;</td>
					</tr>
					<tr>
						<td class="title1">PostAudit</td>
						<td class="title1">:</td>
						<td class="smalltext2">
							<input type="text" name="auditLevel1Rate" value="<?php echo $auditLevel1Rate;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
						</td>
						<td>
							<input type="text" name="auditLevel2Rate" value="<?php echo $auditLevel2Rate;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
						</td>
					</tr>
					<tr>
						<td align='center' colspan="4">&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align='center' colspan="3">&nbsp;</td>
		</tr>
		<tr>
			<td  colspan="3">
				<input type='submit' name='submit' value='submit'>
				<input type="button" name="submit" onClick="history.back()" value="Cancel">
				<input type='hidden' value='1' name='formSubmitted'>
			</td>
		</tr>
	</table>
</form>