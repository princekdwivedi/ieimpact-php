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
	$a_managers					=	$employeeObj->getAllEmployeeManager();
	$isDisplayingManager		=	0;

	function getPlateformClientsNames($employeeId){
		$clients_names			=	array();
		$query					=	"SELECT employee_clients.*,name FROM employee_clients INNER JOIN platform_clients ON employee_clients.platform=platform_clients.platfromId WHERE employeeId=$employeeId ORDER BY name";
		$result					=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			while($row			=	mysql_fetch_assoc($result))
			{
				$plateformName	=	stripslashes($row['name']);
				$clientId		=	$row['clientId'];
				$platformId		=	$row['platform'];				

				$query1			=	"SELECT name FROM platform_clients WHERE parentId=$platformId AND customerId <> 0 AND customerId IN ($clientId) ORDER BY name";
				$result1		=	mysql_query($query1);
				if(mysql_num_rows($result1))
				{
					$a_clients			=	array();
					while($row1			=	mysql_fetch_assoc($result1))
					{
						$clientName		=	stripslashes($row1['name']);
						$a_clients[]	=	$clientName;
					}
				}
				if(count($a_clients) > 0)
				{
					$clients_names[]	= "<font class='smalltext1'><font color='red'><u>".$plateformName."</u></font>:".implode(", ",$a_clients)."</font>";
				}
				
			}
			
			if(count($clients_names) > 0)
			{
				$clients_names	=  implode(".<br />",$clients_names);
			}
			else
			{
				$clients_names	=	"";
			}
			return $clients_names;
		}
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
	if(isset($_GET['searchEmployee']))
	{
		$searchEmployee			=	$_GET['searchEmployee'];
		if(!empty($searchEmployee))
		{
			
			$andClause		=	" AND fullName LIKE '%$searchEmployee%'";
						
			$queryString	=	"&searchEmployee=".$searchEmployee;
			$searchtext		=	"SEARCHING EMPLOYEE NAME MATCHING CHARACTER WITH - <font color='red'>$searchEmployee</font>";
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
		$a_accuracyClients	=	$_POST['accuracyClients'];

		/*$a_shiftFromHrs   	=	$_POST['shiftFromHrs'];
		$a_shiftFromMin		=	$_POST['shiftFromMin'];
		$a_shiftFromSec		=	$_POST['shiftFromSec'];
		$a_shiftToHrs		=	$_POST['shiftToHrs'];
		$a_shiftToMin		=	$_POST['shiftToMin'];
		$a_shiftToSec		=	$_POST['shiftToSec'];*/

	
		foreach($a_postAudit as $employeeId=>$postAudit)
		{
			$pending		=	$a_pending[$employeeId];
			$commentsAlerts	=	$a_comments[$employeeId];
			$nuanceID		=	$a_nuanceID[$employeeId];
			$fiesaID		=	$a_fiesaID[$employeeId];
			$accuracyClients=	$a_accuracyClients[$employeeId];

			/*$shiftFromHrs	=	$a_shiftFromHrs[$employeeId];
			$shiftFromMin	=	$a_shiftFromMin[$employeeId];
			$shiftFromSec	=	$a_shiftFromSec[$employeeId];
			$shiftToHrs		=	$a_shiftToHrs[$employeeId];
			$shiftToMin		=	$a_shiftToMin[$employeeId];
			$shiftToSec		=	$a_shiftToSec[$employeeId];

			if(empty($shiftFromHrs)){
				$shiftFromHrs	=	"00";
			}
			if(empty($shiftFromMin)){
				$shiftFromMin	=	"00";
			}
			if(empty($shiftFromMin)){
				$shiftFromMin	=	"00";
			}
			if(empty($shiftToHrs)){
				$shiftToHrs		=	"00";
			}
			if(empty($shiftToMin)){
				$shiftToMin		=	"00";
			}
			if(empty($shiftToSec)){
				$shiftToSec		=	"00";
			}

			if(strlen($shiftFromHrs) < 2){
				$shiftFromHrs	=	"0".$shiftFromHrs;
			}
			if(strlen($shiftFromMin) < 2){
				$shiftFromMin	=	"0".$shiftFromMin;
			}
			if(strlen($shiftFromSec) < 2){
				$shiftFromSec	=	"0".$shiftFromSec;
			}
			if(strlen($shiftToHrs) < 2){
				$shiftToHrs  	=	"0".$shiftToHrs;
			}
			if(strlen($shiftToHrs) < 2){
				$shiftToHrs  	=	"0".$shiftToHrs;
			}
			if(strlen($shiftToMin) < 2){
				$shiftToMin  	=	"0".$shiftToMin;
			}
			if(strlen($shiftToSec) < 2){
				$shiftToSec  	=	"0".$shiftToSec;
			}

			$shift_from		=	$shiftFromHrs.":".$shiftFromMin.":".$shiftFromSec;
			$shift_to		=	$shiftToHrs.":".$shiftToMin.":".$shiftToSec;*/

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
			if(!empty($accuracyClients))
			{
				$accuracyClients	=	trim($accuracyClients);
				$accuracyClients	=	makeDBSafe($accuracyClients);
			}
			else
			{
				$accuracyClients	=	"";
			}

			dbQuery("UPDATE employee_details SET postAuditAccuracy='$postAudit',pendingAccuracy='$pending',commentsAlerts='$commentsAlerts',nuanceID='$nuanceID',fiesaID='$fiesaID',accuracyClients='$accuracyClients' WHERE employeeId=$employeeId AND isActive=1");
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
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
</script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" />

<script type='text/javascript'>
	$().ready(function() {
		$("#searchName").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-employee.php", {width: 265,selectFirst: false});
	});
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
		<td colspan="12" class='title'>Add-Edit-View Employees Accuracy Level&nbsp;&nbsp;(<a onclick="openPrintExcelWindow('<?php echo $printUrl;?>','')" class='link_style14' style="cursor:pointer;"><b>DOWNLOAD ALL DATA IN EXCEL</b></a>)</td>
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
			<input type="text" name="searchEmployee" value="<?php echo $searchEmployee;?>" size="35" id="searchName">
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
<table width="98%" border="0" cellpadding="1" cellspacing="1" align="center">
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
		<td colspan='11'>
			<hr size='1' width='100%' color='#428484'>
		</td>
	</tr>
	<tr>
		<td width='2%' class='text'>&nbsp;</td>
		<td width='16%' class='text'>Name</td>
		<td width='6%' class='text'>Leave Today</td>
		<td width='11%' class='text'>Shift Timings</td>
		<td width='15%' class='text'>Clients</td>
		<td width='10%' class='text'>Weekly Off</td>
		<td width='11%' class='text'>Special Shift</td>
		<td width='6%' class='text'>Nuance ID</td>
		<td width='6%' class='text'>Fiesa ID</td>
		<td class='text'>Comments/Alerts</td>
	</tr>
	<tr>
		<td colspan='11'>
			<hr size='1' width='100%' color='#428484'>
		</td>
	</tr>
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
				$accuracyClients		=	stripslashes($row['accuracyClients']);

				$timings				=	showTimeShortFormat($shiftFrom)."-".showTimeShortFormat($shiftTo);

				/*list($shiftFromHrs,$shiftFromMin,$shiftFromSec) = explode(":",$shiftFrom);
				list($shiftToHrs,$shiftToMin,$shiftToSec)	    = explode(":",$shiftTo);*/

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
			<td class='smalltext9' valign="top">
				<?php echo $i.")";?>
			</td>
			<td valign="top" class='smalltext9'>
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
				<!--<input type="text" name="shiftFromHrs[<?php echo $employeeId;?>]" value="<?php echo $shiftFromHrs;?>" style="border:1px solid #333333;width:19px;" maxlength="2" onkeypress="return checkForNumber();">:<input type="text" name="shiftFromMin[<?php echo $employeeId;?>]" value="<?php echo $shiftFromMin;?>"  style="border:1px solid #333333;width:19px;" maxlength="2" onkeypress="return checkForNumber();">:<input type="text" name="shiftFromSec[<?php echo $employeeId;?>]" value="<?php echo $shiftFromSec;?>"  style="border:1px solid #333333;width:19px;" maxlength="2" onkeypress="return checkForNumber();">To<input type="text" name="shiftToHrs[<?php echo $employeeId;?>]" value="<?php echo $shiftToHrs;?>" style="border:1px solid #333333;width:19px;" maxlength="2" onkeypress="return checkForNumber();">:<input type="text" name="shiftToMin[<?php echo $employeeId;?>]" value="<?php echo $shiftToMin;?>" style="border:1px solid #333333;width:19px;" maxlength="2" onkeypress="return checkForNumber();">:<input type="text" name="shiftToSec[<?php echo $employeeId;?>]" value="<?php echo $shiftToSec;?>" style="border:1px solid #333333;width:19px;" maxlength="2" onkeypress="return checkForNumber();">-->
				<?php echo $timings;?>
			</td>
			<td class='smalltext2' valign="top">
				<input type="text" name="accuracyClients[<?php echo $employeeId;?>]" value="<?php echo $accuracyClients;?>" size="28" style="border:1px solid #333333">
			</td>
			<td class='smalltext2' valign="top">
				<input type="text" name="postAudit[<?php echo $employeeId;?>]" value="<?php echo $postAuditAccuracy;?>" size="17" style="border:1px solid #333333">
			</td>
			<td class='smalltext2' valign="top">
				<input type="text" name="pending[<?php echo $employeeId;?>]" value="<?php echo $pendingAccuracy;?>" size="20" style="border:1px solid #333333">
			</td>
			<td class='smalltext2' valign="top">
				<input type="text" name="nuanceID[<?php echo $employeeId;?>]" value="<?php echo $nuanceID;?>" size="10" style="border:1px solid #333333">
			</td>
			<td class='smalltext2' valign="top">
				<input type="text" name="fiesaID[<?php echo $employeeId;?>]" value="<?php echo $fiesaID;?>" size="10" style="border:1px solid #333333">
			</td>
			<td class='smalltext2' valign="top">
				<input type="text" name="commentsAlerts[<?php echo $employeeId;?>]" value="<?php echo $commentsAlerts;?>" size="33" style="border:1px solid #333333">
			</td>
		</tr>
		<tr>
			<td colspan='11' height="1"></td>
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
		echo "<tr><td height='10'></td></tr><tr><td colspan='9'><table width='100%'><tr><td align='right'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr></table></td></tr>";
		echo "</table></form>";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>
