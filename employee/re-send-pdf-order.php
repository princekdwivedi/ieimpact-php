<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/new-top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");
	$employeeObj				=   new employee();
	$orderObj					=   new orders();
	$validator					=   new validate();
	$orderAcceptedBy			=	0;
	$qaDoneBy					=	0;
	$a_allDeactivatedEmployees  =	$employeeObj->getAllInactiveEmployees();
	
	$a_orderAdminReplyMessages	=	array();
	$hasAdminMessage			=   0;
	$errorMsg					=	"";
	$resendingReason			=	"";
	$orderFileSize              =   0;

	$isEnableCheckFileHash		=	$employeeObj->getSingleQueryResult("SELECT isEnable FROM enable_disable_duplicate_order_checking","isEnable");
	

	$query						=	"SELECT * FROM admin_added_customer_messages WHERE section=2 ORDER BY message";
	$result						=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		$hasAdminMessage					=	1;
		$a_orderAdminReplyMessages['-1']	=	"<font color='#ff0000'>Not found suitable instructions, add own message</font>";
		while($row							=	mysqli_fetch_assoc($result))
		{
			$t_adminMessadeId				=	$row['messageId'];
			$t_adminMessageLevel			=	stripslashes($row['messageLevel']);
			

			$a_orderAdminReplyMessages[$t_adminMessadeId]	=	$t_adminMessageLevel;
		}
		
	}

	if(isset($_GET['orderId']) && isset($_GET['customerId']))
	{
		$orderId		=	$_GET['orderId'];
		$customerId		=	$_GET['customerId'];
		$orderStatus		= $orderObj->getOrderStatus($orderId,$customerId);
		$orderAcceptedBy	= $orderObj->getOrderAcceptedBY($orderId,$customerId);
		$qaDoneBy			= $orderObj->getOrderQaBY($orderId,$customerId);
		
		if($orderStatus != 2 && $orderStatus != 5 && $orderStatus != 6)
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
			exit();
		}
		else
		{
			//if($s_employeeId != $orderAcceptedBy && empty($s_hasManagerAccess) && $s_employeeId != $qaDoneBy)
			if(empty($s_hasManagerAccess))
			{
				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
				exit();
			}
		}
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
		exit();
	}
	$adminMessadeId		=	$employeeObj->getSingleQueryResult("SELECT replyAdminMessageId FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId","replyAdminMessageId");
	if(empty($adminMessadeId))
	{
		$adminMessadeId	=	0;
	}
	$form				=	SITE_ROOT_EMPLOYEES."/forms/resend-pdf-order.php";
	$formSearch			=	SITE_ROOT_EMPLOYEES."/forms/search-general-order-form.php";
?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
<tr>
	<td colspan="8" class="heading1">
		:: RESEND FILES IN COMEPLETED CUSTOMER ORDER ::
	</td>
</tr>
<tr>
	<td colspan="8" height="5"></td>
