<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/new-top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/common.php");
	$employeeObj				=  new employee();
	$memberObj					=  new members();
	$orderObj					=  new orders();
	$commonObj					=  new common();
	$messgeText					=  "SEND";
	$customerEmail				=	"";
	$customerSecondaryEmail		=	"";
	$a_managerEmails			=	array();
	$message					=	"";
	$a_managerEmails			=  $commonObj->getMangersEmails();

	$calculateReplyRateFrom		=	getPreviousGivenDate($nowDateIndia,7);

	$a_totalUnRepliedQa			=	$orderObj->getTotalUnrepliedratedOrders($calculateReplyRateFrom,$nowDateIndia,$s_employeeId);

	$formSearch					=	SITE_ROOT_EMPLOYEES."/forms/search-general-order-form.php";

	if(isset($_GET['orderId']) && isset($_GET['customerId']))
	{
		$orderId		=	$_GET['orderId'];
		$customerId		=	$_GET['customerId'];
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
		exit();
	}

	//$isExistRatings			=	$orderObj->isRequiredOnRatedComment($orderId,$s_employeeId);

	if(empty($a_totalUnRepliedQa))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
		exit();
	}
	else
	{
		if(!in_array($orderId,$a_totalUnRepliedQa))
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
			exit();
		}
	}

?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
<tr>
	<td colspan="8" class="heading1">
		:: REPLY ON CUSTOMER RATED ORDER ::
	</td>
</tr>
<tr>
	<td colspan="8" height="5"></td>
</tr>
</table>
<?php
	include($formSearch);
	include(SITE_ROOT_EMPLOYEES	. "/includes/view-customer-order1.php");

	$processQaEmployee			=	0;
	$columnFields				=	"hasRatingExplanation=1";

	if($acceptedBy == $s_employeeId && $qaDoneBy != $s_employeeId)
	{
		$processQaEmployee		=	1;
		$columnFields			=	"hasRatingExplanation=1";
	}
	elseif($acceptedBy != $s_employeeId && $qaDoneBy == $s_employeeId)
	{
		$processQaEmployee		=	2;
		$columnFields			=	"hasRatingQaExplanation=1";
	}
	elseif($acceptedBy == $s_employeeId && $qaDoneBy == $s_employeeId)
	{
		$processQaEmployee		=	3;
		$columnFields			=	"hasRatingExplanation=1,hasRatingQaExplanation=1";
	}

	function findexts($filename) 
	{ 
		$ext        =    "";
		$filename   =    strtolower($filename) ; 
		$a_exts		=	 explode(".",$filename);
		$total		=	 count($a_exts);
		if($total > 1){
			$ext	=	 end($a_exts);		
		}		
		return $ext; 
	} 
	function getFileName($fileName)
	{
		$dotPosition	=  strpos($fileName, "'");
		if($dotPosition == true)
		{
			$fileName	=	stringReplace("'", "", $fileName);
		}
		$doubleDotPosition	  =  strpos($fileName, '"');
		if($doubleDotPosition == true)
		{
			$fileName	=	stringReplace('"', '', $fileName);
		}
		$fileExtPos		=  strrpos($fileName, '.');
		$fileName		=  substr($fileName,0,$fileExtPos);
		
		return $fileName;
	}
