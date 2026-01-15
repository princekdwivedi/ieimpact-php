<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_MTEMPLOYEES	.   "/includes/top.php");
	include(SITE_ROOT_MTEMPLOYEES	.   "/includes/check-admin-login.php");
	include(SITE_ROOT				.   "/classes/pagingclass.php");
	include(SITE_ROOT				.	"/includes/send-mail.php");
	$pagingObj						=	new Paging();

	$whereClause					=	"WHERE hasPdfAccess=0";
	$orderBy						=	"firstName";
	$queryString					=	"";
	$andClause						=	"";
	$a_managers						=	$mtemployeeObj->getAllEmployeeManager();
	$managerId						=	0;

	if(isset($_REQUEST['recNo']))
	{
		$recNo						=	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo						=	0;
	}	

	if(isset($_GET['managerId'])){
		$managerId					=	(int)$_GET['managerId'];

		if(!empty($managerId)){
			$queryString			=	"&managerId=".$managerId;
			$andClause				=	" AND underManager=".$managerId;
		}
	}

	if(isset($_GET['employeeId']))
	{
		$employeeId				=	(int)$_GET['employeeId'];
		if(!empty($employeeId))
		{
			if($result			=   $mtemployeeObj->getActiveDeactiveEmployeeDetails($employeeId))
			{
				$row			=	mysqli_fetch_assoc($result);
				$email			=	$row['email'];
				$employeeName	=	stripslashes($row['fullName']);

				$link			 =	"?managerId=".$managerId;

				if(!empty($recNo))
				{
					$link		.=	"&recNo=".$recNo;
				}

				/////////////////////////// DEACTIVATE AN EMPLOYEE ///////////////////
				if(isset($_GET['removeManager']) && $_GET['removeManager'] == 1)
				{
					$managerId					=	(int)$_GET['managerId'];
					
					dbQuery("UPDATE employee_details SET underManager=0 WHERE employeeId=$employeeId");

												
					ob_clean();
					header("Location:".SITE_URL_MTEMPLOYEES."/add-remove-under-managers.php".$link."#".$employeeId);
				    exit();
				}

				/////////////////////////// DEACTIVATE AN EMPLOYEE ///////////////////
				if(isset($_GET['deactivateEmp']) && $_GET['deactivateEmp'] == 1)
				{
					$exactOriginalEmail			=	$email;
					$email						=	"OLD-".$email;
					$deactivatedDate			=	CURRENT_DATE_INDIA;
					
					/****************************** SENDING FEEDBACK EMAIL ************************/
					include(SITE_ROOT			.   "/classes/email-templates.php");
					$emailObj					=	new emails();

					$managerEmployeeEmailSubject=	"Please explain briefly about your experience with ieIMPACT ";
					
					
					$body						=	"Dear ".$employeeName.",<br />Explain your experience with ieIMPACT. Please <a href='".SITE_URL_EMPLOYEES."/exit-employee-feedback.php' target='_blank'>click here</a> to share your experince.<br /><br />";

					
					$a_templateData				=	array("{bodyMatter}"=>$body);
					$managerEmployeeFromName	=	"ieIMPACT";			

					$uniqueTemplateName			=	"TEMPLATE_SENDING_NEW_SIMPLEE_MESSAGE";
					$toEmail					=	$exactOriginalEmail;
					include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
					/*******************************************************************************/

					dbQuery("UPDATE employee_details SET isActive=0,email='$email',exactOriginalEmail='$exactOriginalEmail',deactivatedBy='$s_mtemployeeName',deactivatedDate='".CURRENT_DATE_INDIA."' WHERE employeeId=$employeeId");

					ob_clean();
					header("Location:".SITE_URL_MTEMPLOYEES."/add-remove-under-managers.php".$link."#".$employeeId);
				    exit();
				}

				/////////////////////////// DEACTIVATE AN EMPLOYEE ///////////////////
				if(isset($_GET['activateEmp']) && $_GET['activateEmp'] == 1)
				{
					$exactOriginalEmail			=	stripslashes($row['exactOriginalEmail']);
					
					if(!empty($exactOriginalEmail))
					{
						dbQuery("UPDATE employee_details SET email='$exactOriginalEmail',exactOriginalEmail='',isActive=1 WHERE employeeId=$employeeId");
					}
					else
					{
						dbQuery("UPDATE employee_details SET isActive=1,hasOutsideLoginAccess=1 WHERE employeeId=$employeeId");

						$from			=	"hr@ieimpact.com";
						$fromName		=	"HR ieIMPACT ";
		

						$templateId		=	ADMINISTRATOR_SENDING_ACTIVATE_EMPLOYEES;
						$mailSubject	=	"Activated your employee account in ieIMPACT";

						$a_templateData	=	array("{employeeName}"=>$employeeName,"{employeeEmail}"=>$email);

						sendTemplateMail($from, $fromName, $email, $mailSubject, $templateId, $a_templateData);
					}

					ob_clean();
					header("Location:".SITE_URL_MTEMPLOYEES."/add-remove-under-managers.php".$link."#".$employeeId);
				    exit();
				}
			}
		}
	}	

