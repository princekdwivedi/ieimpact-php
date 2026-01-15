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
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	$pagingObj					=  new Paging();
	$orderObj					=  new orders();

	$month				=	date('m');
	$year				=	date('Y');
	$text				=	"";
	$totalReplyOrders	=	0;
	$totalQaOrders		=	0;
	$replyRate			=	0;
	$qaRate				=	0;
	$grandTotal			=	0;

	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
	}
	$monthText		=	$a_month[$month];
	$text		    =	$monthText.",".$year;

	$totalReplyOrders	=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders WHERE acceptedBy=$s_employeeId AND status=2 AND MONTH(orderAddedOn)=$month AND YEAR(orderAddedOn)=$year"),0);
	if(empty($totalReplyOrders))
	{
		$totalReplyOrders	=	0;
	}
	$totalQaOrders	=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders_reply WHERE hasQaDone=1 AND qaDoneBy=$s_employeeId AND MONTH(qaDoneOn)=$month AND YEAR(qaDoneOn)=$year"),0);
	if(empty($totalQaOrders))
	{
		$totalQaOrders	=	0;
	}

	$query	=	"SELECT * FROM pdf_employees_rate WHERE employeeId=$s_employeeId AND MONTH(rateValidFrom) >= $month AND YEAR(rateValidFrom) >= $year AND $month <= MONTH(rateValidTo) AND $year <= YEAR(rateValidTo) AND isActiveRate=0 ORDER BY pdfRateId DESC LIMIT 1";
	$result	=	dbQuery($query);
	if(mysql_num_rows($result))
	{
		$row		=	mysql_fetch_assoc($result);
		$replyRate	=	$row['orderRate'];
		$qaRate		=	$row['orderQaRate'];
	}
	else
	{
		$query	=	"SELECT * FROM pdf_employees_rate WHERE employeeId=$s_employeeId AND isActiveRate=1 ORDER BY pdfRateId DESC LIMIT 1";
		$result	=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			$row		=	mysql_fetch_assoc($result);
			$replyRate	=	$row['orderRate'];
			$qaRate		=	$row['orderQaRate'];
		}
	}
	if(empty($replyRate))
	{
		$moneyPerReplyOrders	=	"Not Yet Added";
		$totalForReplyOrders	=	"0";
	}
	else
	{
		$moneyPerReplyOrders	=	$replyRate;
		if(!empty($totalReplyOrders))
		{
			$totalForReplyOrders	=	$totalReplyOrders*$moneyPerReplyOrders;
			$totalForReplyOrders	=	round($totalForReplyOrders);
		}
		else
		{
			$totalForReplyOrders	=	"0";
		}
	}
	if(empty($qaRate))
	{
		$moneyPerQaOrders		=	"Not Yet Added";
		$totalForQaOrders		=	"0";
	}
	else
	{
		$moneyPerQaOrders	=	$qaRate;
		if(!empty($totalQaOrders))
		{
			$totalForQaOrders	=	$totalQaOrders*$moneyPerQaOrders;
			$totalForQaOrders	=	round($totalForQaOrders);
		}
		else
		{
			$totalForQaOrders	=	"0";
		}
	}
	$grandTotal	=	$totalForReplyOrders+$totalForQaOrders;
?>
<table width="98%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="2" class='heading'>VIEW YOUR SALARY FROM ORDERS DONE FOR <?php echo $text;?></td>
	</tr>
</table>
<br>
<form name="getSalaryFor" action="" method="POST">
	<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
		<tr>
			<td width="15%" class="smalltext2" valign="top"><b>View Salary For 
			<td width="2%" class="smalltext2" valign="top"><b>:</b></td>
			<td width="15%" valign="top" class="title1">
				<select name="month">
					<?php
						foreach($a_month as $key=>$value)
						{
							$select	  =	"";
							if($month == $key)
							{
								$select	  =	"selected";
							}

							echo "<option value='$key' $select>$value</option>";
						}
					?>
				</select>&nbsp;&nbsp;
				<select name="year">
					<?php
						$sYear	=	"2010";
						$eYear	=	date("Y")+1;
						for($i=$sYear;$i<=$eYear;$i++)
						{
							$select			=	"";
							if($year  == $i)
							{
								$select		=	"selected";
							}
							echo "<option value='$i' $select>$i</option>";
						}
					?>
				</select>
			</td>
			<td valign="top">
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
	</table>
</form>
<br>
<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
<?php
	if(!empty($totalReplyOrders) || !empty($totalQaOrders))
	{
?>
<tr>
	<td colspan="8">
		<hr size="1" width="100%" color="#bebebe">
	</td>
</tr>
<tr>
	<td width="13%" class="smalltext2"><b>Total Replies Orders</b></td>
	<td width="13%" class="smalltext2"><b>Money Per Orders</b></td>
	<td width="13%" class="smalltext2"><b>Total For Reply Orders</b></td>
	<td width="8%" class="smalltext2">&nbsp;</td>
	<td width="13%" class="smalltext2"><b>Total QA Orders</b></td>
	<td width="13%" class="smalltext2"><b>Money Per Orders</b></td>
	<td width="13%" class="smalltext2"><b>Total For QA Orders</b></td>
	<td class="smalltext2"><b>Grand Total</b></td>
</tr>
<tr>
	<td colspan="8">
		<hr size="1" width="100%" color="#bebebe">
	</td>
</tr>
<tr>
	<td class="heading1" align="center"><?php echo $totalReplyOrders;?></td>
	<td class="heading1" align="center"><?php echo $moneyPerReplyOrders;?></td>
	<td class="heading1" align="center"><?php echo $totalForReplyOrders;?></td>
	<td class="heading1">&nbsp;</td>
	<td class="heading1" align="center"><?php echo $totalQaOrders;?></td>
	<td class="heading1" align="center"><?php echo $moneyPerQaOrders;?></td>
	<td class="heading1" align="center"><?php echo $totalForQaOrders;?></td>
	<td class="heading1"><?php echo $grandTotal;?></td>
</tr>
<tr>
	<td colspan="8">
		<hr size="1" width="100%" color="#bebebe">
	</td>
</tr>
<tr>
	<td height="200"></td>
</tr>
<?php
	}
	else
	{
		echo "<tr><td height='30'></td></tr><tr><td class='error' align='center'><b>NO REPLY FILES AND QA DONE EXISTS FOR THIS MONTH !!</b></td></tr><tr><td height='250'></td></tr><tr>";
	}
?>
</table>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>