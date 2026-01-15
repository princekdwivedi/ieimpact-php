<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	$pagingObj		=	new Paging();
	$employeeObj	=	new employee();

	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$attandenceLogintext	=	"VIEW LOGIN DETAILS FOR";
	$month					=	$today_month;
	$year					=	$today_year;
	$currentDay				=	$today_day;
	$currentMonth			=	$today_month;
	$currentYear			=	$today_year;
	$employeeId				=	0;
	$andClause				=	"";
	$andClause1				=	"";
	$employeeName			=	"";
	$departmentId			=   1;
	$display				=	"none";
	$display1				=	"";
	$display2				=	"none";
	$display3				=	"none";
	$table					=	"employee_details";
	$showForm				=	false;
	$employeeName			=	"";
	$searchText				=	"";
	$departmentText			=	"";
	$type					=	0;
	$manager				=	0;
	$a_managers				=	$employeeObj->getAllEmployeeManager();

	$seachingFromAttendence	=	$employeeObj->getSingleQueryResult("SELECT loginDate FROM employee_attendence WHERE attendenceId > '".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID."' AND loginDate <> '0000-00-00' ORDER BY attendenceId LIMIT 1","loginDate");

	$headingText			=	"This page will show records from - ".showDate($seachingFromAttendence);

	$form					=	SITE_ROOT_EMPLOYEES."/forms/month-year-all-employee.php";
	if(isset($_POST['formSubmitted']))
	{
		$month			=	$_POST['month'];
		$year			=	$_POST['year'];
		$monthText		=	$a_month[$month];
		$showForm		=	true;
		$employeeId		=	$_POST['pdfEmployeeId'];
		$departmentText	 =	"(PDF)";
		$display		 =	"none";
		$display1		 =	"none";
		$display2		 =	"none";
		$display3		 =	"";

		if(!empty($employeeId)){

			$employeeName	 =	$employeeObj->getEmployeeName($employeeId);
			$searchText		 =	"SEARCHING LOGIN-LOGOUT RECORDS FOR ".$employeeName.$departmentText." ON ".$monthText.",".$year;
		}
	} 
	?>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class="textstyle3"><b><font color="#ff0000"><?php echo $headingText;?></font></b></td>
	</tr>
</table>
<?php
	include($form);

	$febMonthDays	=	"28";
	
	$divideYear		=	$year%4;

	if($divideYear  == 0)
	{
		$febMonthDays=	"29";
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
<br>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
<?php
	if($showForm && !empty($employeeId))
	{
?>
	<tr>
		<td class="title1" colspan="5"><?php echo $searchText;?>  </td>
	</tr>
	<tr>
		<td colspan="7">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
	<tr>
		<td width="10%" class="smalltext2" valign="top">Date</td>
		<td width="10%" class="smalltext2" valign="top">Day</td>
		<td width="15%" class="smalltext2" valign="top">Login Time</td>
		<td width="10%" class="smalltext2" valign="top">Logout Time</td>
		<td class="smalltext2" valign="top">Leave</td>
	</tr>
	<tr>
		<td colspan="7">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
	<?php
	$employee_in_out_leave =	array();
	$query				   =	"SELECT loginDate,loginTime,logoutTime,onLeave FROM employee_attendence WHERE attendenceId > ".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID." AND employeeId=$employeeId AND MONTH(loginDate) = $month AND YEAR(loginDate)=$year";
	$result 			   =   dbQuery($query);
	if(mysqli_num_rows($result)){
		while($row 			=	mysqli_fetch_assoc($result)){
			$loginDate 		=	$row['loginDate'];
			
			$employee_in_out_leave[$loginDate] = $row['loginTime']."=".$row['logoutTime']."=".$row['onLeave'];
		}

	}

	foreach($a_daysInMonth as $key=>$value)
	{
		if($month > $currentMonth && $year == $currentYear)
		{
			break;
		}
		elseif($key > $currentDay && $month >= $currentMonth && $year == $currentYear)
		{
			break;
		}
		
		$searchLeaveFor	=	$year."-".$month."-".$key;

		$inTime  = "00:00:00";
		$outTime = "00:00:00";
		$onLeave =	0;

		if(array_key_exists($searchLeaveFor,$employee_in_out_leave)){
			list($inTime,$outTime,$onLeave) = explode("=",$employee_in_out_leave[$searchLeaveFor]);
		}

		if(!empty($inTime) && $inTime != "00:00:00")
		{
			$inTime		=	date("H:i",strtotime($inTime));
			$inTime		=	$inTime." Hrs";
		}
		else
		{
			$inTime		=	"Not login";
		}

		if(!empty($outTime) && $outTime != "000:00:00")
		{
			$outTime		=	date("H:i",strtotime($outTime));
			$outTime		=	$outTime." Hrs";
		}
		else
		{
			$outTime		=	"Not logout";
		}

		if(!empty($onLeave))
		{
			if($onLeave == 1)
			{
				$leaveText		=	"Full Day";
			}
			elseif($onLeave == 2)
			{
				$leaveText		=	"Half Day";
			}
			$inTime        =  "Not login";
			$outTime		=	"Not logout";
		}
		else
		{
			$leaveText		=	"<font color='red'>No</font>";
		}

		$dayText		=    date("l",strtotime($year."-".$month."-".$key));

		$dayText		=	substr($dayText,0,3);

		$color			=	"#000000";
		if($dayText		==	"Sun")
		{
			$color		=	"#ff0000";
		}

		echo "<tr><td class='title2' valign='top'>".showDate($searchLeaveFor)."</td>";
		echo "<td class='title2' valign='top'><font color=$color>$dayText</font></td>";
		echo "<td class='title2' valign='top'>$inTime</td>";
		echo "<td class='title2' valign='top'>$outTime</td>";
		echo "<td class='title2' valign='top'>$leaveText</td>";
					
		echo "</tr>";

		echo "<tr><td colspan='7'><hr size='1' size='100%' color='#bebebe'></td></tr>";
	}
}
else
{
	echo "<tr><td colspan='5' style='text-align:center;' class='error' height='200'><b>Please Search An Employee !!</b></td></tr>";
}
echo "</table>";
include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>