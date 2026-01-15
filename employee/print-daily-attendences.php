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
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();
	
	$departmentId				=	0;
	$employeeType				=	0;
	$underManager				=	0;
	$table						=	"employee_details";
	$andClause					=	"";
	$andClause1					=	"";
	$andClause2					=	"";
	$showForm					=	false;
	$employeeId					=	0;
	$a_employeeId				=	array();
	$a_managers					=	$employeeObj->getAllEmployeeManager();
	$searchOn					=	"";
	$displayDate				=	"";

	if(isset($_GET['searchOn']))
	{
		$searchOn					=	$_GET['searchOn'];
		if(!empty($searchOn))
		{
			list($day,$month,$year)	=	explode("-",$searchOn);
			$t_searchOn				=	$year."-".$month."-".$day;
			$displayDate			=	showDate($t_searchOn);
		}
	}
	if(isset($_GET['employeeType']))
	{
		$employeeType				=	$_GET['employeeType'];
	}
	if(isset($_GET['underManager']))
	{
		$underManager				=	$_GET['underManager'];
	}
	if(isset($_GET['departmentId']))
	{
		$departmentId				=	$_GET['departmentId'];
	}
	if(isset($_GET['employeeId']))
	{
		$employeeId					=	$_GET['employeeId'];
	}
	
	if(!empty($employeeType))
	{
		$andClause1				   .=	" AND employee_details.employeeType=$employeeType";
	}
	if(!empty($underManager))
	{
		$andClause1				   .=	" AND employee_details.underManager=$underManager";
	}
	if(!empty($employeeId))
	{
		$andClause2				    =	" AND employee_details.employeeId IN ($employeeId)";
	}

	$storePath						=	SITE_ROOT_FILES."/files/excel-files/";

	require_once 	SITE_ROOT. '/excel/Classes/PHPExcel.php';
		
	/****EXCEL WRITING CODE****/
	// Create new PHPExcel object
	$i	=	0;
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
			
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
	
	$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A$i:F$i");
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,"SR. NO");
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,"DATE");
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,"EMPLOYEE NAME");
	$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,"ATTENDENCE IN AT");
	$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,"ATTENDENCE OUT AT");
	$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,"SHIFT TIME");
	

	$query	=	"SELECT employee_details.employeeId,firstName,lastName,isShiftTimeAdded,shiftFrom,shiftTo,isLogin,isLogout,loginTime,logoutTime,overtimeHours,onLeave,loginIP,logoutIP FROM employee_details LEFT JOIN employee_attendence ON employee_details.employeeId=employee_attendence.employeeId WHERE isActive=1 AND hasPdfAccess=1 AND loginDate='$t_searchOn'".$andClause1.$andClause2." GROUP BY employee_details.employeeId ORDER BY firstName";
	$result		=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		$k	=	0;
		while($row				=	mysqli_fetch_assoc($result))
		{
			$i++;
			$k++;
			$lastName			=	stripslashes($row['lastName']);
			$employeeId			=	$row['employeeId'];
			$firstName			=	stripslashes($row['firstName']);
			$employeeName		=	$firstName." ".$lastName;
			$employeeName		=	ucwords($employeeName);
			$isShiftTimeAdded	=	$row['isShiftTimeAdded'];
			$shiftFrom			=	$row['shiftFrom'];
			$shiftTo			=	$row['shiftTo'];
			$isLogin			=	$row['isLogin'];
			$isLogout			=	$row['isLogout'];
			$loginTime			=	$row['loginTime'];
			$logoutTime			=	$row['logoutTime'];
			$overtimeHours		=	$row['overtimeHours'];
			$onLeave			=	$row['onLeave'];
			$loginIP			=	$row['loginIP'];
			$logoutIP			=	$row['logoutIP'];
			$leaveText			=	"";

			if(!empty($isLogin) && $isLogin != "null")
				{
					if($onLeave		==	1)
					{
						$leaveText	=	"(Full Leave)";
					}
					elseif($onLeave		==	2)
					{
						$leaveText	=	"(Half Leave)";
					}

					if($isLogin == 1)
					{
						if(!empty($loginTime))
						{
							$loginTime	=	date("H:i",strtotime($loginTime));
							$loginTime	=	"Log In At - ".$loginTime." Hrs";
						}
						if(!empty($isLogout))
						{
							$logoutTime	=	date("H:i",strtotime($logoutTime));
							$logoutTime	=	"Log Out At - ".$logoutTime." Hrs";
						}
					}
					
				}
			if(!empty($overtimeHours))
			{
				$overtimeText	=	"<font color='#ff0000'>".getHours($overtimeHours)."</font>Hrs";
			}
			else
			{
				$overtimeText	=	"";
			}

			if($isShiftTimeAdded==  1)
			{
				$displaySiftFrom	=	date("H:i",strtotime($shiftFrom));
				$displaySiftTo		=	date("H:i",strtotime($shiftTo));
				$shiftText			=	 $displaySiftFrom." Hrs To ".$displaySiftTo." Hrs";
			}
			else
			{
				$shiftText			=	"Didnot Added";
			}

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$k);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$displayDate);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$employeeName);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$loginTime.$leaveText);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$logoutTime);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$shiftText);
		}
	}
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

	$filaneNamePrefixed	=	"123456789";
			
	$storeFileName	=	md5($filaneNamePrefixed)."-employee-attendence-details.xls";
	$objWriter->save($storePath.$storeFileName);
	
	 echo "<br><br><center><a href='".SITE_URL_EMPLOYEES."/download-excel.php?t=".$storeFileName."' class='linkstyle8' target='_blank'>DOWNLOAD EXCEL SHEET</a></center>";
?>