?>
<a name="addComment"></a>
<?php
	if($result	=	$orderObj->getOrderEmployeeRatedCommentMessages($orderId))
	{
?>
<br>
<table width='100%' align='center' cellpadding='3' cellspacing='2' border='0'>
	<tr>
		<td colspan="2" class="text">EXISTING COMMENT ON CUSTOMER RATINGS</td>
	</tr>
	<tr>
		<td colspan="2" height="5"></td>
	</tr>
	<?php
		while($row			=	mysqli_fetch_assoc($result))
		{
			$t_empReplyId			=	$row['replyId'];
			$t_empReplyMessage		=	stripslashes($row['comment']);
			$t_empReplyAddedOn		=	showDate($row['addedOn']);
			$t_empReplyBy			=	$row['addedby'];
			$t_processQaEmployee	=	$row['processQaEmployee'];
			$t_empReplyByName		=	$employeeObj->getEmployeeName($t_empReplyBy);

			$addedbyEmployeeType	=	"";
			if($t_processQaEmployee	==	1)
			{
				$addedbyEmployeeType=	"Processed Employee";
			}
			elseif($t_processQaEmployee	==	2)
			{
				$addedbyEmployeeType=	"QA Done Employee";
			}
			elseif($t_processQaEmployee	==	3)
			{
				$addedbyEmployeeType=	"Processed & QA Done Employee";
			}

			
			echo "<tr><td colspan='2' class='smalltext2'>".nl2br($t_empReplyMessage)."</td></tr>";
			echo "<tr><td class='smalltext2'><br>By : <b>$t_empReplyByName</b>, on $t_empReplyAddedOn</td></tr>";
			echo "<tr><td class='smalltext2'><br>Employee Type : <b>$addedbyEmployeeType</b></td></tr>";
			echo "<tr><td colspan='2'><hr size='1' width='100%' color='#bebebe'></td></tr>";
		}
?>
</table>
<?php
	}
	
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		$message		=	trim($message);
		$t_explanation	=	$message;
		$message		=	makeDBSafe($message);
		
		$query	=	"INSERT INTO reply_on_orders_rates SET orderId=$orderId,addedby=$s_employeeId,comment='$message',processQaEmployee=$processQaEmployee,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'";
		dbQuery($query);

		dbQuery("UPDATE members_orders SET ".$columnFields." WHERE orderId=$orderId AND memberId=$customerId");

		
		include(SITE_ROOT		.   "/classes/email-templates.php");
		$emailObj				=	new emails();

		$toEmail				=	$customerEmail; 
		$uniqueTemplateName		=	"TEMPLATE_SENDING_CUSTOMER_RATING_EXPLANATION";
		$a_templateSubject		=	array("{orderNo}"=>$orderAddress);
		$customerRatedOnOrder	=	$a_existingRatings[$rateGiven];
		$memberRateMsg			=	nl2br($memberRateMsg);
		$t_explanation			=	nl2br($t_explanation);

		$a_templateData			=	array("{name}"=>$customerName,"{orderNo}"=>$orderAddress,"{orderType}"=>$orderText,"{orderCompletedOn}"=>$orderCompletedOn,"{customerRating}"=>$customerRatedOnOrder,"{customerRatingComments}"=>$memberRateMsg,"{agentRatingExp}"=>$t_explanation,"{feedbackOfText}"=>"ieIMPACT agent acknowledged on your feedback rating","{feedbackOfText1}"=>"Your feedback rating has been acknowledged.","{orderAcceptedByText}"=>"","{orderAcceptedByDot}"=>"","{orderAcceptedByName}"=>"","{orderQaByText}"=>"","{orderQaByDot}"=>"","{orderQaByName}"=>"");

		//include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

		$acceptedByEmail			=	$employeeObj->getSingleQueryResult("SELECT email FROM employee_details  WHERE employeeId=$acceptedBy","email");

		$qaByEmail					=	$employeeObj->getSingleQueryResult("SELECT email FROM employee_details  WHERE employeeId=$qaDoneBy","email");

		if(!empty($acceptedByEmail))
		{
			$toEmail				=	$acceptedByEmail; 
			$managerEmployeeEmailSubject	=	$customerName." feedback rating has been acknowledged";
			$uniqueTemplateName		=	"TEMPLATE_SENDING_CUSTOMER_RATING_EXPLANATION";
			$a_templateData			=	array("{name}"=>$acceptedByName,"{orderNo}"=>$orderAddress,"{orderType}"=>$orderText,"{orderCompletedOn}"=>$orderCompletedOn,"{customerRating}"=>$customerRatedOnOrder,"{customerRatingComments}"=>$memberRateMsg,"{agentRatingExp}"=>$t_explanation,"{feedbackOfText}"=>"ieIMPACT agent acknowledged on $customerName feedback rating","{feedbackOfText1}"=>"$customerName feedback rating has been acknowledged.","{orderAcceptedByText}"=>"","{orderAcceptedByDot}"=>"","{orderAcceptedByName}"=>"","{orderQaByText}"=>"","{orderQaByDot}"=>"","{orderQaByName}"=>"");

			//include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
		}

		if(!empty($qaByEmail) && $qaByEmail != $acceptedByEmail)
		{
			$toEmail				=	$qaByEmail;
			$managerEmployeeEmailSubject	=	$customerName." feedback rating has been acknowledged";
			$uniqueTemplateName		=	"TEMPLATE_SENDING_CUSTOMER_RATING_EXPLANATION";
			$a_templateData			=	array("{name}"=>$qaDoneByText,"{orderNo}"=>$orderAddress,"{orderType}"=>$orderText,"{orderCompletedOn}"=>$orderCompletedOn,"{customerRating}"=>$customerRatedOnOrder,"{customerRatingComments}"=>$memberRateMsg,"{agentRatingExp}"=>$t_explanation,"{feedbackOfText}"=>"ieIMPACT agent acknowledged on $customerName feedback rating","{feedbackOfText1}"=>"$customerName feedback rating has been acknowledged.","{orderAcceptedByText}"=>"","{orderAcceptedByDot}"=>"","{orderAcceptedByName}"=>"","{orderQaByText}"=>"","{orderQaByDot}"=>"","{orderQaByName}"=>"");

			//include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
		}
		
		if(!empty($a_managerEmails))
		{
			foreach($a_managerEmails as $k=>$value)
			{
				list($managerEmail,$managerName)	=	explode("|",$value);
				$toEmail							=	$managerEmail;
				$managerEmployeeEmailSubject		=	$customerName." feedback rating has been acknowledged";
				$uniqueTemplateName					=	"TEMPLATE_SENDING_CUSTOMER_RATING_EXPLANATION";
				$a_templateData						=	array("{name}"=>$managerName,"{orderNo}"=>$orderAddress,"{orderType}"=>$orderText,"{orderCompletedOn}"=>$orderCompletedOn,"{customerRating}"=>$customerRatedOnOrder,"{customerRatingComments}"=>$memberRateMsg,"{agentRatingExp}"=>$t_explanation,"{feedbackOfText}"=>"ieIMPACT agent acknowledged on $customerName feedback rating","{feedbackOfText1}"=>"$customerName feedback rating has been acknowledged.","{orderAcceptedByText}"=>"Order Processed By","{orderAcceptedByDot}"=>":","{orderAcceptedByName}"=>$acceptedByName,"{orderQaByText}"=>"Order QA By","{orderQaByDot}"=>":","{orderQaByName}"=>$qaDoneByText);

				include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

			}

			$toEmail		=	"rishi@ieimpact.com";
			include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
		}

		if(isset($_GET['backOrderId']) && isset($_GET['backCustomerId']))
		{
			$backOrderId		    =	$_GET['backOrderId'];
			$backCustomerId		    =	$_GET['backCustomerId'];
			if(!empty($backOrderId) && !empty($backCustomerId)){
				
				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES ."/view-order-others.php?orderId=$backOrderId&customerId=$backCustomerId");
				exit();
			}
		}

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES ."/add-comment-on-customer-rated.php?orderId=$orderId&customerId=$customerId#addComment");
		exit();
	}
