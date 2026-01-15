<?php
	function getFileSize($fileSize)
	{
		if($fileSize <= 0)
		{
			$fileSize	=	"";
		}
		else
		{
			$fileSize	=	$fileSize/1024;

			$fileSize	=	round($fileSize,2);

			$fileSize	=	$fileSize." KB";
		}

		return $fileSize;
	}

	function strip_html_tags_email( $text )
	{
		// PHP's strip_tags() function will remove tags, but it
		// doesn't remove scripts, styles, and other unwanted
		// invisible text between tags.  Also, as a prelude to
		// tokenizing the text, we need to insure that when
		// block-level tags (such as <p> or <div>) are removed,
		// neighboring words aren't joined.
		$text = preg_replace(
			array(
				// Remove invisible content
				'@<head[^>]*?>.*?</head>@siu',
				'@<style[^>]*?>.*?</style>@siu',
				'@<script[^>]*?.*?</script>@siu',
				'@<object[^>]*?.*?</object>@siu',
				'@<embed[^>]*?.*?</embed>@siu',
				'@<applet[^>]*?.*?</applet>@siu',
				'@<noframes[^>]*?.*?</noframes>@siu',
				'@<noscript[^>]*?.*?</noscript>@siu',
				'@<noembed[^>]*?.*?</noembed>@siu',

				// Add line breaks before & after blocks
				'@<((br)|(hr))@iu',
				'@</?((address)|(blockquote)|(center)|(del))@iu',
				'@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
				'@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
				'@</?((table)|(th)|(td)|(caption))@iu',
				'@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
				'@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
				'@</?((frameset)|(frame)|(iframe))@iu',
			),
			array(
				' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
				"\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
				"\n\$0", "\n\$0",
			),
			$text );

		// Remove all remaining tags and comments and return.
		return strip_tags($text, '<a><br><br />');
		//return strip_tags($text, '<a>');
		
	}

	$paymentGatewayType				=	PAYMENT_GATEWAY_AUTHORIZE;
	$paymentGatewayUsed				=   "Authorize.net";
	$check_max_authorization_money	=	EMAIL_ORDER_DEFAULT_AUTHORIZE_PRICE;
	$transId						=	0;
	$new_charge_id					=	0;
	$inv_no							=	"";
	$needDefineAuthorizeClass		=	1;
	$forcedToRechargeWallet         =	0; 
	$noSmsActionNeeded              =   1;


	$query							=	"SELECT firstName,lastName,postCode,phone,completeName,email,secondaryEmail,folderId,customerFreeOrders,isRemindedCard,selectedRemindId,isAskingForPrepiad,isDisplayPrepaid,customerOwnEta,stripeCustomerId,noEmails,totalOrdersPlaced,uniqueEmailCode,displayEcheck,optedForWalletRecharge,autoWalletRechargeAmount,customerDeselectAutoRecharge FROM members WHERE memberId=$memberId AND isActiveCustomer=1 AND memberType='".CUSTOMERS."'";
	$result							=	dbQuery($query);						
	if(mysqli_num_rows($result))
	{			
		$row						=	mysqli_fetch_assoc($result);
		$firstName					=	stripslashes($row['firstName']);
		$lastName					=	stripslashes($row['lastName']);
		$completeName				=	$orderPlacedToName = $s_memberName = stripslashes($row['completeName']);
		$postCode					=	stripslashes($row['postCode']);
		$customerEmail				=	$orderPlacedToEmail = $senderEmail = $row['email'];
		$customerEmail				=	$senderEmail = $row['email'];
		$secondaryEmail				=	$row['secondaryEmail'];
		$folderId					=	$row['folderId'];
		$noEmails					=	$row['noEmails'];
		$totalFreeOrdersAvailable	=	$row['customerFreeOrders'];
		$isRemindedCard				=	$row['isRemindedCard'];
		$selectedRemindId			=	$row['selectedRemindId'];
		$isAskingForPrepiad			=	$row['isAskingForPrepiad'];
		$isDisplayPrepaid			=	$row['isDisplayPrepaid'];
		$selectedRemindId			=	$row['selectedRemindId'];
		$customerOwnEta				=	$row['customerOwnEta'];
		$stripeCustomerId			=	$row['stripeCustomerId'];
		$memberstotalOrdersPlaced	=	$row['totalOrdersPlaced'];
		$memberUniqueEmailCode		=	$smartEmailUniqueEmailCode = $row['uniqueEmailCode'];
			/////////////// DOUBLE CHECK FOR CUSTOMER UNIQUE EMAIL IF NOT EXISTS//////	
		if(empty($memberUniqueEmailCode)){
			$memberUniqueEmailCode  = $commonClass->generateCustomerUniquEmailCode($memberId,$lastName,$postCode);
		}
		///////////////////////////////////////////////////////////////////////////////
		$displayEcheckToCustomer	=	$row['displayEcheck'];
		$optedForWalletRecharge	    =	$row['optedForWalletRecharge'];
		$customerDeselectAutoRecharge =	$row['customerDeselectAutoRecharge'];
		$charge_money               =	$advanceAmount = $row['autoWalletRechargeAmount'];
		$toPhone  				    =   $row['phone'];
		if(!empty($toPhone)){
			$toPhone 			    =	"+1".$toPhone;
		}

		$isWalletPayments			=	0;
		$check_max_authorization_money = $memberObj->getCustomerRushPrice($memberId,$nowDateIndia);

		$orderFilePath				=	SITE_ROOT_FILES."/files/orderFiles";

		$walletAmount				=	$employeeObj->getSingleQueryResult("SELECT amount FROM wallet_master WHERE memberId=$memberId","amount");
		if(empty($walletAmount)){
			$walletAmount			=   0;
		}
		else{
			/////////// CHECKING UNVERIFIED EXISTING ORDERS AND AMOUNTS /////////////////
			if(!empty($walletAmount) && $walletAmount >= $check_max_authorization_money)
			{
				$unverifiedEmailOrders	=	$employeeObj->getSingleQueryResult("SELECT COUNT(*) as total FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND memberId=$memberId AND isEmailOrder=1 AND isNotVerfidedEmailOrder=1 AND captureEmailOrderThrough='wallet' AND isPaidThroughWallet='no' AND walletAccountId=0","total");

				if(!empty($unverifiedEmailOrders)){
		
					$unClearedWalletMoney=	$unverifiedEmailOrders*$check_max_authorization_money;
					if($walletAmount > $unClearedWalletMoney){
						$walletAmount    =  $walletAmount-$unClearedWalletMoney;
					}
					else{
						$walletAmount    = 0;
					}
				}
				
			}
			///////////////////////////////////////////////////////////////////////////////
		}

		if($check_max_authorization_money < MINIMUM_CARD_ORDER_PRICE && $isAskingForPrepiad == 1){
			$forcedToRechargeWallet =	1;

			if(empty($optedForWalletRecharge) && empty($charge_money) && empty($customerDeselectAutoRecharge)){
				$optedForWalletRecharge     =   1;
				$charge_money               =   100;

				 dbQuery("UPDATE members SET optedForWalletRecharge=$optedForWalletRecharge,autoWalletRechargeAmount=$charge_money WHERE memberId=$memberId");
			}

		}
	
		if(!empty($optedForWalletRecharge) && !empty($charge_money) && $check_max_authorization_money > $walletAmount){
			$makeAutoRecharge	    =	0;

			if(empty($walletAmount)){
				$makeAutoRecharge   =	1;
			}
			elseif($check_max_authorization_money > $walletAmount){
				$makeAutoRecharge   =	1;
			}
			
			if($makeAutoRecharge    == 1){			
	
				if($paymentGatewayType	== PAYMENT_GATEWAY_AUTHORIZE){
					///////////////// RECHARGE WITH AUTHORIZE.NET /////////////////////////////////
					
					$serachAccountClause=	"";
					if(isset($displayEcheckToCustomer) && $displayEcheckToCustomer == "no"){
						$serachAccountClause		=	" AND remindAccountType <> 'bankaccount'";
					}
					$query1							=	"SELECT * FROM auto_remember_account_details WHERE memberId=$memberId".$serachAccountClause." AND isCurrentlyUsed=1 ORDER BY accountId DESC";
					$result1						=	dbQuery($query1);
					if(mysqli_num_rows($result1))
					{
						include(SITE_ROOT_MEMBERS."/includes/auto-recharge-wallet-account-email.php");
							$needDefineAuthorizeClass	=	0;
						/*if($memberId == 6){
							include(SITE_ROOT_MEMBERS."/includes/auto-recharge-wallet-account-email.php");
							$needDefineAuthorizeClass	=	0;
						}
						else{
							include(SITE_ROOT_MEMBERS."/includes/auto-recharge-wallet-email.php");
							$needDefineAuthorizeClass	=	0;
						}*/
						
					}
				}
			}
		}

		$walletAmount				=	$employeeObj->getSingleQueryResult("SELECT amount FROM wallet_master WHERE memberId=$memberId","amount");
		
		$is_allow_to_create_order   =	true;
		$captureEmailOrderThrough	=	"accounts";

		$totalIncompleteOrders		=	$employeeObj->getSingleQueryResult("SELECT COUNT(*) as total FROM members_orders WHERE memberId=$memberId AND status IN (0,1,3) AND isDeleted=0 AND isVirtualDeleted=0","total");
		if(empty($totalIncompleteOrders))
		{
			$totalIncompleteOrders	=	0;
		}
		$availableFree				=	0;
		if($totalIncompleteOrders	< $totalFreeOrdersAvailable)
		{
			$availableFree			=	$totalFreeOrdersAvailable-$totalIncompleteOrders;
			if($availableFree		==	0)
			{
				$availableFree		=	1;
			}
		}

		$credit_card_email_sub		=	"";
		$credit_card_email_msg		=	"";
		$admin_email_message_case	=	"";
		
		///////////////////////////////////////////////////////////////////////////////////////
		/*************************************************************************************/
		/*************** THIS BLOCK TO CHECK AUTHORIZE PAYMENT ACCOUNT DETAILS ***************/
		if($isAskingForPrepiad	==	1 && empty($availableFree) && $isDisplayPrepaid == 1)
		{   
			//////////////////////// DEDUCT PAYMENTS FROM WALLET /////////////////////////////
			if(!empty($walletAmount) && $walletAmount >= $check_max_authorization_money)
			{
				$isWalletPayments		    =	1;
				$captureEmailOrderThrough	=	"wallet";
			}
			else
			{	
				if($forcedToRechargeWallet == 1){
					
					$is_allow_to_create_order	=	false;

					$credit_card_email_sub		=	"Insufficient amount in wallet to place new order.";

					$credit_card_email_msg		=	" ERROR: Failed to receive your emailed order due to insufficient amount in your wallet. Please recharge your wallet. ";

					
					$admin_email_message_case	=	"<br /><br />Customer Name : ".$completeName." & Reason : Insufficient amount in wallet to place new order";

					$smsMessage       = "MSG from ieIMPACT: Failed to receive your order due to insufficient amount in your wallet. Please update the credit card online on our website.";
					include(SITE_ROOT_EMPLOYEES .  "/includes/sending-sms-customer.php");					
				}
				else
				{								
					if(!empty($paymentGatewayType)){					
				
						if($paymentGatewayType		==	PAYMENT_GATEWAY_AUTHORIZE){
									
							///////////////// CAPTURE PAYMENTS IN AUTHORIZE.NET //////////////////////////
							if(empty($isRemindedCard) && empty($selectedRemindId))
							{
								$is_allow_to_create_order	    =	false;
								$credit_card_email_sub			=	" Failure To Receive Your Order: Missing Credit Card Information.";

								$credit_card_email_msg			=	"Our system failed to receive your order by email since we do not have your payment information on file. Please note all orders must have been paid in advance, so you must provide your payment account information on our website. Please login into your account on <a href='https://secure.ieimpact.com/members' target='_blank'>https://secure.ieimpact.com</a> website and click 'New Order' and click 'Add New Payment' Information. Your credit card information will be securely saved on <a href='https://account.authorize.net/' target='_blank'>authorize.net</a> PCI compliant servers. We do not save any credit card information our local servers.";

								$admin_email_message_case		=	"<br /><br />Customer Name : ".$completeName." & Reason : No card or account added against the account.(Debug Case - I  Customer Message Order)";

								$smsMessage = "MSG from ieIMPACT: Failure To Receive Your Order. Please update the credit card online on our website.";
								include(SITE_ROOT_EMPLOYEES .  "/includes/sending-sms-customer.php");
							}
							else
							{
								$serachAccountClause	=	"";
								if(isset($displayEcheckToCustomer) && $displayEcheckToCustomer == "no"){
									$serachAccountClause=	" AND remindAccountType <> 'bankaccount'";
								}

								$query						    =	"SELECT * FROM auto_remember_account_details WHERE memberId=$memberId AND accountId=$selectedRemindId".$serachAccountClause." ORDER BY isCurrentlyUsed DESC LIMIT 1";
								$result							=	dbQuery($query);
								if(mysqli_num_rows($result))
								{
									$row						=	mysqli_fetch_assoc($result);
									$customerProfileId			=	$row['customerProfileId'];	
									$customerShippingAddressId	=	$row['customerShippingAddressId'];
									$customerPaymentProfileId	=	$row['customerPaymentProfileId'];
									$creditCardNumberMasked		=	$row['cardLastDigits'];

									if($needDefineAuthorizeClass== 1){

								
										include_once(SITE_ROOT      	    .   "/classes/authorize.class.php");
											// Create an object of AuthorizeAPI class
										$objAuthorizeAPI                    =   new AuthorizeAPI(AUTHORIZE_PAYMENT_LOGIN_ID, AUTHORIZE_PAYMENT_TRANSACTION_KEY, 'liveMode');
									}

									$customer_last_id			= $employeeObj->getSingleQueryResult("SELECT orderId FROM members_orders WHERE memberId=$memberId ORDER BY orderId DESC LIMIT 1","orderId");

									$inv_no						=	$customer_last_id.$memberId."N";
									$item_id					=	rand(111111,999999).$memberId;

									//if($memberId == 6){
										$authResponse           =   $objAuthorizeAPI->authCC($customerProfileId, $customerPaymentProfileId, $check_max_authorization_money, $inv_no, "Authorize customer payment");
										$authResponse           =   @json_decode($authResponse,TRUE);

										$addedText		        =	"Employee marking message as new order";

									
										$commonClass->trackAuthorizeResponseWithDetails($authResponse,'Authorize.net',0,$memberId,$addedText,$customerProfileId, $customerPaymentProfileId);

										 if(array_key_exists('success',$authResponse) && $authResponse['success'] == 1 && array_key_exists('paymentFlag',$authResponse) && $authResponse['paymentFlag'] == 1){
					
											$transId					=	htmlspecialchars($authResponse['transId']);		
										}
										else{
											$errorCode                  = $authResponse['errorCode'];
											
											$errorMsg	                =  htmlspecialchars($authResponse['message'])."&nbsp;(Error Code - ".$errorCode.")<br>";

											$is_allow_to_create_order   =	false;

											$credit_card_email_sub		=	" Failure To Receive Your Order: Missing Credit Card Information.";

											$credit_card_email_msg		=   "Our system failed to receive your order by email since we do not have your payment information on file. Please note all orders must have been paid in advance, so you must provide your payment account information on our website. Please login into your ieIMPACT account on <a href='https://secure.ieimpact.com/members' target='_blank'>https://secure.ieimpact.com</a> website and click 'Standard Instructions' and click 'Add New Payment' Information. <font color='#ff0000;'>Please make sure to resend your order after you enter your credit card on file</font>. Your credit card information will be securely saved on <a href='https://account.authorize.net/' target='_blank'>authorize.net</a> PCI compliant servers. We do not save any credit card information our local servers.";

											$admin_email_message_case		=	"<br /><br />Customer Name : ".$completeName." & Reason Server Error: ".$errorMsg." (Debug Case - III Customer Message Order)";
										}
								}
								else{
									$is_allow_to_create_order		=	false;
						
									$credit_card_email_sub			=	" Failure To Receive Your Order: Missing Credit Card Information.";

									$credit_card_email_msg			=	"Our system failed to receive your order by email since we do not have your payment information on file. Please note all orders must have been paid in advance, so you must provide your payment account information on our website. Please login into your ieIMPACT account on <a href='https://secure.ieimpact.com/members' target='_blank'>https://secure.ieimpact.com</a> website and click 'Standard Instructions' and click 'Add New Payment' Information. <font color='#ff0000;'>Please make sure to resend your order after you enter your credit card on file</font>. Your credit card information will be securely saved on <a href='https://account.authorize.net/' target='_blank'>authorize.net</a> PCI compliant servers. We do not save any credit card information our local servers.";

									$admin_email_message_case		=	"<br /><br />Customer Name : ".$completeName." & Reason : The account/card added against the account is not found.(Debug Case - IV Customer Message Order)";

									$smsMessage = "MSG from ieIMPACT: Failure To Receive Your Order. Please update the credit card online on our website.";
									include(SITE_ROOT_EMPLOYEES .  "/includes/sending-sms-customer.php");
								}
							}

						}
						elseif($paymentGatewayType		==	PAYMENT_GATEWAY_STRIPE){
							////////////////////// MAKE PAYMENT THROUGH /////////////////////////
							$query						 =	"SELECT * FROM auto_remember_stripe_account_details WHERE memberId=$memberId AND stripeCustomerId='$stripeCustomerId' AND isCurrentlyUsed=1 AND cardId <> '' ORDER BY accountId DESC LIMIT 1";
							$result						 =	dbQuery($query);
							if(mysqli_num_rows($result))
							{
								$row					 =	mysqli_fetch_assoc($result);
								$cardId					 =	$row['cardId'];
								$stripeAccountId		 =	$row['accountId'];

								require_once(SITE_ROOT.'/stripe/init.php');
								\Stripe\Stripe::setApiKey(STRIPE_SECREAT_KEY);

								$chargeFor				=	$completeName." Email order at EST - ".showDate(CURRENT_DATE_CUSTOMER_ZONE)." ".showTimeShortFormat(CURRENT_TIME_CUSTOMER_ZONE);

								$order_amount_cent		=	$check_max_authorization_money*100;

								try{
								///////////////////////////// CHARGING CARD ACCOUNT ////////////////////////
									$charge = \Stripe\Charge::create(array(
									'amount' => $order_amount_cent, // amount in cents
									'currency' => 'usd',
									'customer' => $stripeCustomerId,
									'capture' => false,
									'source' => $cardId, // obtained with Stripe.js
									'description' => $chargeFor
									));

									$new_charge_id				=	$charge['id'];
								}
								catch(Exception $e){
									$errorMsg					=   $e->getMessage();	
									
									$is_allow_to_create_order	=	false;
										
									$credit_card_email_sub		=	" Failure To Receive Your Order: Missing Credit Card Information.";

									$credit_card_email_msg		=	"Our system failed to receive your order by email since we do not have your payment information on file. Please note all orders must have been paid in advance, so you must provide your payment account information on our website. Please login into your ieIMPACT account on <a href='https://secure.ieimpact.com/members' target='_blank'>https://secure.ieimpact.com</a> website and click 'Standard Instructions' and click 'Add New Payment' Information. <font color='#ff0000;'>Please make sure to resend your order after you enter your credit card on file</font>. Your credit card information will be securely saved on <a href='https://stripe.com/' target='_blank'>Stripe</a> server. We do not save any credit card information our local servers.";

									$admin_email_message_case		=	"<br /><br />Customer Name : ".$completeName." & Reason Server Error: ".$errorMsg." (Stripe)";

									$smsMessage = "MSG from ieIMPACT: Failure To Receive Your Order. Please update the credit card online on our website.";
									include(SITE_ROOT_EMPLOYEES .  "/includes/sending-sms-customer.php");
								}
							}
							else{
								$is_allow_to_create_order	    =	false;
								$credit_card_email_sub			=	" Failure To Receive Your Order: Missing Credit Card Information.";

								$credit_card_email_msg			=	"Our system failed to receive your order by email since we do not have your payment information on file. Please note all orders must have been paid in advance, so you must provide your payment account information on our website. Please login into your account on <a href='https://secure.ieimpact.com/members' target='_blank'>https://secure.ieimpact.com</a> website and click 'New Order' and click 'Add New Payment' Information. Your credit card information will be securely saved on <a href='https://stripe.com/' target='_blank'>Stripet</a> server. We do not save any credit card information our local servers.";

								$admin_email_message_case		=	"<br /><br />Customer Name : ".$completeName." & Reason : No card or account added against the account(Stripe).";

								$smsMessage = "MSG from ieIMPACT: Failure To Receive Your Order. Please update the credit card online on our website.";
									include(SITE_ROOT_EMPLOYEES .  "/includes/sending-sms-customer.php");
							}
						}
					}
			    }
			}				
		}
		////////////////////////////////////////////////////////////////////////////////////////
		/***************************************************************************************/
		/***********************THIS BLOCK CHECKS IS ADDED CIRCUIT BREAKER ********************/
		$isChekedBreaker			=	false;
		$isNeedToCheckBreaker		=	0;
		$latestInvoiceId			=	0;
		$totalLatestOutStanding		=	0;
		$isForcedToPay				=	0;
		$circuitBreakerAmount		=	$memberObj->isCustomerInCircuitBreak($memberId);
		$circuitBreakerMsg		    =	$memberObj->getCircuitBreakerMessage();
		$customerTotalOutsatnding	=	$memberObj->getCustomerLatestOutstanding($memberId);

		$getLastInvoiceId			=	$employeeObj->getSingleQueryResult("SELECT invoiceId FROM members_invoice_details WHERE memberId=$memberId ORDER BY invoiceId DESC LIMIT 1","invoiceId");
		
		if(empty($getLastInvoiceId))
		{
			$isChekedBreaker		=	true;
		}
		else
		{	
			//CHECKING FOR LAST INVOICE DETAILS E.G. For Dec Month Checking  - Dec
			$query1					=	"SELECT * FROM members_invoice_details WHERE memberId=$memberId AND isMailSent=1 AND totalLatestOutStanding <> 0 AND invoiceId=$getLastInvoiceId";
			$result1	=	dbQuery($query1);
			if(mysqli_num_rows($result1))
			{	
				$row1						=	mysqli_fetch_assoc($result1);
				$totalMoney					=	$row1['totalMoney'];
				$lateFees					=	$row1['lateFees'];
				$totalInvoiceMonthMoney		=	$row1['totalInvoiceMonthMoney'];
				$totalPaymentReceived		=	$row1['totalPaymentReceived'];
				$currentInvoiceOutStanding	=   $row1['totalLatestOutStanding'];
				$latestInvoiceGeneratedOn	=	$row1['invoiceGeneratedOn'];
				
				//CHECKING FOR LAST TO LAST INVOICE DETAILS E.G. For Dec Month Checking  - N
				$totalLatestOutStanding		=	$employeeObj->getSingleQueryResult("SELECT totalLatestOutStanding FROM members_invoice_details WHERE memberId=$memberId AND isMailSent=1 ORDER BY invoiceId DESC LIMIT 1,1","totalLatestOutStanding");
				if(empty($totalLatestOutStanding))
				{
					$minituesFromLastInvoiceToCurrent	=	timeBetweenTwoTimes($latestInvoiceGeneratedOn,CURRENT_TIME_CUSTOMER_ZONE,CURRENT_DATE_CUSTOMER_ZONE,CURRENT_TIME_CUSTOMER_ZONE);

					if($minituesFromLastInvoiceToCurrent >= 44640)
					{
						$totalLatestOutStanding		=	$currentInvoiceOutStanding;
					}
					else
					{
						$totalLatestOutStanding		=	0;
					}
				}

				//Checking whether Last invoice amount is greater or less than the outstanding of last to last month . e.g For Dec payment recv with Nov Outstanding
				if(!empty($totalLatestOutStanding))
				{
					if($totalPaymentReceived <  $totalLatestOutStanding)
					{
						$ownTrashMoney		=	$employeeObj->getSingleQueryResult("SELECT circuitbreakerIndividualMoney FROM members WHERE memberId=$memberId AND isAddedCircuitBreaker=1","circuitbreakerIndividualMoney");
						if(!empty($ownTrashMoney))
						{
							 if($customerTotalOutsatnding > $ownTrashMoney)
							 {
								$isForcedToPay	 =	1;
							 }
							 else
							 {
								$isForcedToPay	 =	0;
							 }
						}
						else
						{
							$isForcedToPay	 =	1;
						}
					}
				}
			}
			
		}

		if(!empty($circuitBreakerAmount))
		{
			if($isForcedToPay		==	1)
			{
				$isNeedToCheckBreaker=	1;
			}
			elseif($isChekedBreaker			== true)
			{
				if($customerTotalOutsatnding >= $circuitBreakerAmount)
				{
					$isNeedToCheckBreaker	=	1;
				}
			}
		}
		
		/****************************************************************************************/
		if($isNeedToCheckBreaker	==	1 && $isWalletPayments == 0)
		{
			$memberObj->checkCustomerCircuitBreaker($memberId);
			$thresholdEmailMsg	=	"Customer is forced to show threshold";

			

			$uniqueTemplateName	=	"TEMPLATE_SENDING_SIMPLEE_CUSTOMER_MESSAGE";
			$toEmail			=	$customerEmail;
		
			$thresholdEmailBody	=	nl2br($circuitBreakerMsg);

			$thresholdSub		=	"Please clear your dues before placing a new order";

			$a_templateSubject	=	array("{emailSubject}"=>$thresholdSub);

			$a_templateData		=	array("{completeName}"=>$completeName,"{emailBody}"=>$thresholdEmailBody);

			include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

			if(!empty($sendingCCEmailForMultiple)){
				$toEmail			=	$sendingCCEmailForMultiple;
				include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
			}

			/************************************ SENDING EMAIL TO MANAGER **********************/
			$toEmail			=	"john@ieimpact.net";
		
			$thresholdEmailBody	=	$completeName." saw credit threshold page while placing new orders on ".showDate(CURRENT_DATE_CUSTOMER_ZONE)." ".CURRENT_TIME_CUSTOMER_ZONE." EST. through email. He/She needs to pay first to place a new order. Total outstanding is : ".$customerTotalOutsatnding;

			$thresholdSub		=	$completeName." hit credit threshold limit from email order";

			$a_templateSubject	=	array("{emailSubject}"=>$thresholdSub);

			$a_templateData		=	array("{completeName}"=>"John Bowen","{emailBody}"=>$thresholdEmailBody);

			include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
		}
		else
		{
			if($is_allow_to_create_order	==	false){
				

				$uniqueTemplateName	=	"TEMPLATE_SENDING_SIMPLEE_CUSTOMER_MESSAGE";
				$toEmail			=	$customerEmail;

											
				$a_templateSubject	=	array("{emailSubject}"=>$credit_card_email_sub);

				$a_templateData		=	array("{completeName}"=>$completeName,"{emailBody}"=>$credit_card_email_msg);

				include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

				if(!empty($sendingCCEmailForMultiple)){
					$toEmail			=	$sendingCCEmailForMultiple;
					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
				}

				/************************************ SENDING EMAIL TO MANAGER **********************/
				$toEmail			=	"john@ieimpact.net";
				//$toEmail			=	"gaurabsiva1@gmail.com";
			
				$a_templateSubject	=	array("{emailSubject}"=>$credit_card_email_sub);

				$a_templateData		=	array("{completeName}"=>"John Bowen","{emailBody}"=>$credit_card_email_msg.$admin_email_message_case);

				$managerEmployeeFromBcc	=   "gaurabsiva1@gmail.com,gaurabsiva1@yahoo.co.in";

				include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
			}
			else{
			
				if(empty($folderId))
				{
					$folderId	=	$memberId."_".substr(md5(rand()+date('s')),0,40);
					dbQuery("UPDATE members SET folderId='$folderId' WHERE memberId=$memberId");
				}

				if(!is_dir($orderFilePath."/$folderId"))
				{
					@mkdir($orderFilePath."/$folderId");
				}					

				$orderFilePath			=	SITE_ROOT_FILES."/files/orderFiles/$folderId";

				if(empty($fromemailSubject))
				{
					$fromemailSubject	=	"Order From Email";
				}

				$positionMsg                      = strpos($message, "Message :");
				if ($positionMsg === false) {
					$orderAddress                 =  $message;
					$instructions                 =  $message;
				}
				else{
					list($orderAddress,$instructions) =   explode("Message :",$message);
				}			
				
				$orderAddress					  =	  stringReplace("Subject :","",$orderAddress);
				$orderAddress					  =   strip_html_tags_email(trim($orderAddress));
				$instructions					  =   strip_html_tags_email(trim($instructions));
				$orderAddress					  =   stringReplace("<br />","",$orderAddress);
				

				$orderAddress					  =   makeDBSafe($orderAddress);
				$instructions					  =   makeDBSafe($instructions);

				/////////// CREATE UNIQUE EMAIL REPLY TO EMAIL /////////////
				$memberstotalOrdersPlaced		  =	$memberstotalOrdersPlaced+1;
				$memberOrderReplyToEmail		  =	$memberUniqueEmailCode.$memberstotalOrdersPlaced;
				$memberOrderReplyToEmail		  =	makeDBSafe($memberOrderReplyToEmail);
				
			
				$query	 =	"INSERT INTO members_orders SET memberId=$memberId,orderAddress='$orderAddress',orderType=22,instructions='$instructions',orderAddedOn='$nowDateIndia',orderAddedTime='$nowTimeIndia',estDate='".CURRENT_DATE_CUSTOMER_ZONE."',estTime='".CURRENT_TIME_CUSTOMER_ZONE."',isNewUploadingSystem=1,isEmailOrder=1,isNotVerfidedEmailOrder=1,captureEmailOrderThrough='$captureEmailOrderThrough',isEmailMessageOrder='yes',emailMadeOrderBy=$s_employeeId,orderReplyToEmail='$memberOrderReplyToEmail'";
				dbQuery($query);
				$orderId =	$newOrderIdFromMessage = mysqli_insert_id($db_conn);

				////////////////////////////////////////////////////////////////////////////////////////
				//////////////////// PUTTING THE ORDER IN ORDER TRACK LIST ////////////////////////////
			     $orderObj->addOrderTrackList($memberId,$s_employeeId,$orderId,$orderAddress,'Employee created customer message as new order','EMPLOYEE_CREATED_MESSAGE_AS_ORDER');
			    ////////////////////////////////////////////////////////////////////////////////////////
			    ////////////////////////////////////////////////////////////////////////////////////////

				if($isWalletPayments == 1 && $captureEmailOrderThrough == "wallet"){
									
					dbQuery("UPDATE wallet_master SET amount=amount-$check_max_authorization_money WHERE memberId=$memberId");

					$walletAmount	=	$employeeObj->getSingleQueryResult("SELECT amount FROM wallet_master WHERE memberId=$memberId","amount");
					$walletAmount	=	round($walletAmount,2);
								
					dbQuery("INSERT INTO wallet_transactions SET memberId=$memberId,amount='$check_max_authorization_money',transactionType='debit',debitType='orders',transactionDate='".CURRENT_DATE_INDIA."',transactionTime='".CURRENT_TIME_INDIA."',estTransactionDate='".CURRENT_DATE_CUSTOMER_ZONE."',estTransactionTime='".CURRENT_TIME_CUSTOMER_ZONE."',status='success',ipAddress='".VISITOR_IP_ADDRESS."',orderId=$orderId,orderAddress='$fromemailSubject',currentBalance='$walletAmount'");

					$walletAccountId	=  mysqli_insert_id($db_conn);								

					dbQuery("UPDATE members_orders SET isPaidThroughWallet='yes',walletAccountId=$walletAccountId,prepaidOrderPrice='$check_max_authorization_money' WHERE orderId=$orderId AND memberId=$memberId");
				}

				dbQuery("UPDATE members SET totalOrdersPlaced=totalOrdersPlaced+1,lastOrderAddedOn='$nowDateIndia' WHERE memberId=$memberId");

				//dbQuery("INSERT INTO orders_new_checkboxes SET orderId=$orderId");

				$memberObj->updateOrderRelatedCounts('newOrders');
				$memberObj->updateOrderRelatedCounts('uncheckedOrders');

				$orderFilePath1		=   $orderFilePath."/".$nowDateIndia;

				if(!is_dir($orderFilePath1))
				{
					@mkdir($orderFilePath1);
					@chmod($orderFilePath1,0700);
				}

				$newOrderFilePath	=  $orderFilePath1."/".$orderId;	

				if(!is_dir($newOrderFilePath))
				{
					@mkdir($newOrderFilePath);
					@chmod($newOrderFilePath,0700);
				}

				if(!empty($transId) && !empty($inv_no))
				{
					$extra_columns	=	",isAuthorizedEmailOrder=1,prepaidTransactionId=$transId,usedDebitAccountId=$selectedRemindId,customerProfileId='$customerProfileId',customerShippingAddressId='$customerShippingAddressId',customerPaymentProfileId='$customerPaymentProfileId',creditCardNumberMasked='$creditCardNumberMasked',invNo='$inv_no',item_id='$item_id',prepiadPaymentThrough='creditcard',isRushOrder=$customerOwnEta,paymentGateway='Authorize.net'";	
				}
				elseif(!empty($new_charge_id)){
					$extra_columns	=	",chargeId='$new_charge_id',prepiadPaymentThrough='creditcard',paymentGateway='Stripe',usingStripeAccountId=$stripeAccountId,isRushOrder=$customerOwnEta,isAuthorizedEmailOrder=1";
				}
				else
				{
					$extra_columns		=	"";
				}

				$md5OrderId		        =	md5($orderId);

				$orderEncryptedId		=	@hash_hmac('ripemd160', $md5OrderId, 'Chenaiiexpress');

				dbQuery("UPDATE members_orders SET newUploadingPath='$newOrderFilePath',encryptOrderId='$orderEncryptedId'".$extra_columns." WHERE orderId=$orderId AND memberId=$memberId");

			
				if(!empty($isUploadedMessageFiles))
				{								
					$uploadFileText			=	"Uploaded File ";
					$uploadFileComma		=	":";
					$isUploadedFiles		=	0;
					$orderGeneralMessagePath	=	SITE_ROOT_FILES."/files/general-messages/";
					$orderGeneralMessagePath	=	$orderGeneralMessagePath.$folderId."/";

				
					$query						=	"SELECT * FROM customer_general_message_files WHERE parentId=$messageId AND memberId=$memberId";
					$result						=	dbQuery($query);
					if(mysqli_num_rows($result))
					{
						while($row				=	mysqli_fetch_assoc($result))
						{
							$old_fileId			=	$row['fileId'];
							$fileName			=	$uploadingFileName = $row['fileName'];
							$fileExt			=	$row['fileExt'];
							$fileSize			=	$row['fileSize'];
							$fileType			=	$row['fileType'];
							$t_uploadingFile    =   makeDBSafe($fileName);

							dbQuery("INSERT INTO order_all_files SET uploadingType=1,uploadingFor=1,orderId=$orderId,memberId=$memberId,uploadingFileName='$t_uploadingFile',uploadingFileExt='$fileExt',uploadingFileType='$fileType',uploadingFileSize=$fileSize,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',customerZoneDate='".CURRENT_DATE_CUSTOMER_ZONE."',customerZoneTime='".CURRENT_TIME_CUSTOMER_ZONE."',addedFromIp='".VISITOR_IP_ADDRESS."'");

							$fileId				=	mysqli_insert_id($db_conn);
							$sourcePath		    =	$orderGeneralMessagePath.$old_fileId."_".$fileName.".".$fileExt;

							$destFileName		=	$newOrderFilePath."/".$fileId."_".$fileName.".".$fileExt;
												
							@copy($sourcePath,$destFileName);

							if(file_exists($sourcePath)){
								@unlink($sourcePath);
							}

							$orderFileMd5HasSize	=	md5_file($destFileName);
							if(empty($orderFileMd5HasSize))
							{
								$orderFileMd5HasSize=	"";
							}

							dbQuery("UPDATE order_all_files SET excatFileNameInServer='$destFileName',fileChecksumHas='$orderFileMd5HasSize' WHERE fileId=$fileId AND orderId=$orderId");

							$cronFileName		=	$fileName.".".$fileExt;

							$memberObj->addEditMultipleFilesCronTransfer($orderId,$nowDateIndia,$memberId,$folderId,$destFileName,$cronFileName,1,$completeName,$orderAddress,$fileId);

							$a_filesUploaded[]	=	$uploadingFileName.".".$fileExt."|".$fileSize;

							$isUploadedFiles	=	1;	

							dbQuery("DELETE FROM customer_general_message_files WHERE parentId=$messageId AND memberId=$memberId AND fileId=$fileId");						

						}						
					}
							
				}
				$orderNo				=	removeNewLineChracters($orderAddress);
				$orderInstructions		=	removeNewLineChracters($instructions);
				/////////////////// START OF SENDING EMAIL BLOCK/////////////////////////
				

				$filesUploaded			=	"";
				if(!empty($a_filesUploaded))
				{
					$filesUploaded	   .=	"<table width='98%' align='center' border='0' cellpadding='2' cellspacing='0'>";
					foreach($a_filesUploaded as $key=>$value)
					{
						list($uploadedFileName,$uploadedFileSize)	=	explode("|",$value);
						
						if(!empty($uploadedFileSize))
						{
							$uploadedFileSizeText	=	"&nbsp;<font size='2px' face='verdana' color='#4d4d4d'>(".getFileSize($uploadedFileSize).")</font>";
						}
						else
						{
							$uploadedFileSizeText	=	"&nbsp;<font size='2px' face='verdana' color='#ff0000'>(ZERO BYTES : Please Upload Again)</font>";
						}

						$filesUploaded .=	"<tr><td valign='top'><font size='2px' face='verdana' color='#787878'>".$uploadedFileName.$uploadedFileSizeText."</font></td></tr>";
					}

					$filesUploaded	   .=	"</table>";
				}
				else
				{
					$filesUploaded		=	"No File Uploaded";
				}

				$setThisEmailReplyToo			=	$memberOrderReplyToEmail.CUSTOMER_REPLY_EMAIL_TO;//Setting for reply to make customer reply order mesage
				$setThisEmailReplyTooName		=	"ieIMPACT Orders";//Setting for reply to make customer reply order mesage

				$quickReplyToEmail              = "<a href='mailto:".$setThisEmailReplyToo."'><u>".$setThisEmailReplyToo."</u></a>";

				$newOrdersSmartEmail 			=	"<a href='mailTo:NewOrder".$smartEmailUniqueEmailCode.CUSTOMER_REPLY_EMAIL_TO."'><u>NewOrder".$smartEmailUniqueEmailCode.CUSTOMER_REPLY_EMAIL_TO."</u></a>";

				$newOrdersMessagingEmail 		=	"<a href='mailTo:Email".$smartEmailUniqueEmailCode.CUSTOMER_REPLY_EMAIL_TO."'><u>Email".$smartEmailUniqueEmailCode.CUSTOMER_REPLY_EMAIL_TO."</u></a>";
				
				
				$a_templateData	=	array("{completeName}"=>$completeName,"{filesUploaded}"=>$filesUploaded,"{orderInstructions}"=>$orderInstructions,"{quickReplyToEmail}"=>$quickReplyToEmail,"{newOrdersSmartEmail}"=>$newOrdersSmartEmail,"{newOrdersMessagingEmail}"=>$newOrdersMessagingEmail);
			
				$a_templateSubject		=	array("{orderAddress}"=>$orderNo);

				$uniqueTemplateName		=	"TEMPLATE_SENDING_CUSTOMER_EMAIL_ORDER_RECEIVED";
				$toEmail				=	$customerEmail;
				if($noEmails == 0)
				{
					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

					if(!empty($secondaryEmail))
					{
						$toEmail		=	$secondaryEmail;
						include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
					}

					if(!empty($sendingCCEmailForMultiple)){
						$toEmail		=	$sendingCCEmailForMultiple;
						include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
					}

				}
				
				$uniqueTemplateName	=	"TEMPLATE_SENDING_CUSTOMER_EMAIL_ORDER_RECEIVED";
				$toEmail			=	"john@ieimpact.net";
				//$toEmail			=	"gaurabieimpact1@gmail.com";

				include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
				
				/////////////////// END OF SENDING EMAIL BLOCK/////////////////////////
			}

		}

	}
	////////////////////////// DELETING OLD MESSAGE ///////////////////////////////
	dbQuery("DELETE FROM members_general_messages WHERE generalMsgId=$messageId AND isOrderGeneralMsg=1 AND isBillingMsg=0 AND parentId=0");
	$orderObj->deductOrderRelatedCounts('unrepliedGeneralMsg');
?>