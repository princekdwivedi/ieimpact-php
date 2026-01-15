<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
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
	include(SITE_ROOT			. "/classes/new-pagingclass.php");
	$orderObj					=  new orders();
	$pagingObj					=  new Paging();

	$a_serachingOrdersBy		=	array("1"=>"From less orders to more|totalCustomersOrders","2"=>"From more orders to less|totalCustomersOrders DESC","3"=>"By new customers|members.addedOn DESC","4"=>"By old customers|members.addedOn");

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
	$whereClause				=	"WHERE members_orders.orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND isActiveCustomer=1 AND memberType='".CUSTOMERS."' AND isTestAccount=0";
	$andClause					=	"";
	$orderBy					=	"totalCustomersOrders";
	$queryString				=	"";
	$averageOrderFor			=	15;
	$customerName				=	"";
	$andClause					=	"";
	$serachOrderBy				=	1;
	$displayTotalLink			=	"";
	
	if(isset($_REQUEST['searchFormSubmit']))
	{
		extract($_REQUEST);
		$redirectPath				  =	"averageOrderFor=".$averageOrderFor;
		$redirectPath				 .=	"&serachOrderBy=".$serachOrderBy;
		if(!empty($showCustomerName))
		{
			$showCustomerName		  =	trim($showCustomerName);
			$redirectPath			 .=	"&customerName=".$showCustomerName;
		}

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/view-customer-average-ratings.php?".$redirectPath);
		exit();
	}

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

			$queryString	  .=	"&customerName=".$customerName;
			$displayTotalLink .=	"&customerName=".$customerName;
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
		$serachOrderBy					=	$_GET['serachOrderBy'];
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
	$("#searchName").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-customer.php", {width: 360,selectFirst: false});
});
function search()
{
	return;
	form1	=	document.serachCustomerDays;
	if(form1.customerName.value == "")
	{
		alert("Please serach a customer !!");
		form1.customerName.focus();
		return false;
	}
}
function removeEmployees(memberId,flag)
{
	if(flag == 1)
	{
		document.getElementById('showCustomerOrderProcess'+memberId).style.display = 'inline';
	}
	else
	{
		document.getElementById('showCustomerOrderProcess'+memberId).style.display = 'none';
	}
}
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
</script>
<table width="98%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="2" class='heading1'>VIEW CUSTOMER <?php echo $customerName;?> AVERAGE ORDER RATINGS FOR LAST <?php echo $averageOrderFor;?> DAYS</td>
	</tr>
</table>
<br>
<form name="serachCustomerDays" action="" method="POST" onsubmit="return search();">
	<table width="98%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="14%" class='smalltext2'>SERACH A CUSTOMER</td>
			<td width="1%" class='smalltext2'>:</td>
			<td width="31%">
				<input type='text' name="showCustomerName" size="50" value="<?php echo $customerName;?>" id="searchName"  style="border:1px solid #4d4d4d;height:25px;font-size:15px;">
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
	<tr>
		<td>
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/print-customer-total-order-rating-avg.php?print=1<?php echo $queryString;?>" class="link_style8">PRINT CUSTOMER AVEARGE RATING</a>
		</td>
	</tr>
