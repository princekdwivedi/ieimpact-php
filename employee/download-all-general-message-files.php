<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	$employeeObj				=  new employee();
	$memberObj					=  new members();
	$orderObj					=  new orders();

	$path						=  "";
	$downloadPath				=  "";
	$andClause					=  "";
	$andClause1					=  "";
	$fieldExt					=  "";
	$fieldFileName				=  "";
	$encodeMemberID				=  "";
	$encodeOrderID				=  0;
	$parentId				    =  0;
	$M_D_5_ORDERID				=  ORDERID_M_D_5;
	$M_D_5_ID					=  ID_M_D_5;
	$orderGeneralMessagePath	=	SITE_ROOT_FILES."/files/general-messages/";
	
	$downloadFileName			=   "";

	if(isset($_GET[$M_D_5_ORDERID]) && isset($_GET[$M_D_5_ID]))
	{
		$encodeMemberID			=	$_GET[$M_D_5_ORDERID];
		$encodeMessageID		=	$_GET[$M_D_5_ID];

		$memberId				=	base64_decode($encodeMemberID);
		$parentId				=	base64_decode($encodeMessageID);

		if(empty($memberId) || empty($parentId))
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$a_exactPath		=	array();
	
	
	$query			=	"SELECT customer_general_message_files.*,folderId,completeName FROM customer_general_message_files INNER JOIN members ON customer_general_message_files.memberId=members.memberId WHERE parentId=$parentId AND customer_general_message_files.memberId=$memberId";
	$result		=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row					=	mysqli_fetch_assoc($result)){
			$fileName				=	$row['fileName'];
			$fileExtension			=	$row['fileExt'];
			$fileSize				=	$row['fileSize'];
			$fileMimeType			=	$row['fileType'];
			$memberId				=	$row['memberId'];
			$folderId				=	$row['folderId'];
			$completeName			=	$row['completeName'];
			$fileId					=	$row['fileId'];


			$messagePath			=	$orderGeneralMessagePath.$folderId."/";

			$downloadPath           =   $messagePath.$fileId."_".$fileName.".".$fileExtension;

			$a_exactPath[]          =  $downloadPath;
		}

	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}


	//This script is developed by www.webinfopedia.com
	//For more examples in php visit www.webinfopedia.com
	function zipFilesAndDownload($file_names,$archive_file_name,$folderId)
	{
		$zip = new ZipArchive;
		if ($zip->open($archive_file_name,  ZipArchive::CREATE)) {
			//add each files of $file_name array to archive
			foreach($file_names as $files)
			{
				$addFileNames	=	stringReplace("/home/ieimpact/WebFiles/files/general-messages/".$folderId."//","",$files);
				$slashPos		=	strrpos($addFileNames,"/");
				$addFileNames   =   substr($addFileNames, $slashPos);
				$zip->addFile($files,$addFileNames);
			}
		}
		
		$zip->close();
		//then send the headers to foce download the zip file
		$zipped_size = filesize($archive_file_name);
		header("Content-Description: File Transfer");
		header("Content-type: application/zip"); 
		header("Content-Type: application/force-download");// some browsers need this
		header("Content-Disposition: attachment; filename=customer-general-message-files.zip");
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header("Content-Length:". " $zipped_size");
		ob_clean();
		flush();
		readfile("$archive_file_name");
		unlink("$archive_file_name"); // Now delete the temp file (some servers need this option)
		exit; 
	}

	$completeName      =  stringReplace(" ","-",$completeName);

	$archive_file_name =  SITE_ROOT_FILES."/files/temp-order-folder/".$completeName."-general-message-files.zip";

	//pr($a_exactPath);

	@zipFilesAndDownload($a_exactPath,$archive_file_name,$folderId);
 ?>
