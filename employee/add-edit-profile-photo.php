<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include("../root.php");	
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");
	$validator					=	new validate();
	$headerText					=  "";
	$photoType					=  "";
	$showForm					=	false;
	$existingPhoto				=	"";
	$submitText					=	"";
	$errorMsg					=	"";
	$baseEmployeeId				=	base64_encode($s_employeeId);
	$md5EmployeeId				=	md5($s_employeeId);
	$profilePhotoExt            =   "";
	
	$employeeImagePath			=	SITE_ROOT_FILES."/files/employee-images/";
	$employeeImageUrl			=	SITE_URL."/files/employee-images/";
	
	function getFileName($fileName)
	{
		$fileName		=  stripslashes($fileName);
		$dotPosition	=  strpos($fileName, "'");
		if($dotPosition == true)
		{
			$fileName	=	stringReplace("'", "", $fileName);
		}
		$doubleDotPosition	  =  strpos($fileName, '"');
		if($doubleDotPosition == true)
		{
			$fileName	=	stringReplace('"', '', $fileName);
		}
		$fileName		=	stringReplace("/", '', $fileName);
		$fileName		=	stringReplace(":", '', $fileName);
		$fileName		=	stringReplace("&", '', $fileName);
		$fileName		=	stringReplace("*", '', $fileName);
		$fileName		=	stringReplace("?", '', $fileName);
		$fileName		=	stringReplace("|", '', $fileName);
		$fileName		=	stringReplace("<", '', $fileName);
		$fileName		=	stringReplace(">", '', $fileName);
		$fileExtPos		=   strrpos($fileName, '.');
		$fileName		=   substr($fileName,0,$fileExtPos);
		
		return $fileName;
	}

	
	if(isset($_GET['P']))
	{
		$photoType		=	(int)$_GET['P'];
		if($photoType 		== 1 || $photoType == 2)
		{
			$showForm		=	true;
			if($photoType  ==	1)
			{
				$headerText	=  "Add your photo";
				$submitText	=  "successfully added new photo";
			}
			else
			{
				$headerText	=  "Change your photo";
				$submitText	=  "successfully changed photo";
			}
		}
	}
	if(!isset($_SESSION['employeeId']))
	{
		$showForm				=	false;
	}
	else
	{
		$s_employeeId			=	$_SESSION['employeeId'];
		$query					=	"SELECT hasProfilePhoto,profilePhotoExt FROM employee_details WHERE employeeId=$s_employeeId AND isActive=1 AND hasProfilePhoto=1";
		$result					=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$row				=	mysqli_fetch_assoc($result);
			$hasProfilePhoto 	=	$row['hasProfilePhoto'];
			$profilePhotoExt	=	stripslashes($row['profilePhotoExt']);

			$displayThumbImage	=	$baseEmployeeId."_".$md5EmployeeId.".".$profilePhotoExt;
			
			$existingPhoto		=	"<img src='".SITE_URL_EMPLOYEES."/get-employee-profile-photos.php?ID=".$s_employeeId."&ext=".$profilePhotoExt."'>";
		}
		else
		{
			$hasPhoto			=	0;
			$photoExt			=	"";
			$existingPhoto		=	"";
		}
	}
	$form		=	SITE_ROOT_EMPLOYEES."/forms/edit-profile-photo.php";
?>
<script type="text/javascript">
	function reflectChange()
	{
		window.opener.location.reload();
	}
</script>
<html>
<head>
<title>
	<?php echo $headerText;?>
</title>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
<LINK REL="SHORTCUT ICON" HREF="<?php echo SITE_URL;?>/icon-1.gif">
</head>
<body style="topmargin:0px";>
		<?php
			if($showForm == true)
			{
				if(isset($_REQUEST['formSubmitted']))
				{
					extract($_REQUEST);
					//pr($_REQUEST);
					if(!empty($_FILES['editUpload']['name']))
					{
						$photoName	=	$_FILES['editUpload']['name'];
						$fileSize	=   $_FILES['editUpload']['size'];
						$ext		=	getAllTypesFilesExt($photoName);
						if($fileSize > 1048576)
						{
							$validator->setError("Please upload upto 1 MB file.");
						}
						
						if($ext != 'jpg' && $ext != 'gif' && $ext != 'jpeg' && $ext != 'png')
						{
							$validator->setError("Please upload only .jpg or .gif or .png file.");
						}
					}
					else
					{
						$validator->setError("Please upload photo!");
					}
					$dataValid		=	$validator->isDataValid();
					if($dataValid)
					{
						if(!empty($_FILES['editUpload']['name']))
						{
							$deleteImage	=	$baseEmployeeId."_".$md5EmployeeId.".".$profilePhotoExt;
							
							if(file_exists($employeeImagePath."t_".$deleteImage))
							{
								@unlink($employeeImagePath."t_".$deleteImage);
							}
							if(file_exists($employeeImagePath.$deleteImage))
							{
								@unlink($employeeImagePath.$deleteImage);
							}
							
							
							$photoName		=	$_FILES['editUpload']['name'];
							$tempName		=	$_FILES['editUpload']['tmp_name'];
							$mimeType		=   $_FILES['editUpload']['type'];
							$fileSize		=   $_FILES['editUpload']['size'];
							$ext			=	getAllTypesFilesExt($photoName);

							
							$mainFile		=	$baseEmployeeId."_".$md5EmployeeId.".".$ext;

							@move_uploaded_file($tempName,$employeeImagePath.$mainFile);

							$fileName1		=	$mainFile;
							$fileName2		=	"t_".$mainFile;
							
							include(SITE_ROOT."/classes/image-editor.php");
							$imageObj1		=	new ImageEditor($fileName1,$employeeImagePath);
							$imgWidth		=	$imageObj1->getWidth();
							$imgHeight		=	$imageObj1->getHeight();

							if($imgWidth > 220 || $imgHeight > 220)
							{
								$imageObj1->resizeInProportion($imgWidth,$imgHeight,200);
								$imageObj1->outputFile($fileName1, $employeeImagePath);
							}
													
							$imageObj1->resizeInProportion($imgWidth,$imgHeight,150);
							$imageObj1->outputFile($fileName2, $employeeImagePath);
									

							dbQuery("UPDATE employee_details SET hasProfilePhoto=1,profilePhotoExt='$ext',profilePhotoType='$mimeType',profilePhotoSize='$fileSize' WHERE employeeId=$s_employeeId");										
						}
						
						echo "<table width='95%' align='center' border='0' height='70'><tr><td class='text1'>$submitText.</td></tr></table>";
	
						echo "<script type='text/javascript'>reflectChange();</script>";
				
						echo "<script>setTimeout('window.close()',1000)</script>";
					
					}
					else
					{
						$errorMsg	=	$validator->getErrors();
						include($form);
					}
				}
				else
				{
					include($form);
				}
			}
			else
			{
				echo "<br><center><font class='error'><b>You are not authorized to open this page !!</b></font></center>";
			}
		?>
	
</body>
</html>
