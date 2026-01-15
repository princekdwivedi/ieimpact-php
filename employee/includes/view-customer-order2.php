<?php
	$pageUrl	        =	$_SERVER['SCRIPT_NAME'];
	if(!strstr($_SERVER['HTTP_HOST'],'ieimpact.com'))
	{
		$pageUrl	    =	str_replace("/ieimpact/","/",$pageUrl);
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

	//$a_clickedOrdersAllTabs				=	$orderObj->getEmployeesClickedTabs($orderId,$s_employeeId);

	$instructions	=	@mysql_result(dbQuery("SELECT instructions from members_orders where orderId=187118"),0);

	echo "<br />MMMMMMM-".makeLinkFromText($instructions);

	
?>
<script src="<?php  echo SITE_URL;?>/script/common-ajax.js" type="text/javascript"></script>
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
	var myClickedTabs			=	new Array(); 
	myClickedTabs[1]			=	1;
	function selectUnselectTabs(flag)
	{
		//alert(flag);
		//i++;
				
		for(i=1;i<9;i++)
		{
			var classColor			=	"#FF4F4F";
			var showHideTab			=	"none";

			if( i in myClickedTabs ) {
				classColor			=	"#6B6B6B";
			}

			if(flag					==	i)
			{
				classColor			=	"#0080C0";
				showHideTab			=	"inline";
				if(flag != 1)
				{
					myClickedTabs[i]=	flag;
				}
				
			}
			
			document.getElementById('tabId'+i).style.background				  = classColor;
			document.getElementById('showingOrderDetailsFor'+i).style.display = showHideTab;
		}
				
	}

	function markedPostAuditErrorFiles(orderId,customerId)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/post-audit-errors.php?orderId="+orderId+"&customerId="+customerId;
		prop = "toolbar=no,scrollbars=yes,width=800,height=700,top=100,left=100";
		window.open(path,'',prop);
	}

	function replyAllMessageForcefully(messageId,memberId,type)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/marked-forcefully-replied.php?messageId="+messageId+"&memberId="+memberId+"&type="+type;
		prop = "toolbar=no,scrollbars=yes,width=800,height=600,top=100,left=100";
		window.open(path,'',prop);
	}
