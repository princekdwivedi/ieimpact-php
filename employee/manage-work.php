<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-mt-employee-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	include(SITE_ROOT_EMPLOYEES	."/includes/common-array.php");
	$employeeObj                = new employee();
	$pagingObj			        = new Paging();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");

	if(isset($_GET['datewiseID']))
	{
		$datewiseID	=	$_GET['datewiseID'];
		$isDelete	=	$_GET['isDelete'];
		if($isDelete==	1)
		{
			$employeeObj->deleteMtEmployeeTarget($datewiseID);
			dbQuery("DELETE FROM datewise_employee_works_money WHERE ID=$datewiseID AND employeeId=$s_employeeId");

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
		header("Location: ".SITE_URL_EMPLOYEES."/manage-work.php$link");
		exit();
	}
?>
<table width="99%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='textstyle3'><b>VIEW MONTHLY LINES</b></td>
	</tr>
</table>
<script type="text/javascript">
function deleteWork(datewiseID,rec)
{
	var confirmation = window.confirm("Are you sure to delete these lines?");
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/manage-work.php?datewiseID="+datewiseID+"&rec="+rec+"&isDelete=1";
	}
}
</script>
<?php
	$whereClause		=	"WHERE ID > ".MAX_SEARCH_MT_EMPLOYEE_WORKID." AND employeeId=$s_employeeId AND departmentId=1";
	$orderBy			=	"workedOnDate DESC";
	$queryString		=	"";
	if(isset($_REQUEST['recNo']))
	{
		$recNo		    =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo			=	0;
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
	$pagingObj->table		  =	"datewise_employee_works_money";
	$pagingObj->selectColumns = "*";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/manage-work.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
	?>
		<table width='99%' align='center' cellpadding='0' cellspacing='0' border='0'>
			<tr height='25' bgcolor="#373737">
				<td width='5%' class='smalltext12' valign="top">&nbsp;Sr.No</td>
				<td width='15%' class='smalltext12' valign="top">Platform</td>
				<td width='15%' class='smalltext12' valign="top">Client Name</td>
				<td width='12%' class='smalltext12' valign="top">Transcription</td>
				<td width='12%' class='smalltext12' valign="top">VRE Lines</td>
				<td width='12%' class='smalltext12' valign="top">QA Lines</td>
				<td width='12%' class='smalltext12' valign="top">Night shift lines</td>
				<td width='9%' class='smalltext12' valign="top">Added On</td>
				<td></td>
			</tr>
	<?php
		$i	=	0;
		while($row	=   mysql_fetch_assoc($recordSet))
		{
			$i++;
			$datewiseID					=	$row['ID'];
			$platform					=	$row['platform'];
			$customerId					=	$row['customerId'];
			$transcriptionLinesEntered	=	$row['totalDirectTrascriptionLines'];
			$vreLinesEntered			=	$row['totalDirectVreLines'];
			$qaLinesEntered				=	$row['totalQaLines'];
			$workedOn					=	$row['workedOnDate'];
			$addedTime					=	$row['addedTime'];
			
			$indirectTranscriptionLinesEntered	=	$row['totalIndirectTrascriptionLines'];
			$indirectVreLinesEntered	=	$row['totalIndirectVreLines'];
			$indirectQaLinesEntered		=	$row['totalIndirectQaLines'];

			$auditLinesEntered			=	$row['totalDirectAuditLines'];
			$indirectAuditLinesEntered	=	$row['totalIndirectAuditLines'];

			$platName					=	$employeeObj->getPlatformName($platform);
			$customerName				=	$employeeObj->getCustomerName($customerId,$platform);

			$t_workDate					=	showDate($workedOn);

			
			if($platform <= 3 )
			{
				$text	=	"D-";
				$text1	=	"-N-";
			}
			else
			{
				$text	=	"";
				$text1	=	"";
			}
			
			$bgColor						=	"class='rwcolor1'";
			if($i%2==0)
			{
				$bgColor					=	"class='rwcolor2'";
			}
		?>
		<tr <?php echo $bgColor;?> height="25">
			<td class="smalltext2" valign="top">
				&nbsp;<?php echo $i.")";?>
			</td>
			<td class="smalltext2" valign="top">
				<?php echo $platName;?>
			</td>
			<td class="smalltext2" valign="top">
				<?php echo $customerName;?>
			</td>
			<td class="smalltext2" valign="top">
				<?php echo "D-".$transcriptionLinesEntered."-N-".$indirectTranscriptionLinesEntered;?>
			</td>
			<td class="smalltext2" valign="top">
				<?php echo "D-".$vreLinesEntered."-N-".$indirectVreLinesEntered;?>
			</td>
			<td class="smalltext2" valign="top">
				<?php 
					if($platform <= 3 )
					{
						echo $text.$qaLinesEntered.$text1.$indirectQaLinesEntered;
					}
					else
					{
						echo $text.$qaLevel1.$text1.$qaLevel2;
					}
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php 
					if($platform <= 3 )
					{	
						
						echo "T-".$auditLinesEntered."-V-".$indirectAuditLinesEntered;
					}
					else
					{
						echo "T-".$auditLevel1."-V-".$auditLevel2;
					}
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php echo $t_workDate;?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					$diffe		=	timeBetweenTwoTimes($workedOn,$addedTime,CURRENT_DATE_CUSTOMER_ZONE,CURRENT_TIME_CUSTOMER_ZONE);

					if($diffe   <= 4320)
					{
				?>
				<a href="<?php echo SITE_URL_EMPLOYEES;?>/add-daily-work.php?ID=<?php echo $datewiseID;?>" class="link_style5">Edit</a>|				
				<?php
					}	
				?>
				<img src="<?php echo SITE_URL;?>/images/cross1.gif" border="0" title="Delete" onclick="deleteWork(<?php echo $datewiseID;?>,<?php echo $recNo;?>)" style="cursor:pointer;">
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
		echo "<br><center><font class='error'><b>No Lines Added Yet</b></font></center>";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>