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
	$pagingObj					=	new Paging();
	$employeeObj				=	new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$showHideAllOption		=	false;
	$employeeId				=	0;
	$smsMessage				=	"";
	if($s_employeeId		==	5 || $s_employeeId	==	449 || $s_employeeId	==	137 || $s_employeeId	==	637 || $s_employeeId==	8 || $s_employeeId	==	587 || $s_employeeId	==	3)
	{
		$showHideAllOption	=	true;
	}
	if($showHideAllOption	==	false){
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$searchText				=	"";
	if(isset($_SESSION['hasPdfAccess']))
	{
		$whereClause		=	"WHERE isActive=1 AND hasPdfAccess=1";
	}
	else
	{
		$whereClause		=	"WHERE isActive=1 AND hasPdfAccess=0";
	}
	
	$a_employees			=	array();
	$a_mobiles				=	array();
	$query					=	"SELECT employeeId,fullName,mobile FROM employee_details ".$whereClause." AND mobile <> '' ORDER BY firstName";
	$result					=	dbQuery($query);
	if(mysql_num_rows($result)){
		while($row			=	mysql_fetch_assoc($result)){
			$t_employeeId	=	$row['employeeId'];
			$t_fullName		=	stripslashes($row['fullName']);
			$mobile			=	stripslashes($row['mobile']);


			$a_employees[$t_employeeId] = $t_fullName;
			$a_mobiles[$t_employeeId]   = $mobile;
		}
	}

	if(isset($_REQUEST['recNo']))
	{
		$recNo					 =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo	=	0;
	}
	$orderBy					=	"firstName";
	$queryString				=	"smsId DESC";
	$errorMsg					=	"";

	if(isset($_REQUEST['searchFormSubmit'])){
		extract($_REQUEST);
		

		$smsMessage				=	trim($smsMessage);
		$t_smsMessage			=	makeDBSafe($smsMessage);

		if(!empty($t_smsMessage) && !empty($employeeId)){
			
				
			$mobileNumber		=	$a_mobiles[$employeeId];
			$t_mobileNumber		=	stringReplace("+","",$mobileNumber);
			$t_mobileNumber		=	stringReplace(",","",$t_mobileNumber);
			$mobileLength		=	strlen($mobileNumber);

			if($mobileLength > 12 || !is_numeric($t_mobileNumber)){
				$errorMsg		=	"Invalid Number : ".$mobileNumber;
			}
			else{
				if($mobileLength >= 10){
					$t_mobileNumber	=	substr($t_mobileNumber, -10);
					$t_mobileNumber =   "91".$t_mobileNumber;
					
					include(SITE_ROOT. "/classes/nexmo-message.php");

					$nexmo_sms = new NexmoMessage('8485a866', '5e61daaa');
					$info      = $nexmo_sms->sendText($t_mobileNumber, 'ieIMPACT', $smsMessage);
					//pr($info);
					$result1			=	convertObjectToArray($info);
					///pr($result1);
					$mainResult			=	$result1['messages'];
					$sending_status		=	$mainResult['status'];
					if($sending_status	==	0){
						$message_id		=	$mainResult['messageid'];

						dbQuery("INSERT INTO employee_messages_sms SET toEmployeeId=$employeeId,fromEmployeeId=$s_employeeId,smsMessageID='$message_id',smsMesseSent='$t_smsMessage',sentSmsToPhone='$t_mobileNumber',sentDate='".CURRENT_DATE_INDIA."',sentTime='".CURRENT_TIME_INDIA."',sendingFromIP='".VISITOR_IP_ADDRESS."',status='Successfully Sent'");

						$_SESSION['success_sent']	=	1;

						//ob_clean();
						//header("Location: ".SITE_URL_EMPLOYEES."/sending-sms-to-employees.php#1");
						//exit();
					}
					else{
						$errorMsg		=	$mainResult['errortext'];
					}
				}
			}
		}
		else{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/sending-sms-to-employees.php");
			exit();
		}
	}

	
?>
<script type='text/javascript'>
	function validSend()
	{
		form1	=	document.sendingSMS;
		if(form1.employeeId.value == "0" || form1.employeeId.value == ""){
			alert("Select an employee.");
			form1.employeeId.focus();
			return false;
		}
		if(form1.smsMessage.value == "0" || form1.smsMessage.value == "" || form1.smsMessage.value == " "){
			alert("Select a message.");
			form1.smsMessage.focus();
			return false;
		}

	}
</script>
<form name="sendingSMS" action=""  method="POST" onsubmit="return validSend();">
	<table cellpadding="3" cellspacing="3" width='98%'align="center" border='0'>
		<tr>
			<td colspan="4"  class="textstyle3">SENDING SMS TO EMPLOYEE</td>
		</tr>
		<tr>
			<td colspan="4" height="10"></td>
		</tr>
		<?php
			if(!empty($errorMsg)){
		?>
		<tr>
			<td colspan="4"  class="error"><b>Error : <?php echo $errorMsg;?></b></td>
		</tr>
		<tr>
			<td colspan="4" height="3"></td>
		</tr>
		<?php
			}
		?>
		<tr>
			<td width="15%" class="smalltext2"><b>SENDING SMS TO</b></td>
			<td width="2%" class="smalltext2"><b>:</b></td>
			<td>
				<select name="employeeId">
				<option value="0">Select</option>
				<?php
					foreach($a_employees as $key=>$value)
					{
						$select	=	"";
						if($employeeId	==	$key)
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
			<td colspan="4" height="10"></td>
		</tr>
		<tr>
			<td valign="top" class="smalltext2"><b>SMS MESSAGE</b></td>
			<td valign="top" class="smalltext2"><b>:</b></td>
			<td>
				<textarea name="smsMessage" rows="5" cols="35" style="border:1px solid #000000;font-family:verdana;font-size:13px;"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2"></td>
			<td class="smalltext2">
				<input type="image" name="submit" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='searchFormSubmit' value='1'>&nbsp;[Messages can be only send to India's mobile  numbers within <font color="#ff0000;">09:00 AM</font> to <font color="#ff0000;">09:00 PM</font>]
			</td>
		</tr>
		<?php
			if(isset($_SESSION['success_sent']))
			{
		?>
		<tr>
			<td colspan="4"  class="textstyle3">SENT SMS SUCCESSFULLY</td>
		</tr>
		<tr>
			<td colspan="4" height="5"></td>
		</tr>
		<?php
				unset($_SESSION['success_sent']);
			}
		?>
	</table>
</form>

<?php
	$start					  =	0;
	$recsPerPage	          =	20;//how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"employee_messages_sms INNER JOIN employee_details ON employee_messages_sms.toEmployeeId=employee_details.employeeId";
	$pagingObj->selectColumns = "employee_messages_sms.*,fullName";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/sending-sms-to-employees.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
	?>
	<table width='98%' align='center' cellpadding='0' cellspacing='0' border='0'>
		<tr>
			<td colspan="4"  class="textstyle3">EXISTING SMS TO EMPLOYEE</td>
		</tr>
		<tr>
			<td colspan="4" height="10"></td>
		</tr>
		<tr height='25' bgcolor="#373737">
			<td width='3%' class='smalltext12'>&nbsp;</td>
			<td width='18%' class='smalltext12'>Name</td>
			<td width='35%' class='smalltext12'>Message</td>
			<td width='15%' class='smalltext12'>Number</td>
			<td width='15%' class='smalltext12'>Date & Time</td>
			<td class='smalltext12'>Status</td>
		</tr>
	<?php
		$i							=	$recNo;
		while($row					=   mysql_fetch_assoc($recordSet))
		{
			$i++;

			$employeeId				=	$row['employeeId'];
			$employeeName			=	stripslashes($row['fullName']);
			$sentSmsToPhone			=	stripslashes($row['sentSmsToPhone']);
			$smsMesseSent			=	stripslashes($row['smsMesseSent']);
			$sentDate				=	$row['sentDate'];
			$sentTime		 		=	$row['sentTime'];
			$status					=	$row['status'];
			$bgColor				=	"class='rwcolor1'";
			if($i%2==0)
			{
				$bgColor			=	"class='rwcolor2'";
			}

			
	?>
		<tr <?php echo $bgColor;?> height="30">
			<td class="smalltext2" valign="top">
				&nbsp;<?php echo $i.")";?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo $employeeName;
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo $smsMesseSent;
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo $sentSmsToPhone;
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo showDate($sentDate)." at ".showTimeShortFormat($sentTime);
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo $status;
				?>
			</td>
			
		</tr>
	<?php
		}
		echo "</table>";
		echo "<table width='100%'><tr><td align='right'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr></table>";
	}
	
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>