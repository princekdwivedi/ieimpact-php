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
	$employeeObj				=	new employee();
	$pagingObj					=	new Paging();

	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$departmentId		=	0;
	$searRateBy			=	0;
	$platform			=	0;
	$customerId			=	0;
	$employeeId			=	0;
	$a_employeeId		=	array();
	$forDate			=	date("d-m-Y");
	$t_forDate			=	"";
	$toDate				=	"";
	$t_toDate			=	"";
	$errorMsg			=	"";
	$searrchText		=	"";
	$dateText			=	"";
	$andClause			=	"";
	$dateClause			=	"";
	$platName			=	"";
	$customerName		=	"";
	$employeeName		=	"";
	$mainTotal			=	0;
	$reportView			=	0;
	$a_reportDictaphone	=	array();
	$a_reportProperties	=	array();

	$display			=	"none";
	$display1			=	"none";

	$display2			=	"";
	$display3			=	"none";

	$month				=	date("m");
	$year				=	date("Y");

	$checked			=	"checked";
	$checked1			=	"";

	$totalLines			=	0;
	$totalMoney			=	0;
	$grandLines			=	0;
	$grandTotal		    =	0;
	
	$printLink			=	"";
	$type				=	0;
	$manager			=	0;
	$a_managers			=	$employeeObj->getAllEmployeeManager();

	$seachingFromAttendence		=	@mysql_result(dbQuery("SELECT loginDate FROM employee_attendence WHERE attendenceId > '".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID."' AND loginDate <> '0000-00-00' ORDER BY attendenceId LIMIT 1"),0);
	$headingText	=	"This page will show records from - ".showDate($seachingFromAttendence);

	
	$form				=	SITE_ROOT_EMPLOYEES."/forms/search-employee-worksheet.php";
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		//print_r($_REQUEST);
		if(isset($_POST['employeeId']))
		{
			$a_employeeId	=	$_POST['employeeId'];
		}
		if(empty($departmentId))
		{
			$errorMsg	=	"Please Select A Department !!";
		}
		if(empty($errorMsg))
		{
			$department	=	$a_department[$departmentId];
			$searrchText=	"SEARCHING WORKSHEET FOR DEPARTMENT ".$department;
			$andClause .=	" AND datewise_employee_works_money.departmentId=$departmentId";
			if(!empty($platform))
			{
				$platName	 =	$employeeObj->getPlatformName($platform);
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
				if(isset($_POST['reportDictaphone']))
				{
					$a_reportDictaphone	=	$_POST['reportDictaphone'];
					$displayLinesFor	=	implode(",",$a_reportDictaphone);

					$printLink .=	"&displayLines=".$displayLinesFor;

				}
				$display			=	"";
				
			}
			if($searchBy	==	1)
			{
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
				$t_fromDate		=	$year."-".$month."-01";
				$t_toDate		=	$year."-".$month."-31";
				$andClause	   .=	" AND workedOnDate >= '$t_fromDate' AND workedOnDate <= '$t_toDate'";
				$printLink     .=	"&forDate=".$t_fromDate."&toDate=".$t_toDate;
				$monthText		=	$a_month[$month];

				$display2			=	"none";
				$display3			=	"";

				$checked			=	"";
				$checked1			=	"checked";


				$searrchText   .=  " For Month - ".$monthText.",".$year;
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

					$andClause	.=	" AND datewise_employee_works_money.employeeId IN ($searchEmployee)";
					$totalEmloyee	=	count($a_employeeId);
					if($totalEmloyee < 2 && $totalEmloyee > 0)
					{
						foreach($a_employeeId as $key=>$value)
						{
							$employeeName	=	$employeeObj->getEmployeeName($value);
						}
						$searrchText .=	" FOR EMPLOYEE ".strtoupper($employeeName);
					}
					else
					{
						$searrchText .=	" FOR MULTILE EMPLOYEE";
					}
				}
			}
		}

	}
