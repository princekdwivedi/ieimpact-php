<?php
	$pageUrl	        =	$_SERVER['SCRIPT_NAME'];
	if(!strstr($_SERVER['HTTP_HOST'],'ieimpact.com'))
	{
		$pageUrl	    =	stringReplace("/ieimpact/","/",$pageUrl);
	}
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

	function getSizeNoBracket($fileSize)
	{
		if($fileSize <= 0 || $fileSize == 0)
		{
			$fileSize	=	"";
		}
		else
		{
			$fileSize	=	$fileSize/1024;

			$fileSize	=	round($fileSize,2);

			$fileSize	=	"&nbsp;<font style='font-size:12px;color:#000000;'>(".$fileSize." KB)</font>";
		}

		return $fileSize;
	}


	if(isset($_GET['instructionId']) && isset($_GET['isDeleteInstructions']) && $_GET['isDeleteInstructions'] == 1)
	{
		$instructionId	=	$_GET['instructionId'];
		$query			=	"SELECT * FROM customer_instructions_file WHERE memberId=$customerId AND instructionId=$instructionId AND uploadedBy='".EMPLOYEES."'";
		$result	=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			$row				=	mysql_fetch_assoc($result);
			$fileName			=	$row['fileName'];
			$fileExt			=	$row['fileExt'];
			$instructionsPath	=	SITE_ROOT_FILES."/files/instructions/";

			$d_fileName			=   $instructionId."_".$fileName.".".$fileExt;
			if(file_exists($instructionsPath.$d_fileName))
			{
				chmod($instructionsPath."$d_fileName",0644);
				unlink($instructionsPath.$d_fileName);
			}

			dbQuery("DELETE FROM customer_instructions_file WHERE memberId=$customerId AND instructionId=$instructionId AND uploadedBy='".EMPLOYEES."'");
		}
		ob_clean();
		header("Location: ".SITE_URL."/".$pageUrl."?orderId=$orderId&customerId=$customerId");
		exit();
	}
