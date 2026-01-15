<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	$orderObj					=  new orders();
	
	$a_serachingOrdersBy		=	array("1"=>"From less orders to more|totalEmployeeOrders","2"=>"From more orders to less|totalEmployeeOrders DESC","3"=>"By new employees|employee_details.addedOn DESC","4"=>"By old employees|employee_details.addedOn");

	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	
	$searchBy					=	1;
	$display					=	"";
	$display1					=	"none";
	list($sY,$sM,$sD)			=	explode("-",$nowDateIndia);
	$searchDate					=	$sD."-".$sM."-".$sY;
	$t_searchDate				=	$nowDateIndia;
	$searchMonth				=	$sM;
	$searchYear					=	$sY;
	$employeeId					=	0;
	$text						=	" ON ".showDate($t_searchDate);
	$lastTwoDaysOld				=	getPreviousGivenDate($nowDateIndia,1);   

	$totalAccepted				=	0;
	$totalProcessed				=	0;
	$totalQaDone				=	0;
	$totalRemainingProcessing	=	0;
	$serachString				=	"searchBy=".$searchBy."&searchDate=".$t_searchDate;

	$a_totalOrdersAccepted		=	array();
	$a_totalOrdersProcessed		=	array();
	$a_totalOrdersQAAccepted	=	array();
	$a_totalOrdersQAProcessed	=	array();
	$a_foundEmployees			=	array();

	$query						=	"SELECT COUNT(orderId) as TotalOrders,acceptedBy FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND assignToEmployee >= '$lastTwoDaysOld' AND status=1 GROUP BY acceptedBy ORDER BY TotalOrders";
	$result							=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row					=	mysqli_fetch_assoc($result))
		{
			$totalOrders			=	$row['TotalOrders'];
			$acceptedBy				=	$row['acceptedBy'];
			$a_totalOrdersAccepted[$acceptedBy]= $totalOrders;
			$a_foundEmployees[$acceptedBy]	=	$acceptedBy;
		}
	}

	$query						=	"SELECT COUNT(orderId) as TotalOrders,acceptedBy FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND assignToEmployee='$nowDateIndia' AND status NOT IN (0,1) GROUP BY acceptedBy ORDER BY TotalOrders";
	$result							=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row					=	mysqli_fetch_assoc($result))
		{
			$totalOrders			=	$row['TotalOrders'];
			$acceptedBy				=	$row['acceptedBy'];
			$a_totalOrdersProcessed[$acceptedBy]= $totalOrders;
			$a_foundEmployees[$acceptedBy]	=	$acceptedBy;
		}
	}

	$query						=	"SELECT COUNT(orderId) as TotalOrders,qaAcceptedBy FROM members_orders_reply WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND qaAcceptedDate='$nowDateIndia' AND isQaAccepted=1 AND hasQaDone=0 GROUP BY qaAcceptedBy ORDER BY TotalOrders";
	$result							=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row					=	mysqli_fetch_assoc($result))
		{
			$totalOrders			=	$row['TotalOrders'];
			$qaAcceptedBy			=	$row['qaAcceptedBy'];
			$a_totalOrdersQAAccepted[$qaAcceptedBy]= $totalOrders;
			$a_foundEmployees[$qaAcceptedBy]	=	$qaAcceptedBy;
		}
	}

	
	$query						=	"SELECT COUNT(orderId) as TotalOrders,qaAcceptedBy FROM members_orders_reply WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND qaAcceptedDate='$nowDateIndia' AND isQaAccepted=1 AND hasQaDone=1 GROUP BY qaAcceptedBy ORDER BY TotalOrders";
	$result							=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row					=	mysqli_fetch_assoc($result))
		{
			$totalOrders			=	$row['TotalOrders'];
			$qaAcceptedBy			=	$row['qaAcceptedBy'];
			$a_totalOrdersQAProcessed[$qaAcceptedBy]= $totalOrders;
			$a_foundEmployees[$qaAcceptedBy]	=	$qaAcceptedBy;
		}
	}

	if(!empty($a_foundEmployees))
	{
		$a_newEmployees		=	array_unique($a_foundEmployees);
		$employeeIds		=	implode(",",$a_newEmployees);
		$andClause			=	" AND employeeId IN ($employeeIds)";
	}
	else
	{
		$andClause			=	"";
	}
?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
	<tr>
		<td width="21%" align="center" style="height:17px;color:#ffffff;border:3px solid #333333;background-color:#4c4c4c;">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-customers-orders-details.php" class="link_style1">View Customers Orders Summary</a>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td height="5" colspan="10"></td>
	</tr>
	<tr>
		<td class="textstyle1" colspan="2">
			<b>:: VIEW EMPLOYEES TOTAL ORDERS ACCEPTED/COMPLETED AND QA STATUS <?php echo $text;?>::</b>
		</td>
	</tr>
</table>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>
<?php
	//************** BLOCKS START TO DISPLAY PROCESS ORDER DETAILS ACCEPTED ETC *******
	$query		=	"SELECT employeeId,fullName FROM employee_details WHERE isActive=1 AND hasPdfAccess=1".$andClause." ORDER BY fullName";
	$result		=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
