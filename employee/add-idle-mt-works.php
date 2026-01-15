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

	$workId						=	0;
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
			echo "<script>window.close();</script>";
		}
		else
		{
			$text			=	"ADD WORK FOR ".$employeeName." ON ".showDate($t_workedOn);
		}
	}
	else
	{
		echo "<script>window.close();</script>";
	}
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
	<?php echo $text;?>
</title>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
<body>
<center>
<?php
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		//print_r($_REQUEST);
		//die();
		$transcriptionUserId		=	trim($transcriptionUserId);
		$qaUserId					=	trim($qaUserId);
		$vreUserId					=	trim($vreUserId);
		$auditUserId				=	trim($auditUserId);
		$comments					=	trim($comments);

		$validator ->checkField($platform,"","Please Select A Platform !!");
		$validator ->checkField($customerId,"","Please Select A Client !!");
		if(!empty($platform))
		{
			if($platform <= 3)
			{
				if(empty($transcriptionLinesEntered) && empty($vreLinesEntered) && empty($qaLinesEntered) && empty($indirectTranscriptionLinesEntered) && empty($indirectVreLinesEntered) && empty($indirectQaLinesEntered) && empty($auditLinesEntered) && empty($indirectAuditLinesEntered))
				{
					$validator ->setError("Please Enter Lines !!");
				}
				$display					=	"";
				$display1					=	"none";
			}
			else
			{
				if(empty($directLevel1) && empty($directLevel2) && empty($indirectLevel1) && empty($indirectLevel2) && empty($qaLevel1) && empty($qaLevel2) && empty($auditLevel1) && empty($auditLevel2))
				{
					$validator ->setError("Please Enter Lines !!");
				}
				$display					=	"none";
				$display1					=	"";
			}
		}
		$dataValid	 =	$validator ->isDataValid();
		if($dataValid)
		{
			if($platform <= 3)
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
				$directLevel1				=	0;
				$directLevel2				=	0;
				$indirectLevel1				=	0;
				$indirectLevel2				=	0;
				$qaLevel1					=	0;
				$qaLevel2					=	0;
				$auditLevel1				=	0;
				$auditLevel2				=	0;

			}
			else
			{
				if($directLevel1	==	"")
				{
					$directLevel1	=	0;
				}
				else
				{
					$directLevel1	=	trim($directLevel1);
				}

				if($directLevel2	==	"")
				{
					$directLevel2	=	0;
				}
				else
				{
					$directLevel2	=	trim($directLevel2);
				}

				if($indirectLevel1	==	"")
				{
					$indirectLevel1	=	0;
				}
				else
				{
					$indirectLevel1	=	trim($indirectLevel1);
				}

				if($indirectLevel2	==	"")
				{
					$indirectLevel2	=	0;
				}
				else
				{
					$indirectLevel2	=	trim($indirectLevel2);
				}

				if($qaLevel1	==	"")
				{
					$qaLevel1	=	0;
				}
				else
				{
					$qaLevel1	=	trim($qaLevel1);
				}

				if($qaLevel2	==	"")
				{
					$qaLevel2	=	0;
				}
				else
				{
					$qaLevel2	=	trim($qaLevel2);
				}

				if($auditLevel1	==	"")
				{
					$auditLevel1	=	0;
				}
				else
				{
					$auditLevel1	=	trim($auditLevel1);
				}

				if($auditLevel2	==	"")
				{
					$auditLevel2	=	0;
				}
				else
				{
					$auditLevel2	=	trim($auditLevel2);
				}
				$transcriptionLinesEntered			=	0;
				$vreLinesEntered					=	0;
				$qaLinesEntered						=	0;
				$auditLinesEntered					=	0;
				$indirectTranscriptionLinesEntered	=	0;
				$indirectVreLinesEntered			=	0;
				$indirectQaLinesEntered				=	0;
				$indirectAuditLinesEntered			=	0;
				$transcriptionUserId				=	"";
				$vreUserId							=	"";
				$qaUserId							=	"";
				$auditUserId						=	"";
			}

			$optionQuery	=	" SET employeeId=$employeeId,platform=$platform,customerId=$customerId,transcriptionLinesEntered=$transcriptionLinesEntered,vreLinesEntered=$vreLinesEntered,qaLinesEntered=$qaLinesEntered,auditLinesEntered=$auditLinesEntered,indirectTranscriptionLinesEntered=$indirectTranscriptionLinesEntered,indirectVreLinesEntered=$indirectVreLinesEntered,indirectQaLinesEntered=$indirectQaLinesEntered,indirectAuditLinesEntered=$indirectAuditLinesEntered,comments='$comments',transcriptionUserId='$transcriptionUserId',vreUserId='$vreUserId',qaUserId='$qaUserId',auditUserId='$auditUserId',directLevel1=$directLevel1,directLevel2=$directLevel2,indirectLevel1=$indirectLevel1,indirectLevel2=$indirectLevel2,qaLevel1=$qaLevel1,qaLevel2=$qaLevel2,auditLevel1=$auditLevel1,auditLevel2=$auditLevel2,workAddedBy=$s_employeeId,workAddedOn='$t_workedOn'";

			if(empty($workId))
			{
				$query	=	"INSERT INTO employee_works".$optionQuery.",workedOn='$t_workedOn',addedOn='$t_workedOn',addedTime='".CURRENT_TIME_INDIA."',ip='".VISITOR_IP_ADDRESS."'";
				mysql_query($query);
				$workId	=	mysql_insert_id();

				$employeeObj->updateMtEmployeeWorkRates($workId,$employeeId,$platform,$customerId,$transcriptionLinesEntered,$vreLinesEntered,$qaLinesEntered,$auditLinesEntered,$indirectTranscriptionLinesEntered,$indirectVreLinesEntered,$indirectQaLinesEntered,$indirectAuditLinesEntered);
			}
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
	}
?>

<br><br>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
</center>
</body>
</html>