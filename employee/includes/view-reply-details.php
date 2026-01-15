<?php
	$a_attachmentPath					=	array();
	$a_attachmentType					=	array();
	$a_attachmentName					=	array();
	$isRepliedWithNewSystem				=	0;
	$totalAmountEmailFileSize			=	0;


	if($result							=	$orderObj->getReplyOrderDetails($orderId,$customerId))
	{
		$row							=	mysql_fetch_assoc($result);
		$replyId						=	$row['replyId'];
		$hasReplyOrderFile				=	$row['hasReplyOrderFile'];
		$replyOrderFileExt				=	$row['replyOrderFileExt'];
		$hasReplyPublicRecordFile		=	$row['hasReplyPublicRecordFile'];
		$replyPublicRecordFileExt		=	$row['replyPublicRecordFileExt'];
		$hasReplyMlsFile				=	$row['hasReplyMlsFile'];
		$replyMlsFileExt				=	$row['replyMlsFileExt'];
		$hasReplyMarketCondition		=	$row['hasReplyMarketCondition'];
		$replyMarketConditionExt		=	$row['replyMarketConditionExt'];
		$hasOtherFile					=	$row['hasOtherFile'];
		$otherFileExt					=	$row['otherFileExt'];
		$hasCompletedPdfFile			=	$row['hasCompletedPdfFile'];
		$compltetedPdfFileExt			=	$row['compltetedPdfFileExt'];

		$replyOrderFileName				=	$row['replyOrderFileName'];
		$replyPublicRecordFileName		=	$row['replyPublicRecordFileName'];
		$replyMlsFileName				=	$row['replyMlsFileName'];
		$replyMarketConditionFileName	=	$row['replyMarketConditionFileName'];
		$otherFileName					=	$row['otherFileName'];
		$compltetedPdfFileName			=	$row['compltetedPdfFileName'];

		$replyOrderFileSize				=	$row['replyOrderFileSize'];
		$replyPublicRecordSize			=	$row['replyPublicRecordSize'];
		$replyMlsFileSize				=	$row['replyMlsFileSize'];
		$replyOtherFileSize				=	$row['replyOtherFileSize'];
		$replyMarketConditionFileSize	=	$row['replyMarketConditionFileSize'];
		$compltetedPdfFileSize			=	$row['compltetedPdfFileSize'];

		$replyOrderMimeType				=	$row['replyOrderMimeType'];
		$replyPublicRecordMimeType		=	$row['replyPublicRecordMimeType'];
		$replyMlsMimeType				=	$row['replyMlsMimeType'];
		$replyMarketConditionMimeType	=	$row['replyMarketConditionMimeType'];
		$replyOtherFileMimeType			=	$row['replyOtherFileMimeType'];
		$compltetedPdfFileMimeType		=	$row['compltetedPdfFileMimeType'];

		$replyInstructions				=	stripslashes($row['replyInstructions']);
		$commentsToQa					=   stripslashes($row['commentsToQa']);
		$timeSpentEmployee				=	$row['timeSpentEmployee'];

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
		$isReadOrderRepliedEmail		=	"";

		if($readDateOrderReplyIp		=	$employeeObj->getFirstEmailReadTime($emailUniqueCode))
		{
			list($readDate,$readTime)	=	explode("|",$readDateOrderReplyIp);
			$isReadOrderRepliedEmail	=	"<br><br>(<font color='#ff0000'>Customer Read Email At - ".showDate($readDate)." EST at - ".showTimeFormat($readTime)." Hrs</font>)";
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
			$row2			=  mysql_fetch_assoc($result2);
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

		if(!empty($hasReplyOrderFile))
		{
			if($isRepliedWithNewSystem	==	1)
			{
				if($filenameSize		=	$orderObj->getEmployeeFileNameWithExtSize($orderId,$replyId,2,1))
				{
					list($replyFileID,$fileRepliedToCustomer,$fileSizeRepliedToCustomer)	=	explode("|",$filenameSize);

					$t_replieddFileToustomer =	stringReplace("Upload ", "", $replieddFileToustomer);

					$base_fileId			=	base64_encode($replyFileID);

					$downLoadPath			=	SITE_URL_MEMBERS."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;

					$showFilesNameInEmail	.=	"<tr><td width='35%' valign='top'  align='left'><font style='font-size:10px;color:#4d4d4d;'>".$t_replieddFileToustomer."</font></td><td width='2%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>:</font></td><td valign='top' align='left'><a href='".$downLoadPath."' target='_blank'><font style='font-size:12px;color:#0082bf;'>".$fileRepliedToCustomer."</font></a>".getSizeNoBracket($fileSizeRepliedToCustomer)."</td></tr>";

					if($fileSizeRepliedToCustomer > 0)
					{
						if($returnFileDetails		=	$orderObj->getExactMultipleOrderFiles($orderId,$replyFileID))
						{
							list($path,$ext,$fileName,$mimeType,$fileSize)=	explode("<=>",$returnFileDetails);
							
							$a_attachmentPath[]			=	$path;
							$a_attachmentType[]			=	$mimeType;
							$a_attachmentName[]			=	$fileRepliedToCustomer;

							$totalAmountEmailFileSize	=	$totalAmountEmailFileSize+$fileSize;
						}
					}
				}
			}
			else
			{
				$t_replieddFileToustomer =	stringReplace("Upload ", "", $replieddFileToustomer);

				$showFilesNameInEmail	.=	"<tr><td width='35%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>".$t_replieddFileToustomer."</font></td><td width='2%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>:</font></td><td valign='top' align='left'><a href='".SITE_URL_MEMBERS."/download.php?".$M_D_5_ID."=".$encodeOrderID."&t=OF&f=R' target='_blank'><font style='font-size:12px;color:#0082bf;'>".$replyOrderFileName.".".$replyOrderFileExt."</font></a>".getSizeNoBracket($replyOrderFileSize)."</td></tr>";

				
				if($replyOrderFileSize > 0)
				{
					$orderFilePath				=	SITE_ROOT_FILES."/files/orderFiles/$folderId/";
					$fileName					=	$orderId."_".$replyId."_".$replyOrderFileName.".".$replyOrderFileExt;

					$a_attachmentPath[]			=	$orderFilePath.$fileName;
					$a_attachmentType[]			=	$replyOrderMimeType;
					$a_attachmentName[]			=	$replyOrderFileName.".".$replyOrderFileExt;

					$totalAmountEmailFileSize	=	$totalAmountEmailFileSize+$replyOrderFileSize;
				}
			}
		}

		if(!empty($hasCompletedPdfFile))
		{
			if($isRepliedWithNewSystem	==	1)
			{
				if($filenameSize		=	$orderObj->getEmployeeFileNameWithExtSize($orderId,$replyId,2,7))
				{
					list($replyFileID,$fileRepliedToCustomer,$fileSizeRepliedToCustomer)	=	explode("|",$filenameSize);

					$base_fileId			=	base64_encode($replyFileID);

					$downLoadPath			=	SITE_URL_MEMBERS."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;

					$showFilesNameInEmail	.=	"<tr><td width='35%' valign='top'  align='left'><font style='font-size:10px;color:#4d4d4d;'>Completed Report PDF File for Reference</font></td><td width='2%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>:</font></td><td valign='top' align='left'><a href='".$downLoadPath."' target='_blank'><font style='font-size:12px;color:#0082bf;'>".$fileRepliedToCustomer."</font></a>".getSizeNoBracket($fileSizeRepliedToCustomer)."</td></tr>";

					if($fileSizeRepliedToCustomer > 0)
					{
						if($returnFileDetails		=	$orderObj->getExactMultipleOrderFiles($orderId,$replyFileID))
						{
							list($path,$ext,$fileName,$mimeType,$fileSize)=	explode("<=>",$returnFileDetails);
							
							$a_attachmentPath[]			=	$path;
							$a_attachmentType[]			=	$mimeType;
							$a_attachmentName[]			=	$fileRepliedToCustomer;

							$totalAmountEmailFileSize	=	$totalAmountEmailFileSize+$fileSize;
						}
					}
				}
			}
			else
			{
				$showFilesNameInEmail	.=	"<tr><td width='35%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>Completed Report PDF File for Reference</font></td><td width='2%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>:</font></td><td valign='top' align='left'><a href='".SITE_URL_MEMBERS."/download.php?".$M_D_5_ID."=".$encodeOrderID."&t=PDF&f=R' target='_blank'><font style='font-size:12px;color:#0082bf;'>".$compltetedPdfFileName.".".$compltetedPdfFileExt."</font></a>".getSizeNoBracket($compltetedPdfFileSize)."</td></tr>";

				
				if($compltetedPdfFileSize > 0)
				{
					$otherFilePath				=	SITE_ROOT_FILES."/files/otherFiles/$folderId/";
					$fileName					=	$orderId."_".$replyId."_replied_completed_pdf_file.".$compltetedPdfFileExt;

					$a_attachmentPath[]			=	$otherFilePath.$fileName;
					$a_attachmentType[]			=	$compltetedPdfFileMimeType;
					$a_attachmentName[]			=	$compltetedPdfFileName.".".$compltetedPdfFileExt;

					$totalAmountEmailFileSize	=	$totalAmountEmailFileSize+$compltetedPdfFileSize;
				}
			}
		}


		if(!empty($hasReplyPublicRecordFile))
		{
			if($isRepliedWithNewSystem	==	1)
			{
				if($filenameSize	=	$orderObj->getEmployeeFileNameWithExtSize($orderId,$replyId,2,2))
				{
					list($replyFileID,$replyPublicRecordFileName,$replyPublicRecordSize)	=	explode("|",$filenameSize);

					$base_fileId			=	base64_encode($replyFileID);

					$downLoadPath			=	SITE_URL_MEMBERS."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;


					$showFilesNameInEmail	.=	"<tr><td width='35%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>Public Records File</font></td><td width='2%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>:</font></td><td valign='top' align='left'><a href='".$downLoadPath."' target='_blank'><font style='font-size:12px;color:#0082bf;'>".$replyPublicRecordFileName."</font></a>".getSizeNoBracket($replyPublicRecordSize)."</td></tr>";

					if($replyPublicRecordSize > 0)
					{
						if($returnFileDetails		=	$orderObj->getExactMultipleOrderFiles($orderId,$replyFileID))
						{
							list($path,$ext,$fileName,$mimeType,$fileSize)=	explode("<=>",$returnFileDetails);
							
							$a_attachmentPath[]			=	$path;
							$a_attachmentType[]			=	$mimeType;
							$a_attachmentName[]			=	$replyPublicRecordFileName;

							$totalAmountEmailFileSize	=	$totalAmountEmailFileSize+$fileSize;
						}
					}
				}
			}
			else
			{
				$showFilesNameInEmail	.=	"<tr><td width='35%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>Public Records File</font></td><td width='2%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>:</font></td><td valign='top' align='left'><a href='".SITE_URL_MEMBERS."/download.php?".$M_D_5_ID."=".$encodeOrderID."&t=PF&f=R' target='_blank'><font style='font-size:12px;color:#0082bf;'>".$replyPublicRecordFileName.".".$replyPublicRecordFileExt."</font></a>".getSizeNoBracket($replyPublicRecordSize)."</td></tr>";

				if($replyPublicRecordSize > 0)
				{
					$replyPublicRecordFilePath	=	SITE_ROOT_FILES."/files/publicRecordFile/$folderId/";

					$fileName					=	$orderId."_".$replyId."_".$replyPublicRecordFileName.".".$replyPublicRecordFileExt;

					$a_attachmentPath[]			=	$replyPublicRecordFilePath.$fileName;
					$a_attachmentType[]			=	$replyPublicRecordMimeType;
					$a_attachmentName[]			=	$replyPublicRecordFileName.".".$replyPublicRecordFileExt;

					$totalAmountEmailFileSize	=	$totalAmountEmailFileSize+$replyPublicRecordSize;
				}
			}
		}
		if(!empty($hasReplyMlsFile))
		{
			if($isRepliedWithNewSystem	==	1)
			{
				if($filenameSize	=	$orderObj->getEmployeeFileNameWithExtSize($orderId,$replyId,2,3))
				{
					list($replyFileID,$replyMlsFileName,$replyMlsFileSize)	=	explode("|",$filenameSize);

					$base_fileId			=	base64_encode($replyFileID);

					$downLoadPath			=	SITE_URL_MEMBERS."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;

					$showFilesNameInEmail	.=	"<tr><td width='35%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>Plat Map</font></td><td width='2%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>:</font></td><td valign='top' align='left'><a href='".$downLoadPath."' target='_blank'><font style='font-size:12px;color:#0082bf;'>".$replyMlsFileName."</font></a>".getSizeNoBracket($replyMlsFileSize)."</td></tr>";

					if($replyMlsFileSize > 0)
					{
						if($returnFileDetails		=	$orderObj->getExactMultipleOrderFiles($orderId,$replyFileID))
						{
							list($path,$ext,$fileName,$mimeType,$fileSize)=	explode("<=>",$returnFileDetails);
							
							$a_attachmentPath[]			=	$path;
							$a_attachmentType[]			=	$mimeType;
							$a_attachmentName[]			=	$replyMlsFileName;

							$totalAmountEmailFileSize	=	$totalAmountEmailFileSize+$fileSize;
						}
					}
				}
			}
			else
			{
				$showFilesNameInEmail	.=	"<tr><td width='35%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>Plat Map</font></td><td width='2%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>:</font></td><td valign='top' align='left'><a href='".SITE_URL_MEMBERS."/download.php?".$M_D_5_ID."=".$encodeOrderID."&t=MF&f=R' target='_blank'><font style='font-size:12px;color:#0082bf;'>".$replyMlsFileName.".".$replyMlsFileExt."</font></a>".getSizeNoBracket($replyMlsFileSize)."</td></tr>";

				if($replyMlsFileSize > 0)
				{
					$replyMlsFilePath			=	SITE_ROOT_FILES."/files/mls/$folderId/";

					$fileName					= $orderId."_".$replyId."_".$replyMlsFileName.".".$replyMlsFileExt;

					$a_attachmentPath[]			=	$replyMlsFilePath.$fileName;
					$a_attachmentType[]			=	$replyMlsMimeType;
					$a_attachmentName[]			=	$replyMlsFileName.".".$replyMlsFileExt;

					$totalAmountEmailFileSize	=	$totalAmountEmailFileSize+$replyMlsFileSize;
				}
			}
		}
		if(!empty($hasOtherFile))
		{
			if($isRepliedWithNewSystem	==	1)
			{
				if($filenameSize	=	$orderObj->getEmployeeFileNameWithExtSize($orderId,$replyId,2,6))
				{
					list($replyFileID,$replyOtherFileName,$replyOtherFileSize)	=	explode("|",$filenameSize);

					$base_fileId			=	base64_encode($replyFileID);

					$downLoadPath			=	SITE_URL_MEMBERS."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;


					$showFilesNameInEmail	.=	"<tr><td width='35%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>Reply Other File</font></td><td width='2%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>:</font></td><td valign='top' align='left'><a href='".$downLoadPath."' target='_blank'><font style='font-size:12px;color:#0082bf;'>".$replyOtherFileName."</font></a>".getSizeNoBracket($replyMlsFileSize)."</td></tr>";

					if($replyMlsFileSize > 0)
					{
						if($returnFileDetails		=	$orderObj->getExactMultipleOrderFiles($orderId,$replyFileID))
						{
							list($path,$ext,$fileName,$mimeType,$fileSize)=	explode("<=>",$returnFileDetails);
							
							$a_attachmentPath[]			=	$path;
							$a_attachmentType[]			=	$mimeType;
							$a_attachmentName[]			=	$replyOtherFileName;

							$totalAmountEmailFileSize	=	$totalAmountEmailFileSize+$fileSize;
						}
					}
				}
			}
			else
			{
			
				$showFilesNameInEmail	.=	"<tr><td width='35%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>Reply Other File</font></td><td width='2%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>:</font></td><td valign='top' align='left'><a href='".SITE_URL_MEMBERS."/download.php?".$M_D_5_ID."=".$encodeOrderID."&t=OTF&f=R target='_blank'><font style='font-size:12px;color:#0082bf;'>".$otherFileName.".".$otherFileExt."</font></a>".getSizeNoBracket($replyOtherFileSize)."</td></tr>";

				if($replyOtherFileSize > 0)
				{
					$otherFilePath				=	SITE_ROOT_FILES."/files/otherFiles/$folderId/";
					$fileName					=	 $orderId."_".$replyId."_".$otherFileName.".".$otherFileExt;

					$a_attachmentPath[]			=	$otherFilePath.$fileName;
					$a_attachmentType[]			=	$replyOtherFileMimeType;
					$a_attachmentName[]			=	$otherFileName.".".$otherFileExt;

					$totalAmountEmailFileSize	=	$totalAmountEmailFileSize+$replyOtherFileSize;
				}
			}
		}

		$showFilesNameInEmail	.=	"</table>";
?>
<script type="text/javascript">
	function showHideEmployeeRepliedFiles(flag)
	{
		if(flag)
		{
			document.getElementById('showHideEmployeeReplies').style.display 	   = 'inline';
			document.getElementById('showAndHideReplies').innerHTML   = "<a href='javascript:showHideEmployeeRepliedFiles(0)'><img src='<?php echo SITE_URL;?>/images/hide.jpg' border='0' title='Hide'></a>";
		}
		else
		{
			document.getElementById('showHideEmployeeReplies').style.display 	= 'none';
			document.getElementById('showAndHideReplies').innerHTML= "<a href='javascript:showHideEmployeeRepliedFiles(1)'><img src='<?php echo SITE_URL;?>/images/show.jpg' border='0' title='Hide'></a>";
		}
	}
</script>
<br>
<fieldset style="border:1px solid #333333">
	<legend class="heading3"><b>REPLIED FILES BY EMPLOYEES</b></legend>
		<table width="100%" align="center" border="0" cellpadding="2" cellspacing="2">
			<tr>
				<td colspan="3" align="left">
					<div id="showAndHideReplies">
						<a href="javascript:showHideEmployeeRepliedFiles(0)"><img src="<?php echo SITE_URL;?>/images/hide.jpg" border='0' title='Hide'></a>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="3" valign="top">
					<div id="showHideEmployeeReplies">
						<table width='98%' align='center' cellpadding='3' cellspacing='3' border='0'>
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
							<td class="smalltext2" valign="top" align="left"><b>Uploaded PDF File of the Completd File</b></td>
							<td class="smalltext2" valign="top"><b>:</b></td>
							<td valign="top" class="smalltext2" align="left">
								<?php 
									if($hasCompletedPdfFile)
									{
										if($isRepliedWithNewSystem == 1 && $isNewUploadingSystem == 1)
										{
											if($result	=	$orderObj->getOrderMultipleFiles($orderId,$replyId,2,7))
											{
												$row				=	mysql_fetch_assoc($result);
												$fileId				=	$row['fileId'];
												$uploadingFileName	=	stripslashes($row['uploadingFileName']);
												$uploadingFileExt	=	stripslashes($row['uploadingFileExt']);
												$base_fileId		=	base64_encode($fileId);
												$uploadingFileSize	=	$row['uploadingFileSize'];

												$downLoadPath		=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
										?>
										<a class="link_style26" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a><br><font class='smalltext20'>
										<?php echo getFileSize($uploadingFileSize);?>
										<?php
											}
										}
										else
										{
											echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=PDF&f=R' class='link_style26'>".$compltetedPdfFileName.".".$compltetedPdfFileExt."</a>";
											
											echo "<br><font class='smalltext20'>".getFileSize($compltetedPdfFileSize)."</font>";
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
							<td class="smalltext2" width="30%" valign="top" align="left"><b><?php echo $replieddFileToustomer;?></b></td>
							<td class="smalltext2" width="2%"  valign="top"><b>:</b></td>
							<td valign="top" class="smalltext2" align="left">
								<?php 
									if($hasReplyOrderFile)
									{
										if($isRepliedWithNewSystem == 1 && $isNewUploadingSystem == 1)
										{
											if($result	=	$orderObj->getOrderMultipleFiles($orderId,$replyId,2,1))
											{
												$row				=	mysql_fetch_assoc($result);
												$fileId				=	$row['fileId'];
												$uploadingFileName	=	stripslashes($row['uploadingFileName']);
												$uploadingFileExt	=	stripslashes($row['uploadingFileExt']);
												$base_fileId		=	base64_encode($fileId);
												$uploadingFileSize	=	$row['uploadingFileSize'];

												$downLoadPath		=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
										?>
										<a class="link_style26" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a><br><font class='smalltext20'>
										<?php echo getFileSize($uploadingFileSize);?>
										<?php
											}
										}
										else
										{
										
											echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=OF&f=R'  class='link_style26'>".$replyOrderFileName.".".$replyOrderFileExt."</a>";
										
											echo "<br><font class='smalltext20'>".getFileSize($replyOrderFileSize)."</font>";
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
							<td class="smalltext2" valign="top"  align="left"><b>Uploaded Public Records File</b></td>
							<td class="smalltext2" valign="top"><b>:</b></td>
							<td valign="top" class="smalltext2" align="left">
								<?php 
									if($hasReplyPublicRecordFile)
									{
										if($isRepliedWithNewSystem == 1 && $isNewUploadingSystem == 1)
										{
											if($result	=	$orderObj->getOrderMultipleFiles($orderId,$replyId,2,2))
											{
												$row				=	mysql_fetch_assoc($result);
												$fileId				=	$row['fileId'];
												$uploadingFileName	=	stripslashes($row['uploadingFileName']);
												$uploadingFileExt	=	stripslashes($row['uploadingFileExt']);
												$base_fileId		=	base64_encode($fileId);
												$uploadingFileSize	=	$row['uploadingFileSize'];

												$downLoadPath		=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
										?>
										<a class="link_style26" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a><br><font class='smalltext20'>
										<?php echo getFileSize($uploadingFileSize);?>
										<?php
											}
										}
										else
										{
											echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=PF&f=R' class='link_style26'>".$replyPublicRecordFileName.".".$replyPublicRecordFileExt."</a>";
											
											echo "<br><font class='smalltext20'>".getFileSize($replyPublicRecordSize)."</font>";
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
							<td class="smalltext2" valign="top" align="left"><b>Upload Plat Map</b></td>
							<td class="smalltext2" valign="top"><b>:</b></td>
							<td valign="top" class="smalltext2" align="left">
								<?php 
									if($hasReplyMlsFile)
									{
										if($isRepliedWithNewSystem == 1 && $isNewUploadingSystem == 1)
										{
											if($result	=	$orderObj->getOrderMultipleFiles($orderId,$replyId,2,3))
											{
												$row				=	mysql_fetch_assoc($result);
												$fileId				=	$row['fileId'];
												$uploadingFileName	=	stripslashes($row['uploadingFileName']);
												$uploadingFileExt	=	stripslashes($row['uploadingFileExt']);
												$base_fileId		=	base64_encode($fileId);
												$uploadingFileSize	=	$row['uploadingFileSize'];

												$downLoadPath		=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
										?>
										<a class="link_style26" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a><br><font class='smalltext20'>
										<?php echo getFileSize($uploadingFileSize);?>
										<?php
											}
										}
										else
										{
										
											echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=MF&f=R' class='link_style26'>".$replyMlsFileName.".".$replyMlsFileExt."</a>";
											echo "<br><font class='smalltext20'>".getFileSize($replyMlsFileSize)."</font>";
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
							<td class="smalltext2" valign="top" align="left"><b>Uploaded Reply Other File</b></td></b></td>
							<td class="smalltext2" valign="top"><b>:</b></td>
							<td valign="top" class="smalltext2" align="left">
								<?php 
									if($hasOtherFile)
									{
										if($isRepliedWithNewSystem == 1 && $isNewUploadingSystem == 1)
										{
											if($result	=	$orderObj->getOrderMultipleFiles($orderId,$replyId,2,6))
											{
												$row				=	mysql_fetch_assoc($result);
												$fileId				=	$row['fileId'];
												$uploadingFileName	=	stripslashes($row['uploadingFileName']);
												$uploadingFileExt	=	stripslashes($row['uploadingFileExt']);
												$base_fileId		=	base64_encode($fileId);
												$uploadingFileSize	=	$row['uploadingFileSize'];

												$downLoadPath		=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
										?>
										<a class="link_style26" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a><br><font class='smalltext20'>
										<?php echo getFileSize($uploadingFileSize);?>
										<?php
											}
										}
										else
										{
										
											echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=OTF&f=R'  class='link_style26'>".$otherFileName.".".$otherFileExt."</a>";
											echo "<br><font class='smalltext20'>".getFileSize($replyOtherFileSize)."</font>";
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
							<td class="smalltext2" valign="top" align="left" width="23%"><b>Customer Instructions</b></td>
							<td class="smalltext2" valign="top" width="2%"><b>:</b></td>
							<td valign="top" class="smalltext2" align="left">
								<?php echo nl2br($replyInstructions).$isReadOrderRepliedEmail;?>
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
							<td class="smalltext2" valign="top" align="left"><b>Rate Given By Customer</b></td>
							<td class="smalltext2" valign="top"><b>:</b></td>
							<td valign="top" class="heading1" align="left">
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
							<td class="smalltext2" valign="top" align="left"><b>Rate Note By Customer</b></td>
							<td class="smalltext2" valign="top"><b>:</b></td>
							<td valign="top" class="smalltext2" align="left">
								<?php
									echo $memberRateMsg;
								?>
							</td>
						</tr>
						<?php
								}
								$query11	=	"SELECT ratingFileExt,ratingFileName,ratingFileFileSize FROM members_orders WHERE orderId=$orderId AND memberId=$customerId AND hasUploadedRatingFile=1 AND rateGiven <> 0";
								$result11	=	dbQuery($query11);
								if(mysql_num_rows($result11))
								{
									
						?>
						<tr>
							<td class="smalltext2" valign="top" align="left"><b>Rate File By Customer</b></td>
							<td class="smalltext2" valign="top"><b>:</b></td>
							<td valign="top" class="smalltext2" align="left">
								<?php
									if($isRepliedWithNewSystem == 1 && $isNewUploadingSystem == 1)
									{
										if($result	=	$orderObj->getMultipleOrderFiles($orderId,$customerId,4,7))
										{
											$row		=	mysql_fetch_assoc($result);
											$fileId					=	$row['fileId'];
											$uploadingFileName		=	stripslashes($row['uploadingFileName']);
											$uploadingFileExt		=	stripslashes($row['uploadingFileExt']);
											$base_fileId			=	base64_encode($fileId);
											$uploadingFileSize		=	$row['uploadingFileSize'];

											$downLoadPath			=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
									?>
									<a class="link_style26" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a>&nbsp;<font class='smalltext20'>
									<?php echo getFileSize($uploadingFileSize);?></font>
									<?php
										}
									}
									else
									{
										$row11					=	mysql_fetch_assoc($result11);
										$ratingFileName			=	$row11['ratingFileName'];
										$ratingFileExt			=	$row11['ratingFileExt'];
										$ratingFileFileSize		=	$row11['ratingFileFileSize'];

										echo "<a href='".SITE_URL_EMPLOYEES."/download-rating-file.php?ID=$orderId&t=RTF'  class='link_style2'>".$ratingFileName.".".$ratingFileExt."</a>";
										echo "&nbsp;&nbsp;<font class='smalltext2'>".getFileSize($ratingFileFileSize)."</font>";
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
							<td class="smalltext2" valign="top" align="left"><b>Comments From Employee To QA</b></td>
							<td class="smalltext2" valign="top"><b>:</b></td>
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
							<td class="smalltext2" valign="top" align="left"><b>Time Spent By Employee</b></td>
							<td class="smalltext2" valign="top"><b>:</b></td>
							<td valign="top" class="smalltext2" align="left">
								<?php echo $timeSpentEmployee;?> Minitues.
							</td>
						</tr>
						<?php
							}
							if(!empty($qaChecked))
							{
						?>
						<tr>
							<td class="smalltext2" valign="top" align="left"><b>Works Done By QA</b></td>
							<td class="smalltext2" valign="top"><b>:</b></td>
							<td valign="top" class="smalltext2" align="left">
								<?php echo $qaChecked;?>
							</td>
						</tr>
						<?php
							}
							if(!empty($errorCorrected))
							{
						?>
						<tr>
							<td class="smalltext2" valign="top" align="left"><b>Errors Found And Corrected By QA</b></td>
							<td class="smalltext2" valign="top"><b>:</b></td>
							<td valign="top" class="smalltext2" align="left">
								<?php echo nl2br($errorCorrected);?>
							</td>
						</tr>
						<?php
							}
							if(!empty($feedbackToEmployee))
							{
						?>
						<tr>
							<td class="smalltext2" valign="top" align="left"><b>Feedback From QA</b></td>
							<td class="smalltext2" valign="top"><b>:</b></td>
							<td valign="top" class="smalltext2" align="left">
								<?php echo $feedbackToEmployee;?>
							</td>
						</tr>
						<?php	
							}
							if(!empty($timeSpentQa))
							{
						?>
						<tr>
							<td class="smalltext2" valign="top" align="left"><b>Time Spent By QA</b></td>
							<td class="smalltext2" valign="top"><b>:</b></td>
							<td valign="top" class="smalltext2" align="left">
								<?php echo $timeSpentQa;?> Minitues.
							</td>
						</tr>
						<?php		
							}
							
							if(!empty($qaRateMessage))
							{
						?>
						<tr>
							<td class="smalltext2" valign="top" align="left"><b>Comments From QA</b></td>
							<td class="smalltext2" valign="top"><b>:</b></td>
							<td colspan="2" valign="top" class="smalltext2" align="left">
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
						<td colspan='4' height="2">
							<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
								<tr>
									<td colspan="5" class="textstyle1">
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
									<td width="3%" class="textstyle" valign="top"><?php echo $countMarkedCheklist;?>)</td>
									<td width="5%" class="textstyle" valign="top">
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
									<td colspan="5" class="textstyle1">
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
									<td width="3%" class="textstyle" valign="top"><?php echo $countMarkedCheclist;?>)</td>
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
						
					?>
					</table>
				</div>
				</td>
			</tr>
		</table>
<?php
		if($result1						=	$orderObj->getPostAuditOrderDetails($orderId))
		{
			$row1						=	mysql_fetch_assoc($result1);
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
	<br>
	<table width='98%' align='center' cellpadding='3' cellspacing='3' border='0'>
		<tr>
			<td colspan="5" class="heading3"><b>POST AUDIT ERROR DETAILS</b></td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td width="38%" class="textstyle1" valign="top">
				IS HAVING CATEGORY A ERRORS
			</td>
			<td width="2%" class="textstyle1"  valign="top">
				:
			</td>
			<td class="textstyle1" valign="top">
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
			<td height="5"></td>
		</tr>
		<tr>
			<td class="textstyle1" valign="top">
				IS HAVING CATEGORY B ERRORS
			</td>
			<td class="textstyle1"  valign="top">
				:
			</td>
			<td class="textstyle1" valign="top">
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
			<td height="5"></td>
		</tr>
		<tr>
			<td class="textstyle1" valign="top">
				IS HAVING CATEGORY C ERRORS
			</td>
			<td class="textstyle1"  valign="top">
				:
			</td>
			<td class="textstyle1" valign="top">
				<b><?php echo $checkedThirdCategory;?></b>
				<?php
					if(!empty($thirdCategoryDescription))
					{
						echo "<p align='justify'>".$thirdCategoryDescription."</p>";
					}
				?>
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
	</table>
<?php
				
		}
?>
</fieldset>
<?php
	}	
	if($customerId	==	6 && $s_employeeId == 3)
	{
		echo "FILE SIZES : ".$totalAmountEmailFileSize;
	}
?>