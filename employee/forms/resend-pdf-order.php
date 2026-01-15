<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>

<script type="text/javascript">
 var checkExistsFileArray    =    new Array();
 <?php
	if(count($a_customerOrderTemplateFiles) > 0)
	{
		foreach($a_customerOrderTemplateFiles as $kk1=>$vv1)
		{
 ?>
			checkExistsFileArray["<?php echo $kk1?>"]    =    "<?php echo $vv1?>";
<?php
		}
	}
 ?>
 function showBox(flag,path,originalFile)
 {
	var msg         = "";

	fullName        = path;
	shortName       = fullName.match(/[^\/\\]+$/);
	
	var	isExists	=	0;	
	if(originalFile == 1 && shortName != ""){
	
	  for(i=0;i<checkExistsFileArray.length;i++){
			if(checkExistsFileArray[i] == shortName){
				isExists = 1;
				break;
			}
		}
	}

	
	
	if(isExists == 0 || isExists == 1)
	{
		var filext = path.substring(path.lastIndexOf(".")+1);

		var filext = filext.toLowerCase();

		var	showButton	=	0;

		if(flag == 1 && filext != "zap")
		{
			msg        =    "The file you have submitted does not appear to be a valid ZAP file.";

			showButton =	1;
		}
		else if(flag == 2 && (filext != "aci" && filext != "zoo"))
		{
			msg        =    "The file you have submitted does not appear to be a valid ACI/ZOO file.";
			
			showButton =	1;
		}
		else if(flag == 3 && filext != "clk")
		{
			msg        =    "The file you have submitted does not appear to be a valid CLK file.";

			showButton =	1;
		}
		else if(flag == 4 && (filext != "rpt" && filext != "rptx"))
		{
			msg        =    "The file you have submitted does not appear to be a valid RPT file.";

			showButton =	1;
		}
		else if(flag == 5 && filext != "zap")
		{
			msg        =    "The file you have submitted does not appear to be a valid ZAP file.";

			showButton =	1;
		}
		else if(flag == 6 && filext != "rpt")
		{
			msg        =    "The file you have submitted does not appear to be a valid RPT file.";

			showButton =	1;
		}

		if(showButton == 1)
		{
			document.getElementById('showSubmitDisable').disabled = 'true';
		}
		else
		{
			document.getElementById('showSubmitDisable').disabled = '';
		}
	}
	else
	{
		document.getElementById('showSubmitDisable').disabled = '';
	}

	var pdfFileDataTemplate		 = document.getElementById("appraisalTemplateFileId");
	var pdfFileSizeTemplate		 = pdfFileDataTemplate.files[0].size;
	if(pdfFileSizeTemplate != "" && pdfFileSizeTemplate > 104857800){
		msg        =    "The template file you are trying to upload is very large. Please upload less than 100 MB";
		
		document.getElementById('showSubmitDisable').disabled = 'true';
	}
	else{
		document.getElementById('showSubmitDisable').disabled = '';
	}

	document.getElementById('displayType').innerHTML = msg;
 }

 function showUploadSizeError(id,type)
 {
	var msg         =   "";
	var pdfFileDataTemplate1		 = document.getElementById(id);
	var pdfFileSizeTemplate1		 = pdfFileDataTemplate1.files[0].size;
	
	if(pdfFileSizeTemplate1 != "" && pdfFileSizeTemplate1 > 104857800){
		msg        =    "The "+type+" file you are trying to upload is very large. Please upload less than 100 MB";
		document.getElementById('showSubmitDisable').disabled = 'true';
	}
	else{
		document.getElementById('showSubmitDisable').disabled = '';
	}

	document.getElementById('displayType').innerHTML = msg;
 }

 function checkReplyFileName(appraisalText,uploadingFile,originalFile)
 {
	document.getElementById('matchFileName').innerHTML = "";

	//var uploadingFile	=	uploadingFile.toLowerCase();
	var	uploadingFile	=	uploadingFile.replace(/^.*[\\\/]/, '');
	var dotPosition		=   uploadingFile.lastIndexOf(".");
	var replyOrderFile	=	uploadingFile.substring(0, dotPosition);
	var replyFileExt	=	uploadingFile.substring(dotPosition+1);
	replyFileExt	    =	replyFileExt.toLowerCase();
	var t_appraisalText	=	appraisalText.toLowerCase();
	
	var	isExists	=	0;	
	if(originalFile == 1){
	
	  for(i=0;i<checkExistsFileArray.length;i++){
			if(checkExistsFileArray[i] == uploadingFile){
				isExists = 1;
				break;
			}
		}
	}

	if(originalFile == 1 && isExists == 0)
	{
		if(t_appraisalText == "aci" && replyFileExt == "zoo"){
			return true;
		}
		else{
			filemsg     = "Completed "+appraisalText+" file name not matching with customer "+appraisalText+" file name.";

			document.getElementById('matchFileName').innerHTML = filemsg;
		}

	}

 }

