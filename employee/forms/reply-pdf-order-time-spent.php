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
	fullName        = path;
	shortName       = fullName.match(/[^\/\\]+$/);
	
	var msg         =   "";

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
			msg        =    "The file you have submitted does not appear to be a valid ACI file.";

			showButton =	1;
		}
		else if(flag == 3 && filext != "clk")
		{
			msg        =    "The file you have submitted does not appear to be a valid CLK file.";

			showButton =	1;
		}
		else if(flag == 4  && (filext != "rpt" && filext != "rptx"))
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

	var maxFileSizeAllowed	=	"<?php echo MAXIMUM_SINGLE_FILE_SIZE_ALLOWED;?>";
    var maxFileSizeAllowedTxt=	"<?php echo MAXIMUM_SINGLE_FILE_SIZE_ALLOWED_TEXT?>";
	
	if(pdfFileSizeTemplate1 != "" && pdfFileSizeTemplate1 > maxFileSizeAllowed){
		msg        =    "The "+type+" file you are trying to upload is very large. Please upload less than "+maxFileSizeAllowedTxt;
		document.getElementById('showSubmitDisable').disabled = 'true';
		document.getElementById(id).value = null;
	}
	else{
		document.getElementById('showSubmitDisable').disabled = '';
	}

	document.getElementById('displayType').innerHTML = msg;
 }


 function showMessagePDfFile(path)
 {
	var pdfFileData		 = document.getElementById("checkPdfComFileId");
	var pdfFileSize		 = pdfFileData.files[0].size;

	var maxFileSizeAllowed	=	"<?php echo MAXIMUM_SINGLE_FILE_SIZE_ALLOWED;?>";
    var maxFileSizeAllowedTxt=	"<?php echo MAXIMUM_SINGLE_FILE_SIZE_ALLOWED_TEXT?>";

	fullName1    = path;
	shortName1   = fullName1.match(/[^\/\\]+$/);
	
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

		document.getElementById("checkPdfComFileId").value = null;
	}
	else if(pdfFileSize != "" && pdfFileSize > maxFileSizeAllowed){
		msg        =    "The PDF File you are trying to upload is very large. Please upload less than "+maxFileSizeAllowedTxt;
		document.getElementById('showSubmitDisable').disabled = 'true';
		document.getElementById("checkPdfComFileId").value = null;
	}
	
	document.getElementById('matchPdfName').innerHTML = msg;
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

 function isValidCheckAnswer(selected,answer)
 {
	if(answer	!=	0)
	{
		if(selected		!=	answer)
		{
			if(answer == 2)
			{
				if(selected == 1)
				{
					alert("Are you SURE ?");
					//return false;
				}
			}
			else if(answer == 1)
			{
				if(selected == 2)
				{
					alert("Are you SURE ?");
					//return false;
				}
			}
		}
	}
 }

