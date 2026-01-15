<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/includes/send-mail.php");
	include(SITE_ROOT			. "/classes/common.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");
	$employeeObj				= new employee();
	$memberObj					= new members();
	$orderObj					= new orders();
	$commonObj					= new common();
	$validator					= new validate();
	$a_allmanagerEmails			= $commonObj->getMangersEmails();
	$showForm					= false;
	$orderId					= 0;
	$customerId					= 0;
	$status						= 0;
	$employeeId					= 0;
	$errorMsg					= "";
	$explanation 				= "";
	$errorMessageForm			= "You are not authorized to view this page !!";
	
	$auditAddedByText			=	"";

	if(isset($_GET['orderId']) && isset($_GET['customerId']))
	{
		$orderId					=	$_GET['orderId'];
		$customerId					=	$_GET['customerId'];
		$query		                =	"SELECT members_orders.*,completeName FROM members_orders INNER JOIN members ON members_orders.memberId=members.memberId WHERE orderId=$orderId AND members_orders.memberId=$customerId AND members_orders.isVirtualDeleted=0 AND members_orders.status=0";
			$result		           =	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$showForm				=	true;

			$row					=	mysqli_fetch_assoc($result);
			$customerName			=	stripslashes($row['completeName']);
			$orderAddress			=   stripslashes($row['orderAddress']);
			$customersOwnOrderText	=	stripslashes($row['customersOwnOrderText']);
			$orderType				=	$row['orderType'];
			$acceptedBy				=	$row['acceptedBy'];
			$rateGiven				=	$row['rateGiven'];
			$orderAddedOn			=	$row['orderAddedOn'];
			$orderCompletedTat		=	$row['orderCompletedTat'];
			$isHavingEstimatedTime	=	$row['isHavingEstimatedTime'];
			$isAddedTatTiming		=	$row['isAddedTatTiming'];
			$employeeWarningDate	=	$row['employeeWarningDate'];
			$employeeWarningTime	=	$row['employeeWarningTime'];
			$orderAddedOn			=	$row['orderAddedOn'];
			$displayDate			=	showDateMonth($orderAddedOn);
			$orderAddedTime			=	$row['orderAddedTime'];
			$displayTime			=	showTimeFormat($orderAddedTime);
			
			$t_orderAddedOn			=	showDate($orderAddedOn);
			$t_orderCompletedOn		=	showDate($orderCompletedOn);
			
			$orderText				=	$a_customerOrder[$orderType];
			if($orderType			==	6)
			{
				$orderText			=	$orderText."(".$customersOwnOrderText.")";
			}
			$expctDelvText          =   "";
			if($isHavingEstimatedTime==	1 && empty($isAddedTatTiming))
			{
				$expctDelvText		 =	orderTAT1($employeeWarningDate,$employeeWarningTime);
			}
			
		}
	}
?>
<html>
<head>
<TITLE>Marked Post Audit Errors</TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
<body>
<script type="text/javascript">
	function reflectChange()
	{
		window.opener.location.reload();
	}
