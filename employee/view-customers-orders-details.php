<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/new-pagingclass.php");
	$orderObj					=  new orders();
	$pagingObj					=  new Paging();
	
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	
	$searchBy					=	1;
	$display					=	"";
	$display1					=	"none";
	list($sY,$sM,$sD)			=	explode("-",$nowDateIndia);
	$searchDate					=	$sD."-".$sM."-".$sY;
	$t_searchDate				=	$nowDateIndia;
	$searchMonth				=	$sM;
	$searchYear					=	$sY;
	$employeeId					=	0;
	$checked					=	"checked";
	$checked1					=	"";
	$serachString				=	"searchBy=".$searchBy."&searchDate=".$t_searchDate;
	$text						=	" ON ".showDate($t_searchDate);

	$link						=	"";
	$link1						=	"";
	if(isset($_REQUEST['recNo']))
	{
		$recNo					=	(int)$_REQUEST['recNo'];
		if(!empty($recNo))
		{
			$link				=	"?recNo=".$recNo;
			$link1				=	"&recNo=".$recNo;
		}
	}
	if(empty($recNo))
	{
		$recNo					=	0;
	}

	$whereClause				=	"WHERE members_orders.orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND isActiveCustomer=1 AND isTestAccount=0";
	$orderBy					=	"TotalOrders DESC";
	$queryString				=	"";
	$andClause					=	" AND orderAddedOn='$t_searchDate'";

	$queryString				=	"&".$serachString;

?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
	<tr>
		<td width="21%" align="center" style="height:17px;color:#ffffff;border:3px solid #333333;background-color:#4c4c4c;">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-employee-order-processing.php" class="link_style1">View Employees Orders Summary</a>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td height="5" colspan="10"></td>
	</tr>
</table>
<br>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>

<script type="text/javascript">
function showSearch(flag)
{
	if(flag == 1)
	{
		document.getElementById('displayDate').style.display  = 'inline';
		document.getElementById('displayMonth').style.display = 'none';
	}
	else
	{
		document.getElementById('displayDate').style.display  = 'none';
		document.getElementById('displayMonth').style.display = 'inline';
	}
}
function removeEmployees(Id,flag)
{
	if(flag == 1)
	{
		document.getElementById('showEmployeeDetails'+Id).style.display = 'inline';
	}
	else
	{
		document.getElementById('showEmployeeDetails'+Id).style.display = 'none';
	}
}
function redirectPageLink(path,clause)
{
	window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/view-customers-orders-details.php?"+path+"&"+clause;
}
</script>
<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
	<tr>
		<td class="textstyle1" colspan="6">
			<b>:: VIEW CUSTOMERS TOTAL ORDERS NEW/PROCESSED AND COMPLETED <?php echo $text;?> ::</b>
		</td>
	</tr>
	<tr>
		<td colspan="8" height="10"></td>
	</tr>
</table>

<?php
	
	$showByTotalOrdersAscDesc				=	"totalAsc=1";
	$showByTotalOrdersAscDescText			=	"Ascending By Total Orders";

	$showByCustomerNameAscDesc				=	"nameAsc=1";
	$showByCustomerNameAscDescText			=	"Ascending By Customer Name";

	if(isset($_GET['totalAsc']))
	{
		$totalAsc							=	$_GET['totalAsc'];
		if(!empty($totalAsc))
		{
			$orderBy						=	"TotalOrders";
			$showByTotalOrdersAscDesc		=	"totalDsc=1";
			$showByTotalOrdersAscDescText	=	"Descending By Total Orders";
		}
	}
	elseif(isset($_GET['totalDsc']))
	{
		$totalDsc							=	$_GET['totalDsc'];
		if(!empty($totalDsc))
		{
			$orderBy						=	"TotalOrders DESC";
			$showByTotalOrdersAscDesc		=	"totalAsc=1";
			$showByTotalOrdersAscDescText	=	"Ascending By Total Orders";
		}
	}
	elseif(isset($_GET['nameAsc']))
	{
		$nameAsc							=	$_GET['nameAsc'];
		if(!empty($nameAsc))
		{
			$orderBy						=	"completeName";
			$showByCustomerNameAscDesc		=	"nameDsc=1";
			$showByCustomerNameAscDescText	=	"Descending By Customer Name";
		}
	}
	elseif(isset($_GET['nameDsc']))
	{
		$nameDsc							=	$_GET['nameDsc'];
		if(!empty($nameDsc))
		{
			$orderBy						=	"completeName DESC";
			$showByCustomerNameAscDesc		=	"nameAsc=1";
			$showByCustomerNameAscDescText	=	"Ascending By Customer Name";
		}
	}
	
	
	$start					  =	0;
	$recsPerPage	          =	50;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause." GROUP BY members_orders.memberId";
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"members_orders INNER JOIN members ON members_orders.memberId=members.memberId";
	$pagingObj->selectColumns = "members_orders.memberId,completeName,COUNT(*) AS TotalOrders";
	$pagingObj->primaryColumn =	"members_orders.memberId";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/view-customers-orders-details.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
		$i					  =	$recNo;
