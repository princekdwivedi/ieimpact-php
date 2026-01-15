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
	$employeeObj				= new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/common.php");
	include(SITE_ROOT			. "/includes/send-mail.php");
	$orderObj					= new orders();
	$commonObj					= new common();
	$chooseShiftType			= "1";
	$orderByShifht			    = "shiftType,";
	if(CURRENT_TIME_INDIA      >= "16:00:00" || CURRENT_TIME_INDIA <= "04:00:00")
	{
		$chooseShiftType		= "2";
		$orderByShifht			= "shiftType DESC,";
	}

	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$a_pdfEmployeeWithoutLeave	=	array();
	$a_currentAsignOrders		=	array();
	$a_implodeNewOrdersEmployee =	array();



	$query	=	"SELECT employeeId,fullName,lastName,maximumOrdersAccept,shiftType FROM employee_details  WHERE isActive=1 AND hasPdfAccess=1 ORDER BY ".$orderByShifht."firstName";
	$result	=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row		 =	mysqli_fetch_assoc($result))
		{
			$employeeId				=	$row['employeeId'];
			$completeName			=	stripslashes($row['fullName']);
			$shiftType				=   $row['shiftType'];
			$maximumOrdersAccept	=	$row['maximumOrdersAccept'];
			if($maximumOrdersAccept	==	5)
			{
				$maximumOrdersAccept=	5;
			}
			

			$isOnLeave	 =	$employeeObj->getSingleQueryResult("SELECT onLeave FROM employee_attendence WHERE attendenceId > ".MAX_SEARCH_EMPLOYEE_ATTENDENCE_ID." AND loginDate='".CURRENT_DATE_INDIA."' AND employeeId=$employeeId","onLeave");
			if(empty($isOnLeave))
			{
				$a_pdfEmployeeWithoutLeave[$employeeId]	=	$completeName."|".$shiftType."|".$maximumOrdersAccept;
			}
		}
	}

	$totalNewOrdersTillNow		=	$employeeObj->getSingleQueryResult("SELECT COUNT(*) as total FROM members_orders WHERE status=0 AND isVirtualDeleted=0 AND isOrderChecked=1 AND orderId NOT IN (select orderId from employee_log_prep_orders) AND isNotVerfidedEmailOrder=0","total");
	if(empty($totalNewOrdersTillNow))
	{
		$totalNewOrdersTillNow	=	0;
	}

	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		//pr($_REQUEST);
		if(isset($_POST['assignToEmployee']))
		{
			$a_assignToEmployee			=	$_POST['assignToEmployee'];
			$a_maximumCanAccept			=	$_POST['maximumCanAccept'];

			$a_implodeNewOrdersEmployee	=	implode(",",$a_assignToEmployee);

			$query						=	"SELECT orderId,memberId FROM members_orders WHERE status=0  AND isVirtualDeleted=0 AND orderId NOT IN (select orderId from employee_log_prep_orders) order By orderAddedOn limit 10";
			$result						=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row				=	mysqli_fetch_assoc($result))
				{
					$memberId			=	$row['memberId'];
					$orderId			=	$row['orderId'];
					$a_currentAsignOrders[$orderId]	= $orderId;	

					$query1				=	"SELECT COUNT(*) AS TotalDone,acceptedBy FROM members_orders WHERE acceptedBy IN ($a_implodeNewOrdersEmployee) AND status IN (2,4,5) AND memberId=$memberId GROUP BY acceptedBy ORDER BY TotalDone DESC";
					$result1			=	dbQuery($query1);
					if(mysqli_num_rows($result1))
					{
						while($row1					=	mysqli_fetch_assoc($result1))
						{
							$acceptedBy				=	$row1['acceptedBy'];
							$TotalDone				=	$row1['TotalDone'];

								
							$totalAcceptedOrders	=	$employeeObj->getSingleQueryResult("SELECT COUNT(*) as total FROM members_orders WHERE assignToEmployee='".CURRENT_DATE_INDIA."' AND acceptedBy=$acceptedBy","total");
							if(empty($totalAcceptedOrders))
							{
								$totalAcceptedOrders=	0;
							}
							$maxOrdersCanAccept		=	$a_maximumCanAccept[$acceptedBy];
							if(empty($maxOrdersCanAccept))
							{
								$maxOrdersCanAccept	=	5;
							}

							if($totalAcceptedOrders < $maxOrdersCanAccept)
							{
								$orderStatus	=	$orderObj->getOrderStatus($orderId,$memberId);
								if($orderStatus ==  0)
								{
									$orderObj->acceptCustomerOrder($orderId,$memberId,$acceptedBy);
								}
							}
						}
					}
				}
			}
			if(!empty($a_currentAsignOrders))
			{
				$cuurentlyAssignedOrders	=	implode(",",$a_currentAsignOrders);

				$_SESSION['cuurentlyAssignedOrders']	=	$cuurentlyAssignedOrders;
			}

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/assign-orders-automatically.php?isAssignedManually=1");
			exit();
		}
		
	}

	if(isset($_GET['isAssignedManually']) && $_GET['isAssignedManually'] == 1)
	{
		if(isset($_SESSION['cuurentlyAssignedOrders']))
		{
			$cuurentlyAssignedOrders	=	$_SESSION['cuurentlyAssignedOrders'];
			$andClause		=	" AND orderId IN ($cuurentlyAssignedOrders)";
		}
		else
		{
			$andClause		=	"";
		}
?>
<script type="text/javascript">
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
</script>
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="10" class="textstyle1"><b>VIEW CURRENT ASSIGNED ORDER DETAILS ON <?php echo $nowDateIndia;?></b></td>
	</tr>
	<tr bgcolor="#373737" height="20">
		<td width="4%" class="smalltext8"><b>SR NO<b></td>
		<td width="25%" class="smalltext8"><b>ORDER ADDRESS</b></td>
		<td width="20%" class="smalltext8"><b>CUSTOMER NAME</b></td>
		<td width="12%" class="smalltext8"><b>ORDER PLACED ON</b></td>
		<td width="8%" class="smalltext8"><b>STATUS</b></td>
		<td width="15%" class="smalltext8"><b>ASSIGNED TO EMPLOYEE</b></td>
		<td class="smalltext8"><b>TOTAL ASSIGN FOR TODAY</b></td>
	</tr>
<?php
	$query			=	"SELECT orderId,members_orders.memberId,orderAddress,acceptedBy,status,completeName,totalOrdersPlaced,orderAddedOn,orderAddedTime FROM members_orders INNER JOIN members ON members_orders.memberId=members.memberId WHERE  status IN (0,1) AND isVirtualDeleted=0 AND orderId NOT IN (select orderId from employee_log_prep_orders)".$andClause." order By orderAddedOn,orderAddedTime";
	$result			=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		$i			=	0;
		while($row					=	mysqli_fetch_assoc($result))
		{
			$i++;
			$memberId				=	$row['memberId'];
			$orderId				=	$row['orderId'];
			$acceptedBy				=	$row['acceptedBy'];
			$status					=	$row['status'];
			$orderAddress			=	stripslashes($row['orderAddress']);
			$completeName			=	stripslashes($row['completeName']);
			$totalCustomerOrders	=	$row['totalOrdersPlaced'];
			$orderAddedOn			=	$row['orderAddedOn'];
			$displayDate			=	showDateMonth($orderAddedOn);
			$orderAddedTime			=	$row['orderAddedTime'];
			$displayTime			=	showTimeFormat($orderAddedTime);
			if(!empty($acceptedBy))
			{
				$empName			=	$employeeObj->getEmployeeName($acceptedBy);
				$totalAcceptedOrders=	$employeeObj->getSingleQueryResult("SELECT COUNT(*) as total FROM members_orders WHERE assignToEmployee='".CURRENT_DATE_INDIA."' AND acceptedBy=$acceptedBy","total");
				if(empty($totalAcceptedOrders))
				{
					$totalAcceptedOrders=	0;
				}
			}
			else
			{
				$empName			=	"";
				$totalAcceptedOrders=	"";
			}

			$hasReplied				=	0;
			$statusText				=   "<font color='red'>New Order</font>";
			if($result11			=	$orderObj->isOrderChecked($orderId))
			{
				$statusText			=   "<font color='green'>New Order</font>";
			}
			if($status				==	1)
			{
				$statusText			=   "<font color='#4F0000'>Accepted</font>";
				$hasReplied			=	$employeeObj->getSingleQueryResult("SELECT hasRepliedFileUploaded FROM members_orders_reply WHERE orderId=$orderId AND memberId=$memberId AND hasRepliedFileUploaded=1","hasRepliedFileUploaded");
				if(!empty($hasReplied))
				{
					$statusText		=	"<font color='blue'>QA Pending</font>";
				}
			}

			$customerOrderText		=	"";
			$customerLinkStyle		=	"link_style16";
			
			if(empty($totalCustomerOrders))
			{
				$totalCustomerOrders=	0;
			}
			if($totalCustomerOrders <= 3)
			{
				$customerOrderText	=	"(New Customer)";
			}
			elseif($totalCustomerOrders > 3 && $totalCustomerOrders <= 7)
			{
				$customerOrderText	=	"(Trial Customer)";
			}
			elseif($totalCustomerOrders >= 100 && $totalCustomerOrders < 350)
			{
				$customerOrderText	=	"(Big Customer)";
			}
			elseif($totalCustomerOrders >= 350 && $totalCustomerOrders < 700)
			{
				$customerOrderText	=	"(VIP Customer)";
			}
			elseif($totalCustomerOrders >= 700)
			{
				$customerOrderText	=	"(VVIP Customer)";
			}
			
			
			if($status				==	2)
			{
				$statusText			=   "<font color='green'>Completed</font>";
			}
			elseif($status			==	3)
			{	
				$statusText			=   "<font color='#333333'>Nd Atten.</font>";
			}
			elseif($status			==	5)
			{
				$statusText			=   "<font color='green'>Nd Feedbk.</font>";
			}
			elseif($status			==	4)
			{
				$statusText			=   "<font color='#ff0000'>Cancelled</font>";
			}
			elseif($status			==	6)
			{
				$statusText			=   "<font color='green'>Fd Rcvd</font>";
			}
			$bgColor				=	"class='rwcolor1'";
			if($i%2==0)
			{
				$bgColor			=   "class='rwcolor2'";
			}
			?>
			<tr height="23" <?php echo $bgColor;?>>
				<td class="smalltext16"><?php echo $i;?></td>
				<td class="smalltext16"><?php echo $orderAddress;?></td>
				<td class="smalltext16"><?php echo $completeName;?></td>
				<td class="smalltext16"><?php echo $displayDate.",".$displayTime;?></td>
				<td class="smalltext16"><?php echo $statusText;?></td>
				<td class="smalltext16"><?php echo $empName;?></td>
				<td class="smalltext16">
					<?php
						echo $totalAcceptedOrders."&nbsp;&nbsp;";
						if($status == 1 && empty($hasReplied))	
						{
					?>
					(<a onclick="reAssignOrderWindow(<?php echo $orderId;?>,<?php echo $memberId;?>)" class="link_style12" style='cursor:pointer;' title='Re-Assign'>RE-ASSIGN</a>)
					<?php
						}	
						elseif($status == 0)
						{
					?>
					<a onclick="acceptOrderWindow(<?php echo $orderId;?>,<?php echo $memberId;?>)" class="link_style12" style='cursor:pointer;' title='Re-Assign'>ASSIGN</a>
					<?php
						}
					?>
				</td>
			</tr>
			<?php
			}			
			
		}
		else
		{
			echo "<tr><td colspan='5' class='error' align='center'><b>Sorry No Orders Assign Yet </b></td></tr>";
		}

	}			

