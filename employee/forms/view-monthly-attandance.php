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
	$month			=	date("m");
	$year			=	date("Y");
	$andClause		=	"";
	$andClause1		=	"";
	$andClause2		=	"";
	$employeeName	=	"";
	$departmentId	=   0;
	$display		=	"";
	$display1		=	"none";
	$display2		=	"none";
	$display3		=	"none";
	$table			=	"employee_details";
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

	$form		=	SITE_ROOT_EMPLOYEES."/forms/search-employee-type-month.php";

	$text		=	"";
	$text1		=	"";
	if(isset($_POST['formSubmitted']))
	{
		$departmentId	=	$_POST['departmentId'];
		$month			=	$_POST['month'];
		$year			=	$_POST['year'];
		$employeeType	=	$_POST['employeeType'];
		$underManager	=	$_POST['underManager'];
		$showForm		=	true;
		$redirectLink	=	"month=".$month."&year=".$year."&departmentId=".$departmentId."&employeeType=".$employeeType."&underManager=".$underManager;
		if(isset($_POST['employeeId'])  && empty($departmentId))
		{
			$a_employeeId		=	$_POST['employeeId'];
		}
		if(isset($_POST['mtEmployeeId']) && $departmentId == 1)
		{
			$mtEmployeeId		=	$_POST['mtEmployeeId'];
			if(!empty($mtEmployeeId))
			{
				$a_employeeId	=	$mtEmployeeId;
			}
		}
		if(isset($_POST['revEmployeeId']) && $departmentId == 2)
		{
			$revEmployeeId		=	$_POST['revEmployeeId'];
			if(!empty($revEmployeeId))
			{
				$a_employeeId	=	$revEmployeeId;
			}
		}
		if(isset($_POST['pdfEmployeeId']) && $departmentId == 3)
		{
			$pdfEmployeeId		=	$_POST['pdfEmployeeId'];
			if(!empty($pdfEmployeeId))
			{
				$a_employeeId	=	$pdfEmployeeId;
			}
		}
		if(!empty($a_employeeId))
		{
			if(!in_array("0",$a_employeeId))
			{
				$searchEmployee	=	implode(",",$a_employeeId);
				$redirectLink  .=   "&employeeId=".$searchEmployee;

			}
		}
		
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
			$pos	=	strpos($searchEmployee, ",");
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
			}
		}
	}
	if($departmentId== 1)
	{
		$table		    =	"employee_details INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId";
		$display		=	"none";
		$display1		=	"";
		$display2		=	"none";
		$display3		=	"none";
	}
	elseif($departmentId== 2)
	{
		$table		    =	"employee_details INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId";
		$display		=	"none";
		$display1		=	"none";
		$display2		=	"";
		$display3		=	"none";
	}
	elseif($departmentId== 3)
	{
		$table		    =	"employee_details";
		$display		=	"none";
		$display1		=	"none";
		$display2		=	"none";
		$display3		=	"";
	}

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
?>
<br>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
<?php
	if($showForm)
	{
?>
	<tr>
		<td class="title1">VIEW ATTENDENCE REGISTER ON <?php echo $monthText.",".$year." ".$text." ".$text1;?>  </td>
	</tr>
</table>
<br>
<table width="98%" border="1" cellpadding="0" cellspacing="2" align="center">
<tr>
	<td colspan="33">
		<a href="<?php echo SITE_URL_EMPLOYEES;?>/print-monthly-attendence.php?<?php echo $printFor;?>" class="link_style9">PRINT THIS REPORT IN EXCEL SHEET</a>
	</td>
</tr>
<tr>
	<td width="20%" class="smalltext2" valign="top">Employee Name</td>
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
	<td width="25" valign="top"><font class="smalltext2"><b><?php echo $i;?></b></font><br><font size="1" color="<?php echo $color;?>"><?php echo $dayText;?></font></td>
	<?php
		}
	?>
	<td class="smalltext2" valign="top"><b>Present</b></td>
</tr>
<tr>
	<td colspan="34">
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
	$recsPerPage	          =	20;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause.$andClause1.$andClause2;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	$table;
	$pagingObj->selectColumns = "employee_details.employeeId,firstName,lastName";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/view-monthly-attandance.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$showSummary	=	true;
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();

		$i=$recNo;
		while($row	=   mysql_fetch_assoc($recordSet))
		{
			$i++;
			$lastName		=	$row['lastName'];
			$employeeId		=	$row['employeeId'];
			$firstName		=	$row['firstName'];
			$employeeName	=	$firstName." ".$lastName;
			$employeeName	=	ucwords($employeeName);

			$a_presentDays		=	array();

		?>
<tr>
	<td class="text2"><?php echo $i.")".$employeeName;?></td>
<?php
	$query1			=	"SELECT loginDate FROM employee_attendence WHERE employeeId=$employeeId AND MONTH(loginDate)=$month AND YEAR(loginDate)=$year AND isLogin=1";
	$result1		=	mysql_query($query1);
	if(mysql_num_rows($result1))
	{	
		while($row1		=	mysql_fetch_assoc($result1))
		{
			$loginDate	=	$row1['loginDate'];
			list($year,$month,$day)	=	explode("-",$loginDate);

			$a_presentDays[$day]	=	$day;
		}
	}
	$presentDays	=	0;
	foreach($a_daysInMonth as $key=>$value)
	{
		$halfLeave		=	"";
		$searchLeaveFor	=	$year."-".$month."-".$key;
		
		$onLeave		=	@mysql_result(dbQuery("SELECT onLeave FROM employee_attendence WHERE employeeId=$employeeId AND loginDate='$searchLeaveFor'"),0);
		if(empty($onLeave))
		{
			$onLeave	=	0;
		}
		if($onLeave ==  2)
		{
			$halfLeave	   =	"<font color='#ff0000' size='1'>(HD)</font>";
		}
		if($onLeave	==	1)
		{
			$attandanceText	=	"<font color='#ff0000'><b>L<b></font>";
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
				$attandanceText	=	"<font color='#ff0000'><b>A<b></font>";
				if(in_array($key,$a_presentDays))
				{
					$attandanceText	=	"<font color='#00000'><b>P<b></font>";
					$presentDays	=	$presentDays+1;
				}
				else
				{
					$sundayText		=    date("l",strtotime($year."-".$month."-".$key));
					if($sundayText  ==    "Sunday")
					{
						$attandanceText	=	"<font color='#8C0000'><b>Sun<b></font>";
					}
				}
			}
		}
		echo "<td class='text2'>".$attandanceText.$halfLeave."</td>";
	}
	echo "<td class='smalltext2'><b>$presentDays Days</b></td>";
?>
</tr>
<tr>
	<td colspan="33" height="2"></td>
</tr>
<?php
	}
?>
<tr>
	<td colspan="33">
		<a href="<?php echo SITE_URL_EMPLOYEES;?>/print-monthly-attendence.php?<?php echo $printFor;?>" class="link_style9">PRINT THIS REPORT IN EXCEL SHEET</a>
	</td>
</tr>
<?php
	echo "<tr><td colspan='33'><table width='90%' border='0' ><tr height=20><td align=center><font color='#000000'>";
	$pagingObj->displayPaging($queryString);
	echo "<b>Total Records : " . $totalRecords . "</font></b></td></tr></table></td></tr>";

	}
	}
	else
	{
		echo "<tr><td height='200' class='error' align='center'><b>Please Submit The Above Form !!</b></td></tr>";
	}
	echo "</table>";
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>