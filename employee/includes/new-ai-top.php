<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php
	if(!isset($docTitle))
	{
		$docTitle		=	"ieIMPACT Employee Area";
	}
	if(!isset($docKeywords))
	{
		$docKeywords	=	"Employee Area";
	}
	if(!isset($docDescription))
	{
		$docDescription	=	"Employee Area";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/check-site-maintanence.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	$currentDate	=	date("Y-m-d");
	$isForceToOpenTestPage		=	0;
	$topUrlExtraLinkTestQ		=   "";

	$a_allEmployeesNotQuestions	=	array();

	
?>
<TITLE><?php echo $docTitle;?></TITLE>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/modern-employee.css" rel="stylesheet">
<script type="text/javascript">
	window.onload=blinkOn;

	function blinkOn()
	{
	  document.getElementById("blink").style.color="#333333";
	  document.getElementById("blink1").style.color="#333333";
	  setTimeout("blinkOff()",1000);
	}
	 
	function blinkOff()
	{
	  document.getElementById("blink").style.color="";
	  document.getElementById("blink1").style.color="";
	  setTimeout("blinkOn()",1000);
	}

	function showEmployeeTopScorer()
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/view-top-scorer-test-questions.php";
		prop = "toolbar=no,scrollbars=yes,width=440,height=400,top=100,left=100";
		window.open(path,'',prop);
	}


</script>
</head>
<body topmargin="0">
<div class="mainDiv">
<center>

