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

	$averageOrderFor			=	"";
	$customerName				=	"";
	$andClause					=	"";

	if(isset($_GET['customerName']))
	{
		$customerName			=	$_GET['customerName'];
		if(!empty($customerName))
		{
			$pos				=	strpos($customerName, " ");
			if($pos				==  true)
			{
				$firstName		=	substr($customerName,0,$pos);
				$lastName		=	substr($customerName,$pos+1);

				$andClause	   .=	" AND (firstName LIKE '%$firstName%' OR lastName LIKE '%$lastName%')";
			}
			else
			{
				$andClause	   .=	" AND (firstName LIKE '%$customerName%')";
			}

		}
	}
	if(isset($_GET['averageOrderFor']))
	{
		$averageOrderFor				=	$_GET['averageOrderFor'];
	}
	$whereClause						=	"WHERE isActiveCustomer=1 AND memberType='".CUSTOMERS."'";
	$andClause							=	"";
	$orderBy							=	"totalCustomersOrders";
	
	$calculateShowingOrderFrom			=	getPreviousGivenDate($nowDateIndia,$averageOrderFor);


	$totalOrders						=	0;
	$totalOrdersLastCalculate			=	0;
	$totalRatedOrders					=	0;
	$totalAvgRate						=	0;
	$totalRateOrdersLastCalculate		=	0;
	$totalAvgProcessTime				=	0;
	$totalAvgProcessTimeLastCalculate	=	0;
	$totalAvgQaTime						=	0;
	$totalAvgQaTimeLastCalculate		=	0;
	$totalAvgProcessQaTime				=	0;
	$totalAvgProcessQaTimeLastCalculate	=	0;

	$query			=	"SELECT members_orders.memberId,COUNT(orderId) AS totalCustomersOrders FROM members INNER JOIN members_orders ON members.memberId=members_orders.memberId ".$whereClause.$andClause." GROUP BY members_orders.memberId ORDER BY ".$orderBy;
 
    $result			=	dbQuery($query);
    if(mysql_num_rows($result))
    {
		$k			=	0;
		$i			=	0;
		$k1			=	0;	
		$k2			=	0;
		$k3			=	0;
		$k4			=	0;
		$k5			=	0;
		$k6			=	0;
		$k7			=	0;
		while($row	=   mysql_fetch_assoc($result))
		{
			$i++;
			$k++;
			
			$memberId				=	$row['memberId'];
			$memberTotalOrders		=	$row['totalCustomersOrders'];

			$totalOrders			=	$totalOrders+$memberTotalOrders;

			$lastCalculateDaysOrders=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders WHERE memberId=$memberId AND orderAddedOn >= '$calculateShowingOrderFrom'"),0);

			if(empty($lastCalculateDaysOrders))
			{
				$lastCalculateDaysOrders	=	0;
			}
			else
			{
				$totalOrdersLastCalculate	=	$totalOrdersLastCalculate+$lastCalculateDaysOrders;
			}

			$memberTotalratedOrders		=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders WHERE memberId=$memberId AND rateGiven <> 0"),0);

			if(empty($memberTotalratedOrders))
			{
				$memberTotalratedOrders	=	"<font class='error'>N/A</font>";
			}
			else
			{
				$totalRatedOrders		=	$totalRatedOrders+$memberTotalratedOrders;
			}

			$memberTotalRatesSum		=	@mysql_result(dbQuery("SELECT SUM(rateGiven) FROM members_orders WHERE memberId=$memberId AND rateGiven <> 0"),0);

			if(empty($memberTotalRatesSum))
			{
				$memberTotalRatesSum	=	0;
			}

			if(!empty($memberTotalratedOrders) && !empty($memberTotalRatesSum))
			{
				$averageRate			=	$memberTotalRatesSum/$memberTotalratedOrders;
				$k1++;

				$averageRate			=	round($averageRate,2);
				$totalAvgRate			=	$totalAvgRate+$averageRate;
			}
			else
			{
				$averageRate			=	"<font class='error'>N/A</font>";
			}

			$lastCalculateTotalratedOrders		=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders WHERE memberId=$memberId AND rateGiven <> 0 AND orderAddedOn >= '$calculateShowingOrderFrom'"),0);

			if(empty($lastCalculateTotalratedOrders))
			{
				$lastCalculateTotalratedOrders	=	"<font class='error'>N/A</font>";
			}
			else
			{
				$totalRateOrdersLastCalculate	=	$totalRateOrdersLastCalculate+$lastCalculateTotalratedOrders;
			}

			$customerTotalProcessedOrders	=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders_reply WHERE memberId=$memberId"),0);
			if(empty($customerTotalProcessedOrders))
			{
				$customerTotalProcessedOrders=	0;
			}
			
			$customerTotalProcessedOrdersTime	=	@mysql_result(dbQuery("SELECT SUM(timeSpentEmployee) FROM members_orders_reply WHERE memberId=$memberId"),0);
			if(empty($customerTotalProcessedOrders))
			{
				$customerTotalProcessedOrdersTime=	0;
			}

			if(!empty($customerTotalProcessedOrders)  && !empty($customerTotalProcessedOrdersTime))
			{
				$averageProcessTime		=	$customerTotalProcessedOrdersTime/$customerTotalProcessedOrders;
				$totalAvgProcessTime	=	$totalAvgProcessTime+$averageProcessTime;
				$k2++;

				$t_averageProcessTime	=	getHours($averageProcessTime);
			}
			else
			{
				$t_averageProcessTime	=	"<font class='error'>N/A</font>";
				$averageProcessTime		=	0;
			}



			$calculateCustomerTotalProcessedOrders	  =	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders_reply WHERE memberId=$memberId AND replyFileAddedOn  >= '$calculateShowingOrderFrom'"),0);
			if(empty($calculateCustomerTotalProcessedOrders))
			{
				$calculateCustomerTotalProcessedOrders=	0;
			}
			
			$calculateCustomerTotalProcessedOrdersTime	=	@mysql_result(dbQuery("SELECT SUM(timeSpentEmployee) FROM members_orders_reply WHERE memberId=$memberId AND replyFileAddedOn  >= '$calculateShowingOrderFrom'"),0);
			if(empty($calculateCustomerTotalProcessedOrdersTime))
			{
				$calculateCustomerTotalProcessedOrdersTime=	0;
			}

			if(!empty($calculateCustomerTotalProcessedOrders)  && !empty($calculateCustomerTotalProcessedOrdersTime))
			{
				$calculateAverageProcessTime       =	$calculateCustomerTotalProcessedOrdersTime/$calculateCustomerTotalProcessedOrders;
				
				$totalAvgProcessTimeLastCalculate  =	$totalAvgProcessTimeLastCalculate+$calculateAverageProcessTime;
				$k3++;
				
				$t_calculateAverageProcessTime     =	getHours($calculateAverageProcessTime);
			}
			else
			{
				$t_calculateAverageProcessTime	=	"<font class='error'>N/A</font>";
				$calculateAverageProcessTime	=	0;
			}



			$customerTotalQaOrders	=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders_reply WHERE memberId=$memberId AND hasQaDone=1"),0);
			if(empty($customerTotalQaOrders))
			{
				$customerTotalQaOrders=	0;
			}
					
			$customerTotalQaOrdersTime	=	@mysql_result(dbQuery("SELECT SUM(timeSpentQa) FROM members_orders_reply WHERE memberId=$memberId AND hasQaDone=1"),0);
			if(empty($customerTotalQaOrdersTime))
			{
				$customerTotalQaOrdersTime=	0;
			}

			if(!empty($customerTotalQaOrders)  && !empty($customerTotalQaOrdersTime))
			{
				$averageQaTime		=	$customerTotalQaOrdersTime/$customerTotalQaOrders;

				$totalAvgQaTime		=	$totalAvgQaTime+$averageQaTime;
				$k4++;

				$t_averageQaTime	=	getHours($averageQaTime);
			}
			else
			{
				$t_averageQaTime	=	"<font class='error'>N/A</font>";
				$averageQaTime		=	0;
			}



			$calculateCustomerTotalQaOrders	  =	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders_reply WHERE memberId=$memberId AND replyFileAddedOn  >= '$calculateShowingOrderFrom' AND hasQaDone=1"),0);
			if(empty($calculateCustomerTotalQaOrders))
			{
				$calculateCustomerTotalQaOrders=	0;
			}
			
			$calculateCustomerTotalQaOrdersTime	=	@mysql_result(dbQuery("SELECT SUM(timeSpentQa) FROM members_orders_reply WHERE memberId=$memberId AND replyFileAddedOn  >= '$calculateShowingOrderFrom' AND hasQaDone=1"),0);
			if(empty($calculateCustomerTotalQaOrdersTime))
			{
				$calculateCustomerTotalQaOrdersTime=	0;
			}

			if(!empty($calculateCustomerTotalQaOrders)  && !empty($calculateCustomerTotalQaOrdersTime))
			{
				$calculateAverageQaTime    =	$calculateCustomerTotalQaOrdersTime/$calculateCustomerTotalQaOrders;

				$totalAvgQaTimeLastCalculate= $totalAvgQaTimeLastCalculate+$calculateAverageQaTime;

				$t_calculateAverageQaTime  =	getHours($calculateAverageQaTime);
				$k5++;
			}
			else
			{
				$t_calculateAverageQaTime	=	"<font class='error'>N/A</font>";
				$calculateAverageQaTime		=	0;
			}


			$totalAverageProcesQaTime			=	$averageProcessTime+$averageQaTime;
			$totalAverageProcesQaTimeForLast	=	$calculateAverageProcessTime+$calculateAverageQaTime;

			if(!empty($totalAverageProcesQaTime))
			{
				$totalAvgProcessQaTime				=	$totalAvgProcessQaTime+$totalAverageProcesQaTime;

				$k6++;
			}

			if(!empty($totalAverageProcesQaTimeForLast))
			{
				$totalAvgProcessQaTimeLastCalculate	=	$totalAvgProcessQaTimeLastCalculate+$totalAverageProcesQaTimeForLast;

				$k7++;
			}


		}
	}

