<?php
	if(!empty($totalUnrepliedOrdersMsg))
	{
		$whereClause			  =	"WHERE members_employee_messages.isVirtualDeleted =0 AND messageBy='".EMPLOYEES."' AND isTestAccount=0";
		$orderBy				  =	"addedOn DESC,addedTime DESC";

		$queryString			  =	"&empsent=1#six";

		$andClause1				  = " AND messageId > 250000";

		$homeText				  =	"";

		$hiddenSearchName		  =	"empsent";
		$hiddenSearchValue		  =	"1";

		$showingForMember		  = "";

		if(isset($_GET['showingForMember']) && $_GET['showingForMember'] != "")
		{
			$showingForMember	 =	$_GET['showingForMember'];

			if(!empty($showingForMember))
			{
				$andClause1		 =	" AND completeName='$showingForMember'";
				$queryString	 =	"&showingForMember=$showingForMember&empsent=1#six";

				$homeText		 =	 "View Messages Sent To - ".$showingForMember;
			}
		}

		$form					  =	SITE_ROOT_EMPLOYEES."/forms/searching-messages.php";
?>
<script type="text/javascript">
	function viewCustomerEmployeeMessages(orderId,customerId)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/view-order-all-messages.php?orderId="+orderId+"&customerId="+customerId;
		prop = "toolbar=no,scrollbars=yes,width=800,height=650,top=100,left=100";
		window.open(path,'',prop);
	}
