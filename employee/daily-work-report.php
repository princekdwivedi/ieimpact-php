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
	$andClause		=	"";
	$text1			=	"ALL EMPLOYEE";
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
	$printLink		=	"";
	$employeeSearch =	"";
	$andClause		=	" AND workedOnDate='$t_searchOn'";
	$printLink		=	"searchBy=1&date=".$t_searchOn.$employeeSearch;
	$orderBy		=	"firstName";
	$queryString	=	"&searchBy=1&searchOn=".$searchOn;

	$summeryLink	=	" ON ".showDate($t_searchOn);
	$type			=	0;
	$manager		=	0;
	$a_managers		=	$employeeObj->getAllEmployeeManager();
	
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	
	if(isset($_POST['formSubmitted']))
	{
		if(isset($_POST['employeeId']))
		{
			$a_employeeId	=	$_POST['employeeId'];
		}
		$searchBy		=	$_POST['searchBy'];
		if(isset($_POST['searchBy']))
		{
			$redirectLink	=	"searchBy=".$searchBy;
		}
		if(isset($_POST['type']))
		{
			$type			=	$_POST['type'];
			$redirectLink  .=	"&type=".$type;
		}
		if(isset($_POST['manager']))
		{
			$manager		=	$_POST['manager'];
			$redirectLink  .=	"&manager=".$manager;
		}
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
		if(!empty($a_employeeId))
		{
			if(!in_array("0",$a_employeeId))
			{
				$searchEmployee	=	implode(",",$a_employeeId);
				$redirectLink  .=	"&employee=".$searchEmployee;
			}
		}
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/daily-work-report.php?".$redirectLink);
		exit();
	}
	if(isset($_GET['searchBy']))
	{
		$summeryLink			=	"";
		$searchBy				=	$_GET['searchBy'];
		$queryString			=	"&searchBy=".$searchBy;
		$printLink				=	"searchBy=".$searchBy;
		if($searchBy	== 1)
		{
			$searchOn					=	$_GET['searchOn'];

			list($day,$month,$year)		=	explode("-",$searchOn);
			$t_searchOn		=	$year."-".$month."-".$day;

			$andClause		=	" AND workedOnDate='$t_searchOn'";
			$text1		    =	" ON ".showDate($t_searchOn);
			$queryString   .=	"&searchOn=".$searchOn;
			$printLink	   .=	"&date=".$t_searchOn;
			$orderBy		=	"firstName";
		}
		else
		{
			$month			=	$_GET['month'];
			$year			=	$_GET['year'];
			$andClause		=	" AND MONTH(workedOnDate)=$month AND YEAR(workedOnDate)=$year";

			$displayDate	=	"none";
			$displayMonth	=	"";
			$checked		=	"";
			$checked1		=	"checked";
			$monthText		=	$a_month[$month];
			$text1		    =	$monthText.",".$year;

			$queryString   .=	"&month=".$month."&year=".$year;
			$printLink	   .=	"&month=".$month."&year=".$year;
			$orderBy		=	"workedOnDate DESC";
		}
		if(isset($_REQUEST['type']))
		{
			$type				=	$_REQUEST['type'];
			if(!empty($type))
			{
				$andClause	   .=	" AND employee_details.employeeType=$type";
				$text1		   .=	" for ".$a_inetExtEmployee[$type]." employees";
				$queryString   .=   "&type=".$type;
				$printLink     .=   "&type=".$type;
			}
		}
		if(isset($_REQUEST['manager']))
		{
			$manager			=	$_REQUEST['manager'];
			if(!empty($manager))
			{
				$andClause	   .=	" AND employee_details.underManager=$manager";
				$text1		   .=	" under manager ".$a_managers[$manager];
				$queryString   .=   "&manager=".$manager;
				$printLink     .=   "&manager=".$manager;
			}
		}
		if(isset($_GET['employee']))
		{
			$a_employeeId	=	$_GET['employee'];
			if(!empty($a_employeeId))
			{
				$a_employeeId	=	explode(",",$a_employeeId);
				if(!in_array("0",$a_employeeId))
				{
					$searchEmployee	=	implode(",",$a_employeeId);
					$employeeSearch	=	"&employee=".$searchEmployee;

					$andClause		.=	" AND datewise_employee_works_money.employeeId IN ($searchEmployee)";
					$totalEmloyee	=	count($a_employeeId);
					if($totalEmloyee < 2 && $totalEmloyee > 0)
					{
						foreach($a_employeeId as $key=>$value)
						{
							$employeeName	=	$employeeObj->getEmployeeName($value);
						} 
						$text1			.=	" FOR ".$employeeName;
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
	}



	$whereClause				=   "WHERE datewise_employee_works_money.ID > ".MAX_SEARCH_MT_EMPLOYEE_WORKID." AND  employee_shift_rates.departmentId=1";

	$totalDirectTranscription	=	0;
	$totalIndirectTranscription	=	0;
	$totalDirectVre				=	0;
	$totalIndirectVre			=	0;
	$totalDirectQa				=	0;
	$totalIndirectQa			=	0;
	$totalDirectPostAudit		=	0;
	$totalIndirectPostAudit		=	0;
	
	$showSummary				=	false;

	$seachingFromAttendence		=	@mysql_result(dbQuery("SELECT loginDate FROM employee_attendence WHERE attendenceId > '".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID."' AND loginDate <> '0000-00-00' ORDER BY attendenceId LIMIT 1"),0);
	$headingText	=	"This page will show records from - ".showDate($seachingFromAttendence);

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
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class="textstyle3"><b><font color="#ff0000"><?php echo $headingText;?></font></b></td>
	</tr>
</table>
<form name="searchForm" action=""  method="POST" onsubmit="return search();">
	<table cellpadding="0" cellspacing="0" width='100%'align="center" border='0'>
		<tr>
			<td colspan="15" height="5"></td>
		</tr>
		<tr>
			<td width="28%" class="smalltext2" valign="top">Employees Works By <input type="radio" name="searchBy" value="1" <?php echo $checked;?> onclick="return showSearch(1)">Date Or By <input type="radio" name="searchBy" value="2" <?php echo $checked1;?> onclick="return showSearch(2)">Month</td>
			<td width="1%" class="smalltext2" valign="top">:</td>
			<td width="15%" valign="top" class="smalltext2">
				<div  id="displayDate" style="display:<?php echo $displayDate;?>">
					DATE&nbsp;&nbsp;
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
			<td width="8%" class="smalltext2" valign="top">
				For Employee
			</td>
			<td width="15%" valign="top">
				<select name="employeeId[]" multiple style="height:100px;">
					<option value="0">All</option>
					<?php
						if($result	=	$employeeObj->getAllMtEmployees())
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
<script type="text/javascript">
function openEditWidow(workId)
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/edit-mt-daily-works.php?ID="+workId;
	prop = "toolbar=no,scrollbars=yes,width=750,height=600,top=100,left=100";
	window.open(path,'',prop);
}
function openEditWidowMessage(workId)
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/send-message-to-employee.php?ID="+workId;
	prop = "toolbar=no,scrollbars=yes,width=750,height=600,top=100,left=100";
	window.open(path,'',prop);
}
function openPrintExcelWindow(pageUrl,extra)
{
	path = pageUrl+extra;
	prop = "toolbar=no,scrollbars=yes,width=400,height=100,top=200,left=300";
	window.open(path,'',prop);
}
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td colspan="2" class='title'>VIEW WORK SHEET FOR <?php echo $text1;?></td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
</table>
<br>
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
<tr height='25' bgcolor="#373737">
	<td width="3%" class="smalltext12" valign="top"><b>S.No</b></td>
	<td width="12%" class="smalltext12" valign="top"><b>Employee Name</b></td>
	<td width="8%" class="smalltext12" valign="top"><b>Date</b></td>
	<td width="8%" class="smalltext12" valign="top"><b>Platform</b></td>
	<td width="10%" class="smalltext12" valign="top"><b>Client</b></td>
	<td width="11%" class="smalltext12" valign="top"><b>Transcription</b></td>
	<td width="8%" class="smalltext12" valign="top"><b>VRE</b></td>
	<td width="8%" class="smalltext12" valign="top"><b>QA</b></td>
	<td width="11%" class="smalltext12" valign="top"><b>Night shift lines</b></td>
	<td class="smalltext12" valign="top"><b>Comments</b></td>
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
	$pagingObj->whereClause   =	$whereClause.$andClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"datewise_employee_works_money INNER JOIN employee_details ON datewise_employee_works_money .employeeId=employee_details.employeeId INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId";
	$pagingObj->selectColumns = "datewise_employee_works_money .*,fullName";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/daily-work-report.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$showSummary	=	true;
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
?>
<tr>
	<td colspan="10">
		<?php
			$printUrl	=	SITE_URL_EMPLOYEES."/print-daily-mt-worksheet.php?".$printLink;
		?>
		<a onclick="openPrintExcelWindow('<?php echo $printUrl?>','')" class='link_style10' style="cursor:pointer;" title="PRINT THIS REPORT IN EXCEL SHEET">PRINT THIS REPORT IN EXCEL SHEET</a>
	</td>
</tr>
<tr>
	<td colspan="10">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
 <tr>
	<td colspan="10" valign="top">
	<div style='border:0px solid #ff0000;overflow:auto;height:2300px'>
	<table width="100%" align="center" border="0" cellpadding="2" cellspacing="2">
	<tr>
		<td height="9"></td>
	</tr> 
<?php
		$i			=	$recNo;
		while($row	=   mysql_fetch_assoc($recordSet))
		{
			$i++;
			$employeeId					=	$row['employeeId'];
			$datewiseID					=	$row['ID'];
			$fullName					=	stripslashes($row['fullName']);
			$platform					=	$row['platform'];
			$customerId					=	$row['customerId'];
			$comments					=	$row['comments'];
			$workedOn					=	showDate($row['workedOnDate']);

			$transcriptionLinesEntered	=	$row['totalDirectTrascriptionLines'];
			$vreLinesEntered			=	$row['totalDirectVreLines'];
			$qaLinesEntered				=	$row['totalQaLines'];
			
			$indirectTranscriptionLinesEntered	=	$row['totalIndirectTrascriptionLines'];
			$indirectVreLinesEntered	=	$row['totalIndirectVreLines'];
			$indirectQaLinesEntered		=	$row['totalIndirectQaLines'];

			$auditLinesEntered			=	$row['totalDirectAuditLines'];
			$indirectAuditLinesEntered	=	$row['totalIndirectAuditLines'];

			$transcriptionUserId		=	$row['transcriptionUserId'];
			$vreUserId					=	$row['vreUserId'];
			$qaUserId					=	$row['qaUserId'];
			$auditUserId				=	$row['auditUserId'];

			$platName					=	$employeeObj->getPlatformName($platform);
			$customerName				=	$employeeObj->getCustomerName($customerId,$platform);
			$employeeName				=	ucwords($fullName);

			$totalDirectTranscription	=	$totalDirectTranscription+$transcriptionLinesEntered;
			$totalIndirectTranscription	=	$totalIndirectTranscription+$indirectTranscriptionLinesEntered;
			$totalDirectVre				=	$totalDirectVre+$vreLinesEntered;
			$totalIndirectVre			=	$totalIndirectVre+$indirectVreLinesEntered;
			$totalDirectQa				=	$totalDirectQa+$qaLinesEntered;
			$totalIndirectQa			=	$totalIndirectQa+$indirectQaLinesEntered;
			$totalDirectPostAudit		=	$totalDirectPostAudit+$auditLinesEntered;
			$totalIndirectPostAudit		=	$totalIndirectPostAudit+$indirectAuditLinesEntered;
	 ?>
	<tr>
			<td class="text2" width="3%"  valign="top"><?php echo $i;?>.</td>
			<td class="text2" width="12%" valign="top"><?php echo $employeeName;?></td>
			<td class="text2" width="8%" valign="top"><?php echo $workedOn;?></td>
			<td class="text2" width="8%" valign="top"><?php echo $platName;?></td>
			<td class="text2" width="10%" valign="top"><?php echo $customerName;?></td>
			<td class="text2" width="11%" valign="top">
				<?php 
					if($transcriptionLinesEntered)
					{
						echo "D-".$transcriptionLinesEntered."<br>";
					}
					if($indirectTranscriptionLinesEntered)
					{
						echo "N-".$indirectTranscriptionLinesEntered;
					}
					if(!empty($transcriptionUserId))
					{
						echo "<br><font color='#ff0000'>User ID - ".$transcriptionUserId."</font>";
					}
				?>
			</td>
			<td class="text2" width="8%" valign="top">
				<?php 
					if($vreLinesEntered)
					{
						echo "D-".$vreLinesEntered."<br>";
					}
					if($indirectVreLinesEntered)
					{
						echo "N-".$indirectVreLinesEntered;
					}
					if(!empty($vreUserId))
					{
						echo "<br><font color='#ff0000'>User ID - ".$vreUserId."</font>";
					}
				?>
			</td>
			<td class="text2" width="8%" valign="top">
				<?php 
					if($qaLinesEntered)
					{
						echo "D-".$qaLinesEntered."<br>";
					}
					if($indirectQaLinesEntered)
					{
						echo "N-".$indirectQaLinesEntered;
					}
					if(!empty($qaUserId))
					{
						echo "<br><font color='#ff0000'>User ID - ".$qaUserId."</font>";
					}
				?>
			</td>
			<td class="text2" width="11%" valign="top">
				<?php 
					if($auditLinesEntered)
					{
						echo "T-".$auditLinesEntered."<br>";
					}
					if($indirectAuditLinesEntered)
					{
						echo "V-".$indirectAuditLinesEntered;
					}
					if(!empty($auditUserId))
					{
						echo "<br><font color='#ff0000'>User ID - ".$auditUserId."</font>";
					}
				?>
			</td>
			<td class="text2" valign="top" width="18%">
				<p align="justify">
				<?php 
					echo $comments;
				?>
				</p>
			</td>
			<td valign="top">
				<a onclick='openEditWidow(<?php echo $datewiseID;?>)' class='link_style12' style="cursor:pointer">Edit</a>
			</td>
		</tr>
		<tr>
			<td colspan="11">
				<hr size="1" width="100%" color="#e4e4e4">
			</td>
		</tr>
	<?php
		}
	?>
		  </table>
		</div>
	 </td>
	</tr>
	<?php
		echo "<tr><td colspan='10'><br /><table width='90%' border='0' ><tr><td align=center><font color='#000000'>";
		$pagingObj->displayPaging($queryString);
		echo "<br /><b>Total Records : " . $totalRecords . "</font><br /></b></td></tr></table></td></tr>";
	}
	else
	{
		echo "<tr><td colspan='10' class='error' style='text-align:center;'><b>NO WORKS AVAILABLE !!</b></td></tr><tr><td colspan='9' height='150'></td></tr>";
	}
	echo "<br /></table>";
	if($showSummary)
	{
		$query	=	"SELECT SUM(totalDirectTrascriptionLines) AS totalTranscriptionLinesEntered,SUM(totalIndirectTrascriptionLines) AS totalIndirectTranscriptionLinesEntered,SUM(totalDirectVreLines) AS totalVreLinesEntered,SUM(totalIndirectVreLines) AS totalIndirectVreLinesEntered,SUM(totalQaLines) AS totalQaLinesEntered,SUM(totalIndirectQaLines) AS totalIndirectQaLinesEntered,SUM(totalDirectAuditLines) AS totalAuditLinesEntered,SUM(totalIndirectAuditLines) AS totalIndirectAuditLinesEntered FROM datewise_employee_works_money INNER JOIN employee_details ON datewise_employee_works_money.employeeId=employee_details.employeeId WHERE ID > ".MAX_SEARCH_MT_EMPLOYEE_WORKID." ".$andClause;
		$result	=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			$row	=	mysql_fetch_assoc($result);
			$totalTranscriptionLinesEntered			=	$row['totalTranscriptionLinesEntered'];
			$totalIndirectTranscriptionLinesEntered	=	$row['totalIndirectTranscriptionLinesEntered'];
			$totalVreLinesEntered	=	$row['totalVreLinesEntered'];
			$totalIndirectVreLinesEntered			=	$row['totalIndirectVreLinesEntered'];
			$totalQaLinesEntered					=	$row['totalQaLinesEntered'];
			$totalIndirectQaLinesEntered			=	$row['totalIndirectQaLinesEntered'];
			$totalAuditLinesEntered					=	$row['totalAuditLinesEntered'];
			$totalIndirectAuditLinesEntered	=	$row['totalIndirectAuditLinesEntered'];

			if(empty($totalTranscriptionLinesEntered))
			{
				$totalTranscriptionLinesEntered	=	0;
			}
			if(empty($totalIndirectTranscriptionLinesEntered))
			{
				$totalIndirectTranscriptionLinesEntered	=	0;
			}
			if(empty($totalVreLinesEntered))
			{
				$totalVreLinesEntered	=	0;
			}
			if(empty($totalIndirectVreLinesEntered))
			{
				$totalIndirectVreLinesEntered	=	0;
			}
			if(empty($totalQaLinesEntered))
			{
				$totalQaLinesEntered	=	0;
			}
			if(empty($totalIndirectQaLinesEntered))
			{
				$totalIndirectQaLinesEntered	=	0;
			}
			if(empty($totalAuditLinesEntered))
			{
				$totalAuditLinesEntered	=	0;
			}
			if(empty($totalIndirectAuditLinesEntered))
			{
				$totalIndirectAuditLinesEntered	=	0;
			}
		}
?>
<table width="100%" border="0" cellpadding="0" cellspacing="3" align="center">
<tr>
	<td colspan="3" class='title'>
		TOTAL WORK DONE SUMMARY ON - <?php echo $text1.$summeryLink;?>
	</td>
</tr>
<tr>
	<td colspan="3" height="10">
		
	</td>
</tr>
<tr>
	 <td width="25%" class="smalltext2"><b>Transcription (SINGLE) Lines</b></td>
	<td width="15%" class="smalltext2"><b>DSP - <?php echo $totalTranscriptionLinesEntered;?></b></td>
	<td class="smalltext2"><b>N-DSP - <?php echo $totalIndirectTranscriptionLinesEntered;?></b></td>
</tr>
<tr>
	<td colspan="3" height="10"></td>
</tr>
<tr>
	<td class="smalltext2"><b>VRE Lines</b></td>
	<td class="smalltext2"><b>DSP - <?php echo $totalVreLinesEntered;?></b></td>
	<td class="smalltext2"><b>N-DSP - <?php echo $totalIndirectVreLinesEntered;?></b></td>
</tr>
<tr>
	<td colspan="3" height="10"></td>
</tr>
<tr>
	<td class="smalltext2"><b>QA Lines</b></td>
	<td class="smalltext2"><b>DSP - <?php echo $totalQaLinesEntered;?></b></td>
	<td class="smalltext2"><b>N-DSP - <?php echo $totalIndirectQaLinesEntered;?></b></td>
</tr>
<tr>
	<td colspan="3" height="10"></td>
</tr>
<tr>
	<td class="smalltext2"><b>Post Audit Lines</b></td>
	<td class="smalltext2"><b>DSP - <?php echo $totalAuditLinesEntered;?></b></td>
	<td class="smalltext2"><b>N-DSP - <?php echo $totalIndirectAuditLinesEntered;?></b></td>
</tr>
<tr>
	<td colspan="3" height="10"></td>
</tr>
</table>
<?php
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>
