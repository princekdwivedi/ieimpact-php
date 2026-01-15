<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/new-top.php");
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
	include(SITE_ROOT			. "/classes/email-track-reading.php");
	$employeeObj				=  new employee();
	$memberObj					=  new members();
	$orderObj					=  new orders();
	$commonObj					=  new common();
	$emailTrackObj				=  new trackReading();
	$a_allmanagerEmails			=  $commonObj->getMangersEmails();
	$calculateReplyRateFrom		=	getPreviousGivenDate($nowDateIndia,7);
	$a_fisrtThirtyOrdersList 	=   $orderObj->getFirstThirtyNewOrders();
	$a_bypassTatExplnation   	=   $orderObj->getByPassTatExplanationCustomers();
	//Making total accepted as 0
	//$totalAvailabaleOrdersToProces = 0;

	$formSearch					=	SITE_ROOT_EMPLOYEES."/forms/search-general-order-form.php";

	include($formSearch);

	if(isset($_GET['orderId']) && isset($_GET['customerId']))
	{
		$orderId		=	$_GET['orderId'];
		$customerId		=	$_GET['customerId'];
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
		exit();
	}

	
	$a_totalUnRepliedQa			=	$orderObj->getTotalUnrepliedratedOrders($calculateReplyRateFrom,$nowDateIndia,$s_employeeId);

	if(!empty($a_totalUnRepliedQa))
	{
?>
<script type='text/javascript'>
function showCommentsAlert()
{
	jQuery.facebox({ajax: "<?php echo SITE_URL_EMPLOYEES;?>/display-comments-required-orders.php?backOrderId=<?php echo $orderId;?>&backCustomerId=<?php echo $customerId;?>"});
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

	$a_allDeactivatedEmployees  =	$employeeObj->getAllInactiveEmployees();

	include(SITE_ROOT_EMPLOYEES	. "/includes/view-customer-order1.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/show-customer-general-emails.php");

	
	if(isset($_GET['accept']) && $_GET['accept'] == 1)
	{
		$accept	=	$_GET['accept'];
		
		if(!empty($orderId) && $accept == 1)
		{
			$totalAcceptedFilesByYou	=	$employeeObj->getSingleQueryResult("SELECT count(*) as total FROM members_orders WHERE isVirtualDeleted=0 AND status=1 AND acceptedBy=$s_employeeId","total");

			if(empty($totalAcceptedFilesByYou))
			{
				$totalAcceptedFilesByYou=	0;
			}

			$totalAddedReplyFilesByYou	=	$employeeObj->getSingleQueryResult("SELECT count(*) as total FROM members_orders INNER JOIN members_orders_reply ON members_orders.orderId=members_orders_reply.orderId WHERE  members_orders.orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND members_orders.isVirtualDeleted=0 AND status=1 AND acceptedBy=$s_employeeId AND hasRepliedFileUploaded=1","total");

			if(empty($totalAddedReplyFilesByYou))
			{
				$totalAddedReplyFilesByYou=	0;
			}

			if($totalAcceptedFilesByYou >  $totalAddedReplyFilesByYou)
			{
				$totalAvailabaleOrdersToProces	=	$totalAcceptedFilesByYou-$totalAddedReplyFilesByYou;
			}
			else{
				$totalAvailabaleOrdersToProces	=   0;
			}
			//Making total accepted as 0
			//$totalAvailabaleOrdersToProces      =  0;

		

			if(!empty($totalAvailabaleOrdersToProces)){

				$rediectTo	=	SITE_URL_EMPLOYEES."/view-order-others.php?orderId=".$orderId."&customerId=".$customerId;
		?>
		<script type="text/javascript">
			alert("Please complete previous orders before accepting new.");
			window.location.href = "<?php echo $rediectTo;?>";
		</script>
		<?php
			}
			else{
			
				$orderObj->acceptCustomerOrder($orderId,$customerId,$s_employeeId);

				$orderObj->addOrderTracker($s_employeeId,$orderId,$orderAddress,'Employee accpet order','EMPLOYEE_ACCEPT_ORDER');

				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=".$orderId."&customerId=".$customerId."#action");
				exit();
			}
		}
		
		
	}

	if(isset($_GET['unaccept']) && $_GET['unaccept'] == 1)
	{
		$unaccept	=	$_GET['unaccept'];
		
		if(!empty($orderId) && $unaccept == 1)
		{
			if(!empty($s_hasManagerAccess))
			{
				$orderObj->unacceptCustomerOrder($orderId,$customerId);

				$orderObj->addOrderTracker($s_employeeId,$orderId,$orderAddress,'Manager un-accpet order','MANAGER_UNACCEPT_ORDER');
			}
		}
		
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=".$orderId."&customerId=".$customerId."#action");
		exit();
	}
	
	if(isset($_GET['markedCompleted']) && $_GET['markedCompleted'] == 1)
	{
		$markedCompleted	=	$_GET['markedCompleted'];
		
		if(!empty($orderId) && $markedCompleted == 1)
		{
			dbQuery("UPDATE members_orders set status=2 where orderId=$orderId AND status IN (5,6) AND memberId=$customerId");
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
	function markedPostAuditErrorFiles(orderId,customerId)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/post-audit-errors.php?orderId="+orderId+"&customerId="+customerId;
		prop = "toolbar=no,scrollbars=yes,width=800,height=700,top=100,left=100";
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
	function reAssignOrderWindow(orderId,customerId)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/re-assign-accepted-orders.php?orderId="+orderId+"&customerId="+customerId;
		prop = "toolbar=no,scrollbars=yes,width=1200,height=650,top=100,left=100";
		window.open(path,'',prop);
	}
	function messageFileNotChecked()
	{
		alert("Files must be checked first before you accept or assign this order.");
		return false;
		
	}
	function alertReadIns(orderId,customerId,redirectTo)
	{
		if(redirectTo == 3){
			var confirmation = window.confirm("Please read customer instructions before processing this order.");
		}
		else{
			var confirmation = window.confirm("Please read employee notes before processing this order.");
		}
		if(confirmation == true)
		{
			window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId="+orderId+"&customerId="+customerId+"&selectedTab="+redirectTo;
		}
		return false;
		
	}

	function acceptOldOrders(orderId,customerId)
	{
		/*path = "<?php echo SITE_URL_EMPLOYEES?>/accept-not-tat-orders.php?orderId="+orderId+"&customerId="+customerId;
		prop = "toolbar=no,scrollbars=yes,width=800,height=700,top=100,left=100";
		window.open(path,'',prop);*/

		alert("Please accept orders according to the tat or ask manager to assign.");
		return false;
	}
</script>
<?php
	
	
	//////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////// PUTTING THE ORDER IN ORDER TRACK LIST ///////////////////////////////////////
    $orderObj->addOrderTracker($s_employeeId,$orderId,$orderAddress,'Employee view order','EMPLOYEE_VIEWED_ORDER');
    ////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////


	//***********************************************************************************************
	///////////////////////////////////// STARTING OPERATION SECTION ////////////////////////////////
	/***********************************************************************************************/

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

					////////////////////////////////////////////////////////////////////////////////////////
					//////////////////// PUTTING THE ORDER IN ORDER TRACK LIST ////////////////////////////
				    $orderObj->addOrderTracker($s_employeeId,$orderId,$orderAddress,'Employee accpet QA order','EMPLOYEE_ACCEPT_QA_ORDER');
				    ////////////////////////////////////////////////////////////////////////////////////////
				    ////////////////////////////////////////////////////////////////////////////////////////

					ob_clean();
					header("Location: ".SITE_URL_EMPLOYEES."/view-qa-order.php?orderId=$orderId&customerId=$customerId&doneQa=1#mark");
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

					////////////////////////////////////////////////////////////////////////////////////////
					//////////////////// PUTTING THE ORDER IN ORDER TRACK LIST ////////////////////////////
				    $orderObj->addOrderTracker($s_employeeId,$orderId,$orderAddress,'Employee accpet QA order','EMPLOYEE_ACCEPT_QA_ORDER');
				    ////////////////////////////////////////////////////////////////////////////////////////
				    ////////////////////////////////////////////////////////////////////////////////////////

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
			$latestOrderStatus		=	$employeeObj->getSingleQueryResult("SELECT status FROM members_orders WHERE orderId=$orderId AND memberId=$customerId","status");

			if($latestOrderStatus	==	3)
			{

				$attentionSubject	=	"Received the requested files in your order: $orderAddress";
				$attention			=	"We have received the requested files, We are now processing this order, Thank you";
				
				$query		=	"SELECT attentionId FROM order_attention WHERE orderId=$orderId AND customerId=$customerId AND attentionStatus=1";
				$result		=	dbQuery($query);
				if(mysqli_num_rows($result))
				{
					$row			=	mysqli_fetch_assoc($result);
					$attentionId	=	$row['attentionId'];

					dbQuery("UPDATE order_attention SET attentionStatus=2,unmarkOn='".CURRENT_DATE_INDIA."',unmarkTime='".CURRENT_TIME_INDIA."',unmarkBy=$s_employeeId WHERE orderId=$orderId AND customerId=$customerId AND attentionStatus=1 AND attentionId=$attentionId");

					if($isRushOrder				==	0)
					{
						$addingRequiredHrs		=	STANDRAD_ORDER_COMPLETE_TIME_HOURS;
					}
					elseif($isRushOrder			==	2)
					{
						$addingRequiredHrs		=	ECONOMY_ORDER_COMPLETE_TIME_HOURS;
					}
					else
					{
						$addingRequiredHrs		=	RUSH_ORDER_COMPLETE_TIME_HOURS;
					}
					$warningDateTimeEmployee	=	getNextCalculatedHours(CURRENT_DATE_INDIA,CURRENT_TIME_INDIA,$addingRequiredHrs);

					list($warningOrderDate,$warningOrderTime)	=	explode("=",$warningDateTimeEmployee);

					dbQuery("UPDATE members_orders SET status=0,isOrderNeedAttention=0,isHavingEstimatedTime=1,employeeWarningDate='$warningOrderDate',employeeWarningTime='$warningOrderTime' WHERE orderId=$orderId AND memberId=$customerId");

					$performedTask	=	"Un-Marked as need attention by employee own";
				
					$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);

					////////////////////////////////////////////////////////////////////////////////////////
					//////////////////// PUTTING THE ORDER IN ORDER TRACK LIST ////////////////////////////
				    $orderObj->addOrderTracker($s_employeeId,$orderId,$orderAddress,'Employee Un-Marked need attention order','EMPLOYEE_UNMARK_ATTENTION_ORDER');
				    ////////////////////////////////////////////////////////////////////////////////////////
				    ////////////////////////////////////////////////////////////////////////////////////////

				}

				/////////////////// START OF SENDING EMAIL BLOCK///////////////////////////////
				include(SITE_ROOT		.   "/classes/email-templates.php");
				$emailObj			    =	new emails();
				
				$trackEmailImage		=	$emailTrackObj->addTrackEmailRead($customerEmail,"Sending Unmark Need Attention Email","orders@ieimpact.com",$customerId,1,4);

				if(!empty($memberOrderReplyToEmail)){
					$setThisEmailReplyToo			=	$memberOrderReplyToEmail.CUSTOMER_REPLY_EMAIL_TO;//Setting for reply to make customer reply order mesage
					$setThisEmailReplyTooName		=	"ieIMPACT Orders";//Setting for reply to make customer reply order mesage
				}
				else{
					if(!empty($orderEncryptedId))
					{
						$setThisEmailReplyToo			=	$orderEncryptedId.CUSTOMER_REPLY_EMAIL_TO;//Setting for reply to make customer reply order mesage
						$setThisEmailReplyTooName		=	"ieIMPACT Orders";//Setting for reply to make customer reply order mesage
					}
				}

				$quickReplyToEmail      = "<a href='mailto:".$setThisEmailReplyToo."'>".$setThisEmailReplyToo."</a>";

				$newOrdersSmartEmail 	=	"<a href='mailTo:NewOrder".$smartEmailUniqueEmailCode.CUSTOMER_REPLY_EMAIL_TO."'><u>NewOrder".$smartEmailUniqueEmailCode.CUSTOMER_REPLY_EMAIL_TO."</u></a>";

				$newOrdersMessagingEmail=	"<a href='mailTo:Email".$smartEmailUniqueEmailCode.CUSTOMER_REPLY_EMAIL_TO."'><u>Email".$smartEmailUniqueEmailCode.CUSTOMER_REPLY_EMAIL_TO."</u></a>";



				$a_templateData	=	array("{attention}"=>$attention,"{name}"=>$firstName,"{orderNo}"=>$orderAddress,"{orderType}"=>$orderText,"{trackEmailImage}"=>$trackEmailImage,"{quickReplyToEmail}"=>$quickReplyToEmail,"{newOrdersSmartEmail}"=>$newOrdersSmartEmail,"{newOrdersMessagingEmail}"=>$newOrdersMessagingEmail);

				$a_templateSubject	=	array("{attentionSubject}"=>$attentionSubject);
				$toEmail			=	$customerEmail;
				$uniqueTemplateName	=	"TEMPLATE_SENDING_NEED_MARKED_UNMARKED_ATTENTION";
								
				include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

				$trackEmailImage	=	"images/white-space.jpg";

				$a_templateData		=	array("{attention}"=>$attention,"{name}"=>$firstName,"{orderNo}"=>$orderAddress,"{orderType}"=>$orderText,"{trackEmailImage}"=>$trackEmailImage,"{quickReplyToEmail}"=>$quickReplyToEmail);

				if(!empty($customerSecondaryEmail))
				{
					$toEmail			=	$customerSecondaryEmail;
					$uniqueTemplateName	=	"TEMPLATE_SENDING_NEED_MARKED_UNMARKED_ATTENTION";
					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
				}
		

				if(!empty($a_allmanagerEmails))
				{
					foreach($a_allmanagerEmails as $k=>$value)
					{
						list($managerEmail,$managerName)	=	explode("|",$value);
						
						$toEmail			=	$managerEmail;
						$uniqueTemplateName	=	"TEMPLATE_SENDING_NEED_MARKED_UNMARKED_ATTENTION";
						include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
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
			$repliedUploaded=	$hasRepliedUploaded;
			$replyText		=	"PROCESS";
			if($repliedUploaded	==	1)
			{
				$replyText	=	"EDIT";
			}
			if($orderType != 9)
			{
				if($status	==	0 && $isNotVerfidedEmailOrder == 0)
				{
					$totalUnReplied	=	0;
					$totalUnReplied	=   $orderObj->checkAcceptedReplyOrder($s_employeeId);
					if($s_hasManagerAccess)
					{
						//echo "<a href='javascript:acceptOrder($orderId,$customerId)' class='link_style13'>ACCEPT</a>&nbsp; |&nbsp;";
						if($isOrderChecked	==	1)
						{
							echo "<a onclick='acceptOrderWindow($orderId,$customerId)' class='link_style13' style='cursor:pointer;'>ASSIGN</a>&nbsp; |&nbsp;";
						}
						else
						{
							echo "<a onclick='messageFileNotChecked()' class='link_style13' style='cursor:pointer;'>ASSIGN</a>&nbsp; |&nbsp;";
						}
					}
					else
					{								
						
						if($isOrderChecked	==	1)
						{
							if(!empty($totalUnReplied) && !empty($maximumOrdersAccept))
							{
								if($totalUnReplied < $maximumOrdersAccept)
								{
									if(isset($totalAvailabaleOrdersToProces) && !empty($totalAvailabaleOrdersToProces))
									{
										echo "<a onclick='acceptMaximumOrder($orderId,$customerId)' class='link_style13' style='cursor:pointer'>ACCEPT</a>&nbsp; |&nbsp;";
									}
									else{
										if(!empty($a_fisrtThirtyOrdersList) && count($a_fisrtThirtyOrdersList) > 0 && in_array($orderId,$a_fisrtThirtyOrdersList)){
											
											echo "<a onclick='acceptOrder($orderId,$customerId)' class='link_style13' style='cursor:pointer;'>ACCEPT</a>&nbsp; |&nbsp;";
										}
										else{
											if(in_array($customerId,$a_bypassTatExplnation)){
												echo "<a onclick='acceptOrder($orderId,$customerId)' class='link_style13' style='cursor:pointer;'>ACCEPT</a>&nbsp; |&nbsp;";
											}
											else{
												echo "<a onclick='acceptOldOrders($orderId,$customerId)' class='link_style13' style='cursor:pointer' title='Accept It'>ACCEPT</a>&nbsp; |&nbsp;";
											}
											
										}
									}
								}
								else
								{
									echo "<a onclick='acceptMaximumOrder($orderId,$customerId)' class='link_style13' style='cursor:pointer'>ACCEPT</a>&nbsp; |&nbsp;";
								}
							}
							else
							{
								if(isset($totalAvailabaleOrdersToProces) && !empty($totalAvailabaleOrdersToProces))
								{
									echo "<a onclick='acceptMaximumOrder($orderId,$customerId)' class='link_style13' style='cursor:pointer'>ACCEPT</a>&nbsp; |&nbsp;";
								}
								else
								{
									if(!empty($a_fisrtThirtyOrdersList) && count($a_fisrtThirtyOrdersList) > 0 && in_array($orderId,$a_fisrtThirtyOrdersList)){
									
										echo "<a onclick='acceptOrder($orderId,$customerId)' class='link_style13' style='cursor:pointer'>ACCEPT</a>&nbsp; |&nbsp;";
									}
									else{
										if(in_array($customerId,$a_bypassTatExplnation)){
											echo "<a onclick='acceptOrder($orderId,$customerId)' class='link_style13' style='cursor:pointer'>ACCEPT</a>&nbsp; |&nbsp;";
										}
										else{
											echo "<a onclick='acceptOldOrders($orderId,$customerId)' class='link_style13' style='cursor:pointer' title='Accept It'>ACCEPT</a>&nbsp; |&nbsp;";
										}
										
									}
								}
							}
						}
						else
						{
							echo "<a onclick='messageFileNotChecked()' class='link_style13' style='cursor:pointer'>ACCEPT</a>&nbsp; |&nbsp;";
						}
					}
				}
				if($status	==	1 && !empty($acceptedBy))
				{
					$isManger	=	$employeeObj->isEmployeeManager($acceptedBy);
					if(empty($isManger))
					{
						$isManger	=	0;
					}
					if(!empty($s_hasManagerAccess))
					{
						if(empty($isReadInstructions) && !empty($splInstructionToEmployee) && $status == 1 && $repliedUploaded	!=	1){
							echo "<a onclick='alertReadIns($orderId,$customerId,3);' class='link_style13' style='cursor:pointer;'>PROCESS</a>&nbsp; |&nbsp;";
						}
						elseif(empty($isReadEmployeeNote) && empty($splInstructionToEmployee) && !empty($splInstructionOfCustomer) && $status == 1 && $repliedUploaded	!=	1){
							echo "<a onclick='alertReadIns($orderId,$customerId,4);' class='link_style13' style='cursor:pointer;'>PROCESS</a>&nbsp; |&nbsp;";
						}
						else{
							echo "<a href='".SITE_URL_EMPLOYEES."/process-pdf-order.php?orderId=$orderId&customerId=$customerId#process' class='link_style13'>$replyText</a>&nbsp; |&nbsp;";
						}
					}
					else
					{
						if($acceptedBy == $s_employeeId && $isManger ==	0)
						{
							if(empty($isReadInstructions) && !empty($splInstructionToEmployee) && $status == 1 && $repliedUploaded	!=	1){
								echo "<a onclick='alertReadIns($orderId,$customerId,3);' class='link_style13' style='cursor:pointer;'>PROCESS</a>&nbsp; |&nbsp;";
							}
							elseif(empty($isReadEmployeeNote) && empty($splInstructionToEmployee) && !empty($splInstructionOfCustomer) && $status == 1 && $repliedUploaded	!=	1){
								echo "<a onclick='alertReadIns($orderId,$customerId,4);' class='link_style13' style='cursor:pointer;'>PROCESS</a>&nbsp; |&nbsp;";
							}
							else{
								echo "<a href='".SITE_URL_EMPLOYEES."/process-pdf-order.php?orderId=$orderId&customerId=$customerId#process' class='link_style13'>$replyText</a>&nbsp; |&nbsp;";
							}
						}
					}
				}
				if($status == 1 && $repliedUploaded == 1 && $isHavingEmployeeQaAccess == 1)
				{
					if($isQaAccepted	==	0)
					{
						//Allow Only Employee Who Is Not Done This Order
						if($acceptedBy != $s_employeeId || !empty($s_hasManagerAccess) || !empty($s_iasHavingAllQaAccess))
						{
							echo "<a onclick='acceptProcessQaOrder($orderId,$customerId,1)' class='link_style13' style='cursor:pointer;'>ACCEPT QA</a>&nbsp; |&nbsp;";
						}

						//Allow Only Employee Who Has QA Access
						/*echo "<a onclick='acceptProcessQaOrder($orderId,$customerId,1)' class='link_style13' style='cursor:pointer;'>ACCEPT QA</a>&nbsp; |&nbsp;";*/
					}
					else
					{
						if(!empty($qaAcceptedBy) && $isQaAccepted	==	1)
						{
							if($qaAcceptedBy == $s_employeeId)
							{
								echo "<a onclick='acceptProcessQaOrder($orderId,$customerId,2)' class='link_style13' style='cursor:pointer;'>DO QA</a>&nbsp; |&nbsp;";
							}
							else
							{
								if(!empty($s_hasManagerAccess) || !empty($s_iasHavingAllQaAccess))
								{
									echo "<a onclick='acceptProcessQaOrder($orderId,$customerId,3)' class='link_style13' style='cursor:pointer;'>UNACCEPT QA & DO QA</a>&nbsp; |&nbsp;";
								}
							}
							//echo "<a href='".SITE_URL_EMPLOYEES."/view-qa-order.php?orderId=$orderId&customerId=$customerId&doneQa=1#mark' class='link_style13'>DO QA</a>&nbsp; |&nbsp;";
						}
					}
				
				}
				if($repliedUploaded == 0 && !empty($s_hasManagerAccess) && $status == 1)
				{
					echo "<a onclick='unacceptOrder($orderId,$customerId)' class='link_style13' style='cursor:pointer;' title='UNACCEPT'>UNACCEPT</a> | <a onclick='reAssignOrderWindow($orderId,$customerId)' class='link_style13' style='cursor:pointer;' title='RE-ASSIGN'>RE-ASSIGN</a>";
				}
				if($status == 2 || $status == 5 || $status == 6)
				{
					if(!empty($s_hasManagerAccess))
					{
						echo "<a href='".SITE_URL_EMPLOYEES."/re-send-pdf-order.php?orderId=$orderId&customerId=$customerId' class='link_style13'>RESEND FILES</a>";
					}
					/*else
					{
						if($acceptedBy == $s_employeeId || $qaDoneBy == $s_employeeId)
						{
							echo "<a href='".SITE_URL_EMPLOYEES."/re-send-pdf-order.php?orderId=$orderId&customerId=$customerId' class='link_style13'>RESEND FILES</a>";
						}
					}*/
				}
				if($status == 5)
				{
					$qaDoneDate	=	$employeeObj->getSingleQueryResult("SELECT qaDoneOn FROM members_orders_reply WHERE replyId > ".MAX_SEARCH_MEMBER_ORDERID." AND hasQaDone=1 AND orderId=$orderId AND memberId=$customerId","qaDoneOn");

					$qaDoneTime	=	$employeeObj->getSingleQueryResult("SELECT qaDoneTime FROM members_orders_reply WHERE replyId > ".MAX_SEARCH_MEMBER_ORDERID." AND hasQaDone=1 AND orderId=$orderId AND memberId=$customerId","qaDoneTime");


					$diffMin	=	timeBetweenTwoTimes($qaDoneDate,$qaDoneTime,$nowDateIndia,$nowTimeIndia);
					if($diffMin >= 10080)
					{
						if(!empty($s_hasManagerAccess))
						{
							echo "&nbsp;|&nbsp;<a onclick='markFeedbackOrderComepleted($orderId,$customerId)' class='link_style13' style='cursor:pointer;'>MARK AS COMPLETED</a>";
						}
						else
						{
							if($qaDoneBy == $s_employeeId)
							{
								echo "&nbsp;|&nbsp;<a onclick='markFeedbackOrderComepleted($orderId,$customerId)' class='link_style13' style='cursor:pointer;'>MARK AS COMPLETED</a>";
							}
						}
					}

				}
				if($status == 6)
				{
					if(!empty($s_hasManagerAccess))
					{
						echo "&nbsp;|&nbsp;<a onclick='markFeedbackOrderComepleted($orderId,$customerId)' class='link_style13' style='cursor:pointer;'>MARK AS COMPLETED</a>";
					}
					else
					{
						if($qaDoneBy == $s_employeeId)
						{
							echo "&nbsp;|&nbsp;<a onclick='markFeedbackOrderComepleted($orderId,$customerId)' class='link_style13'>MARK AS COMPLETED</a>";
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
				echo " | <a href='".SITE_URL_EMPLOYEES."/send-message-pdf-customer.php?orderId=$orderId&customerId=$customerId#sendMessages' class='link_style13'>SEND MSG</a> |&nbsp;<a href='".SITE_URL_EMPLOYEES."/internal-emp-msg.php?orderId=$orderId&customerId=$customerId#sendMessages' class='link_style13'>INTERNAL EMP. MSG</a> |&nbsp;";
				$isAratingOrder	=	0;
				if(($rateGiven == 1 || $rateGiven == 2) && ($acceptedBy=$s_employeeId || $qaDoneBy= $s_employeeId)){
					$isAratingOrder = 1;
				}

				if(!empty($isAratingOrder) || $hasRatingExplanation	==	1)
				{
					echo "<a href='".SITE_URL_EMPLOYEES."/add-comment-on-customer-rated.php?orderId=$orderId&customerId=$customerId#addComment' class='link_style13'>ADD COMMENTS ON CUSTOMER RATINGS</a>";
				}
			}
			if(!empty($s_hasManagerAccess))
			{
				if($status	==	0)
				{
					echo " |&nbsp;<a href='javascript:markedAsNeedAttention($orderId,$customerId)' class='link_style13'>NEED CUSTOMER ATTENTION</a>&nbsp; |&nbsp;";
				}
				elseif($status	==	3)
				{
					echo " |&nbsp;<a href='javascript:attentionOrder($orderId,$customerId,2)' class='link_style13'>UNMARKED CUSTOMER ATTENTION</a>&nbsp; |&nbsp;";
				}
				if($status	==	2 || $status	==	5 || $status	==	6)
				{
					echo "<a onclick='markedPostAuditErrorFiles($orderId,$customerId)' class='link_style13' style='cursor:pointer;'>".$postAuditErrorText."</a>";
				}
			}
		?>
	</td>
</tr>
<?php
	}	
	else
	{
		if($status == 5 || $status == 6)
		{
			echo "<tr><td colspan='6'>";
			if(!empty($s_hasManagerAccess))
			{
				echo "<a onclick='markFeedbackOrderComepleted($orderId,$customerId)' class='link_style13' style='cursor:pointer;'>MARK AS COMPLETED</a>";
			}
			else
			{
				if($qaDoneBy == $s_employeeId)
				{
					echo "<a onclick='markFeedbackOrderComepleted($orderId,$customerId)' class='link_style13' style='cursor:pointer;'>MARK AS COMPLETED</a>";
				}
			}
			echo "</td></tr>";
		}
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
