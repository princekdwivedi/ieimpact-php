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
	$a_customersEmployees		=	array();
	$a_employees				=	array();
	$a_employeesName			=	array();
	$implode_customersEmployees	=	0;
	$errorMsg					=	"";
	
	
	if(isset($_GET['memberId']))
	{
		$customerId			=	$_GET['memberId'];
		
		$query				=	"SELECT * FROM members WHERE memberId=$customerId AND isActiveCustomer=1";
		$result				=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$showForm		=	true;

			$row					=	mysqli_fetch_assoc($result);
			$firstName		        =	stripslashes($row['firstName']);
			$lastName		        =	stripslashes($row['lastName']);
			$customerName	        =	$firstName." ".substr($lastName, 0, 1);
			$email					=	$row['email'];
			$appraisalSoftwareType	=	$row['appraisalSoftwareType'];
			$appraisalTypeText		=	"";
			if(!empty($appraisalSoftwareType))
			{
				$appraisalTypeText	=	"&nbsp;(<font color='#ff0000'>".$a_appraisalSoftware[$appraisalSoftwareType]."</font>)";
			}
		}
		else
		{
			$errorMessageForm	=  "Not a customer !!";
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
	$form		=	SITE_ROOT_EMPLOYEES."/forms/asign-customers-all-orders.php";

	if(!empty($customerId))
	{
		$query								=	"SELECT a.fullName,b.memberId,a.employeeId,b.totalAccepted,b.ratingWithThreeOrMore FROM employee_details a LEFT JOIN customers_total_orders_done_by b ON (a.employeeId=b.employeeId AND memberId=$customerId) WHERE a.isActive = 1 AND a.hasPdfAccess=1 ORDER BY b.totalAccepted DESC,a.fullName";
		$result									=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			while($row							=  mysqli_fetch_assoc($result))
			{
				$employeeId						=  $row['employeeId'];
				$fullName						=  stripslashes($row['fullName']);
				$a_employeesName[$employeeId]	=  $fullName;
			}
		}
	}
?>
<html>
<head>
<TITLE>Assign Orders Of - <?php echo $customerName;?></TITLE>
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
		<td colspan="3" class="textstyle1"><b>Assign New Orders's Of <?php echo $customerName.$appraisalTypeText;?> To Employes</b></td>
	</tr>
</table>
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
			foreach($a_assignSingleOrderTo as $orderId=>$employeeId)
			{

				if(!empty($employeeId))
				{
					$orderObj->acceptCustomerOrder($orderId,$customerId,$employeeId);

					dbQuery("INSERT INTO assign_orders_to_employee SET orderId=$orderId,memberId=$customerId,employeeId=$employeeId,managerId='$s_employeeId',assignDate='".CURRENT_DATE_INDIA."',assignedTime='".CURRENT_TIME_INDIA."'");
				}
			}
			echo "<br><center><font class='smalltext2'><b>Successfully assigned to employee !!</b></font></center></br>";

			echo "<script type='text/javascript'>reflectChange();</script>";
		
			echo "<script>setTimeout('window.close()',1000)</script>";

		}
		else
		{
			$errorMsg	=	"Please select one employee !";
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

	