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
	
	$month			=	"";
	$year			=	"";
	$toMonth		=	"";
	$toYear			=	"";
	$andClause		=	"";
	$andClause1		=	"";
	$andClause2		=	"";
	$andClause3		=	"";
	$employeeName	=	"";
	$departmentId	=   0;
	$display		=	"";
	$display1		=	"none";
	$display2		=	"none";
	$display3		=	"none";
	$whereClause	=	" WHERE employee_details.isActive=1";
	$orderBy		=	" ORDER BY firstName";
	$currentDay		=	$today_day;
	$currentMonth	=	$today_month;
	$currentYear	=	$today_year;
	$employeeType	=	0;
	$underManager	=	0;
	$employeeId		=	0;
	$formText		=	"SALARY";
	$monthText		=	"";
	$monthText1		=	"";
	$a_managers		=	$employeeObj->getAllEmployeeManager();
	$text			=	"VIEW SALARY GIVEN";
	$dateText		=	"";
	$totalGivenMoney    =	0;
	$totalGivenPf	    =	0;
	$totalGivenTds	    =	0;
	$totalUnDedSalary	=	0;
	$totalDedGrosSalary =	0;

	if(isset($_GET['month']) && isset($_GET['year']))
	{
		$month			=	$_GET['month'];
		$year			=	$_GET['year'];
	}

	if(!empty($month) && !empty($year))
	{
		$serarchFromDate=	$year."-".$month."-01";
		$dateText		=	" for ".$a_month[$month].",".$year;
		$andClause		=	" AND employee_salary_given.salaryPaidForDate='$serarchFromDate'";
		if(isset($_REQUEST['toMonth']) && isset($_REQUEST['toYear']))
		{
			$toMonth		=	$_REQUEST['toMonth'];
			$toYear			=	$_REQUEST['toYear'];
			if(!empty($toMonth) && !empty($toYear))
			{
				$dateText		=	" from ".$a_month[$month].",".$year." to ".$a_month[$toMonth].",".$toYear;

				$serarchToDate=	$toYear."-".$toMonth."-31";

				$andClause	  =	" AND employee_salary_given.salaryPaidForDate  >= '$serarchFromDate' AND employee_salary_given.salaryPaidForDate <= '$serarchToDate'";
				}
		}
	}

	if($departmentId== 1)
	{
		$andClause1		=	" AND departmentId=1";
		$text		   .=	" of department - MT";
	}
	elseif($departmentId== 2)
	{
		$andClause1		=	" AND departmentId=2";
		$text		   .=	" of department - Rev";
	}
	elseif($departmentId== 3)
	{
		$andClause1		=	" AND departmentId=3";
		$text		   .=	" of department - PDF";
	}
	if(isset($_REQUEST['employeeType']))
	{
		$employeeType	=	$_REQUEST['employeeType'];
		if(!empty($employeeType))
		{
			$andClause2	   .=	" AND employee_details.employeeType=$employeeType";
		}
	}
	if(isset($_REQUEST['underManager']))
	{
		$underManager	=	$_REQUEST['underManager'];
		if(!empty($underManager))
		{
			$andClause2	   .=	" AND employee_details.underManager=$underManager";
		}
	}
	if(isset($_GET['employeeId']))
	{
		$searchEmployee		=	$_GET['employeeId'];
		if(!empty($searchEmployee))
		{
			$pos	=	strpos($searchEmployee, ",");
			if($pos == true)
			{
				$andClause2    .=	" AND employee_details.employeeId IN ($searchEmployee)";
				$text		   .=	" multiple employees";
			}
			else
			{
				$andClause2    .=	" AND employee_details.employeeId = $searchEmployee";
				$employeeName	=	$employeeObj->getEmployeeName($searchEmployee);
				$text		   .=	" for employee ".$employeeName;
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
  HeaderingExcel('paid-employee-salary.xls');

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
 
  //$worksheet1->write_string(0,0,$text.$dateText); 

  $worksheet1->write_string(0,0,"EMPLOYEE NAME",$formatot);
  $worksheet1->write_string(0,1,"TOTAL SALARY",$formatot);
  $worksheet1->write_string(0,2,"TDS DEDUCTING",$formatot);
  $worksheet1->write_string(0,3,"PF DEDUCTING",$formatot);
  $worksheet1->write_string(0,4,"GROSS SALARY",$formatot);
  $worksheet1->write_string(0,5,"SALARY GIVEN",$formatot);
  

function cleanData(&$str)
{ 
	$str = preg_replace("/\t/", "\\t", $str);
	$str = preg_replace("/\r?\n/", "\\n", $str);

	return $str;
} 
$query	=	"SELECT SUM(fixedSalary) as totalFixedSalary,SUM(totalMoney) as totalOrderLineMoney,SUM(pfMoney) as totalPfMoney,SUM(tdsDeduction) as totalTdsMoney,SUM(salaryGiven) as totalSalaryGiven,employee_salary_given.employeeId,firstName,lastName FROM employee_salary_given INNER JOIN employee_details ON employee_salary_given.employeeId=employee_details.employeeId".$whereClause.$andClause.$andClause1.$andClause2." GROUP BY employee_salary_given.employeeId".$orderBy;
$result	=	dbQuery($query);
if(mysql_num_rows($result))
{
	$i	=	0;
	while($row			=	mysql_fetch_assoc($result))
	{
		$i++;
		$employeeId		     =	$row['employeeId'];
		$firstName		     =	stripslashes($row['firstName']);
		$lastName		     =	stripslashes($row['lastName']);
		$employeeName	     =	$firstName." ".$lastName;
		$totalFixedSalary    =	$row['totalFixedSalary'];
		$totalOrderLineMoney =	$row['totalOrderLineMoney'];
		$totalPfMoney		 =	$row['totalPfMoney'];
		$totalTdsMoney		 =	$row['totalTdsMoney'];
		$totalSalaryGiven	 =	$row['totalSalaryGiven'];
		$totalOrderLineMoney =  $totalFixedSalary+$totalOrderLineMoney;

		$totalDeducting		 =	$totalPfMoney+$totalTdsMoney;
		$grossSalary		 =  $totalOrderLineMoney-$totalDeducting;

		$totalGivenMoney	 =	$totalGivenMoney+$totalSalaryGiven;
		$totalGivenPf		 =	$totalGivenPf+$totalPfMoney;
		$totalGivenTds		 =	$totalGivenTds+$totalTdsMoney;
		$totalUnDedSalary	 =	$totalUnDedSalary+$totalOrderLineMoney;
		$totalDedGrosSalary  =	$totalDedGrosSalary+$grossSalary;


		$worksheet1->write($i,0,$employeeName);
		$worksheet1->write($i,1,$totalOrderLineMoney);
		$worksheet1->write($i,2,$totalTdsMoney);
		$worksheet1->write($i,3,$totalPfMoney);
		$worksheet1->write($i,4,$grossSalary);
		$worksheet1->write($i,5,$totalSalaryGiven);
	}

	$k	=	$i+1;

	$worksheet1->write($k,0,"TOTAL");
	$worksheet1->write($k,1,$totalUnDedSalary);
	$worksheet1->write($k,2,$totalGivenTds);
	$worksheet1->write($k,3,$totalGivenPf);
	$worksheet1->write($k,4,$totalDedGrosSalary);
	$worksheet1->write($k,5,$totalGivenMoney);
}

 $workbook->close();
 ?>