<?php
	$displayOtherOrderType		=	"none";
	if(empty($customersOwnOrderText))
	{
		$customersOwnOrderText	=	"Enter Other Type";
	}
	$verifyOrderErrorMsg		=	"";
	$orderTypeText				=	"";
	$etaText					=	"";
	$sketchText					=	"<img src='".SITE_URL."/images/uncheck-checkbox.jpg' height='12' width='12'>";
	$nonUadText					=	"<img src='".SITE_URL."/images/uncheck-checkbox.jpg' height='12' width='12'>";
	$reoText					=	"<img src='".SITE_URL."/images/uncheck-checkbox.jpg' height='12' width='12'>";
	$financingText				=	"";
	$shortOrderDefaultAddress	=	$customerName." EZ order";
	$shortOrderDefaultAddress   =   strtolower($shortOrderDefaultAddress);

	$a_membersUploadingFilles	=	array();

	if($result					=	$orderObj->getMultipleOrderFiles($orderId,$customerId,1,1))
	{
		while($row				=	mysqli_fetch_assoc($result))
		{
			
			$uploadingFileName	=	stripslashes($row['uploadingFileName']);
			$uploadingFileExt	=	stripslashes($row['uploadingFileExt']);
			$uploadingFileSize	=	$row['uploadingFileSize'];

			$a_membersUploadingFilles[]	=	$uploadingFileName.".".$uploadingFileExt."|".getFileSize($uploadingFileSize);
		}
	}
	

	if(isset($_REQUEST['verifyEmailOrderSubmitted']))
	{
		extract($_REQUEST);

		$verifyOrderSubject		=	trim($verifyOrderSubject);
		$customersOwnOrderText	=	trim($customersOwnOrderText);

		$checkedCompsFiles		=	trim($checkedCompsFiles);
		$checkEmployeeNotes		=	trim($checkEmployeeNotes);

		
		/*if($editInstructions	==	1)
		{
			$instructions		=	trim($editedInstructions);
		}*/
		
		$hasSketch				=	0;
		if($providedSketch		==	1)
		{		 
		  $hasSketch			=	1;
		  $sketchText			=	"Yes";
		}

		$txt_orderAddress		=	stripslashes($verifyOrderSubject);
		$txt_instructions		=	stripslashes($instructions);

		$orderAddress			=	makeDBSafe($verifyOrderSubject);
		//$instructions			=	makeDBSafe($instructions);

		$checkEmployeeNotes		=	makeDBSafe($checkEmployeeNotes);


		if(isset($_POST['reoForm']))
		{
			$reoForm			=	$_POST['reoForm'];
			$checkedReoForm		=	"checked";
			$reoText			=	"<img src='".SITE_URL."/images/check-checkbox.jpg' height='12' width='12'>";
		}
		else
		{
			$reoForm			=	0;
		}
		if(isset($_POST['financingVa']))
		{
			$financingVa		=	$_POST['financingVa'];
			$checkFinancingVa	=	"checked";
			$financingText	   .=   "VA : <img src='".SITE_URL."/images/check-checkbox.jpg' height='12' width='12'>&nbsp;";
		}
		else
		{
			$financingVa		=	0;
			$financingText	   .=   "VA : <img src='".SITE_URL."/images/uncheck-checkbox.jpg' height='12' width='12'>&nbsp;";
		}
		if(isset($_POST['financingFha']))
		{
			$financingFha		=	$_POST['financingFha'];
			$checkFinancingFha	=	"checked";
			$financingText	   .=   "FHA : <img src='".SITE_URL."/images/check-checkbox.jpg' height='12' width='12'>&nbsp;";
		}
		else
		{
			$financingFha		=	0;
			$financingText	   .=   "FHA : <img src='".SITE_URL."/images/uncheck-checkbox.jpg' height='12' width='12'>&nbsp;";
		}
		if(isset($_POST['financingHud']))
		{
			$financingHud		=	$_POST['financingHud'];
			$checkFinancingHud	=	"checked";
			$financingText	   .=   "HUD : <img src='".SITE_URL."/images/check-checkbox.jpg' height='12' width='12'>&nbsp;";
		}
		else
		{
			$financingHud		=	0;
			$financingText	   .=   "HUD : <img src='".SITE_URL."/images/uncheck-checkbox.jpg' height='12' width='12'>&nbsp;";
		}
		if(isset($_POST['nonUad']))
		{
			$nonUad				=	$_POST['nonUad'];
			$checkNonUad		=	"checked";
			$displayNonUadText	=	"";
			$nonUadText			=	"<img src='".SITE_URL."/images/check-checkbox.jpg' height='12' width='12'>";
		}
		else
		{
			$nonUad						=	0;
		}
		$orderTypeText					=	$a_customerOrder[$orderType];

		if($orderType					==	6)
		{
			$displayOtherOrderType		=	"";
			if($customersOwnOrderText	==	"Enter Other Type")
			{
				$customersOwnOrderText	=	"";
			}
			if(!empty($customersOwnOrderText))
			{
				$orderTypeText			=	$orderTypeText."&nbsp;(".$customersOwnOrderText.")";
			}
		}
		else
		{
			$customersOwnOrderText		=	"";
		}
		

		if(empty($verifyOrderSubject))
		{
			$verifyOrderErrorMsg		.=	"Please enter order address.<br />";
		}
		else{
			if($isShortOrder == 1){
				$checkWithOrderAddress	  =  strtolower($verifyOrderSubject);
				if($checkWithOrderAddress == $shortOrderDefaultAddress){
					$verifyOrderErrorMsg .=	"Please enter proper order address, not with customer name.<br />";
				}
			}
		}
		if($isRushOrder == "" && $isRushOrder != "0")
		{
			$verifyOrderErrorMsg		.=	"Please enter order ETA.<br />";
		}
		if(empty($orderType))
		{
			$verifyOrderErrorMsg		.=	"Please select order type.<br />";
		}
		else
		{
			if($orderType	==	6 && empty($customersOwnOrderText))
			{
				$verifyOrderErrorMsg	.=	"Please enter other order type.<br />";
			}
		}
		/*if(empty($instructions) && $editInstructions	==	1)
		{
			$verifyOrderErrorMsg	.=	"Please enter instructions.<br />";
		}*/

		if(isset($_POST['readFileChecklist']))
		{
			$a_readChecklist	  =	$_POST['readFileChecklist'];
			$totalListChecked	  =	count($a_readChecklist);
			if($totalListChecked !=	10)
			{
				$verifyOrderErrorMsg   .=	"Please complete the received data checklist.<br />";
			}
		}
		else
		{
			$verifyOrderErrorMsg      .=	"Please complete the received data checklist.<br />";
		}

		if(empty($checkedCompsFiles))
		{
			$verifyOrderErrorMsg		.=	"Please enter number of comps sent.<br />";
		}

		if(empty($checkEmployeeNotes))
		{
			$verifyOrderErrorMsg		.=	"Please enter internal employee notes.<br />";
		}


		$checkedCompsFiles		=	trim($checkedCompsFiles);
		$checkEmployeeNotes		=	trim($checkEmployeeNotes);

		if(isset($_POST['markedChecklistSendSms']))
		{
			$markedChecklistSendSms = $_POST['markedChecklistSendSms'];
		}
		else
		{
			$markedChecklistSendSms = 0;
		}

		$t_orderType				=	$a_customerOrder[$orderType];
		if($orderType				==	6 && !empty($customersOwnOrderText))
		{
			$t_orderType			=	$t_orderType."&nbsp;(".$customersOwnOrderText.")";
		}

		
		if(empty($verifyOrderErrorMsg))
		{
			$isStillUnverified			=	$employeeObj->getSingleQueryResult("SELECT orderId FROM members_orders WHERE orderId=$orderId AND memberId=$customerId AND isEmailOrder=1 AND isNotVerfidedEmailOrder=1","orderId");

			if(!empty($isStillUnverified))
			{				
				$single_order_price		     =	$memberObj->getMemberPostOrderPrice($customerId,$orderId,$orderType,$nowDateIndia,$isRushOrder);

				$extra_columns	  	         =	"";
				$is_failed_prepaid_payment   =  0;
				$admin_email_message_case    =  "";
				$send_admin_email_fail_wallet=  0;

				if($captureEmailOrderThrough == "wallet" && $isPaidThroughWallet == "no" && $walletAccountId == 0)
				{
					$walletAmount			 =	$employeeObj->getSingleQueryResult("SELECT amount FROM wallet_master WHERE memberId=$customerId","amount");
					if(!empty($walletAmount) && $walletAmount >= $single_order_price){
						
						dbQuery("UPDATE wallet_master SET amount=amount-$single_order_price WHERE memberId=$customerId");

						$walletAmount	=	$employeeObj->getSingleQueryResult("SELECT amount FROM wallet_master WHERE memberId=$customerId","amount");
						$walletAmount	=	round($walletAmount,2);
									
						dbQuery("INSERT INTO wallet_transactions SET memberId=$customerId,amount='$single_order_price',transactionType='debit',debitType='orders',transactionDate='".CURRENT_DATE_INDIA."',transactionTime='".CURRENT_TIME_INDIA."',estTransactionDate='".CURRENT_DATE_CUSTOMER_ZONE."',estTransactionTime='".CURRENT_TIME_CUSTOMER_ZONE."',status='success',ipAddress='".VISITOR_IP_ADDRESS."',orderId=$orderId,orderAddress='$orderAddress',currentBalance='$walletAmount'");

						$walletAccountId	=  mysqli_insert_id($db_conn);

						$extra_columns		=	",isPaidThroughWallet='yes',walletAccountId=$walletAccountId,prepaidOrderPrice='$single_order_price'";

					}
					else{
						$extra_columns		=	",prepaidOrderPrice='$single_order_price'";
						$send_admin_email_fail_wallet=  1;
					}

				}
				elseif($captureEmailOrderThrough == "wallet" && $isPaidThroughWallet == "yes" && !empty($walletAccountId))
				{					
					$prepaidOrderPrice." - ".$single_order_price;
					///// ALREADY PAID SUCCESS THROUGH WALLET ////////
					if($prepaidOrderPrice != $single_order_price && $prepaidOrderPrice > $single_order_price){						
						$moneyDifference	=	$prepaidOrderPrice-$single_order_price;
						$moneyDifference    =   round($moneyDifference,2);

						dbQuery("UPDATE wallet_transactions SET amount='$single_order_price' WHERE transactionId=$walletAccountId AND memberId=$customerId AND orderId=$orderId");

						$query11			=	"SELECT * FROM wallet_transactions WHERE memberId=$customerId AND transactionId >= $walletAccountId ORDER BY transactionId";
						$result11			=	dbQuery($query11);
						if(mysqli_num_rows($result11)){
							while($row11	=	mysqli_fetch_assoc($result11)){
								$t_transactionId	=	$row11['transactionId'];
								$t_transactionType	=	$row11['transactionType'];
								$t_amount			=	$row11['amount'];

								$lastWalletBalance	=	$employeeObj->getSingleQueryResult("SELECT currentBalance FROM wallet_transactions WHERE memberId=$customerId AND transactionId < $t_transactionId ORDER BY transactionId DESC LIMIT 1","currentBalance");

								$current_balance	  =	$t_amount+$lastWalletBalance;
								if($t_transactionType == "debit"){
									$current_balance  =	$lastWalletBalance-$t_amount;
								}
								$current_balance	  =	round($current_balance,2);

								dbQuery("UPDATE wallet_transactions SET currentBalance='$current_balance' WHERE transactionId=$t_transactionId AND memberId=$customerId");
							}
						}

						dbQuery("UPDATE wallet_master SET amount=amount+$moneyDifference WHERE memberId=$customerId");
					}
					$extra_columns			=	",prepaidOrderPrice='$single_order_price'";
				}
				elseif($paymentGateway == "Stripe" && !empty($stripeChargeId))
				{
					$extra_columns			=	",prepaidOrderPrice='$single_order_price'";
				}
				else{
				
					if($isAuthorizedEmailOrder == 1 && $isPrepaidOrder == 1 && !empty($prepaidTransactionId) && !empty($single_order_price))
					{
						try{
							include(SITE_ROOT			.   "/classes/vars.php");
							include(SITE_ROOT		    .   "/classes/util.php");

							$exponent_money				=	$memberObj->getMoneyExponent($single_order_price);

							//echo "<br /><br />TRANS - ".$prepaidTransactionId;
							//echo "<br /><br />PRO - ".$customerProfileId;
							//echo "<br /><br />SHIPP - ".$customerShippingAddressId;
							//echo "<br /><br />PAYMENT - ".$customerPaymentProfileId;

						$content =
							"<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
							"<createCustomerProfileTransactionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
							MerchantAuthenticationBlock().
							"<transaction>".
							"<profileTransPriorAuthCapture>".
							"<amount>".$exponent_money."</amount>".
							"<customerProfileId>".$customerProfileId."</customerProfileId>".
							"<customerPaymentProfileId>".$customerPaymentProfileId."</customerPaymentProfileId>".
							"<customerShippingAddressId>".$customerShippingAddressId."</customerShippingAddressId>".
							"<transId>".$prepaidTransactionId."</transId>".
							"</profileTransPriorAuthCapture>".
							"</transaction>".
							"<extraOptions><![CDATA[]]></extraOptions>".
							"</createCustomerProfileTransactionRequest>";

							//echo "<br /><br />Raw request: " . htmlspecialchars($content) . "<br><br>";
							//$response = send_xml_request($content);
							//echo "<br /><br />Raw response: " . htmlspecialchars($response) . "<br><br>";
							//die();

							$response		= send_xml_request($content);
							/////////////////////////////////////////////////////////////////////////////
							//////////////////// BLOCK TO ADDING AUTHORIZE RESPONSE /////////////////////
							/////////////////////////////////////////////////////////////////////////////
							$response_xml	=   @simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
							$response_json	=   @json_encode($response_xml);
							$response_json	=   @json_decode($response_json,TRUE);
							$email_authorize_response = @recursive_implode($response_json);
							$addedText		=	"Auth Capture Email Order - ".$orderAddress;
							$commonClass	= $commonClass->trackAuthorizeResponseWithDetails($response_json,'Authorize.net',$orderId,$customerId,$addedText,$customerProfileId,$customerPaymentProfileId);
							/////////////////////////////////////////////////////////////////////////////
							/////////////////////////////////////////////////////////////////////////////
							$parsedresponse = parse_api_response($response);
							if ("Ok"        ==    $parsedresponse->messages->resultCode) {
								
								if (isset($parsedresponse->directResponse)) {
		
									$directResponseFields = explode(",", $parsedresponse->directResponse);
									$responseCode = $directResponseFields[0]; // 1 = Approved 2 = Declined 3 = Error
									$responseReasonCode = $directResponseFields[2]; // See http://www.authorize.net/support/AIM_guide.pdf
									$responseReasonText = $directResponseFields[3];
									$approvalCode = $directResponseFields[4]; // Authorization code
									$transId = $directResponseFields[6];

									if($responseCode		==	"1")
									{
										/******************************************************************/
										//****** UPDATE ORDER MAKING ADVANCED PAYMENT FOR THIS ORDER ******/
										dbQuery("INSERT INTO advance_payment_money SET memberId=$customerId,paymentType=2,paymentAmount='$single_order_price',paymentStatus=2,date='".$nowDateIndia."',time='".$nowTimeIndia."',ip='".VISITOR_IP_ADDRESS."',statusText='Successfully Advanced Payment Made Through Paypal',excatProceedingIstDate='".$nowDateIndia."',excatIstProceedingEstTime='".$nowTimeIndia."',excatProceedingEstDate='".$customer_zone_date."',excatProceedingEstTime='".$customer_zone_time."',isForPrepiadOrder=1,paidOn='".$nowDateIndia."',paidTime='".$nowTimeIndia."',paidIP='".VISITOR_IP_ADDRESS."'");

										$advanced_payment_id	=	mysqli_insert_id($db_conn);

										$extra_columns			=	",advancedPaymentId=$advanced_payment_id,prepaidOrderPrice='$single_order_price',prepiadPaymentThrough='creditcard'";

										list($p_year,$p_month,$p_day)		=	explode("-",$nowDateIndia);

										if($receivedId = $memberObj->isExistsMonthAdvanceMoney($customerId,$p_month,$p_year))
										{
											dbQuery("UPDATE total_advance_month_money SET amountRceived=amountRceived+$single_order_price WHERE receivedId=$receivedId AND memberId=$customerId");
										}
										else
										{
											dbQuery("INSERT INTO total_advance_month_money SET amountRceived='$single_order_price',memberId=$customerId,month=$p_month,year=$p_year");
										}
									}
								}
								else
								{								
									////////////////////////////////////////////////////////////////////////
									/* THIS BLOCK TO MAKE THE ORDER AS IN IS DELETED STAGE AND GOES TO RETRY*/
									$is_failed_prepaid_payment =    1;
									$admin_email_message_case  =    "";

									$errorMsgPay			   =	"";
									$errorCode				   = $parsedresponse->messages->message->code;
									foreach ($parsedresponse->messages->message as $msg) {
										$errorMsgPay		  .= htmlspecialchars($msg->text)."&nbsp;(Error Code - ".$errorCode.")<br>";
									}

									$admin_email_message_case		=	"<br /><br />Customer Name : ".$customerName." & Verify Email Order Reason Server Error: Didn't get any response.(Debug Case - V - In Verify Email Order)<br />Gateway response : ".$email_authorize_response."<br />";
						
								}

							}
							else{
								////////////////////////////////////////////////////////////////////////////////
								/****** THIS BLOCK TO MAKE THE ORDER AS IN IS DELETED STAGE AND GOES TO RETRY**/
								$is_failed_prepaid_payment =    1;
								$admin_email_message_case  =    "";

								$errorMsgPay			   =	"";
								$errorCode  = $parsedresponse->messages->message->code;
								foreach ($parsedresponse->messages->message as $msg) {
									$errorMsgPay		  .= htmlspecialchars($msg->text)."&nbsp;(Error Code - ".$errorCode.")<br>";
								}

								$admin_email_message_case		=	"<br /><br />Customer Name : ".$customerName." & Verify Email Order Reason Server Error: ".$errorMsgPay." (Debug Case - VI - In Verify Email Order)<br />Gateway response : ".$email_authorize_response."<br />";
						
							}
						}
						catch(Exception $e){
							$errorMsgPay	=	$e->getMessage();	
							
							$is_failed_prepaid_payment =    1;
							$admin_email_message_case  =    "";

							$admin_email_message_case		=	"<br /><br />Customer Name : ".$customerName." & Verify Email Order Reason Server Error: ".$errorMsgPay." (Debug Case - VII)<br />";
						}	
					}	
					elseif($isAuthorizedEmailOrder == 1 && $isPrepaidOrder == 0 && !empty($prepaidTransactionId) && !empty($single_order_price))
					{
						$extra_columns			=	",prepaidOrderPrice='$single_order_price'";
					}
				}
				/////////// CREATE UNIQUE EMAIL REPLY TO EMAIL /////////////				
				$memberOrderReplyToEmail	=	$memberUniqueEmailCode.$cutomerTotalOrdersPlaced;
				$memberOrderReplyToEmail	=	makeDBSafe($memberOrderReplyToEmail);
				
				$query	 =	"UPDATE members_orders SET orderAddress='$orderAddress',orderType=$orderType,providedSketch=$hasSketch,customersOwnOrderText='$customersOwnOrderText',orderReplyToEmail='$memberOrderReplyToEmail',isRushOrder=$isRushOrder".$extra_columns." WHERE orderId=$orderId AND memberId=$customerId";
				dbQuery($query);

				////////////////////////////////////////////////////////////////////////////////////////
				//////////////////// PUTTING THE ORDER IN ORDER TRACK LIST ////////////////////////////
			    $orderObj->addOrderTracker($s_employeeId,$orderId,$orderAddress,'Employee verify email order','EMPLOYEE_VERIFY_EMAIL_ORDER');
			    ////////////////////////////////////////////////////////////////////////////////////////
			    ////////////////////////////////////////////////////////////////////////////////////////

				///////////////// Adding checkboxes /////////////////////////////////////////
                if(!empty($reoForm) || !empty($financingVa) || !empty($financingFha) || !empty($financingHud) || !empty($nonUad)){

                	if($isAlreadyAddedCheckbox == 1){
                		dbQuery("UPDATE orders_new_checkboxes SET reoForm=$reoForm,financingVa=$financingVa,financingFha=$financingFha,financingHud=$financingHud,nonUad=$nonUad WHERE orderId=$orderId");
                	}
                	else{
                		dbQuery("INSERT INTO orders_new_checkboxes SET orderId=$orderId,reoForm=$reoForm,financingVa=$financingVa,financingFha=$financingFha,financingHud=$financingHud,nonUad=$nonUad");
                	}
				}
				///////////////////////////////////////////////////////////////////////////////

				$memberObj->setMemberPostOrderPrice($customerId,$orderId,$orderType,$nowDateIndia);
				
				if($isRushOrder				==	0)
				{
					$addingRequiredHrs		=	STANDRAD_ORDER_COMPLETE_TIME_HOURS;
				}
				elseif($isRushOrder			==	1)
				{
					$addingRequiredHrs		=	 RUSH_ORDER_COMPLETE_TIME_HOURS;
				}
				else
				{
					$addingRequiredHrs		=	 ECONOMY_ORDER_COMPLETE_TIME_HOURS;
				}

				$warningDateTimeEmployee	=	getNextCalculatedHours($orderPlacedDate,$orderAddedTime,$addingRequiredHrs);

				list($warningOrderDate,$warningOrderTime)	=	explode("=",$warningDateTimeEmployee);

				dbQuery("UPDATE members_orders SET isHavingEstimatedTime=1,employeeWarningDate='$warningOrderDate',employeeWarningTime='$warningOrderTime' WHERE orderId=$orderId AND memberId=$customerId");

				$myTextFile				=  $newUploadingPath."/".$orderId.".txt";
				if(!file_exists($myTextFile))
				{
					$fh					=  fopen($myTextFile, 'w');
				}
				else
				{
					$fh					=  fopen($myTextFile, 'w');
				}
				
				$stringData = "1) Customer Name : ".$customerName."\r\n";
				fwrite($fh, $stringData);
				//$stringData = " 2) Customer Email : ".$email."\r\n";
				//fwrite($fh, $stringData);
				$stringData = " 2) Order address : ".$txt_orderAddress."\r\n";
				fwrite($fh, $stringData);
				$stringData = " 3) Order date	: ".showdate($orderPlacedDate)."\r\n";
				fwrite($fh, $stringData);
				$stringData = " 4) Order Type	: ".$t_orderType."\r\n";
				fwrite($fh, $stringData);
				$stringData = " 5) Order Instructions	: ".$txt_instructions."\r\n";
				fwrite($fh, $stringData);
				fclose($fh);
				

				$sendingChecklistEmail				=	false;
				$a_choiceSelected					=	array();
				$a_choiceEmailText					=	array();
				$a_choiceEmailSelected				=	array();
				$a_dataNotReceived					=	array();
				$a_displayCheckresultValue			=	array();
				$a_checkedValueText					=	array("1"=>"<font color='#333333'>Yes</font>","2"=>"<font color='#ff0000'>No</font>","3"=>"<font color='#333333'>Not Required</font>");

				foreach($a_readChecklist as $key=>$value)
				{
					list($operation,$listingId)			=	explode("|",$value);
					$operationText						=	$a_checkedValueText[$operation];

					$listingChoice						=	$a_checklistFirstOrderCheck[$listingId];

					list($listName,$dbNameList)			=	explode("|",$listingChoice);

					$listNameSub						=	$listName;

					//$listNameSub						=	stringReplace("Subject ","",$listNameSub);
					//$listNameSub						=	stringReplace("Comps ","",$listNameSub);
					
					if($operation						==	2)
					{
						$sendingChecklistEmail			=	true;
						$a_dataNotReceived[]			=	$listNameSub;
					}				

					$a_choiceSelected[]					=	$dbNameList."=".$operation;
					$a_choiceEmailSelected[$listingId]	=	$operationText;
				}
				$columns=	implode(",",$a_choiceSelected);
				
				$query	=	"INSERT INTO checked_customer_orders SET checkedBy=$s_employeeId,orderId=$orderId,checkedOn='".CURRENT_DATE_INDIA."',checkedOnTime='".CURRENT_TIME_INDIA."',checkedIP='".VISITOR_IP_ADDRESS."',checkedCompsFiles=$checkedCompsFiles ,checkEmployeeNotes='$checkEmployeeNotes',".$columns;
				dbQuery($query);

				dbQuery("UPDATE members_orders SET isOrderChecked=1,orderCheckedBy='$s_employeeName',isNotVerfidedEmailOrder=0,emailOrderVerifiedby=$s_employeeId,emailOrderVerifiedOn='".CURRENT_DATE_INDIA."',emailOrderVerifiedTime='".CURRENT_TIME_INDIA."',emailOrderVerifiedOnEST='".CURRENT_DATE_CUSTOMER_ZONE."',emailOrderVerifiedTimeEST='".CURRENT_TIME_CUSTOMER_ZONE."' WHERE orderId=$orderId AND memberId=$customerId");

				$memberObj->updateOrderRelatedCounts('newOrders');
				$orderObj->deductOrderRelatedCounts('newEmailOrders');

				$performedTask	=	"Verified Email Order";
				
				$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);

				$employeeObj->updateEmployeeTotalChecked($s_employeeId,$s_employeeName,CURRENT_DATE_INDIA);

				if($is_failed_prepaid_payment	   ==   "1")
				{
					///////////////////////////////////////////////////////////////////////
					/************* THIS BLOCK TO MAKE PAYMENT PREPAID IN PAYPAL **********/
					dbQuery("UPDATE members_orders SET isVirtualDeleted=1,isDeleted=1,isProceedPaypalOrder=1,isRetryOption=1 WHERE orderId=$orderId AND memberId=$customerId");

					//////////////////////// START OF SENDING EMAIL BLOCK/////////////////////////////
					include(SITE_ROOT		.   "/classes/email-templates.php");
					$emailObj				=	new emails();

					$uniqueTemplateName		=	"TEMPLATE_SENDING_SIMPLEE_CUSTOMER_MESSAGE";
					$toEmail				=	$customerEmail;

					$credit_card_email_sub  =	" Failure To Proceed Your Order: Missing Credit Card Information.";

					$credit_card_email_msg	=	"Our system failed to proceed your order by email since we do not have a correct payment information on file. Please note starting Dec 1st, 2014, all orders must have been paid in advance, so you must provide correct payment account information on our website. Please login into your account on <a href='https://secure.ieimpact.com/members' target='_blank'>https://secure.ieimpact.com</a> website and click 'New Order' and click 'Edit' Pencil Icon or Click 'Add New Payment' Information. Your credit card information will be securely saved on <a href='https://account.authorize.net/' target='_blank'>authorize.net</a> PCI compliant servers. We do not save any credit card information our local servers. You can retry payemnt form your account.";

												
					$a_templateSubject		=	array("{emailSubject}"=>$credit_card_email_sub);

					$a_templateData			=	array("{completeName}"=>$customerName,"{emailBody}"=>$credit_card_email_msg);

					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

					$uniqueTemplateName		=	"TEMPLATE_SENDING_SIMPLEE_CUSTOMER_MESSAGE";
					$toEmail				=	"john@ieimpact.net";
					//$toEmail				=	"gaurabsiva1@gmail.com";
					$a_templateData			=	array("{completeName}"=>"John","{emailBody}"=>$credit_card_email_msg.$admin_email_message_case);

					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

					$_SESSION['invalid_order_address'] = $orderAddress;
					$_SESSION['invalid_customer_name'] = $customerName;
				
					ob_clean();
					header("Location: ".SITE_URL_EMPLOYEES ."/order-incomplete-prepiad.php");
					exit();

					///////////////////////////////////////////////////////////////////////
				}

				//////////////////////// START OF SENDING EMAIL BLOCK/////////////////////////////
				include(SITE_ROOT		.   "/classes/email-templates.php");
				$emailObj				=	new emails();

				$filesUploaded			=	"";
				if(!empty($a_membersUploadingFilles) && count($a_membersUploadingFilles) > 0)
				{
					$filesUploaded	   .=	"<table width='98%' align='center' border='0' cellpadding='2' cellspacing='0'>";
					foreach($a_membersUploadingFilles as $key=>$uploadedFileName)
					{
						list($uploadedFileName,$filesSize)	=	explode("|",$uploadedFileName);
						
						$filesUploaded .=	"<tr><td valign='top'><font size='2px' face='verdana' color='#787878'>".$uploadedFileName." ".$filesSize."</font></td></tr>";
					}

					$filesUploaded	   .=	"</table>";
				}
				else{
					$filesUploaded			=	"No file uploaded";
				}

				//******************************* SENDING VERIFY EMAIL ORDER EMAIL *********************/
				if($isShortOrder		==	0){
					$uniqueTemplateName	=	"TEMPLATE_SENDING_CUSTOMER_EMAIL_ORDER_VERIFIED";
				}
				else{
					$uniqueTemplateName	=	"TEMPLATE_SENDING_CUSTOMER_EZ_ORDER_VERIFIED";
					
				}
				$toEmail				=	$customerEmail;
				$etaText				=	$a_estimatedTimeArray[$isRushOrder];
				$orderDateTimeText		=	showDateTimeFormat($orderPlacedCustomerDate,$orderAddedCustomerTime,1);

				$setThisEmailReplyToo		 =	$memberOrderReplyToEmail.CUSTOMER_REPLY_EMAIL_TO;//Setting for reply to make customer reply order mesage
					$setThisEmailReplyTooName=	"ieIMPACT Orders";//Setting for reply to make customer reply order mesage

				$a_templateSubject		=	array("{orderAddress}"=>$txt_orderAddress);
				$orderReplyEmail		=	$memberOrderReplyToEmail.CUSTOMER_REPLY_EMAIL_TO;
				$orderReplyEmail		=	"<a href='mailto:".$orderReplyEmail."'>".$orderReplyEmail."</a>";

				$a_templateData			=	array("{completeName}"=>$customerName,"{orderAddress}"=>$txt_orderAddress,"{orderType}"=>$orderTypeText,"{eta}"=>$etaText,"{sketchtext}"=>$sketchText,"{nonUadText}"=>$nonUadText,"{reoText}"=>$reoText,"{financingText}"=>$financingText,"{orderInstructions}"=>$txt_instructions,"{filesUploaded}"=>$filesUploaded,"{orderDateTimeText}"=>$orderDateTimeText,"{orderReplyEmail}"=>$orderReplyEmail);

				include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

				if(!empty($customerSecondaryEmail))
				{
					$toEmail			=	$customerSecondaryEmail;
					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
				}

				$setThisEmailReplyToo			=	"";//Setting for reply to make empty to manager
				$setThisEmailReplyTooName		=	"";//Setting for reply to make empty to manager

				$orderDateTimeText				=	$orderDateTimeText."<br />Verified By Employee : ".$s_employeeName;


				$a_templateData			=	array("{completeName}"=>$customerName,"{orderAddress}"=>$txt_orderAddress,"{orderType}"=>$orderTypeText,"{eta}"=>$etaText,"{sketchtext}"=>$sketchText,"{nonUadText}"=>$nonUadText,"{reoText}"=>$reoText,"{financingText}"=>$financingText,"{orderInstructions}"=>$txt_instructions,"{filesUploaded}"=>$filesUploaded,"{orderDateTimeText}"=>$orderDateTimeText,"{orderReplyEmail}"=>$orderReplyEmail);

				$toEmail						=	"john@ieimpact.net";
				//$toEmail						=	"gaurabsiva1@gmail.com";
				include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

				if($send_admin_email_fail_wallet== 1){
					//////////// SENDING EMAIL TO ADMIN STATING THAT WALLET EMAIL BECOME POSTPAID //////
					$uniqueTemplateName	=	"TEMPLATE_SENDING_SIMPLEE_CUSTOMER_MESSAGE";
					$subject			=	$customerName." email order become postpaid";
					$emailBody			=	$customerName." email order <b>".$orderAddress."</b> which is become postpaid. This order was through wallet.";
																	
					$a_templateSubject	=	array("{emailSubject}"=>$subject);

					$toEmail			=	"john@ieimpact.net";
					//$toEmail			=	"hemant@ieimpact.net";
					//$toEmail			=	"gaurabsiva1@gmail.com";
					$a_templateData		=	array("{completeName}"=>"John Bowen","{emailBody}"=>$emailBody);

					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
				}

				////////////////////////////////////////////////////////////////////////////////////////
				//********************** SENDING CHECKLIST EMAIL IF SINGLE NO OCCUR ******************//
				/////////////////////////////////////////////////////////////////////////////////////////
				if($sendingChecklistEmail	==	true)
				{
					$a_dataNotReceived1		=	implode(",",$a_dataNotReceived);
					$a_dataNotReceived2		=	implode(", and ",$a_dataNotReceived);
					$subjectMsg				=	"Data Not Received:".$a_dataNotReceived1." for your Order#".$orderAddress;
					$a_templateSubject		=	array("{dynmaicSubject}"=>$subjectMsg);

					$addingOrderMessage		=	"We did not receive ".$a_dataNotReceived2." for this order. Please send this data ASAP so that we can start working on your order ASAP.";

					$query12				=	"INSERT INTO members_employee_messages SET orderId=$orderId,memberId=$customerId,employeeId=$s_employeeId,message='$addingOrderMessage',parentId=0,addedOn='$nowDateIndia',addedTime='$nowTimeIndia',messageBy='".EMPLOYEES."',estDate='".CURRENT_DATE_CUSTOMER_ZONE."',estTime='".CURRENT_TIME_CUSTOMER_ZONE."',isShownPopUp=0";
					dbQuery($query12);

					$lastInsertedMsgId		=	mysqli_insert_id($db_conn);

					$yesNoSPR				=	$a_choiceEmailSelected[1];
					$yesNoSMLS				=	$a_choiceEmailSelected[2];
					$yesNoSIS				=	$a_choiceEmailSelected[3];
					$yesNoRS				=	$a_choiceEmailSelected[4];
					$yesNo100MC				=	$a_choiceEmailSelected[5];
					$yesNoCPR				=	$a_choiceEmailSelected[6];
					$yesNoCMLS				=	$a_choiceEmailSelected[7];
					$yesNoCOI				=	$a_choiceEmailSelected[8];
					$yesNoLC				=	$a_choiceEmailSelected[9];
					$yesNoCTF				=	$a_choiceEmailSelected[10];

					$sentByEmployee			=	 "<b>Customer Name : </b>".$customerName." and <b>Checked by :</b> ".$s_employeeName." at ".showDateFullText(CURRENT_DATE_INDIA)." ".showTimeShortFormat(CURRENT_TIME_INDIA)." IST";

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

					$quickReplyToEmail      = "<a href='mailto:".$setThisEmailReplyToo."'>".$setThisEmailReplyToo."</a>";

					$a_templateData			=	array("{name}"=>$customerName,"{orderAddress}"=>$orderAddress,"{orderType}"=>$orderText,"{notReceived}"=>$a_dataNotReceived2,"{filesUploaded}"=>$filesUploaded,"{checklistCheckedBy}"=>"","{yesNoSPR}"=>$yesNoSPR,"{yesNoSMLS}"=>$yesNoSMLS,"{yesNoSIS}"=>$yesNoSIS,"{yesNoRS}"=>$yesNoRS,"{yesNo100MC}"=>$yesNo100MC,"{yesNoCPR}"=>$yesNoCPR,"{yesNoCMLS}"=>$yesNoCMLS,"{yesNoCOI}"=>$yesNoCOI,"{yesNoLC}"=>$yesNoLC,"{yesNoCTF}"=>$yesNoCTF,"{quickReplyToEmail}"=>$quickReplyToEmail);

					$uniqueTemplateName		=	"TEMPLATE_SENDING_CUSTOMER_ORDER_CHECKLIST";
					$toEmail				=	$customerEmail;
					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

					$setThisEmailReplyToo			=	"";//Setting for reply to make empty to manager
					$setThisEmailReplyTooName		=	"";//Setting for reply to make empty to manager

					$a_templateData		=	array("{name}"=>$customerName,"{orderAddress}"=>$orderAddress,"{orderType}"=>$orderText,"{notReceived}"=>$a_dataNotReceived2,"{filesUploaded}"=>$filesUploaded,"{checklistCheckedBy}"=>$sentByEmployee,"{yesNoSPR}"=>$yesNoSPR,"{yesNoSMLS}"=>$yesNoSMLS,"{yesNoSIS}"=>$yesNoSIS,"{yesNoRS}"=>$yesNoRS,"{yesNo100MC}"=>$yesNo100MC,"{yesNoCPR}"=>$yesNoCPR,"{yesNoCMLS}"=>$yesNoCMLS,"{yesNoCOI}"=>$yesNoCOI,"{yesNoLC}"=>$yesNoLC,"{yesNoCTF}"=>$yesNoCTF,"{quickReplyToEmail}"=>$quickReplyToEmail);

					$uniqueTemplateName	=	"TEMPLATE_SENDING_CUSTOMER_ORDER_CHECKLIST";
					$toEmail			=	"hemant@ieimpact.net";
					//$toEmail			=	"gaurabsiva1@gmail.com";
					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

					//Sending Checklist not found SMS to customer
					if($markedChecklistSendSms	   ==	1)
					{
						//if($customerId ==  	3580){
							$toPhone           =   "+".$smsCustomerMobileNo;
							$displaySmsOrderNo =   subString($orderAddress,10);
							$smsMessage		   =	"MSG from ieIMPACT : We did not receive ".$a_dataNotReceived1." for your Order - ".$orderAddress.". Please send this data ASAP, otherwise we will try to complete this order without this data.";
							$messageId 	       =  $lastInsertedMsgId;
							include(SITE_ROOT_EMPLOYEES .  "/includes/sending-sms-customer.php");
						/*}
						else{

							$displaySmsOrderNo =   subString($orderAddress,10);
							$smsMessage		   =	"MSG from ieIMPACT : We did not receive ".$a_dataNotReceived1." for your Order - ".$orderAddress.". Please send this data ASAP, otherwise we will try to complete this order without this data.";

							$smsMessage		   =	stringReplace("<br>", " ", $smsMessage);
							$smsMessage		   =	stringReplace("</ br>", " ", $smsMessage);
							$smsMessage		   =	stringReplace("</ br>", " ", $smsMessage);
							$smsReferenceID	   =    $orderId."-".rand(11,99)."-".substr(md5(microtime()+rand()+date('s')),0,5);

							$smsReturnPath	   =	"https://secure.ieimpact.com/read-sms-postback.php"; 

							$smsKey			   =	SMS_CDYNE_KEY;
							$client			   =	new SoapClient('http://sms2.cdyne.com/sms.svc?wsdl');
						
							$lk				   =	$smsKey;
							class AdvancedCallRequestData
							{
							  public $AdvancedRequest;
							 
							  function AdvancedCallRequestData($licensekey,$requests)
							  { 
										$this->AdvancedRequest = array();
										$this->AdvancedRequest['LicenseKey'] = $licensekey;
										$this->AdvancedRequest['SMSRequests'] = $requests;
							  }
							}
							 
							$PhoneNumbersArray1=    array($smsCustomerMobileNo);
											 
							$RequestArray = array(
								array(
									'AssignedDID'=>'',
														  //If you have a Dedicated Line, you would assign it here.
									'Message'=>$smsMessage,   
									'PhoneNumbers'=>$PhoneNumbersArray1,
									'ReferenceID'=>$smsReferenceID,
														  //User defined reference, set a reference and use it with other SMS functions.
									//'ScheduledDateTime'=>'2010-05-06T16:06:00Z',
														  //This must be a UTC time.  Only Necessary if you want the message to send at a later time.
									'StatusPostBackURL'=>$smsReturnPath 
														  //Your Post Back URL for responses.
								)
							);
							 
							$request		=   new AdvancedCallRequestData($smsKey,$RequestArray);
							//pr($request);
							$result			=   $client->AdvancedSMSsend($request);
							//pr($request);
							$result1		=	convertObjectToArray($result);
							//pr($result1);
							$mainResult	    =	$result1['AdvancedSMSsendResult'];
							$a_mainSmsResult=	$mainResult['SMSResponse'];
							//pr($a_mainSmsResult);
							$cancelled		=	$a_mainSmsResult['Cancelled'];
							if(empty($cancelled))
							{
								$cancelled	=	"";
							}
							$smsMessageID	=	$a_mainSmsResult['MessageID'];
							if(empty($smsMessageID))
							{
								$smsMessageID	=	"";
							}
							$smsReferenceID	=	$a_mainSmsResult['ReferenceID'];
							if(empty($smsReferenceID))
							{
								$smsReferenceID	=	"";
							}
							$queued			=	$a_mainSmsResult['Queued'];
							if(empty($queued))
							{
								$queued		=	"";
							}
							$smsError		=	$a_mainSmsResult['SMSError'];
							if(empty($smsError))
							{
								$smsError	=	"";
							}

							$smsMessage		=	addslashes($smsMessage);

							$newSmsID= $orderObj->addOrderMessageSms($cancelled,$smsReferenceID,$orderId,$customerId,$s_employeeId,$smsMessageID,$queued,$smsError,$smsMessage,$smsCustomerMobileNo);

							dbQuery("UPDATE members_employee_messages SET isFromSms=1,smsId=$newSmsID WHERE orderId=$orderId AND memberId=$customerId AND messageId=$lastInsertedMsgId");
						}*/
					}

				}

				ob_clean();
				header("Location: ".SITE_URL."/".$pageUrl."?orderId=$orderId&customerId=$customerId");
				exit();
			}
		}
	}
