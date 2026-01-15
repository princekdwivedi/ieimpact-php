<script type="text/javascript">
function checkValidReply()
{
	form1	=	document.replyOrders;
	if(form1.replyInstructions.value	==	"")
	{
		alert("Please enter instructions !!");
		form1.replyInstructions.focus();
		return false;
	}
		
	form1.submit.value    = "Wait while editing under process";
	form1.submit.disabled = true;
}

</script>
<br>
<form name="replyOrders" action="" method="POST" enctype="multipart/form-data" onsubmit="return checkValidReply();">
	<table width='98%' align='center' cellpadding='3' cellspacing='0' border='0'>
		<tr>
			<td colspan="4" class="heading1">
				REPLY ORDER FILES
			</td>
		</tr>
		<tr>
			<td class="smalltext2" width="28%" valign="top"><b>Replied Completed Subject MLS & TAX PDF File</b></td>
			<td class="smalltext2"  width="2%" valign="top"><b>:</b></td>
			<td class="smalltext10" valign="top">
				<input type="file" name="replyOrderFile">
				<br>
				<b>
					<?php 
						if($hasReplyOrderFile)
						{
							echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=OF&f=R'  class='link_style2'>".$replyOrderFileName.".".$replyOrderFileExt."</a>";
							
							echo "<br><font class='smalltext2'>".getFileSize($replyOrderFileSize)."</font>";
						}
					?>
				</b>
			</td>
		</tr>
		<tr>
			<td class="smalltext2" valign="top"><b>Replied Active Comps File</b></td>
			<td class="smalltext2" valign="top"><b>:</b></td>
			<td class="smalltext10" valign="top">
				<input type="file" name="replyPublicRecordFile">
				<br>
				<b>
					<?php 
						if($hasReplyPublicRecordFile)
						{
							echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=PF&f=R' class='link_style2'>".$replyPublicRecordFileName.".".$replyPublicRecordFileExt."</a>";
							
							echo "<br><font class='smalltext2'>".getFileSize($replyPublicRecordSize)."</font>";
						}
					?>
				</b>
			</td>
		</tr>
		<tr>
			<td class="smalltext2" valign="top"><b>Replied Sold Comps File</b></td>
			<td class="smalltext2" valign="top"><b>:</b></td>
			<td class="smalltext10" valign="top">
				<input type="file" name="replyMlsFile">
				<br>
				<b>
					<?php 
						if($hasReplyMlsFile)
						{
							echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=MF&f=R' class='link_style2'>".$replyMlsFileName.".".$replyMlsFileExt."</a>";
							echo "<br><font class='smalltext2'>".getFileSize($replyMlsFileSize)."</font>";
						}
					?>
				</b>
			</td>
		</tr>
		<tr>
			<td class="smalltext2" valign="top"><b>Replied Summary File</b></td>
			<td class="smalltext2" valign="top"><b>:</b></td>
			<td class="smalltext10" valign="top">
				<input type="file" name="otherFile">
				<br>
				<b>
					<?php 
						if($hasOtherFile)
						{
							echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=OTF&f=R'  class='link_style2'>".$otherFileName.".".$otherFileExt."</a>";
							echo "<br><font class='smalltext2'>".getFileSize($replyOtherFileSize)."</font>";
						}
					?>
				</b>
			</td>
		</tr>
		<tr>
			<td class="smalltext2" valign="top"><b>Reply Instructions for customer</b></td>
			<td class="smalltext2"  valign="top"><b>:</b></td>
			<td  valign="top">
				<textarea name="replyInstructions" rows="8" cols="45"><?php echo stripslashes(htmlentities($replyInstructions,ENT_QUOTES))?></textarea>
			</td>
		</tr>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td>
				<input type="submit" name="submit" value="Submit">
				<input type="button" name="submit" onClick="history.back()" value="Back">
				<input type="hidden" name="formSubmitted" value="1">
			</td>
		</tr>
	</table>
</form>