?>
<script type="text/javascript">
function deleteNoteInstructionsFile(instructionId,url,customerId,orderId)
{
	var confirmation = window.confirm("Are You Sure Delete This File?");
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL;?>"+url+"?orderId="+orderId+"&customerId="+customerId+"&instructionId="+instructionId+"&isDeleteInstructions=1";
	}
}
function downloadMultipleOrderFile(url)
{
	//window.open(url, "_blank");
	  location.href   = url;
}
</script>
</script>
<?php
	$M_D_5_ORDERID				=	ORDERID_M_D_5;
	$M_D_5_ID					=	ID_M_D_5;
	
	$a_existingRatings			=	$orderObj->getFeedbackText();
	$instructionDaysDifferent	=	"0";
	if($result					=	$orderObj->getOrderDetails($orderId,$customerId))
	{
		$row							=	mysql_fetch_assoc($result);
		$encodeOrderID					=	base64_encode($orderId);
		$customerId						=	$row['memberId'];
		$orderAddress					=	stripslashes($row['orderAddress']);
		$orderType						=	$row['orderType'];
		$customersOwnOrderText			=	stripslashes($row['customersOwnOrderText']);
		$state							=	$row['state'];
		$instructions					=	stripslashes($row['instructions']);
		$hasOrderFile					=	$row['hasOrderFile'];
		$orderFileExt					=	$row['orderFileExt'];
		$hasPublicRecordFile			=	$row['hasPublicRecordFile'];
		$publicRecordFileExt			=	$row['publicRecordFileExt'];
		$hasMlsFile						=	$row['hasMlsFile'];
		$mlsFileExt						=	$row['mlsFileExt'];
		$hasMarketConditionFile			=	$row['hasMarketConditionFile'];
		$marketConditionExt				=	$row['marketConditionExt'];
		$orderAddedOn					=	showDate($row['orderAddedOn']);
		$assignToEmployee				=	showDate($row['assignToEmployee']);
		$firstName						=	stripslashes($row['firstName']);
		$lastName						=	stripslashes($row['lastName']);
		$dispalyCustomerPhone			=	$row['phone'];
		$customerEmail					=	$row['email'];
		$hasReceiveEmails				=	$row['noEmails'];
		$customerSecondaryEmail			=	$row['secondaryEmail'];
		$folderId						=	$row['folderId'];
		$hasOtherFile					=	$row['hasOtherFile'];
		$otherFileExt					=	$row['otherFileExt'];
		$orderFileName					=	$row['orderFileName'];
		$publicRecordFileName			=	$row['publicRecordFileName'];
		$mlsFileName					=	$row['mlsFileName'];
		$marketConditionFileName		=	$row['marketConditionFileName'];
		$otherFileName					=	$row['otherFileName'];

		$orderFileSize					=	$row['orderFileSize'];
		$publicRecordFileSize			=	$row['publicRecordFileSize'];
		$mlsFileSize					=	$row['mlsFileSize'];
		$marketConditionFileSize		=	$row['marketConditionFileSize'];
		$otherFileSize					=	$row['otherFileSize'];
		$orderAddedTime					=	$row['orderAddedTime'];
		$appraisalSoftwareType			=	$row['appraisalSoftwareType'];
		$acceptedBy						=	$row['acceptedBy'];
		$status							=	$row['status'];
		$orderCompletedOn				=	showDate($row['orderCompletedOn']);
		$refferedBy						=	$row['refferedBy'];
		$isDeleted						=	$row['isDeleted'];
		$rateGiven						=	$row['rateGiven'];
		$state							=	$row['state'];
		$isReplyFileInEmail				=	$row['isReplyFileInEmail'];
		$isCustomerOptedForSms			=	$row['isOptedForSms'];
		$isDonePostAudit				=	$row['isDonePostAudit'];
		$isNewUploadingSystem			=	$row['isNewUploadingSystem'];
		$newUploadingPath				=	$row['newUploadingPath'];
		$isAlamodeOrder					=	$row['isAlamodeOrder'];
		$aLamodeCustomerID				=	$row['aLamodeCustomerID'];
		$orderEncryptedId				=	$row['encryptOrderId'];
		$cutomerTotalOrdersPlaced		=	$row['totalOrdersPlaced'];

		$postAuditErrorText				=	"ADD POST AUDIT ERRORS";
		if($isDonePostAudit				==	1)
		{
			$postAuditErrorText			=	"EDIT POST AUDIT ERRORS";
		}

		if(!empty($dispalyCustomerPhone)&& $isCustomerOptedForSms  == 1)
		{
			$smsCustomerMobileNo		=	"1".$dispalyCustomerPhone;
		}
		else
		{
			$smsCustomerMobileNo		=	"";
		}
		

		$orderAcceptedDateByEmployee	=	$row['assignToEmployee'];
		$orderAcceptedTimeByEmployee	=	$row['assignToTime'];
		$providedSketch					=	$row['providedSketch'];
		$sketchStatus					=	$row['sketchStatus'];
		$sketchAcceptBy					=	$row['sketchAcceptBy'];
		$sketchAcceptedOn				=	$row['sketchAcceptedOn'];
		$sketchAcceptedTime				=	$row['sketchAcceptedTime'];
		$sketchDoneBy					=	$row['sketchDoneBy'];
		$sketchDoneOn					=	$row['sketchDoneOn'];
		$sketchDoneTime					=	$row['sketchDoneTime'];
		$orderFileMd5HasSize			=	$row['orderFileMd5HasSize'];
		$isRushOrder					=	$row['isRushOrder'];


		$isHavingEstimatedTime			=	$row['isHavingEstimatedTime'];
		$employeeWarningDate			=	$row['employeeWarningDate'];
		$employeeWarningTime			=	$row['employeeWarningTime'];

		$newAttentionUnmarkTxt	=	"";
		if($status				==	0)
		{
			if($isUnmarkedNeedAttention	=	$orderObj->isOrderWasInNeedAttention($orderId))
			{
				$newAttentionUnmarkTxt	=	"<font color='#ff0000'>(Need Atten. Order)</font>";
			}
		}

		if($isRushOrder			==	1)
		{
		   $isRushOrderText		=	"<font color='#ff0000'>YES</font>";
		}
		else
		{
			$isRushOrderText	=	"NO";
		}
		$hasMarkedSketchYes		=	"NO";
		if($providedSketch		==	1)
		{
		   $hasMarkedSketchYes	=	"<font color='red'>YES</font>";
		}

		$displayOrderTimeFormat	 =	showTimeFormat($orderAddedTime);

		$memberRateMsg			 =	stripslashes($row['memberRateMsg']);
		$splInstructionToEmployee=	stripslashes($row['splInstructionToEmployee']);
		$instructionsUpdatedOn	 =	$row['instructionsUpdatedOn'];
		$splInstructionOfCustomer=	stripslashes($row['splInstructionOfCustomer']);
		$hasRatingExplanation	 =	$row['hasRatingExplanation'];

		$employeeDisplayOrderID	 =	$employeeObj->getAAOrderID($orderId);

		$uadCompliant			=	$row['uadCompliant'];
		$uadText				=	"<font color='#66CC00'>NO</font>";
		if($uadCompliant		==	1)
		{
			$uadText			=	"<font color='#ff0000'>YES</font>";
		}

		$refferedByText			=   "No";
		if($refferedBy			==  1)
		{
			$refferedByText		=   "Yes";
		}

		$statusText				=	"New Order";
		$acceptedText			=	"Not Yet Accepted";
		$qaDoneByText			=	"";
		

		$qaDoneBy				=	0;
		
		$orderText				=	$a_customerOrder[$orderType];
		if($orderType	        ==	6 && !empty($customersOwnOrderText))
		{
			$orderText		    =	$orderText."&nbsp;(".$customersOwnOrderText.")";
		}
		$customerName			=   $firstName." ".$lastName;

		$appraisalText			=	$a_allAppraisalFileTypes[$appraisalSoftwareType];

		$replieddFileToustomer	=	$a_replyCustomerTypeFile[$appraisalSoftwareType];

		if(array_key_exists($state,$a_usaProvinces))
		{
			$stateName			=	$a_usaProvinces[$state];
			list($stateName,$timeZone)	=	explode("|",$stateName);
			if(array_key_exists($timeZone,$a_timeZoneColor))
			{
				$timeZoneColor	=	$a_timeZoneColor[$timeZone];
			}
			else
			{
				$timeZoneColor	=	"#333333";
			}

			$displayZoneTime	=	"(<font color='$timeZoneColor'>".$timeZone."</font>)";
		}
		else
		{
			$displayZoneTime	=	"";
		}

		
		$uploadedFileByCustomer	=	$a_uploadedFileBYCustomer[$appraisalSoftwareType];
		if(!empty($acceptedBy))
		{
			$acceptedByName		=   $employeeObj->getEmployeeName($acceptedBy);
		}
		else
		{
			$acceptedByName		=	"";
		}

		if($status	!= 2)
		{
			$orderCompletedOn	=	"";
		}

		if($status	!= 0 && $status	!=  1)
		{
			$qaDoneBy			=	@mysql_result(dbQuery("SELECT qaDoneBy FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId AND hasQaDone=1"),0);
			if(!empty($qaDoneBy))
			{
				$qaDoneByText	=	$employeeObj->getEmployeeName($qaDoneBy);
			}
			else
			{
				$qaDoneByText	=	"";
			}
		}

		if($status != 0)
		{
			$statusText			=	"Accepted";
			$acceptedText		=	$acceptedByName.",On-".$assignToEmployee;
		}

		$hasReplied				=	@mysql_result(dbQuery("SELECT hasRepliedFileUploaded FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId AND hasRepliedFileUploaded=1"),0);
		if(!empty($hasReplied))
		{
			$statusText			=	"QA Pending";
		}

		if($status == 2)
		{
			$statusText			=	"Completed";
		}
		if($status == 3)
		{
			$statusText			=	"Need Attention";
		}
		if($status				==	5)
		{
			$statusText			=   "<font color='green'>Need Feedbk.</font>";
		}
		if($status				==	4)
		{
			$statusText			=   "<font color='#ff0000'>Cancelled</font>";
		}
		if($status				==	6)
		{
			$statusText			=   "<font color='green'>Feedback Recv</font>";
		}


		$customerOrderText	=	"";
		$customerLinkStyle	=	"link_style16";
		$totalCustomerOrders=	$orderObj->getCustomerTotalOrders($customerId);
		if(empty($totalCustomerOrders))
		{
			$totalCustomerOrders	=	0;
		}
		if($totalCustomerOrders <= 3)
		{
			$customerOrderText	=	"(New Customer)";
			$customerLinkStyle	=	"link_style17";
		}
		if($totalCustomerOrders > 3 && $totalCustomerOrders <= 7)
		{
			$customerOrderText	=	"(Trial Customer)";
			$customerLinkStyle	=	"link_style18";
		}

		$checkedId				=	0;
		$checkedBy				=	"";
		$checkedOn				=	"";
		$checkedOnTime			=	"";
		$checkedMessage			=	"";
		$checkedByName			=	"";
		$query2					=	"SELECT * FROM checked_customer_orders WHERE orderId=$orderId";
		$result					=	dbQuery($query2);
		if(mysql_num_rows($result))
		{
			$row			=	mysql_fetch_assoc($result);
			$checkedId		=	$row['checkedId'];
			$checkedBy		=	$row['checkedBy'];
			$checkedOn		=	$row['checkedOn'];
			$checkedOnTime	=	$row['checkedOnTime'];
			$checkedMessage	=	stripslashes($row['checkedMessage']);

			$checkedByName	=	$employeeObj->getEmployeeName($checkedBy);
		}

		$query3				=	"SELECT * FROM orders_new_checkboxes WHERE orderId=$orderId";
		$result3			=	dbQuery($query3);
		if(mysql_num_rows($result3))
		{
			$row3			=	mysql_fetch_assoc($result3);
			$reoForm		=	$row3['reoForm'];
			$financingVa	=	$row3['financingVa'];
			$financingFha	=	$row3['financingFha'];
			$financingHud	=	$row3['financingHud'];
			$nonUad			=	$row3['nonUad'];
			$showCheckboxes =	true;
		}
		else
		{
			$showCheckboxes =	false;
		}

		if(isset($_GET['acceptUnacceptSketch']) && isset($_GET['isAcceptDoneSketch']) && $_GET['isAcceptDoneSketch'] == 1)
		{
			$acceptUnacceptSketch	=	$_GET['acceptUnacceptSketch'];
			if($acceptUnacceptSketch==  1)
			{
				dbQuery("UPDATE members_orders SET sketchStatus=1,sketchAcceptBy=$s_employeeId,sketchAcceptedOn='".CURRENT_DATE_INDIA."',sketchAcceptedTime='".CURRENT_TIME_INDIA."' WHERE orderId=$orderId AND memberId=$customerId AND sketchStatus=0");
			}
			elseif($acceptUnacceptSketch== 2)
			{
				dbQuery("UPDATE members_orders SET sketchStatus=2,sketchDoneBy=$s_employeeId,sketchDoneOn='".CURRENT_DATE_INDIA."',sketchDoneTime='".CURRENT_TIME_INDIA."' WHERE orderId=$orderId AND memberId=$customerId AND sketchStatus=1");
			}

			ob_clean();
			header("Location: ".SITE_URL."/".$pageUrl."?orderId=$orderId&customerId=$customerId");
			exit();
		}
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
		exit();
	}

	if(isset($_REQUEST['checkFormSubmitted']))
	{
		extract($_REQUEST);
		if(isset($_POST['markedAsChecked']))
		{
			$markedAsChecked	=	$_POST['markedAsChecked'];
		}
		else
		{
			$markedAsChecked	=	0;
		}
		if(isset($_POST['checkedMessage']))
		{
			$checkedMessage		=	$_POST['checkedMessage'];
			$checkedMessage		=	trim($checkedMessage);
			$checkedMessage		=	makeDBSafe($checkedMessage);
		}
		else
		{
			$checkedMessage		=	"";
		}

		if($markedAsChecked		== 1 && empty($existingCheckId))
		{
			dbQuery("INSERT INTO checked_customer_orders SET checkedBy=$s_employeeId,orderId=$orderId,checkedOn='".CURRENT_DATE_INDIA."',checkedOnTime='".CURRENT_TIME_INDIA."',checkedIP='".VISITOR_IP_ADDRESS."',checkedMessage='$checkedMessage'");
		}
		elseif($markedAsChecked == 2 && !empty($existingCheckId))
		{
			dbQuery("DELETE FROM checked_customer_orders WHERE checkedId=$checkedId");
		}

		ob_clean();
		header("Location: ".SITE_URL."/".$pageUrl."?orderId=$orderId&customerId=$customerId");
		exit();
	}
