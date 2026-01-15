<?php
	$customer_files_uploaded	=	array();
	if($result					=	$orderObj->getMultipleOrderFiles($orderId,$customerId,1))
	{	
		while($row				=	mysqli_fetch_assoc($result)){
			$uploadingType		=   $row['uploadingType'];
			$customer_files_uploaded[$uploadingType][] = $row;
		}
	}

	$totalCustomerOrderFils 	=	0;
	
	if(array_key_exists(1,$customer_files_uploaded))	
	{
		$first_files	=	$customer_files_uploaded[1];
?>
<tr>
	<td class="smalltext23" valign="top" width="23%"><?php echo $uploadedFileByCustomer;?></td>
	<td class="smalltext23" valign="top" width="1%">:</td>
	<td valign="top">
		<table width="100%" align="center" valign="top" border="0" cellpadding="0" cellspacing="0">
			<?php
				$fn	=	0;
				foreach($first_files as $value)
				{
					$fn++;
					$totalCustomerOrderFils++;
					$fileId							=	$value['fileId'];
					$uploadingFileName				=	stripslashes($value['uploadingFileName']);
					$uploadingFileExt				=	stripslashes($value['uploadingFileExt']);
					$base_fileId					=	base64_encode($fileId);
					$uploadingFileSize				=	$value['uploadingFileSize'];
					$isDownloadedFirstServer		=	$value['isDownloadedFirstServer'];
					$isDownloadedSecondServer		=	$value['isDownloadedSecondServer'];
					

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
	if(array_key_exists(2,$customer_files_uploaded))	
	{
		$second_files	=	$customer_files_uploaded[2];
?>
<tr>
	<td class="smalltext23" valign="top"  width="20%">Uploaded Public Records File</td>
	<td class="smalltext23" valign="top"  width="1%">:</td>
	<td valign="top">
		<table width="100%" align="center" valign="top" border="0" cellpadding="0" cellspacing="0">
			<?php
				$fn	=	0;
				foreach($second_files as $value)
				{
					$fn++;
					$totalCustomerOrderFils++;
					$fileId							=	$value['fileId'];
					$uploadingFileName				=	stripslashes($value['uploadingFileName']);
					$uploadingFileExt				=	stripslashes($value['uploadingFileExt']);
					$base_fileId					=	base64_encode($fileId);
					$uploadingFileSize				=	$value['uploadingFileSize'];
					$isDownloadedFirstServer		=	$value['isDownloadedFirstServer'];
					$isDownloadedSecondServer		=	$value['isDownloadedSecondServer'];
					

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
	if(array_key_exists(3,$customer_files_uploaded))	
	{
		$third_files	=	$customer_files_uploaded[3];
?>
<tr>
	<td class="smalltext23" valign="top"  width="20%">Uploaded MLS File</td>
	<td class="smalltext23" valign="top" width="1%">:</td>
	<td valign="top">
		<table width="100%" align="center" valign="top" border="0" cellpadding="0" cellspacing="0">
			<?php
				$fn	=	0;
				foreach($third_files as $value)
				{
					$fn++;
					$totalCustomerOrderFils++;
					$fileId							=	$value['fileId'];
					$uploadingFileName				=	stripslashes($value['uploadingFileName']);
					$uploadingFileExt				=	stripslashes($value['uploadingFileExt']);
					$base_fileId					=	base64_encode($fileId);
					$uploadingFileSize				=	$value['uploadingFileSize'];
					$isDownloadedFirstServer		=	$value['isDownloadedFirstServer'];
					$isDownloadedSecondServer		=	$value['isDownloadedSecondServer'];
					

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
	if(array_key_exists(4,$customer_files_uploaded))	
	{
		$fourth_files	=	$customer_files_uploaded[4];
?>
<tr>
	<td class="smalltext23" valign="top" width="20%">Uploaded Market Conditions File</td>
	<td class="smalltext23" valign="top" width="1%">:</td>
	<td valign="top">
		<table width="100%" align="center" valign="top" border="0" cellpadding="0" cellspacing="0">
			<?php
				$fn	=	0;
				foreach($fourth_files as $value)
				{
					$fn++;
					$totalCustomerOrderFils++;
					$fileId							=	$value['fileId'];
					$uploadingFileName				=	stripslashes($value['uploadingFileName']);
					$uploadingFileExt				=	stripslashes($value['uploadingFileExt']);
					$base_fileId					=	base64_encode($fileId);
					$uploadingFileSize				=	$value['uploadingFileSize'];

					$isDownloadedFirstServer		=	$value['isDownloadedFirstServer'];
					$isDownloadedSecondServer		=	$value['isDownloadedSecondServer'];
					

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
	if(array_key_exists(5,$customer_files_uploaded))	
	{
		$fifth_files	=	$customer_files_uploaded[5];
?>
<tr>
	<td class="smalltext23" valign="top" width="20%">Uploaded Field Inspection Notes</td>
	<td class="smalltext23" valign="top" width="1%">:</td>
	<td valign="top">
		<table width="100%" align="center" valign="top" border="0" cellpadding="0" cellspacing="0">
			<?php
				$fn	=	0;
				foreach($fifth_files as $value)
				{
					$fn++;
					$totalCustomerOrderFils++;
					$fileId								=	$value['fileId'];
					$uploadingFileName					=	stripslashes($value['uploadingFileName']);
					$uploadingFileExt					=	stripslashes($value['uploadingFileExt']);
					$base_fileId						=	base64_encode($fileId);
					$uploadingFileSize					=	$value['uploadingFileSize'];
					$isDownloadedFirstServer			=	$value['isDownloadedFirstServer'];
					$isDownloadedSecondServer			=	$value['isDownloadedSecondServer'];
					

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
	if(array_key_exists(6,$customer_files_uploaded))	
	{
		$sixth_files	=	$customer_files_uploaded[6];
?>
<tr>
	<td class="smalltext23" valign="top" width="20%">Uploaded More Files</td>
	<td class="smalltext23" valign="top" width="1%">:</td>
	<td valign="top">
		<table width="100%" align="center" valign="top" border="0" cellpadding="0" cellspacing="0">
			<?php
				$fn	=	0;			
				foreach($sixth_files as $value)
				{
					$fn++;
					$totalCustomerOrderFils++;
					$fileId								=	$value['fileId'];
					$uploadingFileName					=	stripslashes($value['uploadingFileName']);
					$uploadingFileExt					=	stripslashes($value['uploadingFileExt']);
					$base_fileId						=	base64_encode($fileId);
					$uploadingFileSize					=	$value['uploadingFileSize'];
					$isDownloadedFirstServer			=	$value['isDownloadedFirstServer'];
					$isDownloadedSecondServer			=	$value['isDownloadedSecondServer'];
					

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

	if($totalCustomerOrderFils > 1){
																						
		$messageFiledownLoadPath	=	SITE_URL_EMPLOYEES."/download-all-order-files.php?".$M_D_5_ORDERID."=".$encodeOrderID;

		$query		=	"SELECT * FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND orderId=$orderId AND isDeleted=0";
		$result		=	mysqli_query($db_conn,$query);
		if(mysqli_num_rows($result))
		{
			$row					=	mysqli_fetch_assoc($result);
			$downloadPath			=	$row['excatFileNameInServer'];
			$downloadPath           =   stringReplace("/home/ieimpact", "", $downloadPath);

			$downloadFileName		=	"extracted-data.pdf";
			$downloadPathInfo		=	pathinfo($downloadPath);
			$downloadPath           =   $downloadPathInfo['dirname'] . "/ocrFiles/$downloadFileName";
			if(file_exists($downloadPath)) {
?>
		<tr>
			<td>&nbsp;</td>
			<td colspan="2" align="left">
				(<a class="link_style13" onclick="downloadMultipleOrderFile('<?php echo SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."&FILE_TYPE=OCR_RESULT";?>');" title="View AI-Extracted Property Details" style="cursor:pointer;"><b>View AI-Extracted Property Details</b></a>)
			</td>
		</tr>

<?php
			}
			else
			{
?>
		<tr>
			<td>&nbsp;</td>
			<td colspan="2" align="left" id="ocrStatusContainer_<?php echo $orderId;?>">
				(<a class="link_style13" onclick="getAIExtractedPropertyDetails(<?php echo $orderId;?>, '<?php echo $encodeOrderID;?>', '<?php echo $M_D_5_ORDERID;?>', '<?php echo $M_D_5_ID;?>');" title="Get AI-Extracted Property Details" style="cursor:pointer;"><b>Get AI-Extracted Property Details</b></a>)
			</td>
		</tr>
<?php
			}
		}
?>

		<!--<tr>
			<td>&nbsp;</td>
			<td colspan="2" align="left">									
				(<a class="link_style13" onclick="downloadMultipleOrderFile('<?php echo $messageFiledownLoadPath;?>');" title="Download All Files as ZIP" style="cursor:pointer;"><b>Download All Order Files As .zip</b></a>)
			</td>
		</tr>-->
<?php
		
	}
?>