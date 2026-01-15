<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");

	include(SITE_ROOT_EMPLOYEES		.   "/includes/test-top.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES		.   "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/common-array.php");
	$employeeObj					=	new employee();
	include(SITE_ROOT_EMPLOYEES		.   "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES		.   "/classes/orders.php");
	$orderObj						=   new orders();

	$isLastOutByEmployee			=	1;//THIS MEANS EMPLOYEE LAST LOGOUT
	$lastLoginDifference			=	"";
	$processedDone					=	0;
	$qaDone							=	0;
	$existingAttendenceId			=	0;
	$attendenceId                   =   0;
	//$isEmployeeDefaultHomePage		=	1;
	$checkYesterdayStatus		    =   0;

	////////////////////////// CHECK IS ADDED TODAY/YESTERDAY STATUS ////////////////////////
	$yesterdayDate					=	date('Y-m-d', strtotime("-1 day", strtotime($nowDateIndia)));
	$dayBeforeYesterdayDate			=	date('Y-m-d', strtotime("-2 day", strtotime($nowDateIndia)));
	$loginDateClause				=	" AND reportDate='$yesterdayDate'";

	if($s_employeeId == 1977 || $s_employeeId == 1978 || $s_employeeId == 3){
		$loginDateClause				=	" AND reportDate IN ('$dayBeforeYesterdayDate', '$yesterdayDate','$nowDateIndia')";
	}
	else{
		$loginDateClause				=	" AND reportDate IN ('$yesterdayDate','$nowDateIndia')";
	}
	
	

	$isLoginYesterday				=	$employeeObj->getSingleQueryResult("SELECT attendenceId FROM employee_attendence WHERE employeeId=$s_employeeId AND isLogin=1 AND loginDate='$yesterdayDate'","attendenceId");
	if(!empty($isLoginYesterday)){
		$isAddedWork				=	$employeeObj->getSingleQueryResult("SELECT statusId FROM employee_daily_status_report WHERE employeeId=$s_employeeId".$loginDateClause." LIMIT 1","statusId");
		if(empty($isAddedWork)){
			$checkYesterdayStatus   =   1;
		}
	}

	if($s_employeeId == 1977 || $s_employeeId == 1978 || $s_employeeId == 3){
		$isAddedTodayStatus				=	$employeeObj->getSingleQueryResult("SELECT statusId FROM employee_daily_status_report WHERE employeeId=$s_employeeId AND reportDate IN ('$yesterdayDate','$nowDateIndia')","statusId");
	}
	else{
		$isAddedTodayStatus				=	$employeeObj->getSingleQueryResult("SELECT statusId FROM employee_daily_status_report WHERE employeeId=$s_employeeId AND reportDate='$nowDateIndia'","statusId");
	}
	if(empty($isAddedTodayStatus)){
		$isAddedTodayStatus         =   0;
	}

	

	////////////////////////////////////////////////////////////////////////////////////
		

    $query							=	"SELECT * FROM temp_corn_employee_attendance WHERE employeeId=$s_employeeId AND loginDate='".$nowDateIndia."'";
	$result							=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		$row						=	mysqli_fetch_assoc($result);
		$attendenceId				=	$existingAttendenceId = $row['attendenceId'];
		$isLogin					=	$row['isLogin'];
		$isLogout					=	$row['isLogout'];
		$loginDate					=	$row['loginDate'];
		$loginTime					=	$row['loginTime'];
		$logoutDate					=	$row['logoutDate'];
		$logoutTime					=	$row['logoutTime'];
		$onLeave					=	$row['onLeave'];
		$isMarkedAbsent				=	$row['isMarkedAbsent'];
	}

	if(empty($attendenceId)){
		$query						=	"SELECT * FROM employee_attendence WHERE attendenceId > ".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID." AND employeeId=$s_employeeId ORDER BY attendenceId DESC LIMIT 1";
		$result						=	dbQuery($query);
		if(mysqli_num_rows($result))
		{	
			$row					=	mysqli_fetch_assoc($result);
			$last_attendenceId		=	$row['attendenceId'];
			$last_isLogin			=	$row['isLogin'];
			$last_isLogout			=	$row['isLogout'];
			$last_loginDate			=	$row['loginDate'];
			$last_loginTime			=	$row['loginTime'];
			$last_logoutDate		=	$row['logoutDate'];
			$last_logoutTime		=	$row['logoutTime'];
			$last_onLeave			=	$row['onLeave'];
			$last_isMarkedAbsent	=	$row['isMarkedAbsent'];

			if($last_isLogin		==	1 && $last_isLogout == 0 && ($last_onLeave == 0 || $last_onLeave == 2)){
				$time_difference	=	timeBetweenTwoTimes($last_loginDate,$last_loginTime,$nowDateIndia,$nowTimeIndia);
				if($time_difference <= 720){
					$isLastOutByEmployee			=	0;//THIS MEANS EMPLOYEE LAST NOT LOGOUT
					$lastLoginDifference			=	$time_difference;
				}
			}
		}
	}

	$a_officeIPAddress				=	array();
	$visitorIpAddress				=	VISITOR_IP_ADDRESS;
	$employeeImagePath				=	SITE_ROOT."/files/employee-images/";
	$employeeImageUrl				=	SITE_URL."/files/employee-images/";

	list($currentY,$currentM,$currentD)	=	explode("-",$nowDateIndia);

	$nonLeadingZeroMonth		=	$currentM;
	if($currentM < 10 && strlen($currentM) > 1)
	{
		$nonLeadingZeroMonth	=	substr($currentM,1);
	}

	$query						=	"SELECT processedDone,qaDone FROM employee_target WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$currentY AND employeeId=$s_employeeId";
	$result						=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		$row					=	mysqli_fetch_assoc($result);
		$processedDone			=	$row['processedDone'];
		$qaDone					=	$row['qaDone'];
	}

	$query						=	"SELECT * FROM office_ip_addresses_list WHERE isActive='yes'";
	$result						=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row				=	mysqli_fetch_assoc($result)){
			$ipAddress			=	stripslashes($row['ipAddress']);
			$isActive			=	stripslashes($row['isActive']);

			$a_officeIPAddress[]		=	$ipAddress;
		}
	}
	$a_officeIPAddress			=	"";
	
	if(isset($_GET['addLogin']) && $_GET['addLogin'] == 1)
	{
		$addLogin				=	(int)$_GET['addLogin'];
		if($addLogin			==	1  && $isLogin == 0)
		{
			
			if($employeeType		==	1)
			{
				if(!empty($a_officeIPAddress))
				{
					if(!in_array($visitorIpAddress,$a_officeIPAddress))
					{
						ob_clean();
						header("Location: ".SITE_URL_EMPLOYEES ."/employee-details.php");
						exit();
					}
				}
			}
			
			if(empty($existingAttendenceId))
			{
				////////////////////// CHECK IS CAME ON SIFT TIMINGS /////////////////
				$allowLogin			=	1;
				if(!empty($shiftFrom)){
					if($nowTimeIndia > $shiftFrom){
						 $to_time   = strtotime($nowTimeIndia);
						 $from_time = strtotime($shiftFrom);
						 $time_diff = $to_time- $from_time;
						 $time_diff = $time_diff/60;
						 $time_diff = round($time_diff);
						 if($time_diff > 15){
							$allowLogin			=	0;
						 }
					}
				}

				if($allowLogin == 0){
					dbQuery("INSERT INTO employee_attendence SET employeeId=$s_employeeId,onLeave=1,loginDate='".$nowDateIndia."',loginTime='".$nowTimeIndia."',loginIP='".VISITOR_IP_ADDRESS."',isTransferred=1,isLogin=0,isForLateAttendance=1,isMarkedAbsent=1");

					$t_attendenceId	=	mysqli_insert_id($db_conn);

					@dbQuery("INSERT INTO temp_corn_employee_attendance SET attendenceId=$t_attendenceId,isLogin=0,onLeave=1,employeeId=$s_employeeId,loginDate='".$nowDateIndia."',loginTime='".$nowTimeIndia."',isForLateAttendance=1,isMarkedAbsent=1");

					$attendanceMarkedAs		=	3;//Abesnt
					$isAHalfDay				=	0;
				}
				else{					
					dbQuery("INSERT INTO employee_attendence SET employeeId=$s_employeeId,isLogin=1,loginDate='".$nowDateIndia."',loginTime='".$nowTimeIndia."',loginIP='".VISITOR_IP_ADDRESS."',isTransferred=1");

					$t_attendenceId	=	mysqli_insert_id($db_conn);

					@dbQuery("INSERT INTO temp_corn_employee_attendance SET attendenceId=$t_attendenceId,isLogin=1,employeeId=$s_employeeId,loginDate='".$nowDateIndia."',loginTime='".$nowTimeIndia."'");

					$attendanceMarkedAs		=	1;//Present
					$isAHalfDay				=	0;
				}
			}
			else
			{
				dbQuery("UPDATE employee_attendence SET isLogin=1,loginDate='".$nowDateIndia."',loginTime='".$nowTimeIndia."',loginIP='".VISITOR_IP_ADDRESS."' WHERE employeeId=$s_employeeId AND attendenceId=$existingAttendenceId");

				$attendanceMarkedAs		=	2;//Half Day
				$isAHalfDay				=	1;
			}

			$totalDaysInMonth	=	$a_daysInMonth[$nonLeadingZeroMonth];

			$employeeObj->updateEmployeeAttendanceTracking($s_employeeId,$attendanceMarkedAs,$s_employeeName,$totalDaysInMonth,$currentD,$currentM,$currentY,$isAHalfDay,1);			
		}
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES ."/employee-details.php");
		exit();
		
	}
	if(isset($_GET['outLogin']) && $_GET['outLogin'] == 1)
	{
		$outLogin		=	(int)$_GET['outLogin'];
		if(isset($_GET['outForId']))
		{
			$outForId	=	$_GET['outForId'];

			dbQuery("UPDATE employee_attendence SET isLogout=1,logoutDate='".$nowDateIndia."',logoutTime='".$nowTimeIndia."',logoutIP='".VISITOR_IP_ADDRESS."' WHERE attendenceId=$outForId AND employeeId=$s_employeeId");

			dbQuery("UPDATE temp_corn_employee_attendance SET isLogout=1,logoutDate='".$nowDateIndia."',logoutTime='".$nowTimeIndia."' WHERE attendenceId=$outForId");
			
		}
		else
		{
			if($outLogin	==	1  && $isLogin == 1 && $isLogout == 0 && !empty($attendenceId))
			{							
				dbQuery("UPDATE employee_attendence SET isLogout=1,logoutDate='".$nowDateIndia."',logoutTime='".$nowTimeIndia."',logoutIP='".VISITOR_IP_ADDRESS."' WHERE attendenceId=$attendenceId AND employeeId=$s_employeeId");

				dbQuery("UPDATE temp_corn_employee_attendance SET isLogout=1,logoutDate='".$nowDateIndia."',logoutTime='".$nowTimeIndia."' WHERE attendenceId=$attendenceId");
			
			}
		
		}	
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES ."/employee-details.php");
		exit();
		
	}
	if(isset($_GET['messageId']) && isset($_GET['operation']))
	{
		$messageId		=	(int)$_GET['messageId'];
		$operation		=	(int)$_GET['operation'];
		if(!empty($messageId))
		{
			if($operation	==	1)
			{
				dbQuery("UPDATE employee_messages SET isRead=1,readOn='".CURRENT_DATE_INDIA."' WHERE employeeId=$s_employeeId AND messageId=$messageId AND isDeleted=0 AND isRead=0");
			}
			elseif($operation	==	2)
			{
				dbQuery("UPDATE employee_messages SET isDeleted=1,deletedOn='".CURRENT_DATE_INDIA."' WHERE employeeId=$s_employeeId AND messageId=$messageId AND isDeleted=0 AND isRead=1");
			}

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES ."/employee-details.php");
			exit();
		}
	}

	$allTotalCustomersNewOrders	=	$employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total FROM members_orders WHERE orderId > ".MAX_SEARCH_EMPLOYEE_ORDER_ID." AND status=0 AND orderAddedOn >= '2012-04-01' AND isDeleted=0 AND isVirtualDeleted=0 AND isNotVerfidedEmailOrder=0","total");

	$totalUnrepliedOrdersMsg	=	$orderObj->getTotalUnrepliedOrderMessage();
	$totalUnrepliedRatingMsg	=	$orderObj->getAllTotalUnrepliedRatingMessage();
	$a_unrepliedGeneralMsg		=	$orderObj->getAllUnrepliedGeneralMessageCustomers();
	$totalUnrepliedGeneralMsg   =   count($a_unrepliedGeneralMsg);
	$totalUncheckedOrders		=	$orderObj->getAllTotalUncheckedOrders();
	$totalExceedTatOrders		=	$orderObj->getAllTotalExceedTatOrders();

