<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	."/includes/common-array.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");
	$employeeObj				=	new employee();
	$validator					=	new validate();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	$form						=	SITE_ROOT_EMPLOYEES  . "/forms/month-year.php";
	$month						=	date("m");
	$year						=	date("Y");
	$text						=	"Attendance for ".$a_month[$month].",".$year;
	$isDisplayOvertime			=	false;
	$currentDay					=	$today_day;
	$currentMonth				=	$today_month;
	$currentYear				=	$today_year;
	$a_presentDays				=	array();
	$presentDays				=	0;

	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		$text					=	"Attendance for ".$a_month[$month].",".$year;
	}
	if(!empty($s_hasPdfAccess))
	{
		$isDisplayOvertime		=	true;
	}

	$febMonthDays				=	"28";
	
	$divideYear					=	$year%4;

	if($divideYear				== 0)
	{
		$febMonthDays			=	"29";
	}

	$a_monthDays	=	array("01"=>"31","02"=>$febMonthDays,"03"=>"31","04"=>"30","05"=>"31","06"=>"30","07"=>"31","08"=>"31","09"=>"30","10"=>"31","11"=>"30","12"=>"31");

	$endMonthDate	=	$a_monthDays[$month];

	$a_daysInMonth	=	array();
	for($i=1;$i<=$endMonthDate;$i++)
	{
		$d	=	$i;
		if($i < 10)
		{
			$d	=	"0".$i;
		}

		$a_daysInMonth[$d]	=	$i;
	}


?>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td class='title'>VIEW ATTENDANCE DETAILS</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
</table>
<?php
	include($form);
