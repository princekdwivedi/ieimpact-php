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
	$a_newDepartment=	array("1"=>"MT","2"=>"REV");
	$month			=	date("m");
	$year			=	date("Y");
	$andClause		=	"";
	$andClause1		=	"";
	$employeeName	=	"";
	$departmentId	=   3;
	$currentDay		=	$today_day;
	$currentMonth	=	$today_month;
	$currentYear	=	$today_year;
	$printFor		=	"";
	$printFor1		=	"";
	$queryString	=	"";
	$queryString1	=	"";
	$whereClause	=	"WHERE isActive=1 AND hasPdfAccess=1";
	$orderBy		=	"firstName";
	$showForm		=	false;
	$employeeType	=	0;
	$underManager	=	0;
	$employeeId		=	0;
	$totalQaMoney	=	0;
	$a_employeeId	=	array();
	$a_managers		=	$employeeObj->getAllEmployeeManager();
	$formText		=	"PDF SALARY";
	
	$form		=	SITE_ROOT_EMPLOYEES."/forms/pdf-employee-type-month.php";

	$text		=	"";
	$text1		=	"";
	if(isset($_POST['formSubmitted']))
	{
		$month			=	$_POST['month'];
		$year			=	$_POST['year'];
		$employeeType	=	$_POST['employeeType'];
		$underManager	=	$_POST['underManager'];
		$a_employeeId	=	$_POST['employeeId'];
		$showForm		=	true;
		$redirectLink	=	"month=".$month."&year=".$year."&employeeType=".$employeeType."&underManager=".$underManager;
		if(!empty($a_employeeId))
		{
			if(!in_array("0",$a_employeeId))
			{
				$searchEmployee	=	implode(",",$a_employeeId);
				$redirectLink  .=   "&employeeId=".$searchEmployee;

			}
		}
		
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/pdf-employee-salary.php?".$redirectLink);
		exit();
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
			$andClause	   .=	" AND employee_details.employeeType=$employeeType";
			$text1		   .=	" for ".$a_inetExtEmployee[$employeeType]." employees";
			$queryString   .=   "&employeeType=".$employeeType;
		}
	}
	if(isset($_REQUEST['underManager']))
	{
		$underManager	=	$_REQUEST['underManager'];
		if(!empty($underManager))
		{
			$andClause	   .=	" AND employee_details.underManager=$underManager";
			$text		   .=	" under manager ".$a_managers[$underManager];
			$queryString   .=   "&underManager=".$underManager;
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
				$andClause     .=	" AND employee_details.employeeId IN ($searchEmployee)";
				$queryString   .=   "&employeeId=".$searchEmployee;
				$text		   .=	" multiple employees";
				$a_employeeId	=	explode(",",$searchEmployee);
			}
			else
			{
				$andClause     .=	" AND employee_details.employeeId = $searchEmployee";
				$queryString   .=   "&employeeId=".$a_employeeId;
				$employeeName	=	$employeeObj->getEmployeeName($searchEmployee);
				$text 		   .=	" for employee ".$employeeName;
				$a_employeeId[]	=	$searchEmployee;
			}
		}
	}
	
	$monthText			=	$a_month[$month];
	$queryString		=	"&month=".$month."&year=".$year.$queryString;
	include($form);
