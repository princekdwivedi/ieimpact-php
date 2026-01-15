<?php
	session_start();
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	$employeeObj				=	new employee();

	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");

	$text				=	"";

	$totalTranscriptionDirect		=	0;
	$totalTranscriptionIndirirect	=	0;
	$totalVreDirect					=	0;
	$totalVreIndirect				=	0;
	$totalQaDirect					=	0;
	$totalQaIndirect				=	0;
	$totalAuditDirect				=	0;
	$totalAuditIndirect				=	0;

	$grandTotalTranscription		=	0;
	$grandTotalVre					=	0;
	$grandTotalQa					=	0;
	$grandTotalAudit				=	0;

	$grandTotalTranscriptionMoney	=	0;
	$grandTotalVreMoney				=	0;
	$grandTotalQaMoney				=	0;
	$grandTotalAuditMoney			=	0;

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
  HeaderingExcel('monthly-mt-worksheet.xls');

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
  $worksheet1->set_column(4,4,20);
  $worksheet1->set_column(5,5,20);
  $worksheet1->set_column(6,6,20);
  $worksheet1->set_column(7,7,20);
  $worksheet1->set_column(8,8,30);
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
  $worksheet1->set_column(19,19,30);
  $worksheet1->set_column(20,20,20);
  $worksheet1->set_column(21,21,30);
  $worksheet1->set_column(22,22,20);
  $worksheet1->set_column(23,23,30);
  $worksheet1->set_column(24,24,20);
  $worksheet1->set_column(25,25,20);
 
 // $worksheet1->write_string(1,0,$text); 

  //$worksheet1->write_string(2,0,""); 

  $worksheet1->write_string(0,0,"WORKED ON",$formatot);
  $worksheet1->write_string(0,1,"COMMENTS",$formatot);
  $worksheet1->write_string(0,2,"PLATFORM",$formatot);
  $worksheet1->write_string(0,3,"CLIENT",$formatot);
  $worksheet1->write_string(0,4,"TRANSCRIPTION-DSP",$formatot);
  $worksheet1->write_string(0,5,"X MONEY PER LINES",$formatot);
  $worksheet1->write_string(0,6,"TRANSCRIPTION-NDSP",$formatot);
  $worksheet1->write_string(0,7,"X MONEY PER LINES",$formatot);
  $worksheet1->write_string(0,8,"TRANSCRIPTION TOTAL MONEY",$formatot);
  $worksheet1->write_string(0,9,"VRE-DSP",$formatot);
  $worksheet1->write_string(0,10,"X MONEY PER LINES",$formatot);
  $worksheet1->write_string(0,11,"VRE-NDSP",$formatot);
  $worksheet1->write_string(0,12,"X MONEY PER LINES",$formatot);
  $worksheet1->write_string(0,13,"VRE TOTAL MONEY",$formatot);
  $worksheet1->write_string(0,14,"QA-DSP",$formatot);
  $worksheet1->write_string(0,15,"X MONEY PER LINES",$formatot);
  $worksheet1->write_string(0,16,"QA-NDSP",$formatot);
  $worksheet1->write_string(0,17,"X MONEY PER LINES",$formatot);
  $worksheet1->write_string(0,18,"QA TOTAL MONEY",$formatot);
  $worksheet1->write_string(0,19,"NIGHT SHIFT LINES-TRANSCRIPTION",$formatot);
  $worksheet1->write_string(0,20,"X MONEY PER LINES",$formatot);
  $worksheet1->write_string(0,21,"NIGHT SHIFT LINES-VRE",$formatot);
  $worksheet1->write_string(0,22,"X MONEY PER LINES",$formatot);
  $worksheet1->write_string(0,23,"NIGHT SHIFT LINES TOTAL MONEY",$formatot);
  $worksheet1->write_string(0,24,"TOTAL LINES",$formatot);
  $worksheet1->write_string(0,25,"TOTAL MONEY",$formatot);
 
  function cleanData(&$str)
  { 
	$str = preg_replace("/\t/", "\\t", $str);
	$str = preg_replace("/\r?\n/", "\\n", $str);

	return $str;
  }
  
	$query	=	"SELECT * FROM datewise_employee_works_money WHERE ID > ".MAX_SEARCH_MT_EMPLOYEE_WORKID." AND MONTH(workedOnDate)=$month AND YEAR(workedOnDate)=$year AND employeeId=$s_employeeId ORDER BY workedOnDate";
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
			
			$transcriptionLinesEntered		=	$row['totalDirectTrascriptionLines'];
			$vreLinesEntered				=	$row['totalDirectVreLines'];
			$qaLinesEntered					=	$row['totalQaLines'];
			$auditLinesEntered				=	$row['totalDirectAuditLines'];
			
			$indirectTranscriptionLinesEntered	=	$row['totalIndirectTrascriptionLines'];
			$indirectVreLinesEntered		=	$row['totalIndirectVreLines'];
			$indirectQaLinesEntered			=	$row['totalIndirectQaLines'];
			$indirectAuditLinesEntered		=	$row['totalIndirectAuditLines'];

			$directTranscriptionRate		=	$row['directTranscriptionRate'];
			$indirectTranscriptionRate		=	$row['indirectTranscriptionRate'];
			$directVreRate					=	$row['directVreRate'];
			$indirectVreRate				=	$row['indirectVreRate'];
			$directQaRate					=	$row['directQaRate'];
			$indirectQaRate					=	$row['indirectQaRate'];
			$directAuditRate				=	$row['directAuditRate'];
			$indirectAuditRate				=	$row['indirectAuditRate'];

			$totalDirectTrascriptionMoney	=	$row['totalDirectTrascriptionMoney'];
			$totalIndirectTrascriptionMoney	=	$row['totalIndirectTrascriptionMoney'];
			$totalDirectVreMoney			=	$row['totalDirectVreMoney'];
			$totalIndirectVreMoney			=	$row['totalIndirectVreMoney'];
			$totalDirectQaMoney				=	$row['totalDirectQaMoney'];
			$totalIndirectQaMoney			=	$row['totalIndirectQaMoney'];
			$totalDirectAuditMoney			=	$row['totalDirectAuditMoney'];
			$totalIndirectAuditMoney		=	$row['totalIndirectAuditMoney'];

			$workedOn						=	$row['workedOnDate'];
			$t_workedOn						=	showDate($row['workedOnDate']);
			$comments						=	stripslashes($row['comments']);
			
			$platName						=	$employeeObj->getPlatformName($platform);
			$customerName					=	$employeeObj->getCustomerName($customerId,$platform);

			
			$transcriptionMoney				=	$totalDirectTrascriptionMoney+$totalIndirectTrascriptionMoney;
			$vreMoney						=	$totalDirectVreMoney+$totalIndirectVreMoney;
			$qaMoney						=	$totalDirectQaMoney+$totalIndirectQaMoney;
			$auditMoney						=	$totalDirectAuditMoney+$totalIndirectAuditMoney;


			$grandTotalTranscriptionMoney	=	$grandTotalTranscriptionMoney+$transcriptionMoney;

			$grandTotalVreMoney				=	$grandTotalVreMoney+$vreMoney;
			$grandTotalQaMoney				=	$grandTotalQaMoney+$qaMoney;
			$grandTotalAuditMoney			=	$grandTotalAuditMoney+$auditMoney;

			$totalLInes			= $transcriptionLinesEntered+$indirectTranscriptionLinesEntered+$vreLinesEntered+$indirectVreLinesEntered+$qaLinesEntered+$indirectQaLinesEntered+$auditLinesEntered+$indirectAuditLinesEntered;

			$totalMoney			=	$transcriptionMoney+$vreMoney+$qaMoney+$auditMoney;

			$totalMoney			=	round($totalMoney);

			$grandMoney			=	$grandMoney+$totalMoney;
			$grandLines			=	$grandLines+$totalLInes;

			$grandMoney			=	round($grandMoney);

			$worksheet1->write($i,0,$t_workedOn);
			$worksheet1->write($i,1,$comments);
			$worksheet1->write($i,2,$platName);
			$worksheet1->write($i,3,$customerName);

			$worksheet1->write($i,4,$transcriptionLinesEntered);
			$worksheet1->write($i,5,$directTranscriptionRate);
			$worksheet1->write($i,6,$indirectTranscriptionLinesEntered);
			$worksheet1->write($i,7,$indirectTranscriptionRate);
			$worksheet1->write($i,8,$transcriptionMoney);

			$worksheet1->write($i,9,$vreLinesEntered);
			$worksheet1->write($i,10,$directVreRate);
			$worksheet1->write($i,11,$indirectVreLinesEntered);
			$worksheet1->write($i,12,$indirectVreRate);
			$worksheet1->write($i,13,$vreMoney);

			$worksheet1->write($i,14,$qaLinesEntered);
			$worksheet1->write($i,15,$directQaRate);
			$worksheet1->write($i,16,$indirectQaLinesEntered);
			$worksheet1->write($i,17,$indirectQaRate);
			$worksheet1->write($i,18,$qaMoney);

			$worksheet1->write($i,19,$auditLinesEntered);
			$worksheet1->write($i,20,$directAuditRate);
			$worksheet1->write($i,21,$indirectAuditLinesEntered);
			$worksheet1->write($i,22,$indirectAuditRate);
			$worksheet1->write($i,23,$auditMoney);

			$worksheet1->write($i,24,$totalLInes);
			$worksheet1->write($i,25,$totalMoney);


			$directTranscriptionMoney		=	0;
			$indirectTranscriptionMoney		=	0;

			$directVreMoney					=	0;
			$indirectVreMoney				=	0;

			$directQaMoney					=	0;
			$indirectQaMoney				=	0;


			$directAuditMoney				=	0;
			$indirectAuditMoney				=	0;

			$transcriptionMoney				=	0;
			$vreMoney						=	0;
			$qaMoney						=	0;
			$auditMoney						=	0;
			$totalLInes						=	0;
			$totalMoney						=	0;
		}
		$grandLines	=	$grandLines;
		$grandMoney	=	round($grandMoney);


		$k	=	$i+1;

		$worksheet1->write_string($k,0,"-"); 

		$line	=	$i+2;

		$worksheet1->write($line,0,"GRAND TOTAL");
		$worksheet1->write($line,8,$grandTotalTranscriptionMoney);
		$worksheet1->write($line,13,$grandTotalVreMoney);
		$worksheet1->write($line,17,$grandTotalQaMoney);
		$worksheet1->write($line,23,$grandTotalAuditMoney);
		$worksheet1->write($line,24,$grandLines);
		$worksheet1->write($line,25,$grandMoney);
	}
	$workbook->close();
?>
  
	