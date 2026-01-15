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
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/common.php");
	$employeeObj				= new employee();
	$memberObj					= new members();
	$orderObj					= new orders();
	$commonObj					= new common();
	$showForm					= false;
	$orderId					= 0;
	$customerId					= 0;
	$status						= 0;
	$employeeId					= 0;
	$orderStatus				= 0;
	$errorMessageForm			= "You are not authorized to view this page !!";
	$a_customersEmployees		=	array();
	$implode_customersEmployees	=	0;
	$errorMsg					=	"";
	$acceptedByName				=	"";
	$existingAssignedId			=	0;
	$serachString				=	"";
	$lastAcceptedByEmployeeId	=	"";
	$expctDelvText				=	"";

	$a_totalOrdersAccepted		=	array();
	$query						=	"SELECT COUNT(orderId) as TotalOrders,acceptedBy FROM members_orders WHERE assignToEmployee='$nowDateIndia' AND status=1 GROUP BY acceptedBy ORDER BY TotalOrders";
	$result						=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row					=	mysqli_fetch_assoc($result))
		{
			$totalOrders			=	$row['TotalOrders'];
			$acceptedBy				=	$row['acceptedBy'];
			$a_totalOrdersAccepted[$acceptedBy]= $totalOrders;
		}
	}
	
	if(isset($_GET['orderId']) && isset($_GET['customerId']))
	{
		$orderId				=	$_GET['orderId'];
		$customerId				=	$_GET['customerId'];

		if(!empty($orderId)  && !empty($customerId))
		{				
			if($result				=	$orderObj->getOrderDetails($orderId,$customerId))
			{
				$row						=	mysqli_fetch_assoc($result);
				$status						=	$row['status'];
				$hasReplied					=	$row['hasRepliedUploaded'];

				if($status					==	1 && empty($hasReplied))
				{
					$showForm					=	true;
					$firstName					=	stripslashes($row['firstName']);
					$lastName					=	stripslashes($row['lastName']);
					$orderAddress				=   stripslashes($row['orderAddress']);
					$email						=	$row['email'];
					$acceptedBy					=	$row['acceptedBy'];
					$assignToEmployee			=	$row['assignToEmployee'];
					$assignToTime				=	$row['assignToTime'];
					$isHavingEstimatedTime		=	$row['isHavingEstimatedTime'];
					$employeeWarningDate		=	$row['employeeWarningDate'];
					$employeeWarningTime		=	$row['employeeWarningTime'];
					$acceptedByName				=	stripslashes($row['acceeptedByName']);
					
					if($isHavingEstimatedTime	==	1)
					{
						$expctDelvText		    =	orderTAT($employeeWarningDate,$employeeWarningTime);
					}

					$customerName				=	$firstName." ".substr($lastName, 0, 1);
					$lastAcceptedByEmployeeId	=	$acceptedBy;

					$existingAssignedId			=	$employeeObj->getSingleQueryResult("SELECT assignId FROM assign_orders_to_employee WHERE orderId=$orderId AND memberId=$customerId","assignId");
				}
				else
				{
					
					$errorMessageForm	=  "Trying to open an invalid order.";
				}
			}
			else
			{
				
				$errorMessageForm	=  "Trying to open an invalid order.";
			}
		}
		else
		{
			
			$errorMessageForm	=  "Trying to open an invalid order.";
		}
	}
	else
	{
		$showForm					= false;
	}

	if(empty($s_hasManagerAccess))
	{
		$showForm					= false;
	}
	$form		=	SITE_ROOT_EMPLOYEES."/forms/reset-accepted-orders.php";

	
	if(isset($_GET['searchBy']))
	{
		$searchBy				=	$_GET['searchBy'];

		if($searchBy			==	1)
		{
			if(isset($_GET['searchDate']))
			{
				$t_searchDate	=	$_GET['searchDate'];
				$serachString	=	"&searchBy=".$searchBy."&searchDate=".$t_searchDate;
			}
		}
		else
		{
			if(isset($_GET['searchMonth']))
			{
				$searchMonth	=	$_GET['searchMonth'];
			}
			if(isset($_GET['searchYear']))
			{
				$searchYear		=	$_GET['searchYear'];
			}
			if(!empty($searchYear) && !empty($searchYear))
			{
				$serachString		=	"&searchBy=".$searchBy."&searchMonth=".$searchMonth."&searchYear=".$searchYear;
			}
		}
	}
