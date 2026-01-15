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
	$pagingObj					=	new Paging();

	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	if(isset($_REQUEST['recNo']))
	{
		$recNo					=	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo					=	0;
	}
	$whereClause				=	"WHERE employeeId <> 0";
	$andClause					=	"";
	$andClause1					=	"";
	$queryString				=	"";
	$orderBy					=	"employeeName";
	list($currentY,$currentM,$currentD)	=	explode("-",$nowDateIndia);
	$serachForMonth				=   $currentM;
	$serachForYear				=   $currentY;

	if(isset($_GET['serachForMonth']))
	{
		$serachForMonth			=	$_GET['serachForMonth'];
	}
	if(isset($_GET['serachForYear']))
	{
		$serachForYear			=	$_GET['serachForYear'];
	}

	$monthText					=	$a_month[$serachForMonth];

	$a_mtEmployees				=	array();

	if($result					=	$employeeObj->getAllMtEmployees())
	{
		while($row				=	mysql_fetch_assoc($result))
		{
			$t_employeeId		=	$row['employeeId'];
			$t_firstName		=	stripslashes($row['firstName']);
			$t_lastName			=	stripslashes($row['lastName']);

			$a_mtEmployees[$t_employeeId] = $t_firstName." ".$t_lastName;
		}
	}

	$nonLeadingZeroMonth		=	$serachForMonth;
	if($serachForMonth < 10 && strlen($serachForMonth) > 1)
	{
		$nonLeadingZeroMonth	=	substr($serachForMonth,1);
	}
	$andClause					=	" AND month=$nonLeadingZeroMonth AND year=$serachForYear";
	$queryString				=	"&serachForMonth=".$serachForMonth."&serachForYear=".$serachForYear;
	if(isset($_GET['searchEmployeeId']))
	{
		$searchEmployeeId		=	(int)$_GET['searchEmployeeId'];
		if(!empty($searchEmployeeId))
		{
			$andClause1			=	" AND employeeId=$searchEmployeeId";
			$queryString	   .=   "&searchEmployeeId=$searchEmployeeId";
		}
	}
?>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class="textstyle1"><b>VIEW MT EMPLOYEES MONTHLY WORKSHEET ADMIN UPDATED</b></td>
	</tr>
	<tr>
		<td><b>(<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-monthly-worksheet.php">VIEW ADMIN ADDED LINES</a>)</b></td>
	</tr>
</table>
<br />
<form name="serachLines" action="" method="GET">
	<table width="98%" align="center" border="0" cellpadding="3" cellspacing="3">
		<tr>
			<td width="20%" class="smalltext2"><b>SEARCH RECORDS FOR</b></td>
			<td width="2%" class="smalltext2"><b>:</b></td>
			<td class="smalltext2" width="20%">
				<select name="serachForMonth">
					<option value="0">Month</option>
					<?php
						foreach($a_month as $key=>$value)
						{
							$select		   =	"";
							if($key		   ==   $serachForMonth)
							{
								$select	   =	"selected";
							}
							echo "<option value='$key' $select>$value</option>";
						}
					?>
				</select>&nbsp;
				<select name="serachForYear">
					<option value="0">Year</option>
					<?php
						$fromoDate		   =   date('Y')-1;
						$toDate			   =   date('Y');
						for($i=$fromoDate;$i<=$toDate;$i++)
						{
							$select		   =	"";
							if($i		   ==   $serachForYear)
							{
								$select	   =	"selected";
							}
							echo "<option value='$i' $select>$i</option>";
						}
					?>
				</select>
			</td>
			<td width="10%" class="smalltext2"><b>EMPLOYEE</b></td>
			<td width="2%" class="smalltext2"><b>:</b></td>
			<td class="smalltext2" width="20%">
				<select name="searchEmployeeId">
					<option value="0">Select</option>
					<?php
						foreach($a_mtEmployees as $key=>$value)
						{
							$select		   =	"";
							if($key		   ==   $searchEmployeeId)
							{
								$select	   =	"selected";
							}
							echo "<option value='$key' $select>$value</option>";
						}
					?>
				</select>
			</td>
			<td>
				<input type="image" name="submit" src="<?php echo SITE_URL;?>/images/submit.jpg">
				<input type="hidden" name="formSubmitted" value="1">
			</td>
		</tr>
	</table>
</form>
<?php
	$start					  =	0;
	$recsPerPage	          =	50;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause.$andClause1;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"mt_employee_excel_csv_data";
	$pagingObj->selectColumns = "*";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/view-mt-employee-excel-data.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
		$i		   = $recNo;
?>
	<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
		<tr height='25' class='rwcolor'>
			<td width="4%" class="smalltext12">
				&nbsp;<b>SR NO</b>
			</td>
			<td width="20%" class="smalltext12">
				<b>EMPLOYEE NAME</b>
			</td>
			<td width="10%" class="smalltext12">
				<b>MONTH/YEAR</b>
			</td>
			<td width="13%" class="smalltext12">
				 <b>EDITED DIRECT</b>
			</td>
			<td width="13%" class="smalltext12">
				<b>EDITED INDIRECT</b>
			</td>
			<td width="13%" class="smalltext12">
				 <b>TYPED DIRECT</b> 
			</td>
			<td class="smalltext12" width="10%">
				<b>TYPED INDIRECT</b>
			</td>
		</tr>
		<?php
			while($row					=   mysql_fetch_assoc($recordSet))
			{
				$i++;
				$ID						=	$row['ID'];
				$employeeName			=	stripslashes($row['employeeName']);
				$employeeId				=	$row['employeeId'];
				$month					=	$row['month'];
				$year					=	$row['year'];
				$editedDirect			=	$row['editedDirect'];
				$editedIndirect			=	$row['editedIndirect'];
				$typedDirect			=	$row['typedDirect'];
				$typedIndirect			=	$row['typedIndirect'];

				$bgColor				=	"class='rwcolor1'";
				if($i%2==0)
				{
					$bgColor			=	"class='rwcolor2'";
				}
		?>
		<tr height='25' <?php echo $bgColor;?>>
			<td class="smalltext1" valign="top">
				&nbsp;<?php echo $i.")";?>
			</td>
			<td class="smalltext2" valign="top">
				<b><?php echo $employeeName;?></b>
			</td>
			<td class="smalltext2" valign="top">
				<b><?php echo $monthText."/".$year;?></b>
			</td>
			<td class="smalltext2" valign="top">
				<b><?php echo $editedDirect;?></b>
			</td>
			<td class="smalltext2" valign="top">
				<b><?php echo $editedIndirect;?></b>
			</td>
			<td class="smalltext2" valign="top">
				<b><?php echo $typedDirect;?></b>
			</td>
			<td class="smalltext2" valign="top">
				<b><?php echo $typedIndirect;?></b>
			</td>
		</tr>
		<?php
			}
			echo "<tr><td align='right' colspan='8'>";
			$pagingObj->displayPaging($queryString);
			echo "&nbsp;&nbsp;</td></tr>";	
		?>
	</table>
	</form>
<?php

	}
	else
	{
		echo "<table width='80%' align='center'><tr><td height='200' align='center' class='error'><b>No Record Found.</b></td></tr></table>";
	}

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>