?>
	<form name="serachUnderManagers" action="" method="GET">
		<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
			<tr>
				<td width="38%" class="smalltext23"><b>SELECT MANAGER TO VIEW EMPLOYEE UNDER HIM/HER :</b></td>
				<td>
					<select name="managerId" onchange="document.serachUnderManagers.submit();">
						<option value="0">Select</option>
						<?php
							foreach($a_managers as $k=>$v)
							{
								$select		=	"";

								if($k		==	$managerId)
								{
									$select	=	"selected";
								}

								echo "<option value='$k' $select>$v</option>";

							}
						?>
					</select>
				</td>
			</tr>
		</table>
	</form>
	<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
	<link href="<?php echo SITE_URL_MTEMPLOYEES;?>/css/thickbox.css" media="screen" rel="stylesheet" type="text/css" />
	<script src="<?php echo SITE_URL_MTEMPLOYEES;?>/scripts/thickbox-big.js" type="text/javascript"></script>

	<script type='text/javascript'>
		function removeFromManagers(employeeId,managerId,recNo){
			var confirmation = window.confirm("Are you sure to remove this employee from under manager?");
			if(confirmation == true)
			{
				window.location.href='<?php echo SITE_URL_MTEMPLOYEES;?>/add-remove-under-managers.php?employeeId='+employeeId+'&managerId='+managerId+'&recNo='+recNo+'&removeManager=1';
			}
		}

		function activeDeactiveEmployee(employeeId,type,managerId,recNo)
		{
			if(type == 1)
			{
				var confirmation = window.confirm("Are you sure to deactivate this employee, a deactivation email will go to the employee?");
				if(confirmation == true)
				{
					window.location.href='<?php echo SITE_URL_MTEMPLOYEES;?>/add-remove-under-managers.php?managerId='+managerId+'&employeeId='+employeeId+'&deactivateEmp=1'+'&recNo='+recNo;
				}
			}
			else
			{
				var confirmation = window.confirm("Are you sure to activate this employee?");
				if(confirmation == true)
				{
					window.location.href='<?php echo SITE_URL_MTEMPLOYEES;?>/add-remove-under-managers.php?managerId='+managerId+'&employeeId='+employeeId+'&activateEmp=1'+'&recNo='+recNo;
				}
			}
		}
	</script>
