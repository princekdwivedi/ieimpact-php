<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	//ini_set('display_errors', 1);
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	include(SITE_ROOT			. "/includes/send-mail.php");
	$pagingObj					=	new Paging();
	$employeeObj				=	new employee();
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
	$andClause1		=	"";
	$whereClause	=	" WHERE isActive=1 AND hasPdfAccess=1";

	if(isset($_POST['formSubmitted']))
	{
		$searchBy			=	$_POST['searchBy'];
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


	$a_addedLoginLeave		=	array();
	if($t_searchOn < $nowDateIndia && $searchBy	==	1){
		$query				=	"SELECT employee_attendence.employeeId FROM employee_attendence INNER JOIN employee_details ON employee_attendence.employeeId=employee_details.employeeId".$whereClause.$andClause." GROUP BY employeeId ORDER BY loginDate DESC";
		$result				=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			while($row					=	mysqli_fetch_assoc($result)){
				$a_addedLoginLeave[]	=	$row['employeeId'];
			}
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
		<td width="28%" class="smalltext23" valign="top">View Employees Leave BY <input type="radio" name="searchBy" value="1" <?php echo $checked;?> onclick="return showSearch(1)">Date Or By <input type="radio" name="searchBy" value="2" <?php echo $checked1;?> onclick="return showSearch(2)">Month</td>
		<td width="2%" class="smalltext23" valign="top">:</td>
		<td width="15%" valign="top" class="smalltext23">
			<div  id="displayDate" style="display:<?php echo $displayDate;?>">
				&nbsp;&nbsp;
				<input type="text" name="searchOn" value="<?php echo $searchOn;?>" id="atOn" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('atOn','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
			</div>
			<div  id="displayMonth" style="display:<?php echo $displayMonth;?>">
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
			<td colspan="6" height="15"></td>
		</tr>
	</table>
</form>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td width="5%" class="smalltext23"><b>SR NO</b></td>
		<td width="20%" class="smalltext23"><b>EMPLOYEE NAME</b></td>
		<td width="17%" class="smalltext23"><b>LEAVE TYPE</b></td>
		<td class="smalltext23" width="8%"><b>LEAVE ON</b></td>

		<td width="5%" class="smalltext23"><b>SR NO</b></td>
		<td width="20%" class="smalltext23"><b>EMPLOYEE NAME</b></td>
		<td width="17%" class="smalltext23"><b>LEAVE TYPE</b></td>
		<td class="smalltext23"><b>LEAVE ON</b></td>
	</tr>
	<tr>
		<td colspan="8">
			<hr size="1" width="100%" color="#bebebe">
		</td>
	</tr>
</table>
<?php
	$i			=	0;
	$m			=	0;
	$j			=	0;
	$noResult	=	1;
	$query		=	"SELECT employee_attendence.*,fullName FROM employee_attendence INNER JOIN employee_details ON employee_attendence.employeeId=employee_details.employeeId".$whereClause." AND employee_attendence.onLeave <> 0".$andClause." GROUP BY employee_details.employeeId ORDER BY loginDate DESC";
	$result		=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		$noResult	=	0;
?>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
	<?php		
		while($row	=	mysqli_fetch_assoc($result))
		{
			$i++;
			$m++;
			$j++;
			$employeeId			=	$row['employeeId'];
			$loginDate			=	showDate($row['loginDate']);
			$onLeave			=	$row['onLeave'];
					
			$leaveText			=	"FULL DAY";

			if($onLeave			==	2)
			{
				$leaveText		=	"HALF DAY";
			}
			$employeeName		=	ucwords($row['fullName']);
		?>
		<td width="50%">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td class="smalltext2" width="10%"><?php echo $i;?>)</td>
					<td class="smalltext2" width="40%"><?php echo $employeeName;?></td>
					<td class="smalltext2" width="34%"><font color="#ff0000;"><?php echo $leaveText;?></font></td>
					<td class="smalltext2"><?php echo $loginDate;?></td>
				</tr>
			</table>
		</td>
<?php
			if($m	==	 2){
				echo "</tr><tr><td height='4'></td></tr><tr>";
				$m	=	0;
			}
		}
		if($j < 2){
			echo "<td></td>";
		}
?>
		</tr>
	</table>
<?php
	}
?>
	<?php
	if(!empty($a_addedLoginLeave) && count($a_addedLoginLeave) > 0 && $t_searchOn < $nowDateIndia && $searchBy	==	1){
		$a_addedLoginLeave			=	implode(",",$a_addedLoginLeave);
		$query1				=	"SELECT fullName from employee_details".$whereClause.$andClause1." AND employeeId NOT IN ($a_addedLoginLeave) ORDER BY fullName";
		$result1			=	dbQuery($query1);
		$k					=	$i;
		if(mysqli_num_rows($result1)){
			$n				=	0;
			$noResult		=	0;
?>
<br />
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
<?php
		while($row1			=	mysqli_fetch_assoc($result1)){
			$t_fullName		=	ucwords($row1['fullName']);
			$k++;
			$n++;
	?>
	<td width="50%">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
			<tr>
				<td class="smalltext2" width="10%"><?php echo $k;?>)</td>
				<td class="smalltext2" width="40%"><?php echo $t_fullName;?></td>
				<td class="smalltext2" width="34%"><font color="#ff0000;">Not Applied Leave/Not Log in</font></td>
				<td class="smalltext2"><?php echo showDate($t_searchOn);?></td>
			</tr>
		</table>
	</td>
	<?php
			if($n	==	 2){
				echo "</tr><tr><td height='4'></td></tr><tr>";
				$n	=	0;
			}
		}
	?>
	</tr>
</table>
<?php
		}
	}

	if($noResult == 1){
		echo "<br><br><br><center><font class='error'><b>NO LEAVE AVAILABLE !!</b></font></center><br><br><br><br><br><br><br>";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>