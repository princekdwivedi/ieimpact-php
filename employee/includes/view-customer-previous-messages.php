<?php
	$calculatedOrderFrom	=	getPreviousGivenDate($nowDateIndia,30);
	//*****Section to show previous 10 customer messages if any given*****//
	if($result		=	$orderObj->previousOrdersMessages($orderId,$customerId,10,$calculatedOrderFrom))
	{
?>
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td colspan="6" class="text"><b>PREVIOUS ORDER MESSAGES FROM - <?php echo $customerName;?></b></td>
		</tr>
		<tr>
			<td width="4%">&nbsp;</td>
			<td width="30%" class="textstyle"><b>Order No</b></td>
			<td width="1%">&nbsp;</td>
			<td class="textstyle" width="40%"><b>Message</b></td>
			<td width="1%">&nbsp;</td>
			<td class="textstyle"><b>Ratings</b></td>
		</tr>
		<tr>
			<td colspan="6">
				<hr size="1" width="100%" color="#bebebe">
			</td>
		</tr>
		<tr>
			<td colspan="6">
				<div style='width:465px;border:0px solid #ff0000;overflow:auto;height:200px'>
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<?php
							$messageCount			=	0;
							while($messageRow		=	mysql_fetch_assoc($result))
							{
								$messageCount++;
								$m_messageId		=  $messageRow['messageId'];
								$m_orderId			=  $messageRow['orderId'];
								$m_message			=  stripslashes($messageRow['message']);
								$m_message			=  nl2br($m_message);

								$m_orderName		=	@mysql_result(dbQuery("SELECT orderAddress FROM members_orders WHERE orderId=$m_orderId"),0);

								$m_orderName		=	stripslashes($m_orderName);

								$m_rateGiven		=	@mysql_result(dbQuery("SELECT rateGiven FROM members_orders WHERE orderId=$m_orderId"),0);

						?>
						<tr>
							<td class="textstyle" valign="top" width="4%"><?php echo $messageCount;?>)</td>
							<td class="textstyle" valign="top" width="31%">
								<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $m_orderId;?>&customerId=<?php echo $customerId;?>#messages" class='link_style12'><?php echo $m_orderName;?></a>
							</td>
							<td width="1%">&nbsp;</td>
							<td class="textstyle" valign="top" width="40%"><?php echo $m_message;?></td>
							<td width="1%">&nbsp;</td>
							<td class="smalltext1" valign="top">
								<?php
									if(!empty($m_rateGiven))
									{
										for($i=1;$i<=$m_rateGiven;$i++)
										{
											echo "<img src='".SITE_URL."/images/star.gif'  width=12 height=12'>";
										}
										echo $a_existingRatings[$m_rateGiven];
									}
									else
									{
										echo "&nbsp;";
									}
								?>
							</td>
						</tr>
						<tr>
							<td colspan="6">
								<hr size="1" width="100%" color="#bebebe">
							</td>
						</tr>
					<?php
						}
					?>
						</table>
					</div>
				<td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
	</table>
