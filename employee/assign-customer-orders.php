<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				= new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/common.php");
	include(SITE_ROOT			. "/includes/send-mail.php");
	$orderObj					= new orders();
	$commonObj					= new common();
	$a_allmanagerEmails			= $commonObj->getMangersEmails();
	
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
?>
	<script type="text/javascript">
		function redirectViewPageTo(flag)
		{
			window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/assign-customer-orders.php?"+flag;
		}
		function assignForCustomer(memberId)
		{
			path = "<?php echo SITE_URL_EMPLOYEES?>/assign-customer-all-orders.php?memberId="+memberId;
			prop = "toolbar=no,scrollbars=yes,width=1200,height=650,top=100,left=70";
			window.open(path,'',prop);
		}
	</script>
<table cellpadding="2" cellspacing="2" width='98%'align="center" border='0'>
	<tr>
		<td class="textstyle1">
			<b>ASSIGN CUSTOMERS NEW ORDERS</b> 
		</td>
	</tr>
	<tr height="50">
		<td>
			 <a href="<?php echo SITE_URL_EMPLOYEES;?>/assign-orders-automatically.php" class="link_style23">ASSIGN ALL AUTOMATICALLY</a>
		</td>
	</tr>
</table>
<br>
<?php
	$totalCustomersNewOrders	=	0;
	$query		=	"SELECT COUNT(*) AS totalNewOrders,members_orders.memberId,completeName,firstName,lastName,appraisalSoftwareType,totalOrdersPlaced FROM members_orders INNER JOIN members ON members_orders.memberId=members.memberId WHERE orderId > ".MAX_SEARCH_EMPLOYEE_ORDER_ID." AND status=0 AND isNotVerfidedEmailOrder=0 AND isDeleted=0 AND isVirtualDeleted=0 AND isTestAccount=0 GROUP BY members_orders.memberId ORDER BY totalNewOrders DESC";
	$result		=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
?>
<table cellpadding="2" cellspacing="2" width='98%'align="center" border='0'>
	<tr>
		<td class="textstyle1" width="20%">
			<b>VIEW ORDERS BY</b>
		</td>
		<td>&nbsp;</td>
	</tr>
</table>
<br>
<table cellpadding="2" cellspacing="2" width='98%'align="center" border='0'>
	<tr>
		<td width="10%" class="smalltext2">Sr No</td>
		<td width="30%" class="smalltext2">Customer Name</td>
		<td width="12%" class="smalltext2">Total New Orders</td>
		<td class="smalltext2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="10">
			<hr size="1" width="100%" color="#4d4d4d">
		</td>
	</tr>
	<?php
		$count	=	0;
		while($row					=	mysqli_fetch_assoc($result))
		{
			$count++;
			$memberId				=	$row['memberId'];
			$totalNewOrders			=	$row['totalNewOrders'];
			$firstName		        =	stripslashes($row['firstName']);
			$lastName		        =	stripslashes($row['lastName']);
			$customerName	        =	$firstName." ".substr($lastName, 0, 1);
			$appraisalSoftwareType	=	stripslashes($row['appraisalSoftwareType']);
			$cutomersAllTotalOrders	=	$row['totalOrdersPlaced'];

			$appraisalTypeText		=	"";
			if(!empty($appraisalSoftwareType))
			{
				$appraisalTypeText	=	"&nbsp;(<font color='#ff0000'>".$a_appraisalSoftware[$appraisalSoftwareType]."</font>)";
			}

			$customerOrderText		=	"";
			$customerLinkStyle		=	"link_style16";
			if($cutomersAllTotalOrders <= 3)
			{
				$customerOrderText	=	"(New Customer)";
				$customerLinkStyle	=	"link_style17";
			}
			elseif($cutomersAllTotalOrders > 3 && $cutomersAllTotalOrders <= 7)
			{
				$customerOrderText	=	"(Trial Customer)";
				$customerLinkStyle	=	"link_style18";
			}

			$totalCustomersNewOrders=	$totalCustomersNewOrders+$totalNewOrders;
	?>
	<tr>
		<td class="textstyle1"><?php echo $count;?>)</td>
		<td>
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&searchOrderType=3&showPageOrders=50&serachCustomerById=<?php echo $memberId;?>" title="View All Orders" class="<?php echo $customerLinkStyle;?>"><?php echo $customerName.$customerOrderText.$appraisalTypeText;?></a>
		</td>
		<td class="textstyle1"><b>
			<a onclick="assignForCustomer(<?php echo $memberId;?>)" title="Assign Orders" style="cursor:pointer;" class="link_style14"><?php echo $totalNewOrders;?></a></b>
		</td>
		<td class="smalltext2">
			<a onclick="assignForCustomer(<?php echo $memberId;?>)" title="Assign Orders" style="cursor:pointer;" class="link_style14">Assign All Orders</a>
		</td>
	</tr>
	<tr>
		<td colspan="10">
			<hr size="1" width="100%" color="#4d4d4d">
		</td>
	</tr>
	<?php

		}
	?>
	<tr>
		<td class="textstyle1" colspan="2">Total New Un-Assigned Orders</td>
		<td class="textstyle1"><b><?php echo $totalCustomersNewOrders;?></b></td>
		<td class="textstyle1">&nbsp;</td>
		<td class="textstyle1">&nbsp;</td>
		<td class="smalltext2">
			&nbsp;
		</td>
	</tr>
</table>
<?php
	}
	else
	{
		echo "<br><br><center><font class='error'><b>No Record Found !!</b></font></center>";
	}

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>