<?php
	Header("Cache-Control: must-revalidate");
	$ExpStr = "Expires: Thu, 29 Oct 1998 17:04:19 GMT";
	Header($ExpStr);
	session_start();
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	
	$section					=	1;
	$messageId					=	0;
	$t_adminMessageLevel		=	"";
	$t_adminMessage				=	"";
	$messageText				=	"Enter own message";

	if(isset($_GET['messageId']))
	{
		$messageId			=	$_GET['messageId'];
	}

	if(!empty($messageId))
	{
		if($messageId				==	"-1")
		{
			$t_adminMessageLevel    =	"Not found suitable message, add own message";
			$t_adminMessage		    =	"";
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
				$messageText			=	"Message (You can modify the message and send)";
			}
		}
	?>
	<table width="100%" align="center" border="0" cellpadding="3" cellspacing="3">
		<tr>
			<td class="smalltext2" width="14%"><b>Message Title</b></td>
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
			<td valign="top" colspan="3">
				<textarea name="message" rows="7" cols="70" wrap="hard" onKeyDown="textCounter(this.form.message,this.form.remLentext1,1000);" onKeyUp="textCounter(this.form.message,this.form.remLentext1,1000);" onFocus="if(this.value=='Enter Your Message Here') this.value='';" onBlur="if(this.value=='') this.value='Enter Your Message Here';"><?php echo nl2br($t_adminMessage);?></textarea>

				<br><font class="smalltext2">Characters Left: <input type="textbox" readonly name="remLentext1" size=2 value="1000" style="border:0"></font>
			</td>
		</tr>
	<table>
<?php
	}
?>