<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	."/includes/common-array.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	$employeeObj				=	new employee();
	$pagingObj			        = new Paging();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	else
	{
		if($s_employeeId != 3 && $s_employeeId != 5 && $s_employeeId != 8 && $s_employeeId != 137)
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}
	}
	$currentBalance	=	$employeeObj->getCurrentAccountBalance();
	$accountId		=	0;
	$month			=	"";
	$year			=	"";
	$toMonth		=	"";
	$toYear			=	"";
	$whereClause	=	"";
	$andClause		=	"";
	$orderBy		=	"accountsFor DESC";
	$queryString	=	"";
	$text			=	"View Accounts Statements";
	$printLink		=	"";
	if(isset($_GET['ID']))
	{
		$accountId	=	(int)$_GET['ID'];
		if(isset($_GET['isDelete']) && $_GET['isDelete'] == 1)
		{
			dbQuery("DELETE FROM company_daily_accounts WHERE accountId=$accountId ");

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/view-accounts-statements.php");
			exit();
		}
	}
	$form	=	SITE_ROOT_EMPLOYEES."/forms/view-accounts-details.php";
	if(isset($_GET['month']) && isset($_GET['year']))
	{
		$month					=	$_GET['month'];
		$year					=	$_GET['year'];
		if(!empty($month) && !empty($year))
		{
			$whereClause		=	"WHERE month=$month AND year=$year";
			$andClause			=	" AND month=$month AND year=$year";
			$queryString		=	"&month=$month&year=$year";
			$text				=	"View Accounts Statements For ".$a_month[$month].",".$year;
			$printLink			=	"?month=$month&year=$year";

			if(isset($_GET['toMonth']) && isset($_GET['toYear']))
			{
				$toMonth		=	$_GET['toMonth'];
				$toYear			=	$_GET['toYear'];
				if(!empty($toMonth) && !empty($toYear))
				{
					$whereClause	=	"WHERE month >= $month AND year >= $year AND month <= $toMonth AND year <= $toYear";

					$andClause		=	" AND month >= $month AND year >= $year AND month <= $toMonth AND year <= $toYear";

					$queryString   .=	"&toMonth=$toMonth&toYear=$toYear";
					$text			=	"View Accounts Statements From ".$a_month[$month].",".$year." To ".$a_month[$toMonth].",".$toYear;

					$printLink	   .=	"&toMonth=$toMonth&toYear=$toYear";
				}
			}
		}
	}
	
	$totalDebitAmount	=	@mysql_result(dbQuery("SELECT SUM(amount) FROM company_daily_accounts WHERE type=1".$andClause),0);
	if(empty($totalDebitAmount))
	{
		$totalDebitAmount=	0;
	}

	$totalCredeitAmount	=	@mysql_result(dbQuery("SELECT SUM(amount) FROM company_daily_accounts WHERE type=2".$andClause),0);
	if(empty($totalCredeitAmount))
	{
		$totalCredeitAmount =	0;
	}
?>
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='heading' colspan="3"><?php echo $text;?></td>
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
	include($form);
	if(isset($_REQUEST['recNo']))
	{
		$recNo		    =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo	=	0;
	}
	
	$start					  =	0;
	$recsPerPage	          =	100;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"company_daily_accounts";
	$pagingObj->selectColumns = "*";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/view-accounts-statements.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
?>
<script type="text/javascript">
function deleteAccount(ID)
{
	var confirmation = window.confirm("Are you sure to delete this transaction?");
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/view-accounts-statements.php?ID="+ID+"&isDelete=1";
	}
}
</script>
<table width='99%' align='center' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<td colspan="2" class="title">
			Total Debited Amount 
		</td>
		<td colspan="6" class="title">
			<?php echo $totalDebitAmount;?>
		</td>
	</tr>
	<tr>
		<td colspan="8">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
	<tr>
		<td colspan="2" class="title">
			Total Credited Amount 
		</td>
		<td colspan="6" class="title">
			<?php echo $totalCredeitAmount;?>
		</td>
	</tr>
	<tr>
		<td colspan="8">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
	<tr>
		<td colspan="8">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-accounts-statements.php" class="link_style8">View Complete Account Balance</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo SITE_URL_EMPLOYEES;?>/print-account-statements.php<?php echo $printLink;?>">PRINT STATEMENTS</a>
		</td>
	</tr>
	<tr>
		<td colspan="8">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
	<tr>
		<td width="8%" class="smalltext2">
			<b>Sr No.</b>
		</td>
		<td width="12%" class="smalltext2">
			<b>Date</b>
		</td>
		<td width="12%" class="smalltext2">
			<b>Debit</b>
		</td>
		<td width="12%" class="smalltext2">
			<b>Credit</b>
		</td>
		<td width="18%"  class="smalltext2">
			<b>Voucher Number</b>
		</td>
		<td class="smalltext2" width="25%"><b>Remarks</b></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="8">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
</table>
<div style='border:0px solid #ff0000;overflow:auto;height:2300px'>
	<table width="100%" align="center" border="0" cellpadding="2" cellspacing="2">
	<?php
		$i	=	$recNo;
		while($row	=   mysql_fetch_assoc($recordSet))
		{
			$i++;
			$accountId		=	$row['accountId'];
			$amount			=	$row['amount'];
			$accountsFor	=	showDate($row['accountsFor']);
			$remarks		=	stripslashes($row['remarks']);
			$remarks		=	nl2br($remarks);

			$voucherNo		=	stripslashes($row['voucherNo']);
			$voucherNo		=	nl2br($voucherNo);

			$type			=	$row['type'];

			$debitMoney		=	"";
			$creditMoney	=	"";
			if($type		==	1)
			{
				$debitMoney	=	$amount;
			}
			else
			{
				$creditMoney=	$amount;
			}
	?>
	<tr>
		<td width="8%" class="smalltext2" valign="top">
			<?php echo $i;?>)
		</td>
		<td width="12%" class="smalltext2" valign="top">
			<?php echo $accountsFor;?>
		</td>
		<td width="12%" class="smalltext2" valign="top">
			<?php echo $debitMoney;?>
		</td>
		<td width="12%" class="smalltext2" valign="top">
			<?php echo $creditMoney;?>
		</td>
		<td width="18%"  class="smalltext2" valign="top">
			<?php echo $voucherNo;?>
		</td>
		<td valign="top" class="smalltext2" width="25%" >
			<?php echo $remarks;?>

		</td>
		<td valign="top" class="smalltext2">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/add-accounts-details.php?ID=<?php echo $accountId;?>" class="link_style6">Edit</a> &nbsp;|&nbsp;
			<a href="javascript:deleteAccount(<?php echo $accountId;?>)" class="link_style6">Delete</a>
		</td>
	</tr>
	<tr>
		<td colspan="8">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
	<?php
		}
	?>
	</table>
</div>
<?php
	echo "<table width='100%'><tr><td align='right'>";
	$pagingObj->displayPaging($queryString);
	echo "&nbsp;&nbsp;</td></tr></table>";
}
else
{
	echo "<br><br><center><font class='error'><b>No Record Found !!</b></font></center><br><br>";
}
include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>