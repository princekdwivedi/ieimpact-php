<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	ini_set('display_errors', '1');
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");

	$topDisplayDiv	=	"";
	if($s_employeeId && !isset($isNotDisplayLoadingDiv))
	{
		require_once (SITE_ROOT . '/classes/loading-div.php');
		$divLoader = new loadingDiv;
		$divLoader->loader($topDisplayDiv);
	}
?>
<html>
<head>
<TITLE>Delete Customer Order</TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
<link rel="shortcut icon" href="/new-favicon.ico" type="image/x-icon" />
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
	
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/common.php");
	include(SITE_ROOT			. "/classes/email-templates.php");
	$employeeObj				= new employee();
	$memberObj					= new members();
	$orderObj					= new orders();
	$commonObj					= new common();
	$emailObj					= new emails();
	$a_allmanagerEmails			= $commonObj->getMangersEmails();
	$showForm					= false;
	$orderId					= 0;
	$customerId					= 0;
	$status						= 0;
	$employeeId					= 0;
	$orderStatus				= 0;
	$checkedReason				=	0;
	$errorMessageForm			= "You are not authorized to view this page !!";
	$a_customersEmployees		=  array();
	$implode_customersEmployees	=  0;
	$errorMsg					=  "";
	$expctDelvText				=  "";
	$hideOtherReason			=  "none";
	$lastTwoDaysOld				=	getPreviousGivenDate($nowDateIndia,1); 
	$prepaidText				=	"";
	

	if(isset($_GET['orderId']) && isset($_GET['customerId']))
	{
		$orderId				=	$_GET['orderId'];
		$customerId				=	$_GET['customerId'];
		$status					=	$orderObj->getOrderStatus($orderId,$customerId);
		if($status	==	2 || $status	==	4 || $status	==	5)
		{
			$errorMessageForm   =  "This order is already completed.";
		}
		else
		{
			$query			=	"SELECT members_orders.*,firstName,lastName,completeName,email,secondaryEmail FROM members_orders INNER JOIN members ON  members_orders.memberId=members.memberId WHERE orderId=$orderId AND members_orders.memberId=$customerId AND members_orders.status IN (0,1,3,6)";
			$result			=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$showForm							=	true;

				$row								=	mysqli_fetch_assoc($result);
				$orderAddress						=	stripslashes($row['orderAddress']);
				$orderType							=	$row['orderType'];
				$hasOrderFile						=	$row['hasOrderFile'];
				$orderAddedOn						=	$row['orderAddedOn'];
				$orderAddedTime						=	$row['orderAddedTime'];
				$hasPublicRecordFile				=	$row['hasPublicRecordFile'];
				$hasMlsFile							=	$row['hasMlsFile'];
				$hasMarketConditionFile				=	$row['hasMarketConditionFile'];
				$firstName				            =	stripslashes($row['firstName']);
				$lastName				            =	stripslashes($row['lastName']);
				$completeName						=	$firstName." ".substr($lastName, 0, 1);
				$customerName                       =   stripslashes($row['completeName']);
				$customerEmail						=	$row['email'];
				$orderTypeText						=	$a_customerOrder[$orderType];
				$headerText							=	"DELETE ORDER - ".$orderAddress;
				$isNewUploadingSystem				=	$row['isNewUploadingSystem'];
				$newUploadingPath					=	$row['newUploadingPath'];
				$isPrepaidOrder						=	$row['isPrepaidOrder'];
				$usedDebitAccountId					=	$row['usedDebitAccountId'];
				$customerProfileId					=	$row['customerProfileId'];
				$customerShippingAddressId			=	$row['customerShippingAddressId'];
				$customerPaymentProfileId			=	$row['customerPaymentProfileId'];
				$invNo								=	$row['invNo'];
				$item_id							=	$row['item_id'];
				$advancedPaymentId					=	$row['advancedPaymentId'];
				$estDate							=	$row['estDate'];
				$estTime							=	$row['estTime'];
				$prepaidOrderPrice					=	$row['prepaidOrderPrice'];
				$prepaidTransactionId				=	$row['prepaidTransactionId'];
				$prepiadPaymentThrough				=	$row['prepiadPaymentThrough'];
				$isPaidThroughWallet				=	$row['isPaidThroughWallet'];
				$walletAccountId					=	$row['walletAccountId'];
				$postOrderCost						=	$row['postOrderCost'];
				$chargeId							=	$row['chargeId'];
				$paymentGateway						=	$row['paymentGateway'];
				$isHavingEstimatedTime				=	$row['isHavingEstimatedTime'];
				$employeeWarningDate				=	$row['employeeWarningDate'];
				$employeeWarningTime				=	$row['employeeWarningTime'];
				$isOrderChecked						=	$row['isOrderChecked'];
				
				if($isHavingEstimatedTime			==	1)
				{
					$expctDelvText					=	orderTAT($employeeWarningDate,$employeeWarningTime);
				}
			}
		}
	}
	else
	{
		$showForm					= false;
	}

	if(empty($s_hasManagerAccess))
	{
		$showForm					= false;
	}
	$form							=	SITE_ROOT_EMPLOYEES."/forms/add-delete-order-notes.php";
	$a_deletingOrderReason			=	array("1"=>"Late ETA (will miss deadline)","2"=>"Uploading Again","3"=>"Client Canceled","4"=>"Duplicate Order","5"=>"Other");	

	if($showForm)
	{
?>
<table width="100%" align="center" border="0" cellspacing="3" cellspacing="3">
	<tr>
		<td colspan="3" class="textstyle1"><b>Delete Customer Order</b></td>
	</tr>
	<tr>
		<td width="20%" class="title1">
			Customer Name
		</td>
		<td width="2%" class="title1">
			:
		</td>
		<td class="title">
			<?php echo $completeName;?>
		</td>
	</tr>
	<tr>
		<td class="title1">
			Order Address
		</td>
		<td class="title1">
			:
		</td>
		<td class="title">
			<?php echo $orderAddress;?>
		</td>
	</tr>
	<tr>
		<td class="title1">
			Order Added On
		</td>
		<td class="title1">
			:
		</td>
		<td class="title">
			<?php echo showDate($orderAddedOn)."/".showTimeShortFormat($orderAddedTime)." IST";?>
		</td>
	</tr>
	<tr>
		<td class="title1">
			TAT
		</td>
		<td class="title1">
			:
		</td>
		<td class="title">
			<?php echo $expctDelvText;?>
		</td>
	</tr>
	
</table>
<?php
		if(isset($_REQUEST['formSubmitted']))
		{
			extract($_REQUEST);
			//pr($_REQUEST);
			//die();
			if(isset($_POST['checkedReason']))
			{
				$checkedReason		=	$_POST['checkedReason'];
				if($checkedReason	==	5)
				{
					$hideOtherReason=	"";
					if(empty($deleteOrderNotes))
					{
						$errorMsg	   .=	"Please enter other reason.";
					}
					else
					{
						$deleteOrderNotes	=	trim($deleteOrderNotes);
					}
				}
				else
				{
					$deleteOrderNotes		=	$a_deletingOrderReason[$checkedReason];
				}
			}
			else
			{
				$errorMsg	   .=	"Please select one reason for deleting.";
			}
			if(empty($errorMsg))
			{
				////////////// DELETE CUSTOMER ORDER //////////////////////
				$memberObj->deleteMemberOrderFolder($orderId,$customerId);
				///////////////////////////////////////////////////////////
				
								
				$t_deleteOrderNotes		=	makeDBSafe($deleteOrderNotes);
				$deletedBy				=	"Employee - ".$s_employeeId;

				dbQuery("INSERT INTO delete_order_reason SET orderId=$orderId,orderDate='$orderAddedOn',deletedOn='".CURRENT_DATE_INDIA."',memberId=$customerId,deletedBy='$deletedBy',deletedTime='".CURRENT_TIME_INDIA."',deletedIp='".VISITOR_IP_ADDRESS."',deleteOrderNotes='$t_deleteOrderNotes'");

				if($isPaidThroughWallet == "yes" && !empty($walletAccountId)){
					$deleteOrderCost	=	$postOrderCost;
					if(empty($postOrderCost) && !empty($prepaidOrderPrice)){
						$deleteOrderCost=	$prepaidOrderPrice;
					}
					
					$walletAmount		=	$employeeObj->getSingleQueryResult("SELECT amount FROM wallet_master WHERE memberId=$customerId","amount");
					if(empty($walletAmount)){
						$walletAmount	=	0;
					}

					$referenceNumber		= $walletAccountId;
					if(strlen($referenceNumber) < 4){
						$referenceNumber	=	"1010".$referenceNumber;
					}
					$currentBalance			=	$walletAmount+$deleteOrderCost;
					$currentBalance			=	round($currentBalance,2);


					$t_orderAddress			=	makeDBSafe($orderAddress);


					dbQuery("INSERT INTO wallet_transactions SET memberId=$customerId,amount='$deleteOrderCost',transactionType='credit',creditType='deletedorders',proceedingDate='".CURRENT_DATE_INDIA."',proceedingTime='".CURRENT_TIME_INDIA."',estProceedingDate='".CURRENT_DATE_CUSTOMER_ZONE."',estProceedingTime='".CURRENT_TIME_CUSTOMER_ZONE."',status='success',paymentThrough='revertdeletedorders',orderId=$orderId,referenceNumber='$referenceNumber',orderAddress='$t_orderAddress',currentBalance='$currentBalance'");

					dbQuery("UPDATE wallet_master SET amount='$currentBalance' WHERE memberId=$customerId");
				}
				else{
					if(!empty($chargeId) && $paymentGateway == "Stripe"){
						////////////////////////REFUND STRIPE PAYMENT /////
						require_once(SITE_ROOT.'/stripe/init.php');
						\Stripe\Stripe::setApiKey(STRIPE_SECREAT_KEY);

						try{
							///////////////////////////// CHARGING CARD ACCOUNT ////////////////////////
							$refund = \Stripe\Refund::create(array(
							  "charge" => $chargeId
							));

							$refundToken = $result['id'];

							$prepaidText					=	"We have issued a refund of $".$memberObj->getMoneyExponent($prepaidOrderPrice)." back to the same payment account you used.";

							$managerEmployeeEmailSubject	=	"A Prepaid Order With $ ".$prepaidOrderPrice." is deleted";
							$managerEmployeeFromName		=	"Deleted Prepaid Order";

							$table					=	"<table width='98%' align='center' cellpadding='3' cellspacing='3'>
								<tr>
									<td colspan='3'>
										Dear Admin,<br /><br />
										Customer ".$customerName." has deleted his/her prepaid order of $ ".$prepaidOrderPrice." payment made through <b>Creditcard (Stripe)</b>.<br />Here is the details of the order :<br /><br />
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
									<td>".$orderTypeText."</td>
								</tr>
								<tr>
									<td>Order Date</td>
									<td>:</td>
									<td>".showDate($orderAddedOn)." IST & ".showDate($estDate)." EST</td>
								</tr>
							</table>";

							$a_templateData			=	array("{bodyMatter}"=>$table);

							$toEmail				=	"hemant@ieimpact.net"; 
							$uniqueTemplateName		=	"TEMPLATE_SENDING_NEW_SIMPLEE_MESSAGE";
							include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

							$managerEmployeeEmailSubject	=	"";
							$managerEmployeeFromName		=	"";


						}
						catch(Exception $e){
							$refundErrorMsg   =   $e->getMessage();	
							
							$prepaidText					=	 "We will issue a refund of $".$memberObj->getMoneyExponent($prepaidOrderPrice)." back to the same payment account you used.";

							$managerEmployeeEmailSubject	=	"Manual Action needed for this refund with $ ".$prepaidOrderPrice." is deleted";

							$managerEmployeeFromName		=	"Deleted Prepaid Order";

							$table					=	"<table width='98%' align='center' cellpadding='3' cellspacing='3'>
								<tr>
									<td colspan='3'>
										Dear Admin,<br /><br />
										Customer ".$customerName." has deleted his/her prepaid order of $ ".$prepaidOrderPrice." payment made through <b>Creditcard (Stripe)</b>.<br />Here is the details of the order :<br /><br />
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
									<td>".$orderTypeText."</td>
								</tr>
								<tr>
									<td>Order Date</td>
									<td>:</td>
									<td>".showDate($orderAddedOn)." IST & ".showDate($estDate)." EST</td>
								</tr>
								<tr>
									<td>Error Details</td>
									<td>:</td>
									<td>Error Code : ".$refundErrorMsg."</td>
								</tr>
								<tr>
									<td>Transaction Through</td>
									<td>:</td>
									<td>Stripe</td>
								</tr>
								<tr>
									<td>Payment ID</td>
									<td>:</td>
									<td>".$chargeId."</td>
								</tr>
								<tr>
									<td>Stripe Customer ID</td>
									<td>:</td>
									<td>".$stripeCustomerId."</td>
								</tr>
							</table>";

							$a_templateData			=	array("{bodyMatter}"=>$table);

							$toEmail				=	"hemant@ieimpact.net"; 
							$uniqueTemplateName		=	"TEMPLATE_SENDING_NEW_SIMPLEE_MESSAGE";
							include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

							$managerEmployeeEmailSubject	=	"";
							$managerEmployeeFromName		=	"";
						}	
					}

					
					if($isPrepaidOrder	==	0 && $prepiadPaymentThrough != "paypal" && $prepaidTransactionId != "0" && $advancedPaymentId == 0)
					{
						
						try{

						include_once(SITE_ROOT      .   "/classes/authorize.class.php");
						// Create an object of AuthorizeAPI class
						$objAuthorizeAPI            =   new AuthorizeAPI(AUTHORIZE_PAYMENT_LOGIN_ID, AUTHORIZE_PAYMENT_TRANSACTION_KEY, 'liveMode');

						$arrRefundResponse = $objAuthorizeAPI->refundMoneyFromTransaction($customerProfileId,$customerPaymentProfileId, $prepaidTransactionId, $postOrderCost);


						$arrRefundResponse  =   @json_decode($arrRefundResponse,TRUE);

						$addedText		=	$s_employeeName." deleting ".$customerName." order - ".$orderAddress;
						$commonClass	= $commonObj->trackAuthorizeResponse($arrRefundResponse,'Authorize.net',$orderId,$customerId,$addedText);

						//Updating advanced table records
						$memberObj->removePrepaidAdvancedPayment($customerId,$advancedPaymentId,$prepaidOrderPrice,$orderId,'creditcard');

						$prepaidText					=	"We have issued a refund of $".$memberObj->getMoneyExponent($prepaidOrderPrice)." back to the same payment account you used.";

						$managerEmployeeEmailSubject	=	"A Prepaid Order With $ ".$prepaidOrderPrice." is deleted";
						$managerEmployeeFromName		=	"Deleted Prepaid Order";

						$table					=	"<table width='98%' align='center' cellpadding='3' cellspacing='3'>
							<tr>
								<td colspan='3'>
									Dear Admin,<br /><br />
									Employee ".$s_employeeName." has deleted customer".$customerName." prepaid order of $ ".$prepaidOrderPrice." payment made through <b>Creditcard</b>.<br />Here is the details of the order :<br /><br />
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
								<td>".$orderTypeText."</td>
							</tr>
							<tr>
								<td>Order Date</td>
								<td>:</td>
								<td>".showDate($orderAddedOn)." IST & ".showDate($estDate)." EST</td>
							</tr>
						</table>";

						$a_templateData			=	array("{bodyMatter}"=>$table);

						$toEmail				=	"hemant@ieimpact.net"; 
						$uniqueTemplateName		=	"TEMPLATE_SENDING_NEW_SIMPLEE_MESSAGE";
						include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

						$managerEmployeeEmailSubject	=	"";
						$managerEmployeeFromName		=	"";
						
						}
						catch(Exception $e){
							$refundErrorMsg   =   $e->getMessage();	
							
							$prepaidText					=	 "We will issue a refund of $".$memberObj->getMoneyExponent($prepaidOrderPrice)." back to the same payment account you used.";

							$managerEmployeeEmailSubject	=	"Manual Action needed for this refund with $ ".$prepaidOrderPrice." is deleted";

							$managerEmployeeFromName		=	"Deleted Prepaid Order";

							$table					=	"<table width='98%' align='center' cellpadding='3' cellspacing='3'>
								<tr>
									<td colspan='3'>
										Dear Admin,<br /><br />
										Customer ".$customerName." has deleted his/her prepaid order of $ ".$prepaidOrderPrice." payment made through <b>Creditcard (Stripe)</b>.<br />Here is the details of the order :<br /><br />
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
									<td>".$orderTypeText."</td>
								</tr>
								<tr>
									<td>Order Date</td>
									<td>:</td>
									<td>".showDate($orderAddedOn)." IST & ".showDate($estDate)." EST</td>
								</tr>
								<tr>
									<td>Error Details</td>
									<td>:</td>
									<td>Error Code : ".$refundErrorMsg."</td>
								</tr>
								<tr>
									<td>Transaction Through</td>
									<td>:</td>
									<td>Stripe</td>
								</tr>
								<tr>
									<td>Payment ID</td>
									<td>:</td>
									<td>".$chargeId."</td>
								</tr>
								<tr>
									<td>Stripe Customer ID</td>
									<td>:</td>
									<td>".$stripeCustomerId."</td>
								</tr>
							</table>";

							$a_templateData			=	array("{bodyMatter}"=>$table);

							$toEmail				=	"hemant@ieimpact.net"; 
							$uniqueTemplateName		=	"TEMPLATE_SENDING_NEW_SIMPLEE_MESSAGE";
							include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

							$managerEmployeeEmailSubject	=	"";
							$managerEmployeeFromName		=	"";
						}
					}


					if($isPrepaidOrder		==	1 && $prepiadPaymentThrough != "paypal")
					{
						try{
							include_once(SITE_ROOT      .   "/classes/authorize.class.php");
							// Create an object of AuthorizeAPI class
							$objAuthorizeAPI            =   new AuthorizeAPI(AUTHORIZE_PAYMENT_LOGIN_ID, AUTHORIZE_PAYMENT_TRANSACTION_KEY, 'liveMode');

							$arrRefundResponse = $objAuthorizeAPI->refundMoneyFromTransaction($customerProfileId,$customerPaymentProfileId, $prepaidTransactionId, $postOrderCost);


							$arrRefundResponse  =   @json_decode($arrRefundResponse,TRUE);

							$addedText		=	$s_employeeName." deleting ".$customerName." order - ".$orderAddress;
							$commonClass	= $commonObj->trackAuthorizeResponse($arrRefundResponse,'Authorize.net',$orderId,$customerId,$addedText);
						
								//Updating advanced table records
								$memberObj->removePrepaidAdvancedPayment($customerId,$advancedPaymentId,$prepaidOrderPrice,$orderId,'creditcard');

								$prepaidText					=	"We have issued a refund of $".$memberObj->getMoneyExponent($prepaidOrderPrice)." back to the same payment account you used.";

								$managerEmployeeEmailSubject	=	"A Prepaid Order With $ ".$prepaidOrderPrice." is deleted";
								$managerEmployeeFromName		=	"Deleted Prepaid Order";

								$table					=	"<table width='98%' align='center' cellpadding='3' cellspacing='3'>
								<tr>
									<td colspan='3'>
										Dear Admin,<br /><br />
										Employee ".$s_employeeName." deleting ".$customerName."prepaid order of $ ".$prepaidOrderPrice." payment made through <b>Creditcard</b>.<br />Here is the details of the order :<br /><br />
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
									<td>".$orderTypeText."</td>
								</tr>
								<tr>
									<td>Order Date</td>
									<td>:</td>
									<td>".showDate($orderAddedOn)." IST & ".showDate($estDate)." EST</td>
								</tr>
							</table>";

							$a_templateData			=	array("{bodyMatter}"=>$table);

							$toEmail				=	"hemant@ieimpact.net"; 
							$uniqueTemplateName		=	"TEMPLATE_SENDING_NEW_SIMPLEE_MESSAGE";
							include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

							$managerEmployeeEmailSubject	=	"";
							$managerEmployeeFromName		=	"";					
						}
						catch(Exception $e){
							$refundErrorMsg   =   $e->getMessage();	
							
							$prepaidText					=	 "We will issue a refund of $".$memberObj->getMoneyExponent($prepaidOrderPrice)." back to the same payment account you used.";

							$managerEmployeeEmailSubject	=	"Manual Action needed for this refund with $ ".$prepaidOrderPrice." is deleted";

							$managerEmployeeFromName		=	"Deleted Prepaid Order";

							$table					=	"<table width='98%' align='center' cellpadding='3' cellspacing='3'>
								<tr>
									<td colspan='3'>
										Dear Admin,<br /><br />
										Employee ".$s_employeeName." deleting ".$customerName." prepaid order of $ ".$prepaidOrderPrice." payment made through <b>Creditcard (Stripe)</b>.<br />Here is the details of the order :<br /><br />
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
									<td>".$orderTypeText."</td>
								</tr>
								<tr>
									<td>Order Date</td>
									<td>:</td>
									<td>".showDate($orderAddedOn)." IST & ".showDate($estDate)." EST</td>
								</tr>
								<tr>
									<td>Error Details</td>
									<td>:</td>
									<td>Error Code : ".$refundErrorMsg."</td>
								</tr>
								<tr>
									<td>Transaction Through</td>
									<td>:</td>
									<td>Stripe</td>
								</tr>
								<tr>
									<td>Payment ID</td>
									<td>:</td>
									<td>".$chargeId."</td>
								</tr>
								<tr>
									<td>Stripe Customer ID</td>
									<td>:</td>
									<td>".$stripeCustomerId."</td>
								</tr>
							</table>";

							$a_templateData			=	array("{bodyMatter}"=>$table);

							$toEmail				=	"hemant@ieimpact.net"; 
							$uniqueTemplateName		=	"TEMPLATE_SENDING_NEW_SIMPLEE_MESSAGE";
							include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

							$managerEmployeeEmailSubject	=	"";
							$managerEmployeeFromName		=	"";
						}
					}
					elseif($isPrepaidOrder	==	1 && $prepiadPaymentThrough == "paypal")
					{
						//Updating advanced table records
						$memberObj->removePrepaidAdvancedPayment($customerId,$advancedPaymentId,$prepaidOrderPrice,$orderId,'paypal');

						$paypalTransactionId= $employeeObj->getSingleQueryResult("SELECT txnId FROM master_paypal_transactions WHERE referenceId=$prepaidTransactionId","txnId");

						$managerEmployeeEmailSubject	=	"Manual Refund Required: $ ".$memberObj->getMoneyExponent($prepaidOrderPrice)." - ".$s_memberName.", ".$orderAddress;
						$managerEmployeeFromName		=	"Deleted Prepaid Order";

						$table					=	"<table width='98%' align='center' cellpadding='3' cellspacing='3'>
							<tr>
								<td colspan='3'>
									Dear Admin,<br /><br />
									Employee ".$s_employeeName." deleting ".$customerName." prepaid order of $ ".$prepaidOrderPrice." payment made through <b>Paypal</b>.<br />Here is the details of the order :<br /><br />
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
								<td>".$orderTypeText."</td>
							</tr>
							<tr>
								<td>Transaction ID</td>
								<td>:</td>
								<td><a href='https://www.paypal.com/us/cgi-bin/webscr?cmd=_view-a-trans&id=$paypalTransactionId' target='_blank'><u>".$paypalTransactionId."</u></a></td>
							</tr>
							<tr>
								<td>Order Date</td>
								<td>:</td>
								<td>".showDate($orderAddedOn)." IST & ".showDate($estDate)." EST</td>
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
				}

				$a_templateSubject				=	array("{orderNo}"=>$orderAddress);
				$managerEmployeeEmailSubject	=	"Order canceled : ".$orderAddress;

				$a_templateData			=	array("{name}"=>$customerName,"{orderNo}"=>$orderAddress,"{orderType}"=>$orderTypeText,"{cutomerName}"=>"We","{deleteOrderNotes}"=>$deleteOrderNotes,"{prepaidText}"=>$prepaidText);

				$toEmail				=	$customerEmail; 
				$uniqueTemplateName		=	"TEMPLATE_SENDING_DELETE_CUSTOMER_ORDER_WITH_REASON";
				include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

				
				if(!empty($a_allmanagerEmails))
				{
					//$a_allmanagerEmails	  =	stringReplace(',john@ieimpact.net','',$a_allmanagerEmails);
					$a_allmanagerEmails		  =  array("hemant@ieimpact.net");
					
					$a_templateData		  =	array("{name}"=>"Manager","{orderNo}"=>$orderAddress,"{orderType}"=>$orderTypeText,"{cutomerName}"=>$customerName,"{deleteOrderNotes}"=>$deleteOrderNotes,"{prepaidText}"=>$prepaidText);

					$managerEmployeeEmailSubject	=	"Order canceled : ".getSubstring($deleteOrderNotes,40).": ".$orderAddress." by ".$customerName;


					$uniqueTemplateName		=	"TEMPLATE_SENDING_DELETE_CUSTOMER_ORDER_WITH_REASON";
					$toEmail				=	DEFAULT_BCC_EMAIL;
					$managerEmployeeFromBcc=	$a_allmanagerEmails;
					
					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");				
				}

				echo "<table width='95%' align='center' border='0' height='70'><tr><td align='center'><font style='font-family:verdana;font-size:17px;color:#333333;'>Successfully deleted your order.</font></td></tr></table>";

				echo "<script type='text/javascript'>reflectChange();</script>";		
				echo "<script>setTimeout('window.close()',10)</script>";
			}
			else
			{
				include($form);
			}
		}
		else
		{
			include($form);
		}
		
	}
	else
	{
		echo "<table width='90%' align='center' border='1' height='100'><tr><td align='center' align='center' class='error'><b>$errorMessageForm</b></td></tr></table>";
	}
?>
	<br><br>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
</center>
</body>
</html>

	