<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>
<script type="text/javascript">
function validTransactions()
{
	form1	=	document.addCashCheque;
	if(form1.amount.value ==	"")
	{
		alert("Please Enter Amount !!");
		form1.amount.focus();
		return false;
	}
	if(form1.paidReceivedFrom.value ==	"")
	{
		alert("Please Enter Person/Firm/Organization Name !!");
		form1.paidReceivedFrom.focus();
		return false;
	}
	if(form1.transactionDetails.value ==	"")
	{
		alert("Please Enter Transaction Details !!");
		form1.transactionDetails.focus();
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
 function changeText(flag)
{
	
	var text	=	"";
	if(flag == 1)
	{
		text	=	"Voucher No";
	}
	else if(flag == 2)
	{
		text	=	"Cheque No";
	}
	document.getElementById('showNoText').innerHTML = text;
}
</script>

<form  name='addCashCheque' method='POST' action="" onsubmit="return validTransactions();">
<table width="98%" border="0" align="center" cellpadding="6" cellspacing="2" valign="top">
	<tr>
		<td colspan="3" height="15" class="error"><?php echo $errorMsg;?></td>
	</tr>
	<tr>
		<td class="title1" width="22%"><b>Transaction Type</b></td>
		<td class="title1" width="2%"><b>:</b></td>
		<td class="smalltext2">
			<input type='radio' name='transactionsType' value='1' <?php echo $checked2;?> onclick="return changeText(1);"><b>CASH</b>&nbsp;&nbsp;
			<input type='radio' name='transactionsType' value='2' <?php echo $checked3;?>  onclick="return changeText(2);"><b>CHEQUE</b>
		</td>
	</tr>
	<tr>
		<td class="title1"><b>Amount</b></td>
		<td class="title1"><b>:</b></td>
		<td class="smalltext2">
			<input type="text" name="amount" value="<?php echo $amount;?>" size="20" maxlength="20" onKeyPress="return checkForNumber();">
		</td>
	</tr>
	<tr>
		<td class="title1"><b>This Amount Is</b></td>
		<td class="title1"><b>:</b></td>
		<td class="smalltext2">
			<input type="radio" name="type" value="1" <?php echo $checked;?>><b>DEBITED</b>&nbsp;&nbsp;
			<input type="radio" name="type" value="2" <?php echo $checked1;?>><b>CREDITED</b>
		</td>
	</tr>
	<tr>
		<td class="title1"><b>Transaction Date</b></td>
		<td class="title1"><b>:</b></td>
		<td class="smalltext2">
			<input type="text" name="transactionDate" value="<?php echo $transactionDate;?>" id="for" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('for','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
		</td>
	</tr>
	<tr>
		<td class="title1" valign="top"><b>Person/Firm/Organization Name</b></td>
		<td class="title1" valign="top"><b>:</b></td>
		<td class="smalltext2" valign="top">
			<input type="text" name="paidReceivedFrom" value="<?php echo $paidReceivedFrom;?>" size="46" maxlength="150">
		</td>
	</tr>
	<tr>
		<td valign="top"><div id="showNoText" class="title1"><b><?php echo $displayText;?></b></div></td>
		<td class="title1" valign="top"><b>:</b></td>
		<td class="smalltext2" valign="top">
			<input type="text" name="voucherNo" value="<?php echo $voucherNo;?>" size="46" maxlength="100">
		</td>
	</tr>
	<tr>
		<td class="title1" valign="top"><b>Transaction Details</b></td>
		<td class="title1" valign="top"><b>:</b></td>
		<td class="smalltext2" valign="top">
			<textarea name="transactionDetails" cols="35" rows="5"><?php echo $transactionDetails;?></textarea>
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