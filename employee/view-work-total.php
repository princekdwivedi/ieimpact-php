<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-mt-employee-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	."/includes/common-array.php");
	$employeeObj                = new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");

	$month						=	date("m");
	$year						=	date("Y");
	$currentYear				=	date("Y");
	$andClause					=	"";
	$showForm					=	false;
	$departmentText				=	"Not Yet Added";
	$shiftText					=	"Not Yet Added";
	
	$totalTranscriptionLinesEntered			=	0;
	$totalIndirectTranscriptionLinesEntered	=	0;
	$totalVreLinesEntered					=	0;
	$totalIndirectVreLinesEntered			=	0;
	$totalQaLinesEntered					=	0;
	$totalIndirectQaLinesEntered			=	0;
	$totalAuditLinesEntered					=	0;
	$totalIndirectAuditLinesEntered			=	0;
	

	$totalLines					=	0;
	$totalMoney					=	0;
	$departmentText				=	$a_department[$s_departmentId];
	$shift						=	@mysql_result(dbQuery("SELECT shiftId FROM employee_shift_rates WHERE employeeId=$s_employeeId"),0);
	if(!empty($shift))
	{
		$shiftText				=	$a_shift[$shift];
	}


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
?>
<br>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class="textstyle3" width="40%" valign="top">
			<b>VIEW TOTAL OF YOUR LINES IN <?php echo $monthText.",".$year;?></b>
		</td>
		<td>
