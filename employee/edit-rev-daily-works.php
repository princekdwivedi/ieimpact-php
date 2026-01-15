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
	function findexts($filename) 
	{ 
		$filename = strtolower($filename) ; 
		$exts	  = split("[/\\.]", $filename) ; 
		$n		  = count($exts)-1; 
		$exts     = $exts[$n]; 
		return $exts; 
	} 
	function getFileName($fileName)
	{
		$fileExtPos		=  strrpos($fileName, '.');
		$fileName		=  substr($fileName,0,$fileExtPos);

		return $fileName;
	}


	$workID						=	0;
	$employeeId					=	0;
	$employeeName				=	"";
	$statusText					=	"INCOMPLETE WORK";

	$assignedWorkId				=	0;
	$directLevel1				=	"";
	$directLevel2				=	"";
	$indirectLevel1				=	"";
	$indirectLevel2				=	"";
	$qaLevel1					=	"";
	$qaLevel2					=	"";
	$auditLevel1				=	"";
	$auditLevel2				=	"";
	$comments					=	"";
	$success					=	"";
	$errorMsg					=	"";
	$toatlLines					=	0;

	$totalDirectLevel1			=	0;
	$totalDirectLevel2			=	0;
	$totalIndirectLevel1		=	0;
	$totalIndirectLevel2		=	0;
	$totalQaLevel1				=	0;
	$totalQaLevel2				=	0;
	$totalAuditLevel1			=	0;
	$totalAuditLevel2			=	0;

	$balenceDirectLevel1		=	0;
	$balenceDirectLevel2		=	0;
	$balenceIndirectLevel1		=	0;
	$balenceIndirectLevel2		=	0;
	$balenceQaLevel1			=	0;
	$balenceQaLevel2			=	0;
	$balenceAuditLevel1			=	0;
	$balenceAuditLevel2			=	0;

	$workDoneTotal				=	0;
	$balanceTotal				=	0;

	$hasUploadFile				=	0;
	$uploadFileName				=	"";
	$extension					=	"";

	if(isset($_GET['success']))
	{
		$success	=	$_GET['success'];
	}
	if(isset($_GET['employeeId']) && isset($_GET['workId']) && isset($_GET['ID']))
	{
		$employeeId		=	(int)$_GET['employeeId'];
		$workId			=	(int)$_GET['workId'];
		$assignedWorkId	=	(int)$_GET['ID'];
		$employeeName	=	$employeeObj->getEmployeeName($employeeId);
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

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
		$acceptedOn			=	showDate($row['acceptedOn']);
		$status				=	$row['status'];
		$uploadedFileName	=	$row['uploadedFileName'];
		if($status	==	2)
		{
			$statusText		=	"COMPLETE WORK";
		}

		$platName		=	$employeeObj->getPlatformName($platform);
		$customerName	=	$employeeObj->getCustomerName($customerId,$platform);
		
		$query		=	"SELECT SUM(directLevel1) AS totalDirectLevel1,SUM(directLevel2) AS totalDirectLevel2,SUM(indirectLevel1) AS totalIndirectLevel1,SUM(indirectLevel2) AS totalIndirectLevel2,SUM(qaLevel1) AS totalQaLevel1,SUM(qaLevel2) AS totalQaLevel2,SUM(auditLevel1) AS totalAuditLevel1,SUM(auditLevel2) AS totalAuditLevel2  FROM employee_works WHERE employeeId=$employeeId AND assignedWorkId=$assignedWorkId";
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

			$balenceDirectLevel1		=	$direct1-$totalDirectLevel1;
			$balenceDirectLevel2		=	$direct2-$totalDirectLevel2;
			$balenceIndirectLevel1		=	$indirect1-$totalIndirectLevel1;
			$balenceIndirectLevel2		=	$indirect2-$totalIndirectLevel2;
			$balenceQaLevel1			=	$qa1-$totalQaLevel1;
			$balenceQaLevel2			=	$qa2-$totalQaLevel2;
			$balenceAuditLevel1			=	$audit1-$totalAuditLevel1;
			$balenceAuditLevel2			=	$audit2-$totalAuditLevel2;

			$balanceTotal				=	$balenceDirectLevel1+$balenceDirectLevel2+$balenceIndirectLevel1+$balenceIndirectLevel2+$balenceQaLevel1+$balenceQaLevel2+$balenceAuditLevel1+$balenceAuditLevel2;
		}
		$query		=	"SELECT * FROM employee_works WHERE employeeId=$employeeId AND assignedWorkId=$assignedWorkId AND workId=$workId";
		$result	=	mysql_query($query);
		if(mysql_num_rows($result))
		{
			$row	=	mysql_fetch_assoc($result);
			$directLevel1				=	$row['directLevel1'];
			$directLevel2				=	$row['directLevel2'];
			$indirectLevel1				=	$row['indirectLevel1'];
			$indirectLevel2				=	$row['indirectLevel2'];
			$qaLevel1					=	$row['qaLevel1'];
			$qaLevel2					=	$row['qaLevel2'];
			$auditLevel1				=	$row['auditLevel1'];
			$auditLevel2				=	$row['auditLevel2'];
			$comments					=	$row['comments'];
			$hasUploadFile				=	$row['hasUploadFile'];
			$uploadFileName				=	$row['uploadFileName'];
			$extension					=	$row['extension'];
			$workedOn					=	showDate($row['workedOn']);

			if($directLevel1	==	0)
			{
				$directLevel1	=	"";
			}
			if($directLevel2	==	0)
			{
				$directLevel2	=	"";
			}
			if($indirectLevel1	==	0)
			{
				$indirectLevel1	=	"";
			}
			if($indirectLevel2	==	0)
			{
				$indirectLevel2	=	"";
			}
			if($qaLevel1	==	0)
			{
				$qaLevel1	=	"";
			}
			if($qaLevel2	==	0)
			{
				$qaLevel2	=	"";
			}
			if($auditLevel1	==	0)
			{
				$auditLevel1	=	"";
			}
			if($auditLevel2	==	0)
			{
				$auditLevel2	=	"";
			}

			$t_directLevel1				=	$directLevel1;
			$t_directLevel2				=	$directLevel2;
			$t_indirectLevel1			=	$indirectLevel1;
			$t_indirectLevel2			=	$indirectLevel2;
			$t_qaLevel1					=	$qaLevel1;
			$t_qaLevel2					=	$qaLevel2;
			$t_auditLevel1				=	$auditLevel1;
			$t_auditLevel2				=	$auditLevel2;
		}
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	
	$form						=	SITE_ROOT_EMPLOYEES."/forms/add-work-details.php";
