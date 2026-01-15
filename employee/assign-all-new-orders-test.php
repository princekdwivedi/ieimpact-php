<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				= new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/common.php");
	include(SITE_ROOT			. "/includes/send-mail.php");
	include(SITE_ROOT		    . "/classes/pagingclass.php");
	$pagingObj					= new Paging();
	$orderObj					= new orders();
	$commonObj					= new common();
	$a_allmanagerEmails			= $commonObj->getMangersEmails();
	$a_employeesName			= array();
	
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	if(isset($_REQUEST['recNo']))
	{
		$recNo				=	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo				=	0;
	}

	$whereClause			=	"WHERE members_orders.orderId > ".MAX_SEARCH_EMPLOYEE_ORDER_ID." AND members_orders.isVirtualDeleted=0 AND isTestAccount=0 AND isNotVerfidedEmailOrder=0 AND status=0 AND orderAddedOn >= '2012-04-01'";	
	$orderBy				=	"employeeWarningDate ASC,employeeWarningTime ASC";
	$queryString			=	"";
	$andClause				=	"";

	function getCustomerAssignedOffice($customerId,$nowDateIndia){
		
		$a_employeesName	=	array();
		$query				=	"SELECT a.fullName,b.memberId,a.employeeId,b.totalAccepted,b.ratingWithThreeOrMore FROM employee_details a LEFT JOIN customers_total_orders_done_by b ON (a.employeeId=b.employeeId AND memberId=$customerId) WHERE a.isActive = 1 AND a.hasPdfAccess=1 ORDER BY b.totalAccepted DESC,a.fullName";
		$result									=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			while($row							=  mysqli_fetch_assoc($result))
			{
				$employeeId						=  $row['employeeId'];
				$totalAccepted					=  $row['totalAccepted'];
				$fullName						=  stripslashes($row['fullName']);
				if(!empty($totalAccepted)){
					$fullName				   .=	"&nbsp;(".$totalAccepted.")";
				}

				$totalAssigned     =    0;

				$query11	       =	"SELECT COUNT(orderId) as total FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND assignToEmployee='$nowDateIndia' AND status=1 AND acceptedBy=$employeeId";	
				$result11	       =	dbQuery($query11);
				if(mysqli_num_rows($result11)){
					$row11 		   =    mysqli_fetch_assoc($result11);
					$totalAssigned =    $row11['total'];

				}

				if(!empty($totalAssigned)){
					$fullName					.=	"&nbsp;(Assigned - ".$totalAssigned.")";
				}
				else{
					$assigned					=	"";
				}


				$a_employeesName[$employeeId]	=  $fullName;
			}
		}
		return $a_employeesName;
	}

	
?>
	<script type="text/javascript">
		function redirectViewPageTo(flag)
		{
			window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/assign-customer-orders.php?"+flag;
		}
		function assignForCustomer(memberId)
		{
			path = "<?php echo SITE_URL_EMPLOYEES?>/assign-customer-all-orders.php?memberId="+memberId;
			prop = "toolbar=no,scrollbars=yes,width=1200,height=650,top=100,left=70";
			window.open(path,'',prop);
		}

		function displayAssignPopUp(employeeId)
		{
			path = "<?php echo SITE_URL_EMPLOYEES?>/display-employee-assigned-orders-list.php?employeeId="+employeeId+"&displayAll=1";
			prop = "toolbar=no,scrollbars=yes,width=800,height=550,top=100,left=100";
			window.open(path,'',prop);
		}
	</script>
<table cellpadding="2" cellspacing="2" width='98%'align="center" border='0'>
	<tr>
		<td class="textstyle1" colspan="2">
			<b>ASSIGN CUSTOMERS ALL NEW ORDERS</b> 
		</td>
	</tr>
	<tr height="50">
		<td width="30%">
			 <a href="<?php echo SITE_URL_EMPLOYEES;?>/assign-customer-orders.php" class="link_style23">ASSIGN CUSTOMERWISE ORDERS</a>
		</td>
		<td width="30%">
			 <a href="<?php echo SITE_URL_EMPLOYEES;?>/assign-orders-automatically.php" class="link_style23">ASSIGN ALL AUTOMATICALLY</a>
		</td>
	</tr>
