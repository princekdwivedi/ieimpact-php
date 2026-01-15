<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	."/includes/common-array.php");
	$employeeObj				=	new employee();
	include(SITE_ROOT			. "/admin/includes/common-array.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$validator					=	new validate();

	$a_assignFor				=	array();
	$a_replyAccess				=	array();
	$a_qaAccess					=	array();
	$a_existingCustomers		=	array();
	$isDelete					=	0;
	$pdfRateId					=	0;
	$hasPdfAccess				=	0;
	$showDelete					=	false;
	$checkedEmailReceive		=	"";
	$emailAccess				=	0;
	$maximumOrdersAccept		=	0;
	
	if(isset($_GET['ID']))
	{
		$employeeId		=   (int)$_GET['ID'];

		$emailAccess		=	$employeeObj->hasEmailReceiveAccess($employeeId);
		$maximumOrdersAccept=	$employeeObj->maximumAcceptOrders($employeeId);
		if($emailAccess == 1)
		{
			$checkedEmailReceive	=	"checked";
		}

		if($employeeName=	$employeeObj->getEmployeeName($employeeId))
		{
			$query		=	"SELECT * FROM pdf_clients_employees WHERE employeeId=$employeeId";
			$result		=	dbQuery($query);
			if(mysql_num_rows($result))
			{
				$showDelete	=	true;
				while($row  =   mysql_fetch_assoc($result))
				{
					$customerId		=	$row['customerId'];
					$hasReplyAccess	=	$row['hasReplyAccess'];
					$hasQaAccess	=	$row['hasQaAccess'];
					$a_assignFor[]	=	$customerId;
					$a_existingCustomers[$customerId]=$customerId;
					if(!empty($hasReplyAccess))
					{
						$a_replyAccess[$customerId]=	1;
					}
					if(!empty($hasQaAccess))
					{
						$a_qaAccess[$customerId]   =	1;
					}
				}
			}
			if(isset($_GET['customerId']) && isset($_GET['isDelete']) && $_GET['isDelete'] == 1)
			{
				$customerId		=	$_GET['customerId'];
				if(!empty($customerId))
				{
					dbQuery("DELETE FROM pdf_clients_employees WHERE customerId=$customerId AND employeeId=$employeeId");
				}

				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES."/assign-employee-pdf.php?ID=$employeeId");
				exit();
			}
		}
		else
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$form		=	SITE_ROOT_EMPLOYEES . "/forms/assign-employee-pdf.php";
	
?>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td colspan="2" class='title1'>ADD EDIT PDF CUSTOMERS FOR EMPLOYEE - <?php echo strtoupper($employeeName);?></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
</table>
<?php
if(isset($_GET['success']))
{
	echo "<center><br><font class='text'>Successfully Add Edit Employee To Pdf Customers !!</font></center><br>";
}
if(isset($_REQUEST['formSubmitted1']))
{
	//echo "vvvvv";
	extract($_REQUEST);
	//pr($_POST);
	//die();
	if(isset($_POST['isDelete']))
	{
		$isDelete		  =	1;
	}
	if(isset($_POST['hasEmailAccess']))
	{
		$receivePdfEmails	=	1;
	}
	else
	{
		$receivePdfEmails	=	0;
	}
	if(isset($_POST['assignFor']))
	{
		$a_assignFor	   =	$_POST['assignFor'];
		if(isset($_POST['hasReplyAccess']))
		{
			$a_replyAccess =	$_POST['hasReplyAccess'];
		}
		else
		{
			$a_replyAccess	=	array();
		}
		if(isset($_POST['hasQaAccess']))
		{
			$a_qaAccess =	$_POST['hasQaAccess'];
		}
		else
		{
			$a_qaAccess		=	array();
		}
	}
	else
	{
		$a_assignFor	=	array();
		$a_replyAccess	=	array();
		$a_qaAccess		=	array();
	}
	if(empty($a_assignFor) && empty($isDelete))
	{
		$validator ->setError("Please Select At Least One Customer !!");
	}
	$dataValid	 =	$validator ->isDataValid();
	if($dataValid)
	{
		if($isDelete == 0)
		{
			foreach($a_assignFor as $key=>$assignForCustomer)
			{
				if(!empty($a_replyAccess) || !empty($a_qaAccess))
				{
				
					$hasReplyAccess	=	$a_replyAccess[$assignForCustomer];
					$hasQaAccess	=	$a_qaAccess[$assignForCustomer];
					if(empty($hasReplyAccess))
					{
						$hasReplyAccess	=	0;
					}
					if(empty($hasQaAccess))
					{
						$hasQaAccess	=	0;
					}
					if(!empty($hasReplyAccess) || !empty($hasQaAccess))
					{
						dbQuery("INSERT INTO pdf_clients_employees SET customerId=$assignForCustomer,employeeId=$employeeId,hasReplyAccess=$hasReplyAccess,hasQaAccess=$hasQaAccess");	
					}
				}
			}
			dbQuery("UPDATE employee_details SET hasPdfAccess=1,receivePdfEmails=$receivePdfEmails,maximumOrdersAccept=$maximumOrdersAccept WHERE employeeId=$employeeId");

			ob_clean();
			header("Location:".SITE_URL_EMPLOYEES."/assign-employee-pdf.php?ID=$employeeId&success=1");
			exit();
		}
		else
		{
			dbQuery("DELETE FROM pdf_clients_employees WHERE employeeId=$employeeId");

			dbQuery("UPDATE employee_details SET hasPdfAccess=0,receivePdfEmails=0,maximumOrdersAccept=$maximumOrdersAccept WHERE employeeId=$employeeId");

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/assign-pdf-clients.php?search=$employeeName");
			exit();
		}
			
	}
	else
	{
		echo $errorMsg	 =	$validator ->getErrors();
	}
}	
include($form);
include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>