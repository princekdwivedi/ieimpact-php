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
	include(SITE_ROOT			. "/classes/common.php");
	include(SITE_ROOT			. "/includes/send-mail.php");
	$employeeObj				=  new employee();
	$memberObj					=  new members();
	$orderObj					=  new orders();
	$commonObj					=  new common();
	$a_allmanagerEmails			=  $commonObj->getMangersEmails();

	$bucketNumber				=	"customer_ieimpact";
	//include the S3 class
	if (!class_exists('S3'))require_once(SITE_ROOT.'/S3.php');
	
	//AWS access info
	if (!defined('awsAccessKey')) define('awsAccessKey', AMAZON_EMAIL_API);
	if (!defined('awsSecretKey')) define('awsSecretKey', AMAZON_EMAIL_KEY);
	
	//instantiate the class
	$s3 = new S3(awsAccessKey, awsSecretKey);


	$calculateReplyRateFrom		=	getPreviousGivenDate($nowDateIndia,7);

	$a_totalUnRepliedQa			=	$orderObj->getTotalUnrepliedratedOrders($calculateReplyRateFrom,$nowDateIndia,$getAllCustomers,$s_employeeId);
	
	if(!empty($a_totalUnRepliedQa))
	{
?>
<script src="<?php  echo SITE_URL;?>/script/jquery.js" type="text/javascript"></script>
<script type='text/javascript'>
function showCommentsAlert()
{
	jQuery.facebox({ajax: "<?php echo SITE_URL_EMPLOYEES;?>/display-comments-required-orders.php"});
}
</script>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/facebox.css" media="screen" rel="stylesheet" type="text/css" />
<script src="<?php  echo SITE_URL;?>/script/facebox.js" type="text/javascript"></script>
<script type="text/javascript">
	//showCommentsAlert();
window.onload =	showCommentsAlert;
</script>
<?php
		//pr($a_totalUnRepliedQa);
	}

	if(isset($_GET['orderId']) && isset($_GET['customerId']))
	{
		$orderId		=	$_GET['orderId'];
		$customerId		=	$_GET['customerId'];
		if(!in_array($customerId,$a_assignedToCustomerIds))
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
			exit();
		}
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
		exit();
	}
	if(isset($_GET['accept']) && $_GET['accept'] == 1)
	{
		$accept	=	$_GET['accept'];
		if(in_array($customerId,$a_orderCustomers))
		{
			if(!empty($orderId) && $accept == 1)
			{
				$orderObj->acceptCustomerOrder($orderId,$customerId,$s_employeeId);

				$orderAddress	=	@mysql_result(dbQuery("SELECT orderAddress FROM members_orders WHERE memberId=$customerId AND orderId=$orderId"),0);
				$orderAddress	=	stripslashes($orderAddress);

				$firstName		=	@mysql_result(dbQuery("SELECT firstName FROM members WHERE memberId=$customerId"),0);
				$firstName		=	stripslashes($firstName);

				$email			=	@mysql_result(dbQuery("SELECT email FROM members WHERE memberId=$customerId"),0);
				$email			=	stripslashes($email);
				
				/////////////////// START OF SENDING EMAIL BLOCK///////////////////////////////
				include(SITE_ROOT		.   "/classes/email-templates.php");
				$emailObj			    =	new emails();
				$a_templateData			=	array("{orderAddress}"=>$orderAddress,"{name}"=>$firstName);
				$a_templateSubject		=	array("{orderAddress}"=>$orderAddress);
				$uniqueTemplateName		=	"TEMPLATE_SENDING_MESSAGE_TO_ACCEPT_ORDER";
				$toEmail				=	$email;
				
				//include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

				if(!empty($a_allmanagerEmails))
				{
					foreach($a_allmanagerEmails as $k=>$value)
					{
						list($managerEmail,$managerName)	=	explode("|",$value);
						$managerEmployeeEmailSubject		=	$s_employeeName." Started accepting order - ".$orderAddress;
						$a_templateData			=	array("{orderAddress}"=>$orderAddress,"{name}"=>$managerName);
						$uniqueTemplateName		=	"TEMPLATE_SENDING_MESSAGE_TO_ACCEPT_ORDER";
						$toEmail				=	$managerEmail;

						//include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

					}
				}
				
				///////////////////////END OF SENDING EMAIL BLOCK////////////////////////
			}
		}
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=".$orderId."&customerId=".$customerId."#action");
		exit();
	}

	if(isset($_GET['unaccept']) && $_GET['unaccept'] == 1)
	{
		$unaccept	=	$_GET['unaccept'];
		if(in_array($customerId,$a_orderCustomers))
		{
			if(!empty($orderId) && $unaccept == 1)
			{
				if(!empty($s_hasManagerAccess))
				{
					$orderObj->unacceptCustomerOrder($orderId,$customerId);
				}
			}
		}
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=".$orderId."&customerId=".$customerId."#action");
		exit();
	}
	
	if(isset($_GET['markedCompleted']) && $_GET['markedCompleted'] == 1)
	{
		$markedCompleted	=	$_GET['markedCompleted'];
		if(in_array($customerId,$a_qaCustomers))
		{
			if(!empty($orderId) && $markedCompleted == 1)
			{
				dbQuery("UPDATE members_orders set status=2 where orderId=$orderId AND status IN (5,6) AND memberId=$customerId");
			}
		}
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=".$orderId."&customerId=".$customerId."#action");
		exit();
	}
	
