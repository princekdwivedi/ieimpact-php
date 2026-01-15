<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");
	include(SITE_ROOT			. "/includes/send-mail.php");
	$employeeObj				=  new employee();
	$memberObj					=  new members();
	$orderObj					=  new orders();
	$validator					=  new validate();
	$replyId					=	0;
	$doneQa						=	0;
	$qaChecked					=	"";
	$errorCorrected				=	"";
	$feedbackToEmployee			=	"";
	$timeSpentQa				=	"";


	$qaHeadingText				=  "VIEW CUSTOMER BPO ORDER WITH REPLIED ORDER";
	if(isset($_GET['orderId']) && isset($_GET['customerId']))
	{
		$orderId		=	$_GET['orderId'];
		$customerId		=	$_GET['customerId'];
		if(!in_array($customerId,$a_qaCustomers))
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
			exit();
		}
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
		exit();
	}

	if(isset($_GET['doneQa']) && $_GET['doneQa'] == 1)
	{
		$doneQa			 =	$_GET['doneQa'];
		$orderStatus     = $orderObj->getOrderStatus($orderId,$customerId);
		if($orderStatus != 1)
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
			exit();
		}
		else
		{
			$qaHeadingText	=  "MARKED CUSTOMER ORDER AS QA DONE";

			if(isset($_GET['replyId']))
			{
				$replyId		=	(int)$_GET['replyId'];
				
				if(!empty($replyId))
				{
					$orderObj->markOrderQaDone($orderId,$replyId,$customerId,$s_employeeId);
				}

				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES."/pdf-completed-qa-orders.php");
				exit();
			}
		}

	}
	
?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
<tr>
	<td colspan="7" height="20"></td>
</tr>
<tr>
	<td colspan="8" class="heading1">
		:: <?php echo $qaHeadingText;?> ::
	</td>
</tr>
<tr>
	<td colspan="8" height="5"></td>
