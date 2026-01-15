<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");

	
	//$toPhone           =   "+14083387741";
	$smsKey			   =	SMS_CDYNE_KEY;
	$toPhone           =   "+917042145367";
	$smsMessage	       =	"This is gaurab test. No need to reply i will check in cdyne response";
	//include(SITE_ROOT_EMPLOYEES .  "/includes/sending-sms-customer.php");
	//die();


	
	$smsMessage		   =	str_replace("<br>", " ", $smsMessage);
	$smsMessage		   =	str_replace("</ br>", " ", $smsMessage);
	$smsMessage		   =	str_replace("</ br>", " ", $smsMessage);
	$smsReferenceID	   =    "121121-".rand(11,99)."-".substr(md5(microtime()+rand()+date('s')),0,5);
	$a_messageStatus   =   cdyneStatusCodes();

	$smsReturnPath	   =	"https://secure.ieimpact.com/read-sms-postback.php"; 
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
			"Subject":"",
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
		//pr($result);		 
		curl_close($cURL);		 
		$array = json_decode($result, true);
		//pr($array);
		$new_array  = 	$array[0];	
		pr($new_array);
		die();
		echo "xxx-".$messageid  =    $new_array['MessageID'];
		echo "<br />-".$messageStatus  =    $new_array['MessageStatus'];
		if(isset($messageStatus) && !empty($messageStatus) && array_key_exists($messageStatus,$a_messageStatus)){
			echo "<br />Status-".$a_messageStatus[$messageStatus];
		}
		
	}
	catch (Exception $e) {
		$error = $e->getMessage();
		pr($error);
		die();
	}
	
	/*$smsReturnPath		 =	"http://www.ieimpact.com/read-sms-postback.php"; 

	$smsKey			     =	SMS_CDYNE_KEY;
	$client			     =	new SoapClient('http://sms2.cdyne.com/sms.svc?wsdl');

	$lk				     =	$smsKey;
	$smsMessage		     =	"This is gaurab test. No need to reply i will check in cdyne response";

	class AdvancedCallRequestData
	{
		public $AdvancedRequest;
		 
		function AdvancedCallRequestData($licensekey,$requests)
		{ 
		  $this->AdvancedRequest = array();
		  $this->AdvancedRequest['LicenseKey']  = $licensekey;
		  $this->AdvancedRequest['SMSRequests'] = $requests;
		}
	}

	$PhoneNumbersArray1=    array('14083387741');
	$smsReferenceID	   =	rand(111,999);
		 
	$RequestArray = array(
		array(
			'AssignedDID'=>'+447937985815',
								  //If you have a Dedicated Line, you would assign it here.
			'Message'	=>$smsMessage,   
			'PhoneNumbers'=>$PhoneNumbersArray1,
			'ReferenceID'=>$smsReferenceID,
								  //User defined reference, set a reference and use it with other SMS functions.
			//'ScheduledDateTime'=>'2010-05-06T16:06:00Z',
								  //This must be a UTC time.  Only Necessary if you want the message to send at a later time.
			//'StatusPostBackURL'=>$smsReturnPath 
								  //Your Post Back URL for responses.
		)
	);
		 
	$request		=   new AdvancedCallRequestData($smsKey,$RequestArray);
	pr($request);
	$result			=   $client->AdvancedSMSsend($request);
	pr($request);

	$result1		=	convertObjectToArray($result);
	pr($result1);
	$mainResult	    =	$result1['AdvancedSMSsendResult'];
	$a_mainSmsResult=	$mainResult['SMSResponse'];
	pr($a_mainSmsResult);
	die();*/

	/* $url     			= 'http://rest.nexmo.com/sms/json?api_key=8485a866&api_secret=5e61daaa&from=Gaurab Kr Baruah&to=917042145367&text=Hey how is the match';

	 $curl 				=  new anlutro\cURL\cURL;
	 $responsecurl 		=  $curl->get($url);

	 $body 				=  json_decode($responsecurl->body, true);
	 pr($body);

	 $status 			=  $body['messages'][0]['status'];

	if($status == 0){                        
		echo   "Successfully sent";
	}
	else{
		echo    "Fail sending";
	}

	/*include(SITE_ROOT. "/classes/nexmo-message.php");

	$nexmo_sms = new NexmoMessage('8485a866', '5e61daaa');

	// Step 2: Use sendText( $to, $from, $message ) method to send a message. 
	$info = $nexmo_sms->sendText( '917042145367', '397525856425', 'Test SMS Please Ignore');
	pr($info);

	$result1		=	convertObjectToArray($info);

	pr($result1);

	$mainResult	    =	$result1['messages'];

	pr($mainResult);*/

	// Step 3: Display an overview of the message
	//$result	=	 $nexmo_sms->displayOverview($info);
	//pr($result);

	//https://rest.nexmo.com/sms/json?api_key=8485a866&api_secret=5e61daaa&from=MyCompany20&to=+919988694910&text=Hey how is the match1
?>