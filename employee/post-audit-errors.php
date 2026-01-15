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
	$errorMessageForm			= "You are not authorized to view this page !!";
	$auditId					=  0;

	$checked1					=	"checked";
	$checked11					=	"";
	$display1					=	"";
	$firstCategoryDescription	=	"";

	$checked2					=	"checked";
	$checked22					=	"";
	$display2					=	"";
	$secondCategoryDescription	=	"";

	$checked3					=	"checked";
	$checked33					=	"";
	$display3					=	"";
	$thirdCategoryDescription	=	"";

	$auditAddedByText			=	"";

	if(isset($_GET['orderId']) && isset($_GET['customerId']))
	{
		$orderId					=	$_GET['orderId'];
		$customerId					=	$_GET['customerId'];
		if($result					=	$orderObj->getCompletedOrderDetails($orderId,$customerId))
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
			$orderCompletedOn		=	$row['orderCompletedOn'];

			$t_orderAddedOn			=	showDate($orderAddedOn);
			$t_orderCompletedOn		=	showDate($orderCompletedOn);
			
			$orderText				=	$a_customerOrder[$orderType];
			if($orderType			==	6)
			{
				$orderText			=	$orderText."(".$customersOwnOrderText.")";
			}
			$qaDoneByText			=	"";
			$acceptedByText			=	"";

			$qaDoneBy				=	$employeeObj->getSingleQueryResult("SELECT qaDoneBy FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId AND hasQaDone=1","qaDoneBy");
			if(!empty($qaDoneBy))
			{
				$qaDoneByText		=	$employeeObj->getEmployeeName($qaDoneBy);
			}
			$acceptedByText			=	$employeeObj->getEmployeeName($acceptedBy);

			if($result1						=	$orderObj->getPostAuditOrderDetails($orderId))
			{
				$row1						=	mysqli_fetch_assoc($result1);
				$auditId					=	$row1['auditId'];
				$firstCategory				=	$row1['firstCategory'];
				$firstCategoryDescription	=	stripslashes($row1['firstCategoryDescription']);
				$secondCategory				=	$row1['secondCategory'];
				$secondCategoryDescription	=	stripslashes($row1['secondCategoryDescription']);
				$thirdCategory				=	$row1['thirdCategory'];
				$thirdCategoryDescription	=	stripslashes($row1['thirdCategoryDescription']);

				$auditAddedBy				=	$row1['addedBy'];
				$auditAddedOn				=	showDate($row1['addedOn']);
				$auditAddedByText			=	$employeeObj->getEmployeeName($auditAddedBy)." on ".$auditAddedOn;
				$updatedBy					=	$row1['updatedBy'];
				$updatedOn					=	showDate($row1['updatedOn']);
				if(!empty($updatedBy))
				{
					$auditUpdatedByText		=	$employeeObj->getEmployeeName($updatedBy)." on ".$updatedOn;
				}
				else
				{
					$auditUpdatedByText		=	"";
				}

				if($firstCategory			==	0)
				{
					$checked1				=	"";
					$checked11				=	"checked";
					$display1				=	"none";
				}
				if($secondCategory			==	0)
				{
					$checked2				=	"";
					$checked22				=	"checked";
					$display2				=	"none";
				}
				if($thirdCategory			==	0)
				{
					$checked3				=	"";
					$checked33				=	"checked";
					$display3				=	"none";
				}


			}
		}
	}

	if(!$s_hasManagerAccess)
	{
		$showForm					=	false;
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
		<td colspan="3" class="textstyle1"><b>Marked Post Audit Errors</b></td>
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
			Completed On
		</td>
		<td class="textstyle1">
			:
		</td>
		<td class="title">
			<?php echo $t_orderCompletedOn;?>
		</td>
	</tr>
	<tr>
		<td class="textstyle1">
			Processed By
		</td>
		<td class="textstyle1">
			:
		</td>
		<td class="title">
			<?php echo $acceptedByText;?>
		</td>
	</tr>
	<tr>
		<td class="textstyle1">
			QA Done By
		</td>
		<td class="textstyle1">
			:
		</td>
		<td class="title">
			<?php echo $qaDoneByText;?>
		</td>
	</tr>
	<?php 
		if(!empty($rateGiven))
		{
	?>
	<tr>
		<td class="textstyle1">
			Rate Given
		</td>
		<td class="textstyle1">
			:
		</td>
		<td class="title">
			<img src="<?php echo SITE_URL;?>/images/rating/<?php echo $rateGiven;?>.png">
		</td>
	</tr>
	<?php
		}
		if(!empty($auditAddedByText))
		{
	?>
	<tr>
		<td class="textstyle1">
			Already Audited By
		</td>
		<td class="textstyle1">
			:
		</td>
		<td class="title">
			<?php echo $auditAddedByText;?>
		</td>
	</tr>
	<?php
		} 
		if(!empty($updatedBy) && !empty($auditUpdatedByText))
		{
	?>
	<tr>
		<td class="textstyle1">
			Edited By
		</td>
		<td class="textstyle1">
			:
		</td>
		<td class="title">
			<?php echo $auditUpdatedByText;?>
		</td>
	</tr>
	<?php
		}
	?>
</table>
<?php
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);	
		$firstCategoryDescription		=	trim($firstCategoryDescription);
		$secondCategoryDescription		=	trim($secondCategoryDescription);
		$thirdCategoryDescription		=	trim($thirdCategoryDescription);

		$firstCategoryDescription		=	makeDBSafe($firstCategoryDescription);
		$secondCategoryDescription		=	makeDBSafe($secondCategoryDescription);
		$thirdCategoryDescription		=	makeDBSafe($thirdCategoryDescription);

		if($firstCategory	==	1 && empty($firstCategoryDescription))
		{
			$errorMsg				   .=	"Please enter A category error description.<br>";
		}
		if($secondCategory	==	1 && empty($secondCategoryDescription))
		{
			$errorMsg				   .=	"Please enter B category error description.<br>";
		}
		if($thirdCategory	==	1 && empty($thirdCategoryDescription))
		{
			$errorMsg				   .=	"Please enter C category error description.<br>";
		}
		if(empty($errorMsg))
		{
			if($firstCategory	==	2)
			{
				$firstCategory				=	0;
				$firstCategoryDescription	=	"";
				$categoryTextA				=	"No";
			}
			else
			{
				$categoryTextA				=	"Yes&nbsp;(".$firstCategoryDescription.")";
			}
			if($secondCategory	==	2)
			{
				$secondCategory				=	0;
				$secondCategoryDescription	=	"";
				$categoryTextB				=	"No";
			}
			else
			{
				$categoryTextB				=	"Yes&nbsp;(".$secondCategoryDescription.")";
			}
			if($thirdCategory	==	2)
			{
				$thirdCategory				=	0;
				$thirdCategoryDescription	=	"";
				$categoryTextC				=	"No";
			}
			else
			{
				$categoryTextC				=	"Yes&nbsp;(".$thirdCategoryDescription.")";
			}
			
			if(empty($auditId))
			{
				dbQuery("INSERT INTO orders_post_audit_details SET orderId=$orderId,processEmployee=$acceptedBy,qaEmployee=$qaDoneBy,firstCategory=$firstCategory,firstCategoryDescription='$firstCategoryDescription',secondCategory=$secondCategory,secondCategoryDescription='$secondCategoryDescription',thirdCategory=$thirdCategory,thirdCategoryDescription='$thirdCategoryDescription',addedBy=$s_employeeId,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',addedFromIp='".VISITOR_IP_ADDRESS."',orderAddedOn='$orderAddedOn',orderCompletedOn='$orderCompletedOn'");

				dbQuery("UPDATE members_orders SET isDonePostAudit=1 WHERE orderId=$orderId AND memberId=$customerId");

				$ensteredByTxt		=	"Entered By";
				$emailSubject		=	"Post Audit Error Marked For - ".$orderAddress;
			}
			else
			{
				dbQuery("UPDATE orders_post_audit_details SET processEmployee=$acceptedBy,qaEmployee=$qaDoneBy,firstCategory=$firstCategory,firstCategoryDescription='$firstCategoryDescription',secondCategory=$secondCategory,secondCategoryDescription='$secondCategoryDescription',thirdCategory=$thirdCategory,thirdCategoryDescription='$thirdCategoryDescription',updatedBy=$s_employeeId,updatedOn='".CURRENT_DATE_INDIA."',updatedTime='".CURRENT_TIME_INDIA."',updatedFromIp='".VISITOR_IP_ADDRESS."' WHERE auditId=$auditId AND orderId=$orderId");

				$ensteredByTxt		=	"Updated By";
				$emailSubject		=	"Updated Post Audit Error Marked For - ".$orderAddress;
			}

			/////////////////// START OF SENDING EMAIL BLOCK/////////////////////////
			include(SITE_ROOT		.   "/classes/email-templates.php");
			$emailObj			    =	new emails();

			$emailBodyText			=	"<table width='98%' border='0' cellpadding='0' celspacing='0'><tr><td width='20%' valign='top'>Order Address</td><td width='2%' valign='top'>:</td><td valign='top'>".$orderAddress."</td></tr><tr><td height='5'></td></tr><tr><td valign='top'>Customer Name</td><td  valign='top'>:</td><td valign='top'>".$customerName."</td></tr><tr><td height='5'></td></tr><tr><td height='5'></td></tr><tr><td valign='top'>Process By</td><td  valign='top'>:</td><td valign='top'>".$acceptedByText."</td></tr><tr><td height='5'></td></tr><tr><td valign='top'>QA By</td><td  valign='top'>:</td><td valign='top'>".$qaDoneByText."</td></tr><tr><td height='5'></td></tr><tr><td height='5'></td></tr><tr><td valign='top'>Category A</td><td  valign='top'>:</td><td valign='top'>".$categoryTextA."</td></tr><tr><td height='5'></td></tr><tr><td height='5'></td></tr><tr><td valign='top'>Category B</td><td  valign='top'>:</td><td valign='top'>".$categoryTextB."</td></tr><tr><td height='5'></td></tr><tr><td height='5'></td></tr><tr><td valign='top'>Category C</td><td  valign='top'>:</td><td valign='top'>".$categoryTextC."</td></tr><tr><td height='5'></td></tr><tr><td height='5'></td></tr><tr><td valign='top'>".$ensteredByTxt."</td><td  valign='top'>:</td><td valign='top'>".$s_employeeName."</td></tr><tr><td height='5'></td></tr></table>";

			if(!empty($a_allmanagerEmails))
			{

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

			echo "<br><center><font class='smalltext2'><b>Successfully add post audit error !!</b></font></center></br>";
	
			echo "<script type='text/javascript'>reflectChange();</script>";
		
			echo "<script>setTimeout('window.close()',1)</script>";

		}
	}
