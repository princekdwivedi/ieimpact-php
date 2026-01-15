<?php
	ob_start();
	session_start();
	ini_set('display_errors', '1');
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	//include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	$orderObj					=  new orders();
	$pagingObj					=  new Paging();

	$a_serachingOrdersBy		=	array("1"=>"From less orders to more|totalEmployeeOrders","2"=>"From more orders to less|totalEmployeeOrders DESC","3"=>"By new employees|employee_details.addedOn DESC","4"=>"By old employees|employee_details.addedOn");

	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	if(isset($_REQUEST['recNo']))
	{
		$recNo					=	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo	=	0;
	}
	$whereClause				=	"WHERE members_orders.orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND isActive=1 AND acceptedBy <> 0";
	$andClause					=	"";
	$orderBy					=	"totalEmployeeOrders ";
	$queryString				=	"";
	$averageOrderFor			=	15;
	$employeeName				=	"";
	$andClause					=	"";
	$serachOrderBy				=	1;
	$displayTotalLink			=	"";
	
	if(isset($_REQUEST['searchFormSubmit']))
	{
		extract($_REQUEST);
		$redirectPath				  =	"averageOrderFor=".$averageOrderFor;
		$redirectPath				 .=	"&serachOrderBy=".$serachOrderBy;
		if(!empty($showEmployeeName))
		{
			$showEmployeeName		  =	trim($showEmployeeName);
			$redirectPath			 .=	"&employeeName=".$employeeName;
		}

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/view-employee-average-ratings.php?".$redirectPath);
		exit();
	}

	if(isset($_GET['employeeName']))
	{
		$employeeName			=	$_GET['employeeName'];
		if(!empty($employeeName))
		{
			$andClause					.=	" AND fullName LIKE '%$employeeName%'";
			

			$queryString				.=	"&employeeName=".$employeeName;
			$displayTotalLink			.=	"&employeeName=".$employeeName;
		}
	}
	if(isset($_GET['averageOrderFor']))
	{
		$averageOrderFor				=	$_GET['averageOrderFor'];
		$queryString				   .=	"&averageOrderFor=".$averageOrderFor;
		$displayTotalLink			   .=	"&averageOrderFor=".$averageOrderFor;
	}
	if(isset($_GET['serachOrderBy']))
	{
		$serachOrderBy						=	$_GET['serachOrderBy'];
		if(!empty($serachOrderBy))
		{
			$serachValue	=	$a_serachingOrdersBy[$serachOrderBy];

			list($serachByText,$orderBy)	=	explode("|",$serachValue);

			
			$queryString				   .=	"&serachOrderBy=".$serachOrderBy;
		}
	}
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

?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
</script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" />
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>
<script type='text/javascript'>
$().ready(function() 
{
	$("#searchName").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-pdf-employee.php", {width: 360,selectFirst: false});
});

function removeTotal(flag)
{
	if(flag == 1)
	{
		document.getElementById('showTotal').style.display = 'inline';
	}
	else
	{
		document.getElementById('showTotal').style.display = 'none';
	}
}
function removeEmployees(employeeId,flag)
{
	if(flag == 1)
	{
		document.getElementById('showEmployeeOrderProcess'+employeeId).style.display = 'inline';
	}
	else
	{
		document.getElementById('showEmployeeOrderProcess'+employeeId).style.display = 'none';
	}
}
</script>
<table width="98%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="2" class='heading1'>VIEW EMPLOYEE <?php echo $employeeName;?> AVERAGE ORDER RATINGS FOR LAST <?php echo $averageOrderFor;?> DAYS</td>
	</tr>