</script>
<?php
	$instructions	=	@mysql_result(dbQuery("SELECT instructions from members_orders where orderId=187118"),0);

	echo "<br />PPPPPPPP-".makeLinkFromText($instructions);
	$M_D_5_ORDERID						=	ORDERID_M_D_5;
	$M_D_5_ID							=	ID_M_D_5;
	$a_customerOrderTemplateFiles		=	array();
	$a_customerOrderTemplateFilesSize	=	array();
	
	$a_existingRatings					=	$orderObj->getFeedbackText();
	$instructionDaysDifferent			=	"0";
	if($result							=	$orderObj->getOrderDetails($orderId,$customerId))
	{
		$row							=	mysql_fetch_assoc($result);
		$encodeOrderID					=	base64_encode($orderId);
		$customerId						=	$row['memberId'];
		$orderAddress					=	stripslashes($row['orderAddress']);
		$orderType						=	$row['orderType'];
		$customersOwnOrderText			=	stripslashes($row['customersOwnOrderText']);
		$state							=	$row['state'];
		$instructions					=	trim(stripslashes($row['instructions']));
	
		$hasOrderFile					=	$row['hasOrderFile'];
		$orderFileExt					=	$row['orderFileExt'];
		$hasPublicRecordFile			=	$row['hasPublicRecordFile'];
		$publicRecordFileExt			=	$row['publicRecordFileExt'];
		$hasMlsFile						=	$row['hasMlsFile'];
		$mlsFileExt						=	$row['mlsFileExt'];
		$hasMarketConditionFile			=	$row['hasMarketConditionFile'];
		$marketConditionExt				=	$row['marketConditionExt'];
		$orderPlacedDate				=	$row['orderAddedOn'];
		
		$orderPlacedCustomerDate		=	$row['estDate'];
		$orderAddedCustomerTime			=	$row['estTime'];

		$orderAddedOn					=	showDate($orderPlacedDate);
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
		$orderCompletedOn				=	$row['orderCompletedOn'];
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
		$isOrderChecked					=	$row['isOrderChecked'];
		$isEmailOrder					=	$row['isEmailOrder'];
		$isNotVerfidedEmailOrder		=	$row['isNotVerfidedEmailOrder'];
		$isAddedTatTiming				=	$row['isAddedTatTiming'];
		$isCompletedOnTime				=	$row['isCompletedOnTime'];
		$orderCompletedTat				=	$row['orderCompletedTat'];
		$beforeAfterTimingMin			=	$row['beforeAfterTimingMin'];

		$isAuthorizedEmailOrder			=	$row['isAuthorizedEmailOrder'];
		$isPrepaidOrder					=	$row['isPrepaidOrder'];
		$prepaidTransactionId			=	$row['prepaidTransactionId'];
		$customerProfileId				=	$row['customerProfileId'];
		$customerShippingAddressId		=	$row['customerShippingAddressId'];
		$customerPaymentProfileId		=	$row['customerPaymentProfileId'];
		$advancedPaymentId				=	$row['advancedPaymentId'];
		$prepaidOrderPrice				=	$row['prepaidOrderPrice'];
		$prepiadPaymentThrough			=	$row['prepiadPaymentThrough'];
		$isChangedPrice					=	$row['isChangedPrice'];
		$ignorePrepaidCapture			=	$row['ignorePrepaidCapture'];
		$isVocalCustomer				=	$row['isVocalCustomer'];
		$isPaidThroughWallet			=	$row['isPaidThroughWallet'];
		$walletAccountId				=	$row['walletAccountId'];

		$vocalText						=	"";
		if($isVocalCustomer				==	"yes"){
			$vocalText					=	"(<font color='#ff0000'>V****</font>)";
		}

		$emailOrderVerifiedByText		=	"";
		if($isEmailOrder ==	1 && $isNotVerfidedEmailOrder == 0)
		{
			$emailOrderVerifiedby		=	$row['emailOrderVerifiedby'];
			if(!empty($emailOrderVerifiedby))
			{
				$emailOrderVerifiedOn	=	$row['emailOrderVerifiedOn'];
				$emailOrderVerifiedTime	=	$row['emailOrderVerifiedTime'];

				if($isSetedOrderField	==	0)
				{
					$verifiedOrderByName=	$employeeObj->getEmployeeName($emailOrderVerifiedby);
				}
				else
				{
					$verifiedOrderByName=  $orderCheckedBy;
				}

				$emailOrderVerifiedByText	=	"&nbsp;[Email Order verified by : ".$verifiedOrderByName." at ".showDateTimeFormat($emailOrderVerifiedOn,$emailOrderVerifiedTime)."]";
			}
		}

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

		if(!empty($s_hasManagerAccess))
		{
			$postAuditText		=	"&nbsp;&nbsp;(<a onclick='markedPostAuditErrorFiles($orderId,$customerId)' style='cursor:pointer;' class='link_style32'><font color='#ff0000'>Do Audit</font></a>)";
			if($isDonePostAudit	==	1)
			{
				$postAuditText	=	"&nbsp;&nbsp;(<a onclick='markedPostAuditErrorFiles($orderId,$customerId)' style='cursor:pointer;' class='link_style32'><b>View Audit</b></a>)";
			}
		}
		else
		{
			$postAuditText		=	"";
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
		$isSetedOrderField				=	$row['isSetedOrderField'];
		$isHavingInternalMsg			=	$row['isHavingInternalMsg'];
		$completedTime					=	$row['completedTime'];
		$qaDoneByName					=	stripslashes($row['qaDoneByName']);
		$qaDoneBy						=	$row['qaDoneById'];
		$acceeptedByName				=	stripslashes($row['acceeptedByName']);
		$hasRepliedUploaded				=	$row['hasRepliedUploaded'];
		$isOrderNeedAttention			=	$row['isOrderNeedAttention'];
		$orderCheckedBy					=	stripslashes($row['orderCheckedBy']);
		$isCustomerViewedTheOrder		=	$row['isViewedDownloadOrder'];
		$customerViewedOrderDate		=	$row['viewedEstDate'];
		$customerViewedOrderTime		=	$row['viewedEstTime'];



		$newAttentionUnmarkTxt				=	"";
		if($status							==	0)
		{
			if($isSetedOrderField			==	0)
			{
				if($isUnmarkedNeedAttention	=	$orderObj->isOrderWasInNeedAttention($orderId))
				{
					$newAttentionUnmarkTxt	=	"<font color='#ff0000'>(Need Atten. Order)</font>";
				}
			}
			else
			{
				if($isOrderNeedAttention	==	1)
				{
					$newAttentionUnmarkTxt	=	"<font color='#ff0000'>(Need Atten. Order)</font>";
				}
			}
		}

		if($isRushOrder			==	1)
		{
		   $isRushOrderText		=	"<font color='#ff0000'>6 Hours</font>";
		}
		elseif($isRushOrder		==	2)
		{
		   $isRushOrderText		=	"<font color='#ff0000'>24 Hours</font>";
		}
		else
		{
			$isRushOrderText	=	"<font color='#ff0000'>12 Hours</font>";
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

		//$employeeDisplayOrderID	 =	$employeeObj->getAAOrderID($orderId);

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
			if($isSetedOrderField	==	0)
			{
				$acceptedByName		=   $employeeObj->getEmployeeName($acceptedBy);
			}
			else
			{
				$acceptedByName		=	$acceeptedByName;
			}

			if(!empty($s_hasManagerAccess) || $s_employeeId == $acceptedBy)
			{
				$acceptedByName	=	"<a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&orderOf=$acceptedBy&showingEmployeeOrder=1' class='link_style32'>".$acceptedByName."</a>";
			}
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
			if($isSetedOrderField	==	0)
			{
				$qaDoneBy			=	@mysql_result(dbQuery("SELECT qaDoneBy FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId AND hasQaDone=1"),0);
				if(!empty($qaDoneBy))
				{
					$qaDoneByText	=	$employeeObj->getEmployeeName($qaDoneBy);

					$qaDoneByText	=	"<a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&orderOf=$qaDoneBy&showingEmployeeOrder=1&displayTypeCompleted=1' class='link_style32'>".$qaDoneByText."</a>";
				}
				else
				{
					$qaDoneByText	=	"";
				}
			}
			else
			{
				$qaDoneByText	=	"<a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&orderOf=$qaDoneBy&showingEmployeeOrder=1&displayTypeCompleted=1' class='link_style32'>".$qaDoneByName."</a>";
			}
		}

		if($status != 0 && $status != 3)
		{
			$acceptedText		=	$acceptedByName.",On - ".showDateTimeFormat($orderAcceptedDateByEmployee,$orderAcceptedTimeByEmployee);
			$statusText			=	"Accepted by - ".$acceptedText;
		}

		$hasReplied				=	@mysql_result(dbQuery("SELECT hasRepliedFileUploaded FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId AND hasRepliedFileUploaded=1"),0);
		if(!empty($hasReplied))
		{
			$statusText			=	"QA Pending";
		}

		if($status				== 2)
		{
			$statusText			=	"Completed";
		}
		if($status				== 3)
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


		$customerOrderText		=	"";
		$customerLinkStyle		=	"link_style29";
		$totalCustomerOrders	=	$cutomerTotalOrdersPlaced;
		if(empty($totalCustomerOrders))
		{
			$totalCustomerOrders=	0;
		}
		if($totalCustomerOrders <= 3)
		{
			$customerOrderText	=	"(New Customer)";
			$customerLinkStyle	=	"link_style30";
		}
		if($totalCustomerOrders > 3 && $totalCustomerOrders <= 7)
		{
			$customerOrderText	=	"(Trial Customer)";
			$customerLinkStyle	=	"link_style31";
		}

		$checkedReoForm		=	"";
		$checkFinancingVa	=	"";
		$checkFinancingFha	=	"";
		$checkFinancingHud	=	"";
		$checkNonUad		=	"";

		
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

			if($reoForm				==	1)
			{
				$checkedReoForm		=	"checked";
			}
			if($financingVa			==	1)
			{
				$checkFinancingVa	=	"checked";
			}
			if($financingFha		==	1)
			{
				$checkFinancingFha	=	"checked";
			}
			if($financingHud		==	1)
			{
				$checkFinancingHud	=	"checked";
			}
			if($nonUad				==	1)
			{
				$checkNonUad		=	"checked";
			}
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

		if(isset($_GET['messageId']) && isset($_GET['replyEmailMsg']) && $_GET['replyEmailMsg'] == 1)
		{
			$messageId				=	$_GET['messageId'];
			if(!empty($messageId))
			{
				dbQuery("UPDATE members_employee_messages SET isRepliedToEmail=1,messageRepliedMarkedBy=$s_employeeId,repliedOn='".CURRENT_DATE_INDIA."',repliedTime='".CURRENT_TIME_INDIA."',repliedFromIP='".VISITOR_IP_ADDRESS."' WHERE orderId=$orderId AND memberId=$customerId AND messageId=$messageId AND isRepliedToEmail=0 AND messageRepliedMarkedBy=0");
			}
			
			ob_clean();
			header("Location: ".SITE_URL."/".$pageUrl."?orderId=$orderId&customerId=$customerId&selectedTab=5");
			exit();
		}
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
		exit();
	}

	
	$instructions	=	@mysql_result(dbQuery("SELECT instructions from members_orders where orderId=187118"),0);

	echo "<br />MMMMMMMMMM-".makeLinkFromText($instructions);
	
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
function markMessageReplied(page,orderId,customerId,messageId)
{
	var confirmation = window.confirm("Are You Sure To Marked This Email As replied By You?");
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL;?>"+page+"?orderId="+orderId+"&customerId="+customerId+"&messageId="+messageId+"&replyEmailMsg=1";
	}
}
function downloadGeneralMessageFile(url)
{
	//window.open(url, "_blank");
	 location.href   = url;
}
</script>
<?php
	$instructions	=	@mysql_result(dbQuery("SELECT instructions from members_orders where orderId=187118"),0);

	echo "<br />YYYYYYYYYY-".makeLinkFromText($instructions);
