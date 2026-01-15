<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=  new employee();
	$orderObj					=  new orders();
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$customerId					=	0;

	if(isset($_GET['customerId']))
	{
		$customerId		=	$_GET['customerId'];
	}

	$query				=	"SELECT * FROM members WHERE memberId=$customerId AND isActiveCustomer=1";
	$result				=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		$showForm		=	true;

		$row			=	mysqli_fetch_assoc($result);
		$customerName	=	stripslashes($row['completeName']);
		$email			=	$row['email'];
	}
	
?>
<html>
<head>
	
</head>

<body onLoad="init()">
<div id="loading" style="position:absolute; width:100%; text-align:center; top:300px;"><img src="<?php echo SITE_URL;?>/images/loading.gif" border=0></div>
<script>
var ld=(document.all);

var ns4=document.layers;
var ns6=document.getElementById&&!document.all;
var ie4=document.all;

if (ns4)
	ld=document.loading;
else if (ns6)
	ld=document.getElementById("loading").style;
else if (ie4)
	ld=document.all.loading.style;

function init()
{
if(ns4){ld.visibility="hidden";}
else if (ns6||ie4) ld.display="none";
}
</script>

<center>
	<div id="showCustomerOrderList">
	<table width="100%" align="center" border="0" cellspacing="2" cellspacing="2">
		<tr>
			<td colspan="5" class="textstyle1"><b>Last 5 Completed Order details for <?php echo $customerName;?></b></td>
			<td align="right">
				<img src="<?php echo SITE_URL;?>/images/close.gif" title="Close" border="0" onclick="removeCustomerOrderList(2)" style="cursor:pointer;">&nbsp;
			</td>
		</tr>
		<tr>
			<td width="15%" class="smalltext2">Order Address</td>
			<td width="10%" class="smalltext2">Order Type</td>
			<td width="7%" class="smalltext2">Date</td>
			<td width="13%" class="smalltext2">Accepted By</td>
			<td width="13%" class="smalltext2">Qa By</td>
			<td width="7%" class="smalltext2">Qa Rate</td>
			<td width="14%" class="smalltext2">Message</td>
			<td width="7%" class="smalltext2">Cus. Rate</td>
			<td class="smalltext2">Message</td>
		</tr>
		<tr>
			<td colspan="10">
				<hr size="1" width="100%" color="#bebebe">
			</td>
		</tr>
		<?php
			$orderAcceptedHrs		=	"";
			$orderQaHrs				=	"";
			$query	=	"SELECT * FROM members_orders WHERE memberId=$customerId AND status IN (2,5,6) ORDER BY orderId DESC LIMIT 5";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row		=	mysqli_fetch_assoc($result))
				{
					$t_orderId		=	$row['orderId'];
					$t_orderAddress	=	stripslashes($row['orderAddress']);
					$t_orderType	=	$row['orderType'];
					$t_acceptedBy	=	$row['acceptedBy'];
					$t_rateGiven	=	$row['rateGiven'];
					$t_orderAddedOn	=	showDate($row['orderAddedOn']);
					$t_memberRateMsg=	stripslashes($row['memberRateMsg']);
					$qaDoneByText   =	stripslashes($row['qaDoneByName']);
					$acceptedByName =	stripslashes($row['acceeptedByName']);
					
					if($result2			=  $orderObj->getOrderQaRate($t_orderId))
					{
						$row2			=   mysqli_fetch_assoc($result2);
						$t_rateByQa		=	$row2['rateByQa'];
						$t_qaRateMessage=	stripslashes($row2['qaRateMessage']);
					}
					else
					{
						$t_rateByQa		=	"";
						$t_qaRateMessage=	"";
					}
					
					$orderText			=	$a_customerOrder[$t_orderType];

					$orderAcceptedHrs	=	$employeeObj->getSingleQueryResult("SELECT timeSpentEmployee FROM members_orders_reply WHERE orderId=$t_orderId AND memberId=$customerId","timeSpentEmployee");

					if(!empty($orderAcceptedHrs))
					{
						$orderAcceptedHrs=	" (".getHours($orderAcceptedHrs)." Hrs)";
					}

					$orderQaHrs			=	$employeeObj->getSingleQueryResult("SELECT timeSpentQa FROM members_orders_reply WHERE orderId=$t_orderId AND memberId=$customerId AND hasQaDone=1","timeSpentQa");

					if(!empty($orderQaHrs))
					{
						$orderQaHrs		=	" (".getHours($orderQaHrs)." Hrs)";
					}
?>
<tr>
	<td class="smalltext1" valign="top"><?php echo $t_orderAddress;?></td>
	<td class="smalltext1" valign="top"><?php echo $orderText;?></td>
	<td class="smalltext1" valign="top"><?php echo $t_orderAddedOn;?></td>
	<td class="smalltext1" valign="top"><?php echo $acceptedByName.$orderAcceptedHrs;?></td>
	<td class="smalltext1" valign="top"><?php echo $qaDoneByText.$orderQaHrs;?></td>
	<td class="smalltext1" valign="top">
		<?php
				if(!empty($t_rateByQa))
				{
					for($m=1;$m<=$t_rateByQa;$m++)
					{
				?>
					<img src="<?php echo SITE_URL;?>/images/star.gif"  width="12" height="12">
				<?php
					}
				}
			?>
	</td>
	<td class="smalltext1" valign="top">
		<?php echo nl2br($t_qaRateMessage);?>
	</td>
	<td class="smalltext1" valign="top">
		<?php
				if(!empty($t_rateGiven))
				{
					for($m=1;$m<=$t_rateGiven;$m++)
					{
				?>
					<img src="<?php echo SITE_URL;?>/images/star.gif"  width="12" height="12">
				<?php
					}
				}
			?>
	</td>
	<td class="smalltext1" valign="top">
		<?php echo $t_memberRateMsg;?>
	</td>
</tr>
<tr>
	<td colspan="10">
		<hr size="1" width="100%" color="#bebebe">
	</td>
</tr>
<?php

		}
	}
	else
	{
		echo "<tr><td colspan='10' align='center' class='error'><b>No Orders Availabale !</b></td></tr>";
	}
?>
</table>

	</div>
</center>
</body>
</html>