<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>
<script type="text/javascript">
function openEditWidow(customerId,EID)
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/assigned-pdf-employees.php?ID="+customerId+"&EID="+EID;
	prop = "toolbar=no,scrollbars=yes,width=650,height=220,top=100,left=100";
	window.open(path,'',prop);
}
function checkedChild(flag)
{
	mainBox	=   document.getElementById('mainCustomerId'+flag);
	if(mainBox.checked == true)
	{
		document.getElementById('child'+flag).checked  = true;
		document.getElementById('childx'+flag).checked = true;
	}
	else
	{
		document.getElementById('child'+flag).checked  = false;
		document.getElementById('childx'+flag).checked = false;
	}
}
function removeCustomer(customerId,employeeId)
{
	var confirmation = window.confirm("Are You Sure To Remove This Customer?");
	if(confirmation==true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/assign-employee-pdf.php?ID="+employeeId+"&customerId="+customerId+"&isDelete=1";
	}
}
</script>
<?php
if(!empty($a_existingCustomers))
{
	$existingCustomers	=	implode(",",$a_existingCustomers);
	$andClause			=	" AND memberId NOT IN ($existingCustomers)";
}
else
{
	$andClause			=	"";
}
?>
<form  name='addPdfClent' method='POST' action="">
	<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
		<tr>
			<td colspan="6" class="title1"><b>LISTS OF CUSTOMERS</b></td>
		</tr>
		<tr>
			<td colspan="6">
				<hr size="1" width="100%" color="#bebebe">
			</td>
		</tr>
		<tr>
			<td width="5%" class="smalltext2"><b>&nbsp;</b></td>
			<td width="25%" class="smalltext2"><b>CUSTOMER NAME</b></td>
			<td width="10%" class="smalltext2"><b>TYPE</b></td>
			<td width="10%" class="smalltext2"><b>FIRST LEVEL</b></td>
			<td class="smalltext2" width="5%"><b>QA</b></td>
			<td class="smalltext2"><b>ASSIGNED TO OTHER EMPLOYEES</b></td>
		</tr>
		<tr>
			<td colspan="6">
				<hr size="1" width="100%" color="#bebebe">
			</td>
		</tr>
		<?php
			$query		=	"SELECT memberId,firstName,lastName,appraisalSoftwareType FROM members WHERE memberType='".CUSTOMERS."'".$andClause." ORDER BY firstName";
			$result		=	mysql_query($query);
			if(mysql_num_rows($result))
			{
	?>
	<tr>
		<td colspan="6" class="title1"></a><b>NEW CUSTOMERS TO ADD</b></td>
	</tr>
	<tr>
		<td colspan="6">
			<hr size="1" width="100%" color="#bebebe">
		</td>
	</tr>
	<?php
				$i	=	0;
				$j	=	0;
				$k	=	0;
				while($row			=	mysql_fetch_assoc($result))
				{
					$i++;
					$j++;
					$k++;
					$customerId		=	$row['memberId'];
					$firstName		=	stripslashes($row['firstName']);
					$lastName		=	stripslashes($row['lastName']);
					$appraisalSoftwareType	=	$row['appraisalSoftwareType'];
					$customerName	=	$firstName." ".$lastName;
					$appraisalText	=	$a_appraisalSoftware[$appraisalSoftwareType];
			
					$checkedAssign	=	"";
					$checkedReply	=	"";
					$checkedQa		=	"";
					$checkedAssign	=	"checked";
					if(array_key_exists($customerId,$a_replyAccess))
					{
						$checkedReply=	"checked";
					}
					if(array_key_exists($customerId,$a_qaAccess))
					{
						$checkedQa	 =	"checked";
					}
				

					$totalAlreadyAssigned	=	@mysql_result(dbQuery("SELECT COUNT(assignedId) FROM pdf_clients_employees WHERE customerId=$customerId AND employeeId <> $employeeId LIMIT 1"),0);
			?>
			<tr>
				<td class="text">
					<input type="checkbox" name="assignFor[]" value='<?php echo $customerId;?>'  id="mainCustomerId<?php echo $customerId;?>" onclick="return checkedChild(<?php echo $customerId;?>)">
					<!-- <input type="checkbox" name="assignForCustomer[]" value='<?php echo $customerId;?>' <?php echo $checked;?> id="mainCustomerId<?php echo $customerId;?>"  onclick="return checkedChild(<?php echo $customerId;?>)"> -->
				</td>
				<td class="text">
					<?php echo $customerName;?>
				</td>
				<td class="error">
					<b>
						<?php echo $appraisalText;?>
					</b>
				</td>
				<td class="text">
					<input type="checkbox" name="hasReplyAccess[<?php echo $customerId;?>]" value='1' <?php echo $checkedReply;?> id="child<?php echo $customerId;?>">
				</td>
				<td class="text">
					<input type="checkbox" name="hasQaAccess[<?php echo $customerId;?>]" value='1' <?php echo $checkedQa;?> id="childx<?php echo $customerId;?>">
				</td>
				<td class="text">
					<?php
						if(!empty($totalAlreadyAssigned))
						{
					?>
						<a href='javascript:openEditWidow(<?php echo $customerId;?>,<?php echo $employeeId;?>)' class='link_style10'><?php echo $totalAlreadyAssigned;?></a>
					<?php
						}
						else
						{
							echo "<font color='red'>NO</font>";
						}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6">
					<hr size="1" width="100%" color="#bebebe">
				</td>
			</tr>
			<?php
				}
			}
	?>
	<tr>
		<td colspan="2" class="smalltext2">
			<b>Allow Maximum Orders To Accept</b>
		</td>
		<td colspan="2">
			<select name="maximumOrdersAccept">
				<option value="0">Unlimited</option>
				<?php
					for($i=1;$i<=15;$i++)
					{
						$select		=	"";
						if($maximumOrdersAccept == $i)
						{
							$select		=	"selected";
						}
						echo "<option value='$i' $select>$i</option>";
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="6">
			<hr size="1" width="100%" color="#bebebe">
		</td>
	</tr>
	<?php
	
		if($showDelete)
		{
	?>
	<tr>
		<td colspan="6" class="smalltext2">
			<input type="checkbox" name="isDelete" value="1"> <b>Delete This Employee From Pdf Customers List</b> 
		</td>
	</tr>
	<tr>
		<td colspan="6">
			<hr size="1" width="100%" color="#bebebe">
		</td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td colspan="4" class="smalltext2">
			<input type="checkbox" name="hasEmailAccess" value="1" <?php echo $checkedEmailReceive;?>> <b>Click To Stop Receiving any Emails.</b>
		</td>
	</tr>
	<tr>
		<td colspan="6">
			<hr size="1" width="100%" color="#bebebe">
		</td>
	</tr>
	<tr>
		<td>
			&nbsp;
		</td>
		<td colspan="4">
			<input type="image" name="submit" src="<?php echo SITE_URL;?>/images/submit.jpg">
			<input type="hidden" name="formSubmitted1" value="1">
		</td>
	</tr>
</table>
</form>
<?php
	if(!empty($a_existingCustomers))
	{
		$existingCustomers	=	implode(",",$a_existingCustomers);
?>
<br><br>
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
<tr>
	<td colspan="6" class="error"><b>ALREADY ASSIGNED CUSTOMERS</b></td>
</tr>
<tr>
	<td colspan="6">
		<hr size="1" width="100%" color="#bebebe">
	</td>
</tr>
<?php
		$query1		=	"SELECT memberId,firstName,lastName,appraisalSoftwareType FROM members WHERE memberType='".CUSTOMERS."' ORDER BY firstName";
		$result1	=	mysql_query($query1);
		if(mysql_num_rows($result1))
		{
		
			while($row1				=	mysql_fetch_assoc($result1))
			{
				$t_customerId		=	$row1['memberId'];
				$t_firstName		=	stripslashes($row1['firstName']);
				$t_lastName			=	stripslashes($row1['lastName']);
				$t_appraisalSoftwareType	=	$row1['appraisalSoftwareType'];
				$t_customerName		=	$t_firstName." ".$t_lastName;
				$t_appraisalText	=	$a_appraisalSoftware[$t_appraisalSoftwareType];
		
				$hasReply	=	"<font color='#ff0000'>No</font>";
				$hasQa		=	"<font color='#ff0000'>No</font>";
				
				if(array_key_exists($t_customerId,$a_replyAccess))
				{
					$hasReply	=	"<font color='#00AA2B'>Yes</font>";
				}
				if(array_key_exists($t_customerId,$a_qaAccess))
				{
					$hasQa		=	"<font color='#00AA2B'>Yes</font>";
				}
				

				$t_totalAlreadyAssigned	=	@mysql_result(dbQuery("SELECT COUNT(assignedId) FROM pdf_clients_employees WHERE customerId=$t_customerId AND employeeId <> $employeeId LIMIT 1"),0);
		?>
	<tr>
		<td class="text" colspan="2">
			<?php echo $t_customerName;?>
		</td>
		<td class="error">
			<b>
				<?php echo $t_appraisalText;?>
			</b>
		</td>
		<td class="text">
			<?php echo $hasReply;?>
		</td>
		<td class="text">
			<?php echo $hasQa;?>
		</td>
		<td class="text">
			<?php
				if(!empty($t_totalAlreadyAssigned))
				{
			?>
				<a href='javascript:openEditWidow(<?php echo $t_customerId;?>,<?php echo $employeeId;?>)' class='link_style10'><?php echo $t_totalAlreadyAssigned;?></a>
			<?php
				}
				else
				{
					echo "<font color='red'>NO</font>";
				}
			?>&nbsp;&nbsp;&nbsp;
			(<a href="javascript:removeCustomer(<?php echo $t_customerId;?>,<?php echo $employeeId;?>)">Remove This Customer From Employee</a>)
		</td>
	</tr>
	<tr>
		<td colspan="6">
			<hr size="1" width="100%" color="#bebebe">
		</td>
	</tr>
	<?php
			}
		}
	}
?>