?>
<script type="text/javascript">
function openNoteWindow(customerId)
{
	path = "<?php echo SITE_URL_EMPLOYEES;?>/edit-special-note.php?customerId="+customerId;
	prop = "toolbar=no,scrollbars=yes,width=650,height=550,top=50,left=100";
	window.open(path,'',prop);
}
function openEditCheckMessage(checkedId)
{
	path = "<?php echo SITE_URL_EMPLOYEES;?>/edit-order-checked-message.php?checkedId="+checkedId;
	prop = "toolbar=no,scrollbars=yes,width=600,height=400,top=50,left=100";
	window.open(path,'',prop);
}

function checkEmployeeOrder()
{
	form1	=	document.checkOrder;
	if(form1.markedAsChecked.checked == false)
	{
		alert("Please checked the checkbox given !!");
		form1.markedAsChecked.focus();
		return false;
	}
}
function accepetDoneSketchFile(page,orderId,customerId,flag)
{
	if(flag	==	1)
	{
		var confirmation = window.confirm("Are You Sure To Accept This Sketch?");
	}
	else
	{
		var confirmation = window.confirm("Are You Sure To Done This Sketch?");
	}
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL;?>"+page+"?orderId="+orderId+"&customerId="+customerId+"&acceptUnacceptSketch="+flag+"&isAcceptDoneSketch=1";
	}
}
</script>
<table width='98%' align='center' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<td align="left">
			<?php
				include(SITE_ROOT_EMPLOYEES . "/includes/next-previous-order.php");
			?>
		</td>
	</tr>
	<?php
		if($isHavingEstimatedTime ==	1 && ($status	==	0 || $status	==	1))
		{
			
			$expctDelvText		  =		orderTAT($employeeWarningDate,$employeeWarningTime);
			
		?>
		<tr>
			<td valign="top">
				<font style="font-size:16px;font-family:verdana;color:#ff0000;font-weight:bold">Delivery Time Left : <?php echo $expctDelvText;?></font>
			</td>
		</tr>
		<?php
		}
	?>
</table>
<fieldset style="border:1px solid #333333">
	<legend class="heading3"><b>ORDER SUMMARY</b></legend>
	
		<table width="100%" align="center" border="0" cellpadding="2" cellspacing="2">
			<tr>
				<td width="10%" class="smalltext2">ORDER NO</td>
				<td width="1%" class="smalltext2">:</td>
				<td colspan="4" class="smalltext2">
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $customerId;?>"><?php echo $orderAddress;?></a>&nbsp;(<font color='#ff0000'><b><?php echo $state;?></b></font>)
				</td>
				<td width="10%" class="smalltext2">FILE TYPE</td>
				<td width="1%" class="smalltext2">:</td>
				<td class="error">
					<b><?php echo $appraisalText;?></b>
				</td>
			</tr>
			<tr>
				<td colspan="3" valign="top">
					<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td class="smalltext2" width="34%" valign="top">ORDER TYPE</td>
							<td class="smalltext2" width="3%" valign="top" align="center">&nbsp;:</td>
							<td class="smalltext14" valign="top">
								&nbsp;<?php echo $orderText;?>
							</td>
						</tr>
						<tr>
							<td colspan="3" height="5"></td>
						</tr>
						<tr>
							<td class="smalltext2" valign="top">SKETCH</td>
							<td class="smalltext2" valign="top" align="center">&nbsp;:</td>
							<td class="smalltext14" valign="top">
								&nbsp;<?php echo $hasMarkedSketchYes;?>
							</td>
						</tr>
						<?php
							if($providedSketch		==	1)
							{
								if(empty($sketchStatus))
								{
						?>
						<tr>
							<td colspan="3" height="5"></td>
						</tr>
						<tr>
							<td class="smalltext2" valign="top">&nbsp;</td>
							<td class="smalltext2" valign="top" align="center">&nbsp;</td>
							<td class="smalltext14" valign="top">
								<a onclick="accepetDoneSketchFile('<?php echo $pageUrl;?>',<?php echo $orderId;?>,<?php echo $customerId;?>,1);" style="cursor:pointer;" title="Accept Sketch" class="link_style12">Accept This Sketch</a>
							</td>
						</tr>
						<?php
								}
								else
								{
									$sketchAcceptByEmp	=	$employeeObj->getEmployeeName($sketchAcceptBy);
						?>
						<tr>
							<td colspan="3" height="5"></td>
						</tr>
						<tr>
							<td class="smalltext2" valign="top">SKETCH ACCEPTED</td>
							<td class="smalltext2" valign="top" align="center">&nbsp;:</td>
							<td class="smalltext14" valign="top">
								&nbsp;<?php echo $sketchAcceptByEmp;?>
							</td>
						</tr>
						<?php
							if($sketchStatus == 1)
							{
								if($sketchAcceptBy == $s_employeeId || !empty($s_hasManagerAccess)) 
								{
							?>
							<tr>
								<td colspan="3" height="5"></td>
							</tr>
							<tr>
								<td class="smalltext2" valign="top">&nbsp;</td>
								<td class="smalltext2" valign="top" align="center">&nbsp;</td>
								<td class="smalltext14" valign="top">
									<a onclick="accepetDoneSketchFile('<?php echo $pageUrl;?>',<?php echo $orderId;?>,<?php echo $customerId;?>,2);" style="cursor:pointer;" title="Accept Sketch" class="link_style12">Done This Sketch</a>
								</td>
							</tr>
							<?php
								}
							}
							elseif($sketchStatus == 2)
							{
									$sketchDonetByEmp	=	$employeeObj->getEmployeeName($sketchDoneBy);
							?>
								<tr>
									<td colspan="3" height="5"></td>
								</tr>
								<tr>
									<td class="smalltext2" valign="top">SKETCH DONE</td>
									<td class="smalltext2" valign="top" align="center">&nbsp;:</td>
									<td class="smalltext14" valign="top">
										&nbsp;<?php echo $sketchDonetByEmp;?>
									</td>
								</tr>
							<?php
							}
						  }
						}
						if($showCheckboxes		==	true)
						{
							$reoFromText			=	"NO";
							$financingVaText		=	"NO";
							$financingFhaText		=	"NO";
							$financingHudText		=	"NO";
							$nonUadText				=	"NO";
							if($reoForm				==	1)
							{
								$reoFromText		=	"<font color='red'>YES</font>";
							}
							if($financingVa			==	1)
							{
								$financingVaText	=	"<font color='red'>YES</font>";
							}
							if($financingFha		==	1)
							{
								$financingFhaText	=	"<font color='red'>YES</font>";
							}
							if($financingHud		==	1)
							{
								$financingHudText	=	"<font color='red'>YES</font>";
							}
							if($nonUad				==	1)
							{
								$nonUadText			=	"<font color='red'>YES</font>";
							}
					?>
						<tr>
							<td colspan="3" height="5"></td>
						</tr>
						<tr>
							<td class="smalltext2" valign="top">REO Form</td>
							<td class="smalltext2" valign="top" align="center">&nbsp;:</td>
							<td valign="top" class="smalltext14">
								&nbsp;<?php echo $reoFromText;?>
							</td>
						</tr>
						<tr>
							<td colspan="3" height="5"></td>
						</tr>
						<tr>
							<td class="smalltext2" valign="top">Financing</td>
							<td class="smalltext2" valign="top" align="center">&nbsp;:</td>
							<td valign="top" class="smalltext14">
								&nbsp;VA : <?php echo $financingVaText;?> &nbsp;
								 FHA : <?php echo $financingFhaText;?> &nbsp;
								 HUD : <?php echo $financingHudText;?> &nbsp;
							</td>
						</tr>
						<tr>
							<td colspan="3" height="5"></td>
						</tr>
						<tr>
							<td class="smalltext2" valign="top">NON-UAD</td>
							<td class="smalltext2" valign="top" align="center">&nbsp;:</td>
							<td valign="top" class="smalltext14">
								&nbsp;<?php echo $nonUadText;?>
							</td>
						</tr>
					<?php
						}
					?>
					</table>
				</td>
				<td class="smalltext2" valign="top" width="12%">ORDER DATE</td>
				<td class="smalltext2" valign="top" width="1%">:</td>
				<td class="smalltext14" valign="top" width="15%">
					<?php echo $orderAddedOn." ".$displayOrderTimeFormat."<br>".$displayZoneTime;?>
				</td>
				<td class="smalltext2" valign="top">CUSTOMER</td>
				<td class="smalltext2" valign="top">:</td>
				<td valign="top" class='smalltext2'>
					
						<?php 
							echo "<font size='3'><b> <a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=$customerId' class='$customerLinkStyle'>".ucwords($customerName)."</a><font class='$customerLinkStyle'>".$customerOrderText."</font></a>";
							
						?></b></font>
						<br>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=<?php echo $customerId?>" class="link_style12">Click here to view all orders of this customer</a>
				</td>
			</tr>
			<tr>
				<td class="smalltext2">STATUS</td>
				<td class="smalltext2">:</td>
				<td class="smalltext14" width="20%">
					<?php echo $statusText.$newAttentionUnmarkTxt;?>
				</td>
				<td class="smalltext2">ACCEPTED BY</td>
				<td class="smalltext2">:</td>
				<td class="smalltext14" colspan="4">
					<?php echo $acceptedText;?>
				</td>
				<!-- <td class="smalltext2">UAD COMPLIANT</td>
				<td class="smalltext2">:</td>
				<td class="smalltext14">
					<?php echo $uadText;?>
				</td> -->
			</tr>
			<tr>
				<td class="smalltext2">COMPLETED ON</td>
				<td class="smalltext2">:</td>
				<td class="smalltext14">
					<?php echo $orderCompletedOn;?>
				</td>
				<td class="smalltext2">QA DONE BY</td>
				<td class="smalltext2">:</td>
				<td class="smalltext14">
					<?php echo $qaDoneByText;?>
				</td>
				<td class="smalltext2">RUSH ORDER</td>
				<td class="smalltext2">:</td>
				<td class="smalltext14">
					<?php echo $isRushOrderText;?>
				</td>
			</tr>
		</table>

