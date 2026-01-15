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

	////////////////////////// UPDATING CRON DETAILS ////////////////////////////////////////

	$corn_section				=	"CORN_TRANSFER_ATTENDANCE_DETAILS";
	$query						=	"SELECT * FROM corn_updates_details WHERE section='$corn_section'";
	$time_start					=   microtime(true);
	$result						=	mysql_query($query);
	if(mysql_num_rows($result))
	{
		$row					=	mysql_fetch_assoc($result);
		$cron_update_id			=	$row['Id'];
		$startEstDate			=	$row['startEstDate'];
		$startEstTime			=	$row['startEstTime'];
				
		@mysql_query("UPDATE corn_updates_details SET startEstDate='$customer_zone_date',startEstTime='$customer_zone_time',lastStartEstDate='$startEstDate',lastStartEstTime='$startEstTime' WHERE section='$corn_section' AND Id=$cron_update_id");
	}
	else{
				
		@mysql_query("INSERT INTO corn_updates_details SET section='$corn_section',startEstDate='$customer_zone_date',startEstTime='$customer_zone_time'");
		$cron_update_id			=	mysql_insert_id();
	}
	
	/*************** THIS BLOCK SHOULD UNCOMMENT WHILE YOU WILL UPLOAD ONLINE ***********************
	**************** THIS BLOCK SHOULD COMMENT WHILE YOU WILL UPLOAD ONLINE *************************

	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");


	/*************** THIS BLOCK SHOULDCOMMENT WHILE YOU WILL UPLOAD ONLINE **************************/
	//**********************************************************************************************//
	//////////////////////////////////////////////////////////////////////////////////////////////////

	$updateRecordsTill		=	getPreviousGivenDate($nowDateIndia,1);

	$query					=	"SELECT * FROM employee_attendence WHERE isTransferred=0 AND loginDate >= '$updateRecordsTill' ORDER BY attendenceId";
	$result					=	mysql_query($query);
	if(mysql_num_rows($result)){
		
		while($row			=	mysql_fetch_assoc($result))
		{
			$employeeId		=	$row['employeeId'];
			$attendenceId	=	$row['attendenceId'];
			$isLogin		=	$row['isLogin'];
			$isLogout		=	$row['isLogout'];
			$loginDate		=	$row['loginDate'];
			$loginTime		=	$row['loginTime'];
			$logoutDate		=	$row['logoutDate'];
			$logoutTime		=	$row['logoutTime'];
			$onLeave		=	$row['onLeave'];

			@mysql_query("INSERT INTO temp_corn_employee_attendance SET attendenceId=$attendenceId,employeeId=$employeeId,isLogin=$isLogin,isLogout=$isLogout,loginDate='$loginDate',loginTime='$loginTime',onLeave=$onLeave");

			@mysql_query("UPDATE employee_attendence SET isTransferred=1 WHERE attendenceId=$attendenceId AND employeeId=$employeeId");
		}
	}
	///////////////////////////////// UPDATE END OF CORN TIME ////////////////////////////////
	$end_date_time				=	getDateTimeFromMicrotime();
	list($end_date,$end_time)	=	explode("|", $end_date_time);

	@mysql_query("UPDATE corn_updates_details SET endEstDate='$end_date',endEstTime='$end_time' WHERE section='$corn_section' AND Id=$cron_update_id");

?>