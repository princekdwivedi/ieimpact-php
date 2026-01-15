<?php
    function strip_html_tags_email( $text )
	{
		// PHP's strip_tags() function will remove tags, but it
		// doesn't remove scripts, styles, and other unwanted
		// invisible text between tags.  Also, as a prelude to
		// tokenizing the text, we need to insure that when
		// block-level tags (such as <p> or <div>) are removed,
		// neighboring words aren't joined.
		$text = preg_replace(
			array(
				// Remove invisible content
				'@<head[^>]*?>.*?</head>@siu',
				'@<style[^>]*?>.*?</style>@siu',
				'@<script[^>]*?.*?</script>@siu',
				'@<object[^>]*?.*?</object>@siu',
				'@<embed[^>]*?.*?</embed>@siu',
				'@<applet[^>]*?.*?</applet>@siu',
				'@<noframes[^>]*?.*?</noframes>@siu',
				'@<noscript[^>]*?.*?</noscript>@siu',
				'@<noembed[^>]*?.*?</noembed>@siu',

				// Add line breaks before & after blocks
				'@<((br)|(hr))@iu',
				'@</?((address)|(blockquote)|(center)|(del))@iu',
				'@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
				'@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
				'@</?((table)|(th)|(td)|(caption))@iu',
				'@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
				'@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
				'@</?((frameset)|(frame)|(iframe))@iu',
			),
			array(
				' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
				"\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
				"\n\$0", "\n\$0",
			),
			$text );

		// Remove all remaining tags and comments and return.
		return strip_tags($text, '<a><br>');
		//return strip_tags($text, '<a>');
		
	}

	$fromToDate				  =	date('Y-m-d', strtotime("-300 days", strtotime($today_year."-".$today_month."-".$today_day)));

	$whereClause			  =	"WHERE members_employee_messages.orderId > ".MAX_SEARCH_EMPLOYEE_ORDER_ID." AND members_employee_messages.isVirtualDeleted =0 AND messageBy='".CUSTOMERS."'";
	$orderBy				  =	"isRepliedToEmail,members_employee_messages.addedOn DESC,members_employee_messages.addedTime DESC";

	$queryString			  =	"&showAllOrders=1#first";

	$hiddenSearchName		  =	"showAllOrders";
	$hiddenSearchValue		  =	"1";

	$andClause				  =	" AND members_employee_messages.addedOn >= '$fromToDate' AND members_employee_messages.addedOn <= '$nowDateIndia'";
	$andClause1				  =	"";

	$homeText				  =	"";

	$showingForMember		  =	"";

	if(isset($_GET['showingForMember']) && $_GET['showingForMember'] != "")
	{
		$showingForMember	  =	$_GET['showingForMember'];

		if(!empty($showingForMember))
		{
			$andClause1		  =	" AND completeName='$showingForMember'";
			$queryString	  =	"&showingForMember=$showingForMember&showAllOrders=1#first";

			$homeText		  =	 "View Messages From - ".$showingForMember;
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
	$pagingObj->whereClause   =	$whereClause.$andClause.$andClause1;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"members_employee_messages INNER JOIN members ON members_employee_messages .memberId=members.memberId INNER JOIN members_orders ON members_employee_messages.orderId=members_orders.orderId";
	$pagingObj->selectColumns = "members_employee_messages.orderId,members_employee_messages.messageId,members_employee_messages.memberId,members_employee_messages.message,members_employee_messages.addedOn,members_employee_messages.addedTime,members_employee_messages.messageRepliedMarkedBy,members_employee_messages.managerRepliedText,members_employee_messages.isRepliedToEmail,members_employee_messages.isRepliedMessage,members_employee_messages.isRepliedMessage,firstName,lastName,appraisalSoftwareType,status,orderAddress,acceptedBy,hasRepliedUploaded,acceeptedByName,isOrderChecked	";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/pdf-customer-messages.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet			=	$pagingObj->getRecords();
		$i					=	$recNo;
?>
	<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
		<tr bgcolor="#373737" height="20">
			<td class="smalltext8" width="2%">&nbsp;</td>
			<td class="smalltext8" width="10%">&nbsp;<b>From</b></td>
			<td class="smalltext8" width="12%">&nbsp;<b>Date&Time</b></td>
			<td class="smalltext8" width="15%">&nbsp;<b>Order Address</b></td>
			<td class="smalltext8" width="25%">&nbsp;<b>Message</b></td>
			<td class="smalltext8" width="8%">&nbsp;<b>Status</b></td>
			<td class="smalltext8" width="8%">&nbsp;<b>Accepted By</b></td>
			<td class="smalltext8"><b>Action Taken</b></td>
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
				$t_appraisalSoftwareType	=	$row['appraisalSoftwareType'];
				$displayMesage				=	strip_html_tags_email($row['message']);
				$messageDate				=	$row['addedOn'];
				$messageTime				=	$row['addedTime'];
				$messageRepliedMarkedBy		=	$row['messageRepliedMarkedBy'];
				$managerRepliedText			=	$row['managerRepliedText'];
				$isRepliedToEmail			=	$row['isRepliedToEmail'];
				$isRepliedMessage			=	$row['isRepliedMessage'];
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
				if($i%2==0)
				{
					$bgColor				=   "class='rwcolor2'";
				}
				$repliedByText				=	"";
				if(!empty($messageRepliedMarkedBy)  && array_key_exists($messageRepliedMarkedBy,$all_pdfEmployees))
				{
					$repliedByText			=	$all_pdfEmployees[$messageRepliedMarkedBy];
				}
				
				$displayName				=	getSubstring($customerName,20);

				$daysAgo					=	showDateTimeFormat($messageDate,$messageTime);
			?>
		<tr>
			<td colspan="10">
				<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
					<tr>
						<td class="smalltext2" valign="top" width="2%" valign="top"><?php echo $i;?>)</td>
						<td class="smalltext2" valign="top" width="10%" valign="top"><a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=<?php echo $memberId;?>" class="link_style12"><?php echo $displayName;?></a></td>
						<td class="smalltext1" valign="top" width="12%"  valign="top"><?php echo $daysAgo;?></td>
						<td class="smalltext2" valign="top" width="15%" valign="top">
							<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $memberId;?>&selectedTab=5"  class="link_style12">
								<b>
									<?php echo stripslashes(getSubstring($oderAddress,33));?>
								</b>
							</a>
						</td>
						<!--<td width="10%" valign="top">
							<a onclick="showCustomerMessage(<?php echo $orderId;?>,<?php echo $memberId;?>)" class="link_style12" style="cursor:pointer;">View Message</a>
						</td>-->
						<td valign="top" width="25%" valign="top">
							<div style='overflow:auto;width:330px;scrollbars:no;border:0px;padding:0 0 0 0 ;'>
								<table width="100%">
									<tr>
										<td class="smalltext1" valign="top">
											<?php echo nl2br($displayMesage);?>
										</td>
									</tr>
								</table>
							</div>
						</td>
						<td class="smalltext2" valign="top" width="8%" valign="top"><?php echo $statusText;?></td>
						<td class="smalltext16" valign="top" width="7%" valign="top">
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
						<td valign="top" class="smalltext2">
							<?php
								if(!empty($isRepliedToEmail))
								{
									if(!empty($repliedByText))
									{
										echo nl2br($managerRepliedText);
									?>
									  <br /><b>By :</b> <a onclick="serachRedirectFileType('?isSubmittedForm=1&orderOf=<?php echo $repliedByText;?>&showingEmployeeOrder=1')" class='link_style12' style="cursor:pointer;" title='View orders of <?php echo $repliedByText;?>'><?php echo $repliedByText;?></a>
								<?php
									}
								}
								else
								{
							?>
										<a onclick="replyAllMessageForcefully(<?php echo $messageId;?>,<?php echo $memberId;?>,1)" class="greenLink" style='cursor:pointer;' title='Action Taken'>Action Taken</a>	
							<?php
									//echo "<font color='#ff0000;'>No Action taken yet</font>";
								}
								
							?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
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
		echo "<table width='70%' align='center' border='0'><tr><td height='50'></td></tr><tr><td style='text-align:center;' class='error'><b>No Messages Available !!</b></td></tr><tr><td height='200'></td></tr></table>";
	}
?>