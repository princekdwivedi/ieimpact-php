<?php
	$validTillNext	 =	getPlusCalculatedMinitue(CURRENT_DATE_INDIA,CURRENT_TIME_INDIA,15);
	list($date,$time)=  explode("=",$validTillNext);

	$codeExpireOn	 =  $date." ".$time;
	$otpCode		 =  rand(1111,9999);

	dbQuery("UPDATE employee_details SET isOtpRequired=1,otpCode='$otpCode',codeExpireOn='$codeExpireOn'  WHERE employeeId=$loginId");

	/*if(!empty($employeePhone)){

		$authKey			=   "8037ANJHHBRKFlGh555cda06";
		$senderId			=   "IMPACT";
		$message			=   "To Login Into ieIMPACT, The OTP is : ".$otpCode." and valid for next 15 minutes";
		$smsMessage			=   urlencode($message);
		$t_mobileNumber		=	stringReplace("+","",$employeePhone);
		$t_mobileNumber		=	stringReplace(",","",$t_mobileNumber);
		$mobileLength		=	strlen($t_mobileNumber);
		

		if($mobileLength	>= 10){
			$to_number	    =	substr($t_mobileNumber, -10);
		}
		else
		{
			$to_number  	=	$t_mobileNumber;	
		}
									
		if(count($to_number) > 0)
		{
			//Define route 
			$route = "4";
			//Prepare you post parameters
			$postData = array(
				'authkey' => $authKey,
				'mobiles' => $to_number,
				'message' => $message,
				'sender'  => $senderId,
				'route'   => $route
			);

			//API URL
			$url="http://sms.ssdindia.com/sendhttp.php";

			// init the resource
			$ch = curl_init();
			curl_setopt_array($ch, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $postData
				//,CURLOPT_FOLLOWLOCATION => true
			));

			//Ignore SSL certificate verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

			//get response
			$output = curl_exec($ch);

			//pr($output);


			//Print error if any
			if(curl_errno($ch))
			{
				//echo 'error:' . curl_error($ch);
			}

			curl_close($ch);

		}
	}*/
	//die();

	include(SITE_ROOT		.   "/includes/send-mail.php");

	$from					=	"hr@ieimpact.com";
	$fromName				=	"HR ieIMPACT ";
	$mailSubject			=	"OTP confirmation alert for your ieIMPACT login";
	$templateId				=	ADMINISTRATOR_SENDING_EMAIL_EMPLOYEES;
	if(isset($isForDiffSystem) && $isForDiffSystem == 1){
		$smsMessage             =   "Please verify your ieIMPACT employee login for which One Time Password (OTP) has been generated and sent on your registered mobile number on ".showDate(CURRENT_DATE_INDIA)." at ".CURRENT_TIME_INDIA." and valid for next 15 minutes. The OTP is - <b><u>".$otpCode."</u></b><br /><br />In case you have not logged in to your ieIMPACT employee account, please call HR department";	
	}
	else{
		$smsMessage             =   "You have accessed ieIMPACT employee login for which One Time Password (OTP) has been generated and sent on your registered mobile number on ".showDate(CURRENT_DATE_INDIA)." at ".CURRENT_TIME_INDIA." and valid for next 15 minutes. The OTP is - <b><u>".$otpCode."</u></b><br /><br />In case you have not logged in to your ieIMPACT employee account, please call HR department";	
	}

	$a_templateData	=	array("{employeeName}"=>$fullName,"{message}"=>$smsMessage);

	@sendTemplateMail($from, $fromName, $employeeEmail, $mailSubject, $templateId, $a_templateData);
	
?>