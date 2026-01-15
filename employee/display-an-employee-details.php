<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	
	$employeeObj				= new employee();
	$employeeId					= 0;

	if(isset($_GET['ID']))
	{
		$employeeId				=	$_GET['ID'];
		$query					=	"SELECT * FROM employee_details WHERE employeeId=$employeeId AND isActive=1";
		$result					=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$row					=	mysqli_fetch_assoc($result);
			$facebookId             =	$row['facebookId'];
			$facebookEmailId        =   $row['facebookEmailId'];
			$fullName				=	stripslashes($row['fullName']);
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
			$bankName				=	stripslashes($row['bankName']);
			$branchName				=	stripslashes($row['branchName']);
			$accountName			=	stripslashes($row['accountName']);
			$accountNumber			=	stripslashes($row['accountNumber']);
			$bankIFSCcode			=	stripslashes($row['bankIFSCcode']);
			$addedOn				=	showDate($row['addedOn']);
			$hasPdfAccess			=	$row['hasPdfAccess'];
			$panCardNumber			=	stripslashes($row['panCardNumber']);
			$referredBy				=	stripslashes($row['referredBy']);

			$highestQualification	=	$row['highestQualification'];
			$otherQualification		=	stripslashes($row['otherQualification']);
			$qualificationStatus	=	$row['qualificationStatus'];
			$boardUniversity		=	stripslashes($row['boardUniversity']);
			$passedOutOn			=	$row['passedOutOn'];
			$hasProfilePhoto 		=	$row['hasProfilePhoto'];
			$profilePhotoExt		=	stripslashes($row['profilePhotoExt']);
			$aadhaarNumber 			=	stripslashes($row['aadhaarNumber']);
			$isNightShiftEmployee   =	$row['isNightShiftEmployee'];

			$shiftText				=	"&nbsp;(Day Shift)";
			if($isNightShiftEmployee==  1){
				$shiftText			=	"&nbsp;(Night Shift)";
			}
			if(empty($aadhaarNumber)){
				$aadhaarNumber		=	"N/A";
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



			
			$genderText		=	"Male";
			if($gender	==	"F")
			{
				$genderText	=	"Female";
			}
			$countryText	=	$a_countries[$country];
			
		}
	}
	$employeeImageUrl		=	SITE_URL."/files/employee-images/";
