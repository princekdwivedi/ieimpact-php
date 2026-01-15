<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");


	ob_clean();
	header("Location: ".SITE_URL_EMPLOYEES);
	exit();

	include(SITE_ROOT_EMPLOYEES .   "/includes/new-top.php");
	include(SITE_ROOT_EMPLOYEES .   "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES .   "/classes/employee.php");
	include(SITE_ROOT			.   "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	.   "/includes/common-array.php");
	include(SITE_ROOT			.   "/classes/new-pagingclass.php");
	$orderRecordsTill			=	getPreviousGivenDate($nowDateIndia,30);
	$employeeObj				=   new employee();
	$pagingObj					=   new Paging();
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	if(isset($_REQUEST['recNo']))
	{
		$recNo		    =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo	=	0;
	}
	$whereClause		=	"WHERE isVirtualDeleted=0 AND isTestAccount=0 AND orderAddedOn >= '$orderRecordsTill'";
	$andClause			=	"";
	$orderBy			=	"orderAddedOn DESC,orderAddedTime DESC";
	$queryString		=	"";
	$searchOrder		=	"";

	$a_allProcessEmployee	=	array();
	if($result				=	$employeeObj->getAllPdfEmployees())
	{
		while($row			=	mysql_fetch_assoc($result))
		{
			$employeeId		=	$row['employeeId'];
			$completeName	=	stripslashes($row['firstName'])." ".stripslashes($row['lastName']);
			$a_allProcessEmployee[$employeeId]	=	$completeName;
		}
	}

	if(isset($_GET['searchOrder']))
	{
		$searchOrder	=	$_GET['searchOrder'];
		if(!empty($searchOrder))
		{
			$andClause	=	" AND orderAddress LIKE '%$searchOrder%'";
			$queryString=	"&searchOrder=$searchOrder";
		}
	}

	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		$a_changeAcceptedBy	=	$_POST['changeAcceptedBy'];
		$a_changeQaBy		=	$_POST['changeQaBy'];
		if(isset($_POST['checkedToChanged']))
		{
			$a_checkedToChanged	=	$_POST['checkedToChanged'];
		}
		else
		{
			$a_checkedToChanged	=	"";
		}
		if(!empty($a_checkedToChanged))
		{
			foreach($a_checkedToChanged as $orderId=>$value)
			{
				$acceptedBy		=	$a_changeAcceptedBy[$orderId];
				$qaBy			=	$a_changeQaBy[$orderId];

				$employeeObj->updateOrderChangeEmployees($orderId,$acceptedBy,$qaBy);

				/*dbQuery("UPDATE members_orders SET employeeId=$acceptedBy,acceptedBy=$acceptedBy WHERE orderId=$orderId");
				//if(!empty($qaBy))
				//{
					dbQuery("UPDATE members_orders_reply SET qaDoneBy=$qaBy WHERE orderId=$orderId");
				}*/
			}
		}
		if(!empty($recNo))
		{
			$link		=	"recNo=$recNo";
		}
		else
		{
			$link		=	"";
		}

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/change-order-status.php?".$link.$queryString);
		exit();
	}
?>
<script type="text/javascript">
	function changeStatus(flag)
	{
		mainBox	=   document.getElementById('mainCustomerId'+flag);
		if(mainBox.checked == true)
		{
			document.getElementById('process'+flag).disabled  = false;
			document.getElementById('qa'+flag).disabled = false;
		}
		else
		{
			document.getElementById('process'+flag).disabled  = true;
			document.getElementById('qa'+flag).disabled = true;
		}
		
	}
</script>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
</script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" />

<script type="text/javascript">
$().ready(function() {
	$("#orderAddress").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/employees-pdf-orders.php", {width: 250,selectFirst: false});
});
function search()
{
	form1	=	document.searchForm;
	if(form1.searchOrder.value == "")
	{
		alert("Please enter address !!");
		form1.searchOrder.focus();
		return false;
	}
}
</script>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td colspan="12" class='title'>Change PDF Order's Employees Till : <?php echo showDate($orderRecordsTill);?></td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
</table>
<br>
<form name="searchForm" action=""  method="GET" onsubmit="return search();">
	<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
		<tr>
			<td width="23%" class="smalltext1" valign="top"><b>SEARCH AN ORDER BY ORDER ADDRESS</b></td>
			<td width="1%" class="smalltext1" valign="top"><b>:</b></td>
			<td width="22%" valign="top">
				<input type='text' name="searchOrder" size="38" value="<?php echo $searchOrder;?>" id="orderAddress" onkeypress="return checkedRadio()">
			</td>
			<td valign="top">
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='searchFormSubmit' value='1'>
			</td>
		</tr>
	</table>
</form>
<br>
<form name="changeOrderStatus" action="" method="POST">
<table width="98%" border="0" cellpadding="2" cellspacing="2" align="center">
<tr>
	<td width="4%" class="smalltext2"><b>Sr No</b></td>
	<td width="15%" class="smalltext2"><b>Clicked To Change</b></td>
	<td width="30%" class="smalltext2"><b>Order Address</b></td>
	<td width="8%" class="smalltext2"><b>Added On</b></td>
	<td width="8%" class="smalltext2"><b>Status</b></td>
	<td width="15%" class="smalltext2"><b>Accepted By</b></td>
	<td class="smalltext2"><b>QA Done By</b></td>
</tr>
<tr>
	<td colspan="10">
		<hr size="1" width="100%" color="#bebebe">
	</td>
</tr>
<?php
	$start					  =	0;
	$recsPerPage	          =	50;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"members_orders INNER JOIN members ON members_orders.memberId=members.memberId";
	$pagingObj->selectColumns = "members_orders.*,completeName";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/change-order-status.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();

		$i	=	$recNo;
		while($row	=   mysql_fetch_assoc($recordSet))
		{
			$i++;
			$orderId		=	$row['orderId'];
			$customerId		=	$row['memberId'];
			$orderAddedOn	=	$row['orderAddedOn'];
			$orderAddress	=	stripslashes($row['orderAddress']);
			$status			=	$row['status'];
			$acceptedBy		=	$row['acceptedBy'];
			$completeName	=	stripslashes($row['completeName']);

			$statusText		=   "<font color='red'>New Order</font>";
			$qaDoneBy		=	0;
			if($status		==	1)
			{
				$statusText	=   "<font color='#4F0000'>Accepted</font>";
			}
			
			$hasReplied		 =	@mysql_result(dbQuery("SELECT hasRepliedFileUploaded FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId AND hasRepliedFileUploaded=1"),0);
			if(!empty($hasReplied))
			{
				$statusText	=	"<font color='blue'>QA Pending</font>";
			}
			if($status		==	2)
			{
				$statusText	=   "<font color='green'>Completed</font>";

				$qaDoneBy	=	@mysql_result(dbQuery("SELECT qaDoneBy FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId AND hasQaDone=1"),0);
			}
			if($status		==	3)
			{
				$statusText	=   "<font color='blue'>Need Attention</font>";
			}
			if($status		==	4)
			{
				$statusText	=   "<font color='red'>Cancelled</font>";
			}
			if($status		==	5)
			{
				$statusText	=   "<font color='green'>Need Feedback</font>";
			}
			if($status		==	6)
			{
				$statusText	=   "<font color='green'>Feedback Recv</font>";
			}

		?>
			<tr>
				<td class="textstyle1" valign="top"><?php echo $i;?>)</td>
				<td valign="top">
					<?php
						if(!empty($status) && $status != 1)
						{	
					?>
					<input type="checkbox" name="checkedToChanged[<?php echo $orderId;?>]" value="1" id="mainCustomerId<?php echo $i;?>" onclick="return changeStatus(<?php echo $i;?>)">
					<?php
						}	
					?>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=<?php echo $customerId?>" class="link_style15"><?php echo $completeName;?></a>
				</td>
				<td valign="top">
					<?php
						echo "<a href='".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=$orderId&customerId=$customerId#action' class='link_style15'>$orderAddress</a>";
					?>
				</td>
				<td class="textstyle1" valign="top">
					<?php echo showDate($orderAddedOn);?>
				</td>
				<td class="textstyle1" valign="top"><?php echo $statusText;?></td>
				<td valign="top">
					<?php
						if(!empty($status) && $status != 1)
						{
					?>
					<select name="changeAcceptedBy[<?php echo $orderId;?>]" disabled id="process<?php echo $i;?>">
						<?php
							foreach($a_allProcessEmployee as $processEmpId=>$processBy)
							{
								$select		=	"";
								if($processEmpId	==	$acceptedBy)
								{
									$select	=	"selected";
								}

								echo "<option value='$processEmpId' $select>$processBy</option>";
							}
							
						?>
					</select>
					<?php
						}
						else
						{
							echo "&nbsp;";
						}
					?>
				</td>
				<td>
					<?php
						if($status == 2 && !empty($hasReplied))
						{
					?>
					<select name="changeQaBy[<?php echo $orderId;?>]" disabled id="qa<?php echo $i;?>">
						<?php
							foreach($a_allProcessEmployee as $qaEmpId=>$qaBy)
							{
								$select		=	"";
								if($qaEmpId	==	$qaDoneBy)
								{
									$select	=	"selected";
								}

								echo "<option value='$qaEmpId' $select>$qaBy</option>";
							}
						?>
					</select>
					<?php
						}
						else
						{
							echo "&nbsp;<input type='hidden' name='changeQaBy[$orderId]' value='0'>";
						}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="10">
					<hr size="1" width="100%" color="#bebebe">
				</td>
			</tr>
		<?php
		}
		if(!empty($status))
		{
		?>
		<tr>
			<td colspan="10">
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
		<?php
		}
		echo "<tr><td colspan='9'><table width='100%'><tr><td align='right'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr></table></td></tr>";
		
	}

echo "</table></form>";

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>