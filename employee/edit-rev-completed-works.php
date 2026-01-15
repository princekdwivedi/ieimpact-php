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

	$assignedWorkId				=	0;

	if(isset($_GET['success']))
	{
		$success	=	$_GET['success'];
	}
	if(isset($_GET['employeeId']) && isset($_GET['ID']))
	{
		$employeeId		=	(int)$_GET['employeeId'];
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
		$completedOn		=	showDate($row['completedOn']);
		$status				=	$row['status'];
		$uploadedFileName	=	$row['uploadedFileName'];

		$platName		=	$employeeObj->getPlatformName($platform);
		$customerName	=	$employeeObj->getCustomerName($customerId,$platform);
			
		if($status	!=	2)
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
	
	$form						=	SITE_ROOT_EMPLOYEES."/forms/completed-rev-work-details.php";
?>
<table width="99%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='heading'>Edit Completed Work Of <?php echo $employeeName;?></td>
	</tr>
</table>
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
		<td class='title1' align="center">Successfully Updated completed Work !!</td>
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
		//print_r($_REQUEST);
		$direct1					=	trim($direct1);
		$direct2					=	trim($direct2);
		$indirect1					=	trim($indirect1);
		$indirect2					=	trim($indirect2);
		$qa1						=	trim($qa1);
		$qa2						=	trim($qa2);
		$audit1						=	trim($audit1);
		$audit2						=	trim($audit2);
		$comments					=	trim($assignComments);
		if(empty($direct1))
		{
			$direct1				=	0;
		}
		if(empty($direct2))
		{
			$direct2				=	0;
		}
		if(empty($indirect1))
		{
			$indirect1				=	0;
		}
		if(empty($indirect2))
		{
			$indirect2				=	0;
		}
		if(empty($qa1))
		{
			$qa1					=	0;
		}
		if(empty($qa2))
		{
			$qa2					=	0;
		}
		if(empty($audit1))
		{
			$audit1					=	0;
		}
		if(empty($audit2))
		{
			$audit2					=	0;
		}
		$validator ->checkField($platform,"","Please Select A Platform !!");
		$validator ->checkField($customerId,"","Please Select A Client !!");
		if(!empty($_FILES['assignFile']['name']))
		{
			$uploadedFileName   =   $_FILES['assignFile']['name'];
			$fileExtension	    =	findexts($uploadedFileName);
			if($fileExtension != "xlsx")
			{
				$validator ->setError("Please Only Upload .xlsx file !!");
			}
		}

		$dataValid	 =	$validator ->isDataValid();
		if($dataValid)
		{
			$optionQuery	=	" SET platform=$platform,customerId=$customerId,direct1=$direct1,direct2=$direct2,indirect1=$indirect1,indirect2=$indirect2,qa1=$qa1,qa2=$qa2,audit1=$audit1,audit2=$audit2,totalLinesAssigned=$totalLinesAssigned,comments='$comments'";
			
			$query	=	"UPDATE assign_employee_works".$optionQuery." WHERE assignedWorkId=$assignedWorkId AND status = 2 AND employeeId=$employeeId";
			mysql_query($query);

			if(!empty($_FILES['assignFile']['name']))
			{
				$uploadedFileName   =   $_FILES['assignFile']['name'];
				$fileExtension	    =	findexts($uploadedFileName);

				mysql_query("UPDATE assign_employee_works SET hasUploadedFile=1,uploadedFileName='$uploadedFileName',fileExtension='$fileExtension' WHERE assignedWorkId=$assignedWorkId AND status = 2 AND employeeId=$employeeId");
			}

			$a_directLevel1		=	$_POST['directLevel1'];
			$a_directLevel2		=	$_POST['directLevel2'];

			$a_indirectLevel1	=	$_POST['indirectLevel1'];
			$a_indirectLevel2	=	$_POST['indirectLevel2'];

			$a_qaLevel1			=	$_POST['qaLevel1'];
			$a_qaLevel2			=	$_POST['qaLevel2'];

			$a_auditLevel1		=	$_POST['auditLevel1'];
			$a_auditLevel2		=	$_POST['auditLevel2'];

			$a_total			=	$_POST['total'];

			$a_comments			=	$_POST['comments'];

			foreach($a_directLevel1 as $workId=>$directLevel1)
			{
				$directLevel2	=	$a_directLevel2[$workId];

				$indirectLevel1	=	$a_indirectLevel1[$workId];
				$indirectLevel2	=	$a_indirectLevel2[$workId];

				$qaLevel1		=	$a_qaLevel1[$workId];
				$qaLevel2		=	$a_qaLevel2[$workId];

				$auditLevel1	=	$a_auditLevel1[$workId];
				$auditLevel2	=	$a_auditLevel2[$workId];

				$workComments	=	$a_comments[$workId];
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
				if(empty($workComments))
				{
					$workComments	=	"";
				}

				$optionQuery	=	" SET employeeId=$employeeId,platform=$platform,customerId=$customerId,comments='$workComments',directLevel1=$directLevel1,directLevel2=$directLevel2,indirectLevel1=$indirectLevel1,indirectLevel2=$indirectLevel2,qaLevel1=$qaLevel1,qaLevel2=$qaLevel2,auditLevel1=$auditLevel1,auditLevel2=$auditLevel2,workAddedBy=$s_employeeId,workAddedOn='".CURRENT_DATE_INDIA."'";

				$query	=	"UPDATE employee_works".$optionQuery.",ip='".VISITOR_IP_ADDRESS."' WHERE workId=$workId AND employeeId=$employeeId AND assignedWorkId=$assignedWorkId";
				mysql_query($query);

				$employeeObj->updateRevEmployeeWorkRates($workId,$employeeId,$platform,$customerId,$directLevel1,$directLevel2,$indirectLevel1,$indirectLevel2,$qaLevel1,$qaLevel2,$auditLevel1,$auditLevel2);
			}
			if(!empty($_FILES['workFileName']['name']))
			{
				$a_workFileName		=	$_FILES['workFileName']['name'];
				foreach($a_workFileName as $workId=>$fileName)
				{
					if(!empty($fileName))
					{
						$t_extension	=	findexts($fileName);
						if($t_extension	== "xlsx")
						{
							mysql_query("UPDATE employee_works SET hasUploadFile=1,uploadFileName='$fileName',extension='$t_extension' WHERE assignedWorkId=$assignedWorkId AND workId=$workId");
						}
					}
				}
			}
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES ."/edit-rev-completed-works.php?employeeId=$employeeId&ID=$assignedWorkId&success=1");
			exit();

		}
		else
		{
			echo $errorMsg	 =	$validator ->getErrors();
			include($form);
		}

		
	}
	else
	{
		include($form);
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
	?>
