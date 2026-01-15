<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	$memberId					=	0;
	$errorMsg					=	"";
	$splInstructionOfCustomer	=	"";
	$instructionsPath			=	SITE_ROOT_FILES."/files/instructions/";
	if(isset($_GET['customerId']))
	{
		$memberId				=	(int)$_GET['customerId'];

		$query					=	"SELECT firstName,lastName,splInstructionOfCustomer FROM members WHERE memberId=$memberId";
		$result	=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$row				=	mysqli_fetch_assoc($result);
			$firstName			=	stripslashes($row['firstName']);
			$lastName			=	stripslashes($row['lastName']);
			$splInstructionOfCustomer=	stripslashes($row['splInstructionOfCustomer']);

			$customerName		=	$firstName." ".$lastName;
			$customerName		=	ucwords($customerName);
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
		$fileExtPos		=  strrpos($fileName, '.');
		$fileName		=  substr($fileName,0,$fileExtPos);
		
		return $fileName;
	}

?>
<html>
<head>
<title>Edit Note For <?php echo $customerName;?></title>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
<body>
	<center>
		<script type="text/javascript">
			function validNote()
			{
				//return true;
				form1 = document.addNote;
				if(form1.splInstructionOfCustomer.value == "")
				{
					alert("Please enter note !!");
					form1.splInstructionOfCustomer.focus();
					return false;
				}
				
			}
			function reflectChange()
			{
				window.opener.location.reload();
			}
		</script>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td class="textstyle1">
					<b>EDIT NOTE FOR - <?php echo $customerName;?></b>
				</td>
			</tr>
		</table>
		<?php 
			if(isset($_REQUEST['formSubmitted']))
			{
				extract($_REQUEST);
				$splInstructionOfCustomer	=	trim($splInstructionOfCustomer);
				$splInstructionOfCustomer	=	makeDBSafe($splInstructionOfCustomer);
				if(empty($splInstructionOfCustomer))
				{
					$errorMsg				=	"Please enter note !!";
				}
				if(empty($errorMsg))
				{
					dbQuery("UPDATE members SET splInstructionOfCustomer='$splInstructionOfCustomer',addedInstructionsOn='$nowDateIndia' WHERE memberId=$memberId");

					if(!empty($_FILES['instructionsfile']['name']))
					{
						$a_fileNames	=	$_FILES['instructionsfile']['name'];
						$a_fileSizes	=	$_FILES['instructionsfile']['size'];
						$a_fileTypes	=	$_FILES['instructionsfile']['type'];
						$a_tempNames	=	$_FILES['instructionsfile']['tmp_name'];
						foreach($a_fileNames as $key=>$fileName)
						{
							if(!empty($fileName))
							{
								$fileType		=	$a_fileTypes[$key];
								$fileSize		=	$a_fileSizes[$key];
								$temp			=	$a_tempNames[$key];
								$ext			=	findexts($fileName);
								$fileName		=	getFileName($fileName);

								$query			=	"INSERT INTO customer_instructions_file SET memberId='$memberId',fileName='$fileName',fileExt='$ext',size='$fileSize',mimeType='$fileType',addedOn='$nowDateIndia',addedTime='$nowTimeIndia',uploadedBy='".EMPLOYEES."'";
								dbQuery($query);
								$instructionId	=	mysqli_insert_id($db_conn);

								$t_fileName		=   $instructionId."_".$fileName.".".$ext;
											
								move_uploaded_file($temp,$instructionsPath.$t_fileName);
								chmod($instructionsPath."$t_fileName",0600);
							}
						}
					}

					echo "<script type='text/javascript'>reflectChange();</script>";

					echo "<script>window.close();</script>";
				}
			}
		?>
		<form name="addNote" action="" method="POST" enctype="multipart/form-data" onsubmit="return validNote();">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td class="error" colspan="3">
						<b><?php echo $errorMsg;?></b>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<textarea name="splInstructionOfCustomer" cols="75" rows="13" style="border: 2px solid #333333"><?php echo $splInstructionOfCustomer;?></textarea>
					</td>
				</tr>
				<tr>
					<td height="8"></td>
				</tr>
				<tr>
					<td colspan="3" class="smalltext2">
						<b>Upload Note File</b>
					</td>
				</tr>
				<tr>
					<td height="4"></td>
				</tr>
				<?php
					for($i=1;$i<=5;$i++)
					{
				?>
				<tr>
					<td width="3%" align="center" class="textstyle"><b><?php echo $i;?>)</b></td>
					<td >
						<input type="file" name="instructionsfile[]">
					</td>
				</tr>
				<tr>
					<td height="4"></td>
				</tr>
				<?php
					}
				?>
				<tr>
					<td align="center" colspan="3">
						<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
						<input type='hidden' name='formSubmitted' value='1'>
					</td>
				</tr>
			</table>
		</form>
		<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
	</center>
</body>
</html>