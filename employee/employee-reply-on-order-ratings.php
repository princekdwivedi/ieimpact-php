<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	$pagingObj					= new Paging();
	$employeeObj				= new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	$orderObj					=  new orders();

	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	if(isset($_REQUEST['recNo']))
	{
		$recNo					=	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo					=	0;
	}
	$serachRatingBy				=	0;
	$searchCustomer				=	"";
	$searchOrderBy				=	"";
	$fromDate					=	"";
	$toDate						=	"";
	$a_searchRating				=	array("0"=>"ALL","1"=>"POOR","2"=>"AVERAGE");
	$a_searchOrderBy			=	array("1"=>"NEW ORDERS|members_orders.orderId DESC","2"=>"OLD ORDERS|members_orders.orderId");

	$whereClause				=	"WHERE members_orders.orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND rateGiven IN (1,2) AND isTestAccount=0 AND isRateCountingEmployeeSide='yes'";
	$andClause					=	"";
	$queryString				=	"";
	$orderBy					=	"members_orders.orderId DESC";

	if(isset($_GET['serachRatingBy']))
	{
		$serachRatingBy			=	$_GET['serachRatingBy'];
		if(!empty($serachRatingBy))
		{
			$whereClause		=	"WHERE members_orders.orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND isRateCountingEmployeeSide='yes' AND rateGiven=".$serachRatingBy;
			$queryString	   .=	"&serachRatingBy=".$serachRatingBy;
		}
	}
	if(isset($_GET['searchCustomer']))
	{
		$searchCustomer			=	$_GET['searchCustomer'];
		$searchCustomer			=	trim($searchCustomer);
		if(!empty($searchCustomer))
		{
			$andClause		   .=	" AND completeName='$searchCustomer'";
			$queryString	   .=	"&searchCustomer=".$searchCustomer;
		}
	}
	if(isset($_GET['searchOrderBy']))
	{
		$searchOrderBy			=	$_GET['searchOrderBy'];
		if(!empty($searchOrderBy))
		{
			$searchOrderByText		=	$a_searchOrderBy[$searchOrderBy];
			list($b,$orderByText)	=	explode("|",$searchOrderByText);
			$orderBy				=	$orderByText;
			$queryString		   .=	"&searchOrderBy=".$searchOrderBy;
		}
	}
	if(isset($_GET['fromDate']))
	{
		$fromDate					=	$_GET['fromDate'];
		if(!empty($fromDate))
		{
			list($d,$m,$y)			=	explode("-",$fromDate);
			$t_fromDate				=	$y."-".$m."-".$d;
			$dateClause				=	" AND rateGivenOn='$t_fromDate'";
			$queryString		   .=	"&fromDate=".$fromDate;
			if(isset($_GET['toDate']))
			{
				$toDate				=	$_GET['toDate'];
				if(!empty($toDate))
				{
					list($d1,$m1,$y1)=	explode("-",$toDate);
					$t_toDate		=	$y1."-".$m1."-".$d1;
					$dateClause		=	" AND rateGivenOn >= '$t_fromDate' AND rateGivenOn <= '$t_toDate'";
					$queryString   .=	"&toDate=".$toDate;

				}
			}

			$andClause			   .=	$dateClause;
		}
	}
?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
	<tr>
		<td height="5"></td>
	</tr>
	<tr>
		<td class="heading3">
			:: VIEW EMPLOYEES EXPLANATION ON CUSTOMER COMPLETED ORDERS WITH POOR & AVERAGE RATINGS ::
		</td>
	</tr>
	<tr>
		<td colspan="8" height="5"></td>
	</tr>
</table>
<br>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
</script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" />

<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>
<script type="text/javascript">
$().ready(function() {
	$("#searchName").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-customer.php", {width: 230,selectFirst: false});
});
</script>
<form name="searchRepliedOrder" action="" method="GET">
	<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="8%" class="text1">
				<select name="serachRatingBy">
					<?php
						foreach($a_searchRating as $key=>$value)
						{
							$select		=	"";
							if($serachRatingBy == $key)
							{
								$select	=	"selected";
							}
							echo "<option value='$key' $select>$value</option>";
						}
					?>
				</select>
			</td>
			<td width="19%" class="text1">RATINGS ORDERS OF CUSTOMER</td>
			<td class="smalltext4" width="20%">
				<input type='text' name="searchCustomer" size="30" value="<?php echo $searchCustomer;?>" id="searchName" style="border:1px solid #4d4d4d;height:25px;font-size:15px;">
			</td>
			<td width="8%" class="text1">&nbsp;FOR/FROM</td>
			<td width="9%" class="text1">
				<input type="text" name="fromDate" value="<?php echo $fromDate;?>" id="dateFor" size="10" readonly style="border:1px solid #4d4d4d;height:15px;font-size:10px;">&nbsp;&nbsp;<a href="javascript:NewCssCal('dateFor','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
			</td>
			<td width="4%" class="text1">&nbsp;TO</td>
			<td width="8%" class="text1">
				<input type="text" name="toDate" value="<?php echo $toDate;?>" id="dateTo" size="10" readonly style="border:1px solid #4d4d4d;height:15px;font-size:10px;">&nbsp;&nbsp;<a href="javascript:NewCssCal('dateTo','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
			</td>
			<td width="7%" class="text1">&nbsp;ORDER BY</td>
			<td width="10%" class="text1">
				<select name="searchOrderBy">
					<?php
						foreach($a_searchOrderBy as $key=>$value)
						{
							list($orderByText,$a)	=	explode("|",$value);
							$select		=	"";
							if($searchOrderBy == $key)
							{
								$select	=	"selected";
							}
							echo "<option value='$key' $select>$orderByText</option>";
						}
					?>
				</select>
			</td>
			<td>
				<input type="submit" name="submit" value="Search" border="0">
				<input type='hidden' name='searchFormSubmit' value='1'>
			</td>
			<td>&nbsp;</td>
		</tr>
	</table>
