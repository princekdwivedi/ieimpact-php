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
	$text1						=	"ALL EMPLOYEE";
	$employeeName				=	"";
	$displayDate				=	"";
	$displayMonth				=	"none";
	$checked					=	"checked";
	$checked1					=	"";
	$type						=	0;
	$manager					=	0;
	$a_managers					=	$employeeObj->getAllEmployeeManager();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$searchOn		=	date("d-m-Y");
	$t_searchOn		=	date("Y-m-d");
	$month			=	date("m");
	$year			=	date("Y");
	$andClause		=	" AND workedOn='$t_searchOn'";

	$employeeId		=	0;
	$a_employeeId	=	array();
	$printLink		=	"";
	$employeeSearch =	"";
	$orderBy		=	"firstName";
	$queryString	=	"";
	$printLink		=	"";
	if(isset($_GET['workId']))
	{
		$workId		=	$_GET['workId'];
		$isDelete	=	$_GET['isDelete'];
		if($isDelete	==	1)
		{
			$query		=	"SELECT * FROM employee_works WHERE workId=$workId";
			$result		=	mysql_query($query);
			if(mysql_num_rows($result))
			{
				$row			=	mysql_fetch_assoc($result);
				$assignedWorkId	=	$row['assignedWorkId'];
				$employeeId		=	$row['employeeId'];
				$directLevel1	=	$row['directLevel1'];
				$directLevel2	=	$row['directLevel2'];
				$indirectLevel1	=	$row['indirectLevel1'];
				$indirectLevel2	=	$row['indirectLevel2'];
				$qaLevel1		=	$row['qaLevel1'];
				$qaLevel2		=	$row['qaLevel2'];
				$auditLevel1	=	$row['auditLevel1'];
				$auditLevel2	=	$row['auditLevel2'];
				$query1			=	"SELECT * FROM assign_employee_works WHERE assignedWorkId=$assignedWorkId AND employeeId=$employeeId AND status=2";
				$result1		=	mysql_query($query1);
				if(mysql_num_rows($result1))
				{
					
					dbQuery("UPDATE assign_employee_works SET status=1,completedOn='0000-00-00' WHERE assignedWorkId=$assignedWorkId AND employeeId=$employeeId");

					dbQuery("DELETE FROM employee_works WHERE workId=$workId AND employeeId=$employeeId");

					dbQuery("DELETE FROM datewise_employee_works_money WHERE workId=$workId AND employeeId=$employeeId");


				}
				else
				{
					dbQuery("DELETE FROM employee_works WHERE workId=$workId AND employeeId=$employeeId");

					dbQuery("DELETE FROM datewise_employee_works_money WHERE workId=$workId AND employeeId=$employeeId");
				}
			}	
			
		}
		if(isset($_GET['rec']))
		{
			$rec	=	$_GET['rec'];
			if(!empty($rec))
			{
				$link		=	"?recNo=$rec";
			}
		}
		else
		{
			$link	=	"";
		}
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/rev-daily-worksheet.php$link");
		exit();
	}
	$printLink		=	"searchBy=1&searchOn=".$t_searchOn;
	$text1			=	" ON ".showDate($t_searchOn);

	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		//print_r($_REQUEST);
		if(isset($_POST['employeeId']))
		{
			$a_employeeId	=	$_POST['employeeId'];
		}
		if(isset($_POST['searchBy']))
		{
			$searchBy		=	$_POST['searchBy'];
			$queryString	=	"&searchBy=".$searchBy;
			$printLink		=	"searchBy=".$searchBy;
		}
		if($searchBy		==	1)
		{
			list($day,$month,$year)		=	explode("-",$searchOn);
			$t_searchOn		=	$year."-".$month."-".$day;
			$andClause		=	" AND workedOn='$t_searchOn'";
			$text1			=	" ON ".showDate($t_searchOn);
			$queryString   .=	"&searchOn=".$t_searchOn;
			$printLink	   .=	"&date=".$t_searchOn;
			$orderBy		=	"firstName";
		}
		else
		{
			$month			=	$_POST['month'];
			$year			=	$_POST['year'];
			$andClause		=	" AND MONTH(workedOn)=$month AND YEAR(workedOn)=$year";

			$displayDate	=	"none";
			$displayMonth	=	"";
			$checked		=	"";
			$checked1		=	"checked";
			$monthText		=	$a_month[$month];
			$text1			=	$monthText.",".$year;

			$queryString   .=	"&month=".$month."&year=".$year;
			$printLink	   .=	"&month=".$month."&year=".$year;
			$orderBy		=	"workedOn DESC";
		}
		if(isset($_POST['type']))
		{
			$type			=	$_POST['type'];
			if(!empty($type))
			{
				$andClause	   .=	" AND employee_details.employeeType=$type";
				$text1		   .=	" for ".$a_inetExtEmployee[$type]." employees";
				$queryString   .=   "&type=".$type;
				$printLink     .=   "&employeeType=".$type;
			}
		}
		if(isset($_POST['manager']))
		{
			$manager			=	$_POST['manager'];
			if(!empty($manager))
			{
				$andClause	   .=	" AND employee_details.underManager=$manager";
				$text1		   .=	" under manager ".$a_managers[$manager];
				$queryString   .=   "&manager=".$manager;
				$printLink     .=   "&underManager=".$manager;
			}
		}
		if(!empty($a_employeeId))
		{
			if(!in_array("0",$a_employeeId))
			{
				$searchEmployee	=	implode(",",$a_employeeId);
				$employeeSearch	=	"&employee=".$searchEmployee;

				$andClause		.=	" AND employee_works.employeeId IN ($searchEmployee)";
				$totalEmloyee	=	count($a_employeeId);
				if($totalEmloyee < 2 && $totalEmloyee > 0)
				{
					foreach($a_employeeId as $key=>$value)
					{
						$employeeName	=	$employeeObj->getEmployeeName($value);
					}
					$text1			.=	" FOR".$employeeName;
				}
				else
				{
					$text1			.=	" FOR MULTILE EMPLOYEE";
				}
				$queryString   .=	"&employee=".$searchEmployee;
				$printLink	   .=	"&employee=".$searchEmployee;
			}
		}
	}

	$whereClause		=   "WHERE employee_shift_rates.departmentId=2";
	
	$totalDirectLevel1	=	0;
	$totalDirectLevel2	=	0;
	$totalIndirectLevel1=	0;
	$totalIndirectLevel2=	0;
	$totalQaLevel1		=	0;
	$totalQaLevel2		=	0;
	$totalAuditLevel1	=	0;
	$totalAuditLevel2	=	0;
	
	$showSummary				=	false;

