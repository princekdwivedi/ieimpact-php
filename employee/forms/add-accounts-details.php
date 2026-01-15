<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>
<script type="text/javascript">
function validAccounts()
{
	form1	=	document.addAccounts;
	if(form1.amount.value ==	"")
	{
		alert("Please Enter Amount !!");
		form1.amount.focus();
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
	else if(k == 46)
	{
		return true;
	}
	else
	{
		return false;
	}
 }
</script>

<form  name='addAccounts' method='POST' action="" onsubmit="return validAccounts();">
<table width="98%" border="0" align="center" cellpadding="4" cellspacing="2" valign="top">
	<tr>
		<td colspan="3" height="15" class="error"><?php echo $errorMsg;?></td>
	</tr>
	<tr>
		<td width="20%" class="smalltext2"><b>For The Month Of</b></td>
		<td width="2%" class="smalltext2"><b>:</b></td>
		<td class="smalltext2">
			<b>Month</b>&nbsp;&nbsp;
			<select name="month">
				<?php
					foreach($a_month as $key=>$value)
					{
						$select	  =	"";
						if($month == $key)
						{
							$select	  =	"selected";
						}

						echo "<option value='$key' $select>$value</option>";
					}
				?>
			</select>&nbsp;&nbsp;
			<b>Year</b>&nbsp;&nbsp;
			<select name="year">
				<?php
					$sYear	=	"2010";
					$eYear	=	date("Y")+1;
					for($i=$sYear;$i<=$eYear;$i++)
					{
						$select			=	"";
						if($year  == $i)
						{
							$select		=	"selected";
						}
						echo "<option value='$i' $select>$i</option>";
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="smalltext2"><b>Amount</b></td>
		<td class="smalltext2"><b>:</b></td>
		<td class="smalltext2">
			<input type="text" name="amount" value="<?php echo $amount;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
		</td>
	</tr>
	<tr>
		<td class="smalltext2"><b>This Amount Is</b></td>
		<td class="smalltext2"><b>:</b></td>
		<td class="smalltext2">
			<input type="radio" name="type" value="1" <?php echo $checked;?>>DEBITED&nbsp;&nbsp;
			<input type="radio" name="type" value="2" <?php echo $checked1;?>>CREDITED
		</td>
	</tr>
	<tr>
		<td class="smalltext2"><b>Transaction Date</b></td>
		<td class="smalltext2"><b>:</b></td>
		<td class="smalltext2">
			<input type="text" name="accountsFor" value="<?php echo $accountsFor;?>" id="for" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('for','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
		</td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top"><b>Voucher Number</b></td>
		<td class="smalltext2" valign="top"><b>:</b></td>
		<td class="smalltext2" valign="top">
			<input type="text" name="voucherNo" value="<?php echo $voucherNo;?>" size="46" maxlength="100">
		</td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top"><b>Remarks On This Transaction</b></td>
		<td class="smalltext2" valign="top"><b>:</b></td>
		<td class="smalltext2" valign="top">
			<textarea name="remarks" cols="35" rows="5"><?php echo nl2br($remarks);?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
		<td>
			<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
			<input type='hidden' name='formSubmitted' value='1'>
		</td>
	</tr>
	<tr>
		<td colspan="3" height="50"></td>
	</tr>
</table>
</form>