<?php
	if($result					=	$employeeObj->getMtEmployeeeExcelData($s_employeeId,$month,$year))
	{
		$row					=	mysql_fetch_assoc($result);
		$editedDirect			=	$row['editedDirect'];
		$editedIndirect			=	$row['editedIndirect'];
		$typedDirect			=	$row['typedDirect'];
		$typedIndirect			=	$row['typedIndirect'];
?>
<table width="80%" border="0" align="left" cellpadding="0" cellspacing="0" valign="top" style="border:1px solid #bebebe;">
	<tr height='25' class='rwcolor'>
		<td width="20%" class="smalltext12">
			&nbsp;<b>EDITED DIRECT</b>
		</td>
		<td width="20%" class="smalltext12">
			<b>EDITED INDIRECT</b>
		</td>
		<td width="20%" class="smalltext12">
			<b>TYPED DIRECT</b>
		</td>
		<td class="smalltext12">
			<b>TYPED INDIRECT</b>
		</td>
	</tr>
	<tr height='20' class='rwcolor2'>
		<td class="smalltext2" valign="top">
			&nbsp;<b><?php echo $editedDirect;?></b>
		</td>
		<td class="smalltext2" valign="top">
			<b><?php echo $editedIndirect;?></b>
		</td>
		<td class="smalltext2" valign="top">
			<b><?php echo $typedDirect;?></b>
		</td>
		<td class="smalltext2" valign="top">
			<b><?php echo $typedIndirect;?></b>
		</td>
	</tr>
</table>
<?php
	}
	else
	{
		echo "&nbsp;";
	}
	echo "</td></tr></table>";

	if($showForm)
	{
		
		$query		=	"SELECT SUM(totalDirectTrascriptionLines) AS totalTranscriptionLinesEntered,SUM(totalIndirectTrascriptionLines) AS totalIndirectTranscriptionLinesEntered,SUM(totalDirectVreLines) AS totalVreLinesEntered,SUM(totalIndirectVreLines) AS totalIndirectVreLinesEntered,SUM(totalQaLines) AS totalQaLinesEntered,SUM(totalIndirectQaLines) AS totalIndirectQaLinesEntered,SUM(totalDirectAuditLines) AS totalAuditLinesEntered,SUM(totalIndirectAuditLines) as totalIndirectAuditLinesEntered,SUM(totalDirectTrascriptionMoney) as totalDirectTrascriptionMoney,SUM(totalIndirectTrascriptionMoney) as totalIndirectTrascriptionMoney,SUM(totalDirectVreMoney) as totalDirectVreMoney,SUM(totalIndirectVreMoney) as totalIndirectVreMoney,SUM(totalDirectQaMoney) as totalDirectQaMoney,SUM(totalIndirectQaMoney) as totalIndirectQaMoney,SUM(totalDirectAuditMoney) as totalDirectAuditMoney,SUM(totalIndirectAuditMoney) as totalIndirectAuditMoney FROM datewise_employee_works_money WHERE datewise_employee_works_money.ID > ".MAX_SEARCH_MT_EMPLOYEE_WORKID." AND employeeId=$s_employeeId AND MONTH(workedOnDate)=$month AND YEAR(workedOnDate)=$year";
		$result	=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			$row							=	mysql_fetch_assoc($result);
			$totalTranscriptionLinesEntered	=	$row['totalTranscriptionLinesEntered'];
			$totalIndirectTranscriptionLinesEntered	=	$row['totalIndirectTranscriptionLinesEntered'];
			$totalVreLinesEntered			=	$row['totalVreLinesEntered'];
			$totalIndirectVreLinesEntered	=	$row['totalIndirectVreLinesEntered'];
			$totalQaLinesEntered			=	$row['totalQaLinesEntered'];
			$totalIndirectQaLinesEntered	=	$row['totalIndirectQaLinesEntered'];
			$totalAuditLinesEntered			=	$row['totalAuditLinesEntered'];
			$totalIndirectAuditLinesEntered	=	$row['totalIndirectAuditLinesEntered'];

			$totalDirectTrascriptionMoney	=	$row['totalDirectTrascriptionMoney'];
			$totalIndirectTrascriptionMoney	=	$row['totalIndirectTrascriptionMoney'];
			$totalDirectVreMoney			=	$row['totalDirectVreMoney'];
			$totalIndirectVreMoney			=	$row['totalIndirectVreMoney'];
			$totalDirectQaMoney				=	$row['totalDirectQaMoney'];
			$totalIndirectQaMoney			=	$row['totalIndirectQaMoney'];
			$totalDirectAuditMoney			=	$row['totalDirectAuditMoney'];
			$totalIndirectAuditMoney		=	$row['totalIndirectAuditMoney'];
			

			if(empty($totalTranscriptionLinesEntered))
			{
				$totalTranscriptionLinesEntered	=	0;
			}
			if(empty($totalIndirectTranscriptionLinesEntered))
			{
				$totalIndirectTranscriptionLinesEntered	=	0;
			}
			if(empty($totalVreLinesEntered))
			{
				$totalVreLinesEntered	=	0;
			}
			if(empty($totalIndirectVreLinesEntered))
			{
				$totalIndirectVreLinesEntered	=	0;
			}
			if(empty($totalQaLinesEntered))
			{
				$totalQaLinesEntered	=	0;
			}
			if(empty($totalIndirectQaLinesEntered))
			{
				$totalIndirectQaLinesEntered	=	0;
			}
			if(empty($totalAuditLinesEntered))
			{
				$totalAuditLinesEntered	=	0;
			}
			if(empty($totalIndirectAuditLinesEntered))
			{
				$totalIndirectAuditLinesEntered	=	0;
			}
		}

		$totalLines		=	$totalLines+$totalTranscriptionLinesEntered+$totalIndirectTranscriptionLinesEntered+$totalVreLinesEntered+$totalIndirectVreLinesEntered+$totalQaLinesEntered+$totalIndirectQaLinesEntered+$totalAuditLinesEntered+$totalIndirectAuditLinesEntered;

		$totalMoney		=	$totalMoney+$totalDirectTrascriptionMoney+$totalIndirectTrascriptionMoney+$totalDirectVreMoney+$totalIndirectVreMoney+$totalDirectQaMoney+$totalIndirectQaMoney+$totalDirectAuditMoney+$totalIndirectAuditMoney;

		$totalMoney		=	round($totalMoney);

?>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" valign="top">
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
		VIEW TOTAL LINES ADDED AND TOTAL MONEY
		<?php
			if(!empty($totalLines))
			{
		?>
			&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo SITE_URL_EMPLOYEES;?>/print-employee-mt-worksheet.php?month=<?php echo $month;?>&year=<?php echo $year;?>" class="link_style9">VIEW THIS REPORT IN EXCEL SHEET</a>
		<?php
			}
		?>
	</td>
