<?php
	$query	=	"SELECT * FROM employee_order_customer_messages WHERE messageType=2 AND messageFor=$orderId ORDER BY messageId";
	$result	=	dbQuery($query);
	if(mysql_num_rows($result))
	{
		$totalCustomerMessages	=	@mysql_result(dbQuery("SELECT COUNT(messageId) FROM employee_order_customer_messages WHERE messageType=3 AND messageFor=$customerId"),0);
?>
<div id="pop01" class="leightbox">
	<a href="#" class="lbAction" rel="deactivate"><img src="<?php echo SITE_URL;?>/images/close.gif" border="0"></a>
<div class="scrollbox">
<table width="99%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td width="60%" class="heading"><b>Messages on this order</b></td>
	<td>
		<?php
			if(!empty($totalCustomerMessages))
			{
		?>
		<a href="#" class="lbOn" rel="pop02" class="link_style13">View messages for this customer</a>
		<?php
			}
			else
			{
				echo "&nbsp;";
			}
		?>
	</td>
</tr>
<tr>
	<td colspan="2">
		<hr size="1" width="100%" color="#333333">
	</td>
</tr>
<?php
	while($row	=	mysql_fetch_assoc($result))	
	{
		$popUpMessage			=	$row['message'];
		$popUpMessageAddedOn	=	showDate($row['addedOn']);
		$popUpMessageBy			=	$row['messageBy'];

		$popUpMessageByName		=	$employeeObj->getEmployeeName($popUpMessageBy);
?>
<tr>
	<td colspan="2"><?php echo $popUpMessage;?></td>
</tr>
<tr>
	<td class="textstyle1" colspan="2">By : <b><?php echo $popUpMessageByName;?></b> On <b><?php echo $popUpMessageAddedOn;?></b></td>
</tr>
<tr>
	<td colspan="2">
		<hr size="1" width="100%" color="#333333">
	</td>
</tr>
<?php
	}
?>
</table>
</div>
</div>

<!----------// POPUP (AUTOLOAD script: add AFTER the autoload popup div) //---------->
<script type="text/javascript">
lb = new lightbox();
lb.initCallable('pop01');
lb.activate();
</script>
<?php
	}
	else
	{
		$query	=	"SELECT * FROM employee_order_customer_messages WHERE messageType=3 AND messageFor=$customerId ORDER BY messageId";
		$result	=	dbQuery($query);
		if(mysql_num_rows($result))
		{
?>
<div id="pop02" class="leightbox">
	<a href="#" class="lbAction" rel="deactivate"><img src="<?php echo SITE_URL;?>/images/close.gif" border="0"></a>
<div class="scrollbox">
<table width="99%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td class="heading"><b>Messages regarding <?php echo $customerName;?></b></td>
</tr>
<tr>
	<td colspan="2">
		<hr size="1" width="100%" color="#333333">
	</td>
</tr>
<?php
	while($row	=	mysql_fetch_assoc($result))	
	{
		$popUpMessage			=	$row['message'];
		$popUpMessageAddedOn	=	showDate($row['addedOn']);
		$popUpMessageBy			=	$row['messageBy'];

		$popUpMessageByName		=	$employeeObj->getEmployeeName($popUpMessageBy);
?>
<tr>
	<td colspan="2"><?php echo $popUpMessage;?></td>
</tr>
<tr>
	<td class="textstyle1" colspan="2">By : <b><?php echo $popUpMessageByName;?></b> On <b><?php echo $popUpMessageAddedOn;?></b></td>
</tr>
<tr>
	<td colspan="2">
		<hr size="1" width="100%" color="#333333">
	</td>
</tr>
<?php
	}
?>
</table>
</div>
</div>

<!----------// POPUP (AUTOLOAD script: add AFTER the autoload popup div) //---------->
<script type="text/javascript">
lb = new lightbox();
lb.initCallable('pop02');
lb.activate();
</script>
<?php
	}
}

$query	=	"SELECT * FROM employee_order_customer_messages WHERE messageType=3 AND messageFor=$customerId ORDER BY messageId";
$result	=	dbQuery($query);
if(mysql_num_rows($result))
{
?>
<div id="pop02" class="leightbox">
<a href="#" class="lbAction" rel="deactivate"><img src="<?php echo SITE_URL;?>/images/close.gif" border="0"></a>
<div class="scrollbox">
<table width="99%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td class="heading"><b>Messages regarding <?php echo $customerName;?></b></td>
</tr>
<tr>
	<td colspan="2">
		<hr size="1" width="100%" color="#333333">
	</td>
</tr>
<?php
	while($row	=	mysql_fetch_assoc($result))	
	{
		$popUpMessage			=	$row['message'];
		$popUpMessageAddedOn	=	showDate($row['addedOn']);
		$popUpMessageBy			=	$row['messageBy'];

		$popUpMessageByName		=	$employeeObj->getEmployeeName($popUpMessageBy);
?>
<tr>
	<td colspan="2"><?php echo $popUpMessage;?></td>
</tr>
<tr>
	<td class="textstyle1" colspan="2">By : <b><?php echo $popUpMessageByName;?></b> On <b><?php echo $popUpMessageAddedOn;?></b></td>
</tr>
<tr>
	<td colspan="2">
		<hr size="1" width="100%" color="#333333">
	</td>
</tr>
<?php
	}
?>
</table>
</div>
</div>
<?php
	}	
?>