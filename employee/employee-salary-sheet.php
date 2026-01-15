<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	$pagingObj		=	new Paging();
	$employeeObj	=	new employee();

	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$month			=	date("m");
	$year			=	date("Y");
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
	$table			=	"employee_details";
	$currentDay		=	$today_day;
	$currentMonth	=	$today_month;
	$currentYear	=	$today_year;
	$printFor		=	"";
	$printFor1		=	"";
	$queryString	=	"";
	$queryString1	=	"";
	$whereClause	=	"WHERE isActive=1";
	$orderBy		=	"firstName";
	$showForm		=	false;
	$employeeType	=	0;
	$underManager	=	0;
	$employeeId		=	0;
	$a_employeeId	=	array();
	$formText		=	"SALARY";
	$a_managers		=	$employeeObj->getAllEmployeeManager();

	$form		=	SITE_ROOT_EMPLOYEES."/forms/search-employee-type-month.php";

	$text		=	"";
	$text1		=	"";
	if(isset($_POST['formSubmitted']))
	{
		$departmentId	=	$_POST['departmentId'];
		$month			=	$_POST['month'];
		$year			=	$_POST['year'];
		$employeeType	=	$_POST['employeeType'];
		$underManager	=	$_POST['underManager'];
		$showForm		=	true;
		$redirectLink	=	"month=".$month."&year=".$year."&departmentId=".$departmentId."&employeeType=".$employeeType."&underManager=".$underManager;
		if(isset($_POST['employeeId']))
		{
			$a_employeeId		=	$_POST['employeeId'];
		}
		if(!empty($a_employeeId))
		{
			if(!in_array("0",$a_employeeId))
			{
				$searchEmployee	=	implode(",",$a_employeeId);
				$redirectLink  .=   "&employeeId=".$searchEmployee;

			}
		}
		
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/employee-salary-sheet.php?".$redirectLink);
		exit();

	}
	if(isset($_REQUEST['departmentId']))
	{
		$departmentId	=	$_REQUEST['departmentId'];
	}
	if(isset($_REQUEST['month']))
	{
		$month			=	$_REQUEST['month'];
		$showForm		=	true;
	}
	if(isset($_REQUEST['year']))
	{
		$year			=	$_REQUEST['year'];
	}
	if(isset($_REQUEST['employeeType']))
	{
		$employeeType	=	$_REQUEST['employeeType'];
		if(!empty($employeeType))
		{
			$andClause2	   .=	" AND employee_details.employeeType=$employeeType";
			$text1		   .=	" for ".$a_inetExtEmployee[$employeeType]." employees";
			$queryString1  .=   "&employeeType=".$employeeType;
			$printFor1     .=   "&employeeType=".$employeeType;
		}
	}
	if(isset($_REQUEST['underManager']))
	{
		$underManager	=	$_REQUEST['underManager'];
		if(!empty($underManager))
		{
			$andClause2	   .=	" AND employee_details.underManager=$underManager";
			$text1		   .=	" under manager ".$a_managers[$underManager];
			$queryString1  .=   "&underManager=".$underManager;
			$printFor1     .=   "&underManager=".$underManager;
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
				$queryString1  .=   "&employeeId=".$searchEmployee;
				$text1		   .=	" multiple employees";
				$printFor1     .=   "&employeeId=".$searchEmployee;
				$a_employeeId	=	explode(",",$searchEmployee);
			}
			else
			{
				$andClause2    .=	" AND employee_details.employeeId = $searchEmployee";
				$queryString1  .=   "&employeeId=".$a_employeeId;
				$employeeName	=	$employeeObj->getEmployeeName($searchEmployee);
				$text1		   .=	" for employee ".$employeeName;
				$printFor1     .=   "&employeeId=".$searchEmployee;
				$a_employeeId[]	=	$searchEmployee;
			}
		}
	}
	if($departmentId== 1)
	{
		$table		    =	"employee_details INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId";
		$display		=	"none";
		$display1		=	"";
		$display2		=	"none";
		$display3		=	"none";
		$andClause3		=	" AND departmentId=1";
		$andClause2	   .=	" AND departmentId=1";
	}
	elseif($departmentId== 2)
	{
		$table		    =	"employee_details INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId";
		$display		=	"none";
		$display1		=	"none";
		$display2		=	"";
		$display3		=	"none";
		$andClause3		=	" AND departmentId=2";
		$andClause2	   .=	" AND departmentId=2";
	}
	elseif($departmentId== 3)
	{
		$table		    =	"employee_details";
		$display		=	"none";
		$display1		=	"none";
		$display2		=	"none";
		$display3		=	"";
		$andClause3		=	" AND departmentId=3";
		$andClause2	   .=	" AND hasPdfAccess=1";
	}

	$queryString		=	"&departmentId=".$departmentId."&month=".$month."&year=".$year.$queryString1;

	$monthText			=	$a_month[$month];
	$printFor			=	"month=$month&year=$year&departmentId=$departmentId".$printFor1;
	include($form);