</form>
<?php
	$start					  =	0;
	$recsPerPage	          =	15;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"members_orders INNER JOIN reply_on_orders_rates ON members_orders.orderId=reply_on_orders_rates.orderId INNER JOIN members ON members_orders.memberId=members.memberId";
	$pagingObj->selectColumns = "members_orders.*,comment,addedby,reply_on_orders_rates.addedOn,processQaEmployee,firstName";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/employee-reply-on-order-ratings.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
?>
<br>
<table width="98%" align="center" border="0" cellpadding="1" cellspacing="1">
	<tr>
		<td width="3%" class="smalltext2">
			&nbsp;
		</td>
		<td width="20%" class="smalltext2">
			<b>Order Address</b>
		</td>
		<td width="12%" class="smalltext2">
			<b>Customer Name</b>
		</td>
		<td width="8%" class="smalltext2">
			<b>Rating On</b>
		</td>
		<td width="5%" class="smalltext2">
			<b>Rating</b>
		</td>
		<td width="15%" class="smalltext2">
			<b>Message</b>
		</td>
		<td width="7%" class="smalltext2">
			<b>Process By</b>
		</td>
		<td width="7%" class="smalltext2">
			<b>QA By</b>
		</td>
		<td width="15%" class="smalltext2">
			<b>Explanation</b>
		</td>
		<td class="smalltext2">
			<b>By</b>
		</td>
	</tr>
	<tr>
		<td colspan='10'>
			<hr size='1' width='100%' bgcolor='#bebebe'>
		</td>
	</tr>
	<?php
		$i	=	$recNo;
		while($row	=   mysql_fetch_assoc($recordSet))
		{
			$i++;
			$orderId				=	$row['orderId'];
			$memberId				=	$row['memberId'];
			$orderAddress			=	stripslashes($row['orderAddress']);
			$orderAddedOn			=	showDate($row['orderAddedOn']);
			$rateGivenOn			=	showDate($row['rateGivenOn']);
			$rateGiven				=	$row['rateGiven'];
			$memberRateMsg			=	stripslashes($row['memberRateMsg']);
			$comment				=	stripslashes($row['comment']);
			$acceptedBy				=	$row['acceptedBy'];
			$explanationBy			=	$row['addedby'];
			$customerName			=	stripslashes($row['firstName']);
			$qaDoneBy				=	@mysql_result(dbQuery("SELECT qaDoneBy FROM members_orders_reply WHERE orderId=$orderId AND memberId=$memberId AND hasQaDone=1"),0);

			$acceptedByEmployee		=	$employeeObj->getEmployeeFirstName($acceptedBy);
			$qaDoneByEmployee		=	$employeeObj->getEmployeeFirstName($qaDoneBy);
			$explanationByEmployee	=	$employeeObj->getEmployeeFirstName($explanationBy);
			
			$ratingText				=	"POOR";
			if($rateGiven			==	2)
			{
				$ratingText			=	"AVERAGE";
			}
	?>
	<tr>
		<td class="smalltext2" valign="top">
			<?php echo $i;?>.
		</td>
		<td class="smalltext2" valign="top">
			<?php
				echo "<a href='".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=$orderId&customerId=$memberId' class='link_style15'>$orderAddress</a>";
			?>
		</td>
		<td class="smalltext2" valign="top">
			<?php 
				echo "<a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmitAdvancedSearch=1&serachCustomerId=$memberId' class='link_style15'>$customerName</a>";
			?>
		</td>
		<td class="smalltext2" valign="top">
			<?php echo $rateGivenOn;?>
		</td>
		<td class="error" valign="top">
			<?php echo $ratingText;?>
		</td>
		<td class="smalltext2" valign="top">
			<?php echo nl2br($memberRateMsg);?>
		</td>
		<td class="smalltext2" valign="top">
			<b><?php echo $acceptedByEmployee;?></b>
		</td>
		<td class="smalltext2" valign="top">
			<b><?php echo $qaDoneByEmployee;?></b>
		</td>
		<td class="smalltext2" valign="top">
			<?php echo nl2br($comment);?>
		</td>
		<td class="smalltext2" valign="top">
			<b><?php echo $explanationByEmployee;?></b>
		</td>
	</tr>
	<tr>
		<td colspan='10'>
			<hr size='1' width='100%' bgcolor='#bebebe'>
		</td>
	</tr>
	<?php
		}
	?>
</table>
<?php
		echo "<table width='100%'><tr><td align='right'><center>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</center></td></tr></table>";
	}
	else
	{
		echo "<br><br><center><font class='error'>NO RECORD FOUND</font></center><br><br><br><br><br><br><br><br>";
	}

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>