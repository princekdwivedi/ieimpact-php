<!DOCTYPE html>
<html lang="en">
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
	$currentDate				=	date("Y-m-d");
	$isForceToOpenTestPage		=	0;
	$topUrlExtraLinkTestQ       =   "";

	$a_allEmployeesNotQuestions	=	array();
	
?>
<title><?php echo $docTitle;?></title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
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
</script>
<!-- <?php
	if(isset($_SESSION['employeeName']) && $_SESSION['employeeName'] != "" && isset($_SESSION['employeeEmail']) && $_SESSION['employeeEmail'] != "" && isset($_SESSION['hasManagerAccess']) && $_SESSION['hasManagerAccess'] != ""){

		$twakEmpEmail = $_SESSION['employeeEmail'];
?>
<script type="text/javascript">
	var Tawk_API=Tawk_API||{};
	Tawk_API.visitor = {
	name : '<?php echo $_SESSION['employeeName'];?>',
	email : '<?php echo $_SESSION['employeeEmail'];?>',
	hash : '<?php echo hash_hmac("sha256","$twakEmpEmail","bb256d16fa46231f1ab0e04147843894603b24ae"); ?>'
	};

	var Tawk_LoadStart=new Date();

	var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
	(function(){
	var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
	s1.async=true;
	s1.src='https://embed.tawk.to/5cffb138b534676f32ae5e6e/default';
	s1.charset='UTF-8';
	s1.setAttribute('crossorigin','*');
	s0.parentNode.insertBefore(s1,s0);
	})();
</script>
<?php

	}
?> -->
</head>
<body topmargin="0px;">
<div class="mainDiv">
<center>