?>
<br>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
<?php
	if($showForm)
	{
?>
	<tr>
		<td class="title1">VIEW SALARY GIVEN FOR <?php echo $monthText.",".$year." ".$text." ".$text1;?>  </td>
	</tr>
</table>
<br>
<table width="98%" border="0" cellpadding="0" cellspacing="2" align="center">
<tr>
	<td colspan="12">
		<a href="<?php echo SITE_URL_EMPLOYEES;?>/print-employee-salary-sheet.php?<?php echo $printFor;?>" class="link_style9">PRINT THIS REPORT IN EXCEL SHEET</a>
	</td>
</tr>
<tr>
	<td colspan="12">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<tr>
	<td width="8%" class="smalltext1"><b>Sr No</b></td>
	<td width="20%" class="smalltext1"><b>Employee Name</b></td>
	<td width="15%" class="smalltext1"><b>Fixed Salary</b></td>
	<td width="15%" class="smalltext1"><b>Total</b></td>
	<td width="15%" class="smalltext1"><b>TDS Deducting</b></td>
	<td width="15%" class="smalltext1"><b>PF Deducting</b></td>
	<td class="smalltext1"><b>Gross Salary</b></td>
</tr>
<tr>
	<td colspan="12">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<?php
	if(isset($_REQUEST['recNo']))
	{
		$recNo		    =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo	=	0;
	}
	
	$start					  =	0;
	$recsPerPage	          =	20;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause.$andClause1.$andClause2;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	$table;
	$pagingObj->selectColumns = "employee_details.employeeId,firstName,lastName";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/employee-salary-sheet.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$showSummary	=	true;
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();

		$i			=	$recNo;
		while($row	=   mysql_fetch_assoc($recordSet))
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
?>
<tr>
	<td class="textstyle" valign="top"><?php echo $i;?>)</td>
	<td class="textstyle" valign="top"><?php echo $employeeName;?></td>
	<td class="textstyle" valign="top"><?php echo $totalFixedSalary;?></td>
	<td class="textstyle" valign="top"><?php echo $lineOrderFixedTotal;?></td>
	<td class="textstyle" valign="top"><?php echo $totalTdsDeduction;?></td>
	<td class="textstyle" valign="top"><?php echo $totalPfDeduction;?></td>
	<td class="textstyle" valign="top"><?php echo $totalSalaryGiven;?></td>
</tr>
<tr>
	<td colspan="12">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<?php
}
?>
<tr>
	<td colspan="33">
		<a href="<?php echo SITE_URL_EMPLOYEES;?>/print-employee-salary-sheet.php?<?php echo $printFor;?>" class="link_style9">PRINT THIS REPORT IN EXCEL SHEET</a>
	</td>
</tr>
<?php
	echo "<tr><td colspan='33'><table width='90%' border='0' ><tr height=20><td align=center><font color='#000000'>";
	$pagingObj->displayPaging($queryString);
	echo "<b>Total Records : " . $totalRecords . "</font></b></td></tr></table></td></tr>";

	}
}
else
{
	echo "<tr><td height='200' class='error' align='center'><b>Please Submit The Above Form !!</b></td></tr>";
}
echo "</table>";
include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>