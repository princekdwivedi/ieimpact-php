<?php
	///////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////// AUTO RECHARGE WALLET ////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////
	if($paymentGatewayId	== PAYMENT_GATEWAY_AUTHORIZE){
		///////////////// RECHARGE WITH AUTHORIZE.NET /////////////////////////////////
		$serachAccountClause=	"";
		if(isset($displayEcheckToCustomer) && $displayEcheckToCustomer == "no"){
			$serachAccountClause		=	" AND remindAccountType <> 'bankaccount'";
		}
		$query							=	"SELECT * FROM auto_remember_account_details WHERE memberId=$memberId".$serachAccountClause." ORDER BY isCurrentlyUsed DESC LIMIT 1";
		$result							=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			
			$row						=	mysqli_fetch_assoc($result);
			$accountId					=	$row['accountId'];
			$customerProfileId			=	$row['customerProfileId'];	
			$customerShippingAddressId	=	$row['customerShippingAddressId'];
			$customerPaymentProfileId	=	$row['customerPaymentProfileId'];
			$remindAccountType			=	$row['remindAccountType'];
			$cardType					=	$row['cardType'];
			$cardLastDigits 			=	$row['cardLastDigits'];

			////////////////////////////// CHECKING RECENT TRANSACTIONS /////////////////////////////
			
			$isAllowPaymentCharge       =  $commonClass->checkIsAllowedTransactions($memberId, $customerProfileId, $customerPaymentProfileId);
			

			if(!empty($isAllowPaymentCharge)){

				$isIncludedAuthorizedClass  =   1;

				include_once(SITE_ROOT      	    .   "/classes/authorize.class.php");
				// Create an object of AuthorizeAPI class
				$objAuthorizeAPI                    =   new AuthorizeAPI(AUTHORIZE_PAYMENT_LOGIN_ID, AUTHORIZE_PAYMENT_TRANSACTION_KEY, 'liveMode');
							
				$last_wallet_id				=	$employeeObj->getSingleQueryResult("SELECT transactionId FROM wallet_transactions ORDER BY transactionId DESC LIMIT 1","transactionId");

				$referenceNumber			=   $last_wallet_id;
				if(strlen($referenceNumber) < 4){
					$referenceNumber		=	"1010".$referenceNumber;
				}
				$referenceNumber			=	$memberId.$referenceNumber.

				$invNo						=	$referenceNumber;
				$item_id					=	rand(111111,999999).$memberId;
				$advanceAmount				=	$charge_money = $autoWalletRechargeAmount;

				try{
	                $captureMoney          = $objAuthorizeAPI->chargeCCeCheck($customerProfileId,$customerPaymentProfileId, $charge_money);
	                $captureMoney          =   @json_decode($captureMoney,TRUE);

	                $addedText		       =	"Auto Recharge Wallet While Place Order - ".$charge_money;
	                $commonClass->trackAuthorizeResponseWithDetails($captureMoney,'Authorize.net',0,$memberId,$addedText, $customerProfileId,$customerPaymentProfileId);

	                if(array_key_exists('success',$captureMoney) && $captureMoney['success'] == 1 && array_key_exists('paymentFlag',$captureMoney) && $captureMoney['paymentFlag'] == 1){
					
						$onlineTransactionsId	=	 htmlspecialchars($captureMoney['transId']); 

						dbQuery("INSERT INTO wallet_transactions SET memberId=$memberId,amount='$advanceAmount',transactionType='credit',creditType='own',proceedingDate='".CURRENT_DATE_INDIA."',proceedingTime='".CURRENT_TIME_INDIA."',estProceedingDate='".CURRENT_DATE_CUSTOMER_ZONE."',estProceedingTime='".CURRENT_TIME_CUSTOMER_ZONE."',status='success',paymentThrough='authorizedaccounts',authorizedAccountId=$customerProfileId,referenceNumber='$referenceNumber',onlineTransactionsId='$onlineTransactionsId',customerProfileId='$customerProfileId',customerPaymentProfileId='$customerPaymentProfileId',customerShippingAddressId='$customerShippingAddressId',invNo='$invNo',isAutoRecharge=1");

							$walletTransactionId	=  mysqli_insert_id($db_conn);

							$query					=	"SELECT amount,accountId FROM wallet_master WHERE memberId=$memberId";
							$result					=	dbQuery($query);
							if(mysqli_num_rows($result)){

								$row				=	mysqli_fetch_assoc($result);
								$accountId			=	$row['accountId'];
								$walletAmount		=	stripslashes($row['amount']);

								dbQuery("UPDATE wallet_master SET amount=amount+$advanceAmount WHERE memberId=$memberId");

								$currentBalance	=	$walletAmount+$advanceAmount;
								$currentBalance	=	round($currentBalance,2);

								dbQuery("UPDATE wallet_transactions SET currentBalance='$currentBalance' WHERE memberId=$memberId AND transactionId=$walletTransactionId");
							}
							else{
								dbQuery("INSERT INTO wallet_master SET amount='$advanceAmount',memberId=$memberId");

								dbQuery("UPDATE wallet_transactions SET currentBalance='$advanceAmount' WHERE memberId=$memberId AND transactionId=$walletTransactionId");
							}

							/////////////// START OF SENDING EMAIL BLOCK////////////////////

							$paymentAmount			=	displayMoneyExpo($advanceAmount);
							$emailSubject			=	$paymentAmount." has been successfully credited to your ieIMPACT wallet";

							$a_templateSubject		=	array("{emailSubject}"=>$emailSubject);

							$receivedOn				=	showDate($customer_zone_date);

							$a_templateData			=	array("{name}"=>$completeName,"{referenceNo}"=>$referenceNumber,"{paymentMode}"=>"Account On-File ","{receivedOn}"=>$receivedOn,"{paymentAmount}"=>$paymentAmount);
							$uniqueTemplateName		=	"TEMPLATE_SENDING_SUCCESS_WALLET_PAYMENT";
							$toEmail				=	$customerEmail;

							include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

							if(!empty($a_managerEmails))
							{
								$a_managerEmails	    =	stringReplace(',john@ieimpact.net','',$a_managerEmails);

								$admin_email_sub		=	$paymentAmount." Wallet payment recd from ".$completeName." on ".$receivedOn;


								$a_templateSubject		=	array("{emailSubject}"=>$admin_email_sub);


								$uniqueTemplateName		=	"TEMPLATE_SENDING_SUCCESS_WALLET_PAYMENT";
								$toEmail				=	DEFAULT_BCC_EMAIL;
								$managerEmployeeFromBcc =	$a_managerEmails;

								include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");				
							}

							/////////END OF SENDING EMAIL BLOCK///////////////
							$isAmaxTransactions		=	0;
							if(!empty($cardType) && $cardType	== "American Express"){	
								$isAmaxTransactions	=	1;
							}

							$memberObj->addEditCustomersAllTransaction($paymentAmount,$memberId,2,'Recharge wallet through - Account On-File','Recharge wallet through - Account On-File',$isAmaxTransactions);
						}						
						else{
							$errorCode          =  $captureMoney['errorCode']; 					
							$errorAuto		    =  htmlspecialchars($captureMoney['message']) . "&nbsp;(Error Code - ".$errorCode.")<br>";	

							if($errorCode	    == "E00040")
							{
								////////////////////// THE ACCOUNT IS GET DELETED FROM AUTHORIZE.NET ///////////

								dbQuery("UPDATE members SET isRemindedCard=0,remindAccountType='',selectedRemindId='0',lastVerifyStatus='' WHERE memberId=$memberId");

								dbQuery("DELETE FROM auto_remember_account_details WHERE memberId=$memberId AND accountId=$accountId");
								

								$lastAccountId		=	$memberObj->getSingleDataResult("SELECT accountId FROM auto_remember_account_details WHERE memberId=$memberId ORDER BY accountId DESC LIMIT 1","accountId");

								if(!empty($lastAccountId))
								{
									dbQuery("UPDATE members SET isRemindedCard=1,remindAccountType='creditcard',selectedRemindId='$lastAccountId',lastVerifyStatus='success' WHERE memberId=$memberId");

									dbQuery("UPDATE auto_remember_account_details SET isCurrentlyUsed=1 WHERE memberId=$memberId AND accountId=$lastAccountId");
								}

								//////////////////////////////////////////////////////////////////////////
							}


							////////////////////////////////// ADMIN EMAILS //////////////////
							$uniqueTemplateName	=	"TEMPLATE_SENDING_SIMPLEE_CUSTOMER_MESSAGE";
							$toEmail			=	"john@ieimpact.net";
							//$toEmail			=	"gaurabsiva1@gmail.com";

							$emailSubject		=	"Error while employee creating order auto recharge of  ".$autoWalletRechargeAmount." by ".$completeName;

							$a_templateSubject	=	array("{emailSubject}"=>$emailSubject);

							
							$emailBody	       =	"<table width='98%' align='center' border='0' cellpadding='2' cellspacing='0'><tr><td colspan='3'><b>Error while new order auto recharge of  $".$autoWalletRechargeAmount." by ".$completeName." for placing new order # ".$orderAddress."</b></td></tr><tr><td width='15%'>Error</td><td width='2%'>:</td><td>".$errorAuto."</td></tr><tr><td>Source</td><td>:</td><td>".$_SERVER['REQUEST_URI']."</td></tr></table>";

							$a_templateData		    =	array("{completeName}"=>"John","{emailBody}"=>$emailBody);

							$managerEmployeeFromBcc	=   "gaurabsiva1@gmail.com,gaurabsiva1@yahoo.co.in";	

							include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
						}
					
					
				}
				catch(Exception $e){
					$errorAuto			=   $e->getMessage();
					////////////////// GATEWAY ERROR WHILE AUTO RECHARGE /////////////////
								
					///////////////////////////// ADMIN EMAILS ///////////////////////////
					$uniqueTemplateName	=	"TEMPLATE_SENDING_SIMPLEE_CUSTOMER_MESSAGE";
					$toEmail			=	"john@ieimpact.net";
					//$toEmail			=	"gaurabsiva1@gmail.com";

					$emailSubject		=	"Error while employee creating order auto recharge of  ".$autoWalletRechargeAmount." by ".$completeName;

					$a_templateSubject	=	array("{emailSubject}"=>$emailSubject);

					
					$emailBody	        =	"<table width='98%' align='center' border='0' cellpadding='2' cellspacing='0'><tr><td colspan='3'><b>Error while new order auto recharge of  $".$autoWalletRechargeAmount." by ".$completeName." for placing new order # ".$orderAddress."</b></td></tr><tr><td width='15%'>Error</td><td width='2%'>:</td><td>".$errorAuto."</td></tr><tr><td>Source</td><td>:</td><td>".$_SERVER['REQUEST_URI']."</td></tr></table>";

					$a_templateData		    =	array("{completeName}"=>"John","{emailBody}"=>$emailBody);

					$managerEmployeeFromBcc	=   "gaurabsiva1@gmail.com,gaurabsiva1@yahoo.co.in";	

					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
				}
			}
			else{
				////////////////// GATEWAY ERROR WHILE AUTO RECHARGE /////////////////
								
					///////////////////////////// ADMIN EMAILS ///////////////////////////
					$uniqueTemplateName	=	"TEMPLATE_SENDING_SIMPLEE_CUSTOMER_MESSAGE";
					$toEmail			=	"john@ieimpact.net";
					//$toEmail			=	"gaurabsiva1@gmail.com";

					$emailSubject		=	"Error while employee creating order auto recharge of  ".$autoWalletRechargeAmount." by ".$completeName;

					$a_templateSubject	=	array("{emailSubject}"=>$emailSubject);

					
					$emailBody	        =	"<table width='98%' align='center' border='0' cellpadding='2' cellspacing='0'><tr><td colspan='3'><b>Error while new order auto recharge of  $".$autoWalletRechargeAmount." by ".$completeName." for placing new order # ".$orderAddress."</b></td></tr><tr><td width='15%'>Error</td><td width='2%'>:</td><td>URGENT AND IMPORTANT:</b> Your credit card ending ".$cardLastDigits." is getting declined. Please update your credit card ASAP. Only then our system can allow us to process your orders on time.</td></tr></table>";

					$a_templateData		    =	array("{completeName}"=>"John","{emailBody}"=>$emailBody);

					$managerEmployeeFromBcc	=   "gaurabsiva1@gmail.com,gaurabsiva1@yahoo.co.in";	

					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
			}
		}
		else{
			////////////////// CUSTOMER DOESNOT ADDED ANY WALLET ACCOUNT TO AUTO RECHARGE //////
			//////////////////////////// SENDING EMAIL TO ADMIN ///////////////////////////////
			$uniqueTemplateName	=	"TEMPLATE_SENDING_SIMPLEE_CUSTOMER_MESSAGE";
			$toEmail			=	"john@ieimpact.net";
			//$toEmail			=	"gaurabsiva1@gmail.com";

			$emailSubject		=	"No Account found for auto recharge of  ".$autoWalletRechargeAmount." by ".$completeName;

			$a_templateSubject	=	array("{emailSubject}"=>$emailSubject);

			$emailBody	        =	"<table width='98%' align='center' border='0' cellpadding='2' cellspacing='0'><tr><td colspan='3'><b>No Account found for auto recharge of  $".$autoWalletRechargeAmount." by ".$completeName." for placing new order # ".$orderAddress."</b><br /></td></tr></table>";

			$a_templateData		    =	array("{completeName}"=>"John","{emailBody}"=>$emailBody);

			$managerEmployeeFromBcc	=   "gaurabsiva1@gmail.com,gaurabsiva1@yahoo.co.in";	

			include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
		}
	}
		
?>