<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/common.php");
	include(SITE_ROOT			. "/classes/email-track-reading.php");

	$employeeObj				= new employee();
	$memberObj					= new members();
	$orderObj					= new orders();
	$commonObj					= new common();
	$a_allmanagerEmails			= $commonObj->getMangersEmails();
	$emailTrackObj				= new trackReading();

	$showForm					= false;
	$generalMsgId				= 0;
	$memberId					= 0;
	$error						= "";
	$replyMessage				= "Enter Your Reply Message Here";
	$errorMessageForm			= "You are not authorized to view this page !!";

	if(isset($_GET['generalMsgId']) && isset($_GET['memberId']))
	{
		$generalMsgId			=	$_GET['generalMsgId'];
		$memberId				=	$_GET['memberId'];

		$query					=	"SELECT members_general_messages .*,completeName,email,secondaryEmail,uniqueEmailCode FROM members_general_messages INNER JOIN members ON members_general_messages.memberId=members.memberId WHERE members_general_messages.memberId=$memberId AND generalMsgId=$generalMsgId AND isOrderGeneralMsg=1 AND isBillingMsg=0 AND members_general_messages.status=0 AND parentId=0";
		$result	=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$showForm					=	true;

			$row						=	mysqli_fetch_assoc($result);
			$generalMsgId				=	$row['generalMsgId'];
			$memberId					=	$row['memberId'];
			$customerName				=	stripslashes($row['completeName']);
			$messageDate				=	$row['addedOn'];
			$messageTime				=	$row['addedtime'];
			$messageRelatedOrder		=	stripslashes($row['messageRelatedOrder']);
			$message					=	stripslashes($row['message']);
			$email						=	$row['email'];
			$secondaryEmail				=	$row['secondaryEmail'];
			$memberUniqueEmailCode		=	$row['uniqueEmailCode'];
			$t_messageRelatedOrder		=	makeDBSafe($messageRelatedOrder);
	

			if(empty($messageRelatedOrder))
			{
				$messageRelatedOrder	=	"Not Specific Order";
				$t_messageRelatedOrder	=	"";
				$e_messageRelatedOrder	=	"";
			}
			else
			{
				$e_messageRelatedOrder	=	" : ".$messageRelatedOrder;
			}

			$messageDateTime			=	showDate($messageDate)." ".showTimeFormat($messageTime);
		}
	}
?>
<html>
<head>
<TITLE>Send Reply Message To Customer General Message</TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
<body>
<script type="text/javascript">
	function reflectChange()
	{
		window.opener.location.reload();
	}
