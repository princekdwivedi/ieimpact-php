<?php
	class employee
	{
		//Function to add edit employee
		function addEditEmployee($employeeId,$firstName,$lastName,$fatherName,$gender,$email,$altEmail,$password,$registrationCode,$phone,$mobile,$dob,$city,$state,$country,$address,$perAddress,$identityProofType,$bankName,$branchName,$accountName,$accountNumber,$bankIFSCcode,$panCardNumber)
		{
			global $db_conn;

			$optionQuery	=	" SET firstName='$firstName',lastName='$lastName',fatherName='$fatherName',email='$email',gender='$gender',altEmail='$altEmail',password='$password',phone='$phone',mobile=$mobile,dob='$dob',city='$city',state='$state',country='$country',address='$address',perAddress='$perAddress',identityProofType=$identityProofType,bankName='$bankName',branchName='$branchName',accountName='$accountName',accountNumber='$accountNumber',bankIFSCcode='$bankIFSCcode',panCardNumber='$panCardNumber'";
			if(empty($employeeId))
			{
				$query	=	"INSERT INTO employee_details".$optionQuery.",registrationCode='$registrationCode',addedOn=CURRENT_DATE,ip='".VISITOR_IP_ADDRESS."'";
				dbQuery($query);

				$employeeId	=	mysqli_insert_id($db_conn);
			}
			else
			{
				$query	=	"UPDATE employee_details".$optionQuery." WHERE employeeId=$employeeId";
				dbQuery($query);
			}

			return $employeeId;
		}
		//Function to get employee name
		function getEmployeeName($employeeId)
		{
			$employeeName	=	$this->getSingleQueryResult("SELECT fullName FROM employee_details  WHERE employeeId=$employeeId","fullName");
			if(!empty($employeeName))
			{
				$employeeName	=	stripslashes($employeeName);
				$employeeName	=	ucwords($employeeName);

				return $employeeName;
			}	
			else
			{
				return false;
			}
		}

		//Function to get active employee name
		function getActiveEmployeeName($employeeId)
		{
			$query		=	"SELECT fullName FROM employee_details  WHERE employeeId=$employeeId AND isActive=1";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row			=	mysqli_fetch_assoc($result);
				$fullName		=	stripslashes($row['fullName']);
				
				$fullName		=	ucwords($fullName);

				return $fullName;
			}	
			else
			{
				return false;
			}
		}
		//Function to get employee name
		function getEmployeeFirstName($employeeId)
		{
			$query		=	"SELECT firstName FROM employee_details  WHERE employeeId=$employeeId";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row			=	mysqli_fetch_assoc($result);
				$firstName		=	stripslashes($row['firstName']);

				$firstName	=	ucwords($firstName);

				return $firstName;
			}	
			else
			{
				return false;
			}
		}
		//Function to get existing email
		function getEmployeeEmail($email)
		{
			$query		=	"SELECT employeeId FROM employee_details  WHERE email='$email'";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}	
			else
			{
				return false;
			}
		}
		//Function to get existing email
		function getEmployeeExistingEmail($email,$employeeId=0)
		{
			$andClause		=	"";
			if(!empty($employeeId))
			{
				$andClause	=	" AND employeeId <> $employeeId";
			}
			
			$query		    =	"SELECT employeeId FROM employee_details  WHERE email='$email'".$andClause;
			$result		    =	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}	
			else
			{
				return false;
			}
		}
		//Function to log in an employee
		function doMemberLogin($employeeId,$password,$email)
		{
			$query	=	"SELECT employeeId,firstName,lastName,isActive,isManager,hasPdfAccess,isInBreak,hasAllQaAccess FROM employee_details  WHERE employeeId=$employeeId AND password='$password' AND email='$email' AND isActive=1";
			$result		=	dbQquery($query);
			if(mysqli_num_rows($result))
			{
				$row			=	mysqli_fetch_assoc($result);
				$employeeId		=	$row['employeeId'];
				$lastName		=	stripslashes($row['lastName']);
				$firstName		=	stripslashes($row['firstName']);
				$isActive		=	$row['isActive'];
				$isManager		=	$row['isManager'];
				$hasPdfAccess	=	$row['hasPdfAccess'];
				$isInBreak		=	$row['isInBreak'];
				$hasAllQaAccess	=	$row['hasAllQaAccess'];
				$employeeName	=	$firstName." ".$lastName;
				$employeeName	=	ucwords($employeeName);
				$departmentId 	=	1;
				if($hasPdfAccess== 1){
					$departmentId=	2;
				}

				if($isActive	==	0)
				{
					$error		=	"error";

					return      $error;
				}
				else
				{
					$_SESSION['employeeId']   =	$employeeId;
					$_SESSION['employeeName'] =	$employeeName;
					$_SESSION['employeeEmail'] =	$email;

					if($isManager == 1)
					{
						$_SESSION['hasManagerAccess'] =	$isManager;
					}
					if(!empty($hasPdfAccess))
					{
						$_SESSION['hasPdfAccess'] =	$hasPdfAccess;
					}
					if($departmentId	=	$this->getEmployeeDepartment($employeeId))
					{
						$_SESSION['departmentId'] =	$departmentId;
					}
					if(!empty($hasAllQaAccess))
					{
						$_SESSION['iasHavingAllQaAccess'] =	$hasAllQaAccess;
					}
					if(!empty($isInBreak))
					{
						$breakId		=	$this->getSingleQueryResult("SELECT breakId FROM employee_breaks WHERE employeeId=$employeeId AND breakFinsheddate='0000-00-00' AND breakFinishedTime='00:00:00' AND breakDate <> '0000-00-00' AND breakTime <> '00:00:00' ORDER BY breakId DESC LIMIT 1","breakId");
						if(!empty($breakId))
						{
							$_SESSION['isInBreak']   =	1;
							$_SESSION['breakId']	 =	$breakId;
						}
					}
					
					return $employeeId;
				}

			}
			else
			{
				return false;
			}
		}

		//Function to get all office IP addresses 
		function getAllOfficeIpAddresses()
		{
			$a_officeIPAddress			=	array();
		
			$query						=	"SELECT * FROM office_ip_addresses_list";
			$result						=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row              =  mysqli_fetch_assoc($result)){
					$ipAddress 			=  stripslashes($row['ipAddress']);
					$a_officeIPAddress[]=	$this->getTwoIpDigits($ipAddress);
				}
			}
			
			return $a_officeIPAddress;
		}
		//Function to get upto two marked IP
		function getTwoIpDigits($ip)
		{
			$a_ips		=	explode(".",$ip);

			if(array_key_exists(0,$a_ips)){
				$pos1   =	$a_ips[0];
			}
			else{
				$pos1	=	"";
			}

			if(array_key_exists(1,$a_ips)){
				$pos2	=	$a_ips[1];
			}
			else{
				$pos2	=	"";
			}			
			
			if(!empty($pos1) && !empty($pos2))
			{
				$ip		=	$pos1.".".$pos2;
			}
			return $ip;
		}

		//Function to get all office IP addresses 
		function getAllOfficeIpAddressesUptoTwo()
		{
			$a_officeIPAddress			=	array();
			$query						=	"SELECT * FROM office_ip_addresses_list";
			$result						=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row              =  mysqli_fetch_assoc($result)){
					$ipAddress 			=  stripslashes($row['ipAddress']);
					$a_officeIPAddress[]=	$this->getTwoIpDigits($ipAddress);
				}
			}
			return $a_officeIPAddress;
		}

		//Function to get minitues different in two times
		function minutesBetweenTwoTimes($fromDate,$fromTime,$toDate,$toTime)    
		{
			$fromTimeStamp					=	strtotime($fromDate." ".$fromTime);
			$toTimeStamp					=	strtotime($toDate." ".$toTime);

			$difference						=	$toTimeStamp-$fromTimeStamp;
			$min							=	$difference/60;

			return $min;
		}
		//Function to update failed login counts show captcha
		function updateFailedCount($employeeId)
		{
			dbQuery("UPDATE employee_details SET countFailLogin=countFailLogin+1 WHERE employeeId=$employeeId");

			$totalFailedAttempts	=	$this->getSingleQueryResult("SELECT countFailLogin FROM employee_details WHERE employeeId=$employeeId","countFailLogin");

			return $totalFailedAttempts;
		}
		//Function update count fail login
		function updateCountFailedlogin($employeeId)
		{
			$attemptsTime				=   0;
			$query						=	"SELECT employeeId,isLocked,triedFailCount,lockedDate,lockedTime,lockedFromIP FROM employee_details  WHERE employeeId=$employeeId";
			$result						=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row					=	mysqli_fetch_assoc($result);
				$employeeId				=	$row['employeeId'];
				$isLocked				=	$row['isLocked'];
				$triedFailCount			=	$row['triedFailCount'];
				$lockedDate				=	$row['lockedDate'];
				$lockedTime				=	$row['lockedTime'];
				$lockedFromIP			=	$row['lockedFromIP'];
				$attemptsTime			=	$triedFailCount;

				if($isLocked			==	0)
				{	
					if(!empty($triedFailCount))
					{
						$diffMin			  =  $this->minutesBetweenTwoTimes($lockedDate,$lockedTime,CURRENT_DATE_INDIA,CURRENT_TIME_INDIA);
						if($diffMin > 15)
						{
							$attemptsTime	=	1;
							dbQuery("UPDATE employee_details SET triedFailCount=1,lockedDate='".CURRENT_DATE_INDIA."',lockedTime='".CURRENT_TIME_INDIA."',lockedFromIP='".VISITOR_IP_ADDRESS."',countFailLogin=1 WHERE employeeId=$employeeId AND isLocked=0");
						}
						else
						{
							$attemptsTime	=	$attemptsTime+1;
							if($attemptsTime > 6)
							{
								dbQuery("UPDATE employee_details SET isLocked=1,triedFailCount=$attemptsTime,lockedDate='".CURRENT_DATE_INDIA."',lockedTime='".CURRENT_TIME_INDIA."',lockedFromIP='".VISITOR_IP_ADDRESS."',countFailLogin=countFailLogin+1 WHERE employeeId=$employeeId AND isLocked=0");
							}
							else
							{
								dbQuery("UPDATE employee_details SET triedFailCount=triedFailCount+1,countFailLogin=countFailLogin+1 WHERE employeeId=$employeeId AND isLocked=0");
							}
						}
					}
					else
					{
						$attemptsTime	=	1;
						dbQuery("UPDATE employee_details SET triedFailCount=1,lockedDate='".CURRENT_DATE_INDIA."',lockedTime='".CURRENT_TIME_INDIA."',lockedFromIP='".VISITOR_IP_ADDRESS."',countFailLogin=countFailLogin+1 WHERE employeeId=$employeeId AND isLocked=0");
					}
				}
			}
			$attemptsTime = 0;
			return $attemptsTime;
		}

		//Function to log in an employee with IP
		function doEmployeeLoginWithIp($employeeId,$password,$email,$loginIpCountry)
		{
			$query	=	"SELECT employeeId,firstName,lastName,isActive,isManager,hasPdfAccess,isInBreak,isOutsideCountryEmployee,shiftType,hasAllQaAccess,isLocked,triedFailCount,lockedDate,lockedTime,lockedFromIP,hasverificationAccess FROM employee_details  WHERE employeeId=$employeeId AND password='$password' AND email='$email'";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row					  =	mysqli_fetch_assoc($result);
				$employeeId				  =	$row['employeeId'];
				$lastName				  =	stripslashes($row['lastName']);
				$firstName				  =	stripslashes($row['firstName']);
				$isActive				  =	$row['isActive'];
				$isManager				  =	$row['isManager'];
				$hasPdfAccess			  =	$row['hasPdfAccess'];
				$isOutsideCountryEmployee =	$row['isOutsideCountryEmployee'];
				$isInBreak				  =	$row['isInBreak'];
				$shiftType				  =	$row['shiftType'];
				$hasAllQaAccess			  =	$row['hasAllQaAccess'];
				$isLocked				  =	$row['isLocked'];
				$triedFailCount			  =	$row['triedFailCount'];
				$lockedDate				  =	$row['lockedDate'];
				$lockedTime				  =	$row['lockedTime'];
				$lockedFromIP			  =	$row['lockedFromIP'];
				$hasverificationAccess	  =	$row['hasverificationAccess'];

				$employeeName			  =	$firstName." ".$lastName;
				$employeeName			  =	ucwords($employeeName);

				$isLockedAccount		  =	0;

				if($isLocked			  == 1)
				{
					$diffMin			  =  $this->minutesBetweenTwoTimes($lockedDate,$lockedTime,CURRENT_DATE_INDIA,CURRENT_TIME_INDIA);
					if($diffMin > 60)
					{
						dbQuery("UPDATE employee_details SET isLocked=0,triedFailCount=0,lockedDate='0000-00-00',lockedTime='00:00:00',lockedFromIP='',countFailLogin=0 WHERE employeeId=$employeeId AND isLocked=1");
					}
					else
					{
						$isLockedAccount  =	1;
					}
				}

				if($isLockedAccount		==	1)
				{
					$error	=	"errorFailLogin";

					return $error;
				}
				else
				{

					if($isActive	==	0)
					{
						$this->updateCountFailedlogin($employeeId);
						$error		=	"error";

						return      $error;
					}
					else
					{
						if($hasPdfAccess	==	1 && strstr($_SERVER['HTTP_HOST'],'ieimpact.com'))
						{
							$a_allOfficeAddress	=	$this->getAllOfficeIpAddressesUptoTwo();
							$employeeLoginFromIP=	VISITOR_IP_ADDRESS;
							$employeeLoginFromIP=	$this->getTwoIpDigits($employeeLoginFromIP);

							if($loginIpCountry == "India" || $isOutsideCountryEmployee == 1 || in_array($employeeLoginFromIP,$a_allOfficeAddress))
							{
								$_SESSION['employeeId']   =	$employeeId;
								$_SESSION['employeeName'] =	$employeeName;
								$_SESSION['employeeEmail'] =	$email;

								if($shiftType	==	2)
								{
									$_SESSION['isNightShiftEmployee']	=	1;
								}
								if($isManager	== 1)
								{
									$_SESSION['hasManagerAccess'] =	$isManager;
								}
								if(!empty($hasPdfAccess))
								{
									$_SESSION['hasPdfAccess'] =	$hasPdfAccess;
								}
								if(!empty($hasAllQaAccess))
								{
									$_SESSION['iasHavingAllQaAccess'] =	$hasAllQaAccess;
								}
								if(!empty($hasverificationAccess))
								{
									$_SESSION['isHavingVerifyAccess'] =	$hasverificationAccess;
								}
								if($departmentId	=	$this->getEmployeeDepartment($employeeId))
								{
									$_SESSION['departmentId'] =	$departmentId;
								}
								if(!empty($isInBreak))
								{
									$breakId		=	$this->getSingleQueryResult("SELECT breakId FROM employee_breaks WHERE employeeId=$employeeId AND breakFinsheddate='0000-00-00' AND breakFinishedTime='00:00:00' AND breakDate <> '0000-00-00' AND breakTime <> '00:00:00' ORDER BY breakId DESC LIMIT 1","breakId");
									if(!empty($breakId))
									{
										$_SESSION['isInBreak']   =	1;
										$_SESSION['breakId']	 =	$breakId;
									}
								}
								dbQuery("UPDATE employee_details SET isLocked=0,triedFailCount=0,lockedDate='0000-00-00',lockedTime='00:00:00',lockedFromIP='',countFailLogin=0 WHERE employeeId=$employeeId");
							}
							else
							{
								$error1		=	"iperror";
	
								return      $error1;
							}
						}
						else
						{
						
							$_SESSION['employeeId']    =	$employeeId;
							$_SESSION['employeeName']  =	$employeeName;
							$_SESSION['employeeEmail'] =	$email;
							if($shiftType	==	2)
							{
								$_SESSION['isNightShiftEmployee']	=	1;
							}
							if($isManager == 1)
							{
								$_SESSION['hasManagerAccess'] =	$isManager;
							}
							if(!empty($hasPdfAccess))
							{
								$_SESSION['hasPdfAccess'] =	$hasPdfAccess;
							}
							if(!empty($hasAllQaAccess))
							{
								$_SESSION['iasHavingAllQaAccess'] =	$hasAllQaAccess;
							}
							if(!empty($hasverificationAccess))
							{
								$_SESSION['isHavingVerifyAccess'] =	$hasverificationAccess;
							}
							if($departmentId	=	$this->getEmployeeDepartment($employeeId))
							{
								$_SESSION['departmentId'] =	$departmentId;
							}
							if(!empty($isInBreak))
							{
								$breakId		=	$this->getSingleQueryResult("SELECT breakId FROM employee_breaks WHERE employeeId=$employeeId AND breakFinsheddate='0000-00-00' AND breakFinishedTime='00:00:00' AND breakDate <> '0000-00-00' AND breakTime <> '00:00:00' ORDER BY breakId DESC LIMIT 1","breakId");
								if(!empty($breakId))
								{
									$_SESSION['isInBreak']   =	1;
									$_SESSION['breakId']	 =	$breakId;
								}
							}
							dbQuery("UPDATE employee_details SET isLocked=0,triedFailCount=0,lockedDate='0000-00-00',lockedTime='00:00:00',lockedFromIP='',countFailLogin=0 WHERE employeeId=$employeeId");
						}
						
						return $employeeId;
					}
				}

			}
			else
			{
				return false;
			}
		}

		//Function to log in an MT employee with IP
		function MTEmployeeLoginWithNewPassword($employeeId,$password,$email,$loginIpCountry)
		{

			$query	    =	"SELECT employeeId,firstName,lastName,isActive,isManager,hasPdfAccess,isOutsideCountryEmployee,shiftType,hasAllQaAccess,isLocked,triedFailCount,lockedDate,lockedTime,lockedFromIP,hasverificationAccess,showQuestionnaire,password FROM employee_details  WHERE employeeId=$employeeId AND email='$email' AND hasPdfAccess=0";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row					  =	mysqli_fetch_assoc($result);
				$employeeId				  =	$row['employeeId'];
				$lastName				  =	stripslashes($row['lastName']);
				$firstName				  =	stripslashes($row['firstName']);
				$isActive				  =	$row['isActive'];
				$dbPassword				  =	$row['password'];
				$isManager				  =	$row['isManager'];
				$hasPdfAccess			  =	$row['hasPdfAccess'];
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

				if(password_verify($password, $dbPassword)){

					$employeeName			  =	$firstName." ".$lastName;
					$employeeName			  =	ucwords($employeeName);
					$departmentId 			  = 1;
					if($hasPdfAccess == 1){
						$departmentId 	      = 2;
					}

					$isLockedAccount		  =	0;

					if($isLocked			  == 1)
					{
						$diffMin			  =  $this->minutesBetweenTwoTimes($lockedDate,$lockedTime,CURRENT_DATE_INDIA,CURRENT_TIME_INDIA);
						if($diffMin > 60)
						{
							dbQuery("UPDATE employee_details SET isLocked=0,triedFailCount=0,lockedDate='0000-00-00',lockedTime='00:00:00',lockedFromIP='',countFailLogin=0 WHERE employeeId=$employeeId AND isLocked=1");
						}
						else
						{
							$isLockedAccount  =	1;
						}
					}

					if($isLockedAccount		==	1)
					{
						$error	=	"errorFailLogin";

						return $error;
					}
					else
					{

						if($isActive	==	0)
						{
							$this->updateCountFailedlogin($employeeId);
							$error		=	"error";

							return      $error;
						}
						else
						{							
							$_SESSION['employeeId']        =	$employeeId;
							$_SESSION['employeeName']      =	$employeeName;
							$_SESSION['employeeEmail']     =	$email;
							$_SESSION['showQuestionnaire'] =	$showQuestionnaire;
							if($shiftType	==	2)
							{
								$_SESSION['isNightShiftEmployee']	=	1;
							}
							if($isManager == 1)
							{
								$_SESSION['hasManagerAccess'] =	$isManager;
							}
							if(!empty($hasPdfAccess))
							{
								$_SESSION['hasPdfAccess'] =	$hasPdfAccess;
							}
							if(!empty($hasAllQaAccess))
							{
								$_SESSION['iasHavingAllQaAccess'] =	$hasAllQaAccess;
							}
							if(!empty($hasverificationAccess))
							{
								$_SESSION['isHavingVerifyAccess'] =	$hasverificationAccess;
							}
							if($departmentId	=	$this->getEmployeeDepartment($employeeId))
							{
								$_SESSION['departmentId'] =	$departmentId;
							}
							
							if($departmentId	==	1)
							{
								$_SESSION['mtemployeeId']			=	$_SESSION['employeeId'];
								$_SESSION['mtemployeeName']			=	$_SESSION['employeeName'];
								//$_SESSION['mthasManagerAccess']	=   1;
								$_SESSION['hasMtAccess']			=	1;
					
								if($isManager == 1)
								{
									//$_SESSION['mthasHrAccess']		=	$isManager;
									$_SESSION['mthasManagerAccess']	=   1;
								}
							}

							dbQuery("UPDATE employee_details SET isLocked=0,triedFailCount=0,lockedDate='0000-00-00',lockedTime='00:00:00',lockedFromIP='',countFailLogin=0 WHERE employeeId=$employeeId");
							
							
							return $employeeId;
						}
					}
				}
				else{
					return false;
				}
			}
			else
			{
				return false;
			}
		}

		//Function to log in an PDF employee with IP
		function PDFEmployeeLoginWithNewPassword($employeeId,$password,$email,$loginIpCountry)
		{

			$query	    =	"SELECT employeeId,fullName,isActive,isManager,hasPdfAccess,isOutsideCountryEmployee,shiftType,hasAllQaAccess,isLocked,triedFailCount,lockedDate,lockedTime,lockedFromIP,hasverificationAccess,showQuestionnaire,password,facebookId FROM employee_details  WHERE employeeId=$employeeId AND email='$email' AND hasPdfAccess=1";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row					  =	mysqli_fetch_assoc($result);
				$employeeId				  =	$row['employeeId'];
				$fullName				  =	stripslashes($row['fullName']);
				$isActive				  =	$row['isActive'];
				$dbPassword				  =	$row['password'];
				$isManager				  =	$row['isManager'];
				$hasPdfAccess			  =	$row['hasPdfAccess'];
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
				$facebookId		          = $row['facebookId'];
				
				if(password_verify($password, $dbPassword)){

					$employeeName			  =	ucwords($fullName);
					$departmentId 			  = 1;
					if($hasPdfAccess == 1){
						$departmentId 	      = 2;
					}

					$isLockedAccount		  =	0;

					if($isLocked			  == 1)
					{
						$diffMin			  =  $this->minutesBetweenTwoTimes($lockedDate,$lockedTime,CURRENT_DATE_INDIA,CURRENT_TIME_INDIA);
						if($diffMin > 60)
						{
							dbQuery("UPDATE employee_details SET isLocked=0,triedFailCount=0,lockedDate='0000-00-00',lockedTime='00:00:00',lockedFromIP='',countFailLogin=0 WHERE employeeId=$employeeId AND isLocked=1");
						}
						else
						{
							$isLockedAccount  =	1;
						}
					}

					if($isLockedAccount		==	1)
					{
						$error	=	"errorFailLogin";

						return $error;
					}
					else
					{

						if($isActive	==	0)
						{
							$this->updateCountFailedlogin($employeeId);
							$error		=	"error";

							return      $error;
						}
						else
						{
							if($hasPdfAccess	==	1 && strstr($_SERVER['HTTP_HOST'],'ieimpact.com'))
							{
								$a_allOfficeAddress	=	$this->getAllOfficeIpAddressesUptoTwo();
								$employeeLoginFromIP=	VISITOR_IP_ADDRESS;
								$employeeLoginFromIP=	$this->getTwoIpDigits($employeeLoginFromIP);

								//if($loginIpCountry == "India" || $isOutsideCountryEmployee == 1 || in_array($employeeLoginFromIP,$a_allOfficeAddress))// $loginIpCountry == "India" was removed on 21st Jan,2021 due to failure of https://tools.keycdn.com/  IP tracker API
								
								if(strtolower($loginIpCountry) == "india" ||$isOutsideCountryEmployee == 1 || in_array($employeeLoginFromIP,$a_allOfficeAddress))
								{

									$_SESSION['SUCCESS_VERIFIED_PDF_EMPLOYEE_ID'] = $employeeId;
									
									dbQuery("UPDATE employee_details SET isLocked=0,triedFailCount=0,lockedDate='0000-00-00',lockedTime='00:00:00',lockedFromIP='',countFailLogin=0 WHERE employeeId=$employeeId");
								}
								else
								{
									$error1		=	"iperror";
		
									return      $error1;
								}
							}
							else
							{
								$_SESSION['SUCCESS_VERIFIED_PDF_EMPLOYEE_ID'] = $employeeId;

								dbQuery("UPDATE employee_details SET isLocked=0,triedFailCount=0,lockedDate='0000-00-00',lockedTime='00:00:00',lockedFromIP='',countFailLogin=0 WHERE employeeId=$employeeId");
							}
							
							return $employeeId;
						}
					}
				}
				else{
					return false;
				}
			}
			else
			{
				return false;
			}
		}

		//Function to log in an employee with IP
		function checkEmployeeLogin($employeeId,$password,$email)
		{

			$query	    =	"SELECT employeeId,firstName,lastName,isActive,isManager,hasPdfAccess,isOutsideCountryEmployee,shiftType,hasAllQaAccess,isLocked,triedFailCount,lockedDate,lockedTime,lockedFromIP,hasverificationAccess,showQuestionnaire,password FROM employee_details  WHERE employeeId=$employeeId AND email='$email' and isActive=1";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row					  =	mysqli_fetch_assoc($result);
				$employeeId				  =	$row['employeeId'];
				$lastName				  =	stripslashes($row['lastName']);
				$firstName				  =	stripslashes($row['firstName']);
				$isActive				  =	$row['isActive'];
				$dbPassword				  =	$row['password'];
				$isManager				  =	$row['isManager'];
				$hasPdfAccess			  =	$row['hasPdfAccess'];
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


				if(password_verify($password, $dbPassword)){

					$employeeName			  =	$firstName." ".$lastName;
					$employeeName			  =	ucwords($employeeName);
					$departmentId 			  = 1;
					
					return $employeeId;
				}
				else{
					return false;
				}
			}
			else
			{
				return false;
			}
		}

		//Function to log in an employee with IP
		function employeeLoginWithNewPassword($employeeId,$password,$email,$loginIpCountry)
		{

			$query	    =	"SELECT employeeId,firstName,lastName,isActive,isManager,hasPdfAccess,isOutsideCountryEmployee,shiftType,hasAllQaAccess,isLocked,triedFailCount,lockedDate,lockedTime,lockedFromIP,hasverificationAccess,showQuestionnaire,password FROM employee_details  WHERE employeeId=$employeeId AND email='$email'";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row					  =	mysqli_fetch_assoc($result);
				$employeeId				  =	$row['employeeId'];
				$lastName				  =	stripslashes($row['lastName']);
				$firstName				  =	stripslashes($row['firstName']);
				$isActive				  =	$row['isActive'];
				$dbPassword				  =	$row['password'];
				$isManager				  =	$row['isManager'];
				$hasPdfAccess			  =	$row['hasPdfAccess'];
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

				/**** THIS CODE TO USE CHECKING NEW PASSWORD SYSTEM. NOT IN USE NOW
	
				$options                  = array('cost' => 12);

				$isHavingUpdatedPassword      =  0;
				if(empty($dbPassword) && !empty($oldPassword) && $oldPassword == md5($password)){
					$isHavingUpdatedPassword  =  1;			     

					$newPassword 		      =	password_hash($password, PASSWORD_BCRYPT, $options);

					dbQuery("UPDATE employee_details SET password='$newPassword',oldPassword='' WHERE employeeId=$employeeId");
				}
				elseif(!empty($dbPassword) && empty($oldPassword)){
					
					if(password_verify($password, $dbPassword)){
						$isHavingUpdatedPassword =  2;
					}
				}*/

				if(password_verify($password, $dbPassword)){

					$employeeName			  =	$firstName." ".$lastName;
					$employeeName			  =	ucwords($employeeName);
					$departmentId 			  = 1;
					if($hasPdfAccess == 1){
						$departmentId 	      = 2;
					}

					$isLockedAccount		  =	0;

					if($isLocked			  == 1)
					{
						$diffMin			  =  $this->minutesBetweenTwoTimes($lockedDate,$lockedTime,CURRENT_DATE_INDIA,CURRENT_TIME_INDIA);
						if($diffMin > 60)
						{
							dbQuery("UPDATE employee_details SET isLocked=0,triedFailCount=0,lockedDate='0000-00-00',lockedTime='00:00:00',lockedFromIP='',countFailLogin=0 WHERE employeeId=$employeeId AND isLocked=1");
						}
						else
						{
							$isLockedAccount  =	1;
						}
					}

					if($isLockedAccount		==	1)
					{
						$error	=	"errorFailLogin";

						return $error;
					}
					else
					{

						if($isActive	==	0)
						{
							$this->updateCountFailedlogin($employeeId);
							$error		=	"error";

							return      $error;
						}
						else
						{
							if($hasPdfAccess	==	1)
							{
								$a_allOfficeAddress	=	$this->getAllOfficeIpAddressesUptoTwo();
								$employeeLoginFromIP=	VISITOR_IP_ADDRESS;
								$employeeLoginFromIP=	$this->getTwoIpDigits($employeeLoginFromIP);

								$_SESSION['employeeId']   =	$employeeId;
								$_SESSION['employeeName'] =	$employeeName;
								$_SESSION['employeeEmail'] =	$email;
								if($shiftType	==	2)
								{
									$_SESSION['isNightShiftEmployee']	=	1;
								}
								if($isManager	== 1)
								{
									$_SESSION['hasManagerAccess'] =	$isManager;
								}
								if(!empty($hasPdfAccess))
								{
									$_SESSION['hasPdfAccess'] =	$hasPdfAccess;
								}
								if(!empty($hasAllQaAccess))
								{
									$_SESSION['iasHavingAllQaAccess'] =	$hasAllQaAccess;
								}
								if(!empty($hasverificationAccess))
								{
									$_SESSION['isHavingVerifyAccess'] =	$hasverificationAccess;
								}
								$_SESSION['departmentId'] =	$departmentId;
								$_SESSION['showQuestionnaire'] =	$showQuestionnaire;
								
								dbQuery("UPDATE employee_details SET isLocked=0,triedFailCount=0,lockedDate='0000-00-00',lockedTime='00:00:00',lockedFromIP='',countFailLogin=0 WHERE employeeId=$employeeId");
						}
							else
							{
							
								$_SESSION['employeeId']        =	$employeeId;
								$_SESSION['employeeName']      =	$employeeName;
								$_SESSION['employeeEmail']     =	$email;
								$_SESSION['showQuestionnaire'] =	$showQuestionnaire;
								if($shiftType	==	2)
								{
									$_SESSION['isNightShiftEmployee']	=	1;
								}
								if($isManager == 1)
								{
									$_SESSION['hasManagerAccess'] =	$isManager;
								}
								if(!empty($hasPdfAccess))
								{
									$_SESSION['hasPdfAccess'] =	$hasPdfAccess;
								}
								if(!empty($hasAllQaAccess))
								{
									$_SESSION['iasHavingAllQaAccess'] =	$hasAllQaAccess;
								}
								if(!empty($hasverificationAccess))
								{
									$_SESSION['isHavingVerifyAccess'] =	$hasverificationAccess;
								}
								if($departmentId	=	$this->getEmployeeDepartment($employeeId))
								{
									$_SESSION['departmentId'] =	$departmentId;
								}
								
								if($departmentId	==	1)
								{
									$_SESSION['mtemployeeId']			=	$_SESSION['employeeId'];
									$_SESSION['mtemployeeName']			=	$_SESSION['employeeName'];
									//$_SESSION['mthasManagerAccess']	=   1;
									$_SESSION['hasMtAccess']			=	1;
						
									if($isManager == 1)
									{
										//$_SESSION['mthasHrAccess']		=	$isManager;
										$_SESSION['mthasManagerAccess']	=   1;
									}
								}								
								dbQuery("UPDATE employee_details SET isLocked=0,triedFailCount=0,lockedDate='0000-00-00',lockedTime='00:00:00',lockedFromIP='',countFailLogin=0 WHERE employeeId=$employeeId");
							}
							
							return $employeeId;
						}
					}
				}
				else{
					return false;
				}
			}
			else
			{
				return false;
			}
		}

		//Function to log in an employee with IP
		function doEmployeeLoginWithType($employeeId,$password,$email,$loginIpCountry)
		{
			$query	    =	"SELECT employeeId,firstName,lastName,isActive,isManager,hasPdfAccess,isInBreak,isOutsideCountryEmployee,shiftType,hasAllQaAccess,isLocked,triedFailCount,lockedDate,lockedTime,lockedFromIP,hasverificationAccess,showQuestionnaire FROM employee_details  WHERE employeeId=$employeeId AND password='$password' AND email='$email'";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row					  =	mysqli_fetch_assoc($result);
				$employeeId				  =	$row['employeeId'];
				$lastName				  =	stripslashes($row['lastName']);
				$firstName				  =	stripslashes($row['firstName']);
				$isActive				  =	$row['isActive'];
				$isManager				  =	$row['isManager'];
				$hasPdfAccess			  =	$row['hasPdfAccess'];
				$isOutsideCountryEmployee =	$row['isOutsideCountryEmployee'];
				$isInBreak				  =	$row['isInBreak'];
				$shiftType				  =	$row['shiftType'];
				$hasAllQaAccess			  =	$row['hasAllQaAccess'];
				$isLocked				  =	$row['isLocked'];
				$triedFailCount			  =	$row['triedFailCount'];
				$lockedDate				  =	$row['lockedDate'];
				$lockedTime				  =	$row['lockedTime'];
				$lockedFromIP			  =	$row['lockedFromIP'];
				$hasverificationAccess	  =	$row['hasverificationAccess'];
				$showQuestionnaire		  = $row['showQuestionnaire'];

				$employeeName			  =	$firstName." ".$lastName;
				$employeeName			  =	ucwords($employeeName);
				$departmentId 			  = 1;
				if($hasPdfAccess == 1){
					$departmentId 	      = 2;
				}

				$isLockedAccount		  =	0;

				if($isLocked			  == 1)
				{
					$diffMin			  =  $this->minutesBetweenTwoTimes($lockedDate,$lockedTime,CURRENT_DATE_INDIA,CURRENT_TIME_INDIA);
					if($diffMin > 60)
					{
						dbQuery("UPDATE employee_details SET isLocked=0,triedFailCount=0,lockedDate='0000-00-00',lockedTime='00:00:00',lockedFromIP='',countFailLogin=0 WHERE employeeId=$employeeId AND isLocked=1");
					}
					else
					{
						$isLockedAccount  =	1;
					}
				}

				if($isLockedAccount		==	1)
				{
					$error	=	"errorFailLogin";

					return $error;
				}
				else
				{

					if($isActive	==	0)
					{
						$this->updateCountFailedlogin($employeeId);
						$error		=	"error";

						return      $error;
					}
					else
					{
						if($hasPdfAccess	==	1 && strstr($_SERVER['HTTP_HOST'],'ieimpact.com'))
						{
							$a_allOfficeAddress	=	$this->getAllOfficeIpAddressesUptoTwo();
							$employeeLoginFromIP=	VISITOR_IP_ADDRESS;
							$employeeLoginFromIP=	$this->getTwoIpDigits($employeeLoginFromIP);

							//if($loginIpCountry == "India" || $isOutsideCountryEmployee == 1 || in_array($employeeLoginFromIP,$a_allOfficeAddress))// $loginIpCountry == "India" was removed on 21st Jan,2021 due to failure of https://tools.keycdn.com/  IP tracker API
							
							if(strtolower($loginIpCountry) == "india" ||$isOutsideCountryEmployee == 1 || in_array($employeeLoginFromIP,$a_allOfficeAddress))
							{
								$_SESSION['employeeId']   =	$employeeId;
								$_SESSION['employeeName'] =	$employeeName;
								$_SESSION['employeeEmail'] =	$email;
								if($shiftType	==	2)
								{
									$_SESSION['isNightShiftEmployee']	=	1;
								}
								if($isManager	== 1)
								{
									$_SESSION['hasManagerAccess'] =	$isManager;
								}
								if(!empty($hasPdfAccess))
								{
									$_SESSION['hasPdfAccess'] =	$hasPdfAccess;
								}
								if(!empty($hasAllQaAccess))
								{
									$_SESSION['iasHavingAllQaAccess'] =	$hasAllQaAccess;
								}
								if(!empty($hasverificationAccess))
								{
									$_SESSION['isHavingVerifyAccess'] =	$hasverificationAccess;
								}
								$_SESSION['departmentId'] =	$departmentId;
								$_SESSION['showQuestionnaire'] =	$showQuestionnaire;
								
								dbQuery("UPDATE employee_details SET isLocked=0,triedFailCount=0,lockedDate='0000-00-00',lockedTime='00:00:00',lockedFromIP='',countFailLogin=0 WHERE employeeId=$employeeId");
							}
							else
							{
								$error1		=	"iperror";
	
								return      $error1;
							}
						}
						else
						{
						
							$_SESSION['employeeId']        =	$employeeId;
							$_SESSION['employeeName']      =	$employeeName;
							$_SESSION['employeeEmail']     =	$email;
							$_SESSION['showQuestionnaire'] =	$showQuestionnaire;
							if($shiftType	==	2)
							{
								$_SESSION['isNightShiftEmployee']	=	1;
							}
							if($isManager == 1)
							{
								$_SESSION['hasManagerAccess'] =	$isManager;
							}
							if(!empty($hasPdfAccess))
							{
								$_SESSION['hasPdfAccess'] =	$hasPdfAccess;
							}
							if(!empty($hasAllQaAccess))
							{
								$_SESSION['iasHavingAllQaAccess'] =	$hasAllQaAccess;
							}
							if(!empty($hasverificationAccess))
							{
								$_SESSION['isHavingVerifyAccess'] =	$hasverificationAccess;
							}
							if($departmentId	=	$this->getEmployeeDepartment($employeeId))
							{
								$_SESSION['departmentId'] =	$departmentId;
							}
							
							if($departmentId	==	1)
							{
								$_SESSION['mtemployeeId']			=	$_SESSION['employeeId'];
								$_SESSION['mtemployeeName']			=	$_SESSION['employeeName'];
								//$_SESSION['mthasManagerAccess']	=   1;
								$_SESSION['hasMtAccess']			=	1;
					
								if($isManager == 1)
								{
									//$_SESSION['mthasHrAccess']		=	$isManager;
									$_SESSION['mthasManagerAccess']	=   1;
								}
							}

							if(!empty($isInBreak))
							{
								$breakId		=	$this->getSingleQueryResult("SELECT breakId FROM employee_breaks WHERE employeeId=$employeeId AND breakFinsheddate='0000-00-00' AND breakFinishedTime='00:00:00' AND breakDate <> '0000-00-00' AND breakTime <> '00:00:00' ORDER BY breakId DESC LIMIT 1","breakId");
								if(!empty($breakId))
								{
									$_SESSION['isInBreak']   =	1;
									$_SESSION['breakId']	 =	$breakId;
								}
							}
							dbQuery("UPDATE employee_details SET isLocked=0,triedFailCount=0,lockedDate='0000-00-00',lockedTime='00:00:00',lockedFromIP='',countFailLogin=0 WHERE employeeId=$employeeId");
						}
						
						return $employeeId;
					}
				}

			}
			else
			{
				return false;
			}
		}

		//Function to add edit employee works
		function addEditWorks($workId,$employeeId,$platform,$customerId,$transcriptionLinesEntered,$indirectTranscriptionLinesEntered,$vreLinesEntered,$indirectVreLinesEntered,$qaLinesEntered,$indirectQaLinesEntered,$propertiesLinesEntered,$indirectpropertiesLinesEntered,$workedOn,$comments,$transcriptionUserId,$vreUserId,$qaUserId,$propertiesUserId)
		{
			$optionQuery	=	" SET employeeId=$employeeId,platform=$platform,customerId=$customerId,transcriptionLinesEntered=$transcriptionLinesEntered,vreLinesEntered=$vreLinesEntered,qaLinesEntered=$qaLinesEntered,workedOn='$workedOn',indirectTranscriptionLinesEntered=$indirectTranscriptionLinesEntered,indirectVreLinesEntered=$indirectVreLinesEntered,indirectQaLinesEntered=$indirectQaLinesEntered,propertiesLinesEntered=$propertiesLinesEntered,indirectpropertiesLinesEntered=$indirectpropertiesLinesEntered,comments='$comments',transcriptionUserId='$transcriptionUserId',vreUserId='$vreUserId',qaUserId='$qaUserId',propertiesUserId='$propertiesUserId'";

			if(empty($workId))
			{
				$query	=	"INSERT INTO employee_works".$optionQuery.",addedOn=CURRENT_DATE,addedTime=CURRENT_TIME,ip='".VISITOR_IP_ADDRESS."'";
				dbQuery($query);
			}
			else
			{
				$query	=	"UPDATE employee_works".$optionQuery.",ip='".VISITOR_IP_ADDRESS."' WHERE workId=$workId AND employeeId=$employeeId";
				dbQquery($query);
			}
		}
		//Function to get employee email
		function getForgotPasswordEmail($email,$employeeId)
		{
			$query	=	"SELECT employeeId,firstName,lastName,email,password,fullName FROM employee_details  WHERE (email='$email' OR employeeId=$employeeId) AND isActive=1";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//Function to get already assigned client id
		function getEmployeeClientAssignedId($platform,$employeeId,$rateId)
		{
			$query	=	"SELECT assignedId FROM employee_clients WHERE platform=$platform AND employeeId=$employeeId AND rateId=$rateId";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row		= mysqli_fetch_assoc($result);
				$assignedId	=	$row['assignedId'];

				return $assignedId;
			}
			else
			{
				return false;
			}
		}
		//Function to get already assigned client ids of a paltform
		function getEmployeeClients($platform,$employeeId,$rateId)
		{
			$query	=	"SELECT clientId FROM employee_clients WHERE platform=$platform AND employeeId=$employeeId AND rateId=$rateId";
			$result	=	dnQuery($query);
			if(mysqli_num_rows($result))
			{
				$row		= mysqli_fetch_assoc($result);
				$clientId	=	$row['clientId'];

				return $clientId;
			}
			else
			{
				return false;
			}
		}

		//Function to get all active employees
		function getAllEmployees()
		{
			$query	=	"SELECT employeeId,firstName,lastName FROM employee_details  WHERE isActive=1 ORDER BY firstName";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//Function to get employee deoartment
		function getEmployeeDepartment($employeeId)
		{
			$query	=	"SELECT departmentId FROM employee_shift_rates WHERE employeeId=$employeeId";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row			=	mysqli_fetch_assoc($result);
				$departmentId	=	$row['departmentId'];

				return $departmentId;
			}
			else
			{
				return false;
			}
		}
		//Get all rev employees
		function getAllRevEmployees()
		{
			$query	=	"SELECT employee_shift_rates.employeeId,firstName,lastName FROM employee_shift_rates INNER JOIN employee_details ON employee_shift_rates.employeeId=employee_details.employeeId WHERE departmentId=2 AND employee_details.isActive=1 ORDER BY firstName";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//Get all mt employees
		function getAllMtEmployees()
		{
			$query	=	"SELECT employee_shift_rates.employeeId,firstName,lastName,email FROM employee_shift_rates INNER JOIN employee_details ON employee_shift_rates.employeeId=employee_details.employeeId WHERE departmentId=1 AND employee_details.isActive=1 ORDER BY firstName";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//Functiont to get assigned work details
		function getAssignedWorkDetails($employeeId,$assignedWorkId)
		{
			$query	=	"SELECT * FROM assign_employee_works WHERE employeeId=$employeeId AND assignedWorkId=$assignedWorkId";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//Function to get all assigned new works
		function getUnAccepetedWorks($employeeId)
		{
			$query	=	"SELECT COUNT(assignedWorkId) as Total FROM assign_employee_works WHERE employeeId=$employeeId AND status=0 ";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row		=	mysqli_fetch_assoc($result);
				$total		=	$row['Total'];

				return $total;
			}
			else
			{
				return false;
			}
		}
		//Function to get existing paltform
		function getExistingPlatform($name,$departmentId)
		{
			$query	=	"SELECT platfromId FROM platform_clients WHERE name='$name' AND departmentId=$departmentId AND parentId=0";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row		=	mysqli_fetch_assoc($result);
				$platfromId	=	$row['platfromId'];

				return $platfromId;
			}
			else
			{
				return false;
			}
		}
		//Function to get paltform name
		function getPlatformName($platfromId)
		{
			$name = $this->getSingleQueryResult("SELECT name FROM platform_clients WHERE platfromId=$platfromId AND parentId=0","name");
			return $name;
		}
		//Function to get existing customer
		function getExistingCustomer($name,$parentId)
		{
			$query	=	"SELECT platfromId FROM platform_clients WHERE name='$name' AND parentId=$parentId";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row		=	mysqli_fetch_assoc($result);
				$platfromId		=	$row['platfromId'];

				return $platfromId;
			}
			else
			{
				return false;
			}
		}
		//Function to get customer name
		function getCustomerName($customerId,$parentId)
		{
			return $name = $this->getSingleQueryResult("SELECT name FROM platform_clients WHERE customerId=$customerId AND parentId=$parentId","name");
		}

		////Function to get all paltform customer
		function getAllPlatform()
		{
			$query	=	"SELECT platfromId,name FROM platform_clients WHERE parentId=0 ORDER BY name";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}

		//Function to get all paltform for department
		function getPlatformByDepartment($departmentId)
		{
			$query	=	"SELECT platfromId,name FROM platform_clients WHERE parentId=0 AND departmentId=$departmentId ORDER BY name";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//Function to get all customers of  an platform department
		function getPlatformClients($parentId)
		{
			$query	=	"SELECT customerId,name FROM platform_clients WHERE parentId=$parentId AND customerId <> 0 ORDER BY name";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}

		//Function To Get Opening Balance
		function getCurrentAccountBalance()
		{
			$mainOpeningBal	=	OPENING_BALANCE;
			$totalDebit		=	$this->getSingleQueryResult("SELECT SUM(amount) as total FROM company_daily_accounts WHERE type=1","total");
			if(empty($totalDebit))
			{
				$totalDebit	=	0;
			}

			$totalCredit	=	$this->getSingleQueryResult("SELECT SUM(amount) as total FROM company_daily_accounts WHERE type=2","total");
			if(empty($totalDebit))
			if(empty($totalCredit))
			{
				$totalCredit=	0;
			}

			$grandTotalCredit	=	$totalCredit+$mainOpeningBal;

			$balanceMoney		=	$grandTotalCredit-$totalDebit;
			$balanceMoney		=	round($balanceMoney);

			return $balanceMoney;
		}
		//View All Employess For A Customers
		function replyEmployessForCustomers($customerId,$employeeId=0)
		{
			$a_employeesList=	array();
			$andClause		=	"";
			if(!empty($employeeId))
			{
				$andClause	=	" AND pdf_clients_employees.employeeId <> $employeeId";
			}
			$query	=	"SELECT pdf_clients_employees.employeeId,firstName,lastName FROM pdf_clients_employees INNER JOIN employee_details ON pdf_clients_employees.employeeId=employee_details.employeeId WHERE customerId=$customerId AND hasReplyAccess=1 AND isActive=1".$andClause;
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row	=	mysqli_fetch_assoc($result))
				{
					$employeeId		=	$row['employeeId'];
					$firstName		=	stripslashes($row['firstName']);
					$lastName		=	stripslashes($row['lastName']);

					$employeeName	=	$firstName." ".$lastName;
					$employeeName	=	ucwords($employeeName);

					$a_employeesList[$employeeId]	=	$employeeName;
				}
				$a_employeesList=	implode(",<br>",$a_employeesList);

				return $a_employeesList;

			}
			else
			{
				return false;
			}
		}
		//Function to get has email receiving access
		function hasEmailReceiveAccess($employeeId)
		{
			$access		=	$this->getSingleQueryResult("SELECT receivePdfEmails FROM employee_details WHERE employeeId=$employeeId","receivePdfEmails");

			return $access;
		}
		//Function to get has email receiving access
		function maximumAcceptOrders($employeeId)
		{
			$maximum		=	$this->getSingleQueryResult("SELECT maximumOrdersAccept FROM employee_details WHERE employeeId=$employeeId","maximumOrdersAccept");

			return $maximum;
		}
		//Function To Check Is A Employee Manager
		function isEmployeeManager($employeeId)
		{
			$isManager		=	$this->getSingleQueryResult("SELECT isManager FROM employee_details WHERE employeeId=$employeeId","isManager");

			return $isManager;
		}
		//Function to get all pdf employees
		function getAllPdfEmployees()
		{
			$query	=	"SELECT employeeId,firstName,lastName,maximumOrdersAccept,shiftType,email FROM employee_details  WHERE isActive=1 AND hasPdfAccess=1 ORDER BY firstName";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}

		//Function to get all pdf employees day shift
		function getAllDayShiftPdfEmployees()
		{
			$query	=	"SELECT employeeId,firstName,lastName FROM employee_details  WHERE isActive=1 AND hasPdfAccess=1  AND isNightShiftEmployee=0 ORDER BY firstName";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		//Function to get all pdf employees night shift
		function getAllNightShiftPdfEmployees()
		{
			$query	=	"SELECT employeeId,firstName,lastName FROM employee_details  WHERE isActive=1 AND hasPdfAccess=1 AND isNightShiftEmployee=1 ORDER BY firstName";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}
		
		//Function to add edit MT employee work
		function addEditMtWorksRates($datewiseID,$employeeId,$platform,$customerId,$transcriptionLinesEntered,$vreLinesEntered,$qaLinesEntered,$auditLinesEntered,$indirectTranscriptionLinesEntered,$indirectVreLinesEntered,$indirectQaLinesEntered,$indirectAuditLinesEntered,$comments,$transcriptionUserId,$vreUserId,$qaUserId,$auditUserId,$isDateSpecific="")
		{
						
			global $db_conn;

			if(!empty($datewiseID))
			{
				$workedOnDate		=	$this->getSingleQueryResult("SELECT workedOnDate FROM datewise_employee_works_money WHERE ID=$datewiseID AND employeeId=$employeeId","workedOnDate");
			}
			else
			{
				if(!empty($isDateSpecific))
				{
					$workedOnDate	=  $isDateSpecific;
				}
				else
				{
					$workedOnDate	=  CURRENT_DATE_CUSTOMER_ZONE;
				}
			}

			if($result		=	$this->isClientFixedRateExists($platform,$customerId,$workedOnDate))
			{
				$row							=	mysqli_fetch_assoc($result);
				$directTranscriptionRate		=	$row['directTranscriptionRate'];
				$indirectTranscriptionRate		=	$row['indirectTranscriptionRate'];
				$directVreRate					=	$row['directVreRate'];
				$indirectVreRate				=	$row['indirectVreRate'];
				$directQaRate					=	$row['directQaRate'];
				$indirectQaRate					=	$row['indirectQaRate'];
				$directAuditRate				=	$row['directAuditRate'];
				$indirectAuditRate				=	$row['indirectAuditRate'];

				$totalDirectTrascriptionMoney	=	$transcriptionLinesEntered*$directTranscriptionRate;
				$totalDirectTrascriptionMoney	=	round($totalDirectTrascriptionMoney);

				$totalIndirectTrascriptionMoney	=	$indirectTranscriptionLinesEntered*$indirectTranscriptionRate;
				$totalIndirectTrascriptionMoney	=	round($totalIndirectTrascriptionMoney);

				$totalDirectVreMoney			=	$vreLinesEntered*$directVreRate;
				$totalDirectVreMoney			=	round($totalDirectVreMoney);

				$totalIndirectVreMoney			=	$indirectVreLinesEntered*$indirectVreRate;
				$totalIndirectVreMoney			=	round($totalIndirectVreMoney);

				$totalDirectQaMoney				=	$qaLinesEntered*$directQaRate;
				$totalDirectQaMoney				=	round($totalDirectQaMoney);

				$totalIndirectQaMoney			=	$indirectQaLinesEntered*$indirectQaRate;
				$totalIndirectQaMoney			=	round($totalIndirectQaMoney);

				$totalDirectAuditMoney			=	$auditLinesEntered*$directAuditRate;
				$totalDirectAuditMoney			=	round($totalDirectAuditMoney);

				$totalIndirectAuditMoney		=	$indirectAuditLinesEntered*$indirectAuditRate;
				$totalIndirectAuditMoney		=	round($totalIndirectAuditMoney);
			}
			else
			{
				if($result						=	$this->getRatesOfEmployee($employeeId,$workedOnDate))
				{
					$row						=	mysqli_fetch_assoc($result);
					$directTranscriptionRate	=	$row['directTranscriptionRate'];
					$indirectTranscriptionRate	=	$row['indirectTranscriptionRate'];
					$directVreRate				=	$row['directVreRate'];
					$indirectVreRate			=	$row['indirectVreRate'];
					$directQaRate				=	$row['directQaRate'];
					$indirectQaRate				=	$row['indirectQaRate'];
					$directAuditRate			=	$row['directAuditRate'];
					$indirectAuditRate			=	$row['indirectAuditRate'];

					$totalDirectTrascriptionMoney	=	$transcriptionLinesEntered*$directTranscriptionRate;
					$totalDirectTrascriptionMoney	=	round($totalDirectTrascriptionMoney);

					$totalIndirectTrascriptionMoney	=	$indirectTranscriptionLinesEntered*$indirectTranscriptionRate;
					$totalIndirectTrascriptionMoney	=	round($totalIndirectTrascriptionMoney);

					$totalDirectVreMoney			=	$vreLinesEntered*$directVreRate;
					$totalDirectVreMoney			=	round($totalDirectVreMoney);

					$totalIndirectVreMoney			=	$indirectVreLinesEntered*$indirectVreRate;
					$totalIndirectVreMoney			=	round($totalIndirectVreMoney);

					$totalDirectQaMoney				=	$qaLinesEntered*$directQaRate;
					$totalDirectQaMoney				=	round($totalDirectQaMoney);

					$totalIndirectQaMoney			=	$indirectQaLinesEntered*$indirectQaRate;
					$totalIndirectQaMoney			=	round($totalIndirectQaMoney);

					$totalDirectAuditMoney			=	$auditLinesEntered*$directAuditRate;
					$totalDirectAuditMoney			=	round($totalDirectAuditMoney);

					$totalIndirectAuditMoney		=	$indirectAuditLinesEntered*$indirectAuditRate;
					$totalIndirectAuditMoney		=	round($totalIndirectAuditMoney);
				}
			}

			$optionQuery	=	" SET employeeId=$employeeId,platform=$platform,customerId=$customerId,departmentId=1,totalDirectTrascriptionLines='$transcriptionLinesEntered',directTranscriptionRate='$directTranscriptionRate',totalDirectTrascriptionMoney='$totalDirectTrascriptionMoney',totalIndirectTrascriptionLines='$indirectTranscriptionLinesEntered',indirectTranscriptionRate='$indirectTranscriptionRate',totalIndirectTrascriptionMoney='$totalIndirectTrascriptionMoney',totalDirectVreLines='$vreLinesEntered',directVreRate='$directVreRate',totalDirectVreMoney='$totalDirectVreMoney',totalIndirectVreLines='$indirectVreLinesEntered',indirectVreRate='$indirectVreRate',totalIndirectVreMoney='$totalIndirectVreMoney',totalQaLines='$qaLinesEntered',directQaRate='$directQaRate',totalDirectQaMoney='$totalDirectQaMoney',totalIndirectQaLines='$indirectQaLinesEntered',indirectQaRate='$indirectQaRate',totalIndirectQaMoney='$totalIndirectQaMoney',totalDirectAuditLines='$auditLinesEntered',directAuditRate='$directAuditRate',totalDirectAuditMoney='$totalDirectAuditMoney',totalIndirectAuditLines='$indirectAuditLinesEntered',indirectAuditRate='$indirectAuditRate',totalIndirectAuditMoney='$totalIndirectAuditMoney',comments='$comments',transcriptionUserId='$transcriptionUserId',vreUserId='$vreUserId',qaUserId='$qaUserId',auditUserId='$auditUserId'";

			if(empty($datewiseID))
			{
				$query	=	"INSERT INTO datewise_employee_works_money ".$optionQuery.",workedOnDate='".$workedOnDate."',addedOn='".CURRENT_DATE_CUSTOMER_ZONE."',addedTime='".CURRENT_TIME_CUSTOMER_ZONE."',isTragetAdded=1";
				dbQuery($query);
				$datewiseID	=	mysqli_insert_id($db_conn);
			}
			else
			{
				$query	=	"UPDATE datewise_employee_works_money".$optionQuery." WHERE ID=$datewiseID AND employeeId=$employeeId";
				
				dbQuery($query);
			}

			return $datewiseID;
		}
		//Function to update eixtsing line count total money when rates are increased
		function updateExistingCustomerFixedRate($fixedRateId,$platform,$customerId,$fromDate,$toDate)
		{
			if($result		=	$this->isClientFixedRateExists($platform,$customerId,""))
			{
				$row							=	mysqli_fetch_assoc($result);
				$directTranscriptionRate		=	$row['directTranscriptionRate'];
				$indirectTranscriptionRate		=	$row['indirectTranscriptionRate'];
				$directVreRate					=	$row['directVreRate'];
				$indirectVreRate				=	$row['indirectVreRate'];
				$directQaRate					=	$row['directQaRate'];
				$indirectQaRate					=	$row['indirectQaRate'];
				$directAuditRate				=	$row['directAuditRate'];
				$indirectAuditRate				=	$row['indirectAuditRate'];


				$query1							=	"SELECT * FROM datewise_employee_works_money WHERE platform=$platform AND customerId=$customerId AND workedOnDate >= '$fromDate' AND workedOnDate <= '$toDate'";
				$result1						=	dbQuery($query1);
				if(mysqli_num_rows($result1))
				{
					while($row1	=	mysqli_fetch_assoc($result1))
					{
						$employeeId						=	$row1['employeeId'];
						$ID								=	$row1['ID'];
						
						$totalDirectTrascriptionLines	=	$row1['totalDirectTrascriptionLines'];
						$totalIndirectTrascriptionLines	=	$row1['totalIndirectTrascriptionLines'];

						$totalDirectVreLines			=	$row1['totalDirectVreLines'];
						$totalIndirectVreLines			=	$row1['totalIndirectVreLines'];

						$totalQaLines					=	$row1['totalQaLines'];
						$totalIndirectQaLines			=	$row1['totalIndirectQaLines'];

						$totalDirectAuditLines			=	$row1['totalDirectAuditLines'];
						$totalIndirectAuditLines		=	$row1['totalIndirectAuditLines'];
						
						$totalDirectLevel1Lines			=	$row1['totalDirectLevel1Lines'];
						$totalDirectLevel2Lines			=	$row1['totalDirectLevel2Lines'];

						$totalIndirectLevel1Lines		=	$row1['totalIndirectLevel1Lines'];
						$totalIndirectLevel2Lines		=	$row1['totalIndirectLevel2Lines'];

						$totalQaLevel1Lines				=	$row1['totalQaLevel1Lines'];
						$totalQaLevel2Lines				=	$row1['totalQaLevel2Lines'];

						$totalAuditLevel1Lines			=	$row1['totalAuditLevel1Lines'];
						$totalAuditLevel2Lines			=	$row1['totalAuditLevel2Lines'];

						$totalDirectTrascriptionMoney	=	$totalDirectTrascriptionLines*$directTranscriptionRate;
						$totalDirectTrascriptionMoney	=	round($totalDirectTrascriptionMoney);

						$totalIndirectTrascriptionMoney	=	$totalIndirectTrascriptionLines*$indirectTranscriptionRate;
						$totalIndirectTrascriptionMoney	=	round($totalIndirectTrascriptionMoney);

						$totalDirectVreMoney			=	$totalDirectVreLines*$directVreRate;
						$totalDirectVreMoney			=	round($totalDirectVreMoney);

						$totalIndirectVreMoney			=	$totalIndirectVreLines*$indirectVreRate;
						$totalIndirectVreMoney			=	round($totalIndirectVreMoney);

						$totalDirectQaMoney				=	$totalQaLines*$directQaRate;
						$totalDirectQaMoney				=	round($totalDirectQaMoney);

						$totalIndirectQaMoney			=	$totalIndirectQaLines*$indirectQaRate;
						$totalIndirectQaMoney			=	round($totalIndirectQaMoney);

						$totalDirectAuditMoney			=	$totalDirectAuditLines*$directAuditRate;
						$totalDirectAuditMoney			=	round($totalDirectAuditMoney);

						$totalIndirectAuditMoney		=	$totalIndirectAuditLines*$indirectAuditRate;
						$totalIndirectAuditMoney		=	round($totalIndirectAuditMoney);


						$totalDirectLevel1Money			=	$totalDirectLevel1Lines*$directLevel1Rate;
						$totalDirectLevel1Money			=	round($totalDirectLevel1Money);

						$totalDirectLevel2Money			=	$totalDirectLevel2Lines*$directLevel2Rate;
						$totalDirectLevel2Money			=	round($totalDirectLevel2Money);

						$totalIndirectLevel1Money		=	$totalIndirectLevel1Lines*$indirectLevel1Rate;
						$totalIndirectLevel1Money		=	round($totalIndirectLevel1Money);

						$totalIndirectLevel2Money		=	$totalIndirectLevel2Lines*$indirectLevel2Rate;
						$totalIndirectLevel2Money		=	round($totalIndirectLevel2Money);

						$totalQaLevel1Money				=	$totalQaLevel1Lines*$qaLevel1Rate;
						$totalQaLevel1Money				=	round($totalQaLevel1Money);

						$totalQaLevel2Money				=	$totalQaLevel2Lines*$qaLevel2Rate;
						$totalQaLevel2Money				=	round($totalQaLevel2Money);

						$totalAuditLevel1Money			=	$totalAuditLevel1Lines*$auditLevel1Rate;
						$totalAuditLevel1Money			=	round($totalAuditLevel1Money);

						$totalAuditLevel2Money			=	$totalAuditLevel2Lines*$auditLevel2Rate;
						$totalAuditLevel2Money			=	round($totalAuditLevel2Money);

						$optionQuery1	=	" SET totalDirectTrascriptionLines='$totalDirectTrascriptionLines',directTranscriptionRate='$directTranscriptionRate',totalDirectTrascriptionMoney='$totalDirectTrascriptionMoney',totalIndirectTrascriptionLines='$totalIndirectTrascriptionLines',indirectTranscriptionRate='$indirectTranscriptionRate',totalIndirectTrascriptionMoney='$totalIndirectTrascriptionMoney',totalDirectVreLines='$totalDirectVreLines',directVreRate='$directVreRate',totalDirectVreMoney='$totalDirectVreMoney',totalIndirectVreLines='$totalIndirectVreLines',indirectVreRate='$indirectVreRate',totalIndirectVreMoney='$totalIndirectVreMoney',totalQaLines='$totalQaLines',directQaRate='$directQaRate',totalDirectQaMoney='$totalDirectQaMoney',totalIndirectQaLines='$totalIndirectQaLines',indirectQaRate='$indirectQaRate',totalIndirectQaMoney='$totalIndirectQaMoney',totalDirectAuditLines='$totalDirectAuditLines',directAuditRate='$directAuditRate',totalDirectAuditMoney='$totalDirectAuditMoney',totalIndirectAuditLines='$totalIndirectAuditLines',indirectAuditRate='$indirectAuditRate',totalIndirectAuditMoney='$totalIndirectAuditMoney'";

						dbQuery("UPDATE datewise_employee_works_money".$optionQuery1." WHERE employeeId=$employeeId AND ID=$ID AND platform=$platform AND customerId=$customerId");

					}
				}
			}
			return true;
		}


		//function to add edit mt employee daily works with money
		function updateMtEmployeeWorkRates($workId,$employeeId,$platform,$customerId,$transcriptionLinesEntered,$vreLinesEntered,$qaLinesEntered,$auditLinesEntered,$indirectTranscriptionLinesEntered,$indirectVreLinesEntered,$indirectQaLinesEntered,$indirectAuditLinesEntered)
		{
					
			$workedOnDate	=	$this->getSingleQueryResult("SELECT workedOn FROM employee_works WHERE workId=$workId AND employeeId=$employeeId","workedOn");

			if($result		=	$this->isClientFixedRateExists($platform,$customerId,$workedOnDate))
			{
				$row						=	mysqli_fetch_assoc($result);
				$directTranscriptionRate	=	$row['directTranscriptionRate'];
				$indirectTranscriptionRate	=	$row['indirectTranscriptionRate'];
				$directVreRate				=	$row['directVreRate'];
				$indirectVreRate			=	$row['indirectVreRate'];
				$directQaRate				=	$row['directQaRate'];
				$indirectQaRate				=	$row['indirectQaRate'];
				$directAuditRate			=	$row['directAuditRate'];
				$indirectAuditRate				=	$row['indirectAuditRate'];

				$totalDirectTrascriptionMoney	=	$transcriptionLinesEntered*$directTranscriptionRate;
				$totalDirectTrascriptionMoney	=	round($totalDirectTrascriptionMoney);

				$totalIndirectTrascriptionMoney	=	$indirectTranscriptionLinesEntered*$indirectTranscriptionRate;
				$totalIndirectTrascriptionMoney	=	round($totalIndirectTrascriptionMoney);

				$totalDirectVreMoney			=	$vreLinesEntered*$directVreRate;
				$totalDirectVreMoney			=	round($totalDirectVreMoney);

				$totalIndirectVreMoney			=	$indirectVreLinesEntered*$indirectVreRate;
				$totalIndirectVreMoney			=	round($totalIndirectVreMoney);

				$totalDirectQaMoney				=	$qaLinesEntered*$directQaRate;
				$totalDirectQaMoney				=	round($totalDirectQaMoney);

				$totalIndirectQaMoney			=	$indirectQaLinesEntered*$indirectQaRate;
				$totalIndirectQaMoney			=	round($totalIndirectQaMoney);

				$totalDirectAuditMoney			=	$auditLinesEntered*$directAuditRate;
				$totalDirectAuditMoney			=	round($totalDirectAuditMoney);

				$totalIndirectAuditMoney		=	$indirectAuditLinesEntered*$indirectAuditRate;
				$totalIndirectAuditMoney		=	round($totalIndirectAuditMoney);

				$optionQuery	=	" SET workId=$workId,employeeId=$employeeId,platform=$platform,customerId=$customerId,departmentId=1,workedOnDate='$workedOnDate',totalDirectTrascriptionLines='$transcriptionLinesEntered',directTranscriptionRate='$directTranscriptionRate',totalDirectTrascriptionMoney='$totalDirectTrascriptionMoney',totalIndirectTrascriptionLines='$indirectTranscriptionLinesEntered',indirectTranscriptionRate='$indirectTranscriptionRate',totalIndirectTrascriptionMoney='$totalIndirectTrascriptionMoney',totalDirectVreLines='$vreLinesEntered',directVreRate='$directVreRate',totalDirectVreMoney='$totalDirectVreMoney',totalIndirectVreLines='$indirectVreLinesEntered',indirectVreRate='$indirectVreRate',totalIndirectVreMoney='$totalIndirectVreMoney',totalQaLines='$qaLinesEntered',directQaRate='$directQaRate',totalDirectQaMoney='$totalDirectQaMoney',totalIndirectQaLines='$indirectQaLinesEntered',indirectQaRate='$indirectQaRate',totalIndirectQaMoney='$totalIndirectQaMoney',totalDirectAuditLines='$auditLinesEntered',directAuditRate='$directAuditRate',totalDirectAuditMoney='$totalDirectAuditMoney',totalIndirectAuditLines='$indirectAuditLinesEntered',indirectAuditRate='$indirectAuditRate',totalIndirectAuditMoney='$totalIndirectAuditMoney'";

				$query1	=	"SELECT ID FROM datewise_employee_works_money WHERE workId=$workId AND employeeId=$employeeId AND workedOnDate='$workedOnDate'";
				$result1	=	dbQuery($query1);
				if(mysqli_num_rows($result1))
				{
					$row1	=	mysqli_fetch_assoc($result1);

					$ID		=	$row1['ID'];

					dbQuery("UPDATE datewise_employee_works_money".$optionQuery." WHERE ID=$ID");
				}
				else
				{
					dbQuery("INSERT INTO datewise_employee_works_money".$optionQuery.",addedOn='".CURRENT_DATE_CUSTOMER_ZONE."',addedTime='".CURRENT_TIME_CUSTOMER_ZONE."'");
				}
			}
			else
			{
				if($result		=	$this->getRatesOfEmployee($employeeId,$workedOnDate))
				{
					$row						=	mysqli_fetch_assoc($result);
					$directTranscriptionRate	=	$row['directTranscriptionRate'];
					$indirectTranscriptionRate	=	$row['indirectTranscriptionRate'];
					$directVreRate				=	$row['directVreRate'];
					$indirectVreRate			=	$row['indirectVreRate'];
					$directQaRate				=	$row['directQaRate'];
					$indirectQaRate				=	$row['indirectQaRate'];
					$directAuditRate			=	$row['directAuditRate'];
					$indirectAuditRate			=	$row['indirectAuditRate'];

					$totalDirectTrascriptionMoney	=	$transcriptionLinesEntered*$directTranscriptionRate;
					$totalDirectTrascriptionMoney	=	round($totalDirectTrascriptionMoney);

					$totalIndirectTrascriptionMoney	=	$indirectTranscriptionLinesEntered*$indirectTranscriptionRate;
					$totalIndirectTrascriptionMoney	=	round($totalIndirectTrascriptionMoney);

					$totalDirectVreMoney			=	$vreLinesEntered*$directVreRate;
					$totalDirectVreMoney			=	round($totalDirectVreMoney);

					$totalIndirectVreMoney			=	$indirectVreLinesEntered*$indirectVreRate;
					$totalIndirectVreMoney			=	round($totalIndirectVreMoney);

					$totalDirectQaMoney				=	$qaLinesEntered*$directQaRate;
					$totalDirectQaMoney				=	round($totalDirectQaMoney);

					$totalIndirectQaMoney			=	$indirectQaLinesEntered*$indirectQaRate;
					$totalIndirectQaMoney			=	round($totalIndirectQaMoney);

					$totalDirectAuditMoney			=	$auditLinesEntered*$directAuditRate;
					$totalDirectAuditMoney			=	round($totalDirectAuditMoney);

					$totalIndirectAuditMoney		=	$indirectAuditLinesEntered*$indirectAuditRate;
					$totalIndirectAuditMoney		=	round($totalIndirectAuditMoney);

					$optionQuery	=	" SET workId=$workId,employeeId=$employeeId,platform=$platform,customerId=$customerId,departmentId=1,workedOnDate='$workedOnDate',totalDirectTrascriptionLines='$transcriptionLinesEntered',directTranscriptionRate='$directTranscriptionRate',totalDirectTrascriptionMoney='$totalDirectTrascriptionMoney',totalIndirectTrascriptionLines='$indirectTranscriptionLinesEntered',indirectTranscriptionRate='$indirectTranscriptionRate',totalIndirectTrascriptionMoney='$totalIndirectTrascriptionMoney',totalDirectVreLines='$vreLinesEntered',directVreRate='$directVreRate',totalDirectVreMoney='$totalDirectVreMoney',totalIndirectVreLines='$indirectVreLinesEntered',indirectVreRate='$indirectVreRate',totalIndirectVreMoney='$totalIndirectVreMoney',totalQaLines='$qaLinesEntered',directQaRate='$directQaRate',totalDirectQaMoney='$totalDirectQaMoney',totalIndirectQaLines='$indirectQaLinesEntered',indirectQaRate='$indirectQaRate',totalIndirectQaMoney='$totalIndirectQaMoney',totalDirectAuditLines='$auditLinesEntered',directAuditRate='$directAuditRate',totalDirectAuditMoney='$totalDirectAuditMoney',totalIndirectAuditLines='$indirectAuditLinesEntered',indirectAuditRate='$indirectAuditRate',totalIndirectAuditMoney='$totalIndirectAuditMoney'";

					$query1		=	"SELECT ID FROM datewise_employee_works_money WHERE workId=$workId AND employeeId=$employeeId AND workedOnDate='$workedOnDate'";
					$result1	=	dbQuery($query1);
					if(mysqli_num_rows($result1))
					{
						$row1	=	mysqli_fetch_assoc($result1);

						$ID		=	$row1['ID'];

						dbQuery("UPDATE datewise_employee_works_money".$optionQuery." WHERE ID=$ID");
					}
					else
					{
						dbQuery("INSERT INTO datewise_employee_works_money".$optionQuery.",addedOn='".CURRENT_DATE_CUSTOMER_ZONE."',addedTime='".CURRENT_TIME_CUSTOMER_ZONE."'");
					}
				}
			 }
			 return true;
		}

		//function to add edit mt employee daily works with money
		function updateRevEmployeeWorkRates($workId,$employeeId,$platform,$customerId,$directLevel1,$directLevel2,$indirectLevel1,$indirectLevel2,$qaLevel1,$qaLevel2,$auditLevel1,$auditLevel2)
		{
			$workedOnDate	=	$this->getSingleQueryResult("SELECT workedOn FROM employee_works WHERE workId=$workId AND employeeId=$employeeId","workedOn");
			if($result		=	$this->getRatesOfEmployee($employeeId,$workedOnDate))
			{
				$row						=	mysqli_fetch_assoc($result);
				$directLevel1Rate			=	$row['directLevel1Rate'];
				$directLevel2Rate			=	$row['directLevel2Rate'];
				$indirectLevel1Rate			=	$row['indirectLevel1Rate'];
				$indirectLevel2Rate			=	$row['indirectLevel2Rate'];
				$qaLevel1Rate				=	$row['qaLevel1Rate'];
				$qaLevel2Rate				=	$row['qaLevel2Rate'];
				$auditLevel1Rate			=	$row['auditLevel1Rate'];
				$auditLevel2Rate			=	$row['auditLevel2Rate'];

				$totalDirectLevel1Money		=	$directLevel1*$directLevel1Rate;
				$totalDirectLevel1Money		=	round($totalDirectLevel1Money);

				$totalDirectLevel2Money		=	$directLevel2*$directLevel2Rate;
				$totalDirectLevel2Money		=	round($totalDirectLevel2Money);

				$totalIndirectLevel1Money	=	$indirectLevel1*$indirectLevel1Rate;
				$totalIndirectLevel1Money	=	round($totalIndirectLevel1Money);

				$totalIndirectLevel2Money	=	$indirectLevel2*$indirectLevel2Rate;
				$totalIndirectLevel2Money	=	round($totalIndirectLevel2Money);

				$totalQaLevel1Money			=	$qaLevel1*$qaLevel1Rate;
				$totalQaLevel1Money			=	round($totalQaLevel1Money);

				$totalQaLevel2Money			=	$qaLevel2*$qaLevel2Rate;
				$totalQaLevel2Money			=	round($totalQaLevel2Money);

				$totalAuditLevel1Money		=	$auditLevel1*$auditLevel1Rate;
				$totalAuditLevel1Money		=	round($totalAuditLevel1Money);

				$totalAuditLevel2Money		=	$auditLevel2*$auditLevel2Rate;
				$totalAuditLevel2Money		=	round($totalAuditLevel2Money);


				$optionQuery	=	" SET workId=$workId,employeeId=$employeeId,platform=$platform,customerId=$customerId,departmentId=2,workedOnDate='$workedOnDate',totalDirectLevel1Lines=$directLevel1,directLevel1Rate=$directLevel1Rate,totalDirectLevel1Money=$totalDirectLevel1Money,totalDirectLevel2Lines=$directLevel2,directLevel2Rate=$directLevel2Rate,totalDirectLevel2Money=$totalDirectLevel2Money,totalIndirectLevel1Lines=$indirectLevel1,indirectLevel1Rate=$indirectLevel1Rate,totalIndirectLevel1Money=$totalIndirectLevel1Money,totalIndirectLevel2Lines=$indirectLevel2,indirectLevel2Rate=$indirectLevel2Rate,totalIndirectLevel2Money=$totalIndirectLevel2Money,totalQaLevel1Lines=$qaLevel1,qaLevel1Rate=$qaLevel1Rate,totalQaLevel1Money=$totalQaLevel1Money,totalQaLevel2Lines=$qaLevel2,qaLevel2Rate=$qaLevel2Rate,totalQaLevel2Money=$totalQaLevel2Money,totalAuditLevel1Lines=$auditLevel1,auditLevel1Rate=$auditLevel1Rate,totalAuditLevel1Money=$totalAuditLevel1Money,totalAuditLevel2Lines=$auditLevel2,auditLevel2Rate=$auditLevel2Rate,totalAuditLevel2Money=$totalAuditLevel2Money";

				$query1	=	"SELECT ID FROM datewise_employee_works_money WHERE workId=$workId AND employeeId=$employeeId AND workedOnDate='$workedOnDate'";
				$result1	=	dbQuery($query1);
				if(mysqli_num_rows($result1))
				{
					$row1	=	mysqli_fetch_assoc($result1);

					$ID		=	$row1['ID'];

					dbQuery("UPDATE datewise_employee_works_money".$optionQuery." WHERE ID=$ID");
				}
				else
				{
					dbQuery("INSERT INTO datewise_employee_works_money".$optionQuery.",addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
				}
				return true;

			}
			else
			{
				return false;
			}
		
		}

		//Function to get datewise mt employee money
		function getRatesOfEmployee($employeeId,$rateFor)
		{
			$query	=	"SELECT * FROM employee_line_rates WHERE employeeId=$employeeId AND rateValidFrom <= '$rateFor' AND rateValidTo >= '$rateFor' AND isActiveRate=1";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				$query	=	"SELECT * FROM employee_line_rates WHERE employeeId=$employeeId AND isActiveRate=1";
				$result	=	dbQuery($query);
				if(mysqli_num_rows($result))
				{
					return $result;
				}
				else
				{
					return false;
				}
			}
		}
		//Function to get all managers
		function getAllEmployeeManager($type="")
		{
			$andClause		=	"";
			if($type		==	"MT")
			{
				$andClause	=	" AND hasPdfAccess=0";
			}
			elseif($type	==	"PDF")
			{
				$andClause	=	" AND hasPdfAccess=1";
			}
			
			$query	=	"SELECT employeeId,firstName,lastName FROM employee_details WHERE isManager=1 AND isActive=1".$andClause." ORDER BY firstName";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$a_managers			=	array();
				while($row			=	mysqli_fetch_assoc($result))
				{
					$employeeId		=	$row['employeeId'];
					$firstName		=	$row['firstName'];
					$lastName		=	$row['lastName'];

					$employeeName	=	$firstName." ".$lastName;
					$employeeName	=	ucwords($employeeName);

					$a_managers[$employeeId]	=	$employeeName;
				}
				return $a_managers;
			}
			else
			{
				return false;
			}
		}
		//Function to get total money for process PDF orders
		function getProcessPdfTotalMoney($employeeId,$month,$year)
		{
			$a_pendingIds	=	array();
			$query	=	"SELECT orderId FROM members_orders WHERE acceptedBy=$employeeId AND MONTH(assignToEmployee)=".$month." AND YEAR(assignToEmployee)=".$year;
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row1		=	mysqli_fetch_assoc($result))
				{
					$orderId				=	$row1['orderId'];
					$a_pendingIds[$orderId]	=	$orderId;
				}
			}
			if(!empty($a_pendingIds))
			{
				$totalOrderMoney = 0;
				$pendingIds	=	implode(",",$a_pendingIds);
				$query		=	"SELECT orderId FROM members_orders_reply WHERE hasRepliedFileUploaded=1 AND orderId IN ($pendingIds)";
				$result		=	dbQuery($query);
				if(mysqli_num_rows($result))
				{
					while($row	=	mysqli_fetch_assoc($result))
					{
						$orderId			=	$row['orderId'];
						$assignToEmployee	=	$this->getSingleQueryResult("SELECT assignToEmployee FROM members_orders WHERE employeeId=$employeeId AND orderId=$orderId","assignToEmployee");

						$money		=	$this->getSingleQueryResult("SELECT orderRate FROM pdf_employees_rate WHERE employeeId=$employeeId AND '$assignToEmployee' >= rateValidFrom AND rateValidTo='0000-00-00' AND isActiveRate=1","orderRate");
						if(empty($money))
						{
							$money	=	$this->getSingleQueryResult("SELECT orderRate FROM pdf_employees_rate WHERE employeeId=$employeeId AND '$assignToEmployee' >= rateValidFrom AND rateValidTo <= '$assignToEmployee' AND isActiveRate=0","orderRate");
						}
						if(empty($money))
						{
							$money	=	0;
						}
						$totalOrderMoney	=	$totalOrderMoney+$money;

					}
					return $totalOrderMoney;
				}
				else
				{
					return false;
				}
			} 
			else
			{
				return false;
			}
		}
		//Function to get total money for QA PDF orders
		function getQaPdfTotalMoney($employeeId,$month,$year)
		{
			$totalQaMoney	=	0;
			$query			=	"SELECT replyId,qaDoneOn FROM members_orders_reply WHERE hasQaDone=1 AND qaDoneBy=$employeeId AND MONTH(qaDoneOn)=".$month." AND YEAR(qaDoneOn)=".$year;
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row		=	mysqli_fetch_assoc($result))
				{
					$replyId	=	$row['replyId'];
					$qaDoneOn	=	$row['qaDoneOn'];

					$money		=	$this->getSingleQueryResult("SELECT orderQaRate FROM pdf_employees_rate WHERE employeeId=$employeeId AND '$qaDoneOn' >= rateValidFrom AND rateValidTo='0000-00-00' AND isActiveRate=1","orderQaRate");
					if(empty($money))
					{
						$money	=	$this->getSingleQueryResult("SELECT orderQaRate FROM pdf_employees_rate WHERE employeeId=$employeeId AND '$qaDoneOn' >= rateValidFrom AND rateValidTo <= '$qaDoneOn' AND isActiveRate=0","orderQaRate");
					}
					if(empty($money))
					{
						$money	=	0;
					}
					$totalQaMoney	=	$totalQaMoney+$money;

				}
				return $totalQaMoney;
			}
			else
			{
				return false;
			}
		}
		//Function to get total lines done in a mkonth by MT
		function getCurrentMonthMTLinesMoney($employeeId,$month,$year)
		{
			$grandLines		=	0;
			$grandTotal		=	0;
			$query			=	"SELECT SUM(totalDirectTrascriptionLines) AS totalDirectTrascriptionLines,SUM(totalDirectTrascriptionMoney) AS totalDirectTrascriptionMoney,SUM(totalIndirectTrascriptionLines) AS totalIndirectTrascriptionLines,SUM(totalIndirectTrascriptionMoney) AS totalIndirectTrascriptionMoney,SUM(totalDirectVreLines) AS totalDirectVreLines,SUM(totalDirectVreMoney) AS totalDirectVreMoney,SUM(totalIndirectVreLines) AS totalIndirectVreLines,SUM(totalIndirectVreMoney) AS totalIndirectVreMoney,SUM(totalQaLines) AS totalQaLines,SUM(totalDirectQaMoney) AS totalDirectQaMoney,SUM(totalIndirectQaLines) AS totalIndirectQaLines,SUM(totalIndirectQaMoney) AS totalIndirectQaMoney,SUM(totalDirectAuditLines) AS totalDirectAuditLines,SUM(totalDirectAuditMoney) AS totalDirectAuditMoney,SUM(totalIndirectAuditLines) AS totalIndirectAuditLines,SUM(totalIndirectAuditMoney) AS totalIndirectAuditMoney FROM datewise_employee_works_money WHERE ID > '".MAX_SEARCH_MT_EMPLOYEE_WORKID."' AND  employeeId=$employeeId AND MONTH(workedOnDate)=$month AND YEAR(workedOnDate)=$year";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row	=	mysqli_fetch_assoc($result))
				{
					$totalLines		=	0;
					$totalMoney		=	0;
					$totalDirectTrascriptionLines	=	$row['totalDirectTrascriptionLines'];
					$totalDirectTrascriptionMoney	=	$row['totalDirectTrascriptionMoney'];

					$totalIndirectTrascriptionLines	=	$row['totalIndirectTrascriptionLines'];
					$totalIndirectTrascriptionMoney	=	$row['totalIndirectTrascriptionMoney'];

					$totalDirectVreLines			=	$row['totalDirectVreLines'];
					$totalDirectVreMoney			=	$row['totalDirectVreMoney'];

					$totalIndirectVreLines			=	$row['totalIndirectVreLines'];
					$totalIndirectVreMoney			=	$row['totalIndirectVreMoney'];

					$totalQaLines					=	$row['totalQaLines'];
					$totalDirectQaMoney				=	$row['totalDirectQaMoney'];

					$totalIndirectQaLines			=	$row['totalIndirectQaLines'];
					$totalIndirectQaMoney			=	$row['totalIndirectQaMoney'];

					$totalDirectAuditLines			=	$row['totalDirectAuditLines'];
					$totalDirectAuditMoney			=	$row['totalDirectAuditMoney'];

					$totalIndirectAuditLines		=	$row['totalIndirectAuditLines'];
					$totalIndirectAuditMoney		=	$row['totalIndirectAuditMoney'];

					$totalLines		=	$totalDirectTrascriptionLines+$totalIndirectTrascriptionLines+$totalDirectVreLines+$totalIndirectVreLines+$totalQaLines+$totalIndirectQaLines+$totalDirectAuditLines+$totalIndirectAuditLines;

					$totalMoney		=	$totalDirectTrascriptionMoney+$totalIndirectTrascriptionMoney+$totalDirectVreMoney+$totalIndirectVreMoney+$totalDirectQaMoney+$totalIndirectQaMoney+$totalDirectAuditMoney+$totalIndirectAuditMoney;
					
					$totalLines		=	round($totalLines);
					$totalMoney		=	round($totalMoney);

					$grandLines		=	$grandLines+$totalLines;
					$grandTotal		=	$grandTotal+$totalMoney;
				}

				$totalLineMoney		=	$grandLines."=".$grandTotal;

				return $totalLineMoney;
			}
			else
			{
				return false;
			}
		}
		//Function to get total lines done in a mkonth by REV
		function getCurrentMonthRevLinesMoney($employeeId,$month,$year)
		{
			$grandLines		=	0;
			$grandTotal		=	0;
			$query	=	"SELECT SUM(totalDirectLevel1Lines) AS totalDirectLevel1Lines,SUM(totalDirectLevel1Money) AS totalDirectLevel1Money,SUM(totalDirectLevel2Lines) AS totalDirectLevel2Lines,SUM(totalDirectLevel2Money) AS totalDirectLevel2Money,SUM(totalIndirectLevel1Lines) AS totalIndirectLevel1Lines,SUM(totalIndirectLevel1Money) AS totalIndirectLevel1Money,SUM(totalIndirectLevel2Lines) AS totalIndirectLevel2Lines,SUM(totalIndirectLevel2Money) AS totalIndirectLevel2Money,SUM(totalQaLevel1Lines) AS totalQaLevel1Lines,SUM(totalQaLevel1Money) AS totalQaLevel1Money,SUM(totalQaLevel2Lines) AS totalQaLevel2Lines,SUM(totalQaLevel2Money) AS totalQaLevel2Money,SUM(totalAuditLevel1Lines) AS totalAuditLevel1Lines,SUM(totalAuditLevel1Money) AS totalAuditLevel1Money,SUM(totalAuditLevel2Lines) AS totalAuditLevel2Lines,SUM(totalAuditLevel2Money) AS totalAuditLevel2Money FROM datewise_employee_works_money WHERE employeeId=$employeeId AND MONTH(workedOnDate)=$month AND YEAR(workedOnDate)=$year";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row	=	mysqli_fetch_assoc($result))
				{
					$totalLines		=	0;
					$totalMoney		=	0;
					$totalDirectLevel1Lines			=	$row['totalDirectLevel1Lines'];
					$totalDirectLevel2Lines			=	$row['totalDirectLevel2Lines'];
					$totalIndirectLevel1Lines		=	$row['totalIndirectLevel1Lines'];
					$totalIndirectLevel2Lines		=	$row['totalIndirectLevel2Lines'];
					$totalQaLevel1Lines				=	$row['totalQaLevel1Lines'];
					$totalQaLevel2Lines				=	$row['totalQaLevel2Lines'];
					$totalAuditLevel1Lines			=	$row['totalAuditLevel1Lines'];
					$totalAuditLevel2Lines			=	$row['totalAuditLevel2Lines'];

					$totalDirectLevel1Money			=	$row['totalDirectLevel1Money'];
					$totalDirectLevel2Money			=	$row['totalDirectLevel2Money'];
					$totalIndirectLevel1Money		=	$row['totalIndirectLevel1Money'];
					$totalIndirectLevel2Money		=	$row['totalIndirectLevel2Money'];
					$totalQaLevel1Money				=	$row['totalQaLevel1Money'];
					$totalQaLevel2Money				=	$row['totalQaLevel2Money'];
					$totalAuditLevel1Money			=	$row['totalAuditLevel1Money'];
					$totalAuditLevel2Money			=	$row['totalAuditLevel2Money'];

								
					$totalLines		=	$totalDirectLevel1Lines+$totalDirectLevel2Lines+$totalIndirectLevel1Lines+$totalIndirectLevel2Lines+$totalQaLevel1Lines+$totalQaLevel2Lines+$totalAuditLevel1Lines+$totalAuditLevel2Lines;

					$totalMoney		=	$totalDirectLevel1Money+$totalDirectLevel2Money+$totalIndirectLevel1Money+$totalIndirectLevel2Money+$totalQaLevel1Money+$totalQaLevel2Money+$totalAuditLevel1Money+$totalAuditLevel2Money;

					$totalLines		=	round($totalLines);
					$totalMoney		=	round($totalMoney);

					$grandLines		=	$grandLines+$totalLines;
					$grandTotal		=	$grandTotal+$totalMoney;
				}

				$totalLineMoney		=	$grandLines."=".$grandTotal;

				return $totalLineMoney;
			}
			else
			{
				return false;
			}
		}
		//Function get customer all pdf reply employees
		function getCustomerAllReplyEmployees($customerId)
		{
			$query	=	"SELECT pdf_clients_employees.employeeId,firstName,lastName FROM pdf_clients_employees INNER JOIN employee_details ON pdf_clients_employees.employeeId=employee_details.employeeId WHERE customerId=$customerId AND hasReplyAccess=1 AND isActive=1 ORDER BY firstName";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$a_processEmployees	=	array();
				while($row			=	mysqli_fetch_assoc($result))
				{
					$employeeId		=	$row['employeeId'];
					$firstName		=	stripslashes($row['firstName']);
					$lastName		=	stripslashes($row['lastName']);

					$employeeName	=	$firstName." ".$lastName;
					$employeeName	=	ucwords($employeeName);

					$a_processEmployees[$employeeId]	=	$employeeName;
				}
				return $a_processEmployees;
			}
			else
			{
				$query	=	"SELECT employeeId,firstName,lastName FROM employee_details WHERE isManager=1 AND isActive=1 ORDER BY firstName";
				$result	=	dbQuery($query);
				if(mysqli_num_rows($result))
				{
					$a_processEmployees	=	array();
					while($row			=	mysqli_fetch_assoc($result))
					{
						$employeeId		=	$row['employeeId'];
						$firstName		=	stripslashes($row['firstName']);
						$lastName		=	stripslashes($row['lastName']);

						$employeeName	=	$firstName." ".$lastName;
						$employeeName	=	ucwords($employeeName);

						$a_processEmployees[$employeeId]	=	$employeeName;
					}
					return $a_processEmployees;
				}
				else
				{
					return false;
				}
			}
		}

		//Function get customer all pdf qa employees
		function getCustomerAllQaEmployees($customerId)
		{
			$query	=	"SELECT pdf_clients_employees.employeeId,firstName,lastName FROM pdf_clients_employees INNER JOIN employee_details ON pdf_clients_employees.employeeId=employee_details.employeeId WHERE customerId=$customerId AND hasQaAccess=1 AND isActive=1 ORDER BY firstName";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$a_qaEmployees		=	array();
				while($row			=	mysqli_fetch_assoc($result))
				{
					$employeeId		=	$row['employeeId'];
					$firstName		=	stripslashes($row['firstName']);
					$lastName		=	stripslashes($row['lastName']);

					$employeeName	=	$firstName." ".$lastName;
					$employeeName	=	ucwords($employeeName);

					$a_qaEmployees[$employeeId]	=	$employeeName;
				}
				return $a_qaEmployees;
			}
			else
			{
				$query	=	"SELECT employeeId,firstName,lastName FROM employee_details WHERE isManager=1 AND isActive=1 ORDER BY firstName";
				$result	=	dbQuery($query);
				if(mysqli_num_rows($result))
				{
					$a_qaEmployees			=	array();
					while($row			=	mysqli_fetch_assoc($result))
					{
						$employeeId		=	$row['employeeId'];
						$firstName		=	stripslashes($row['firstName']);
						$lastName		=	stripslashes($row['lastName']);

						$employeeName	=	$firstName." ".$lastName;
						$employeeName	=	ucwords($employeeName);

						$a_qaEmployees[$employeeId]	=	$employeeName;
					}
					return $a_qaEmployees;
				}
				else
				{
					return false;
				}
			}
		}
		//Function to get AA Order ID
		function getAAOrderID($orderId)
		{
			$query	=	"SELECT * FROM members_orders WHERE orderId=$orderId";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row				=	mysqli_fetch_assoc($result);
				$memberId			=	$row['memberId'];	
				$orderAddedTime		=	$row['orderAddedTime'];
				$orderAddress		=	stripslashes($row['orderAddress']);

				$orderId			=	strrev($orderId);
				
				list($h,$i,$s)		=	explode(":",$orderAddedTime);

				$aaID				=	$memberId."-".$orderId."-".$h;


				return $aaID;
			}
			else
			{
				return false;
			}
		}
		//Function to get single employee total order accepted
		function getSingleEmployeeCompletedOrders($acceptedBy,$memberId=0)
		{
			$andClause		=	"";
			if(!empty($memberId))
			{
				$andClause	=	" AND memberId=$memberId";
			}

			$totalOrders	=	$this->getSingleQueryResult("SELECT COUNT(*) as total FROM members_orders WHERE acceptedBy=$acceptedBy AND status IN (2,5,6)".$andClause,"total");
			if(empty($totalOrders))
			{
				$totalOrders=	0;
			}

			return $totalOrders;
		}

		//Function to get client fised rate
		function isClientFixedRateExists($platform,$customerId,$rateValidFrom="")
		{
			$andClause			=	"";
			if($rateValidFrom  !=   "")
			{
				$andClause		=	" AND rateValidFrom >= '$rateValidFrom' AND rateValidTo <= $rateValidFrom";
			}
			
			$query			=	"SELECT * FROM mt_clients_fixed_money WHERE isActiveRate=1 AND isDeletedRate=0 AND platform=$platform AND customerId=$customerId";
			$result			=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}

		//Function to add increasing percentage of employee
		function updateIncrementedPercentageMoney($directTranscriptionHike,$indirectTranscriptionHike,$directVreHike,$indirectVreHike,$directQaHike,$indirectQaHike,$directAuditHike,$indirectAuditHike,$employeeId)
		{
			$query				=	"SELECT * FROM employee_line_rates WHERE employeeId=$employeeId AND isActiveRate=1 ORDER BY employeeRateId DESC LIMIT 1";
			$result				=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row							=	mysqli_fetch_assoc($result);

				$employeeRateId					=	$row['employeeRateId'];
				$directTranscriptionRate		=	$row['directTranscriptionRate'];
				$indirectTranscriptionRate		=	$row['indirectTranscriptionRate'];
				$directVreRate					=	$row['directVreRate'];
				$indirectVreRate				=	$row['indirectVreRate'];
				$directQaRate					=	$row['directQaRate'];
				$indirectQaRate					=	$row['indirectQaRate'];
				$directAuditRate				=	$row['directAuditRate'];
				$indirectAuditRate				=	$row['indirectAuditRate'];


				$directLevel1Rate				=	$row['directLevel1Rate'];
				$directLevel2Rate				=	$row['directLevel2Rate'];
				$indirectLevel1Rate				=	$row['indirectLevel1Rate'];
				$indirectLevel2Rate				=	$row['indirectLevel2Rate'];
				$qaLevel1Rate					=	$row['qaLevel1Rate'];
				$qaLevel2Rate					=	$row['qaLevel2Rate'];
				$auditLevel1Rate				=	$row['auditLevel1Rate'];
				$auditLevel2Rate				=	$row['auditLevel2Rate'];

				if(!empty($directTranscriptionRate) && !empty($directTranscriptionHike))
				{
					$newDTrate					=	$directTranscriptionHike/100;
					$newDTrate					=	$newDTrate*$directTranscriptionRate;
					$newDTrate					=	$directTranscriptionRate+$newDTrate;
					$directTranscriptionRate	=	round($newDTrate,2);
				}
				if(!empty($indirectTranscriptionRate) && !empty($indirectTranscriptionHike))
				{
					$newIDTrate					=	$indirectTranscriptionHike/100;
					$newIDTrate					=	$newIDTrate*$indirectTranscriptionRate;
					$newIDTrate					=	$indirectTranscriptionRate+$newIDTrate;
					$indirectTranscriptionRate	=	round($newIDTrate,2);
				}
				if(!empty($directVreRate) && !empty($directVreHike))
				{
					$newVRErate					=	$directVreHike/100;
					$newVRErate					=	$newVRErate*$directVreRate;
					$newVRErate					=	$directVreRate+$newVRErate;
					$directVreRate				=	round($newVRErate,2);
				}
				if(!empty($indirectVreRate) && !empty($indirectVreHike))
				{
					$newIVRErate				=	$indirectVreHike/100;
					$newIVRErate				=	$newIVRErate*$indirectVreRate;
					$newIVRErate				=	$indirectVreRate+$newIVRErate;
					$indirectVreRate			=	round($newIVRErate,2);
				}
				if(!empty($directQaRate) && !empty($directQaHike))
				{
					$newQArate					=	$indirectVreHike/100;
					$newQArate					=	$newQArate*$directQaRate;
					$newQArate					=	$directQaRate+$newQArate;
					$directQaRate				=	round($newQArate,2);
				}
				if(!empty($indirectQaRate) && !empty($indirectQaHike))
				{
					$newIQArate					=	$indirectQaHike/100;
					$newIQArate					=	$newIQArate*$indirectQaRate;
					$newIQArate					=	$indirectQaRate+$newIQArate;
					$indirectQaRate				=	round($newIQArate,2);
				}
				if(!empty($directAuditRate) && !empty($directAuditHike))
				{
					$newAUDrate					=	$directAuditHike/100;
					$newAUDrate					=	$newAUDrate*$directAuditRate;
					$newAUDrate					=	$directAuditRate+$newAUDrate;
					$directAuditRate			=	round($newAUDrate,2);
				}
				if(!empty($indirectAuditRate) && !empty($indirectAuditHike))
				{
					$newIAUDrate				=	$indirectAuditHike/100;
					$newIAUDrate				=	$newIAUDrate*$indirectAuditRate;
					$newIAUDrate				=	$indirectAuditRate+$newIAUDrate;
					$indirectAuditRate			=	round($newIAUDrate,2);
				}

				dbQuery("UPDATE employee_line_rates SET isActiveRate=0,rateValidTo='".CURRENT_DATE_INDIA."' WHERE employeeRateId=$employeeRateId AND employeeId=$employeeId");

				$optionQuery	=	" SET employeeId=$employeeId,directTranscriptionRate='$directTranscriptionRate',indirectTranscriptionRate='$indirectTranscriptionRate',directVreRate='$directVreRate',indirectVreRate='$indirectVreRate',directQaRate='$directQaRate',indirectQaRate='$indirectQaRate',directAuditRate='$directAuditRate',indirectAuditRate='$indirectAuditRate',directLevel1Rate='$directLevel1Rate',directLevel2Rate='$directLevel2Rate',indirectLevel1Rate='$indirectLevel1Rate',indirectLevel2Rate='$indirectLevel2Rate',qaLevel1Rate='$qaLevel1Rate',qaLevel2Rate='$qaLevel2Rate',auditLevel1Rate='$auditLevel1Rate',auditLevel2Rate='$auditLevel2Rate'";

				$query			=	"INSERT INTO employee_line_rates ".$optionQuery.",addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',isActiveRate=1,rateValidFrom='".CURRENT_DATE_INDIA."',rateValidTo='".CURRENT_DATE_INDIA."',isIncrementedPercentage=1,incrementedOn='".CURRENT_DATE_INDIA."',incrementedTime='".CURRENT_DATE_INDIA."'";
				dbQuery($query);
				
				return true;
				

			}
			else
			{
				return false;
			}
		}

		//Function to check employee id type
		function isPDFEmployee($employeeId)
		{
			$isPdfEmployee	=	0;
			if(is_numeric($employeeId))
			{
				$isPdfEmployee		=	$this->getSingleQueryResult("SELECT hasPdfAccess FROM employee_details WHERE employeeId=$employeeId","hasPdfAccess");

				if(empty($isPdfEmployee))
				{
					$isPdfEmployee	=	0;
				}
			}
			return $isPdfEmployee;
		}

		//Function to check employee login type
		function isAllowingOutsideLogin($employeeId)
		{
			$isAllowingOutsideLogin		=	$this->getSingleQueryResult("SELECT hasOutsideLoginAccess FROM employee_details WHERE employeeId=$employeeId","hasOutsideLoginAccess");

			if(empty($isAllowingOutsideLogin))
			{
				$isAllowingOutsideLogin	=	0;
			}

			return $isAllowingOutsideLogin;
		}

		//Function to get employee own security code
		function getEmployeeOwnSecurityCode($employeeId)
		{
			$securityCode		=	$this->getSingleQueryResult("SELECT securityCode FROM employee_details WHERE employeeId=$employeeId","securityCode");


			if(empty($securityCode))
			{
				$securityCode	=	"";
			}

			return $securityCode;
		}
		//Function to track fail login
		function trackFailEmployeeLogin($employeeId=0,$email,$passwordEntered,$securityTokenEntered,$errorMsg,$errorFromIp,$isPdfEmployee,$ipCity,$ipRegion,$ipCountry,$ipISP)
		{
			$ipCity	    =	makeDBSafe($ipCity);
			$ipRegion	=	makeDBSafe($ipRegion);
			$ipCountry	=	makeDBSafe($ipCountry);
			$errorFromIp=	makeDBSafe($errorFromIp);
			$ipISP      =	makeDBSafe($ipISP);

			dbQuery("INSERT INTO track_employee_customers_failed_login SET employeeId=$employeeId,email='$email',passwordEntered='$passwordEntered',securityTokenEntered ='$securityTokenEntered ',errorMsg='$errorMsg',errorFromIp='$errorFromIp',isPdfEmployee=$isPdfEmployee,date='".CURRENT_DATE_INDIA."',time='".CURRENT_TIME_INDIA."',estDate='".CURRENT_DATE_CUSTOMER_ZONE."',estTime='".CURRENT_TIME_CUSTOMER_ZONE."',ipCity='$ipCity',ipRegion='$ipRegion',ipCountry='$ipCountry',ipISP='$ipISP'");

			return true;
		}

		//Function to track failed security code
		function trackEmployeePasswordSecurityTracking($employeeId,$isRequestForSecurityToken=0,$isRequestForPassword=0,$requestMadeThroughEmail=0,$requestMadeThroughId=0,$email,$requestIp,$requestIpCity,$requestIpRegion,$requestIpZipCode,$requestIpCountry,$requestIpIsp)
		{
			$requestIp			=	makeDBSafe($requestIp);
			$requestIpCity		=	makeDBSafe($requestIpCity);
			$requestIpRegion	=	makeDBSafe($requestIpRegion);
			$requestIpZipCode   =   makeDBSafe($requestIpZipCode);
			$requestIpCountry   =   makeDBSafe($requestIpCountry);
			$requestIpIsp		=	makeDBSafe($requestIpIsp);
			
			dbQuery("INSERT INTO request_for_security_forgot_password SET employeeId=$employeeId,email='$email',isRequestForSecurityToken=$isRequestForSecurityToken,isRequestForPassword=$isRequestForPassword,requestMadeThroughEmail=$requestMadeThroughEmail,requestMadeThroughId=$requestMadeThroughId,requestDate='".CURRENT_DATE_INDIA."',requestTime='".CURRENT_TIME_INDIA."',requestEstDate='".CURRENT_DATE_CUSTOMER_ZONE."',requestEstTime='".CURRENT_TIME_CUSTOMER_ZONE."',requestIp='$requestIp',requestIpCity='$requestIpCity',requestIpRegion='$requestIpRegion',requestIpCountry='$requestIpCountry',requestIpIsp='$requestIpIsp',requestIpZipCode='$requestIpZipCode'");

			return true;
		}

		//Function to get accepted by total employee
		function isExistTotalCustomerOrdersAccepted($memberId,$employeeId)
		{
			$doneId		=	$this->getSingleQueryResult("SELECT doneId FROM customers_total_orders_done_by WHERE memberId=$memberId AND employeeId=$employeeId AND totalAccepted <> 0","doneId");

			if(empty($doneId))
			{
				$doneId	=	0;
			}

			return $doneId;
		}
		//Get all mt employees with mobile number
		function getAllMtEmployeesWithMobile($type=0)
		{
			$a_employees	=	array();
			$table			=	" employee_details";
			$column			=	"employeeId";
			$andCaluse		=	"";
			if($type		==	1)//MT EMPLOYEES
			{
				$table		=	" employee_shift_rates INNER JOIN employee_details ON employee_shift_rates.employeeId=employee_details.employeeId";
				$column		=	"employee_shift_rates.employeeId";
				$andCaluse	=	" AND departmentId=1";
			}
			elseif($type	==	3)//PDF EMPLOYEES
			{
				$andCaluse	=	" AND hasPdfAccess=1";
			}
			
			$query					=	"SELECT ".$column.",fullName,mobile FROM".$table." WHERE employee_details.isActive=1".$andCaluse." ORDER BY firstName";
			$result					=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row			=	mysqli_fetch_assoc($result))
				{
					$t_employeeId	=	$row['employeeId'];
					$fullName		=	stripslashes($row['fullName']);
					$mobile			=	stripslashes($row['mobile']);

					$a_employees[$t_employeeId]	=	$fullName."<=>".$mobile;
				}	
				return $a_employees;
			}
			else
			{
				return false;
			}
		}
		//Function to add employee message as SMS
		function addEmployeeMessageSms($cancelled,$smsReferenceID,$toEmployeeId,$fromEmployeeId,$smsMessageID,$queued,$smsError,$smsMessage,$smsEmployeeMobileNo)
		{
			global $db_conn;
			
			dbQuery("INSERT INTO employee_messages_sms SET cancelled='$cancelled',toEmployeeId=$toEmployeeId,fromEmployeeId=$fromEmployeeId,smsMessageID='$smsMessageID',queued='$queued',smsError='$smsError',smsMesseSent='$smsMessage',sentSmsToPhone='$smsEmployeeMobileNo',sentDate='".CURRENT_DATE_INDIA."',sentTime='".CURRENT_TIME_INDIA."',sendingFromIP='".VISITOR_IP_ADDRESS."',smsReferenceID='$smsReferenceID'");

			$newSmsID	=	mysqli_insert_id($db_conn);
			
			return $newSmsID;
		}
		//Function to get has employee QA Access
		function hasEmployeeQaAccess($employeeId)
		{
			return	$this->getSingleQueryResult("SELECT hasQaDoneAccess FROM employee_details WHERE employeeId=$employeeId","hasQaDoneAccess");
		}

		//Function to get when email is read first
		function getFirstEmailReadTime($emailUniqueCode)
		{
			$query		=	"SELECT * FROM tracking_email_read WHERE uniqueCode='$emailUniqueCode' ORDER BY emailReadDate LIMIT 1";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row			=	mysqli_fetch_assoc($result);
				$emailReadDate	=	$row['emailReadDate'];
				$emailReadTime	=	$row['emailReadTime'];

				return $emailReadDate."|".$emailReadTime;
			}
			else
			{
				return false;
			}
		}

		//Update employee attdenance track table
		function updateEmployeeAttendanceTracking($employeeId,$attendanceMarkedAs,$employeeName,$totalDaysInMonth,$todaysDay,$forMonth,$forYear,$isAHalfDay,$absentPresent)
		{
			$a_monthDateText			=	array();
			$a_monthDateText[1]			=	"1st";
			$a_monthDateText[2]			=	"2nd";
			$a_monthDateText[3]			=	"3rd";
			$a_monthDateText[4]			=	"4th";
			$a_monthDateText[5]			=	"5th";
			$a_monthDateText[6]			=	"6th";
			$a_monthDateText[7]			=	"7th";
			$a_monthDateText[8]			=	"8th";
			$a_monthDateText[9]			=	"9th";
			$a_monthDateText[10]		=	"10th";
			$a_monthDateText[11]		=	"11th";
			$a_monthDateText[12]		=	"12th";
			$a_monthDateText[13]		=	"13th";
			$a_monthDateText[14]		=	"14th";
			$a_monthDateText[15]		=	"15th";
			$a_monthDateText[16]		=	"16th";
			$a_monthDateText[17]		=	"17th";
			$a_monthDateText[18]		=	"18th";
			$a_monthDateText[19]		=	"19th";
			$a_monthDateText[20]		=	"20th";
			$a_monthDateText[21]		=	"21st";
			$a_monthDateText[22]		=	"22nd";
			$a_monthDateText[23]		=	"23rd";
			$a_monthDateText[24]		=	"24th";
			$a_monthDateText[25]		=	"25th";
			$a_monthDateText[26]		=	"26th";
			$a_monthDateText[27]		=	"27th";
			$a_monthDateText[28]		=	"28th";
			$a_monthDateText[29]		=	"29th";
			$a_monthDateText[30]		=	"30th";
			$a_monthDateText[31]		=	"31st";
			

			$nonLeadingZeroDay			=	$todaysDay;
			if($todaysDay < 10 && strlen($todaysDay) > 1)
			{
				$nonLeadingZeroDay		=	substr($todaysDay,1);
			}
			$column						=	$a_monthDateText[$nonLeadingZeroDay];
			
			
			$t_month					=	$forMonth;
			if($forMonth < 10)
			{
				if(strlen($forMonth) > 1)
				{
					$forMonth			=	substr($forMonth,1);
				}
				else
				{
					$t_month			=	"0".$forMonth;
				}
			}

			$todaysDate					=	$forYear."-".$t_month."-".$todaysDay;

			$absentPresentColsInsert	=	"";
			$absentPresentColsUpdate	=	"";
			if($absentPresent			==	1)
			{
				$absentPresentColsInsert=	",totalPresent=1";
				$absentPresentColsUpdate=	",totalPresent=totalPresent+1";
			}
			elseif($absentPresent		==	2 && empty($isAHalfDay))
			{
				$absentPresentColsInsert=	",totalAbsent=1";
				$absentPresentColsUpdate=	",totalAbsent=totalAbsent+1";
			}

			$isHavingRecrd		=	$this->getSingleQueryResult("SELECT employeeId FROM track_daily_employee_attendance WHERE employeeId=$employeeId AND forMonth=$forMonth AND forYear=$forYear","employeeId");
			if(empty($isHavingRecrd))
			{
				$employeeName	=	makeDBSafe($employeeName);
				$query			=	"INSERT INTO track_daily_employee_attendance SET employeeId=$employeeId,employeeName='$employeeName',".$column."=".$attendanceMarkedAs.$absentPresentColsInsert.",totalDaysInMonth=$totalDaysInMonth,forMonth=$forMonth,forYear=$forYear,totalHalfDays=$isAHalfDay";
			}
			else
			{
				$query			=	"UPDATE track_daily_employee_attendance SET ".$column."=".$attendanceMarkedAs.$absentPresentColsUpdate.",totalHalfDays=totalHalfDays+$isAHalfDay WHERE employeeId=$employeeId AND forMonth=$forMonth AND forYear=$forYear";
			}
			dbQuery($query);

			$previousDayDate	=	getPreviousGivenDate($todaysDate,1);

			list($pY,$pM,$pD)	=	explode("-",$previousDayDate);

			$isLoginYesterDay	=	$this->getSingleQueryResult("SELECT attendenceId FROM employee_attendence WHERE employeeId=$employeeId AND loginDate='$previousDayDate'","attendenceId");

			if(empty($isLoginYesterDay))
			{
				$sundayText		=    date("l",strtotime($pY."-".$pM."-".$pD));
				if($sundayText	==   "Sunday")
				{
					if($pM < 10 && strlen($pM) > 1)
					{
						$pM		=	substr($pM,1);
					}
					if($pD < 10 && strlen($pD) > 1)
					{
						$pD		=	substr($pD,1);
					}

					$updateColumn=	$a_monthDateText[$pD];

					dbQuery("UPDATE track_daily_employee_attendance SET ".$updateColumn."=5 WHERE employeeId=$employeeId AND forMonth=$pM AND forYear=$pY");
				}
			}

			return true;
		}

		//Function to calculate total overtime in a month
		function addingOvertimeAMonth($employeeId,$overtimeHrs,$month,$year)
		{
			$t_month			=	$forMonth;
			if($forMonth < 10)
			{
				if(strlen($forMonth) < 1)
				{
					$forMonth	=	substr($forMonth,1);
				}
				else
				{
					$t_month	=	"0".$forMonth;
				}
			}

			dbQuery("UPDATE track_daily_employee_attendance SET totalOvertime=totalOvertime+$overtimeHrs WHERE employeeId=$employeeId AND forMonth=$month AND forYear=$year");

			return true;
		}

		//Update employee attdenance total absent half
		function updateEmployeePresentAbsent($employeeId,$forMonth,$forYear)
		{
			$a_monthDateText			=	array();
			$a_monthDateText[1]			=	"1st";
			$a_monthDateText[2]			=	"2nd";
			$a_monthDateText[3]			=	"3rd";
			$a_monthDateText[4]			=	"4th";
			$a_monthDateText[5]			=	"5th";
			$a_monthDateText[6]			=	"6th";
			$a_monthDateText[7]			=	"7th";
			$a_monthDateText[8]			=	"8th";
			$a_monthDateText[9]			=	"9th";
			$a_monthDateText[10]		=	"10th";
			$a_monthDateText[11]		=	"11th";
			$a_monthDateText[12]		=	"12th";
			$a_monthDateText[13]		=	"13th";
			$a_monthDateText[14]		=	"14th";
			$a_monthDateText[15]		=	"15th";
			$a_monthDateText[16]		=	"16th";
			$a_monthDateText[17]		=	"17th";
			$a_monthDateText[18]		=	"18th";
			$a_monthDateText[19]		=	"19th";
			$a_monthDateText[20]		=	"20th";
			$a_monthDateText[21]		=	"21st";
			$a_monthDateText[22]		=	"22nd";
			$a_monthDateText[23]		=	"23rd";
			$a_monthDateText[24]		=	"24th";
			$a_monthDateText[25]		=	"25th";
			$a_monthDateText[26]		=	"26th";
			$a_monthDateText[27]		=	"27th";
			$a_monthDateText[28]		=	"28th";
			$a_monthDateText[29]		=	"29th";
			$a_monthDateText[30]		=	"30th";
			$a_monthDateText[31]		=	"31st";

			$query						=	"SELECT * FROM track_daily_employee_attendance WHERE employeeId=$employeeId AND forMonth=$forMonth AND forYear=$forYear";
			$result						=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row					=	mysqli_fetch_assoc($result);
				$totalDaysInMonth		=	$row['totalDaysInMonth'];

				$totalPresent			=	0;
				$totalHalfDays			=	0;
				$totalAbsent			=	0;

				foreach($a_monthDateText as $kk1=>$vv1)
				{
					if($kk1 > $totalDaysInMonth)
					{
						break;
					}

					$value				=	$row[$vv1];
					
					if($value			==	1)
					{
						$totalPresent	=	$totalPresent+1;
					}
					elseif($value		==	2)
					{
						$totalHalfDays	=	$totalHalfDays+1;
					}
					elseif($value		==	3)
					{
						$totalAbsent	=	$totalAbsent+1;
					}
				}
				

				$query1		=	"UPDATE track_daily_employee_attendance SET totalPresent=$totalPresent,totalHalfDays=$totalHalfDays,totalAbsent=$totalAbsent WHERE employeeId=$employeeId AND forMonth=$forMonth AND forYear=$forYear";
				dbQuery($query1);
			}
			return true;
		}

		//Function to marked target order processed
		function makeTargetOrderProcessedQa($employeeId,$employeeName,$targetMonth,$targetYear,$isProcessedQa)
		{
			$column						=	"processedDone";//1 is for processed order
			if($isProcessedQa			==	2)
			{
				$column					=	"qaDone";//2 is for qa order
			}

			
			$nonLeadingZeroMonth		=	$targetMonth;
			if($targetMonth < 10 && strlen($targetMonth) > 1)
			{
				$nonLeadingZeroMonth	=	substr($targetMonth,1);
			}

			$this->updateNewTargetWithOld($nonLeadingZeroMonth,$targetMonth,$targetYear);

			$isExistsTarget		=	$this->getSingleQueryResult("SELECT employeeId FROM employee_target WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$targetYear AND employeeId=$employeeId","employeeId");

			if(!empty($column) && ($column == "processedDone" || $column == "qaDone"))
			{
				if(!empty($isExistsTarget))
				{
					$query	=	"UPDATE employee_target SET $column=$column+1 WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$targetYear AND employeeId=$employeeId";
					dbQuery($query);
				}
				else
				{
					$query	=	"INSERT INTO employee_target SET employeeId=$employeeId,employeeName='$employeeName',$column=1,targetMonth=$nonLeadingZeroMonth,targetYear=$targetYear";
					dbQuery($query);
				}

			}

			return true;
		}

		//Interchange employee target 
		function pdfEmployeeTargetInterchange($fromemployee,$toEmployee,$ratingColumn,$targetMonth,$targetYear,$isProcessedQa)
		{
			$column						=	"processedDone";//1 is for processed order
			if($isProcessedQa			==	2)
			{
				$column					=	"qaDone";//2 is for qa order
			}

			
			$nonLeadingZeroMonth		=	$targetMonth;
			if($targetMonth < 10 && strlen($targetMonth) > 1)
			{
				$nonLeadingZeroMonth	=	substr($targetMonth,1);
			}

			$query						=	"SELECT * FROM employee_target WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$targetYear AND employeeId=$fromemployee";
			$result						=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row					=	mysqli_fetch_assoc($result);
				$targetId				=	$row['targetId'];
				$reducedcolumn			=	$row[$column];
				$ratingColumnVal		=	$row[$ratingColumn];

				if(!empty($reducedcolumn) && !empty($column))
				{
					dbQuery("UPDATE employee_target SET $column=$column-1 WHERE targetId=$targetId AND employeeId=$fromemployee");
				}
				if(!empty($ratingColumnVal) && !empty($ratingColumn))
				{
					dbQuery("UPDATE employee_target SET $ratingColumn=$ratingColumn-1 WHERE targetId=$targetId AND employeeId=$fromemployee");
				}

				$toEmployeeName	=	$this->getEmployeeName($toEmployee);

				$this->makeTargetOrderProcessedQa($toEmployee,$toEmployeeName,$targetMonth,$targetYear,$isProcessedQa);
			}
			return true;
		}

		//Function to update new target with old one
		function updateNewTargetWithOld($nonLeadingZeroMonth,$targetMonth,$targetYear)
		{
			$targetId = $this->getSingleQueryResult("SELECT targetId FROM employee_target WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$targetYear ORDER BY targetId DESC LIMIT 1","targetId");

			if(empty($targetId))
			{
				$query=	"SELECT targetMonth,targetYear FROM employee_target ORDER BY targetId DESC LIMIT 1";
				$result				=	dbQuery($query);
				if(mysqli_num_rows($result))
				{
					$row			=	mysqli_fetch_assoc($result);
					$targetMonth1	=	$row['targetMonth'];
					$targetYear1	=	$row['targetYear'];

					$query1			=	"SELECT employeeId,employeeName,processedTarget,qaTarget FROM employee_target WHERE targetMonth=$targetMonth1 AND targetYear=$targetYear1 ORDER BY targetId";
					$result1		=	dbQuery($query1);
					if(mysqli_num_rows($result1))
					{
						while($row1	=	mysqli_fetch_assoc($result1))
						{
							$employeeId		=	$row1['employeeId'];
							$employeeName	=	stripslashes($row1['employeeName']);
							$processedTarget=	stripslashes($row1['processedTarget']);
							$qaTarget		=	stripslashes($row1['qaTarget']);

							$t_employeeName	=	makeDBSafe($employeeName);

							$query11		=	"INSERT INTO employee_target SET employeeId=$employeeId,employeeName='$t_employeeName',processedTarget=$processedTarget,qaTarget=$qaTarget,targetMonth=$nonLeadingZeroMonth,targetYear=$targetYear,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',adddedFromIP='".VISITOR_IP_ADDRESS."'";
							dbQuery($query11);
						}
					}

				}
			}
			return true;
		}
		//Function to add edit lines counts
		function addMtEmployeeTargetLines($employeeId,$employeeName,$targetMonth,$targetYear,$achieved,$isEdit=0,$totalOldLines=0)
		{
			
			$nonLeadingZeroMonth		=	$targetMonth;
			if($targetMonth < 10 && strlen($targetMonth) > 1)
			{
				$nonLeadingZeroMonth	=	substr($targetMonth,1);
			}

			$this->updateNewMtTargetWithOld($nonLeadingZeroMonth,$targetMonth,$targetYear);

			$isExistsTarget		=	$this->getSingleQueryResult("SELECT employeeId FROM mt_employee_target  WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$targetYear AND employeeId=$employeeId","employeeId");

			if(!empty($isExistsTarget))
			{
				$isExistsOldAchieved=	$this->getSingleQueryResult("SELECT targetAchieved FROM mt_employee_target WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$targetYear AND employeeId=$employeeId","targetAchieved");

				if($isEdit			==	1 && !empty($totalOldLines) && !empty($isExistsOldAchieved))
				{
					if($isExistsOldAchieved > $totalOldLines)
					{						
						dbQuery("UPDATE mt_employee_target SET targetAchieved=targetAchieved-$totalOldLines WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$targetYear AND employeeId=$employeeId");
					}
					else
					{
						dbQuery("UPDATE mt_employee_target SET targetAchieved=0 WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$targetYear AND employeeId=$employeeId");
					}
				}
				$query				=	"UPDATE mt_employee_target SET targetAchieved=targetAchieved+$achieved WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$targetYear AND employeeId=$employeeId";
				dbQuery($query);
			}
			else
			{
				$query			=	"INSERT INTO mt_employee_target SET employeeId=$employeeId,employeeName='$employeeName',targetAchieved=$achieved,targetMonth=$nonLeadingZeroMonth,targetYear=$targetYear";
				dbQuery($query);
			}			

			return true;
		}

		//Function to update new MT target with old one
		function updateNewMtTargetWithOld($nonLeadingZeroMonth,$targetMonth,$targetYear)
		{
			$targetId = $this->getSingleQueryResult("SELECT targetId FROM mt_employee_target WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$targetYear ORDER BY targetId DESC LIMIT 1","targetId");
			if(empty($targetId))
			{
				$query=	"SELECT targetMonth,targetYear FROM mt_employee_target ORDER BY targetId DESC LIMIT 1";
				$result				=	dbQuery($query);
				if(mysqli_num_rows($result))
				{
					$row			=	mysqli_fetch_assoc($result);
					$targetMonth1	=	$row['targetMonth'];
					$targetYear1	=	$row['targetYear'];

					$query1			=	"SELECT employeeId,employeeName,processedTarget FROM mt_employee_target WHERE targetMonth=$targetMonth1 AND targetYear=$targetYear1 ORDER BY targetId";
					$result1		=	dbQuery($query1);
					if(mysqli_num_rows($result1))
					{
						while($row1	=	mysqli_fetch_assoc($result1))
						{
							$employeeId		=	$row1['employeeId'];
							$employeeName	=	stripslashes($row1['employeeName']);
							$processedTarget=	stripslashes($row1['processedTarget']);
							
							$t_employeeName	=	makeDBSafe($employeeName);

							$query11		=	"INSERT INTO mt_employee_target SET employeeId=$employeeId,employeeName='$t_employeeName',processedTarget=$processedTarget,targetMonth=$nonLeadingZeroMonth,targetYear=$targetYear,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',addedFromIP='".VISITOR_IP_ADDRESS."'";
							dbQuery($query11);
						}
					}

				}
			}
			return true;
		}
		//Function to delete employee target
		function deleteMtEmployeeTarget($datewiseID)
		{
			$query		=	"SELECT * FROM datewise_employee_works_money WHERE ID=$datewiseID";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row							=	mysqli_fetch_assoc($result);
				$employeeId						=	$row['employeeId'];
				$workedOnDate					=	$row['workedOnDate'];
				$transcriptionLinesEntered		=	$row['totalDirectTrascriptionLines'];
				$vreLinesEntered				=	$row['totalDirectVreLines'];
				$qaLinesEntered					=	$row['totalQaLines'];
				$indirectTranscriptionLinesEntered	=	$row['totalIndirectTrascriptionLines'];
				$indirectVreLinesEntered		=	$row['totalIndirectVreLines'];
				$indirectQaLinesEntered			=	$row['totalIndirectQaLines'];
				$comments						=	stripslashes($row['comments']);

				$auditLinesEntered				=	$row['totalDirectAuditLines'];
				$indirectAuditLinesEntered		=	$row['totalIndirectAuditLines'];

				$totalOldLines					=	$transcriptionLinesEntered+$vreLinesEntered+$qaLinesEntered+$indirectTranscriptionLinesEntered+$indirectVreLinesEntered+$indirectQaLinesEntered+$auditLinesEntered+$indirectAuditLinesEntered;

				list($currentY,$currentM,$currentD)	=	explode("-",$workedOnDate);

				
				$nonLeadingZeroMonth		=	$currentM;
				if($currentM < 10 && strlen($currentM) > 1)
				{
					$nonLeadingZeroMonth	=	substr($currentM,1);
				}

				$isExistsTarget				=	$this->getSingleQueryResult("SELECT employeeId FROM mt_employee_target WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$currentY AND employeeId=$employeeId","employeeId");

				if(!empty($isExistsTarget))
				{
					$isExistsOldAchieved	=	$this->getSingleQueryResult("SELECT targetAchieved FROM mt_employee_target WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$currentY AND employeeId=$employeeIdd","targetAchieved");

					if(!empty($isExistsOldAchieved))
					{
						if($isExistsOldAchieved > $totalOldLines)
						{						
							dbQuery("UPDATE mt_employee_target SET targetAchieved=targetAchieved-$totalOldLines WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$currentY AND employeeId=$employeeId");
						}
						else
						{
							dbQuery("UPDATE mt_employee_target SET targetAchieved=0 WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$currentY AND employeeId=$employeeId");
						}
					}
				}
			}
			return true;
		}

		//Function to change process and qa employee
		function updateOrderChangeEmployees($orderId,$changeAcceptedBy,$changeQaBy)
		{
			$query		=	"SELECT acceptedBy,status,orderAddedOn,rateGiven FROM members_orders WHERE orderId=$orderId AND status <> 0";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row							=	mysqli_fetch_assoc($result);
				$oldOrderAcceptedBy				=	$row['acceptedBy'];
				$orderStatus					=	$row['status'];
				$orderAddedOn					=	$row['orderAddedOn'];
				$rateGiven						=	$row['rateGiven'];

				$peocessRatingColumn			=	"";
				$qaRatingColumn					=	"";
				if(!empty($rateGiven))
				{
					if($rateGiven				==	1)
					{
						$peocessRatingColumn	=	"poorRating";
						$qaRatingColumn			=	"qaPoorRating";
					}
					elseif($rateGiven			==	2)
					{
						$peocessRatingColumn	=	"averageRating";
						$qaRatingColumn			=	"qaAverageRating";
					}
					elseif($rateGiven			==	3)
					{
						$peocessRatingColumn	=	"goodRating";
						$qaRatingColumn			=	"qaGoodRating";
					}
					elseif($rateGiven			==	4)
					{
						$peocessRatingColumn	=	"veryGoodRating";
						$qaRatingColumn			=	"qaVeryGoodRating";
					}
					elseif($rateGiven			==	5)
					{
						$peocessRatingColumn	=	"excellentRating";
						$qaRatingColumn			=	"qaExcellentRating";
					}
				}

				$query1							=	"SELECT replyId,hasQaDone,qaDoneBy,hasRepliedFileUploaded FROM members_orders_reply WHERE orderId=$orderId";
				$result1						=	dbQuery($query1);
				if(mysqli_num_rows($result1))
				{
					$row1						=	mysqli_fetch_assoc($result1);
					$replyId					=	$row1['replyId'];
					$hasQaDone					=	$row1['hasQaDone'];
					$qaDoneBy					=	$row1['qaDoneBy'];
					$hasRepliedFileUploaded		=	$row1['hasRepliedFileUploaded'];

					list($pY,$pM,$pD)			=	explode("-",$orderAddedOn);

					if($hasRepliedFileUploaded	==	1 && $oldOrderAcceptedBy != $changeAcceptedBy)
					{
						//UPDATE PROCESS EMPLOYEE 
						$employeeName			=	$this->getSingleQueryResult("SELECT fullName FROM employee_details WHERE employeeId=$changeAcceptedBy","fullName");
						$employeeName			=	makeDBSafe($employeeName);

						dbQuery("UPDATE members_orders SET employeeId=$changeAcceptedBy,acceptedBy=$changeAcceptedBy,acceeptedByName='$employeeName' WHERE orderId=$orderId");

						$this->pdfEmployeeTargetInterchange($oldOrderAcceptedBy,$changeAcceptedBy,$peocessRatingColumn,$pM,$pY,1);

						if($orderStatus	==	2 || $orderStatus == 5 || $orderStatus == 6)
						{
							dbQuery("UPDATE employee_details SET totalOrderProcessedDone=totalOrderProcessedDone+1 WHERE employeeId=$changeAcceptedBy");

							dbQuery("UPDATE employee_details SET totalOrderProcessedDone=totalOrderProcessedDone-1 WHERE employeeId=$oldOrderAcceptedBy");
						}
					}

					if(($orderStatus	==	2 || $orderStatus == 5 || $orderStatus == 6) && $hasQaDone == 1 && !empty($changeQaBy) && $qaDoneBy != $changeQaBy)
					{
						//UPDATE QA EMPLOYEE 
						$employeeName			=	$this->getSingleQueryResult("SELECT fullName FROM employee_details WHERE employeeId=$changeQaBy","fullName");
						$employeeName			=	makeDBSafe($employeeName);

						dbQuery("UPDATE members_orders_reply SET qaDoneBy=$changeQaBy,employeeId=$changeQaBy WHERE orderId=$orderId");

						dbQuery("UPDATE members_orders SET qaDoneById=$changeQaBy,qaDoneByName='$employeeName' WHERE orderId=$orderId");

						$this->pdfEmployeeTargetInterchange($qaDoneBy,$changeQaBy,$qaRatingColumn,$pM,$pY,2);

						dbQuery("UPDATE employee_details SET totalOrderQaDone=totalOrderQaDone+1 WHERE employeeId=$changeQaBy");

						dbQuery("UPDATE employee_details SET totalOrderQaDone=totalOrderQaDone-1 WHERE employeeId=$qaDoneBy");
					}
				}

			}
			return true;
		}
		//Update PDF employee total target order checked
		function updateEmployeeTotalChecked($employeeId,$employeeName,$date)
		{
			list($targetYear,$targetMonth,$targetDay)	=	explode("-",$date);

			$nonLeadingZeroMonth		=	$targetMonth;
			if($targetMonth < 10 &&     strlen($targetMonth) > 1)
			{
				$nonLeadingZeroMonth	=	substr($targetMonth,1);
			}
			
			//$this->updateNewTargetWithOld($nonLeadingZeroMonth,$targetMonth,$targetYear);

			$isExistsTarget				=	$this->getSingleQueryResult("SELECT employeeId FROM employee_target WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$targetYear AND employeeId=$employeeId","employeeId");

			if(!empty($isExistsTarget))
			{
				$query	=	"UPDATE employee_target SET totalCheckedOrders=totalCheckedOrders+1 WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$targetYear AND employeeId=$employeeId";
				dbQuery($query);
			}
			else
			{
				$query	=	"INSERT INTO employee_target SET employeeId=$employeeId,employeeName='$employeeName',totalCheckedOrders=totalCheckedOrders+1,targetMonth=$nonLeadingZeroMonth,targetYear=$targetYear";
				dbQuery($query);
			}
			return true;
		}
		//Function to get MT employee excel update data
		function getMtEmployeeeExcelData($employeeId,$month,$year)
		{
			$nonLeadingZeroMonth		=	$month;
			if($month < 10 && strlen($month) > 1)
			{
				$nonLeadingZeroMonth	=	substr($month,1);
			}	

			$query		=	"SELECT * FROM mt_employee_excel_csv_data WHERE employeeId=$employeeId AND month=$nonLeadingZeroMonth AND year=$year";
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}

		//Function to get an mt employee employee details
		function getActiveDeactiveEmployeeDetails($employeeId)
		{
			$query	=	"SELECT * FROM employee_details WHERE employeeId=$employeeId";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				return $result;
			}
			else
			{
				return false;
			}
		}

		//////Fuction to get single RESULT //////
		function getSingleQueryResult($query,$param){
			
			$retrnResult	=	"";
			$result			=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row		=	mysqli_fetch_assoc($result);
				$retrnResult=   $row[$param];
			}
			return $retrnResult;
		}

		//////Fuction to get all inactive users //////
		function getAllInactiveEmployees(){
			
			$a_allEmployees 	=	array();

			$query 	=	"SELECT employeeId,fullName FROM employee_details WHERE isActive=0 AND hasPdfAccess=1 ORDER BY employeeId";
			$result =	dbQuery($query);
			if(mysqli_num_rows($result)){
				while($row = mysqli_fetch_assoc($result)){
					$fullName 	=	stripslashes($row['fullName']);
					$employeeId =	stripslashes($row['employeeId']);

					$a_allEmployees[$employeeId] = $fullName;
				}
			}
			return $a_allEmployees;
		}

		//////Fuction to get all employee profile files //////
		function getEmployeeProfileFiles($employeeId){
			
			$a_allFiles 	=	array();

			$query       	=	"SELECT type,fileServerPath FROM employeee_profile_files WHERE employeeId=$employeeId ORDER BY fileId";
			$result =	dbQuery($query);
			if(mysqli_num_rows($result)){
				while($row          =   mysqli_fetch_assoc($result)){
					$type 	        =	stripslashes($row['type']);
					$fileServerPath =	stripslashes($row['fileServerPath']);

					$a_allFiles[$type] = $fileServerPath;
				}
			}
			return $a_allFiles;
		}

		//Optimized function to get monthly employee work statistics
		function getOptimizedMonthlyEmployeeWorkStats($month, $year)
		{
			// Calculate date range for better index usage instead of MONTH() and YEAR() functions
			$startDate = sprintf('%04d-%02d-01', $year, $month);
			$endDate = date('Y-m-t', strtotime($startDate)); // Last day of the month
			
			// Single query with conditional aggregation to get all statistics at once
			$query = "SELECT 
						members_orders.acceptedBy,
						employee_details.fullName,
						COUNT(*) AS totalFiles,
						SUM(CASE WHEN members_orders.status IN (2,4,5) AND isRushOrder=1 THEN 1 ELSE 0 END) AS rushOrders,
						SUM(CASE WHEN members_orders.status IN (2,4,5) AND isRushOrder=0 THEN 1 ELSE 0 END) AS orders12Hours,
						SUM(CASE WHEN members_orders.status IN (2,4,5) AND isRushOrder=2 THEN 1 ELSE 0 END) AS orders24Hours,
						SUM(CASE WHEN rateGiven=1 THEN 1 ELSE 0 END) AS awfulOrders,
						SUM(CASE WHEN rateGiven=2 THEN 1 ELSE 0 END) AS poorOrders
					FROM members_orders 
					INNER JOIN employee_details ON members_orders.acceptedBy=employee_details.employeeId 
					WHERE assignToEmployee >= '$startDate' 
						AND assignToEmployee <= '$endDate 23:59:59'
					GROUP BY members_orders.acceptedBy, employee_details.fullName
					ORDER BY employee_details.fullName";
			
			$result = dbQuery($query);
			$employeeStats = array();
			
			if($result && mysqli_num_rows($result))
			{
				while($row = mysqli_fetch_assoc($result))
				{
					$employeeId = $row['acceptedBy'];
					$employeeStats[$employeeId] = array(
						'fullName' => stripslashes($row['fullName']),
						'totalFiles' => $row['totalFiles'],
						'rushOrders' => $row['rushOrders'],
						'orders12Hours' => $row['orders12Hours'],
						'orders24Hours' => $row['orders24Hours'],
						'awfulOrders' => $row['awfulOrders'],
						'poorOrders' => $row['poorOrders']
					);
				}
			}
			
			return $employeeStats;
		}

	}
?>