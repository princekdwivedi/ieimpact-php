<?php
	session_start();
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	include(SITE_ROOT_EMPLOYEES .  "/classes/employee.php");
	include(SITE_ROOT			.  "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/common-array.php");
	$employeeObj				=	new employee();
	
	$whereClause				=	"WHERE isActive=1";
	$andClause					=	"";
	$andClause1					=	"";
	$orderBy					=	"firstName";

	function getPlateformClientsNames($employeeId){
		$clients_names			=	array();
		$query					=	"SELECT employee_clients.*,name FROM employee_clients INNER JOIN platform_clients ON employee_clients.platform=platform_clients.platfromId WHERE employeeId=$employeeId ORDER BY name";
		$result					=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			while($row			=	mysql_fetch_assoc($result))
			{
				$plateformName	=	stripslashes($row['name']);
				$clientId		=	$row['clientId'];
				$platformId		=	$row['platform'];				

				$query1			=	"SELECT name FROM platform_clients WHERE parentId=$platformId AND customerId <> 0 AND customerId IN ($clientId) ORDER BY name";
				$result1		=	mysql_query($query1);
				if(mysql_num_rows($result1))
				{
					$a_clients			=	array();
					while($row1			=	mysql_fetch_assoc($result1))
					{
						$clientName		=	stripslashes($row1['name']);
						$a_clients[]	=	$clientName;
					}
				}
				if(count($a_clients) > 0)
				{
					$clients_names[]	= $plateformName.":".implode(", ",$a_clients);
				}
				
			}
			
			if(count($clients_names) > 0)
			{
				$clients_names	=  implode(". ",$clients_names);
			}
			else
			{
				$clients_names	=	"";
			}
			return $clients_names;
		}
	}

	$headerText					=	" Under All Managers";

	if(isset($_GET['isDisplayingManager']))
	{
		$isDisplayingManager    =  $_GET['isDisplayingManager'];
		if(!empty($isDisplayingManager))
		{
			$managerName		=	$employeeObj->getEmployeeName($isDisplayingManager);
			$andClause			=	" AND employee_details.underManager=$isDisplayingManager";
			$headerText			=	" Under Manager - ".$managerName;

		}
	}


	$storePath					=	SITE_ROOT_FILES."/files/excel-files/";

	require_once 	SITE_ROOT. '/excel/Classes/PHPExcel.php';
		
	/****EXCEL WRITING CODE****/
	// Create new PHPExcel object
	$i	=	0;
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(35);

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
	$objPHPExcel->getActiveSheet()->setTitle('Attendence Details');
	
	$objDrawing = new PHPExcel_Worksheet_Drawing();
	$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

	$i			=	1;

	$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A$i:J$i");
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$headerText);

	$i++;
	
	$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A$i:J$i");
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,"SR. NO");
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);

	$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,"IS ON LEAVE TODAY");
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);

	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,"EMPLOYEE NAME");
	$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);

	$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,"CLIENTS");
	$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);


	$objPHPExcel->getActiveSheet()->setCellValue('E'.$i," SHIFT TIMINGS");
	$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setBold(true);

	$objPHPExcel->getActiveSheet()->setCellValue('F'.$i," WEEKLY OFF");
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setBold(true);

	$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,"SPECIAL SHIFT");
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->setBold(true);

	$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,"NUANCE ID");
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getFont()->setBold(true);

	$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,"FIESA ID");
	$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getFont()->setBold(true);

	$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,"COMMENTS/ALERTS");
	$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getFont()->setBold(true);
	

	$query	=	"SELECT employee_details.* FROM employee_details INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId WHERE isActive=1 AND employee_shift_rates.departmentId=1".$andClause." ORDER BY firstName";
	$result		=	mysql_query($query);
	if(mysql_num_rows($result))
	{
		$k	=	0;
		while($row				=	mysql_fetch_assoc($result))
		{
			$i++;
			$k++;
			$employeeId			=	$row['employeeId'];
			$fullName			=	stripslashes($row['fullName']);
			$hasPdfAccess		=	$row['hasPdfAccess'];
			$postAuditAccuracy 	=	stripslashes($row['postAuditAccuracy']);
			$pendingAccuracy	=	stripslashes($row['pendingAccuracy']);
			$commentsAlerts		=	stripslashes($row['commentsAlerts']);
			$nuanceID			=	stripslashes($row['nuanceID']);
			$fiesaID			=	stripslashes($row['fiesaID']);
			$shiftFrom			=	stripslashes($row['shiftFrom']);
			$shiftTo			=	stripslashes($row['shiftTo']);
			$accuracyClients	=	stripslashes($row['accuracyClients']);

			//$clients			=	getPlateformClientsNames($employeeId);

			$timings			=	$shiftFrom." - ".$shiftTo;

			$onLeaveText		=	"No";
				
			$onLeave			=	@mysql_result(dbQuery("SELECT onLeave FROM employee_attendence WHERE attendenceId > ".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID." AND employeeId=$employeeId AND loginDate='".$nowDateIndia."'"),0);
			if($onLeave			==	1)
			{
				$onLeaveText	=	"Yes";
			}
			elseif($onLeave		==	2)
			{
				$onLeaveText	=	"H.Day";
			}

			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$k);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$onLeaveText);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$fullName);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$accuracyClients);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$timings);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$postAuditAccuracy);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$pendingAccuracy);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$nuanceID);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$fiesaID);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$commentsAlerts);
		}
	}
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

	$filaneNamePrefixed	=	"111333555";
			
	$storeFileName	=	md5($filaneNamePrefixed)."-employee-add-edit-accuracy.xls";
	$objWriter->save($storePath.$storeFileName);
	
	 echo "<br><br><center><a href='".SITE_URL_EMPLOYEES."/download-excel.php?t=".$storeFileName."' class='linkstyle8' target='_blank'>DOWNLOAD EXCEL SHEET</a></center>";
?>