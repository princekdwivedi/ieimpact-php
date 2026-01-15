<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
</script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" />

<script type="text/javascript">
$().ready(function() {
	$("#orderAddress").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/employees-pdf-orders.php", {width: 250,selectFirst: false});
});

$().ready(function() {
	$("#employeeCustomers").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/employees-customers.php", {width: 224,selectFirst: false});
});
function search()
{
	form1	=	document.searchForm;
	if(form1.chk1.checked == true)
	{
		if(form1.searchOrder.value == "")
		{
			alert("Please enter address !!");
			form1.searchOrder.focus();
			return false;
		}
		if(form1.searchOrder.value == "")
		{
			alert("Please select a customer !!");
			form1.searchOrder.focus();
			return false;
		}
	}
	
}
function checkedRadio()
{
	document.getElementById('chk1').checked  = true;
	document.getElementById('chk2').checked  = false;
}
function checkedRadio1()
{
	document.getElementById('chk1').checked  = false;
	document.getElementById('chk2').checked  = true;
}
</script>
<form name="searchForm" action=""  method="GET" onsubmit="return search();">
	<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
		<tr>
			<td width="20%" class="smalltext1" valign="top"><b><input type="radio" name="searchBy" value="1" <?php echo $checked;?> id="chk1">SEARCH AN ORDER BY ADDRESS :</b></td>
			<td width="22%">
				<input type='text' name="searchOrder" size="38" value="<?php echo $searchOrder;?>" id="orderAddress" onkeypress="return checkedRadio()">
			</td>
			<td width="11%" class="smalltext1" valign="top"><b><input type="radio" name="searchBy" value="2" <?php echo $checked1;?> id="chk2">BY CUSTOMER :</b></td>
			<td width="15%">
				<input type='text' name="searchCustomer" size="33" value="<?php echo $searchCustomer;?>" id="employeeCustomers" onkeypress="return checkedRadio1()">
				<!-- <select name="serachCustomerId" onchange="return checkedRadio1()">
					<option value="">All</option>
					<?php
						foreach($a_allCustomersName as $memberId=>$name)
						{
							$select		 =	"";
							if($memberId == $serachCustomerId)
							{
								$select	 =	"selected";
							}

							echo "<option value='$memberId' $select>$name</option>";
						}
					?>
				</select> -->
			</td>
			<td width="18%" align="center">
				<select name="orderType">
					<option value="">All Orders</option>
					<?php
						foreach($a_searchStatus as $key=>$value)
						{
							$select		 =	"";
							if($key == $orderType)
							{
								$select	 =	"selected";
							}

							echo "<option value='$key' $select>$value</option>";
						}
					?>
				</select>
			</td>
			<td>
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='searchFormSubmit' value='1'>
			</td>
		</tr>
	</table>
</form>