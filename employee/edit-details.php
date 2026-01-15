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
	include(SITE_ROOT			. "/classes/validate-fields.php");
	$showHideBrowseOption		=	false;

	$downloadingID	=	$s_employeeId;
	$dob			=	"";
	$check			=	"checked";
	$check1			=	"";
	$t_email		=	"";
	$validator		=	new validate();
	$filePath		=	SITE_ROOT_FILES."/files/member-identity/";
	$redirectUrl	=	"edit-details.php?";
	$employeeId     =   $s_employeeId; 
	
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

	$query			=	"SELECT * FROM employee_details WHERE employeeId=$s_employeeId AND isActive=1";
	$result		=	dbQuery($query);
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
		$country				=	$row['country'];
		$address				=	stripslashes($row['address']);
		$perAddress				=	stripslashes($row['perAddress']);
		$bankName				=	stripslashes($row['bankName']);
		$branchName				=	stripslashes($row['branchName']);
		$accountName			=	stripslashes($row['accountName']);
		$accountNumber			=	stripslashes($row['accountNumber']);
		$bankIFSCcode			=	stripslashes($row['bankIFSCcode']);
		$panCardNumber			=	stripslashes($row['panCardNumber']);
		$addedOn				=	showDate($row['addedOn']);

		$aadhaarNumber		    =	stripslashes($row['aadhaarNumber']);
		if(empty($aadhaarNumber)){
			$aadhaarNumber 		=	"";
		}
		
		$hasIdentityProof		=	$row['hasIdentityProof'];
		$hasPanCardProof		=	$row['hasPanCardProof'];
		$hasComplianceForm		=	$row['hasComplianceForm'];
		$hasResume				=	$row['hasResume'];
		$hasResidenceProof		=	$row['hasResidenceProof'];
		$hasAgreement			=	$row['hasAgreement'];
		$hasAppointmentLetter	=	$row['hasAppointmentLetter'];
		$hasEmployeeAgreement	=	$row['hasEmployeeAgreement'];		


		$isShiftTimeAdded		=	$row['isShiftTimeAdded'];
		$shiftFrom				=	$row['shiftFrom'];
		$shiftTo				=	$row['shiftTo'];
		$dictaEscrId			=	$row['dictaEscrId'];

		$highestQualification	=	$row['highestQualification'];
		$otherQualification		=	stripslashes($row['otherQualification']);
		$qualificationStatus	=	$row['qualificationStatus'];
		$boardUniversity		=	stripslashes($row['boardUniversity']);
		$passedOutOn			=	$row['passedOutOn'];

		$hasCancelledCheque  	=	$row['hasCancelledCheque'];
		
		$hasFormEleven      	=	$row['hasFormEleven'];
		
		$hasResignedFile       	=	$row['hasResignedFile'];
		

		$hasFormElevenRevised  	=	$row['hasFormElevenRevised'];

		if(isset($_GET['deleteFileType'])){
			$deleteFileType		=	$_GET['deleteFileType'];
			if($deleteFileType  ==  2 && $hasFormEleven == 1){
				////DELETE FORM 11
				$type 			=	12;
				$updateColumn 	=	"hasFormEleven";
			}
			if($deleteFileType  ==  3 && $hasFormEleven == 1){
				////DELETE Resignation

				$type 			=	11;
				$updateColumn 	=	"hasResignedFile";
			}
			elseif($deleteFileType  ==  4 && $hasFormElevenRevised == 1){
				////DELETE FORM 11 REVISED
				$type 			 =	12;
				$updateColumn 	 =	"hasFormElevenRevised";	
			}

			$query = "SELECT fileServerPath FROM employeee_profile_files WHERE employeeId=$s_employeeId AND type=$type ORDER BY fileId LIMIT 1";

			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row			 =	mysqli_fetch_assoc($result);
				$fileServerPath  =  $row['fileServerPath'];

				if(file_exists($fileServerPath))
				{
					unlink($fileServerPath);
				}

				dbQuery("UPDATE employee_details SET ".$updateColumn."=0 WHERE employeeId=$s_employeeId");

				dbQuery("DELETE FROM employeee_profile_files WHERE employeeId=$s_employeeId AND type=$type");

			}


			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES ."/edit-details.php");
			exit();
		}
		

		

		if($highestQualification			==	6)
		{
			$showHideExtraQualifications	=	"";
		}


		if($isShiftTimeAdded	==	1)
		{
			list($shiftFromHrs,$shiftFromMinitue,$fh)	=	explode(":",$shiftFrom);
			list($sfiftToHrs,$shiftToMinitue,$th)		=	explode(":",$shiftTo);
		}		

		if($gender		==	"F")
		{
			$check		=	"";
			$check1		=	"checked";
		}
		$employeeName	=	$firstName." ".$lastName;
		$employeeName	=	ucwords($employeeName);
		if($t_dob		!=	"0000-00-00")
		{
			list($year,$month,$day)		=	explode("-",$t_dob);
			$dob			=	$day."-".$month."-".$year;
		}	

		$a_investmentFiles	 =	array();
		$showAddingInvestment=	true;
		$query				 =	"SELECT * FROM employee_investment_files WHERE employeeId=$s_employeeId";
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

		
	}

	if(isset($_GET['investmentID']) && $_GET['investmentID'] !=0  && isset($_GET['isDeleteInvestment']) && $_GET['isDeleteInvestment'] == 1)
	{
		$investmentId			=	$_GET['investmentID'];
			
		$query					=	"SELECT * FROM employee_investment_files WHERE investmentId=$investmentId AND employeeId=$s_employeeId";
		$result					=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$row				=	mysqli_fetch_assoc($result);
		
			$fileName			=	$row['fileName'];
			$ext				=	$row['fileExt'];

			if(file_exists($filePath."INVESMENT".$investmentId."_".$s_employeeId."_".$fileName.".".$ext))
			{
				unlink($filePath."INVESMENT".$investmentId."_".$s_employeeId."_".$fileName.".".$ext);

				dbQuery("DELETE FROM employee_investment_files WHERE investmentId=$investmentId AND employeeId=$s_employeeId");
			}
		}
		
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES ."/edit-details.php#investment");
		exit();
	}
	$form		=	SITE_ROOT_EMPLOYEES  . "/forms/edit-employee.php";
