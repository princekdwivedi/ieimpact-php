<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	include(SITE_ROOT			. "/classes/common.php");
	
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$pagingObj		=	new Paging();
	$employeeObj	=	new employee();
	$commonObj		=   new common();
	$checked		=	"";
	$checked1		=	"";
	$checked2		=	"";
	$showDiv		=	"none";
	$showDiv1		=	"none";
	$showDiv2		=	"none";
	$a_employeeId	=	array();
	$searchOrder	=	"";
	$searchCustomer	=	"";
	$showForm		=	false;
	$errorMsg		=	"";
	$errorMsg1		=	"";
	$description	=	"";
	$a_messageTo	=	array("1"=>"Sending message to PDF employee to","Sending message by an order in","Sending message for PDF customer order of");
	$messageToText	=	"";
	
	$s_sendingMessageTo			=	0;
	$s_sendingMessageToEmp		=	"";
	$s_sendingMessageOrder		=	"";
	$s_sendingMessageCustomer	=	"";

	if(isset($_SESSION['sendingMessageTo']))
	{
		$s_sendingMessageTo			=	$_SESSION['sendingMessageTo'];
	}
	if(isset($_SESSION['sendingMessageToEmp']))
	{
		$s_sendingMessageToEmp		=	$_SESSION['sendingMessageToEmp'];
	}
	if(isset($_SESSION['sendingMessageOrder']))
	{
		$s_sendingMessageOrder		=	$_SESSION['sendingMessageOrder'];
	}
	if(isset($_SESSION['sendingMessageCustomer']))
	{
		$s_sendingMessageCustomer	=	$_SESSION['sendingMessageCustomer'];
	}

	if(!empty($s_sendingMessageTo))
	{
		$showForm				=	true;
		$messageToText			=	$a_messageTo[$s_sendingMessageTo];
		if(!empty($s_sendingMessageToEmp))
		{
			$checked		=	"checked";
			$checked1		=	"";
			$checked2		=	"";
			$showDiv		=	"";
			$showDiv1		=	"none";
			$showDiv2		=	"none";
			$a_employeeId	=	explode(",",$s_sendingMessageToEmp);
		}
		if(!empty($s_sendingMessageOrder))
		{
			$checked		=	"";
			$checked1		=	"checked";
			$checked2		=	"";
			$showDiv		=	"none";
			$showDiv1		=	"";
			$showDiv2		=	"none";
		}
		if(!empty($s_sendingMessageCustomer))
		{
			$checked		=	"";
			$checked1		=	"";
			$checked2		=	"checked";
			$showDiv		=	"none";
			$showDiv1		=	"none";
			$showDiv2		=	"";
		}
	}
	//pr($_SESSION);


	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		if(isset($_POST['messageTo']))
		{
			$messageTo		=	$_POST['messageTo'];
			if($messageTo	==	1)
			{
				if(isset($_POST['employeeId']))
				{
					$a_employeeId	=	$_POST['employeeId'];
				}
				else
				{
					$errorMsg	.=	"Please select an employee !!";
				}

				$checked		=	"checked";
				$checked1		=	"";
				$checked2		=	"";
				$showDiv		=	"";
				$showDiv1		=	"none";
				$showDiv2		=	"none";
			}
			elseif($messageTo	==	2)
			{
				if(empty($searchOrder))
				{
					$errorMsg	.=	"Please search an order !!";
				}

				$checked		=	"";
				$checked1		=	"checked";
				$checked2		=	"";
				$showDiv		=	"none";
				$showDiv1		=	"";
				$showDiv2		=	"none";
			}
			elseif($messageTo	==	3)
			{
				if(empty($searchCustomer))
				{
					$errorMsg	.=	"Please search a customer !!";
				}

				$checked		=	"";
				$checked1		=	"";
				$checked2		=	"checked";
				$showDiv		=	"none";
				$showDiv1		=	"none";
				$showDiv2		=	"";
			}
		}
		else
		{
			$errorMsg	.=	"Please select an option !!";
		}
		if(empty($errorMsg))
		{
			$_SESSION['sendingMessageTo']	=	$messageTo;
			if($messageTo	==	1)
			{
				$messageToEmp	=	implode(",",$a_employeeId);

				$_SESSION['sendingMessageToEmp']	=	$messageToEmp;
				unset($_SESSION['sendingMessageOrder']);
				unset($_SESSION['sendingMessageCustomer']);
			}
			if($messageTo	==	2)
			{
				$_SESSION['sendingMessageOrder']	=	$searchOrder;
				unset($_SESSION['sendingMessageToEmp']);
				unset($_SESSION['sendingMessageCustomer']);
			}
			if($messageTo	==	3)
			{
				$_SESSION['sendingMessageCustomer']	=	$searchCustomer;
				unset($_SESSION['sendingMessageToEmp']);
				unset($_SESSION['sendingMessageOrder']);
			}

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/message-pdf-employees.php");
			exit();
		}
	}

	

