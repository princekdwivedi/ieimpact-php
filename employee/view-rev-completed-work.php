<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");;
	include(SITE_ROOT_EMPLOYEES	."/includes/common-array.php");
	$employeeObj                = new employee();
	$validator					= new validate();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");

	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$assignedWorkId				=	0;
	$employeeId					=	0;
	$employeeName				=	"";
	$totalDirectLevel1			=	0;
	$totalDirectLevel2			=	0;
	$totalIndirectLevel1		=	0;
	$totalIndirectLevel2		=	0;
	$totalQaLevel1				=	0;
	$totalQaLevel2				=	0;
	$totalAuditLevel1			=	0;
	$totalAuditLevel2			=	0;

	$workDoneTotal				=	0;
	$balanceTotal				=	0;

	if(isset($_GET['ID']) && isset($_GET['employeeId']))
	{
		$assignedWorkId		=	(int)$_GET['ID'];
		$employeeId			=	(int)$_GET['employeeId'];
		$employeeName		=	$employeeObj->getEmployeeName($employeeId);
		if($result = $employeeObj->getAssignedWorkDetails($employeeId,$assignedWorkId))
		{
			$row				=	mysql_fetch_assoc($result);
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
			$t_comments			=	$row['comments'];
			$totalLinesAssigned	=	$row['totalLinesAssigned'];
			$assignedOn			=	showDate($row['assignedOn']);
			$completedOn		=	showDate($row['completedOn']);
			$status				=	$row['status'];
			$uploadedFileName	=	$row['uploadedFileName'];

			$platName		=	$employeeObj->getPlatformName($platform);
			$customerName	=	$employeeObj->getCustomerName($customerId,$platform);
			
			$query		=	"SELECT SUM(directLevel1) AS totalDirectLevel1,SUM(directLevel2) AS totalDirectLevel2,SUM(indirectLevel1) AS totalIndirectLevel1,SUM(indirectLevel2) AS totalIndirectLevel2,SUM(qaLevel1) AS totalQaLevel1,SUM(qaLevel2) AS totalQaLevel2,SUM(auditLevel1) AS totalAuditLevel1,SUM(auditLevel2) AS totalAuditLevel2  FROM employee_works WHERE employeeId=$s_employeeId AND assignedWorkId=$assignedWorkId";
			$result	=	mysql_query($query);
			if(mysql_num_rows($result))
			{
				$row				=	mysql_fetch_assoc($result);
				$totalDirectLevel1	=	$row['totalDirectLevel1'];
				$totalDirectLevel2	=	$row['totalDirectLevel2'];
				$totalIndirectLevel1=	$row['totalIndirectLevel1'];
				$totalIndirectLevel2=	$row['totalIndirectLevel2'];
				$totalQaLevel1		=	$row['totalQaLevel1'];
				$totalQaLevel2		=	$row['totalQaLevel2'];
				$totalAuditLevel1	=	$row['totalAuditLevel1'];
				$totalAuditLevel2	=	$row['totalAuditLevel2'];
			

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
			}
			if($status != 2)
			{
				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES);
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
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
?>
<table width="99%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='heading'>Complete Work Done By <?php echo $employeeName;?></td>
	</tr>
</table>
<br>
<table width='99%' align='center' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<td colspan='15'>
			<hr size='1' width='100%' color='#428484'>
		</td>
	</tr>
	<tr>
		<td width='8%' class='text'>Platform</td>
		<td width='8%' class='text'>Client Name</td>
		<td colspan="2" class='text' align="center">Direct</td>
		<td colspan="2" class='text' align="center">Indirect</td>
		<td colspan="2" class='text' align="center">QA</td>
		<td colspan="2" class='text' align="center">Post Audit</td>
		<td width='5%' class='text' align="center">Total</td>
		<td width='8%' class='text'>File Name</td>
		<td width='15%' class='text'>Comments</td>
		<td width='8%' class='text'>Assigned On</td>
		<td class='text'>Completed On</td>
	</tr>
	<tr>
		<td width='5%' colspan="2">&nbsp;</td>
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
		<td colspan='15'>
			<hr size='1' width='100%' color='#428484'>
		</td>
	</tr>
	<tr>
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
			<?php echo $t_comments;?>
		</td>
		<td class='smalltext1' valign="top">
			<?php echo $assignedOn;?>
		</td>
		<td class='smalltext1' valign="top">
			<?php echo $completedOn;?>
		</td>
	</tr>
	<tr>
		<td colspan='15'>
			<hr size='1' width='100%' color='#428484'>
		</td>
	</tr>
</table>
<?php
	$query		=	"SELECT * FROM employee_works WHERE employeeId=$employeeId AND assignedWorkId=$assignedWorkId ORDER BY workedOn DESC";
	$result	=	mysql_query($query);
	if(mysql_num_rows($result))
	{
?>
<br>
<table width='99%' align='center' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<td colspan='13' class="heading">
			Work Done Details
		</td>
	</tr>
	<tr>
		<td colspan='13'>
			<hr size='1' width='100%' color='#428484'>
		</td>
	</tr>
	<tr>
		<td width="15%" class='text'>Sr No</td>
		<td colspan="2" class='text' align="center">Direct</td>
		<td colspan="2" class='text' align="center">Indirect</td>
		<td colspan="2" class='text' align="center">QA</td>
		<td colspan="2" class='text' align="center">Post Audit</td>
		<td width='8%' class='text' align="center">Total</td>
		<td width='10%' class='text'>File Name</td>
		<td width='18%' class='text'>Comments</td>
		<td class='text'>Done On</td>
	</tr>
	<tr>
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
		<td colspan='13'>
			<hr size='1' width='100%' color='#428484'>
		</td>
	</tr>
	<?php
		$i			=	0;
		while($row	=	mysql_fetch_assoc($result))
		{
			$i++;
			$workId			=	$row['workId'];
			$directLevel1	=	$row['directLevel1'];
			$directLevel2	=	$row['directLevel2'];
			$indirectLevel1	=	$row['indirectLevel1'];
			$indirectLevel2	=	$row['indirectLevel2'];

			$qaLevel1		=	$row['qaLevel1'];
			$qaLevel2		=	$row['qaLevel2'];
			$auditLevel1	=	$row['auditLevel1'];
			$auditLevel2	=	$row['auditLevel2'];

			$workedOn		=	$row['workedOn'];
			$t_workedOn		=	showDate($row['workedOn']);
			$comments		=	$row['comments'];
			$uploadFileName	=	$row['uploadFileName'];

			$total			=	$directLevel1+$directLevel2+$indirectLevel1+$indirectLevel2+$qaLevel1+$qaLevel2+$auditLevel1+$auditLevel2;
	?>
	<tr>
		<td class="smalltext2" valign="top"><?php echo $i?>)</td>
		<td class="smalltext2" valign="top" align="center"><?php echo $directLevel1?></td>
		<td class="smalltext2" valign="top" align="center"><?php echo $directLevel2?></td>
		<td class="smalltext2" valign="top" align="center"><?php echo $indirectLevel1?></td>
		<td class="smalltext2" valign="top" align="center"><?php echo $indirectLevel2?></td>
		<td class="smalltext2" valign="top" align="center"><?php echo $qaLevel1?></td>
		<td class="smalltext2" valign="top" align="center"><?php echo $qaLevel2?></td>
		<td class="smalltext2" valign="top" align="center"><?php echo $auditLevel1?></td>
		<td class="smalltext2" valign="top" align="center"><?php echo $auditLevel2?></td>
		<td class="smalltext2" valign="top" align="center"><?php echo $total?></td>
		<td class="smalltext2" valign="top"><?php echo $uploadFileName?></td>
		<td class="smalltext2" valign="top"><?php echo $comments?></td>
		<td class="smalltext2" valign="top"><?php echo $t_workedOn?></td>
	</tr>
	<tr>
		<td colspan='13'>
			<hr size='1' width='100%' color='#428484'>
		</td>
	</tr>
	<?php

		}
	}
	?>
</table>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>