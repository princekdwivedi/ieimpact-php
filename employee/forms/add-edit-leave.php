<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>



<script type="text/javascript">
function showDays(flag)
{
	var daysId = document.getElementById('daysId').value;
	if(flag == 1)
	{
		document.getElementById('showLeaveDays').style.display = 'inline';
		if(daysId > 1)
		{
			document.getElementById('showMoreDate').style.display = 'inline';
			document.getElementById('showSingleDate').style.display = 'none'
		}
	}
	else
	{
		document.getElementById('showLeaveDays').style.display = 'none';
		document.getElementById('showMoreDate').style.display = 'none';
		document.getElementById('showSingleDate').style.display = 'inline';
	}
}
function showFromToDate(flag)
{
	if(flag == 1)
	{
		document.getElementById('showSingleDate').style.display = 'inline';
		document.getElementById('showMoreDate').style.display = 'none';
	}
	else
	{
		document.getElementById('showSingleDate').style.display = 'none';
		document.getElementById('showMoreDate').style.display = 'inline';
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
 function isValidLeave()
 {
	form1	=	document.addEditLeave;
	if(form1.leaveType[0].checked == true)
	{
		if(form1.leaveDays.value == "1")
		{
			if(form1.forDate.value == "")
			{
				alert("Please Select Leave Date.");
				form1.forDate.focus();
				return false;
			}
		}
		else
		{
			if(form1.fromDate.value == "")
			{
				alert("Please Select Leave From Date.");
				form1.fromDate.focus();
				return false;
			}
			if(form1.toDate.value == "")
			{
				alert("Please Select Leave To Date.");
				form1.toDate.focus();
				return false;
			}
		}
	}
	if(form1.leaveType[1].checked == true)
	{
		if(form1.forDate.value == "")
		{
			alert("Please Select Leave Date.");
			form1.forDate.focus();
			return false;
		}
	}
	if(form1.leaveReason.value == "")
	{
		alert("Please Enter Leave Reason.");
		form1.leaveReason.focus();
		return false;
	}

	var confirmation = window.confirm("Please check the Date(s)/Year you selected for leave?");
				
	if(confirmation == true)
	{
		return true;
	}
	else{
		return false;
	}
 }
</script>

<form name="addEditLeave" action="" method="POST" onsubmit="return isValidLeave()">
<table cellpadding="4" cellspacing="2" width="98%" border="0" align="center">
	<tr>
		<td class="smalltext23" width="15%" valign="top">
			Leave Type
		</td>
		<td class="smalltext23" width="2%" valign="top">
			:
		</td>
		<td class="smalltext23" width="20%" valign="top">
			<input type="radio" name="leaveType" value="1" <?php echo $checked;?> onclick="return showDays(1)">Full Leave&nbsp;&nbsp;
			<input type="radio" name="leaveType" value="2" <?php echo $checked1;?> onclick="return showDays(2)">Half Leave&nbsp;&nbsp;
		</td>
		<td valign="top">
			<div id="showLeaveDays" style="display:<?php echo $display;?>">
				<table width="100%" align="center" border="0" cellpadding="2" cellspacing="2">
					<tr>
						<td width="4%" class="smalltext2" valign="top">For</td>
						<td class="smalltext2" width="2%">
							:
						</td>
						<td width="10%" valign="top">
							<select name="leaveDays" onchange="showFromToDate(this.value);" id="daysId">
							<?php
								for($i=1;$i<=15;$i++)
								{
									$select	=	"";
									if($i == $leaveDays)
									{
										$select	=	"selected";
									}

									echo "<option value='$i' $select>$i</option>";
								}
							?>
							</select>
						</td>
						<td class="smalltext23" valign="top">
							Day(s)
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="4">
			<div id="showSingleDate" style="display:<?php echo $display1;?>">
				<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td class="smalltext23" valign="top" width="15%">
							For Date
						</td>
						<td class="smalltext23" valign="top"  width="2%">
							:
						</td>
						<td valign="top" width="15%">
							<input type="text" name="forDate" value="<?php echo $forDate;?>" id="date1" size="8" readonly style="border:1px solid #4d4d4d;height:15px;font-size:15px;">&nbsp;&nbsp;<a href="javascript:NewCssCal('date1','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
						<td class="smalltext1" colspan="6">
							&nbsp;[<u>Note<font color="red">*</font></u>:  You can apply leave from <font color="red"><?php echo showDate($maxLeavecanApplyFrom);?></font> to <font color="red"><?php echo showDate($maxLeavecanApply);?></font>]
						</td>
					</tr>
					</tr>
				</table>
			</div>
			<div id="showMoreDate" style="display:<?php echo $display2;?>">
				<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td class="smalltext23" valign="top" width="15%">
							From Date
						</td>
						<td class="smalltext23" valign="top"  width="2%">
							:
						</td>
						<td valign="top" width="15%">
							<input type="text" name="fromDate" value="<?php echo $fromDate;?>" id="date2" size="8" readonly style="border:1px solid #4d4d4d;height:15px;font-size:15px;">&nbsp;&nbsp;<a href="javascript:NewCssCal('date2','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
						</td>
						<td class="smalltext23" valign="top" width="8%">
							To Date
						</td>
						<td class="smalltext23" valign="top"  width="1%">
							:
						</td>
						<td valign="top" width="15%">
							<input type="text" name="toDate" value="<?php echo $toDate;?>" id="date3" size="8" readonly style="border:1px solid #4d4d4d;height:15px;font-size:15px;">&nbsp;&nbsp;<a href="javascript:NewCssCal('date3','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
						<td class="smalltext1" colspan="6">
							&nbsp;[<u>Note<font color="red">*</font></u>:  You can apply leave from <font color="red"><?php echo showDate($maxLeavecanApplyFrom);?></font> to <font color="red"><?php echo showDate($maxLeavecanApply);?></font>]
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<tr>
		<td class="smalltext23" valign="top">
			Leave Reason
		</td>
		<td class="smalltext2" valign="top">
			:
		</td>
		<td class="smalltext23" valign="top" colspan="2">
			<textarea name="leaveReason" rows="6" cols="35" style="border:1px solid #333333;"><?php echo stripslashes(htmlentities($leaveReason,ENT_QUOTES))?></textarea>
		</td>
	</tr>
	<tr>
		<td class="smalltext23" valign="top">
			Emergency Contact No.
		</td>
		<td class="smalltext23" valign="top">
			:
		</td>
		<td valign="top" colspan="2">
			<input type="text" name="emergencyNo" value="<?php echo $emergencyNo;?>" size="46" onKeyPress="return checkForNumber();" maxlength="25" style="border:1px solid #333333;">
		</td>
	</tr>
	<tr>
		<td colspan="4">
			&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan="2">
			&nbsp;
		</td>
		<td colspan="2">
			<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
			<input type='hidden' name='formSubmitted' value='1'>
		</td>
	</tr>
</table>
</form>