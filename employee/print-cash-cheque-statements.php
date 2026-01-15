<?php
	ob_start();
	session_start();
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();
	require_once(SITE_ROOT.'/classes/Worksheet.php');
	require_once(SITE_ROOT.'/classes/Workbook.php');

	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	
	$whereClause	=	"";
	$andClause		=	"";
	$orderBy		=	"transactionDate DESC";
	$text			=	"View Cash/Cheque Statements";

	if(isset($_GET['month']) && isset($_GET['year']))
	{
		$month					=	$_GET['month'];
		$year					=	$_GET['year'];
		if(!empty($month) && !empty($year))
		{
			$whereClause		=	"WHERE MONTH(transactionDate)=$month AND YEAR(transactionDate)=$year";
			$text				=	"View Cash/Cheque Statements For ".$a_month[$month].",".$year;
			$andClause			=	" AND MONTH(transactionDate)=$month AND YEAR(transactionDate)=$year";

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

					$text			=	"View Cash/Cheque Statements From ".$a_month[$month].",".$year." To ".$a_month[$toMonth].",".$toYear;
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

	function HeaderingExcel($filename)
	{
      header("Content-type: application/vnd.ms-excel");
      header("Content-Disposition: attachment; filename=$filename" );
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
      header("Pragma: public");
   }

  // HTTP headers
  HeaderingExcel('debit-credit-statements.xls');

  // Creating a workbook
  $workbook = new Workbook("-");
 // // Creating the first worksheet
  $worksheet1 =& $workbook->add_worksheet('First One');
 $worksheet1->set_column(1, 1, 40);
 $worksheet1->set_row(1, 20);
  //$worksheet1->write_string(1, 1, "This worksheet's name is ".$worksheet1->get_name());
  //$worksheet1->write(2,1,"http://www.phpclasses.org/browse.html/package/767.html");
 // $worksheet1->write_number(3, 0, 11);
// $worksheet1->write_number(3, 1, 1);
  //$worksheet1->write_string(3, 2, "by four is");
 // $worksheet1->write_formula(3, 3, "=A4 * (2 + 2)");
 // $worksheet1->write_formula(3, 3, "= SUM(A4:B4)");
 //$worksheet1->write(5, 4, "= POWER(2,3)");
 //$worksheet1->write(4, 4, "= SUM(5, 5, 5)");
 // $worksheet1->write_formula(4, 4, "= LN(2.71428)");
 // $worksheet1->write_formula(5, 4, "= SIN(PI()/2)");

  // Creating the second worksheet
  $worksheet2 =& $workbook->add_worksheet();

  // Format for the headings
  $formatot =& $workbook->add_format();
  $formatot->set_size(10);
  $formatot->set_align('center');
  $formatot->set_color('white');
  $formatot->set_pattern();
  $formatot->set_fg_color('black');

 
  $worksheet1->set_column(0,0,20);
  $worksheet1->set_column(1,2,5);
  $worksheet1->set_column(3,3,15);
  $worksheet1->set_column(4,4,15);
  $worksheet1->set_column(5,5,35);
  $worksheet1->set_column(6,6,35);
  $worksheet1->set_column(6,7,35);
  $worksheet1->set_column(6,8,35);

  //$worksheet1->write_string(0,0,$text); 
 
  $worksheet1->write_string(0,0,"Total Debited Amount");
  $worksheet1->write(0,1,$totalDebitAmount);

  $worksheet1->write_string(1,0,"Total Credited Amount");
  $worksheet1->write(1,1,$totalCredeitAmount);

  $worksheet1->write_string(2,0,"Sr No.",$formatot);
  $worksheet1->write_string(2,1,"Date",$formatot);
  $worksheet1->write_string(2,2,"Transaction Type",$formatot);
  $worksheet1->write_string(2,3,"Debit",$formatot);
  $worksheet1->write_string(2,4,"Credit",$formatot);
  $worksheet1->write_string(2,5,"Person/Firm/Organization Name",$formatot);
  $worksheet1->write_string(2,6,"Voucher/Cheque Number",$formatot);
  $worksheet1->write_string(2,7,"Details",$formatot);
  

function cleanData(&$str)
{ 
	$str = preg_replace("/\t/", "\\t", $str);
	$str = preg_replace("/\r?\n/", "\\n", $str);

	return $str;
} 
$query	=	"SELECT * FROM cash_cheque_details ".$whereClause." ORDER BY transactionDate DESC";
$result		=	mysql_query($query);
if(mysql_num_rows($result))
{
	$i	=	2;
	$k	=	0;
	while($row			=	mysql_fetch_assoc($result))
	{
		$i++;
		$k++;
		$cashId			    =	$row['cashId'];
		$transactionsType	=	$row['transactionsType'];
		$amount				=	$row['amount'];
		$transactionDate	=	showDate($row['transactionDate']);
		$paidReceivedFrom	=	stripslashes($row['paidReceivedFrom']);
		$voucherNo			=	stripslashes($row['voucherNo']);
		$transactionDetails	=	stripslashes($row['transactionDetails']);
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

		$worksheet1->write($i,0,$k);
		$worksheet1->write($i,1,$transactionDate);
		$worksheet1->write($i,2,$transactionsTypeText);
		$worksheet1->write($i,3,$debitMoney);
		$worksheet1->write($i,4,$creditMoney);
		$worksheet1->write($i,5,$paidReceivedFrom);
		$worksheet1->write($i,6,$voucherNo);
		$worksheet1->write($i,7,$transactionDetails);
	}
}
$workbook->close();
?>