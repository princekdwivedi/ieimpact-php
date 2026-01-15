<?php
	$smsKey			   =	SMS_CDYNE_KEY;
	$smsMessage		   =	stringReplace("<br>", " ", $smsMessage);
	$smsMessage		   =	stringReplace("</ br>", " ", $smsMessage);
	$smsMessage		   =	stringReplace("</ br>", " ", $smsMessage);
	$smsReferenceID	   =    "121121-".rand(11,99)."-".substr(md5(rand()+date('s')),0,5);  
	$a_smsMessageStatusCodes   =    cdyneStatusCodes();
	$isSuccessfullySent 	   =    0;
	$newSmsMessageStatus       =    0;
	$newSmsMessageId           =    0;
	$smsReturnPath	           =	"https://secure.ieimpact.com/read-sms-postback.php"; 
	try{	 
		$json='{
			"Attachments":[],
			"Body":"'.$smsMessage.'",
			"Concatenate":true, 
			"From":"",
			"IsUnicode":true, 
			"LicenseKey":"'.$smsKey.'",
			"PostbackUrl":"https://secure.ieimpact.com/read-sms-postback.php",
			"ReferenceID":"'.$smsReferenceID.'",
			"Subject":"",,
			"To":["'.$toPhone.'"],
			"UseMMS":false	
		}';
 
		$url='http://messaging.cdyne.com/Messaging.svc/SendMessage';
		$cURL = curl_init();
		 
		curl_setopt($cURL,CURLOPT_URL,$url);
		curl_setopt($cURL,CURLOPT_POST,true);
		curl_setopt($cURL,CURLOPT_POSTFIELDS,$json);
		curl_setopt($cURL,CURLOPT_RETURNTRANSFER, true);  
		curl_setopt($cURL, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json'));
		$result = curl_exec($cURL);
		//pr($result);	785663	 
		curl_close($cURL);		 
		$array 				      =     json_decode($result, true);

		if(!empty($array) && is_array($array) && array_key_exists(0,$array)){
		//pr($array);
			$new_array  		      = 	$array[0];	
			//pr($new_array);
			if(array_key_exists('MessageID',$new_array)){
				$newSmsMessageId      =     $new_array['MessageID'];
			}
			if(array_key_exists('MessageStatus',$new_array)){
				$newSmsMessageStatus  =     $new_array['MessageStatus'];
			}
		}
		
		if(isset($newSmsMessageStatus) && !empty($newSmsMessageStatus) && ($newSmsMessageStatus == 200 || $newSmsMessageStatus == 201 || $newSmsMessageStatus == 202)){
			$isSuccessfullySent 	 =    1;
		}

		if(VISITOR_IP_ADDRESS	==	"73.202.82.127"){
			//pr($new_array);
			//die("Success");
		}
	}
	catch (Exception $e) {
		if(VISITOR_IP_ADDRESS	==	"73.202.82.127"){
			//$error = $e->getMessage();
			//pr($error);
			//die("Failed");
		}
		
	}

	/////////////////////////////////// THIS IS UPDATE SMS SENDING /////////////////////////
	if(!isset($noSmsActionNeeded)){
		$smsMessage		=	makeDBSafe($smsMessage);

		if(isset($isFromCustomerAutoActive) && $isFromCustomerAutoActive == 1){
			dbQuery("UPDATE members SET activationSmsCode=$smsCode WHERE memberId=$memberId");
		}
		elseif(isset($isFromAttentionOrder) && $isFromAttentionOrder == 1){
			$newSmsID = $orderObj->addNeedAttentionOrderSms("",$smsReferenceID,$orderId,$customerId,$s_employeeId,$newSmsMessageId,"","",$smsMessage,$customerMobileNo,1,$newSmsMessageStatus);

			dbQuery("UPDATE order_attention_messages SET isSentSMS=1,smsId=$newSmsID WHERE messageId=$orderAttentionMsgId AND orderId=$orderId");
		}
		elseif(isset($isFromCustomerOtp) && $isFromCustomerOtp == 1){
			////DO NOTHING///////////////////////////
		}
		elseif(isset($isFromAdminSms) && $isFromAdminSms == 1){
			dbQuery("INSERT INTO admin_customer_sms SET memberId=$memberId,adminId='".$_SESSION['userID']."',adminName='".$_SESSION['loginName']."',smsMessageID='$newSmsMessageId',smsMesseSent='$t_message',sentSmsToPhone='$dispalyCustomerPhone',sentMessageEstDate='".CURRENT_DATE_CUSTOMER_ZONE."',sentMessageEstTime='".CURRENT_TIME_CUSTOMER_ZONE."',sendingFromIP='".VISITOR_IP_ADDRESS."',smsReferenceID='$smsReferenceID'");
		}
		else{
			$newSmsID       =   $orderObj->addOrderMessageSmsNew($smsReferenceID,$orderId,$customerId,$s_employeeId,$newSmsMessageId,$smsMessage,$smsCustomerMobileNo,$newSmsMessageStatus);

	        dbQuery("UPDATE members_employee_messages SET isFromSms=1,smsId=$newSmsID WHERE orderId=$orderId AND memberId=$customerId AND messageId=$messageId");
		}

	}


	if(!isset($noSmsActionNeeded)){	
		$smsSendingErrorMessage     = "";
		if(!empty($newSmsMessageStatus) && array_key_exists($newSmsMessageStatus,$a_smsMessageStatusCodes)){
			$smsSendingErrorMessage = $a_smsMessageStatusCodes[$newSmsMessageStatus];
		}
		
	}

?>