function checkValidReply()
{
	form1	=	document.replyOrders;

	var pathPdf	=	document.getElementById('checkPdfComFileId').value;

	if(pathPdf != "")
	{
		var filext  = pathPdf.substring(pathPdf.lastIndexOf(".")+1);

		var filext  = filext.toLowerCase();
		
		if(filext != "pdf")
		{
			alert("Please upload PDF File of the Completd File with extention of .pdf.");
			form1.pdfCompletedFile.focus();
			return false;
		}
	}

	if(form1.hasCompletedPdfFile.value == "0")
	{	
				
		if(pathPdf == "")
		{
			alert("Please upload a PDF File of the Completd File.");
			form1.pdfCompletedFile.focus();
			return false;
		}
	}


	if(form1.hasAdminMessage.value	  ==	"1")
	{
		if(form1.adminMessadeId.value == "0")
		{
			alert("Please Select a Message !!");
			form1.adminMessadeId.focus();
			return false;
		}
		if(form1.replyInstructions.value	==	"")
		{
			alert("Please enter instructions !!");
			form1.replyInstructions.focus();
			return false;
		}
	}
	else
	{
		if(form1.replyInstructions.value	==	"")
		{
			alert("Please enter instructions !!");
			form1.replyInstructions.focus();
			return false;
		}
	}

	if(form1.resendingReason.value == "" || form1.resendingReason.value == " " || form1.resendingReason.value == "0")
	{
		alert("Please enter explanation on resending files !!");
		form1.resendingReason.focus();
		return false;
	}
			
	form1.submit.value    = "Wait while resending under process";
	form1.submit.disabled = true;
	display_loading()
}

 function showMessagePDfFile(path)
 {
	fullName1    = path;
	shortName1   = fullName1.match(/[^\/\\]+$/);

	var pdfFileData		 = document.getElementById("checkPdfComFileId");
	var pdfFileSize		 = pdfFileData.files[0].size;
	
	var msg1     = "";
	
	var filext = path.substring(path.lastIndexOf(".")+1);

	var filext = filext.toLowerCase();

	var	showButton	=	0;

	var msg			=	"";

	

	document.getElementById('showSubmitDisable').disabled = '';
	if(filext != "pdf")
	{
		msg        =    "The file you have submitted does not appear to be a PDF file.";
		document.getElementById('showSubmitDisable').disabled = 'true';
	}
	else if(pdfFileSize != "" && pdfFileSize > 104857800){
		msg        =    "The PDF File you are trying to upload is very large. Please upload less than 100 MB";
		document.getElementById('showSubmitDisable').disabled = 'true';
	}
	
	document.getElementById('matchPdfName').innerHTML = msg;
 }