</fieldset>
<table width='98%' align='center' cellpadding='3' cellspacing='3' border='0'>
	<tr>
		<td colspan="15">
			<!-- View orders checked by and comments -->
			<form name="checkOrder" action="" method="POST" onsubmit="return checkEmployeeOrder();">
			<table width='100%' align='center' cellpadding='3' cellspacing='3' border='0'>
				<tr>
					<td width="45%" class="smalltext2">
						<?php
							if(empty($checkedId))
							{
								echo "<input type='checkbox' name='markedAsChecked' value='1'>Checked";
							}
							else
							{
								echo "<input type='checkbox' name='markedAsChecked' value='2'>Click here to unmarked as checked by - <b>".$checkedByName."</b>";
							}
						?>
					</td>
					<?php
						if(empty($checkedId))
						{
					?>
						<td width="15%" class="smalltext2">Checked Comments :</td>
						<td width="30%">
							<input type="text" name="checkedMessage" value="<?php echo $checkedMessage;?>" size="50" style="border:1px solid #333333">
						</td>
					<?php
						}
					?>
					<td>
						<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
						<input type='hidden' name='checkFormSubmitted' value='1'>
						<input type='hidden' name='existingCheckId' value='<?php echo $checkedId;?>'>
					</td>
				</tr>
				<?php
					if(!empty($checkedId) && !empty($checkedMessage))
					{
				?>
				<td colspan="5" valign="top" class="textstyle1"><b>Checked Comments :</b>
					<?php
						echo nl2br($checkedMessage);
					?>&nbsp;&nbsp;(<a onclick="openEditCheckMessage(<?php echo $checkedId;?>)" class="link_style2" style="cursor:pointer;" title="Edit">Edit</a>)
				</td>
				<?php
					}
				?>
			</table>
			</form>
		</td>
	</tr>
</table>
<br>
<!-- **************************CUSTOMER ORDER DETAILS***********************-->
<script type="text/javascript">
	function showHideCustomerOrder1(flag)
	{
		if(flag)
		{
			document.getElementById('showHideCustomerOrder').style.display 	   = 'inline';
			document.getElementById('showAndHideCustomerOrderFiles').innerHTML   = "<a href='javascript:showHideCustomerOrder1(0)'><img src='<?php echo SITE_URL;?>/images/hide.jpg' border='0' title='Hide'></a>";
		}
		else
		{
			document.getElementById('showHideCustomerOrder').style.display 	= 'none';
			document.getElementById('showAndHideCustomerOrderFiles').innerHTML= "<a href='javascript:showHideCustomerOrder1(1)'><img src='<?php echo SITE_URL;?>/images/show.jpg' border='0' title='Hide'></a>";
		}
	}
