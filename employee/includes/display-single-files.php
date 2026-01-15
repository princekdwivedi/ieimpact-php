<tr>
	<td class="smalltext23" valign="top" width="20%"><?php echo $uploadedFileByCustomer;?></td>
	<td class="smalltext23" valign="top" width="1%">:</td>
	<td valign="top" class="smalltext24">
		<?php 
			if($hasOrderFile)
			{
				$downloadedText	=	$orderObj->getFileCronDownloadedStatus($orderId,$customerId,"orderFile",0);

				echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=OF&f=N' class='link_style32'>".$orderFileName.".".$orderFileExt."</a><font class='smalltext20'>".getFileSize($orderFileSize)."&nbsp;".$downloadedText."</font>";

				$a_customerOrderTemplateFiles[]		=	$orderFileName.".".$orderFileExt;
				$a_customerOrderTemplateFilesSize[]	=	getFileSize($orderFileSize);

			}
			else
			{
				echo "N/A";
			}
		?>
	</td>
</tr>
<tr>
	<td class="smalltext23" valign="top">Uploaded Public Records File</td>
	<td class="smalltext23" valign="top">:</td>
	<td valign="top" class="smalltext24">
		<?php 
			if($hasPublicRecordFile)
			{
				$downloadedText	=	$orderObj->getFileCronDownloadedStatus($orderId,$customerId,"publicFile",0);
				
				echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=PF&f=N' class='link_style32'>".$publicRecordFileName.".".$publicRecordFileExt."</a><font class='smalltext20'>".getFileSize($publicRecordFileSize)."&nbsp;".$downloadedText."</font>";

				$a_customerOrderTemplateFiles[]		=	$publicRecordFileName.".".$publicRecordFileExt;
				$a_customerOrderTemplateFilesSize[]	=	getFileSize($publicRecordFileSize);
			}
			else
			{
				echo "N/A";
			}
		?>
	</td>
</tr>
<tr>
	<td class="smalltext23" valign="top">Uploaded MLS File</td>
	<td class="smalltext23" valign="top">:</td>
	<td valign="top" class="smalltext24">
		<?php 
			if($hasMlsFile)
			{
				$downloadedText	=	$orderObj->getFileCronDownloadedStatus($orderId,$customerId,"mlsFile",0);

				echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=MF&f=N' class='link_style32'>".$mlsFileName.".".$mlsFileExt."</a><font class='smalltext20'>".getFileSize($mlsFileSize)."&nbsp;".$downloadedText."</font>";

				$a_customerOrderTemplateFiles[]		=	$mlsFileName.".".$mlsFileExt;
				$a_customerOrderTemplateFilesSize[]	=	getFileSize($mlsFileSize);
			}
			else
			{
				echo "N/A";
			}
		?>
	</td>
</tr>
<tr>
	<td class="smalltext23" valign="top">Uploaded Market Conditions File</td>
	<td class="smalltext23" valign="top">:</td>
	<td valign="top" class="smalltext24">
		<?php 
			if($hasMarketConditionFile)
			{
				$downloadedText	=	$orderObj->getFileCronDownloadedStatus($orderId,$customerId,"marketFile",0);

				
				echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=CF&f=N' class='link_style32'>".$marketConditionFileName.".".$marketConditionExt."</a><font class='smalltext20'>".getFileSize($marketConditionFileSize)."&nbsp;".$downloadedText."</font>";

				$a_customerOrderTemplateFiles[]		=	$marketConditionFileName.".".$marketConditionExt;
				$a_customerOrderTemplateFilesSize[]	=	getFileSize($marketConditionFileSize);
			}
			else
			{
				echo "N/A";
			}
		?>
	</td>
</tr>
<tr>
	<td class="smalltext23" valign="top">Uploaded Field Inspection Notes</td>
	<td class="smalltext23" valign="top">:</td>
	<td valign="top" class="smalltext24">
		<?php 
			if($hasOtherFile)
			{
				$downloadedText	=	$orderObj->getFileCronDownloadedStatus($orderId,$customerId,"otherFile",0);
				
				echo "<a href='".SITE_URL_EMPLOYEES."/download.php?ID=$orderId&t=OTF&f=N' class='link_style32'>".$otherFileName.".".$otherFileExt."</a><font class='smalltext20'>".getFileSize($otherFileSize)."&nbsp;".$downloadedText."</font>";

				$a_customerOrderTemplateFiles[]		=	$otherFileName.".".$otherFileExt;
				$a_customerOrderTemplateFilesSize[]	=	getFileSize($otherFileSize);
			}
			else
			{
				echo "N/A";
			}
		?>
	</td>
</tr>
<?php
	$query	=	"SELECT * FROM other_order_files WHERE orderId=$orderId AND uploadingFor=1";
	$result		=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
?>
<tr>
	<td class="smalltext23" valign="top">Uploaded More Files</td>
	<td class="smalltext23" valign="top">:</td>
	<td valign="top">
<?php
		while($row		=	mysqli_fetch_assoc($result))
		{
			$otherId		=	$row['otherId'];
			$fileName		=	$row['fileName'];
			$fileExtension	=	$row['fileExtension'];
			$fileSize		=	$row['fileSize'];

			$downloadedText	=	$orderObj->getFileCronDownloadedStatus($orderId,$customerId,"moreFile",$otherId);

			echo "<a href='".SITE_URL_EMPLOYEES."/other-download.php?ID=$otherId&t=OT' class='link_style32'>".$fileName.".".$fileExtension."</a><font class='smalltext20'>".getFileSize($fileSize)."&nbsp;".$downloadedText."</font>";	
			echo "<br>";

			$a_customerOrderTemplateFiles[]		=	$fileName.".".$fileExtension;
			$a_customerOrderTemplateFilesSize[]	=	getFileSize($fileSize);
			
		}
		echo "</td></tr>";
	}
?>