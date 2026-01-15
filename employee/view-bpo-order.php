<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	$employeeObj				=  new employee();
	$memberObj					=  new members();
	$orderObj					=  new orders();

	if(isset($_GET['orderId']) && isset($_GET['customerId']))
	{
		$orderId		=	$_GET['orderId'];
		$customerId		=	$_GET['customerId'];
		if(!in_array($customerId,$a_assignedToCustomerIds))
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
			exit();
		}
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
		exit();
	}

	if(isset($_GET['accept']) && $_GET['accept'] == 1)
	{
		$accept	=	$_GET['accept'];
		if(in_array($customerId,$a_orderCustomers))
		{
			if(!empty($orderId) && $accept == 1)
			{
				$orderObj->acceptCustomerOrder($orderId,$customerId,$s_employeeId);
			}
		}
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/view-bpo-order.php?orderId=".$orderId."&customerId=".$customerId."#action");
		exit();
	}

	if(isset($_GET['unaccept']) && $_GET['unaccept'] == 1)
	{
		$unaccept	=	$_GET['unaccept'];
		if(in_array($customerId,$a_orderCustomers))
		{
			if(!empty($orderId) && $unaccept == 1)
			{
				if(!empty($s_hasManagerAccess))
				{
					$orderObj->unacceptCustomerOrder($orderId,$customerId);
				}
			}
		}
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/view-bpo-order.php?orderId=".$orderId."&customerId=".$customerId."#action");
		exit();
	}

	if(isset($_GET['attention']))
	{
		$attention	=	$_GET['attention'];
		if(!empty($attention))
		{
			if($attention	== 1)
			{
				dbQuery("INSERT INTO order_attention SET orderId=$orderId,customerId=$customerId,attentionStatus=1,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',addedBy=$s_employeeId");

				dbQuery("UPDATE members_orders SET status=3 WHERE orderId=$orderId AND memberId=$customerId");
			}
			elseif($attention	== 2)
			{
				$query		=	"SELECT attentionId FROM order_attention WHERE orderId=$orderId AND customerId=$customerId AND attentionStatus=1";
				$result		=	dbQuery($query);
				if(mysql_num_rows($result))
				{
					$row			=	mysql_fetch_assoc($result);
					$attentionId	=	$row['attentionId'];

					dbQuery("UPDATE order_attention SET attentionStatus=2,unmarkOn='".CURRENT_DATE_INDIA."',unmarkTime='".CURRENT_TIME_INDIA."',unmarkBy=$s_employeeId WHERE orderId=$orderId AND customerId=$customerId AND attentionStatus=1 AND attentionId=$attentionId");

					dbQuery("UPDATE members_orders SET status=0 WHERE orderId=$orderId AND memberId=$customerId");
				}
			}
		}
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/view-bpo-order.php?orderId=".$orderId."&customerId=".$customerId."#action");
		exit();
	}
?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
<tr>
	<td colspan="7" height="20"></td>
</tr>
<tr>
	<td colspan="8" class="heading1">
		:: VIEW CUSTOMER BPO ORDER ::
	</td>
</tr>
<tr>
	<td colspan="8" height="5"></td>
</tr>
</table>
<?php

	include(SITE_ROOT_EMPLOYEES	. "/includes/bpo-customer-order-details.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/bpo-order-details.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/view-bpo-reply-details.php");
?>
<script type="text/javascript">

