<?php
	ob_start();
	session_start();
	//ini_set('display_errors', '1');
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES .  "/includes/new-top.php");
	include(SITE_ROOT_EMPLOYEES .  "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES .  "/classes/employee.php");
	include(SITE_ROOT			.  "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/check-pdf-login.php");
	include(SITE_ROOT_MEMBERS	.  "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	.  "/classes/orders.php");
	include(SITE_ROOT			.  "/classes/email-track-reading.php");
	include(SITE_ROOT			.  "/classes/validate-fields.php");

	if($s_employeeId == 3){
?>
<script type='text/javascript'>
function showCommentsAlert()
{
	jQuery.facebox({ajax: "<?php echo SITE_URL_EMPLOYEES;?>/testing.php"});
}
</script>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/facebox.css" media="screen" rel="stylesheet" type="text/css" />
<script src="<?php  echo SITE_URL;?>/script/facebox.js" type="text/javascript"></script>
<script type="text/javascript">
	//showCommentsAlert();
window.onload =	showCommentsAlert;
</script>
<?php
	}

	$employeeObj				=  new employee();
	$memberObj					=  new members();
	$orderObj					=  new orders();
	$emailTrackObj				=  new trackReading();
	$validator					=  new validate();

	$messgeText					=  "SEND";
	$customerEmail				=  "";
	$customerSecondaryEmail		=  "";
	
	$section					=	1;
	$a_orderAdminMessages		=	array();
	$displayCustomMessage		=	"none";
	$adminMessageId				=	0;
	$hasAdminMessage			=	0;
	$a_allDeactivatedEmployees  =	$employeeObj->getAllInactiveEmployees();

	$formSearch					=	SITE_ROOT_EMPLOYEES."/forms/search-general-order-form.php";

	$query						=	"SELECT * FROM admin_added_customer_messages WHERE section=$section ORDER BY message";
	$result						=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		$hasAdminMessage		=	1;
		$a_orderAdminMessages['-1']	=	"<font color='#ff0000'>Not found suitable message, add own message</font>";
		while($row				    =	mysqli_fetch_assoc($result))
		{
			$t_adminMessageId	    =	$row['messageId'];
			$t_adminMessageLevel    =	stripslashes($row['messageLevel']);

			$a_orderAdminMessages[$t_adminMessageId]	=	$t_adminMessageLevel;
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
	
?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
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
	include($formSearch);

	include(SITE_ROOT_EMPLOYEES	. "/includes/view-customer-order1.php");

	$newMssageToEmployee		=	"";
	$messageId					=	0;
	$existingMessageId			=	0;
	$displayMainMessageDiv		=	"none";
	$adminMessageId				=	0;
	$t_adminMessage				=	"";
	$checkedSms					=	"";
	$errorMsg					=	"";
	$isHavingExistingFile		=	0;
	$sendingMessageForm			=	SITE_ROOT_EMPLOYEES."/forms/sending-message-pdf-customers.php";
	$messageFilePath			=	SITE_ROOT_FILES."/files/messages/";
	$isEditedMessage			=	0;
	$message					=	"";

	if(isset($_GET['vmsg']) && !empty($s_isHavingVerifyAccess))
	{
		$messageId				=	(int)$_GET['vmsg'];
		if(!empty($messageId))
		{
			$query				=	"SELECT * from members_employee_messages WHERE orderId > '".MAX_SEARCH_MEMBER_ORDERID."' AND isNeedToVerify=1 AND messageId=$messageId AND orderId=$orderId AND memberId=$customerId";
			$result				=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row						=	mysqli_fetch_assoc($result);
				$isEditedMessage			=	1;
				$displayMainMessageDiv		=	"";
				$adminMessageId				=	$row['adminAddedMessageId'];
				$t_adminMessage				=	stripslashes($row['message']);
				$isSelectedSMS				=	$row['isSelectedSMS'];
				$isHavingExistingFile		=	$row['hasMessageFiles'];
				if($isSelectedSMS			==	1)
				{
					$checkedSms				=	"checked";
				}
				$t_hasMessageFiles		    =	$row['hasMessageFiles'];
				$t_fileName					=	$row['fileName'];
				$t_fileExtension			=	$row['fileExtension'];
				$t_fileSize					=	$row['fileSize'];
				$originalSentBy				=	$row['employeeId'];
				$originalSentDate			=	showDateFullText($row['addedOn']);
				$originalSentTime			=	showTimeShortFormat($row['addedTime']);
				$originalSentBy				=	$employeeObj->getEmployeeName($originalSentBy);
			}
			else
			{
				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES);
				exit();
			}
		}
	}


	if(empty($messageId))
	{
		if(!empty($s_isHavingVerifyAccess))
		{
			$existingMessageId=	1;
			//$existingMessageId	=	0;
		}
	}
	else
	{
		$existingMessageId		=	$messageId;
	}

	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		if(isset($messageId) && !empty($messageId))
		{
			$isVerifiedMsg		=	$employeeObj->getSingleQueryResult("SELECT orderId FROM members_employee_messages WHERE orderId > '".MAX_SEARCH_MEMBER_ORDERID."' AND isNeedToVerify=1 AND messageId=$messageId AND orderId=$orderId AND memberId=$customerId","orderId");
			if(empty($isVerifiedMsg))
			{			
				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES ."/send-message-pdf-customer.php?orderId=$orderId&customerId=$customerId&selectedTab=5");
				exit();
			}
		}
		$message				=	trim($message);
		if($message				==	"Enter Your Message Here")
		{
			$message			=	"";
		}
		if($isSendingForverify	==	2 && !empty($adminMessageId) && empty($message))
		{
			$message			=	$employeeObj->getSingleQueryResult("SELECT message FROM admin_added_customer_messages WHERE section=1 AND messageId=$adminMessageId","message");
			if(!empty($message))
			{
				$message		=	stripslashes($message);
				$n_message		=	$message;
			}
		}
		if(isset($_POST['markedImportantSendSms']) && !empty($smsCustomerMobileNo))
		{
			$isSelectedSMS		=	1;
		}
		else
		{
			$isSelectedSMS		=	0;
		}
		$validator ->checkField($message,"","Please Enter Your Message.");
		$dataValid				=	$validator ->isDataValid();
		if($dataValid)
		{
			if(!empty($memberOrderReplyToEmail)){
				$setThisEmailReplyToo			=	$memberOrderReplyToEmail.CUSTOMER_REPLY_EMAIL_TO;//Setting for reply to make customer reply order mesage
				$setThisEmailReplyTooName		=	"ieIMPACT Orders";//Setting for reply to make customer reply order mesage
			}
			else{
				if(!empty($orderEncryptedId))
				{
					$setThisEmailReplyToo	  =	 $orderEncryptedId.CUSTOMER_REPLY_EMAIL_TO;//Setting for reply to make customer reply order mesage
					$setThisEmailReplyTooName =	 "ieIMPACT Orders";//Setting for reply to make customer reply order mesage
				}
			}

			$quickReplyToEmail      =   "<a href='mailto:".$setThisEmailReplyToo."'>".$setThisEmailReplyToo."</a>";

			$newOrdersSmartEmail 	=	"<a href='mailTo:NewOrder".$smartEmailUniqueEmailCode.CUSTOMER_REPLY_EMAIL_TO."'><u>NewOrder".$smartEmailUniqueEmailCode.CUSTOMER_REPLY_EMAIL_TO."</u></a>";

			$newOrdersMessagingEmail=	"<a href='mailTo:Email".$smartEmailUniqueEmailCode.CUSTOMER_REPLY_EMAIL_TO."'><u>Email".$smartEmailUniqueEmailCode.CUSTOMER_REPLY_EMAIL_TO."</u></a>";

			$baseConvertUniqueEmailCode  = base64_encode($memberUniqueEmailCode);
			$base_fileId				 = "";

			
			if(empty($messageId))
			{
				$n_message	=	$message;
				$message	=	makeDBSafe($message);
				
				$query		=	"INSERT INTO members_employee_messages SET orderId=$orderId,memberId=$customerId,employeeId=$s_employeeId,message='$message',parentId=0,addedOn='$nowDateIndia',addedTime='$nowTimeIndia',messageBy='".EMPLOYEES."',adminAddedMessageId='$adminMessageId',estDate='".CURRENT_DATE_CUSTOMER_ZONE."',estTime='".CURRENT_TIME_CUSTOMER_ZONE."',isSelectedSMS=$isSelectedSMS,isShownPopUp=0";
				dbQuery($query);
				$messageId	=	mysqli_insert_id($db_conn);
			}
			else
			{
				$n_message	=	$message;
				$message	=	makeDBSafe($message);

				$query		=	"UPDATE members_employee_messages SET message='$message',isNeedToVerify=0,isSelectedSMS=$isSelectedSMS,verifyMessageBy=$s_employeeId,verifiedDateIst='".CURRENT_DATE_INDIA."',verifiedTimeIst='".CURRENT_TIME_INDIA."',verifiedDateEst='".CURRENT_DATE_CUSTOMER_ZONE."',verifiedTimeEst='".CURRENT_TIME_CUSTOMER_ZONE."',isShownPopUp=0 WHERE messageId=$messageId AND orderId=$orderId AND memberId=$customerId";
				dbQuery($query);

				$orderObj->deductOrderRelatedCounts('verifyMessages');
			}

			$isNewFileUploaded				=	0;
			$uploadedThroughNewSystem		=	0;
			$sendingFileAttachmentMsg		=	"";
			$messageAttachmentPath			=	"";
			
			if(!empty($_FILES['messageFile']['name']))
			{				
				if($isHavingExistingFile	==	1)
				{
					if($isNewUploadingSystem	==	1)
					{
						$exactServerpasth	=	$employeeObj->getSingleQueryResult("SELECT excatFileNameInServer FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND messageId=$messageId AND orderId=$orderId AND uploadingFor=3 AND isDeleted=0 AND uploadingType=7","excatFileNameInServer");
						if(!empty($exactServerpasth) && file_exists($exactServerpasth))
						{
							unlink($exactServerpasth);
							dbQuery("DELETE FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND messageId=$messageId AND orderId=$orderId AND uploadingFor=3 AND isDeleted=0 AND uploadingType=7");
						}
					}
					else
					{
						$fileName	=	$t_messageId."_".$uploadingFileName.".".$fileExt;
						if(file_exists($messageFilePath.$fileName))
						{
							unlink($exactServerpasth);
						}
					}
				}
							
				$isHavingExistingFile		=	1;
				$isNewFileUploaded			=	1;
				$uploadingFile				=   $_FILES['messageFile']['name'];
				$mimeType					=   $_FILES['messageFile']['type'];
				$fileSize					=   $_FILES['messageFile']['size'];
				$tempName					=	$_FILES['messageFile']['tmp_name'];
				$ext						=	findexts($uploadingFile);
				$uploadingFileName			=	getFileName($uploadingFile);

				$fileName					=	$messageId."_".$uploadingFileName.".".$ext;

				if($isNewUploadingSystem	== 1)
				{
					$t_uploadingFile		=	makeDBSafe($uploadingFileName);

					dbQuery("INSERT INTO order_all_files SET uploadingType=7,uploadingFor=3,orderId=$orderId,memberId=$customerId,uploadingFileName='$t_uploadingFile',uploadingFileExt='$ext',uploadingFileType='$mimeType',uploadingFileSize=$fileSize,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',customerZoneDate='".CURRENT_DATE_CUSTOMER_ZONE."',customerZoneTime='".CURRENT_TIME_CUSTOMER_ZONE."',addedFromIp='".VISITOR_IP_ADDRESS."',messageId=$messageId");

					$fileId					=	mysqli_insert_id($db_conn);
					$base_fileId			=	base64_encode($fileId);

					$destFileName			=	$newUploadingPath."/".$fileId."_".$uploadingFileName.".".$ext;

					move_uploaded_file($tempName,$destFileName);

					dbQuery("UPDATE order_all_files SET excatFileNameInServer='$destFileName' WHERE fileId=$fileId AND orderId=$orderId AND messageId=$messageId");

					dbQuery("UPDATE members_employee_messages SET hasMessageFiles=1 WHERE messageId=$messageId");

					$uploadedThroughNewSystem		=	1;
					$messageAttachmentPath			=	$destFileName;
					$isUploadingInNew				=	1;
				}
				else
				{
					move_uploaded_file($tempName,$messageFilePath.$fileName);
								
					dbQuery("UPDATE members_employee_messages SET hasMessageFiles=1,fileExtension='$ext',fileName='$uploadingFileName',fileMimeType='$mimeType',fileSize=$fileSize WHERE messageId=$messageId");
					
					$messageAttachmentPath			=	$messageFilePath.$fileName;
					$isUploadingInNew				=	0;
				}
			}
			

			/***************** SENDING EMAIL IF & ONLY IF SELECTED SEND THIS AS IS IT****************/
			if($isSendingForverify		==	2)
			{
				//dbQuery("UPDATE members_orders SET isHavingOrderNewMessage=0 WHERE orderId=$orderId AND memberId=$customerId");

				//dbQuery("UPDATE members_employee_messages SET isRepliedMessage=1 WHERE orderId=$orderId AND memberId=$customerId");

				dbQuery("UPDATE members SET isHavingEmployeeOrderMessage=1 WHERE memberId=$customerId");
				
				$performedTask			=	"Send Message To - ".$customerId." On Order No - ".$orderId;

				include(SITE_ROOT		.   "/classes/email-templates.php");
				$emailObj			    =	new emails();
				$n_message				=	stripslashes($n_message);
				$subject_message		=	$n_message;
				$n_message				=	nl2br($n_message);
				$smsOrderMsg			=	$subject_message;
				$mailSubject			=	getSubstring($subject_message,75);
				$orderForText			=	"You have a message for your order";
				$referFriendLink		=   "";

				$a_templateSubject		=	array("{emailOwnSubject}"=>$mailSubject);
			
				$uniqueTemplateName		=	"TEMPLATE_SENDING_MESSAGE_CUSTOMER_MANAGER_WITH_ATTACHMENT";

				$hasAttachment					=	0;
				if(!empty($isHavingExistingFile) && empty($isNewFileUploaded))
				{						
					if($isNewUploadingSystem	== 1)
					{
						if($result1				=	$orderObj->getOrdereMessageFile($orderId,$messageId,3,7))
						{
							$row1					=	mysqli_fetch_assoc($result1);
							$uploadingFileName		=	stripslashes($row1['uploadingFileName']);
							$ext					=	stripslashes($row1['uploadingFileExt']);
							$mimeType				=	$row1['uploadingFileType'];
							$fileSize				=	$row1['uploadingFileSize'];
							$imageOnServerPath		=	$row1['excatFileNameInServer'];
							$messageAttachmentPath	=	$imageOnServerPath;
							$isUploadingInNew		=	1;
						}
					}
					else
					{
						$query1						=	"SELECT fileExtension,fileName,fileMimeType,fileSize FROM members_employee_messages WHERE orderId > '".MAX_SEARCH_MEMBER_ORDERID."'  AND messageId=$messageId";
						$result1					=	dbQuery($query1);
						if(mysqli_num_rows($result1))
						{
							$row1					=	mysqli_fetch_assoc($result1);
							$uploadingFileName		=	stripslashes($row1['fileName']);
							$ext					=	stripslashes($row1['fileExtension']);
							$mimeType				=	$row1['fileMimeType'];
							$fileSize				=	$row1['fileSize'];
							$messageAttachmentPath	=	$messageFilePath.$messageId."_".$uploadingFileName.".".$ext;
							$isUploadingInNew		=	0;
						}
					}
				}
				
				if(!empty($messageAttachmentPath) && file_exists($messageAttachmentPath))
				{
					if(!empty($fileSize) && $ext != "exe")
					{
						if($fileSize <= 7340032)
						{
							$hasAttachment			=	1;
							$a_attachmentPath		=	array();
							$a_attachmentType		=	array();
							$a_attachmentName		=	array();
						
							$a_attachmentPath[]		=	$messageAttachmentPath;
							$a_attachmentType[]		=	$mimeType;
							$a_attachmentName[]		=	$uploadingFileName.".".$ext;

							$sendingFileAttachmentMsg=	"Please note: Files are also attached in this email.";
						}
						else
						{
							$sendingFileAttachmentMsg=	"Note: Failed to send files by email becuase size was greater than 7mb.";
						}
					}

					$downLoadPath			=	SITE_URL_MEMBERS."/download-multiple-file.php?suf=".$baseConvertUniqueEmailCode."&".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;

					if(in_array($ext,$a_displayAnyImageOfType) && $fileSize <= "3145728")
					{
						list($imgWidth, $imgHeight, $type, $attr) = getimagesize($messageAttachmentPath);

						if($imgWidth > 600 || $imgHeight > 400)
						{
							$imageWidth		=	"width='600'";
							$imageHeight	=	"height='400'";
						}
						else
						{
							$imageWidth		=	"";
							$imageHeight	=	"";
						}

						$toEmailBase		=	base64_encode($customerEmail);
						$toOrderIdBase		=	base64_encode($orderId);
						$toMessageIdBase	=	base64_encode($messageId);						

						$uploadFileName		=	"<a href='".$downLoadPath."' target='_blank' title='Download' style='cursor:pointer;'><img src='". SITE_URL_MEMBERS."/get-employee-message-image-email.php?a=$toOrderIdBase&b=$toMessageIdBase&c=$isUploadingInNew&d=$toEmailBase'  border='0' title='".$uploadingFileName.".".$ext."'></a>";

					}
					else
					{
						$uploadFileName	=	"<a href='".$downLoadPath."' target='_blank' title='Download' style='cursor:pointer;'>".$uploadingFileName.".".$ext."</a>";
					}

					$uploadFileText		=	"Uploaded File";
					
					$uploadFileComma	=	":";
				}
				
				
				if($hasReceiveEmails	== 0)
				{
					
					$trackEmailImage		=	$emailTrackObj->addTrackEmailRead($customerEmail,$mailSubject,"orders@ieimpact.com",$customerId,1,9,3,$s_employeeName,$s_employeeId);

					if($trackEmailImage		!=  "images/white-space.jpg")
					{
						$sendingUniqueCode	 =	stringReplace("mail-t/","",$trackEmailImage);
						$sendingUniqueCode	 =	stringReplace(".jpg","",$sendingUniqueCode);

						dbQuery("UPDATE members_employee_messages SET emailUniqueCode='$sendingUniqueCode' WHERE messageId=$messageId AND memberId=$customerId");
					}

					


					$a_templateData				=	array("{orderForText}"=>$orderForText,"{orderNo}"=>$orderAddress,"{orderText}"=>$orderText,"{name}"=>$firstName,"{orderText}"=>$orderText,"{message}"=>$n_message,"{uploadFileText}"=>$uploadFileText,"{uploadFileName}"=>$uploadFileName,"{uploadFileComma}"=>$uploadFileComma,"{trackEmailImage}"=>$trackEmailImage,"{sendingFileAttachmentMsg}"=>$sendingFileAttachmentMsg,"{referFriendLink}"=>$referFriendLink,"{quickReplyToEmail}"=>$quickReplyToEmail,"{newOrdersSmartEmail}"=>$newOrdersSmartEmail,"{newOrdersMessagingEmail}"=>$newOrdersMessagingEmail);
					
					$toEmail					=	$customerEmail;
					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

					if(!empty($customerSecondaryEmail))
					{
						$trackEmailImage		=	"images/white-space.jpg";

						$a_templateData			=	array("{orderForText}"=>$orderForText,"{orderNo}"=>$orderAddress,"{orderText}"=>$orderText,"{name}"=>$firstName,"{orderText}"=>$orderText,"{message}"=>$n_message,"{uploadFileText}"=>$uploadFileText,"{uploadFileName}"=>$uploadFileName,"{uploadFileComma}"=>$uploadFileComma,"{trackEmailImage}"=>$trackEmailImage,"{sendingFileAttachmentMsg}"=>$sendingFileAttachmentMsg,"{referFriendLink}"=>$referFriendLink,"{quickReplyToEmail}"=>$quickReplyToEmail,"{newOrdersSmartEmail}"=>$newOrdersSmartEmail,"{newOrdersMessagingEmail}"=>$newOrdersMessagingEmail);

						$toEmail				=	$customerSecondaryEmail;
						include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
					}
				}
				$hasAttachment			=	0;
				
				$a_managerEmails		=	$orderObj->getMangersOnlyEmails();
				$trackEmailImage		=	"images/white-space.jpg";
				$referFriendLink		=	"";

				$manager_message		=	"";
				$mailSubject			=	getSubstring($subject_message,75);

				if($isEditedMessage		==	1)
				{
					$manager_message	=	$n_message."<br><br><b>Sent By Employee </b>: ".$originalSentBy." at ".$originalSentDate." ".$originalSentTime."<br /><br /><b>Verified By </b>: ".$s_employeeName." at ".showDateFullText(CURRENT_DATE_INDIA)." ".showTimeShortFormat(CURRENT_TIME_INDIA);
				}
				else
				{
					$manager_message	=	$n_message."<br><br><b>Sent By Employee </b>: ".$s_employeeName." at ".showDateFullText(CURRENT_DATE_INDIA)." ".showTimeShortFormat(CURRENT_TIME_INDIA);
				}

				if(isset($_POST['filesNotProperlyChecked']) && !empty($isOrderChecked) && !empty($orderCheckedBy)){
					$manager_message	=	$manager_message."<br><br><b>Employee marked as order not properly checked by </b>: ".$orderCheckedBy;
				}

				if(!empty($a_managerEmails))
				{
					$orderForText	    =	"Customer ".$customerName." have a message his/her order";

					$a_managerEmails	=	stringReplace(',john@ieimpact.net','',$a_managerEmails);


					$a_templateData		=	array("{orderForText}"=>$orderForText,"{orderNo}"=>$orderAddress,"{orderText}"=>$orderText,"{name}"=>"Manager","{orderText}"=>$orderText,"{message}"=>$manager_message,"{uploadFileText}"=>$uploadFileText,"{uploadFileName}"=>$uploadFileName,"{uploadFileComma}"=>$uploadFileComma,"{trackEmailImage}"=>$trackEmailImage,"{sendingFileAttachmentMsg}"=>$sendingFileAttachmentMsg,"{referFriendLink}"=>$referFriendLink,"{quickReplyToEmail}"=>$quickReplyToEmail,"{newOrdersSmartEmail}"=>$newOrdersSmartEmail,"{newOrdersMessagingEmail}"=>$newOrdersMessagingEmail);

					$a_templateSubject	=	array("{emailOwnSubject}"=>$mailSubject);

					$uniqueTemplateName    =	"TEMPLATE_SENDING_MESSAGE_CUSTOMER_MANAGER_WITH_ATTACHMENT";
					$toEmail			   =	DEFAULT_BCC_EMAIL;
					$managerEmployeeFromBcc=	$a_managerEmails;
					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
				}

				//Sending SMS to customer
				if($isSelectedSMS	   ==	1)
				{
					//if($customerId ==  	3580){
					    $displaySmsOrderNo =   subString($orderAddress,10);
						$toPhone           =   "+".$smsCustomerMobileNo;
						$smsMessage		   =   "MSG from ieIMPACT : ".$displaySmsOrderNo." : ".$smsOrderMsg;
						include(SITE_ROOT_EMPLOYEES .  "/includes/sending-sms-customer.php");

					/*}
					else{

						try{
							$displaySmsOrderNo =   subString($orderAddress,10);
							$smsMessage		   =   "MSG from ieIMPACT : ".$displaySmsOrderNo." : ".$smsOrderMsg;

							$smsMessage		   =	stringReplace("<br>", " ", $smsMessage);
							$smsMessage		   =	stringReplace("</ br>", " ", $smsMessage);
							$smsMessage		   =	stringReplace("</ br>", " ", $smsMessage);
							$smsReferenceID	   =    $orderId."-".rand(11,99)."-".substr(md5(microtime()+rand()+date('s')),0,5);

							$smsReturnPath	   =	"https://secure.ieimpact.com/read-sms-postback.php"; 

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

							$smsMessage		=	addslashes($smsMessage);

							$newSmsID= $orderObj->addOrderMessageSms($cancelled,$smsReferenceID,$orderId,$customerId,$s_employeeId,$smsMessageID,$queued,$smsError,$smsMessage,$smsCustomerMobileNo);

							dbQuery("UPDATE members_employee_messages SET isFromSms=1,smsId=$newSmsID WHERE orderId=$orderId AND memberId=$customerId AND messageId=$messageId");
						}
						catch(Exception $e){
							//$error = $e->getMessage();
							//die($error);
						}
					}*/
				}



				$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);
			}
			else
			{
				$query		=	"UPDATE members_employee_messages SET isNeedToVerify=1 WHERE orderId=$orderId AND memberId=$customerId AND messageId=$messageId";
				dbQuery($query);

				include(SITE_ROOT			.   "/classes/email-templates.php");
				$emailObj					=	new emails();
				$n_message					=	stripslashes($n_message);
				$subject_message			=	$n_message;
				$n_message					=	nl2br($n_message);
				$hasAttachment				=	0;
				
				$a_managerEmails			=	$orderObj->getMangersOnlyEmails();
				$trackEmailImage			=	"images/white-space.jpg";
				$referFriendLink			=	"";
				$manager_message			=	"";
				$sendingFileAttachmentMsg	=	"";
				$mailSubject				=	getSubstring($subject_message,75);
				$manager_message			=	$n_message."<br><br><b>Sent By Employee </b>: <b>".$s_employeeName."&nbsp;</b>(Verification Pending)";
				
				$orderForText			 =	"Customer ".$customerName." have a message his/her order";

				if(!empty($_FILES['messageFile']['name']))
				{
					$uploadFileName		=   $_FILES['messageFile']['name'];
					$uploadFileText		=	"Uploaded File";						
					$uploadFileComma	=	":";
				}	


				if(!empty($a_managerEmails))
				{					
					$a_managerEmails	    =	stringReplace(',john@ieimpact.net','',$a_managerEmails);
					
					$a_templateData			=	array("{orderForText}"=>$orderForText,"{orderNo}"=>$orderAddress,"{orderText}"=>$orderText,"{name}"=>"Manager","{orderText}"=>$orderText,"{message}"=>$manager_message,"{uploadFileText}"=>$uploadFileText,"{uploadFileName}"=>$uploadFileName,"{uploadFileComma}"=>$uploadFileComma,"{trackEmailImage}"=>$trackEmailImage,"{sendingFileAttachmentMsg}"=>$sendingFileAttachmentMsg,"{referFriendLink}"=>$referFriendLink,"{quickReplyToEmail}"=>$quickReplyToEmail,"{newOrdersSmartEmail}"=>$newOrdersSmartEmail,"{newOrdersMessagingEmail}"=>$newOrdersMessagingEmail);

					$a_templateSubject		=	array("{emailOwnSubject}"=>$mailSubject);

					$uniqueTemplateName		=	"TEMPLATE_SENDING_MESSAGE_CUSTOMER_MANAGER_WITH_ATTACHMENT";
					$toEmail				=	DEFAULT_BCC_EMAIL;
					$managerEmployeeFromBcc =	$a_managerEmails;
					//include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
					$memberObj->updateOrderRelatedCounts('verifyMessages');
				}

			}
			/************************************* END OF SENDING EMAIl PROCESS **********************/
			////////////////////////////////////////////////////////////////////////////////////////
			//////////////////// PUTTING THE ORDER IN ORDER TRACK LIST ////////////////////////////
		    $orderObj->addOrderTracker($s_employeeId,$orderId,$orderAddress,'Sending message to customer','EMPLOYEE_SENDING_ORDER_MESSSGE');
		    ////////////////////////////////////////////////////////////////////////////////////////
		    ////////////////////////////////////////////////////////////////////////////////////////
			
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES ."/send-message-pdf-customer.php?orderId=$orderId&customerId=$customerId&selectedTab=5");
			exit();

		}
		else
		{
			$errorMsg			=	$validator->getErrors();
		}
	}
	include($sendingMessageForm);
?>
<br>
<a name="messages"></a>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>
