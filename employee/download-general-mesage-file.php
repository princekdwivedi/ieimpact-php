<?php
	ob_start();
	session_start();
	ini_set('display_errors', '1');
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	$employeeObj				=  new employee();
	$memberObj					=  new members();
	$orderObj					=  new orders();
	$M_D_5_ID					=	ID_M_D_5;
	$orderGeneralMessagePath	=	SITE_ROOT_FILES."/files/general-messages/";

	$fileId						=	0;
	$path						=	"";
	$downloadPath				=	"";
	$downloadFileName			=	"";

	if(isset($_GET[$M_D_5_ID]))
	{
		$encodeFileID			=	$_GET[$M_D_5_ID];
		$fileId					=	base64_decode($encodeFileID);
		$query					=	"SELECT customer_general_message_files.*,folderId FROM customer_general_message_files INNER JOIN members ON customer_general_message_files.memberId=members.memberId WHERE fileId=$fileId";
		$result					=	mysqli_query($db_conn,$query);
		if(mysqli_num_rows($result))
		{
			$row			=	mysqli_fetch_assoc($result);
			$fileName		=	$row['fileName'];
			$fileExtension	=	$row['fileExt'];
			$fileSize		=	$row['fileSize'];
			$fileMimeType	=	$row['fileType'];
			$memberId		=	$row['memberId'];
			$folderId		=	$row['folderId'];

			$orderGeneralMessagePath	=	$orderGeneralMessagePath.$folderId."/";


			$downloadFileName=	$fileName.".".$fileExtension;

			$downloadPath	 =	$orderGeneralMessagePath.$fileId."_".$fileName.".".$fileExtension;
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