</tr>
<tr height='25' bgcolor="#373737">
	<td class="smalltext12" colspan="3">&nbsp;<b>WORK DONE IN</b></td>
	<td class="smalltext12"><b>TOTAL LINES</b></td>
	<td class="smalltext12" width="3%">&nbsp;</td>
	<td class="smalltext12"><b>TOTAL MONEY</b></td>
</tr>
<tr height='25' class='rwcolor1'>
	<td class="smalltext2" colspan="2"><b>Transcription (SINGLE)</b></td>
	<td class="smalltext2" width="10%"><b>DSP</b></td>
	<td class="smalltext2" width="20%"><b><?php echo $totalTranscriptionLinesEntered;?></b></td>
	<td class="smalltext2">&nbsp;</td>
	<td class="smalltext2"><b><?php echo $totalDirectTrascriptionMoney;?></b></td>
</tr>
<tr height='25' class='rwcolor2'>
	<td class="smalltext2" colspan="2"><b>Transcription (SINGLE)</b></td>
	<td class="smalltext2" width="10%"><b>N-DSP</b></td>
	<td class="smalltext2"><b><?php echo $totalIndirectTranscriptionLinesEntered;?></b></td>
	<td class="smalltext2">&nbsp;</td>
	<td class="smalltext2"><b><?php echo $totalIndirectTrascriptionMoney;?></b></td>
</tr>
<tr height='25' class='rwcolor1'>
	<td class="smalltext2" colspan="2"><b>VRE</b></td>
	<td class="smalltext2"><b>DSP</b></td>
	<td class="smalltext2"><b><?php echo $totalVreLinesEntered;?></b></td>
	<td class="smalltext2">&nbsp;</td>
	<td class="smalltext2"><b><?php echo $totalDirectVreMoney;?></b></td>
</tr>
<tr height='25' class='rwcolor2'>
	<td class="smalltext2" colspan="2"><b>VRE</b></td>
	<td class="smalltext2"><b>N-DSP</b></td>
	<td class="smalltext2"><b><?php echo $totalIndirectVreLinesEntered;?></b></td>
	<td class="smalltext2">&nbsp;</td>
	<td class="smalltext2"><b><?php echo $totalIndirectVreMoney;?></b></td>
</tr>
<tr height='25' class='rwcolor1'>
	<td class="smalltext2" colspan="2"><b>QA</b></td>
	<td class="smalltext2"><b>DSP</b></td>
	<td class="smalltext2"><b><?php echo $totalQaLinesEntered;?></b></td>
	<td class="smalltext2">&nbsp;</td>
	<td class="smalltext2"><b><?php echo $totalDirectQaMoney;?></b></td>
</tr>
<tr height='25' class='rwcolor2'>
	<td class="smalltext2" colspan="2"><b>QA</b></td>
	<td class="smalltext2"><b>N-DSP</b></td>
	<td class="smalltext2"><b><?php echo $totalIndirectQaLinesEntered;?></b></td>
	<td class="smalltext2">&nbsp;</td>
	<td class="smalltext2"><b><?php echo $totalIndirectQaMoney;?></b></td>
</tr>
<tr height='25' class='rwcolor1'>
	<td class="smalltext2" colspan="2"><b>Night shift lines</b></td>
	<td class="smalltext2"><b>Transcription</b></td>
	<td class="smalltext2"><b><?php echo $totalAuditLinesEntered;?></b></td>
	<td class="smalltext2">&nbsp;</td>
	<td class="smalltext2"><b><?php echo $totalDirectAuditMoney;?></b></td>
</tr>
<tr height='25' class='rwcolor2'>
	<td class="smalltext2" colspan="2"><b>Night shift lines</b></td>
	<td class="smalltext2"><b>VRE</b></td>
	<td class="smalltext2"><b><?php echo $totalIndirectAuditLinesEntered;?></b></td>
	<td class="smalltext2">&nbsp;</td>
	<td class="smalltext2"><b><?php echo $totalIndirectAuditMoney;?></b></td>
</tr>
<tr height='25' class='rwcolor1'>
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