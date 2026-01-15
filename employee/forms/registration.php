<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/validate.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>
<script type="text/javascript">

var count  = "<?php echo $totalInvesmentAvailable;?>";
var count1 = 1;
function addMore()
{
	var tr1        = document.createElement('tr');
	var td2        = document.createElement('td');	
	var txt2       = document.createElement('input');
	txt2.type      = "text";
	txt2.name      = "investmentOn[]";
	txt2.size      = "60";
	txt2.maxlength = "150";
	

	td2.appendChild(txt2);	
	tr1.appendChild(td2);

	var td3        = document.createElement('td');	
	var txt3       = document.createElement('input');
	txt3.type      = "file";
	txt3.name      = "investmentFile[]";

	td3.appendChild(txt3);	
	tr1.appendChild(td3);
					
	if(count > 0)
	{
		var img     = document.createElement('IMG');
		img.setAttribute('src', '<?php echo SITE_URL?>/images/c_delete.gif');
		img.onclick = function()
		{
			removeContact(tr1);
		}
		td3.appendChild(img);
	}
	if(count < 3)
	{
		document.getElementById('table1').appendChild(tr1);
		count	= parseInt(count)+1;
	}
}

function removeContact(tr)
{
	tr.parentNode.removeChild(tr);
	count	=	count-1;
}

function checkForNumber()
{
	k = (document.all)?event.keyCode : arguments.callee.caller.arguments[0].which;
	if(k == 8 || k== 0)
	{
		return true;
	}
	if(k >= 48 && k <= 57 )
	{
		return true;
	}
	else
	{
		return false;
	}
}

function checkForCharcNumber()
{
	k = (document.all)?event.keyCode : arguments.callee.caller.arguments[0].which;
	if(k == 8 || k== 0)
	{
		return true;
	}
	if(k >= 48 && k <= 57 )
	{
		return true;
	}
	else if(k >= 65 && k <= 90 )
	{
		return true;
	}
	else if(k >= 97 && k <= 122 )
	{
		return true;
	}
	else
	{
		return false;
	}
}

