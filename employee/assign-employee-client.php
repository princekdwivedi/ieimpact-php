<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
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
	$rateId						=	0;
	$assignedId					=	0;
	$employeeId					=   0;
	$employeeName				=   0;
	$departmentId				=	0;
	$shiftId					=	0;
	$platform					=	0;
	$customerId					=	0;
	$text						=	"Assign";

	$displayDictaphone			=	"none";
	$displayEscription			=	"none";
	$displayNetcare				=	"none";
	$displayProperties			=	"none";
	$displayPdfReports			=	"none";

	$directTranscriptionRate	=	"";
	$indirectTranscriptionRate	=	"";
	$directVreRate				=	"";
	$indirectVreRate			=	"";
	$directQaRate				=	"";
	$indirectQaRate				=	"";
	$directAuditRate			=	"";
	$indirectAuditRate			=	"";

	$directLevel1Rate			=	"";
	$directLevel2Rate			=	"";
	$indirectLevel1Rate			=	"";
	$indirectLevel2Rate			=	"";
	$qaLevel1Rate				=	"";
	$qaLevel2Rate				=	"";
	$auditLevel1Rate			=	"";
	$auditLevel2Rate			=	"";

	$a_employeePlatform			=	array();
	$a_employeeExistingPlatform	=	array();
	$a_dictaphoneClients		=	array();
	$a_escriptionClients		=	array();
	$a_netcareClients			=	array();
	$a_propertiesClients		=	array();
	$a_pdfClients				=	array();
		

	if(isset($_GET['ID']))
	{
		$employeeId		=   (int)$_GET['ID'];
		if($s_employeeId != $employeeId)
		{
			if($employeeName=	$employeeObj->getEmployeeName($employeeId))
			{
				$query		=	"SELECT * FROM employee_shift_rates WHERE employeeId=$employeeId";
				$result	=	mysql_query($query);
				if(mysql_num_rows($result))
				{
					$text						=	"Edit ";
					$row						=	mysql_fetch_assoc($result);
					$rateId						=	$row['rateId'];
					$departmentId				=	$row['departmentId'];
					$shiftId					=	$row['shiftId'];
					$directTranscriptionRate	=	$row['directTranscriptionRate'];
					$indirectTranscriptionRate	=	$row['indirectTranscriptionRate'];
					$directVreRate				=	$row['directVreRate'];
					$indirectVreRate			=	$row['indirectVreRate'];
					$directQaRate				=	$row['directQaRate'];
					$indirectQaRate				=	$row['indirectQaRate'];
					$directAuditRate			=	$row['directAuditRate'];
					$indirectAuditRate			=	$row['indirectAuditRate'];

					$directLevel1Rate			=	$row['directLevel1Rate'];
					$directLevel2Rate			=	$row['directLevel2Rate'];
					$indirectLevel1Rate			=	$row['indirectLevel1Rate'];
					$indirectLevel2Rate			=	$row['indirectLevel2Rate'];
					$qaLevel1Rate				=	$row['qaLevel1Rate'];
					$qaLevel2Rate				=	$row['qaLevel2Rate'];
					$auditLevel1Rate			=	$row['auditLevel1Rate'];
					$auditLevel2Rate			=	$row['auditLevel2Rate'];

					if(empty($directTranscriptionRate))
					{
						$directTranscriptionRate =	"";
					}
					if(empty($indirectTranscriptionRate))
					{
						$indirectTranscriptionRate =	"";
					}
					if(empty($directVreRate))
					{
						$directVreRate =	"";
					}
					if(empty($indirectVreRate))
					{
						$indirectVreRate =	"";
					}
					if(empty($directQaRate))
					{
						$directQaRate =	"";
					}
					if(empty($indirectQaRate))
					{
						$indirectQaRate =	"";
					}
					if(empty($directAuditRate))
					{
						$directAuditRate =	"";
					}
					if(empty($indirectAuditRate))
					{
						$indirectAuditRate =	"";
					}
					if(empty($directLevel1Rate))
					{
						$directLevel1Rate =	"";
					}
					if(empty($directLevel2Rate))
					{
						$directLevel2Rate =	"";
					}
					if(empty($indirectLevel1Rate))
					{
						$indirectLevel1Rate =	"";
					}
					if(empty($indirectLevel2Rate))
					{
						$indirectLevel2Rate =	"";
					}
					if(empty($qaLevel1Rate))
					{
						$qaLevel1Rate =	"";
					}
					if(empty($qaLevel2Rate))
					{
						$qaLevel2Rate =	"";
					}
					if(empty($auditLevel1Rate))
					{
						$auditLevel1Rate =	"";
					}
					if(empty($auditLevel2Rate))
					{
						$auditLevel2Rate=	"";
					}

					$query1	=	"SELECT * FROM employee_clients WHERE employeeId=$employeeId AND rateId=$rateId";
					$result1=	mysql_query($query1);
					if(mysql_num_rows($result1))
					{
						while($row1	=	mysql_fetch_assoc($result1))
						{
							$platform		=	$row1['platform'];
							
							$a_employeePlatform[$platform]	=	$platform;
							$a_employeeExistingPlatform[$platform]	=	$platform;
							if($platform	== 1)
							{
								
								$displayDictaphone	=	"";
							}
							if($platform	==	2)
							{
								$displayEscription	=	"";
							}	
							if($platform	==	3)
							{
								$displayNetcare				=	"";
							}	
							if($platform	==	4)
							{
								$displayProperties			=	"";
							}	
							if($platform	==	5)
							{
								$displayPdfReports			=	"";
							}
						}
					}

					foreach($a_employeePlatform as $key=>$value)
					{
						if($key	== 1)
						{
							$clients	=	$employeeObj->getEmployeeClients($key,$employeeId,$rateId);
							if($clients)
							{
								$n_dictaphoneClients	=	explode(",",$clients);
								foreach($n_dictaphoneClients as $key=>$value)
								{
									$a_dictaphoneClients[$value]= $value;	
								}
							}
						}
						if($key	==	2)
						{
							$clients1	=	$employeeObj->getEmployeeClients($key,$employeeId,$rateId);
							if($clients1)
							{
								$n_escriptionClients	=	explode(",",$clients1);
								foreach($n_escriptionClients as $key=>$value)
								{
									$a_escriptionClients[$value]= $value;	
								}
							}
						}	
						if($key	==	3)
						{
							$clients2	=	$employeeObj->getEmployeeClients($key,$employeeId,$rateId);
							if($clients2)
							{
								$n_netcareClients	=	explode(",",$clients2);
								foreach($n_netcareClients as $key=>$value)
								{
									$a_netcareClients[$value]= $value;	
								}
							}
						}	
						if($key	==	4)
						{
							$clients3	=	$employeeObj->getEmployeeClients($key,$employeeId,$rateId);
							if($clients3)
							{
								$n_propertiesClients	=	explode(",",$clients3);
								foreach($n_propertiesClients  as $key=>$value)
								{
									$a_propertiesClients[$value]= $value;	
								}
							}
						}	
						if($key	==	5)
						{
							$clients4	=	$employeeObj->getEmployeeClients($key,$employeeId,$rateId);
							if($clients4)
							{
								$n_pdfClients	=	explode(",",$clients4);
								foreach($n_pdfClients as $key=>$value)
								{
									$a_pdfClients[$value]= $value;	
								}
							}
						}
					}

					
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
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$form		=	SITE_ROOT_EMPLOYEES  . "/forms/assign-employee-client.php";
	
?>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td colspan="2" class='title'><?php echo $text;?> <?php echo $employeeName;?>'s shift and clients </td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
</table>
<?php
if(isset($_GET['success']))
{
?>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td class='title' align="center">
			SUCCESSFULLY ADDED DEPARTMENT,SHIFT,CLINT AND RATES FOR <?php echo $employeeName;?>
		</td>
	</tr>
	<tr>
		<td align="center">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-employee-details.php" class='link_style3'>BACK TO EMPLOYEE !!</a>
		</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
</table>
<?php
}
elseif(isset($_REQUEST['formSubmitted']))
{
	extract($_REQUEST);
	//print_r($_REQUEST);
	$directTranscriptionRate	=	trim($directTranscriptionRate);
	$indirectTranscriptionRate	=	trim($indirectTranscriptionRate);
	$directVreRate				=	trim($directVreRate);
	$indirectVreRate			=	trim($indirectVreRate);
	$directQaRate				=	trim($directQaRate);
	$indirectQaRate				=	trim($indirectQaRate);
	$directAuditRate			=	trim($directAuditRate);
	$indirectAuditRate			=	trim($indirectAuditRate);

	$directLevel1Rate			=	trim($directLevel1Rate);
	$directLevel2Rate			=	trim($directLevel2Rate);
	$indirectLevel1Rate			=	trim($indirectLevel1Rate);
	$indirectLevel2Rate			=	trim($indirectLevel2Rate);
	$qaLevel1Rate				=	trim($qaLevel1Rate);
	$qaLevel2Rate				=	trim($qaLevel2Rate);
	$auditLevel1Rate			=	trim($auditLevel1Rate);
	$auditLevel2Rate			=	trim($auditLevel2Rate);

	if(empty($directTranscriptionRate))
	{
		$directTranscriptionRate =	0;
	}
	if(empty($indirectTranscriptionRate))
	{
		$indirectTranscriptionRate =	0;
	}
	if(empty($directVreRate))
	{
		$directVreRate =	0;
	}
	if(empty($indirectVreRate))
	{
		$indirectVreRate =	0;
	}
	if(empty($directQaRate))
	{
		$directQaRate =	0;
	}
	if(empty($indirectQaRate))
	{
		$indirectQaRate =	0;
	}
	if(empty($directAuditRate))
	{
		$directAuditRate =	0;
	}
	if(empty($indirectAuditRate))
	{
		$indirectAuditRate =	0;
	}
	if(empty($directLevel1Rate))
	{
		$directLevel1Rate =	0;
	}
	if(empty($directLevel2Rate))
	{
		$directLevel2Rate =	0;
	}
	if(empty($indirectLevel1Rate))
	{
		$indirectLevel1Rate =	0;
	}
	if(empty($indirectLevel2Rate))
	{
		$indirectLevel2Rate =	0;
	}
	if(empty($qaLevel1Rate))
	{
		$qaLevel1Rate =	0;
	}
	if(empty($qaLevel2Rate))
	{
		$qaLevel2Rate =	0;
	}
	if(empty($auditLevel1Rate))
	{
		$auditLevel1Rate =	0;
	}
	if(empty($auditLevel2Rate))
	{
		$auditLevel2Rate=	0;
	}
	
	if(isset($_POST['platform']))
	{
		$a_employeePlatform		=	$_POST['platform'];
	}
	$validator ->checkField($departmentId,"","Please select a department !!");
	$validator ->checkField($shiftId,"","Please select a shift !!");
	if(empty($a_employeePlatform))
	{
		$validator ->setError("Please select a platform !!");
	}
	else
	{
		foreach($a_employeePlatform as $key=>$value)
		{
			if($key	== 1)
			{
				$displayDictaphone	=	"";
				if(isset($_POST['dictaphone']))
				{
					$a_dictaphoneClients		=	$_POST['dictaphone'];
				}
				if(empty($a_dictaphoneClients))
				{
					$validator ->setError("Please select Dictaphone client !!");
				}

			}
			if($key	==	2)
			{
				$displayEscription	=	"";
				if(isset($_POST['escription']))
				{
					$a_escriptionClients		=	$_POST['escription'];
				}
				if(empty($a_escriptionClients))
				{
					$validator ->setError("Please select Escription client !!");
				}
			}	
			if($key	==	3)
			{
				$displayNetcare				=	"";
				if(isset($_POST['netcare']))
				{
					$a_netcareClients		=	$_POST['netcare'];
				}
				if(empty($a_netcareClients))
				{
					$validator ->setError("Please select Netcare Clients client !!");
				}
			}	
			if($key	==	4)
			{
				$displayProperties			=	"";
				if(isset($_POST['properties']))
				{
					$a_propertiesClients		=	$_POST['properties'];
				}
				if(empty($a_propertiesClients))
				{
					$validator ->setError("Please select Properties client !!");
				}
			}	
			if($key	==	5)
			{
				$displayPdfReports			=	"";
				if(isset($_POST['pdf']))
				{
					$a_pdfClients			=	$_POST['pdf'];
				}
				if(empty($a_pdfClients))
				{
					$validator ->setError("Please select PDF client !!");
				}
			}
			/*if($key	<= 3)
			{
				if(empty($directTranscriptionRate) && empty($indirectTranscriptionRate) && empty($directVreRate) && empty($indirectVreRate) && empty($directQaRate) && empty($indirectQaRate) && empty($directAuditRate) && empty($indirectAuditRate))
				{
					$validator ->setError("Please enter rate !!");
				}
			}
			else
			{
				if(empty($directLevel1Rate) && empty($directLevel2Rate) && empty($indirectLevel1Rate) && empty($indirectLevel2Rate) && empty($qaLevel1Rate) && empty($qaLevel2Rate) && empty($auditLevel1Rate) && empty($auditLevel2Rate))
				{
					$validator ->setError("Please enter rate !!");
				}
			}*/
		}
	}
	$dataValid	 =	$validator ->isDataValid();
	if($dataValid)
	{
		
		//print_r($a_employeeExistingPlatform);

		//print_r($a_employeePlatform);
		foreach($a_employeeExistingPlatform as $key=>$value)
		{
			if(!array_key_exists($key,$a_employeePlatform))
			{
				mysql_query("DELETE FROM employee_clients WHERE platform=$key AND employeeId=$employeeId AND rateId=$rateId");
			}
		}
		$optionQuery	=	" SET employeeId=$employeeId,departmentId=$departmentId,shiftId=$shiftId,directTranscriptionRate=$directTranscriptionRate,indirectTranscriptionRate=$indirectTranscriptionRate,directVreRate=$directVreRate,indirectVreRate=$indirectVreRate,directQaRate=$directQaRate,indirectQaRate=$indirectQaRate,directAuditRate=$directAuditRate,indirectAuditRate=$indirectAuditRate,directLevel1Rate=$directLevel1Rate,directLevel2Rate=$directLevel2Rate,indirectLevel1Rate=$indirectLevel1Rate,indirectLevel2Rate=$indirectLevel2Rate,qaLevel1Rate=$qaLevel1Rate,qaLevel2Rate=$qaLevel2Rate,auditLevel1Rate=$auditLevel1Rate,auditLevel2Rate=$auditLevel2Rate";
		if(empty($rateId))
		{
			$query	=	"INSERT INTO employee_shift_rates ".$optionQuery.",addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',addedBy=$s_employeeId";
			mysql_query($query);
			$rateId	=	mysql_insert_id();
		}
		else
		{
			$query	=	"UPDATE employee_shift_rates ".$optionQuery." WHERE rateId=$rateId AND employeeId=$employeeId";
			mysql_query($query);
		}
		foreach($a_employeePlatform as $key=>$value)
		{
			if($key	== 1)
			{
				$a_dictaphoneClients	=	implode(",",$a_dictaphoneClients);
				
				if($firstExistAssignedId		=	$employeeObj->getEmployeeClientAssignedId($key,$employeeId,$rateId))
				{
					mysql_query("UPDATE employee_clients SET clientId='$a_dictaphoneClients' WHERE platform=$key AND employeeId=$employeeId AND rateId=$rateId");
				}
				else
				{
					if(!empty($a_dictaphoneClients))
					{
						mysql_query("INSERT INTO employee_clients SET platform=$key,clientId='$a_dictaphoneClients',employeeId=$employeeId,assignOn='".CURRENT_DATE_INDIA."',assignedTime='".CURRENT_TIME_INDIA."',addedBy=$s_employeeId,rateId=$rateId");
					}

				}
				
			}
			if($key	==	2)
			{
				$a_escriptionClients	=	implode(",",$a_escriptionClients);
				
				if($firstExistAssignedId		=	$employeeObj->getEmployeeClientAssignedId($key,$employeeId,$rateId))
				{
					mysql_query("UPDATE employee_clients SET clientId='$a_escriptionClients' WHERE platform=$key AND employeeId=$employeeId AND rateId=$rateId");
				}
				else
				{
					if(!empty($a_escriptionClients))
					{
						mysql_query("INSERT INTO employee_clients SET platform=$key,clientId='$a_escriptionClients',employeeId=$employeeId,assignOn='".CURRENT_DATE_INDIA."',assignedTime='".CURRENT_TIME_INDIA."',addedBy=$s_employeeId,rateId=$rateId");
					}

				}
			}	
			if($key	==	3)
			{
				$a_netcareClients	=	implode(",",$a_netcareClients);
				
				if($firstExistAssignedId		=	$employeeObj->getEmployeeClientAssignedId($key,$employeeId,$rateId))
				{
					mysql_query("UPDATE employee_clients SET clientId='$a_netcareClients' WHERE platform=$key AND employeeId=$employeeId AND rateId=$rateId");
				}
				else
				{
					if(!empty($a_netcareClients))
					{
						mysql_query("INSERT INTO employee_clients SET platform=$key,clientId='$a_netcareClients',employeeId=$employeeId,assignOn='".CURRENT_DATE_INDIA."',assignedTime='".CURRENT_TIME_INDIA."',addedBy=$s_employeeId,rateId=$rateId");
					}
				}
			}	
			if($key	==	4)
			{
				$a_propertiesClients	=	implode(",",$a_propertiesClients);
				
				if($firstExistAssignedId		=	$employeeObj->getEmployeeClientAssignedId($key,$employeeId,$rateId))
				{
					mysql_query("UPDATE employee_clients SET clientId='$a_propertiesClients' WHERE platform=$key AND employeeId=$employeeId AND rateId=$rateId");
				}
				else
				{
					if(!empty($a_propertiesClients))
					{
						mysql_query("INSERT INTO employee_clients SET platform=$key,clientId='$a_propertiesClients',employeeId=$employeeId,assignOn='".CURRENT_DATE_INDIA."',assignedTime='".CURRENT_TIME_INDIA."',addedBy=$s_employeeId,rateId=$rateId");
					}
				}
			}	
			if($key	==	5)
			{
				$a_pdfClients	=	implode(",",$a_pdfClients);
				
				if($firstExistAssignedId		=	$employeeObj->getEmployeeClientAssignedId($key,$employeeId,$rateId))
				{
					mysql_query("UPDATE employee_clients SET clientId='$a_pdfClients' WHERE platform=$key AND employeeId=$employeeId AND rateId=$rateId");
				}
				else
				{
					if(!empty($a_pdfClients))
					{
						mysql_query("INSERT INTO employee_clients SET platform=$key,clientId='$a_pdfClients',employeeId=$employeeId,assignOn='".CURRENT_DATE_INDIA."',assignedTime='".CURRENT_TIME_INDIA."',addedBy=$s_employeeId,rateId=$rateId");
					}
				}
			}
		}
		

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES ."/assign-employee-client.php?ID=$employeeId&success=1");
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