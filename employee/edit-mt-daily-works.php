<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");
	$employeeObj	=	new employee();
	$validator		=	new validate();
	$workId			=	0;
	$employeeId		=	0;
	$employeeNane	=	"";
	$errorMsg		=	"";
	if(!$s_hasManagerAccess)
	{
		echo "<script>window.close();</script>";
	}
	$platform					=	"";
	$customerId					=	0;
	
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

	$headingText				=	"";
	$totalOldLines				=	0;


	if(isset($_GET['ID']))
	{
		$datewiseID				=	(int)$_GET['ID'];

		$query					=	"SELECT datewise_employee_works_money .*,fullName FROM datewise_employee_works_money INNER JOIN employee_details ON datewise_employee_works_money.employeeId=employee_details.employeeId WHERE ID=$datewiseID";
		$result					=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			$row						=	mysql_fetch_assoc($result);
			$employeeId					=	$row['employeeId'];
			$datewiseID					=	$row['ID'];
			$platform					=	$row['platform'];
			$customerId					=	$row['customerId'];
			$transcriptionLinesEntered	=	$row['totalDirectTrascriptionLines'];
			$vreLinesEntered			=	$row['totalDirectVreLines'];
			$qaLinesEntered				=	$row['totalQaLines'];
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

			$employeeName				=	$row['fullName'];
		
			$workedOn					=	showDate($row['workedOnDate']);

			if($transcriptionLinesEntered			==	0)
			{
				$transcriptionLinesEntered			=	"";
			}
			if($vreLinesEntered						==	0)
			{
				$vreLinesEntered					=	"";
			}
			if($qaLinesEntered						==	0)
			{
				$qaLinesEntered						=	"";
			}
			if($indirectTranscriptionLinesEntered	==	0)
			{
				$indirectTranscriptionLinesEntered	=	"";
			}
			if($indirectVreLinesEntered				==	0)
			{
				$indirectVreLinesEntered			=	"";
			}
			if($indirectQaLinesEntered				==	0)
			{
				$indirectQaLinesEntered				=	"";
			}
			if($auditLinesEntered					==	0)
			{
				$auditLinesEntered					=	"";
			}
			if($indirectAuditLinesEntered			==	0)
			{
				$indirectAuditLinesEntered			=	"";
			}
			$headingText	=	"Edit Work Of ".ucwords($employeeName)." On ".$workedOn;

			$totalOldLines							=	$transcriptionLinesEntered+$vreLinesEntered+$qaLinesEntered+$indirectTranscriptionLinesEntered+$indirectVreLinesEntered+$indirectQaLinesEntered+$auditLinesEntered+$indirectAuditLinesEntered;
		}
		else
		{
			echo "<script>window.close();</script>";
		}
	}
	else
	{
		echo "<script>window.close();</script>";
	}
	$form	=	SITE_ROOT_EMPLOYEES."/forms/edit-daily-mt-work.php";
?>
<script type="text/javascript">
function reflectChange()
{
	window.opener.location.reload();
}
</script>
<html>
<head>
<title>
	<?php echo $headingText;?>
</title>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
<body>
<center>
<?php
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);

		$transcriptionUserId		=	trim($transcriptionUserId);
		$qaUserId					=	trim($qaUserId);
		$vreUserId					=	trim($vreUserId);
		$auditUserId				=	trim($auditUserId);
		$comments					=	trim($comments);

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

		$validator ->checkField($platform,"","Please Select A Platform !!");
		$validator ->checkField($customerId,"","Please Select A Client !!");

		if(empty($transcriptionLinesEntered) && empty($vreLinesEntered) && empty($qaLinesEntered) && empty($indirectTranscriptionLinesEntered) && empty($indirectVreLinesEntered) && empty($indirectQaLinesEntered) && empty($auditLinesEntered) && empty($indirectAuditLinesEntered))
		{
			$validator ->setError("Please Enter Lines !!");
		}
		$dataValid	 =	$validator ->isDataValid();
		if($dataValid)
		{
			$employeeObj->addEditMtWorksRates($datewiseID,$employeeId,$platform,$customerId,$transcriptionLinesEntered,$vreLinesEntered,$qaLinesEntered,$auditLinesEntered,$indirectTranscriptionLinesEntered,$indirectVreLinesEntered,$indirectQaLinesEntered,$indirectAuditLinesEntered,$comments,$transcriptionUserId,$vreUserId,$qaUserId,$auditUserId);

			$totalNewLines		=	$transcriptionLinesEntered+$vreLinesEntered+$qaLinesEntered+$auditLinesEntered+$indirectTranscriptionLinesEntered+$indirectVreLinesEntered+$indirectQaLinesEntered+$indirectAuditLinesEntered;



			$employeeObj->addMtEmployeeTargetLines($employeeId,$employeeName,$currentM,$currentY,$totalNewLines,1,$totalOldLines);

			echo "<script type='text/javascript'>reflectChange();</script>";

			echo "<script>window.close();</script>";
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
?>


<br><br>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
</center>
</body>
</html>
