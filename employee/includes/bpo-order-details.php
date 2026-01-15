<script type="text/javascript">
function openNoteWindow(customerId)
{
	path = "<?php echo SITE_URL_EMPLOYEES;?>/edit-special-note.php?customerId="+customerId;
	prop = "toolbar=no,scrollbars=yes,width=650,height=550,top=50,left=100";
	window.open(path,'',prop);
}
</script>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td width="55%" valign="top">
				<table width='100%' align='center' cellpadding='3' cellspacing='3' border='0'>
				<tr>
					<td colspan="3" class="text">UPLOADING FILES BY CUSTOMER</td>
				</tr>
				<?php
					if(!empty($isDeleted))
					{
				?>
				<tr>
					<td colspan="3" height="50" class="error">
						<b> FILES ARE DELETED</b>
					</td>
				</tr>
				<?php	
					}
					else
					{
				?>
				<!-- <tr>
					<td class="smalltext2" width="38%" valign="top"><b><?php echo $uploadedFileByCustomer;?></b></td>
					<td class="smalltext2" width="2%"  valign="top"><b>:</b></td>
					<td valign="top" class="smalltext2">
						<?php 
							if($hasOrderFile)
							{
								echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=OF&f=N' class='link_style2'>".$orderFileName.".".$orderFileExt."</a>";
								
								echo "<br><font class='smalltext2'>".getFileSize($orderFileSize)."</font>";
							}
							else
							{
								echo "<font color='red'>NO</font>";
							}
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top"><b>Uploaded Public Records File</b></td>
					<td class="smalltext2" valign="top"><b>:</b></td>
					<td valign="top" class="smalltext2">
						<?php 
							if($hasPublicRecordFile)
							{
								echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=PF&f=N' class='link_style2'>".$publicRecordFileName.".".$publicRecordFileExt."</a>";
								echo "<br><font class='smalltext2'>".getFileSize($publicRecordFileSize)."</font>";
							}
							else
							{
								echo "<font color='red'>NO</font>";
							}
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top"><b>Uploaded MLS File</b></td>
					<td class="smalltext2" valign="top"><b>:</b></td>
					<td valign="top" class="smalltext2">
						<?php 
							if($hasMlsFile)
							{
								echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=MF&f=N' class='link_style2'>".$mlsFileName.".".$mlsFileExt."</a>";
								echo "<br><font class='smalltext2'>".getFileSize($mlsFileSize)."</font>";
							}
							else
							{
								echo "<font color='red'>NO</font>";
							}
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top"><b>Uploaded Market Conditions File</b></td>
					<td class="smalltext2" valign="top"><b>:</b></td>
					<td valign="top" class="smalltext2">
						<?php 
							if($hasMarketConditionFile)
							{
								echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=CF&f=N' class='link_style2'>".$marketConditionFileName.".".$marketConditionExt."</a>";
								echo "<br><font class='smalltext2'>".getFileSize($marketConditionFileSize)."</font>";
							}
							else
							{
								echo "<font color='red'>NO</font>";
							}
						?>
					</td>
				</tr>
				<tr>
					<td class="smalltext2" valign="top"><b>Uploaded Other File</b></td>
					<td class="smalltext2" valign="top"><b>:</b></td>
					<td valign="top" class="smalltext2">
						<?php 
							if($hasOtherFile)
							{
								echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=OTF&f=N' class='link_style2'>".$otherFileName.".".$otherFileExt."</a>";
								echo "<br><font class='smalltext2'>".getFileSize($otherFileSize)."</font>";
							}
							else
							{
								echo "<font color='red'>NO</font>";
							}
						?>
					</td>
				</tr> -->
				<?php
					$query	=	"SELECT * FROM other_order_files WHERE orderId=$orderId AND uploadingFor=1";
					$result		=	mysql_query($query);
					if(mysql_num_rows($result))
					{
				?>
				<tr>
					<td class="smalltext2" valign="top"><b>Uploaded More Files</b></td>
					<td class="smalltext2" valign="top"><b>:</b></td>
					<td valign="top">
				<?php
						while($row		=	mysql_fetch_assoc($result))
						{
							$otherId		=	$row['otherId'];
							$fileName		=	$row['fileName'];
							$fileExtension	=	$row['fileExtension'];
							$fileSize		=	$row['fileSize'];

							echo "<a href='".SITE_URL_EMPLOYEES."/other-download.php?ID=$otherId&t=OT' class='link_style2'><b>".$fileName.".".$fileExtension."</b></a>";
							echo "<br><font class='smalltext2'>".getFileSize($fileSize)."</font>";	
							echo "<br>";
							
						}
					echo "</td></tr>";
					}
				}
				?>
				<tr>
					<td class="smalltext2" valign="top" colspan="3"><b>Customer Additional Instructions :</b>
						<?php echo nl2br($instructions);?>
					</td>
				</tr>
			</table>
		</td>
		<td width="2%">&nbsp;</td>
		<td valign="top">
			<table width='100%' align='center' cellpadding='3' cellspacing='3' border='0'>
				<?php
					if(!empty($splInstructionToEmployee))
					{
				?>
				<tr>
					<td colspan="3" class="text"><b>SPECIAL INSTRUCTIONS FROM <?php echo  ucwords($customerName);?></b></td>
				</tr>
				<tr>
					<td colspan="3">
						<div style='width:500px;border:0px solid #ff0000;overflow:auto;height:180px'>
							<table width="100%" cellpadding="3" cellspacing="3" border="0">
								<tr>
									<td class="textstyle">
										<p align="justify">
											<?php echo nl2br($splInstructionToEmployee);?>
										</p>
									</td>
								</tr>
							</table>
						</div>
						<br>
						</td>
					</tr>
				<?php
					}
		$query	=	"SELECT * FROM customer_instructions_file WHERE memberId=$customerId AND uploadedBy='".CUSTOMERS."' ORDER BY addedOn,addedTime";
		$result	=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			?>
			<tr>
				<td colspan="3" class="text"><b>CUSTOMER INSTRUCTIONS FILES</b></td>
			</tr>
			<tr>
				<td colspan="3">
					<table width="100%" cellpadding="3" cellspacing="3" border="0">
						<?php
							$i	=	0;
							while($row	=	mysql_fetch_assoc($result))
							{
								$i++;
								$instructionId	=	$row['instructionId'];
								$fileName		=	$row['fileName'];
								$fileExt		=	$row['fileExt'];
								$size			=	$row['size'];
								$fileAddeddate	=	showDate($row['addedOn']);
						?>
							<tr>
								<td width="5%" class="smalltext2" valign="top"><b><?php echo $i;?>)</b></td>
								<td valign="top">
									<?php
						echo "<a href='".SITE_URL_EMPLOYEES."/download-instructions.php?ID=$instructionId&memberId=$customerId'  class='linkstyle6'><b>".$fileName.".".$fileExt."</b></a>";
						echo "&nbsp;<font class='smalltext1'>".getFileSize($size)."<br>(".$fileAddeddate.")</font>";
									?>
								</td>
							</tr>
							<tr>
								<td colspan="2" height='5'></td>
							</tr>
						<?php
							}
						?>
					</table>
				</td>
			</tr>
			<?php
				}
			?>
		  </table>
		</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="3" cellspacing="3" border="0">
				<tr>
					<td width="45%" class="text"><b>EMPLOYEES NOTE</b></td>
					<td>
						<input type="button" name="submit" onClick="javascript:openNoteWindow(<?php echo $customerId;?>);" value="EDIT">
					</td>
				</tr>
				<tr>
				<td colspan="3">
					<?php
						if(!empty($splInstructionOfCustomer))
						{
					?>
					<div style='width:500px;border:0px solid #ff0000;overflow:auto;height:180px'>
						<table width="100%" cellpadding="3" cellspacing="3" border="0">
							<tr>
								<td class="textstyle">
									<p align="justify">
										<?php echo nl2br($splInstructionOfCustomer);?>
									</p>
								</td>
							</tr>
						</table>
					</div>
					<br>
					<?php
						}
					?>
				</td>
			</tr>
			<?php
			$query	=	"SELECT * FROM customer_instructions_file WHERE memberId=$customerId AND uploadedBy='".EMPLOYEES."' ORDER BY fileName";
			$result	=	dbQuery($query);
			if(mysql_num_rows($result))
			{
			?>
			<tr>
				<td colspan="3" class="text"><b>EMPLOYEE NOTE FILES</b></td>
			</tr>
			<tr>
				<td colspan="3">
					<table width="100%" cellpadding="3" cellspacing="3" border="0">
						<?php
							$i	=	0;
							while($row	=	mysql_fetch_assoc($result))
							{
								$i++;
								$instructionId	=	$row['instructionId'];
								$fileName		=	$row['fileName'];
								$fileExt		=	$row['fileExt'];
								$size			=	$row['size'];
						?>
							<tr>
								<td width="5%" class="smalltext2" valign="top"><b><?php echo $i;?>)</b></td>
								<td valign="top" width="80%" valign="top">
									<?php
										echo "<a href='".SITE_URL_EMPLOYEES."/download-instructions.php?ID=$instructionId&memberId=$customerId'  class='linkstyle6'><b>".$fileName.".".$fileExt."</b></a>";
										echo "&nbsp;<font class='smalltext1'>".getFileSize($size)."</font>"
									?>
								</td>
								<td valign="top">
									<a href="javascript:deleteNoteInstructionsFile(<?php echo $instructionId?>,'<?php echo $pageUrl;?>',<?php echo $customerId;?>,<?php echo $orderId?>)">
										<img src="<?php echo SITE_URL;?>/images/delete.gif" border="0">
									</a>
								</td>
							</tr>
							<tr>
								<td colspan="3" height='5'></td>
							</tr>
						<?php
							}
						?>
					</table>
				</td>
			</tr>
			<?php
				}
			?>

			</table>
		</td>
		<td valign="top">&nbsp;</td>
		<td valign="top">
			<table width="100%" cellpadding="3" cellspacing="3" border="0">
			<tr>
				<td colspan="6" class="text"><b>YOUR PREVIOUS ORDER RATINGS FROM QA</b></td>
			</tr>
			<?php
			if($result	=	$orderObj->previousOrdersQaComments($orderId,$customerId,$acceptedBy,5))
			{
			?>
			<br>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
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
					<div style='width:500px;border:0px solid #ff0000;overflow:auto;height:180px'>
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
			<?php
				}
				else
				{
			?>
			<tr>
				<td class="error" align="center"><b>No Record Found !!</b></td>
			</tr>
			<?php
				}
			?>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="3" cellspacing="3" border="0">
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td colspan="6" class="text"><b>PREVIOUS EMPLOYEE MESSAGES FOR ORDERS - <?php echo $customerName;?></b></td>
					</tr>
				<?php
					if($result		=	$orderObj->previousOrdersEmployeesMessages($orderId,$customerId,10))
					{
				?>
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
						<div style='width:650px;border:0px solid #ff0000;overflow:auto;height:150px'>
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
									<td class="textstyle" valign="top" width="30%">
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
				<?php
					}
					else
					{
				?>
					<tr>
						<td class="error" align="center"><b>No Record Found !!</b></td>
					</tr>
				<?php		
					}
				?>
			</table>
		</td>
		<td>&nbsp;</td>
		<td valign="top">
			<table width="100%" cellpadding="3" cellspacing="3" border="0">
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td colspan="6" class="text"><b>PREVIOUS ORDER MESSAGES FROM - <?php echo $customerName;?></b></td>
					</tr>
					<?php
						if($result		=	$orderObj->previousOrdersMessages($orderId,$customerId,10))
						{
					?>
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
								<div style='width:500px;border:0px solid #ff0000;overflow:auto;height:150px'>
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
				<?php
					}
					else
					{
						?>
					<tr>
						<td class="error" align="center"><b>No Record Found !!</b></td>
					</tr>
					<?php		
					}
				?>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="3" cellspacing="3" border="0">
				<tr>
					<td colspan="2" class="text">EXISTING EMPLOYEE MESSAGES</td>
				</tr>
				<tr>
					<td colspan="2" height="5"></td>
				</tr>
				<?php
					if($result	=	$orderObj->getOrderEmployeeMessages($orderId))
					{
						while($row			=	mysql_fetch_assoc($result))
						{
							$t_empMessageId			=	$row['messageId'];
							$t_empMessage			=	stripslashes($row['message']);
							$t_empMessageAddedOn	=	showDate($row['addedOn']);
							$t_empMessageBy			=	$row['messageBy'];
							$t_empMessageByName		=	$employeeObj->getEmployeeName($t_empMessageBy);
							
							echo "<tr><td colspan='2' class='smalltext2'>".nl2br($t_empMessage)."</td></tr>";
							echo "<tr><td class='title3'><br>By : <b>$t_empMessageByName</b>, on $t_empMessageAddedOn</td></tr>";
							echo "<tr><td colspan='2'><hr size='1' width='100%' color='#bebebe'></td></tr>";
						}
					}
					else
					{
					?>
					<tr>
						<td class="error" align="center"><b>No Record Found !!</b></td>
					</tr>
					<?php		
					}
				?>
			</table>
		</td>
		<td>&nbsp;</td>
		<td valign="top">
			<table width="100%" cellpadding="3" cellspacing="3" border="0">
				<tr>
						<td colspan="2" class="text">MESSAGES BETWEEN CUSTOMER AND EMPLOYEE</td>
				</tr>
				<tr>
					<td colspan="2" height="5"></td>
				</tr>
				<?php
					if($result	=	$orderObj->getOrderMessages($orderId,$customerId))
					{
						while($row			=	mysql_fetch_assoc($result))
						{
							$t_messageId	=	$row['messageId'];
							$t_message		=	stripslashes($row['message']);
							$addedOn		=	showDate($row['addedOn']);
							$addedTime		=	$row['addedTime'];
							$messageBy		=	$row['messageBy'];
							$hasMessageFiles=	$row['hasMessageFiles'];
							$fileName		=	$row['fileName'];
							$fileExtension	=	$row['fileExtension'];
							$fileSize		=	$row['fileSize'];
							if($messageBy   ==  EMPLOYEES)
							{
								$employeeId		=	$row['employeeId'];
								$employeeName	=	$employeeObj->getEmployeeName($employeeId);
								echo "<tr><td class='smalltext2'><b>Message From ".$employeeName." to - ".$customerName." on $addedOn</b></td><td></tr>";
							}
							elseif($messageBy   ==  CUSTOMERS)
							{
								echo "<tr><td class='smalltext2'><b>Message From ".$customerName." to Employee  on $addedOn</b></td></tr>";
							}
							echo "<tr><td class='smalltext2'>".nl2br($t_message)."</td></tr>";
							if($hasMessageFiles == 1 && empty($isDeleted))
							{
								echo "<tr><td colspan='2' class='title3'><b>Uploaded File : </b>";
								echo "<a href='".SITE_URL_EMPLOYEES."/download-message-files.php?ID=$t_messageId'  class='linkstyle6'><b>".$fileName.".".$fileExtension."</b></a>&nbsp;&nbsp;<font class='smalltext'>".getFileSize($fileSize)."</font>";
								echo "</td></tr>";
							}
							echo "<tr><td colspan='2'><hr size='1' width='100%' color='#bebebe'></td></tr>";
						}
					}
					else
					{
					?>
					<tr>
						<td class="error" align="center"><b>No Record Found !!</b></td>
					</tr>
					<?php		
					}
				?>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td valign="top">

		</td>
		<td>&nbsp;</td>
		<td valign="top">

		</td>
	</tr>
</table>
