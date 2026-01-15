<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES .  "/includes/new-top.php");
	include(SITE_ROOT_EMPLOYEES .  "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES .  "/classes/employee.php");
	include(SITE_ROOT			.  "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/check-pdf-login.php");
	include(SITE_ROOT_MEMBERS	.  "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	.  "/classes/orders.php");
	include(SITE_ROOT			.  "/classes/email-track-reading.php");
	include(SITE_ROOT			.  "/classes/pagingclass.php");

	$employeeObj				=  new employee();
	$memberObj					=  new members();
	$orderObj					=  new orders();
	$emailTrackObj				=  new trackReading();
	$pagingObj					=  new Paging();
	$messageFilePath			=	SITE_ROOT_FILES."/files/messages/";

	$formSearch					=	SITE_ROOT_EMPLOYEES."/forms/search-general-order-form.php";

	if(empty($s_isHavingVerifyAccess))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	if(isset($_REQUEST['recNo']))
	{
		$recNo					=	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo	=	0;
	}
	$whereClause				=	"WHERE isNeedToVerify=1 AND members_employee_messages.isDeleted=0 AND members_employee_messages.isVirtualDeleted=0 AND members_orders.orderId > ".MAX_SEARCH_EMPLOYEE_ORDER_ID;
	$orderBy					=	"addedOn,addedTime";
	$queryString				=	"";
	$andCaluse					=	"";
	$andClause1					=	"";

	if(isset($_GET['orderId']) && isset($_GET['customerId']) && isset($_GET['messageId']))
	{
		$orderId				=	(int)$_GET['orderId'];
		$customerId				=	(int)$_GET['customerId'];
		$messageId				=	(int)$_GET['messageId'];


		if(!empty($orderId) && !empty($customerId) && !empty($messageId))
		{
			if($result							=	$orderObj->getOrderDetails($orderId,$customerId))
			{
				$row							=	mysqli_fetch_assoc($result);
				$isNewUploadingSystem			=	$row['isNewUploadingSystem'];
				$newUploadingPath				=	$row['newUploadingPath'];

				$query							=	"SELECT * FROM members_employee_messages WHERE messageId=$messageId AND orderId=$orderId AND memberId=$customerId AND isNeedToVerify=1";
				$result							=	dbQuery($query);
				if(mysqli_num_rows($result))
				{
					$row						=	mysqli_fetch_assoc($result);
					$t_messageId				=	$row['messageId'];
					$t_message					=	stripslashes($row['message']);
					$t_message					=	trim($t_message);
					$addedOn					=	$row['addedOn'];
					$addedTime					=	$row['addedTime'];
					$messageBy					=	$row['messageBy'];
					$hasMessageFiles			=	$row['hasMessageFiles'];
					$uploadingFileName			=	stripslashes($row['fileName']);
					$fileExt					=	$row['fileExtension'];
					$fileExtension				=	$row['fileExtension'];
					$fileSize					=	$row['fileSize'];
					$emailSubject				=	stripslashes($row['emailSubject']);
					$readEmailText				=	"";

					if(isset($_GET['isDelete']) && $_GET['isDelete'] == 1)
					{
						if($hasMessageFiles				==	1)
						{
							if($isNewUploadingSystem	==	1)
							{
							$exactServerpasth	=	$orderObj->getQueryResult("SELECT excatFileNameInServer FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND messageId=$t_messageId AND orderId=$orderId AND uploadingFor=3 AND isDeleted=0 AND uploadingType=7","excatFileNameInServer");
								if(!empty($exactServerpasth) && file_exists($exactServerpasth))
								{
									unlink($exactServerpasth);
									dbQuery("DELETE FROM order_all_files WHERE fileId > ".MAX_SEARCH_MEMBER_ORDER_FILEID." AND messageId=$t_messageId AND orderId=$orderId AND uploadingFor=3 AND isDeleted=0 AND uploadingType=7");
								}
							}
							else
							{
								$fileName	=	$t_messageId."_".$uploadingFileName.".".$fileExt;
								if(file_exists($messageFilePath.$fileName))
								{
									unlink($exactServerpasth);
								}
							}
						}
						dbQuery("DELETE FROM members_employee_messages WHERE messageId=$t_messageId AND orderId=$orderId AND memberId=$customerId AND isNeedToVerify=1");

						$orderObj->deductOrderRelatedCounts('verifyMessages');

						ob_clean();
						header("Location: ".SITE_URL_EMPLOYEES."/verify-order-messages.php");
						exit();
					}

				}
				else
				{
					ob_clean();
					header("Location: ".SITE_URL_EMPLOYEES."/verify-order-messages.php");
					exit();
				}

			}
			else
			{
				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES."/verify-order-messages.php");
				exit();
			}
		}
		else
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/verify-order-messages.php");
			exit();
		}
	}




