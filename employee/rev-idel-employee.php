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
	$pagingObj					=	new Paging();
	$employeeObj				=	new employee();
	$andClause					=	"";
	$andClause1					=	"";
	$searchOn					=	date("d-m-Y");
	$t_searchOn					=	date("Y-m-d");
	$redirectLink				=	"";
	$text						=	"";
	$printLink					=	"";
	$month						=	date("m");
	$year						=	date("Y");

	$type						=	0;
	$manager					=	0;
	$a_managers					=	$employeeObj->getAllEmployeeManager();

	$displayDate				=	"";
	$displayMonth				=	"none";
	$checked					=	"checked";
	$checked1					=	"";
	$searchBy					=	1;
	$andClause1					=	" AND workedOn='$t_searchOn'";

	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$whereClause				=   "WHERE employee_shift_rates.departmentId=2 AND isActive=1";
	$orderBy					=	"firstName";
	$queryString				=	"";

	if(isset($_POST['formSubmitted']))
	{
		$searchBy			=	$_POST['searchBy'];
		$redirectLink		=	"searchBy=".$searchBy;
		if($searchBy		==	1)
		{
			$searchOn		=	$_POST['searchOn'];
			$redirectLink  .=	"&searchOn=".$searchOn;
		}
		else
		{
			$month			=	$_POST['month'];
			$year			=	$_POST['year'];

			$redirectLink  .=	"&month=".$month."&year=".$year;
			
		}
		if(isset($_POST['type']))
		{
			$type			=	$_POST['type'];
			if(!empty($type))
			{
				$redirectLink  .=	"&type=".$type;
			}
		}
		if(isset($_POST['manager']))
		{
			$manager		=	$_POST['manager'];
			if(!empty($manager))
			{
				$redirectLink  .=	"&manager=".$manager;
			}
		}
		
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/rev-idel-employee.php?".$redirectLink);
		exit();
	}

	if(isset($_GET['searchBy']))
	{
		$searchBy				=	$_GET['searchBy'];
		$queryString			=	"&searchBy=".$searchBy;
		$printLink				=	"searchBy=".$searchBy;

		if($searchBy	== 1)
		{
			$searchOn					=	$_GET['searchOn'];

			list($day,$month,$year)		=	explode("-",$searchOn);
			$t_searchOn		=	$year."-".$month."-".$day;

			$andClause1		=	" AND workedOn='$t_searchOn'";
			$text			=	"View idle REV employees on - ".showDate($t_searchOn);
			$queryString   .=	"&searchOn=".$t_searchOn;
			$printLink	   .=	"&date=".$t_searchOn;
		}
		else
		{
			$month			=	$_GET['month'];
			$year			=	$_GET['year'];
			$andClause1		=	" AND MONTH(workedOn)=$month AND YEAR(workedOn)=$year";

			$displayDate	=	"none";
			$displayMonth	=	"";
			$checked		=	"";
			$checked1		=	"checked";
			$monthText		=	$a_month[$month];
			$text		    =	"View idle REV employees on - ".$monthText.",".$year;

			$queryString   .=	"&month=".$month."&year=".$year;
			$printLink	   .=	"&month=".$month."&year=".$year;
		}
	}

	if(isset($_GET['type']))
	{
		$type				=	$_GET['type'];
		if(!empty($type))
		{
			$andClause	   .=	" AND employee_details.employeeType=$type";
			$text		   .=	" for ".$a_inetExtEmployee[$type]." employees";
			$printLink     .=   "&type=".$type;
			$queryString   .=	"&type=".$type;
		}
	}
	if(isset($_REQUEST['manager']))
	{
		$manager			=	$_REQUEST['manager'];
		if(!empty($manager))
		{
			$andClause	   .=	" AND employee_details.underManager=$manager";
			$text		   .=	" under manager ".$a_managers[$manager];
			$queryString   .=   "&manager=".$manager;
			$printLink     .=   "&manager=".$manager;
		}
	}


?>
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

