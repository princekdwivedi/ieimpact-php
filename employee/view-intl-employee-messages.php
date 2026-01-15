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
	$employeeObj				= new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/common.php");
	$pagingObj					=  new Paging();
	$memberObj					=  new members();
	$orderObj					=  new orders();
	$commonObj					=  new common();
?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
<tr>
	<td colspan="3" class="heading1">
		:: VIEW ALL INTERNAL EMPLOYEES MESSAGES  ::
	</td>
	<td colspan="2" class="heading1">
		&nbsp;&nbsp;<a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php" class="link_style19">CLICK TO VIEW MESSAGES BETWEEN CUSTOMER AND EMPLOYEE</a>
	</td>
</tr>
<tr>
	<td colspan="9" height="5"></td>
</tr>
<?php
	$whereClause	=	"WHERE messageType=1";
	$orderBy		=	"messageId DESC";
	$queryString	=	"";
	
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
	$recsPerPage	          =	30;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"employee_order_customer_messages";
	$pagingObj->selectColumns = "*";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/view-intl-employee-messages.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
		$i	=	$recNo;
?>

<tr bgcolor="#373737" height="20">
	<td class="smalltext8" width="5%">&nbsp;<b>Sr. No</b></td>
	<td class="smalltext8" width="15%"><b>By</b></td>
	<td class="smalltext8" width="15%"><b>Customer name</b></td>
	<td class="smalltext8" width="25%"><b>Order Address</b></td>
	<td class="smalltext8" width="30%"><b>Message</b></td>
	<td class="smalltext8"><b>Date</b></td>
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
			$orderId		=	$row['messageFor'];
			$message		=	stripslashes($row['message']);
			$messageBy		=	$row['messageBy'];
			$addedOn		=	showDate($row['addedOn']);

			$oredrAddress	=	@mysql_result(dbQuery("SELECT orderAddress FROM members_orders WHERE orderId=$orderId"),0);
			$customerId		=	@mysql_result(dbQuery("SELECT memberId FROM members_orders WHERE orderId=$orderId"),0);
			$oredrAddress	=	stripslashes($oredrAddress);

			$messageByName	=	$employeeObj->getEmployeeName($messageBy);
			$customerName	=	$commonObj->getMemberName($customerId);

	?>
			<tr>
				<td class="smalltext2" valign="top"><b><?php echo $i;?>)</b></td>
				<td class="smalltext2" valign="top"><b><?php echo $messageByName;?></b></td>
				<td class="smalltext2" valign="top"><b><?php echo $customerName;?></b></td>
				<td class="smalltext2" valign="top">
					<b>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/internal-emp-msg.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $customerId;?>#messages"  class="link_style2">
							<?php echo nl2br($oredrAddress);?>
						</a>
					</b>
				</td>
				<td class="smalltext2" valign="top"><b><?php echo nl2br($message);?></b></td>
				<td class="smalltext2" valign="top"><b><?php echo $addedOn;?></b></td>
			</tr>
			<tr>
				<td colspan="9">
					<hr size="1" width="100%" bgcolor="#e4e4e4">
				</td>
			</tr>
	<?php
			
		}
		echo "<tr><td align='right' colspan='8'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr>";		
	}
	else
	{
		echo "<tr><td height='50'></td></tr><tr><td align='center' class='error'  colspan='10'><b>No Messages Available !!</b></td></tr><tr><td height='200'></td></tr>";
		
	}
    echo "</table>";
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>