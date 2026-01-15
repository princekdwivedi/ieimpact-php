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
function checkValidSearch()
{
	form1		=	document.monthYearAllEmployee;
	if(form1.departmentId.value == "")
	{
		if(form1.employeeId.value == "0")
		{
			alert("Please select an employee.");
			form1.employeeId.focus();
			return false;
		}
	}
	if(form1.departmentId.value == "1")
	{
		if(form1.mtEmployeeId.value == "0")
		{
			alert("Please select an employee.");
			form1.mtEmployeeId.focus();
			return false;
		}
	}
	if(form1.departmentId.value == "2")
	{
		if(form1.revEmployeeId.value == "0")
		{
			alert("Please select an employee.");
			form1.revEmployeeId.focus();
			return false;
		}
	}
	if(form1.departmentId.value == "3")
	{
		if(form1.pdfEmployeeId.value == "0")
		{
			alert("Please select an employee.");
			form1.pdfEmployeeId.focus();
			return false;
		}
	}
}
</script>
<form  name='monthYearAllEmployee' method='POST' action="" onsubmit="return checkValidSearch();">
	<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
		<tr>
			<td colspan="15" class="textstyle1">
				<b>VIEW MONTHLY EMPLOYEE LOGIN DETAILS</b>
			</td>
		</tr>
		<tr>
			<td colspan="15" height="5"></td>
		</tr>
		<tr>
			<td width="20%" class="smalltext2">VIEW ATTENDENCE FOR &nbsp;&nbsp;
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
						$sYear	=	"2009";
						$eYear	=	date("Y");
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
			<td width="5%" class="smalltext2">FOR </td>
			<td width="15%">
				<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" valign="top">
					<tr>
						<td>
							<select name="pdfEmployeeId">
								<option value="0">SELECT EMPLOYEE</option>
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
			</td>
			<td>
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
	</table>
</form>