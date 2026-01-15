<?php
	ob_start();
	session_start();
	//ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES .  "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES .  "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES .  "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/common-array.php");
	include(SITE_ROOT			.  "/classes/pagingclass.php");
	$pagingObj					=	new Paging();
	$employeeObj				=	new employee();

	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$month			=	date("m");
	$year			=	date("Y");
	$andClause		=	"";
	$andClause1		=	"";
	$andClause2		=	"";
	$employeeName	=	"";
	$departmentId	=   1;
	$display		=	"none";
	$display1		=	"";
	$display2		=	"none";
	$display3		=	"none";
	$table			=	"employee_details INNER JOIN track_daily_employee_attendance ON employee_details.employeeId=track_daily_employee_attendance.employeeId";
	$currentDay		=	$today_day;
	$currentMonth	=	$today_month;
	$currentYear	=	$today_year;
	$printFor		=	"";
	$printFor1		=	"";
	$queryString	=	"";
	$queryString1	=	"";
	$whereClause	=	"WHERE isActive=1";
	$orderBy		=	"firstName";
	$showForm		=	false;
	$employeeType	=	0;
	$underManager	=	0;
	$employeeId		=	0;
	$a_employeeId	=	array();
	$formText		=	"ATTENDENCE";
	$a_managers		=	$employeeObj->getAllEmployeeManager();
	
	$headingText	=	"This page will show records from - Jan,2014";

	$form			=	SITE_ROOT_EMPLOYEES."/forms/search-employee-monthly-attendance.php";
?>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class="textstyle3"><b><font color="#ff0000"><?php echo $headingText;?></font></b></td>
	</tr>
