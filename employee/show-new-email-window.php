<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj					=  new employee();
	$pagingObj						=  new Paging();

	$timeDifference					=	1;
	$insertTable					=	false;


	if(isDomainAvailible('http%3A%2F%2Fwww.appraiseraide.com'))
    {

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
			
			$mbox   = imap_open("{pop.appraiseraide.com:995/pop3/ssl/novalidate-cert}", "orders@appraiseraide.com", "mar9wyne")
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
	}
?>
<table width="98%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td colspan="5">
			<font style="font-size:20px;fot-weight:bold;color:#4d4d4d">View Emails Unseen At orders@appraiseraide.com</font><br><br>
		</td>
	</tr>
	<tr>
		<td colspan="5">
			<hr size="1" width="100%" color="#4d4d4d">
		</td>
	</tr>
<?php
	if(isset($_REQUEST['recNo']))
	{
		$recNo		    =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo	=	0;
	}

	$whereClause			 =	"";
	$orderBy				 =	"ID DESC";
	$queryString			 =	"";
	
	$start					  =	0;
	$recsPerPage	          =	25;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"appraiseraide_orders_email_received";
	$pagingObj->selectColumns = "*";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/show-new-email-window.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
		$i	=	$recNo;
?>
<tr>
	<td width="5%" class="heading1">
		Sr No
	</td>
	<td width="25%" class="heading1">
		Date
	</td>
	<td width="25%" class="heading1">
		From
	</td>
	<td class="heading1">
		Subject
	</td>
</tr>
<tr>
	<td colspan="5">
		<hr size="1" width="100%" color="#4d4d4d">
	</td>
</tr>
<?php
		while($row	=   mysql_fetch_assoc($recordSet))
		{
			$i++;
			$emailFrom		=	stripslashes($row['EmailFrom']);
			$emailDate		=	showDate($row['EmailDate']);
			$emailTime		=	$row['EmailTime'];
			$emailSubject	=	stripslashes($row['EmailSubject']);
	?>
	<tr>
		<td class="text">
			<?php echo $i;?>
		</td>
		<td class="text">
			<?php echo $emailDate." at ".$emailTime." Hrs";?>
		</td>
		<td class="text">
			<?php echo $emailFrom;?>
		</td>
		<td class="text">
			<?php echo $emailSubject;?>
		</td>
	</tr>
	<tr>
		<td colspan="5">
			<hr size="1" width="100%" color="#4d4d4d">
		</td>
	</tr>
	<?php
			
		}
		echo "<tr><td align='right' colspan='15'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr>";		
	}
	else
	{
		echo "<tr><td height='50'></td></tr><tr><td align='center' class='error'><b>No Unseen Emails Avaialable !!</b></td></tr><tr><td height='200'></td></tr>";
		
	}
    echo "</table>";
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>
