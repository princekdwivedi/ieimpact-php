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
	if(form1.employeeId.value	==	"")
	{
		alert("Please Select An Employee !!");
		form1.employeeId.focus();
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
</script>
<form  name='assignWork' method='POST' action="" enctype="multipart/form-data" onsubmit="return checkValidWork();">
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='title' valign="top" colspan="4"><?php echo $text;?></td>
	</tr>
	<tr>
		<td width="15%" class="title3">Platform</td>
		<td width="2%" class="title3">:</td>
		<td colspan="2">
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
	</tr>
	<tr>
		<td align='center' colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td class="title3">Client</td>
		<td class="title3">:</td>
		<td colspan="2">
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
	</tr>
	<tr>
		<td align='center' colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td class="title3">Assign To Employee</td>
		<td class="title3">:</td>
		<td colspan="2">
			<select name="employeeId">
				<option value="">Select</option>
				<?php
					if($result	=	$employeeObj->getAllRevEmployees())
					{
						while($row	=	mysql_fetch_assoc($result))
						{
							$t_employeeId	=	$row['employeeId'];
							$firstName		=	$row['firstName'];
							$lastName		=	$row['lastName'];

							$employeeName	=	$firstName." ".$lastName;
							$employeeName	=	ucwords($employeeName);

							$select			=	"";
							if($t_employeeId	==	$employeeId)
							{
								$select		=	"selected";
							}

							echo "<option value='$t_employeeId' $select>$employeeName</option>";
						}
						
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td align='center' colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td width="20%">&nbsp;</td>
		<td width="2%">&nbsp;</td>
		<td width="15%" class="title3">LEVEL1</td>
		<td  class="title3">LEVEL2</td>
	</tr>
	<tr>
		<td class="title3">Direct</td>
		<td class="title3">:</td>
		<td class="smalltext2">
			<input type="text" name="direct1" value="<?php echo $direct1;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
		</td>
		<td>
			<input type="text" name="direct2" value="<?php echo $direct2;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
		</td>
	</tr>
	<tr>
		<td align='center' colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td class="title3">Indirect</td>
		<td class="title3">:</td>
		<td>
			<input type="text" name="indirect1" value="<?php echo $indirect1;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">&nbsp;&nbsp;
		</td>
		<td>
			<input type="text" name="indirect2" value="<?php echo $indirect2;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
		</td>
	</tr>
	<tr>
		<td align='center' colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td class="title3">QA</td>
		<td class="title3">:</td>
		<td>
			<input type="text" name="qa1" value="<?php echo $qa1;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">&nbsp;&nbsp;
		</td>
		<td>
			<input type="text" name="qa2" value="<?php echo $qa2;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
		</td>
	</tr>
	<tr>
		<td align='center' colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td class="title3">Post Audit</td>
		<td class="title3">:</td>
		<td>
			<input type="text" name="audit1" value="<?php echo $audit1;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">&nbsp;&nbsp;
		</td>
		<td>
			<input type="text" name="audit2" value="<?php echo $audit2;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
		</td>
	</tr>
	<tr>
		<td align='center' colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td class="title3" valign="top">File Name</td>
		<td class="title3" valign="top">:</td>
		<td class="smalltext2" valign="top" colspan="2">
			<input type='file' name='assignFile' class="">
			<br>
			[Only .xlsx file]
		</td>
	</tr>
	<tr>
		<td align='center' colspan="4">&nbsp;</td>
	</tr>
	<?php
		if(!empty($assignedWorkId) && !empty($hasUploadedFile))
		{
	?>
	<tr>
		<td class="title3" valign="top">Existing File Name</td>
		<td class="title3" valign="top">:</td>
		<td class="smalltext2" valign="top" colspan="2">
			<b><?php echo $uploadedFileName;?></b>
		</td>
	</tr>
	<tr>
		<td align='center' colspan="4">&nbsp;</td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td class="title3" valign="top">Comments</td>
		<td class="title3" valign="top">:</td>
		<td colspan="2">
			<textarea name="comments" rows="5" cols="45"><?php echo $comments;?></textarea>
		</td>
	</tr>
	<tr>
		<td align='center' colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td align='center' colspan="2">&nbsp;</td>
		<td colspan="2">
			<input type='submit' name='submit' value='Submit'>
			<input type="button" name="submit" onClick="history.back()" value="Cancel">
			<input type='hidden' value='1' name='formSubmitted'>
		</td>
	</tr>

</table>
</form>