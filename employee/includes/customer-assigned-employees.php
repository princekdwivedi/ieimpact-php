<?php
	$a_totalOrdersAccepted		=	array();
	$query						=	"SELECT COUNT(orderId) as TotalOrders,acceptedBy FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND assignToEmployee='$nowDateIndia' AND status=1 GROUP BY acceptedBy ORDER BY TotalOrders";
	$result							=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row					=	mysqli_fetch_assoc($result))
		{
			$totalOrders			=	$row['TotalOrders'];
			$acceptedBy				=	$row['acceptedBy'];
			$a_totalOrdersAccepted[$acceptedBy]= $totalOrders;
		}
	}
?>
<table width="100%" align="center" border="0" cellspacing="0" cellspacing="0">
	<tr bgcolor="#373737" height="20">
		<td width="15%" class="smalltext8">&nbsp;<b>Employee Name</b></td>
		<td width="10%" class="smalltext8"><b>Todays orders</b></td>
		<td width="16%" class="smalltext8"><b>Orders Done For Customer</b></td>
		<td class="smalltext8" width="25%"><b>Orders rated good or more by Customer</b></td>
		<td align="right">
			<img src="<?php echo SITE_URL;?>/images/close.gif" title="Close" border="0" onclick="removeEmployeesList(2)" style="cursor:pointer;">&nbsp;
		</td>
	</tr>
	<?php
		$query								=	"SELECT * FROM customers_total_orders_done_by WHERE memberId=$customerId ORDER BY totalAccepted DESC";
		$result								=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$n								=	0;
			while($row						=  mysqli_fetch_assoc($result))
			{
				$n++;
				$employeeId			        =  $row['employeeId'];
				$fullName				    =  stripslashes($row['employeeName']);
				$totalCustCompletedOrders	=  $row['totalAccepted'];
				$totalAverageRating			=  $row['ratingWithThreeOrMore'];
				if(array_key_exists($employeeId,$a_totalOrdersAccepted)){
					$totalOrdersAccepted	=  $a_totalOrdersAccepted[$employeeId];

					if(empty($totalOrdersAccepted))
					{
						$totalOrdersAccepted=	0;
					}
				}
				else{
					$totalOrdersAccepted	=	0;
				}

				$bgColor					=	"class='rwcolor1'";
				if($n%2==0)
				{
					$bgColor				=   "class='rwcolor2'";
				}
	?>
	<tr height="26" <?php echo $bgColor;?>>
		<td class="smalltext6">&nbsp;
			<?php echo $fullName;?>
		</td>
		<td class="smalltext6">
			<?php echo $totalOrdersAccepted;?>
		</td>
		<td class="smalltext6">
			<?php
				echo $totalCustCompletedOrders;
			?>
		</td>
		<td class="smalltext6">
			<?php
				echo $totalAverageRating;
			?>
		</td>
		<td>&nbsp;</td>
	</tr>
	<?php
		}
	}
	?>
	
</table>