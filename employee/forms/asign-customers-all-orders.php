<script type="text/javascript">
function validAccept()
{
	form1	=	document.acceptOrder;
	if(form1.employeeId.value ==	"0")
	{
		alert("Please select an employee !!");
		form1.employeeId.focus();
		return false;
	}
}

function redirectViewPageTo(memberId,flag)
{
	window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/assign-customer-all-orders.php?memberId="+memberId+"&"+flag;
}
function removeEmployeesList(flag)
{
	if(flag == 1)
	{
		document.getElementById('showEmployeeList').style.display = 'inline';
	}
	else
	{
		document.getElementById('showEmployeeList').style.display = 'none';
	}
}
function removeCustomerOrderList(flag)
{
	if(flag == 1)
	{
		document.getElementById('showCustomerOrderList').style.display = 'inline';
	}
	else
	{
		document.getElementById('showCustomerOrderList').style.display = 'none';
	}
}
		
</script>
<br>
<table cellpadding="2" cellspacing="2" width='98%'align="center" border='0'>
	<tr>
		<td>
			<?php
				$url1			=	SITE_URL_EMPLOYEES."/show-customer-last-orders.php?customerId=".$customerId;
			?>
			<a onclick="commonFunc('<?php echo $url1;?>','showCustomerOrderList');removeCustomerOrderList(1);" style="cursor:pointer">Last 5 Completed Order details for</a>&nbsp;<font class="textstyle1"> - <?php echo $customerName.$appraisalTypeText;?></font>
		</td>
	</tr>
	<tr>
		<td>
			<div id="showCustomerOrderList"></div>
		</td>
	</tr>
</table>
<form name="acceptAllOrders" action="" method="POST">
	<table width="100%" align="center" border="0" cellspacing="0" cellspacing="0">
		<tr>
			<td colspan="10" class="textstyle1"><b>List Of New Orders</b></td>
		</tr>
		<tr bgcolor="#373737" height="20">
			<td width="35%" class="smalltext8">&nbsp;<b>Order Address</b></td>
			<td width="22%" class="smalltext8"><b>Order Type</b></td>
			<td width="15%" class="smalltext8"><b>Date</b></td>
			<td width="10%" class="smalltext8"><b>TAT</b></td>
			<td class="smalltext8"><b>Assign To</b></td>
		</tr>
		<?php
			$query		=	"SELECT * FROM members_orders WHERE orderId > ".MAX_SEARCH_EMPLOYEE_ORDER_ID." AND memberId=$customerId AND status=0 AND isDeleted=0 AND isVirtualDeleted=0 ORDER BY employeeWarningDate,employeeWarningTime";
			$result		=	dbQuery($query);
			$totalAvaiable	=	0;
			if(mysqli_num_rows($result))
			{
				$l								=	0;
				while($row						=	mysqli_fetch_assoc($result))
				{
					$l++;
					$n_orderId					=	$row['orderId'];
					$n_orderAddress				=	stripslashes($row['orderAddress']);
					$n_orderType				=	$row['orderType'];
					$n_orderAddedOn				=	showDate($row['orderAddedOn']);
					$orderAddedTime				=	$row['orderAddedTime'];
					$isHavingEstimatedTime		=	$row['isHavingEstimatedTime'];
					$employeeWarningDate		=	$row['employeeWarningDate'];
					$employeeWarningTime		=	$row['employeeWarningTime'];
					$isOrderChecked				=	$row['isOrderChecked'];
					$expctDelvText				=	 "";
					$displayTime				=	showTimeFormat($orderAddedTime);
					$n_orderText				=	$a_customerOrder[$n_orderType];

					$bgColor					=	"class='rwcolor1'";
					if($l%2==0)
					{
						$bgColor				=   "class='rwcolor2'";
					}

					if($isHavingEstimatedTime==	1)
					{
						$expctDelvText		    =	orderTAT($employeeWarningDate,$employeeWarningTime);
					}
		?>
		<tr height="26" <?php echo $bgColor;?>>
			<td class="smalltext6" valign="top">&nbsp;<?php echo $n_orderAddress;?></td>
			<td class="smalltext6" valign="top"><?php echo $n_orderText;?></td>
			<td class="smalltext6" valign="top"><?php echo $n_orderAddedOn."/".$displayTime." Hrs";?></td>
			<td class="smalltext6" valign="top"><?php echo $expctDelvText;?></td>
			<td valign="top">
				<?php
					if($isOrderChecked	==	1)
					{
						$totalAvaiable++;
				?>
				<select name="assignSingleOrderTo[<?php echo $n_orderId;?>]">
					<option value="0">Select</option>
					<?php
						foreach($a_employeesName as $k=>$name)
						{
							echo "<option value='$k'>$name</option>";
						}
					?>
				</select>
				<?php
					}
					else
					{
						echo "<font color='#ff0000'>Files must be checked first before assign this order.</font>";
					}
				?>
			</td>
		</tr>
		<?php
				}
			}
		?>
		<tr>
			<td colspan="3">&nbsp;</td>
			<td align="left">
				<?php
					if(!empty($totalAvaiable))
					{
				?>
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmittedAssign' value='1'>
				<?php
					}
					else
					{
						echo "&nbsp;";
					}
				?>
			</td>
		</tr>
	</table>
</form>
<br>
<table cellpadding="2" cellspacing="2" width='98%'align="center" border='0'>
	<tr>
		<td>
			<?php
				$url			=	SITE_URL_EMPLOYEES."/customer-assigned-employees.php?customerId=".$customerId;
			?>
			<a onclick="removeEmployeesList(1);" style="cursor:pointer">View List Of Processing Employees</a>&nbsp;<font class="textstyle1">Assigning To - <?php echo $customerName.$appraisalTypeText;?></font>
		</td>
	</tr>
	<tr>
		<td width="95%">
			<div id="showEmployeeList">
				<?php
					include(SITE_ROOT_EMPLOYEES."/includes/customer-assigned-employees.php");
				?>
			</div>
		</td>
	</tr>
</table>
	