<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/common.php");
	$employeeObj				= new employee();
	$memberObj					= new members();
	$orderObj					= new orders();
	$commonObj					= new common();
	$showForm					= false;
	$orderId					= 0;
	$memberId					= 0;
	$customerId					= 0;

	$M_D_5_ORDERID				=	ORDERID_M_D_5;
	$M_D_5_ID					=	ID_M_D_5;

	function getFileSize($fileSize)
	{
		if($fileSize   <= 0 || $fileSize == 0)
		{
			$fileSize	=	"";
		}
		else
		{
			$fileSize	=	$fileSize/1024;

			$fileSize	=	round($fileSize,2);

			$fileSize	=	$fileSize." (KB)";
		}

		if(empty($fileSize))
		{
			$fileSize	=	"<font color='#ff0000'>(File size is 0 byte)</font>";
		}

		return $fileSize;
	}
	
	if(isset($_GET['orderId']) && isset($_GET['customerId']))
	{
		$orderId				=	$_GET['orderId'];
		$memberId				=	$_GET['customerId'];
		$query					=	"SELECT orderAddress,isNewUploadingSystem FROM members_orders WHERE orderId=$orderId AND memberId=$memberId";
		$result					=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$row					=	mysqli_fetch_assoc($result);
			$orderAddress			=	stripslashes($row['orderAddress']);
			$isNewUploadingSystem	=	stripslashes($row['isNewUploadingSystem']);
			$encodeOrderID			=	base64_encode($orderId);
			$customerId				=	$memberId;


			$completeName			=	$employeeObj->getSingleQueryResult("SELECT completeName FROM members WHERE memberId=$memberId","completeName");
			$completeName			=	stripslashes($completeName);


			if(!empty($orderAddress))
			{
				$showForm				=	true;
			}
		}
	}
?>
<html>
<head>
<TITLE>View customers orders message from - <?php echo $completeName;?></TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
<body>
<script type="text/javascript">
	function reflectChange()
	{
		window.opener.location.reload();
	}
	function downloadMultipleOrderFile(url)
	{
		//window.open(url, "_blank");
		  location.href   = url;
	}