?>
<table width='98%' align='center' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<td align="left">
			<?php
				include(SITE_ROOT_EMPLOYEES . "/includes/next-previous-order.php");
			?>
		</td>
	</tr>
	<tr>
		<td class="textstyle1">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=<?php echo $customerId;?>" class="link_style10"><?php echo ucwords($customerName);?></a> / <b><?php echo $orderAddress;?></b>
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
	<tr>
		<td height="5"></td>
	</tr>
</table>
<?php
	$instructions	=	@mysql_result(dbQuery("SELECT instructions from members_orders where orderId=187118"),0);

	echo "<br />XXXXXXXXXXXXXXXXXX-".makeLinkFromText($instructions);
	
	$a_showingTabs						=	array("1"=>"ORDER INFO","5"=>"ORDER MESSAGES","3"=>"CUSTOMERS INSTRUCTIONS","4"=>"EMPLOYEE NOTES AND NOTE FILES","2"=>"COMPLETED FILES","6"=>"PREVIOUS ORDER & GENERAL MESSAGES","7"=>"RATINGS IN PREVIOUS ORDERS","8"=>"EMPLOYEES INTERNAL MESSAGES");

	$displayHideMainTabs1				=	"";
	$displayHideMainTabs2				=	"none";
	$displayHideMainTabs3				=	"none";
	$displayHideMainTabs4				=	"none";
	$displayHideMainTabs5				=	"none";
	$displayHideMainTabs6				=	"none";
	$displayHideMainTabs7				=	"none";
	$displayHideMainTabs8				=	"none";


	$selectedTab						=	0;
	if(isset($_GET['selectedTab']))
	{
		$selectedTab					=	(int)$_GET['selectedTab'];
		if(!empty($selectedTab) && !array_key_exists($selectedTab,$a_showingTabs))
		{
			$selectedTab				=	1;
		}
	}
	if(empty($selectedTab))
	{
		$selectedTab					=	1;
	}

		
?>
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<?php
			foreach($a_showingTabs as $kTab=>$kTabName)
			{
				
				$bgColor			=	"#FF4F4F";

				$urlTabChange		=	SITE_URL_EMPLOYEES."/update-employee-checked-tabs.php?orderId=$orderId&tabId=";

				//$onClickFun			 = "onclick=\"commonFunc('$urlTabChange','changeTabDiv$kTab','$kTab'),selectUnselectTabs('$kTab')\"";

				$onClickFun			 = "onclick=\"selectUnselectTabs('$kTab')\"";

				/*if(in_array($kTab,$a_clickedOrdersAllTabs))
				{
					$bgColor		 =	"#6B6B6B";

					$onClickFun		 = "onclick=\"selectUnselectTabs('$kTab')\"";

				}*/
								
				if($kTab			        ==  $selectedTab)
				{
					$bgColor				=	"#0080C0";
					if($selectedTab		   != 1)
					{
						$temp				 =  "displayHideMainTabs".$kTab;
						$dispalyTab			 =	@$$temp = "";
						$displayHideMainTabs1=	"none";
					}					
				}				

		?>
		<td width="11%" align="center" valign="middle" bgcolor="<?php echo $bgColor;?>" style="border:2px solid #bebebe;text-align:center;" id="tabId<?php echo $kTab;?>">
			<a <?php echo $onClickFun;?> class="link_button_select" style="cursor:pointer;" id="tabClassId<?php echo $kTab?>"><?php echo $kTabName;?></a>
		</td>
		<td width="1">&nbsp;</td><div id="changeTabDiv<?php echo $kTab;?>"></div>
		<?php
			}
		?>
	</tr>
</table>
<?php
	$instructions	=	@mysql_result(dbQuery("SELECT instructions from members_orders where orderId=187118"),0);

	echo "<br />AAAAAAAAA-".makeLinkFromText($instructions);
	
