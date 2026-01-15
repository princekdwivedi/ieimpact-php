<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");

	$topDisplayDiv	=	"";
	if($s_employeeId && !isset($isNotDisplayLoadingDiv))
	{
		require_once (SITE_ROOT . '/classes/loading-div.php');
		$divLoader = new loadingDiv;
		$divLoader->loader($topDisplayDiv);
	}
?>
<html>
<head>
<TITLE>Assign To Employee</TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
<link rel="shortcut icon" href="/new-favicon.ico" type="image/x-icon" />
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
	
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	//include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/common.php");
	include(SITE_ROOT			. "/includes/send-mail.php");
	$employeeObj				= new employee();
	$memberObj					= new members();
	$orderObj					= new orders();
	$commonObj					= new common();
	$a_allmanagerEmails			= $commonObj->getMangersEmails();
	$showForm					= false;
	$orderId					= 0;
	$customerId					= 0;
	$status						= 0;
	$employeeId					= 0;
	$orderStatus				= 0;
	$errorMessageForm			= "You are not authorized to view this page !!";
	$a_customersEmployees		=  array();
	$implode_customersEmployees	=  0;
	$errorMsg					=  "";
	$expctDelvText				=  "";
	$lastTwoDaysOld				=	getPreviousGivenDate($nowDateIndia,1);   
	$a_totalOrdersAccepted		=	array();
	$query						=	"SELECT COUNT(orderId) as TotalOrders,acceptedBy FROM members_orders WHERE orderId > ".MAX_SEARCH_EMPLOYEE_ORDER_ID." AND assignToEmployee >= '$lastTwoDaysOld' AND status=1 GROUP BY acceptedBy ORDER BY TotalOrders";
	$result							=	dbQuery($query);
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
		$orderId		=	$_GET['orderId'];
		$customerId		=	$_GET['customerId'];
		$status			=	$orderObj->getOrderStatus($orderId,$customerId);
		if($status	   !=	0 && empty($orderStatus))
		{
			$errorMessageForm				=  "This order is already accepted.";
		}
		else
		{
			if($result						=	$orderObj->getOrderDetails($orderId,$customerId))
			{
				$showForm					=	true;

				$row						=	mysqli_fetch_assoc($result);
				$firstName					=	stripslashes($row['firstName']);
				$lastName					=	stripslashes($row['lastName']);
				$orderAddress				=   stripslashes($row['orderAddress']);
				$email						=	$row['email'];
				$isHavingEstimatedTime		=	$row['isHavingEstimatedTime'];
				$employeeWarningDate		=	$row['employeeWarningDate'];
				$employeeWarningTime		=	$row['employeeWarningTime'];
				$isOrderChecked				=	$row['isOrderChecked'];
				
				if(empty($isOrderChecked))
				{
					$showForm				= false;
				}

				if($isHavingEstimatedTime	==	1)
				{
					$expctDelvText		    =	orderTAT($employeeWarningDate,$employeeWarningTime);
				}

				$customerName				=	$firstName." ".substr($lastName, 0, 1);
			}
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
	$form		=	SITE_ROOT_EMPLOYEES."/forms/accept-employee-on-behalf.php";
	

	if($showForm)
	{
?>
<table width="100%" align="center" border="0" cellspacing="3" cellspacing="3">
	<tr>
		<td colspan="3" class="textstyle1"><b>Assign To Employee</b></td>
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
				$orderObj->acceptCustomerOrder($orderId,$customerId,$employeeId);

				dbQuery("INSERT INTO assign_orders_to_employee SET orderId=$orderId,memberId=$customerId,employeeId=$employeeId,managerId='$s_employeeId',assignDate='".CURRENT_DATE_INDIA."',assignedTime='".CURRENT_TIME_INDIA."'");

				///////////////////////////////////////////////////////////////////////////////
			    //////////////////// PUTTING THE ORDER IN ORDER TRACK LIST ////////////////////
			    $orderObj->addOrderTracker($s_employeeId,$orderId,$orderAddress,'Manager assigned order to employee','MANAGER_ASSIGNED_ORDER');
			    ////////////////////////////////////////////////////////////////////////////////////////////
			    ////////////////////////////////////////////////////////////////////////////////////////////

				$n_from			=	ORDER_FROM_EMAIL;
				$n_fromName		=	"ieIMPACT";;
				$n_to			=	$email; 
				$n_templateId	=	TEMPLATE_SENDING_MESSAGE_TO_ACCEPT_ORDER;
				$n_mailSubject	=	"Started processing your order - ".$orderAddress;
				$employeenName	=	$employeeObj->getEmployeeName($employeeId);
				

				$a_templateData	=	array("{orderNo}"=>$orderAddress,"{name}"=>$firstName);

				//sendTemplateMail($n_from, $n_fromName, $n_to, $n_mailSubject, $n_templateId, $a_templateData);

				if(!empty($a_allmanagerEmails))
				{
					foreach($a_allmanagerEmails as $k=>$value)
					{
						list($managerEmail,$managerName)	=	explode("|",$value);
						$n_to								=	$managerEmail;

						$n_mailSubject	=	"Manager accepting order - ".$orderAddress." for - ".$employeenName; 

						//sendTemplateMail($n_from, $n_fromName, $n_to, $n_mailSubject, $n_templateId, $a_templateData);
					}
				}
				echo "<br><center><font class='smalltext2'><b>Successfully assigned to employee !!</b></font></center></br>";
	
				echo "<script type='text/javascript'>reflectChange();</script>";
			
				echo "<script>setTimeout('window.close()',1000)</script>";

			}
			else
			{
				$errorMsg	=	"Please select an employee.";
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

	