</script>
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
		$all_employees_list 	=	array();
		$query					=	"SELECT employeeId,fullName FROM employee_details WHERE isActive=1 AND hasPdfAccess=1 ORDER BY fullName";
		$result	=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$a_processEmployees	=	array();
			while($row			=	mysqli_fetch_assoc($result))
			{
				$employeeId		=	$row['employeeId'];
					
				$all_employees_list[$employeeId]	=	stripslashes($row['fullName']);
			}

		}

		$start					  =	0;
		$recsPerPage	          =	15;	//	how many records per page
		$showPages		          =	10;	
		$pagingObj->recordNo	  =	$recNo;
		$pagingObj->startRow	  =	$recNo;
		$pagingObj->whereClause   =	$whereClause.$andClause1." GROUP BY members_employee_messages.orderId";
		$pagingObj->recsPerPage   =	$recsPerPage;
		$pagingObj->showPages	  =	$showPages;
		$pagingObj->orderBy		  =	$orderBy;
		$pagingObj->table		  =	"members_employee_messages INNER JOIN members ON members_employee_messages.memberId=members.memberId";
		$pagingObj->selectColumns = "members_employee_messages .*,firstName,lastName,appraisalSoftwareType";
		//status,orderAddress,acceptedBy,hasRepliedUploaded,acceeptedByName,isOrderChecked,fullName
		$pagingObj->primaryColumn =	"members_employee_messages.orderId";
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
				<td class="smalltext8" width="9%"><b>From Employee</b></td>
				<td class="smalltext8" width="13%"><b>To Customer</b></td>
				<td class="smalltext8" width="17%"><b>Date</b></td>
				<td class="smalltext8" width="20%"><b>Order Address</b></td>
				<td class="smalltext8" width="6%"><b>Status</b></td>
				<td class="smalltext8"><b>Message</b></td>
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
					$message					=	stripslashes($row['message']);
					$t_appraisalSoftwareType	=	$row['appraisalSoftwareType'];
					$messageDate				=	$row['addedOn'];
					$messageTime				=	$row['addedTime'];
					$sentByEmpId				=	$row['employeeId'];
					$isNeedToVerify				=	$row['isNeedToVerify'];

					$status						=	0;
					$oderAddress				=	"";
					$acceptedBy					=	"";
					$isOrderChecked				=	0;
					$hasReplied					=	0;

					if(!empty($orderId)){
						$query1					=	"SELECT status,orderAddress,acceptedBy,isOrderChecked,hasRepliedUploaded FROM members_orders WHERE orderId=$orderId AND memberId=$memberId";
						$result1 				=	dbQuery($query1);
						if(mysqli_num_rows($result1)){
							while($row1 		=	mysqli_fetch_assoc($result1)){
								$status			=	$row1['status'];
								$oderAddress	=	stripslashes($row1['orderAddress']);
								$acceptedBy		=	$row1['acceptedBy'];
								$isOrderChecked	=	$row1['isOrderChecked'];
								$hasReplied		=	$row1['hasRepliedUploaded'];
							}
						}
					}


					$sentByEmp                  =   "";

					if(in_array($sentByEmpId,$a_allDeactivatedEmployees)){
					  	 $sentByEmp             = "Hemant Jindal";
					}
					elseif(array_key_exists($sentByEmpId,$all_employees_list)){
						$sentByEmp              =   $all_employees_list[$sentByEmpId];
					}
							

					

					$notifyText					=	"";
					if($isNeedToVerify			==	1)
					{
						$notifyText				=	"<font class='smalltext23'>[<font color='#ff0000;'>This Message Is Not Yet Sent, Need verification. Please ask manager to send it ASAP</font>";
						if(!empty($s_isHavingVerifyAccess))
						{
							$notifyText .=	"&nbsp;<a href='".SITE_URL_EMPLOYEES."/send-message-pdf-customer1.php?orderId=$orderId&customerId=$memberId&vmsg=$messageId#sendMessages' class='link_style14' target='_blank'>Verify It</a>";
						}
						
						$notifyText		.=	"]</font>";

					}	
					
				

					$statusText					=   "<font color='red'>New Order</font>";
					$acceptedByText				=	"";
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
					elseif($status				==	2)
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

					if(!empty($notifyText))
					{
			?>
			<tr height="25">
				<td colspan="5">
					<?php echo $notifyText;?>
				</td>
			</tr>
			<?php
					}
			?>
			<tr <?php echo $bgColor;?> height="25">
				<td class="smalltext2" valign="top"><?php echo $i;?>)</td>
				<td class="smalltext16" valign="top">
					<?php 
						if(!empty($sentByEmp))
						{
							if(!empty($s_hasManagerAccess))
							{
						?>
							  <a onclick="serachRedirectFileType('?isSubmittedForm=1&orderOf=<?php echo $sentByEmpId;?>&showingEmployeeOrder=1')" class='link_style12' style="cursor:pointer;" title='View orders of <?php echo $sentByEmp;?>'><?php echo $sentByEmp;?></a>
						<?php
							}
							elseif(empty($s_hasManagerAccess) && $s_employeeId == $acceptedBy)
							{
						?>
							  <a onclick="serachRedirectFileType('?isSubmittedForm=1&orderOf=<?php echo $sentByEmpId;?>&showingEmployeeOrder=1')" class='link_style12' style="cursor:pointer;" title='View orders of <?php echo $sentByEmp;?>'><?php echo $sentByEmp;?></a>
						<?php	
							}
							else
							{
								echo $sentByEmp;
							}
						}
					?>
				</td>
				<td class="smalltext2" valign="top">
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=<?php echo $memberId;?>" class="link_style12"><?php echo $customerName;?></a>
				</td>
				<td class="smalltext16" valign="top">
					<?php echo $daysAgo;?>
				</td>
				<td class="smalltext2" valign="top">
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $memberId;?>&selectedTab=5"  class="link_style12">
						<b>
							<?php echo stripslashes($oderAddress);?>
						</b>
					</a><br />
					<?php
						echo "(<a onclick='viewCustomerEmployeeMessages($orderId,$memberId)' class='link_style17' style='cursor:pointer;'>View All</a>)";	
					?>
				</td>
				<td class="smalltext2" valign="top"><?php echo $statusText;?></td>
				<td class="smalltext2" valign="top"><?php echo nl2br($message);?></td>
			</tr>
			<?php
				}

				echo "<tr><td height='20'></td></tr><tr><td style='text-align:center' colspan='8'>";
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
