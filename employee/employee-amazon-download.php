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

	$path						=	"";
	$downloadPath				=	"";
	$andClause					=	"";
	$andClause1					=	"";
	$fieldExt					=	"";
	$fieldFileName				=	"";
	$mimeTypeField				=	"";
	$performedTask				=	"";
	$isAmazonFile				=	"";
	$amazonFileText				=	"";
	$orderFileSize				=	"";
	$encodeOrderID				=	0;

	$downloadFileName			=	"";

	$a_orderTypes				=	array("1"=>"OF","2"=>"PF","3"=>"MF","4"=>"CF","5"=>"OTF");
	
	if(isset($_GET['ID']) && isset($_GET['t']) && isset($_GET['f']))
	{
		$orderId			=	(int)$_GET['ID'];
		$encodeOrderID		=	base64_encode($orderId);
		$orderType			=	$_GET['t'];
		$orderFor			=	$_GET['f'];
		if(in_array($orderType,$a_orderTypes))
		{
			if($orderFor    == "N")
			{
				if($orderType   == "OF")
				{
					$andClause1		=	" AND hasOrderFile=1";
					$path			=	$orderFilePath."/";
					$fieldExt		=	"orderFileExt";
					$fieldFileName	=	"orderFileName";
					$mimeTypeField	=	"orderMimeType";
					$isAmazonFile	=	"isStoredOrderInAmazon";
					$amazonFileText	=	"orderfile";
					$orderFileSize	=	"orderFileSize";
					$performedTask	=	"Downloading Order Files";
				}
				elseif($orderType   == "PF")
				{
					$andClause1		=	" AND hasPublicRecordFile=1";
					$path			=	$publicRecordFilePath."/";
					$fieldExt		=	"publicRecordFileExt";
					$fieldFileName	=	"publicRecordFileName";
					$mimeTypeField	=	"publicRecordMimeType";
					$isAmazonFile	=	"isStoredPublicRecordInAmazon";
					$amazonFileText	=	"publicRecordFile";
					$orderFileSize	=	"publicRecordFileSize";
					$performedTask	=	"Downloading Public Record Files";
				}
				elseif($orderType   == "MF")
				{
					$andClause1		=	" AND hasMlsFile=1";
					$path			=	$mlsFilePath."/";
					$fieldExt		=	"mlsFileExt";
					$fieldFileName	=	"mlsFileName";
					$mimeTypeField	=	"mlsMimeType";
					$isAmazonFile	=	"isStoredMlsFileInAmazon";
					$amazonFileText	=	"mlsFile";
					$orderFileSize	=	"mlsFileSize";
					$performedTask	=	"Downloading Mls Files";
				}
				elseif($orderType   == "CF")
				{
					$andClause1		=	" AND hasMarketConditionFile=1";
					$path			=	$marketConditionFilePath."/";
					$fieldExt		=	"marketConditionExt";
					$fieldFileName	=	"marketConditionFileName";
					$mimeTypeField	=	"marketConditionMimeType";
					$isAmazonFile	=	"isStoredMarketConditionInAmazon";
					$amazonFileText	=	"marketcondition";
					$orderFileSize	=	"marketConditionFileSize";
					$performedTask	=	"Downloading Market condition Files";
				}
				elseif($orderType   == "OTF")
				{
					$andClause1		=	" AND hasOtherFile=1";
					$path			=	$otherFilePath."/";
					$fieldExt		=	"otherFileExt";
					$fieldFileName	=	"otherFileName";
					$mimeTypeField	=	"otherMimeType";
					$isAmazonFile	=	"isStoredOtherFileInAmazon";
					$amazonFileText	=	"other";
					$orderFileSize	=	"otherFileSize";
					$performedTask	=	"Downloading other Files";
				}
			}
			elseif($orderFor    == "R")
			{
				if($orderType   == "OF")
				{
					$andClause1		=	" AND hasReplyOrderFile=1";
					$path			=	$orderFilePath."/";
					$fieldExt		=	"replyOrderFileExt";
					$fieldFileName	=	"replyOrderFileName";
					$mimeTypeField	=	"replyOrderMimeType";
					$performedTask	=	"Downloading reply order Files";
				}
				elseif($orderType   == "PF")
				{
					$andClause1		=	" AND hasReplyPublicRecordFile=1";
					$path			=	$publicRecordFilePath."/";
					$fieldExt		=	"replyPublicRecordFileExt";
					$fieldFileName	=	"replyPublicRecordFileName";
					$mimeTypeField	=	"replyPublicRecordMimeType";
					$performedTask	=	"Downloading reply public record Files";
				}
				elseif($orderType   == "MF")
				{
					$andClause1		=	" AND hasReplyMlsFile=1";
					$path			=	$mlsFilePath."/";
					$fieldExt		=	"replyMlsFileExt";
					$fieldFileName	=	"replyMlsFileName";
					$mimeTypeField	=	"replyMlsMimeType";
					$performedTask	=	"Downloading reply MLS Files";
				}
				elseif($orderType   == "CF")
				{
					$andClause1		=	" AND hasReplyMarketCondition=1";
					$path			=	$marketConditionFilePath."/";
					$fieldExt		=	"replyMarketConditionExt";
					$fieldFileName	=	"replyMarketConditionFileName";
					$mimeTypeField	=	"replyMarketConditionMimeType";
					$performedTask	=	"Downloading reply Market Condition Files";
				}
				elseif($orderType   == "OTF")
				{
					$andClause1		=	" AND hasOtherFile=1";
					$path			=	$otherFilePath."/";
					$fieldExt		=	"otherFileExt";
					$fieldFileName	=	"otherFileName";
					$mimeTypeField	=	"replyOtherFileMimeType";
					$performedTask	=	"Downloading reply other Files";
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
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	if($orderFor    == "N")
	{
		$query		=	"SELECT orderId,folderId,".$fieldExt.",".$fieldFileName.",".$mimeTypeField.",".$isAmazonFile." FROM members_orders INNER JOIN members ON members_orders.memberId=members.memberId WHERE orderId=$orderId".$andClause1;
		$result		=	mysql_query($query);
		if(mysql_num_rows($result))
		{
			$row			 =	mysql_fetch_assoc($result);
			$folderId		 =	$row['folderId'];
			$fileExt		 =	$row[$fieldExt];
			$fieldFileName	 =	$row[$fieldFileName];
			$mimeTypeField	 =	$row[$mimeTypeField];
			$isAmazonFile	 =	$row[$isAmazonFile];
			$orderFileSize	 =	$row[$orderFileSize];

			if($isAmazonFile == 1)
			{
				$bucketNumber=	"customer_ieimpact";
				//include the S3 class
				if (!class_exists('S3'))require_once(SITE_ROOT.'/S3.php');
				
				//AWS access info
				if (!defined('awsAccessKey')) define('awsAccessKey', AMAZON_EMAIL_API);
				if (!defined('awsSecretKey')) define('awsSecretKey', AMAZON_EMAIL_KEY);
				
				//instantiate the class
				$s3 = new S3(awsAccessKey, awsSecretKey);
	
				$amazonFileName	 = $folderId."/".$encodeOrderID."/".$amazonFileText."_".$fieldFileName.".".$fileExt;

				$downloadPath	 =	"http://".$bucketNumber.".s3.amazonaws.com/".$amazonFileName;
				$downloadFileName=	$fieldFileName.".".$fileExt;

			}
			else
			{
				$downloadFileName=	$fieldFileName.".".$fileExt;

				$downloadPath	 =	$path.$folderId."/".$orderId."_".$fieldFileName.".".$fileExt;
			}
		}
		else
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}
	}
	elseif($orderFor    == "R")
	{
		$query		=	"SELECT replyId,folderId,orderId,".$fieldExt.",".$fieldFileName.",".$mimeTypeField." FROM members_orders_reply INNER JOIN members ON members_orders_reply.memberId=members.memberId WHERE orderId=$orderId".$andClause1;
		$result		=	mysql_query($query);
		if(mysql_num_rows($result))
		{
			$row			=	mysql_fetch_assoc($result);
			$folderId		=	$row['folderId'];
			$replyId		=	$row['replyId'];
			$fileExt		=	$row[$fieldExt];
			$fieldFileName	=	$row[$fieldFileName];
			$mimeTypeField	=	$row[$mimeTypeField];

			$downloadFileName=	$fieldFileName.".".$fileExt;

			$downloadPath	=	$path.$folderId."/".$orderId."_".$replyId."_".$fieldFileName.".".$fileExt;
			
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

	$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);

	//echo $downloadPath;
if($isAmazonFile == 1)
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

			//echo "<br>".$downloadPath;
			//header('Content-Description: File Transfer');
			//header("Content-Type: " . $mimeTypeField);
			//header("Content-Length: " .(string)(filesize($downloadPath)) );
			//header('Content-Disposition: attachment; filename="'.basename($downloadFileName).'"');
			//header("Content-Transfer-Encoding: binary\n");

			//  readfile($downloadPath); // outputs the content of the file
			//echo file_get_contents($downloadPath);

			//------
			header("Content-Type: " . $mimeTypeField);
			header("Content-Length: " .(string)(filesize($downloadPath)) );
			header('Content-Disposition: attachment; filename="'.basename($downloadFileName).'"');
			header("Content-Transfer-Encoding: binary\n");

			//  readfile($downloadPath); // outputs the content of the file
			//echo file_get_contents($downloadPath);




		  // If it's a large file, readfile might not be able to do it in one go, so:
		  $chunksize = 1 * (1024 * 1024); // how many bytes per chunk

		  $handle = fopen($downloadPath, 'rb');
		  $buffer = '';
		  while (!feof($handle))
		  {
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
		echo "<br><br><center><b>Sorry No Files Available !!<br><br>Please contact us at support@ieimpact.com</b></center></br>";
	 }
}
?>
