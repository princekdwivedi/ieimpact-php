<?php
	if($result	=	$orderObj->getMultipleOrderFiles($orderId,$customerId,1,1))
	{
?>
<tr>
	<td class="smalltext23" valign="top" width="23%"><?php echo $uploadedFileByCustomer;?></td>
	<td class="smalltext23" valign="top" width="1%">:</td>
	<td valign="top">
		<table width="100%" align="center" valign="top" border="0" cellpadding="0" cellspacing="0">
			<?php
				$fn	=	0;
				while($row							=	mysqli_fetch_assoc($result))
				{
					$fn++;
					$fileId							=	$row['fileId'];
					$uploadingFileName				=	stripslashes($row['uploadingFileName']);
					$uploadingFileExt				=	stripslashes($row['uploadingFileExt']);
					$base_fileId					=	base64_encode($fileId);
					$uploadingFileSize				=	$row['uploadingFileSize'];
					$isDownloadedFirstServer		=	$row['isDownloadedFirstServer'];
					$isDownloadedSecondServer		=	$row['isDownloadedSecondServer'];
					

					if(!empty($isDownloadedFirstServer) || !empty($isDownloadedSecondServer))
					{
						$downloadedText				=	"&nbsp;";
					}
					else
					{
						$downloadedText				=	"(File in Queue)";
					}

					$a_customerOrderTemplateFiles[]		=	$uploadingFileName.".".$uploadingFileExt;
					$a_customerOrderTemplateFilesSize[]	=	getFileSize($uploadingFileSize);

					$downLoadPath			=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
			?>
			<tr>
				<td width="2%" class="smalltext2" valign="bottom">
					<?php echo $fn;?>)
				</td>
				<td valign="top" width="75%">
					<a class="link_style32" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a>&nbsp;<font class='smalltext20'>
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
	<td class="smalltext23" valign="top"  width="20%">Uploaded Public Records File</td>
	<td class="smalltext23" valign="top"  width="1%">:</td>
	<td valign="top">
		<table width="100%" align="center" valign="top" border="0" cellpadding="0" cellspacing="0">
			<?php
				$fn	=	0;
				while($row							=	mysqli_fetch_assoc($result))
				{
					$fn++;
					$fileId							=	$row['fileId'];
					$uploadingFileName				=	stripslashes($row['uploadingFileName']);
					$uploadingFileExt				=	stripslashes($row['uploadingFileExt']);
					$base_fileId					=	base64_encode($fileId);
					$uploadingFileSize				=	$row['uploadingFileSize'];
					$isDownloadedFirstServer		=	$row['isDownloadedFirstServer'];
					$isDownloadedSecondServer		=	$row['isDownloadedSecondServer'];
					

					if(!empty($isDownloadedFirstServer) || !empty($isDownloadedSecondServer))
					{
						$downloadedText				=	"&nbsp;";
					}
					else
					{
						$downloadedText				=	"(File in Queue)";
					}

					$a_customerOrderTemplateFiles[]		=	$uploadingFileName.".".$uploadingFileExt;
					$a_customerOrderTemplateFilesSize[]	=	getFileSize($uploadingFileSize);

					$downLoadPath			=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;

			?>
			<tr>
				<td width="2%" class="smalltext2" valign="bottom">
					<?php echo $fn;?>)
				</td>
				<td valign="top" width="75%">
					<a class="link_style32" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a>&nbsp;<font class='smalltext20'>
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
	<td class="smalltext23" valign="top"  width="20%">Uploaded MLS File</td>
	<td class="smalltext23" valign="top" width="1%">:</td>
	<td valign="top">
		<table width="100%" align="center" valign="top" border="0" cellpadding="0" cellspacing="0">
			<?php
				$fn	=	0;
				while($row							=	mysqli_fetch_assoc($result))
				{
					$fn++;
					$fileId							=	$row['fileId'];
					$uploadingFileName				=	stripslashes($row['uploadingFileName']);
					$uploadingFileExt				=	stripslashes($row['uploadingFileExt']);
					$base_fileId					=	base64_encode($fileId);
					$uploadingFileSize				=	$row['uploadingFileSize'];
					$isDownloadedFirstServer		=	$row['isDownloadedFirstServer'];
					$isDownloadedSecondServer		=	$row['isDownloadedSecondServer'];
					

					if(!empty($isDownloadedFirstServer) || !empty($isDownloadedSecondServer))
					{
						$downloadedText					=	"&nbsp;";
					}
					else
					{
						$downloadedText					=	"(File in Queue)";
					}

					$a_customerOrderTemplateFiles[]		=	$uploadingFileName.".".$uploadingFileExt;
					$a_customerOrderTemplateFilesSize[]	=	getFileSize($uploadingFileSize);

					$downLoadPath			=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;

			?>
			<tr>
				<td width="2%" class="smalltext2" valign="bottom">
					<?php echo $fn;?>)
				</td>
				<td valign="top" width="75%">
					<a class="link_style32" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a>&nbsp;<font class='smalltext20'>
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
	<td class="smalltext23" valign="top" width="20%">Uploaded Market Conditions File</td>
	<td class="smalltext23" valign="top" width="1%">:</td>
	<td valign="top">
		<table width="100%" align="center" valign="top" border="0" cellpadding="0" cellspacing="0">
			<?php
				$fn	=	0;
				while($row							=	mysqli_fetch_assoc($result))
				{
					$fn++;
					$fileId							=	$row['fileId'];
					$uploadingFileName				=	stripslashes($row['uploadingFileName']);
					$uploadingFileExt				=	stripslashes($row['uploadingFileExt']);
					$base_fileId					=	base64_encode($fileId);
					$uploadingFileSize				=	$row['uploadingFileSize'];

					$isDownloadedFirstServer		=	$row['isDownloadedFirstServer'];
					$isDownloadedSecondServer		=	$row['isDownloadedSecondServer'];
					

					if(!empty($isDownloadedFirstServer) || !empty($isDownloadedSecondServer))
					{
						$downloadedText					=	"&nbsp;";
					}
					else
					{
						$downloadedText					=	"(File in Queue)";
					}

					$a_customerOrderTemplateFiles[]		=	$uploadingFileName.".".$uploadingFileExt;
					$a_customerOrderTemplateFilesSize[]	=	getFileSize($uploadingFileSize);

					$downLoadPath			=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
			?>
			<tr>
				<td width="2%" class="smalltext2" valign="bottom">
					<?php echo $fn;?>)
				</td>
				<td valign="top" width="75%">
					<a class="link_style32" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a>&nbsp;<font class='smalltext20'>
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
	<td class="smalltext23" valign="top" width="20%">Uploaded Field Inspection Notes</td>
	<td class="smalltext23" valign="top" width="1%">:</td>
	<td valign="top">
		<table width="100%" align="center" valign="top" border="0" cellpadding="0" cellspacing="0">
			<?php
				$fn	=	0;
				while($row								=	mysqli_fetch_assoc($result))
				{
					$fn++;
					$fileId								=	$row['fileId'];
					$uploadingFileName					=	stripslashes($row['uploadingFileName']);
					$uploadingFileExt					=	stripslashes($row['uploadingFileExt']);
					$base_fileId						=	base64_encode($fileId);
					$uploadingFileSize					=	$row['uploadingFileSize'];
					$isDownloadedFirstServer			=	$row['isDownloadedFirstServer'];
					$isDownloadedSecondServer			=	$row['isDownloadedSecondServer'];
					

					if(!empty($isDownloadedFirstServer) || !empty($isDownloadedSecondServer))
					{
						$downloadedText					=	"&nbsp;";
					}
					else
					{
						$downloadedText					=	"(File in Queue)";
					}

					$a_customerOrderTemplateFiles[]		=	$uploadingFileName.".".$uploadingFileExt;
					$a_customerOrderTemplateFilesSize[]	=	getFileSize($uploadingFileSize);

					$downLoadPath			=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
			?>
			<tr>
				<td width="2%" class="smalltext2" valign="bottom">
					<?php echo $fn;?>)
				</td>
				<td valign="top" width="75%">
					<a class="link_style32" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a>&nbsp;<font class='smalltext20'>
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
	<td class="smalltext23" valign="top" width="20%">Uploaded More Files</td>
	<td class="smalltext23" valign="top" width="1%">:</td>
	<td valign="top">
		<table width="100%" align="center" valign="top" border="0" cellpadding="0" cellspacing="0">
			<?php
				$fn	=	0;			
				while($row								=	mysqli_fetch_assoc($result))
				{
					$fn++;
					$fileId								=	$row['fileId'];
					$uploadingFileName					=	stripslashes($row['uploadingFileName']);
					$uploadingFileExt					=	stripslashes($row['uploadingFileExt']);
					$base_fileId						=	base64_encode($fileId);
					$uploadingFileSize					=	$row['uploadingFileSize'];
					$isDownloadedFirstServer			=	$row['isDownloadedFirstServer'];
					$isDownloadedSecondServer			=	$row['isDownloadedSecondServer'];
					

					if(!empty($isDownloadedFirstServer) || !empty($isDownloadedSecondServer))
					{
						$downloadedText					=	"&nbsp;";
					}
					else
					{
						$downloadedText					=	"(File in Queue)";
					}

					$a_customerOrderTemplateFiles[]		=	$uploadingFileName.".".$uploadingFileExt;
					$a_customerOrderTemplateFilesSize[]	=	getFileSize($uploadingFileSize);

					$downLoadPath			=	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;
			?>
			<tr>
				<td width="2%" class="smalltext2" valign="bottom">
					<?php echo $fn;?>)
				</td>
				<td valign="top" width="75%">
					<a class="link_style32" onclick="downloadMultipleOrderFile('<?php echo $downLoadPath;?>');" title="Download" style="cursor:pointer;"><?php echo $uploadingFileName.".".$uploadingFileExt;?></a>&nbsp;<font class='smalltext20'>
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