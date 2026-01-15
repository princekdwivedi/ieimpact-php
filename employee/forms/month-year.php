<form  name='monthYear' method='POST' action="">
	<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
		<tr>
			<td width="14%" class="title1">SEARCH FOR </td>
			<td width="10%" class="title1">
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
			<td>
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
	</table>
</form>