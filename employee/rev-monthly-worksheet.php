<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");;
	include(SITE_ROOT_EMPLOYEES	."/includes/common-array.php");
	$employeeObj                = new employee();
	$validator					= new validate();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");

	$departmentId				=	2;
	$department					=	"REV";
	$platform					=	0;
	$customerId					=	0;
	$employeeId					=	0;
	$a_employeeId				=	array();;
	$forDate					=	date("d-m-Y");
	$t_forDate					=	date("Y-m-d");
	$toDate						=	"";
	$t_toDate					=	"";
	$a_reportProperties			=	array();
	$errorMsg					=	"";
	$searrchText				=	"";
	$dateText					=	"";
	$andClause					=	"";
	$dateClause					=	"";
	$platName					=	"";
	$customerName				=	"";
	$employeeName				=	"";
	$mainTotal					=	0;
	$reportView					=	0;
	$grandTotal					=	0;
	$grandLines					=	0;
	$reportView					=	1;

	$searrchText				=	"SEARCHING WORKSHEET FOR DEPARTMENT ".$department;
	$andClause					=	" AND departmentId=$departmentId";

	$printLink					=	"";
	$showForm					=	false;
	$type						=	0;
	$manager					=	0;
	$a_managers					=	$employeeObj->getAllEmployeeManager();
	$month						=	date("m");
	$year						=	date("Y");

	$checked					=	"";
	$checked1					=	"checked";

	$display					=	"none";
	$display1					=	"";

	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		$showForm		=	true;
		//print_r($_REQUEST);
		if($searchBy	==	1)
		{
			$checked				=	"checked";
			$checked1				=	"";
			$display				=	"";
			$display1				=	"none";
			if(!empty($forDate))
			{
				list($day,$month,$year)		=	explode("-",$forDate);
				$t_forDate	=	$year."-".$month."-".$day;
				$dateText	=	" OF ".showDate($t_forDate);
				$dateClause	=	" AND workedOnDate='$t_forDate'";
				$printLink .=	"&forDate=".$t_forDate;
				if(!empty($toDate))
				{
					list($t_day,$t_month,$t_year)		=	explode("-",$toDate);
					$t_toDate	=	$t_year."-".$t_month."-".$t_day;
					$dateText	=	" FROM ".showDate($t_forDate)." TO ".showDate($t_toDate);
					$dateClause	=	" AND workedOnDate >= '$t_forDate' AND workedOnDate <= '$t_toDate'";

					$printLink .=	"&toDate=".$t_toDate;
				}
				$searrchText.=  strtoupper($dateText);
				$andClause  .=	$dateClause;
			}
		}
		else
		{
			if(!empty($month) && !empty($year))
			{
				$t_fromDate		=	$year."-".$month."-01";
				$t_toDate		=	$year."-".$month."-31";
				$andClause	   .=	" AND workedOnDate >= '$t_fromDate' AND workedOnDate <= '$t_toDate'";
				$printLink     .=	"&forDate=".$t_fromDate."&toDate=".$t_toDate;
				$monthText		=	$a_month[$month];
				$searrchText   .=  " For Month - ".$monthText.",".$year;
			}
		}
		if(isset($_POST['employeeId']))
		{
			$a_employeeId	=	$_POST['employeeId'];
		}
		if(!empty($platform))
		{
			$platName	=	$employeeObj->getPlatformName($platform);
			$searrchText.=  " OF ".strtoupper($platName);
			$andClause	.=	" AND platform=$platform";
			$printLink	.=	"platform=".$platform;
			if(!empty($customerId))
			{
				$customerName	=	$employeeObj->getCustomerName($customerId,$platform);

				$searrchText.=  " - CLIENT: ".strtoupper($customerName);
				$andClause  .=	" AND customerId=$customerId";
				$printLink  .=	"&customerId=".$customerId;
			}
		}
		if(isset($_POST['type']))
		{
			$type			=	$_POST['type'];
			if(!empty($type))
			{
				$andClause	   .=	" AND employee_details.employeeType=$type";
				$searrchText   .=	" for ".$a_inetExtEmployee[$type]." employees";
				$printLink     .=   "&employeeType=".$type;
			}
		}
		if(isset($_POST['manager']))
		{
			$manager		=	$_POST['manager'];
			if(!empty($manager))
			{
				$andClause	   .=	" AND employee_details.underManager=$manager";
				$searrchText   .=	" under manager ".$a_managers[$manager];
				$printLink     .=   "&underManager=".$manager;
			}
		}
		if(!empty($a_employeeId))
		{
			if(!in_array("0",$a_employeeId))
			{
				$searchEmployee	=	implode(",",$a_employeeId);
				$printLink     .=	"&employee=".$searchEmployee;

				$andClause		.=	" AND datewise_employee_works_money.employeeId IN ($searchEmployee)";
				$totalEmloyee	=	count($a_employeeId);
				if($totalEmloyee < 2 && $totalEmloyee > 0)
				{
					foreach($a_employeeId as $key=>$value)
					{
						$employeeName	=	$employeeObj->getEmployeeName($value);
					}
					$searrchText.=  " FOR EMPLOYEE ".strtoupper($employeeName);
				}
				else
				{
					$searrchText.=  " FOR MULTIPLE EMPLOYEE";
				}
			}
		}
		if(isset($_POST['reportProperties']))
		{
			$a_reportProperties	=	$_POST['reportProperties'];
			$displayLinesFor	=	implode(",",$a_reportProperties);

			$printLink         .=	"&displayLines=".$displayLinesFor;
		}
	}
	$form				=	SITE_ROOT_EMPLOYEES."/forms/search-rev-worksheet.php";
	
	include($form);
	if($showForm)
	{
?>
<table width="99%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='title'><?php echo $searrchText;?></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
</table>
<?php
	$query	=	"SELECT datewise_employee_works_money.employeeId,SUM(totalDirectLevel1Lines) AS totalDirectLevel1Lines,SUM(totalDirectLevel1Money) AS totalDirectLevel1Money,SUM(totalDirectLevel2Lines) AS totalDirectLevel2Lines,SUM(totalDirectLevel2Money) AS totalDirectLevel2Money,SUM(totalIndirectLevel1Lines) AS totalIndirectLevel1Lines,SUM(totalIndirectLevel1Money) AS totalIndirectLevel1Money,SUM(totalIndirectLevel2Lines) AS totalIndirectLevel2Lines,SUM(totalIndirectLevel2Money) AS totalIndirectLevel2Money,SUM(totalQaLevel1Lines) AS totalQaLevel1Lines,SUM(totalQaLevel1Money) AS totalQaLevel1Money,SUM(totalQaLevel2Lines) AS totalQaLevel2Lines,SUM(totalQaLevel2Money) AS totalQaLevel2Money,SUM(totalAuditLevel1Lines) AS totalAuditLevel1Lines,SUM(totalAuditLevel1Money) AS totalAuditLevel1Money,SUM(totalAuditLevel2Lines) AS totalAuditLevel2Lines,SUM(totalAuditLevel2Money) AS totalAuditLevel2Money,firstName,lastName FROM datewise_employee_works_money INNER JOIN employee_details ON datewise_employee_works_money.employeeId=employee_details.employeeId WHERE employee_details.isActive=1".$andClause." GROUP BY datewise_employee_works_money.employeeId";
	$result	=	dbQuery($query);
	if(mysql_num_rows($result))
	{
?>
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
<tr>
	<td colspan="15">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<tr>
	<td colspan="15">
		<a href="<?php echo SITE_URL_EMPLOYEES;?>/print-monthly-rev-worksheet.php?<?php echo $printLink;?>&reportView=<?php echo $reportView;?>" class="link_style9">PRINT THIS REPORT IN EXCEL SHEET</a>
	</td>
</tr>
<tr>
	<td colspan="15">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<tr>
	<td width="15%" class="text2"  valign="top">Employee</td>
	<?php
		if(isset($_POST['reportProperties']))
		{
			$a_reportProperties	=	$_POST['reportProperties'];
			foreach($a_reportProperties as $key=>$value)
			{
				$value	=	$a_viewPropertiesReport[$key];	
		?>
		<td width="17%" valign="top" class="text2"><?php echo $value?></td>
		<?php
			}
		}
		else
		{
		?>
		<td width="17%" valign="top" class="text2">Direct</td>
		<td width="17%" valign="top" class="text2">Indirect</td>
		<td width="17%" valign="top" class="text2">QA</td>
		<td width="17%" valign="top" class="text2">Post Audit</td>
	<?php
		}
		if($reportView == 1)
		{
			echo "<td class='text2' width='9%'>Total Money</td>";
			echo "<td class='text2'>Total Lines</td>";
		}
		elseif($reportView == 2)
		{
			echo "<td class='text2'>Total Money</td>";
		}
		elseif($reportView == 3)
		{
			echo "<td class='text2'>Total Lines</td>";
		}
		while($row	=	mysql_fetch_assoc($result))
		{
			$totalLines		=	0;
			$totalMoney		=	0;
			$employeeId						=	$row['employeeId'];

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

			$firstName						=	$row['firstName'];
			$lastName						=	$row['lastName'];

			$employeeName					=	$firstName." ".$lastName;
			$employeeName					=	ucwords($employeeName);
			
			$totalLines		=	$totalDirectLevel1Lines+$totalDirectLevel2Lines+$totalIndirectLevel1Lines+$totalIndirectLevel2Lines+$totalQaLevel1Lines+$totalQaLevel2Lines+$totalAuditLevel1Lines+$totalAuditLevel2Lines;

			$totalMoney		=	$totalDirectLevel1Money+$totalDirectLevel2Money+$totalIndirectLevel1Money+$totalIndirectLevel2Money+$totalQaLevel1Money+$totalQaLevel2Money+$totalAuditLevel1Money+$totalAuditLevel2Money;
			if(isset($_POST['reportProperties']))
			{
				$a_reportProperties	=	$_POST['reportProperties'];
				$directLines	=	0;
				$directMoney	=	0;
				$indirectLines	=	0;
				$indirectMoney	=	0;
				$qaLines		=	0;
				$qaMoney		=	0;
				$auditLines		=	0;
				$auuditMoney	=	0;
				foreach($a_reportProperties as $key=>$value)
				{
					if($key == 1)
					{
						$directLines	=	$totalDirectLevel1Lines+$totalDirectLevel2Lines;
						$directMoney	=	$totalDirectLevel1Money+$totalDirectLevel2Money;
					}
					if($key == 2)
					{
						$indirectLines	=	$totalIndirectLevel1Lines+$totalIndirectLevel2Lines;
						$indirectMoney	=	$totalIndirectLevel1Money+$totalIndirectLevel2Money;
					}
					if($key == 3)
					{
						$qaLines		=	$totalQaLevel1Lines+$totalQaLevel2Lines;
						$qaMoney		=	$totalQaLevel1Money+$totalQaLevel2Money;
					}
					if($key == 4)
					{
						$auditLines		=	$totalAuditLevel1Lines+$totalAuditLevel2Lines;
						$auuditMoney	=	$totalAuditLevel1Money+$totalAuditLevel2Money;
					}
				}
				$totalLines		=	$directLines+$indirectLines+$qaLines+$auditLines;
				$totalMoney		=	$directMoney+$indirectMoney+$qaMoney+$auuditMoney;
			}
			
			$totalLines		=	round($totalLines);
			$totalMoney		=	round($totalMoney);

			$grandLines		=	$grandLines+$totalLines;
			$grandTotal		=	$grandTotal+$totalMoney;
?>
<tr>
	<td class="smalltext2"  valign="top"><?php echo $employeeName;?></td>
<?php
	if(isset($_POST['reportProperties']))
	{
		$a_reportProperties	=	$_POST['reportProperties'];
		foreach($a_reportProperties as $key=>$value)
		{
			if($key == 1)
			{
		?>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">L1-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalDirectLevel1Lines;
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">L2-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalDirectLevel2Lines;
						?>
					</td>
				</tr>
			</table>
		</td>
		<?php
		}
		if($key == 2)
		{
		?>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">L1-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalIndirectLevel1Lines;
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">L2-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalIndirectLevel1Lines;
						?>
					</td>
				</tr>
			</table>
		</td>
		<?php
		}
		if($key == 3)
		{
		?>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">L1-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalQaLevel1Lines;
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">L2-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalQaLevel2Lines;
						?>
					</td>
				</tr>
			</table>
		</td>
		<?php
		}
		if($key == 4)
		{
		?>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">L1-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalAuditLevel1Lines;
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">L2-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalAuditLevel2Lines;
						?>
					</td>
				</tr>
			</table>
		</td>
		<?php
					}
				}
			}
			else
			{
		?>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">L1-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalDirectLevel1Lines;
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">L2-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalDirectLevel2Lines;
						?>
					</td>
				</tr>
			</table>
		</td>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">L1-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalIndirectLevel1Lines;
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">L2-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalIndirectLevel2Lines;
						?>
					</td>
				</tr>
			</table>
		</td>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">L1-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalQaLevel1Lines;
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">L2-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalQaLevel2Lines;
						?>
					</td>
				</tr>
			</table>
		</td>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">L1-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalAuditLevel1Lines;
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">L2-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalAuditLevel2Lines;
						?>
					</td>
				</tr>
			</table>
		</td>
		<?php
		}
		if($reportView == 1)
		{
	?>
	<td class="text2" valign="top"><?php echo $totalMoney;?></td>
	<td class="text2" valign="top"><?php echo $totalLines;?></td>
	<?php
		}
		elseif($reportView == 2)
		{
	?>
	<td class="text2" valign="top"><?php echo $totalMoney;?></td>
	<?php
		}
		else
		{
	?>
	<td class="text2" valign="top"><?php echo $totalLines;?></td>
	<?php
		}
	
	?>
</tr>	
<tr>
	<td colspan="15">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<?php
	}
	if($reportView == 1)
	{
?>
<tr>
	<td class="smalltext2" colspan="15">
		<b>TOTAL MONEY</b> : <b><?php echo round($grandTotal,2);?></b>
	</td>
</tr>
<tr>
	<td colspan="15">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<tr>
	<td class="smalltext2" colspan="15">
		<b>TOTAL LINES</b> : <b><?php echo $grandLines;?></b>
	</td>
</tr>
<?php
	}
	elseif($reportView == 2)
	{
?>
<tr>
	<td class="smalltext2" colspan="15">
		<b>TOTAL MONEY</b> : <b><?php echo round($grandTotal,2);?></b>
	</td>
</tr>
<?php
	}
	elseif($reportView == 3)
	{
?>
<tr>
	<td class="smalltext2" colspan="15">
		<b>TOTAL LINES</b> : <b><?php echo $grandLines;?></b>
	</td>
</tr>
<?php 
	}
?>
</table>
<?php
	}
	else
	{
?>
<table>
	<tr>
		<td colspan="15" class="error">
			<b>NO RECORD FOUND !!</b>
		</td>
	</tr>
	<tr>
		<td colspan="15" height="200"></td>
	</tr>
</table>
<?php
	}
}
include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>