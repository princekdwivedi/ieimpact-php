<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	."/includes/common-array.php");
	$employeeObj                = new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if($s_departmentId		!=	2)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$month					=	date("m");
	$year					=	date("Y");
	$currentYear			=	date("Y");
	$andClause				=	"";
	$showForm				=	false;
	$department				=	"Not Yet Added";
	$shift					=	"Not Yet Added";
	
	$totalDirectLevel1		=	0;
	$totalDirectLevel2		=	0;
	$totalIndirectLevel1	=	0;
	$totalIndirectLevel2	=	0;
	$totalQaLevel1			=	0;
	$totalQaLevel2			=	0;
	$totalAuditLevel1		=	0;
	$totalAuditLevel2		=	0;

	$totalLines				=	0;
	$totalMoney				=	0;


	$form		=	SITE_ROOT_EMPLOYEES."/forms/month-year.php";
	if(isset($_POST['formSubmitted']))
	{
		$month		=	$_POST['month'];
		$year		=	$_POST['year'];
		$showForm	=	true;
	}
	$monthText	=	$a_month[$month];
	$monthText	=	strtoupper($monthText);
	include($form);
	/*if($year <= $today_year)
	{
		if($month <= $today_month)
		{
			$showForm	=	true;
		}
	}*/

	$departmentText				=	$a_department[$s_departmentId];
	$shift						=	@mysql_result(dbQuery("SELECT shiftId FROM employee_shift_rates WHERE employeeId=$s_employeeId"),0);
	if(!empty($shift))
	{
		$shiftText				=	$a_shift[$shift];
	}
?>
<br>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class="title1">VIEW TOTAL OF YOUR WORKS IN <?php echo $monthText.",".$year;?>  </td>
	</tr>
