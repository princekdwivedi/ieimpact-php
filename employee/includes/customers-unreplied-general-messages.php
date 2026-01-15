<?php
	$M_D_5_ORDERID			  =	ORDERID_M_D_5;
	$M_D_5_ID				  =	ID_M_D_5;
	
	$whereClause			  =	"WHERE isOrderGeneralMsg=1 AND isBillingMsg=0 AND parentId=0 AND status=0 AND employeeSendingFirstMsg=0";

	$orderBy				  =	"members_general_messages.status,members_general_messages.addedOn DESC,members_general_messages.addedtime";

	$andClause1				  =	"";

	$queryString			  =	"&unrepliedGeneralMsg=1#fifth";

	$homeText				  =	 "";

	
	$hiddenSearchName		  =	"unrepliedGeneralMsg";
	$hiddenSearchValue		  =	"1";

	$showingForMember		  = "";

	if(isset($_GET['showingForMember']) && $_GET['showingForMember'] != "")
	{
		$showingForMember	 =	$_GET['showingForMember'];

		if(!empty($showingForMember))
		{
			$andClause1		 =	" AND completeName='$showingForMember'";
			$queryString	 =	"&showingForMember=$showingForMember&unrepliedGeneralMsg=1#fifth";

			$orderBy		 =	"addedOn DESC,addedtime DESC";

			$homeText		 =	 "View General Messages From - ".$showingForMember;

			////////// IF AVAILABLE THE SEARCH CUSTOMER /////////////
			$serachCustomerId=	$employeeObj->getSingleQueryResult("SELECT memberId from members WHERE completeName='$showingForMember'","memberId");
			if(empty($serachCustomerId)){
				$serachCustomerId	=	0;
			}
		}
	}

	$form					  =	SITE_ROOT_EMPLOYEES."/forms/searching-messages.php";
?>
<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<td colspan="10" height="10"></td>
	</tr>
	<tr>
		<td colspan="10">
			<?php
				include($form);
			?>
		</td>
	</tr>
	<tr>
		<td colspan="10" height="10"></td>
	</tr>
	<tr>
		<td colspan="10" class="textstyle1" style="text-align:left;"><b><?php echo $homeText;?></b>&nbsp;
			<?php
				if(!empty($serachCustomerId)){
			?>
			(<a onclick="sendingNewGeneralMsg(<?php echo $serachCustomerId;?>);" class='link_style14' style='cursor:pointer;'>Send A Message</a>)
			<?php
				}	
			?>
		</td>
	</tr>
	<tr>
		<td colspan="10" height="10"></td>
	</tr>
