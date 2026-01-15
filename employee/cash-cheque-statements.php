<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	$employeeObj				=  new employee();
	$pagingObj			        =  new Paging();
	include(SITE_ROOT_EMPLOYEES	.  "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$cashId			=	0;
	$month			=	"";
	$year			=	"";
	$toMonth		=	"";
	$toYear			=	"";
	$whereClause	=	"";
	$andClause		=	"";
	$orderBy		=	"transactionDate DESC";
	$queryString	=	"";
	$text			=	"View Cash/Cheque Statements";
	$printLink		=	"";
	if(isset($_GET['ID']))
	{
		$cashId		=	(int)$_GET['ID'];
		if(isset($_GET['isDelete']) && $_GET['isDelete'] == 1)
		{
			dbQuery("DELETE FROM cash_cheque_details WHERE cashId=$cashId ");

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/cash-cheque-statements.php");
			exit();
		}
	}
	$form						=	SITE_ROOT_EMPLOYEES."/forms/view-accounts-details.php";
	if(isset($_GET['month']) && isset($_GET['year']))
	{
		$month					=	$_GET['month'];
		$year					=	$_GET['year'];
		if(!empty($month) && !empty($year))
		{
			$whereClause		=	"WHERE MONTH(transactionDate)=$month AND YEAR(transactionDate)=$year";
			$queryString		=	"&month=$month&year=$year";
			$text				=	"View Cash/Cheque Statements For ".$a_month[$month].",".$year;
			$andClause			=	" AND MONTH(transactionDate)=$month AND YEAR(transactionDate)=$year";
			$printLink			=	"?month=$month&year=$year";

			if(isset($_GET['toMonth']) && isset($_GET['toYear']))
			{
				$toMonth		=	$_GET['toMonth'];
				$toYear			=	$_GET['toYear'];
				
				$fromDate		=	$year."-".$month."-01";
				$toDate			=	$toYear."-".$toMonth."-31";

				if(!empty($toMonth) && !empty($toYear))
				{
					$whereClause	=	"WHERE transactionDate >= '$fromDate' AND transactionDate <= '$toDate'";

					$andClause		=	" AND transactionDate >= '$fromDate' AND transactionDate <= '$toDate'";


					$queryString   .=	"&toMonth=$toMonth&toYear=$toYear";
					$text			=	"View Cash/Cheque Statements From ".$a_month[$month].",".$year." To ".$a_month[$toMonth].",".$toYear;
					$printLink	   .=	"&toMonth=$toMonth&toYear=$toYear";
				}
			}
		}
	}
	
	$totalDebitAmount	=	@mysql_result(dbQuery("SELECT SUM(amount) FROM cash_cheque_details WHERE type=1".$andClause),0);
	if(empty($totalDebitAmount))
	{
		$totalDebitAmount=	0;
	}

	$totalCredeitAmount	=	@mysql_result(dbQuery("SELECT SUM(amount) FROM cash_cheque_details WHERE type=2".$andClause),0);
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
	$pagingObj->table		  =	"cash_cheque_details";
	$pagingObj->selectColumns = "*";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/cash-cheque-statements.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
?>
<script type="text/javascript">
function deleteTransaction(ID)
{
	var confirmation = window.confirm("Are you sure to delete this transaction?");
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/cash-cheque-statements.php?ID="+ID+"&isDelete=1";
	}
}
</script>
<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<td colspan="3" class="title">
			Total Debited Amount 
		</td>
		<td colspan="7" class="title">
			<?php echo $totalDebitAmount;?>
		</td>
	</tr>
	<tr>
		<td colspan="10">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
	<tr>
		<td colspan="3" class="title">
			Total Credited Amount 
		</td>
		<td colspan="7" class="title">
			<?php echo $totalCredeitAmount;?>
		</td>
	</tr>
	<tr>
		<td colspan="10">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
	<tr>
		<td colspan="10">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/cash-cheque-statements.php" class="link_style8">View Complete Account Balance</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo SITE_URL_EMPLOYEES;?>/print-cash-cheque-statements.php<?php echo $printLink;?>">PRINT STATEMENTS</a>
		</td>
	</tr>
	<tr>
		<td colspan="10">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
	<tr>
		<td width="4%" class="smalltext2">
			<b>Sr No.</b>
		</td>
		<td width="9%" class="smalltext2">
			<b>Date</b>
		</td>
		<td width="10%" class="smalltext2">
			<b>Transaction Type</b>
		</td>
		<td width="8%" class="smalltext2">
			<b>Debit</b>
		</td>
		<td width="8%" class="smalltext2">
			<b>Credit</b>
		</td>
		<td width="19%" class="smalltext2">
			<b>Person/Firm/Organization Name</b>
		</td>
		<td width="15%"  class="smalltext2">
			<b>Voucher/Cheque Number</b>
		</td>
		<td class="smalltext2" width="20%"><b>Details</b></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="10">
			<hr size="1" width="100%" color="#e4e4e4">
		</td>
	</tr>
</table>
<div style='border:0px solid #ff0000;overflow:auto;height:300px'>
	<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
	<?php
		$i	=	$recNo;
		while($row	=   mysql_fetch_assoc($recordSet))
		{
			$i++;
			$cashId			    =	$row['cashId'];
			$transactionsType	=	$row['transactionsType'];
			$amount				=	$row['amount'];
			$transactionDate	=	showDate($row['transactionDate']);
			$paidReceivedFrom	=	stripslashes($row['paidReceivedFrom']);
			$voucherNo			=	stripslashes($row['voucherNo']);
			$transactionDetails	=	stripslashes($row['transactionDetails']);
			$transactionDetails	=	nl2br($transactionDetails);
			$type				=	$row['type'];
			$transactionsTypeText	=	"CASH";

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

			if($transactionsType == 2)
			{
				$transactionsTypeText	=	"CHEQUE";
			}
	?>
	<tr>
		<td width="4%" class="smalltext2" valign="top">
			<?php echo $i;?>)
		</td>
		<td width="9%" class="smalltext2" valign="top">
			<?php echo $transactionDate;?>
		</td>
		<td width="10%" class="smalltext2" valign="top">
			<?php echo $transactionsTypeText;?>
		</td>
		<td width="8%" class="smalltext2" valign="top">
			<?php echo $debitMoney;?>
		</td>
		<td width="8%" class="smalltext2" valign="top">
			<?php echo $creditMoney;?>
		</td>
		<td width="19%" class="smalltext2" valign="top">
			<?php echo $paidReceivedFrom;?>
		</td>
		<td width="15%"  class="smalltext2" valign="top">
			<?php echo $voucherNo;?>
		</td>
		<td valign="top" class="smalltext2" width="20%" >
			<?php echo $transactionDetails;?>
		</td>
		<td valign="top" class="smalltext2">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/cash-cheque-details.php?ID=<?php echo $cashId;?>" class="link_style6">Edit</a>|
			<a href="javascript:deleteTransaction(<?php echo $cashId;?>)" class="link_style6">Delete</a>
		</td>
	</tr>
	<tr>
		<td colspan="10">
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