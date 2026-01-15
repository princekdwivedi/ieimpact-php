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
	$a_newDepartment=	array("1"=>"MT","2"=>"REV");
	$month			=	date("m");
	$year			=	date("Y");
	$andClause		=	"";
	$andClause1		=	"";
	$andClause2		=	"";
	$employeeName	=	"";
	$departmentId	=   0;
	$display		=	"";
	$display1		=	"none";
	$display2		=	"none";
	$display3		=	"none";
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
	$a_managers		=	$employeeObj->getAllEmployeeManager();
	$formText		=	"SALARY";
	$table		    =	"employee_details INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId";

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
		if(!empty($a_employeeId))
		{
			if(!in_array("0",$a_employeeId))
			{
				$searchEmployee	=	implode(",",$a_employeeId);
				$redirectLink  .=   "&employeeId=".$searchEmployee;

			}
		}
		
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/view-employee-salary.php?".$redirectLink);
		exit();
	}
	if(isset($_REQUEST['departmentId']))
	{
		$departmentId	=	$_REQUEST['departmentId'];
		if($departmentId== 1)
		{
			$display		=	"none";
			$display1		=	"";
			$display2		=	"none";
			$display3		=	"none";
			$text		   .=	" MT department";
			$andClause2	   .=	" AND departmentId=1";
		}
		elseif($departmentId== 2)
		{
			$display		=	"none";
			$display1		=	"none";
			$display2		=	"";
			$display3		=	"none";
			$text		   .=	" REV department";
			$andClause2	   .=	" AND departmentId=2";
		}
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
	
	$monthText			=	$a_month[$month];
	$queryString		=	"&departmentId=".$departmentId."&month=".$month."&year=".$year.$queryString1;
	include($form);
?>
<br>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
<?php
	if($showForm)
	{
?>
	<tr>
		<td class="title1">VIEW SALARY FOR <?php echo $monthText.",".$year." ".$text." ".$text1;?>  </td>
	</tr>
</table>
<br>
<script type="text/javascript">
function openSalaryWidow(employeeId,month,year,departmentId)
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/pay-employee-salary.php?employeeId="+employeeId+"&month="+month+"&year="+year+"&departmentId="+departmentId;
	prop = "toolbar=no,scrollbars=yes,width=750,height=600,top=100,left=100";
	window.open(path,'',prop);
}
</script>
<table width="98%" border="0" cellpadding="0" cellspacing="2" align="center">
<!-- <tr>
	<td colspan="33">
		<a href="<?php echo SITE_URL_EMPLOYEES;?>/print-monthly-attendence.php?<?php echo $printFor;?>" class="link_style9">PRINT THIS REPORT IN EXCEL SHEET</a>
	</td>
</tr> -->
<tr>
	<td colspan="10">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<tr>
	<td width="5%" class="smalltext1">Sr No</td>
	<td width="13%" class="smalltext1">Employee Name</td>
	<td width="13%" class="smalltext1">Total Lines</td>
	<td width="13%" class="smalltext1">Total Money</td>
	<td width="13%" class="smalltext1">Salary Status</td>
	<td width="20%" class="smalltext1">Remarks</td>
	<td class="smalltext1">Action</td>
</tr>
<tr>
	<td colspan="10">
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
	$pagingObj->selectColumns = "employee_details.employeeId,firstName,lastName,departmentId";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/view-employee-salary.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$showSummary	=	true;
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();

		$i=$recNo;
		while($row	=   mysql_fetch_assoc($recordSet))
		{
			$i++;
			$lastName		=	stripslashes($row['lastName']);
			$employeeId		=	$row['employeeId'];
			$firstName		=	stripslashes($row['firstName']);
			$departmentId	=	$row['departmentId'];
			$employeeName	=	$firstName." ".$lastName;
			$employeeName	=	ucwords($employeeName);

			if($departmentId== 1)
			{
				$totalLines	=	@mysql_result(dbQuery("SELECT SUM(totalDirectTrascriptionLines+totalIndirectTrascriptionLines+totalDirectVreLines+totalIndirectVreLines+totalQaLines+totalIndirectQaLines+totalDirectAuditLines+totalIndirectAuditLines) FROM datewise_employee_works_money WHERE MONTH(workedOnDate)=$month AND YEAR(workedOnDate)=$year AND employeeId=$employeeId AND departmentId=1"),0);

				$totalMoney	=	@mysql_result(dbQuery("SELECT SUM(totalDirectTrascriptionMoney+totalIndirectTrascriptionMoney+totalDirectVreMoney+totalIndirectVreMoney+totalDirectQaMoney+totalIndirectQaMoney+totalDirectAuditMoney+totalIndirectAuditMoney) FROM datewise_employee_works_money WHERE MONTH(workedOnDate)=$month AND YEAR(workedOnDate)=$year AND employeeId=$employeeId AND departmentId=1"),0);


				
			}
			elseif($departmentId== 2)
			{
				$totalLines	=	@mysql_result(dbQuery("SELECT SUM(totalDirectLevel1Lines+totalDirectLevel2Lines+totalIndirectLevel1Lines+totalIndirectLevel2Lines+totalQaLevel1Lines+totalQaLevel2Lines+totalAuditLevel1Lines+totalAuditLevel2Lines) FROM datewise_employee_works_money WHERE MONTH(workedOnDate)=$month AND YEAR(workedOnDate)=$year AND employeeId=$employeeId AND departmentId=2"),0);

				$totalMoney	=	@mysql_result(dbQuery("SELECT SUM(totalDirectLevel1Money+totalDirectLevel2Money+totalIndirectLevel1Money+totalIndirectLevel2Money+totalQaLevel1Money+totalQaLevel2Money+totalAuditLevel1Money+totalAuditLevel2Money) FROM datewise_employee_works_money WHERE MONTH(workedOnDate)=$month AND YEAR(workedOnDate)=$year AND employeeId=$employeeId AND departmentId=2"),0);
			}
			if(empty($totalLines))
			{
				$totalLines	=	0;
			}
			if(empty($totalMoney))
			{
				$totalMoney	=	0;
			}
			else
			{
				$totalMoney	=	round($totalMoney);
			}
			$query1			=	"SELECT * FROM employee_salary_given WHERE employeeId=$employeeId AND month=$month AND year=$year AND departmentId=$departmentId";
			$result1		=   dbQuery($query1);
			if(mysql_num_rows($result1))
			{
				$row1			=   mysql_fetch_assoc($result1);
				$salaryId		=	$row1['salaryId'];
				$salaryGiven	=	$row1['salaryGiven'];
				$remarks		=	$row1['remarks'];
				$isPaidSalary	=	$row1['isPaidSalary'];
			}
			else
			{
				$salaryId		=	0;
				$salaryGiven	=	"";
				$remarks		=	"";
				$isPaidSalary	=	"";
			}
?>
<tr>
	<td class="textstyle" valign="top"><?php echo $i;?>)</td>
	<td class="textstyle" valign="top"><?php echo $employeeName;?></td>
	<td class="textstyle" valign="top"><?php echo $totalLines;?></td>
	<td class="textstyle" valign="top"><?php echo $totalMoney;?></td>
	<td class="textstyle" valign="top">
		<?php
			if(empty($isPaidSalary))
			{
				echo "<font color='red'>Not Paid</font>";
			}
			elseif(!empty($isPaidSalary) && !empty($salaryGiven))
			{
				echo "<font color='red'>Paid:$salaryGiven Rs/-</font>";
			}
		?>
	</td>
	<td class="textstyle" valign="top">
		<?php echo nl2br($remarks);?>
	</td>
	<td class="smalltext1" valign="top">
		<?php
			if(!empty($totalMoney))
			{
				if(empty($salaryId))
				{	
					echo "<a href='javascript:openSalaryWidow($employeeId,$month,$year,$departmentId)' class='link_style12'>Pay now</a>";
				}
				elseif(!empty($salaryId))
				{
					echo "<a href='javascript:openSalaryWidow($employeeId,$month,$year,$departmentId)' class='link_style12'>Edit</a>";
				}
			}
		?>
	</td>
</tr>
<tr>
	<td colspan="10">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<?php
	
		}
?>
<!-- <tr>
	<td colspan="10">
		<a href="<?php echo SITE_URL_EMPLOYEES;?>/print-monthly-attendence.php?<?php echo $printFor;?>" class="link_style9">PRINT THIS REPORT IN EXCEL SHEET</a>
	</td>
</tr> -->
<?php
	echo "<tr><td colspan='10'><table width='90%' border='0' ><tr height=20><td align=center><font color='#000000'>";
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