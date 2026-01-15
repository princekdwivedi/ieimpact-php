<?php
	$inHouseAddForm 	=	 SITE_ROOT_EMPLOYEES."/forms/add-manage-inhouse-files.php";

	$inhouseOrderText   =    "";
	$inhouseErrorMsg    =    "";

	if(VISITOR_IP_ADDRESS	==	"122.160.167.153"){
		echo CURRENT_DATE_INDIA." - ".CURRENT_TIME_INDIA;
	}

	if(isset($_REQUEST['inhouseFormSubmitted'])){
		extract($_REQUEST);

		$inhouseOrderText   			= trim($inhouseOrderText);


		if(empty($inhouseOrderText)){
			$inhouseErrorMsg           .=    "Please enter Message/Title.";
		}
		if(!empty($_FILES['inhouseFile']['name']))
		{	
			$uploadingFile				=   $_FILES['inhouseFile']['name'];
			$mimeType					=   $_FILES['inhouseFile']['type'];
			$fileSize					=   $_FILES['inhouseFile']['size'];
			$tempName					=	$_FILES['inhouseFile']['tmp_name'];
			$ext						=	getAllTypesFilesExt($uploadingFile);
			$uploadingFileName			=	getUploadedSingleFileName($uploadingFile);
			if($fileSize > MAXIMUM_SINGLE_FILE_SIZE_ALLOWED)
			{
				$inhouseErrorMsg .= "<br />The File you are trying to send is very large. It's size must be less than ".MAXIMUM_SINGLE_FILE_SIZE_ALLOWED_TEXT.".";
			}
		}
		if(empty($inhouseErrorMsg)){
			/////////////////////// START ADDING INHOUSE FILES AND MESSAGE //////////////////////////
			$inhouseOrderText 		    =   makeDBSafe($inhouseOrderText);

			dbQuery("INSERT INTO order_inhouse_messages SET message='$inhouseOrderText',memberId=$customerId,orderId=$orderId,employeeId=$s_employeeId,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',ip='".VISITOR_IP_ADDRESS."'");

			$inhouseId				    =	mysqli_insert_id($db_conn);

			//////////////////////// UPLOAD FILE IF ANY ////////////////////////////////////////////
			if(!empty($_FILES['inhouseFile']['name']))
			{
				$t_uploadingFile		=	makeDBSafe($uploadingFileName);
				//uploadingType         =   20
				//uploadingFor          =   1

				dbQuery("INSERT INTO order_all_files SET uploadingType=20,uploadingFor=1,orderId=$orderId,memberId=$customerId,uploadingFileName='$t_uploadingFile',uploadingFileExt='$ext',uploadingFileType='$mimeType',uploadingFileSize=$fileSize,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',customerZoneDate='".CURRENT_DATE_CUSTOMER_ZONE."',customerZoneTime='".CURRENT_TIME_CUSTOMER_ZONE."',addedFromIp='".VISITOR_IP_ADDRESS."',messageId=$inhouseId,employeeId=$s_employeeId");

				$fileId					=	mysqli_insert_id($db_conn);

				$destFileName			=	$newUploadingPath."/".$fileId."_".$uploadingFileName.".".$ext;

				move_uploaded_file($tempName,$destFileName);

				dbQuery("UPDATE order_all_files SET excatFileNameInServer='$destFileName' WHERE fileId=$fileId AND orderId=$orderId AND messageId=$inhouseId");

				dbQuery("UPDATE order_inhouse_messages SET hasFile=1 WHERE id=$inhouseId");

			}

			$_SESSION['success']  = 1;

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES ."/view-order-others.php?orderId=$orderId&customerId=$customerId&selectedTab=9");
			exit();
		}
	}

	if(isset($_GET['inhouseMessageId']) && isset($_GET['isDeleteInHouse']) && $_GET['isDeleteInHouse'] == 1){
		$inhouseMessageId  = (int)$_GET['inhouseMessageId'];
		if(!empty($inhouseMessageId)){

			$query      = "SELECT * FROM order_inhouse_messages WHERE id=$inhouseMessageId AND memberId=$customerId AND orderId=$orderId ORDER BY id DESC";
			$result     =  dbQuery($query);
			if(mysqli_num_rows($result)){
				$row 					  =	 mysqli_fetch_assoc($result);
				$inhouseHasFile 		  =	 $row['hasFile'];
				if($inhouseHasFile        == 1){
					///////////// DELETE IN HOUSE FILES ////////////////
					$query33     = "SELECT * FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND messageId=$inhouseMessageId AND orderId=$orderId AND memberId=$customerId AND uploadingType=20 AND uploadingFor=1 ";
					$result33    =  dbQuery($query33);
					if(mysqli_num_rows($result33)){
						while($row33  = mysqli_fetch_assoc($result33)){							
							$inhouse_fileId			    =	$row33['fileId'];
							$inhouse_imageOnServerPath	=	$row33['excatFileNameInServer'];
							$inhouse_imageOnServerPath  =   stringReplace("/home/ieimpact", "", $inhouse_imageOnServerPath);


							if(file_exists($inhouse_imageOnServerPath))
							{
								unlink($inhouse_imageOnServerPath);
							}

							dbQuery("DELETE FROM order_all_files WHERE fileId=$inhouse_fileId");
							
						}
					}
				}
				$_SESSION['delete_success'] = 1;
				dbQuery("DELETE FROM order_inhouse_messages WHERE id=$inhouseMessageId");
			}
		}
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES ."/view-order-others.php?orderId=$orderId&customerId=$customerId&selectedTab=9");
		exit();

	}


