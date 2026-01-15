<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	//ini_set('display_errors', 1);
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/common.php");	
	include(SITE_ROOT			.   "/classes/email-templates.php");
    $emailObj					=	new emails();
	$employeeObj				=	new employee();
	$memberObj					=	new members();
	$orderObj					=	new orders();
	$commonClass				=	new common();
	$showForm					=	false;
	$orderId					=	0;
	$memberId					=	0;
	$error						=	"";
	$currentISTTime				=  CURRENT_DATE_INDIA.' '.CURRENT_TIME_INDIA;
	$a_deliveryTimeDisplay		=	array();
	for($i=1;$i<=20;$i++){
		$defaultDeliveryTime    =  date('Y-m-d H:i:s', strtotime("+$i hours", strtotime($currentISTTime)));
		list($date,$time)		=	explode(" ",$defaultDeliveryTime);
		$a_deliveryTimeDisplay[$i] = showDate($date)." ".showTimeShortFormat($time);

	}

	
	if(isset($_GET['orderId']))
	{
		$orderId				=	(int)$_GET['orderId'];
	
		if(!empty($orderId))
		{		

			$query				=	"SELECT members_orders.*,completeName,email,secondaryEmail,state FROM members_orders INNER JOIN members ON members_orders.memberId=members.memberId WHERE orderId=$orderId AND status NOT IN (2,5,6)";
			$result				=	dbQuery($query);
			if(mysql_num_rows($result))
			{
				$showForm				=   true;
				$row					=	mysql_fetch_assoc($result);
			
				$orderId				=	$row['orderId'];
				$memberId				=	$row['memberId'];
				$status					=	$row['status'];
				$orderAddress			=	stripslashes($row['orderAddress']);
				$customerName			=	stripslashes($row['completeName']);
				$date					=	$row['orderAddedOn'];
				$time					=	$row['orderAddedTime'];
				$acceptedBy				=   $row['acceptedBy'];
				$isRushOrder			=	$row['isRushOrder'];
				$employeeWarningDate    =	$row['employeeWarningDate'];
				$employeeWarningTime    =	$row['employeeWarningTime'];
				$state					=	$row['state'];
				$orderReplyToEmail		=	$row['orderReplyToEmail'];
				$email		            =	$row['email'];
				$secondaryEmail         =	$row['secondaryEmail'];
				$acceeptedByName		=	stripslashes($row['acceeptedByName']);
				$expctDelvText		    =	orderTAT($employeeWarningDate,$employeeWarningTime);

				$statusText				=   "<font color='red'>New Order</font>";
				if($status				==	1)
				{
					$statusText			=  "<font color='#4F0000'>Accepted</font>";
					$hasReplied			=	@mysql_result(dbQuery("SELECT hasRepliedFileUploaded FROM members_orders_reply WHERE orderId=$orderId AND memberId=$memberId AND hasRepliedFileUploaded=1"),0);
					if(!empty($hasReplied))
					{
						$statusText		=	"<font color='blue'>QA Pending</font>";
					}
				}
				elseif($status			==	3)
				{	
					$statusText			=   "<font color='#333333'>Nd Atten.</font>";
				}
				elseif($status			==	4)
				{
					$statusText			=   "<font color='#ff0000'>Cancelled</font>";
				}
				
				$daysAgo					=	showDateTimeFormat($date,$time);
			}		
			
		}

	}

	

	
?>
<html>
<head>
<TITLE>ieIMPACT order delivery delaye</TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
<body>
<script type="text/javascript">
	function reflectChange()
	{
		window.opener.location.reload();
	}
	
	 function validMessage()
	 {
		form1	=	document.sendDelayedTime;
		if(form1.deliveryTimeSel.value == "" || form1.deliveryTimeSel.value == " " || form1.deliveryTimeSel.value == "0" || form1.deliveryTimeSel.value == "  ")
		{
			alert("Please select delivery time.");
			form1.deliveryTimeSel.focus();
			return false;
		}
		
	 }
	 function showFinishedAt(time)
	 {	
		
		var displayTime = "";	
		var js_array    = [<?php echo '"'.implode('","', $a_deliveryTimeDisplay).'"' ?>];
		time            = time-1;
		displayTime     = js_array[time];
		document.getElementById('showHideTime').innerHTML = "<font style='color:#ff0000;font-size:16px;font-weight:bold;'>At "+displayTime+"</font>";
	 }
