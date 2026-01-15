<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
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
	include(SITE_ROOT			. "/classes/validate-fields.php");
	include(SITE_ROOT			. "/includes/send-mail.php");
	include(SITE_ROOT			. "/classes/email-track-reading.php");
	include(SITE_ROOT			. "/classes/email-templates.php");
	include(SITE_ROOT			. "/classes/common.php");
				
	list($currentY,$currentM,$currentD)	=	explode("-",$nowDateIndia);

	$employeeObj				=  new employee();
	$emailObj					=  new emails();
	$memberObj					=  new members();
	$orderObj					=  new orders();
	$validator					=  new validate();
	$emailTrackObj				=  new trackReading();
	$commonClass				=  new common();
	$replyId					=  0;
	$doneQa						=  0;
	$qaChecked					=  "";
	$errorCorrected				=  "";
	$feedbackToEmployee			=  "";
	$timeSpentQa				=  "";
	$needFeedBackMessage		=  "";
	$isSentFailPaymentEmail		=	false;
	
	if(empty($isHavingEmployeeQaAccess))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
		exit();
	}

		
	$qaHeadingText				=  "VIEW CUSTOMER ORDER WITH REPLIED ORDER";
	if(isset($_GET['orderId']) && isset($_GET['customerId']))
	{
		$orderId				=	$_GET['orderId'];
		$customerId				=	$_GET['customerId'];
		
		$qaAcceptedByEmployee	=	@mysql_result(dbQuery("SELECT qaAcceptedBy FROM members_orders_reply WHERE isQaAccepted=1 AND qaAcceptedBy <> 0 AND orderId=$orderId"),0);
		
		if(empty($qaAcceptedByEmployee))
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
	if(isset($_GET['doneQa']) && $_GET['doneQa'] == 1)
	{
		$doneQa			 =	$_GET['doneQa'];
		$orderStatus     = $orderObj->getOrderStatus($orderId,$customerId);
		if($orderStatus != 1)
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
			exit();
		}
		else
		{
			$qaHeadingText	=  "MARKED CUSTOMER ORDER AS QA DONE";

			if(isset($_GET['replyId']))
			{
				$replyId		=	(int)$_GET['replyId'];
				
				if(!empty($replyId))
				{
					$orderObj->markOrderQaDone($orderId,$replyId,$customerId,$s_employeeId);
				}

				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES."/pdf-completed-qa-orders.php");
				exit();
			}
		}

	}
?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
<tr>
	<td colspan="7" height="20"></td>
</tr>
<tr>
	<td colspan="8" class="heading1">
		:: <?php echo $qaHeadingText;?> ::
	</td>
</tr>
<tr>
	<td colspan="8" height="5"></td>
