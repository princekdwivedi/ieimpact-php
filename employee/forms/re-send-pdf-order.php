<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");
	$employeeObj				=  new employee();
	$orderObj					=  new orders();
	$validator					=  new validate();
	$orderAcceptedBy			=	0;
	$qaDoneBy					=	0;
	
	$a_orderAdminReplyMessages	=	array();
	$hasAdminMessage			=   0;
	

	$query						=	"SELECT * FROM admin_added_customer_messages WHERE section=2 ORDER BY message";
	$result						=	dbQuery($query);
	if(mysql_num_rows($result))
	{
		$hasAdminMessage					=	1;
		$a_orderAdminReplyMessages['-1']	=	"<font color='#ff0000'>Not found suitable instructions, add own message</font>";
		while($row							=	mysql_fetch_assoc($result))
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
		if(!in_array($customerId,$a_orderCustomers))
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
			exit();
		}
		else
		{
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
				if($s_employeeId != $orderAcceptedBy && empty($s_hasManagerAccess) && $s_employeeId != $qaDoneBy)
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
	$adminMessadeId		=	@mysql_result(dbQuery("SELECT replyAdminMessageId FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId"),0);
	if(empty($adminMessadeId))
	{
		$adminMessadeId	=	0;
	}
	$form		=	SITE_ROOT_EMPLOYEES."/forms/resend-pdf-order.php";
?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
<tr>
	<td colspan="7" height="20"></td>
</tr>
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
	include(SITE_ROOT_EMPLOYEES	. "/includes/view-customer-order.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/view-feedback-details.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/view-messages-details.php");
	
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
		$fileExtPos		=  strrpos($fileName, '.');
		$fileName		=  substr($fileName,0,$fileExtPos);
		
		return $fileName;
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


	$folder						=	@mysql_result(dbQuery("SELECT folderId FROM members WHERE memberId=$customerId"),0);

	$query		=	"SELECT * FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId";
	$result		=	dbQuery($query);
	if(mysql_num_rows($result))
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

		$replyOrderFileSize		=	$row['replyOrderFileSize'];
		$replyPublicRecordSize	=	$row['replyPublicRecordSize'];
		$replyMlsFileSize		=	$row['replyMlsFileSize'];
		$replyOtherFileSize		=	$row['replyOtherFileSize'];
		$replyMarketConditionFileSize	=	$row['replyMarketConditionFileSize'];

		$replyInstructions		=	$row['replyInstructions'];
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
		$replyInstructions	=	addslashes($replyInstructions);
		if(!empty($_FILES['replyOrderFile']['name']))
		{
			$uploadingFile	=   $_FILES['replyOrderFile']['name'];
			$ext			=	findexts($uploadingFile);
			if($appraisalSoftwareType == 1)
			{
				if($ext		!=	"zap")
				{
					$validator ->setError("Please Only ".$replieddFileToustomer." !!");
				}
			}
			elseif($appraisalSoftwareType == 2)
			{
				if($ext		!=	"aci")
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
				if($ext		!=	"rpt")
				{
					$validator ->setError("Please Only ".$replieddFileToustomer." !!");
				}
			}
		}
		$validator ->checkField($replyInstructions,"","Please enter instructions !!");
		$dataValid	 =	$validator ->isDataValid();
		if($dataValid)
		{	
			$replyOrderFilePath	=	SITE_ROOT_FILES."/files/orderFiles/$folder/";
			
			$replyPublicRecordFilePath	=	SITE_ROOT_FILES."/files/publicRecordFile/$folder/";
		
			$replyMlsFilePath	=	SITE_ROOT_FILES."/files/mls/$folder/";

			$replyMarketConditionFilePath=	SITE_ROOT_FILES."/files/marketCondition/$folder/";
			
			$otherFilePath			=	SITE_ROOT_FILES."/files/otherFiles/$folder/";

			$query	= "UPDATE members_orders_reply SET replyInstructions='$replyInstructions',replyAdminMessageId=$replyAdminMessageId WHERE orderId=$orderId AND replyId=$replyId AND memberId=$customerId";
			dbQuery($query);

			$performedTask	=	"Re-upload Of Files In Reply Order Of Completed Order ".$orderAddress." With Reply ID - ".$replyId;
			$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);

			if(!empty($_FILES['replyOrderFile']['name']))
			{
				if(file_exists($replyOrderFilePath.$orderId."_".$replyId."_".$replyOrderFileName.".".$replyOrderFileExt))
				{
					$file1	=	$orderId."_".$replyId."_".$replyOrderFileName.".".$replyOrderFileExt;
					myChmod($replyOrderFilePath."$file1",0664);
					
					unlink($replyOrderFilePath.$orderId."_".$replyId."_".$replyOrderFileName.".".$replyOrderFileExt);
				}
				$uploadingFile		=   $_FILES['replyOrderFile']['name'];
				$mimeType			=   $_FILES['replyOrderFile']['type'];
				$fileSize			=   $_FILES['replyOrderFile']['size'];
				$tempName			=	$_FILES['replyOrderFile']['tmp_name'];
				$ext				=	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);


				$fileName		= $orderId."_".$replyId."_".$uploadingFileName.".".$ext;
				



				move_uploaded_file($tempName,$replyOrderFilePath.$fileName);
				myChmod($replyOrderFilePath."$fileName",0664);
				
				dbQuery("UPDATE members_orders_reply SET hasReplyOrderFile=1,replyOrderFileExt='$ext',replyOrderFileName='$uploadingFileName',replyOrderMimeType='$mimeType',replyOrderFileSize=$fileSize WHERE replyId=$replyId");

				$performedTask	=	"Re-upload Of Reply Order File Of Comepleted Order ".$orderAddress." With Reply ID - ".$replyId;
				$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);
			}
			if(!empty($_FILES['replyPublicRecordFile']['name']))
			{
				if(file_exists($replyPublicRecordFilePath.$orderId."_".$replyId."_".$replyPublicRecordFileName.".".$replyPublicRecordFileExt))
				{
					$file2	=	$orderId."_".$replyId."_".$replyPublicRecordFileName.".".$replyPublicRecordFileExt;
					myChmod($replyPublicRecordFilePath."$file2",0664);
					
					unlink($replyPublicRecordFilePath.$orderId."_".$replyId."_".$replyPublicRecordFileName.".".$replyPublicRecordFileExt);
				}
				
				$uploadingFile		=   $_FILES['replyPublicRecordFile']['name'];
				$mimeType			=   $_FILES['replyPublicRecordFile']['type'];
				$fileSize			=   $_FILES['replyPublicRecordFile']['size'];
				$tempName			=	$_FILES['replyPublicRecordFile']['tmp_name'];
				$ext				=	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);

				$fileName			= $orderId."_".$replyId."_".$uploadingFileName.".".$ext;

				move_uploaded_file($tempName,$replyPublicRecordFilePath.$fileName);
				myChmod($replyPublicRecordFilePath."$fileName",0664);
				
				dbQuery("UPDATE members_orders_reply SET hasReplyPublicRecordFile=1,replyPublicRecordFileExt='$ext',replyPublicRecordFileName='$uploadingFileName',replyPublicRecordMimeType='$mimeType',replyPublicRecordSize=$fileSize WHERE replyId=$replyId");

				$performedTask	=	"Re-upload Of Reply Public Record File Of Comepleted Order ".$orderAddress." With Reply ID - ".$replyId;
				$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);
			}
			if(!empty($_FILES['replyMlsFile']['name']))
			{
				if(file_exists($replyMlsFilePath.$orderId."_".$replyId."_".$replyMlsFileName.".".$replyMlsFileExt))
				{
					$file3	=	$orderId."_".$replyId."_".$replyMlsFileName.".".$replyMlsFileExt;
					myChmod($replyMlsFilePath."$file3",0664);
					
					unlink($replyMlsFilePath.$orderId."_".$replyId."_".$replyMlsFileName.".".$replyMlsFileExt);
				}
				
				$uploadingFile		=   $_FILES['replyMlsFile']['name'];
				$mimeType			=   $_FILES['replyMlsFile']['type'];
				$fileSize			=   $_FILES['replyMlsFile']['size'];
				$tempName			=	$_FILES['replyMlsFile']['tmp_name'];
				$ext				=	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);

				$fileName			= $orderId."_".$replyId."_".$uploadingFileName.".".$ext;

				move_uploaded_file($tempName,$replyMlsFilePath.$fileName);
				myChmod($replyMlsFilePath."$fileName",0664);
		
				dbQuery("UPDATE members_orders_reply SET hasReplyMlsFile=1,replyMlsFileExt='$ext',replyMlsFileName='$uploadingFileName',replyMlsMimeType='$mimeType',replyMlsFileSize=$fileSize WHERE replyId=$replyId");

				$performedTask	=	"Re-upload Of Reply MLS Record File Of Comepleted Order ".$orderAddress." With Reply ID - ".$replyId;
				$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);
			}
			if(!empty($_FILES['otherFile']['name']))
			{
				if(file_exists($otherFilePath.$orderId."_".$replyId."_".$otherFileName.".".$otherFileExt))
				{
					$file4	=	$orderId."_".$replyId."_".$otherFileName.".".$otherFileExt;
					myChmod($otherFilePath."$file4",0664);
					
					unlink($otherFilePath.$orderId.$replyId."_".$otherFileName.".".$otherFileExt);
				}
				
				$uploadingFile		=   $_FILES['otherFile']['name'];
				$mimeType			=   $_FILES['otherFile']['type'];
				$fileSize			=   $_FILES['otherFile']['size'];
				$tempName			=	$_FILES['otherFile']['tmp_name'];
				$ext				=	findexts($uploadingFile);
				$uploadingFileName	=	getFileName($uploadingFile);

				$fileName			= $orderId."_".$replyId."_".$uploadingFileName.".".$ext;

				move_uploaded_file($tempName,$otherFilePath.$fileName);
				myChmod($otherFilePath."$fileName",0664);
		
		
				dbQuery("UPDATE members_orders_reply SET hasOtherFile=1,otherFileExt='$ext',otherFileName='$uploadingFileName',replyOtherFileMimeType='$mimeType',replyOtherFileSize=$fileSize WHERE replyId=$replyId");

				$performedTask	=	"Re-upload Reply Other File Of Comepleted Order ".$orderAddress." With Reply ID - ".$replyId;
				$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);
			}

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES ."/re-send-pdf-order.php?orderId=$orderId&customerId=$customerId&success=1");
			exit();
		}
	}
	include($form);
	include(SITE_ROOT_EMPLOYEES . "/includes/next-previous-order.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>