<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/validate.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>

<script type="text/javascript">
function showSearch(flag)
{
	if(flag == 1)
	{
		document.getElementById('displayDate').style.display = 'inline';
		document.getElementById('displayMonth').style.display = 'none';
	}
	else
	{
		document.getElementById('displayDate').style.display = 'none';
		document.getElementById('displayMonth').style.display = 'inline';
	}
}
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
<form name="searchForm" action=""  method="POST" onsubmit="return search();">
	<table cellpadding="0" cellspacing="0" width='100%'align="center" border='0'>
		<tr>
			<td width="5%" class="smalltext2" valign="top">View For</td>
			<td width="1%" class="smalltext2" valign="top">:</td>
			<td width="5%" valign="top">
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
			<td width="13%" class="smalltext2" valign="top"> <input type="radio" name="searchBy" value="1" <?php echo $checked;?> onclick="return showSearch(1)">Date Or By <input type="radio" name="searchBy" value="2" <?php echo $checked1;?> onclick="return showSearch(2)">Month</td>
			<td width="1%" class="smalltext2" valign="top">:</td>
			<td width="13%" valign="top" class="smalltext2">
				<div  id="displayDate" style="display:<?php echo $displayDate;?>">
					DATE&nbsp;&nbsp;
					<input type="text" name="searchOn" value="<?php echo $searchOn;?>" id="atOn" readonly size="8" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('atOn','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
				</div>
				<div  id="displayMonth" style="display:<?php echo $displayMonth;?>">
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
					</select>&nbsp;&nbsp;
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
				</div>
			</td>
			<td width="3%" class="smalltext2" valign="top">Type</td>
			<td width="7%" valign="top">
				<select name="type">
					<option value="">All</option>
					<?php
						foreach($a_inetExtEmployee as $key=>$value)
						{
							$select		=	"";
							if($type == $key)
							{
								$select	=	"selected";
							}
							echo "<option value='$key' $select>$value</option>";
						}
					?>
				</select>
			</td>
			<td width="5%" class="smalltext2" valign="top">Manger</td>
			<td width="13%" valign="top">
				<select name="manager">
					<option value="">All</option>
					<?php
						foreach($a_managers as $key=>$value)
						{
							$select	=	"";
							if($manager	==	$key)
							{
								$select	=	"selected";
							}

							echo "<option value='$key' $select>$value</option>";
						}
					?>
				</select>
			</td>
			<td width="8%" class="smalltext2" valign="top">
				For Employee
			</td>
			<td width="17%" valign="top">
				<div id="displayAllEmployee" style="display:<?php echo $display;?>">
					<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" valign="top">
						<tr>
							<td>
								<select name="employeeId[]" multiple style='height:150px;'>
									<option value="0">All Employee</option>
									<?php
										if($result	=	$employeeObj->getAllEmployees())
										{
											while($row	=	mysql_fetch_assoc($result))
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
								<select name="mtEmployeeId[]"  multiple style='height:150px;'>
									<option value="0">All Employee</option>
									<?php
										if($result	=	$employeeObj->getAllMtEmployees())
										{
											while($row	=	mysql_fetch_assoc($result))
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
								<select name="revEmployeeId[]"  multiple style='height:150px;'>
									<option value="0">All Employee</option>
									<?php
										if($result	=	$employeeObj->getAllRevEmployees())
										{
											while($row	=	mysql_fetch_assoc($result))
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
							<td>
								<select name="pdfEmployeeId[]"  multiple style='height:150px;'>
									<option value="0">All Employee</option>
									<?php
										if($result	=	$employeeObj->getAllPdfEmployees())
										{
											while($row	=	mysql_fetch_assoc($result))
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
			</td>
			<td valign="top">
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
		<tr>
			<td colspan="6" height="5"></td>
		</tr>
		<tr>
			<td colspan="7">&nbsp;</td>
			<td colspan="5" class="smalltext7" align="right">
				[Use Ctrl+Select to select multiple employees]
			</td>
		</tr>
	</table>
</form>