<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");
	$employeeObj				=	new employee();
	$validator					=	new validate();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	list($currentY,$currentM,$currentD)	=	explode("-",$nowDateIndia);
	$datewiseID					=	0;
	$employeeId					=	0;
	$text						=	"";
	$errorMsg					=	"";
	$platform					=	"";
	$customerId					=	0;
	$display					=	"";
	$display1					=	"none";
	$transcriptionLinesEntered	=	"";
	$indirectTranscriptionLinesEntered	=	"";
	$transcriptionUserId		=	"";
	$vreLinesEntered			=	"";
	$indirectVreLinesEntered	=	"";
	$qaLinesEntered				=	"";
	$indirectQaLinesEntered		=	"";
	$auditLinesEntered			=	"";
	$indirectAuditLinesEntered	=	"";
	$qaUserId					=	"";
	$vreUserId					=	"";
	$auditUserId				=	"";
	$comments					=	"";

	$directLevel1				=	"";
	$directLevel2				=	"";
	$indirectLevel1				=	"";
	$indirectLevel2				=	"";
	$qaLevel1					=	"";
	$qaLevel2					=	"";
	$auditLevel1				=	"";
	$auditLevel2				=	"";
	$success					=	"";
	$successText				=	"added";
	$workedOn					=	"";
	$t_workedOn					=	"0000-00-00";
	$employeeName				=	"";
	$date						=	"";
	$edit						=	0;
	$isOldRecord				=	0;
	$totalOldLines				=	0;
	$totalNewLines				=	0;

	$form						=	SITE_ROOT_EMPLOYEES."/forms/add-daily-work.php";

	if(isset($_GET['date']) && isset($_GET['ID']))
	{
		$date					=	$_GET['date'];
		$employeeId				=	(int)$_GET['ID'];
		$workedOn				=	$date;
		list($day,$month,$year)	=	explode("-",$date);
		$t_workedOn				=	$year."-".$month."-".$day;
		if(!$employeeName		=	$employeeObj->getEmployeeName($employeeId))
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}
		else
		{
			$text			=	"ADD WORK FOR ".$employeeName." ON ".showDate($t_workedOn);
		}
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	if(isset($_GET['datewiseID']))
	{
		$datewiseID		=	$_GET['datewiseID'];
		$query			=	"SELECT * FROM datewise_employee_works_money WHERE ID > ".MAX_SEARCH_MT_EMPLOYEE_WORKID." AND employeeId=$employeeId AND ID=$datewiseID AND workedOnDate='$t_workedOn'";
		$result		=	mysql_query($query);
		if(mysql_num_rows($result))
		{
			$edit						=	1;
			$text						=	"EDIT WORK FOR ".$employeeName." ON ".showDate($t_workedOn);
			$row						=	mysql_fetch_assoc($result);
			$platform					=	$row['platform'];
			$customerId					=	$row['customerId'];
			$transcriptionLinesEntered	=	$row['totalDirectTrascriptionLines'];
			$vreLinesEntered			=	$row['totalDirectVreLines'];
			$qaLinesEntered						=	$row['totalQaLines'];
			$indirectTranscriptionLinesEntered	=	$row['totalIndirectTrascriptionLines'];
			$indirectVreLinesEntered	=	$row['totalIndirectVreLines'];
			$indirectQaLinesEntered		=	$row['totalIndirectQaLines'];
			$comments					=	$row['comments'];

			$auditLinesEntered			=	$row['totalDirectAuditLines'];
			$indirectAuditLinesEntered	=	$row['totalIndirectAuditLines'];

			$transcriptionUserId		=	$row['transcriptionUserId'];
			$vreUserId					=	$row['vreUserId'];
			$qaUserId					=	$row['qaUserId'];
			$auditUserId				=	$row['auditUserId'];
			
			$isOldRecord				=	1;
			$totalOldLines				=	$transcriptionLinesEntered+$vreLinesEntered+$qaLinesEntered+$indirectTranscriptionLinesEntered+$indirectVreLinesEntered+$indirectQaLinesEntered+$auditLinesEntered+$indirectAuditLinesEntered;
			
			
			if($transcriptionLinesEntered	==	0)
			{
				$transcriptionLinesEntered	=	"";
			}
			if($vreLinesEntered	==	0)
			{
				$vreLinesEntered	=	"";
			}
			if($qaLinesEntered	==	0)
			{
				$qaLinesEntered	=	"";
			}
			if($indirectTranscriptionLinesEntered	==	0)
			{
				$indirectTranscriptionLinesEntered	=	"";
			}
			if($indirectVreLinesEntered	==	0)
			{
				$indirectVreLinesEntered	=	"";
			}
			if($indirectQaLinesEntered	==	0)
			{
				$indirectQaLinesEntered	=	"";
			}
			if($auditLinesEntered	==	0)
			{
				$auditLinesEntered	=	"";
			}
			if($indirectAuditLinesEntered	==	0)
			{
				$indirectAuditLinesEntered	=	"";
			}
		}
		else
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}
	}


	if(isset($_GET['success']))
	{
		$success	=	$_GET['success'];
		if($success	==	2)
		{
			$successText		=	"edited";
		}
	}
	if(!empty($success))
	{
?>
	<table cellpadding="3" cellspacing="2" width="98%" border="0" align="center">
		<tr>
			<td>
				<font class="title">You have successfully <?php echo $successText;?> work.</font><br>
			</td>
		</tr>
		<tr>
			<td height="80"></td>
		</tr>
	</table>
<?php
	}
	elseif(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		//print_r($_REQUEST);
		//die();
		$transcriptionUserId		=	trim($transcriptionUserId);
		$qaUserId					=	trim($qaUserId);
		$vreUserId					=	trim($vreUserId);
		$auditUserId				=	trim($auditUserId);
		$comments					=	trim($comments);

		$validator ->checkField($platform,"","Please Select A Platform.");
		$validator ->checkField($customerId,"","Please Select A Client.");
		if(!empty($platform))
		{
			if(empty($transcriptionLinesEntered) && empty($vreLinesEntered) && empty($qaLinesEntered) && empty($indirectTranscriptionLinesEntered) && empty($indirectVreLinesEntered) && empty($indirectQaLinesEntered) && empty($auditLinesEntered) && empty($indirectAuditLinesEntered))
			{
				$validator ->setError("Please Enter Lines.");
			}
		}
		$dataValid	 =	$validator ->isDataValid();
		if($dataValid)
		{
			if($transcriptionLinesEntered	==	"")
			{
				$transcriptionLinesEntered	=	0;
			}
			else
			{
				$transcriptionLinesEntered	=	trim($transcriptionLinesEntered);
			}

			if($vreLinesEntered	==	"")
			{
				$vreLinesEntered	=	0;
			}
			else
			{
				$vreLinesEntered	=	trim($vreLinesEntered);
			}

			if($qaLinesEntered	==	"")
			{
				$qaLinesEntered	=	0;
			}
			else
			{
				$qaLinesEntered	=	trim($qaLinesEntered);
			}

			if($auditLinesEntered	==	"")
			{
				$auditLinesEntered	=	0;
			}
			else
			{
				$auditLinesEntered	=	trim($auditLinesEntered);
			}

			if($indirectTranscriptionLinesEntered	==	"")
			{
				$indirectTranscriptionLinesEntered	=	0;
			}
			else
			{
				$indirectTranscriptionLinesEntered	=	trim($indirectTranscriptionLinesEntered);
			}

			if($indirectVreLinesEntered	==	"")
			{
				$indirectVreLinesEntered	=	0;
			}
			else
			{
				$indirectVreLinesEntered	=	trim($indirectVreLinesEntered);
			}

			if($indirectQaLinesEntered	==	"")
			{
				$indirectQaLinesEntered	=	0;
			}
			else
			{
				$indirectQaLinesEntered	=	trim($indirectQaLinesEntered);
			}

			if($indirectAuditLinesEntered	==	"")
			{
				$indirectAuditLinesEntered	=	0;
			}
			else
			{
				$indirectAuditLinesEntered	=	trim($indirectAuditLinesEntered);
			}
	

			$datewiseID	=	$employeeObj->addEditMtWorksRates($datewiseID,$employeeId,$platform,$customerId,$transcriptionLinesEntered,$vreLinesEntered,$qaLinesEntered,$auditLinesEntered,$indirectTranscriptionLinesEntered,$indirectVreLinesEntered,$indirectQaLinesEntered,$indirectAuditLinesEntered,$comments,$transcriptionUserId,$vreUserId,$qaUserId,$auditUserId);

			dbQuery("UPDATE datewise_employee_works_money SET workedOnDate='$t_workedOn' WHERE ID=$datewiseID AND employeeId=$employeeId");

			$totalNewLines		=	$transcriptionLinesEntered+$vreLinesEntered+$qaLinesEntered+$auditLinesEntered+$indirectTranscriptionLinesEntered+$indirectVreLinesEntered+$indirectQaLinesEntered+$indirectAuditLinesEntered;

			$employeeObj->addMtEmployeeTargetLines($employeeId,$employeeName,$currentM,$currentY,$totalNewLines,$isOldRecord,$totalOldLines);

			if(empty($edit))
			{
				$sendSuccess	=	1;
				$link			=	"";
			}
			else
			{
				$sendSuccess=	2;
				$link		=	"&datewiseID=".$datewiseID;
			}

			
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES ."/view-employee-work.php?ID=$employeeId&date=$date&success=$sendSuccess".$link);
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
		
		//echo "<br><br><br><br><br><br><br><center><font style='color:#4d4d4d;font-family:arial;font-family:arial;font-size:16px;'> Due to  some technical upgration we are disabled this interface. <br>Our technical team working on it. It should not take more than 2 Hours.<br><br> Thanks for your patience.</font><br><br><br><br><br><br><br></center>";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>