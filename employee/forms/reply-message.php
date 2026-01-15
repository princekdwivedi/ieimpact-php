<script type="text/javascript">
function checkReply()
{
	form1	=	document.replyMessage;
	if(form1.replyText.value	==	"")
	{
		alert("Please enter your reply !!");
		form1.replyText.focus();
		return false;
	}
}
</script>
<form name="replyMessage" action="" method="POST" onsubmit="return checkReply();">
<table cellpadding="3" cellspacing="2" width="98%" border="0" align="center">
	<tr height="10" bgcolor="#00557D">
		<td colspan="3" class="heading2">REPLY TO NOTICE</td>
	</tr>
	<tr>
		<td width="20%" class="title1">
			Notice 
		</td>
		<td width="2%" class="title1">
			:
		</td class="smalltext2">
		<td>
			<b>
				<?php
					echo $title;
				?>
			</b>
		</td>
	</tr>
	<tr>
		<td class="title1" valign="top">
			Message 
		</td>
		<td  valign="top" class="title1">
			:
		</td class="smalltext2"  valign="top">
		<td>
			<b>
				<?php
					echo $message;
				?>
			</b>
		</td>
	</tr>
	<tr>
		<td class="title1" valign="top">
			Notice Given On
		</td>
		<td  valign="top" class="title1">
			:
		</td class="smalltext2"  valign="top">
		<td>
			<b>
				<?php
					echo showDate($addedOn);
				?>
			</b>
		</td>
	</tr>
	<tr>
		<td colspan="3" class="title1">Enter Your Reply Message Below</td>
	</tr>
	<tr>
		<td colspan="3">
			<textarea name="replyText" cols="50" rows="4"><?php echo $replyText;?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
			<input type='hidden' name='formSubmitted' value='1'>
		</td>
	</tr>
</table>
</form>