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

	$generalMsgId				=	0;
	$srNo						=	0;
	$isDelete					=	0;
	if(isset($_GET['srNo']))
	{
		$srNo					=	$_GET['srNo'];
	}
	$statusClause				=	" AND status=0";
	if(isset($_GET['isDelete']))
	{
		$isDelete				=	$_GET['isDelete'];

		if($isDelete			==	1)
		{
			$statusClause		=	"";
		}
	}

	if(isset($_GET['msgId']))
	{
		$generalMsgId			=	$_GET['msgId'];
		if(!empty($generalMsgId))
		{
			$query					=	"SELECT * FROM members_general_messages WHERE generalMsgId=$generalMsgId AND isOrderGeneralMsg=1 AND isBillingMsg=0".$statusClause." AND parentId=0";
			$result	=	dbQuery($query);
			if(mysql_num_rows($result))
			{
				$showForm					=	true;

				$row						=	mysql_fetch_assoc($result);
				$generalMsgId				=	$row['generalMsgId'];
				$memberId					=	$row['memberId'];

				if($isDelete == 1)
				{
					dbQuery("DELETE FROM members_general_messages WHERE memberId=$memberId AND generalMsgId=$generalMsgId AND isOrderGeneralMsg=1 AND isBillingMsg=0");

					dbQuery("DELETE FROM members_general_messages WHERE memberId=$memberId AND parentId=$generalMsgId AND isOrderGeneralMsg=1 AND isBillingMsg=0");
				}
				else
				{
					dbQuery("UPDATE members_general_messages SET status=1,replyBy=$s_employeeId,isReplyByEmployee=1,repliedOn='".CURRENT_DATE_INDIA."',repliedTime='".CURRENT_TIME_INDIA."' WHERE memberId=$memberId AND generalMsgId=$generalMsgId AND isOrderGeneralMsg=1 AND isBillingMsg=0 AND status=0 AND parentId=0");
				}
				
			}
		}
	}
?>
<div id="showHideGeneralMessage<?php echo $srNo;?>">
	 <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
		<tr height='20'>
			<?php
				if($isDelete    == 1)
				{
					$delMsg		=	"Successfully Deleted !";
				}	
				else
				{
					$delMsg		=	"Successfully Marked As Replied !";
				}
			?>
			<td class='error' style="text-align:center"><?php echo $delMsg;?></td>
		</tr>
	</table>
</div>

