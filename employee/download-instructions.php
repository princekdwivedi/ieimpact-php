<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");

	$path			=	"";
	$downloadPath	=	"";
	$fieldFileName	=	"";
	$mimeTypeField	=	"";
	$instructionId	=	0;
	$memberId		=	0;

	$downloadFileName=	"";
	$instructionsPath=	SITE_ROOT_FILES."/files/instructions/";

	if(empty($s_employeeId))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	if(isset($_GET['ID']) && isset($_GET['memberId']))
	{
		$instructionId		=	(int)$_GET['ID'];
		$memberId			=	(int)$_GET['memberId'];
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$query	=	"SELECT * FROM customer_instructions_file WHERE memberId=$memberId AND instructionId=$instructionId";
	$result	=	mysqli_query($db_conn,$query);
	if(mysqli_num_rows($result))
	{
		$row				=	mysqli_fetch_assoc($result);
		$fileName			=	$row['fileName'];
		$fileExt			=	$row['fileExt'];
		$size				=	$row['size'];
		$mimeType			=	$row['mimeType'];

		$downloadFileName	=	$fileName.".".$fileExt;

		$downloadPath		=	$instructionsPath.$instructionId."_".$fileName.".".$fileExt;
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

			header('Content-Type: application/octet-stream');
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