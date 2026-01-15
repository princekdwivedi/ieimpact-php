<?php
	$attendenceId			=  0;
	$isLogin				=  0;
	$isLogout				=  0;
	$loginDate				=  "0000-00-00";
	$loginTime				=  "00:00:00";
	$logoutDate				=  "0000-00-00";
	$logoutTime				=  "00:00:00";
	$onLeave				=	0;
	$a_assignedToCustomerIds=	array();
	$a_assignedToCustomers	=	array();
	$a_orderCustomers		=	array();
	$a_qaCustomers			=	array();
	$getAllCustomers		=	"";
	$getAllOrderCustomers	=	"";
	$getAllQaCustomers		=	"";
	$totalNewPdfOrders		=	0;
	if(isset($_SESSION['employeeId']))
	{
		$query			=	"SELECT * FROM employee_details WHERE employeeId=$s_employeeId AND isActive=1";
		$result			=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			$row			=	mysql_fetch_assoc($result);
			$fatherName		=	stripslashes($row['fatherName']);
			$email			=	stripslashes($row['email']);
			$gender			=	$row['gender'];
			$altEmail		=	stripslashes($row['altEmail']);
			$phone			=	stripslashes($row['phone']);
			$mobile			=	stripslashes($row['mobile']);
			$dob			=	showDate($row['dob']);
			$city			=	stripslashes($row['city']);
			$state			=	stripslashes($row['state']);
			$country		=	$row['country'];
			$address		=	stripslashes($row['address']);
			$employeeWorksFor=	$row['employeeWorksFor'];
			$employeeType	=   $row['employeeType'];
			$panCardNumber	=   stripslashes($row['panCardNumber']);
			$addedOn		=	showDate($row['addedOn']);
			$isShiftTimeAdded	=	$row['isShiftTimeAdded'];
			$shiftFrom			=	$row['shiftFrom'];
			$shiftTo			=	$row['shiftTo'];
			$t_dictaEscrId		=	stripslashes($row['dictaEscrId']);
					
			$genderText		=	"(M)";
			if($gender	==	"F")
			{
				$genderText	=	"(F)";
			}
			$countryText	=	$a_countries[$country];
		}
		$query		=	"SELECT * FROM employee_attendence WHERE loginDate='".$nowDateIndia."' AND employeeId=$s_employeeId";
		$result		=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			$row			=	mysql_fetch_assoc($result);
			$attendenceId	=	$row['attendenceId'];
			$isLogin		=	$row['isLogin'];
			$isLogout		=	$row['isLogout'];
			$loginDate		=	$row['loginDate'];
			$loginTime		=	$row['loginTime'];
			$logoutDate		=	$row['logoutDate'];
			$logoutTime		=	$row['logoutTime'];
			$onLeave		=	$row['onLeave'];

			
		}
		if(isset($_SESSION['hasManagerAccess']) && $_SESSION['hasManagerAccess'] == 1)
		{
			$query	=	"SELECT * FROM pdf_clients_employees GROUP BY customerId";
			$result		=	dbQuery($query);
			if(mysql_num_rows($result))
			{
				while($row			=	mysql_fetch_assoc($result))
				{
					$customerId		=	$row['customerId'];
					
					$a_orderCustomers[$customerId]	=	$customerId;
						
					$a_qaCustomers[$customerId]		=	$customerId;
						

					$a_assignedToCustomerIds[$customerId]=	$customerId;
					$a_assignedToCustomers[$customerId]	 =	"1|1";
				}
				if(!empty($a_qaCustomers))
				{
					$getAllQaCustomers =	implode(",",$a_qaCustomers);
				}
				if(!empty($a_assignedToCustomerIds))
				{
					$getAllCustomers   =	implode(",",$a_assignedToCustomerIds);
				}
			}
		}
		else
		{
			$query	=	"SELECT * FROM pdf_clients_employees WHERE employeeId=$s_employeeId AND (hasReplyAccess <> 0 OR hasQaAccess <> 0)";
			$result		=	dbQuery($query);
			if(mysql_num_rows($result))
			{
				while($row			=	mysql_fetch_assoc($result))
				{
					$customerId		=	$row['customerId'];
					$hasReplyAccess	=	$row['hasReplyAccess'];
					$hasQaAccess	=	$row['hasQaAccess'];
					
					if(!empty($hasReplyAccess))
					{
						$a_orderCustomers[$customerId]	=	$customerId;
						
					}
					if(!empty($hasQaAccess))
					{
						$a_qaCustomers[$customerId]		=	$customerId;
						

					}
					$a_assignedToCustomerIds[$customerId]=	$customerId;
					$a_assignedToCustomers[$customerId]	 =	$hasReplyAccess."|".$hasQaAccess;
				}
				if(!empty($a_qaCustomers))
				{
					$getAllQaCustomers =	implode(",",$a_qaCustomers);
				}
				if(!empty($a_assignedToCustomerIds))
				{
					$getAllCustomers   =	implode(",",$a_assignedToCustomerIds);
				}
			
				/*if(!empty($a_orderCustomers))
				{
				
					$getAllOrderCustomers =	implode(",",$a_orderCustomers);

					$totalNewPdfOrders	=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders WHERE memberId IN ($getAllOrderCustomers) AND status=0"),0);
					if(empty($totalNewPdfOrders))
					{
						$totalNewPdfOrders	=	0;
					}
				}*/
				/*if(!empty($a_qaCustomers))
				{
					$getAllQaCustomers =	implode(",",$a_qaCustomers);
				}
				if(!empty($a_assignedToCustomerIds))
				{
					$getAllCustomers   =	implode(",",$a_assignedToCustomerIds);
				}*/
			}
		}
		if(!empty($getAllCustomers))
		{
			$totalNewPdfOrders	=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders WHERE memberId IN ($getAllCustomers) AND status=0 AND members_orders.isVirtualDeleted=0"),0);
			if(empty($totalNewPdfOrders))
			{
				$totalNewPdfOrders	=	0;
			}
		}
	}
	$orderFilePath			=	SITE_ROOT_FILES."/files/orderFiles";
	$orderFileUrl			=	SITE_URL."/files/orderFiles";

	$publicRecordFilePath	=	SITE_ROOT_FILES."/files/publicRecordFile";
	$publicRecordFileUrl	=	SITE_URL."/files/publicRecordFile";

	$mlsFilePath			=	SITE_ROOT_FILES."/files/mls";
	$mlsFileUrl				=	SITE_URL."/files/mls";

	$marketConditionFilePath=	SITE_ROOT_FILES."/files/marketCondition";
	$marketConditionFileUrl	=	SITE_URL."/files/marketCondition";

	$otherFilePath			=	SITE_ROOT_FILES."/files/otherFiles";
	$otherFileUrl			=	SITE_URL."/files/otherFiles";
?>