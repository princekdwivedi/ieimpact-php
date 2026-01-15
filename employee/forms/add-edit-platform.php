<script type="text/javascript">
function checkValidPlatform()
{
	form1	=	document.addEditPlatform;
	if(form1.platfromName.value	==	"")
	{
		alert("Please Enter Platform Name !!");
		form1.platfromName.focus();
		return false;
	}
}
</script>

<form  name='addEditPlatform' method='POST' action="" onsubmit="return checkValidPlatform();">
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='title' valign="top" colspan="4"><?php echo $text;?></td>
	</tr>
	<tr>
		<td width="15%" class="title3">Platform Name</td>
		<td width="2%" class="title3">:</td>
		<td>
			<input type="text" name="platfromName" value="<?php echo $platfromName;?>" size="30" maxlength="50">
		</td>
	</tr>
	<td class="title3">Department</td>
		<td class="title3">:</td>
		<td class="smalltext2">
			
			<?php
				if(!empty($departmentId) && !empty($platfromId))
				{
					echo "<b>".$departmentText."</b>";
					echo "<input type='hidden' name='departmentId' value='$departmentId'>";
				}
				else
				{
					echo "<select name='departmentId'>";
					foreach($a_department as $key=>$value)
					{
						$select		=	"";
						if($departmentId == $key)
						{
							$select	=	"selected";
						}
						echo "<option value='$key' $select>$value</option>";
					}
					echo "</select>";
				}
			?>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
		<td>
			<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
			<input type='hidden' name='formSubmitted' value='1'>
		</td>
</table>
</form>
<br>
<?php
	$query	=	"SELECT * FROM platform_clients WHERE parentId=0 ORDER BY name";
	$result	=	mysql_query($query);
	if(mysql_num_rows($result))
	{
?>
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='title' valign="top" colspan="4">Existing Platform</td>
	</tr>
	<tr>
		<td class='text2' width="4%">Sr.No</td>
		<td class='text2' width="15%">Platform Name</td>
		<td class='text2' width="5%">Dapartment</td>
		<td class='text2'></td>
	</tr>
	<tr>
		<td colspan="4">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
<?php
	$i	=	0;
	while($row	=	mysql_fetch_assoc($result))
	{
		$i++;
		$platfromId		=	$row['platfromId'];
		$platfromName	=	$row['name'];
		$departmentId	=	$row['departmentId'];
		$departmentText	=	$a_department[$departmentId];
?>
<tr>
	<td class='smalltext2'><?php echo $i;?>)</td>
	<td class='smalltext2'><?php echo $platfromName;?></td>
	<td class='smalltext2'><?php echo $departmentText;?></td>
	<td>
		<a href="<?php echo SITE_URL_EMPLOYEES;?>/add-edit-platform.php?ID=<?php echo $platfromId;?>" class="link_style5">Edit Name</a>
	</td>
</tr>
<tr>
	<td colspan="4">
		<hr size="1" width="100%" color="#e4e4e4">
	</td>
</tr>
<?php
	}
?>
</table>
<?php
	}
?>