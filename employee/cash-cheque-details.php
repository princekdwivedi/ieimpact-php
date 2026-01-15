<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	."/includes/common-array.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");
	$employeeObj				=	new employee();
	$validator					=	new validate();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$cashId				=	0;
	$type				=	"1";
	$transactiontype	=	"1";
	$transactionDate	=	date("d-m-Y");
	$t_transactionDate	=	"";
	$amount				=	"";
	$transactionDetails	=	"";
	$voucherNo			=	"";
	$paidReceivedFrom	=	"";
	$checked			=	"checked";
	$checked1			=	"";
	$checked2			=	"checked";
	$checked3			=	"";
	$text				=	"ADD CASH/CHEQUE DETAILS";
	$errorMsg			=	"";
	$displayText		=	"Voucher No";
	
	if(isset($_GET['ID']))
	{
		$cashId		=	$_GET['ID'];

		$query		=	"SELECT * FROM cash_cheque_details WHERE cashId=$cashId";
		$result		=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			$text				=	"EDIT CASH/CHEQUE DETAILS";
			$row				=	mysql_fetch_assoc($result);
			$cashId			    =	$row['cashId'];
			$transactionsType	=	$row['transactionsType'];
			$amount				=	$row['amount'];
			$t_transactionDate	=	$row['transactionDate'];
			$paidReceivedFrom	=	stripslashes($row['paidReceivedFrom']);
			$voucherNo			=	stripslashes($row['voucherNo']);
			$transactionDetails	=	stripslashes($row['transactionDetails']);
			$transactionDetails	=	nl2br($transactionDetails);
			$type				=	$row['type'];

			list($year,$month,$day)	=	explode("-",$t_transactionDate);
			$transactionDate		=	$day."-".$month."-".$year;
			if($type		==	2)
			{
				$checked	=	"";
				$checked1	=	"checked";
			}
			if($transactionsType == 2)
			{
				$checked2			=	"";
				$checked3			=	"checked";
				$displayText		=	"Cheque No";
			}
		}
	}

	$form						=	SITE_ROOT_EMPLOYEES."/forms/cash-cheque-details.php";
?>
<table width="98%" border="0" align="center" cellpadding="4" cellspacing="2" valign="top">
	<tr>
		<td colspan="3" class="title"><?php echo $text;?></td>
	</tr>
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
</table>
<?php
	if(isset($_GET['success']))
	{
		echo "<br><center><font class='smalltext2'><b>Successfully ".$text."</b></font></center><br>";
	}
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		if($transactionsType == 2)
		{
			$checked2			=	"";
			$checked3			=	"checked";
			$displayText		=	"Cheque No";
		}
		$amount				=	trim($amount);
		$paidReceivedFrom	=	trim($paidReceivedFrom);
		$voucherNo			=	trim($voucherNo);
		$paidReceivedFrom	=	makeDBSafe($paidReceivedFrom);
		$voucherNo			=	makeDBSafe($voucherNo);
		$transactionDetails	=	makeDBSafe($transactionDetails);
		$voucherNo			=	makeDBSafe($voucherNo);
		if(empty($amount))
		{
			$errorMsg	.=	"Please Enter Amount !!<br><br>";
		}
		if(empty($paidReceivedFrom))
		{
			$errorMsg	.=	"Please Enter Person/Firm/Organization Name !!<br><br>";
		}
		if(empty($transactionDetails))
		{
			$errorMsg	.=	"Please Enter Transaction Details !!<br><br>";
		}
		if(empty($errorMsg))
		{
			list($day,$month,$year)	=	explode("-",$transactionDate);
			$t_transactionDate		=	$year."-".$month."-".$day;

			$optionQuery	=	" SET transactionsType=$transactionsType,amount='$amount',type=$type,transactionDate='$t_transactionDate',paidReceivedFrom='$paidReceivedFrom',transactionDetails='$transactionDetails',voucherNo='$voucherNo'";
			if(empty($cashId))
			{
				$query	=	"INSERT INTO cash_cheque_details".$optionQuery.",addedBy=$s_employeeId,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'";
				dbQuery($query);
			}
			else
			{
				$query	=	"UPDATE cash_cheque_details".$optionQuery.",updatedBy=$s_employeeId,updatedOn='".CURRENT_DATE_INDIA."',updatedTime='".CURRENT_TIME_INDIA."' WHERE cashId=$cashId";
				dbQuery($query);
			}

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/cash-cheque-details.php?success=1");
			exit();
		}
	}
	include($form);
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>