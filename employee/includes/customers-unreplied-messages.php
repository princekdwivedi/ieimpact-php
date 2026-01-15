<?php
	if(!empty($totalUnrepliedOrdersMsg))
	{
		$searched_orders_ids			=	array();
		$searched_member_ids			=	array();
		$query12						=	"SELECT orderId,memberId FROM customer_orders_messages_counts";
		$result12						=	dbQuery($query12);
		if(mysqli_num_rows($result12)){
			while($row12				=	mysqli_fetch_assoc($result12)){

				$m_orderId				=	$row12['orderId'];
				$m_memberId				=	$row12['memberId'];

				if(!in_array($m_orderId,$searched_orders_ids)){
					$searched_orders_ids[]	=	$m_orderId;
				}

				if(!in_array($m_memberId,$searched_member_ids)){
					$searched_member_ids[]	=	$m_memberId;
				}
			}
			$searched_orders_ids		=	implode(",",$searched_orders_ids);
			$searched_member_ids		=	implode(",",$searched_member_ids);
		}
		else{
			$searched_orders_ids		=	0;
			$searched_member_ids		=	0;
		}

		
		$whereClause			  =	"WHERE members_employee_messages.orderId IN (".$searched_orders_ids.") AND members_employee_messages.memberId IN (".$searched_member_ids.") AND members_employee_messages.isVirtualDeleted =0 AND messageBy='".CUSTOMERS."' AND isRepliedToEmail=0";

		$orderBy				  =	"members_employee_messages.addedOn DESC,members_employee_messages.addedTime DESC";

		$queryString			  =	"&unrepliedMsg=1#second";

		$andClause1				  = "";

		$homeText				  =	"";

		$hiddenSearchName		  =	"unrepliedMsg";
		$hiddenSearchValue		  =	"1";

		$showingForMember		  = "";

		if(isset($_GET['showingForMember']) && $_GET['showingForMember'] != "")
		{
			$showingForMember	 =	$_GET['showingForMember'];

			if(!empty($showingForMember))
			{
				if(is_numeric($showingForMember)){
					$whereClause	 =	"WHERE members_employee_messages.orderId=$showingForMember AND messageBy='".CUSTOMERS."' AND isRepliedToEmail=0";

					$homeText		 =	 "View Order Messages For OrderID - ".$showingForMember;
				}
				else{
				
					$andClause1		 =	" AND completeName='$showingForMember'";
					$queryString	 =	"&showingForMember=$showingForMember&unrepliedMsg=1#second";

					$homeText		 =	 "View Order Messages From - ".$showingForMember;
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
		<td colspan="10" class="textstyle1" style="text-align:left;"><b><?php echo $homeText;?></b></td>
	</tr>
	<tr>
		<td colspan="10" height="10"></td>
	</tr>
</table>
<?php
		$start					  =	0;
		$recsPerPage	          =	25;	//	how many records per page
		$showPages		          =	10;	
		$pagingObj->recordNo	  =	$recNo;
		$pagingObj->startRow	  =	$recNo;
		$pagingObj->whereClause   =	$whereClause.$andClause1;
		$pagingObj->recsPerPage   =	$recsPerPage;
		$pagingObj->showPages	  =	$showPages;
		$pagingObj->orderBy		  =	$orderBy;
		$pagingObj->table		  =	"members_employee_messages INNER JOIN members ON members_employee_messages.memberId=members.memberId INNER JOIN members_orders ON members_employee_messages.orderId=members_orders.orderId";
		$pagingObj->selectColumns = "members_employee_messages .*,firstName,lastName,appraisalSoftwareType,status,orderAddress,acceptedBy,hasRepliedUploaded,acceeptedByName,isOrderChecked";
		$pagingObj->path		  = SITE_URL_EMPLOYEES."/pdf-customer-messages.php";
		$totalRecords = $pagingObj->getTotalRecords();
		if($totalRecords && $recNo <= $totalRecords)
		{
			$pagingObj->setPageNo();
			$recordSet = $pagingObj->getRecords();
			$i		   =	$recNo;
	?>
		
		<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
			<tr bgcolor="#373737" height="20">
				<td class="smalltext8" width="4%">&nbsp;<b>Sr. No</b></td>
				<td class="smalltext8" width="13%"><b>From</b></td>
				<td class="smalltext8" width="17%"><b>Date</b></td>
				<td class="smalltext8" width="16%"><b>Order Address</b></td>
				<td class="smalltext8" width="20%"><b>Message</b></td>
				<td class="smalltext8" width="8%"><b>Status</b></td>
				<td class="smalltext8" width="10%"><b>Accepted By</b></td>
				<td>&nbsp;</td>
			</tr>
			<?php
				while($row	=   mysqli_fetch_assoc($recordSet))
				{
					$i++;
					$orderId					=	$row['orderId'];
					$messageId					=	$row['messageId'];
					$memberId					=	$row['memberId'];
					$firstName					=	stripslashes($row['firstName']);
					$lastName					=	stripslashes($row['lastName']);
					$customerName				=	$firstName." ".substr($lastName, 0, 1);
					$messageDate				=	$row['addedOn'];
					$messageTime				=	$row['addedTime'];
					$main_message				=	stripslashes($row['message']);
					$status						=	$row['status'];
					$oderAddress				=	stripslashes($row['orderAddress']);
					$acceptedBy					=	$row['acceptedBy'];
					
					$isOrderChecked				=	$row['isOrderChecked'];
					$hasReplied					=	$row['hasRepliedUploaded'];
					$acceptedByText				=	stripslashes($row['acceeptedByName']);
					
				
					$statusText					=   "<font color='red'>New Order</font>";
					if($isOrderChecked			==	1)
					{
						$statusText				=   "<font color='green'>New Order</font>";
					}
					if($status					==	1)
					{
						$statusText				=   "<font color='#4F0000'>Accepted</font>";
						if(!empty($hasReplied))
						{
							$statusText			=	"<font color='blue'>QA Pending</font>";
						}
					}
					
					if($status					==	2)
					{
						$statusText				=   "<font color='green'>Completed</font>";
					}
					elseif($status				==	3)
					{	
						$statusText				=   "<font color='#333333'>Nd Atten.</font>";
					}
					elseif($status				==	5)
					{
						$statusText				=   "<font color='green'>Nd Feedbk.</font>";
					}

					elseif($status				==	4)
					{
						$statusText				=   "<font color='#ff0000'>Cancelled</font>";
					}
					elseif($status				==	6)
					{
						$statusText				=   "<font color='green'>Fd Rcvd</font>";
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
				<td colspan="10" id="showHideMessage<?php echo $i?>">
					<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
						<tr <?php echo $bgColor;?> height="23">
							<td class="smalltext2" valign="top" width="4%"><?php echo $i;?>)</td>
							<td class="smalltext2" valign="top" width="13%"><a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=<?php echo $memberId;?>" class="link_style12"><?php echo $customerName;?></a></td>
							<td width="17%" class="smalltext16" valign="top">
								<?php echo $daysAgo;?>
							</td>
							<td class="smalltext2" valign="top" width="16%">
								<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $memberId;?>&selectedTab=5"  class="link_style12">
									<b>
										<?php echo stripslashes($oderAddress);?>
									</b>
								</a>
							</td>
							<td class="smalltext16" valign="top" width="20%"><?php echo nl2br($main_message);?></td>
							<td class="smalltext2" valign="top" width="8%"><?php echo $statusText;?></td>
							<td class="smalltext16" valign="top" width="10%">
								<?php 
									if(!empty($acceptedByText))
									{
										if(!empty($s_hasManagerAccess))
										{
									?>
										  <a onclick="serachRedirectFileType('?isSubmittedForm=1&orderOf=<?php echo $acceptedBy;?>&showingEmployeeOrder=1')" class='link_style12' style="cursor:pointer;" title='View orders of <?php echo $acceptedByText;?>'><?php echo $acceptedByText;?></a>
									<?php
										}
										elseif(empty($s_hasManagerAccess) && $s_employeeId == $acceptedBy)
										{
									?>
										  <a onclick="serachRedirectFileType('?isSubmittedForm=1&orderOf=<?php echo $acceptedBy;?>&showingEmployeeOrder=1')" class='link_style12' style="cursor:pointer;" title='View orders of <?php echo $acceptedByText;?>'><?php echo $acceptedByText;?></a>
									<?php	
										}
										else
										{
											echo $acceptedByText;
										}
									}
								?>
							</td>
							<td valign="top" style="text-align:right;">
								<!--<a onclick="showCustomerMessage(<?php echo $orderId;?>,<?php echo $memberId;?>)" class="link_style12" style="cursor:pointer;">View</a><font class="smalltext2">&nbsp;|&nbsp;</font>-->
								<?php
									$markedUrl	=	SITE_URL_EMPLOYEES."/marked-reply-email-message.php?srNo=".$i."&orderId=".$orderId."&messageId=";
								?>
								<a href="<?php echo SITE_URL_EMPLOYEES;?>/send-message-pdf-customer.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $memberId;?>#sendMessages" class='link_style12'>Reply</a>
								<font class="smalltext2"> |</font>

								
								<a onclick="replyAllMessageForcefully(<?php echo $messageId;?>,<?php echo $memberId;?>,1)" class="greenLink" style='cursor:pointer;' title='Action Taken'>Action Taken</a>
								
								<!--<a onclick="commonFunc1('<?php echo $markedUrl;?>','showHideMessage<?php echo $i;?>','Are you sure to mark as replied?',<?php echo $messageId?>)" class="greenLink" style='cursor:pointer;' title='Mark As Replied'>Mark As Replied</a>-->
								
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<?php
				}
				echo "<tr><td style='text-align:center' colspan='8'>";
				$pagingObj->displayPaging($queryString);
				echo "&nbsp;&nbsp;</td></tr>";	
			?>
		</table>
	<?php
		}
		else
		{
			echo "<table width='70%' align='center' border='0'><tr><td height='50'></td></tr><tr><td style='text-align:center;' class='error'><b>No Messages Available !!</b></td></tr><tr><td height='200'></td></tr></table>";
		}
	}
	else
	{
		echo "<table width='70%' align='center' border='0'><tr><td height='50'></td></tr><tr><td style='text-align:center;' class='error'><b>No Messages Available !!</b></td></tr><tr><td height='200'></td></tr></table>";
	}
?>