</table>
<br>
<?php
	if($showForm)
	{
		$query		=	"SELECT SUM(totalDirectLevel1Lines) AS totalDirectLevel1Lines,SUM(totalDirectLevel1Money) AS totalDirectLevel1Money,SUM(totalDirectLevel2Lines) AS totalDirectLevel2Lines,SUM(totalDirectLevel2Money) AS totalDirectLevel2Money,SUM(totalIndirectLevel1Lines) AS totalIndirectLevel1Lines,SUM(totalIndirectLevel1Money) AS totalIndirectLevel1Money,SUM(totalIndirectLevel2Lines) AS totalIndirectLevel2Lines,SUM(totalIndirectLevel2Money) as totalIndirectLevel2Money,SUM(totalQaLevel1Lines) as totalQaLevel1Lines,SUM(totalQaLevel1Money) as totalQaLevel1Money,SUM(totalQaLevel2Lines) as totalQaLevel2Lines,SUM(totalQaLevel2Money) as totalQaLevel2Money,SUM(totalAuditLevel1Lines) as totalAuditLevel1Lines,SUM(totalAuditLevel1Money) as totalAuditLevel1Money,SUM(totalAuditLevel2Lines) as totalAuditLevel2Lines,SUM(totalAuditLevel2Money) as totalAuditLevel2Money FROM datewise_employee_works_money WHERE employeeId=$s_employeeId AND MONTH(workedOnDate)=$month AND YEAR(workedOnDate)=$year";
		$result	=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			$row							=	mysql_fetch_assoc($result);
			$totalDirectLevel1Lines			=	$row['totalDirectLevel1Lines'];
			$totalDirectLevel2Lines			=	$row['totalDirectLevel2Lines'];
			$totalIndirectLevel1Lines		=	$row['totalIndirectLevel1Lines'];
			$totalIndirectLevel2Lines		=	$row['totalIndirectLevel2Lines'];
			$totalQaLevel1Lines				=	$row['totalQaLevel1Lines'];
			$totalQaLevel2Lines				=	$row['totalQaLevel2Lines'];
			$totalAuditLevel1Lines			=	$row['totalAuditLevel1Lines'];
			$totalAuditLevel2Lines			=	$row['totalAuditLevel2Lines'];

			$totalDirectLevel1Money			=	$row['totalDirectLevel1Money'];
			$totalDirectLevel2Money			=	$row['totalDirectLevel2Money'];
			$totalIndirectLevel1Money		=	$row['totalIndirectLevel1Money'];
			$totalIndirectLevel2Money		=	$row['totalIndirectLevel2Money'];
			$totalQaLevel1Money				=	$row['totalQaLevel1Money'];
			$totalQaLevel2Money				=	$row['totalQaLevel2Money'];
			$totalAuditLevel1Money			=	$row['totalAuditLevel1Money'];
			$totalAuditLevel2Money			=	$row['totalAuditLevel2Money'];
			

			if(empty($totalDirectLevel1Lines))
			{
				$totalDirectLevel1Lines	=	0;
			}
			if(empty($totalDirectLevel2Lines))
			{
				$totalDirectLevel2Lines	=	0;
			}
			if(empty($totalIndirectLevel1Lines))
			{
				$totalIndirectLevel1Lines	=	0;
			}
			if(empty($totalIndirectLevel2Lines))
			{
				$totalIndirectLevel2Lines	=	0;
			}
			if(empty($totalQaLevel1Lines))
			{
				$totalQaLevel1Lines	=	0;
			}
			if(empty($totalQaLevel2Lines))
			{
				$totalQaLevel2Lines	=	0;
			}
			if(empty($totalAuditLevel1Lines))
			{
				$totalAuditLevel1Lines	=	0;
			}
			if(empty($totalAuditLevel2Lines))
			{
				$totalAuditLevel2Lines	=	0;
			}

		}


		$totalLines		=	$totalLines+$totalDirectLevel1Lines+$totalDirectLevel2Lines+$totalIndirectLevel1Lines+$totalIndirectLevel2Lines+$totalQaLevel1Lines+$totalQaLevel2Lines+$totalAuditLevel1Lines+$totalAuditLevel2Lines;

		$totalMoney		=	$totalMoney+$totalDirectLevel1Money+$totalDirectLevel2Money+$totalIndirectLevel1Money+$totalIndirectLevel2Money+$totalQaLevel1Money+$totalQaLevel2Money+$totalAuditLevel1Money+$totalAuditLevel2Money;

		$totalMoney		=	round($totalMoney);


?>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="2" valign="top">
<tr>
	<td width="25%" class="smalltext2"><b>Department</b></td>
	<td width="2%" class="smalltext2"><b>:</td>
	<td class="title" colspan="6">
		<?php echo $departmentText;?>
	</td>
</tr>
<tr>
	<td class="smalltext2"><b>Shift</b></td>
	<td class="smalltext2"><b>:</b></td>
	<td class="title"  colspan="6">
		<?php echo $shiftText;?>
	</td>
</tr>
<tr>
	<td colspan="8" class="title">
		VIEW TOTAL WORK DONE AND TOTAL MONEY
		<?php
			if(!empty($totalLines))
			{
		?>
			&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo SITE_URL_EMPLOYEES;?>/print-employee-rev-worksheet.php?month=<?php echo $month;?>&year=<?php echo $year;?>" class="link_style9">VIEW THIS REPORT IN EXCEL SHEET</a>
		<?php
			}
		?>
	</td>
</tr>
<tr>
	<td class="smalltext2" colspan="3"><b>WORK DONE IN</b></td>
	<td class="smalltext2"><b>TOTAL LINES</b></td>
	<td class="smalltext2" width="3%">&nbsp;</td>
	<td class="smalltext2"><b>TOTAL MONEY</b></td>
