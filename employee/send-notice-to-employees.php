<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	include(SITE_ROOT			. "/includes/send-mail.php");
	$pagingObj					=	new Paging();
	$employeeObj				=	new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");

	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$display			=	"none";
	$display1			=	"";
	$display2			=	"none";
	$display3			=	"none";
	$employee_lists		=	$employeeObj->getAllMtEmployees();
	$department			=	1;//MT Department
	if(!empty($s_hasPdfAccess))
	{
		$department		=	3;
		$display		=	"none";
		$display1		=	"none";
		$display2		=	"none";
		$display3		=	"";
		$employee_lists	=	$employeeObj->getAllPdfEmployees();
	}

	$manager			=	0;
	$departmentText     =   $a_newDepartment[$department];

	$a_managers			=	$employeeObj->getAllEmployeeManager($departmentText);

	$messageId			=	0;
	$message			=	"";
	$title				=	"";
	$fromDate			=	date("d-m-Y");
	$toDate				=	date("d-m-Y");
	$t_fromDate			=	"0000-00-00";
	$t_toDate			=	"0000-00-00";
	$employeeId			=	0;
	$errorMsg			=	"";
	$whereClause		=	"WHERE departmentId=$department";
	$orderBy			=	"messageId DESC";
	$queryString		=	"";

	$departmentId		=	0;
	
	$checkedAll			=	"checked";
	$mailSend			=	false;
	$a_employeeId		=	array();
	$joiningQuery		=	"";
	$employeeGroupId	=	0;
	$a_allExistingEmployees	=	$employeeObj->getAllMtEmployeesWithMobile();
	$a_emailToDepartment=	array("1"=>"MT","3"=>"PDF");
	$msg				=	"[Use Ctrl+Select to select multiple employees]";
	$multiple			=	"multiple";
	$style				=	"style='height:200px;'";

	if(isset($_REQUEST['recNo']))
	{
		$recNo		    =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo	=	0;
	}

	if(isset($_GET['messageId']))
	{
		$messageId		=	(int)$_GET['messageId'];

		$query			=	"SELECT * FROM employee_messages WHERE messageId=$messageId AND departmentId=$department";
		$result			=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$msg			=	"";
			$multiple		=	"";
			$style			=	"";

			$row			=	mysqli_fetch_assoc($result);
			$messageId		=	$row['messageId'];
			$title			=	$row['title'];
			$message		=	$row['message'];
			$t_fromDate		=	$row['displayFrom'];
			$t_toDate		=	$row['displayTo'];
			$employeeId		=	$row['employeeId'];
			$departmentId	=	$row['departmentId'];

			if($departmentId	== 1)
			{
				$display	=	"none";
				$display1	=	"";
				$display2	=	"none";
				$display3	=	"none";
				$checkedAll	=	"";
			}
			elseif($departmentId	== 2)
			{
				$display	=	"none";
				$display1	=	"none";
				$display2	=	"";
				$display3	=	"none";
				$checkedAll	=	"";
			}
			elseif($departmentId	== 3)
			{
				$display	=	"none";
				$display1	=	"none";
				$display2	=	"none";
				$display3	=	"";
				$checkedAll	=	"";
			}
			else
			{
				$checkedAll	=	"checked";
			}
			if(!empty($employeeId))
			{
				$a_employeeId[$employeeId]	=	$employeeId;
			}

			list($year,$month,$day)	=	explode("-",$t_fromDate);
			$fromDate				=	$day."-".$month."-".$year;	

			list($t_year,$t_month,$t_day)	=	explode("-",$t_toDate);
			$toDate				=	$t_day."-".$t_month."-".$t_year;

			if(isset($_GET['isDelete']) && $_GET['isDelete'] == 1)
			{
				dbQuery("DELETE FROM employee_messages WHERE messageId=$messageId");
				
				$link	=	"";
				if($recNo)
				{
					$link	=	"?recNo=".$recNo;
				}
				ob_clean();
				header("Location:".SITE_URL_EMPLOYEES."/send-notice-to-employees.php".$link);
				exit();
			}
		}
	}
	$form		=	SITE_ROOT_EMPLOYEES."/forms/message-to-employees.php"
