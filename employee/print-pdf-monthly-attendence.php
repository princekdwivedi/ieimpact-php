<?php
	ob_start();
	session_start();
	ini_set('display_errors', '1');
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

	/////Fuction to get single query RESULT //////
	function getSingleQueryDataResult($query,$param){
		$retrnResult	=	"";
		$result			=	@dbQuery($query);
		if(@mysqli_num_rows($result))
		{
			$row		=	@mysqli_fetch_assoc($result);
			$retrnResult=   $row[$param];
		}
		return $retrnResult;
	}

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

	$a_monthDateText			=	array();
	$a_monthDateText[1]			=	"1st|B";
	$a_monthDateText[2]			=	"2nd|C";
	$a_monthDateText[3]			=	"3rd|D";
	$a_monthDateText[4]			=	"4th|E";
	$a_monthDateText[5]			=	"5th|F";
	$a_monthDateText[6]			=	"6th|G";
	$a_monthDateText[7]			=	"7th|H";
	$a_monthDateText[8]			=	"8th|I";
	$a_monthDateText[9]			=	"9th|J";
	$a_monthDateText[10]		=	"10th|K";
	$a_monthDateText[11]		=	"11th|L";
	$a_monthDateText[12]		=	"12th|M";
	$a_monthDateText[13]		=	"13th|N";
	$a_monthDateText[14]		=	"14th|O";
	$a_monthDateText[15]		=	"15th|P";
	$a_monthDateText[16]		=	"16th|Q";
	$a_monthDateText[17]		=	"17th|R";
	$a_monthDateText[18]		=	"18th|S";
	$a_monthDateText[19]		=	"19th|T";
	$a_monthDateText[20]		=	"20th|U";
	$a_monthDateText[21]		=	"21st|V";
	$a_monthDateText[22]		=	"22nd|W";
	$a_monthDateText[23]		=	"23rd|X";
	$a_monthDateText[24]		=	"24th|Y";
	$a_monthDateText[25]		=	"25th|Z";
	$a_monthDateText[26]		=	"26th|AA";
	$a_monthDateText[27]		=	"27th|AB";
	$a_monthDateText[28]		=	"28th|AC";
	$a_monthDateText[29]		=	"29th|AD";
	$a_monthDateText[30]		=	"30th|AE";
	$a_monthDateText[31]		=	"31st|AF";


  $storePath						=	SITE_ROOT_FILES."/files/excel-files/";

  require_once 	SITE_ROOT. '/excel/Classes/PHPExcel.php';

  
/****EXCEL WRITING CODE****/
	// Create new PHPExcel object
	$i	=	0;
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
	foreach($a_monthDateText as $k=>$v){
		list($text,$letter) = explode("|",$v);
		$objPHPExcel->getActiveSheet()->getColumnDimension($letter)->setWidth(15);
	}
	$objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setWidth(25);
	
			
	$sharedStyle1 = new PHPExcel_Style();
	
	$sharedStyle1->applyFromArray(
		array('fill' 	=> array(
									'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
									'color'		=> array('argb' => 'FFFFFF00')
								),
			  'borders' => array(
									'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
									'right'		=> array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
								)
			 ));

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Attendence Details');
	
	$objDrawing = new PHPExcel_Worksheet_Drawing();
	$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

	$l	=	1;

	$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A$l:AI$l");
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$l,"EMPLOYEE NAME");
	$objPHPExcel->getActiveSheet()->getStyle('A'.$l)->getFont()->setBold(true);
	foreach($a_monthDateText as $i=>$v){
		list($text,$letter) = explode("|",$v);

		$objPHPExcel->getActiveSheet()->setCellValue($letter.$l,$text);
		$objPHPExcel->getActiveSheet()->getStyle($letter.$l)->getFont()->setBold(true);
	}

	$objPHPExcel->getActiveSheet()->setCellValue('AG'.$l,"Total Present");
	$objPHPExcel->getActiveSheet()->getStyle('AG'.$l)->getFont()->setBold(true);

	$objPHPExcel->getActiveSheet()->setCellValue('AH'.$l,"Total Absent");
	$objPHPExcel->getActiveSheet()->getStyle('AH'.$l)->getFont()->setBold(true);

	$objPHPExcel->getActiveSheet()->setCellValue('AI'.$l,"Total Overtime");
	$objPHPExcel->getActiveSheet()->getStyle('AI'.$l)->getFont()->setBold(true);



