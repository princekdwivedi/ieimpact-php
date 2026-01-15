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
	$departmentId			=   0;
	$display				=	"";
	$display1				=	"none";
	$display2				=	"none";
	$display3				=	"none";
	$table					=	"employee_details";
	$showForm				=	false;
	$employeeName			=	"";
	$searchText				=	"";
	$departmentText			=	"";

	$form		=	SITE_ROOT_EMPLOYEES."/forms/month-year-all-employee.php";
	if(isset($_POST['formSubmitted']))
	{
		$departmentId	=	$_POST['departmentId'];
		$month			=	$_POST['month'];
		$year			=	$_POST['year'];
		$monthText		=	$a_month[$month];
		$showForm		=	true;
		if(empty($departmentId))
		{
			$employeeId		 =	$_POST['employeeId'];
			$departmentText	 =	"";
			
		}
		elseif($departmentId== 1)
		{
			$employeeId		 =	$_POST['mtEmployeeId'];
			$departmentText	 =	"(MT)";
			$display		 =	"none";
			$display1		 =	"";
			$display2		 =	"none";
			$display3		 =	"none";
		}
		elseif($departmentId== 2)
		{
			$employeeId		 =	$_POST['revEmployeeId'];
			$departmentText	 =	"(REV)";
			$display		 =	"none";
			$display1		 =	"none";
			$display2		 =	"";
			$display3		 =	"none";
		}
		elseif($departmentId== 3)
		{
			$employeeId		 =	$_POST['pdfEmployeeId'];
			$departmentText	 =	"(PDF)";
			$display		 =	"none";
			$display1		 =	"none";
			$display2		 =	"none";
			$display3		 =	"";
		}
		if(!empty($employeeId))
		{
			$employeeName	 =	$employeeObj->getEmployeeName($employeeId);
			$searchText		 =	"SEARCHING LOGIN-LOGOUT RECORDS FOR ".$employeeName.$departmentText." ON ".$monthText.",".$year;
		}
	}
	
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
		<td colspan="5">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
	<tr>
		<td width="20%" class="smalltext2" valign="top">Date</td>
		<td width="10%" class="smalltext2" valign="top">Day</td>
		<td width="25%" class="smalltext2" valign="top">Login Time</td>
		<td width="20%" class="smalltext2" valign="top">Logout Time</td>
		<td class="smalltext2" valign="top">Leave</td>
	</tr>
	<tr>
		<td colspan="5">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
	<?php
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
		$inTime			=	@mysql_result(dbQuery("SELECT loginTime FROM employee_attendence WHERE attendenceId > ".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID." AND employeeId=$employeeId AND isLogin=1 AND loginDate='$searchLeaveFor'"),0);

		$outTime		=	@mysql_result(dbQuery("SELECT logoutTime FROM employee_attendence WHERE attendenceId > ".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID." AND employeeId=$employeeId AND isLogout=1 AND loginDate='$searchLeaveFor'"),0);

		$onLeave		=	0;
		$onLeave		=	@mysql_result(dbQuery("SELECT onLeave FROM employee_attendence WHERE attendenceId > ".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID." AND employeeId=$employeeId AND onLeave <> 0 AND loginDate='$searchLeaveFor'"),0);

		if(!empty($inTime) && $inTime != "0000-00-00")
		{
			$inTime		=	date("H:i",strtotime($inTime));
			$inTime		=	$inTime." Hrs";
		}
		else
		{
			$inTime		=	"Not login";
		}

		if(!empty($outTime) && $outTime != "0000-00-00")
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

		echo "<tr><td class='title2'>".showDate($searchLeaveFor)."</td>";
		echo "<td class='title2'><font color=$color>$dayText</font></td>";
		echo "<td class='title2'>$inTime</td>";
		echo "<td class='title2'>$outTime</td>";
		echo "<td class='title2'>$leaveText</td></tr>";

		echo "<tr><td colspan='5'><hr size='1' size='100%' color='#bebebe'></td></tr>";
	}
}
else
{
	echo "<tr><td colspan='5' align='center' class='error' height='200'><b>Please Search An Employee !!</b></td></tr>";
}
echo "</table>";
include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>