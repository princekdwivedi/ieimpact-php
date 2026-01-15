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

	$employeeId					=	0;
	$averageOrderFor			=	15;
	$employeeName				=	"";

	if(isset($_GET['employeeId']))
	{
		$employeeId				=	$_GET['employeeId'];
		$employeeName			=	$employeeObj->getEmployeeName($employeeId);
	}
	if(isset($_GET['averageOrderFor']))
	{
		$averageOrderFor		=	$_GET['averageOrderFor'];
	}

	$calculateShowingOrderFrom	=	getPreviousGivenDate($nowDateIndia,$averageOrderFor);
	$hasAcceptedCustomer		=	@mysql_result(dbQuery("SELECT COUNT(*) FROM members_orders WHERE status <> 0 AND acceptedBy = $employeeId"),0);
	if(empty($hasAcceptedCustomer))
	{
		$hasAcceptedCustomer	=	0;
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
<div id="showEmployeeOrderProcess<?php echo $employeeId;?>">
<?php
	if(!empty($employeeId) && !empty($averageOrderFor) && !empty($hasAcceptedCustomer))
	{
		$countTotalProcessedOrder	=	@mysql_result(dbQuery("SELECT COUNT(*) FROM members_orders WHERE acceptedBy = $employeeId AND status <> 0"),0);
		if(!empty($countTotalProcessedOrder))
		{
?>
<table width='100%' align='center' cellpadding='2' cellspacing='2' border='0' style="border:3px solid #4d4d4d;">
	<tr>
		<td colspan='12' class="smalltext2">
			<b>VIEW PROCESS CUSTOMERS RATING AVERAGE OF <?php echo $employeeName;?></b>
		</td>
		<td align="right">
			<img src="<?php echo SITE_URL;?>/images/close.gif" title="Close" border="0" onclick="javascript:removeEmployees(<?php echo $employeeId;?>,2)">&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan='15'>
			<hr size='1' width='100%' color='#4d4d4d'>
		</td>
	</tr>
	<tr>
		<td width='5%' class='text' valign='top'>Customer ID</td>
		<td width='17%' class='text' valign='top'>Customer Name</td>
		<td width='7%' class='text' valign='top'>Total Orders</td>
		<td width='9%' class='text' valign='top'>Orders In Last <?php echo $averageOrderFor;?> Days</td>
		<td width='6%' class='text' valign='top'>Total Rated Orders</td>
		<td width='5%' class='text' valign='top'>Ave. rate</td>
		<td width='7%' class='text' valign='top'>Ratings In Last <?php echo $averageOrderFor;?> Days</td>
		<td width='7%' class='text' valign='top'>Ave. Process Time</td>
		<td class='text' valign='top' width='7%'>Ave. Process Time Last <?php echo $averageOrderFor;?> Days</td>
		<td width='7%' class='text' valign='top'>Ave. QA Time</td>
		<td class='text' valign='top' width='6%'>Ave. QA Time Last <?php echo $averageOrderFor;?> Days</td>
		<td width='6%' class='text' valign='top'>Ave. Process+QA Time</td>
		<td class='text' valign='top'>Ave. Process+QA Time Last <?php echo $averageOrderFor;?> Days</td>
	</tr>
	<tr>
		<td colspan='15'>
			<hr size='1' width='100%' color='#4d4d4d'>
		</td>
	</tr>
	<?php
		$query			=	"SELECT memberId,COUNT(orderId) AS totalMembersOrders FROM members_orders WHERE acceptedBy=$employeeId AND status <> 0 AND memberId <> 0 GROUP BY memberId ORDER BY totalMembersOrders DESC";	
		$result			=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			$m			=	0;
			$m1			=	0;	
			$m2			=	0;
			$m3			=	0;
			$m4			=	0;
			$m5			=	0;
			$m6			=	0;
			$m7			=	0;
			while($row	=	mysql_fetch_assoc($result))
			{
				$m++;
				$memberId			=	$row['memberId'];
				$totalMembersOrders	=	$row['totalMembersOrders'];

				$customerName		=	$orderObj->getActiaveCustomerName($memberId);

				$employeesAcceptedOrder	=	$orderObj->getEmployeesProcessedOrder($employeeId,$memberId);
				
				if(empty($totalMembersOrders))
				{
					$totalMembersOrders	=	"N/A";
				}

				$totalOrderCalculateAccepted	=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders WHERE memberId=$memberId AND acceptedBy=$employeeId AND assignToEmployee >= '$calculateShowingOrderFrom'"),0);

				if(empty($totalOrderCalculateAccepted))
				{
					$totalOrderCalculateAccepted	=	"N/A";
				}

				$customerTotalratedOrders	=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders WHERE memberId=$memberId AND rateGiven <> 0 AND acceptedBy=$employeeId"),0);

				if(empty($customerTotalratedOrders))
				{
					$customerTotalratedOrders	=	"<font color='#ff0000'>N/A</font>";
				}

				$customerTotalRatesSum		=	@mysql_result(dbQuery("SELECT SUM(rateGiven) FROM members_orders WHERE memberId=$memberId AND rateGiven <> 0 AND acceptedBy=$employeeId"),0);

				if(empty($customerTotalRatesSum))
				{
					$customerTotalRatesSum	=	0;
				}

				if(!empty($customerTotalratedOrders) && !empty($customerTotalRatesSum))
				{
					$averageRate			=	$customerTotalRatesSum/$customerTotalratedOrders;
					$m1++;

					$averageRate			=	round($averageRate,2);
				}
				else
				{
					$averageRate			=	"<font color='#ff0000'>N/A</font>";
				}

				$lastCalculateTotalCustomerRatedOrders		=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders WHERE memberId=$memberId AND rateGiven <> 0 AND orderAddedOn >= '$calculateShowingOrderFrom' AND acceptedBy=$employeeId"),0);

				if(empty($lastCalculateTotalCustomerRatedOrders))
				{
					$lastCalculateTotalCustomerRatedOrders	=	"<font color='#ff0000'>N/A</font>";
				}

				

				$totalEmployeeProcessedOrders	=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders_reply WHERE memberId=$memberId AND orderId IN ($employeesAcceptedOrder)"),0);
				if(empty($totalEmployeeProcessedOrders))
				{
					$totalEmployeeProcessedOrders=	0;
				}
				
				$totalEmployeeProcessedTime		=	@mysql_result(dbQuery("SELECT SUM(timeSpentEmployee) FROM members_orders_reply WHERE memberId=$memberId AND orderId IN ($employeesAcceptedOrder)"),0);
				if(empty($totalEmployeeProcessedTime))
				{
					$totalEmployeeProcessedTime=	0;
				}

				if(!empty($totalEmployeeProcessedOrders)  && !empty($totalEmployeeProcessedTime))
				{
					$averageProcessTime		=	$totalEmployeeProcessedTime/$totalEmployeeProcessedOrders;

					$averageProcessTime		=	getHours($averageProcessTime);
				}
				else
				{
					$averageProcessTime		=	"<font color='#ff0000'>N/A</font>";
				}

				$totalEmployeeProcessedCalculateOrders	  =	 @mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders_reply WHERE memberId=$memberId AND orderId IN ($employeesAcceptedOrder) AND replyFileAddedOn  >= '$calculateShowingOrderFrom'"),0);
				if(empty($totalEmployeeProcessedCalculateOrders))
				{
					$totalEmployeeProcessedCalculateOrders =	0;
				}
				
				$totalEmployeeProcessedCalculatedTime		=	@mysql_result(dbQuery("SELECT SUM(timeSpentEmployee) FROM members_orders_reply WHERE memberId=$memberId AND orderId IN ($employeesAcceptedOrder)  AND replyFileAddedOn  >= '$calculateShowingOrderFrom'"),0);
				if(empty($totalEmployeeProcessedCalculatedTime))
				{
					$totalEmployeeProcessedCalculatedTime=	0;
				}

				if(!empty($totalEmployeeProcessedCalculateOrders)  && !empty($totalEmployeeProcessedCalculatedTime))
				{
					$averageProcessCalculatedTime		=	$totalEmployeeProcessedCalculatedTime/$totalEmployeeProcessedCalculateOrders;

					$averageProcessCalculatedTime		=	getHours($averageProcessCalculatedTime);
				}
				else
				{
					$averageProcessCalculatedTime		=	"<font color='#ff0000'>N/A</font>";
				}


				$employeeCustomerTotalQaOrders	      =	 @mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders_reply WHERE memberId=$memberId AND hasQaDone=1 AND orderId IN ($employeesAcceptedOrder)"),0);
				if(empty($employeeCustomerTotalQaOrders))
				{
					$employeeCustomerTotalQaOrders    =	 0;
				}
						
				$employeeCustomerTotalQaOrdersTime	  =		@mysql_result(dbQuery("SELECT SUM(timeSpentQa) FROM members_orders_reply WHERE memberId=$memberId AND hasQaDone=1 AND orderId IN ($employeesAcceptedOrder)"),0);
				if(empty($employeeCustomerTotalQaOrdersTime))
				{
					$employeeCustomerTotalQaOrdersTime=		0;
				}

				if(!empty($employeeCustomerTotalQaOrders)  && !empty($employeeCustomerTotalQaOrdersTime))
				{
					$averageQaTime		=	$employeeCustomerTotalQaOrdersTime/$employeeCustomerTotalQaOrders;

					$averageQaTime		=	getHours($averageQaTime);
				}
				else
				{
					$averageQaTime		=	"<font color='#ff0000'>N/A</font>";
				}



				$calculateEmployeeCustomerTotalQaOrders	  =	   @mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders_reply WHERE memberId=$memberId AND replyFileAddedOn  >= '$calculateShowingOrderFrom' AND hasQaDone=1 AND orderId IN ($employeesAcceptedOrder)"),0);
				if(empty($calculateEmployeeCustomerTotalQaOrders))
				{
					$calculateEmployeeCustomerTotalQaOrders=	0;
				}
				
				$calculateEmployeeCustomerTotalQaOrdersTime	=	@mysql_result(dbQuery("SELECT SUM(timeSpentQa) FROM members_orders_reply WHERE memberId=$memberId AND replyFileAddedOn  >= '$calculateShowingOrderFrom' AND hasQaDone=1 AND orderId IN ($employeesAcceptedOrder)"),0);
				if(empty($calculateEmployeeCustomerTotalQaOrdersTime))
				{
					$calculateEmployeeCustomerTotalQaOrdersTime=	0;
				}

				if(!empty($calculateEmployeeCustomerTotalQaOrders)  && !empty($calculateEmployeeCustomerTotalQaOrdersTime))
				{
					$calculateAverageQaTime    =	$calculateEmployeeCustomerTotalQaOrdersTime/$calculateEmployeeCustomerTotalQaOrders;

					$calculateAverageQaTime        =	getHours($calculateAverageQaTime);
				}
				else
				{
					$calculateAverageQaTime	        =	"<font color='#ff0000'>N/A</font>";
				}

				$totalAverageProcesQaTime			=	$averageProcessTime+$averageQaTime;
				$totalAverageProcesQaTimeForLast	=	$averageProcessCalculatedTime+$calculateAverageQaTime;

				if(!empty($totalAverageProcesQaTime))
				{
					$totalAverageProcesQaTime	=	getHours($totalAverageProcesQaTime);
				}
				else
				{
					$totalAverageProcesQaTime	=	"<font color='#ff0000'>N/A</font>";
				}

				if(!empty($totalAverageProcesQaTimeForLast))
				{
					$totalAverageProcesQaTimeForLast =	getHours($totalAverageProcesQaTimeForLast);
				}
				else
				{
					$totalAverageProcesQaTimeForLast	=	"<font color='#ff0000'>N/A</font>";
				}
		?>
		<tr>
		<td class='text' valign='top'><?php echo $memberId;?></td>
		<td class='text' valign='top'><?php echo $customerName;?></td>
		<td class='text' valign='top'><?php echo $totalMembersOrders;?></td>
		<td class='text' valign='top'><?php echo $totalOrderCalculateAccepted;?></td>
		<td class='text' valign='top'><?php echo $customerTotalratedOrders;?></td>
		<td class='text' valign='top'><?php echo $averageRate;?></td>
		<td class='text' valign='top'><?php echo $lastCalculateTotalCustomerRatedOrders;?></td>
		<td class='text' valign='top'><?php echo $averageProcessTime;?></td>
		<td class='text' valign='top'><?php echo $averageProcessCalculatedTime;?></td>
		<td class='text' valign='top'><?php echo $averageQaTime;?></td>
		<td class='text' valign='top'><?php echo $calculateAverageQaTime;?></td>
		<td class='text' valign='top'><?php echo $totalAverageProcesQaTime;?></td>
		<td class='text' valign='top'><?php echo $totalAverageProcesQaTimeForLast;?></td>
	</tr>
	<tr>
		<td colspan='15'>
			<hr size='1' width='100%' color='#4d4d4d'>
		</td>
	</tr>
		<?php
			}
		}
	?>
</table>
<?php
		}
		else
		{
			echo "<br><center><font class='error'><b>No order processed yet</b></font></center>";
		}
	}
	else
	{
?>
<table width='100%' align='center' cellpadding='2' cellspacing='2' border='0' style="border:3px solid #4d4d4d;">
	<tr>
		<td width="85%" class="smalltext2">
			<b>VIEW PROCESS CUSTOMERS RATING AVERAGE OF <?php echo $employeeName;?></b>
		</td>
		<td align="right">
			<img src="<?php echo SITE_URL;?>/images/close.gif" title="Close" border="0" onclick="javascript:removeEmployees(<?php echo $employeeId;?>,2)">&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan='2' align="center" height="20">
			<font class='error'><b>No customer found</b></font>
		</td>
	</tr>
</table>
<?php
	}
?>
</div>
</center>
</body>
</html>
