<?php
	Header("Cache-Control: must-revalidate");
	$ExpStr = "Expires: Thu, 29 Oct 1998 17:04:19 GMT";
	Header($ExpStr);
	session_start();
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	
	$section					=	2;
	$messageId					=	0;
	$t_adminMessageLevel		=	"";
	$t_adminMessage				=	"";
	$messageText				=	"";

	if(isset($_GET['messageId']))
	{
		$messageId						=	$_GET['messageId'];
	}

	if(!empty($messageId))
	{
		if($messageId					==	"-1")
		{
			$t_adminMessageLevel		=	"Not found suitable instructions, add own message";
			$t_adminMessage				=	"";
			$messageText			=	"Message to Customer <font color='ff0000'>(Make sure to write Correct English)</font>";
		}
		else
		{
			$query						=	"SELECT * FROM admin_added_customer_messages WHERE section=$section AND messageId=$messageId";
			$result						=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row				    =	mysqli_fetch_assoc($result);
				$t_adminMessageLevel    =	stripslashes($row['messageLevel']);
				$t_adminMessage		    =	stripslashes($row['message']);
				$messageText			=	"Message to Customer <font color='ff0000'>(Make sure to write Correct English)</font>";
			}
		}
	?>
	<table width="100%" align="center" border="0" cellpadding="3" cellspacing="3">
		<tr>
			<td class="smalltext2" width="24%"><b>Message Title</b></td>
			<td class="smalltext2" width="2%" align="center"><b>:</b></td>
			<td class="error">
				<b>
					<?php
						echo $t_adminMessageLevel;
					?>
				</b>
			</td>
		</tr>
		<tr>
			<td  colspan="4"></td>
		</tr>
		<tr>
			<td colspan="3" class="smalltext2"><b><?php echo $messageText?></b></td>
		</tr>
		<tr>
			<td colspan="3">
				<textarea name="replyInstructions" rows="7" cols="70"><?php echo stripslashes(htmlentities($t_adminMessage,ENT_QUOTES))?></textarea>
			</td>
		</tr>
	<table>
<?php
	}
?>
