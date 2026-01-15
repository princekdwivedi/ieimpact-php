<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include("../root.php");	
	include(SITE_ROOT_EMPLOYEES .   "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES .   "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES .   "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES .   "/classes/employee.php");
	$employeeObj				=	new employee();
	$headerText					=   "";
	$showForm					=	false;
	$employeeId				    =	"";
	$errorMsg					=	"";
	$filePath		            =	SITE_ROOT_FILES."/files/member-identity/";
	$a_typesUploading 			=	array();
	$a_typesUploading[1] 		=	"Crossed Cheque";
	$a_typesUploading[2] 		=	"Form 11";
	$a_typesUploading[3] 		=	"Resignation";
	$a_typesUploading[4] 		=	"Form 11 Revised";

	$a_uploadingMainTypes     	=	array();
	$a_uploadingMainTypes[1] 	=	10;
	$a_uploadingMainTypes[2] 	=	12;
	$a_uploadingMainTypes[3] 	=	11;
	$a_uploadingMainTypes[4] 	=	13;
		
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

	
	if(isset($_GET['employeeId']) && isset($_GET['type']))
	{
		$employeeId		   =	(int)$_GET['employeeId'];
		$type		       =	$_GET['type'];
	
		if(!empty($employeeId) && !empty($type) && array_key_exists($type,$a_typesUploading) && array_key_exists($type,$a_uploadingMainTypes)){

			$fullName 	     =   $employeeObj->getSingleQueryResult("SELECT fullName FROM employee_details WHERE employeeId=$employeeId AND isActive=1 AND hasPdfAccess=1", "fullName");

			if(!empty($fullName)){

				$a_employeeFiles =	$employeeObj->getEmployeeProfileFiles($employeeId);
				
				$showForm		 =	true;
				$upoadingForText =	$a_typesUploading[$type];
			}
		}
	}
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
	Add <?php echo $upoadingForText;?>
</title>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
<LINK REL="SHORTCUT ICON" HREF="<?php echo SITE_URL;?>/icon-1.gif">
</head>
<body style="topmargin:0px";>
		<?php
			if($showForm == true)
			{
				if(isset($_REQUEST['formSubmittedCheque']))
				{
					extract($_REQUEST);
					//pr($_REQUEST);
					if(!empty($_FILES['blankCrossedCheque']['name']))
					{
						$uploadingFile	    =   $_FILES['blankCrossedCheque']['name'];
						$mimeType		    =   $_FILES['blankCrossedCheque']['type'];
						$fileSize		    =   $_FILES['blankCrossedCheque']['size'];
						$tempName		    =	$_FILES['blankCrossedCheque']['tmp_name'];
						$ext			    =	findexts($uploadingFile);
						$uploadingFileName	=	getFileName($uploadingFile);

						$addNew             =   true;
						$uploadingFileType 	=	$a_uploadingMainTypes[$type];
						if(!empty($a_employeeFiles) && array_key_exists($uploadingFileType, $a_employeeFiles))
						{
							
							$fileServerPath 	=	$a_employeeFiles[$uploadingFileType];
							if(file_exists($fileServerPath))
							{
								unlink($fileServerPath);
								$addNew         =   false;
							}
						}						

						if($type == 1){

							$fileName			=   "CRQ_".$employeeId."_".$uploadingFileName.".".$ext;
						}
						elseif($type == 2){

							$fileName			=   "ELEVEN_".$employeeId."_".$uploadingFileName.".".$ext;
						}
						elseif($type == 3){

							$fileName			=   "RESIGNED_".$employeeId."_".$uploadingFileName.".".$ext;
						}
						elseif($type == 4){

							$fileName			=   "ELEVENRES_".$employeeId."_".$uploadingFileName.".".$ext;
						}

						move_uploaded_file($tempName,$filePath.$fileName);

						$uploadingFileName	=	makeDBSafe($uploadingFileName);
						$fileTypeName 		=   $a_employeesFilesUpoadingTypes[$uploadingFileType];//Identity Proof File
						$fileServerPath     =   $filePath.$fileName;

						if($addNew == true){
							dbQuery("INSERT INTO employeee_profile_files SET employeeId=$employeeId,type=$uploadingFileType,fileTypeName='$fileTypeName',fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
						}
						else{
							dbQuery("UPDATE employeee_profile_files SET fileName='$uploadingFileName',fileServerPath='$fileServerPath',fileSize=$fileSize,mimeType='$mimeType',ext='$ext',addedDate='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."' WHERE employeeId=$employeeId AND type=$uploadingFileType");
						}

						if($type == 1){

							dbQuery("UPDATE employee_details SET hasCancelledCheque=1 WHERE employeeId=$employeeId");
						}
						elseif($type == 2){

							dbQuery("UPDATE employee_details SET hasFormEleven=1 WHERE employeeId=$employeeId");
						}
						elseif($type == 3){

							dbQuery("UPDATE employee_details SET hasResignedFile=1 WHERE employeeId=$employeeId");
						}
						elseif($type == 4){

							dbQuery("UPDATE employee_details SET hasFormElevenRevised=1 WHERE employeeId=$employeeId");
						}

	
						echo "<table width='95%' align='center' border='0' height='70'><tr><td class='text1'>Successfully added ".$upoadingForText."</td></tr></table>";

						echo "<script type='text/javascript'>reflectChange();</script>";
				
						echo "<script>setTimeout('window.close()',1000)</script>";
					}
					else
					{
						$errorMsg 	=	"Please upload ".strtolower($upoadingForText)." in .jpg/.png format.";
					}
					
				}
		?>
			<script type="text/javascript">
				function display_loading()
				{
					document.getElementById('loading').style.display = 'block';
				} 
				function checkValid(upoadingForText){
					form1	 =	document.editEmployeeCheque;
					var file = document.getElementById("blankCrossedCheque"); 

					if(file.files.length == 0 ){ 
			            alert("Please upload "+upoadingForText+" in .jpg/.png/.pdf format.");
					    form1.blankCrossedCheque.focus();
					    return false;
			        }
			       /* else{
			        	var filePath = file.value; 
			          
			            // Allowing file type 
			            var allowedExtensions =  
			                    /(\.jpg|\.jpeg|\.png|\.pdf)$/i; 
			              
			            if (!allowedExtensions.exec(filePath)) { 
			                alert('Please upload only .jpg/.png/.pdf file'); 
			                form1.blankCrossedCheque.focus();
					        return false;
			            }  
			        }*/

			        form1.submit.value    = "Uploading... Please wait";
					form1.submit.disabled = true;

					display_loading();
				}
			</script>
			<form name="editEmployeeCheque" action="" method="POST" enctype="multipart/form-data" onSubmit="return checkValid('<?php echo $upoadingForText;?>');">
				<table width="100%" cellpadding="1" cellspacing="1" border="0" style="border:1px solid #033A61">
					<?php
						if(!empty($errorMsg)){
							echo "<tr><td colspan='3' class='error'><b>$errorMsg</b></td></tr>";
						}
					?>
					<tr>
						<td class="text5" valign="top" width="35%">&nbsp;&nbsp;&nbsp; Employee Name</td>
						<td class="text5" valign="top" width="2%">:</td>
						<td class="text5">
							<b><?php echo $fullName;?></b>
						</td>
					</tr>
					<tr>
						<td class="text5" valign="top">&nbsp;&nbsp;&nbsp; <?php echo $upoadingForText;?></td>
						<td class="text5" valign="top">:</td>
						<td valign="top">
							<input type='file' name='blankCrossedCheque' id="blankCrossedCheque"/> 
						</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
						<td class="smalltext2">[Please upload only .jpg/.png/.pdf file] 
						<?php if($type == 1){?>(Eg. <a href="<?php echo SITE_URL;?>/images/blank-cheque.png" target="_blank" class="link_style2">Sample</a>)<?php } ?></td>
					</tr>
					<tr height="5"><td></td></tr>
					<tr>
						<td colspan="2">&nbsp;</td>
						<td>
							<div id="loading" style="display: none;"><img src="<?php echo OFFLINE_IMAGE_PATH;?>/images/ajax-loader.gif" alt="" /></div> 
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center">&nbsp;</td>
						<td>
							
							<input type="submit" name="submit" value="ADD <?php echo strtoupper($upoadingForText);?>">
							&nbsp;&nbsp;
							<input type="hidden" name="formSubmittedCheque" value="1">
							
						</td>
					</tr>
				</table>
			</form>
			<?php
			}
			else
			{
				echo "<br><center><font class='error'><b>You are not authorized to open this page !!</b></font></center>";
			}
		?>
	
</body>
</html>
