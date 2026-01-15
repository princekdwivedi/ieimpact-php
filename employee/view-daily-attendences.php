<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$department			=	1;//MT Department
	$display			=	"none";
	$display1			=	"";
	$display2			=	"none";
	$display3			=	"none";
	if(!empty($s_hasPdfAccess))
	{
		$department		=	3;
		$display		=	"none";
		$display1		=	"none";
		$display2		=	"none";
		$display3		=	"";
	}
	$displayType		=	0;
	$a_displayType		=	array("0"=>"All","1"=>"Only Logged-In","2"=>"Only Didn't Logged-In");

	$searchOn		=	date("d-m-Y");
	$t_searchOn		=	date("Y-m-d");
	$departmentId	=	1;
	$employeeType	=	0;
	$underManager	=	0;
	$table		    =	"employee_details";
	$andClause		=	"";
	$andClause1		=	"";
	$andClause2		=	"";
	$showForm		=	false;
	$text			=	"";
	$text1			=   "";
	$employeeId		=	0;
	$a_employeeId	=	array();
	$a_managers		=	$employeeObj->getAllEmployeeManager();
	$printLink		=	"";

	$seachingFromAttendence	=	$employeeObj->getSingleQueryResult("SELECT loginDate FROM employee_attendence WHERE attendenceId > '".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID."' AND loginDate <> '0000-00-00' ORDER BY attendenceId LIMIT 1","loginDate");
	$headingText	=	"This page will show records from - ".showDate($seachingFromAttendence);

	if(isset($_POST['formSubmitted']))
	{
		$searchOn		=	$_POST['searchOn'];
		$employeeType	=	$_POST['employeeType'];
		$underManager	=	$_POST['underManager'];
		
		$showForm		=	true;
		$departmentId	=	$_POST['departmentId'];
		$printLink		=	"?searchOn=".$searchOn."&employeeType=".$employeeType."&underManager=".$underManager."&departmentId=".$departmentId;
		list($day,$month,$year)		=	explode("-",$searchOn);
		$t_searchOn	=	$year."-".$month."-".$day;
		if($departmentId== 1)
		{
			$table		    =	"employee_details INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId";
			
			$andClause	    =	" AND employee_shift_rates.departmentId=1";
			$text			=	"MT Department";
			$display		=	"none";
			$display1		=	"";
			$display2		=	"none";
			$display3		=	"none";
		}
		elseif($departmentId== 2)
		{
			$table		    =	"employee_details INNER JOIN employee_shift_rates ON employee_details.employeeId=employee_shift_rates.employeeId";
			
			$andClause	    =	" AND employee_shift_rates.departmentId=2";
			$text			=	"REV Department";
			$display		=	"none";
			$display1		=	"none";
			$display2		=	"";
			$display3		=	"none";
		}
		elseif($departmentId== 3)
		{
			$table		    =	"employee_details";
			
			$andClause	    =	" AND employee_details.hasPdfAccess=1";
			$text			=	"PDF Department";
			$display		=	"none";
			$display1		=	"none";
			$display2		=	"none";
			$display3		=	"";
		}
		if(!empty($employeeType))
		{
			$andClause1	   .=	" AND employee_details.employeeType=$employeeType";
			$text			=	" For ".$a_inetExtEmployee[$employeeType]." Employees";
		}
		if(!empty($underManager))
		{
			$andClause1	   .=	" AND employee_details.underManager=$underManager";
			$text		   .=	" Under Manager ".$a_managers[$underManager];
		}
		if(isset($_POST['employeeId'])  && empty($departmentId))
		{
			$a_employeeId		=	$_POST['employeeId'];
		}
		if(isset($_POST['mtEmployeeId']) && $departmentId == 1)
		{
			$mtEmployeeId		=	$_POST['mtEmployeeId'];
			if(!empty($mtEmployeeId))
			{
				$a_employeeId	=	$mtEmployeeId;
			}
		}
		if(isset($_POST['revEmployeeId']) && $departmentId == 2)
		{
			$revEmployeeId		=	$_POST['revEmployeeId'];
			if(!empty($revEmployeeId))
			{
				$a_employeeId	=	$revEmployeeId;
			}
		}
		if(isset($_POST['pdfEmployeeId']) && $departmentId == 3)
		{
			$pdfEmployeeId		=	$_POST['pdfEmployeeId'];
			if(!empty($pdfEmployeeId))
			{
				$a_employeeId	=	$pdfEmployeeId;
			}
		}
		if(!empty($a_employeeId))
		{
			if(!in_array("0",$a_employeeId))
			{
				$searchEmployee	=	implode(",",$a_employeeId);
				$printLink	   .=   $printLink."&employeeId=".$searchEmployee;
				$andClause2     =	" AND employee_details.employeeId IN ($searchEmployee)";
				$totalEmloyee	=	count($a_employeeId);
				if($totalEmloyee < 2 && $totalEmloyee > 0)
				{
					foreach($a_employeeId as $key=>$value)
					{
						$employeeName	=	$employeeObj->getEmployeeName($value);
					}
					$text1			.=	" for ".$employeeName;
				}
				else
				{
					$text1			.=	" for MULTILE EMPLOYEE";
				}
			}
		}
		$displayType	=	$_POST['displayType'];
	}

	
