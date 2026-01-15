<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/common.php");
	$employeeObj				= new employee();
	$orderObj					= new orders();

	$showForm					= false;
	$orderId					= 0;
	$customerId					= 0;
	$status						= 0;
	$employeeId					= 0;
	$errorMsg					= "";
	$errorMessageForm			= "You are not authorized to view this page !!";
	$auditId					=  0;	
	$auditAddedByText			=	"";

	if(isset($_GET['orderId']) && isset($_GET['customerId']))
	{
		$orderId					=	$_GET['orderId'];
		$customerId					=	$_GET['customerId'];
		if($result					=	$orderObj->getOrderDetails($orderId,$customerId))
		{
			$showForm				=	true;

			$row					=	mysqli_fetch_assoc($result);
			$firstName			    =	stripslashes($row['firstName']);
			$lastName   			=	stripslashes($row['lastName']);
			$customerName			=	stripslashes($row['completeName']);
			$orderAddress			=   stripslashes($row['orderAddress']);
			$status         	    =	$row['status'];
			$orderType				=	$row['orderType'];
			$orderAddedOn			=	$row['orderAddedOn'];
			$orderAddedTime		    =	$row['orderAddedTime'];
			$hasRepliedUploaded 	=	$row['hasRepliedUploaded'];
			$customersOwnOrderText  =	stripslashes($row['customersOwnOrderText']);
			$orderAddedDateTime     =   showDateTimeFormat($orderAddedOn,$orderAddedTime);

			$customerName			=   $firstName." ".substr($lastName, 0, 1);
						
			$orderText				=	$a_customerOrder[$orderType];
			if($orderType			==	6)
			{
				$orderText			=	$orderText."(".$customersOwnOrderText.")";
			}

			$statusText				=   "<font color='red'>New Order</font>";
			
			if($status				==	1)
			{
				$statusText			=   "<font color='#4F0000'>Accepted</font>";
				if(!empty($hasRepliedUploaded))
				{
					$statusText		=	"<font color='blue'>QA Pending</font>";
				}				
			}

			if($status				==	2)
			{
				$statusText			=   "<font color='green'>Completed</font>";//.$postAuditText;
				
			}
			elseif($status			==	3)
			{	
				$statusText			=   "<font color='#333333'>Nd Atten.</font>";
			}
			elseif($status			==	5)
			{
				$statusText			=   "<font color='green'>Nd Feedbk.</font>";///.$postAuditText;
			}

			elseif($status			==	4)
			{
				$statusText			=   "<font color='#ff0000'>Cancelled</font>";
			}
			elseif($status			==	6)
			{
				$statusText			=   "<font color='green'>Fd Rcvd</font>";//.$postAuditText;
			}
			
		}
	}

?>
<html>
<head>
<TITLE>Marked Post Audit Errors</TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
<body>
<center>
<?php
	if($showForm)
	{
?>
<table width="100%" align="center" border="0" cellspacing="3" cellspacing="3">
	<tr>
		<td colspan="3" class="textstyle1"><b>View Order History</b></td>
	</tr>
	<tr>
		<td width="10%" class="smalltext2">
			<b>Customer</b>
		</td>
		<td width="2%" class="smalltext2">
			:
		</td>
		<td class="smalltext2" width="30%">
			<?php echo $customerName;?>
		</td>
		<td class="smalltext2" width="10%">
			<b>Order</b>
		</td>
		<td class="smalltext2" width="2%">
			:
		</td>
		<td class="smalltext2">
			<?php echo $orderAddress;?>
		</td>
	</tr>
	<tr>
		<td class="smalltext2">
			<b>Type</b>
		</td>
		<td class="smalltext2">
			:
		</td>
		<td class="smalltext2">
			<?php echo $orderText;?>
		</td>
		<td class="smalltext2">
			<b>Status</b>
		</td>
		<td class="smalltext2">
			:
		</td>
		<td class="smalltext2">
			<?php echo $statusText;?>
		</td>
	</tr>
	<tr>
		<td class="smalltext2">
			<b>Added On</b>
		</td>
		<td class="smalltext2">
			:
		</td>
		<td class="smalltext2" coslpan="3">
			<?php echo $orderAddedDateTime;?>
		</td>		
	</tr>
</table>
<?php
		$query = "SELECT order_history.*,fullName FROM order_history LEFT JOIN employee_details ON order_history.employeeId=employee_details.employeeId WHERE orderId=$orderId ORDER BY historyId DESC";
		$result	=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
?>		
	        <br />
			<table width="100%" align="center" border="0" cellspacing="3" cellspacing="3">
				<tr>
					<td width="3%" class="smalltext2">&nbsp;</td>
					<td width="45%" class="smalltext2"><b>Action Performed</b></td>
					<td width="20%" class="smalltext2"><b>Employee</b></td>
					<td class="smalltext2"><b>Date & Time</b></td>
				</tr>
				<?php
					$count 	=	0;
					while($row 	=	mysqli_fetch_assoc($result)){
						$count++;

						$actionPerformed 	=	stripslashes($row['actionPerformed']);
						$employeeName    	=	$row['fullName'];
						if(!empty($employeeName)){
							$employeeName   =	stripslashes($employeeName);
						}
						$operationDate    	=	$row['operationDate'];
						$operationTime    	=	$row['operationTime'];
					?>
					<tr>
						<td class="smalltext2" valign="top"><?php echo $count;?>)</td>
						<td class="smalltext2" valign="top"><?php echo $actionPerformed;?></td>
						<td class="smalltext2" valign="top"><?php echo $employeeName;?></td>
						<td class="smalltext2" valign="top"><?php echo showDateTimeFormat($operationDate,$operationTime);?></td>
					</tr>
					<?php
					}
				?>
			</table>
<?php
		}
		else{
			echo "<table width='90%' align='center' border='1' height='100'><tr><td align='center' align='center' class='error'><b>No History Available</b></td></tr></table>";
		}

	}
	else
	{
		echo "<table width='90%' align='center' border='1' height='100'><tr><td align='center' align='center' class='error'><b>$errorMessageForm</b></td></tr></table>";
	}
?>
<br><br>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
</center>
</body>
</html>