?>
<script type="text/javascript">
	function validAudit()
	{
		form1	=	document.sendPostAuditOfFiles;
		if(form1.firstCategory[0].checked == true || form1.firstCategory.value == 1)
		{
			if(form1.firstCategoryDescription.value == "0" || form1.firstCategoryDescription.value == "")
			{
				alert("Please enter A category error description.");
				form1.firstCategoryDescription.focus();
				return false;
			}
		}
		if(form1.secondCategory[0].checked == true || form1.secondCategory.value == 1)
		{
			if(form1.secondCategoryDescription.value == "0" || form1.secondCategoryDescription.value == "")
			{
				alert("Please enter B category error description.");
				form1.secondCategoryDescription.focus();
				return false;
			}
		}
		if(form1.thirdCategory[0].checked == true || form1.thirdCategory.value == 1)
		{
			if(form1.thirdCategoryDescription.value == "0" || form1.thirdCategoryDescription.value == "")
			{
				alert("Please enter C category error description.");
				form1.thirdCategoryDescription.focus();
				return false;
			}
		}
	}
	function checkFirstTextbox(flag)
	{
		if(flag	==	1)
		{
			document.getElementById('showHideFirstCategoryBox').style.display = 'inline';
		}
		else
		{
			document.getElementById('showHideFirstCategoryBox').style.display = 'none';
		}
	}
	function checkSecondTextbox(flag)
	{
		if(flag	==	1)
		{
			document.getElementById('showHideSecondCategoryBox').style.display = 'inline';
		}
		else
		{
			document.getElementById('showHideSecondCategoryBox').style.display = 'none';
		}
	}
	function checkThirdTextbox(flag)
	{
		if(flag	==	1)
		{
			document.getElementById('showHideThirdCategoryBox').style.display = 'inline';
		}
		else
		{
			document.getElementById('showHideThirdCategoryBox').style.display = 'none';
		}
	}
