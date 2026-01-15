<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
	set_time_limit(0); 
	ini_set('memory_limit', '512M');
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
	$mimeTypeField				=  "";
	$encodeOrderID				=  0;
	$encodeFileID				=  0;
	$M_D_5_ORDERID				=  ORDERID_M_D_5;

	
	$downloadFileName			=   "";

	if(isset($_GET[$M_D_5_ORDERID]))
	{
		$encodeOrderID			=	$_GET[$M_D_5_ORDERID];

		$orderId				=	base64_decode($encodeOrderID);

		if(empty($orderId))
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
	
	
	$query				=	"SELECT order_all_files.*,folderId,orderAddress FROM order_all_files INNER JOIN members ON order_all_files.memberId=members.memberId INNER JOIN members_orders ON order_all_files.orderId=members_orders.orderId WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND order_all_files.orderId=$orderId AND order_all_files.isDeleted=0 AND uploadingFor=1";

	$result		=	dbQuery($query);

	if(mysqli_num_rows($result))
	{
		while($row					=	mysqli_fetch_assoc($result)){
			$downloadPath			=	$row['excatFileNameInServer'];
			
			$downloadPath           =   stringReplace("/home/ieimpact", "", $downloadPath);	
			$uploadingFileType		=	$row['uploadingFileType'];
			$orderAddress			=	stripslashes($row['orderAddress']);
			$folderId			    =	$row['folderId'];
			$orderAddress			=	changeToValidString($orderAddress);
		
			$a_exactPath[]          =   $downloadPath;
		
		}

	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$orderAddress					=	stringReplace(" ","-",$orderAddress);


	//This script is developed by www.webinfopedia.com
	//For more examples in php visit www.webinfopedia.com
	function zipFilesAndDownload($file_names,$archive_file_name,$folderId,$orderAddress)
	{
		$zip = new ZipArchive;
		if ($zip->open($archive_file_name,  ZipArchive::CREATE)) {
			//add each files of $file_name array to archive
			foreach($file_names as $files)
			{
				$addFileNames	=	stringReplace("/WebFiles/files/orderFiles/".$folderId."//","",$files);
				$slashPos		=	strrpos($addFileNames,"/");
				$addFileNames   =   substr($addFileNames, $slashPos);
				$pos            =   strpos($addFileNames, "_");
				if($pos        !== false) {
					$addFileNames = substr($addFileNames,$pos+1);
				}

				$zip->addFile($files,$addFileNames);
			}
		}		
		
		$downloadFileName		=	$orderAddress."-order-files.zip";

		$zip->close();
		//then send the headers to foce download the zip file
		$zipped_size = filesize($archive_file_name);
		header("Content-Description: File Transfer");
		header("Content-type: application/zip"); 
		header("Content-Type: application/force-download");// some browsers need this
		header("Content-Disposition: attachment; filename=$downloadFileName");
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

	//Archive name
	$archive_file_name =  SITE_ROOT_FILES."/files/temp-order-folder/".$orderAddress."-message-files.zip";
	//die();

	@zipFilesAndDownload($a_exactPath,$archive_file_name,$folderId,$orderAddress);

 ?>
