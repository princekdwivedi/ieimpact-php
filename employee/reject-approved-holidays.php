<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_MTEMPLOYEES		.   "/includes/top.php");
	include(SITE_ROOT_MTEMPLOYEES		.   "/includes/check-manager-hr-login.php");

	$employeeResult						=	$mtemployeeObj->getAllMtEmployees();
	$a_allEmployees						=	array();
	while($row							=	mysqli_fetch_assoc($employeeResult))
	{
		$t_employeeId					=	$row['employeeId'];
		$firstName						=	stripslashes($row['firstName']);
		$lastName						=	stripslashes($row['lastName']);
		$name							=	$firstName." ".$lastName;

		$a_allEmployees[$t_employeeId]	=	$name;
	}
	$searchEmployeeId					=	0;
	$searchText							=	"";
	if(isset($_GET['searchEmployeeId']))
	{
		$searchEmployeeId				=	(int)$_GET['searchEmployeeId'];
		if(!empty($searchEmployeeId) && array_key_exists($searchEmployeeId,$a_allEmployees))
		{
			$searchText					=	"Viewing <font color='#ff0000'>".$a_allEmployees[$searchEmployeeId]."</font> Future Leaves Dates";
		}
		else
		{
			$searchEmployeeId			=	0;
		}
	}
?>
<form name="searchForm" action=""  method="GET" onsubmit="return search();">
	<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
		<tr>
			<td colspan="0" height="15"></td>
		</tr>
		<tr>
			<td width="40%" class="title" valign="top"><b>SEARCH AN EMPLOYEE TO REMOVE FUTURE DATE LEAVES : </td>
			<td>
				<select name="searchEmployeeId" onchange="document.searchForm.submit();">
					<option value="0">Select</option>
					<?php
						foreach($a_allEmployees as $key=>$value)
						{
							$select				=	"";
							if($searchEmployeeId == $key)
							{
								$select	  =	"selected";
							}

							echo "<option value='$key' $select>$value</option>";
						}
					?>
				</select>&nbsp;&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="6" height="5"></td>
		</tr>
	</table>
</form>
<?php	
	if(!empty($searchEmployeeId))
	{
		
		if(isset($_REQUEST['updateFormSubmitted']))
		{
			if(isset($_POST['rejectLeavs']))
			{
				$a_rejectLeavs		=	$_POST['rejectLeavs'];
				foreach($a_rejectLeavs as $kk=>$vv)
				{
					list($attendenceId,$loginDate)	=	explode("|",$vv);
					
					dbQuery("DELETE FROM employee_attendence WHERE attendenceId=$attendenceId AND employeeId=$searchEmployeeId");
					
					list($leaveY,$leaveM,$leaveD)=	explode("-",$loginDate);

					$nonLeadingZeroMonth		=	$leaveM;
					if($leaveM < 10 && strlen($leaveM) > 1)
					{
						$nonLeadingZeroMonth	=	substr($leaveM,1);
					}

					$nonLeadingZeroDay			=	$leaveD;
					if($leaveD < 10 && strlen($leaveD) > 1)
					{
						$nonLeadingZeroDay		=	substr($leaveD,1);
					}

					@dbQuery("DELETE FROM temp_corn_employee_attendance WHERE employeeId=$searchEmployeeId AND loginDate='".$loginDate."'");


					$isHavingRecrd		=	$mtemployeeObj->getSingleQueryResultMt("SELECT employeeId FROM track_daily_employee_attendance WHERE employeeId=$searchEmployeeId AND forMonth=$nonLeadingZeroMonth AND forYear=$leaveY", 'employeeId');
					if(!empty($isHavingRecrd))
					{
						$field			=	$a_monthDateText[$nonLeadingZeroDay];

						dbQuery("UPDATE track_daily_employee_attendance SET ".$field."=0 WHERE employeeId=$searchEmployeeId AND forMonth=$nonLeadingZeroMonth AND forYear=$leaveY");
						
						$mtemployeeObj->updateEmployeePresentAbsent($searchEmployeeId,$nonLeadingZeroMonth,$leaveY);
					}	
				}
			}

			ob_clean();
			header("Location: ".SITE_URL_MTEMPLOYEES."/reject-approved-holidays.php?searchEmployeeId=$searchEmployeeId");
			exit();
		}

		$query	=	"SELECT * FROM employee_attendence WHERE employee_attendence.onLeave <> 0 AND employeeId=$searchEmployeeId AND loginDate > '".CURRENT_DATE_INDIA."' ORDER BY loginDate";
		$result	=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
?>
			<script type='text/javascript'>
				function selectAll()
				{
					var access		=	document.getElementsByName('rejectLeavs[]');
					var checkUncheck=	document.getElementById('checkUncheck').innerHTML;
					if(checkUncheck == 1)
					{
						document.getElementById('checkUncheck').innerHTML =	0;
						for(i=0;i<access.length;i++)
						{
							access[i].checked = true;
						}
					}
					else
					{
						document.getElementById('checkUncheck').innerHTML =	1;
						for(i=0;i<access.length;i++)
						{
							access[i].checked = false;
						}
					}
				}
			</script>
			<br />
			<form name="updateRejectLeavs" action=""  method="POST">
				<table width="98%" border="0" cellpadding="3" cellspacing="3" align="center">
					<tr>
						<td colspan="8" class="title" valign="top"><b><?php echo $searchText;?></b></td>
					</tr>
					<tr>
						<td width="5%" class="smalltext2"><b>SR NO</b></td>
						<td width="12%" class="smalltext2"><b>DATE</b></td>
						<td class="smalltext2"><b>CLICK TO REMOVE&nbsp;(<a onclick="selectAll();" class="link_style10" style="cursor:pointer;">All</a>)<div id="checkUncheck" style="display:none">1</div></b></td>
					</tr>
					<tr>
						<td colspan="4">
							<hr size="1" width="100%" color="#bebebe">
						</td>
					</tr>
					<?php
						$count		=	0;
						while($row	=	mysqli_fetch_assoc($result))
						{
							$count++;

							$attendenceId	=	$row['attendenceId'];
							$loginDate		=	$row['loginDate'];
					?>
					<tr>
						<td class="textstyle1"><?php echo $count;?>)</td>
						<td width="12%" class="textstyle1"><?php echo showDate($loginDate);?></td>
						<td class="smalltext21">
							<input type="checkbox" name="rejectLeavs[]" value="<?php echo $attendenceId."|".$loginDate;?>">
						</td>
					</tr>
					<?php
						}
					?>
					<tr>
						<td valign="top" colspan="3" class="error">
							[Please make sure you will reject and delete the leave applied if deleted all leaves.]
						</td>
					</tr>
					<tr>
						<td valign="top" colspan="3">
							<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
							<input type='hidden' name='updateFormSubmitted' value='1'>
						</td>
					</tr>
				</table>
			</form>
<?php
		}
		else
		{
			echo "<br><br><br><center><font class='error'><b>NO LEAVE AVAILABLE !!</b></font></center><br><br><br><br><br><br><br>";
		}
	}
	else
	{
		echo "<br><br><br><center><font class='error'><b>NO LEAVE AVAILABLE !!</b></font></center><br><br><br><br><br><br><br>";
	}
	include(SITE_ROOT_MTEMPLOYEES . "/includes/bottom.php");
?>