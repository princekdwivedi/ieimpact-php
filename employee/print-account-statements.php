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
	
	$text			=	"View Accounts Statements";
	$whereClause	=	"";
	$andClause		=	"";
	$orderBy		=	"accountsFor DESC";
	$currentBalance	=	$employeeObj->getCurrentAccountBalance();

	if(isset($_GET['month']) && isset($_GET['year']))
	{
		$month					=	$_GET['month'];
		$year					=	$_GET['year'];
		if(!empty($month) && !empty($year))
		{
			$whereClause		=	"WHERE month=$month AND year=$year";
			$andClause			=	" AND month=$month AND year=$year";
			$text				=	"View Accounts Statements For ".$a_month[$month].",".$year;

			if(isset($_GET['toMonth']) && isset($_GET['toYear']))
			{
				$toMonth		=	$_GET['toMonth'];
				$toYear			=	$_GET['toYear'];
				if(!empty($toMonth) && !empty($toYear))
				{
					$whereClause	=	"WHERE month >= $month AND year >= $year AND month <= $toMonth AND year <= $toYear";

					$andClause		=	" AND month >= $month AND year >= $year AND month <= $toMonth AND year <= $toYear";

					$text			=	"View Accounts Statements From ".$a_month[$month].",".$year." To ".$a_month[$toMonth].",".$toYear;
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

	function HeaderingExcel($filename)
	{
      header("Content-type: application/vnd.ms-excel");
      header("Content-Disposition: attachment; filename=$filename" );
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
      header("Pragma: public");
   }

  // HTTP headers
  HeaderingExcel('account-statements.xls');

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

 // $worksheet1->write_string(0,0,$text); 

  $worksheet1->write_string(0,0,"Current Balance");
  $worksheet1->write(0,1,$currentBalance);

  $worksheet1->write_string(1,0,"Total Debited Amount");
  $worksheet1->write(1,1,$totalDebitAmount);

  $worksheet1->write_string(2,0,"Total Credited Amount");
  $worksheet1->write(2,1,$totalCredeitAmount);

  $worksheet1->write_string(3,0,"Sr No.",$formatot);
  $worksheet1->write_string(3,1,"Date",$formatot);
  $worksheet1->write_string(3,2,"Debit",$formatot);
  $worksheet1->write_string(3,3,"Credit",$formatot);
  $worksheet1->write_string(3,4,"Voucher Number",$formatot);
  $worksheet1->write_string(3,5,"Remarks",$formatot);
  

function cleanData(&$str)
{ 
	$str = preg_replace("/\t/", "\\t", $str);
	$str = preg_replace("/\r?\n/", "\\n", $str);

	return $str;
} 
$query	=	"SELECT * FROM company_daily_accounts ".$whereClause." ORDER BY accountsFor DESC";
$result		=	mysql_query($query);
if(mysql_num_rows($result))
{
	$i	=	3;
	$k	=	0;
	while($row			=	mysql_fetch_assoc($result))
	{
		$i++;
		$k++;
		$accountId		=	$row['accountId'];
		$amount			=	$row['amount'];
		$accountsFor	=	showDate($row['accountsFor']);
		$remarks		=	stripslashes($row['remarks']);
		$voucherNo		=	stripslashes($row['voucherNo']);
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

		$worksheet1->write($i,0,$k);
		$worksheet1->write($i,1,$accountsFor);
		$worksheet1->write($i,2,$debitMoney);
		$worksheet1->write($i,3,$creditMoney);
		$worksheet1->write($i,4,$voucherNo);
		$worksheet1->write($i,5,$remarks);

	}
}
$workbook->close();
?>