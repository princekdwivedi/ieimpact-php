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
	$pagingObj			        =   new Paging();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");

	$leaveId					=	0;
	if(isset($_GET['leaveId']) && isset($_GET['isDelete']) && $_GET['isDelete'] == 1)
	{
		$leaveId	=	$_GET['leaveId'];
		dbQuery("DELETE FROM employee_leave_applied WHERE leaveId=$leaveId AND employeeId=$s_employeeId AND approvedStatus=0");

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES ."/view-leave-status.php");
		exit();
	}
?>
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='heading'>VIEW LEAVE DETAILS APPLIED ONLINE</td>
	</tr>
</table>
<script type="text/javascript">
function deleteLeave(leaveId)
{
	var confirmation = window.confirm("Are you sure to delete this Leave?");
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/view-leave-status.php?leaveId="+leaveId+"&isDelete=1";
	}
}
</script>
<?php
	$whereClause	=	"WHERE employeeId=$s_employeeId";
	$orderBy		=	"appliedOn DESC";
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
	$recsPerPage	          =	50;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"employee_leave_applied ";
	$pagingObj->selectColumns = "*";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/view-leave-status.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
	?>
		<table width='99%' align='center' cellpadding='0' cellspacing='0' border='0'>
			<tr>
				<td colspan='10'>
					<hr size='1' width='100%' color='#428484'>
				</td>
			</tr>
			<tr>
				<td width='4%' class='text'>Sr.No</td>
				<td width='7%' class='text'>Leave Type</td>
				<td width='6%' class='text'>Leave For</td>
				<td width='8%' class='text'>Applied On</td>
				<td width='9%' class='text'>For/From Date</td>
				<td width='8%' class='text'>To Date</td>
				<td width='20%' class='text'>Reason</td>
				<td width='20%' class='text'>Approved/Reject Reason</td>
				<td width='10%' class='text'>Status</td>
				<td class='text'>Action</td>
			</tr>
			<tr>
				<td colspan='10'>
					<hr size='1' width='100%' color='#428484'>
				</td>
			</tr>
		<?php
			$i	=	0;
			while($row	=   mysqli_fetch_assoc($recordSet))
			{
				$i++;
				$leaveId			=	$row['leaveId'];
				$leaveDays			=	$row['leaveDays'];
				$leaveType			=	$row['leaveType'];
				$leaveFrom			=	$row['leaveFrom'];
				$leaveTo			=	$row['leaveTo'];
				$leaveReason		=	stripslashes($row['leaveReason']);
				$appliedOn			=	$row['appliedOn'];
				$approvedStatus		=	$row['approvedStatus'];
				$leaveReason		=	nl2br($leaveReason);
				$rejectReason		=	stripslashes($row['rejectReason']);
				echo $approvedReason		=	stripslashes($row['approvedReason']);
		?>
		<tr>
			<td class='smalltext2' valign="top"><?php echo $i;?>)</td>
			<td class='smalltext2' valign="top">
				<?php
					if($leaveType  == 1)
					{
						echo "Full Day";
					}
					else
					{
						echo "Half Day";
					}
				?>
			</td>
			<td class='smalltext2' valign="top"><?php echo $leaveDays;?> Days</td>
			<td class='smalltext2' valign="top"><?php echo showDate($appliedOn);?></td>
			<td class='smalltext2' valign="top"><?php echo showDate($leaveFrom);?></td>
			<td class='smalltext2' valign="top">
				<?php
					if($leaveTo != '0000-00-00')
					{
						echo showDate($leaveTo);
					}
					else
					{
						echo "&nbsp;";
					}
				?>
			</td>
			<td class='smalltext2' valign="top"><?php echo $leaveReason;?></td>
			<td class='smalltext2' valign="top">
				<?php
					if($approvedStatus == 1)
					{
						echo nl2br($rejectReason);
					}
					elseif($approvedStatus == 2)
					{
						echo nl2br($approvedReason);
					}
				?>
			</td>
			<td class='smalltext2' valign="top">
				<?php
					if($approvedStatus == '0')
					{
						echo "Pending";
					}
					elseif($approvedStatus == '1')
					{
						echo "Rejected";
					}
					else
					{
						echo "Approved";
					}
				?>
			</td>
			<td class='smalltext2' valign="top">
				<?php
					if($approvedStatus == '0')
					{
						echo "<a href='javascript:deleteLeave($leaveId)' class='link_style5'>Delete</a>";
					}
					else
					{
						echo "&nbsp;";
					}
				?>
			</td>
		</tr>
		<tr>
			<td colspan='10'>
				<hr size='1' width='100%' color='#428484'>
			</td>
		</tr>
		<?php
			}
		?>
		</table>
	<?php
	}
	else
	{
		echo "<br><center><font class='error'><b>You Didinot Apply Any Leave Online</b></font></center><br><br><br><br><br><br><br><br><br><br>";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>