</table>
<?php

	list($currentY,$currentM,$currentD)	=	explode("-",$nowDateIndia);
	$text		=	"";
	$text1		=	"";
	if(isset($_POST['formSubmitted']))
	{
			
		$departmentId	=	$_POST['departmentId'];
		$month			=	$_POST['month'];
		$year			=	$_POST['year'];
		$employeeType	=	$_POST['employeeType'];
		$underManager	=	$_POST['underManager'];
		if(isset($_POST['employeeId'])){
			$employeeId	=	implode(",",$_POST['employeeId']);
		}
		else{
			$employeeId	=	0;
		}
		$showForm		=	true;
		$redirectLink	=	"month=".$month."&year=".$year."&departmentId=".$departmentId."&employeeType=".$employeeType."&underManager=".$underManager."&employeeId=".$employeeId;
		
		
		
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/view-monthly-attandance.php?".$redirectLink);
		exit();

	}
	if(isset($_REQUEST['departmentId']))
	{
		$departmentId	=	$_REQUEST['departmentId'];
	}
	if(isset($_REQUEST['month']))
	{
		$month			=	$_REQUEST['month'];
		$showForm		=	true;
	}
	if(isset($_REQUEST['year']))
	{
		$year			=	$_REQUEST['year'];
	}
	if(isset($_REQUEST['employeeType']))
	{
		$employeeType	=	$_REQUEST['employeeType'];
		if(!empty($employeeType))
		{
			$andClause2	   .=	" AND employee_details.employeeType=$employeeType";
			$text1		   .=	" for ".$a_inetExtEmployee[$employeeType]." employees";
			$queryString1  .=   "&employeeType=".$employeeType;
			$printFor1     .=   "&employeeType=".$employeeType;
		}
	}
	if(isset($_REQUEST['underManager']))
	{
		$underManager	=	$_REQUEST['underManager'];
		if(!empty($underManager))
		{
			$andClause2	   .=	" AND employee_details.underManager=$underManager";
			$text1		   .=	" under manager ".$a_managers[$underManager];
			$queryString1  .=   "&underManager=".$underManager;
			$printFor1     .=   "&underManager=".$underManager;
		}
	}
	if(isset($_GET['employeeId']))
	{
		$searchEmployee		=	$_GET['employeeId'];
		if(!empty($searchEmployee))
		{
			/*$pos	=	strpos($searchEmployee, ",");
			if($pos == true)
			{
				$andClause2    .=	" AND employee_details.employeeId IN ($searchEmployee)";
				$queryString1  .=   "&employeeId=".$searchEmployee;
				$text1		   .=	" multiple employees";
				$printFor1     .=   "&employeeId=".$searchEmployee;
				$a_employeeId	=	explode(",",$searchEmployee);
			}
			else
			{
				$andClause2    .=	" AND employee_details.employeeId = $searchEmployee";
				$queryString1  .=   "&employeeId=".$a_employeeId;
				$employeeName	=	$employeeObj->getEmployeeName($searchEmployee);
				$text1		   .=	" for employee ".$employeeName;
				$printFor1     .=   "&employeeId=".$searchEmployee;
				$a_employeeId[]	=	$searchEmployee;
			}*/
			$andClause2    .=	" AND employee_details.employeeId IN ($searchEmployee)";
			$queryString1  .=   "&employeeId=".$searchEmployee;
			$text1		   .=	" multiple employees";
			$printFor1     .=   "&employeeId=".$searchEmployee;
			$a_employeeId	=	explode(",",$searchEmployee);
		}
	}
	if($departmentId== 1)
	{
		$table		    =	"employee_details INNER JOIN track_daily_employee_attendance ON employee_details.employeeId=track_daily_employee_attendance.employeeId INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId";
		$display		=	"none";
		$display1		=	"";
		$display2		=	"none";
		$display3		=	"none";
	}
	elseif($departmentId== 2)
	{
		$table		    =	"employee_details INNER JOIN track_daily_employee_attendance ON employee_details.employeeId=track_daily_employee_attendance.employeeId INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId";
		$display		=	"none";
		$display1		=	"none";
		$display2		=	"";
		$display3		=	"none";
	}
	elseif($departmentId== 3)
	{
		$table		    =	"employee_details INNER JOIN track_daily_employee_attendance ON employee_details.employeeId=track_daily_employee_attendance.employeeId";
		$display		=	"none";
		$display1		=	"none";
		$display2		=	"none";
		$display3		=	"";
		$andClause1		=	" AND employee_details.hasPdfAccess=1";
	}


	$a_leaveNotLogout	=	array();
	$query				=	"SELECT employeeId,loginDate,isForNotLogout,isForLateAttendance FROM employee_attendence WHERE (isForNotLogout=1 OR isForLateAttendance=1) AND MONTH(loginDate)=$month AND YEAR(loginDate)=$year";
	$result				=	dbQuery($query);
	if(mysqli_num_rows($result)){
		while($row					=	mysqli_fetch_assoc($result)){
			$t_employeeId			=	$row['employeeId'];
			$isForNotLogout			=	$row['isForNotLogout'];
			$isForLateAttendance	=	$row['isForLateAttendance'];
			$t_loginDate			=	$row['loginDate'];
			list($y,$m,$d)			=	explode("-",$t_loginDate);
			if($isForNotLogout      == 1){
				$a_leaveNotLogout[$t_employeeId][$d] = 1;
			}
			elseif($isForLateAttendance == 1){
				$a_leaveNotLogout[$t_employeeId][$d] = 2;
			}
		}
	}

	//pr($a_leaveNotLogout);


	$queryString		=	"&departmentId=".$departmentId."&month=".$month."&year=".$year.$queryString1;

	$monthText			=	$a_month[$month];
	$printFor			=	"month=$month&year=$year&departmentId=$departmentId".$printFor1;
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

	$nonLeadingZeroMonth		=	$month;
	if($month < 10 && strlen($month) > 1)
	{
		$nonLeadingZeroMonth	=	substr($month,1);
	}

	$andClause				=	" AND track_daily_employee_attendance.forMonth=$nonLeadingZeroMonth AND track_daily_employee_attendance.forYear=$year";


?>
<script type="text/javascript">
	function updateEmpAttendance(employeeId,date)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/update-employee-attendance.php?employeeId="+employeeId+"&date="+date;
		prop = "toolbar=no,scrollbars=yes,width=600,height=400,top=100,left=100";
		window.open(path,'',prop);
	}