</tr>
</table>
<?php
	include(SITE_ROOT_EMPLOYEES	. "/includes/bpo-customer-order-details.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/bpo-order-details.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/view-bpo-reply-details.php");


	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		//pr($_REQUEST);
		//die();
		$qaChecked			=	addslashes($qaChecked);
		$errorCorrected		=	addslashes($errorCorrected);
		$feedbackToEmployee	=	addslashes($feedbackToEmployee);

		//$validator ->checkField($qaChecked,"","Please enter what you done in QA !!");
		//$validator ->checkField($errorCorrected,"","Please enter what error found and corrected !!");
		//$validator ->checkField($feedbackToEmployee,"","Please enter feedback to employee !!");
		$validator ->checkField($timeSpentQa,"","Please enter total time spent in QA !!");
		if(empty($isRateSelectedByQa))
		{
			$validator ->setError("Please rate the employees reply files !!");
		}
		$dataValid	 =	$validator ->isDataValid();
		if($dataValid)
		{	
			$orderObj->markOrderQaDone($orderId,$replyId,$customerId,$s_employeeId);
			
			dbQuery("UPDATE members_orders_reply SET timeSpentQa=$timeSpentQa WHERE replyId=$replyId AND orderId=$orderId");

			//dbQuery("UPDATE members_orders_reply SET qaChecked='$qaChecked',errorCorrected='$errorCorrected',feedbackToEmployee='$feedbackToEmployee',timeSpentQa=$timeSpentQa WHERE replyId=$replyId AND orderId=$orderId");

			if(!empty($isRateSelectedByQa) && !empty($selectedRate))
			{
				$rateByQa			=	$selectedRate;
				if(!empty($qaRateMessage))
				{
					$qaRateMessage	=	addslashes($qaRateMessage);
				}
				else
				{
					$qaRateMessage	=	"";
				}
				dbQuery("INSERT employee_miscellaneous_details SET orderId=$orderId,rateByQa=$rateByQa,qaRateMessage='$qaRateMessage',addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");
			}
				
			$a_managerEmails	=	$orderObj->getAllMangersEmails();

			if($result	=	$orderObj->getOrderDetails($orderId,$customerId))
			{
				$row			=	mysql_fetch_assoc($result);
				$orderAddress	=	stripslashes($row['orderAddress']);
				$orderType		=	$row['orderType'];
				$orderAddedOn	=	$row['orderAddedOn'];
				$firstName		=	stripslashes($row['firstName']);
				$lastName		=	stripslashes($row['lastName']);
				$customerEmail	=	$row['email'];
				$customerSecondaryEmail	=	$row['secondaryEmail'];
				$hasReceiveEmails	=	$row['noEmails'];
				$t_orderAddedOn	=	showDate($orderAddedOn);

				$orderText		=	$a_customerOrder[$orderType];
				$customerName	=   $firstName." ".$lastName;
				$customerName	=	ucwords($customerName);
				
				$t_moneyPerOrder=	$memberObj->getSingleOrderPrice($orderType,$customerId,$orderAddedOn);

				dbQuery("UPDATE members_orders SET cost='$t_moneyPerOrder' WHERE orderId=$orderId");
				
				$orderAcceptedBy = $orderObj->getOrderAcceptedBY($orderId,$customerId);
				if(!empty($orderAcceptedBy))
				{
					$acceptedByName	=	$employeeObj->getEmployeeName($orderAcceptedBy);
					if(empty($acceptedByName))
					{
						$acceptedByName	=	"Unknown";
					}
				}
				else
				{
					$acceptedByName	=	"Unknown";
				}

				$from			=	ORDER_FROM_EMAIL;
				$fromName		=	"ieIMPACT";
				$to				=	$customerEmail; 
				$mailSubject	=	"Reply of your order No - $orderAddress from ieIMPACT";
				$templateId		=	TEMPLATE_SENDING_REPLY_ORDER;
				if($hasReceiveEmails == 0)
				{
					if($refferedBy   != 4)
					{
						$a_templateData	=	array("{name}"=>$customerName,"{orderNo}"=>$orderAddress,"{orderType}"=>$orderText);
						sendTemplateMail($from, $fromName, $to, $mailSubject, $templateId, $a_templateData);

						if(!empty($customerSecondaryEmail))
						{
							sendTemplateMail($from, $fromName, $customerSecondaryEmail, $mailSubject, $templateId, $a_templateData);
						}
					}
					else
					{
						$n_from			=	ORDER_FROM_EMAIL_AAPRAISERAIDE;
						$n_fromName		=	ORDER_FROM_NAME_AAPRAISERAIDE;
						$n_to			=	$customerEmail; 
						$n_templateId	=	TEMPLATE_SENDING_REPLY_ORDER_AAPRAISERAIDE_CUSTOMER;
						$n_mailSubject	=	"Reply of your order No - $orderAddress";

						$a_templateData	=	array("{name}"=>$customerName,"{orderNo}"=>$orderAddress,"{orderType}"=>$orderText,"{messageFromSite}"=>$n_fromName,"{mailFromWesbiteUrl}"=>"http://www.appraiseraide.com","{mailFromWesbiteName}"=>"www.appraiseraide.com");

						sendTemplateMail($n_from, $n_fromName, $n_to, $n_mailSubject, $n_templateId, $a_templateData);

						if(!empty($secondaryEmail))
						{
							sendTemplateMail($n_from, $n_fromName, $n_to, $n_mailSubject, $n_templateId, $a_templateData);
						}
					}
				}
				if(!empty($a_managerEmails))
				{
					foreach($a_managerEmails as $k=>$value)
					{
						list($managerEmail,$managerName)	=	explode("|",$value);
						
						$to2			=	$managerEmail; 
						$mailSubject2	=	"Reply details of Customer - $customerName on order - $orderAddress";
						$templateId2	=	TEMPLATE_SENDING_REPLY_TO_MANAGER;

						$a_templateData2	=	array("{managerName}"=>$managerName,"{orderNo}"=>$orderAddress,"{orderDate}"=>$t_orderAddedOn,"{orderType}"=>$orderText,"{customerName}"=>$customerName,"{acceptedBy}"=>$acceptedByName,"{qaDoneBy}"=>$s_employeeName);

						sendTemplateMail($from, $fromName, $to2, $mailSubject2, $templateId2, $a_templateData2);
					}
				}
			}
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/mark-bpo-qa-order.php?orderId=$orderId&customerId=$customerId");
			exit();
		}
		else
		{
			echo $validator->getErrors();
		}
	}
?>
<script type="text/javascript">
function doneQaOrder(orderId,customerId,replyId)
{
	var confirmation = window.confirm("Are You Sure To Marked This Order As QA Done And Completed Order?");
	if(confirmation == true)
	{
		window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/pdf-pending-qa-orders.php?orderId='+orderId+"&customerId="+customerId+"&replyId="+replyId+"&doneQa=1";
	}
}
function goToEdit(orderId,customerId)
{
	location.href = "<?php echo SITE_URL_EMPLOYEES?>/process-bpo-order.php?orderId="+orderId+"&customerId="+customerId;
}
function checkForNumber()
 {
	k = (document.all)?event.keyCode : arguments.callee.caller.arguments[0].which;
	if(k == 8 || k== 0)
	{
		return true;
	}
	if(k >= 48 && k <= 57 )
	{
		return true;
	}
	else
	{
		return false;
	}
 }
 function checkValidQa()
 {
	//return;
	form1		=	document.markedQaDone;
	if(form1.timeSpentQa.value	==	"")
	{
		alert("Please enter total time spent in QA !!");
		form1.timeSpentQa.focus();
		return false;
	}
	if(form1.isRateSelectedByQa.value	==	"0")
	{
		alert("Please rate in employee replies files !!");
		return false;
	}
 }
 function showRateText(text)
 {
	document.getElementById('showRateText').innerHTML = text;
 }
 function showHideRate(flag)
 {
	if(flag)
	{
		document.getElementById("enterRateEmployeesReply").style.display = 'inline';
		document.getElementById('isRateGiven').value = 1;
	}
	else
	{
		document.getElementById("enterRateEmployeesReply").style.display = 'none';
		document.getElementById('isRateGiven').value = 0;
	}
 }