</table>
<?php
	if(VISITOR_IP_ADDRESS	==	"122.160.167.153"){
		//echo "KASE1 -".$totalUnrepliedGeneralMsg;
		/*echo "SELECT members_general_messages .*,completeName FROM members_general_messages INNER JOIN members ON members_general_messages.memberId=members.memberId ".$whereClause.$andClause1;
		die();*/
	}
	if(!empty($totalUnrepliedGeneralMsg))
	{
		$start					  =	0;
		$recsPerPage	          =	25;	//	how many records per page
		$showPages		          =	10;	
		$pagingObj->recordNo	  =	$recNo;
		$pagingObj->startRow	  =	$recNo;
		$pagingObj->whereClause   =	$whereClause.$andClause1;
		$pagingObj->recsPerPage   =	$recsPerPage;
		$pagingObj->showPages	  =	$showPages;
		$pagingObj->orderBy		  =	$orderBy;
		$pagingObj->table		  =	"members_general_messages INNER JOIN members ON members_general_messages.memberId=members.memberId";
		$pagingObj->selectColumns = "members_general_messages .*,firstName,lastName";
		$pagingObj->path		  = SITE_URL_EMPLOYEES."/pdf-customer-messages.php";
		$totalRecords = $pagingObj->getTotalRecords();
		if($totalRecords && $recNo <= $totalRecords)
		{
			$pagingObj->setPageNo();
			$recordSet = $pagingObj->getRecords();
			$i							=	$recNo;
			$M_D_5_ID					=	ID_M_D_5;
	?>
		
		<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
			<tr bgcolor="#373737" height="20">
				<td class="smalltext8" width="2%">&nbsp;</td>
				<td class="smalltext8" width="12%"><b>From</b></td>
				<td class="smalltext8" width="15%"><b>Date</b></td>
				<td class="smalltext8" width="18%"><b>Order Address</b></td>
				<td class="smalltext8" width="35%"><b>Message</b></td>
				<td class="smalltext8"><b>Action</b></td>
			</tr>
			<?php
			
				while($row	=   mysqli_fetch_assoc($recordSet))
				{
					$i++;
					
					$generalMsgId				=	$row['generalMsgId'];
					$memberId					=	$row['memberId'];
					$firstName					=	stripslashes($row['firstName']);
					$lastName					=	stripslashes($row['lastName']);
					$customerName				=	$firstName." ".substr($lastName, 0, 1);
					$messageDate				=	$row['addedOn'];
					$messageTime				=	$row['addedtime'];
					$messageRelatedOrder		=	stripslashes($row['messageRelatedOrder']);
					$message					=	stripslashes($row['message']);
					$isUploadedFiles			=	$row['isUploadedFiles'];
					$mesageStatus				=	$row['status'];
					$replyBy					=	$row['replyBy'];
					$repliedByEmployeetext		=	stripslashes($row['repliedByEmployeetext']);

					$repliedByText				=	"";
					if(!empty($replyBy))
					{
						$repliedByText			=	$employeeObj->getEmployeeFirstName($replyBy);
					}

					$message                    =   preg_replace( "/\r|\n/", "", $message);

					if(empty($messageRelatedOrder))
					{
						$messageRelatedOrder	=	"Not Specific Order";
					}

				
					$bgColor					=	"class='rwcolor1'";
					$backGroundColor			=	"#FFFFFF";
					if($i%2==0)
					{
						$bgColor				=   "class='rwcolor2'";
						$backGroundColor		=	"#DAEEF3";
					}

					$daysAgo					=	showDateTimeFormat($messageDate,$messageTime);
			?>
			<tr>
				<td colspan="20">
					<div id="showHideGeneralMessage<?php echo $i;?>">
						<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
							<tr height="25">
								<td class="smalltext2" valign="top"  width="2%"><?php echo $i;?>)</td>
								<td class="smalltext2" valign="top"  width="12%"><a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=<?php echo $memberId;?>" class="link_style12"><?php echo $customerName;?></a></td>
								<td  valign="top" class="smalltext16" valign="top" width="15%">
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
													<table width="100%">
														<tr>
															<td class="smalltext1" valign="top">
																<?php echo nl2br($message);?>
															</td>
														</tr>
													</table>
												</div>
											</td>
										</tr>
										<?php
											if($isUploadedFiles == 1)	
											{
												
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
												<td width="8%" class="smalltext22" valign="top">
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
											
										}
									?>
								</table>
							</td>
							<td valign="top" style="text-align:left;">
								<?php
									if($mesageStatus == 0)
									{
										
									?>
										<a onclick="replyCustomerGeneralMessage(<?php echo $generalMsgId;?>,<?php echo $memberId;?>);" class='link_style12' style='cursor:pointer;'>Reply</a>
										<font class="smalltext2"> | </font>
														
										<a onclick="replyAllMessageForcefully(<?php echo $generalMsgId;?>,<?php echo $memberId;?>,3)" class="greenLink" style='cursor:pointer;' title='Action Taken'>Action Taken</a>
													
								<?php
									}
									else
									{
										if(!empty($repliedByText))
										{
								?>
										<font class="smalltext2">
											<?php echo nl2br($repliedByEmployeetext);?><br />
										</font>
										<b>By :</b> <a onclick="serachRedirectFileType('?isSubmittedForm=1&orderOf=<?php echo $repliedByText;?>&showingEmployeeOrder=1')" class='link_style12' style="cursor:pointer;" title='View orders of <?php echo $repliedByText;?>'><?php echo $repliedByText;?></a>
							<?php
											
										}
										if($s_hasManagerAccess == 1)
										{
											$markedUrl	=	SITE_URL_EMPLOYEES."/marked-reply-general-order-message.php?srNo=".$i."&isDelete=1&msgId=";
									?>
											<br /><a onclick="commonFunc1('<?php echo $markedUrl;?>','showHideGeneralMessage<?php echo $i;?>','Are you sure to delete this message?',<?php echo $generalMsgId?>)" class="greenLink" style='cursor:pointer;' title='Mark As Replied'>Delete</a>
									<?php
										}
										
									}
								?>
							</td>
						</tr>
					</table>
				</div>
			<td></tr>
			<?php
				}
				echo "<tr><td height='10'></td></tr><tr><td style='text-align:center' colspan='8'>";
				$pagingObj->displayPaging($queryString);
				echo "&nbsp;&nbsp;</td></tr>";	
			?>
		</table>
	<?php
		}
		else
		{
			echo "<table width='70%' align='center' border='0'><tr><td height='50'></td></tr><tr><td style='text-align:center;' class='error'><b>No Messages Available.</b></td></tr><tr><td height='200'></td></tr></table>";
		}
	}
	else
	{
		echo "<table width='70%' align='center' border='0'><tr><td height='50'></td></tr><tr><td style='text-align:center;' class='error'><b>No Messages Available.</b></td></tr><tr><td height='200'></td></tr></table>";
	}
?>