?>
<br>
<?php
	if($showForm)
	{
?>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
<tr>
		<td class="title1">VIEW PDF DEPARTMENT SALARY FOR <?php echo $monthText.",".$year." ".$text;?>  </td>
	</tr>
</table>
<br>
<script type="text/javascript">
function openSalaryWidow(employeeId,month,year)
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/pay-pdf-employee-salary.php?employeeId="+employeeId+"&month="+month+"&year="+year;
	prop = "toolbar=no,scrollbars=yes,width=750,height=600,top=100,left=100";
	window.open(path,'',prop);
}
function openViewSalaryWidow(employeeId,month,year,salaryId)
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/view-pdf-salary-given.php?employeeId="+employeeId+"&month="+month+"&year="+year+"&salaryId="+salaryId;
	prop = "toolbar=no,scrollbars=yes,width=700,height=500,top=100,left=100";
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
	<td width="5%" class="smalltext1"><b>Sr No</b></td>
	<td width="15%" class="smalltext1"><b>Employee Name</b></td>
	<td width="13%" class="smalltext1"><b>Total Processed Order</b></td>
	<td width="10%" class="smalltext1"><b>Total QA Done</b></td>
	<td width="10%" class="smalltext1"><b>Total Money</b></td>
	<td width="15%" class="smalltext1"><b>Salary Status</b></td>
	<td width="20%" class="smalltext1"><b>Remarks</b></td>
	<td class="smalltext1"><b>Action</b></td>
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
	$pagingObj->whereClause   =	$whereClause.$andClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"employee_details";
	$pagingObj->selectColumns = "*";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/pdf-employee-salary.php";
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
			$totalOrders	=	0;
			$totalReplied	=	0;
			$totalQaOrders	=	0;
			$a_pendingIds	=   array();
			$lastName		=	stripslashes($row['lastName']);
			$employeeId		=	$row['employeeId'];
			$firstName		=	stripslashes($row['firstName']);
			$employeeName	=	$firstName." ".$lastName;
			$employeeName	=	ucwords($employeeName);

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

				$totalReplied		=	@mysql_result(dbQuery("SELECT COUNT(replyId) FROM members_orders_reply WHERE hasRepliedFileUploaded=1 AND orderId IN ($pendingIds)"),0);
				if(empty($totalReplied))
				{
					$totalReplied	=	0;
				}
			}  
			
			$totalQaOrders	=	@mysql_result(dbQuery("SELECT COUNT(*) FROM members_orders_reply WHERE hasQaDone=1 AND qaDoneBy=$employeeId AND MONTH(qaDoneOn)=".$month." AND YEAR(qaDoneOn)=".$year),0);
			if(empty($totalQaOrders))
			{
				$totalQaOrders	=	0;
			}
			$totalOrders		=	$totalReplied+$totalQaOrders;
			if(!empty($totalOrders))
			{
				if(!empty($totalReplied))
				{
					$totalOrderMoney =	$employeeObj->getProcessPdfTotalMoney($employeeId,$month,$year);
					if(empty($totalOrderMoney))
					{
						$totalOrderMoney=	0;
					}
				}
				if(!empty($totalQaOrders))
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
				$totalOrderMoney	=	0;
				$totalQaMoney		=	0;
			}
			$totalMoney		=	$totalOrderMoney+$totalQaMoney;

			$query2			=	"SELECT * FROM employee_salary_given WHERE employeeId=$employeeId AND month=$month AND year=$year AND departmentId=3";
			$result2		=   dbQuery($query2);
			if(mysql_num_rows($result2))
			{
				$row2			=   mysql_fetch_assoc($result2);
				$salaryId		=	$row2['salaryId'];
				$salaryGiven	=	$row2['salaryGiven'];
				$remarks		=	$row2['remarks'];
				$isPaidSalary	=	$row2['isPaidSalary'];
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
	<td class="textstyle" valign="top"><b><?php echo $i;?>)</b></td>
	<td class="textstyle" valign="top"><b><?php echo $employeeName;?></b></td>
	<td class="textstyle" valign="top"><b><?php echo $totalReplied;?></b></td>
	<td class="textstyle" valign="top"><b><?php echo $totalQaOrders;?></b></td>
	<td class="textstyle" valign="top"><b><?php echo $totalMoney;?></b></td>
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
	<td class="textstyle" valign="top">
		<?php
			if(!empty($totalMoney))
			{
				if(empty($salaryId))
				{	
					echo "<a href='javascript:openSalaryWidow($employeeId,$month,$year)' class='link_style12'>Pay now</a>";
				}
				elseif(!empty($salaryId))
				{
					echo "<a href='javascript:openSalaryWidow($employeeId,$month,$year)' class='link_style12'>Edit</a> | ";
					echo "<a href='javascript:openViewSalaryWidow($employeeId,$month,$year,$salaryId)' class='link_style12'>View</a>";
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
	echo "<tr><td colspan='10'><table width='90%' border='0' ><tr height=20><td align=center><font color='#000000'>";
	$pagingObj->displayPaging($queryString);
	echo "<b>Total Records : " . $totalRecords . "</font></b></td></tr></table></td></tr>";
	}
	echo "</table>";
}
else
{
	echo "<table><tr><td height='200' class='error' align='center'><b>Please Submit The Above Form !!</b></td></tr></table>";
}
include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>