</script>
<br>
<fieldset style="border:1px solid #333333">
	<legend class="heading3"><b>ORDER FILES</b></legend>

		<table width="100%" align="center" border="0" cellpadding="2" cellspacing="2">
			<tr>
				<td colspan="3" align="left">
					<div id="showAndHideCustomerOrderFiles">
						<a href="javascript:showHideCustomerOrder1(0)"><img src="<?php echo SITE_URL;?>/images/hide.jpg" border='0' title='Hide'></a>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="3" valign="top">
					<div id="showHideCustomerOrder">
						<table width="100%" cellpadding="3" cellspacing="3" border="0">
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
									if($isNewUploadingSystem	==	1)
									{
										include(SITE_ROOT_EMPLOYEES."/includes/display-multiple-order-files.php");
									}
									else
									{
							?>
							<tr>
								<td class="smalltext2" width="23%" valign="top"><b><?php echo $uploadedFileByCustomer;?></b></td>
								<td class="smalltext2" width="2%"  valign="top"><b>:</b></td>
								<td valign="top" class="smalltext2">
									<?php 
										if($hasOrderFile)
										{
											$downloadedText	=	$orderObj->getFileCronDownloadedStatus($orderId,$customerId,"orderFile",0);

											echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=OF&f=N' class='link_style2'>".$orderFileName.".".$orderFileExt."</a>";
											
											echo "<br><font class='smalltext2'>".getFileSize($orderFileSize)."&nbsp;".$downloadedText."</font>";
										}
										else
										{
											echo "N/A";
										}
									?>
								</td>
							</tr>
							<tr>
								<td class="smalltext2" valign="top"><b>Uploaded Public Records File</b></td>
								<td class="smalltext2" valign="top"><b>:</b></td>
								<td valign="top" class="smalltext2">
									<?php 
										if($hasPublicRecordFile)
										{
											$downloadedText	=	$orderObj->getFileCronDownloadedStatus($orderId,$customerId,"publicFile",0);
											
											echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=PF&f=N' class='link_style2'>".$publicRecordFileName.".".$publicRecordFileExt."</a>";
											echo "<br><font class='smalltext2'>".getFileSize($publicRecordFileSize)."&nbsp;".$downloadedText."</font>";
										}
										else
										{
											echo "N/A";
										}
									?>
								</td>
							</tr>
							<tr>
								<td class="smalltext2" valign="top"><b>Uploaded MLS File</b></td>
								<td class="smalltext2" valign="top"><b>:</b></td>
								<td valign="top" class="smalltext2">
									<?php 
										if($hasMlsFile)
										{
											$downloadedText	=	$orderObj->getFileCronDownloadedStatus($orderId,$customerId,"mlsFile",0);

											echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=MF&f=N' class='link_style2'>".$mlsFileName.".".$mlsFileExt."</a>";
											echo "<br><font class='smalltext2'>".getFileSize($mlsFileSize)."&nbsp;".$downloadedText."</font>";
										}
										else
										{
											echo "N/A";
										}
									?>
								</td>
							</tr>
							<tr>
								<td class="smalltext2" valign="top"><b>Uploaded Market Conditions File</b></td>
								<td class="smalltext2" valign="top"><b>:</b></td>
								<td valign="top" class="smalltext2">
									<?php 
										if($hasMarketConditionFile)
										{
											$downloadedText	=	$orderObj->getFileCronDownloadedStatus($orderId,$customerId,"marketFile",0);

											
											echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=CF&f=N' class='link_style2'>".$marketConditionFileName.".".$marketConditionExt."</a>";
											echo "<br><font class='smalltext2'>".getFileSize($marketConditionFileSize)."&nbsp;".$downloadedText."</font>";
										}
										else
										{
											echo "N/A";
										}
									?>
								</td>
							</tr>
							<tr>
								<td class="smalltext2" valign="top"><b>Uploaded Field Inspection Notes</b></td>
								<td class="smalltext2" valign="top"><b>:</b></td>
								<td valign="top" class="smalltext2">
									<?php 
										if($hasOtherFile)
										{
											$downloadedText	=	$orderObj->getFileCronDownloadedStatus($orderId,$customerId,"otherFile",0);
											
											echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=OTF&f=N' class='link_style2'>".$otherFileName.".".$otherFileExt."</a>";
											echo "<br><font class='smalltext2'>".getFileSize($otherFileSize)."&nbsp;".$downloadedText."</font>";
										}
										else
										{
											echo "N/A";
										}
									?>
								</td>
							</tr>
							<?php
								$query	=	"SELECT * FROM other_order_files WHERE orderId=$orderId AND uploadingFor=1";
								$result		=	mysql_query($query);
								if(mysql_num_rows($result))
								{
							?>
							<tr>
								<td class="smalltext2" valign="top"><b>Uploaded More Files</b></td>
								<td class="smalltext2" valign="top"><b>:</b></td>
								<td valign="top">
							<?php
									while($row		=	mysql_fetch_assoc($result))
									{
										$otherId		=	$row['otherId'];
										$fileName		=	$row['fileName'];
										$fileExtension	=	$row['fileExtension'];
										$fileSize		=	$row['fileSize'];

										$downloadedText	=	$orderObj->getFileCronDownloadedStatus($orderId,$customerId,"moreFile",$otherId);

										echo "<a href='".SITE_URL_EMPLOYEES."/other-download.php?ID=$otherId&t=OT' class='link_style2'><b>".$fileName.".".$fileExtension."</b></a>";
										echo "<br><font class='smalltext2'>".getFileSize($fileSize)."&nbsp;".$downloadedText."</font>";	
										echo "<br>";
										
									}
								echo "</td></tr>";
								}
							  }
							}
							?>
							<tr>
								<td class="smalltext2" valign="top" width="23%"><b>Customer Instructions</b></td>
								<td class="smalltext2" valign="top" width="2%"><b>:</b></td>
								<td valign="top" class="error">
									<?php echo nl2br($instructions);?>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
		</table>
	
</fieldset>
<br>
<script type="text/javascript">
	function showHideInstructionsAndFiles(flag)
	{
		if(flag)
		{
			document.getElementById('showHideInstructions').style.display 	   = 'inline';
			document.getElementById('showAndHideCustomerInstructions').innerHTML   = "<a href='javascript:showHideInstructionsAndFiles(0)'><img src='<?php echo SITE_URL;?>/images/hide.jpg' border='0' title='Hide'></a>";
		}
		else
		{
			document.getElementById('showHideInstructions').style.display 	= 'none';
			document.getElementById('showAndHideCustomerInstructions').innerHTML= "<a href='javascript:showHideInstructionsAndFiles(1)'><img src='<?php echo SITE_URL;?>/images/show.jpg' border='0' title='Hide'></a>";
		}
	}
</script>
<!-- ************************* CUSTOMER INSTRUCTIONS AND FILES*************************** -->
<?php
	if($instructionsUpdatedOn	!=	"0000-00-00")
	{
		$diffHours				=	getHoursNumericBetweenDates($instructionsUpdatedOn,CURRENT_DATE_INDIA);
		
		if($diffHours <= 72)
		{
			$newDiffImage			=	"<img src='".SITE_URL."/images/blinking-new.gif' borde='0'>";
		}
		else
		{
			$newDiffImage			=	"";
		}
	}
	else
	{
		$newDiffImage			=	"";
	}