</table>
<br>
<?php
	if(isset($_REQUEST['formSubmittedAssign']))
	{
		extract($_REQUEST);
		if(isset($_POST['assignSingleOrderTo']))
		{
			$a_assignSingleOrderTo	=	$_POST['assignSingleOrderTo'];
		}
		else
		{
			$a_assignSingleOrderTo	=	0;
		}

		if(isset($_POST['orderOfCustomer']))
		{
			$a_orderOfCustomer  	=	$_POST['orderOfCustomer'];
		}
		else
		{
			$a_orderOfCustomer	   =	0;
		}
		foreach($a_assignSingleOrderTo as $orderId=>$employeeId)
		{

			if(!empty($employeeId))
			{
				$customerId			=	$a_orderOfCustomer[$orderId];
				$orderObj->acceptCustomerOrder($orderId,$customerId,$employeeId);

				dbQuery("INSERT INTO assign_orders_to_employee SET orderId=$orderId,memberId=$customerId,employeeId=$employeeId,managerId='$s_employeeId',assignDate='".CURRENT_DATE_INDIA."',assignedTime='".CURRENT_TIME_INDIA."'");
			}
		}
		
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/assign-all-new-orders.php");
		exit();	

	}
	

	$totalCustomersNewOrders=	0;
	$start					  =	0;
	$recsPerPage	          =	20;//how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"members_orders INNER JOIN members ON members_orders.memberId=members.memberId";
	$pagingObj->selectColumns = "members_orders.*,firstName,completeName,appraisalSoftwareType,totalOrdersPlaced,state,isVocalCustomer";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/assign-all-new-orders.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>
