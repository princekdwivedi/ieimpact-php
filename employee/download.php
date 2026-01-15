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

	$path			=	"";
	$downloadPath	=	"";
	$andClause		=	"";
	$andClause1		=	"";
	$fieldExt		=	"";
	$fieldFileName	=	"";
	$mimeTypeField	=	"";
	$performedTask	=	"";

	$downloadFileName=	"";

	$a_orderTypes				=	array("1"=>"OF","2"=>"PF","3"=>"MF","4"=>"CF","5"=>"OTF","7"=>"PDF");
	
	if(isset($_GET['ID']) && isset($_GET['t']) && isset($_GET['f']))
	{
		$orderId			=	(int)$_GET['ID'];
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
					$performedTask	=	"Downloading Order Files";
				}
				elseif($orderType   == "PF")
				{
					$andClause1		=	" AND hasPublicRecordFile=1";
					$path			=	$publicRecordFilePath."/";
					$fieldExt		=	"publicRecordFileExt";
					$fieldFileName	=	"publicRecordFileName";
					$mimeTypeField	=	"publicRecordMimeType";
					$performedTask	=	"Downloading Public Record Files";
				}
				elseif($orderType   == "MF")
				{
					$andClause1		=	" AND hasMlsFile=1";
					$path			=	$mlsFilePath."/";
					$fieldExt		=	"mlsFileExt";
					$fieldFileName	=	"mlsFileName";
					$mimeTypeField	=	"mlsMimeType";
					$performedTask	=	"Downloading Mls Files";
				}
				elseif($orderType   == "CF")
				{
					$andClause1		=	" AND hasMarketConditionFile=1";
					$path			=	$marketConditionFilePath."/";
					$fieldExt		=	"marketConditionExt";
					$fieldFileName	=	"marketConditionFileName";
					$mimeTypeField	=	"marketConditionMimeType";
					$performedTask	=	"Downloading Market condition Files";
				}
				elseif($orderType   == "OTF")
				{
					$andClause1		=	" AND hasOtherFile=1";
					$path			=	$otherFilePath."/";
					$fieldExt		=	"otherFileExt";
					$fieldFileName	=	"otherFileName";
					$mimeTypeField	=	"otherMimeType";
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
				elseif($orderType   == "PDF")
				{
					$andClause1		=	" AND hasCompletedPdfFile=1";
					$path			=	$otherFilePath."/";
					$fieldExt		=	"compltetedPdfFileExt";
					$fieldFileName	=	"compltetedPdfFileName";
					$mimeTypeField	=	"compltetedPdfFileMimeType";
					$performedTask	=	"Downloading reply completed order PDF Files";
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
		$query		=	"SELECT orderId,folderId,".$fieldExt.",".$fieldFileName.",".$mimeTypeField." FROM members_orders INNER JOIN members ON members_orders.memberId=members.memberId WHERE orderId=$orderId".$andClause1;
		$result		=	mysql_query($query);
		if(mysql_num_rows($result))
		{
			$row			 =	mysql_fetch_assoc($result);
			$folderId		 =	$row['folderId'];
			$fileExt		 =	$row[$fieldExt];
			$fieldFileName	 =	$row[$fieldFileName];
			$mimeTypeField	 =	$row[$mimeTypeField];

			$downloadFileName=	$fieldFileName.".".$fileExt;

			$downloadPath	 =	$path.$folderId."/".$orderId."_".$fieldFileName.".".$fileExt;
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

			if($orderType		== "PDF")
			{
				$downloadPath	=	$path.$folderId."/".$orderId."_".$replyId."_replied_completed_pdf_file.".$fileExt;
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

	$orderObj->trackEmployeeWork($orderId,$s_employeeId,$performedTask);

    if(file_exists($downloadPath))
	{		
		$exactFileSize		=	@filesize($downloadPath);
		if($exactFileSize > 0 && $exactFileSize < "104857600")
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
			include(SITE_ROOT_EMPLOYEES   .   "/includes/file-not-found-page.php");
		}
	}
	else
	{
		include(SITE_ROOT_EMPLOYEES   .   "/includes/file-not-found-page.php");
	}
?>
