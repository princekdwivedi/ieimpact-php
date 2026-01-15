<?php
	if($result				=   $orderObj->employeeLogPrepOrdersDetails($orderId))
	{
			$row			=   mysql_fetch_assoc($result);
			$isPrepChecked	=	$row['isChecked'];
			$prepCheckedBy	=	$row['employeeId'];
			$prepCheckedOn	=	$row['checkedDate'];
			$prepMessage	=	$row['checkedMessage'];
			$prepCheckedByName	=	"";
			if(!empty($prepCheckedBy))
			{
				$prepCheckedByName=	$employeeObj->getEmployeeName($prepCheckedBy);
			}
	}
	else
	{
			$isPrepChecked		=	0;
			$prepCheckedBy		=	"";
			$prepCheckedOn		=	"";
			$prepMessage		=	"";
			$prepCheckedByName	=	"";
	}
	if(isset($_GET['logPrepStatus']) && $_GET['logPrepStatus'] == 1)
	{
		dbQuery("INSERT INTO employee_log_prep_orders SET orderId=$orderId,isChecked=1,employeeId=$s_employeeId,checkedDate='".CURRENT_DATE_INDIA."',checkedTime='".CURRENT_TIME_INDIA."'");

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=$orderId&customerId=$customerId#logPrep");
		exit();
	}
?>
<script type="text/javascript">
function checkedLogPrepOrder(orderId,customerId)
{
	var confirmation = window.confirm("Are You Sure To Marked This Order As Checked?");
	if(confirmation == true)
	{
		window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId='+orderId+"&customerId="+customerId+"&logPrepStatus=1";
	}
}
</script>
<a name="logPrep"></a>
	<?php
		if($isPrepChecked == 0)
		{
			echo "<a href='javascript:checkedLogPrepOrder($orderId,$customerId)' class='link_style13'>Mark as Completed This Log&Prep order</a>";	
		}
		else
		{
			echo "<font class='textstyle1'>This order was entered by $prepCheckedByName on ".showDate($prepCheckedOn)."</font>";
		}
	?>