<form name="assignAllOrders" action="" method="POST">
<table cellpadding="2" cellspacing="2" width='98%'align="center" border='0'>
	<tr bgcolor="#373737" height="20">
		<td width="2%" class="smalltext12">&nbsp;</td>
		<td width="15%" class="smalltext12">Customer Name</td>
		<td width="22%" class="smalltext12">Order Address</td>
		<td width="9%" class="smalltext12">Type</td>
		<td width="6%" class="smalltext12">Order On</td>
		<td width="8%" class="smalltext12">TAT</td>
		<td width="8%" class="smalltext12">Is Checked</td>
		<td width="13%" class="smalltext12">Assign To</td>
		<td>&nbsp;</td>
	</tr>
	<?php
		$i				=	$recNo;
		$totalAvaiable	=	0;
		$assignUrl		=	SITE_URL_EMPLOYEES."/display-employee-assigned.php?employeeId=";
		while($row					=	mysqli_fetch_assoc($recordSet))
		{
			$i++;	
			$customerId				=	$row['memberId'];
			$completeName			=	stripslashes($row['completeName']);
			$firstName				=	stripslashes($row['firstName']);
			$firstName				=	str_replace("'","",$firstName);
			$firstName				=	str_replace('"',"",$firstName);
			$orderId				=	$row['orderId'];
			$orderAddress			=	stripslashes($row['orderAddress']);
			$orderAddress			=	getSubstring($orderAddress,55);
			$orderType				=	$row['orderType'];
			$orderTypeText			=	$a_customerOrder[$orderType];
			$appraisalSoftwareType	=	$row['appraisalSoftwareType'];
			
			$state					=	$row['state'];
			$isOrderChecked			=	$row['isOrderChecked'];

			
			$orderAddedOn			=	$row['orderAddedOn'];
			$displayDate			=	showDateMonth($orderAddedOn);
			$orderAddedTime			=	$row['orderAddedTime'];
			$displayTime			=	showTimeFormat($orderAddedTime);
			$isAddedTatTiming		=	$row['isAddedTatTiming'];
			$isCompletedOnTime		=	$row['isCompletedOnTime'];
			$orderCompletedTat		=	$row['orderCompletedTat'];
			$beforeAfterTimingMin	=	$row['beforeAfterTimingMin'];
			$isRushOrder			=	$row['isRushOrder'];
			$isHavingEstimatedTime	=	$row['isHavingEstimatedTime'];
			$employeeWarningDate	=	$row['employeeWarningDate'];
			$employeeWarningTime	=	$row['employeeWarningTime'];
			$isHavingOrderNewMessage=	$row['isHavingOrderNewMessage'];
			$orderCheckedBy			=	stripslashes($row['orderCheckedBy']);
			
			$isVocalCustomer		=	$row['isVocalCustomer'];

			$vocalText				=	"";
			if($isVocalCustomer		==	"yes"){
				$vocalText			=	"(<font color='#ff0000'>V**</font>)";
			}

		
			$expctDelvText			=	 "";
			
			
			$orderCheckByName				=	"<font color='#ff0000;'>Not Checked</font>";
			if(!empty($isOrderChecked))
			{
				$orderCheckByName			=	getSubstring($orderCheckedBy,20);
			}

			


			
			if($isRushOrder		    ==	1)
			{
			   $isRushOrderFont	    =	"<font color='#ff0000'><b>*</b></font>";
			}
			else
			{
				$isRushOrderFont	=	"";
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
			
						
			
			$bgColor					=	"class='rwcolor1'";
			if($i%2==0)
			{
				$bgColor				=   "class='rwcolor2'";
			}			

			$displayEstimatedLeft		=	0;
			if($isHavingEstimatedTime	==	1 && empty($isAddedTatTiming))
			{
				$expctDelvText			=	orderTAT1($employeeWarningDate,$employeeWarningTime);
			}

			$a_employeesName			=	getCustomerAssignedOffice($customerId,$nowDateIndia);
			pr($a_employeesName);
			die();
	?>
	<tr height="23" <?php echo $bgColor;?>>
		<td class="smalltext17" valign="top">&nbsp;&nbsp;<?php echo $i;?>)</td>
		<td valign="top">
			<?php 
				echo "<a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=$customerId' class='link_style16' style='cursor:pointer;'>$completeName".$vocalText."</a>";
			?>
		</td>
		<td class="smalltext17" valign="top">
			<?php 
				
					echo $isRushOrderFont."<a href='".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=$orderId&customerId=$customerId' class='link_style12'>$orderAddress</a>&nbsp;($state)";
				
			?>
		</td>
		<td class="smalltext17" valign="top"><?php echo $orderTypeText;?></td>
		<td class="smalltext17" valign="top"><font color="<?php echo $timeZoneColor;?>"><?php echo $displayDate.",".$displayTime;?></font></td>
		<td class="smalltext17" valign="top">
			<?php
				if($isAddedTatTiming		==	1)
				{
					$expctDelvText			=	getHours($orderCompletedTat);
					$onTimeText				=	"<b>Ontime</b>";
					if($isCompletedOnTime	==	2)
					{
						$onTimeText			=	"<font color='#ff0000;'><b>Late <b></font>(".getHours($beforeAfterTimingMin).")";
					}
					echo $expctDelvText." ".$onTimeText;
				}
				else
				{
					echo $expctDelvText;
				}
			?>
		</td>
		<td class="smalltext17" valign="top">
			<?php echo $orderCheckByName;?>
		</td>
		<td class="smalltext17" valign="top">
			<?php
				if($isOrderChecked	==	1)
				{
					$totalAvaiable++;
			?>
			<input type="hidden" name="orderOfCustomer[<?php echo $orderId;?>]" value="<?php echo $customerId;?>">
			<select name="assignSingleOrderTo[<?php echo $orderId;?>]" onchange="commonFunc('<?php echo $assignUrl;?>','displayAssign<?php echo $i;?>',this.value);">
				<option value="0">Select</option>
				<?php
					foreach($a_employeesName as $k=>$name)
					{
						echo "<option value='$k'>$name</option>";
					}
				?>
			</select>
			<?php
				}
				else
				{
					echo "<font color='#ff0000'>Files must be checked before assign.</font>";
				}
			?>
		</td>
		<td id="displayAssign<?php echo $i;?>"></td>
	</tr>
	<?php

		}
	?>
	<td colspan="8" style="text-align:center;">
		<?php
			if(!empty($totalAvaiable))
			{
		?>
		<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
		<input type='hidden' name='formSubmittedAssign' value='1'>
		<?php
			}
			else
			{
				echo "&nbsp;";
			}
		?>
	</td>
</tr>
<tr>
	<td colspan="8" style="text-align:right">
		<?php
			$pagingObj->displayPaging($queryString);
		?>&nbsp;&nbsp;
	</td>
</tr>
</table>
<?php
	}
	else
	{
		echo "<br><br><center><font class='error'><b>No Record Found !!</b></font></center>";
	}

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>