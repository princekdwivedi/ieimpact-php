<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	ini_set('display_errors', 1);
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");
	$employeeObj				=	new employee();
	$validator					=	new validate();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess && !$s_hasAdminAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$showHideBrowseOption	=	true;

	//$checked_employees	=	array(5,449,137,637,8,587,340,10,508,1038,7,412,675);


	/*if(!in_array($s_employeeId,$checked_employees))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}*/

	$downloadingID				=	0;
	$employeeId					=   0;
	$dob						=	"";
	$check						=	"checked";
	$check1						=	"";
	$t_email					=	"";
	
	$shiftFrom					=	"";
	$shiftTo					=	"";
	$shiftFromHrs				=	"";
	$shiftFromMinitue			=	"";
	$sfiftToHrs					=	"";
	$shiftToMinitue				=	"";
	$dictaEscrId				=	"";
	$totalInvesmentAvailable	=	0;
	$highestQualification		=	"";
	$otherQualification			=	"";
	$qualificationStatus		=	0;
	$boardUniversity			=	"";
	$passedOutOn				=	0;
	$showHideExtraQualifications=	"none";


	$filePath			=	SITE_ROOT_FILES."/files/member-identity/";
	function findexts($filename) 
	{ 
		$ext        =    "";
		$filename   =    strtolower($filename) ; 
		$a_exts		=	 explode(".",$filename);
		$total		=	 count($a_exts);
		if($total > 1){
			$ext	=	 end($a_exts);		
		}		
		return $ext; 
	} 
	function getFileName($fileName)
	{
		$fileExtPos		=  strrpos($fileName, '.');
		$fileName		=  substr($fileName,0,$fileExtPos);

		return $fileName;
	}


	if(isset($_GET['ID']))
	{
		$employeeId		=   (int)$_GET['ID'];
		$downloadingID	=	$employeeId;
		$query			=	"SELECT * FROM employee_details WHERE employeeId=$employeeId AND isActive=1";
		$result			=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$row					=	mysqli_fetch_assoc($result);
			$lastName				=	stripslashes($row['lastName']);
			$firstName				=	stripslashes($row['firstName']);
			$isActive				=	$row['isActive'];
			$fatherName				=	stripslashes($row['fatherName']);
			$email					=	stripslashes($row['email']);
			$t_email				=	$email;
			$gender					=	$row['gender'];
			$mobile					=	stripslashes($row['mobile']);
			$t_dob					=	$row['dob'];
			$city					=	stripslashes($row['city']);
			$state					=	stripslashes($row['state']);
			$country				=	stripslashes($row['country']);
			$address				=	stripslashes($row['address']);
			$perAddress				=	stripslashes($row['perAddress']);
			$moneyPerLine			=	stripslashes($row['moneyPerLine']);
			$bankName				=	stripslashes($row['bankName']);
			$branchName				=	stripslashes($row['branchName']);
			$accountName			=	stripslashes($row['accountName']);
			$accountNumber			=	stripslashes($row['accountNumber']);
			$bankIFSCcode			=	stripslashes($row['bankIFSCcode']);
			$addedOn				=	showDate($row['addedOn']);

			$hasIdentityProof		=	$row['hasIdentityProof'];
			$hasPanCardProof		=	$row['hasPanCardProof'];
			$hasComplianceForm		=	$row['hasComplianceForm'];
			$hasResume				=	$row['hasResume'];
			$hasResidenceProof		=	$row['hasResidenceProof'];
			$hasAgreement			=	$row['hasAgreement'];
			$hasAppointmentLetter	=	$row['hasAppointmentLetter'];
			$hasEmployeeAgreement	=	$row['hasEmployeeAgreement'];

			$panCardNumber			=	stripslashes($row['panCardNumber']);
			$isShiftTimeAdded		=	$row['isShiftTimeAdded'];
			$shiftFrom				=	$row['shiftFrom'];
			$shiftTo				=	$row['shiftTo'];
			$dictaEscrId			=	$row['dictaEscrId'];

			$highestQualification	=	$row['highestQualification'];
			$otherQualification		=	stripslashes($row['otherQualification']);
			$qualificationStatus	=	$row['qualificationStatus'];
			$boardUniversity		=	stripslashes($row['boardUniversity']);
			$passedOutOn			=	$row['passedOutOn'];
			$aadhaarNumber			=	stripslashes($row['aadhaarNumber']);

			$hasCancelledCheque  	=	$row['hasCancelledCheque'];
		
			$hasFormEleven      	=	$row['hasFormEleven'];
			

			$hasResignedFile       	=	$row['hasResignedFile'];
			

			$hasFormElevenRevised  	=	$row['hasFormElevenRevised'];
			
			if($isShiftTimeAdded	==	1)
			{
				list($shiftFromHrs,$shiftFromMinitue,$fh)	=	explode(":",$shiftFrom);
				list($sfiftToHrs,$shiftToMinitue,$th)		=	explode(":",$shiftTo);
			}

			if($highestQualification			==	6)
			{
				$showHideExtraQualifications	=	"";
			}


			if($gender	==	"F")
			{
				$check				=	"";
				$check1				=	"checked";
			}
			$employeeName	=	$firstName." ".$lastName;
			$employeeName	=	ucwords($employeeName);
			if($t_dob	!=	"0000-00-00")
			{
				list($year,$month,$day)		=	explode("-",$t_dob);
				$dob		=	$day."-".$month."-".$year;
			}	

			$a_investmentFiles	 =	array();
			$showAddingInvestment=	true;
			$query				 =	"SELECT * FROM employee_investment_files WHERE employeeId=$employeeId";
			$result				=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row					=	mysqli_fetch_assoc($result))
				{
					$totalInvesmentAvailable++;
					$investmentId						=	$row['investmentId'];
					$investmentOn						=	stripslashes($row['investmentOn']);
					$fileExt							=	$row['fileExt'];
					$a_investmentFiles[$investmentId]	=	$investmentOn."<=>".$fileExt;
				}
				$showAddingInvestment					=	false;
			}
			else
			{
				$totalInvesmentAvailable		=	1;
			}
			$a_employeeFiles =	$employeeObj->getEmployeeProfileFiles($employeeId);

		}
		else
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$redirectUrl	=	"view-an-employee.php?ID=$employeeId&";

	if(isset($_GET['investmentID']) && $_GET['investmentID'] !=0  && isset($_GET['isDeleteInvestment']) && $_GET['isDeleteInvestment'] == 1)
	{
		$investmentId			=	$_GET['investmentID'];
			
		$query					=	"SELECT * FROM employee_investment_files WHERE investmentId=$investmentId AND employeeId=$employeeId";
		$result					=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$row				=	mysqli_fetch_assoc($result);
		
			$fileName			=	$row['fileName'];
			$ext				=	$row['fileExt'];

			if(file_exists($filePath."INVESMENT".$investmentId."_".$employeeId."_".$fileName.".".$ext))
			{
				unlink($filePath."INVESMENT".$investmentId."_".$employeeId."_".$fileName.".".$ext);

				dbQuery("DELETE FROM employee_investment_files WHERE investmentId=$investmentId AND employeeId=$employeeId");
			}
		}
		
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES ."/view-an-employee.php?ID=$employeeId#investment");
		exit();
	}
	$form		=	SITE_ROOT_EMPLOYEES  . "/forms/edit-employee.php";
	
