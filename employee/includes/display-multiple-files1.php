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
				// Fetch feedback for this order
				$feedbackQuery = "SELECT f.*, e.fullName as employeeName 
								  FROM ocr_data_feedback f 
								  LEFT JOIN employee_details e ON f.userId = e.employeeId 
								  WHERE f.orderId = $orderId 
								  ORDER BY f.addedOn DESC, f.addedTime DESC";
				$feedbackResult = dbQuery($feedbackQuery);
				$feedbackCount = mysqli_num_rows($feedbackResult);
				?>
				<tr>
					<td>&nbsp;</td>
					<td colspan="2" align="left">
						(<a class="link_style13" onclick="downloadMultipleOrderFile('<?php echo SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."&FILE_TYPE=OCR_RESULT";?>');" title="View AI-Extracted Property Details" style="cursor:pointer;"><b>View AI-Extracted Property Details</b></a>)
						&nbsp;|&nbsp;
						(<a class="link_style13" onclick="openOCRFeedbackModal(<?php echo $orderId;?>);" title="Provide Feedback" style="cursor:pointer; color: #0080C0;"><b>Add Feedback</b></a>)
						<?php if($feedbackCount > 0): ?>
							&nbsp;|&nbsp;
							<span style="color: #666; font-size: 11px;">(<?php echo $feedbackCount; ?> feedback<?php echo $feedbackCount > 1 ? 's' : ''; ?> submitted)</span>
						<?php endif; ?>
					</td>
				</tr>
				
				<?php if($feedbackCount > 0): ?>
				<tr>
					<td>&nbsp;</td>
					<td colspan="2" align="left" style="padding-top: 10px;">
						<div style="border: 1px solid #ddd; padding: 10px; background: #f9f9f9; border-radius: 4px; max-height: 300px; overflow-y: auto;">
							<strong style="color: #333; font-size: 13px;">Submitted Feedback (<?php echo $feedbackCount; ?>):</strong>
							<?php 
							$fbIndex = 0;
							while($feedbackRow = mysqli_fetch_assoc($feedbackResult)): 
								$fbIndex++;
								$feedbackText = stripslashes($feedbackRow['feedbackText']);
								$feedbackFiles = json_decode($feedbackRow['feedbackFiles'], true);
								$employeeName = !empty($feedbackRow['employeeName']) ? stripslashes($feedbackRow['employeeName']) : 'Unknown User';
								$feedbackDate = $feedbackRow['addedOn'];
								$feedbackTime = $feedbackRow['addedTime'];
								$feedbackDateTime = $feedbackDate . ' ' . $feedbackTime;
							?>
							<div style="border-bottom: 1px solid #e0e0e0; padding: 10px 0; <?php echo $fbIndex < $feedbackCount ? 'margin-bottom: 10px;' : ''; ?>">
								<div style="margin-bottom: 5px;">
									<span style="font-weight: bold; color: #0080C0; font-size: 12px;"><?php echo htmlspecialchars($employeeName); ?></span>
									<span style="color: #999; font-size: 11px; margin-left: 10px;"><?php echo date('M d, Y h:i A', strtotime($feedbackDateTime)); ?></span>
								</div>
								<?php if(!empty($feedbackText)): ?>
								<div style="color: #333; font-size: 12px; margin: 5px 0; padding: 5px; background: #fff; border-left: 3px solid #0080C0;">
									<?php echo nl2br(htmlspecialchars($feedbackText)); ?>
								</div>
								<?php endif; ?>
								<?php if(!empty($feedbackFiles) && is_array($feedbackFiles) && count($feedbackFiles) > 0): ?>
								<div style="margin-top: 5px; font-size: 11px;">
									<strong style="color: #666;">Files:</strong>
									<?php 
									$fileLinks = array();
									foreach($feedbackFiles as $file):
										if(isset($file['path']) && file_exists($file['path'])):
											$fileId = base64_encode($feedbackRow['id'] . '_' . $file['savedName']);
											$fileLink = SITE_URL_EMPLOYEES . "/download-ocr-feedback-file.php?fileId=" . urlencode($fileId);
											$fileLinks[] = '<a href="' . $fileLink . '" target="_blank" style="color: #0080C0; text-decoration: underline;">' . htmlspecialchars($file['originalName']) . '</a> (' . getFileSize($file['size']) . ')';
										endif;
									endforeach;
									if(!empty($fileLinks)):
										echo '<br>' . implode('<br>', $fileLinks);
									endif;
									?>
								</div>
								<?php endif; ?>
							</div>
							<?php endwhile; ?>
						</div>
					</td>
				</tr>
				<?php endif; ?>

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