</script>
<center>
<?php
	if($showForm)
	{
		if(isset($_REQUEST['formSubmitted']))
		{
			extract($_REQUEST);
			$replyMessage		=	trim($replyMessage);
			if($replyMessage	==	"Enter Your Reply Message Here")
			{
				$replyMessage	=	"";
			}
			if(empty($replyMessage))
			{
				$error			= "Enter your reply message.";
			}
			else
			{
				$replyMessage	=	stripslashes($replyMessage);
				$t_replyMessage	=	makeDBSafe($replyMessage);
				$replyMessage	=	nl2br($replyMessage);
				
				dbQuery("INSERT INTO members_general_messages SET memberId=$memberId,messageRelatedOrder='$t_messageRelatedOrder',message='$t_replyMessage',addedOn='".CURRENT_DATE_INDIA."',addedtime='".CURRENT_TIME_INDIA."',isOrderGeneralMsg=1,parentId=$generalMsgId,replyBy=$s_employeeId,isReplyByEmployee=1");

				//dbQuery("UPDATE members_general_messages SET status=1,replyBy=$s_employeeId,isReplyByEmployee=1,repliedOn='".CURRENT_DATE_INDIA."',repliedTime='".CURRENT_TIME_INDIA."' WHERE memberId=$memberId AND generalMsgId=$generalMsgId AND isOrderGeneralMsg=1 AND isBillingMsg=0 AND status=0 AND parentId=0");

				//dbQuery("UPDATE members SET isHavingGeneralMessage=1 WHERE memberId=$memberId");

				$subject			=	"Reply of your order problem".$e_messageRelatedOrder;

				include(SITE_ROOT	.	"/classes/email-templates.php");
				$emailObj			=	new emails();

				$setThisEmailReplyToo		=	"Email".$memberUniqueEmailCode.CUSTOMER_REPLY_EMAIL_TO;//Setting for reply to make customer reply order mesage
				$setThisEmailReplyTooName	=	"ieIMPACT Message";//Setting for reply to make customer reply order mesage

				$trackEmailImage	=	$emailTrackObj->addTrackEmailRead($email,$subject,"orders@ieimpact.com",$memberId,1,3,3,$s_employeeName,$s_employeeId);
				
				$a_templateSubject	=	array("{emailSubject}"=>$subject);
								
				$a_templateData	=	array("{completeName}"=>$customerName,"{emailBody}"=>$replyMessage,"{subject}"=>$subject,"{trackEmailImage}"=>$trackEmailImage);

				$uniqueTemplateName		=	"TEMPLATE_SENDING_SIMPLEE_CUSTOMER_MESSAGE";
				$toEmail				=	$email;
				include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

				$trackEmailImage		=	"images/white-space.jpg";
				$setThisEmailReplyToo	= "";
				$setThisEmailReplyTooName	= "";

				$a_managerEmails		=	"john@ieimpact.net";
					
				$a_templateData			=	array("{completeName}"=>$customerName,"{emailBody}"=>$replyMessage,"{subject}"=>$subject,"{trackEmailImage}"=>$trackEmailImage);

				$uniqueTemplateName		=	"TEMPLATE_SENDING_SIMPLEE_CUSTOMER_MESSAGE";
				$toEmail				=	DEFAULT_BCC_EMAIL;
				$managerEmployeeFromBcc =	$a_managerEmails;
		
								
				include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

				echo "<br><center><font class='smalltext2'><b>Successfully sent reply message !!</b></font></center></br>";
	
				echo "<script type='text/javascript'>reflectChange();</script>";
			
				echo "<script>setTimeout('window.close()',1)</script>";
			}

		}
?>
	<script type="text/javascript">
		function display_loading()
		{
			document.getElementById('loading').style.display = 'block';
		} 
		function validMessage()
		{
			form1	=	document.generalReplyMessage;
			if(form1.replyMessage.value == "" || form1.replyMessage.value == "Enter Your Reply Message Here")
			{
				alert("Enter your reply message.");
				form1.replyMessage.focus();
				return false;
			}
			display_loading();

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
	<form name="generalReplyMessage" action="" method="POST" onSubmit="return validMessage();">
		<table width="100%" align="center" border="0" cellspacing="3" cellspacing="3">
			<tr>
				<td colspan="3" class="textstyle1"><b>Send Reply Message To Order General Message</b></td>
			</tr>
			<tr>
				<td colspan="3" class="error">
					<?php echo $error;?>
				</td>
			</tr>
			<tr>
				<td width="24%" class="smalltext2">
					<b>Customer Name</b>
				</td>
				<td width="2%" class="smalltext2">
					<b>:</b>
				</td>
				<td class="title">
					<?php echo $customerName;?>
				</td>
			</tr>
			<tr>
				<td class="smalltext2">
					<b>Related Order</b>
				</td>
				<td class="smalltext2">
					<b>:</b>
				</td>
				<td class="title">
					<?php echo $messageRelatedOrder;?>
				</td>
			</tr>
			<tr>
				<td class="smalltext2">
					<b>Date & Time</b>
				</td>
				<td class="smalltext2">
					<b>:</b>
				</td>
				<td class="title">
					<?php echo $messageDateTime;?>
				</td>
			</tr>
			
			<tr>
				<td valign="top" colspan="3">
					<textarea name="replyMessage" rows="10" cols="60" wrap="hard" onKeyDown="textCounter(this.form.replyMessage,this.form.remLentext1,1000);" onKeyUp="textCounter(this.form.replyMessage,this.form.remLentext1,1000);" onFocus="if(this.value=='Enter Your Reply Message Here') this.value='';" onBlur="if(this.value=='') this.value='Enter Your Reply Message Here';"><?php echo nl2br($replyMessage);?></textarea>

					<br><font class="smalltext2">Characters Left: <input type="textbox" readonly name="remLentext1" size=2 value="1000" style="border:0"></font>
				</td>
			</tr>
			
			<?php
				}	
			?>
			<tr>
				<td colspan="3" class="smlltext">[Note<font color="red"><b>*</b></font>: Please check your english as your message will go to customer]</td>
			</tr>
			<tr>
				<td colspan="3" height="5"></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td colspan="2">
					<div id="loading" style="display: none;"><img src="<?php echo OFFLINE_IMAGE_PATH;?>/images/ajax-loader.gif" alt="" /></div> 
				</td>
			</tr>
			<tr>
				<td colspan="3" height="5"></td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td>
					<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
					<input type='hidden' name='formSubmitted' value='1'>
				</td>
			</tr>
		</table>
	</form>
<br><br>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
</center>
</body>
</html>

