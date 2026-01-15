<?php
	ob_start();
	session_start();
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-admin-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();

	$month 							=	0;
	$year 							=	0;
	$searchType 					= 	'monthwise'; // Default to monthwise for backward compatibility
	$dateFrom 						= 	'';
	$dateTo 						= 	'';
	$dateCondition					=	'';
	$headingText					=	'';

	// Check search type
	if(isset($_GET['searchType'])){
		$searchType 				=	$_GET['searchType'];
	}

	if($searchType == 'monthwise'){
		if(isset($_GET['month']) && isset($_GET['year'])){
			$month 					=	(int)$_GET['month'];
			$year 					=	(int)$_GET['year'];
			if(!empty($month) && !empty($year)){
				$dateCondition		=	"MONTH(assignToEmployee)=$month AND YEAR(assignToEmployee)=$year";
				$headingText		=	"GET PDF WORK STATUS - ".$a_month[$month].", ".$year;
			}
		}
	} else {
		// Datewise - FROM DATE is mandatory, TO DATE is optional
		if(isset($_GET['dateFrom'])){
			$dateFrom 				=	trim($_GET['dateFrom']);
			
			// Validate date format (YYYY-MM-DD)
			if(!empty($dateFrom) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)){
				$dateFrom 			=	addslashes($dateFrom);
				
				// Check if TO DATE is also provided
				if(isset($_GET['dateTo']) && !empty($_GET['dateTo'])){
					$dateTo 		=	trim($_GET['dateTo']);
					// Validate TO DATE format
					if(preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)){
						$dateTo 		=	addslashes($dateTo);
						// Date range search
						$dateCondition	=	"DATE(assignToEmployee) BETWEEN '$dateFrom' AND '$dateTo'";
						$headingText	=	"GET PDF WORK STATUS - ".date('M d, Y', strtotime($dateFrom))." to ".date('M d, Y', strtotime($dateTo));
					}
				} else {
					// Single date search (only FROM DATE)
					$dateCondition	=	"DATE(assignToEmployee) = '$dateFrom'";
					$headingText	=	"GET PDF WORK STATUS - ".date('M d, Y', strtotime($dateFrom));
				}
			}
		}
	}

	
	$storePath					=	SITE_ROOT_FILES."/files/excel-files/";

	require_once 	SITE_ROOT. '/excel/Classes/PHPExcel.php';

	/****EXCEL WRITING CODE****/
	// Create new PHPExcel object
	$i	=	0;
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
	
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
	$objPHPExcel->getActiveSheet()->setTitle('GET MONTHLY PDF WORK STATUS');
	
	$objDrawing = new PHPExcel_Worksheet_Drawing();
	$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

	$i			=	1;

	$objPHPExcel->getActiveSheet()->setCellValue("A".$i,$headingText);

	$i++;
	
	$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A$i:AF$i");
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,"EMPLOYEE NAME");
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,"FILES PROCESSED");
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,"NATALIA FILES");
	$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,"MICHAEL BANKS");
	$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,"AWFUL RATING");
	$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,"POOR RATING");
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setBold(true);

	// OPTIMIZED QUERY - Single query with conditional aggregation for better performance
	if(!empty($dateCondition)){
		$query = "SELECT 
			mo.acceptedBy,
			ed.fullName,
			COUNT(*) AS totalDone,
			SUM(CASE WHEN mo.memberId=3778 THEN 1 ELSE 0 END) AS nataliaOrders,
			SUM(CASE WHEN mo.memberId=3545 THEN 1 ELSE 0 END) AS bankOrders,
			SUM(CASE WHEN mo.rateGiven=1 THEN 1 ELSE 0 END) AS awfulOrders,
			SUM(CASE WHEN mo.rateGiven=2 THEN 1 ELSE 0 END) AS poorOrders
		FROM members_orders mo
		INNER JOIN employee_details ed ON mo.acceptedBy = ed.employeeId
		WHERE $dateCondition
		GROUP BY mo.acceptedBy, ed.fullName
		ORDER BY totalDone DESC, ed.fullName";
		
		$result = dbQuery($query);
		if(mysqli_num_rows($result))
		{
			while($row = mysqli_fetch_assoc($result))
			{
				$i++;
				$employeeId				=	$row['acceptedBy'];
				$fullName 				=	stripslashes($row['fullName']);
				$totalDone				=	$row['totalDone'];
				$nataliaOrders 			=	$row['nataliaOrders'];
				$bankOrders 			=	$row['bankOrders'];
				$awfulOrders 			=	$row['awfulOrders'];
				$poorOrders 			=	$row['poorOrders'];

				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$fullName);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$totalDone);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$nataliaOrders);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$bankOrders);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$awfulOrders);	
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$poorOrders);						
			}
		}
	}
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

	$filaneNamePrefixed	=	"123456789";
		
	$storeFileName	    =	md5($filaneNamePrefixed)."-employee-monthly-work-sheet.xls";
	$objWriter->save($storePath.$storeFileName);
	
	 echo "<br><br><center><a href='".SITE_URL_EMPLOYEES."/download-excel.php?t=".$storeFileName."' class='linkstyle8' target='_blank'>DOWNLOAD EXCEL SHEET</a></center>";
?>