<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>
<script type="text/javascript">
	function replyCustomerGeneralMessage(generalMsgId,memberId)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/reply-customer-general-message.php?generalMsgId="+generalMsgId+"&memberId="+memberId;
		prop = "toolbar=no,scrollbars=yes,width=600,height=500,top=100,left=100";
		window.open(path,'',prop);
	}
</script>
<?php
	if($result	=	$orderObj->getCustomerUnRepliedGeneralMessages($customerId))
	{
?>
<br>
	<table width='100%' align='center' cellpadding='3' cellspacing='2' border='0'>
	<tr>
		<td colspan="2" class="textstyle1"><b>CUSTOMER EMAIL MESSAGES</b></td>
	</tr>
	<tr>
		<td colspan="2" height="5"></td>
	</tr>
	<tr bgcolor="#373737" height="20">
		<td class="smalltext8" width="5%">&nbsp;<b>Sr. No</b></td>
		<td class="smalltext8" width="19%"><b>Date</b></td>
		<td class="smalltext8" width="63%"><b>Message</b></td>
		<td>&nbsp;</td>
	</tr>
	<?php
		$i			=	0;
		while($row	=   mysqli_fetch_assoc($result))
		{
			$i++;
			$generalMsgId				=	$row['generalMsgId'];
			$memberId					=	$row['memberId'];
			$messageDate				=	$row['addedOn'];
			$messageTime				=	$row['addedtime'];
			$message					=	stripslashes($row['message']);
			$isUploadedFiles			=	$row['isUploadedFiles'];
			$message                    =   preg_replace( "/\r|\n/", "", $message);
			$messageDateTime			=	showDateTimeFormat($messageDate,$messageTime);

			$bgColor					=	"class='rwcolor1'";
			$backGroundColor			=	"#FFFFFF";
			if($i%2==0)
			{
				$bgColor				=   "class='rwcolor2'";
				$backGroundColor		=	"#DAEEF3";
			}

			list($agoY,$agoM,$agoD)		=	explode("-",$messageDate);
			$agoMonthDay				=	showFullTextDate($messageDate);
			$agoYearTime				=	$agoY." ".$messageTime;

			$messageOlderTime			=	getHoursBetweenDates($messageDate,$nowDateIndia,$messageTime,$nowTimeIndia);

		?>
			<tr>
				<td colspan="10" id="showHideGeneralMessage<?php echo $i?>">
					<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
						<tr <?php echo $bgColor;?> height="23">
							<td class="smalltext2" valign="top" width="5%"><?php echo $i;?>)</td>
							<td  valign="top" width="19%" class="smalltext16" valign="top">
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td class="smalltext16" valign="top">
											<?php echo $messageDateTime;?>
										</td>
									</tr>
								</table>
							</td>
							<td  valign="top" width="63%">
								<table width="100%" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td colspan="2" class="smalltext16">
											<?php 
												echo nl2br($message);
											?>
										</td>
									</tr>
									<?php
										if($isUploadedFiles == 1)	
										{
											
											if($a_files	=	$orderObj->getCustomerGeneralMessageEmailFiles($memberId,$generalMsgId))
											{
												$cn	=	0;
												foreach($a_files as $fileId=>$value)
												{
													$cn++;
													list($fileName,$size) = explode("|",$value);

													$base_fileId	=	base64_encode($fileId);

													$downLoadPath	=	SITE_URL_EMPLOYEES."/download-general-mesage-file.php?".$M_D_5_ID."=".$base_fileId;

													$fileSize	=	getFileSize($size);
										?>
										<tr>
											<td width="8%" class="smalltext22" valign="top">
												<?php echo $cn;?>)
											</td>
											<td valign="top">
												<a class="link_style12" onclick="downloadGeneralMessageFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $fileName;?></a>
												&nbsp;<?php echo $fileSize;?>
											</td>
										</tr>
										<tr>
											<td height="3"></td>
										</tr>
										<?php

												}
											}
											
										}
									?>
								</table>
							</td>
							<td valign="top" style="text-align:right;">
								<?php
									$markedUrl	=	SITE_URL_EMPLOYEES."/marked-reply-general-order-message.php?srNo=".$i."&msgId=";
								?>
								<a onclick="replyCustomerGeneralMessage(<?php echo $generalMsgId;?>,<?php echo $memberId;?>);" class='link_style12' style='cursor:pointer;'>Reply</a>
								<font class="smalltext2"> | </font>

								<a onclick="replyAllMessageForcefully(<?php echo $generalMsgId;?>,<?php echo $memberId;?>,3)" class="greenLink" style='cursor:pointer;' title='Action Taken'>Action Taken</a>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		<?php
			}
		?>
	</table>
<?php
	}
?>