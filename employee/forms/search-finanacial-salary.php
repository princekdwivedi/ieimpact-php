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
			<td width="5%" class="smalltext1" valign="top"><b>FOR</b></td>
			<td width="5%" valign="top">
			<select name="departmentId">
				<?php
					foreach($a_newDepartment as $key=>$value)
					{
						if($s_hasPdfAccess == 1)
						{
							if($key	==	3)
							{
								echo "<option value='$key'>$value</option>";
							}
						}
						else
						{
							if($key	==	1)
							{
								echo "<option value='$key'>$value</option>";
							}
						}
					}
				?>
			</select>
			</td>
			<?php
				if(empty($s_hasPdfAccess))
				{
			?>
			<td width="3%" class="smalltext2" valign="top">Type</td>
			<td width="7%" valign="top">
				<select name="employeeType">
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
			<?php
				}
				else
				{
					echo "<input type='hidden' name='employeeType' value=''>";
				}
				if(empty($s_hasPdfAccess))
				{
			?>
			<td width="9%" class="smalltext2" valign="top">Under Manger</td>
			<td width="13%" valign="top">
				<select name="underManager">
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
			<?php
				}
				else
				{
					echo "<input type='hidden' name='underManager' value=''>";
				}
			?>
			<td width="18%" class="smalltext1"  valign="top"><b>For/From</b> &nbsp;
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
			<td width="5%" valign="top">
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
			<td width="10%" class="smalltext1"  valign="top"><b>To</b>&nbsp;
				<select name="toMonth">
					<option value="">Select</option>
					<?php
						foreach($a_month as $key=>$value)
						{
							$select	  =	"";
							if($toMonth == $key)
							{
								$select	  =	"selected";
							}

							echo "<option value='$key' $select>$value</option>";
						}
					?>
				</select>
			</td>
			<td width="5%" valign="top">
				<select name="toYear">
					<option value="">Select</option>
					<?php
						$sYear	=	"2010";
						$eYear	=	date("Y")+1;
						for($i=$sYear;$i<=$eYear;$i++)
						{
							$select			=	"";
							if($toYear  == $i)
							{
								$select		=	"selected";
							}
							echo "<option value='$i' $select>$i</option>";
						}
					?>
				</select>
			</td>
			<td width="3%" class="smalltext1" valign="top"><b>FOR</b> </td>
			<td width="15%" valign="top">
				<?php
					if(empty($s_hasPdfAccess))
					{
				?>
						<select name="employeeId[]" multiple style='height:150px;'>
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
										if(in_array($t_employeeId, $a_employeeId))
										{
											$select		=	"selected";
										}
										echo "<option value='$t_employeeId' $select>".ucwords($employeeName)."</option>";
										
									}	
								}
							?>
						</select>
				<?php
					}
					else
					{
				?>
						<select name="employeeId[]" multiple style='height:150px;'>
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
										if(in_array($t_employeeId, $a_employeeId))
										{
											$select		=	"selected";
										}
										echo "<option value='$t_employeeId' $select>".ucwords($employeeName)."</option>";
										
									}	
								}
							?>
						</select>
				<?php
					}
				?>
			</td>
			<td valign="top">
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
		<tr>
			<td colspan="10">&nbsp;</td>
			<td colspan="3" class="smalltext1">[Use Ctrl+Select to select multiple employees]</td>
		</tr>
	</table>
</form>