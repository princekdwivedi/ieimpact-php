<?php
	$showTestQuestionLink        = 0;
	$topUrlExtraLinkTestQ	     = "";
	$addTopUrlExtraLinkTestQ     = "";
	if(isset($_SESSION['showTestQuestionId']) && $_SESSION['showTestQuestionId'] != ""){
		$topUrlExtraLinkTestQ    = "?yesDisplayTestQuestion=1";
		$addTopUrlExtraLinkTestQ = "&yesDisplayTestQuestion=1";
	}

	$a_allowChangeRatingInternalAccess	=	array('637','3','8','137','946');
?>
<script type="text/javascript">
	function openGpt()
	{
		path = "<?php echo SITE_URL?>/chat/index.php";
		prop = "toolbar=no,scrollbars=yes,width=750,height=600,top=100,left=100";
		window.open(path,'',prop);
	}
</script>

<tr>
	<td colspan="3" bgcolor="#000000" height="1"></td>
</tr>
<tr>
	<td colspan="3">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr height="25" bgcolor="#373737">
			<td width="83%">
				<div id="ddtopmenubar" class="mattblackmenu">
					<ul>
						<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/employee-details.php<?php echo $topUrlExtraLinkTestQ;?>">HOME</a></li>
						<li><a>|</a></li>
						<!--<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/add-break-time.php<?php echo $topUrlExtraLinkTestQ;?>">BREAK TIME</a></li>
						<li><a>|</a></li>-->
						<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/edit-details.php<?php echo $topUrlExtraLinkTestQ;?>" rel="ddsubmenu2">PROFILE</a></li>
						<li><a>|</a></li>
						<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/apply-leave.php<?php echo $topUrlExtraLinkTestQ;?>" rel="ddsubmenu4">LEAVE</a></li>
						<!--<li><a>|</a></li>
						<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/add-manage-tax-certificates.php<?php echo $topUrlExtraLinkTestQ;?>">INVESTMENT DETAILS</a></li>-->
								
						<?php
							if(!empty($s_hasManagerAccess))
							{
						?>
							<li><a>|</a></li>
							<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/view-employee-details.php<?php echo $topUrlExtraLinkTestQ;?>" rel="ddsubmenu3">MANAGER</a></li>
						<?php
							}
							
						?>
						<li><a>|</a></li>
						<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php<?php echo $topUrlExtraLinkTestQ;?>" rel="ddsubmenu5">PDF WORKS</a></li>
					<?php
						if(!empty($s_hasAdminAccess))
						{
					?>
					<li><a>|</a></li>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/view-employee-list.php<?php echo $topUrlExtraLinkTestQ;?>"	rel="ddsubmenu7">ADMIN</a></li>
					<?php

						}
						if(!empty($s_isHavingVerifyAccess))
						{	
					?>
							<li><a>|</a></li>
							<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/verify-order-messages.php<?php echo $topUrlExtraLinkTestQ;?>" rel="ddsubmenu6">VERIFY MESSAGES</a></li>
					<?php
						}
								
						?>
						<li><a>|</a></li>
						<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/daily-status-report.php<?php echo $topUrlExtraLinkTestQ;?>" rel="ddsubmenu9">DAILY REPORT</a></li>
						<li><a>|</a></li>
						<li><a onclick="openGpt();" style="cursor:pointer;">AI Help</a></li>
					</ul>
				</div>
				<script type="text/javascript">
					ddlevelsmenu.setup("ddtopmenubar", "topbar") 
				</script>
				<ul id="ddsubmenu2" class="ddsubmenustyle">
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/edit-details.php">VEW PROFILE</a></li>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/change-password.php">CHANGE PASSWORD/IMAGE</a></li>
				</ul>
				<ul id="ddsubmenu4" class="ddsubmenustyle">
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/apply-leave.php">APPLY FOR LEAVE</a></li>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/view-leave-status.php">VIEW LEAVE STATUS</a></li>
				</ul>
				<?php
					if(!empty($s_hasManagerAccess))
					{
				?>
				<ul id="ddsubmenu3" class="ddsubmenustyle">
					<?php
						if(strstr($_SERVER['HTTP_HOST'],'ieimpact.com') || strstr($_SERVER['HTTP_HOST'],'ieimpact.net'))
						{
							$a_managerLevelEmployees	=	array("3"=>"3","5"=>"5","137"=>"137","449"=>"449","340"=>"340","587"=>"587","8"=>"8","637"=>"637","946"=>"946");
						}
						else
						{
							$a_managerLevelEmployees	=	array("3"=>"3");
						}
						if(in_array($s_employeeId,$a_managerLevelEmployees))
						{
					?>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/send-notice-to-employees.php">MESSAGE TO EMPLOYEES</a>
					<ul>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/send-notice-to-employees.php">SEND NOTICE TO EMPLOYEES</a></li>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/send-emails-to-employees.php">SEND EMAIL TO EMPLOYEES</a></li>
						 <!-- <?php
							if($s_employeeId		==	3 || $s_employeeId	==	8 || $s_employeeId	==	449 || $s_employeeId	==	137 || $s_employeeId	==	637)
							{
						 ?>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/sending-sms-to-employees.php">SEND SMS TO EMPLOYEES</a></li>
						 <?php
							}
						  ?>-->
					</ul>
					</li>
					<?php
						}
					?>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/view-employee-details.php">EMPLOYEE DETAILS</a></li>
					<li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-daily-attendences.php">EMPLOYEES ATTENDANCE</a>
					<ul>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-daily-attendences.php">DAILY ATTENDANCE</a></li>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-employee-today-leave.php">LEAVE ON TODAY</a></li>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-monthly-attandance.php">MONTHLY ATTENDANCE</a></li>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/employee-login-details.php">VIEW LOGIN DETAILS</a></li>
						  <!-- <li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-employee-break-details.php">VIEW BREAK DETAILS</a></li> -->
					</ul>
					<li>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-online-leaves.php">ONLINE LEAVE APPLIED</a>
						<ul>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-online-leaves.php">VIEW LEAVE APPLIED</a></li>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-today-leave.php">VIEW TODAYS ON LEAVE</a></li>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/reject-approved-holidays.php">REJECT FUTURE LEAVE</a></li>
						</ul>
					</li>
					<li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-employee-order-processing.php">PDF DEPARTMENT</a>
						<ul>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-employee-order-processing.php">DAILY/MONTHLY WORK</a></li>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES;?>/assign-customer-orders.php">ASSIGN CUSTOMERS ORDERS</a></li>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES;?>/assign-employee-target.php">ASSIGN EMPLOYEE TARGETS</a></li>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES;?>/add-employees-max-orders.php">ASSIGN MAX ACCEPT & SHIFT</a></li>
						  <?php
							if($s_employeeId == 3 || $s_employeeId == 137){
						  ?>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-total-completed-order-ratings-hemant.php">COMPLETED ORDERS RATINGS</a></li>
						  <?php
							}
							else{
						  ?>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-total-completed-order-ratings.php">COMPLETED ORDERS RATINGS</a></li>
						  <?php
							}	  
						  ?>
						  <!--<li><a href="<?php echo SITE_URL_EMPLOYEES?>/change-order-status.php">CHANGE ORDER EMPLOYEES</a></li>-->
						  <li><a href="<?php echo SITE_URL_EMPLOYEES;?>/view-customer-average-ratings.php"> CUSTOMER RATING AVERAGE</a></li>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES;?>/view-employee-average-ratings.php"> EMPLOYEE RATING AVERAGE</a></li>
						  <!--<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/employee-reply-on-order-ratings.php"> EXPLANATION ON RATING</a></li>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES;?>/employee-incentives-on-ratings.php"> INCENTIVES ON RATING</a></li>--->
						  <li><a href="<?php echo SITE_URL_EMPLOYEES;?>/view-team-score.php"> TEAM SCORE</a></li>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES;?>/gone-customers.php"> LOST CUSTOMERS ORDERS</a></li>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES;?>/employee-status-report.php"> VIEW STATUS REPORT</a></li>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES;?>/sending-customer-direct-login.php">SEND CUSTOMER LOGIN LINK</a></li>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES;?>/employee-live-order-operations.php">LIVE EMPLOYEE WORKS</a></li>
						</ul>
					    <!--<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/add-accounts-details.php">MANAGE ACCOUNTS</a>
						<ul>
							 <li><a href="<?php echo SITE_URL_EMPLOYEES?>/add-accounts-details.php">ADD DEBIT AND CREDIT</a></li>
							 <?php
								if($s_hasManagerAccess == 1)
								{
									if($s_employeeId == 3 || $s_employeeId == 5 || $s_employeeId == 8 || $s_employeeId == 137)
									{
							?>
							 <li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-accounts-statements.php">VIEW STATEMENTS</a></li>
							  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/cash-cheque-details.php">CASH/CHEQUE DETAILS</a></li>
							 <li><a href="<?php echo SITE_URL_EMPLOYEES?>/cash-cheque-statements.php">CASH/CHEQUE STATEMENTS</a></li>
							 <li><a href="<?php echo SITE_URL_EMPLOYEES?>/pdf-employee-salary.php">PDF EMPLOYEES SALARY</a></li>
							 <?php
									}
								}	
							?>
							
							 <li><a href="<?php echo SITE_URL_EMPLOYEES?>/employee-salary-sheet.php">PRINT EMPLOYEES SALARY</a></li>
							 <li><a href="<?php echo SITE_URL_EMPLOYEES?>/all-employee-salary-sheet.php">SALARY PAID TO EMPLOYEES</a></li>
						</ul>
					  </li>-->
					  <li><a href="<?php echo SITE_URL_EMPLOYEES;?>/assign-pdf-client-to-employee.php">ASSIGN PDF CLIENTS</a></li>
					  <!--<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/update-vocal-customer.php">UPDATE VOCAL CUSTOMER</a></li>-->
					  <?php
						if(in_array($s_employeeId,$a_managersTestQuestionAccess)){
					  ?>
					  <li><a href="<?php echo SITE_URL_EMPLOYEES;?>/add-employee-test-questions.php">ADD/MANAGE TEST QUESTIONS</a></li>
					  <?php
						}	
					    
					  ?>
					  <li><a href="<?php echo SITE_URL_EMPLOYEES;?>/update-internal-order-rating.php">POST AUDIT ERRORS ENTRY</a></li>
					  <li><a href="<?php echo SITE_URL_EMPLOYEES;?>/daily-work-status.php">CURRENT ORDERS STATUS</a></li>
					  
					  <!-- <li><a href="<?php echo SITE_URL_EMPLOYEES;?>/employees-internet-speed.php">EMPLOYEES INTERNET SPEED</a></li> -->
					  
					 </ul>
					<?php
					}
					if(!empty($s_hasPdfAccess))
					{
				?>
				<ul id="ddsubmenu5" class="ddsubmenustyle">
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php<?php echo $topUrlExtraLinkTestQ;?>">SEARCH ORDERS</a></li>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-ratigs.php">VIEW CUSTOMER RATINGS</a></li>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?showAllOrders=1#first">VIEW ALL MESSAGES</a></li>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/upload-file-checklist.php">UPLOAD FILES CHECK LIST</a></li>
					<!--<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/view-post-audit-details.php">POST AUDIT RESULTS</a></li>-->
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/check-order-status.php">CHECK ORDER STATUS</a></li>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/create-customer-order.php">CREATE NEW ORDER</a></li>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/employee-login-ip-specific.php">SERVER USAGE</a></li>
					<?php
						if($s_hasManagerAccess == 1)
						{
					?>
					
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/edit-customer-order-eta.php">EDIT CUSTOMER ORDER ETA</a></li>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/edit-customer-profile-type.php"> EDIT CUSTOMER PROFILE FILE</a></li>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/delete-customer-order.php">DELETE CUSTOMER ORDER</a></li>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/hide-employee-area-ratings.php">HIDE CUSTOMER RATING</a></li>
					<?php						
						}
						
					?>
				</ul>
				<?php
					}
					if(!empty($s_hasAdminAccess))
					{
				?>
				<ul id="ddsubmenu7" class="ddsubmenustyle">
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/view-employee-list.php">EMPLOYEES DETAILS/ACCESS</a>
						<ul>
							<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/view-employee-list.php">VIEW ALL EMPLOYEES</a></li>
							<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/allow-qa-access.php">ALLLOW QA ACCESS</a></li>
							<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/print-pdf-employee-order-done.php">PRINT ORDER DONE</a></li>
							<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/employees-investment-details.php">INVESTMENT DETAILS</a></li>
							<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/revert-logout-attendance.php">REVERT LOGOUT ATTENDANCE</a></li>
						</ul>
					</li>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/show-hide-orders-eta.php">SHOW/HIDE ORDERS ETA</a></li>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/daily-work-status.php">CURRENT ORDERS STATUS</a></li>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/clientwise-employee-processed.php">MONTHLY ORDERS COMPLETED</a></li>
					<?php
						if($s_employeeId == 340 || $s_employeeId == 3 || $s_employeeId == 137 || $s_employeeId == 8){
					?>
						<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/view-employee-monthly-sheet.php">MONTHLY PROCESSED ORDERS</a></li>
						<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/timezone-wise-customers-orders.php">TIME ZONE WISE ORDERS</a></li>
					<?php
						}	
					?>
				</ul>
				<?php
					}
				?>
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
	</table>
</tr>
<tr>
	<td colspan="3" bgcolor="#000000" height="1">
	</td>
</tr>
<tr>
	<td colspan="3" height="10">
		
	</td>
</tr>
<?php
	if(isset($_GET['yesDisplayTestQuestion'])){
		$yesDisplayTestQuestion	=	$_GET['yesDisplayTestQuestion'];
		if(!empty($yesDisplayTestQuestion)){
			$showTestQuestionLink			=	1;
		}
	}
	if(!isset($isEmployeeDefaultHomePage) && $showTestQuestionLink == 1){
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/employee-test-questions.php");
		exit();
	}
?>
