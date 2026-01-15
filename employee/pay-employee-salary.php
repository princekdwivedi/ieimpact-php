<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");
	$employeeObj				=	new employee();
	$validator					=	new validate();
	$employeeId					=	0;
	$salaryeId					=	0;
	$employeeName				=	"";
	$errorMsg					=	"";
	$month						=	0;
	$year						=	0;
	$departmentId				=	0;
	$monthText					=	"";
	$employeeTypeText			=	"NOT YET DEFINED";
	$departmentText				=	"";
	$display					=	"none";
	$display1					=	"none";
	if(!$s_hasManagerAccess)
	{
		echo "<script>window.close();</script>";
	}
	if(isset($_GET['employeeId']) && isset($_GET['month']) && isset($_GET['year'])  && isset($_GET['departmentId']))
	{
		$employeeId				=	$_GET['employeeId'];
		$month					=	$_GET['month'];
		$year					=	$_GET['year'];
		$departmentId			=	$_GET['departmentId'];
		if($month < 10)
		{
			$month				=	"0".$month;
		}
		$employeeName			=	$employeeObj->getEmployeeName($employeeId);
		$monthText				=	$a_month[$month];
		$departmentText			=	$a_newDepartment[$departmentId];
		if(!empty($employeeId) && !empty($month) && !empty($year) && !empty($departmentId))
		{
			$employeeType		=	@mysql_result(dbQuery("SELECT employeeType FROm employee_details WHERE employeeID=$employeeId"),0);
			if(!empty($employeeType))
			{
				$employeeTypeText	=	$a_inetExtEmployee[$employeeType];
			}
			$query				=	"SELECT * FROM employee_salary_given WHERE employeeId=$employeeId AND month=$month AND year=$year AND departmentId=$departmentId";
			$result			=   dbQuery($query);
			if(mysql_num_rows($result))
			{
				$row			=   mysql_fetch_assoc($result);
				$salaryId		=	$row['salaryId'];
				$salaryGiven	=	$row['salaryGiven'];
				$remarks		=	$row['remarks'];
				$isPaidSalary	=	$row['isPaidSalary'];
				$fixedSalary	=   $row['fixedSalary'];
				$totalLine		=   $row['totalLine'];
				$totalMoney		=   $row['totalMoney'];
				$tdsPercentage	=   $row['tdsPercentage'];
				$tdsDeduction	=   $row['tdsDeduction'];
				$givenThrough	=   $row['givenThrough'];
				$remarks		=   $row['remarks'];
				$transactionId	=   $row['transactionId'];
				$chequeNo		=   $row['chequeNo'];
				$checkBank		=   $row['checkBank'];
				$givenOn		=	$row['givenOn'];
				$checkDate		=   $row['checkDate'];
				$pfMoney		=   $row['pfMoney'];
				
				$text			=	"Edit salary of ".$employeeName." for ".$monthText.",".$year;
				if(empty($fixedSalary))
				{
					$fixedSalary	=	"";
				}
				if(empty($tdsDeduction))
				{
					$tdsDeduction	=	"";
				}
				if(empty($salaryGiven))
				{
					$salaryGiven	=	"";
				}
				if(empty($pfMoney))
				{
					$pfMoney	=	"";
				}
				if($givenThrough==  2)
				{
					$display	=	"";
					$display1	=	"none";
				}
				elseif($givenThrough==  3)
				{
					$display	=	"none";
					$display1	=	"";
				}
			}
			else
			{
				$text			=	"Pay salary of ".$employeeName." for ".$monthText.",".$year;
				$salaryId		=	0;
				$salaryGiven	=	"";
				$remarks		=	"";
				$isPaidSalary	=	0;
				$fixedSalary	=   "";
				$tdsPercentage	=   1;
				$tdsDeduction	=   "";
				$givenThrough	=   0;
				$transactionId	=   "";
				$chequeNo		=   "";
				$checkBank		=   "";
				$checkDate		=   "";
				$givenOn		=	"";
				$pfMoney		=	"";
				if($departmentId== 1)
				{
					$totalMoney	=	@mysql_result(dbQuery("SELECT SUM(totalDirectTrascriptionMoney+totalIndirectTrascriptionMoney+totalDirectVreMoney+totalIndirectVreMoney+totalDirectQaMoney+totalIndirectQaMoney+totalDirectAuditMoney+totalIndirectAuditMoney) FROM datewise_employee_works_money WHERE ID > ".MAX_SEARCH_MT_EMPLOYEE_WORKID." AND MONTH(workedOnDate)=$month AND YEAR(workedOnDate)=$year AND employeeId=$employeeId AND departmentId=1"),0);
				}
				elseif($departmentId== 2)
				{
					$totalMoney	=	@mysql_result(dbQuery("SELECT SUM(totalDirectLevel1Money+totalDirectLevel2Money+totalIndirectLevel1Money+totalIndirectLevel2Money+totalQaLevel1Money+totalQaLevel2Money+totalAuditLevel1Money+totalAuditLevel2Money) FROM datewise_employee_works_money WHERE ID > ".MAX_SEARCH_MT_EMPLOYEE_WORKID." AND MONTH(workedOnDate)=$month AND YEAR(workedOnDate)=$year AND employeeId=$employeeId AND departmentId=2"),0);
				}
				if(empty($totalLine))
				{
					$totalLine	=	0;
				}
				if(empty($totalMoney))
				{
					$totalMoney	=	0;
				}
			}
			if($departmentId== 1)
			{
				$totalLine	=	@mysql_result(dbQuery("SELECT SUM(totalDirectTrascriptionLines+totalIndirectTrascriptionLines+totalDirectVreLines+totalIndirectVreLines+totalQaLines+totalIndirectQaLines+totalDirectAuditLines+totalIndirectAuditLines) FROM datewise_employee_works_money WHERE ID > ".MAX_SEARCH_MT_EMPLOYEE_WORKID." AND MONTH(workedOnDate)=$month AND YEAR(workedOnDate)=$year AND employeeId=$employeeId AND departmentId=1"),0);
			}
			elseif($departmentId== 2)
			{
				$totalLine	=	@mysql_result(dbQuery("SELECT SUM(totalDirectLevel1Lines+totalDirectLevel2Lines+totalIndirectLevel1Lines+totalIndirectLevel2Lines+totalQaLevel1Lines+totalQaLevel2Lines+totalAuditLevel1Lines+totalAuditLevel2Lines) FROM datewise_employee_works_money WHERE ID > ".MAX_SEARCH_MT_EMPLOYEE_WORKID." AND MONTH(workedOnDate)=$month AND YEAR(workedOnDate)=$year AND employeeId=$employeeId AND departmentId=2"),0);
			}
			$totalLineFixedMoney	=	$fixedSalary+$totalMoney;
		}
		else
		{
			echo "<script>window.close();</script>";
		}
	}
	else
	{
		echo "<script>window.close();</script>";
	}
	$form		=	SITE_ROOT_EMPLOYEES."/forms/pay-employee-salary.php";
