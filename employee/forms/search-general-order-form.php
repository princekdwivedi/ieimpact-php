<?php
	if(!isset($searchOrderType))
	{
		$searchOrderType	=	 0;
	}
	if(!isset($searchOrder))
	{
		$searchOrder		=	"";
	}
	if(!isset($searchName))
	{
		$searchName			=	"";
	}

	if(!isset($a_searchOrderType))
	{
		$a_searchOrderType	=	array("1"=>"New","2"=>"Accepted","3"=>"Completed","4"=>"Incompleted","5"=>"Need Attention");
	}
		
	if(!isset($a_searchRushSketch))
	{
		$a_searchRushSketch	=	array("-1"=>"All","2"=>"24 Hours","0"=>"12 Hours","1"=>"6 Hours","3"=>"Getting Late");
	}
?>


<?php
	if(!isset($donotCallSearchJquery))
	{
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<?php
	}	
?>
<script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
</script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" />


<script type="text/javascript">
$().ready(function() {
	$("#orderAddress").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/employees-pdf-orders.php", {width: 365,selectFirst: false});
});

$().ready(function() {
	$("#searchName").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-customer.php", {width: 290,selectFirst: false});
});


function checkForNumber()
{
	k = (document.all)?event.keyCode : arguments.callee.caller.arguments[0].which;
	if(k == 8 || k== 0)
	{
		return true;
	}
	if(k >= 48 && k <= 57 )
	{
		return true;
	}
	else
	{
		return false;
	}
 }
 function pageRedirectIntoUrl(url)
 {
	location.href   = url;
 }
</script>
<form name="searchPdfOrderForm" action="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php"  method="GET">
	<table width="99%" align="center" border="0" cellpadding="0" cellspacing="0">
	   	<tr>
			<td width="6%" class="textstyle1">TYPE</td>
			<td width="2%" class="textstyle1">:</td>
			<td class="smalltext2" width="40%">
				<?php
					foreach($a_searchOrderType as $k=>$v)
					{
						echo "<input type='radio' name='searchOrderType' value='$k'>$v&nbsp;";
					}
				?>
			</td>
			<td class="textstyle1" width="10%">ADDRESS</td>
			<td class="textstyle1" width="2%">:</td>
			<td class="smalltext2" valign="top">
				<input type='text' name="searchOrder" size="51" value="" id="orderAddress"  style="border:1px solid #4d4d4d;height:25px;font-size:15px;">
						
			</td>
		</tr>
		<tr>
			<td colspan="8" height="8"></td>
		</tr>
		<tr>
			<?php
				if(isset($_SESSION['hasManagerAccess']) && !empty($s_hasManagerAccess))
				{
			?>
			<td class="textstyle1">NAME</td>
			<td class="textstyle1">:</td>
			<td class="smalltext2">
				<input type='text' name="searchText" size="40" value="" id="searchName" style="border:1px solid #4d4d4d;height:25px;font-size:15px;">
			</td>
			<?php
				}
			?>
			<td class="textstyle1">DELIVERY</td>
			<td class="textstyle1">:</td>
			<td class="smalltext2" colspan="4">
			<?php
				foreach($a_searchRushSketch as $k=>$v)
				{
					echo "<input type='radio' name='searchRushSketch' value='$k'>$v&nbsp;";
				}
			?>
			<input type='hidden' name="searchText" size="40" value="" id="searchName" style="border:1px solid #4d4d4d;height:25px;font-size:15px;">
		</td>
		</tr>
		<tr>
			<td colspan="8" height="6"></td>
		</tr>
		<tr>
			<td colspan="6">
				<input type="image" name="submit" src="<?php echo SITE_URL;?>/images/small-submit.png" border="0" style="cursor:pointer;">
				<img src="<?php echo SITE_URL;?>/images/reset-small.png" border="0" onClick="document.searchPdfOrderForm.reset()" style="cursor:pointer;" title="Reset">
				<input type='hidden' name='searchFormSubmit' value='1'>
			</td>
		</tr>
		<tr>
			<td colspan="8" height="8"></td>
		</tr>
	</table>
