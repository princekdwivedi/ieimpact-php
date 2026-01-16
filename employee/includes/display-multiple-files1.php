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

		// check if the order folder exists
		if (is_dir($downloadPathInfo['dirname']) && file_exists($downloadPathInfo['dirname']) && file_exists($downloadPath)) {
			$downloadPath           =   $downloadPathInfo['dirname'] . "/ocrFiles/$downloadFileName";
			$statusFilePath = $downloadPathInfo['dirname'] . "/ocrFiles/ocr-processing-status.json";
			$statusData = null;
			
			// Check for status file
			if(file_exists($statusFilePath)) {
				$statusContent = @file_get_contents($statusFilePath);
				if($statusContent) {
					$statusData = json_decode($statusContent, true);
				}
			}
			
			if(file_exists($downloadPath)) {
				?>
				<tr>
					<td>&nbsp;</td>
					<td colspan="2" align="left">
						(<a class="link_style13" onclick="downloadMultipleOrderFile('<?php echo SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."&FILE_TYPE=OCR_RESULT";?>');" title="View AI-Extracted Property Details" style="cursor:pointer;"><b>View AI-Extracted Property Details</b></a>)
					</td>
				</tr>

				<?php } else { 
					// Check status file for errors or processing status
					$jobId = null;
					if($statusData && isset($statusData['jobId']) && !empty($statusData['jobId']))
					{
						$jobId = $statusData['jobId'];
					}
					// Also check in response object
					if(empty($jobId) && $statusData && isset($statusData['response']['jobId']) && !empty($statusData['response']['jobId']))
					{
						$jobId = $statusData['response']['jobId'];
					}
					if(empty($jobId) && $statusData && isset($statusData['response']['job_id']) && !empty($statusData['response']['job_id']))
					{
						$jobId = $statusData['response']['job_id'];
					}
					
					if($statusData && isset($statusData['success'])) {
						// Check response status to determine if we should poll
						$responseStatus = null;
						if(isset($statusData['response']['status']))
						{
							$responseStatus = $statusData['response']['status'];
						}
						elseif(isset($statusData['response']['data']['status']))
						{
							$responseStatus = $statusData['response']['data']['status'];
						}
						
						// If we have jobId OR status is pending/processing, start polling
						// Also check if success is false but we have jobId (HTTP 201 case)
						if($jobId || in_array(strtolower($responseStatus), array('pending', 'processing', 'in_progress')) || ($statusData['success'] == false && $jobId))
						{
							// Job ID exists or status indicates processing, start polling automatically
							?>
							<tr>
								<td>&nbsp;</td>
								<td colspan="2" align="left" id="ocrStatusContainer_<?php echo $orderId;?>">
									<span style="color:#0080C0;"><b>Property Details Extraction in Progress...</b></span> <img src="<?php echo SITE_URL;?>/images/loading.gif" border="0" style="vertical-align:middle;width:20px;">
								</td>
							</tr>
							<script type="text/javascript">
							// Auto-start polling when page loads if jobId exists or status is pending/processing
							(function() {
								var orderId = <?php echo $orderId; ?>;
								var encodeOrderID = '<?php echo $encodeOrderID; ?>';
								var mD5OrderID = '<?php echo $M_D_5_ORDERID; ?>';
								var mD5ID = '<?php echo $M_D_5_ID; ?>';
								var jobId = '<?php echo $jobId ? htmlspecialchars($jobId, ENT_QUOTES) : 'null'; ?>';
								
								// Wait for DOM to be ready
								if(document.readyState === 'loading') {
									document.addEventListener('DOMContentLoaded', function() {
										setTimeout(function() {
											if(typeof checkOCRFileStatus === 'function') {
												checkOCRFileStatus(orderId, 'ocrStatusContainer_' + orderId, encodeOrderID, mD5OrderID, mD5ID, jobId !== 'null' ? jobId : null);
											}
										}, 500);
									});
								} else {
									setTimeout(function() {
										if(typeof checkOCRFileStatus === 'function') {
											checkOCRFileStatus(orderId, 'ocrStatusContainer_' + orderId, encodeOrderID, mD5OrderID, mD5ID, jobId !== 'null' ? jobId : null);
										}
									}, 500);
								}
							})();
							</script>
							<?php
						}
						elseif($statusData['success'] == false) {
							// Processing failed - show error and retry option
							$errorMessage = isset($statusData['message']) ? htmlspecialchars($statusData['message']) : 'Unknown error occurred';
							$timestamp = isset($statusData['timestamp']) ? htmlspecialchars($statusData['timestamp']) : '';
							?>
							<tr>
								<td>&nbsp;</td>
								<td colspan="2" align="left" id="ocrStatusContainer_<?php echo $orderId;?>" style="color: red;">
									<span style="color: red;"><b>Error:</b> <?php echo $errorMessage; ?></span>
									<?php if($timestamp): ?>
										<br><span style="font-size: 11px; color: #666;">(Last attempted: <?php echo $timestamp; ?>)</span>
									<?php endif; ?>
									<br>(<a class="link_style13" onclick="getAIExtractedPropertyDetails(<?php echo $orderId;?>, '<?php echo $encodeOrderID;?>', '<?php echo $M_D_5_ORDERID;?>', '<?php echo $M_D_5_ID;?>', null);" title="Retry AI Extraction" style="cursor:pointer; color: #0080C0;"><b>Retry AI Extraction</b></a>)
								</td>
							</tr>
							<?php
						} else {
							// Processing was initiated - check if jobId exists to start polling
							if($jobId) {
								// Job ID exists, start polling automatically
								?>
								<tr>
									<td>&nbsp;</td>
									<td colspan="2" align="left" id="ocrStatusContainer_<?php echo $orderId;?>">
										<span style="color:#0080C0;"><b>Property Details Extraction in Progress...</b></span> <img src="<?php echo SITE_URL;?>/images/loading.gif" border="0" style="vertical-align:middle;width:20px;">
									</td>
								</tr>
								<script type="text/javascript">
								// Auto-start polling when page loads if jobId exists
								(function() {
									var orderId = <?php echo $orderId; ?>;
									var encodeOrderID = '<?php echo $encodeOrderID; ?>';
									var mD5OrderID = '<?php echo $M_D_5_ORDERID; ?>';
									var mD5ID = '<?php echo $M_D_5_ID; ?>';
									var jobId = '<?php echo htmlspecialchars($jobId, ENT_QUOTES); ?>';
									
									// Wait for DOM to be ready
									if(document.readyState === 'loading') {
										document.addEventListener('DOMContentLoaded', function() {
											setTimeout(function() {
												if(typeof checkOCRFileStatus === 'function') {
													checkOCRFileStatus(orderId, 'ocrStatusContainer_' + orderId, encodeOrderID, mD5OrderID, mD5ID, jobId);
												}
											}, 500);
										});
									} else {
										setTimeout(function() {
											if(typeof checkOCRFileStatus === 'function') {
												checkOCRFileStatus(orderId, 'ocrStatusContainer_' + orderId, encodeOrderID, mD5OrderID, mD5ID, jobId);
											}
										}, 500);
									}
								})();
								</script>
								<?php
							} else {
								// No jobId - processing completed but file not found
								?>
								<tr>
									<td>&nbsp;</td>
									<td colspan="2" align="left" id="ocrStatusContainer_<?php echo $orderId;?>">
										<span style="color: #0080C0;">Processing completed but result file not found. </span>
										(<a class="link_style13" onclick="getAIExtractedPropertyDetails(<?php echo $orderId;?>, '<?php echo $encodeOrderID;?>', '<?php echo $M_D_5_ORDERID;?>', '<?php echo $M_D_5_ID;?>', null);" title="Retry AI Extraction" style="cursor:pointer;"><b>Retry AI Extraction</b></a>)
									</td>
								</tr>
								<?php
							}
						}
					} else {
						// No status file - no processing attempted yet
						?>
						<tr>
							<td>&nbsp;</td>
							<td colspan="2" align="left" id="ocrStatusContainer_<?php echo $orderId;?>">
								(<a class="link_style13" onclick="getAIExtractedPropertyDetails(<?php echo $orderId;?>, '<?php echo $encodeOrderID;?>', '<?php echo $M_D_5_ORDERID;?>', '<?php echo $M_D_5_ID;?>', null);" title="Get AI-Extracted Property Details" style="cursor:pointer;"><b>Get AI-Extracted Property Details</b></a>)
							</td>
						</tr>
						<?php
					}
				}
			} else { ?>
				<tr>
					<td>&nbsp;</td>
					<td colspan="2" align="left" style="color: red;">
						Order file folder does not exist. AI extraction is not allowed.
					</td>
				</tr>
			<?php }
		} ?>

		<!--<tr>
			<td>&nbsp;</td>
			<td colspan="2" align="left">									
				(<a class="link_style13" onclick="downloadMultipleOrderFile('<?php echo $messageFiledownLoadPath;?>');" title="Download All Files as ZIP" style="cursor:pointer;"><b>Download All Order Files As .zip</b></a>)
			</td>
		</tr>-->
<?php } ?>