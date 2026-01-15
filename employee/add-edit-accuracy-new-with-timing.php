<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-mt-employee-login.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=  new employee();
	$pagingObj					=  new Paging();
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	if(isset($_REQUEST['recNo']))
	{
		$recNo		    =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo	=	0;
	}
	$searchEmployee				=	"";
	$searchtext					=	"ALL MT EMPLOYEES";
	$type						=	0;
	$whereClause				=	"WHERE isActive=1";
	$andClause					=	"";
	$andClause1					=	"";
	$orderBy					=	"firstName";
	$queryString				=	"";
	$underManagerOf				=	0;
	$employeeId					=	0;
	$a_managers					=	$employeeObj->getAllEmployeeManager();
	$a_pdfEmployees				=	$employeeObj->getAllMtEmployees();
	$a_employees				=	array();
	$isDisplayingManager		=	0;

	while($row	=   mysql_fetch_assoc($a_pdfEmployees))
	{
		$t_employeeId			=	$row['employeeId'];
		$t_firstName			=	stripslashes($row['firstName']);
		$t_lastName				=	stripslashes($row['lastName']);

		$a_employees[$t_employeeId]	=	$t_firstName." ".$t_lastName;
	}

	if(isset($_GET['type']))
	{
		$type					=	$_GET['type'];
		if(!empty($type))
		{
			if($type			==	1)
			{
				$searchtext		=  "ALL MT EMPLOYEES";
				$andClause		=  " AND employee_shift_rates.departmentId=1 AND employee_details.hasPdfAccess=0";
			}
			else
			{
				$searchtext		=  "ALL REV EMPLOYEES";
				$andClause		=  " AND employee_shift_rates.departmentId=2 AND employee_details.hasPdfAccess=0";
			}
			$queryString		=	"&type=$type";
		}
	}
	if(isset($_GET['employeeId']))
	{
		$employeeId			=	$_GET['employeeId'];
		if(!empty($employeeId))
		{
			
			$andClause		=	" AND employee_details.employeeId=$employeeId";
						
			$queryString	=	"&employeeId=".$employeeId;
			$searchtext		=	"SEARCHING EMPLOYEE NAME - <font color='red'>$a_employees[$employeeId]</font>";
		}
	}
	if(isset($_GET['underManagerOf']))
	{
		$underManagerOf			=	$_GET['underManagerOf'];
		if(!empty($underManagerOf))
		{
			$isDisplayingManager		=	$underManagerOf;
			$managerName	=	$employeeObj->getEmployeeName($underManagerOf);
			$andClause		=	" AND employee_details.underManager=$underManagerOf";
			$queryString	=	"&underManagerOf=".$underManagerOf;
			$searchtext		=	"SEARCHING EMPLOYEES UNDER MANAGER OF - <font color='red'>$managerName</font>";
		}
	}
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		$a_postAudit		=	$_POST['postAudit'];
		$a_pending			=	$_POST['pending'];
		$a_comments			=	$_POST['commentsAlerts'];
		$a_nuanceID			=	$_POST['nuanceID'];
		$a_fiesaID			=	$_POST['fiesaID'];

		$a_shiftFrom   		=	$_POST['shiftFrom'];
		$a_shiftTo			=	$_POST['shiftTo'];

	
		foreach($a_postAudit as $employeeId=>$postAudit)
		{
			$pending		=	$a_pending[$employeeId];
			$commentsAlerts	=	$a_comments[$employeeId];
			$nuanceID		=	$a_nuanceID[$employeeId];
			$fiesaID		=	$a_fiesaID[$employeeId];

			$shift_from		=	$a_shiftFrom[$employeeId].":00";
			$shift_to		=	$a_shiftTo[$employeeId].":00";

			if(!empty($postAudit))
			{
				$postAudit	=	trim($postAudit);
				$postAudit	=	makeDBSafe($postAudit);
			}
			else
			{
				$postAudit	=	"";
			}
			if(!empty($pending))
			{
				$pending	=	trim($pending);
				$pending	=	makeDBSafe($pending);
			}
			else
			{
				$pending	=	"";
			}

			if(!empty($commentsAlerts))
			{
				$commentsAlerts	=	trim($commentsAlerts);
				$commentsAlerts	=	makeDBSafe($commentsAlerts);
			}
			else
			{
				$commentsAlerts	=	"";
			}

			if(!empty($nuanceID))
			{
				$nuanceID	=	trim($nuanceID);
				$nuanceID	=	makeDBSafe($nuanceID);
			}
			else
			{
				$nuanceID	=	"";
			}

			if(!empty($fiesaID))
			{
				$fiesaID	=	trim($fiesaID);
				$fiesaID	=	makeDBSafe($fiesaID);
			}
			else
			{
				$fiesaID	=	"";
			}

			dbQuery("UPDATE employee_details SET postAuditAccuracy='$postAudit',pendingAccuracy='$pending',commentsAlerts='$commentsAlerts',nuanceID='$nuanceID',fiesaID='$fiesaID',shiftFrom='$shift_from',shiftTo='$shift_to' WHERE employeeId=$employeeId AND isActive=1");
		}
		if(!empty($recNo))
		{
			$link		=	"recNo=$recNo";
		}
		else
		{
			$link		=	"";
		}

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/add-edit-accuracy.php?".$link.$queryString);
		exit();
	}

	$printUrl		=	SITE_URL_EMPLOYEES."/print-employee-accuracy-data.php?isDisplayingManager=".$isDisplayingManager;
