<?php 
	Header("Cache-Control: must-revalidate");
	$ExpStr = "Expires: Thu, 29 Oct 1998 17:04:19 GMT";
	Header($ExpStr);
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES .   "/includes/session-vars.php");
	if(empty($s_employeeId))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	include(SITE_ROOT_EMPLOYEES .   "/classes/employee.php");
	include(SITE_ROOT			.   "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	.   "/includes/common-array.php");
	include(SITE_ROOT_MEMBERS	.   "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	.   "/classes/orders.php");

	$employeeObj				=   new employee();
	$memberObj					=   new members();
	$orderObj					=   new orders();
	$a_allDeactivatedEmployees  =	$employeeObj->getAllInactiveEmployees();
	$totalUnReplied	            =   $orderObj->checkAcceptedReplyOrder($s_employeeId);
	$maximumOrdersAccept	    =	$employeeObj->getSingleQueryResult("SELECT maximumOrdersAccept FROM employee_details WHERE employeeId=$s_employeeId","maximumOrdersAccept");


	/*$totalAcceptedFilesByYou	=	$employeeObj->getSingleQueryResult("SELECT count(*) as total FROM members_orders WHERE isVirtualDeleted=0 AND status=1 AND acceptedBy=$s_employeeId","total");

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
	$totalAvailabaleOrdersToProces	=   0;*/

	$totalAvailabaleOrdersToProces	=   0;
	if(!empty($maximumOrdersAccept) && !empty($totalUnReplied) && $totalUnReplied >= $maximumOrdersAccept){
		$totalAvailabaleOrdersToProces	=   1;
	}

	if(!empty($totalAvailabaleOrdersToProces)){
?>
	<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="smalltext17"><font color='#ff0000;'>Complete previous <br />orders</font></td>
		</tr>
	</table>
<?php
	}
	else{

		$orderId					=	0;
		$srNo						=	0;
		if(isset($_GET['srNo']))
		{
			$srNo					=	$_GET['srNo'];
			$bgColor				=	"class='rwcolor1'";
			if($srNo%2==0)
			{
				$bgColor			=   "class='rwcolor2'";
			}
		}
		if(isset($_GET['orderId']))
		{
			$orderId				=	$_GET['orderId'];
			if(!empty($orderId))
			{
				$orderCustomerId	=	$employeeObj->getSingleQueryResult("SELECT memberId FROM members_orders WHERE orderId=$orderId","memberId");
				$orderObj->acceptCustomerOrder($orderId,$orderCustomerId,$s_employeeId);
				$query				=	"SELECT members_orders.*,completeName,appraisalSoftwareType,totalOrdersPlaced,state FROM members_orders INNER JOIN members ON members_orders.memberId=members.memberId WHERE orderId=$orderId";
				$result				=	dbQuery($query);
				if(mysqli_num_rows($result))
				{
					$row					=	mysqli_fetch_assoc($result);
					$customerId				=	$row['memberId'];
					$completeName			=	stripslashes($row['completeName']);
					$completeName			=	getSubstring($completeName,16);
					$orderId				=	$row['orderId'];
					$orderAddress			=	$t_orderAddress = stripslashes($row['orderAddress']);
					$orderAddress			=	getSubstring($orderAddress,25);
					$orderType				=	$row['orderType'];
					$orderTypeText			=	$a_customerOrder[$orderType];
					$appraisalSoftwareType	=	$row['appraisalSoftwareType'];
					if(in_array($appraisalSoftwareType,$a_appraisalFileTypes)){
						$appraisalText		=	$a_appraisalFileTypes[$appraisalSoftwareType];
					}
					else{
						$appraisalText		=	$a_allAppraisalFileTypes[$appraisalSoftwareType];
					}
					$providedSketch			=	$row['providedSketch'];
					$sketchStatus			=	$row['sketchStatus'];
					$state					=	$row['state'];
					$orderCheckedBy			=	stripslashes($row['orderCheckedBy']);
					$isOrderChecked			=	$row['isOrderChecked'];
					$assignToEmployee 		=	$row['assignToEmployee'];
				    $assignToTime 		    =	$row['assignToTime'];


					$hasMarkedSketchYes		=	"NO";
					if($providedSketch		==	1)
					{
					   $hasMarkedSketchYes	=	"YES";
					   if($sketchStatus		==	1)
					   {
							$hasMarkedSketchYes	=	"ACK";
					   }
					   elseif($sketchStatus	==	2)
					   {
							$hasMarkedSketchYes	=	"DONE";
					   }
					}
					$appraisalText			=	$appraisalText."/".$hasMarkedSketchYes;
					$orderAddedOn			=	$row['orderAddedOn'];
					$displayDate			=	showDateMonth($orderAddedOn);
					$orderAddedTime			=	$row['orderAddedTime'];
					$displayTime			=	showTimeFormat($orderAddedTime);
					$status					=	$row['status'];
					$statusText				=   "<font color='red'>New Order</font>";
					$qaDoneByText			=	"";
					if($result11			=	$orderObj->isOrderChecked($orderId))
					{
						$statusText			=   "<font color='green'>New Order</font>";
					}
					if($status				==	1)
					{
						$statusText			=   "<font color='#4F0000'>Accepted</font>";
					}
					
					$acceptedBy				=	$row['acceptedBy'];
					$totalCustomerOrders	=	$row['totalOrdersPlaced'];
					$isRushOrder			=	$row['isRushOrder'];
					if($isRushOrder		    ==	1)
					{
					   $isRushOrderFont	    =	"<font color='#ff0000'><b>*</b></font>";
					}
					else
					{
						 $isRushOrderFont	=	"";
					}
					$customerOrderText		=	"";
					$customerLinkStyle		=	"link_style16";
					
					if(empty($totalCustomerOrders))
					{
						$totalCustomerOrders=	0;
					}
					if($totalCustomerOrders <= 3)
					{
						$customerOrderText	=	"(New Cus.)";
						$customerLinkStyle	=	"link_style17";
					}
					elseif($totalCustomerOrders > 3 && $totalCustomerOrders <= 7)
					{
						$customerOrderText	=	"(Trial Cus.)";
						$customerLinkStyle	=	"link_style18";
					}
					elseif($totalCustomerOrders >= 100 && $totalCustomerOrders < 350)
					{
						$customerOrderText	=	"(Big Cus.)";
						$customerLinkStyle	=	"link_style20";
					}
					elseif($totalCustomerOrders >= 350 && $totalCustomerOrders < 700)
					{
						$customerOrderText	=	"(VIP Cus.)";
						$customerLinkStyle	=	"link_style21";
					}
					elseif($totalCustomerOrders >= 700)
					{
						$customerOrderText	=	"(VVIP Cus.)";
						$customerLinkStyle	=	"link_style22";
					}

					$timeZoneColor		=	"#333333";
					$timezoneText		=	"";
					if(array_key_exists($state,$a_usaProvinces))
					{
						$timeZone		=	$a_usaProvinces[$state];

						list($stateName,$zone)	=	explode("|",$timeZone);
						if(array_key_exists($zone,$a_timeZoneColor))
						{
							$timeZoneColor		=	$a_timeZoneColor[$zone];
							$timezoneText		=	"(".$zone.")";
						}
						else
						{
							$zone				=	"CST";
							$timeZoneColor		=	$a_timeZoneColor[$zone];
							$timezoneText		=	"(".$zone.")";
						}
					}
					else
					{
						$zone				=	"CST";
						$timeZoneColor		=	$a_timeZoneColor[$zone];
						$timezoneText		=	"(".$zone.")";
					}
					if(!empty($acceptedBy))
					{
						$acceptedByName			=   $employeeObj->getEmployeeFirstName($acceptedBy);
					}
					else
					{
						$acceptedByName			=	"";
					}

				$orderCheckByName				=	"<font color='#ff0000;'>Not Checked</font>";
				$orderCheckedById 				=	0;
				if(!empty($isOrderChecked)){
					$orderCheckByName			=	$orderCheckedBy;
					$orderCheckedById           =   $employeeObj->getSingleQueryResult("SELECT checkedBy FROM checked_customer_orders WHERE orderId=$orderId","checkedBy");
				}
					
				$checkOrderLatestMessageDateTime=	 timeBetweenBeforeMinutes($nowDateIndia,$nowTimeIndia,30);
				list($searchOrderMessageDate,$searchOrderMessageTime)	=	explode("=",$checkOrderLatestMessageDateTime);

				$showCustomerOldMessages	=	"";

				if($result =	$orderObj->getLastOrderMessages($orderId,$customerId,$searchOrderMessageDate,$searchOrderMessageTime))
				{
					$showCustomerLatestMessages	=	"<br><font style='font-size:9px;font-family:verdana;color:#ff0000'>(Customer New Message)</font>";
				}
				else
				{
					$showCustomerLatestMessages	=	"";

					if($result =	$orderObj->getOrderOldMessages($orderId,$customerId,$searchOrderMessageDate,$searchOrderMessageTime))
					{
						$showCustomerOldMessages	=	"<br><font style='font-size:9px;font-family:verdana;color:#363636'>(Customer Old Message)</font>";
					}
					else
					{
						$showCustomerOldMessages	=	"";
					}
				}

				if($result		=	$orderObj->getOrderInternalMessages($orderId,$searchOrderMessageDate))
				{
					$showEmployeeInternalMessageText=	"<br><a href='".SITE_URL_EMPLOYEES."/internal-emp-msg.php?orderId=$orderId&customerId=$customerId#sendMessages' class='link_style15'>(View internal msg)</a>";
				}
				else
				{
					$showEmployeeInternalMessageText=	"";
				}

				////////////////////////////////////////////////////////////////////////////////////////
				//////////////////// PUTTING THE ORDER IN ORDER TRACK LIST ////////////////////////////
			     $orderObj->addOrderTracker($s_employeeId,$orderId,$t_orderAddress,'Employee accpet order','EMPLOYEE_ACCEPT_ORDER');
			    ////////////////////////////////////////////////////////////////////////////////////////
			    ////////////////////////////////////////////////////////////////////////////////////////

			    $orderAccptedDateTime = showDateMonth($assignToEmployee).",".showTimeFormat($assignToTime);
	?>
	<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="smalltext17" valign="top" width="30%"><font color='#4F0000'>Accepted</font></td>
			<td class="smalltext16" width="33%" valign="top">
				<?php 
				  if(in_array($orderCheckedBy,$a_allDeactivatedEmployees) && array_key_exists($orderCheckedById,$a_allDeactivatedEmployees)){
				  	 $orderCheckByName = "Hemant Jindal";
				  }
					echo $orderCheckByName;
				?>
			</td>
			<td class="smalltext16"valign="top">
				  <a onclick="serachRedirectFileType('?isSubmittedForm=1&orderOf=<?php echo $acceptedBy;?>&showingEmployeeOrder=1')" class='link_style12' style="cursor:pointer;" title='View orders of <?php echo $acceptedByName;?>'><br /><?php echo $acceptedByName;?></a><font class='smalltext11'>(<?php echo $orderAccptedDateTime;?>)</font>
			</td>
		</tr>
	</table>
	
	<?php
				}
			}
		}
	}
?>
