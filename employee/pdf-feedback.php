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
	$employeeObj				=	new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	$pagingObj					=  new Paging();
	$memberObj					=  new members();
	$orderObj					=  new orders();

	$fromToDate			=	date('Y-m-d', strtotime("-30 days", strtotime($today_year."-".$today_month."-".$today_day)));

	$andClause1		=	" AND orderAddedOn >= '$fromToDate' AND orderAddedOn <= '$nowDateIndia'";
?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
<?php
	$whereClause	=	"WHERE status=2 AND hasQaDone=1 AND isFeedback=1 AND members_orders.isVirtualDeleted=0";
	$orderBy		=	"orderCompletedOn DESC";
	$queryString	=	"";
	if(isset($_REQUEST['recNo']))
	{
		$recNo		    =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo	=	0;
	}
	
	$start					  =	0;
	$recsPerPage	          =	10;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause1;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"members_orders INNER JOIN  members_orders_reply ON members_orders.orderId=members_orders_reply.orderId INNER JOIN members ON members_orders.memberId=members.memberId";
	$pagingObj->selectColumns = "members_orders.*,firstName,lastName,appraisalSoftwareType,qaDoneBy,hasQaDone";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/pdf-feedback.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
		$i	=	$recNo;
?>
<tr>
	<td colspan="9" height="20"></td>
</tr>
<tr>
	<td colspan="9" class="heading1">
		:: VIEW ALL FEEDBACKS ON COMPLETED ORDERS FOR YOU ::
	</td>
</tr>
<tr>
	<td colspan="9" height="5"></td>
</tr>
<tr bgcolor="#373737" height="20">
	<td class="smalltext8" width="5%">&nbsp;<b>Sr. No</b></td>
	<td class="smalltext8" width="13%"><b>Customer Name</b></td>
	<td class="smalltext8" width="15%"><b>Order Address</b></td>
	<td class="smalltext8" width="9%"><b>Order Type</b></td>
	<td class="smalltext8" width="6%"><b>File Type</b></td>
	<td class="smalltext8" width="9%"><b>Order On</b></td>
	<td class="smalltext8" width="9%"><b>Completed On</b></td>
	<td class="smalltext8" width="12%"><b>QA By</b></td>
	<td></td>
</tr>
<tr>
	<td colspan="9">
		<hr size="1" width="100%" bgcolor="#e4e4e4">
	</td>
</tr>
<?php
		while($row	=   mysql_fetch_assoc($recordSet))
		{
			$i++;
			$orderId				=	$row['orderId'];
			$memberId				=	$row['memberId'];
			$orderAddedOn			=	$row['orderAddedOn'];
			$orderType				=	$row['orderType'];
			$orderAddress			=	stripslashes($row['orderAddress']);
			$firstName				=	stripslashes($row['firstName']);
			$lastName				=	stripslashes($row['lastName']);
			$appraisalSoftwareType	=	$row['appraisalSoftwareType'];
			$orderAddedOn			=	showDate($row['orderAddedOn']);
			$orderCompletedOn		=	showDate($row['orderCompletedOn']);
			$qaDoneBy				=	$row['qaDoneBy'];
			
			$orderText		=	$a_customerOrder[$orderType];

			$customerName	=   $firstName." ".$lastName;
			
			if($appraisalSoftwareType	==	5)
			{
				$appraisalText		=	"ZAP*";
			}
			else
			{
				$appraisalText		=	$a_appraisalFileTypes[$appraisalSoftwareType];
			}
		
			$qaByName		=	$employeeObj->getEmployeeName($qaDoneBy);
	?>
			<tr>
				<td class="smalltext2" valign="top"><b><?php echo $i;?>)</b></td>
				<td class="smalltext2" valign="top"><b><?php echo ucwords($customerName);?></b></td>
				<td class="smalltext2" valign="top"><b><?php echo $orderAddress;?></b></td>
				<td class="smalltext2" valign="top"><b><?php echo $orderText;?></b></td>
				<td class="error" valign="top" align="center"><b><?php echo $appraisalText;?></b></td>
				<td class="smalltext2" valign="top"><b><?php echo $orderAddedOn;?></b></td>
				<td class="smalltext2" valign="top"><b><?php echo $orderCompletedOn;?></b></td>
				<td class="smalltext2" valign="top"><b><?php echo $qaByName;?></b></td>
				<td valign="top" class="link_style2">
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $memberId;?>#feedback"  class="link_style2">VIEW FEEDBACK</a>
					<!--  | <a href="<?php echo SITE_URL_EMPLOYEES;?>/send-message-pdf-customer.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $memberId;?>#sendMessages"  class="link_style2">SEND MESSAGE</a> -->
				</td>
			</tr>
			<tr>
				<td colspan="9">
					<hr size="1" width="100%" bgcolor="#e4e4e4">
				</td>
			</tr>
	<?php
			
		}
		echo "<tr><td align='right' colspan='9'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr>";		
	}
	else
	{
		echo "<tr><td height='50'></td></tr><tr><td align='center' class='error'><b>No Feedback Available For You !!</b></td></tr><tr><td height='200'></td></tr>";
		
	}
    echo "</table>";
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>