</table>
<?php
	$start					  =	0;
	$recsPerPage	          =	20;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause." GROUP BY members_orders.memberId";
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"members INNER JOIN members_orders ON members.memberId=members_orders.memberId";
	$pagingObj->primaryColumn  =	"members_orders.memberId";
	$pagingObj->selectColumns = "members_orders.memberId,firstName,lastName,COUNT(orderId) AS totalCustomersOrders";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/view-customer-average-ratings.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();

		////////////////// CREATING ALL COUNTING ARRAY //////////////////////
		$a_lastCalculatedOrders      			= array();
		$a_totalRatedOrders          			= array();
		$a_totalRatedSum   		     			= array();
		$a_lastCalculateRated   	 			= array();
		$a_customerTotalProcessed    			= array();
		$a_customerProcessedTime     			= array();
		$a_customerTotalProcessedOrders 		= array();
		$a_customerTotalProcessedOrdersTime		= array();	
		$a_customerTotalQaOrders 				= array();
		$a_customerTotalQaOrdersTime 			= array();
		$a_calculateCustomerTotalQaOrders   	= array();
		$a_calculateCustomerTotalQaOrdersTime 	= array();

		$query 	=	"SELECT COUNT(orderId) as totalOrders,memberId FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND orderAddedOn >= '$calculateShowingOrderFrom' GROUP BY memberId";
		$result =	dbQuery($query);
		if(mysqli_num_rows($result)){
			while($row  =  mysqli_fetch_assoc($result)){
				$memberId 	=	$row['memberId'];

				$a_lastCalculatedOrders[$memberId] = $row['totalOrders'];
			}
		}

		$query 	=	"SELECT COUNT(orderId) as totalOrders,memberId FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND rateGiven <> 0 AND isRateCountingEmployeeSide='yes' GROUP BY memberId";
		$result =	dbQuery($query);
		if(mysqli_num_rows($result)){
			while($row  =  mysqli_fetch_assoc($result)){
				$memberId 	=	$row['memberId'];

				$a_totalRatedOrders[$memberId] = $row['totalOrders'];
			}
		}

		$query 	=	"SELECT SUM(rateGiven) as totalSum,memberId FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND rateGiven <> 0 AND isRateCountingEmployeeSide='yes' GROUP BY memberId";
		$result =	dbQuery($query);
		if(mysqli_num_rows($result)){
			while($row  =  mysqli_fetch_assoc($result)){
				$memberId 	=	$row['memberId'];

				$a_totalRatedSum[$memberId] = $row['totalSum'];
			}
		}

		$query 	=	"SELECT COUNT(orderId) as totalOrders,memberId FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND rateGiven <> 0 AND orderAddedOn >= '$calculateShowingOrderFrom' AND isRateCountingEmployeeSide='yes' GROUP BY memberId";
		$result =	dbQuery($query);
		if(mysqli_num_rows($result)){
			while($row  =  mysqli_fetch_assoc($result)){
				$memberId 	=	$row['memberId'];

				$a_lastCalculateRated[$memberId] = $row['totalOrders'];
			}
		}

		$query 	=	"SELECT COUNT(orderId) as totalOrders,SUM(timeSpentEmployee) as totalTime, memberId FROM members_orders_reply WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." GROUP BY memberId";
		$result =	dbQuery($query);
		if(mysqli_num_rows($result)){
			while($row  =  mysqli_fetch_assoc($result)){
				$memberId 	=	$row['memberId'];

				$a_customerTotalProcessed[$memberId] = $row['totalOrders'];
				$a_customerProcessedTime[$memberId]  = $row['totalTime'];
			}
		}

		$query 	=	"SELECT COUNT(orderId) as totalOrders,SUM(timeSpentEmployee) as totalTime,memberId FROM members_orders_reply WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND replyFileAddedOn  >= '$calculateShowingOrderFrom' GROUP BY memberId";
		$result =	dbQuery($query);
		if(mysqli_num_rows($result)){
			while($row  =  mysqli_fetch_assoc($result)){
				$memberId 	=	$row['memberId'];

				$a_customerTotalProcessedOrders[$memberId] = $row['totalOrders'];
				$a_customerTotalQaOrders[$memberId]        = $row['totalTime'];
			}
		}

		$query 	=	"SELECT COUNT(orderId) as totalOrders,SUM(timeSpentQa) as totalTime,memberId FROM members_orders_reply WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND hasQaDone=1 GROUP BY memberId";
		$result =	dbQuery($query);
		if(mysqli_num_rows($result)){
			while($row  =  mysqli_fetch_assoc($result)){
				$memberId 	=	$row['memberId'];

				$a_customerTotalProcessedOrdersTime[$memberId] = $row['totalOrders'];
				$a_customerTotalQaOrdersTime[$memberId]        = $row['totalTime'];
			}
		}

		
		$query 	=	"SELECT COUNT(orderId) as totalOrders,SUM(timeSpentQa) as totalTime,memberId FROM members_orders_reply WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND replyFileAddedOn  >= '$calculateShowingOrderFrom' AND hasQaDone=1 GROUP BY memberId";
		$result =	dbQuery($query);
		if(mysqli_num_rows($result)){
			while($row  =  mysqli_fetch_assoc($result)){
				$memberId 	=	$row['memberId'];

				$a_calculateCustomerTotalQaOrders[$memberId] = $row['totalOrders'];
				$a_calculateCustomerTotalQaOrdersTime[$memberId] = $row['totalTime'];
			}
		}
