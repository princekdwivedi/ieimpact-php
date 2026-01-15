<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/common.php");
	$employeeObj				=  new employee();
	$memberObj					=  new members();
	$orderObj					=  new orders();
	$commonObj					=  new common();
	$a_allmanagerEmails			=  $commonObj->getMangersEmails();
	$selectDaysFor				=	7;
	$backOrderId				=	0;
	$backCustomerId				=	0;
	$backOrderLink				=	"";

	$calculateReplyRateFrom		=	getPreviousGivenDate($nowDateIndia,$selectDaysFor);

	$a_totalUnRepliedQa			=	$orderObj->getTotalUnrepliedratedOrders($calculateReplyRateFrom,$nowDateIndia,$s_employeeId);

	if(isset($_GET['backOrderId']) && isset($_GET['backCustomerId']))
	{
		$backOrderId		    =	$_GET['backOrderId'];
		$backCustomerId		    =	$_GET['backCustomerId'];
		if(!empty($backOrderId) && !empty($backCustomerId)){
			$backOrderLink		=	"&backOrderId=".$backOrderId."&backCustomerId=".$backCustomerId;
		}
	}
	if(!empty($a_totalUnRepliedQa))
	{
		$explanationOnRatingText	=	$employeeObj->getSingleQueryResult("SELECT explanationOnRatingText FROM asking_employee_explanation_on_ratings","explanationOnRatingText");
		if(empty($explanationOnRatingText))
		{
			$explanationOnRatingText=	"Awful, Poor, Fair, Good, Excellent";
		}
		$askingExplanationFromText	=	$employeeObj->getSingleQueryResult("SELECT askingExplanationFromText FROM asking_employee_explanation_on_ratings","askingExplanationFromText");
		if(empty($explanationOnRatingText))
		{
			$askingExplanationFromText=	"processed";
		}
?>
<html>
	<head>
		<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
	</head>
	<body style="topmargin:0px;">
		<table width="98%" align="cnter" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="10" class="heading3">
					Reply on customer <?php echo $explanationOnRatingText;?> rating given on last <?php echo $selectDaysFor;?> days which were <?php echo $askingExplanationFromText;?> by you 
				</td>
			</tr>
			<tr>
				<td colspan="10">
					<hr size="1" width="100%" color="#4d4d4d">
				</td>
			</tr>
			<tr>
				<td width="35%" class="smalltext2"><b>Order Address</b></td>
				<td width="20%" class="smalltext2"><b>Customer Name</b></td>
				<td width="12%" class="smalltext2"><b>Rated Given</b></td>
				<td width="12%" class="smalltext2"><b>Processed By</b></td>
				<td width="12%" class="smalltext2"><b>Qa By</b></td>
				<td></td>
			</tr>
			<tr>
				<td colspan="10">
					<hr size="1" width="100%" color="#4d4d4d">
				</td>
			</tr>
			<?php 
				foreach($a_totalUnRepliedQa as $orderId=>$value)
				{
					$query	=	"SELECT members_orders.memberId,orderAddress,rateGiven,acceptedBy,qaDoneBy FROM members_orders INNER JOIN members_orders_reply ON members_orders.orderId=members_orders_reply.orderId WHERE members_orders.orderId=$orderId";
					$result	=	dbQuery($query);
					if(mysqli_num_rows($result))
					{
						$row				=	mysqli_fetch_assoc($result);
						$customerId			=	$row['memberId'];
						$orderAddress		=	stripslashes($row['orderAddress']);
						$acceptedBy			=	$row['acceptedBy'];
						$rateGiven			=	$row['rateGiven'];
						$qaDoneBy			=	$row['qaDoneBy'];

						$customerName		=	$commonObj->getMemberName($customerId);
						$processBy			=	$employeeObj->getEmployeeFirstName($acceptedBy);
						$qaDoneBy			=	$employeeObj->getEmployeeFirstName($qaDoneBy);


			?>
			<tr>
				<td class="smalltext10" valign="top">
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/add-comment-on-customer-rated.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $customerId.$backOrderLink;?>#addComment" class="link_style5">
						<?php echo $orderAddress;?>
					</a>
				</td>
				<td class="smalltext10" valign="top">
					<?php echo $customerName;?>
				</td>
				<td class="smalltext10" valign="top">
					<?php
						if(!empty($rateGiven))
						{
							for($i=1;$i<=$rateGiven;$i++)
							{
								echo "<img src='".SITE_URL."/images/star.gif'  width=12 height=12'>";
							}
						}
					?>
				</td>
				<td class="smalltext10" valign="top">
					<?php echo $processBy;?>
				</td>
				<td class="smalltext10" valign="top">
					<?php echo $qaDoneBy;?>
				</td>
				<td>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/add-comment-on-customer-rated.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $customerId.$backOrderLink;?>#addComment" class="link_style7">Add Reply</a>
				</td>
			</tr>
			<tr>
				<td colspan="10">
					<hr size="1" width="100%" color="#4d4d4d">
				</td>
			</tr>
			<?php
					}
				}
			?>
		</table>
	</body>
</html>
<?php
	}
?>