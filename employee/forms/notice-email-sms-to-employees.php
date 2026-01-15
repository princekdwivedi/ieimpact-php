<script type="text/javascript" src="<?php echo SITE_URL;?>/admin/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL;?>/admin/scripts/calender.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL?>/script/common-ajax.js"></script>

<script type="text/javascript">
function checkValidMessage()
{
	form1	=	document.messageToEmployee;
	if(form1.title.value == "")
	{
		alert("Please enter message heading.");
		form1.title.focus();
		return false;
	}
	if(form1.message.value == "")
	{
		alert("Please enter message.");
		form1.message.focus();
		return false;
	}

	if(form1.sendingNotice.checked == false &&  form1.sendingEmail.checked == false && form1.sendingSms.checked == false)
	{
		alert("Please select a sending option.");
		return false;
	}

	if(form1.sendingSms.checked == true)
	{
		if(form1.message.value.length > 160)
		{
			alert("Please enter message within 160 chracaters for sending SMS.");
			form1.message.focus();
			return false;
		}
	}


	var confirmation = window.confirm("Please confirm the department before sending the message?");
	if(confirmation == true)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function deleteMessage(messageId,rec)
{
	var confirmation = window.confirm("Are you sure to delete this message?");
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/email-sms-notice-common-page.php?messageId="+messageId+"&rec="+rec+"&isDelete=1";
	}
}