?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
</script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" />

<script type="text/javascript">
$().ready(function() {
	$("#orderAddress").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/employees-pdf-orders.php", {width: 295,selectFirst: false});
});

$().ready(function() {
	$("#employeeCustomers").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/employees-customers.php", {width: 295,selectFirst: false});
});

function showMessageTo(flag)
{
	if(flag == 1)
	{
		document.getElementById('showEmployee').style.display   = 'inline';
		document.getElementById('showOrder').style.display		= 'none';
		document.getElementById('showCustomer').style.display   = 'none';
	}
	else if(flag == 2)
	{
		document.getElementById('showEmployee').style.display   = 'none';
		document.getElementById('showOrder').style.display		= 'inline';
		document.getElementById('showCustomer').style.display   = 'none';
	}
	else if(flag == 3)
	{
		document.getElementById('showEmployee').style.display   = 'none';
		document.getElementById('showOrder').style.display		= 'none';
		document.getElementById('showCustomer').style.display   = 'inline';
	}
	else
	{
		document.getElementById('showEmployee').style.display   = 'none';
		document.getElementById('showOrder').style.display		= 'none';
		document.getElementById('showCustomer').style.display   = 'none';
	}
}
</script>
<form name="sendMessageEmployee" action=""  method="POST">
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='heading' colspan="3">Send Message To PDF Employee</td>
	</tr>
	<tr>
		<td colspan="3" height="10" align="center" class="error2">
			<?php
				if(isset($_GET['success']) && $_GET['success'] == 1)
				{
					echo "<b>Successfully sent message !!</b>";
				}
			?>
		</td>
	</tr>
	<tr>
		<td width="30%" class="textstyle1"><input type="radio" name="messageTo" value="1" <?php echo $checked;?> onclick="return showMessageTo(1)">Send message to PDF employee</td>
		<td width="30%" class="textstyle1"><input type="radio" name="messageTo" value="2" <?php echo $checked1;?> onclick="return showMessageTo(2)">Send message by an order</td>
		<td class="textstyle1"><input type="radio" name="messageTo" value="3" <?php echo $checked2;?> onclick="return showMessageTo(3)">Send message by a PDF customer</td>
	</tr>
	
	<tr>
		<td colspan="3" class="error2"><?php echo $errorMsg;?></td>
	</tr>
	<tr>
		<td colspan="3">
			<div id="showEmployee" style="display:<?php echo $showDiv;?>">
				<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
					<tr>
						<td width="15%" class="textstyle1" valign="top"><b>Select Employee</b></td>
						<td width="2%" class="textstyle1" valign="top">:</td>
						<td>
							<select name="employeeId[]" multiple style="height:100px;">
								<?php
									if($result	=	$employeeObj->getAllPdfEmployees())
									{
										while($row	=	mysql_fetch_assoc($result))
										{
											$t_employeeId	=	$row['employeeId'];
											$firstName		=	stripslashes($row['firstName']);
											$lastName		=	stripslashes($row['lastName']);

											$employeeName	=	$firstName." ".$lastName;
											$employeeName	=	ucwords($employeeName);

											$select			=	"";
											if(in_array($t_employeeId, $a_employeeId))
											{
												$select		=	"selected";
											}

											echo  "<option value='$t_employeeId' $select>$employeeName</option>";
										}
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
						<td class="smalltext7">
							[Use Ctrl+Select to select multiple employees]
						</td>
					</tr>
				</table>
			</div>
			<div id="showOrder" style="display:<?php echo $showDiv1;?>">
				<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
					<tr>
						<td width="15%" class="textstyle1"><b>Search an Order</b></td>
						<td width="2%" class="textstyle1"><b>:</b></td>
						<td>
							<input type='text' name="searchOrder" size="48" value="<?php echo $s_sendingMessageOrder;?>" id="orderAddress">
						</td>
					</tr>
				</table>
			</div>
			<div id="showCustomer" style="display:<?php echo $showDiv2;?>">
				<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
					<tr>
						<td width="15%" class="textstyle1"><b>Search a customer</b></td>
						<td width="2%" class="textstyle1"><b>:</b></td>
						<td>
							<input type='text' name="searchCustomer" size="48" value="<?php echo $s_sendingMessageCustomer;?>" id="employeeCustomers">
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
			<input type='hidden' name='formSubmitted' value='1'>
		</td>
	</tr>
</table>
</form>
<?php
	
	if($showForm)
	{
		if(isset($_REQUEST['messageSubmitted']))
		{
			extract($_REQUEST);
			$a_selEmp		=	array();
			$a_selOdr		=	array();
			$a_selCus		=	array();
			if(isset($_POST['selEmp']))
			{
				$a_selEmp		=	$_POST['selEmp'];
			}
			if(isset($_POST['selOdr']))
			{
				$a_selOdr		=	$_POST['selOdr'];
			}
			if(isset($_POST['selCus']))
			{
				$a_selCus		=	$_POST['selCus'];
			}
			$description		=	$_POST['description'];

			if($s_sendingMessageTo == 1 && empty($a_selEmp))
			{
				$errorMsg1		=	"Please select employees !!";
			}
			elseif($s_sendingMessageTo == 2 && empty($a_selOdr))
			{
				$errorMsg1		=	"Please select orders !!";
			}
			elseif($s_sendingMessageTo == 3 && empty($a_selCus))
			{
				$errorMsg1		=	"Please select customers !!";
			}
			if(empty($description))
			{
				$errorMsg1		.=	"<br>Please enter message !!";
			}
			if(empty($errorMsg1))
			{
				if($s_sendingMessageTo == 1 && !empty($a_selEmp))
				{
					foreach($a_selEmp as $k=>$v)
					{
						if(!empty($v))
						{
							dbQuery("INSERT INTO employee_order_customer_messages SET messageType=1,messageFor=$v,message='$description',addedOn='".CURRENT_DATE_INDIA."',messageBy=$s_employeeId");
						}
					}
				}
				elseif($s_sendingMessageTo == 2 && !empty($a_selOdr))
				{
					foreach($a_selOdr as $k=>$v)
					{
						if(!empty($v))
						{
							dbQuery("INSERT INTO employee_order_customer_messages SET messageType=2,messageFor=$v,message='$description',addedOn='".CURRENT_DATE_INDIA."',messageBy=$s_employeeId");
						}
					}
				}
				elseif($s_sendingMessageTo == 3 && !empty($a_selCus))
				{
					foreach($a_selCus as $k=>$v)
					{
						if(!empty($v))
						{
							dbQuery("INSERT INTO employee_order_customer_messages SET messageType=3,messageFor=$v,message='$description',addedOn='".CURRENT_DATE_INDIA."',messageBy=$s_employeeId");
						}
					}
				}
				unset($_SESSION['sendingMessageTo']);
				unset($_SESSION['sendingMessageToEmp']);
				unset($_SESSION['sendingMessageOrder']);
				unset($_SESSION['sendingMessageCustomer']);


				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES."/message-pdf-employees.php?success=1");
				exit();
			}
		}
?>
<script type="text/javascript" src="<?php echo SITE_URL?>/script/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL?>/script/tinymce.js"></script>
<form name="sendMessage" action=""  method="POST">
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='heading' colspan="8"><?php echo $messageToText;?></td>
	</tr>
	<tr>
		<td colspan="8" class="error2"><?php echo $errorMsg1;?></td>
	</tr>
	<?php
		if(!empty($s_sendingMessageTo) && $s_sendingMessageTo	==	1)
		{	
			foreach($a_employeeId as $k=>$v)
			{
				$empName	=	$employeeObj->getEmployeeName($v);
	?>
	<tr>
		<td width="5%"><input type="checkbox" name="selEmp[]" value="<?php echo $k;?>"></td>
		<td class="error"><?php echo $empName;?></td>
	</tr>
	<?php
			}
		}
		elseif(!empty($s_sendingMessageTo) && $s_sendingMessageTo	==	2)
		{	
			$query	=	"SELECT orderId,orderAddress,memberId,orderAddedOn,status FROM members_orders WHERE orderAddress='$s_sendingMessageOrder'";
			$result	=	dbQuery($query);
			if(mysql_num_rows($result))
			{
				
	?>
	<tr>
		<td width="5%">&nbsp;</td>
		<td class="smalltext2" width="40%">Order Address</td>
		<td class="smalltext2" width="25%">Customer Name</td>
		<td class="smalltext2" width="10%">Order On</td>
		<td class="smalltext2">Status</td>
	</tr>
	<tr>
		<td colspan="5">
			<hr size="1" width="100%" color="#333333">
		</td>
	</tr>
	<?php
				while($row  = mysql_fetch_assoc($result))
				{
					$orderId		=	$row['orderId'];
					$orderAddress	=	stripslashes($row['orderAddress']);
					$memberId		=	$row['memberId'];
					$orderAddedOn	=	showDate($row['orderAddedOn']);
					$status			=	$row['status'];
					$customerName	=	$commonObj->getMemberName($memberId);
					$statusText		=	"New";
					if($status		==	1)
					{
						$statusText	=	"On-Process";
					}
					elseif($status		==	2)
					{
						$statusText	=	"Completed";
					}
	?>
	<tr>
		<td><input type="checkbox" name="selOdr[]" value="<?php echo $orderId;?>"></td>
		<td class="error"><?php echo $orderAddress;?></td>
		<td class="textstyle1"><?php echo $customerName;?></td>
		<td class="textstyle1"><?php echo $orderAddedOn;?></td>
		<td class="textstyle1"><?php echo $statusText;?></td>
	</tr>
	<?php
					}
				}
			}
			elseif(!empty($s_sendingMessageTo) && $s_sendingMessageTo	==	3)
			{	
				$pos			=	strpos($s_sendingMessageCustomer, " ");
				if($pos == true)
				{
					
					$firstName		=	substr($s_sendingMessageCustomer,0,$pos);
					$lastName		=	substr($s_sendingMessageCustomer,$pos+1);

					$whereClause	=	"WHERE (firstname LIKE '%$firstName%' OR lastName LIKE '%$lastName%')";
				}
				else
				{
					$whereClause	=	"WHERE (firstname LIKE '%$searchCustomer%')";
				}
				$query	=	"SELECT memberId,firstName,lastName FROM members ".$whereClause." AND isActiveCustomer=1";
				$result	=	dbQuery($query);
				if(mysql_num_rows($result))
				{
					while($row  = mysql_fetch_assoc($result))
					{
						$memberId		=	$row['memberId'];
						$firstName		=	stripslashes($row['firstName']);
						$lastName		=	stripslashes($row['lastName']);

						$cusName		=	$firstName." ".$lastName;
		?>
		<tr>
			<td width="5%"><input type="checkbox" name="selCus[]" value="<?php echo $memberId;?>"></td>
			<td class="error"><?php echo $cusName;?></td>
		</tr>
		<?php
				}
			}
		}
	?>
	<tr>
		<td class="textstyle1" colspan="10">Message</td>
	</tr>
	<tr>
		<td colspan="10">
			<textarea name="description" id="elm1" cols="10" rows="20"><?php echo			stripslashes(htmlentities($description,ENT_QUOTES));?>
			</textarea>
		</td>
	</tr>
	<tr>
		<td class="textstyle1" colspan="10" height="10"></td>
	</tr>
	<tr>
		<td class="textstyle1" colspan="10" height="10">
			<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
			<input type='hidden' name='messageSubmitted' value='1'>
		</td>
	</tr>
</table>
</form>
<?php
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>
