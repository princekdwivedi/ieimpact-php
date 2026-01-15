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
	$employeeObj				= new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/common.php");
	include(SITE_ROOT			. "/includes/send-mail.php");
	$orderObj					= new orders();
	$commonObj					= new common();
		
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$a_pdfEmployeeWithMaxOrder	=	array();


	$query	=	"SELECT employeeId,fullName,maximumOrdersAccept,shiftType,totalOrderProcessedDone FROM employee_details  WHERE isActive=1 AND hasPdfAccess=1 AND  employeeId NOT IN (3,340,137) ORDER BY maximumOrdersAccept DESC,fullName";
	$result	=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row					=	mysqli_fetch_assoc($result))
		{
			$employeeId				=	$row['employeeId'];
			$fullName				=	stripslashes($row['fullName']);
			$maximumOrdersAccept	=	$row['maximumOrdersAccept'];
			$shiftType				=	$row['shiftType'];
			$totalOrdersDone		=	$row['totalOrderProcessedDone'];

			
			$a_pdfEmployeeWithMaxOrder[$employeeId]	=	$fullName."|".$maximumOrdersAccept."|".$totalOrdersDone."|".$shiftType;
		}
	}
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);

		//pr($_REQUEST);
		$a_maxOrderCanAccept	 =	$_POST['maxOrderCanAccept'];
		$a_shiftType			 =	$_POST['shiftType'];
		if(!empty($a_maxOrderCanAccept))
		{
			foreach($a_maxOrderCanAccept as $employeeId=>$maxCanAccept)
			{
				$shiftType		 =	$a_shiftType[$employeeId];
				
				if(empty($maxCanAccept))
				{
					$maxCanAccept=	0;
				}
				dbQuery("UPDATE employee_details SET maximumOrdersAccept=$maxCanAccept,shiftType=$shiftType WHERE employeeId=$employeeId");
			}
		}

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/add-employees-max-orders.php");
		exit();
	}

?>
<script type="text/javascript">
function checkForNumber()
{
	k = (document.all)?event.keyCode : arguments.callee.caller.arguments[0].which;
	if(k == 8 || k== 0)
	{
		return true;
	}
	if(k >= 48 && k <= 57 )
	{
		return true;
	}
	else if(k == 46)
	{
		return true;
	}
	else
	{
		return false;
	}
 }
</script>
<form  name='assignAutomatically' method='POST' action="" onsubmit="return validAssign();">
	<table cellpadding="2" cellspacing="2" width='80%'align="left" border='0'>
		<tr>
			<td class="textstyle1" colspan="6">
				<b>ASSIGN EMPLOYEES MAXIMUM ORDERS CAN ACCEPT AND MARKED DAY NIGHT SHIFT</b>
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<tr bgcolor="#373737" height="20">
			<td class="smalltext8" width="5%">&nbsp;<b>Sr No.</b></td>
			<td class="smalltext8" width="25%">&nbsp;<b>Employee Name</b></td>
			<td class="smalltext8" width="20%"><b>Maxmium Orders Can Accept</b></td>
			<td class="smalltext8" width="20%"><b>Employee Shift Type</b></td>
			<td class="smalltext8">&nbsp;<b>Total Orders Done</b></td>
		</tr>
		<?php 
		if(!empty($a_pdfEmployeeWithMaxOrder))
		{
			$i	= 0;
			foreach($a_pdfEmployeeWithMaxOrder as $k=>$v)	
			{

				$i++;
				list($employeeName,$maxOrdersAccept,$totalDone,$shiftType)	=	explode("|",$v);

				$dayChecked		=	"checked";
				$nightChecked	=	"";
				$nightShiftText	=	"Night Shift";
				if($shiftType	==	2)
				{
					$dayChecked		=	"";
					$nightChecked	=	"checked";
					$nightShiftText	=	"<font color='#ff0000'>Night Shift</font>";
				}

				$bgColor		=	 "class='rwcolor1'";
				if($i%2			==   0)
				{
					$bgColor	=   "class='rwcolor2'";
				}
			?>
			<tr height="23" <?php echo $bgColor;?>>
				<td class="smalltext2" valign="top">&nbsp;<?php echo $i;?>)</td>
				<td class="smalltext2" valign="top"><?php echo $employeeName;?></td>
				<td valign="top">
					<input type="text" name="maxOrderCanAccept[<?php echo $k;?>]" value="<?php echo $maxOrdersAccept;?>" size="4" onkeypress="return checkForNumber();" maxlength="2">
				</td>
				<td class="smalltext2" valign="top">
					<input type="radio" name="shiftType[<?php echo $k;?>]" value="1" <?php echo $dayChecked;?>>Day Shift&nbsp;
					<input type="radio" name="shiftType[<?php echo $k;?>]" value="2" <?php echo $nightChecked;?>><?php echo $nightShiftText;?>
				</td>
				<td class="smalltext2" valign="top">
					<?php 
						echo $totalDone;
					?>
				</td>
				
			</tr>
			<?php
				}
			?>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
					<input type='hidden' name='formSubmitted' value='1'>
				</td>
			</tr>
		<?php
		}
		else
		{
	?>
		<tr>
			<td colspan="2" class="error">
				&nbsp;&nbsp;&nbsp;No New Orders Available
			</td>
		</tr>
	<?php
		
		}
		?>
	</table>
</form>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>