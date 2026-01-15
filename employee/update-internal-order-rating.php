<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	ini_set('display_errors', 1);
	include(SITE_ROOT_EMPLOYEES		.   "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES		.   "/classes/employee.php");
	include(SITE_ROOT				.   "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/set-variables.php");
	$displayChange					=	false;
	$t_searchOrder					=	"";
	$searchOrder					=	"";
	
	
	if(isset($_GET['searchOrder'])){
		$searchOrder				=	$_GET['searchOrder'];
		if(!empty($searchOrder)){
			$displayChange			=	true;
			$t_searchOrder			=	makeDBSafe($searchOrder);
		}
	}

	if(empty($s_hasManagerAccess))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	/*elseif(!in_array($s_employeeId,$a_allowChangeRatingInternalAccess)){
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}*/

	$a_existingCustomerRatings	=	array("1"=>"Poor","2"=>"Average","3"=>"Good","4"=>"very Good","5"=>"Excellent");

	$a_alreadyChangedRating		=	array();
	$query						=	"SELECT orderId,rating FROM internal_order_rating";
	$result						=	dbQuery($query);
	if(mysqli_num_rows($result)){
		while($row				=	mysqli_fetch_assoc($result)){
			$t_orderId			=	$row['orderId'];
			$a_alreadyChangedRating[$t_orderId] = $row['rating'];
		}
	}
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
</script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" />


<script type="text/javascript">
$().ready(function() {
	$("#orderAddress").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/searching-completed-orders.php", {width: 365,selectFirst: false});
});

function changeOrderRate(orderId,customerId)
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/change-order-rate-internally.php?orderId="+orderId+"&customerId="+customerId;
	prop = "toolbar=no,scrollbars=yes,width=650,height=450,top=100,left=100";
	window.open(path,'',prop);
}

</script>
<form name="searchDeleteOrderForm" action=""  method="GET">
	<table width="99%" align="center" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="38%" class="nextText">SEARCH ORDER TO ADD POST AUDIT ERRORS ENTRY :</td>
			<td width="35%" class="nextText">
				<input type='text' name="searchOrder" size="43" value="<?php echo $t_searchOrder;?>" id="orderAddress"  style="border:1px solid #4d4d4d;height:25px;font-size:15px;">
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
		if($displayChange	== true){
			$query			=	"SELECT completeName,totalOrdersPlaced,members_orders.* FROM members_orders INNER JOIN members ON members_orders.memberId=members.memberId WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND orderAddress like '%$t_searchOrder%' AND isVirtualDeleted=0 AND isDeleted=0 AND status IN (2,5,6)";
			$result			=	dbQuery($query);
			if(mysqli_num_rows($result)){
		?>
		<tr bgcolor="#373737" height="20">
			<td width="13%" class="smalltext8">&nbsp;<b>Customer Name</b></td>
			<td width="20%" class="smalltext8"><b>Order Address</b></td>
			<td width="11%" class="smalltext8"><b>Type</b></td>
			<td width="6%" class="smalltext8"><b>Added On</b></td>
			<td width="7%" class="smalltext8"><b>Completed On</b></td>
			<td width="5%" class="smalltext8"><b>ETA (Hr)</b></td>
			<td width="10%" class="smalltext8"><b>Estimated ETA</b></td>
			<td width="10%" class="smalltext8"><b>Processed By</b></td>
			<td width="9%" class="smalltext8"><b>Customer Rating</b></td>
			<td class="smalltext8"><b>Action</b></td>
		</tr>
		<?php
				$i	=	0;
				while($row					=	mysqli_fetch_assoc($result)){
					$i++;
					$customerId				=	$memberId = $row['memberId'];
					$orderId				=	$row['orderId'];
					$completeName			=	stripslashes($row['completeName']);
					$orderAddress			=	stripslashes($row['orderAddress']);
					$orderType				=	$row['orderType'];
					$orderTypeText			=	$a_customerOrder[$orderType];
					$orderAddedOn			=	showDate($row['orderAddedOn']);
					$orderCompletedOn		=	showDate($row['orderCompletedOn']);
					$isHavingEstimatedTime	=	$row['isHavingEstimatedTime'];
					$employeeWarningDate	=	$row['employeeWarningDate'];
					$employeeWarningTime	=	$row['employeeWarningTime'];
					$totalOrdersPlaced		=	$row['totalOrdersPlaced'];
					$acceptedBy				=	$row['acceptedBy'];
					$acceptedByName			=	stripslashes($row['acceeptedByName']);
					$rateGiven			    =	$row['rateGiven'];
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
				<td class="smalltext17" valign="top"><?php echo $orderAddedOn;?></td>	
				<td class="smalltext17" valign="top"><?php echo $orderCompletedOn;?></td>
				<td class="smalltext17" valign="top"><?php echo $etaHours;?></td>
				<td class="smalltext17" valign="top"><?php echo $expctDelvText;?></td>
				<td class="smalltext17" valign="top"><?php echo $acceptedByName;?></td>
				<td class="smalltext17" valign="top">
					<?php 
						if(!empty($rateGiven)){
							echo $a_existingCustomerRatings[$rateGiven];
						}
						else{
							echo "N/A";
						}

					?>
				</td>
				<td class="smalltext17" valign="top">
					<?php
						if(!array_key_exists($orderId,$a_alreadyChangedRating)){
					?>
					<a onclick="changeOrderRate(<?php echo $orderId;?>,<?php echo $customerId;?>)" class="link_style12" style='cursor:pointer;' title='Delete'>CHANGE RATE</a>
					<?php
						}
						else{
							echo "Changed to - ".$a_existingCustomerRatings[$a_alreadyChangedRating[$orderId]];
						}
					?>
				</td>				
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