?>
<fieldset style="border:1px solid #333333">
	<legend class="heading3"><b>CUSTOMER INSTRUCTIONS</b><?php echo $newDiffImage;?></legend>
	
		<table width="100%" align="center" border="0" cellpadding="2" cellspacing="2">
			<tr>
				<td colspan="3" align="left">
					<div id="showAndHideCustomerInstructions">
						<a href="javascript:showHideInstructionsAndFiles(0)"><img src="<?php echo SITE_URL;?>/images/hide.jpg" border='0' title='Hide'></a>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="3" valign="top">
					<div id="showHideInstructions">
						<table width="100%" cellpadding="3" cellspacing="3" border="0">
							<tr>
								<td width="60%" valign="top">
									<table width="100%" cellpadding="3" cellspacing="3" border="0">
										<?php
											if(!empty($splInstructionToEmployee))
											{
										?>
										<tr>
											<td colspan="3" class="text"><b>SPECIAL INSTRUCTIONS FROM <?php echo  ucwords($customerName);?></b></td>
										</tr>
										<tr>
											<td colspan="3" class="textstyle">>
												<p align="justify">
													<?php echo nl2br($splInstructionToEmployee);?>
												</p>
											</td>
										</tr>
									<?php
										}
										else
										{
											echo "<tr><td colspan='3' class='smalltext2' align='center'>No Instructions Available</td></tr>";
										}
									?>
								</table>
							</td>
							<td valign="top">
								<table width="100%" cellpadding="3" cellspacing="3" border="0">
									<?php
										$query	=	"SELECT * FROM customer_instructions_file WHERE memberId=$customerId AND uploadedBy='".CUSTOMERS."' ORDER BY addedOn,addedTime";
										$result	=	dbQuery($query);
										if(mysql_num_rows($result))
										{
									?>
										<tr>
											<td colspan="3" class="text"><b>CUSTOMER INSTRUCTIONS FILES</b></td>
										</tr>
										<tr>
											<td colspan="3">
												<table width="100%" cellpadding="3" cellspacing="3" border="0">
													<?php
														$i	=	0;
														while($row	=	mysql_fetch_assoc($result))
														{
															$i++;
															$instructionId	=	$row['instructionId'];
															$fileName		=	$row['fileName'];
															$fileExt		=	$row['fileExt'];
															$size			=	$row['size'];
															$fileAddedOn	=	$row['addedOn'];
															$fileAddeddate	=	showDate($fileAddedOn);
													
															$instructionDifferent	= (strtotime(date("Y-m-d")) - strtotime($fileAddedOn)) / (60 * 60 * 24);
															$newInstructionText		=	"";
															if($instructionDifferent <= 10)
															{
																$instructionDaysDifferent	=	1;
																$newInstructionText	=	"<img src='".SITE_URL."/images/blinking-new.gif' width='30' height='15'>";
															}
													?>
														<tr>
															<td width="5%" class="smalltext2" valign="top"><b><?php echo $i;?>)</b><?php echo $newInstructionText;?></td>
															<td valign="top">
																<?php
													echo "<a href='".SITE_URL_EMPLOYEES."/download-instructions.php?ID=$instructionId&memberId=$customerId'  class='linkstyle6'><b>".$fileName.".".$fileExt."</b></a>";
													echo "&nbsp;<font class='smalltext1'>".getFileSize($size)."<br>(".$fileAddeddate.")</font>";
																?>
															</td>
														</tr>
														<tr>
															<td colspan="2" height='5'></td>
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
							</td>
						</tr>
					 </table>
					</div>
				</td>
			</tr>
		</table>
	
</fieldset>
<!-- **************************EMPLOYEE INSTRUCTIONS NOTES AND FILES***********************-->
<script type="text/javascript">
	function showHideEmployeeNotesAndFiles(flag)
	{
		if(flag)
		{
			document.getElementById('showHideNotes').style.display 	   = 'inline';
			document.getElementById('showAndHideEmployeeNotes').innerHTML   = "<a href='javascript:showHideEmployeeNotesAndFiles(0)'><img src='<?php echo SITE_URL;?>/images/hide.jpg' border='0' title='Hide'></a>";
		}
		else
		{
			document.getElementById('showHideNotes').style.display 	= 'none';
			document.getElementById('showAndHideEmployeeNotes').innerHTML= "<a href='javascript:showHideEmployeeNotesAndFiles(1)'><img src='<?php echo SITE_URL;?>/images/show.jpg' border='0' title='Hide'></a>";
		}
	}
	function downloadGeneralMessageFile(url)
	{
		//window.open(url, "_blank");
		 location.href   = url;
	}
</script>
<br>
<fieldset style="border:1px solid #333333">
	<legend class="heading3"><b>EMPLOYEE NOTES AND NOTE FILES</b></legend>
		<table width="100%" align="center" border="0" cellpadding="2" cellspacing="2">
			<tr>
				<td colspan="3" align="left">
					<div id="showAndHideEmployeeNotes">
						<a href="javascript:showHideEmployeeNotesAndFiles(0)"><img src="<?php echo SITE_URL;?>/images/hide.jpg" border='0' title='Hide'></a>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="3" valign="top">
					<div id="showHideNotes">
						<table width="100%" cellpadding="3" cellspacing="3" border="0">
							<tr>
								<td align="left" colspan='3'>
									<a onclick="javascript:openNoteWindow(<?php echo $customerId;?>);" class="link_style3" style="cursor:pointer">EDIT</a>
									<!-- <input type="button" name="submit" onClick="javascript:openNoteWindow(<?php echo $customerId;?>);" value="EDIT"> -->
								</td>
							</tr>
							<?php
								if(!empty($splInstructionOfCustomer))
								{
							?>
							<tr>
								<td class="textstyle" colspan='3'>
									<p align="justify">
										<?php echo nl2br($splInstructionOfCustomer);?>
									</p>
								</td>
							</tr>
							<?php
								}
								else
								{
									echo "<tr><td  class='smalltext2' align='center' colspan='3'>No Note Available</td></tr>";
								}
								$query	=	"SELECT * FROM customer_instructions_file WHERE memberId=$customerId AND uploadedBy='".EMPLOYEES."' ORDER BY fileName";
								$result	=	dbQuery($query);
								if(mysql_num_rows($result))
								{
							?>
							<tr>
								<td colspan="3" class="text"><b>EMPLOYEE NOTE FILES</b></td>
							</tr>
										
							<?php
								$i	=	0;
								while($row	=	mysql_fetch_assoc($result))
								{
									$i++;
									$instructionId	=	$row['instructionId'];
									$fileName		=	$row['fileName'];
									$fileExt		=	$row['fileExt'];
									$size			=	$row['size'];
								?>
										<tr>
											<td width="5%" class="smalltext2" valign="top"><b><?php echo $i;?>)</b></td>
											<td valign="top" width="30%" valign="top">
												<?php
													echo "<a href='".SITE_URL_EMPLOYEES."/download-instructions.php?ID=$instructionId&memberId=$customerId'  class='link_style12'><b>".$fileName.".".$fileExt."</b></a>";
													echo "&nbsp;<font class='smalltext1'>".getFileSize($size)."</font>"
												?>
											</td>
											<td valign="top">
												<a onclick="deleteNoteInstructionsFile(<?php echo $instructionId?>,'<?php echo $pageUrl;?>',<?php echo $customerId;?>,<?php echo $orderId?>)" style="cursor:pointer;">
													<img src="<?php echo SITE_URL;?>/images/delete.gif" border="0">
												</a>
											</td>
										</tr>
										<tr>
											<td colspan="3" height='5'></td>
										</tr>
										<?php
											}
										}	
										?>
									</table>
								</td>
						  </tr>
					</table>
					</div>
				</td>
			</tr>
		</table>
	</fieldset>
<br>
<!-- **************************CUSTOMER PREVIOUS MESSAGES AND RATIONS***********************-->
<script type="text/javascript">
	function showHideCustomerMessagesRatings(flag)
	{
		if(flag)
		{
			document.getElementById('showHideMessageRatings').style.display 	   = 'inline';
			document.getElementById('showAndHideRatings').innerHTML   = "<a href='javascript:showHideCustomerMessagesRatings(0)'><img src='<?php echo SITE_URL;?>/images/hide.jpg' border='0' title='Hide'></a>";
		}
		else
		{
			document.getElementById('showHideMessageRatings').style.display 	= 'none';
			document.getElementById('showAndHideRatings').innerHTML= "<a href='javascript:showHideCustomerMessagesRatings(1)'><img src='<?php echo SITE_URL;?>/images/show.jpg' border='0' title='Hide'></a>";
		}
	}