</form>
<br />
<?php
	$displayMarqueMessageDateTime				=	 timeBetweenBeforeMinutes($nowDateIndia,$nowTimeIndia,30);
	list($searchMessageDate,$searchMessageTime)	=	explode("=",$displayMarqueMessageDateTime);

	if($a_marqueeCustomers =	$orderObj->getCustomersMostRecentMessages($searchMessageDate,$searchMessageTime))
	{

		$a_marqueeCustomers	=	implode(", ",$a_marqueeCustomers);
	?>
	<table width='99%' align='center' cellpadding='2' cellspacing='2' border='0'>
		<tr>
			<td width="16%" class="heading3" valign="top">New Messages From :</td>
			<td class="text1" valign="top">
				<?php echo $a_marqueeCustomers;?>
			</td>
		</tr>
	</table>
	<?php
	}
	if(!isset($allTotalCustomersNewOrders))
	{
		$allTotalCustomersNewOrders	=	$employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND status=0 AND orderAddedOn >= '2012-04-01' AND isDeleted=0 AND isVirtualDeleted=0 AND isNotVerfidedEmailOrder=0","total");
	}
	if(!isset($_SESSION['s_allTotalUnrepliedOrderMsg']))
	{
		$totalUnrepliedOrdersMsg	=	$orderObj->getTotalUnrepliedOrderMessage();
	}
	else
	{
		$totalUnrepliedOrdersMsg	=	$_SESSION['s_allTotalUnrepliedOrderMsg'];
	}
	if(!isset($_SESSION['s_allTotalUnrepliedRatingMsg']))
	{
		$totalUnrepliedRatingMsg	=	$orderObj->getAllTotalUnrepliedRatingMessage();
		
	}
	else
	{
		$totalUnrepliedRatingMsg	=	 $_SESSION['s_allTotalUnrepliedRatingMsg'];
	}
	if(!isset($_SESSION['s_allTotalUnrepliedGeneralMsg']))
	{
		$totalUnrepliedGeneralMsg	=	$orderObj->getAllTotalUnrepliedGeneralMessage();
	}
	else
	{
		$totalUnrepliedGeneralMsg	=   $_SESSION['s_allTotalUnrepliedGeneralMsg'];
	}
	if(!isset($_SESSION['s_allTotalExceedTatOrders']))
	{
		$totalExceedTatOrders		=	$orderObj->getAllTotalExceedTatOrders();

	}
	else
	{
		$totalExceedTatOrders		=   $_SESSION['s_allTotalExceedTatOrders'];
	
	}
	if(!isset($_SESSION['s_allTotalUncheckedOrders']))
	{
		$totalUncheckedOrders		=	$orderObj->getAllTotalUncheckedOrders();
	}
	else
	{
		$totalUncheckedOrders		=   $_SESSION['s_allTotalUncheckedOrders'];
	}
?>
<table width="99%" align="center" border="0" cellpadding="0" cellspacing="0">
	 <tr>
		<td align="left">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&orderOf=<?php echo $s_employeeId;?>&showingEmployeeOrder=1&Olink=1<?php echo $addTopUrlExtraLinkTestQ;?>" class='link_style6' style="cursor:pointer;">ALL MY ORDERS</a>
			|
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&orderOf=<?php echo $s_employeeId;?>&showingEmployeeOrder=1&displayTypeCompleted=1&Olink=2<?php echo $addTopUrlExtraLinkTestQ;?>" class='link_style6' style="cursor:pointer;">ALL MY QA ORDERS</a>
		<?php
			if(!empty($allTotalCustomersNewOrders) && !empty($s_hasManagerAccess))
			{
				//$assignNewUrl	 =	SITE_URL_EMPLOYEES."/assign-customer-orders.php";
				$assignNewUrl    =	SITE_URL_EMPLOYEES."/assign-all-new-orders.php".$topUrlExtraLinkTestQ;
		?>
			|
			<a href="<?php echo $assignNewUrl;?>" class='link_style6' style="cursor:pointer;">ASSIGN ALL NEW ORDERS - <?php echo $allTotalCustomersNewOrders;?></a>
		<?php
			}
			if(!empty($totalUnrepliedOrdersMsg))
			{
			?>
			| 
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?unrepliedMsg=1<?php echo $addTopUrlExtraLinkTestQ;?>#second" class='link_style6' style="cursor:pointer;">UNREPLIED MESSAGES - <?php echo $totalUnrepliedOrdersMsg;?></a>
		<?php
			}
			if(!empty($totalUnrepliedRatingMsg))
			{
			?>
			| 
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?unrepliedRatingMsg=1<?php echo $addTopUrlExtraLinkTestQ;?>#third" class='link_style6' style="cursor:pointer;">UNREPLIED RATINGS - <?php echo $totalUnrepliedRatingMsg;?></a> 
		<?php
			}
			if(!empty($totalUnrepliedGeneralMsg))
			{
			?>
			|
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?unrepliedGeneralMsg=1<?php echo $addTopUrlExtraLinkTestQ;?>#fifth" class='link_style6' style="cursor:pointer;">GENERAL MESSAGES - <?php echo $totalUnrepliedGeneralMsg;?></a>
		<?php
			}
			if(!empty($totalExceedTatOrders))
			{
		?>
			|
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&searchExceedTat=1<?php echo $addTopUrlExtraLinkTestQ;?>" class='link_style6' style="cursor:pointer;">EXCEEDED TAT - <?php echo $totalExceedTatOrders;?></a>
		<?php
			}
			if(!empty($totalUncheckedOrders))
			{
		?>
			|
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&searchUnchecked=1<?php echo $addTopUrlExtraLinkTestQ;?>" class='link_style6' style="cursor:pointer;">UNCHECKED ORDERS - <?php echo $totalUncheckedOrders;?></a>
		<?php
			}
		?>
		&nbsp;</td>
	</tr>
	<tr>
		<td height="5"></td>
	</tr>
</table>