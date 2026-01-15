<?php
	if($result	=	$orderObj->getMultipleOrderFiles($orderId,$customerId,1,1))
	{
?>
<tr>
	<td class="smalltext2" valign="top"><b><?php echo $uploadedFileByCustomer;?></b></td>
	<td class="smalltext2" valign="top"><b>:</b></td>
	<td valign="top">
		<table width="100%" align="center" valign="top" border="0" cellpadding="0" cellspacing="0">
			<?php
				$fn	=	0;
				while($row					=	mysql_fetch_assoc($result))
				{
					$fn++;
					$fileId					=	$row['fileId'];
					$uploadingFileName		=	stripslashes($row['uploadingFileName']);
					$uploadingFileExt		=	stripslashes($row['uploadingFileExt']);
					$base_fileId			=	base64_encode($fileId);
					$uploadingFileSize		=	$row['uploadingFileSize'];

					$downLoadPath			=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;

					$downloadedText			=	$orderObj->getMultipleFileCronDownloadedStatus($orderId,$customerId,$fileId);
			?>
			<tr>
				<td width="4%" class="smalltext22" valign="bottom">
					<?php echo $fn;?>)
				</td>
				<td valign="top" width="75%">
					<a class="link_style26" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a>&nbsp;<font class='smalltext20'>
					<?php echo getFileSize($uploadingFileSize)." ".$downloadedText;?></font>
				</td>
			</tr>
			<tr>
				<td height="6"></td>
			</tr>
			<?php
				}
			?>
		</table>
	</td>
</tr>
<?php
	}
	if($result	=	$orderObj->getMultipleOrderFiles($orderId,$customerId,1,2))
	{
?>
<tr>
	<td class="smalltext2" valign="top"><b>Uploaded Public Records File</b></td>
	<td class="smalltext2" valign="top"><b>:</b></td>
	<td valign="top">
		<table width="100%" align="center" valign="top" border="0" cellpadding="0" cellspacing="0">
			<?php
				$fn	=	0;
				while($row					=	mysql_fetch_assoc($result))
				{
					$fn++;
					$fileId					=	$row['fileId'];
					$uploadingFileName		=	stripslashes($row['uploadingFileName']);
					$uploadingFileExt		=	stripslashes($row['uploadingFileExt']);
					$base_fileId			=	base64_encode($fileId);
					$uploadingFileSize		=	$row['uploadingFileSize'];

					$downLoadPath			=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;

					$downloadedText			=	$orderObj->getMultipleFileCronDownloadedStatus($orderId,$customerId,$fileId);
			?>
			<tr>
				<td width="4%" class="smalltext22" valign="bottom">
					<?php echo $fn;?>)
				</td>
				<td valign="top" width="75%">
					<a class="link_style26" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a>&nbsp;<font class='smalltext20'>
					<?php echo getFileSize($uploadingFileSize)." ".$downloadedText;?></font>
				</td>
			</tr>
			<tr>
				<td height="6"></td>
			</tr>
			<?php
				}
			?>
		</table>
	</td>
</tr>
<?php
	}
	if($result	=	$orderObj->getMultipleOrderFiles($orderId,$customerId,1,3))
	{
?>
<tr>
	<td class="smalltext2" valign="top"><b>Uploaded MLS File</b></td>
	<td class="smalltext2" valign="top"><b>:</b></td>
	<td valign="top">
		<table width="100%" align="center" valign="top" border="0" cellpadding="0" cellspacing="0">
			<?php
				$fn	=	0;
				while($row					=	mysql_fetch_assoc($result))
				{
					$fn++;
					$fileId					=	$row['fileId'];
					$uploadingFileName		=	stripslashes($row['uploadingFileName']);
					$uploadingFileExt		=	stripslashes($row['uploadingFileExt']);
					$base_fileId			=	base64_encode($fileId);
					$uploadingFileSize		=	$row['uploadingFileSize'];

					$downLoadPath			=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;

					$downloadedText			=	$orderObj->getMultipleFileCronDownloadedStatus($orderId,$customerId,$fileId);
			?>
			<tr>
				<td width="4%" class="smalltext22" valign="bottom">
					<?php echo $fn;?>)
				</td>
				<td valign="top" width="75%">
					<a class="link_style26" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a>&nbsp;<font class='smalltext20'>
					<?php echo getFileSize($uploadingFileSize)." ".$downloadedText;?></font>
				</td>
			</tr>
			<tr>
				<td height="6"></td>
			</tr>
			<?php
				}
			?>
		</table>
	</td>
