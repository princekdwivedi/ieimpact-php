<?php
	ob_start();
	session_start();
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	$orderObj					=  new orders();
	$employeeObj				=	new employee();
	require_once(SITE_ROOT.'/classes/Worksheet.php');
	require_once(SITE_ROOT.'/classes/Workbook.php');
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$a_serachingOrdersBy		=	array("1"=>"From less orders to more|totalEmployeeOrders","2"=>"From more orders to less|totalEmployeeOrders DESC","3"=>"By new employees|employee_details.addedOn DESC","4"=>"By old employees|employee_details.addedOn");

	$whereClause				=	"WHERE isActive=1 AND acceptedBy <> 0";
	$andClause					=	"";
	$orderBy					=	"totalEmployeeOrders ";
	$averageOrderFor			=	15;
	$employeeName				=	"";
	$andClause					=	"";
	
	if(isset($_GET['employeeName']))
	{
		$employeeName			=	$_GET['employeeName'];
		if(!empty($employeeName))
		{
			$andClause			.=	" AND fullName LIKE '%$employeeName%'";
		}
	}
	
	if(isset($_GET['averageOrderFor']))
	{
		$averageOrderFor				=	$_GET['averageOrderFor'];
	}
	if(isset($_GET['serachOrderBy']))
	{
		$serachOrderBy					=	$_GET['serachOrderBy'];
		if(!empty($serachOrderBy))
		{
			$serachValue	=	$a_serachingOrdersBy[$serachOrderBy];

			list($serachByText,$orderBy)=	explode("|",$serachValue);
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

	function HeaderingExcel($filename)
	{
      header("Content-type: application/vnd.ms-excel");
      header("Content-Disposition: attachment; filename=$filename" );
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
      header("Pragma: public");
   }

  // HTTP headers
  HeaderingExcel('view-customer-average-ratings.xls');

  // Creating a workbook
  $workbook = new Workbook("-");
 // // Creating the first worksheet
  $worksheet1 =& $workbook->add_worksheet('First One');
 $worksheet1->set_column(1, 1, 40);
 $worksheet1->set_row(1, 20);
  //$worksheet1->write_string(1, 1, "This worksheet's name is ".$worksheet1->get_name());
  //$worksheet1->write(2,1,"http://www.phpclasses.org/browse.html/package/767.html");
 // $worksheet1->write_number(3, 0, 11);
// $worksheet1->write_number(3, 1, 1);
  //$worksheet1->write_string(3, 2, "by four is");
 // $worksheet1->write_formula(3, 3, "=A4 * (2 + 2)");
 // $worksheet1->write_formula(3, 3, "= SUM(A4:B4)");
 //$worksheet1->write(5, 4, "= POWER(2,3)");
 //$worksheet1->write(4, 4, "= SUM(5, 5, 5)");
 // $worksheet1->write_formula(4, 4, "= LN(2.71428)");
 // $worksheet1->write_formula(5, 4, "= SIN(PI()/2)");

  // Creating the second worksheet
  $worksheet2 =& $workbook->add_worksheet();

  // Format for the headings
  $formatot =& $workbook->add_format();
  $formatot->set_size(10);
  $formatot->set_align('center');
  $formatot->set_color('white');
  $formatot->set_pattern();
  $formatot->set_fg_color('black');

  $worksheet1->set_column(0,0,10);
  $worksheet1->set_column(1,2,20);
  $worksheet1->set_column(3,3,15);
  $worksheet1->set_column(4,4,15);
  $worksheet1->set_column(5,5,28);
  $worksheet1->set_column(6,6,28);
  $worksheet1->set_column(7,7,28);
  $worksheet1->set_column(8,8,25);
  $worksheet1->set_column(9,9,25);
  $worksheet1->set_column(10,10,25);
  $worksheet1->set_column(11,11,25);
  $worksheet1->set_column(12,12,25);
 
  $worksheet1->write_string(0,0,"CUSTOMER ID",$formatot);
  $worksheet1->write_string(0,1,"CUSTOMER NAME",$formatot);
  $worksheet1->write_string(0,2,"Total Orders",$formatot);
  $worksheet1->write_string(0,3,"Orders In Last ".$averageOrderFor." Days",$formatot);
  $worksheet1->write_string(0,4,"Total Rated Orders",$formatot);
  $worksheet1->write_string(0,5,"Average rate",$formatot);
  $worksheet1->write_string(0,6,"Ratings In Last ".$averageOrderFor." Days",$formatot);
  $worksheet1->write_string(0,7,"Average Process Time",$formatot);
  $worksheet1->write_string(0,8,"Average Process Time Last ".$averageOrderFor." Days",$formatot);
  $worksheet1->write_string(0,9,"Average QA Time",$formatot);
  $worksheet1->write_string(0,10,"Average. QA Time Last ".$averageOrderFor." Days",$formatot);
  $worksheet1->write_string(0,11,"Ave. Process+QA Time",$formatot);
  $worksheet1->write_string(0,12,"Ave. Process+QA Time Last ".$averageOrderFor." Day",$formatot);
 
  function cleanData(&$str)
  { 
	$str = preg_replace("/\t/", "\\t", $str);
	$str = preg_replace("/\r?\n/", "\\n", $str);

	return $str;
  } 
	
 $query	=	"SELECT employee_details.employeeId,fullName,COUNT(orderId) AS totalEmployeeOrders FROM employee_details INNER JOIN members_orders ON employee_details.employeeId=members_orders.acceptedBy ".$whereClause.$andClause." GROUP BY employee_details.employeeId ORDER BY ".$orderBy;
 
  $result	=	dbQuery($query);
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
		
		$employeeId				=	$row['employeeId'];
		$fullName				=	stripslashes($row['fullName']);
		$totalEmployeeOrders	=	$row['totalEmployeeOrders'];

		$totalOrders			=	$totalOrders+$totalEmployeeOrders;

		$employeesAcceptedOrder	=	$orderObj->getEmployeesOwnProcessedOrder($employeeId);

		$lastCalculateDaysOrders=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders WHERE orderAddedOn >= '$calculateShowingOrderFrom' AND acceptedBy=$employeeId"),0);

		if(empty($lastCalculateDaysOrders))
		{
			$lastCalculateDaysOrders	=	0;
		}
		else
		{
			$totalOrdersLastCalculate	=	$totalOrdersLastCalculate+$lastCalculateDaysOrders;
		}

		$memberTotalratedOrders		=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders WHERE rateGiven <> 0 AND isRateCountingEmployeeSide='yes' AND acceptedBy=$employeeId"),0);

		if(empty($memberTotalratedOrders))
		{
			$memberTotalratedOrders	=	"N/A";
		}
		else
		{
			$totalRatedOrders		=	$totalRatedOrders+$memberTotalratedOrders;
		}

		$memberTotalRatesSum		=	@mysql_result(dbQuery("SELECT SUM(rateGiven) FROM members_orders WHERE rateGiven <> 0 AND isRateCountingEmployeeSide='yes'  AND acceptedBy=$employeeId"),0);

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
			$averageRate			=	"N/A";
		}

		$lastCalculateTotalratedOrders		=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders WHERE rateGiven <> 0 AND isRateCountingEmployeeSide='yes' AND orderAddedOn >= '$calculateShowingOrderFrom' AND acceptedBy=$employeeId"),0);

		if(empty($lastCalculateTotalratedOrders))
		{
			$lastCalculateTotalratedOrders	=	"N/A";
		}
		else
		{
			$totalRateOrdersLastCalculate	=	$totalRateOrdersLastCalculate+$lastCalculateTotalratedOrders;
		}

		$customerTotalProcessedOrders	=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders_reply WHERE orderId IN ($employeesAcceptedOrder)"),0);
		if(empty($customerTotalProcessedOrders))
		{
			$customerTotalProcessedOrders=	0;
		}
		
		$customerTotalProcessedOrdersTime	=	@mysql_result(dbQuery("SELECT SUM(timeSpentEmployee) FROM members_orders_reply WHERE orderId IN ($employeesAcceptedOrder)"),0);
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
			$t_averageProcessTime	=	"N/A";
			$averageProcessTime		=	0;
		}



		$calculateCustomerTotalProcessedOrders	  =	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders_reply WHERE orderId IN ($employeesAcceptedOrder) AND replyFileAddedOn  >= '$calculateShowingOrderFrom'"),0);
		if(empty($calculateCustomerTotalProcessedOrders))
		{
			$calculateCustomerTotalProcessedOrders=	0;
		}
		
		$calculateCustomerTotalProcessedOrdersTime	=	@mysql_result(dbQuery("SELECT SUM(timeSpentEmployee) FROM members_orders_reply WHERE orderId IN ($employeesAcceptedOrder) AND replyFileAddedOn  >= '$calculateShowingOrderFrom'"),0);
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
			$t_calculateAverageProcessTime	=	"N/A";
			$calculateAverageProcessTime	=	0;
		}



		$customerTotalQaOrders	=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders_reply WHERE orderId IN ($employeesAcceptedOrder) AND hasQaDone=1"),0);
		if(empty($customerTotalQaOrders))
		{
			$customerTotalQaOrders=	0;
		}
				
		$customerTotalQaOrdersTime	=	@mysql_result(dbQuery("SELECT SUM(timeSpentQa) FROM members_orders_reply WHERE orderId IN ($employeesAcceptedOrder) AND hasQaDone=1"),0);
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
			$t_averageQaTime	=	"N/A";
			$averageQaTime		=	0;
		}



		$calculateCustomerTotalQaOrders	  =	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders_reply WHERE orderId IN ($employeesAcceptedOrder) AND replyFileAddedOn  >= '$calculateShowingOrderFrom' AND hasQaDone=1"),0);
		if(empty($calculateCustomerTotalQaOrders))
		{
			$calculateCustomerTotalQaOrders=	0;
		}
		
		$calculateCustomerTotalQaOrdersTime	=	@mysql_result(dbQuery("SELECT SUM(timeSpentQa) FROM members_orders_reply WHERE orderId IN ($employeesAcceptedOrder) AND replyFileAddedOn  >= '$calculateShowingOrderFrom' AND hasQaDone=1"),0);
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
			$t_calculateAverageQaTime	=	"N/A";
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



		$worksheet1->write($i,0,$employeeId);
		$worksheet1->write($i,1,$fullName);
		$worksheet1->write($i,2,$totalEmployeeOrders);
		$worksheet1->write($i,3,$lastCalculateDaysOrders);
		$worksheet1->write($i,4,$memberTotalratedOrders);
		$worksheet1->write($i,5,$averageRate);
		$worksheet1->write($i,6,$lastCalculateTotalratedOrders);
		$worksheet1->write($i,7,$t_averageProcessTime);
		$worksheet1->write($i,8,$t_calculateAverageProcessTime);

		$worksheet1->write($i,9,$t_averageQaTime);
		$worksheet1->write($i,10,$t_calculateAverageQaTime);
		
		if(empty($totalAverageProcesQaTime))
		{
			$displayLast1	=  "N/A";
		}
		else
		{
			$displayLast1	=  getHours($totalAverageProcesQaTime);
		}

		$worksheet1->write($i,11,$displayLast1);
		if(empty($totalAverageProcesQaTimeForLast))
		{
			$displayLast2	=  "N/A";
		}
		else
		{
			$displayLast2	= getHours($totalAverageProcesQaTimeForLast);
		}
		$worksheet1->write($i,12,$displayLast2);
	 }

	 $m	=	$i+1;

	 $worksheet1->write_string($m,0,""); 
 
	 $line	=	$i+2;

	$worksheet1->write($line,1,"AVERAGE/RATINGS");
	$worksheet1->write($line,2,$totalOrders);
	$worksheet1->write($line,3,$totalOrdersLastCalculate);
	$worksheet1->write($line,4,$totalRatedOrders);
	if(!empty($totalAvgRate))
	{
		$displayTotalAvg	=	$totalAvgRate/$k1;

		$showDisplayTotalAvg=  round($displayTotalAvg,2);
	}
	else
	{
		$showDisplayTotalAvg= "N/A";
	}
	$worksheet1->write($line,5,$showDisplayTotalAvg);
	$worksheet1->write($line,6,$totalRateOrdersLastCalculate);
	if(!empty($totalAvgProcessTime))
	{
		$displayTotalAvgProcees		=	$totalAvgProcessTime/$k2;
		
		$showDisplayTotalAvgProcees	=	getHours($displayTotalAvgProcees);
	}
	else
	{
		$showDisplayTotalAvgProcees	=	"N/A";
	}
	$worksheet1->write($line,7,$showDisplayTotalAvgProcees);
	if(!empty($totalAvgProcessTimeLastCalculate))
	{
		$displayTotalCalculateAvgProcees	=	$totalAvgProcessTimeLastCalculate/$k3;

		$showDisplayTotalCalculateAvgProcees=	 getHours($displayTotalCalculateAvgProcees);
	}
	else
	{
		$showDisplayTotalCalculateAvgProcees= "N/A";
	}
	$worksheet1->write($line,8,$showDisplayTotalCalculateAvgProcees);
	if(!empty($totalAvgQaTime))
	{
		$displayTotalQaAvg		=	$totalAvgQaTime/$k4;

		$showDisplayTotalQaAvg	=	getHours($displayTotalQaAvg);
	}
	else
	{
		$showDisplayTotalQaAvg	= "N/A";
	}
	$worksheet1->write($line,9,$showDisplayTotalQaAvg);
	if(!empty($totalAvgQaTimeLastCalculate))
	{
		$displayTotallastCalculateQaAvg		=	$totalAvgQaTimeLastCalculate/$k5;

		$showDisplayTotallastCalculateQaAvg	= getHours($displayTotallastCalculateQaAvg);
	}
	else
	{
		$showDisplayTotallastCalculateQaAvg = "N/A";
	}
	$worksheet1->write($line,10,$showDisplayTotallastCalculateQaAvg);
	if(!empty($totalAvgProcessQaTime))
	{
		$displayTotalProcessQaAvg		=	$totalAvgProcessQaTime/$k6;

		$showDisplayTotalProcessQaAvg	=	getHours($displayTotalProcessQaAvg);
	}
	else
	{
		$showDisplayTotalProcessQaAvg	=	 "N/A";
	}

	$worksheet1->write($line,11,$showDisplayTotalProcessQaAvg);
	if(!empty($totalAvgProcessQaTimeLastCalculate))
	{
		$displayTotalLastProcessQaAvg	 =	$totalAvgProcessQaTimeLastCalculate/$k7;

		$showDisplayTotalLastProcessQaAvg=	 getHours($displayTotalLastProcessQaAvg);
	}
	else
	{
		$showDisplayTotalLastProcessQaAvg= "N/A";
	}
	$worksheet1->write($line,12,$showDisplayTotalLastProcessQaAvg);
 }

  $workbook->close();
?>
