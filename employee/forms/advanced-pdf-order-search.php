<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
</script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" />

<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>

<script type="text/javascript">
$().ready(function() {
	$("#orderAddress").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/employees-pdf-orders.php", {width: 365,selectFirst: false});
});

$().ready(function() {
	$("#searchName").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-customer.php", {width: 290,selectFirst: false});
});
$().ready(function() {
	$("#searchName1").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-wintotal-customer.php", {width: 290,selectFirst: false});
});
$().ready(function() {
	$("#searchName2").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-aci-customer.php", {width: 290,selectFirst: false});
});
$().ready(function() {
	$("#searchName3").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-clickforms-customer.php", {width: 290,selectFirst: false});
});
$().ready(function() {
	$("#searchName4").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-appraise-customer.php", {width: 290,selectFirst: false});
});

$().ready(function() {
	$("#searchCustomerByState").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-customer-by-state.php", {width: 170,selectFirst: false});
});
function checkAppraiserType(flag)
{
	//alert(flag);
	if(flag	==	1)
	{
		document.getElementById('showAllCustomer').style.display		= 'none';
		document.getElementById('showWinTotalCustomer').style.display	= 'inline';
		document.getElementById('showACICustomer').style.display		= 'none';
		document.getElementById('showClickformsCustomer').style.display = 'none';
		document.getElementById('showAppraiseCustomer').style.display	= 'none';
	}
	else if(flag	==	2)
	{
		document.getElementById('showAllCustomer').style.display		= 'none';
		document.getElementById('showWinTotalCustomer').style.display   = 'none';
		document.getElementById('showACICustomer').style.display		= 'inline';
		document.getElementById('showClickformsCustomer').style.display = 'none';
		document.getElementById('showAppraiseCustomer').style.display	= 'none';
	}
	else if(flag	==	3)
	{
		document.getElementById('showAllCustomer').style.display		= 'none';
		document.getElementById('showWinTotalCustomer').style.display	= 'none';
		document.getElementById('showACICustomer').style.display		= 'none';
		document.getElementById('showClickformsCustomer').style.display = 'inline';
		document.getElementById('showAppraiseCustomer').style.display	= 'none';
	}
	else if(flag	==	4)
	{
		document.getElementById('showAllCustomer').style.display		= 'none';
		document.getElementById('showWinTotalCustomer').style.display	= 'none';
		document.getElementById('showACICustomer').style.display		= 'none';
		document.getElementById('showClickformsCustomer').style.display = 'none';
		document.getElementById('showAppraiseCustomer').style.display	= 'inline';
	}
	else
	{
		document.getElementById('showAllCustomer').style.display		= 'inline';
		document.getElementById('showWinTotalCustomer').style.display	= 'none';
		document.getElementById('showACICustomer').style.display		= 'none';
		document.getElementById('showClickformsCustomer').style.display = 'none';
		document.getElementById('showAppraiseCustomer').style.display	= 'none';
	}
}
function getOrderNumberValues(flag)
{
	if(flag		   !=   "")
	{
		var first	=	"";
		var second	=	"";
		var third	=	"";

		var myString = flag;

		var mySplitResult = myString.split("-");
		
		if(mySplitResult[0] != "")
		{
			first		=	mySplitResult[0];
		}
		if(mySplitResult[1] != "")
		{
			second		=	mySplitResult[1];
		}
		if(mySplitResult[2] != "")
		{
			third		=	mySplitResult[2];
		}

		if(first	==	undefined || first	==	"0")
		{
			first	=	"";
		}
		if(second	==	undefined || second	==	"0")
		{
			second	=	"";
		}
		if(third	==	undefined || third	==	"0")
		{
			third	=	"";
		}

		document.getElementById('firstOrderNumberId').value		= first;
		document.getElementById('secondOrderNumberId').value	= second;
		document.getElementById('thirdOrderNumberId').value		= third;
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
 function search()
 {
	return;
	form1	=	document.searchForm;

	if(form1.firstSearchOrderCustomer.value != "" && form1.secondSearchOrderNumber.value == "" && form1.thirdSearchOrderTime.value == "")
	{
		alert("Enter complete order number !");
		form1.secondSearchOrderNumber.focus();
		return false;
	}
	if(form1.firstSearchOrderCustomer.value == "" && form1.secondSearchOrderNumber.value != "" && form1.thirdSearchOrderTime.value == "")
	{
		alert("Enter complete order number !");
		form1.firstSearchOrderCustomer.focus();
		return false;
	}
	if(form1.firstSearchOrderCustomer.value == "" && form1.secondSearchOrderNumber.value == "" && form1.thirdSearchOrderTime.value != "")
	{
		alert("Enter complete order number !");
		form1.firstSearchOrderCustomer.focus();
		return false;
	}
	if(form1.firstSearchOrderCustomer.value != "" && form1.secondSearchOrderNumber.value != "" && form1.thirdSearchOrderTime.value == "")
	{
		alert("Enter complete order number !");
		form1.thirdSearchOrderTime.focus();
		return false;
	}
	if(form1.firstSearchOrderCustomer.value == "" && form1.secondSearchOrderNumber.value != "" && form1.thirdSearchOrderTime.value != "")
	{
		alert("Enter complete order number !");
		form1.firstSearchOrderCustomer.focus();
		return false;
	}
	if(form1.firstSearchOrderCustomer.value != "" && form1.secondSearchOrderNumber.value == "" && form1.thirdSearchOrderTime.value != "")
	{
		alert("Enter complete order number !");
		form1.secondSearchOrderNumber.focus();
		return false;
	}
 }
</script>

<form name="searchForm" action=""  method="GET" onsubmit="return search();">
	<!-- <fieldset style="border:1px solid #333333">
		<legend><font style="font-size:16px;font-weight:bold;font-family:verdana;color:#4d4d4d">Advanced PDF order search form</font></legend>
		<label> -->
			<table width="100%" align="center" border="0" cellpadding="3" cellspacing="3">
				<tr>
					<td colspan="12">
						<font style="font-size:16px;font-weight:bold;font-family:verdana;color:#4d4d4d">Advanced PDF order search form</font>
					</td>
				</tr>
				<tr>
					<td width="19%" class="smalltext4">SEARCH AN ORDER BY ADDRESS</td>
					<td width="28%">
						<input type='text' name="searchOrder" size="51" value="<?php echo $searchOrder;?>" id="orderAddress"  style="border:1px solid #4d4d4d;height:25px;font-size:15px;" tabindex=1>
					</td>
					<td width="4%" class="smalltext4">OR BY</td>
				<!-- 	<td width="8%" class="smalltext4" valign="top">
						<select name="searchCustomerBy" onclick="return checkAppraiserType(this.value);">
							<option value="0">All</option>
							<?php
								foreach($a_appraisalSoftware as $key=>$value)
								{
									$select		=	"";
									if($searchCustomerBy	==	$key)
									{
										$select	=	"selected";
									}
									echo "<option value='$key' $select>$value</option>";
								}
							?>
						</select>
						<select name="serachFileType" onclick="return checkAppraiserType(this.value);" tabindex=2>
							<option value="0">Select</option>
							<?php
								foreach($a_appraisalFileTypes as $k=>$v)
								{
									$select	=	"";
									if($k	==	$serachFileType)
									{
										$select	=	"selected";
									}
									echo "<option value='$k' $select>$v</option>";
								}
							?>
						</select>
					</td> -->
					<td width="12%" class="smalltext4"><?php echo $textRed;?></td>
					<td>
						<input type='text' name="searchText" size="40" value="<?php echo $searchName;?>" id="searchName" style="border:1px solid #4d4d4d;height:25px;font-size:15px;" tabindex=3>
					</td>
				</tr>
				<tr>
					<td colspan="8" height="8"></td>
				</tr>
				<tr>
					<td colspan="8">
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="8%" class="smalltext4">FOR/FROM</td>
								<td width="11%" class="smalltext4">
									<input type="text" name="fromDate" value="<?php echo $fromDate;?>" id="dateFor" size="13" readonly style="border:1px solid #4d4d4d;height:15px;font-size:10px;">&nbsp;&nbsp;<a href="javascript:NewCssCal('dateFor','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
								</td>
								<td width="8%" class="smalltext4">TO DATE</td>
								<td width="11%" class="smalltext4">
									<input type="text" name="endDate" value="<?php echo $endDate;?>" id="dateTo" size="13" readonly style="border:1px solid #4d4d4d;height:15px;font-size:10px;">&nbsp;&nbsp;<a href="javascript:NewCssCal('dateTo','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
								</td>
								<td width="12%" class="smalltext4" align="left">ORDER ID/NUMBER</td>
								<td>
									<input type="text" name="serachOrderIdNumber" value="<?php echo $serachOrderIdNumber?>" size="50" maxlength="50" onKeyPress="return checkForNumber();" style="border:1px solid #333333">
								</td>
							</tr>
							<tr>
								<td height="8"></td>
							</tr>
							<tr>
								<tr>
								<td colspan="2" class="smalltext4">SERACH CUSTOMER BY STATES</td>
								<td class="smalltext4" colspan="2">
									<input type='text' name="serachCustomerStates" size="20" value="<?php echo $serachCustomerStates;?>" id="searchCustomerByState" style="border:1px solid #4d4d4d;height:25px;font-size:15px;text-transform:uppercase;">
								</td>
								<td class="smalltext4" colspan="3" align="left">RECORD PER PAGE
								&nbsp;&nbsp;
									<select name="showPageOrders">
										<?php
											$a_serachingPageRecords	=	array("25"=>"25","50"=>"50","75"=>"75","100"=>"100");
											foreach($a_serachingPageRecords as $k=>$v)
											{
												$select		 =	"";
												if($k		 == $showPageOrders)
												{
													$select	 =	"selected";
												}

												echo "<option value='$k' $select>$k</option>";
											}
										?>
									</select>
								</td>
								<!-- <td colspan="10" class="smalltext1" align="right">
									[Note : Copy paste any order number within any of the given three boxes above]
									&nbsp;
								</td> -->
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="10">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td>
						<input type="submit" name="submit" value="Search" border="0">
						<input type='reset' name='reset' value='Reset'>
						<input type='hidden' name='orderType' value='<?php echo $orderSelectType;?>'>
						<input type='hidden' name='serachFileType' value='<?php echo $orderSearchFileType;?>'>
						<input type='hidden' name='searchFormSubmit' value='1'>
					</td>
				</tr>
			</table>
		<!-- </label>
	</fieldset> -->
</form>