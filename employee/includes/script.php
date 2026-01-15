<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES			.  "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES			.  "/classes/orders.php");
	include(SITE_ROOT_EMPLOYEES			.  "/includes/common-array.php");
	$employeeObj						=	new employee();
	$orderObj							=   new orders();

	$targetYear							=	"2014";
	$nonLeadingZeroMonth				=	"5";

	$query								=	"SELECT COUNT(*) as Total,checkedBy from checked_customer_orders where MONTH(checkedOn)=5 AND YEAR(checkedOn)=2014 GROUP BY checkedBy";
	$result	=	dbQuery($query);
	if(mysql_num_rows($result))
	{
		$count							=	0;
		while($row						=	mysql_fetch_assoc($result))
		{
			$count++;
			$totalOrders				=	$row['Total'];
			$employeeId					=	$row['checkedBy'];
			$employeeName				=	@mysql_result(dbQuery("SELECT fullName FROM employee_details WHERE employeeId=$employeeId"),0);
			$employeeName				=	makeDBSafe($employeeName);

			$isExistsTarget				=	@mysql_result(dbQuery("SELECT employeeId FROM employee_target WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$targetYear AND employeeId=$employeeId"),0);

			if(!empty($isExistsTarget))
			{
				$query	=	"UPDATE employee_target SET totalCheckedOrders=$totalOrders WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$targetYear AND employeeId=$employeeId";
				dbQuery($query);
			}
			else
			{
				$query	=	"INSERT INTO employee_target SET employeeId=$employeeId,employeeName='$employeeName',totalCheckedOrders=$totalOrders,targetMonth=$nonLeadingZeroMonth,targetYear=$targetYear";
				dbQuery($query);
			}
			
			echo "<br />$count)UPDATED ORDER ID  - ".$employeeName." - ".$totalOrders;

		}
		
	}
	else
	{
		echo "NO RECORD FOUND";
	}


	/*$query							=	"SELECT employeeId,SUM(totalDirectTrascriptionLines) AS totalDirectTrascriptionLines,SUM(totalDirectTrascriptionMoney) AS totalDirectTrascriptionMoney,SUM(totalIndirectTrascriptionLines) AS totalIndirectTrascriptionLines,SUM(totalIndirectTrascriptionMoney) AS totalIndirectTrascriptionMoney,SUM(totalDirectVreLines) AS totalDirectVreLines,SUM(totalDirectVreMoney) AS totalDirectVreMoney,SUM(totalIndirectVreLines) AS totalIndirectVreLines,SUM(totalIndirectVreMoney) AS totalIndirectVreMoney,SUM(totalQaLines) AS totalQaLines,SUM(totalDirectQaMoney) AS totalDirectQaMoney,SUM(totalIndirectQaLines) AS totalIndirectQaLines,SUM(totalIndirectQaMoney) AS totalIndirectQaMoney,SUM(totalDirectAuditLines) AS totalDirectAuditLines,SUM(totalDirectAuditMoney) AS totalDirectAuditMoney,SUM(totalIndirectAuditLines) AS totalIndirectAuditLines,SUM(totalIndirectAuditMoney) AS totalIndirectAuditMoney FROM datewise_employee_works_money WHERE MONTH(workedOnDate)=7 AND YEAR(workedOnDate)=2014 AND isTragetAdded=0 GROUP BY employeeId LIMIT 50";
	$result	=	dbQuery($query);
	if(mysql_num_rows($result))
	{
		$a_printRecords		=	array();
		while($row			=	mysql_fetch_assoc($result))
		{
			$employeeId						=	$row['employeeId'];
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

			$totalLines						=	$totalDirectTrascriptionLines+$totalIndirectTrascriptionLines+$totalDirectVreLines+$totalIndirectVreLines+$totalQaLines+$totalIndirectQaLines+$totalDirectAuditLines+$totalIndirectAuditLines;

			$employeeName					=	$employeeObj->getEmployeeName($employeeId);

			$employeeObj->addMtEmployeeTargetLines($employeeId,$employeeName,"07","2014",$totalLines,0,0);

			$a_printRecords[$employeeName]	=	$totalLines;

			dbQuery("UPDATE datewise_employee_works_money SET isTragetAdded=1 WHERE MONTH(workedOnDate)=7 AND YEAR(workedOnDate)=2014 AND isTragetAdded=0 AND employeeId=$employeeId");
			
		}

		pr($a_printRecords);	
	}
	else
	{
		echo "NO RECORD FOUND";
	}*/


?>
