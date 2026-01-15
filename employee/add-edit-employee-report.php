<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT	."/classes/pagingclass.php");
	$pagingObj					=	new Paging();
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
	$searchDate			=	date("d-m-Y");
	$t_searchDate		=	date("Y-m-d");
	$searchEmployee		=	"";
	$searchingText		=	"";
	$whereClause		=	"";
	$queryString		=	"";
	$orderBy			=	"firstName";
	$t_searchEmployee	=   "";
	$firstName			=	"";
	$lastName			=	"";
	if(isset($_REQUEST['searchEmployee']) && isset($_REQUEST['searchDate']))
	{
		extract($_REQUEST);
		list($day,$month,$year)		=	explode("-",$searchDate);
		$t_searchDate				=	$year."-".$month."-".$day;
		if(!empty($searchEmployee))
		{
			$whereClause	 =	"WHERE employee_shift_rates.departmentId=1 AND fullName LIKE '%$searchEmployee%'";
			
			$t_searchEmployee=   $searchEmployee;
						
			$queryString	=	"&searchEmployee=".$searchEmployee."&searchDate=".$t_searchDate;
			$searchingText	=	"SEARCHING EMPLOYEE NAME - ".$searchEmployee;;
		}
	}
	if(isset($_GET['ID']) && isset($_GET['search']) && isset($_GET['date']))
	{
		$search					=	$_GET['search'];
		$datewiseID				=	$_GET['ID'];
		$date					=	$_GET['date'];
		$search					=	trim($search);
		list($day,$month,$year)	=	explode("-",$date);
		$t_date					=	$year."-".$month."-".$day;
		if(isset($_GET['isDelete']) && $_GET['isDelete'] == 1)
		{
			$employeeObj->deleteMtEmployeeTarget($datewiseID);
			dbQuery("DELETE FROM datewise_employee_works_money WHERE ID=$datewiseID");
		}
		$rec			=	$_GET['rec'];
		if(!empty($rec))
		{
			$link		=	"?recNo=$rec&";
		}
		else
		{
			$link		=	"?";
		}
		ob_clean();
		header("Location:".SITE_URL_EMPLOYEES."/add-edit-employee-report.php".$link."searchEmployee=".$search."&searchDate=".$date);
		exit();
	}
?>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/validate.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>

<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
</script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" />

