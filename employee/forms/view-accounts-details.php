<form  name='searchAccounts' method='GET' action="">
<table width="98%" border="0" align="center" cellpadding="4" cellspacing="2" valign="top">
	<tr>
		<td width="30%" class="title">View Account Statements For/From</td>
		<td width="2%" class="smalltext2"><b>:</b></td>
		<td class="smalltext2" width="25%">
			<b>Month</b>&nbsp;&nbsp;
			<select name="month">
			<option value="">Select</option>
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
			<b>Year</b>&nbsp;&nbsp;
			<select name="year">
			<option value="">Select</option>
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
		<td class="smalltext2" width="25%">
			<b>To &nbsp;&nbsp;Month</b>&nbsp;&nbsp;
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
			</select>&nbsp;&nbsp;
			<b>Year</b>&nbsp;&nbsp;
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
		<td>
			<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
			<input type='hidden' name='formSubmitted' value='1'>
		</td>
	</tr>
</table>
</form>