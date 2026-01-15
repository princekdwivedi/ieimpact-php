<?php
	ob_start();
	session_start();
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$platform					=	0;
	$customerId					=	0;
	$employeeId					=	0;
	$a_employeeId				=	array();
	$forDate					=	"";
	$toDate						=	"";
	$displayLineFor				=	0;
	$reportView					=	0;
	$andClause					=	"";
	$text						=	"MT WORKSHEET";

	$totalLines					=	0;
	$totalMoney					=	0;
	$grandLines					=	0;
	$grandMoney					=	0;

	$reportView					=	1;
	$employeeType				=	0;
	$underManager				=	0;
	$a_managers				    =	$employeeObj->getAllEmployeeManager();

	$a_displayLines				=	array();

	if(isset($_GET['platform']))
	{
		$platform		=	$_GET['platform'];
		if(!empty($platform))
		{
			$platName	 =	$employeeObj->getPlatformName($platform);
			$text		.=  " OF ".strtoupper($platName);
			$andClause	.=	" AND platform=$platform";
		}
	}
	if(isset($_GET['customerId']))
	{
		$customerId		=	(int)$_GET['customerId'];
		if(!empty($customerId))
		{
			$customerName	=	$employeeObj->getCustomerName($customerId,$platform);

			$text	   .=  " - CLIENT: ".strtoupper($customerName);
			$andClause .=	" AND customerId=$customerId";
		
		}
	}
	if(isset($_GET['forDate']))
	{
		$forDate		=	$_GET['forDate'];
		if(!empty($forDate))
		{
			$dateText	=	" OF ".showDate($forDate);
			$dateClause	=	" AND workedOnDate='$forDate'";
		}
		if(isset($_GET['toDate']))
		{
			$toDate		=	$_GET['toDate'];
			if(!empty($toDate))
			{
				$dateText	=	" FROM ".showDate($forDate)." TO ".showDate($toDate);
				$dateClause	=	" AND workedOnDate >= '$forDate' AND workedOnDate <= '$toDate'";
			}
			$text		.=  strtoupper($dateText);
			$andClause  .=	$dateClause;
		}
	}
	if(isset($_GET['employeeType']))
	{
		$employeeType		=	$_GET['employeeType'];
		if(!empty($employeeType))
		{
			$andClause	   .=	" AND employee_details.employeeType=$employeeType";
			$text		   .=	" for ".$a_inetExtEmployee[$employeeType]." employees";
		}
	}
	if(isset($_GET['underManager']))
	{
		$underManager		=	$_GET['underManager'];
		if(!empty($underManager))
		{
			$andClause	   .=	" AND employee_details.underManager=$underManager";
			$text		   .=	" under manager ".$a_managers[$underManager];
		}
	}
	
	if(isset($_GET['employee']))
	{
		$employeeId	=	$_GET['employee'];
		if(!empty($employeeId))
		{
			$a_employeeId		=	explode(",",$employeeId);
			$andClause		   .=	" AND datewise_employee_works_money.employeeId IN ($employeeId)";

			$totalEmloyee		=	count($a_employeeId);

			if($totalEmloyee < 2 && $totalEmloyee > 0)
			{
				foreach($a_employeeId as $key=>$value)
				{
					$employeeName	=	$employeeObj->getEmployeeName($value);
				}
				$text			.=	" FOR EMPLOYEE ".$employeeName;
			}
			else
			{
				$text			.=	" FOR MULTILE EMPLOYES";
			}
		}
	}
	if(isset($_GET['displayLines']))
	{
		$a_displayLines		=	$_GET['displayLines'];
		if(!empty($a_displayLines))
		{
			$a_displayLines	=	explode(",",$a_displayLines);
		}
	}

	if(isset($_GET['reportView']))
	{
		$reportView		=	$_GET['reportView'];
	}

	require_once(SITE_ROOT.'/classes/Worksheet.php');
	require_once(SITE_ROOT.'/classes/Workbook.php');

	function HeaderingExcel($filename)
	{
      header("Content-type: application/vnd.ms-excel");
      header("Content-Disposition: attachment; filename=$filename" );
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
      header("Pragma: public");
   }

  // HTTP headers
  HeaderingExcel('mt-worksheet.xls');

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
  $worksheet1->set_column(1,1,20);
  $worksheet1->set_column(2,2,20);
  
	$worksheet1->set_column(3,3,20);
	$worksheet1->set_column(4,4,15);
	$worksheet1->set_column(5,5,15);
	$worksheet1->set_column(6,6,28);
	$worksheet1->set_column(7,7,28);
	$worksheet1->set_column(8,8,28);
	$worksheet1->set_column(9,9,15);
	$worksheet1->set_column(10,10,13);
	$worksheet1->set_column(11,11,13);
	$worksheet1->set_column(12,13,13);
	$worksheet1->set_column(13,13,13);
	$worksheet1->set_column(14,14,13);
	$worksheet1->set_column(15,15,13);
	$worksheet1->set_column(16,16,37);
	$worksheet1->set_column(17,17,17);
	$worksheet1->set_column(18,18,37);
	$worksheet1->set_column(19,19,17);

    
  $worksheet1->write_string(0,0,"EMPLOYEE NAME",$formatot);
   $worksheet1->write_string(0,1,"ACCURACY LEVEL",$formatot);
  $worksheet1->write_string(0,2,"ALL TOTAL LINES",$formatot);
  $worksheet1->write_string(0,3,"ALL TOTAL MONEY",$formatot);

  $worksheet1->write_string(0,4,"TRANSCRIPTION-DSP",$formatot);
  $worksheet1->write_string(0,5,"MONEY",$formatot);

  $worksheet1->write_string(0,6,"TRANSCRIPTION-NDSP",$formatot);
  $worksheet1->write_string(0,7,"MONEY",$formatot);

  $worksheet1->write_string(0,8,"VRE-DSP",$formatot);
  $worksheet1->write_string(0,9,"MONEY",$formatot);

  $worksheet1->write_string(0,10,"VRE-NDSP",$formatot);
  $worksheet1->write_string(0,11,"MONEY",$formatot);

  $worksheet1->write_string(0,12,"QA-DSP",$formatot);
  $worksheet1->write_string(0,13,"MONEY",$formatot);

  $worksheet1->write_string(0,14,"QA-NDSP",$formatot);
  $worksheet1->write_string(0,15,"MONEY",$formatot);

  $worksheet1->write_string(0,16,"NIGHT SHIFT LINES-TRANSCRIPTION",$formatot);
  $worksheet1->write_string(0,17,"MONEY",$formatot);
  
  $worksheet1->write_string(0,18,"NIGHT SHIFT LINES-VRE",$formatot);
  $worksheet1->write_string(0,19,"MONEY",$formatot);
 
  
  function cleanData(&$str)
  { 
	$str = preg_replace("/\t/", "\\t", $str);
	$str = preg_replace("/\r?\n/", "\\n", $str);

	return $str;
  } 

	$query	=	"SELECT datewise_employee_works_money.employeeId,SUM(totalDirectTrascriptionLines) AS totalDirectTrascriptionLines,SUM(totalDirectTrascriptionMoney) AS totalDirectTrascriptionMoney,SUM(totalIndirectTrascriptionLines) AS totalIndirectTrascriptionLines,SUM(totalIndirectTrascriptionMoney) AS totalIndirectTrascriptionMoney,SUM(totalDirectVreLines) AS totalDirectVreLines,SUM(totalDirectVreMoney) AS totalDirectVreMoney,SUM(totalIndirectVreLines) AS totalIndirectVreLines,SUM(totalIndirectVreMoney) AS totalIndirectVreMoney,SUM(totalQaLines) AS totalQaLines,SUM(totalDirectQaMoney) AS totalDirectQaMoney,SUM(totalIndirectQaLines) AS totalIndirectQaLines,SUM(totalIndirectQaMoney) AS totalIndirectQaMoney,SUM(totalDirectAuditLines) AS totalDirectAuditLines,SUM(totalDirectAuditMoney) AS totalDirectAuditMoney,SUM(totalIndirectAuditLines) AS totalIndirectAuditLines,SUM(totalIndirectAuditMoney) AS totalIndirectAuditMoney,firstName,lastName,postAuditAccuracy,pendingAccuracy FROM datewise_employee_works_money INNER JOIN employee_details ON datewise_employee_works_money.employeeId=employee_details.employeeId WHERE datewise_employee_works_money.ID > ".MAX_SEARCH_MT_EMPLOYEE_WORKID." AND employee_details.isActive=1".$andClause." GROUP BY datewise_employee_works_money.employeeId";;
  $result	=	mysql_query($query);
  if(mysql_num_rows($result))
  {
		$i			=	0;
		while($row	=	mysql_fetch_assoc($result))
	    {
			$i++;
			$employeeId						=	$row['employeeId'];
			$firstName						=	stripslashes($row['firstName']);
			$lastName						=	stripslashes($row['lastName']);

			$totalDirectTrascriptionLines	=	$row['totalDirectTrascriptionLines'];
			$totalDirectTrascriptionMoney	=	$row['totalDirectTrascriptionMoney'];

			$totalIndirectTrascriptionLines	=	$row['totalIndirectTrascriptionLines'];
			$totalIndirectTrascriptionMoney	=	$row['totalIndirectTrascriptionMoney'];

			$totalDirectVreLines			=	$row['totalDirectVreLines'];
			$totalDirectVreMoney			=	$row['totalDirectVreMoney'];

			$totalIndirectVreLines			=	$row['totalIndirectVreLines'];
			$totalIndirectVreMoney			=	$row['totalIndirectVreMoney'];

			$totalQaLines					=	$row['totalQaLines'];
			$totalDirectQaMoney				=	$row['totalDirectQaMoney'];

			$totalIndirectQaLines			=	$row['totalIndirectQaLines'];
			$totalIndirectQaMoney			=	$row['totalIndirectQaMoney'];

			$totalDirectAuditLines			=	$row['totalDirectAuditLines'];
			$totalDirectAuditMoney			=	$row['totalDirectAuditMoney'];

			$totalIndirectAuditLines		=	$row['totalIndirectAuditLines'];
			$totalIndirectAuditMoney		=	$row['totalIndirectAuditMoney'];

			$postAuditAccuracy				=	$row['postAuditAccuracy'];
			$pendingAccuracy				=	$row['pendingAccuracy'];

			$employeeName					=	$firstName." ".$lastName;
			$employeeName					=	ucwords($employeeName);

			$totalLines		=	$totalDirectTrascriptionLines+$totalIndirectTrascriptionLines+$totalDirectVreLines+$totalIndirectVreLines+$totalQaLines+$totalIndirectQaLines+$totalDirectAuditLines+$totalIndirectAuditLines;

			$totalMoney		=	$totalDirectTrascriptionMoney+$totalIndirectTrascriptionMoney+$totalDirectVreMoney+$totalIndirectVreMoney+$totalDirectQaMoney+$totalIndirectQaMoney+$totalDirectAuditMoney+$totalIndirectAuditMoney;
						
			$totalLines		=	round($totalLines);
			$totalMoney		=	round($totalMoney);

			$grandLines		=	$grandLines+$totalLines;
			$grandMoney		=	$grandMoney+$totalMoney;

			$accuracyLevel	=	"";
			if(!empty($postAuditAccuracy))
			{
				$accuracyLevel.= "Post Audit:".$postAuditAccuracy;
			}
			if(!empty($pendingAccuracy))
			{
				$accuracyLevel.= "Pending:".$pendingAccuracy;
			}

			
			$worksheet1->write($i,0,$employeeName);
			$worksheet1->write($i,1,$accuracyLevel);
			$worksheet1->write($i,2,$totalLines);
			$worksheet1->write($i,3,$totalMoney);

			$worksheet1->write($i,4,$totalDirectTrascriptionLines);
			$worksheet1->write($i,5,$totalDirectTrascriptionMoney);

			$worksheet1->write($i,6,$totalIndirectTrascriptionLines);
			$worksheet1->write($i,7,$totalIndirectTrascriptionMoney);

			$worksheet1->write($i,8,$totalDirectVreLines);
			$worksheet1->write($i,9,$totalDirectVreMoney);

			$worksheet1->write($i,10,$totalIndirectVreLines);
			$worksheet1->write($i,11,$totalIndirectVreMoney);

			$worksheet1->write($i,12,$totalQaLines);
			$worksheet1->write($i,13,$totalDirectQaMoney);

			$worksheet1->write($i,14,$totalIndirectQaLines);
			$worksheet1->write($i,15,$totalIndirectQaMoney);

			$worksheet1->write($i,16,$totalDirectAuditLines);
			$worksheet1->write($i,17,$totalDirectAuditMoney);

			$worksheet1->write($i,18,$totalIndirectAuditLines);
			$worksheet1->write($i,19,$totalIndirectAuditMoney);

			$totalLines		=	0;
			$totalMoney		=	0;
		}
		$k	=	$i+1;

		$worksheet1->write_string($k,0,""); 

		$line	=	$i+2;

		$worksheet1->write($line,0,"GRAND TOTAL");
		$worksheet1->write($line,1,$grandLines);
		$worksheet1->write($line,2,$grandMoney);
  }

  $workbook->close();

?>