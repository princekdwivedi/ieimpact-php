<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_MTEMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_MTEMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT			  . "/classes/validate-fields.php");
	$validator					=	new validate();
	$headerText					=  "";
	$photoType					=  "";
	$showForm					=	false;
	$existingPhoto				=	"";
	$submitText					=	"";
	$errorMsg					=	"";
	$baseEmployeeId				=	base64_encode($s_mtemployeeId);
	$md5EmployeeId				=	md5($s_mtemployeeId);
	
	$employeeImagePath			=	SITE_ROOT_FILES."/files/employee-images/";
	$employeeImageUrl			=	SITE_URL."/files/employee-images/";

	
	if(isset($_GET['employeeId']))
	{
		$employeeId			=	(int)$_GET['employeeId'];
		$query				=	"SELECT * FROM employee_details WHERE hasPdfAccess=0 AND employeeId=$employeeId AND isActive=1 AND isManager=1";
		$result				=	dbQuery($query);
		if(mysqli_num_rows($result)){
			$row				=	mysqli_fetch_assoc($result);
			$showForm			=	true;
			$managerName		=	stripslashes($row['fullName']);
			$hasProfilePhoto 	=	$row['hasProfilePhoto'];
			$profilePhotoExt	=	stripslashes($row['profilePhotoExt']);
			$mobile 			=	$row['mobile'];
			$email				=	$row['email'];

		}
	}
	
?>
<html>
<head>
<title>
	View manager - <?php echo $managerName;?> details
</title>
<link href="<?php echo SITE_URL_MTEMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
<LINK REL="SHORTCUT ICON" HREF="<?php echo SITE_URL;?>/icon-1.gif">
</head>
<body style="topmargin:0px";>
		<?php
			if($showForm == true)
			{
		?>
		<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
			<tr>
				<td colspan="9" class="smalltext21"><b>DETAILS OF MANAGER - <?php echo $managerName."(Emp Code - ".$employeeId.")";?></b></td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<?php
				if(!empty($hasProfilePhoto) && !empty($profilePhotoExt))
				{
			?>
			<tr>
				<td colspan="3">
					<img src="<?php echo SITE_URL_MTEMPLOYEES;?>/get-employee-profile-photos.php?ID=<?php echo $employeeId;?>&ext=<?php echo $profilePhotoExt;?>" border="0" title="<?php echo $managerName;?>" width="150" height="150">
				</td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<?php
				}
			?>
			<tr>
				<td width="15%" valign="top" class="smalltext21">Email</td>
				<td width="3%" valign="top" class="smalltext21">:</td>
				<td valign="top" class="smalltext23"><?php echo $email;?></td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td valign="top" class="smalltext21">Mobile</td>
				<td valign="top" class="smalltext21">:</td>
				<td valign="top" class="smalltext23"><?php echo $mobile;?></td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
		</table>
		<?php
			}
			else
			{
				echo "<br><center><font class='error'><b>You are not authorized to open this page !!</b></font></center>";
			}
		?>
		<center><a onclick="window.close();" style="cursor:pointer;color:#ff0000;"><u>Close</u></a>
	
</body>
</html>
