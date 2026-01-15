<script type="text/javascript">
	var count  = 1;
	var count1 = 1;
	function addMore()
	{
		/*var tr1 = document.createElement('tr');
		var td1 = document.createElement('td');	
		td1.setAttribute("valign", "top");
		td1.innerHTML = "Simple File";
		//td1.appendChild(txt1);	
		tr1.appendChild(td1);*/

		if(count > 19)
		{
			alert("Maximum limit reached.");
			return false;
		}

		var tr1 = document.createElement('tr');
		var td2 = document.createElement('td');	
		var txt2 = document.createElement('input');
		txt2.type = "file";
		txt2.name = "file[]";

		td2.appendChild(txt2);	
		tr1.appendChild(td2);

		
		
		if(count>0)
		{
			var img     = document.createElement('IMG');
			img.setAttribute('src', '<?php echo OFFLINE_IMAGE_PATH;?>/images/c_delete.gif');
			img.setAttribute('title', 'Remove');
			img.setAttribute('style', 'cursor:pointer');
			img.onclick = function()
			{
				removeContact(tr1);
			}
			td2.appendChild(img);
		}
		document.getElementById('table1').appendChild(tr1);	

		count	= parseInt(count)+1;
	}
	function removeContact(tr)
	{
		tr.parentNode.removeChild(tr);
		count	= parseInt(count)-1;
	}

	function showProgress()
	{
		var pb = document.getElementById("progressBar");
		pb.innerHTML = '<img src="../images/progress-bar.gif"/>';
		pb.style.display = '';
	}

	function validOrderForm()
	{
		form2	=	document.addOrders;
		if(form2.orderAddress.value == "" || form2.orderAddress.value == " " || form2.orderAddress.value == "0"){
			alert("Please enter order address.");
			form2.orderAddress.focus();
			return false;
		}

		if(form2.orderType.value == "" || form2.orderType.value == " " || form2.orderType.value == "0"){
			alert("Please select order type.");
			form2.orderType.focus();
			return false;
		}
		if(form2.orderType.value == "6" && (form2.customersOwnOrderText.value == "" || form2.customersOwnOrderText.value == " " || form2.customersOwnOrderText.value == "0" || form2.customersOwnOrderText.value == "Enter Other Type")){
			alert("Please enter other type.");
			form2.customersOwnOrderText.focus();
			return false;
		}
		form2.submit.value    = "Creating order please wait...";
		form2.submit.disabled = true;
		showProgress();
	}
    function showBox(flag,path)
    {
        var msg = "";
        var filext = path.substring(path.lastIndexOf(".")+1);

        var filext = filext.toLowerCase();

        if(flag == 1 && filext != "zap")
        {
            msg        =    "The file you have submitted does not appear to be a valid ZAP file.";
        }
        else if(flag == 2 && (filext != "aci" && filext != "zoo"))
        {
            msg        =    "The file you have submitted does not appear to be a valid ACI file.";
        }
		else if(flag == 3 && filext != "clk")
        {
            msg        =    "The file you have submitted does not appear to be a valid CLK file.";
        }
		else if(flag == 4 && (filext != "rpt" && filext != "rptx"))
        {
            msg        =    "The file you have submitted does not appear to be a valid RPT file.";
        }
		else if(flag == 5 && filext != "zap")
        {
            msg        =    "The file you have submitted does not appear to be a valid ZAP file.";
        }
		else if(flag == 6 && filext != "rpt")
        {
            msg        =    "The file you have submitted does not appear to be a valid RPT file.";
        }
        document.getElementById('displayType').innerHTML = msg;
    }

	

	function validateFileSize(checkingFor)
	{
	   form1			   =	document.addOrders;

	   document.getElementById('showSubmitDisable').disabled = '';

	   var msgFor		   =	"";

	   var formValue	   =	"";

	   var formValue1	   =	"";

	   var maxFileSizeAllowed	=	"<?php echo MAXIMUM_SINGLE_FILE_SIZE_ALLOWED;?>";
	   var maxFileSizeAllowedTxt=	"<?php echo MAXIMUM_SINGLE_FILE_SIZE_ALLOWED_TEXT?>";

	   if(checkingFor	   ==	1)
	   {
			msgFor		   =	"Template";

			formValue	   =    form1.orderFile.value;
			formValue1	   =    form1.orderFile;
	   }
	   else if(checkingFor ==	2)
	   {
			msgFor		   =	"Public Records";

			formValue	   =    form1.publicRecordFile.value;
			formValue1	   =    form1.publicRecordFile;
	   }
	   else if(checkingFor ==	3)
	   {
			msgFor		   =	"MLS";

			formValue	   =    form1.mlsFile.value;
			formValue1	   =    form1.mlsFile;
	   }
	   else if(checkingFor ==	4)
	   {
			msgFor		   =	"Market Conditions";

			formValue	   =    form1.marketCondition.value;
			formValue1	   =    form1.marketCondition;
	   }
	   else if(checkingFor ==	5)
	   {
			msgFor		   =	"Field Inspection Notes";

			formValue	   =    form1.otherFile.value;
			formValue1	   =    form1.otherFile;
	   }
	   document.getElementById('displaySizeMessage'+checkingFor).innerHTML = "";


	   var uploadedSize	   =	0;
	   if(navigator.appName=="Microsoft Internet Explorer")
	   {
		  if(formValue)
		  {
			 var oas=new ActiveXObject("Scripting.FileSystemObject");
			 var e=oas.getFile(formValue);
			 var size=e.size;

			 uploadedSize	=	size;
		  }
	   }
	   else
	   {
		  if(formValue1.files[0]!=undefined)
		  {
			 size			= formValue1.files[0].size;
			 uploadedSize	=	size;
		  }
	   }
	   if(uploadedSize != 0 && uploadedSize > maxFileSizeAllowed)
	   {
		  document.getElementById('showSubmitDisable').disabled = "true";
		  
		  var typeSizeMsg	=	"The "+msgFor+" you are trying to send is very large. It's size must be less than "+maxFileSizeAllowedTxt+". Please reduce the filesize by removing large pictures etc.";

		  document.getElementById('displaySizeMessage'+checkingFor).innerHTML = typeSizeMsg;

		  alert("The "+msgFor+" you are trying to send is very large. It's size must be less than "+maxFileSizeAllowedTxt+". Please reduce the filesize by removing large pictures etc.");

		  return false;
	   }
	 }

	

	function showHideOtherOrderType(flag)
	{
		if(flag == 6)
		{
			document.getElementById('customerOtherOrderType').style.display = 'inline';
		}
		else
		{
			document.getElementById('customerOtherOrderType').style.display = 'none';
		}
	}