</script>
<br>
	<fieldset style="border:1px solid #333333">
	<legend class="heading3"><b>CUSTOMER MESSAGES & RATINGS</b></legend>
		<table width="100%" align="center" border="0" cellpadding="2" cellspacing="2">
			<tr>
				<td colspan="3" align="left">
					<div id="showAndHideRatings">
						<a href="javascript:showHideCustomerMessagesRatings(0)"><img src="<?php echo SITE_URL;?>/images/hide.jpg" border='0' title='Hide'></a>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="3" valign="top">
					<div id="showHideMessageRatings">
						<table width="100%" cellpadding="3" cellspacing="3" border="0">
							<tr>
								<td class="heading5" colspan="6">PREVIOUS ORDER MESSAGES FROM - <?php echo $customerName;?></td>
							</tr>
							<?php
							$calculatedOrderFrom	=	getPreviousGivenDate($nowDateIndia,30);
							//*****Section to show previous 10 customer messages if any given*****//
							if($result		=	$orderObj->previousOrdersMessages($orderId,$customerId,10,$calculatedOrderFrom))
							{
							?>
							<tr>
								<td width="4%">&nbsp;</td>
								<td width="25%" class="textstyle"><b>Order No</b></td>
								<td width="1%">&nbsp;</td>
								<td class="textstyle" width="7%"><b>Date</b></td>
								<td width="1%">&nbsp;</td>
								<td class="textstyle"><b>Message</b></td>
							</tr>
							<tr>
								<td colspan="6">
									<hr size="1" width="100%" color="#bebebe">
								</td>
							</tr>
							
							<?php
								$messageCount			=	0;
								while($messageRow		=	mysql_fetch_assoc($result))
								{
									$messageCount++;
									$m_messageId		=  $messageRow['messageId'];
									$m_orderId			=  $messageRow['orderId'];
									$m_message			=  stripslashes($messageRow['message']);
									$messageDate		=	$messageRow['addedOn'];
									$messageTime		=	$messageRow['addedTime'];


									$m_message			=  nl2br($m_message);

									$m_orderName		=	@mysql_result(dbQuery("SELECT orderAddress FROM members_orders WHERE orderId=$m_orderId"),0);

									$m_orderName		=	stripslashes($m_orderName);

																	

									$hoursMessageDiff	=	timeBetweenTwoTimes($messageDate,$messageTime,$nowDateIndia,$nowTimeIndia);

									if($hoursMessageDiff <= 2880)
									{
										$customerMessageImage	=	"<img src='".SITE_URL."/images/blinking-new.gif' title='New Ratings' width='30' height='15'>";
									}
									else
									{
										$customerMessageImage	=	"";
									}

								?>
									<tr>
										<td class="textstyle" valign="top"><?php echo $messageCount;?>)</td>
										<td class="textstyle" valign="top">
											<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $m_orderId;?>&customerId=<?php echo $customerId;?>#messages" class='link_style12'><?php echo $m_orderName;?></a><br>
												<?php
													echo $customerMessageImage;
												?>
											</td>
											<td>&nbsp;</td>
											<td class="textstyle" valign="top"><?php echo showDate($messageDate);?></td>
											<td width="1%">&nbsp;</td>
											<td class="textstyle" valign="top"><?php echo $m_message;?></td>
											</tr>
										<tr>
											<td colspan="6">
												<hr size="1" width="100%" color="#bebebe">
											</td>
										</tr>
									<?php
										}
																
									}
									elseif($result		=	$orderObj->previousAllOrdersMessages($customerId,10))
									{
								?>
								<tr>
									<td width="5%">&nbsp;</td>
									<td class="textstyle" width="7%"><b>Date</b></td>
									<td class="textstyle"><b>Message</b></td>
								</tr>
								<tr>
									<td colspan="6">
										<hr size="1" width="100%" color="#bebebe">
									</td>
								</tr>
								
							<?php
								$messageCount			=	0;
								while($messageRow		=	mysql_fetch_assoc($result))
								{
									$messageCount++;
									$m_messageId		=  $messageRow['generalMsgId'];
									$m_message			=  stripslashes($messageRow['message']);
									$messageDate		=	$messageRow['addedOn'];
									$messageTime		=	$messageRow['addedtime'];
									$isUploadedFiles	=	$messageRow['isUploadedFiles'];

									$m_message			=  nl2br($m_message);

									$hoursMessageDiff	=	timeBetweenTwoTimes($messageDate,$messageTime,$nowDateIndia,$nowTimeIndia);

									if($hoursMessageDiff <= 2880)
									{
										$customerMessageImage	=	"<img src='".SITE_URL."/images/blinking-new.gif' title='New Ratings' width='30' height='15'>";
									}
									else
									{
										$customerMessageImage	=	"";
									}

								?>
									<tr>
										<td class="textstyle" valign="top"><?php echo $messageCount.$customerMessageImage;?>)</td>
										<td class="textstyle" valign="top"><?php echo showDate($messageDate);?></td>
										<!--<td class="textstyle" valign="top"><?php echo $m_message;?></td>-->
										<td valign="top">
											<table width="100%" align="center" cellpadding="0" cellspacing="0">
												<tr>
													<td colspan="2" class="textstyle">
														<?php 
															echo nl2br($m_message);
														?>
													</td>
												</tr>
												<?php
													if($isUploadedFiles == 1)	
													{
														
														if($a_files	=	$orderObj->getCustomerGeneralMessageEmailFiles($customerId,$m_messageId))
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
														<td width="3%" class="smalltext22" valign="top">
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
										</tr>
									<tr>
										<td colspan="6">
											<hr size="1" width="100%" color="#bebebe">
										</td>
									</tr>
									<?php
										}
																
									}
									else
									{
										echo "<tr><td colspan='6' class='smalltext2' align='center'>No Message Available</td></tr>";
									}
								?>
							    </table>
								<table width="100%" cellpadding="3" cellspacing="3" border="0">
								   <tr>
										<td class="heading5" colspan="8">RATINGS BY <?php echo $customerName;?> IN 10 PREVIOUS ORDERS</td>
									</tr>
									<?php
										if($result	=	$orderObj->previousOrdersCustomerRatingComments($orderId,$customerId,10,$calculatedOrderFrom))
										{
									?>
											<tr>
												<td width="3%">&nbsp;</td>
												<td width="25%" class="textstyle"><b>Order No</b></td>
												<td width="1%">&nbsp;</td>
												<td class="textstyle" width="20%"><b>Rating</b></td>
												<td width="1%">&nbsp;</td>
												<td class="textstyle" width="15%"><b>Date</b></td>
												<td width="1%">&nbsp;</td>
												<td class="textstyle"><b>Message</b></td>
											</tr>
											<tr>
												<td colspan="8">
													<hr size="1" width="100%" color="#bebebe">
												</td>
											</tr>
											<?php
												$customerRatingCount	=	0;
												while($ratingCustomerRow=	mysql_fetch_assoc($result))
												{
													$customerRatingCount++;
													$cr_orderId			=  $ratingCustomerRow['orderId'];
													$cr_rateGiven		=  $ratingCustomerRow['rateGiven'];
													$cr_rateGivenOn		=  $ratingCustomerRow['rateGivenOn'];
													$cr_message			=  stripslashes($ratingCustomerRow['memberRateMsg']);
													$cr_message			=  nl2br($cr_message);
													$cr_orderName		=  $ratingCustomerRow['orderAddress'];
													$cr_orderName		=  stripslashes($cr_orderName);

													$hoursDiff	=	timeBetweenTwoTimes($cr_rateGivenOn,$nowTimeIndia,$nowDateIndia,$nowTimeIndia);

													if($hoursDiff <= 2880)
													{
														$rateMessageImage	=	"<img src='".SITE_URL."/images/blinking-new.gif' title='New Ratings'>";
													}
													else
													{
														$rateMessageImage	=	"";
													}
													?>
													<tr>
														<td class="smalltext" valign="top"><?php echo $customerRatingCount;?>)</td>
														<td class="textstyle" valign="top">
															<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $cr_orderId;?>&customerId=<?php echo $customerId;?>" class='link_style12'><?php echo $cr_orderName;?></a>
															<br>
															<?php echo $rateMessageImage;?>
														</td>
														<td>&nbsp;</td>
														<td class="textstyle" valign="top">
															<?php
																if(!empty($cr_rateGiven))
																{
																	for($i=1;$i<=$cr_rateGiven;$i++)
																	{
																		echo "<img src='".SITE_URL."/images/star.gif'  width=12 height=12'>";
																	}
																	echo $a_existingRatings[$cr_rateGiven];
																}
																else
																{
																	echo "&nbsp;";
																}
															?>
															
														</td>
														<td>&nbsp;</td>
														<td class="smalltext2" valign="top">
															<?php echo showDate($cr_rateGivenOn);?>
														</td>
														<td>&nbsp;</td>
														<td class="smalltext1" valign="top">
															<?php echo $cr_message;?>
														</td>
													</tr>
													<tr>
														<td colspan="8">
															<hr size="1" width="100%" color="#bebebe">
														</td>
													</tr>
												<?php
													}
												}
												else
												{
													echo "<tr><td colspan='3' class='smalltext2' align='center'>No Ratings Available</td></tr>";
												}
											?>
						</table>
					</div>
				</td>
			</tr>
		</table>
