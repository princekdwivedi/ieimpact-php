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
	//include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
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
	$M_D_5_ID					=  ID_M_D_5;

	$downloadFileName			=  "";

	if(isset($_GET['FILE_TYPE']) && $_GET['FILE_TYPE'] === 'OCR_RESULT')
	{
		if(isset($_GET[$M_D_5_ORDERID]))
		{
			$encodeOrderID		=	$_GET[$M_D_5_ORDERID];

			$orderId			=	base64_decode($encodeOrderID);
			if(empty($orderId))
			{
				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES);
				exit();
			}

			$query		=	"SELECT * FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND orderId=$orderId AND isDeleted=0";
			$result		=	mysqli_query($db_conn,$query);
			if(mysqli_num_rows($result))
			{
				$row					=	mysqli_fetch_assoc($result);
				$downloadPath			=	$row['excatFileNameInServer'];
				$downloadPath           =   stringReplace("/home/ieimpact", "", $downloadPath);

				$downloadFileName		=	"extracted-data.pdf";
				$downloadPathInfo		=	pathinfo($downloadPath);
				$downloadPath           =   $downloadPathInfo['dirname'] . "/ocrFiles/$downloadFileName";
				$downloadFileName		=   "$orderId-AI-Extracted Property Details.pdf";

				if(VISITOR_IP_ADDRESS	==	"122.160.167.153" || VISITOR_IP_ADDRESS	==	"45.12.221.2"){
					echo "<br />".$downloadPath;
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
	}
	else
	{
		if(isset($_GET[$M_D_5_ID]) && isset($_GET[$M_D_5_ORDERID]))
		{
			$encodeOrderID		=	$_GET[$M_D_5_ORDERID];
			$encodeFileID		=	$_GET[$M_D_5_ID];

			$orderId			=	base64_decode($encodeOrderID);
			$fileId				=	base64_decode($encodeFileID);
			if(empty($orderId) || empty($fileId))
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
			
		
		$query		=	"SELECT * FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND orderId=$orderId AND isDeleted=0 AND fileId=$fileId";
		$result		=	mysqli_query($db_conn,$query);
		if(mysqli_num_rows($result))
		{
			$row					=	mysqli_fetch_assoc($result);
			$downloadPath			=	$row['excatFileNameInServer'];
			if(VISITOR_IP_ADDRESS	==	"122.160.167.153" || VISITOR_IP_ADDRESS	==	"45.12.221.2"){
				echo $downloadPath;
			}
			$downloadPath           =   stringReplace("/home/ieimpact", "", $downloadPath);

			if(VISITOR_IP_ADDRESS	==	"122.160.167.153" || VISITOR_IP_ADDRESS	==	"45.12.221.2"){
				echo "<br />".$downloadPath;
			}
			$uploadingFileType		=	$row['uploadingFileType'];
			$uploadingFileExt		=	$row['uploadingFileExt'];
			$uploadingFileName		=	$row['uploadingFileName'];
			$mimeTypeField			=	$row['uploadingFileType'];

			$downloadFileName		=	$uploadingFileName.".".$uploadingFileExt;
		}
		else
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}
	}

	if(file_exists($downloadPath))
	{		
		$exactFileSize		=	@filesize($downloadPath);
		if($exactFileSize > 0 && $exactFileSize < "1048576000")
		{

			//echo "<br>".$downloadPath;
			 header("Cache-Control: maxage=1"); // Age is in seconds.
			 header("Pragma: public");
		   // header("Content-Type: application/force-download");

			header("Content-Type: " . $mimeTypeField);
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
			//include(SITE_ROOT_EMPLOYEES   .   "/includes/file-not-found-page.php");
			echo "KASE1";
		}
	}
	else
	{
		//include(SITE_ROOT_EMPLOYEES   .   "/includes/file-not-found-page.php");
		echo "<b r/>KASE2 > " . $downloadFileName;
	}
 ?>
