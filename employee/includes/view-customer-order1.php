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
		if(mysqli_num_rows($result))
		{
			$row				=	mysqli_fetch_assoc($result);
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
				
		for(i=1;i<10;i++)
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

	function viewOrderHistory(orderId,customerId)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/view-order-history.php?orderId="+orderId+"&customerId="+customerId;
		prop = "toolbar=no,scrollbars=yes,width=850,height=700,top=100,left=100";
		window.open(path,'',prop);
	}
	function viewOrderDoneEmployeeList(memberId)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/view-customers-total-order-by-employee.php?memberId="+memberId;
		prop = "toolbar=no,scrollbars=yes,width=600,height=450,top=100,left=100";
		window.open(path,'',prop);
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

	function viewBiggerImage(customerId,orderId,messageId,isNewSystem)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/view-message-file-bigger-image.php?customerId="+customerId+"&orderId="+orderId+"&messageId="+messageId+"&isNewSystem="+isNewSystem;
		prop = "toolbar=no,scrollbars=yes,width=1100,height=800,top=100,left=100";
		window.open(path,'',prop);
	}

	function viewAnyFileBiggerImage(fileId)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/view-any-file-bigger-image.php?fileId="+fileId;
		prop = "toolbar=no,scrollbars=yes,width=1100,height=800,top=100,left=100";
		window.open(path,'',prop);
	}

	function markUnmarkTatExplanation(customerId,orderId,operation)
	{
		/*if(operation == 1){
			var confirmation = window.confirm("Are You Sure Mark Tat Explanation For This Customer?");
		}
		else{
			var confirmation = window.confirm("Are You Sure Un-Mark Tat Explanation For This Customer?");
		}

		if(confirmation == true)
		{*/

			window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId="+orderId+"&customerId="+customerId+"&markUnmarkTat="+operation;
		//}
		
	}

	

	