</tr>
</table>
<?php
	include(SITE_ROOT_EMPLOYEES	. "/includes/view-customer-order1.php");

	$totalChecklistOrderTotal		=	@mysql_result(dbQuery("SELECT checklistOrderTotal FROM members WHERE memberId=$customerId"),0);
	$totalPoorAverageChecklistTotal	=	@mysql_result(dbQuery("SELECT poorAverageChecklistTotal FROM employee_details WHERE employeeId=$acceptedBy"),0);

	$isRequiredChecklistText		=	"";

	if($totalChecklistOrderTotal != 0 || $totalPoorAverageChecklistTotal != 0)
	{
		$needToDoQaChecklist		=	1;

		if(!empty($totalChecklistOrderTotal))
		{
			$isRequiredChecklistText.=	"This QA check list is required for this customer for at least ".$totalChecklistOrderTotal." times.<br><br>";
		}
		if(!empty($totalPoorAverageChecklistTotal))
		{
			$isRequiredChecklistText.=	"This QA check list is required for you for at least ".$totalPoorAverageChecklistTotal." times.";
		}
	}
	else
	{
		$needToDoQaChecklist		=	0;
	}

	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		//pr($_REQUEST);
		
		$qaChecked			=	makeDBSafe($qaChecked);
		$errorCorrected		=	makeDBSafe($errorCorrected);
		$feedbackToEmployee	=	makeDBSafe($feedbackToEmployee);

		if($isChecklistAvailabale	==	1)
		{
			if($needToDoQaChecklist	==	1)
			{
				if(isset($_POST['readChecklist']))
				{
					$a_readChecklist	=	$_POST['readChecklist'];

					$countTotalChecked	=	count($a_readChecklist);

					if($countTotalChecked < $totalChecklistExists)
					{
						$validator ->setError("Please complete the checklist.");
					}

				}
				else
				{
					$validator ->setError("Please complete the checklist.");
				}
			}
			else
			{
				if(isset($_POST['readChecklist']))
				{
					$a_readChecklist	=	$_POST['readChecklist'];
				}
				else
				{
					$a_readChecklist	=	array();
				}
			}
		}
		else
		{
			$a_readChecklist	=	array();
		}

		$dataValid	 =	$validator ->isDataValid();
		if($dataValid)
		{	
			$is_marked_as_postpaid	=	false;
			dbQuery("UPDATE members SET lastOrderCompletedOn='".CURRENT_DATE_INDIA."' WHERE memberId=$customerId");
			
			$orderObj->markOrderQaDone($orderId,$replyId,$customerId,$s_employeeId);
			$isRevertedDueToEta		=	0;// THIS IS TO CHECK WHETHER WE DID REFUND FOR LATE REPORT

			/////////////////////////////////////////////////////////////////////////////////////////
			/*********** THIS CHECK IF ORDER TAT EXPIRED THAN REFUND THE REMAING AMOUNT ************/
			if($isRushOrder	!=	2){
				
				$completedOrderTimeTaken	=	@mysql_result(dbQuery("SELECT beforeAfterTimingMin FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND orderId=$orderId AND isCompletedOnTime=2 AND memberId=$customerId AND beforeAfterTimingMin > 120 AND isRushOrder=$isRushOrder"),0);

				if(!empty($completedOrderTimeTaken)){
					
					$a_allOrdersPrice			=	$memberObj->getCustomersAllOrderPrice($customerId,$nowDateIndia);
					$postOrderCost				=	$a_allOrdersPrice[2];
					if($orderType				==	19){
						$postOrderCost			=	$a_allOrdersPrice[4];
					}
					elseif($orderType			==	20){
						$postOrderCost			=	$a_allOrdersPrice[6];
					}

					$isRevertedDueToEta			=	1;
					$extraAdjustColumn			=	",cost='$postOrderCost'";

					$existingPostOrderCost		=	@mysql_result(dbQuery("SELECT postOrderCost FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND orderId=$orderId AND isCompletedOnTime=2 AND memberId=$customerId AND isRushOrder=$isRushOrder"),0);
					if(empty($existingPostOrderCost)){
						$existingPostOrderCost	=	0;
					}

					$transactionsMoney			=	$existingPostOrderCost-$postOrderCost;
					$transactionsMoney			=	round($transactionsMoney,2);

					/////////////////// REVERT IF ORDER IS WALLET PAYMENT //////////////////////
					if($isPaidThroughWallet			==  "yes" && !empty($walletAccountId))
					{
						$extraAdjustColumn			=	"";
						$walletAmount				=	@mysql_result(dbQuery("SELECT amount FROM wallet_master WHERE memberId=$customerId"),0);
						if(empty($walletAmount)){
							$walletAmount			=	0;
						}
						

						if(!empty($transactionsMoney) && $existingPostOrderCost > $postOrderCost){
							
							$balanceWalletMoney		=	$walletAmount+$transactionsMoney;
							$balanceWalletMoney		=	round($balanceWalletMoney,2);
							$t_txt_orderAddress     =   makeDBSafe($orderAddress);
							
							dbQuery("UPDATE wallet_transactions SET amount='$postOrderCost',orderAddress='$t_txt_orderAddress',currentBalance='$balanceWalletMoney' WHERE orderId=$orderId AND transactionId=$walletAccountId");

							dbQuery("UPDATE wallet_master SET amount='$balanceWalletMoney' WHERE memberId=$customerId");
						}
					}
					if($isPrepaidOrder == 1 && !empty($prepaidTransactionId) && empty($advancedPaymentId) && $prepiadPaymentThrough == "paypal")
					{
						//////////////////////// SENDING ADMIN EMAIL TO REFUND PAYPAL MONEY /////////
						$refundMoney		=	
						$paypalTransactionId= @mysql_result(dbQuery("SELECT txnId FROM master_paypal_transactions WHERE referenceId=$prepaidTransactionId"),0);

						$managerEmployeeEmailSubject	=	"Manual Refund Required: $ ".$memberObj->getMoneyExponent($transactionsMoney)." - ".$customerName.", ".$orderAddress;
							$managerEmployeeFromName		=	"Deleted Prepaid Order";

							$table					=	"<table width='98%' align='center' cellpadding='3' cellspacing='3'>
								<tr>
									<td colspan='3'>
										Dear Admin,<br /><br />
										Customer ".$customerName." order - ".$orderAddress." price has been changed due to delay in estimated time. Please make refund of  prepaid $ ".$transactionsMoney." payment made through <b>Paypal</b>.<br />Here is the details of the order :<br /><br />
									</td>
								</tr>
								<tr>
									<td width='30%'>Order Address</td>
									<td width='3%'>:</td>
									<td>".$orderAddress."</td>
								</tr>
								<tr>
									<td>Order Type</td>
									<td>:</td>
									<td>".$orderText."</td>
								</tr>
								<tr>
									<td>Transaction ID</td>
									<td>:</td>
									<td><a href='https://www.paypal.com/us/cgi-bin/webscr?cmd=_view-a-trans&id=$paypalTransactionId' target='_blank'><u>".$paypalTransactionId."</u></a></td>
								</tr>
								<tr>
									<td>Order Date</td>
									<td>:</td>
									<td>".showDate($orderPlacedDate)." IST & ".showDate($orderPlacedCustomerDate)." EST</td>
								</tr>
							</table>";

							$a_templateData			=	array("{bodyMatter}"=>$table);

							$toEmail				=	"hemant@ieimpact.net"; 
							//$toEmail				=	"gaurabsiva1@gmail.com"; 
							$uniqueTemplateName		=	"TEMPLATE_SENDING_NEW_SIMPLEE_MESSAGE";
							include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

							$managerEmployeeEmailSubject	=	"";
							$managerEmployeeFromName		=	"";
					}
					$exCol				   =	"";
					if(!empty($prepaidOrderPrice)){
						$exCol			   =	",prepaidOrderPrice='$postOrderCost'";
						$prepaidOrderPrice = $postOrderCost;
					}
					dbQuery("UPDATE members_orders SET postOrderCost='$postOrderCost',rushOrderExtraMoney='0',isExceededEtaAdjust=1".$exCol.$extraAdjustColumn." WHERE orderId=$orderId AND memberId=$customerId");
				}
			}
			////////////////////////////////////////////////////////////////////////////////////////

			dbQuery("UPDATE members_orders SET qaDoneById=$s_employeeId,qaDoneByName='$s_employeeName',completedTime='".CURRENT_TIME_INDIA."',completedTimeEst='".CURRENT_TIME_CUSTOMER_ZONE."' WHERE orderId=$orderId AND memberId=$customerId");

			$employeeObj->makeTargetOrderProcessedQa($s_employeeId,$s_employeeName,$currentM,$currentY,2);

			$totalCustomerOrders		=	$orderObj->getCustomerTotalCompletedOrders($customerId);
			if(empty($totalCustomerOrders))
			{
				$totalCustomerOrders	=	0;
			}
			if($totalCustomerOrders		<= 3)
			{
				$needFeedBackMessage	=	"<b>Need your attention :</b> We need your feedback in order to complete this order.<br>";
				dbQuery("UPDATE members_orders SET status=5 WHERE orderId=$orderId");
				
			}

			$timeSpentQa			=	timeBetweenTwoTimes($qaAcceptedDate,$qaAcceptedTime,$nowDateIndia,$nowTimeIndia);
			$timeSpentQa			=	round($timeSpentQa,0);
						
			dbQuery("UPDATE members_orders_reply SET timeSpentQa=$timeSpentQa WHERE replyId=$replyId AND orderId=$orderId");

			if($isChecklistAvailabale	==	1 && !empty($a_readChecklist))
			{
				$orderObj->setQAChecklistMarked($orderId,$s_employeeId,$a_readChecklist);
				if($needToDoQaChecklist	==	1)
				{
					if(!empty($totalChecklistOrderTotal))
					{
						if($totalChecklistOrderTotal > 1)
						{
							$remaining		=	$totalChecklistOrderTotal-1;
						}
						else
						{
							$remaining		=	0;
						}

						dbQuery("UPDATE members SET checklistOrderTotal=$remaining WHERE memberId=$customerId");
					}
					if(!empty($totalPoorAverageChecklistTotal))
					{
						if($totalPoorAverageChecklistTotal > 1)
						{
							$remainingCheck		=	$totalPoorAverageChecklistTotal-1;
						}
						else
						{
							$remainingCheck		=	0;
						}

						dbQuery("UPDATE employee_details SET poorAverageChecklistTotal=$remainingCheck WHERE employeeId=$acceptedBy");
					}
				}
			}

			$totalFreeOrdersLeft=	@mysql_result(dbQuery("SELECT customerFreeOrders FROM members WHERE memberId=$customerId"),0);
			////////////////////////////////////////////////////////////////////////////////////////////
			////////////////////////////////////////////////////////////////////////////////////////////
			/****** MAKE FREE ORDERS TOTAL 0 IF ORDER IS PREPAID AND TOLTAL FREE ORDER NOT EMPTY ******/
			////////////////////////////////////////////////////////////////////////////////////////////
			////////////////////////////////////////////////////////////////////////////////////////////
			/******/if(!empty($totalFreeOrdersLeft) && !empty($prepaidTransactionId)) //////////////////
			/******/{																  //////////////////
			/******/	$totalFreeOrdersLeft			=	0;						  //////////////////
			/******/}																  //////////////////
			////////////////////////////////////////////////////////////////////////////////////////////
			////////////////////////////////////////////////////////////////////////////////////////////
			////////////////////////////////////////////////////////////////////////////////////////////

			if(empty($totalFreeOrdersLeft))
			{
				$totalFreeOrdersLeft			=	0;
			}
			else
			{
				$freeEmailSub	=	 "Free order completed:".$customerName.$isAlamodeCustomerText." - ".$orderAddress;
				$freeEmailPrice	=	 "Payment info: Postpaid";
				if(!empty($prepaidTransactionId))
				{
					$paypalTransactionId= @mysql_result(dbQuery("SELECT txnId FROM master_paypal_transactions WHERE referenceId=$prepaidTransactionId"),0);
					
					$freeEmailSub		=	 "Order was given free becuase of free credits available : ".$customerName." ".$orderAddress;

					$freeEmailPrice	    =	 "Payment info: Prepaid through paypal<br />Order price: $ ".$prepaidOrderPrice."<br /> Transaction id: ".$paypalTransactionId;
					if($prepiadPaymentThrough	==	"creditcard")
					{
						$freeEmailSub	=	 "Order was given free becuase of free credits available : ".$customerName." ".$orderAddress;

						$freeEmailPrice	=	 "Payment info: Prepaid through card/account<br />Order price: $ ".$prepaidOrderPrice."<br /> Transaction id: ".$prepaidTransactionId;
					}
				}
				/************************ SENDING EMAIL TO MANAGER **********************/
				$toEmail			=	"hemant@ieimpact.net";
				//$toEmail			=	"gaurabsiva1@gmail.com";
				$freeEmailBody		=	$freeEmailSub."<br /><br />Customer Name: ".$customerName." <br />Order: ".$orderAddress." <br />Price: FREE.<br />".$freeEmailPrice;
				$uniqueTemplateName	=	"TEMPLATE_SENDING_SIMPLEE_CUSTOMER_MESSAGE";
				
				$a_templateSubject	=	array("{emailSubject}"=>$freeEmailSub);

				$a_templateData		=	array("{completeName}"=>"John Bowen","{emailBody}"=>$freeEmailBody);

				include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

			}

			if($isPrepaidOrder == 1 && !empty($prepaidTransactionId) && empty($advancedPaymentId))
			{
				$paymentType				=	1;
				$statusText					=	"Successfully Advanced Payment Made Through Paypal";
				if($prepiadPaymentThrough	==	"creditcard")
				{
					$paymentType			=	2;
					$statusText				=	"Successfully Advanced Payment Made Through Credit Card";
				}
				
				dbQuery("INSERT INTO advance_payment_money SET memberId=$customerId,paymentType=$paymentType,paymentAmount='$prepaidOrderPrice',paymentStatus=2,date='".$nowDateIndia."',time='".$nowTimeIndia."',ip='".VISITOR_IP_ADDRESS."',statusText='$statusText',excatProceedingIstDate='".$nowDateIndia."',excatIstProceedingEstTime='".$nowTimeIndia."',excatProceedingEstDate='".$customer_zone_date."',excatProceedingEstTime='".$customer_zone_time."',isForPrepiadOrder=1,paidOn='".$nowDateIndia."',paidTime='".$nowTimeIndia."',paidIP='".VISITOR_IP_ADDRESS."'");

				$advanced_payment_id	=	mysql_insert_id();

				list($p_year,$p_month,$p_day)		=	explode("-",$nowDateIndia);

				if($receivedId = $memberObj->isExistsMonthAdvanceMoney($customerId,$p_month,$p_year))
				{
					dbQuery("UPDATE total_advance_month_money SET amountRceived=amountRceived+$prepaidOrderPrice WHERE receivedId=$receivedId AND memberId=$customerId");
				}
				else
				{
					dbQuery("INSERT INTO total_advance_month_money SET amountRceived='$prepaidOrderPrice',memberId=$customerId,month=$p_month,year=$p_year");
				}

				dbQuery("UPDATE members_orders SET advancedPaymentId=$advanced_payment_id WHERE memberId=$customerId AND orderId=$orderId");
			}
			elseif($isPrepaidOrder == 0 && !empty($stripeChargeId) && $paymentGateway == 'Stripe')
			{
				//////////////////// THIS BLOCK IS FOR CHARGE STRIPE PAYMENT //////////////////
				if($isChangedPrice == 1 && $ignorePrepaidCapture == 1)
				{
					/******************************************************************/
					//****** UPDATE ORDER MAKING ADVANCED PAYMENT FOR THIS ORDER ******/
					$order_cost_money			=	@mysql_result(dbQuery("SELECT postOrderCost FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND orderId=$orderId AND memberId=$customerId"),0);
					$paymentType				=	2;
					$statusText					=	"Successfully Advanced Payment Made Through Credit Card";
					if($prepiadPaymentThrough	==	"creditcard")
					{
						$isAmaxTransactions			=	0;
						if(!empty($usingStripeAccountId)){
							$transactionCardType	=	@mysql_result(dbQuery("SELECT cardType FROM auto_remember_stripe_account_details WHERE cardType <> '' AND accountId=$usingStripeAccountId AND memberId=$customerId"),0);
							if($transactionCardType == "American Express"){
								$isAmaxTransactions	=	1;
							}
						}
						
						$memberObj->addEditCustomersAllTransaction($order_cost_money,$customerId,2,'Made Prepaid Payment Through Credit Card','Made Prepaid Payment Through Credit Card',$isAmaxTransactions);
					}
					
					
					dbQuery("INSERT INTO advance_payment_money SET memberId=$customerId,paymentType=$paymentType,paymentAmount='$order_cost_money',paymentStatus=2,date='".$nowDateIndia."',time='".$nowTimeIndia."',ip='".VISITOR_IP_ADDRESS."',statusText='$statusText',excatProceedingIstDate='".$nowDateIndia."',excatIstProceedingEstTime='".$nowTimeIndia."',excatProceedingEstDate='".$customer_zone_date."',excatProceedingEstTime='".$customer_zone_time."',isForPrepiadOrder=1,paidOn='".$nowDateIndia."',paidTime='".$nowTimeIndia."',paidIP='".VISITOR_IP_ADDRESS."'");

					$advanced_payment_id				=	mysql_insert_id();

					list($p_year,$p_month,$p_day)		=	explode("-",$nowDateIndia);

					if($receivedId = $memberObj->isExistsMonthAdvanceMoney($customerId,$p_month,$p_year))
					{
						dbQuery("UPDATE total_advance_month_money SET amountRceived=amountRceived+$order_cost_money WHERE receivedId=$receivedId AND memberId=$customerId");
					}
					else
					{
						dbQuery("INSERT INTO total_advance_month_money SET amountRceived='$order_cost_money',memberId=$customerId,month=$p_month,year=$p_year");
					}

					dbQuery("UPDATE members_orders SET advancedPaymentId=$advanced_payment_id,isPrepaidOrder=1,prepaidOrderPrice='$order_cost_money' WHERE memberId=$customerId AND orderId=$orderId");
				}
				else
				{
					if(!empty($totalFreeOrdersLeft))
					{
						require_once(SITE_ROOT.'/stripe/init.php');
						\Stripe\Stripe::setApiKey(STRIPE_SECREAT_KEY);

						try{
							$re = \Stripe\Refund::create(array($stripeChargeId));	
						}
						catch(Exception $e){
						   								   
						}	
						
						dbQuery("UPDATE members_orders SET chargeId='',balance_transaction='',prepaidOrderPrice='',prepiadPaymentThrough='',paymentGateway='',usingStripeAccountId=0 WHERE memberId=$customerId AND orderId=$orderId");
					}
					else
					{
						require_once(SITE_ROOT.'/stripe/init.php');
						\Stripe\Stripe::setApiKey(STRIPE_SECREAT_KEY);						

						$order_cost_money	=	@mysql_result(dbQuery("SELECT postOrderCost FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND orderId=$orderId AND memberId=$customerId"),0);
						
						
						

						try{

							$order_cost_money_cent	=	$order_cost_money*100;
							$charge_stripe			=	\Stripe\Charge::retrieve($stripeChargeId);
							$charge_result          =	$charge_stripe->capture(array('amount' => $order_cost_money_cent));

							$balance_transaction	=	$charge_result['balance_transaction'];
							$order_cost_money		=	$memberObj->getMoneyExponent($order_cost_money);

							/************************************************************/
							//**UPDATE ORDER MAKING ADVANCED PAYMENT THROUGH STRIPE FOR THIS ORDER**/
							$paymentType				=	2;
							$statusText					=	"Successfully Advanced Payment Made Through Credit Card";
							if($prepiadPaymentThrough	==	"creditcard")
							{									

								$isAmaxTransactions			=	0;
								if(!empty($usingStripeAccountId)){
									$transactionCardType	=	@mysql_result(dbQuery("SELECT cardType FROM auto_remember_stripe_account_details WHERE cardType <> '' AND accountId=$usingStripeAccountId AND memberId=$customerId"),0);
									if($transactionCardType == "American Express"){
										$isAmaxTransactions	=	1;
									}
								}
								
								$memberObj->addEditCustomersAllTransaction($order_cost_money,$customerId,2,'Made Prepaid Payment Through Credit Card','Made Prepaid Payment Through Credit Card',$isAmaxTransactions);
							}
							
							
							dbQuery("INSERT INTO advance_payment_money SET memberId=$customerId,paymentType=$paymentType,paymentAmount='$order_cost_money',paymentStatus=2,date='".$nowDateIndia."',time='".$nowTimeIndia."',ip='".VISITOR_IP_ADDRESS."',statusText='$statusText',excatProceedingIstDate='".$nowDateIndia."',excatIstProceedingEstTime='".$nowTimeIndia."',excatProceedingEstDate='".$customer_zone_date."',excatProceedingEstTime='".$customer_zone_time."',isForPrepiadOrder=1,paidOn='".$nowDateIndia."',paidTime='".$nowTimeIndia."',paidIP='".VISITOR_IP_ADDRESS."'");

							$advanced_payment_id				=	mysql_insert_id();

							list($p_year,$p_month,$p_day)		=	explode("-",$nowDateIndia);

							if($receivedId = $memberObj->isExistsMonthAdvanceMoney($customerId,$p_month,$p_year))
							{
								dbQuery("UPDATE total_advance_month_money SET amountRceived=amountRceived+$order_cost_money WHERE receivedId=$receivedId AND memberId=$customerId");
							}
							else
							{
								dbQuery("INSERT INTO total_advance_month_money SET amountRceived='$order_cost_money',memberId=$customerId,month=$p_month,year=$p_year");
							}

							dbQuery("UPDATE members_orders SET advancedPaymentId=$advanced_payment_id,isPrepaidOrder=1,prepaidOrderPrice='$order_cost_money',balance_transaction='$balance_transaction' WHERE memberId=$customerId AND orderId=$orderId");

						}
						catch(Exception $e){
						    $is_marked_as_postpaid	=	true;
							$cardAccountErrorMsg	=	$e->getMessage();	
							$cardAccountErrorMsg   .=   "&nbsp;&nbsp;(KASE I)";
						}						
						if($is_marked_as_postpaid   ==  true)
						{
							$isSentFailPaymentEmail	=	true;
							//MAKING ORDER AS POSTPAID
							dbQuery("UPDATE members_orders SET chargeId='',balance_transaction='',prepaidOrderPrice='',prepiadPaymentThrough='',paymentGateway='Stripe',isFailedCapture=1,failedTransactionId='$stripeChargeId' WHERE memberId=$customerId AND orderId=$orderId");
						}
						
					}
				}
				/////////////////////////////////////////////////////////////////////////////////////

			}
			elseif($isPrepaidOrder == 0 && !empty($prepaidTransactionId) && empty($advancedPaymentId))
			{
				if($isChangedPrice == 1 && $ignorePrepaidCapture == 1)
				{
					/******************************************************************/
					//****** UPDATE ORDER MAKING ADVANCED PAYMENT FOR THIS ORDER ******/
					$order_cost_money			=	@mysql_result(dbQuery("SELECT postOrderCost FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND orderId=$orderId AND memberId=$customerId"),0);
					$paymentType				=	2;
					$statusText					=	"Successfully Advanced Payment Made Through Credit Card";
					if($prepiadPaymentThrough	==	"creditcard")
					{
						$isAmaxTransactions			=	0;
						if(!empty($customerPaymentProfileId)){
							$transactionCardType	=	@mysql_result(dbQuery("SELECT cardType FROM auto_remember_account_details WHERE cardType <> '' AND customerPaymentProfileId=$customerPaymentProfileId AND memberId=$customerId"),0);
							if($transactionCardType == "American Express"){
								$isAmaxTransactions	=	1;
							}
						}
						
						$memberObj->addEditCustomersAllTransaction($order_cost_money,$customerId,2,'Made Prepaid Payment Through Credit Card','Made Prepaid Payment Through Credit Card',$isAmaxTransactions);
					}
					else
					{
						$memberObj->addEditCustomersAllTransaction($order_cost_money,$customerId,3,'Made Prepaid Payment Through Credit eCheck','Made Prepaid Payment Through Credit eCheck');
					}
					
					dbQuery("INSERT INTO advance_payment_money SET memberId=$customerId,paymentType=$paymentType,paymentAmount='$order_cost_money',paymentStatus=2,date='".$nowDateIndia."',time='".$nowTimeIndia."',ip='".VISITOR_IP_ADDRESS."',statusText='$statusText',excatProceedingIstDate='".$nowDateIndia."',excatIstProceedingEstTime='".$nowTimeIndia."',excatProceedingEstDate='".$customer_zone_date."',excatProceedingEstTime='".$customer_zone_time."',isForPrepiadOrder=1,paidOn='".$nowDateIndia."',paidTime='".$nowTimeIndia."',paidIP='".VISITOR_IP_ADDRESS."'");

					$advanced_payment_id				=	mysql_insert_id();

					list($p_year,$p_month,$p_day)		=	explode("-",$nowDateIndia);

					if($receivedId = $memberObj->isExistsMonthAdvanceMoney($customerId,$p_month,$p_year))
					{
						dbQuery("UPDATE total_advance_month_money SET amountRceived=amountRceived+$order_cost_money WHERE receivedId=$receivedId AND memberId=$customerId");
					}
					else
					{
						dbQuery("INSERT INTO total_advance_month_money SET amountRceived='$order_cost_money',memberId=$customerId,month=$p_month,year=$p_year");
					}

					dbQuery("UPDATE members_orders SET advancedPaymentId=$advanced_payment_id,isPrepaidOrder=1,prepaidOrderPrice='$order_cost_money' WHERE memberId=$customerId AND orderId=$orderId");
				}
				else
				{
					if(!empty($totalFreeOrdersLeft))
					{
						include(SITE_ROOT	.   "/classes/vars.php");
						include(SITE_ROOT	.   "/classes/util.php");
						
						$content =
						"<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
						"<createCustomerProfileTransactionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
						MerchantAuthenticationBlock().
						"<transaction>".
						"<profileTransVoid>".
						"<customerProfileId>".$customerProfileId."</customerProfileId>".
						"<customerPaymentProfileId>".$customerPaymentProfileId."</customerPaymentProfileId>".
						"<customerShippingAddressId>".$customerShippingAddressId."</customerShippingAddressId>".
						"<transId>".$prepaidTransactionId."</transId>".
						"</profileTransVoid>".
						"</transaction>".
						"</createCustomerProfileTransactionRequest>";

						$response       =     send_xml_request($content);
						$parsedresponse =     parse_api_response($response);

						dbQuery("UPDATE members_orders SET prepaidTransactionId=0,usedDebitAccountId=0,customerProfileId='',customerShippingAddressId='',customerPaymentProfileId='',creditCardNumberMasked='',invNo='',prepaidOrderPrice='',prepiadPaymentThrough='' WHERE memberId=$customerId AND orderId=$orderId");
					}
					else
					{
						include(SITE_ROOT	.   "/classes/vars.php");
						include(SITE_ROOT	.   "/classes/util.php");

						$order_cost_money	=	@mysql_result(dbQuery("SELECT postOrderCost FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND orderId=$orderId AND memberId=$customerId"),0);
						
						$order_cost_money		=	$memberObj->getMoneyExponent($order_cost_money);
						$is_marked_as_postpaid	=	false;

						$content =
							"<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
							"<createCustomerProfileTransactionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
							MerchantAuthenticationBlock().
							"<transaction>".
							"<profileTransPriorAuthCapture>".
							"<amount>".$order_cost_money."</amount>".
							"<customerProfileId>".$customerProfileId."</customerProfileId>".
							"<customerPaymentProfileId>".$customerPaymentProfileId."</customerPaymentProfileId>".
							"<customerShippingAddressId>".$customerShippingAddressId."</customerShippingAddressId>".
							"<transId>".$prepaidTransactionId."</transId>".
							"</profileTransPriorAuthCapture>".
							"</transaction>".
							"<extraOptions><![CDATA[]]></extraOptions>".
							"</createCustomerProfileTransactionRequest>";

							$response		= send_xml_request($content);

							/////////////////////////////////////////////////////////////////////////////
							//////////////////// BLOCK TO ADDING AUTHORIZE RESPONSE /////////////////////
							/////////////////////////////////////////////////////////////////////////////
							$response_xml	=   @simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
							$response_json	=   @json_encode($response_xml);
							$response_json	=   @json_decode($response_json,TRUE);
							$email_authorize_response = @recursive_implode($response_json);
							$addedText		=	"QA Done Order - ".$orderAddress;
							$commonClass	= $commonClass->trackAuthorizeResponse($response_json,'Authorize.net',$orderId,$customerId,$addedText);
							////////////////////////////////////////////////////////////////////////////
							////////////////////////////////////////////////////////////////////////////
							$parsedresponse = parse_api_response($response);
							if ("Ok"        ==    $parsedresponse->messages->resultCode) {
								if (isset($parsedresponse->directResponse)) {

									$directResponseFields = explode(",", $parsedresponse->directResponse);
									$responseCode = $directResponseFields[0]; // 1 = Approved 2 = Declined 3 = Error
									$responseReasonCode = $directResponseFields[2]; // See http://www.authorize.net/support/AIM_guide.pdf
									$responseReasonText = $directResponseFields[3];
									$approvalCode = $directResponseFields[4]; // Authorization code
									$transId = $directResponseFields[6];
									
									if($responseCode				==	"1")
									{
										/************************************************************/
										//**  UPDATE ORDER MAKING ADVANCED PAYMENT FOR THIS ORDER  **/
										$paymentType				=	2;
										$statusText					=	"Successfully Advanced Payment Made Through Credit Card";
										if($prepiadPaymentThrough	==	"creditcard")
										{											

											$isAmaxTransactions			=	0;
											if(!empty($customerPaymentProfileId)){
												$transactionCardType	=	@mysql_result(dbQuery("SELECT cardType FROM auto_remember_account_details WHERE cardType <> '' AND customerPaymentProfileId=$customerPaymentProfileId AND memberId=$customerId"),0);
												if($transactionCardType == "American Express"){
													$isAmaxTransactions	=	1;
												}
											}
											
											$memberObj->addEditCustomersAllTransaction($order_cost_money,$customerId,2,'Made Prepaid Payment Through Credit Card','Made Prepaid Payment Through Credit Card',$isAmaxTransactions);
										}
										else
										{
											$memberObj->addEditCustomersAllTransaction($order_cost_money,$customerId,3,'Made Prepaid Payment Through Credit eCheck','Made Prepaid Payment Through Credit eCheck');
										}
										
										dbQuery("INSERT INTO advance_payment_money SET memberId=$customerId,paymentType=$paymentType,paymentAmount='$order_cost_money',paymentStatus=2,date='".$nowDateIndia."',time='".$nowTimeIndia."',ip='".VISITOR_IP_ADDRESS."',statusText='$statusText',excatProceedingIstDate='".$nowDateIndia."',excatIstProceedingEstTime='".$nowTimeIndia."',excatProceedingEstDate='".$customer_zone_date."',excatProceedingEstTime='".$customer_zone_time."',isForPrepiadOrder=1,paidOn='".$nowDateIndia."',paidTime='".$nowTimeIndia."',paidIP='".VISITOR_IP_ADDRESS."'");

										$advanced_payment_id				=	mysql_insert_id();

										list($p_year,$p_month,$p_day)		=	explode("-",$nowDateIndia);

										if($receivedId = $memberObj->isExistsMonthAdvanceMoney($customerId,$p_month,$p_year))
										{
											dbQuery("UPDATE total_advance_month_money SET amountRceived=amountRceived+$order_cost_money WHERE receivedId=$receivedId AND memberId=$customerId");
										}
										else
										{
											dbQuery("INSERT INTO total_advance_month_money SET amountRceived='$order_cost_money',memberId=$customerId,month=$p_month,year=$p_year");
										}

										dbQuery("UPDATE members_orders SET advancedPaymentId=$advanced_payment_id,isPrepaidOrder=1,prepaidOrderPrice='$order_cost_money' WHERE memberId=$customerId AND orderId=$orderId");
									}
									else
									{
										$is_marked_as_postpaid	=	true;	
										$cardAccountErrorMsg	=	"Fail to get desired response code.&nbsp;&nbsp;".$email_authorize_response;
									}
								}
								else
								{								
									$is_marked_as_postpaid	=	true;
									$cardAccountErrorMsg	=	"Fail to get transactions ID.&nbsp;&nbsp;".$email_authorize_response;
								}
							}
							else
							{
								$is_marked_as_postpaid	=	true;

								$cardAccountErrorMsg	=	"";
								$errorCode				=	$parsedresponse->messages->message->code;
								foreach ($parsedresponse->messages->message as $msg) {
									$cardAccountErrorMsg.= htmlspecialchars($msg->text)."&nbsp;(Error Code - ".$errorCode.")<br>";
								}
								$cardAccountErrorMsg   .=   "&nbsp;&nbsp;".$email_authorize_response;
							}
						
							if($is_marked_as_postpaid   ==  true)
							{
								$isSentFailPaymentEmail	=	true;
								//MAKING ORDER AS POSTPAID
								dbQuery("UPDATE members_orders SET prepaidTransactionId=0,prepaidOrderPrice='',prepiadPaymentThrough='',isFailedCapture=1,paymentGateway='Authorize.net',failedTransactionId='$prepaidTransactionId' WHERE memberId=$customerId AND orderId=$orderId");
							}
						
					}
				}
			}

			if(!empty($qaRateMessage))
			{
				$qaRateMessage	=	makeDBSafe($qaRateMessage);
				
				dbQuery("INSERT employee_miscellaneous_details SET orderId=$orderId,qaRateMessage='$qaRateMessage',addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
			}
				
			$a_managerEmails	=	$orderObj->getMangersOnlyEmails();

			if($result			=	$orderObj->getOrderDetails($orderId,$customerId))
			{
				$row			=	mysql_fetch_assoc($result);
				$orderAddress	=	stripslashes($row['orderAddress']);
				$orderType		=	$row['orderType'];
				$customersOwnOrderText			=	stripslashes($row['customersOwnOrderText']);
				$orderAddedOn	=	$row['orderAddedOn'];
				$firstName		=	stripslashes($row['firstName']);
				$lastName		=	stripslashes($row['lastName']);
				$customerEmail	=	$row['email'];
				$customerSecondaryEmail	=	$row['secondaryEmail'];
				$hasReceiveEmails=	$row['noEmails'];
				$t_orderAddedOn	=	showDate($orderAddedOn);

				$orderText		=	$a_customerOrder[$orderType];
				if($orderType	==	6 && !empty($customersOwnOrderText))
				{
					$orderText  =	$orderText."&nbsp;(".$customersOwnOrderText.")";
				}
				$customerName	=   $firstName." ".$lastName;
				$customerName	=	ucwords($customerName);

				if(!empty($isAlamodeOrder) && !empty($aLamodeCustomerID))
				{
					$memberObj->updateMemberFreeCompletedOrder($customerId);
				}
				else
				{
					if($isChangedPrice	==	0 && $isPaidThroughWallet == "no" && $isRevertedDueToEta == 0)
					{
						$memberObj->updateMemberOrderPriceNew($orderType,$orderId,$customerId,$orderAddedOn);
					}
				}
				
				//$t_moneyPerOrder=	$memberObj->getSingleOrderPrice($orderType,$customerId,$orderAddedOn);

				//dbQuery("UPDATE members_orders SET cost='$t_moneyPerOrder' WHERE orderId=$orderId");				
				$orderAcceptedBy		=	$orderObj->getOrderAcceptedBY($orderId,$customerId);				

				if(!empty($orderAcceptedBy))
				{
					$acceptedByName		=	$employeeObj->getEmployeeName($orderAcceptedBy);
					if(empty($acceptedByName))
					{
						$acceptedByName	=	"Unknown";
					}
					$slashEmployee		=	makeDBSafe($acceptedByName);
					$doneAcceptedId		=	$employeeObj->isExistTotalCustomerOrdersAccepted($customerId,$orderAcceptedBy);
					if(empty($doneAcceptedId))
					{
						dbQuery("INSERT INTO customers_total_orders_done_by SET memberId=$customerId,employeeId=$orderAcceptedBy,totalAccepted=1,employeeName='$slashEmployee'");
					}
					else
					{
						dbQuery("UPDATE customers_total_orders_done_by SET totalAccepted=totalAccepted+1 WHERE memberId=$customerId AND employeeId=$orderAcceptedBy AND doneId=$doneAcceptedId");
					}
				}
				else
				{
					$acceptedByName			=	"Unknown";
				}
		
				$replyInstructions			=	@mysql_result(dbQuery("SELECT replyInstructions FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId"),0);
				$replyInstructions			=	stripslashes($replyInstructions);
				$replyInstructions			=	nl2br($replyInstructions);
				
				$hasAttachment				=	0;
				$sendingFileAttachmentMsg	=	"";

				//if($isSentFailPaymentEmail  ==  true)
				if($is_marked_as_postpaid  ==  true)
				{
					/************************ SENDING EMAIL TO MANAGER **********************/
					$toEmail				=	"john@ieimpact.net";
					//$toEmail				=	"gaurabsiva1@gmail.com";
					$credit_card_email_msg	=	"An order from ".$customerName." which address is - ".$orderAddress." become postpaid while employee completed beacuse of the following reason :- <br />".$cardAccountErrorMsg;
					$credit_card_email_sub	=	$customerName." order become postpaid";

					$a_templateSubject		=	array("{emailSubject}"=>$credit_card_email_sub);

					$a_templateData			=	array("{completeName}"=>"John","{emailBody}"=>$credit_card_email_msg);
					$uniqueTemplateName		=	"TEMPLATE_SENDING_SIMPLEE_CUSTOMER_MESSAGE";

					$managerEmployeeFromBcc	=   "gaurabsiva1@gmail.com,gaurabsiva1@yahoo.co.in";

					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
				}
				////////////////// REDEFINE FILES DETAILS AFTER SUBMITTED //////////////////////
				include(SITE_ROOT_EMPLOYEES."/includes/completed-email-links-attach-files.php");
				///////////////////////////////////////////////////////////////////////////////

				if($isReplyFileInEmail		==	1)
				{
					if(!empty($a_attachmentPath))
					{						
						if(!empty($totalAmountEmailFileSize) && $totalAmountEmailFileSize <= 7340032)
						{
							$hasAttachment			 =	1;
							$sendingFileAttachmentMsg=	"Please note: Files are also attached in this email.";
						}
						else
						{
							$sendingFileAttachmentMsg	=	"Note: Failed to send files by email becuase size was greater than 7mb.";
							$hasAttachment				=	0;
							$a_attachmentPath			=	array();
							$a_attachmentType			=	array();
							$a_attachmentName			=	array();
						}
					}
				}

				echo $sendingFileAttachmentMsg;
				echo "<br />TOTAl SIZE : ".$totalAmountEmailFileSize;
				echo "<br />HAS ATTACHEMNT : ".$hasAttachment;
				pr($a_attachmentPath);
				pr($a_attachmentType);
				pr($a_attachmentName);

				die();
				if($hasReceiveEmails		 == 0)
				{
					/////////////////// START OF SENDING EMAIL BLOCK/////////////////////////
					$trackEmailOrderNo		 =	"Completed your order No - ".stringReplace(",","",$orderAddress);

					$excellentLink		=	SITE_URL_MEMBERS."/rate-this-order.php?".ORDERID_M_D_5."=".$orderEncryptedId."&code=".$encodeOrderID."&rate=5";

					$goodLink			=	SITE_URL_MEMBERS."/rate-this-order.php?".ORDERID_M_D_5."=".$orderEncryptedId."&code=".$encodeOrderID."&rate=4";

					$fairLink			=	SITE_URL_MEMBERS."/rate-this-order.php?".ORDERID_M_D_5."=".$orderEncryptedId."&code=".$encodeOrderID."&rate=3";

					$poorLink			=	SITE_URL_MEMBERS."/rate-this-order.php?".ORDERID_M_D_5."=".$orderEncryptedId."&code=".$encodeOrderID."&rate=2";

					$awfulLink			=	SITE_URL_MEMBERS."/rate-this-order.php?".ORDERID_M_D_5."=".$orderEncryptedId."&code=".$encodeOrderID."&rate=1";


					$trackEmailImage		 =	$emailTrackObj->addTrackEmailRead($customerEmail,$trackEmailOrderNo,"orders@ieimpact.com",$customerId,1,10,3,$s_employeeName,$s_employeeId);

					$referFriendLink		 =   "";

					if($cutomerTotalOrdersPlaced > 3)
					{
						$referFriendLink	 =   "<a href='".SITE_URL_MEMBERS."/refer-a-friend.php' target='_blank'><img src='".SITE_URL."/images/refer_a_friend-new.jpg' alt='Refer 1 Get $15' title='Refer 1 Get $15' border='0' width='800px' height='90px'></a>";
					}
					
					if($trackEmailImage		!=  "images/white-space.jpg")
					{
						$sendingUniqueCode	 =	stringReplace("mail-t/","",$trackEmailImage);
						$sendingUniqueCode	 =	stringReplace(".jpg","",$sendingUniqueCode);

						dbQuery("UPDATE members_orders_reply SET emailUniqueCode='$sendingUniqueCode' WHERE replyId=$replyId AND orderId=$orderId");
					}

					if(!empty($memberOrderReplyToEmail)){
						$setThisEmailReplyToo			=	$memberOrderReplyToEmail.CUSTOMER_REPLY_EMAIL_TO;//Setting for reply to make customer reply order mesage
						$setThisEmailReplyTooName		=	"ieIMPACT Orders";//Setting for reply to make customer reply order mesage
					}
					else{
						if(!empty($orderEncryptedId))
						{
							$setThisEmailReplyToo	  =	 $orderEncryptedId.CUSTOMER_REPLY_EMAIL_TO;//Setting for reply to make customer reply order mesage
							$setThisEmailReplyTooName =	 "ieIMPACT Orders";//Setting for reply to make customer reply order mesage
						}
					}

					$sendingFileAttachmentMsg .=	$sendingFileAttachmentMsg."<br />Please reply to this email or please use the email address below for any feedback or support with this order.<br />
					Tracking Email Address for this order : ".$setThisEmailReplyToo;
					
					$a_templateData			=	array("{name}"=>$customerName,"{orderNo}"=>$orderAddress,"{orderType}"=>$orderText,"{instructions}"=>$replyInstructions,"{needFeedBackMessage}"=>$needFeedBackMessage,"{showFilesNameInEmail}"=>$showFilesNameInEmail,"{trackEmailImage}"=>$trackEmailImage,"{sendingFileAttachmentMsg}"=>$sendingFileAttachmentMsg,"{referFriendLink}"=>$referFriendLink,"{excellentLink}"=>$excellentLink,"{goodLink}"=>$goodLink,"{fairLink}"=>$fairLink,"{poorLink}"=>$poorLink,"{awfulLink}"=>$awfulLink);

					$a_templateSubject		=	array("{orderAddress}"=>$orderAddress);

					$uniqueTemplateName		=	"TEMPLATE_SENDING_CUSTOMER_ORDER_REPLY_FILE_ATTACHMENT";
					$toEmail				=	$customerEmail;
					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

					if(!empty($customerSecondaryEmail))
					{
						$trackEmailImage	=	"images/white-space.jpg";

						$a_templateData		=	array("{name}"=>$customerName,"{orderNo}"=>$orderAddress,"{orderType}"=>$orderText,"{instructions}"=>$replyInstructions,"{needFeedBackMessage}"=>$needFeedBackMessage,"{showFilesNameInEmail}"=>$showFilesNameInEmail,"{trackEmailImage}"=>$trackEmailImage,"{sendingFileAttachmentMsg}"=>$sendingFileAttachmentMsg,"{referFriendLink}"=>$referFriendLink,"{excellentLink}"=>$excellentLink,"{goodLink}"=>$goodLink,"{fairLink}"=>$fairLink,"{poorLink}"=>$poorLink,"{awfulLink}"=>$awfulLink);

						$a_templateSubject		=	array("{orderAddress}"=>$orderAddress);

						$uniqueTemplateName		=	"TEMPLATE_SENDING_CUSTOMER_ORDER_REPLY_FILE_ATTACHMENT";
						$toEmail				=	$customerSecondaryEmail;
						include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
					}
				}
				$hasAttachment				=	0;
				$setThisEmailReplyToo		=	"";//Setting for reply to make empty to manager
				$setThisEmailReplyTooName	=	"";//Setting for reply to make empty to manager
				$trackEmailImage			=	"images/white-space.jpg";
				if(!empty($a_managerEmails))
				{
					$a_managerEmails	    =	stringReplace(',john@ieimpact.net','',$a_managerEmails);
					
					$a_templateData			=	array("{managerName}"=>"Manager","{instructions}"=>$replyInstructions,"{orderNo}"=>$orderAddress,"{orderDate}"=>$t_orderAddedOn,"{orderType}"=>$orderText,"{customerName}"=>$customerName,"{acceptedBy}"=>$acceptedByName,"{qaDoneBy}"=>$s_employeeName,"{showFilesNameInEmail}"=>$showFilesNameInEmail,"{trackEmailImage}"=>$trackEmailImage,"{sendingFileAttachmentMsg}"=>$sendingFileAttachmentMsg);

					//$a_templateSubject		=	array("{orderAddress}"=>$orderAddress,"{customerName}"=>$customerName);

					$managerEmployeeEmailSubject	= "Completed Order from ".$customerName.", ".$orderAddress." on ".showDate($customer_zone_date);

					$uniqueTemplateName		=	"TEMPLATE_SENDING_ORDER_REPLY_TO_MANAGER";
					$toEmail				=	DEFAULT_BCC_EMAIL;
					$managerEmployeeFromBcc =	$a_managerEmails;
					
					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
					
				}
			}

			////////////////////////////// UPDATE LOYALTY SCORE /////////////////////////////
			$commonClass->addLoyaltyScore($customerId,1,'neworder',$orderAddress,$orderId);
			//////////////////////////////////////////////////////////////////////////////////
	
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=$orderId&customerId=$customerId&selectedTab=2");
			exit();
		}
		else
		{
			echo $validator->getErrors();
		}
	}
