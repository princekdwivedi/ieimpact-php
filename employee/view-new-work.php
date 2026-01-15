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

	if(isset($_GET['assignedWorkId']))
	{	
		$assignedWorkId		=	(int)$_GET['assignedWorkId'];
		if($result = $employeeObj->getAssignedWorkDetails($s_employeeId,$assignedWorkId))
		{
			if(isset($_GET['isAccept']) && $_GET['isAccept'] == 1)
			{
				mysql_query("UPDATE assign_employee_works SET status=1,acceptedOn='".CURRENT_DATE_INDIA."' WHERE assignedWorkId=$assignedWorkId AND employeeId=$s_employeeId");

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
				header("Location: ".SITE_URL_EMPLOYEES."/view-new-work.php$link");
				exit();
			}
		}
		else
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}
	}
?>
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='heading'>New Works Assigned To You</td>
	</tr>
</table>
<script type="text/javascript">
function acceptWork(assignedWorkId,rec)
{
	var confirmation = window.confirm("Are you sure to accept this work?");
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/view-new-work.php?assignedWorkId="+assignedWorkId+"&rec="+rec+"&isAccept=1";
	}
}
</script>
<?php
	$whereClause	=	"WHERE employeeId=$s_employeeId AND status=0";
	$orderBy		=	"assignedOn DESC";
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
	$recsPerPage	          =	10;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"assign_employee_works";
	$pagingObj->selectColumns = "*";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/view-new-work.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
	?>
		<table width='99%' align='center' cellpadding='0' cellspacing='0' border='0'>
			<tr>
				<td colspan='16'>
					<hr size='1' width='100%' color='#428484'>
				</td>
			</tr>
			<tr>
				<td width='5%' class='text'>Sr.No</td>
				<td width='8%' class='text'>Platform</td>
				<td width='8%' class='text'>Client Name</td>
				<td colspan="2" class='text' align="center">Direct</td>
				<td colspan="2" class='text' align="center">Indirect</td>
				<td colspan="2" class='text' align="center">QA</td>
				<td colspan="2" class='text' align="center">Post Audit</td>
				<td width='5%' class='text' align="center">Total</td>
				<td width='7%' class='text'>File Name</td>
				<td width='15%' class='text'>Comments</td>
				<td width='7%' class='text'>Assigned On</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width='5%' colspan="3">&nbsp;</td>
				<td width='5%' class='text2' align="center">LEVEL1</td>
				<td width='5%' class='text2' align="center">LEVEL2</td>
				<td width='5%' class='text2' align="center">LEVEL1</td>
				<td width='5%' class='text2' align="center">LEVEL2</td>
				<td width='5%' class='text2' align="center">LEVEL1</td>
				<td width='5%' class='text2' align="center">LEVEL2</td>
				<td width='5%' class='text2' align="center">LEVEL1</td>
				<td width='5%' class='text2' align="center">LEVEL2</td>
				<td colspan="5"></td>
			</tr>
			<tr>
				<td colspan='16'>
					<hr size='1' width='100%' color='#428484'>
				</td>
			</tr>
	<?php
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
			$comments			=	$row['comments'];
			$totalLinesAssigned	=	$row['totalLinesAssigned'];
			$assignedOn			=	showDate($row['assignedOn']);
			$uploadedFileName   =	$row['uploadedFileName'];

			$platName		=	$employeeObj->getPlatformName($platform);
			$customerName	=	$employeeObj->getCustomerName($customerId,$platform);
		?>
		<tr>
			<td class="smalltext2" valign="top">
				<?php echo $i.")";?>
			</td>
			<td class="smalltext2" valign="top">
				<?php echo $platName;?>
			</td>
			<td class="smalltext2" valign="top">
				<?php echo $customerName;?>
			</td>
			<td class='smalltext1' valign="top" align="center">
				<?php echo $direct1;?>
			</td>
			<td class='smalltext1' valign="top" align="center">
				<?php echo $direct2;?>
			</td>
			<td class='smalltext1' valign="top" align="center">
				<?php echo $indirect1;?>
			</td>
			<td class='smalltext1' valign="top" align="center">
				<?php echo $indirect2;?>
			</td>
			<td class='smalltext1' valign="top" align="center">
				<?php echo $qa1;?>
			</td>
			<td class='smalltext1' valign="top" align="center">
				<?php echo $qa2;?>
			</td>
			<td class='smalltext1' valign="top" align="center">
				<?php echo $audit1;?>
			</td>
			<td class='smalltext1' valign="top" align="center">
				<?php echo $audit2;?>
			</td>
			<td class='smalltext1' valign="top" align="center">
				<?php echo $totalLinesAssigned;?>
			</td>
			<td class='smalltext1' valign="top">
				<?php echo $uploadedFileName;?>
			</td>
			<td class='smalltext1' valign="top">
				<?php echo $comments;?>
			</td>
			<td class='smalltext1' valign="top">
				<?php echo $assignedOn;?>
			</td>
			<td class='smalltext1' valign="top">
				<a href="javascript:acceptWork(<?php echo $assignedWorkId;?>,<?php echo $recNo;?>)" class="link_style5">Accept It</a>
			</td>
		</tr>
		<tr>
			<td colspan='16'>
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
		echo "<br><center><font class='error'><b>No New Work Assigned To You</b></font></center>";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>