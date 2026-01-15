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
		<td class='heading'>Pending Works List</td>
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
	$whereClause	=	"WHERE employeeId=$s_employeeId AND status=1";
	$orderBy		=	"acceptedOn DESC";
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
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/add-rev-work.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
	?>
		<table width='99%' align='center' cellpadding='3' cellspacing='3' border='0'>
			<tr>
	<?php
		$i	=	0;
		$k	=	0;
		while($row	=   mysql_fetch_assoc($recordSet))
		{
			$i++;
			$k++;

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
			$acceptedOn			=	showDate($row['acceptedOn']);
			$assignedOn			=	showDate($row['assignedOn']);
			$uploadedFileName   =	$row['uploadedFileName'];


			$platName		=	$employeeObj->getPlatformName($platform);
			$customerName	=	$employeeObj->getCustomerName($customerId,$platform);
		?>
		<td width="50%" valign="top">
			<table width='100%' align='center' cellpadding='2' cellspacing='2' border='0' style="border:1px solid #bebebe">
				<tr>
					<td width="10%" class="text">
						Platform
					</td>
					<td width="1%" class="text">
						:
					</td>
					<td class="text2" width="15%">
						<?php echo $platName;?>
					</td>
					<td width="5%">&nbsp;</td>
					<td width="8%" class="text">
						Client
					</td>
					<td width="1%" class="text">
						:
					</td>
					<td class="text2" width="18%">
						<?php echo $customerName;?>
					</td>
					<td width="5%">&nbsp;</td>
					<td width="18%" class="text">
						Accepted On
					</td>
					<td width="1%" class="text">
						:
					</td>
					<td class="text2">
						<?php echo $acceptedOn;?>
					</td>
				</tr>
				<tr>
					<td colspan='11'>
						<hr size='1' width='100%' color='#428484'>
					</td>
				</tr>
				<tr>
					<td colspan="11">
						<table width='100%' align='center' cellpadding='2' cellspacing='2' border='0'>
							<tr>
								<td width="25%">&nbsp;</td>
								<td colspan="2" class='text' align="center">Direct</td>
								<td colspan="2" class='text' align="center">Indirect</td>
								<td colspan="2" class='text' align="center">QA</td>
								<td colspan="2" class='text' align="center">Post Audit</td>
								<td class='text' align="center">Total</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td width='8%' class='smalltext' align="center">LEVEL1</td>
								<td width='8%' class='smalltext' align="center">LEVEL2</td>
								<td width='8%' class='smalltext' align="center">LEVEL1</td>
								<td width='8%' class='smalltext' align="center">LEVEL2</td>
								<td width='8%' class='smalltext' align="center">LEVEL1</td>
								<td width='8%' class='smalltext' align="center">LEVEL2</td>
								<td width='8%' class='smalltext' align="center">LEVEL1</td>
								<td width='8%' class='smalltext' align="center">LEVEL2</td>
								<td class='text' align="center">&nbsp;</td>
							</tr>
							<tr>
								<td class='smalltext2'>Work Assigned</td>
								<td class='smalltext1' align="center"><?php echo $direct1;?></td>
								<td class='smalltext1' align="center"><?php echo $direct2;?></td>
								<td class='smalltext1' align="center"><?php echo $indirect1;?></td>
								<td class='smalltext1' align="center"><?php echo $indirect2;?></td>
								<td class='smalltext1' align="center"><?php echo $qa1;?></td>
								<td class='smalltext1' align="center"><?php echo $qa2;?></td>
								<td class='smalltext1' align="center"><?php echo $audit1;?></td>
								<td class='smalltext1' align="center"><?php echo $audit2;?></td>
								<td class='smalltext1' align="center"><?php echo $totalLinesAssigned;?></td>
							</tr>
							<?php
								$query1		=	"SELECT SUM(directLevel1) AS totalDirectLevel1,SUM(directLevel2) AS totalDirectLevel2,SUM(indirectLevel1) AS totalIndirectLevel1,SUM(indirectLevel2) AS totalIndirectLevel2,SUM(qaLevel1) AS totalQaLevel1,SUM(qaLevel2) AS totalQaLevel2,SUM(auditLevel1) AS totalAuditLevel1,SUM(auditLevel2) AS totalAuditLevel2  FROM employee_works WHERE employeeId=$s_employeeId AND assignedWorkId=$assignedWorkId";
								$result1	=	mysql_query($query1);
								if(mysql_num_rows($result1))
								{
									$row1				=	mysql_fetch_assoc($result1);
									$totalDirectLevel1	=	$row1['totalDirectLevel1'];
									$totalDirectLevel2	=	$row1['totalDirectLevel2'];
									$totalIndirectLevel1=	$row1['totalIndirectLevel1'];
									$totalIndirectLevel2=	$row1['totalIndirectLevel2'];
									$totalQaLevel1		=	$row1['totalQaLevel1'];
									$totalQaLevel2		=	$row1['totalQaLevel2'];
									$totalAuditLevel1	=	$row1['totalAuditLevel1'];
									$totalAuditLevel2	=	$row1['totalAuditLevel2'];
								

									if(empty($totalDirectLevel1))
									{
										$totalDirectLevel1	=	0;
									}
									if(empty($totalDirectLevel2))
									{
										$totalDirectLevel2	=	0;
									}
									if(empty($totalIndirectLevel1))
									{
										$totalIndirectLevel1	=	0;
									}
									if(empty($totalIndirectLevel2))
									{
										$totalIndirectLevel2	=	0;
									}
									if(empty($totalQaLevel1))
									{
										$totalQaLevel1	=	0;
									}
									if(empty($totalQaLevel2))
									{
										$totalQaLevel2	=	0;
									}
									if(empty($totalAuditLevel1))
									{
										$totalAuditLevel1	=	0;
									}
									if(empty($totalAuditLevel2))
									{
										$totalAuditLevel2	=	0;
									}

									$workDoneTotal		=	$totalDirectLevel1+$totalDirectLevel2+$totalIndirectLevel1+$totalIndirectLevel2+$totalQaLevel1+$totalQaLevel2+$totalAuditLevel1+$totalAuditLevel2;

									$balenceDirectLevel1		=	$direct1-$totalDirectLevel1;
									$balenceDirectLevel2		=	$direct2-$totalDirectLevel2;
									$balenceIndirectLevel1		=	$indirect1-$totalIndirectLevel1;
									$balenceIndirectLevel2		=	$indirect2-$totalIndirectLevel2;
									$balenceQaLevel1			=	$qa1-$totalQaLevel1;
									$balenceQaLevel2			=	$qa2-$totalQaLevel2;
									$balenceAuditLevel1			=	$audit1-$totalAuditLevel1;
									$balenceAuditLevel2			=	$audit2-$totalAuditLevel2;

									$balanceTotal				=	$balenceDirectLevel1+$balenceDirectLevel2+$balenceIndirectLevel1+$balenceIndirectLevel2+$balenceQaLevel1+$balenceQaLevel2+$balenceAuditLevel1+$balenceAuditLevel2;
							?>
							<tr>
								<td class="smalltext2">
									Work Done
								</td>
								<td class='smalltext1' valign="top" align="center">
									<?php echo $totalDirectLevel1;?>
								</td>
								<td class='smalltext1' valign="top" align="center">
									<?php echo $totalDirectLevel2;?>
								</td>
								<td class='smalltext1' valign="top" align="center">
									<?php echo $totalIndirectLevel1;?>
								</td>
								<td class='smalltext1' valign="top" align="center">
									<?php echo $totalIndirectLevel2;?>
								</td>
								<td class='smalltext1' valign="top" align="center">
									<?php echo $totalQaLevel1;?>
								</td>
								<td class='smalltext1' valign="top" align="center">
									<?php echo $totalQaLevel2;?>
								</td>
								<td class='smalltext1' valign="top" align="center">
									<?php echo $totalAuditLevel1;?>
								</td>
								<td class='smalltext1' valign="top" align="center">
									<?php echo $totalAuditLevel2;?>
								</td>
								<td class='smalltext1' valign="top" align="center">
									<?php echo $workDoneTotal;?>
								</td>
							</tr>
							<tr>
								<td colspan='14'>
									<hr size='1' width='100%' color='#428484'>
								</td>
							</tr>
							<tr>
								<td class="smalltext2">
									Balance Work
								</td>
								<td class='smalltext1' valign="top" align="center">
									<?php echo $balenceDirectLevel1;?>
								</td>
								<td class='smalltext1' valign="top" align="center">
									<?php echo $balenceDirectLevel2;?>
								</td>
								<td class='smalltext1' valign="top" align="center">
									<?php echo $balenceIndirectLevel1;?>
								</td>
								<td class='smalltext1' valign="top" align="center">
									<?php echo $balenceIndirectLevel2;?>
								</td>
								<td class='smalltext1' valign="top" align="center">
									<?php echo $balenceQaLevel1;?>
								</td>
								<td class='smalltext1' valign="top" align="center">
									<?php echo $balenceQaLevel2;?>
								</td>
								<td class='smalltext1' valign="top" align="center">
									<?php echo $balenceAuditLevel1;?>
								</td>
								<td class='smalltext1' valign="top" align="center">
									<?php echo $balenceAuditLevel2;?>
								</td>
								<td class='smalltext1' valign="top" align="center">
									<?php echo $balanceTotal;?>
								</td>
							</tr>
							<tr>
								<td colspan='11'>
									<hr size='1' width='100%' color='#428484'>
								</td>
							</tr>
							<?php
								}
							?>
						</table>
					</td>
				</tr>
				<tr>
					<td class="text" valign="top" colspan="3">
						File Name:
					</td>
					<td colspan="9" valign="top">&nbsp;
						<?php
							echo $uploadedFileName;
						?>
					</td>
				</tr>
				<tr>
					<td class="text" valign="top" colspan="3">
						Comments:
					</td>
					<td colspan="9" valign="top">&nbsp;
						<?php
							echo $comments;
						?>
					</td>
				</tr>
				<tr>
					<td class="text" valign="top" colspan="3">
						Assigned On :
					</td>
					<td colspan="9" valign="top">&nbsp;
						<?php
							echo $assignedOn;
						?>
					</td>
				</tr>
				<tr>
					<td colspan="11" valign="top">
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/add-work-details.php?ID=<?php echo $assignedWorkId;?>" class="link_style8">ADD YOUR WORK</a>
					</td>
				</tr>
			</table>
		</td>
	<?php
			if($k == 2)
			{
				echo "</tr><tr><td colspan='2'>&nbsp;</td></tr><tr>";
				$k = 0;
			}
		}
		for($l=$i;$l<2;$l++)
		{
			echo "<td>&nbsp;</td>";
		}
	?>
	<tr>
	</table>
	<?php
		echo "<table width='100%'><tr><td align='right'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr></table>";
	}
	else
	{
		echo "<br><center><font class='error'><b>No Pending Work Available !!</b></font></center>";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>