</script>
<?php
	$M_D_5_ORDERID						=	ORDERID_M_D_5;
	$M_D_5_ID							=	ID_M_D_5;
	$a_customerOrderTemplateFiles		=	array();
	$a_customerOrderTemplateFilesSize	=	array();
	
	$a_existingRatings					=	$orderObj->getFeedbackText();
	if(is_numeric($orderId) && is_numeric($customerId)){
		$instructionDaysDifferent			=	"0";
		if($result							=	$orderObj->getOrderDetails($orderId,$customerId))
		{
			$row							=	mysqli_fetch_assoc($result);
			$encodeOrderID					=	base64_encode($orderId);
			$customerId						=	$row['memberId'];
			$orderAddress					=	stripslashes($row['orderAddress']);
			$orderType						=	$row['orderType'];
			$customersOwnOrderText			=	stripslashes($row['customersOwnOrderText']);
			$state							=	$row['state'];
			$instructions					=	trim(stripslashes($row['instructions']));

			$orderPlacedDate				=	$orderAddedISTOn = $row['orderAddedOn'];
			
			$orderPlacedCustomerDate		=	$row['estDate'];
			$orderAddedCustomerTime			=	$row['estTime'];

			$orderAddedOn					=	showDate($orderPlacedDate);
			$assignToEmployee				=	showDate($row['assignToEmployee']);
			$firstName				        =	stripslashes($row['firstName']);
			$firstName				        =	stringReplace("'","",$firstName);
			$firstName				        =	stringReplace('"',"",$firstName);
			$lastName				        =	stripslashes($row['lastName']);
			$lastName				        =	stringReplace("'","",$lastName);
			$lastName				        =	stringReplace('"',"",$lastName);
			$dispalyCustomerPhone			=	$row['phone'];
			$customerEmail					=	$row['email'];
			$hasReceiveEmails				=	$row['noEmails'];
			$customerSecondaryEmail			=	$row['secondaryEmail'];
			$folderId						=	$row['folderId'];
			
			$orderAddedTime					=	$row['orderAddedTime'];
			$appraisalSoftwareType			=	$row['appraisalSoftwareType'];
			$acceptedBy						=	$row['acceptedBy'];
			$status							=	$row['status'];
			$orderCompletedOn				=	$row['orderCompletedOn'];
			$refferedBy						=	$row['refferedBy'];
			$isDeleted						=	$row['isDeleted'];
			$state							=	$row['state'];
			$isReplyFileInEmail				=	$row['isReplyFileInEmail'];
			$isCustomerOptedForSms			=	$row['isOptedForSms'];
			$isDonePostAudit				=	$row['isDonePostAudit'];
			$isNewUploadingSystem			=	$row['isNewUploadingSystem'];
			$newUploadingPath				=	$row['newUploadingPath'];
			$newUploadingPath               =   stringReplace("/home/ieimpact", "", $newUploadingPath);
			$isAlamodeOrder					=	$row['isAlamodeOrder'];
			$aLamodeCustomerID				=	$row['aLamodeCustomerID'];
			$customerAlaModeId				=	$row['customerAlaModeId'];
			$orderEncryptedId				=	$row['encryptOrderId'];
			$cutomerTotalOrdersPlaced		=	$row['totalOrdersPlaced'];
			$isOrderChecked					=	$row['isOrderChecked'];
			$isEmailOrder					=	$row['isEmailOrder'];
			$captureEmailOrderThrough		=	$row['captureEmailOrderThrough'];
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
			$isSetedOrderField				=	$row['isSetedOrderField'];
			$memberUniqueEmailCode			=	$smartEmailUniqueEmailCode = $row['uniqueEmailCode'];
			$memberOrderReplyToEmail	    =	$row['orderReplyToEmail'];
			$isSpecialRateCustomer			=	$row['specialRateCustomer'];
			$easyNQuickInstructionsDone		=	$row['easyNQuickInstructionsDone'];


			////MERGED
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
			$paymentGateway					=	$row['paymentGateway'];
			$stripeChargeId					=	$row['chargeId'];
			$usingStripeAccountId			=	$row['usingStripeAccountId'];
			$isHavingOrderNewMessage		=	$row['isHavingOrderNewMessage'];
			$isRepliedToRatingMessage		=	$row['isRepliedToRatingMessage'];
			$isRateCountingEmployeeSide		=	$row['isRateCountingEmployeeSide'];
			$isReadInstructions		        =	$row['isReadInstructions'];
			$isReadEmployeeNote				=	$row['isReadEmployeeNote'];
			$postOrderCost					=	$row['postOrderCost'];
			$isShortOrder					=	$row['isShortOrder'];

			$hasUploadedRatingFile			=	$row['hasUploadedRatingFile'];
			$rateGiven                      =	$row['rateGiven'];
			$isAllowedRewards				=	$row['isAllowedRewards'];
			$customerAverageTimeTaken 		=	$row['averageTimeTaken'];
			$needTatExplanation 			=	$row['needTatExplanation'];
			$islacarteOrder			        =	$orderObj->isALaCarteOrder($customerId,$orderId);
			if($islacarteOrder == 1){

				if($islacarteOrder  == 1){
					$a_checkedLaCartePrice  =   array();
					$a_allLaCartePrices     =   $orderObj->getLaCartePrices($customerId);
					
					$query1 			=	"SELECT selectedId FROM la_carte_orders_checkfiled WHERE orderId=$orderId AND memberId=$customerId";
					$result1		    =	dbQuery($query1);
					if(mysqli_num_rows($result1))
					{
						while($row1 =  mysqli_fetch_assoc($result1)){
							$choosenSelectedId 	     = $row1['selectedId'];
							$a_checkedLaCartePrice[] = $choosenSelectedId;
						}
					}
					
				}
			}

			$totalExistsTemplateFile        =	$employeeObj->getSingleQueryResult("SELECT COUNT(fileId) as total FROM order_all_files WHERE uploadingType=1 AND uploadingFor=1 AND orderId=$orderId","total");

			if($totalExistsTemplateFile     == 1){
				////////////////// ONLY CHECK IF CUSTOMER HAS UPLOADED SINGLE TEMPLATE FILE ////
				$orderFileSize	=	$employeeObj->getSingleQueryResult("SELECT uploadingFileSize FROM order_all_files WHERE orderId=$orderId AND uploadingType=1 AND uploadingFor=1","uploadingFileSize");
				if(empty($orderFileSize)){
					$orderFileSize = "";
				}
			}
		
			$isAlamodeCustomerText			=	"";

			if(!empty($isAlamodeOrder)){
				$isAlamodeCustomerText		=	" (Almode Customer)";
			}
			elseif(!empty($aLamodeCustomerID)){
				$isAlamodeCustomerText		=	" (Almode Customer)";
			}
			elseif(!empty($customerAlaModeId)){
				$isAlamodeCustomerText		=	" (Almode Customer)";
			}

			$vocalText						=	"";
			/*if($isVocalCustomer				==	"yes"){
				$vocalText					=	"(<font color='#ff0000'>V****</font>)";
			}*/

			$specialRateText				=	"";
			if($isSpecialRateCustomer		==	"yes"){
				$specialRateText			=	"<br /><font class='smalltext1'>[<font color='#ff0000;'>Note*:</font> Using DataMaster, Need Less Data Entry. Customer getting discount as he promised will need less Data Entry time, if not true, let Ranbir/Hemant know]</font>";
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
			$hasMarkedSketchYes		=	"<img src='".SITE_URL."/images/uncheck-checkbox.jpg'>";
			if($providedSketch		==	1)
			{
			   $hasMarkedSketchYes	=	"<img src='".SITE_URL."/images/check-checkbox.jpg'>";
			}

			$displayOrderTimeFormat	 =	showTimeFormat($orderAddedTime);

			$memberRateMsg			 =	stripslashes($row['memberRateMsg']);
			$splInstructionToEmployee=	stripslashes($row['splInstructionToEmployee']);
			$instructionsUpdatedOn	 =	$row['instructionsUpdatedOn'];
			$splInstructionOfCustomer=	stripslashes($row['splInstructionOfCustomer']);
			$addedInstructionsOn 	 =	$row['addedInstructionsOn'];
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
			$customerName			=   $firstName." ".substr($lastName, 0, 1);

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
					if(isset($a_allDeactivatedEmployees) && !empty($a_allDeactivatedEmployees) && array_key_exists($acceptedBy,$a_allDeactivatedEmployees)){
						$acceptedByName	=	"<a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&orderOf=137&showingEmployeeOrder=1' class='link_style32'>Hemant Jindal</a>";
					}
					else{
						$acceptedByName	=	"<a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&orderOf=$acceptedBy&showingEmployeeOrder=1' class='link_style32'>".$acceptedByName."</a>";
					}
					
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

			if(isset($a_allDeactivatedEmployees) && !empty($a_allDeactivatedEmployees)  && array_key_exists($qaDoneBy,$a_allDeactivatedEmployees)){
				$qaDoneByName=  "Hemant Jindal";
				$qaDoneBy    =  137;
			}

			if($status	!= 0 && $status	!=  1)
			{
				if($isSetedOrderField	==	0)
				{
					
					if(!empty($qaDoneBy))
					{
						$qaDoneByText	=	"<a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&orderOf=$qaDoneBy&showingEmployeeOrder=1&displayTypeCompleted=1' class='link_style32'>".$qaDoneByName."</a>";
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


			$hasReplied				=	$hasRepliedUploaded;
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
			$isAlreadyAddedCheckbox = 0;
			$reoForm			=	0;
			$financingVa		=	0;
			$financingFha		=	0;
			$financingHud		=	0;
			$nonUad				=	0;

			
			$query3				=	"SELECT * FROM orders_new_checkboxes WHERE orderId=$orderId";
			$result3			=	dbQuery($query3);
			if(mysqli_num_rows($result3))
			{
				$row3			=	mysqli_fetch_assoc($result3);
				$isAlreadyAddedCheckbox = 1;
				$reoForm		=	$row3['reoForm'];
				$financingVa	=	$row3['financingVa'];
				$financingFha	=	$row3['financingFha'];
				$financingHud	=	$row3['financingHud'];
				$nonUad			=	$row3['nonUad'];

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

					dbQuery("DELETE FROM customer_orders_messages_counts WHERE messageId=$messageId AND orderId=$orderId AND memberId=$customerId");
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
	}
	else{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
		exit();
	}


	$instructionsBlinkingStar	    =	"";
	$employeeNoteBlinkingStar	    =	"";
	$inhouseBlinkingStar            =   "";
	
	if(!empty($splInstructionToEmployee))
	{
		if($instructionsUpdatedOn	!=	"0000-00-00")
		{			
			$diffDays	= (strtotime(CURRENT_DATE_INDIA) - strtotime($instructionsUpdatedOn)) / (60 * 60 * 24);			
			
			if($diffDays <= 3)
			{
				$instructionsBlinkingStar	=	"&nbsp;<img src='".SITE_URL."/images/yellow-blink.png'>";
			}		
		}		
	}
	if(!empty($splInstructionOfCustomer))
	{
		if($addedInstructionsOn	!=	"0000-00-00")
		{
			$diffDays	= (strtotime(CURRENT_DATE_INDIA) - strtotime($addedInstructionsOn)) / (60 * 60 * 24);	
			
			if($diffDays <= 3)
			{
				$employeeNoteBlinkingStar	=	"&nbsp;<img src='".SITE_URL."/images/yellow-blink.png'>";
			}		
		}		
	}
	if(empty($instructionsBlinkingStar)){
		$lastFileUploaded	=	$employeeObj->getSingleQueryResult("SELECT addedOn FROM customer_instructions_file WHERE memberId=$customerId AND uploadedBy='".CUSTOMERS."' ORDER BY addedOn DESC LIMIT 1","addedOn");

		if(!empty($lastFileUploaded) && $lastFileUploaded != "0000-00-00"){

			$diffDays	= (strtotime(CURRENT_DATE_INDIA) - strtotime($lastFileUploaded)) / (60 * 60 * 24);	
		
			if($diffDays <= 3)
			{
				$instructionsBlinkingStar	=	"&nbsp;<img src='".SITE_URL."/images/yellow-blink.png'>";
			}	
		}
		
	}

	if(empty($employeeNoteBlinkingStar)){
		$lastFileUploaded	=	$employeeObj->getSingleQueryResult("SELECT addedOn FROM customer_instructions_file WHERE memberId=$customerId AND uploadedBy='".EMPLOYEES."' ORDER BY addedOn DESC LIMIT 1","addedOn");

		if(!empty($lastFileUploaded) && $lastFileUploaded != "0000-00-00"){
			
			$diffDays	= (strtotime(CURRENT_DATE_INDIA) - strtotime($lastFileUploaded)) / (60 * 60 * 24);	
		
			if($diffDays <= 3)
			{
				$employeeNoteBlinkingStar	=	"&nbsp;<img src='".SITE_URL."/images/yellow-blink.png'>";
			}	
		}
		
	}

	$lastInHouseFileUploaded	=	$employeeObj->getSingleQueryResult("SELECT addedOn FROM order_inhouse_messages WHERE orderId=$orderId AND memberId=$customerId ORDER BY id DESC LIMIT 1","addedOn");

	if(!empty($lastInHouseFileUploaded) && $lastInHouseFileUploaded	!=	"0000-00-00")
	{
		$diffDaysInHousemsg	= (strtotime(CURRENT_DATE_INDIA) - strtotime($lastInHouseFileUploaded)) / (60 * 60 * 24);	
		
		if($diffDaysInHousemsg <= 3)
		{
			$inhouseBlinkingStar	=	"&nbsp;<img src='".SITE_URL."/images/yellow-blink.png'>";
		}		
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
function sendingNewGeneralMsg(memberId)
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/sending-customer-general-message.php?memberId="+memberId;
	prop = "toolbar=no,scrollbars=yes,width=1100,height=600,top=100,left=100";
	window.open(path,'',prop);
}

function getAIExtractedPropertyDetails(orderId, encodeOrderID, mD5OrderID, mD5ID)
{
	var containerId = 'ocrStatusContainer_' + orderId;
	var container = document.getElementById(containerId);
	
	if(!container)
	{
		alert('Container not found');
		return;
	}
	
	// Show loading state
	container.innerHTML = '(<span style="color:#0080C0;"><b>Property Details Extraction is in Progress...</b></span> <img src="<?php echo SITE_URL;?>/images/loading.gif" border="0" style="vertical-align:middle;">)';
	
	// Call the API asynchronously (fire and forget)
	var apiUrl = 'https://whizdev.ieimpact.com/employee/process-order-files-api.php?orderId=' + orderId;
	var xhr = new XMLHttpRequest();
	xhr.open('GET', apiUrl, true);
	xhr.send(); // Don't wait for response, just initiate the async process
	
	// Start polling immediately since API is asynchronous
	// The file will be available when processing is complete
	checkOCRFileStatus(orderId, containerId, encodeOrderID, mD5OrderID, mD5ID);
}

function checkOCRFileStatus(orderId, containerId, encodeOrderID, mD5OrderID, mD5ID)
{
	var checkUrl = '<?php echo SITE_URL_EMPLOYEES;?>/check-ocr-file-status.php?orderId=' + orderId;
	var container = document.getElementById(containerId);
	var pollCount = 0;
	var maxPolls = 120; // Maximum 120 polls (10 minutes if polling every 5 seconds)
	
	var pollInterval = setInterval(function() {
		pollCount++;
		
		var xhr = new XMLHttpRequest();
		xhr.open('GET', checkUrl, true);
		xhr.onreadystatechange = function() {
			if(xhr.readyState == 4 && xhr.status == 200)
			{
				try
				{
					var response = JSON.parse(xhr.responseText);
					if(response.exists)
					{
						// File exists, show the view link
						clearInterval(pollInterval);
						var viewLink = '<?php echo SITE_URL_EMPLOYEES;?>/download-multiple-file.php?' + mD5OrderID + '=' + encodeOrderID + '&' + mD5ID + '&FILE_TYPE=OCR_RESULT';
						container.innerHTML = '(<a class="link_style13" onclick="downloadMultipleOrderFile(\'' + viewLink + '\');" title="View AI-Extracted Property Details" style="cursor:pointer;"><b>View AI-Extracted Property Details</b></a>)';
					}
					else if(pollCount >= maxPolls)
					{
						// Timeout after max polls
						clearInterval(pollInterval);
						container.innerHTML = '(<span style="color:#ff0000;"><b>Timeout: File extraction is taking longer than expected. Please try again later.</b></span>)';
					}
				}
				catch(e)
				{
					if(pollCount >= maxPolls)
					{
						clearInterval(pollInterval);
						container.innerHTML = '(<span style="color:#ff0000;"><b>Error checking file status</b></span>)';
					}
				}
			}
		};
		xhr.send();
	}, 5000); // Poll every 5 seconds
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
	<tr>
		<td class="textstyle1">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=<?php echo $customerId;?>" class="link_style10"><?php echo ucwords($customerName);?></a>(<?php echo $customerAverageTimeTaken;?>) / <b><?php echo $orderAddress;?></b>
			<?php
				
				echo "&nbsp;(<a onclick='viewOrderHistory($orderId,$customerId)' style='cursor:pointer;' class='link_style10'>Log</a>)&nbsp;(<a onclick='viewOrderDoneEmployeeList($customerId)' style='cursor:pointer;' class='link_style10'>Stars</a>)";
				
			?>
		</td>
	</tr>
	<?php
		if($isHavingEstimatedTime ==	1 && ($status	==	0 || $status	==	1))
		{
			
			$expctDelvText		  =		orderETATAT($employeeWarningDate,$employeeWarningTime);
			
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
	if(isset($_POST['formQuickInsChnageSubmitted'])){
		
		if(isset($_POST['markeQuickCheckDone'])){
			$markeQuickCheckDone	=	$_POST['markeQuickCheckDone'];
			dbQuery("UPDATE members SET easyNQuickInstructionsDone='$markeQuickCheckDone' WHERE memberId=$customerId");
		}

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=".$orderId."&customerId=".$customerId."&selectedTab=3");
		exit();
	}

	if(isset($_POST['formInsReadSubmitted'])){
		
		if(isset($_POST['markInstructionsDone'])){
			dbQuery("UPDATE members_orders SET isReadInstructions=1 WHERE orderId=$orderId AND memberId=$customerId");
		}

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=".$orderId."&customerId=".$customerId."&selectedTab=3");
		exit();
	}

	if(isset($_POST['formEmpNoteReadSubmitted'])){
		
		if(isset($_POST['markInternalNoteDone'])){
			dbQuery("UPDATE members_orders SET isReadEmployeeNote=1 WHERE orderId=$orderId AND memberId=$customerId");
		}

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=".$orderId."&customerId=".$customerId."&selectedTab=4");
		exit();
	}




	

	
	$a_showingTabs						=	array("1"=>"ORDER INFO","5"=>"ORDER MESSAGES","9"=>"IN HOUSE USE".$inhouseBlinkingStar,"3"=>"STANDARD INSTRUCTIONS".$instructionsBlinkingStar,"4"=>"EMPLOYEE NOTES & FILES".$employeeNoteBlinkingStar,"2"=>"COMPLETED FILES","6"=>"MESSAGE HISTORY","7"=>"RATINGS IN PREVIOUS ORDERS","8"=>"EMPLOYEES INTERNAL MESSAGES");

	$displayHideMainTabs1				=	"";
	$displayHideMainTabs2				=	"none";
	$displayHideMainTabs3				=	"none";
	$displayHideMainTabs4				=	"none";
	$displayHideMainTabs5				=	"none";
	$displayHideMainTabs6				=	"none";
	$displayHideMainTabs7				=	"none";
	$displayHideMainTabs8				=	"none";
	$displayHideMainTabs9				=	"none";


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


	//////////////////////// UPDATE TAT EXPALANATION //////////////////////
	if(isset($_GET['markUnmarkTat'])){
		$markUnmarkTat 					=	$_GET['markUnmarkTat'];

		if($markUnmarkTat == 0 || $markUnmarkTat == 1){
			dbQuery("UPDATE members SET needTatExplanation=$markUnmarkTat WHERE memberId=$customerId");
		}
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=".$orderId."&customerId=".$customerId."&selectedTab=1");
		exit();
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
							<?php
								if(!empty($s_hasManagerAccess))
								{	
									$checkedTatEx 	        =	"checked";
									$checkedTatEx1 	        =	"";
									$checkedTatText 	    =	"<font color='#ff0000;'><b>Yes</b></font>";
									$checkedTatText1 	    =	"<font color='#000000;'><b>No</b></font>";

									if($needTatExplanation	==	0){
										$checkedTatEx 	    =	"";
									    $checkedTatEx1    	=	"checked";
									    $checkedTatText 	=	"<font color='#000000;'><b>Yes</b></font>";
										$checkedTatText1 	=	"<font color='#ff0000;'><b>No</b></font>";
									}
							?>
								&nbsp;(TAT Explanation <input type="radio" name="needTatExplanation" value="1" onclick="markUnmarkTatExplanation(<?php echo $customerId;?>,<?php echo $orderId;?>,1);" <?php echo $checkedTatEx;?>><?php  echo $checkedTatText;?> &nbsp; <input type="radio" name="needTatExplanation" value="0" onclick="markUnmarkTatExplanation(<?php echo $customerId;?>,<?php echo $orderId;?>,0);" <?php echo $checkedTatEx1;?>><?php  echo $checkedTatText1;?>)
							<?php
								}
							?>
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
					<?php
						if($resultTat      = $orderObj->isHavingTATExplanation($orderId)){
							$rowTat        =  mysqli_fetch_assoc($resultTat);
							$tatExplanation=  stripslashes($rowTat['explanation']);
					?>
					<tr>
						<td class="smalltext23">TAT Explanation</td>
						<td class="smalltext23">:</td>
						<td class="smalltext20">
							<b><?php echo nl2br($tatExplanation);?></b>
						</td>
					</tr>
					<?php

						}
					?>
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
						<td class="smalltext23" valign="top">Order Type</td>
						<td class="smalltext23" valign="top">:</td>
						<td valign="top">
							<?php echo "<font class='smalltext24'>".$orderText."</font>".$specialRateText;?>
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
						
						$reoFromText			=	"<img src='".SITE_URL."/images/uncheck-checkbox.jpg'>";
						$financingVaText		=	"<img src='".SITE_URL."/images/uncheck-checkbox.jpg'>";
						$financingFhaText		=	"<img src='".SITE_URL."/images/uncheck-checkbox.jpg'>";
						$financingHudText		=	"<img src='".SITE_URL."/images/uncheck-checkbox.jpg'>";
						$nonUadText				=	"<img src='".SITE_URL."/images/uncheck-checkbox.jpg'>";
						if($reoForm				==	1)
						{
							$reoFromText		=	"<img src='".SITE_URL."/images/check-checkbox.jpg'>";
						}
						if($financingVa			==	1)
						{
							$financingVaText	=	"<img src='".SITE_URL."/images/check-checkbox.jpg'>";
						}
						if($financingFha		==	1)
						{
							$financingFhaText	=	"<img src='".SITE_URL."/images/check-checkbox.jpg'>";
						}
						if($financingHud		==	1)
						{
							$financingHudText	=	"<img src='".SITE_URL."/images/check-checkbox.jpg'>";
						}
						if($nonUad				==	1)
						{
							$nonUadText			=	"<img src='".SITE_URL."/images/check-checkbox.jpg'>";
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
						<td class="smalltext23" valign='top'>Financing</td>
						<td class="smalltext23" valign='top'>:</td>
						<td valign='top'>
							<table width="100%">
								<tr>
									<td width="5%" class="smalltext24">
										VA : 
									</td>
									<td width="5%">
										 <?php echo $financingVaText;?>
									</td>
									<td width="5%" class="smalltext24">
										FHA : 
									</td>
									<td width="5%">
										 <?php echo $financingFhaText;?>
									</td>
									<td width="6%" class="smalltext24">
										HUD : 
									</td>
									<td>
										 <?php echo $financingHudText;?>
									</td>
								</tr>
							</table>
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
											include(SITE_ROOT_EMPLOYEES."/includes/display-multiple-files1.php");
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
												<?php 
												    if(!empty($instructions)){
													  echo makeLinkFromText(nl2br($instructions));
													}
													else{
														echo "N/A";
													}
												?>
											</td>
										</tr>
									</table>
								</div>
							</td>
						</tr>
						<?php
							if($islacarteOrder  == 1 && !empty($a_checkedLaCartePrice) && count($a_checkedLaCartePrice) > 0){

														
						?>
						<tr>
							<td class="smalltext23" valign="top">Process With Only</td>
							<td class="smalltext23" valign="top">:</td>
							<td valign="top">								
								<table width="100%"  cellpadding="3" cellspacing="3">
									<?php
										$countProcess   = 0;
										foreach($a_allLaCartePrices as $k=>$v){
											$countProcess++;

											list($price,$priceText) = explode("|",$v);

											$dynamicCheckbox = "<img src='".SITE_URL."/images/uncheck-checkbox.jpg'>";
											 $dontChangeText  = "&nbsp;<font style='color:#ff0000'>(DONOT CHANGE)</font>";

											if(in_array($k,$a_checkedLaCartePrice)){
												$dynamicCheckbox = "<img src='".SITE_URL."/images/check-checkbox.jpg'>";
												$dontChangeText  = "";
											}

									?>
									<tr>
										<td width="3%" class="smalltext24"><?php echo $dynamicCheckbox;?></td>
										<td class="smalltext24"><?php echo $priceText.$dontChangeText;?></td>
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
								if(mysqli_num_rows($result))
								{
									$row			=    mysqli_fetch_assoc($result);
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
						/*if($customerId	== 6 || $s_employeeId == 3){
							include(SITE_ROOT_EMPLOYEES	. "/includes/view-reply-details2.php");
						}
						else{
							include(SITE_ROOT_EMPLOYEES	. "/includes/view-reply-details1.php");
						}*/

						include(SITE_ROOT_EMPLOYEES	. "/includes/view-reply-details2.php");
						
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
			//////////////////////////////// ENDING OF DISPLAYING ORDER DEATILS ////////////////
			************************************************************************************
			************************************************************************************
			///////////////////////////DISPLAYING INHOUSE ORRDER FILES ////////////////////////
			************************************************************************************--> 
			<div id="showingOrderDetailsFor9" style="display:<?php echo $displayHideMainTabs9;?>">
				<?php					
					include(SITE_ROOT_EMPLOYEES	. "/includes/order-in-house-history.php");
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
					<tr>
						<td colspan="4"><a onclick="sendingNewGeneralMsg(<?php echo $customerId;?>);" class='link_style32' style='cursor:pointer;'><b>SEND GENERAL EMAIL</b></a></td>
					</tr>
					<?php
						
						$instructionQuery = "SELECT * FROM customer_instructions_file WHERE memberId=$customerId AND uploadedBy='".CUSTOMERS."' ORDER BY instructionId  DESC";
						$instructionsResult=	dbQuery($instructionQuery);

						if(!empty($splInstructionToEmployee) || mysqli_num_rows($instructionsResult))
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
					    ?>
						<tr>
							<td colspan="3" class="smalltext24">
								<b><font color='#ff0000;'>STANDARD INSTRUCTIONS </font></b>&nbsp;<?php echo $newDiffImage."&nbsp;(On - ".showDateTimeFormat($instructionsUpdatedOn,'00:00:01').")";?>
							</td>
						</tr>
						<?php
							}
							
						?>
						
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
							if(!empty($easyNQuickInstructionsDone) && $easyNQuickInstructionsDone == "yes")
							{
						?>
						<tr>
							<td class="smalltext24">EasyNQuick instructions Done : <b><?php echo ucwords($easyNQuickInstructionsDone);?></b></td>
						</tr>
						<?php
							}
							else{
								if(!empty($s_hasManagerAccess))
								{									
									/*$url		=	SITE_URL_EMPLOYEES."/multiple-employee-ajax-task.php?updateQuickCheck=1&customerId=".$customerId;
									$popUpText1	=  "Are you sure to mark as YES?";
									$popUpText2	=  "Are you sure to mark as NO?";*/
									
						?>
						<tr>
							<td class="smalltext24"><form name="changeQuickIns" action="" method="POST">EasyNQuick instructions Done : <b><input type="radio" name="markeQuickCheckDone" value="yes">Yes&nbsp;<!--<input type="radio" name="markeQuickCheckDone" value="no" checked>No&nbsp;--><input type="submit" name="submit" value="Submit"><input type="hidden" name="formQuickInsChnageSubmitted" value="1"></form></td>
						</tr>
						<?php
								}
							}
							if($status == 1 && empty($isReadInstructions) && !empty($acceptedBy)){
						?>
						<tr>
							<td class="smalltext24"><form name="markAsInsRead" action="" method="POST">I read the instructions : <b><input type="radio" name="markInstructionsDone" value="yes">Yes&nbsp;<input type="submit" name="submit" value="Submit"><input type="hidden" name="formInsReadSubmitted" value="1"></form></td>
						</tr>
						<?php
							}
							
							if(mysqli_num_rows($instructionsResult))
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
										while($row	=	mysqli_fetch_assoc($instructionsResult))
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
							<b><font color='#ff0000;'>EMPLOYEE NOTES & FILES FOR THE CUSTOMER</font></b>&nbsp;<a onclick="openNoteWindow(<?php echo $customerId;?>);" class="link_style32" style="cursor:pointer"><b>EDIT</b></a>
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
							if(empty($splInstructionToEmployee) && empty($isReadEmployeeNote)){
					?>
					<tr>
						<td class="smalltext24"  colspan='4'><form name="markAsEmpNoteRead" action="" method="POST">I read employee notes : <b><input type="radio" name="markInternalNoteDone" value="yes">Yes&nbsp;<input type="submit" name="submit" value="Submit"><input type="hidden" name="formEmpNoteReadSubmitted" value="1"></form></td>
					</tr>
					<?php
							}
						}
						else
						{
							echo "<tr><td  class='error' style='text-align:center' colspan='4'><b>No Note Available</td></td></tr>";
						}

						$query			=	"SELECT * FROM customer_instructions_file WHERE memberId=$customerId AND uploadedBy='".EMPLOYEES."' ORDER BY fileName";
						$result			=	dbQuery($query);
						if(mysqli_num_rows($result))
						{
					?>
					<tr>
						<td colspan="4" class="text"><b>EMPLOYEE NOTE FILES</b></td>
					</tr>
					<?php
						$i	=	0;
						while($row	=	mysqli_fetch_assoc($result))
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
						$oneMonthOldDate 			=  date('Y-m-d', strtotime("-30 days", strtotime($nowDateIndia)));

						////////////////////IF ORDER IS 1 MONTH OLD THAN WE ARE NOT DISPAYING THE ORDER/// MESSAGES TO EMPLOYEES. IT WAS DECIDED BY HEMANT AND ME ON CALL ///////////////////////////////////////////////////////////////////////////////////////////////////////

						if($orderPlacedDate > $oneMonthOldDate){

							$a_messagesDeliverdStatus   =  $orderObj->getOrderMessagesDeliveryStatus($orderId,$customerId);
							
							if($result	=	$orderObj->getOrderMessages($orderId,$customerId))
							{	
								while($row					=	mysqli_fetch_assoc($result))
								{
									$isDisplayZipFile		=	0;
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
									$messageReplyByEmployee =	$row['employeeId'];
									$repliedOrMessageBy 	=	$row['fullName']; 

									if(isset($a_allDeactivatedEmployees) && !empty($a_allDeactivatedEmployees) && array_key_exists($messageReplyByEmployee,$a_allDeactivatedEmployees)){
										$repliedOrMessageBy=  "Hemant Jindal";
									}

									$smsSentText			=	"";
									if(!empty($selectedSmsId))
									{
										$smsDeliveryNote    =   "";
										if(!empty($a_messagesDeliverdStatus) && count($a_messagesDeliverdStatus) > 0 && array_key_exists($selectedSmsId,$a_messagesDeliverdStatus)){
											$smsDeliveryNote= $a_messagesDeliverdStatus[$selectedSmsId];
										}
										$smsSentText		=	"&nbsp;[<font color='#ff0000;'>Also sent SMS</font>".$smsDeliveryNote."]";
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
											$repliedText	=	"&nbsp;[Replied By : ".$repliedOrMessageBy." at ".showDateTimeFormat($msgRepliedOn,$msgRepliedTime);"]";
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
										//echo "<tr><td class='smalltext1'><b>".$repliedOrMessageBy." at ".$daysAgo."</b><br />".$notifyText.$smsSentText."</td><td></tr>";

										if(!empty($emailUniqueCode)){
											if($readDateIp		=	$employeeObj->getFirstEmailReadTime($emailUniqueCode))
											{
												list($readDate,$readTime)	=	explode("|",$readDateIp);
												$readDateTime  =  showDateTimeFormat($readDate,$readTime,1);	

												$readEmailText =	"&nbsp;(<font color='#ff0000'>Customer Read at </font>".$readDateTime.")";
											}
										}
									}
									elseif($messageBy   ==  CUSTOMERS)
									{
										echo "<tr><td class='smalltext1'><b>".$customerName." at ".$daysAgo."</b>".$repliedText."</td></tr>";
									}
									echo "<tr><td class='smalltext2' style='margin-top:0px;line-height:16px;'>";
									
									echo $displayEmpCustMsg.$readEmailText;
									
									echo "</td></tr>";
									if(!empty($emailSubject)){
										echo "<tr><td class='smalltext2'>Sub:<u><b>".removedSpecialCharacters($emailSubject)."</b></u></td></tr>";
									}
									if($hasMessageFiles == 1 && empty($isDeleted))
									{
										
										if($isNewUploadingSystem == 1)
										{
											if($result1			=	$orderObj->getOrdereMessageFile($orderId,$t_messageId,3,7))
											{
												echo "<tr><td colspan='2' valign='top'><table width='100%' align='left'><tr><td width='12%' class='smalltext2' valign='top'><b>Uploaded File : </b></td><td valign='top'><table width='100%' align='left'>";
												$countTotal			=	0;

												while($row1			=	mysqli_fetch_assoc($result1))
												{
													$countTotal++;
													if($countTotal	> 3){
														$isDisplayZipFile	=	1;
													}
													$fileId			=	$row1['fileId'];
													$fileName		=	stripslashes($row1['uploadingFileName']);
													$fileExtension	=	$row1['uploadingFileExt'];
													$fileSize		=	$row1['uploadingFileSize'];
													$imageOnServerPath	=	$row1['excatFileNameInServer'];
													$imageOnServerPath  =   stringReplace("/home/ieimpact", "", $imageOnServerPath);

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
													<br />
													(<a onclick="viewBiggerImage(<?php echo $customerId;?>,<?php echo $orderId;?>,<?php echo $t_messageId;?>,1)" style="cursor:pointer">View Large</a>)
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
												<br />
												(<a onclick="viewBiggerImage(<?php echo $customerId;?>,<?php echo $orderId;?>,<?php echo $t_messageId;?>,0)" style="cursor:pointer">View Large</a>)
										<?php
											}
											echo "</td></tr>";
										}
										if($isDisplayZipFile == 1){
																							
											$messageFiledownLoadPath	=	SITE_URL_EMPLOYEES."/download-all-message-files.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".base64_encode($t_messageId);
									?>
											<!--<tr>
												<td colspan="2" align="left">									
													(<a class="link_style13" onclick="downloadMultipleOrderFile('<?php echo $messageFiledownLoadPath;?>');" title="Download Message File as ZIP" style="cursor:pointer;"><b>Download Message All Files As .zip</b></a>)
												</td>
											</tr>-->
									<?php
											
										}
									}
									
								}
								
							}
							else
							{
								echo "<tr><td height='50' style='text-align:center'><font style='font-size:16px;font-family:verdana;color:#ff0000;font-weight:bold'>No Message Available</font></td></tr>";
							}
						}
						else{
							echo "<tr><td height='50' style='text-align:center'><font style='font-size:16px;font-family:verdana;color:#ff0000;font-weight:bold'>No Message Available</font></td></tr>";
						}
					?>
				</table>
			<?php
				$query	=	"SELECT order_attention_messages.*,fullName FROM order_attention_messages LEFT JOIN employee_details ON order_attention_messages.employeeId=employee_details.employeeId WHERE memberId=$customerId AND orderId=$orderId ORDER BY date,time";
				$result	=	dbQuery($query);
				if(mysqli_num_rows($result))
				{
					
			?>
			<br>
				<table width='98%' align='center' cellpadding='3' cellspacing='2' border='0'>
					<tr>
						<td colspan="2" class="heading3"><b>ATTENTION MESSAGE TO CUSTOMER</b></td>
					</tr>
					<tr>
						<td colspan="2" height="5"></td>
					</tr>
				<?php
						$at					 =	0;
						while($row			 =	mysqli_fetch_assoc($result))
						{
							$at++;
							$attentionId	 =	$row['messageId'];
							$attentionMessage=	stripslashes($row['message']);
							$attentionDate	 =	$row['date'];
							$attentionTime	 =	$row['time'];
							$attentionBy	 =	$row['employeeId'];
							$emailUniqueCode =	$row['emailUniqueCode'];
							$isSentSMS		 =	$row['isSentSMS'];
							$sentSMSId		 =	$row['smsId'];
							$hasFile 		 =  $row['hasFile'];
							$attentionBy	 =	stripslashes($row['fullName']);

							$daysAgoAttention=	showDateTimeFormat($attentionDate,$attentionTime);

							$smsSentAttention=	"";
							if(!empty($isSentSMS))
							{
								
								$attentionMesgDeliveryNote    =    "<font color='green'> and successfully delivered.</font>";
								
									
								if(!empty($a_messagesDeliverdStatus) && count($a_messagesDeliverdStatus) > 0 && array_key_exists($sentSMSId,$a_messagesDeliverdStatus)){
								   $attentionMesgDeliveryNote= $a_messagesDeliverdStatus[$sentSMSId];
								}
								
								$smsSentAttention=	"&nbsp;[<font color='#ff0000;'>Also sent SMS</font>".$attentionMesgDeliveryNote."]";
							}

							$readEmailText	 =	"";
							if(!empty($emailUniqueCode)){
								if($readDateIp	 =	$employeeObj->getFirstEmailReadTime($emailUniqueCode))
								{
									list($readDate,$readTime)	=	explode("|",$readDateIp);
									$readEmailText =	"&nbsp;(<font color='#ff0000'>Customer Read At - ".showDate($readDate)." EST at - ".showTimeFormat($readTime)." Hrs</font>)";
								}
							}
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
								$hasFile 	== 1;
								if($hasFile == 1){
									$query2			=	"SELECT * FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND orderId=$orderId AND uploadingType=8 AND uploadingFor=3 AND isDeleted=0 AND orderAttentionMsgId=$attentionId";
									$result2			=	dbQuery($query2);
									if(mysqli_num_rows($result2))
									{
									
										echo "<tr><td colspan='2' valign='top'><table width='100%' align='left'><tr><td width='12%' class='smalltext2' valign='top'><b>Uploaded File : </b></td><td valign='top'><table width='100%' align='left'>";
										$countTotal			=	0;

										while($row2			=	mysqli_fetch_assoc($result2))
										{
											$countTotal++;
											if($countTotal	> 3){
												$isDisplayZipFile	=	1;
											}
											$fileId			    =	$row2['fileId'];
											$fileName		    =	stripslashes($row2['uploadingFileName']);
											$fileExtension	    =	$row2['uploadingFileExt'];
											$fileSize		    =	$row2['uploadingFileSize'];
											$imageOnServerPath	=	$row2['excatFileNameInServer'];
											$imageOnServerPath  =   stringReplace("/home/ieimpact", "", $imageOnServerPath);

											$base_fileId	    =	base64_encode($fileId);
											
											$downLoadPath	    =	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
										?>
										<tr>
											 <td>
												<a class="link_style32" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download Message File" style="cursor:pointer;"><?php echo $fileName.".".$fileExtension;?></a>&nbsp;&nbsp;<font class='smalltext20'><?php echo getFileSize($fileSize);?></font>
											<?php
												if(in_array($fileExtension,$a_displayAnyImageOfType) && $fileSize <= "3145728"){
											
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
												<br><img src="<?php echo SITE_URL_EMPLOYEES;?>/get-any-file-image.php?fileId=<?php echo $fileId;?>" border="0" title="<?php echo $fileName.".".$fileExtension;?>" <?php echo $imageWidth;?> <?php echo $imageHeight;?> onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');"  style="cursor:pointer">
												<br />
												(<a onclick="viewAnyFileBiggerImage(<?php echo $fileId;?>)" style="cursor:pointer">View Large</a>)
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
				<?php					
					include(SITE_ROOT_EMPLOYEES	. "/includes/employee-customer-message-history.php");
				?>
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
					if($result	=	$orderObj->previousOrdersCustomerRatingComments($orderId,$customerId,10))
					{
				?>
					<tr>
						<td width="3%">&nbsp;</td>
						<td width="35%" class="smalltext23"><b>Order No</b></td>
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
						while($ratingCustomerRow=	mysqli_fetch_assoc($result))
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
								<td valign="top"><a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $cr_orderId;?>&customerId=<?php echo $customerId;?>" class='link_style32'><?php echo getSubstring($cr_orderName,50);?></a><br><?php echo $rateMessageImage;?></td>
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
							while($row					=	mysqli_fetch_assoc($result))
							{
								$count2++;
								$t_empMessageId			=	$row['messageId'];
								$t_empMessage			=	stripslashes($row['message']);
								$t_empMessageAddedOn	=	showDate($row['addedOn']);
								$t_empMessageBy			=	$row['messageBy'];
								$t_addedOn				=	$row['addedOn'];
								$t_empMessageByName		=	stripslashes($row['fullName']);

								if(isset($a_allDeactivatedEmployees) && !empty($a_allDeactivatedEmployees) && array_key_exists($t_empMessageBy,$a_allDeactivatedEmployees)){
									$t_empMessageByName=  "Hemant Jindal";
								}

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
							while($emessageRow		=	mysqli_fetch_assoc($result))
							{
								$emessageCount++;
								$e_messageId		=  $emessageRow['messageId'];
								$e_orderId			=  $emessageRow['messageFor'];
								$e_message			=  stripslashes($emessageRow['message']);
								$e_message			=  nl2br($e_message);
								$e_address			=  stripslashes($emessageRow['orderAddress']);
								$e_messageBy		=  $emessageRow['messageBy'];
								$e_addedOn			=  $emessageRow['addedOn'];
								$orderAddedOn		=  $emessageRow['addedOn'];
								$e_messageByName	=  stripslashes($emessageRow['fullName']);
								if(isset($a_allDeactivatedEmployees) && !empty($a_allDeactivatedEmployees) && in_array($e_messageBy,$a_allDeactivatedEmployees)){
								  $e_messageByName	=  "Hemant Jindal";
								}

								$orderAddedOn	= (strtotime(date("Y-m-d")) - strtotime($orderAddedOn)) / (60 * 60 * 24);
								$orderAddedOnImg		=	"";
								if($orderAddedOn <= 3)
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
			
		</td>
	</tr>
</table>
<?php
	if(isset($hasRatingExplanation) && !empty($hasRatingExplanation))
	{
		$query	=	"SELECT * FROM reply_on_orders_rates WHERE orderId=$orderId ORDER BY replyId DESC";
		$result	=	dbQuery($query);
		if(mysqli_num_rows($result))
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
	while($row	= mysqli_fetch_assoc($result))
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