function display_loading()
{
	document.getElementById('loading').style.display = 'block';
} 
</script>
<br>
<form name="replyOrders" action="" method="POST" enctype="multipart/form-data" onsubmit="return checkValidReply();">
	<table width='98%' align='center' cellpadding='3' cellspacing='0' border='0'>
		<tr>
			<td colspan="4" class="heading1">
				REPLY ORDER FILES
			</td>
		</tr>
		<tr>
            <td colspan="4">
                <div id="displayType" style="font-family:arial;font-size:14px;color:#ff0000;text-decoration:none;font-weight:bold;"></div>
				<br>
				<div id="matchFileName" style="font-family:arial;font-size:14px;color:#ff0000;text-decoration:none;font-weight:bold;"></div>
				<div id="matchPdfName" style="font-family:arial;font-size:14px;color:#ff0000;text-decoration:none;font-weight:bold;"></div>
            </td>
        </tr>
		<tr>
			<td colspan="3" class="smalltext2">
				Now you must upload a PDF file of the completed file also. This file will be also sent to customer for reference purpose only.
			</td>
		</tr>
		<?php
			$all_replied_files		=	array();

			$query1				    =	"SELECT * FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND orderId=$orderId AND replyOrderId=$replyId AND uploadingFor=2 AND isDeleted=0";
			$result1			    =	dbQuery($query1);
			if(mysqli_num_rows($result1))
			{
				while($row1						=	mysqli_fetch_assoc($result1)){
					$fileId						=	$row1['fileId'];
					$uploadingFileName			=	stripslashes($row1['uploadingFileName']);
					$uploadingFileExt			=	stripslashes($row1['uploadingFileExt']);
					$uploadingFileSize			=	$row1['uploadingFileSize'];
					$uploadingType				=   $row1['uploadingType'];

					$downloadPath				=	$row1['excatFileNameInServer'];
					$mimeTypeField			    =	$row1['uploadingFileType'];
				
					$all_replied_files[$uploadingType] = $fileId."|".$uploadingFileName."|".$uploadingFileExt."|".$uploadingFileSize;
			
				}
			}
		?>
		<tr>
			<td class="smalltext2" valign="top"><b>PDF File of the Completd File</b></td>
			<td class="smalltext2" valign="top"><b>:</b></td>
			<td class="smalltext10" valign="top">
				<input type="file" name="pdfCompletedFile" id="checkPdfComFileId"  onchange="showMessagePDfFile(this.value)">
				<br>
				<b>
					<?php 
						if($hasCompletedPdfFile)
						{
							if($isRepliedWithNewSystem == 1 && $isNewUploadingSystem == 1)
							{
								if(array_key_exists(7,$all_replied_files)){
									
									$file_data 			     =	 $all_replied_files[7];
							
									list($fileId,$uploadingFileName,$uploadingFileExt,$uploadingFileSize)	=	explode("|",$file_data);
                               									
									$base_fileId		=	base64_encode($fileId);

									$downLoadPath		=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
							?>
							<a class="link_style26" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a>&nbsp;<font class='smalltext20'>
							<?php echo getFileSize($uploadingFileSize);?></font>
							<?php
								}
							}
							else
							{
								echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=PDF&f=R'  class='link_style26'>".$compltetedPdfFileName.".".$compltetedPdfFileExt."</a>&nbsp;<font class='smalltext20'>".getFileSize($compltetedPdfFileSize)."</font>";
							}
						}
					?>
				</b>
			</td>
		</tr>
		<tr>
			<?php
				$t_replieddFileToustomer=	stringReplace("ACI","ACI/ZOO",$replieddFileToustomer);
				$t_replieddFileToustomer=	stringReplace(".aci",".aci/zoo",$t_replieddFileToustomer);
			?>
			<td class="smalltext2" width="22%" valign="top"><b><?php echo $t_replieddFileToustomer;?></b></td>
			<td class="smalltext2"  width="2%" valign="top"><b>:</b></td>
			<td class="smalltext10" valign="top">
				<input type="file" name="replyOrderFile" id="appraisalTemplateFileId" onchange="showBox(<?php echo $appraisalSoftwareType;?>,this.value,'<?php echo $checkExistingOrderfileName;?>');checkReplyFileName('<?php echo $appraisalText;?>',this.value,'<?php echo $checkExistingOrderfileName;?>');">
				<br>
				<b>
					<?php 
						if($hasReplyOrderFile)
						{
							if($isRepliedWithNewSystem == 1 && $isNewUploadingSystem == 1)
							{
								if(array_key_exists(1,$all_replied_files)){
									
									$file_data 			     =	 $all_replied_files[1];
							
									list($fileId,$uploadingFileName,$uploadingFileExt,$uploadingFileSize)	=	explode("|",$file_data);
                               									
									$base_fileId		=	base64_encode($fileId);

									$downLoadPath		=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
							?>
							<a class="link_style26" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a>&nbsp;<font class='smalltext20'>
							<?php echo getFileSize($uploadingFileSize);?></font>
							<?php
								}
							}
							
						}
					?>
				</b>
			</td>
		</tr>
		<tr>
			<td class="smalltext2" valign="top"><b>Upload Reply Public Records File</b></td>
			<td class="smalltext2" valign="top"><b>:</b></td>
			<td class="smalltext10" valign="top">
				<input type="file" name="replyPublicRecordFile" id="replyPublicRecordFileId" onchange="showUploadSizeError('replyPublicRecordFileId','Public Records');">
				<br>
				<b>
					<?php 
						if($hasReplyPublicRecordFile)
						{
							if($isRepliedWithNewSystem == 1 && $isNewUploadingSystem == 1)
							{
								if(array_key_exists(2,$all_replied_files)){
									
									$file_data 			     =	 $all_replied_files[2];
							
									list($fileId,$uploadingFileName,$uploadingFileExt,$uploadingFileSize)	=	explode("|",$file_data);
                               									
									$base_fileId		=	base64_encode($fileId);

									$downLoadPath		=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
							?>
							<a class="link_style26" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a>&nbsp;<font class='smalltext20'>
							<?php echo getFileSize($uploadingFileSize);?></font>
							<?php
								}
							}
							
						}
					?>
				</b>
			</td>
		</tr>
		<tr>
			<td class="smalltext2" valign="top"><b>Upload Plat Map File</b></td>
			<td class="smalltext2" valign="top"><b>:</b></td>
			<td class="smalltext10" valign="top">
				<input type="file" name="replyMlsFile" id="replyMlsFileId" onchange="showUploadSizeError('replyMlsFileId','Plat Map');">
				<br>
				<b>
					<?php 
						if($hasReplyMlsFile)
						{
							if($isRepliedWithNewSystem == 1 && $isNewUploadingSystem == 1)
							{
								if(array_key_exists(3,$all_replied_files)){
									
									$file_data 			     =	 $all_replied_files[3];
							
									list($fileId,$uploadingFileName,$uploadingFileExt,$uploadingFileSize)	=	explode("|",$file_data);
                               									
									$base_fileId		=	base64_encode($fileId);

									$downLoadPath		=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
							?>
							<a class="link_style26" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a>&nbsp;<font class='smalltext20'>
							<?php echo getFileSize($uploadingFileSize);?></font>
							<?php
								}
							}
							
						}
					?>
				</b>
			</td>
		</tr>
		<tr>
			<td class="smalltext2" valign="top"><b>Upload Reply Other File</b></td>
			<td class="smalltext2" valign="top"><b>:</b></td>
			<td class="smalltext10" valign="top">
				<input type="file" name="otherFile" id="otherFileId" onchange="showUploadSizeError('otherFileId','Reply Other');">
				<br>
				<b>
					<?php 
						if($hasOtherFile)
						{
							
							if($isRepliedWithNewSystem == 1 && $isNewUploadingSystem == 1)
							{
								if(array_key_exists(6,$all_replied_files)){
									
									$file_data 			     =	 $all_replied_files[6];
							
									list($fileId,$uploadingFileName,$uploadingFileExt,$uploadingFileSize)	=	explode("|",$file_data);
                               									
									$base_fileId		=	base64_encode($fileId);

									$downLoadPath		=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
								?>
								<a class="link_style26" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a>&nbsp;<font class='smalltext20'>
								<?php echo getFileSize($uploadingFileSize);?></font>
								<?php
								}
							}
							
						}
					?>
				</b>
			</td>
		</tr>
		<?php 
		if(!empty($a_orderAdminReplyMessages))
		{
	?>
	<tr>
		<td class="smalltext2"><b>Select a message with reply instructions</b></td>
		<td class="smalltext2"><b>:</b></td>
		<td>
			<?php
				$url		=	SITE_URL_EMPLOYEES."/get-all-admin-order-reply-messages.php?messageId=";
			?>
			<select name="adminMessadeId" onchange="commonFunc('<?php echo $url?>','displayCustomReplyMessage',this.value);">
				<option value="0">Select</option>
				<?php 
					foreach($a_orderAdminReplyMessages as $key=>$value)
					{
						$select		=	"";
						if($key		==	$adminMessadeId)
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
		<td colspan="3" valign="top">
			<div id="displayCustomReplyMessage">
				<?php
					if(!empty($adminMessadeId) && !empty($replyInstructions))
					{
				?>
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td class="smalltext2" valign="top">
								<b>Reply Instructions For Customer</b>
							</td>
						</tr>
						<tr>
							<td  valign="top">
								<textarea name="replyInstructions" rows="8" cols="70"><?php echo stripslashes(htmlentities($replyInstructions,ENT_QUOTES))?></textarea>
							</td>
						</tr>
					</table>
				<?php
					}
				?>
			</div>
		</td>
	</tr>
	<?php	
		}
		else
		{
	?>
		<tr>
			<td class="smalltext2" valign="top"><b>Reply Instructions</b></td>
			<td class="smalltext2"  valign="top"><b>:</b></td>
			<td  valign="top">
				<textarea name="replyInstructions" rows="8" cols="45"><?php echo stripslashes(htmlentities($replyInstructions,ENT_QUOTES))?></textarea>
			</td>
		</tr>
	<?php
		}	
	?>
		<tr>
			<td class="smalltext2" valign="top" colspan="3">
				<b>Explanation on Resending Files</b>
			</td>
		</tr>
		<tr>
			<td  valign="top" colspan="3">
				<textarea name="resendingReason" rows="4" cols="70"><?php echo stripslashes(htmlentities($resendingReason,ENT_QUOTES))?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="3" height="5"></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2">
				<div id="loading" style="display: none;"><img src="<?php echo OFFLINE_IMAGE_PATH;?>/images/ajax-loader.gif" alt="" /></div> 
			</td>
		</tr>
		<tr>
			<td colspan="3" height="5"></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td>
				<input type="submit" name="submit" value="Submit" id="showSubmitDisable">
				<input type="button" name="submit" onClick="history.back()" value="Back">
				<input type="hidden" name="hasAdminMessage" value="<?php echo $hasAdminMessage;?>">
				<input type="hidden" name="hasCompletedPdfFile" value="<?php echo $hasCompletedPdfFile;?>">
				<input type="hidden" name="formSubmitted" value="1">
			</td>
		</tr>
	</table>
</form>