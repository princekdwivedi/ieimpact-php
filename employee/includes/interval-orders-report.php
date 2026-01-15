<?php
	/*************** THIS BLOCK SHOULD UNCOMMENT WHILE YOU WILL UPLOAD ONLINE **************/
	define("SITE_ROOT"		,	"/home/ieimpact/public_html");
	define("SITE_URL"		,	"https://secure.ieimpact.com");
	define("SITE_ROOT_FILES",	"/home/ieimpact/WebFiles");

	include(SITE_ROOT		.   "/includes/cron-connection.php");	
	include(SITE_ROOT		.	"/includes/common-functions.php");

	define("SITE_ROOT_FILES",	"/home/ieimpact/WebFiles");

	date_default_timezone_set('America/New_York');

	$today_day				=	gmdate("d");
	$today_month			=	gmdate("m");
	$today_year				=	gmdate("Y");

	$now_hours				=	gmdate("G");
	$now_minutes			=	gmdate("i");
	$now_seconds			=	gmdate("s");	
	
	$nowIndiaHours			=	$now_hours +4;
	$nowIndiaMinutes		=	$now_minutes+30;

	$nowIndiaTimeStamp		=	mktime($nowIndiaHours, $nowIndiaMinutes, $now_seconds, $today_month, $today_day, $today_year);

	$nowTimeIndia			=	date('H:i:s',$nowIndiaTimeStamp);
	$nowDateIndia			=	date('Y-m-d',$nowIndiaTimeStamp);


	define("CURRENT_DATE_INDIA",	$nowDateIndia);
	define("CURRENT_TIME_INDIA",	$nowTimeIndia);

	define("CUSTOMERS"		   ,	1); 

	$customer_zone_date			=	date("Y-m-d");
	$customer_zone_time			=	date("H:i:s");

	/*************** THIS BLOCK SHOULD UNCOMMENT WHILE YOU WILL UPLOAD ONLINE **************
	*************** THIS BLOCK SHOULD COMMENT WHILE YOU WILL UPLOAD ONLINE **************

	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");


	/*************** THIS BLOCK SHOULDCOMMENT WHILE YOU WILL UPLOAD ONLINE **************/
	/****************************** REPORT TO DISPLAY ***********************************
		1) New orders not accepted       :   12
		2) Orders Need Attention         :    1
		3) Orders in process             :   24
		4) Total uncompleted orders      :   35
		5) Orders Checked for files      :   23
		6) Orders Exceeded TAT           :    1
		7) Messages not replied          :   23
		8) Ratings not replied           :    3
		9) Email messages not replied    :   34

  ***************************************************************************************/
  $orderDate			=	CURRENT_DATE_INDIA;

  $totalNotAccepted		=	@mysql_result(mysql_query("SELECT COUNT(*) FROM members_orders WHERE status=0 AND isVirtualDeleted=0 AND orderAddedOn='$orderDate'"),0);
  if(empty($totalNotAccepted))
  {
	$totalNotAccepted	=	0;
  }

  $totalInAttention		=	@mysql_result(mysql_query("SELECT COUNT(*) FROM members_orders WHERE status=0 AND isVirtualDeleted=0 AND status=3 AND orderAddedOn='$orderDate'"),0);
  if(empty($totalInAttention))
  {
	$totalInAttention	=	0;
  }

  $totalInProcess		=	@mysql_result(mysql_query("SELECT COUNT(*) FROM members_orders WHERE status=0 AND isVirtualDeleted=0 AND status IN (0,1,3,6) AND orderAddedOn='$orderDate'"),0);
  if(empty($totalInProcess))
  {
	$totalInProcess		=	0;
  }

  $totalUnCompleted		=	@mysql_result(mysql_query("SELECT COUNT(*) FROM members_orders_reply WHERE hasRepliedFileUploaded=1 AND isVirtualDeleted=0 AND hasQaDone=0 AND replyFileAddedOn='$orderDate'"),0);
  if(empty($totalIncompleted))
  {
	$totalIncompleted	=	0;
  }

  $totalCheckedOrders	=	@mysql_result(mysql_query("SELECT COUNT(*) FROM members_orders WHERE isVirtualDeleted=0 AND isOrderChecked=1 AND orderAddedOn='$orderDate'"),0);
  if(empty($totalCheckedOrders))
  {
	$totalCheckedOrders	=	0;
  }

 
  $totalOrderExceedTime	=	"";
  $currentDate			=	CURRENT_DATE_INDIA;
  $currentTime		    =	CURRENT_TIME_INDIA;


  $query				=	"SELECT employeeWarningDate,employeeWarningTime FROM members_orders WHERE status=0 AND isVirtualDeleted=0 AND status IN (0,1,3,6) AND orderAddedOn='$orderDate' AND isHavingEstimatedTime=1";
  $result				=	mysql_query($query);
  if(mysql_num_rows($result))
  {
	$total							=	0;
	while($result					=	mysql_fetch_assoc($result))
	{
		$employeeWarningDate		=	$row['employeeWarningDate'];
		$employeeWarningTime		=	$row['employeeWarningTime'];

	
		if($currentDate				==	$employeeWarningDate)
		{
			if($currentTime			>  $employeeWarningTime)
			{
				$total++;
			}
		}
		elseif($currentDate > $employeeWorkDate)
		{
			$total++;
		}
		
	}
	$totalOrderExceedTime			=	$totalOrderExceedTime+$total;
  }


  $messagesNotReplied		=	@mysql_result(mysql_query("SELECT COUNT(*) FROM members_employee_messages WHERE isVirtualDeleted =0 AND isRepliedToEmail=0 AND messageBy=1"),0);
  if(empty($messagesNotReplied))
  {
	$messagesNotReplied		=	0;
  }

  $ratingsNotReplied		=	@mysql_result(mysql_query("SELECT COUNT(*) FROM members_orders WHERE isVirtualDeleted =0 AND isDeleted=0 AND isHavingOrderNewMessage=1 AND rateGiven <> 0"),0);
  if(empty($ratingsNotReplied))
  {
	$ratingsNotReplied		=	0;
  }

  $emailMsgReplied			=	@mysql_result(mysql_query("SELECT COUNT(*) FROM members_general_messages WHERE isOrderGeneralMsg=1 AND isBillingMsg=0 AND status=0 AND parentId=0"),0);
  if(empty($emailMsgReplied))
  {
	$emailMsgReplied		=	0;
  }

	include(SITE_ROOT .  "/classes/phpmailer.php");
	$mailObj		  =	 new PHPMailer();

	$body			  =	 "<table width='90%' align='center' cellpadding=='0' cellspacing='0'><tr><td align='left' colspan='3'><font size='3px' face='verdana' color='#387070'> Orders Status as of ".showTimeShortFormat($currentTime)." IST on ".showDateFullText($currentDate)."</font></td></tr>";

	$body			 .=	"<tr><td width='5%' align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'>1) </font></td><td width='30%' align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'>New orders not accepted</font></td><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;font-weight:bold;'>".$totalNotAccepted."</font></td></tr>";

	$body			 .=	"<tr><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'>2) </font></td><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'>Orders Need Attention</font></td><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;font-weight:bold;'>".$totalInAttention."</font></td></tr>";

	$body			 .=	"<tr><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'>3) </font></td><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'>Orders in process</font></td><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;font-weight:bold;'>".$totalInProcess."</font></td></tr>";

	$body			 .=	"<tr><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'>4) </font></td><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'>Total uncompleted orders</font></td><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;font-weight:bold;'>".$totalIncompleted."</font></td></tr>";

	$body			 .=	"<tr><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'>5) </font></td><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'>Orders Checked for files</font></td><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;font-weight:bold;'>".$totalCheckedOrders."</font></td></tr>";

	$body			 .=	"<tr><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'>6) </font></td><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'>Orders Exceeded TAT</font></td><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;font-weight:bold;'>".$totalOrderExceedTime."</font></td></tr>";

	$body			 .=	"<tr><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'>7) </font></td><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'>Messages not replied</font></td><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;font-weight:bold;'>".$messagesNotReplied."</font></td></tr>";

	$body			 .=	"<tr><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'>8) </font></td><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'>Ratings not replied</font></td><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;font-weight:bold;'>".$ratingsNotReplied."</font></td></tr>";

	$body			 .=	"<tr><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'>9) </font></td><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;'>Email messages not replied</font></td><td align='left'><font style='font-family:verdana;font-size:13px;color:#000000;text-decoration:none;font-weight:bold;'>".$emailMsgReplied."</font></td></tr>";


	$body			 .=	"</table>";

	$mailSubject	  =  "Orders Status as of ".showTimeShortFormat($currentTime)." IST on ".showDateFullText($currentDate);
	//$to				  =  "hemant@ieimpact.com";
	$to				  =  "gaurabsiva1@gmail.com";
	$mailObj->From	  =  "hemant@ieimpact.com";
	$mailObj->FromName=  "ieIMPACT";
	$mailObj->AddAddress($to);
	$mailObj->Subject = $mailSubject;
	$mailObj->Body	  =	$body;
	$mailObj->Send();
?>