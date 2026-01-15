<?php
    include(SITE_ROOT		    .   "/classes/pagingclass1.php");
	$pagingObj					=   new Paging();
	if(isset($_REQUEST['recNo']))
	{
		$recNo					=	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo					=	0;
	}
	$showOrderMessageAll	    = 0;
	if(isset($_GET['showOrderMessageAll']) && $_GET['showOrderMessageAll'] == 1){
		$showOrderMessageAll	= 1;
	}


	$n_whereClause			=	"WHERE memberId=$customerId AND isOrderGeneralMsg=1 AND isBillingMsg=0";
	$n_orderBy				=	"addedOn DESC,addedtime DESC";
	$n_queryString			=	"&orderId=$orderId&customerId=$customerId&selectedTab=6";
	$n_andClause			=	"";
	$n_table				=   "members_general_messages";
	$n_columns				=   "*";
	$n_path					=   SITE_URL_EMPLOYEES."/view-order-others.php";
	$n_linkClass			=   "link_style34";
	$n_linkClass1			=   "link_style35";

	

	if($showOrderMessageAll	  == 1){
		$n_whereClause			  =	"WHERE members_employee_messages.memberId=$customerId AND members_orders.isVirtualDeleted=0 AND isNeedToVerify=0";
		$n_orderBy				  =	"addedOn DESC,addedTime DESC";
		$n_queryString			  =	"&orderId=$orderId&customerId=$customerId&selectedTab=6&showOrderMessageAll=1";
		$n_andClause			  =	"";
		$n_table				  = "members_employee_messages INNER JOIN members_orders ON members_employee_messages.orderId=members_orders.orderId";
		$n_columns				  = "members_employee_messages.*,orderAddress,isNewUploadingSystem";
		$n_path					  = SITE_URL_EMPLOYEES."/view-order-others.php";
		$n_linkClass			  = "link_style35";
		$n_linkClass1			  = "link_style34";
	}
?>
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0" style="border:0px solid #333333;">
		<tr>
			<td width="25%" align="center">&nbsp;<a href="<?php echo SITE_URL_EMPLOYEES."/view-order-others.php?orderId=$orderId&customerId=$customerId&selectedTab=6&showOrderMessageAll=1";?>" class="<?php echo $n_linkClass;?>">ORDERS SPECIFIC MESSAGES</a>
			</td>
			<td align="center">
				<a href="<?php echo SITE_URL_EMPLOYEES."/view-order-others.php?orderId=$orderId&customerId=$customerId&selectedTab=6";?>" class="<?php echo $n_linkClass1;?>">GENERAL ORDERS MESSAGES</a>	
			</td>
		</tr>
		<tr>
			<td colspan="3" height="5"></td>
		</tr>
		<tr>
			<td colspan="3" style="border-bottom: 1px solid black;"></td>
		</tr>
	</table>