?>
<html>
<head>
<TITLE>Reset Order Accepted By</TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
<link rel="shortcut icon" href="/new-favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>
</head>
<body>
<script type="text/javascript">
	function reflectChange()
	{
		window.opener.location.reload();
	}
</script>
<center>
<?php
	if($showForm)
	{
?>
<table width="100%" align="center" border="0" cellspacing="3" cellspacing="3">
	<tr>
		<td colspan="3" class="textstyle1"><b>Re-Assign To Employee</b></td>
	</tr>
	<tr>
		<td width="20%" class="title1">
			Customer Name
		</td>
		<td width="2%" class="title1">
			:
		</td>
		<td class="title">
			<?php echo $customerName;?>
		</td>
	</tr>
	<tr>
		<td class="title1">
			Order Address
		</td>
		<td class="title1">
			:
		</td>
		<td class="title">
			<?php echo $orderAddress;?>
		</td>
	</tr>
	<tr>
		<td class="title1">
			TAT
		</td>
		<td class="title1">
			:
		</td>
		<td class="title">
			<?php echo $expctDelvText;?>
		</td>
	</tr>
	<tr>
		<td class="title1">
			Already Assign Order To
		</td>
		<td class="title1">
			:
		</td>
		<td class="error2">
			<?php echo $acceptedByName;?> On <?php echo showDate($assignToEmployee);?> at <?php echo showTimeFormat($assignToTime);?>
		</td>
	</tr>
	
</table>
<?php
		if(isset($_REQUEST['formSubmitted']))
		{
			extract($_REQUEST);
			//pr($_REQUEST);
			//die();
			if(isset($_POST['selectedEmployee']))
			{
				$employeeId	=	$_POST['selectedEmployee'];
			}
			else
			{
				$employeeId	=	0;
			}
			if(!empty($employeeId))
			{
				$employeeName	=	$employeeObj->getSingleQueryResult("SELECT fullName FROM employee_details WHERE employeeId=$employeeId","fullName");
				$employeeName	=	makeDBSafe($employeeName);
				
				dbQuery("UPDATE members_orders SET status=1,acceptedBy=$employeeId,acceeptedByName='$employeeName',assignToEmployee='".CURRENT_DATE_INDIA."',assignToTime='".CURRENT_TIME_INDIA."' WHERE orderId=$orderId AND memberId=$customerId");

				dbQuery("DELETE FROM order_tat_explanation WHERE orderId=$orderId");

				$orderObj->deductOrderRelatedCounts('newOrders');
				
				$performedTask	=	"Change Assigned Employee Order ID - ".$orderId;
				
				$orderObj->trackEmployeeWork($orderId,$employeeId,$performedTask);


				if(!empty($existingAssignedId))
				{
					dbQuery("UPDATE assign_orders_to_employee SET employeeId=$employeeId,managerId='$s_employeeId',assignDate='".CURRENT_DATE_INDIA."',assignedTime='".CURRENT_TIME_INDIA."' WHERE assignId=$existingAssignedId AND orderId=$orderId AND memberId=$customerId");

				}
				else
				{
					dbQuery("INSERT INTO assign_orders_to_employee SET orderId=$orderId,memberId=$customerId,employeeId=$employeeId,managerId='$s_employeeId',assignDate='".CURRENT_DATE_INDIA."',assignedTime='".CURRENT_TIME_INDIA."'");
				}

				if(!empty($serachString))
				{
					$changeUrl		=	SITE_URL_EMPLOYEES."/display-employee-processd-qa-orders.php?".$serachString."&employeeId=".$lastAcceptedByEmployeeId."&isReflect=1";
					//$changeUrl		=	SITE_URL_EMPLOYEES."/test.php?x=";
					$changeInDiv	=	"showEmployeeDetails".$lastAcceptedByEmployeeId;

					echo "<script type='text/javascript'>commonFunc2('$changeUrl','$changeInDiv',$employeeId)</script>";
				}
				else
				{
					echo "<br><center><font class='smalltext2'><b>Successfully re-assigned to employee !!</b></font></center></br>";
		
					echo "<script type='text/javascript'>reflectChange();</script>";
				
					echo "<script>setTimeout('window.close()',1000)</script>";
				}

			}
			else
			{
				$errorMsg	=	"Please select one employee !";
				include($form);
			}
		}	
		else
		{
			include($form);
		}
	}
	else
	{
		echo "<table width='90%' align='center' border='1' height='100'><tr><td align='center' align='center' class='error'><b>$errorMessageForm</b></td></tr></table>";
	}
?>

	<br><br>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
</center>
</body>
</html>

	