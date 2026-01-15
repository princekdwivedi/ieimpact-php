<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");

	include(SITE_ROOT_EMPLOYEES .	"/includes/top.php");
	include(SITE_ROOT_EMPLOYEES .	"/classes/employee.php");
	include(SITE_ROOT_MEMBERS	.	"/classes/members.php");
	include(SITE_ROOT_EMPLOYEES .	"/includes/common-array.php");
	$employeeObj				=	new employee();
	$memberObj					=	new members();
	$start_time_stamp           =    time();

	/*if(VISITOR_IP_ADDRESS	==	"73.202.82.127"){
		pr($_SESSION);

		die("KASE21");

	}*/

	if(!isset($_SESSION['SUCCESS_VERIFIED_PDF_EMPLOYEE_ID']) && !isset($_SESSION['CUSTOMER_FROM_FACEBOOK']))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/pdf-login.php");
		exit();
	}

	if(isset($_SESSION['SUCCESS_VERIFIED_PDF_EMPLOYEE_ID']))
	{

		$verifiedEmployeeId 	    =	$_SESSION['SUCCESS_VERIFIED_PDF_EMPLOYEE_ID'];
		$fbUserId 				    =	"";

		$query 						=	"SELECT fullName,email,facebookId,facebookEmailId,isActive,isManager,isOutsideCountryEmployee,shiftType,hasAllQaAccess,isLocked,triedFailCount,lockedDate,lockedTime,lockedFromIP,hasverificationAccess,showQuestionnaire,passwordChangeOn FROM employee_details WHERE employeeId=$verifiedEmployeeId AND hasPdfAccess=1 AND isActive=1";

		$result						=	dbQuery($query);
		if(mysqli_num_rows($result)){
			$row					  =	mysqli_fetch_assoc($result);
			$fullName		          =	ucwords(stripslashes($row['fullName']));
			$existingFacebookId       =	$row['facebookId'];
			$existingFacebookEmailId  =   $row['facebookEmailId'];
			$email				      =	stripslashes($row['email']);
			$isActive				  =	$row['isActive'];
			$isManager				  =	$row['isManager'];
			$isOutsideCountryEmployee =	$row['isOutsideCountryEmployee'];
			$shiftType				  =	$row['shiftType'];
			$hasAllQaAccess			  =	$row['hasAllQaAccess'];
			$isLocked				  =	$row['isLocked'];
			$triedFailCount			  =	$row['triedFailCount'];
			$lockedDate				  =	$row['lockedDate'];
			$lockedTime				  =	$row['lockedTime'];
			$lockedFromIP			  =	$row['lockedFromIP'];
			$hasverificationAccess	  =	$row['hasverificationAccess'];
			$showQuestionnaire		  = $row['showQuestionnaire'];
			$passwordChangeOn		  = $row['passwordChangeOn'];
		}
		else{
			unset($_SESSION['SUCCESS_VERIFIED_PDF_EMPLOYEE_ID']);

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/pdf-login.php?error=8");
			exit();
		}

		include_once(SITE_ROOT		.   "/Facebook/autoload.php");
		/*$fb = new Facebook\Facebook(array(
		    'app_id'                => '307211674239541', // Replace with your app id
		    'app_secret'            => 'fe4da22856bbf260082beb2a89160dab',  // Replace with your app secret
		    'default_graph_version' => 'v3.2',
		));*/

		$fb = new Facebook\Facebook(array(
		    'app_id'                => '355824524526802', // Replace with your app id
		    'app_secret'            => 'f3f77e67020aea5379ae18c1a2591fc6',  // Replace with your app secret
		    'default_graph_version' => 'v3.2',
		));
		 
		$helper                     = $fb->getRedirectLoginHelper();

		try {
		  	$accessToken = $helper->getAccessToken();
		}catch(Facebook\Exceptions\FacebookResponseException $e) {
		  // When Graph returns an error
		  $_SESSION['facebook_login_error']  = "<b>Facebook returned an error : </b>" . $e->getMessage().". Please try after sometime.";

		  ob_clean();
		  header("Location: ".SITE_URL_EMPLOYEES."/pdf-facebook-login.php");
		  exit();
		  
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  // When validation fails or other local issues
		  $_SESSION['facebook_login_error']  = "<b>Facebook returned an error : </b>" . $e->getMessage().". Please try after sometime.";

		  ob_clean();
		  header("Location: ".SITE_URL_EMPLOYEES."/pdf-facebook-login.php");
		  exit();
		}
		 
		if (!isset($accessToken)) {
		  if ($helper->getError()) {
		    /*header('HTTP/1.0 401 Unauthorized');
		    echo "Error: " . $helper->getError() . "\n";
		    echo "Error Code: " . $helper->getErrorCode() . "\n";
		    echo "Error Reason: " . $helper->getErrorReason() . "\n";
		    echo "Error Description: " . $helper->getErrorDescription() . "\n";*/

		    $_SESSION['facebook_login_error']  = "<b>Facebook returned an error : </b>" . $helper->getErrorDescription().". Please try after sometime.";

		  } else {
		    /*header('HTTP/1.0 400 Bad Request');
		    echo 'Bad request';*/
		    $_SESSION['facebook_login_error']  = "<b>Facebook returned an error : </b> Bad request. Please try after sometime.";
		  }
		  ob_clean();
		  header("Location: ".SITE_URL_EMPLOYEES."/pdf-facebook-login.php");
		  exit();
		}
		 
		if(!$accessToken->isLongLived()){
		  // Exchanges a short-lived access token for a long-lived one
		  try {
		    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
		  } catch (Facebook\Exceptions\FacebookSDKException $e) {
		    $_SESSION['facebook_login_error'] = "<p>Facebook returned an error, Error getting long-lived access token: </b>" . $e->getMessage() . " Please logout from facebook and login again.";
		    
		    ob_clean();
		    header("Location: ".SITE_URL_EMPLOYEES."/pdf-facebook-login.php");
		    exit();
		  }
		}
		 
		//$fb->setDefaultAccessToken($accessToken);
		 
		# These will fall back to the default access token
		try{
			$res    =   $fb->get('/me?fields=name,email,location,gender,birthday,hometown', $accessToken->getValue());


			$fbUser =   $res->getDecodedBody();	

			if(!empty($fbUser) && is_array($fbUser)){
				//pr($fbUser);
				$facebook_email 	=	"";

				/*if(array_key_exists('email', $fbUser) && !empty($fbUser['email'])){
					echo "KASE1 ".$fbUser['email'];
				}
				else{
					echo "KASE2";
				}
				die();*/
				

				$facebook_name 		=	$fbUser['name'];
				$facebook_id 		=	$fbUser['id'];
				if(array_key_exists('email', $fbUser) && !empty($fbUser['email'])){
					$facebook_email =	$fbUser['email'];

					$existing_facebook_employeeId = $employeeObj->getSingleQueryResult("SELECT employeeId FROM employee_details WHERE facebookEmailId='$facebook_email' AND employeeId <> $verifiedEmployeeId","employeeId");

					$existing_facebook_Id = $employeeObj->getSingleQueryResult("SELECT employeeId FROM employee_details WHERE facebookId='$facebook_id' AND employeeId <> $verifiedEmployeeId","employeeId");

					$makeLogin 		=	false;

					if(!empty($existing_facebook_employeeId) || !empty($existing_facebook_Id)){
						$_SESSION['facebook_login_error'] = "<b>Error :</b> The facebook account you are using is already use by others. please use another one."; //103;

						ob_clean();
		   				header("Location: ".SITE_URL_EMPLOYEES."/pdf-facebook-login.php");
		        		exit();
					}
					elseif(empty($existingFacebookId) && empty($existingFacebookEmailId)){
						//Add the values in first time from facebook data 
						$makeLogin 		=	true;
						dbQuery("UPDATE employee_details SET facebookId='$facebook_id',facebookEmailId='$facebook_email' WHERE employeeId=$verifiedEmployeeId");
					}
					elseif(!empty($existingFacebookId) && !empty($existingFacebookEmailId) && $existingFacebookEmailId != $facebook_email){

						$_SESSION['facebook_login_error'] = "<b>Error :</b> The emailid from Facebook is not match with your existing facebook emailid. Please login into Facebook with your exact Facebook account. Your existing facebook account email is - <b><font color='#6600FF;'>".$existingFacebookEmailId."</font></b>";//102; 

						ob_clean();
		   				header("Location: ".SITE_URL_EMPLOYEES."/pdf-facebook-login.php");
		        		exit();
					}
					elseif(!empty($existingFacebookId) && !empty($existingFacebookEmailId) && $existingFacebookEmailId == $facebook_email){
						//Matching With facebook data 
						$makeLogin 		=	true;
					}

					if($makeLogin == true){
						///////////// MAKE LOGIN WITH SUCESS ///////////////////////
						$_SESSION['employeeId']   =	$verifiedEmployeeId;
						$_SESSION['employeeName'] =	$fullName;
						$_SESSION['employeeEmail']=	$email;
						if($shiftType	==	2)
						{
							$_SESSION['isNightShiftEmployee']	=	1;
						}
						if($isManager	== 1)
						{
							$_SESSION['hasManagerAccess'] =	$isManager;
						}
						
						$_SESSION['hasPdfAccess']         =	1;
						
						if(!empty($hasAllQaAccess))
						{
							$_SESSION['iasHavingAllQaAccess'] =	$hasAllQaAccess;
						}
						if(!empty($hasverificationAccess))
						{
							$_SESSION['isHavingVerifyAccess'] =	$hasverificationAccess;
						}
						$_SESSION['departmentId']      =	2;
						$_SESSION['showQuestionnaire'] =	$showQuestionnaire;
						
						dbQuery("UPDATE employee_details SET isLocked=0,triedFailCount=0,lockedDate='0000-00-00',lockedTime='00:00:00',lockedFromIP='',countFailLogin=0,lastLoginDate='".CURRENT_DATE_INDIA."',lastLoginTime='".CURRENT_TIME_INDIA."' WHERE employeeId=$verifiedEmployeeId");					

						$end_time_stamp         =    time();
						$start_time_stamp       =    $_SESSION['FACEBOOK_LOGIN_PROCESSED_ON'];
		                $time_taken             =    $end_time_stamp-$start_time_stamp;

		                unset($_SESSION['SUCCESS_VERIFIED_PDF_EMPLOYEE_ID']);
		                unset($_SESSION['FACEBOOK_LOGIN_PROCESSED_ON']);

						dbQuery("INSERT INTO employee_login_track SET employeeId=$verifiedEmployeeId,loginDate='".CURRENT_DATE_INDIA."',loginTime='".CURRENT_TIME_INDIA."',loginIP='".VISITOR_IP_ADDRESS."',start_time_stamp='$start_time_stamp',end_time_stamp='$end_time_stamp',time_taken='$time_taken',isFaceboookLogin='yes'");

						$employeeLoginSessionTrackId = mysqli_insert_id($db_conn);


						$_SESSION['employeeLoginSessionTrackId']   =	$employeeLoginSessionTrackId;

						if($passwordChangeOn	==	"0000-00-00")
						{
							$_SESSION['forceResetPassword']	=	1;

							ob_clean();
							header("Location: ".SITE_URL_EMPLOYEES."/change-password.php");
							exit();
						}
						elseif($passwordChangeOn	!=	"0000-00-00")
						{
							$fixedSixtyDaysOldDate		=	getPreviousGivenDate(CURRENT_DATE_INDIA,60);
							if($fixedSixtyDaysOldDate   >   $passwordChangeOn)
							{
								$_SESSION['forceResetPassword']	=	1;
							
								ob_clean();
								header("Location: ".SITE_URL_EMPLOYEES."/change-password.php");
								exit();
							}
							else
							{
								ob_clean();
								header("Location: ".SITE_URL_EMPLOYEES."/employee-details.php");
								exit();
							}
						}
					}
				}
				else{
					$_SESSION['facebook_login_error'] = 101;//"Email from facebook is not retrievable.";

					ob_clean();
		   			header("Location: ".SITE_URL_EMPLOYEES."/pdf-facebook-login.php");
		        	exit();
				}			
			}
			else{
				$_SESSION['facebook_login_error'] = "<p>Facebook returned an error: </b>" . $e->getMessage() . " Please try after sometime.";

				ob_clean();
		   		header("Location: ".SITE_URL_EMPLOYEES."/pdf-facebook-login.php");
		        exit();
			}		

		}
		catch (Exception $e) {
			//$error = $e->getMessage();
			$_SESSION['facebook_login_error'] = "<p>Facebook returned an error: </b>" . $e->getMessage() . " Please try after sometime.";

			ob_clean();
		    header("Location: ".SITE_URL_EMPLOYEES."/pdf-facebook-login.php");
		    exit();
		}
	}
	elseif(isset($_SESSION['CUSTOMER_FROM_FACEBOOK'])){
		include_once(SITE_ROOT		.   "/Facebook/autoload.php");
		$fb = new Facebook\Facebook(array(
		    'app_id'                => '355824524526802', // Replace with your app id
		    'app_secret'            => 'f3f77e67020aea5379ae18c1a2591fc6',  // Replace with your app secret
		    'default_graph_version' => 'v3.2',
		));
		 
		$helper                     = $fb->getRedirectLoginHelper();

		try {
		  	$accessToken = $helper->getAccessToken();
		}catch(Facebook\Exceptions\FacebookResponseException $e) {
		  // When Graph returns an error
		  $_SESSION['customer_facebook_login_error']  = "<b>Facebook returned an error : </b>" . $e->getMessage().". Please try after sometime.";

		  ob_clean();
		  header("Location: ".SITE_URL_MEMBERS."/index.php");
		  exit();
		  
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  // When validation fails or other local issues
		   $_SESSION['customer_facebook_login_error']  = "<b>Facebook returned an error : </b>" . $e->getMessage().". Please try after sometime.";

		   ob_clean();
		   header("Location: ".SITE_URL_MEMBERS."/index.php");
		   exit();
		}
		 
		if (!isset($accessToken)) {
		  if ($helper->getError()) {
		    $_SESSION['customer_facebook_login_error']  = "<b>Facebook returned an error : </b>" . $helper->getErrorDescription().". Please try after sometime.";

		  } else {
		    $_SESSION['customer_facebook_login_error']  = "<b>Facebook returned an error : </b> Bad request. Please try after sometime.";
		  }		  
		   ob_clean();
		   header("Location: ".SITE_URL_MEMBERS."/index.php");
		   exit();
		}
		 
		if(!$accessToken->isLongLived()){
		  // Exchanges a short-lived access token for a long-lived one
		  try {
		    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
		  } catch (Facebook\Exceptions\FacebookSDKException $e) {
		   pr($e->getMessage());
			$_SESSION['customer_facebook_login_error'] = "<p>Facebook returned an error, Error getting long-lived access token: </b>" . $e->getMessage() . " Please logout from facebook and login again.";

			ob_clean();
		    header("Location: ".SITE_URL_MEMBERS."/index.php");
		    exit();
		  }
		}
		 
		//$fb->setDefaultAccessToken($accessToken);
		 
		# These will fall back to the default access token
		try{
			$res    =   $fb->get('/me?fields=name,email,location,gender,birthday,hometown', $accessToken->getValue());
			$fbUser =   $res->getDecodedBody();

			if(!empty($fbUser) && is_array($fbUser)){
				$facebook_name 		=	$fbUser['name'];
				$facebook_id 		=	$fbUser['id'];

				if(array_key_exists('email', $fbUser) && !empty($fbUser['email'])){
					$customer_email =	$fbUser['email'];

					$query			=	"SELECT firstName,lastName,memberId,email,memberType,isReadTerms,securityToken,isMarkedIpCheck,aLaModeCustomerID,totalOrdersPlaced,isLocked,triedFailCount,lockedDate,lockedTime,lockedFromIP,otp,otpSentOn,otpExpireOn,password,oldPassword FROM members WHERE email='$customer_email' AND isActiveCustomer=1 AND memberType='".CUSTOMERS."'";
					$result			=	dbQuery($query);
					if(mysqli_num_rows($result))
					{
						$row				  =	mysqli_fetch_assoc($result);
					    $firstName		      =	stripslashes($row['firstName']);
					    $lastName		      =	stripslashes($row['lastName']);
					    $memberId		      =	$row['memberId'];
					    $memberType			  =	$row['memberType'];
						$isReadTerms		  =	$row['isReadTerms'];
						$isMarkedIpCheck	  =	$row['isMarkedIpCheck'];
						$existingSecurityToken=	$row['securityToken'];
						$t_aLaModeCustomerID  =	$row['aLaModeCustomerID'];
						$totalOrdersPlaced	  =	$row['totalOrdersPlaced'];
						$isLocked			  =	$row['isLocked'];
						$triedFailCount		  =	$row['triedFailCount'];
						$lockedDate			  =	$row['lockedDate'];
						$lockedTime			  =	$row['lockedTime'];
						$lockedFromIP		  =	$row['lockedFromIP'];

						$loginIpCountry				 =	"United States";
						$ip_details                  =  "";


						if($ip_details				   =	getIPDetailsWithAlternateFunctions($customerLoginFromIP))
						{						
						
							$customerLoginIpCountryCode=	$ip_details['country_code'];
							$customerLoginIpCountry	   =	$ip_details['country'];
							if($customerLoginIpCountry ==   "United States of America" || $customerLoginIpCountry ==   "united states of america" || $customerLoginIpCountryCode == "US"  || $customerLoginIpCountryCode == "us"){
								$customerLoginIpCountry =   "United States";
							}

							$customerLoginIpRegion	 =	$ip_details['region'];
							$customerLoginIpCity	 =	$ip_details['city'];
							$customerLoginIpZipCode  =	$ip_details['zipcode'];
							$customerLoginIpLatitude =	$ip_details['latitude'];
							$customerLoginIpLongitude=	$ip_details['longitude'];
							$customerLoginIpISP		 =	$ip_details['ipisp'];
							if(!empty($customerLoginIpCountry))
							{
								$loginIpCountry		 =	strtolower($customerLoginIpCountry);
								$loginIpCountry		 =	ucwords($loginIpCountry);
								
							}
							else{
								$loginIpCountry		 =	"United States";
							}
							
						}
						else{
							$ip_details              =   "";
						}

						if(!empty($memberId))
						{
							$_SESSION['memberId']   =	$memberId;
						}
						if(!empty($email))
						{
							$_SESSION['email']		=	$customer_email;
						}
						if(!empty($firstName))
						{
							$_SESSION['memberName'] =	$firstName." ".$lastName;
						}
						if(!empty($memberType))
						{
							$_SESSION['memberType'] =	$memberType;
						}
						if(!empty($t_aLaModeCustomerID))
						{
							$_SESSION['isThisAloamodeCustomer'] =	$t_aLaModeCustomerID;
						}
						if($totalOrdersPlaced	> 3)
						{
							$_SESSION['iaHavingReferralAccess'] =	1;
						}

						$_SESSION['user_ip_details'] =	$ip_details;
					

						if(isset($_SESSION['memberType']))
						{
							$type		=	$_SESSION['memberType'];
						}

						if($pdue		=	$memberObj->isInvoicePaymentPastDue($memberId))
						{
							$_SESSION['isMemberPaymentDue'] =	$pdue;
						}

						if(isset($_SESSION['needOutsideUsaOTP']))
						{
							unset($_SESSION['needOutsideUsaOTP']);
						}

						if(isset($_SESSION['resendOTPEmail']))
						{
							unset($_SESSION['resendOTPEmail']);
						}

						dbQuery("UPDATE members SET lastLoginDate='".CURRENT_DATE_INDIA."',otp=0,otpSentOn='0000-00-00 00:00:00',otpExpireOn='0000-00-00 00:00:00',directLoginCode='',directLoginExpired='0000-00-00 00:00:00' WHERE memberId=$memberId");
						//////////////////////////  ADD FREE CREDITS /////////////////////////
						if(isset($_SESSION['availFreeCreditCode']))
						{
							$creditId	= $memberObj->updateFreeCreditsIntoAccount($memberId,$_SESSION['availFreeCreditCode']);

							unset($_SESSION['availFreeCreditCode']);
						}
						/////////////////////////////////////////////////////////////////////

						if(isset($_SESSION['tryingLoginIP']) && isset($_SESSION['tryingSameIPCounts']))
						{
							unset($_SESSION['tryingLoginIP']);
							unset($_SESSION['tryingSameIPCounts']);
						}

						$start_time_stamp       =    $_SESSION['FACEBOOK_LOGIN_PROCESSED_ON'];

						unset($_SESSION['CUSTOMER_FROM_FACEBOOK']);
						unset($_SESSION['FACEBOOK_LOGIN_PROCESSED_ON']);


						if($loginId	=	$memberObj->trackCustomerLoginWithTime($memberId, $start_time_stamp))
						{
							if(!empty($customerLoginIpCountry))
							{
								$t_registeredIpCountry	=	makeDBSafe($customerLoginIpCountry);
								$t_registeredIpRegion	=	makeDBSafe($customerLoginIpRegion);
								$t_registeredIpCity		=	makeDBSafe($customerLoginIpCity);
								$t_registeredIpZipCode	=	makeDBSafe($customerLoginIpZipCode);
								$t_registeredIpLatitude	=	makeDBSafe($customerLoginIpLatitude);
								$t_registeredIpLongitude=	makeDBSafe($customerLoginIpLongitude);
								$t_registeredIpISP		=	makeDBSafe($customerLoginIpISP);

								dbQuery("UPDATE track_member_login SET loginIpCity='$t_registeredIpCity',loginIpRegion='$t_registeredIpRegion',loginIpCountry='$t_registeredIpCountry',loginIpZipCode='$t_registeredIpZipCode',loginIpLatitude='$t_registeredIpLatitude',loginIpLongitude='$t_registeredIpLongitude',loginIpISP='$t_registeredIpISP',loginThroughSocial=1,facebookId='$facebook_id' WHERE memberId=$memberId AND loginId=$loginId");
							}							
						}

						if(empty($isReadTerms))
						{
							ob_clean();
							header("Location: ".SITE_URL_MEMBERS."/mark-read-terms.php");
							exit();
						}
						else
						{
							$page	=	$memberObj->loginMemberLoginIntoPage($memberId);
							
							ob_clean();
							header("Location: ".$page);
							exit();
						}

					}
					else{
						$_SESSION['customer_facebook_login_error'] = "<b>Error :</b> The emailid from Facebook is not match with your account.";//102; 

						ob_clean();
		   				header("Location: ".SITE_URL_MEMBERS."/index.php");
		   				exit();
					}
				}
				else{
					$_SESSION['customer_facebook_login_error'] =  "<b>Error :</b> We are not able to get your emaill address from Facebook. Please change your Facebook setting.";//"Email from facebook is not retrievable.";

					ob_clean();
		            header("Location: ".SITE_URL_MEMBERS."/index.php");
		            exit();
				}
			}
			else{
				$_SESSION['customer_facebook_login_error'] = "<p>Facebook returned an error: </b>" . $e->getMessage() . " Please try after sometime.";

				ob_clean();
		        header("Location: ".SITE_URL_MEMBERS."/index.php");
		        exit();
			}				
		}
		catch (Exception $e) {
			//$error = $e->getMessage();
			$_SESSION['customer_facebook_login_error'] = "<p>Facebook returned an error: </b>" . $e->getMessage() . " Please try after sometime.";

			ob_clean();
		    header("Location: ".SITE_URL_MEMBERS."/index.php");
		    exit();
		}
	}
	else{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/pdf-login.php");
		exit();
	}

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>