</script>
<form name='addOrders' method='POST' enctype="multipart/form-data" action="" onsubmit="return validOrderForm();">
	<table width="95%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
		<?php 
			if(isset($_SESSION['successPlaced'])){
		?>
		<tr>
			<td colspan="3" class="textstyle3"><font color="#ff0000;"><b>Successfully placed order</b></font></td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
		<?php
				unset($_SESSION['successPlaced']);
			}
		?>
		<tr>
			<td colspan="3" class="textstyle1"><b>Place an Order For : <?php echo $customerName."(".$customerEmail.")";?></b>&nbsp;&nbsp;[A fields marked with <font color="#ff0000;">*</font> are mandatory]</td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
		<tr>
			<td colspan="4">
				<div id="displayType" style="font-family:arial;font-size:14px;color:#ff0000;text-decoration:none;font-weight:bold;"></div>
				<div id="displaySizeMessage1" style="font-family:arial;font-size:14px;color:#ff0000;text-decoration:none;font-weight:bold;"></div>
				<div id="displaySizeMessage2" style="font-family:arial;font-size:14px;color:#ff0000;text-decoration:none;font-weight:bold;"></div>
				<div id="displaySizeMessage3" style="font-family:arial;font-size:14px;color:#ff0000;text-decoration:none;font-weight:bold;"></div>
				<div id="displaySizeMessage4" style="font-family:arial;font-size:14px;color:#ff0000;text-decoration:none;font-weight:bold;"></div>
				<div id="displaySizeMessage5" style="font-family:arial;font-size:14px;color:#ff0000;text-decoration:none;font-weight:bold;"></div>
			</td>
		</tr>
		<?php 
			if(!empty($errorMsg)){
		?>
		<tr>
			<td colspan="3" class="error"><b><?php echo $errorMsg;?></b></td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
		<?php
			}
		?>
		<tr>
			<td width="22%" class="textstyle1">Order Address<font color="#ff0000;">*</font></td>
			<td width="2%" class="textstyle1">:</td>
			<td>
				<input type="text" name="orderAddress" value="<?php echo $orderAddress;?>" style="height:25Px;width:350px;border:1px solid #000000;color:#444444;font-size:16px;" maxlength="50">
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td class="textstyle1" valign="top">Order Type<font color="#ff0000;">*</font></td>
			<td class="textstyle1" valign="top">:</td>
			<td>
				<select name="orderType" onchange="showHideOtherOrderType(this.value);" style="height:25Px;width:150px;border:1px solid #000000;color:#444444;font-size:16px;">
					<option value="">Select</option>
					<?php
						foreach($a_addingCustomerOrderTypes as $key=>$value)
						{
							$select		=	"";
							if($orderType	==	$key)
							{
								$select		=	"selected";
							}

							echo "<option value='$key' $select>$value</option>";
						}
					?>
				</select>
				<br>
				<div id="customerOtherOrderType" style="display:<?php echo $displayOtherOrderType;?>">
					<input type="text" name="customersOwnOrderText" value="<?php echo stripslashes(htmlentities($customersOwnOrderText,ENT_QUOTES))?>" onFocus="if(this.value=='Enter Other Type') this.value='';" onBlur="if(this.value=='') this.value='Enter Other Type';" style="height:25Px;width:350px;border:1px solid #000000;color:#444444;font-size:16px;">
				</div>
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td class="textstyle1"><?php echo $uploadingFileNameText;?></td>
			<td class="textstyle1">:</td>
			<td>
				<input type="file" name="orderFile" onchange="showBox(<?php echo $appraisalSoftwareType;?>,this.value),validateFileSize(1);">
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td class="textstyle1">Upload MLS File</td>
			<td class="textstyle1">:</td>
			<td>
				<input type="file" name="mlsFile" onchange="validateFileSize(3);">
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td class="textstyle1">Upload Market Conditions File</td>
			<td class="textstyle1">:</td>
			<td>
				<input type="file" name="marketCondition"  onchange="validateFileSize(4);">
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td class="textstyle1">Upload Field Inspection Notes</td>
			<td class="textstyle1">:</td>
			<td>
				<input type="file" name="otherFile" onchange="validateFileSize(5);">
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td class="textstyle1">Upload More Files</td>
			<td class="textstyle1">:</td>
			<td>
				<input id="file" type="file" name="file[]">
			</td>
		</tr>
		<tr>
			<td colspan='2'></td>
			<td>
				<table>
					<tbody id="table1"></tbody>
					<tr><td><a onclick="addMore();" class="linkstyle1" style="cursor:pointer;" title="Add More Files"><u>+Add More</u></a></td></tr>
				</table>
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td class="textstyle1" valign="top">Instructions if any</td>
			<td class="textstyle1" valign="top">:</td>
			<td valign="top">
				<textarea name="instructions" rows="8" cols="65" class="textarea" style="width:450px;height:150px;border:1px solid #000000;color:#444444;font-size:16px;"><?php echo stripslashes(htmlentities($instructions,ENT_QUOTES))?></textarea>
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td class="textstyle1">ETA<font color="#ff0000;">*</font></td>
			<td class="textstyle1">:</td>
			<td>
				<select name="isRushOrder" style="height:25Px;width:150px;border:1px solid #000000;color:#444444;font-size:16px;">
					<!--<option value="">Select</option>-->
					<option value="1">Deliver in 6 Hours</option>
					<!--<?php
						foreach($a_estimatedTimeArray as $key=>$value)
						{
							$select		=	"";
							if($isRushOrder	==	$key)
							{
								$select		=	"selected";
							}

							echo "<option value='$key' $select>$value</option>";
						}
					?>-->
				</select>
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2">
				<div id="progressBar" style="display: none;"><img src="<?php echo SITE_URL;?>/images/progress-bar.gif"/></div>
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2">
				<input type="submit" class="btn btn-large btn-info" name="submit" value="Submit" id="showSubmitDisable" style="cursor:pointer;">
				<input type="hidden" name="memberId" value="<?php echo $memberId;?>">
				<input type="hidden" name="searchCustomer" value="<?php echo $searchCustomer;?>">
				<input type="hidden" name="formSubmitted" value="1">
			</td>
		</tr>
		<tr>
			<td colspan="3" height="10"></td>
		</tr>
	</table>
</form>