<!-- OCR Feedback Modal -->
<style>
.ocr-feedback-modal-overlay {
	display: none;
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background-color: rgba(0, 0, 0, 0.5);
	z-index: 10000;
	animation: fadeIn 0.3s ease;
}

.ocr-feedback-modal-container {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	background: #fff;
	border-radius: 8px;
	box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
	width: 90%;
	max-width: 600px;
	max-height: 90vh;
	overflow-y: auto;
}

.ocr-feedback-modal-header {
	padding: 20px;
	border-bottom: 1px solid #ddd;
	display: flex;
	justify-content: space-between;
	align-items: center;
	background: #f8f9fa;
	border-radius: 8px 8px 0 0;
}

.ocr-feedback-modal-header h2 {
	margin: 0;
	font-size: 20px;
	color: #333;
}

.ocr-feedback-modal-close {
	background: none;
	border: none;
	font-size: 28px;
	cursor: pointer;
	color: #666;
	padding: 0;
	width: 30px;
	height: 30px;
	line-height: 30px;
}

.ocr-feedback-modal-close:hover {
	color: #000;
}

.ocr-feedback-modal-body {
	padding: 20px;
}

.ocr-feedback-form-group {
	margin-bottom: 20px;
}

.ocr-feedback-form-group label {
	display: block;
	margin-bottom: 8px;
	font-weight: bold;
	color: #333;
	font-size: 14px;
}

.ocr-feedback-form-group textarea {
	width: 100%;
	padding: 10px;
	border: 1px solid #ddd;
	border-radius: 4px;
	font-size: 14px;
	font-family: Arial, sans-serif;
	resize: vertical;
	min-height: 120px;
	box-sizing: border-box;
}

.ocr-feedback-form-group input[type="file"] {
	width: 100%;
	padding: 8px;
	border: 1px solid #ddd;
	border-radius: 4px;
	font-size: 14px;
	box-sizing: border-box;
}

.ocr-feedback-file-list {
	margin-top: 10px;
	padding: 10px;
	background: #f8f9fa;
	border-radius: 4px;
	font-size: 12px;
}

.ocr-feedback-file-item {
	padding: 5px 0;
	border-bottom: 1px solid #e0e0e0;
}

.ocr-feedback-file-item:last-child {
	border-bottom: none;
}

.ocr-feedback-modal-button-group {
	display: flex;
	justify-content: flex-end;
	gap: 10px;
	margin-top: 20px;
}

.ocr-feedback-btn {
	padding: 10px 20px;
	border: none;
	border-radius: 4px;
	cursor: pointer;
	font-size: 14px;
	font-weight: bold;
}

.ocr-feedback-btn-primary {
	background: #0080C0;
	color: #fff;
}

.ocr-feedback-btn-primary:hover {
	background: #006699;
}

.ocr-feedback-btn-secondary {
	background: #ccc;
	color: #333;
}

.ocr-feedback-btn-secondary:hover {
	background: #bbb;
}

.ocr-feedback-error {
	background: #fee;
	color: #c33;
	padding: 10px;
	border-radius: 4px;
	margin-bottom: 15px;
	display: none;
}

