<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	ini_set('display_errors', 1);
	$docTitle					=	"New Employee Registration";
	include(SITE_ROOT_EMPLOYEES .   "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES .   "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES .   "/classes/employee.php");
	include(SITE_ROOT			.   "/classes/validate-fields.php");
	include(SITE_ROOT			.   "/classes/email-templates.php");

	if(!isset($_SESSION['isValidationRegsitartionDone'])){
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/validate-registration.php");
		exit();
	}

	$form						=	SITE_ROOT_EMPLOYEES.    "/forms/registration1.php";
	$employeeId					=	0;
	$firstName					=	"";
	$lastName					=	"";
	$fatherName					=	"";
	$email						=	"";
	$mobile						=	"";
	$city						=	"";
	$state						=	"";
	$dob						=	"";
	$t_dob						=	"0000-00-00";
	$address					=	"";
	$perAddress					=	"";
	$country					=	"";
	$registrationCode			=	"";
	$check						=	"checked";
	$check1						=	"";
	$terms						=	0;
	$sex						=	"Male";
	$employeeObj				=	new employee();
	$validator					=	new validate();
	$emailObj					=   new emails();
	$filePath					=	SITE_ROOT_FILES."/files/member-identity/";
	$success					=	0;
	$employeeName				=	"";
	$identityProofType			=	"1";
	$bankName					=	"";
	$branchName					=	"";
	$accountName				=	"";
	$accountNumber				=	"";
	$bankIFSCcode				=	"";
	$panCardNumber				=	"";
	$referredBy					=   "";
	$fullName					=	"";
	$totalInvesmentAvailable	=	1;

	$highestQualification		=	"";
	$otherQualification			=	"";
	$qualificationStatus		=	1;
	$boardUniversity			=	"";
	$passedOutOn				=	0;
	$showHideExtraQualifications=	"none";
	$number1					=	rand(11,99);
	$number2					=	rand(11,99);
	$numberResult				=	$number1+$number2;
	$aadhaarNumber				=	"";

	if(isset($_SESSION['successEmployeeId']))
	{
		$success		 =	$_SESSION['successEmployeeId'];
		if(!$employeeName=	$employeeObj->getEmployeeName($success))
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}		
	}

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

	if(isset($_SESSION['employeeId']))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES ."/employee-details.php");
		exit();
	}
	if(isset($_SESSION['successEmployeeId']))
	{
?>
	<table cellpadding="3" cellspacing="2" width="98%" border="0" align="center">
		<tr>
			<td>
				<p><font class="text5"><b>Dear <?php echo $employeeName;?>,</b><br>
				Welcome to ieIMPACT Employee Area. You have successfully enrolled into our employee area.<br>
				Currenly your account is under review. Your account will activate after one of our  administrator will verify it.<br>Please use this email address to login your account in our employee area.<br>
				Please contact administrator.</font></p>
			</td>
		</tr>
		<tr>
			<td height="80"></td>
		</tr>
	</table>
<?php
		unset($_SESSION['successEmployeeId']);
	}
	elseif(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);

		$firstName			=	trim($firstName);
		$lastName			=	trim($lastName);
		$referredBy			=   makeDBSafe($referredBy);
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
		$panCardNumber		=	strtoupper($panCardNumber);
		$boardUniversity	=	trim($boardUniversity);
		$otherQualification	=	trim($otherQualification);
		$aadhaarNumber		=	trim($aadhaarNumber);
		
		$firstName			=	makeDBSafe($firstName);
		$lastName			=	makeDBSafe($lastName);
		$referredBy			=   makeDBSafe($referredBy);
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
		$aadhaarNumber		=	makeDBSafe($aadhaarNumber);

		if($highestQualification			==	6)
		{
			$showHideExtraQualifications	=	"";
		}
		
		if(isset($_POST['terms']))
		{
			$terms	=	1;
		}
		$validator->checkField($firstName,"","Please enter your first name.");
		$validator->checkField($lastName,"","Please enter your last name.");
		$validator->checkField($dob,"","Please enter date of birth.");
		$validator->checkField($fatherName,"","Please enter your father's name.");
		$validator->checkField($email,"","Please enter your email.");
		$disposableEmails  = blockEmailAddress();

		if(!empty($email))
		{
			if(!filter_var($email, FILTER_VALIDATE_EMAIL))
			{
				$validator->setError("Your email is invalid.");
			}
			else
			{
				$emailDomain 	   = getEmailDomain($email);
				if(!empty($emailDomain) && in_array($emailDomain,$disposableEmails)){
                    $validator->setError("The email address is invalid. Please check.");
                }
                elseif($result=	$employeeObj->getEmployeeExistingEmail($email))
				{
					$validator->setError("This email is already in use.");
				}
			}
		}
		$validator->checkField($password,"","Please enter password.");
		if(!empty($password))
		{
			$passwordLength	=	strlen($password);
			if($passwordLength < 5)
			{
				$validator->setError("Your password is too short.");
			}
		}
		$validator->checkField($rePassword,"","Please re-type password.");
		if(!empty($password) && !empty($rePassword) && $password != $rePassword)
		{
			$validator->setError("Password and re-typed password does not match.");
		}
		$validator->checkField($registrationCode,"","Please enter registration cod.");
		if(!empty($registrationCode) && $registrationCode != EMP_REGISTRATION_CODE)
		{
			$validator->setError("Please enter valid registration Code.");
		}
		$validator->checkField($mobile,"","Please enter mobile no.");
		if(!empty($mobile) && !is_numeric($mobile))
		{
			$validator->setError("Please donot use character in mobile number.");
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
		}
        $validator->checkField($aadhaarNumber,"","Please enter your Aadhaar number.");
		if(!empty($aadhaarNumber)){
			$validateAadhaar	 =	isAadharValid($aadhaarNumber);
			
			if($validateAadhaar !=  1){
				$validator->setError("Please enter a valid Aadhaar number.");
			}
		}
		
		$validator->checkField($city,"","Please enter city.");
		$validator->checkField($state,"","Please enter state/province.");
		$validator->checkField($country,"","Please select country.");
		$validator->checkField($address,"","Please enter correspondence  address.");
		$validator->checkField($perAddress,"","Please enter Please enter permanent address !!");
		if(!empty($_FILES['identityProof']['name']))
		{
			$uploadingFile	    =   $_FILES['identityProof']['name'];
			$ext			    =	findexts($uploadingFile);
			if($ext != "jpg" && $ext != "jpeg" && $ext != "gif" && $ext != "png" && $ext != "bmp"  && $ext != "pdf")
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

			if($ext != "jpg" && $ext != "jpeg" && $ext != "gif" && $ext != "png" && $ext != "bmp" && $ext != "doc" && $ext != "docx" && $ext != "xls" && $ext != "pdf")
			{
				$validator->setError("Please upload  HIPPA compliance in a valid format like image, document or pdf.");
			}
		}
		if(!empty($_FILES['resume']['name']))
		{
			$uploadingFile	    =   $_FILES['resume']['name'];
			$ext			    =	findexts($uploadingFile);
			if($ext != "doc" && $ext != "docx"  && $ext != "pdf")
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
		$validator->checkField($highestQualification,"","Please select highest qualification !!");
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
		$validator->checkField($terms,"","Please agree with our terms and conditions.");
		$validator->checkField($securityCode,"","Please enter the sum of numbers.");
		if(!empty($securityCode) && $securityCode != $inputsumOfTotal)
		{			
			$validator->setError("Please enter correct sum of numbers.");			
		}
		$dataValid	 =	$validator->isDataValid();
		if($dataValid)
		{
			list($day,$month,$year)		=	explode("-",$dob);
			$t_dob		=	$year."-".$month."-".$day;

			$options    =   array('cost' => 12);
			$newPassword=	password_hash($password, PASSWORD_BCRYPT, $options);

			if($gender	==	"F"){
				$sex	=	"Female";
			}

		    if(empty($lastName))
			{
				$lastName		=	"";
			}
			else
			{
				$lastName		=	" ".$lastName;
			}

			$fullName				=	$firstName.$lastName;
			$employeeSecurityCode	=	rand(123,789);

			$query	=	"INSERT INTO employee_details SET firstName='$firstName',lastName='$lastName',fatherName='$fatherName',email='$email',gender='$gender',password='$newPassword',mobile=$mobile,dob='$t_dob',city='$city',state='$state',country='$country',address='$address',perAddress='$perAddress',identityProofType=$identityProofType,bankName='$bankName',branchName='$branchName',accountName='$accountName',accountNumber='$accountNumber',bankIFSCcode='$bankIFSCcode',panCardNumber='$panCardNumber',fullName='$fullName',securityCode='$employeeSecurityCode',highestQualification=$highestQualification,otherQualification='$otherQualification',qualificationStatus=$qualificationStatus,boardUniversity='$boardUniversity',passedOutOn=$passedOutOn,onsiteOffsite=$onsiteOffsite,enrollAs='$enrollAs',isNewRegistration=1,aadhaarNumber=$aadhaarNumber,showQuestionnaire=1,registrationCode='$registrationCode',addedOn='".CURRENT_DATE_INDIA."',ip='".VISITOR_IP_ADDRESS."'";

				dbQuery($query);

			$employeeId	=	mysqli_insert_id($db_conn);

			if(!empty($_FILES['identityProof']['name']))
			{
				$uploadingFile	    =   $_FILES['identityProof']['name'];
				$mimeType		    =   $_FILES['identityProof']['type'];
				$fileSize		    =   $_FILES['identityProof']['size'];
				$tempName		    =	$_FILES['identityProof']['tmp_name'];
				$ext			    =	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);

				$fileName			= "ID_".$employeeId."_".$uploadingFileName.".".$ext;

				move_uploaded_file($tempName,$filePath.$fileName);

				$uploadingFileName	=	makeDBSafe($uploadingFileName);
				$fileTypeName 		=   $a_employeesFilesUpoadingTypes[1];//Identity Proof File
				$fileServerPath     =   makeDBSafe($filePath.$fileName);


				dbQuery("UPDATE employee_details SET hasIdentityProof=1 WHERE employeeId=$employeeId");

				
				dbQuery("INSERT INTO employeee_profile_files SET employeeId=$employeeId,type=1,fileTypeName='$fileTypeName',fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");


			}
			if(!empty($_FILES['panCard']['name']))
			{
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
				$fileServerPath     =   makeDBSafe($filePath.$fileName);


				dbQuery("UPDATE employee_details SET hasPanCardProof=1 WHERE employeeId=$employeeId");

				
				dbQuery("INSERT INTO employeee_profile_files SET employeeId=$employeeId,type=2,fileTypeName='$fileTypeName',fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
			}
			if(!empty($_FILES['complianceForm']['name']))
			{
				$uploadingFile	    =   $_FILES['complianceForm']['name'];
				$mimeType		    =   $_FILES['complianceForm']['type'];
				$fileSize		    =   $_FILES['complianceForm']['size'];
				$tempName		    =	$_FILES['complianceForm']['tmp_name'];
				$ext			    =	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);

				$fileName		= "CF_".$employeeId."_".$uploadingFileName.".".$ext;

				move_uploaded_file($tempName,$filePath.$fileName);

				$uploadingFileName	=	makeDBSafe($uploadingFileName);
				$fileTypeName 		=   $a_employeesFilesUpoadingTypes[3];//Compliance Form File
				$fileServerPath     =   makeDBSafe($filePath.$fileName);


				dbQuery("UPDATE employee_details SET hasComplianceForm=1 WHERE employeeId=$employeeId");

				
				dbQuery("INSERT INTO employeee_profile_files SET employeeId=$employeeId,type=3,fileTypeName='$fileTypeName',fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
			}
			if(!empty($_FILES['resume']['name']))
			{
				$uploadingFile	    =   $_FILES['resume']['name'];
				$mimeType		    =   $_FILES['resume']['type'];
				$fileSize		    =   $_FILES['resume']['size'];
				$tempName		    =	$_FILES['resume']['tmp_name'];
				$ext			    =	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);

				$fileName		= "RS_".$employeeId."_".$uploadingFileName.".".$ext;

				move_uploaded_file($tempName,$filePath.$fileName);

				$uploadingFileName	=	makeDBSafe($uploadingFileName);
				$fileTypeName 		=   $a_employeesFilesUpoadingTypes[4];//Resume File
				$fileServerPath     =   makeDBSafe($filePath.$fileName);

				dbQuery("UPDATE employee_details SET hasResume=1 WHERE employeeId=$employeeId");

				
				dbQuery("INSERT INTO employeee_profile_files SET employeeId=$employeeId,type=4,fileTypeName='$fileTypeName',fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
			}

			if(!empty($_FILES['residenceProof']['name']))
			{
				$uploadingFile	    =   $_FILES['residenceProof']['name'];
				$mimeType		    =   $_FILES['residenceProof']['type'];
				$fileSize		    =   $_FILES['residenceProof']['size'];
				$tempName		    =	$_FILES['residenceProof']['tmp_name'];
				$ext			    =	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);

				$fileName			=   "RP_".$employeeId."_".$uploadingFileName.".".$ext;

				move_uploaded_file($tempName,$filePath.$fileName);

				$uploadingFileName	=	makeDBSafe($uploadingFileName);
				$fileTypeName 		=   $a_employeesFilesUpoadingTypes[6];//Residence Proof File
				$fileServerPath     =   makeDBSafe($filePath.$fileName);


				dbQuery("UPDATE employee_details SET hasResidenceProof=1 WHERE employeeId=$employeeId");

				
				dbQuery("INSERT INTO employeee_profile_files SET employeeId=$employeeId,type=6,fileTypeName='$fileTypeName',fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");

			}

			if(!empty($_FILES['ieimpactAgreement']['name']))
			{
				$uploadingFile	    =   $_FILES['ieimpactAgreement']['name'];
				$mimeType		    =   $_FILES['ieimpactAgreement']['type'];
				$fileSize		    =   $_FILES['ieimpactAgreement']['size'];
				$tempName		    =	$_FILES['ieimpactAgreement']['tmp_name'];
				$ext			    =	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);

				$fileName			=   "IA_".$employeeId."_".$uploadingFileName.".".$ext;

				move_uploaded_file($tempName,$filePath.$fileName);

				$uploadingFileName	=	makeDBSafe($uploadingFileName);
				$fileTypeName 		=   $a_employeesFilesUpoadingTypes[7];//Agreement File
				$fileServerPath     =   makeDBSafe($filePath.$fileName);


				dbQuery("UPDATE employee_details SET hasAgreement=1 WHERE employeeId=$employeeId");
				
				dbQuery("INSERT INTO employeee_profile_files SET employeeId=$employeeId,type=7,fileTypeName='$fileTypeName',fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
			}

			if(!empty($_FILES['ieimpactAppoinmentLetter']['name']))
			{
				$uploadingFile	    =   $_FILES['ieimpactAppoinmentLetter']['name'];
				$mimeType		    =   $_FILES['ieimpactAppoinmentLetter']['type'];
				$fileSize		    =   $_FILES['ieimpactAppoinmentLetter']['size'];
				$tempName		    =	$_FILES['ieimpactAppoinmentLetter']['tmp_name'];
				$ext			    =	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);

				$fileName			=   "IAL_".$employeeId."_".$uploadingFileName.".".$ext;

				move_uploaded_file($tempName,$filePath.$fileName);

				$uploadingFileName	=	makeDBSafe($uploadingFileName);
				$fileTypeName 		=   $a_employeesFilesUpoadingTypes[8];//Appointment File
				$fileServerPath     =   makeDBSafe($filePath.$fileName);


				dbQuery("UPDATE employee_details SET hasAppointmentLetter=1 WHERE employeeId=$employeeId");
				
				dbQuery("INSERT INTO employeee_profile_files SET employeeId=$employeeId,type=8,fileTypeName='$fileTypeName',fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
			}


			if(!empty($_FILES['ieimpactEmployeeAgreement']['name']))
			{
				$uploadingFile	    =   $_FILES['ieimpactEmployeeAgreement']['name'];
				$mimeType		    =   $_FILES['ieimpactEmployeeAgreement']['type'];
				$fileSize		    =   $_FILES['ieimpactEmployeeAgreement']['size'];
				$tempName		    =	$_FILES['ieimpactEmployeeAgreement']['tmp_name'];
				$ext			    =	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);

				$fileName			=   "IEA_".$employeeId."_".$uploadingFileName.".".$ext;

				move_uploaded_file($tempName,$filePath.$fileName);

				$uploadingFileName	=	makeDBSafe($uploadingFileName);
				$fileTypeName 		=   $a_employeesFilesUpoadingTypes[9];//Employee Agreement File
				$fileServerPath     =   makeDBSafe($filePath.$fileName);


				dbQuery("UPDATE employee_details SET hasEmployeeAgreement=1 WHERE employeeId=$employeeId");
				
				dbQuery("INSERT INTO employeee_profile_files SET employeeId=$employeeId,type=9,fileTypeName='$fileTypeName',fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
			}

			////////////////////////////////// SENDING EMAIL WITH EMPLOYEE INFO /////////////////
				
			$registeredIp					=	VISITOR_IP_ADDRESS;
			$registeredIpCountry			=	"";
			$registeredIpRegion				=	"";
			$registeredIpCity				=	"";
			$registeredIpZipCode			=	"";
			$registeredIpLatitude			=	"";
			$registeredIpLongitude			=	"";
			$registeredIpISP				=	"";

			if($ipLattitudeLocationCity		=	getIPDetailsWithAlternateFunctions($registeredIp))
			{
				$registeredIpCountry		=	$ipLattitudeLocationCity['country'];
				$registeredIpRegion			=	$ipLattitudeLocationCity['region'];
				$registeredIpCity			=	$ipLattitudeLocationCity['city'];
				$registeredIpZipCode		=	$ipLattitudeLocationCity['zipcode'];
				$registeredIpLatitude		=	$ipLattitudeLocationCity['latitude'];
				$registeredIpLongitude		=	$ipLattitudeLocationCity['longitude'];
				$registeredIpISP			=	$ipLattitudeLocationCity['ipisp'];

				if(!empty($registeredIpCountry))
				{
					$t_registeredIpCountry	=	makeDBSafe($registeredIpCountry);
					$t_registeredIpRegion	=	makeDBSafe($registeredIpRegion);
					$t_registeredIpCity		=	makeDBSafe($registeredIpCity);
					$t_registeredIpZipCode	=	makeDBSafe($registeredIpZipCode);
					$t_registeredIpLatitude	=	makeDBSafe($registeredIpLatitude);
					$t_registeredIpLongitude=	makeDBSafe($registeredIpLongitude);
					$t_registeredIpISP		=	makeDBSafe($registeredIpISP);
				}
			}
			
			$infoTable			=	"<table width='100%' align='center' cellpadding='0' cellspacing='0'>";
							
			$infoTable		   .=   "<tr><td colspan='3' align='left'><font size='3px' face='verdana' color='#4d4d4d'>A new employee ".$fullName." created account in ieIMPACT employee area<br><br><b>EMPLOYEE DETAILS</b></font></td></tr><tr><td height='5'></td></tr>";
			
			$infoTable		   .=   "<tr><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>Employee ID</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>:</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'><b>".$employeeId."</b></font></td></tr><tr><td height='5'></td></tr>";

			$infoTable		   .=   "<tr><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>Register as</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>:</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'><b>".ucwords($enrollAs)."</b></font></td></tr><tr><td height='5'></td></tr>";

			$infoTable		   .=   "<tr><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>Email</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>:</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'><b>".$email."</b></font></td></tr><tr><td height='5'></td></tr>";

			$infoTable		   .=   "<tr><td width='30%' align='left'><font size='2px' face='verdana' color='#4d4d4d'>First Name</font></td><td width='1%' align='left'><font size='2px' face='verdana' color='#4d4d4d'>:</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'><b>".stripslashes($firstName)."</b></font></td></tr><tr><td height='5'></td></tr>";

			$infoTable		   .=   "<tr><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>Last Name</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>:</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'><b>".stripslashes($lastName)."</b></font></td></tr><tr><td height='5'></td></tr>";

			if(!empty($referredBy))
			{
				$infoTable		   .=   "<tr><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>Referred by</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>:</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'><b>".stripslashes($referredBy)."</b></font></td></tr><tr><td height='5'></td></tr>";
			}

			$infoTable		   .=   "<tr><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>D.O.B.</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>:</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'><b>".showDate($t_dob)."</b></font></td></tr><tr><td height='5'></td></tr>";

			$infoTable		   .=   "<tr><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>Sex</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>:</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'><b>".$sex."</b></font></td></tr><tr><td height='5'></td></tr>";

			$infoTable		   .=   "<tr><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>Father Name</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>:</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'><b>".stripslashes($fatherName)."</b></font></td></tr><tr><td height='5'></td></tr>";

			

			$infoTable		   .=   "<tr><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>Correspondence Address</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>:</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'><b>".$address."</b></font></td></tr><tr><td height='5'></td></tr>";

			$infoTable		   .=   "<tr><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>Permanent Address</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>:</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'><b>".$perAddress."</b></font></td></tr><tr><td height='5'></td></tr>";

			$infoTable		   .=   "<tr><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>City/State</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>:</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'><b>".$city.", ".$state."</b></font></td></tr><tr><td height='5'></td></tr>";

			$infoTable		   .=   "<tr><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>Mobile Phone</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>:</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'><b>".$mobile."</b></font></td></tr><tr><td height='5'></td></tr>";

			$infoTable		   .=   "<tr><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>IP Address</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>:</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'><a href='http://api.ip-adress.com/?u=f0adfe266b50303ee61693ec917ef6fb2ef5c&h=".$registeredIp."' target='_blank'>".$registeredIp."</a></font></td></tr><tr><td height='5'></td></tr>";

			$infoTable		   .=   "<tr><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>IP Region</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>:</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'><b>".$registeredIpRegion."</b></font></td></tr><tr><td height='5'></td></tr>";

			$infoTable		   .=   "<tr><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>IP City</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>:</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'><b>".$registeredIpCity."</b></font></td></tr><tr><td height='5'></td></tr>";

			$infoTable		   .=   "<tr><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>IP Country</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>:</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'><b>".$registeredIpCountry."</b></font></td></tr><tr><td height='5'></td></tr>";

			$infoTable		   .=   "<tr><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>IP Zip Code</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>:</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'><b>".$registeredIpZipCode."</b></font></td></tr><tr><td height='5'></td></tr>";

			$infoTable		   .=   "<tr><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>IP Latitude</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>:</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'><b>".$registeredIpLatitude."</b></font></td></tr><tr><td height='5'></td></tr>";

			$infoTable		   .=   "<tr><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>IP Longitude</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>:</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'><b>".$registeredIpLongitude."</b></font></td></tr><tr><td height='5'></td></tr>";

			$infoTable		   .=   "<tr><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>IP Service Provider</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'>:</font></td><td align='left'><font size='2px' face='verdana' color='#4d4d4d'><b>".$registeredIpISP."</b></font></td></tr><tr><td height='5'></td></tr></table>";

			$managerEmployeeFromName     	=   "ieIMPACT Support";
			$managerEmployeeEmailSubject 	=   "New Employee Registartion";
			$a_templateData		        	=	array("{bodyMatter}"=>$infoTable);
			$toEmail			            =	"john@ieimpact.net"; 
			$managerEmployeeFromCc          =   "hr@ieimpact.com,rishi@ieimpact.com,hemant@ieimpact.net,gaurabsiva1@gmail.com,harminder@ieimpact.com";			
			$uniqueTemplateName	            =	"TEMPLATE_SENDING_NEW_SIMPLEE_MESSAGE";
			include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");			

			$_SESSION['successEmployeeId']	=	$employeeId;

			if(isset($_SESSION['isValidationRegsitartionDone'])){
				unset($_SESSION['isValidationRegsitartionDone']);
			}

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES ."/registration.php");
			exit();
		}
		else
		{
			echo $errorMsg	 =	$validator->getErrors();
			include($form);
		}
	}
	else
	{
		include($form);
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");

?>