function check()
{
	//return;
	form1	=	document.registration;
	if(form1.firstName.value	==	"")
	{
		alert("Please enter your first name !!");
		form1.firstName.focus();
		return false;
	}
	if(form1.lastName.value	==	"")
	{
		alert("Please enter your last name !!");
		form1.lastName.focus();
		return false;
	}
	if(form1.dob.value	==	"")
	{
		alert("Please enter date of birth !!");
		form1.dob.focus();
		return false;
	}
	if(form1.fatherName.value	==	"")
	{
		alert("Please enter your father's name !!");
		form1.fatherName.focus();
		return false;
	}
	if(form1.email.value	==	"")
	{
		alert("Please enter your email !!");
		form1.email.focus();
		return false;
	}
	if(form1.email.value != "")
	{
		if(isEmail(form1.email.value) == false)
		{
			alert("Entered email is invalid !!");
			form1.email.focus();
			return false;
		}
	}
	if(form1.altEmail.value != "")
	{
		if(isEmail(form1.altEmail.value) == false)
		{
			alert("Entered alternate email is invalid !!");
			form1.altEmail.focus();
			return false;
		}
	}
	if(form1.password.value	==	"")
	{
		alert("Please enter password !!");
		form1.password.focus();
		return false;
	}
	if(form1.password.value.length < 5)
	{
		alert("Your password is too short !!");
		form1.password.focus();
		return false;
	}
	if(form1.rePassword.value	==	"")
	{
		alert("Please re-type password !!");
		form1.rePassword.focus();
		return false;
	}
	if(form1.password.value != form1.rePassword.value)
	{
		alert("Password and re-typed password does not match !!");
		form1.rePassword.focus();
		return false;
	}
	if(form1.registrationCode.value	==	"")
	{
		alert("Please enter registration code !!");
		form1.registrationCode.focus();
		return false;
	}
	if(form1.registrationCode.value	!=	"")
	{
		if(form1.registrationCode.value	!=	form1.existingCode.value)
		{
			alert("Please enter valid registration Code !!");
			form1.registrationCode.focus();
			return false;
		}
	}
	if(form1.mobile.value	==	"")
	{
		alert("Please enter mobile no !!");
		form1.mobile.focus();
		return false;
	}
	if(form1.bankName.value	==	"")
	{
		alert("Please enter bank name where your account is !!");
		form1.bankName.focus();
		return false;
	}
	if(form1.branchName.value	==	"")
	{
		alert("Please enter your bank branch name !!");
		form1.branchName.focus();
		return false;
	}
	if(form1.accountName.value	==	"")
	{
		alert("Please enter name in your account !!");
		form1.accountName.focus();
		return false;
	}
	if(form1.accountNumber.value	==	"" || form1.accountNumber.value	==	" " || form1.accountNumber.value	==	"0")
	{
		alert("Please enter your account number !!");
		form1.accountNumber.focus();
		return false;
	}
	if(form1.bankIFSCcode.value	==	"" || form1.bankIFSCcode.value	==	" " || form1.bankIFSCcode.value	==	"0")
	{
		alert("Please enter your bank IFSC code !!");
		form1.bankIFSCcode.focus();
		return false;
	}
	if(form1.panCardNumber.value	==	"" || form1.panCardNumber.value	==	" " || form1.panCardNumber.value	==	"0")
	{
		alert("Please enter your PAN number !!");
		form1.panCardNumber.focus();
		return false;
	}
	if(form1.aadhaarNumber.value	==	"" || form1.aadhaarNumber.value	==	" " || form1.aadhaarNumber.value	==	"0")
	{
		alert("Please enter your Aadhaar number !!");
		form1.aadhaarNumber.focus();
		return false;
	}
	if(form1.city.value	==	"")
	{
		alert("Please enter city !!");
		form1.city.focus();
		return false;
	}
	if(form1.state.value	==	"")
	{
		alert("Please enter state/province !!");
		form1.state.focus();
		return false;
	}
	if(form1.country.value	==	"")
	{
		alert("Please select country !!");
		form1.country.focus();
		return false;
	}
	if(form1.address.value	==	"")
	{
		alert("Please enter correspondence  address !!");
		form1.address.focus();
		return false;
	}
	if(form1.perAddress.value	==	"")
	{
		alert("Please enter permanent pddress !!");
		form1.perAddress.focus();
		return false;
	}
	if(form1.highestQualification.value	==	"0")
	{
		alert("Please select highest qualification !!");
		form1.highestQualification.focus();
		return false;
	}
	else if(form1.highestQualification.value	==	"6")
	{
		if(form1.otherQualification.value	==	"" || form1.otherQualification.value	==	"0")
		{
			alert("Please enter other qualification !!");
			form1.otherQualification.focus();
			return false;
		}
	}

	if(form1.qualificationStatus.value	==	"0")
	{
		alert("Please select qualification status !!");
		form1.qualificationStatus.focus();
		return false;
	}

	if(form1.qualificationStatus.value	==	"1" || form1.qualificationStatus.value	==	"3")
	{
		if(form1.passedOutOn.value == "0")
		{
			alert("Please select passed out !!");
			form1.passedOutOn.focus();
			return false;
		}
	}

	if(form1.boardUniversity.value	==	"" || form1.boardUniversity.value	==	"0")
	{
		alert("Please enter board/college/university !!");
		form1.boardUniversity.focus();
		return false;
	}
	
	/*if(form1.identityProof.value	==	"")
	{
		alert("Please upload scan copy of your identity proof !!");
		form1.identityProof.focus();
		return false;
	}
	if(form1.panCard.value	==	"")
	{
		alert("Please upload scan copy of pan card !!");
		form1.panCard.focus();
		return false;
	}*/
	if(form1.terms.checked == false)
	{
		alert("Please agree with our terms and conditions !!");
		form1.terms.focus();
		return false;
	}
	if(form1.securityCode.value == "")
	{
		alert("Please enter the sum of numbers !!");
		form1.securityCode.focus();
		return false;
	}
	else if(form1.securityCode.value != form1.inputsumOfTotal.value){
		alert("Please enter correct sum of numbers !!");
		form1.securityCode.focus();
		return false;
	}
	form1.submit.value    = "Please Wait";
	form1.submit.disabled = true;
	
}
function showHideQualification(flag)
{
	if(flag		==   6)
	{
		document.getElementById('showHideOtherQualification').style.display = 'inline';
	}
	else
	{
		document.getElementById('showHideOtherQualification').style.display = 'none';
	}
}
</script>

