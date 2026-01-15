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

	$orderId					=	0;
	$srNo						=	0;
	$messageId					=	0;
	if(isset($_GET['srNo']))
	{
		$srNo					=	$_GET['srNo'];
	}
	if(isset($_GET['messageId']))
	{
		$messageId				=	$_GET['messageId'];
	}
	if(isset($_GET['orderId']))
	{
		$orderId				=	$_GET['orderId'];
		if(!empty($orderId))
		{
						
			$memberId			=	@mysql_result(dbQuery("SELECT memberId FROM members_orders WHERE orderId=$orderId"),0);
			if(!empty($memberId))
			{
				dbQuery("UPDATE members_orders SET isHavingOrderNewMessage=0 WHERE orderId=$orderId");

				dbQuery("UPDATE members_employee_messages SET isRepliedMessage=1 WHERE orderId=$orderId AND memberId=$memberId");

				dbQuery("UPDATE members_employee_messages  SET isRepliedToEmail=1,messageRepliedMarkedBy=$s_employeeId,repliedOn='".CURRENT_DATE_INDIA."',repliedTime='".CURRENT_TIME_INDIA."',repliedFromIP='".VISITOR_IP_ADDRESS."' WHERE orderId=$orderId AND memberId=$memberId AND messageId=$messageId AND isRepliedToEmail=0 AND messageRepliedMarkedBy=0");

				dbQuery("DELETE FROM customer_orders_messages_counts WHERE messageId=$messageId AND orderId=$orderId AND memberId=$memberId");
			}
		}
	}
?>
<div id="showHideMessage<?php echo $srNo;?>">
	 <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
		<tr height='20'>
			<td class='error' style="text-align:center">Successfully Marked As Replied !</td>
		</tr>
	</table>
</div>