?>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td colspan="2" class='title'>
			View Your Details
		</td>
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
			SUCCESSFULLY EDITED YOUR DETAILS
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
</table>
<?php
}
if(isset($_REQUEST['formSubmitted']))
{
	extract($_REQUEST);

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
	$aadhaarNumber		=	makeDBSafe($aadhaarNumber);
	
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
	$panCardNumber		=	strtoupper($panCardNumber);
	$boardUniversity	=	trim($boardUniversity);
	$otherQualification	=	trim($otherQualification);
	$aadhaarNumber	    =	trim($aadhaarNumber);

	$validator ->checkField($firstName,"","Please enter your first name.");
	$validator ->checkField($dob,"","Please enter date of birth.");
	$validator ->checkField($fatherName,"","Please enter your father's name.");
	$validator ->checkField($email,"","Please enter your email.");
	if(!empty($email))
	{
		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$validator->setError("Your email is invalid.");
		}
		else
		{
			if(!checkingMailgunValidEmail($email))
			{
				$validator->setError("The email address is invalid. Please check.");
			}
			else
			{
				if($result=	$employeeObj->getEmployeeExistingEmail($email,$s_employeeId))
				{
					$validator ->setError("This email is already in use.");
				}
			}
		}
	}
	
	$validator ->checkField($mobile,"","Please enter mobile no.");
	if(!empty($mobile) && !is_numeric($mobile))
	{
		$validator ->setError("Please donot use character in mobile number.");
	}
	$validator ->checkField($city,"","Please enter city.");
	$validator ->checkField($state,"","Please enter state/province.");
	$validator ->checkField($country,"","Please select country.");
	$validator ->checkField($address,"","Please enter correspondence  address.");
	$validator ->checkField($perAddress,"","Please enter Please enter permanent address.");

	$validator ->checkField($bankName,"","Please enter bank name where your account is.");
	$validator ->checkField($branchName,"","Please enter your bank branch name.");
	$validator ->checkField($accountName,"","Please enter name in your account.");
	$validator ->checkField($accountNumber,"","Please enter your account number.");
	$validator ->checkField($bankIFSCcode,"","Please enter your bank IFSC code.");
	$validator ->checkField($panCardNumber,"","Please enter your PAN number.");
	if(!empty($aadhaarNumber)){
		
		if(!is_numeric($aadhaarNumber)){
			$validator ->setError("Please enter a valid aadhaar number with only digits.");
		}
		elseif(strlen($aadhaarNumber) < 10){
			$validator ->setError("Please enter a valid aadhaar number.");
		}
		else{		
			$isExistAadhaar	=	$employeeObj->getSingleQueryResult("SELECT aadhaarNumber FROM employee_details WHERE aadhaarNumber=$aadhaarNumber AND employeeId <> $s_employeeId ORDER BY employeeId DESC LIMIT 1","aadhaarNumber");
			if(!empty($isExistAadhaar)){
				$validator ->setError("This aadhaar number is already added by other employee.");
			}
		}
	}
	else{
		$aadhaarNumber	= 0;
	}
	
	$validator ->checkField($highestQualification,"","Please select highest qualification.");
	if(!empty($highestQualification) && $highestQualification == 6 && empty($otherQualification))
	{
		$validator ->setError("Please enter other qualification.");
	}
	$validator ->checkField($qualificationStatus,"","Please select qualification status.");
	if($qualificationStatus	==	1 || $qualificationStatus == 3)
	{
		if(empty($passedOutOn))
		{
			$validator ->setError("Please select passed out.");
		}
	}
	$validator ->checkField($boardUniversity,"","Please enter board/college/university.");
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

		$query		=	"UPDATE employee_details SET firstName='$firstName',lastName='$lastName',fullName='$fullName',fatherName='$fatherName',email='$email',gender='$gender',altEmail='$altEmail',phone='$phone',mobile=$mobile,dob='$t_dob',city='$city',state='$state',country='$country',address='$address',perAddress='$perAddress',bankName='$bankName',branchName='$branchName',accountName='$accountName',accountNumber='$accountNumber',bankIFSCcode='$bankIFSCcode',panCardNumber='$panCardNumber',aadhaarNumber=$aadhaarNumber,highestQualification=$highestQualification,otherQualification='$otherQualification',qualificationStatus=$qualificationStatus,boardUniversity='$boardUniversity',passedOutOn=$passedOutOn WHERE employeeId=$s_employeeId AND isActive=1";
		dbQuery($query);

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES ."/edit-details.php?success=1");
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