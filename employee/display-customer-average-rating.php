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

	$memberId					=	0;
	$averageOrderFor			=	15;
	$customerName				=	"";

	if(isset($_GET['memberId']))
	{
		$memberId				=	$_GET['memberId'];
		$customerName			=	$orderObj->getActiaveCustomerName($memberId);
	}
	if(isset($_GET['averageOrderFor']))
	{
		$averageOrderFor		=	$_GET['averageOrderFor'];
	}

	$calculateShowingOrderFrom	=	getPreviousGivenDate($nowDateIndia,$averageOrderFor);
	$hasAcceptedEmployee		=	$employeeObj->getSingleQueryResult("SELECT COUNT(*) as total FROM members_orders WHERE memberId=$memberId AND status <> 0 AND acceptedBy <> 0","total");
	if(empty($hasAcceptedEmployee))
	{
		$hasAcceptedEmployee	=	0;
	}
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>
<div id="showCustomerOrderProcess<?php echo $memberId;?>">
<?php
	if(!empty($memberId) && !empty($averageOrderFor) && !empty($hasAcceptedEmployee))
	{
		$countTotalProcessedOrder	=	$employeeObj->getSingleQueryResult("SELECT COUNT(*) as total  FROM members_orders WHERE memberId=$memberId AND status <> 0","total");
		if(!empty($countTotalProcessedOrder))
		{
?>
<table width='100%' align='center' cellpadding='2' cellspacing='2' border='0' style="border:3px solid #4d4d4d;">
	<tr>
		<td colspan='12' class="smalltext2">
			<b>VIEW PROCESS EMPLOYEES RATING AVERAGE OF <?php echo $customerName;?></b>
		</td>
		<td align="right">
			<img src="<?php echo SITE_URL;?>/images/close.gif" title="Close" border="0" onclick="javascript:removeEmployees(<?php echo $memberId;?>,2)">&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan='15'>
			<hr size='1' width='100%' color='#4d4d4d'>
		</td>
	</tr>
	<tr>
		<td width='5%' class='text' valign='top'>Employee ID</td>
		<td width='17%' class='text' valign='top'>Employee Name</td>
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
		$query			=	"SELECT acceptedBy,COUNT(orderId) AS totalAccepted FROM members_orders WHERE memberId=$memberId AND status <> 0 AND acceptedBy <> 0 GROUP BY acceptedBy ORDER BY totalAccepted DESC";	
		$result			=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$m			=	0;
			$m1			=	0;	
			$m2			=	0;
			$m3			=	0;
			$m4			=	0;
			$m5			=	0;
			$m6			=	0;
			$m7			=	0;
			while($row	=	mysqli_fetch_assoc($result))
			{
				$m++;
				$acceptedBy	=	$row['acceptedBy'];

				$employeeName=	$employeeObj->getEmployeeName($acceptedBy);
				$employeesAcceptedOrder	=	$orderObj->getEmployeesProcessedOrder($acceptedBy,$memberId);
				
				$totalOrderAccepted	=	$employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total FROM members_orders WHERE memberId=$memberId AND acceptedBy=$acceptedBy","total");

				if(empty($totalOrderAccepted))
				{
					$totalOrderAccepted	=	"N/A";
				}

				$totalOrderCalculateAccepted	=	$employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total FROM members_orders WHERE memberId=$memberId AND acceptedBy=$acceptedBy AND assignToEmployee >= '$calculateShowingOrderFrom'","total");

				if(empty($totalOrderCalculateAccepted))
				{
					$totalOrderCalculateAccepted	=	"N/A";
				}

				$customerTotalratedOrders	=	$employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total FROM members_orders WHERE memberId=$memberId AND rateGiven <> 0 AND acceptedBy=$acceptedBy","total");


				if(empty($customerTotalratedOrders))
				{
					$customerTotalratedOrders	=	"<font color='#ff0000'>N/A</font>";
				}

				$customerTotalRatesSum		=	$employeeObj->getSingleQueryResult("SELECT SUM(rateGiven) as total FROM members_orders WHERE memberId=$memberId AND rateGiven <> 0 AND acceptedBy=$acceptedBy","total");

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

				$lastCalculateTotalCustomerRatedOrders		=	$employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total FROM members_orders WHERE memberId=$memberId AND rateGiven <> 0 AND orderAddedOn >= '$calculateShowingOrderFrom' AND acceptedBy=$acceptedBy","total");

				if(empty($lastCalculateTotalCustomerRatedOrders))
				{
					$lastCalculateTotalCustomerRatedOrders	=	"<font color='#ff0000'>N/A</font>";
				}

				

				$totalEmployeeProcessedOrders	=	$employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total FROM members_orders_reply WHERE memberId=$memberId AND orderId IN ($employeesAcceptedOrder)","total");
				if(empty($totalEmployeeProcessedOrders))
				{
					$totalEmployeeProcessedOrders=	0;
				}
				
				$totalEmployeeProcessedTime		=	$employeeObj->getSingleQueryResult("SELECT SUM(timeSpentEmployee) as total FROM members_orders_reply WHERE memberId=$memberId AND orderId IN ($employeesAcceptedOrder)","total");
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

				$totalEmployeeProcessedCalculateOrders	  =	 $employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total FROM members_orders_reply WHERE memberId=$memberId AND orderId IN ($employeesAcceptedOrder) AND replyFileAddedOn  >= '$calculateShowingOrderFrom'","total");
				if(empty($totalEmployeeProcessedCalculateOrders))
				{
					$totalEmployeeProcessedCalculateOrders =	0;
				}
				
				$totalEmployeeProcessedCalculatedTime		=	$employeeObj->getSingleQueryResult("SELECT SUM(timeSpentEmployee) as total FROM members_orders_reply WHERE memberId=$memberId AND orderId IN ($employeesAcceptedOrder)  AND replyFileAddedOn  >= '$calculateShowingOrderFrom'","total");
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


				$employeeCustomerTotalQaOrders	      =	 $employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total FROM members_orders_reply WHERE memberId=$memberId AND hasQaDone=1 AND orderId IN ($employeesAcceptedOrder)","total");
				if(empty($employeeCustomerTotalQaOrders))
				{
					$employeeCustomerTotalQaOrders    =	 0;
				}
						
				$employeeCustomerTotalQaOrdersTime	  =		$employeeObj->getSingleQueryResult("SELECT SUM(timeSpentQa)  as total FROM members_orders_reply WHERE memberId=$memberId AND hasQaDone=1 AND orderId IN ($employeesAcceptedOrder)","total");
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



				$calculateEmployeeCustomerTotalQaOrders	  =	   $employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total FROM members_orders_reply WHERE memberId=$memberId AND replyFileAddedOn  >= '$calculateShowingOrderFrom' AND hasQaDone=1 AND orderId IN ($employeesAcceptedOrder)","total");
				if(empty($calculateEmployeeCustomerTotalQaOrders))
				{
					$calculateEmployeeCustomerTotalQaOrders=	0;
				}
				
				$calculateEmployeeCustomerTotalQaOrdersTime	=	$employeeObj->getSingleQueryResult("SELECT SUM(timeSpentQa) as total FROM members_orders_reply WHERE memberId=$memberId AND replyFileAddedOn  >= '$calculateShowingOrderFrom' AND hasQaDone=1 AND orderId IN ($employeesAcceptedOrder)","total");
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
		<td class='text' valign='top'><?php echo $acceptedBy;?></td>
		<td class='text' valign='top'><?php echo $employeeName;?></td>
		<td class='text' valign='top'><?php echo $totalOrderAccepted;?></td>
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
			<b>VIEW PROCESS EMPLOYEES RATING AVERAGE OF <?php echo $customerName;?></b>
		</td>
		<td align="right">
			<img src="<?php echo SITE_URL;?>/images/close.gif" title="Close" border="0" onclick="javascript:removeEmployees(<?php echo $memberId;?>,2)">&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan='2' align="center" height="20">
			<font class='error'><b>No employee found</b></font>
		</td>
	</tr>
</table>
<?php
	}
?>
</div>