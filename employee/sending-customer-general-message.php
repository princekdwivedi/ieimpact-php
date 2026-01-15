<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT			. "/classes/common.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	$orderGeneralMessagePath	=	SITE_ROOT_FILES."/files/general-messages/";	
	$M_D_5_ORDERID			    =	ORDERID_M_D_5;
	$M_D_5_ID				    =	ID_M_D_5;

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

			$fileSize	=	$fileSize." KB";
		}

		return $fileSize;
	}

	function getFileName($fileName)
	{
		$fileName				=  stripslashes($fileName);
		$dotPosition			=  strpos($fileName, "'");
		if($dotPosition			== true)
		{
			$fileName			=	stringReplace("'", "", $fileName);
		}
		$doubleDotPosition		=  strpos($fileName, '"');
		if($doubleDotPosition	== true)
		{
			$fileName			=	stringReplace('"', '', $fileName);
		}
		$fileName				=	stringReplace("/", '', $fileName);
		$fileName				=	stringReplace(":", '', $fileName);
		$fileName				=	stringReplace("&", '', $fileName);
		$fileName				=	stringReplace("*", '', $fileName);
		$fileName				=	stringReplace("?", '', $fileName);
		$fileName				=	stringReplace("|", '', $fileName);
		$fileName				=	stringReplace("<", '', $fileName);
		$fileName				=	stringReplace(">", '', $fileName);
		$fileExtPos				=   strrpos($fileName, '.');
		$fileName				=   substr($fileName,0,$fileExtPos);
		
		return $fileName;
	}

	$employeeObj				= new employee();
	$memberObj					= new members();
	$commonObj					= new common();
    $pagingObj					= new Paging();
	$orderObj					=  new orders();

	if(isset($_REQUEST['recNo']))
	{
		$recNo					 =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo					 =	0;
	}


	$showForm					= false;
	$memberId					= 0;
	$error						= "";
	$replyMessage				= "Enter Your Reply Message Here";
	$errorMessageForm			= "You are not authorized to view this page !!";
	$customerName               = "";
	$messageSubject				= "";

	if(isset($_GET['memberId']))
	{
		$memberId				    =	$_GET['memberId'];
		if(!empty($memberId)){
			$query					=	"SELECT * FROM members WHERE memberId=$memberId";
			$result					=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$showForm				=	true;

				$row					=	mysqli_fetch_assoc($result);
				$firstName				=	stripslashes($row['firstName']);
				$lastName				=	stripslashes($row['lastName']);
				$customerName			=	stripslashes($row['completeName']);
				$email					=	$row['email'];
				$secondaryEmail			=	$row['secondaryEmail'];
				$folderId				=	$row['folderId'];
				$memberUniqueEmailCode	=	$row['uniqueEmailCode'];
			}
		}
	}
