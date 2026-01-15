<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				= new employee();

	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	
	if(isset($_GET['employeeId']) && isset($_GET['displayAll']) && $_GET['displayAll'] == 1){
		$employeeId			=	(int)$_GET['employeeId'];

		if(!empty($employeeId)){

			$employeeName	=	@mysql_result(dbQuery("SELECT fullName FROM employee_details WHERE employeeId=$employeeId"),0);
			$employeeName	=	stripslashes($employeeName);

			$query			=	"SELECT members_orders.memberId,orderId,orderAddress,orderType,orderAddedOn,orderAddedTime,isVocalCustomer,isHavingEstimatedTime,isRushOrder,employeeWarningDate,isAddedTatTiming,employeeWarningTime,completeName,appraisalSoftwareType,totalOrdersPlaced,state,isVocalCustomer FROM members_orders INNER JOIN members ON members_orders.memberId=members.memberId WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND members_orders.assignToEmployee='$nowDateIndia' AND status=1 AND acceptedBy=$employeeId ORDER BY employeeWarningDate ASC,employeeWarningTime ASC";
			$result			=	dbQuery($query);
			if(mysql_num_rows($result)){	
		
?>
<html>
<head>
<TITLE>VIEW ALL INCOMPLETE ORDERS ASSIGNED TO - <?php echo $employeeName;?></TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
<body>
<center>
	<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
		<tr>
			<td colspan="10"><b>VIEW ALL INCOMPLETE ORDERS ASSIGNED TO - <?php echo $employeeName;?></b></td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
		<tr bgcolor="#373737" height="20">
			<td width="3%" class="smalltext12">&nbsp;</td>
			<td width="23%" class="smalltext12">Customer Name</td>
			<td width="35%" class="smalltext12">Order Address</td>
			<td width="15%" class="smalltext12">Type</td>
			<td width="9%" class="smalltext12">Order On</td>
			<td class="smalltext12">TAT</td>
		</tr>
	<?php
		$i							=	0;
		while($row					=	mysql_fetch_assoc($result))
		{
			$i++;	
			$customerId				=	$row['memberId'];
			$completeName			=	stripslashes($row['completeName']);
			$orderId				=	$row['orderId'];
			$orderAddress			=	stripslashes($row['orderAddress']);
			$orderAddress			=	getSubstring($orderAddress,55);
			$orderType				=	$row['orderType'];
			$orderTypeText			=	$a_customerOrder[$orderType];
			$orderAddedOn			=	$row['orderAddedOn'];
			$displayDate			=	showDateMonth($orderAddedOn);
			$orderAddedTime			=	$row['orderAddedTime'];
			$displayTime			=	showTimeFormat($orderAddedTime);
			$state					=	$row['state'];
			$isHavingEstimatedTime	=	$row['isHavingEstimatedTime'];
			$employeeWarningDate	=	$row['employeeWarningDate'];
			$employeeWarningTime	=	$row['employeeWarningTime'];
			$isVocalCustomer		=	$row['isVocalCustomer'];
			$isRushOrder			=	$row['isRushOrder'];
			$isAddedTatTiming		=	$row['isAddedTatTiming'];

			$vocalText				=	"";
			if($isVocalCustomer		==	"yes"){
				$vocalText			=	"(<font color='#ff0000'>V**</font>)";
			}

		
			$expctDelvText			=	 "";
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
			
						
			
			if($isRushOrder		    ==	1)
			{
			   $isRushOrderFont	    =	"<font color='#ff0000'><b>*</b></font>";
			}
			else
			{
				$isRushOrderFont	=	"";
			}		

			$displayEstimatedLeft		=	0;
			if($isHavingEstimatedTime	==	1 && empty($isAddedTatTiming))
			{
				$expctDelvText			=	orderTAT1($employeeWarningDate,$employeeWarningTime);
			}
			$bgColor					=	"class='rwcolor1'";
			if($i%2==0)
			{
				$bgColor				=   "class='rwcolor2'";
			}	
	?>
	<tr height="23" <?php echo $bgColor;?>>
		<td class="smalltext17" valign="top">&nbsp;&nbsp;<?php echo $i;?>)</td>
		<td valign="top">
			<?php 
				echo "<a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=$customerId' class='link_style16' style='cursor:pointer;' target='_blank'>$completeName".$vocalText."</a>";
			?>
		</td>
		<td class="smalltext17" valign="top">
			<?php 				
				echo $isRushOrderFont."<a href='".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=$orderId&customerId=$customerId' class='link_style12' target='_blank'>$orderAddress</a>&nbsp;($state)";
				
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

	</tr>
	<?php
		}
		echo "</table>";
		}
	}
}

?>
<br><br>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
</center>
</body>
</html>