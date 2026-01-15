	<table width="100%" align="center" border="0" cellspacing="0" cellspacing="2">
		<tr>
			<td colspan="10" class="textstyle1"><b>Last 5 Completed Order details for <?php echo $customerName;?></b></td>
		</tr>
		<tr bgcolor="#373737" height="20">
			<td width="30%" class="smalltext8">&nbsp;<b>Order Address</b></td>
			<td width="15%" class="smalltext8"><b>Order Type</b></td>
			<td width="9%" class="smalltext8"><b>Date</b></td>
			<td width="13%" class="smalltext8"><b>Accepted By</b></td>
			<td width="13%" class="smalltext8"><b>Qa By</b></td>
			<td width="7%" class="smalltext8"><b>Cus. Rate</b></td>
			<td class="smalltext8"><b>Message</b></td>
		</tr>
		<?php
			$orderAcceptedHrs		=	"";
			$orderQaHrs				=	"";
			$query					=	"SELECT members_orders.orderId,orderAddress,orderType,acceptedBy,rateGiven,orderAddedOn,orderAddedOn,memberRateMsg,timeSpentEmployee,timeSpentQa,qaDoneByName,acceeptedByName FROM members_orders INNER JOIN members_orders_reply ON members_orders.orderId=members_orders_reply.orderId WHERE members_orders.memberId=$customerId AND status IN (2,4,5) ORDER BY orderAddedOn DESC LIMIT 5";
			$result	=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$m				=	0;
				while($row		=	mysqli_fetch_assoc($result))
				{
					$m++;
					$t_orderId			=	$row['orderId'];
					$t_orderAddress		=	stripslashes($row['orderAddress']);
					$t_orderType		=	$row['orderType'];
					$t_acceptedBy		=	$row['acceptedBy'];
					$t_rateGiven		=	$row['rateGiven'];
					$t_orderAddedOn		=	showDate($row['orderAddedOn']);
					$t_memberRateMsg	=	stripslashes($row['memberRateMsg']);
					$acceptedByName		=	stripslashes($row['acceeptedByName']);
					$qaDoneByText		=	$row['qaDoneByName'];
					$orderAcceptedHrs	=	$row['timeSpentEmployee'];
					$orderQaHrs			=	$row['timeSpentQa'];

					$orderText			=	$a_customerOrder[$t_orderType];
					
					if(!empty($orderAcceptedHrs))
					{
						$orderAcceptedHrs=	" (".getHours($orderAcceptedHrs)." Hrs)";
					}
		

					if(!empty($orderQaHrs))
					{
						$orderQaHrs		=	" (".getHours($orderQaHrs)." Hrs)";
					}

					$bgColor			=	"class='rwcolor1'";
					if($m%2==0)
					{
						$bgColor		=   "class='rwcolor2'";
					}
?>
<tr height="26" <?php echo $bgColor;?>>
	<td class="smalltext6" valign="top">&nbsp;<?php echo $t_orderAddress;?></td>
	<td class="smalltext6" valign="top"><?php echo $orderText;?></td>
	<td class="smalltext6" valign="top"><?php echo $t_orderAddedOn;?></td>
	<td class="smalltext6" valign="top"><?php echo $acceptedByName.$orderAcceptedHrs;?></td>
	<td class="smalltext6" valign="top"><?php echo $qaDoneByText.$orderQaHrs;?></td>
	<td class="smalltext6" valign="top">
		<?php
			if(!empty($t_rateGiven))
			{
		?>
				<img src="<?php echo SITE_URL;?>/images/rating/<?php echo $t_rateGiven;?>.png" >
		<?php
			}
		?>
	</td>
	<td class="smalltext6" valign="top">
		<?php echo $t_memberRateMsg;?>
	</td>
</tr>
<?php

		}
	}
?>
</table>
<form name="acceptOrder" action="" method="POST">
	<table width="100%" align="center" border="0" cellspacing="2" cellspacing="2">
		<tr>
			<td colspan="10" class="textstyle1"><b>List Of Processing Employees Assigning To - <?php echo $customerName;?></b></td>
		</tr>
		<?php
			if(!empty($errorMsg))
			{
		?>
		<tr>
			<td colspan="10" class="error"><b><?php echo $errorMsg;?></b></td>
		</tr>
		<?php
			}
		?>
	</table>
	<br>	
	<table cellpadding="2" cellspacing="2" width='98%'align="center" border='0'>
		<tr>
			<td width="7%" class="smalltext2"><b>Select</b></td>
			<td width="15%" class="smalltext2"><b>Employee Name</b></td>
			<td width="10%" class="smalltext2"><b>Todays orders</b></td>
			<td width="20%" class="smalltext2"><b>Total Orders Done For Customer</b></td>
			<td class="smalltext2"><b>Orders rated good or more by Customer</b></td>
		</tr>
		<tr>
			<td colspan="10">
				<hr size="1" width="100%" color="#bebebe">
			</td>
		</tr>
		<?php
			$a_existingAcceptedId	=	array();
			$a_employees			=	array();

			$query					=	"SELECT fullName,employeeId as Emp,(SELECT totalAccepted from customers_total_orders_done_by WHERE memberId=$customerId AND employeeId=emp) AS totalAccepted,(SELECT ratingWithThreeOrMore from customers_total_orders_done_by WHERE memberId=$customerId AND employeeId=emp) AS totalMoreThaThreRating FROM employee_details WHERE isActive=1 AND hasPdfAccess=1 order by totalAccepted DESC,fullName";
			$result					=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row		    =  mysqli_fetch_assoc($result))
				{
					$employeeId			        =  $row['Emp'];
					$fullName				    =  stripslashes($row['fullName']);
					$totalCustCompletedOrders	=  $row['totalAccepted'];
					$totalAverageRating			=  $row['totalMoreThaThreRating'];

					if(array_key_exists($employeeId,$a_totalOrdersAccepted)){

						$totalOrdersAccepted	 =	$a_totalOrdersAccepted[$employeeId];
					}
					else
					{
						$totalOrdersAccepted	 =	0;
					}

		?>
		<tr>
			<td class="smalltext2">
				<input type="radio" name="selectedEmployee" value="<?php echo $employeeId;?>">
			</td>
			<td class="smalltext2">
				<?php echo $fullName;?>
			</td>
			<td class="smalltext2">
				<?php echo $totalOrdersAccepted;?>
			</td>
			<td class="smalltext2">
				<?php
					echo $totalCustCompletedOrders;
				?>
			</td>
			<td class="smalltext2">
				<?php
					echo $totalAverageRating;
				?>
			</td>
		</tr>
		<tr>
			<td colspan="10">
				<hr size="1" width="100%" color="#bebebe">
			</td>
		</tr>
		<?php	
				   }

				}
			
		?>
		<tr>
			<td colspan="2">
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
	</table>
</form>