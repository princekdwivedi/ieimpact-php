<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES .   "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES .   "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES .   "/classes/employee.php");
	include(SITE_ROOT			.   "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	.   "/includes/common-array.php");
	$employeeObj				=   new employee();
	include(SITE_ROOT_EMPLOYEES	.   "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	.   "/includes/check-pdf-login.php");
	include(SITE_ROOT			.   "/classes/pagingclass.php");
	include(SITE_ROOT_MEMBERS	.   "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	.   "/classes/orders.php");
	include(SITE_ROOT			.   "/classes/common.php");
	$pagingObj					=   new Paging();
	$memberObj					=   new members();
	$orderObj					=   new orders();
	$commonObj					=   new common();
	$showSubmittedResult		=	false;

	$searchOrderType			=	0;
	$searchOrderTime			=	0;
	$searchFileType				=	0;
	$searchCustomerType			=	0;
	$searchRushSketch			=	0;
	$searchOrder				=	"";
	$t_searchOrder				=	"";
	$searchName					=	"";
	$fromDate					=	"";
	$endDate					=	"";
	$serachOrderIdNumber		=	"";
	$serachCustomerStates		=	"";
	$showPageOrders				=	50;
	$textRed					=	"CUSTOMER NAME/ID";
	$a_allCustomersName			=	$orderObj->getAllCustomersNames();
	$totalUnrepliedOrdersMsg	=	$orderObj->getAllTotalUnrepliedOrderMessage();
	$totalUnrepliedRatingMsg	=	$orderObj->getAllTotalUnrepliedRatingMessage();
	$totalUnrepliedGeneralMsg	=	$orderObj->getAllTotalUnrepliedGeneralMessage();

	//$_SESSION['searchCustomersForNonManager']	=	$getAllCustomers;

	$redirectToPage				=	"";
	$table						=	"members_orders INNER JOIN members ON members_orders.memberId=members.memberId";

	$totalUnReplied				=	0;
	$totalUnReplied				=   $orderObj->checkAcceptedReplyOrder($s_employeeId);
	$maximumOrdersAccept		=	$employeeObj->maximumAcceptOrders($s_employeeId);

	$a_existingCustomerRatings	=	$orderObj->getFeedbackText();
	$a_searchOrderType			=	array("0"=>"All","1"=>"New","2"=>"Accepted","3"=>"Completed","4"=>"Incompleted","5"=>"Need Attention");

	$a_searchOrderTime			=	array("0"=>"All","1"=>"EST","2"=>"CST","3"=>"PST","5"=>"MST","4"=>"HST");

	$a_searchCustomerType		=	array("0"=>"All","1"=>"New","2"=>"Trial");

	$a_searchOrderFileType			=	array("0"=>"All","1"=>"Aurora","2"=>"ACI","3"=>"CLK","4"=>"RPT","5"=>"TOTAL");

	$a_searchRushSketch			=	array("0"=>"All","1"=>"SKETCH","2"=>"RUSH");


	$form						=	SITE_ROOT_EMPLOYEES."/forms/searching-pdf-orders.php";
	$noRecordsFoundFor			=	"";

	if(isset($_REQUEST['searchFormSubmit']))
	{
		extract($_REQUEST);
		//pr($_REQUEST);
		if(!empty($searchOrderType))
		{
			$redirectToPage		.=	"&searchOrderType=".$searchOrderType;
		}
		if(!empty($searchRushSketch))
		{
			$redirectToPage		.=	"&searchRushSketch=".$searchRushSketch;
		}
		if(!empty($searchCustomerType))
		{
			$redirectToPage		.=	"&searchCustomerType=".$searchCustomerType;
		}
		if(!empty($searchFileType))
		{
			$redirectToPage		.=	"&searchFileType=".$searchFileType;
		}
		if(!empty($searchOrderTime))
		{
			$redirectToPage		.=	"&searchOrderTime=".$searchOrderTime;
		}
		if(!empty($searchOrder))
		{
			$searchOrder		 =  stringReplace("#","<=>",$searchOrder);
			$redirectToPage		.=	"&searchOrder=".$searchOrder;
		}
		if(!empty($searchText))
		{
			$searchText				 =	trim($searchText);
			if(is_numeric($searchText))
			{
				$redirectToPage		.=  "&serachCustomerById=$searchText";
			}
			else
			{
				$redirectToPage		.=	"&searchName=$searchText";
			}
		}
		if(!empty($fromDate))
		{
			$redirectToPage		.=	"&fromDate=".$fromDate;
			if(!empty($endDate))
			{
				$redirectToPage	.=	"&endDate=".$endDate;
			}
		}
		if(!empty($serachOrderIdNumber))
		{
			$redirectToPage		.=	"&serachOrderIdNumber=".$serachOrderIdNumber;
		}
		if(!empty($serachCustomerStates))
		{
			$redirectToPage		.=	"&serachCustomerStates=".$serachCustomerStates;
		}
		if(!empty($showPageOrders))
		{
			$redirectToPage		.=	"&showPageOrders=".$showPageOrders;
		}

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1".$redirectToPage);
		exit();
	}
	if(isset($_REQUEST['recNo']))
	{
		$recNo						=	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo						=	0;
	}

	if(isset($_SESSION['isSearchPDFByRatings']))
	{
		unset($_SESSION['isSearchPDFByRatings']);
	}

	$s_isSearchByCustomerOrderType	=	0;
	$s_isSearchByCustomerFileType	=	0;
	$s_isSearchByCustomerRushSketch	=	0;
	$s_isSearchByCustomerTimeZone	=	0;
	$s_isSearchByCustomerFromDate	=	"";
	$s_isSearchByCustomerToDate		=	"";
	$s_isSearchByCustomerToState	=	"";
	$s_isSearchByCustomerId			=	"";
	$s_isSearchByCustomerType		=	"";
	
	$whereClause					=	"WHERE members_orders.isVirtualDeleted=0";
	$orderBy						=	"orderAddedOn DESC,orderAddedTime DESC";
	$andClause						=	"";
	$andClause1						=	"";
	$andClause2						=	"";
	$andClause3						=	"";
	$queryString					=	"";


	if(isset($_GET['isSubmittedForm']) && $_GET['isSubmittedForm'] == 1)
	{
		$showSubmittedResult		=	true;
		$queryString			   .=	"&isSubmittedForm=1";
	}
	else
	{
		if(isset($_SESSION['isSearchByCustomerOrderType']))
		{
			unset($_SESSION['isSearchByCustomerOrderType']);
		}
		if(isset($_SESSION['isSearchByCustomerFileType']))
		{
			unset($_SESSION['isSearchByCustomerFileType']);
		}
		if(isset($_SESSION['isSearchByCustomerRushSketch']))
		{
			unset($_SESSION['isSearchByCustomerRushSketch']);
		}
		if(isset($_SESSION['isSearchByCustomerTimeZone']))
		{
			unset($_SESSION['isSearchByCustomerTimeZone']);
		}
		if(isset($_SESSION['isSearchByCustomerFromDate']))
		{
			unset($_SESSION['isSearchByCustomerFromDate']);
		}
		if(isset($_SESSION['isSearchByCustomerToDate']))
		{
			unset($_SESSION['isSearchByCustomerToDate']);
		}
		if(isset($_SESSION['isSearchByCustomerToState']))
		{
			unset($_SESSION['isSearchByCustomerToState']);
		}
		if(isset($_SESSION['isSearchByCustomerID']))
		{
			unset($_SESSION['isSearchByCustomerID']);
		}
		if(isset($_SESSION['isSearchByCustomerType']))
		{
			unset($_SESSION['isSearchByCustomerType']);
		}
	}
	if(isset($_GET['showPageOrders']))
	{
		$showPageOrders				=	$_GET['showPageOrders'];
		$queryString			   .=	"&showPageOrders=".$showPageOrders;
	}
	if(isset($_GET['searchOrderType']))
	{
		$searchOrderType			=	$_GET['searchOrderType'];
		if(!empty($searchOrderType))
		{
			if($searchOrderType		== 1)
			{
				$andClause			.=	" AND members_orders.status IN(0,6)";
				//$orderBy			 =	"orderAddedOn DESC,orderAddedTime DESC";
				$orderBy			 =	"employeeWarningDate,employeeWarningTime";
				$noRecordsFoundFor  .=  " AND NEW ORDERS";
			}
			elseif($searchOrderType	 == 2)
			{
				$andClause			.=	" AND members_orders.status=1";
				$orderBy			 =	"orderAddedOn,orderAddedTime";
				$noRecordsFoundFor  .=  " AND ACCEPTED ORDERS";
			}
			elseif($searchOrderType	 == 3)
			{
				$andClause			.=	" AND members_orders.status IN (2,4,5)";
				$noRecordsFoundFor  .=  " AND COMPLETED ORDERS";
			}
			elseif($searchOrderType	== 4)
			{
				$andClause			.=	" AND members_orders.status IN (0,1,3,6)";
				$orderBy			 =	"employeeWarningDate,employeeWarningTime";
				$noRecordsFoundFor  .=  " AND INCOMPLETED ORDERS";
			}
			elseif($searchOrderType	== 5)
			{
				$andClause			.=	" AND members_orders.status=3";
				$orderBy			 =	"orderAddedOn,orderAddedTime";
				$noRecordsFoundFor  .=  " AND NEED ATTENTION ORDERS";
			}
			$_SESSION['isSearchByCustomerOrderType']	=	$searchOrderType;
			$queryString			.=	"&searchOrderType=".$searchOrderType;
		}
	}
	if(isset($_GET['serachOrderIdNumber']))
	{
		$serachOrderIdNumber			=	$_GET['serachOrderIdNumber'];
		if(!empty($serachOrderIdNumber))
		{
			$andClause			   .=	" AND members_orders.orderId=$serachOrderIdNumber";
			$queryString		   .=	"&serachOrderIdNumber=".$serachOrderIdNumber;
			$noRecordsFoundFor     .=  " AND ORDER ID";
		}
	}
	if(isset($_GET['searchOrderTime']))
	{
		$searchOrderTime			=	$_GET['searchOrderTime'];
		if(!empty($searchOrderTime))
		{
			$zoneTime				=	$a_searchOrderTime[$searchOrderTime];
			$zones					=	timeZoneStates($zoneTime,$a_usaProvinces);	
			$andClause			   .=	" AND members.state IN ($zones) AND members.country='US'";
			$_SESSION['isSearchByCustomerTimeZone']	=	$zones;
			$queryString		   .=	"&searchOrderTime=".$searchOrderTime;
	
			$noRecordsFoundFor     .=  " AND TIME ZONE - ".$a_searchOrderTime[$searchOrderTime];
		}
	}
	if(isset($_GET['searchRushSketch']))
	{
		$searchRushSketch				=	$_GET['searchRushSketch'];
		if(!empty($searchRushSketch))
		{
			if($searchRushSketch		 ==	2)
			{
				$andClause				.=	" AND isRushOrder=1";
				$noRecordsFoundFor      .=  " AND RUSH ORDER";
			}
			elseif($searchRushSketch	 ==	1)
			{
				$andClause				.=	" AND providedSketch=1";
				$noRecordsFoundFor      .=  " AND SKETCH ORDER";
			}
			$_SESSION['isSearchByCustomerRushSketch']	=	$searchRushSketch;
			$queryString				.=	"&searchRushSketch=".$searchRushSketch;
		}
	}
	if(isset($_GET['searchCustomerType']))
	{
		$searchCustomerType				=	$_GET['searchCustomerType'];
		if(!empty($searchCustomerType))
		{
			if($searchCustomerType		 ==	1)
			{
				$andClause				.=	" AND members.totalOrdersPlaced <= 3";
				$noRecordsFoundFor      .=  " AND CUSTOMER TYPE";
			}
			elseif($searchCustomerType	 ==	2)
			{
				$andClause				.=	" AND members.totalOrdersPlaced > 3 AND members.totalOrdersPlaced <= 7";
				$noRecordsFoundFor      .=  " AND CUSTOMER TYPE";
			}
			$_SESSION['isSearchByCustomerType']	=	$searchCustomerType;
			$queryString				.=	"&searchCustomerType=".$searchCustomerType;
		}
	}
	if(isset($_GET['searchFileType']))
	{
		$searchFileType					=	$_GET['searchFileType'];
		if(!empty($searchFileType))
		{
			$andClause1					.=	" AND members.appraisalSoftwareType=$searchFileType";

			$_SESSION['isSearchByCustomerFileType']	=	$searchFileType;
			$queryString			   .=	"&searchFileType=".$searchFileType;
			$noRecordsFoundFor         .=  " AND FILE TYPE ".$a_allAppraisalFileTypes[$searchFileType];
		}
	}
	if(isset($_GET['searchOrder']))
	{
		$searchOrder		=	$_GET['searchOrder'];
		if(!empty($searchOrder))
		{
			$t_searchOrder		 =	stringReplace("<=>","#",$searchOrder);
			$andClause1			 =	" AND orderAddress='$t_searchOrder'";
			$queryString		.=	"&orderAddress=".$searchOrder;
			$noRecordsFoundFor  .=  " AND ORDER ADDRESS - ".$searchOrder;
		}
	}
	if(isset($_GET['serachCustomerById']))
	{
		$serachCustomerById					=	$_REQUEST['serachCustomerById'];
		if(!empty($serachCustomerById))
		{
			
			$andClause1				   .=	" AND members_orders.memberId IN ($serachCustomerById)";
			$queryString			   .=	"&serachCustomerById=".$serachCustomerById;
			$_SESSION['isSearchByCustomerID']	=	$serachCustomerById;

			$textRed					=	"CUSTOMER NAME/<font color='#ff0000'>ID</font>";
			$noRecordsFoundFor		   .=  " AND CUSTOMER ID";
			
		}
	}
	if(isset($_GET['searchName']))
	{
		$searchName					 =	$_GET['searchName'];
		if(!empty($searchName))
		{
			$andClause1				.=	" AND completeName='$searchName'";
			$textRed				 =	"CUSTOMER <font color='#ff0000'>NAME</font>/ID";
			$queryString			.=	"&searchName=".$searchName;
			$noRecordsFoundFor		.=  " AND CUSTOMER NAME";
		}
	}
	if(isset($_GET['serachCustomerStates']))
	{
		$serachCustomerStates		=	$_GET['serachCustomerStates'];
		$serachCustomerStates		=	trim($serachCustomerStates);
		if(!empty($serachCustomerStates))
		{
			$stateAbbre				=	getSearchStateAbbre($serachCustomerStates,$a_usaProvinces);
			if(!empty($stateAbbre))
			{
				$andClause1		   .=	" AND members.state ='$stateAbbre'";
				$queryString	   .=	"&serachCustomerStates=".$serachCustomerStates;
				$_SESSION['isSearchByCustomerToState']	=	$stateAbbre;
				$noRecordsFoundFor .=  " AND CUSTOMER STATE";
			}
		}
	}
	
	if(isset($_GET['fromDate']))
	{
		$fromDate			 =	$_GET['fromDate'];

		if(!empty($fromDate))
		{
			list($d,$m,$y)	    =   explode("-",$fromDate);

			$t_fromDate		    =	$y."-".$m."-".$d;

			$andClause2		    =	" AND members_orders.orderAddedOn='$t_fromDate'";
			$queryString	   .=	"&fromDate=".$fromDate;
			$_SESSION['isSearchByCustomerFromDate']	=	$t_fromDate;
			$noRecordsFoundFor .=  " AND FROM/FOR DATE - ".showDate($t_fromDate);
			if(isset($_GET['endDate']))
			{
				$endDate			    =	$_GET['endDate'];

				if(!empty($endDate))
				{
					list($ed,$em,$ey)   =   explode("-",$endDate);

					$t_endDate		    =	$ey."-".$em."-".$ed;

					$andClause2		    =	" AND members_orders.orderAddedOn >= '$t_fromDate' AND orderAddedOn <= '$t_endDate'";
					$queryString	   .=	"&endDate=".$endDate;
					$_SESSION['isSearchByCustomerToDate']	=	$t_endDate;
					$noRecordsFoundFor .=  " AND TO DATE - ".showDate($t_endDate);
				}
			}
		}
	}


	include($form);
	if(isset($_GET['orderOf']) && isset($_GET['showingEmployeeOrder']))
	{
		$displayingOrderOfEmployee	=	$_GET['orderOf'];
		$showingEmployeeOrder		=	$_GET['showingEmployeeOrder'];
		if(!empty($displayingOrderOfEmployee) && !empty($showingEmployeeOrder))
		{
			if($s_hasManagerAccess == 1 || $displayingOrderOfEmployee == $s_employeeId)
			{
				$andClause3       =	" AND members_orders.acceptedBy=".$displayingOrderOfEmployee;
				$queryString	  = "&isSubmittedForm=1&orderOf=".$displayingOrderOfEmployee."&showingEmployeeOrder=".$showingEmployeeOrder;
				if(isset($_GET['PAD']) && $_GET['PAD'] == 1)
				{
					$andClause3       .= " AND isDonePostAudit=1";
					$queryString	  .= "&PAD=1";
				}
			
				if(isset($_GET['displayTypeCompleted']) && $_GET['displayTypeCompleted'] == 1)
				{
					$table				=	"members_orders INNER JOIN members ON members_orders.memberId=members.memberId INNER JOIN members_orders_reply ON members_orders.orderId=members_orders_reply.orderId";	
					$andClause3			=	" AND members_orders_reply.qaDoneBy=".$displayingOrderOfEmployee;
					$queryString		= "&isSubmittedForm=1&orderOf=".$displayingOrderOfEmployee."&showingEmployeeOrder=".$showingEmployeeOrder."&displayTypeCompleted=1";
				}
			}
		}
	}
	$headingTextOfPage				=	"VIEW ALL ORDERS FOR YOU";
	$OlinkClass						=	"link_style6";
	$OlinkClass1					=	"link_style6";
	if(isset($_GET['Olink']) && $_GET['Olink'] != 0)
	{
		$Olink						=	$_GET['Olink'];
		if($Olink					==	1)
		{
			$OlinkClass			    =	"link_style24";
			$headingTextOfPage=	"VIEW MY ALL ORDERS";
		}
		if($Olink				    ==	2)
		{
			$OlinkClass1			=	"link_style24";
			$headingTextOfPage		=	"VIEW MY ALL QA ORDERS";
		}
	}
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/wz_tooltip.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>
<script type="text/javascript">
	function openEditWidow(customerId,type)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/show-pdf-customers-employees.php?ID="+customerId+"&type="+type;
		prop = "toolbar=no,scrollbars=yes,width=650,height=220,top=100,left=100";
		window.open(path,'',prop);
	}
	function serachRedirectFileType(addUrl)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php"+addUrl;
	}
	function serachOrderTypeFileType(backLink)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php"+backLink;
	}
	function addCustomerSessionID(customerId,orderId)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&customerId="+customerId+"&orderId="+orderId+"&isSelectCustomer=1";
	}
	function acceptOrderWindow(orderId,customerId)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/accept-orders-behalf-employee.php?orderId="+orderId+"&customerId="+customerId;
		prop = "toolbar=no,scrollbars=yes,width=1200,height=650,top=100,left=100";
		window.open(path,'',prop);
	}
	function reAssignOrderWindow(orderId,customerId)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/re-assign-accepted-orders.php?orderId="+orderId+"&customerId="+customerId;
		prop = "toolbar=no,scrollbars=yes,width=1200,height=650,top=100,left=100";
		window.open(path,'',prop);
	}
	function viewCustomerEmployeeMessages(orderId,customerId)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/view-order-all-messages.php?orderId="+orderId+"&customerId="+customerId;
		prop = "toolbar=no,scrollbars=yes,width=800,height=650,top=100,left=100";
		window.open(path,'',prop);
	}
	function markedPostAuditErrorFiles(orderId,customerId)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/post-audit-errors.php?orderId="+orderId+"&customerId="+customerId;
		prop = "toolbar=no,scrollbars=yes,width=800,height=700,top=100,left=100";
		window.open(path,'',prop);
	}
	
