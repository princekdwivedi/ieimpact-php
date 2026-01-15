<script type="text/javascript">
function checkValidCustomer()
{
	form1	=	document.addEditCustomer;
	if(form1.customerName.value	==	"")
	{
		alert("Please Enter customer Name !!");
		form1.customerName.focus();
		return false;
	}
}
</script>

<form  name='addEditCustomer' method='POST' action="" onsubmit="return checkValidCustomer();">
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='title' valign="top" colspan="4"><?php echo $text.$text1;?></td>
	</tr>
	<td width="15%" class="title3">Customer Name</td>
		<td width="2%" class="title3">:</td>
		<td>
			<input type="text" name="customerName" value="<?php echo $customerName;?>" size="40" maxlength="50">
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
	$query	=	"SELECT * FROM platform_clients WHERE parentId=$parentId ORDER BY name";
	$result	=	mysql_query($query);
	if(mysql_num_rows($result))
	{
?>
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='title' valign="top" colspan="4">Existing Customers <?php echo $text1;?></td>
	</tr>
	<tr>
		<td class='text2' width="4%">Sr.No</td>
		<td class='text2' width="15%">Customer Name</td>
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
		$customerId		=	$row['customerId'];
		$customerName	=	$row['name'];
		$departmentId	=	$row['departmentId'];
		$departmentText	=	$a_department[$departmentId];
?>
<tr>
	<td class='smalltext2'><?php echo $i;?>)</td>
	<td class='smalltext2'><?php echo $customerName;?></td>
	<td class='smalltext2'><?php echo $departmentText;?></td>
	<td>
		<a href="<?php echo SITE_URL_EMPLOYEES;?>/add-edit-clients.php?parentId=<?php echo $parentId;?>&ID=<?php echo $customerId;?>" class="link_style5">Edit Name</a>
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