?>
<script type="text/javascript">

function markedAsNeedAttention(orderId,customerId)
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/marked-as-need-attention.php?orderId="+orderId+"&customerId="+customerId;
	prop = "toolbar=no,scrollbars=yes,width=600,height=500,top=100,left=100";
	window.open(path,'',prop);
}

function acceptOrder(orderId,customerId)
{
	var confirmation = window.confirm("Are You Sure Accept This Order?");
	if(confirmation == true)
	{
		window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId='+orderId+"&customerId="+customerId+"&accept=1";
	}
}
function attentionOrder(orderId,customerId,flag)
{
	if(flag == 1)
	{
		var confirmation = window.confirm("Are You Sure To Marked This Order As Need Attention?");
	}
	else
	{
		var confirmation = window.confirm("Are You Sure To Unmarked This Order As Need Attention?");
	}
	if(confirmation == true)
	{
		window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId='+orderId+"&customerId="+customerId+"&attention="+flag;
	}
}
function acceptProcessQaOrder(orderId,customerId,flag)
{
	if(flag == 1)
	{
		var confirmation = window.confirm("Are You Sure To Accept QA For This Order?");
	}
	else if(flag == 2)
	{
		var confirmation = window.confirm("Are You Sure To QA This Order?");
	}
	else if(flag == 3)
	{
		var confirmation = window.confirm("Are You Sure To Unmarked QA Accept First Than Do QA?");
	}
	if(confirmation == true)
	{
		window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId='+orderId+"&customerId="+customerId+"&acceptProcessQaOrderType="+flag;
	}
}
function acceptMaximumOrder(orderId,customerId)
{
	var confirmation = window.confirm("Please complete previous accepted orders first to accept a new order.");
	if(confirmation == true)
	{
		window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId='+orderId+"&customerId="+customerId+"#action";
	}
}
function unacceptOrder(orderId,customerId)
{
	var confirmation = window.confirm("Are You Sure Unaccept This Order?");
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId="+orderId+"&customerId="+customerId+"&unaccept=1";
	}
}
function markFeedbackOrderComepleted(orderId,customerId)
{
	var confirmation = window.confirm("Are You Sure To Completed This Order?");
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId="+orderId+"&customerId="+customerId+"&markedCompleted=1";
	}
}
function acceptOrderWindow(orderId,customerId)
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/accept-orders-behalf-employee.php?orderId="+orderId+"&customerId="+customerId;
	prop = "toolbar=no,scrollbars=yes,width=1200,height=650,top=100,left=100";
	window.open(path,'',prop);
}
</script>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
<tr>
	<td colspan="7" height="20"></td>
</tr>
<tr>
	<td colspan="8" class="heading4">
		:: VIEW CUSTOMER ORDER ::
	</td>
</tr>
<tr>
	<td colspan="8" height="5"></td>
