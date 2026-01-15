<?php
	if($result	=	$orderObj->getOrderMessages($orderId,$customerId))
	{
?>
<br>
	<table width='100%' align='center' cellpadding='3' cellspacing='2' border='0'>
	<tr>
		<td colspan="2" class="textstyle1"><b>MESSAGES BETWEEN CUSTOMER AND EMPLOYEE</b></td>
	</tr>
	<tr>
		<td colspan="2" height="5"></td>
	</tr>
<?php
		while($row			=	mysql_fetch_assoc($result))
		{
			$t_messageId	=	$row['messageId'];
			$t_message		=	stripslashes($row['message']);
			$t_message		=	trim($t_message);
			$addedOn		=	showDate($row['addedOn']);
			$addedTime		=	$row['addedTime'];
			$messageBy		=	$row['messageBy'];
			$hasMessageFiles=	$row['hasMessageFiles'];
			$fileName		=	$row['fileName'];
			$fileExtension	=	$row['fileExtension'];
			$fileSize		=	$row['fileSize'];
			$emailUniqueCode=	$row['emailUniqueCode'];
			$emailSubject	=	stripslashes($row['emailSubject']);
			$readEmailText	=	"";

			if(empty($t_message) && !empty($emailSubject))
			{
				$t_message	=	$emailSubject;
			}
			
			
			$messageFirstFourChracter	=	substr($t_message,0,4);
			$messageFirstFiveChracter	=	substr($t_message,0,5);
			$displayEmpCustMsg			=	nl2br($t_message);
			if($messageFirstFourChracter==	"http" || $messageFirstFiveChracter == "https")
			{
				$spacePos=	strpos($t_message," ");
				if(!empty($spacePos))
				{
					$link				=	substr($t_message,0,$spacePos);
					$displayEmpCustMsg	=	substr($t_message,$spacePos+1);
					$displayEmpCustMsg	=	nl2br($displayEmpCustMsg);
				}
				else
				{
					$link				=	$t_message;
					$displayEmpCustMsg	=	"";
					
				}
				$link					=	"<a href='".$link."' target='_blank' class='link_style26'>$link</a>";
				$displayEmpCustMsg		=	$link."&nbsp;".$displayEmpCustMsg;
			}

			if($messageBy   ==  EMPLOYEES)
			{
				$employeeId		=	$row['employeeId'];
				$employeeName	=	$employeeObj->getEmployeeName($employeeId);
				echo "<tr><td class='textstyle2'><b>Message From ".$employeeName." to - ".$customerName." on $addedOn</b></td><td></tr>";

				if($readDateIp	=	$employeeObj->getFirstEmailReadTime($emailUniqueCode))
				{
					list($readDate,$readTime)	=	explode("|",$readDateIp);
					$readEmailText =	"&nbsp;(<font color='#ff0000'>Customer Read At - ".showDate($readDate)." EST at - ".showTimeFormat($readTime)." Hrs</font>)";
				}
			}
			elseif($messageBy   ==  CUSTOMERS)
			{
				echo "<tr><td class='textstyle2'><b>Message From ".$customerName." to Employee  on $addedOn</b></td></tr>";
			}
			echo "<tr><td class='textstyle1'>";
			
			echo $displayEmpCustMsg.$readEmailText;
			
			echo "</td></tr>";
			if($hasMessageFiles == 1 && empty($isDeleted))
			{
				
				if($isNewUploadingSystem == 1)
				{
					if($result1			=	$orderObj->getOrdereMessageFile($orderId,$t_messageId,3,7))
					{
						echo "<tr><td colspan='2' valign='top'><table width='100%' align='left'><tr><td width='12%' class='textstyle2' valign='top'><b>Uploaded File : </b></td><td valign='top'><table width='100%' align='left'>";

						while($row1			=	mysql_fetch_assoc($result1))
						{
							$fileId			=	$row1['fileId'];
							$fileName		=	stripslashes($row1['uploadingFileName']);
							$fileExtension	=	$row1['uploadingFileExt'];
							$fileSize		=	$row1['uploadingFileSize'];
							$imageOnServerPath	=	$row1['excatFileNameInServer'];
							$imageOnServerPath  =   stringReplace("/home/ieimpact", "", $imageOnServerPath);


							$base_fileId	=	base64_encode($fileId);
							
							$downLoadPath	=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
						?>
						<tr>
							 <td>
								<a class="link_style26" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download Message File" style="cursor:pointer;"><?php echo $fileName.".".$fileExtension;?></a>&nbsp;&nbsp;<font class='smalltext20'><?php echo getFileSize($fileSize);?></font>
							<?php
								if(in_array($fileExtension,$a_displayAnyImageOfType) && $messageBy   ==  EMPLOYEES && $fileSize <= "3145728")
								{
									list($imgWidth, $imgHeight, $type, $attr) = getimagesize($imageOnServerPath);

									if($imgWidth > 600 || $imgHeight > 400)
									{
										$imageWidth	=	"width='600'";
										$imageHeight=	"height='400'";
									}
									else
									{
										$imageWidth	=	"";
										$imageHeight=	"";
									}
							?>
							<br><img src="<?php echo SITE_URL_EMPLOYEES;?>/get-employee-message-image.php?memberId=<?php echo $customerId;?>&orderId=<?php echo $orderId;?>&messageId=<?php echo $t_messageId;?>&isNewSystem=1" border="0" title="<?php echo $fileName.".".$fileExtension;?>" <?php echo $imageWidth;?> <?php echo $imageHeight;?> onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');"  style="cursor:pointer">
							<?php
								}
							?>
							</td>
						</tr>
						<?php
						}
					?>
					</table></td></tr></table></td></tr>
					<?php
					}
				}
				else
				{
					echo "<tr><td colspan='2' class='textstyle2'><b>Uploaded File : </b><a href='".SITE_URL_EMPLOYEES."/download-message-files.php?ID=$t_messageId'  class='linkstyle6'><b>".$fileName.".".$fileExtension."</b></a>&nbsp;&nbsp;<font class='smalltext'>".getFileSize($fileSize)."</font>";
					if(in_array($fileExtension,$a_displayAnyImageOfType) && $messageBy   ==  EMPLOYEES  && $fileSize <= "3145728")
					{
						$messageFilePath			=	SITE_ROOT_FILES."/files/messages/".$t_messageId."_".$fileName.".".$fileExtension;

						list($imgWidth, $imgHeight, $type, $attr) = getimagesize($messageFilePath);

						if($imgWidth > 600 || $imgHeight > 400)
						{
							$imageWidth	=	"width='600'";
							$imageHeight=	"height='400'";
						}
						else
						{
							$imageWidth	=	"";
							$imageHeight=	"";
						}

				?>
						<br><a href="<?php echo SITE_URL_EMPLOYEES;?>/download-message-files.php?ID=<?php echo $t_messageId;?>"><img src="<?php echo SITE_URL_EMPLOYEES;?>/get-employee-message-image.php?memberId=<?php echo $customerId;?>&orderId=<?php echo $orderId;?>&messageId=<?php echo $t_messageId;?>&isNewSystem=0" border="0" title="<?php echo $fileName.".".$fileExtension;?>" <?php echo $imageWidth;?> <?php echo $imageHeight;?>></a>
				<?php
					}
					echo "</td></tr>";
				}
				
			}
			//echo "<tr><td colspan='2'><hr size='1' width='100%' color='#bebebe'></td></tr>";
		}
}
echo "</table>";
$query	=	"SELECT * FROM order_attention_messages WHERE memberId=$customerId AND orderId=$orderId ORDER BY date,time";
$result	=	dbQuery($query);
if(mysql_num_rows($result))
{
?>
<br>
	<table width='100%' align='center' cellpadding='3' cellspacing='2' border='0'>
	<tr>
		<td colspan="2" class="textstyle1"><b>ATTENTION MESSAGE TO CUSTOMER</b></td>
	</tr>
	<tr>
		<td colspan="2" height="5"></td>
	</tr>
	<?php
			$at	=	0;
			while($row	=	mysql_fetch_assoc($result))
			{
				$at++;
				$attentionId	 =	$row['messageId'];
				$attentionMessage=	stripslashes($row['message']);
				$attentionDate	 =	showDate($row['date']);
				$attentionBy	 =	$row['employeeId'];
				$emailUniqueCode =	$row['emailUniqueCode'];

				$readEmailText	 =	"";
				if($readDateIp	 =	$employeeObj->getFirstEmailReadTime($emailUniqueCode))
				{
					list($readDate,$readTime)	=	explode("|",$readDateIp);
					$readEmailText =	"&nbsp;(<font color='#ff0000'>Customer Read At - ".showDate($readDate)." EST at - ".showTimeFormat($readTime)." Hrs</font>)";
				}

				$attentionBy	 =	$employeeObj->getEmployeeFirstName($attentionBy);
			?>
			<tr>
				<td class="textstyle2" valign="top">
					<?php 
						echo "<b>Sent need attention message to - ".$customerName." by ".$attentionBy." on ".$attentionDate."</b>";
					?>
				</td>
			</tr>
			<tr>
					<td class="textstyle1" valign="top">
						<?php echo $attentionMessage.$readEmailText;?>
					</td>
			</tr>
			<?php
				}
			?>
	</table>
<?php
	}
	if($result	=	$orderObj->getOrderEmployeeMessages($orderId))
	{
?>
<br>
<table width='100%' align='center' cellpadding='3' cellspacing='2' border='0'>
	<tr>
		<td colspan="2" class="textstyle1"><b>EXISTING EMPLOYEE MESSAGES</b></td>
	</tr>
	<tr>
		<td colspan="2" height="5"></td>
	</tr>
<?php
		while($row			=	mysql_fetch_assoc($result))
		{
			$t_empMessageId			=	$row['messageId'];
			$t_empMessage			=	stripslashes($row['message']);
			$t_empMessageAddedOn	=	showDate($row['addedOn']);
			$t_empMessageBy			=	$row['messageBy'];
			$t_empMessageByName		=	$employeeObj->getEmployeeName($t_empMessageBy);
			
			echo "<tr><td colspan='2' class='textstyle1'>".nl2br($t_empMessage)."</td></tr>";
			echo "<tr><td class='textstyle2'><br>By : <b>$t_empMessageByName, on $t_empMessageAddedOn</b></td></tr>";
			echo "<tr><td colspan='2'><hr size='1' width='100%' color='#bebebe'></td></tr>";
		}
?>
</table>
<?php
	}
	if(isset($hasRatingExplanation) && !empty($hasRatingExplanation))
	{
		$query	=	"SELECT * FROM reply_on_orders_rates WHERE orderId=$orderId ORDER BY replyId DESC";
		$result	=	dbQuery($query);
		if(mysql_num_rows($result))
		{
?>
<br>
<table width='100%' align='center' cellpadding='3' cellspacing='2' border='0'>
	<tr>
		<td colspan="2" class="textstyle"><b>EXPLANATION ON CUSTOMER RATINGS</b></td>
	</tr>
	<tr>
		<td colspan="2" height="5"></td>
	</tr>
</tr>
<?php
			while($row	= mysql_fetch_assoc($result))
			{
				$t_empReplyId				=	$row['replyId'];
				$t_empReplyMessage			=	stripslashes($row['comment']);
				$t_empReplyAddedOn			=	showDate($row['addedOn']);
				$t_empReplyBy				=	$row['addedby'];
				$t_processQaEmployee		=	$row['processQaEmployee'];
				$t_empReplyByName			=	$employeeObj->getEmployeeName($t_empReplyBy);
				
				$addedbyEmployeeType		=	"";
				if($t_processQaEmployee		==	1)
				{
					$addedbyEmployeeType	=	"Processed Employee";
				}
				elseif($t_processQaEmployee	==	2)
				{
					$addedbyEmployeeType	=	"QA Done Employee";
				}
				elseif($t_processQaEmployee	==	3)
				{
					$addedbyEmployeeType	=	"Processed & QA Done Employee";
				}

				
				echo "<tr><td colspan='2' class='textstyle1'>".nl2br($t_empReplyMessage)."</td></tr>";
				echo "<tr><td class='textstyle2'><br><b>By : <b>$t_empReplyByName</b>, on $t_empReplyAddedOn</b></td></tr>";
				echo "<tr><td class='textstyle2'><br><b>Employee Type : <b>$addedbyEmployeeType</b></td></tr>";
				echo "<tr><td colspan='2'><hr size='1' width='100%' color='#bebebe'></td></tr>";
			}
		}
	}

	
	/*$query	=	"SELECT * FROM order_messages_sms WHERE orderId=$orderId AND parentId=0 AND isSendingSms=1 ORDER BY smsId";
	$result	=	dbQuery($query);
	if(mysql_num_rows($result))
	{
?>
<br>
<table width='100%' align='center' cellpadding='3' cellspacing='2' border='0'>
<tr>
	<td colspan="5" class="textstyle1"><b>SMS BETWEEN CUSTOMER AND EMPLOYEE</b></td>
</tr>
<tr>
	<td colspan="5" height="5"></td>
</tr>
<?php
		while($row	= mysql_fetch_assoc($result))
		{
			$t_smsReplyId				=	$row['smsId'];
			$t_smsCancelled				=	$row['cancelled'];
			$t_smsEmployeeId			=	$row['employeeId'];
			$t_sentMessageEstDate		=	showDate($row['sentMessageEstDate']);
			$t_sentMessageEstTime		=	$row['sentMessageEstTime'];
			$t_smsMesseSent				=	stripslashes($row['smsMesseSent']);
			$t_smsEmployeeByName		=	$employeeObj->getEmployeeName($t_smsEmployeeId);
			$t_smsMessageID				=	$row['smsMessageID'];

			echo "<tr><td colspan='5' class='textstyle1'>".nl2br($t_smsMesseSent)."</td></tr>";
			echo "<tr><td class='textstyle2' colspan='2'><b>By : <b>$t_smsEmployeeByName</b>, on $t_sentMessageEstDate(E.S.T.)</b></td></tr>";

			$query1	=	"SELECT * FROM order_messages_sms WHERE  parentId=$t_smsReplyId AND isReceivingSms=1 AND matchedMessageID='$t_smsMessageID' ORDER BY smsId";
			$result1=	dbQuery($query1);
			if(mysql_num_rows($result1))
			{
				while($row1	= mysql_fetch_assoc($result1))
				{
					$t_incomingMessageEstDate		=	showDate($row1['incomingMessageEstDate']);
					$t_sentMessageEstTime		=	$row1['sentMessageEstTime'];
					$t_incomingMessage			=	stripslashes($row1['incomingMessage']);
				?>
				<tr>
					<td width="4%" valign="top">
						<img src="<?php echo SITE_URL;?>/images/right.jpg" title="customer reply">
					</td>
					<td valign="top" class='textstyle1'>
						<?php echo nl2br($t_incomingMessage);?>
					</td>
				</tr>
				<tr>
					<td valign="top">
						&nbsp;
					</td>
					<td valign="top" class='textstyle2'>
						<b>By Customer on <?php echo $t_incomingMessageEstDate;?> (E.S.T.)</b>
					</td>
				</tr>
				<?php
				}
			}
							

			echo "<tr><td colspan='5'><hr size='1' width='100%' color='#bebebe'></td></tr>";

		}
	}*/

	include(SITE_ROOT_EMPLOYEES	. "/includes/show-customer-general-emails.php");
?>