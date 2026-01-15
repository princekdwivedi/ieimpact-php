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
	$a_all_employees	=	array();
	$a_sending_to_employees	=	array();
	$department			=	1;//MT Department
	$andClauseMtPdf		=	" AND hasPdfAccess=0";
	if(!empty($s_hasPdfAccess))
	{
		$department		=	3;
		$display		=	"none";
		$display1		=	"none";
		$display2		=	"none";
		$display3		=	"";
		$andClauseMtPdf	=	" AND hasPdfAccess=1";
		$employee_lists	=	$employeeObj->getAllPdfEmployees();
	}

	$query				=	"SELECT employeeId,fullName,email FROM employee_details WHERE isActive=1".$andClauseMtPdf." ORDER BY firstName";
	$result				=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row			=	mysqli_fetch_assoc($result))
		{
			$t_employeeId	=	$row['employeeId'];
			$t_fullName		=	stripslashes($row['fullName']);
			$t_email		=	$row['email'];

			$a_sending_to_employees[$t_employeeId] = $t_fullName."<=>".$t_email;
			$a_all_employees[$t_employeeId]        = $t_employeeId;
		}
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
	$smsSend			=	false;
	$noticeSend			=	false;
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

			if($departmentId== 1)
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
	$form		=	SITE_ROOT_EMPLOYEES."/forms/notice-email-sms-to-employees.php";
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
		$smsTitle	=	$title;
		$title		=	makeDBSafe($title);
		$message	=	makeDBSafe($message);

		if(isset($_POST['sendingNotice']))
		{
			$noticeSend	        =	true;
		}
		if(isset($_POST['sendingEmail']))
		{
			$mailSend	        =	true;
		}
		if(isset($_POST['sendingSms']))
		{
			$smsSend	        =	true;
		}

		if(isset($_POST['employeeId'])  && !empty($departmentId))
		{
			$a_employeeId       =	$_POST['employeeId'];
		}		
		
		if(empty($title))
		{
			$errorMsg			.=	"Please enter heading.<br>";
		}
		if(empty($message))
		{
			$errorMsg			.=	"Please enter message.<br>";
		}
		if($noticeSend			== false && $mailSend == false && $smsSend == false)
		{
			$errorMsg			.=	"Please select a sending option.<br>";
		}
		if(!empty($message) && strlen($message) > 160 && $smsSend == true)
		{
			$errorMsg			.=	"Please enter message within 160 chracaters for sending SMS.<br>";
		}
		if(empty($errorMsg))
		{
			if(!empty($manager))
			{
				$a_manager_employees	=	array();
				$query			=	"SELECT employeeeId FROM employee_details WHERE underManager=$manager AND isActive=1 ORDER BY fullName";
				$result			=	dbQuery($query);
				if(mysqli_num_rows($result))
				{
					while($row	=	mysqli_fetch_assoc($result)){
						$m_employeeeId	=	$row['employeeeId'];

						$a_manager_employees[$m_employeeeId]	=	$m_employeeeId;
					}
				}

				if(isset($_POST['employeeId']))
				{
					$a_employeeId			=	$_POST['employeeId'];

					if(!in_array("0",$a_employeeId))
					{
						$a_sendingEmployees	=	$a_employeeId;
					}
					else
					{
						$a_sendingEmployees	=	$a_manager_employees;	
					}
				}
				else
				{
					$a_sendingEmployees		=	$a_manager_employees;
				}
			}
			else
			{
				if(isset($_POST['employeeId']))
				{
					$a_employeeId			=	$_POST['employeeId'];

					if(!in_array("0",$a_employeeId))
					{
						$a_sendingEmployees	=	$a_employeeId;
					}
					else
					{
						$a_sendingEmployees	=	$a_all_employees;	
					}
				}
				else
				{
					$a_sendingEmployees		=	$a_all_employees;
				}
			}
			
			if(count($a_sendingEmployees) > 0){
				/////////////////////////////////////////////////////////////////////////////////
				/********************* SENDING NOTICE TO EMPLOYEES ********************//////////
				if($noticeSend		==	true){
					
					list($day,$month,$year)	=	explode("-",$fromDate);
					$t_fromDate				=	$year."-".$month."-".$day;	

					list($t_day,$t_month,$t_year)	=	explode("-",$toDate);
					$t_toDate		=	$t_year."-".$t_month."-".$t_day;
					$employeeGroupId=	substr(md5(rand()+date('s')),0,10);
					foreach($a_sendingEmployees as $key=>$employeeId)
					{
						$query		=	"INSERT INTO employee_messages SET title='$title',message='$message',displayFrom='$t_fromDate',displayTo='$t_toDate',addedOn='".CURRENT_DATE_INDIA."',employeeId=$employeeId,departmentId=$departmentId,employeeGroupId='$employeeGroupId',addedByName='$s_employeeName',addedByType='Employee Manager'";
						dbQuery($query);
					}
				}

				/////////////////////////////////////////////////////////////////////////////////
				/********************* SENDING EMAIL TO EMPLOYEES ********************//////////
				if($mailSend		==	true){
					$from			=	"hr@ieimpact.com";
					$fromName		=	"HR ieIMPACT ";
					$mailSubject	=	$smsTitle;
					$templateId		=	ADMINISTRATOR_SENDING_EMAIL_EMPLOYEES;

					foreach($a_sendingEmployees as $key=>$employeeId)
					{
						$name_email	=	$a_sending_to_employees[$employeeId];
						list($toName,$toEmail) = explode("<=>",$name_email);

						$a_templateData	=	array("{employeeName}"=>$employeeName,"{message}"=>$smsMessage);

						sendTemplateMail($from, $fromName, $toEmail, $mailSubject, $templateId, $a_templateData);
					}
				}
		
				/////////////////////////////////////////////////////////////////////////////////
				/********************* SENDING SMS TO EMPLOYEES ********************//////////
				if($smsSend			==	true){
					/*include(SITE_ROOT. "/classes/nexmo-message.php");

					$nexmo_sms = new NexmoMessage('8485a866', '5e61daaa');
					foreach($a_sendingEmployees as $key=>$employeeId)
					{
						$name_mobile	=	$a_allExistingEmployees[$employeeId];
						list($toName,$mobileNumber) = explode("<=>",$name_mobile);
						
						$t_mobileNumber		=	stringReplace("+","",$mobileNumber);
						$t_mobileNumber		=	stringReplace(",","",$t_mobileNumber);
						$mobileLength		=	strlen($mobileNumber);
		
						if($mobileLength >= 10){
							$t_mobileNumber	=	substr($t_mobileNumber, -10);
							$t_mobileNumber =   "91".$t_mobileNumber;
							
							$info      = $nexmo_sms->sendText($t_mobileNumber, 'ieIMPACT', $smsMessage);
							//pr($info);
							$result1			=	convertObjectToArray($info);
							///pr($result1);
							$mainResult			=	$result1['messages'];
							$sending_status		=	$mainResult['status'];
							if($sending_status	==	0){
								$message_id		=	$mainResult['messageid'];

								dbQuery("INSERT INTO employee_messages_sms SET toEmployeeId=$employeeId,fromEmployeeId=$s_employeeId,smsMessageID='$message_id',smsMesseSent='$message',sentSmsToPhone='$t_mobileNumber',sentDate='".CURRENT_DATE_INDIA."',sentTime='".CURRENT_TIME_INDIA."',sendingFromIP='".VISITOR_IP_ADDRESS."',status='Successfully Sent'");
							}
						}

					}*/

					/*****************************************************************************
					****************************************************************************
					****************************************************************************
					************** THIS CODE IS FOR NEW SSD SENDING SMS ************************
					****************************************************************************/
					$authKey  = "8037ANJHHBRKFlGh555cda06";
					$senderId = "IMPACT";
					$message  = urlencode($smsMessage);
					$to_numbers			=	array();
					foreach($a_sendingEmployees as $key=>$employeeId)
					{
						$name_mobile	=	$a_allExistingEmployees[$employeeId];
						list($toName,$mobileNumber) = explode("<=>",$name_mobile);
						
						$t_mobileNumber		=	stringReplace("+","",$mobileNumber);
						$t_mobileNumber		=	stringReplace(",","",$t_mobileNumber);
						$mobileLength		=	strlen($mobileNumber);
						

						if($mobileLength >= 10){
							$to_numbers[]	=	substr($t_mobileNumber, -10);
						}
						else
						{
							$to_numbers[]	=	$t_mobileNumber;	
						}

						dbQuery("INSERT INTO employee_messages_sms SET toEmployeeId=$employeeId,fromEmployeeId=$s_employeeId,smsMessageID='',smsMesseSent='$message',sentSmsToPhone='$t_mobileNumber',sentDate='".CURRENT_DATE_INDIA."',sentTime='".CURRENT_TIME_INDIA."',sendingFromIP='".VISITOR_IP_ADDRESS."',status='Successfully Sent'");
					}
												
					if(count($to_numbers) > 0)
					{
						$mobileNumber	=	implode(",",$to_numbers);
									
						//Define route 
						$route = "4";
						//Prepare you post parameters
						$postData = array(
							'authkey' => $authKey,
							'mobiles' => $mobileNumber,
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
							echo 'error:' . curl_error($ch);
						}

						curl_close($ch);

					}
				}
			}
			ob_clean();
			header("Location:".SITE_URL_EMPLOYEES."/email-sms-notice-common-page.php".$link);
			exit();
		}
	}
	include($form);
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>