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

	if($s_departmentId		!=	2)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$assignedWorkId			=	0;
?>
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='heading'>Completed Works List</td>
	</tr>
</table>
<?php
	$whereClause	=	"WHERE employeeId=$s_employeeId AND status=2";
	$orderBy		=	"completedOn DESC";
	$queryString	=	"";
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
	$pagingObj->table		  =	"assign_employee_works";
	$pagingObj->selectColumns = "*";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/completed-rev-work.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
?>
<table width='99%' align='center' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<td colspan='15'>
			<hr size='1' width='100%' color='#428484'>
		</td>
	</tr>
	<tr>
		<td width="5%" class='text'>Sr. No</td>
		<td width='10%' class='text'>Platform</td>
		<td width='10%' class='text'>Client Name</td>
		<td colspan="2" class='text' align="center">Direct</td>
		<td colspan="2" class='text' align="center">Indirect</td>
		<td colspan="2" class='text' align="center">QA</td>
		<td colspan="2" class='text' align="center">Post Audit</td>
		<td width='8%' class='text' align="center">Total</td>
		<td width='8%' class='text' align="center">Assigned On</td>
		<td width='8%' class='text'>Completed On</td>
		<td class='text'></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td width='5%' class='text2' align="center">LEVEL1</td>
		<td width='5%' class='text2' align="center">LEVEL2</td>
		<td width='5%' class='text2' align="center">LEVEL1</td>
		<td width='5%' class='text2' align="center">LEVEL2</td>
		<td width='5%' class='text2' align="center">LEVEL1</td>
		<td width='5%' class='text2' align="center">LEVEL2</td>
		<td width='5%' class='text2' align="center">LEVEL1</td>
		<td width='5%' class='text2' align="center">LEVEL2</td>
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td colspan='15'>
			<hr size='1' width='100%' color='#428484'>
		</td>
	</tr>
<?php
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
		$i	=	0;
		while($row	=   mysql_fetch_assoc($recordSet))
		{
			$i++;
			$assignedWorkId		=	$row['assignedWorkId'];
			$platform			=	$row['platform'];
			$customerId			=	$row['customerId'];
			$direct1			=	$row['direct1'];
			$direct2			=	$row['direct2'];
			$indirect1			=	$row['indirect1'];
			$indirect2			=	$row['indirect2'];
			$qa1				=	$row['qa1'];
			$qa2				=	$row['qa2'];
			$audit1				=	$row['audit1'];
			$audit2				=	$row['audit2'];
			$totalLinesAssigned	=	$row['totalLinesAssigned'];
			$assignedOn			=	showDate($row['assignedOn']);
			$completedOn		=	showDate($row['completedOn']);

			$platName		=	$employeeObj->getPlatformName($platform);
			$customerName	=	$employeeObj->getCustomerName($customerId,$platform);
	?>
	<tr>
		<td class='smalltext2'><?php echo $i;?></td>
		<td class='smalltext2'><?php echo $platName;?></td>
		<td class='smalltext2'><?php echo $customerName;?></td>
		<td class='smalltext2' align="center"><?php echo $direct1;?></td>
		<td class='smalltext2' align="center"><?php echo $direct2;?></td>
		<td class='smalltext2' align="center"><?php echo $indirect1;?></td>
		<td class='smalltext2' align="center"><?php echo $indirect1;?></td>
		<td class='smalltext2' align="center"><?php echo $qa1;?></td>
		<td class='smalltext2' align="center"><?php echo $qa2;?></td>
		<td class='smalltext2' align="center"><?php echo $audit1;?></td>
		<td class='smalltext2' align="center"><?php echo $audit2;?></td>
		<td class='smalltext2' align="center"><?php echo $totalLinesAssigned;?></td>
		<td class='smalltext2'><?php echo $assignedOn;?></td>
		<td class='smalltext2'><?php echo $completedOn;?></td>
		<td>
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-rev-work-done.php?ID=<?php echo $assignedWorkId;?>" class="link_style5">View Work Done</a>
			
		</td>
	</tr>
	<tr>
		<td colspan='15'>
			<hr size='1' width='100%' color='#428484'>
		</td>
	</tr>
	<?php
		}
		echo "</table";
		echo "<table width='100%'><tr><td align='right'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr></table>";
	}
	else
	{
		echo "<br><center><font class='error'><b>No Completed Works Available !!</b></font></center>";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>