<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL?>/css/ddlevelsmenu-base.css" />
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/ddlevelsmenu-topbar.css" />
<link rel="shortcut icon" href="/new-favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/ddlevelsmenu.js"></script>
<?php
	if(empty($s_employeeId))
	{
?>
<table cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
	<tr bgcolor="#f0f0f0">
		<td width="30%">
			<img src="<?php echo SITE_URL;?>/images/logo-bg.gif" border="0" title="Innovation. Excellence. i.e. IMPACT">
		</td>
		<td class="heading" valign="top">
			<table cellpadding="2" cellspacing="2" border="0" align="center" width="100%">
				<tr>
					<td height="15"></td>
				</tr>
				<tr>
					<td class="heading">EMPLOYEE AREA</td>
				</tr>
				<tr>
					<td class="title">
						CURRENT DATE : <?php echo showDate($nowDateIndia);?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="3" bgcolor="#000000" height="1"></td>
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
								<li><a>|</a></li>
								<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/registration.php">REGISTRATION</a></li>
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
</table>
<?php
	}
	else
	{
		if(!empty($s_hasPdfAccess))
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


			/////////////////////////////////////////////////////////////////////////////////////////
			/////////////////////////// CHECK IS THERE ANY UNREPLIED MESSAGE AVAILABLE//////////////
			$totalExpiredMsg 				=	0;
			$totalUnrepliedEmpExpiredMesg	=	0;
			$totalUnrepliedGenExpiredMesg	=	0;

			$checkMessagesOriginateTime 	=	date('Y-m-d H:i:s', strtotime("-10 minutes", strtotime(CURRENT_DATE_INDIA." ".CURRENT_TIME_INDIA)));
			////////////////////////// ORDER RELATD COUNTS ////////////////////
			$query = "SELECT COUNT(*) as total FROM customer_orders_messages_counts WHERE concat(messageDate, ' ', messageTime) <= '$checkMessagesOriginateTime'";
			$result= dbQuery($query);
			if(mysqli_num_rowS($result)){
				$row 	= 	mysqli_fetch_assoc($result);
				$totalUnrepliedEmpExpiredMesg = $row['total'];
			}

			////////////////////////// GENERAL MESSAGES COUNTS ////////////////////
			$query = "SELECT COUNT(*) as total FROM members_general_messages WHERE isOrderGeneralMsg=1 AND isBillingMsg=0 AND status=0 AND parentId=0 AND employeeSendingFirstMsg=0 AND concat(addedOn, ' ', addedtime) <= '$checkMessagesOriginateTime'";
			$result= dbQuery($query);
			if(mysqli_num_rowS($result)){
				$row 	     = 	mysqli_fetch_assoc($result);
				$totalUnrepliedGenExpiredMesg = $row['total'];
			}
			$totalExpiredMsg =  $totalUnrepliedEmpExpiredMesg+$totalUnrepliedGenExpiredMesg;		
?>
			<table cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
				<tr bgcolor="#f0f0f0">
					<td width="18%" style="text-align:center">
						<img src="<?php echo SITE_URL;?>/images/logo-bg.gif" border="0" title="Innovation. Excellence. i.e. IMPACT" width="150" height="33">
					</td>
					<td valign="top">
						<?php
							   $totalAvailabaleOrdersToProces=	0;

							   $query 	=	"SELECT count(*) as total FROM members_orders WHERE members_orders.orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND isVirtualDeleted=0 AND status=1 AND acceptedBy=$s_employeeId AND hasRepliedUploaded=0";
								$result =	dbQuery($query);
								if(mysqli_num_rows($result)){
									$row 	=	mysqli_fetch_assoc($result);
									$totalAvailabaleOrdersToProces = $row['total'];
								}

								if(empty($totalAvailabaleOrdersToProces))
								{
									$totalAvailabaleOrdersToProces=	0;
								}
								
						?>
						<table cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
							<tr>
								<td width="60%" valign="top">
									<font class="smalltext23">Welcome <?php echo $s_employeeName;?>,</font> <a href="<?php echo SITE_URL_EMPLOYEES;?>/logout.php" class="link_style4">Logout</a>&nbsp;
									<?php
										if(!empty($totalAvailabaleOrdersToProces))
										{
											
											echo "<font class='textstyle1'>You have <a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&orderOf=".$s_employeeId."&searchOrderType=2&showingEmployeeOrder=1' class='link_style10'>".$totalAvailabaleOrdersToProces."</a> orders remaining to be processed</font>";
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
													/*if(VISITOR_IP_ADDRESS	==	"122.160.167.153"){
														
														$showingTimeRandNumber = 100;
														pr($showingTimeRandNumber);
													}*/
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
									?>
								</td>
								<td valign="top">
									<table cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
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
											?>
											<td>&nbsp;</td>
										</tr>
									</table>
								</td>
							</tr>
							<!-- <?php
								if(!empty($totalExpiredMsg)){
							?>
							<tr>
								<td colspan="3">
									<font class="error2"><b>All the employees on shift will be marked as absent if pending customer messages are not replied back or no action taken within next 10 minutes.</b></font>
								</td>
							</tr>
							<?php
								}
							?> -->
							
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="3" bgcolor="#000000" height="1"></td>
				</tr>
				<tr>
					<td colspan="3">
						<?php
							include(SITE_ROOT_EMPLOYEES."/includes/pdf-employee-manager-links.php");
						?>
					</td>
				</tr>
			</table>
			<?php
		}
		else
		{
	?>
	<table cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
		<tr bgcolor="#f0f0f0">
			<td width="30%">
				<img src="<?php echo SITE_URL;?>/images/logo-bg.gif" border="0" title="Innovation. Excellence. i.e. IMPACT">
			</td>
			<td class="heading" valign="top">
				<table cellpadding="2" cellspacing="2" border="0" align="center" width="100%">
					<tr>
						<td height="15"></td>
					</tr>
					<tr>
						<td class="heading">EMPLOYEE AREA</td>
					</tr>
					<tr>
						<td class="title">
							CURRENT DATE : <?php echo showDate($nowDateIndia);?>
						</td>
					</tr>
					<tr>
						<td>
							<font class="heading1">Welcome <?php echo getSubstring($s_employeeName,18);?>,</font> <a href="<?php echo SITE_URL_EMPLOYEES;?>/logout.php" class="link_style4">Logout</a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" bgcolor="#f0f0f0" height="5"></td>
		</tr>
		<tr>
			<td colspan="3">
				<?php
					if($s_departmentId == 1)
					{
						include(SITE_ROOT_EMPLOYEES."/includes/mt-employee-manager-links.php");
					}
					else
					{
				?>
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr height="25" bgcolor="#373737">
								<td>
									&nbsp;
								</td>
							</tr>
						</table>
				<?php
					}
				?>
			</td>
		</tr>
	</table>
	<?php
		}
	
	}
?>








