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
	$employeeObj				= new employee();
	$pagingObj			        = new Paging();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");

	///////////////////////////////// UPDATE LEAVES WHERE ACTION NOT TAKEN ////////////////////
	$currentDate                =  CURRENT_DATE_INDIA;
	$currentTime 			    =  CURRENT_TIME_INDIA;
	$query 					    =  "UPDATE employee_leave_applied SET approvedStatus=1,isForcedAction=1,forcedActionDate='$currentDate',forcedActionTime='$currentTime' WHERE approvedStatus=0 AND leaveFrom <= '$currentDate' AND leaveType <> 2";
	dbQuery($query);
	///////////////////////////////////////////////////////////////////////////////////////////

	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$leaveId					=	0;
	$employeeId					=	0;
	$a_leaveFromToDate			=	array();
	if(isset($_GET['leaveId']) && isset($_GET['ID']))
	{
		$leaveId	=	$_GET['leaveId'];
		$employeeId	=	$_GET['ID'];
		$query		=	"SELECT * FROM employee_leave_applied  WHERE leaveId=$leaveId AND employeeId=$employeeId";
		$result		=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$row				=	mysqli_fetch_assoc($result);
			$leaveType			=	$row['leaveType'];
			$leaveFrom			=	$row['leaveFrom'];
			$leaveTo			=	$row['leaveTo'];
			$leaveDays			=	$row['leaveDays'];

			if($leaveDays == 1)
			{
				$a_leaveFromToDate[]	=	$leaveFrom;
			}
			elseif($leaveDays > 1)
			{
				$a_leaveFromToDate		=	datesBetweenTwoDates($leaveFrom,$leaveTo);
			}

			if(isset($_GET['isDelete']) && $_GET['isDelete'] == 1)
			{
				dbQuery("DELETE FROM employee_leave_applied WHERE leaveId=$leaveId AND employeeId=$employeeId");

			}
			if(isset($_GET['type']))
			{
				$type	 =	$_GET["type"];
				if($type == 1)
				{
					if(!empty($a_leaveFromToDate))
					{
						foreach($a_leaveFromToDate as $k=>$loginDate)
						{
							$existingAttendenceId	=	$employeeObj->getSingleQueryResult("SELECT attendenceId FROM employee_attendence WHERE attendenceId > ".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID." AND employeeId=$employeeId AND loginDate='$loginDate'","attendenceId");

							if(!empty($existingAttendenceId))
							{
								$hasLogin	=	$employeeObj->getSingleQueryResult("SELECT isLogin FROM employee_attendence WHERE attendenceId > ".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID." AND attendenceId=$existingAttendenceId AND employeeId=$employeeId AND loginDate='$loginDate'","isLogin");

								if($hasLogin == 1)
								{
									dbQuery("UPDATE employee_attendence SET onLeave=0 WHERE attendenceId=$existingAttendenceId AND employeeId=$employeeId");
								}
								else
								{
									dbQuery("DELETE FROM employee_attendence WHERE attendenceId=$existingAttendenceId AND employeeId=$employeeId");
								}
							}
								
						}
						
						dbQuery("UPDATE employee_leave_applied SET approvedByManager=$s_employeeId,approvedStatus=1,approvedOn='".CURRENT_DATE_INDIA."' WHERE leaveId=$leaveId AND employeeId=$employeeId");
					}
				}
				elseif($type == 2)
				{
					
					foreach($a_leaveFromToDate as $k=>$loginDate)
					{
						$existingAttendenceId	=	$employeeObj->getSingleQueryResult("SELECT attendenceId FROM employee_attendence WHERE attendenceId > ".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID." AND employeeId=$employeeId AND isLogin=1 AND loginDate='$loginDate'","attendenceId");

						if(empty($existingAttendenceId))
						{
							dbQuery("INSERT INTO employee_attendence SET onLeave=$leaveType,employeeId=$employeeId,loginDate='$loginDate'");
						}
						else
						{
							dbQuery("UPDATE employee_attendence SET onLeave=$leaveType WHERE attendenceId=$existingAttendenceId AND employeeId=$employeeId AND loginDate='$loginDate'");
						}
					}
					
					dbQuery("UPDATE employee_leave_applied SET approvedByManager=$s_employeeId,approvedStatus=2,approvedOn='".CURRENT_DATE_INDIA."' WHERE leaveId=$leaveId AND employeeId=$employeeId");
				}
			}
		}
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES ."/view-online-leaves.php");
		exit();
	}
?>
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='heading'>VIEW LEAVE DETAILS APPLIED ONLINE</td>
	</tr>