?>
<script type='text/javascript'>
function removeEmployees(employeeId,flag)
{
	if(flag == 1)
	{
		document.getElementById('showEmployeeDetails'+employeeId).style.display = 'inline';
	}
	else
	{
		document.getElementById('showEmployeeDetails'+employeeId).style.display = 'none';
	}
}
function reAssignOrderWindow(orderId,customerId)
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/re-assign-accepted-orders.php?orderId="+orderId+"&customerId="+customerId;
	prop = "toolbar=no,scrollbars=yes,width=1200,height=650,top=100,left=100";
	window.open(path,'',prop);
}
</script>
<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
	<!--<tr>
		<td colspan="10">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/print-employee-order-processing.php?<?php echo $serachString;?>" class="link_style8">PRINT THIS LIST</a>
		</td>
	</tr>-->
	<tr bgcolor="#373737" height="20">
		<td width="10%" class="smalltext8"><b>&nbsp;Sr No</b></td>
		<td width="30%" class="smalltext8"><b>Employee Name</b></td>
		<td width="15%" class="smalltext8"><b>Currently Holding Orders</b></td>
		<td width="15%" class="smalltext8"><b>Total Processed Orders</b></td>
		<td width="15%" class="smalltext8"><b>Currently QA Accepted</b></td>
		<td class="smalltext8"><b>Total QA Processed</b></td>
	</tr>
	<?php
		$i							=	0;
		while($row					=	mysqli_fetch_assoc($result))
		{
			$i++;

			$bgColor				=	"class='rwcolor1'";
			if($i%2==0)
			{
				$bgColor			=   "class='rwcolor2'";
			}
			$employeeId				=	$row['employeeId'];
			$employeeName			=	stripslashes($row['fullName']);
			if(array_key_exists($employeeId,$a_totalOrdersAccepted))
			{
				$totalNewOrders		=	$a_totalOrdersAccepted[$employeeId];
				$totalAccepted		=	$totalAccepted+$totalNewOrders;
			}
			else
			{
				$totalNewOrders		=	0;
			}

			if(array_key_exists($employeeId,$a_totalOrdersProcessed))
			{
				$totalProcessedOrders=	$a_totalOrdersProcessed[$employeeId];
				$totalProcessed		 =	$totalProcessed+$totalProcessedOrders;
			}
			else
			{
				$totalProcessedOrders=	0;
			}

			if(array_key_exists($employeeId,$a_totalOrdersQAAccepted))
			{
				$totalNewQaOrders			=	$a_totalOrdersQAAccepted[$employeeId];
				$totalRemainingProcessing	=	$totalRemainingProcessing+$totalNewQaOrders;
			}
			else
			{
				$totalNewQaOrders	 =	0;
			}

			if(array_key_exists($employeeId,$a_totalOrdersQAProcessed))
			{
				$totalDoneQaOrders   =	$a_totalOrdersQAProcessed[$employeeId];
				$totalQaDone		 =	$totalQaDone+$totalDoneQaOrders;
			}
			else
			{
				$totalDoneQaOrders	 =	0;
			}

	?>
	<tr height="23" <?php echo $bgColor;?>>
		<td class="smalltext16">
			&nbsp;<?php echo $i;?>
		</td>
		<td class="smalltext16">
			<?php
				$url			=	SITE_URL_EMPLOYEES."/display-employee-processd-qa-orders.php?".$serachString."&employeeId=";
			?>
			<a onclick="commonFunc('<?php echo $url;?>','showEmployeeDetails<?php echo $employeeId;?>',<?php echo $employeeId;?>);removeEmployees(<?php echo $employeeId;?>,1);" class='link_style2' style="cursor:pointer">
				<?php echo $employeeName;?>
			</a>
		</td>
		<td style="color:green">
			<b><?php 
					echo $totalNewOrders;
			?></b>
		</td>
		<td class="error2">
			<b><?php 
					echo $totalProcessedOrders;
			?></b>
		</td>
		<td style="color:green">
			<b><?php 
					echo $totalNewQaOrders;
			?></b>
		</td>
		<td class="error2">
			<b><?php 
					echo $totalDoneQaOrders;
			?></b>
		</td>
	</tr>
	<tr>
		<td colspan='15'>
			<div id="showEmployeeDetails<?php echo $employeeId;?>"></div>
		</td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td class="smalltext4" colspan="2">
			<b>TOTAL</b>
		</td>
		<td class="smalltext4">
			<b><?php echo $totalAccepted;?></b>
		</td>
		<td class="smalltext4">
			<b><?php echo $totalProcessed;?></b>
		</td>
		<td class="smalltext4">
			<b><?php echo $totalRemainingProcessing;?></b>
		</td>
		<td class="smalltext4">
			<b><?php echo $totalQaDone;?></b>
		</td>
	</tr>
	<tr>
		
		<td colspan="10" height="1%" style='border-top:1px dotted #4d4d4d;'>&nbsp;</td>
	
	</tr>
	<tr>
		<td colspan="10"></td>
	</tr>
</table>
<?php
	}
	else
	{
		echo "<br><br><center><font class='error'><b>No Order Accepted/QA By Employee</b></font></center>";
	}
	
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>