</script>
<form name="sendPostAuditOfFiles" action="" method="POST" onSubmit="return validAudit();">
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
			<td width="30%" class="textstyle1">
				CATEGORY A ERRORS
			</td>
			<td width="2%" class="textstyle1">
				:
			</td>
			<td class="textstyle1">
				<input type="radio" name="firstCategory" value="1" <?php echo $checked1;?> onclick="checkFirstTextbox(1);"><b>Yes</b>&nbsp;
				<input type="radio" name="firstCategory" value="2" <?php echo $checked11;?> onclick="checkFirstTextbox(2);"><b>No</b>
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td colspan="3">
				<div id="showHideFirstCategoryBox" style="display:<?php echo $display1?>">
					<table width="100%" align="center" border="0" cellspacing="0" cellspacing="0">
						<tr>
							<td width="30%" class="textstyle1" valign="top">
								ERROR DESCRIPTION
							</td>
							<td width="2%" class="textstyle1" valign="top">
								:
							</td>
							<td valign="top">
								<textarea name="firstCategoryDescription" rows="4" cols="50" style="border:2px solid #4d4d4d"><?php echo $firstCategoryDescription;?></textarea>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td class="textstyle1">
				CATEGORY B ERRORS
			</td>
			<td class="textstyle1">
				:
			</td>
			<td class="textstyle1">
				<input type="radio" name="secondCategory" value="1" <?php echo $checked2;?> onclick="checkSecondTextbox(1);"><b>Yes</b>&nbsp;
				<input type="radio" name="secondCategory" value="2" <?php echo $checked22;?> onclick="checkSecondTextbox(2);"><b>No</b>
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td colspan="3">
				<div id="showHideSecondCategoryBox" style="display:<?php echo $display2?>">
					<table width="100%" align="center" border="0" cellspacing="0" cellspacing="0">
						<tr>
							<td width="30%" class="textstyle1" valign="top">
								ERROR DESCRIPTION
							</td>
							<td width="2%" class="textstyle1" valign="top">
								:
							</td>
							<td valign="top">
								<textarea name="secondCategoryDescription" rows="4" cols="50" style="border:2px solid #4d4d4d"><?php echo $secondCategoryDescription;?></textarea>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td class="textstyle1">
				CATEGORY C ERRORS
			</td>
			<td class="textstyle1">
				:
			</td>
			<td class="textstyle1">
				<input type="radio" name="thirdCategory" value="1" <?php echo $checked3;?> onclick="checkThirdTextbox(1);"><b>Yes</b>&nbsp;
				<input type="radio" name="thirdCategory" value="2" <?php echo $checked33;?> onclick="checkThirdTextbox(2);"><b>No</b>
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td colspan="3">
				<div id="showHideThirdCategoryBox" style="display:<?php echo $display3?>">
					<table width="100%" align="center" border="0" cellspacing="0" cellspacing="0">
						<tr>
							<td width="30%" class="textstyle1" valign="top">
								ERROR DESCRIPTION
							</td>
							<td width="2%" class="textstyle1" valign="top">
								:
							</td>
							<td valign="top">
								<textarea name="thirdCategoryDescription" rows="4" cols="50" style="border:2px solid #4d4d4d"><?php echo $thirdCategoryDescription;?></textarea>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td height="5"></td>
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

