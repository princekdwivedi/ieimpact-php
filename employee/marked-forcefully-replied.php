<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	ini_set('display_errors', 1);
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/common.php");
	include(SITE_ROOT			. "/classes/email-templates.php");
	$emailObj					= new emails();
	$payment_gateways			=	array(PAYMENT_GATEWAY_AUTHORIZE => "Authorize.net",PAYMENT_GATEWAY_STRIPE => "Stripe");
	
	$employeeObj			= new employee();
	$memberObj				= new members();
	$orderObj				= new orders();
	$commonClass			= new common();
	$showForm				= false;

	$paymentGatewayId				=	$employeeObj->getSingleQueryResult("SELECT id FROM active_payment_gateway","id");
	if(empty($paymentGatewayId)){
		$paymentGatewayId			=	0;
	}
	else{
		$paymentGatewayUsed			= $payment_gateways[$paymentGatewayId];
	}

	$messageId				= 0;
	$memberId				= 0;
	$type					= 0;
	$employeeId				= 0;
	$error					= "";
	$displayOrderAddress	= "";
	$acceptedByText			= "";
	$rateGiven				= "";
	$oldOrderId 			= 0;
	$oldMessageId 			= 0;
	$oldOrderAddress		= "";
	//Type	=	1 Customer Order Message Type = 2 Customer Rating Message Type = 3 Customer General Message

	
