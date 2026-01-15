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
		$query							=	"SELECT * FROM auto_remember_account_details WHERE memberId=$memberId".$serachAccountClause." AND isCurrentlyUsed=1 ORDER BY accountId DESC";
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
			$isIncludedAuthorizedClass  =   1;

			include(SITE_ROOT			.   "/classes/vars.php");
			include(SITE_ROOT			.   "/classes/util.php");

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
				$content =
					"<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
					"<createCustomerProfileTransactionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
					MerchantAuthenticationBlock().
					"<transaction>".
					"<profileTransAuthCapture>".
					"<amount>".$charge_money."</amount>". // should include tax, shipping, and everything.
					"<lineItems>".
					"<itemId>".$item_id."</itemId>".
					"<name>Data entry report</name>".
					"<description>Data entry report wallet</description>".
					"<quantity>1</quantity>".
					"<unitPrice>1</unitPrice>".
					"<taxable>false</taxable>".
					"</lineItems>".
					"<customerProfileId>".$customerProfileId."</customerProfileId>".
					"<customerPaymentProfileId>".$customerPaymentProfileId."</customerPaymentProfileId>".
					"<customerShippingAddressId>".$customerShippingAddressId."</customerShippingAddressId>".
					"<order>".
					"<invoiceNumber>".$invNo."</invoiceNumber>".
					"<description>Wallet Payment</description>".
					"</order>".
					"</profileTransAuthCapture>".
					"</transaction>".
					"<extraOptions><![CDATA[x_duplicate_window=1]]></extraOptions>".
					"</createCustomerProfileTransactionRequest>";

					$response       =     send_xml_request($content);
					/////////////////////////////////////////////////////////////////////////////
					//////////////////// BLOCK TO ADDING AUTHORIZE RESPONSE /////////////////////
					/////////////////////////////////////////////////////////////////////////////
					$response_xml	=   @simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
					$response_json	=   @json_encode($response_xml);
					$response_json	=   @json_decode($response_json,TRUE);
					$email_authorize_response = @recursive_implode($response_json);
					$addedText		=	"Auto Recharge Wallet While Place Order - ".$charge_money;
					//$trackData		=		$commonClass->trackAuthorizeResponse($response_json,'Authorize.net',0,$memberId,$addedText);
					/////////////////////////////////////////////////////////////////////////////
					/////////////////////////////////////////////////////////////////////////////
					$parsedresponse =     parse_api_response($response);
					
					if ("Ok"        ==    $parsedresponse->messages->resultCode) {

					}
					if (isset($parsedresponse->directResponse))
					{

						$directResponseFields		= explode(",", $parsedresponse->directResponse);
						$responseCode				= $directResponseFields[0]; // 1 = Approved 2 = Declined 3 = Error
						$responseReasonCode			= $directResponseFields[2]; // See http://www.authorize.net/support/AIM_guide.pdf
						$responseReasonText			= $directResponseFields[3];
						$approvalCode				= $directResponseFields[4]; // Authorization code
						$transId					= $directResponseFields[6];

						if($responseCode			==	"1")
						{
							$onlineTransactionsId	=	htmlspecialchars($transId);	

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
						$errorAuto			=	"";
						$autoErrorCode		= $parsedresponse->messages->message->code;
						foreach ($parsedresponse->messages->message as $msg) {
							$errorAuto		=  htmlspecialchars($msg->text) . "&nbsp;(Error Code - ".$autoErrorCode.")<br>";
						}	


						////////////////////////////////// ADMIN EMAILS //////////////////
						$uniqueTemplateName	=	"TEMPLATE_SENDING_SIMPLEE_CUSTOMER_MESSAGE";
						$toEmail			=	"john@ieimpact.net";
						//$toEmail			=	"gaurabsiva1@gmail.com";

						$emailSubject		=	"Error while employee creating order auto recharge of  ".$autoWalletRechargeAmount." by ".$completeName;

						$a_templateSubject	=	array("{emailSubject}"=>$emailSubject);

						
						$emailBody	       =	"<table width='98%' align='center' border='0' cellpadding='2' cellspacing='0'><tr><td colspan='3'><b>Error while new order auto recharge of  $".$autoWalletRechargeAmount." by ".$completeName." for placing new order # ".$orderAddress."</b></td></tr><tr><td width='15%'>Error</td><td width='2%'>:</td><td>".$errorAuto."</td></tr></table>";

						$a_templateData		    =	array("{completeName}"=>"John","{emailBody}"=>$emailBody);

						$managerEmployeeFromBcc	=   "gaurabsiva1@gmail.com,gaurabsiva1@yahoo.co.in";	

						include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
					}
				}
				else{
					$autoErrorCode		= $parsedresponse->messages->message->code;
					$errorAuto		= "";
					foreach ($parsedresponse->messages->message as $msg) {
						$errorAuto		.=  htmlspecialchars($msg->text) . "&nbsp;(Error Code - ".$autoErrorCode.")<br>";
					}
					
					////////////////// GATEWAY ERROR WHILE AUTO RECHARGE //////////////
					
					////////////////////////////////// ADMIN EMAILS //////////////////
					$uniqueTemplateName	=	"TEMPLATE_SENDING_SIMPLEE_CUSTOMER_MESSAGE";
					$toEmail			=	"john@ieimpact.net";
					//$toEmail			=	"gaurabsiva1@gmail.com";

					$emailSubject		=	"Error while employee creating order auto recharge of  ".$autoWalletRechargeAmount." by ".$completeName;

					$a_templateSubject	=	array("{emailSubject}"=>$emailSubject);

				
					$emailBody	        =	"<table width='98%' align='center' border='0' cellpadding='2' cellspacing='0'><tr><td colspan='3'><b>Error while new order auto recharge of  $".$autoWalletRechargeAmount." by ".$completeName." for placing new order # ".$orderAddress."</b></td><tr><td width='15%'>Error</td><td width='2%'>:</td><td>".$errorAuto."</td></tr></table>";

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

				
				$emailBody	        =	"<table width='98%' align='center' border='0' cellpadding='2' cellspacing='0'><tr><td colspan='3'><b>Error while new order auto recharge of  $".$autoWalletRechargeAmount." by ".$completeName." for placing new order # ".$orderAddress."</b></td></tr><tr><td width='15%'>Error</td><td width='2%'>:</td><td>".$errorAuto."</td></tr></table>";

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