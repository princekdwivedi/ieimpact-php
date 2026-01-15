<tr>
	<td colspan="3" bgcolor="#000000" height="1"></td>
</tr>
<tr>
	<td colspan="3">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr height="25" bgcolor="#373737">
			<td>
				<div id="ddtopmenubar" class="mattblackmenu">
					<ul>
						<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/employee-details.php">HOME</a></li>
						<li><a>|</a></li>
						<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/add-break-time.php">BREAK TIME</a></li>
						<li><a>|</a></li>
						<?php
							if($s_departmentId == 1)
							{
						?>
							<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/add-daily-work.php" rel="ddsubmenu1">LINES</a></li>
							<li><a>|</a></li>
						<?php
							
							}
						?>
						
						<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/edit-details.php" rel="ddsubmenu2">PROFILE</a></li>
						<li><a>|</a></li>
						<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/apply-leave.php" rel="ddsubmenu4">LEAVE</a></li>
						<?php
							if(!empty($s_hasManagerAccess))
							{
						?>
							<li><a>|</a></li>
							<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/view-employee-details.php" rel="ddsubmenu3">MANAGER</a></li>
						<?php
							}
						?>
					</ul>
				</div>
				<script type="text/javascript">
					ddlevelsmenu.setup("ddtopmenubar", "topbar") 
				</script>
				
				<ul id="ddsubmenu1" class="ddsubmenustyle">
				<?php
					if($s_departmentId == 1)
					{
				?>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/add-daily-work.php">ADD DAILY LINES</a></li>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/manage-work.php">VIEW MONTHLY LINES</a></li>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/view-work-total.php">VIEW PREVIOUS MONTHS' LINES</a></li>
				<?php
					}
				?>
				</ul>
				<ul id="ddsubmenu2" class="ddsubmenustyle">
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/edit-details.php">EDIT PROFILE</a></li>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/change-password.php">CHANGE PASSWORD</a></li>
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
							$a_managerLevelEmployees	=	array("3"=>"3","5"=>"5","137"=>"137","449"=>"449","340"=>"340","44"=>"44");
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
						  <?php
							if($s_employeeId		==	5 || $s_employeeId	==	449)
							{
						 ?>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/sending-sms-to-employees.php">SEND SMS TO EMPLOYEES</a></li>
						 <?php
							}
						  ?>
					</ul>
					</li>
					<?php
						}
					?>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/view-employee-details.php">EMPLOYEE DETAILS</a>
					<ul>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-employee-details.php">EDIT EMPLOYEE DETAILS</a></li>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/add-edit-accuracy.php">ADD EDIT ACCURACY</a></li>
					</ul>
					<li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-daily-attendences.php">EMPLOYEES ATTENDANCE</a>
					<ul>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-daily-attendences.php">DAILY ATTENDANCE</a></li>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-monthly-attandance.php">MONTHLY ATTENDANCE</a></li>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/employee-login-details.php">VIEW LOGIN DETAILS</a></li>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-employee-break-details.php">VIEW BREAK DETAILS</a></li>
					</ul>
					<li>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-online-leaves.php">ONLINE LEAVE APPLIED</a>
						<ul>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-online-leaves.php">VIEW LEAVE APPLIED</a></li>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-today-leave.php">VIEW TODAYS ON LEAVE</a></li>
						  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/reject-approved-holidays.php">REJECT FUTURE LEAVE</a></li>
						</ul>
					</li>
					<li><a href="<?php echo SITE_URL_EMPLOYEES?>/daily-work-report.php">MT DEPARTMENT</a>
						<ul>
							  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/daily-work-report.php">DAILY WORK REPORT</a></li>
							   <li><a href="<?php echo SITE_URL_EMPLOYEES?>/daily-idle-employee.php">VIEW IDLE EMPLOYEE</a></li>
							   <li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-monthly-worksheet.php">MONTHLY WORK REPORT</a></li>
							  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/add-edit-employee-report.php">ADD EDIT EMPLOYEE WORK</a></li>
							  <li><a href="<?php echo SITE_URL_EMPLOYEES?>/assign-mt-employee-target.php">ASSIGN MT WORK TARGET</a></li>
						</ul>
					</li>
					<li><a href="<?php echo SITE_URL_EMPLOYEES;?>/add-edit-platform.php">PLATFORM</a>
						<ul>
							 <li><a href="<?php echo SITE_URL_EMPLOYEES?>/add-edit-platform.php">ADD EDIT PLATFORM</a></li>
							 <li><a href="<?php echo SITE_URL_EMPLOYEES?>/add-edit-clients.php">ADD EDIT CLIENT</a></li>
						</ul>
					  </li>
					  <li><a href="<?php echo SITE_URL_EMPLOYEES;?>/add-accounts-details.php">MANAGE ACCOUNTS</a>
						<ul>
							
							 <?php
								if($s_hasManagerAccess == 1)
								{
									if($s_employeeId == 3 || $s_employeeId == 5 || $s_employeeId == 8 || $s_employeeId == 137)
									{
							?>
							 <li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-accounts-statements.php">VIEW STATEMENTS</a></li>
							 <li><a href="<?php echo SITE_URL_EMPLOYEES?>/add-accounts-details.php">ADD DEBIT AND CREDIT</a></li>
							 <li><a href="<?php echo SITE_URL_EMPLOYEES?>/cash-cheque-details.php">CASH/CHEQUE DETAILS</a></li>
							 <li><a href="<?php echo SITE_URL_EMPLOYEES?>/cash-cheque-statements.php">CASH/CHEQUE STATEMENTS</a></li>
							 <?php
									}
								}	
							?>
							 
							 <li><a href="<?php echo SITE_URL_EMPLOYEES?>/view-employee-salary.php">MT EMPLOYEES SALARY</a></li>
							 <li><a href="<?php echo SITE_URL_EMPLOYEES?>/employee-salary-sheet.php">PRINT EMPLOYEES SALARY</a></li>
							 <li><a href="<?php echo SITE_URL_EMPLOYEES?>/all-employee-salary-sheet.php">SALARY PAID TO EMPLOYEES</a></li>
						</ul>
					  </li>
					  	   
						</li>
					 </ul>
					<?php
					}
				?>
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