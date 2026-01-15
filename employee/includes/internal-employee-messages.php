<?php
	$whereClause			  =	"WHERE messageType=1";
	$orderBy		          =	"messageId DESC";
	$queryString			  =	"&internalMsg=1#fourth";


	$start					  =	0;
	$recsPerPage	          =	25;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"employee_order_customer_messages INNER JOIN members_orders ON employee_order_customer_messages.messageFor=members_orders.orderId INNER JOIN members ON members_orders.memberId=members.memberId INNER JOIN employee_details ON employee_order_customer_messages.messageBy=employee_details.employeeId";
	$pagingObj->selectColumns = "employee_order_customer_messages.*,orderAddress,members_orders.memberId,members.firstName,members.lastName,fullName";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/pdf-customer-messages.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
		$i		   =	$recNo;
?>
	<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
		<tr bgcolor="#373737" height="20">
			<td class="smalltext8" width="4%">&nbsp;<b>Sr. No</b></td>
			<td class="smalltext8" width="15%"><b>Message By</b></td>
			<td class="smalltext8" width="16%"><b>Customer name</b></td>
			<td class="smalltext8" width="20%"><b>Order Address</b></td>
			<td class="smalltext8" width="33%"><b>Message</b></td>
			<td class="smalltext8" style="text-align:right"><b>Date</b>&nbsp;</td>
		</tr>
		<?php
			while($row	=   mysqli_fetch_assoc($recordSet))
			{
				$i++;
				$orderId		=	$row['messageFor'];
				$message		=	stripslashes($row['message']);
				$messageBy		=	$row['messageBy'];
				$addedOn		=	showDateFullText($row['addedOn']);
				$oredrAddress	=	stripslashes($row['orderAddress']);
				$customerId		=	$row['memberId'];
				$firstName		=	stripslashes($row['firstName']);
				$lastName		=	stripslashes($row['lastName']);
				$customerName	=	$firstName." ".substr($lastName, 0, 1);
				$messageByName	=	stripslashes($row['fullName']);

				if(in_array($messageBy,$a_allDeactivatedEmployees)){
			  	  $messageByName = "Hemant Jindal";
			    }
				

				$bgColor		=	"class='rwcolor1'";
				if($i%2==0)
				{
					$bgColor	=   "class='rwcolor2'";
				}
		?>
		<tr <?php echo $bgColor;?> height="23">
			<td class="smalltext2" valign="top"><?php echo $i;?>)</td>
			<td class="smalltext2" valign="top"><?php echo $messageByName;?></td>
			<td class="smalltext16" valign="top"><a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=<?php echo $customerId;?>" class="link_style12"><?php echo $customerName;?></a></td>
			<td class="smalltext2" valign="top">
				<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $customerId;?>&selectedTab=8" class="link_style12">
					<b>
						<?php echo stripslashes($oredrAddress);?>
					</b>
				</a>
			</td>
			<td class="smalltext16" valign="top"><?php echo nl2br($message);?></td>
			<td class="smalltext16" valign="top" style="text-align:right">
				<?php 
					echo $addedOn;
				?>&nbsp;
			</td>
		</tr>
		<?php
			}
			echo "<tr><td style='text-align:center' colspan='8'>";
			$pagingObj->displayPaging($queryString);
			echo "&nbsp;&nbsp;</td></tr>";	
		?>
	</table>
<?php
	}
	else
	{
		echo "<table width='70%' align='center' border='0'><tr><td height='50'></td></tr><tr><td style='text-align:center;' class='error'><b>No Messages Available !!</b></td></tr><tr><td height='200'></td></tr></table>";
	}
?>