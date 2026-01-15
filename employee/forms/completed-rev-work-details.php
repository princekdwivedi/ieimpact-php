<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>
<script type="text/javascript">
function checkValidWork()
{
	form1	=	document.assignWork;
	if(form1.platform.value	==	"")
	{
		alert("Please Select A Platform !!");
		form1.platform.focus();
		return false;
	}
	if(form1.customerId.value	==	"")
	{
		alert("Please Select A Client !!");
		form1.customerId.focus();
		return false;
	}
	if(form1.direct1.value	==	"" && form1.direct2.value	==	"" && form1.indirect1.value	==	"" && form1.indirect2.value	==	"" && form1.qa1.value	==	"" && form1.qa2.value	==	"" && form1.audit1.value	==	"" && form1.audit2.value	==	"")
	{
		alert("Please Enter Lines !!");
		form1.direct1.focus();
		return false;
	}
	if(form1.comments.value	==	"")
	{
		alert("Please Enter Comments !!");
		form1.comments.focus();
		return false;
	}
}
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
	else
	{
		return false;
	}
 }
 function calculateTotalAssign()
{
    assignDirect1    = document.getElementById('assignDirect1').value;
    assignDirect2    = document.getElementById('assignDirect2').value;

	assignIndirect1  = document.getElementById('assignIndirect1').value;
    assignIndirect2  = document.getElementById('assignIndirect2').value;

	assignQa1		 = document.getElementById('assignQa1').value;
    assignQa2		 = document.getElementById('assignQa2').value;

	assignAudit1	 = document.getElementById('assignAudit1').value;
    assignAudit2	 = document.getElementById('assignAudit2').value;

 
    assignDirect1    = Math.abs(assignDirect1);
	assignDirect2    = Math.abs(assignDirect2);
    assignIndirect1  = Math.abs(assignIndirect1);
    assignIndirect2  = Math.abs(assignIndirect2);
	assignQa1        = Math.abs(assignQa1);
    assignQa2		 = Math.abs(assignQa2);
	assignAudit1     = Math.abs(assignAudit1);
    assignAudit2	 = Math.abs(assignAudit2);

	totalLines		 =	parseInt(assignDirect1) + parseInt(assignDirect2) + parseInt(assignIndirect1) + parseInt(assignIndirect2) + parseInt(assignQa1) + parseInt(assignQa2) + parseInt(assignAudit1) + parseInt(assignAudit2);
 
    document.getElementById('assignTotal').value = totalLines;
}
function calculateTotalWork(i)
{
	workDirect1    = document.getElementById('workDirect1'+i).value;
	workDirect2    = document.getElementById('workDirect2'+i).value;

	workIndirect1  = document.getElementById('workIndirect1'+i).value;
	workIndirect2  = document.getElementById('workIndirect2'+i).value;

	workQa1		   = document.getElementById('workQa1'+i).value;
	workQa2		   = document.getElementById('workQa2'+i).value;

	workAudit1	   = document.getElementById('workAudit1'+i).value;
	workAudit2	   = document.getElementById('workAudit2'+i).value;

	workDirect1    = Math.abs(workDirect1);
	workDirect2    = Math.abs(workDirect2);
    workIndirect1  = Math.abs(workIndirect1);
    workIndirect2  = Math.abs(workIndirect2);
	workQa1        = Math.abs(workQa1);
    workQa2		   = Math.abs(workQa2);
	workAudit1     = Math.abs(workAudit1);
    workAudit2	   = Math.abs(workAudit2);

	totalWorkedLines		 =	parseInt(workDirect1) + parseInt(workDirect2) + parseInt(workIndirect1) + parseInt(workIndirect2) + parseInt(workQa1) + parseInt(workQa2) + parseInt(workAudit1) + parseInt(workAudit2);
 
    document.getElementById('workTotal'+i).value = totalWorkedLines;
}
</script>
<form name="editCOmpleted" action="" method="POST" enctype="multipart/form-data" onsubmit="return checkValidWork();">
	<table width='99%' align='center' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<td colspan='15' class="smalltext2">
			<b>Assigned Work Details, Completed On <?php echo $completedOn;?></b>
		</td>
	</tr>
	<tr>
		<td colspan='15' height="10"></td>
	</tr>
	<tr>
		<td width='10%' class='text'>Platform</td>
		<td width='10%' class='text'>Client Name</td>
		<td colspan="2" class='text' align="center">Direct</td>
		<td colspan="2" class='text' align="center">Indirect</td>
		<td colspan="2" class='text' align="center">QA</td>
		<td colspan="2" class='text' align="center">Post Audit</td>
		<td width='5%' class='text' align="center">Total</td>
		<td width='15%' class='text'>File Name</td>
		<td class='text'>Comments</td>
	</tr>
	<tr>
		<td width='5%' colspan="2">&nbsp;</td>
		<td width='5%' class='text2' align="center">LEVEL1</td>
		<td width='5%' class='text2' align="center">LEVEL2</td>
		<td width='5%' class='text2' align="center">LEVEL1</td>
		<td width='5%' class='text2' align="center">LEVEL2</td>
		<td width='5%' class='text2' align="center">LEVEL1</td>
		<td width='5%' class='text2' align="center">LEVEL2</td>
		<td width='5%' class='text2' align="center">LEVEL1</td>
		<td width='5%' class='text2' align="center">LEVEL2</td>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td colspan='15'>
			<hr size='1' width='100%' color='#428484'>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<?php
				$url2	=	SITE_URL_EMPLOYEES. "/get-platform-customer.php?parentId=";
			?>
			<select name="platform"  onchange="commonFunc('<?php echo $url2?>','displayCustomer',this.value)">
				<option value="">Select</option>
				<?php
					if($result = $employeeObj->getPlatformByDepartment(2))
					{
						while($row	=	mysql_fetch_assoc($result))
						{
							$t_parentId		=	$row['platfromId'];
							$t_parentName	=	$row['name'];

							$select		 =	"";
							if($t_parentId == $platform)
							{
								$select	 =	"selected";
							}
							echo "<option value='$t_parentId' $select>$t_parentName</option>";
						}
					}
				?>
			</select>
		</td>
		<td class="smalltext2" valign="top">
			<div id="displayCustomer">
			<select name="customerId">
				<option value="">Select</option>
				<?php
					if(!empty($platform) && !empty($customerId))
					{
						if($result = $employeeObj->getPlatformClients($platform))
						{
							while($row	=	mysql_fetch_assoc($result))
							{
								$t_customerId	=	$row['customerId'];
								$customerName	=	$row['name'];

								$select		 =	"";
								if($customerId == $t_customerId)
								{
									$select	 =	"selected";
								}
								
								echo "<option value='$t_customerId' $select>$customerName</option>";
							}
						}
					}
				?>
			</select>
			</div>
		</td>
		<td class='smalltext1' valign="top" align="center">
			<input type="text" name="direct1" value="<?php echo $direct1;?>" size="5" maxlength="20" onKeyPress="return checkForNumber();" id="assignDirect1" onblur="javascript:return calculateTotalAssign();">
		</td>
		<td class='smalltext1' valign="top" align="center">
			<input type="text" name="direct2" value="<?php echo $direct2;?>" size="5" maxlength="20" onKeyPress="return checkForNumber();" id="assignDirect2" onblur="javascript:return calculateTotalAssign();">
		</td>
		<td class='smalltext1' valign="top" align="center">
			<input type="text" name="indirect1" value="<?php echo $indirect1;?>" size="5" maxlength="20" onKeyPress="return checkForNumber();" id="assignIndirect1" onblur="javascript:return calculateTotalAssign();">
		</td>
		<td class='smalltext1' valign="top" align="center">
			<input type="text" name="indirect2" value="<?php echo $indirect2;?>" size="5" maxlength="20" onKeyPress="return checkForNumber();"  id="assignIndirect2" onblur="javascript:return calculateTotalAssign();">
		</td>
		<td class='smalltext1' valign="top" align="center">
			<input type="text" name="qa1" value="<?php echo $qa1;?>" size="5" maxlength="20" onKeyPress="return checkForNumber();"   id="assignQa1" onblur="javascript:return calculateTotalAssign();">
		</td>
		<td class='smalltext1' valign="top" align="center">
			<input type="text" name="qa2" value="<?php echo $qa2;?>" size="5" maxlength="20" onKeyPress="return checkForNumber();" id="assignQa2" onblur="javascript:return calculateTotalAssign();">
		</td>
		<td class='smalltext1' valign="top" align="center">
			<input type="text" name="audit1" value="<?php echo $audit1;?>" size="5" maxlength="20" onKeyPress="return checkForNumber();" id="assignAudit1" onblur="javascript:return calculateTotalAssign();">
		</td>
		<td class='smalltext1' valign="top" align="center">
			<input type="text" name="audit2" value="<?php echo $audit2;?>" size="5" maxlength="20" onKeyPress="return checkForNumber();" id="assignAudit2" onblur="javascript:return calculateTotalAssign();">
		</td>
		<td class='smalltext1' valign="top" align="center">
			<input type="text" name="totalLinesAssigned" value="<?php echo $totalLinesAssigned;?>" size="5" maxlength="20" onKeyPress="return checkForNumber();" id="assignTotal">
		</td>
		<td class='smalltext1' valign="top">
			<input type='file' name='assignFile'><br>
			<?php
				if(!empty($uploadedFileName))
				{
					echo $uploadedFileName;
				}
				else
				{
					echo "No File Added";
				}
			?>
		</td>
		<td class='smalltext1' valign="top">
			<textarea name="assignComments" rows="3" cols="22"><?php echo $t_comments;?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan='15' height="10"></td>
	</tr>
	<tr>
		<td colspan='15' class="smalltext2">
			<b>Work Done Details</b>
		</td>
	</tr>
	<tr>
		<td colspan='15' height="10"></td>
	</tr>
