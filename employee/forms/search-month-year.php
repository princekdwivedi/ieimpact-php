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
</script>
<form  name='monthYearEmployee' method='POST' action="">
	<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
		<tr>
			<td width="20%" class="title1">VIEW ATTENDENCE FOR</td>
			<td width="10%">
			<select name="departmentId" onchange="showEmployee(this.value);">
				<option value="">All</option>
				<?php
					foreach($a_newDepartment as $key=>$value)
					{
						$select		=	"";
						if($departmentId == $key)
						{
							$select	=	"selected";
						}
						echo "<option value='$key' $select>$value</option>";
					}
				?>
			</select>
			</td>
			<td width="10%" class="title1">ON &nbsp;&nbsp;
				<select name="month">
					<?php
						foreach($a_month as $key=>$value)
						{
							$select	  =	"";
							if($month == $key)
							{
								$select	  =	"selected";
							}

							echo "<option value='$key' $select>$value</option>";
						}
					?>
				</select>
			</td>
			<td width="10%">
				<select name="year">
					<?php
						$sYear	=	"2010";
						$eYear	=	date("Y")+1;
						for($i=$sYear;$i<=$eYear;$i++)
						{
							$select			=	"";
							if($year  == $i)
							{
								$select		=	"selected";
							}
							echo "<option value='$i' $select>$i</option>";
						}
					?>
				</select>
			</td>
			<td width="5%" class="title1">FOR </td>
			<td width="15%">
				<div  id="displayAllEmployee" style="display:<?php echo $display;?>">
				<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" valign="top">
					<tr>
						<td>
							<select name="employeeId">
								<option value="0">All Employee</option>
								<?php
									if($result	=	$employeeObj->getAllEmployees())
									{
										while($row	=	mysql_fetch_assoc($result))
										{
											$t_employeeId	=	$row['employeeId'];
											$firstName		=	$row['firstName'];
											$lastName		=	$row['lastName'];
											$employeeName	=	$firstName." ".$lastName;

											$select			=	"";
											if($employeeId  == $t_employeeId)
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
							<select name="mtEmployeeId">
								<option value="0">All Employee</option>
								<?php
									if($result	=	$employeeObj->getAllMtEmployees())
									{
										while($row	=	mysql_fetch_assoc($result))
										{
											$t_employeeId	=	$row['employeeId'];
											$firstName		=	$row['firstName'];
											$lastName		=	$row['lastName'];
											$employeeName	=	$firstName." ".$lastName;

											$select			=	"";
											if($employeeId  == $t_employeeId)
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
							<select name="revEmployeeId">
								<option value="0">All Employee</option>
								<?php
									if($result	=	$employeeObj->getAllRevEmployees())
									{
										while($row	=	mysql_fetch_assoc($result))
										{
											$t_employeeId	=	$row['employeeId'];
											$firstName		=	$row['firstName'];
											$lastName		=	$row['lastName'];
											$employeeName	=	$firstName." ".$lastName;

											$select			=	"";
											if($employeeId  == $t_employeeId)
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
						<td>
							<select name="pdfEmployeeId">
								<option value="0">All Employee</option>
								<?php
									if($result	=	$employeeObj->getAllPdfEmployees())
									{
										while($row	=	mysql_fetch_assoc($result))
										{
											$t_employeeId	=	$row['employeeId'];
											$firstName		=	$row['firstName'];
											$lastName		=	$row['lastName'];
											$employeeName	=	$firstName." ".$lastName;

											$select			=	"";
											if($employeeId  == $t_employeeId)
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
			</td>
			<td>
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
	</table>
</form>