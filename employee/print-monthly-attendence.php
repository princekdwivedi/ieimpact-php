<?php
	ob_start();
	session_start();
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
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
	if(isset($_GET['month']) && isset($_GET['year']))
	{
		$month			=	$_GET['month'];
		$year			=	$_GET['year'];
	}

	$nonLeadingZeroMonth		=	$month;
	if($month < 10 && strlen($month) > 1)
	{
		$nonLeadingZeroMonth	=	substr($month,1);
	}
	
	$monthText		=	$a_month[$month];
	$andClause		=	"";
	$andClause1		=	"";
	$andClause2		=	"";
	$employeeName	=	"";
	$departmentId	=   0;
	$display		=	"";
	$display1		=	"none";
	$display2		=	"none";
	$table			=	"employee_details";
	$currentDay		=	$today_day;
	$currentMonth	=	$today_month;
	$currentYear	=	$today_year;
	$text			=	"View Attandence For ".$monthText.",".$year;
	$text1			=	"";
	$employeeType	=	0;
	$underManager	=	0;
	$employeeId		=	0;
	$a_employeeId	=	array();
	$a_managers		=	$employeeObj->getAllEmployeeManager();

	if(isset($_GET['departmentId']))
	{
		$departmentId		=	$_GET['departmentId'];
		if(empty($departmentId))
		{
			$departmentId	=	0;
		}
	}
	if(isset($_GET['employeeType']))
	{
		$employeeType	=	$_GET['employeeType'];
		if(!empty($employeeType))
		{
			$andClause	   .=	" AND employee_details.employeeType=$employeeType";
			$text1		   .=	" for ".$a_inetExtEmployee[$employeeType]." employees";
		}
	}
	if(isset($_GET['underManager']))
	{
		$underManager	=	$_GET['underManager'];
		if(!empty($underManager))
		{
			$andClause	   .=	" AND employee_details.underManager=$underManager";
			$text1		   .=	" under manager ".$a_managers[$underManager];
		}
	}
	if($departmentId== 1)
	{
		$text		    .=	" MT DEPARTMENT";
		$table		    =	"employee_details INNER JOIN track_daily_employee_attendance ON employee_details.employeeId=track_daily_employee_attendance.employeeId INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId";
		$andClause	   .=	" AND employee_shift_rates.departmentId=1";
	}
	elseif($departmentId== 2)
	{
		$text		   .=	" REV DEPARTMENT";
		$table		    =	"employee_details INNER JOIN track_daily_employee_attendance ON employee_details.employeeId=track_daily_employee_attendance.employeeId INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId";
		$andClause	   .=	" AND employee_shift_rates.departmentId=2";
	}
	elseif($departmentId== 3)
	{
		$text	       .=	" PDF DEPARTMENT";
		$table		    =	"employee_details INNER JOIN track_daily_employee_attendance ON employee_details.employeeId=track_daily_employee_attendance.employeeId";
		$andClause	   .=	" AND employee_details.hasPdfAccess=1";
	}
	if(isset($_GET['employeeId']))
	{
		$searchEmployee		=	$_GET['employeeId'];
		if(!empty($searchEmployee))
		{
			$pos	=	strpos($searchEmployee, ",");
			if($pos == true)
			{
				$andClause1    .=	" AND employee_details.employeeId IN ($searchEmployee)";
				$text1		   .=	" multiple employees";
			}
			else
			{
				$andClause1    .=	" AND employee_details.employeeId = $searchEmployee";
				$employeeName	=	$employeeObj->getEmployeeName($searchEmployee);
				$text1		   .=	" for employee ".$employeeName;
			}
		}
	}

	

	$endMonthDate	=	$a_daysInMonth[$nonLeadingZeroMonth];
	$lastPlace		=	$endMonthDate+1;
	$lastPlace1		=	$lastPlace+1;
	$lastPlace2		=	$lastPlace1+1;


	function HeaderingExcel($filename)
	{
      header("Content-type: application/vnd.ms-excel");
      header("Content-Disposition: attachment; filename=$filename" );
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
      header("Pragma: public");
   }

  // HTTP headers
  HeaderingExcel('monthly-attendance.xls');

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
  $worksheet1->set_column(1,2,5);
  $worksheet1->set_column(3,3,5);
  $worksheet1->set_column(4,4,5);
  $worksheet1->set_column(5,5,5);
  $worksheet1->set_column(6,6,5);
  $worksheet1->set_column(7,7,5);
  $worksheet1->set_column(8,8,5);
  $worksheet1->set_column(9,9,5);
  $worksheet1->set_column(10,10,5);
  $worksheet1->set_column(11,11,5);
  $worksheet1->set_column(12,12,5);
  $worksheet1->set_column(13,13,5);
  $worksheet1->set_column(14,14,5);
  $worksheet1->set_column(15,15,5);
  $worksheet1->set_column(16,16,5);
  $worksheet1->set_column(17,17,5);
  $worksheet1->set_column(18,18,5);
  $worksheet1->set_column(19,19,5);
  $worksheet1->set_column(20,20,5);
  $worksheet1->set_column(21,21,5);
  $worksheet1->set_column(22,22,5);
  $worksheet1->set_column(23,23,5);
  $worksheet1->set_column(24,24,5);
  $worksheet1->set_column(25,25,5);
  $worksheet1->set_column(26,26,5);
  $worksheet1->set_column(27,27,5);
  $worksheet1->set_column(28,28,5);
  $worksheet1->set_column(29,29,5);
  $worksheet1->set_column(30,30,5);
  $worksheet1->set_column(31,31,5);
  $worksheet1->set_column(32,32,15);
  $worksheet1->set_column(33,33,15);
  $worksheet1->set_column(34,34,15);
  $worksheet1->set_column(35,35,15);

  $worksheet1->write_string(0,0,"EMPLOYEE NAME",$formatot);
  foreach($a_monthDateText as $dayNum=>$dayText)
  {
	    $worksheet1->write(0,$dayNum,$dayText,$formatot);
  }
  $worksheet1->write_string(0,$lastPlace,"Total Present",$formatot);
  $worksheet1->write_string(0,$lastPlace1,"Total Absent",$formatot);
  $worksheet1->write_string(0,$lastPlace2,"Total Overtime",$formatot);