?>
</table>
<script type="text/javascript">
	function checkForNumber()
	{
		k = (document.all)?event.keyCode : arguments.callee.caller.arguments[0].which;
		if(k == 8 || k== 0)
		{
			return true;
		}
		if(k >= 48 && k <= 57 )
		{
			return true;
		}
		else if(k == 46)
		{
			return true;
		}
		else
		{
			return false;
		}
	 }
</script>
<form  name='assignAutomatically' method='POST' action="" onsubmit="return validAssign();">
	<table cellpadding="2" cellspacing="2" width='98%'align="center" border='0'>
		<tr>
			<td class="textstyle1" colspan="6">
				<b>ASSIGN NEW ORDERS AUTOMATICALLY TO EMPLOYEES (TOTAL NEW ORDERS - <?php echo $totalNewOrdersTillNow;?>)</b> &nbsp;&nbsp;<br><br><a href="<?php echo SITE_URL_EMPLOYEES;?>/assign-customer-orders.php" class="link_style23">ASSIGN CUSTOMERS MANUALLY</a>
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<?php 
		if(!empty($totalNewOrdersTillNow) && !empty($a_pdfEmployeeWithoutLeave))
		{
				foreach($a_pdfEmployeeWithoutLeave as $k=>$v)	
				{
					list($empName,$timing,$maxOrder)	=	explode("|",$v);
					$shiftText				=   "";
					if($timing				==	2)
					{
						$shiftText			=  "&nbsp;(<font color='#ff0000'>Night Shift</font>)";
					}

					$checked				=	"";
					if($chooseShiftType		==	$timing)
					{
						$checked			=	"checked";
					}
			?>
			<tr>
				<td width="5%">
					<input type="checkbox" name="assignToEmployee[<?php echo $k;?>]" value="<?php echo $k;?>" <?php echo $checked;?>>
				</td>
				<td width="3%">
					<input type="text" name="maximumCanAccept[<?php echo $k;?>]" value="<?php echo $maxOrder;?>" size="3" maxlength="2" onkeypress="return checkForNumber();">
				</td>
				<td class="textstyle">
					<b><?php echo $empName.$shiftText;?></b>
				</td>
			</tr>
			<?php
					
				}
			?>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
					<input type='hidden' name='formSubmitted' value='1'>
				</td>
			</tr>
		<?php
		}
		else
		{
	?>
		<tr>
			<td colspan="2" class="error">
				&nbsp;&nbsp;&nbsp;No New Orders Available
			</td>
		</tr>
	<?php
		
		}
		?>
	</table>
</form>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>