?>


<script type="text/javascript">
	function isValidVerify()
	{
		form1	=	document.verifyCustomerEmailOrder;

		if(form1.verifyOrderSubject.value == "" || form1.verifyOrderSubject.value == " " || form1.verifyOrderSubject.value == "0")
		{
			alert("Please enter order subject.");
			form1.verifyOrderSubject.focus();
			return false;
		
		}
		if(form1.isRushOrder.value == "" && form1.isRushOrder.value != "0")
		{
			alert("Please select order ETA.");
			form1.isRushOrder.focus();
			return false;
		}

		if(form1.orderType.value == "")
		{
			alert("Please select order type.");
			form1.orderType.focus();
			return false;
		}
		else
		{
			if(form1.orderType.value == 6)
			{
				if(form1.customersOwnOrderText.value == "" || form1.customersOwnOrderText.value == "Enter Other Type" || form1.customersOwnOrderText.value == "0")
				{
					alert("Please enter other order type.");
					form1.customersOwnOrderText.focus();
					return false;
				}
			}
		}
	
		/*if(form1.editInstructions[0].checked == true || form1.editInstructions.value == 1)
		{		
			if(form1.editedInstructions.value == "")
			{
				alert("Please enter any instructions.");
				form1.editedInstructions.focus();
				return false;
			}
		}*/

		var countTotalChecked	=	1;
		
		for(j=1;j<11;j++){
			access	=	document.getElementsByName('readFileChecklist['+j+']');
			for(i=0;i<access.length;i++)
			{
				if(access[i].checked == true)
				{
					countTotalChecked	=	countTotalChecked+1;
				}
			}
		}
		if(countTotalChecked != 11)
		{
			alert("Please complete the received data checklist.");
			return false;
		}

		if(form1.checkedCompsFiles.value == 0 || form1.checkedCompsFiles.value == "" || form1.checkedCompsFiles.value == " ")
		{
			alert("Please enter number of comps sent.");
			form1.checkedCompsFiles.focus();
			return false;
		}
		if(form1.checkEmployeeNotes.value == 0 || form1.checkEmployeeNotes.value == "" || form1.checkEmployeeNotes.value == " ")
		{
			alert("Please enter internal employee notes.");
			form1.checkEmployeeNotes.focus();
			return false;
		}
	}
	function showHideOtherOrderType(flag)
	{
		if(flag == 6)
		{
			document.getElementById('customerOtherOrderType').style.display = 'inline';
		}
		else
		{
			document.getElementById('customerOtherOrderType').style.display = 'none';
		}
	}
	function showHideInstructions(flag)
	{
		if(flag == 1)
		{
			document.getElementById('displayEditIns').style.display = 'inline';
			document.getElementById('nonDisplayEditIns').style.display = 'none';
		}
		else
		{
			document.getElementById('displayEditIns').style.display = 'none';
			document.getElementById('nonDisplayEditIns').style.display = 'inline';
		}
	}
	function delEmailOrder(orderId,customerId)
	{
		var confirmation = window.confirm("Are You Sure Delete This Order?");
		if(confirmation == true)
		{
			window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/verify-email-orders.php?orderId="+orderId+"&customerId="+customerId+"&isDelete=1";
		}
	}
	function clickCheckedForList(flag)
	{		
		if(flag	==	2)
		{
			document.getElementById('showNoText').innerHTML = "<font style='fot-family:verdana;font-size:16px;color:#ff0000;font-weight:bold;'>(A message will be sent to customer to request it)</font>";
		}
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
<form name="verifyCustomerEmailOrder" action="" method="POST" onsubmit="return isValidVerify();">
	<table width="98%" align="center" border="0" cellpadding="3" cellspacing="2">
		<tr>
			<td colspan="3" class="smalltext24">
				<b><font color='#ff0000;'>CUSTOMER ORDER INFO</font></b>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="error2">
				<b>This order was sent by customer by email. <br /><br />
				Customer provided three things: 1) Instructions in Subject, 2) Instructions Text and 3) attached Files. <br /><br />
				Look carefully all of these 3 things and fill correctly all the fields below.</b><br />
			</td>
		</tr>
		<tr>
			<td class="smalltext23" width="20%">Customer</td>
			<td class="smalltext23" width="2%">:</td>
			<td class="smalltext2">
				<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=<?php echo $customerId;?>" class="<?php echo $customerLinkStyle;?>"><?php echo ucwords($customerName);?></a>&nbsp;
			</td>
		</tr>
		<?php
			if(!empty($verifyOrderErrorMsg))
			{
		?>
		<tr>
			<td colspan="3" style="color:#ff0000;">
				<font style="font-size:18px;font-family:verdana,font-weight:bold;"><?php echo $verifyOrderErrorMsg;?></font>
			</td>
		</tr>
		<?php
			}
		?>
		<tr>
			<td class="smalltext23"><font style="color:#ff0000;">Subject<font></td>
			<td class="smalltext23">:</td>
			<td>
				<input type="text" name="verifyOrderSubject" value="<?php echo $orderAddress;?>" size="40" maxlength="100" style="border:1px solid #4d4d4d;font-family:verdana;font-size:14px;height:30px;color:#ff0000;">&nbsp;(<font color='#ff0000'><b><?php echo $state;?></b></font>)
			</td>
		</tr>
		<tr>
			<td class="smalltext23">Order Status</td>
			<td class="smalltext23">:</td>
			<td class="smalltext24">
				<b><?php echo $statusText.$newAttentionUnmarkTxt;?></b>
			</td>
		</tr>
		<tr>
			<td class="smalltext23">Order Date</td>
			<td class="smalltext23">:</td>
			<td class="smalltext24">
				<?php echo $orderAddedOn." ".$displayOrderTimeFormat." ".$displayZoneTime;?>
			</td>
		</tr>
		<tr>
			<td class="smalltext23">ETA</td>
			<td class="smalltext23">:</td>
			<td class="smalltext24">
				<?php
					if($isShortOrder		== 1){
						echo $a_estimatedTimeArray[$isRushOrder];
						echo "<input type='hidden' name='isRushOrder' value='$isRushOrder'>";
						
					}
					else{
				?>
				<select name="isRushOrder">
					<option value="">Select</option>
					<?php						
						foreach($a_estimatedTimeArray as $etaK=>$etaV)
						{
							$select		    =  "";
							if($isRushOrder	==  $etaK)
							{
								$select		=   "selected";
							}

							echo "<option value='$etaK' $select>$etaV</option>";
						}
					?>
				</select>
				<?php
					}		
				?>
			</td>
		</tr>
		<tr>
			<td class="smalltext23" valign="top">Order Type</td>
			<td class="smalltext23" valign="top">:</td>
			<td class="smalltext24" valign="top">
				<select name="orderType" onchange="showHideOtherOrderType(this.value);">
					<option value="">Select</option>
					<?php
						if($isShortOrder == 1 && $isPaidThroughWallet == "yes" && $walletAccountId != 0){
							foreach($a_addingEZOrderTypes as $typeK=>$typeV)
							{
								$select		    =  "";
								if($orderType  !=22 && $orderType	==  $typeK)
								{
									$select		=  "selected";
								}

								echo "<option value='$typeK' $select>$typeV</option>";
							}
						}
						else{
							foreach($a_addingCustomerOrderTypes as $typeK=>$typeV)
							{
								$select		    =  "";
								if($orderType  !=22 && $orderType	==  $typeK)
								{
									$select		=  "selected";
								}

								echo "<option value='$typeK' $select>$typeV</option>";
							}
						}
					?>
				</select>
				<br>
				<div id="customerOtherOrderType" style="display:<?php echo $displayOtherOrderType;?>">
					<input type="text" name="customersOwnOrderText" value="<?php echo stripslashes(htmlentities($customersOwnOrderText,ENT_QUOTES))?>" onFocus="if(this.value=='Enter Other Type') this.value='';" onBlur="if(this.value=='') this.value='Enter Other Type';"  size="40" maxlength="120" style="border:1px solid #4d4d4d;font-family:verdana;font-size:14px;height:30px;color:#4d4d4d;">
				</div>
			</td>
		</tr>
		<tr>
			<td class="smalltext23">File Type</td>
			<td class="smalltext23">:</td>
			<td class="error">
				<b><?php echo $appraisalText;?></b>
			</td>
		</tr>
		<tr>
			<td class="smalltext23">REO Form</td>
			<td class="smalltext23">:</td>
			<td class="smalltext24">
				<input type="checkbox" name="reoForm" value="1" <?php echo $checkedReoForm;?>> 
			</td>
		</tr>
		<tr>
			<td class="smalltext23">Financing</td>
			<td class="smalltext23">:</td>
			<td class="smalltext24">
				 <input type="checkbox" name="financingVa" value="1" <?php echo $checkFinancingVa;?>> VA&nbsp;
				<input type="checkbox" name="financingFha" value="1" <?php echo $checkFinancingFha;?>> FHA&nbsp;
				<input type="checkbox" name="financingHud" value="1" <?php echo $checkFinancingHud;?>> HUD&nbsp;
			</td>
		</tr>
		<tr>
			<td class="smalltext23">NON-UAD</td>
			<td class="smalltext23">:</td>
			<td class="smalltext24">
				<input type="checkbox" name="nonUad" id="1Uad" value="1" <?php echo $checkNonUad;?>/>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<?php

						if($isNewUploadingSystem	==	1)
						{
							include(SITE_ROOT_EMPLOYEES."/includes/display-multiple-files.php");
						}
						else
						{
							include(SITE_ROOT_EMPLOYEES."/includes/display-single-files.php");
							
						}
					?>
				</table>
			 </td>
		</tr>
		<tr>
			<td class="smalltext23" valign="top">Make Sketch <font class="smalltext2">(Draft Sketch Provided)</font></td>
			<td class="smalltext23">:</td>
			<td class="smalltext24">
				<input type="radio" name="providedSketch" value="2" checked>NO &nbsp;
				<input type="radio" name="providedSketch" value="1">YES 
			</td>
		</tr>
		<tr>
			<td class="smalltext23" valign="top"><font style="color:#ff0000;">Customer Instructions</font></td>
			<td class="smalltext23" valign="top">:</td>
			<td valign="top">
				<table width="100%" align="center" align="center" border="0">
					<tr>
						<td>
							<div id="nonDisplayEditIns" style='overflow:auto;width:800px;scrollbars:no'>
								<table width="100%">
									<tr>
										<td class="error">
											<?php 
												//$instructions=   preg_replace( "/\r|\n/", "", $instructions);
												echo nl2br($instructions);
											?>
										</td>
									</tr>
								</table>
							</div>
							<!--<div id="displayEditIns" style='display:none;'> 
								<textarea name="editedInstructions" class="textarea" style="width:650px;height:150px;"><?php echo stripslashes(htmlentities($instructions,ENT_QUOTES))?></textarea>
							</div>-->
						</td>
					</tr>
					<!--<tr>
						<td class="smalltext1">
							<font color="#ff0000;"><b>EDIT</b></font> <input type="radio" name="editInstructions" value="1" onclick="showHideInstructions(1);"> <b>Yes</b>&nbsp;<input type="radio" name="editInstructions" value="2" onclick="showHideInstructions(2);" checked> <b>No</b>
						</td>
					</tr>-->
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="smalltext23"><b>Received Data Checklist</b>&nbsp;[<font class="error">Select "NO" only if you want customer to send it.</font>]</td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
		<tr>
			<td colspan="3">
				<table align='left' cellpadding='0' cellspacing='0' border='0' width="98%">
					<tr>
						<td width="4%" class="heading3">&nbsp;</td>
						<td width="17%" class="heading3"><b>Checklist</b></td>
						<td align="center" class="heading3"><b>Data Received</b></td>
					</tr>
					<?php
						$countList	=	0;
						foreach($a_checklistFirstOrderCheck as $listId=>$v)
						{
							list($listName,$dbNameList)	=	explode("|",$v);
							$countList++;
					?>
					<tr>
						<td class="smalltext23" valign="top" align='left'><?php echo $countList;?>)</td>
						<td class="smalltext23" valign="top" align='left'><?php echo $listName;?></td>
						<td align="center" class="smalltext23" valign="top" align='left'>
							<input type="radio" name="readFileChecklist[<?php echo $countList;?>]" value="1|<?php echo $listId;?>" onclick="clickCheckedForList(1)">Yes
							<input type="radio" name="readFileChecklist[<?php echo $countList;?>]" value="2|<?php echo $listId;?>" onclick="clickCheckedForList(2)">No
							<input type="radio" name="readFileChecklist[<?php echo $countList;?>]" value="3|<?php echo $listId;?>" onclick="clickCheckedForList(3)">Not Required
						</td>
					</tr>
					<tr>
						<td height="3"></td>
					</tr>
					<?php
						}
					?>
					<tr>
						<td colspan="3">
							<div id="showNoText"></div>
						</td>
					</tr>
					<tr>
						<td colspan="2"><b>Number of Comps Sent :</b></td>
						<td>
							<input type="text" name="checkedCompsFiles" size="10" value="" onkeypress="return checkForNumber();" style="border:1px solid #333333;">
						</td>
					</tr>
					<tr>
						<td height="5"></td>
					</tr>
					<tr>
						<td class="smalltext2" valign="top" colspan="2"><b>Internal Employee Notes :</b></td>
						<td>
							<input type="text" name="checkEmployeeNotes" size="60" value="" onCopy="return false" onDrag="return false" onDrop="return false" onPaste="return false" autocomplete=off onKeyDown="textCounter(this.form.checkEmployeeNotes,this.form.remLentext1,100);" onKeyUp="textCounter(this.form.checkEmployeeNotes,this.form.remLentext1,100);" style="border:1px solid #333333;">
							<br><font class="smalltex1t">Characters Left: <input type="textbox" readonly name="remLentext1" size=2 value="100" style="border:0"></font>
						</td>
					</tr>
					<?php
						if(!empty($smsCustomerMobileNo))
						{
					?>
					<tr>
						<td height="6"></td>
					</tr>
					<tr>
						<td class="smalltext23" colspan="3">
							ALSO Click this box to SEND this message as SMS to customer if urgent<input type="checkbox" name="markedChecklistSendSms" value="1">
						</td>
					</tr>
					<?php
						}	
					?>
					<tr>
						<td height="5"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="2">
				<input type="image" name="submit" src="<?php echo SITE_URL_EMPLOYEES;?>/images/confirm.jpg" border="0">
				<input type='hidden' name='verifyEmailOrderSubmitted' value='1'>
				<?php
					if($s_hasManagerAccess)
					{
				?>
						&nbsp;<img src="<?php echo SITE_URL;?>/images/delete.png" height="27" width="67" border="0" onclick="delEmailOrder(<?php echo $orderId;?>,<?php echo $customerId;?>);" style="cursor:pointer;">
				<?php
					}
				?>
			</td>
		</tr>
	</table>
</form>