?>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class="textstyle3"><b><font color="#ff0000"><?php echo $headingText;?></font></b></td>
	</tr>
	<tr>
		<td><b>(<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-mt-employee-excel-data.php">VIEW ADMIN ADDED EXCEL SHEET UPDATED WORK LINES</a>)</b></td>
	</tr>
</table>
<?php
	include($form);
	if(!empty($departmentId))
	{
?>
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td colspan="15" class="smalltext1">
			<b><?php echo $searrchText;?></b>
		</td>
	</tr>
	<tr>
		<td colspan="15">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
	<tr>
		<td colspan="15">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/print-monthly-mt-worksheet.php?<?php echo $printLink;?>&reportView=<?php echo $reportView;?>" class="link_style9">PRINT THIS REPORT IN EXCEL SHEET</a>
		</td>
	</tr>
	<tr>
		<td colspan="15">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
	<?php
		$query	=	"SELECT datewise_employee_works_money.employeeId,SUM(totalDirectTrascriptionLines) AS totalDirectTrascriptionLines,SUM(totalDirectTrascriptionMoney) AS totalDirectTrascriptionMoney,SUM(totalIndirectTrascriptionLines) AS totalIndirectTrascriptionLines,SUM(totalIndirectTrascriptionMoney) AS totalIndirectTrascriptionMoney,SUM(totalDirectVreLines) AS totalDirectVreLines,SUM(totalDirectVreMoney) AS totalDirectVreMoney,SUM(totalIndirectVreLines) AS totalIndirectVreLines,SUM(totalIndirectVreMoney) AS totalIndirectVreMoney,SUM(totalQaLines) AS totalQaLines,SUM(totalDirectQaMoney) AS totalDirectQaMoney,SUM(totalIndirectQaLines) AS totalIndirectQaLines,SUM(totalIndirectQaMoney) AS totalIndirectQaMoney,SUM(totalDirectAuditLines) AS totalDirectAuditLines,SUM(totalDirectAuditMoney) AS totalDirectAuditMoney,SUM(totalIndirectAuditLines) AS totalIndirectAuditLines,SUM(totalIndirectAuditMoney) AS totalIndirectAuditMoney,fullName,postAuditAccuracy,pendingAccuracy FROM datewise_employee_works_money INNER JOIN employee_details ON datewise_employee_works_money.employeeId=employee_details.employeeId WHERE datewise_employee_works_money.ID > ".MAX_SEARCH_MT_EMPLOYEE_WORKID." AND employee_details.isActive=1".$andClause." GROUP BY datewise_employee_works_money.employeeId";

		$result	=	dbQuery($query);
		if(mysql_num_rows($result))
		{
?>
<tr>
	<td width="18%" class="text2"  valign="top">Employee</td>
	<td width="10%" class="text2"  valign="top">Accuracy</td>
<?php
		if(isset($_POST['reportDictaphone']))
		{
			$a_reportDictaphone	=	$_POST['reportDictaphone'];
			foreach($a_reportDictaphone as $key=>$value)
			{
				$value	=	$a_viewDictaphoneReport[$key];
		?>
			<td width="16%" valign="top" class="text2"><?php echo $value?> Line</td>
		<?php
			}
		}
		else
		{
			?>
			<td width="16%" valign="top" class="text2">Transcription (SINGLE) Line</td>
			<td width="15%" valign="top" class="text2">VRE Line</td>
			<td width="15%" valign="top" class="text2">QA Line</td>
			<td width="15%" valign="top" class="text2">Night shift lines</td>
<?php
		}
		if($reportView == 1)
		{
			echo "<td class='text2' valign='top' width='8%'>Total Money</td>";
			echo "<td class='text2' valign='top'>Total Lines</td>";
		}
		elseif($reportView == 2)
		{
			echo "<td class='text2' valign='top'>Total Money</td>";
		}
		elseif($reportView == 3)
		{
			echo "<td class='text2' valign='top'>Total Lines</td>";
		}
?>
</tr>	
<tr>
	<td colspan="17">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<?php
		while($row	=	mysql_fetch_assoc($result))
		{
			$employeeId						=	$row['employeeId'];
			$employeeName					=	stripslashes($row['fullName']);
			
			$totalDirectTrascriptionLines	=	$row['totalDirectTrascriptionLines'];
			$totalDirectTrascriptionMoney	=	$row['totalDirectTrascriptionMoney'];

			$totalIndirectTrascriptionLines	=	$row['totalIndirectTrascriptionLines'];
			$totalIndirectTrascriptionMoney	=	$row['totalIndirectTrascriptionMoney'];

			$totalDirectVreLines			=	$row['totalDirectVreLines'];
			$totalDirectVreMoney			=	$row['totalDirectVreMoney'];

			$totalIndirectVreLines			=	$row['totalIndirectVreLines'];
			$totalIndirectVreMoney			=	$row['totalIndirectVreMoney'];

			$totalQaLines					=	$row['totalQaLines'];
			$totalDirectQaMoney				=	$row['totalDirectQaMoney'];

			$totalIndirectQaLines			=	$row['totalIndirectQaLines'];
			$totalIndirectQaMoney			=	$row['totalIndirectQaMoney'];

			$totalDirectAuditLines			=	$row['totalDirectAuditLines'];
			$totalDirectAuditMoney			=	$row['totalDirectAuditMoney'];

			$totalIndirectAuditLines		=	$row['totalIndirectAuditLines'];
			$totalIndirectAuditMoney		=	$row['totalIndirectAuditMoney'];

			$postAuditAccuracy				=	$row['postAuditAccuracy'];
			$pendingAccuracy				=	$row['pendingAccuracy'];

			$employeeName					=	ucwords($employeeName);


			$totalLines		=	$totalDirectTrascriptionLines+$totalIndirectTrascriptionLines+$totalDirectVreLines+$totalIndirectVreLines+$totalQaLines+$totalIndirectQaLines+$totalDirectAuditLines+$totalIndirectAuditLines;

			$totalMoney		=	$totalDirectTrascriptionMoney+$totalIndirectTrascriptionMoney+$totalDirectVreMoney+$totalIndirectVreMoney+$totalDirectQaMoney+$totalIndirectQaMoney+$totalDirectAuditMoney+$totalIndirectAuditMoney;
			
			$totalLines		=	round($totalLines);
			$totalMoney		=	round($totalMoney);

			$grandLines		=	$grandLines+$totalLines;
			$grandTotal		=	$grandTotal+$totalMoney;
		?>
<tr>
	<td class="smalltext2"  valign="top"><?php echo $employeeName;?></td>
	<td class="smalltext2"  valign="top">
		<?php 
				if(!empty($postAuditAccuracy))
				{
					echo "Post Audit:".$postAuditAccuracy."<br>";
				}
				if(!empty($pendingAccuracy))
				{
					echo "Pending:".$pendingAccuracy;
				}
		?>
	</td>
<?php
		if($departmentId	== 1)
		{
			if(isset($_POST['reportDictaphone']))
			{
				$a_reportDictaphone	=	$_POST['reportDictaphone'];
				foreach($a_reportDictaphone as $key=>$value)
				{
					if($key	== 1)
					{
				?>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">D-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalDirectTrascriptionLines;
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">N-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalIndirectTrascriptionLines;
						?>
					</td>
				</tr>
			</table>
		</td>
		<?php
					}
					if($key	== 2)
					{
		?>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">D-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalDirectVreLines;
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">N-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalIndirectVreLines;
						?>
					</td>
				</tr>
			</table>
		</td>
		<?php
		}
		if($key	== 3)
		{
		?>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">D-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalQaLines;
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">N-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalIndirectQaLines;
						?>
					</td>
				</tr>
			</table>
		</td>
		<?php
			}
			if($key	== 4)
			{
		?>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">D-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalDirectAuditLines;
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">N-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalIndirectAuditLines;
						?>
					</td>
				</tr>
			</table>
		</td>
		<?php
			}
		?>
		<?php
				}
			}
			else
			{
		?>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">D-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalDirectTrascriptionLines;;
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">N-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalIndirectTrascriptionLines;
						?>
					</td>
				</tr>
			</table>
		</td>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">D-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalDirectVreLines;
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">N-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalIndirectVreLines;
						?>
					</td>
				</tr>
			</table>
		</td>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">D-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalQaLines;
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">N-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalIndirectQaLines;
						?>
					</td>
				</tr>
			</table>
		</td>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">T-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalDirectAuditLines;
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">V-</td>
					<td class="smalltext2" valign="top">
						<?php
							echo $totalIndirectAuditLines;
						?>
					</td>
				</tr>
			</table>
		</td>
		<?php
			}
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
	<td colspan="17">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<?php
		$totalLines	=	0;
		$totalMoney	=	0;
		
	}
	if($reportView == 1)
	{
	?>
	<tr>
		<td class="smalltext2" colspan="15">
			<b>TOTAL MONEY</b> : <b><?php echo round($grandTotal);?></b>
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
			<b>TOTAL MONEY</b> : <b><?php echo round($grandTotal);?></b>
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
	}
?>
</table>
<?php
	}
	else
	{
		echo "<table><tr><td height='200'></td></tr></table>";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>