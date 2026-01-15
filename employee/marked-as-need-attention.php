<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	//ini_set('display_errors', '1');
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	//include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	//include(SITE_ROOT			. "/includes/send-mail.php");
	include(SITE_ROOT			. "/classes/common.php");
	include(SITE_ROOT			. "/classes/email-track-reading.php");
	
	$employeeObj				= new employee();
	$memberObj					= new members();
	$orderObj					= new orders();
	$commonObj					= new common();
	$emailTrackObj				= new trackReading();
	$a_allmanagerEmails			= $commonObj->getMangersOnlyEmails();
	$showForm					= false;
	$orderId					= 0;
	$customerId					= 0;
	$status						= 0;
	$employeeId					= 0;
	$error						= "";
	$M_D_5_ORDERID				= ORDERID_M_D_5;
	$M_D_5_ID					= ID_M_D_5;

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

	function getSizeNoBracket($fileSize)
	{
		if($fileSize <= 0 || $fileSize == 0)
		{
			$fileSize	=	"";
		}
		else
		{
			$fileSize	=	$fileSize/1024;

			$fileSize	=	round($fileSize,2);

			$fileSize	=	"&nbsp;<font style='font-size:12px;color:#000000;'>(".$fileSize." KB)</font>";
		}

		return $fileSize;
	}
	
	$errorMessageForm			= "You are not authorized to view this page !!";

	if(isset($_GET['orderId']) && isset($_GET['customerId']))
	{
		$orderId				=	$_GET['orderId'];
		$customerId				=	$_GET['customerId'];
		if($result				=	$orderObj->getOrderDetails($orderId,$customerId))
		{
			$showForm				=	true;
			$encodeOrderID			=	base64_encode($orderId);

			$row					=	mysqli_fetch_assoc($result);
			$firstName				=	stripslashes($row['firstName']);
			$lastName				=	stripslashes($row['lastName']);
			$orderAddress			=   stripslashes($row['orderAddress']);
			$orderType				=	$row['orderType'];
			$orderAddedOn			=	showDate($row['orderAddedOn']);
			$customerEmail			=	$row['email'];
			$customerSecondaryEmail	=	$row['secondaryEmail'];
			$customerMobileNo		=	$row['phone'];
			$orderEncryptedId		=	$row['encryptOrderId'];
			$orderReplyToEmail		=	$row['orderReplyToEmail'];
			$newUploadingPath		=	$row['newUploadingPath'];
			$memberUniqueEmailCode	=	$smartEmailUniqueEmailCode = $row['uniqueEmailCode'];
			$isOrderChecked			=	$row['isOrderChecked'];
			$orderCheckedBy			=	$row['orderCheckedBy'];

			if(!empty($customerMobileNo))
			{
				$customerMobileNo	=	"1".$customerMobileNo;
			}
			$isCustomerOptedForSms	=	$row['isOptedForSms'];

			$customerName			=	$firstName." ".substr($lastName, 0, 1);
			$orderText				=	$a_customerOrder[$orderType];
		}
	}

	$section					=	1;
	$a_orderAdminMessages		=	array();
	$adminMessadeId				=	0;
	$hasAdminMessage			=	0;

	$query						=	"SELECT * FROM admin_added_customer_messages WHERE section=$section ORDER BY message";
	$result						=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		$hasAdminMessage		=	1;
		$a_orderAdminMessages['-1']	=	"<font color='#ff0000'>Not found suitable message, add own message</font>";
		while($row				    =	mysqli_fetch_assoc($result))
		{
			$t_adminMessadeId	    =	$row['messageId'];
			$t_adminMessageLevel    =	stripslashes($row['messageLevel']);

			$a_orderAdminMessages[$t_adminMessadeId]	=	$t_adminMessageLevel;
		}
		
	}
?>
<html>
<head>
<TITLE>Send Need Attention Message</TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
<body>
<script type="text/javascript">
	function reflectChange()
	{
		window.opener.location.reload();
	}