?>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/validate.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>


<script type="text/javascript">
	function showEmployee(flag)
	{
		if(flag  == 1)
		{
			document.getElementById('displayAllEmployee').style.display = 'none';
			document.getElementById('displayMtEmployee').style.display  = 'inline';
			document.getElementById('displayRevEmployee').style.display = 'none';
			document.getElementById('displayPdfEmployee').style.display = 'none';
		}
		else if(flag  == 2) 
		{
			document.getElementById('displayAllEmployee').style.display = 'none';
			document.getElementById('displayMtEmployee').style.display  = 'none';
			document.getElementById('displayRevEmployee').style.display = 'inline';
			document.getElementById('displayPdfEmployee').style.display = 'none';
		}
		else if(flag  == 3) 
		{
			document.getElementById('displayAllEmployee').style.display = 'none';
			document.getElementById('displayMtEmployee').style.display  = 'none';
			document.getElementById('displayRevEmployee').style.display = 'none';
			document.getElementById('displayPdfEmployee').style.display = 'inline';
		}
		else if(flag  == "")
		{
			document.getElementById('displayAllEmployee').style.display = 'inline';
			document.getElementById('displayMtEmployee').style.display  = 'none';
			document.getElementById('displayRevEmployee').style.display = 'none';
			document.getElementById('displayPdfEmployee').style.display = 'none';
		}
	}
	function openPrintExcelWindow(printLink)
	{
		path = "<?php echo SITE_URL_EMPLOYEES;?>/print-daily-attendences.php"+printLink;
		prop = "toolbar=no,scrollbars=yes,width=400,height=100,top=200,left=300";
		window.open(path,'',prop);
	}
</script>
<script type="text/javascript" src="<?php echo OFFLINE_IMAGE_PATH;?>/script/jquery.js"></script>

<link href="<?php echo SITE_URL;?>/css/thickbox.css" media="screen" rel="stylesheet" type="text/css" />
<script src="<?php echo SITE_URL;?>/script/thickbox-big.js" type="text/javascript"></script>

