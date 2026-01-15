<?php
	ob_start();
	session_start();
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
	$urlRef						=	SITE_URL_EMPLOYEES."/employee-details.php";
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
	$showCaptcha				=	"0";	
	
	$isPdfEmployee				=	0;
	$failLoginPassword			=	"";
	$s_failLoginEmailPassword	=	0;

	$a_officeIPAddress			=	array();
	$a_officeIPAddressUptoTwo	=	array();
	
	$isRequiredOtp				=	0;
	$otpCode					=	"";
	if(isset($_SESSION['isNeededOtp'])){
		$isRequiredOtp			=	1;
	}

	$query								=	"SELECT * FROM office_ip_addresses_list WHERE isActive='yes'";
	$result								=	dbQuery($query);
	if(mysql_num_rows($result))
	{
		while($row						=	mysql_fetch_assoc($result)){
			$ipAddress					=	stripslashes($row['ipAddress']);
			$isActive					=	stripslashes($row['isActive']);

			$a_officeIPAddress[]		=	$ipAddress;
			$a_officeIPAddressUptoTwo[]	=	getFirstTwoIpDigits($ipAddress);
		}
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
	}
	if(isset($_REQUEST['formsubmitted']))
	{
		extract($_REQUEST);

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

		/////////////////////// THIS FUNCTION IS TO GET EMPLOYEE IP DETAILS ///////////////
		$user_ip_data				=	$commonClass->getUserIPDetails();
		$employeeLoginIpCountry		=	$user_ip_data['employeeLoginIpCountry'];
		$employeeLoginIpRegion		=	$user_ip_data['employeeLoginIpRegion'];
		$employeeLoginIpCity		=	$user_ip_data['employeeLoginIpCity'];
		$employeeLoginIpZipCode		=	$user_ip_data['employeeLoginIpZipCode'];
		$employeeLoginIpLatitude	=	$user_ip_data['employeeLoginIpLatitude'];
		$employeeLoginIpLongitude	=	$user_ip_data['employeeLoginIpLongitude'];
		$employeeLoginIpISP			=	$user_ip_data['employeeLoginIpISP'];
		$loginIpCountry			    =	$user_ip_data['loginIpCountry'];

		$validator->checkField($loginEmail, '', 'Please enter your email.');
		if(!empty($loginEmail))
		{
			$validator->checkField($loginEmail, 'E', 'Enter a valid email.');
		}
		$validator ->checkField($password,"","Enter your password.");
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
			$query				=	"SELECT employeeId,email,fullName,mobile,hasOutsideLoginAccess,hasPdfAccess,isActive,isOtpRequired,otpCode,codeExpireOn FROM employee_details WHERE email='$loginEmail' AND isActive=1";
			$result				=	dbQuery($query);
			if(mysql_num_rows($result)){
				$row			=	mysql_fetch_assoc($result);
				$fullName		=	$row['fullName'];
				$employeeEmail	=	$row['email'];
				$employeePhone	=	$row['mobile'];
				$loginId	    =	$row['employeeId'];
				$t_hasOutsideLoginAccess	=	$row['hasOutsideLoginAccess'];
				$t_hasPdfAccess	=	$row['hasPdfAccess'];
				$t_isActive   	=	$row['isActive'];
				$t_isOtpRequired=	$row['isOtpRequired'];
				$t_otpCode   	=	$row['otpCode'];
				$t_codeExpireOn	=	$row['codeExpireOn'];

				if(empty($employeePhone)){
					$employeePhone	=	"";
				}
				

				//////////////////////////////////////////////////////////////////////
				/******************* CHECKING FOR OUTSIDE OFFICE AND OTP ************/
				if($t_hasOutsideLoginAccess == 1 && $loginId != 8 && $t_isActive == 1 && $t_hasPdfAccess == 1 && !empty($a_officeIPAddress) && !in_array($employeeLoginFromIPUptoTwo,$a_officeIPAddressUptoTwo) && $isRequiredOtp == 0){
					
					$_SESSION['isNeededOtp'] =  1;
					$isSendOtp				 =	1;
					

					$isAlreadySent	    =	@mysql_result(dbQuery("SELECT isOtpRequired FROM employee_details WHERE employeeId=$loginId AND codeExpireOn >= '$currentDateTime' AND isOtpRequired=1"),0);
					if(empty($isAlreadySent)){
						$isSendOtp		=	1;
					}
					else{
						$isSendOtp		=	0;
					}

					if($isSendOtp		==	1){
						include(SITE_ROOT_EMPLOYEES."/includes/sending-otp.php");
					}

					ob_clean();
					header("Location: ".SITE_URL_EMPLOYEES."/index.php?error=6");
					exit();

				}
			}
			else{
				$loginId	=	@mysql_result(dbQuery("SELECT employeeId FROM employee_details WHERE email='$loginEmail'"),0);
			}

			if(empty($loginId))
			{
				$loginId=	0;
				$validator->setError("This email doesnot exist.");
			}
		}

		if(!empty($securityToken) && !empty($loginId) && $displayCaptchaError == 0)
		{
			$employeeType				 =	$employeeObj->isPDFEmployee($loginId);
			$isAllowingOutsideLogin		 =  $employeeObj->isAllowingOutsideLogin($loginId);

			if($employeeType			 ==	1)
			{
				$isPdfEmployee			 =	1;
				$employeeOwnSecurityCode =	$employeeObj->getEmployeeOwnSecurityCode($loginId);

				if($securityToken		!= $employeeOwnSecurityCode)
				{
					$failLoginMessage	=	"Incorrect Security Code Entered";
					$validator->setError("Login ID or Password or Security Token.");

					$employeeObj->trackFailEmployeeLogin($loginId,$loginEmail,$failLoginPassword,$securityToken,$failLoginMessage,$employeeLoginFromIP,$isPdfEmployee,$employeeLoginIpCity,$employeeLoginIpRegion,$employeeLoginIpCountry,$employeeLoginIpISP);

					include(SITE_ROOT_EMPLOYEES."/includes/sending-fail-login.php");

					$attemptedTimes		=	$employeeObj->updateCountFailedlogin($loginId);
					if($attemptedTimes > 3 && $attemptedTimes < 6)
					{
						ob_clean();
						header("Location: ".SITE_URL_EMPLOYEES."/index.php?error=5&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
						exit();
					}
					elseif($attemptedTimes == 6)
					{
						ob_clean();
						header("Location: ".SITE_URL_EMPLOYEES."/index.php?error=5&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
						exit();
					}
					elseif($attemptedTimes > 6)
					{
						include(SITE_ROOT_EMPLOYEES."/includes/sending-lock-account.php");
						
						ob_clean();
						header("Location: ".SITE_URL_EMPLOYEES."/index.php?error=4&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
						exit();
					}
				}
				else
				{
					
					if(!empty($a_officeIPAddress) && empty($isAllowingOutsideLogin))
					{
						if(!in_array($employeeLoginFromIPUptoTwo,$a_officeIPAddressUptoTwo))
						{
							$failLoginMessage	=	"Trying To Login From Outside Office IP";
							$validator->setError("Login ID or Password or Security Token.");

							$employeeObj->trackFailEmployeeLogin($loginId,$loginEmail,$failLoginPassword,$securityToken,$failLoginMessage,$employeeLoginFromIP,$isPdfEmployee,$employeeLoginIpCity,$employeeLoginIpRegion,$employeeLoginIpCountry,$employeeLoginIpISP);

							include(SITE_ROOT_EMPLOYEES."/includes/sending-fail-login.php");
							$failCountCaptcha	= $employeeObj->updateFailedCount($loginId);
							if($failCountCaptcha > 3)
							{
								ob_clean();
								header("Location: ".SITE_URL_EMPLOYEES."/index.php?error=5&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
								exit();
							}
						}
					}
				}
			}
			else
			{
				
				if($securityToken		!=  $employeeSecurityCode)
				{
					$failLoginMessage	=	"Incorrect Security Code Entered";

					$employeeObj->trackFailEmployeeLogin($loginId,$loginEmail,$failLoginPassword,$securityToken,$failLoginMessage,$employeeLoginFromIP,$isPdfEmployee,$employeeLoginIpCity,$employeeLoginIpRegion,$employeeLoginIpCountry,$employeeLoginIpISP);

					include(SITE_ROOT_EMPLOYEES."/includes/sending-fail-login.php");

					$attemptedTimes		=	$employeeObj->updateCountFailedlogin($loginId);
					if($attemptedTimes > 3 && $attemptedTimes < 6)
					{
						ob_clean();
						header("Location: ".SITE_URL_EMPLOYEES."/index.php?error=5&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
						exit();
					}
					elseif($attemptedTimes == 6)
					{
						ob_clean();
						header("Location: ".SITE_URL_EMPLOYEES."/index.php?error=5&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
						exit();
					}
					elseif($attemptedTimes > 6)
					{
						include(SITE_ROOT_EMPLOYEES."/includes/sending-lock-account.php");
						
						ob_clean();
						header("Location: ".SITE_URL_EMPLOYEES."/index.php?error=4&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
						exit();
					}


					$validator->setError("Login ID or Password or Security Token.");
				}
				else
				{
					if(!empty($a_officeIPAddress) && empty($isAllowingOutsideLogin))
					{
						if(!in_array($employeeLoginFromIPUptoTwo,$a_officeIPAddressUptoTwo))
						{
							$failLoginMessage	=	"Trying To Login From Outside Office IP";
							$validator->setError("Login ID or Password or Security Token.");

							$employeeObj->trackFailEmployeeLogin($loginId,$loginEmail,$failLoginPassword,$securityToken,$failLoginMessage,$employeeLoginFromIP,$isPdfEmployee,$employeeLoginIpCity,$employeeLoginIpRegion,$employeeLoginIpCountry,$employeeLoginIpISP);

							include(SITE_ROOT_EMPLOYEES."/includes/sending-fail-login.php");
							
							$failCountCaptcha	= $employeeObj->updateFailedCount($loginId);
							if($failCountCaptcha > 3)
							{
								ob_clean();
								header("Location: ".SITE_URL_EMPLOYEES."/index.php?error=5&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
								exit();
							}							
						}
					}
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
					$password	=	md5($password);
					
					if($result	=   $employeeObj->doEmployeeLoginWithType($loginId,$password,$loginEmail,$loginIpCountry))
					{
						if(is_numeric($result))
						{
							if(isset($_SESSION['isNeededOtp'])){
								
								dbQuery("UPDATE employee_details SET isOtpRequired=0,otpCode='',codeExpireOn='0000-00-00 00:00:00' WHERE employeeId=$loginId");
								unset($_SESSION['isNeededOtp']);
							}

							dbQuery("INSERT INTO employee_login_track SET employeeId=$loginId,loginDate='".CURRENT_DATE_INDIA."',loginTime='".CURRENT_TIME_INDIA."',loginIP='$employeeLoginFromIP'");

							dbQuery("UPDATE employee_details SET lastLoginDate='".CURRENT_DATE_INDIA."',lastLoginTime='".CURRENT_TIME_INDIA."' WHERE employeeId=$loginId");

							$employeeLoginSessionTrackId			   = mysql_insert_id();

							$_SESSION['employeeLoginSessionTrackId']   =	$employeeLoginSessionTrackId;

							if(isset($_SESSION['hasPdfAccess']) && $_SESSION['hasPdfAccess'] == 1)
							{
								if(!in_array($employeeLoginFromIPUptoTwo,$a_officeIPAddressUptoTwo))
								{
									include(SITE_ROOT_EMPLOYEES."/includes/track-outside-office-login.php");
								}
							}

							$lastPasswordChangedOn	=	@mysql_result(dbQuery("SELECT passwordChangeOn FROM employee_details WHERE employeeId=$loginId"),0);

							if($lastPasswordChangedOn	==	"0000-00-00")
							{
								$_SESSION['forceResetPassword']	=	1;

								if(isset($_SESSION['hasPdfAccess']) && $_SESSION['hasPdfAccess'] == 1)
								{
								
									ob_clean();
									header("Location: ".SITE_URL_EMPLOYEES."/change-password.php");
									exit();
								}
								else{
									ob_clean();
									header("Location: ".SITE_URL_MTEMPLOYEES."/change-password.php");
									exit();
								}
							}
							elseif($lastPasswordChangedOn	!=	"0000-00-00")
							{
								$fixedSixtyDaysOldDate		=	getPreviousGivenDate(CURRENT_DATE_INDIA,60);
								if($fixedSixtyDaysOldDate   >   $lastPasswordChangedOn)
								{
									$_SESSION['forceResetPassword']	=	1;
								
									if(isset($_SESSION['hasPdfAccess']) && $_SESSION['hasPdfAccess'] == 1)
									{
									
										ob_clean();
										header("Location: ".SITE_URL_EMPLOYEES."/change-password.php");
										exit();
									}
									else{
										ob_clean();
										header("Location: ".SITE_URL_MTEMPLOYEES."/change-password.php");
										exit();
									}
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
									header("Location: ".SITE_URL_EMPLOYEES."/index.php?error=3&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
									exit();
								}
								else
								{
									ob_clean();
									header("Location: ".SITE_URL_EMPLOYEES."/index.php?error=3");
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
								header("Location: ".SITE_URL_EMPLOYEES."/index.php?error=4&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
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
									header("Location: ".SITE_URL_EMPLOYEES."/index.php?error=2&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
									exit();
								}
								elseif($attemptedTimes > 6)
								{
									include(SITE_ROOT_EMPLOYEES."/includes/sending-lock-account.php");
									
									ob_clean();
									header("Location: ".SITE_URL_EMPLOYEES."/index.php?error=4&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
									exit();
								}
								elseif($attemptedTimes == 6)
								{															
									ob_clean();
									header("Location: ".SITE_URL_EMPLOYEES."/index.php?error=2&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
									exit();
								}
								else
								{
									ob_clean();
									header("Location: ".SITE_URL_EMPLOYEES."/index.php?error=2");
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
							header("Location: ".SITE_URL_EMPLOYEES."/index.php?error=1&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
							exit();
						}
						elseif($attemptedTimes == 6)
						{
							ob_clean();
							header("Location: ".SITE_URL_EMPLOYEES."/index.php?error=1&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
							exit();
						}
						elseif($attemptedTimes > 6)
						{
							include(SITE_ROOT_EMPLOYEES."/includes/sending-lock-account.php");
							
							ob_clean();
							header("Location: ".SITE_URL_EMPLOYEES."/index.php?error=4&".$checkingFailCaptcha."=".$checkingFailCaptchaCode);
							exit();
						}
						else
						{
							ob_clean();
							header("Location: ".SITE_URL_EMPLOYEES."/index.php?error=1");
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