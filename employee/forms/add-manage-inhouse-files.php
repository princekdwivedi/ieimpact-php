<script type="text/javascript">
	function checkValidInhouse()
	{
		form1	=	document.inhouseMessage;
		if(form1.inhouseOrderText.value	==	"" || form1.inhouseOrderText.value	==	" " || form1.inhouseOrderText.value	==	"0" || form1.inhouseOrderText.value	==	"  ")
		{
			alert("Please enter Message/Title.");
			form1.inhouseOrderText.focus();
			return false;
		}
		
	}

	function deleteInhouseMessage(customerId,orderId,inhouseMessageId)
	{
		var confirmation = window.confirm("Are You Sure Delete This Message/File?");
		if(confirmation == true)
		{
			window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId="+orderId+"&customerId="+customerId+"&inhouseMessageId="+inhouseMessageId+"&isDeleteInHouse=1";
		}
		
	}

</script>
<form name="inhouseMessage" action="" method="POST" enctype="multipart/form-data" onsubmit="return checkValidInhouse();">
	<table width='98%' align='center' cellpadding='3' cellspacing='3' border='0'>
		<?php
			if(isset($_SESSION['success'])){
		?>
		<tr>
			<td valign="top" colspan="2">&nbsp;</td>
			<td class="textstyle1">
				<font color="green"><b>Successfully updated In House Message</b></fonnt>
			</td>
		</tr>
		<?php
				unset($_SESSION['success']);
			}
			if(isset($_SESSION['delete_success'])){
		?>
		<tr>
			<td valign="top" colspan="2">&nbsp;</td>
			<td class="textstyle1">
				<font color="red"><b>Successfully deleted In House Message</b></fonnt>
			</td>
		</tr>
		<?php
				unset($_SESSION['delete_success']);
			}
			if(!empty($inhouseErrorMsg)){
		?>
		<tr>
			<td valign="top" colspan="2">&nbsp;</td>
			<td class="textstyle1">
				<font color="#ff0000"><?php echo $inhouseErrorMsg; ?></fonnt>
			</td>
		</tr>
		<?php
			}
		?>
		<tr>
			<td width="15%" class="textstyle1" valign="top">
				Message/Title
			</td>
			<td width="2%" class="textstyle1" valign="top">
				:
			</td>
			<td valign="top">
				<textarea name="inhouseOrderText" style="width:400px;height:70px;border:1px solid #000000"><?php echo $inhouseOrderText?></textarea>
			</td>
		</tr>
		<tr>
			<td class="textstyle1" valign="top">
				Files to be Upload
			</td>
			<td class="textstyle1" valign="top">
				:
			</td>
			<td valign="top">
				<input type="file" name="inhouseFile">
			</td>
		</tr>
		<tr>
			<td valign="top" colspan="2">&nbsp;</td>
			<td>
				<input type='image' name='submit' src='<?php echo SITE_URL;?>/images/submit.jpg'>
				<input type='hidden' value='1' name='inhouseFormSubmitted'>
			</td>
		</tr>
	</table>
</form>
<br /><br />