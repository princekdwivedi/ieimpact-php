<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/validate.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>

<script type="text/javascript">
	function checkValidSearch()
	{
		form1	=	document.searchWorksheet;
		if(form1.departmentId.value	==	"")
		{
			alert("Please Select A Department !!");
			form1.departmentId.focus();
			return false;
		}
		if(form1.forDate.value	==	"" && form1.toDate.value	!=	"")
		{
			alert("Please Select For Date !!");
			form1.forDate.focus();
			return false;
		}

	}
	function showCheckboxes(flag)
	{
		if(flag  <= 3 && flag != "")
		{
			document.getElementById('displayDictaphone').style.display = 'inline';
			document.getElementById('displayProperties').style.display = 'none';
		}
		else if(flag  > 3) 
		{
			document.getElementById('displayDictaphone').style.display = 'none';
			document.getElementById('displayProperties').style.display = 'inline';
		}
		else if(flag  == 0)
		{
			document.getElementById('displayDictaphone').style.display = 'none';
			document.getElementById('displayProperties').style.display = 'none';
		}
	}
	function showSearch(flag)
	{
		if(flag == 1)
		{
			document.getElementById('showDate').style.display = 'inline';
			document.getElementById('showMonth').style.display = 'none';
		}
		else
		{
			document.getElementById('showDate').style.display = 'none';
			document.getElementById('showMonth').style.display = 'inline';
		}
	}
