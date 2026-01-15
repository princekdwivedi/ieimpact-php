<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	ini_set('display_errors', '1');
	include(SITE_ROOT_EMPLOYEES .   "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES .   "/includes/check-login.php");

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
<TITLE>Change Customer Order ETA</TITLE>
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
	
	include(SITE_ROOT_EMPLOYEES .  "/classes/employee.php");
	include(SITE_ROOT			.  "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/common-array.php");
	include(SITE_ROOT_MEMBERS	.  "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	.  "/classes/orders.php");
	include(SITE_ROOT			.  "/classes/common.php");
	include(SITE_ROOT			.  "/classes/email-templates.php");
	$employeeObj				=  new employee();
	$memberObj					=  new members();
	$orderObj					=  new orders();
	$commonObj					=  new common();
	$emailObj					=  new emails();
	$a_allmanagerEmails			=  $commonObj->getMangersEmails();
	$showForm					=  false;
	$orderId					=  0;
	$customerId					=  0;
	$checkedReason				=  0;
	$errorMessageForm			=  "You are not authorized to view this page !!";
	$a_customersEmployees		=  array();
	$errorMsg					=  "";
	$expctDelvText				=  "";
	$prepaidText				=  "";
	

	if(isset($_GET['orderId']) && isset($_GET['customerId']))
	{
		$orderId				=	$_GET['orderId'];
		$customerId				=	$memberId = $_GET['customerId'];
		
		$query					=	"SELECT members_orders.*,firstName,lastName,email,secondaryEmail,stripeCustomerId FROM members_orders INNER JOIN members ON  members_orders.memberId=members.memberId WHERE orderId=$orderId AND members_orders.memberId=$customerId AND members_orders.status IN (0,1,3) AND isNotVerfidedEmailOrder=0";
		$result			=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$showForm					=	true;

			$row						=	mysqli_fetch_assoc($result);
			$orderAddress				=	stripslashes($row['orderAddress']);
			$orderType					=	$row['orderType'];
			$customersOwnOrderText		=	stripslashes($row['customersOwnOrderText']);
			$instructions				=	stripslashes($row['instructions']);
			$hasOrderFile				=	$row['hasOrderFile'];
			$hasPublicRecordFile		=	$row['hasPublicRecordFile'];
			$hasMlsFile					=	$row['hasMlsFile'];
			$hasMarketConditionFile		=	$row['hasMarketConditionFile'];
			$hasOtherFile				=	$row['hasOtherFile'];
		    $status						=	$row['status'];
			$providedSketch				=	$row['providedSketch'];
			$isRushOrder				=	$row['isRushOrder'];
			$isNewUploadingSystem		=	$row['isNewUploadingSystem'];
			$newUploadingPath			=	$row['newUploadingPath'];
			$isAlamodeOrder				=	$row['isAlamodeOrder'];
			$aLamodeCustomerID			=	$row['aLamodeCustomerID'];
			$usingAlamodeCredit			=	$row['usingAlamodeCredit'];
			$orderAddedOn				=	$row['orderAddedOn'];
			$orderAddedTime				=	$row['orderAddedTime'];
			$encryptOrderId				=	$row['encryptOrderId'];
			$isFromSingleFileUploading	=	$row['isFromSingleFileUploading'];

			$isPrepaidOrder				=	$row['isPrepaidOrder'];
			$prepaidTransactionId		=	$row['prepaidTransactionId'];
			$customerProfileId			=	$row['customerProfileId'];
			$customerShippingAddressId	=	$row['customerShippingAddressId'];
			$customerPaymentProfileId	=	$row['customerPaymentProfileId'];
			$invNo						=	$row['invNo'];
			$item_id					=	$row['item_id'];
			$isPaidThroughWallet		=	$row['isPaidThroughWallet'];
			$walletAccountId			=	$row['walletAccountId'];
			$postOrderCost				=	$row['postOrderCost'];
			$chargeId				    =	$row['chargeId'];
			$paymentGateway				=	$row['paymentGateway'];
			$isRushOrder			    =	$row['isRushOrder'];
			$status						=	$row['status'];
			$firstName					=	stripslashes($row['firstName']);
			$lastName					=	stripslashes($row['lastName']);
			$customerName          	    =   $firstName." ".substr($lastName, 0, 1);
			$email						=	$row['email'];
			$isHavingEstimatedTime		=	$row['isHavingEstimatedTime'];
			$employeeWarningDate		=	$row['employeeWarningDate'];
			$employeeWarningTime		=	$row['employeeWarningTime'];
			$prepiadPaymentThrough		=	$row['prepiadPaymentThrough'];
			$stripeCustomerId			=	$row['stripeCustomerId'];
			$txt_orderAddress			=	stripslashes($orderAddress);


			if($isPaidThroughWallet     ==  "yes" && !empty($walletAccountId))
			{
				$walletAmount			=	$employeeObj->getSingleQueryResult("SELECT amount FROM wallet_master WHERE memberId=$memberId","amount");
				if(empty($walletAmount)){
					$walletAmount		=	0;
				}
			}
			
			$expctDelvText						=	"";
			$etaHours							=	$a_estimatedTimeHours[$isRushOrder]." Hrs";
			
			if($isHavingEstimatedTime			==	1)
			{
				$expctDelvText					=	orderTAT($employeeWarningDate,$employeeWarningTime);
			}

			$statusText							=   "<font color='red'>New Order</font>";

			if($status							==	1)
			{
				$statusText						=   "<font color='#4F0000'>Accepted</font>";
				if(!empty($hasRepliedUploaded))
				{
					$statusText					=	"<font color='blue'>QA Pending</font>";
				}
				
			}
			elseif($status						==	3)
			{	
				$statusText						=   "<font color='#333333'>Nd Atten.</font>";
			}

			$lacarteOrderPrice 			        =  $employeeObj->getSingleQueryResult("SELECT SUM(unitPrice) as totalPrice FROM la_carte_orders_checkfiled WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND orderId=$orderId AND memberId=$customerId","totalPrice");
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

	if($showForm)
	{
		if(isset($_REQUEST['changeEtaFormSubmit'])){
			extract($_REQUEST);

			$changeEtaInto		=	$_POST['changeEtaInto'];
			$originalEta		=	$_POST['originalEta'];
			if($changeEtaInto	==	$originalEta){
				$errorMsg		=	"Please change ETA from existing one.<br />";
			}
			else{
				$cardErrorMsg			=	"";
				$new_transId			=	0;
				$order_amount			=	0;
				$newChargeId			=	"";
				$addingLacartePrice 	=	0;

				$is_required_check_transactions			= false;
	
				if($originalEta == 2 && $changeEtaInto  == 1)
				{
					$is_required_check_transactions		= true;
					$addingLacartePrice 	=	3;
				}
				elseif($originalEta == 2 && $changeEtaInto == 0)
				{
					$is_required_check_transactions		= true;
					$addingLacartePrice 	=	1.50;
				}
				elseif(empty($originalEta) && $changeEtaInto == 1)
				{
					$is_required_check_transactions		= true;
					$addingLacartePrice 	=	3;
				}
				
				if(!empty($lacarteOrderPrice)){
					$order_amount							=	$lacarteOrderPrice+$addingLacartePrice;
				}
				else{
					$order_amount							=	$memberObj->getMemberPostOrderPrice($memberId,$orderId,$orderType,$orderAddedOn,$changeEtaInto);
				}

				if(!empty($prepaidTransactionId) && !empty($invNo) && $isAlamodeOrder == 0 && $prepiadPaymentThrough == "creditcard" && $paymentGateway != "Stripe" && $is_required_check_transactions	==	true && !empty($order_amount))
				{					
					$invNo							=	rand(111,999).$orderId.$memberId."N";
						
					$order_descriptions				=	changeToValidString($txt_orderAddress);
					$order_amount					=	$memberObj->getMoneyExponent($order_amount);

					//if($customerId == 6){
						include_once(SITE_ROOT      .   "/classes/authorize.class.php");
						// Create an object of AuthorizeAPI class
						$objAuthorizeAPI            =   new AuthorizeAPI(AUTHORIZE_PAYMENT_LOGIN_ID, AUTHORIZE_PAYMENT_TRANSACTION_KEY, 'liveMode');
						$authResponse           =   $objAuthorizeAPI->authCC($customerProfileId, $customerPaymentProfileId, $order_amount, $invNo, "Authorize customer payment");
						$authResponse           =   @json_decode($authResponse,TRUE);

			 		    if(array_key_exists('success',$authResponse) && $authResponse['success'] == 1 && array_key_exists('paymentFlag',$authResponse) && $authResponse['paymentFlag'] == 1){
						
							$new_transId	   =	htmlspecialchars($authResponse['transId']);
							///////////////////////// VOID EXISTING TRANSACTONS /////////////////
							$arrRefundResponse = $objAuthorizeAPI->refundMoneyFromTransaction($customerProfileId,$customerPaymentProfileId, $prepaidTransactionId, $postOrderCost);

							///////////////////////////////////////////////////////////////////// 
							
						}
						else{
							
							$errorCode                  = $authResponse['errorCode'];
			
				            $errorMsg	               .=  htmlspecialchars($authResponse['message'])."&nbsp;(Error Code - ".$errorCode.")<br>";	
						}
				}
				if(!empty($chargeId) && $paymentGateway == "Stripe" && $isAlamodeOrder == 0 && $is_required_check_transactions	==	true && !empty($order_amount))
				{					
					///////////////////// CHANGE PREPAID STRIPE ORDER ETA ////////////////////////
					$query						 =	"SELECT * FROM auto_remember_stripe_account_details WHERE memberId=$memberId AND stripeCustomerId='$stripeCustomerId' AND isCurrentlyUsed=1";
					$result		=	dbQuery($query);
					if(mysqli_num_rows($result))
					{
						require_once(SITE_ROOT.'/stripe/init.php');
						\Stripe\Stripe::setApiKey(STRIPE_SECREAT_KEY);	
						
						$row					=	mysqli_fetch_assoc($result);
						$cardId					=	$row['cardId'];	
				
						$order_descriptions		=	changeToValidString($txt_orderAddress).rand(111,999);
						$chargeFor				=	"Crarge for order - ".$order_descriptions;

						$order_amount_cent		=	$order_amount*100;

						if(!empty($cardId)){
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

								$newChargeId				=	$charge['id'];
								$balance_transaction		=	$charge['balance_transaction'];

								try{
									/////////////////////////////DELETING EXISTING CHARGE ////////////////////////
									$refund = \Stripe\Refund::create(array(
									  "charge" => $chargeId
									));
								}
								catch(Exception $e){
													   
								}	

							}
							catch(Exception $e){
								$errorMsg .=   $e->getMessage();					   
							}	
						}
						else{
							$errorMsg .= "Please add your auto debit account.<br />";
						}
					}
					else{
						$errorMsg .= "Please add your auto debit account.<br />";
					}
					
				}
				if($isPaidThroughWallet			==  "yes" && !empty($walletAccountId) && !empty($order_amount))
				{
					$t_txt_orderAddress	=	makeDBSafe($txt_orderAddress);
					if($postOrderCost >= $order_amount){
						
						$transactionsMoney	=	$postOrderCost-$order_amount;
						$transactionsMoney	=	round($transactionsMoney,2);

						if(!empty($transactionsMoney) && $postOrderCost > $order_amount){
							
							$balanceWalletMoney		=	$walletAmount+$transactionsMoney;
							$balanceWalletMoney		=	round($balanceWalletMoney,2);
							
							dbQuery("UPDATE wallet_transactions SET amount='$order_amount',orderAddress='$t_txt_orderAddress',currentBalance='$balanceWalletMoney' WHERE orderId=$orderId AND transactionId=$walletAccountId");

							$query11			=	"SELECT * FROM wallet_transactions WHERE memberId=$memberId AND transactionId >= $walletAccountId ORDER BY transactionId";
							$result11			=	dbQuery($query11);
							if(mysqli_num_rows($result11)){
								while($row11			=	mysqli_fetch_assoc($result11)){
									$t_transactionId	=	$row11['transactionId'];
									$t_transactionType	=	$row11['transactionType'];
									$t_amount			=	$row11['amount'];

									$lastWalletBalance	=	$employeeObj->getSingleQueryResult("SELECT currentBalance FROM wallet_transactions WHERE memberId=$memberId AND transactionId < $t_transactionId ORDER BY transactionId DESC LIMIT 1","currentBalance");

									$current_balance	  =	$t_amount+$lastWalletBalance;
									if($t_transactionType == "debit"){
										$current_balance  =	$lastWalletBalance-$t_amount;
									}
									$current_balance	  =	round($current_balance,2);

									dbQuery("UPDATE wallet_transactions SET currentBalance='$current_balance' WHERE transactionId=$t_transactionId AND memberId=$memberId");
								}
							}

							dbQuery("UPDATE wallet_master SET amount='$balanceWalletMoney' WHERE memberId=$memberId");
						}
					}
					else{
						
						if(!empty($walletAmount)){
							
							$currentWalletMoney		=	$walletAmount+$postOrderCost;
							if($order_amount <= $currentWalletMoney){
							
								$order_amount."-".$postOrderCost;
								$transactionsMoney	=	$order_amount-$postOrderCost;
								$transactionsMoney	=	round($transactionsMoney,2);

								if(!empty($transactionsMoney)){
									
									
									$balanceWalletMoney		=	$walletAmount-$transactionsMoney;
									$balanceWalletMoney		=	round($balanceWalletMoney,2);
									
									dbQuery("UPDATE wallet_transactions SET amount='$order_amount',orderAddress='$t_txt_orderAddress',currentBalance='$balanceWalletMoney' WHERE orderId=$orderId AND transactionId=$walletAccountId");

									$query11			=	"SELECT * FROM wallet_transactions WHERE memberId=$memberId AND transactionId >= $walletAccountId ORDER BY transactionId";
									$result11			=	dbQuery($query11);
									if(mysqli_num_rows($result11)){
										while($row11	=	mysqli_fetch_assoc($result11)){
											$t_transactionId	=	$row11['transactionId'];
											$t_transactionType	=	$row11['transactionType'];
											$t_amount			=	$row11['amount'];

											$lastWalletBalance	=	$employeeObj->getSingleQueryResult("SELECT currentBalance FROM wallet_transactions WHERE memberId=$memberId AND transactionId < $t_transactionId ORDER BY transactionId DESC LIMIT 1","currentBalance");

											$current_balance	  =	$t_amount+$lastWalletBalance;
											if($t_transactionType == "debit"){
												$current_balance  =	$lastWalletBalance-$t_amount;
											}
											$current_balance	  =	round($current_balance,2);

											dbQuery("UPDATE wallet_transactions SET currentBalance='$current_balance' WHERE transactionId=$t_transactionId AND memberId=$memberId");
										}
									}

									dbQuery("UPDATE wallet_master SET amount='$balanceWalletMoney' WHERE memberId=$memberId");
								}
							}
							else{
								
								$errorMsg	.=	"Sorry customer don't have required balance to upgrade your order ETA.";
							}
						}
						else{
							
							$errorMsg		.=	"Sorry customer don't have required balance to upgrade your order ETA.";
						}
					}
				}

				if(empty($errorMsg)){		

					
					if($changeEtaInto		==	0)
					{
						$addingRequiredHrs	=	STANDRAD_ORDER_COMPLETE_TIME_HOURS;
					}
					elseif($changeEtaInto	==	2)
					{
						$addingRequiredHrs	=	ECONOMY_ORDER_COMPLETE_TIME_HOURS;
					}
					else
					{
						$addingRequiredHrs	=	RUSH_ORDER_COMPLETE_TIME_HOURS;
					}
					$warningDateTimeEmployee=	getNextCalculatedHours(CURRENT_DATE_INDIA,CURRENT_TIME_INDIA,$addingRequiredHrs);

					list($warningOrderDate,$warningOrderTime)	=	explode("=",$warningDateTimeEmployee);

					$query					=	"UPDATE members_orders SET orderEditedDate='$nowDateIndia',orderEditedTime='$nowTimeIndia',orderEditedEstDate='".CURRENT_DATE_CUSTOMER_ZONE."',orderEditedEstTime='".CURRENT_TIME_CUSTOMER_ZONE."',orderEditedFromIp='".VISITOR_IP_ADDRESS."',isRushOrder=$changeEtaInto,isHavingEstimatedTime=1,employeeWarningDate='$warningOrderDate',employeeWarningTime='$warningOrderTime',editedOrderByEmployee=$s_employeeId WHERE orderId=$orderId AND memberId=$memberId";
					dbQuery($query);

					////////////////////////////////////////////////////////////////////////////
					//////////////////// PUTTING THE ORDER IN ORDER TRACK LIST /////////////////
				     $orderObj->addOrderTracker($s_employeeId,$orderId,$orderAddress,'Employee changed order ETA','EMPLOYEE_CHANGED_ORDER_ETA');
				    ////////////////////////////////////////////////////////////////////////////
				    ////////////////////////////////////////////////////////////////////////////

					if(!empty($prepaidTransactionId) && !empty($new_transId) && $prepaidTransactionId != $new_transId && !empty($order_amount))
					{
						dbQuery("UPDATE members_orders SET prepaidTransactionId=$new_transId,invNo='$invNo',prepaidOrderPrice='$order_amount' WHERE orderId=$orderId");
					}

					if(!empty($chargeId) && !empty($newChargeId) && $paymentGateway == "Stripe")
					{
						dbQuery("UPDATE members_orders SET chargeId='$newChargeId',prepaidOrderPrice='$order_amount' WHERE orderId=$orderId");
					}

					if(!empty($lacarteOrderPrice)){
						//////////////////////////UPDATE LA CARTE ORDER PRICE ////////////
						dbQuery("UPDATE members_orders SET postOrderCost='$order_amount' WHERE orderId=$orderId AND memberId=$memberId");
					}
					else
					{
						//////////////////////////UPDATE NORMAL ORDER PRICE ////////////
						$memberObj->setMemberPostOrderPrice($memberId,$orderId,$orderType,$orderAddedOn);
					}	
				}
			}
			if(empty($errorMsg)){
				echo "<table width='95%' align='center' border='0' height='70'><tr><td align='center'><font style='font-family:verdana;font-size:17px;color:#333333;'>Successfully updated ETA.</font></td></tr></table>";

				echo "<script type='text/javascript'>reflectChange();</script>";		
				echo "<script>setTimeout('window.close()',10)</script>";
			}
			
		}
?>
<table width="100%" align="center" border="0" cellspacing="3" cellspacing="3">
	<tr>
		<td colspan="3" class="textstyle1"><b>Change Customer Order ETA</b></td>
	</tr>
	<tr>
		<td width="20%" class="smalltext22">
			Customer Name
		</td>
		<td width="2%" class="smalltext22">
			:
		</td>
		<td class="smalltext23">
			<?php echo $customerName;?>
		</td>
	</tr>
	<tr>
		<td class="smalltext22">
			Order Address
		</td>
		<td class="smalltext22">
			:
		</td>
		<td class="smalltext23">
			<?php echo $orderAddress;?>
		</td>
	</tr>
	<tr>
		<td class="smalltext22">
			Order Status
		</td>
		<td class="smalltext22">
			:
		</td>
		<td class="smalltext23">
			<?php echo $statusText;?>
		</td>
	</tr>
	<tr>
		<td class="smalltext22">
			Order Added On
		</td>
		<td class="smalltext22">
			:
		</td>
		<td class="smalltext23">
			<?php echo showDate($orderAddedOn)."/".showTimeShortFormat($orderAddedTime)." IST";?>
		</td>
	</tr>
	<tr>
		<td class="smalltext22">
			ETA (Hours)
		</td>
		<td class="smalltext22">
			:
		</td>
		<td class="smalltext23">
			<?php echo $etaHours;?>
		</td>
	</tr>
	<tr>
		<td class="smalltext22">
			Estimated ETA
		</td>
		<td class="smalltext22">
			:
		</td>
		<td class="smalltext23">
			<?php echo $expctDelvText;?>
		</td>
	</tr>	
</table>
<script type="text/javascript">
	function isValidEta(){
		form1	=	document.changeEtaByEmp;
		if(form1.changeEtaInto.value == form1.originalEta.value){
			alert("Please change ETA from existing one.");
			return false;
		}
	}
</script>
<form name="changeEtaByEmp" action="" method="POST" onsubmit="return isValidEta();">
	<table width="100%" align="center" border="0" cellspacing="3" cellspacing="3">
		<?php
			if(!empty($errorMsg)){
				echo "<tr><td colspan='2'>&nbsp;</td><td class='error2'>".$errorMsg."</td></tr>";
			}
		?>
		<tr>
			<td width="20%" class="smalltext22">
				Change ETA Into
			</td>
			<td width="2%" class="smalltext22">
				:
			</td>
			<td class="smalltext23">
				<?php
					foreach($a_estimatedTimeHours as $kk=>$vv){
						$checked	=	"";
						if($kk      ==  $isRushOrder){
							$checked=	"checked";
						}
						echo "<input type='radio' name='changeEtaInto' value='$kk' $checked>".$vv." Hrs";
					}
				?>
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td>
				<input type="image" name="submit" src="<?php echo SITE_URL;?>/images/submit.jpg" border="0" style="cursor:pointer;">
				<input type='hidden' name='changeEtaFormSubmit' value='1'>
				<input type='hidden' name='originalEta' value='<?php echo $isRushOrder;?>'>
			</td>
		</tr>
	</table>
</form>
<?php
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

	