.ocr-feedback-success {
	background: #efe;
	color: #3c3;
	padding: 10px;
	border-radius: 4px;
	margin-bottom: 15px;
	display: none;
}

.ocr-feedback-loading {
	text-align: center;
	padding: 20px;
	display: none;
}

@keyframes fadeIn {
	from { opacity: 0; }
	to { opacity: 1; }
}

@keyframes fadeOut {
	from { opacity: 1; }
	to { opacity: 0; }
}
</style>

<div class="ocr-feedback-modal-overlay" id="ocrFeedbackModal">
	<div class="ocr-feedback-modal-container">
		<div class="ocr-feedback-modal-header">
			<h2>OCR Report Feedback</h2>
			<button type="button" class="ocr-feedback-modal-close" onclick="closeOCRFeedbackModal()" aria-label="Close">&times;</button>
		</div>
		<div class="ocr-feedback-modal-body">
			<div id="ocrFeedbackError" class="ocr-feedback-error"></div>
			<div id="ocrFeedbackSuccess" class="ocr-feedback-success"></div>
			<div id="ocrFeedbackLoading" class="ocr-feedback-loading">
				<img src="<?php echo SITE_URL;?>/images/loading.gif" border="0" style="vertical-align:middle;width:20px;"> Submitting feedback...
			</div>
			
			<form id="ocrFeedbackForm" onsubmit="return submitOCRFeedback(event);">
				<div class="ocr-feedback-form-group">
					<label for="ocrFeedbackText">Feedback <span style="color: #999;">(Optional)</span></label>
					<textarea id="ocrFeedbackText" name="feedbackText" placeholder="Please provide your feedback about the OCR extracted report..." rows="6"></textarea>
				</div>
				
				<div class="ocr-feedback-form-group">
					<label for="ocrFeedbackFiles">Upload Files <span style="color: #999;">(Optional, Multiple files allowed)</span></label>
					<input type="file" id="ocrFeedbackFiles" name="feedbackFiles[]" multiple accept="*/*">
					<div id="ocrFeedbackFileList" class="ocr-feedback-file-list" style="display: none;"></div>
				</div>
				
				<div class="ocr-feedback-modal-button-group">
					<button type="button" class="ocr-feedback-btn ocr-feedback-btn-secondary" onclick="closeOCRFeedbackModal()">Cancel</button>
					<button type="submit" class="ocr-feedback-btn ocr-feedback-btn-primary" id="ocrFeedbackSubmit">Submit Feedback</button>
				</div>
				
				<input type="hidden" id="ocrFeedbackOrderId" name="orderId" value="">
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
var currentOCRFeedbackOrderId = null;

function openOCRFeedbackModal(orderId) {
	currentOCRFeedbackOrderId = orderId;
	document.getElementById('ocrFeedbackOrderId').value = orderId;
	document.getElementById('ocrFeedbackForm').reset();
	document.getElementById('ocrFeedbackError').style.display = 'none';
	document.getElementById('ocrFeedbackSuccess').style.display = 'none';
	document.getElementById('ocrFeedbackFileList').style.display = 'none';
	document.getElementById('ocrFeedbackFileList').innerHTML = '';
	document.getElementById('ocrFeedbackModal').style.display = 'block';
	document.body.style.overflow = 'hidden';
}

function closeOCRFeedbackModal() {
	document.getElementById('ocrFeedbackModal').style.display = 'none';
	document.body.style.overflow = '';
	currentOCRFeedbackOrderId = null;
}

// Close modal when clicking outside
document.getElementById('ocrFeedbackModal').addEventListener('click', function(e) {
	if(e.target === this) {
		closeOCRFeedbackModal();
	}
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
	if(e.key === 'Escape' && document.getElementById('ocrFeedbackModal').style.display === 'block') {
		closeOCRFeedbackModal();
	}
});

