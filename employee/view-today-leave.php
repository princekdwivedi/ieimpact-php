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
	$employeeObj				=	new employee();
	$pagingObj			        =   new Paging();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$displayDate	=	"";
	$displayMonth	=	"none";
	$checked		=	"checked";
	$checked1		=	"";
	$searchOn		=	date("d-m-Y");
	$t_searchOn		=	date("Y-m-d");
	$month			=	date("m");
	$year			=	date("Y");
	$text			=	showDate($t_searchOn);
	$andClause		=	" AND loginDate='$t_searchOn'";

	if(isset($_POST['formSubmitted']))
	{
		$searchBy		=	$_POST['searchBy'];
		if($searchBy		==	1)
		{
			$searchOn		=	$_POST['searchOn'];
			list($day,$month,$year)		=	explode("-",$searchOn);
			$t_searchOn		=	$year."-".$month."-".$day;
			$andClause		=	" AND loginDate='$t_searchOn'";
			$text			=	showDate($t_searchOn);
		}
		else
		{
			$month			=	$_POST['month'];
			$year			=	$_POST['year'];

			$andClause		=	" AND MONTH(loginDate)=".$month." AND YEAR(loginDate)=".$year;
			$monthText		=	$a_month[$month];
			$text		    =	$monthText.",".$year;

			$displayDate	=	"none";
			$displayMonth	=	"";
			$checked		=	"";
			$checked1		=	"checked";
		}
	}
?>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/validate.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>

<script type="text/javascript">
function showSearch(flag)
{
	if(flag == 1)
	{
		document.getElementById('displayDate').style.display = 'inline';
		document.getElementById('displayMonth').style.display = 'none';
	}
	else
	{
		document.getElementById('displayDate').style.display = 'none';
		document.getElementById('displayMonth').style.display = 'inline';
	}
}
</script>

<form name="searchForm" action=""  method="POST" onsubmit="return search();">
	<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
		<tr>
			<td width="33%" class="smalltext2" valign="top"><b>View Employees Leave BY <input type="radio" name="searchBy" value="1" <?php echo $checked;?> onclick="return showSearch(1)">Date Or By <input type="radio" name="searchBy" value="2" <?php echo $checked1;?> onclick="return showSearch(2)">Month</b></td>
			<td width="2%" class="smalltext2" valign="top"><b>:</b></td>
			<td width="20%" valign="top" class="title1">
				<div  id="displayDate" style="display:<?php echo $displayDate;?>">
					DATE&nbsp;&nbsp;
					<input type="text" name="searchOn" value="<?php echo $searchOn;?>" id="atOn" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('atOn','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
				</div>
				<div  id="displayMonth" style="display:<?php echo $displayMonth;?>">
					MONTH
					<select name="month">
						<?php
							foreach($a_month as $key=>$value)
							{
								$select	  =	"";
								if($month == $key)
								{
									$select	  =	"selected";
								}

								echo "<option value='$key' $select>$value</option>";
							}
						?>
					</select>&nbsp;&nbsp;
					YEAR
					<select name="year">
						<?php
							$sYear	=	"2010";
							$eYear	=	date("Y")+1;
							for($i=$sYear;$i<=$eYear;$i++)
							{
								$select			=	"";
								if($year  == $i)
								{
									$select		=	"selected";
								}
								echo "<option value='$i' $select>$i</option>";
							}
						?>
					</select>
				</div>
			</td>
			<td valign="top">
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
		<tr>
			<td colspan="6" height="5"></td>
		</tr>
	</table>
</form>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td colspan="2" class='title'>VIEW EMPLOYEE LEAVE ON <?php echo $text;?></td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
</table>
<?php
	$query	=	"SELECT employee_attendence.*,firstName,lastName FROM employee_attendence INNER JOIN employee_details ON employee_attendence.employeeId=employee_details.employeeId WHERE attendenceId > ".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID." AND employee_attendence.onLeave <> 0 AND hasPdfAccess=1".$andClause." GROUP BY employee_details.employeeId ORDER BY loginDate DESC";
	$result	=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
?>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td width="7%" class="smalltext2"><b>SR NO</b></td>
		<td width="20%" class="smalltext2"><b>EMPLOYEE NAME</b></td>
		<td width="15%" class="smalltext2"><b>LEAVE TYPE</b></td>
		<td class="smalltext2"><b>LEAVE ON</b></td>
	</tr>
	<tr>
		<td colspan="4">
			<hr size="1" width="100%" color="#bebebe">
		</td>
	</tr>
	<?php
		$i	=	0;
		while($row	=	mysqli_fetch_assoc($result))
		{
			$i++;
			$employeeId			=	$row['employeeId'];
			$loginDate			=	showDate($row['loginDate']);
			$onLeave			=	$row['onLeave'];
			$firstName			=	$row['firstName'];
			$lastName			=	$row['lastName'];
			
			$leaveText			=	"FULL DAY";

			if($onLeave			==	2)
			{
				$leaveText		=	"HALF DAY";
			}
			$employeeName		=	$firstName." ".$lastName;
			$employeeName		=	ucwords($employeeName);
	?>
	<tr>
		<td class="smalltext2"><b><?php echo $i;?>)</b></td>
		<td class="smalltext2"><b><?php echo $employeeName;?></b></td>
		<td class="error"><b><?php echo $leaveText;?></b></td>
		<td class="smalltext2"><b><?php echo $loginDate;?></b></td>
	</tr>
	<tr>
		<td colspan="4">
			<hr size="1" width="100%" color="#bebebe">
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
		echo "<br><br><br><center><font class='error'><b>NO LEAVE AVAILABLE !!</b></font></center><br><br><br><br><br><br><br>";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>