function display_loading()
{
	document.getElementById('loading').style.display = 'block';
} 
function checkValidReply()
{
	form1	=	document.replyOrders;
	var countTotalChecked	=	1;
	if(form1.isChecklistAvailabale.value == 1)
	{
		for(j=1;j<form1.totalChecklistExists.value;j++){
			access	=	document.getElementsByName('readChecklist['+j+']');
			for(i=0;i<access.length;i++)
			{
				if(access[i].checked == true)
				{
					countTotalChecked	=	countTotalChecked+1;
				}
			}
		}

		//alert(form1.totalChecklistExists.value+"=="+countTotalChecked);

		if(form1.totalChecklistExists.value != countTotalChecked)
		{
			alert("Please complete the checklist.");
			return false;
		}
	}

	if(form1.numberOfCompsFilled.value == "" || form1.numberOfCompsFilled.value == "0" || form1.numberOfCompsFilled.value == " "){
		alert("Please enter number of comps filled.");
		form1.numberOfCompsFilled.focus();
		return false;
	}

	if(form1.orderInteralNotes.value == "" || form1.orderInteralNotes.value == "0" || form1.orderInteralNotes.value == " "){
		alert("Please enter internal employee notes.");
		form1.orderInteralNotes.focus();
		return false;
	}

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
			alert("Please Select a Message.");
			form1.adminMessadeId.focus();
			return false;
		}
		if(form1.replyInstructions.value	==	"")
		{
			alert("Please enter instructions.");
			form1.replyInstructions.focus();
			return false;
		}
	}
	else
	{
		if(form1.replyInstructions.value	==	"")
		{
			alert("Please enter instructions.");
			form1.replyInstructions.focus();
			return false;
		}
	}
	if(form1.commentsToQa.value	==	"")
	{
		alert("Please enter simple comment to QA person.");
		form1.commentsToQa.focus();
		return false;
	}

	if(form1.timeSpentEmployee.value	==	"" || form1.timeSpentEmployee.value	==	" " || form1.timeSpentEmployee.value	==	"0" )
	{
		alert("Please enter time spent on this order.");
		form1.timeSpentEmployee.focus();
		return false;
	}
	else if(form1.timeSpentEmployee.value > 200){
		alert("You cannot spend more than 200 minutes in an order.");
		form1.timeSpentEmployee.focus();
		return false;
	}	
		
	form1.submit.value    = "Wait while uploading under process";
	form1.submit.disabled = true;
	display_loading();
}
function deleteProcessedOrderFile(orderId,customerId,orderText,fileType)
{
	var confirmation = window.confirm("Are You Sure Delete "+orderText+"?");
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/process-pdf-order.php?orderId="+orderId+"&customerId="+customerId+"&deleteFileType="+fileType+"&isDeleteFile=1";
	}
}
function deleteMultipleProcessedOrderFile(orderId,customerId,orderText,processFileId,uploadingType)
{
	var confirmation = window.confirm("Are You Sure Delete "+orderText+"?");
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/process-pdf-order.php?orderId="+orderId+"&customerId="+customerId+"&processFileId="+processFileId+"&uploadingType="+uploadingType+"&isDeleteMultipleFile=1";
	}
}

function textCounter(field,countfield,maxlimit)
{
	if(field.value.length > maxlimit)
	{
		field.value = field.value.substring(0, maxlimit);
	}
	else
	{
		countfield.value = maxlimit - field.value.length;
	}
 }

