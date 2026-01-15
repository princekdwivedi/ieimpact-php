<script type="text/javascript">
	function checkForNumberPoints()
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

	 function deleteInvesment(sectionId,page)
	{
		var confirmation = window.confirm("Are You Sure To Delete This Invesment File?");
		if(confirmation==true)
		{
			window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/add-manage-tax-certificates.php?sectionId="+sectionId+"&isDeleteInvestment=1";
		}
	}
	
	function validForm(){
		form1	=	document.addInvesment;

		if(form1.totalTaxExemption.value == "" || form1.totalTaxExemption.value == " " || form1.totalTaxExemption.value == "0"){
			alert("Please enter total tax exemption.");
			form1.totalTaxExemption.focus();
			return false;
		}
		if(form1.taxRateApproximately.value == "" || form1.taxRateApproximately.value == " " || form1.taxRateApproximately.value == "0"){
			alert("Please enter taxation rate approximately.");
			form1.taxRateApproximately.focus();
			return false;
		}
		if(form1.totalTax.value == "" || form1.totalTax.value == " " || form1.totalTax.value == "0"){
			alert("Please enter total tax.");
			form1.totalTax.focus();
			return false;
		}
	}
</script>
<form name="addInvesment" action="" method="POST" enctype="multipart/form-data" onsubmit="return validForm();">
	<table align='center' cellpadding="3" cellspacing="1" border="0" width="98%" style="border:0px solid #033A61">
		<tr>
			<td colspan="10" class='title'>INCOME TAX INVESTMENT DECLARATION FORM FOR THE YEAR 2016-2017</td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
		<?php
			if(isset($_SESSION['successTax']) && $_SESSION['successTax'] == 1){
				
		?>
		<tr>
			<td colspan="10" class='smalltext24'><a name="#message"></a><b>Successfully added your tax savings</b></td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
		<?php
				unset($_SESSION['successTax']);
			}
			if(!empty($errorMsg)){
		?>
		<tr>
			<td colspan="10" class='error'><?php echo $errorMsg;?></td>
		</tr>
		<?php
			
			}
		?>
		<tr>
			<td width="30%" class="title"><b>Type</b></td>
			<td width="25%" class="title"><b>Details</b></td>
			<td width="9%" class="title"><b>Under Section</b></td>
			<td width="9%" class="title"><b>Amount</b></td>
			<td><font class="title"><b>Upload File</b></font><font class="smalltext1"> (.jpg/.gif/.png/.docx/.xls/.pdf Allowed)</font></td>
		</tr>
		<tr>
			<td colspan="6">
				<hr size="1" width="100%" bgcolor="#bebebe;">
			</td>
		</tr>
		<?php
			foreach($a_invesmentDetails as $key=>$value){
				$mainText		=	$a_invesmentDetails[$key];

				$level			=	0;
				$type			=	"";
				$isreqAmount	=	"";

				list($text,$level,$type,$underSection,$isreqAmount) = explode("|",$mainText);

				if($level		==	0){
					$text		=	"<font class='textstyle2'><b>$text</b></font>";
				}
				elseif($level	==	1){
					$text		=	"<font class='smalltext2'>$text</font>";
				}
				else{
					$text		=	"<font class='smalltext7'><font color='#ff0000;'>$text</font></font>";
				}

				//1= Level TA = TYPE 0 IS req U/S 0 Amount
				if($level		==	0 && $key != 1){
			?>
		<tr>
			<td colspan="6">
				<hr size="1" width="100%" bgcolor="#bebebe;">
			</td>
		</tr>
			<?php
				}
			?>
		<tr>
			<td valign="top"><?php echo $text;?></td>
			<td valign="top">
				<input type="hidden" name="inputFileds[<?php echo $key;?>]" value="0">
				<?php
					$existingDescriptions		=	"";
					if(!empty($a_descriptions) && count($a_descriptions) > 0 && array_key_exists($key,$a_descriptions)){
						$existingDescriptions	=	$a_descriptions[$key];
					}
					if($type == "T" || $type == "TF"){
				?>
				<input type="text" name="description[<?php echo $key;?>]" size="40" value="<?php echo $existingDescriptions;?>" style="border:1px solid #333333;font-family:verdana;font-size:12px;height:20px;">
				<?php
					}
					elseif($type == "TA"){
				?>
				<textarea name="description[<?php echo $key;?>]" rows="6" cols="40" style="border:1px solid #333333;font-family:verdana;font-size:12px;"><?php echo $existingDescriptions;?></textarea>
				<?php
					}
				?>
			</td>
			
			<td class="smalltext2" valign="top">
				<?php
					if(!empty($underSection)){
						echo "<b>$underSection</b>";
					}	
				?>
			</td>
			<td valign="top">
				<?php
					$existingAmount		=	"";
					if(!empty($a_amounts) && count($a_amounts) > 0 && array_key_exists($key,$a_amounts)){
						$existingAmount	=	$a_amounts[$key];
					}
					if(!empty($isreqAmount)){
				?>
					&#8377;<input type="text" name="amount[<?php echo $key;?>]" size="12" value="<?php echo $existingAmount;?>" style="border:1px solid #333333;font-family:verdana;font-size:12px;height:20px;" onkeypress="return checkForNumberPoints();">
				<?php
					}	
				?>
			</td>
			<td valign="top">
				<?php
					if($type == "TF" || $type == "F"){
				?>
						<input type="file" name="taxfiles[<?php echo $key;?>]">
				<?php
						if(!empty($a_existsFiles) && array_key_exists($key,$a_existsFiles)){
							echo "<br />";
				?>
							<a href="<?php echo SITE_URL_EMPLOYEES;?>/dowanload-invesment.php?ID=<?php echo $key;?>&type=tax-invesment" class='link_style28'><?php echo $a_existsFiles[$key];?></a> |

							<a onclick="deleteInvesment(<?php echo $key;?>)"class='link_style28' style="cursor:pointer;" title="Delete File">Delete</a>
				<?php
						}
					}	
				?>
			</td>
		</tr>
		<tr>
			<td height="3"></td>
		</tr>
		<?php
			}
		?>
		<tr>
			<td class="textstyle2"><b>Total Tax Exemption</b></td>
			<td class="smalltext18">&nbsp;</td>
			<td class="smalltext18">&nbsp;</td>
			<td class="smalltext18">&#8377;<input type="text" name="totalTaxExemption" value="<?php echo $totalTaxExemption;?>" size="12" style="border:1px solid #333333;font-family:verdana;font-size:12px;height:20px;" onkeypress="return checkForNumberPoints();"></td>
			<td class="smalltext18">&nbsp;</td>
		</tr>
		<tr>
			<td height="3"></td>
		</tr>
		<tr>
			<td class="textstyle2"><b>Taxation Rate Approximately</b></td>
			<td class="smalltext18">&nbsp;</td>
			<td class="smalltext18">&nbsp;</td>
			<td class="smalltext18">&#8377;<input type="text" name="taxRateApproximately" value="<?php echo $taxRateApproximately;?>" size="12" style="border:1px solid #333333;font-family:verdana;font-size:12px;height:20px;" onkeypress="return checkForNumberPoints();"></td>
			<td class="smalltext18">&nbsp;</td>
		</tr>
		<tr>
			<td height="3"></td>
		</tr>
		<tr>
			<td class="textstyle2"><b>Total Tax</b></td>
			<td class="smalltext18">&nbsp;</td>
			<td class="smalltext18">&nbsp;</td>
			<td class="smalltext18">&#8377;<input type="text" name="totalTax" size="12" value="<?php echo $totalTax;?>" style="border:1px solid #333333;font-family:verdana;font-size:12px;height:20px;" onkeypress="return checkForNumberPoints();"></td>
			<td class="smalltext18">&nbsp;</td>
		</tr>
		<tr>
			<td height="3"></td>
		</tr>
		<tr>
			<td class="textstyle2"><b>Declaration:</b></td>
		</tr>
		<tr>
			<td height="3"></td>
		</tr>
		<tr>
			<td class="smalltext2" colspan="6">1. I, hereby declare that the information given above is correct and true in all respects.</td>
		</tr>
		<tr>
			<td class="smalltext2" colspan="6">2. I also undertake to indemnify the company for any loss/liability that may arise in the event of the information being incorrect.</td>
		</tr>
		<tr>
			<td height="3"></td>
		</tr>
		<tr>
			<td>
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type="hidden" name="formSubmitted" value="1">
			</td>
		</tr>
	</table>
</form>