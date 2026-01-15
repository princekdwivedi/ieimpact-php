<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/common-array.php");
	$employeeObj				= new employee();
	$showForm					= false;
	$orderId					= 0;
	$errorMessageForm			= "You are not authorized to view this page !!";
	$customerName				=	"";
	$showExplain				=	0;

	if(isset($_GET['showExplain']))
	{
		$showExplain			=	$_GET['showExplain'];
	}

	if(isset($_GET['orderId']))
	{
		$orderId		=	$_GET['orderId'];
		$query			=	"SELECT members_orders.*,firstName,lastName FROM members_orders INNER JOIN members ON members_orders.memberId=members.memberId WHERE orderId=$orderId AND members_orders.status=2 AND rateGiven <> 0 AND memberRateMsg <> ''";
		$result			=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$showForm		=	true;
			$row			=	mysqli_fetch_assoc($result);
			$firstName		=	stripslashes($row['firstName']);	
			$lastName		=	stripslashes($row['lastName']);
			$address		=	stripslashes($row['orderAddress']);
			$orderOn		=	showDate($row['orderAddedOn']);
			$rateGiven		=	$row['rateGiven'];
			$memberRateMsg	=	stripslashes($row['memberRateMsg']);
			$rateGivenOn	=	showDate($row['rateGivenOn']);

			$customerName	=	$firstName." ".$lastName;
			
		}
	}
?>
<html>
<head>
<TITLE>View Rate Messages From <?php echo $customerName;?></TITLE>
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
		<td colspan="3" class="smalltext2">
			<b>View Order Rating & Message From <?php echo $customerName;?></b>
		</td>
	</tr>
	<tr>
		<td width="25%" class="smalltext2">Order No</td>
		<td width="2%" class="smalltext2">:</td>
		<td class="error">
			<?php echo $address;?>
		</td>
	</tr>
	<tr>
		<td colspan="3" height="5"></td>
	</tr>
	<tr>
		<td class="smalltext2">Rate By Customer</td>
		<td class="smalltext2">:</td>
		<td class="smalltext1">
			<?php 
				for($m=1;$m<=$rateGiven;$m++)
				{
			?>
				<img src="<?php echo SITE_URL;?>/images/star.gif"  width="12" height="12">&nbsp;
			<?php
				}
				echo $a_ratingByQa[$rateGiven];
			?>
		</td>
	</tr>
	<tr>
		<td colspan="3" height="5"></td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top">Rate Given On</td>
		<td class="smalltext2" valign="top">:</td>
		<td class="smalltext6" valign="top">
			<?php 
				echo $rateGivenOn;
			?>
		</td>
	</tr>
	<tr>
		<td colspan="3" height="5"></td>
	</tr>
	<tr>
		<td class="smalltext2" valign="top">Rate Message</td>
		<td class="smalltext2" valign="top">:</td>
		<td class="smalltext6" valign="top">
			<b>
			<?php 
				echo $memberRateMsg;
			?>
			</b>
		</td>
	</tr>
	<?php
		if($showExplain	==	1)
		{
			$query		=	"SELECT * FROM reply_on_orders_rates WHERE orderId=$orderId";	
			$result		=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
					
				$row			=	mysqli_fetch_assoc($result);
				$explanation	=	stripslashes($row['comment']);
				$explanationBy	=	$row['addedby'];
				$explanationOn	=	showDate($row['addedOn']);
				$explanationBy	=	$employeeObj->getEmployeeFirstName($explanationBy);
	?>
	<tr>
		<td colspan="3" height="5"></td>
	</tr>
	</tr>
	<tr>
		<td class="smalltext2" valign="top">Explanation</td>
		<td class="smalltext2" valign="top">:</td>
		<td class="smalltext6" valign="top">
			<b>
			<?php 
				echo nl2br($explanation);
			?>
			</b>
		</td>
	</tr>
	<tr>
		<td colspan="3" height="5"></td>
	</tr>
	</tr>
	<tr>
		<td class="smalltext2" valign="top">Explanation By</td>
		<td class="smalltext2" valign="top">:</td>
		<td class="smalltext6" valign="top">
			<?php 
				echo $explanationBy." on ".$explanationOn;
			?>
		</td>
	</tr>
	<?php
				
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

