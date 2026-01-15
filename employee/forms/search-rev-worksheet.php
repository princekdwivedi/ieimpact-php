<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/validate.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>

<script type="text/javascript">
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

<form  name='searchWorksheet' method='POST' action="">
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td colspan="17" class="error"><?php echo $errorMsg;?></td>
	</tr>
	<tr>
		<td colspan="17" class="textstyle1"><b>VIEW REV EMPLOYEES MONTHLY WORKSHEET</b></td>
	</tr>
	<tr>
		<td colspan="17">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
	<tr>
		<td width="9%" class="smalltext2">Platform</td>
		<td width="1%" class="smalltext2">:</td>
		<td  width="9%"> 
			<?php
				$url2	=	SITE_URL_EMPLOYEES. "/get-platform-customer.php?parentId=";
			?>
			<select name="platform"  onchange="commonFunc('<?php echo $url2?>','displayCustomer',this.value);showCheckboxes(this.value);">
				<option value="">Select</option>
				<?php
					if($result = $employeeObj->getPlatformByDepartment(2))
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
		<td width="4%" class="smalltext2">Client</td>
		<td width="1%" class="smalltext2">:</td>
		<td  width="18%"> 
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
		<td width="9%" class="smalltext2">Display Report</td>
		<td width="1%" class="smalltext2">:</td>
		<td  width="9%"> 
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
		<td class="smalltext2" valign="top" width="18%">
			Search By 
			<?php echo $checked;?>
			<input type="radio" name="searchBy" value="1" onclick="return showSearch(1)" <?php echo $checked;?>>Date Or 
			<input type="radio" name="searchBy" value="2" onclick="return showSearch(2)" <?php echo $checked1;?>>Month
		</td>
		<td class="smalltext2" valign="top" width="1%">
			:
		</td>
		<td valign="top">
			<div  id="showDate" style="display:<?php echo $display;?>">
				<table width="100%" align="center" border="0">
				<tr>
					<td> 
						<input type="text" name="forDate" value="<?php echo $forDate;?>" id="for" readonly size="7" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('for','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>TO
						<input type="text" name="toDate" value="<?php echo $toDate;?>" id="to" readonly size="7" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('to','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
					</td>
				</tr>
				</table>
			</div>
			<div  id="showMonth" style="display:<?php echo $display1;?>">
				<table width="100%" align="center" border="0">
				<tr>
					<td  width="50%"> 
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
	</tr>
	<tr>
		<td  class="smalltext2" valign="top">
			Show Report Of
		</td>
		<td class="smalltext2" valign="top">
			:
		</td>
		<td colspan="9" valign="top">
		<table width="100%" border="0">
			<tr>
				<td width="57%" valign="top">
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
				<td class="smalltext2" valign="top" width="5%">Type</td>
				<td class="smalltext2" valign="top">:</td>
				<td width="5%"> 
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
				<td width="5%" class="smalltext2" valign="top">Manager</td>
				<td width="1%" class="smalltext2" valign="top">:</td>
				<td width="15%"> 
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
				<td class="smalltext2" valign="top" align="right" width="15%">Employee</td>
				<td class="smalltext2" valign="top">:</td>
			  </tr>
			</table>
		</td>
		<td>
			<select name="employeeId[]" multiple style="height:100px;">
				<option value="0">All</option>
				<?php
					if($result	=	$employeeObj->getAllRevEmployees())
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
		<td colspan="2">
			<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
			<input type='hidden' name='formSubmitted' value='1'>
		</td>
		<td colspan="10" align="right" class="smalltext1">
			[Use Ctrl+Select to select multiples] 
		</td>
	</tr>
</table>
</form>