</script>
<br>
<form name="replyOrders" action="" method="POST" enctype="multipart/form-data" onsubmit="return checkValidReply();">
	<table width='98%' align='center' cellpadding='3' cellspacing='0' border='0'>
		<tr>
            <td colspan="3">
				<table width='30%' align='left' cellpadding='3' cellspacing='0' border='0'>
					<tr>
						<td colspan="3">
							<a name="process"></a><?php echo $errorMsg;?>
						</td>
					</tr>
				</table>
			</td>
			<td>&nbsp;</td>
		</tr>
	
		<tr>
			<td colspan="3">
				<?php
					$query		=	"SELECT * FROM qa_checklist WHERE status=0 ORDER BY checklistQaTitle";
					$result		=	dbQuery($query);
					if(mysqli_num_rows($result))
					{
			?>
			<br>
			<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
				<tr>
					<td colspan="5" class="textstyle1">
						<b>Please mark the following checklist</b>
					</td>
				</tr>
				<tr>
					<td height="5"></td>
				</tr>
				<?php
					$countChecklist	=	0;
					while($row		=	mysqli_fetch_assoc($result))
					{
						$countChecklist++;
						$checklistId		=	$row['checklistId'];
						$checklistQaTitle	=	stripslashes($row['checklistQaTitle']);
						$checkAnswer		=	$row['answer'];

						$checklistChk		=	"";
						$checklistChk1		=	"";
						$checklistChk2		=	"";
						if(!empty($a_existingProcessChecklist))
						{
							$checkedValue		=	$a_existingProcessChecklist[$checklistId];
							if($checkedValue	==	1)
							{
								$checklistChk	=	"checked";
							}
							elseif($checkedValue==	2)
							{
								$checklistChk1	=	"checked";
							}
							elseif($checkedValue==	3)
							{
								$checklistChk2	=	"checked";
							}
						}
				?>
				<tr>
					<td width="2%" class="textstyle" valign="top"><?php echo $countChecklist;?>)</td>
					<td valign="top">
						<font style="font-family:Trebuchet MS;color:#333333;font-size:16px;font-weight:normal;text-decoration:none;letter-spacing:0px;text-align:justify;">
							<?php echo $checklistQaTitle;?>
						</font>
						<input type="radio" name="readChecklist[<?php echo $countChecklist;?>]" value="1|<?php echo $checklistId;?>"  onclick="return isValidCheckAnswer(1,<?php echo $checkAnswer;?>)" <?php echo $checklistChk;?>>Yes
						<input type="radio" name="readChecklist[<?php echo $countChecklist;?>]" value="2|<?php echo $checklistId;?>"  onclick="return isValidCheckAnswer(2,<?php echo $checkAnswer;?>)" <?php echo $checklistChk1;?>>No
						<input type="radio" name="readChecklist[<?php echo $countChecklist;?>]" value="3|<?php echo $checklistId;?>" <?php echo $checklistChk2;?>>N/A
					</td>
				</tr>
				<?php
					}
				?>
				<tr>
					<td class="smlltext2" colspan="8">
						<input type="hidden" name="isChecklistAvailabale" value="1">
						<input type="hidden" name="totalChecklistExists" value="<?php echo $countChecklist;?>">
					</td>
				</tr>
			</table>
			<br>
			<?php
				}
				else
				{
			?>
			<input type="hidden" name="isChecklistAvailabale" value="0">
			<?php
				}
			?>
			</td>
		</tr>
		<tr>
			<td colspan="4" class="heading1">
				UPLOAD COMPLETED FILES 
			</td>
		</tr>
		<?php
				
				$t_replieddFileToustomer=	stringReplace("ACI","ACI/ZOO",$replieddFileToustomer);
				$t_replieddFileToustomer=	stringReplace(".aci",".aci/zoo",$t_replieddFileToustomer);
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
			<td colspan="3" height="10"></td>
		</tr>
		<tr>
			<td class="smalltext2"><b>Number of Comps filled</b><font color="#ff0000;">*</font></td>
			<td class="smalltext2"><b>:</b></td>
			<td>
				<input type="text" name="numberOfCompsFilled" size="10" value="<?php echo $numberOfCompsFilled;?>" onkeypress="return checkForNumber();">
			</td>
		</tr>
		<tr>
			<td class="smalltext2" valign="top"><b>Internal Employee Notes</b><font color="#ff0000;">*</font></td>
			<td class="smalltext2" valign="top"><b>:</b></td>
			<td>
				<input type="text" name="orderInteralNotes" size="60" value="<?php echo $orderInteralNotes;?>" onCopy="return false" onDrag="return false" onDrop="return false" onPaste="return false" autocomplete=off onKeyDown="textCounter(this.form.orderInteralNotes,this.form.remLentext1,100);" onKeyUp="textCounter(this.form.orderInteralNotes,this.form.remLentext1,100);">
				<br><font class="smalltex1t">Characters Left: <input type="textbox" readonly name="remLentext1" size=2 value="100" style="border:0"></font>
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
		<tr>
			<td class="smalltext2" valign="top"><b>PDF File of the Completd File</b></td>
			<td class="smalltext2" valign="top"><b>:</b></td>
			<td class="smalltext10" valign="top">
				<input type="file" name="pdfCompletedFile" id="checkPdfComFileId"  onchange="showMessagePDfFile(this.value);">
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
							<?php echo getFileSize($uploadingFileSize);?></font><br>
							<img src="<?php echo OFFLINE_IMAGE_PATH;?>/images/c_delete.gif" border="0" style="cursor:pointer;" onclick="deleteMultipleProcessedOrderFile(<?php echo $orderId;?>,<?php echo $customerId;?>,'Completed PDF',<?php echo $fileId;?>,7)" title="Delete <?php echo $replieddFileToustomer;?>">
							<?php
								}
							}
							else
							{
								echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=PDF&f=R'  class='link_style26'>".$compltetedPdfFileName.".".$compltetedPdfFileExt."</a>";
								
								echo "&nbsp;<font class='smalltext20'>".getFileSize($compltetedPdfFileSize)."</font><br>";
					?>
					<img src="<?php echo OFFLINE_IMAGE_PATH;?>/images/c_delete.gif" border="0" style="cursor:pointer;" onclick="deleteProcessedOrderFile(<?php echo $orderId;?>,<?php echo $customerId;?>,'PDF File',7)" title="Delete Completed PDF File">
					<?php
							}
						}
					?>
				</b>
			</td>
		</tr>
		<tr>
			
			<td class="smalltext2" width="25%" valign="top"><b><font color="#ff0000;"><?php echo $t_replieddFileToustomer;?></font></b></td>
			<td class="smalltext2"  width="2%" valign="top"><b>:</b></td>
			<td class="smalltext10" valign="top">
				<input type="file" name="replyOrderFile" id="appraisalTemplateFileId" onchange="showBox(<?php echo $appraisalSoftwareType;?>,this.value,'<?php echo $checkExistingOrderfileName;?>');checkReplyFileName('<?php echo $appraisalText;?>',this.value,'<?php echo $checkExistingOrderfileName;?>'); showUploadSizeError('appraisalTemplateFileId','Template File');">
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
							<?php echo getFileSize($uploadingFileSize);?></font><br>
							<img src="<?php echo OFFLINE_IMAGE_PATH;?>/images/c_delete.gif" border="0" style="cursor:pointer;" onclick="deleteMultipleProcessedOrderFile(<?php echo $orderId;?>,<?php echo $customerId;?>,'<?php echo $replieddFileToustomer;?>',<?php echo $fileId;?>,1)" title="Delete <?php echo $replieddFileToustomer;?>">
							<?php
								}
							}
							else
							{
								echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=OF&f=R'  class='link_style26'>".$replyOrderFileName.".".$replyOrderFileExt."</a>";
								
								echo "&nbsp;<font class='smalltext20'>".getFileSize($replyOrderFileSize)."</font><br>";
					?>
					<img src="<?php echo OFFLINE_IMAGE_PATH;?>/images/c_delete.gif" border="0" style="cursor:pointer;" onclick="deleteProcessedOrderFile(<?php echo $orderId;?>,<?php echo $customerId;?>,'<?php echo $replieddFileToustomer;?>',1)" title="Delete <?php echo $replieddFileToustomer;?>">
					<?php
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
							<?php echo getFileSize($uploadingFileSize);?></font><br>
							<img src="<?php echo OFFLINE_IMAGE_PATH;?>/images/c_delete.gif" border="0" style="cursor:pointer;" onclick="deleteMultipleProcessedOrderFile(<?php echo $orderId;?>,<?php echo $customerId;?>,'Upload Reply Public Records File',<?php echo $fileId;?>,2)" title="Delete Upload Reply Public Records File">
							<?php
								}
							}
							else
							{
							
							echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=PF&f=R' class='link_style26'>".$replyPublicRecordFileName.".".$replyPublicRecordFileExt."</a>";
							
							echo "&nbsp;<font class='smalltext20'>".getFileSize($replyPublicRecordSize)."</font><br>";
					?>
					<img src="<?php echo OFFLINE_IMAGE_PATH;?>/images/c_delete.gif" border="0" style="cursor:pointer;" onclick="deleteProcessedOrderFile(<?php echo $orderId;?>,<?php echo $customerId;?>,'Upload Reply Public Records File',2)" title="Delete Upload Reply Public Records File">
					<?php
							}
						}
					?>
				</b>
			</td>
		</tr>
		<tr>
			<td class="smalltext2" valign="top"><b>Upload Plat Map</b></td>
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
							<?php echo getFileSize($uploadingFileSize);?></font><br>
							<img src="<?php echo OFFLINE_IMAGE_PATH;?>/images/c_delete.gif" border="0" style="cursor:pointer;" onclick="deleteMultipleProcessedOrderFile(<?php echo $orderId;?>,<?php echo $customerId;?>,'Upload Plat Map',<?php echo $fileId;?>,3)" title="Delete Upload Plat Map">
							<?php
								}
							}
							else
							{
								echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=MF&f=R' class='link_style26'>".$replyMlsFileName.".".$replyMlsFileExt."</a>";
								echo "&nbsp;<font class='smalltext20'>".getFileSize($replyMlsFileSize)."</font><br>";
					?>
					<img src="<?php echo OFFLINE_IMAGE_PATH;?>/images/c_delete.gif" border="0" style="cursor:pointer;" onclick="deleteProcessedOrderFile(<?php echo $orderId;?>,<?php echo $customerId;?>,'Upload Plat Map',3)" title="Delete Upload Plat Map">
					<?php
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
				<input type="file" name="otherFile"  id="otherFileId" onchange="showUploadSizeError('otherFileId','Reply Other');">
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
								<?php echo getFileSize($uploadingFileSize);?></font><br>
								<img src="<?php echo OFFLINE_IMAGE_PATH;?>/images/c_delete.gif" border="0" style="cursor:pointer;" onclick="deleteMultipleProcessedOrderFile(<?php echo $orderId;?>,<?php echo $customerId;?>,'Upload Reply Other File',<?php echo $fileId;?>,6)" title="Delete Upload Reply Other File">
								<?php
								}
							}
							else
							{
							
							echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=OTF&f=R'  class='link_style26'>".$otherFileName.".".$otherFileExt."</a>";
							echo "&nbsp;<font class='smalltext20'>".getFileSize($replyOtherFileSize)."</font><br>";
					?>
					<img src="<?php echo OFFLINE_IMAGE_PATH;?>/images/c_delete.gif" border="0" style="cursor:pointer;" onclick="deleteProcessedOrderFile(<?php echo $orderId;?>,<?php echo $customerId;?>,'Upload Reply Other File',4)" title="Delete Upload Reply Other File">
					<?php
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
			<td class="smalltext2" valign="top"><b>Reply Instructions For Customer</b></td>
			<td class="smalltext2"  valign="top"><b>:</b></td>
			<td  valign="top">
				<textarea name="replyInstructions" rows="8" cols="45"><?php echo stripslashes(htmlentities($replyInstructions,ENT_QUOTES))?></textarea>
			</td>
		</tr>
	<?php
		}	
	?>
		<tr>
			<td colspan="3" height="30"></td>
		</tr>
		<tr>
			<td class="smalltext2" valign="top"><b>Add Comments For QA Person</b></td>
			<td class="smalltext2"  valign="top"><b>:</b></td>
			<td  valign="top">
				<input type="text" name="commentsToQa" size="60" value="<?php echo $commentsToQa;?>">
			</td>
		</tr>
		<tr>
			<td colspan="3" height="30"></td>
		</tr>
		<tr>
			<td class="smalltext2" valign="top"><b>Time Spent On This Order</b></td>
			<td class="smalltext2"  valign="top"><b>:</b></td>
			<td  valign="top">
				<input type="text" name="timeSpentEmployee" size="10" value="<?php echo $timeSpentEmployee;?>" onkeypress="return checkForNumber();"> Minutes
			</td>
		</tr>
		<!--<tr>
			<td class="smalltext2" valign="top"><b>Total Time Spent</b></td>
			<td class="smalltext2" valign="top"><b>:</b></td>
			<td  valign="top" class="smalltext1">
				<input type="text" name="timeSpentEmployee" size="10" value="<?php echo $timeSpentEmployee;?>" onKeyPress="return checkForNumber();">(IN MINUTES)
			</td>
		</tr>-->
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
				<input type="button" name="backBtn" onClick="history.back()" value="Back">
				<input type="hidden" name="hasAdminMessage" value="<?php echo $hasAdminMessage;?>">
				<input type="hidden" name="hasCompletedPdfFile" value="<?php echo $hasCompletedPdfFile;?>">
				<input type="hidden" name="formSubmitted" value="1">
			</td>
		</table>
	</form>