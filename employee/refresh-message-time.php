<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");

	$messageId				=	0;
	$messageForType			=	0;
	if(isset($_GET['messageForType']))
	{
		$messageForType		=	$_GET['messageForType'];
	}
	if(isset($_GET['messageId']))
	{
		$messageId			=	$_GET['messageId'];
		if(!empty($messageId) && empty($messageForType))
		{
			$query			=	"SELECT addedOn,addedTime FROM members_employee_messages WHERE messageId=$messageId";
			$result			=	dbQuery($query);
			if(mysql_num_rows($result))
			{
				$row		=	mysql_fetch_assoc($result);
				$messageDate=	$row['addedOn'];
				$messageTime=	$row['addedTime'];
			}
		}
		elseif(!empty($messageId) && $messageForType == 1)
		{
			$query			=	"SELECT addedOn,addedtime FROM members_general_messages WHERE generalMsgId=$messageId";
			$result			=	dbQuery($query);
			if(mysql_num_rows($result))
			{
				$row		=	mysql_fetch_assoc($result);
				$messageDate=	$row['addedOn'];
				$messageTime=	$row['addedtime'];
			}
		}
	}
	?>
	<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
		<tr>
			<td class="error" valign="top">
				(<?php 
					echo getHoursBetweenDates($messageDate,CURRENT_DATE_INDIA,$messageTime,CURRENT_TIME_INDIA);
				?>)
			</td>
		</tr>
	</table>
	
