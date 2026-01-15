<?php
	if($result		=	$orderObj->previousOrdersEmployeesMessages($orderId,$customerId,10))
	{
?>
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td colspan="6" class="text"><b>PREVIOUS EMPLOYEE MESSAGES FOR ORDERS - <?php echo $customerName;?></b></td>
		</tr>
		<tr>
			<td width="4%">&nbsp;</td>
			<td width="30%" class="textstyle"><b>Order No</b></td>
			<td width="1%">&nbsp;</td>
			<td class="textstyle" width="40%"><b>Message</b></td>
			<td width="1%">&nbsp;</td>
			<td class="textstyle"><b>By</b></td>
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
							$emessageCount			=	0;
							while($emessageRow		=	mysql_fetch_assoc($result))
							{
								$emessageCount++;
								$e_messageId		=  $emessageRow['messageId'];
								$e_orderId			=  $emessageRow['messageFor'];
								$e_message			=  stripslashes($emessageRow['message']);
								$e_message			=  nl2br($e_message);
								$e_address			=  stripslashes($emessageRow['orderAddress']);
								$e_messageBy		=  $emessageRow['messageBy'];
	
								$e_messageByName	=	$employeeObj->getEmployeeFirstName($e_messageBy);
						?>
						<tr>
							<td class="textstyle" valign="top" width="4%"><?php echo $emessageCount;?>)</td>
							<td class="textstyle" valign="top" width="31%">
								<a href="<?php echo SITE_URL_EMPLOYEES;?>/internal-emp-msg.php?orderId=<?php echo $e_orderId;?>&customerId=<?php echo $customerId;?>#messages" class='link_style12'><?php echo $e_address;?></a>
							</td>
							<td width="1%">&nbsp;</td>
							<td class="textstyle" valign="top" width="40%"><?php echo $e_message;?></td>
							<td width="1%">&nbsp;</td>
							<td class="smalltext1" valign="top">
								<?php
									echo $e_messageByName;
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
	</table>
<?php
	}
	
?>