</script>
<center>
<?php
	if($showForm)
	{
?>
<table width="100%" align="center" border="0" cellspacing="3" cellspacing="3">
	<tr>
		<td colspan="3" class="textstyle1"><b>PLEASE GIVE THE REASON TO ACCEPT THIS ORDER BECAUSE IT IS NOT TATWISE</b></td>
	</tr>
	<tr>
		<td colspan="3" class="textstyle1"><font color='#ff0000;'><b>PLEASE ACCEPT ACCORDING TO THE TAT, DON'T LEAVE MULTI/CONDO/MANUFACTURE FILE</b></font></td>
	</tr>
	<tr>
		<td width="30%" class="textstyle1">
			Customer Name
		</td>
		<td width="2%" class="textstyle1">
			:
		</td>
		<td class="title">
			<?php echo $customerName;?>
		</td>
	</tr>
	<tr>
		<td class="textstyle1">
			Order No
		</td>
		<td class="textstyle1">
			:
		</td>
		<td class="title">
			<?php echo $orderAddress;?>
		</td>
	</tr>
	<tr>
		<td class="textstyle1">
			Order Type
		</td>
		<td class="textstyle1">
			:
		</td>
		<td class="title">
			<?php echo $orderText;?>
		</td>
	</tr>
	<tr>
		<td class="textstyle1">
			Order ON
		</td>
		<td class="textstyle1">
			:
		</td>
		<td class="title">
			<font color="<?php echo $timeZoneColor;?>"><?php echo $displayDate.",".$displayTime;?></font>
		</td>
	</tr>
	<tr>
		<td class="textstyle1">
			TAT
		</td>
		<td class="textstyle1">
			:
		</td>
		<td class="title">
			<?php
				if($isAddedTatTiming		==	1)
				{
					$expctDelvText			=	getHours($orderCompletedTat);
					$onTimeText				=	"<b>Ontime</b>";
					if($isCompletedOnTime	==	2)
					{
						$onTimeText			=	"<font color='#ff0000;'><b>Late <b></font>(".getHours($beforeAfterTimingMin).")";
					}
					echo $expctDelvText." ".$onTimeText;
				}
				else
				{
					echo $expctDelvText;
				}
			?>
		</td>
	</tr>	
</table>
<?php
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);	
		$explanation		=	trim($explanation);
		

		if(empty($explanation))
		{
			$errorMsg				   .=	"Please add an explanation.<br>";
		}
		
		if(empty($errorMsg))
		{
			
			$t_orderAddress 			=	makeDBSafe($orderAddress);
			$t_customerName				=	makeDBSafe($customerName);
			$t_explanation 				=	makeDBSafe($explanation);


			$orderObj->acceptCustomerOrder($orderId,$customerId,$s_employeeId);

			dbQuery("INSERT INTO order_tat_explanation SET employeeId=$s_employeeId,orderId=$orderId,memberId=$customerId,orderAddress='$t_orderAddress',customerName='$t_customerName',explanation='$t_explanation',adddedOn='".CURRENT_DATE_INDIA."',adddedTime='".CURRENT_TIME_INDIA."'");

			///////////////// SENDING EMAIL TO MANAGERS /////////////////////////////////////
			/////////////////// START OF SENDING EMAIL BLOCK/////////////////////////
			include(SITE_ROOT		.   "/classes/email-templates.php");
			$emailObj			    =	new emails();

			$emailBodyText			=	"<table width='98%' border='0' cellpadding='0' celspacing='0'><tr><td width='20%' valign='top'>Order Address</td><td width='2%' valign='top'>:</td><td valign='top'>".$orderAddress."</td></tr><tr><td height='5'></td></tr><tr><td valign='top'>Customer Name</td><td  valign='top'>:</td><td valign='top'>".$customerName."</td></tr><tr><td height='5'></td></tr><tr><td height='5'></td></tr><tr><td valign='top'>Accepted By</td><td  valign='top'>:</td><td valign='top'>".$s_employeeName."</td></tr><tr><td height='5'></td></tr><tr><td valign='top'>Explanation</td><td  valign='top'>:</td><td valign='top'>".$explanation."</td></tr><tr><td height='5'></td></tr><tr><td height='5'></td></tr><tr><td valign='top'>Order On</td><td  valign='top'>:</td><td valign='top'>".$displayDate.",".$displayTime."</td></table>";

			if(!empty($a_allmanagerEmails))
			{
				$emailSubject		=	"Order Accepted Explanation From - ".$s_employeeName;
				foreach($a_allmanagerEmails as $k=>$value)
				{
					list($managerEmail,$managerName)	=	explode("|",$value);

					$a_templateData		=	array("{subject}"=>$emailSubject,"{completeName}"=>$managerName,"{emailBody}"=>$emailBodyText);

					$a_templateSubject	=	array("{emailSubject}"=>$emailSubject);

					$uniqueTemplateName	=	"TEMPLATE_SENDING_SIMPLEE_CUSTOMER_MESSAGE";
					$toEmail			=	$managerEmail;
					//include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
				}
			}

			echo "<br><center><font class='smalltext2'><b>Successfully accepted order!!</b></font></center></br>";
		
			echo "<script type='text/javascript'>reflectChange();</script>";
			
			echo "<script>setTimeout('window.close()',1)</script>";

		}
	}
?>
<script type="text/javascript">
	function validAudit()
	{
		form1	=	document.sendLateEta;
		if(form1.explanation.value == "" || form1.explanation.value == 0 || form1.explanation.value == " ")
		{
			
			alert("Please add an explanation.");
			form1.explanation.focus();
			return false;
			
		}	

		//alert("This is under constructions.");
		//return false;	
	}
</script>
<form name="sendLateEta" action="" method="POST" onSubmit="return validAudit();">
	<table width="100%" align="center" border="0" cellspacing="0" cellspacing="0">
		<?php
			if(empty($errorMsg))
			{
		?>
		<tr>
			<td colspan="3" class="error"><?php echo $errorMsg;?></td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<?php
			}
		?>
		<tr>
			<td width="30%" class="textstyle1" valign="top">
				ADD EXPLANATION
			</td>
			<td width="2%" class="textstyle1"  valign="top">
				:
			</td>
			<td class="textstyle1">
				<textarea name="explanation" rows="4" cols="50" style="border:2px solid #4d4d4d"><?php echo $explanation;?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2"></td>
			<td>
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
	</table>
</form>
<?php

	}
	else
	{
		echo "<table width='90%' align='center' border='1' height='100'><tr><td align='center' align='center' class='error'><b>$errorMessageForm</b></td></tr></table>";
	}
?>
<br><br>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
</center>
</body>
</html>