</script>
<center>
<?php
	if($showForm)
	{
		if(isset($_REQUEST['formSubmitted']))
		{
			extract($_REQUEST);
			$message		=	trim($message);
			if($message		==	"Enter Your Message Here")
			{
				$message	=	"";
			}
			if(empty($message))
			{
				$error		= "Please Enter Message.";
			}
			if(!empty($_FILES['attentionMessageFile']['name']))
			{	
				$uploadingFile				=   $_FILES['attentionMessageFile']['name'];
				$mimeType					=   $_FILES['attentionMessageFile']['type'];
				$fileSize					=   $_FILES['attentionMessageFile']['size'];
				$tempName					=	$_FILES['attentionMessageFile']['tmp_name'];
				$ext						=	findexts($uploadingFile);
				$uploadingFileName			=	getFileName($uploadingFile);
				if($fileSize > MAXIMUM_SINGLE_FILE_SIZE_ALLOWED)
				{
					$error .= "<br />The File you are trying to send is very large. It's size must be less than ".MAXIMUM_SINGLE_FILE_SIZE_ALLOWED_TEXT.".";
				}
			}


			if(empty($error))
			{
				if(empty($hasAdminMessage))
				{
					$adminAddedMessageId	=	0;
				}
				$t_message		=	makeDBSafe($message);

				////////////////////////////////////////////////////////////////////////////////////////////
			    //////////////////// PUTTING THE ORDER IN ORDER TRACK LIST /////////////////////////////////
			    $orderObj->addOrderTracker($s_employeeId,$orderId,$orderAddress,'Employee marked order as need attention','EMPLOYEE_MARKED_ORDER_ATTENTION');
			    ////////////////////////////////////////////////////////////////////////////////////////////
			    ////////////////////////////////////////////////////////////////////////////////////////////
				

				dbQuery("INSERT INTO order_attention_messages SET message='$t_message',memberId=$customerId,orderId=$orderId,employeeId=$s_employeeId,date='".CURRENT_DATE_INDIA."',time='".CURRENT_TIME_INDIA."',adminAddedMessageId=$adminMessadeId");

				$orderAttentionMsgId	=	mysqli_insert_id($db_conn);

				dbQuery("INSERT INTO order_attention SET orderId=$orderId,customerId=$customerId,attentionStatus=1,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',addedBy=$s_employeeId");

				dbQuery("UPDATE members_orders SET status=3,isOrderNeedAttention=1 WHERE orderId=$orderId AND memberId=$customerId");

				//////////////////////// UPLOAD FILE IF ANY /////////////////
				if(!empty($_FILES['attentionMessageFile']['name']))
				{
					$t_uploadingFile		=	makeDBSafe($uploadingFileName);

					dbQuery("INSERT INTO order_all_files SET uploadingType=8,uploadingFor=3,orderId=$orderId,memberId=$customerId,uploadingFileName='$t_uploadingFile',uploadingFileExt='$ext',uploadingFileType='$mimeType',uploadingFileSize=$fileSize,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',customerZoneDate='".CURRENT_DATE_CUSTOMER_ZONE."',customerZoneTime='".CURRENT_TIME_CUSTOMER_ZONE."',addedFromIp='".VISITOR_IP_ADDRESS."',orderAttentionMsgId=$orderAttentionMsgId");

					$fileId					=	mysqli_insert_id($db_conn);

					$destFileName			=	$newUploadingPath."/".$fileId."_".$uploadingFileName.".".$ext;

					move_uploaded_file($tempName,$destFileName);

					dbQuery("UPDATE order_all_files SET excatFileNameInServer='$destFileName' WHERE fileId=$fileId AND orderId=$orderId AND orderAttentionMsgId=$orderAttentionMsgId");

					dbQuery("UPDATE order_attention_messages SET hasFile=1 WHERE messageId=$orderAttentionMsgId");

				}
				///////////////////////////////////////////////////////////////////////////////////
				$performedTask	=	"Marked as need attention - ".$message;
				
				$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);


				$message		=	stripslashes($message);
				$smsAttentionMsg=	$message;
				$subject_message=	getSubstring($message,70);
				//$message		=	nl2br($message);

				/////////////////// START OF SENDING EMAIL BLOCK///////////////////////////////
				include(SITE_ROOT		. "/classes/email-templates.php");
				$emailObj			    =	new emails();

				$attention				=	"This order requires your attention.<br><br>We cannot process this order until we receive a reply or data files as follows :<br><p align='justify'>".$message."</p><br /><b>Please note:</b> The receive timestamp of this order will be the time when we will receive these files and new ETA will be calculated based on that time. <br />";
				if(!empty($_FILES['attentionMessageFile']['name']))
				{
					$base_fileId			=	base64_encode($fileId);
					$baseConvertUniqueEmailCode  = base64_encode($memberUniqueEmailCode);

					$downLoadPath			=	SITE_URL_MEMBERS."/download-multiple-file.php?suf=".$baseConvertUniqueEmailCode."&".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;

					$attention			   .=   "Uploaded File : <a href='".$downLoadPath."' target='_blank'><font style='font-size:12px;color:#0082bf;'>".$uploadingFileName.".".$ext."</font></a>&nbsp;".getSizeNoBracket($fileSize)."<br />";

				}

				$n_mailSubject			=	"Need Your Attention : ".$subject_message;

				$trackEmailImage		=	$emailTrackObj->addTrackEmailRead($customerEmail,$n_mailSubject,"orders@ieimpact.com",$customerId,1,4,3,$s_employeeName,$s_employeeId);

				if($trackEmailImage		!=  "images/white-space.jpg")
				{
					$sendingUniqueCode	 =	stringReplace("mail-t/","",$trackEmailImage);
					$sendingUniqueCode	 =	stringReplace(".jpg","",$sendingUniqueCode);

					dbQuery("UPDATE order_attention_messages SET emailUniqueCode='$sendingUniqueCode' WHERE messageId=$orderAttentionMsgId");
				}
				

				if(!empty($orderReplyToEmail))
				{
					$setThisEmailReplyToo			=	$orderReplyToEmail.CUSTOMER_REPLY_EMAIL_TO;//Setting for reply to make customer reply order mesage
					$setThisEmailReplyTooName		=	"ieIMPACT Orders";//Setting for reply to make customer reply order mesage
				}
				else{
					if(!empty($orderEncryptedId))
					{
						$setThisEmailReplyToo			=	$orderEncryptedId.CUSTOMER_REPLY_EMAIL_TO;//Setting for reply to make customer reply order mesage
						$setThisEmailReplyTooName		=	"ieIMPACT Orders";//Setting for reply to make customer reply order mesage
					}
				}

				$quickReplyToEmail      = "<a href='mailto:".$setThisEmailReplyToo."'>".$setThisEmailReplyToo."</a>";

				$newOrdersSmartEmail 	=	"<a href='mailTo:NewOrder".$smartEmailUniqueEmailCode.CUSTOMER_REPLY_EMAIL_TO."'><u>NewOrder".$smartEmailUniqueEmailCode.CUSTOMER_REPLY_EMAIL_TO."</u></a>";

				$newOrdersMessagingEmail=	"<a href='mailTo:Email".$smartEmailUniqueEmailCode.CUSTOMER_REPLY_EMAIL_TO."'><u>Email".$smartEmailUniqueEmailCode.CUSTOMER_REPLY_EMAIL_TO."</u></a>";

				$a_templateSubject		=	array("{attentionSubject}"=>$n_mailSubject);
				$a_templateData			=	array("{attention}"=>$attention,"{name}"=>$firstName,"{orderNo}"=>$orderAddress,"{orderType}"=>$orderText,"{trackEmailImage}"=>$trackEmailImage,"{quickReplyToEmail}"=>$quickReplyToEmail,"{newOrdersSmartEmail}"=>$newOrdersSmartEmail,"{newOrdersMessagingEmail}"=>$newOrdersMessagingEmail);
				$toEmail				=	$customerEmail;
				
				$uniqueTemplateName		=	"TEMPLATE_SENDING_NEED_MARKED_UNMARKED_ATTENTION";
								
				include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

				$trackEmailImage		=	"images/white-space.jpg";

				if(!empty($customerSecondaryEmail))
				{
					$a_templateData			=	array("{attention}"=>$attention,"{name}"=>$firstName,"{orderNo}"=>$orderAddress,"{orderType}"=>$orderText,"{trackEmailImage}"=>$trackEmailImage,"{quickReplyToEmail}"=>$quickReplyToEmail);

					$toEmail			=	$customerSecondaryEmail;
					$uniqueTemplateName	=	"TEMPLATE_SENDING_NEED_MARKED_UNMARKED_ATTENTION";
					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

				}
				
				if(!empty($a_allmanagerEmails))
				{
					$a_allmanagerEmails	=	stringReplace(',john@ieimpact.net','',$a_allmanagerEmails);

					if(!empty($isOrderChecked) && !empty($orderCheckedBy)){
						$orderText1		=	$orderText."&nbsp;(Order Checked By : ".$orderCheckedBy." and Sent By Employee - ".$s_employeeName.")";
					}
					else{
						$orderText1		=	$orderText."&nbsp;(By Employee - ".$s_employeeName.")";
					}

					$a_templateData		=	array("{attention}"=>$attention,"{name}"=>$firstName,"{orderNo}"=>$orderAddress,"{orderType}"=>$orderText1,"{trackEmailImage}"=>$trackEmailImage,"{quickReplyToEmail}"=>$quickReplyToEmail);

					$uniqueTemplateName		=	"TEMPLATE_SENDING_NEED_MARKED_UNMARKED_ATTENTION";
					$toEmail				=	DEFAULT_BCC_EMAIL;
					$managerEmployeeFromBcc =	$a_allmanagerEmails;
				

					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
				}
				//////////////////// Email Sending End ////////////

				if($isCustomerOptedForSms == 1 && !empty($customerMobileNo))
				{
					//if($customerId ==  	3580){
						$toPhone           =   "+".$customerMobileNo;
						$displaySmsOrderNo =  subString($orderAddress,10);
						$smsMessage		   =  "MSG from ieIMPACT : ".$displaySmsOrderNo." : ".$smsAttentionMsg;
						$isFromAttentionOrder = 1;
						include(SITE_ROOT_EMPLOYEES .  "/includes/sending-sms-customer.php");
					/*}
					else{

						try{
							$displaySmsOrderNo =  subString($orderAddress,10);
							$smsMessage		   =  "MSG from ieIMPACT : ".$displaySmsOrderNo." : ".$smsAttentionMsg;

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
							 
							$phoneNumbersArray1=    array($customerMobileNo);
											 
							$RequestArray	   =    array(
										array(
											'AssignedDID'=>'',
																  //If you have a Dedicated Line, you would assign it here.
											'Message'=>$smsMessage,   
											'PhoneNumbers'=>$phoneNumbersArray1,
											'ReferenceID'=>$smsReferenceID,
											'StatusPostBackURL'=>$smsReturnPath 
											)
							);
							 
							$request		=   new AdvancedCallRequestData($smsKey,$RequestArray);
							$result			=   $client->AdvancedSMSsend($request);
							$result1		=	convertObjectToArray($result);
							$mainResult	    =	$result1['AdvancedSMSsendResult'];
							$a_mainSmsResult=	$mainResult['SMSResponse'];
							
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

							$orderObj->addNeedAttentionOrderSms($cancelled,$smsReferenceID,$orderId,$customerId,$s_employeeId,$smsMessageID,$queued,$smsError,$smsMessage,$customerMobileNo,1);

							dbQuery("UPDATE order_attention_messages SET isSentSMS=1 WHERE messageId=$orderAttentionMsgId AND orderId=$orderId");
						}
						catch(Exception $e){
							if(VISITOR_IP_ADDRESS	==	"122.160.167.153"){
								$error = $e->getMessage();
								die($error);
							}
						}
					}*/	
				
				}

				echo "<br><center><font class='smalltext2'><b>Successfully sent attention message !!</b></font></center></br>";
	
				echo "<script type='text/javascript'>reflectChange();</script>";
			
				echo "<script>setTimeout('window.close()',1)</script>";
			}

		}
