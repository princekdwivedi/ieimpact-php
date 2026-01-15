<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();
	$employeeId					=	0;
	$salaryeId					=	0;
	$employeeName				=	"";
	$month						=	0;
	$year						=	0;
	$monthText					=	"";
	$departmentText				=	"";
	if(!$s_hasManagerAccess)
	{
		echo "<script>window.close();</script>";
	}
	if(isset($_GET['employeeId']) && isset($_GET['month']) && isset($_GET['year']) && isset($_GET['salaryId']))
	{
		$employeeId				=	$_GET['employeeId'];
		$month					=	$_GET['month'];
		$year					=	$_GET['year'];
		$salaryId				=	$_GET['salaryId'];
		if($month < 10)
		{
			$month				=	"0".$month;
		}
		$employeeName			=	$employeeObj->getEmployeeName($employeeId);
		$monthText				=	$a_month[$month];
		if(!empty($employeeId) && !empty($month) && !empty($year) &&  !empty($salaryId))
		{
			$query				=	"SELECT * FROM employee_salary_given WHERE employeeId=$employeeId AND month=$month AND year=$year AND departmentId=3 AND salaryId=$salaryId";
			$result			=   dbQuery($query);
			if(mysql_num_rows($result))
			{
				$row			=   mysql_fetch_assoc($result);
				$salaryGiven	=	$row['salaryGiven'];
				$remarks		=	$row['remarks'];
				$isPaidSalary	=	$row['isPaidSalary'];
				$fixedSalary	=   $row['fixedSalary'];
				$totalMoney		=   $row['totalMoney'];
				$tdsPercentage	=   $row['tdsPercentage'];
				$tdsDeduction	=   $row['tdsDeduction'];
				$givenThrough	=   $row['givenThrough'];
				$remarks		=   $row['remarks'];
				$transactionId	=   $row['transactionId'];
				$chequeNo		=   $row['chequeNo'];
				$checkBank		=   $row['checkBank'];
				$givenOn		=	$row['givenOn'];
				$checkDate		=   $row['checkDate'];
				$pfMoney		=   $row['pfMoney'];
				$text			=	"Salary slip of ".$employeeName." for ".$monthText.",".$year;
				
				$totalOrdersFixedMoney	=	$fixedSalary+$totalMoney;
			}
			else
			{
				echo "<script>window.close();</script>";
			}
		}
		else
		{
			echo "<script>window.close();</script>";
		}
	}
	else
	{
		echo "<script>window.close();</script>";
	}
?>
<script type="text/javascript">
function reflectChange()
{
	window.opener.location.reload();
}
</script>
<html>
<head>
<title>
	<?php echo $text;?>
</title>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
<style type="text/css" media="screen"> 
		#screen {} 
		#print { 
				display: none; 
		} 
</style> 
<style type="text/css" media="print"> 
		#screen { 
				display: none; 
		} 
		#print {} 
		 
</style>
</head>
<body>
<center>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top" width="40%">
				<img src="<?php echo SITE_URL;?>/images/templatemo_logo_PNG_source.png" border="0" title="Innovation. Excellence. i.e. IMPACT" alt="Innovation. Excellence. i.e. IMPACT">
			</td>
			<td class="textstyle1" valign="top">
				<b>
					ieIMPACT Microsystems Pvt. Ltd.<br> 
					SCO 102, 2nd Floor, Sector 47-C,<br>
					Chandigarh (UT) 160047<br>
					INDIA
				</b>
			</td>
		</tr>
	</table>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="textstyle1" colspan="3">
				<b><?php echo $text;?></b>
			</td>
		</tr>
		<tr>
			<td height="5" colspan="3"></td>
		</tr>
		<tr>
			<td width="25%" class="textstyle1">
				Department
			</td>
			<td width="2%" class="textstyle1">
				:
			</td>
			<td class="error">
				<b>PDF</b>
			</td>
		</tr>
		<tr>
			<td height="5" colspan="3"></td>
		</tr>
		<tr>
			<td class="textstyle1">
				Total Salary
			</td>
			<td class="textstyle1">
				:
			</td>
			<td class="textstyle1">
				<b><?php echo $totalOrdersFixedMoney;?></b>/-Rs.
			</td>
		</tr>
		<tr>
			<td height="5" colspan="3"></td>
		</tr>
		<tr>
			<td class="textstyle1">
				TDS Deduction
			</td>
			<td class="textstyle1">
				:
			</td>
			<td class="textstyle1">
				<b><?php echo $tdsDeduction;?></b>/-Rs.
			</td>
		</tr>
		<tr>
			<td height="5" colspan="3"></td>
		</tr>
		<tr>
			<td class="textstyle1">
				PF Deduction
			</td>
			<td class="textstyle1">
				:
			</td>
			<td class="textstyle1">
				<b><?php echo $pfMoney;?></b>/-Rs.
			</td>
		</tr>
		<tr>
			<td height="5" colspan="3"></td>
		</tr>
		<tr>
			<td class="textstyle1">
				Net Salary
			</td>
			<td class="textstyle1">
				:
			</td>
			<td class="textstyle1">
				<b><?php echo $salaryGiven;?></b>/-Rs.
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td class="textstyle">
				[<?php echo convert_number($salaryGiven);?> only/-]
			</td>
		</tr>
		<tr>
			<td height="5" colspan="3"></td>
		</tr>
		<tr>
			<td class="textstyle1">
				Salary Paid By
			</td>
			<td class="textstyle1">
				:
			</td>
			<td class="textstyle1">
				<b><?php echo $a_salaryPaidTrough[$givenThrough];?></b>
			</td>
		</tr>
		<tr>
			<td height="5" colspan="3"></td>
		</tr>
		<tr>
			<td class="textstyle1">
				Salary Paid On
			</td>
			<td class="textstyle1">
				:
			</td>
			<td class="textstyle1">
				<b><?php echo showDate($givenOn);?></b>
			</td>
		</tr>
		<tr>
			<td height="25" colspan="3"></td>
		</tr>
		<tr>
			<td class="textstyle1">
				Rceciver Signature
			</td>
			<td class="textstyle1">
				:
			</td>
			<td class="textstyle1">
				_____________________
			</td>
		</tr>
	</table>
<br><br>
	<div id="screen">
		<?php
			echo "<br><a href='javascript:window.print()'  class='link_style2'><b>Print This Page</b></a><br><br>";		
		?>
	</div>
<br>
	<div id="screen">
		<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
	</div>
</center>
</body>
</html>