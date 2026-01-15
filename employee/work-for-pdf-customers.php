<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");	
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/classes/common.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$commonObj		=	new common();
	$employeeObj	=	new employee();

	$showForm		=	false;
	$employeeId		=	0;
	$employeeName	=	"";
	$month			=	"";
	$year			=	"";
	$text			=	"";

	$andClause		=	"";
	if(isset($_GET['ID']) && isset($_GET['month']) && isset($_GET['year']))
	{
		$employeeId		=	 $_GET['ID'];
		$month			=	 $_GET['month'];
		$year			=	 $_GET['year'];

		$monthText		=	$a_month[$month];
		$text		    =	$monthText.",".$year;
		
		$employeeName=  $employeeObj->getEmployeeName($employeeId);
		if(!empty($employeeName))
		{
			$showForm	=	true;
		}
	}
?>
<html>
<title>
	View Completed Replied Files By <?php echo $employeeName;?> On <?php echo $text;?>
</title>
<link href="<?php echo SITE_URL;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
<body>
<center>
	<table width="98%" align="center" border="0" cellpadding="2" cellspacing="2">
		<tr>
			<td colspan="4" class="smalltext2"><b>View Completed Replied Files By Customer Wise For - <?php echo $employeeName;?> On <?php echo $text;?></b></td>
		</tr>
		<tr>
			<td width="10%" class="smalltext2">Sr No.</td>
			<td width="50%" class="smalltext2">Customer Name</td>
			<td class="smalltext2" align="center">Total Replied</td>
		</tr>
		<tr>
			<td colspan="4">
				<hr size="1" width="100%" color="#bebebe">
			</td>
		</tr>
	<?php
		$query	=	"SELECT members_orders.memberId,firstName,lastName FROM members_orders INNER JOIN members ON members_orders.memberId=members.memberId WHERE acceptedBy=$employeeId AND status=2 AND MONTH(orderAddedOn)=$month AND YEAR(orderAddedOn)=$year GROUP BY members_orders.memberId";
		$result		=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			$i	=	0;
			while($row			=	mysql_fetch_assoc($result))
			{
				$i++;
				$memberId		=	$row['memberId'];
				$firstName		=	stripslashes($row['firstName']);
				$lastName		=	stripslashes($row['lastName']);
				$customerName	=	$firstName." ".$lastName;

				$totalReplied	=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders WHERE memberId=$memberId AND acceptedBy=$employeeId AND status=2 AND MONTH(orderAddedOn)=$month AND YEAR(orderAddedOn)=$year"),0);
				if(empty($totalReplied))
				{
					$totalReplied=	0;
				}
		?>
		<tr>
			<td class="smalltext2"><b><?php echo $i;?>)</b></td>
			<td class="smalltext2"><b><?php echo ucwords($customerName);?></b></td>
			<td align="center" class="text2"><b><?php echo $totalReplied;?></b></td>
		</tr>
		<tr>
			<td colspan="4">
				<hr size="1" width="100%" color="#bebebe">
			</td>
		</tr>
		<?php
			}
		}
		else
		{
	?>
	<tr>
		<td colspan="4" align="center" class="error">
			<b>No Completed Replied Order Found !!</b>
		</td>
	</tr>
	<?php
		}
	?>
	</table>
<br><br>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
</center>
</body>
</html>