?>
<br>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td class='heading' colspan="35">&nbsp;<?php echo $text;?></td>
	</tr>
	<tr>
		<td colspan="10">
			<hr size="1" width="100%" color="#4d4d4d">
		</td>
	</tr>
	<tr>
		<td width="20%" class="heading1">Date</td>
		<td width="25%" class="heading1">Attendance Status</td>
		<td width="15%" class="heading1">Login Time</td>
		<td width="15%" class="heading1">Logout Time</td>
		<td class="heading1">
			<?php
				if($isDisplayOvertime == true)
				{
					echo "<b>Overtime</b>";
				}
				else
				{
					echo "&nbsp;";
				}
			?>
		</td>
	</tr>
	<tr>
		<td colspan="10">
			<hr size="1" width="100%" color="#4d4d4d">
		</td>
	</tr>
	<?php
			$query1			=	"SELECT loginDate,onLeave,isMarkedAbsent,loginTime,logoutTime,overtimeHours FROM employee_attendence WHERE employeeId=$s_employeeId AND MONTH(loginDate)=$month AND YEAR(loginDate)=$year AND isLogin=1";
			$result1		=	dbQuery($query1);
			if(mysqli_num_rows($result1))
			{	
				while($row1		=	mysqli_fetch_assoc($result1))
				{
					$loginDate	    =	$row1['loginDate'];
					$onLeave	    =	$row1['onLeave'];
					$isMarkedAbsent	=	$row1['isMarkedAbsent'];
					$loginTime	    =	$row1['loginTime'];
					$logoutTime	    =	$row1['logoutTime'];
					$overtimeAdded	=   $row1['overtimeHours'];

					list($year,$month,$day)	=	explode("-",$loginDate);

					$a_presentDays[$day]	=	$day."|".$onLeave."|".$isMarkedAbsent."|".$loginTime."|".$logoutTime."|".$overtimeAdded;
				}
			}
			foreach($a_daysInMonth as $key=>$value)
			{
				$halfLeave		=	"";
				$searchLeaveFor	=	$year."-".$month."-".$key;
				$loginTime		=	"";
				$logoutTime		=	"";
				$overTime		=	"";

				if(array_key_exists($key,$a_presentDays)){
					$leavePresent        = $a_presentDays[$key];
					$leavePresent        = explode("|",$leavePresent);
					$onLeave             = $leavePresent[1];
					$isAbesntMarked      = $leavePresent[2];
					$loginTime           = $leavePresent[3];
					$logoutTime          = $leavePresent[4];
					$overtimeAdded       = $leavePresent[5];
				}
				else{
					$onLeave             = 0;
					$isAbesntMarked      = 0;
					$loginTime           = "";
					$logoutTime          = "";
					$overtimeAdded       = 0;
				}				
				
				if(empty($onLeave))
				{
					$onLeave	=	0;
				}
				if(empty($isAbesntMarked))
				{
					$isAbesntMarked	=	0;
				}
				if($onLeave ==  2)
				{
					$halfLeave	   =	"<font color='#ff0000' size='1'>Half Day</font>";
				}
				if($onLeave	==	1)
				{
					if($isAbesntMarked == 1){
						$attandanceText	=	"<font color='#ff0000'><b>Absent<b></font>";
					}
					else{
						$attandanceText	=	"<font color='#ff0000'><b>Leave<b></font>";
					}
				}
				else
				{
					if($currentYear < $year)
					{
						$attandanceText	=	"<font color='#000000'><b>-<b></font>";
					}
					elseif($currentMonth < $month && $currentYear == $year)
					{
						$attandanceText	=	"<font color='#000000'><b>-<b></font>";
					}
					elseif($currentDay < $key && $currentMonth == $month && $currentYear == $year)
					{
						
						$attandanceText	=	"<font color='#000000'><b>-<b></font>";
					}
					else
					{
						$attandanceText	=	"<font color='#ff0000'><b>Absent<b></font>";
						if(array_key_exists($key,$a_presentDays))
						{
							$attandanceText	=	"<font color='#00000'><b>Present<b></font>";
							$presentDays	=	$presentDays+1;
						
							$loginTime		=	date("H:i",strtotime($loginTime));
							$loginTime		=	"Log In At - ".$loginTime." Hrs";
							
							if($logoutTime	!=	"00:00:00")
							{
								$logoutTime	=	date("H:i",strtotime($logoutTime));
								$logoutTime	=	"Log Out At - ".$logoutTime." Hrs";
							}
							else
							{
								$logoutTime		=	"Didnot Log Out";
							}
							
						
							if(empty($overtimeAdded))
							{
								$overTime			=	"";
							}
							else
							{
								$overTime			=	"<font color='#ff0000'>".getHours($overtimeAdded)." Hrs</font>";
							}
							

						}
						else
						{
							$sundayText		=    date("l",strtotime($year."-".$month."-".$key));
							if($sundayText  ==    "Sunday")
							{
								$attandanceText	=	"<font color='#8C0000'><b>Sunday<b></font>";
							}
						}
					}
					
				}
				?>
				<tr>
					<td class="smalltext2">
						<?php echo showDate($searchLeaveFor);?>
					</td>
					<td class="smalltext2">
						<?php echo $attandanceText;?>
					</td>
					<td class="smalltext2">
						<?php echo $loginTime;?>
					</td>
					<td class="smalltext2">
						<?php echo $logoutTime;?>
					</td>
					<td class="smalltext2">
						<?php
							if($isDisplayOvertime == true)
							{
								echo $overTime;
							}
							else
							{
								echo "&nbsp;";
							}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="10">
						<hr size="1" width="100%" color="#4d4d4d">
					</td>
				</tr>
				<?php
			}
		?>
		<tr>
			<td class="smalltext2">
				<b>Total Present In This Month : </b>
			</td>
			<td class="title">
				<b><?php echo $presentDays;?> Day/s</b>
			</td>
		</tr>
		<?php
			if($isDisplayOvertime == true)
			{
		?>
		<tr>
			<td height="10">&nbsp;</td>
		</tr>
		<tr>
			<td class="smalltext2">
				<b>Total Overtime In This Month : </b>
			</td>
			<td class="title">
				<b>
					<?php 
						$totalOvertimeAdded				=	$employeeObj->getSingleQueryResult("SELECT totalOvertimeAdded FROM employee_attendence WHERE employeeId=$s_employeeId AND MONTH(loginDate)='$month' AND YEAR(loginDate)='$year' AND totalOvertimeAdded <> 0 ORDER BY attendenceId DESC LIMIT 1","totalOvertimeAdded");


						if(empty($totalOvertimeAdded))
						{
							$totalOvertimeAdded			=	"00:00 Hrs";
						}
						else
						{
							$totalOvertimeAdded			=	"<font color='#ff0000'>".getHours($totalOvertimeAdded)."</font>";
						}
						echo $totalOvertimeAdded;
					?> 
				</b>
			</td>
		</tr>
		<?php
			}
		?>
	</table>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>