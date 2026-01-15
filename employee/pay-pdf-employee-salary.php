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
	if(isset($_GET['employeeId']) && isset($_GET['month']) && isset($_GET['year']))
	{
		$employeeId				=	$_GET['employeeId'];
		$month					=	$_GET['month'];
		$year					=	$_GET['year'];
		if($month < 10)
		{
			$month				=	"0".$month;
		}
		$employeeName			=	$employeeObj->getEmployeeName($employeeId);
		$monthText				=	$a_month[$month];
		if(!empty($employeeId) && !empty($month) && !empty($year))
		{
			$employeeType		=	@mysql_result(dbQuery("SELECT employeeType FROm employee_details WHERE employeeID=$employeeId"),0);
			if(!empty($employeeType))
			{
				$employeeTypeText	=	$a_inetExtEmployee[$employeeType];
			}
			$query				=	"SELECT * FROM employee_salary_given WHERE employeeId=$employeeId AND month=$month AND year=$year AND departmentId=3";
			$result			=   dbQuery($query);
			if(mysql_num_rows($result))
			{
				$row			=   mysql_fetch_assoc($result);
				$salaryId		=	$row['salaryId'];
				$salaryGiven	=	$row['salaryGiven'];
				$remarks		=	$row['remarks'];
				$isPaidSalary	=	$row['isPaidSalary'];
				$fixedSalary	=   $row['fixedSalary'];
				$totalPdfOrder	=   $row['totalPdfOrder'];
				$totalPdfMoney	=   $row['totalPdfMoney'];
				$totalQaOrder	=   $row['totalQaOrder'];
				$totalQaMoney	=   $row['totalQaMoney'];
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
				$totalPdfOrder	=   0;
				$totalPdfMoney	=   0;
				$totalQaOrder	=   0;
				$totalQaMoney	=   0;
				
					
				$query	=	"SELECT orderId FROM members_orders WHERE acceptedBy=$employeeId AND MONTH(assignToEmployee)=".$month." AND YEAR(assignToEmployee)=".$year;
				$result	=	dbQuery($query);
				if(mysql_num_rows($result))
				{
					while($row1		=	mysql_fetch_assoc($result))
					{
						$orderId				=	$row1['orderId'];
						$a_pendingIds[$orderId]	=	$orderId;
					}
				}
				if(!empty($a_pendingIds))
				{
					$pendingIds			=	implode(",",$a_pendingIds);

					$totalPdfOrder		=	@mysql_result(dbQuery("SELECT COUNT(replyId) FROM members_orders_reply WHERE hasRepliedFileUploaded=1 AND orderId IN ($pendingIds)"),0);
					if(empty($totalPdfOrder))
					{
						$totalPdfOrder	=	0;
					}
				}  
				
				$totalQaOrder	=	@mysql_result(dbQuery("SELECT COUNT(*) FROM members_orders_reply WHERE hasQaDone=1 AND qaDoneBy=$employeeId AND MONTH(qaDoneOn)=".$month." AND YEAR(qaDoneOn)=".$year),0);
				if(empty($totalQaOrder))
				{
					$totalQaOrder	=	0;
				}
				$allTotalOrders		=	$totalPdfOrder+$totalQaOrder;
				if(!empty($allTotalOrders))
				{
					if(!empty($totalPdfOrder))
					{
						$totalPdfMoney =	$employeeObj->getProcessPdfTotalMoney($employeeId,$month,$year);
						if(empty($totalPdfMoney))
						{
							$totalPdfMoney=	0;
						}
					}
					if(!empty($totalQaOrder))
					{
						$totalQaMoney	 =	$employeeObj->getQaPdfTotalMoney($employeeId,$month,$year);
						if(empty($totalQaMoney))
						{
							$totalQaMoney=	0;
						}
					}
				}
				else
				{
					$totalPdfMoney	=	0;
					$totalQaMoney	=	0;
				}
			}
			$totalMoney		=	$totalPdfMoney+$totalQaMoney;
			if(empty($totalMoney))
			{
				$totalMoney	=	0;
			}
			$totalFixedOrdersMoney	=	$fixedSalary+$totalMoney;
			$salaryGiven			=	$totalFixedOrdersMoney;
			
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
	$form		=	SITE_ROOT_EMPLOYEES."/forms/pay-pdf-employee-salary.php";
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

			$optionQuery	=	" SET employeeId=$employeeId,departmentId=3,fixedSalary='$fixedSalary',totalMoney='$totalMoney',tdsPercentage=$tdsPercentage,tdsDeduction='$tdsDeduction',pfMoney='$pfMoney',month=$month,year=$year,salaryGiven='$salaryGiven',givenThrough=$givenThrough,givenOn='$givenOn',remarks='$remarks',totalPdfOrder='$totalPdfOrder',totalPdfMoney='$totalPdfMoney',totalQaOrder='$totalQaOrder',totalQaMoney='$totalQaMoney',salaryPaidForDate='$salaryPaidForDate'";
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