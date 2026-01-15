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
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/common.php");
	$employeeObj				=  new employee();
	$pagingObj					=  new Paging();
	$memberObj					=  new members();
	$orderObj					=  new orders();
	$commonObj					=  new common();
	$employeeId					=	0;
	$text						=  "View Your Order History";

	if(isset($_REQUEST['recNo']))
	{
		$recNo					=	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo	=	0;
	}
	$whereClause				=	"WHERE (acceptedBy=$s_employeeId OR qaDoneBy=$s_employeeId)";
	$andClause					=	"";
	$orderBy					=	"orderAddedOn DESC";
	$queryString				=	"";
	$table						=	"members_orders INNER JOIN members_orders_reply ON members_orders.orderId=members_orders_reply.orderId INNER JOIN members ON members_orders.memberId=members.memberId";
	if($s_hasManagerAccess)
	{
		$whereClause			=	"";
		$text					=	"All Employees Order History";
		if(isset($_GET['employeeId']))
		{
			$employeeId			=	(int)$_GET['employeeId'];
			if(!empty($employeeId))
			{
				$whereClause	=	"WHERE (acceptedBy=$employeeId OR qaDoneBy=$employeeId)";
				$queryString	=	"&employeeId=$employeeId";
			}
		}
	}

?>
<script type="text/javascript">
function validSearch()
{
	form1	=	document.serachEmployeesOrder;
	if(form1.employeeId.value ==	"0")
	{
		alert("Please select an employee !!");
		form1.employeeId.focus();
		return false;
	}
}
</script>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='heading' colspan="4"><?php echo $text;?></td>
	</tr>
	<?php
		if($s_hasManagerAccess)
		{
	?>
	<form name="serachEmployeesOrder" action="" method="GET" onsubmit="return validSearch();">
		<tr>
			<td colspan="4" height="10"></td>
		</tr>
		<tr>
			<td width="20%" class="textstyle1">View Orders Of Employee</td>
			<td width="1%" class="textstyle1">:</td>
			<td width="20%">
				<select name="employeeId">
					<option value="0">Select</option>
					<?php
						if($result		=	$employeeObj->getAllPdfEmployees())
						{
								while($row		=	mysql_fetch_assoc($result))
								{
									$t_employeeId	=	$row['employeeId'];
									$t_firstName	=	stripslashes($row['firstName']);
									$t_lastName		=	stripslashes($row['lastName']);

									$employeeName	=	$t_firstName." ".$t_lastName;

									$select			=	"";
									if($t_employeeId==	$employeeId)
									{
										$select		=	"selected";
									}

									echo "<option value='$t_employeeId' $select>$employeeName</option>";
								
								}
						}
					?>
				</select>
			</td>
			<td>
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
		<tr>
			<td colspan="4" height="10"></td>
		</tr>
	</form>
	<?php
		}
	?>
</table>
<?php

	$start						=	0;
	$recsPerPage				=	25;	//	how many records per page
	$showPages					=	10;	
	$pagingObj->recordNo	    =	$recNo;
	$pagingObj->startRow	    =	$recNo;
	$pagingObj->whereClause     =	$whereClause.$andClause;
	$pagingObj->recsPerPage     =	$recsPerPage;
	$pagingObj->showPages	    =	$showPages;
	$pagingObj->orderBy		    =	$orderBy;
	$pagingObj->table		    =	$table;
	$pagingObj->selectColumns   =	"members_orders.*,firstName,lastName,qaDoneBy";
	$pagingObj->path			=	SITE_URL_EMPLOYEES."/order-history.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
		$i	=	$recNo;
