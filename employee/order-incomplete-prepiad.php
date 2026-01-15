<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES .   "/includes/new-top.php");
	include(SITE_ROOT_EMPLOYEES .   "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES .   "/classes/employee.php");
	include(SITE_ROOT			.   "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	.   "/includes/common-array.php");
	$employeeObj				=   new employee();
	include(SITE_ROOT_EMPLOYEES	.   "/includes/set-variables.php");

	if(isset($_session['invalid_order_address']) && isset($_session['invalid_customer_name']))
	{
		$invalid_order_address	=	$_session['invalid_order_address'];
		$invalid_customer_name	=	$_session['invalid_customer_name'];

	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
?>
	<table width="98%" align="center" border="1" cellpadding="0" cellspacing="0">
		<tr>
			<td height="200" valign="top" class="textstyle1">
				Due to payment problem this order is removed. Please let Hemant and admin/employee manager with following details : <br /><br />
				<b>Customer Name : </b><?php echo $invalid_customer_name;?><br />
				<b>Order Address : </b><?php echo $invalid_order_address;?><br />
			</td>
		</tr>
	</table>
<?php
	unset($_session['invalid_order_address']);
	unset($_session['invalid_customer_name']);
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>
