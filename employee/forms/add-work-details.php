<script type="text/javascript">
function checkValidWork()
{
	//return;
	form1	=	document.addDailyWork;
	if(form1.directLevel1.value	==	"" && form1.directLevel2.value	==	"" && form1.indirectLevel1.value	==	"" && form1.indirectLevel2.value	==	"" && form1.qaLevel1.value	==	"" && form1.qaLevel2.value	==	"" && form1.auditLevel1.value	==	"" && form1.auditLevel2.value	==	"")
	{
		alert("Please Enter Work !!");
		form1.directLevel1.focus();
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
<form  name='addDailyWork' method='POST' action="" enctype="multipart/form-data" onsubmit="return checkValidWork();">
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td colspan="4">
			<?php
				echo $errorMsg;
			?>
		</td>
	</tr>
	<tr>
		<td width="20%">&nbsp;</td>
		<td width="2%">&nbsp;</td>
		<td width="17%" class="title3">LEVEL1</td>
		<td  class="title3">LEVEL2</td>
	</tr>
	<tr>
		<td class="title">Direct</td>
		<td class="title">:</td>
		<td class="smalltext2">
			<input type="text" name="directLevel1" value="<?php echo $directLevel1;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
		</td>
		<td>
			<input type="text" name="directLevel2" value="<?php echo $directLevel2;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
		</td>
	</tr>
	<tr>
		<td align='center' colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td class="title">Indirect</td>
		<td class="title">:</td>
		<td>
			<input type="text" name="indirectLevel1" value="<?php echo $indirectLevel1;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">&nbsp;&nbsp;
		</td>
		<td>
			<input type="text" name="indirectLevel2" value="<?php echo $indirectLevel2;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
		</td>
	</tr>
	<tr>
		<td align='center' colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td class="title">QA</td>
		<td class="title">:</td>
		<td>
			<input type="text" name="qaLevel1" value="<?php echo $qaLevel1;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">&nbsp;&nbsp;
		</td>
		<td>
			<input type="text" name="qaLevel2" value="<?php echo $qaLevel2;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
		</td>
	</tr>
	<tr>
		<td align='center' colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td class="title">Post Audit</td>
		<td class="title">:</td>
		<td>
			<input type="text" name="auditLevel1" value="<?php echo $auditLevel1;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">&nbsp;&nbsp;
		</td>
		<td>
			<input type="text" name="auditLevel2" value="<?php echo $auditLevel2;?>" size="10" maxlength="20" onKeyPress="return checkForNumber();">
		</td>
	</tr>
	<tr>
		<td align='center' colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td class="title" valign="top">File Name</td>
		<td class="title" valign="top">:</td>
		<td class="smalltext2" valign="top" colspan="2">
			<input type='file' name='workedOnFile' class="">
			<br>
			[Only .xlsx file]
		</td>
	</tr>
	<tr>
		<td align='center' colspan="4">&nbsp;</td>
	</tr>
	<?php
		if(!empty($workId) && !empty($hasUploadFile))
		{
	?>
	<tr>
		<td class="title" valign="top">Existong File Name</td>
		<td class="title" valign="top">:</td>
		<td class="smalltext2" valign="top" colspan="2">
			<b><?php echo $uploadFileName;?></b>
		</td>
	</tr>
	<tr>
		<td align='center' colspan="4">&nbsp;</td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td class="title" valign="top">Comments</td>
		<td class="title" valign="top">:</td>
		<td colspan="2">
			<textarea name="comments" rows="5" cols="45"><?php echo $comments;?></textarea>
		</td>
	</tr>
	<!-- <tr>
		<td class="title" valign="top">Working Hours</td>
		<td class="title" valign="top">:</td>
		<td valign="top" colspan="2">
			<select name="totalHours">
				<option value="0">00</option>
				<?php
					for($i=1;$i<=23;$i++)
					{
						$select		=	"";
						$k			=	$i;
						if($k <= 9)
						{
							$k		=	"0".$i;
						}
						if($i		==	$totalHours)
						{
							$select	=	"selected";
						}
						echo "<option value='$i' $select>$k</option>";
					}
				?>
			</select><font class="smalltext2">Hrs</font>&nbsp;&nbsp;
			<select name="totalMinitues">
				<option value="0">00</option>
				<?php
					for($i=1;$i<=59;$i++)
					{
						$select		=	"";
						$k			=	$i;
						if($k <= 9)
						{
							$k		=	"0".$i;
						}
						if($i		==	$totalMinitues)
						{
							$select	=	"selected";
						}
						echo "<option value='$i' $select>$k</option>";
					}
				?>
			</select><font class="smalltext2">Minitues</font>
		</td>
	</tr>
	<tr>
		<td class="title" valign="top">Worked From Time</td>
		<td class="title" valign="top">:</td>
		<td valign="top">
			<select name="startingHours">
				<option value="0">00</option>
				<?php
					for($i=1;$i<=23;$i++)
					{
						$select		=	"";
						$k			=	$i;
						if($k <= 9)
						{
							$k		=	"0".$i;
						}
						if($i		==	$startingHours)
						{
							$select	=	"selected";
						}
						echo "<option value='$i' $select>$k</option>";
					}
				?>
			</select><font class="smalltext2">Hrs</font>&nbsp;&nbsp;
			<select name="startingMinitues">
				<option value="0">00</option>
				<?php
					for($i=1;$i<=59;$i++)
					{
						$select		=	"";
						$k			=	$i;
						if($k <= 9)
						{
							$k		=	"0".$i;
						}
						if($i		==	$startingMinitues)
						{
							$select	=	"selected";
						}
						echo "<option value='$i' $select>$k</option>";
					}
				?>
			</select><font class="smalltext2">Minitues</font>
		</td>
		<td valign="top"><font class="title">To</font>&nbsp;&nbsp;
			<select name="endingHours">
				<option value="0">00</option>
				<?php
					for($i=1;$i<=23;$i++)
					{
						$select		=	"";
						$k			=	$i;
						if($k <= 9)
						{
							$k		=	"0".$i;
						}
						if($i		==	$endingHours)
						{
							$select	=	"selected";
						}
						echo "<option value='$i' $select>$k</option>";
					}
				?>
			</select><font class="smalltext2">Hrs</font>&nbsp;&nbsp;
			<select name="endingMinitues">
				<option value="0">00</option>
				<?php
					for($i=1;$i<=59;$i++)
					{
						$select		=	"";
						$k			=	$i;
						if($k <= 9)
						{
							$k		=	"0".$i;
						}
						if($i		==	$endingMinitues)
						{
							$select	=	"selected";
						}
						echo "<option value='$i' $select>$k</option>";
					}
				?>
			</select><font class="smalltext2">Minitues</font>
		</td>
	</tr> -->
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
