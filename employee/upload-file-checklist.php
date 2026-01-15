<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/new-top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				= new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT			. "/classes/new-pagingclass.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	$pagingObj					=  new Paging();
	$memberObj					=  new members();
	$orderObj					=  new orders();
	$isEmployeeAManager			=  0;
	$headingText				=  "CHECK YOUR PROCESSED FILES NAME/SIZE";
	$link						=  "";
	$showEmployee				=  0;
	$employeeText				=  "&nbsp;";
	if($s_hasManagerAccess)
	{
		$isEmployeeAManager		=  1;
	}

	$checkDateData				=	"checked";
	$checkMonthData				=	"";
	$displayDate				=	"";
	$displayMonth				=	"none";
	$searchEmployeeId			=	0;
	$forDate					=	date("d-m-Y");
	$t_forDate					=	date("Y-m-d");
	$searchMonth				=	date("m");
	$searchYear					=	date("Y");
	$searchBy					=	1;
	$formSearch					=  SITE_ROOT_EMPLOYEES."/forms/search-general-order-form.php";

	function getFileSize($fileSize)
	{
		if($fileSize <= 0 || $fileSize == 0)
		{
			$fileSize	=	"";
		}
		else
		{
			$fileSize	=	$fileSize/1024;

			$fileSize	=	round($fileSize,2);

			$fileSize	=	$fileSize;
		}

		if(empty($fileSize))
		{
			$fileSize	=	"<font color='#ff0000'>(0 byte)</font>";
		}

		return $fileSize;
	}

	if(isset($_REQUEST['recNo']))
	{
		$recNo					=	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo			=	0;
	}
	$whereClause		=	"WHERE members_orders.orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND members_orders.isVirtualDeleted =0 AND isTestAccount=0";
	$orderBy			=	"orderAddedOn DESC,orderAddedTime DESC";
	$andClause			=	"";
	$andClause1			=	"";
	$andClause2			=	" AND acceptedBy=".$s_employeeId;
	$queryString		=	"&searchBy=1&forDate=".$forDate;
	$a_allPdfEmployees	=	array();
	if($result			=	$employeeObj->getAllPdfEmployees())
	{
		while($row				=	mysqli_fetch_assoc($result))
		{
			$employeeId			=	$row['employeeId'];
			$firstName			=	stripslashes($row['firstName']);
			$lastName			=	stripslashes($row['lastName']);
			$completeEmpName	=	$firstName." ".$lastName;
			
			$a_allPdfEmployees[$employeeId]=	$completeEmpName;
			
		}
	}
			
	if(isset($_GET['searchEmployeeId']))
	{
		$searchEmployeeId		=	$_GET['searchEmployeeId'];
		if(!empty($searchEmployeeId) && array_key_exists($searchEmployeeId,$a_allPdfEmployees))
		{
			$andClause2			=	" AND acceptedBy=".$searchEmployeeId;
			$name				=	$a_allPdfEmployees[$searchEmployeeId];
			$headingText		=  "CHECK ".strtoupper($name)."'S PROCESSED FILES NAME/SIZE";
		}
		else
		{
			$andClause2			=	"";
			$headingText		=  "CHECK ALL PROCESSING EMPLOYEES PROCESSED FILES NAME/SIZE";
		}
		$showEmployee			=   1;
		$queryString		   .=	"&searchEmployeeId=".$searchEmployeeId;
		$link				    =  "<a href='".SITE_URL_EMPLOYEES."/upload-file-checklist.php' class='link_style3'>VEW YOUR PROCESSED FILES</a>";
		$employeeText		    =  "EMPLOYEE";
		
	}
	
?>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
<tr>
	<td class="heading3">
		:: <?php echo $headingText;?> ::
	</td>
</tr>
<tr>
	<td>
		<?php
			include($formSearch);
		?>
	</td>
</tr>
<tr>
	<td height="5"></td>
</tr>
<?php
	if(!empty($link))
	{
?>

<tr>
	<td height="5">
		<?php echo $link;?>
	</td>
</tr>
<tr>
	<td height="5"></td>
</tr>
<?php
	}
?>
</table>
<form name="searchUploadFileForm" action=""  method="GET">
	<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
		<tr>
			<?php
				if($isEmployeeAManager	== 1)
				{
			?>
			<td width="25%" class="smalltext2" valign="top"><b>For Employee :</b>
				
				<select name="searchEmployeeId" onchange="document.searchUploadFileForm.submit();">
					<option value="0">All</option>
					<?php
						foreach($a_allPdfEmployees as $k=>$v)
						{
							$select					=	"";
							if($searchEmployeeId	==	$k)
							{
								$select				=	"selected";
							}
							echo "<option value='$k' $select>$v</option>";
						}
					?>
				</select>
			</td>
			<?php
				}
			?>
		</tr>
		<tr>
			<td colspan="6" height="5"></td>
		</tr>
	</table>
