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
	$employeeObj				=  new employee();
	require_once(SITE_ROOT.'/classes/Worksheet.php');
	require_once(SITE_ROOT.'/classes/Workbook.php');
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$searchBy					=	0;
	$t_searchDate				=	"";
	$searchMonth				=	"";
	$searchYear					=	"";
	$employeeId					=	0;
	$andClause					=	"";
	$andClause1					=	"";
	$text						=	"Employee Order Processing";
	$employeeName				=	"";

	$totalAccepted				=	0;
	$totalProcessed				=	0;
	$totalQaDone				=	0;
	$totalRemainingProcessing	=	0;


	$toatlQAAccepted			=	0;
	$totalQAOrderProcessed		=	0;
	$totalQAOrderDone			=	0;
	$totalRemainingQAProcessing	=	0;

	//pr($_REQUEST);

	if(isset($_GET['employeeId']))
	{
		$employeeId				=	$_GET['employeeId'];
		$employeeName			=	$employeeObj->getEmployeeName($employeeId);
	}

	if(isset($_GET['searchBy']))
	{
		$searchBy				=	$_GET['searchBy'];

		if($searchBy			==	1)
		{
			if(isset($_GET['searchDate']))
			{
				$t_searchDate	=	$_GET['searchDate'];

				$andClause		=	" AND assignToEmployee='$t_searchDate'";
				$andClause1		=	" AND isQaAccepted=1 AND qaAcceptedDate='$t_searchDate'";
				$text		   .=	" ON ".showDate($t_searchDate);
			}
		}
		else
		{
			if(isset($_GET['searchMonth']))
			{
				$searchMonth	=	$_GET['searchMonth'];
			}
			if(isset($_GET['searchYear']))
			{
				$searchYear		=	$_GET['searchYear'];
			}
			if(!empty($searchYear) && !empty($searchYear))
			{
				$andClause		=	" AND MONTH(assignToEmployee)=$searchMonth AND YEAR(assignToEmployee)=$searchYear";

				$andClause1		=	" AND isQaAccepted=1 AND MONTH(qaAcceptedDate)=$searchMonth AND YEAR(qaAcceptedDate)=$searchYear";

				$text		   .=	" ON ".$a_month[$searchMonth].",".$searchYear;
			}
		}
	}

	function HeaderingExcel($filename)
	{
      header("Content-type: application/vnd.ms-excel");
      header("Content-Disposition: attachment; filename=$filename" );
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
      header("Pragma: public");
   }

  // HTTP headers
  HeaderingExcel('view-employee-order-processing.xls');

  //Creating a workbook
  $workbook = new Workbook("-");
  // // Creating the first worksheet
  $worksheet1 =& $workbook->add_worksheet('First One');
  $worksheet1->set_column(1, 1, 40);
  $worksheet1->set_row(1, 20);
  // Creating the second worksheet
  $worksheet2 =& $workbook->add_worksheet();

  // Format for the headings
  $formatot =& $workbook->add_format();
  $formatot->set_size(10);
  $formatot->set_align('center');
  $formatot->set_color('white');
  $formatot->set_pattern();
  $formatot->set_fg_color('black');

  $worksheet1->set_column(0,0,20);
  $worksheet1->set_column(1,2,20);
  $worksheet1->set_column(3,3,15);
  $worksheet1->set_column(4,4,15);
  $worksheet1->set_column(5,5,28);
  $worksheet1->set_column(6,6,28);
  
   $worksheet1->write_string(0,0,"SR NO",$formatot);
  $worksheet1->write_string(0,1,"EMPLOYEE NAME",$formatot);
  $worksheet1->write_string(0,2,"TOTAL ORDER ASSIGNED",$formatot);
  $worksheet1->write_string(0,3,"TOTAL ORDER PROCESSED",$formatot);
  $worksheet1->write_string(0,4,"TOTAL QA ACCEPTED",$formatot);
  $worksheet1->write_string(0,5,"Total QA Processed",$formatot);
 
  function cleanData(&$str)
  { 
	$str = preg_replace("/\t/", "\\t", $str);
	$str = preg_replace("/\r?\n/", "\\n", $str);

	return $str;
  } 
  $i			=	0;
  $x			=	0;
  $query		=	"SELECT employeeId,fullName FROM employee_details WHERE isActive=1 AND hasPdfAccess=1 ORDER BY fullName";
  $result		=	dbQuery($query);
  if(mysqli_num_rows($result))
  {
  
		while($row					=	mysqli_fetch_assoc($result))
		{
			$x++;
			$employeeId				=	$row['employeeId'];
			$employeeName			=	$row['fullName'];

			$totalEmployeeOrders	=	0;
			$totalEmployeeQaOrders	=	0;
			
			$totalEmployeeOrders	=	$employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total FROM members_orders WHERE acceptedBy=$employeeId".$andClause,"total");

			if(empty($totalEmployeeOrders))
			{
				$totalEmployeeOrders=	0;
			}
								
			$totalProcessedOrderIds	=	$orderObj->totalPocessEdOrderIds($employeeId,$andClause);

			if(!empty($totalProcessedOrderIds))
			{
				$totalEmployeeProcessed	=	$employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total FROM members_orders_reply WHERE orderId IN ($totalProcessedOrderIds) AND hasReplyOrderFile=1","total");
				if(!empty($totalEmployeeProcessed))
				{
					$totalProcessed	=	$totalProcessed+$totalEmployeeProcessed;
				}
			}
			else
			{
				$totalEmployeeProcessed	=	0;
			}

			$totalEmployeeOrders		=	$totalEmployeeOrders-$totalEmployeeProcessed;
			if(empty($totalEmployeeOrders))
			{
				$totalEmployeeOrders	=	0;
			}
			else
			{
				$totalAccepted			=	$totalAccepted+$totalEmployeeOrders;
			}


			$totalEmployeeQaOrders	=	$employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total FROM members_orders_reply WHERE isQaAccepted=1 AND qaAcceptedBy=$employeeId".$andClause1,"total");

			if(empty($totalEmployeeQaOrders))
			{
				$totalEmployeeQaOrders=	0;
			}
			
			$totalEmployeeQACompleted		=	$employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total FROM members_orders_reply WHERE isQaAccepted=1 AND qaAcceptedBy=$employeeId AND hasQaDone=1".$andClause1,"total");

			if(empty($totalEmployeeQACompleted))
			{
				$totalEmployeeQACompleted	=	0;
			}
			else
			{
				$totalQAOrderDone	=	$totalQAOrderDone+$totalEmployeeQACompleted;
			}

			$totalEmployeeQaOrders	=	$totalEmployeeQaOrders-$totalEmployeeQACompleted;
			if(empty($totalEmployeeQaOrders))
			{
				$totalEmployeeQaOrders=	0;
			}
			else
			{
				$toatlQAAccepted	  =	$toatlQAAccepted+$totalEmployeeQaOrders;
			}


			if(!empty($totalEmployeeOrders) || !empty($totalEmployeeProcessed) || !empty($totalEmployeeQaOrders) || !empty($totalEmployeeQACompleted))
			{
				$i++;
				$worksheet1->write($i,0,$x);
				$worksheet1->write($i,1,$employeeName);
				
				$worksheet1->write($i,2,$totalEmployeeOrders);
				
				$worksheet1->write($i,3,$totalEmployeeProcessed);
				
				$worksheet1->write($i,4,$totalEmployeeQaOrders);
				
				$worksheet1->write($i,5,$totalEmployeeQACompleted);
			}
		}

		 $m	=	$i+1;
		 $worksheet1->write_string($m,0,""); 
	 
		 $line	=	$i+2;

		$worksheet1->write($line,1,"TOTAL");
		$worksheet1->write($line,2,$totalAccepted);
		$worksheet1->write($line,3,$totalProcessed);
		$worksheet1->write($line,4,$toatlQAAccepted);
		$worksheet1->write($line,5,$totalQAOrderDone);
}
$workbook->close();
?>