</table>
<br>
<form name="serachCustomerDays" action="" method="POST" onsubmit="return search();">
	<table width="98%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="14%" class='smalltext2'>SERACH AN EMPLOYEE</td>
			<td width="1%" class='smalltext2'>:</td>
			<td width="31%">
				<input type='text' name="showEmployeeName" size="50" value="<?php echo $employeeName;?>" id="searchName"  style="border:1px solid #4d4d4d;height:25px;font-size:15px;">
			</td>
			<td width="14%" class='smalltext2'>TO VIEW ORDERS FOR</td>
			<td width="1%" class='smalltext2'>:</td>
			<td width="7%" class="smalltext">
				<select name="averageOrderFor">
				<?php
					for($i=10;$i<=60;$i++)
					{
						$select		=	"";
						if($i		==	$averageOrderFor)
						{
							$select	=	"selected";
						}

						echo "<option value='$i' $select>$i</option>";
					}
				?>
				</select>Days
			</td>
			<td width="6%" class='smalltext2'>ORDER BY</td>
			<td width="1%" class='smalltext2'>:</td>
			<td width="12%" class="smalltext2">
				<select name="serachOrderBy">
				<?php
					foreach($a_serachingOrdersBy as $k=>$v)
					{
						$select		=	"";
						if($k		==	$serachOrderBy)
						{
							$select	=	"selected";
						}
						$serachValue1	=	$a_serachingOrdersBy[$k];

						list($text,$by)	=	explode("|",$serachValue1);

						echo "<option value='$k' $select>$text</option>";
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
<br>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
	<!--<tr>
		<td>
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/print-employee-total-order-rating-avg.php?print=1<?php echo $queryString;?>" class="link_style8">PRINT EMPLOYEE AVEARGE RATING</a>
		</td>
	</tr>-->
</table>
<?php
	$start					  =	0;
	$recsPerPage	          =	20;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause." GROUP BY employee_details.employeeId";
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"employee_details INNER JOIN members_orders ON employee_details.employeeId=members_orders.acceptedBy";
	$pagingObj->primaryColumn =	"employee_details.employeeId";
	$pagingObj->selectColumns = "employee_details.employeeId,fullName,COUNT(orderId) AS totalEmployeeOrders";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/view-employee-average-ratings.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
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
		$i			=	$recNo;
		$k			=	0;
		$k1			=	0;	
		$k2			=	0;
		$k3			=	0;
		$k4			=	0;
		$k5			=	0;
		$k6			=	0;
		$k7			=	0;
		while($row	=   mysqli_fetch_assoc($recordSet))
		{
			$i++;
			$k++;
			
			$employeeId				=	$row['employeeId'];
			$fullName				=	stripslashes($row['fullName']);
			$totalEmployeeOrders	=	$row['totalEmployeeOrders'];

			$totalOrders			=	$totalOrders+$totalEmployeeOrders;

			$employeesAcceptedOrder	=	$orderObj->getEmployeesOwnProcessedOrder($employeeId);

			$lastCalculateDaysOrders=	$employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND orderAddedOn >= '$calculateShowingOrderFrom' AND acceptedBy=$employeeId","total");

			if(empty($lastCalculateDaysOrders))
			{
				$lastCalculateDaysOrders	=	0;
			}
			else
			{
				$totalOrdersLastCalculate	=	$totalOrdersLastCalculate+$lastCalculateDaysOrders;
			}

			$memberTotalratedOrders		=	$employeeObj->getSingleQueryResult("SELECT COUNT(orderId)as total  FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND rateGiven <> 0 AND acceptedBy=$employeeId AND isRateCountingEmployeeSide='yes'","total");

			if(empty($memberTotalratedOrders))
			{
				$memberTotalratedOrders	=	"<font color='#ff0000'>N/A</font>";
			}
			else
			{
				$totalRatedOrders		=	$totalRatedOrders+$memberTotalratedOrders;
			}

			$memberTotalRatesSum		=	$employeeObj->getSingleQueryResult("SELECT SUM(rateGiven) as total FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND rateGiven <> 0 AND acceptedBy=$employeeId AND isRateCountingEmployeeSide='yes'","total");

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
				$averageRate			=	"<font color='#ff0000'>N/A</font>";
			}

			$lastCalculateTotalratedOrders		=	$employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total  FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND rateGiven <> 0 AND orderAddedOn >= '$calculateShowingOrderFrom' AND acceptedBy=$employeeId AND isRateCountingEmployeeSide='yes'","total");

			if(empty($lastCalculateTotalratedOrders))
			{
				$lastCalculateTotalratedOrders	=	"<font color='#ff0000'>N/A</font>";
			}
			else
			{
				$totalRateOrdersLastCalculate	=	$totalRateOrdersLastCalculate+$lastCalculateTotalratedOrders;
			}

			$customerTotalProcessedOrders	=	$employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total FROM members_orders_reply WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND orderId IN ($employeesAcceptedOrder)","total");
			if(empty($customerTotalProcessedOrders))
			{
				$customerTotalProcessedOrders=	0;
			}
			
			$customerTotalProcessedOrdersTime	=	$employeeObj->getSingleQueryResult("SELECT SUM(timeSpentEmployee) as total FROM members_orders_reply WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND orderId IN ($employeesAcceptedOrder)","total");
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
				$t_averageProcessTime	=	"<font color='#ff0000'>N/A</font>";
				$averageProcessTime		=	0;
			}



			$calculateCustomerTotalProcessedOrders	  =	$employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total FROM members_orders_reply WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND replyFileAddedOn  >= '$calculateShowingOrderFrom' AND orderId IN ($employeesAcceptedOrder)","total");
			if(empty($calculateCustomerTotalProcessedOrders))
			{
				$calculateCustomerTotalProcessedOrders=	0;
			}
			
			$calculateCustomerTotalProcessedOrdersTime	=	$employeeObj->getSingleQueryResult("SELECT SUM(timeSpentEmployee) as total FROM members_orders_reply WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND replyFileAddedOn  >= '$calculateShowingOrderFrom' AND orderId IN ($employeesAcceptedOrder)","total");
			if(empty($calculateCustomerTotalProcessedOrdersTime))
			{
				$calculateCustomerTotalProcessedOrdersTime=	0;
			}

			if(!empty($calculateCustomerTotalProcessedOrders)  && !empty($calculateCustomerTotalProcessedOrdersTime))
			{
				$calculateAverageProcessTime       =	$calculateCustomerTotalProcessedOrdersTime/$calculateCustomerTotalProcessedOrders;
				
				$totalAvgProcessTimeLastCalculate  =	$totalAvgProcessTimeLastCalculate+$calculateAverageProcessTime;
				$k3++;
				
				$t_calculateAverageProcessTime  =	getHours($calculateAverageProcessTime);
			}
			else
			{
				$t_calculateAverageProcessTime	=	"<font color='#ff0000'>N/A</font>";
				$calculateAverageProcessTime	=	0;
			}



			$customerTotalQaOrders	      =	$employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total FROM members_orders_reply WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND hasQaDone=1 AND orderId IN ($employeesAcceptedOrder)","total");
			if(empty($customerTotalQaOrders))
			{
				$customerTotalQaOrders    =	0;
			}
					
			$customerTotalQaOrdersTime	  =	$employeeObj->getSingleQueryResult("SELECT SUM(timeSpentQa) as total FROM members_orders_reply WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND hasQaDone=1 AND orderId IN ($employeesAcceptedOrder)","total");
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
				$t_averageQaTime	=	"<font color='#ff0000'>N/A</font>";
				$averageQaTime		=	0;
			}



			$calculateCustomerTotalQaOrders	  =	$employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total FROM members_orders_reply WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND replyFileAddedOn  >= '$calculateShowingOrderFrom' AND hasQaDone=1 AND orderId IN ($employeesAcceptedOrder)","total");
			if(empty($calculateCustomerTotalQaOrders))
			{
				$calculateCustomerTotalQaOrders=	0;
			}
			
			$calculateCustomerTotalQaOrdersTime	=	$employeeObj->getSingleQueryResult("SELECT SUM(timeSpentQa) as total FROM members_orders_reply WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND replyFileAddedOn  >= '$calculateShowingOrderFrom' AND hasQaDone=1 AND orderId IN ($employeesAcceptedOrder)","total");
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
				$t_calculateAverageQaTime	=	"<font color='#ff0000'>N/A</font>";
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
	?>
	<tr>
		<td class='smalltext2' valign='top'><?php echo $employeeId;?></td>
		<td class='smalltext2' valign='top'>
			<?php
				$url			=	SITE_URL_EMPLOYEES."/display-employee-processing-customers-rating.php?averageOrderFor=$averageOrderFor&employeeId=";
			?>
			<a onclick="commonFunc('<?php echo $url;?>','showEmployeeOrderProcess<?php echo $employeeId;?>',<?php echo $employeeId;?>);removeEmployees(<?php echo $employeeId;?>,1);" class='link_style2' style="cursor:pointer">
				<?php echo $fullName;?>
			</a>
		</td>
		<td class='smalltext2' valign='top'><?php echo $totalEmployeeOrders;?></td>
		<td class='text' valign='top'><?php echo $lastCalculateDaysOrders;?></td>
		<td class='text' valign='top'><?php echo $memberTotalratedOrders;?></td>
		<td class='text' valign='top'><?php echo $averageRate;?></td>
		<td class='text' valign='top'><?php echo $lastCalculateTotalratedOrders;?></td>
		<td class='text' valign='top'><?php echo $t_averageProcessTime;?></td>
		<td class='text' valign='top'><?php echo $t_calculateAverageProcessTime;?></td>
		<td class='text' valign='top'><?php echo $t_averageQaTime;?></td>
		<td class='text' valign='top'><?php echo $t_calculateAverageQaTime;?></td>
		<td class='text' valign='top'>
			<?php
				if(empty($totalAverageProcesQaTime))
				{
					echo	"<font color='#ff0000'>N/A</font>";
				}
				else
				{
					echo getHours($totalAverageProcesQaTime);
				}
			?>
		</td>
		<td class='text' valign='top'>
			<?php 
				if(empty($totalAverageProcesQaTimeForLast))
				{
					echo "<font color='#ff0000'>N/A</font>";
				}
				else
				{
					echo getHours($totalAverageProcesQaTimeForLast);
				}
			?>
		</td>
	</tr>
	<tr>
		<td colspan='15'>
			<div id="showEmployeeOrderProcess<?php echo $employeeId;?>"></div>
		</td>
	</tr>
	<tr>
		<td colspan='15'>
			<hr size='1' width='100%' color='#4d4d4d'>
		</td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td class='smalltext2' valign='top' colspan="2">
			<b><?php echo $averageOrderFor;?> Days Total/Average For <?php echo $k;?> Employees</b>
		</td>
		<td class='smalltext2' valign='top'>
			<?php
				echo "<b>".$totalOrders."</b>";
			?>
		</td>
		<td class='smalltext2' valign='top'><b>
			<?php
				echo "<b>".$totalOrdersLastCalculate."</b>";
			?></b>
		</td>
		<td class='smalltext2' valign='top'><b>
			<?php
				echo "<b>".$totalRatedOrders."</b>";
			?></b>
		</td>
		<td class='smalltext2' valign='top'><b>
			<?php
				if(!empty($totalAvgRate))
				{
					$displayTotalAvg	=	$totalAvgRate/$k1;

					echo round($displayTotalAvg,2);
				}
				else
				{
					echo "<font color='#ff0000'>N/A</font>";
				}
			?>
		</td>
		<td class='smalltext2' valign='top'><b>
			<?php
				echo "<b>".$totalRateOrdersLastCalculate."</b>";
			?></b>
		</td>
		<td class='smalltext2' valign='top'><b>
			<?php
				if(!empty($totalAvgProcessTime))
				{
					$displayTotalAvgProcees	=	$totalAvgProcessTime/$k2;
					
					echo getHours($displayTotalAvgProcees);
				}
				else
				{
					echo "<font color='#ff0000'>N/A</font>";
				}
			?></b>
		</td>
		<td class='smalltext2' valign='top'><b>
			<?php
				if(!empty($totalAvgProcessTimeLastCalculate))
				{
					$displayTotalCalculateAvgProcees	=	$totalAvgProcessTimeLastCalculate/$k3;

					echo getHours($displayTotalCalculateAvgProcees);
				}
				else
				{
					echo "<font color='#ff0000'>N/A</font>";
				}
			?></b>
		</td>
		<td class='smalltext2' valign='top'><b>
			<?php
				if(!empty($totalAvgQaTime))
				{
					$displayTotalQaAvg	=	$totalAvgQaTime/$k4;

					echo getHours($displayTotalQaAvg);
				}
				else
				{
					echo "<font color='#ff0000'>N/A</font>";
				}
			?></b>
		</td>
		<td class='smalltext2' valign='top'><b>
			<?php
				if(!empty($totalAvgQaTimeLastCalculate))
				{
					$displayTotallastCalculateQaAvg	=	$totalAvgQaTimeLastCalculate/$k5;

					echo getHours($displayTotallastCalculateQaAvg);
				}
				else
				{
					echo "<font color='#ff0000'>N/A</font>";
				}
			?></b>
		</td>
		<td class='smalltext2' valign='top'><b>
			<?php
				if(!empty($totalAvgProcessQaTime))
				{
					$displayTotalProcessQaAvg	=	$totalAvgProcessQaTime/$k6;

					echo getHours($displayTotalProcessQaAvg);
				}
				else
				{
					echo "<font color='#ff0000'>N/A</font>";
				}
			?></b>
		</td>
		<td class='smalltext2' valign='top'><b>
			<?php
				if(!empty($totalAvgProcessQaTimeLastCalculate))
				{
					$displayTotalLastProcessQaAvg	=	$totalAvgProcessQaTimeLastCalculate/$k7;

					echo getHours($displayTotalLastProcessQaAvg);
				}
				else
				{
					echo "<font color='#ff0000'>N/A</font>";
				}
			?></b>
		</td>
	</tr>
	<tr>
		<td colspan="2"></td>
	</tr>
	<tr>
		<td colspan="15" class="smalltext2">
			<?php
				$displayUrl		=	SITE_URL_EMPLOYEES."/display-all-employee-average-total.php?display=1".$displayTotalLink;	
			?>
			<a onclick="commonFunc('<?php echo $displayUrl;?>','showTotal');removeTotal(1);" class="link_style2" style="cursor:pointer"><b>TOTAL AVERAGE/RATINGS </b></a>
		</td>
	</tr>
	<tr>
		<td colspan="15">
			<div id="showTotal" style="display:none;"></div>
		</td>
	</tr>
	<?php
		echo "<tr><td align='right' colspan='15'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr>";
	?>
</table>
<?php
	}
	else
	{
		echo "<br><br><center><font class='error'><b>No Record Found </b></font></center>";
	}
?>
<br>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
	<tr>
		<td>
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/print-employee-total-order-rating-avg.php?print=1<?php echo $queryString;?>" class="link_style8">PRINT EMPLOYEE AVEARGE RATING</a>
		</td>
	</tr>
</table>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>