<form name="registration" action="" method="POST" enctype="multipart/form-data" onSubmit="return check();">
<table align='center' cellpadding="3" cellspacing="1" border="0" width="100%" style="border:0px solid #033A61">
<tr height="20">
	<td bgcolor="#EBEEF5" colspan="2"><font class="heading1">New Employee Registration Form</font>&nbsp;&nbsp;<font class="error">(Note: All fields marked with * are mandatory)</font></td>
</tr>
<tr height="10"><td></td></tr>
<tr>
	<!-- 1st table -->
	<td valign="top">
		<table width="100%" cellpadding="3" cellspacing="2" border="0" >
			<tr>
				<td colspan="3">
					<table width="100%" align="left" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td class='topHeading'>&nbsp;PERSONAL DETAILS</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="8"></td>
			</tr>
			<tr>
				<td class="text5" width="40%">&nbsp;&nbsp;&nbsp;First Name<font class="error">*</font></td>
				<td class="text5"  width="3%">:</td>
				<td>
					<input type="text" name="firstName" value="<?php echo $firstName;?>" class="textbox2" maxlength="40" size="40">
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Last Name<font class="error">*</font></td>
				<td class="text5">:</td>
				<td>
					<input type="text" name="lastName" value="<?php echo $lastName;?>" class="textbox2" maxlength="40" size="40">
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Referred By</td>
				<td class="text5">:</td>
				<td>
					<input type="text" name="referredBy" value="<?php echo $referredBy;?>" class="textbox2" maxlength="70" size="40">
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Gender</td>
				<td class="text5">:</td>
				<td class="smalltext5">
					<input type="radio" name="gender" value="M" <?php echo $check;?>><b>Male</b>&nbsp;&nbsp;
					<input type="radio" name="gender" value="F" <?php echo $check1;?>><b>Female</b>
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Date Of Birth<font class="error">*</font></td>
				<td class="text5">:</td>
				<td>
					<input type="text" name="dob" value="<?php echo $dob?>" class="textbox2" id="birthDate" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('birthDate','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Father's Name<font class="error">*</font></td>
				<td class="text5">:</td>
				<td>
					<input type="text" name="fatherName" value="<?php echo $fatherName;?>" class="textbox2" maxlength="80"  size="40">
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Email<font class="error">*</font></td>
				<td class="text5">:</td>
				<td>
					<input type="text" name="email" value="<?php echo $email;?>" class="textbox2" size="40" maxlength="100">
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Alternate Email</td>
				<td class="text5">:</td>
				<td>
					<input type="text" name="altEmail" value="<?php echo $altEmail;?>" class="textbox2" size="40" maxlength="100">
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Password For Login<font class="error">*</font></td>
				<td class="text5">:</td>
				<td>
					<input type="password" name="password" value="" class="textbox2" size="10" maxlength="20">
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Re-Type Password<font class="error">*</font></td>
				<td class="text5">:</td>
				<td>
					<input type="password" name="rePassword" value="" class="textbox2" size="10" maxlength="20">
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Registration Code<font class="error">*</font></td>
				<td class="text5">:</td>
				<td>
					<input type="text" name="registrationCode" value="<?php echo $registrationCode;?>" class="textbox2" size="40" maxlength="100">
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Fixed Line Phone No</td>
				<td class="text5">:</td>
				<td>
					<input type="text" name="phone" value="<?php echo $phone;?>" class="textbox2" maxlength="20" size="40" onKeyPress="return checkForNumber();">
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp;Mobile No<font class="error">*</font></td>
				<td class="text5" valign="top">:</td>
				<td class="smalltext2" valign="top">
					+91-<input type="text" name="mobile" value="<?php echo $mobile;?>" class="textbox2" maxlength="10" size="36" onKeyPress="return checkForNumber();"><br />[Send SMS messages on this number.]
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<table width="100%" align="left" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td class='topHeading'>&nbsp;BANK DETAILS</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="8"></td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp; Bank Name<font class="error">*</font></td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<input type="text" name="bankName" value="<?php echo $bankName;?>" class="textbox2" maxlength="200" size="40">
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp; Branch Name<font class="error">*</font></td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<input type="text" name="branchName" value="<?php echo $branchName;?>" class="textbox2" maxlength="200" size="40">
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp; Account Holder's Name<font class="error">*</font></td>
				<td class="text5" valign="top">:</td>
				<td valign="top" class="smalltext5">
					<input type="text" name="accountName" value="<?php echo $accountName;?>" class="textbox2" maxlength="70" size="40">
					<br>
					(As In Bank statement)
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp; Account Number<font class="error">*</font></td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<input type="text" name="accountNumber" value="<?php echo $accountNumber;?>" class="textbox2" maxlength="20" size="40" onKeyPress="return checkForNumber();">
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp; IFSC code<font class="error">*</font></td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<input type="text" name="bankIFSCcode" value="<?php echo $bankIFSCcode;?>" class="textbox2" style="text-transform:uppercase;" onKeyPress="return checkForCharcNumber();" maxlength="100" size="20">
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp; PAN Card Number<font class="error">*</font></td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<input type="text" name="panCardNumber" value="<?php echo $panCardNumber;?>" style="text-transform:uppercase;" onKeyPress="return checkForCharcNumber();" class="textbox2" maxlength="12" size="20">
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp; Aadhaar Number<font class="error">*</font></td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<input type="text" name="aadhaarNumber" value="<?php echo $aadhaarNumber;?>" class="textbox2" maxlength="12" size="20" onKeyPress="return checkForNumber();">
				</td>
			</tr>
		</table>
	</td>
	<!-- 2nd table -->
	<td valign='top'>
		<table width="100%" cellpadding="3" cellspacing="2" border="0">
			<tr>
				<td colspan="3">
					<table width="100%" align="left" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td class='topHeading'>&nbsp;ADDRESS DETAILS</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="8"></td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;City<font class="error">*</font></td>
				<td class="text5">:</td>
				<td>
					<input type="text" name="city" value="<?php echo $city;?>" class="textbox2" maxlength="200" size="40">
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;State/Province<font class="error">*</font></td>
				<td class="text5">:</td>
				<td>
					<input type="text" name="state" value="<?php echo $state;?>" class="textbox2" maxlength="200" size="40">
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Country<font class="error">*</font></td>
				<td class="text5">:</td>
				<td>
					<select name="country" class="textbox2">
					<?php
						foreach($a_countries as $key=>$value)
						{
							$select	=	"";
							if($key	==	$country)
							{
								$select	=	"selected";
							}

							echo "<option value='$key' $select>$value</option>";
						}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top" width="45%">&nbsp;&nbsp;&nbsp;Correspondence Address<font class="error">*</font></td>
				<td class="text5" valign="top" width="3%">:</td>
				<td valign="top">
					<textarea name="address" cols="38" rows="3" class="textarea"><?php echo $address;?></textarea>
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp; Permanent Address<font class="error">*</font></td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<textarea name="perAddress" cols="38" rows="3" class="textarea"><?php echo $perAddress;?></textarea>
				</td>
			</tr>
			<!-- <tr>
				<td class="smalltext1" colspan="3">
					<?php
						foreach($a_identityProof as $key=>$value)
						{
							$checked	=	"";
							if($identityProofType	==	$key)
							{
								$checked	=	"checked";
							}
							echo "<input type='radio' name='identityProofType' value='$key' $checked><b>$value</b>";
						}
					?>
				</td>
			</tr> -->
			<tr>
				<td colspan="3">
					<table width="100%" align="left" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td class='topHeading'>&nbsp;REQUIRED DOCUMENTS</td>
						</tr>
					</table>
				</td>
			</tr>
		
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Upload Photo ID Proof (Any Govt ID)</td>
				<td class="text5">:</td>
				<td class="smalltext5">
					<input type='file' name='identityProof'>
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Upload PAN Card</td>
				<td class="text5">:</td>
				<td class="smalltext5">
					<input type='file' name='panCard'>
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;HIPAA Compliance (MT employees)</td>
				<td class="text5">:</td>
				<td class="smalltext5">
					<input type='file' name='complianceForm'>
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp;Upload Resume</td>
				<td class="text5" valign="top">:</td>
				<td class="smalltext5" valign="top">
					<input type='file' name='resume'>
					<br>
					[Only Upload .doc, .docx file]
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp;Updated Residence Proof</td>
				<td class="text5" valign="top">:</td>
				<td class="smalltext5" valign="top">
					<input type='file' name='residenceProof'>
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp;ieIMPACT Nondisclosure Agreement</td>
				<td class="text5" valign="top">:</td>
				<td class="smalltext5" valign="top">
					<input type='file' name='ieimpactAgreement'>
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp;ieImpact Signed Appointment Letter</td>
				<td class="text5" valign="top">:</td>
				<td class="smalltext5" valign="top">
					<input type='file' name='ieimpactAppoinmentLetter'>
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp;ieImpact Employee Agreement</td>
				<td class="text5" valign="top">:</td>
				<td class="smalltext5" valign="top">
					<input type='file' name='ieimpactEmployeeAgreement'>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<table width="100%" align="left" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td class='topHeading'>&nbsp;EDUCATION DETAILS</td>
						</tr>
					</table>
				</td>
			</tr>
		
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp;Highest Qualification<font class="error">*</font></td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<select name="highestQualification" onchange="showHideQualification(this.value);"  class="textbox2">
						<option value="0">Select</option>
						<?php
							foreach($a_employeeHighestQualifications as $k=>$value)
							{
								$select		=	"";
								if($k		==	$highestQualification)
								{
									$select	=	"selected";
								}

								echo "<option value='$k' $select>$value</option>";
							}
						?>
					</select><br>
					<div id="showOthers">
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<div id="showHideOtherQualification" style="display:<?php echo $showHideExtraQualifications;?>">
						<table width="100%" cellpadding="3" cellspacing="2" border="0" >
							<tr>
								<td class="text5" width="45%">&nbsp;OTHER QUALIFICATION<font class="error">*</font></td>
								<td class="text5"  width="3%">:</td>
								<td>
									<input type="text" name="otherQualification" value="<?php echo $otherQualification;?>" class="textbox2" maxlength="120" size="40">
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Qualification Status<font class="error">*</font></td>
				<td class="text5">:</td>
				<td>
					<select name="qualificationStatus" class="textbox2">
						<?php
							foreach($a_employeeQualificationsStatus as $k1=>$v1)
							{
								$select		=	"";
								if($k1		==	$qualificationStatus)
								{
									$select	=	"selected";
								}

								echo "<option value='$k1' $select>$v1</option>";
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Passes Out On</td>
				<td class="text5">:</td>
				<td>
					<select name="passedOutOn" class="textbox2">
					<option value="0">Select</option>
						<?php
							$calculateFrom	=	date("Y")-30;
							$calculateTo	=	date("Y");
							for($i=$calculateFrom;$i<=$calculateTo;$i++)
							{
								$select		=	"";
								if($i		==	$passedOutOn)
								{
									$select	=	"selected";
								}

								echo "<option value='$i' $select>$i</option>";
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Board/College/University<font class="error">*</font></td>
				<td class="text5">:</td>
				<td>
					<input type="text" name="boardUniversity" value="<?php echo $boardUniversity;?>" class="textbox2" maxlength="150" size="40">
				</td>
			</tr>
			<!--<tr>
				<td colspan="3">
					<table width="100%" align="left" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td class='topHeading'>&nbsp;FINANCIAL INVESTMENT RECEIPT</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="8"></td>
			</tr>
			<tr>
				<td colspan="3" align="left">
					<table width="100%" cellpadding="3" cellspacing="2" border="0">
						<tr>
							<td class="text5" width="47%">&nbsp;Savings for Tax purposes under section 80CC</td>
							<td class="text5">&nbsp;Upload Investment Receipt File</td>
						</tr>
						<tr>
							<td class="text5">
								<input type="text" name="investmentOn[]" value="" class="textbox2" maxlength="200" size="50">
							</td>
							<td>
								<input type="file" name="investmentFile[]">
							</td>
						</tr>
						<tr>
							<td colspan="3">
								<table border="0">
									<tbody id="table1"></tbody>
									<tr><td align="left"><a href="javascript:addMore();" class='link_style3'>+Add More</a></td></tr>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan="3" class="smalltext5">
								[Please upload your receipts for the current financial year for rebate under section 80CC]
							</td>
						</tr>
					</table>
				</td>
			</tr>-->
		</table>
	</td>
</tr>
<tr>
	<td colspan="2" class="text5" valign="bottom">
		<table width="100%" cellpadding="3" cellspacing="2" border="0">			
			<tr>
				<td colspan="2" class="text5">
					I want to join as <input type="radio" name="enrollAs" value="mt" checked><b>MT Employee</b>  or <input type="radio" name="enrollAs" value="pdf"><b>Data Entry Employee</b>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="text5">
					Employee Type - <input type="radio" name="onsiteOffsite" value="0" checked><b>Onsite</b> Or <input type="radio" name="onsiteOffsite" value="1"><b>Offsite</b>
				</td>
			</tr>
			<tr>
				<td width="3">
					<input class="textbox2" type="checkbox" name="terms">
				</td>
				<td class="text5">
					I completely agree with terms & conditions of ieIMPACT Microsystems Pvt Ltd.
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr height="10"><td></td></tr>
<tr>
	<td class="text5" colspan="3">
		Enter the  Sum of below given numbers
	</td>
</tr>
<tr>
	<td colspan="3">
		<table width="100%" align="center" border="0">
			<tr>
				<td width="7%" class="smalltext2">
					<b><?php echo $number1." + ".$number2." = ";?></b></b>
				</td>
				<td>
					&nbsp;&nbsp;<input type="text"  name="securityCode" size="5" value="" maxlength="3" onkeypress="return checkForNumber();"><input type="hidden"  name="inputsumOfTotal" value="<?php echo $numberResult;?>">
				</td>
			</tr>
		</table>
	</td>
</tr>
<!--<tr>
	<td class="text5" colspan="3">
		Enter the  Verification Code As Shown
	</td>
</tr>
<tr>
	<td colspan="3">
		<table width="100%" align="center" border="0">
			<tr>
				<td id="captchaImage" width="15%">
					<img src="<?php echo SITE_URL ."/classes/captcha.php";?>">
				</td>
				<td class="smalltext5">
					&nbsp;&nbsp;<input type="text"  name="securityCode" size="18" value="" maxlength="6">
					<br>&nbsp;&nbsp;Characters are case sensitive.
				</td>
			</tr>
		</table>
	</td>
</tr>-->
<tr height="20"><td></td></tr>
<tr>
	<td colspan="4" class="text5">
		<!-- <input type="Submit" name="Submit" value="Submit" class="textbox2">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="Reset" name="Reset" value="Reset" class="textbox2">
		<input type="hidden" name="formSubmitted" value="1">
		<input type="hidden" name="existingCode" value="<?php echo EMP_REGISTRATION_CODE;?>"> -->
		[<b>Currenly we are updating our system, Please visit after couple of hours</b>]
	</td>
</tr>
<tr height="10"><td></td></tr>
</table>
</form>