</script>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>
<form name="markedQaDone" action="" method="POST" onsubmit="return checkValidQa();">
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
<tr>
	<td colspan="3">
		<a name="mark"></a>
	</td>
</tr>
	<?php
		if(!empty($replyId) && !empty($doneQa))
		{
	?>
	<!-- <tr>
		<td colspan="3" height="30"></td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top" width="20%"><b>What Did You Check In QA?</b></td>
		<td class="smalltext2"  valign="top" width="2%"><b>:</b></td>
		<td  valign="top">
			<input type="text" name="qaChecked" size="60" value="<?php echo $qaChecked;?>" style="border:1px solid #333333">
		</td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top" width="20%"><b>Error Found And Corrected</b></td>
		<td class="smalltext2"  valign="top" width="2%"><b>:</b></td>
		<td  valign="top">
			<textarea name="errorCorrected" rows="8" cols="45" style="border:1px solid #333333"><?php echo stripslashes(htmlentities($errorCorrected,ENT_QUOTES))?></textarea>
		</td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top"><b>Feedback For Employee</b></td>
		<td class="smalltext2"  valign="top"><b>:</b></td>
		<td  valign="top">
			<input type="text" name="feedbackToEmployee" size="60" value="<?php echo $feedbackToEmployee;?>" style="border:1px solid #333333">
		</td>
	</tr> -->
	<tr>
		<td class="smalltext2" valign="top" width="20%"><b>Total Time Spent In QA</b></td>
		<td class="smalltext2" valign="top" width="2%"><b>:</b></td>
		<td  valign="top" class="smalltext1">
			<input type="text" name="timeSpentQa" size="10" value="<?php echo $timeSpentQa;?>" onKeyPress="return checkForNumber();" style="border:1px solid #333333">(IN MINITUES)
		</td>
	</tr>
	<?php
		if(empty($rateByQa))
		{
	?>
	<tr>
		<td colspan="3">
			<div id="addOrderRate">
				<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
					<tr>
						<td width="20%" class="smalltext2"><b>Rate Employee Replies</b></td>
						<td width="2%" class="smalltext2"><b>:</b></td>
						<td width="10%">
							<?php
								$url2	=	SITE_URL_EMPLOYEES. "/add-order-rating.php?orderId=$orderId&rateChoose=";
								foreach($a_ratingByQa as $key=>$value)
								{
									echo "<img src='".SITE_URL."/images/emptystar.gif'  width=12 height=12 onMouseOver=\"return showRateText('".$value."');\" onMouseOut=\"document.getElementById('showRateText').innerHTML=''\" onClick=\"return commonFunc('$url2','enterRateEmployeesReply',$key),showHideRate(1);\">";
								}
							?>
						</td>
						<td id="showRateText" class="error" width="20%"></td>
						<td>&nbsp;</td>
					</tr>
				</table>
				<div id="enterRateEmployeesReply">
				
				</div>
				<input type="hidden" id="isRateGiven" name="isRateSelectedByQa" value="0">
			</div>
		</td>
	</tr>
	<?php
		}
		else
		{
	?>
	<tr>
		<td width="25%" class="smalltext2" valign="top"><b>Rate Given By QA</b></td>
		<td width="1%" class="smalltext2" valign="top"><b>:</b></td>
		<td width="10%" class="smalltext2" valign="top">
			<?php
				for($i=1;$i<=$rateGiven;$i++)
				{
					echo "<img src='".SITE_URL."/images/star.gif'  width=12 height=12'>";
				}
			?>
		</td>
		<td class="title" valign="top"><b><?php echo $a_existingRatings[$rateGiven];?></b></td>
	</tr>
	<?php
		if(!empty($qaRateMessage))
		{
	?>
	<tr>
		<td colspan='4' height="3"></td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top"><b>Comments</b></td>
		<td class="smalltext2" valign="top"><b>:</b></td>
		<td colspan="2" valign="top" class="smalltext2">
			<?php
				echo $qaRateMessage;
			?>
		</td>
	</tr>
	<?php
		}
	}
	?>
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
	<tr>
		<td colspan="3">
			<input type='button' name='edit' value='EDIT REPLIED FILES' onClick="return goToEdit(<?php echo $orderId;?>,<?php echo $customerId;?>)">&nbsp;&nbsp;
			<input type="submit" name="submit" value="MARK AS QA DONE">
			<input type="hidden" name="replyId" value="<?php echo $replyId?>">
			<input type="hidden" name="formSubmitted" value="1">
			<!-- <input type="button" name="submit" onClick="javascript:doneQaOrder(<?php echo $orderId;?>,<?php echo $customerId?>,<?php echo $replyId?>)" value="MARK AS QA DONE">&nbsp;&nbsp; -->
		</td>
	</tr>
	<?php
		}	
	?>
	<tr>
		<td colspan="3">
			<input type="button" name="submit" onClick="history.back()" value="BACK">
		</td>
	</tr>
</table>
</form>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>