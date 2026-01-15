<script type="text/javascript">
	function checkEmployeeChecklist()
	{
		//return;
		form1	=	document.checkOrder;
		var countTotalChecked	=	1;
		
		for(j=1;j<11;j++){
			access	=	document.getElementsByName('readFileChecklist['+j+']');
			for(i=0;i<access.length;i++)
			{
				if(access[i].checked == true)
				{
					countTotalChecked	=	countTotalChecked+1;
				}
			}
		}
		if(countTotalChecked != 11)
		{
			alert("Please complete the received data checklist.");
			return false;
		}
		if(form1.checkedCompsFiles.value == 0 || form1.checkedCompsFiles.value == "" || form1.checkedCompsFiles.value == " ")
		{
			alert("Please enter number of comps sent.");
			form1.checkedCompsFiles.focus();
			return false;
		}
		if(form1.checkEmployeeNotes.value == 0 || form1.checkEmployeeNotes.value == "" || form1.checkEmployeeNotes.value == " ")
		{
			alert("Please enter internal employee notes.");
			form1.checkEmployeeNotes.focus();
			return false;
		}
	}
	function clickCheckedForList(flag)
	{		
		if(flag	==	2)
		{
			document.getElementById('showNoText').innerHTML = "<font style='fot-family:verdana;font-size:16px;color:#ff0000;font-weight:bold;'>(A message will be sent to customer to request it)</font>";
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
	 function textCounter(field,countfield,maxlimit)
	 {
		if(field.value.length > maxlimit)
		{
			field.value = field.value.substring(0, maxlimit);
		}
		else
		{
			countfield.value = maxlimit - field.value.length;
		}
	 }
</script>
<br />
<form name="checkOrder" action="" method="POST" onsubmit="return checkEmployeeChecklist();">
	<table align='left' cellpadding='0' cellspacing='0' border='0' width="98%">
		<tr>
			<td colspan="3" class="smalltext23"><b>Received Data Checklist</b>&nbsp;[<font class="error">Select "NO" only if you want customer to send it.</font>]</td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
		<?php
			if(!empty($checklistError))
			{
		?>
		<tr>
			<td colspan="3" class="error">
				<?php echo $checklistError;?>
			</td>
		</tr>
		<?php
			}
		?>
		<tr>
			<td width="4%" class="heading3">&nbsp;</td>
			<td width="17%" class="heading3"><b>Checklist</b></td>
			<td align="center" class="heading3"><b>Data Received</b></td>
		</tr>
		<?php
			$countList	=	0;
			foreach($a_checklistFirstOrderCheck as $listId=>$v)
			{
				list($listName,$dbNameList)	=	explode("|",$v);
				$countList++;
		?>
		<tr>
			<td class="smalltext23" valign="top" align='left'><?php echo $countList;?>)</td>
			<td class="smalltext23" valign="top" align='left'><?php echo $listName;?></td>
			<td align="center" class="smalltext23" valign="top" align='left'>
				<input type="radio" name="readFileChecklist[<?php echo $countList;?>]" value="1|<?php echo $listId;?>" onclick="clickCheckedForList(1)">Yes
				<input type="radio" name="readFileChecklist[<?php echo $countList;?>]" value="2|<?php echo $listId;?>" onclick="clickCheckedForList(2)">No
				<input type="radio" name="readFileChecklist[<?php echo $countList;?>]" value="3|<?php echo $listId;?>" onclick="clickCheckedForList(3)">Not Required
			</td>
		</tr>
		<tr>
			<td height="3"></td>
		</tr>
		
		<?php
			}
		?>
		<tr>
			<td colspan="2"><b>Number of Comps Sent :</b></td>
			<td>
				<input type="text" name="checkedCompsFiles" size="10" value="" onkeypress="return checkForNumber();" style="border:1px solid #333333;">
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td class="smalltext2" valign="top" colspan="2"><b>Internal Employee Notes :</b></td>
			<td>
				<input type="text" name="checkEmployeeNotes" size="60" value="" onCopy="return false" onDrag="return false" onDrop="return false" onPaste="return false" autocomplete=off onKeyDown="textCounter(this.form.checkEmployeeNotes,this.form.remLentext1,100);" onKeyUp="textCounter(this.form.checkEmployeeNotes,this.form.remLentext1,100);" style="border:1px solid #333333;">
				<br><font class="smalltex1t">Characters Left: <input type="textbox" readonly name="remLentext1" size=2 value="100" style="border:0"></font>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<div id="showNoText"></div>
			</td>
		</tr>
		<?php
			if(!empty($smsCustomerMobileNo))
			{
		?>
		<tr>
			<td height="6"></td>
		</tr>
		<tr>
			<td class="smalltext23" colspan="3">
				ALSO Click this box to SEND this message as SMS to customer if urgent<input type="checkbox" name="markedChecklistSendSms" value="1">
			</td>
		</tr>
		<?php
			}	
		?>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td colspan="3">
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='checkFormSubmitted' value='1'>
			</td>
		</tr>
	</table>
</form>