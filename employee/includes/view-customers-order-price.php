<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT	.  "/admin/includes/top.php");
	include(SITE_ROOT	.  "/admin/includes/common-array.php");
	include(SITE_ROOT	.  "/classes/pagingclass-test.php");
	$pagingObj			=  new Paging();

	if(isset($_REQUEST['recNo']))
	{
		$recNo		    =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo			=	0;
	}
	$whereClause				=	"WHERE isInactiveRate=0";
	$andClause					=	"";
	$queryString				=	"";

	if(isset($_GET['searchName']))
	{
		$searchName				=	$_GET['searchName'];
		$searchName				=	trim($searchName);

		if(!empty($searchName))
		{
			$andClause			=	" AND completeName='$searchName'";
		}
	}
	
	$serachByTotalOrdersAsc		=	0;
	$serachByTotalOrdersDesc	=	0;
	$serachByRateAsc			=	1;
	$serachByRateDesc			=	0;
	$orderBy					=	"customerRates";
	$totalOrderUpImg			=	"sort_up_green.png";
	$totalOrderDnImg			=	"sort_down_grey.png";
	$orderRateUpImg				=	"sort_up_grey.png";
	$orderRateDnImg				=	"sort_down_grey.png";
	$searchName					=	"";

	if(isset($_GET['totalAsc']))
	{
		$serachByTotalOrdersAsc	=	$_GET['totalAsc'];
		if(!empty($serachByTotalOrdersAsc))
		{
			$orderBy			=	"totalOrdersPlaced";
			$totalOrderUpImg	=	"sort_up_green.png";
			$totalOrderDnImg	=	"sort_down_grey.png";
			$orderRateUpImg		=	"sort_up_grey.png";
			$orderRateDnImg		=	"sort_down_grey.png";
			$queryString		=	"&totalAsc=1";
		}
	}
	elseif(isset($_GET['totalDsc']))
	{
		$serachByTotalOrdersDesc	=	$_GET['totalDsc'];
		if(!empty($serachByTotalOrdersDesc))
		{
			$orderBy			=	"totalOrdersPlaced DESC";
			$totalOrderUpImg	=	"sort_up_grey.png";
			$totalOrderDnImg	=	"sort_down_green.png";
			$orderRateUpImg		=	"sort_up_grey.png";
			$orderRateDnImg		=	"sort_down_grey.png";
			$queryString		=	"&totalDsc=1";
		}
	}
	elseif(isset($_GET['rateAsc']))
	{
		$serachByRateAsc		=	$_GET['rateAsc'];
		if(!empty($serachByRateAsc))
		{
			$orderBy			=	"customerRates";
			$totalOrderUpImg	=	"sort_up_grey.png";
			$totalOrderDnImg	=	"sort_down_grey.png";
			$orderRateUpImg		=	"sort_up_green.png";
			$orderRateDnImg		=	"sort_down_grey.png";
			$queryString		=	"&rateAsc=1";
		}
	}
	elseif(isset($_GET['rateDsc']))
	{
		$serachByRateDesc		=	$_GET['rateDsc'];
		if(!empty($serachByRateDesc))
		{
			$orderBy			=	"customerRates DESC";
			$totalOrderUpImg	=	"sort_up_grey.png";
			$totalOrderDnImg	=	"sort_down_grey.png";
			$orderRateUpImg		=	"sort_up_grey.png";
			$orderRateDnImg		=	"sort_down_green.png";
			$queryString		=	"&rateDsc=1";
		}
	}

?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
</script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" />
<script type="text/javascript">
$().ready(function() {
	$("#orderCustomerName").autocomplete("<?php echo SITE_URL?>/admin/search-complete-customer.php", {width: 220,selectFirst: false});
});

