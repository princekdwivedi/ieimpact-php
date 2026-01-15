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
		$query				=	"SELECT fatherName,email,gender,mobile,dob,city,state,country,address,employeeWorksFor,employeeType,panCardNumber,addedOn,isShiftTimeAdded,shiftFrom,shiftTo,dictaEscrId,postAuditAccuracy,pendingAccuracy,commentsAlerts,nuanceID,fiesaID,hasProfilePhoto,profilePhotoExt,hasQaDoneAccess,maximumOrdersAccept,isManager,perAddress,aadhaarNumber,testScore,underManager FROM employee_details WHERE employeeId=$s_employeeId AND isActive=1";
		$result				=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$row								=	mysqli_fetch_assoc($result);
			$fatherName							=	stripslashes($row['fatherName']);
			$email								=	stripslashes($row['email']);
			$gender								=	$row['gender'];
			$mobile								=	stripslashes($row['mobile']);
			$dob								=	showDate($row['dob']);
			$city								=	stripslashes($row['city']);
			$state								=	stripslashes($row['state']);
			$country							=	$row['country'];
			$address							=	stripslashes($row['address']);
			$employeeWorksFor					=	$row['employeeWorksFor'];
			$employeeType						=   $row['employeeType'];
			$panCardNumber						=   stripslashes($row['panCardNumber']);
			$addedOn							=	showDate($row['addedOn']);
			$isShiftTimeAdded					=	$row['isShiftTimeAdded'];
			$shiftFrom							=	$row['shiftFrom'];
			$shiftTo							=	$row['shiftTo'];
			$t_dictaEscrId						=	stripslashes($row['dictaEscrId']);
			$postAuditAccuracy 					=	stripslashes($row['postAuditAccuracy']);
			$pendingAccuracy					=	stripslashes($row['pendingAccuracy']);
			$commentsAlertsToEmployee			=	stripslashes($row['commentsAlerts']);
			$employeeNuanceID					=	stripslashes($row['nuanceID']);
			$employeeFiesaID					=	stripslashes($row['fiesaID']);
			$hasProfilePhoto 					=	$row['hasProfilePhoto'];
			$profilePhotoExt					=	stripslashes($row['profilePhotoExt']);
			$isHavingEmployeeQaAccess			=	$row['hasQaDoneAccess'];
			$maximumOrdersAccept				=	$row['maximumOrdersAccept'];
			$isManger							=	$row['isManager'];
			$perAddress							=	stripslashes($row['perAddress']);
			$aadhaarNumber 						=	stripslashes($row['aadhaarNumber']);
			$employeeOwnTestScore 			    =	$row['testScore'];
			$employeeUnderManager 			    =	$row['underManager'];
			if(empty($aadhaarNumber)){
				$aadhaarNumber					=	"";
			}
					
			$genderText							=	"(M)";
			if($gender							==	"F")
			{
				$genderText						=	"(F)";
			}
			if(array_key_exists($country,$a_countries)){
				$countryText					=	$a_countries[$country];
			}
			else{
				$countryText					=	"India";
			}
		}
		
		
		if(isset($_SESSION['hasManagerAccess']) && $_SESSION['hasManagerAccess'] == 1)
		{
			$s_hasManagerAccess					=	$_SESSION['hasManagerAccess'];
		}
		else
		{
			$s_hasManagerAccess					=	0;
		}

		$totalNewPdfOrders	=	0;		

		$query 				    =	"SELECT orderId FROM members_orders ORDER BY orderId DESC LIMIT 1";
		$result 				=	dbQuery($query);
		if(mysqli_num_rows($result)){
   			$row 				=	mysqli_fetch_assoc($result);
   			$currentLatestOrderId = $row['orderId'];

		}

		$normalSearchId 	  =	$currentLatestOrderId-1000;//Showing Last 1000 orders by 

		$query 				=	"SELECT COUNT(orderId) as total FROM members_orders WHERE members_orders.orderId > ".$normalSearchId." AND members_orders.isVirtualDeleted=0 AND isNotVerfidedEmailOrder=0 AND members_orders.status IN(0,6) ";

		$result 				=	dbQuery($query);
		if(mysqli_num_rows($result)){
   			$row 				=	mysqli_fetch_assoc($result);
   			$totalNewPdfOrders = $row['total'];

		}
		if(empty($totalNewPdfOrders))
		{
			$totalNewPdfOrders	=	0;
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