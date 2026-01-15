<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/includes/send-mail.php");
	$employeeObj				=  new employee();
	$memberObj					=  new members();
	$orderObj					=  new orders();
	$messgeText					=  "SEND";
	$customerEmail				=	"";
	$customerSecondaryEmail		=	"";
	$a_managerEmails			=	array();

	$uploadFileText				=	"";
	$uploadFileName				=	"";
	if(isset($_GET['orderId']) && isset($_GET['customerId']))
	{
		$orderId		=	$_GET['orderId'];
		$customerId		=	$_GET['customerId'];
		if(!in_array($customerId,$a_assignedToCustomerIds))
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
			exit();
			echo "case2";
		}
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
		exit();
	}
	$newMssageToEmployee	=	"";
	$messageId				=	0;
	if(isset($_GET['messageId']))
	{
		$messageId		=	(int)$_GET['messageId'];
		$message		=	@mysql_result(dbQuery("SELECT message FROM members_employee_messages WHERE orderId=$orderId AND memberId=$customerId AND messageBy='".EMPLOYEES."' AND messageId=$messageId"),0);
		if(empty($message))
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
			exit();
		}
		else
		{
			$newMssageToEmployee =  $message;
			$messgeText			 =  "EDIT";
		}
	}
?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
<tr>
	<td colspan="7" height="20"></td>
</tr>
<tr>
	<td colspan="8" class="heading1">
		:: SEND MESSAGE TO CUSTOMER ON BPO ORDER ::
	</td>
</tr>
<tr>
	<td colspan="8" height="5"></td>
