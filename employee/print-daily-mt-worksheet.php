<?php
	session_start();
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();
	

	$date						=	"";
	$employeeId					=	"";
	$a_employeeId				=	array();
	$text						=	"";

	$totalDirectTranscription	=	0;
	$totalIndirectTranscription	=	0;
	$totalDirectVre				=	0;
	$totalIndirectVre			=	0;
	$totalDirectQa				=	0;
	$totalIndirectQa			=	0;
	$totalDirectPostAudit		=	0;
	$totalIndirectPostAudit		=	0;

	$grandTotalTrans			=	0;
	$grandTotalVre				=	0;
	$grandTotalQa				=	0;
	$grandTotalAudit			=	0;

	$searchBy					=	"";
	$andClause					=	"";
	$month						=	"";
	$year						=	"";
	$employeeType				=	0;
	$underManager				=	0;
	$a_managers				    =	$employeeObj->getAllEmployeeManager();

	if(isset($_GET['searchBy']))
	{
		$searchBy		=	$_GET['searchBy'];
		if($searchBy	==	1)
		{
			if(isset($_GET['date']))
			{
				$date			=	$_GET['date'];
				$andClause		=	" AND datewise_employee_works_money.workedOnDate='$date'";
				$text			=	"MT WORK SHEET ON - ".showDate($date);
				$orderBy		=	"firstName";
			}
		}
		else
		{
			if(isset($_GET['month']) && isset($_GET['year']))
			{
				$month			=	$_GET['month'];
				$year			=	$_GET['year'];

				$monthText		=	$a_month[$month];
				$text			=	"MT WORK SHEET ON - ".$monthText.",".$year;

				$andClause		=	" AND MONTH(workedOnDate)=$month AND YEAR(workedOnDate)=$year";
				$orderBy		=	"workedOnDate DESC";
			}
		}
	}
	if(isset($_GET['type']))
	{
		$employeeType		=	$_GET['type'];
		if(!empty($employeeType))
		{
			$andClause	   .=	" AND employee_details.employeeType=$employeeType";
			$text		   .=	" for ".$a_inetExtEmployee[$employeeType]." employees";
		}
	}
	if(isset($_GET['manager']))
	{
		$underManager		=	$_GET['manager'];
		if(!empty($underManager))
		{
			$andClause	   .=	" AND employee_details.underManager=$underManager";
			$text		   .=	" under manager ".$a_managers[$underManager];
		}
	}
	if(isset($_GET['employee']))
	{
		$employeeId			=	$_GET['employee'];
		if(!empty($employeeId))
		{
			$a_employeeId		=	explode(",",$employeeId);
			$andClause		   .=	" AND datewise_employee_works_money.employeeId IN ($employeeId)";

			$totalEmloyee		=	count($a_employeeId);

			if($totalEmloyee < 2 && $totalEmloyee > 0)
			{
				foreach($a_employeeId as $key=>$value)
				{
					$employeeName	=	$employeeObj->getEmployeeName($value);
				}
				$text			.=	" FOR EMPLOYEE ".$employeeName;
			}
			else
			{
				$text			.=	" FOR MULTILE EMPLOYES";
			}
		}
	}


	
	$whereClause	=   " WHERE datewise_employee_works_money.ID > ".MAX_SEARCH_MT_EMPLOYEE_WORKID." AND employee_shift_rates.departmentId=1";

	$storePath		=	SITE_ROOT_FILES."/files/excel-files/";

	require_once 	SITE_ROOT. '/excel/Classes/PHPExcel.php';
		
	/****EXCEL WRITING CODE****/
	// Create new PHPExcel object
	$i	=	0;
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(48);
	$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(48);
	$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(48);
	$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(15);
	
	
	$sharedStyle1 = new PHPExcel_Style();
	
	$sharedStyle1->applyFromArray(
		array('fill' 	=> array(
									'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
									'color'		=> array('argb' => 'FFFFFF00')
								),
			  'borders' => array(
									'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
									'right'		=> array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
								)
			 ));

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Transaction details');
	
	$objDrawing = new PHPExcel_Worksheet_Drawing();
	$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

	$i			=	1;
	
	$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A$i:U$i");
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,"EMPLOYEE NAME");
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,"WORKED ON");
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,"COMMENTS");
	$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,"PLATFORM");
	$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,"CLIENT");
	$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,"TRANSCRIPTION-DSP");
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,"TRANSCRIPTION-NDSP");
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,"TOTAL TRANSCRIPTION");
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,"USER ID");
	$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,"VRE-DSP");
	$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,"VRE-NDSP");
	$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('L'.$i,"TOTAL VRE");
	$objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('M'.$i,"USERID");
	$objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('N'.$i,"QA-DSP");
	$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$i,"QA-NDSP");
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('P'.$i,"TOTAL QA");
	$objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i,"USERID");
	$objPHPExcel->getActiveSheet()->getStyle('Q'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('R'.$i,"NIGHT SHIFT LINES-TRANSCRIPTION");
	$objPHPExcel->getActiveSheet()->getStyle('R'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('S'.$i,"NIGHT SHIFT LINES-VRE");
	$objPHPExcel->getActiveSheet()->getStyle('S'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('T'.$i,"NIGHT SHIFT LINES TOTAL");
	$objPHPExcel->getActiveSheet()->getStyle('T'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('U'.$i,"USERID");
	$objPHPExcel->getActiveSheet()->getStyle('U'.$i)->getFont()->setBold(true);
	

	$query			=	"SELECT datewise_employee_works_money.*,fullName FROM datewise_employee_works_money INNER JOIN employee_details ON datewise_employee_works_money.employeeId=employee_details.employeeId INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId".$whereClause.$andClause;
	$result			=	mysql_query($query);
	if(mysql_num_rows($result))
	{
		$k			=	0;
		while($row	=	mysql_fetch_assoc($result))
		{
			$i++;
			$k++;
			$employeeId					=	$row['employeeId'];
			$employeeName				=	stripslashes($row['fullName']);
			$platform					=	$row['platform'];
			$customerId					=	$row['customerId'];
			$comments					=	$row['comments'];
			$workedOn					=	showDate($row['workedOnDate']);

			$transcriptionLinesEntered	=	$row['totalDirectTrascriptionLines'];
			$vreLinesEntered			=	$row['totalDirectVreLines'];
			$qaLinesEntered				=	$row['totalQaLines'];
			
			$indirectTranscriptionLinesEntered	=	$row['totalIndirectTrascriptionLines'];
			$indirectVreLinesEntered	=	$row['totalIndirectVreLines'];
			$indirectQaLinesEntered		=	$row['totalIndirectQaLines'];

			$auditLinesEntered			=	$row['totalDirectAuditLines'];
			$indirectAuditLinesEntered	=	$row['totalIndirectAuditLines'];

			$transcriptionUserId		=	$row['transcriptionUserId'];
			$vreUserId					=	$row['vreUserId'];
			$qaUserId					=	$row['qaUserId'];
			$auditUserId				=	$row['auditUserId'];

			$platName					=	$employeeObj->getPlatformName($platform);
			$customerName				=	$employeeObj->getCustomerName($customerId,$platform);
			
			$employeeName				=	ucwords($employeeName);

			$totalTrans					=	0;
			$totalVre					=	0;
			$totalQa					=	0;
			$totalAudit					=	0;

			$totalTrans					=	$transcriptionLinesEntered+$indirectTranscriptionLinesEntered;
			$totalVre					=	$vreLinesEntered+$indirectVreLinesEntered;
			$totalQa					=	$qaLinesEntered+$indirectQaLinesEntered;
			$totalAudit					=	$auditLinesEntered+$indirectAuditLinesEntered;

			$grandTotalTrans			=	$transcriptionLinesEntered+$indirectTranscriptionLinesEntered;
			$grandTotalVre				=	$vreLinesEntered+$indirectVreLinesEntered;
			$grandTotalQa				=	$qaLinesEntered+$indirectQaLinesEntered;
			$grandTotalAudit			=	$auditLinesEntered+$indirectAuditLinesEntered;

			
			$totalDirectTranscription	=	$totalDirectTranscription+$transcriptionLinesEntered;

			$totalIndirectTranscription	=	$totalIndirectTranscription+$indirectTranscriptionLinesEntered;

			$totalDirectVre				=	$totalDirectVre+$vreLinesEntered;

			$totalIndirectVre			=	$totalIndirectVre+$indirectVreLinesEntered;

			$totalDirectQa				=	$totalDirectQa+$qaLinesEntered;

			$totalIndirectQa			=	$totalIndirectQa+$indirectQaLinesEntered;

			$totalDirectPostAudit		=	$totalDirectPostAudit+$auditLinesEntered;

			$totalIndirectPostAudit		=	$totalIndirectPostAudit+$indirectAuditLinesEntered;

			$grandTotalTrans			=	$totalDirectTranscription+$totalIndirectTranscription;

			$grandTotalVre				=	$totalDirectVre+$totalIndirectVre;

			$grandTotalQa				=	$totalDirectQa+$totalIndirectQa;

			$grandTotalAudit			=	$totalDirectPostAudit+$totalIndirectPostAudit;

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$employeeName);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$workedOn);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$comments);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$platName);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$customerName);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$transcriptionLinesEntered);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$indirectTranscriptionLinesEntered);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$totalTrans);

			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$transcriptionUserId);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$vreLinesEntered);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,$indirectVreLinesEntered);
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i,$totalVre);
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$i,$vreUserId);
			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i,$qaLinesEntered);
			$objPHPExcel->getActiveSheet()->setCellValue('O'.$i,$indirectQaLinesEntered);
			$objPHPExcel->getActiveSheet()->setCellValue('P'.$i,$totalQa);
			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i,$qaUserId);
			$objPHPExcel->getActiveSheet()->setCellValue('R'.$i,$auditLinesEntered);
			$objPHPExcel->getActiveSheet()->setCellValue('S'.$i,$indirectAuditLinesEntered);
			$objPHPExcel->getActiveSheet()->setCellValue('T'.$i,$totalAudit);
			$objPHPExcel->getActiveSheet()->setCellValue('U'.$i,$auditUserId);
		}
		$k	=	$i+1;

		$objPHPExcel->getActiveSheet()->setCellValue('E'.$k,"GRAND TOTAL");
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$k,$totalDirectTranscription);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$k,$totalIndirectTranscription);
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$k,$grandTotalTrans);

		$objPHPExcel->getActiveSheet()->setCellValue('J'.$k,$totalDirectVre);
		$objPHPExcel->getActiveSheet()->setCellValue('K'.$k,$totalIndirectVre);
		$objPHPExcel->getActiveSheet()->setCellValue('L'.$k,$grandTotalVre);

		$objPHPExcel->getActiveSheet()->setCellValue('N'.$k,$totalDirectQa);
		$objPHPExcel->getActiveSheet()->setCellValue('O'.$k,$totalIndirectQa);
		$objPHPExcel->getActiveSheet()->setCellValue('P'.$k,$grandTotalQa);

		$objPHPExcel->getActiveSheet()->setCellValue('R'.$k,$totalDirectPostAudit);
		$objPHPExcel->getActiveSheet()->setCellValue('S'.$k,$totalIndirectPostAudit);
		$objPHPExcel->getActiveSheet()->setCellValue('T'.$k,$grandTotalAudit);

	}
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

	$filaneNamePrefixed	=	"123456789";
			
	$storeFileName	=	md5($filaneNamePrefixed)."-print-daily-mt-worksheet.xls";
	$objWriter->save($storePath.$storeFileName);
	
	 echo "<br><br><center><a href='".SITE_URL_EMPLOYEES."/download-excel.php?t=".$storeFileName."' class='linkstyle8' target='_blank'>DOWNLOAD EXCEL SHEET</a></center>";
?>