?>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL_EMPLOYEES;?>/css/bootstrap.min.css">

<!-- ClockPicker Stylesheet -->
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL_EMPLOYEES;?>/css/bootstrap-clockpicker.min.css">

<script type='text/javascript'>
	function search()
	{
		form1	=	document.searchForm;
		if(form1.searchEmployee.value == "")
		{
			alert("Please Enter Name !!");
			form1.searchEmployee.focus();
			return false;
		}
	}
	function openPrintExcelWindow(pageUrl,extra)
	{
		path = pageUrl+extra;
		prop = "toolbar=no,scrollbars=yes,width=400,height=100,top=200,left=300";
		window.open(path,'',prop);
	}
	function checkForNumber()
	{
		k = (document.all)?event.keyCode : arguments.callee.caller.arguments[0].which;
		if(k == 8 || k== 0)
		{
			return true;
		}
		if(k >= 48 && k <= 57 )
		{
			return true;
		}
		else if(k == 46)
		{
			return true;
		}
		else
		{
			return false;
		}
	 }
</script>
<form name="searchForm" action=""  method="GET" onsubmit="return search();">
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td colspan="12" class='smalltext23'><b>Add-Edit-View Employees Accuracy Level</b>&nbsp;&nbsp;(<a onclick="openPrintExcelWindow('<?php echo $printUrl;?>','')" class='link_style14' style="cursor:pointer;"><b>DOWNLOAD ALL DATA IN EXCEL</b></a>)</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<!--<td width="12%">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/add-edit-accuracy.php?type=1" class="link_style14">ALL MT EMPLOYEES</a>
		</td>
		<td width="2%" align="center" class="smalltext2">
			<b>OR</b>
		</td>
		<td width="13%">&nbsp;
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/add-edit-accuracy.php?type=2" class="link_style14">ALL REV EMPLOYEES</a>
		</td>
		<td width="2%" align="center" class="smalltext2">
			<b>OR</b>
		</td>-->
		<td width="14%" class="textstyle1">
			&nbsp;Search An Employee
		</td>
		<td width="20%">
			<select name="employeeId">
				<option value="">All</option>
				<?php
					foreach($a_employees as $key=>$value)
					{
						$select	=	"";
						if($employeeId	==	$key)
						{
							$select	=	"selected";
						}

						echo "<option value='$key' $select>$value</option>";
					}
				?>
			</select>
		</td>
		<td width="2%" align="center" class="smalltext2">
			<b>OR</b>
		</td>
		<td width="9%" class="textstyle1">Under Manger</td>
			<td width="19%" valign="top">
				<select name="underManagerOf">
					<option value="">All</option>
					<?php
						foreach($a_managers as $key=>$value)
						{
							$select	=	"";
							if($underManagerOf	==	$key)
							{
								$select	=	"selected";
							}

							echo "<option value='$key' $select>$value</option>";
						}
					?>
				</select>
			</td>
		<td>
			<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
			<input type='hidden' name='searchFormSubmit' value='1'>
		</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td colspan="12" class='textstyle1'><b><?php echo $searchtext;?></b></td>
	</tr>
