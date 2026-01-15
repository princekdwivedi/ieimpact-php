<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	$showForm 					=	false;
	$completeName               =   "";

	$memberId 					=	0;
	if(isset($_GET['memberId'])){
		$memberId 				=	(int)$_GET['memberId'];
		if(!empty($memberId)){
			$query 				=	"SELECT firstName,lastName,totalOrdersPlaced FROM members WHERE memberId=$memberId AND isActiveCustomer=1 AND isJunkMember=0";
			$result 			=	dbQuery($query);
			if(mysqli_num_rows($result)){
				$showForm 		=	true;
				$row 			=	mysqli_fetch_assoc($result);
				$firstName		=	stripslashes($row['firstName']);
				$lastName		=	stripslashes($row['lastName']);
				$completeName	=	$firstName." ".substr($lastName, 0, 1);
			}
		}
	}

?>
<html>
<head>
<TITLE>View Total Orders Done By Employees For - <?php echo $completeName;?></TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
<link rel="shortcut icon" href="/new-favicon.ico" type="image/x-icon" />
</head>
<body>
<center>
<?php
	
	if($showForm)
	{
?>
<table width="100%" align="center" border="0" cellspacing="3" cellspacing="3">
	<tr>
		<td colspan="5" class="textstyle1"><b>View Total Orders Done By Employees For - <?php echo $completeName;?></b></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<?php
		$query 		=	"SELECT a.fullName,b.memberId,a.employeeId,b.totalAccepted,b.ratingWithThreeOrMore FROM employee_details a LEFT JOIN customers_total_orders_done_by b ON (a.employeeId=b.employeeId AND memberId=$memberId) WHERE a.isActive = 1 AND a.hasPdfAccess=1 ORDER BY b.totalAccepted DESC,a.fullName";
		$result								=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$a_displayEmployees 			=	array();
			while($row						=  mysqli_fetch_assoc($result))
			{
				$employeeId			        =  $row['employeeId'];
				$fullName				    =  stripslashes($row['fullName']);
				$totalCustCompletedOrders	=  $row['totalAccepted'];
				$totalAverageRating			=  $row['ratingWithThreeOrMore'];

				if(!empty($totalCustCompletedOrders)){
					$a_displayEmployees[$employeeId] = $fullName."<=>".$totalCustCompletedOrders."<=>".$totalAverageRating;
				}
			}

	?>
	<tr>
		<td width="30%" class="smalltext2"><b>Employee Name</b></td>
		<td width="30%" class="smalltext2"><b>Total Done For Customer</b></td>
		<td class="smalltext2"><b>Rated good or more by Customer</b></td>
	</tr>
	<tr>
		<td colspan="10">
			<hr size="1" width="100%" color="#bebebe">
		</td>
	</tr>
	<?php
			foreach($a_displayEmployees as $k=>$v)
			{
				list($fullName,$totalCustCompletedOrders,$totalAverageRating) = explode("<=>",$v);
				
	?>
		<tr>
			<td class="smalltext2">
				<?php 
					echo $fullName;
				?>
			</td>
			<td class="smalltext2">
				<?php
					echo $totalCustCompletedOrders;
				?>
			</td>
			<td class="smalltext2">
				<?php
					echo $totalAverageRating;
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
		}
		else{
	?>
	<tr>
		<td colspan="5" class="error"><b>No Order Is Completed For This Customer.</b></td>
	</tr>
	<?php
		}

	?>
</table>
<?php
		
	}
	else
	{
		echo "<table width='90%' align='center' border='0' height='100'><tr><td class='error' style='text-align:center'><b>You are not authorized to view this page.</b></td></tr></table>";
	}
?>

	<br><br>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
</center>
</body>
</html>

	