</script>
<center>
<?php
	if($showForm)
	{
?>
		<table width="100%" align="center" border="0" cellspacing="3" cellspacing="3">
			<tr>
				<td colspan="3" class="textstyle1"><b>View customers orders message from - <?php echo $completeName;?></b></td>
			</tr>
			<!--<tr>
				<td width="24%" class="smalltext2">
					<b>Customer Name</b>
				</td>
				<td width="2%" class="smalltext2">
					<b>:</b>
				</td>
				<td class="title">
					<?php echo $completeName;?>
				</td>
			</tr>-->
			<tr>
				<td class="smalltext2" width="24%" >
					<b>Order Address</b>
				</td>
				<td class="smalltext2" width="2%">
					<b>:</b>
				</td>
				<td class="title">
					<?php echo $orderAddress;?>
				</td>
			</tr>
			<?php
				$query					=	"SELECT members_employee_messages.*,completeName from members_employee_messages INNER JOIN members ON members_employee_messages.memberId=members.memberId WHERE orderId=$orderId AND members_employee_messages.memberId=$memberId ORDER BY members_employee_messages.addedOn, members_employee_messages.addedTime";
				$result	=	dbQuery($query);
				if(mysqli_num_rows($result))
				{
					

					while($row		=	mysqli_fetch_assoc($result))
					{
						$t_messageId	=	$row['messageId'];
						$t_message		=	stripslashes($row['message']);
						$t_message		=	trim($t_message);
						$addedOn		=	$row['addedOn'];
						$addedTime		=	$row['addedTime'];
						$messageBy		=	$row['messageBy'];
						$hasMessageFiles=	$row['hasMessageFiles'];
						$fileName		=	$row['fileName'];
						$fileExtension	=	$row['fileExtension'];
						$fileSize		=	$row['fileSize'];
						$completeName	=	stripslashes($row['completeName']);
						$emailSubject	=	stripslashes($row['emailSubject']);
						$isDeleted		=	$row['isDeleted'];
						$isVirtualDeleted=	$row['isVirtualDeleted'];
						$isNeedToVerify	=	$row['isNeedToVerify'];

						$daysAgo		=	showDateTimeFormat($addedOn,$addedTime);

						if(empty($t_message) && !empty($emailSubject))
						{
							$t_message	=	$emailSubject;
						}

						$notifyText			=	"";
						if($isNeedToVerify	==	1)
						{
							$notifyText		=	"<font class='smalltext23'>[<font color='#ff0000;'>This Message Is Not Yet Sent, Need verification. Please ask manager to send it ASAP</font>";
							if(!empty($s_isHavingVerifyAccess))
							{
								$notifyText .=	"&nbsp;<a href='".SITE_URL_EMPLOYEES."/send-message-pdf-customer.php?orderId=$orderId&customerId=$customerId&vmsg=$t_messageId#sendMessages' class='link_style14' target='_blank'>Verify It</a>";
							}
							
							$notifyText		.=	"]</font>";

						}		

						if($messageBy		 ==  EMPLOYEES)
						{
							echo "<tr><td colspan='3' class='smalltext16'><b>Data entry team on ".$daysAgo."</b><br />".$notifyText."</td></tr>";
							$textClass		=	"class='smalltext16'";
						}
						elseif($messageBy   ==  CUSTOMERS)
						{
							echo "<tr><td colspan='3' class='smalltext2'><b>From customer on ".$daysAgo."</b></td></tr>";
							$textClass	=	"class='smalltext2'";
						}
						echo "<tr><td colspan='3' ".$textClass."><p align='justify'>".$t_message."</p></td></tr>";
						if($isDeleted	==	0 && $isVirtualDeleted == 0)
						{
							if($hasMessageFiles			== 1)
							{
								if($isNewUploadingSystem == 1)
								{
									if($result1			=	$orderObj->getOrdereMessageFile($orderId,$t_messageId,3,7))
									{
										echo "<tr><td colspan='3' valign='top'><table width='100%' align='left'><tr><td width='16%' class='textstyle2' valign='top'><b>Uploaded Files : </b></td><td valign='top'><table width='100%' align='left'>";

										$countingFiles			=	0;
										
										while($row1				=	mysqli_fetch_assoc($result1))
										{
											$countingFiles++;
											$fileId				=	$row1['fileId'];
											$fileName			=	stripslashes($row1['uploadingFileName']);
											$fileExtension		=	$row1['uploadingFileExt'];
											$fileSize			=	$row1['uploadingFileSize'];
											$imageOnServerPath	=	$row1['excatFileNameInServer'];

											$base_fileId		=	base64_encode($fileId);
											
											$downLoadPath		=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
										?>
										<tr>
											 <td valign="top">
												<font class='smalltext20'><?php echo $countingFiles;?>)</font>&nbsp;<a class="link_style26" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download Message File" style="cursor:pointer;"><?php echo $fileName.".".$fileExtension;?></a>&nbsp;&nbsp;<font class='smalltext20'><?php echo getFileSize($fileSize);?></font>
											<?php
												if(in_array($fileExtension,$a_displayAnyImageOfType) && $messageBy   ==  EMPLOYEES  && $fileSize <= "3145728")
												{
													list($imgWidth, $imgHeight, $type, $attr) = getimagesize($imageOnServerPath);

													if($imgWidth > 600 || $imgHeight > 400)
													{
														$imageWidth		=	"width='600'";
														$imageHeight	=	"height='400'";
													}
													else
													{
														$imageWidth		=	"";
														$imageHeight	=	"";
													}
											?>
											<br><img src="<?php echo SITE_URL_EMPLOYEES;?>/get-employee-message-image.php?memberId=<?php echo $customerId;?>&orderId=<?php echo $orderId;?>&messageId=<?php echo $t_messageId;?>&isNewSystem=1" border="0" title="<?php echo $fileName.".".$fileExtension;?>" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" style="cursor:pointer">
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
									echo "<tr><td colspan='3' class='textstyle2'><b>Uploaded File : </b><a href='".SITE_URL_EMPLOYEES."/download-message-files.php?ID=$t_messageId'  class='link_style26'>".$fileName.".".$fileExtension."</a>&nbsp;&nbsp;<font class='smalltext20'>".getFileSize($fileSize)."</font>";

									if(in_array($fileExtension,$a_displayAnyImageOfType) && $messageBy   ==  EMPLOYEES  && $fileSize <= "3145728")
									{
										$n_messageFilePath			=	SITE_ROOT_FILES."/files/messages/".$t_messageId."_".$fileName.".".$fileExtension;

										list($imgWidth, $imgHeight, $type, $attr) = getimagesize($n_messageFilePath);

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
										<br><a href="<?php echo SITE_URL_EMPLOYEES;?>/download-message-files.php?ID=<?php echo $t_messageId;?>"><img src="<?php echo SITE_URL_EMPLOYEES;?>/get-employee-message-image.php?memberId=<?php echo $customerId;?>&orderId=<?php echo $orderId;?>&messageId=<?php echo $t_messageId;?>&isNewSystem=0" border="0" title="<?php echo $fileName.".".$fileExtension;?>"></a>
								<?php
									}
									echo "</td></tr>";
								}
							}
						}

					}

				}
			?>
		</table>
		<?php
		}
		?>
<br><br>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
</center>
</body>
</html>

