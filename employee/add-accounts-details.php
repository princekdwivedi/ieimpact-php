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
	$currentBalance	=	$employeeObj->getCurrentAccountBalance();
	$accountId		=	0;
	$type			=	"1";
	$amount			=	"";
	$month			=	date("m");
	$year			=	date("Y");
	$accountsFor	=	date("d-m-Y");
	$t_accountsFor	=	"";
	$remarks		=	"";
	$voucherNo		=	"";
	$checked		=	"checked";
	$checked1		=	"";
	$text			=	"ADD DEBIT-CREDIT COMAPNY DETAILS";
	$errorMsg		=	"";
	if(isset($_GET['ID']))
	{
		$accountId	=	$_GET['ID'];

		$query		=	"SELECT * FROM company_daily_accounts WHERE accountId=$accountId";
		$result		=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			$text			=	"EDIT DEBIT-CREDIT COMAPNY DETAILS";
			$row			=	mysql_fetch_assoc($result);
			$amount			=	$row['amount'];
			$t_accountsFor	=	$row['accountsFor'];
			$remarks		=	stripslashes($row['remarks']);
			$type			=	$row['type'];
			$voucherNo		=	stripslashes($row['voucherNo']);
			if($type		==	2)
			{
				$checked	=	"";
				$checked1	=	"checked";
			}
			list($year,$month,$day)	=	explode("-",$t_accountsFor);
			$accountsFor			=	$day."-".$month."-".$year;
		}
	}

	$form						=	SITE_ROOT_EMPLOYEES."/forms/add-accounts-details.php";
?>
<table width="98%" border="0" align="center" cellpadding="4" cellspacing="2" valign="top">
	<tr>
		<td colspan="3" class="title"><?php echo $text;?></td>
	</tr>
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
	<tr>
		<td width="20%" class="title"><b>Current Balance</b></td>
		<td width="2%" class="title"><b>:</b></td>
		<td class="title">
			<?php 
				echo " Rs. ".$currentBalance.".00";
			?>
		</td>
	</tr>
</table>
<?php
	if(isset($_GET['success']))
	{
		echo "<br><center><font class='smalltext2'><b>Success Fully ".$text."</b></font></center><br>";
	}
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		$amount		=	trim($amount);
		$remarks	=	makeDBSafe($remarks);
		$voucherNo	=	makeDBSafe($voucherNo);
		if(empty($amount))
		{
			$errorMsg	=	"Please Enter Amount !!";
		}
		if(empty($errorMsg))
		{
			list($day,$month,$year)	=	explode("-",$accountsFor);
			$t_accountsFor			=	$year."-".$month."-".$day;

			$optionQuery	=	" SET month=$month,year=$year,amount='$amount',type=$type,accountsFor='$t_accountsFor',remarks='$remarks',voucherNo='$voucherNo'";
			if(empty($accountId))
			{
				$query	=	"INSERT INTO company_daily_accounts".$optionQuery.",addedBy=$s_employeeId,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'";
				dbQuery($query);
				$accountId	=	mysql_insert_id();
			}
			else
			{
				$query	=	"UPDATE company_daily_accounts".$optionQuery.",updatesBy=$s_employeeId,updatedOn='".CURRENT_DATE_INDIA."',updatedTime='".CURRENT_TIME_INDIA."' WHERE accountId=$accountId";
				dbQuery($query);
			}

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/add-accounts-details.php?success=1");
			exit();
		}
	}
	include($form);
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>