?>
<table width="98%" align="center" border="0" cellpadding="3" cellspacing="2">
	<tr>
		<td colspan="3" class="smalltext24">
			<b><font color='#ff0000;'>IN HOUSE USE FOR THIS ORDER ONLY</font></b>&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan="3" class="smalltext24">
			<?php
				include($inHouseAddForm);
			?>
		</td>
	</tr>

<?php
	$query = "SELECT order_inhouse_messages.*,fullName FROM order_inhouse_messages LEFT JOIN employee_details ON order_inhouse_messages.employeeId=employee_details.employeeId WHERE memberId=$customerId AND orderId=$orderId ORDER BY id DESC";
	$result=  dbQuery($query);
	if(mysqli_num_rows($result)){

		$allFilesUploadedInHouse  = array();

		$query33     = "SELECT * FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND uploadingType=20 AND uploadingFor=1 AND orderId=$orderId AND memberId=$customerId";
		$result33    =  dbQuery($query33);
		if(mysqli_num_rows($result33)){
			while($row33  = mysqli_fetch_assoc($result33)){
				$inhouse_fileId			    =	$row33['fileId'];
				$inhouse_fileName		    =	stripslashes($row33['uploadingFileName']);
				$inhouse_fileExtension	    =	$row33['uploadingFileExt'];
				$inhouse_fileSize		    =	$row33['uploadingFileSize'];
				$inhouse_imageOnServerPath	=	$row33['excatFileNameInServer'];
				$inhouse_messageId          =   $row33['messageId'];

				$allFilesUploadedInHouse[$inhouse_messageId] = $inhouse_fileId."<=>".$inhouse_fileName."<=>".$inhouse_fileExtension."<=>".$inhouse_fileSize;
			}
		}


?>
<tr>
	<td colspan="3">
		<table width='98%' align='center' cellpadding='0' cellspacing='0' border='0'>			
<?php
		$countHouse	                  =	  0;
		while($row                    =   mysqli_fetch_assoc($result)){
			$countHouse++;
			$inhouseid 				  =	 $row['id'];
			$inhouseMessage		      =	 stripslashes($row['message']);
			$inhouseEmployeeId 		  =	 $row['employeeId'];
			$inhouseAddedOn			  =	 $row['addedOn'];
			$inhouseAddedTime		  =  $row['addedTime'];
			$inhouseHasFile 		  =	 $row['hasFile'];

			if(isset($a_allDeactivatedEmployees) && !empty($a_allDeactivatedEmployees)  && array_key_exists($inhouseEmployeeId,$a_allDeactivatedEmployees)){
				$inhouseDoneByName    =  "Hemant Jindal";
				$inhouseEmployeeId    =  137;
			}
			else{
				$inhouseDoneByName    =  stripslashes($row['fullName']);
			}
			$inhouse_file_details     = "";
			if(!empty($inhouseHasFile) && array_key_exists($inhouseid,$allFilesUploadedInHouse)){
				$inhouse_file_details = $allFilesUploadedInHouse[$inhouseid];
			}

			$houseMessageTime         = showDateTimeFormat($inhouseAddedOn,$inhouseAddedTime);

	?>	
	<tr>
		<td class="textstyle2" valign="top"><b><?php echo $inhouseDoneByName;?> &nbsp;on&nbsp;<?php echo $houseMessageTime;?>&nbsp;&nbsp;<img src="<?php echo SITE_URL;?>/images/c_delete.gif" title="Delete" style="cursor:pointer;" onclick="deleteInhouseMessage(<?php echo $customerId;?>,<?php echo $orderId;?>,<?php echo $inhouseid;?>);"></b></td>
	</tr>
	<tr>
		<td height="3"></td>
	</tr>
	<tr>
		<td class="textstyle1" valign="top"><?php echo $inhouseMessage;?></td>
	</tr>
	<tr>
		<td height="5"></td>
	</tr>
	<?php
		if(!empty($inhouse_file_details)){
			list($inhouse_fileId,$inhouse_fileName,$inhouse_fileExtension,$inhouse_fileSize) = explode("<=>",$inhouse_file_details);

			$base_inhouse_fileId =	base64_encode($inhouse_fileId);
									
			$downLoadInhousePath =	SITE_URL_EMPLOYEES."/download-multiple-file.php?".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_inhouse_fileId;
	?>
		<tr>
			<td>
				<img src="<?php echo SITE_URL;?>/images/paperclip.png" height="20" width="20"><a class="link_style32" onclick="downloadMultipleOrderFile('<?php echo $downLoadInhousePath;?>');" title="Download Inhouse File" style="cursor:pointer;"><?php echo $inhouse_fileName.".".$inhouse_fileExtension;?></a>&nbsp;&nbsp;<font class='smalltext20'><?php echo getFileSize($inhouse_fileSize);?></font>
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
	<?php
		}
	}
?>
		</table>
	</td>
</tr>
<?php
	}
	else{
?>
<tr>
	<td colspan="2">&nbsp;</td>
	<td class="textstyle1">
			<font color="#ff0000"><b>No Message/Files Added Yet</b></fonnt>
	</td>
</tr>
<tr>
	<td height="10"></td>
</tr>
<?php
	}
?>
</table>