?>
<table width="99%" align="center" border="1" cellpadding="0" cellspacing="0" style="border:1px solid #333333;">
	<tr>
		<td valign="top">
			<!--*******************************************************************************
			/////////////////////////////////// DISPLAYING ORDER DEATILS //////////////////////
			***********************************************************************************-->
			<div id="showingOrderDetailsFor1" style="display:<?php echo $displayHideMainTabs1;?>">
				<?php
					if($isEmailOrder	==	1 && $isNotVerfidedEmailOrder	==	1)
					{
						include(SITE_ROOT_EMPLOYEES."/includes/verify-customer-email-orders.php");
					}
					else
					{
				?>
				<table width="98%" align="center" border="0" cellpadding="3" cellspacing="2">
					<tr>
						<td colspan="3" class="smalltext24">
							<b><font color='#ff0000;'>CUSTOMER ORDER INFO</font></b>
						</td>
					</tr>
					<tr>
						<td class="smalltext23">Customer</td>
						<td class="smalltext23">:</td>
						<td class="smalltext2">
							<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=<?php echo $customerId;?>" class="<?php echo $customerLinkStyle;?>"><?php echo ucwords($customerName).$vocalText;?></a>&nbsp;
						</td>
					</tr>
					<tr>
						<td class="smalltext23" width="20%">Subject</td>
						<td class="smalltext23" width="1%">:</td>
						<td>
							<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $customerId;?>" class="link_style28"><?php echo $orderAddress;?></a>&nbsp;(<font color='#ff0000'><b><?php echo $state;?></b></font>)<?php echo $emailOrderVerifiedByText;?>
						</td>
					</tr>
					<tr>
						<td class="smalltext23">Order Status</td>
						<td class="smalltext23">:</td>
						<td class="smalltext24">
							<b><?php echo $statusText.$newAttentionUnmarkTxt;?></b>
						</td>
					</tr>
					<tr>
						<td class="smalltext23">Order Date</td>
						<td class="smalltext23">:</td>
						<td class="smalltext24">
							<?php echo $orderAddedOn." ".$displayOrderTimeFormat." ".$displayZoneTime;?>
						</td>
					</tr>
					<tr>
						<td class="smalltext23">ETA</td>
						<td class="smalltext23">:</td>
						<td class="smalltext24">
							<?php echo $isRushOrderText;?>
						</td>
					</tr>
					<tr>
						<td class="smalltext23">Order Type</td>
						<td class="smalltext23">:</td>
						<td class="smalltext24">
							<?php echo $orderText;?>
						</td>
					</tr>
					<tr>
						<td class="smalltext23">File Type</td>
						<td class="smalltext23">:</td>
						<td class="error">
							<b><?php echo $appraisalText;?></b>
						</td>
					</tr>
					<?php
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
						<td class="smalltext23">REO Form</td>
						<td class="smalltext23">:</td>
						<td class="smalltext24">
							<?php echo $reoFromText;?>
						</td>
					</tr>
					<tr>
						<td class="smalltext23">Financing</td>
						<td class="smalltext23">:</td>
						<td class="smalltext24">
							 VA : <?php echo $financingVaText;?> &nbsp;
							 FHA : <?php echo $financingFhaText;?> &nbsp;
							 HUD : <?php echo $financingHudText;?> &nbsp;
						</td>
					</tr>
					<tr>
						<td class="smalltext23">NON-UAD</td>
						<td class="smalltext23">:</td>
						<td class="smalltext24">
							<?php echo $nonUadText;?>
						</td>
					</tr>
					<?php
						}
						if(!empty($isDeleted))
						{
					?>
							<tr>
								<td colspan="3" class="smalltext24">
									<b><font color='#ff0000;'>ORDER FILES ARE DELETED</font></b>
								</td>
							</tr>
					<?php
						}
						else
						{
							
						?>
							<tr>
								<td colspan="3">
									<table width="100%" border="0" cellpadding="0" cellspacing="0">
										<?php

											if($isNewUploadingSystem	==	1)
											{
												include(SITE_ROOT_EMPLOYEES."/includes/display-multiple-files.php");
											}
											else
											{
												include(SITE_ROOT_EMPLOYEES."/includes/display-single-files.php");
												
											}
										?>
									</table>
								 </td>
							</tr>
						<?php
							 }
						?>
						<tr>
							<td class="smalltext23" valign="top">Make Sketch <font class="smalltext2">(Draft Sketch Provided)</font></td>
							<td class="smalltext23" valign="top">:</td>
							<td valign="top" class="smalltext24">
								<?php 
									echo $hasMarkedSketchYes;

									if($providedSketch		==	1)
									{
										if(empty($sketchStatus))
										{
									?>
											<a onclick="accepetDoneSketchFile('<?php echo $pageUrl;?>',<?php echo $orderId;?>,<?php echo $customerId;?>,1);" style="cursor:pointer;" title="Accept Sketch" class="link_style12"><b>Accept This Sketch</b></a>
									<?php
										}
										else
										{
											$sketchAcceptByEmp	=	$employeeObj->getEmployeeName($sketchAcceptBy);
											echo "&nbsp;&nbsp;<font class='smalltext23'>Sketch Accepted By : </font><font class='smalltext24'>".$sketchAcceptByEmp."</font>";

											if($sketchStatus	== 1)
											{
												if($sketchAcceptBy == $s_employeeId || !empty($s_hasManagerAccess)) 
												{
										?>
													<a onclick="accepetDoneSketchFile('<?php echo $pageUrl;?>',<?php echo $orderId;?>,<?php echo $customerId;?>,2);" style="cursor:pointer;" title="Accept Sketch" class="link_style32"><b>Done This Sketch</b></a>
										<?php
												}
											}
											elseif($sketchStatus	== 2)
											{
												$sketchDonetByEmp	=	$employeeObj->getEmployeeName($sketchDoneBy);

												echo "&nbsp;&nbsp;<font class='smalltext23'>Sketch Done By : </font><font class='smalltext24'>".$sketchDonetByEmp."</font>";
											}
										}
									}
								?>
							</td>
						</tr>
						<tr>
							<td class="smalltext23" valign="top">Customer Instructions</td>
							<td class="smalltext23" valign="top">:</td>
							<td valign="top" class="error">
								<div style='overflow:auto;width:800px;scrollbars:no'>
									<table width="100%">
										<tr>
											<td class="error">
												<?php echo makeLinkFromText($instructions);?>
											</td>
										</tr>
									</table>
								</div>
							</td>
						</tr>
						<tr>
							<td class="smalltext23" valign="top">Accepted By</td>
							<td class="smalltext23" valign="top">:</td>
							<td valign="top" class="smalltext24">
								<?php echo $acceptedText;?>
							</td>
						</tr>
						<?php
							if($hasReplied		== 1 && $status == 1)
							{
								$query			=    "SELECT qaAcceptedBy,qaAcceptedDate,qaAcceptedTime FROM members_orders_reply WHERE isQaAccepted=1 AND qaAcceptedBy <> 0 AND orderId=$orderId";
								$result			=    dbQuery($query);
								if(mysql_num_rows($result))
								{
									$row			=    mysql_fetch_assoc($result);
									$qaAcceptedBy	=	$row['qaAcceptedBy'];
									$qaAcceptedDate =	$row['qaAcceptedDate'];
									$qaAcceptedTime	=	$row['qaAcceptedTime'];

									$qaAcceptedByName=	$employeeObj->getEmployeeName($qaAcceptedBy);

									$qaAcceptedByName=	"<a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&orderOf=$qaAcceptedBy&showingEmployeeOrder=1&displayTypeCompleted=1' class='link_style32'>".$qaAcceptedByName."</a> On - ".showDateTimeFormat($qaAcceptedDate,$qaAcceptedTime);
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
						?>
						<tr>
							<td colspan="3">
								<!-- View orders checked by and comments -->
								<?php
									include(SITE_ROOT_EMPLOYEES	. "/includes/check-order-checklist.php");
								?>
							</td>
						</tr>
					</table>
				<?php
					}			
				?>
			</div>
			<!--********************************************************************************
			//////////////////////////////// ENDING OF DISPLAYING ORDER DEATILS ////////////////
			************************************************************************************
			************************************************************************************
			///////////////////////////DISPLAYING COMPLETED ORDER DEATILS //////////////////////
			************************************************************************************-->
			<div id="showingOrderDetailsFor2" style="display:<?php echo $displayHideMainTabs2;?>">
				<?php
					if($status	!=	0)
					{
						include(SITE_ROOT_EMPLOYEES	. "/includes/view-reply-details1.php");
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
			</div>
			<!--********************************************************************************
			/////////////////////////// ENDING OF DISPLAYING COMPLETED ORDER DEATILS ///////////
			************************************************************************************
			************************************************************************************
			************************************************************************************
			///////////////DISPLAYING CUSTOMERS SPECIAL INSTRUCTIONS ORDER DEATILS /////////////
			************************************************************************************-->
			<div id="showingOrderDetailsFor3" style="display:<?php echo $displayHideMainTabs3;?>">
				<table width="98%" align="center" border="0" cellpadding="3" cellspacing="2">
					<?php
						if(!empty($splInstructionToEmployee))
						{
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
						<tr>
							<td colspan="3" class="smalltext24">
								<b><font color='#ff0000;'>CUSTOMERS STANDARD INSTRUCTIONS </font></b>&nbsp;<?php echo $newDiffImage;?>
							</td>
						</tr>
						<tr>
							<td colspan="3" class="textstyle">
								<p align="justify" style="margin-top:0px;line-height:16px;">
									<?php 
										echo nl2br($splInstructionToEmployee);

										
									?>
								</p>
							</td>
						</tr>
						<?php
							$query	=	"SELECT * FROM customer_instructions_file WHERE memberId=$customerId AND uploadedBy='".CUSTOMERS."' ORDER BY instructionId  DESC";
							$result	=	dbQuery($query);
							if(mysql_num_rows($result))
							{
						?>
						<tr>
							<td colspan="3">
								<table width="100%" cellpadding="2" cellspacing="2" border="0">
									<tr>
										<td colspan="4" class="heading3"><b>CUSTOMER INSTRUCTIONS FILES</b></td>
									</tr>
									<?php
										$i	=	0;
										$i1	=	0;
										while($row	=	mysql_fetch_assoc($result))
										{
											$i++;
											$i1++;

											$instructionId	=	$row['instructionId'];
											$fileName		=	$row['fileName'];
											$fileExt		=	$row['fileExt'];
											$size			=	$row['size'];
											$fileAddedOn	=	$row['addedOn'];
											$addedTime		=	$row['addedTime'];
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
										<td width="5%" align="right"  valign="top"><?php echo $i.")".$newInstructionText;?></td>
										<td valign="top">
											<a href="<?php echo SITE_URL_EMPLOYEES;?>/download-instructions.php?ID=<?php echo $instructionId."&memberId=".$customerId;?>" class='link_style32'><?php echo $fileName.".".$fileExt;?></a>&nbsp;<font class='smalltext20'><?php echo getFileSize($size);?>&nbsp;Added On - <?php echo showDateTimeFormat($fileAddedOn,$addedTime);?></font>
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
						}
						else
						{
							echo "<tr><td height='250' style='text-align:center'><font style='font-size:16px;font-family:verdana;color:#ff0000;font-weight:bold'>No Instructions Available</font></td></tr>";
						}
					?>
				</table>
			</div>
			<!--********************************************************************************
			///////////////// ENDING OF DISPLAYING CUSTOMERS SPECIAL INSTRUCTIONS //////////////
			************************************************************************************
			************************************************************************************
			************************************************************************************
			////////////////////////DISPLAYING EMPLOYEE SPECAIL NOTE & FILES ///////////////////
			************************************************************************************-->
			<div id="showingOrderDetailsFor4" style="display:<?php echo $displayHideMainTabs4;?>">
				<table width="98%" align="center" border="0" cellpadding="3" cellspacing="2">
					<tr>
						<td colspan="3" class="smalltext24">
							<b><font color='#ff0000;'>EMPLOYEE NOTES AND NOTE FILES FOR THE CUSTOMER</font></b>&nbsp;<a onclick="openNoteWindow(<?php echo $customerId;?>);" class="link_style32" style="cursor:pointer"><b>EDIT</b></a>
						</td>
					</tr>
					<?php
						if(!empty($splInstructionOfCustomer))
						{
					?>
					<tr>
						<td class="textstyle" colspan='4'>
							<p align="justify" style="margin-top:0px;line-height:16px;">
								<?php echo nl2br($splInstructionOfCustomer);?>
							</p>
						</td>
					</tr>
					<?php
						}
						else
						{
							echo "<tr><td  class='error' style='text-align:center' colspan='4'><b>No Note Available</td></td></tr>";
						}

						$query			=	"SELECT * FROM customer_instructions_file WHERE memberId=$customerId AND uploadedBy='".EMPLOYEES."' ORDER BY fileName";
						$result			=	dbQuery($query);
						if(mysql_num_rows($result))
						{
					?>
					<tr>
						<td colspan="4" class="text"><b>EMPLOYEE NOTE FILES</b></td>
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
							<td class='smalltext2' width='2%'><?php echo $i;?>)</td>
							<td valign="top"><?php echo "<a href='".SITE_URL_EMPLOYEES."/download-instructions.php?ID=$instructionId&memberId=$customerId'  class='link_style32'>".$fileName.".".$fileExt."</a>&nbsp;<font class='smalltext20'>".getFileSize($size)."</font>"?>&nbsp;&nbsp;
								<a onclick="deleteNoteInstructionsFile(<?php echo $instructionId?>,'<?php echo $pageUrl;?>',<?php echo $customerId;?>,<?php echo $orderId?>)" style="cursor:pointer;">
									<img src="<?php echo SITE_URL;?>/images/c_delete.gif" border="0">
								</a>
							</td>
						</tr>
						<?php
							}
						}	
					?>
				</table>
			</div>
			<!--********************************************************************************
			/////////// ENDING OF DISPLAYING DISPLAYING EMPLOYEE SPECAIL NOTE & FILES //////////
			************************************************************************************
			************************************************************************************
			************************************************************************************
			////////////////////DISPLAYING EMPLOYEE CUSTOMER ORDER MESSAGES ////////////////////
			************************************************************************************-->
			<div id="showingOrderDetailsFor5" style="display:<?php echo $displayHideMainTabs5;?>">
				<table width="98%" align="center" border="0" cellpadding="3" cellspacing="2">
					<tr>
						<td colspan="3" class="smalltext24">
							<b><font color='#ff0000;'>THIS ORDER MESSAGES </font></b>&nbsp;
						</td>
					</tr>
					<?php
						$instructions	=	@mysql_result(dbQuery("SELECT instructions from members_orders where orderId=187118"),0);

				echo "<br />BBBBBBBBBBBB-".makeLinkFromText($instructions);
			
						if($result	=	$orderObj->getOrderMessages($orderId,$customerId))
						{
							while($row					=	mysql_fetch_assoc($result))
							{
								$t_messageId			=	$row['messageId'];
								$t_message				=	stripslashes($row['message']);
								$t_message				=	trim($t_message);
								$addedOn				=	$row['addedOn'];
								$addedTime				=	$row['addedTime'];
								$messageBy				=	$row['messageBy'];
								$hasMessageFiles		=	$row['hasMessageFiles'];
								$fileName				=	$row['fileName'];
								$fileExtension			=	$row['fileExtension'];
								$fileSize				=	$row['fileSize'];
								$emailUniqueCode		=	$row['emailUniqueCode'];
								$isNeedToVerify			=	$row['isNeedToVerify'];
								$isRepliedToEmail		=	$row['isRepliedToEmail'];
								$messageRepliedMarkedBy	=   $row['messageRepliedMarkedBy'];
								$msgRepliedOn			=   $row['repliedOn'];
								$msgRepliedTime			=   $row['repliedTime'];
								$selectedSmsId			=	$row['smsId'];

								$smsSentText			=	"";
								if(!empty($selectedSmsId))
								{
									$smsSentText		=	"&nbsp;[<font color='#ff0000;'>Also sent SMS</font>]";
								}

								$repliedText			=   "";
								if($isRepliedToEmail	==	0)
								{
									$repliedText		=	"&nbsp;[<a onClick=\"replyAllMessageForcefully($t_messageId,$customerId,1);\"  class=\"greenLink\" style='cursor:pointer;'>Action Taken</a>]";
								}
								else
								{
									if(!empty($messageRepliedMarkedBy))
									{
										$respliedMsgBy	=	$employeeObj->getEmployeeName($messageRepliedMarkedBy);

										$repliedText	=	"&nbsp;[Replied By : ".$respliedMsgBy." at ".showDateTimeFormat($msgRepliedOn,$msgRepliedTime);"]";
									}
								}
					



								$emailSubject	=	stripslashes($row['emailSubject']);
								$readEmailText	=	"";

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
										$notifyText .=	"&nbsp;<a href='".SITE_URL_EMPLOYEES."/send-message-pdf-customer.php?orderId=$orderId&customerId=$customerId&vmsg=$t_messageId&selectedTab=5#sendMessages' class='link_style14'>Verify It</a>";
									}
									
									$notifyText		.=	"]</font>";

								}	
								$displayEmpCustMsg			=	makeLinkFromText($t_message);
								
								if($messageBy   ==  EMPLOYEES)
								{
									$employeeId		=	$row['employeeId'];
									$employeeName	=	$employeeObj->getEmployeeName($employeeId);
									echo "<tr><td class='smalltext1'><b>".$employeeName." at ".$daysAgo."</b><br />".$notifyText.$smsSentText."</td><td></tr>";

									if($readDateIp		=	$employeeObj->getFirstEmailReadTime($emailUniqueCode))
									{
										list($readDate,$readTime)	=	explode("|",$readDateIp);
										$readDateTime  =  showDateTimeFormat($readDate,$readTime,1);	

										$readEmailText =	"&nbsp;(<font color='#ff0000'>Customer Read at </font>".$readDateTime.")";
									}
								}
								elseif($messageBy   ==  CUSTOMERS)
								{
									echo "<tr><td class='smalltext1'><b>".$customerName." at ".$daysAgo."</b>".$repliedText."</td></tr>";
								}
								echo "<tr><td class='smalltext2' style='margin-top:0px;line-height:16px;'>";
								
								echo $displayEmpCustMsg.$readEmailText;
								
								echo "</td></tr>";
								if($hasMessageFiles == 1 && empty($isDeleted))
								{
									
									if($isNewUploadingSystem == 1)
									{
										if($result1			=	$orderObj->getOrdereMessageFile($orderId,$t_messageId,3,7))
										{
											echo "<tr><td colspan='2' valign='top'><table width='100%' align='left'><tr><td width='12%' class='smalltext2' valign='top'><b>Uploaded File : </b></td><td valign='top'><table width='100%' align='left'>";

											while($row1			=	mysql_fetch_assoc($result1))
											{
												$fileId			=	$row1['fileId'];
												$fileName		=	stripslashes($row1['uploadingFileName']);
												$fileExtension	=	$row1['uploadingFileExt'];
												$fileSize		=	$row1['uploadingFileSize'];
												$imageOnServerPath	=	$row1['excatFileNameInServer'];

												$base_fileId	=	base64_encode($fileId);
												
												$downLoadPath	=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
											?>
											<tr>
												 <td>
													<a class="link_style32" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download Message File" style="cursor:pointer;"><?php echo $fileName.".".$fileExtension;?></a>&nbsp;&nbsp;<font class='smalltext20'><?php echo getFileSize($fileSize);?></font>
												<?php
													if(in_array($fileExtension,$a_displayAnyImageOfType) && $messageBy   ==  EMPLOYEES && $fileSize <= "3145728")
													{
														list($imgWidth, $imgHeight, $type, $attr) = getimagesize($imageOnServerPath);

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
												<br><img src="<?php echo SITE_URL_EMPLOYEES;?>/get-employee-message-image.php?memberId=<?php echo $customerId;?>&orderId=<?php echo $orderId;?>&messageId=<?php echo $t_messageId;?>&isNewSystem=1" border="0" title="<?php echo $fileName.".".$fileExtension;?>" <?php echo $imageWidth;?> <?php echo $imageHeight;?> onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');"  style="cursor:pointer">
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
										echo "<tr><td colspan='2' class='textstyle2'><b>Uploaded File : </b><a href='".SITE_URL_EMPLOYEES."/download-message-files.php?ID=$t_messageId'  class='link_style32'>".$fileName.".".$fileExtension."</a>&nbsp;&nbsp;<font class='smalltext'>".getFileSize($fileSize)."</font>";
										if(in_array($fileExtension,$a_displayAnyImageOfType) && $messageBy   ==  EMPLOYEES  && $fileSize <= "3145728")
										{
											$messageFilePath			=	SITE_ROOT_FILES."/files/messages/".$t_messageId."_".$fileName.".".$fileExtension;

											list($imgWidth, $imgHeight, $type, $attr) = getimagesize($messageFilePath);

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
											<br><a href="<?php echo SITE_URL_EMPLOYEES;?>/download-message-files.php?ID=<?php echo $t_messageId;?>"><img src="<?php echo SITE_URL_EMPLOYEES;?>/get-employee-message-image.php?memberId=<?php echo $customerId;?>&orderId=<?php echo $orderId;?>&messageId=<?php echo $t_messageId;?>&isNewSystem=0" border="0" title="<?php echo $fileName.".".$fileExtension;?>" <?php echo $imageWidth;?> <?php echo $imageHeight;?>></a>
									<?php
										}
										echo "</td></tr>";
									}
									
								}
								
							}
						}
						else
						{
							echo "<tr><td height='50' style='text-align:center'><font style='font-size:16px;font-family:verdana;color:#ff0000;font-weight:bold'>No Message Available</font></td></tr>";
						}
					?>
				</table>
			<?php
				$query	=	"SELECT * FROM order_attention_messages WHERE memberId=$customerId AND orderId=$orderId ORDER BY date,time";
				$result	=	dbQuery($query);
				if(mysql_num_rows($result))
				{
			?>
			<br>
				<table width='100%' align='center' cellpadding='3' cellspacing='2' border='0'>
					<tr>
						<td colspan="2" class="heading3"><b>ATTENTION MESSAGE TO CUSTOMER</b></td>
					</tr>
					<tr>
						<td colspan="2" height="5"></td>
					</tr>
				<?php
						$at					 =	0;
						while($row			 =	mysql_fetch_assoc($result))
						{
							$at++;
							$attentionId	 =	$row['messageId'];
							$attentionMessage=	stripslashes($row['message']);
							$attentionDate	 =	$row['date'];
							$attentionTime	 =	$row['time'];
							$attentionBy	 =	$row['employeeId'];
							$emailUniqueCode =	$row['emailUniqueCode'];
							$isSentSMS		 =	$row['isSentSMS'];

							$daysAgoAttention=	showDateTimeFormat($attentionDate,$attentionTime);

							$smsSentAttention=	"";
							if(!empty($isSentSMS))
							{
								$smsSentAttention=	"&nbsp;[<font color='#ff0000;'>Also sent SMS</font>]";
							}

							$readEmailText	 =	"";
							if($readDateIp	 =	$employeeObj->getFirstEmailReadTime($emailUniqueCode))
							{
								list($readDate,$readTime)	=	explode("|",$readDateIp);
								$readEmailText =	"&nbsp;(<font color='#ff0000'>Customer Read At - ".showDate($readDate)." EST at - ".showTimeFormat($readTime)." Hrs</font>)";
							}

							$attentionBy	 =	$employeeObj->getEmployeeFirstName($attentionBy);
						?>
						<tr>
							<td class="smalltext1" valign="top">
								<?php 
									echo "<b>Need attention message by - ".$attentionBy." on ".$daysAgoAttention.$smsSentAttention."</b>";
								?>
							</td>
						</tr>
						<tr>
							<td class="smalltext2" valign="top">
								<?php echo $attentionMessage.$readEmailText;?>
							</td>
						</tr>
						<?php
							}
						?>
				</table>
			<?php
				}
			?>
			</div>
			<!--********************************************************************************
			/////////////// ENDING OF DISPLAYING EMPLOYEE CUSTOMER ORDER MESSAGES //////////////
			************************************************************************************
			************************************************************************************
			************************************************************************************
			///////////////DISPLAYING EMPLOYEE CUSTOMER PREVIOUS ORDER MESSAGES ////////////////
			************************************************************************************-->
			<div id="showingOrderDetailsFor6" style="display:<?php echo $displayHideMainTabs6;?>">
				<table width="98%" align="center" border="0" cellpadding="3" cellspacing="2">
					<tr>
						<td colspan="6" class="smalltext24">
							<b><font color='#ff0000;'>PREVIOUS ORDER MESSAGES & CUSTOMER GENERAL MESSAGES </font></b>&nbsp;
						</td>
					</tr>
					<tr>
						<td colspan="2" class="heading3"><b>PREVIOUS ORDER MESSAGES</b></td>
					</tr>
					<tr>
						<td colspan="2" height="5"></td>
					</tr>
					<?php
						$calculatedOrderFrom	=	getPreviousGivenDate($nowDateIndia,30);
						//*****Section to show previous 10 customer messages if any given*****//
						if($result				=	$orderObj->previousOrdersMessages($orderId,$customerId,10,$calculatedOrderFrom))
						{
						?>
						<tr>
							<td width="2%">&nbsp;</td>
							<td width="27%" class="smalltext23"><b>Order No</b></td>
							<td class="smalltext23" width="15%"><b>Date</b></td>
							<td class="smalltext23"><b>Message</b></td>
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
									<td class="smalltext2" valign="top"><?php echo $messageCount;?>)</td>
									<td valign="top"><a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $m_orderId;?>&customerId=<?php echo $customerId;?>#messages" class='link_style32'><?php echo getSubstring($m_orderName,45);?></a><br><?php echo $customerMessageImage;?>
									</td>
									<td class="smalltext2" valign="top"><?php echo showDateTimeFormat($messageDate,$messageTime);?></td>
									<td class="smalltext1" valign="top"><?php echo $m_message;?></td>
								</tr>
						<?php
							}
													
						}
						else
						{
							echo "<tr><td height='20' style='text-align:center'><font style='font-size:16px;font-family:verdana;color:#ff0000;font-weight:bold'>No Message Available</font></td></tr>";
						}
					?>
				</table>
				<br />
				<table width="98%" align="center" border="0" cellpadding="3" cellspacing="2">
					<tr>
						<td colspan="3" class="heading3"><b>CUSTOMER LAST 10 GENERAL MESSAGES</b></td>
					</tr>
					<?php
						if($result		=	$orderObj->previousAllOrdersMessages($customerId,10))
						{
						?>
							<tr>
								<td width="3%">&nbsp;</td>
								<td class="smalltext23" width="15%"><b>Date</b></td>
								<td class="smalltext23"><b>Message</b></td>
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
									<td class="smalltext2" valign="top"><?php echo $messageCount.$customerMessageImage;?>)</td>
									<td class="smalltext2" valign="top"><?php echo showDateTimeFormat($messageDate,$messageTime);?></td>
									<td valign="top">
										<table width="100%" align="center" cellpadding="0" cellspacing="0">
											<tr>
												<td colspan="2" class="smalltext1">
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
													<td width="3%" class="smalltext2" valign="top">
														<?php echo $cn;?>)
													</td>
													<td valign="top">
														<a class="link_style32" onclick="downloadGeneralMessageFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $fileName;?></a>
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
						<?php
							   }
							}
							else
							{
								echo "<tr><td height='20' style='text-align:center'><font style='font-size:16px;font-family:verdana;color:#ff0000;font-weight:bold'>No Message Available</font></td></tr>";
							}
					?>
				</table>
			</div>
			<!--********************************************************************************
			/////////// ENDING OF DISPLAYING EMPLOYEE CUSTOMER PREVIOUS ORDER MESSAGES /////////
			************************************************************************************
			************************************************************************************
			************************************************************************************
			//////////////////// DISPLAYING CUSTOMER PREVIOUS ORDER RATINGS ////////////////////
			************************************************************************************-->
			<div id="showingOrderDetailsFor7" style="display:<?php echo $displayHideMainTabs7;?>">
				<table width="98%" align="center" border="0" cellpadding="3" cellspacing="2">
					<tr>
						<td colspan="6" class="smalltext24">
							<b><font color='#ff0000;'>RATINGS IN 10 PREVIOUS ORDERS</font></b>&nbsp;
						</td>
					</tr>
				<?php
					if($result	=	$orderObj->previousOrdersCustomerRatingComments($orderId,$customerId,10,$calculatedOrderFrom))
					{
				?>
					<tr>
						<td width="3%">&nbsp;</td>
						<td width="25%" class="smalltext23"><b>Order No</b></td>
						<td class="smalltext23" width="10%"><b>Rating</b></td>
						<td class="smalltext23" width="13%"><b>Date</b></td>
						<td class="smalltext23"><b>Message</b></td>
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
							$cr_rateGivenTime	=  $ratingCustomerRow['rateGivenTime'];
							$cr_message			=  stripslashes($ratingCustomerRow['memberRateMsg']);
							$cr_message			=  nl2br($cr_message);
							$cr_orderName		=  $ratingCustomerRow['orderAddress'];
							$cr_orderName		=  stripslashes($cr_orderName);

							$hoursDiff	=	timeBetweenTwoTimes($cr_rateGivenOn,$cr_rateGivenTime,$nowDateIndia,$nowTimeIndia);

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
								<td class="smalltext2" valign="top"><?php echo $customerRatingCount;?>)</td>
								<td valign="top"><a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $cr_orderId;?>&customerId=<?php echo $customerId;?>" class='link_style32'><?php echo getSubstring($cr_orderName,20);?></a><br><?php echo $rateMessageImage;?></td>
								<td class="smalltext23" valign="top">
									<?php
										if(!empty($cr_rateGiven))
										{
											echo "<img src='".SITE_URL."/images/rating/$cr_rateGiven.png'>";
											echo $a_existingRatings[$cr_rateGiven];
										}
										else
										{
											echo "&nbsp;";
										}
									?>
									
								</td>
								<td class="smalltext2" valign="top">
									<?php echo showDateTimeFormat($cr_rateGivenOn,$cr_rateGivenTime);?>
								</td>
								<td class="smalltext1" valign="top">
									<?php echo $cr_message;?>
								</td>
							</tr>
						<?php
							}
						}
						else
						{
							echo "<tr><td height='250' style='text-align:center'><font style='font-size:16px;font-family:verdana;color:#ff0000;font-weight:bold'>No Rating Available</font></td></tr>";
						}
					?>
				</table>
			</div>
			<!--********************************************************************************
			//////////////// ENDING OF DISPLAYING CUSTOMER PREVIOUS ORDER RATINGS //////////////
			************************************************************************************
			************************************************************************************
			************************************************************************************
			////////////////////// DISPLAYING EMPLOYEE INTERNAL ORDER MESSAGES /////////////////
			************************************************************************************-->
			<div id="showingOrderDetailsFor8" style="display:<?php echo $displayHideMainTabs8;?>">
				<table width="98%" align="center" border="0" cellpadding="3" cellspacing="2">
					<tr>
						<td colspan="6" class="smalltext24">
							<b><font color='#ff0000;'>CURRENT ORDERS EMPLOYEES INTERNAL MESSAGES</font></b>&nbsp;
						</td>
					</tr>
					<?php
						if($result	=	$orderObj->getOrderEmployeeMessages($orderId))
						{
					?>
					<tr>
						<td width="2%">&nbsp;</td>
						<td class="smalltext23" width="70%"><b>Message</b></td>
						<td class="smalltext23" width="18%"><b>Employee</b></td>
						<td class="smalltext23"><b>Date</b></td>
					</tr>
					<tr>
						<td colspan="6">
							<hr size="1" width="100%" color="#bebebe">
						</td>
					</tr>
					<?php
							$count2						=  0;
							while($row					=	mysql_fetch_assoc($result))
							{
								$count2++;
								$t_empMessageId			=	$row['messageId'];
								$t_empMessage			=	stripslashes($row['message']);
								$t_empMessageAddedOn	=	showDate($row['addedOn']);
								$t_empMessageBy			=	$row['messageBy'];
								$t_addedOn				=	$row['addedOn'];
								$t_empMessageByName		=	$employeeObj->getEmployeeFirstName($t_empMessageBy);
					?>
					<tr>
						<td class="smalltext2" valign="top"><?php echo$count2;?>)</td>
						<td class="smalltext1" valign="top"><?php echo nl2br($t_empMessage);?></td>
						<td class="smalltext2" valign="top"><?php echo $t_empMessageByName;?>
						</td>
						<td class="smalltext2" valign="top"><?php echo showDate($t_addedOn);?>
						</td>
					</tr>
					<?php							
							}
						}
						else
						{
							echo "<tr><td height='250' style='text-align:center'><font style='font-size:16px;font-family:verdana;color:#ff0000;font-weight:bold'>No Message Available</font></td></tr>";
						}
						if($result		=	$orderObj->previousOrdersEmployeesMessages($orderId,$customerId,10))
						{
					?>
					</table>
					<table width="98%" align="center" border="0" cellpadding="3" cellspacing="2">
						<tr>
							<td height="10"></td>
						</tr>
						<tr>
							<td colspan="6" class="smalltext24">
								<b><font color='#ff0000;'>CUSTOMERS PREVIOUS ORDERS EMPLOYEES INTERNAL MESSAGES</font></b>&nbsp;
							</td>
						</tr>
						<tr>
							<td width="2%">&nbsp;</td>
							<td width="20%" class="smalltext1"><b>Order No</b></td>
							<td class="smalltext1" width="50%"><b>Message</b></td>
							<td class="smalltext1" width="18%"><b>Employee</b></td>
							<td class="smalltext23"><b>Date</b></td>
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
								$e_addedOn			=  $emessageRow['addedOn'];

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
							<td class="smalltext2" valign="top"><?php echo $emessageCount;?>)</td>
							<td valign="top"><a href="<?php echo SITE_URL_EMPLOYEES;?>/internal-emp-msg.php?orderId=<?php echo $e_orderId;?>&customerId=<?php echo $customerId;?>#messages" class='link_style12'><?php echo getSubstring($e_address,35);?></a><br><?php echo $orderAddedOnImg;?></td>
							<td class="smalltext1" valign="top"><?php echo nl2br($e_message);?></td>
							<td class="smalltext1" valign="top">
								<?php
									echo $e_messageByName;
								?>
							</td>
							<td class="smalltext1" valign="top"><?php echo showDate($e_addedOn);?></td>
						</tr>
						<tr>
							<td colspan="6" height="3"></td>
						</tr>
					<?php
						}
					}
					?>
				</table>
			</div>
			<!--********************************************************************************
			/////////////// ENDING OF DISPLAYING EMPLOYEE INTERNAL ORDER MESSAGES///// /////////
			************************************************************************************-->

			<!--<div id="showingOrderDetailsFor">
				<!-- DISPLAYING ORDER INFO
				<?php
					if($selectedTab	==	1)
					{
						include(SITE_ROOT_EMPLOYEES	. "/includes/viewing-orders-data.php");
					}
					elseif($selectedTab	==	2)
					{
						include(SITE_ROOT_EMPLOYEES	. "/includes/viewing-orders-completed-data.php");
					}
					elseif($selectedTab	==	3)
					{
						include(SITE_ROOT_EMPLOYEES	. "/includes/viewing-customers-special-instructions.php");
					}
					elseif($selectedTab	==	4)
					{
						include(SITE_ROOT_EMPLOYEES	. "/includes/viewing-employee-notes-files.php");
					}
					elseif($selectedTab	==	5)
					{
						include(SITE_ROOT_EMPLOYEES	. "/includes/viewing-employee-customer-order-messages.php");
					}
					elseif($selectedTab	==	6)
					{
						include(SITE_ROOT_EMPLOYEES	. "/includes/viewing-employee-customer-previous-order-messages.php");
					}
					elseif($selectedTab	==	7)
					{
						include(SITE_ROOT_EMPLOYEES	. "/includes/viewing-customer-previous-order-ratings.php");
					}
					elseif($selectedTab	==	8)
					{
						include(SITE_ROOT_EMPLOYEES	. "/includes/viewing-employees-internal-messages.php");
					}
				?>
			</div>-->
		</td>
	</tr>
