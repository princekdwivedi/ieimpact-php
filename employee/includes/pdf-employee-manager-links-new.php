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

<div class="modern-nav-container">
	<div class="modern-nav-wrapper">
		<nav class="modern-nav-menu" id="modernNavMenu">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/employee-details.php<?php echo $topUrlExtraLinkTestQ;?>" class="modern-nav-link">HOME</a>
			<span class="modern-nav-separator">|</span>
			<div class="modern-nav-dropdown">
				<a href="<?php echo SITE_URL_EMPLOYEES;?>/edit-details.php<?php echo $topUrlExtraLinkTestQ;?>" class="modern-nav-link">PROFILE</a>
				<div class="modern-nav-dropdown-content">
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/edit-details.php">VIEW PROFILE</a>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/change-password.php">CHANGE PASSWORD/IMAGE</a>
				</div>
			</div>
			<span class="modern-nav-separator">|</span>
			<div class="modern-nav-dropdown">
				<a href="<?php echo SITE_URL_EMPLOYEES;?>/apply-leave.php<?php echo $topUrlExtraLinkTestQ;?>" class="modern-nav-link">LEAVE</a>
				<div class="modern-nav-dropdown-content">
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/apply-leave.php">APPLY FOR LEAVE</a>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-leave-status.php">VIEW LEAVE STATUS</a>
				</div>
			</div>
			<?php
				if(!empty($s_hasManagerAccess))
				{
			?>
			<span class="modern-nav-separator">|</span>
			<div class="modern-nav-dropdown">
				<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-employee-details.php<?php echo $topUrlExtraLinkTestQ;?>" class="modern-nav-link">MANAGER</a>
				<div class="modern-nav-dropdown-content modern-nav-mega">
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
					<div class="modern-nav-mega-section">
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/send-notice-to-employees.php" class="modern-nav-mega-title">MESSAGE TO EMPLOYEES</a>
						<a href="<?php echo SITE_URL_EMPLOYEES?>/send-notice-to-employees.php">SEND NOTICE TO EMPLOYEES</a>
						<a href="<?php echo SITE_URL_EMPLOYEES?>/send-emails-to-employees.php">SEND EMAIL TO EMPLOYEES</a>
					</div>
					<?php
						}
					?>
					<div class="modern-nav-mega-section">
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-employee-details.php" class="modern-nav-mega-title">EMPLOYEE DETAILS</a>
					</div>
					<div class="modern-nav-mega-section">
						<a href="<?php echo SITE_URL_EMPLOYEES?>/view-daily-attendences.php" class="modern-nav-mega-title">EMPLOYEES ATTENDANCE</a>
						<a href="<?php echo SITE_URL_EMPLOYEES?>/view-daily-attendences.php">DAILY ATTENDANCE</a>
						<a href="<?php echo SITE_URL_EMPLOYEES?>/view-employee-today-leave.php">LEAVE ON TODAY</a>
						<a href="<?php echo SITE_URL_EMPLOYEES?>/view-monthly-attandance.php">MONTHLY ATTENDANCE</a>
						<a href="<?php echo SITE_URL_EMPLOYEES?>/employee-login-details.php">VIEW LOGIN DETAILS</a>
					</div>
					<div class="modern-nav-mega-section">
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-online-leaves.php" class="modern-nav-mega-title">ONLINE LEAVE APPLIED</a>
						<a href="<?php echo SITE_URL_EMPLOYEES?>/view-online-leaves.php">VIEW LEAVE APPLIED</a>
						<a href="<?php echo SITE_URL_EMPLOYEES?>/view-today-leave.php">VIEW TODAYS ON LEAVE</a>
						<a href="<?php echo SITE_URL_EMPLOYEES?>/reject-approved-holidays.php">REJECT FUTURE LEAVE</a>
					</div>
					<div class="modern-nav-mega-section">
						<a href="<?php echo SITE_URL_EMPLOYEES?>/view-employee-order-processing.php" class="modern-nav-mega-title">PDF DEPARTMENT</a>
						<a href="<?php echo SITE_URL_EMPLOYEES?>/view-employee-order-processing.php">DAILY/MONTHLY WORK</a>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/assign-customer-orders.php">ASSIGN CUSTOMERS ORDERS</a>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/assign-employee-target.php">ASSIGN EMPLOYEE TARGETS</a>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/add-employees-max-orders.php">ASSIGN MAX ACCEPT & SHIFT</a>
						<?php
							if($s_employeeId == 3 || $s_employeeId == 137){
						?>
						<a href="<?php echo SITE_URL_EMPLOYEES?>/view-total-completed-order-ratings-hemant.php">COMPLETED ORDERS RATINGS</a>
						<?php
							}
							else{
						?>
						<a href="<?php echo SITE_URL_EMPLOYEES?>/view-total-completed-order-ratings.php">COMPLETED ORDERS RATINGS</a>
						<?php
							}	  
						?>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-customer-average-ratings.php">CUSTOMER RATING AVERAGE</a>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-employee-average-ratings.php">EMPLOYEE RATING AVERAGE</a>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-team-score.php">TEAM SCORE</a>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/gone-customers.php">LOST CUSTOMERS ORDERS</a>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/employee-status-report.php">VIEW STATUS REPORT</a>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/sending-customer-direct-login.php">SEND CUSTOMER LOGIN LINK</a>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/employee-live-order-operations.php">LIVE EMPLOYEE WORKS</a>
					</div>
					<div class="modern-nav-mega-section">
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/assign-pdf-client-to-employee.php" class="modern-nav-mega-title">ASSIGN PDF CLIENTS</a>
					</div>
					<?php
						if(in_array($s_employeeId,$a_managersTestQuestionAccess)){
					?>
					<div class="modern-nav-mega-section">
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/add-employee-test-questions.php" class="modern-nav-mega-title">ADD/MANAGE TEST QUESTIONS</a>
					</div>
					<?php
						}	
					?>
					<div class="modern-nav-mega-section">
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/update-internal-order-rating.php" class="modern-nav-mega-title">POST AUDIT ERRORS ENTRY</a>
					</div>
					<div class="modern-nav-mega-section">
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/daily-work-status.php" class="modern-nav-mega-title">CURRENT ORDERS STATUS</a>
					</div>
				</div>
			</div>
			<?php
				}
			?>
			<span class="modern-nav-separator">|</span>
			<div class="modern-nav-dropdown">
				<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php<?php echo $topUrlExtraLinkTestQ;?>" class="modern-nav-link">PDF WORKS</a>
				<div class="modern-nav-dropdown-content">
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php<?php echo $topUrlExtraLinkTestQ;?>">SEARCH ORDERS</a>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-ratigs.php">VIEW CUSTOMER RATINGS</a>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?showAllOrders=1#first">VIEW ALL MESSAGES</a>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/upload-file-checklist.php">UPLOAD FILES CHECK LIST</a>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/check-order-status.php">CHECK ORDER STATUS</a>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/create-customer-order.php">CREATE NEW ORDER</a>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/employee-login-ip-specific.php">SERVER USAGE</a>
					<?php
						if($s_hasManagerAccess == 1)
						{
					?>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/edit-customer-order-eta.php">EDIT CUSTOMER ORDER ETA</a>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/edit-customer-profile-type.php">EDIT CUSTOMER PROFILE FILE</a>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/delete-customer-order.php">DELETE CUSTOMER ORDER</a>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/hide-employee-area-ratings.php">HIDE CUSTOMER RATING</a>
					<?php						
						}
					?>
				</div>
			</div>
			<?php
				if(!empty($s_hasAdminAccess))
				{
			?>
			<span class="modern-nav-separator">|</span>
			<div class="modern-nav-dropdown">
				<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-employee-list.php<?php echo $topUrlExtraLinkTestQ;?>" class="modern-nav-link">ADMIN</a>
				<div class="modern-nav-dropdown-content">
					<div class="modern-nav-mega-section">
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-employee-list.php" class="modern-nav-mega-title">EMPLOYEES DETAILS/ACCESS</a>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-employee-list.php">VIEW ALL EMPLOYEES</a>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/allow-qa-access.php">ALLOW QA ACCESS</a>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/print-pdf-employee-order-done.php">PRINT ORDER DONE</a>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/employees-investment-details.php">INVESTMENT DETAILS</a>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/revert-logout-attendance.php">REVERT LOGOUT ATTENDANCE</a>
					</div>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/show-hide-orders-eta.php">SHOW/HIDE ORDERS ETA</a>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/daily-work-status.php">CURRENT ORDERS STATUS</a>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/clientwise-employee-processed.php">MONTHLY ORDERS COMPLETED</a>
					<?php
						if($s_employeeId == 340 || $s_employeeId == 3 || $s_employeeId == 137 || $s_employeeId == 8){
					?>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-employee-monthly-sheet.php">MONTHLY PROCESSED ORDERS</a>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/timezone-wise-customers-orders.php">TIME ZONE WISE ORDERS</a>
					<?php
						}	
					?>
				</div>
			</div>
			<?php
				}
				if(!empty($s_isHavingVerifyAccess))
				{	
			?>
			<span class="modern-nav-separator">|</span>
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/verify-order-messages.php<?php echo $topUrlExtraLinkTestQ;?>" class="modern-nav-link">VERIFY MESSAGES</a>
			<?php
				}
			?>
			<span class="modern-nav-separator">|</span>
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/daily-status-report.php<?php echo $topUrlExtraLinkTestQ;?>" class="modern-nav-link">DAILY REPORT</a>
		</nav>
		<button class="modern-nav-toggle" id="modernNavToggle" aria-label="Toggle navigation">
			<span></span>
			<span></span>
			<span></span>
		</button>
	</div>
</div>

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
