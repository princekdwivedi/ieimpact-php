<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES     .   "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/check-admin-login.php");
	include(SITE_ROOT				.   "/classes/pagingclass.php");
	include(SITE_ROOT_EMPLOYEES		.	"/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/set-variables.php");
	$pagingObj						=	new Paging();
	$employeeObj					=	new employee();
	$a_employeeList					=	array();
	if($result						=   $employeeObj->getAllPdfEmployees()){
		while($row					=   mysqli_fetch_assoc($result)){
			$t_employeeId			=   $row['employeeId'];
			$t_firstName			=   stripslashes($row['firstName']);
			$t_lastName				=   stripslashes($row['lastName']);

			$a_employeeList[$t_employeeId] = $t_firstName." ".$t_lastName;
		}
	}

	if(isset($_REQUEST['recNo']))
	{
		$recNo		    =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo			=	0;
	}

	$whereClause		=	"WHERE hasPdfAccess=1 AND isActive=1 AND isForNotLogout=1";
	$orderBy			=	"attendenceId DESC";
	$queryString		=	"";
	$link				=	"?recNo=".$recNo;
	$month				=	"";
	$year				=	"";
	$andClause			=	"";

	if(isset($_GET['employeeId'])){
		$employeeId     =	(int)$_GET['employeeId'];
		if(!empty($employeeId)){
			$queryString .=	"&employeeId=".$employeeId;
			$link        .= "&employeeId=".$employeeId;
			$andClause	 .=	" AND employee_attendence.employeeId=".$employeeId;
		}
	}

	if(isset($_GET['month'])){
		$month            =	$_GET['month'];
		if(!empty($month)){
			$queryString .=	"&month=".$month;
			$link        .= "&month=".$month;
			$andClause	 .=	" AND MONTH(loginDate)=".$month;
		}
	}

	if(isset($_GET['year'])){
		$year             =	$_GET['year'];
		if(!empty($year)){
			$queryString .=	"&year=".$year;
			$link        .= "&year=".$year;
			$andClause	 .=	" AND YEAR(loginDate)=".$year;
		}
	}

	if(isset($_GET['attendanceId'])){
		$attendanceId	  =  (int)$_GET['attendanceId'];
		if(!empty($attendanceId)){
			
			$query	=	"SELECT * FROM employee_attendence WHERE attendenceId=$attendanceId AND isForNotLogout=1";
			$result	=   dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row			=	mysqli_fetch_assoc($result);
				$employeeId		=	$row['employeeId'];
				$loginDate		=	$row['loginDate'];

				dbQuery("UPDATE employee_attendence SET onLeave=0,isLogin=1,isForNotLogout=0 WHERE attendenceId=$attendanceId AND isForNotLogout=1 AND employeeId=$employeeId");

				dbQuery("UPDATE temp_corn_employee_attendance SET onLeave=0,isLogin=1,isForNotLogout=0 WHERE attendenceId=$attendanceId AND isForNotLogout=1 AND employeeId=$employeeId");

				list($leaveY,$leaveM,$leaveD)=	explode("-",$loginDate);

				$nonLeadingZeroMonth		 =	$leaveM;
				if($leaveM < 10 && strlen($leaveM) > 1)
				{
					$nonLeadingZeroMonth	 =	substr($leaveM,1);
				}

				$a_monthDateText			=	array();
				$a_monthDateText[1]			=	"1st";
				$a_monthDateText[2]			=	"2nd";
				$a_monthDateText[3]			=	"3rd";
				$a_monthDateText[4]			=	"4th";
				$a_monthDateText[5]			=	"5th";
				$a_monthDateText[6]			=	"6th";
				$a_monthDateText[7]			=	"7th";
				$a_monthDateText[8]			=	"8th";
				$a_monthDateText[9]			=	"9th";
				$a_monthDateText[10]		=	"10th";
				$a_monthDateText[11]		=	"11th";
				$a_monthDateText[12]		=	"12th";
				$a_monthDateText[13]		=	"13th";
				$a_monthDateText[14]		=	"14th";
				$a_monthDateText[15]		=	"15th";
				$a_monthDateText[16]		=	"16th";
				$a_monthDateText[17]		=	"17th";
				$a_monthDateText[18]		=	"18th";
				$a_monthDateText[19]		=	"19th";
				$a_monthDateText[20]		=	"20th";
				$a_monthDateText[21]		=	"21st";
				$a_monthDateText[22]		=	"22nd";
				$a_monthDateText[23]		=	"23rd";
				$a_monthDateText[24]		=	"24th";
				$a_monthDateText[25]		=	"25th";
				$a_monthDateText[26]		=	"26th";
				$a_monthDateText[27]		=	"27th";
				$a_monthDateText[28]		=	"28th";
				$a_monthDateText[29]		=	"29th";
				$a_monthDateText[30]		=	"30th";
				$a_monthDateText[31]		=	"31st";

				$nonLeadingZeroDay			=	$leaveD;
				if($leaveD < 10 && strlen($leaveD) > 1)
				{
					$nonLeadingZeroDay		=	substr($leaveD,1);
				}
				$column						=	$a_monthDateText[$nonLeadingZeroDay];

				$query						=	"UPDATE track_daily_employee_attendance SET ".$column."=1,totalPresent=totalPresent+1,totalAbsent=totalAbsent-1 WHERE employeeId=$employeeId AND forMonth=$leaveM AND forYear=$leaveY";
				dbQuery($query);
			}
		}

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/revert-logout-attendance.php".$link);
		exit();
	}
	