if(isset($_GET['messageId']) && isset($_GET['memberId']) && isset($_GET['type']))
{
	$messageId					=	(int)$_GET['messageId'];
	$memberId					=	(int)$_GET['memberId'];
	$type						=	(int)$_GET['type'];
					
	if(!empty($messageId) && !empty($memberId) && !empty($type))
	{
		$table					=	"members_employee_messages INNER JOIN members ON members_employee_messages.memberId=members.memberId";
		$whereClause			=	" WHERE members_employee_messages.messageId=$messageId AND isVirtualDeleted =0 AND messageBy='".CUSTOMERS."' AND isRepliedToEmail=0 AND isTestAccount=0";
		$selectedColumn			=	"members_employee_messages .*,completeName,firstName,lastName,appraisalSoftwareType";
		$text					=	"Customer Order Message";
		if($type				==	2)
		{
			$table				=	"members_orders INNER JOIN members ON members_orders.memberId=members.memberId";
			$whereClause		=	" WHERE members_orders.orderId=$messageId AND isRepliedToRatingMessage=0 AND rateGiven <> 0 AND isTestAccount=0 AND isVirtualDeleted =0";
			$text				=	"Customer Order Ratings";
			$selectedColumn		=	"members_orders .*,completeName,firstName,lastName,appraisalSoftwareType";
		}
		elseif($type			==	3)
		{
			$table				=	"members_general_messages INNER JOIN members ON members_general_messages.memberId=members.memberId";
			$whereClause		=	" WHERE members_general_messages.generalMsgId=$messageId AND isOrderGeneralMsg=1 AND isBillingMsg=0  AND parentId=0";
			$text				=	"Customer General Message";
			$selectedColumn		=	"members_general_messages .*,completeName,firstName,lastName";
		}

		$query							=	"SELECT ".$selectedColumn." FROM ".$table.$whereClause;
		$result							=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$showForm					=   true;
			$row						=	mysqli_fetch_assoc($result);
			if($type					==	1)
			{
				$orderId				=	$oldOrderId   = $row['orderId'];
				$messageId				=	$oldMessageId = $row['messageId'];
				$message				=	stripslashes($row['message']);
				$customerName			=	stripslashes($row['completeName']);
				$firstName				=	stripslashes($row['firstName']);
				$lastName				=	stripslashes($row['lastName']);
				$completeName			=	$firstName." ".substr($lastName, 0, 1);
				$date					=	$row['addedOn'];
				$time					=	$row['addedTime'];
				$oderAddress			=	$oldOrderAddress= $employeeObj->getSingleQueryResult("SELECT orderAddress FROM members_orders WHERE orderId=$orderId","orderAddress");
				$oderAddress			=	stripslashes($oderAddress);

				$query1					=	"SELECT status,orderAddress,acceptedBy FROM members_orders WHERE memberId=$memberId AND orderId=$orderId";
				$result1				=	dbQuery($query1);
				if(mysqli_num_rows($result1))
				{
					$row1				=	mysqli_fetch_assoc($result1);
					$status             =   $row1['status'];
					$orderAddress       =   stripslashes($row1['orderAddress']);
					$acceptedBy         =   $row1['acceptedBy'];
				}

				$statusText				=   "<font color='red'>New Order</font>";
				$acceptedByText			=	"";
				if($result11			=	$orderObj->isOrderChecked($orderId))
				{
					$statusText			=   "<font color='green'>New Order</font>";
				}
				if($status				==	1)
				{
					$statusText			=  "<font color='#4F0000'>Accepted</font>";
					$hasReplied			=	$employeeObj->getSingleQueryResult("SELECT hasRepliedFileUploaded FROM members_orders_reply WHERE orderId=$orderId AND memberId=$memberId AND hasRepliedFileUploaded=1","hasRepliedFileUploaded");
					if(!empty($hasReplied))
					{
						$statusText		=	"<font color='blue'>QA Pending</font>";
					}
				}
				
				if($status				==	2)
				{
					$statusText			=   "<font color='green'>Completed</font>";
				}
				elseif($status			==	3)
				{	
					$statusText			=   "<font color='#333333'>Nd Atten.</font>";
				}
				elseif($status			==	5)
				{
					$statusText			=   "<font color='green'>Nd Feedbk.</font>";
				}

				elseif($status			==	4)
				{
					$statusText			=   "<font color='#ff0000'>Cancelled</font>";
				}
				elseif($status			==	6)
				{
					$statusText			=   "<font color='green'>Fd Rcvd</font>";
				}
				if(!empty($acceptedBy))
				{
					$acceptedByText		=	 $employeeObj->getEmployeeFirstName($acceptedBy);
				}
				$displayOrderAddress	=	$oderAddress;
			}
			elseif($type				==	2)
			{

				$orderId				=	$row['orderId'];
				$memberId				=	$row['memberId'];
				$customerName			=	stripslashes($row['completeName']);
				$firstName				=	stripslashes($row['firstName']);
				$lastName				=	stripslashes($row['lastName']);
				$completeName			=	$firstName." ".substr($lastName, 0, 1);
				$status					=	$row['status'];
				$date					=	$row['rateGivenOn'];
				$time					=	$row['rateGivenTime'];
				$acceptedBy				=	$row['acceptedBy'];
				$oderAddress			=	stripslashes($row['orderAddress']);
				$message				=	stripslashes($row['memberRateMsg']);
				$rateGiven				=	$row['rateGiven'];
				$statusText				=	"<font color='green'>Completed</font>";
				$acceptedByText			=	 $employeeObj->getEmployeeFirstName($acceptedBy);
				$displayOrderAddress	=	$oderAddress;
			}
			if($type					==	3)
			{
				$customerName			=	stripslashes($row['completeName']);
				$firstName				=	stripslashes($row['firstName']);
				$lastName				=	stripslashes($row['lastName']);
				$completeName			=	$firstName." ".substr($lastName, 0, 1);
				$date					=	$row['addedOn'];
				$time					=	$row['addedtime'];
				$messageRelatedOrder	=	stripslashes($row['messageRelatedOrder']);
				$message				=	stripslashes($row['message']);
				$isUploadedMessageFiles	=	$row['isUploadedFiles'];
				
				$message                =   preg_replace( "/\r|\n/", "", $message);

				if(empty($messageRelatedOrder))
				{
					$messageRelatedOrder=	"Not Specific Order";
				}
				else
				{
					$displayOrderAddress=	$messageRelatedOrder;
				}
			}
			$daysAgo					=	showDateTimeFormat($date,$time);
		}

	}
}
	

	
?>
<html>
<head>
<TITLE>Action Taken On <?php echo $text;?></TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
<body>
<script type="text/javascript">
	function reflectChange()
	{
		window.opener.location.reload();
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
	 function validMessage()
	 {
		form1	=	document.sendAttentionMessage;
		if(form1.replyMessage.value == "" || form1.replyMessage.value == " " || form1.replyMessage.value == "0" || form1.replyMessage.value == "  ")
		{
			alert("Please enter action taken.");
			form1.replyMessage.focus();
			return false;
		}
		else
		{
			if(form1.replyMessage.value.length  > 50)
			{
				alert("Please enter action taken within 50 words.");
				form1.replyMessage.focus();
				return false;
			}
		}
	 }
</script>
<center>
<?php
	if($showForm)
	{

		if(isset($_REQUEST['formSubmitted']))
		{
			extract($_REQUEST);

			$isNewOrder		=	0;
			if(isset($_POST['isNewOrder'])){
				$isNewOrder	=	$_POST['isNewOrder'];
			}

			if($isNewOrder	==	1){
				
				include(SITE_ROOT_EMPLOYEES	. "/includes/make-email-message-order.php");

				if($type				==	1 && isset($newOrderIdFromMessage) && !empty($newOrderIdFromMessage) && !empty($oldOrderId))
				{				
					
					dbQuery("UPDATE members_orders SET isHavingOrderNewMessage=0 WHERE orderId=$oldOrderId");

					
					dbQuery("UPDATE members_employee_messages SET managerRepliedText='Created new order from the message',isRepliedMessage=1,isRepliedToEmail=1,messageRepliedMarkedBy=$s_employeeId,repliedOn='".CURRENT_DATE_INDIA."',repliedTime='".CURRENT_TIME_INDIA."',repliedFromIP='".VISITOR_IP_ADDRESS."' WHERE orderId=$oldOrderId AND memberId=$memberId AND messageId=$oldMessageId AND isRepliedToEmail=0 AND messageRepliedMarkedBy=0");

					dbQuery("DELETE FROM customer_orders_messages_counts WHERE messageId=$messageId AND orderId=$oldOrderId");

					$orderObj->deductOrderRelatedCounts('unrepliedMessages');

					$orderObj->addOrderTracker($s_employeeId,$oldOrderId,$oldOrderAddress,'Employee action taken on order message and creted new order','EMPLOYEE_ACTION_TAKEN_ORDER');
				}
				
				echo "<script type='text/javascript'>reflectChange();</script>";
				
				echo "<script>setTimeout('window.close()',1)</script>";
			}
			else{
				$replyMessage	=	trim($replyMessage);
				$replyMessage	=	makeDBSafe($replyMessage);

				if(empty($replyMessage))
				{
					$error		=   "Please enter action taken.";
				}
				else
				{
					$msgLength	=	strlen($replyMessage);
					if($msgLength > 55)
					{
						$error	=   "Please enter action taken within 50 words.";
					}
				}
				if(empty($error))
				{
					$replyMessage			=	makeDBSafe($replyMessage);
					
					if($type				==	1)
					{				
						$orderAddress           =   $employeeObj->getSingleQueryResult("SELECT orderAddress FROM members_orders WHERE orderId=$orderId","orderAddress");

						dbQuery("UPDATE members_orders SET isHavingOrderNewMessage=0 WHERE orderId=$orderId");

						dbQuery("UPDATE members_employee_messages SET managerRepliedText='$replyMessage',isRepliedMessage=1,isRepliedToEmail=1,messageRepliedMarkedBy=$s_employeeId,repliedOn='".CURRENT_DATE_INDIA."',repliedTime='".CURRENT_TIME_INDIA."',repliedFromIP='".VISITOR_IP_ADDRESS."' WHERE orderId=$orderId AND memberId=$memberId AND messageId=$messageId AND isRepliedToEmail=0 AND messageRepliedMarkedBy=0");

						dbQuery("DELETE FROM customer_orders_messages_counts WHERE messageId=$messageId AND orderId=$orderId AND memberId=$memberId");

						$orderObj->deductOrderRelatedCounts('unrepliedMessages');

						$orderObj->addOrderTracker($s_employeeId,$orderId,$orderAddress,'Employee action taken on order message','EMPLOYEE_ACTION_TAKEN_ORDER');
					}
					elseif($type				==	2)
					{
						$orderAddress           =    $employeeObj->getSingleQueryResult("SELECT orderAddress FROM members_orders WHERE orderId=$orderId","orderAddress"); 
						
						dbQuery("UPDATE members_orders SET isRepliedToRatingMessage=1,managerRepliedRatingText='$replyMessage',ratingMessageRepliedBy=$s_employeeId,ratingRepliedOn='".CURRENT_DATE_INDIA."',ratingRepliedTime='".CURRENT_TIME_INDIA."',ratingRepliedFromIP='".VISITOR_IP_ADDRESS."',isHavingOrderNewMessage=0 WHERE orderId=$orderId AND memberId=$memberId AND isHavingOrderNewMessage=1 AND rateGiven <> 0 "); 

						dbQuery("DELETE FROM all_unreplied_rating WHERE orderId=$orderId AND memberId=$memberId");

						$orderObj->deductOrderRelatedCounts('unrepliedRatings');

						$orderObj->addOrderTracker($s_employeeId,$orderId,$orderAddress,'Employee action taken on order rating','EMPLOYEE_ACTION_TAKEN_ORDER_RATING');
					}
					elseif($type				==	3)
					{
						dbQuery("UPDATE members_general_messages SET repliedByEmployeetext='$replyMessage',status=1,replyBy=$s_employeeId,isReplyByEmployee=1,repliedOn='".CURRENT_DATE_INDIA."',repliedTime='".CURRENT_TIME_INDIA."' WHERE memberId=$memberId AND generalMsgId=$messageId AND isOrderGeneralMsg=1 AND isBillingMsg=0 AND status=0 AND parentId=0");


						dbQuery("UPDATE members SET isHavingGeneralMessage=1 WHERE memberId=$memberId");

						$orderObj->deductOrderRelatedCounts('unrepliedGeneralMsg');

						$orderObj->addOrderTracker($s_employeeId,0,"",'Employee action taken on customer general message','EMPLOYEE_ACTION_TAKEN_GENERAL_MESSAGE',$memberId);
					}
					
					echo "<script type='text/javascript'>reflectChange();</script>";
				
					echo "<script>setTimeout('window.close()',1)</script>";
				}
			}
		}
?>
	<form name="sendAttentionMessage" action="" method="POST" onSubmit="return validMessage();">
		<table width="100%" align="center" border="0" cellspacing="3" cellspacing="3">
			<tr>
				<td colspan="3" class="textstyle1"><b>Action Taken On <?php echo $text;?></b></td>
			</tr>
			<tr>
				<td colspan="3" class="error">
					<?php echo $error;?>
				</td>
			</tr>
			<tr>
				<td width="22%" class="smalltext2">
					<b>Message Type</b>
				</td>
				<td width="2%" class="smalltext2">
					<b>:</b>
				</td>
				<td class="title">
					<?php echo $text;?>
				</td>
			</tr>
			<tr>
				<td class="smalltext2">
					<b>Customer Name</b>
				</td>
				<td class="smalltext2">
					<b>:</b>
				</td>
				<td class="title">
					<?php echo $completeName;?>
				</td>
			</tr>
			<?php
				if(!empty($displayOrderAddress))
				{
			?>
			<tr>
				<td class="smalltext2">
					<b>Order Address</b>
				</td>
				<td class="smalltext2">
					<b>:</b>
				</td>
				<td class="title">
					<?php echo $displayOrderAddress;?>
				</td>
			</tr>
			<?php
				}	
				if(!empty($rateGiven))
				{
			?>
			<tr>
				<td class="smalltext2">
					<b>Rate Given</b>
				</td>
				<td class="smalltext2">
					<b>:</b>
				</td>
				<td class="title">
					<img src="<?php echo SITE_URL;?>/images/rating/<?php echo $rateGiven;?>.png">
				</td>
			</tr>
			<?php
				}
				if(!empty($acceptedByText))
				{
			?>
			<tr>
				<td class="smalltext2">
					<b>Accepted By</b>
				</td>
				<td class="smalltext2">
					<b>:</b>
				</td>
				<td class="title">
					<?php echo $acceptedByText;?>
				</td>
			</tr>
			<?php
				}
			?>
			<tr>
				<td class="smalltext2">
					<b>Date</b>
				</td>
				<td class="smalltext2">
					<b>:</b>
				</td>
				<td class="title">
					<?php echo $daysAgo;?>
				</td>
			</tr>
			<tr>
				<td class="smalltext2" valign="top">
					<b>Customer Message</b>
				</td>
				<td class="smalltext2" valign="top">
					<b>:</b>
				</td>
				<td class="title" valign="top">
					<div style='overflow:auto;width:450px;scrollbars:no'>
						<table width="100%">
							<tr>
								<td class="smalltext2">
									<?php echo nl2br($message);?>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td class="smalltext2" valign="top">
					<b>Action Taken</b>
				</td>
				<td class="smalltext2" valign="top">
					<b>:</b>
				</td>
				<td valign="top">
					<textarea name="replyMessage" cols="55" rows="5" border="1px solid #000000;font-family:verdana;color:#4d4d4d;" onKeyDown="textCounter(this.form.replyMessage,this.form.remLentext1,50);" onKeyUp="textCounter(this.form.replyMessage,this.form.remLentext1,50);"  onCopy="return false" onDrag="return false" onDrop="return false" onPaste="return false" autocomplete="off"></textarea>
					<br><font class="smalltext2"><b>Characters Left:</b><input type="textbox" readonly name="remLentext1" size=2 value="50" style="border:0"></font>
					<br /><font class="smalltext1">[This message only for internal purpose and will not go to customer.<br/>Please briefly state the action taken.]</font>
				</td>
			</tr>
			<?php
				if($s_hasManagerAccess == 1){
			?>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td colspan="3" class="smalltext2">Do you want to make this email message as new order?&nbsp;<input type="radio" name="isNewOrder" value="0" checked>No&nbsp;<input type="radio" name="isNewOrder" value="1">Yes</td>
			</tr>
			<?php
				}
			?>
			<tr>
				
			</tr>
			<tr>
				<td colspan="2">
					&nbsp;
				</td>
				<td>
					<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
					<input type='hidden' name='formSubmitted' value='1'>
				</td>
			</tr>
		</table>
	</form>
<?php
	}
	else
	{
		echo "<table width='90%' align='center' border='1' height='100'><tr><td align='center' align='center' class='error'><b>You are trying to open an invalid page.</b></td></tr></table>";
	}
?>
<br><br>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
</center>
</body>
</html>

