<?php
	if($result	=	$orderObj->getReplyOrderDetails($orderId,$customerId))
	{
		$row						=	mysql_fetch_assoc($result);
		$replyId					=	$row['replyId'];
		$hasReplyOrderFile			=	$row['hasReplyOrderFile'];
		$replyOrderFileExt			=	$row['replyOrderFileExt'];
		$hasReplyPublicRecordFile	=	$row['hasReplyPublicRecordFile'];
		$replyPublicRecordFileExt	=	$row['replyPublicRecordFileExt'];
		$hasReplyMlsFile			=	$row['hasReplyMlsFile'];
		$replyMlsFileExt			=	$row['replyMlsFileExt'];
		$hasReplyMarketCondition	=	$row['hasReplyMarketCondition'];
		$replyMarketConditionExt	=	$row['replyMarketConditionExt'];
		$hasOtherFile				=	$row['hasOtherFile'];
		$otherFileExt				=	$row['otherFileExt'];

		$replyOrderFileName			=	$row['replyOrderFileName'];
		$replyPublicRecordFileName	=	$row['replyPublicRecordFileName'];
		$replyMlsFileName			=	$row['replyMlsFileName'];
		$replyMarketConditionFileName=	$row['replyMarketConditionFileName'];
		$otherFileName				=	$row['otherFileName'];

		$replyOrderFileSize			=	$row['replyOrderFileSize'];
		$replyPublicRecordSize		=	$row['replyPublicRecordSize'];
		$replyMlsFileSize			=	$row['replyMlsFileSize'];
		$replyOtherFileSize			=	$row['replyOtherFileSize'];
		$replyMarketConditionFileSize	=	$row['replyMarketConditionFileSize'];

		$replyInstructions			=	stripslashes($row['replyInstructions']);
		$commentsToQa				=   stripslashes($row['commentsToQa']);
		$timeSpentEmployee			=	$row['timeSpentEmployee'];

		$qaChecked					=	stripslashes($row['qaChecked']);
		$errorCorrected				=   stripslashes($row['errorCorrected']);
		$feedbackToEmployee			=	stripslashes($row['feedbackToEmployee']);
		$timeSpentQa				=	$row['timeSpentQa'];
		if(empty($timeSpentQa))
		{
			$timeSpentQa			=	"";
		}
		if(empty($timeSpentEmployee))
		{
			$timeSpentEmployee			=	"";
		}
		if($result2			=  $orderObj->getOrderQaRate($orderId))
		{
			$row2			=  mysql_fetch_assoc($result2);
			$rateByQa		=	$row2['rateByQa'];
			$qaRateMessage	=	stripslashes($row2['qaRateMessage']);
		}
		else
		{
			$rateByQa		=	"";
			$qaRateMessage	=	"";
		}
?>
<table width='98%' align='center' cellpadding='3' cellspacing='3' border='0'>
	<tr>
		<td colspan="3" class="text">REPLIED FILES BY EMPLOYEES</td>
	</tr>
	<?php
		if(!empty($isDeleted))
		{
	?>
	<tr>
		<td colspan="3" height="50" class="error">
			<b> FILES ARE DELETED</b>
		</td>
	</tr>
	<?php	
		}
		else
		{
	?>
	<tr>
		<td colspan="3" height="20"></td>
	</tr>
	<tr>
		<td class="smalltext2" width="25%" valign="top"><b>Completed Subject MLS & TAX PDF</b></td>
		<td class="smalltext2" width="2%"  valign="top"><b>:</b></td>
		<td valign="top" class="smalltext2">
			<?php 
				if($hasReplyOrderFile)
				{
					echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=OF&f=R'  class='link_style2'>".$replyOrderFileName.".".$replyOrderFileExt."</a>";
					
					echo "<br><font class='smalltext2'>".getFileSize($replyOrderFileSize)."</font>";
				}
				else
				{
					echo "<font color='red'>NO</font>";
				}
			?>
		</td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top"><b>Active Comps</b></td>
		<td class="smalltext2" valign="top"><b>:</b></td>
		<td valign="top" class="smalltext2">
			<?php 
				if($hasReplyPublicRecordFile)
				{
					echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=PF&f=R' class='link_style2'>".$replyPublicRecordFileName.".".$replyPublicRecordFileExt."</a>";
					
					echo "<br><font class='smalltext2'>".getFileSize($replyPublicRecordSize)."</font>";
				}
				else
				{
					echo "<font color='red'>NO</font>";
				}
			?>
		</td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top"><b>Sold Comps</b></td>
		<td class="smalltext2" valign="top"><b>:</b></td>
		<td valign="top" class="smalltext2">
			<?php 
				if($hasReplyMlsFile)
				{
					echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=MF&f=R' class='link_style2'>".$replyMlsFileName.".".$replyMlsFileExt."</a>";
					echo "<br><font class='smalltext2'>".getFileSize($replyMlsFileSize)."</font>";
				}
				else
				{
					echo "<font color='red'>NO</font>";
				}
			?>
		</td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top"><b>Summary</b></td></b></td>
		<td class="smalltext2" valign="top"><b>:</b></td>
		<td valign="top" class="smalltext2">
			<?php 
				if($hasOtherFile)
				{
					echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=OTF&f=R'  class='link_style2'>".$otherFileName.".".$otherFileExt."</a>";
					echo "<br><font class='smalltext2'>".getFileSize($replyOtherFileSize)."</font>";
				}
				else
				{
					echo "<font color='red'>NO</font>";
				}
			?>
		</td>
	</tr>
	<?php
		}			
	?>
	<tr>
		<td class="smalltext2" valign="top" width="20%"><b>Customer Instructions</b></td>
		<td class="smalltext2" valign="top" width="2%"><b>:</b></td>
		<td valign="top" class="smalltext2">
			<?php echo nl2br($replyInstructions);?>
		</td>
	</tr>
	<tr>
		<td colspan="3" height="30"></td>
	</tr>
	<?php
		if(!empty($rateGiven))
		{
	?>
	<tr>
		<td class="smalltext2" valign="top"><b>Rate Given By Customer</b></td>
		<td class="smalltext2" valign="top"><b>:</b></td>
		<td valign="top" class="heading1">
			<?php
				for($i=1;$i<=$rateGiven;$i++)
				{
					echo "<img src='".SITE_URL."/images/star.gif'  width=12 height=12'>";
				}
				echo $a_existingRatings[$rateGiven];
			?>
		</td>
	</tr>
	<?php
			if(!empty($memberRateMsg))
			{
	?>
	<tr>
		<td class="smalltext2" valign="top"><b>Rate Given By Customer</b></td>
		<td class="smalltext2" valign="top"><b>:</b></td>
		<td valign="top" class="smalltext2">
			<?php
				echo $memberRateMsg;
			?>
		</td>
	</tr>
	<?php
			}
		}
		if(!empty($commentsToQa))
		{
	?>
	<tr>
		<td class="smalltext2" valign="top"><b>Comments From Employee To QA</b></td>
		<td class="smalltext2" valign="top"><b>:</b></td>
		<td valign="top" class="smalltext2">
			<?php echo nl2br($commentsToQa);?>
		</td>
	</tr>
	<?php
		}
		if(!empty($timeSpentEmployee))
		{
	?>
	<tr>
		<td class="smalltext2" valign="top"><b>Time Spent By Employee</b></td>
		<td class="smalltext2" valign="top"><b>:</b></td>
		<td valign="top" class="smalltext2">
			<?php echo $timeSpentEmployee;?> Minitues.
		</td>
	</tr>
	<?php
		}
		if(!empty($qaChecked))
		{
	?>
	<tr>
		<td class="smalltext2" valign="top"><b>Works Done By QA</b></td>
		<td class="smalltext2" valign="top"><b>:</b></td>
		<td valign="top" class="smalltext2">
			<?php echo $qaChecked;?>
		</td>
	</tr>
	<?php
		}
		if(!empty($errorCorrected))
		{
	?>
	<tr>
		<td class="smalltext2" valign="top"><b>Errors Found And Corrected By QA</b></td>
		<td class="smalltext2" valign="top"><b>:</b></td>
		<td valign="top" class="smalltext2">
			<?php echo nl2br($errorCorrected);?>
		</td>
	</tr>
	<?php
		}
		if(!empty($feedbackToEmployee))
		{
	?>
	<tr>
		<td class="smalltext2" valign="top"><b>Feedback From QA</b></td>
		<td class="smalltext2" valign="top"><b>:</b></td>
		<td valign="top" class="smalltext2">
			<?php echo $feedbackToEmployee;?>
		</td>
	</tr>
	<?php	
		}
		if(!empty($timeSpentQa))
		{
	?>
	<tr>
		<td class="smalltext2" valign="top"><b>Time Spent By QA</b></td>
		<td class="smalltext2" valign="top"><b>:</b></td>
		<td valign="top" class="smalltext2">
			<?php echo $timeSpentQa;?> Minitues.
		</td>
	</tr>
	<?php		
		}
		if(!empty($rateByQa))
		{
	?>
	<tr>
		<td width="25%" class="smalltext2" valign="top"><b>Rate Employee Replies</b></td>
		<td width="1%" class="smalltext2" valign="top"><b>:</b></td>
		<td width="10%" class="smalltext2" valign="top">
			<?php
				for($i=1;$i<=$rateByQa;$i++)
				{
					echo "<img src='".SITE_URL."/images/star.gif'  width=12 height=12'>";
				}
			?>
		</td>
		<td class="title" valign="top"><b><?php echo $a_existingRatings[$rateByQa];?></b></td>
	</tr>
	<?php
		if(!empty($qaRateMessage))
		{
	?>
	<tr>
		<td colspan='4' height="2"></td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top"><b>Comments</b></td>
		<td class="smalltext2" valign="top"><b>:</b></td>
		<td colspan="2" valign="top" class="smalltext2">
			<?php
				echo $qaRateMessage;
			?>
		</td>
	</tr>
	<?php
		}
	}
	?>
</table>
<?php
	}				
?>