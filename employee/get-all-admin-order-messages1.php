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
	$existingMessageId			=	0;
	$t_adminMessageLevel		=	"";
	$t_adminMessage				=	"";
	$messageText				=	"Enter own message";
	$disableTextAdded			=	"disabled";
	$checkIsEdit				=	"";
	$checkIsEdit1				=	"checked";
	//pr($_REQUEST);

	if(isset($_GET['messageId']))
	{
		$messageId					=	$_GET['messageId'];
	}
	if(isset($_GET['existingMessageId']))
	{
		$existingMessageId			=	$_GET['existingMessageId'];
		if(!empty($existingMessageId))
		{
			$disableTextAdded		=	"";
		}
	}

	if(!empty($messageId))
	{
		if($messageId				==	"-1")
		{
			$t_adminMessageLevel    =	"Not found suitable message, add own message";
			$t_adminMessage		    =	"Enter Your Message Here";
			$disableTextAdded		=	"";
			$checkIsEdit			=	"checked";
			$checkIsEdit1			=	"";
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
				<textarea name="message" rows="9" cols="80" id="messageWriteID" wrap="hard" onKeyDown="textCounter(this.form.message,this.form.remLentext1,1000);" onKeyUp="textCounter(this.form.message,this.form.remLentext1,1000);" onFocus="if(this.value=='Enter Your Message Here') this.value='';" onBlur="if(this.value=='') this.value='Enter Your Message Here';" <?php echo $disableTextAdded;?> style="border:1px solid #333333;font-family:verdana;font-size:12px;"><?php echo $t_adminMessage;?></textarea>

				<br><font class="smalltext2">Characters Left: <input type="textbox" readonly name="remLentext1" size=2 value="1000" style="border:0"></font>
				&nbsp;
				<?php
					if(empty($existingMessageId))
					{
						if($messageId		!=	"-1")
						{	
					?>
					<font class="nextText"><input type="radio" name="isSendingForverify" value="1" <?php echo $checkIsEdit;?> onclick="changeEnableDisable(1);"  id="ends1">Edit This Message&nbsp;
					<input type="radio" name="isSendingForverify" value="2" <?php echo $checkIsEdit1;?>  onclick="changeEnableDisable(2);" id="ends2">Send As It Is</font>
					<?php
						}
						else
						{
							echo "<input type='hidden' name='isSendingForverify' value='1'>";
						}
					}
					else
					{
						echo "<input type='hidden' name='isSendingForverify' value='2'>";
					}
				?>
			</td>
		</tr>
	<table>
<?php
	}
?>