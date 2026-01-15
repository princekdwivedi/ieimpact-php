<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>
<script type="text/javascript">
function display_loading()
{
	document.getElementById('loading').style.display = 'block';
} 
function changeEnableDisable(flag)
{
	if(flag	==	1)
	{
		document.getElementById("messageWriteID").disabled = false;
		document.getElementById("ends2").disabled = true;
	}
	else
	{
		document.getElementById("messageWriteID").disabled = false;
	}
}
function checkValidMessage()
{
	form1	=	document.sendCustomerMessage;
	if(form1.adminMessageId.value == "0")
	{
		alert("Please Select a Message.");
		form1.adminMessageId.focus();
		return false;
	}
	if(form1.message.value == "" || form1.message.value == "Enter Your Message Here" || form1.message.value == " " || form1.message.value == "0")
	{
		alert("Please Enter Your Message.");
		form1.message.focus();
		return false;
	}
	form1.submit.value    = "Sending... Please wait";
	form1.submit.disabled = true;

	display_loading();
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
 function makeDisplayDiv()
 {
	document.getElementById('displayCustomMessage').style.display = 'inline';
 }
 function checkCustomMessage(flag)
 {
	if(flag  == -1)
	{
		document.getElementById('displayCustomMessage').style.display = 'inline';
	}
	else 
	{
		document.getElementById('displayCustomMessage').style.display = 'none';
	}
 }
</script>
<br>
<a name="sendMessages"></a>
<form name="sendCustomerMessage" action=""  method="POST" enctype="multipart/form-data" onsubmit="return checkValidMessage();">
	<table width='100%' align='center' cellpadding='3' cellspacing='0' border='0'>
		<tr>
			<td colspan="3" class="smalltext23"><b>SEND MESSAGE TO CUSTOMER</b></td>
		</tr>
		<tr>
			<td colspan="3" height="5"></td>
		</tr>
		<?php
			if(!empty($errorMsg))
			{
		?>
		<tr>
			<td colspan="3"><?php echo $errorMsg;?></td>
		</tr>
		<tr>
			<td colspan="3" height="5"></td>
		</tr>
		<?php
			}
		?>
		<tr>
			<td class="smalltext2"><b>Select a message</b></td>
			<td class="smalltext2"><b>:</b></td>
			<td>
				<?php
					$url	=	SITE_URL_EMPLOYEES."/get-all-admin-order-messages1.php?existingMessageId=".$existingMessageId."&messageId=";
				?>
				<select name="adminMessageId" onchange="commonFunc('<?php echo $url?>','displayCustomMessage',this.value);makeDisplayDiv();" style="border:1px solid #333333;font-family:verdana;font-size:12px;">
					<option value="0">Select</option>
					<?php 
						foreach($a_orderAdminMessages as $key=>$value)
						{
							$select		=	"";
							if($key		==	$adminMessageId)
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
			<td colspan="3">
				<div id="displayCustomMessage" style="display:<?php echo $displayMainMessageDiv;?>">
					<textarea name="message" rows="9" cols="80" id="messageWriteID" wrap="hard" onKeyDown="textCounter(this.form.message,this.form.remLentext1,1000);" onKeyUp="textCounter(this.form.message,this.form.remLentext1,1000);" onFocus="if(this.value=='Enter Your Message Here') this.value='';" onBlur="if(this.value=='') this.value='Enter Your Message Here';" style="border:1px solid #333333;font-family:verdana;font-size:12px;"><?php echo $t_adminMessage;?></textarea><input type='hidden' name='isSendingForverify' value='2'>
				</div>
			</td>
		</tr>
		<?php
			if(empty($isDeleted))
			{
		?>
		<tr>
			<td height="5" colspan="3"></td>
		</tr>
		<tr>
			<td width="15%" class="smalltext2"><b>Upload A File</b></td>
			<td width="2%" class="smalltext2"><b>:</b></td>
			<td>
				<input type="file" name="messageFile">
			</td>
		</tr>
		<tr>
			<td height="5" colspan="3"></td>
		</tr>
		<?php
				if(!empty($isHavingExistingFile))
				{
		?>
		<tr>
			<td class="smalltext2" valign="top"><b>Existing File Uploaded</b></td>
			<td class="smalltext2" valign="top"><b>:</b></td>
			<td>
				<?php
					if($isNewUploadingSystem == 1)
					{
						if($result1			=	$orderObj->getOrdereMessageFile($orderId,$messageId,3,7))
						{
							while($row1			=	mysqli_fetch_assoc($result1))
							{
								$fileId			=	$row1['fileId'];
								$fileName		=	stripslashes($row1['uploadingFileName']);
								$fileExtension	=	$row1['uploadingFileExt'];
								$fileSize		=	$row1['uploadingFileSize'];
								$imageOnServerPath	=	$row1['excatFileNameInServer'];

								$base_fileId	=	base64_encode($fileId);
								
								$downLoadPath	=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;

					?>
						<a class="link_style32" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download Message File" style="cursor:pointer;"><?php echo $fileName.".".$fileExtension;?></a>&nbsp;&nbsp;<font class='smalltext20'><?php echo getFileSize($fileSize);?></font>
					<?php
							if(in_array($fileExtension,$a_displayAnyImageOfType) && $fileSize <= "3145728")
							{
								list($imgWidth, $imgHeight, $type, $attr) = getimagesize($imageOnServerPath);

								if($imgWidth > 600 || $imgHeight > 400)
								{
									$imageWidth	=	"width='600'";
									$imageHeight=	"height='400'";
								}
								else
								{
									$imageWidth	=	"";
									$imageHeight=	"";
								}
							?>
							<br /><br /><img src="<?php echo SITE_URL_EMPLOYEES;?>/get-employee-message-image.php?memberId=<?php echo $customerId;?>&orderId=<?php echo $orderId;?>&messageId=<?php echo $messageId;?>&isNewSystem=1" border="0" title="<?php echo $fileName.".".$fileExtension;?>" <?php echo $imageWidth;?> <?php echo $imageHeight;?> onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');"  style="cursor:pointer">
							<?php
								}
							}
						}
					}
					else
					{
						echo "<a href='".SITE_URL_EMPLOYEES."/download-message-files.php?ID=$messageId'  class='link_style32'>".$t_fileName.".".$t_fileExtension."</a>&nbsp;&nbsp;<font class='smalltext'>".getFileSize($t_fileSize)."</font>";
						if(in_array($fileExtension,$a_displayAnyImageOfType) && $fileSize <= "3145728")
						{
							$messageFilePath			=	SITE_ROOT_FILES."/files/messages/".$messageId."_".$t_fileName.".".$t_fileExtension;

							list($imgWidth, $imgHeight, $type, $attr) = getimagesize($messageFilePath);

							if($imgWidth > 600 || $imgHeight > 400)
							{
								$imageWidth	=	"width='600'";
								$imageHeight=	"height='400'";
							}
							else
							{
								$imageWidth	=	"";
								$imageHeight=	"";
							}

					?>
							<br /><br /><a href="<?php echo SITE_URL_EMPLOYEES;?>/download-message-files.php?ID=<?php echo $messageId;?>"><img src="<?php echo SITE_URL_EMPLOYEES;?>/get-employee-message-image.php?memberId=<?php echo $customerId;?>&orderId=<?php echo $orderId;?>&messageId=<?php echo $messageId;?>&isNewSystem=0" border="0" title="<?php echo $t_fileName.".".$t_fileExtension;?>" <?php echo $imageWidth;?> <?php echo $imageHeight;?>></a>
					<?php
						}
					}
				?>
			</td>
		</tr>
		<tr>
			<td height="5" colspan="3"></td>
		</tr>
		<?php
				}
			}
			if(!empty($smsCustomerMobileNo))
			{
		?>
		<tr>
			<td class="smalltext2" colspan="3">
				ALSO Click this box to SEND this message as SMS to customer if urgent..<input type="checkbox" name="markedImportantSendSms" value="1" <?php echo $checkedSms;?>>
			</td>
		</tr>
		<tr>
			<td height="5" colspan="3"></td>
		</tr>
		<?php
			}
			if(!empty($isOrderChecked) && !empty($orderCheckedBy)){
		?>
		<tr>
			<td class="smalltext2" colspan="3">
				Click here if order files properly not checked &nbsp;<input type="checkbox" name="filesNotProperlyChecked" value="1" <?php echo $checkedSms;?>>
			</td>
		</tr>
		<tr>
			<td height="5" colspan="3"></td>
		</tr>
		<?php
			}
		?>
		<tr>
			<td colspan="3">
				<div id="loading" style="display: none;"><img src="<?php echo OFFLINE_IMAGE_PATH;?>/images/ajax-loader.gif" alt="" /></div> 
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<input type="submit" name="submit" value="Submit">
				<input type="button" name="back" onClick="history.back()" value="Back">
				<input type="hidden" name="isHavingExistingFile" value="<?php echo $isHavingExistingFile;?>">
				<input type="hidden" name="formSubmitted" value="1">
				&nbsp;
				<?php
					include(SITE_ROOT_EMPLOYEES . "/includes/next-previous-order.php");
				?>
			</td>
		</tr>
	</table>
</form>