</script>
<br>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
<?php
	if($showForm)
	{
?>
	<tr>
		<td class="smallteext25" width="50%"><b>VIEW ATTENDENCE REGISTER ON <?php echo $monthText.",".$year." ".$text." ".$text1;?> </b></td>
	</tr>
	<tr>
		<td class="smalltext2">
			(<font color='#ff0000'><b>A<b></font>=Absent, <font color='#00000'><b>P<b></font>=Present, <font color='#FF7979'>HD</font>=Half Day, <font color='#FF9393'><b>L<b></font>=Leave, <font color='#E10000'><b>L<b></font>= Leave (Attendance Not Logout), <font color='#FF0000'><b>L<b></font>= Leave (For Late Attendance), <font color='#008040'><b>H<b></font>=Holiday, <font color='#8C0000'><b>S<b></font>=Sunday)
		</td>	
	</tr>
</table>
<br>
<script type="text/javascript">
	
	function openPrintExcelWindow(printLink)
	{
		path = "<?php echo SITE_URL_EMPLOYEES;?>/print-pdf-monthly-attendence.php?"+printLink;
		prop = "toolbar=no,scrollbars=yes,width=400,height=100,top=200,left=300";
		window.open(path,'',prop);
	}
</script>
<table width="100%" border="1" cellpadding="0" cellspacing="1" align="center">
<!--<tr>
	<td colspan="34">
		<?php
			if($s_employeeId == 3){
		?>
			
			<a onclick="openPrintExcelWindow('<?php echo $printFor?>')" class='link_style9' style="cursor:pointer;">PRINT THIS PAGE</a>
		<?php
			} 
		  else{
		?>

			<a onclick="openPrintExcelWindow('<?php echo $printFor?>')" class='link_style9' style="cursor:pointer;">PRINT THIS PAGE</a>
		<?php
			}
		  
		?>
	</td>
</tr>-->
<tr>
	<td width="15%" class="smalltext2" valign="top">Employee Name</td>
	<?php
		for($i=1;$i<=$endMonthDate;$i++)
		{
			$dayText		=    date("l",strtotime($year."-".$month."-".$i));

			$dayText		=	substr($dayText,0,3);

			$color			=	"#000000";
			if($dayText		==	"Sun")
			{
				$color		=	"#ff0000";
			}
	?>
	<td width="33" valign="top"><font class="smalltext2"><?php echo $i;?></font><br><font size="1" color="<?php echo $color;?>"><?php echo $dayText;?></font></td>
	<?php
		}
	?>
	<td class="smalltext2" valign="top">Present</td>
	<td class="smalltext2" valign="top">Leave</td>
	<td class="smalltext2" valign="top">Half</td>
	<td class="smalltext2" valign="top">Overtime</td>
</tr>
<tr>
	<td colspan="38">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<?php
		

	if(isset($_REQUEST['recNo']))
	{
		$recNo		    =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo	=	0;
	}
	
	$start					  =	0;
	$recsPerPage	          =	100;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause.$andClause1.$andClause2;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	$table;
	$pagingObj->selectColumns = "track_daily_employee_attendance.*";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/view-monthly-attandance.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$showSummary	=	true;
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();

		$i=$recNo;
		while($row			=   mysqli_fetch_assoc($recordSet))
		{
			$i++;
			$employeeName	=	stripslashes($row['employeeName']);
			$employeeId		=	$row['employeeId'];
			$presentDays	=	$row['totalPresent'];
			$totalAbsent	=	$row['totalAbsent'];
			$totalHalfDays	=	$row['totalHalfDays'];
			$totalOvertime	=	$row['totalOvertime'];
			$totalDaysInMonth	=	$row['totalDaysInMonth'];
			$showForYear	=	$row['forYear'];
			$showForMonth	=	$row['forMonth'];

			$a_notLogOut	=	array();

			if(array_key_exists($employeeId,$a_leaveNotLogout)){
				
				$a_notLogOut=	$a_leaveNotLogout[$employeeId];
			}

			$bgColor		=	"";
			if(isset($_SESSION['showChangedEmployeeColore']) && $_SESSION['showChangedEmployeeColore'] == $employeeId && $totalRecords > 1)
			{
				$bgColor	=	"bgcolor='#00A3F0';";

				unset($_SESSION['showChangedEmployeeColore']);
			}

	?>

<tr height="25" <?php echo $bgColor;?>>
	<td class="smalltetx1"><?php echo getSubstring($employeeName,20);?></td>
<?php
		foreach($a_monthDateText as $kk1=>$vv1)
		{
			if($kk1 > $totalDaysInMonth)
			{
				break;
			}

			$checkDate		=	$kk1;
			if($kk1 < 10){
				$checkDate	=	"0".$kk1;
			}

			$value			=	$row[$vv1];
			$attText		=	$a_attendanceMarked[$value];

			list($fullAtttext,$abbAttText)	=	explode("|",$attText);

			$attandanceText		=	"<font color='#ff0000'><b>$abbAttText<b></font>";
			if($value			==	1)
			{
				$attandanceText	=	"<font color='#00000'><b>P<b></font>";
			}
			elseif($value		==	2)
			{
				$attandanceText	=	"<font color='#FF7979'>HD</font>";
			}
			elseif($value		==	3)
			{
				$attandanceText	=	"<font color='#FF9393'><b>L<b></font>";
				if(!empty($a_notLogOut) && array_key_exists($checkDate,$a_notLogOut)){
					
					$leaveTypeValue	=	$a_notLogOut[$checkDate];

					if($leaveTypeValue == 1){
					
						$attandanceText	=	"<font color='#E10000'><b>L<b></font>";
					}
					else{
						$attandanceText	=	"<font color='#FF0000'><b>L<b></font>";
					}
				}

				$t_checkDate		=	$checkDate;
				$t_showForMonth		=	$showForMonth;
				if(strlen($showForMonth) < 2){
					$t_showForMonth	=	"0".$showForMonth;
				}
				if(strlen($checkDate) < 2){
					$t_checkDate	=	"0".$checkDate;
				}
				$checkdate		=	$showForYear."-".$t_showForMonth."-".$t_checkDate;
				
			}
			elseif($value		==	4)
			{
				$attandanceText	=	"<font color='#008040'><b>H<b></font>";
			}
			elseif($value		==	5)
			{
				$attandanceText	=	"<font color='#8C0000'><b>S<b></font>";
			}

			if($kk1 > $currentD && $showForYear == $currentY && $showForMonth ==  $currentM && ($value != 2 && $value != 3))
			{
				$attandanceText	=	"-";
			}
			else
			{
				if($s_employeeId == 3 || $s_employeeId == 449 || $s_employeeId == 587 || $s_employeeId == 137 || $s_employeeId == 340 || $s_employeeId == 5)
				{
					$updateDay		=	$kk1;
					if($kk1	<	10)
					{
						$updateDay	=	"0".$kk1;
					}
					$updateMonth	=	$showForMonth;
					if($showForMonth	<	10)
					{
						$updateMonth=	"0".$showForMonth;
					}
					
					$updateDate		=	$showForYear."-".$updateMonth."-".$updateDay;

					$attandanceText		=	"<a style='cursor:pointer;'  onClick=\"updateEmpAttendance($employeeId,'$updateDate');\" title='Update $employeeName attendance on ".showDate($updateDate)." '>$attandanceText</a>";
				}
			}
	?>
	<td class='text2'><?php echo $attandanceText;?></td>
	<?php

		}
	?>	
		<td class='smalltext2'><?php echo $presentDays;?></td>
		<td class='smalltext2'><?php echo $totalAbsent;?></td>
		<td class='smalltext2'><?php echo $totalHalfDays;?></td>
		<td class='smalltext2'><?php echo getHours($totalOvertime);?> Hrs</td>
</tr>
<tr>
	<td colspan="35" height="2"></td>
</tr>
<?php
		}
		echo "<tr><td colspan='35'><table width='90%' border='0' ><tr height=20><td align=center><font color='#000000'>";
		$pagingObj->displayPaging($queryString);
		echo "<b>Total Records : " . $totalRecords . "</font></b></td></tr></table></td></tr>";

		}
	}
	else
	{
		echo "<tr><td height='200' class='error' style='text-align:center;'><b>Please Submit The Above Form.</b></td></tr>";
	}
	echo "</table>";
	
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>