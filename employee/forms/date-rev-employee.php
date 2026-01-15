<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>
<script type="text/javascript">
	function validCheck()
	{
		form1	=	document.dateEmployee;
		if(form1.forDate.value == "" && form1.toDate.value != "")
		{
			alert("Please select from date !!");
			form1.forDate.focus();
			return false;
		}
	}
</script>
<form  name='dateEmployee' method="POST" action="" onsubmit="return validCheck();">
	<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
		<tr>
			<td width="20%" class="smalltext2" valign="top">
				<b>
					<?php echo $formHeaderText;?> 
				</b>
			</td>
			<td width="6%" class="smalltext2" valign="top">
				<b>
					For Date
				</b>
			</td>
			<td width="10%" valign="top">
				<input type="text" name="forDate" value="<?php echo $forDate;?>" class="textbox" id="dateFor" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('dateFor','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
			</td>
			<td width="6%" class="smalltext2" valign="top">
				<b>
					To Date
				</b>
			</td>
			<td width="10%" valign="top">
				<input type="text" name="toDate" value="<?php echo $toDate;?>" class="textbox" id="dateTo" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('dateTo','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
			</td>
			<td width="10%" class="smalltext2" valign="top">
				<b>
					Employee
				</b>
			</td>
			<td width="15%" valign="top">
				<select name="employeeId[]" multiple style="height:100px;">
				<option value="0">All</option>
				<?php
					if($result	=	$employeeObj->getAllRevEmployees())
					{
						while($row	=	mysql_fetch_assoc($result))
						{
							$t_employeeId	=	$row['employeeId'];
							$firstName		=	$row['firstName'];
							$lastName		=	$row['lastName'];

							$employeeName	=	$firstName." ".$lastName;
							$employeeName	=	ucwords($employeeName);

							$select			=	"";
							if(in_array($t_employeeId, $a_employeeId))
							{
								$select		=	"selected";
							}

							echo "<option value='$t_employeeId' $select>$employeeName</option>";
						}
						
					}
				?>
				</select>
			</td>
			<td valign="top">
				<input type='submit' name='submit' value='Submit'>
				<input type='hidden' value='1' name='formSubmitted'>
			</td>
		</tr>
		<tr>
			<td colspan="5">&nbsp;</td>
			<td colspan="2" class="smalltext7" align="right">
				[Use Ctrl+Select to select multiple employees]
			</td>
		</tr>
	</table>
</form>