</table>
<script type="text/javascript">
function deleteLeave(leaveId,ID)
{
	var confirmation = window.confirm("Are you sure to delete this Leave?");
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/view-online-leaves.php?leaveId="+leaveId+"&ID="+ID+"&isDelete=1";
	}
}
function markLeave(leaveId,ID,type)
{
	if(type == 1)
	{
		var confirmation = window.confirm("Are you sure to reject this Leave?");
	}
	if(type == 2)
	{
		var confirmation = window.confirm("Are you sure to approve this Leave?");
	}
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/view-online-leaves.php?leaveId="+leaveId+"&ID="+ID+"&type="+type;
	}
}
function openAcceptRejectWindow(employeeId,leaveId,type)
{
	path = "<?php echo SITE_URL_EMPLOYEES;?>/leave-approve-reject-status.php?employeeId="+employeeId+"&leaveId="+leaveId+"&type="+type;
	prop = "toolbar=no,scrollbars=yes,width=350,height=250,top=50,left=100";
	window.open(path,'',prop);
}
</script>
<?php
	if(isset($_SESSION['hasPdfAccess']))
	{
		$whereClause		=	"WHERE isActive=1 AND hasPdfAccess=1";
	}
	else
	{
		$whereClause		=	"WHERE isActive=1 AND hasPdfAccess=0";
	}
	$orderBy				=	"appliedOn DESC";
	$queryString			=	"";
	if(isset($_REQUEST['recNo']))
	{
		$recNo				 =	(int)$_REQUEST['recNo'];
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
	
	$pagingObj->table		  =	"employee_leave_applied INNER JOIN employee_details ON employee_leave_applied.employeeId=employee_details.employeeId";
	$pagingObj->selectColumns = "employee_leave_applied.*,firstName,lastName";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/view-online-leaves.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
	?>
		<table width='99%' align='center' cellpadding='0' cellspacing='0' border='0'>
			<tr>
				<td colspan='11'>
					<hr size='1' width='100%' color='#428484'>
				</td>
			</tr>
			<tr>
				<td width='3%' class='text'>Sr.No</td>
				<td width='13%' class='text'>Employee</td>
				<td width='7%' class='text'>Leave Type</td>
				<td width='6%' class='text'>Leave For</td>
				<td width='8%' class='text'>Applied On</td>
				<td width='9%' class='text'>For/From Date</td>
				<td width='8%' class='text'>To Date</td>
				<td width='15%' class='text'>Reason</td>
				<td width='10%' class='text'>Emergency No</td>
				<td width='9%' class='text'>Status</td>
				<td class='text'>Action</td>
			</tr>
			<tr>
				<td colspan='11'>
					<hr size='1' width='100%' color='#428484'>
				</td>
			</tr>
		<?php
			$i	=	$recNo;
			while($row	=   mysqli_fetch_assoc($recordSet))
			{
				$i++;
				$leaveId			=	$row['leaveId'];
				$employeeId			=	$row['employeeId'];
				$leaveDays			=	$row['leaveDays'];
				$leaveType			=	$row['leaveType'];
				$leaveFrom			=	$row['leaveFrom'];
				$leaveTo			=	$row['leaveTo'];
				$leaveReason		=	stripslashes($row['leaveReason']);
				$appliedOn			=	$row['appliedOn'];
				$approvedStatus		=	$row['approvedStatus'];
				$emergencyNo		=	$row['emergencyNo'];
				$firstName			=	$row['firstName'];
				$lastName			=	$row['lastName'];
				$isForcedAction 	=	$row['isForcedAction'];
				$leaveReason		=	nl2br($leaveReason);

				$employeeName		=	$firstName." ".$lastName;
				$employeeName		=	ucwords($employeeName);
		?>
		<tr>
			<td class='smalltext2' valign="top"><?php echo $i;?>)</td>
			<td class='smalltext2' valign="top"><?php echo $employeeName;?></td>
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
			<td class='smalltext2' valign="top"><?php echo $emergencyNo;?></td>
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
					if(empty($isForcedAction)){

						if($approvedStatus == '0')
						{
							echo "<a onclick='openAcceptRejectWindow($employeeId,$leaveId,1)' class='link_style2' style='cursor:pointer;'>Reject</a>|<a onclick='openAcceptRejectWindow($employeeId,$leaveId,2)' class='link_style2' style='cursor:pointer;'>Approve</a>|<a onclick='deleteLeave($leaveId,$employeeId)' class='link_style2' style='cursor:pointer;'>Delete</a>";
						}
						elseif($approvedStatus == '1')
						{
							echo "<a onclick='openAcceptRejectWindow($employeeId,$leaveId,2)' class='link_style2' style='cursor:pointer;'>Approve Now</a>|<a onclick='deleteLeave($leaveId,$employeeId)' class='link_style2' style='cursor:pointer;'>Delete</a>";
						}
						else
						{
							echo "<a onclick='openAcceptRejectWindow($employeeId,$leaveId,1)' class='link_style2' style='cursor:pointer;'>Reject This Leave</a>";
						}
					}
					else{
						echo "-";
					}
				?>
			</td>
		</tr>
		<tr>
			<td colspan='11'>
				<hr size='1' width='100%' color='#428484'>
			</td>
		</tr>
		<?php
			}
			echo "<tr><td colspan='11' align='right'><br /><br /><table width='90%' border='0' ><tr height=20><td align=right><font color='#000000'>";
			$pagingObj->displayPaging($queryString);
			echo "<br /><br /><b>Total Records : " . $totalRecords . "</font></b></td></tr></table></td></tr>";
		?>
		</table>
	<?php
	}
	else
	{
		echo "<br><center><font class='error'><b>No Online Leave Found</b></font></center>";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>
