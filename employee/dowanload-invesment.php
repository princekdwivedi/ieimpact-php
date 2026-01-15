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

	
	$path						=	"";
	$downloadPath				=	"";
	$fileExt					=	"";
	$fileType					=	"";
	$fileName					=	"";
	$investmentId				=	0;
	$employeeId					=	0;
	$tableType					=	"";
	$downloadFileName			=	"";

	if(isset($_GET['type']) && $_GET['type'] != "" && $_GET['type'] == "tax-invesment")
	{
		$tableType				=	"tax-invesment";
	}

	if(isset($_GET['ID']) && $_GET['ID'] != "")
	{
		$investmentId			=	$_GET['ID'];

		if(empty($tableType)){

			$query	=	"SELECT * FROM employee_investment_files WHERE investmentId=$investmentId";
			$result	=	dbQuery($query);
			if(mysql_num_rows($result))
			{
				$row				=	mysql_fetch_assoc($result);
				$investmentOn		=	$row['investmentOn'];
				$fileExt			=	$row['fileExt'];
				$fileType			=	$row['fileType'];
				$fileName			=	$row['fileName'];
				$employeeId			=	$row['employeeId'];

				$file_name			=  "INVESMENT".$investmentId."_".$employeeId."_".$fileName.".".$fileExt;

				$downloadFileName	=	$file_name;
				$downloadPath		=	$filePath.$file_name;
			}
		}
		else{
			$query	=	"SELECT * FROM employee_tax_declaration_files WHERE sectionId=$investmentId";
			$result	=	dbQuery($query);
			if(mysql_num_rows($result))
			{
				$row				=	mysql_fetch_assoc($result);
				$fileExt			=	$row['fileExt'];
				$fileType			=	$row['fileMimeType'];
				$fileName			=	$row['fileName'];
				$employeeId			=	$row['employeeId'];
				$serverPath			=	$row['filePath'];

				$file_name			=  "SAVING_INVESMENT_".$investmentId."_".$employeeId."_".$fileName.".".$fileExt;

				$downloadFileName	=	$file_name;
				$downloadPath		=	$filePath.$serverPath;
			}
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

	if(file_exists($downloadPath))
	{		
		$exactFileSize		=	@filesize($downloadPath);
		if($exactFileSize > 0 && $exactFileSize < "104857600")
		{

			//echo "<br>".$downloadPath;
			 header("Cache-Control: maxage=1"); // Age is in seconds.
			 header("Pragma: public");
		   // header("Content-Type: application/force-download");

			header("Content-Type: " . $fileType);
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