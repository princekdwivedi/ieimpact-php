<script type="text/javascript">
function display_loading()
{
	document.getElementById('loading').style.display = 'block';
} 
function checkvalidFile()
{
	form1	=	document.changeImage;
		
	var node = document.getElementById('file');
	var check = node.files[0].fileSize;

	if(1048576 < check)
	{
		
		alert("Upload File upto 1 MB Only.");
		return false;
	}

	form1.submit.value    = "Please wait..We are uploading your image";
	form1.submit.disabled = true;

	display_loading();

 }
</script>
<form name="changeImage" action="" method="POST" enctype="multipart/form-data" onsubmit="return checkvalidFile();">
	<table width="98%" align="center" border="0" cellpadding="3" cellspacing="3">
	<tr>
		<td colspan="3" class='text3'>
			<?php echo $headerText;?>
		</td>
	</tr>
	<?php
		if(!empty($errorMsg))
		{
	?>
	<tr>
		<td colspan="3" class='text3'>
			<?php echo $errorMsg;?>
		</td>
	</tr>
	<?php
		}
		if(!empty($existingPhoto))
		{
	?>
	<tr>
		<td valign="top" class="smalltext2"><b>Existing Photo</b></td>
		<td valign="top"  class="smalltext2"><b>:</b></td>
		<td valign="top">
		<?php
			echo $existingPhoto;
		?>
		</td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td valign="top" class="smalltext2" width="29%"><b>Upload New</b></td>
		<td valign="top" class="smalltext2" width="2%"><b>:</b></td>
		<td valign="top">
			<input type="file" name="editUpload" size="15" id="file">
		</td>
	</tr>
	<tr>
		<td colspan='2'>&nbsp;</td>
		<td class='smalltext1'>
			[Please upload Only .jpg / .gif / .png files with <font color="#ff0000">maximum size of 200 KB</font>]
		</td>
	</tr>
	<tr>
			<td colspan="3" height="5"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td colspan="2">
			<div id="loading" style="display: none;"><img src="<?php echo OFFLINE_IMAGE_PATH;?>/images/ajax-loader.gif" alt="" /></div> 
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
		<td>
			<input type="submit" name="submit" value="Submit">&nbsp;
			<input type="button" name="close" value=" Close " onClick="javascript:window.close();">
			<input type="hidden" name="formSubmitted" value="1">
		</td>
	</tr>
	<tr>
		<td colspan="2" height="20"></td>
	</tr>
</table>
</form>
