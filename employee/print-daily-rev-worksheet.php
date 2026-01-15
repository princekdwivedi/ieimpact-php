<?php
	ob_start();
	session_start();
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();
	require_once(SITE_ROOT.'/classes/Worksheet.php');
	require_once(SITE_ROOT.'/classes/Workbook.php');
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$date				=	"";
	$employeeId			=	"";
	$a_employeeId		=	array();
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

	$searchBy			=	"";
	$andClause			=	"";
	$month				=	"";
	$year				=	"";
	$employeeType		=	0;
	$underManager		=	0;
	$a_managers			=	$employeeObj->getAllEmployeeManager();



	if(isset($_GET['searchBy']))
	{
		$searchBy		=	$_GET['searchBy'];
		if($searchBy	==	1)
		{
			if(isset($_GET['date']))
			{
				$date			=	$_GET['date'];
				$andClause		=	" AND workedOn='$date'";
				$text			=	"REV WORK SHEET ON - ".showDate($date);
				$orderBy		=	"firstName";
			}
		}
		else
		{
			if(isset($_GET['month']) && isset($_GET['year']))
			{
				$month			=	$_GET['month'];
				$year			=	$_GET['year'];

				$monthText		=	$a_month[$month];
				$text			=	"REV WORK SHEET ON - ".$monthText.",".$year;

				$andClause		=	" AND MONTH(workedOn)=$month AND YEAR(workedOn)=$year";
				$orderBy		=	"workedOn DESC";
			}
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
		$employeeId			=	$_GET['employee'];
		if(!empty($employeeId))
		{
			$a_employeeId		=	explode(",",$employeeId);
			$andClause		   .=	" AND employee_works.employeeId IN ($employeeId)";

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
	$whereClause	=   " WHERE employee_shift_rates.departmentId=2";
	

	function HeaderingExcel($filename)
	{
      header("Content-type: application/vnd.ms-excel");
      header("Content-Disposition: attachment; filename=$filename" );
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
      header("Pragma: public");
   }

  // HTTP headers
  HeaderingExcel('daily-rev-worksheet.xls');

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
 
 
  //$worksheet1->write_string(0,0,$text); 

  $worksheet1->write_string(0,0,"EMPLOYEE NAME",$formatot);
  $worksheet1->write_string(0,1,"WORKED ON",$formatot);
  $worksheet1->write_string(0,2,"FILE NAME",$formatot);
  $worksheet1->write_string(0,3,"COMMENTS",$formatot);
  $worksheet1->write_string(0,4,"PLATFORM",$formatot);
  $worksheet1->write_string(0,5,"CLIENT",$formatot);
  $worksheet1->write_string(0,6,"DIRECT-LEVEL1",$formatot);
  $worksheet1->write_string(0,7,"DIRECT-LEVEL12",$formatot);
  $worksheet1->write_string(0,8,"DIRECT TOTAL",$formatot);
  $worksheet1->write_string(0,9,"INDIRECT-LEVEL1",$formatot);
  $worksheet1->write_string(0,10,"INDIRECT-LEVEL2",$formatot);
  $worksheet1->write_string(0,11,"INDIRECT TOTAL",$formatot);
  $worksheet1->write_string(0,12,"QA-LEVEL1",$formatot);
  $worksheet1->write_string(0,13,"QA-LEVEL2",$formatot);
  $worksheet1->write_string(0,14,"QA TOTAL",$formatot);
  $worksheet1->write_string(0,15,"POST AUDIT-LEVEL1",$formatot);
  $worksheet1->write_string(0,16,"POST AUDIT-LEVEL2",$formatot);
  $worksheet1->write_string(0,17,"TOTAL POST AUDIT",$formatot);
 
  function cleanData(&$str)
  { 
	$str = preg_replace("/\t/", "\\t", $str);
	$str = preg_replace("/\r?\n/", "\\n", $str);

	return $str;
  }
  
	$query			=	"SELECT employee_works.*,firstName,lastName FROM employee_works INNER JOIN employee_details ON employee_works.employeeId=employee_details.employeeId INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId".$whereClause.$andClause." ORDER BY ".$orderBy;
	$result			=	mysql_query($query);
	if(mysql_num_rows($result))
	{
		$i			=	0;
		while($row	=	mysql_fetch_assoc($result))
		{
			$i++;
			$employeeId					=	$row['employeeId'];
			$firstName					=	$row['firstName'];
			$lastName					=	$row['lastName'];
			$platform					=	$row['platform'];
			$customerId					=	$row['customerId'];
			$comments					=	$row['comments'];

			$directLevel1				=	$row['directLevel1'];
			$directLevel2				=	$row['directLevel2'];
			$indirectLevel1				=	$row['indirectLevel1'];
			$indirectLevel2				=	$row['indirectLevel2'];
			$qaLevel1					=	$row['qaLevel1'];
			$qaLevel2					=	$row['qaLevel2'];
			$auditLevel1				=	$row['auditLevel1'];
			$auditLevel2				=	$row['auditLevel2'];

			$comments					=	$row['comments'];
			$fromTime					=	$row['fromTime'];
			$toTime						=	$row['toTime'];
			$totalHours					=	$row['totalHours'];

			$uploadFileName				=	$row['uploadFileName'];

			$workedOn					=	showDate($row['workedOn']);

			$grandTotalHours			=	$grandTotalHours+$totalHours;

			$hours						=	getHours($totalHours);


			$platName		=	$employeeObj->getPlatformName($platform);
			$customerName	=	$employeeObj->getCustomerName($customerId,$platform);
			$employeeName				=	$firstName." ".$lastName;
			$employeeName				=	ucwords($employeeName);

			$totalDirect				=	0;
			$totalIndirect				=	0;
			$totalQa					=	0;
			$totalAudit					=	0;

			$totalDirect				=	$directLevel1+$directLevel2;
			$totalIndirect				=	$indirectLevel1+$indirectLevel2;
			$totalQa					=	$qaLevel1+$qaLevel2;
			$totalAudit					=	$auditLevel1+$auditLevel2;

			$totalDirectLevel1	=	$totalDirectLevel1+$directLevel1;
			$totalDirectLevel2	=	$totalDirectLevel2+$directLevel2;

			$totalIndirectLevel1=	$totalIndirectLevel1+$indirectLevel1;
			$totalIndirectLevel2=	$totalIndirectLevel2+$indirectLevel2;

			$totalQaLevel1		=	$totalQaLevel1+$qaLevel1;
			$totalQaLevel2		=	$totalQaLevel2+$qaLevel2;

			$totalAuditLevel1	=	$totalAuditLevel1+$auditLevel1;
			$totalAuditLevel2	=	$totalAuditLevel2+$auditLevel2;

			$grandTotalDirect	=	$totalDirectLevel1+$totalDirectLevel2;
			$grandTotalIndirect	=	$totalIndirectLevel1+$totalIndirectLevel2;
			$grandTotalQa		=	$totalQaLevel1+$totalQaLevel2;
			$grandTotalAudit	=	$totalAuditLevel1+$totalAuditLevel2;

			$worksheet1->write($i,0,$employeeName);
			$worksheet1->write($i,1,$workedOn);
			$worksheet1->write($i,2,$uploadFileName);
			$worksheet1->write($i,3,$comments);
			$worksheet1->write($i,4,$platName);
			$worksheet1->write($i,5,$customerName);
			$worksheet1->write($i,6,$directLevel1);
			$worksheet1->write($i,7,$directLevel2);
			$worksheet1->write($i,8,$totalDirect);

			$worksheet1->write($i,9,$indirectLevel1);
			$worksheet1->write($i,10,$indirectLevel2);
			$worksheet1->write($i,11,$totalIndirect);

			$worksheet1->write($i,12,$qaLevel1);
			$worksheet1->write($i,13,$qaLevel2);
			$worksheet1->write($i,14,$totalQa);

			$worksheet1->write($i,15,$auditLevel1);
			$worksheet1->write($i,16,$auditLevel2);
			$worksheet1->write($i,17,$totalAudit);

		}
		$k	=	$i+1;

		$worksheet1->write_string($k,0,""); 

		$line	=	$i+2;

		$totalTime	=	getHours($grandTotalHours);

		$worksheet1->write($line,5,"GRAND TOTAL");
		$worksheet1->write($line,6,$totalDirectLevel1);
		$worksheet1->write($line,7,$totalDirectLevel2);
		$worksheet1->write($line,8,$grandTotalDirect);

		$worksheet1->write($line,9,$totalIndirectLevel1);
		$worksheet1->write($line,10,$totalIndirectLevel2);
		$worksheet1->write($line,11,$grandTotalIndirect);

		$worksheet1->write($line,12,$totalQaLevel1);
		$worksheet1->write($line,13,$totalQaLevel2);
		$worksheet1->write($line,14,$grandTotalQa);

		$worksheet1->write($line,15,$totalAuditLevel1);
		$worksheet1->write($line,16,$totalAuditLevel2);
		$worksheet1->write($line,17,$grandTotalAudit);

	}

	$workbook->close();
?>