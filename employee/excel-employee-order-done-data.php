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


	$whereClause				=	"WHERE isActive=1";
	$andClause					=	"";
	$andClause1					=	"";
	$orderBy					=	"firstName";
	$andClause2					=	"";
	$column						=	"acceptedBy";
	$column1					=	"assignToEmployee";
	$headingText				=	"Processed On - ";
	$a_rows						=	array("1"=>"B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF");
	
	
	if(isset($_GET['type']))
	{
		$type				    =	$_GET['type'];
		if($type	            ==	2)
		{
			$column				=	"qaDoneById";
			$column1			=	"assignToEmployee";
			$headingText		=	"QA Done On - ";
		}
	}
	if(isset($_GET['employeeId']))
	{
		$employeeId				=	$_GET['employeeId'];
		if(!empty($employeeId)){
			$andClause1			=	" AND employeeId=".$employeeId;
		}			
	}

	if(isset($_GET['month']) && isset($_GET['year']))
	{
		$month				    =	$_GET['month'];
		$monthText			    =	$a_month[$month];
		$year				    =	$_GET['year'];	
		$t_month				=	$month;
		if($month < 10){
			$t_month			=	substr($month, 1);
		}
	}
	$headingText				=	$headingText.$monthText.", ".$year;

	$totalMonthDays				=	$a_daysInMonth[$t_month];
	

	$storePath					=	SITE_ROOT_FILES."/files/excel-files/";

	require_once 	SITE_ROOT. '/excel/Classes/PHPExcel.php';

	/****EXCEL WRITING CODE****/
	// Create new PHPExcel object
	$i	=	0;
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(5);
	
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
	$objPHPExcel->getActiveSheet()->setTitle('Employee Order Done Details');
	
	$objDrawing = new PHPExcel_Worksheet_Drawing();
	$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

	$i			=	1;

	$objPHPExcel->getActiveSheet()->setCellValue("A".$i,$headingText);

	$i++;
	
	$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A$i:AF$i");
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,"EMPLOYEE NAME");
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,"1");
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,"2");
	$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,"3");
	$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,"4");
	$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,"5");
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,"6");
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,"7");
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,"8");
	$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,"9");
	$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,"10");
	$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('L'.$i,"11");
	$objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('M'.$i,"12");
	$objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('N'.$i,"13");
	$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$i,"14");
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('P'.$i,"15");
	$objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i,"16");
	$objPHPExcel->getActiveSheet()->getStyle('Q'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('R'.$i,"17");
	$objPHPExcel->getActiveSheet()->getStyle('R'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('S'.$i,"18");
	$objPHPExcel->getActiveSheet()->getStyle('S'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('T'.$i,"19");
	$objPHPExcel->getActiveSheet()->getStyle('T'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('U'.$i,"20");
	$objPHPExcel->getActiveSheet()->getStyle('U'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('V'.$i,"21");
	$objPHPExcel->getActiveSheet()->getStyle('V'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('W'.$i,"22");
	$objPHPExcel->getActiveSheet()->getStyle('W'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('X'.$i,"23");
	$objPHPExcel->getActiveSheet()->getStyle('X'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('Y'.$i,"24");
	$objPHPExcel->getActiveSheet()->getStyle('Y'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('Z'.$i,"25");
	$objPHPExcel->getActiveSheet()->getStyle('Z'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('AA'.$i,"26");
	$objPHPExcel->getActiveSheet()->getStyle('AA'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('AB'.$i,"27");
	$objPHPExcel->getActiveSheet()->getStyle('AB'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('AC'.$i,"28");
	$objPHPExcel->getActiveSheet()->getStyle('AC'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('AD'.$i,"29");
	$objPHPExcel->getActiveSheet()->getStyle('AD'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('AE'.$i,"30");
	$objPHPExcel->getActiveSheet()->getStyle('AE'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('AF'.$i,"31");
	$objPHPExcel->getActiveSheet()->getStyle('AF'.$i)->getFont()->setBold(true);


	$query			=	"SELECT employeeId,fullName FROM employee_details WHERE isActive=1 AND hasPdfAccess=1".$andClause1." ORDER BY fullName";
	$result			=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row			=	mysqli_fetch_assoc($result))
		{
			$i++;
			$employeeId		=	$row['employeeId'];
			$fullName		=	stripslashes($row['fullName']);
			if($type		==	1){
				
				$query1		=	"SELECT COUNT(*) as totalOrders,orderAddedOn as date FROM members_orders WHERE acceptedBy=$employeeId AND MONTH(orderAddedOn)=$month AND YEAR(orderAddedOn)=$year GROUP BY orderAddedOn ORDER BY orderAddedOn";
			}
			else{
				$query1		=	"SELECT COUNT(*) as totalOrders,orderCompletedOn as date FROM members_orders_reply WHERE qaDoneBy=$employeeId AND MONTH(orderCompletedOn)=$month AND YEAR(orderCompletedOn)=$year GROUP BY orderCompletedOn ORDER BY orderCompletedOn";
			}
			$display_dates	=	array();
			
			$result1		=	dbQuery($query1);
			if(mysqli_num_rows($result1)){
				while($row1	              =	mysqli_fetch_assoc($result1)){
					$totalOrders	      =	$row1['totalOrders'];
					$date			      =	$row1['date'];
					$display_dates[$date] = $totalOrders;
				}
			}		


			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$fullName);

			
			for($j=1;$j<=$totalMonthDays;$j++){
				$text		=	$a_rows[$j];

				$ddm		=	$j;
				if($ddm < 10)
				{
					$ddm	=	"0".$ddm;
				}

				$orderDate	=	$year."-".$month."-".$ddm;
				if(array_key_exists($orderDate,$display_dates)){
					$total_found	=	$display_dates[$orderDate];
				}
				else{
					$total_found	=	0;
				}


				$objPHPExcel->getActiveSheet()->setCellValue($text.$i,$total_found);
			}			
		}
	}
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

	$filaneNamePrefixed	=	"123456789";
			
	$storeFileName	=	md5($_SESSION['loginName'])."-employee-process-qadone-orders.xls";
	$objWriter->save($storePath.$storeFileName);
	
	echo "<br><br><center><a href='".SITE_URL."/admin/download-excel.php?t=".$storeFileName."' class='linkstyle8' target='_blank'>DOWNLOAD EXCEL SHEET</a></center>";		
?>