</script>
<center>
<?php
	if($showForm)
	{

		if(isset($_REQUEST['formSubmitted']))
		{
			extract($_REQUEST);			

			if(empty($deliveryTimeSel))
			{
				$error		=   "Please select delivery time.";
			}
			else
			{
				if(array_key_exists($state,$a_usaProvinces))
				{
					$timeZone		=	$a_usaProvinces[$state];

					list($stateName,$zone)	=	explode("|",$timeZone);
					if(!array_key_exists($zone,$a_timeZoneColor))
					{
						$zone		=	"CST";
					}
				}
				else
				{
					$zone			=	"CST";
				}
				//CST,PST,MST,EST,
				$currentISTDateTime =  CURRENT_DATE_INDIA.' '.CURRENT_TIME_INDIA;
				$orderDeliveryTime  =  date('Y-m-d H:i:s', strtotime("+$deliveryTimeSel hours", strtotime($currentISTDateTime)));

				$orderDeliveryAt	=	getZoneTime($zone,$orderDeliveryTime,'IST');
				list($deliveryD,$deliveryT) = explode(" ",$orderDeliveryAt);

				$emailDay			=	 date("D M d", strtotime($deliveryD));
				$emailTime			=	 showTimeShortFormat($deliveryT);
				$orderArriveAt		=	 $emailDay." by ".$emailTime;	
			
				/////////////////////////// SENDING EMAIL SECTION //////////////////////
				$setThisEmailReplyToo			=	$orderReplyToEmail.CUSTOMER_REPLY_EMAIL_TO;//Setting for reply to make customer reply order mesage
				$setThisEmailReplyTooName		=	"ieIMPACT Orders";//Setting for reply to make customer reply order mesage
				$quickReplyToEmail     = "<a href='mailto:".$setThisEmailReplyToo."'>".$setThisEmailReplyToo."</a>";


				$a_templateData	=	array("{orderAddress}"=>$orderAddress,"{orderArriveAt}"=>$orderArriveAt);


				$a_templateSubject		=	array("{orderAddress}"=>$orderAddress);
				$uniqueTemplateName	    =	"TEMPLATE_SENDING_DELAYED_ORDER_ORDER_EMAIL";


				$toEmail				=	$email;
				$managerEmployeeFromBcc	=   "gaurabsiva1@gmail.com,hemant@ieimpact.net";
				if(!empty($secondaryEmail))
				{
					$managerEmployeeFromCc=	$secondaryEmail;
				}
				include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

				


				//echo "<script type='text/javascript'>reflectChange();</script>";			
				//echo "<script>setTimeout('window.close()',1)</script>";
			}
		}
?>
	<form name="sendDelayedTime" action="" method="POST" onSubmit="return validMessage();">
		<table width="100%" align="center" border="0" cellspacing="3" cellspacing="3">
			<tr>
				<td colspan="3" class="textstyle1"><b>ieIMPACT order delivery delayed for - <?php echo $orderAddress?></b></td>
			</tr>
			<tr>
				<td colspan="3" class="error">
					<?php echo $error;?>
				</td>
			</tr>
			<tr>
				<td class="smalltext2" width="22%">
					<b>Customer Name</b>
				</td>
				<td class="smalltext2" width="2%">
					<b>:</b>
				</td>
				<td class="title">
					<?php echo $customerName;?>
				</td>
			</tr>
			<tr>
				<td class="smalltext2">
					<b>Current Status</b>
				</td>
				<td class="smalltext2">
					<b>:</b>
				</td>
				<td class="title">
					<?php 
						echo $statusText;	
						if(!empty($acceeptedByName)){
							echo "&nbsp;Accepted By :".$acceeptedByName;
						}
					?>
				</td>
			</tr>
			<tr>
				<td class="smalltext2">
					<b>Order Date&Time</b>
				</td>
				<td class="smalltext2">
					<b>:</b>
				</td>
				<td class="title">
					<?php echo $daysAgo;?>
				</td>
			</tr>	
			<tr>
				<td class="smalltext2">
					<b>Delivery Time Left</b>
				</td>
				<td class="smalltext2">
					<b>:</b>
				</td>
				<td>
					<font style="font-size:16px;font-family:verdana;color:#ff0000;font-weight:bold"> <?php echo $expctDelvText;?></font>
				</td>
			</tr>	
			<tr>
				<td class="smalltext2" valign="top">
					<b>Will Complete After</b>
				</td>
				<td class="smalltext2" valign="top">
					<b>:</b>
				</td>
				<td valign="top">
					<select name="deliveryTimeSel" onchange="showFinishedAt(this.value)">
						<?php
							for($i=1;$i<=10;$i++){
								echo "<option value='$i'>$i</option>";
							}
						?>
					</select>Hrs
					<br />
					<div id="showHideTime"><?php echo "<font style='color:#ff0000;font-size:16px;font-weight:bold;'>At $a_deliveryTimeDisplay[1]</font>"?></div>
				</td>
			</tr>			
			<tr>
				<td colspan="2">
					&nbsp;
				</td>
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
		echo "<table width='90%' align='center' border='1' height='100'><tr><td align='center' align='center' class='error'><b>You are trying to open an invalid page.</b></td></tr></table>";
	}
?>
<br><br>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
</center>
</body>
</html>