?>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td colspan="2" class='title'>Edit <?php echo $employeeName;?> Details</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
</table>
<?php
if(isset($_GET['success']))
{
?>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td class='title' align="center">
			SUCCESSFULLY EDITED DETAILS OF <?php echo $employeeName;?>
		</td>
	</tr>
	<tr>
		<td align="center">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-employee-details.php" class='link_style3'>BACK TO EMPLOYEE.</a>
		</td>
	</tr>
	<tr>
		<td height="200"></td>
	</tr>
</table>
<?php
}
elseif(isset($_REQUEST['formSubmitted']))
{
	extract($_REQUEST);

	$firstName			=	trim($firstName);
	$lastName			=	trim($lastName);
	$fatherName			=	trim($fatherName);
	$email				=	trim($email);

	$mobile				=	trim($mobile);
	$city				=	trim($city);
	$state				=	trim($state);
	$address			=	trim($address);
	$perAddress			=	trim($perAddress);
	$bankName			=	trim($bankName);
	$branchName			=	trim($branchName);
	$accountName		=	trim($accountName);
	$accountNumber		=	trim($accountNumber);
	$bankIFSCcode		=	trim($bankIFSCcode);
	$panCardNumber		=	trim($panCardNumber);
	$boardUniversity	=	trim($boardUniversity);
	$otherQualification	=	trim($otherQualification);
	$aadhaarNumber	    =	trim($aadhaarNumber);


	$panCardNumber		=	strtoupper($panCardNumber);

	
	$boardUniversity	=	makeDBSafe($boardUniversity);
	$otherQualification	=	makeDBSafe($otherQualification);
	$aadhaarNumber		=	makeDBSafe($aadhaarNumber);


	$firstName			=	makeDBSafe($firstName);
	$lastName			=	makeDBSafe($lastName);
	$fatherName			=	makeDBSafe($fatherName);
	$email				=	makeDBSafe($email);
	
	$mobile				=	makeDBSafe($mobile);
	$city				=	makeDBSafe($city);
	$state				=	makeDBSafe($state);
	$address			=	makeDBSafe($address);
	$perAddress			=	makeDBSafe($perAddress);

	$bankName			=	makeDBSafe($bankName);
	$branchName			=	makeDBSafe($branchName);
	$accountName		=	makeDBSafe($accountName);
	$accountNumber		=	makeDBSafe($accountNumber);
	$bankIFSCcode		=	makeDBSafe($bankIFSCcode);
	$panCardNumber		=	makeDBSafe($panCardNumber);
	$boardUniversity	=	makeDBSafe($boardUniversity);
	$otherQualification	=	makeDBSafe($otherQualification);

	
	$validator->checkField($firstName,"","Please enter your first name.");
	$validator->checkField($dob,"","Please enter date of birth.");
	$validator->checkField($fatherName,"","Please enter your father's name.");
	$validator->checkField($email,"","Please enter your email.");
	if(!empty($email))
	{
		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$validator->setError("Your email is invalid.");
		}
		else
		{
			/*if(!checkingMailgunValidEmail($email))
			{
				$validator->setError("The email address is invalid. Please check.");
			}
			else
			{*/
				if($result=	$employeeObj->getEmployeeExistingEmail($email,$employeeId))
				{
					$validator->setError("This email is already in use.");
				}
			//}
		}
	}
	
	$validator->checkField($mobile,"","Please enter mobile no.");	
	if(!empty($mobile) && !is_numeric($mobile))
	{
		$validator->setError("Please donot use character in mobile number .");
	}
	
	$validator->checkField($bankName,"","Please enter bank name where your account is !!");
	$validator->checkField($branchName,"","Please enter your bank branch name.");
	$validator->checkField($accountName,"","Please enter name in your account.");
	$validator->checkField($accountNumber,"","Please enter your account number.");
	if(!empty($accountNumber) && !is_numeric($accountNumber))
	{
		$validator->setError("Please donot use character in account number.");
	}
	elseif(strlen($accountNumber) < 7){
		$validator->setError("Your account number is too short.");
	}
	$validator->checkField($bankIFSCcode,"","Please enter your bank IFSC code.");
	$validator->checkField($panCardNumber,"","Please enter your PAN number.");
	if(!empty($panCardNumber)){
		$validatePan	=	validatePan($panCardNumber);
		if(empty($validatePan)){
			$validator->setError("Please enter a valid PAN number.");
		}
		else{		
			$isExistPanFor	=	$employeeObj->getSingleQueryResult("SELECT fullName FROM employee_details WHERE panCardNumber='$panCardNumber' AND employeeId <> $employeeId AND isActive=1 ORDER BY employeeId DESC","fullName");
			if(!empty($isExistPanFor)){
				$validator->setError("This PAN number is already added by employee - ".stripslashes($isExistPanFor).".");
			}
		}
	}
	
	if(!empty($aadhaarNumber)){
		$validateAadhaar	 =	isAadharValid($aadhaarNumber);
		
		if($validateAadhaar !=  1){
			$validator->setError("Please enter a valid Aadhaar number.");
		}
		else{
			if(!is_numeric($aadhaarNumber)){
				$validator->setError("Please enter a valid aadhaar number with only digits.");
			}
			elseif(strlen($aadhaarNumber) < 10){
				$validator->setError("Please enter a valid aadhaar number.");
			}
			else{		
				$isExistAadhaarFor	=	$employeeObj->getSingleQueryResult("SELECT fullName FROM employee_details WHERE aadhaarNumber=$aadhaarNumber AND employeeId <> $employeeId AND isActive=1 ORDER BY employeeId DESC LIMIT 1","fullName");
				if(!empty($isExistAadhaarFor)){
					$validator->setError("This aadhaar number is already added by employee - ".stripslashes($isExistAadhaarFor).".");
				}
			}
		}
	}
	else{
		$aadhaarNumber	= 0;
	}

	$validator->checkField($city,"","Please enter city.");
	$validator->checkField($state,"","Please enter state/province.");
	$validator->checkField($country,"","Please select country.");
	$validator->checkField($address,"","Please enter correspondence  address.");
	$validator->checkField($perAddress,"","Please enter Please enter permanent address.");
	
	if(!empty($_FILES['identityProof']['name']))
	{
		$uploadingFile	    =   $_FILES['identityProof']['name'];
		$ext			    =	findexts($uploadingFile);
		if($ext != "jpg" && $ext != "jpeg" && $ext != "gif" && $ext != "png" && $ext != "bmp" && $ext != "pdf")
		{
			$validator->setError("Please upload scan copy of identity Proof in .jpg,.gif,.png,.pdf and .bmp format only.");
		}
	}
	if(!empty($_FILES['panCard']['name']))
	{
		$uploadingFile	    =   $_FILES['panCard']['name'];
		$ext			    =	findexts($uploadingFile);
		if($ext != "jpg" && $ext != "jpeg" && $ext != "gif" && $ext != "png" && $ext != "bmp" && $ext != "pdf")
		{
			$validator->setError("Please upload scan copy of pan card in .jpg,.gif,.png,.pdf and .bmp format only.");
		}
	}
	if(!empty($_FILES['complianceForm']['name']))
	{
		$uploadingFile	    =   $_FILES['complianceForm']['name'];
		$ext			    =	findexts($uploadingFile);
		if($ext != "jpg" && $ext != "jpeg" && $ext != "gif" && $ext != "png" && $ext != "bmp" && $ext != "pdf")
		{
			$validator->setError("Please upload scan copy of HIPPA compliance form in .jpg,.gif,.png,.pdf and .bmp format only.");
		}
	}
	if(!empty($_FILES['resume']['name']))
	{
		$uploadingFile	    =   $_FILES['resume']['name'];
		$ext			    =	findexts($uploadingFile);
		if($ext != "doc" && $ext != "docx" && $ext != "pdf")
		{
			$validator->setError("Please upload your resume in .doc,.docx,.pdf format only.");
		}
	}

	if(!empty($_FILES['residenceProof']['name']))
	{
		$uploadingFile	    =   $_FILES['residenceProof']['name'];
		$ext			    =	findexts($uploadingFile);
		if($ext != "jpg" && $ext != "jpeg" && $ext != "gif" && $ext != "png" && $ext != "bmp" && $ext != "pdf")
		{
			$validator->setError("Please upload scan copy of residence proof in .jpg,.gif,.png,.pdf and .bmp format only.");
		}
	}
	if(!empty($_FILES['ieimpactAgreement']['name']))
	{
		$uploadingFile	    =   $_FILES['ieimpactAgreement']['name'];
		$ext			    =	findexts($uploadingFile);
		if($ext != "jpg" && $ext != "jpeg" && $ext != "gif" && $ext != "png" && $ext != "bmp" && $ext != "doc" && $ext != "docx" && $ext != "xls" && $ext != "pdf")
		{
			$validator->setError("Please upload nondisclosure agreement in a valid format like image, document or pdf.");
		}
	}
	if(!empty($_FILES['ieimpactAppoinmentLetter']['name']))
	{
		$uploadingFile	    =   $_FILES['ieimpactAppoinmentLetter']['name'];
		$ext			    =	findexts($uploadingFile);
		if($ext != "jpg" && $ext != "jpeg" && $ext != "gif" && $ext != "png" && $ext != "bmp" && $ext != "doc" && $ext != "docx" && $ext != "xls" && $ext != "pdf")
		{
			$validator->setError("Please upload ieImpact signed appointment letter in a valid format like image, document or pdf.");
		}
	}
	if(!empty($_FILES['ieimpactEmployeeAgreement']['name']))
	{
		$uploadingFile	    =   $_FILES['ieimpactEmployeeAgreement']['name'];
		$ext			    =	findexts($uploadingFile);
		if($ext != "jpg" && $ext != "jpeg" && $ext != "gif" && $ext != "png" && $ext != "bmp" && $ext != "doc" && $ext != "docx" && $ext != "xls" && $ext != "pdf")
		{
			$validator->setError("Please upload ieImpact employee agreement in a valid format like image, document or pdf.");
		}
	}

	$validator->checkField($highestQualification,"","Please select highest qualification.");
	if(!empty($highestQualification) && $highestQualification == 6 && empty($otherQualification))
	{
		$validator->setError("Please enter other qualification.");
	}
	$validator->checkField($qualificationStatus,"","Please select qualification status.");
	if($qualificationStatus	==	1 || $qualificationStatus == 3)
	{
		if(empty($passedOutOn))
		{
			$validator->setError("Please select passed out.");
		}
	}
	$validator->checkField($boardUniversity,"","Please enter board/college/university.");
	$dataValid	 =	$validator ->isDataValid();
	if($dataValid)
	{
		list($day,$month,$year)		=	explode("-",$dob);
		$t_dob		=	$year."-".$month."-".$day;

		if(empty($lastName))
		{
			$lastName		=	"";
		}
		else
		{
			$lastName		=	" ".$lastName;
		}
		$fullName	=	$firstName.$lastName;

		$query		=	"UPDATE employee_details SET firstName='$firstName',lastName='$lastName',fullName='$fullName',fatherName='$fatherName',email='$email',gender='$gender',mobile=$mobile,dob='$t_dob',city='$city',state='$state',country='$country',address='$address',perAddress='$perAddress',bankName='$bankName',branchName='$branchName',accountName='$accountName',accountNumber='$accountNumber',bankIFSCcode='$bankIFSCcode',panCardNumber='$panCardNumber',aadhaarNumber=$aadhaarNumber,highestQualification=$highestQualification,otherQualification='$otherQualification',qualificationStatus=$qualificationStatus,boardUniversity='$boardUniversity',passedOutOn=$passedOutOn WHERE employeeId=$employeeId AND isActive=1";
		dbQuery($query);

	
		if(!empty($_FILES['identityProof']['name']))
		{
			$type 	=	1;
			$addNew =   true;
			if(!empty($hasIdentityProof) && !empty($a_employeeFiles) && array_key_exists($type, $a_employeeFiles))
			{
				
				$fileServerPath 	=	$a_employeeFiles[$type];
				if(file_exists($fileServerPath))
				{
					unlink($fileServerPath);
					$addNew         =   false;
				}
			}
			$uploadingFile	    =   $_FILES['identityProof']['name'];
			$mimeType		    =   $_FILES['identityProof']['type'];
			$fileSize		    =   $_FILES['identityProof']['size'];
			$tempName		    =	$_FILES['identityProof']['tmp_name'];
			$ext			    =	findexts($uploadingFile);
			$uploadingFileName	=	getFileName($uploadingFile);

			$fileName		    =   "ID_".$employeeId."_".$uploadingFileName.".".$ext;

			move_uploaded_file($tempName,$filePath.$fileName);

			$uploadingFileName	=	makeDBSafe($uploadingFileName);
			$fileTypeName 		=   $a_employeesFilesUpoadingTypes[$type];//Identity Proof File
			$fileServerPath     =   $filePath.$fileName;

			dbQuery("UPDATE employee_details SET hasIdentityProof=1 WHERE employeeId=$employeeId");


			if($addNew == true){
				dbQuery("INSERT INTO employeee_profile_files SET employeeId=$employeeId,type=$type,fileTypeName='$fileTypeName',fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
			}
			else{
				dbQuery("UPDATE employeee_profile_files SET fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."' WHERE employeeId=$employeeId AND type=$type");
			}
		}
		if(!empty($_FILES['panCard']['name']))
		{
			
			$type 	=	2;
			$addNew =   true;
			if(!empty($hasPanCardProof) && !empty($a_employeeFiles) && array_key_exists($type, $a_employeeFiles))
			{
				
				$fileServerPath 	=	$a_employeeFiles[$type];
				if(file_exists($fileServerPath))
				{
					unlink($fileServerPath);
					$addNew         =   false;
				}
			}
			$uploadingFile	    =   $_FILES['panCard']['name'];
			$mimeType		    =   $_FILES['panCard']['type'];
			$fileSize		    =   $_FILES['panCard']['size'];
			$tempName		    =	$_FILES['panCard']['tmp_name'];
			$ext			    =	findexts($uploadingFile);
			$uploadingFileName	=	getFileName($uploadingFile);

			$fileName		= "PC_".$employeeId."_".$uploadingFileName.".".$ext;

			move_uploaded_file($tempName,$filePath.$fileName);

			$uploadingFileName	=	makeDBSafe($uploadingFileName);
			$fileTypeName 		=   $a_employeesFilesUpoadingTypes[2];//Pan CardFile File
			$fileServerPath     =   $filePath.$fileName;

			dbQuery("UPDATE employee_details SET hasPanCardProof=1 WHERE employeeId=$employeeId");

			if($addNew == true){
				dbQuery("INSERT INTO employeee_profile_files SET employeeId=$employeeId,type=$type,fileTypeName='$fileTypeName',fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
			}
			else{
				dbQuery("UPDATE employeee_profile_files SET fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."' WHERE employeeId=$employeeId AND type=$type");
			}
		}
		if(!empty($_FILES['complianceForm']['name']))
		{
			$type 	=	3;
			$addNew =   true;
			if(!empty($hasComplianceForm) && !empty($a_employeeFiles) && array_key_exists($type, $a_employeeFiles))
			{
				
				$fileServerPath 	=	$a_employeeFiles[$type];
				if(file_exists($fileServerPath))
				{
					unlink($fileServerPath);
					$addNew         =   false;
				}
			}
			$uploadingFile	    =   $_FILES['complianceForm']['name'];
			$mimeType		    =   $_FILES['complianceForm']['type'];
			$fileSize		    =   $_FILES['complianceForm']['size'];
			$tempName		    =	$_FILES['complianceForm']['tmp_name'];
			$ext			    =	findexts($uploadingFile);
			$uploadingFileName	=	getFileName($uploadingFile);

			$fileName		= "CF_".$employeeId."_".$uploadingFileName.".".$ext;

			move_uploaded_file($tempName,$filePath.$fileName);

			$uploadingFileName	=	makeDBSafe($uploadingFileName);
			$fileTypeName 		=   $a_employeesFilesUpoadingTypes[$type];//Compliance Form File
			$fileServerPath     =   $filePath.$fileName;

			dbQuery("UPDATE employee_details SET hasComplianceForm=1 WHERE employeeId=$employeeId");

			if($addNew == true){
				dbQuery("INSERT INTO employeee_profile_files SET employeeId=$employeeId,type=$type,fileTypeName='$fileTypeName',fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
			}
			else{
				dbQuery("UPDATE employeee_profile_files SET fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."' WHERE employeeId=$employeeId AND type=$type");
			}

		}
		if(!empty($_FILES['resume']['name']))
		{
			$type 	=	4;
			$addNew =   true;
			if(!empty($hasResume) && !empty($a_employeeFiles) && array_key_exists($type, $a_employeeFiles))
			{
				
				$fileServerPath 	=	$a_employeeFiles[$type];
				if(file_exists($fileServerPath))
				{
					unlink($fileServerPath);
					$addNew         =   false;
				}
			}

			$uploadingFile	    =   $_FILES['resume']['name'];
			$mimeType		    =   $_FILES['resume']['type'];
			$fileSize		    =   $_FILES['resume']['size'];
			$tempName		    =	$_FILES['resume']['tmp_name'];
			$ext			    =	findexts($uploadingFile);
			$uploadingFileName	=	getFileName($uploadingFile);

			$fileName			= "RS_".$employeeId."_".$uploadingFileName.".".$ext;

			move_uploaded_file($tempName,$filePath.$fileName);

			$uploadingFileName	=	makeDBSafe($uploadingFileName);
			$fileTypeName 		=   $a_employeesFilesUpoadingTypes[$type];//Resume File
			$fileServerPath     =   $filePath.$fileName;


			dbQuery("UPDATE employee_details SET hasResume=1 WHERE employeeId=$employeeId");

			if($addNew == true){
				dbQuery("INSERT INTO employeee_profile_files SET employeeId=$employeeId,type=$type,fileTypeName='$fileTypeName',fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
			}
			else{
				dbQuery("UPDATE employeee_profile_files SET fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."' WHERE employeeId=$employeeId AND type=$type");
			}	

		}

		if(!empty($_FILES['residenceProof']['name']))
		{
			$type 	=	6;
			$addNew =   true;
			if(!empty($hasResidenceProof) && !empty($a_employeeFiles) && array_key_exists($type, $a_employeeFiles))
			{
				
				$fileServerPath 	=	$a_employeeFiles[$type];
				if(file_exists($fileServerPath))
				{
					unlink($fileServerPath);
					$addNew         =   false;
				}
			}
			

			$uploadingFile	    =   $_FILES['residenceProof']['name'];
			$mimeType		    =   $_FILES['residenceProof']['type'];
			$fileSize		    =   $_FILES['residenceProof']['size'];
			$tempName		    =	$_FILES['residenceProof']['tmp_name'];
			$ext			    =	findexts($uploadingFile);
			$uploadingFileName	=	getFileName($uploadingFile);

			$fileName			=   "RP_".$employeeId."_".$uploadingFileName.".".$ext;

			move_uploaded_file($tempName,$filePath.$fileName);

			$uploadingFileName	=	makeDBSafe($uploadingFileName);
			$fileTypeName 		=   $a_employeesFilesUpoadingTypes[$type];//Residence Proof File
			$fileServerPath     =   $filePath.$fileName;


			dbQuery("UPDATE employee_details SET hasResidenceProof=1 WHERE employeeId=$employeeId");

			if($addNew == true){
				dbQuery("INSERT INTO employeee_profile_files SET employeeId=$employeeId,type=$type,fileTypeName='$fileTypeName',fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
			}
			else{
				dbQuery("UPDATE employeee_profile_files SET fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."' WHERE employeeId=$employeeId AND type=$type");
			}	
		}

		if(!empty($_FILES['ieimpactAgreement']['name']))
		{
			$type 	=	7;
			$addNew =   true;
			if(!empty($hasAgreement) && !empty($a_employeeFiles) && array_key_exists($type, $a_employeeFiles))
			{
				
				$fileServerPath 	=	$a_employeeFiles[$type];
				if(file_exists($fileServerPath))
				{
					unlink($fileServerPath);
					$addNew         =   false;
				}
			}
			
			
			$uploadingFile	    =   $_FILES['ieimpactAgreement']['name'];
			$mimeType		    =   $_FILES['ieimpactAgreement']['type'];
			$fileSize		    =   $_FILES['ieimpactAgreement']['size'];
			$tempName		    =	$_FILES['ieimpactAgreement']['tmp_name'];
			$ext			    =	findexts($uploadingFile);
			$uploadingFileName	=	getFileName($uploadingFile);

			$fileName			=   "IA_".$employeeId."_".$uploadingFileName.".".$ext;

			move_uploaded_file($tempName,$filePath.$fileName);

			$uploadingFileName	=	makeDBSafe($uploadingFileName);
			$fileTypeName 		=   $a_employeesFilesUpoadingTypes[$type];//Agreement File
			$fileServerPath     =   $filePath.$fileName;


			dbQuery("UPDATE employee_details SET hasAgreement=1 WHERE employeeId=$employeeId");

			if($addNew == true){
				dbQuery("INSERT INTO employeee_profile_files SET employeeId=$employeeId,type=$type,fileTypeName='$fileTypeName',fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
			}
			else{
				dbQuery("UPDATE employeee_profile_files SET fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."' WHERE employeeId=$employeeId AND type=$type");
			}	
		}

		if(!empty($_FILES['ieimpactAppoinmentLetter']['name']))
		{
			$type 	=	8;
			$addNew =   true;
			if(!empty($hasAppointmentLetter) && !empty($a_employeeFiles) && array_key_exists($type, $a_employeeFiles))
			{
				
				$fileServerPath 	=	$a_employeeFiles[$type];
				if(file_exists($fileServerPath))
				{
					unlink($fileServerPath);
					$addNew         =   false;
				}
			}

			$uploadingFile	    =   $_FILES['ieimpactAppoinmentLetter']['name'];
			$mimeType		    =   $_FILES['ieimpactAppoinmentLetter']['type'];
			$fileSize		    =   $_FILES['ieimpactAppoinmentLetter']['size'];
			$tempName		    =	$_FILES['ieimpactAppoinmentLetter']['tmp_name'];
			$ext			    =	findexts($uploadingFile);
			$uploadingFileName	=	getFileName($uploadingFile);

			$fileName			=   "IAL_".$employeeId."_".$uploadingFileName.".".$ext;

			move_uploaded_file($tempName,$filePath.$fileName);

			$uploadingFileName	=	makeDBSafe($uploadingFileName);
			$fileTypeName 		=   $a_employeesFilesUpoadingTypes[$type];//Appointment File
			$fileServerPath     =   $filePath.$fileName;


			dbQuery("UPDATE employee_details SET hasAppointmentLetter=1 WHERE employeeId=$employeeId");

			if($addNew == true){
				dbQuery("INSERT INTO employeee_profile_files SET employeeId=$employeeId,type=$type,fileTypeName='$fileTypeName',fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
			}
			else{
				dbQuery("UPDATE employeee_profile_files SET fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."' WHERE employeeId=$employeeId AND type=$type");
			}	

		}

		if(!empty($_FILES['ieimpactEmployeeAgreement']['name']))
		{
			$type 	=	9;
			$addNew =   true;
			if(!empty($hasEmployeeAgreement) && !empty($a_employeeFiles) && array_key_exists($type, $a_employeeFiles))
			{
				
				$fileServerPath 	=	$a_employeeFiles[$type];
				if(file_exists($fileServerPath))
				{
					unlink($fileServerPath);
					$addNew         =   false;
				}
			}
			$uploadingFile	    =   $_FILES['ieimpactEmployeeAgreement']['name'];
			$mimeType		    =   $_FILES['ieimpactEmployeeAgreement']['type'];
			$fileSize		    =   $_FILES['ieimpactEmployeeAgreement']['size'];
			$tempName		    =	$_FILES['ieimpactEmployeeAgreement']['tmp_name'];
			$ext			    =	findexts($uploadingFile);
			$uploadingFileName	=	getFileName($uploadingFile);

			$fileName			=   "IEA_".$employeeId."_".$uploadingFileName.".".$ext;

			move_uploaded_file($tempName,$filePath.$fileName);

			$uploadingFileName	=	makeDBSafe($uploadingFileName);
			$fileTypeName 		=   $a_employeesFilesUpoadingTypes[$type];//Employee Agreement File
			$fileServerPath     =   $filePath.$fileName;


			dbQuery("UPDATE employee_details SET hasEmployeeAgreement=1 WHERE employeeId=$employeeId");

			if($addNew == true){
				dbQuery("INSERT INTO employeee_profile_files SET employeeId=$employeeId,type=$type,fileTypeName='$fileTypeName',fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
			}
			else{
				dbQuery("UPDATE employeee_profile_files SET fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."' WHERE employeeId=$employeeId AND type=$type");
			}
		}

		if(!empty($_FILES['investmentFile']['name']))
		{
			
			$a_investmentOn		=	$_POST['investmentOn'];
			$a_photoNames		=	$_FILES['investmentFile']['name'];
			$a_photoSizes		=	$_FILES['investmentFile']['size'];
			$a_photoTypes		=	$_FILES['investmentFile']['type'];
			$a_tempNames		=	$_FILES['investmentFile']['tmp_name'];

			foreach($a_photoNames as $key => $photoName)
			{
				if(!empty($photoName))
				{
					$investmentOn		=	$a_investmentOn[$key];
					$size				=	$a_photoSizes[$key];
					$type				=	$a_photoTypes[$key];
					$temp				=	$a_tempNames[$key];
					$ext				=	findexts($photoName);
					$photoName			=	getFileName($photoName);

					if(!empty($investmentOn))
					{
						$photoName		=	makeDBSafe($photoName);
						$investmentOn	=	trim($investmentOn);
						$investmentOn	=	makeDBSafe($investmentOn);
						dbQuery("INSERT INTO employee_investment_files SET investmentOn='$investmentOn',fileExt='$ext',fileType='$type',fileSize='$size',fileName='$photoName',AddedOn=CURRENT_DATE,employeeId='$employeeId'");

						$investmentId	=	mysqli_insert_id($db_conn);
						
						$file_name		=  "INVESMENT".$investmentId."_".$employeeId."_".$photoName.".".$ext;

						move_uploaded_file($temp,$filePath.$file_name);

					}
				}
			}
		}

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES ."/view-an-employee.php?ID=$employeeId&success=1");
		exit();
	}
	else
	{
		echo $errorMsg	 =	$validator ->getErrors();
		include($form);
	}
}
else
{
	include($form);
}
include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>