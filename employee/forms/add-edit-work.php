<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>

<!-- <!-- <script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>
<script type="text/javascript"> -->

<script type="text/javascript">
function checkValidWork()
{
	form1	=	document.addWork;
	if(form1.platform.value	==	"")
	{
		alert("Please Select A Platform !!");
		form1.platform.focus();
		return false;
	}
	if(form1.customerId.value	==	"")
	{
		alert("Please Select A Client !!");
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
</script>
<form  name='addWork' method='POST' action="" onsubmit="return checkValidWork();">
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='heading1' valign="top"><?php echo $text;?> Work</td>
	</tr>
	<?php 
		if(!empty($errorMsg))
		{
			echo "<tr><td colspan='3'><font size='3' color='red'>$errorMsg</td></tr>";
		}
	
	?>
	<tr>
		<td width="20%" class="title">Platform</td>
		<td width="2%" class="title">:</td>
		<td>
			<?php
				$url2	=	SITE_URL_EMPLOYEES. "/get-platform-customer.php?parentId=";
			?>
			<select name="platform"  onchange="commonFunc('<?php echo $url2?>','displayCustomer',this.value);showProperties(this.value);">
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
		<td class="title">Client</td>
		<td class="title">:</td>
		<td>
			<div id="displayCustomer">
			<select name="customerId">
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
					<td class="title" width="20%">Transcription (SINGLE)</td>
					<td class="title" width="2%">:</td>
					<td class="smalltext2">
						<!-- <input type="checkbox" name="transcriptionDirect" value="1" <?php echo $transcriptionDirectCheck;?>> --><b>DSP</b>&nbsp;&nbsp;
						<input type="text" name="transcriptionLinesEntered" value="<?php echo $transcriptionLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
						&nbsp;&nbsp;<!-- <input type="checkbox" name="transcriptionIndirect" value="1" <?php echo $transcriptionIndirectCheck;?>> --><b>N-DSP</b>&nbsp;&nbsp;
						<input type="text" name="indirectTranscriptionLinesEntered" value="<?php echo $indirectTranscriptionLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">&nbsp;&nbsp;
						<b>USER ID</b>&nbsp;&nbsp;<input type="text" name="transcriptionUserId" value="<?php echo $transcriptionUserId;?>" size="10" maxlength="30">

					</td>
				</tr>
				<tr>
					<td align='center' colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td class="title">VRE</td>
					<td class="title">:</td>
					<td class="smalltext2">
						<!-- <input type="checkbox" name="vreDirect" value="1" <?php echo $vreDirectCheck;?>> --><b>DSP</b>&nbsp;&nbsp;
						<input type="text" name="vreLinesEntered" value="<?php echo $vreLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">&nbsp;&nbsp;
						<!-- <input type="checkbox" name="vreIndirect" value="1" <?php echo $vreIndirectCheck;?>> --><b>N-DSP</b>&nbsp;&nbsp;
						<input type="text" name="indirectVreLinesEntered" value="<?php echo $indirectVreLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">&nbsp;&nbsp;
						<b>USER ID</b>&nbsp;&nbsp;<input type="text" name="vreUserId" value="<?php echo $vreUserId;?>" size="10" maxlength="30">
					</td>
				</tr>
				<tr>
					<td align='center' colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td class="title">QA</td>
					<td class="title">:</td>
					<td class="smalltext2">
						<!-- <input type="checkbox" name="qaDirect" value="1" <?php echo $qaDirectCheck;?>> --><b>DSP</b>&nbsp;&nbsp;
						<input type="text" name="qaLinesEntered" value="<?php echo $qaLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">&nbsp;&nbsp;
						<!-- <input type="checkbox" name="qaIndirect" value="1" <?php echo $qaIndirectCheck;?>> --><b>N-DSP</b>&nbsp;&nbsp;
						<input type="text" name="indirectQaLinesEntered" value="<?php echo $indirectQaLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">&nbsp;&nbsp;
						<b>USER ID</b>&nbsp;&nbsp;<input type="text" name="qaUserId" value="<?php echo $qaUserId;?>" size="10" maxlength="30">
					</td>
				</tr>
				<tr>
					<td align='center' colspan="3">&nbsp;</td>
				</tr>
			</table>
		 </div>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<div  id="showProperties" style="display:<?php echo $display1;?>">
			<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
			<tr>
				<td class="title" width="20%">First Level</td>
				<td class="title" width="2%">:</td>
				<td class="smalltext2">
					<input type="text" name="propertiesLines" value="<?php echo $propertiesLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
				</td>
			</tr>
			<tr>
				<td align='center' colspan="3">&nbsp;</td>
			</tr>
			<tr>
					<td class="title">QA</td>
					<td class="title">:</td>
					<td class="smalltext2">
						<input type="text" name="qaLines" value="<?php echo $qaLinesEntered;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
					</td>
				</tr>
			</table>
			</div>
		</td>
	</tr>
	<tr>
		<td align='center' colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td class="title">Work Done On</td>
		<td class="title">:</td>
		<td class="title2">
			<?php
				echo "<b>".showDate($workedOn)."</b>";
			?>
			<input type="hidden" name="workedOn" value="<?php echo $workedOn;?>">
		</td>
	</tr>
	<tr>
		<td align='center' colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td class="title" valign="top">Comments</td>
		<td class="title" valign="top">:</td>
		<td>
			<textarea name="comments" rows="5" cols="40"><?php echo $comments;?></textarea>
		</td>
	</tr>
	<tr>
		<td align='center' colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td align='center' colspan="2">&nbsp;</td>
		<td>
			<input type='submit' name='submit' value='submit'>
			<input type="button" name="submit" onClick="history.back()" value="Cancel">
			<input type='hidden' value='1' name='formSubmitted'>
		</td>
	</tr>

</table>
</form>