?>
<html>
<head>
<TITLE>Send General Message Message To - <?php echo $customerName;?></TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
<body>
<center>
<?php
	if($showForm)
	{
		if(isset($_REQUEST['formSubmitted']))
		{
			extract($_REQUEST);
			$replyMessage		=	trim($replyMessage);
			$messageSubject     =   trim($messageSubject);
			$t_replyMessage		=	makeDBSafe($replyMessage);
			$t_messageSubject	=	makeDBSafe($messageSubject);
			if($replyMessage	==	"Enter Your Reply Message Here")
			{
				$replyMessage	=	"";
			}
			if(empty($messageSubject))
			{
				$error		    =  "Enter message subject.";
			}
			if(empty($replyMessage))
			{
				$error		   .=  "Enter your reply message.";
			}
			
			if(!empty($_FILES['generalMessageFile']['name']))
			{								
				$uploadingFile		=   $_FILES['generalMessageFile']['name'];
				$fileSize			=   $_FILES['generalMessageFile']['size'];

				if($fileSize > MAXIMUM_SINGLE_FILE_SIZE_ALLOWED)
				{
					$error .= "The File you are trying to send is very large. It's size must be less than ".MAXIMUM_SINGLE_FILE_SIZE_ALLOWED_TEXT.". Please reduce the filesize by removing large pictures etc.";
				}
				
			}
			if(empty($error))
			{
				$replyMessage	=	stripslashes($replyMessage);
				$messageSubject	=	stripslashes($messageSubject);
				$replyMessage	=	nl2br($replyMessage);

				dbQuery("INSERT INTO members_general_messages SET memberId=$memberId,messageRelatedOrder='$t_messageSubject',message='$t_replyMessage',addedOn='".CURRENT_DATE_INDIA."',addedtime='".CURRENT_TIME_INDIA."',customerZoneDate='".CURRENT_DATE_CUSTOMER_ZONE."',customerZoneTime='".CURRENT_TIME_CUSTOMER_ZONE."',isOrderGeneralMsg=1,replyBy=$s_employeeId,employeeSendingFirstMsg=1");
				$generalMsgId			=	mysqli_insert_id($db_conn);
				$hasAttachment			=	0;
				$a_attachmentPath		=	array();
				$a_attachmentType		=	array();
				$a_attachmentName		=	array();

				$uploadFileName         =  "";
				if(!empty($_FILES['generalMessageFile']['name']))
				{								
					$uploadingFile		=   $_FILES['generalMessageFile']['name'];
					$mimeType			=   $_FILES['generalMessageFile']['type'];
					$fileSize			=   $_FILES['generalMessageFile']['size'];
					$tempName			=	$_FILES['generalMessageFile']['tmp_name'];
					$ext				=	findexts($uploadingFile);
					$uploadingFileName	=	getFileName($uploadingFile);
					$uploadFileName     =   "<b>Uploaded File :</b>".$uploadingFileName.".".$ext;
					$t_uploadingFile	=	makeDBSafe($uploadingFileName);

					

					if(!is_dir($orderGeneralMessagePath."/$folderId"))
					{
						mkdir($orderGeneralMessagePath."/$folderId");
						chmod($orderGeneralMessagePath."/$folderId",0775);
					}
					$orderGeneralMessagePath=	SITE_ROOT_FILES."/files/general-messages/$folderId/";
					

					dbQuery("INSERT INTO customer_general_message_files SET memberId=$memberId,fileName='$t_uploadingFile',fileExt='$ext',fileType='$mimeType',fileSize='$fileSize',parentId=$generalMsgId,messageType='EMPLOYEE TO CUSTOMER GENERAL MESSAGE',addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',customerZoneDate='".CURRENT_DATE_CUSTOMER_ZONE."',customerZoneTime='".CURRENT_TIME_CUSTOMER_ZONE."',addedFromIp='".VISITOR_IP_ADDRESS."'");

					$fileId				=	mysqli_insert_id($db_conn);
					
					$fileName			=   $fileId."_".$uploadingFileName.".".$ext;

					move_uploaded_file($tempName,$orderGeneralMessagePath.$fileName);
					
					dbQuery("UPDATE members_general_messages SET isUploadedFiles=1 WHERE generalMsgId=$generalMsgId");

					$hasAttachment	    =	1;
					$a_attachmentPath[]	=	$orderGeneralMessagePath.$fileName;
					$a_attachmentType[]	=	$mimeType;
					$a_attachmentName[]	=	$uploadingFileName.".".$ext;
				}

				if(!empty($uploadFileName)){
					$replyMessage   =  $replyMessage."<br />".$uploadFileName;
				}

				

				$subject			=	"Message from ieIMPACT";

				include(SITE_ROOT	.	"/classes/email-templates.php");
				$emailObj			=	new emails();

				$a_templateSubject	=	array("{emailSubject}"=>$messageSubject);
				$trackEmailImage	=	"images/white-space.jpg";
								
				$a_templateData		=	array("{completeName}"=>$customerName,"{emailBody}"=>$replyMessage,"{subject}"=>$subject,"{trackEmailImage}"=>$trackEmailImage);
				$setThisEmailReplyToo		=	"Email".$memberUniqueEmailCode.CUSTOMER_REPLY_EMAIL_TO;//Setting for reply to make customer reply order mesage
				$setThisEmailReplyTooName	=	"ieIMPACT Message";//Setting for reply to make customer reply order mesage

				$uniqueTemplateName	=	"TEMPLATE_SENDING_SIMPLEE_CUSTOMER_MESSAGE";
				$toEmail			=	$email;		
 
				include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");	
				$setThisEmailReplyToo		= "";
				$setThisEmailReplyTooName	= "";
				$toEmail			        = DEFAULT_BCC_EMAIL;		
								
				include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES."/sending-customer-general-message.php?memberId=$memberId#messages");
				exit();
				
			}

		}