</tr>
</table>
<?php
	include(SITE_ROOT_EMPLOYEES	. "/includes/view-customer-order-amazon.php");
	//include(SITE_ROOT_EMPLOYEES	. "/includes/show-pop-up-message.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/view-reply-details.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/view-feedback-details.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/view-messages-details.php");

	if(isset($_GET['acceptProcessQaOrderType']))
	{
		$acceptProcessQaOrderType			=	$_GET['acceptProcessQaOrderType'];
		if(isset($replyId) && !empty($replyId))
		{
			if(!empty($acceptProcessQaOrderType))
			{
				if($acceptProcessQaOrderType	==	1)
				{
					dbQuery("UPDATE members_orders_reply SET isQaAccepted=1,qaAcceptedBy=$s_employeeId,qaAcceptedDate='".CURRENT_DATE_INDIA."',qaAcceptedTime='".CURRENT_TIME_INDIA."' WHERE orderId=$orderId AND memberId=$customerId AND replyId=$replyId");

					ob_clean();
					header("Location: ".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=".$orderId."&customerId=".$customerId."#action");
					exit();
				}
				elseif($acceptProcessQaOrderType	==	2)
				{
					ob_clean();
					header("Location: ".SITE_URL_EMPLOYEES."/view-qa-order.php?orderId=$orderId&customerId=$customerId&doneQa=1#mark");
					exit();
				}
				elseif($acceptProcessQaOrderType	==	3)
				{
					dbQuery("UPDATE members_orders_reply SET isQaAccepted=1,qaAcceptedBy=$s_employeeId,qaAcceptedDate='".CURRENT_DATE_INDIA."',qaAcceptedTime='".CURRENT_TIME_INDIA."' WHERE orderId=$orderId AND memberId=$customerId AND replyId=$replyId");

					ob_clean();
					header("Location: ".SITE_URL_EMPLOYEES."/view-qa-order.php?orderId=$orderId&customerId=$customerId&doneQa=1#mark");
					exit();
				}
			}
		}
	}

	if(isset($_GET['attention']))
	{
		$attention					=	$_GET['attention'];
		if(!empty($attention) && $attention	== 2)
		{
			$latestOrderStatus		=	@mysql_result(dbQuery("SELECT status FROM members_orders WHERE orderId=$orderId AND memberId=$customerId"),0);

			if($latestOrderStatus	==	3)
			{

				$attentionSubject	=	"Received the requested files in your order: $orderAddress";
				$attention			=	"Received the requested files";
				$attention1			=	"We have received the requested files, We are now processing this order, Thank you";
				
				$query		=	"SELECT attentionId FROM order_attention WHERE orderId=$orderId AND customerId=$customerId AND attentionStatus=1";
				$result		=	dbQuery($query);
				if(mysql_num_rows($result))
				{
					$row			=	mysql_fetch_assoc($result);
					$attentionId	=	$row['attentionId'];

					dbQuery("UPDATE order_attention SET attentionStatus=2,unmarkOn='".CURRENT_DATE_INDIA."',unmarkTime='".CURRENT_TIME_INDIA."',unmarkBy=$s_employeeId WHERE orderId=$orderId AND customerId=$customerId AND attentionStatus=1 AND attentionId=$attentionId");

					dbQuery("UPDATE members_orders SET status=0 WHERE orderId=$orderId AND memberId=$customerId");
				}

				$n_from			=	ORDER_FROM_EMAIL;
				$n_fromName		=	"ieIMPACT";
				$n_to			=	$customerEmail; 
				$n_templateId	=	TEMPLATE_SENDING_NEED_MARKED_UNMARKED_ATTENTION;
				$n_mailSubject	=	$attentionSubject;

				$a_templateData	=	array("{attention}"=>$attention,"{attention1}"=>$attention1,"{name}"=>$firstName,"{orderNo}"=>$orderAddress,"{orderType}"=>$orderText);

				sendTemplateMail($n_from, $n_fromName, $n_to, $n_mailSubject, $n_templateId, $a_templateData);

				if(!empty($customerSecondaryEmail))
				{
					sendTemplateMail($n_from, $n_fromName, $customerSecondaryEmail, $n_mailSubject, $n_templateId, $a_templateData);
				}

				if(!empty($a_allmanagerEmails))
				{
					foreach($a_allmanagerEmails as $k=>$value)
					{
						list($managerEmail,$managerName)	=	explode("|",$value);
						$n_to								=	$managerEmail;

						sendTemplateMail($n_from, $n_fromName, $n_to, $n_mailSubject, $n_templateId, $a_templateData);
					}
				}
			}
		}

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=".$orderId."&customerId=".$customerId."#action");
		exit();
	}
	if(empty($a_totalUnRepliedQa))
	{
?>
<a name="action"></a>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
<?php
	if(empty($isDeleted))
	{
		if($instructionDaysDifferent == 1)
		{
			echo "<tr><td colspan='8'><img src='".SITE_URL."/images/view-customer-instructions.gif'></td></tr>";
		}
?>

<tr>
	<td class="heading1" colspan="2">
		<?php
			$repliedUploaded=	0;
			$repliedUploaded=	$orderObj->getRepliedStatus($orderId,$customerId);
			$replyText		=	"PROCESS";
			if($repliedUploaded	==	1)
			{
				$replyText	=	"EDIT";
			}
			if($orderType != 9)
			{
				if($status	==	0 && in_array($customerId,$a_orderCustomers))
				{
					$totalUnReplied	=	0;
					$totalUnReplied	=   $orderObj->checkAcceptedReplyOrder($s_employeeId);
					$maximumOrdersAccept=	$employeeObj->maximumAcceptOrders($s_employeeId);
					if($s_hasManagerAccess)
					{
						echo "<a href='javascript:acceptOrder($orderId,$customerId)' class='link_style13'>ACCEPT</a>&nbsp; |&nbsp;";
						echo "<a href='javascript:acceptOrderWindow($orderId,$customerId)' class='link_style13'>ASSIGN</a>&nbsp; |&nbsp;";
					}
					else
					{
						if(!empty($totalUnReplied) && !empty($maximumOrdersAccept))
						{
							if($totalUnReplied < $maximumOrdersAccept)
							{
								echo "<a href='javascript:acceptOrder($orderId,$customerId)' class='link_style13'>ACCEPT</a>&nbsp; |&nbsp;";
							}
							else
							{
								echo "<a href='javascript:acceptMaximumOrder($orderId,$customerId)' class='link_style13'>ACCEPT</a>&nbsp; |&nbsp;";
							}
						}
						else
						{
							echo "<a href='javascript:acceptOrder($orderId,$customerId)' class='link_style13'>ACCEPT</a>&nbsp; |&nbsp;";
						}
					}
				}
				if($status	==	1 && in_array($customerId,$a_orderCustomers) && !empty($acceptedBy))
				{
					$isManger	=	$employeeObj->isEmployeeManager($acceptedBy);
					if(empty($isManger))
					{
						$isManger	=	0;
					}
					if(!empty($s_hasManagerAccess))
					{
						echo "<a href='".SITE_URL_EMPLOYEES."/process-pdf-order.php?orderId=$orderId&customerId=$customerId' class='link_style13'>$replyText</a>&nbsp; |&nbsp;";
					}
					else
					{
						if($acceptedBy == $s_employeeId && $isManger ==	0)
						{
							echo "<a href='".SITE_URL_EMPLOYEES."/process-pdf-order.php?orderId=$orderId&customerId=$customerId' class='link_style13'>$replyText</a>&nbsp; |&nbsp;";
						}
					}
				}
				if($status == 1 && $repliedUploaded == 1 && in_array($customerId,$a_qaCustomers))
				{
					if($isQaAccepted	==	0)
					{
						echo "<a href='javascript:acceptProcessQaOrder($orderId,$customerId,1)' class='link_style13'>ACCEPT QA</a>&nbsp; |&nbsp;";
					}
					else
					{
						if(!empty($qaAcceptedBy) && $isQaAccepted	==	1)
						{
							if($qaAcceptedBy == $s_employeeId)
							{
								echo "<a href='javascript:acceptProcessQaOrder($orderId,$customerId,2)' class='link_style13'>DO QA</a>&nbsp; |&nbsp;";
							}
							else
							{
								if(!empty($s_hasManagerAccess))
								{
									echo "<a href='javascript:acceptProcessQaOrder($orderId,$customerId,3)' class='link_style13'>UNACCEPT QA & DO QA</a>&nbsp; |&nbsp;";
								}
							}
							//echo "<a href='".SITE_URL_EMPLOYEES."/view-qa-order.php?orderId=$orderId&customerId=$customerId&doneQa=1#mark' class='link_style13'>DO QA</a>&nbsp; |&nbsp;";
						}
					}
				
				}
				if($repliedUploaded == 0 && !empty($s_hasManagerAccess) && in_array($customerId,$a_qaCustomers) && $status == 1)
				{
					echo "<a href='javascript:unacceptOrder($orderId,$customerId)' class='link_style13'>UNACCEPT</a>";
				}
				if($status == 2 || $status == 5 || $status == 6)
				{
					if(!empty($s_hasManagerAccess))
					{
						echo "<a href='".SITE_URL_EMPLOYEES."/re-send-pdf-order.php?orderId=$orderId&customerId=$customerId' class='link_style13'>RESEND FILES</a>";
					}
					else
					{
						if($acceptedBy == $s_employeeId || $qaDoneBy == $s_employeeId)
						{
							echo "<a href='".SITE_URL_EMPLOYEES."/re-send-pdf-order.php?orderId=$orderId&customerId=$customerId' class='link_style13'>RESEND FILES</a>";
						}
					}
				}
				if($status == 5)
				{
					$qaDoneDate	=	@mysql_result(dbQuery("SELECT qaDoneOn FROM members_orders_reply WHERE hasQaDone=1 AND orderId=$orderId AND memberId=$customerId"),0);

					$qaDoneTime	=	@mysql_result(dbQuery("SELECT qaDoneTime FROM members_orders_reply WHERE hasQaDone=1 AND orderId=$orderId AND memberId=$customerId"),0);

					$diffMin	=	timeBetweenTwoTimes($qaDoneDate,$qaDoneTime,$nowDateIndia,$nowTimeIndia);
					if($diffMin >= 10080)
					{
						if(!empty($s_hasManagerAccess))
						{
							echo "&nbsp;|&nbsp;<a href='javascript:markFeedbackOrderComepleted($orderId,$customerId)' class='link_style13'>MARK AS COMPLETED</a>";
						}
						else
						{
							if($qaDoneBy == $s_employeeId)
							{
								echo "&nbsp;|&nbsp;<a href='javascript:markFeedbackOrderComepleted($orderId,$customerId)' class='link_style13'>MARK AS COMPLETED</a>";
							}
						}
					}

				}
				if($status == 6)
				{
					if(!empty($s_hasManagerAccess))
					{
						echo "&nbsp;|&nbsp;<a href='javascript:markFeedbackOrderComepleted($orderId,$customerId)' class='link_style13'>MARK AS COMPLETED</a>";
					}
					else
					{
						if($qaDoneBy == $s_employeeId)
						{
							echo "&nbsp;|&nbsp;<a href='javascript:markFeedbackOrderComepleted($orderId,$customerId)' class='link_style13'>MARK AS COMPLETED</a>";
						}
					}
				}
			}
			else
			{
				include(SITE_ROOT_EMPLOYEES	. "/includes/log-prep-order.php");
			}
			if($status	!=	4)
			{
				echo " | <a href='".SITE_URL_EMPLOYEES."/send-message-pdf-customer.php?orderId=$orderId&customerId=$customerId#sendMessages' class='link_style13'>SEND MSG</a> |&nbsp;";
				echo "<a href='".SITE_URL_EMPLOYEES."/internal-emp-msg.php?orderId=$orderId&customerId=$customerId#sendMessages' class='link_style13'>INTERNAL EMP. MSG</a> |&nbsp;";
				$isAratingOrder	=	$orderObj->isRequiredOnRatedComment($orderId,$s_employeeId);
				if(!empty($isAratingOrder) || $hasRatingExplanation	==	1)
				{
					echo "<a href='".SITE_URL_EMPLOYEES."/add-comment-on-customer-rated.php?orderId=$orderId&customerId=$customerId#addComment' class='link_style13'>ADD COMMENTS ON CUSTOMER RATINGS</a> |&nbsp;";
				}
			}
			if($status	==	0)
			{
				echo "<a href='javascript:markedAsNeedAttention($orderId,$customerId)' class='link_style13'>NEED CUSTOMER ATTENTION</a>&nbsp; |&nbsp;";
			}
			elseif($status	==	3)
			{
				echo "<a href='javascript:attentionOrder($orderId,$customerId,2)' class='link_style13'>UNMARKED CUSTOMER ATTENTION</a>&nbsp; |&nbsp;";
			}
		?>
	</td>
</tr>
<?php
	}			
?>
<tr>
	<td width="5%">
		<input type="button" name="submit" onClick="history.back()" value="BACK">
	</td>
	<td>
		<?php
			include(SITE_ROOT_EMPLOYEES . "/includes/next-previous-order.php");
		?>
	</td>
</tr>
</table>
<?php
	}
	else
	{
?>
<br>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
	<tr>
		<td style="text-align:center;" class="error2" width="60%">
			<b> Please read the ratings and messages sent by customers first, please send a reply.</b>
		</td>
		<td>
			<!--<?php
				echo "<a href='".SITE_URL_EMPLOYEES."/send-message-pdf-customer.php?orderId=$orderId&customerId=$customerId#sendMessages' class='link_style13'>SEND MSG</a>";
			?>-->
		</td>
	</tr>
</table>
<?php
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>