</table>
</form>
<form name="searchForm" action="" method="POST">
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
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
	$pagingObj->table		  =	"employee_details INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId";
	$pagingObj->selectColumns = "*";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/add-edit-accuracy.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
	?>
	<tr>
		<td width='2%' class='smalltext23'>&nbsp;</td>
		<td width='15%' class='smalltext23'>Name</td>
		<td width='4%' class='smalltext23'>Leave</td>
		<td width='8%' class='smalltext23'>Shift From</td>
		<td width='8%' class='smalltext23'>Shift To</td>
		<td width='12%' class='smalltext23'>Weekly Off</td>
		<td width='14%' class='smalltext23'>Special Shift</td>
		<td width='8%' class='smalltext23'>Nuance ID</td>
		<td width='8%' class='smalltext23'>Fiesa ID</td>
		<td class='smalltext23'>Comments/Alerts</td>
	</tr>
	<tr><td height="2" colspan="11" style="background-color:#bebebe;"></td></tr>
	<?php
			$i	=	$recNo;
			while($row	=   mysql_fetch_assoc($recordSet))
			{
				$i++;
				$employeeId				=	$row['employeeId'];
				$firstName				=	stripslashes($row['firstName']);
				$lastName				=	stripslashes($row['lastName']);
				$hasPdfAccess			=	$row['hasPdfAccess'];
				$postAuditAccuracy 		=	stripslashes($row['postAuditAccuracy']);
				$pendingAccuracy		=	stripslashes($row['pendingAccuracy']);
				$commentsAlerts			=	stripslashes($row['commentsAlerts']);
				$nuanceID				=	stripslashes($row['nuanceID']);
				$fiesaID				=	stripslashes($row['fiesaID']);
				$shiftFrom				=	stripslashes($row['shiftFrom']);
				$shiftTo				=	stripslashes($row['shiftTo']);
				$timings				=	$shiftFrom." - ".$shiftTo;

				list($shiftFromHrs,$shiftFromMin,$shiftFromSec) = explode(":",$shiftFrom);
				list($shiftToHrs,$shiftToMin,$shiftToSec)	    = explode(":",$shiftTo);

				$shiftFrom				=	$shiftFromHrs.":".$shiftFromMin;
				$shiftTo				=	$shiftToHrs.":".$shiftToMin;

				if($hasPdfAccess        ==  1)
				{
					$departmentText		=	"PDF";
				}
				else
				{
					$departmentId		=	@mysql_result(dbQuery("SELECT departmentId FROM employee_shift_rates WHERE employeeId=$employeeId"),0);
					if($departmentId	==	1)
					{
						$departmentText	=	"MT";
					}
					elseif($departmentId==	2)
					{
						$departmentText	=	"REV";
					}
				}

				$employeeName			=	$firstName." ".$lastName;
				$employeeName			=	ucwords($employeeName);
				$onLeaveText			=	"<font color='#008A45'><b>No</b></font>";
				
				$onLeave				=	@mysql_result(dbQuery("SELECT onLeave FROM employee_attendence WHERE attendenceId > ".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID." AND employeeId=$employeeId AND loginDate='".$nowDateIndia."'"),0);
				if($onLeave				==	1)
				{
					$onLeaveText		=	"<font color='#ff000'><b>Yes</b></font>";
				}
				elseif($onLeave			==	2)
				{
					$onLeaveText		=	"<font color='#000000'><b>H.Day</b></font>";
				}
		?>
		<tr>
			<td valign="top" class='smalltext2'>					
				<?php echo $i;?>)
			</td>
			<td valign="top">
				<?php
					echo $employeeName;
				?>
			</td>
			<td class='error' valign="top">
				<?php
					echo $onLeaveText;
				?>
			</td>
			<td valign="top" class='smalltext2'>					
				<div class="input-group clockpicker">
					<input type="text" class="form-control" name="shiftFrom[<?php echo $employeeId;?>]" value="<?php echo $shiftFrom;?>" readonly>
				</div>
			</td>
			<td>
				<div class="input-group clockpicker">
					<input type="text" class="form-control" name="shiftTo[<?php echo $employeeId;?>]" value="<?php echo $shiftTo;?>" readonly>
				</div>
			</td>
			<td class='smalltext2' valign="top">
				<input type="text" name="postAudit[<?php echo $employeeId;?>]" value="<?php echo $postAuditAccuracy;?>" size="17" style="border:1px solid #333333;height:30px;font-size:13px;">
			</td>
			<td class='smalltext2' valign="top">
				<input type="text" name="pending[<?php echo $employeeId;?>]" value="<?php echo $pendingAccuracy;?>" size="20" style="border:1px solid #333333;height:30px;font-size:13px;">
			</td>
			<td class='smalltext2' valign="top">
				<input type="text" name="nuanceID[<?php echo $employeeId;?>]" value="<?php echo $nuanceID;?>" size="10" style="border:1px solid #333333;height:30px;font-size:13px;">
			</td>
			<td class='smalltext2' valign="top">
				<input type="text" name="fiesaID[<?php echo $employeeId;?>]" value="<?php echo $fiesaID;?>" size="10" style="border:1px solid #333333;height:30px;font-size:13px;">
			</td>
			<td class='smalltext2' valign="top">
				<input type="text" name="commentsAlerts[<?php echo $employeeId;?>]" value="<?php echo $commentsAlerts;?>" size="35" style="border:1px solid #333333;height:30px;font-size:13px;">
			</td>
		</tr>
		<tr>
			<td colspan='11'>
				<hr size="1" width="100%" color="#bebebe">
			</td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td colspan="6">
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
		<?php
		echo "<tr><td height='25'></td></tr><tr><td colspan='9'><table width='100%'><tr><td align='right'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr></table></td></tr>";
		echo "</table></form>";
	}
?>
<!-- jQuery and Bootstrap scripts -->
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/bootstrap.min.js"></script>

<!-- ClockPicker script -->
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/bootstrap-clockpicker.min.js"></script>

<script type="text/javascript">
$('.clockpicker').clockpicker()
    .find('input').change(function(){
        // TODO: time changed
        console.log(this.value);
    });
$('#demo-input').clockpicker({
    autoclose: true
});

if (something) {
    // Manual operations (after clockpicker is initialized).
    $('#demo-input').clockpicker('show') // Or hide, remove ...
            .clockpicker('toggleView', 'minutes');
}
</script>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>
