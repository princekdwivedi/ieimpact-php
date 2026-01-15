<?php
	

	
	$timeDifference					=	5;
	$insertTable					=	false;

	$query							=	"SELECT * FROM appraiseraide_orders_email_received ORDER BY ID DESC LIMIT 1";
	$result1						=	dbQuery($query);
	if(mysql_num_rows($result1))
	{

		$row						=	mysql_fetch_assoc($result1);
		$createdDate				=	$row['CreatedOn'];
		$createdTime				=	$row['CreatedTime'];

		list($year,$month,$date)	=	explode("-",$createdDate);
		list($hr,$min,$sec)			=	explode(":",$createdTime);

		$lastRetriveTimeStamp	    =	mktime($hr,$min,$sec,$month,$date,$year);

		$diff	=  $nowIndiaTimeStamp-$lastRetriveTimeStamp;

		$min	=  $diff/60;

		if($min > $timeDifference)
		{
			$insertTable	=	true;
		}
		else
		{
			$insertTable	=	false;;
		}
	}
	else
	{
		$insertTable		=	true;
	}

	if($insertTable	 ==	true)
	{
		dbQuery("TRUNCATE appraiseraide_orders_email_received");
		
		$mbox   = imap_open("{mail.appraiseraide.com:995/pop3/ssl/novalidate-cert}", "orders@appraiseraide.com", "mar9wyne")
    or die("can't connect: " . imap_last_error());

		$MC		= imap_check($mbox);

		$result = imap_fetch_overview($mbox,"1:{$MC->Nmsgs}",0);

		foreach($result as $overview)
		{
		   $seen1					=	$overview->seen;
		   if($seen1				==	0)
		   {
			   $msgNo				=	$overview->msgno;
			   $body				=	imap_body($mbox,$msgNo,0);
			   $emaildateTime		=	$overview->date;

				$comaPos=	strpos($emaildateTime,",");
				if($comaPos	!== 0)
				{
					$emaildateTime			=	substr($emaildateTime,$comaPos+1);
				}
				$date			=	substr($emaildateTime,0,12);
				$emailTime		=	substr($emaildateTime,12,9);
				$day			=	trim(substr($date,0,3));
				$month			=	trim(substr($date,3,4));
				$year			=	trim(substr($date,-4));
				$countYear		=	strlen($year);
				if($countYear < 4)
				{
					$year		=	"2".$year;
				}
				$month			=	strtolower($month);
				$month			=	ucwords($month);

				$month			=	array_search($month, $a_month);
				
				$emailDate		=	$year."-".$month."-".$day;
				$emailSubject	=	makeDBSafe($overview->subject);
				$emailFrom		=	makeDBSafe($overview->from);
				$body			=	makeDBSafe($body);
 
			  dbQuery("INSERT INTO appraiseraide_orders_email_received SET EmailFrom='$emailFrom',EmailDate='$emailDate',EmailTime='$emailTime',EmailSubject='$emailSubject',CreatedOn='".CURRENT_DATE_INDIA."',CreatedTime='".CURRENT_TIME_INDIA."'");
		   }
		}
		imap_close($mbox);
	}
?>
