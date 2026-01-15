<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/new-top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/includes/send-mail.php");
	$employeeObj				=  new employee();
	$memberObj					=  new members();
	$orderObj					=  new orders();
	$messgeText					=  "SEND";
	$customerEmail				=	"";
	$customerSecondaryEmail		=	"";
	$a_managerEmails			=	array();
	$message					=	"";

	if(isset($_GET['orderId']) && isset($_GET['customerId']))
	{
		$orderId		=	$_GET['orderId'];
		$customerId		=	$_GET['customerId'];
		
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/new-pdf-work.php");
		exit();
	}
	$formSearch					=	SITE_ROOT_EMPLOYEES."/forms/search-general-order-form.php";
?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
<tr>
	<td colspan="8" class="heading1">
		:: INTERNAL EMPLOYEE MESSAGES ::
	</td>
</tr>
<tr>
	<td colspan="8" height="5"></td>
</tr>
</table>
<?php
	include($formSearch);

	include(SITE_ROOT_EMPLOYEES	. "/includes/view-customer-order1.php");

	function findexts($filename) 
	{ 
		$ext        =    "";
		$filename   =    strtolower($filename) ; 
		$a_exts		=	 explode(".",$filename);
		$total		=	 count($a_exts);
		if($total > 1){
			$ext	=	 end($a_exts);		
		}		
		return $ext; 
	} 
	function getFileName($fileName)
	{
		$dotPosition	=  strpos($fileName, "'");
		if($dotPosition == true)
		{
			$fileName	=	stringReplace("'", "", $fileName);
		}
		$doubleDotPosition	  =  strpos($fileName, '"');
		if($doubleDotPosition == true)
		{
			$fileName	=	stringReplace('"', '', $fileName);
		}
		$fileExtPos		=  strrpos($fileName, '.');
		$fileName		=  substr($fileName,0,$fileExtPos);
		
		return $fileName;
	}
?>
<br>
<a name="messages"></a>
<?php
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		$message		=	makeDBSafe($message);
		
		$query	=	"INSERT INTO employee_order_customer_messages SET messageType=1,messageFor=$orderId,message='$message',messageBy=$s_employeeId,addedOn='".CURRENT_DATE_INDIA."'";
		dbQuery($query);

		dbQuery("UPDATE members_orders SET isHavingInternalMsg=1 WHERE orderId=$orderId");

		$performedTask	=	"Adding Internal Employee Message - ".$message;
				
		$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES ."/internal-emp-msg.php?orderId=$orderId&customerId=$customerId&selectedTab=8");
		exit();
	}
?>
<script type="text/javascript">
function checkValidMessage()
{
	form1	=	document.sendEmployeeMessage;
	if(form1.message.value == "" || form1.message.value == "Enter Your Message Here")
	{
		alert("Please Enter Your Message !!");
		form1.message.focus();
		return false;
	}
}
function textCounter(field,countfield,maxlimit)
{
	if(field.value.length > maxlimit)
	{
		field.value = field.value.substring(0, maxlimit);
	}
	else
	{
		countfield.value = maxlimit - field.value.length;
	}
 }
 </script>
 <br>
 <a name="sendMessages"></a>
<form name="sendEmployeeMessage" action=""  method="POST" onsubmit="return checkValidMessage();">
<table width='100%' align='center' cellpadding='3' cellspacing='0' border='0'>
<tr>
	<td colspan="3" class="text">SEND MESSAGE ON THIS ORDERS (Only for employees)</td>
</tr>
<tr>
	<td colspan="3" height="5"></td>
</tr>
<tr>
	<td valign="top" colspan="3">
		<textarea name="message" rows="7" cols="70" wrap="hard" onKeyDown="textCounter(this.form.message,this.form.remLentext1,1000);" onKeyUp="textCounter(this.form.message,this.form.remLentext1,1000);" onFocus="if(this.value=='Enter Your Message Here') this.value='';" onBlur="if(this.value=='') this.value='Enter Your Message Here';" style="border:1px solid #333333;"><?php echo stripslashes(htmlentities($message,ENT_QUOTES))?></textarea>

		<br><font class="smalltext2">Characters Left: <input type="textbox" readonly name="remLentext1" size=2 value="1000" style="border:0"></font>
	</td>
</tr>
<tr>
	<td height="5" colspan="3"></td>
</tr>
<tr>
	<td colspan="2">
		<input type="submit" name="submit" value="Submit">
		<input type="button" name="submit" onClick="history.back()" value="Back">
		<input type="hidden" name="formSubmitted" value="1">
	</td>
	<td>
		<?php
			include(SITE_ROOT_EMPLOYEES . "/includes/next-previous-order.php");
		?>
	</td>
</tr>
</table>
</form>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>