?>
<script type="text/javascript">
function addAttendence()
{
	var confirmation = window.confirm("Are you sure to add your attendance?");
	if(confirmation == true)
	{
		window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/employee-details.php?addLogin=1';
	}
}

function addWorkStatus(flag)
{
	var msg			=  "Yesterday you didn't added your daily work, add now.";
	var param       =  "Y";
	if(flag == "2"){
		var msg	    =  "You didn't added your daily work, add now.";
		var param   =  "T";
	}
	var confirmation= window.confirm(msg);
	if(confirmation == true)
	{
		window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/daily-status-report.php?param=0h876qwe335432mnddkjf787l4hdr&h=7hytbg577&type='+param;
	}
}

function outAttendence()
{
	var confirmation = window.confirm("Are you sure to out your attendance?");
	if(confirmation == true)
	{
		window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/employee-details.php?outLogin=1';
	}
}
function readDeleteNotice(messageId,operation)
{
	if(operation == 1)
	{
		var confirmation = window.confirm("Are you sure to mark as read?");
	}
	else
	{
		var confirmation = window.confirm("Are you sure to delete this message?");
	}
	if(confirmation == true)
	{
		window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/employee-details.php?messageId='+messageId+'&operation='+operation;
	}
}
function openWindow(messageId)
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/reply-message.php?messageId="+messageId;
	prop = "toolbar=no,scrollbars=yes,width=650,height=450,top=50,left=100";
	window.open(path,'',prop);
}

