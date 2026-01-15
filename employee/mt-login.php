<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");

	include(SITE_ROOT_EMPLOYEES .	"/includes/top.php");
	include(SITE_ROOT_EMPLOYEES .	"/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES .	"/includes/common-array.php");
	include(SITE_ROOT			.	"/classes/validate-fields.php");
	include(SITE_ROOT			.	"/classes/common.php");
	include(SITE_ROOT			.   "/classes/email-templates.php");
	$emailObj					=	new emails();

	/////////////////////// FUNCTION TO GET GOOGLE RE-CAPTCHA CURL DATA ///////////////////
	function getCurlData($url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16");
		$curlData = curl_exec($curl);
		curl_close($curl);
		return $curlData;
	}

	$loginFormHeadingText		=	"MT Employee Login Area";
	$form						=	SITE_ROOT_EMPLOYEES.    "/forms/new-login-otp.php";
	$commonClass				=	new common();

	$a_managerEmails			=	$commonClass->getMangersEmails();
	$checkingFailCaptcha		=	"e11185b6e35c1b767174dc988aa0f179";
	$checkingFailCaptchaCode	=	"70b29c4920daf4e51e8175179027e668";

	if($s_visitingFromMobileComputer	!=	"computer")
	{
		$deviceType				=	"Mobile";
	}
	else
	{
		$deviceType				=	"Computer";
	}

	if(VISITOR_BROWSER_TYPE		==   "IE Browser")
	{
		$userBowser				=	 "ie Browser";
	}
	else
	{
		$userBowser				=    getUsersBrowserTypes();
	}

	$userOs						=    getUserOperatingSystem();


	$loginId					=	"";
	$employeeSecurityCode		=	EMPLOYEE_SPECIAL_SECURITY_CODE;
	$password					=	"";
	$employeeObj				=	new employee();
	$validator					=	new validate();
	$errorMsg					=	"";
	$loginError					=	"";
	if(isset($_SESSION['employeeId']))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES ."/employee-details.php");
		exit();
	}
	if(isset($_SESSION['mtemployeeId']))
	{
		ob_clean();
		header("Location: ".SITE_URL_MTEMPLOYEES);
		exit();
	}
	$urlRef						=	SITE_URL_MTEMPLOYEES;
	$domain						=	REMEMBER_PASSWORD_ON_SITE;
	$rememberEmployeePass		=	"";
	$rememberEmail				=	"";
	$rememberSecurityToken		=	"";
	$rememberID					=	"";
	$pwdChecked					=	"";
	$securityToken				=	"";

	$plusNumber1				=	rand(1,9);
	$plusNumber2				=	rand(1,9);
	$numberSum					=	$plusNumber1+$plusNumber2;
	$calSum						=	"";
	$employeeLoginFromIP		=	VISITOR_IP_ADDRESS;
	$employeeLoginFromIPUptoTwo	=	getFirstTwoIpDigits($employeeLoginFromIP);
	if(VISITOR_IP_ADDRESS		==	"122.160.167.153"){
		$employeeLoginFromIP	=	VISITOR_IP_ADDRESS;
		$employeeLoginFromIPUptoTwo	=	getFirstTwoIpDigits($employeeLoginFromIP);
		echo "xxx-".$employeeLoginFromIPUptoTwo;
		echo "<br />YYY-".$employeeLoginFromIP;

		/*$user_ip_data				=	getIPAccessDetails("73.92.161.253");
		pr($user_ip_data);
		echo "<br />KASE1".$employeeLoginIpCountry		=	$user_ip_data['country'];
		echo "<br />KASE2".$loginIpCountry			    =	$user_ip_data['country'];
		die();*/

	}

	$showCaptcha				=	"0";	
	
	$isPdfEmployee				=	0;
	$failLoginPassword			=	"";
	$s_failLoginEmailPassword	=	0;

		
	$isRequiredOtp				=	0;
	$otpCode					=	"";
	if(isset($_SESSION['isNeededOtp'])){
		$isRequiredOtp			=	1;
	}
	

	if(isset($_GET[$checkingFailCaptcha]))
	{
		$getsecure				=	$_GET[$checkingFailCaptcha];
		if($getsecure			==	$checkingFailCaptchaCode)
		{
			$showCaptcha		=	1;
		}
		else
		{
			$showCaptcha		=	1;
		}
	}


	if(isset($_COOKIE['rememberEmployeePass']))
	{
		$rememberEmployeePass=	$_COOKIE['rememberEmployeePass'];
		$rememberEmployeePass=	base64_decode($rememberEmployeePass);
		$pwdChecked			 =	"checked";
	}
	
	if(isset($_COOKIE['rememberEmail']))
	{
		$rememberEmail		        =	$_COOKIE['rememberEmail'];
	}
	if(isset($_COOKIE['rememberSecurityToken']))
	{
		$rememberSecurityToken		=	$_COOKIE['rememberSecurityToken'];
	}

	if(isset($_COOKIE['rememberID']))
	{
		$rememberID					=	$_COOKIE['rememberID'];
	}


	if(isset($_GET['urlRef']))
	{
		$urlRef	=	$_GET['urlRef'];
	}
	if(isset($_GET['error']))
	{
		$error		=	$_GET['error'];
		if($error	==	1)
		{
			$loginError	=	"Login ID or Password or Security Token.";
		}
		elseif($error	==	2)
		{
			$loginError	=	"Login ID or Password or Security Token.";
		}
		elseif($error	==	3)
		{
			$loginError	=	"Your account has some problem.<br /> Please contact support@ieimpact.com.<br /> Please mention error code X1234.";
		}
		elseif($error	==	5)
		{
			$loginError	=	"Login ID or Password or Security Token Mismatch";
		}
		elseif($error	==	6)
		{
			$loginError	=	"We are sending an O.T.P in your registered mobile and email";
		}
		elseif($error	==	7)
		{
			$loginError	=	"Due to long inactivity, your account is deactivated. Please contact your manager or HR.";
		}
	}
	if(isset($_REQUEST['formsubmitted']))
	{
		extract($_REQUEST);
		$start_time_stamp   =    time();

		$loginEmail			=	trim($loginEmail);
		$password			=	trim($password);
		$securityToken		=	trim($securityToken);

		$securityToken		=	makeDBSafe($securityToken);
		$loginEmail			=	makeDBSafe($loginEmail);
		$password			=	makeDBSafe($password);

		$failLoginPassword	=   $password;

		$rememberPass		=	base64_encode($password);

		if(isset($_POST['rememberCheckPass']))
		{
			setcookie('rememberEmail',$loginEmail, time()+60*60*24*15, '/', $domain);
			setcookie('rememberEmployeePass',$rememberPass, time()+60*60*24*15, '/', $domain);
			setcookie('rememberSecurityToken',$securityToken, time()+60*60*24*15, '/', $domain);
		}
		else
		{
			setcookie('rememberEmail','',false,'/',$domain);
			setcookie('rememberEmployeePass','',false,'/',$domain);
			setcookie('rememberSecurityToken','',false,'/',$domain);
		}
		$loginId		=	0;


		if($user_ip_data				=	getIPDetailsWithAlternateFunctions(VISITOR_IP_ADDRESS))
		{	
			$employeeLoginIpCountryCode =	$user_ip_data['country_code'];
			$employeeLoginIpCountry		=	$user_ip_data['country'];
			if(empty($employeeLoginIpCountry)){
				if($employeeLoginIpCountryCode == "IN"  || $employeeLoginIpCountryCode == "IN"){
					$employeeLoginIpCountry =   "India";
				}
			}
			$employeeLoginIpRegion		=	$user_ip_data['region'];
			$employeeLoginIpCity		=	$user_ip_data['city'];
			$employeeLoginIpZipCode		=	$user_ip_data['zipcode'];
			$employeeLoginIpLatitude	=	$user_ip_data['latitude'];
			$employeeLoginIpLongitude	=	$user_ip_data['longitude'];
			$employeeLoginIpISP			=	$user_ip_data['ipisp'];
			$loginIpCountry			    =	$employeeLoginIpCountry;
		}
		else{
			$employeeLoginIpCountry		=	"";
			$employeeLoginIpRegion		=	"";
			$employeeLoginIpCity		=	"";
			$employeeLoginIpZipCode		=	"";
			$employeeLoginIpLatitude	=	"";
			$employeeLoginIpLongitude	=	"";
			$employeeLoginIpISP			=	"";
			$loginIpCountry			    =	"";
		}
				

		$validator->checkField($loginEmail, '', 'Please enter your email.');
		if(!empty($loginEmail))
		{
			$emailDomain 	   = getEmailDomain($loginEmail);
			$disposableEmails  = blockEmailAddress();
			if(!filter_var($loginEmail, FILTER_VALIDATE_EMAIL))
			{
				$validator->setError("Enter a valid email.");
			}
			elseif(!empty($emailDomain) && in_array($emailDomain,$disposableEmails)){
                $validator->setError("The email address is invalid. Please check.");
            }
		}
		$validator->checkField($password,"","Enter your password.");
		$validator->checkField($securityToken, '', 'Please enter security token.');

		if($isRequiredOtp	==	 1 && empty($otpCode)){
			$validator->setError("Please enter O.T.P. received.");
		}

		$displayCaptchaError	=	0;
		
		if($showCaptcha			==	1)
		{
		    if(isset($_POST['g-recaptcha-response']))
			{			
				$recaptcha  =  $_POST['g-recaptcha-response'];

				$google_url = "https://www.google.com/recaptcha/api/siteverify";
				
				$verify_url = "https://www.google.com/recaptcha/api/siteverify?secret=".GOOGLE_RECAPTCHA_SECRET."&response=".$recaptcha."&remoteip=".$_SERVER['REMOTE_ADDR'];
				$resp  =  getCurlData($verify_url);
				$res   =  json_decode($resp, TRUE);
				
				if(!$res['success'])
				{
					$validator->setError("Please click the check box below.");
					$displayCaptchaError		=	1;
				}
			}
			else
			{
				$validator->setError("Please click the check box below.");
				$displayCaptchaError		=	1;
			}
		}

		if(!empty($loginEmail))
		{
			$currentDateTime	=	CURRENT_DATE_INDIA." ".CURRENT_TIME_INDIA;
			$query				=	"SELECT employeeId,email,fullName,mobile,hasOutsideLoginAccess,hasPdfAccess,isActive,isOtpRequired,otpCode,codeExpireOn,bypassOtp,hasPdfAccess,hasOutsideLoginAccess,securityCode,passwordChangeOn,securityCodeChangeOn,securityCodeChangeTime,lastLoginDate,lastLoginTime FROM employee_details WHERE email='$loginEmail' AND isActive=1 AND hasPdfAccess=0";
			$result							=	dbQuery($query);
			if(mysqli_num_rows($result)){
				$row						=	mysqli_fetch_assoc($result);
				$fullName					=	$row['fullName'];
				$employeeEmail				=	$row['email'];
				$employeePhone				=	$row['mobile'];
				$loginId	    			=	$row['employeeId'];
				$t_hasOutsideLoginAccess	=	$row['hasOutsideLoginAccess'];
				$t_hasPdfAccess				=	$row['hasPdfAccess'];
				$t_isActive   				=	$row['isActive'];
				$t_isOtpRequired			=	$row['isOtpRequired'];
				$t_otpCode   				=	$row['otpCode'];
				$t_codeExpireOn				=	$row['codeExpireOn'];
				$t_bypassOtp				=	$row['bypassOtp'];
				$employeeType				=	$row['hasPdfAccess'];
			    $isAllowingOutsideLogin		=	$row['hasOutsideLoginAccess'];
			    $employeeOwnSecurityCode 	=	$row['securityCode'];
			    $lastPasswordChangedOn      =	$row['passwordChangeOn'];
			    $securityCodeChangeOn       =	$row['securityCodeChangeOn'];
			    $securityCodeChangeTime     =	$row['securityCodeChangeTime'];
			    $lastLoginDate              =	$row['lastLoginDate'];
			    $lastLoginTime              =	$row['lastLoginTime'];
				
			    $isForcedToDeactivateAccount=   0;
			   
			    $securityTokenUpdatedDateTime = $securityCodeChangeOn." ".$securityCodeChangeTime;
			    $lastLoginDateTime            = $lastLoginDate." ".$lastLoginTime;

				if(empty($employeePhone)){
					$employeePhone	=	"";
				}
			}
			else{
				$loginId=	0;
				$validator->setError("Login ID or Password or Security Token.");
			}
		}
		else{
			$validator->setError("Login ID or Password or Security Token.");
		}

		if(!empty($securityToken) && !empty($loginId) && $displayCaptchaError == 0 && $validator)
		{	
			if($securityToken		!=  $employeeSecurityCode)
			{
				if($securityTokenUpdatedDateTime > $lastLoginDateTime){
                    $failLoginMessage	=	"Security token in updated";
				    $validator->setError("Your security token in updated, please click How To Get It and get security token in email.");
				}
				else{
					$validator->setError("Login ID or Password or Security Token.");
				    $failLoginMessage	=	"Incorrect Security Code Entered";
				}
				

				$employeeObj->trackFailEmployeeLogin($loginId,$loginEmail,$failLoginPassword,$securityToken,$failLoginMessage,$employeeLoginFromIP,$isPdfEmployee,$employeeLoginIpCity,$employeeLoginIpRegion,$employeeLoginIpCountry,$employeeLoginIpISP);

				include(SITE_ROOT_EMPLOYEES."/includes/sending-fail-login.php");

				$attemptedTimes		=	$employeeObj->updateCountFailedlogin($loginId);
				if($attemptedTimes > 3 && $attemptedTimes < 6)
				{
					ob_clean();
					header("Location: ".SITE_URL_EMPLOYEES."/mt-login.php?error=5&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
					exit();
				}
				elseif($attemptedTimes == 6)
				{
					ob_clean();
					header("Location: ".SITE_URL_EMPLOYEES."/mt-login.php?error=5&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
					exit();
				}
				elseif($attemptedTimes > 6)
				{
					include(SITE_ROOT_EMPLOYEES."/includes/sending-lock-account.php");
					
					ob_clean();
					header("Location: ".SITE_URL_EMPLOYEES."/mt-login.php?error=4&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
					exit();
				}
			}
			
		}
		$dataValid	 =	$validator ->isDataValid();
		if($dataValid)
		{			
			
			if($isRequiredOtp	 == 1){
				if($otpCode     !=  $t_otpCode){
					$errorMsg	 =	"<font color='#ff0000;'>Entered invalid O.T.P.</font>";
					include($form);
				}
				elseif($currentDateTime >= $t_codeExpireOn){
					$errorMsg	 =	"<font color='#ff0000;'>The O.T.P. you entered is expired.</font>";
					include($form);
				}
			}

			$otpErrorMessage	=	"";
			if($isRequiredOtp	 == 1){
				if($otpCode     !=  $t_otpCode){
					$otpErrorMessage	 =	"<font color='#ff0000;'>Entered invalid O.T.P.</font>";	
				}
				elseif($currentDateTime >= $t_codeExpireOn){
					$otpErrorMessage	 =	"<font color='#ff0000;'>The O.T.P. you entered is expired.</font>";
				}
			}
			if(!empty($otpErrorMessage)){
				$errorMsg	 =	$otpErrorMessage;
				include($form);
			}
			else{			
				
				if(empty($securityCaptcha) && !empty($loginFromEmpCityName) && $loginFromEmpCityName == "chandigarh")
				{					

					$result	    =   $employeeObj->MTEmployeeLoginWithNewPassword($loginId,$password,$loginEmail,$loginIpCountry);
					
					if($result)
					{
						
						if(!empty($isForcedToDeactivateAccount)){

							$exactOriginalEmail			=	$employeeEmail;
							$email						=	$employeeEmail.".OLD";
							$deactivatedDate			=	CURRENT_DATE_INDIA;

							//////////////////////////// FORCED TO DEACTIVATE ACCOUNT ////////
							dbQuery("UPDATE employee_details SET isActive=0,email='$email',exactOriginalEmail='$exactOriginalEmail',deactivatedBy='$s_mtemployeeName',deactivatedDate='".CURRENT_DATE_INDIA."' WHERE employeeId=$loginId");

							if(isset($_SESSION['employeeId'])){
								unset($_SESSION['employeeId']);
							}
							if(isset($_SESSION['employeeName'])){
								unset($_SESSION['employeeName']);
							}
							if(isset($_SESSION['employeeEmail'])){
								unset($_SESSION['employeeEmail']);
							}
							if(isset($_SESSION['isNightShiftEmployee'])){
								unset($_SESSION['isNightShiftEmployee']);
							}
							if(isset($_SESSION['hasManagerAccess'])){
								unset($_SESSION['hasManagerAccess']);
							}
							if(isset($_SESSION['hasPdfAccess'])){
								unset($_SESSION['hasPdfAccess']);
							}
							if(isset($_SESSION['iasHavingAllQaAccess'])){
								unset($_SESSION['iasHavingAllQaAccess']);
							}
							if(isset($_SESSION['isHavingVerifyAccess'])){
								unset($_SESSION['isHavingVerifyAccess']);
							}
							if(isset($_SESSION['departmentId'])){
								unset($_SESSION['departmentId']);
							}

							ob_clean();
							header("Location: ".SITE_URL_EMPLOYEES."/mt-login.php?error=7");
							exit();

						}

						if(is_numeric($result))
						{
							if(isset($_SESSION['isNeededOtp'])){
								
								dbQuery("UPDATE employee_details SET isOtpRequired=0,otpCode='',codeExpireOn='0000-00-00 00:00:00' WHERE employeeId=$loginId");
								unset($_SESSION['isNeededOtp']);
							}

							$end_time_stamp         =    time();
			                $time_taken             =    $end_time_stamp-$start_time_stamp;

							dbQuery("INSERT INTO employee_login_track SET employeeId=$loginId,loginDate='".CURRENT_DATE_INDIA."',loginTime='".CURRENT_TIME_INDIA."',loginIP='$employeeLoginFromIP',start_time_stamp='$start_time_stamp',end_time_stamp='$end_time_stamp',time_taken='$time_taken'");

							dbQuery("UPDATE employee_details SET lastLoginDate='".CURRENT_DATE_INDIA."',lastLoginTime='".CURRENT_TIME_INDIA."' WHERE employeeId=$loginId");

							$employeeLoginSessionTrackId			   = mysqli_insert_id($db_conn);


							$_SESSION['employeeLoginSessionTrackId']   =	$employeeLoginSessionTrackId;

							if(isset($_SESSION['hasPdfAccess']) && $_SESSION['hasPdfAccess'] == 1)
							{
								if(!in_array($employeeLoginFromIPUptoTwo,$a_officeIPAddressUptoTwo))
								{
									include(SITE_ROOT_EMPLOYEES."/includes/track-outside-office-login.php");
								}
							}

							if($lastPasswordChangedOn	==	"0000-00-00")
							{
								$_SESSION['forceResetPassword']	=	1;

								ob_clean();
								header("Location: ".SITE_URL_MTEMPLOYEES."/change-password.php");
								exit();
							}
							elseif($lastPasswordChangedOn	!=	"0000-00-00")
							{
								$fixedSixtyDaysOldDate		=	getPreviousGivenDate(CURRENT_DATE_INDIA,60);
								if($fixedSixtyDaysOldDate   >   $lastPasswordChangedOn)
								{
									$_SESSION['forceResetPassword']	=	1;
								
									ob_clean();
									header("Location: ".SITE_URL_MTEMPLOYEES."/change-password.php");
									exit();
								}
								else
								{
									if(isset($_SESSION['departmentId']) && $_SESSION['departmentId'] == 1)
									{
										ob_clean();
										header("Location: ".SITE_URL_MTEMPLOYEES);
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
						else
						{
							if($result == "iperror")
							{
								$failLoginMessage	=	"Seems like trying to login from outside India.";

								$employeeObj->trackFailEmployeeLogin($loginId,$loginEmail,$failLoginPassword,$securityToken,$failLoginMessage,$employeeLoginFromIP,$isPdfEmployee,$employeeLoginIpCity,$employeeLoginIpRegion,$employeeLoginIpCountry,$employeeLoginIpISP);

								include(SITE_ROOT_EMPLOYEES."/includes/sending-fail-login.php");
								$totalfailedCount	=	$employeeObj->updateFailedCount($loginId);
								if($totalfailedCount > 3)
								{
									ob_clean();
									header("Location: ".SITE_URL_EMPLOYEES."/mt-login.php?error=3&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
									exit();
								}
								else
								{
									ob_clean();
									header("Location: ".SITE_URL_EMPLOYEES."/mt-login.php?error=3");
									exit();
								}
								
							}
							elseif($result			== "errorFailLogin")
							{
								$failLoginMessage	=	"Seems like trying to login for a locked account.";

								$employeeObj->trackFailEmployeeLogin($loginId,$loginEmail,$failLoginPassword,$securityToken,$failLoginMessage,$employeeLoginFromIP,$isPdfEmployee,$employeeLoginIpCity,$employeeLoginIpRegion,$employeeLoginIpCountry,$employeeLoginIpISP);

								include(SITE_ROOT_EMPLOYEES."/includes/sending-fail-login.php");

								include(SITE_ROOT_EMPLOYEES."/includes/sending-lock-account.php");
								
								ob_clean();
								header("Location: ".SITE_URL_EMPLOYEES."/mt-login.php?error=4&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
								exit();
							}
							else
							{
								
								$failLoginMessage	=	"Seems like account is not yet activated by administrator.";

								$employeeObj->trackFailEmployeeLogin($loginId,$loginEmail,$failLoginPassword,$securityToken,$failLoginMessage,$employeeLoginFromIP,$isPdfEmployee,$employeeLoginIpCity,$employeeLoginIpRegion,$employeeLoginIpCountry,$employeeLoginIpISP);

								include(SITE_ROOT_EMPLOYEES."/includes/sending-fail-login.php");

								$attemptedTimes		=	$employeeObj->updateCountFailedlogin($loginId);
								if($attemptedTimes > 3 && $attemptedTimes < 6)
								{
									ob_clean();
									header("Location: ".SITE_URL_EMPLOYEES."/mt-login.php?error=2&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
									exit();
								}
								elseif($attemptedTimes > 6)
								{
									include(SITE_ROOT_EMPLOYEES."/includes/sending-lock-account.php");
									
									ob_clean();
									header("Location: ".SITE_URL_EMPLOYEES."/mt-login.php?error=4&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
									exit();
								}
								elseif($attemptedTimes == 6)
								{															
									ob_clean();
									header("Location: ".SITE_URL_EMPLOYEES."/mt-login.php?error=2&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
									exit();
								}
								else
								{
									ob_clean();
									header("Location: ".SITE_URL_EMPLOYEES."/mt-login.php?error=2");
									exit();
								}
							}
						}
					}
					else
					{
						$failLoginMessage	=	"Seems like Login ID and Email and Password Mismatch.";

						$employeeObj->trackFailEmployeeLogin($loginId,$loginEmail,$failLoginPassword,$securityToken,$failLoginMessage,$employeeLoginFromIP,$isPdfEmployee,$employeeLoginIpCity,$employeeLoginIpRegion,$employeeLoginIpCountry,$employeeLoginIpISP);

						if(isset($_SESSION['failLoginEmailPassword']))
						{
							$s_failLoginEmailPassword	=	$_SESSION['failLoginEmailPassword'];
							$s_failLoginEmailPassword	=	$s_failLoginEmailPassword+1;
							$_SESSION['failLoginEmailPassword']		=	$s_failLoginEmailPassword;
						}
						else
						{
							$_SESSION['failLoginEmailPassword']	=	1;
						}

						if($s_failLoginEmailPassword >= 3)
						{
							include(SITE_ROOT_EMPLOYEES."/includes/sending-fail-login.php");
						}

						$attemptedTimes		=	$employeeObj->updateCountFailedlogin($loginId);
						if($attemptedTimes > 3 && $attemptedTimes < 6)
						{
							ob_clean();
							header("Location: ".SITE_URL_EMPLOYEES."/mt-login.php?error=1&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
							exit();
						}
						elseif($attemptedTimes == 6)
						{
							ob_clean();
							header("Location: ".SITE_URL_EMPLOYEES."/mt-login.php?error=1&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
							exit();
						}
						elseif($attemptedTimes > 6)
						{
							include(SITE_ROOT_EMPLOYEES."/includes/sending-lock-account.php");
							
							ob_clean();
							header("Location: ".SITE_URL_EMPLOYEES."/mt-login.php?error=4&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
							exit();
						}
						else
						{
							ob_clean();
							header("Location: ".SITE_URL_EMPLOYEES."/mt-login.php?error=1");
							exit();
						}
					}
				}
				else
				{
					include($form);
				}
			}
		}
		else
		{
			$errorMsg	 =	$validator ->getErrors();
			include($form);
		}
	}
	else
	{
		include($form);
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>