function showHideFormDisplaying()
{
	if(document.getElementById('noticeId').checked  == true)
	{
		document.getElementById('displayMessageDates').style.display = 'inline';
	}
	else{
		document.getElementById('displayMessageDates').style.display = 'none';
	}
}
</script>
<form name="messageToEmployee" method='POST' action="" onsubmit="return checkValidMessage();">
<table width="98%" border="0" align="center" cellpadding="4" cellspacing="2" valign="top">
	<tr>
		<td colspan="6" class="smalltext2">
			<font style="color:#ff0000;"><?php echo $errorMsg;?></font>
		</td>
	</tr>
	<tr>
		<td width="24%" class="smalltext2"><b>SEND THIS MESSAGE TO DEPARTMENT</b></td>
		<td width="2%" class="smalltext2"><b>:</b></td>
		<td class="textstyle1">
			<b>
			<?php
				echo $departmentText;
			?>
			</b>
			<input type='hidden' name='departmentId' value='<?php echo $department;?>'>
			
		</td>
	</tr>
	<tr>
		<td class="smalltext2"><b>EMPLOYEES UNDER MANAGER</b></td>
		<td class="smalltext2"><b>:</b></td>
		<td class="textstyle1">
			<?php
				$change_url =	SITE_URL_EMPLOYEES."/get-employee-under-manager.php?departmentId=$department&manager="
			?>
			<select name="manager" onchange="commonFunc('<?php echo $change_url;?>','displayAllEmployee', this.value);">
				<option value="0">All</option>
				<?php
					foreach($a_managers as $key=>$value)
					{
						$select	=	"";
						if($manager	==	$key)
						{
							$select	=	"selected";
						}

						echo "<option value='$key' $select>$value</option>";
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top"><b>SEND THIS MESSAGE TO </b></td>
		<td class="smalltext2"  valign="top"><b>:</b></td>
		<td valign="top">
			<div  id="displayAllEmployee">
				<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" valign="top">
					<tr>
						<td>
							<select name="employeeId[]" multiple style="width:350px;height:150px;">
								<option value="0">All Employee</option>
								<?php									
									while($row	=	mysqli_fetch_assoc($employee_lists))
									{
										$t_employeeId	=	$row['employeeId'];
										$firstName		=	$row['firstName'];
										$lastName		=	$row['lastName'];
										$employeeName	=	$firstName." ".$lastName;

										$select			=	"";
										if(in_array($t_employeeId, $a_employeeId))
										{
											$select		=	"selected";
										}
										echo "<option value='$t_employeeId' $select>".ucwords($employeeName)."</option>";
									}									
								?>
							</select>
						</td>
					</tr>
				</table>
			</div>				
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
		<td class="smalltext1">
			<?php echo $msg;?>
		</td>
	</tr>
	<tr>
		<td width="20%" class="smalltext2"><b>MESSAGE HEADING</b></td>
		<td width="2%" class="smalltext2"><b>:</b></td>
		<td>
			<input type="text" name="title" value="<?php echo $title;?>" size="86" maxlength="150">
		</td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top"><b>MESSAGE</b></td>
		<td class="smalltext2"  valign="top"><b>:</b></td>
		<td valign="top">
			<textarea name="message" cols="65" rows="8"><?php echo $message;?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
		<td class="smalltext1" colspan="4">
			[Note : Please don't add your name as from, it will be dynamically displayed]
		</td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top"><b>SENDING</b></td>
		<td class="smalltext2"  valign="top"><b>:</b></td>
		<td class="smalltext24" valign="top">
			<input type="checkbox" name="sendingNotice" id="noticeId" value="1" onclick="showHideFormDisplaying();">Notice&nbsp;
			<input type="checkbox" name="sendingEmail" value="1">Email&nbsp;
			<input type="checkbox" name="sendingSms" value="1">SMS&nbsp;&nbsp;<font class="smalltext2">[Messages can be only send to India's mobile numbers.]</font>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<div id="displayMessageDates" style="display:none;">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td class="smalltext2" width="24%"><b>DISPLAY MESSAGE FROM</b></td>
						<td class="smalltext2"><b>:</b></td>
						<td width="10%">
							<input type="text" name="fromDate" value="<?php echo $fromDate;?>" class="textbox" id="from" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('from','ddmmyyyy')"><img src="<?php echo SITE_URL;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
						</td>
						<td width="12%" class="smalltext2" align="right"><b>TO DISPLAY TILL</b></td>
						<td width="2%" class="smalltext2"><b>:</b></td>
						<td>
							<input type="text" name="toDate" value="<?php echo $toDate;?>" class="textbox" id="to" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('to','ddmmyyyy')"><img src="<?php echo SITE_URL;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<!--<tr>
		<td colspan="8" class="error"><input type="checkbox" name="sendSMS" value="1"><b>CLICK HERE TO SEND THIS MESSAGE AS SMS TO EMPLOYEE MOBILE</b></td>
	</tr>-->
	<tr>
		<td colspan="2"></td>
		<td colspan="6">
			<input type="image" name="name" src="<?php echo SITE_URL;?>/images/submit.jpg" border="0">
			<input type='hidden' name='formSubmitted' value='1'>
		</td>
	</tr>
</table>
</form>
<br><br>
<?php
	$start					  =	0;
	$recsPerPage	          =	10;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"employee_messages";
	$pagingObj->selectColumns = "*";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/send-notice-to-employees.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
?>
<table width="98%" border="0" align="center" cellpadding="4" cellspacing="2" valign="top">
	<tr>
		<td colspan="8" class="smalltext2">
			<b>View Existing Messages</b>
		</td>
	</tr>
	<tr>
		<td colspan="8">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
	<tr>
		<td width="3%" class="smalltext2">&nbsp;</td>
		<td width="20%" class="smalltext2"><b>Heading</b></td>
		<td width="28%" class="smalltext2"><b>Message Sent</b></td>
		<td width="12%" class="smalltext2"><b>Message To</b></td>
		<td width="8%" class="smalltext2"><b>Department</b></td>
		<td width="9%" class="smalltext2"><b>Display From</b></td>
		<td width="9%" class="smalltext2"><b>Display To</b></td>
		<td>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan="8">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
<?php
		$i	=	$recNo;
		while($row	=   mysqli_fetch_assoc($recordSet))
		{
			$i++;
			$messageId				=	$row['messageId'];
			$title					=	$row['title'];
			$message				=	$row['message'];
			$displayFrom			=	showDate($row['displayFrom']);
			$displayTo				=	showDate($row['displayTo']);
			$employeeId				=	$row['employeeId'];
			$isRead					=	$row['isRead'];
			$readOn					=	$row['readOn'];
			$isReplied				=	$row['isReplied'];
			$replyText				=	$row['replyText'];
			$repliedOn				=	$row['repliedOn'];
			$isDeleted				=	$row['isDeleted'];
			$deletedOn				=	$row['deletedOn'];
			$departmentId			=	$row['departmentId'];
			if($departmentId == 0)
			{
				$departmentText		=	"All";
			}
			else
			{
				$departmentText		=	$a_newDepartment[$departmentId];
			}


			$title					=	stripslashes($title);
			$message				=	stripslashes($message);
			$message				=	nl2br($message);
			$employeeName			=			$employeeObj->getEmployeeName($employeeId);
			if(empty($employeeName))
			{
				$sendTo				=	"All Employee";
			}
			else
			{
				$sendTo				=	$employeeName;
			}

?>
	<tr>
		<td class="smalltext2" valign="top"><?php echo $i;?>)</td>
		<td class="smalltext2" valign="top"><?php echo $title;?></td>
		<td class="smalltext2" valign="top"><?php echo $message;?></td>
		<td class="smalltext2" valign="top"><?php echo $sendTo;?></td>
		<td class="smalltext2" valign="top"><?php echo $departmentText;?></td>
		<td class="smalltext2" valign="top"><?php echo $displayFrom;?></td>
		<td class="smalltext2" valign="top"><?php echo $displayTo;?></td>
		<td valign="top" class="smalltext2">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/email-sms-notice-common-page.php?messageId=<?php echo $messageId?>&recNo=<?php echo $recNo;?>">Edit</a> | <a href="javascript:deleteMessage(<?php echo $messageId?>,<?php echo $recNo;?>)">Delete</a>
		</td>
	</tr>
	<?php
		if(!empty($employeeId))
		{
	?>
	<tr>
		<td colspan="8" class="smalltext2">
	<?php
		if($isRead || $isReplied || $isDeleted)
		{
			if(!empty($isRead))
			{
				echo "Read Message On - ".showDate($readOn)." | &nbsp;";
			}
			if(!empty($isDeleted))
			{
				echo "Deleted Message On - ".showDate($deletedOn)." | &nbsp;";
			}
			if(!empty($isReplied))
			{
				echo "Replied To Message On - ".showDate($repliedOn);
			}
			if($replyText)
			{
				echo "<br><br>Reply To Your Message : ".$replyText;
			}
		}
	?>
		</td>
	</tr>
	<?php
		}	
	?>
	<tr>
		<td colspan="8">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
<?php
		}
?>
</table>
<?php
		echo "<table width='100%'><tr><td align='right'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr></table>";
	}
?>