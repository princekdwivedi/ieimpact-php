<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES .  "/includes/new-top.php");
	include(SITE_ROOT_EMPLOYEES .  "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES .  "/classes/employee.php");
	include(SITE_ROOT			.  "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/check-pdf-login.php");
	include(SITE_ROOT_MEMBERS	.  "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	.  "/classes/orders.php");
	include(SITE_ROOT			.  "/classes/email-track-reading.php");
	include(SITE_ROOT			.  "/classes/pagingclass.php");

	$employeeObj				=  new employee();
	$memberObj					=  new members();
	$orderObj					=  new orders();
	$emailTrackObj				=  new trackReading();
	$pagingObj					=  new Paging();

	$formSearch					=	SITE_ROOT_EMPLOYEES."/forms/search-general-order-form.php";
	
	if(isset($_REQUEST['recNo']))
	{
		$recNo					=	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo	=	0;
	}
	$whereClause				=	"WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND isEmailOrder=1 AND isNotVerfidedEmailOrder=1 AND isDeleted=0 AND isVirtualDeleted=0";
	$orderBy					=	"orderId";
	$queryString				=	"";
	$andCaluse					=	"";
	$andClause1					=	"";

	if(isset($_GET['orderId']) && isset($_GET['customerId']) && isset($_GET['isDelete']) && $_GET['isDelete'] == 1 && !empty($s_hasManagerAccess))
	{
		$orderId				=	(int)$_GET['orderId'];
		$memberId				=	(int)$_GET['customerId'];

		if(!empty($orderId) && !empty($memberId))
		{

			$query				=	"SELECT orderAddress,orderType,customersOwnOrderText,orderAddedOn,prepaidTransactionId,customerProfileId,customerShippingAddressId,customerPaymentProfileId,chargeId,usingStripeAccountId,paymentGateway,isPaidThroughWallet,walletAccountId,prepiadPaymentThrough,prepaidOrderPrice,postOrderCost FROM members_orders WHERE orderId=$orderId AND memberId=$memberId AND isEmailOrder=1 AND isNotVerfidedEmailOrder=1";
			$result				=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row						=	mysqli_fetch_assoc($result);
				$orderAddress				=	stripslashes($row['orderAddress']);
				$orderType					=	$row['orderType'];
				$customersOwnOrderText		=	stripslashes($row['customersOwnOrderText']);
				$orderPlacedDate			=	$row['orderAddedOn'];
				$customerProfileId			=	$row['customerProfileId'];
				$customerShippingAddressId	=	$row['customerShippingAddressId'];
				$customerPaymentProfileId	=	$row['customerPaymentProfileId'];
				$prepaidTransactionId		=	$row['prepaidTransactionId'];
				$chargeId					=	$row['chargeId'];
				$usingStripeAccountId		=	$row['usingStripeAccountId'];
				$paymentGateway				=	$row['paymentGateway'];
				$isPaidThroughWallet		=	$row['isPaidThroughWallet'];
				$walletAccountId			=	$row['walletAccountId'];
				$prepaidOrderPrice			=	$row['prepaidOrderPrice'];
				$postOrderCost			    =	$row['postOrderCost'];
				
		
				$orderText					=	$a_customerOrder[$orderType];
				if($orderType				==	6 && !empty($customersOwnOrderText))
				{
					$orderText				=	$orderText."&nbsp;(".$customersOwnOrderText.")";
				}
						
				$query1						=	"SELECT folderId,completeName,email,secondaryEmail,folderId,noEmails FROM members WHERE memberId=$memberId AND isActiveCustomer=1 AND memberType='".CUSTOMERS."'";
				$result1					=	dbQuery($query1);						
				if(mysqli_num_rows($result1))
				{
					
					$row1					=	mysqli_fetch_assoc($result1);
					$folderId				=	stripslashes($row1['folderId']);
					$completeName			=	stripslashes($row1['completeName']);
					$customerEmail			=	$row1['email'];
					$secondaryEmail			=	$row1['secondaryEmail'];
					$folderId				=	$row1['folderId'];
					$noEmails				=	$row1['noEmails'];

					dbQuery("UPDATE members SET totalOrdersPlaced=totalOrdersPlaced-1 WHERE memberId=$memberId AND totalOrdersPlaced > 0");

					$memberObj->deleteMemberOrderFolder($orderId,$memberId);

					dbQuery("DELETE FROM cron_transfer_order_files WHERE orderId=$orderId AND memberId=$memberId AND isNewMultipleOrderSystem=1 AND fileId <> 0");

					dbQuery("DELETE FROM members_employee_messages WHERE orderId=$orderId AND memberId=$memberId");

					dbQuery("DELETE FROM members_orders WHERE orderId=$orderId AND memberId=$memberId AND isEmailOrder=1 AND isNotVerfidedEmailOrder=1");
	
					if(!empty($chargeId) && $paymentGateway == "Stripe"){
						/////////////// VOID STRIPE PAYMENTS //////////////////
						
						require_once(SITE_ROOT.'/stripe/init.php');
						\Stripe\Stripe::setApiKey(STRIPE_SECREAT_KEY);

						try{
							
							$re = \Stripe\Refund::create(array(
							  "charge" => $chargeId
							));

							//pr($re);
		
						}
						catch(Exception $e){
						   	//echo  "Message - ".$e->getMessage();								   
						}						
					}
					elseif(!empty($prepaidTransactionId))
					{
						include_once(SITE_ROOT      .   "/classes/authorize.class.php");
						// Create an object of AuthorizeAPI class
						$objAuthorizeAPI            =   new AuthorizeAPI(AUTHORIZE_PAYMENT_LOGIN_ID, AUTHORIZE_PAYMENT_TRANSACTION_KEY, 'liveMode');

						$arrRefundResponse = $objAuthorizeAPI->refundMoneyFromTransaction($customerProfileId,$customerPaymentProfileId, $prepaidTransactionId, $postOrderCost);

						/*if(VISITOR_IP_ADDRESS	==	"122.160.167.153"){
			        		print_r($arrRefundResponse);
			            	die("KASE FINAL");	
			            }*/

						/*include(SITE_ROOT	.   "/classes/vars.php");
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
						$parsedresponse =     parse_api_response($response);*/
					}
					elseif($isPaidThroughWallet == "yes" && !empty($walletAccountId)){
						$walletAmount		=	$employeeObj->getSingleQueryResult("SELECT amount FROM wallet_master WHERE memberId=$memberId","amount");
						if(empty($walletAmount)){
							$walletAmount	=	0;
						}

						$referenceNumber		= $walletAccountId;
						if(strlen($referenceNumber) < 4){
							$referenceNumber	=	"1010".$referenceNumber;
						}
						$currentBalance			=	$walletAmount+$prepaidOrderPrice;
						$currentBalance			=	round($currentBalance,2);
						$t_orderAddress			=	makeDBSafe($orderAddress);


						dbQuery("INSERT INTO wallet_transactions SET memberId=$memberId,amount='$prepaidOrderPrice',transactionType='credit',creditType='deletedorders',proceedingDate='".CURRENT_DATE_INDIA."',proceedingTime='".CURRENT_TIME_INDIA."',estProceedingDate='".CURRENT_DATE_CUSTOMER_ZONE."',estProceedingTime='".CURRENT_TIME_CUSTOMER_ZONE."',status='success',paymentThrough='revertdeletedorders',orderId=$orderId,referenceNumber='$referenceNumber',orderAddress='$t_orderAddress',currentBalance='$currentBalance'");

						dbQuery("UPDATE wallet_master SET amount='$currentBalance' WHERE memberId=$memberId");
					}									
					$t_deleteOrderNotes		=	"Employee ".$s_employeeName." Deleted order Comed from Email";

					dbQuery("INSERT INTO delete_order_reason SET orderId=$orderId,orderDate='$orderPlacedDate',deletedOn='".CURRENT_DATE_INDIA."',memberId=$memberId,deletedBy='Employee',deletedTime='".CURRENT_TIME_INDIA."',deletedIp='".VISITOR_IP_ADDRESS."',deleteOrderNotes='$t_deleteOrderNotes'");

					include(SITE_ROOT		.   "/classes/email-templates.php");
					$emailObj				=	new emails();

					$a_templateSubject		=	array("{orderNo}"=>$orderAddress);

					$a_templateData			=	array("{name}"=>"Manager","{orderNo}"=>$orderAddress,"{orderType}"=>$orderText,"{cutomerName}"=>$completeName,"{deleteOrderNotes}"=>$t_deleteOrderNotes);
					$uniqueTemplateName		=	"TEMPLATE_SENDING_DELETE_CUSTOMER_ORDER_WITH_REASON";
					//$toEmail				=	"gaurabieimpact1@gmail.com";
					$toEmail				=	"john@ieimpact.net";
						
					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

				}

			}
		}

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/verify-email-orders.php");
		exit();
	}
?>
<script type="text/javascript">
	function delOrder(orderId,customerId)
	{
		var confirmation = window.confirm("Are You Sure Delete This Order?");
		if(confirmation == true)
		{
			window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/verify-email-orders.php?orderId="+orderId+"&customerId="+customerId+"&isDelete=1";
		}
	}
</script>
<table width='99%' align='center' cellpadding='0' cellspacing='0' border='0'>
<tr>
	<td colspan="10" class="textstyle2">
		<b>
			EMAIL ORDERS NEED TO VERIFY
		</b>
	</td>
</tr>
<tr>
	<td colspan="10">
		<?php
			include($formSearch);
		?>
	</td>
</tr>
<?php	
	$start					  =	0;
	$recsPerPage	          =	25;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andCaluse.$andClause1;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"members_orders INNER JOIN members ON members_orders.memberId=members.memberId";
	$pagingObj->selectColumns = "members_orders.memberId,orderId,orderAddress,orderAddedOn,orderAddedTime,orderType,customersOwnOrderText,firstName,lastName";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/verify-email-orders.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet				= $pagingObj->getRecords();
		$i						=	$recNo;

	?>
	<tr bgcolor="#373737" height="20">
		<td class="smalltext12" width="2%">&nbsp;</td>
		<td class="smalltext12" width="16%">CUSTOMER NAME</td>
		<td class="smalltext12" width="22%">ORDER ADDRESS</td>
		<td class="smalltext12" width="20%">ORDER TYPE</td>
		<td class="smalltext12" width="17%">DATE</td>
		<td class="smalltext12">&nbsp;</td>
	</tr>
	<?php

		while($row					=   mysqli_fetch_assoc($recordSet))
		{
			$i++;
			$orderId				=	$row['orderId'];
			$memberId				=	$row['memberId'];
			$firstName				=	stripslashes($row['firstName']);
			$lastName				=	stripslashes($row['lastName']);
			$completeName			=	$firstName." ".substr($lastName, 0, 1);
			$orderId				=	$row['orderId'];
			$orderAddress			=	stripslashes($row['orderAddress']);
			$orderType				=	$row['orderType'];
			$orderTypeText			=	$a_customerOrder[$orderType];
			$orderAddedOn			=	$row['orderAddedOn'];
			$orderAddedTime			=	$row['orderAddedTime'];
			$customersOwnOrderText	=	stripslashes($row['customersOwnOrderText']);

			$bgColor				=	"class='rwcolor1'";
			if($i%2==0)
			{
				$bgColor			=	"class='rwcolor2'";
			}
			if($orderType			==	6 && !empty($customersOwnOrderText))
			{
				$orderTypeText		=	$orderTypeText."&nbsp;(".$customersOwnOrderText.")";
			}
			$daysAgo				=	showDateTimeFormat($orderAddedOn,$orderAddedTime);
	?>
	<tr height="25" <?php echo $bgColor;?> valign="top">
		<td class="smalltext20">
			<?php echo $i;?>)
		</td>
		<td class="smalltext20" valign="top">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=<?php echo $memberId;?>" class='link_style12' style='cursor:pointer'><?php echo getSubstring($completeName,30);?></a>
		</td>
		<td class="smalltext20" valign="top">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $memberId;?>" class='link_style12' style='cursor:pointer'><?php echo getSubstring($orderAddress,45);?></a>
		</td>
		<td class="smalltext3" valign="top">
			<?php echo $orderTypeText;?>
		</td>
		<td class="smalltext3" valign="top">
			<?php echo $daysAgo;?>
		</td>
		<td class="smalltext">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $memberId;?>" class='link_style12' style='cursor:pointer'>Verify</a>
			<?php
				if($s_hasManagerAccess)
				{
					echo " | <a onclick='delOrder($orderId,$memberId)' class='link_style12' style='cursor:pointer;'>Delete</a>";
				}
			?>
		</td>
	</tr>
	<?php
		}
	?>
		<tr><td colspan='15' height="15"></td>
		<tr><td colspan='15' align='right'>
		<?php
			
			$pagingObj->displayPaging($queryString);
		?>
		</td></tr>
	<?php
	}
	else
	{
		echo "<tr><td align='center' class='error' colspan='8' height='50'><b>No New Emails Orders To Verify.</b></td></tr><tr><td height='200'></td></tr>";
	}
	echo "</table>";
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>