$query		=	"SELECT track_daily_employee_attendance.* FROM ". $table." WHERE isActive=1 AND forMonth=$nonLeadingZeroMonth AND forYear=$year".$andClause.$andClause1." ORDER BY firstName";

$result		=	dbQuery($query);
if(mysqli_num_rows($result))
{
	$i		=	0;
	while($row			    =	mysqli_fetch_assoc($result))
	{
		$i++;
		$l++;
		$employeeName	    =	stripslashes($row['employeeName']);
		$employeeId		    =	$row['employeeId'];
		$presentDays	    =	$row['totalPresent'];
		$totalAbsent	    =	$row['totalAbsent'];
		$totalOvertime	    =	$row['totalOvertime'];
		$totalDaysInMonth	=	$row['totalDaysInMonth'];
		$showForYear	    =	$row['forYear'];
		$showForMonth	    =	$row['forMonth'];

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$l,$employeeName);
		
		foreach($a_monthDateText as $kk1=>$vv1)
		{
			list($text,$letter) = explode("|",$vv1);
			if($kk1 > $totalDaysInMonth)
			{
				break;
			}

			$value				=	$row[$text];
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
				$isMarkedAbsent	=	getSingleQueryDataResult("SELECT isMarkedAbsent FROM employee_attendence WHERE employeeId=$employeeId AND loginDate='$checkdate'","isMarkedAbsent");
				if($isMarkedAbsent == 1){
					$attandanceText	=	"A";
				}
				
				$isForLateAttendance	=	getSingleQueryDataResult("SELECT isForLateAttendance FROM employee_attendence WHERE employeeId=$employeeId AND loginDate='$checkdate' AND isMarkedAbsent=1","isForLateAttendance");
				if($isForLateAttendance == 1){
					$attandanceText		=	"L (Late Attendance)";
				}

				$isForNotLogout	=	getSingleQueryDataResult("SELECT isForNotLogout FROM employee_attendence WHERE employeeId=$employeeId AND loginDate='$checkdate' AND isMarkedAbsent=1","isForNotLogout");
				if($isForNotLogout == 1){
					$attandanceText		=	"L (Not Logout)";
				}

				/*$isMarkedAbsent	=	getSingleQueryDataResult("SELECT isMarkedAbsent FROM employee_attendence WHERE employeeId=$employeeId AND loginDate='$checkdate'","isMarkedAbsent");
				if($isMarkedAbsent == 1){
					$attandanceText	=	"A";
				}*/

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

			$objPHPExcel->getActiveSheet()->setCellValue($letter.$l,$attandanceText);
	  }
	  
	   $totalOvertimeAdded  = getHours($totalOvertime);
	   $objPHPExcel->getActiveSheet()->setCellValue('AG'.$l,$presentDays);
	   $objPHPExcel->getActiveSheet()->setCellValue('AH'.$l,$totalAbsent);
	   $objPHPExcel->getActiveSheet()->setCellValue('AI'.$l,$totalOvertimeAdded);
	}
}

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

	$filaneNamePrefixed	=	"12345678912";
			
	$storeFileName	=	md5($filaneNamePrefixed)."pdf-monthly-attendance-".$monthText."-".$year.".xls";
	$objWriter->save($storePath.$storeFileName);
	
	 echo "<br><br><center><a href='".SITE_URL_EMPLOYEES."/download-excel.php?t=".$storeFileName."' class='linkstyle8' target='_blank'>DOWNLOAD EXCEL SHEET</a></center>";
?>