</tr>
<?php
	}
	if($result	=	$orderObj->getMultipleOrderFiles($orderId,$customerId,1,4))
	{
?>
<tr>
	<td class="smalltext2" valign="top"><b>Uploaded Market Conditions File</b></td>
	<td class="smalltext2" valign="top"><b>:</b></td>
	<td valign="top">
		<table width="100%" align="center" valign="top" border="0" cellpadding="0" cellspacing="0">
			<?php
				$fn	=	0;
				while($row					=	mysql_fetch_assoc($result))
				{
					$fn++;
					$fileId					=	$row['fileId'];
					$uploadingFileName		=	stripslashes($row['uploadingFileName']);
					$uploadingFileExt		=	stripslashes($row['uploadingFileExt']);
					$base_fileId			=	base64_encode($fileId);
					$uploadingFileSize		=	$row['uploadingFileSize'];

					$downLoadPath			=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;

					$downloadedText			=	$orderObj->getMultipleFileCronDownloadedStatus($orderId,$customerId,$fileId);
			?>
			<tr>
				<td width="4%" class="smalltext22" valign="bottom">
					<?php echo $fn;?>)
				</td>
				<td valign="top" width="75%">
					<a class="link_style26" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a>&nbsp;<font class='smalltext20'>
					<?php echo getFileSize($uploadingFileSize)." ".$downloadedText;?></font>
				</td>
			</tr>
			<tr>
				<td height="6"></td>
			</tr>
			<?php
				}
			?>
		</table>
	</td>
</tr>
<?php
	}
	if($result	=	$orderObj->getMultipleOrderFiles($orderId,$customerId,1,5))
	{
?>
<tr>
	<td class="smalltext2" valign="top"><b>Uploaded Field Inspection Notes</b></td>
	<td class="smalltext2" valign="top"><b>:</b></td>
	<td valign="top">
		<table width="100%" align="center" valign="top" border="0" cellpadding="0" cellspacing="0">
			<?php
				$fn	=	0;
				while($row					=	mysql_fetch_assoc($result))
				{
					$fn++;
					$fileId					=	$row['fileId'];
					$uploadingFileName		=	stripslashes($row['uploadingFileName']);
					$uploadingFileExt		=	stripslashes($row['uploadingFileExt']);
					$base_fileId			=	base64_encode($fileId);
					$uploadingFileSize		=	$row['uploadingFileSize'];

					$downLoadPath			=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;

					
					$downloadedText			=	$orderObj->getMultipleFileCronDownloadedStatus($orderId,$customerId,$fileId);
			?>
			<tr>
				<td width="4%" class="smalltext22" valign="bottom">
					<?php echo $fn;?>)
				</td>
				<td valign="top" width="75%">
					<a class="link_style26" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a>&nbsp;<font class='smalltext20'>
					<?php echo getFileSize($uploadingFileSize)." ".$downloadedText;?></font>
				</td>
			</tr>
			<tr>
				<td height="6"></td>
			</tr>
			<?php
				}
			?>
		</table>
	</td>
</tr>
<?php
	}
	if($result	=	$orderObj->getMultipleOrderFiles($orderId,$customerId,1,6))
	{
?>
<tr>
	<td class="smalltext2" valign="top"><b>Uploaded More Files</b></td>
	<td class="smalltext2" valign="top"><b>:</b></td>
	<td valign="top">
		<table width="100%" align="center" valign="top" border="0" cellpadding="0" cellspacing="0">
			<?php
				$fn	=	0;
				while($row					=	mysql_fetch_assoc($result))
				{
					$fn++;
					$fileId					=	$row['fileId'];
					$uploadingFileName		=	stripslashes($row['uploadingFileName']);
					$uploadingFileExt		=	stripslashes($row['uploadingFileExt']);
					$base_fileId			=	base64_encode($fileId);
					$uploadingFileSize		=	$row['uploadingFileSize'];

					$downLoadPath			=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;

					$downloadedText			=	$orderObj->getMultipleFileCronDownloadedStatus($orderId,$customerId,$fileId);
			?>
			<tr>
				<td width="4%" class="smalltext22" valign="bottom">
					<?php echo $fn;?>)
				</td>
				<td valign="top" width="75%">
					<a class="link_style26" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a>&nbsp;<font class='smalltext20'>
					<?php echo getFileSize($uploadingFileSize)." ".$downloadedText;?></font>
				</td>
			</tr>
			<tr>
				<td height="6"></td>
			</tr>
			<?php
				}
			?>
		</table>
	</td>
</tr>
<?php
	}
?>