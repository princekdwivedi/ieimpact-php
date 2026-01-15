<?php
	ob_start();
	session_start();
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	
	$platform					=	0;
	$customerId					=	0;
	$employeeId					=	0;
	$a_employeeId				=	array();
	$forDate					=	"";
	$toDate						=	"";
	$displayLineFor				=	0;
	$reportView					=	0;
	$andClause					=	"";
	$text						=	"REV WORKSHEET";

	$totalDirectLevel1			=	0;
	$totalDirectLevel2			=	0;
	$totalIndirectLevel1		=	0;
	$totalIndirectLevel2		=	0;
	$totalQaLevel1				=	0;
	$totalQaLevel2				=	0;
	$totalAuditLevel1			=	0;
	$totalAuditLevel2			=	0;

	
	$directLevel1Rate			=	0;
	$directLevel2Rate			=	0;
	$indirectLevel1Rate			=	0;
	$indirectLevel2Rate			=	0;
	$qaLevel1Rate				=	0;
	$qaLevel2Rate				=	0;
	$auditLevel1Rate			=	0;
	$auditLevel2Rate			=	0;

	$t_directLevel1Rate			=	0;
	$t_directLevel2Rate			=	0;
	$t_indirectLevel1Rate		=	0;
	$t_indirectLevel2Rate		=	0;
	$t_qaLevel1Rate				=	0;
	$t_qaLevel2Rate				=	0;
	$t_auditLevel1Rate			=	0;
	$t_auditLevel2Rate			=	0;

	$totalLines					=	0;
	$totalMoney					=	0;
	$grandLines					=	0;
	$grandMoney					=	0;

	$reportView					=	1;

	$a_displayLines				=	array();
	$employeeType				=	0;
	$underManager				=	0;
	$a_managers					=	$employeeObj->getAllEmployeeManager();




	if(isset($_GET['platform']))
	{
		$platform		=	$_GET['platform'];
		if(!empty($platform))
		{
			$platName	 =	$employeeObj->getPlatformName($platform);
			$text		.=  " OF ".strtoupper($platName);
			$andClause	.=	" AND platform=$platform";
		}
	}
	if(isset($_GET['customerId']))
	{
		$customerId		=	(int)$_GET['customerId'];
		if(!empty($customerId))
		{
			$customerName	=	$employeeObj->getCustomerName($customerId,$platform);

			$text	   .=  " - CLIENT: ".strtoupper($customerName);
			$andClause .=	" AND customerId=$customerId";
		
		}
	}
	if(isset($_GET['forDate']))
	{
		$forDate		=	$_GET['forDate'];
		if(!empty($forDate))
		{
			$dateText	=	" OF ".showDate($forDate);
			$dateClause	=	" AND workedOnDate='$forDate'";
		}
		if(isset($_GET['toDate']))
		{
			$toDate		=	$_GET['toDate'];
			if(!empty($toDate))
			{
				$dateText	=	" FROM ".showDate($forDate)." TO ".showDate($toDate);
				$dateClause	=	" AND workedOnDate >= '$forDate' AND workedOnDate <= '$toDate'";
			}
			$text		.=  strtoupper($dateText);
			$andClause  .=	$dateClause;
		}
	}
	if(isset($_GET['employeeType']))
	{
		$employeeType		=	$_GET['employeeType'];
		if(!empty($employeeType))
		{
			$andClause	   .=	" AND employee_details.employeeType=$employeeType";
			$text		   .=	" for ".$a_inetExtEmployee[$employeeType]." employees";
		}
	}
	if(isset($_GET['underManager']))
	{
		$underManager		=	$_GET['underManager'];
		if(!empty($underManager))
		{
			$andClause	   .=	" AND employee_details.underManager=$underManager";
			$text		   .=	" under manager ".$a_managers[$underManager];
		}
	}
	if(isset($_GET['employee']))
	{
		$employeeId	=	$_GET['employee'];
		if(!empty($employeeId))
		{
			$a_employeeId		=	explode(",",$employeeId);
			$andClause		   .=	" AND datewise_employee_works_money.employeeId IN ($employeeId)";

			$totalEmloyee		=	count($a_employeeId);

			if($totalEmloyee < 2 && $totalEmloyee > 0)
			{
				foreach($a_employeeId as $key=>$value)
				{
					$employeeName	=	$employeeObj->getEmployeeName($value);
				}
				$text			.=	" FOR EMPLOYEE ".$employeeName;
			}
			else
			{
				$text			.=	" FOR MULTILE EMPLOYES";
			}
		}
	}
	
	if(isset($_GET['displayLines']))
	{
		$a_displayLines		=	$_GET['displayLines'];
		if(!empty($a_displayLines))
		{
			$a_displayLines	=	explode(",",$a_displayLines);
		}
	}

	if(isset($_GET['reportView']))
	{
		$reportView		=	$_GET['reportView'];
	}

	require_once(SITE_ROOT.'/classes/Worksheet.php');
	require_once(SITE_ROOT.'/classes/Workbook.php');

	function HeaderingExcel($filename)
	{
      header("Content-type: application/vnd.ms-excel");
      header("Content-Disposition: attachment; filename=$filename" );
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
      header("Pragma: public");
   }

  // HTTP headers
  HeaderingExcel('rev-worksheet.xls');

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