</script>
<form  name='searchWorksheet' method='POST' action="" onsubmit="return checkValidSearch();">
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td colspan="11" class="errpr"><?php echo $errorMsg;?></td>
	</tr>
	<tr>
		<td colspan="11" class="textstyle1"><b>VIEW MT EMPLOYEES MONTHLY WORKSHEET</b></td>
	</tr>
	<tr>
		<td colspan="11">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
	<tr>
		<td width="20%" class="smalltext2">
			<b>Department</b>
		</td>
		<td width="1%" class="smalltext2">
			<b>:</b>
		</td>
		<td width="13%">
			<select name="departmentId">
				<option value="">Select</option>
				<?php
					foreach($a_department as $key=>$value)
					{
						$select		=	"";
						if($departmentId == $key)
						{
							$select	=	"selected";
						}
						if($key == 1)
						{
							echo "<option value='$key' $select>$value</option>";
						}
					}
				?>
			</select>
		</td>
		<td width="3%">&nbsp;</td>
		<td width="10%" class="smalltext2"><b>Platform</b></td>
		<td width="1%" class="smalltext2"><b>:</b></td>
		<td  width="10%"> 
			<?php
				$url2	=	SITE_URL_EMPLOYEES. "/get-platform-customer.php?parentId=";
			?>
			<select name="platform"  onchange="commonFunc('<?php echo $url2?>','displayCustomer',this.value);showCheckboxes(this.value);">
				<option value="">Select</option>
				<?php
					if($result = $employeeObj->getPlatformByDepartment(1))
					{
						while($row	=	mysql_fetch_assoc($result))
						{
							$t_parentId		=	$row['platfromId'];
							$t_parentName	=	$row['name'];

							$select		 =	"";
							if($t_parentId == $platform)
							{
								$select	 =	"selected";
							}
							echo "<option value='$t_parentId' $select>$t_parentName</option>";
						}
					}
				?>
			</select>
		</td>
		<td width="2%">&nbsp;</td>
		<td width="10%" class="smalltext2"><b>Client</b></td>
		<td width="1%"  class="smalltext2"><b>:</b></td>
		<td>
			<div id="displayCustomer">
			<select name="customerId">
				<option value="">Select</option>
				<?php
					if(!empty($platform))
					{
						if($result = $employeeObj->getPlatformClients($platform))
						{
							while($row	=	mysql_fetch_assoc($result))
							{
								$t_customerId	=	$row['customerId'];
								$customerName	=	$row['name'];

								$select		 =	"";
								if($customerId == $t_customerId)
								{
									$select	 =	"selected";
								}
								
								echo "<option value='$t_customerId' $select>$customerName</option>";
							}
						}
					}
				?>
			</select>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="11" height="5"></td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top">
			<b>Search By</b> 
			<input type="radio" name="searchBy" value="1" onclick="return showSearch(1)" <?php echo $checked;?>><b>Date Or</b>&nbsp;
			<input type="radio" name="searchBy" value="2" onclick="return showSearch(2)" <?php echo $checked1;?>><b>Month</b>
		</td>
		<td class="smalltext2" valign="top">
			<b>:</b>
		</td>
		<td valign="top" colspan="6">
			<div  id="showDate" style="display:<?php echo $display2;?>">
				<table width="100%" align="center" border="0">
				<tr>
					<td width="18%" class="smalltext2" valign="top"><b>For Date</b></td>
					<td width="1%" class="smalltext2" valign="top"><b>:</b></td>
					<td> 
						<input type="text" name="forDate" value="<?php echo $forDate;?>" id="for" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('for','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
					</td>
					<td width="8%" class="smalltext2">&nbsp;</td>
					<td width="15%" class="smalltext2" valign="top"><b>To Date</b></td>
					<td width="1%" class="smalltext2" valign="top"><b>:</b></td>
					<td> 
						<input type="text" name="toDate" value="<?php echo $toDate;?>" id="to" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('to','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
					</td>
				</tr>
				</table>
			</div>
			<div  id="showMonth" style="display:<?php echo $display3;?>">
				<table width="100%" align="center" border="0">
				<tr>
					<td width="18%" class="smalltext2" valign="top"><b>For Month</b></td>
					<td width="1%" class="smalltext2" valign="top"><b>:</b></td>
					<td  width="27%"> 
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
					<td width="8%" class="smalltext2">&nbsp;</td>
					<td width="15%" class="smalltext2" valign="top"><b>Year</b></td>
					<td width="1%" class="smalltext2" valign="top"><b>:</b></td>
					<td> 
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
				</tr>
				</table>
			</div>
		</td>
		<td class="smalltext2" valign="top"><b>Employee</b></td>
		<td class="smalltext2" valign="top"><b>:</b></td>
		<td valign="top">
			<select name="employeeId[]" multiple style="height:100px;">
				<option value="0">All</option>
				<?php
					if($result	=	$employeeObj->getAllMtEmployees())
					{
						while($row	=	mysql_fetch_assoc($result))
						{
							$t_employeeId	=	$row['employeeId'];
							$firstName		=	$row['firstName'];
							$lastName		=	$row['lastName'];

							$employeeName	=	$firstName." ".$lastName;
							$employeeName	=	ucwords($employeeName);

							$select			=	"";
							if(in_array($t_employeeId, $a_employeeId))
							{
								$select		=	"selected";
							}

							echo  "<option value='$t_employeeId' $select>$employeeName</option>";
						}
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top">
			<b>Display Report</b>
		</td>
		<td class="smalltext2"  valign="top">
			<b>:</b>
		</td>
		<td valign="top">
			<select name="reportView">
			<?php
				foreach($a_displayReport as $key=>$value)
				{
					$select	 =	"";
					if($key == $reportView)
					{
						$select =	"selected";
					}
					echo "<option value='$key' $select>$value</option>";
				}
			?>
		</td>
		<td colspan="7">
			<table width="100%" align="center" border="0">
				<tr>
					<td width="10%" class="smalltext2" valign="top"><b>Type</b></td>
					<td width="1%" class="smalltext2" valign="top"><b>:</b></td>
					<td  width="15%"> 
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
					<td width="8%" class="smalltext2">&nbsp;</td>
					<td width="15%" class="smalltext2" valign="top"><b>Manager</b></td>
					<td width="1%" class="smalltext2" valign="top"><b>:</b></td>
					<td> 
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
				</tr>
				</table>
		</td>
		<td class="smalltext7" valign="top">
			[Use Ctrl+Select to select multiple employees]
		</td>
	</tr>
	<tr>
		<td colspan="11">
			<div  id="displayDictaphone" style="display:<?php echo $display;?>">
				<table width="100%" border="0" align="center" cellpadding="0" cellspacing="7" valign="top">
					<tr>
						<td width="19%" class="smalltext2">
							<b>View Report Of </b>
						</td>
						<td width="5%" class="smalltext2">
							<b>:</b>
						</td>
						<td class="text2">
							<?php
								foreach($a_viewDictaphoneReport as $key=>$value)
								{
									$checked	 =	"";
									if(in_array($key,$a_reportDictaphone))
									{
										$checked =	"checked";
									}
									echo "<input type='checkbox' name='reportDictaphone[$key]' value='$key' $checked>$value&nbsp;";
								}
							?>
						</td>
					</tr>
				</td>
				</table>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="11">
			<div  id="displayProperties" style="display:<?php echo $display1;?>">
				<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" valign="top">
					<tr>
						<td width="24%" class="smalltext2">
							<b>View Report Of </b>
						</td>
						<td width="5%" class="smalltext2">
							<b>:</b>
						</td>
						<td class="text2">
							<?php
								foreach($a_viewPropertiesReport as $key=>$value)
								{
									$checked	 =	"";
									if(in_array($key,$a_reportProperties))
									{
										$checked =	"checked";
									}
									echo "<input type='checkbox' name='reportProperties[$key]' value='$key' $checked>$value&nbsp;";
								}
							?>
						</td>
					</tr>
				</td>
				</table>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="11">
			<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
			<input type='hidden' name='formSubmitted' value='1'>
		</td>
	</tr>
</table>
</form>