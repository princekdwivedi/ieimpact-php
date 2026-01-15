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
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_SUPPORT	. "/classes/support.php");
	include(SITE_ROOT			. "/classes/common.php");
	include(SITE_ROOT			. "/includes/send-mail.php");
	$employeeObj				=  new employee();
	$memberObj					=  new members();
	$suppurtObj					=  new support();
	$commonObj					=  new common();
	include(SITE_ROOT_EMPLOYEES . "/includes/check-suport-access.php");
	$supportFilePath			=  SITE_ROOT_FILES."/files/support/";
	$supportFileUrl				=  SITE_URL."/files/support/";
	$a_supportParent			=  array();
	$a_managerEmails			=  $commonObj->getMangersEmails();
	include(SITE_ROOT_EMPLOYEES . "/includes/check-suport-access.php");
	if($result					=	$suppurtObj->getSupportCategory())
	{
		while($row			=	mysql_fetch_assoc($result))
		{
			$t_parentId		=	$row['categoryId'];
			$t_parentName	=	stripslashes($row['categoryName']);
			$a_supportParent[$t_parentId]		=	$t_parentName;
		}
	}
	$supportId				=	0;
	if(isset($_GET['ID']))
	{
		$supportId			=	(int)$_GET['ID'];
		if($result			=	$suppurtObj->getSupportDetails($supportId))
		{
			$row			=	mysql_fetch_assoc($result);
			$parentId		=	$row['parentId'];
			if(in_array($parentId,$a_supportAccessFor))
			{
				$categoryId				=	$row['categoryId'];
				$softwareId				=	$row['softwareId'];
				$invoiceNo				=	$row['invoiceNo'];
				$orderAddress			=	$row['orderAddress'];
				$onlineAccountName		=	$row['onlineAccountName'];
				$hasUploadedFile		=	$row['hasUploadedFile'];
				$fileName				=	$row['fileName'];
				$fileSize				=	$row['fileSize'];
				$ext					=	$row['ext'];
				$priority				=	$row['priority'];
				$name					=	stripslashes($row['name']);
				$email					=	stripslashes($row['email']);
				$company				=	stripslashes($row['company']);
				$phone					=	stripslashes($row['phone']);
				$city					=	stripslashes($row['city']);
				$state					=	stripslashes($row['state']);
				$country				=	$row['country'];
				$problemDescription		=	stripslashes($row['problemDescription']);
				$ticketNumber			=	$row['ticketNumber'];
				$verificationCode		=	$row['verificationCode'];
				$addedOn				=	showDate($row['addedOn']);
				$status					=	$row['status'];
				$acceptedBy				=	$row['acceptedBy'];
				$acceptedOn				=	showDate($row['acceptedOn']);
				$parentName				=	$a_supportParent[$parentId];
				$categoryName			=	$suppurtObj->getSupportCategoryName($categoryId);
				$statusText				=	"New";
				if($status				==	1)
				{
					$acceptedByEmployee	=	$employeeObj->getEmployeename($acceptedBy);
					$statusText			=	"Accepted (By : ".$acceptedByEmployee." ON ".$acceptedOn.")";
				}
				if(!empty($softwareId))
				{
					$softwareName		=	@mysql_result(dbQuery("SELECT softwareName FROM software_names WHERE softwareId=$softwareId"),0);
				}
				$countryText			=	$a_countries[$country];
				if(isset($_GET['accept']) && $_GET['accept'] == 1)
				{
					dbQuery("UPDATE support_master SET status=1,acceptedBy=$s_employeeId,acceptedOn='".CURRENT_DATE_INDIA."',acceptedTime='".CURRENT_TIME_INDIA."' WHERE status=0 AND supportId=$supportId");

					ob_clean();
					header("Location: ".SITE_URL_EMPLOYEES."/support-order-details.php?ID=$supportId");
					exit();
				}
			}
			else
			{
				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES);
				exit();
			}
		}
		else
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	function getFileSize($fileSize)
	{
		if($fileSize <= 0)
		{
			$fileSize	=	"";
		}
		else
		{
			$fileSize	=	$fileSize/1024;

			$fileSize	=	round($fileSize,2);

			$fileSize	=	$fileSize." (KB)";
		}

		return $fileSize;
	}
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
?>
<script type="text/javascript">
function acceptSupport(supportId)
{
	var confirmation = window.confirm("Are You Sure Accept This Support?");
	if(confirmation == true)
	{
		window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/support-order-details.php?ID='+supportId+"&accept=1";
	}
}
function validReplySupport()
{
	form1		=	 document.addReplySupport;
	if(form1.replySupport.value	==	"")
	{
		alert("Please reply to support !!");
		form1.replySupport.focus();
		return false;
	}
}
</script>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
	<tr>
		<td colspan="8" class="textstyle1">
			<b>SUPPORT DETAILS</b>
		</td>
	</tr>
	<tr>
		<td width="4%" class="smalltext2" valign="top">Priority</td>
		<td width="1%" class="smalltext2" valign="top">:</td>
		<td width="5%" class="smalltext2" valign="top"><b><?php echo $a_priorities[$priority];?></b></td>
		<td width="2%">&nbsp;</td>
		<td width="12%" class="smalltext2" valign="top">Support Nedded For</td>
		<td width="1%" class="smalltext2" valign="top">:</td>
		<td width="15%" class="smalltext2" valign="top"><b><?php echo $parentName;?></b></td>
		<td width="2%">&nbsp;</td>
		<td width="6%" class="smalltext2" valign="top">Category</td>
		<td width="1%" class="smalltext2" valign="top">:</td>
		<td width="18%" class="smalltext2" valign="top"><b><?php echo $categoryName;?></b></td>
		<td width="2%">&nbsp;</td>
		<td width="6%" class="smalltext2" valign="top">Status</td>
		<td width="1%" class="smalltext2" valign="top">:</td>
		<td class="error" valign="top"><b><?php echo $statusText;?></b></td>
	</tr>
	<?php
		if($categoryId  == 11 || $categoryId  == 12)
		{
			if(!empty($orderAddress))
			{
	?>
	<tr>
		<td colspan="5" class="smalltext2">Order Name or Address :</td>
		<td colspan="10" class="smalltext2"><b><?php echo $orderAddress;?></b></td>
	</tr>
	<?php
			}
			if(!empty($invoiceNo))
			{
	?>
	<tr>
		<td colspan="3" class="smalltext2">Invoice No  :</td>
		<td colspan="10" class="smalltext2"><b><?php echo $invoiceNo;?></b></td>
	</tr>
	<?php
			}
		}
		if($parentId  ==  1  && !empty($softwareId))
		{
	?>
	<tr>
		<td colspan="3" class="smalltext2">Product Name  :</td>
		<td colspan="10" class="smalltext2"><b><?php echo $softwareName;?></b></td>
	</tr>
	<?php
		}
		if($parentId  ==  3  && !empty($onlineAccountName))
		{
	?>
	<tr>
		<td colspan="3" class="smalltext2">Login Name  :</td>
		<td colspan="10" class="smalltext2"><b><?php echo $onlineAccountName;?></b></td>
	</tr>
	<?php
		}
	?>