?>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td colspan="2" class='title5'><b>SEND MESSAGE EMPLOYEES</b></td>
	</tr>
</table>
<?php
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		//pr($_REQUEST);
		//die();
		
		$title		=	trim($title);
		$message	=	trim($message);
		$smsMessage	=	$message;
		$title		=	makeDBSafe($title);
		$message	=	makeDBSafe($message);
		if(isset($_POST['sendEmail']))
		{
			$mailSend	=	true;
		}
		if(isset($_POST['sendSMS']))
		{
			$sendSMS	=	1;
		}
		else
		{
			$sendSMS	=	0;
		}

		if(isset($_POST['employeeId'])  && !empty($departmentId))
		{
			$a_employeeId		=	$_POST['employeeId'];
		}
		/*if(isset($_POST['mtEmployeeId']) && $departmentId == 1)
		{
			$mtEmployeeId		=	$_POST['mtEmployeeId'];
			if(!empty($mtEmployeeId))
			{
				$a_employeeId	=	$mtEmployeeId;
			}
		}
		if(isset($_POST['revEmployeeId']) && $departmentId == 2)
		{
			$revEmployeeId		=	$_POST['revEmployeeId'];
			if(!empty($revEmployeeId))
			{
				$a_employeeId	=	$revEmployeeId;
			}
		}
		if(isset($_POST['pdfEmployeeId']) && $departmentId == 3)
		{
			$pdfEmployeeId		=	$_POST['pdfEmployeeId'];
			if(!empty($pdfEmployeeId))
			{
				$a_employeeId	=	$pdfEmployeeId;
			}
		}*/
		if(empty($departmentId))
		{
			$departmentId		=	0;
			$departmentClause	=	" ";
		}
		else
		{
			if($departmentId		== 3)
			{
				$departmentClause	=	" AND employee_details.hasPdfAccess=1";
			}
			else
			{
				$departmentClause	=	" AND employee_shift_rates.departmentId=$departmentId";
				$joiningQuery		=	" INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId";
			}
		}
		if(empty($title))
		{
			$errorMsg .=	"Please Enter Heading !!<br>";
		}
		if(empty($message))
		{
			$errorMsg .=	"Please Enter Message !!<br>";
		}
		if(empty($errorMsg))
		{
			list($day,$month,$year)	=	explode("-",$fromDate);
			$t_fromDate				=	$year."-".$month."-".$day;	

			list($t_day,$t_month,$t_year)	=	explode("-",$toDate);
			$t_toDate				=	$t_year."-".$t_month."-".$t_day;

			if(empty($a_employeeId))
			{
				$employeeId	=	0;

				$andClause		=	"";
				$t_employeeId	=	0;
				
			}
			else
			{
				if(!in_array("0",$a_employeeId))
				{
					$getEmployee	=	implode(",",$a_employeeId);
					$andClause		=	" AND employee_details.employeeId IN ($getEmployee)";
					$t_employeeId	=	$getEmployee;
				}
				else
				{
					$employeeId	=	0;

					$andClause		=	"";
				}
			}
			if(empty($messageId))
			{
				if(empty($a_employeeId))
				{
					$query		=	"INSERT INTO employee_messages SET title='$title',message='$message',displayFrom='$t_fromDate',displayTo='$t_toDate',addedOn=CURRENT_DATE,employeeId=$employeeId,departmentId=$departmentId,addedByName='$s_employeeName',addedByType='Employee Manager'";
					dbQuery($query);
					$messageId	=	mysqli_insert_id($db_conn);
				}
				else
				{
					if(!in_array("0",$a_employeeId))
					{
						$getEmployee		=	implode(",",$a_employeeId);
						$employeeLength		=	count($a_employeeId);
						if($employeeLength > 1)
						{
							$employeeGroupId	=	substr(md5(rand()+date('s')),0,10);
							foreach($a_employeeId as $key=>$employeeId)
							{
								$query		=	"INSERT INTO employee_messages SET title='$title',message='$message',displayFrom='$t_fromDate',displayTo='$t_toDate',addedOn=CURRENT_DATE,employeeId=$employeeId,departmentId=$departmentId,employeeGroupId='$employeeGroupId',addedByName='$s_employeeName',addedByType='Employee Manager'";
								dbQuery($query);
							}
							$messageId		=	$employeeObj->getSingleQueryResult("SELECT messageId FROM employee_messages WHERE employeeId IN ($getEmployee) AND employeeGroupId='$employeeGroupId' GROUP BY messageId DESC LIMIT 1","messageId");
						}
						else
						{
							$query		=	"INSERT INTO employee_messages SET title='$title',message='$message',displayFrom='$t_fromDate',displayTo='$t_toDate',addedOn=CURRENT_DATE,employeeId=$getEmployee,departmentId=$departmentId,addedByName='$s_employeeName',addedByType='Employee Manager'";
							dbQuery($query);
							$messageId	=	mysqli_insert_id($db_conn);
						}
					}
					else
					{
						$query		=	"INSERT INTO employee_messages SET title='$title',message='$message',displayFrom='$t_fromDate',displayTo='$t_toDate',addedOn=CURRENT_DATE,employeeId=$employeeId,departmentId=$departmentId,addedByName='$s_employeeName',addedByType='Employee Manager'";
						dbQuery($query);
						$messageId	=	mysqli_insert_id($db_conn);
					}
				}
			}
			else
			{
				$query	=	"UPDATE employee_messages SET title='$title',message='$message',displayFrom='$t_fromDate',displayTo='$t_toDate',departmentId=$departmentId,addedByName='$s_employeeName',addedByType='Employee Manager' WHERE messageId=$messageId";
				dbQuery($query);
			}
			$link	=	"";
			if($recNo)
			{
				$link	=	"?recNo=".$recNo;
			}
			
			if($mailSend)
			{
				$query	=	"SELECT title,message,addedOn FROM employee_messages WHERE messageId=$messageId";
				$result	=	dbQuery($query);
				if(mysqli_num_rows($result))
				{
					$row				=	mysqli_fetch_assoc($result);
					$emailMessagetitle	=	stripslashes($row['title']);
					$emailMessage		=	stripslashes($row['message']);
					$messageDate		=	showDate($row['addedOn']);

					$emailMessage		=	nl2br($emailMessage);
				}

				$from			=	"hr@ieimpact.com";
				$fromName		=	"HR ieIMPACT ";
				$mailSubject	=	$emailMessagetitle;
				$templateId		=	TEMPLATE_SENDING_MESSAGE_EMPLOYEE;

				$query	=	"SELECT employee_details.employeeId,firstName,lastName,email FROM employee_details".$joiningQuery." WHERE employee_details.isActive=1 AND email <> ''".$andClause.$departmentClause." ORDER BY firstName";
				$result	=	dbQuery($query);
				if(mysqli_num_rows($result))
				{
					while($row		=	mysqli_fetch_assoc($result))
					{
						$firstName		=	$row['firstName'];
						$lastName		=	$row['lastName'];
						$employeeEmail	=	$row['email'];

						$employeeName	=	$firstName." ".$lastName;
						$employeeName	=	ucwords($employeeName);

						$a_templateData	=	array("{messageDate}"=>$messageDate,"{employeeName}"=>$employeeName,"{title}"=>$emailMessagetitle,"{message}"=>$emailMessage);

						sendTemplateMail($from, $fromName, $employeeEmail, $mailSubject, $templateId, $a_templateData);
					}
				}
			}
			if(empty($a_employeeId))
			{
				if(empty($departmentId))
				{
					$departmentId		   =	0;
				}
				$a_allExistingemployees	   =	$employeeObj->getAllMtEmployeesWithMobile($departmentId);

				foreach($a_allExistingemployees as $k=>$employeeId)
				{
					$a_employeeId[$k]	   =	$k;
				}
			}
				
			if($sendSMS	==	1)
			{
				$smsMessage				    =	stripslashes($smsMessage);
				$smsMessage				    =	stringReplace("<br>", " ", $smsMessage);
				$smsMessage				    =	stringReplace("</ br>", " ", $smsMessage);
				$smsMessage				    =	"ieIMPACT hr@ieimpact.com : ".$smsMessage;
				$a_allEmployeeMobileNumbers =	array();
				$a_allEmployeeMobileNumbers1=	array();
				
				foreach($a_employeeId as $k=>$employeeId)
				{
					$nameMobile			   =	$a_allExistingEmployees[$employeeId];
					list($name,$mobile)	   =	explode("<=>",$nameMobile);
					if(!empty($mobile))
					{
						$mobile			   =	stringReplace("+", "", $mobile);
						$firstTwoDigit	   =	substr($mobile, 0, 2);
						if($firstTwoDigit !=	"91")
						{
							$mobile		              = "+91".$mobile;
						}
						$smsEmployeeMobileNo		  =	$mobile;

						$a_allEmployeeMobileNumbers[] = $smsEmployeeMobileNo;
						$a_allEmployeeMobileNumbers1[]= $employeeId."<=>".$smsEmployeeMobileNo;
					}
				}
				if(!empty($a_allEmployeeMobileNumbers))
				{
					$smsReturnPath	   =	"http://www.ieimpact.com/read-sms-postback.php"; 

					$smsKey			   =	SMS_CDYNE_KEY;
					$client			   =	new SoapClient('http://sms2.cdyne.com/sms.svc?wsdl');

					$smsReferenceID	   =    "emp-".rand(11,99)."-".substr(md5(microtime()+rand()+date('s')),0,5);
				
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

					$PhoneNumbersArray1  =    $a_allEmployeeMobileNumbers;
												 
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

					$request			=   new AdvancedCallRequestData($smsKey,$RequestArray);
					//pr($request);
					$result				=   $client->AdvancedSMSsend($request);
					//pr($request);
					$result1			=	convertObjectToArray($result);
					//pr($result1);
					$mainResult			=	$result1['AdvancedSMSsendResult'];
					$a_mainSmsResult	=	$mainResult['SMSResponse'];
					//pr($a_mainSmsResult);
					
					$smsMessage			=	makeDBSafe($smsMessage);

					foreach($a_allEmployeeMobileNumbers1 as $k=>$v)
					{
						list($toEmployeeId,$toMobileId)	   =	explode("<=>",$v);

						$cancelled			=	$a_mainSmsResult[$k]['Cancelled'];
						if(empty($cancelled))
						{
							$cancelled		=	"";
						}
						$smsMessageID		=	$a_mainSmsResult[$k]['MessageID'];
						if(empty($smsMessageID))
						{
							$smsMessageID	=	"";
						}
						$smsReferenceID		=	$a_mainSmsResult[$k]['ReferenceID'];
						if(empty($smsReferenceID))
						{
							$smsReferenceID	=	"";
						}
						$queued				=	$a_mainSmsResult[$k]['Queued'];
						if(empty($queued))
						{
							$queued			=	"";
						}
						$smsError			=	$a_mainSmsResult[$k]['SMSError'];
						if(empty($smsError))
						{
							$smsError		=	"";
						}
						
						$newSmsID		= $employeeObj->addEmployeeMessageSms($cancelled,$smsReferenceID,$toEmployeeId,$s_employeeId,$smsMessageID,$queued,$smsError,$smsMessage,$toMobileId);
					}

				}
			}

			ob_clean();
			header("Location:".SITE_URL_EMPLOYEES."/send-notice-to-employees.php".$link);
			exit();
		}
	}
	include($form);
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>