?>
	<script type="text/javascript">
		function showProgress()
		{
			document.getElementById('loading').style.display = 'block';
		}
		function validMessage()
		{
			form1	=	document.generalReplyMessage;
			if(form1.messageSubject.value == "" || form1.messageSubject.value == " ")
			{
				alert("Enter message subject.");
				form1.messageSubject.focus();
				return false;
			}
			if(form1.replyMessage.value == "" || form1.replyMessage.value == " " || form1.replyMessage.value == "Enter Your Reply Message Here")
			{
				alert("Enter your reply message.");
				form1.replyMessage.focus();
				return false;
			}
			showProgress();

		}
		function textCounter(field,countfield,maxlimit)
		{
			if(field.value.length > maxlimit)
			{
				field.value = field.value.substring(0, maxlimit);
			}
			else
			{
				countfield.value = maxlimit - field.value.length;
			}
		 }

		

		function downloadGeneralMessageFile(url)
		{
			//window.open(url, "_blank");
			 location.href   = url;
		}
	</script>
	<form name="generalReplyMessage" action="" method="POST" enctype="multipart/form-data" onSubmit="return validMessage();">
		<table width="100%" align="center"  border="0" cellspacing="3" cellspacing="3">
			<tr>
				<td colspan="3" class="textstyle1"><b>Send General Message Message To - <?php echo $customerName;?></b></td>
			</tr>
			<tr>
				<td colspan="3" class="error">
					<?php echo $error;?>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="2"></td>
			</tr>
			<tr>
				<td width="10%" class="smalltext2"><b>Subject : </b></td>
				<td>
					<input type="text" name="messageSubject" value="<?php echo $messageSubject;?>" style="height:30px;width:400px;border:1px solid #333333;font-family:verdana;font-size:12px;">
				</td>
			</tr>
			<tr>
				<td colspan="3" height="2"></td>
			</tr>
			<tr>
				<td valign="top" colspan="3">
					<textarea name="replyMessage" rows="10" cols="60" wrap="hard" onKeyDown="textCounter(this.form.replyMessage,this.form.remLentext1,1000);" onKeyUp="textCounter(this.form.replyMessage,this.form.remLentext1,1000);" onFocus="if(this.value=='Enter Your Reply Message Here') this.value='';" onBlur="if(this.value=='') this.value='Enter Your Reply Message Here';"><?php echo nl2br($replyMessage);?></textarea>

					<br><font class="smalltext2">Characters Left: <input type="textbox" readonly name="remLentext1" size=2 value="1000" style="border:0"></font>
				</td>
			</tr>
			<tr>
				<td colspan="3" class="smlltext">[Note<font color="red"><b>*</b></font>: Please check your english as your message will go to customer]</td>
			</tr>
			<tr>
				<td colspan="3" height="2"></td>
			</tr>
			<tr>
				<td colspan="3" class="smalltext2">Add a File : <input type="file" name="generalMessageFile"></td>
			</tr>
			<tr>
				<td colspan="3" height="5"></td>
			</tr>
			<tr>
				<td colspan="2">
					<div id="loading" style="display: none;"><img src="<?php echo OFFLINE_IMAGE_PATH;?>/images/ajax-loader.gif" alt="" /></div> 
				</td>
			</tr>
			<tr>
				<td colspan="3" height="5"></td>
			</tr>
			<tr>
				<td>
					<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
					<input type='hidden' name='formSubmitted' value='1'>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="5"></td>
			</tr>
		</table>
	</form>
	<?php
		$whereClause			  =	"WHERE members_general_messages.memberId=$memberId";
		$orderBy				  =	"generalMsgId DESC";
		$andClause				  =	"";
		$queryString			  =	"&memberId=$memberId";

		$start					  =	0;
		$recsPerPage	          =	25;	//	how many records per page
		$showPages		          =	10;	
		$pagingObj->recordNo	  =	$recNo;
		$pagingObj->startRow	  =	$recNo;
		$pagingObj->whereClause   =	$whereClause;
		$pagingObj->recsPerPage   =	$recsPerPage;
		$pagingObj->showPages	  =	$showPages;
		$pagingObj->orderBy		  =	$orderBy;
		$pagingObj->table		  =	"members_general_messages INNER JOIN members ON members_general_messages.memberId=members.memberId";
		$pagingObj->selectColumns = "members_general_messages .*,completeName";
		$pagingObj->path		  = SITE_URL_EMPLOYEES."/sending-customer-general-message.php";
		$totalRecords = $pagingObj->getTotalRecords();
		if($totalRecords && $recNo <= $totalRecords)
		{
			$pagingObj->setPageNo();
			$recordSet = $pagingObj->getRecords();
			$i						=	$recNo;
		?>
		
		<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
			<tr bgcolor="#373737" height="20">
				<td class="smalltext8" width="2%">&nbsp;<a name="messages"></a></td>
				<td class="smalltext8" width="22%"><b>From</b></td>
				<td class="smalltext8" width="20%"><b>Date</b></td>
				<td class="smalltext8" width="9%"><b>Order Address</b></td>
				<td class="smalltext8"><b>Message</b></td>
			</tr>
		<?php
			while($row	=   mysqli_fetch_assoc($recordSet))
			{
				$i++;
				
				$generalMsgId				=	$row['generalMsgId'];
				$memberId					=	$row['memberId'];
				$customerName				=	stripslashes($row['completeName']);
				$messageDate				=	$row['addedOn'];
				$messageTime				=	$row['addedtime'];
				$messageRelatedOrder		=	stripslashes($row['messageRelatedOrder']);
				$message					=	stripslashes($row['message']);
				$isUploadedFiles			=	$row['isUploadedFiles'];
				$mesageStatus				=	$row['status'];
				$replyBy					=	$row['replyBy'];
				$parentId					=	$row['parentId'];
				$repliedByEmployeetext		=	stripslashes($row['repliedByEmployeetext']);
				$employeeSendingFirstMsg	=	$row['employeeSendingFirstMsg'];
				$isUploadedFiles         	=	$row['isUploadedFiles'];
				


				if($employeeSendingFirstMsg == 0){

					if($parentId			==  0)
					{
						$messageBytext		=	"Customer";
					}
					else
					{
						$messageBytext		=	"Data entry team";
					}
				}
				else{
					$messageBytext			=	$employeeObj->getEmployeeFirstName($replyBy)."&nbsp;(Data entry team)";
				}

				$message                       =   preg_replace( "/\r|\n/", "", $message);

				if(empty($messageRelatedOrder))
				{
					$messageRelatedOrder	   =	"Not Specific Order";
				}
				else{
					$messageRelatedOrder		=	 "Address :". $messageRelatedOrder;
				}
			
				$bgColor				=	" class='rwcolor1'";
				if($i%2==0)
				{
					$bgColor			=   " class='rwcolor2'";
				}

				$daysAgo					=	showDateTimeFormat($messageDate,$messageTime);
		?>
		<tr<?php echo $bgColor;?>>
			<td class="smalltext2" valign="top"><?php echo $i;?>)</td>
			<td class="smalltext2" valign="top">
				<?php echo $messageBytext;?>
			</td>
			<td class="smalltext16" valign="top" width="18%">
				<?php echo $daysAgo;?>
			</td>
			<td class="smalltext16" valign="top" width="18%">
				<?php echo $messageRelatedOrder;?>
			</td>
			<td  valign="top" width="35%">
				<table width="100%" align="center" cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="2" class="smalltext16">
							<div style='overflow:auto;width:430px;scrollbars:no;border:0px;padding:0 0 0 0 ;'>
								<table width="100%" border="0" align="center">
									<tr>
										<td class="smalltext1" valign="top" colspan="2">
											<?php echo nl2br($message);?>
										</td>
									</tr>
									<?php
										if($a_files	=	$orderObj->getCustomerGeneralMessageEmailFiles($memberId,$generalMsgId))
										{
											$cn	=	0;
											$isDisplayZipFile			=	0;
											foreach($a_files as $fileId=>$value)
											{
												$cn++;
												
												if($cn > 3){
													$isDisplayZipFile	=	1;
												}
												list($fileName,$size) = explode("|",$value);

												$base_fileId	=	base64_encode($fileId);

												$downLoadPath	=	SITE_URL_EMPLOYEES."/download-general-mesage-file.php?".$M_D_5_ID."=".$base_fileId;

												$fileSize	=	getFileSize($size);
									?>
									<tr>
										<td width="3%" class="smalltext22" valign="top">
											<?php echo $cn;?>)
										</td>
										<td valign="top">
											<a class="link_style12" onclick="downloadGeneralMessageFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $fileName;?></a>
											&nbsp;<font class='smalltext1'><?php echo $fileSize;?></font>
										</td>
									</tr>
									<tr>
										<td height="3"></td>
									</tr>
								<?php

										}
										if($isDisplayZipFile == 1){
																				
											$messageFiledownLoadPath	=	SITE_URL_EMPLOYEES."/download-all-general-message-files.php?".$M_D_5_ORDERID."=".base64_encode($memberId)."&".$M_D_5_ID."=".base64_encode($generalMsgId);
									?>
											<!--<tr>
												<td colspan="2" align="left">									
													(<a class="link_style13" onclick="downloadGeneralMessageFile('<?php echo $messageFiledownLoadPath;?>');" title="Download Message File as ZIP" style="cursor:pointer;"><b>Download Message All Files As .zip</b></a>)
												</td>
											</tr>-->
									<?php
											
										}
									}
									?>
								</table>
							</div>
						</td>
					</tr>								
				</table>
			</td>
		</tr>
	<?php
		}
		echo "<tr><td height='10'></td></tr><tr><td style='text-align:right' colspan='8'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr>";		
		echo "</table>";
		}

	}
	else{
		echo "<br /><br /><font class='error'><b>".$errorMessageForm."</b></font>";	
	}
	?>
	<br><br>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
</center>
</body>
</html>

