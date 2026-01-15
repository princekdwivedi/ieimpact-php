<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES .  "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES .  "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES .  "/classes/employee.php");
	include(SITE_ROOT			.  "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/check-pdf-login.php");
	include(SITE_ROOT_MEMBERS	.  "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	.  "/classes/orders.php");
	include(SITE_ROOT			.  "/classes/email-track-reading.php");

	$employeeObj				=  new employee();
	$memberObj					=  new members();
	$orderObj					=  new orders();
	$emailTrackObj				=  new trackReading();

	$messgeText					=  "SEND";
	$customerEmail				=  "";
	$customerSecondaryEmail		=  "";
	
	$section					=	1;
	$a_orderAdminMessages		=	array();
	$displayCustomMessage		=	"none";
	$adminMessadeId				=	0;
	$hasAdminMessage			=	0;

	$query						=	"SELECT * FROM admin_added_customer_messages WHERE section=$section ORDER BY message";
	$result						=	dbQuery($query);
	if(mysql_num_rows($result))
	{
		$hasAdminMessage		=	1;
		$a_orderAdminMessages['-1']	=	"<font color='#ff0000'>Not found suitable message, add own message</font>";
		while($row				    =	mysql_fetch_assoc($result))
		{
			$t_adminMessadeId	    =	$row['messageId'];
			$t_adminMessageLevel    =	stripslashes($row['messageLevel']);

			$a_orderAdminMessages[$t_adminMessadeId]	=	$t_adminMessageLevel;
		}
		
	}

	$uploadFileText				=	"";
	$uploadFileName				=	"";
	$uploadFileComma			=	"";
	if(isset($_GET['orderId']) && isset($_GET['customerId']))
	{
		$orderId		=	$_GET['orderId'];
		$customerId		=	$_GET['customerId'];
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
		exit();
	}
	$newMssageToEmployee	=	"";
	$messageId				=	0;
	if(isset($_GET['messageId']))
	{
		$messageId		=	(int)$_GET['messageId'];
		$message		=	@mysql_result(dbQuery("SELECT message FROM members_employee_messages WHERE orderId=$orderId AND memberId=$customerId AND messageBy='".EMPLOYEES."' AND messageId=$messageId"),0);
		if(empty($message))
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
			exit();
		}
		else
		{
			$newMssageToEmployee =  $message;
			$messgeText			 =  "EDIT";
		}
	}
?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
<tr>
	<td colspan="7" height="20"></td>
</tr>
<tr>
	<td colspan="8" class="heading1">
		:: SEND MESSAGE TO CUSTOMER ON ORDER ::
	</td>
</tr>
<tr>
	<td colspan="8" height="5"></td>