</table>
<?php
	if(isset($hasRatingExplanation) && !empty($hasRatingExplanation))
	{
		$query	=	"SELECT * FROM reply_on_orders_rates WHERE orderId=$orderId ORDER BY replyId DESC";
		$result	=	dbQuery($query);
		if(mysql_num_rows($result))
		{
?>
<br>
<table width="99%" align="center" border="0" cellpadding="0" cellspacing="0" style="border:0px solid #333333;">
	<tr>
		<td colspan="6" class="smalltext24">
			<b><font color='#ff0000;'>EXPLANATION ON CUSTOMER RATINGS</font></b>&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan="2" height="5"></td>
	</tr>
</tr>
<?php
	while($row	= mysql_fetch_assoc($result))
	{
		$t_empReplyId				=	$row['replyId'];
		$t_empReplyMessage			=	stripslashes($row['comment']);
		$t_empReplyAddedOn			=	showDate($row['addedOn']);
		$t_empReplyBy				=	$row['addedby'];
		$t_processQaEmployee		=	$row['processQaEmployee'];
		$t_empReplyByName			=	$employeeObj->getEmployeeName($t_empReplyBy);
		
		$addedbyEmployeeType		=	"";
		if($t_processQaEmployee		==	1)
		{
			$addedbyEmployeeType	=	"Processed Employee";
		}
		elseif($t_processQaEmployee	==	2)
		{
			$addedbyEmployeeType	=	"QA Done Employee";
		}
		elseif($t_processQaEmployee	==	3)
		{
			$addedbyEmployeeType	=	"Processed & QA Done Employee";
		}

		
		echo "<tr><td colspan='2' class='smalltext2'>".nl2br($t_empReplyMessage)."</td></tr>";
		echo "<tr><td class='smalltext1'><br><b>By : <b>$t_empReplyByName</b>, on $t_empReplyAddedOn</b></td></tr>";
		echo "<tr><td class='smalltext1'><br><b>Employee Type : <b>$addedbyEmployeeType</b></td></tr>";
		echo "<tr><td colspan='2'><hr size='1' width='100%' color='#bebebe'></td></tr>";
		}
	}
	echo "</table>";
}

?>