<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL?>/css/ddlevelsmenu-base.css" />
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/ddlevelsmenu-topbar.css" />
<link rel="shortcut icon" href="/new-favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/ddlevelsmenu.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/modern-employee.js"></script>
<div class="modern-header">
	<div class="modern-header-top-row">
		<div class="modern-header-logo-left">
			<img src="<?php echo SITE_URL;?>/images/logo-bg.gif" border="0" title="Innovation. Excellence. i.e. IMPACT" alt="ieIMPACT Logo">
		</div>
		<div class="modern-header-welcome-center">
			<div class="modern-header-title">EMPLOYEE AREA</div>
			<div class="modern-header-welcome">
				Welcome <?php echo $s_employeeName;?>, <a href="<?php echo SITE_URL_EMPLOYEES;?>/logout.php">Logout</a>
			</div>
		</div>
	</div>
	<div class="modern-header-info-line-full">
					<?php
						if(!empty($s_employeeId))
						{	
							// Initialize variables
							$thisMonthTotalDone = "N/A";
							$lastMonthTotalDone = "N/A";
							$thisMonthTargetAchieved = "N/A";
							$lastMonthTargetAchieved = "N/A";
							$currentMonthExceedTat = 0;
							$lastMonthExceedTat = 0;
							$topemployeeOwnTestScore = "";
							$totalEmailOrdersToVerify = 0;
							$totalAddedReplyFilesByYou = 0;

							if(isset($displayEmployeeTargetDone) && $displayEmployeeTargetDone == 1){	
								$thisMonthTarget = 0;
								$lastMonthTarget = 0;
								$currentTargetYear = $checkExceedTataLastYear = date('Y');
								$currentTargetLastYear = date('Y')-1;

								$checkTargetCurrentMonth = date('m');
								$checkTargetLastMonth = date('m')-1;
								if($checkTargetLastMonth < 10 && strlen($checkTargetLastMonth) > 1)
								{
									$checkTargetLastMonth = substr($checkTargetLastMonth,1);
								}

								if($checkTargetCurrentMonth < 10 && strlen($checkTargetCurrentMonth) > 1)
								{
									$checkTargetCurrentMonth = substr($checkTargetCurrentMonth,1);
								}
								$targetAndCaluse = " AND targetMonth >= $checkTargetLastMonth AND targetYear=".$currentTargetYear;
								if($checkTargetCurrentMonth == 1){
									$checkTargetLastMonth = 12;
									$checkExceedTataLastYear = $currentTargetLastYear;
									$targetAndCaluse = " AND targetMonth IN (12,1) AND targetYear IN (".$currentTargetLastYear.",".$currentTargetYear.")";
								}

								if(isset($_SESSION['current_month_exceeded_tat'])){
									$currentMonthExceedTat = $_SESSION['current_month_exceeded_tat'];
								}
								else{
									$currentMonthExceedTat = $employeeObj->getSingleQueryResult("select count(*) as total from members_orders where orderId >= ".MAX_SEARCH_EMPLOYEE_ORDER_ID." and isCompletedOnTime=2 and MONTH(orderAddedOn)=$checkTargetCurrentMonth and YEAR(orderAddedOn)=$currentTargetYear and status IN (2,4,5)","total");
									if(empty($currentMonthExceedTat)){
										$currentMonthExceedTat = 0;
									}
									$_SESSION['current_month_exceeded_tat'] = $currentMonthExceedTat;
								}

								if(isset($_SESSION['last_month_exceeded_tat'])){
									$lastMonthExceedTat = $_SESSION['last_month_exceeded_tat'];
								}
								else{
									$lastMonthExceedTat = $employeeObj->getSingleQueryResult("select count(*) as total from members_orders where isCompletedOnTime=2 and MONTH(orderAddedOn)=$checkTargetLastMonth and YEAR(orderAddedOn)=$checkExceedTataLastYear and status IN (2,4,5)","total");
									if(empty($lastMonthExceedTat)){
										$lastMonthExceedTat = 0;
									}
									$_SESSION['last_month_exceeded_tat'] = $lastMonthExceedTat;
								}

								$query = "SELECT * FROM employee_target WHERE employeeId=$s_employeeId".$targetAndCaluse." ORDER BY targetMonth DESC";
								$result = dbQuery($query);
								if(mysqli_num_rows($result))
								{	
									while($row = mysqli_fetch_assoc($result))
									{
										$t_processedTarget = $row['processedTarget'];
										$t_processedDone = $row['processedDone'];
										$t_targetMonth = $row['targetMonth'];
										$t_targetYear = $row['targetYear'];

										if($t_targetMonth == $checkTargetCurrentMonth){
											$thisMonthTotalDone = $t_processedDone;
											$thisMonthTarget = $t_processedTarget;
											if(!empty($thisMonthTarget)){
												$thisMonthTargetAchieved = $thisMonthTotalDone/$thisMonthTarget;
												$thisMonthTargetAchieved = $thisMonthTargetAchieved*100;
												$thisMonthTargetAchieved = round($thisMonthTargetAchieved,2)."%";
											}
										}
										if($t_targetMonth == $checkTargetLastMonth){
											$lastMonthTotalDone = $t_processedDone;
											$lastMonthTarget = $t_processedTarget;
											if(!empty($lastMonthTarget)){
												$lastMonthTargetAchieved = $lastMonthTotalDone/$lastMonthTarget;
												$lastMonthTargetAchieved = $lastMonthTargetAchieved*100;
												$lastMonthTargetAchieved = round($lastMonthTargetAchieved,2)."%";
											}
										}
									}
								}
							}

							// Get test score and email orders info
							if(!empty($s_employeeId) && !empty($s_hasPdfAccess))
							{	
								$query = "SELECT * FROM track_employee_active_on_website WHERE employeeId=".$s_employeeId;
								$result = dbQuery($query);
								if(mysqli_num_rows($result)){
									dbQuery("UPDATE track_employee_active_on_website SET activeDate='".CURRENT_DATE_INDIA."',activeTime='".CURRENT_TIME_INDIA."',employeeIP='".VISITOR_IP_ADDRESS."' WHERE employeeId=$s_employeeId");
								}
								else{
									dbQuery("INSERT INTO track_employee_active_on_website SET employeeId=$s_employeeId,activeDate='".CURRENT_DATE_INDIA."',activeTime='".CURRENT_TIME_INDIA."',employeeIP='".VISITOR_IP_ADDRESS."'");
								}

								$queryTestScore = "SELECT testScore FROM employee_details WHERE employeeId=$s_employeeId";
								$queryTestScoreResult = dbQuery($queryTestScore);
								if(mysqli_num_rows($queryTestScoreResult)){
									$queryTestScoreRow = mysqli_fetch_assoc($queryTestScoreResult);
									$topemployeeOwnTestScore = $queryTestScoreRow['testScore'];
								}

								$query = "SELECT count(*) as total FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND isEmailOrder=1 AND isNotVerfidedEmailOrder=1 AND isDeleted=0 AND isVirtualDeleted=0";
								$result = dbQuery($query);
								if(mysqli_num_rows($result)){
									$row = mysqli_fetch_assoc($result);
									$totalEmailOrdersToVerify = $row['total'];
								}
							}

							// Get messages to verify
							if(!empty($s_isHavingVerifyAccess))
							{
								$query = "SELECT count(*) as total FROM members_employee_messages WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND isNeedToVerify=1 AND isDeleted=0 AND isVirtualDeleted=0";
								$result = dbQuery($query);
								if(mysqli_num_rows($result)){
									$row = mysqli_fetch_assoc($result);
									$totalAddedReplyFilesByYou = $row['total'];
								}
							}

							$empTestQPageLink = SITE_URL_EMPLOYEES."/employee-test-questions.php";
					?>
					<div class="info-line-item">
						<span class="info-label">Month To Date:</span> <span class="info-value"><?php echo $thisMonthTotalDone;?></span> <span class="info-sub">(Last: <?php echo $lastMonthTotalDone;?>)</span>
					</div>
					<div class="info-line-separator">|</div>
					<div class="info-line-item">
						<span class="info-label">Target:</span> <span class="info-value"><?php echo $thisMonthTargetAchieved;?></span> <span class="info-sub">(Last: <?php echo $lastMonthTargetAchieved;?>)</span>
					</div>
					<div class="info-line-separator">|</div>
					<div class="info-line-item">
						<span class="info-label">Exceeded TAT:</span> <span class="info-value"><?php echo $currentMonthExceedTat;?></span> <span class="info-sub">(Last: <?php echo $lastMonthExceedTat;?>)</span>
					</div>
					<div class="info-line-separator">|</div>
					<div class="info-line-item">
						<a href="<?php echo $empTestQPageLink;?>" class="info-link">All Test Questions</a>
					</div>
					<div class="info-line-separator">|</div>
					<div class="info-line-item">
						<a onclick="showEmployeeTopScorer();" class="info-link" style="cursor:pointer;">Top Scorers</a>
					</div>
					<div class="info-line-separator">|</div>
					<div class="info-line-item">
						<span class="info-label">Your Score:</span> <span class="info-value"><?php echo $topemployeeOwnTestScore;?></span>
					</div>
					<?php if($totalAddedReplyFilesByYou > 0) { ?>
					<div class="info-line-separator">|</div>
					<div class="info-line-item info-alert">
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/verify-order-messages.php" class="info-link-alert">
							<span class="alert-badge"><?php echo $totalAddedReplyFilesByYou;?></span>
							<span>messages to verify</span>
						</a>
					</div>
					<?php } ?>
					<?php if($totalEmailOrdersToVerify > 0) { ?>
					<div class="info-line-separator">|</div>
					<div class="info-line-item info-alert">
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/verify-email-orders.php" class="info-link-alert">
							<span class="alert-badge"><?php echo $totalEmailOrdersToVerify;?></span>
							<span>email orders to verify</span>
						</a>
					</div>
					<?php } ?>
					<?php
						}
					?>
	</div>
</div>
<div style="height: 5px; background: #f0f0f0;"></div>
<table cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
	<tr>
	<?php
		if($s_employeeId)
		{
			include(SITE_ROOT_EMPLOYEES."/includes/pdf-employee-manager-links-new.php");
		}
		else
		{
	?>
	<tr>
		<td colspan="3" bgcolor="#000000" height="1">
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr height="25" bgcolor="#373737">
					<td>
						<?php
							if(!isset($isResetPasswordPage))
							{
						?>
						<div id="ddtopmenubar" class="mattblackmenu">
							<ul>
								
								<li><a href="<?php echo SITE_URL_EMPLOYEES;?>">LOGIN</a></li>
								<!-- <li><a>|</a></li>
								<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/registration.php">REGISTRATION</a></li> -->
							</ul>
						</div>
						<?php
							}
							else
							{
								echo "&nbsp;&nbsp;<font class='text4'>RESET PASSWORD</font>";
							}
						?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<?php
		}
	?>
</table>