</tr>
</table>
<?php
	include(SITE_ROOT_EMPLOYEES	. "/includes/view-customer-order.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/view-reply-details.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/view-feedback-details.php");

	function findexts($filename) 
	{ 
		$filename = strtolower($filename) ; 
		$exts	  = split("[/\\.]", $filename) ; 
		$n		  = count($exts)-1; 
		$exts     = $exts[$n]; 
		return $exts; 
	} 
	function getFileName($fileName)
	{
		$fileName		=  stripslashes($fileName);
		$dotPosition	=  strpos($fileName, "'");
		if($dotPosition == true)
		{
			$fileName	=	str_replace("'", "", $fileName);
		}
		$doubleDotPosition	  =  strpos($fileName, '"');
		if($doubleDotPosition == true)
		{
			$fileName	=	str_replace('"', '', $fileName);
		}
		$fileName		=	str_replace("/", '', $fileName);
		$fileName		=	str_replace(":", '', $fileName);
		$fileName		=	str_replace("&", '', $fileName);
		$fileName		=	str_replace("*", '', $fileName);
		$fileName		=	str_replace("?", '', $fileName);
		$fileName		=	str_replace("|", '', $fileName);
		$fileName		=	str_replace("<", '', $fileName);
		$fileName		=	str_replace(">", '', $fileName);
		$fileExtPos		=   strrpos($fileName, '.');
		$fileName		=   substr($fileName,0,$fileExtPos);
		
		return $fileName;
	}

	$messageFilePath =	SITE_ROOT_FILES."/files/messages/";

	if($result	=	$orderObj->getOrderMessages($orderId,$customerId))
	{
?>
<br>
<a name="messages"></a>
<table width='100%' align='center' cellpadding='3' cellspacing='2' border='0'>
	<tr>
		<td colspan="2" class="text"><?php echo $messgeText;?> BETWEEN CUSTOMER AND EMPLOYEE</td>
	</tr>
	<tr>
		<td colspan="2" height="5"></td>
	</tr>
<?php
		while($row			=	mysql_fetch_assoc($result))
		{
			$t_messageId	=	$row['messageId'];
			$t_message		=	stripslashes($row['message']);
			$addedOn		=	showDate($row['addedOn']);
			$addedTime		=	$row['addedTime'];
			$messageBy		=	$row['messageBy'];
			$hasMessageFiles=	$row['hasMessageFiles'];
			$fileName		=	$row['fileName'];
			$fileExtension	=	$row['fileExtension'];
			$fileSize		=	$row['fileSize'];
			$emailUniqueCode=	$row['emailUniqueCode'];
			$readEmailText	=	"";
			if($messageBy   ==  EMPLOYEES)
			{
				$employeeId		=	$row['employeeId'];
				$employeeName	=	$employeeObj->getEmployeeName($employeeId);
				echo "<tr><td width='80%' class='smalltext2'><b>Message From ".$employeeName." to - ".$customerName." on $addedOn</b></td><td>";
				//<a href='".SITE_URL_EMPLOYEES."/send-message-pdf-customer.php?orderId=$orderId&customerId=$customerId&messageId=$t_messageId' class='link_style2'>Edit This Message</a>
				echo "</td></tr>";
				if($readDateIp	=	$employeeObj->getFirstEmailReadTime($emailUniqueCode))
				{
					list($readDate,$readTime)	=	explode("|",$readDateIp);
					$readEmailText =	"&nbsp;(<font color='#ff0000'>Customer Read At - ".showDate($readDate)." EST  at - ".showTimeFormat($readTime)." Hrs</font>)";
				}
			}
			elseif($messageBy   ==  CUSTOMERS)
			{
				echo "<tr><td width='80%' class='smalltext2' colspan='2'><b>Message From ".$customerName." to Employee  on $addedOn</b></td></tr>";
			}
			echo "<tr><td colspan='2' class='smalltext2'>".nl2br($t_message).$readEmailText."</td></tr>";
			if($hasMessageFiles == 1)
			{
				echo "<tr><td colspan='2' class='title3'><b>Uploaded File : </b>";
				if($isNewUploadingSystem == 1)
				{
					if($result1			=	$orderObj->getOrdereMessageFile($orderId,$t_messageId,3,7))
					{
						$row1			=	mysql_fetch_assoc($result1);
						$fileId			=	$row1['fileId'];
						$fileName		=	stripslashes($row1['uploadingFileName']);
						$fileExtension	=	$row1['uploadingFileExt'];
						$fileSize		=	$row1['uploadingFileSize'];

						$base_fileId	=	base64_encode($fileId);
						
						$downLoadPath	=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
					?>
						<a class="link_style26" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download Message File" style="cursor:pointer;"><?php echo $fileName.".".$fileExtension;?></a>&nbsp;&nbsp;<font class='smalltext20'><?php echo getFileSize($fileSize);?></font>
					<?php
					}
				}
				else
				{
					echo "<a href='".SITE_URL_EMPLOYEES."/download-message-files.php?ID=$t_messageId'  class='linkstyle6'><b>".$fileName.".".$fileExtension."</b></a>&nbsp;&nbsp;<font class='smalltext'>".getFileSize($fileSize)."</font>";
				}
				echo "</td></tr>";
			}
			echo "<tr><td colspan='2'><hr size='1' width='100%' color='#bebebe'></td></tr>";
		}
?>
</table>
<?php
	}
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		if($message		==	"Enter Your Message Here")
		{
			$message	=	"";
		}
		//$message		=	addslashes($message);
		if(empty($messageId) && !empty($message))
		{
			if($hasAdminMessage	==	1)
			{
				$message			=	trim($message);
				$message			=	makeDBSafe($message);
				
				$query	=	"INSERT INTO members_employee_messages SET orderId=$orderId,memberId=$customerId,employeeId=$s_employeeId,message='$message',parentId=0,addedOn='$nowDateIndia',addedTime='$nowTimeIndia',messageBy='".EMPLOYEES."',adminAddedMessageId='$adminMessadeId',estDate='".CURRENT_DATE_CUSTOMER_ZONE."',estTime='".CURRENT_TIME_CUSTOMER_ZONE."'";
				dbQuery($query);
				$messageId			=	mysql_insert_id();
			}
			else
			{
				$message=	makeDBSafe($message);
				$query	=	"INSERT INTO members_employee_messages SET orderId=$orderId,memberId=$customerId,employeeId=$s_employeeId,message='$message',parentId=0,addedOn='$nowDateIndia',addedTime='$nowTimeIndia',messageBy='".EMPLOYEES."',estDate='".CURRENT_DATE_CUSTOMER_ZONE."',estTime='".CURRENT_TIME_CUSTOMER_ZONE."'";
				dbQuery($query);
				$messageId		=	mysql_insert_id();
			}
			dbQuery("UPDATE members_orders SET isHavingOrderNewMessage=0 WHERE orderId=$orderId AND memberId=$customerId");

			dbQuery("UPDATE members_employee_messages SET isRepliedMessage=1 WHERE orderId=$orderId AND memberId=$customerId");

			dbQuery("UPDATE members SET isHavingEmployeeOrderMessage=1 WHERE memberId=$customerId");

			if(!empty($_FILES['messageFile']['name']))
			{
				$uploadingFile		=   $_FILES['messageFile']['name'];
				$mimeType			=   $_FILES['messageFile']['type'];
				$fileSize			=   $_FILES['messageFile']['size'];
				$tempName			=	$_FILES['messageFile']['tmp_name'];
				$ext				=	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);


				$fileName			= $messageId."_".$uploadingFileName.".".$ext;

				if($isNewUploadingSystem == 1)
				{
					$t_uploadingFile	=	makeDBSafe($uploadingFileName);

					dbQuery("INSERT INTO order_all_files SET uploadingType=7,uploadingFor=3,orderId=$orderId,memberId=$customerId,uploadingFileName='$t_uploadingFile',uploadingFileExt='$ext',uploadingFileType='$mimeType',uploadingFileSize=$fileSize,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',customerZoneDate='".CURRENT_DATE_CUSTOMER_ZONE."',customerZoneTime='".CURRENT_TIME_CUSTOMER_ZONE."',addedFromIp='".VISITOR_IP_ADDRESS."',messageId=$messageId");

					$fileId					=	mysql_insert_id();

					$destFileName			=	$newUploadingPath."/".$fileId."_".$uploadingFileName.".".$ext;

					move_uploaded_file($tempName,$destFileName);

					dbQuery("UPDATE order_all_files SET excatFileNameInServer='$destFileName' WHERE fileId=$fileId AND orderId=$orderId AND messageId=$messageId");

					dbQuery("UPDATE members_employee_messages SET hasMessageFiles=1 WHERE messageId=$messageId");
				}
				else
				{
					move_uploaded_file($tempName,$messageFilePath.$fileName);
								
					dbQuery("UPDATE members_employee_messages SET hasMessageFiles=1,fileExtension='$ext',fileName='$uploadingFileName',fileMimeType='$mimeType',fileSize=$fileSize WHERE messageId=$messageId");
				}

				move_uploaded_file($tempName,$messageFilePath.$fileName);
				
				dbQuery("UPDATE members_employee_messages SET hasMessageFiles=1,fileExtension='$ext',fileName='$uploadingFileName',fileMimeType='$mimeType',fileSize=$fileSize WHERE messageId=$messageId");

				$uploadFileText		=	"Uploaded File";
				$uploadFileName		=	$uploadingFileName.".".$ext;
				$uploadFileComma	=	":";

			}

			$performedTask	=	"Send Message To - ".$customerId." On Order No - ".$orderId;

			include(SITE_ROOT		.   "/classes/email-templates.php");
			$emailObj			    =	new emails();

			
			$n_message		=	@mysql_result(dbQuery("SELECT message FROM members_employee_messages WHERE orderId=$orderId AND memberId=$customerId AND messageId=$messageId"),0);

			$n_message		=	stripslashes($n_message);
			$subject_message=	$n_message;
			$n_message		=	nl2br($n_message);
			$smsOrderMsg	=	$subject_message;
			$mailSubject	=	getSubstring($subject_message,75);
			$orderForText	=	"You have a message for your order";

			$a_templateSubject			=	array("{emailOwnSubject}"=>$mailSubject);

			$uniqueTemplateName			=	"TEMPLATE_SENDING_MESSAGE_CUSTOMER_MANAGER";
			if($hasReceiveEmails		== 0)
			{
				$trackEmailImage		=	$emailTrackObj->addTrackEmailRead($customerEmail,$mailSubject,"orders@ieimpact.com",$customerId,1,9,3,$s_employeeName,$s_employeeId);

				if($trackEmailImage		!=  "images/white-space.jpg")
				{
					$sendingUniqueCode	 =	str_replace("mail-t/","",$trackEmailImage);
					$sendingUniqueCode	 =	str_replace(".jpg","",$sendingUniqueCode);

					dbQuery("UPDATE members_employee_messages SET emailUniqueCode='$sendingUniqueCode' WHERE messageId=$messageId AND memberId=$customerId");
				}
					

				$a_templateData			=	array("{orderForText}"=>$orderForText,"{orderNo}"=>$orderAddress,"{orderText}"=>$orderText,"{name}"=>$firstName,"{orderText}"=>$orderText,"{message}"=>$n_message,"{uploadFileText}"=>$uploadFileText,"{uploadFileName}"=>$uploadFileName,"{uploadFileComma}"=>$uploadFileComma,"{trackEmailImage}"=>$trackEmailImage);
				
				$toEmail			=	$customerEmail;
				include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

				if(!empty($customerSecondaryEmail))
				{
					$trackEmailImage		=	"images/white-space.jpg";

					$a_templateData			=	array("{orderForText}"=>$orderForText,"{orderNo}"=>$orderAddress,"{orderText}"=>$orderText,"{name}"=>$firstName,"{orderText}"=>$orderText,"{message}"=>$n_message,"{uploadFileText}"=>$uploadFileText,"{uploadFileName}"=>$uploadFileName,"{uploadFileComma}"=>$uploadFileComma,"{trackEmailImage}"=>$trackEmailImage);

					$toEmail				=	$customerSecondaryEmail;
					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
				}
			}

			$a_managerEmails		=	$orderObj->getMangersOnlyEmails();
			$trackEmailImage		=	"images/white-space.jpg";
			if(!empty($a_managerEmails))
			{
				$manager_message	=	"";
				$mailSubject		=	getSubstring($subject_message,75);
				$manager_message	=	$n_message."<br><br><b>Sent By Employee </b>: <b>".$s_employeeName."</b>";
				
				$orderForText	   =	"Customer ".$customerName." have a message his/her order";

				$a_templateData			=	array("{orderForText}"=>$orderForText,"{orderNo}"=>$orderAddress,"{orderText}"=>$orderText,"{name}"=>"Manager","{orderText}"=>$orderText,"{message}"=>$manager_message,"{uploadFileText}"=>$uploadFileText,"{uploadFileName}"=>$uploadFileName,"{uploadFileComma}"=>$uploadFileComma,"{trackEmailImage}"=>$trackEmailImage);

				$a_templateSubject		=	array("{emailOwnSubject}"=>$mailSubject);

				$uniqueTemplateName		=	"TEMPLATE_SENDING_MESSAGE_CUSTOMER_MANAGER";
				$toEmail				=	"gaurabieimpact@yahoo.com";
				$managerEmployeeFromBcc=	$a_managerEmails;
				include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
			}
		}
		else
		{
			$query	=	"UPDATE members_employee_messages SET message='$message' WHERE orderId=$orderId AND memberId=$customerId AND messageId=$messageId";
			dbQuery($query);

			$performedTask	=	"Update Message To - ".$customerId." On Order No - ".$orderId;
		}
		
		if(isset($_POST['markedImportantSendSms']) && !empty($smsCustomerMobileNo))
		{
							
			$displaySmsOrderNo =  subString($orderAddress,10);
			$smsMessage		   =  "MSG from ieIMPACT : ".$displaySmsOrderNo." : ".$smsOrderMsg;

			$smsMessage		   =	str_replace("<br>", " ", $smsMessage);
			$smsMessage		   =	str_replace("</ br>", " ", $smsMessage);
			$smsMessage		   =	str_replace("</ br>", " ", $smsMessage);
			$smsReferenceID	   =    $orderId."-".rand(11,99)."-".substr(md5(microtime()+rand()+date('s')),0,5);

			$smsReturnPath	   =	"http://www.ieimpact.com/read-sms-postback.php"; 

			$smsKey			   =	SMS_CDYNE_KEY;
			$client			   =	new SoapClient('http://sms2.cdyne.com/sms.svc?wsdl');
		
			$lk				   =	$smsKey;

			class AdvancedCallRequestData
			{
			  public $AdvancedRequest;
			 
			  function AdvancedCallRequestData($licensekey,$requests)
			  { 
						$this->AdvancedRequest = array();
						$this->AdvancedRequest['LicenseKey'] = $licensekey;
						$this->AdvancedRequest['SMSRequests'] = $requests;
			  }
			}
			 
			$PhoneNumbersArray1=    array($smsCustomerMobileNo);
							 
			$RequestArray = array(
				array(
					'AssignedDID'=>'',
										  //If you have a Dedicated Line, you would assign it here.
					'Message'=>$smsMessage,   
					'PhoneNumbers'=>$PhoneNumbersArray1,
					'ReferenceID'=>$smsReferenceID,
										  //User defined reference, set a reference and use it with other SMS functions.
					//'ScheduledDateTime'=>'2010-05-06T16:06:00Z',
										  //This must be a UTC time.  Only Necessary if you want the message to send at a later time.
					'StatusPostBackURL'=>$smsReturnPath 
										  //Your Post Back URL for responses.
				)
			);
			 
			$request		=   new AdvancedCallRequestData($smsKey,$RequestArray);
			//pr($request);
			$result			=   $client->AdvancedSMSsend($request);
			//pr($request);
			$result1		=	convertObjectToArray($result);
			//pr($result1);
			$mainResult	    =	$result1['AdvancedSMSsendResult'];
			$a_mainSmsResult=	$mainResult['SMSResponse'];
			//pr($a_mainSmsResult);
			$cancelled		=	$a_mainSmsResult['Cancelled'];
			if(empty($cancelled))
			{
				$cancelled	=	"";
			}
			$smsMessageID	=	$a_mainSmsResult['MessageID'];
			if(empty($smsMessageID))
			{
				$smsMessageID	=	"";
			}
			$smsReferenceID	=	$a_mainSmsResult['ReferenceID'];
			if(empty($smsReferenceID))
			{
				$smsReferenceID	=	"";
			}
			$queued			=	$a_mainSmsResult['Queued'];
			if(empty($queued))
			{
				$queued		=	"";
			}
			$smsError		=	$a_mainSmsResult['SMSError'];
			if(empty($smsError))
			{
				$smsError	=	"";
			}
			$smsMessage		=	makeDBSafe($smsMessage);

			$newSmsID= $orderObj->addOrderMessageSms($cancelled,$smsReferenceID,$orderId,$customerId,$s_employeeId,$smsMessageID,$queued,$smsError,$smsMessage,$smsCustomerMobileNo);

			dbQuery("UPDATE members_employee_messages SET isFromSms=1,smsId=$newSmsID WHERE orderId=$orderId AND memberId=$customerId AND messageId=$messageId");
		}

		$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES ."/send-message-pdf-customer.php?orderId=$orderId&customerId=$customerId");
		exit();
	}
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>
<script type="text/javascript">
function display_loading()
{
	document.getElementById('loading').style.display = 'block';
} 
function checkValidMessage()
{
	form1	=	document.sendCustomerMessage;
	if(form1.hasAdminMessage.value	==	"0")
	{
		if(form1.message.value == "" || form1.message.value == "Enter Your Message Here")
		{
			alert("Please Enter Your Message !!");
			form1.message.focus();
			return false;
		}
	}
	else
	{
		if(form1.adminMessadeId.value == "0")
		{
			alert("Please Select a Message !!");
			form1.adminMessadeId.focus();
			return false;
		}
		if(form1.adminMessadeId.value	==	"-1")
		{
			if(form1.message.value == "" || form1.message.value == "Enter Your Message Here")
			{
				alert("Please Enter Your Message !!");
				form1.message.focus();
				return false;
			}
		}
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
	<td colspan="3" class="text">SEND MESSAGE TO CUSTOMER</td>
</tr>
<tr>
	<td colspan="3" height="5"></td>
</tr>
<?php 
	if(!empty($a_orderAdminMessages))
	{
?>
<tr>
	<td class="smalltext2"><b>Select a message</b></td>
	<td class="smalltext2"><b>:</b></td>
	<td>
		<?php
			$url	=	SITE_URL_EMPLOYEES."/get-all-admin-order-messages.php?messageId=";
		?>
		<select name="adminMessadeId" onchange="commonFunc('<?php echo $url?>','displayCustomMessage',this.value);">
			<option value="0">Select</option>
			<?php 
				foreach($a_orderAdminMessages as $key=>$value)
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
	<td colspan="3">
		<div id="displayCustomMessage"></div>
	</td>
</tr>
<?php	
	}
	else
	{
?>
<tr>
	<td valign="top" colspan="3">
		<textarea name="message" rows="7" cols="70" wrap="hard" onKeyDown="textCounter(this.form.message,this.form.remLentext1,1000);" onKeyUp="textCounter(this.form.message,this.form.remLentext1,1000);" onFocus="if(this.value=='Enter Your Message Here') this.value='';" onBlur="if(this.value=='') this.value='Enter Your Message Here';"><?php echo stripslashes(htmlentities($newMssageToEmployee,ENT_QUOTES))?></textarea>

		<br><font class="smalltext2">Characters Left: <input type="textbox" readonly name="remLentext1" size=2 value="1000" style="border:0"></font>
	</td>
</tr>
<tr>
	<td height="5" colspan="3"></td>
</tr>
<?php
	}
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
	}
	if(!empty($smsCustomerMobileNo))
	{
?>
<tr>
	<td class="smalltext2" colspan="3">
		<input type="checkbox" name="markedImportantSendSms" value="1" checked>&nbsp;  ALSO Click this box to SEND this message as SMS to customer if urgent..
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
		<input type="hidden" name="hasAdminMessage" value="<?php echo $hasAdminMessage;?>">
		<input type="hidden" name="formSubmitted" value="1">
		&nbsp;
		<?php
			include(SITE_ROOT_EMPLOYEES . "/includes/next-previous-order.php");
		?>
	</td>
</tr>
</table>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>
