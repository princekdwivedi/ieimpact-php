<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();
	$filePath					=	SITE_ROOT_FILES."/files/member-identity/";
	
	$type						=	"";
	$employeeId					=	0;
	$uploadingType 				=	0;

	
	$path						=	"";
	$downloadPath				=	"";
	$andClause					=	"";
	$andClause1					=	"";
	$fieldExt					=	"";
	$fieldFileName				=	"";
	$mimeTypeField				=	"";

	$downloadFileName			=	"";

	if(isset($_GET['T']) && isset($_GET['I']))
	{
		$type					=	$_GET['T'];
		$employeeId				=	(int)$_GET['I'];

		if($type				==	"I")
		{
			$uploadingType 		=	1;
		}
		elseif($type			==	"P")
		{
			$uploadingType 		=	2;
		}
		elseif($type			==	"C")
		{
			$uploadingType 		=	3;
		}
		elseif($type			==	"R")
		{
			$uploadingType 		=	4;
		}
		elseif($type			==	"RP")
		{
			$uploadingType 		=	6;
		}
		elseif($type			==	"IA")
		{
			$uploadingType 		=	7;
		}
		elseif($type			==	"IAL")
		{
			$uploadingType 		=	8;
		}
		elseif($type			==	"IEA")
		{
			$uploadingType 		=	9;
		}
		elseif($type			==	"CRQ")
		{
			$uploadingType 		=	10;
		}
		elseif($type			==	"ELEVEN")
		{
			$uploadingType 		=	12;
		}
		elseif($type			==	"RESIGNED")
		{
			$uploadingType 		=	11;
		}
		elseif($type			==	"ELEVENRES")
		{
			$uploadingType 		=	13;
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
	if(empty($s_hasManagerAccess))
	{
		if($employeeId != $s_employeeId)
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}
	}

	$query	=	"SELECT * FROM employeee_profile_files WHERE employeeId=$employeeId AND type=$uploadingType";
	$result	=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		$row			=	mysqli_fetch_assoc($result);
		$fileTypeName   =	$row['fileTypeName'];
		$fileName		=	$row['fileName'];
		$downloadPath   =	$row['fileServerPath'];
		$downloadPath   =   stringReplace("/home/ieimpact", "", $downloadPath);	


		$fileSize		=	$row['fileSize'];
		$mimeType		=	$row['mimeType'];
		$ext		    =	$row['ext'];

		$downloadFileName=	$fileName.".".$ext;
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

			header("Content-Type: " . $mimeType);
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