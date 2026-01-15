<!--<?php
	if($result	=	$orderObj->getOrderFeedbackDetails($orderId,$customerId))
	{
		$row				=	mysql_fetch_assoc($result);

		$otherId			=	$row['otherId'];
		$fileName			=	$row['fileName'];
		$fileExtension		=	$row['fileExtension'];
		$fileSize			=	$row['fileSize'];
		$feedback			=	stripslashes($row['feedback']);
		$feedbackAddedOn	=	showDate($row['addedOn']);
?>
<a name="feedback"></a>
<table width='98%' align='center' cellpadding='3' cellspacing='3' border='0'>
	<tr>
		<td colspan="3" class="text">FEEDBACK BY CUSTOMER</td>
	</tr>
	<tr>
		<td colspan="3" height="20"></td>
	</tr>
	<tr>
		<td class="smalltext2" width="20%" valign="top"><b>Feedback</b></td>
		<td class="smalltext2" width="2%"  valign="top"><b>:</b></td>
		<td valign="top" class="smalltext2">
			<?php echo nl2br($feedback);?>
		</td>
	</tr>
	<?php
		if(!empty($fileName) && empty($isDeleted))	
		{
	?>
	<tr>
		<td class="smalltext2" valign="top"><b>Feedback File</b></td>
		<td class="smalltext2" valign="top"><b>:</b></td>
		<td valign="top">
	<?php
		echo "<a href='".SITE_URL_EMPLOYEES."/other-download.php?ID=$otherId&t=FD'  class='link_style2'><b>".$fileName.".".$fileExtension."</b></a>";
		echo "<br><font class='smalltext2'>".getFileSize($fileSize)."</font></td></tr>";
	  }
	?>
	<tr>
		<td class="smalltext2"><b>Feedback Added On </b></td>
		<td class="smalltext2"><b>:</b></td>
		<td class="smalltext10">
			<b>
				<?php 
					echo $feedbackAddedOn;
				?>
			</b>
		</td>
	</tr>
</table>
<?php
	}
?>-->