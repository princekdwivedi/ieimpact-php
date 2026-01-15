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

	$messageId		=	0;
	$path			=	"";
	$downloadPath	=	"";
	$downloadFileName=	"";
	$messageFilePath =	SITE_ROOT_FILES."/files/messages/";

	if(isset($_GET['ID']))
	{
		$messageId	=	(int)$_GET['ID'];
		$query		=	"SELECT * FROM members_employee_messages WHERE messageId=$messageId AND hasMessageFiles=1";
		$result		=	dbQuery($query);
		if(mysql_num_rows($result))
		{
			$row			=	mysql_fetch_assoc($result);
			$hasMessageFiles=	$row['hasMessageFiles'];
			$fileName		=	$row['fileName'];
			$fileExtension	=	$row['fileExtension'];
			$fileSize		=	$row['fileSize'];
			$fileMimeType	=	$row['fileMimeType'];


			$downloadFileName=	$fileName.".".$fileExtension;

			$downloadPath	 =	$messageFilePath.$messageId."_".$fileName.".".$fileExtension;
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