?>
	<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>
	<script type="text/javascript">
		function display_loading()
		{
			document.getElementById('loading').style.display = 'block';
		} 
		function validMessage()
		{
			form1	=	document.sendAttentionMessage;
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

			//form1.submit.value    = "Sending... Please wait";
			//form1.submit.disabled = true;

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
	</script>
	<form name="sendAttentionMessage" action="" method="POST" enctype="multipart/form-data"  onSubmit="return validMessage();">
		<table width="100%" align="center" border="0" cellspacing="3" cellspacing="3">
			<tr>
				<td colspan="3" class="textstyle1"><b>Send Need Attention Message</b></td>
			</tr>
			<tr>
				<td colspan="3" class="error">
					<?php echo $error;?>
				</td>
			</tr>
			<tr>
				<td width="24%" class="smalltext2">
					<b>Customer Name</b>
				</td>
				<td width="2%" class="smalltext2">
					<b>:</b>
				</td>
				<td class="title">
					<?php echo $customerName;?>
				</td>
			</tr>
			<tr>
				<td class="smalltext2">
					<b>Order No</b>
				</td>
				<td class="smalltext2">
					<b>:</b>
				</td>
				<td class="title">
					<?php echo $orderAddress;?>
				</td>
			</tr>
			<tr>
				<td class="smalltext2">
					<b>Order Type</b>
				</td>
				<td class="smalltext2">
					<b>:</b>
				</td>
				<td class="title">
					<?php echo $orderText;?>
				</td>
			</tr>
			<tr>
				<td class="smalltext2">
					<b>Added On</b>
				</td>
				<td class="smalltext2">
					<b>:</b>
				</td>
				<td class="title">
					<?php echo $orderAddedOn;?>
				</td>
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
				<td class="smalltext2" valign="top">
					Attention Message
				</td>
				<td class="smalltext2" valign="top">
					:
				</td>
				<td valign="top">
					<textarea name="message" rows="8" cols="50" style="border:2px solid #4d4d4d"></textarea>
				</td>
			</tr>
			
			<?php
				}	
			?>
			<tr>
				<td height="5" colspan="3"></td>
			</tr>
			<tr>
				<td width="15%" class="smalltext2"><b>Upload A File</b></td>
				<td width="2%" class="smalltext2"><b>:</b></td>
				<td>
					<input type="file" name="attentionMessageFile">
				</td>
			</tr>
			<tr>
				<td height="5" colspan="3"></td>
			</tr>
			<tr>
				<td colspan="3" class="smlltext">[Note<font color="red"><b>*</b></font>: Please check your english as your message will go to customer]</td>
			</tr>
			<tr>
				<td colspan="3">
					<div id="loading" style="display: none;"><img src="<?php echo OFFLINE_IMAGE_PATH;?>/images/ajax-loader.gif" alt="" /></div> 
				</td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td>
					<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
					<input type="hidden" name="hasAdminMessage" value="<?php echo $hasAdminMessage;?>">
					<input type='hidden' name='formSubmitted' value='1'>
				</td>
			</tr>
		</table>
	</form>
	<?php
		$query	=	"SELECT * FROM order_attention_messages WHERE memberId=$customerId AND orderId=$orderId ORDER BY date,time";
		$result	=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
	?>
	<table width="100%" align="center" border="0" cellspacing="0" cellspacing="0">
		<tr>
			<td colspan="6" class="textstyle1"><b>Existing Attention Message</b></td>
		</tr>
		<tr>
			<td width="3%" class="smalltext2">
				&nbsp;
			</td>
			<td width="65%" class="smalltext2">
				<b>Message</b>
			</td>
			<td class="smalltext2" width="15%">
				<b>Date</b>
			</td>
			<td class="smalltext2">
				<b>By Employee</b>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<hr size="1" width="100%" color="#4d4d4d">
			</td>
		</tr>
		<?php
				$i		=0;
			    while($row = mysqli_fetch_assoc($result))
				{
					$i++;
					$message		=	stripslashes($row['message']);
					$date			=	showDate($row['date']);
					$employeeId		=	$row['employeeId'];

					$firstName		=	$employeeObj->getEmployeeFirstName($employeeId);
		?>
		<tr>
			<td class="smalltext2" valign="top">
				<?php echo $i;?>)
			</td>
			<td class="smalltext4" valign="top">
				<?php echo nl2br($message);?>
			</td>
			<td class="smalltext2" valign="top">
				<?php echo $date;?>
			</td>
			<td class="smalltext2" valign="top">
				<?php echo $firstName;?>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<hr size="1" width="100%" color="#4d4d4d">
			</td>
		</tr>
		<?php
				}
		?>
	</table>
	<?php
		}
	}
	else
	{
		echo "<table width='90%' align='center' border='1' height='100'><tr><td align='center' align='center' class='error'><b>$errorMessageForm</b></td></tr></table>";
	}
?>
<br><br>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
</center>
</body>
</html>

