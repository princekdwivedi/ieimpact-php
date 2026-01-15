<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");	
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	$employeeObj	=	new employee();
	$departmentId	=	1;

	$query			=	"SELECT * FROM employee_works WHERE employeeId=246 ORDER BY workedOn";
	$result			=	dbQuery($query);
	if(mysql_num_rows($result))
	{
		while($row		=	mysql_fetch_assoc($result))
		{
			$employeeId					=	$row['employeeId'];
			$workId						=	$row['workId'];
			$platform					=	$row['platform'];
			$customerId					=	$row['customerId'];
			$transcriptionLinesEntered	=	$row['transcriptionLinesEntered'];
			$vreLinesEntered			=	$row['vreLinesEntered'];
			$qaLinesEntered				=	$row['qaLinesEntered'];
			$indirectTranscriptionLinesEntered	=	$row['indirectTranscriptionLinesEntered'];
			$indirectVreLinesEntered	=	$row['indirectVreLinesEntered'];
			$indirectQaLinesEntered		=	$row['indirectQaLinesEntered'];
			$auditLinesEntered			=	$row['auditLinesEntered'];
			$indirectAuditLinesEntered	=	$row['indirectAuditLinesEntered'];
			$directLevel1				=	$row['directLevel1'];
			$directLevel2				=	$row['directLevel2'];
			$indirectLevel1				=	$row['indirectLevel1'];
			$indirectLevel2				=	$row['indirectLevel2'];
			$auditLevel1				=	$row['auditLevel1'];
			$auditLevel2				=	$row['auditLevel2'];
			$qaLevel1					=	$row['qaLevel1'];
			$qaLevel2					=	$row['qaLevel2'];
			$workedOn					=	$row['workedOn'];

			$departmentId				=	@mysql_result(dbQuery("SELECT departmentId FROM employee_shift_rates WHERE employeeId=246"),0);

			if($result1		=	$employeeObj->getRatesOfEmployee($employeeId,$workedOn))
			{
				$row1							=	mysql_fetch_assoc($result1);
				$directTranscriptionRate		=	$row1['directTranscriptionRate'];
				$indirectTranscriptionRate		=	$row1['indirectTranscriptionRate'];
				$directVreRate					=	$row1['directVreRate'];
				$indirectVreRate				=	$row1['indirectVreRate'];
				$directQaRate					=	$row1['directQaRate'];
				$indirectQaRate					=	$row1['indirectQaRate'];
				$directAuditRate				=	$row1['directAuditRate'];
				$indirectAuditRate				=	$row1['indirectAuditRate'];

				$directLevel1Rate				=	$row1['directLevel1Rate'];
				$directLevel2Rate				=	$row1['directLevel2Rate'];
				$indirectLevel1Rate				=	$row1['indirectLevel1Rate'];
				$indirectLevel2Rate				=	$row1['indirectLevel2Rate'];
				$qaLevel1Rate					=	$row1['qaLevel1Rate'];
				$qaLevel2Rate					=	$row1['qaLevel2Rate'];
				$auditLevel1Rate				=	$row1['auditLevel1Rate'];
				$auditLevel2Rate				=	$row1['auditLevel2Rate'];

				$totalDirectTrascriptionMoney	=	$transcriptionLinesEntered*$directTranscriptionRate;
				$totalDirectTrascriptionMoney	=	round($totalDirectTrascriptionMoney);

				$totalIndirectTrascriptionMoney	=	$indirectTranscriptionLinesEntered*$indirectTranscriptionRate;
				$totalIndirectTrascriptionMoney	=	round($totalIndirectTrascriptionMoney);

				$totalDirectVreMoney			=	$vreLinesEntered*$directVreRate;
				$totalDirectVreMoney			=	round($totalDirectVreMoney);

				$totalIndirectVreMoney			=	$indirectVreLinesEntered*$indirectVreRate;
				$totalIndirectVreMoney			=	round($totalIndirectVreMoney);

				$totalDirectQaMoney				=	$qaLinesEntered*$directQaRate;
				$totalDirectQaMoney				=	round($totalDirectQaMoney);

				$totalIndirectQaMoney			=	$indirectQaLinesEntered*$indirectQaRate;
				$totalIndirectQaMoney			=	round($totalIndirectQaMoney);

				$totalDirectAuditMoney			=	$auditLinesEntered*$directAuditRate;
				$totalDirectAuditMoney			=	round($totalDirectAuditMoney);

				$totalIndirectAuditMoney		=	$indirectAuditLinesEntered*$indirectAuditRate;
				$totalIndirectAuditMoney		=	round($totalIndirectAuditMoney);

				$totalDirectLevel1Money			=	$directLevel1*$directLevel1Rate;
				$totalDirectLevel1Money			=	round($totalDirectLevel1Money);

				$totalDirectLevel2Money			=	$directLevel2*$directLevel2Rate;
				$totalDirectLevel2Money			=	round($totalDirectLevel2Money);

				$totalIndirectLevel1Money		=	$indirectLevel1*$indirectLevel1Rate;
				$totalIndirectLevel1Money		=	round($totalIndirectLevel1Money);

				$totalIndirectLevel2Money		=	$indirectLevel2*$indirectLevel2Rate;
				$totalIndirectLevel2Money		=	round($totalIndirectLevel2Money);

				$totalQaLevel1Money				=	$qaLevel1*$qaLevel1Rate;
				$totalQaLevel1Money				=	round($totalQaLevel1Money);

				$totalQaLevel2Money				=	$qaLevel2*$qaLevel2Rate;
				$totalQaLevel2Money				=	round($totalQaLevel2Money);

				$totalAuditLevel1Money			=	$auditLevel1*$auditLevel1Rate;
				$totalAuditLevel1Money			=	round($totalAuditLevel1Money);
	
				$totalAuditLevel2Money			=	$auditLevel2*$auditLevel2Rate;
				$totalAuditLevel2Money			=	round($totalAuditLevel2Money);

				$optionQuery	=	" SET workId=$workId,employeeId=246,platform=$platform,customerId=$customerId,departmentId=$departmentId,workedOnDate='$workedOn',totalDirectTrascriptionLines=$transcriptionLinesEntered,directTranscriptionRate=$directTranscriptionRate,totalDirectTrascriptionMoney=$totalDirectTrascriptionMoney,totalIndirectTrascriptionLines=$indirectTranscriptionLinesEntered,indirectTranscriptionRate=$indirectTranscriptionRate,totalIndirectTrascriptionMoney=$totalIndirectTrascriptionMoney,totalDirectVreLines=$vreLinesEntered,directVreRate=$directVreRate,totalDirectVreMoney=$totalDirectVreMoney,totalIndirectVreLines=$indirectVreLinesEntered,indirectVreRate=$indirectVreRate,totalIndirectVreMoney=$totalIndirectVreMoney,totalQaLines=$qaLinesEntered,directQaRate=$directQaRate,totalDirectQaMoney=$totalDirectQaMoney,totalIndirectQaLines=$indirectQaLinesEntered,indirectQaRate=$indirectQaRate,totalIndirectQaMoney=$totalIndirectQaMoney,totalDirectAuditLines=$auditLinesEntered,directAuditRate=$directAuditRate,totalDirectAuditMoney=$totalDirectAuditMoney,totalIndirectAuditLines=$indirectAuditLinesEntered,indirectAuditRate=$indirectAuditRate,totalIndirectAuditMoney=$totalIndirectAuditMoney,   totalDirectLevel1Lines=$directLevel1,directLevel1Rate=$directLevel1Rate,totalDirectLevel1Money=$totalDirectLevel1Money,totalDirectLevel2Lines=$directLevel2,directLevel2Rate=$directLevel2Rate,totalDirectLevel2Money=$totalDirectLevel2Money,totalIndirectLevel1Lines=$indirectLevel1,indirectLevel1Rate=$indirectLevel1Rate,totalIndirectLevel1Money=$totalIndirectLevel1Money,totalIndirectLevel2Lines=$indirectLevel2,indirectLevel2Rate=$indirectLevel2Rate,totalIndirectLevel2Money=$totalIndirectLevel2Money,totalQaLevel1Lines=$qaLevel1,qaLevel1Rate=$qaLevel1Rate,totalQaLevel1Money=$totalQaLevel1Money,totalQaLevel2Lines=$qaLevel2,qaLevel2Rate=$qaLevel2Rate,totalQaLevel2Money=$totalQaLevel2Money,totalAuditLevel1Lines=$auditLevel1,auditLevel1Rate=$auditLevel1Rate,totalAuditLevel1Money=$totalAuditLevel1Money,totalAuditLevel2Lines=$auditLevel2,auditLevel2Rate=$auditLevel2Rate,totalAuditLevel2Money=$totalAuditLevel2Money,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'";

				dbQuery("INSERT INTO datewise_employee_works_money".$optionQuery);
				dbQuery("UPDATE employee_works SET transferredToDatewise=1 WHERE workId=$workId AND employeeId=246");
			}
		}
		echo "<br><center><b>successfully done !!</b></center>";
	}
	else
	{
		echo "<br><center><b>No record found !!</b></center>";
	}
?>