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
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$month				=	date('m');
	$year				=	date('Y');
	$employeeId			=	0;
	$whereClause		=	"WHERE hasPdfAccess=1 AND isActive=1";
	$andClause			=	"";
	$orderBy			=	"firstName";
	$text				=	"";
	$text1				=	"";
	$totalReplyOrders	=	0;
	$totalQaOrders		=	0;
	$replyRate			=	0;
	$qaRate				=	0;
	$grandTotal			=	0;

	if(isset($_REQUEST['month']) && isset($_REQUEST['year']))
	{
		$month		=	$_REQUEST['month'];
		$year		=	$_REQUEST['year'];
	}
	$monthText		=	$a_month[$month];
	$text		    =	$monthText.",".$year;
	$queryString	=	"&month=".$month."&year=".$year;
	if(isset($_REQUEST['employeeId']))
	{
		$employeeId	=	$_REQUEST['employeeId'];
		if($employeeName	=	$employeeObj->getEmployeeName($employeeId))
		{
			$text1		    =	" For Employee - ".$employeeName;
			$andClause		=	" AND employeeId=$employeeId";
		}
	}
?>
<script type="text/javascript">
function openEditWidow(employeeId,month,year)
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/work-for-pdf-customers.php?ID="+employeeId+"&month="+month+"&year="+year;
	prop = "toolbar=no,scrollbars=yes,width=650,height=220,top=100,left=100";
	window.open(path,'',prop);
}

function openEditWidow1(employeeId,month,year)
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/work-qa-pdf-customers.php?ID="+employeeId+"&month="+month+"&year="+year;
	prop = "toolbar=no,scrollbars=yes,width=650,height=220,top=100,left=100";
	window.open(path,'',prop);
}
</script>
<table width="98%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="2" class='heading'>VIEW SALARY FROM ORDERS DONE ON <?php echo $text.$text1;?></td>
	</tr>