function redirectViewPageTo(flag)
{
	window.location.href="<?php echo SITE_URL;?>/admin/view-customers-order-price.php?"+flag;
}
function openCustomerWindow(memberId)
{
	path = "<?php echo SITE_URL?>/admin/view-customer.php?memberId="+memberId;
	prop = "toolbar=no,scrollbars=yes,width=1150,height=650,top=50,left=100";
	window.open(path,'',prop);
}
function isValidSearchCustomer()
{
	form1	=	document.searchCustomerName;
	if(form1.searchName.value == "" || form1.searchName.value == "0")
	{
		alert("Please enter customer name !!");
		form1.searchName.focus();
		return false;
	}
}
</script>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td colspan="2" class='title5'>View Various Customers Rates</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
</table>
<form name="searchCustomerName" action="" method="GET" onsubmit="return isValidSearchCustomer();">
	<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
		<tr>
			<td width="25%" class="title5">Short By Total Order Added</td>
			<td width="22" valign="bottom">
				<img src="<?php echo SITE_URL;?>/images/<?php echo $totalOrderUpImg;?>" border="0" title="Total Orders By Ascending Order" onclick="redirectViewPageTo('totalAsc=1')" height="20" width="20" style="cursor:pointer;">
			</td>
			<td width="22">
				<img src="<?php echo SITE_URL;?>/images/<?php echo $totalOrderDnImg;?>" border="0" title="Total Orders By Descending Order" onclick="redirectViewPageTo('totalDsc=1')" height="20" width="20" style="cursor:pointer;">
			</td>
			<td class="smalltext2" width="2%">&nbsp;</td>
			<td width="10%" class="title5">Order Rate</td>
			<td width="22" valign="bottom">
				<img src="<?php echo SITE_URL;?>/images/<?php echo $orderRateUpImg;?>" border="0" title="Rates By Ascending Order" onclick="redirectViewPageTo('rateAsc=1')" height="20" width="20" style="cursor:pointer;">
			</td>
			<td width="22">
				<img src="<?php echo SITE_URL;?>/images/<?php echo $orderRateDnImg;?>" border="0" title="Rates By Descending Order" onclick="redirectViewPageTo('rateDsc=1')" height="20" width="20" style="cursor:pointer;">
			</td>
			<td class="smalltext2" width="2%">&nbsp;</td>
			<td width="15%" class="title5">Search Customer</td>
			<td width="15%">
				<input type='text' name="searchName" size="38 value="<?php echo $searchName;?>" id="orderCustomerName">
			</td>
			<td class="smalltext2">
				<input type="image" name="submit" src="<?php echo SITE_URL;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
	</table>
</form>
<?php
	$start					  =	0;
	$recsPerPage	          =	50;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause." GROUP BY customer_rates.customerId";
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"customer_rates INNER JOIN members ON customer_rates.customerId=members.memberId";
	$pagingObj->selectColumns = "memberId,completeName,email,totalOrdersPlaced,customerRates";
	$pagingObj->primaryColumn =	"customer_rates.customerId";
	$pagingObj->path		  = SITE_URL."/admin/view-customers-order-price.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet			  = $pagingObj->getRecords();

		$i					  =	$recNo;
?>

<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td width="title2" width="10%"><b>Sr No.</b></td>
		<td width="title2" width="20%"><b>Name</b></td>
		<td width="title2" width="25%"><b>Email</b></td>
		<td width="title2" width="15%"><b>Total Orders</b></td>
		<td width="title2"><b>Order rate</b></td>
	</tr>
	<tr>
		<td colspan="5">
			<hr size="1" width="100%" bgcolor="#bebebe;">
		</td>
	</tr>
	<?php
			while($row	=   mysql_fetch_assoc($recordSet))
			{
				$i++;
				$memberId				=	$row['memberId'];
				$completeName			=	stripslashes($row['completeName']);
				$email					=	$row['email'];
				$totalOrdersPlaced		=	$row['totalOrdersPlaced'];
				$customerRates			=	$row['customerRates'];

				if(empty($customerRates))
				{
					$customerRates		  =	 @mysql_result(dbQuery("SELECT price FROM customer_order_default_price WHERE priceSetFor=1"),0);
					if(empty($customerRates))
					{
						$customerRates	  =	 STANDARD_ORDER_PRICE;
					}
				}
	?>
	<tr>
		<td width="smalltext2"><?php echo $i;?></td>
		<td width="smalltext2"><a onclick="openCustomerWindow(<?php echo $memberId;?>)" style="cursor:pointer" class="linkstyle7"><?php echo $completeName;?></a></td>
		<td width="smalltext2"><?php echo $email;?></td>
		<td width="smalltext2"><?php echo $totalOrdersPlaced;?></td>
		<td width="smalltext2"><?php echo $customerRates;?></td>
	</tr>
	<tr>
		<td colspan="5">
			<hr size="1" width="100%" bgcolor="#bebebe;">
		</td>
	</tr>
	<?php
			}
		echo "<tr><td colspan='5' align='center'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr>";
	?>
</table>
<?php
		
	}
	else
	{
		echo "<br><br><center><font class='error'><b>No Record Found !!</b></font></center>";
	}

	include(SITE_ROOT	."/admin/includes/bottom.php");
?>