<form name="searchForm" action=""  method="POST">
	<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
		<tr>
			<td colspan="15" class="textstyle1">
				<b>VIEW REV DAILY IDLE EMPLOYEE (Didn't do work or added lines)</b>
			</td>
		</tr>
		<tr>
			<td colspan="15" height="10"></td>
		</tr>
		<tr>
			<td width="28%" class="smalltext2" valign="top"><b>View By <input type="radio" name="searchBy" value="1" <?php echo $checked;?> onclick="return showSearch(1)">Date Or By <input type="radio" name="searchBy" value="2" <?php echo $checked1;?> onclick="return showSearch(2)">Month</b></td>
			<td width="1%" class="smalltext2" valign="top">:</td>
			<td width="15%" valign="top" class="smalltext2">
				<div  id="displayDate" style="display:<?php echo $displayDate;?>">
					<input type="text" name="searchOn" value="<?php echo $searchOn;?>" class="textbox" id="atOn" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('atOn','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
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
			<td width="5%" class="smalltext2" valign="top"><b>Type</b></td>
			<td width="12%" valign="top">
				<select name="type">
					<option value="">All</option>
					<?php
						foreach($a_inetExtEmployee as $key=>$value)
						{
							$select		=	"";
							if($type == $key)
							{
								$select	=	"selected";
							}
							echo "<option value='$key' $select>$value</option>";
						}
					?>
				</select>
			</td>
			<td width="7%" class="smalltext2" valign="top"><b>Manger</b></td>
			<td width="13%" valign="top">
				<select name="manager">
					<option value="">All</option>
					<?php
						foreach($a_managers as $key=>$value)
						{
							$select	=	"";
							if($manager	==	$key)
							{
								$select	=	"selected";
							}

							echo "<option value='$key' $select>$value</option>";
						}
					?>
				</select>
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
<br>
<script type="text/javascript">
function openEditWidow(employeeId,date)
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/add-idle-mt-works.php?ID="+employeeId+"&date="+date;
	prop = "toolbar=no,scrollbars=yes,width=750,height=600,top=100,left=100";
	window.open(path,'',prop);
}
</script>
<?php
	$query	=	"SELECT employee_details.* FROM employee_details INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId ".$whereClause.$andClause." ORDER BY firstName";
	$result	=	mysql_query($query);
	if(mysql_num_rows($result))
	{
?>
<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
	<tr>
		<td colspan="10" class="title"><?php echo $text;?></td>
	</tr>
	<tr>
		<td colspan="10" height="5">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="10">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/print-idle-rev-employee.php?<?php echo $printLink?>" class="link_style9">PRINT THIS REPORT IN EXCEL SHEET</a>
		</td>
	</tr>
	<tr>
		<td colspan="10">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
	<?php
		$i			=	0;
		while($row	=   mysql_fetch_assoc($result))
		{
			$employeeId					=	$row['employeeId'];
			$firstName					=	stripslashes($row['firstName']);
			$lastName					=	stripslashes($row['lastName']);

			$employeeName				=	$firstName." ".$lastName;

			$query1		=	"SELECT workId FROM employee_works WHERE employeeId=$employeeId".$andClause1;
			$result1	=	dbQuery($query1);
			if(!mysql_num_rows($result1))
			{
				$i++;
	?>
	<tr>
			<td class="text2" valign="top" width="5%"><?php echo $i;?>)</td>
			<td class="text2" valign="top" width="20%"><?php echo $employeeName;?></td>
			<td valign="top">
				<!-- <?php
					if($searchBy	==	1)
					{
					if($t_searchOn	< CURRENT_DATE_INDIA)
					{
				?>
				<a href="javascript:openEditWidow(<?php echo $employeeId;?>,'<?php echo $searchOn;?>')" class='link_style12'>Add Work</a>
				<?php
					}
					else
					{
						echo "<font class='error'>Cannot add works for current date</font>";
					}
					}
				?> -->
			</td>
	</tr>
	<tr>
		<td colspan="5">
			<hr size="1" width="100%" color="#bebebe">
		</td>
	</tr>
	<?php
			}
		}
	?>
</table>
<?php

	}
	else
	{
		echo "<br><br><center><font class='error'><b>No Employee Found !!</b></font></center>";
	}

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>