<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$departmentId		=	0;
	$platform			=	0;
	$customerId			=	0;
	$employeeId			=	0;
	$forDate			=	"";
	$t_forDate			=	"";
	$toDate				=	"";
	$t_toDate			=	"";
	$a_reportDictaphone	=	array();
	$a_reportProperties	=	array();
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

	$display			=	"none";
	$display1			=	"none";

	$totalTranscriptionLinesEntered	=	0;
	$totalIndirectTranscriptionLinesEntered	=	0;
	$totalVreLinesEntered	=	0;
	$totalIndirectVreLinesEntered	=	0;
	$totalQaLinesEntered	=	0;
	$totalIndirectQaLinesEntered	=	0;
	$totalAuditLinesEntered	=	0;
	$totalIndirectAuditLinesEntered	=	0;
	$totalDirectLevel1	=	0;
	$totalDirectLevel2	=	0;
	$totalIndirectLevel1=	0;
	$totalIndirectLevel2=	0;
	$totalQaLevel1		=	0;
	$totalQaLevel2		=	0;
	$totalAuditLevel1	=	0;
	$totalAuditLevel2	=	0;

	$directTranscriptionRate	=	0;
	$indirectTranscriptionRate	=	0;
	$directVreRate				=	0;
	$indirectVreRate			=	0;
	$directQaRate				=	0;
	$indirectQaRate				=	0;
	$directAuditRate			=	0;
	$indirectAuditRate			=	0;

	$directLevel1Rate			=	0;
	$directLevel2Rate			=	0;
	$indirectLevel1Rate			=	0;
	$indirectLevel2Rate			=	0;
	$qaLevel1Rate				=	0;
	$qaLevel2Rate				=	0;
	$auditLevel1Rate			=	0;
	$auditLevel2Rate			=	0;

	$t_directTranscriptionRate	=	0;
	$t_indirectTranscriptionRate=	0;
	$t_directVreRate			=	0;
	$t_indirectVreRate			=	0;
	$t_directQaRate				=	0;
	$t_indirectQaRate			=	0;
	$t_directAuditRate			=	0;
	$t_indirectAuditRate		=	0;

	$t_directLevel1Rate			=	0;
	$t_directLevel2Rate			=	0;
	$t_indirectLevel1Rate		=	0;
	$t_indirectLevel2Rate		=	0;
	$t_qaLevel1Rate				=	0;
	$t_qaLevel2Rate				=	0;
	$t_auditLevel1Rate			=	0;
	$t_auditLevel2Rate			=	0;

	$totalLines					=	0;
	$totalMoney					=	0;
	$grandLines					=	0;
	$grandTotal					=	0;

	$form				=	SITE_ROOT_EMPLOYEES."/forms/search-employee-worksheet.php";
	
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		//print_r($_REQUEST);
		if(empty($departmentId))
		{
			$errorMsg	=	"Please Select A Department !!";
		}
		if(empty($errorMsg))
		{
			$department	=	$a_department[$departmentId];
			$searrchText=	"SEARCHING WORKSHEET FOR DEPARTMENT ".$department;
			$andClause .=	" AND departmentId=$departmentId";
			if(!empty($platform))
			{
				$platName	 =	$a_platform[$platform];
				$searrchText.=  " OF ".strtoupper($platName);
				$andClause	.=	" AND platform=$platform";
				if(!empty($customerId))
				{
					$temp						=	"a_platform$platform";
					$a_customerName				=	$$temp;
					$customerName				=	$a_customerName[$customerId];

					$searrchText.=  " - CLIENT: ".strtoupper($customerName);
					$andClause .=	" AND customerId=$customerId";

					
				}
				if($platform <= 3)
				{
					if(isset($_POST['reportDictaphone']))
					{
						$a_reportDictaphone	=	$_POST['reportDictaphone'];
					}
					$display			=	"";
				}
				else
				{
					if(isset($_POST['reportProperties']))
					{
						$a_reportProperties	=	$_POST['reportProperties'];
					}
					$display1			=	"";
				}
			}
			if(!empty($forDate))
			{
				list($day,$month,$year)		=	explode("-",$forDate);
				$t_forDate	=	$year."-".$month."-".$day;
				$dateText	=	" OF ".showDate($t_forDate);
				$dateClause	=	" AND workedOn='$t_forDate'";
				if(!empty($toDate))
				{
					list($t_day,$t_month,$t_year)		=	explode("-",$toDate);
					$t_toDate	=	$t_year."-".$t_month."-".$t_day;
					$dateText	=	" FROM ".showDate($t_forDate)." TO ".showDate($t_toDate);
					$dateClause	=	" AND workedOn >= '$t_forDate' AND workedOn <= '$t_toDate'";
				}
				$searrchText.=  strtoupper($dateText);
				$andClause  .=	$dateClause;
			}
			if(!empty($employeeId))
			{
				$employeeName	=	$employeeObj->getEmployeeName($employeeId);
				$searrchText.=  " FOR EMPLOYEE ".strtoupper($employeeName);
				$andClause  .=	" AND employee_works.employeeId=$employeeId";
			}
		}

	}
	include($form);
	if(!empty($departmentId))
	{
?>
<br>
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
<?php
		$query	=	"SELECT employee_shift_rates.*, SUM(transcriptionLinesEntered) AS totalTranscriptionLinesEntered,SUM(indirectTranscriptionLinesEntered) AS totalIndirectTranscriptionLinesEntered,SUM(vreLinesEntered) AS totalVreLinesEntered,SUM(indirectVreLinesEntered) AS totalIndirectVreLinesEntered,SUM(qaLinesEntered) AS totalQaLinesEntered,SUM(indirectQaLinesEntered) AS totalIndirectQaLinesEntered,SUM(auditLinesEntered) AS totalAuditLinesEntered,SUM(indirectAuditLinesEntered) AS totalIndirectAuditLinesEntered,SUM(directLevel1) AS totalDirectLevel1,SUM(directLevel2) AS totalDirectLevel2,SUM(indirectLevel1) AS totalIndirectLevel1,SUM(indirectLevel2) AS totalIndirectLevel2,SUM(qaLevel1) AS totalQaLevel1,SUM(qaLevel2) AS totalQaLevel2,SUM(auditLevel1) AS totalAuditLevel1,SUM(auditLevel2) AS totalAuditLevel2 FROM employee_shift_rates INNER JOIN employee_works ON employee_shift_rates.employeeId=employee_works.employeeId WHERE employee_shift_rates.departmentId=$departmentId".$andClause." GROUP BY employee_works.employeeId";
		$result	=	mysql_query($query);
		if(mysql_num_rows($result))
		{
?>
<tr>
	<td width="15%" class="text2"  valign="top">Employee</td>
	<?php
		if($departmentId	== 1)
		{
			if(isset($_POST['reportDictaphone']))
			{
				$a_reportDictaphone	=	$_POST['reportDictaphone'];
				foreach($a_reportDictaphone as $key=>$value)
				{
					$value	=	$a_viewDictaphoneReport[$key];
		?>
		<td width="17%" valign="top" class="text2"><?php echo $value?></td>
		<?php
				}
			}
			else
			{
		?>
		<td width="17%" valign="top" class="text2">Transcription (SINGLE)</td>
		<td width="17%" valign="top" class="text2">VRE</td>
		<td width="17%" valign="top" class="text2">QA</td>
		<td width="17%" valign="top" class="text2">Post Audit</td>
		<?php
			}
		}
		else
		{
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
		<td width="17%" valign="top" class="text2">Ddfhirect</td>
		<td width="17%" valign="top" class="text2">Indirect</td>
		<td width="17%" valign="top" class="text2">QA</td>
		<td width="17%" valign="top" class="text2">Post Audit</td>
		<?php
			}
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
	?>
</tr>	
<tr>
	<td colspan="15">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<?php
	while($row	=	mysql_fetch_assoc($result))
	{
		$employeeId						=	$row['employeeId'];
		$totalTranscriptionLinesEntered			=	$row['totalTranscriptionLinesEntered'];
		$totalIndirectTranscriptionLinesEntered	=	$row['totalIndirectTranscriptionLinesEntered'];
		$totalVreLinesEntered	=	$row['totalVreLinesEntered'];
		$totalIndirectVreLinesEntered			=	$row['totalIndirectVreLinesEntered'];
		$totalQaLinesEntered					=	$row['totalQaLinesEntered'];
		$totalIndirectQaLinesEntered			=	$row['totalIndirectQaLinesEntered'];
		$totalAuditLinesEntered					=	$row['totalAuditLinesEntered'];
		$totalIndirectAuditLinesEntered	=	$row['totalIndirectAuditLinesEntered'];

		$totalDirectLevel1	=	$row['totalDirectLevel1'];
		$totalDirectLevel2	=	$row['totalDirectLevel2'];
		$totalIndirectLevel1=	$row['totalIndirectLevel1'];
		$totalIndirectLevel2=	$row['totalIndirectLevel2'];
		$totalQaLevel1		=	$row['totalQaLevel1'];
		$totalQaLevel2		=	$row['totalQaLevel2'];
		$totalAuditLevel1	=	$row['totalAuditLevel1'];
		$totalAuditLevel2	=	$row['totalAuditLevel2'];

		$directTranscriptionRate	=	$row['directTranscriptionRate'];
		$indirectTranscriptionRate	=	$row['indirectTranscriptionRate'];
		$directVreRate				=	$row['directVreRate'];
		$indirectVreRate			=	$row['indirectVreRate'];
		$directQaRate				=	$row['directQaRate'];
		$indirectQaRate				=	$row['indirectQaRate'];
		$directAuditRate			=	$row['directAuditRate'];
		$indirectAuditRate			=	$row['indirectAuditRate'];

		$directLevel1Rate			=	$row['directLevel1Rate'];
		$directLevel2Rate			=	$row['directLevel2Rate'];
		$indirectLevel1Rate			=	$row['indirectLevel1Rate'];
		$indirectLevel2Rate			=	$row['indirectLevel2Rate'];
		$qaLevel1Rate				=	$row['qaLevel1Rate'];
		$qaLevel2Rate				=	$row['qaLevel2Rate'];
		$auditLevel1Rate			=	$row['auditLevel1Rate'];
		$auditLevel2Rate			=	$row['auditLevel2Rate'];

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
		if(empty($totalDirectLevel1))
		{
			$totalDirectLevel1	=	0;
		}
		if(empty($totalDirectLevel2))
		{
			$totalDirectLevel2	=	0;
		}
		if(empty($totalIndirectLevel1))
		{
			$totalIndirectLevel1	=	0;
		}
		if(empty($totalIndirectLevel2))
		{
			$totalIndirectLevel2	=	0;
		}
		if(empty($totalQaLevel1))
		{
			$totalQaLevel1	=	0;
		}
		if(empty($totalQaLevel2))
		{
			$totalQaLevel2	=	0;
		}
		if(empty($totalAuditLevel1))
		{
			$totalAuditLevel1	=	0;
		}
		if(empty($totalAuditLevel2))
		{
			$totalAuditLevel2	=	0;
		}
		
		$t_directTranscriptionRate	=	$totalTranscriptionLinesEntered*$directTranscriptionRate;
		$t_indirectTranscriptionRate	=	$indirectTranscriptionRate*$totalIndirectTranscriptionLinesEntered;
		$t_directVreRate				=	$directVreRate*$totalVreLinesEntered;
		$t_indirectVreRate			=	$indirectVreRate*$totalIndirectVreLinesEntered;
		$t_directQaRate				=	$directQaRate*$totalQaLinesEntered;
		$t_indirectQaRate				=	$indirectQaRate*$totalIndirectQaLinesEntered;
		$t_directAuditRate			=	$directAuditRate*$totalAuditLinesEntered;
		$t_indirectAuditRate			=	$indirectAuditRate*$totalIndirectAuditLinesEntered;

		$t_directLevel1Rate			=	$directLevel1Rate*$totalDirectLevel1;
		$t_directLevel2Rate			=	$directLevel2Rate*$totalDirectLevel2;
		$t_indirectLevel1Rate			=	$indirectLevel1Rate*$totalIndirectLevel1;
		$t_indirectLevel2Rate			=	$indirectLevel2Rate*$totalIndirectLevel2;
		$t_qaLevel1Rate				=	$qaLevel1Rate*$totalQaLevel1;
		$t_qaLevel2Rate				=	$qaLevel2Rate*$totalQaLevel2;
		$t_auditLevel1Rate			=	$auditLevel1Rate*$totalAuditLevel1;
		$t_auditLevel2Rate			=	$auditLevel2Rate*$totalAuditLevel2;

		$employeeName	=	$employeeObj->getEmployeeName($employeeId);
		$totalMoney		=	0;
		$totalLines		=	0;
?>
<tr>
	<td class="smalltext2"  valign="top"><?php echo $employeeName;?></td>
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
						$totalMoney	=	$totalMoney+$t_directTranscriptionRate+$t_indirectTranscriptionRate;
		?>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">D-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalTranscriptionLinesEntered;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalTranscriptionLinesEntered."X".$directTranscriptionRate." = ".round($t_directTranscriptionRate,2);
							}
							else
							{
								echo $totalTranscriptionLinesEntered;
							}
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">N-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalIndirectTranscriptionLinesEntered;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalIndirectTranscriptionLinesEntered."X".$indirectTranscriptionRate." = ".round($t_indirectTranscriptionRate,2);
							}
							else
							{
								echo $totalIndirectTranscriptionLinesEntered;
							}
						?>
					</td>
				</tr>
			</table>
		</td>
		<?php
					}
					if($key	== 2)
					{
						$totalMoney	=	$totalMoney+$t_directVreRate+$t_indirectVreRate;
		?>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">D-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalVreLinesEntered;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalVreLinesEntered."X".$directVreRate." = ".round($t_directVreRate,2);
							}
							else
							{
								echo $totalVreLinesEntered;
							}
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">N-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalIndirectVreLinesEntered;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalIndirectVreLinesEntered."X".$indirectVreRate." = ".round($t_indirectVreRate,2);
							}
							else
							{
								echo $totalIndirectVreLinesEntered;
							}
							
						?>
					</td>
				</tr>
			</table>
		</td>
		<?php
					}
					if($key	== 3)
					{
						$totalMoney	=	$totalMoney+$t_directQaRate+$t_indirectQaRate;
		?>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">D-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalQaLinesEntered;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalQaLinesEntered."X".$directQaRate." = ".round($t_directQaRate,2);
							}
							else
							{
								echo $totalQaLinesEntered;
							}
							
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">N-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalIndirectQaLinesEntered;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalIndirectQaLinesEntered."X".$indirectQaRate." = ".round($t_indirectQaRate,2);
							}
							else
							{
								echo $totalIndirectQaLinesEntered;
							}
							
						?>
					</td>
				</tr>
			</table>
		</td>
		<?php
					}
					if($key	== 4)
					{
						$totalMoney	=	$totalMoney+$t_directAuditRate+$t_indirectAuditRate;
		?>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">D-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalAuditLinesEntered;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalAuditLinesEntered."X".$directAuditRate." = ".round($t_directAuditRate,2);
							}
							else
							{
								echo $totalAuditLinesEntered;
							}
							
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">N-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalIndirectAuditLinesEntered;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalIndirectAuditLinesEntered."X".$indirectAuditRate." = ".round($t_indirectAuditRate,2);
							}
							else
							{
								echo $totalIndirectAuditLinesEntered;
							}
							
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
							$totalMoney	=	$totalMoney+$t_directTranscriptionRate+$t_indirectTranscriptionRate;
							$totalLines	=	$totalLines+$totalTranscriptionLinesEntered;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalTranscriptionLinesEntered."X".$directTranscriptionRate." = ".round($t_directTranscriptionRate,2);
							}
							else
							{
								echo $totalTranscriptionLinesEntered;
							}
							
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">N-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalIndirectTranscriptionLinesEntered;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalIndirectTranscriptionLinesEntered."X".$indirectTranscriptionRate." = ".round($t_indirectTranscriptionRate,2);
							}
							else
							{
								echo $totalIndirectTranscriptionLinesEntered;
							}
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
							$totalMoney	=	$totalMoney+$t_directVreRate+$t_indirectVreRate;
							$totalLines	=	$totalLines+$totalVreLinesEntered;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalVreLinesEntered."X".$directVreRate." = ".round($t_directVreRate,2);
							}
							else
							{
								echo $totalVreLinesEntered;
							}
							
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">N-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalIndirectVreLinesEntered;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalIndirectVreLinesEntered."X".$indirectVreRate." = ".round($t_indirectVreRate,2);
							}
							else
							{
								echo $totalIndirectVreLinesEntered;
							}
							
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
							$totalMoney	=	$totalMoney+$t_directQaRate+$t_indirectQaRate;
							$totalLines	=	$totalLines+$totalQaLinesEntered;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalQaLinesEntered."X".$directQaRate." = ".round($t_directQaRate,2);
							}
							else
							{
								echo $totalQaLinesEntered;
							}
							
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">N-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalIndirectQaLinesEntered;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalIndirectQaLinesEntered."X".$indirectQaRate." = ".round($t_indirectQaRate,2);
							}
							else
							{
								echo $totalIndirectQaLinesEntered;
							}
							
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
							$totalMoney	=	$totalMoney+$t_directAuditRate+$t_indirectAuditRate;
							$totalLines	=	$totalLines+$totalAuditLinesEntered;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalAuditLinesEntered."X".$directAuditRate." = ".round($t_directAuditRate,2);
							}
							else
							{
								echo $totalAuditLinesEntered;
							}
							
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">N-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalIndirectAuditLinesEntered;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalIndirectAuditLinesEntered."X".$indirectAuditRate." = ".round($t_indirectAuditRate,2);
							}
							else
							{
								echo $totalIndirectAuditLinesEntered;
							}
							
						?>
					</td>
				</tr>
			</table>
		</td>
		<?php
			}
		}
		else
		{
			if(isset($_POST['reportProperties']))
			{
				$a_reportProperties	=	$_POST['reportProperties'];
				foreach($a_reportProperties as $key=>$value)
				{
					if($key == 1)
					{
						$totalMoney	=	$totalMoney+$t_directLevel1Rate+$t_directLevel2Rate;
		?>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">L1-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalDirectLevel1;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalDirectLevel1."X".$directLevel1Rate." = ".round($t_directLevel1Rate,2);
							}
							else
							{
								echo $totalDirectLevel1;
							}		
							
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">L2-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalDirectLevel2;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalDirectLevel2."X".$directLevel2Rate." = ".round($t_directLevel2Rate,2);
							}
							else
							{
								echo $totalDirectLevel2;
							}		
							
						?>
					</td>
				</tr>
			</table>
		</td>
		<?php
					}
					if($key == 2)
					{
						$totalMoney	=	$totalMoney+$t_indirectLevel1Rate+$t_indirectLevel2Rate;
		?>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">L1-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalIndirectLevel1;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalIndirectLevel1."X".$indirectLevel1Rate." = ".round($t_indirectLevel1Rate,2);
							}
							else
							{
								echo $totalIndirectLevel1;
							}		
							
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">L2-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalIndirectLevel2;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalIndirectLevel2."X".$indirectLevel2Rate." = ".round($t_indirectLevel2Rate,2);
							}
							else
							{
								echo $totalIndirectLevel2;
							}		
							
						?>
					</td>
				</tr>
			</table>
		</td>
		<?php
					}
					if($key == 3)
					{
						$totalMoney	=	$totalMoney+$t_qaLevel1Rate+$t_qaLevel2Rate;
		?>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">L1-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalQaLevel1;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalQaLevel1."X".$qaLevel1Rate." = ".round($t_qaLevel1Rate,2);
							}
							else
							{
								echo $totalQaLevel1;
							}		
							
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">L2-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalQaLevel2;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalQaLevel2."X".$qaLevel2Rate." = ".round($t_qaLevel2Rate,2);
							}
							else
							{
								echo $totalQaLevel2;
							}		
							
						?>
					</td>
				</tr>
			</table>
		</td>
		<?php
					}
					if($key == 4)
					{
						$totalMoney	=	$totalMoney+$t_auditLevel1Rate+$t_auditLevel2Rate;
		?>
		<td valign="top">
			<table width="100%">
				<tr>
					<td class="smalltext2" width="5%" valign="top">L1-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalAuditLevel1;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalAuditLevel1."X".$auditLevel1Rate." = ".round($t_auditLevel1Rate,2);
							}
							else
							{
								echo $totalAuditLevel1;
							}		
							
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">L2-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalAuditLevel2;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalAuditLevel2."X".$auditLevel2Rate." = ".round($t_auditLevel2Rate,2);
							}
							else
							{
								echo $totalAuditLevel2;
							}		
							
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
							$totalMoney	=	$totalMoney+$t_directLevel1Rate+$t_directLevel2Rate;
							$totalLines	=	$totalLines+$totalDirectLevel1;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalDirectLevel1."X".$indirectLevel1Rate." = ".round($t_indirectLevel1Rate,2);
							}
							else
							{
								echo $totalDirectLevel1;
							}		
							
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">L2-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalDirectLevel2;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalDirectLevel2."X".$directLevel2Rate." = ".round($t_directLevel2Rate,2);
							}
							else
							{
								echo $totalDirectLevel2;
							}	
							
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
							$totalMoney	=	$totalMoney+$t_indirectLevel1Rate+$t_indirectLevel2Rate;
							$totalLines	=	$totalLines+$totalIndirectLevel1;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalIndirectLevel1."X".$indirectLevel1Rate." = ".round($t_indirectLevel1Rate,2);
							}
							else
							{
								echo $totalIndirectLevel1;
							}	
							
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">L2-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalIndirectLevel2;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalIndirectLevel2."X".$indirectLevel2Rate." = ".round($t_indirectLevel2Rate,2);
							}
							else
							{
								echo $totalIndirectLevel2;
							}	
							
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
							$totalMoney	=	$totalMoney+$t_qaLevel1Rate+$t_qaLevel2Rate;
							$totalLines	=	$totalLines+$totalQaLevel1;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalQaLevel1."X".$qaLevel1Rate." = ".round($t_qaLevel1Rate,2);
							}
							else
							{
								echo $totalQaLevel1;
							}	
							
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">L2-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalQaLevel2;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalQaLevel2."X".$qaLevel2Rate." = ".round($t_qaLevel2Rate,2);
							}
							else
							{
								echo $totalQaLevel2;
							}	
							
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
							$totalMoney	=	$totalMoney+$t_auditLevel1Rate+$t_auditLevel2Rate;
							$totalLines	=	$totalLines+$totalAuditLevel1;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalAuditLevel1."X".$auditLevel1Rate." = ".round($t_auditLevel1Rate,2);
							}
							else
							{
								echo $totalAuditLevel1;
							}	
							
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top">L2-</td>
					<td class="smalltext2" valign="top">
						<?php
							$totalLines	=	$totalLines+$totalAuditLevel2;
							if($reportView == 1 || $reportView == 2)
							{
								echo $totalAuditLevel2."X".$auditLevel2Rate." = ".round($t_auditLevel2Rate,2);
							}
							else
							{
								echo $totalAuditLevel2;
							}	
							
						?>
					</td>
				</tr>
			</table>
		</td>
		<?php
			}
		}

		$tempTotal  = round($totalMoney,2);
		$grandTotal += $totalMoney;
		$grandLines	+= $totalLines;
		if($reportView == 1)
		{
	?>
	<td class="text2" valign="top"><?php echo $tempTotal;?></td>
	<td class="text2" valign="top"><?php echo $totalLines;?></td>
	<?php
		}
		elseif($reportView == 2)
		{
	?>
	<td class="text2" valign="top"><?php echo $tempTotal;?></td>
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
}
else
{
?>
<tr>
	<td colspan="15" class="error">
		<b>NO RECORD FOUND !!</b>
	</td>
</tr>
<tr>
	<td colspan="15" height="100"></td>
</tr>
<?php
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