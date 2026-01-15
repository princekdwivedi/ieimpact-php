<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	include(SITE_ROOT_EMPLOYEES	."/includes/common-array.php");
	$employeeObj                = new employee();
	$pagingObj			        = new Paging();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$formHeaderText		=   "View Assigned Work";
	$forDate			=	"";
	$toDate				=	"";
	$employeeId			=	0;
	$a_employeeId		=	array();
	$whereClause		=	"WHERE status != 2";
	$orderBy			=	"assignedOn DESC";
	$queryString		=	"";
	$andClause			=	"";

	if(isset($_GET['assignedWorkId']))
	{
		$assignedWorkId	=	$_GET['assignedWorkId'];
		$isDelete		=	$_GET['isDelete'];
		if($isDelete	==	1)
		{
			mysql_query("DELETE FROM assign_employee_works WHERE assignedWorkId=$assignedWorkId AND status=0");
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
		header("Location: ".SITE_URL_EMPLOYEES."/manage-assign-work.php$link");
		exit();
	}
	if(isset($_REQUEST['forDate']))
	{
		$forDate			=	$_GET['forDate'];
	}
	if(isset($_REQUEST['toDate']))
	{
		$toDate				=	$_GET['toDate'];
	}
	if(isset($_GET['employeeId']))
	{
		$a_employeeId[]	=	$_GET['employeeId'];
	}
	if(isset($_POST['employeeId']))
	{
		$a_employeeId			=	$_POST['employeeId'];
	}
	if(!empty($forDate))
	{
		list($day,$month,$year)		=	explode("-",$forDate);
		$t_forDate		=	$year."-".$month."-".$day;

		$andClause		=	" AND assignedOn='$t_forDate'";
		$queryString	=	"&forDate=".$forDate;
		if(!empty($toDate))
		{
			list($day,$month,$year)		=	explode("-",$toDate);
			$t_toDate		=	$year."-".$month."-".$day;

			$andClause		=	" AND assignedOn >= '$t_forDate' AND assignedOn <= '$t_toDate'";
			$queryString	=	"&forDate=".$forDate."&toDate=".$toDate;
		}
	}
	if(!empty($a_employeeId))
	{
		if(!in_array("0",$a_employeeId))
		{
			$searchEmployee	=	implode(",",$a_employeeId);

			$andClause	   .=	" AND employeeId IN ($searchEmployee)";
		}
	}

	$form	=  SITE_ROOT_EMPLOYEES."/forms/date-rev-employee.php";
?>
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='heading'>View All Un-Accepted And Pending Works</td>
	</tr>
</table>
<?php
	include($form);
?>
<script type="text/javascript">
function deleteWork(assignedWorkId,rec)
{
	var confirmation = window.confirm("Are you sure to delete this assigned work?");
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/manage-assign-work.php?assignedWorkId="+assignedWorkId+"&rec="+rec+"&isDelete=1";
	}
}
</script>
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
	$recsPerPage	          =	50;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"assign_employee_works";
	$pagingObj->selectColumns = "*";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/manage-assign-work.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
	?>
		<table width='99%' align='center' cellpadding='0' cellspacing='0' border='0'>
			<tr>
				<td colspan='12'>
					<hr size='1' width='100%' color='#428484'>
				</td>
			</tr>
			<tr>
				<td width='5%' class='text'>Sr.No</td>
				<td width='13%' class='text'>Employee</td>
				<td width='9%' class='text'>Platform</td>
				<td width='9%' class='text'>Client</td>
				<td width='9%' class='text'>Direct</td>
				<td width='9%' class='text'>Indirect</td>
				<td width='9%' class='text'>QA</td>
				<td width='9%' class='text'>Post Audit</td>
				<td width='10%' class='text'>File Name</td>
				<td width='9%' class='text'>Assigned On</td>
				<td width='9%' class='text'>Status</td>
				<td></td>
			</tr>
			<tr>
				<td colspan='12'>
					<hr size='1' width='100%' color='#428484'>
				</td>
			</tr>
	<?php
		$i	=	0;
		while($row	=   mysql_fetch_assoc($recordSet))
		{
			$i++;
			$assignedWorkId				=	$row['assignedWorkId'];
			$platform					=	$row['platform'];
			$customerId					=	$row['customerId'];
			$employeeId					=	$row['employeeId'];
			
			$direct1					=	$row['direct1'];
			$direct2					=	$row['direct2'];
			$indirect1					=	$row['indirect1'];
			$indirect2					=	$row['indirect2'];
			$qa1						=	$row['qa1'];
			$qa2						=	$row['qa2'];
			$audit1						=	$row['audit1'];
			$audit2						=	$row['audit2'];
			$uploadedFileName			=	$row['uploadedFileName'];

			$status						=	$row['status'];

			$directTotal				=	$direct1+$direct2;
			$indirectTotal				=	$indirect1+$indirect2;
			$qaTotal					=	$qa1+$qa2;
			$auditTotal					=	$audit1+$audit2;

			$assignedOn					=	showDate($row['assignedOn']);

			$platName		=	$employeeObj->getPlatformName($platform);
			$customerName	=	$employeeObj->getCustomerName($customerId,$platform);
			$employeeName	=	$employeeObj->getEmployeeName($employeeId);
		?>
		<tr>
			<td class="smalltext2" valign="top">
				<?php echo $i.")";?>
			</td>
			<td class="smalltext2" valign="top">
				<?php echo $employeeName;?>
			</td>
			<td class="smalltext2" valign="top">
				<?php echo $platName;?>
			</td>
			<td class="smalltext2" valign="top">
				<?php echo $customerName;?>
			</td>
			<td class="smalltext2" valign="top">
				<?php echo $directTotal;?>
			</td>
			<td class="smalltext2" valign="top">
				<?php echo $indirectTotal;?>
			</td>
			<td class="smalltext2" valign="top">
				<?php 
					echo $qaTotal;
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php 
					echo $auditTotal;
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php 
					echo $uploadedFileName;
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php 
					echo $assignedOn;
				?>
			</td>
			<td valign="top" class="smalltext2">
				<?php
					if($status == 0)
					{
						echo "<a href='".SITE_URL_EMPLOYEES."/assign-work.php?ID=$assignedWorkId' class='link_style6'>Edit</a> | <a href='javascript:deleteWork($assignedWorkId,$recNo)' class='link_style6'>Delete</a>";
					}
					if($status == 1)
					{
						echo "<font class='smalltext2'>Accepted | </font>&nbsp;<a href='".SITE_URL_EMPLOYEES."/assign-work.php?ID=$assignedWorkId' class='link_style6'>Edit</a>";
					}


				?>
			</td>
		</tr>
		<tr>
			<td colspan='12'>
				<hr size='1' width='100%' color='#428484'>
			</td>
		</tr>
	<?php
		}
	?>
	</table>
	<?php
		echo "<table width='100%'><tr><td align='right'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr></table>";
	}
	else
	{
		echo "<br><center><font class='error'><b>No Works Assigned Yet</b></font></center>";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>