<script type='text/javascript'>
$().ready(function() {
	$("#searchName").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-mt-employee.php", {width: 325,selectFirst: false});
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
function deleteWork(ID,rec,search,date)
{
	var confirmation = window.confirm("Are you sure to delete this work?");
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/add-edit-employee-report.php?ID="+ID+"&rec="+rec+"&search="+search+"&date="+date+"&isDelete=1";
	}
}
</script>

<form name="searchForm" action=""  method="GET" onsubmit="return search();">
	<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
		<tr>
			<td width="15%" class="smalltext2"><b>VIEW WORK ON </b></td>
			<td width="2%" class="smalltext2"><b>:</b></td>
			<td width="12%">
				<input type="text" name="searchDate" value="<?php echo $searchDate;?>" class="textbox" id="atOn" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('atOn','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
			</td>
			<td width="18%" class="smalltext2"><b>FOR EMPLOYEE NAME</b></td>
			<td width="35%">
				<input type='text' name="searchEmployee" size="50" value="<?php echo $searchEmployee;?>" id="searchName">
			</td>
			<td>
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
	</table>
</form>
<br>
<?php
	if(!empty($searchEmployee))
	{
?>
<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
<tr>
	<td colspan="6" class="title">
		<?php echo $searchingText;?>
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
		$recsPerPage	          =	20;	//	how many records per page
		$showPages		          =	10;	
		$pagingObj->recordNo	  =	$recNo;
		$pagingObj->startRow	  =	$recNo;
		$pagingObj->whereClause   =	$whereClause;
		$pagingObj->recsPerPage   =	$recsPerPage;
		$pagingObj->showPages	  =	$showPages;
		$pagingObj->orderBy		  =	$orderBy;
		$pagingObj->table		  =	"employee_details INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId";
		$pagingObj->selectColumns = "employee_details.*";
		$pagingObj->path		  = SITE_URL_EMPLOYEES. "/add-edit-employee-report.php";
		$totalRecords = $pagingObj->getTotalRecords();
		if($totalRecords && $recNo <= $totalRecords)
		{
			$pagingObj->setPageNo();
			$recordSet = $pagingObj->getRecords();
	?>
		<tr>
			<td colspan='6'>
				<hr size='1' width='100%' color='#428484'>
			</td>
		</tr>
		<tr>
			<td width='5%' class='text'>Sr.No</td>
			<td width='18%' class='text'>Name</td>
			<td width='18%' class='text'>Email</td>
			<td width='25%' class='text'>Address</td>
			<td class='text' width="20%">Mobile</td>
		</tr>
		<tr>
			<td colspan='6'>
				<hr size='1' width='100%' color='#428484'>
			</td>
		</tr>
	<?php
		$i	=	0;
		while($row	=   mysql_fetch_assoc($recordSet))
		{
			$i++;
			$employeeId				=	$row['employeeId'];
			$employeeName			=	$row['fullName'];
			$address				=	$row['address'];
			$email					=	$row['email'];
			$mobile					=	$row['mobile'];
			
	?>
	<tr>
			<td class="text2" valign="top">
				<?php echo $i.")";?>
			</td>
			<td class="text2" valign="top">
				<?php
					echo $employeeName;
				?>
			</td>
			<td class="text2" valign="top">
				<?php
					echo $email;
				?>
			</td>
			<td class="text2" valign="top">
				<?php
					echo $address;
				?>
			</td>
			<td class="text2" valign="top">
				<?php
					echo $mobile;
				?>
			</td>
			<td class="text2" valign="top">
				<a href="<?php echo SITE_URL_EMPLOYEES?>/view-employee-work.php?ID=<?php echo $employeeId;?>&date=<?php echo $searchDate;?>" class='link_style2'>ADD NEW WORK</a>
			</td>
		</tr>
		<?php
			$query1	=	"SELECT ID,platform,customerId FROM datewise_employee_works_money WHERE employeeId=$employeeId AND workedOnDate='$t_searchDate' ORDER BY ID DESC";	
			$result1=	mysql_query($query1);
			if(mysql_num_rows($result1))
			{
		?>
		<tr>
			<td colspan='6'>
				<hr size='1' width='100%' color='#428484'>
			</td>
		</tr>
		<tr>
			<td colspan="6" class="smalltext2">
				<b>ALREADY DONE WORK IN <?php echo showDate($t_searchDate);?></b>
			</td>
		</tr>
		<tr>
			<td colspan='6'>
				<hr size='1' width='100%' color='#428484'>
			</td>
		</tr>
		<tr>
			<td colspan='2' class="smalltext2">
				<b>PLATFORM</b>
			</td>
			<td class="smalltext2">
				<b>CUSTOMER</b>
			</td>
			<td class="smalltext2" colspan='3'>
				<b>ACTION</b>
			</td>
		</tr>
		<tr>
			<td colspan='6'>
				<hr size='1' width='100%' color='#428484'>
			</td>
		</tr>
		<?php
				while($row1	=	mysql_fetch_assoc($result1))
				{
					$datewiseID		=	$row1['ID'];
					$platform		=	$row1['platform'];
					$customerId		=	$row1['customerId'];

					$platName		=	$employeeObj->getPlatformName($platform);
					$customerName	=	$employeeObj->getCustomerName($customerId,$platform);
		?>
		<tr>
			<td colspan='2' class="text2">
				<?php echo $platName;?>
			</td>
			<td class="text2">
				<?php echo $customerName;?>
			</td>
			<td class="smalltext2" colspan='3'>
				<a href="<?php echo SITE_URL_EMPLOYEES?>/view-employee-work.php?ID=<?php echo $employeeId;?>&date=<?php echo $searchDate;?>&datewiseID=<?php echo $datewiseID;?>" class='link_style2'>Edit</a> | <a onclick="deleteWork(<?php echo $datewiseID;?>,<?php echo $recNo?>,'<?php echo $t_searchEmployee;?>','<?php echo $searchDate;?>')" class='link_style2' style="cursor:pointer">DELETE</a>
			</td>
		</tr>
		<tr>
			<td colspan='6'>
				<hr size='1' width='100%' color='#428484'>
			</td>
		</tr>
		<?php

				}
			}
		?>
		<tr>
			<td colspan="6">
				<hr size="1" width="100%" color="#428484">
			</td>
		</tr>
	<?php
		  }
	?>
		<tr>
			<td colspan="6">
				<?php
					echo "<table width='100%'><tr><td align='right'>";
					$pagingObj->displayPaging($queryString);
					echo "&nbsp;&nbsp;</td></tr></table>";
				?>
			</td>
		</tr>
	<?php
		}
		else
		{
			echo "<tr><td colspan='3' class='error' align='center'><b>NO EMPLOYEE AVAILABLE</b></td></tr>";
			echo "<tr><td colspan='5' height='100'></td></tr>";
		}
	
	   echo "</table>";
	}
	else
	{
		echo "<br><center><font class='error'><b>Please Select An Employee !!</b></font></center><br><br><br>";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>