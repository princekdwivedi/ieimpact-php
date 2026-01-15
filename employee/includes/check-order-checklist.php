<?php
	$checklistError						=	"";
	$checkedId							=	0;
	$checkedBy							=	"";
	$checkedByName						=	"";
	$checkedOn							=	"";
	$checkedOnTime						=	"";
	$checkedMessage						=	"";
	$checkedByName						=	"";
	$a_displayCheckresultValue			=	array();
	$a_checkedValueText					=	array("1"=>"<font color='#58B000'>Yes</font>","2"=>"<font color='#ff0000'>No</font>","3"=>"<font color='#FF7575'>Not Req.</font>");

	$query2								=	"SELECT * FROM checked_customer_orders WHERE orderId=$orderId";
	$result								=	dbQuery($query2);
	if(mysqli_num_rows($result))
	{
		$row							=	mysqli_fetch_assoc($result);
		$checkedId						=	$row['checkedId'];
		$checkedBy						=	$row['checkedBy'];
		$checkedOn						=	$row['checkedOn'];
		$checkedOnTime					=	$row['checkedOnTime'];
		$checkedMessage					=	stripslashes($row['checkedMessage']);
		$hasSubjectPublecRecords		=	$row['hasSubjectPublecRecords'];
		$hasSubjectMls					=	$row['hasSubjectMls'];
		$hasSubjectInspectionSheet		=	$row['hasSubjectInspectionSheet'];
		$hasRoughSketch					=	$row['hasRoughSketch'];
		$has100Mc						=	$row['has100Mc'];
		$hasCompsPulicRecords			=	$row['hasCompsPulicRecords'];
		$hasComsMlsData					=	$row['hasComsMlsData'];
		$hasClientOrderInfo				=	$row['hasClientOrderInfo'];
		$hasListingComps				=	$row['hasListingComps'];
		$hasClonedTemplateFile			=	$row['hasClonedTemplateFile'];
		$checkedCompsFiles				=	$row['checkedCompsFiles'];
		$checkEmployeeNotes				=	$row['checkEmployeeNotes'];


		if(empty($hasSubjectPublecRecords))
		{
			$hasSubjectPublecRecords	=	3;
		}
		if(empty($hasSubjectMls))
		{
			$hasSubjectMls				=	3;
		}
		if(empty($hasSubjectInspectionSheet))
		{
			$hasSubjectInspectionSheet	=	3;
		}
		if(empty($hasRoughSketch))
		{
			$hasRoughSketch				=	3;
		}
		if(empty($has100Mc))
		{
			$has100Mc					=	3;
		}
		if(empty($hasCompsPulicRecords))
		{
			$hasCompsPulicRecords		=	3;
		}
		if(empty($hasComsMlsData))
		{
			$hasComsMlsData				=	3;
		}
		if(empty($hasClientOrderInfo))
		{
			$hasClientOrderInfo			=	3;
		}
		if(empty($hasListingComps))
		{
			$hasListingComps			=	3;
		}
		if(empty($hasClonedTemplateFile))
		{
			$hasClonedTemplateFile		=	3;
		}

		$a_displayCheckresultValue[1]	=	$a_checkedValueText[$hasSubjectPublecRecords];
		$a_displayCheckresultValue[2]	=	$a_checkedValueText[$hasSubjectMls];
		$a_displayCheckresultValue[3]	=	$a_checkedValueText[$hasSubjectInspectionSheet];
		$a_displayCheckresultValue[4]	=	$a_checkedValueText[$hasRoughSketch];
		$a_displayCheckresultValue[5]	=	$a_checkedValueText[$has100Mc];
		$a_displayCheckresultValue[6]	=	$a_checkedValueText[$hasCompsPulicRecords];
		$a_displayCheckresultValue[7]	=	$a_checkedValueText[$hasComsMlsData];
		$a_displayCheckresultValue[8]	=	$a_checkedValueText[$hasClientOrderInfo];
		$a_displayCheckresultValue[9]	=	$a_checkedValueText[$hasListingComps];
		$a_displayCheckresultValue[10]	=	$a_checkedValueText[$hasClonedTemplateFile];

		if($isSetedOrderField			==	1)
		{
			$checkedByName				=	$orderCheckedBy;
		}
		else
		{			
			$checkedByName				=	$employeeObj->getEmployeeName($checkedBy);
			if(in_array($checkedByName,$a_allDeactivatedEmployees) && array_key_exists($checkedBy,$a_allDeactivatedEmployees)){
			  	 $checkedByName         = "Hemant Jindal";
			}
		}
	}

	if(isset($_REQUEST['checkFormSubmitted']))
	{
		extract($_REQUEST);

		$checkedCompsFiles				=	trim($checkedCompsFiles);
		$checkEmployeeNotes				=	trim($checkEmployeeNotes);

		if(!is_numeric($checkedCompsFiles)){
			$checkedCompsFiles          =   "";
		}

		if(isset($_POST['readFileChecklist']) && !empty($checkedCompsFiles) && !empty($checkEmployeeNotes))
		{
			$a_readChecklist	  =	$_POST['readFileChecklist'];
			$totalListChecked	  =	count($a_readChecklist);
			if($totalListChecked !=	10)
			{
				$checklistError	  =	"Please complete the received data checklist.";
			}
		}
		else
		{
			$checklistError		   =	"Please complete the received data checklist and number of comps and internal employee notes.";
		}

		if(isset($_POST['markedChecklistSendSms']))
		{
			$markedChecklistSendSms = $_POST['markedChecklistSendSms'];
		}
		else
		{
			$markedChecklistSendSms = 0;
		}

		if(empty($checklistError))
		{
			$sendingChecklistEmail	=	false;
			$a_choiceSelected		=	array();
			$a_choiceEmailText		=	array();
			$a_choiceEmailSelected	=	array();
			$a_dataNotReceived		=	array();

			foreach($a_readChecklist as $key=>$value)
			{
				list($operation,$listingId)			=	explode("|",$value);
				$operationText						=	$a_checkedValueText[$operation];

				$listingChoice						=	$a_checklistFirstOrderCheck[$listingId];

				list($listName,$dbNameList)			=	explode("|",$listingChoice);

				$listNameSub						=	$listName;

				//$listNameSub						=	stringReplace("Subject ","",$listNameSub);
				//$listNameSub						=	stringReplace("Comps ","",$listNameSub);
				
				if($operation						==	2)
				{
					$sendingChecklistEmail			=	true;
					$a_dataNotReceived[]			=	$listNameSub;
				}				

				$a_choiceSelected[]					=	$dbNameList."=".$operation;
				$a_choiceEmailSelected[$listingId]	=	$operationText;
			}
			$columns=	implode(",",$a_choiceSelected);
			$checkEmployeeNotes		=	makeDBSafe($checkEmployeeNotes);

			
			$query	=	"INSERT INTO checked_customer_orders SET checkedBy=$s_employeeId,orderId=$orderId,checkedOn='".CURRENT_DATE_INDIA."',checkedOnTime='".CURRENT_TIME_INDIA."',checkedIP='".VISITOR_IP_ADDRESS."',checkedCompsFiles=$checkedCompsFiles ,checkEmployeeNotes='$checkEmployeeNotes',".$columns;
			dbQuery($query);

			dbQuery("UPDATE members_orders SET isOrderChecked=1,orderCheckedBy='$s_employeeName' WHERE orderId=$orderId");

			$employeeObj->updateEmployeeTotalChecked($s_employeeId,$s_employeeName,CURRENT_DATE_INDIA);

			$orderObj->deductOrderRelatedCounts('uncheckedOrders');

			$performedTask	=	"Check order checklist of Order - ".$orderAddress;
				
			$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);

			$orderObj->deductOrderRelatedCounts('uncheckedOrders');

			////////////////////////////////////////////////////////////////////////////////////////
			//////////////////// PUTTING THE ORDER IN ORDER TRACK LIST ////////////////////////////
		    $orderObj->addOrderTracker($s_employeeId,$orderId,$orderAddress,'Employee check checklist','EMPLOYEE_CHECK_CHECKLIST');
		    ////////////////////////////////////////////////////////////////////////////////////////
		    ////////////////////////////////////////////////////////////////////////////////////////

			if($sendingChecklistEmail	==	true)
			{
				/////////////////// START OF SENDING EMAIL BLOCK/////////////////////////
				include(SITE_ROOT		.   "/classes/email-templates.php");
				$emailObj				=	new emails();

				$a_dataNotReceived1		=	implode(",",$a_dataNotReceived);
				$a_dataNotReceived2		=	implode(", and ",$a_dataNotReceived);
				$subjectMsg				=	"Data Not Received:".$a_dataNotReceived1." for your Order#".$orderAddress;
				$a_templateSubject		=	array("{dynmaicSubject}"=>$subjectMsg);

				$addingOrderMessage		=	"We did not receive ".$a_dataNotReceived2." for this order. Please send this data ASAP so that we can start working on your order ASAP.";

				$query12				=	"INSERT INTO members_employee_messages SET orderId=$orderId,memberId=$customerId,employeeId=$s_employeeId,message='$addingOrderMessage',parentId=0,addedOn='$nowDateIndia',addedTime='$nowTimeIndia',messageBy='".EMPLOYEES."',estDate='".CURRENT_DATE_CUSTOMER_ZONE."',estTime='".CURRENT_TIME_CUSTOMER_ZONE."',isShownPopUp=0";
				dbQuery($query12);

				$lastInsertedMsgId		=	mysqli_insert_id($db_conn);

				$filesUploaded			=	"";
				if(!empty($a_customerOrderTemplateFiles))
				{
					$filesUploaded	   .=	"<table width='98%' align='center' border='0' cellpadding='2' cellspacing='0'>";
					foreach($a_customerOrderTemplateFiles as $key=>$uploadedFileName)
					{
						$filesUploaded .=	"<tr><td valign='top'><font size='2px' face='verdana' color='#787878'>".$uploadedFileName."</font></td></tr>";
					}

					$filesUploaded	   .=	"</table>";
				}

				$yesNoSPR				=	$a_choiceEmailSelected[1];
				$yesNoSMLS				=	$a_choiceEmailSelected[2];
				$yesNoSIS				=	$a_choiceEmailSelected[3];
				$yesNoRS				=	$a_choiceEmailSelected[4];
				$yesNo100MC				=	$a_choiceEmailSelected[5];
				$yesNoCPR				=	$a_choiceEmailSelected[6];
				$yesNoCMLS				=	$a_choiceEmailSelected[7];
				$yesNoCOI				=	$a_choiceEmailSelected[8];
				$yesNoLC				=	$a_choiceEmailSelected[9];
				$yesNoCTF				=	$a_choiceEmailSelected[10];

				$sentByEmployee			=	 "<b>Customer Name : </b>".$customerName." and <b>Checked by :</b> ".$s_employeeName." at ".showDateFullText(CURRENT_DATE_INDIA)." ".showTimeShortFormat(CURRENT_TIME_INDIA)." IST";

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

				$quickReplyToEmail      = "<a href='mailto:".$setThisEmailReplyToo."'>".$setThisEmailReplyToo."</a>";
			
				$a_templateData			=	array("{name}"=>$customerName,"{orderAddress}"=>$orderAddress,"{orderType}"=>$orderText,"{notReceived}"=>$a_dataNotReceived2,"{filesUploaded}"=>$filesUploaded,"{checklistCheckedBy}"=>"","{yesNoSPR}"=>$yesNoSPR,"{yesNoSMLS}"=>$yesNoSMLS,"{yesNoSIS}"=>$yesNoSIS,"{yesNoRS}"=>$yesNoRS,"{yesNo100MC}"=>$yesNo100MC,"{yesNoCPR}"=>$yesNoCPR,"{yesNoCMLS}"=>$yesNoCMLS,"{yesNoCOI}"=>$yesNoCOI,"{yesNoLC}"=>$yesNoLC,"{yesNoCTF}"=>$yesNoCTF,"{quickReplyToEmail}"=>$quickReplyToEmail);

				$uniqueTemplateName		=	"TEMPLATE_SENDING_CUSTOMER_ORDER_CHECKLIST";
				$toEmail				=	$customerEmail;
				include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

				$setThisEmailReplyToo			=	"";//Setting for reply to make empty to manager
				$setThisEmailReplyTooName		=	"";//Setting for reply to make empty to manager

				$a_templateData		=	array("{name}"=>$customerName,"{orderAddress}"=>$orderAddress,"{orderType}"=>$orderText,"{notReceived}"=>$a_dataNotReceived2,"{filesUploaded}"=>$filesUploaded,"{checklistCheckedBy}"=>$sentByEmployee,"{yesNoSPR}"=>$yesNoSPR,"{yesNoSMLS}"=>$yesNoSMLS,"{yesNoSIS}"=>$yesNoSIS,"{yesNoRS}"=>$yesNoRS,"{yesNo100MC}"=>$yesNo100MC,"{yesNoCPR}"=>$yesNoCPR,"{yesNoCMLS}"=>$yesNoCMLS,"{yesNoCOI}"=>$yesNoCOI,"{yesNoLC}"=>$yesNoLC,"{yesNoCTF}"=>$yesNoCTF,"{quickReplyToEmail}"=>$quickReplyToEmail);

				$uniqueTemplateName	=	"TEMPLATE_SENDING_CUSTOMER_ORDER_CHECKLIST";
				$toEmail			=	"hemant@ieimpact.net";
				//$toEmail			=	"gaurabsiva1@gmail.com";
				include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

				//Sending Checklist not found SMS to customer
				if($markedChecklistSendSms	   ==	1)
				{
					//if($customerId ==  	3580){
						$toPhone           =   "+".$smsCustomerMobileNo;
						$displaySmsOrderNo =   subString($orderAddress,10);
						$smsMessage		   =   "MSG from ieIMPACT : We did not receive ".$a_dataNotReceived1." for your Order - ".$orderAddress.". Please send this data ASAP, otherwise we will try to complete this order without this data.";
						$messageId 	       =  $lastInsertedMsgId;
						include(SITE_ROOT_EMPLOYEES .  "/includes/sending-sms-customer.php");
					/*}
					else{

						try{
							$displaySmsOrderNo =   subString($orderAddress,10);
							$smsMessage		   =	"MSG from ieIMPACT : We did not receive ".$a_dataNotReceived1." for your Order - ".$orderAddress.". Please send this data ASAP, otherwise we will try to complete this order without this data.";

							$smsMessage		   =	stringReplace("<br>", " ", $smsMessage);
							$smsMessage		   =	stringReplace("</ br>", " ", $smsMessage);
							$smsMessage		   =	stringReplace("</ br>", " ", $smsMessage);
							$smsReferenceID	   =    $orderId."-".rand(11,99)."-".substr(md5(microtime()+rand()+date('s')),0,5);

							$smsReturnPath	   =	"https://secure.ieimpact.com/read-sms-postback.php"; 

							$smsKey			   =	SMS_CDYNE_KEY;
							$client			   =	new SoapClient('http://sms2.cdyne.com/sms.svc?wsdl');
						
							$lk				   =	$smsKey;

							class AdvancedCallRequestData
							{
							  public $AdvancedRequest;
							 
							  function AdvancedCallRequestData($licensekey,$requests)
							  { 
								$this->AdvancedRequest = array();
								$this->AdvancedRequest['LicenseKey'] = $licensekey;
								$this->AdvancedRequest['SMSRequests'] = $requests;
							  }
							}
							 
							$PhoneNumbersArray1=    array($smsCustomerMobileNo);
											 
							$RequestArray = array(
								array(
									'AssignedDID'=>'',
														  //If you have a Dedicated Line, you would assign it here.
									'Message'=>$smsMessage,   
									'PhoneNumbers'=>$PhoneNumbersArray1,
									'ReferenceID'=>$smsReferenceID,
														  //User defined reference, set a reference and use it with other SMS functions.
									//'ScheduledDateTime'=>'2010-05-06T16:06:00Z',
														  //This must be a UTC time.  Only Necessary if you want the message to send at a later time.
									'StatusPostBackURL'=>$smsReturnPath 
														  //Your Post Back URL for responses.
								)
							);
							 
							$request		=   new AdvancedCallRequestData($smsKey,$RequestArray);
							//pr($request);
							$result			=   $client->AdvancedSMSsend($request);
							//pr($request);
							$result1		=	convertObjectToArray($result);
							//pr($result1);
							$mainResult	    =	$result1['AdvancedSMSsendResult'];
							$a_mainSmsResult=	$mainResult['SMSResponse'];
							//pr($a_mainSmsResult);
							$cancelled		=	$a_mainSmsResult['Cancelled'];
							if(empty($cancelled))
							{
								$cancelled	=	"";
							}
							$smsMessageID	=	$a_mainSmsResult['MessageID'];
							if(empty($smsMessageID))
							{
								$smsMessageID	=	"";
							}
							$smsReferenceID	=	$a_mainSmsResult['ReferenceID'];
							if(empty($smsReferenceID))
							{
								$smsReferenceID	=	"";
							}
							$queued			=	$a_mainSmsResult['Queued'];
							if(empty($queued))
							{
								$queued		=	"";
							}
							$smsError		=	$a_mainSmsResult['SMSError'];
							if(empty($smsError))
							{
								$smsError	=	"";
							}

							$smsMessage		=	addslashes($smsMessage);

							$newSmsID= $orderObj->addOrderMessageSms($cancelled,$smsReferenceID,$orderId,$customerId,$s_employeeId,$smsMessageID,$queued,$smsError,$smsMessage,$smsCustomerMobileNo);

							dbQuery("UPDATE members_employee_messages SET isFromSms=1,smsId=$newSmsID WHERE orderId=$orderId AND memberId=$customerId AND messageId=$lastInsertedMsgId");
						}
						catch(Exception $e){
							//$error = $e->getMessage();
							//die($error);
						}
					}*/
				}

			}
						
			ob_clean();
			header("Location: ".SITE_URL."/".$pageUrl."?orderId=$orderId&customerId=$customerId");
			exit();
		}
	}
	
	$formCheckreply				=	SITE_ROOT_EMPLOYEES."/forms/make-order-checklist.php";

	if(empty($checkedId))
	{
		include($formCheckreply);
	}
	else
	{
?>
<table align='left' cellpadding='2' cellspacing='2' border='0' width="98%">
	<tr>
		<td colspan="4" class="smalltext23"><b>Received Data Checklist</b></td>
	</tr>
	<tr>
	<?php
		$countList	=	0;
		$countList1	=	0;

		foreach($a_checklistFirstOrderCheck as $listId=>$v)
		{
			list($listName,$dbNameList)	=	explode("|",$v);
			$countList++;
			$countList1++;
			$displayYesNoValue	=	$a_displayCheckresultValue[$listId];
		
	?>
	<td width="25%">
	   <table align='left' cellpadding='1' cellspacing='1' border='0' width="100%">
			<tr>
				<td width="10%" class="smalltext2"><?php echo $countList;?> )</td>
				<td class="smalltext2"><?php echo $listName;?>&nbsp;(<b><?php echo $displayYesNoValue;?></b>)</td>
			</tr>
		</table>
	</td>
	<?php
			if($countList1 == 4){
				echo "</tr><tr>";
				$countList1 =0;
			}
		}
	?>
	</tr>
</table>
<table align='left' cellpadding='2' cellspacing='2' border='0' width="98%">
	<tr>
		<td class="smalltext23" valign="top" width="19%">Number of Comps Sent</td>
		<td class="smalltext23" valign="top" width="1%">:</td>
		<td class="smalltext24"><?php echo $checkedCompsFiles;?></td>
	</tr>
	<tr>
		<td class="smalltext23" valign="top">Internal Employee Notes</td>
		<td class="smalltext23" valign="top">:</td>
		<td class="smalltext24" colspan=""><?php echo $checkEmployeeNotes;?></td>
	</tr>
	<tr>
		<td class="smalltext23">Checked By</td>
		<td class="smalltext23">:</td>
		<td class="smalltext24"><?php echo $checkedByName;?> on <?php echo showDateTimeFormat($checkedOn,$checkedOnTime);?></td>
	</tr>
	<?php
		if(!empty($checkedMessage))	
		{
	?>
	<tr>
		<td class="smalltext23" valign="top">Comments</td>
		<td class="smalltext23" valign="top">:</td>
		<td class="smalltext24"><?php echo $checkedMessage;?></td>
	</tr>
	<?php
		}	
	?>

</table>
<?php
	}
?>

