<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$formText		=	"View Employees Break Of";
	$employeeName	=	"";
	$displayDate	=	"";
	$displayMonth	=	"none";
	$checked		=	"checked";
	$checked1		=	"";
	$searchOn		=	date("d-m-Y");
	$t_searchOn		=	date("Y-m-d");
	$month			=	date("m");
	$year			=	date("Y");
	$employeeId		=	0;
	$a_employeeId	=	array();
	$type			=	0;
	$manager		=	0;
	$display1		=	"none";
	$display2		=	"none";
	$display3		=	"none";
	$departmentId	=	0;
	$table		    =	"";
	$a_managers		=	$employeeObj->getAllEmployeeManager();
	$showForm		=	false;
	$andClause		=	"";
	$text			=	"";

	$form			=	SITE_ROOT_EMPLOYEES. "/forms/employee-department-manager.php";

	if(isset($_POST['formSubmitted']))
	{
		$searchBy		=	$_POST['searchBy'];
		$type			=	$_POST['type'];
		$manager		=	$_POST['manager'];
		$showForm		=	true;
		$departmentId	=	$_POST['departmentId'];
		list($day,$month,$year)		=	explode("-",$searchOn);
		$t_searchOn	=	$year."-".$month."-".$day;
		if($departmentId== 1)
		{
			$table		    =	"INNER JOIN employee_shift_rates ON employee_breaks.employeeId=employee_shift_rates.employeeId";
			
			$andClause	    =	" AND employee_shift_rates.departmentId=1";
			$text			=	"MT Department";
			$display		=	"none";
			$display1		=	"";
			$display2		=	"none";
			$display3		=	"none";
		}
		elseif($departmentId== 2)
		{
			$table		    =	"INNER JOIN employee_shift_rates ON employee_breaks.employeeId=employee_shift_rates.employeeId";
			
			$andClause	    =	" AND employee_shift_rates.departmentId=2";
			$text			=	"REV Department";
			$display		=	"none";
			$display1		=	"none";
			$display2		=	"";
			$display3		=	"none";
		}
		elseif($departmentId== 3)
		{
			$table		    =	"";
			$andClause	    =	" AND employee_details.hasPdfAccess=1";
			$text			=	"PDF Department";
			$display		=	"none";
			$display1		=	"none";
			$display2		=	"none";
			$display3		=	"";
		}
		if($searchBy		==	1)
		{
			$searchOn		=	$_POST['searchOn'];
			list($day,$month,$year)		=	explode("-",$searchOn);
			$t_searchOn		=	$year."-".$month."-".$day;
			$andClause	   .=	" AND breakDate='$t_searchOn'";
			$text		   .=	" on ".showDate($t_searchOn);
		}
		elseif($searchBy	==	2)
		{
			$month			=	$_POST['month'];
			$year			=	$_POST['year'];
			$andClause	   .=	" AND MONTH(breakDate)=$month AND YEAR(breakDate)=$year";

			$displayDate	=	"none";
			$displayMonth	=	"";
			$checked		=	"";
			$checked1		=	"checked";

			$monthText		=	$a_month[$month];
			$text		   .=	" for".$monthText.",".$year;
		}
		
		if(!empty($type))
		{
			$andClause	   .=	" AND employee_details.employeeType=$type";
			$text		   .=	" employee type ".$a_inetExtEmployee[$type];
		}
		if(!empty($manager))
		{
			$andClause	   .=	" AND employee_details.underManager=$manager";
			$text		   .=	" under manager ".$a_managers[$manager];
		}
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
				$andClause     .=	" AND employee_details.employeeId IN ($searchEmployee)";
				$totalEmloyee	=	count($a_employeeId);
				if($totalEmloyee < 2 && $totalEmloyee > 0)
				{
					foreach($a_employeeId as $key=>$value)
					{
						$employeeName	=	$employeeObj->getEmployeeName($value);
					}
					$text			.=	" for ".$employeeName;
				}
				else
				{
					$text			.=	" for MULTILE EMPLOYEE";
				}
			}
		}
	}
?>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='heading'>VIEW EMPLOYEE'S BREAK TIME</td>
	</tr>
</table>
<?php
	include($form);
	if($showForm)
	{
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td colspan="2" class='title'>VIEW EMPLOYEE BREAK TIME <?php echo $text;?></td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
</table>
<?php
		$query	=	"SELECT employee_breaks.*,firstName,lastName FROM employee_breaks ".$table." INNER JOIN employee_details ON employee_breaks.employeeId=employee_details.employeeId WHERE employee_details.isActive=1".$andClause." ORDER BY breakDate, breakTime DESC";
		$result	=	dbQuery($query);
		if(mysql_num_rows($result))
		{
?>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
<tr>
	<td width="5%" class="smalltext2"><b>Sr. No</b></td>
	<td width="18%" class="smalltext2"><b>Employee Name</b></td>
	<td width="15%" class="smalltext2"><b>Break Date</b></td>
	<td class="smalltext2" width="15%"><b>Break From</b></td>
	<td class="smalltext2" width="15%"><b>Break To</b></td>
	<td class="smalltext2"><b>Break Reason</b></td>
</tr>
<tr>
	<td colspan="7">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<?php
	$i=	0;
	while($row					=	mysql_fetch_assoc($result))
	{
		$i++;
		$employeeId				=	$row['employeeId'];
		$breakDate				=	showDate($row['breakDate']);
		$breakFinsheddate		=	showDate($row['breakFinsheddate']);
		$breakTime				=	date("H:i",strtotime($row['breakTime']));
		$breakFinishedTime		=	date("H:i",strtotime($row['breakFinishedTime']));
		$breakTakingFor			=	stripslashes($row['breakTakingFor']);
		$firstName				=	stripslashes($row['firstName']);
		$lastName				=	stripslashes($row['lastName']);
		$employeeName			=	$firstName." ".$lastName;
?>
<tr>
	<td class="textstyle" valign="top"><b><?php echo $i;?>)</b></td>
	<td class="textstyle" valign="top"><b><?php echo $employeeName;?></b></td>
	<td class="textstyle" valign="top"><b><?php echo $breakDate;?></b></td>
	<td class="textstyle" valign="top"><b><?php echo $breakTime;?></b>Hrs</td>
	<td class="textstyle" valign="top"><b><?php echo $breakFinishedTime;?></b>Hrs</td>
	<td class="textstyle" valign="top"><b><?php echo nl2br($breakTakingFor);?></b></td>
</tr>
<tr>
	<td colspan="7">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<?php
	}
?>
</table>
<?php
		}
		else
		{
			echo "<table><tr><td height='250' class='error' align='center'><b>No Record Found !! </b></td></tr></table>";
		}
	}
	else
	{
		echo "<table><tr><td height='250' class='error' align='center'><b>Please submit the form to view employee's break time !! </b></td></tr></table>";
	}

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>