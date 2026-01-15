<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	."/includes/common-array.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");
	$employeeObj				=	new employee();
	$validator					=	new validate();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	$form						=	SITE_ROOT_EMPLOYEES."/forms/add-edit-work.php";
	$workId						=	0;
	$text						=	"Add A ";
	$errorMsg					=	"";
	$platform					=	"";
	$customerId					=	0;
	$transcriptionDirect		=	0;
	$transcriptionIndirect		=	0;
	$transcriptionDirectCheck	=	"";
	$transcriptionIndirectCheck	=	"";
	$transcriptionLinesEntered	=	"";
	$indirectTranscriptionLinesEntered	=	"";
	$vreDirect					=	0;
	$vreIndirect				=	0;
	$vreDirectCheck				=	"";
	$vreIndirectCheck			=	"";
	$vreLinesEntered			=	"";
	$indirectVreLinesEntered	=	"";
	$qaDirect					=	0;
	$qaIndirect					=	0;
	$qaDirectCheck				=	"";
	$qaIndirectCheck			=	"";
	$qaLinesEntered				=	"";
	$indirectQaLinesEntered		=	"";
	$propertiesLinesEntered		=	"";
	$indirectpropertiesLinesEntered	=	"";
	$workedOn					=	date("d-m-Y");
	$t_workedOn					=	"0000-00-00";
	$success					=	"";
	$successText				=	"added";
	$comments					=	"";

	$transcriptionUserId		=	"";
	$vreUserId					=	"";
	$qaUserId					=	"";
	$propertiesUserId			=	"";
	
	$display					=	"";
	$display1					=	"none";


	if(isset($_GET['success']))
	{
		$success	=	$_GET['success'];
		if($success	==	2)
		{
			$successText		=	"edited";
		}
	}

	if(isset($_GET['ID']))
	{
		$workId	=	$_GET['ID'];
		$query	=	"SELECT * FROM employee_works where employeeId=$s_employeeId AND workId=$workId AND addedOn=CURRENT_DATE";
		$result		=	mysql_query($query);
		if(mysql_num_rows($result))
		{
			$text						=	"Edit A ";
			$row						=	mysql_fetch_assoc($result);
			$workId						=	$row['workId'];
			$platform					=	$row['platform'];
			$customerId					=	$row['customerId'];
			
			$transcriptionLinesEntered	=	$row['transcriptionLinesEntered'];
			
			$vreLinesEntered			=	$row['vreLinesEntered'];
			
			$qaLinesEntered				=	$row['qaLinesEntered'];
			$indirectTranscriptionLinesEntered	=	$row['indirectTranscriptionLinesEntered'];
			$indirectVreLinesEntered	=	$row['indirectVreLinesEntered'];
			$indirectQaLinesEntered		=	$row['indirectQaLinesEntered'];
			$t_workedOn					=	$row['workedOn'];
			$comments					=	$row['comments'];

			$propertiesLinesEntered		=	$row['propertiesLinesEntered'];
			$indirectpropertiesLinesEntered	=	$row['indirectpropertiesLinesEntered'];

			$transcriptionUserId		=	$row['transcriptionUserId'];
			$vreUserId					=	$row['vreUserId'];
			$qaUserId					=	$row['qaUserId'];
			$propertiesUserId			=	$row['propertiesUserId'];
			
			if($platform > 3)
			{
				$display					=	"none";
				$display1					=	"";
			}

			list($year,$month,$day)	=	explode("-",$t_workedOn);
			$workedOn				=	$day."-".$month."-".$year;

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
			if($propertiesLinesEntered	==	0)
			{
				$propertiesLinesEntered	=	"";
			}
			if($indirectpropertiesLinesEntered	==	0)
			{
				$indirectpropertiesLinesEntered	=	"";
			}
		}
		else
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}
	}

	if(!empty($success))
	{
?>
	<table cellpadding="3" cellspacing="2" width="98%" border="0" align="center">
		<tr>
			<td>
				<font class="text">You have successfully <?php echo $successText;?> your work.</font><br>
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
		$validator ->checkField($platform,"","Please Select A Platform !!");
		$validator ->checkField($customerId,"","Please Select A Client !!");
		if(!empty($platform))
		{
			if($platform <= 3)
			{
				if(empty($transcriptionLinesEntered) && empty($vreLinesEntered) && empty($qaLinesEntered) && empty($indirectTranscriptionLinesEntered) && empty($indirectVreLinesEntered) && empty($indirectQaLinesEntered))
				{
					$validator ->setError("Please Enter Lines !!");
				}
			}
			else
			{
				if(empty($qaLines) &&  empty($propertiesLines))
				{
					$validator ->setError("Please Enter Lines !!");
					$display	=	"none";
					$display1	=	"";
				}
			}
		}
		$dataValid	 =	$validator ->isDataValid();
		if($dataValid)
		{
			list($day,$month,$year)	=	explode("-",$workedOn);
			$t_workedOn				=	$year."-".$month."-".$day;
			if($platform <= 3)
			{
				if($transcriptionLinesEntered	==	"")
				{
					$transcriptionLinesEntered	=	0;
				}
				if($vreLinesEntered	==	"")
				{
					$vreLinesEntered	=	0;
				}
				if($qaLinesEntered	==	"")
				{
					$qaLinesEntered	=	0;
				}
				if($indirectTranscriptionLinesEntered	==	"")
				{
					$indirectTranscriptionLinesEntered	=	0;
				}
				if($indirectVreLinesEntered	==	"")
				{
					$indirectVreLinesEntered	=	0;
				}
				if($indirectQaLinesEntered	==	"")
				{
					$indirectQaLinesEntered	=	0;
				}
				if($propertiesLinesEntered	==	"")
				{
					$propertiesLinesEntered	=	0;
				}
				if($indirectpropertiesLinesEntered	==	"")
				{
					$indirectpropertiesLinesEntered	=	0;
				}
			}
			else
			{
				$qaLinesEntered			=	$qaLines;
				$propertiesLinesEntered	=	$propertiesLines;
				if($qaLinesEntered	==	"")
				{
					$qaLinesEntered	=	0;
				}
				if($propertiesLinesEntered	==	"")
				{
					$propertiesLinesEntered	=	0;
				}
				$transcriptionLinesEntered			=	0;
				$vreLinesEntered					=	0;
				$indirectTranscriptionLinesEntered	=	0;
				$indirectVreLinesEntered			=	0;
				$indirectpropertiesLinesEntered		=	0;
				$indirectQaLinesEntered				=	0;
				$transcriptionUserId				=	"";
				$vreUserId							=	"";
				$qaUserId							=	"";
				$propertiesUserId					=	"";
			}

			$employeeObj->addEditWorks($workId,$s_employeeId,$platform,$customerId,$transcriptionLinesEntered,$indirectTranscriptionLinesEntered,$vreLinesEntered,$indirectVreLinesEntered,$qaLinesEntered,$indirectQaLinesEntered,$propertiesLinesEntered,$indirectpropertiesLinesEntered,$t_workedOn,$comments,$transcriptionUserId,$vreUserId,$qaUserId,$propertiesUserId);

			$sendSuccess	=	1;
			if(!empty($workId))
			{
				$sendSuccess=	2;
			}
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES ."/add-work.php?success=$sendSuccess");
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