</table>
<br>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
	<tr>
		<td class="smalltext2">Support Added On</td>
		<td class="smalltext2">:</td>
		<td class="smalltext2"><b><?php echo $addedOn;?></b></td>
	</tr>
	<tr>
		<td width="20%" class="smalltext2">Name</td>
		<td width="2%" class="smalltext2">:</td>
		<td class="smalltext2"><b><?php echo $name;?></b></td>
	</tr>
	<tr>
		<td class="smalltext2">Email</td>
		<td class="smalltext2">:</td>
		<td class="smalltext2"><b><?php echo $email;?></b></td>
	</tr>
	<tr>
		<td class="smalltext2">Comapny</td>
		<td class="smalltext2">:</td>
		<td class="smalltext2"><b><?php echo $company;?></b></td>
	</tr>
	<tr>
		<td class="smalltext2">Phone</td>
		<td class="smalltext2">:</td>
		<td class="smalltext2"><b><?php echo $phone;?></b></td>
	</tr>
	<!-- <tr>
		<td class="smalltext2">City</td>
		<td class="smalltext2">:</td>
		<td class="smalltext2"><b><?php echo $city;?></b></td>
	</tr> -->
	<tr>
		<td class="smalltext2">State/Province</td>
		<td class="smalltext2">:</td>
		<td class="smalltext2"><b><?php echo $state;?></b></td>
	</tr>
	<tr>
		<td class="smalltext2">Country</td>
		<td class="smalltext2">:</td>
		<td class="smalltext2"><b><?php echo $countryText;?></b></td>
	</tr>
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
	<?php
		if(!empty($hasUploadedFile))
		{
	?>
	<tr>
		<td class="smalltext2">Uploaded File</td>
		<td class="smalltext2">:</td>
		<td>
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/download-support-files.php?id=<?php echo $supportId;?>" class='link_style12'><?php echo $fileName.".".$ext;?></a>&nbsp;&nbsp;<font class="smalltext2">(<?php echo getFileSize($fileSize);?>)</font>
		</td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td class="smalltext2" valign="top">Ticket Number</td>
		<td class="smalltext2" valign="top">:</td>
		<td class="smalltext2" valign="top"><b><?php echo $ticketNumber;?></b></td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top">Problem Descriptions</td>
		<td class="smalltext2" valign="top">:</td>
		<td class="textstyle" valign="top"><?php echo nl2br($problemDescription);?></td>
	</tr>
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
	<?php
		if($status == 0)
		{
	?>
	<tr>
		<td colspan="3">
			<a href='javascript:acceptSupport(<?php echo $supportId;?>)' class='link_style13'>ACCEPT THIS SUPPORT</a>
		</td>
	</tr>
	<?php
		}
	?>
</table>
<?php
	$query		=	"SELECT * FROM support_replies WHERE supportId=$supportId ORDER BY supportReplyId";
	$result	=  dbQuery($query);
	if(mysql_num_rows($result))
	{
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td width="60%">
			<table width="100%" border="0" cellpadding="2" cellspacing="2" align="center">
				<tr>
					<td colspan="3" class="text">
						View Replies In This Support
					</td>
				</tr>
			<?php
					$i	=	0;
					while($row		=	mysql_fetch_assoc($result))
					{
						$i++;
						$supportReplyId		=	$row['supportReplyId'];
						$supportQuestion	=	stripslashes($row['supportQuestion']);
						$supportAnswer		=	stripslashes($row['supportAnswer']);
						$addedOn			=	showDate($row['addedOn']);
						$replyBy			=	$row['replyBy'];
						$isUploadedFile		=	$row['isUploadedFile'];
						$fileName			=	$row['fileName'];
						$fileSize			=	$row['fileSize'];
						$ext				=	$row['ext'];

						$headingText		=	"";
						$displaySupport		=	"";
						if(!empty($supportAnswer) && !empty($replyBy))
						{
							$employeeName	=	$employeeObj->getEmployeeName($replyBy);
							$headingText	=	$employeeName." replied on this support on ".$addedOn;
							$displaySupport	=	nl2br($supportAnswer);
						}
						elseif(!empty($supportQuestion) && empty($replyBy))
						{
							$headingText	=	$name." sent more query on this support on ".$addedOn;
							$displaySupport	=	nl2br($supportQuestion);
						}
			?>
			<tr>
				<td>
					<hr size="1" width="100%" color="#e4e4e4">
				</td>
			</tr>
			<tr bgcolor="#585858">
				<td class="smalltext2"><font color="#ffffff"><?php echo $headingText;?></font></td>
			</tr>
			<tr>
				<td class="smalltext2"><?php echo $displaySupport;?></td>
			</tr>
			<?php
				if(!empty($isUploadedFile))
				{
			?>
			<tr>
				<td height="2"></td>
			</tr>
			<tr>
				<td class="smalltext2">Uploaded File : <a href="<?php echo SITE_URL_EMPLOYEES;?>/download-support-files.php?id=<?php echo $supportId;?>&rid=<?php echo $supportReplyId;?>" class='link_style12'><?php echo $fileName.".".$ext;?></a>&nbsp;&nbsp;<font class="smalltext2">(<?php echo getFileSize($fileSize);?>)</font></td>
			</tr>
			<?php
				}
			}
			?>
			</table>
		</td>
		<td>&nbsp;</td>
	</tr>
</table>
<?php
	}
	if($status == 1)
	{
		$replySupport	=	"";
		$replySupportId	=	0;
		$replyErroeMsg	=	"";
		$supportReplyId	=	0;
		if(isset($_REQUEST['formSubmitted']))
		{
			extract($_REQUEST);
			$replySupport		=	trim($replySupport);
			if(empty($replySupport))
			{
				$errorMsg		=	"Please add support reply !!";
			}
			if(empty($errorMsg))
			{
				$replySupport	=	makeDBSafe($replySupport);
				dbQuery("INSERT INTO support_replies SET supportAnswer='$replySupport',supportId=$supportId,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',replyBy=$s_employeeId");
				$supportReplyId =	mysql_insert_id();

				if(!empty($_FILES['replySupportFile']['name']))
				{
					$uploadingFile		=   $_FILES['replySupportFile']['name'];
					$mimeType			=   $_FILES['replySupportFile']['type'];
					$fileSize			=   $_FILES['replySupportFile']['size'];
					$tempName			=	$_FILES['replySupportFile']['tmp_name'];
					$ext				=	findexts($uploadingFile);
					$uploadingFileName	=	getFileName($uploadingFile);


					$fileName			=   $supportId."_".$supportReplyId.".".$ext;

					move_uploaded_file($tempName,$supportFilePath.$fileName);
					chmod($supportFilePath."$fileName",0600);

					dbQuery("UPDATE support_replies SET isUploadedFile=1,fileName='$uploadingFileName',fileMimeType='$mimeType',fileSize=$fileSize,ext='$ext' WHERE supportId=$supportId AND supportReplyId=$supportReplyId");
				}
				$from			=	"support@ieimpact.com";
				$fromName		=	"ieIMPACT";
				$to				=	$email; 
				$mailSubject	=	"Replied from ieIMPACT in your support";
				$templateId		=	TEMPLATE_REPLIED_SUPPORT;

				$replyText1		=	$mailSubject;
				$replyText2		=	stripslashes($replySupport);
				$replyText3		=	"If you are not satisfied with our reply send us more informations.";
				$replyText4		=	"Please use the below link to view support status in  website.";
				$replyText5		=	"<a href='".SITE_URL_SUPPORT."/search-ticket.php?searchTicket=$ticketNumber&code=$verificationCode' target='_blank'>Click here to view support status</a";
				$replyText6		=	"Thanks for write to us";

				$a_templateData	=	array("{name}"=>$name,"{replyText1}"=>$replyText1,"{emailtext2}"=>$replyText2,"{emailtext3}"=>$replyText3,"{emailtext4}"=>$replyText4,"{emailtext5}"=>$replyText5,"{emailtext6}"=>$replyText6,"{ticketNumber}"=>$ticketNumber);

				sendTemplateMail($from, $fromName, $to, $mailSubject, $templateId, $a_templateData);

				if($a_supportEmployeeEmails = $suppurtObj->getSectionSupportEmployees($parentId))
				{
					if(!empty($a_supportEmployeeEmails))
					{
						$mailSubject	=	"Replied on support of ".$name." by employee - ".$s_employeeName;

						$replyText1		=	$mailSubject;
						$replyText2		=	"Below the reply details of support for ".$name." by employee ".$s_employeeName;
						$replyText3		=	stripslashes($replySupport);
						$replyText6		=	"Please check employee area for details";

						foreach($a_supportEmployeeEmails as $k=>$value)
						{
							list($employeeEmail,$employeeName)	=	explode("|",$value);
							$a_templateData	=	array("{name}"=>$employeeName,"{replyText1}"=>$replyText1,"{emailtext2}"=>$replyText2,"{emailtext3}"=>$replyText3,"{emailtext4}"=>"","{emailtext5}"=>"","{emailtext6}"=>$replyText6,"{ticketNumber}"=>$ticketNumber);

							sendTemplateMail($from, $fromName, $employeeEmail, $mailSubject, $templateId, $a_templateData);

						}
					}
				}
				if(!empty($a_managerEmails))
				{
					$mailSubject	=	"Replied on support of ".$name." by employee - ".$s_employeeName;

					$replyText1		=	$mailSubject;
					$replyText2		=	"Below the reply details of support for ".$name." by employee ".$s_employeeName;
					$replyText3		=	stripslashes($replySupport);
					$replyText6		=	"Please check admin support area for details";
					foreach($a_managerEmails as $key=>$value)
					{
						list($managerEmail,$managerName)	=	explode("|",$value);

						$a_templateData	= array("{name}"=>$managerName,"{replyText1}"=>$replyText1,"{emailtext2}"=>$replyText2,"{emailtext3}"=>$replyText3,"{emailtext4}"=>$replyText4,"{emailtext5}"=>$replyText5,"{emailtext6}"=>$replyText6,"{ticketNumber}"=>$ticketNumber);

						sendTemplateMail($from, $fromName, $managerEmail, $mailSubject, $templateId, $a_templateData);

					}
				}
	
				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES."/support-order-details.php?ID=$supportId");
				exit();
			}
		}

?>
<form name="addReplySupport" action="" method="POST" enctype="multipart/form-data" onsubmit="return validReplySupport();">
	<table width="98%" border="0" cellpadding="3" cellspacing="3" align="center">
		<tr>
			<td colspan="3" class="text">
				Reply To This Support
			</td>
		</tr>
		<tr>
			<td colspan="3" class="error">
				<?php
					echo $replyErroeMsg;
				?>
			</td>
		</tr>
		<tr>
			<td class="smalltext2" valign="top" width="10%">
				<b>Reply</b>
			</td>
			<td class="smalltext2" valign="top">
				<b>:</b>
			</td>
			<td valign="top">
				<textarea name="replySupport" rows="8" cols="60" style="border:1px solid #333333"><?php echo $replySupport;?></textarea>
			</td>
		</tr>
		<tr>
			<td class="smalltext2">
				<b>Upload File</b>
			</td>
			<td class="smalltext2">
				<b>:</b>
			</td>
			<td>
				<input type="file" name="replySupportFile" style="border:1px solid #333333">
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td>
				<input type="image" name="submit" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
	</table>
</form>
<?php
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>