?>
<script type="text/javascript">
function doneQaOrder(orderId,customerId,replyId)
{
	var confirmation = window.confirm("Are You Sure To Marked This Order As QA Done And Completed Order?");
	if(confirmation == true)
	{
		window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/pdf-pending-qa-orders.php?orderId='+orderId+"&customerId="+customerId+"&replyId="+replyId+"&doneQa=1";
	}
}
function goToEdit(orderId,customerId)
{
	location.href = "<?php echo SITE_URL_EMPLOYEES?>/process-pdf-order.php?orderId="+orderId+"&customerId="+customerId;
}
function checkForNumber()
 {
	k = (document.all)?event.keyCode : arguments.callee.caller.arguments[0].which;
	if(k == 8 || k== 0)
	{
		return true;
	}
	if(k >= 48 && k <= 57 )
	{
		return true;
	}
	else
	{
		return false;
	}
 }
 function display_loading()
 {
	document.getElementById('loading').style.display = 'block';
 } 
 function checkValidQa()
 {
	//return;
	form1		=	document.markedQaDone;
	var countTotalChecked	=	1;
	if(form1.isChecklistAvailabale.value == 1 && form1.needToDoQaChecklist.value == 1)
	{
		for(j=1;j<form1.totalChecklistExists.value;j++){
			access	=	document.getElementsByName('readChecklist['+j+']');
			for(i=0;i<access.length;i++)
			{
				if(access[i].checked == true)
				{
					countTotalChecked	=	countTotalChecked+1;
				}
			}
		}

		//alert(form1.totalChecklistExists.value+"=="+countTotalChecked);

		if(form1.totalChecklistExists.value != countTotalChecked)
		{
			alert("Please complete the checklist.");
			return false;
		}
	}
	/*if(form1.timeSpentQa.value	==	"")
	{
		alert("Please enter total time spent in QA !!");
		form1.timeSpentQa.focus();
		return false;
	}
	if(form1.isRateSelectedByQa.value	==	"0")
	{
		alert("Please rate in employee replies files !!");
		return false;
	}*/
	display_loading();
 }
 function showRateText(text)
 {
	document.getElementById('showRateText').innerHTML = text;
 }
 function showHideRate(flag)
 {
	if(flag)
	{
		document.getElementById("enterRateEmployeesReply").style.display = 'inline';
		document.getElementById('isRateGiven').value = 1;
	}
	else
	{
		document.getElementById("enterRateEmployeesReply").style.display = 'none';
		document.getElementById('isRateGiven').value = 0;
	}
 }
 function isValidCheckAnswer(selected,answer,currentNo)
 {
	if(answer	!=	0)
	{
		if(selected		!=	answer)
		{
			if(answer == 2)
			{
				if(selected == 1)
				{
					alert("Are you SURE ?");
					//return false;
				}
			}
			else if(answer == 1)
			{
				if(selected == 2)
				{
					alert("Are you SURE ?");
					//return false;
				}
			}
		}
	}
	var number = currentNo+1;
	document.getElementById("showCheckList"+number).style.display = 'inline';

 }
