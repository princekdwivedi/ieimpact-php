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
 function pageRedirectIntoUrl(url)
 {
	location.href   = url;
 }
</script>
<form name="searchPdfOrderForm" action=""  method="POST">
	<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
	    <tr>
			<td width="55%">
				<table width="100%" align="center" border="0" cellpadding="3" cellspacing="3">
					<tr>
						<td width="18%" class="textstyle1">ORDER TYPE</td>
						<td width="2%" class="textstyle1">:</td>
						<td class="smalltext2">
							<?php
								foreach($a_searchOrderType as $k=>$v)
								{
									$checked	=	"";
									if($k		==	$searchOrderType)
									{
										$checked=	"checked";
									}

									echo "<input type='radio' name='searchOrderType' value='$k' $checked>$v&nbsp;";
								}
							?>
						</td>
					</tr>
					<tr>
						<td colspan="8" height="8"></td>
					</tr>
					<tr>
						<td class="textstyle1">SKETCH/RUSH</td>
						<td class="textstyle1">:</td>
						<td class="smalltext2" valign="top">
							<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td class="smalltext2" valign="top" width="38%">
										<?php
											foreach($a_searchRushSketch as $k=>$v)
											{
												$checked	=	"";
												if($k		==	$searchRushSketch)
												{
													$checked=	"checked";
												}

												echo "<input type='radio' name='searchRushSketch' value='$k' $checked>$v&nbsp;";
											}
										?>
									</td>
									<td width="25%" class="textstyle1">CUSTOMER TYPE : </td>
									<td class="smalltext2">
										<?php
											foreach($a_searchCustomerType as $k=>$v)
											{
												$checked	=	"";
												if($k		==	$searchCustomerType)
												{
													$checked=	"checked";
												}

												echo "<input type='radio' name='searchCustomerType' value='$k' $checked>$v&nbsp;";
											}
										?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
			<td width="1%">&nbsp;</td>
			<td>
				<table width="100%" align="center" border="0" cellpadding="3" cellspacing="3">
					<tr>
						<td width="22%" class="textstyle1">FILE TYPE</td>
						<td width="2%" class="textstyle1">:</td>
						<td class="smalltext2">
							<?php
								foreach($a_searchOrderFileType as $k=>$v)
								{
									$checked	=	"";
									if($k		==	$searchFileType)
									{
										$checked=	"checked";
									}

									echo "<input type='radio' name='searchFileType' value='$k' $checked>$v&nbsp;";
								}
							?>
						</td>
					</tr>
					<tr>
						<td colspan="8" height="8"></td>
					</tr>
					<tr>
						<td class="textstyle1">TIME ZONE</td>
						<td class="textstyle1">:</td>
						<td class="smalltext2">
							<?php
								foreach($a_searchOrderTime as $k=>$v)
								{
									$checked	=	"";
									if($k		==	$searchOrderTime)
									{
										$checked=	"checked";
									}

									echo "<input type='radio' name='searchOrderTime' value='$k' $checked>$v&nbsp;";
								}
							?>
						</td>
					</tr>
				</table>
			</td>
		 </tr>
		<tr>
			<td colspan="8" height="8"></td>
		</tr>
		<tr>
			<td colspan="8">
				<table width="100%" align="center" border="0" cellpadding="3" cellspacing="3">
					<tr>
						<td width="12%" class="textstyle1">ORDER ADDRESS</td>
						<td width="28%">
							<input type='text' name="searchOrder" size="51" value="<?php echo $t_searchOrder;?>" id="orderAddress"  style="border:1px solid #4d4d4d;height:25px;font-size:15px;" tabindex=1>
						</td>
						<td width="14%" class="textstyle1"><?php echo $textRed;?></td>
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
									<td width="7%" class="textstyle1">FOR/FROM</td>
									<td width="11%" class="textstyle1">
										<input type="text" name="fromDate" value="<?php echo $fromDate;?>" id="dateFor" size="8" readonly style="border:1px solid #4d4d4d;height:25px;font-size:15px;">&nbsp;&nbsp;<a href="javascript:NewCssCal('dateFor','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
									</td>
									<td width="6%" class="textstyle1">TO DATE</td>
									<td width="11%" class="textstyle1">
										<input type="text" name="endDate" value="<?php echo $endDate;?>" id="dateTo" size="8" readonly style="border:1px solid #4d4d4d;height:25px;font-size:15px;">&nbsp;&nbsp;<a href="javascript:NewCssCal('dateTo','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
									</td>
									<td width="6%" class="textstyle1" align="left">ORDER ID</td>
									<td width="10%">
										<input type="text" name="serachOrderIdNumber" value="<?php echo $serachOrderIdNumber;?>" size="10" maxlength="50" onKeyPress="return checkForNumber();" style="border:1px solid #4d4d4d;height:25px;font-size:15px;">
									</td>
									<td width="5%" class="textstyle1" align="left">STATE</td>
									<td width="18%">
										<input type='text' name="serachCustomerStates" size="20" value="<?php echo $serachCustomerStates;?>" id="searchCustomerByState" style="border:1px solid #4d4d4d;height:25px;font-size:15px;text-transform:uppercase;">
									</td>
									<td width="13%" class="textstyle1" align="left">RECORD PER PAGE</td>
									<td>
										<select name="showPageOrders">
											<?php
												$a_serachingPageRecords	=	array("50"=>"50","100"=>"100","150"=>"150","200"=>"200");
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
								</tr>
								<tr>
									<td height="8"></td>
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
							<input type="image" name="submit" src="<?php echo SITE_URL;?>/images/small-submit.png" border="0" style="cursor:pointer;">
							<?php
								if($showSubmittedResult == false)
								{
							?>
							<img src="<?php echo SITE_URL;?>/images/reset-small.png" border="0" onClick="document.searchPdfOrderForm.reset()" style="cursor:pointer;" title="Reset">
							<?php
								}
								else
								{
							?>
							<img src="<?php echo SITE_URL;?>/images/reset-small.png" border="0" onClick="pageRedirectIntoUrl('<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php')" style="cursor:pointer;" title="Reset">
							<?php
								}
							?>
							<input type='hidden' name='searchFormSubmit' value='1'>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</form>