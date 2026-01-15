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
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");

	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$pagingObj		=	new Paging();
	$employeeObj	=	new employee();

	$text1			=	"";
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
	$showForm		=	false;
	$showSummary	=	false;
	$redirectLink	=	"";
	$whereClause	=	"WHERE hasPdfAccess=1 AND isActive=1";
	$orderBy		=	"firstName";
	$andClause		=	"";
	$andClause1		=	"";
	$andClause2		=	"";

	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		$a_employeeId	=	$_POST['employeeId'];
		//pr($_REQUEST);
		$redirectLink	=	"?searchBy=".$searchBy;
		if($searchBy		== 1)
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
			$searchEmployee	=	implode(",",$a_employeeId);
			$redirectLink  .=	"&employee=".$searchEmployee;
		}

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/pdf-work-report.php".$redirectLink);
		exit();
	}

	if(isset($_GET['searchBy']))
	{
		$searchBy				=	$_GET['searchBy'];
		$showForm				=	true;
		$queryString			=	"&searchBy=".$searchBy;
		$printLink				=	"searchBy=".$searchBy;
		if($searchBy	== 1)
		{
			$searchOn					=	$_GET['searchOn'];

			list($day,$month,$year)		=	explode("-",$searchOn);
			$t_searchOn		=	$year."-".$month."-".$day;

			$text1		   .=	" ON ".showDate($t_searchOn);
			$queryString   .=	"&searchOn=".$searchOn;
			$printLink	   .=	"&searchOn=".$t_searchOn;
			$andClause		=	" AND assignToEmployee='$t_searchOn'";
			$andClause1		=	" AND replyFileAddedOn='$t_searchOn'";
			$andClause2		=	" AND qaDoneOn='$t_searchOn'";
		}
		else
		{
			$month			=	$_GET['month'];
			$year			=	$_GET['year'];

			$displayDate	=	"none";
			$displayMonth	=	"";
			$checked		=	"";
			$checked1		=	"checked";
			$monthText		=	$a_month[$month];
			$text1		   .=	"ON ".$monthText.",".$year;

			$queryString   .=	"&month=".$month."&year=".$year;
			$printLink	   .=	"&month=".$month."&year=".$year;
			$andClause		=	" AND MONTH(assignToEmployee)=".$month." AND YEAR(assignToEmployee)=".$year;
			$andClause1		=	" AND MONTH(replyFileAddedOn)=".$month." AND YEAR(replyFileAddedOn)=".$year;
			$andClause2		=	" AND MONTH(qaDoneOn)=".$month." AND YEAR(qaDoneOn)=".$year;
		}
		if(isset($_GET['employee']))
		{
			$a_employeeId	=	$_GET['employee'];
			if(!empty($a_employeeId))
			{
				$pos = strpos($a_employeeId, ",");
				if($pos === false)
				{
					$n_employeeId[0]	=	$a_employeeId;
				}
				else
				{
					$n_employeeId	=	explode(",",$a_employeeId);
				}
			
				$employeeSearch	=	"&employee=".$a_employeeId;

				$whereClause	   .=	" AND employeeId IN ($a_employeeId)";
				$totalEmloyee	=	count($n_employeeId);
				if($totalEmloyee < 2 && $totalEmloyee > 0)
				{
					foreach($n_employeeId as $key=>$value)
					{
						$employeeName	=	$employeeObj->getEmployeeName($value);
					}
					$text1			.=	" FOR".$employeeName;
				}
				else
				{
					$text1			.=	" FOR MULTILE EMPLOYEE";
				}
				$queryString   .=	"&employee=".$a_employeeId;
				$printLink	   .=	"&employee=".$a_employeeId;
			}
		}
	}
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
		document.getElementById('displayDate').style.display  = 'inline';
		document.getElementById('displayMonth').style.display = 'none';
	}
	else
	{
		document.getElementById('displayDate').style.display  = 'none';
		document.getElementById('displayMonth').style.display = 'inline';
	}
}
</script>
<form name="searchPdfForm" action=""  method="POST" onsubmit="return search();">
	<table cellpadding="0" cellspacing="0" width='100%'align="center" border='0'>
		<tr>
			<td width="40%" class="smalltext2" valign="top"><b>View PDF Employees Worksheet By <input type="radio" name="searchBy" value="1" <?php echo $checked;?> onclick="return showSearch(1)">Date Or By <input type="radio" name="searchBy" value="2" <?php echo $checked1;?> onclick="return showSearch(2)">Month</b></td>
			<td width="2%" class="smalltext2" valign="top"><b>:</b></td>
			<td width="20%" valign="top" class="title1">
				<div  id="displayDate" style="display:<?php echo $displayDate;?>">
					DATE&nbsp;&nbsp;
					<input type="text" name="searchOn" value="<?php echo $searchOn;?>" class="textbox" id="atOn" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('atOn','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
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
			<td width="10%" class="smalltext2" valign="top">
				<b>For Employee</b>
			</td>
			<td width="15%" valign="top">
				<select name="employeeId[]" multiple style="height:100px;">
					<option value="0">All</option>
					<?php
						if($result	=	$employeeObj->getAllPdfEmployees())
						{
							while($row	=	mysql_fetch_assoc($result))
							{
								$t_employeeId	=	$row['employeeId'];
								$firstName		=	$row['firstName'];
								$lastName		=	$row['lastName'];

								$employeeName	=	$firstName." ".$lastName;
								$employeeName	=	ucwords($employeeName);

								$select			=	"";
								if(in_array($t_employeeId, $n_employeeId))
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
			<td colspan="4">&nbsp;</td>
			<td colspan="2" class="smalltext7">
				[Use Ctrl+Select to select multiple employees]
			</td>
		</tr>
	</table>
</form>
<?php
	if($showForm)
	{
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td colspan="2" class='title'>PDF EMPLOYEE WORK DETAILS <?php echo $text1;?></td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
</table>
<br>
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
		$recsPerPage	          =	25;	//	how many records per page
		$showPages		          =	10;	
		$pagingObj->recordNo	  =	$recNo;
		$pagingObj->startRow	  =	$recNo;
		$pagingObj->whereClause   =	$whereClause;
		$pagingObj->recsPerPage   =	$recsPerPage;
		$pagingObj->showPages	  =	$showPages;
		$pagingObj->orderBy		  =	$orderBy;
		$pagingObj->table		  =	"employee_details";
		$pagingObj->selectColumns = "employeeId,firstName,lastName";
		$pagingObj->path		  = SITE_URL_EMPLOYEES. "/pdf-work-report.php";
		$totalRecords = $pagingObj->getTotalRecords();
		if($totalRecords && $recNo <= $totalRecords)
		{
			$showSummary	=	true;
			$pagingObj->setPageNo();
			$recordSet = $pagingObj->getRecords();
	?>
	<table width="100%" border="0" cellpadding="0" cellspacing="3" align="center">
	<tr>
		<td width="5%" class="smalltext2"><b>S.No</b></td>
		<td width="20%" class="smalltext2"><b>Employee Name</b></td>
		<td width="20%" class="smalltext2"><b>Orders Accepted</b></td>
		<td width="20%" class="smalltext2"><b>Orders Processed</b></td>
		<td class="smalltext2"><b>QA Done</b></td>
	</tr>
	<tr>
		<td colspan="5">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
	<?php
			$i=$recNo;
			while($row	=   mysql_fetch_assoc($recordSet))
			{
				$i++;
				$employeeId		=	$row['employeeId'];
				$firstName		=	$row['firstName'];
				$lastName		=	$row['lastName'];

				$employeeName	=	$firstName." ".$lastName;
				$employeeName	=	ucwords($employeeName);

				$acceptedOrders	=	0;
				$totalReplied	=   0;
				$a_pendingIds	=   array();
				$acceptedOrders	=	@mysql_result(dbQuery("SELECT COUNT(*) FROM members_orders WHERE acceptedBy=$employeeId".$andClause),0);
				if(empty($acceptedOrders))
				{
					$acceptedOrders	=	0;
				}
				$query	=	"SELECT orderId FROM members_orders WHERE acceptedBy=$employeeId".$andClause;
				$result	=	dbQuery($query);
				if(mysql_num_rows($result))
				{
					while($row1		=	mysql_fetch_assoc($result))
					{
						$orderId				=	$row1['orderId'];
						$a_pendingIds[$orderId]	=	$orderId;
					}
				}
				if(!empty($a_pendingIds))
				{
					$pendingIds			=	implode(",",$a_pendingIds);

					$totalReplied		=	@mysql_result(dbQuery("SELECT COUNT(replyId) FROM members_orders_reply WHERE hasRepliedFileUploaded=1 AND orderId IN ($pendingIds)"),0);
					if(empty($totalReplied))
					{
						$totalReplied	=	0;
					}
				}  
				
				$totalQaOrders	=	@mysql_result(dbQuery("SELECT COUNT(*) FROM members_orders_reply WHERE hasQaDone=1 AND qaDoneBy=$employeeId".$andClause2),0);
				if(empty($totalQaOrders))
				{
					$totalQaOrders	=	0;
				}
		?>
		<tr>
			<td class="text2"><b><?php echo $i;?>)</b></td>
			<td class="text2"><b><?php echo $employeeName;?></b></td>
			<td class="text2"><b><?php echo $acceptedOrders;?></b></td>
			<td class="text2"><b><?php echo $totalReplied;?></b></td>
			<td class="text2"><b><?php echo $totalQaOrders;?></b></td>
		</tr>
		<tr>
			<td colspan="5">
				<hr size="1" width="100%" color="#e4e4e4">
			</td>
		</tr>
		<?php
			}
			echo "<tr><td colspan='5'><table width='90%' border='0' ><tr height=20><td align=center><font color='#000000'>";
			$pagingObj->displayPaging($queryString);
			echo "</font></b></td></tr></table></td></tr></table>";
		}
	}
	else
	{
		echo "<br><br><center><font class='error'><b>Please submit the above form !!</b></font></center><br><br><br><br><br><br><br><br>";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>