</script>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>
<form name="markedQaDone" action="" method="POST" onsubmit="return checkValidQa();">
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
<tr>
	<td colspan="3">
		<a name="mark"></a>
	</td>
</tr>
	<?php
		if(!empty($replyId) && !empty($doneQa))
		{
	?>
	<!-- <tr>
		<td colspan="3" height="30"></td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top" width="20%"><b>What Did You Check In QA?</b></td>
		<td class="smalltext2"  valign="top" width="2%"><b>:</b></td>
		<td  valign="top">
			<input type="text" name="qaChecked" size="60" value="<?php echo $qaChecked;?>" style="border:1px solid #333333">
		</td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top" width="20%"><b>Error Found And Corrected</b></td>
		<td class="smalltext2"  valign="top" width="2%"><b>:</b></td>
		<td  valign="top">
			<textarea name="errorCorrected" rows="8" cols="45" style="border:1px solid #333333"><?php echo stripslashes(htmlentities($errorCorrected,ENT_QUOTES))?></textarea>
		</td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top"><b>Feedback For Employee</b></td>
		<td class="smalltext2"  valign="top"><b>:</b></td>
		<td  valign="top">
			<input type="text" name="feedbackToEmployee" size="60" value="<?php echo $feedbackToEmployee;?>" style="border:1px solid #333333">
		</td>
	</tr> -->
	<tr>
		<td colspan="3">
			<?php
				$query		=	"SELECT * FROM qa_checklist WHERE status=0 ORDER BY checklistQaTitle";
				$result		=	dbQuery($query);
				if(mysql_num_rows($result))
				{
		?>
		<br>
		<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
			<tr>
				<td colspan="5" class="textstyle1">
					<b>Please mark the following checklist</b>
				</td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<?php
				$countChecklist	=	0;
				while($row		=	mysql_fetch_assoc($result))
				{
					$countChecklist++;
					$checklistId			=	$row['checklistId'];
					$checklistQaTitle		=	stripslashes($row['checklistQaTitle']);
					$checkAnswer			=	$row['answer'];

					$showHideChecklist		=	"";
					if($countChecklist > 1){
						$showHideChecklist	=	"none";
					}

					$color		            =	"#006F37";
					if($countChecklist      ==  2)
					{
						$color			    =   "#007BB7";
					}
					elseif($countChecklist  ==  3)
					{
						$color			    =   "#EA0000";
					}
					elseif($countChecklist  ==  4)
					{
						$color			    =   "#6F0000";
					}
					elseif($countChecklist  ==  5)
					{
						$color			    =   "#5353A8";
					}
					elseif($countChecklist  ==  6)
					{
						$color			    =   "#000000";
					}
					elseif($countChecklist  ==  7)
					{
						$color			    =   "#408080";
					}
			?>
			<tr>
				<td colspan="3">
					<div id="showCheckList<?php echo $countChecklist;?>" style="display:<?php echo $showHideChecklist;?>">
						<table width="100%" align="center" border="0">
							<tr>
								<td width="2%" class="textstyle3" valign="top"><b><?php echo $countChecklist;?>)</b></td>
								<td valign="top" class="textstyle3">
									<b>
										<font color="<?php echo $color;?>"><?php echo $checklistQaTitle;?></font>
									
									&nbsp;&nbsp;
									<input type="radio" name="readChecklist[<?php echo $countChecklist;?>]" value="1|<?php echo $checklistId;?>"  onclick="return isValidCheckAnswer(1,<?php echo $checkAnswer;?>,<?php echo $countChecklist;?>)">Yes
									&nbsp;&nbsp;
									<input type="radio" name="readChecklist[<?php echo $countChecklist;?>]" value="2|<?php echo $checklistId;?>"  onclick="return isValidCheckAnswer(2,<?php echo $checkAnswer;?>,<?php echo $countChecklist;?>)">No
									&nbsp;&nbsp;
									<input type="radio" name="readChecklist[<?php echo $countChecklist;?>]" value="3|<?php echo $checklistId;?>" onclick="return isValidCheckAnswer(3,<?php echo $checkAnswer;?>,<?php echo $countChecklist;?>)">N/A</b>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<?php
				}
			?>
			<tr>
				<td class="smlltext2" colspan="8">
					<input type="hidden" name="isChecklistAvailabale" value="1">
					<input type="hidden" name="totalChecklistExists" value="<?php echo $countChecklist;?>">
					<?php
						if(!empty($needToDoQaChecklist) && !empty($isRequiredChecklistText))
						{
							echo "<font class='error'>".$isRequiredChecklistText."</font>";
						}
				
					?>
				</td>
			</tr>
		</table>
		<br>
		<?php
			}
			else
			{
		?>
		<input type="hidden" name="isChecklistAvailabale" value="0">
		<?php
			}
		?>
		</td>
	</tr>
	<!--<tr>
		<td class="smalltext2" valign="top" width="20%"><b>Total Time Spent In QA</b></td>
		<td class="smalltext2" valign="top" width="2%"><b>:</b></td>
		<td  valign="top" class="smalltext1">
			<input type="text" name="timeSpentQa" size="10" value="<?php echo $timeSpentQa;?>" onKeyPress="return checkForNumber();" style="border:1px solid #333333">(IN MINITUES)
		</td>
	</tr>-->
	<tr>
		<td class="smalltext2" valign="top"><b>Comments (If Any)</b></td>
		<td class="smalltext2" valign="top" align="center"><b> : </td>
		<td class="smalltext2" valign="top">
			<textarea name="qaRateMessage" cols="51" rows="6" style="border:1px solid #333333"><?php echo $qaRateMessage;?></textarea>
		</td>
	</tr>
	<!-- THIS BLOCK IS FOR ADDING QA ORDER RATING
	<?php
		if(empty($rateByQa))
		{
	?>
	<tr>
		<td colspan="3">
			<div id="addOrderRate">
				<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
					<tr>
						<td width="20%" class="smalltext2"><b>Rate Employee Replies</b></td>
						<td width="2%" class="smalltext2"><b>:</b></td>
						<td width="10%">
							<?php
								$url2	=	SITE_URL_EMPLOYEES. "/add-order-rating.php?orderId=$orderId&rateChoose=";
								foreach($a_ratingByQa as $key=>$value)
								{
									echo "<img src='".SITE_URL."/images/emptystar.gif'  width=12 height=12 onMouseOver=\"return showRateText('".$value."');\" onMouseOut=\"document.getElementById('showRateText').innerHTML=''\" onClick=\"return commonFunc('$url2','enterRateEmployeesReply',$key),showHideRate(1);\">";
								}
							?>
						</td>
						<td id="showRateText" class="error" width="20%"></td>
						<td>&nbsp;</td>
					</tr>
				</table>
				<div id="enterRateEmployeesReply">
				
				</div>
				<input type="hidden" id="isRateGiven" name="isRateSelectedByQa" value="0">
			</div>
		</td>
	</tr>
	<?php
		}
		else
		{
	?>
	<tr>
		<td width="25%" class="smalltext2" valign="top"><b>Rate Given By QA</b></td>
		<td width="1%" class="smalltext2" valign="top"><b>:</b></td>
		<td width="10%" class="smalltext2" valign="top">
			<?php
				for($i=1;$i<=$rateGiven;$i++)
				{
					echo "<img src='".SITE_URL."/images/star.gif'  width=12 height=12'>";
				}
			?>
		</td>
		<td class="title" valign="top"><b><?php echo $a_existingRatings[$rateGiven];?></b></td>
	</tr>
	<?php
		if(!empty($qaRateMessage))
		{
	?>
	<tr>
		<td colspan='4' height="3"></td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top"><b>Comments</b></td>
		<td class="smalltext2" valign="top"><b>:</b></td>
		<td colspan="2" valign="top" class="smalltext2">
			<?php
				echo stripslashes($qaRateMessage);
			?>
		</td>
	</tr>
	<?php
		}
	}
	?>-->
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
		<td colspan="3">
			
			<input type='button' name='edit' value='EDIT REPLIED FILES' onClick="return goToEdit(<?php echo $orderId;?>,<?php echo $customerId;?>)">&nbsp;&nbsp;
			<input type="submit" name="submit" value="MARK AS QA DONE">
			<input type="hidden" name="replyId" value="<?php echo $replyId?>">
			<input type="hidden" name="needToDoQaChecklist" value="<?php echo $needToDoQaChecklist?>">
			<input type="hidden" name="formSubmitted" value="1">
			<!-- <input type="button" name="submit" onClick="javascript:doneQaOrder(<?php echo $orderId;?>,<?php echo $customerId?>,<?php echo $replyId?>)" value="MARK AS QA DONE">&nbsp;&nbsp; -->
			
		</td>
	</tr>
	<?php
		}	
	?>
	<tr>
		<td colspan="2">
			<input type="button" name="submit" onClick="history.back()" value="BACK">
		</td>
		<td>
			<?php	
				include(SITE_ROOT_EMPLOYEES . "/includes/next-previous-order.php");
			?>
		</td>
	</tr>
</table>
</form>
<?php
	
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>