</tr>
<tr>
	<td colspan="8">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<tr>
	<td class="smalltext2" colspan="2"><b>Direct</b></td>
	<td class="smalltext2"><b>LEVEL1</b></td>
	<td class="smalltext2"><b><?php echo $totalDirectLevel1Lines;?></b></td>
	<td class="smalltext2">&nbsp;</td>
	<td class="smalltext2"><b><?php echo $totalDirectLevel1Money;?></b></td>
</tr>
<tr>
	<td colspan="8">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<tr>
	<td class="smalltext2" colspan="2"><b>Direct</b></td>
	<td class="smalltext2"><b>LEVEL2</b></td>
	<td class="smalltext2"><b><?php echo $totalDirectLevel2Lines;?></b></td>
	<td class="smalltext2">&nbsp;</td>
	<td class="smalltext2"><b><?php echo $totalDirectLevel2Money;?></b></td>
</tr>
<tr>
	<td colspan="8">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<tr>
	<td class="smalltext2" colspan="2"><b>Indirect</b></td>
	<td class="smalltext2"><b>LEVEL1</b></td>
	<td class="smalltext2"><b><?php echo $totalIndirectLevel1Lines;?></b></td>
	<td class="smalltext2">&nbsp;</td>
	<td class="smalltext2"><b><?php echo $totalIndirectLevel1Money;?></b></td>
</tr>
<tr>
	<td colspan="8">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<tr>
	<td class="smalltext2" colspan="2"><b>Indirect</b></td>
	<td class="smalltext2"><b>LEVEL2</b></td>
	<td class="smalltext2"><b><?php echo $totalIndirectLevel2Lines;?></b></td>
	<td class="smalltext2">&nbsp;</td>
	<td class="smalltext2"><b><?php echo $totalIndirectLevel2Lines;?></b></td>
</tr>
<tr>
	<td colspan="8">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<tr>
	<td class="smalltext2" colspan="2"><b>QA</b></td>
	<td class="smalltext2"><b>LEVEL1</b></td>
	<td class="smalltext2"><b><?php echo $totalQaLevel1Lines;?></b></td>
	<td class="smalltext2">&nbsp;</td>
	<td class="smalltext2"><b><?php echo $totalQaLevel1Money;?></b></td>
</tr>
<tr>
	<td colspan="8">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<tr>
	<td class="smalltext2" colspan="2"><b>QA</b></td>
	<td class="smalltext2"><b>LEVEL2</b></td>
	<td class="smalltext2"><b><?php echo $totalQaLevel2Lines;?></b></td>
	<td class="smalltext2">&nbsp;</td>
	<td class="smalltext2"><b><?php echo $totalQaLevel2Money;?></b></td>
</tr>
<tr>
	<td colspan="8">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<tr>
	<td class="smalltext2" colspan="2"><b>Post Audit</b></td>
	<td class="smalltext2"><b>LEVEL1</b></td>
	<td class="smalltext2"><b><?php echo $totalAuditLevel1Lines;?></b></td>
	<td class="smalltext2">&nbsp;</td>
	<td class="smalltext2"><b><?php echo $totalAuditLevel1Money;?></b></td>
</tr>
<tr>
	<td colspan="8">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<tr>
	<td class="smalltext2" colspan="2"><b>Post Audit</b></td>
	<td class="smalltext2"><b>LEVEL2</b></td>
	<td class="smalltext2"><b><?php echo $totalAuditLevel2Lines;?></b></td>
	<td class="smalltext2">&nbsp;</td>
	<td class="smalltext2"><b><?php echo $totalAuditLevel2Money;?></b></td>
</tr>
<tr>
	<td colspan="8">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<tr>
	<td class="title" colspan="3"><b>GRAND TOTAL</b></td>
	<td class="title"><?php echo $totalLines;?></td>
	<td class="title">&nbsp;</td>
	<td class="title">
		<?php 
			echo round($totalMoney,2);
		?>
	</td>
</tr>
<?php
	}
	else
	{
		echo "<br><br><center><font class='error'><b>Please Select Valid Month And Year !!</b></font></center><br><br><br>";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>