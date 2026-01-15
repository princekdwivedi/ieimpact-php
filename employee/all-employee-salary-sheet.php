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
	$pagingObj					=	new Paging();
	$employeeObj				=	new employee();

	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");

	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$month			=	date("m");
	$year			=	date("Y");
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
	$printFor		=	"";
	$printFor1		=	"";
	
	$showForm		=	false;
	$employeeType	=	0;
	$underManager	=	0;
	$employeeId		=	0;
	$a_employeeId	=	array();
	$formText		=	"SALARY";
	$monthText		=	"";
	$monthText1		=	"";
	$a_managers		=	$employeeObj->getAllEmployeeManager();

		
	//$form			=	SITE_ROOT_EMPLOYEES."/forms/search-employee-type-month.php";
	$form			=	SITE_ROOT_EMPLOYEES."/forms/search-finanacial-salary.php";

	$text			=	"";
	$text1			=	"";
	if(isset($_POST['formSubmitted']))
	{
		//pr($_POST);
		$departmentId	=	$_POST['departmentId'];
		$month			=	$_POST['month'];
		$year			=	$_POST['year'];
		$toMonth		=	$_POST['toMonth'];
		$toYear			=	$_POST['toYear'];
		$employeeType	=	$_POST['employeeType'];
		$underManager	=	$_POST['underManager'];
		$showForm		=	true;
		$redirectLink	=	"month=".$month."&year=".$year."&departmentId=".$departmentId."&employeeType=".$employeeType."&underManager=".$underManager;
		if(!empty($toMonth))
		{
			$redirectLink  .=	$redirectLink."&toMonth=$toMonth";
		}
		if(!empty($toYear))
		{
			$redirectLink	.=	$redirectLink."&toYear=$toYear";
		}
		if(isset($_POST['employeeId'])  && empty($departmentId))
		{
			$a_employeeId		=	$_POST['employeeId'];
		}
		if(isset($_POST['mtEmployeeId']) && $departmentId == 1)
		{
			$mtEmployeeId		=	$_POST['mtEmployeeId'];
			if(!empty($mtEmployeeId))
			{
				$a_employeeId	=	$mtEmployeeId;
			}
		}
		if(isset($_POST['revEmployeeId']) && $departmentId == 2)
		{
			$revEmployeeId		=	$_POST['revEmployeeId'];
			if(!empty($revEmployeeId))
			{
				$a_employeeId	=	$revEmployeeId;
			}
		}
		if(isset($_POST['pdfEmployeeId']) && $departmentId == 3)
		{
			$pdfEmployeeId		=	$_POST['pdfEmployeeId'];
			if(!empty($pdfEmployeeId))
			{
				$a_employeeId	=	$pdfEmployeeId;
			}
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
		header("Location: ".SITE_URL_EMPLOYEES."/all-employee-salary-sheet.php?".$redirectLink);
		exit();
	}
	
	if(isset($_REQUEST['departmentId']))
	{
		$departmentId	=	$_REQUEST['departmentId'];
	}
	if(isset($_REQUEST['month']) && isset($_REQUEST['year']))
	{
		$month			=	$_REQUEST['month'];
		$year			=	$_REQUEST['year'];
		$showForm		=	true;
		if(!empty($month) && !empty($year))
		{
			$serarchFromDate=	$year."-".$month."-01";
			$dateText		=	" for ".$a_month[$month].",".$year;
			$andClause		=	" AND employee_salary_given.salaryPaidForDate='$serarchFromDate'";
			$printFor		=	"month=$month&year=$year";
			if(isset($_REQUEST['toMonth']) && isset($_REQUEST['toYear']))
			{
				$toMonth		=	$_REQUEST['toMonth'];
				$toYear			=	$_REQUEST['toYear'];
				if(!empty($toMonth) && !empty($toYear))
				{
					$dateText		=	" from ".$a_month[$month].",".$year." to ".$a_month[$toMonth].",".$toYear;

					$serarchToDate=	$toYear."-".$toMonth."-31";

					$andClause	  =	" AND employee_salary_given.salaryPaidForDate  >= '$serarchFromDate' AND employee_salary_given.salaryPaidForDate <= '$serarchToDate'";
					$printFor	  =	"month=$month&year=$year&toMonth=$toMonth&toYear=$toYear";
				}
			}
		}
	}

	if($departmentId== 1)
	{
		$display		=	"none";
		$display1		=	"";
		$display2		=	"none";
		$display3		=	"none";
		$andClause1		=	" AND departmentId=1";
		$printFor	   .=	"&departmentId=1";
		$text		   .=	" of department - MT";
	}
	elseif($departmentId== 2)
	{
		$display		=	"none";
		$display1		=	"none";
		$display2		=	"";
		$display3		=	"none";
		$andClause1		=	" AND departmentId=2";
		$printFor	   .=	"&departmentId=2";
		$text		   .=	" of department - Rev";
	}
	elseif($departmentId== 3)
	{
		$display		=	"none";
		$display1		=	"none";
		$display2		=	"none";
		$display3		=	"";
		$andClause1		=	" AND departmentId=3";
		$printFor	   .=	"&departmentId=1";
		$text		   .=	" of department - PDF";
	}
	if(isset($_REQUEST['employeeType']))
	{
		$employeeType	=	$_REQUEST['employeeType'];
		if(!empty($employeeType))
		{
			$andClause2	   .=	" AND employee_details.employeeType=$employeeType";
			$text		   .=	" for ".$a_inetExtEmployee[$employeeType]." employees";
			$printFor     .=   "&employeeType=".$employeeType;
		}
	}
	if(isset($_REQUEST['underManager']))
	{
		$underManager	=	$_REQUEST['underManager'];
		if(!empty($underManager))
		{
			$andClause2	   .=	" AND employee_details.underManager=$underManager";
			$text		   .=	" under manager ".$a_managers[$underManager];
			$printFor      .=   "&underManager=".$underManager;
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
				$printFor      .=   "&employeeId=".$searchEmployee;
				$a_employeeId	=	explode(",",$searchEmployee);
			}
			else
			{
				$andClause2    .=	" AND employee_details.employeeId = $searchEmployee";
				$employeeName	=	$employeeObj->getEmployeeName($searchEmployee);
				$text		   .=	" for employee ".$employeeName;
				$printFor      .=   "&employeeId=".$searchEmployee;
				$a_employeeId[]	=	$searchEmployee;
			}
		}
	}
	
	include($form);

	if($showForm		   ==	true)
	{
		$totalGivenMoney    =	0;
		$totalGivenPf	    =	0;
		$totalGivenTds	    =	0;
		$totalUnDedSalary	=	0;
		$totalDedGrosSalary =	0;
?>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class="title1">VIEW SALARY GIVEN <?php echo $dateText." ".$text;?></td>
	</tr>;
</table>
<?php
		$query	=	"SELECT SUM(fixedSalary) as totalFixedSalary,SUM(totalMoney) as totalOrderLineMoney,SUM(pfMoney) as totalPfMoney,SUM(tdsDeduction) as totalTdsMoney,SUM(salaryGiven) as totalSalaryGiven,employee_salary_given.employeeId,firstName,lastName FROM employee_salary_given INNER JOIN employee_details ON employee_salary_given.employeeId=employee_details.employeeId".$whereClause.$andClause.$andClause1.$andClause2." GROUP BY employee_salary_given.employeeId".$orderBy;
		$result	=	dbQuery($query);
		if(mysql_num_rows($result))
		{
?>
<table width="98%" border="0" cellpadding="0" cellspacing="2" align="center">
<tr>
	<td colspan="12">
		<a href="<?php echo SITE_URL_EMPLOYEES;?>/print-all-employee-salary-sheet.php?<?php echo $printFor;?>" class="link_style9" target="_blank">PRINT THIS REPORT IN EXCEL SHEET</a>
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
	<td width="15%" class="smalltext1"><b>Total Salary</b></td>
	<td width="15%" class="smalltext1"><b>TDS Deducting</b></td>
	<td width="15%" class="smalltext1"><b>PF Deducting</b></td>
	<td width="15%" class="smalltext1"><b>Gross Salary</b></td>
	<td class="smalltext1"><b>Salary Given</b></td>
</tr>
<tr>
	<td colspan="12">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<?php
			$i			=	0;
			while($row	=	mysql_fetch_assoc($result))
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
	?>
<tr>
	<td class="textstyle"><?php echo $i;?>)</td>
	<td class="textstyle"><?php echo $employeeName;?></td>
	<td class="textstyle"><?php echo $totalOrderLineMoney;?></td>
	<td class="textstyle"><?php echo $totalTdsMoney;?></td>
	<td class="textstyle"><?php echo $totalPfMoney;?></td>
	<td class="textstyle"><?php echo $grossSalary;?></td>
	<td class="textstyle"><?php echo $totalSalaryGiven;?></td>
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
	<td class="text" colspan="2" align="right">Total :&nbsp;</td>
	<td class="text"><?php echo $totalUnDedSalary;?></td>
	<td class="text"><?php echo $totalGivenTds;?></td>
	<td class="text"><?php echo $totalGivenPf;?></td>
	<td class="text"><?php echo $totalDedGrosSalary;?></td>
	<td class="text"><?php echo $totalGivenMoney;?></td>
</tr>
<?php
		}
		else
		{
			echo "<table><tr><td height='200' class='error' align='center'><b>No Record Found !!</b></td></tr></table>";
		}
	}
	else
	{
		echo "<table><tr><td height='200' class='error' align='center'><b>Please Submit The Above Form !!</b></td></tr></table>";
	}

include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>