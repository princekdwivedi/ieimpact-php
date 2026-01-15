<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	$bypassRateLimitar = 1;
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=  new employee();
	$orderObj					=  new orders();
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$searchBy					=	0;
	$t_searchDate				=	"";
	$searchMonth				=	"";
	$searchYear					=	"";
	$employeeId					=	0;
	$andClause					=	"";
	$andClause1					=	"";
	$text						=	"";
	$employeeName				=	"";
	$orderBy					=	"";	

	//pr($_REQUEST);

	if(isset($_GET['employeeId']))
	{
		$employeeId				=	$_GET['employeeId'];
		$employeeName			=	$employeeObj->getEmployeeName($employeeId);
	}

	if(isset($_GET['searchBy']))
	{
		$searchBy				=	$_GET['searchBy'];

		if($searchBy			==	1)
		{
			if(isset($_GET['searchDate']))
			{
				$t_searchDate	=	$_GET['searchDate'];

				$andClause		=	" AND members_orders.assignToEmployee='$t_searchDate'";
				$text			=	" ON ".showDate($t_searchDate);
				$orderBy		=	" ORDER BY orderAddedOn DESC,orderAddedTime DESC";	
				$serachString	=	"&searchBy=".$searchBy."&searchDate=".$t_searchDate;
			}
		}
		else
		{
			if(isset($_GET['searchMonth']))
			{
				$searchMonth	=	$_GET['searchMonth'];
			}
			if(isset($_GET['searchYear']))
			{
				$searchYear		=	$_GET['searchYear'];
			}
			if(!empty($searchYear) && !empty($searchYear))
			{
				$andClause			=	" AND MONTH(members_orders.assignToEmployee)=$searchMonth AND YEAR(members_orders.assignToEmployee)=$searchYear";
				$text				=	" ON ".$a_month[$searchMonth].",".$searchYear;

				$orderBy			=	" ORDER BY orderAddedOn DESC,orderAddedTime DESC";	
				$serachString		=	"&searchBy=".$searchBy."&searchMonth=".$searchMonth."&searchYear=".$searchYear;
			}
		}
	}
?>
<html>
<head>

</head>

<body>