<form name="searchForm" action=""  method="POST" onsubmit="return search();">
	<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
		<tr height="30">
			<td class="textstyle3" colspan="6"><b><font color="#ff0000"><?php echo $headingText;?></font></b></td>
		</tr>
		<tr>
			<td width="18%" class="textstyle1" valign="top">VIEWING ATTENDENCE OF</td>
			<td width="2%" class="textstyle1" valign="top">:</td>
			<td width="10%">
				<?php
					echo $a_newDepartment[$department];
				?>
				<input type="hidden" name="departmentId" value="<?php echo $department;?>">
			</td>
			<td width="10%" class="textstyle1" valign="top">EMPLOYEE TYPE</td>
			<td width="2%" class="textstyle1" valign="top">:</td>
			<td width="10%">
				<select name="employeeType" class="form_text" style="width:100px;">
					<option value="">All</option>
					<?php
						foreach($a_inetExtEmployee as $key=>$value)
						{
							$select		=	"";
							if($employeeType == $key)
							{
								$select	=	"selected";
							}
							echo "<option value='$key' $select>$value</option>";
						}
					?>
				</select>
			</td>
			<td width="10%" class="textstyle1" valign="top">UNDER MANAGER</td>
			<td width="2%" class="textstyle1" valign="top">:</td>
			<td width="10%">
				<select name="underManager"  class="form_text" style="width:100px;">
					<option value="">All</option>
					<?php
						foreach($a_managers as $key=>$value)
						{
							$select	=	"";
							if($underManager	==	$key)
							{
								$select	=	"selected";
							}

							echo "<option value='$key' $select>$value</option>";
						}
					?>
				</select>
			</td>
			<td width="10%" class="textstyle1" valign="top">OF DATE</td>
			<td width="2%" class="textstyle1" valign="top">:</td>
			<td>
				<input type="text" name="searchOn" value="<?php echo $searchOn;?>" id="atOn" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('atOn','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
			</td>
		</tr>
		<tr>
			<td height="8"></td>
		</tr>
		<tr>
			<td class="textstyle1" valign="top">FOR EMPLOYEE</td>
			<td class="textstyle1" valign="top">:</td>
			<td colspan="3">
				<div  id="displayAllEmployee" style="display:<?php echo $display;?>">
				<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" valign="top">
					<tr>
						<td>
							<select name="employeeId[]" multiple class="form_text" style="width:250px;height:100px;">
								<option value="0">All Employee</option>
								<?php
									if($result	=	$employeeObj->getAllEmployees())
									{
										while($row	=	mysqli_fetch_assoc($result))
										{
											$t_employeeId	=	$row['employeeId'];
											$firstName		=	stripslashes($row['firstName']);
											$lastName		=	stripslashes($row['lastName']);
											$employeeName	=	$firstName." ".$lastName;

											$select			=	"";
											if(in_array($t_employeeId, $a_employeeId))
											{
												$select		=	"selected";
											}
											echo "<option value='$t_employeeId' $select>".ucwords($employeeName)."</option>";
										}	
									}
								?>
							</select>
						</td>
					</tr>
				</table>
				</div>
				<div  id="displayMtEmployee" style="display:<?php echo $display1;?>">
				<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" valign="top">
					<tr>
						<td>
							<select name="mtEmployeeId[]"  multiple class="form_text" style="width:250px;height:100px;">
								<option value="0">All Employee</option>
								<?php
									if($result	=	$employeeObj->getAllMtEmployees())
									{
										while($row	=	mysqli_fetch_assoc($result))
										{
											$t_employeeId	=	$row['employeeId'];
											$firstName		=	stripslashes($row['firstName']);
											$lastName		=	stripslashes($row['lastName']);
											$employeeName	=	$firstName." ".$lastName;

											$select			=	"";
											if(in_array($t_employeeId,$a_employeeId))
											{
												$select		=	"selected";
											}
											echo "<option value='$t_employeeId' $select>".ucwords($employeeName)."</option>";
											
										}	
									}
								?>
							</select>
						</td>
					</tr>
				</table>
				</div>
				<div  id="displayRevEmployee" style="display:<?php echo $display2;?>">
				<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" valign="top">
					<tr>
						<td>
							<select name="revEmployeeId[]"  multiple class="form_text" style="width:250px;height:100px;">
								<option value="0">All Employee</option>
								<?php
									if($result	=	$employeeObj->getAllRevEmployees())
									{
										while($row	=	mysqli_fetch_assoc($result))
										{
											$t_employeeId	=	$row['employeeId'];
											$firstName		=	stripslashes($row['firstName']);
											$lastName		=	stripslashes($row['lastName']);
											$employeeName	=	$firstName." ".$lastName;

											$select			=	"";
											if(in_array($t_employeeId,$a_employeeId))
											{
												$select		=	"selected";
											}
											echo "<option value='$t_employeeId' $select>".ucwords($employeeName)."</option>";
										}	
									}
								?>
							</select>
						</td>
					</tr>
				</table>
				</div>
				<div  id="displayPdfEmployee" style="display:<?php echo $display3;?>">
				<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" valign="top">
					<tr>
						<td class="smalltext1" >
							<select name="pdfEmployeeId[]"  multiple class="form_text" style="width:250px;height:100px;">
								<option value="0">All Employee</option>
								<?php
									if($result	=	$employeeObj->getAllPdfEmployees())
									{
										while($row	=	mysqli_fetch_assoc($result))
										{
											$t_employeeId	=	$row['employeeId'];
											$firstName		=	stripslashes($row['firstName']);
											$lastName		=	stripslashes($row['lastName']);
											$employeeName	=	$firstName." ".$lastName;

											$select			=	"";
											if(in_array($t_employeeId,$a_employeeId))
											{
												$select		=	"selected";
											}
											echo "<option value='$t_employeeId' $select>".ucwords($employeeName)."</option>";
										}	
									}
								?>
							</select>
							<br />[Use Ctrl+Select to select multiple employees]
						</td>
					</tr>
				</table>
				</div>
			</td>
			<td class="textstyle1" colspan="2" valign="top" style="text-align:right">DISPLAY TYPE&nbsp;</td>
			<td class="textstyle1" valign="top">:</td>
			<td valign="top">
				<select name="displayType"  class="form_text" style="width:150px;">
					<?php
						
						foreach($a_displayType	as $k=>$v)
						{
							$select			=	"";
							if($displayType	==	$k)
							{
								$select		=	"selected";
							}
							echo "<option value='$k' $select>".$v."</option>";
						}	
						
					?>
				</select>
			</td>
		</td>
	 </tr>
	 <tr>
		<td height="8"></td>
	</tr>
	<tr>
		<td>
			<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
			<input type='hidden' name='formSubmitted' value='1'>
		</td>
	</tr>
  </table>
</form>
<?php
	if($showForm)
	{
?>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td colspan="2" class='title'>VIEW ATTENDENCE FOR <?php echo showDate($t_searchOn)." ".$text." ".$text1;?></td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<!--<tr>
		<td colspan="2"><a onclick="openPrintExcelWindow('<?php echo $printLink?>')" class='link_style9' style="cursor:pointer;">PRINT THIS PAGE</a></td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>-->
</table>
<br>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
<tr>
	<td width="4%" class="smalltext2"><b>Sr. No</b></td>
	<td width="15%" class="smalltext2"><b>Employee Name</b></td>
	<td width="13%" class="smalltext2"><b>Attendence In At</b></td>
	<td class="smalltext2" width="13%"><b>Attendence Out At</b></td>
	<td class="smalltext2" width="13%"><b>Shift Time</b></td>
	<td class="smalltext2" width="7%"><b>Ovetime</b></td>
	<td class="smalltext2" width="15%"><b>Break Time</b></td>
	<td class="smalltext2"><b>Break Reason</b></td>
</tr>
<tr>
	<td colspan="8">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<?php

	$query	=	"SELECT employee_details.employeeId,firstName,lastName,isShiftTimeAdded,shiftFrom,shiftTo FROM ".$table." WHERE isActive=1 AND hasPdfAccess=1 ".$andClause.$andClause1.$andClause2." ORDER BY firstName";
	

	if($displayType	== 1)
	{
		$query			=	"SELECT employee_details.employeeId,firstName,lastName,isShiftTimeAdded,shiftFrom,shiftTo FROM ".$table." INNER JOIN employee_attendence ON employee_details.employeeId=employee_attendence.employeeId WHERE isActive=1 AND hasPdfAccess=1 AND attendenceId > ".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID." AND loginDate='$t_searchOn'".$andClause.$andClause1.$andClause2." AND isLogin=1 ORDER BY firstName";
	}
	elseif($displayType	== 2)
	{
		$andClauseNotIn	=	"";
		$query111	    =  "SELECT employeeId FROM employee_attendence WHERE attendenceId > ".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID." AND loginDate='$t_searchOn' AND isLogin=1";
		$result111		=	dbQuery($query111);
		if(mysqli_num_rows($result111))
		{
			$a_notSearch		=	array();
			while($row111		=	mysqli_fetch_assoc($result111))
			{
				$t_employeeId	=	$row111['employeeId'];
				$a_notSearch[]	=	$t_employeeId;
			}
			
			$notinId			=	implode(",",$a_notSearch);

			$andClauseNotIn		=	" AND employee_details.employeeId NOT IN ($notinId)";
		}
		
		
		$query					=	"SELECT employee_details.employeeId,firstName,lastName,isShiftTimeAdded,shiftFrom,shiftTo FROM ".$table." WHERE isActive=1 AND hasPdfAccess=1".$andClauseNotIn.$andClause.$andClause1.$andClause2." ORDER BY firstName";
	}
	


	//$query	=	"SELECT employee_details.employeeId,firstName,lastName,isShiftTimeAdded,shiftFrom,shiftTo FROM ".$table." WHERE isActive=1".$andClause.$andClause1.$andClause2." ORDER BY firstName";
	$result		=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		$i	=	0;
		while($row			=	mysqli_fetch_assoc($result))
		{
			$i++;
			$lastName		=	stripslashes($row['lastName']);
			$employeeId		=	$row['employeeId'];
			$firstName		=	stripslashes($row['firstName']);
			$employeeName	=	$firstName." ".$lastName;
			$employeeName	=	ucwords($employeeName);
			$isShiftTimeAdded	=	$row['isShiftTimeAdded'];
			$shiftFrom			=	$row['shiftFrom'];
			$shiftTo			=	$row['shiftTo'];
			$leaveText			=	"";
			$popUpInLink		=	"";
			$popUpOtLink		=	"";

			$query1				=	"SELECT * FROM employee_attendence WHERE attendenceId > ".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID." AND employeeId=$employeeId AND loginDate='$t_searchOn'";
			$result1			=	dbQuery($query1);
			if(mysqli_num_rows($result1))
			{
				$row1			=	mysqli_fetch_assoc($result1);
				$isLogin		=	$row1['isLogin'];
				$isLogout		=	$row1['isLogout'];
				$loginTime		=	$row1['loginTime'];
				$logoutTime		=	$row1['logoutTime'];
				$overtimeHours	=	$row1['overtimeHours'];
				$onLeave		=	$row1['onLeave'];
				$loginIP		=	$row1['loginIP'];
				$logoutIP		=	$row1['logoutIP'];

				if($onLeave		==	1)
				{
					$leaveText	=	"(Full Leave)";
				}
				elseif($onLeave		==	2)
				{
					$leaveText	=	"(Half Leave)";
				}

				if($isLogin == 1)
				{
					$popUpInLink	=	"<br /><center>(<a href='".SITE_URL_EMPLOYEES."/view-ip-details.php?ip=$loginIP&keepThis=true&TB_iframe=true&height=300&width=550' title='' class='thickbox''><font class='link_style6'><u>IP Look Up</u></font></a>)</center>";
					if(!empty($loginTime))
					{
						$loginTime	=	date("H:i",strtotime($loginTime));
						$loginTime	=	"Log In At - ".$loginTime." Hrs";
					}
					if(!empty($isLogout))
					{
						$popUpOtLink=	"<br /><center>(<a href='".SITE_URL_EMPLOYEES."/view-ip-details.php?ip=$logoutIP&keepThis=true&TB_iframe=true&height=300&width=550' title='' class='thickbox''><font class='link_style6'><u>IP Look Up</u></font></a>)</center>";
						
						$logoutTime	=	date("H:i",strtotime($logoutTime));
						$logoutTime	=	"Log Out At - ".$logoutTime." Hrs";
					}
				}
				
			}
			else
			{
				$isLogin		=	0;
				$isLogout		=	0;
				$loginTime		=	"Didnot Log In";
				$logoutTime		=	"Didnot Log Out";
				$overtimeHours	=	0;
			}
			if(!empty($overtimeHours))
			{
				$overtimeText	=	"<font color='#ff0000'>".getHours($overtimeHours)."</font>Hrs";
			}
			else
			{
				$overtimeText	=	"";
			}	
	?>
	<tr>
		<td class="smalltext2" valign="top"><?php echo $i;?>)</td>
		<td class="smalltext2" valign="top"><?php echo $employeeName;?></td>
		<td class="smalltext2" valign="top"><?php echo $loginTime;?><font class='error'>&nbsp;<?php echo $leaveText;?></font><?php echo $popUpInLink;?></td>
		<td class="smalltext2" valign="top"><?php echo $logoutTime.$popUpOtLink;?></td>
		<td class="smalltext2" valign="top">
			<?php
				if($isShiftTimeAdded==  1)
				{
					$displaySiftFrom	=	date("H:i",strtotime($shiftFrom));
					$displaySiftTo		=	date("H:i",strtotime($shiftTo));
					echo $displaySiftFrom." Hrs To ".$displaySiftTo." Hrs";
				}
				else
				{
					echo "Didnot Added";
				}
			?>
		</td>
		<td class="smalltext2" valign="top"><?php echo $overtimeText;?></td>
		<?php
			}
		?>
	</tr>
	<tr>
		<td colspan="8">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
	<?php
	}
	else
	{
?>
<tr>
	<td height="10"></td>
</tr>
<tr>
	<td colspan="4" align="center" class="error">
		<b>No Employee Found !!</b>
	</td>
</tr>
<tr>
	<td height="200"></td>
</tr>
<?php
	}
?>
</table>
<?php
	}
	else
	{
		echo "<table><tr><td height='250' class='smalltext2' style='text-align:center;'><font color='#ff0000'><b>Please submit the form !! </b></font></td></tr></table>";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>