?>
<script type="text/javascript">
	function delMesg(orderId,customerId,messageId,recNo)
	{
		var confirmation = window.confirm("Are You Sure Delete This Message?");
		if(confirmation == true)
		{
			window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/verify-order-messages.php?orderId="+orderId+"&customerId="+customerId+"&messageId="+messageId+"&recNo="+recNo+"&isDelete=1";
		}
	}
</script>
<table width='99%' align='center' cellpadding='0' cellspacing='0' border='0'>
<tr>
	<td colspan="10" class="textstyle2">
		<b>
			ORDER MESSAGES NEED TO VERIFY
		</b>
	</td>
</tr>
<tr>
	<td colspan="10">
		<?php
			include($formSearch);
		?>
	</td>
</tr>
<?php	
	$start					  =	0;
	$recsPerPage	          =	25;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andCaluse.$andClause1;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"members_employee_messages INNER JOIN members ON members_employee_messages.memberId=members.memberId INNER JOIN members_orders ON members_employee_messages.orderId=members_orders.orderId LEFT JOIN employee_details ON members_employee_messages.employeeId=employee_details.employeeId";
	$pagingObj->selectColumns = "members_employee_messages.*,members.firstName,members.lastName,fullName,orderAddress";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/verify-order-messages.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet				= $pagingObj->getRecords();
		$i						=	$recNo;

	?>
	<tr bgcolor="#373737" height="20">
		<td class="smalltext12" width="2%">&nbsp;</td>
		<td class="smalltext12" width="12%">CUSTOMER NAME</td>
		<td class="smalltext12" width="20%">ORDER ADDRESS</td>
		<td class="smalltext12" width="33%">MESSAGE</td>
		<td class="smalltext12" width="17%">DATE & TIME</td>
		<td class="smalltext12" width="9%">BY EMPLOYEE</td>
		<td class="smalltext12">&nbsp;</td>
	</tr>
	<?php

		while($row	=   mysqli_fetch_assoc($recordSet))
		{
			$i++;
			$orderId		=	$row['orderId'];
			$memberId		=	$row['memberId'];
			$firstName		=	stripslashes($row['firstName']);
			$lastName		=	stripslashes($row['lastName']);
			$completeName	=	$firstName." ".substr($lastName, 0, 1);
			$t_messageId	=	$row['messageId'];
			$t_message		=	stripslashes($row['message']);
			$t_message		=	trim($t_message);
			$addedOn		=	$row['addedOn'];
			$addedTime		=	$row['addedTime'];
			$employeeId		=	$row['employeeId'];
			$employeeName	=	$row['fullName'];
			$orderAddress	=	stripslashes($row['orderAddress']);
			if(!empty($orderAddress))
			{
				$orderAddress	=	stripslashes($orderAddress);
			}

			$bgColor			=	"class='rwcolor1'";
			if($i%2==0)
			{
				$bgColor		=	"class='rwcolor2'";
			}

			$daysAgo			=	showDateTimeFormat($addedOn,$addedTime);
	?>
	<tr height="25" <?php echo $bgColor;?> valign="top">
		<td class="smalltext20">
			<?php echo $i;?>)
		</td>
		<td class="smalltext20" valign="top">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=<?php echo $memberId;?>" class='link_style12' style='cursor:pointer'><?php echo getSubstring($completeName,30);?></a>
		</td>
		<td class="smalltext20" valign="top">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $memberId;?>&selectedTab=5" class='link_style12' style='cursor:pointer'><?php echo getSubstring($orderAddress,45);?></a>
		</td>
		<td class="smalltext3" valign="top">
			<?php echo nl2br($t_message);?>
		</td>
		<td class="smalltext20" valign="top">
			<?php echo $daysAgo;?>
		</td>
		<td class="smalltext20" valign="top">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&orderOf=<?php echo $employeeId;?>&showingEmployeeOrder=1" class='link_style12' style='cursor:pointer'><?php echo $employeeName;?></a>
		</td>
		<td class="smalltext">
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/send-message-pdf-customer.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $memberId;?>&vmsg=<?php echo $t_messageId;?>&selectedTab=5#sendMessages" class='link_style12' style='cursor:pointer'>Verify</a> | <a onclick="delMesg(<?php echo $orderId;?>,<?php echo $memberId;?>,<?php echo $t_messageId;?>,<?php echo $recNo;?>);" class='link_style12' style='cursor:pointer'>Delete</a>
		</td>
	</tr>
	<?php
		}
	}
	else
	{
		echo "<tr><td align='center' class='error' colspan='8' height='50'><b>No New Messages Available To Verify.</b></td></tr><tr><td height='200'></td></tr>";
	}
	echo "</table>";
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>