?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
	<tr>
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
			
			$memberId				=	$row['memberId'];
			$firstName				=	stripslashes($row['firstName']);
			$lastName				=	stripslashes($row['lastName']);
			$memberTotalOrders		=	$row['totalCustomersOrders'];

			$memberName				=	$firstName." ".substr($lastName, 0, 1);

			$totalOrders			=	$totalOrders+$memberTotalOrders;

			
			$lastCalculateDaysOrders=   0;
			if(!empty($a_lastCalculatedOrders) && count($a_lastCalculatedOrders) && array_key_exists($memberId,$a_lastCalculatedOrders)){
				$lastCalculateDaysOrders   =   $a_lastCalculatedOrders[$memberId];
			}

			if(empty($lastCalculateDaysOrders))
			{
				$lastCalculateDaysOrders	=	0;
			}
			else
			{
				$totalOrdersLastCalculate	=	$totalOrdersLastCalculate+$lastCalculateDaysOrders;
			}

			$memberTotalratedOrders    =  0;

			if(!empty($a_totalRatedOrders) && count($a_totalRatedOrders) && array_key_exists($memberId,$a_totalRatedOrders)){
				$memberTotalratedOrders   =   $a_totalRatedOrders[$memberId];
			}

			if(empty($memberTotalratedOrders))
			{
				$memberTotalratedOrders	=	"<font color='#ff0000'>N/A</font>";
			}
			else
			{
				$totalRatedOrders		=	$totalRatedOrders+$memberTotalratedOrders;
			}

			
			$memberTotalRatesSum    =  0;

			if(!empty($a_totalRatedSum) && count($a_totalRatedSum) && array_key_exists($memberId,$a_totalRatedSum)){
				$memberTotalRatesSum   =   $a_totalRatedSum[$memberId];
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

			$lastCalculateTotalratedOrders      = 0;
			if(!empty($a_lastCalculateRated) && count($a_lastCalculateRated) && array_key_exists($memberId,$a_lastCalculateRated)){
				$lastCalculateTotalratedOrders   =   $a_lastCalculateRated[$memberId];
			}

			if(empty($lastCalculateTotalratedOrders))
			{
				$lastCalculateTotalratedOrders	=	"<font color='#ff0000'>N/A</font>";
			}
			else
			{
				$totalRateOrdersLastCalculate	=	$totalRateOrdersLastCalculate+$lastCalculateTotalratedOrders;
			}

			$customerTotalProcessedOrders=	0;
			if(!empty($a_customerTotalProcessed) && count($a_customerTotalProcessed) && array_key_exists($memberId,$a_customerTotalProcessed)){
				$customerTotalProcessedOrders   =   $a_customerTotalProcessed[$memberId];
			}
			
			
			$customerTotalProcessedOrdersTime     = 0;
			if(!empty($a_customerProcessedTime) && count($a_customerProcessedTime) && array_key_exists($memberId,$a_customerProcessedTime)){
				$customerTotalProcessedOrdersTime   =   $a_customerProcessedTime[$memberId];
			}
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


			$calculateCustomerTotalProcessedOrders=	0;
			if(!empty($a_customerTotalProcessedOrders) && count($a_customerTotalProcessedOrders) && array_key_exists($memberId,$a_customerTotalProcessedOrders)){
				$calculateCustomerTotalProcessedOrders   =   $a_customerTotalProcessedOrders[$memberId];
			}
			
			$calculateCustomerTotalProcessedOrdersTime=	0;
			if(!empty($a_customerTotalProcessedOrdersTime) && count($a_customerTotalProcessedOrdersTime) && array_key_exists($memberId,$a_customerTotalProcessedOrdersTime)){
				$calculateCustomerTotalProcessedOrdersTime   =   $a_customerTotalProcessedOrdersTime[$memberId];
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
			
			$customerTotalQaOrders    =	0;
			if(!empty($a_customerTotalQaOrders) && count($a_customerTotalQaOrders) && array_key_exists($memberId,$a_customerTotalQaOrders)){
				$customerTotalQaOrders   =   $a_customerTotalQaOrders[$memberId];
			}
					
			$customerTotalQaOrdersTime=	0;
			if(!empty($a_customerTotalQaOrdersTime) && count($a_customerTotalQaOrdersTime) && array_key_exists($memberId,$a_customerTotalQaOrdersTime)){
				$customerTotalQaOrdersTime   =   $a_customerTotalQaOrdersTime[$memberId];
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



			$calculateCustomerTotalQaOrders=	0;

			if(!empty($a_calculateCustomerTotalQaOrders) && count($a_calculateCustomerTotalQaOrders) && array_key_exists($memberId,$a_calculateCustomerTotalQaOrders)){
				$calculateCustomerTotalQaOrders   =   $a_calculateCustomerTotalQaOrders[$memberId];
			}
			
			$calculateCustomerTotalQaOrdersTime=	0;

			if(!empty($a_calculateCustomerTotalQaOrdersTime) && count($a_calculateCustomerTotalQaOrdersTime) && array_key_exists($memberId,$a_calculateCustomerTotalQaOrdersTime)){
				$calculateCustomerTotalQaOrdersTime   =   $a_calculateCustomerTotalQaOrdersTime[$memberId];
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
		<td class='smalltext2' valign='top'>
			<?php
				$url			=	SITE_URL_EMPLOYEES."/display-customer-average-rating.php?averageOrderFor=$averageOrderFor&memberId=";
			?>
			<a onclick="commonFunc('<?php echo $url;?>','showCustomerOrderProcess<?php echo $memberId;?>',<?php echo $memberId;?>);removeEmployees(<?php echo $memberId;?>,1);" class='link_style2' style="cursor:pointer">
				<?php echo $memberName;?>
			</a>
		</td>
		<td class='smalltext2' valign='top'><?php echo $memberTotalOrders;?></td>
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
			<div id="showCustomerOrderProcess<?php echo $memberId;?>"></div>
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
			<b><?php echo $averageOrderFor;?> Days Total/Average For <?php echo $k;?> Customers</b>
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
	<!-- <tr>
		<td colspan="15" class="smalltext2">
			<?php
				$displayUrl		=	SITE_URL_EMPLOYEES."/display-customer-average-total.php?display=1".$displayTotalLink;	
			?>
			<a onclick="commonFunc('<?php echo $displayUrl;?>','showTotal');removeTotal(1);" class="link_style2" style="cursor:pointer"><b>TOTAL AVERAGE/RATINGS </b></a>
		</td>
	</tr> -->
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
<!-- <br>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
	<tr>
		<td>
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/print-customer-total-order-rating-avg.php?print=1<?php echo $queryString;?>" class="link_style8">PRINT CUSTOMER AVEARGE RATING</a>
		</td>
	</tr>
</table> -->
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>