<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=  new employee();
	$orderObj					=  new orders();

	//pr($_REQUEST);
	
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$customerId					=	0;
	$text						=	"";
	$andClause					=	"";
	$andClause1					=	"";
	$text						=	"";
	$customerName				=	"";

	if(isset($_GET['customerId']))
	{
		$customerId				=	$_GET['customerId'];
		if(!empty($customerId))
		{
			$customerName		=	$employeeObj->getSingleQueryResult("SELECT completeName FROM members WHERE memberId=$customerId","completeName");
		}
	}

	if(isset($_GET['searchBy']))
	{
		$searchBy				=	$_GET['searchBy'];

		if($searchBy			==	1)
		{
			if(isset($_GET['searchDate']))
			{
				$searchDate		=	$_GET['searchDate'];

				$t_searchDate	=	$searchDate;

				$andClause		=	" AND orderAddedOn='$t_searchDate'";
				$andClause1		=	" AND orderCompletedOn='$t_searchDate'";
				$text			=	" ON ".showDate($t_searchDate);
			}
		}
		else
		{
			if(isset($_GET['searchMonth']))
			{
				$searchMonth	=	$_GET['searchMonth'];
			}
			if(isset($_GET['searchYear']))
			{
				$searchYear		=	$_GET['searchYear'];
			}
			if(!empty($searchYear) && !empty($searchYear))
			{
				$andClause			=	" AND MONTH(orderAddedOn)=$searchMonth AND YEAR(orderAddedOn)=$searchYear";
				$andClause1			=	" AND MONTH(orderCompletedOn)=$searchMonth AND YEAR(orderCompletedOn)=$searchYear";
				$text				=	" ON ".$a_month[$searchMonth].",".$searchYear;
			}
		}
	}

	if(!empty($customerId))
	{
?>
<table width='100%' align='center' cellpadding='2' cellspacing='2' border='0' style="border:3px solid #4d4d4d;">
	<tr>
		<td align="right" class="smalltext2">
			CLOSE &nbsp;<img src="<?php echo SITE_URL;?>/images/close.gif" title="Close" border="0" onclick="javascript:removeEmployees(<?php echo $customerId;?>,2)" style="cursor:pointer;">&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan='3'>
			<hr size='1' width='100%' color='#4d4d4d'>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<table width='100%' align='center' cellpadding='2' cellspacing='2' border='0' style="border:1px solid #CCCCCC;">
				<tr>
					<td class="textstyle" colspan="3"><b>Processing Employee Details</b></td>
				</tr>
				<!--<tr>
					<td width="70%" class="textstyle">Employee</td>
					<td class="textstyle">Total</td>
				</tr>
				<tr>
					<td colspan="3">
						<hr size="1" width="100%" color="#333333;">
					</td>
				</tr>-->
				<?php
					$query			=	"SELECT acceptedBy,COUNT(*) AS TotalProcessedOrders FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND memberId=$customerId AND status IN (1,2,5,6)".$andClause." GROUP BY acceptedBy ORDER BY TotalProcessedOrders DESC";
					$result			=	dbQuery($query);
					if(mysqli_num_rows($result))
					{
						while($row	=	mysqli_fetch_assoc($result))
						{
							$acceptedBy				=	$row['acceptedBy'];
							$totalProcessedOrders	=	$row['TotalProcessedOrders'];

							$acceptedByName			=	$employeeObj->getEmployeeFirstName($acceptedBy);

					?>
					<tr>
						<td class="textstyle"><?php echo $acceptedByName;?></td>
						<td class="textstyle"><?php echo $totalProcessedOrders;?></td>
					</tr>
					<tr>
						<td colspan="3" height="5"></td>
					</tr>
					<?php
						}
					}
				?>
			</table>
		</td>
		<td valign="top"></td>
		<td valign="top">
			<table width='100%' align='center' cellpadding='2' cellspacing='2' border='0' style="border:1px solid #CCCCCC;">
				<tr>
					<td class="textstyle" colspan="3"><b>QA Details</b></td>
				</tr>
				<!--<tr>
					<td width="70%" class="textstyle">Employee</td>
					<td class="textstyle">Total</td>
				</tr>
				<tr>
					<td colspan="3">
						<hr size="1" width="100%" color="#333333;">
					</td>
				</tr>-->
				<?php
					$query			=	"SELECT qaDoneBy,COUNT(members_orders.orderId) AS TotalCompletedOrders FROM members_orders_reply INNER JOIN members_orders ON members_orders_reply.orderId=members_orders.orderId WHERE members_orders.orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND members_orders.memberId=$customerId AND hasQaDone=1".$andClause." GROUP BY qaDoneBy ORDER BY TotalCompletedOrders DESC";
					$result			=	dbQuery($query);
					if(mysqli_num_rows($result))
					{
						while($row	=	mysqli_fetch_assoc($result))
						{
							$qaDoneBy				=	$row['qaDoneBy'];
							$totalCompletedOrders	=	$row['TotalCompletedOrders'];

							$qaDoneByName			=	$employeeObj->getEmployeeFirstName($qaDoneBy);

					?>
					<tr>
						<td class="textstyle"><?php echo $qaDoneByName;?></td>
						<td class="textstyle"><?php echo $totalCompletedOrders;?></td>
					</tr>
					<tr>
						<td colspan="3" height="5"></td>
					</tr>
					<?php
						}
					}
				?>
			</table>
		</td>
	</tr>
</table>
<?php
	}
?>
