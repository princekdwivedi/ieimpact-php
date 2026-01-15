<?php
	Header("Cache-Control: must-revalidate");
	$ExpStr = "Expires: Thu, 29 Oct 1998 17:04:19 GMT";
	Header($ExpStr);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES ."/classes/employee.php");
	$employeeObj =	new employee();


	$departmentId	 =	"";
	$manager		 =	0;
	$andClause		 =	"";

	if(isset($_GET['departmentId']))
	{
		$departmentId=	$_GET['departmentId'];
	}
	if(isset($_GET['manager']))
	{
		$manager	 =	$_GET['manager'];
		if(!empty($manager))
		{
			$andClause =	" AND underManager=$manager";
		}
	}
	
	if($departmentId	==	1)
	{
		$query	=	"SELECT employee_shift_rates.employeeId,fullName FROM employee_shift_rates INNER JOIN employee_details ON employee_shift_rates.employeeId=employee_details.employeeId WHERE departmentId=1 AND employee_details.isActive=1".$andClause." ORDER BY firstName";
		$result	=	dbQuery($query);
	}
	else
	{
		$query	=	"SELECT employeeId,fullName FROM employee_details  WHERE isActive=1 AND hasPdfAccess=1".$andClause." ORDER BY firstName";
		$result	=	dbQuery($query);
	}

	if(!empty($manager))
	{
		
		if(mysqli_num_rows($result))
		{
	?>
			<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" valign="top">
				<tr>
					<td>
						<select name="employeeId[]" multiple style="width:350px;height:150px;">
							<option value="0">All Employee</option>
							<?php									
								while($row	=	mysqli_fetch_assoc($result))
								{
									$t_employeeId	=	$row['employeeId'];
									$employeeName	=	$row['fullName'];
							
									echo "<option value='$t_employeeId' $select>".ucwords($employeeName)."</option>";
								}									
							?>
						</select>
					</td>
				</tr>
			</table>
	<?php
		}
		else
		{
	?>
	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" valign="top">
		<tr>
			<td>
				<select name="employeeId[]" multiple style="width:350px">
					<option value="0">All Employee</option>
				</select>
			</td>
		</tr>
	</table>
	<?php
		}
	}
	else{
			
		if(mysqli_num_rows($result))
		{
	?>
			<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" valign="top">
				<tr>
					<td>
						<select name="employeeId[]" multiple style="width:350px;height:150px;">
							<option value="0">All Employee</option>
							<?php									
								while($row	=	mysqli_fetch_assoc($result))
								{
									$t_employeeId	=	$row['employeeId'];
									$employeeName	=	$row['fullName'];
							
									echo "<option value='$t_employeeId' $select>".ucwords($employeeName)."</option>";
								}									
							?>
						</select>
					</td>
				</tr>
			</table>
	<?php
		}
	}
	
?>