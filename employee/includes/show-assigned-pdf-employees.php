<script type="text/javascript">
function openEditWidow(customerId,type)
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/show-pdf-customers-employees.php?ID="+customerId+"&type="+type;
	prop = "toolbar=no,scrollbars=yes,width=650,height=220,top=100,left=100";
	window.open(path,'',prop);
}
</script>
<br>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
<tr>
	<td colspan="6" class="title1"><b>LISTS OF CUSTOMERS WITH ASSIGNED EMPLOYEES</b></td>
</tr>
<tr>
	<td colspan="6">
		<hr size="1" width="100%" color="#bebebe">
	</td>
</tr>
<tr>
	<td width="5%" class="smalltext2"><b>&nbsp;</b></td>
	<td width="25%" class="smalltext2"><b>CUSTOMER NAME</b></td>
	<td width="10%" class="smalltext2"><b>TYPE</b></td>
	<td width="10%" class="smalltext2"><b>FIRST LEVEL</b></td>
	<td class="smalltext2"><b>QA</b></td>
</tr>
<?php
	$query		=	"SELECT memberId,firstName,lastName,appraisalSoftwareType FROM members WHERE memberType='".CUSTOMERS."' ORDER BY firstName";
	$result		=	mysql_query($query);
	if(mysql_num_rows($result))
	{
		$i	=	0;
		while($row			=	mysql_fetch_assoc($result))
		{
			$i++;
			$customerId		=	$row['memberId'];
			$firstName		=	stripslashes($row['firstName']);
			$lastName		=	stripslashes($row['lastName']);
			$appraisalSoftwareType	=	$row['appraisalSoftwareType'];
			$customerName	=	$firstName." ".$lastName;
			$appraisalText	=	$a_appraisalSoftware[$appraisalSoftwareType];
	
			$totalReplyAssigned	=	@mysql_result(dbQuery("SELECT COUNT(assignedId) FROM pdf_clients_employees WHERE customerId=$customerId AND hasReplyAccess=1"),0);

			$totalQaAssigned	=	@mysql_result(dbQuery("SELECT COUNT(assignedId) FROM pdf_clients_employees WHERE customerId=$customerId AND hasQaAccess=1"),0);

	?>
	<tr>
		<td class="text">
			<?php echo $i;?>
		</td>
		<td class="text">
			<?php echo $customerName;?>
		</td>
		<td class="error">
			<b>
				<?php echo $appraisalText;?>
			</b>
		</td>
		<td class="text">
			<?php
				if(!empty($totalReplyAssigned))
				{
			?>
			<a href='javascript:openEditWidow(<?php echo $customerId;?>,1)' class='link_style10'><?php echo $totalReplyAssigned;?></a>
			<?php
				}
				else
				{
					echo "<b>0</b>";
				}
			?>
		</td>
		<td class="text">
			<?php
				if(!empty($totalQaAssigned))
				{
			?>
			<a href='javascript:openEditWidow(<?php echo $customerId;?>,2)' class='link_style10'><?php echo $totalQaAssigned;?></a>
			<?php
				}
				else
				{
					echo "<b>0</b>";
				}
			?>
		</td>
	</tr>
	<tr>
		<td colspan="6">
			<hr size="1" width="100%" color="#bebebe">
		</td>
	</tr>
	<?php
	}
}
?>
</table>