<?php
	$start					  =	0;
	$recsPerPage	          =	20;	//	how many records per page
	$showPages		          =	3;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$n_whereClause.$n_andClause;
	$pagingObj->recsPerPage   =	20;
	$pagingObj->showPages	  =	3;
	$pagingObj->orderBy		  =	$n_orderBy;
	$pagingObj->table		  =	$n_table;
	$pagingObj->selectColumns = $n_columns;
	$pagingObj->path		  = $n_path;
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
?>
	<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0" style="border:0px solid #333333;">
		<?php
			if(!empty($showOrderMessageAll)){
		?>
		 <tr>
			<td class="smalltext23" width="15%">&nbsp;<b>Message By</b></th>
			<td class="smalltext23" width="15%"><b>Date</b></th>
			<td class="smalltext23" width="25%"><b>Order Address</b></th>
			<td class="smalltext23"><b>Message</b></th>
		</tr>
		<tr>
			<td colspan="5" height="5"></td>
		</tr>
		<tr>
			<td colspan="5" style="border-bottom: 1px solid black;"></td>
		</tr>
		<?php
			}
			else{
		?>
		<tr>
			<td class="smalltext23" width="15%">&nbsp;<b>Message By</b></th>
			<td class="smalltext23" width="15%"><b>Date</b></th>
			<td class="smalltext23"><b>Message</b></th>
		</tr>
		<tr>
			<td colspan="5" height="5"></td>
		</tr>
		<tr>
			<td colspan="5" style="border-bottom: 1px solid black;"></td>
		</tr>
		<?php
			}
			$i						=	$recNo;
			while($row				=   mysqli_fetch_assoc($recordSet))
			{
				$i++;
				if(!empty($showOrderMessageAll)){
					////////////////////// SHOWING ORDER RELATED MESSAGES ////////////////////////
					$t_messageId		=	$row['messageId'];
					$t_isDisplayZipFile	=	0;
					$t_isNewUploadingSystem	=	$row['isNewUploadingSystem'];
					$n_orderId			=	$row['orderId'];
					$t_hasMessageFiles	=	$row['hasMessageFiles'];
					$t_isDeletedMsgFile	=	$row['isDeleted'];
					$n_orderAddress		=	stripslashes($row['orderAddress']);
					$n_isVirtualDeletedMsgFile	=	$row['isVirtualDeleted'];

					$t_addedOn			=	$row['addedOn'];
					
					$messageTime		=	$row['addedTime'];
					
					$t_message			=	stripslashes($row['message']);
					$messageBy			=	$row['messageBy'];
					$messageBytext		=	"Data entry team";
					if($messageBy		==  CUSTOMERS)
					{
						$messageBytext	=	$customerName;
					}

					$t_encodeOrderID		=	base64_encode($n_orderId);
					$base_maessageId	=	base64_encode($t_messageId);

					$daysAgo			=	showDateTimeFormat($t_addedOn,$messageTime);
					$bgColor			=	"";
					if($i%2==0)
					{
						$bgColor		=   " bgcolor='#E6E6E6'";
					}
		?>
		<tr<?php echo $bgColor?>>
			<td valign="top" class="smalltext2">&nbsp;<?php echo $messageBytext;?></td>
			<td valign="top"><?php echo $daysAgo;?></td>
			<td valign="top">
				<?php
					if($n_isVirtualDeletedMsgFile == 0){
				?>
				<a href="<?php echo SITE_URL_EMPLOYEES."/view-order-others.php?orderId=$n_orderId&customerId=$customerId";?>" class="link_style5"><?php echo getSubstring($n_orderAddress,45);?></a>
				<?php
					}
					else{
						echo getSubstring($n_orderAddress,45); 
					}
				?>
			</td>
			<td valign="top">
				<?php 
					echo nl2br($t_message);
		
					if($t_hasMessageFiles == 1 && empty($t_isDeletedMsgFile))
					{
						
						if($t_isNewUploadingSystem == 1)
						{
							if($result1			=	$orderObj->getOrdereMessageFile($n_orderId,$t_messageId,3,7))
							{
								$countTotal			=	0;

								while($row1			=	mysqli_fetch_assoc($result1))
								{
									$countTotal++;
									if($countTotal	> 3){
										$t_isDisplayZipFile	=	1;
									}
									$t_fileId	    =	$row1['fileId'];
									$fileName		=	stripslashes($row1['uploadingFileName']);
									$fileExtension	=	$row1['uploadingFileExt'];
									$fileSize		=	$row1['uploadingFileSize'];
									$imageOnServerPath	=	$row1['excatFileNameInServer'];
									$imageOnServerPath  =   stringReplace("/home/ieimpact", "", $imageOnServerPath);

									$t_base_fileId	=	base64_encode($t_fileId);
									
									$downLoadPath	=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$t_encodeOrderID."&".$M_D_5_ID."=".$t_base_fileId;
								?>
								<br />>>&nbsp;<a class="link_style32" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download Message File" style="cursor:pointer;"><?php echo $fileName.".".$fileExtension;?></a>&nbsp;&nbsp;<font class='smalltext20'><?php echo getFileSize($fileSize);?></font>
								<?php
								}
								if($t_isDisplayZipFile == 1){
																			
									$messageFiledownLoadPath	=	SITE_URL_EMPLOYEES."/download-all-message-files.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".base64_encode($t_messageId);
							?>
									<br /><!-->>&nbsp;(<a class="link_style13" onclick="downloadMultipleOrderFile('<?php echo $messageFiledownLoadPath;?>');" title="Download Message File as ZIP" style="cursor:pointer;"><b>Download Message All Files As .zip</b></a>)-->
							<?php
									
								}
							}
						}
						else
						{
							echo ">>&nbsp;<a href='".SITE_URL_EMPLOYEES."/download-message-files.php?ID=$t_messageId'  class='link_style32'>".$fileName.".".$fileExtension."</a>&nbsp;&nbsp;<font class='smalltext'>".getFileSize($fileSize)."</font>";
						}
						
					}
				?>
			</td>
		</tr>
		
		<?php

					/////////////////////// FINISHED SHOWING ORDER RELATED MESSAGES ////////////////
				}
				else{
					////////////////////////// SHOWING GENERAL MESSAGES //////////////////////////////
					$t_isDisplayZipFile     =   0;
					$generalMsgId			=	$row['generalMsgId'];
					$customerZoneDate		=	showDate($row['customerZoneDate']);
					$t_message				=	trim(stripslashes($row['message']));
					$parentId				=	$row['parentId'];
					$t_isUploadedFiles		=	$row['isUploadedFiles'];
					$t_addedOn				=	$row['customerZoneDate'];
					$messageRelatedOrder	=	trim(stripslashes($row['messageRelatedOrder']));
					$employeeSendingFirstMsg	=	$row['employeeSendingFirstMsg'];
					
					if($t_addedOn			==	"0000-00-00")
					{	
						$t_addedOn			=	$row['addedOn'];
					}
					$messageEstTime			=	$row['customerZoneTime'];
					if($messageEstTime		==	"00:00:00")
					{
						$messageEstTime		=	$row['addedtime'];
					}

					$t_message              =   preg_replace( "/\r|\n/", "", $t_message);

					if($employeeSendingFirstMsg == 1){
						$messageBytext			=	"Data entry team";
					}
					else{
						if($parentId			==  0)
						{
							$messageBytext		=	$firstName;
						}
						else
						{
							$messageBytext		=	"Data entry team";
						}
					}
					$daysAgo				=	showDateTimeFormat($t_addedOn,$messageEstTime);

					$bgColor			=	"";
					if($i%2==0)
					{
						$bgColor		=   " bgcolor='#E6E6E6'";
					}
		?>
		<tr<?php echo $bgColor;?>>
			<td valign='top'>&nbsp;<?php echo $messageBytext;?></td>
			<td valign='top'><?php echo $daysAgo;?></td>
			<td valign="top">               
				<?php 
					if(!empty($messageRelatedOrder)){
						echo "Address :". $messageRelatedOrder."<br />";
					}
					echo nl2br($t_message);
					if($t_isUploadedFiles == 1)	
					{
						
						if($a_files	=	$orderObj->getCustomerGeneralMessageEmailFiles($customerId,$generalMsgId))
						{
							$cn	=	0;
							$isDisplayZipFile			=	0;
							foreach($a_files as $fileId=>$value)
							{
								$cn++;
								if($cn > 3){
									$t_isDisplayZipFile	=	1;
								}
								list($fileName,$size) = explode("|",$value);

								$base_fileId	=	base64_encode($fileId);

								$downLoadPath	=	SITE_URL_EMPLOYEES."/download-general-mesage-file.php?".$M_D_5_ID."=".$base_fileId;

								$fileSize	=	getFileSize($size);
					?>
						<br />>>&nbsp;<a class="link_style32" onclick="downloadGeneralMessageFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $fileName;?></a>
							&nbsp;<?php echo $fileSize;?>
						
					<?php

							}
							if($t_isDisplayZipFile == 1){
																	
								$messageFiledownLoadPath	=	SITE_URL_EMPLOYEES."/download-all-general-message-files.php?".$M_D_5_ORDERID."=".base64_encode($customerId)."&".$M_D_5_ID."=".base64_encode($generalMsgId);
						?>
								<br /><!-->>&nbsp;(<a class="link_style13" onclick="downloadGeneralMessageFile('<?php echo $messageFiledownLoadPath;?>');" title="Download Message File as ZIP" style="cursor:pointer;"><b>Download Message All Files As .zip</b></a>)-->
						<?php
								
							}
						}
						
					}
				?>                        
            </td>
		</tr>
		<?php

					///////////////////////// FINISHED SHOWING GENERAL MESSAGES /////////////////////
				}
			}
		?>
		<tr>
			<td colspan="6">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="6" style="text-align:right"><?php echo $pagingObj->displayPaging($n_queryString);?>&nbsp;</td>
		</tr>
	</table>
<?php
	}
	else{
		echo "<table width='100%' align='center'><tr><td height='200' style='text-align:center'><font style='font-size:16px;font-family:verdana;color:#ff0000;font-weight:bold'>No Message Available</font></td></tr></table>";
	}
?>