?>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/validate.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>
<script type="text/javascript">
function deleteWork(workId,rec)
{
	var confirmation = window.confirm("Are you sure to delete this work?");
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/rev-daily-worksheet.php?workId="+workId+"&rec="+rec+"&isDelete=1";
	}
}
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
	<table cellpadding="0" cellspacing="0" width='100%'align="center" border='0'>
		<tr>
			<td colspan="15" class="textstyle1">
				<b>VIEW REV EMPLOYEES DAILY WORKSHEET</b>
			</td>
		</tr>
		<tr>
			<td colspan="15" height="5"></td>
		</tr>
		<tr>
			<td width="28%" class="smalltext2" valign="top">Employees Worksheet By <input type="radio" name="searchBy" value="1" <?php echo $checked;?> onclick="return showSearch(1)">Date Or By <input type="radio" name="searchBy" value="2" <?php echo $checked1;?> onclick="return showSearch(2)">Month</b></td>
			<td width="1%" class="smalltext2" valign="top">:</td>
			<td width="12%" valign="top" class="smalltext2">
				<div  id="displayDate" style="display:<?php echo $displayDate;?>">
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
			<td width="3%" class="smalltext2" valign="top">Type</td>
			<td width="7%" valign="top">
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
			<td width="5%" class="smalltext2" valign="top">Manger</td>
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
			<td width="10%" class="smalltext2" valign="top">
				For Employee
			</td>
			<td width="15%" valign="top">
				<select name="employeeId[]" multiple style="height:100px;">
					<option value="0">All</option>
					<?php
						if($result	=	$employeeObj->getAllRevEmployees())
						{
							while($row	=	mysql_fetch_assoc($result))
							{
								$t_employeeId	=	$row['employeeId'];
								$firstName		=	$row['firstName'];
								$lastName		=	$row['lastName'];

								$employeeName	=	$firstName." ".$lastName;
								$employeeName	=	ucwords($employeeName);

								
								$select			=	"";
								if(in_array($t_employeeId, $a_employeeId))
								{
									$select		=	"selected";
								}

								echo  "<option value='$t_employeeId' $select>$employeeName</option>";
							}
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
		<tr>
			<td colspan="7">&nbsp;</td>
			<td colspan="3" class="smalltext7">
				[Use Ctrl+Select to select multiple employees]
			</td>
		</tr>
	</table>
</form>
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td colspan="2" class='title'>VIEW WORK SHEET ON <?php echo $text1;?></td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
</table>
<br>
<table width="100%" border="0" cellpadding="0" cellspacing="3" align="center">
<tr>
	<td width="3%" class="smalltext2"><b>S.No</b></td>
	<td width="10%" class="smalltext2"><b>Employee Name</b></td>
	<td width="9%" class="smalltext2"><b>Worked On</b></td>
	<td width="8%" class="smalltext2"><b>Platform</b></td>
	<td width="8%" class="smalltext2"><b>Client</b></td>
	<td width="7%" class="smalltext2"><b>Direct</b></td>
	<td width="7%" class="smalltext2"><b>Indirect</b></td>
	<td width="6%" class="smalltext2"><b>QA</b></td>
	<td width="8%" class="smalltext2"><b>Post Audit</b></td>
	<td width="8%" class="smalltext2"><b>File Name</b></td>
	<td width="13%" class="smalltext2"><b>Comments</b></td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td colspan="12">
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
	$recsPerPage	          =	800;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"employee_works INNER JOIN employee_details ON employee_works.employeeId=employee_details.employeeId INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId";
	$pagingObj->selectColumns = "employee_works.*,firstName,lastName";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/rev-daily-worksheet.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$showSummary	=	true;
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
?>
<tr>
	<td colspan="12">
		<a href="<?php echo SITE_URL_EMPLOYEES;?>/print-daily-rev-worksheet.php?<?php echo $printLink;?>" class="link_style9">PRINT THIS REPORT IN EXCEL SHEET</a>
	</td>
</tr>
<tr>
	<td colspan="12">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<?php
		$i=0;
		while($row	=   mysql_fetch_assoc($recordSet))
		{
			$i++;
			$employeeId					=	$row['employeeId'];
			$assignedWorkId				=	$row['assignedWorkId'];
			$workId						=	$row['workId'];
			$firstName					=	$row['firstName'];
			$lastName					=	$row['lastName'];
			$platform					=	$row['platform'];
			$customerId					=	$row['customerId'];

			$workedOn					=	$row['workedOn'];

			$directLevel1				=	$row['directLevel1'];
			$directLevel2				=	$row['directLevel2'];
			$indirectLevel1				=	$row['indirectLevel1'];
			$indirectLevel2				=	$row['indirectLevel2'];
			$qaLevel1					=	$row['qaLevel1'];
			$qaLevel2					=	$row['qaLevel2'];
			$auditLevel1				=	$row['auditLevel1'];
			$auditLevel2				=	$row['auditLevel2'];
			$uploadFileName				=	$row['uploadFileName'];
			$workedOn					=	showDate($row['workedOn']);

			$comments					=	$row['comments'];
			
			$platName		=	$employeeObj->getPlatformName($platform);
			$customerName	=	$employeeObj->getCustomerName($customerId,$platform);
			$employeeName				=	$firstName." ".$lastName;
			$employeeName				=	ucwords($employeeName);

			$totalDirectLevel1	=	$totalDirectLevel1+$directLevel1;
			$totalDirectLevel2	=	$totalDirectLevel2+$directLevel2;

			$totalIndirectLevel1=	$totalIndirectLevel1+$indirectLevel1;
			$totalIndirectLevel2=	$totalIndirectLevel2+$indirectLevel2;

			$totalQaLevel1		=	$totalQaLevel1+$qaLevel1;
			$totalQaLevel2		=	$totalQaLevel2+$qaLevel2;

			$totalAuditLevel1	=	$totalAuditLevel1+$auditLevel1;
			$totalAuditLevel2	=	$totalAuditLevel2+$auditLevel2;
	 ?>
		<tr>
			<td class="text2" valign="top"><?php echo $i;?>.</td>
			<td class="text2" valign="top"><?php echo $employeeName;?></td>
			<td class="text2" valign="top"><?php echo $workedOn;?></td>
			<td class="text2" valign="top"><?php echo $platName;?></td>
			<td class="text2" valign="top"><?php echo $customerName;?></td>
			<td class="text2" valign="top">
				<?php 
					if($directLevel1)
					{
						echo "L1-".$directLevel1."<br>";
					}
					if($directLevel2)
					{
						echo "L2-".$directLevel2;
					}
				?>
			</td>
			<td class="text2" valign="top">
				<?php 
					if($indirectLevel1)
					{
						echo "L1-".$indirectLevel1."<br>";
					}
					if($indirectLevel2)
					{
						echo "L2-".$indirectLevel2;
					}
				?>
			</td>
			<td class="text2" valign="top">
				<?php 
					if($qaLevel1)
					{
						echo "L1-".$qaLevel1."<br>";
					}
					if($qaLevel2)
					{
						echo "L2-".$qaLevel2;
					}
				?>
			</td>
			<td class="text2" valign="top">
				<?php 
					if($auditLevel1)
					{
						echo "L1-".$auditLevel1."<br>";
					}
					if($auditLevel2)
					{
						echo "L2-".$auditLevel2;
					}
				?>
			</td>
			<td class="text2" valign="top"><?php echo $uploadFileName;?></td>
			<td class="text2" valign="top"><?php echo $comments;?></td>
			<td class="smalltext2" valign="top">
				<a href="<?php echo SITE_URL_EMPLOYEES;?>/edit-rev-daily-works.php?employeeId=<?php echo $employeeId;?>&workId=<?php echo $workId;?>&ID=<?php echo $assignedWorkId;?>" class="link_style5">Edit</a>|<a href="javascript:deleteWork(<?php echo $workId;?>,<?php echo $recNo;?>)" class="link_style5">Del</a>
			</td>
		</tr>
		<tr>
			<td colspan="12">
				<hr size="1" width="100%" color="#e4e4e4">
			</td>
		</tr>
	<?php
		}
		
		echo "<tr><td colspan='11'><table width='100%'><tr><td align='right'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr></table></td></tr>";
	
	}
	else
	{
		echo "<tr><td colspan='12' class='error'><b>NO WORKS AVAILABLE !!</b></td></tr><tr><td colspan='9' height='150'></td></tr>";
	}
	echo "</table>";
	if($showSummary)
	{
?>
<table width="100%" border="0" cellpadding="0" cellspacing="3" align="center">
<tr>
	<td colspan="3" class='title'>
		TOTAL WORK DONE SUMMARY ON - <?php echo showDate($t_searchOn);?>
	</td>
</tr>
<tr>
	 <td width="25%" class="smalltext2"><b>Direct</b></td>
	<td width="15%" class="smalltext2"><b>LEVEL1 - <?php echo $totalDirectLevel1;?></b></td>
	<td class="smalltext2"><b>LEVEL2 - <?php echo $totalDirectLevel2;?></b></td>
</tr>
<tr>
	<td colspan="3" height="10"></td>
</tr>
<tr>
	<td class="smalltext2"><b>Indirect</b></td>
	<td class="smalltext2"><b>LEVEL1 - <?php echo $totalIndirectLevel1;?></b></td>
	<td class="smalltext2"><b>LEVEL2 - <?php echo $totalIndirectLevel2;?></b></td>
</tr>
<tr>
	<td colspan="3" height="10"></td>
</tr>
<tr>
	<td class="smalltext2"><b>QA Lines</b></td>
	<td class="smalltext2"><b>LEVEL1 - <?php echo $totalQaLevel1;?></b></td>
	<td class="smalltext2"><b>LEVEL2 - <?php echo $totalQaLevel2;?></b></td>
</tr>
<tr>
	<td colspan="3" height="10"></td>
</tr>
<tr>
	<td class="smalltext2"><b>Post Audit</b></td>
	<td class="smalltext2"><b>LEVEL1 - <?php echo $totalAuditLevel1;?></b></td>
	<td class="smalltext2"><b>LEVEL2 - <?php echo $totalAuditLevel2;?></b></td>
</tr>
<tr>
	<td colspan="3" height="10"></td>
</tr>
</table>
<?php
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>