</tr>
</table>
<?php
	include(SITE_ROOT_EMPLOYEES	. "/includes/bpo-customer-order-details.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/bpo-order-details.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/view-bpo-reply-details.php");

	function findexts($filename) 
	{ 
		$ext        =    "";
		$filename   =    strtolower($filename) ; 
		$a_exts		=	 explode(".",$filename);
		$total		=	 count($a_exts);
		if($total > 1){
			$ext	=	 end($a_exts);		
		}		
		return $ext; 
	} 
	function getFileName($fileName)
	{
		$dotPosition	=  strpos($fileName, "'");
		if($dotPosition == true)
		{
			$fileName	=	str_replace("'", "", $fileName);
		}
		$doubleDotPosition	  =  strpos($fileName, '"');
		if($doubleDotPosition == true)
		{
			$fileName	=	str_replace('"', '', $fileName);
		}
		$fileExtPos		=  strrpos($fileName, '.');
		$fileName		=  substr($fileName,0,$fileExtPos);
		
		return $fileName;
	}

	$messageFilePath =	SITE_ROOT_FILES."/files/messages/";

	if($result	=	$orderObj->getOrderMessages($orderId,$customerId))
	{
?>
<br>
<a name="messages"></a>
<table width='100%' align='center' cellpadding='3' cellspacing='2' border='0'>
	<tr>
		<td colspan="2" class="text"><?php echo $messgeText;?> BETWEEN CUSTOMER AND EMPLOYEE</td>
	</tr>
	<tr>
		<td colspan="2" height="5"></td>
	</tr>
<?php
		while($row			=	mysql_fetch_assoc($result))
		{
			$t_messageId	=	$row['messageId'];
			$t_message		=	stripslashes($row['message']);
			$addedOn		=	showDate($row['addedOn']);
			$addedTime		=	$row['addedTime'];
			$messageBy		=	$row['messageBy'];
			$hasMessageFiles=	$row['hasMessageFiles'];
			$fileName		=	$row['fileName'];
			$fileExtension	=	$row['fileExtension'];
			$fileSize		=	$row['fileSize'];
			if($messageBy   ==  EMPLOYEES)
			{
				$employeeId		=	$row['employeeId'];
				$employeeName	=	$employeeObj->getEmployeeName($employeeId);
				echo "<tr><td width='80%' class='smalltext2'><b>Message From ".$employeeName." to - ".$customerName." on $addedOn</b></td><td>";
				//<a href='".SITE_URL_EMPLOYEES."/send-message-pdf-customer.php?orderId=$orderId&customerId=$customerId&messageId=$t_messageId' class='link_style2'>Edit This Message</a>
				echo "</td></tr>";
			}
			elseif($messageBy   ==  CUSTOMERS)
			{
				echo "<tr><td width='80%' class='smalltext2' colspan='2'><b>Message From ".$customerName." to Employee  on $addedOn</b></td></tr>";
			}
			echo "<tr><td colspan='2' class='smalltext2'>".nl2br($t_message)."</td></tr>";
			if($hasMessageFiles == 1)
			{
				echo "<tr><td colspan='2' class='title3'><b>Uploaded File : </b>";
				echo "<a href='".SITE_URL_EMPLOYEES."/download-message-files.php?ID=$t_messageId'  class='linkstyle6'><b>".$fileName.".".$fileExtension."</b></a>&nbsp;&nbsp;<font class='smalltext'>".getFileSize($fileSize)."</font>";
				echo "</td></tr>";
			}
			echo "<tr><td colspan='2'><hr size='1' width='100%' color='#bebebe'></td></tr>";
		}
?>
</table>
<?php
	}
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		$message		=	makeDBSafe($message);
		if(empty($messageId))
		{
			$query	=	"INSERT INTO members_employee_messages SET orderId=$orderId,memberId=$customerId,employeeId=$s_employeeId,message='$message',parentId=0,addedOn='$nowDateIndia',addedTime='$nowTimeIndia',messageBy='".EMPLOYEES."'";
			dbQuery($query);
			$messageId		=	mysql_insert_id();

			if(!empty($_FILES['messageFile']['name']))
			{
				$uploadingFile	=   $_FILES['messageFile']['name'];
				$mimeType		=   $_FILES['messageFile']['type'];
				$fileSize		=   $_FILES['messageFile']['size'];
				$tempName		=	$_FILES['messageFile']['tmp_name'];
				$ext			=	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);


				$fileName		= $messageId."_".$uploadingFileName.".".$ext;

				move_uploaded_file($tempName,$messageFilePath.$fileName);
				chmod($messageFilePath."$fileName",0600);

				dbQuery("UPDATE members_employee_messages SET hasMessageFiles=1,fileExtension='$ext',fileName='$uploadingFileName',fileMimeType='$mimeType',fileSize=$fileSize WHERE messageId=$messageId");

				$uploadFileText		=	"Uploaded File :";
				$uploadFileName		=	$uploadingFileName.".".$ext;

			}

			$performedTask	=	"Send Message To - ".$customerId." On Order No - ".$orderId;

			$messageText	=	"for your";
			$messageText1	=	"ieIMPACT staff sent a message for your order";
			$messageText2	=	"You can also view this message by login into our website.";

			$n_message		=	@mysql_result(dbQuery("SELECT message FROM members_employee_messages WHERE orderId=$orderId AND memberId=$customerId AND messageId=$messageId"),0);

			$n_message		=	stripslashes($n_message);


			$n_message		=	nl2br($n_message);

			$from			=	ORDER_FROM_EMAIL;
			$fromName		=	"ieIMPACT";
			$mailSubject	=	"Message for order - $orderAddress";

			$to				=	$customerEmail;
			$templateId		=	TEMPLATE_SENDING_MESSAGE_EMPLOYEE_TO_CUSTOMER;
			$a_templateData	=	array("{messageText}"=>$messageText,"{orderNo}"=>$orderAddress,"{orderText}"=>$orderText,"{name}"=>$customerName,"{orderType}"=>$orderText,"{messageText1}"=>$messageText1,"{message}"=>$n_message,"{messageText2}"=>$messageText2,"{uploadFileText}"=>$uploadFileText,"{uploadFileName}"=>$uploadFileName);

			if($hasReceiveEmails == 0)
			{
				if($refferedBy   != 4)
				{
					sendTemplateMail($from, $fromName, $to, $mailSubject, $templateId, $a_templateData);

					if(!empty($customerSecondaryEmail))
					{
						sendTemplateMail($from, $fromName, $customerSecondaryEmail, $mailSubject, $templateId, $a_templateData);
					}
				}
				else
				{
					$n_from			=	ORDER_FROM_EMAIL_AAPRAISERAIDE;
					$n_fromName		=	ORDER_FROM_NAME_AAPRAISERAIDE;
					$n_to			=	$customerEmail; 
					$n_templateId	=	TEMPLATE_SENDING_MESSAGE_AAPRAISERAIDE_CUSTOMER;

					$a_templateData	=	array("{name}"=>$customerName,"{orderNo}"=>$orderAddress,"{orderText}"=>$orderText,"{message}"=>$n_message,"{messageFromSite}"=>$n_fromName,"{uploadFileText}"=>$uploadFileText,"{uploadFileName}"=>$uploadFileName,"{mailFromWesbiteUrl}"=>"http://www.appraisersaide.com","{mailFromWesbiteName}"=>"www.appraisersaide.com");

					sendTemplateMail($n_from, $n_fromName, $n_to, $mailSubject, $n_templateId, $a_templateData);

					if(!empty($secondaryEmail))
					{
						sendTemplateMail($n_from, $n_fromName, $n_to, $mailSubject, $n_templateId, $a_templateData);
					}
				}
			}

			$a_managerEmails	=	$orderObj->getAllMangersEmails();

			if(!empty($a_managerEmails))
			{
				foreach($a_managerEmails as $k=>$value)
				{
					list($managerEmail,$managerName)	=	explode("|",$value);
					
					$messageText	=	"on";
					$messageText1	=	ucwords($s_employeeName)." sends a message on ".$customerName." order";
					$messageText2	=	"";
					
					$from			=	ORDER_FROM_EMAIL;
					$fromName		=	"ieIMPACT";
					$mailSubject	=	"Message from ".$s_employeeName." for order no - $orderNo";

					$to1			=	$managerEmail;
					$templateId1	=	TEMPLATE_SENDING_MESSAGE_CUSTOMER_MANAGER;
					$a_templateData1	=	array("{messageText}"=>$messageText,"{orderNo}"=>$orderAddress,"{orderText}"=>$orderText,"{name}"=>$managerName,"{orderType}"=>$orderText,"{messageText1}"=>$messageText1,"{message}"=>$n_message,"{messageText2}"=>$messageText2,"{uploadFileText}"=>$uploadFileText,"{uploadFileName}"=>$uploadFileName);

					sendTemplateMail($from, $fromName, $to1, $mailSubject, $templateId1, $a_templateData1);

				}
			}
		}
		else
		{
			$query	=	"UPDATE members_employee_messages SET message='$message' WHERE orderId=$orderId AND memberId=$customerId AND messageId=$messageId";
			dbQuery($query);

			$performedTask	=	"Update Message To - ".$customerId." On Order No - ".$orderId;
		}
		$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES ."/send-message-bpo-customer.php?orderId=$orderId&customerId=$customerId");
		exit();
	}