?>
<table width='100%' align='center' cellpadding='2' cellspacing='2' border='0'>
	<tr>
		<td width='12%' class='smalltext2'><b>Customer Name</b></td>
		<td width='12%' class='smalltext2'><b>Order Address</b></td>
		<td width='7%'  class='smalltext2'><b>Order Type</b></td>
		<td width='7%'  class='smalltext2'><b>Order On</b></td>
		<td width='8%' class='smalltext2'><b>Accepted By</b></td>
		<td class='smalltext2' width="7%"><b>Qa By</b></td>
		<td width='8%'  class='smalltext2'><b>Status</b></td>
		<td width='7%' class='smalltext2'><b>Qa Rating</b></td>
		<td class='smalltext2' width='12%'><b>Message</b></td>
		<td width='7%' class='smalltext2'><b>Cus. Rating</b></td>
		<td class='smalltext2'><b>Message</b></td>
	</tr>
	<tr>
		<td colspan="13">
			<hr size="1" width="100%" color="#bebebe">
		</td>
	</tr>
	<?php
		while($row	=   mysql_fetch_assoc($recordSet))
		{
			$i++;
			$orderId		=	$row['orderId'];
			$customerId		=	$row['memberId'];
			$orderAddedOn	=	$row['orderAddedOn'];
			$orderType		=	$row['orderType'];
			$orderAddress	=	stripslashes($row['orderAddress']);
			$firstName		=	stripslashes($row['firstName']);
			$lastName		=	stripslashes($row['lastName']);
			$status			=	$row['status'];
			$acceptedBy		=	$row['acceptedBy'];
			$rateGiven		=	$row['rateGiven'];
			$memberRateMsg	=	stripslashes($row['memberRateMsg']);
			$qaDoneBy		=	$row['qaDoneBy'];

			$customerName	=   $firstName." ".$lastName;

			$acceptedByName	=	"";
			$qaByName		=	"";

			$statusText			=   "<font color='red'>New Order</font>";
			if($result11		=	$orderObj->isOrderChecked($orderId))
			{
				$statusText		=   "<font color='green'>New Order</font>";
			}
			if($status			==	1)
			{
				$statusText		=   "<font color='#4F0000'>Accepted</font>";
			}
			$hasReplied		    =	@mysql_result(dbQuery("SELECT hasRepliedFileUploaded FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId AND hasRepliedFileUploaded=1"),0);
			if(!empty($hasReplied))
			{
				$statusText		=	"<font color='blue'>QA Pending</font>";
			}
			if($status		==	2)
			{
				$statusText		=   "<font color='green'>Completed</font>";
				if(!empty($qaDoneBy))
				{
					$qaByName	=	$employeeObj->getEmployeeFirstName($qaDoneBy);
				}
			}
			if(!empty($acceptedBy))
			{
				$acceptedByName		=   $employeeObj->getEmployeeFirstName($acceptedBy);
			}

			if($result2			=  $orderObj->getOrderQaRate($orderId))
			{
				$row2			=  mysql_fetch_assoc($result2);
				$rateByQa		=	$row2['rateByQa'];
				$qaRateMessage	=	stripslashes($row2['qaRateMessage']);
			}
			else
			{
				$rateByQa		=	"";
				$qaRateMessage	=	"";
			}
			
			$orderText		=	$a_customerOrder[$orderType];
	?>
	<tr>
		<td class='textstyle' valign="top">
			<?php
				echo "$i) <a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?searchBy=2&serachCustomerId=$customerId'' class='link_style16'>$customerName</a>";
			?>
		</td>
		<td class='textstyle' valign="top">
			<?php 
				echo "<a href='".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=$orderId&customerId=$customerId#action' class='link_style12'>$orderAddress</a>";
			?>
		</td>
		<td class='textstyle' valign="top">
			<?php echo $orderText;?>
		</td>
		<td class='textstyle' valign="top">
			<?php echo showDate($orderAddedOn);?>
		</td>
		<td class='textstyle' valign="top">
			<?php echo $acceptedByName;?>
		</td>
		<td class='textstyle' valign="top">
			<?php echo $qaByName;?>
		</td>
		<td class='textstyle' valign="top">
			<?php echo $statusText;?>
		</td>
		<td class='textstyle' valign="top">
			<?php
				if(!empty($rateByQa))
				{
					for($m=1;$m<=$rateByQa;$m++)
					{
				?>
					<img src="<?php echo SITE_URL;?>/images/star.gif"  width="12" height="12">
				<?php
					}
				}
			?>
		</td>
		<td class='textstyle' valign="top">
			<?php echo $qaRateMessage;?>
		</td>
		<td class='textstyle' valign="top">
			<?php
				if(!empty($rateGiven))
				{
					for($k=1;$k<=$rateGiven;$k++)
					{
				?>
					<img src="<?php echo SITE_URL;?>/images/star.gif"  width="12" height="12">
				<?php
					}
				}
			?>
		</td>
		<td class='textstyle' valign="top">
			<?php echo $memberRateMsg;?>
		</td>
	</tr>
	<tr>
		<td colspan="13">
			<hr size="1" width="100%" color="#bebebe">
		</td>
	</tr>
	<?php
		}
		echo "<tr><td align='right' colspan='15'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr>";	
	?>
</table>
<?php
	}
	else
	{
		echo "<table height='200' width='80%'><tr><td align='center' class='error'><b>No Record Found !!</b></td></tr></table>";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>