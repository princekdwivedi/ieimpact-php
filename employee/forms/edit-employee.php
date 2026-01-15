<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/validate.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>
<script type="text/javascript">
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

var count  = "<?php echo $totalInvesmentAvailable;?>";
var count1 = 1;
function addMore()
{
	var tr1        = document.createElement('tr');
	var td2        = document.createElement('td');	
	var txt2       = document.createElement('input');
	txt2.type      = "text";
	txt2.name      = "investmentOn[]";
	txt2.size      = "49";
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

function checkValid()
{
	//return;
	form1	=	document.editEmployee
	if(form1.firstName.value	==	"")
	{
		alert("Please enter your first name !!");
		form1.firstName.focus();
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
	if(form1.accountNumber.value	==	"")
	{
		alert("Please enter your account number !!");
		form1.accountNumber.focus();
		return false;
	}
	if(form1.bankIFSCcode.value	==	"")
	{
		alert("Please enter your bank IFSC code !!");
		form1.bankIFSCcode.focus();
		return false;
	}
	if(form1.panCardNumber.value	==	"")
	{
		alert("Please enter your PAN number !!");
		form1.panCardNumber.focus();
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
	form1.submit.value    = "Please Wait While Updating Records";
	form1.submit.disabled = true;
	
}
function deleteInvesment(ID,page)
{
	var confirmation = window.confirm("Are You Sure To Delete This Invesment File?");
	if(confirmation==true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/"+page+"investmentID="+ID+"&isDeleteInvestment=1";
	}
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
function addEmployeeBlankCheque(employeeeId, type)
{
	path			=	"<?php echo SITE_URL_EMPLOYEES;?>/add-blank-cheque.php?employeeId="+employeeeId+"&type="+type;
	properties	=	"height=360,width=540,top=120,left=250,scrollbars=yes,top=100,left=200";
	it			=	window.open(path,'',properties);
}

function deleteFiles(type)
{
	var confirmation = window.confirm("Are You Sure To Delete This File?");
	if(confirmation==true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/edit-details.php?deleteFileType="+type;
	}
}


</script>

<form name="editEmployee" action="" method="POST" enctype="multipart/form-data" onSubmit="return checkValid();">
<table align='center' cellpadding="3" cellspacing="1" border="0" width="100%" style="border:0px solid #033A61">
<tr height="10"><td></td></tr>
<tr>
	<!-- 1st table -->
	<td valign="top" width="42%">
		<table width="100%" cellpadding="3" cellspacing="2" border="0" style="border:1px solid #033A61">
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
				<td class="text5" width="42%">&nbsp;&nbsp;&nbsp;First Name</td>
				<td class="text5"  width="3%">:</td>
				<td>
					<input type="text" name="firstName" value="<?php echo $firstName;?>" class="textbox2" maxlength="40" size="35">
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Last Name</td>
				<td class="text5">:</td>
				<td>
					<input type="text" name="lastName" value="<?php echo $lastName;?>" class="textbox2" maxlength="40" size="35">
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Gender</td>
				<td class="text5">:</td>
				<td class="smalltext2">
					<input type="radio" name="gender" value="M" <?php echo $check;?>><b>M</b>&nbsp;&nbsp;
					<input type="radio" name="gender" value="F" <?php echo $check1;?>><b>F</b>
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Date Of Birth</td>
				<td class="text5">:</td>
				<td>
					<input type="text" name="dob" value="<?php echo $dob?>" class="textbox2" id="birthDate" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('birthDate','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Father's Name</td>
				<td class="text5">:</td>
				<td>
					<input type="text" name="fatherName" value="<?php echo $fatherName;?>" class="textbox2" maxlength="80"  size="35">
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Email</td>
				<td class="text5">:</td>
				<td>
					<input type="text" name="email" value="<?php echo $email;?>" class="textbox2" size="35" maxlength="100">
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<table width="100%" align="left" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td class='topHeading'>&nbsp;COMMUNICATION DETAILS</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp;Mobile No</td>
				<td class="text5" valign="top">:</td>
				<td valign="top" class="smalltext2">
					+91-<input type="text" name="mobile" value="<?php echo $mobile;?>" class="textbox2" maxlength="10" size="31" onKeyPress="return checkForNumber();">
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;City</td>
				<td class="text5">:</td>
				<td>
					<input type="text" name="city" value="<?php echo $city;?>" class="textbox2" maxlength="200" size="35">
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;State/Province</td>
				<td class="text5">:</td>
				<td>
					<input type="text" name="state" value="<?php echo $state;?>" class="textbox2" maxlength="200" size="35">
				</td>
			</tr>
			<tr>
				<td class="text5">&nbsp;&nbsp;&nbsp;Country</td>
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
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp;Correspondence Address</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<textarea name="address" cols="32" rows="5" class="textbox2"><?php echo $address;?></textarea>
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp; Permanent Address</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<textarea name="perAddress" cols="32" rows="5" class="textbox2"><?php echo $perAddress;?></textarea>
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
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp; Bank Name</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<input type="text" name="bankName" value="<?php echo $bankName;?>" class="textbox2" maxlength="200" size="35">
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp; Branch Name</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<input type="text" name="branchName" value="<?php echo $branchName;?>" class="textbox2" maxlength="200" size="35">
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp; Account Holder's Name</td>
				<td class="text5" valign="top">:</td>
				<td valign="top" class="smalltext5">
					<input type="text" name="accountName" value="<?php echo $accountName;?>" class="textbox2" maxlength="70" size="35">
					<br>
					(As In Bank statement)
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp; Account Number</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<input type="text" name="accountNumber" value="<?php echo $accountNumber;?>" class="textbox2" maxlength="20" size="35" onKeyPress="return checkForNumber();">
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp; IFSC code</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<input type="text" name="bankIFSCcode" value="<?php echo $bankIFSCcode;?>" class="textbox2" style="text-transform:uppercase;" onKeyPress="return checkForCharcNumber();" maxlength="100" size="20">
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp; PAN Card Number</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<input type="text" name="panCardNumber" value="<?php echo $panCardNumber;?>" style="text-transform:uppercase;" onKeyPress="return checkForCharcNumber();" class="textbox2" maxlength="12" size="20">
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp; Aadhaar Number</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<input type="text" name="aadhaarNumber" value="<?php echo $aadhaarNumber;?>" class="textbox2" maxlength="12" size="20" onKeyPress="return checkForNumber();">
				</td>
			</tr>
			
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp; Blank Crossed Cheque</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<?php
						if(!empty($hasCancelledCheque)){

							if($showHideBrowseOption == true){
								echo "<font class='title3'>Yes - </font>&nbsp;&nbsp;<a href='".SITE_URL_EMPLOYEES."/dowanload-uploadings.php?T=CRQ&I=$downloadingID' class='link_style2'>Download Identity Proof</a>";
							}
							else{
								echo "<font class='title3'>Already Added</font>";
							}
							
						}
						else{
							echo "<a onClick=\"addEmployeeBlankCheque('$employeeId', 1);\" / class='link_style2' style='cursor:pointer;'>Add Cross Cheque</a>";
						}
					?>
				</td>
			</tr>
			
			<tr height="10"><td></td></tr>
		</table>
	</td>
	<td valign='top'>
		<table width="100%" cellpadding="3" cellspacing="2" border="0" style="border:1px solid #033A61">
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
				<td class="text5" valign="top" width="45%">&nbsp;&nbsp;Highest Qualification</td>
				<td class="text5" valign="top" width="3%">:</td>
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
								<td class="text5" width="45%">&nbsp;OTHER QUALIFICATION</td>
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
				<td class="text5">&nbsp;&nbsp;&nbsp;Qualification Status</td>
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
				<td class="text5">&nbsp;&nbsp;&nbsp;Board/College/University</td>
				<td class="text5">:</td>
				<td>
					<input type="text" name="boardUniversity" value="<?php echo $boardUniversity;?>" class="textbox2" maxlength="150" size="40">
				</td>
			</tr>
		</table>
		<br>
		<table width="100%" cellpadding="5" cellspacing="2" border="0" style="border:1px solid #033A61">
			<tr>
				<td colspan="3">
					<table width="100%" align="left" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td class='topHeading'>&nbsp;EDIT DOCUMENTS</td>
						</tr>
					</table>
				</td>
			</tr>
			<?php
				if(empty($hasIdentityProof) || $showHideBrowseOption == true)
				{
			?>
			<tr>
				<td class="text5" width="45%">&nbsp;&nbsp;Upload Photo ID Proof (Any Govt ID)</td>
				<td class="text5" width="3%">:</td>
				<td>
					<input type='file' name='identityProof'>
				</td>
			</tr>
			<?php
				}	
			?>
			<tr>
				<td class="text5" width="35%">&nbsp;&nbsp;Existing Photo ID Proof</td>
				<td width="3%">:</td>
				<td>
					<?php 
						if(!empty($hasIdentityProof))
						{
							if($showHideBrowseOption == true){
								echo "<font class='title3'>Yes - </font>&nbsp;&nbsp;<a href='".SITE_URL_EMPLOYEES."/dowanload-uploadings.php?T=I&I=$downloadingID' class='link_style2'>Download Identity Proof</a>";
							}
							else{
								echo "<font class='title3'>Yes</font>";
							}
						}
						else
						{
							echo "<font class='error'><b>NO</b></font>";
						}
					?>
				</td>
			</tr>
			<?php 
				if(empty($hasPanCardProof) || $showHideBrowseOption == true)
				{
			?>
			<tr>
				<td class="text5">&nbsp;&nbsp;Upload PAN Card (Scan)</td>
				<td class="text5">:</td>
				<td>
					<input type='file' name='panCard'>
				</td>
			</tr>
			<?php
				}	
			?>
			<tr>
				<td class="text5">&nbsp;&nbsp;Existing PAN Card</td>
				<td width="3%">:</td>
				<td>
					<?php 
						if(!empty($hasPanCardProof))
						{
							if($showHideBrowseOption == true){
								echo "<font class='title3'>Yes - </font>&nbsp;&nbsp;<a href='".SITE_URL_EMPLOYEES."/dowanload-uploadings.php?T=P&I=$downloadingID' class='link_style2'>Download PAN Card</a>";
							}
							else{
								echo "<font class='title3'>Yes</font>";
							}
						}
						else
						{
							echo "<font class='error'><b>NO</b></font>";
						}
					?>
				</td>
			</tr>
			<?php
				if(empty($hasComplianceForm) || $showHideBrowseOption == true)
				{
			?>
			<tr>
				<td class="text5">&nbsp;&nbsp;HIPPA Compliance Form</td>
				<td class="text5">:</td>
				<td>
					<input type='file' name='complianceForm'>
				</td>
			</tr>
			<?php
				}	
			?>
			<tr>
				<td class="text5">&nbsp;&nbsp;Existing HIPPA Compliance Form</td>
				<td width="3%">:</td>
				<td>
					<?php 
						if(!empty($hasComplianceForm))
						{
							if($showHideBrowseOption == true){
								echo "<font class='title3'>Yes - </font>&nbsp;&nbsp;<a href='".SITE_URL_EMPLOYEES."/dowanload-uploadings.php?T=C&I=$downloadingID' class='link_style2'>Download Compliance Form</a>";
							}
							else{
								echo "<font class='title3'>Yes</font>";
							}
						}
						else
						{
							echo "<font class='error'><b>NO</b></font>";
						}
					?>
				</td>
			</tr>
			<?php
				if(empty($hasResume) || $showHideBrowseOption == true)
				{
			?>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;Upload Resume</td>
				<td class="text5" valign="top">:</td>
				<td class="smalltext2" valign="top">
					<input type='file' name='resume'>
					<br>
					[Only Upload .doc, .docx file]
				</td>
			</tr>
			<?php
				}	
			?>
			<tr>
				<td class="text5">&nbsp;&nbsp;Existing Resume</td>
				<td>:</td>
				<td>
					<?php 
						if(!empty($hasResume))
						{
							if($showHideBrowseOption == true){
								echo "<font class='title3'>Yes - </font>&nbsp;&nbsp;<a href='".SITE_URL_EMPLOYEES."/dowanload-uploadings.php?T=R&I=$downloadingID' class='link_style2'>Download Resume</a>";
							}
							else{
								echo "<font class='title3'>Yes</font>";
							}
						}
						else
						{
							echo "<font class='error'><b>NO</b></font>";
						}
					?>
				</td>
			</tr>
			<?php
				if(empty($hasResidenceProof) || $showHideBrowseOption == true)
				{
			?>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;Updated Residence Proof</td>
				<td class="text5" valign="top">:</td>
				<td class="smalltext5" valign="top">
					<input type='file' name='residenceProof'>
				</td>
			</tr>
			<?php
				}	
			?>
			<tr>
				<td class="text5">&nbsp;&nbsp;Existing Residence Proof</td>
				<td width="3%">:</td>
				<td>
					<?php 
						if(!empty($hasResidenceProof))
						{
							if($showHideBrowseOption == true){
								echo "<font class='title3'>Yes - </font>&nbsp;&nbsp;<a href='".SITE_URL_EMPLOYEES."/dowanload-uploadings.php?T=RP&I=$downloadingID' class='link_style2'>Download Residence Proof</a>";
							}
							else{
								echo "<font class='title3'>Yes";
							}
						}
						else
						{
							echo "<font class='error'><b>NO</b></font>";
						}
					?>
				</td>
			</tr>
			<?php
				if(empty($hasAgreement) || $showHideBrowseOption == true)
				{
			?>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;ieIMPACT Nondisclosure Agreement</td>
				<td class="text5" valign="top">:</td>
				<td class="smalltext5" valign="top">
					<input type='file' name='ieimpactAgreement'>
				</td>
			</tr>
			<?php
				}	
			?>
			<tr>
				<td class="text5">&nbsp;&nbsp;Existing Nondisclosure Agreement</td>
				<td width="3%">:</td>
				<td>
					<?php 
						if(!empty($hasAgreement))
						{
							if($showHideBrowseOption == true){
								echo "<font class='title3'>Yes - </font>&nbsp;&nbsp;<a href='".SITE_URL_EMPLOYEES."/dowanload-uploadings.php?T=IA&I=$downloadingID' class='link_style2'>Download Nondisclosure Agreement</a>";
							}
							else{
								echo "<font class='title3'>Yes";
							}
						}
						else
						{
							echo "<font class='error'><b>NO</b></font>";
						}
					?>
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;Form 11</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<?php
						echo "<a onClick=\"addEmployeeBlankCheque('$employeeId', 2);\" / class='link_style2' style='cursor:pointer;'>Add Form 11</a><br />";
						if(!empty($hasFormEleven)){

							echo "<font class='title3'></font><a href='".SITE_URL_EMPLOYEES."/dowanload-uploadings.php?T=ELEVEN&I=$downloadingID' class='link_style2'>Download Existing Form 11</a>&nbsp;(<a onClick=\"deleteFiles(2);\" / class='link_style4' style='cursor:pointer;'>Delete</a>)";
							
						}
						
					?>
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;Form 11 Revised</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<?php
						echo "<a onClick=\"addEmployeeBlankCheque('$employeeId', 4);\" / class='link_style2' style='cursor:pointer;'>Add Form 11 Revised</a><br />";
						if(!empty($hasFormElevenRevised)){
							
							echo "<font class='title3'></font><a href='".SITE_URL_EMPLOYEES."/dowanload-uploadings.php?T=ELEVENRES&I=$downloadingID' class='link_style2'>Download Existing Form 11 Revised</a>&nbsp;(<a onClick=\"deleteFiles(4);\" / class='link_style4' style='cursor:pointer;'>Delete</a>)";
						}
						
					?>
				</td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;Resignation</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<?php
					   echo "<a onClick=\"addEmployeeBlankCheque('$employeeId', 3);\" / class='link_style2' style='cursor:pointer;'>Add Resignation</a><br />";
						if(!empty($hasResignedFile)){

							
								echo "<font class='title3'></font><a href='".SITE_URL_EMPLOYEES."/dowanload-uploadings.php?T=RESIGNED&I=$downloadingID' class='link_style2'>Download Existing Resignation</a>&nbsp;(<a onClick=\"deleteFiles(3);\" / class='link_style4' style='cursor:pointer;'>Delete</a>)";
							
							
						}
						
					?>
				</td>
			</tr>
			<?php
				if(empty($hasAppointmentLetter) || $showHideBrowseOption == true)
				{
			?>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp;ieImpact Signed Appointment Letter</td>
				<td class="text5" valign="top">:</td>
				<td class="smalltext5" valign="top">
					<input type='file' name='ieimpactAppoinmentLetter'>
				</td>
			</tr>
			<?php
				}	
			?>
			<tr>
				<td class="text5">&nbsp;&nbsp;Existing Appointment Letter</td>
				<td width="3%">:</td>
				<td>
					<?php 
						if(!empty($hasAppointmentLetter))
						{
							if($showHideBrowseOption == true){
								echo "<font class='title3'>Yes - </font>&nbsp;&nbsp;<a href='".SITE_URL_EMPLOYEES."/dowanload-uploadings.php?T=IAL&I=$downloadingID' class='link_style2'>Download Appointment Letter</a>";
							}
							else{
								echo "<font class='title3'>Yes</font>";
							}
						}
						else
						{
							echo "<font class='error'><b>NO</b></font>";
						}
					?>
				</td>
			</tr>
			<?php
				if(empty($hasEmployeeAgreement) || $showHideBrowseOption == true)
				{
			?>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp;ieImpact Employee Agreement</td>
				<td class="text5" valign="top">:</td>
				<td class="smalltext5" valign="top">
					<input type='file' name='ieimpactEmployeeAgreement'>
				</td>
			</tr>
			<?php
				}	
			?>
			<tr>
				<td class="text5">&nbsp;&nbsp;Existing Employee Agreement</td>
				<td>:</td>
				<td>
					<?php 
						if(!empty($hasEmployeeAgreement))
						{
							if($showHideBrowseOption == true){
								echo "<font class='title3'>Yes - </font>&nbsp;&nbsp;<a href='".SITE_URL_EMPLOYEES."/dowanload-uploadings.php?T=IEA&I=$downloadingID' class='link_style2'>Download Employee Agreement</a>";
							}
							else{
								echo "<font class='title3'>Yes";
							}
						}
						else
						{
							echo "<font class='error'><b>NO</b></font>";
						}
					?>
				</td>
			</tr>
			<?php
				if($showHideBrowseOption == true){
			?>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td class="smalltext5">[<font color="red">*Note</font>: Use Mozilla Browser To Download Files]</td>
			</tr>
			<?php
				}	
			?>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
		</table>
	</td>
</tr>
<tr height="10"><td></td></tr>
<tr>
	<td colspan="3">
		<?php
			if($showHideBrowseOption	== true){

				
		?>
		
		<input type="submit" name="submit" value="EDIT RECORDS">
		&nbsp;&nbsp;
		<input type="button" name="back" onClick="history.back()" value="BACK">
		<input type="hidden" name="formSubmitted" value="1">
		<input type="hidden" name="ID" value="<?php echo $employeeId?>">
		<?php
				
			}
			else{
				echo "<font class=''smalltext2>[If You Need To Change Anything Please Request HR/Manager]</font>";
			}
		?>
	</td>
</tr>
</table>
</form>