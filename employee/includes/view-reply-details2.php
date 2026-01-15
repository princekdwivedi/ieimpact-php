<?php
	$a_attachmentPath					=	array();
	$a_attachmentType					=	array();
	$a_attachmentName					=	array();
	$isRepliedWithNewSystem				=	0;
	$totalAmountEmailFileSize			=	0;
	$displayRepliedFilesByEmployee		=	false;

	if($result							=	$orderObj->getReplyOrderDetails($orderId,$customerId))
	{
		$displayRepliedFilesByEmployee	=	true;
		$row							=	mysqli_fetch_assoc($result);
		$replyId						=	$row['replyId'];
		$hasReplyOrderFile				=	$row['hasReplyOrderFile'];		
		$hasReplyMlsFile				=	$row['hasReplyMlsFile'];
		$hasReplyMarketCondition		=	$row['hasReplyMarketCondition'];
		$hasOtherFile					=	$row['hasOtherFile'];		
		$hasCompletedPdfFile			=	$row['hasCompletedPdfFile'];
		$hasReplyPublicRecordFile		=	$row['hasReplyPublicRecordFile'];		
		$replyInstructions				=	stripslashes($row['replyInstructions']);
		$commentsToQa					=   stripslashes($row['commentsToQa']);
		$timeSpentEmployee				=	$calculateCustomerAverageTime = $row['timeSpentEmployee'];
		$qaChecked						=	stripslashes($row['qaChecked']);
		$errorCorrected					=   stripslashes($row['errorCorrected']);
		$feedbackToEmployee				=	stripslashes($row['feedbackToEmployee']);
		$timeSpentQa					=	$row['timeSpentQa'];
		$isQaAccepted					=	$row['isQaAccepted'];
		$qaAcceptedBy					=	$row['qaAcceptedBy'];
		$qaAcceptedDate 				=	$row['qaAcceptedDate'];
		$qaAcceptedTime					=	$row['qaAcceptedTime'];
		$isRepliedWithNewSystem			=	$row['isRepliedWithNewSystem'];
		$emailUniqueCode				=	$row['emailUniqueCode'];
		$orderCompletedTime				=	$row['orderCompletedTime'];
		$numberOfCompsFilled			=	$row['numberOfCompsFilled'];
		$orderFileRepliedOn				=	$row['orderCompletedOn'];
		if(empty($numberOfCompsFilled))
		{
			$numberOfCompsFilled		=	"";
		}
		$orderInteralNotes				=	stripslashes($row['orderInteralNotes']);
		$isReadOrderRepliedEmail		=	"";

		$qaAcceptedByName				=	"";
		if($isSetedOrderField			==	1)
		{
			$qaAcceptedByName			=	$qaDoneByName;
		}
		else
		{
			if($isQaAccepted			==	1   &&   !empty($qaAcceptedBy))
			{
				$qaAcceptedByName		=	$acceeptedByName;
			}
		}

		if(!empty($emailUniqueCode)){
			if($readDateOrderReplyIp		=	$employeeObj->getFirstEmailReadTime($emailUniqueCode))
			{
				list($readDate,$readTime)	=	explode("|",$readDateOrderReplyIp);
				$isReadOrderRepliedEmail	=	"<br><br>(<font color='#ff0000'>Customer Read Email At - ".showDate($readDate)." EST at - ".showTimeFormat($readTime)." Hrs</font>)";
			}
		}		


		if(empty($timeSpentQa))
		{
			$timeSpentQa			=	"";
		}
		if(empty($timeSpentEmployee))
		{
			$timeSpentEmployee		=	"";
		}
		if($result2			=  $orderObj->getOrderQaRate($orderId))
		{
			$row2			=  mysqli_fetch_assoc($result2);
			$rateByQa		=	$row2['rateByQa'];
			$qaRateMessage	=	stripslashes($row2['qaRateMessage']);
		}
		else
		{
			$rateByQa		=	"";
			$qaRateMessage	=	"";
		}
		$showFilesNameInEmail		 =	"";
		$showFilesNameInEmail		.=	"<table width='99%' align='center' border='0' cellpadding='2' cellspacing='2' style='border:2px solid #e4e4e4;'>";
		$showFilesNameInEmail		.=	"<tr><td colspan='3' align='left'><font style='font-size:11px;font-weight:bold;color:#6E6E6E;'>COMPLETED FILES OF THIS ORDER</font></td></tr>";

		$baseConvertUniqueEmailCode  = base64_encode($memberUniqueEmailCode);

		$all_replied_files		=	array();
		$display_replied_files	=	array();
		$email_attachment_files =   array();

		$query1				=	"SELECT * FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND orderId=$orderId AND replyOrderId=$replyId AND uploadingFor=2 AND isDeleted=0";
		$result1			=	dbQuery($query1);
		if(mysqli_num_rows($result1))
		{
			while($row1						=	mysqli_fetch_assoc($result1)){
				$fileId						=	$row1['fileId'];
				$uploadingFileName			=	stripslashes($row1['uploadingFileName']);
				$uploadingFileExt			=	stripslashes($row1['uploadingFileExt']);
				$uploadingFileSize			=	$row1['uploadingFileSize'];
				$uploadingType				=   $row1['uploadingType'];

				$downloadPath				=	$row1['excatFileNameInServer'];
				$mimeTypeField			    =	$row1['uploadingFileType'];
			
				$isViewedDownloaded			=	$row1['isViewedDownloaded'];
				$viewedDownloadedEstDate	=	$row1['viewedDownloadedEstDate'];
				$viewedDownloadedEstTime	=	$row1['viewedDownloadedEstTime'];


				$all_replied_files[$uploadingType] = $fileId."|".$uploadingFileName.".".$uploadingFileExt."|".$uploadingFileSize;

				$display_replied_files[$uploadingType]	=	$fileId."|".$uploadingFileName.".".$uploadingFileExt."|".$uploadingFileSize."|".$isViewedDownloaded."|".$viewedDownloadedEstDate."|".$viewedDownloadedEstTime;

				$email_attachment_files[$uploadingType] = $downloadPath."|".$uploadingFileExt."|".$uploadingFileName."|".$mimeTypeField."|".$uploadingFileSize;
			}
		}

						

		if(!empty($hasReplyOrderFile))
		{
			if(array_key_exists(1,$all_replied_files))	
			{
				$filenameSize		=	$all_replied_files[1];
			
				list($replyFileID,$fileRepliedToCustomer,$fileSizeRepliedToCustomer)	=	explode("|",$filenameSize);

				$t_replieddFileToustomer =	stringReplace("Upload ", "", $replieddFileToustomer);

				$base_fileId			=	base64_encode($replyFileID);

				$downLoadPath			=	SITE_URL_MEMBERS."/download-multiple-file.php?suf=".$baseConvertUniqueEmailCode."&".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;

				
				$showFilesNameInEmail	.=	"<tr><td width='35%' valign='top'  align='left'><font style='font-size:10px;color:#4d4d4d;'>".$t_replieddFileToustomer."</font></td><td width='2%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>:</font></td><td valign='top' align='left'><a href='".$downLoadPath."' target='_blank'><font style='font-size:12px;color:#0082bf;'>".$fileRepliedToCustomer."</font></a>".getSizeNoBracket($fileSizeRepliedToCustomer)."</td></tr>";

				if($fileSizeRepliedToCustomer > 0)
				{
					if(array_key_exists(1,$email_attachment_files))						
					{
						$returnFileDetails		=	$email_attachment_files[1];

						list($path,$ext,$fileName,$mimeType,$fileSize)=	explode("|",$returnFileDetails);
						
						$a_attachmentPath[]			=	$path;
						$a_attachmentType[]			=	$mimeType;
						$a_attachmentName[]			=	$fileRepliedToCustomer;

						$totalAmountEmailFileSize	=	$totalAmountEmailFileSize+$fileSize;
					}
				}
			}			
			
		}

		if(!empty($hasCompletedPdfFile))
		{
			if(array_key_exists(7,$all_replied_files))	
			{
				$filenameSize		=	$all_replied_files[7];

				list($replyFileID,$fileRepliedToCustomer,$fileSizeRepliedToCustomer)	=	explode("|",$filenameSize);

				$base_fileId			=	base64_encode($replyFileID);

				$downLoadPath			=	SITE_URL_MEMBERS."/download-multiple-file.php?suf=".$baseConvertUniqueEmailCode."&".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;

				$showFilesNameInEmail	.=	"<tr><td width='35%' valign='top'  align='left'><font style='font-size:10px;color:#4d4d4d;'>Completed Report PDF File for Reference</font></td><td width='2%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>:</font></td><td valign='top' align='left'><a href='".$downLoadPath."' target='_blank'><font style='font-size:12px;color:#0082bf;'>".$fileRepliedToCustomer."</font></a>".getSizeNoBracket($fileSizeRepliedToCustomer)."</td></tr>";
			}		
			
		}


		if(!empty($hasReplyPublicRecordFile))
		{
			
			if(array_key_exists(2,$all_replied_files))	
			{
				$filenameSize		=	$all_replied_files[2];
				list($replyFileID,$replyPublicRecordFileName,$replyPublicRecordSize)	=	explode("|",$filenameSize);

				$base_fileId			=	base64_encode($replyFileID);

				$downLoadPath			=	SITE_URL_MEMBERS."/download-multiple-file.php?suf=".$baseConvertUniqueEmailCode."&".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;


				$showFilesNameInEmail	.=	"<tr><td width='35%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>Public Records File</font></td><td width='2%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>:</font></td><td valign='top' align='left'><a href='".$downLoadPath."' target='_blank'><font style='font-size:12px;color:#0082bf;'>".$replyPublicRecordFileName."</font></a>".getSizeNoBracket($replyPublicRecordSize)."</td></tr>";

				if($replyPublicRecordSize > 0)
				{
					if(array_key_exists(2,$email_attachment_files))						
					{
						$returnFileDetails		=	$email_attachment_files[2];

						list($path,$ext,$fileName,$mimeType,$fileSize)=	explode("|",$returnFileDetails);
						
						$a_attachmentPath[]			=	$path;
						$a_attachmentType[]			=	$mimeType;
						$a_attachmentName[]			=	$replyPublicRecordFileName;

						$totalAmountEmailFileSize	=	$totalAmountEmailFileSize+$fileSize;
					}
				}
			}		
			
		}
		if(!empty($hasReplyMlsFile))
		{
			
			if(array_key_exists(3,$all_replied_files))	
			{
				$filenameSize		=	$all_replied_files[3];
				list($replyFileID,$replyMlsFileName,$replyMlsFileSize)	=	explode("|",$filenameSize);

				$base_fileId			=	base64_encode($replyFileID);

				$downLoadPath			=	SITE_URL_MEMBERS."/download-multiple-file.php?suf=".$baseConvertUniqueEmailCode."&".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;

				$showFilesNameInEmail	.=	"<tr><td width='35%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>Plat Map</font></td><td width='2%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>:</font></td><td valign='top' align='left'><a href='".$downLoadPath."' target='_blank'><font style='font-size:12px;color:#0082bf;'>".$replyMlsFileName."</font></a>".getSizeNoBracket($replyMlsFileSize)."</td></tr>";

				if($replyMlsFileSize > 0)
				{
					if(array_key_exists(3,$email_attachment_files))						
					{
						$returnFileDetails		=	$email_attachment_files[3];

						list($path,$ext,$fileName,$mimeType,$fileSize)=	explode("|",$returnFileDetails);
						
						$a_attachmentPath[]			=	$path;
						$a_attachmentType[]			=	$mimeType;
						$a_attachmentName[]			=	$replyMlsFileName;

						$totalAmountEmailFileSize	=	$totalAmountEmailFileSize+$fileSize;
					}
				}
			}		
			
		}
		if(!empty($hasOtherFile))
		{
			if(array_key_exists(6,$all_replied_files))	
			{
				$filenameSize		=	$all_replied_files[6];
				list($replyFileID,$replyOtherFileName,$replyOtherFileSize)	=	explode("|",$filenameSize);

				$base_fileId			=	base64_encode($replyFileID);

				$downLoadPath			=	SITE_URL_MEMBERS."/download-multiple-file.php?suf=".$baseConvertUniqueEmailCode."&".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;


				$showFilesNameInEmail	.=	"<tr><td width='35%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>Reply Other File</font></td><td width='2%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>:</font></td><td valign='top' align='left'><a href='".$downLoadPath."' target='_blank'><font style='font-size:12px;color:#0082bf;'>".$replyOtherFileName."</font></a>".getSizeNoBracket($replyOtherFileSize)."</td></tr>";

				if($replyOtherFileSize > 0)
				{
					if(array_key_exists(6,$email_attachment_files))						
					{
						$returnFileDetails		=	$email_attachment_files[6];

						list($path,$ext,$fileName,$mimeType,$fileSize)=	explode("|",$returnFileDetails);
						
						$a_attachmentPath[]			=	$path;
						$a_attachmentType[]			=	$mimeType;
						$a_attachmentName[]			=	$replyOtherFileName;

						$totalAmountEmailFileSize	=	$totalAmountEmailFileSize+$fileSize;
					}
				}
			}			
			
		}

		$showFilesNameInEmail		.=	"<tr><td colspan='3' height='15'></td></tr><tr><td colspan='3' align='left'><font style='font-size:10px;font-weight:bold;color:#333333;'>Note: Anyone with these links can download these files. Do not forward this email to anybody.</font></td></tr>";

		$showFilesNameInEmail	.=	"</table>";

		if($orderFileRepliedOn != "0000-00-00" && $orderCompletedTime != "" && $orderCompletedTime != "00:00:00")
		{
			
			$completedMin		=	timeBetweenTwoTimes($orderPlacedDate,$orderAddedTime,$orderFileRepliedOn,$orderCompletedTime);

			$completedMin		 =	getHours($completedMin);

			$expctDelvText		 =	"&nbsp;&nbsp;TAT : ".$completedMin." Hrs Taken";
		}
		else
		{
			$expctDelvText		=	"";
		}
	}

	if($displayRepliedFilesByEmployee == true)
	{
		
?>
<table width="98%" align="center" border="0" cellpadding="3" cellspacing="2">
	<tr>
		<td colspan="3" class="smalltext24">
			<b><font color='#ff0000;'>COMPLETED FILES OF THIS ORDER</font></b>
		</td>
	</tr>
	<?php
		if($status	==	2  || $status	==	5 || $status	==	6)
		{
	?>
	<tr>
		<td class="smalltext23" width="25%">Completed On</td>
		<td class="smalltext23" width="1%">:</td>
		<td class="smalltext24">
			<?php
				if($isAddedTatTiming		==	1)
				{
					$expctDelvText			=	"&nbsp;&nbsp;TAT : ".getHours($orderCompletedTat);
					$onTimeText				=	"<b>Ontime</b>";
					if($isCompletedOnTime	==	2)
					{
						$onTimeText			=	"<font color='#ff0000;'>Late - </font>(".getHours($beforeAfterTimingMin).")";
					}
				
					echo showDateTimeFormat($orderFileRepliedOn,$orderCompletedTime)." ".$expctDelvText." ".$onTimeText.$postAuditText;
				}
				else
				{
					echo showDateTimeFormat($orderFileRepliedOn,$orderCompletedTime)." ".$expctDelvText.$postAuditText;
				}
				
			?>
		</td>
	</tr>
	<tr>
		<td class="smalltext23" valign="top">Accepted By</td>
		<td class="smalltext23" valign="top">:</td>
		<td valign="top" class="smalltext24">
			<?php echo $acceptedText;?>
		</td>
	</tr>
	<tr>
		<td class="smalltext23" valign="top">QA Done By</td>
		<td class="smalltext23" valign="top">:</td>
		<td valign="top" class="smalltext24">
			<?php echo $qaDoneByText;?>
		</td>
	</tr>
	<?php
		}
		else
		{
	?>
	<tr>
		<td class="smalltext23" valign="top">Processed By</td>
		<td class="smalltext23" valign="top">:</td>
		<td valign="top" class="smalltext24">
			<?php echo $acceptedText;?>
		</td>
	</tr>
	<?php
		if(!empty($timeSpentEmployee))	
		{
	?>
	<tr>
		<td class="smalltext23" valign="top">Time Spent in Process</td>
		<td class="smalltext23" valign="top">:</td>
		<td valign="top" class="smalltext24">
			<?php echo $timeSpentEmployee;?> Minitues.
		</td>
	</tr>
	<?php
		}
		if($isQaAccepted			==	1)
		{				
			$qaAcceptedByName		=	"<a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&orderOf=$qaAcceptedBy&showingEmployeeOrder=1&displayTypeCompleted=1' class='link_style32'>".$qaAcceptedByName."</a> On-".showDate($qaAcceptedDate)." at ".showTimeFormat($qaAcceptedTime)." Hrs IST";
	?>
	<tr>
		<td class="smalltext23" valign="top">Qa Accepted By</td>
		<td class="smalltext23" valign="top">:</td>
		<td valign="top" class="smalltext24">
			<?php echo $qaAcceptedByName;?>
		</td>
	</tr>
	<?php
		}
	  }

	  $customerOrderViewedText     =  "";
	  if($isCustomerViewedTheOrder == "viewed")
	  {
		 $customerOrderViewedText  =	"&nbsp;<font color='#ff0000;'>Customer viewed completed order at ".showDate($customerViewedOrderDate)." ".showTimeShortFormat($customerViewedOrderTime)." EST ".showBeforeDays($customerViewedOrderDate,$customerViewedOrderTime,1)."</font>";
	  }
	?>
	<tr>
		<td colspan="3" class="smalltext24"><b>:: COMPLETED FILES OF THIS ORDER ::</b><?php echo $customerOrderViewedText;?></td>
	</tr>
	<?php
		if(!empty($isDeleted))
		{
	?>
			<tr>
				<td colspan="3" class="smalltext24">
					<b><font color='#ff0000;'>COMPLETED FILES ARE DELETED</font></b>
				</td>
			</tr>
	<?php
		}
		else
		{
	?>
	<tr>
		<td class="smalltext23">Number of Comps filled</td>
		<td class="smalltext23">:</td>
		<td valign="top" class="smalltext23">
			<b><?php  echo $numberOfCompsFilled;?></b>
		</td>
	</tr>
	<tr>
		<td class="smalltext23" valign="top">Internal Employee Notes</td>
		<td class="smalltext23" valign="top">:</td>
		<td valign="top" class="error" valign="top">
			<?php  echo $orderInteralNotes;?>
		</td>
	</tr>
	<tr>
		<td class="smalltext23">Report PDF File for Reference</td>
		<td class="smalltext23">:</td>
		<td valign="top">
			<?php 
				if($hasCompletedPdfFile)
				{	
						if(array_key_exists(7,$display_replied_files))
						{							
							$filenameSize		=	$display_replied_files[7];
							list($fileId,$uploadingFileName,$uploadingFileSize,$isViewedDownloaded,$viewedDownloadedEstDate,$viewedDownloadedEstTime)	=	explode("|",$filenameSize);

							$base_fileId	    =	base64_encode($fileId);

							$isDownloadText		=	"";
							if(!empty($isViewedDownloaded) && $isViewedDownloaded == "downloaded"){
								$isDownloadText	=	"&nbsp;<font color='#ff0000;'>Customer ".$isViewedDownloaded." this file at ".showDate($viewedDownloadedEstDate)." ".showTimeShortFormat($viewedDownloadedEstTime)." EST ".showBeforeDays($viewedDownloadedEstDate,$viewedDownloadedEstTime,1)."</font>";
							}


							$downLoadPath		=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
					?>
					<a class="link_style32" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName;?></a><font class='smalltext20'>
					<?php echo getFileSize($uploadingFileSize).$isDownloadText;
					}
					
				}
				else
				{
					echo "<font color='red'>NO</font>";
				}
			?>
		</td>
	</tr>
	<tr>
		<td class="smalltext23" valign="top" align="left"><?php echo stringReplace("Uploaded","",$replieddFileToustomer);?></td>
		<td class="smalltext23" valign="top">:</td>
		<td valign="top" align="left">
			<?php 
				if($hasReplyOrderFile)
				{
					    $repliedFName			   =  "";
					
						if(array_key_exists(1,$display_replied_files))
						{							
							$filenameSize		=	$display_replied_files[1];
							list($fileId,$uploadingFileName,$uploadingFileSize,$isViewedDownloaded,$viewedDownloadedEstDate,$viewedDownloadedEstTime)	=	explode("|",$filenameSize);

							$base_fileId	    =	base64_encode($fileId);

							$isDownloadText				=	"";
							if(!empty($isViewedDownloaded) && $isViewedDownloaded == "downloaded"){
								$isDownloadText			=	"&nbsp;<font color='#ff0000;'>Customer ".$isViewedDownloaded." this file at ".showDate($viewedDownloadedEstDate)." ".showTimeShortFormat($viewedDownloadedEstTime)." EST ".showBeforeDays($viewedDownloadedEstDate,$viewedDownloadedEstTime,1)."</font>";
							}

							$repliedFName		=	$uploadingFileName;

							$downLoadPath		=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
					?>
					<a class="link_style32" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName;?></a><font class='smalltext20'>
					<?php echo getFileSize($uploadingFileSize).$isDownloadText;
					}
					
					if(!in_array($repliedFName,$a_customerOrderTemplateFiles))
					{
						echo "&nbsp;(<font class='error'>Completed ".stringReplace("Upload Reply","",$replieddFileToustomer)." file name not matching with customer ".stringReplace("Upload Reply","",$replieddFileToustomer)." file name.</font>)";
					}
				}
				else
				{
					echo "<font color='red'>NO</font>";
				}
			?>
		</td>
	</tr>
	<tr>
		<td class="smalltext23" valign="top" align="left">Public Records File</td>
		<td class="smalltext23" valign="top">:</td>
		<td valign="top" align="left">
			<?php 
				if($hasReplyPublicRecordFile)
				{
					
						if(array_key_exists(2,$display_replied_files))
						{							
							$filenameSize		=	$display_replied_files[2];
							list($fileId,$uploadingFileName,$uploadingFileSize,$isViewedDownloaded,$viewedDownloadedEstDate,$viewedDownloadedEstTime)	=	explode("|",$filenameSize);

							$base_fileId	    =	base64_encode($fileId);

							$isDownloadText				=	"";
							if(!empty($isViewedDownloaded) && $isViewedDownloaded == "downloaded"){
								$isDownloadText			=	"&nbsp;<font color='#ff0000;'>Customer ".$isViewedDownloaded." this file at ".showDate($viewedDownloadedEstDate)." ".showTimeShortFormat($viewedDownloadedEstTime)." EST ".showBeforeDays($viewedDownloadedEstDate,$viewedDownloadedEstTime,1)."</font>";
							}

							$downLoadPath		=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
					?>
					<a class="link_style32" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName;?></a><font class='smalltext20'>
					<?php echo getFileSize($uploadingFileSize).$isDownloadText;
					}
					
				}
				else
				{
					echo "<font color='red'>NO</font>";
				}
			?>
		</td>
	</tr>
	<tr>
		<td class="smalltext23" valign="top" align="left">Plat Map</td>
		<td class="smalltext23" valign="top">:</td>
		<td valign="top" align="left">
			<?php 
				if($hasReplyMlsFile)
				{
					
						if(array_key_exists(3,$display_replied_files))
						{							
							$filenameSize		=	$display_replied_files[3];
							list($fileId,$uploadingFileName,$uploadingFileSize,$isViewedDownloaded,$viewedDownloadedEstDate,$viewedDownloadedEstTime)	=	explode("|",$filenameSize);

							$base_fileId	    =	base64_encode($fileId);

							$isDownloadText				=	"";
							if(!empty($isViewedDownloaded) && $isViewedDownloaded == "downloaded"){
								$isDownloadText			=	"&nbsp;<font color='#ff0000;'>Customer ".$isViewedDownloaded." this file at ".showDate($viewedDownloadedEstDate)." ".showTimeShortFormat($viewedDownloadedEstTime)." EST ".showBeforeDays($viewedDownloadedEstDate,$viewedDownloadedEstTime,1)."</font>";
							}

							$downLoadPath		=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
					?>
					<a class="link_style32" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName;?></a><font class='smalltext20'>
					<?php echo getFileSize($uploadingFileSize).$isDownloadText;
					}
					
				}
				else
				{
					echo "<font color='red'>NO</font>";
				}
			?>
		</td>
	</tr>
	<tr>
		<td class="smalltext23" valign="top" align="left">Reply Other File</td></b></td>
		<td class="smalltext23" valign="top">:</td>
		<td valign="top" class="smalltext2" align="left">
			<?php 
				if($hasOtherFile)
				{
					
						if(array_key_exists(6,$display_replied_files))
						{							
							$filenameSize		=	$display_replied_files[6];
							list($fileId,$uploadingFileName,$uploadingFileSize,$isViewedDownloaded,$viewedDownloadedEstDate,$viewedDownloadedEstTime)	=	explode("|",$filenameSize);

							$base_fileId	    =	base64_encode($fileId);

							$isDownloadText				=	"";
							if(!empty($isViewedDownloaded) && $isViewedDownloaded == "downloaded"){
								$isDownloadText			=	"&nbsp;<font color='#ff0000;'>Customer ".$isViewedDownloaded." this file at ".showDate($viewedDownloadedEstDate)." ".showTimeShortFormat($viewedDownloadedEstTime)." EST ".showBeforeDays($viewedDownloadedEstDate,$viewedDownloadedEstTime,1)."</font>";
							}

							$downLoadPath		=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
					?>
					<a class="link_style32" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName;?></a><font class='smalltext20'>
					<?php echo getFileSize($uploadingFileSize).$isDownloadText;
					}
					
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
		<td class="smalltext23" valign="top" align="left">Customer Instructions</td>
		<td class="smalltext23" valign="top">:</td>
		<td valign="top" align="left">
			<div style='overflow:auto;width:800px;scrollbars:no'>
				<table width="100%">
					<tr>
						<td class="smalltext2">
							<?php echo nl2br($replyInstructions).$isReadOrderRepliedEmail;?>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<?php
		if(!empty($rateGiven) && $isRateCountingEmployeeSide == "yes")
		{
	?>
	<tr>
		<td class="smalltext23" valign="top" align="left">Rate Given By Customer</td>
		<td class="smalltext23" valign="top">:</td>
		<td valign="top" class="smalltext24" align="left">
			<?php
				for($i=1;$i<=$rateGiven;$i++)
				{
					echo "<img src='".SITE_URL."/images/star.gif'  width=12 height=12'>";
				}
				echo $a_existingRatings[$rateGiven];

				if(empty($isRepliedToRatingMessage)){
			?>
			<a onclick="replyAllMessageForcefully(<?php echo $orderId;?>,<?php echo $customerId;?>,2)" class="greenLink" style='cursor:pointer;' title='Action Taken'>Action Taken</a>
			<?php
				}

				
			?>
		</td>
	</tr>
	<?php
			if(!empty($memberRateMsg))
			{
	?>
	<tr>
		<td class="smalltext23" valign="top" align="left">Rate Note By Customer</td>
		<td class="smalltext23" valign="top">:</td>
		<td valign="top" class="smalltext24" align="left">
			<?php
				echo $memberRateMsg;
			?>
		</td>
	</tr>
	<?php
		}
		
		if($hasUploadedRatingFile == 1 && !empty($rateGiven))
		{				
	?>
	<tr>
		<td class="smalltext23" valign="top" align="left">Rate File By Customer</td>
		<td class="smalltext23" valign="top">:</td>
		<td valign="top" class="smalltext24" align="left">
			<?php
				
					if($result	=	$orderObj->getMultipleOrderFiles($orderId,$customerId,4,7))
					{
						$row		=	mysqli_fetch_assoc($result);
						$fileId					=	$row['fileId'];
						$uploadingFileName		=	stripslashes($row['uploadingFileName']);
						$uploadingFileExt		=	stripslashes($row['uploadingFileExt']);
						$base_fileId			=	base64_encode($fileId);
						$uploadingFileSize		=	$row['uploadingFileSize'];

						$downLoadPath			=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
				?>
				<a class="link_style32" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a>&nbsp;<font class='smalltext20'>
				<?php echo getFileSize($uploadingFileSize);?></font>
				<?php
					
				}
				
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
		<td class="smalltext23" valign="top" align="left">Comments From Employee To QA</td>
		<td class="smalltext23" valign="top">:</td>
		<td valign="top" class="smalltext2" align="left">
			<?php echo nl2br($commentsToQa);?>
		</td>
	</tr>
	<?php
		}
		if(!empty($timeSpentEmployee))
		{
	?>
	<tr>
		<td class="smalltext23" valign="top" align="left">Time Spent By Employee</td>
		<td class="smalltext23" valign="top">:</td>
		<td valign="top" class="smalltext24" align="left">
			<?php echo $timeSpentEmployee;?> Minitues.
		</td>
	</tr>
	<?php
		}
		if(!empty($qaChecked))
		{
	?>
	<tr>
		<td class="smalltext23" valign="top" align="left">Works Done By QA</td>
		<td class="smalltext23" valign="top">:</td>
		<td valign="top" class="smalltext24" align="left">
			<?php echo $qaChecked;?>
		</td>
	</tr>
	<?php
		}
		if(!empty($errorCorrected))
		{
	?>
	<tr>
		<td class="smalltext23" valign="top" align="left">Errors Found And Corrected By QA</td>
		<td class="smalltext23" valign="top">:</td>
		<td valign="top" class="smalltext24" align="left">
			<?php echo nl2br($errorCorrected);?>
		</td>
	</tr>
	<?php
		}
		if(!empty($feedbackToEmployee))
		{
	?>
	<tr>
		<td class="smalltext23" valign="top" align="left">Feedback From QA></td>
		<td class="smalltext23" valign="top">:</td>
		<td valign="top" class="smalltext24" align="left">
			<?php echo $feedbackToEmployee;?>
		</td>
	</tr>
	<?php	
		}
		if(!empty($timeSpentQa))
		{
	?>
	<tr>
		<td class="smalltext23" valign="top" align="left">Time Spent By QA</td>
		<td class="smalltext23" valign="top">:</td>
		<td valign="top" class="smalltext24" align="left">
			<?php echo $timeSpentQa;?> Minitues.
		</td>
	</tr>
	<?php		
		}
		
		if(!empty($qaRateMessage))
		{
	?>
	<tr>
		<td class="smalltext23" valign="top" align="left">Comments From QA</td>
		<td class="smalltext23" valign="top">:</td>
		<td valign="top" class="smalltext24" align="left">
			<?php
				echo $qaRateMessage;
			?>
		</td>
	</tr>
	<?php
		}
		if($a_markedEmployeeChecklistQa	=	$orderObj->getProcesedEmployeeChecklistMarked($orderId))
		{
			$a_arrayCheckedList	=	array("1"=>"Yes","2"=>"No","3"=>"N/A");
			$a_existingChecklist=	$orderObj->getAllQaCheckListWithAnswer();
		?>
	<tr>
		<td colspan='3' height="2">
			<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
				<tr>
					<td colspan="5" class="smalltext23">
						<b>Marked Reply Employee checklist</b>
					</td>
				</tr>
				<tr>
					<td height="5"></td>
				</tr>
				<?php
					$countMarkedCheklist	=0;
					foreach($a_markedEmployeeChecklistQa as $k=>$v)
					{
						$countMarkedCheklist++;

						$checkedChecklistText	=	$a_existingChecklist[$k];
						list($checkedChecklistText,$correctAns)= explode("<=>",$checkedChecklistText);

						$fontColor		=	"#333333";
						if(!empty($correctAns) && $correctAns	!= $v)
						{
							$fontColor	=	"#ff0000";
						}

						$checkedChecklistValue	=	$a_arrayCheckedList[$v];
				?>
				<tr>
					<td width="3%" class="smalltext23" valign="top"><?php echo $countMarkedCheklist;?>)</td>
					<td width="5%" class="smalltext23" valign="top">
						<font color="<?php echo $fontColor;?>"><?php echo $checkedChecklistValue;?></font>
					</td>
					<td valign="top">
						<font style="font-family:Trebuchet MS;color:#333333;font-size:14px;font-weight:normal;text-decoration:none;letter-spacing:0px;text-align:justify;">
							<?php echo $checkedChecklistText;?>
						</font>
					</td>
				</tr>
				<?php
					}
				?>
			</table>
		</td>
	</tr>
	<?php
		}
		if($a_markedChecklistQa	=	$orderObj->getQAChecklistMarked($orderId))
		{
			$a_arrayCheckedList	=	array("1"=>"Yes","2"=>"No","3"=>"N/A");
			$a_existingChecklist=	$orderObj->getAllQaCheckListWithAnswer();
	?>
	<tr>
		<td colspan='4' height="2">
			<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
				<tr>
					<td colspan="5" class="smalltext23">
						<b>Marked QA checklist</b>
					</td>
				</tr>
				<tr>
					<td height="5"></td>
				</tr>
				<?php
					$countMarkedCheclist	=0;
					foreach($a_markedChecklistQa as $k=>$v)
					{
						$countMarkedCheclist++;

						$checkedText	=	$a_existingChecklist[$k];
						
						list($checkedText,$correctAns)= explode("<=>",$checkedText);

						$fontColor1		=	"#333333";
						if(!empty($correctAns) && $correctAns	!= $v)
						{
							$fontColor1	=	"#ff0000";
						}


						$checkedValue	=	$a_arrayCheckedList[$v];
				?>
				<tr>
					<td width="3%" class="smalltext23" valign="top"><?php echo $countMarkedCheclist;?>)</td>
					<td width="5%" class="error" valign="top">
						<font color="<?php echo $fontColor1;?>"><?php echo $checkedValue;?></font>
					</td>
					<td valign="top">
						<font style="font-family:Trebuchet MS;color:#333333;font-size:14px;font-weight:normal;text-decoration:none;letter-spacing:0px;text-align:justify;">
							<?php echo $checkedText;?>
						</font>
					</td>
				</tr>
				<?php
					}
			?>
			</table>
		 </td>
	</tr>
	<?php
		}
		if($result1						=	$orderObj->getPostAuditOrderDetails($orderId))
		{
			$row1						=	mysqli_fetch_assoc($result1);
			$auditId					=	$row1['auditId'];
			$firstCategory				=	$row1['firstCategory'];
			$firstCategoryDescription	=	stripslashes($row1['firstCategoryDescription']);
			$secondCategory				=	$row1['secondCategory'];
			$secondCategoryDescription	=	stripslashes($row1['secondCategoryDescription']);
			$thirdCategory				=	$row1['thirdCategory'];
			$thirdCategoryDescription	=	stripslashes($row1['thirdCategoryDescription']);

			$auditAddedBy				=	$row1['addedBy'];
			$auditAddedOn				=	showDate($row1['addedOn']);
			$auditAddedByText			=	$employeeObj->getEmployeeName($auditAddedBy)." on ".$auditAddedOn;

			$checkedFirstCategory		=	"Yes";
			$checkedSecondCategory		=	"Yes";
			$checkedThirdCategory		=	"Yes";
			if($firstCategory			==	0)
			{
				$checkedFirstCategory	=	"No";
			}
			if($secondCategory			==	0)
			{
				$checkedSecondCategory	=	"No";
			}
			if($thirdCategory			==	0)
			{
				$checkedThirdCategory	=	"No";
			}
?>
		<tr>
			<td class="smalltext23" valign="top">
				IS HAVING CATEGORY A ERRORS
			</td>
			<td class="smalltext23"  valign="top">
				:
			</td>
			<td class="smalltext24" valign="top">
				<b><?php echo $checkedFirstCategory;?></b>
				<?php
					if(!empty($firstCategoryDescription))
					{
						echo "<p align='justify'>".$firstCategoryDescription."</p>";
					}
				?>
			</td>
		</tr>
		<tr>
			<td class="smalltext23" valign="top">
				IS HAVING CATEGORY B ERRORS
			</td>
			<td class="smalltext23"  valign="top">
				:
			</td>
			<td class="smalltext24" valign="top">
				<b><?php echo $checkedSecondCategory;?></b>
				<?php
					if(!empty($secondCategoryDescription))
					{
						echo "<p align='justify'>".$secondCategoryDescription."</p>";
					}
				?>
			</td>
		</tr>
		<tr>
			<td class="smalltext23" valign="top">
				IS HAVING CATEGORY C ERRORS
			</td>
			<td class="smalltext23"  valign="top">
				:
			</td>
			<td class="smalltext24" valign="top">
				<b><?php echo $checkedThirdCategory;?></b>
				<?php
					if(!empty($thirdCategoryDescription))
					{
						echo "<p align='justify'>".$thirdCategoryDescription."</p>";
					}
				?>
			</td>
		</tr>
	<?php
	}
	echo "</table>";
	}
	else
	{
?>
<table width="98%" align="center" border="0" cellpadding="3" cellspacing="2">
	<tr>
		<td height='250' style='text-align:center'><font style='font-size:16px;font-family:verdana;color:#ff0000;font-weight:bold'>This Order Is Not Yet Completed</font></td>
	</tr>
</table>
<?php
	}
?>
<br />