</fieldset>

<br>
<!-- **********************EMPLOYEE PREVIOUS ORDER MESSAGES AND RATINGS*********************
<script type="text/javascript">
	function showHideEmployeeToCustomerMessagesRatings(flag)
	{
		if(flag)
		{
			document.getElementById('showHideEmployeeMessageRatings').style.display 	   = 'inline';
			document.getElementById('showAndHideEmployeeQARatings').innerHTML   = "<a href='javascript:showHideEmployeeToCustomerMessagesRatings(0)'><img src='<?php echo SITE_URL;?>/images/hide.jpg' border='0' title='Hide'></a>";
		}
		else
		{
			document.getElementById('showHideEmployeeMessageRatings').style.display 	= 'none';
			document.getElementById('showAndHideEmployeeQARatings').innerHTML= "<a href='javascript:showHideEmployeeToCustomerMessagesRatings(1)'><img src='<?php echo SITE_URL;?>/images/show.jpg' border='0' title='Hide'></a>";
		}
	}
</script>
<br>
		<fieldset style="border:1px solid #333333">
			<legend class="heading3"><b>EMPLOYEE MESSAGES & RATINGS</b></legend>
				<table width="100%" align="left" border="0" cellpadding="2" cellspacing="2">
					<tr>
						<td colspan="3" align="left">
							<div id="showAndHideEmployeeQARatings">
								<a href="javascript:showHideEmployeeToCustomerMessagesRatings(0)"><img src="<?php echo SITE_URL;?>/images/hide.jpg" border='0' title='Hide'></a>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="3" valign="top">
							<div id="showHideEmployeeMessageRatings">
								<table width="100%" cellpadding="3" cellspacing="3" border="0">
									<tr>
									   <td class="heading5">PREVIOUS EMPLOYEE MESSAGES FOR ORDERS - <?php echo $customerName;?></td>
									</tr>
									<?php
										if($result		=	$orderObj->previousOrdersEmployeesMessages($orderId,$customerId,10))
										{
										?>
											<tr>
												<td width="4%">&nbsp;</td>
												<td width="25%" class="textstyle"><b>Order No</b></td>
												<td width="1%">&nbsp;</td>
												<td class="textstyle" width="50%"><b>Message</b></td>
												<td width="1%">&nbsp;</td>
												<td class="textstyle"><b>By</b></td>
											</tr>
											<tr>
												<td colspan="6">
													<hr size="1" width="100%" color="#bebebe">
												</td>
											</tr>
														
											<?php
												$emessageCount			=	0;
												while($emessageRow		=	mysql_fetch_assoc($result))
												{
													$emessageCount++;
													$e_messageId		=  $emessageRow['messageId'];
													$e_orderId			=  $emessageRow['messageFor'];
													$e_message			=  stripslashes($emessageRow['message']);
													$e_message			=  nl2br($e_message);
													$e_address			=  stripslashes($emessageRow['orderAddress']);
													$e_messageBy		=  $emessageRow['messageBy'];

													$orderAddedOn			=  $emessageRow['addedOn'];
						
													$e_messageByName	=	$employeeObj->getEmployeeFirstName($e_messageBy);

													$orderAddedOn	= (strtotime(date("Y-m-d")) - strtotime($orderAddedOn)) / (60 * 60 * 24);
													$orderAddedOnImg		=	"";
													if($instructionDifferent <= 3)
													{
														$orderAddedOnImg	=	"<img src='".SITE_URL."/images/blinking-new.gif' width='30' height='15'>";
													}
											?>
											<tr>
												<td class="textstyle" valign="top"><?php echo $emessageCount;?>)</td>
												<td class="textstyle" valign="top">
													<a href="<?php echo SITE_URL_EMPLOYEES;?>/internal-emp-msg.php?orderId=<?php echo $e_orderId;?>&customerId=<?php echo $customerId;?>#messages" class='link_style12'><?php echo $e_address;?></a><br><?php echo $orderAddedOnImg;?>
												</td>
												<td>&nbsp;</td>
												<td class="textstyle" valign="top" width="40%"><?php echo $e_message;?></td>
												<td>&nbsp;</td>
												<td class="smalltext1" valign="top">
													<?php
														echo $e_messageByName;
													?>
												</td>
											</tr>
											<tr>
												<td colspan="6">
													<hr size="1" width="100%" color="#bebebe">
												</td>
											</tr>
										<?php
											}
										}
										else
										{
											echo "<tr><td colspan='6' class='smalltext2' align='center'>No Message Available</td></tr>";
										}
									?>
									</table>
									<table width="100%" cellpadding="3" cellspacing="3" border="0">
										<tr>
										   <td class="heading5" colspan="6">ACCEPTED EMPLOYEES PREVIOUS ORDER RATINGS FROM QA</td>
										 </tr>
										<?php
											if($result	=	$orderObj->previousOrdersQaCommentsWithMessage($orderId,$customerId,$acceptedBy,5))
											{
										?>
											<tr>
												<td width="4%">&nbsp;</td>
												<td width="25%" class="textstyle"><b>Order No</b></td>
												<td width="1%">&nbsp;</td>
												<td class="textstyle"><b>Message</b></td>
											</tr>
											<tr>
												<td colspan="6">
													<hr size="1" width="100%" color="#bebebe">
												</td>
											</tr>
													
											<?php
												$ratingCount			=	0;
												while($ratingRow		=	mysql_fetch_assoc($result))
												{
													$ratingCount++;
													$r_orderId			=  $ratingRow['orderId'];
													$r_memberId			=  $ratingRow['memberId'];
													$r_rateGiven		=  $ratingRow['rateByQa'];
													$r_message			=  stripslashes($ratingRow['qaRateMessage']);
													$r_message			=  nl2br($r_message);

													$r_orderName		=	@mysql_result(dbQuery("SELECT orderAddress FROM members_orders WHERE orderId=$r_orderId"),0);

													$r_orderName		=	stripslashes($r_orderName);
											?>
											<tr>
												<td class="textstyle" valign="top"><?php echo $ratingCount;?>)</td>
												<td class="textstyle" valign="top">
													<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $r_orderId;?>&customerId=<?php echo $r_memberId;?>" class='link_style12'><?php echo $r_orderName;?></a>
												</td>
												<td>&nbsp;</td>
												 <td class="smalltext1" valign="top">
													<?php echo $r_message;?>
												</td>
											</tr>
											<tr>
												<td colspan="6">
													<hr size="1" width="100%" color="#bebebe">
												</td>
											</tr>
										<?php
											}
										
															
										}
										else
										{
											echo "<tr><td colspan='3' class='smalltext2' align='center'>No Ratings Available</td></tr>";
										}
									?>
								</table>
							</div>
						</td>
					</tr>
				</table>
		</fieldset>
<br>-->
