<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/validate.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>

<script type="text/javascript">
function calculateTotalMoney()
{
	fixedSal			=	document.getElementById('fsal').value;
	orderMoney			=	document.getElementById('orderMoney').value;
	if(fixedSal != "")
	{
		fixedSal = Math.abs (fixedSal)
	}
	if(orderMoney != "")
	{
		orderMoney = Math.abs (orderMoney)

		total		= fixedSal+orderMoney;
		total		= Math.round(total);

		document.getElementById('totalMoney').innerHTML = total;
		document.getElementById('salaryReceived').value = total;
		document.getElementById('slarayToGive').value = total;
	}
	
}
function calculateSalaryToGive()
{
	totalMoney			=	document.getElementById('salaryReceived').value;
	tdsMoney			=	document.getElementById('tdsMoney').value;
	pfMoney				=	document.getElementById('pfId').value;
	if(totalMoney != "")
	{
		totalMoney = Math.abs (totalMoney)

		if(tdsMoney != "")
		{
			tdsMoney	= Math.abs (tdsMoney)

			total		= totalMoney-tdsMoney;
			total		= Math.round(total);
		}
		if(pfMoney != "")
		{
			pfMoney		= Math.abs (pfMoney)

			total		= total-pfMoney;
			total		= Math.round(total);
		}
		document.getElementById('slarayToGive').value = total;
	}
	
	
}
function showModeDetails(flag)
{
	if(flag  == 2)
	{
		document.getElementById('displayModCheque').style.display = 'inline';
		document.getElementById('displayModOnline').style.display  = 'none';
	}
	else if(flag  == 3) 
	{
		document.getElementById('displayModCheque').style.display = 'none';
		document.getElementById('displayModOnline').style.display  = 'inline';
	}
	else
	{
		document.getElementById('displayModCheque').style.display = 'none';
		document.getElementById('displayModOnline').style.display  = 'none';
	}
}
function checkForNumberPoints()
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
 function validPay()
 {
	form1	=	document.paySalary;
	if(form1.givenOn.value == "")
	{
		alert("Please select payment done date !!");
		form1.givenOn.focus();
		return false;
	}
 }
