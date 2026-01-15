<script type="text/javascript">
function showProgress()
{
	var pb = document.getElementById("progressBar");
	pb.innerHTML = '<img src="../images/progress-bar.gif"/>';
	pb.style.display = '';
}
function validDeletenotes()
{
	//return;
	form1	= document.addOrderDeletenote;
	
	//document.forms["bug_form"]["Accept"].checked
	

	form1.submit.value    = "Please wait..Deletion Under Process";
	form1.submit.disabled = true;

	showProgress();

}
function showHideDeleteETAText(flag)
{
	if(flag			==	1)
	{
		document.getElementById('showHideEtaText').style.display   = 'inline';
		document.getElementById('showHideOtherText').style.display = 'none';
	}
	else if(flag	==	5)
	{
		document.getElementById('showHideEtaText').style.display   = 'none';
		document.getElementById('showHideOtherText').style.display = 'inline';
	}
	else
	{
		document.getElementById('showHideEtaText').style.display   = 'none';
		document.getElementById('showHideOtherText').style.display = 'none';
	}
}
</script>
<form name="addOrderDeletenote" action="" method="POST" onsubmit="return validDeletenotes();">
	<table width="98%" align="center" border="0" cellpadding="1" cellspacing="1">
		
		<tr>
			<td colspan="3" height="1"></td>
		</tr>
		<tr>
			<td colspan="3">
				<font class="textstyle"><b>Reason for deleting this order :</b></font>
				<?php
					if(!empty($errorMsg))
					{
						echo "&nbsp;<font class='error'>".$errorMsg."</font>";
					}
				?>
			</td>
		</tr>
		<tr>
			<td colspan="3" height="1"></td>
		</tr>
		<?php
			foreach($a_deletingOrderReason as $key=>$value)
			{
					$checked		=	"";
					if($key			==	$checkedReason)
					{
						$checked	=	"checked";
					}
		?>
		<tr>
			<td colspan="3" class="smalltext18">
				<input type="radio" name="checkedReason" value="<?php echo $key?>" onclick="showHideDeleteETAText(<?php echo $key?>);" <?php echo $checked;?>>&nbsp;<?php echo $value;?>
			</td>
		</tr>
		<?php
			}
		?>
		
		<tr>
			<td colspan="3">
				<div id="showHideOtherText" style="display:<?php echo $hideOtherReason;?>;">
					<table width="98%" align="center" border="0" cellpadding="1" cellspacing="1">
						
						<tr>
							<td height="5"></td>
						</tr>
						<tr>
							<td class="smalltext2">Enter Other Reason</td>
						</tr>
						<tr>
							<td class='error'>
								<textarea name="deleteOrderNotes" rows="4" cols="55" style="border:1px solid #666666;"></textarea>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<div id="showHideEtaText" style="display:none;"></div>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="2">
				<div id="progressBar" style="display: none;"><img src="../images/progress-bar.gif"/></div>
			</td>
		</tr>
		<tr>
			<td colspan="2" height="10"></td>
		</tr>
		<tr>
			<td colspan="3">
				<input type="submit" name="submit" value="Delete This Order">&nbsp;
				<input type="hidden" name="formSubmitted" value="1">
			</td>
		</tr>
		<tr>
			<td colspan="2" height="10"></td>
		</tr>
	</table>
</form>