// Show selected files
document.getElementById('ocrFeedbackFiles').addEventListener('change', function(e) {
	var fileList = document.getElementById('ocrFeedbackFileList');
	if(this.files.length > 0) {
		fileList.style.display = 'block';
		var html = '<strong>Selected Files:</strong><br>';
		for(var i = 0; i < this.files.length; i++) {
			html += '<div class="ocr-feedback-file-item">' + (i + 1) + '. ' + this.files[i].name + ' (' + formatFileSize(this.files[i].size) + ')</div>';
		}
		fileList.innerHTML = html;
	} else {
		fileList.style.display = 'none';
		fileList.innerHTML = '';
	}
});

function formatFileSize(bytes) {
	if(bytes === 0) return '0 Bytes';
	var k = 1024;
	var sizes = ['Bytes', 'KB', 'MB', 'GB'];
	var i = Math.floor(Math.log(bytes) / Math.log(k));
	return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

function submitOCRFeedback(event) {
	event.preventDefault();
	
	var form = document.getElementById('ocrFeedbackForm');
	var orderId = document.getElementById('ocrFeedbackOrderId').value;
	var feedbackText = document.getElementById('ocrFeedbackText').value.trim();
	var files = document.getElementById('ocrFeedbackFiles').files;
	var errorDiv = document.getElementById('ocrFeedbackError');
	var successDiv = document.getElementById('ocrFeedbackSuccess');
	var loadingDiv = document.getElementById('ocrFeedbackLoading');
	var submitBtn = document.getElementById('ocrFeedbackSubmit');
	
	// Hide previous messages
	errorDiv.style.display = 'none';
	successDiv.style.display = 'none';
	
	if(!orderId) {
		errorDiv.innerHTML = 'Order ID is missing.';
		errorDiv.style.display = 'block';
		return false;
	}
	
	// Show loading
	loadingDiv.style.display = 'block';
	form.style.opacity = '0.5';
	submitBtn.disabled = true;
	
	// Prepare form data
	var formData = new FormData();
	formData.append('orderId', orderId);
	formData.append('feedbackText', feedbackText);
	
	// Add files
	for(var i = 0; i < files.length; i++) {
		formData.append('feedbackFiles[]', files[i]);
	}
	
	// Submit via AJAX
	var xhr = new XMLHttpRequest();
	xhr.open('POST', '<?php echo SITE_URL_EMPLOYEES;?>/submit-ocr-feedback.php', true);
	
	xhr.onload = function() {
		loadingDiv.style.display = 'none';
		form.style.opacity = '1';
		submitBtn.disabled = false;
		
		if(xhr.status === 200) {
			try {
				var response = JSON.parse(xhr.responseText);
				if(response.success) {
					successDiv.innerHTML = '<strong>Success!</strong> ' + (response.message || 'Your feedback has been submitted successfully.');
					successDiv.style.display = 'block';
					form.reset();
					document.getElementById('ocrFeedbackFileList').style.display = 'none';
					document.getElementById('ocrFeedbackFileList').innerHTML = '';
					
					// Auto close after 3 seconds
					setTimeout(function() {
						closeOCRFeedbackModal();
					}, 3000);
				} else {
					errorDiv.innerHTML = response.message || 'An error occurred. Please try again.';
					errorDiv.style.display = 'block';
				}
			} catch(e) {
				errorDiv.innerHTML = 'An error occurred. Please try again.';
				errorDiv.style.display = 'block';
			}
		} else {
			errorDiv.innerHTML = 'An error occurred. Please try again.';
			errorDiv.style.display = 'block';
		}
	};
	
	xhr.onerror = function() {
		loadingDiv.style.display = 'none';
		form.style.opacity = '1';
		submitBtn.disabled = false;
		errorDiv.innerHTML = 'Network error. Please check your connection and try again.';
		errorDiv.style.display = 'block';
	};
	
	xhr.send(formData);
	return false;
}
</script>