</form>
<?php

	$start					  =	0;
	$recsPerPage	          =	500;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause.$andClause1.$andClause2;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	die("KASE1");
	$pagingObj->table		  =	"members_orders INNER JOIN members_orders_reply ON members_orders.orderId=members_orders_reply.orderId INNER JOIN members ON members_orders.memberId=members.memberId";
	$pagingObj->selectColumns = "members_orders.memberId,members_orders.orderId,members_orders.acceptedBy,orderAddedOn,orderFileName,orderFileSize,replyOrderFileName,replyOrderFileSize,orderFileExt,replyOrderFileExt,completeName";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/upload-file-checklist.php";
	echo "xxxx-".$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
		$i	=	$recNo;
		die("KASE1");
?>
<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
<tr bgcolor="#373737" height="20">
	<td class="smalltext12" width="8%">&nbsp;<b>Date</b></td>
	<td class="smalltext12" width="18%"><b>CUSTOMER</b></td>
	<td class="smalltext12" width="31%"><b>FILE NAME RECEIVED</b></td>
	<td class="smalltext12" width="16%"><b>FILE NAME SENT</b></td>
	<td class="smalltext12" width="8%"><b>RCVD (KB)</b></td>
	<td class="smalltext12" width="8%"><b>SENT (KB)</b></td>
	<td class="smalltext12"><b><?php echo $employeeText;?></b></td>
</tr>
<?php
		while($row	=   mysqli_fetch_assoc($recordSet))
		{
			$i++;
			$orderId			=	$row['orderId'];
			$memberId			=	$row['memberId'];
			$orderFileSize		=	$row['orderFileSize'];
			$orderFileName		=	$row['orderFileName'];
			$replyOrderFileName	=	$row['replyOrderFileName'];
			$replyOrderFileSize	=	$row['replyOrderFileSize'];
			$orderAddedOn		=	showDate($row['orderAddedOn']);
			$orderFileExt		=	$row['orderFileExt'];
			$replyOrderFileExt	=	$row['replyOrderFileExt'];
			$acceptedBy			=	$row['acceptedBy'];
			$customerName		=	stripslashes($row['completeName']);

			//$customerName		=	@mysql_result(dbQuery("SELECT completeName FROM members WHERE memberId=$memberId"),0);
			//$customerName		=	stripslashes($customerName);

			$processEmployeeName=	$employeeObj->getEmployeeName($acceptedBy);

			if(empty($orderFileName))
			{
				$orderFileNameExt	=	"NONE";
			}
			else
			{
				$orderFileNameExt	=	$orderFileName.".".$orderFileExt;
			}
			
			if(empty($replyOrderFileName))
			{
				$replyFileNameExt	=	"NONE";
			}
			else
			{
				$replyFileNameExt	=	$replyOrderFileName.".".$replyOrderFileExt;
			}

			$orderFileSizeKb	=	getFileSize($orderFileSize);
			$replyFileSizeKb	=	getFileSize($replyOrderFileSize);

			$replyFileNameTextClass	 =	"smalltext13";
			$replyFileSizeTextClass	 =	"smalltext13";
			$fileNameSentText		 =	"Same";
			if($orderFileNameExt    !=  $replyFileNameExt)
			{
				$replyFileNameTextClass	 =	"smalltext15";
				$fileNameSentText	=	$replyFileNameExt;
			}

			if($orderFileSize	 >=  $replyOrderFileSize)
			{
				$replyFileSizeTextClass	 =	"smalltext15";
			}


			
			$bgColor			  =	"class='rwcolor1'";
			if($i%2==0)
			{
				$bgColor		  =   "class='rwcolor2'";
			}
	?>
		<tr height='20' <?php echo $bgColor;?>>
			<td class='textstyle' valign="top">&nbsp;<?php echo $orderAddedOn;?></td>
			<td valign="top">
				<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmitAdvancedSearch=1&serachCustomerId=<?php echo $memberId;?>" class="link_style12"><?php echo $customerName;?></a>
			</td>
			<td class='smalltext13' valign="top">
				<?php
					echo $orderFileNameExt;	
				?>
			</td>
			<td class='<?php echo $replyFileNameTextClass;?>' valign="top">
				<?php
					echo $fileNameSentText;	
				?>
			</td>
			<td class='smalltext13' valign="top">
				<?php
					echo $orderFileSizeKb;	
				?>
			</td>
			<td class='<?php echo $replyFileSizeTextClass;?>' valign="top">
				<?php
					echo $replyFileSizeKb;	
				?>
			</td> 
			<td valign="top">
				<?php
					if($showEmployee	==	1)
					{
				?>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?orderOf=<?php echo $acceptedBy;?>&showingEmployeeOrder=1" class="link_style12"><?php echo $processEmployeeName;?></a>
				<?php
					}
					else
					{
						echo "&nbsp;";
					}
				?>
			</td> 
		</tr>
	<?php
			
		}
		echo "<tr><td height='5'></td></tr><tr><td align='right' colspan='8'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr></table>";		
	}
	else
	{
		echo "<table><tr><td height='50'></td></tr><tr><td align='center' class='error'><b>No Record Found !!</b></td></tr><tr><td height='200'></td></tr></table>";
		
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>