</tr>
</table>
<?php
	include($formSearch);

	include(SITE_ROOT_EMPLOYEES	. "/includes/view-customer-order1.php");
	
	function findexts($filename) 
	{ 
		$ext        =    "";
		$filename   =    strtolower($filename) ; 
		$a_exts		=	 explode(".",$filename);
		$total		=	 count($a_exts);
		if($total > 1){
			$ext	=	 end($a_exts);		
		}		
		return $ext; 
	} 
	function getFileName($fileName)
	{
		$fileName		=  stripslashes($fileName);
		$dotPosition	=  strpos($fileName, "'");
		if($dotPosition == true)
		{
			$fileName	=	stringReplace("'", "", $fileName);
		}
		$doubleDotPosition	  =  strpos($fileName, '"');
		if($doubleDotPosition == true)
		{
			$fileName	=	stringReplace('"', '', $fileName);
		}
		$fileName		=	stringReplace("/", '', $fileName);
		$fileName		=	stringReplace(":", '', $fileName);
		$fileName		=	stringReplace("&", '', $fileName);
		$fileName		=	stringReplace("*", '', $fileName);
		$fileName		=	stringReplace("?", '', $fileName);
		$fileName		=	stringReplace("|", '', $fileName);
		$fileName		=	stringReplace("<", '', $fileName);
		$fileName		=	stringReplace(">", '', $fileName);
		$fileExtPos		=   strrpos($fileName, '.');
		$fileName		=   substr($fileName,0,$fileExtPos);
		
		return $fileName;
	}

	if(count($a_customerOrderTemplateFiles) > 0)
	{
		$checkExistingOrderfileName	=	"1";
	}
	else
	{
		$checkExistingOrderfileName	=	"0";
	}

	$hasReplyOrderFile			=	0;
	$hasReplyPublicRecordFile	=	0;
	$hasReplyMlsFile			=	0;
	$hasOtherFile				=	0;
	$replyId					=	0;
	$replyOrderFileExt			=	"";
	$hasReplyPublicRecordFile	=	"";
	$replyPublicRecordFileExt	=	"";
	$replyMlsFileExt			=	"";
	$hasReplyMarketCondition	=	"";
	$replyMarketConditionExt	=	"";
	$otherFileExt				=	"";
	$hasCompletedPdfFile		=	0;
	$compltetedPdfFileExt		=	"";
	$compltetedPdfFileName		=	"";
	$compltetedPdfFileSize		=	"";

	$replyOrderFileName			=	"";
	$replyPublicRecordFileName	=	"";
	$replyMlsFileName			=	"";
	$replyMarketConditionFileName=	"";
	$otherFileName				=	"";

	$replyOrderFileSize			=	"";
	$replyPublicRecordSize		=	"";
	$replyMlsFileSize			=	"";
	$replyOtherFileSize			=	"";
	$replyMarketConditionFileSize	=	"";

	$replyInstructions			=	"";
	$isRepliedWithNewSystem		=	0;

	$totalAmountReplyEmailFileSize	=	0;
	$hasAttachment				=	0;
	$a_attachmentPath			=	array();
	$a_attachmentType			=	array();
	$a_attachmentName			=	array();
	$didUploadAnyFile			=	false;

	$showReplyFilesNameInEmail	 =  "";
	$showReplyFilesNameInEmail	.=	"<table width='99%' align='center' border='0' cellpadding='2' cellspacing='2' style='border:2px solid #e4e4e4;'>";
	$showReplyFilesNameInEmail	.=	"<tr><td colspan='3' align='left'><font style='font-size:11px;font-weight:bold;color:#6E6E6E;'>COMPLETED FILES OF THIS ORDER</font></td></tr>";


	$folder						=	$employeeObj->getSingleQueryResult("SELECT folderId FROM members WHERE memberId=$customerId","folderId");

	$query		=	"SELECT * FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId";
	$result		=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		$row							=	mysqli_fetch_assoc($result);
		$replyId						=	$row['replyId'];
		$hasReplyOrderFile				=	$row['hasReplyOrderFile'];
		$hasReplyPublicRecordFile		=	$row['hasReplyPublicRecordFile'];
		$hasReplyMarketCondition		=	$row['hasReplyMarketCondition'];
		$hasOtherFile					=	$row['hasOtherFile'];
		$replyInstructions				=	$row['replyInstructions'];
		$isRepliedWithNewSystem			=	$row['isRepliedWithNewSystem'];

		$hasCompletedPdfFile			=	$row['hasCompletedPdfFile'];

	}
	if(isset($_GET['success']))
	{
	?>
	<table width="100%" align="center" border="0" cellpadding="2" cellspacing="2">
			<tr>
				<td class="smalltext10" align="center">
						<b>You have Successfuly Uploaded Reply Files For This Order</b>.<br><br>
				</td>
			</tr>
	</table>
<?php
	}
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		if(!empty($hasAdminMessage))
		{
			$replyAdminMessageId	=	$adminMessadeId;
		}
		else
		{
			$replyAdminMessageId	=	0;
		}
		$resendingReason			=	trim($resendingReason);

		if(!empty($_FILES['pdfCompletedFile']['name']))
		{
			$uploadingPdfFile		=   $_FILES['pdfCompletedFile']['name'];
			$uploadingPdfFileSize	=   $_FILES['pdfCompletedFile']['size'];
			$pdfExt					=	findexts($uploadingPdfFile);

			if($pdfExt	!= "pdf")
			{
				$validator ->setError("Please upload a PDF File of the Completd File !!");
			}
			elseif($uploadingPdfFileSize > MAXIMUM_SINGLE_FILE_SIZE_ALLOWED)
			{
				$validator ->setError("The PDF File you are trying to upload is very large. It's size must be less than ".MAXIMUM_SINGLE_FILE_SIZE_ALLOWED_TEXT.". Please reduce the filesize by removing large pictures etc.");
			}
		}
		if(empty($hasCompletedPdfFile))
		{
			if(empty($_FILES['pdfCompletedFile']['name']))
			{
				$validator ->setError("Please upload a PDF File of the Completd File !!");
			}
		}

		$replyInstructions	=	makeDBSafe($replyInstructions);
		if(!empty($_FILES['replyOrderFile']['name']))
		{
			$uploadingFile				=   $_FILES['replyOrderFile']['name'];
			$uploadingFileSize			=   $_FILES['replyOrderFile']['size'];
			$tempName					=	$_FILES['replyOrderFile']['tmp_name'];
			$orderReplyFileMd5HasSize	=	md5_file($tempName);
			//$foundExistsMd5FileFize		=	$orderObj->checkExistingMd5HasOrderReplyFile($orderReplyFileMd5HasSize);
			$replyFileExistingMatch		=	$orderObj->checkRepliedFileChecksum($orderReplyFileMd5HasSize,$uploadingFileSize,$orderFileSize,$orderId);
			$ext						=	findexts($uploadingFile); 
			if($uploadingFile  !=  $checkExistingOrderfileName)
			{
				if($appraisalSoftwareType == 1)
				{
					if($ext		!=	"zap")
					{
						$validator ->setError("Please Only ".$replieddFileToustomer." !!");
					}
				}
				elseif($appraisalSoftwareType == 2)
				{
					if($ext		!=	"aci" && $ext		!=	"zoo")
					{
						$validator ->setError("Please Only ".$replieddFileToustomer." !!");
					}
				}
				elseif($appraisalSoftwareType == 3)
				{
					if($ext		!=	"clk")
					{
						$validator ->setError("Please Only ".$replieddFileToustomer." !!");
					}
				}
				elseif($appraisalSoftwareType == 4)
				{
					if($ext		!=	"rpt" && $ext		!=	"rptx")
					{
						$validator ->setError("Please Only ".$replieddFileToustomer." !!");
					}
				}
				elseif($appraisalSoftwareType == 5)
				{
					if($ext		!=	"zap")
					{
						$validator ->setError("Please Only ".$replieddFileToustomer.".");
					}
				}
				elseif($appraisalSoftwareType == 6)
				{
					if($ext		!=	"rpt")
					{
						$validator ->setError("Please Only ".$replieddFileToustomer.".");
					}
				}
			}
			elseif($uploadingFileSize > MAXIMUM_SINGLE_FILE_SIZE_ALLOWED)
			{
				$validator ->setError("The Template File you are trying to upload is very large. It's size must be less than ".MAXIMUM_SINGLE_FILE_SIZE_ALLOWED_TEXT.". Please reduce the filesize by removing large pictures etc.");
			}
			if(!empty($replyFileExistingMatch))
			{
				$validator ->setError($replyFileExistingMatch.".");
			}
			/*if(!empty($foundExistsMd5FileFize) &&  $isEnableCheckFileHash == 1)
			{
				$validator ->setError($foundExistsMd5FileFize." !!");
			}
			if($uploadingFileSize == $orderFileSize)
			{
				$validator ->setError(" Number of Bytes of this file match exactly. You are trying to upload the same file customer sent. Please upload new file !!");
			}*/
		}

		if(!empty($_FILES['replyPublicRecordFile']['name']))
		{
			$uploadingFileSize			=   $_FILES['replyPublicRecordFile']['size'];

			if($uploadingFileSize > MAXIMUM_SINGLE_FILE_SIZE_ALLOWED)
			{
				$validator ->setError("The Public Records File you are trying to upload is very large. It's size must be less than ".MAXIMUM_SINGLE_FILE_SIZE_ALLOWED_TEXT.". Please reduce the filesize by removing large pictures etc.");
			}
		}
		if(!empty($_FILES['replyMlsFile']['name']))
		{
			$uploadingFileSize			=   $_FILES['replyMlsFile']['size'];

			if($uploadingFileSize > MAXIMUM_SINGLE_FILE_SIZE_ALLOWED)
			{
				$validator ->setError("The Plat Map File you are trying to upload is very large. It's size must be less than ".MAXIMUM_SINGLE_FILE_SIZE_ALLOWED_TEXT.". Please reduce the filesize by removing large pictures etc.");
			}
		}

		if(!empty($_FILES['otherFile']['name']))
		{
			$uploadingFileSize			=   $_FILES['otherFile']['size'];

			if($uploadingFileSize > MAXIMUM_SINGLE_FILE_SIZE_ALLOWED)
			{
				$validator ->setError("The Other File you are trying to upload is very large. It's size must be less than ".MAXIMUM_SINGLE_FILE_SIZE_ALLOWED_TEXT.". Please reduce the filesize by removing large pictures etc.");
			}
		}
		
		$validator ->checkField($replyInstructions,"","Please enter instructions.");
		$validator ->checkField($resendingReason,"","Please enter explanation on resending files.");
		
		$dataValid	 =	$validator ->isDataValid();
		if($dataValid)
		{	
			$chargedFromType 		=   "";
			if(!empty($isPaidThroughWallet)){
				$chargedFromType 	=   " (Wallet)";
			}
			elseif(!empty($prepaidTransactionId) && $prepiadPaymentThrough == "paypal"){
				$chargedFromType 	=   " (PayPal)";
			}
			else{
				$chargedFromType 	=   " (Credit card)";
			}
			$replyOrderFilePath			 =	SITE_ROOT_FILES."/files/orderFiles/$folder/";
			
			$replyPublicRecordFilePath	 =	SITE_ROOT_FILES."/files/publicRecordFile/$folder/";
		
			$replyMlsFilePath			 =	SITE_ROOT_FILES."/files/mls/$folder/";

			$replyMarketConditionFilePath=	SITE_ROOT_FILES."/files/marketCondition/$folder/";
			
			$otherFilePath				 =	SITE_ROOT_FILES."/files/otherFiles/$folder/";

			$baseConvertUniqueEmailCode  = base64_encode($memberUniqueEmailCode);
			$base_fileId				 = "";

			$query	= "UPDATE members_orders_reply SET replyInstructions='$replyInstructions',replyAdminMessageId=$replyAdminMessageId WHERE orderId=$orderId AND replyId=$replyId AND memberId=$customerId";
			dbQuery($query);

			$orderObj->addOrderTracker($s_employeeId,$orderId,$orderAddress,'Employee Re-upload Of Files In Reply Order Of Completed Order','EMPLOYEE_REUPLOAD_ORDEE_FILES');

			$performedTask	=	"Re-upload Of Files In Reply Order Of Completed Order ".$orderAddress." With Reply ID - ".$replyId;
			$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);


			if(!empty($_FILES['replyOrderFile']['name']))
			{
				if($isRepliedWithNewSystem == 1 && $isNewUploadingSystem == 1)
				{
					$orderObj->deleteReplyFilesToEmployee($orderId,$replyId,2,1);
				}
				
				$didUploadAnyFile	=	true;
				$uploadingFile		=   $_FILES['replyOrderFile']['name'];
				$mimeType			=   $_FILES['replyOrderFile']['type'];
				$fileSize			=   $_FILES['replyOrderFile']['size'];
				$tempName			=	$_FILES['replyOrderFile']['tmp_name'];
				$ext				=	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);

				if($isNewUploadingSystem == 1 && $isRepliedWithNewSystem == 1)
				{
					$t_uploadingFile	=	makeDBSafe($uploadingFileName);

					dbQuery("INSERT INTO order_all_files SET uploadingType=1,uploadingFor=2,orderId=$orderId,memberId=$customerId,uploadingFileName='$t_uploadingFile',uploadingFileExt='$ext',uploadingFileType='$mimeType',uploadingFileSize=$fileSize,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',customerZoneDate='".CURRENT_DATE_CUSTOMER_ZONE."',customerZoneTime='".CURRENT_TIME_CUSTOMER_ZONE."',addedFromIp='".VISITOR_IP_ADDRESS."',replyOrderId=$replyId");

					$fileId					=	mysqli_insert_id($db_conn);

					$destFileName			=	$newUploadingPath."/".$fileId."_".$uploadingFileName.".".$ext;

					move_uploaded_file($tempName,$destFileName);

					dbQuery("UPDATE order_all_files SET excatFileNameInServer='$destFileName' WHERE fileId=$fileId AND orderId=$orderId AND replyOrderId=$replyId");

					dbQuery("UPDATE members_orders_reply SET hasReplyOrderFile=1 WHERE replyId=$replyId");

					$base_fileId			=	base64_encode($fileId);

					$downLoadPath			=	SITE_URL_MEMBERS."/download-multiple-file.php?suf=".$baseConvertUniqueEmailCode."&".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;

					
					$t_replieddFileToustomer =	stringReplace("Upload ", "", $replieddFileToustomer);

					$showReplyFilesNameInEmail	.=	"<tr><td width='35%' valign='top'  align='left'><font style='font-size:10px;color:#4d4d4d;'>".$t_replieddFileToustomer."</font></td><td width='2%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>:</font></td><td valign='top' align='left'><a href='".$downLoadPath."' target='_blank'><font style='font-size:12px;color:#0082bf;'>".$uploadingFileName.".".$ext."</font></a>".getSizeNoBracket($fileSize)."</td></tr>";

					if($fileSize > 0)
					{
						$a_attachmentPath[]			=	$destFileName;
						$a_attachmentType[]			=	$mimeType;
						$a_attachmentName[]			=	$uploadingFileName.".".$ext;

						$totalAmountReplyEmailFileSize	=	$totalAmountReplyEmailFileSize+$fileSize;
					}
				}				

				//$performedTask	=	"Re-upload Of Reply Order File Of Comepleted Order ".$orderAddress." With Reply ID - ".$replyId;
				//$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);
			}

			if(!empty($_FILES['pdfCompletedFile']['name']))
			{
				if($isRepliedWithNewSystem == 1 && $isNewUploadingSystem == 1)
				{
					$orderObj->deleteReplyFilesToEmployee($orderId,$replyId,2,7);
				}
				
				$didUploadAnyFile	=	true;
				$uploadingFile		=   $_FILES['pdfCompletedFile']['name'];
				$mimeType			=   $_FILES['pdfCompletedFile']['type'];
				$fileSize			=   $_FILES['pdfCompletedFile']['size'];
				$tempName			=	$_FILES['pdfCompletedFile']['tmp_name'];
				$ext				=	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);

				if($isNewUploadingSystem == 1)
				{
					$t_uploadingFile=	makeDBSafe($uploadingFileName);

					dbQuery("INSERT INTO order_all_files SET uploadingType=7,uploadingFor=2,orderId=$orderId,memberId=$customerId,uploadingFileName='$t_uploadingFile',uploadingFileExt='$ext',uploadingFileType='$mimeType',uploadingFileSize=$fileSize,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',customerZoneDate='".CURRENT_DATE_CUSTOMER_ZONE."',customerZoneTime='".CURRENT_TIME_CUSTOMER_ZONE."',addedFromIp='".VISITOR_IP_ADDRESS."',replyOrderId=$replyId");

					$fileId			=	mysqli_insert_id($db_conn);

					$destFileName	=	$newUploadingPath."/".$fileId."_".$uploadingFileName.".".$ext;

					move_uploaded_file($tempName,$destFileName);

					dbQuery("UPDATE order_all_files SET excatFileNameInServer='$destFileName' WHERE fileId=$fileId AND orderId=$orderId AND replyOrderId=$replyId");

					dbQuery("UPDATE members_orders_reply SET hasCompletedPdfFile=1 WHERE replyId=$replyId");

					$base_fileId			=	base64_encode($fileId);

					$downLoadPath			=	SITE_URL_MEMBERS."/download-multiple-file.php?suf=".$baseConvertUniqueEmailCode."&".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;


					$showReplyFilesNameInEmail	.=	"<tr><td width='35%' valign='top'  align='left'><font style='font-size:10px;color:#4d4d4d;'>Completed Report PDF File for Reference</font></td><td width='2%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>:</font></td><td valign='top' align='left'><a href='".$downLoadPath."' target='_blank'><font style='font-size:12px;color:#0082bf;'>".$uploadingFileName.".".$ext."</font></a>".getSizeNoBracket($fileSize)."</td></tr>";
				}			
				
				//$performedTask	=	"Re-upload Of Reply Order Completed PDF File Of ".$orderAddress." With Reply ID - ".$replyId;
				//$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);
			}

			
			if(!empty($_FILES['replyPublicRecordFile']['name']))
			{
				if($isRepliedWithNewSystem == 1 && $isNewUploadingSystem == 1)
				{
					$orderObj->deleteReplyFilesToEmployee($orderId,$replyId,2,2);
				}
				
				$didUploadAnyFile	=	true;
				$uploadingFile		=   $_FILES['replyPublicRecordFile']['name'];
				$mimeType			=   $_FILES['replyPublicRecordFile']['type'];
				$fileSize			=   $_FILES['replyPublicRecordFile']['size'];
				$tempName			=	$_FILES['replyPublicRecordFile']['tmp_name'];
				$ext				=	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);

				if($isNewUploadingSystem == 1 && $isRepliedWithNewSystem == 1)
				{
					$t_uploadingFile	=	makeDBSafe($uploadingFileName);

					dbQuery("INSERT INTO order_all_files SET uploadingType=2,uploadingFor=2,orderId=$orderId,memberId=$customerId,uploadingFileName='$t_uploadingFile',uploadingFileExt='$ext',uploadingFileType='$mimeType',uploadingFileSize=$fileSize,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',customerZoneDate='".CURRENT_DATE_CUSTOMER_ZONE."',customerZoneTime='".CURRENT_TIME_CUSTOMER_ZONE."',addedFromIp='".VISITOR_IP_ADDRESS."',replyOrderId=$replyId");

					$fileId					=	mysqli_insert_id($db_conn);

					$destFileName			=	$newUploadingPath."/".$fileId."_".$uploadingFileName.".".$ext;

					move_uploaded_file($tempName,$destFileName);

					dbQuery("UPDATE order_all_files SET excatFileNameInServer='$destFileName' WHERE fileId=$fileId AND orderId=$orderId AND replyOrderId=$replyId");

					dbQuery("UPDATE members_orders_reply SET hasReplyPublicRecordFile=1 WHERE replyId=$replyId");


					$base_fileId			=	base64_encode($fileId);

					$downLoadPath			=	SITE_URL_MEMBERS."/download-multiple-file.php?suf=".$baseConvertUniqueEmailCode."&".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;


					$showReplyFilesNameInEmail	.=	"<tr><td width='35%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>Public Records File</font></td><td width='2%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>:</font></td><td valign='top' align='left'><a href='".$downLoadPath."' target='_blank'><font style='font-size:12px;color:#0082bf;'>".$uploadingFileName.".".$ext."</font></a>".getSizeNoBracket($fileSize)."</td></tr>";

					if($fileSize > 0)
					{
						$a_attachmentPath[]			=	$destFileName;
						$a_attachmentType[]			=	$mimeType;
						$a_attachmentName[]			=	$uploadingFileName.".".$ext;

						$totalAmountReplyEmailFileSize	=	$totalAmountReplyEmailFileSize+$fileSize;
					}
				}			

				//$performedTask	=	"Re-upload Of Reply Public Record File Of Comepleted Order ".$orderAddress." With Reply ID - ".$replyId;
				//$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);
			}
			if(!empty($_FILES['replyMlsFile']['name']))
			{
				if($isRepliedWithNewSystem == 1 && $isNewUploadingSystem == 1)
				{
					$orderObj->deleteReplyFilesToEmployee($orderId,$replyId,2,3);
				}
				
				$didUploadAnyFile	=	true;
				$uploadingFile		=   $_FILES['replyMlsFile']['name'];
				$mimeType			=   $_FILES['replyMlsFile']['type'];
				$fileSize			=   $_FILES['replyMlsFile']['size'];
				$tempName			=	$_FILES['replyMlsFile']['tmp_name'];
				$ext				=	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);

				if($isNewUploadingSystem == 1 && $isRepliedWithNewSystem == 1)
				{
					$t_uploadingFile	=	makeDBSafe($uploadingFileName);

					dbQuery("INSERT INTO order_all_files SET uploadingType=3,uploadingFor=2,orderId=$orderId,memberId=$customerId,uploadingFileName='$t_uploadingFile',uploadingFileExt='$ext',uploadingFileType='$mimeType',uploadingFileSize=$fileSize,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',customerZoneDate='".CURRENT_DATE_CUSTOMER_ZONE."',customerZoneTime='".CURRENT_TIME_CUSTOMER_ZONE."',addedFromIp='".VISITOR_IP_ADDRESS."',replyOrderId=$replyId");

					$fileId					=	mysqli_insert_id($db_conn);

					$destFileName			=	$newUploadingPath."/".$fileId."_".$uploadingFileName.".".$ext;

					move_uploaded_file($tempName,$destFileName);

					dbQuery("UPDATE order_all_files SET excatFileNameInServer='$destFileName' WHERE fileId=$fileId AND orderId=$orderId AND replyOrderId=$replyId");

					dbQuery("UPDATE members_orders_reply SET hasReplyMlsFile=1 WHERE replyId=$replyId");

					$base_fileId			=	base64_encode($fileId);

					$downLoadPath			=	SITE_URL_MEMBERS."/download-multiple-file.php?suf=".$baseConvertUniqueEmailCode."&".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;

					$showReplyFilesNameInEmail	.=	"<tr><td width='35%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>Plat Map</font></td><td width='2%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>:</font></td><td valign='top' align='left'><a href='".$downLoadPath."' target='_blank'><font style='font-size:12px;color:#0082bf;'>".$uploadingFileName.".".$ext."</font></a>".getSizeNoBracket($fileSize)."</td></tr>";

					if($fileSize > 0)
					{
						$a_attachmentPath[]			=	$destFileName;
						$a_attachmentType[]			=	$mimeType;
						$a_attachmentName[]			=	$uploadingFileName.".".$ext;

						$totalAmountReplyEmailFileSize	=	$totalAmountReplyEmailFileSize+$fileSize;
					}
				}

				//$performedTask	=	"Re-upload Of Reply MLS Record File Of Comepleted Order ".$orderAddress." With Reply ID - ".$replyId;
				//$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);
			}
			if(!empty($_FILES['otherFile']['name']))
			{
				if($isRepliedWithNewSystem == 1 && $isNewUploadingSystem == 1)
				{
					$orderObj->deleteReplyFilesToEmployee($orderId,$replyId,2,6);
				}
				
				$didUploadAnyFile	=	true;
				$uploadingFile		=   $_FILES['otherFile']['name'];
				$mimeType			=   $_FILES['otherFile']['type'];
				$fileSize			=   $_FILES['otherFile']['size'];
				$tempName			=	$_FILES['otherFile']['tmp_name'];
				$ext				=	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);

				if($isNewUploadingSystem == 1)
				{
					$t_uploadingFile	=	makeDBSafe($uploadingFileName);

					dbQuery("INSERT INTO order_all_files SET uploadingType=6,uploadingFor=2,orderId=$orderId,memberId=$customerId,uploadingFileName='$t_uploadingFile',uploadingFileExt='$ext',uploadingFileType='$mimeType',uploadingFileSize=$fileSize,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',customerZoneDate='".CURRENT_DATE_CUSTOMER_ZONE."',customerZoneTime='".CURRENT_TIME_CUSTOMER_ZONE."',addedFromIp='".VISITOR_IP_ADDRESS."',replyOrderId=$replyId");

					$fileId					=	mysqli_insert_id($db_conn);

					$destFileName			=	$newUploadingPath."/".$fileId."_".$uploadingFileName.".".$ext;

					move_uploaded_file($tempName,$destFileName);

					dbQuery("UPDATE order_all_files SET excatFileNameInServer='$destFileName' WHERE fileId=$fileId AND orderId=$orderId AND replyOrderId=$replyId");

					dbQuery("UPDATE members_orders_reply SET hasOtherFile=1 WHERE replyId=$replyId");

					$base_fileId			=	base64_encode($fileId);

					$downLoadPath			=	SITE_URL_MEMBERS."/download-multiple-file.php?suf=".$baseConvertUniqueEmailCode."&".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;


					$showReplyFilesNameInEmail	.=	"<tr><td width='35%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>Reply Other File</font></td><td width='2%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>:</font></td><td valign='top' align='left'><a href='".$downLoadPath."' target='_blank'><font style='font-size:12px;color:#0082bf;'>".$uploadingFileName.".".$ext."</font></a>".getSizeNoBracket($fileSize)."</td></tr>";

					if($fileSize > 0)
					{
						$a_attachmentPath[]			=	$destFileName;
						$a_attachmentType[]			=	$mimeType;
						$a_attachmentName[]			=	$uploadingFileName.".".$ext;

						$totalAmountReplyEmailFileSize	=	$totalAmountReplyEmailFileSize+$fileSize;
					}
				}				

				//$performedTask	=	"Re-upload Reply Other File Of Comepleted Order ".$orderAddress." With Reply ID - ".$replyId;
				//$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);
			}

			$showReplyFilesNameInEmail	.=	"</table>";

			//THIS SECTION SENDING EMAILS IF ANY FILES NEWLY UPLOADED
			/*******************************************************************************/
			if($didUploadAnyFile			==	true)
			{
				include(SITE_ROOT			.   "/classes/email-templates.php");
				$emailObj					=	new emails();

				if(!empty($memberOrderReplyToEmail)){
					$setThisEmailReplyToo			=	$memberOrderReplyToEmail.CUSTOMER_REPLY_EMAIL_TO;//Setting for reply to make customer reply order mesage
					$setThisEmailReplyTooName		=	"ieIMPACT Orders";//Setting for reply to make customer reply order mesage
				}
				else{
					if(!empty($orderEncryptedId))
					{
						$setThisEmailReplyToo	  =	 $orderEncryptedId.CUSTOMER_REPLY_EMAIL_TO;//Setting for reply to make customer reply order mesage
						$setThisEmailReplyTooName =	 "ieIMPACT Orders";//Setting for reply to make customer reply order mesage
					}
				}
								
				$totalCustomerOrders		=	$orderObj->getCustomerTotalCompletedOrders($customerId);
				if(empty($totalCustomerOrders))
				{
					$totalCustomerOrders	=	0;
				}
				if($totalCustomerOrders		<= 3)
				{
					$needFeedBackMessage	=	"<b>Need your attention :</b> We need your feedback in order to complete this order.<br>";
				}
				else
				{
					$needFeedBackMessage	=	"";
				}

				$hasAttachment			=	0;
				$sendingFileAttachmentMsg=	"";

				if($isReplyFileInEmail	==	1)
				{
					if(!empty($a_attachmentPath))
					{						
						if(!empty($totalAmountReplyEmailFileSize) && $totalAmountReplyEmailFileSize <= 7340032)
						{
							$hasAttachment			 =	1;
							$sendingFileAttachmentMsg=	"Please note: Files are also attached in this email.";
						}
						else
						{
							$sendingFileAttachmentMsg	=	"Note: Failed to send files by email becuase size was greater than 7mb.";
							$hasAttachment		=	0;
							$a_attachmentPath	=	array();
							$a_attachmentType	=	array();
							$a_attachmentName	=	array();
						}
					}
				}
				$trackEmailImage			=	"images/white-space.jpg";
				$replyInstructions			=	stripslashes($replyInstructions);
				$orderAddress				=	stripslashes($orderAddress);
				$replyInstructions			=	nl2br($replyInstructions);
				$resendingReason			=	stripslashes($resendingReason);

				$explainationInEmail		=	"<table width='99%' align='center' border='0' cellpadding='2' cellspacing='2'><tr><td align='left'><font style='font-size:11px;font-weight:bold;color:#6E6E6E;'>EXPLANATION OF RESENDING</font></td></tr><tr><td align='left'><font style='font-size:11px;color:#333333;'>".nl2br($resendingReason)."</font></td></tr></table><br />";

				$walletAmountEmail			=	$employeeObj->getSingleQueryResult("SELECT amount FROM wallet_master WHERE memberId=$customerId","amount");
				if(!empty($walletAmountEmail)){
					$walletAmountEmail      =  "$".displayMoneyExpo($walletAmountEmail);
				}
				else{
					$walletAmountEmail      =  "$0.00";
				}

				$orderExactCost 			=	$employeeObj->getSingleQueryResult("SELECT postOrderCost FROM members_orders WHERE orderId=$orderId AND memberId=$customerId","postOrderCost");
				if(!empty($walletAmountEmail)){
					$orderExactCost         =  "$".displayMoneyExpo($orderExactCost);
				}
				else{
					$orderExactCost         =  "$0.00";
				}

				$orderExactCost             =  $orderExactCost.$chargedFromType;

				if($hasReceiveEmails		== 0)
				{
					/////////////////// START OF SENDING EMAIL BLOCK/////////////////////////

					$referFriendLink		 =   "";

					$excellentLink		=	SITE_URL_MEMBERS."/rate-this-order.php?".ORDERID_M_D_5."=".$orderEncryptedId."&code=".$encodeOrderID."&rate=5";

					$goodLink			=	SITE_URL_MEMBERS."/rate-this-order.php?".ORDERID_M_D_5."=".$orderEncryptedId."&code=".$encodeOrderID."&rate=4";

					$fairLink			=	SITE_URL_MEMBERS."/rate-this-order.php?".ORDERID_M_D_5."=".$orderEncryptedId."&code=".$encodeOrderID."&rate=3";

					$poorLink			=	SITE_URL_MEMBERS."/rate-this-order.php?".ORDERID_M_D_5."=".$orderEncryptedId."&code=".$encodeOrderID."&rate=2";

					$awfulLink			=	SITE_URL_MEMBERS."/rate-this-order.php?".ORDERID_M_D_5."=".$orderEncryptedId."&code=".$encodeOrderID."&rate=1";

					if($cutomerTotalOrdersPlaced > 3)
					{
						$referFriendLink	 =   "<a href='".SITE_URL_MEMBERS."/refer-a-friend.php' target='_blank'><img src='".SITE_URL."/images/refer_a_friend-new.jpg' alt='Refer 1 Get 1' title='Refer 1 Get 1' border='0' width='800px' height='90px'></a>";
					}

					$sendingFileAttachmentMsg .=	$sendingFileAttachmentMsg."<br />Please reply to this email or please use the email address below for any feedback or support with this order.<br />
					Tracking Email Address for this order : ".$setThisEmailReplyToo;
									
					$a_templateData			=	array("{name}"=>$customerName,"{orderNo}"=>$orderAddress,"{orderType}"=>$orderText,"{instructions}"=>$replyInstructions,"{needFeedBackMessage}"=>$needFeedBackMessage,"{showFilesNameInEmail}"=>$explainationInEmail.$showReplyFilesNameInEmail,"{trackEmailImage}"=>$trackEmailImage,"{sendingFileAttachmentMsg}"=>$sendingFileAttachmentMsg,"{referFriendLink}"=>$referFriendLink,"{excellentLink}"=>$excellentLink,"{goodLink}"=>$goodLink,"{fairLink}"=>$fairLink,"{poorLink}"=>$poorLink,"{awfulLink}"=>$awfulLink,"{walletAmountEmail}"=>$walletAmountEmail,"{priceCharged}"=>$orderExactCost);

					$subjectMsg				=	$orderAddress." (Updated)";

					$a_templateSubject		=	array("{orderAddress}"=>$subjectMsg);

					$uniqueTemplateName		=	"TEMPLATE_SENDING_CUSTOMER_ORDER_REPLY_FILE_ATTACHMENT";
					$toEmail				=	$customerEmail;
					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

					if(!empty($customerSecondaryEmail))
					{
						$a_templateData			=	array("{name}"=>$customerName,"{orderNo}"=>$orderAddress,"{orderType}"=>$orderText,"{instructions}"=>$replyInstructions,"{needFeedBackMessage}"=>$needFeedBackMessage,"{showFilesNameInEmail}"=>$explainationInEmail.$showReplyFilesNameInEmail,"{trackEmailImage}"=>$trackEmailImage,"{sendingFileAttachmentMsg}"=>$sendingFileAttachmentMsg,"{referFriendLink}"=>$referFriendLink,"{excellentLink}"=>$excellentLink,"{goodLink}"=>$goodLink,"{fairLink}"=>$fairLink,"{poorLink}"=>$poorLink,"{awfulLink}"=>$awfulLink,"{walletAmountEmail}"=>$walletAmountEmail,"{priceCharged}"=>$orderExactCost);

						$subjectMsg				=	$orderAddress." (Updated)";

						$a_templateSubject		=	array("{orderAddress}"=>$subjectMsg);

						$uniqueTemplateName		=	"TEMPLATE_SENDING_CUSTOMER_ORDER_REPLY_FILE_ATTACHMENT";
						$toEmail				=	$customerSecondaryEmail;
						include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
					}
				}
				$hasAttachment				=	0;
				$setThisEmailReplyToo		=	"";//Setting for reply to make empty to manager
				$setThisEmailReplyTooName	=	"";//Setting for reply to make empty to manager
				$a_managerEmails			=	$orderObj->getMangersOnlyEmails();
				
				
				if(!empty($a_managerEmails))
				{
					$t_orderAddedOn			=	showDate($orderAddedOn);

					$a_managerEmails	    =	stringReplace(',john@ieimpact.net','',$a_managerEmails);

					$explainationInEmail		=	"<table width='99%' align='center' border='0' cellpadding='2' cellspacing='2'><tr><td align='left'><font style='font-size:11px;font-weight:bold;color:#6E6E6E;'>EXPLANATION OF RESENDING</font></td></tr><tr><td align='left'><font style='font-size:11px;color:#333333;'>".nl2br($resendingReason)."</font></td></tr><tr><td height='5'></td></tr><tr><td align='left'><font style='font-size:14px;font-weight:bold;color:#808000;'>Resent By Employee : ".$s_employeeName."</font></td></tr></table><br />";
										
					$a_templateData			=	array("{managerName}"=>"Manager","{instructions}"=>$replyInstructions,"{orderNo}"=>$orderAddress,"{orderDate}"=>$t_orderAddedOn,"{orderType}"=>$orderText,"{customerName}"=>$customerName,"{acceptedBy}"=>$acceptedByName,"{qaDoneBy}"=>$qaDoneByText,"{showFilesNameInEmail}"=>$explainationInEmail.$showReplyFilesNameInEmail,"{trackEmailImage}"=>$trackEmailImage,"{sendingFileAttachmentMsg}"=>$sendingFileAttachmentMsg,"{excellentLink}"=>$excellentLink,"{goodLink}"=>$goodLink,"{fairLink}"=>$fairLink,"{poorLink}"=>$poorLink,"{awfulLink}"=>$awfulLink,"{walletAmountEmail}"=>$walletAmountEmail,"{priceCharged}"=>$orderExactCost);

					//$subjectMsg				=	$orderAddress." (Updated)";

					//$a_templateSubject		=	array("{orderAddress}"=>$subjectMsg,"{customerName}"=>$customerName);

					$managerEmployeeEmailSubject	= "Completed Order from ".$customerName.", ".$orderAddress." on ".showDate($customer_zone_date)." (Updated)";

					$uniqueTemplateName		=	"TEMPLATE_SENDING_ORDER_REPLY_TO_MANAGER";
					$toEmail				=	DEFAULT_BCC_EMAIL;
					$managerEmployeeFromBcc =	$a_managerEmails;
					

					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
					
				}
			}

			/*******************************************************************************/

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES ."/re-send-pdf-order.php?orderId=$orderId&customerId=$customerId&success=1");
			exit();
		}
		else
		{
			echo $errorMsg	=	$validator->getErrors();
		}
	}
	include($form);
	include(SITE_ROOT_EMPLOYEES . "/includes/next-previous-order.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>