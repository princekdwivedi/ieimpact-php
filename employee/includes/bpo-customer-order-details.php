<?php
	$pageUrl	=	$_SERVER['SCRIPT_NAME'];
	//$pageUrl	=	str_replace("/ieimpact","",$pageUrl);
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
	$a_existingRatings	=	$orderObj->getFeedbackText();
	if($result			=	$orderObj->getOrderDetails($orderId,$customerId))
	{
		$row					=	mysql_fetch_assoc($result);
		$customerId				=	$row['memberId'];
		$orderAddress			=	stripslashes($row['orderAddress']);
		$orderType				=	$row['orderType'];
		$instructions			=	stripslashes($row['instructions']);
		$hasOrderFile			=	$row['hasOrderFile'];
		$orderFileExt			=	$row['orderFileExt'];
		$hasPublicRecordFile	=	$row['hasPublicRecordFile'];
		$publicRecordFileExt	=	$row['publicRecordFileExt'];
		$hasMlsFile				=	$row['hasMlsFile'];
		$mlsFileExt				=	$row['mlsFileExt'];
		$hasMarketConditionFile	=	$row['hasMarketConditionFile'];
		$marketConditionExt		=	$row['marketConditionExt'];
		$orderAddedOn			=	showDate($row['orderAddedOn']);
		$assignToEmployee		=	showDate($row['assignToEmployee']);
		$firstName				=	stripslashes($row['firstName']);
		$lastName				=	stripslashes($row['lastName']);
		$dispalyCustomerPhone	=	$row['phone'];
		$customerEmail			=	$row['email'];
		$hasReceiveEmails		=	$row['noEmails'];
		$customerSecondaryEmail	=	$row['secondaryEmail'];
		$folderId				=	$row['folderId'];
		$hasOtherFile			=	$row['hasOtherFile'];
		$otherFileExt			=	$row['otherFileExt'];
		$orderFileName			=	$row['orderFileName'];
		$publicRecordFileName	=	$row['publicRecordFileName'];
		$mlsFileName			=	$row['mlsFileName'];
		$marketConditionFileName=	$row['marketConditionFileName'];
		$otherFileName			=	$row['otherFileName'];

		$orderFileSize			=	$row['orderFileSize'];
		$publicRecordFileSize	=	$row['publicRecordFileSize'];
		$mlsFileSize			=	$row['mlsFileSize'];
		$marketConditionFileSize=	$row['marketConditionFileSize'];
		$otherFileSize			=	$row['otherFileSize'];
		$orderAddedTime			=	$row['orderAddedTime'];
		$appraisalSoftwareType	=	$row['appraisalSoftwareType'];
		$acceptedBy				=	$row['acceptedBy'];
		$status					=	$row['status'];
		$orderCompletedOn		=	showDate($row['orderCompletedOn']);
		$refferedBy				=	$row['refferedBy'];
		$isDeleted				=	$row['isDeleted'];
		$rateGiven				=	$row['rateGiven'];
		$state					=	$row['state'];
		$memberRateMsg			=	stripslashes($row['memberRateMsg']);
		$splInstructionToEmployee=	stripslashes($row['splInstructionToEmployee']);
		$splInstructionOfCustomer=	stripslashes($row['splInstructionOfCustomer']);

		if($orderType			!=	8)
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
			exit();
		}
		else
		{
			$refferedByText	=   "No";
			if($refferedBy  ==  1)
			{
				$refferedByText	=   "Yes";
			}

			$statusText				=	"New Order";
			$acceptedText			=	"Not Yet Accepted";
			$qaDoneByText			=	"";
			

			$qaDoneBy				=	0;
			
			$orderText				=	$a_customerOrder[$orderType];
			$customerName			=   $firstName." ".$lastName;

			$appraisalText			=	$a_appraisalFileTypes[$appraisalSoftwareType];

			$replieddFileToustomer	=	$a_replyCustomerTypeFile[$appraisalSoftwareType];

			if(array_key_exists($state,$a_usaProvinces))
			{
				$stateName		=	$a_usaProvinces[$state];
				list($stateName,$timeZone)	=	explode("|",$stateName);
				$displayZoneTime=	"(".$timeZone.")";
			}
			else
			{
				$displayZoneTime=	"";
			}

			
			$uploadedFileByCustomer	=	$a_uploadedFileBYCustomer[$appraisalSoftwareType];
			if(!empty($acceptedBy))
			{
				$acceptedByName			=   $employeeObj->getEmployeeName($acceptedBy);
			}
			else
			{
				$acceptedByName			=	"";
			}

			if($status	!= 2)
			{
				$orderCompletedOn	=	"";
			}

			if($status == 1)
			{
				$statusText			=	"Accepted";
				$acceptedText		=	$acceptedByName.",On-".$assignToEmployee;
			}

			$hasReplied		=	@mysql_result(dbQuery("SELECT hasRepliedFileUploaded FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId AND hasRepliedFileUploaded=1"),0);
			if(!empty($hasReplied))
			{
				$statusText			=	"QA Pending";
			}

			if($status == 2)
			{
				$statusText			=	"Completed";
				$acceptedText		=	$acceptedByName.",On-".$assignToEmployee;

				$qaDoneBy			=	@mysql_result(dbQuery("SELECT qaDoneBy FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId AND hasQaDone=1"),0);
				if(!empty($qaDoneBy))
				{
					$qaDoneByText		=	$employeeObj->getEmployeeName($qaDoneBy);
				}
				else
				{
					$qaDoneByText		=	"";
				}
			}
			if($status == 3)
			{
				$statusText			=	"Need Attention";
			}

			$customerOrderText	=	"";
			$customerLinkStyle	=	"link_style16";
			$totalCustomerOrders=	$orderObj->getCustomerTotalOrders($customerId);
			if(empty($totalCustomerOrders))
			{
				$totalCustomerOrders	=	0;
			}
			if($totalCustomerOrders < 3)
			{
				$customerOrderText	=	"(New Customer)";
				$customerLinkStyle	=	"link_style17";
			}
			if($totalCustomerOrders >= 3 && $totalCustomerOrders <= 7)
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
		}
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
			$checkedMessage		=	addslashes($checkedMessage);
		}
		else
		{
			$checkedMessage		=	"";
		}

		if($markedAsChecked == 1 && empty($existingCheckId))
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
function openEditCheckMessage(checkedId)
{
	path = "<?php echo SITE_URL_EMPLOYEES;?>/edit-order-checked-message.php?checkedId="+checkedId;
	prop = "toolbar=no,scrollbars=yes,width=450,height=200,top=50,left=100";
	window.open(path,'',prop);
}
function deleteNoteInstructionsFile(instructionId,url,customerId,orderId)
{
	var confirmation = window.confirm("Are You Sure Delete This File?");
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL;?>"+url+"?orderId="+orderId+"&customerId="+customerId+"&instructionId="+instructionId+"&isDeleteInstructions=1";
	}
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
</script>
<table width='98%' align='center' cellpadding='3' cellspacing='3' border='0'>
	<tr>
		<td width="13%" class="smalltext2" valign="top"><b>ORDER NO</b></td>
		<td width="1%" class="smalltext2" valign="top"><b>:</b></td>
		<td width="20%" class="title1" valign="top"><a href="<?php echo SITE_URL_EMPLOYEES;?>/view-bpo-order.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $customerId;?>#action"><?php echo $orderAddress;?></a></td>
		<td width="10%" class="smalltext2" valign="top"><b>ORDER TYPE</b></td>
		<td width="1%" class="smalltext2" valign="top"><b>:</b></td>
		<td width="13%" class="title1" valign="top"><?php echo $orderText;?></td>
		<td width="10%" class="smalltext2" valign="top"><b>CUSTOMER</b></td>
		<td width="1%" class="smalltext2" valign="top"><b>:</b></td>
		<td valign="top"><font size="2"><b>
			<?php 
				echo "<a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?searchOrder=&searchBy=2&serachCustomerId=$customerId' class='$customerLinkStyle'>".ucwords($customerName)."<a><font class='$customerLinkStyle'>".$customerOrderText."</font></a>";
				
				if($dispalyCustomerPhone)
				{
					echo "<br><font class='title2'>Phone : $dispalyCustomerPhone</font>";
				}
				if($customerEmail)
				{
					echo "<br><font class='title2'>Email : $customerEmail</font>";
				}
				if($customerSecondaryEmail)
				{
					echo "<br><font class='title2'>Secondary Email : $customerSecondaryEmail</font>";
				}

			?></b></font></td>
	</tr>
	<tr>
		<td class="smalltext2"><b>ORDER DATE</b></td>
		<td class="smalltext2"><b>:</b></td>
		<td class="title1"><?php echo $orderAddedOn."&nbsp;&nbsp;".$displayZoneTime;?></td>
		<td class="smalltext2"><b>STATUS</b></td>
		<td class="smalltext2"><b>:</b></td>
		<td class="title1">
			<?php echo $statusText;?>
		</td>
		<td class="smalltext2"><b>ACCEPTED BY</b></td>
		<td class="smalltext2"><b>:</b></td>
		<td class="title1"><b><?php echo $acceptedText;?></b></td>
	</tr>
	<tr>
		<td class="smalltext2"><b>COMPLETED DATE</b></td>
		<td class="smalltext2"><b>:</b></td>
		<td class="title1">
			<?php echo $orderCompletedOn;?>
		</td>
		<td class="smalltext2"><b>QA DONE BY</b></td>
		<td class="smalltext2"><b>:</b></td>
		<td class="title1">
			<?php echo $qaDoneByText;?>
		</td>
		<td class="smalltext2"><b>&nbsp;</b></td>
		<td class="smalltext2"><b>&nbsp;</b></td>
		<td class="smalltext2"><b>&nbsp;</b></td>
	</tr>
	<tr>
		<td colspan="15" valign="top">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?searchOrder=&searchBy=2&serachCustomerId=<?php echo $customerId?>" class="link_style14">Click here to view all orders of this customer</a>
		</td>
	<tr>
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
					?>&nbsp;&nbsp;(<a href="javascript:openEditCheckMessage(<?php echo $checkedId;?>)" class="link_style2">Edit</a>)
				</td>
				<?php
					}
				?>
			</table>
			</form>
		</td>
	</tr>
</table>