<?php
	if(!empty($managerId)){

		$start					  =	0;
		$recsPerPage	          =	20;//how many records per page
		$showPages		          =	10;	
		$pagingObj->recordNo	  =	$recNo;
		$pagingObj->startRow	  =	$recNo;
		$pagingObj->whereClause   =	$whereClause.$andClause;
		$pagingObj->recsPerPage   =	$recsPerPage;
		$pagingObj->showPages	  =	$showPages;
		$pagingObj->orderBy		  =	$orderBy;
		$pagingObj->table		  =	"employee_details";
		$pagingObj->selectColumns = "*";
		$pagingObj->path		  = SITE_URL_MTEMPLOYEES. "/add-remove-under-managers.php";
		$totalRecords = $pagingObj->getTotalRecords();
		if($totalRecords && $recNo <= $totalRecords)
		{
			$pagingObj->setPageNo();
			$recordSet = $pagingObj->getRecords();
	?>
	<table width='98%' align='center' cellpadding='0' cellspacing='0' border='0'>
		<tr>
			<td colspan="10" class="smalltext23"><b>Employees assigned under - <?php echo $a_managers[$managerId];?></b></td>
		</tr>
		<tr height='25' bgcolor="#373737">
			<td width='2%' class='smalltext12'>&nbsp;</td>
			<td width='11%' class='smalltext12'>Name</td>
			<td width='16%' class='smalltext12'>Email</td>
			<td width='7%' class='smalltext12'>Reg. On</td>
			<td width='5%' class='smalltext12'>Status</td>
			<td width='10%' class='smalltext12'>City</td>
			<td width='12%' class='smalltext12'>Shift Timing</td>
			<td width='8%' class='smalltext12'>Employee Type</td>
			<td class='smalltext12'>Action</td>
		</tr>
	<?php
			$i							=	$recNo;
			while($row					=   mysqli_fetch_assoc($recordSet))
			{
				$i++;

				$employeeId				=	$row['employeeId'];
				$employeeName			=	ucwords(stripslashes($row['fullName']));
				$city					=	$row['city'];
				$email					=	$row['email'];
				$mobile					=	$row['mobile'];
				$shiftTo				=	$row['shiftTo'];
				$shiftFrom				=	$row['shiftFrom'];
				$shiftType				=	$row['shiftType'];
				$referredBy				=	$row['referredBy'];
				$isActive				=	$row['isActive'];
				$addedOn				=	showDate($row['addedOn']);
				
				if(empty($referredBy))
				{
					$referredBy			=	"<font color='#ff0000;'>None</font>";
				}
				
				$phoneText				=	$mobile;	
				
				$bgColor				=	"class='rwcolor1'";
				if($i%2==0)
				{
					$bgColor			=   "class='rwcolor2'";
				}

				$statusText				=	"<font color='#ff0000;'>In-Active</font>";
				$activeInactiveText		=	"Activate";
				$activationType			=	2;
				if($isActive			==	1)
				{
					$statusText			=	"<font color='#00F078;'>Active</font>";
					$activeInactiveText	=	"De-Activate";
					$activationType		=	1;
				}

				$timings				=	showTimeShortFormat($shiftFrom)."-".showTimeShortFormat($shiftTo);
	?>
		<tr <?php echo $bgColor;?> height="30">
			<td class="smalltext2" valign="top">
				<a name="<?php echo $employeeId;?>"></a>&nbsp;<?php echo $i.")";?>
			</td>
			<td class="smalltext2" valign="top">
				<?php echo $employeeName;?></a>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo "<a href='mailto:$email' class='form_links2'>$email</a>";
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo $addedOn;
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo $statusText;
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo $city;
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo $timings;
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo $a_inetExtEmployee[$shiftType];
				?>
			</td>
			<td valign="top" align="right" class="smalltext1"><a href="<?php echo SITE_URL_MTEMPLOYEES?>/display-an-employee-details.php?ID=<?php echo $employeeId;?>&keepThis=true&TB_iframe=true&height=500&width=700" title='' class='thickbox'/><font class='form_links2'>View</font></a>&nbsp;|&nbsp;<a href="<?php echo SITE_URL_MTEMPLOYEES?>/change-manager.php?ID=<?php echo $employeeId;?>&keepThis=true&TB_iframe=true&height=200&width=500" title='' class='thickbox'/><font class='form_links2'>Change Manager</font></a>&nbsp;|&nbsp;<a onclick="removeFromManagers(<?php echo $employeeId;?>,<?php echo $managerId;?>,<?php echo $recNo;?>);" class='form_links2' style="cursor:pointer;">Remove from manager</a>&nbsp;|&nbsp;<a onclick="activeDeactiveEmployee(<?php echo $employeeId;?>,<?php echo $activationType;?>,<?php echo $managerId;?>,<?php echo $recNo;?>);" class='form_links2' style="cursor:pointer;"><?php echo $activeInactiveText;?></a>
			</td>

		</tr>
	<?php
			}
			echo "<tr><td height='10'></td></tr><tr><td colspan='10' style='text-align:right;'>";
				$pagingObj->displayPaging($queryString);
			echo "&nbsp;&nbsp;</td></tr></table>";
		}
		else
		{
			echo "<br /><br /><br /><br /><br /><br /><center><b><font class='error2'>So employee assigned under - ".$a_managers[$managerId]."</font></b></center><br /><br /><br /><br /><br /><br />";
		}
	}
	else
	{
		echo "<br /><br /><br /><br /><br /><br /><center><b><font class='error2'>Please select a manager first</font></b></center><br /><br /><br /><br /><br /><br />";
	}
	include(SITE_ROOT_MTEMPLOYEES   .   "/includes/bottom.php");
?>