?>
<table width="99%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='heading'>Edit Daily Work For <?php echo $employeeName;?> On <?php echo $workedOn;?> ( <?php echo $statusText;?> )</td>
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
		<td width='16%' class='text'>Comments</td>
		<td width='8%' class='text'>Assigned On</td>
		<td class='text'>Accepted On</td>
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
			<?php echo $acceptedOn;?>
		</td>
	</tr>
	<tr>
		<td colspan='15'>
			<hr size='1' width='100%' color='#428484'>
		</td>
	</tr>
	<tr>
		<td class="smalltext2" colspan="2">
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
		<td class='smalltext1' colspan="4">
			&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan='15'>
			<hr size='1' width='100%' color='#428484'>
		</td>
	</tr>
	<tr>
		<td class="smalltext2" colspan="2">
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
		<td class='smalltext1' colspan="4">
			&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan='15'>
			<hr size='1' width='100%' color='#428484'>
		</td>
	</tr>
<table>
<br>

<?php
	if(!empty($success))
	{
?>
<table width="99%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td class='title1' align="center">Successfully Updated Work !!</td>
	</tr>
	<tr>
		<td height="150"></td>
	</tr>
</table>
<?php
	}
	elseif(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);

		$directLevel1			=	trim($directLevel1);
		$directLevel2			=	trim($directLevel2);
		$indirectLevel1			=	trim($indirectLevel1);
		$indirectLevel2			=	trim($indirectLevel2);
		$qaLevel1				=	trim($qaLevel1);
		$qaLevel2				=	trim($qaLevel2);
		$auditLevel1			=	trim($auditLevel1);
		$auditLevel2			=	trim($auditLevel2);
		$comments				=	trim($comments);

		if($directLevel1	==	"")
		{
			$directLevel1	=	0;
		}
		if($directLevel2	==	"")
		{
			$directLevel2	=	0;
		}
		if($indirectLevel1	==	"")
		{
			$indirectLevel1	=	0;
		}
		if($indirectLevel2	==	"")
		{
			$indirectLevel2	=	0;
		}
		if($qaLevel1	==	"")
		{
			$qaLevel1	=	0;
		}
		if($qaLevel2	==	"")
		{
			$qaLevel2	=	0;
		}
		if($auditLevel1	==	"")
		{
			$auditLevel1	=	0;
		}
		if($auditLevel2	==	"")
		{
			$auditLevel2	=	0;
		}

		$t_totalDirectLevel1	=	$totalDirectLevel1+$directLevel1-$t_directLevel1;
		$t_totalDirectLevel2	=	$totalDirectLevel2+$directLevel2-$t_directLevel2;
		$t_totalIndirectLevel1	=	$totalIndirectLevel1+$indirectLevel1-$t_indirectLevel1;
		$t_totalIndirectLevel2	=	$totalIndirectLevel2+$indirectLevel2-$t_indirectLevel2;
		$t_totalQaLevel1		=	$totalQaLevel1+$qaLevel1-$t_qaLevel1;
		$t_totalQaLevel2		=	$totalQaLevel2+$qaLevel2-$t_qaLevel2;
		$t_totalAuditLevel1		=	$totalAuditLevel1+$auditLevel1-$t_auditLevel1;
		$t_totalAuditLevel2		=	$totalAuditLevel2+$auditLevel2-$t_auditLevel2;

		$toatlLines				=	$t_totalDirectLevel1+$t_totalDirectLevel2+$t_totalIndirectLevel1+$t_totalIndirectLevel2+$t_totalQaLevel1+$t_totalQaLevel2+$t_totalAuditLevel1+$t_totalAuditLevel2;

		if(empty($directLevel1) && empty($directLevel2) && empty($indirectLevel1) && empty($indirectLevel2) && empty($qaLevel1) && empty($qaLevel2) && empty($auditLevel1) && empty($auditLevel2))
		{
			$validator ->setError("Please Enter Lines !!");
		}
		if(!empty($directLevel1) && $directLevel1 > $direct1)
		{
			$validator ->setError("Direct Level1 line is exceed more than assigned !!");
		}
		if(!empty($directLevel2) && $directLevel2 > $direct2)
		{
			$validator ->setError("Direct Level2 line is exceed more than assigned !!");
		}
		if(!empty($indirectLevel1) && $indirectLevel1 > $indirect1)
		{
			$validator ->setError("Indirect Level1 line is exceed more than assigned !!");
		}
		if(!empty($indirectLevel2) && $indirectLevel2 > $indirect2)
		{
			$validator ->setError("Indirect Level2 line is exceed more than assigned !!");
		}
		if(!empty($qaLevel1) && $qaLevel1 > $qa1)
		{
			$validator ->setError("QA Level1 line is exceed more than assigned !!");
		}
		if(!empty($qaLevel2) && $qaLevel2 > $qa2)
		{
			$validator ->setError("QA Level2 line is exceed more than assigned !!");
		}
		if(!empty($auditLevel1) && $auditLevel1 > $audit1)
		{
			$validator ->setError("Post Audit Level1 line is exceed more than assigned !!");
		}
		if(!empty($auditLevel2) && $auditLevel2 > $audit2)
		{
			$validator ->setError("Post Audit Level2 line is exceed more than assigned !!");
		}
		if(!empty($_FILES['workedOnFile']['name']))
		{
			$uploadFileName		=   $_FILES['workedOnFile']['name'];
			$extension		    =	findexts($uploadFileName);
			if($extension != "xlsx")
			{
				$validator ->setError("Please Only Upload .xlsx file !!");
			}
		}

		$validator ->checkField($comments,"","Please Enter Comments !!");
		$dataValid	 =	$validator ->isDataValid();
		if($dataValid)
		{
			$optionQuery	=	" SET employeeId=$employeeId,platform=$platform,customerId=$customerId,comments='$comments',directLevel1=$directLevel1,directLevel2=$directLevel2,indirectLevel1=$indirectLevel1,indirectLevel2=$indirectLevel2,qaLevel1=$qaLevel1,qaLevel2=$qaLevel2,auditLevel1=$auditLevel1,auditLevel2=$auditLevel2,workAddedBy=$s_employeeId,workAddedOn='".CURRENT_DATE_INDIA."'";

			$query	=	"UPDATE employee_works".$optionQuery.",ip='".VISITOR_IP_ADDRESS."' WHERE workId=$workId AND employeeId=$employeeId AND assignedWorkId=$assignedWorkId";
			dbQuery($query);

			$employeeObj->updateRevEmployeeWorkRates($workId,$employeeId,$platform,$customerId,$directLevel1,$directLevel2,$indirectLevel1,$indirectLevel2,$qaLevel1,$qaLevel2,$auditLevel1,$auditLevel2);

			if(!empty($_FILES['workedOnFile']['name']))
			{
				$uploadFileName		=   $_FILES['workedOnFile']['name'];
				$extension		    =	findexts($uploadFileName);
				
				mysql_query("UPDATE employee_works SET hasUploadFile=1,uploadFileName='$uploadFileName',extension='$extension' WHERE employeeId=$employeeId AND assignedWorkId=$assignedWorkId AND workId=$workId");
			}
			if($totalLinesAssigned <= $toatlLines)
			{
				$query	=	"UPDATE assign_employee_works SET status=2,completedOn='".CURRENT_DATE_INDIA."' WHERE assignedWorkId=$assignedWorkId AND employeeId=$employeeId";
				mysql_query($query);
			}
			else
			{
				if($status	== 2)
				{
					$query1			=	"SELECT * FROM assign_employee_works WHERE assignedWorkId=$assignedWorkId AND employeeId=$employeeId AND status=2";
					$result1		=	mysql_query($query1);
					if(mysql_num_rows($result1))
					{
						mysql_query("UPDATE assign_employee_works SET status=1,completedOn='0000-00-00' WHERE assignedWorkId=$assignedWorkId AND employeeId=$employeeId");

					}
				}
			}
			

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES ."/edit-rev-daily-works.php?employeeId=$employeeId&workId=$workId&ID=$assignedWorkId&success=1");
			exit();
		}
		else
		{
			
			$errorMsg	 =	$validator ->getErrors();
			include($form);
		}
	}
	else
	{
		include($form);
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
	?>
