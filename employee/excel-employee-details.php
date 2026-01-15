<?php
	session_start();
	include("../root.php");
	include(SITE_ROOT_MTEMPLOYEES	.	"/includes/session-vars.php");
	include(SITE_ROOT_MTEMPLOYEES	.	"/includes/check-login.php");
	include(SITE_ROOT_MTEMPLOYEES	.   "/includes/check-manager-hr-login.php");
	include(SITE_ROOT_MTEMPLOYEES	.	"/classes/mtemployee.php");
	include(SITE_ROOT				.	"/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/common-array.php");
	$mtemployeeObj					=  new mtemployee();

	if($result								=	$mtemployeeObj->getMtEmployeeDetails($s_mtemployeeId))
	{
		$row						=	mysqli_fetch_assoc($result);
		$s_hasMtAdminAccess." - ".$havingMtManagerEmployeeExcel		=	stripslashes($row['havingMtManagerEmployeeExcel']);
	}


	if(empty($s_hasMtAdminAccess) && $havingMtManagerEmployeeExcel == "no"){
		ob_clean();
		header("Location:".SITE_URL_MTEMPLOYEES);
		exit();
	}

	/*$whereClause					=	" WHERE isActive=1 AND hasPdfAccess=0 AND underManager=$s_mtemployeeId";
	$whereClause					=	" WHERE isActive=1 AND hasPdfAccess=0";
	
	if($s_mthasManagerAccess)
	{
		$whereClause				=	" WHERE isActive=1 AND hasPdfAccess=0";
	}*/
	
	$whereClause					=	" WHERE hasPdfAccess=0 AND isActive=1";
	$employeeId						=	0;
	$text							=	"View Details Of All Employees";
	$text1							=	"all employees details";

	if(isset($_GET['allInactive']) && $_GET['allInactive'] == 1){
		$whereClause=	" WHERE hasPdfAccess=0 AND isActive=0";
		$text		=	"View Details Of All In-Active Employees";
		$text1		=	"all in-active employees details";
	}

	$andClause		=	"";

	if(isset($_GET['ID']))
	{
		$employeeId			=	$_GET['ID'];
		if(!empty($employeeId))
		{
			$employeeName	=	$mtemployeeObj->getEmployeeName($employeeId);

			$text			=	"View Details Of ".$employeeName;
			$text1			=	"details of ".$employeeName;
			$andClause		=	" AND employeeId=$employeeId";
		}
	}

	$storePath				=	SITE_ROOT_FILES."/files/excel-files/";

	require_once 	SITE_ROOT. '/excel/Classes/PHPExcel.php';
		
	/****EXCEL WRITING CODE****/
	// Create new PHPExcel object
	$i	=	0;
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(55);
	$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(55);
	$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(35);
	
	
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
	
	$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A$i:AE$i");
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,"EMPLOYEE ID");
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,"EMPLOYEE NAME");
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,"BANK NAME");
	$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,"BRANCH NAME");
	$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,"ACCOUNT HOLDER'S NAME");
	$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,"ACCOUNT NUMBER");
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,"IFSC CODE");
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,"PAN NUMBER");
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,"AADHAAR NUMBER");
	$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,"DEPARTMENT");
	$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,"IS MANAGER");
	$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('L'.$i,"PDF ACCESS");
	$objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('M'.$i,"GENDER");
	$objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('N'.$i,"DOB");
	$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$i,"FATHER'S NAME");
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('P'.$i,"EMAIL");
	$objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i,"ALT EMAIL");
	$objPHPExcel->getActiveSheet()->getStyle('Q'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('R'.$i,"PHONE");
	$objPHPExcel->getActiveSheet()->getStyle('R'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('S'.$i,"MOBILE");
	$objPHPExcel->getActiveSheet()->getStyle('S'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('T'.$i,"CITY");
	$objPHPExcel->getActiveSheet()->getStyle('T'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('U'.$i,"STATE");
	$objPHPExcel->getActiveSheet()->getStyle('U'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('V'.$i,"COUNTRY");
	$objPHPExcel->getActiveSheet()->getStyle('V'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('W'.$i,"CORRESPONDENCE ADDRESS");
	$objPHPExcel->getActiveSheet()->getStyle('W'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('X'.$i,"PERMANENT ADDRESS");
	$objPHPExcel->getActiveSheet()->getStyle('X'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('Y'.$i,"STATUS");
	$objPHPExcel->getActiveSheet()->getStyle('Y'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('Z'.$i,"HIGHEST QUALIFICATION");
	$objPHPExcel->getActiveSheet()->getStyle('Z'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('AA'.$i,"PASSING OUT ON");
	$objPHPExcel->getActiveSheet()->getStyle('AA'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('AB'.$i,"BOARD/COLLEGE/UNIVERSITY");
	$objPHPExcel->getActiveSheet()->getStyle('AB'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('AC'.$i,"REFERRED BY");
	$objPHPExcel->getActiveSheet()->getStyle('AC'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('AD'.$i,"REGISTERED ON");
	$objPHPExcel->getActiveSheet()->getStyle('AD'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('AE'.$i,"DEACTIVATED ON");
	$objPHPExcel->getActiveSheet()->getStyle('AE'.$i)->getFont()->setBold(true);


	$query			=	"SELECT * FROM employee_details".$whereClause.$andClause." ORDER BY firstName";
	$result			=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		$k			=	0;
		while($row	=	mysqli_fetch_assoc($result))
		{
			$i++;
			$k++;
			$employeeId				=	$row['employeeId'];
			$firstName				=	$row['firstName'];
			$lastName				=	$row['lastName'];
			$address				=	$row['address'];
			$email					=	$row['email'];
			$mobile					=	$row['mobile'];
			$fatherName				=	$row['fatherName'];
			$gender					=	$row['gender'];
			$dob					=	showDate($row['dob']);
			$city					=	$row['city'];
			$state					=	$row['state'];
			$country				=	$row['country'];
			$perAddress				=	$row['perAddress'];
			$moneyPerLine			=	$row['moneyPerLine'];
			$bankName				=	$row['bankName'];
			$branchName				=	$row['branchName'];
			$accountName			=	$row['accountName'];
			$accountNumber			=	$row['accountNumber'];
			$bankIFSCcode			=	$row['bankIFSCcode'];
			$panCardNumber			=	$row['panCardNumber'];
			$highestQualification	=	$row['highestQualification'];
			$otherQualification		=	stripslashes($row['otherQualification']);
			$qualificationStatus	=	$row['qualificationStatus'];
			$boardUniversity		=	stripslashes($row['boardUniversity']);
			$passedOutOn			=	$row['passedOutOn'];
			$referredBy				=	stripslashes($row['referredBy']);
			$addedOn				=	showDate($row['addedOn']);
			$deactivatedDate		=	$row['deactivatedDate'];
			$isActive				=	$row['isActive'];
			$statusText				=	"In-Active";
			$aadhaarNumber 			=	stripslashes($row['aadhaarNumber']);
			if(empty($aadhaarNumber)){
				$aadhaarNumber		=	"N/A";
			}

			if(!empty($referredBy)){
				$referredByText		=	$referredBy;
			}
			else{
				$referredByText		=  "None";
			}

			if($highestQualification == 6 && !empty($otherQualification))
			{
				$highestQualificationText	=	$otherQualification;
			}
			elseif(!empty($highestQualification))
			{
				$highestQualificationText	=	$a_employeeHighestQualifications[$highestQualification];
			}
			else
			{
				$highestQualificationText	=	"";
			}
			$qualificationStatusText		=	"";
			if(!empty($qualificationStatus))
			{
				$qualificationStatusText	=	$a_employeeQualificationsStatus[$qualificationStatus];
			}
			if(empty($passedOutOn))
			{
				$passedOutOn				=	"";
			}

			if(!empty($deactivatedDate) && $deactivatedDate != "0000-00-00"){
				$deactivatedDateText=	showDate($deactivatedDate);
			}
			else{
				$deactivatedDateText=  "N/A";
			}
			if($isActive			==	1)
			{
				$deactivatedDateText=   "-";
				$statusText			=	"Active";
			}

	
			$departmentId	=	$mtemployeeObj->getSingleQueryResultMt("SELECT departmentId FROM employee_shift_rates WHERE employeeId=$employeeId","departmentId");
			$countryText	=	$a_countries[$country];
			$departMentText	=	"";
			if(!empty($departmentId))
			{
				$departMentText	=	$a_department[$departmentId];
			}
			$mangerText		=	"NO";
			$pdfEmployeeText=	"NO";

			$isManger		=	$mtemployeeObj->isEmployeeManager($employeeId);

			if(!empty($isManger))
			{
				$mangerText		=	"YES";
			}
			$isPdf				=	$mtemployeeObj->getSingleQueryResultMt("SELECT hasPdfAccess FROM employee_details WHERE employeeId=$employeeId","hasPdfAccess");
			
			if(!empty($isPdf))
			{
				$pdfEmployeeText=	"YES";
			}

			$employeeName	=	$firstName." ".$lastName;
			$employeeName	=	ucwords($employeeName);

			$genderText		=	"Male";
			if($gender	==	"F")
			{
				$genderText	=	"Female";
			}



			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$employeeId);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$employeeName);
			
			
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$bankName);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$branchName);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$accountName);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$accountNumber);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$bankIFSCcode);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$panCardNumber);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$aadhaarNumber);

			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$departMentText);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,$mangerText);
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i,$pdfEmployeeText);
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$i,$genderText);
			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i,$dob);
			$objPHPExcel->getActiveSheet()->setCellValue('O'.$i,$fatherName);
			$objPHPExcel->getActiveSheet()->setCellValue('P'.$i,$email);
			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i,"N/A");
			$objPHPExcel->getActiveSheet()->setCellValue('R'.$i,"N/A");
			$objPHPExcel->getActiveSheet()->setCellValue('S'.$i,$mobile);
			$objPHPExcel->getActiveSheet()->setCellValue('T'.$i,$city);
			$objPHPExcel->getActiveSheet()->setCellValue('U'.$i,$state);
			$objPHPExcel->getActiveSheet()->setCellValue('V'.$i,$countryText);
			$objPHPExcel->getActiveSheet()->setCellValue('W'.$i,$address);
			$objPHPExcel->getActiveSheet()->setCellValue('X'.$i,$perAddress);
			$objPHPExcel->getActiveSheet()->setCellValue('Y'.$i,$statusText);
			$objPHPExcel->getActiveSheet()->setCellValue('Z'.$i,$highestQualificationText);		
			$objPHPExcel->getActiveSheet()->setCellValue('AA'.$i,$passedOutOn);
			$objPHPExcel->getActiveSheet()->setCellValue('AB'.$i,$boardUniversity);
			$objPHPExcel->getActiveSheet()->setCellValue('AC'.$i,$referredByText);
			$objPHPExcel->getActiveSheet()->setCellValue('AD'.$i,$addedOn);
			$objPHPExcel->getActiveSheet()->setCellValue('AE'.$i,$deactivatedDateText);
			
		}
	}
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

	$filaneNamePrefixed	=	"123456789";
			
	$storeFileName	=	md5($filaneNamePrefixed)."-employee-details.xls";
	$objWriter->save($storePath.$storeFileName);
	
	 echo "<br><br><center><a href='".SITE_URL_MTEMPLOYEES."/download-excel.php?t=".$storeFileName."' class='linkstyle8' target='_blank'>DOWNLOAD EXCEL SHEET</a></center>";
?>