?>
<script type="text/javascript">
function reflectChange()
{
	window.opener.location.reload();
}
</script>
<html>
<head>
<title>
	<?php echo $text;?>
</title>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
<body>
<center>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="textstyle1">
				<b><?php echo $text;?></b>
			</td>
		</tr>
	</table>
	<?php 
		if(isset($_REQUEST['formSubmitted']))
		{
			extract($_REQUEST);
			$remarks		=	makeDBSafe($remarks);
			$chequeNo		=	makeDBSafe($chequeNo);
			$checkBank		=	makeDBSafe($checkBank);
			$transactionId	=	makeDBSafe($transactionId);
			if(empty($fixedSalary))
			{
				$fixedSalary	=	0;
			}
			if($employeeType	== 2)
			{
				$tdsPercentage	=	1;
			}
			else
			{
				$tdsPercentage	=	0;
			}
			if(empty($tdsDeduction))
			{
				$tdsDeduction	=	0;
			}
			if(empty($pfMoney))
			{
				$pfMoney		=	0;
			}

			if($month < 10)
			{
				$dateMonth	=	"0".$month;
			}
			else
			{
				$dateMonth	=	$month;
			}

			$salaryPaidForDate	=	$year."-".$dateMonth."-01";


			$optionQuery	=	" SET employeeId=$employeeId,departmentId=$departmentId,fixedSalary='$fixedSalary',totalLine=$totalLine,totalMoney='$totalMoney',tdsPercentage=$tdsPercentage,tdsDeduction='$tdsDeduction',pfMoney='$pfMoney',month=$month,year=$year,salaryGiven='$salaryGiven',givenThrough=$givenThrough,givenOn='$givenOn',remarks='$remarks',salaryPaidForDate='$salaryPaidForDate'";
			if(empty($salaryId))
			{
				$query		=	"INSERT INTO employee_salary_given".$optionQuery.",isPaidSalary=1,addedOn='".CURRENT_DATE_INDIA."',addedBy=$s_employeeId";
				dbQuery($query);
				$salaryId	=	mysql_insert_id();
			}
			else
			{
				$query		=	"UPDATE employee_salary_given".$optionQuery.",editedOn='".CURRENT_DATE_INDIA."',editedBy=$s_employeeId WHERE salaryId=$salaryId";
				dbQuery($query);
			}
			if($givenThrough == 1 || $givenThrough == 4)
			{
				dbQuery("UPDATE employee_salary_given SET transactionId='',chequeNo='',checkBank='',checkDate='' WHERE salaryId=$salaryId");
			}
			elseif($givenThrough == 2)
			{
				dbQuery("UPDATE employee_salary_given SET transactionId='',chequeNo='$chequeNo',checkBank='$checkBank',checkDate='$checkDate' WHERE salaryId=$salaryId");
			}
			elseif($givenThrough == 3)
			{
				dbQuery("UPDATE employee_salary_given SET transactionId='$transactionId',chequeNo='',checkBank='',checkDate='' WHERE salaryId=$salaryId");
			}

			echo "<script type='text/javascript'>reflectChange();</script>";

			echo "<script>window.close();</script>";
		}
		include($form);
	?>

<br><br>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
</center>
</body>
</html>