$worksheet1->set_column(0,0,20);
$worksheet1->set_column(1,1,20);
$worksheet1->set_column(2,2,20);
$worksheet1->set_column(3,3,20);
$worksheet1->set_column(4,4,15);
$worksheet1->set_column(5,5,15);
$worksheet1->set_column(6,6,28);
$worksheet1->set_column(7,7,28);
$worksheet1->set_column(8,9,28);
$worksheet1->set_column(9,9,15);
$worksheet1->set_column(10,10,13);
$worksheet1->set_column(11,11,13);
$worksheet1->set_column(12,13,13);
$worksheet1->set_column(13,13,13);
$worksheet1->set_column(14,14,13);
$worksheet1->set_column(15,15,13);
$worksheet1->set_column(16,16,17);
$worksheet1->set_column(17,17,17);
$worksheet1->set_column(18,18,17);
 
 
  $worksheet1->write_string(0,0,"EMPLOYEE NAME",$formatot);

  $worksheet1->write_string(0,1,"ALL TOTAL LINES",$formatot);
  $worksheet1->write_string(0,2,"ALL TOTAL MONEY",$formatot);

  $worksheet1->write_string(0,3,"DIRECT LEVEL1",$formatot);
  $worksheet1->write_string(0,4,"MONEY",$formatot);

  $worksheet1->write_string(0,5,"DIRECT LEVEL2",$formatot);
  $worksheet1->write_string(0,6,"MONEY",$formatot);

  $worksheet1->write_string(0,7,"INDIRECT LEVEL1",$formatot);
  $worksheet1->write_string(0,8,"MONEY",$formatot);

  $worksheet1->write_string(0,9,"INDIRECT LEVEL2",$formatot);
  $worksheet1->write_string(0,10,"MONEY",$formatot);

  $worksheet1->write_string(0,11,"QA LEVEL1",$formatot);
  $worksheet1->write_string(0,12,"MONEY",$formatot);

  $worksheet1->write_string(0,13,"QA LEVEL2",$formatot);
  $worksheet1->write_string(0,14,"MONEY",$formatot);

  $worksheet1->write_string(0,15,"AUDIT LEVEL1",$formatot);
  $worksheet1->write_string(0,16,"MONEY",$formatot);

  $worksheet1->write_string(0,17,"AUDIT LEVEL2",$formatot);
  $worksheet1->write_string(0,18,"MONEY",$formatot);
  
  function cleanData(&$str)
  { 
	$str = preg_replace("/\t/", "\\t", $str);
	$str = preg_replace("/\r?\n/", "\\n", $str);

	return $str;
  } 

    $query	=	"SELECT datewise_employee_works_money.employeeId,SUM(totalDirectLevel1Lines) AS totalDirectLevel1Lines,SUM(totalDirectLevel1Money) AS totalDirectLevel1Money,SUM(totalDirectLevel2Lines) AS totalDirectLevel2Lines,SUM(totalDirectLevel2Money) AS totalDirectLevel2Money,SUM(totalIndirectLevel1Lines) AS totalIndirectLevel1Lines,SUM(totalIndirectLevel1Money) AS totalIndirectLevel1Money,SUM(totalIndirectLevel2Lines) AS totalIndirectLevel2Lines,SUM(totalIndirectLevel2Money) AS totalIndirectLevel2Money,SUM(totalQaLevel1Lines) AS totalQaLevel1Lines,SUM(totalQaLevel1Money) AS totalQaLevel1Money,SUM(totalQaLevel2Lines) AS totalQaLevel2Lines,SUM(totalQaLevel2Money) AS totalQaLevel2Money,SUM(totalAuditLevel1Lines) AS totalAuditLevel1Lines,SUM(totalAuditLevel1Money) AS totalAuditLevel1Money,SUM(totalAuditLevel2Lines) AS totalAuditLevel2Lines,SUM(totalAuditLevel2Money) AS totalAuditLevel2Money,firstName,lastName FROM datewise_employee_works_money INNER JOIN employee_details ON datewise_employee_works_money.employeeId=employee_details.employeeId WHERE employee_details.isActive=1 AND departmentId=2".$andClause." GROUP BY datewise_employee_works_money.employeeId";
	$result	=	dbQuery($query);
	if(mysql_num_rows($result))
	{
		$i			=	0;
		while($row	=	mysql_fetch_assoc($result))
	    {
			$i++;
			$employeeId					=	$row['employeeId'];
			$totalDirectLevel1Lines		=	$row['totalDirectLevel1Lines'];
			$totalDirectLevel2Lines		=	$row['totalDirectLevel2Lines'];
			$totalIndirectLevel1Lines	=	$row['totalIndirectLevel1Lines'];
			$totalIndirectLevel2Lines	=	$row['totalIndirectLevel2Lines'];
			$totalQaLevel1Lines			=	$row['totalQaLevel1Lines'];
			$totalQaLevel2Lines			=	$row['totalQaLevel2Lines'];
			$totalAuditLevel1Lines		=	$row['totalAuditLevel1Lines'];
			$totalAuditLevel2Lines		=	$row['totalAuditLevel2Lines'];

			$totalDirectLevel1Money		=	$row['totalDirectLevel1Money'];
			$totalDirectLevel2Money		=	$row['totalDirectLevel2Money'];
			$totalIndirectLevel1Money	=	$row['totalIndirectLevel1Money'];
			$totalIndirectLevel2Money	=	$row['totalIndirectLevel2Money'];
			$totalQaLevel1Money			=	$row['totalQaLevel1Money'];
			$totalQaLevel2Money			=	$row['totalQaLevel2Money'];
			$totalAuditLevel1Money		=	$row['totalAuditLevel1Money'];
			$totalAuditLevel2Money		=	$row['totalAuditLevel2Money'];

			$firstName					=	$row['firstName'];
			$lastName					=	$row['lastName'];

			$employeeName				=	$firstName." ".$lastName;
			$employeeName				=	ucwords($employeeName);
			
			$totalLines		=	$totalDirectLevel1Lines+$totalDirectLevel2Lines+$totalIndirectLevel1Lines+$totalIndirectLevel2Lines+$totalQaLevel1Lines+$totalQaLevel2Lines+$totalAuditLevel1Lines+$totalAuditLevel2Lines;

			$totalMoney		=	$totalDirectLevel1Money+$totalDirectLevel2Money+$totalIndirectLevel1Money+$totalIndirectLevel2Money+$totalQaLevel1Money+$totalQaLevel2Money+$totalAuditLevel1Money+$totalAuditLevel2Money;
			
			$totalLines		=	round($totalLines);
			$totalMoney		=	round($totalMoney);

			$grandLines		=	$grandLines+$totalLines;
			$grandTotal		=	$grandTotal+$totalMoney;
			
			$worksheet1->write($i,0,$employeeName);

			$worksheet1->write($i,1,$totalLines);
			$worksheet1->write($i,2,$totalMoney);

			$worksheet1->write($i,3,$totalDirectLevel1Lines);
			$worksheet1->write($i,4,$totalDirectLevel1Money);

			$worksheet1->write($i,5,$totalDirectLevel2Lines);
			$worksheet1->write($i,6,$totalDirectLevel2Money);
			
			$worksheet1->write($i,7,$totalIndirectLevel1Lines);
			$worksheet1->write($i,8,$totalIndirectLevel1Money);

			$worksheet1->write($i,9,$totalIndirectLevel2Lines);
			$worksheet1->write($i,10,$totalIndirectLevel2Money);
			
			$worksheet1->write($i,11,$totalQaLevel1Lines);
			$worksheet1->write($i,12,$totalQaLevel1Money);

			$worksheet1->write($i,13,$totalQaLevel2Lines);
			$worksheet1->write($i,14,$totalQaLevel2Money);

			$worksheet1->write($i,15,$totalAuditLevel1Lines);
			$worksheet1->write($i,16,$totalAuditLevel1Money);

			$worksheet1->write($i,17,$totalAuditLevel2Lines);
			$worksheet1->write($i,18,$totalAuditLevel2Money);
						
			$totalLines				=	0;
			$totalMoney				=	0;
		}
		$grandLines	=	round($grandLines,2);
		$grandTotal	=	round($grandTotal,2);

		$k	=	$i+1;

		$worksheet1->write_string($k,0,""); 

		$line	=	$i+2;

		$worksheet1->write($line,0,"GRAND TOTAL");
		$worksheet1->write($line,1,$grandLines);
		$worksheet1->write($line,2,$grandTotal);
  }
  $workbook->close();
?>