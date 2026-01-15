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
	include(SITE_ROOT_EMPLOYEES . "/includes/check-suport-access.php");
	
	$employeeObj	=  new employee();
	$supportId		 =	0;
	$supportReplyId	 =	0;
	$path			 =	"";
	$downloadPath	 =	"";
	$downloadFileName=	"";
	$supportFilePath =  SITE_ROOT_FILES."/files/support/";

	if(isset($_GET['id']))
	{
		$supportId	=	(int)$_GET['id'];
		$query		=	"SELECT * FROM support_master WHERE supportId=$supportId AND hasUploadedFile=1";
		$result		=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			$row			=	mysql_fetch_assoc($result);
			$fileName		=	$row['fileName'];
			$fileMimeType	=	$row['fileMimeType'];
			$fileSize		=	$row['fileSize'];
			$ext			=	$row['ext'];

			$downloadFileName=	$supportId.".".$ext;

			$downloadPath	 =	$supportFilePath.$downloadFileName;
			if(isset($_GET['rid']))
			{
				$supportReplyId	=	(int)$_GET['rid'];
				$query			=	"SELECT * FROM support_replies WHERE supportReplyId=$supportReplyId AND supportId=$supportId AND isUploadedFile=1";
				$result		=	dbQuery($query);
				if(mysql_num_rows($result))
				{
					$row			=	mysql_fetch_assoc($result);
					$fileName		=	$row['fileName'];
					$fileMimeType	=	$row['fileMimeType'];
					$fileSize		=	$row['fileSize'];
					$ext			=	$row['ext'];

					$downloadFileName=	$supportId."_".$supportReplyId.".".$ext;

					$downloadPath	 =	$supportFilePath.$downloadFileName;
				}
				else
				{
					ob_clean();
					header("Location: ".SITE_URL_EMPLOYEES);
					exit();
				}
			}
		}
		else
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

	//echo "<br>".$downloadPath;

	if(file_exists($downloadPath))
	{		
		$exactFileSize		=	@filesize($downloadPath);
		if($exactFileSize > 0 && $exactFileSize < "104857600")
		{

			//echo "<br>".$downloadPath;
			 header("Cache-Control: maxage=1"); // Age is in seconds.
			 header("Pragma: public");
		   // header("Content-Type: application/force-download");

			header("Content-Type: " . $fileMimeType);
			header("Content-Length: " .(string)(filesize($downloadPath)) );
			header('Content-Disposition: attachment; filename="'.basename($downloadFileName).'"');
			header("Content-Transfer-Encoding: binary\n");

		  //readfile($downloadPath); // outputs the content of the file
		  //echo file_get_contents($downloadPath);
		  // If it's a large file, readfile might not be able to do it in one go, so:
			$chunksize = 1 * (1024 * 1024); // how many bytes per chunk

		   	$handle		= fopen($downloadPath, 'rb');
			$buffer     = '';
			if($handle)
			{
				while (!feof($handle))
				{
					$buffer = fread($handle, $chunksize);
					if(!($buffer))
					{
						break;
					}
					echo $buffer;
					ob_flush();
					flush();
				}
				fclose($handle);
			}
			exit();

		}
		else
		{
			include(SITE_ROOT_EMPLOYEES   .   "/includes/file-not-found-page.php");
		}
	}
	else
	{
		include(SITE_ROOT_EMPLOYEES   .   "/includes/file-not-found-page.php");
	}

?>