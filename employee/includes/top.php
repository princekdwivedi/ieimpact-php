<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php
	if(!isset($docTitle))
	{
		$docTitle		=	"Employee Area";
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
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
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
<table cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
	<tr bgcolor="#f0f0f0">
		<td width="30%">
			<!--<a href="<?php echo SITE_URL;?>"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/logo.jpg" border="0" width="200" height="100"></a>-->
			<img src="<?php echo SITE_URL;?>/images/logo-bg.gif" border="0" title="Innovation. Excellence. i.e. IMPACT">
		</td>
		<td width="30%" class="heading" valign="top">
			<table cellpadding="2" cellspacing="2" border="0" align="center" width="100%">
				<tr>
					<td height="15"></td>
				</tr>
				<tr>
					<td class="heading">EMPLOYEE AREA</td>
				</tr>
				<!-- <tr>
					<td class="title">
						CURRENT DATE : <?php echo showDate($nowDateIndia);?>
					</td>
				</tr> -->
				<tr>
					<td>
							<font class="heading1">Welcome <?php echo getSubstring($s_employeeName,18);?>,</font> <a href="<?php echo SITE_URL_EMPLOYEES;?>/logout.php" class="link_style4">Logout</a>
					</td>
				</tr>
				<?php
					if(!empty($s_employeeId))
					{	

						if(isset($displayEmployeeTargetDone) && $displayEmployeeTargetDone == 1){	
							$thisMonthTotalDone         =   "N/A";
							$lastMonthTotalDone         =   "N/A";
							$thisMonthTarget 	        =	0;
							$lastMonthTarget        	=	0;
							$thisMonthTargetAchieved    =   "N/A";
							$lastMonthTargetAchieved    =   "N/A";
							$currentTargetYear 			=	$checkExceedTataLastYear = date('Y');
							$currentTargetLastYear 	    =	date('Y')-1;


							$checkTargetCurrentMonth    =	date('m');
							$checkTargetLastMonth 		=	date('m')-1;
							if($checkTargetLastMonth < 10 && strlen($checkTargetLastMonth) > 1)
							{
								$checkTargetLastMonth	=	substr($checkTargetLastMonth,1);
							}

							if($checkTargetCurrentMonth < 10 && strlen($checkTargetCurrentMonth) > 1)
							{
								$checkTargetCurrentMonth	=	substr($checkTargetCurrentMonth,1);
							}
							$targetAndCaluse 	        =	" AND targetMonth >= $checkTargetLastMonth AND targetYear=".$currentTargetYear;
							if($checkTargetCurrentMonth == 1){
								$checkTargetLastMonth   =  12;
								$checkExceedTataLastYear=  $currentTargetLastYear;
								$targetAndCaluse 	    =	" AND targetMonth IN (12,1) AND targetYear IN (".$currentTargetLastYear.",".$currentTargetYear.")";
							}

							if(isset($_SESSION['current_month_exceeded_tat'])){
								$currentMonthExceedTat = $_SESSION['current_month_exceeded_tat'];
							}
							else{
								$currentMonthExceedTat = $employeeObj->getSingleQueryResult("select count(*) as total from members_orders where orderId >= ".MAX_SEARCH_EMPLOYEE_ORDER_ID." and isCompletedOnTime=2 and MONTH(orderAddedOn)=$checkTargetCurrentMonth and YEAR(orderAddedOn)=$currentTargetYear and status IN (2,4,5)","total");

								if(empty($currentMonthExceedTat)){
									$currentMonthExceedTat 	=	 0;
								}

								$_SESSION['current_month_exceeded_tat'] = $currentMonthExceedTat;
							}

							

							if(isset($_SESSION['last_month_exceeded_tat'])){
								$lastMonthExceedTat = $_SESSION['last_month_exceeded_tat'];
							}
							else{
								$lastMonthExceedTat = $employeeObj->getSingleQueryResult("select count(*) as total from members_orders where isCompletedOnTime=2 and MONTH(orderAddedOn)=$checkTargetLastMonth and YEAR(orderAddedOn)=$checkExceedTataLastYear and status IN (2,4,5)","total");

								if(empty($lastMonthExceedTat)){
									$lastMonthExceedTat 	=	 0;
								}

								$_SESSION['last_month_exceeded_tat'] = $lastMonthExceedTat;
							}

							

						  	$query	=	"SELECT * FROM employee_target WHERE employeeId=$s_employeeId".$targetAndCaluse." ORDER BY targetMonth DESC";
						  	$result	=	dbQuery($query);
							if(mysqli_num_rows($result))
							{	
								while($row					=	mysqli_fetch_assoc($result))
								{
									$t_processedTarget		=	$row['processedTarget'];
									$t_processedDone		=	$row['processedDone'];
									$t_targetMonth    		=	$row['targetMonth'];
									$t_targetYear    		=	$row['targetYear'];

									if($t_targetMonth 	    == $checkTargetCurrentMonth){
										$thisMonthTotalDone = $t_processedDone;
										$thisMonthTarget    = $t_processedTarget;

										if(!empty($thisMonthTarget)){

											$thisMonthTargetAchieved	=	$thisMonthTotalDone/$thisMonthTarget;
											$thisMonthTargetAchieved	=	$thisMonthTargetAchieved*100;
											$thisMonthTargetAchieved	=	round($thisMonthTargetAchieved,2)."%";
										}
									}
									if($t_targetMonth 	    == $checkTargetLastMonth){
										$lastMonthTotalDone =  $t_processedDone;
										$lastMonthTarget    =  $t_processedTarget;

										if(!empty($lastMonthTarget)){

											$lastMonthTargetAchieved	=	$lastMonthTotalDone/$lastMonthTarget;
											$lastMonthTargetAchieved	=	$lastMonthTargetAchieved*100;
											$lastMonthTargetAchieved	=	round($lastMonthTargetAchieved,2)."%";
										}
									}


								}
							}		
				?>
				<tr>
					<td class="smalltest24">
						Month To Date  - <?php echo $thisMonthTotalDone;?> (Last month <?php echo $lastMonthTotalDone;?>)
					</td>
				</tr>
				<tr>
					<td class="smalltest24">
						Target Achieved.  <?php echo $thisMonthTargetAchieved;?> (Last month <?php echo $lastMonthTargetAchieved;?>)     
					</td>
				</tr>
				<tr>
					<td class="smalltest24">
						Exceeded TAT  <?php echo $currentMonthExceedTat;?> (Last month <?php echo $lastMonthExceedTat;?>) 
					</td>
				</tr>
				<?php
					}
				?>
				
				<?php
					}				
				?>
			</table>
		</td>
		<td valign="bottom">
			<table cellpadding="2" cellspacing="2" border="0" align="center" width="100%">
				<tr>
					<td class="textstyle1" colspan="8" valign="bottom">
						<?php
							if(!empty($s_employeeId) && !empty($s_hasPdfAccess))
							{	
								////////////////////////// UPDATE IS AVAILABLE ON SITE ///////////////
								$query 						=	"SELECT * FROM track_employee_active_on_website WHERE employeeId=".$s_employeeId;
								$result 					=	dbQuery($query);
								if(mysqli_num_rows($result)){
									dbQuery("UPDATE track_employee_active_on_website SET activeDate='".CURRENT_DATE_INDIA."',activeTime='".CURRENT_TIME_INDIA."',employeeIP='".VISITOR_IP_ADDRESS."' WHERE employeeId=$s_employeeId");
								}
								else{
									dbQuery("INSERT INTO track_employee_active_on_website SET employeeId=$s_employeeId,activeDate='".CURRENT_DATE_INDIA."',activeTime='".CURRENT_TIME_INDIA."',employeeIP='".VISITOR_IP_ADDRESS."'");
								}


								/////////////////////////////////////////////////////////////////////
								
								list($searchY,$searchM,$searchD)	=	explode("-",$nowDateIndia);

								$lastMonthDate	=	getLastCalculatedMonthYear($searchM,$searchY,1);
								

								list($lastY,$lastM,$lastD)	=	explode("-",$lastMonthDate);


								$sm			=	$searchM;
								if($searchM < 10)
								{
									$sm     =	substr($searchM,1);
								}

								$lm			=	$lastM;
								if($lastM < 10)
								{
									$lm     =	substr($lastM,1);
								}

							

								$currentMonthTeamScore	=	"";
								$currentMonthEmpScore	=	"";

								$lastMonthTeamScore	    =	"";
								$lastMonthEmpScore	    =	"";

								/////////////////////////////// CURRENT MONTH SCORE ////////////////
								$scoreQuery =	"SELECT * FROM employee_rating_score WHERE month=$sm AND year=$searchY AND ((scoreType='team' AND employeeId=0) OR (scoreType='individual' AND employeeId=$s_employeeId)) ORDER BY scoreId LIMIT 2";
								$scoreResult=	dbQuery($scoreQuery);
								if(mysqli_num_rows($scoreResult)){
									while($scoreRow		=	mysqli_fetch_assoc($scoreResult)){
										$scoreType      =   $scoreRow['scoreType'];
										$score          =   $scoreRow['score'];

										if($scoreType	==  'team'){
											$currentMonthTeamScore	=	$score;
										}
										else{
											$currentMonthEmpScore	=	$score;
										}
									}
								}
								/////////////////////////////// LAST MONTH SCORE ////////////////
								$lastScoreQuery =	"SELECT * FROM employee_rating_score WHERE month=$lm AND year=$lastY AND ((scoreType='team' AND employeeId=0) OR (scoreType='individual' AND employeeId=$s_employeeId)) ORDER BY scoreId LIMIT 2";
								
								$lastScoreResult=	dbQuery($lastScoreQuery);
								if(mysqli_num_rows($lastScoreResult)){
									while($lastScoreRow	=	mysqli_fetch_assoc($lastScoreResult)){
										$scoreType      =   $lastScoreRow['scoreType'];
										$score          =   $lastScoreRow['score'];

										if($scoreType	==  'team'){
											$lastMonthTeamScore	=	$score;
										}
										else{
											$lastMonthEmpScore	=	$score;
										}
									}
								}
								

								$queryTestScore 		=	"SELECT testScore FROM employee_details WHERE employeeId=$s_employeeId";
								$queryTestScoreResult   =   dbQuery($queryTestScore);
								if(mysqli_num_rows($queryTestScoreResult)){
									$queryTestScoreRow	=	mysqli_fetch_assoc($queryTestScoreResult);
									$topemployeeOwnTestScore  =   $queryTestScoreRow['testScore'];
								}



								$totalAveargeRatingScore=	"";

								$queryToGetAverageScore =  "SELECT SUM(score) as totalAverageScore,count(scoreId) as totalAverageMonth FROM employee_rating_score WHERE employeeId=$s_employeeId"; 
								$averageResult			=   dbQuery($queryToGetAverageScore);
								if(mysqli_num_rows($averageResult)){
									$averageResultRow	=	mysqli_fetch_assoc($averageResult);
									$totalAverageScore  =   $averageResultRow['totalAverageScore'];
									$totalAverageMonth  =   $averageResultRow['totalAverageMonth'];

									if(!empty($totalAverageScore)){

										$totalAveargeRatingScore =	$totalAverageScore/$totalAverageMonth;
										$totalAveargeRatingScore = round($totalAveargeRatingScore,2);
									}
									
								}
						?>
						<table width="70%" align="left" border="0" cellpadding="1" cellspacing="1" style="border:2px solid #bebebe">
								<tr>
									<td colspan="3" class="smalltext2">
										<?php
											$empTestQPageLink     = SITE_URL_EMPLOYEES."/employee-test-questions.php";
											
										?>
										<a href="<?php echo $empTestQPageLink;?>" class="link_style14">All Test Questions</a>&nbsp;|&nbsp;<a onclick="showEmployeeTopScorer();" class="link_style14" style="cursor:pointer;">Top Scorers</a>&nbsp;|&nbsp;<b>Your Score : <?php echo $topemployeeOwnTestScore;?></b></td>
								</tr>
								
						<?php
								if(!empty($currentMonthTeamScore) || !empty($lastMonthTeamScore)){
						?>
								
									<tr>
										<td colspan="4" class="smalltext23"><font color='#ff000'><b>Quality Scroes</b></font>&nbsp;(<a href="<?php echo SITE_URL_EMPLOYEES;?>/employee-score.php" class="link_style14">View All</a>)</td>
									</tr>
									<tr>
										<!-- <td width="20%">&nbsp;</td> -->
										<td width="33%" class="smalltext2"><b>Last Month</b></td>
										<td width="33%" class="smalltext2"><b>Current Month</b></td>
										<td class="smalltext2"><b>Quality Score</b></td>
									</tr>									
									<tr>
										<td class="smalltext23">
											<?php
												if(!empty($lastMonthEmpScore)){
													echo "<b>".$lastMonthEmpScore."</b>";
												}
												else{
													echo "N/A";
												}
											?>
										</td>
										<td class="smalltext23">
											<?php
												if(!empty($currentMonthEmpScore)){
													echo "<b>".$currentMonthEmpScore."</b>";
												}
												else{
													echo "N/A";
												}
											?>
										</td>
										<td class="smalltext23">
											<?php
												if(!empty($totalAveargeRatingScore)){
													echo "<b>".$totalAveargeRatingScore."%</b>";
												}
												else{
													echo "N/A";
												}
											?>
										</td>
									</tr>
								
							<?php
								}
							?>
							</table><br /><br /><br /><br /><br /><br />
							<?php							
								
								$totalAcceptedFilesByYou=	0;
								$totalAddedReplyFilesByYou = 0;

								$query 	=	"SELECT count(*) as total FROM members_orders WHERE isVirtualDeleted=0 AND status=1 AND acceptedBy=$s_employeeId";
								$result =	dbQuery($query);
								if(mysqli_num_rows($result)){
									$row 	=	mysqli_fetch_assoc($result);
									$totalAcceptedFilesByYou = $row['total'];
								}

								if(empty($totalAcceptedFilesByYou))
								{
									$totalAcceptedFilesByYou=	0;
								}

								$query 	=	"SELECT count(*) as total FROM members_orders WHERE  orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND isVirtualDeleted=0 AND status=1 AND acceptedBy=$s_employeeId AND hasRepliedUploaded=1";
								$result =	dbQuery($query);
								if(mysqli_num_rows($result)){
									$row 	=	mysqli_fetch_assoc($result);
									$totalAddedReplyFilesByYou = $row['total'];
								}


								if(empty($totalAddedReplyFilesByYou))
								{
									$totalAddedReplyFilesByYou=	0;
								}

								if($totalAcceptedFilesByYou >  $totalAddedReplyFilesByYou)
								{
									$totalAvailabaleOrdersToProces	=	$totalAcceptedFilesByYou-$totalAddedReplyFilesByYou;
									
									echo "You have <a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&orderOf=".$s_employeeId."&searchOrderType=2&showingEmployeeOrder=1' class='link_style10'>".$totalAvailabaleOrdersToProces."</a> orders assigned are remaining to be processed";
								}
								//////////////// CHECKING TEST QUESTIONS /////////////////
								if(!isset($donotShowTestQuestionTop)){
									if(!empty($s_showQuestionnaire)){
									
										$displayTestQuestionLink = false;
										
										if(isset($_SESSION['showTestQuestionId']) && $_SESSION['showTestQuestionId'] != ""){
											$displayTestQuestionLink = true;
										}
										else{
											$showingTimeRandNumber = rand(1,100);
											
											if($showingTimeRandNumber%100 == 0){
												
												$get_questions  =  array();

												$questionQuery	=	"SELECT questionAnswerId FROM employee_test_questions WHERE parentId=0";
												$result			=	dbQuery($questionQuery);
												if(mysqli_num_rows($result)){
													while($row				=	mysqli_fetch_assoc($result)){
														$t_questionAnswerId	=	$row['questionAnswerId'];
														
														$get_questions[] = $t_questionAnswerId;
													}
													shuffle($get_questions);
												}

												if(count($get_questions) > 0){

													$t_questionAnswerId      = $get_questions[0];
													$_SESSION['showTestQuestionId'] = $t_questionAnswerId;
													$displayTestQuestionLink = true;
												}
											}
										}
										if($displayTestQuestionLink){
											
											$showQuestionTest	=	"";

											$query 	=	"SELECT question FROM employee_test_questions WHERE parentId=0 AND questionAnswerId=".$_SESSION['showTestQuestionId'];
											$result =	dbQuery($query);
											if(mysqli_num_rows($result)){
												$row 	=	mysqli_fetch_assoc($result);
												$showQuestionTest = $row['question'];
											}
											if(!empty($showQuestionTest)){
												//echo "<br /><a href='".SITE_URL_EMPLOYEES."/employee-test-questions.php' class='link_style19'>".stripslashes($showQuestionTest)."</a>";

												$isForceToOpenTestPage		=	1;
											}
										}
									}
								}
								
								///////////////////////////////////////////////////////
							}
							else
							{
								echo "&nbsp;";
							}
						?>
					</td>
				</tr>
				<tr>
					<?php
						if(!empty($s_isHavingVerifyAccess))
						{
							$totalAddedReplyFilesByYou	=	0;

							$query 	=	"SELECT count(*) as total FROM members_employee_messages WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND isNeedToVerify=1 AND isDeleted=0 AND isVirtualDeleted=0";
							$result =	dbQuery($query);
							if(mysqli_num_rows($result)){
								$row 	=	mysqli_fetch_assoc($result);
								$totalAddedReplyFilesByYou = $row['total'];
							}

							if($totalAddedReplyFilesByYou > 0)
							{
								
					?>
					<td width="5%" style="text-align:right">
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/verify-order-messages.php" class="link_style19"><?php echo $totalAddedReplyFilesByYou;?></a>&nbsp;
					</td>
					<td width="8%" align="center">
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/verify-order-messages.php" border="0"><img src="<?php echo SITE_URL;?>/images/blinking-new.gif" alt="New Messages To verify" title="New Messages To verify"></a>
					</td>
					<td>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/verify-order-messages.php" class="link_style19"><div id='blink'><b>messages to verify</b></div></a>
					</td>
					<?php
							}
						}
						if(!empty($s_employeeId) && !empty($s_hasPdfAccess))
						{
							$totalEmailOrdersToVerify	=	0;

							$query 	=	"SELECT count(*) as total FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND isEmailOrder=1 AND isNotVerfidedEmailOrder=1 AND isDeleted=0 AND isVirtualDeleted=0";
							$result =	dbQuery($query);
							if(mysqli_num_rows($result)){
								$row 	=	mysqli_fetch_assoc($result);
								$totalEmailOrdersToVerify = $row['total'];
							}

							if($totalEmailOrdersToVerify > 0)
							{
					?>
					<td width="5%" style="text-align:right">
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/verify-email-orders.php" class="link_style19"><?php echo $totalEmailOrdersToVerify;?></a>&nbsp;
					</td>
					<td width="8%" align="center">
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/verify-email-orders.php" border="0"><img src="<?php echo SITE_URL;?>/images/blinking-new.gif" alt="New Orders To verify" title="New Messages To verify"></a>
					</td>
					<td>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/verify-email-orders.php" class="link_style19"><div id='blink1'><b>email orders to verify</b></div></a>
					</td>
					<?php
							}
							if(!empty($s_hasManagerAccess)){
							////////////////// CHECK IS THERE ANY LEAVE APPROVAL PENDING //////
							$query 	=	"SELECT count(*) as total FROM employee_leave_applied INNER JOIN employee_details ON  employee_leave_applied.employeeid=employee_details.employeeId WHERE approvedStatus=0 AND hasPdfAccess=1";
							$result =	dbQuery($query);
							if(mysqli_num_rows($result)){
								$row=	mysqli_fetch_assoc($result);
								$totalPendingLeaveApproved = $row['total'];

								if(!empty($totalPendingLeaveApproved)){
						?>
						<td width="5%" style="text-align:right">
							<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-online-leaves.php" class="link_style19"><?php echo $totalPendingLeaveApproved;?></a>&nbsp;
						</td>
						<td width="8%" align="center">
							<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-online-leaves.php" border="0"><img src="<?php echo SITE_URL;?>/images/blinking-new.gif" alt="New Orders To verify" title="Approved pending leaves"></a>
						</td>
						<td>
							<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-online-leaves.php" class="link_style19"><b>leaves to approved</b></a>
						</td>
						<?php
								}
							 }
							
							}
						}
					?>
					<td>&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="3" bgcolor="#f0f0f0" height="5"></td>
	</tr>
	<?php
		if($s_employeeId)
		{
			if(!empty($s_hasPdfAccess))
			{
				include(SITE_ROOT_EMPLOYEES."/includes/pdf-employee-manager-links.php");
			}
			else
			{
				if($s_departmentId == 1)
				{
					include(SITE_ROOT_EMPLOYEES."/includes/mt-employee-manager-links.php");
				}
				else
				{
			?>
					<tr>
						<td colspan="3">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr height="25" bgcolor="#373737">
									<td>
										&nbsp;
									</td>
								</tr>
							</table>
						</td>
					</tr>
			<?php
				}
			}
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