<?php
	}
	if($result	=	$orderObj->previousOrdersCustomerRatingComments($orderId,$customerId,10,$calculatedOrderFrom))
	{
?>
<br>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td colspan="6" class="text"><b>RATINGS BY <?php echo $customerName;?> IN 10 PREVIOUS ORDERS</b></td>
		</tr>
		<tr>
			<td width="4%">&nbsp;</td>
			<td width="30%" class="textstyle"><b>Order No</b></td>
			<td width="1%">&nbsp;</td>
			<td class="textstyle" width="25%"><b>Rating</b></td>
			<td width="1%">&nbsp;</td>
			<td class="textstyle"><b>Message</b></td>
		</tr>
		<tr>
			<td colspan="6">
				<hr size="1" width="100%" color="#bebebe">
			</td>
		</tr>
		<tr>
			<td colspan="6">
				<div style='width:465px;border:0px solid #ff0000;overflow:auto;height:200px'>
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<?php
							$customerRatingCount	=	0;
							while($ratingCustomerRow		=	mysql_fetch_assoc($result))
							{
								$customerRatingCount++;
								$cr_orderId			=  $ratingCustomerRow['orderId'];
								$cr_rateGiven		=  $ratingCustomerRow['rateGiven'];
								$cr_message			=  stripslashes($ratingCustomerRow['memberRateMsg']);
								$cr_message			=  nl2br($cr_message);
								$cr_orderName		=  $ratingCustomerRow['orderAddress'];
								$cr_orderName		=  stripslashes($cr_orderName);
						?>
						<tr>
							<td class="textstyle" valign="top" width="4%"><?php echo $customerRatingCount;?>)</td>
							<td class="textstyle" valign="top" width="31%">
								<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $cr_orderId;?>&customerId=<?php echo $customerId;?>" class='link_style12'><?php echo $cr_orderName;?></a>
							</td>
							<td width="1%">&nbsp;</td>
							<td class="textstyle" valign="top" width="25%">
								<?php
									if(!empty($cr_rateGiven))
									{
										for($i=1;$i<=$cr_rateGiven;$i++)
										{
											echo "<img src='".SITE_URL."/images/star.gif'  width=12 height=12'>";
										}
										echo $a_existingRatings[$cr_rateGiven];
									}
									else
									{
										echo "&nbsp;";
									}
								?>
								
							</td>
							<td width="1%">&nbsp;</td>
							<td class="smalltext1" valign="top">
								<?php echo $cr_message;?>
							</td>
						</tr>
						<tr>
							<td colspan="6">
								<hr size="1" width="100%" color="#bebebe">
							</td>
						</tr>
					<?php
						}
					?>
				</table>
					</div>
				<td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
	</table>
<?php
	}

	//*****Section to show previous 10 qa ratings if any given*****//

	if($result	=	$orderObj->previousOrdersQaComments($orderId,$customerId,$acceptedBy,5))
	{
?>
<br>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td colspan="6" class="text"><b>YOUR PREVIOUS ORDER RATINGS FROM QA</b></td>
		</tr>
		<tr>
			<td width="4%">&nbsp;</td>
			<td width="30%" class="textstyle"><b>Order No</b></td>
			<td width="1%">&nbsp;</td>
			<td class="textstyle" width="40%"><b>Rating</b></td>
			<td width="1%">&nbsp;</td>
			<td class="textstyle"><b>Message</b></td>
		</tr>
		<tr>
			<td colspan="6">
				<hr size="1" width="100%" color="#bebebe">
			</td>
		</tr>
		<tr>
			<td colspan="6">
				<div style='width:465px;border:0px solid #ff0000;overflow:auto;height:200px'>
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<?php
							$ratingCount			=	0;
							while($ratingRow		=	mysql_fetch_assoc($result))
							{
								$ratingCount++;
								$r_orderId			=  $ratingRow['orderId'];
								$r_memberId			=  $ratingRow['memberId'];
								$r_rateGiven		=  $ratingRow['rateByQa'];
								$r_message			=  stripslashes($ratingRow['qaRateMessage']);
								$r_message			=  nl2br($r_message);

								$r_orderName		=	@mysql_result(dbQuery("SELECT orderAddress FROM members_orders WHERE orderId=$r_orderId"),0);

								$r_orderName		=	stripslashes($r_orderName);
						?>
						<tr>
							<td class="textstyle" valign="top" width="4%"><?php echo $ratingCount;?>)</td>
							<td class="textstyle" valign="top" width="31%">
								<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $r_orderId;?>&customerId=<?php echo $r_memberId;?>" class='link_style12'><?php echo $r_orderName;?></a>
							</td>
							<td width="1%">&nbsp;</td>
							<td class="textstyle" valign="top" width="40%">
								<?php
									if(!empty($r_rateGiven))
									{
										for($i=1;$i<=$r_rateGiven;$i++)
										{
											echo "<img src='".SITE_URL."/images/star.gif'  width=12 height=12'>";
										}
										echo $a_existingRatings[$r_rateGiven];
									}
									else
									{
										echo "&nbsp;";
									}
								?>
								
							</td>
							<td width="1%">&nbsp;</td>
							<td class="smalltext1" valign="top">
								<?php echo $r_message;?>
							</td>
						</tr>
						<tr>
							<td colspan="6">
								<hr size="1" width="100%" color="#bebebe">
							</td>
						</tr>
					<?php
						}
					?>
				</table>
					</div>
				<td>
			</tr>
	</table>
<?php
	}
?>
