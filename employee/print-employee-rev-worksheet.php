<?php
	session_start();
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	$employeeObj				=	new employee();

	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");

	$text				=	"";

	$totalDirectLevel1	=	0;
	$totalDirectLevel2	=	0;
	$totalIndirectLevel1=	0;
	$totalIndirectLevel2=	0;
	$totalQaLevel1		=	0;
	$totalQaLevel2		=	0;
	$totalAuditLevel1	=	0;
	$totalAuditLevel2	=	0;

	$grandTotalDirect	=	0;
	$grandTotalIndirect	=	0;
	$grandTotalQa		=	0;
	$grandTotalAudit	=	0;

	$grandTotalDirectMoney		=	0;
	$grandTotalIndirectMoney	=	0;
	$grandTotalQaMoney			=	0;
	$grandTotalAuditMoney		=	0;

	$grandMoney			=	0;
	$grandLines			=	0;

	$month				=	"";
	$year				=	"";

	if(isset($_GET['month']) && isset($_GET['year']))
	{
		$month			=	$_GET['month'];
		$year			=	$_GET['year'];
	

		$monthText		=	$a_month[$month];
		$text			=	$s_employeeName." WORK SHEET ON - ".$monthText.",".$year;
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
  HeaderingExcel('monthly-worksheet.xls');

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
  $worksheet1->set_column(1,2,20);
  $worksheet1->set_column(3,3,15);
  $worksheet1->set_column(4,4,15);
  $worksheet1->set_column(5,5,20);
  $worksheet1->set_column(6,6,20);
  $worksheet1->set_column(7,7,20);
  $worksheet1->set_column(8,8,20);
  $worksheet1->set_column(9,9,20);
  $worksheet1->set_column(10,10,20);
  $worksheet1->set_column(11,11,20);
  $worksheet1->set_column(12,12,20);
  $worksheet1->set_column(13,13,20);
  $worksheet1->set_column(14,14,20);
  $worksheet1->set_column(15,15,20);
  $worksheet1->set_column(16,16,20);
  $worksheet1->set_column(17,17,20);
  $worksheet1->set_column(18,18,20);
  $worksheet1->set_column(19,19,20);
  $worksheet1->set_column(20,20,20);
  $worksheet1->set_column(21,21,20);
  $worksheet1->set_column(22,22,20);
  $worksheet1->set_column(23,23,20);
  $worksheet1->set_column(24,24,20);
  $worksheet1->set_column(25,25,20);
  $worksheet1->set_column(26,26,20);
 
 
  //$worksheet1->write_string(0,0,$text); 

  $worksheet1->write_string(0,0,"WORKED ON",$formatot);
  $worksheet1->write_string(0,1,"FILE NAME",$formatot);
  $worksheet1->write_string(0,2,"COMMENTS",$formatot);
  $worksheet1->write_string(0,3,"PLATFORM",$formatot);
  $worksheet1->write_string(0,4,"CLIENT",$formatot);
  $worksheet1->write_string(0,5,"DIRECT-LEVEL1",$formatot);
  $worksheet1->write_string(0,6,"X MONEY PER LINES",$formatot);
  $worksheet1->write_string(0,7,"DIRECT-LEVEL1",$formatot);
  $worksheet1->write_string(0,8,"X MONEY PER LINES",$formatot);
  $worksheet1->write_string(0,9,"DIRECT TOTAL MONEY",$formatot);
  $worksheet1->write_string(0,10,"INDIRECT-LEVEL1",$formatot);
  $worksheet1->write_string(0,11,"X MONEY PER LINES",$formatot);
  $worksheet1->write_string(0,12,"INDIRECT-LEVEL2",$formatot);
  $worksheet1->write_string(0,13,"X MONEY PER LINES",$formatot);
  $worksheet1->write_string(0,14,"INDIRECT TOTAL MONEY",$formatot);
  $worksheet1->write_string(0,15,"QA-LEVEL1",$formatot);
  $worksheet1->write_string(0,16,"X MONEY PER LINES",$formatot);
  $worksheet1->write_string(0,17,"QA-LEVEL2",$formatot);
  $worksheet1->write_string(0,18,"X MONEY PER LINES",$formatot);
  $worksheet1->write_string(0,19,"QA TOTAL MONEY",$formatot);
  $worksheet1->write_string(0,20,"POST AUDIT-LEVEL1",$formatot);
  $worksheet1->write_string(0,21,"X MONEY PER LINES",$formatot);
  $worksheet1->write_string(0,22,"POST AUDIT-LEVEL2",$formatot);
  $worksheet1->write_string(0,23,"X MONEY PER LINES",$formatot);
  $worksheet1->write_string(0,24,"POST AUDIT TOTALMONEY",$formatot);
  $worksheet1->write_string(0,25,"TOTAL LINES",$formatot);
  $worksheet1->write_string(0,26,"TOTAL MONEY",$formatot);
 
  function cleanData(&$str)
  { 
	$str = preg_replace("/\t/", "\\t", $str);
	$str = preg_replace("/\r?\n/", "\\n", $str);

	return $str;
  }
	$query	=	"SELECT * FROM datewise_employee_works_money WHERE MONTH(workedOnDate)=$month AND YEAR(workedOnDate)=$year AND employeeId=$s_employeeId ORDER BY workedOnDate";
	$result	=	mysql_query($query);
	if(mysql_num_rows($result))
	{
		$i				=	0;
		while($row		=	mysql_fetch_assoc($result))
		{
			$i++;
			$platform						=	$row['platform'];
			$customerId						=	$row['customerId'];
			$workId							=	$row['workId'];

			$totalDirectLevel1Lines			=	$row['totalDirectLevel1Lines'];
			$totalDirectLevel2Lines			=	$row['totalDirectLevel2Lines'];
			$totalIndirectLevel1Lines		=	$row['totalIndirectLevel1Lines'];
			$totalIndirectLevel2Lines		=	$row['totalIndirectLevel2Lines'];
			$totalQaLevel1Lines				=	$row['totalQaLevel1Lines'];
			$totalQaLevel2Lines				=	$row['totalQaLevel2Lines'];
			$totalAuditLevel1Lines			=	$row['totalAuditLevel1Lines'];
			$totalAuditLevel2Lines			=	$row['totalAuditLevel2Lines'];

			$directLevel1Rate				=	$row['directLevel1Rate'];
			$directLevel2Rate				=	$row['directLevel2Rate'];
			$indirectLevel1Rate				=	$row['indirectLevel1Rate'];
			$indirectLevel2Rate				=	$row['indirectLevel2Rate'];
			$qaLevel1Rate					=	$row['qaLevel1Rate'];
			$qaLevel2Rate					=	$row['qaLevel2Rate'];
			$auditLevel1Rate				=	$row['auditLevel1Rate'];
			$auditLevel2Rate				=	$row['auditLevel2Rate'];

			$totalDirectLevel1Money			=	$row['totalDirectLevel1Money'];
			$totalDirectLevel2Money			=	$row['totalDirectLevel2Money'];
			$totalIndirectLevel1Money		=	$row['totalIndirectLevel1Money'];
			$totalIndirectLevel2Money		=	$row['totalIndirectLevel2Money'];
			$totalQaLevel1Money				=	$row['totalQaLevel1Money'];
			$totalQaLevel2Money				=	$row['totalQaLevel2Money'];
			$totalAuditLevel1Money			=	$row['totalAuditLevel1Money'];
			$totalAuditLevel2Money			=	$row['totalAuditLevel2Money'];

			$workedOn						=	$row['workedOnDate'];
			$t_workedOn						=	showDate($row['workedOnDate']);
			$comments						=	@mysql_result(dbQuery("SELECT comments FROM employee_works WHERE workId=$workId AND employeeId=$s_employeeId"),0);

			$uploadFileName					=	@mysql_result(dbQuery("SELECT uploadFileName FROM employee_works WHERE workId=$workId AND employeeId=$s_employeeId"),0);
			
			$platName						=	$employeeObj->getPlatformName($platform);
			$customerName					=	$employeeObj->getCustomerName($customerId,$platform);

			$directMoney					=	$totalDirectLevel1Money+$totalDirectLevel2Money;
			$indirectMoney					=	$totalIndirectLevel1Money+$totalIndirectLevel2Money;
			$qaMoney						=	$totalQaLevel1Money+$totalQaLevel2Money;
			$auditMoney						=	$totalAuditLevel1Money+$totalAuditLevel2Money;

			$grandTotalDirectMoney			=	$grandTotalDirectMoney+$directMoney;
			$grandTotalIndirectMoney		=	$grandTotalIndirectMoney+$indirectMoney;
			$grandTotalQaMoney				=	$grandTotalQaMoney+$qaMoney;
			$grandTotalAuditMoney			=	$grandTotalAuditMoney+$auditMoney;

			$totalLInes			= $totalDirectLevel1Lines+$totalDirectLevel2Lines+$totalIndirectLevel1Lines+$totalIndirectLevel2Lines+$totalQaLevel1Lines+$totalQaLevel2Lines+$totalAuditLevel1Lines+$totalAuditLevel2Lines;

			$totalMoney			=	$directMoney+$indirectMoney+$qaMoney+$auditMoney;

			$totalMoney			=	round($totalMoney);

			$grandMoney			=	$grandMoney+$totalMoney;
			$grandLines			=	$grandLines+$totalLInes;

			$grandMoney			=	round($grandMoney);

			$worksheet1->write($i,0,$t_workedOn);
			$worksheet1->write($i,1,$uploadFileName);
			$worksheet1->write($i,2,$comments);
			$worksheet1->write($i,3,$platName);
			$worksheet1->write($i,4,$customerName);
			$worksheet1->write($i,5,$totalDirectLevel1Lines);
			$worksheet1->write($i,6,$directLevel1Rate);
			$worksheet1->write($i,7,$totalDirectLevel2Lines);
			$worksheet1->write($i,8,$directLevel1Rate);
			$worksheet1->write($i,9,$directMoney);



			$worksheet1->write($i,10,$totalIndirectLevel1Lines);
			$worksheet1->write($i,11,$indirectLevel1Rate);
			$worksheet1->write($i,12,$totalIndirectLevel2Lines);
			$worksheet1->write($i,13,$indirectLevel2Rate);
			$worksheet1->write($i,14,$indirectMoney);

			$worksheet1->write($i,15,$totalQaLevel1Lines);
			$worksheet1->write($i,16,$qaLevel1Rate);
			$worksheet1->write($i,17,$totalQaLevel1Lines);
			$worksheet1->write($i,18,$qaLevel2Rate);
			$worksheet1->write($i,19,$qaMoney);

			$worksheet1->write($i,20,$totalAuditLevel1Lines);
			$worksheet1->write($i,21,$auditLevel1Rate);
			$worksheet1->write($i,22,$totalAuditLevel2Lines);
			$worksheet1->write($i,23,$auditLevel2Rate);
			$worksheet1->write($i,24,$auditMoney);

			$worksheet1->write($i,25,$totalLInes);
			$worksheet1->write($i,26,$totalMoney);

			$directMoney			=	0;
			$indirectMoney			=	0;
			$qaMoney				=	0;
			$auditMoney				=	0;

			$totalLInes				=	0;
			$totalMoney				=	0;
		}
		$grandLines	=	round($grandLines,2);
		$grandMoney	=	round($grandMoney,2);


		$k	=	$i+1;

		$worksheet1->write_string($k,0,"-"); 

		$line	=	$i+2;

		$worksheet1->write($line,0,"GRAND TOTAL");
		$worksheet1->write($line,9,$grandTotalDirectMoney);
		$worksheet1->write($line,14,$grandTotalIndirectMoney);
		$worksheet1->write($line,18,$grandTotalQaMoney);
		$worksheet1->write($line,24,$grandTotalAuditMoney);
		$worksheet1->write($line,25,$grandLines);
		$worksheet1->write($line,26,$grandMoney);
	}
	$workbook->close();
?>
  
	