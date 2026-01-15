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
	
	$monthText		=	$a_month[$month];
	$andClause		=	"";
	$andClause1		=	"";
	$andClause2		=	"";
	$andClause3		=	"";
	$employeeName	=	"";
	$departmentId	=   0;
	$display		=	"";
	$display1		=	"none";
	$display2		=	"none";
	$table			=	"employee_details";
	$currentDay		=	$today_day;
	$currentMonth	=	$today_month;
	$currentYear	=	$today_year;
	$text			=	"View Employee Salary For ".$monthText.",".$year;
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
		$table		     =	"employee_details INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId";
		$andClause	    .=	" AND employee_shift_rates.departmentId=1";
		$andClause3		 =	" AND departmentId=1";
	}
	elseif($departmentId== 2)
	{
		$text		   .=	" REV DEPARTMENT";
		$table		    =	"employee_details INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId";
		$andClause	   .=	" AND employee_shift_rates.departmentId=2";
		$andClause3		=	" AND departmentId=2";
	}
	elseif($departmentId== 3)
	{
		$text	       .=	" PDF DEPARTMENT";
		$table		    =	"employee_details";
		$andClause	   .=	" AND employee_details.hasPdfAccess=1";
		$andClause3		=	" AND departmentId=3";
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

	function HeaderingExcel($filename)
	{
      header("Content-type: application/vnd.ms-excel");
      header("Content-Disposition: attachment; filename=$filename" );
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
      header("Pragma: public");
   }

  // HTTP headers
  HeaderingExcel('employee-salary.xls');

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
  $worksheet1->set_column(1,2,15);
  $worksheet1->set_column(3,3,15);
  $worksheet1->set_column(4,4,15);
  $worksheet1->set_column(5,5,15);
  $worksheet1->set_column(6,6,15);
  $worksheet1->set_column(7,7,15);
  $worksheet1->set_column(8,8,15);
  $worksheet1->set_column(9,9,15);
  $worksheet1->set_column(10,10,5);
  $worksheet1->set_column(11,11,5);
  $worksheet1->set_column(12,12,5);
  $worksheet1->set_column(13,13,5);
  $worksheet1->set_column(14,14,5);
  $worksheet1->set_column(15,15,5);

  //$worksheet1->write_string(0,0,$text.$text1); 

  $worksheet1->write_string(0,0,"EMPLOYEE NAME",$formatot);
  $worksheet1->write_string(0,1,"FIXED SALARY",$formatot);
  $worksheet1->write_string(0,2,"TOTAL",$formatot);
  $worksheet1->write_string(0,3,"TDS DEDUCTING",$formatot);
  $worksheet1->write_string(0,4,"PF DEDUCTING",$formatot);
  $worksheet1->write_string(0,5,"GROSS SALARY",$formatot);
  

function cleanData(&$str)
{ 
	$str = preg_replace("/\t/", "\\t", $str);
	$str = preg_replace("/\r?\n/", "\\n", $str);

	return $str;
} 
$query	=	"SELECT employee_details.employeeId,firstName,lastName FROM ". $table." WHERE isActive=1".$andClause.$andClause1." ORDER BY firstName";
$result		=	mysql_query($query);
if(mysql_num_rows($result))
{
	$i	=	0;
	while($row			=	mysql_fetch_assoc($result))
	{
		$i++;
		$lastName		=	stripslashes($row['lastName']);
		$employeeId		=	$row['employeeId'];
		$firstName		=	stripslashes($row['firstName']);
		$employeeName	=	$firstName." ".$lastName;
		$employeeName	=	ucwords($employeeName);

		$query1			=	"SELECT SUM(fixedSalary) AS totalFixedSalary,SUM(totalMoney) AS allTotalMoney,SUM(tdsDeduction) AS totalTdsDeduction,SUM(pfMoney) AS totalPfDeduction,SUM(salaryGiven) AS totalSalaryGiven FROM employee_salary_given WHERE employeeId=$employeeId AND month=$month AND year=$year".$andClause3;
		$result1		=   dbQuery($query1);
		if(mysql_num_rows($result1))
		{
			$row1					=   mysql_fetch_assoc($result1);
			$totalFixedSalary		=	$row1['totalFixedSalary'];
			$allTotalMoney			=	$row1['allTotalMoney'];
			$totalTdsDeduction		=	$row1['totalTdsDeduction'];
			$totalPfDeduction		=	$row1['totalPfDeduction'];
			$totalSalaryGiven		=	$row1['totalSalaryGiven'];
			$lineOrderFixedTotal	=	$totalFixedSalary+$allTotalMoney;
		}
		else
		{
			$totalFixedSalary		=	0;
			$allTotalMoney			=	0;
			$totalTdsDeduction		=	0;
			$totalPfDeduction		=	0;
			$totalSalaryGiven		=	0;
			$lineOrderFixedTotal	=	0;
		}	
		$worksheet1->write($i,0,$employeeName);
		$worksheet1->write($i,1,$totalFixedSalary);
		$worksheet1->write($i,2,$lineOrderFixedTotal);

		$worksheet1->write($i,3,$totalTdsDeduction);
		$worksheet1->write($i,4,$totalPfDeduction);

		$worksheet1->write($i,5,$totalSalaryGiven);
	}
}

 $workbook->close();
 ?>