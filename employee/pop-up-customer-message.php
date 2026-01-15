<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	$employeeObj				= new employee();
	$memberObj					= new members();
	$orderObj					= new orders();
	$showForm					= false;
	$orderId					= 0;
	$customerId					= 0;
	$errorMessageForm			= "You are not authorized to view this page !!";
	$customerName				=	"";

	if(isset($_GET['orderId']) && isset($_GET['customerId']))
	{
		$orderId		=	$_GET['orderId'];
		$customerId		=	$_GET['customerId'];
		if($result		=	$orderObj->getOrderMessages($orderId,$customerId))
		{
			$showForm	=	true;
			if($result1	=	$orderObj->getOrderDetails($orderId,$customerId))
			{
				$row		=	mysql_fetch_assoc($result1);
				$firstName	=	stripslashes($row['firstName']);	
				$lastName	=	stripslashes($row['lastName']);
				$address	=	stripslashes($row['orderAddress']);
				$orderOn	=	showDate($row['orderAddedOn']);

				$customerName=	$firstName." ".$lastName;
			}
		}
	}
?>
<html>
<head>
<TITLE>View Messages From <?php echo $customerName;?></TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
<body>
<center>
<?php
	if($showForm)
	{
?>
<table width='98%' align='center' border='0' cellpadding="2" cellspacing="2">
	<tr>
		<td colspan="3" class="heading3">
			View Order Message From <?php echo $customerName;?>
		</td>
	</tr>
	<tr>
		<td width="20%" class="smalltext2">Order No</td>
		<td width="2%" class="smalltext2">:</td>
		<td class="error">
			<?php echo $address;?>
		</td>
	</tr>
	<?php
		if($result			=	$orderObj->getOrderMessages($orderId,$customerId))
		{
			while($row			=	mysql_fetch_assoc($result))
			{
				$t_messageId	=	$row['messageId'];
				$t_message		=	stripslashes($row['message']);
				$addedOn		=	showDate($row['addedOn']);
				$addedTime		=	$row['addedTime'];
				$messageBy		=	$row['messageBy'];
				$hasMessageFiles=	$row['hasMessageFiles'];
				$fileName		=	$row['fileName'];
				$fileExtension	=	$row['fileExtension'];
				$fileSize		=	$row['fileSize'];

				if($messageBy   ==  CUSTOMERS)
				{
					echo "<tr><td class='smalltext2' colspan='3'><b>Message on $addedOn</b></td></tr>";
					echo "<tr><td class='smalltext2' colspan='3'>".nl2br($t_message)."</td></tr>";
				}
			}
		}
	?>
</table>
<?php
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