?>
<script type = "text/javascript">
	function checkValid()
	{
		form1	=  document.searchEmployees;
		if(form1.month.value	==	0 || form1.year.value	==	0){
			alert("Please select month and year.");
			form1.month.focus();
			return false;
		}
	}
	function revertBack(attendanceId,link)
	{
		var confirmation = window.confirm("Are you sure to revert this employee absent?");
		if(confirmation == true)
		{
			window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/revert-logout-attendance.php?attendanceId="+attendanceId+link;
		}
	}
</script>
<form name="searchEmployees" action="" method="GET" onsubmit="return checkValid();">
	<table width="100%" align="center" border="0" cellpadding="2" cellspacing="2">
		<tr>
			<td class="smalltext23" colspan="8">
				<b>PDF EMPLOYEES BECOME ABSENT FOR NON LOGOUT</b>
			</td>
		</tr>
		<tr>
			<td colspan="5" height="5"></td>
		</tr>
		<tr>
			<td width="7%" class="smalltext23">
				<b>Employee</b>
			</td>
			<td width="1%" class="smalltext23">
				<b>:</b>
			</td>
			<td class="smalltext23" width="20%">
				<select name="employeeId">
					<option value="0">All</option>
					<?php
						foreach($a_employeeList as $k=>$v){
							$select		= "";
							if($k		== $employeeId){
								$select	= "selected";
							}

							echo "<option value='$k' $select>$v</option>";
						}
					?>
				</select>
			</td>
			<td width="3%" class="smalltext23">
				<b>For</b>
			</td>
			<td width="1%" class="smalltext23">
				<b>:</b>
			</td>
			<td class="smalltext23" width="12%">
				<select name="month">
					<option value="0">Month</option>
					<?php
						foreach($a_month as $k=>$v){
							$select		= "";
							if($k		== $month){
								$select	= "selected";
							}

							echo "<option value='$k' $select>$v</option>";
						}
					?>
				</select>&nbsp;
				<select name="year">
					<option value="0">Year</option>
					<?php
						$cY   = date('Y');

						for($i=2015;$i<=$cY;$i++){
							$select		= "";
							if($year	== $i){
								$select	= "selected";
							}

							echo "<option value='$i' $select>$i</option>";
						}
					?>
				</select>
			</td>
			<td>
				<input type="image" name="submit" src="<?php echo SITE_URL;?>/images/submit.jpg">
				<input type="hidden" name="formSubmitted" value="1">
			</td>
		</tr>
		<tr>
			<td colspan="5" height="5"></td>
		</tr>		
	</table>
</form>
<?php
	$start					  =	0;
	$recsPerPage	          =	50;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"employee_attendence INNER JOIN employee_details ON employee_attendence.employeeId=employee_details.employeeId";
	$pagingObj->selectColumns = "employee_attendence.*,fullName";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/revert-logout-attendance.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
?>
	<table width='99%' align='center' cellpadding='0' cellspacing='0' border='0'>
		<tr height='25' bgcolor="#373737">
			<td width='5%' class='smalltext12' valign="top">&nbsp;Sr.No</td>
			<td width='25%' class='smalltext12' valign="top">Employee Name</td>
			<td width='20%' class='smalltext12' valign="top">Login Date Time</td>
			<td width='12%' class='smalltext12' valign="top">Is Logout</td>
			<td width='12%' class='smalltext12' valign="top">Status</td>
			<td class='smalltext12' valign="top">Action</td>
		</tr>
<?php
		$i	=	0;
		while($row			=   mysqli_fetch_assoc($recordSet))
		{
			$i++;
			$attendenceId	=	$row['attendenceId'];
			$employeeId		=	$row['employeeId'];
			$fullName		=	stripslashes($row['fullName']);
			$loginTime	    =	showTimeShortFormat($row['loginTime']);
			$loginDate	    =	showDate($row['loginDate']);

			$bgColor		=	"class='rwcolor1'";
			if($i%2==0)
			{
				$bgColor	=	"class='rwcolor2'";
			}
?>
	<tr <?php echo $bgColor;?> height="25">
		<td class="smalltext2" valign="top">
			&nbsp;<?php echo $i.")";?>
		</td>
		<td class="smalltext2" valign="top">
			<?php echo $fullName;?>
		</td>
		<td class="smalltext2" valign="top">
			<?php echo $loginDate." ".$loginTime;?>
		</td>
		<td class="smalltext2" valign="top">
			No
		</td>
		<td class="smalltext2" valign="top">
			Leave
		</td>
		<td>
			<?php
				$link	=	stringReplace("?","&",$link);
			?>
			<a onclick="revertBack(<?php echo $attendenceId;?>,'<?php echo $link;?>')" class="link_style5" style="cursor:pointer;">Revert</a>
		</td>
	</tr>
<?php
		}
		echo "<tr><td height='10'></td></tr><tr><td style='text-align:right' colspan='8'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr>";
?>
	</table>
<?php

	}
	else{
		echo "<br/><br /><br/><br /><br/><br /><center><font class='error'><b>No record found.</b></font></center><br/><br /><br/><br /><br/><br />";
	}

	include(SITE_ROOT_EMPLOYEES		.   "/includes/bottom.php");
?>