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
	$t_searchOrder					=	"";
	$searchOrder					=	"";
	$t_searchOrder					=	"";
	if(isset($_GET['searchOrder'])){
		$searchOrder				=	$_GET['searchOrder'];
		if(!empty($searchOrder)){
			$displayDelete			=	true;
			$t_searchOrder			=	makeDBSafe($searchOrder);
		}
	}

	if(empty($s_hasManagerAccess))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
</script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" />


<script type="text/javascript">
$().ready(function() {
	$("#orderAddress").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/searching-new-process-orders.php", {width: 365,selectFirst: false});
});

function changeOrderEta(orderId,customerId)
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/change-order-eta-by-employee.php?orderId="+orderId+"&customerId="+customerId;
	prop = "toolbar=no,scrollbars=yes,width=600,height=450,top=100,left=100";
	window.open(path,'',prop);
}

</script>
<form name="searchDeleteOrderForm" action=""  method="GET">
	<table width="99%" align="center" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="24%" class="nextText">SEARCH ORDER TO CHANGE ETA :</td>
			<td width="35%" class="nextText">
				<input type='text' name="searchOrder" size="51" value="<?php echo $t_searchOrder;?>" id="orderAddress"  style="border:1px solid #4d4d4d;height:25px;font-size:15px;">
			</td>
			<td>
				<input type="image" name="submit" src="<?php echo SITE_URL;?>/images/small-submit.png" border="0" style="cursor:pointer;">
				<input type='hidden' name='searchFormSubmit' value='1'>
			</td>
		</tr>
	</table>
</form>
<br /><br />
<table width="99%" align="center" border="0" cellpadding="0" cellspacing="0">
	<?php
		if($displayDelete	== true){
			$query			=	"SELECT firstName,lastName,totalOrdersPlaced,members_orders.* FROM members_orders INNER JOIN members ON members_orders.memberId=members.memberId WHERE orderAddress='$t_searchOrder' AND status IN (0,1,3) AND isNotVerfidedEmailOrder=0";
			$result			=	dbQuery($query);
			if(mysqli_num_rows($result)){
		?>
		<tr bgcolor="#373737" height="20">
			<td width="13%" class="smalltext8">&nbsp;<b>Customer Name</b></td>
			<td width="22%" class="smalltext8"><b>Order Address</b></td>
			<td width="12%" class="smalltext8"><b>Type</b></td>
			<td width="7%" class="smalltext8"><b>Status</b></td>
			<td width="12%" class="smalltext8"><b>Added On</b></td>
			<td width="7%" class="smalltext8"><b>ETA (Hours)</b></td>
			<td width="9%" class="smalltext8"><b>Estimated ETA</b></td>
			<td width="12%" class="smalltext8"><b>Processed By</b></td>
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
					$completeName           =   $firstName." ".substr($lastName, 0, 1);
					$orderAddress			=	stripslashes($row['orderAddress']);
					$orderType				=	$row['orderType'];
					$orderTypeText			=	$a_customerOrder[$orderType];
					$orderAddedOn			=	showDate($row['orderAddedOn']);
					$orderAddedTime			=	showTimeShortFormat($row['orderAddedTime']);
					$isHavingEstimatedTime	=	$row['isHavingEstimatedTime'];
					$employeeWarningDate	=	$row['employeeWarningDate'];
					$employeeWarningTime	=	$row['employeeWarningTime'];
					$totalOrdersPlaced		=	$row['totalOrdersPlaced'];
					$acceptedBy				=	$row['acceptedBy'];
					$status					=	$row['status'];
					$hasRepliedUploaded		=	$row['hasRepliedUploaded'];
					$acceptedByName			=	stripslashes($row['acceeptedByName']);
					$isRushOrder			=	$row['isRushOrder'];
					$expctDelvText			=	"";
					$etaHours				=	$a_estimatedTimeHours[$isRushOrder]."Hrs";

					if($isHavingEstimatedTime==	1 && empty($isAddedTatTiming))
					{
						$expctDelvText		=	orderTAT1($employeeWarningDate,$employeeWarningTime);
					}

					$bgColor				=	"class='rwcolor1'";
					if($i%2==0)
					{
						$bgColor			=   "class='rwcolor2'";
					}

					$statusText				=   "<font color='red'>New Order</font>";

					if($status				==	1)
					{
						$statusText			=   "<font color='#4F0000'>Accepted</font>";
						if(!empty($hasRepliedUploaded))
						{
							$statusText		=	"<font color='blue'>QA Pending</font>";
						}
						
					}
					elseif($status			==	3)
					{	
						$statusText			=   "<font color='#333333'>Nd Atten.</font>";
					}

					
			?>
			<tr height="23" <?php echo $bgColor;?>>
				<td valign="top">&nbsp;
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
				<td class="smalltext17" valign="top"><?php echo $etaHours;?></td>
				<td class="smalltext17" valign="top"><?php echo $expctDelvText;?></td>
				<td class="smalltext17" valign="top"><?php echo $acceptedByName;?></td>
				<td class="smalltext17" valign="top"><a onclick="changeOrderEta(<?php echo $orderId;?>,<?php echo $customerId;?>)" class="link_style12" style='cursor:pointer;' title='Delete'>CHANGE ETA</a></td>				
			</tr>			
			<?php

				}
			}
			else{
		?>
		<tr>
			<td height="300" class="error2" style="text-align:center;"><b>No order found - <?php echo $searchOrder;?>.</b></td>
		</tr>
		<?php
			}
		}
		else{
	?>
	<tr>
		<td height="300" class="error2" style="text-align:center;"><b>Please search an order.</b></td>
	</tr>
	<?php
		}
	?>
</table>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>