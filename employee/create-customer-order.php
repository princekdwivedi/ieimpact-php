<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES .  "/includes/new-top.php");
	include(SITE_ROOT_EMPLOYEES .  "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES .  "/classes/employee.php");
	include(SITE_ROOT			.  "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/check-pdf-login.php");
	include(SITE_ROOT_MEMBERS	.  "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	.  "/classes/orders.php");
	include(SITE_ROOT			.  "/classes/validate-fields.php");
	include(SITE_ROOT			.  "/classes/common.php");
	include(SITE_ROOT			.  "/classes/email-templates.php");
    $emailObj					=  new emails();
	$employeeObj				=  new employee();
	$memberObj					=  new members();
	$orderObj					=  new orders();
	$validator					=  new validate();
	$commonClass				=  new common();
	$showForm					=  false;
	$searchCustomer				=  "";
	if(isset($_GET['searchCustomer'])){
		$searchCustomer			=	trim($_GET['searchCustomer']);
		if(!empty($searchCustomer)){
			$showForm		    =  true;
		}
	}

	if(isset($_GET['customerId']) && isset($_GET['operation'])){
		$customerId				=	$_GET['customerId'];
		$operation				=	$_GET['operation'];
		if(!empty($customerId)){
			$query				=	"SELECT completeName FROM members WHERE memberId=$customerId AND isActiveCustomer=1";
			$result				=	dbQuery($query);
			if(mysqli_num_rows($result)){
				if($operation == 1 || $operation == 0){
					if(isset($_GET['changeEmailOrder']) && $_GET['changeEmailOrder'] == 1){
						dbQuery("UPDATE members SET enableEmailOrder=$operation WHERE memberId=$customerId AND isActiveCustomer=1");

						$performedTask	=	"Changed allowing customer email order of customer - ".$customerId." to - ".$operation;
				
						$orderObj->trackEmployeeWork(0,$s_employeeId,$performedTask);

					}
					if(isset($_GET['changeEmailMessage']) && $_GET['changeEmailMessage'] == 1){
						dbQuery("UPDATE members SET enableEmailMessage=$operation WHERE memberId=$customerId AND isActiveCustomer=1");

						$performedTask	=	"Changed allowing customer email message of customer - ".$customerId." to - ".$operation;
				
						$orderObj->trackEmployeeWork(0,$s_employeeId,$performedTask);
					}
					ob_clean();
					header("Location: ".SITE_URL_EMPLOYEES ."/create-customer-order.php?searchCustomer=".$searchCustomer);
					exit();

				}
			}
		}
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES ."/create-customer-order.php");
		exit();
	}
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" />
<script type="text/javascript">
	$().ready(function() {
		$("#searchName").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-customer.php", {width: 300,selectFirst: false});
	});

	function validSearch(){
		form1	= document.searchCustomerOrder;
		if(form1.searchCustomer.value == "" || form1.searchCustomer.value == " " || form1.searchCustomer.value == "0"){
			alert("Please search customer.");
			form1.searchCustomer.focus();
			return false;
		}
	}

	function allowDisallowEmailOrder(memberId,searchText,operation)
	{
		if(operation == 1)
		{
			var confirmation = window.confirm("Are You Sure To Allow This Customer To Place Order Through Email?");
		}
		else
		{
			var confirmation = window.confirm("Are You Sure To Not Allow This Customer To Place Order Through Email?");
		}
		if(confirmation == true)
		{
			window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/create-customer-order.php?searchCustomer='+searchText+"&customerId="+memberId+"&changeEmailOrder=1&operation="+operation;
		}
	}

	function allowDisallowEmailMessage(memberId,searchText,operation)
	{
		if(operation == 1)
		{
			var confirmation = window.confirm("Are You Sure To Allow This Customer To Send Messages Through Email?");
		}
		else
		{
			var confirmation = window.confirm("Are You Sure To Not Allow This Customer To Send Messages Through Email?");
		}
		if(confirmation == true)
		{
			window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/create-customer-order.php?searchCustomer='+searchText+"&customerId="+memberId+"&changeEmailMessage=1&operation="+operation;
		}
	}
</script>
<form name='searchCustomerOrder' method='GET' action="" onsubmit="return validSearch();">
	<table width="95%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
		<tr>
			<td width="65%" class="textstyle3" valign="top">SEARCH CUSTOMER TO PLACED NEW ORDER OR ENABLE DISABLED EMAIL ORDERS & MESSAGES : </td>
			<td valign="top" width="20%">
				<input type='text' name="searchCustomer" size="35" value="<?php echo $searchCustomer;?>" id="searchName" style="border:1px solid #4d4d4d;height:25px;font-size:15px;">
			</td>
			<td valign="top">
				<input type="image" name="submit" src="<?php echo SITE_URL;?>/images/small-submit.png" border="0" style="cursor:pointer;">
				<input type='hidden' name='searchFormSubmit' value='1'>
			</td>
		</tr>
	</table>
</form>
<?php
	if($showForm	== true){
		$completeName	=	makeDBSafe($searchCustomer);
		$query		=	"SELECT memberId,firstName,lastName,email,totalOrdersPlaced,addedOn,enableEmailMessage,enableEmailOrder FROM members WHERE completeName LIKE '%$completeName%' AND isActiveCustomer=1";
		$result		=	dbQuery($query);
		if(mysqli_num_rows($result)){
			
?>
<table width="99%" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="13" height="5"></td>
	</tr>
	<tr bgcolor="#373737" height="20">
		<td class="smalltext8" width="3%">&nbsp;</td>
		<td class="smalltext8" width="20%">&nbsp;<b>Customer Name</b></td>
		<td class="smalltext8" width="15%">&nbsp;<b>Customer Email</b></td>
		<td class="smalltext8" width="15%">&nbsp;<b>Customer Since</b></td>
		<td class="smalltext8" width="15%">&nbsp;<b>Email Order</b></td>
		<td class="smalltext8" width="15%">&nbsp;<b>Email Messages</b></td>
		<td class="smalltext8">&nbsp;<b>Action</b></td>
	</tr>
	<?php
		$count	=	0;
		while($row			=	mysqli_fetch_assoc($result)){
			$count++;
			$memberId		=	$row['memberId'];
			$firstName		=	stripslashes($row['firstName']);
			$lastName		=	stripslashes($row['lastName']);
			$completeName	=	$firstName." ".substr($lastName, 0, 1);
			$email		    =	$row['email'];
			$addedOn		=	$row['addedOn'];
			$totalCustomerOrders		=	$row['totalOrdersPlaced'];
			$enableEmailMessage		=	$row['enableEmailMessage'];
			$enableEmailOrder		=	$row['enableEmailOrder'];

			$emailOrderText			=	"Allowing";
			$emailOrderLinkText		=	"Not Allow";
			$makeEmailOrder			=	0;
			if($enableEmailOrder	==	 0){
				$emailOrderText		=	"Not Allowing";
				$emailOrderLinkText	=	"Allow Now";
				$makeEmailOrder		=	1;
			}

			$emailMessageText		    =	"Allowing";
			$emailMessageLinkText	    =	"Not Allow";
			$makeEmailMessage		    =	0;
			if($enableEmailMessage	    ==	 0){
				$emailMessageText		=	"Not Allowing";
				$emailMessageLinkText	=	"Allow Now";
				$makeEmailMessage		=	1;
			}


			$customerOrderText		=	"";
			$customerLinkStyle		=	"link_style16";
			
			if(empty($totalCustomerOrders))
			{
				$totalCustomerOrders=	0;
			}
			if($totalCustomerOrders <= 3)
			{
				$customerOrderText	=	"(New Cus.)";
				$customerLinkStyle	=	"link_style17";
			}
			elseif($totalCustomerOrders > 3 && $totalCustomerOrders <= 7)
			{
				$customerOrderText	=	"(Trial Cus.)";
				$customerLinkStyle	=	"link_style18";
			}
			elseif($totalCustomerOrders >= 100 && $totalCustomerOrders < 350)
			{
				$customerOrderText	=	"(Big Cus.)";
				$customerLinkStyle	=	"link_style20";
			}
			elseif($totalCustomerOrders >= 350 && $totalCustomerOrders < 700)
			{
				$customerOrderText	=	"(VIP Cus.)";
				$customerLinkStyle	=	"link_style21";
			}
			elseif($totalCustomerOrders >= 700)
			{
				$customerOrderText	=	"(VVIP Cus.)";
				$customerLinkStyle	=	"link_style22";
			}

			$bgColor	    =	"class='rwcolor1'";
			if($count%2==0)
			{
				$bgColor    =   "class='rwcolor2'";
			}
	?>
	<tr height="23" <?php echo $bgColor;?>>
		<td class="smalltext17" valign="top">&nbsp;&nbsp;<?php  echo $count;?></td>
		<td valign="top">
			<?php 
				echo "<a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=$memberId' class='$customerLinkStyle' style='cursor:pointer;'>$completeName</a>";
			?>
		</td>
		<td class="smalltext17" valign="top"><?php  echo $email;?></td>
		<td class="smalltext17" valign="top"><?php  echo showDate($addedOn);?></td>
		<td class="smalltext17" valign="top"><?php  echo $emailOrderText;?>&nbsp;(<a onclick="allowDisallowEmailOrder(<?php echo $memberId;?>,'<?php echo $searchCustomer?>',<?php echo $makeEmailOrder;?>);" class='link_style12' style="cursor:pointer;"><?php echo $emailOrderLinkText?></a>)</td>
		<td class="smalltext17" valign="top"><?php  echo $emailMessageText;?>&nbsp;(<a onclick="allowDisallowEmailMessage(<?php echo $memberId;?>,'<?php echo $searchCustomer?>',<?php echo $makeEmailMessage;?>);" class='link_style12' style="cursor:pointer;"><?php echo $emailMessageLinkText?></a>)</td>
		<td class="smalltext17" valign="top">
			<?php
				if($enableEmailOrder == 1){
			?>
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/employee-place-customer-order.php?memberId=<?php echo $memberId;?>" class='link_style12'>Place Order</a>
			<?php
				}
				else{
					echo "<font class='error'>Allow Email Order</font>";
				}
			?>
		</td>
	</tr>
	<?php
		}
	?>

</table>
<?php
		}
		else{
			echo "<table width='90%' align='center' border='0'><tr><td  height='400' class='error2' style='text-align:center;'><b>No active customer foundd with searching.</b></td></tr></table>";
		}
	}
	else{
		echo "<table width='90%' align='center' border='0'><tr><td  height='400' class='error2' style='text-align:center;'><b>Please search a customer.</b></td></tr></table>";
	}

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>