</script>
<form name="paySalary" action=""  method="POST" onsubmit="return validPay();">
	<table cellpadding="2" cellspacing="2" width='100%'align="center" border='0'>
		<tr>
			<td width="25%" class="textstyle">
				<b>Employee Type</b>
			</td>
			<td width="2%" class="textstyle1">
				:
			</td>
			<td class="error">
				<?php echo $employeeTypeText;?>
			</td>
		</tr>
		<tr>
			<td class="textstyle">
				<b>Department</b>
			</td>
			<td class="textstyle1">
				:
			</td>
			<td class="textstyle1">
				<b>PDF</b>
			</td>
		</tr>
		<tr>
			<td class="textstyle">
				<b>Total Order Processed</b>
			</td>
			<td class="textstyle1">
				:
			</td>
			<td class="textstyle1">
				<b><?php echo $totalPdfOrder;?></b>
			</td>
		</tr>
		<tr>
			<td class="textstyle">
				<b>Total QA Order</b>
			</td>
			<td class="textstyle1">
				:
			</td>
			<td class="textstyle1">
				<b><?php echo $totalQaOrder;?></b>
			</td>
		</tr>
		<tr>
			<td class="textstyle">
				<b>Fixed Salary</b>
			</td>
			<td class="textstyle1">
				:
			</td>
			<td class="textstyle1">
				<input type="text" name="fixedSalary" value="<?php echo $fixedSalary;?>" size="10" maxlength="10" onKeyPress="return checkForNumberPoints()" id="fsal" onBlur="calculateTotalMoney();">
			</td>
		</tr>
		<tr>
			<td class="textstyle">
				<b>Money From Orders</b>
			</td>
			<td class="textstyle1">
				:
			</td>
			<td class="textstyle1">
				<input type="text" name="totalMoney" value="<?php echo $totalMoney;?>" size="10" maxlength="10" onKeyPress="return checkForNumberPoints()" id="orderMoney" onBlur="calculateTotalMoney();">
			</td>
		</tr>
		<tr>
			<td class="textstyle">
				<b>TOTAL FIXED+ORDER SALARY</b>
			</td>
			<td class="textstyle1">
				:
			</td>
			<td class="textstyle1">
				<div id="totalMoney" class="textstyle1"><b><?php echo $totalFixedOrdersMoney;?></b></div>
				<input type="hidden" name="totalSalaryReceived" value="<?php echo $totalFixedOrdersMoney;?>" readonly  id="salaryReceived" onBlur="calculateSalaryToGive();">
			</td>
		</tr>
		<?php
			if($employeeType == 2)
			{
		?>
		<tr>
			<td class="textstyle" colspan="3">
				<b>TDS deduction 1% of from total</b>
			</td>
		</tr>
		<?php
			}
		?>
		<tr>
			<td class="textstyle">
				<b>TDS Deductions Amoumt</b>
			</td>
			<td class="textstyle1">
				:
			</td>
			<td class="textstyle1">
				<input type="text" name="tdsDeduction" value="<?php echo $tdsDeduction;?>" size="10" maxlength="10" onKeyPress="return checkForNumberPoints()" id="tdsMoney" onBlur="calculateSalaryToGive();">
			</td>
		</tr>
		<tr>
			<td class="textstyle">
				<b>PF Deductions Amoumt</b>
			</td>
			<td class="textstyle1">
				:
			</td>
			<td class="textstyle1">
				<input type="text" name="pfMoney" value="<?php echo $pfMoney;?>" size="10" maxlength="10" onKeyPress="return checkForNumberPoints()" id="pfId" onBlur="calculateSalaryToGive();">
			</td>
		</tr>
		<tr>
			<td class="textstyle">
				<b>Gross Salary</b>
			</td>
			<td class="textstyle1">
				:
			</td>
			<td class="textstyle1">
				<input type="text" name="salaryGiven" value="<?php echo $salaryGiven;?>" size="10" maxlength="10" onKeyPress="return checkForNumberPoints()" id="slarayToGive">
			</td>
		</tr>
		<tr>
			<td class="textstyle">
				<b>Mode Of Payment</b>
			</td>
			<td class="textstyle1">
				:
			</td>
			<td class="textstyle1">
				<select name="givenThrough" onchange="showModeDetails(this.value);">
				<?php
					foreach($a_salaryPaidTrough as $key=>$value)
					{
						$select		=	"";
						if($key		==	$givenThrough)
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
			<td colspan="3">
				<div  id="displayModCheque" style="display:<?php echo $display;?>">
					<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
						<tr>
							<td width="25%" class="textstyle">
								<b>Cheque No</b>
							</td>
							<td width="2%" class="textstyle1">
								:
							</td>
							<td>
								<input type="text" name="chequeNo" value="<?php echo $chequeNo;?>" size="30" maxlength="100">
							</td>
						</tr>
						<tr>
							<td class="textstyle">
								<b>Cheque Bank</b>
							</td>
							<td class="textstyle1">
								:
							</td>
							<td>
								<input type="text" name="checkBank" value="<?php echo $checkBank;?>" size="50" maxlength="100">
							</td>
						</tr>
						<tr>
							<td class="textstyle">
								<b>Cheque Date</b>
							</td>
							<td class="textstyle1">
								:
							</td>
							<td>
								<input type="text" name="checkDate" value="<?php echo $checkDate;?>" id="atOn" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('atOn','yyyymmdd')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
							</td>
						</tr>
					</table>
				</div>
				<div  id="displayModOnline" style="display:<?php echo $display1;?>">
					<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" valign="top">
						<tr>
							<td width="25%" class="textstyle">
								<b>Online Transactions ID</b>
							</td>
							<td width="2%" class="textstyle1">
								:
							</td>
							<td>
								<input type="text" name="transactionId" value="<?php echo $transactionId;?>" size="50" maxlength="100">
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td class="textstyle" valign="top">
				<b>Remarks</b>
			</td>
			<td class="textstyle1"  valign="top">
				:
			</td>
			<td class="textstyle1">
				<textarea name="remarks" rows="3" cols="35"><?php echo $remarks;?></textarea>
			</td>
		</tr>
		<tr>
			<td class="textstyle">
				<b>Payment Made On</b>
			</td>
			<td class="textstyle1">
				:
			</td>
			<td class="textstyle1">
				<input type="text" name="givenOn" value="<?php echo $givenOn;?>" id="gvOn" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('gvOn','yyyymmdd')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
			</td>
		</tr>
		<tr>
			<td colspan="3"></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td class="textstyle1">
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
	</table>
</form>