</table>
<br>
<form name="getSalaryFor" action="" method="GET">
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
			<td width="20%" valign="top" class="title1">Employee : 
				<select name="employeeId">
				<option value="0">All</option>
					<?php
						if($result	=	$employeeObj->getAllPdfEmployees())
						{
							while($row	=	mysql_fetch_assoc($result))
							{
								$t_employeeId	=	$row['employeeId'];
								$firstName		=	$row['firstName'];
								$lastName		=	$row['lastName'];

								$employeeName	=	$firstName." ".$lastName;
								$employeeName	=	ucwords($employeeName);

								$select			=	"";
								if($t_employeeId == $employeeId)
								{
									$select		=	"selected";
								}

								echo  "<option value='$t_employeeId' $select>$employeeName</option>";
							}
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
	if(isset($_REQUEST['recNo']))
	{
		$recNo		    =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo	=	0;
	}
	
	$start					  =	0;
	$recsPerPage	          =	50;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"employee_details";
	$pagingObj->selectColumns = "employeeId,firstName,lastName";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/view-all-pdf-slaray.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
		$totalOrders	=	mysql_affected_rows();
?>
<tr>
	<td colspan="10">
		<hr size="1" width="100%" color="#bebebe">
	</td>
</tr>
<tr>
	<td width="4%" class="smalltext2"><b>Sr No</b></td>
	<td width="16%" class="smalltext2"><b>Employee</b></td>
	<td width="12%" class="smalltext2"><b>Total Replies Orders</b></td>
	<td width="11%" class="smalltext2"><b>Money Per Orders</b></td>
	<td width="13%" class="smalltext2"><b>Total For Reply Orders</b></td>
	<td width="4%" class="smalltext2">&nbsp;</td>
	<td width="10%" class="smalltext2"><b>Total QA Orders</b></td>
	<td width="11%" class="smalltext2"><b>Money Per Orders</b></td>
	<td width="12%" class="smalltext2"><b>Total For QA Orders</b></td>
	<td class="smalltext2"><b>Grand Total</b></td>
</tr>
<tr>
	<td colspan="10">
		<hr size="1" width="100%" color="#bebebe">
	</td>
</tr>
<?php
	$i=$recNo;
	while($row	=   mysql_fetch_assoc($recordSet))
	{
		$i++;
		$employeeId					=	$row['employeeId'];
		$firstName					=	$row['firstName'];
		$lastName					=	$row['lastName'];

		$employeeName				=	$firstName." ".$lastName;
		$employeeName				=	ucwords($employeeName);

		$totalReplyOrders			=	0;
		$totalQaOrders				=	0;
		$replyRate					=	0;
		$qaRate						=	0;
		$grandTotal					=	0;

		$totalReplyOrders	=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders WHERE acceptedBy=$employeeId AND status=2 AND MONTH(orderAddedOn)=$month AND YEAR(orderAddedOn)=$year"),0);
		if(empty($totalReplyOrders))
		{
			$totalReplyOrders	=	0;
		}
		$totalQaOrders	=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders_reply WHERE hasQaDone=1 AND qaDoneBy=$employeeId AND MONTH(qaDoneOn)=$month AND YEAR(qaDoneOn)=$year"),0);
		if(empty($totalQaOrders))
		{
			$totalQaOrders	=	0;
		}

		$query	=	"SELECT * FROM pdf_employees_rate WHERE employeeId=$employeeId AND MONTH(rateValidFrom) >= $month AND YEAR(rateValidFrom) >= $year AND $month <= MONTH(rateValidTo) AND $year <= YEAR(rateValidTo) AND isActiveRate=0 ORDER BY pdfRateId DESC LIMIT 1";
		$result	=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			$row		=	mysql_fetch_assoc($result);
			$replyRate	=	$row['orderRate'];
			$qaRate		=	$row['orderQaRate'];
		}
		else
		{
			$query	=	"SELECT * FROM pdf_employees_rate WHERE employeeId=$employeeId AND isActiveRate=1 ORDER BY pdfRateId DESC LIMIT 1";
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
<tr>
	<td class="text2" align="center" valign="top"><b><?php echo $i;?>.</b></td>
	<td class="smalltext2" valign="top">
		<b><?php echo $employeeName;?></b>
	</td>
	<td class="text2" align="center" valign="top"><b>	
		<?php 
			if(!empty($totalReplyOrders))
			{
				echo "<a href='javascript:openEditWidow($employeeId,$month,$year)' class='link_style10'>$totalReplyOrders</a>";
			}
			else
			{
				echo "0";	
			}
		?>
	</b></td>
	<td class="text2" align="center" valign="top"><b>
		<?php 
			echo $moneyPerReplyOrders;
		?>
	</b></td>
	<td class="text2" align="center">
		<b>
			<?php 
				echo $totalForReplyOrders;
			?>
		</b>
	</td>
	<td class="text2">&nbsp;</td>
	<td class="text2" align="center" valign="top">
		<b>
			<?php 
				if(!empty($totalQaOrders))
				{
					echo "<a href='javascript:openEditWidow1($employeeId,$month,$year)' class='link_style10'>$totalQaOrders</a>";
				}
				else
				{
					echo "0";	
				}
			?>
		</b>
	</td>
	<td class="text2" align="center" valign="top"><b><?php echo $moneyPerQaOrders;?></b></td>
	<td class="text2" align="center" valign="top"><b><?php echo $totalForQaOrders;?></b></td>
	<td class="text2" valign="top"><b><?php echo $grandTotal;?></td>
</tr>
<tr>
	<td colspan="10">
		<hr size="1" width="100%" color="#bebebe">
	</td>
</tr>
<?php
	}
	echo "<tr><td colspan='10' align='right'><table width='90%' border='0' ><tr height=20><td align=right><font color='#000000'>";
	$pagingObj->displayPaging($queryString);
	echo "<b>Total Records : " . $totalRecords . "</font></b></td></tr></table></td></tr>";
}
else
{
	echo "<tr><td height='30'></td></tr><tr><td class='error' align='center'><b>NO PDF EMPLOYEE AVAILABLE !!</b></td></tr><tr><td height='250'></td></tr><tr>";
}

?>
</table>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>