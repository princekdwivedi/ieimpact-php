<form  name='monthYearEmployee' method='POST' action="">
	<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
		<tr>
			<td width="14%" class="smalltext2" valign="top">VIEW <?php echo $formText;?> FOR</td>
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
			<td width="11%" class="smalltext2"  valign="top">ON &nbsp;&nbsp;
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
			<td width="5%" class="smalltext2" valign="top">FOR </td>
			<td width="15%" valign="top">
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
			</td>
			<td valign="top">
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
		<tr>
			<td colspan="8">&nbsp;</td>
			<td colspan="3" class="smalltext1">[Use Ctrl+Select to select multiple employees]</td>
		</tr>
	</table>
</form>