?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>
<div id="showTotal">
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0' style="border:3px solid #4d4d4d;">
	<tr>
		<td width='5%' class='smalltext2' valign='top'>&nbsp;</td>
		<td width='17%' class='smalltext2' valign='top'>&nbsp;</td>
		<td width='7%' class='smalltext2' valign='top'>
			<?php
				echo "<b>".$totalOrders."</b>";
			?>
		</td>
		<td width='9%' class='smalltext2' valign='top'>
			<?php
				echo "<b>".$totalOrdersLastCalculate."</b>";
			?>
		</td>
		<td width='6%' class='smalltext2' valign='top'>
			<?php
				echo "<b>".$totalRatedOrders."</b>";
			?>
		</td>
		<td width='6%' class='smalltext2' valign='top'>
			<?php
				if(!empty($totalAvgRate))
				{
					$displayTotalAvg	=	$totalAvgRate/$k1;

					$showDisplayTotalAvg=  round($displayTotalAvg,2);
				}
				else
				{
					$showDisplayTotalAvg= "<font class='error'>N/A</font>";
				}
				echo "<b>".$showDisplayTotalAvg."</b>";
			?>
		</td>
		<td width='7%' class='smalltext2' valign='top'>
			<?php
				echo "<b>".$totalRateOrdersLastCalculate."</b>";
			?>
		</td>
		<td width='7%' class='smalltext2' valign='top'>
			<?php
				if(!empty($totalAvgProcessTime))
				{
					$displayTotalAvgProcees		=	$totalAvgProcessTime/$k2;
					
					$showDisplayTotalAvgProcees	=	getHours($displayTotalAvgProcees);
				}
				else
				{
					$showDisplayTotalAvgProcees	=	"<font class='error'>N/A</font>";
				}
				echo "<b>".$showDisplayTotalAvgProcees."</b>";
			?>
		</td>
		<td class='smalltext2' valign='top' width='7%'>
			<?php
				if(!empty($totalAvgProcessTimeLastCalculate))
				{
					$displayTotalCalculateAvgProcees	=	$totalAvgProcessTimeLastCalculate/$k3;

					$showDisplayTotalCalculateAvgProcees=	 getHours($displayTotalCalculateAvgProcees);
				}
				else
				{
					$showDisplayTotalCalculateAvgProcees= "<font class='error'>N/A</font>";
				}
				echo "<b>".$showDisplayTotalCalculateAvgProcees."</b>";
			?>
		</td>
		<td width='7%' class='smalltext2' valign='top'>
			<?php
				if(!empty($totalAvgQaTime))
				{
					$displayTotalQaAvg		=	$totalAvgQaTime/$k4;

					$showDisplayTotalQaAvg	=	getHours($displayTotalQaAvg);
				}
				else
				{
					$showDisplayTotalQaAvg	= "<font class='error'>N/A</font>";
				}
				echo "<b>".$showDisplayTotalQaAvg."</b>";
			?>
		</td>
		<td class='smalltext2' valign='top' width='6%'>
			<?php
				if(!empty($totalAvgQaTimeLastCalculate))
				{
					$displayTotallastCalculateQaAvg		=	$totalAvgQaTimeLastCalculate/$k5;

					$showDisplayTotallastCalculateQaAvg	= getHours($displayTotallastCalculateQaAvg);
				}
				else
				{
					$showDisplayTotallastCalculateQaAvg = "<font class='error'>N/A</font>";
				}
				echo "<b>".$showDisplayTotallastCalculateQaAvg."</b>";
			?>
		</td>
		<td width='6%' class='smalltext2' valign='top'>
			<?php
				if(!empty($totalAvgProcessQaTime))
				{
					$displayTotalProcessQaAvg		=	$totalAvgProcessQaTime/$k6;

					$showDisplayTotalProcessQaAvg	=	getHours($displayTotalProcessQaAvg);
				}
				else
				{
					$showDisplayTotalProcessQaAvg	=	 "<font class='error'>N/A</font>";
				}
				echo "<b>".$showDisplayTotalProcessQaAvg."</b>";
			?>
		</td>
		<td class='smalltext2' valign='top'>
			<?php
				if(!empty($totalAvgProcessQaTimeLastCalculate))
				{
					$displayTotalLastProcessQaAvg	 =	$totalAvgProcessQaTimeLastCalculate/$k7;

					$showDisplayTotalLastProcessQaAvg=	 getHours($displayTotalLastProcessQaAvg);
				}
				else
				{
					$showDisplayTotalLastProcessQaAvg= "<font class='error'>N/A</font>";
				}
				echo "<b>".$showDisplayTotalLastProcessQaAvg."</b>";
			?>
		</td>
	</tr>
	<tr>
		<td colspan='15'>
			<hr size='1' width='100%' color='#4d4d4d'>
		</td>
	</tr>
	<tr>
		<td colspan='15' align="right">
			<img src="<?php echo SITE_URL;?>/images/close.gif" title="Close" border="0" onclick="javascript:removeTotal(2)">&nbsp;
		</td>
	</tr>
</table>
</div>