?>
<script type="text/javascript">
function checkValidMessage()
{
	form1	=	document.sendCustomerMessage;
	if(form1.message.value == "" || form1.message.value == "Enter Your Message Here")
	{
		alert("Please Enter Your Message !!");
		form1.message.focus();
		return false;
	}
}
function textCounter(field,countfield,maxlimit)
{
	if(field.value.length > maxlimit)
	{
		field.value = field.value.substring(0, maxlimit);
	}
	else
	{
		countfield.value = maxlimit - field.value.length;
	}
 }
 </script>
 <br>
 <a name="sendMessages"></a>
<form name="sendCustomerMessage" action=""  method="POST" enctype="multipart/form-data" onsubmit="return checkValidMessage();">
<table width='100%' align='center' cellpadding='3' cellspacing='0' border='0'>
<tr>
	<td colspan="3" class="text">SEND MESSAGE TO CUSTOMER</td>
</tr>
<tr>
	<td colspan="3" height="5"></td>
</tr>
<tr>
	<td valign="top" colspan="3">
		<textarea name="message" rows="7" cols="70" wrap="hard" onKeyDown="textCounter(this.form.message,this.form.remLentext1,1000);" onKeyUp="textCounter(this.form.message,this.form.remLentext1,1000);" onFocus="if(this.value=='Enter Your Message Here') this.value='';" onBlur="if(this.value=='') this.value='Enter Your Message Here';"><?php echo stripslashes(htmlentities($newMssageToEmployee,ENT_QUOTES))?></textarea>

		<br><font class="smalltext2">Characters Left: <input type="textbox" readonly name="remLentext1" size=2 value="1000" style="border:0"></font>
	</td>
</tr>
<tr>
	<td height="5" colspan="3"></td>
</tr>
<tr>
	<td width="12%" class="smalltext2"><b>Upload A File</b></td>
	<td width="2%" class="smalltext2"><b>:</b></td>
	<td>
		<input type="file" name="messageFile">
	</td>
</tr>
<tr>
	<td height="10" colspan="3"></td>
</tr>
<tr>
	<td>
		<input type="submit" name="submit" value="Submit">
		<input type="button" name="submit" onClick="history.back()" value="Back">
		<input type="hidden" name="formSubmitted" value="1">
	</td>
</tr>
</table>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>