<?php
	
	if(!empty($employeeId))
	{
		$query	=	"SELECT members_orders.*,firstName,completeName,appraisalSoftwareType,totalOrdersPlaced,state FROM members_orders INNER JOIN members ON members_orders.memberId=members.memberId WHERE members_orders.orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND members_orders.isVirtualDeleted=0 AND members_orders.acceptedBy=$employeeId".$andClause.$orderBy;
		$result		=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$i		=	0;
	?>
	
		<table cellpadding="0" cellspacing="0" width='100%'align="center" border='0'>
			<tr>
				<td colspan="5" class="smalltest2">
					<b>VIEW PROCESSING ORDER DETAILS OF <?php echo $employeeName." ".$text;?></b>
				</td>
				<td align="right" colspan="3">
					<img src="<?php echo SITE_URL;?>/images/close.gif" title="Close" border="0" onclick="removeEmployees(<?php echo $employeeId;?>,2)" style="cursor:pointer;" title="Close">&nbsp;
				</td>
			</tr>
			<tr height="20">
				<td class="smalltext4" width="24%">&nbsp;<b>Customer Name</b></td>
				<td class="smalltext4" width="29%">&nbsp;<b>Order Address</b></td>
				<td class="smalltext4" width="13%"><b>Order Type</b></td>
				<td class="smalltext4" width="7%"><b>File/Sketch</b></td>
				<td class="smalltext4" width="10%"><b>Order On</b></td>
				<td class="smalltext4" width="6%"><b>Status</b></td>
				<td class="smalltext4" width="6%"><b>Qa By</b></td>
				<td class="smalltext4"><b>Rating</b></td>
			</tr>
			<?php
				while($row					=	mysqli_fetch_assoc($result))
				{
					$i++;	
					$customerId				=	$row['memberId'];
					$completeName			=	stripslashes($row['completeName']);
					$firstName				=	stripslashes($row['firstName']);
					$firstName				=	stringReplace("'","",$firstName);
					$firstName				=	stringReplace('"',"",$firstName);
					$completeName			=	getSubstring($completeName,16);
					$orderId				=	$row['orderId'];
					$orderAddress			=	stripslashes($row['orderAddress']);
					$orderAddress			=	getSubstring($orderAddress,35);
					$orderType				=	$row['orderType'];
					$orderTypeText			=	$a_customerOrder[$orderType];
					$appraisalSoftwareType	=	$row['appraisalSoftwareType'];
					$appraisalText		=	$a_allAppraisalFileTypes[$appraisalSoftwareType];
					$providedSketch			=	$row['providedSketch'];
					$sketchStatus			=	$row['sketchStatus'];
					$state					=	$row['state'];
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
					$hasReplied				=	0;
					$statusText				=   "<font color='red'>New Order</font>";
					$qaDoneByText			=	"";
					if($result11			=	$orderObj->isOrderChecked($orderId))
					{
						$statusText			=   "<font color='green'>New Order</font>";
					}
					if($status				==	1)
					{
						$statusText			=   "<font color='#4F0000'>Accepted</font>";
						$hasReplied				=	$employeeObj->getSingleQueryResult("SELECT hasRepliedFileUploaded FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId AND hasRepliedFileUploaded=1","hasRepliedFileUploaded");
						if(!empty($hasReplied))
						{
							$statusText			=	"<font color='blue'>QA Pending</font>";
						}
					}
					
					
					if($status			==	2)
					{
						$statusText			=   "<font color='green'>Completed</font>";
						$qaDoneBy			=	$employeeObj->getSingleQueryResult("SELECT qaDoneBy FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId AND hasQaDone=1","qaDoneBy");
						if(!empty($qaDoneBy))
						{
							$qaDoneByText	=	$employeeObj->getEmployeeFirstName($qaDoneBy);
						}
					}
					elseif($status			==	3)
					{	
						$statusText			=   "<font color='#333333'>Nd Atten.</font>";
					}
					elseif($status			==	5)
					{
						$statusText			=   "<font color='green'>Nd Feedbk.</font>";
					}

					elseif($status			==	4)
					{
						$statusText			=   "<font color='#ff0000'>Cancelled</font>";
					}
					elseif($status			==	6)
					{
						$statusText			=   "<font color='green'>Fd Rcvd</font>";
					}
					$acceptedBy				=	$row['acceptedBy'];
					$totalCustomerOrders	=	$row['totalOrdersPlaced'];
					$rateGiven				=	$row['rateGiven'];
					$memberRateMsg			=	stripslashes($row['memberRateMsg']);
					$isRushOrder			=	$row['isRushOrder'];
					if(!empty($rateGiven))
					{
						if(!empty($memberRateMsg))
						{
							$tipText1		=	$memberRateMsg;
							$tipText1		=	stringReplace('"',"",$tipText1);
							$tipText1		=	stringReplace("'","",$tipText1);
						}
						else
						{
							$tipText1		=	"";
						}
					}
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
								

					$bgColor				=	"class='rwcolor1'";
					if($i%2					==0)
					{
						$bgColor			=   "class='rwcolor2'";
					}
				?>
				
				<tr height="23" <?php echo $bgColor;?>>
					<td class="<?php echo $customerLinkStyle;?>" valign="top">
						<?php 
							echo "<font color='#000000'>".$i.")</font> <a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=$customerId' class='$customerLinkStyle' style='cursor:pointer;'>$completeName</a>";
						?>
					</td>
					<td class="smalltext16" valign="top">
						<?php 
							echo $isRushOrderFont."<a href='".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=$orderId&customerId=$customerId' class='link_style12'>$orderAddress</a>&nbsp;($state)";
						?>
					</td>
					<td class="smalltext16" valign="top"><?php echo $orderTypeText;?></td>
					<td class="smalltext16" valign="top"><?php echo $appraisalText;?></td>
					<td class="smalltext16" valign="top"><font color="<?php echo $timeZoneColor;?>"><?php echo $displayDate.",".$displayTime.$timezoneText;?></font></td>
					<td class="smalltext16" valign="top">
						<?php 
							echo $statusText;
							if($status == 1)
							{
						?>
						(<a onclick="reAssignOrderWindow(<?php echo $orderId;?>,<?php echo $customerId;?>,'<?php echo $serachString;?>')" class="link_style12" style='cursor:pointer;' title='Re-Assign'>RE-ASSIGN</a>)
						<?php
							}
						?>
					</td>
					<td class="smalltext16" valign="top">
						<?php
							echo $qaDoneByText;
						?>
					</td>
					<td class="smalltext16" valign="top">
						<?php
							if(!empty($rateGiven))
							{
						?>
						<img src="<?php echo SITE_URL;?>/images/rating/<?php echo $rateGiven;?>.png"  onmouseover="Tip('<?php echo $tipText1;?>')" onmouseout="UnTip()">
						<?php
							}	
							else
							{
								echo "&nbsp;";
							}
						?>
					</td>
				</tr>
			<?php
					}
				}
			?>
		</table>
		<?php
			}
		
		?>
	
</center>

</html>