?>
<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
	<tr height='25' bgcolor="#373737">
		<td width="6%" class='smalltext12'>
			&nbsp;<b>Sr No</b>
		</td>
		<td width="18%" class='smalltext12'>
			<a onclick="redirectPageLink('<?php echo $showByCustomerNameAscDesc;?>','<?php echo $serachString;?>')" style="cursor:pointer;" title="<?php echo $showByCustomerNameAscDescText?>" class="linkstyle27"><b>CUSTOMER NAME</b><a>
		</td>
		<td width="10%" class='smalltext12'>
			<a onclick="redirectPageLink('<?php echo $showByTotalOrdersAscDesc;?>','<?php echo $serachString;?>')" style="cursor:pointer;" title="<?php echo $showByTotalOrdersAscDescText?>" class="linkstyle27"><b>TOTAL ORDERS</b><a>
		</td>
		<td width="13%" class='smalltext12'>
			<b>TOTAL NEW ORDERS</b>
		</td>
		<td width="13%" class='smalltext12'>
			<b>ORDERS UNDER PROCESS</b>
		</td>
		<td width="8%" class='smalltext12'>
			<b>COMPLETED</b>
		</td>
		<td class='smalltext12'>
			<b>EMPLOYEES</b>
		</td>
	</tr>
	<?php
		$i			=	$recNo;
		while($row	=   mysqli_fetch_assoc($recordSet))
		{
			$i++;
			$completeName					=	stripslashes($row['completeName']);
			$memberId						=	$row['memberId'];
			$totalOrders					=	$row['TotalOrders'];
			$totalNewOrders					=	$employeeObj->getSingleQueryResult("SELECT COUNT(*) as total FROM members_orders WHERE status=0 AND memberId=$memberId AND isVirtualDeleted=0","total");
			$totalCompletedOrders			=	$employeeObj->getSingleQueryResult("SELECT COUNT(*) as total FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND status IN (2,5,6) AND memberId=$memberId".$andClause,"total");

			$totalUnderProcessOrders		=	$employeeObj->getSingleQueryResult("SELECT COUNT(*) as total FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND status=1 AND memberId=$memberId".$andClause,"total");
			if(empty($totalUnderProcessOrders))
			{
				$totalUnderProcessOrders	=	0;
			}


			$a_assignedEmployee				=	array();

			$query1							=	"SELECT acceptedBy,COUNT(*) AS TotalProcessedOrders FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND memberId=$memberId AND status IN (1,2,5,6)".$andClause." GROUP BY acceptedBy ORDER BY TotalProcessedOrders DESC";
			$result1						=	dbQuery($query1);
			if(mysqli_num_rows($result1))
			{
				while($row1					=	mysqli_fetch_assoc($result1))
				{
					$acceptedBy				=	$row1['acceptedBy'];
					$totalProcessedOrders	=	$row1['TotalProcessedOrders'];
					$acceptedByName			=	$employeeObj->getEmployeeName($acceptedBy);
					$a_assignedEmployee[]	=	$acceptedByName."(".$totalProcessedOrders.")";
				}
				
				$a_assignedEmployee			=	implode(", ",$a_assignedEmployee);
			}
			else
			{
				$a_assignedEmployee			=	"";
			}

			$bgColor						=	"class='rwcolor1'";
			if($i%2==0)
			{
				$bgColor					=	"class='rwcolor2'";
			}

	?>
	<tr <?php echo $bgColor;?> height="30">
		<td class="smalltext2" valign="top">
			<?php echo $i;?>)
		</td>
		<td class="smalltext2" valign="top">
			<?php
				$url			=	SITE_URL_EMPLOYEES."/display-customers-all-orders.php?".$serachString."&customerId=";
			?>
			<a onclick="commonFunc('<?php echo $url;?>','showEmployeeDetails<?php echo $memberId;?>',<?php echo $memberId;?>);removeEmployees(<?php echo $memberId;?>,1);" class='link_style2' style="cursor:pointer">
				<?php echo $completeName;?>
			</a>
		</td>
		<td class="smalltext2" valign="top" style="text-align:center;">
			<b><?php 
					echo $totalOrders;
			?></b>
		</td>
		<td class="smalltext2" valign="top" style="text-align:center;">
			<b><?php 
					echo $totalNewOrders;
			?></b>
		</td>
		<td class="smalltext2" valign="top" style="text-align:center;">
			<b><?php echo $totalUnderProcessOrders;?></b>
		</td>
		<td class="smalltext2" valign="top" style="text-align:center;">
			<b><?php 
					echo $totalCompletedOrders;
			?></b>
		</td>
		<td class="textstyle1" valign="top">
			<?php echo $a_assignedEmployee;?>
		</td>
	</tr>
	<tr>
		<td colspan='15'>
			<div id="showEmployeeDetails<?php echo $memberId;?>"></div>
		</td>
	</tr>
	<?php
		}

		echo "<tr><td align='right' colspan='12'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr></table>";	
	?>
</table>
<?php
	}
	else
	{
		echo "<table><tr><td align='center' class='error' colspan='8' height='100'><b>No Record Found !</b></td></tr><tr><td height='200'></td></tr></table>";
	}

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>