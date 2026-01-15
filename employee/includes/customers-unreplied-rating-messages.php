<?php
	if(!empty($totalUnrepliedRatingMsges))
	{
		$whereClause		      =	"";

		$orderBy				  =	"rateGivenOn,rateGivenTiming";
		$queryString			  =	"&unrepliedRatingMsg=1#third";


		$homeText				  =	"";

		$hiddenSearchName		  =	"unrepliedRatingMsg";
		$hiddenSearchValue		  =	"1";

		$showingForMember		  = "";

		if(isset($_GET['showingForMember']) && $_GET['showingForMember'] != "")
		{
			$showingForMember	 =	$_GET['showingForMember'];

			if(!empty($showingForMember))
			{
				
				if(is_numeric($showingForMember)){
					$whereClause	 =	"WHERE orderId=$showingForMember";

					$homeText		 =	 "View Ratings For OrderID - ".$showingForMember;
				}
				else{
					$whereClause	 =	"WHERE completeName='$showingForMember'";
					$queryString	 =	"&showingForMember=$showingForMember&unrepliedRatingMsg=1#third";

					$homeText		 =	 "View Ratings From - ".$showingForMember;
				}
			}
		}

		$display_submit_to		 =  array("3","7","8","137","946");

		if(isset($_POST['markactiontaeksubmitted'])){
			if(isset($_POST['verifyAll'])){
				$a_verifyAll	=	$_POST['verifyAll'];

				$markedFor		=	array();
				foreach($a_verifyAll as $k=>$orderId){
					$markedFor[]=	$orderId;
				}

				if(!empty($markedFor) && count($markedFor) > 0){

					$updateFor		 =  implode(",",$markedFor);
		
					dbQuery("UPDATE members_orders SET isRepliedToRatingMessage=1,managerRepliedRatingText='thanks',ratingMessageRepliedBy=$s_employeeId,ratingRepliedOn='".CURRENT_DATE_INDIA."',ratingRepliedTime='".CURRENT_TIME_INDIA."',ratingRepliedFromIP='".VISITOR_IP_ADDRESS."',isHavingOrderNewMessage=0 WHERE orderId IN ($updateFor) AND isHavingOrderNewMessage=1 AND rateGiven <> 0 ");

					dbQuery("DELETE FROM all_unreplied_rating WHERE orderId IN ($updateFor)");
				}
			}
			$recNo				 = $_POST['recNo'];

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES ."/pdf-customer-messages.php?recNo=$recNo&unrepliedRatingMsg=1#third");
			exit();
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
	try{
		$start					  =	0;
		$recsPerPage	          =	25;	//	how many records per page
		$showPages		          =	10;	
		$pagingObj->recordNo	  =	$recNo;
		$pagingObj->startRow	  =	$recNo;
		$pagingObj->whereClause   =	$whereClause;
		$pagingObj->recsPerPage   =	$recsPerPage;
		$pagingObj->showPages	  =	$showPages;
		$pagingObj->orderBy		  =	$orderBy;
		$pagingObj->table		  =	"employee_details INNER JOIN all_unreplied_rating ON employee_details.employeeId=all_unreplied_rating.accptedBy INNER JOIN members ON all_unreplied_rating.memberId=members.memberId";
		$pagingObj->selectColumns = "all_unreplied_rating .*,members.firstName,members.lastName,members.lastName,appraisalSoftwareType,fullName as acceptedByName";
		$pagingObj->path		  = SITE_URL_EMPLOYEES."/pdf-customer-messages.php";
		$totalRecords = $pagingObj->getTotalRecords();
		if($totalRecords && $recNo <= $totalRecords)
		{
			$pagingObj->setPageNo();
			$recordSet = $pagingObj->getRecords();
			$i		   =	$recNo;
	?>
		<form name='markedAsActionAll' action="" method="POST">
		<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
			<tr bgcolor="#373737" height="20">
				<td class="smalltext8" width="5%">&nbsp;<b>Sr. No</b></td>
				<td class="smalltext8" width="13%"><b>From</b></td>
				<td class="smalltext8" width="17%"><b>Date/Time</b></td>
				<td class="smalltext8" width="18%"><b>Order Address</b></td>
				<td class="smalltext8" width="24%"><b>Message</b></td>
				<td class="smalltext8" width="4%"><b>Rating</b></td>
				<td class="smalltext8" width="8%"><b>Accepted By</b></td>
				<td>&nbsp;</td>
			</tr>
			<?php
				$showAllSubmit					=	false;
				while($row						=   mysqli_fetch_assoc($recordSet))
				{
					$i++;
					$orderId					=	$row['orderId'];
					$memberId					=	$row['memberId'];
					$firstName					=	stripslashes($row['firstName']);
					$lastName					=	stripslashes($row['lastName']);
					$customerName				=	$firstName." ".substr($lastName, 0, 1);
					$t_appraisalSoftwareType	=	$row['appraisalSoftwareType'];
					$messageDate				=	$row['rateGivenOn'];
					$messageTime				=	$row['rateGivenTiming'];
					$acceptedBy					=	$row['accptedBy'];
					$oderAddress				=	stripslashes($row['orderAddress']);
					$memberRateMsg				=	stripslashes($row['message']);
					$rateGiven					=	$row['rateGiven'];
					$acceptedByText				=	stripslashes($row['acceptedByName']);
												
					$bgColor					=	"class='rwcolor1'";
					if($i%2==0)
					{
						$bgColor				=   "class='rwcolor2'";
					}

					$daysAgo					=	showDateTimeFormat($messageDate,$messageTime);
			?>
			<tr>
				<td colspan="12" id="showHideMessage<?php echo $i?>">
					<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
						<tr <?php echo $bgColor;?> height="23">
							<td class="smalltext2" valign="top" width="5%"><?php echo $i;?>)
							<?php
								if(in_array($s_employeeId,$display_submit_to)){
									if($rateGiven > 3){
										echo "<input type='checkbox' name='verifyAll[]' value='$orderId'>";
										$showAllSubmit	=	true;
									}
									else{
										echo "&nbsp;";
									}
								}
							?>
							<td class="smalltext2" valign="top" width="13%"><a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=<?php echo $memberId;?>" class="link_style12"><?php echo $customerName;?></a></td>
							<td class="smalltext16" valign="top" width="17%"><?php echo $daysAgo;?></td>
							<td class="smalltext2" valign="top" width="18%">
								<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $memberId;?>#messages"  class="link_style12">
									<b>
										<?php echo stripslashes($oderAddress);?>
									</b>
								</a>
							</td>
							<td class="smalltext16" valign="top" width="24%"><?php echo nl2br($memberRateMsg);?></td>
							<td valign="top" width="4%">
								<img src="<?php echo SITE_URL;?>/images/rating/<?php echo $rateGiven;?>.png">
							</td>
							<td class="smalltext16" valign="top" width="8%">
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
							<td valign="top">
								<?php
									$markedUrl	=	SITE_URL_EMPLOYEES."/marked-reply-email-message.php?srNo=".$i."&orderId=";
								?>
								<a href="<?php echo SITE_URL_EMPLOYEES;?>/send-message-pdf-customer.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $memberId;?>#sendMessages" class='link_style12'>Reply</a>
								<font class="smalltext2"> | </font>
								
								
									<a onclick="replyAllMessageForcefully(<?php echo $orderId;?>,<?php echo $memberId;?>,2)" class="greenLink" style='cursor:pointer;' title='Action Taken'>Action Taken</a>

									<!--<a onclick="commonFunc1('<?php echo $markedUrl;?>','showHideMessage<?php echo $i;?>','Are you sure to mark as replied?',<?php echo $orderId?>)" class="greenLink" style='cursor:pointer;' title='Mark As Replied'>Mark As Replied</a>-->
								
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<?php
				}
				if(in_array($s_employeeId,$display_submit_to) && $showAllSubmit == 3){
					echo "<tr><td colspan='3'>&nbsp;</td></tr><tr><td colspan='3'><input type='submit' name='submit' value='Mark all 4&5 stars are action taken'><input type='hidden' name='markactiontaeksubmitted' value='1'><input type='hidden' name='recNo' value='$recNo'></td></tr>";
				}
				echo "<tr><td style='text-align:center' colspan='8'>";
				$pagingObj->displayPaging($queryString);
				echo "&nbsp;&nbsp;</td></tr>";	
			?>
		</table></form>
	<?php
		}
		else
		{
			echo "<table width='70%' align='center' border='0'><tr><td height='50'></td></tr><tr><td style='text-align:center;' class='error'><b>No Messages Available !!</b></td></tr><tr><td height='200'></td></tr></table>";
		}
	
	}
	catch (Exception $e) {
	  $error = $e->getMessage();
	  pr($error);
		
	}
}
else
{
	echo "<table width='70%' align='center' border='0'><tr><td height='50'></td></tr><tr><td style='text-align:center;' class='error'><b>No Messages Available !!</b></td></tr><tr><td height='200'></td></tr></table>";
}
?>