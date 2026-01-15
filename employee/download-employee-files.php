<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES	    . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES     . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES     . "/classes/employee.php");
	include(SITE_ROOT				. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		. "/includes/common-array.php");
	$employeeId						= 0;
	$employeeObj				    =	new employee();

	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	if(isset($_GET['ID']))
	{
		$employeeId					=	$_GET['ID'];
		$query						=	"SELECT fullName,hasIdentityProof,hasPanCardProof,hasComplianceForm,hasResume,hasResidenceProof,hasAgreement,hasAppointmentLetter,hasEmployeeAgreement,hasCancelledCheque,hasFormEleven,hasResignedFile,hasFormElevenRevised FROM employee_details WHERE employeeId=$employeeId AND isActive=1";
		$result						=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$row					=	mysqli_fetch_assoc($result);
			$fullName				=	stripslashes($row['fullName']);
			$downloadingID	        =	$employeeId;

			$hasIdentityProof		=	$row['hasIdentityProof'];
			$hasPanCardProof		=	$row['hasPanCardProof'];
			$hasComplianceForm		=	$row['hasComplianceForm'];
			$hasResume				=	$row['hasResume'];
			$hasResidenceProof		=	$row['hasResidenceProof'];
			$hasAgreement			=	$row['hasAgreement'];
			$hasAppointmentLetter	=	$row['hasAppointmentLetter'];
			$hasEmployeeAgreement	=	$row['hasEmployeeAgreement'];			
			$hasCancelledCheque  	=	$row['hasCancelledCheque'];
			$hasFormEleven      	=	$row['hasFormEleven'];
			$hasResignedFile       	=	$row['hasResignedFile'];
			$hasFormElevenRevised  	=	$row['hasFormElevenRevised'];
		}
	}
?>
<html>
<head>
<TITLE></TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
<script type="text/javascript">
	function downloadPageLink(path)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/"+path;
	}
</script>
</head>
	<body>		
		<table cellpadding="3" cellspacing="2" width="98%" border="0" align="center">
			<tr>
				<td colspan="3" class="smalltext23"><b>Download Uploaded Files For - <?php echo $fullName;?></b></td>
			</tr>
			<tr>
				<td class="text5" valign="top" width="40%">&nbsp;&nbsp;&nbsp;Photo ID Proof</td>
				<td class="text5" valign="top" width="3%">:</td>
				<td valign="top">
					<?php
						if(!empty($hasIdentityProof)){
							echo "<a onClick=\"downloadPageLink('dowanload-uploadings.php?T=I&I=$downloadingID');\" / class='link_style2' style='cursor:pointer;'>Download</a>";
						}
						else{
							echo "<font class='error'><b>N/A</b></font>";
						}
					?>
				</td>
			</tr>
			<tr>
				<td height='3'></td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp;Pan Card</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<?php
						if(!empty($hasPanCardProof)){
							echo "<a onClick=\"downloadPageLink('dowanload-uploadings.php?T=P&I=$downloadingID');\" / class='link_style2' style='cursor:pointer;'>Download</a>";
						}
						else{
							echo "<font class='error'><b>N/A</b></font>";
						}
					?>
				</td>
			</tr>
			<tr>
				<td height='3'></td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp;HIPPA Compliance Form</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<?php
						if(!empty($hasComplianceForm)){
							echo "<a onClick=\"downloadPageLink('dowanload-uploadings.php?T=C&I=$downloadingID');\" / class='link_style2' style='cursor:pointer;'>Download</a>";
						}
						else{
							echo "<font class='error'><b>N/A</b></font>";
						}
					?>
				</td>
			</tr>
			<tr>
				<td height='3'></td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp;Residence Proof</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<?php
						if(!empty($hasResidenceProof)){
							echo "<a onClick=\"downloadPageLink('dowanload-uploadings.php?T=RP&I=$downloadingID');\" / class='link_style2' style='cursor:pointer;'>Download</a>";
						}
						else{
							echo "<font class='error'><b>N/A</b></font>";
						}
					?>
				</td>
			</tr>
			<tr>
				<td height='3'></td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp;Nondisclosure Agreement</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<?php
						if(!empty($hasAgreement)){
							echo "<a onClick=\"downloadPageLink('dowanload-uploadings.php?T=IA&I=$downloadingID');\" / class='link_style2' style='cursor:pointer;'>Download</a>";
						}
						else{
							echo "<font class='error'><b>N/A</b></font>";
						}
					?>
				</td>
			</tr>
			<tr>
				<td height='3'></td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp;Appointment Letter</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<?php
						if(!empty($hasAppointmentLetter)){
							echo "<a onClick=\"downloadPageLink('dowanload-uploadings.php?T=IAL&I=$downloadingID');\" / class='link_style2' style='cursor:pointer;'>Download</a>";
						}
						else{
							echo "<font class='error'><b>N/A</b></font>";
						}
					?>
				</td>
			</tr>
			<tr>
				<td height='3'></td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp;Employee Agreement</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<?php
						if(!empty($hasEmployeeAgreement)){
							echo "<a onClick=\"downloadPageLink('dowanload-uploadings.php?T=IEA&I=$downloadingID');\" / class='link_style2' style='cursor:pointer;'>Download</a>";
						}
						else{
							echo "<font class='error'><b>N/A</b></font>";
						}
					?>
				</td>
			</tr>
			<tr>
				<td height='3'></td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp;Crossed Cheque</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<?php
						if(!empty($hasCancelledCheque)){
							echo "<a onClick=\"downloadPageLink('dowanload-uploadings.php?T=CRQ&I=$downloadingID');\" / class='link_style2' style='cursor:pointer;'>Download</a>";
						}
						else{
							echo "<font class='error'><b>N/A</b></font>";
						}
					?>
				</td>
			</tr>
			<tr>
				<td height='3'></td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp;Form 11</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<?php
						if(!empty($hasFormEleven)){
							echo "<a onClick=\"downloadPageLink('dowanload-uploadings.php?T=ELEVEN&I=$downloadingID');\" / class='link_style2' style='cursor:pointer;'>Download</a>";
						}
						else{
							echo "<font class='error'><b>N/A</b></font>";
						}
					?>
				</td>
			</tr>
			<tr>
				<td height='3'></td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp;Form 11 REVISED</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<?php
						if(!empty($hasFormElevenRevised)){
							echo "<a onClick=\"downloadPageLink('dowanload-uploadings.php?T=ELEVENRES&I=$downloadingID');\" / class='link_style2' style='cursor:pointer;'>Download</a>";
						}
						else{
							echo "<font class='error'><b>N/A</b></font>";
						}
					?>
				</td>
			</tr>
			<tr>
				<td height='3'></td>
			</tr>
			<tr>
				<td class="text5" valign="top">&nbsp;&nbsp;&nbsp;Resignation</td>
				<td class="text5" valign="top">:</td>
				<td valign="top">
					<?php
						if(!empty($hasResignedFile)){
							echo "<a onClick=\"downloadPageLink('dowanload-uploadings.php?T=RESIGNED&I=$downloadingID');\" / class='link_style2' style='cursor:pointer;'>Download</a>";
						}
						else{
							echo "<font class='error'><b>N/A</b></font>";
						}
					?>
				</td>
			</tr>
			<tr>
				<td height='3'></td>
			</tr>
		</table>
	</body>
</html>