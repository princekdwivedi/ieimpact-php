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

	$downloadPath				=	SITE_ROOT_FILES."/files/otherFiles";
	$andClause					=	"";
	$andClause1					=	"";
	$otherId					=	0;
	$orderId					=	0;
	$text						=	"";
	$downloadFileName			=	"";
	$performedTask				=	"";
	$encodeOtherID				=	0;
	$isStoredInAmazon			=	0;


	if(isset($_GET['ID']) && isset($_GET['t']))
	{
		$otherId				=	$_GET['ID'];
		$type					=	$_GET['t'];
		$orderId				=	@mysql_result(mysql_query("SELECT orderId FROM other_order_files WHERE otherId=$otherId"),0);
		if($type				==	"OT")
		{
			$andClause1			=	" AND uploadingFor=1";
			$text				=	"other";
			$performedTask		=	"Downloading Other File ID - ".$otherId;
		}
		elseif($type			==	"FD")
		{
			$andClause1			=	" AND uploadingFor=2";
			$text				=	"feedback";
			$performedTask		=	"Downloading Feedback File ID - ".$otherId;
		}
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$query		=	"SELECT * FROM other_order_files WHERE otherId=$otherId AND orderId=$orderId".$andClause1;
	$result		=	mysql_query($query);
	if(mysql_num_rows($result))
	{
		$row			=	mysql_fetch_assoc($result);
		$otherId		=	$row['otherId'];
		$fileName		=	$row['fileName'];
		$fileExtension	=	$row['fileExtension'];
		$fileSize		=	$row['fileSize'];
		$byId			=	$row['memberId'];
		$fileMimeType	=	$row['fileMimeType'];
		$isStoredInAmazon=	$row['isStoredInAmazon'];

		$downloadFileName=	$fileName.".".$fileExtension;

		$memberFolderId	=	@mysql_result(mysql_query("SELECT folderId FROM members WHERE memberId=$byId"),0);

		if($isStoredInAmazon == 1)
		{
			$bucketNumber=	"customer_ieimpact";
			//include the S3 class
			if (!class_exists('S3'))require_once(SITE_ROOT.'/S3.php');
			
			//AWS access info
			if (!defined('awsAccessKey')) define('awsAccessKey', AMAZON_EMAIL_API);
			if (!defined('awsSecretKey')) define('awsSecretKey', AMAZON_EMAIL_KEY);
			
			//instantiate the class
			$s3 = new S3(awsAccessKey, awsSecretKey);

			$encodeOrderID	 = base64_encode($orderId);
			$encodeOtherID	 = base64_encode($otherId);

			$amazonFileName	 = $memberFolderId."/".$encodeOrderID."/other_".$encodeOtherID."_".$fileName.".".$fileExtension;

			$downloadPath	 =	"http://".$bucketNumber.".s3.amazonaws.com/".$amazonFileName;
		}
		else
		{

			$downloadPath	=	$downloadPath."/".$memberFolderId."/".$text."_".$orderId."_".$otherId."_".$fileName.".".$fileExtension;
		}
	}
	else
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);

	if($isStoredInAmazon == 1)
	{
		$info			=	$s3->getObjectInfo($bucketNumber, $amazonFileName);
		$file			=	$s3->getAuthenticatedURL($bucketNumber, $amazonFileName, 36000);
		//pr($info);
		//pr($file);

		
		header("Pragma: public");
		header("Expires: 0");
		header("Content-Transfer-Encoding: binary");
		header("Content-Type: ".$info['type']);
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header('Content-Disposition: attachment; filename="'.basename($downloadFileName).'"');
		header("Content-Length: ".$info['size']);

		$chunksize = 1 * (1024 * 1024); // how many bytes per chunk

		  $handle = fopen($file, 'rb');
		  $buffer = '';
		  while (!feof($handle)) {
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			ob_flush();
			flush();
		  }
		  fclose($handle);
		
		 exit();
	}
	else
	{
		if(file_exists($downloadPath))
		{
			if($fileSize <= 5242880)
			{
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.basename($downloadFileName).'"');
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . filesize($downloadPath));

				ob_clean();
				flush();
				readfile($downloadPath);
				exit;
			}
			else
			{
				header("Content-Type: " . $fileMimeType);
				header("Content-Length: " .(string)(filesize($downloadPath)) );
				header('Content-Disposition: attachment; filename="'.basename($downloadFileName).'"');
				header("Content-Transfer-Encoding: binary\n");
				//readfile($downloadPath); // outputs the content of the file

				// If it's a large file, readfile might not be able to do it in one go, so:
				 $chunksize = 1 * (1024 * 1024); // how many bytes per chunk


				 $handle = fopen($downloadPath, 'rb');
				 $buffer = '';
				 while (!feof($handle)) {
				$buffer = fread($handle, $chunksize);
				echo $buffer;
				ob_flush();
				flush();
				 }
				 fclose($handle);

			  exit();
			}
		}
		else
		{
			echo "<br><br><center><b>Sorry No Files Available !!<br><br>Please contact us at support@ieimpact.com</b></center></br>";
		}
	}
?>