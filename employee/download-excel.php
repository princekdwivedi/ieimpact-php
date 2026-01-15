<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	$fileName		=	"";
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	if(empty($s_hasManagerAccess) && empty($s_hasAdminAccess)) 
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$filaneNamePrefixed	=	"123456789";
	$storePath			=	SITE_ROOT_FILES."/files/excel-files/";
	$storeFileName		=	md5($filaneNamePrefixed)."-employee-details.xls";

	if(isset($_GET['t']) && $_GET['t'] != "")
	{
		$fileName				=	$_GET['t'];

		$downloadPath			=	$storePath.$fileName;
		$downloadFileName		=	$fileName;


		if(file_exists($downloadPath))
		{		
			$exactFileSize		=	@filesize($downloadPath);
			if($exactFileSize > 0 && $exactFileSize < "104857600")
			{

				//echo "<br>".$downloadPath;
				 header("Cache-Control: maxage=1"); // Age is in seconds.
				 header("Pragma: public");
			   // header("Content-Type: application/force-download");

				header("Content-Type: application/vnd.ms-excel");
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
	}
	else
	{
		echo "<br><br><center><font color='#ff0000'><b>Oops ! invalid link.</b></font></center>";
	}

?>