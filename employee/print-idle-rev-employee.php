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
	$t_searchOn					=	"";

	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	
	$text			=	"";
	$whereClause	=   "WHERE employee_shift_rates.departmentId=2 AND isActive=1";
	$andClause		=	"";
	$andClause1		=	"";

	if(isset($_GET['searchBy']))
	{
		$searchBy				=	$_GET['searchBy'];
		
		if($searchBy	== 1)
		{
			$searchOn					=	$_GET['date'];

			$andClause1		=	" AND workedOn='$searchOn'";
			$text			=	"View idle REV employees on - ".showDate($searchOn);
		}
		else
		{
			$month			=	$_GET['month'];
			$year			=	$_GET['year'];
			$andClause1		=	" AND MONTH(workedOn)=$month AND YEAR(workedOn)=$year";

			$monthText		=	$a_month[$month];
			$text		    =	"View idle REV employees on - ".$monthText.",".$year;
		}
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	if(isset($_GET['type']))
	{
		$type						=	$_GET['type'];
		if(!empty($type))
		{
			$andClause	   .=	" AND employee_details.employeeType=$type";
			$text		   .=	" for ".$a_inetExtEmployee[$type]." employees";
		}
	}
	if(isset($_REQUEST['manager']))
	{
		$manager			=	$_REQUEST['manager'];
		if(!empty($manager))
		{
			$andClause	   .=	" AND employee_details.underManager=$manager";
			$text		   .=	" under manager ".$a_managers[$manager];
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
  HeaderingExcel('view-idle-REV-employees.xls');

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

 
  $worksheet1->set_column(0,0,35);
  $worksheet1->set_column(1,2,35);

  //$worksheet1->write_string(0,0,$text); 

  $worksheet1->write_string(0,0,"EMPLOYEE NAME",$formatot);
  
  function cleanData(&$str)
  { 
	$str = preg_replace("/\t/", "\\t", $str);
	$str = preg_replace("/\r?\n/", "\\n", $str);

	return $str;
  } 
  
  $query	=	"SELECT employee_details.* FROM employee_details INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId ".$whereClause.$andClause." ORDER BY firstName";
  $result	=	mysql_query($query);
  if(mysql_num_rows($result))
  {
	$i	=	0;
	while($row			=	mysql_fetch_assoc($result))
	{
		$employeeId		=	$row['employeeId'];
		$firstName		=	stripslashes($row['firstName']);
		$lastName		=	stripslashes($row['lastName']);
		$employeeName	=	$firstName." ".$lastName;

		$query1			=	"SELECT workId FROM employee_works WHERE employeeId=$employeeId".$andClause1;
		$result1		=	dbQuery($query1);
		if(!mysql_num_rows($result1))
		{
			$i++;
			$worksheet1->write($i,0,$employeeName);
		}

	}
}
$workbook->close();
?>