?>
<html>
<head>
<TITLE></TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
	<body>
		<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
			<tr>
				<td colspan="9" class="textstyle3"><b>DETAILS OF <?php echo $fullName."(".$shiftText." Employee Since ".$addedOn.")";?></b></td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<?php
				if(!empty($hasProfilePhoto) && !empty($profilePhotoExt))
				{
			?>
			<tr>
				<td colspan="6">
					<img src="<?php echo SITE_URL_EMPLOYEES;?>/get-employee-profile-photos.php?ID=<?php echo $employeeId;?>&ext=<?php echo $profilePhotoExt;?>" border="0" title="<?php echo $fullName;?>"  height="150" width="150">
				</td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<?php
				}
			?>
			<tr>
				<td class="smalltext21">Referred By</td>
				<td class="smalltext21">:</td>
				<td colspan="3">
					<?php
						if(!empty($referredBy)){
							echo "<b>".$referredBy."</b>";
						}
						else{
							echo "<font color='#ff0000;'><b>None</b></font>";
						}
					?>
				</td>
			</tr>
			<tr>
				<td width="15%" valign="top" class="smalltext1">D.O.B.</td>
				<td width="1%" valign="top" class="smalltext1">:</td>
				<td width="34%" valign="top" class="smalltext13"><?php echo showDate($t_dob);?></td>
				<td width="15%" valign="top" class="smalltext1">Father Name</td>
				<td width="1%" valign="top" class="smalltext1">:</td>
				<td valign="top" class="smalltext13"><?php echo $fatherName;?></td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td valign="top" class="smalltext1">Email</td>
				<td valign="top" class="smalltext1">:</td>
				<td valign="top" class="smalltext13"><?php echo $t_email;?></td>
				<td valign="top" class="smalltext1">Mobile No</td>
				<td valign="top" class="smalltext1">:</td>
				<td valign="top" class="smalltext13"><?php echo $mobile;?></td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td valign="top" class="smalltext1">Facebook ID</td>
				<td valign="top" class="smalltext1">:</td>
				<td valign="top" class="smalltext13">
					<?php 
						if(!empty($facebookId)){
							echo $facebookId;
						}
						else{
							echo "N/A";
						}
					?>					
				</td>
				<td valign="top" class="smalltext1">Facebook Email</td>
				<td valign="top" class="smalltext1">:</td>
				<td valign="top" class="smalltext13">
					<?php 
						if(!empty($facebookEmailId)){
							echo $facebookEmailId;
						}
						else{
							echo "N/A";
						}
					?>					
				</td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td valign="top" class="smalltext1">Correspondence</td>
				<td valign="top" class="smalltext1">:</td>
				<td valign="top" class="smalltext13"><?php echo nl2br($address);?></td>
				<td valign="top" class="smalltext1">Permanent</td>
				<td valign="top" class="smalltext1">:</td>
				<td valign="top" class="smalltext13"><?php echo nl2br($perAddress);?></td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td valign="top" class="smalltext1">City</td>
				<td valign="top" class="smalltext1">:</td>
				<td valign="top" class="smalltext13"><?php echo $city;?></td>
				<td valign="top" class="smalltext1">State</td>
				<td valign="top" class="smalltext1">:</td>
				<td valign="top" class="smalltext13"><?php echo $state."(".$countryText.")";?></td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td valign="top" class="smalltext1">Bank Name</td>
				<td valign="top" class="smalltext1">:</td>
				<td valign="top" class="smalltext13"><?php echo $bankName;?></td>
				<td valign="top" class="smalltext1">Branch Name</td>
				<td valign="top" class="smalltext1">:</td>
				<td valign="top" class="smalltext13"><?php echo $branchName;?></td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td valign="top" class="smalltext1">Account Name</td>
				<td valign="top" class="smalltext1">:</td>
				<td valign="top" class="smalltext13"><?php echo $accountName;?></td>
				<td valign="top" class="smalltext1">Account No</td>
				<td valign="top" class="smalltext1">:</td>
				<td valign="top" class="smalltext13"><?php echo $accountNumber;?></td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td valign="top" class="smalltext1">IFSC Code</td>
				<td valign="top" class="smalltext1">:</td>
				<td valign="top" class="smalltext13"><?php echo $bankIFSCcode;?></td>
				<td valign="top" class="smalltext1">PAN No</td>
				<td valign="top" class="smalltext1">:</td>
				<td valign="top" class="smalltext13"><?php echo $panCardNumber;?></td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td valign="top" class="smalltext1">Highest Qualif.</td>
				<td valign="top" class="smalltext1">:</td>
				<td valign="top" class="smalltext13"><?php echo $highestQualificationText;?></td>
				<td valign="top" class="smalltext1">Qualif. Status</td>
				<td valign="top" class="smalltext1">:</td>
				<td valign="top" class="smalltext13"><?php echo $qualificationStatusText;?></td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td valign="top" class="smalltext1">Board/Univ</td>
				<td valign="top" class="smalltext1">:</td>
				<td valign="top" class="smalltext13"><?php echo $boardUniversity;?></td>
				<td valign="top" class="smalltext1">Passes On</td>
				<td valign="top" class="smalltext1">:</td>
				<td valign="top" class="smalltext13"><?php echo $passedOutOn;?></td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td valign="top" class="smalltext1">Aadhaar Number</td>
				<td valign="top" class="smalltext1">:</td>
				<td valign="top" class="smalltext13"><?php echo $aadhaarNumber;?></td>
				<td valign="top" class="smalltext1">&nbsp;</td>
				<td valign="top" class="smalltext1">&nbsp;</td>
				<td valign="top" class="smalltext13">&nbsp;</td>
			</tr>
		</table>
	</body>
</html>