?>
<script type="text/javascript">
function checkValidMessage()
{
	form1	=	document.sendEmployeeRatedMessage;
	if(form1.message.value == "" || form1.message.value == "Enter Your Message Here")
	{
		alert("Please Enter Your Message !!");
		form1.message.focus();
		return false;
	}
}
function textCounter(field,countfield,maxlimit)
{
	if(field.value.length > maxlimit)
	{
		field.value = field.value.substring(0, maxlimit);
	}
	else
	{
		countfield.value = maxlimit - field.value.length;
	}
 }
 </script>
 <br>
 <a name="sendMessages"></a>
<form name="sendEmployeeRatedMessage" action=""  method="POST" onsubmit="return checkValidMessage();">
<table width='100%' align='center' cellpadding='3' cellspacing='0' border='0'>
<tr>
	<td colspan="3" class="text">Add Comment On This customer Rate</td>
</tr>
<tr>
	<td colspan="3" height="5"></td>
</tr>
<tr>
	<td colspan="3" class="textstyle1"><b>Rate Note By Customer : <font color="#ff0000"><?php echo $memberRateMsg;?></font></b></td>
</tr>
<tr>
	<td colspan="3" height="5"></td>
</tr>
<tr>
	<td colspan="3">
		<?php
			echo "<a href='".SITE_URL_EMPLOYEES."/send-message-pdf-customer.php?orderId=$orderId&customerId=$customerId#sendMessages' class='link_style13'>SEND MSG</a>";
		?>
		<br><br><font class="textstyle1"><b>[ THIS MESSAGE WILL NOT GO TO CUSTOMER, ALSO SEND A MESSAGE TO CUSTOMER USING THE SEND MESSAGE LINK ABOVE. ]</b></font>
	</td>
</tr>
<tr>
	<td colspan="3" height="5"></td>
</tr>
<tr>
	<td valign="top" colspan="3">
		<textarea name="message" rows="7" cols="70" wrap="hard" onKeyDown="textCounter(this.form.message,this.form.remLentext1,1000);" onKeyUp="textCounter(this.form.message,this.form.remLentext1,1000);" onFocus="if(this.value=='Enter Your Message Here') this.value='';" onBlur="if(this.value=='') this.value='Enter Your Message Here';" style="border:1px solid #333333;" oncopy="return false" onpaste="return false" oncut="return false"><?php echo stripslashes(htmlentities($message,ENT_QUOTES))?></textarea>

		<br><font class="smalltext2">Characters Left: <input type="textbox" readonly name="remLentext1" size=2 value="1000" style="border:0"></font>
	</td>
</tr>
<tr>
	<td height="5" colspan="3"></td>
</tr>
<tr>
	<td colspan="2">
		<input type="submit" name="submit" value="Submit">
		<input type="button" name="submit" onClick="history.back()" value="Back">
		<input type="hidden" name="formSubmitted" value="1">
	</td>
	<td>
		<?php
			include(SITE_ROOT_EMPLOYEES . "/includes/next-previous-order.php");
		?>
	</td>
</tr>
</table>
</form>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>