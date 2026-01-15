<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES     .   "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES		.	"/classes/employee.php");
	include(SITE_ROOT				.   "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/set-variables.php");
	$employeeObj					=	new employee();
	$displayDelete					=	false;
	

	if(empty($s_hasManagerAccess))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

?>
<script type="text/javascript">

	function deleteOrderWindow(orderId,customerId)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/delete-order-by-employee.php?orderId="+orderId+"&customerId="+customerId;
		prop = "toolbar=no,scrollbars=yes,width=600,height=450,top=100,left=100";
		window.open(path,'',prop);
	}

</script>
<table width="99%" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="7" class="nextText">48 HRS OLD INCOMPLTED ORDERS</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<?php
		$threeMonthOldDate =	date('Y-m-d', strtotime("-3 month", strtotime(CURRENT_DATE_INDIA)));
		$twoDaysOldDate    =	date('Y-m-d', strtotime("-48 hour", strtotime(CURRENT_DATE_INDIA)));
	
		$query			   =	"SELECT firstName,lastName,members_orders.* FROM members_orders INNER JOIN members ON members_orders.memberId=members.memberId WHERE orderAddedOn > '".$threeMonthOldDate."' AND orderAddedOn < '".$twoDaysOldDate."' AND isVirtualDeleted=0 AND status IN (0,1,3) ORDER BY orderId";
		$result			=	dbQuery($query);
		if(mysqli_num_rows($result)){
		?>
		<tr bgcolor="#373737" height="20">
			<td width="3%" class="smalltext8">&nbsp;</td>
			<td width="12%" class="smalltext8"><b>Customer Name</b></td>
			<td width="30%" class="smalltext8"><b>Order Address</b></td>
			<td width="15%" class="smalltext8"><b>Type</b></td>
			<td width="8%" class="smalltext8"><b>Status</b></td>
			<td width="12%" class="smalltext8"><b>Added On</b></td>
			<td width="10%" class="smalltext8"><b>TAT</b></td>
			<td class="smalltext8"><b>Action</b></td>
		</tr>
		<?php
				$i	=	0;
				while($row					=	mysqli_fetch_assoc($result)){
					$i++;
					$customerId				=	$memberId = $row['memberId'];
					$orderId				=	$row['orderId'];
					$firstName				=	stripslashes($row['firstName']);
					$lastName				=	stripslashes($row['lastName']);
					$completeName			=	$firstName." ".substr($lastName, 0, 1);
					$orderAddress			=	stripslashes($row['orderAddress']);
					$orderType				=	$row['orderType'];
					$orderTypeText			=	$a_customerOrder[$orderType];
					$orderAddedOn			=	showDate($row['orderAddedOn']);
					$orderAddedTime			=	showTimeShortFormat($row['orderAddedTime']);
					$isHavingEstimatedTime	=	$row['isHavingEstimatedTime'];
					$employeeWarningDate	=	$row['employeeWarningDate'];
					$employeeWarningTime	=	$row['employeeWarningTime'];
					$status             	=	$row['status'];
					$expctDelvText			=	"";
					$statusText 			=	"New";
					if($status 				==	1){
						$statusText 		=	"Accepted";
					}
					elseif($status 			==	3){
						$statusText 		=	"Nd Atten.";
					}

					if($isHavingEstimatedTime==	1 && empty($isAddedTatTiming))
					{
						$expctDelvText		 =	orderTAT1($employeeWarningDate,$employeeWarningTime);
					}

					$bgColor					=	"class='rwcolor1'";
					if($i%2==0)
					{
						$bgColor				=   "class='rwcolor2'";
					}

					
			?>
			<tr height="23" <?php echo $bgColor;?>>
				<td class="smalltext17" valign="top">
					&nbsp;<?php echo $i;?>)
				</td>
				<td valign="top">
					<?php 
						echo "<a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=$customerId' class='link_style12' style='cursor:pointer;'>$completeName</a>";
					?>
				</td>
				<td class="smalltext17" valign="top">
					<?php 
						
						echo "<a href='".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=$orderId&customerId=$customerId' class='link_style12'>$orderAddress</a>";		
						
					?>
				</td>
				<td class="smalltext17" valign="top"><?php echo $orderTypeText;?></td>
				<td class="smalltext17" valign="top"><?php echo $statusText;?></td>
				<td class="smalltext17" valign="top"><?php echo $orderAddedOn."/".$orderAddedTime." IST";?></td>
				<td class="smalltext17" valign="top"><?php echo $expctDelvText;?></td>
				<td class="smalltext17" valign="top"><a onclick="deleteOrderWindow(<?php echo $orderId;?>,<?php echo $customerId;?>)" class="link_style12" style='cursor:pointer;' title='Delete'>DELETE ORDER</a></td>				
			</tr>			
			<?php

				}
			}
			else{
		?>
		<tr>
			<td height="300" class="error2" style="text-align:center;"><b>No order found.</b></td>
		</tr>
		<?php
		}
		
	?>
</table>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>