function acceptOrder(orderId,customerId)
{
	var confirmation = window.confirm("Are You Sure Accept This Order?");
	if(confirmation == true)
	{
		window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/view-bpo-order.php?orderId='+orderId+"&customerId="+customerId+"&accept=1";
	}
}
function attentionOrder(orderId,customerId,flag)
{
	if(flag == 1)
	{
		var confirmation = window.confirm("Are You Sure To Marked This Order As Need Attention?");
	}
	else
	{
		var confirmation = window.confirm("Are You Sure To Unmarked This Order As Need Attention?");
	}
	if(confirmation == true)
	{
		window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/view-bpo-order.php?orderId='+orderId+"&customerId="+customerId+"&attention="+flag;
	}
}
function acceptMaximumOrder(orderId,customerId)
{
	var confirmation = window.confirm("Please complete previous accepted orders first to accept a new order.");
	if(confirmation == true)
	{
		window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/view-bpo-order.php?orderId='+orderId+"&customerId="+customerId+"#action";
	}
}
function unacceptOrder(orderId,customerId)
{
	var confirmation = window.confirm("Are You Sure Unaccept This Order?");
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/view-bpo-order.php?orderId="+orderId+"&customerId="+customerId+"&unaccept=1";
	}
}
function acceptOrderWindow(orderId,customerId)
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/accept-orders-behalf-employee.php?orderId="+orderId+"&customerId="+customerId;
	prop = "toolbar=no,scrollbars=yes,width=1200,height=350,top=100,left=100";
	window.open(path,'',prop);
}
</script>
<a name="action"></a>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
<?php
	if(empty($isDeleted))
	{
?>
<tr>
	<td class="heading1">
		<?php
			$repliedUploaded=	0;
			$repliedUploaded=	$orderObj->getRepliedStatus($orderId,$customerId);
			$replyText		=	"PROCESS";
			if($repliedUploaded	==	1)
			{
				$replyText	=	"EDIT";
			}
			
			if($status	==	0 && in_array($customerId,$a_orderCustomers))
			{
				$totalUnReplied	=	0;
				$totalUnReplied	= $orderObj->checkAcceptedReplyOrder($s_employeeId);
				$maximumOrdersAccept=	$employeeObj->maximumAcceptOrders($s_employeeId);
				if($s_hasManagerAccess)
				{
					echo "<a href='javascript:acceptOrder($orderId,$customerId)' class='link_style13'>ACCEPT</a>&nbsp; |&nbsp;";
					echo "<a href='javascript:acceptOrderWindow($orderId,$customerId)' class='link_style13'>ASSIGN</a>&nbsp; |&nbsp;";
				}
				else
				{
					if(!empty($totalUnReplied) && !empty($maximumOrdersAccept))
					{
						if($totalUnReplied < $maximumOrdersAccept)
						{
							echo "<a href='javascript:acceptOrder($orderId,$customerId)' class='link_style13'>ACCEPT</a>&nbsp; |&nbsp;";
						}
						else
						{
							echo "<a href='javascript:acceptMaximumOrder($orderId,$customerId)' class='link_style13'>ACCEPT</a>&nbsp; |&nbsp;";
						}
					}
					else
					{
						echo "<a href='javascript:acceptOrder($orderId,$customerId)' class='link_style13'>ACCEPT</a>&nbsp; |&nbsp;";
					}
				}
			}
			if($status	==	1 && in_array($customerId,$a_orderCustomers) && !empty($acceptedBy))
			{
				$isManger	=	$employeeObj->isEmployeeManager($acceptedBy);
				if(empty($isManger))
				{
					$isManger	=	0;
				}
				if(!empty($s_hasManagerAccess))
				{
					echo "<a href='".SITE_URL_EMPLOYEES."/process-bpo-order.php?orderId=$orderId&customerId=$customerId' class='link_style13'>$replyText</a>&nbsp; |&nbsp;";
				}
				else
				{
					if($acceptedBy == $s_employeeId && $isManger ==	0)
					{
						echo "<a href='".SITE_URL_EMPLOYEES."/process-bpo-order.php?orderId=$orderId&customerId=$customerId' class='link_style13'>$replyText</a>&nbsp; |&nbsp;";
					}
				}
			}
			if($status == 1 && $repliedUploaded == 1 && in_array($customerId,$a_qaCustomers))
			{
				echo "<a href='".SITE_URL_EMPLOYEES."/mark-bpo-qa-order.php?orderId=$orderId&customerId=$customerId&doneQa=1#mark' class='link_style13'>DO QA</a>&nbsp; |&nbsp;";
			}
			if($repliedUploaded == 0 && !empty($s_hasManagerAccess) && in_array($customerId,$a_qaCustomers) && $status == 1)
			{
				echo "<a href='javascript:unacceptOrder($orderId,$customerId)' class='link_style13'>UNACCEPT</a> | ";
			}
			if($status == 2)
			{
				if(!empty($s_hasManagerAccess))
				{
					echo "<a href='".SITE_URL_EMPLOYEES."/re-send-bpo-order.php?orderId=$orderId&customerId=$customerId' class='link_style13'>RESEND FILES</a> | ";
				}
				else
				{
					if($acceptedBy == $s_employeeId || $qaDoneBy == $s_employeeId)
					{
						echo "<a href='".SITE_URL_EMPLOYEES."/re-send-bpo-order.php?orderId=$orderId&customerId=$customerId' class='link_style13'>RESEND FILES</a> |";
					}
				}
			}
	
			echo "<a href='".SITE_URL_EMPLOYEES."/send-message-bpo-customer.php?orderId=$orderId&customerId=$customerId#sendMessages' class='link_style13'>SEND MSG</a> |&nbsp;";
			echo "<a href='".SITE_URL_EMPLOYEES."/internal-emp-msg.php?orderId=$orderId&customerId=$customerId#sendMessages' class='link_style13'>INTERNAL EMP. MSG</a> |&nbsp;";
			if($status	==	0)
			{
				echo "<a href='javascript:attentionOrder($orderId,$customerId,1)' class='link_style13'>NEED CUSTOMER ATTENTION</a>&nbsp; |&nbsp;";
			}
			elseif($status	==	3)
			{
				echo "<a href='javascript:attentionOrder($orderId,$customerId,2)' class='link_style13'>UNMARKED CUSTOMER ATTENTION</a>&nbsp; |&nbsp;";
			}
		?>
	</td>
</tr>
<?php
	}			
?>
<tr>
	<td>
		<input type="button" name="submit" onClick="history.back()" value="BACK">
	</td>
</tr>
</table>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>