<table>
<?php
	$query		=	"SELECT * FROM employee_works WHERE employeeId=$employeeId AND assignedWorkId=$assignedWorkId ORDER BY workedOn DESC";
	$result	=	mysql_query($query);
	if(mysql_num_rows($result))
	{
?>
<table width='99%' align='center' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<td width='21%' class='text'>Worked On Date</td>
		<td colspan="2" class='text' align="center">Direct</td>
		<td colspan="2" class='text' align="center">Indirect</td>
		<td colspan="2" class='text' align="center">QA</td>
		<td colspan="2" class='text' align="center">Post Audit</td>
		<td width='5%' class='text' align="center">Total</td>
		<td width='14%' class='text'>File Name</td>
		<td class='text'>Comments</td>
	</tr>
	<tr>
		<td width='5%'>&nbsp;</td>
		<td width='5%' class='text2' align="center">LEVEL1</td>
		<td width='5%' class='text2' align="center">LEVEL2</td>
		<td width='5%' class='text2' align="center">LEVEL1</td>
		<td width='5%' class='text2' align="center">LEVEL2</td>
		<td width='5%' class='text2' align="center">LEVEL1</td>
		<td width='5%' class='text2' align="center">LEVEL2</td>
		<td width='5%' class='text2' align="center">LEVEL1</td>
		<td width='5%' class='text2' align="center">LEVEL2</td>
		<td colspan="3"></td>
	</tr>
	<tr>
		<td colspan='12'>
			<hr size='1' width='100%' color='#428484'>
		</td>
	</tr>
	<?php
		$i	=  0;
		while($row		=	mysql_fetch_assoc($result))
		{
			$i++;
			$workId			=	$row['workId'];
			$directLevel1	=	$row['directLevel1'];
			$directLevel2	=	$row['directLevel2'];
			$indirectLevel1	=	$row['indirectLevel1'];
			$indirectLevel2	=	$row['indirectLevel2'];

			$qaLevel1		=	$row['qaLevel1'];
			$qaLevel2		=	$row['qaLevel2'];
			$auditLevel1	=	$row['auditLevel1'];
			$auditLevel2	=	$row['auditLevel2'];

			$workedOn		=	$row['workedOn'];
			$t_workedOn		=	showDate($row['workedOn']);
			$comments		=	$row['comments'];
			$t_uploadFileName=	$row['uploadFileName'];
			
			$total			=	$directLevel1+$directLevel2+$indirectLevel1+$indirectLevel2+$qaLevel1+$qaLevel2+$auditLevel1+$auditLevel2;
	?>
	<tr>
		<td class="smalltext2" valign="top">
			<b><?php echo $t_workedOn;?></b>
		</td>
		<td valign="top">
			<input type="text" name="directLevel1[<?php echo $workId;?>]" value="<?php echo $directLevel1;?>" size="5" maxlength="20" onKeyPress="return checkForNumber();" id="workDirect1<?php echo $i;?>" onblur="javascript:return calculateTotalWork(<?php echo $i;?>);">
		</td>
		<td valign="top">
			<input type="text" name="directLevel2[<?php echo $workId;?>]" value="<?php echo $directLevel2;?>" size="5" maxlength="20" onKeyPress="return checkForNumber();" id="workDirect2<?php echo $i;?>" onblur="javascript:return calculateTotalWork(<?php echo $i;?>);">
		</td>
		<td valign="top">
			<input type="text" name="indirectLevel1[<?php echo $workId;?>]" value="<?php echo $indirectLevel1;?>" size="5" maxlength="20" onKeyPress="return checkForNumber();" id="workIndirect1<?php echo $i;?>" onblur="javascript:return calculateTotalWork(<?php echo $i;?>);">
		</td>
		<td valign="top">
			<input type="text" name="indirectLevel2[<?php echo $workId;?>]" value="<?php echo $indirectLevel2;?>" size="5" maxlength="20" onKeyPress="return checkForNumber();" id="workIndirect2<?php echo $i;?>" onblur="javascript:return calculateTotalWork(<?php echo $i;?>);">
		</td>
		<td valign="top">
			<input type="text" name="qaLevel1[<?php echo $workId;?>]" value="<?php echo $qaLevel1;?>" size="5" maxlength="20" onKeyPress="return checkForNumber();" id="workQa1<?php echo $i;?>" onblur="javascript:return calculateTotalWork(<?php echo $i;?>);">
		</td>
		<td valign="top">
			<input type="text" name="qaLevel2[<?php echo $workId;?>]" value="<?php echo $qaLevel2;?>" size="5" maxlength="20" onKeyPress="return checkForNumber();" id="workQa2<?php echo $i;?>" onblur="javascript:return calculateTotalWork(<?php echo $i;?>);">
		</td>
		<td valign="top">
			<input type="text" name="auditLevel1[<?php echo $workId;?>]" value="<?php echo $auditLevel1;?>" size="5" maxlength="20" onKeyPress="return checkForNumber();" id="workAudit1<?php echo $i;?>" onblur="javascript:return calculateTotalWork(<?php echo $i;?>);">
		</td>
		<td valign="top">
			<input type="text" name="auditLevel2[<?php echo $workId;?>]" value="<?php echo $auditLevel2;?>" size="5" maxlength="20" onKeyPress="return checkForNumber();" id="workAudit2<?php echo $i;?>" onblur="javascript:return calculateTotalWork(<?php echo $i;?>);">
		</td>
		<td valign="top">
			<input type="text" name="total[<?php echo $workId;?>]" value="<?php echo $total;?>" size="5" maxlength="20" onKeyPress="return checkForNumber();" id="workTotal<?php echo $i;?>">
		</td>
		<td valign="top" class="smalltext2">
			<input type='file' name='workFileName[<?php echo $workId;?>]'><br>
			<?php 
				if(!empty($t_uploadFileName))
				{
					echo $t_uploadFileName;
				}
				else
				{
					echo "No File Added";
				}
			?>
		</td>
		<td class='smalltext1' valign="top">
			<textarea name="comments[<?php echo $workId;?>]" rows="3" cols="22"><?php echo $comments;?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan='12'>
			<hr size='1' width='100%' color='#428484'>
		</td>
	</tr>
	<?php
		}
	?>
</table>
<?php
	}
?>
<table width="95%" align="center">
	<tr>
		<td align='center'>&nbsp;</td>
	</tr>
	<tr>
		<td>
			<input type='submit' name='submit' value='Submit'>
			<input type="button" name="submit" onClick="history.back()" value="Cancel">
			<input type='hidden' value='1' name='formSubmitted'>
		</td>
	</tr>
<table>
</form>