</script>
<?php

	$allTotalCustomersNewOrders		=	@mysql_result(dbQuery("SELECT COUNT(*) FROM members_orders WHERE status=0 AND orderAddedOn >= '2012-04-01' AND isDeleted=0 AND isVirtualDeleted=0"),0);
	
	$displayMarqueMessageDateTime				=	 timeBetweenBeforeMinutes($nowDateIndia,$nowTimeIndia,30);
	list($searchMessageDate,$searchMessageTime)	=	explode("=",$displayMarqueMessageDateTime);

	if($a_marqueeCustomers =	$orderObj->getLastOrderMessagesByCustomers($searchMessageDate,$searchMessageTime))
	{

		$a_marqueeCustomers	=	implode(", ",$a_marqueeCustomers);
	?>
	<table width='99%' align='center' cellpadding='2' cellspacing='2' border='0'>
		<tr>
			<td width="16%" class="heading3">New Messages From :</td>
			<td class="text1">
				<marquee>
					<?php echo $a_marqueeCustomers;?>
				</marquee>
			</td>
		</tr>
	</table>
	<?php
	}
	?>
	<table width="99%" align="center" border="0" cellpadding="0" cellspacing="0">
		 <tr>
			<td align="left">
				<a onclick="serachRedirectFileType('?isSubmittedForm=1&orderOf=<?php echo $s_employeeId;?>&showingEmployeeOrder=1&Olink=1')" class='<?php echo $OlinkClass;?>' style="cursor:pointer;" title='View all of your processed orders'>ALL MY ORDERS </a> 
					&nbsp;|&nbsp;&nbsp;
				<a onclick="serachRedirectFileType('?isSubmittedForm=1&orderOf=<?php echo $s_employeeId;?>&showingEmployeeOrder=1&displayTypeCompleted=1&Olink=2')" class='<?php echo $OlinkClass1;?>' style="cursor:pointer;" title='View all of your QA orders'>ALL MY QA ORDERS </a>
			<?php
				if(!empty($allTotalCustomersNewOrders) && !empty($s_hasManagerAccess))
				{
			?>
			&nbsp;|&nbsp; <a href="<?php echo SITE_URL_EMPLOYEES;?>/assign-customer-orders.php" class='link_style6' style="cursor:pointer;">ASSIGN ALL NEW ORDERS</a> 
			<?php
				}
				if(!empty($totalUnrepliedOrdersMsg))
				{
				?>
				&nbsp;|&nbsp; <a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?unrepliedMsg=1#second" class='link_style6' style="cursor:pointer;">NEW UNREPLIED MESSAGES - <?php echo $totalUnrepliedOrdersMsg;?></a> 
			<?php
				}
				if(!empty($totalUnrepliedRatingMsg))
				{
				?>
				&nbsp;|&nbsp; <a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?unrepliedRatingMsg=1#third" class='link_style6' style="cursor:pointer;">NEW UNREPLIED RATINGS - <?php echo $totalUnrepliedRatingMsg;?></a> 
			<?php
				}
				if(!empty($totalUnrepliedGeneralMsg))
				{
				?>
				&nbsp;|&nbsp; <a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?unrepliedGeneralMsg=1#fifth" class='link_style6' style="cursor:pointer;">NEW GENERAL ORDERS MESSAGES - <?php echo $totalUnrepliedGeneralMsg;?></a> 
			<?php
				}
			?>
			&nbsp;</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
	</table>
	<?php
	if($showSubmittedResult				==	true)
	{
			
		$start					  =	0;
		$recsPerPage	          =	$showPageOrders;	//	how many records per page
		$showPages		          =	10;	
		$pagingObj->recordNo	  =	$recNo;
		$pagingObj->startRow	  = $recNo;
		$pagingObj->whereClause   =	$whereClause.$andClause.$andClause1.$andClause2.$andClause3;
		$pagingObj->recsPerPage   =	$recsPerPage;
		$pagingObj->showPages	  =	$showPages;
		$pagingObj->orderBy		  =	$orderBy;
		$pagingObj->table		  =	$table;
		$pagingObj->selectColumns = "members_orders.*,firstName,completeName,appraisalSoftwareType,totalOrdersPlaced,state";
		$pagingObj->path		  = SITE_URL_EMPLOYEES."/new-pdf-work.php";
		$totalRecords = $pagingObj->getTotalRecords();
		if($totalRecords && $recNo <= $totalRecords)
		{
			$pagingObj->setPageNo();
			$recordSet = $pagingObj->getRecords();
			$i	=	$recNo;

			$totalOrderFound		=  @mysql_result(dbQuery("SELECT COUNT(members_orders.orderId) FROM ".$table." ".$whereClause.$andClause.$andClause1.$andClause2.$andClause3),0);

			
	?>
	<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-functions.js"></script>
	<table width="99%" align="center" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="3">
				<font style="font-size:16px;font-weight:bold;font-family:verdana;color:#4d4d4d">VIEW ALL ORDERS</font>
			</td>
			<td colspan="2">
				<font style="font-size:16px;font-weight:bold;font-family:verdana;color:#4d4d4d">TOTAL FOUND : <?php echo $totalOrderFound;?></font>
			</td>
			<td colspan="6" style="text-align:right" valign="top">
				<font style="font-size:16px;font-weight:bold;font-family:verdana;color:#4d4d4d">ORDERS PRIORITY
				<?php
					foreach($a_timeZoneColor as $k=>$value)
					{
						echo "<font color='$value'>".$k."</font>";
						if($k == "HST")
						{
							break;
						}
						echo "->";
					}
				?>
				  </font>&nbsp;&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="13" height="5"></td>
		</tr>
		<tr bgcolor="#373737" height="20">
	
			<td class="smalltext8" width="215">&nbsp;<b>Customer Name</b></td>
			<td class="smalltext8" width="40"><b>ID</b></td>
			<td class="smalltext8" width="289">&nbsp;<b>Order Address</b></td>
			<td class="smalltext8" width="124"><b>Type</b></td>
			<td class="smalltext8" width="82"><b>File/Sketch</b></td>
			<td class="smalltext8" width="98"><b>Order On</b></td>
			<td class="smalltext8" width="88"><b>TAT</b></td>
			<td class="smalltext8" width="72"><b>&nbsp;Status</b></td>
			<td class="smalltext8" width="97"><b>Accepted By</b></td>
			<td class="smalltext8" width="71"><b>Qa By</b></td>
			<td class="smalltext8"><b>&nbsp;&nbsp;Rating</b></td>
		</tr>
		<?php
			while($row					=   mysql_fetch_assoc($recordSet))
			{
				$i++;	
				$customerId				=	$row['memberId'];
				$completeName			=	stripslashes($row['completeName']);
				$firstName				=	stripslashes($row['firstName']);
				$firstName				=	stringReplace("'","",$firstName);
				$firstName				=	stringReplace('"',"",$firstName);
				$orderId				=	$row['orderId'];
				$orderAddress			=	stripslashes($row['orderAddress']);
				$orderAddress			=	getSubstring($orderAddress,55);
				$orderType				=	$row['orderType'];
				$orderTypeText			=	$a_customerOrder[$orderType];
				$appraisalSoftwareType	=	$row['appraisalSoftwareType'];
				$appraisalText			=	$a_allAppraisalFileTypes[$appraisalSoftwareType];
				
				$providedSketch			=	$row['providedSketch'];
				$sketchStatus			=	$row['sketchStatus'];
				$state					=	$row['state'];
				$isDonePostAudit		=	$row['isDonePostAudit'];
				if(!empty($s_hasManagerAccess))
				{
					$postAuditText		=	"<br>(<a onclick='markedPostAuditErrorFiles($orderId,$customerId)' style='cursor:pointer;' class='link_style17'>Do Audit</a>)";
					if($isDonePostAudit	==	1)
					{
						$postAuditText	=	"<br>(<a onclick='markedPostAuditErrorFiles($orderId,$customerId)' style='cursor:pointer;' class='link_style18'>View Audit</a>)";
					}
				}
				else
				{
					$postAuditText		=	"";
				}
				$hasMarkedSketchYes		=	"NO";
				if($providedSketch		==	1)
				{
				   $hasMarkedSketchYes	=	"YES";
				   if($sketchStatus		==	1)
				   {
						$hasMarkedSketchYes	=	"ACK";
				   }
				   elseif($sketchStatus	==	2)
				   {
						$hasMarkedSketchYes	=	"DONE";
				   }
				}
				$appraisalText			=	$appraisalText."/".$hasMarkedSketchYes;
				$orderAddedOn			=	$row['orderAddedOn'];
				$displayDate			=	showDateMonth($orderAddedOn);
				$orderAddedTime			=	$row['orderAddedTime'];
				$displayTime			=	showTimeFormat($orderAddedTime);
				$status					=	$row['status'];
				$orderCompletedOn		=	$row['orderCompletedOn'];
				$hasReplied				=	0;
				$statusText				=   "<font color='red'>New Order</font>";
				$qaDoneByText			=	"";

				$newAttentionUnmarkTxt	=	"";
				if($status				==	0)
				{
					if($isUnmarkedNeedAttention	=	$orderObj->isOrderWasInNeedAttention($orderId))
					{
						$newAttentionUnmarkTxt	=	"<font color='#ff0000'>(Atten)</font>";
					}
				}
				if($result11			=	$orderObj->isOrderChecked($orderId))
				{
					$statusText			=   "<font color='green'>New Order</font>";
				}
				if($status				==	1)
				{
					$statusText			=   "<font color='#4F0000'>Accepted</font>";
					$hasReplied			=	@mysql_result(dbQuery("SELECT hasRepliedFileUploaded FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId AND hasRepliedFileUploaded=1"),0);
					if(!empty($hasReplied))
					{
						$statusText				=	"<font color='blue'>QA Pending</font>";
					}
					
				}
				
				
				if($status				==	2)
				{
					$statusText			=   "<font color='green'>Completed</font>".$postAuditText;
					$qaDoneBy			=	@mysql_result(dbQuery("SELECT qaDoneBy FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId AND hasQaDone=1"),0);
					if(!empty($qaDoneBy))
					{
						$qaDoneByText	=	$employeeObj->getEmployeeFirstName($qaDoneBy);
					}
				}
				elseif($status			==	3)
				{	
					$statusText			=   "<font color='#333333'>Nd Atten.</font>";
				}
				elseif($status			==	5)
				{
					$statusText			=   "<font color='green'>Nd Feedbk.</font>".$postAuditText;
				}

				elseif($status			==	4)
				{
					$statusText			=   "<font color='#ff0000'>Cancelled</font>";
				}
				elseif($status			==	6)
				{
					$statusText			=   "<font color='green'>Fd Rcvd</font>".$postAuditText;
				}
				$acceptedBy				=	$row['acceptedBy'];
				$totalCustomerOrders	=	$row['totalOrdersPlaced'];
				$rateGiven				=	$row['rateGiven'];
				$memberRateMsg			=	stripslashes($row['memberRateMsg']);
				$isRushOrder			=	$row['isRushOrder'];
				if(!empty($rateGiven))
				{
					if(!empty($memberRateMsg))
					{
						$tipText1		=	$memberRateMsg;
						$tipText1		=	stringReplace('"',"",$tipText1);
						$tipText1		=	stringReplace("'","",$tipText1);
					}
					else
					{
						$tipText1		=	"";
					}
				}
				if($isRushOrder		    ==	1)
				{
				   $isRushOrderFont	    =	"<font color='#ff0000'><b>*</b></font>";
				}
				else
				{
					$isRushOrderFont	=	"";
				}
				$customerOrderText		=	"";
				$customerLinkStyle		=	"link_style16";
				
				if(empty($totalCustomerOrders))
				{
					$totalCustomerOrders=	0;
				}
				if($totalCustomerOrders <= 3)
				{
					$customerOrderText	=	"(New Cus.)";
					$customerLinkStyle	=	"link_style17";
				}
				elseif($totalCustomerOrders > 3 && $totalCustomerOrders <= 7)
				{
					$customerOrderText	=	"(Trial Cus.)";
					$customerLinkStyle	=	"link_style18";
				}
				elseif($totalCustomerOrders >= 100 && $totalCustomerOrders < 350)
				{
					$customerOrderText	=	"(Big Cus.)";
					$customerLinkStyle	=	"link_style20";
				}
				elseif($totalCustomerOrders >= 350 && $totalCustomerOrders < 700)
				{
					$customerOrderText	=	"(VIP Cus.)";
					$customerLinkStyle	=	"link_style21";
				}
				elseif($totalCustomerOrders >= 700)
				{
					$customerOrderText	=	"(VVIP Cus.)";
					$customerLinkStyle	=	"link_style22";
				}

				$timeZoneColor		=	"#333333";
				$timezoneText		=	"";
				if(array_key_exists($state,$a_usaProvinces))
				{
					$timeZone		=	$a_usaProvinces[$state];

					list($stateName,$zone)	=	explode("|",$timeZone);
					if(array_key_exists($zone,$a_timeZoneColor))
					{
						$timeZoneColor		=	$a_timeZoneColor[$zone];
						$timezoneText		=	"(".$zone.")";
					}
					else
					{
						$zone				=	"CST";
						$timeZoneColor		=	$a_timeZoneColor[$zone];
						$timezoneText		=	"(".$zone.")";
					}
				}
				else
				{
					$zone				=	"CST";
					$timeZoneColor		=	$a_timeZoneColor[$zone];
					$timezoneText		=	"(".$zone.")";
				}
				if(!empty($acceptedBy))
				{
					$acceptedByName			=   $employeeObj->getEmployeeFirstName($acceptedBy);
				}
				else
				{
					$acceptedByName			=	"";
				}

				$checkOrderLatestMessageDateTime	=	 timeBetweenBeforeMinutes($nowDateIndia,$nowTimeIndia,30);
				list($searchOrderMessageDate,$searchOrderMessageTime)	=	explode("=",$checkOrderLatestMessageDateTime);

				$showCustomerOldMessages	=	"";

				if($result =	$orderObj->getLastOrderMessages($orderId,$customerId,$searchOrderMessageDate,$searchOrderMessageTime))
				{
					$showCustomerLatestMessages	=	"<br>(<a onclick='viewCustomerEmployeeMessages($orderId,$customerId)' class='link_style17' style='cursor:pointer;'>Customer New Message</a>)";
				}
				else
				{
					$showCustomerLatestMessages	=	"";

					if($result =	$orderObj->getOrderOldMessages($orderId,$customerId,$searchOrderMessageDate,$searchOrderMessageTime))
					{
						$showCustomerOldMessages	=	"<br>(<a onclick='viewCustomerEmployeeMessages($orderId,$customerId)' class='link_style17' style='cursor:pointer;'>Customer Old Message</a>)";
					}
					else
					{
						$showCustomerOldMessages	=	"";
					}
				}

				if($result		=	$orderObj->getOrderInternalMessages($orderId,$searchOrderMessageDate))
				{
					$showEmployeeInternalMessageText=	"<br><a href='".SITE_URL_EMPLOYEES."/internal-emp-msg.php?orderId=$orderId&customerId=$customerId#sendMessages' class='link_style15'>(View internal msg)</a>";
				}
				else
				{
					$showEmployeeInternalMessageText=	"";
				}
				$bgColor					=	"class='rwcolor1'";
				if($i%2==0)
				{
					$bgColor				=   "class='rwcolor2'";
				}

				$isHavingEstimatedTime		=	$row['isHavingEstimatedTime'];
				$employeeWarningDate		=	$row['employeeWarningDate'];
				$employeeWarningTime		=	$row['employeeWarningTime'];
				$expctDelvText				=	 "";

				$displayEstimatedLeft		=	0;
				if($status <= 1)
				{
					
					if($isHavingEstimatedTime==	1)
					{
						
						
						//$displayEstimatedLeft		=	1;
						//list($estY,$estM,$estD)		=	explode("-",$employeeWarningDate);
						//$estimatedMonthDay			=	/showFullTextDate($employeeWarningDate);
						//$estimatedYearTime			=	$estY." ".$employeeWarningTime;
						if($nowDateIndia < $employeeWarningDate)
						{
							$diffMin				=	timeBetweenTwoTimes($nowDateIndia,$nowTimeIndia,$employeeWarningDate,$employeeWarningTime);

							$diffHrsMin				=	getHours($diffMin);

							$expctDelvText			=	$diffHrsMin." Hrs";
						}
						elseif($nowDateIndia		== $employeeWarningDate)
						{
							if($nowTimeIndia	   <= $employeeWarningTime)
							{
								$diffMin			=	timeBetweenTwoTimes($nowDateIndia,$nowTimeIndia,$employeeWarningDate,$employeeWarningTime);

								$diffHrsMin			=	getHours($diffMin);

								$expctDelvText		=	$diffHrsMin." Hrs";
							}
							else
							{
								$expctDelvText		=	"Exceeded";
							}
						}
						else
						{
							$expctDelvText		=	"Exceeded";
						}
					}
				}
				elseif($status == 2 || $status == 5 || $status == 6)
				{
					$orderCompletedTime		=	@mysql_result(dbQuery("SELECT orderCompletedTime FROM members_orders_reply WHERE orderId=$orderId AND memberId=$customerId AND hasQaDone=1"),0);

					if($orderCompletedOn != "0000-00-00" && $orderCompletedTime != "" && $orderCompletedTime != "00:00:00")
					{
						$completedMin		=	timeBetweenTwoTimes($orderAddedOn,$orderAddedTime,$orderCompletedOn,$orderCompletedTime);

						$completedMin		 =	getHours($completedMin);

						$expctDelvText		 =	$completedMin." Hrs Taken";
						$displayEstimatedLeft=	2;
					}
				}	
				
				
	?>
	<!--<tr>
		<td colspan="11">
			<div id="change<?php echo $orderId;?>">
				 <table width="100%" align="center" border="1" cellpadding="0" cellspacing="0">-->
					<tr height="23" <?php echo $bgColor;?>>
						<td class="<?php echo $customerLinkStyle;?>" valign="top">
							<?php 
								echo "<font color='#000000'>".$i.")</font> ($customerId) <a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=$customerId' class='$customerLinkStyle' style='cursor:pointer;'>$completeName</a>";
							?>
						</td>
						<td class="smalltext17" valign="top"><?php echo $orderId;?></td>
						<td class="smalltext17" valign="top">
							<?php 
								if(empty($isSearchedMemberPdfOrder))
								{
									echo $isRushOrderFont."<a href='".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=$orderId&customerId=$customerId' class='link_style12'>$orderAddress</a>&nbsp;($state)";
								}
								else
								{
									echo $isRushOrderFont."<a onclick='addCustomerSessionID($customerId,$orderId)' class='link_style12' style='cursor:pointer;'>$orderAddress</a>&nbsp;($state)";
								}
								echo $showCustomerLatestMessages.$showCustomerOldMessages.$showEmployeeInternalMessageText.$newAttentionUnmarkTxt;
							?>
						</td>
						<td class="smalltext17" valign="top"><?php echo $orderTypeText;?></td>
						<td class="smalltext17" valign="top"><?php echo $appraisalText;?></td>
						<td class="smalltext17" valign="top"><font color="<?php echo $timeZoneColor;?>"><?php echo $displayDate.",".$displayTime.$timezoneText;?></font></td>
						<td class="smalltext17" valign="top">
							<?php
								echo $expctDelvText;
							?>
						</td>
						<td colspan="2" id="change<?php echo $orderId;?>" valign="top">
							<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
								<tr>
								<td class="smalltext17" valign="top" width="43%"><?php echo $statusText;?></td>
								<td class="smalltext17" valign="top">
									 <?php 
										if(!empty($acceptedByName))
										{
											if(!empty($s_hasManagerAccess))
											{
										?>
											  <a onclick="serachRedirectFileType('?isSubmittedForm=1&orderOf=<?php echo $acceptedBy;?>&showingEmployeeOrder=1')" class='link_style12' style="cursor:pointer;" title='View orders of <?php echo $acceptedByName;?>'><?php echo $acceptedByName;?></a>
										<?php
												if($hasReplied	==	0 && $status == 1)
												{
										?>
											(<a onclick="reAssignOrderWindow(<?php echo $orderId;?>,<?php echo $customerId;?>)" class="link_style12" style='cursor:pointer;' title='Re-Assign'>RE-ASSIGN</a>)
										<?php
												}
											}
											elseif(empty($s_hasManagerAccess) && $s_employeeId == $acceptedBy)
											{
										?>
											  <a onclick="serachRedirectFileType('?isSubmittedForm=1&orderOf=<?php echo $acceptedBy;?>&showingEmployeeOrder=1')" class='link_style12' style="cursor:pointer;" title='View orders of <?php echo $acceptedByName;?>'><?php echo $acceptedByName;?></a>
										<?php	
											}
											else
											{
												echo $acceptedByName;
											}
										}
										elseif($status	==	0)
										{
											$acceptUrl	=	SITE_URL_EMPLOYEES."/accept-orders-ajax.php?srNo=".$i."&orderId=";
											if($s_hasManagerAccess)
											{
										?>
										<a onclick="acceptOrderWindow(<?php echo $orderId;?>,<?php echo $customerId;?>)" class="greenLink" style='cursor:pointer;' title='Assign'>ASSIGN</a>
										<?php
											}
											else
											{
												if(!empty($totalUnReplied) && !empty($maximumOrdersAccept))
												{
													if($totalUnReplied < $maximumOrdersAccept)
													{
											?>
														<a onclick="commonFunc1('<?php echo $acceptUrl;?>','change<?php echo $orderId;?>','Are you sure to accept this order of <?php echo $firstName;?>?',<?php echo $orderId?>)" class="greenLink" style='cursor:pointer;' title='Accept It'>ACCEPT</a>
											<?php
													}
													else
													{
											?>
														<a onclick="commonFunc1('<?php echo $acceptUrl;?>','change<?php echo $orderId;?>','Are you sure to accept this order of <?php echo $firstName;?>?',<?php echo $orderId?>)" class="greenLink" style='cursor:pointer;' title='Accept It'>ACCEPT</a>
											<?php
													}
												}
												else
												{
											?>
													<a onclick="commonFunc1('<?php echo $acceptUrl;?>','change<?php echo $orderId;?>','Are you sure to accept this order of <?php echo $firstName;?>?',<?php echo $orderId?>)" class="greenLink" style='cursor:pointer;' title='Accept It'>ACCEPT</a>
										<?php
												}
											}
										}
									?>
								</td>
							 </tr>
							</table>
						</td>
						<td class="smalltext16" valign="top">
							<?php
								if(!empty($s_hasManagerAccess) && !empty($qaDoneByText))
								{
							?>
								  <a onclick="serachRedirectFileType('?isSubmittedForm=1&orderOf=<?php echo $qaDoneBy;?>&showingEmployeeOrder=1&displayTypeCompleted=1')" class='link_style12' style="cursor:pointer;" title='View orders of <?php echo $qaDoneByText;?>'><?php echo $qaDoneByText;?></a>
							<?php
								}
								elseif(empty($s_hasManagerAccess) && !empty($qaDoneByText) && $s_employeeId == $qaDoneBy)
								{
							?>
								   <a onclick="serachRedirectFileType('?isSubmittedForm=1&orderOf=<?php echo $qaDoneBy;?>&showingEmployeeOrder=1&displayTypeCompleted=1')" class='link_style12' style="cursor:pointer;" title='View orders of <?php echo $qaDoneByText;?>'><?php echo $qaDoneByText;?></a>
							<?php	
								}
								else
								{
									echo $qaDoneByText;
								}
							?>
						</td>
						<td class="smalltext16" valign="top">
							<?php
								if(!empty($rateGiven))
								{
							?>
							<img src="<?php echo SITE_URL;?>/images/rating/<?php echo $rateGiven;?>.png"  onmouseover="Tip('<?php echo $tipText1;?>')" onmouseout="UnTip()">
							<?php
								}	
								else
								{
									echo "&nbsp;";
								}
							?>
						</td>
					</tr>
				<!--</table>
			</div>
		</td>
	</tr>-->
	<?php
				
		}
		echo "<tr><td height='7'></td></tr><tr><td align='right' colspan='15'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr>";	
	?>
	</table>
	<?php

		}
		else
		{
			if(empty($noRecordsFoundFor))
			{
				$noRecordsFoundFor		=	"NO RECORD FOUND";
			}
			else
			{
				$noRecordsFoundFor		=	substr($noRecordsFoundFor,4);
				$noRecordsFoundFor		=	"NO RECORD FOUND FOR : ".$noRecordsFoundFor;
			}
			echo "<table width='100%' border='0' style='text-align:center' height='300'><tr><td style='text-align:center;' class='error2'><b>".$noRecordsFoundFor."</b></td></tr></table>";
		}
	}
	else
	{
		echo "<table width='22%' border='0' align='center' height='300'><tr><td align='center' class='error2'><b>PLEASE SUBMIT THE ABOVE FORM</b></td></tr></table>";
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>