function outAttendenceNew(outForId)
{
	var confirmation = window.confirm("Are you sure to out your attendance?");
	if(confirmation == true)
	{
		window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/employee-details.php?outLogin=1&outForId='+outForId;
	}
}

function addEditMemberProfilePhoto(flag)
{
	path			=	"<?php echo SITE_URL_EMPLOYEES;?>/add-edit-profile-photo.php?P="+flag;
	properties	=	"height=360,width=440,top=150,left=250,scrollbars=yes,top=100,left=200";
	it			=	window.open(path,'',properties);
}

function serachRedirectFileType(addUrl,extraAdd)
{	
	window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php"+addUrl+extraAdd;
}
</script>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
	<tr>
		<td width="20%" valign="top">
			<table width='100%' align='center' cellpadding='1' cellspacing='1' border='0'>
				<?php
					if(!empty($totalNewPdfOrders))
					{
				?>
				<tr>
					<td>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&searchOrderType=1&showPageOrders=50<?php echo $addTopUrlExtraLinkTestQ;?>" class='link_style36' style="cursor:pointer;" title='View new orders'>NEW ORDERS - <?php echo $totalNewPdfOrders;?></a> 
					</td>
				</tr>
				<?php
						
					}
				?>
				<tr>
					<td>
						<a onclick="serachRedirectFileType('?isSubmittedForm=1&orderOf=<?php echo $s_employeeId;?>&showingEmployeeOrder=1&Olink=1','<?php echo $addTopUrlExtraLinkTestQ;?>')" class='link_style36' style="cursor:pointer;" title='View all of your processed orders'>ALL YOUR ORDERS </a> 
					</td>
				</tr>
				<tr>
					<td>
						<a onclick="serachRedirectFileType('?isSubmittedForm=1&orderOf=<?php echo $s_employeeId;?>&showingEmployeeOrder=1&displayTypeCompleted=1&Olink=2','<?php echo $addTopUrlExtraLinkTestQ;?>')" class='link_style36' style="cursor:pointer;" title='View all of your QA orders'>ALL YOUR QA ORDERS </a>
					</td>
				</tr>


				<?php
				if(!empty($allTotalCustomersNewOrders) && !empty($s_hasManagerAccess))
				{
					//$assignNewUrl	 =	SITE_URL_EMPLOYEES."/assign-customer-orders.php";
					$assignNewUrl	 =	SITE_URL_EMPLOYEES."/assign-all-new-orders.php".$topUrlExtraLinkTestQ;
					
			?>
			<tr>
				<td>
					<a href="<?php echo $assignNewUrl;?>" class='link_style36' style="cursor:pointer;">ASSIGN ALL NEW ORDERS - <?php echo $allTotalCustomersNewOrders;?></a>
				</td>
			</tr>
			<?php
				}
				if(!empty($totalUnrepliedOrdersMsg))
				{
				?>
			<tr>
				<td>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?unrepliedMsg=1<?php echo $addTopUrlExtraLinkTestQ;?>#second" class='link_style36' style="cursor:pointer;">UNREPLIED MESSAGES - <?php echo $totalUnrepliedOrdersMsg;?></a>
				</td>
			</tr>
			<?php
				}
				if(!empty($totalUnrepliedRatingMsg))
				{
				?>
			<tr>
				<td>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?unrepliedRatingMsg=1<?php echo $addTopUrlExtraLinkTestQ;?>#third" class='link_style36' style="cursor:pointer;">UNREPLIED RATINGS - <?php echo $totalUnrepliedRatingMsg;?></a> 
				</td>
			</tr>
			<?php
				}
				if(!empty($totalUnrepliedGeneralMsg))
				{
				?>
			<tr>
				<td>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?unrepliedGeneralMsg=1<?php echo $addTopUrlExtraLinkTestQ;?>#fifth" class='link_style36' style="cursor:pointer;">GENERAL MESSAGES - <?php echo $totalUnrepliedGeneralMsg;?></a>
				</td>
			</tr>
			<?php
				}
				if(!empty($totalExceedTatOrders))
				{
			?>
			<tr>
				<td>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&searchExceedTat=1<?php echo $addTopUrlExtraLinkTestQ;?>" class='link_style36' style="cursor:pointer;">EXCEEDED TAT - <?php echo $totalExceedTatOrders;?></a>
				</td>
			</tr>
			<?php
				}
				if(!empty($totalUncheckedOrders))
				{
			?>
			<tr>
				<td>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&searchUnchecked=1<?php echo $addTopUrlExtraLinkTestQ;?>" class='link_style36' style="cursor:pointer;">UNCHECKED ORDERS - <?php echo $totalUncheckedOrders;?></a>
				</td>
			</tr>
			<?php
				}
			?>
			</table>
		</td>
		<td width="30%" valign="top">
			<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
				<tr>
					<td class="textstyle1" valign="top" colspan="5">
						<b>DAILY ATTENDANCE</b>&nbsp;[Shift : <?php echo showTimeShortFormat($shiftFrom)."-".showTimeShortFormat($shiftTo);?>]
					</td>
				</tr>
				<tr>
					<td height="5"></td>
				</tr>
				<tr>
					<td colspan="5">
						<?php
							if($isLastOutByEmployee == 1)
							{
								if($isLogin == 0)
								{
									if($attendenceId == 0 && $onLeave != 1)
									{
										if($employeeType		==	1)
										{
											if(!empty($a_officeIPAddress))
											{
												if(in_array($visitorIpAddress,$a_officeIPAddress))
												{
													if($checkYesterdayStatus == 1){
														echo "<a onclick='addWorkStatus(1)' class='link_style2' style='cursor:pointer;'>CLICK TO MARK YOUR TODAY'S ATTENDANCE</a>";
													}
													else{
														echo "<a onclick='addAttendence()' class='link_style2' style='cursor:pointer;'>CLICK TO MARK YOUR TODAY'S ATTENDANCE</a>";
													}
												}
												else
												{
													echo "<font class='error'><b>You cannot add your attendance from out side office</b></font>";
												}
											}
											else
											{
												if($checkYesterdayStatus == 1){
													echo "<a onclick='addWorkStatus(1)' class='link_style2' style='cursor:pointer;'>CLICK TO MARK YOUR TODAY'S ATTENDANCE</a>";
												}
												else{
													echo "<a onclick='addAttendence()' class='link_style2'  style='cursor:pointer;'>CLICK TO MARK YOUR TODAY'S ATTENDANCE</a>";
												}
											}
										}
										else
										{
											if($checkYesterdayStatus == 1){
												echo "<a onclick='addWorkStatus(1)' class='link_style2' style='cursor:pointer;'>CLICK TO MARK YOUR TODAY'S ATTENDANCE</a>";
											}
											else{
												echo "<a onclick='addAttendence()' class='link_style2'  style='cursor:pointer;'>CLICK TO MARK YOUR TODAY'S ATTENDANCE</a>";
											}
										}
									}
									elseif(!empty($attendenceId) && $onLeave == 1)
									{
										if($isMarkedAbsent == 1){
											echo "<font class='error'><b>You Are Absent Today</b></font>";
										}
										else{
											echo "<font class='error'><b>You Are On Leave Today</b></font>";
										}
									}
									elseif(!empty($attendenceId) && $onLeave == 2)
									{
										if($checkYesterdayStatus == 1){
											echo "<a onclick='addWorkStatus(1)' class='link_style2' style='cursor:pointer;'>CLICK TO MARK YOUR TODAY'S ATTENDANCE</a><br>";
										}
										else{
											echo "<a onclick='addAttendence()' class='link_style2'  style='cursor:pointer;'>ADD YOUR ATTENDANCE</a><br>";
										}
										echo "<font class='error'><b>You Are On Half Leave Today</b></font>";
									}
								}
								else
								{
									if($loginDate != "0000-00-00" && $loginTime != "00:00:00")
									{
										echo "<font class='smalltext23'>You Marked Your Attendance At  - ".showTimeShortFormat($loginTime)." Hrs</font>";
									}
								}
							}
							else
							{
								echo "<font class='smalltext23'>You Marked Your Attendance At - ".showDate($last_loginDate)."/".showTimeShortFormat($last_loginTime)."</font>";
							}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="5">
						<?php
							if($isLastOutByEmployee == 1)
							{
								if($isLogin == 1 && !empty($attendenceId) && $isLogout == 0)
								{
									if($isAddedTodayStatus == 0){
										echo "<a onclick='addWorkStatus(2)' class='link_style2' style='cursor:pointer;'>OUT YOUR ATTENDANCE</a>";
									}
									else{
										echo "<a onclick='outAttendence()' class='link_style2' style='cursor:pointer;'>OUT YOUR ATTENDANCE</a>";
									}
									
									if(!empty($attendenceId) && $onLeave == 2)
									{
										echo "<br><font class='error'><b>You Are On Half Leave Today</b></font>";
									}
								}
								else
								{
									if($loginDate != "0000-00-00" && $loginTime != "00:00:00" && $logoutDate != "0000-00-00" && $logoutTime != "00:00:00")
									{
										$logoutTime	=	date("H:i",strtotime($logoutTime));
										echo "<font class='smalltext23'>Out Your Attendance At - ".$logoutTime." Hrs</font>";

										if(!empty($attendenceId) && $onLeave == 2)
										{
											echo "<br><font class='error'><b>You Are On Half Leave Today</b></font>";
										}
									}
								}
							}
							else{
								if($isAddedTodayStatus == 0){
									echo "<a onclick='addWorkStatus(2)' class='link_style2' style='cursor:pointer;'>OUT YOUR ATTENDANCE</a>";
								}
								else{
									echo "<a onclick='outAttendenceNew($last_attendenceId)' class='link_style2' style='cursor:pointer;'>OUT YOUR ATTENDANCE</a>";
								}
							}
						?>
					</td>
				</tr>
				<tr>
					<td height="2"></td>
				</tr>
				
				<?php
					$query	=	"SELECT * FROM employee_attendence WHERE attendenceId > ".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID." AND employeeId=$s_employeeId AND loginDate <> '".$nowDateIndia."' ORDER BY attendenceId DESC LIMIT 5";
					$result	=	dbQuery($query);
					if(mysqli_num_rows($result)){
				?>
				<tr>
					<td class="smalltext3" valign="top" colspan="5">
						<b>VIEW YOUR LAST 5 DAYS ATTENDANCE</b>&nbsp;(<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-attendance-details.php" class="link_style6">View All</a>)
					</td>
				</tr>
				<tr>
					<td height="5"></td>
				</tr>
				<tr height="20" style="border:1px solid #bebebe;background-color:#0080FF;">
					<td class="smalltext22" valign="top" width="10%">
						<b>&nbsp;Sr No</b>
					</td>
					<td class="smalltext22" valign="top" width="25%">
						<b>Date</b>
					</td>
					<td class="smalltext22" valign="top" width="30%">
						<b>In Time</b>
					</td>
					<td class="smalltext22" valign="top">
						<b>Out Time</b>
					</td>
				</tr>
				<?php
						$k				=	0;
						while($row		=	mysqli_fetch_assoc($result)){
							$k++;
							$loginDate	     =	showDate($row['loginDate']);
							$loginTime	     =	showTimeShortFormat($row['loginTime']);
							$logoutDate	     =	showDate($row['logoutDate']);
							$logoutTime	     =	showTimeShortFormat($row['logoutTime']);
							$isWeeklyOff	 =	$row['isWeeklyOff'];
							$t_isMarkedAbsent=	$row['isMarkedAbsent'];

							if($logoutDate != "0000-00-00" && $logoutTime != "00:00:00")
							{
								$outDateTime	=	$logoutDate."/".$logoutTime;
							}
							else{
								$outDateTime	=	"";
							}

				?>
				<tr>
					<td class="smalltext21" valign="top">
						&nbsp;<?php echo $k;?>)
					</td>
					<td class="smalltext21" valign="top">
						<?php echo $loginDate?>
					</td>
					<?php
						if($isWeeklyOff		==	1){
					?>
					<td class="smalltext21" colspan="2" valign="top" style="text-align:center">
						Weekly Off
					</td>
					<?php
						}
						elseif($t_isMarkedAbsent == 1){
					?>
						<td class="error" colspan="2" valign="top" style="text-align:center">
							Absent
						</td>
					<?php	
						}
						else{
					?>
					<td class="smalltext21" valign="top">
						<?php echo $loginTime;?>
					</td>
					<td class="smalltext21" valign="top">
						<?php echo $outDateTime;?>
					</td>
					<?php
						}	
					?>
				</tr>
				<?php
						}
					}
					/*if(empty($aadhaarNumber)){
				?>
				<tr>
					<td colspan="6"><br />
						<span id="blinker">(<a href="<?php echo SITE_URL_EMPLOYEES;?>/update-aadhaar-number.php" class="link_style19">Update Your Aadhaar</a>)</span>
						<script>
							var blink_speed = 800; var t = setInterval(function () { var ele = document.getElementById('blinker'); ele.style.visibility = (ele.style.visibility == 'hidden' ? '' : 'hidden'); }, blink_speed);
						</script>
					</td>
				</tr>
				<?php
					}*/
				
				?>
			</table>
		</td>
		<td valign="top">
			<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
				<tr>
					<td colspan="8" class="textstyle1"><b>GENERAL & IMPORTANT MESSAGES FOR YOU</b></td>
				</tr>
				<?php
					


					$isMessages			=	0;
					$query	=	"SELECT * FROM employee_messages WHERE displayFrom <= '$nowDateIndia' AND '$nowDateIndia' <= displayTo AND employeeId=0 AND departmentId=0 ORDER BY displayFrom DESC";
					$result			=	dbQuery($query);
					if(mysqli_num_rows($result))
					{
						$isMessages			=	1;
						while($row			=	mysqli_fetch_assoc($result))
						{
							$title			=	$row['title'];
							$message		=	$row['message'];
							$addedOn		=	showDate($row['addedOn']);
							
							$title			=	stripslashes($title);
							$message		=	stripslashes($message);
							$message		=	nl2br($message);
							$addedByName	=	stripslashes($row['addedByName']);

							if(empty($addedByName))
							{
								$addedByName=	"Rishi Jindal";
							}
					?>
					<tr>
						<td class="smalltext23">
							<?php echo $title;?>
						</td>
					</tr>
					<tr>
						<td class="textstyle">
							<p align="justify">
								<?php echo $message;?>
							</p>
						</td>
					</tr>
					<tr>
						<td class="smalltext21" align="left">
							FROM : <?php echo $addedByName;?> On <?php echo $addedOn;?>
						</td>
					</tr>
					<tr>
						<td>
							<hr size="1" width="100%" color="#e4e4e4">
						</td>
					</tr>
					<?php
						}
					}
					$query				=	"SELECT * FROM employee_messages WHERE displayFrom <= '$nowDateIndia' AND '$nowDateIndia' <= displayTo AND employeeId=$s_employeeId AND departmentId=0 AND isDeleted=0 ORDER BY displayFrom DESC";
					$result			=	dbQuery($query);
					if(mysqli_num_rows($result))
					{
						$isMessages			=	1;

						while($row	=	mysqli_fetch_assoc($result))
						{
							$messageId			=	$row['messageId'];
							$title				=	$row['title'];
							$message			=	$row['message'];
							$addedOn			=	showDate($row['addedOn']);
							
							$title				=	stripslashes($title);
							$message			=	stripslashes($message);
							$message			=	nl2br($message);
							$isRead				=	$row['isRead'];
							$isReplied			=	$row['isReplied'];
							$readOn				=	$row['readOn'];

							$addedByName			=	stripslashes($row['addedByName']);

							if(empty($addedByName))
							{
								$addedByName=	"Rishi Jindal";
							}

					?>
					<tr>
						<td class="smalltext23">
							<?php echo $title;?>
						</td>
					</tr>
					<tr>
						<td class="textstyle">
							<p align="justify">
								<?php echo $message;?>
							</p>
						</td>
					</tr>
					<tr>
						<td class="smalltext21"  align="right">
							Message On 
							<?php 
								echo $addedOn;
								if($isRead == 1 && $readOn != "0000-00-00")
								{
									echo "&nbsp;&nbsp;|&nbsp;&nbsp;Read On ".showDate($readOn);
								}
							?>&nbsp;&nbsp;&nbsp;
						</td>
					</tr>
					<tr>
						<td class="title2" align="right">
							<?php 
								if($isRead == 0)
								{
									echo "<a onclick='readDeleteNotice($messageId,1)' class='link_style7'  style='cursor:pointer;'>Mark As Read</a>";
								}
								else
								{
									echo "<a onclick='readDeleteNotice($messageId,2)' class='link_style7'  style='cursor:pointer;'>Delete</a>";
								}
								if($isReplied	==	0)
								{
									echo "&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick='openWindow($messageId)' class='link_style7'  style='cursor:pointer;'>Reply To This Notice</a>";
								}
							?>
							&nbsp;&nbsp;&nbsp;
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<hr size="1" width="100%" color="#e4e4e4">
						</td>
					</tr>
					<?php
						}

					}
					$query			=	"SELECT * FROM employee_messages WHERE displayFrom <= '$nowDateIndia' AND '$nowDateIndia' <= displayTo AND employeeId=$s_employeeId AND departmentId=3 AND isDeleted=0 ORDER BY displayFrom DESC";
					$result			=	dbQuery($query);
					if(mysqli_num_rows($result))
					{
						$isMessages			=	1;
						while($row		=	mysqli_fetch_assoc($result))
						{
							$messageId	=	$row['messageId'];
							$title		=	$row['title'];
							$message	=	$row['message'];
							$addedOn	=	showDate($row['addedOn']);
							
							$title		=	stripslashes($title);
							$message	=	stripslashes($message);
							$message	=	nl2br($message);
							$isRead		=	$row['isRead'];
							$isReplied	=	$row['isReplied'];
							$readOn		=	$row['readOn'];
				?>

				<tr>
					<td class="smalltext23">
						<?php echo $title;?>
					</td>
				</tr>
				<tr>
					<td class="textstyle">
						<p align="justify">
							<?php echo $message;?>
						</p>
					</td>
				</tr>
				<tr>
					<td class="smalltext21"  align="right">
						Message On 
						<?php 
							echo $addedOn;
							if($isRead == 1 && $readOn != "0000-00-00")
							{
								echo "&nbsp;&nbsp;|&nbsp;&nbsp;Read On ".showDate($readOn);
							}
						?>&nbsp;&nbsp;&nbsp;
					</td>
				</tr>
				<tr>
					<td class="title2" align="right">
						<?php 
							if($isRead == 0)
							{
								echo "<a onclick='readDeleteNotice($messageId,1)' class='link_style7' style='cursor:pointer;'>Mark As Read</a>";
							}
							else
							{
								echo "<a onclick='readDeleteNotice($messageId,2)' class='link_style7' style='cursor:pointer;'>Delete</a>";
							}
							if($isReplied	==	0)
							{
								echo "&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick='openWindow($messageId)' class='link_style7' style='cursor:pointer;'>Reply To This Notice</a>";
							}
						?>
						&nbsp;&nbsp;&nbsp;
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<hr size="1" width="100%" color="#e4e4e4">
					</td>
				</tr>
				<?php
						}

					}
					$query	=	"SELECT * FROM employee_messages WHERE displayFrom <= '$nowDateIndia' AND '$nowDateIndia' <= displayTo AND employeeId=0 AND departmentId=3 ORDER BY displayFrom DESC";
					$result	=	dbQuery($query);
					if(mysqli_num_rows($result))
					{
						$isMessages			=	1;
						while($row	=	mysqli_fetch_assoc($result))
						{
							$title			=	$row['title'];
							$message		=	$row['message'];
							$addedOn		=	showDate($row['addedOn']);
							
							$title			=	stripslashes($title);
							$message		=	stripslashes($message);
							$message		=	nl2br($message);

							$addedByName	=	stripslashes($row['addedByName']);

							if(empty($addedByName))
							{
								$addedByName=	"Rishi Jindal";
							}
					?>
					<tr>
						<td class="smalltext23">
							<?php echo $title;?>
						</td>
					</tr>
					<tr>
						<td class="textstyle">
							<p align="justify">
								<?php echo $message;?>
							</p>
						</td>
					</tr>
					<tr>
						<td class="smalltext1" align="right">
							<b>FROM : <?php echo $addedByName;?> On <?php echo $addedOn;?></b>&nbsp;&nbsp;&nbsp;
						</td>
					</tr>
					<tr>
						<td>
							<hr size="1" width="100%" color="#e4e4e4">
						</td>
					</tr>
					<?php

						}
					}
					if(empty($isMessages)){
				?>
				<tr>
					<td align="center" class="error2" style="text-align:center" height="190"><b>No Messages for now</b></td>
				</tr>
				<?php
					}
				?>
			</table>
		</td>
	</tr>
</table>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>