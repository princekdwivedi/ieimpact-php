<?php
	$showFilesNameInEmail		 =	"";
	$showFilesNameInEmail		.=	"<table width='99%' align='center' border='0' cellpadding='2' cellspacing='2' style='border:2px solid #e4e4e4;'>";
	$showFilesNameInEmail		.=	"<tr><td colspan='3' align='left'><font style='font-size:11px;font-weight:bold;color:#6E6E6E;'>COMPLETED FILES OF THIS ORDER</font></td></tr>";

	$a_attachmentPath			=	array();
	$a_attachmentType			=	array();
	$a_attachmentName			=	array();
	$totalAmountEmailFileSize	=	0;
	

	$query1				=	"SELECT * FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND orderId=$orderId AND replyOrderId=$replyId AND uploadingFor=2 AND isDeleted=0 ORDER BY FIELD(uploadingType,1,7,2,3,6)";
	//////////// ORDER BY FIELD IS TO GET COMPLETED FILES IN ORDER /////////////////////////////
	$result1			=	dbQuery($query1);
	if(mysqli_num_rows($result1))
	{
		while($row1						=	mysqli_fetch_assoc($result1)){
			$fileId						=	$row1['fileId'];
			$uploadingFileName			=	stripslashes($row1['uploadingFileName']);
			$uploadingFileExt			=	stripslashes($row1['uploadingFileExt']);
			$uploadingFileSize			=	$row1['uploadingFileSize'];
			$uploadingType				=   $row1['uploadingType'];
			$mimeTypeField				=	$row1['uploadingFileType'];
			$excatFileNameInServer		=	$row1['excatFileNameInServer'];
			$excatFileNameInServer      =   stringReplace("/home/ieimpact", "", $excatFileNameInServer);

			$base_fileId				=	base64_encode($fileId);

			$downLoadPath				=	SITE_URL_MEMBERS."/download-multiple-file.php?suf=".$baseConvertUniqueEmailCode."&".$M_D_5_ORDERID."=".$encodeOrderID."&".$M_D_5_ID."=".$base_fileId;

			if($uploadingType	 == 1){
				$t_replieddFileToustomer =	stringReplace("Upload ", "", $replieddFileToustomer);
			}
			elseif($uploadingType == 7){
				$t_replieddFileToustomer =	"Completed Report PDF File for Reference";
			}
			elseif($uploadingType == 2){
				$t_replieddFileToustomer =	"Public Records File";
			}
			elseif($uploadingType == 3){
				$t_replieddFileToustomer =	"Plat Map";
			}
			elseif($uploadingType == 6){
				$t_replieddFileToustomer =	"Reply Other File";
			}

			$showFilesNameInEmail	.=	"<tr><td width='35%' valign='top'  align='left'><font style='font-size:10px;color:#4d4d4d;'>".$t_replieddFileToustomer."</font></td><td width='2%' valign='top' align='left'><font style='font-size:10px;color:#4d4d4d;'>:</font></td><td valign='top' align='left'><a href='".$downLoadPath."' target='_blank'><font style='font-size:12px;color:#0082bf;'>".$uploadingFileName.".".$uploadingFileExt."</font></a>".getSizeNoBracket($uploadingFileSize)."</td></tr>";

			$a_attachmentPath[]			=	$excatFileNameInServer;
			$a_attachmentType[]			=	$mimeTypeField;
			$a_attachmentName[]			=	$uploadingFileName.".".$uploadingFileExt;

			$totalAmountEmailFileSize	=	$totalAmountEmailFileSize+$uploadingFileSize;
		}
	}
	$showFilesNameInEmail		.=	"<tr><td colspan='3' height='15'></td></tr><tr><td colspan='3' align='left'><font style='font-size:10px;font-weight:bold;color:#333333;'>Note: Anyone with these links can download these files. Do not forward this email to anybody.</font></td></tr></table>";

?>