function cleanData(&$str)
{ 
	$str = preg_replace("/\t/", "\\t", $str);
	$str = preg_replace("/\r?\n/", "\\n", $str);

	return $str;
} 
$query		=	"SELECT track_daily_employee_attendance.* FROM ". $table." WHERE isActive=1 AND forMonth=$nonLeadingZeroMonth AND forYear=$year".$andClause.$andClause1." ORDER BY firstName";

$result		=	mysql_query($query);
if(mysql_num_rows($result))
{
	$i		=	0;
	while($row			=	mysql_fetch_assoc($result))
	{
		$i++;
		$employeeName	=	stripslashes($row['employeeName']);
		$employeeId		=	$row['employeeId'];
		$presentDays	=	$row['totalPresent'];
		$totalAbsent	=	$row['totalAbsent'];
		$totalOvertime	=	$row['totalOvertime'];
		$totalDaysInMonth	=	$row['totalDaysInMonth'];
		$showForYear	=	$row['forYear'];
		$showForMonth	=	$row['forMonth'];

		$worksheet1->write($i,0,$employeeName);
		
		foreach($a_monthDateText as $kk1=>$vv1)
		{
			if($kk1 > $totalDaysInMonth)
			{
				break;
			}

			$value				=	$row[$vv1];
			$attText			=	$a_attendanceMarked[$value];

			list($fullAtttext,$abbAttText)	=	explode("|",$attText);

			$attandanceText		=	$abbAttText;
			if($value			==	1)
			{
				$attandanceText	=	"P";
			}
			elseif($value		==	2)
			{
				$attandanceText	=	"HD";
			}
			elseif($value		==	3)
			{
				$attandanceText	    =	"L";
				$t_checkDate		=	$kk1;
				$t_showForMonth		=	$showForMonth;
				if(strlen($showForMonth) < 2){
					$t_showForMonth	=	"0".$showForMonth;
				}
				if(strlen($kk1) < 2){
					$t_checkDate	=	"0".$kk1;
				}
				$checkdate		=	$showForYear."-".$t_showForMonth."-".$t_checkDate;
				$isMarkedAbsent	=	@mysql_result(dbQuery("SELECT isMarkedAbsent FROM employee_attendence WHERE employeeId=$employeeId AND loginDate='$checkdate'"),0);
				if($isMarkedAbsent == 1){
					$attandanceText	=	"A";
				}
				
				$isForLineCounts	=	@mysql_result(dbQuery("SELECT isForLineCounts FROM employee_attendence WHERE employeeId=$employeeId AND loginDate='$checkdate'"),0);
				if($isForLineCounts == 1){
					$attandanceText	=	"A";
				}

			}
			elseif($value		==	4)
			{
				$attandanceText	=	"H";
			}
			elseif($value		==	5)
			{
				$attandanceText	=	"S";
			}

			if($kk1 > $currentDay && $showForYear == $currentYear && $showForMonth ==  $currentMonth && ($value != 2 && $value != 3))
			{
				$attandanceText	=	"-";
			}

			$worksheet1->write($i,$kk1,$attandanceText);
		}

	   $totalOvertimeAdded  = getHours($totalOvertime);

	   $worksheet1->write($i,$lastPlace,$presentDays);
	   $worksheet1->write($i,$lastPlace1,$totalAbsent);
	   $worksheet1->write($i,$lastPlace2,$totalOvertimeAdded);

	}
}

	$workbook->close();
?>