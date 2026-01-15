<?php
	ob_start();
	session_start();
	//ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/new-top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");
	$employeeObj				=  new employee();
	$memberObj					=  new members();
	$orderObj					=  new orders();
	$validator					=  new validate();
	$orderAcceptedBy			=	0;
	$a_orderAdminReplyMessages	=	array();
	$hasAdminMessage			=   0;
	$errorMsg					=	"";
	list($currentY,$currentM,$currentD)	=	explode("-",$nowDateIndia);
	$calculateCustomerAverageTime=   0;
	$formSearch					=	SITE_ROOT_EMPLOYEES."/forms/search-general-order-form.php";	

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
		$query			=	"SELECT status,acceptedBy FROM members_orders WHERE orderId=$orderId AND memberId=$customerId";
		$result			=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$row			=	mysqli_fetch_assoc($result);
			$orderStatus	=   $row['status'];
			$orderAcceptedBy=   $row['acceptedBy'];
		}
		$isManger		=	$employeeObj->isEmployeeManager($orderAcceptedBy);
		$isExistsReply	=	$employeeObj->getSingleQueryResult("SELECT replyId FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId","replyId");
		if(empty($isExistsReply))
		{
			$isExistsReply	=	0;
		}

		if($isManger == 1)
		{
			if(empty($isExistsReply))
			{			
				if(empty($s_hasManagerAccess))
				{
					ob_clean();
					header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
					exit();
				}
			}
			else
			{
				if(empty($s_hasManagerAccess) && $isHavingEmployeeQaAccess != 1)
				{
					ob_clean();
					header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
					exit();
				}
			}
		}
		else
		{
			if(empty($isExistsReply))
			{
				if(empty($s_hasManagerAccess) && $s_employeeId != $orderAcceptedBy)
				{
					ob_clean();
					header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
					exit();
				}
			}
			else
			{
				if(empty($s_hasManagerAccess) && $s_employeeId != $orderAcceptedBy && $isHavingEmployeeQaAccess != 1)
				{
					ob_clean();
					header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
					exit();
				}
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

	$form		=	SITE_ROOT_EMPLOYEES."/forms/reply-pdf-order-time-spent.php";
	$a_allDeactivatedEmployees 		    =	$employeeObj->getAllInactiveEmployees();
?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
	<tr>
		<td colspan="8" class="heading1">
			:: PROCESS CUSTOMER ORDER ::
		</td>
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

	$hasReplyOrderFile					=	0;
	$hasReplyPublicRecordFile			=	0;
	$hasReplyMlsFile					=	0;
	$hasOtherFile						=	0;
	$replyId							=	0;
	$hasCompletedPdfFile				=	0;

	$replyOrderFileExt					=	"";
	$hasReplyPublicRecordFile			=	"";
	$replyPublicRecordFileExt			=	"";
	$replyMlsFileExt					=	"";
	$hasReplyMarketCondition			=	"";
	$replyMarketConditionExt			=	"";
	$otherFileExt						=	"";
	$compltetedPdfFileExt				=	"";

	$replyOrderFileName					=	"";
	$replyPublicRecordFileName			=	"";
	$replyMlsFileName					=	"";
	$replyMarketConditionFileName		=	"";
	$otherFileName						=	"";
	$compltetedPdfFileName				=	"";

	$replyOrderFileSize					=	"";
	$replyPublicRecordSize				=	"";
	$replyMlsFileSize					=	"";
	$replyOtherFileSize					=	"";
	$replyMarketConditionFileSize		=	"";
	$compltetedPdfFileSize				=	"";

	$replyInstructions					=	"";
	$hasAddedReplyInstructions			=	0;

	$commentsToQa						=   "";
	$timeSpentEmployee					=	"";

	$numberOfCompsFilled				=	0;//$checkedCompsFiles;
	$orderInteralNotes					=	"";//$checkEmployeeNotes;


	$folder								=	$folderId;

	$query								=	"SELECT * FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId";
	$result								=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		$row							=	mysqli_fetch_assoc($result);
		$replyId						=	$row['replyId'];
		$hasReplyOrderFile				=	$row['hasReplyOrderFile'];
		$hasReplyPublicRecordFile		=	$row['hasReplyPublicRecordFile'];
		$hasReplyMlsFile				=	$row['hasReplyMlsFile'];
		$hasReplyMarketCondition		=	$row['hasReplyMarketCondition'];
		$hasOtherFile					=	$row['hasOtherFile'];
		
		$replyInstructions				=	$row['replyInstructions'];
		$isRepliedWithNewSystem			=	$row['isRepliedWithNewSystem'];

		$hasCompletedPdfFile			=	$row['hasCompletedPdfFile'];
		$numberOfCompsFilled			=	$row['numberOfCompsFilled'];
		if(empty($numberOfCompsFilled))
		{
			$numberOfCompsFilled		=	"";
		}
		$orderInteralNotes				=	stripslashes($row['orderInteralNotes']);


		if(!empty($replyInstructions))
		{
			$hasAddedReplyInstructions	=	1;
		}

		$commentsToQa					=   $row['commentsToQa'];
		$timeSpentEmployee				=	$row['timeSpentEmployee'];

		if(isset($_GET['uploadingType']) && isset($_GET['isDeleteMultipleFile']) && isset($_GET['processFileId']) && $isRepliedWithNewSystem == 1)
		{
			$uploadingType				=	$_GET['uploadingType'];
			$isDeleteMultipleFile		=	$_GET['isDeleteMultipleFile'];
			$processFileId				=	$_GET['processFileId'];

			if(!empty($uploadingType) && !empty($processFileId) && $isDeleteMultipleFile	==	1)
			{
				$orderObj->deleteProcessOrderFile($orderId,$processFileId,2,$uploadingType);
				if($uploadingType		==	1)
				{
					
					dbQuery("UPDATE members_orders_reply SET hasReplyOrderFile=0,orderReplyFileMd5HasSize='' WHERE replyId=$replyId AND orderId=$orderId");
					
				}
				elseif($uploadingType	==	2)
				{
					dbQuery("UPDATE members_orders_reply SET hasReplyPublicRecordFile=0 WHERE replyId=$replyId AND orderId=$orderId");
				}
				elseif($uploadingType	==	3)
				{
					dbQuery("UPDATE members_orders_reply SET hasReplyMlsFile=0 WHERE replyId=$replyId AND orderId=$orderId");
				}
				elseif($uploadingType	==	6)
				{
					dbQuery("UPDATE members_orders_reply SET hasOtherFile=0 WHERE replyId=$replyId AND orderId=$orderId");
				}
				elseif($uploadingType	==	7)
				{
					dbQuery("UPDATE members_orders_reply SET hasCompletedPdfFile=0 WHERE replyId=$replyId AND orderId=$orderId");
				}
			}

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES ."/process-pdf-order.php?orderId=$orderId&customerId=$customerId#process");
			exit();
		}
		
	}

	if(!empty($calculateCustomerAverageTime)){
		$timeSpentEmployee	        =   $calculateCustomerAverageTime;
	}
	else{
		$timeSpentEmployee	        =	timeBetweenTwoTimes($orderAcceptedDateByEmployee,$orderAcceptedTimeByEmployee,$nowDateIndia,$nowTimeIndia);
	}
	

	$timeSpentEmployee	            =	round($timeSpentEmployee,0);

	if(!$a_existingProcessChecklist	=	$orderObj->getProcesedEmployeeChecklistMarked($orderId))
	{		
		$a_existingProcessChecklist	=	array();
	}

	if(isset($_GET['success']))
	{
	?>
	<table width="100%" align="center" border="0" cellpadding="2" cellspacing="2">
			<tr>
				<td class="smalltext10" align="center">
						<b>You have Successfuly Added Reply Files For This Order</b>.<br><br>
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
		$replyInstructions			=	makeDBSafe($replyInstructions);
		$commentsToQa				=	makeDBSafe($commentsToQa);
		$timeSpentEmployee   		=	makeDBSafe($timeSpentEmployee);
		$orderInteralNotes			=	makeDBSafe($orderInteralNotes);
		///////////////////// CHECK IS ORDER QA DONE /////////////////////////////////
		$currentOrderStatus			=	$employeeObj->getSingleQueryResult("SELECT status FROM members_orders WHERE orderId=$orderId AND memberId=$customerId","status");
		if($currentOrderStatus == 2 || $currentOrderStatus == 5 || $currentOrderStatus == 6){
			$validator->setError("This order has been already QA done, please resend files to cutomer.");
		}
		else{

			if($isChecklistAvailabale	==	1)
			{
				if(isset($_POST['readChecklist']))
				{
					$a_readChecklist	=	$_POST['readChecklist'];

					$countTotalChecked	=	count($a_readChecklist);

					if($countTotalChecked < $totalChecklistExists)
					{
						$validator->setError("Please complete the checklist.");
					}

				}
				else
				{
					$validator->setError("Please complete the checklist.");
				}
				
			}
			else
			{
				$a_readChecklist	=	array();
			}

			$validator ->checkField($numberOfCompsFilled,"","Please enter number of comps filled.");
			$validator ->checkField($orderInteralNotes,"","Please enter internal employee notes.");
			
			if(!empty($_FILES['pdfCompletedFile']['name']))
			{
				$uploadingPdfFile		=   $_FILES['pdfCompletedFile']['name'];
				$uploadingPdfFileSize	=   $_FILES['pdfCompletedFile']['size'];
				$pdfExt					=	findexts($uploadingPdfFile);

				if($pdfExt	!= "pdf")
				{
					$validator ->setError("Please upload a PDF File of the completd file.");
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
					$validator ->setError("Please upload a PDF File of the Completd File.");
				}
			}

			if(empty($replyId))
			{
				if(empty($_FILES['replyOrderFile']['name']) && empty($_FILES['replyPublicRecordFile']['name']) && empty($_FILES['replyMlsFile']['name']) && empty($_FILES['otherFile']['name']))
				{
					$validator ->setError("Please Upload At Least One Reply File.");
				}

			}
			if(!empty($_FILES['replyOrderFile']['name']))
			{
				$uploadingFile				=   $_FILES['replyOrderFile']['name'];
				$uploadingFileSize			=   $_FILES['replyOrderFile']['size'];
				$tempName					=	$_FILES['replyOrderFile']['tmp_name'];
				$orderReplyFileMd5HasSize	=	md5_file($tempName);
				//$foundExistsMd5FileFize		=	$orderObj->checkExistingMd5HasOrderReplyFile($orderReplyFileMd5HasSize);
				if(!empty($orderFileSize)){
					$replyFileExistingMatch =	$orderObj->checkRepliedFileChecksum($orderReplyFileMd5HasSize,$uploadingFileSize,$orderFileSize,$orderId);
				}
				else{
					$replyFileExistingMatch = "";
				}
				
							
				$ext				=	findexts($uploadingFile);
				if($appraisalSoftwareType == 1)
				{
					if($ext		!=	"zap")
					{
						$validator ->setError("Please Only ".$replieddFileToustomer.".");
					}
				}
				elseif($appraisalSoftwareType == 2)
				{
					if($ext		!=	"aci" && $ext		!=	"zoo")
					{
						$validator ->setError("Please Only ".$replieddFileToustomer.".");
					}
				}
				elseif($appraisalSoftwareType == 3)
				{
					if($ext		!=	"clk")
					{
						$validator ->setError("Please Only ".$replieddFileToustomer.".");
					}
				}
				elseif($appraisalSoftwareType == 4)
				{
					if($ext		!=	"rpt" && $ext		!=	"rptx")
					{
						$validator ->setError("Please Only ".$replieddFileToustomer.".");
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
				elseif($uploadingFileSize > MAXIMUM_SINGLE_FILE_SIZE_ALLOWED)
                {
					$validator ->setError("The Template File you are trying to upload is very large. It's size must be less than ".MAXIMUM_SINGLE_FILE_SIZE_ALLOWED_TEXT.". Please reduce the filesize by removing large pictures etc.");
				}
				if(!empty($replyFileExistingMatch))
				{
					$validator ->setError($replyFileExistingMatch.".");
				}
				/*if(!empty($foundExistsMd5FileFize))
				{
					$validator ->setError($foundExistsMd5FileFize.".");
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
				$otherFileSize			=   $_FILES['otherFile']['size'];

				if($otherFileSize > MAXIMUM_SINGLE_FILE_SIZE_ALLOWED)
                {
					$validator ->setError("The Other File you are trying to upload is very large. It's size must be less than ".MAXIMUM_SINGLE_FILE_SIZE_ALLOWED_TEXT.". Please reduce the filesize by removing large pictures etc.");
				}
			}			

			$validator ->checkField($replyInstructions,"","Please enter instructions.");
			$validator ->checkField($commentsToQa,"","Please enter simple comment to QA person.");
			$validator ->checkField($timeSpentEmployee,"","Please enter time spent on this order.");
			if(!empty($timeSpentEmployee) && $timeSpentEmployee > 200){
				$validator ->setError("You cannot spend more than 200 minutes in an order.");
			}
			
		}				
		$dataValid	 =	$validator ->isDataValid();
		if($dataValid)
		{	
			$timeSpentEmployee 		    =  round($timeSpentEmployee);
			$calculateTimeSpentEmployee =  $timeSpentEmployee;
			
			$isEditedProcess            =   0;
			
			if(empty($replyId))
			{				
				$query	= "INSERT INTO members_orders_reply SET orderId=$orderId,memberId=$customerId,replyInstructions='$replyInstructions',hasRepliedFileUploaded=1,replyFileAddedOn='".CURRENT_DATE_INDIA."',replyFileAddedTime='".CURRENT_TIME_INDIA."',replyAdminMessageId=$replyAdminMessageId,isRepliedWithNewSystem=$isNewUploadingSystem,numberOfCompsFilled=$numberOfCompsFilled,orderInteralNotes='$orderInteralNotes',commentsToQa='$commentsToQa',timeSpentEmployee=$timeSpentEmployee";
				dbQuery($query);
				$replyId=	mysqli_insert_id($db_conn);
				
				dbQuery("UPDATE checked_customer_orders SET checkedCompsFiles=$numberOfCompsFilled WHERE orderId=$orderId");


				dbQuery("UPDATE members_orders SET hasRepliedUploaded=1 WHERE orderId=$orderId AND memberId=$customerId");

				$performedTask	=	"Add Reply Order Of ".$orderAddress." With Reply ID - ".$replyId;
				$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);

				if(!empty($s_hasManagerAccess) && !empty($acceptedBy) && $acceptedBy != $s_employeeId)
				{
					dbQuery("UPDATE members_orders SET acceptedBy=$s_employeeId,assignToEmployee='".CURRENT_DATE_INDIA."',assignToTime='".CURRENT_TIME_INDIA."' WHERE orderId=$orderId AND memberId=$customerId AND status=1");
				
					$performedTask	=	"Uploading file by manager in order - ".$orderId." which is accepted by ".$acceptedBy;
				
					$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);
				}

				//////////////////////////////////////////////////////////////////////////////////
				////////////// THIS IS NOW DONE BY DAILY FIFTEEN MINUTE CRON PAGE ////////////////
				//$employeeObj->makeTargetOrderProcessedQa($s_employeeId,$s_employeeName,$currentM,$currentY,1);
				//////////////////////////////////////////////////////////////////////////////////
			}
			else
			{
				$query	= "UPDATE members_orders_reply SET replyInstructions='$replyInstructions',replyAdminMessageId=$replyAdminMessageId,numberOfCompsFilled=$numberOfCompsFilled,orderInteralNotes='$orderInteralNotes',commentsToQa='$commentsToQa',timeSpentEmployee=$timeSpentEmployee WHERE orderId=$orderId AND replyId=$replyId AND memberId=$customerId";
				dbQuery($query);

				$performedTask	=	"Update Reply Order Of ".$orderAddress." With Reply ID - ".$replyId;
				$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);

				//////////////////////////// UPDATE LAST TIME ADDED TIME SPENT BY EMPLOYEE //////
				$isEditedProcess        = 1;
			}
			if($isChecklistAvailabale	==	1 && !empty($a_readChecklist))
			{
				$orderObj->setProcessEmployeeChecklistMarked($orderId,$s_employeeId,$a_readChecklist);
			}

			if(!empty($_FILES['pdfCompletedFile']['name']))
			{
				if($isRepliedWithNewSystem == 1 && $isNewUploadingSystem == 1)
				{
					$orderObj->deleteReplyFilesToEmployee($orderId,$replyId,2,7);
				}
				
				$uploadingFile		=   $_FILES['pdfCompletedFile']['name'];
				$mimeType			=   $_FILES['pdfCompletedFile']['type'];
				$fileSize			=   $_FILES['pdfCompletedFile']['size'];
				$tempName			=	$_FILES['pdfCompletedFile']['tmp_name'];
				$ext				=	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);

				if($isNewUploadingSystem == 1)
				{
					$t_uploadingFile	=	makeDBSafe($uploadingFileName);

					dbQuery("INSERT INTO order_all_files SET uploadingType=7,uploadingFor=2,orderId=$orderId,memberId=$customerId,uploadingFileName='$t_uploadingFile',uploadingFileExt='$ext',uploadingFileType='$mimeType',uploadingFileSize=$fileSize,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',customerZoneDate='".CURRENT_DATE_CUSTOMER_ZONE."',customerZoneTime='".CURRENT_TIME_CUSTOMER_ZONE."',addedFromIp='".VISITOR_IP_ADDRESS."',replyOrderId=$replyId");

					$fileId				=	mysqli_insert_id($db_conn);

					$destFileName		=	$newUploadingPath."/".$fileId."_".$uploadingFileName.".".$ext;

					move_uploaded_file($tempName,$destFileName);

					dbQuery("UPDATE order_all_files SET excatFileNameInServer='$destFileName' WHERE fileId=$fileId AND orderId=$orderId AND replyOrderId=$replyId");

					dbQuery("UPDATE members_orders_reply SET hasCompletedPdfFile=1 WHERE replyId=$replyId");
				}
				
				
				//$performedTask	=	"Adding Reply Order Completed PDF File Of ".$orderAddress." With Reply ID - ".$replyId;
				//$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);
			}

			if(!empty($_FILES['replyOrderFile']['name']))
			{
				if($isRepliedWithNewSystem == 1 && $isNewUploadingSystem == 1)
				{
					$orderObj->deleteReplyFilesToEmployee($orderId,$replyId,2,1);
				}
				
				$uploadingFile		=   $_FILES['replyOrderFile']['name'];
				$mimeType			=   $_FILES['replyOrderFile']['type'];
				$fileSize			=   $_FILES['replyOrderFile']['size'];
				$tempName			=	$_FILES['replyOrderFile']['tmp_name'];
				$ext				=	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);

				if($isNewUploadingSystem == 1)
				{
					$t_uploadingFile	=	makeDBSafe($uploadingFileName);

					dbQuery("INSERT INTO order_all_files SET uploadingType=1,uploadingFor=2,orderId=$orderId,memberId=$customerId,uploadingFileName='$t_uploadingFile',uploadingFileExt='$ext',uploadingFileType='$mimeType',uploadingFileSize=$fileSize,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',customerZoneDate='".CURRENT_DATE_CUSTOMER_ZONE."',customerZoneTime='".CURRENT_TIME_CUSTOMER_ZONE."',addedFromIp='".VISITOR_IP_ADDRESS."',replyOrderId=$replyId");

					$fileId					=	mysqli_insert_id($db_conn);

					$destFileName			=	$newUploadingPath."/".$fileId."_".$uploadingFileName.".".$ext;

					move_uploaded_file($tempName,$destFileName);

					dbQuery("UPDATE order_all_files SET excatFileNameInServer='$destFileName' WHERE fileId=$fileId AND orderId=$orderId AND replyOrderId=$replyId");

					
					$orderReplyFileMd5HasSize	 =	md5_file($destFileName);
					if(empty($orderReplyFileMd5HasSize))
					{
						$orderReplyFileMd5HasSize=	"";
					}

					dbQuery("UPDATE members_orders_reply SET hasReplyOrderFile=1,orderReplyFileMd5HasSize='$orderReplyFileMd5HasSize' WHERE replyId=$replyId");
				}
								
				//$performedTask	=	"Adding Reply Order File Of ".$orderAddress." With Reply ID - ".$replyId;
				//$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);
			}
			if(!empty($_FILES['replyPublicRecordFile']['name']))
			{
				if($isRepliedWithNewSystem == 1 && $isNewUploadingSystem == 1)
				{
					$orderObj->deleteReplyFilesToEmployee($orderId,$replyId,2,2);
				}
				
				
				$uploadingFile		=   $_FILES['replyPublicRecordFile']['name'];
				$mimeType			=   $_FILES['replyPublicRecordFile']['type'];
				$fileSize			=   $_FILES['replyPublicRecordFile']['size'];
				$tempName			=	$_FILES['replyPublicRecordFile']['tmp_name'];
				$ext				=	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);

				if($isNewUploadingSystem == 1)
				{
					$t_uploadingFile	=	makeDBSafe($uploadingFileName);

					dbQuery("INSERT INTO order_all_files SET uploadingType=2,uploadingFor=2,orderId=$orderId,memberId=$customerId,uploadingFileName='$t_uploadingFile',uploadingFileExt='$ext',uploadingFileType='$mimeType',uploadingFileSize=$fileSize,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',customerZoneDate='".CURRENT_DATE_CUSTOMER_ZONE."',customerZoneTime='".CURRENT_TIME_CUSTOMER_ZONE."',addedFromIp='".VISITOR_IP_ADDRESS."',replyOrderId=$replyId");

					$fileId					=	mysqli_insert_id($db_conn);

					$destFileName			=	$newUploadingPath."/".$fileId."_".$uploadingFileName.".".$ext;

					move_uploaded_file($tempName,$destFileName);

					dbQuery("UPDATE order_all_files SET excatFileNameInServer='$destFileName' WHERE fileId=$fileId AND orderId=$orderId AND replyOrderId=$replyId");

					dbQuery("UPDATE members_orders_reply SET hasReplyPublicRecordFile=1 WHERE replyId=$replyId");
				}
				

				//$performedTask	=	"Adding Reply Public Record File Of ".$orderAddress." With Reply ID - ".$replyId;
				//$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);
			}
			if(!empty($_FILES['replyMlsFile']['name']))
			{
				if($isRepliedWithNewSystem == 1 && $isNewUploadingSystem == 1)
				{
					$orderObj->deleteReplyFilesToEmployee($orderId,$replyId,2,3);
				}
				
				
				$uploadingFile		=   $_FILES['replyMlsFile']['name'];
				$mimeType			=   $_FILES['replyMlsFile']['type'];
				$fileSize			=   $_FILES['replyMlsFile']['size'];
				$tempName			=	$_FILES['replyMlsFile']['tmp_name'];
				$ext				=	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);

				if($isNewUploadingSystem == 1)
				{
					$t_uploadingFile	=	makeDBSafe($uploadingFileName);

					dbQuery("INSERT INTO order_all_files SET uploadingType=3,uploadingFor=2,orderId=$orderId,memberId=$customerId,uploadingFileName='$t_uploadingFile',uploadingFileExt='$ext',uploadingFileType='$mimeType',uploadingFileSize=$fileSize,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',customerZoneDate='".CURRENT_DATE_CUSTOMER_ZONE."',customerZoneTime='".CURRENT_TIME_CUSTOMER_ZONE."',addedFromIp='".VISITOR_IP_ADDRESS."',replyOrderId=$replyId");

					$fileId					=	mysqli_insert_id($db_conn);

					$destFileName			=	$newUploadingPath."/".$fileId."_".$uploadingFileName.".".$ext;

					move_uploaded_file($tempName,$destFileName);

					dbQuery("UPDATE order_all_files SET excatFileNameInServer='$destFileName' WHERE fileId=$fileId AND orderId=$orderId AND replyOrderId=$replyId");

					dbQuery("UPDATE members_orders_reply SET hasReplyMlsFile=1 WHERE replyId=$replyId");
				}
				
				//$performedTask	=	"Adding Reply MLS Record File Of ".$orderAddress." With Reply ID - ".$replyId;
				//$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);
			}
			if(!empty($_FILES['otherFile']['name']))
			{
				if($isRepliedWithNewSystem == 1 && $isNewUploadingSystem == 1)
				{
					$orderObj->deleteReplyFilesToEmployee($orderId,$replyId,2,6);
				}
				
				
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
				}
				

				//$performedTask	=	"Adding Reply Other File Of ".$orderAddress." With Reply ID - ".$replyId;
				//$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);
			}

			//////////////////////////// UPDATE ORDERS PROCESS AVEARGE TIMING ////////////////////////		
			$orderObj->updateOrderAverageTiming($orderId,$customerId,$calculateTimeSpentEmployee,$calculateCustomerAverageTime,$isEditedProcess);			
			//////////////////////////////////////////////////////////////////////////////////

			////////////////////////////////////////////////////////////////////////////////////////
			//////////////////// PUTTING THE ORDER IN ORDER TRACK LIST ////////////////////////////
		     $orderObj->addOrderTracker($s_employeeId,$orderId,$orderAddress,'Employee reply files in order','EMPLOYEE_ADDED_REPLY_FILES_ORDER');
		    ////////////////////////////////////////////////////////////////////////////////////////
		    ////////////////////////////////////////////////////////////////////////////////////////

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES ."/process-pdf-order.php?orderId=$orderId&customerId=$customerId&success=1&selectedTab=2");
			exit();
		}
		else
		{
			$errorMsg	=	$validator->getErrors();
		}
	}
	include($form);
	
	include(SITE_ROOT_EMPLOYEES . "/includes/next-previous-order.php");

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>
