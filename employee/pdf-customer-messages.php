<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES		. "/includes/new-top.php");
	include(SITE_ROOT_EMPLOYEES		. "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES		. "/classes/employee.php");
	include(SITE_ROOT				. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		. "/includes/common-array.php");
	$employeeObj					= new employee();
	include(SITE_ROOT_EMPLOYEES		. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES		. "/includes/check-pdf-login.php");
	if($_SESSION['employeeId'] == 3){
		include(SITE_ROOT			    . "/classes/pagingclass-test.php");
		
	}
	else{
		include(SITE_ROOT			    . "/classes/pagingclass.php");
	}
	include(SITE_ROOT_MEMBERS		. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES		. "/classes/orders.php");
	$pagingObj						=  new Paging();
	$memberObj						=  new members();
	$orderObj						=  new orders();
	
	$formSearch						=  SITE_ROOT_EMPLOYEES."/forms/search-general-order-form.php";
	$all_pdfEmployees				=  array();
	$a_allDeactivatedEmployees      =  array();
	$query							=  "SELECT employeeId,firstName,fullName,isActive FROM employee_details WHERE  hasPdfAccess=1 ORDER BY firstName";
	$result							=	dbQuery($query);
	if(mysqli_num_rows($result)){
		while($row					=	mysqli_fetch_assoc($result)){
			$employeeId				=	$row['employeeId'];
			$firstName				=	stripslashes($row['firstName']);
			$fullName 	            =	stripslashes($row['fullName']);
			$isActive				=	$row['isActive'];
			if($isActive 			== 1){
				$all_pdfEmployees[$employeeId] = $firstName;
			}
			elseif($isActive 		== 0){
				$a_allDeactivatedEmployees[$employeeId] = $fullName;
			}

		}
	}

	

	if(isset($_REQUEST['recNo']))
	{
		$recNo					 =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo					 =	0;
	}

	function getFileSize($fileSize)
	{
		if($fileSize		   <= 0)
		{
			$fileSize			=	"";
		}
		else
		{
			$fileSize			=	$fileSize/1024;

			$fileSize			=	round($fileSize,2);

			$fileSize			=	$fileSize." KB";
		}

		return $fileSize;
	}
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>


<script type="text/javascript">
	function serachRedirectFileType(addUrl)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php"+addUrl;
	}
	function refreshIframePage(text,text1)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?"+text1+"#"+text;
	}
	function showCustomerMessage(orderId,customerId)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/pop-up-customer-message.php?orderId="+orderId+"&customerId="+customerId;
		prop = "toolbar=no,scrollbars=yes,width=600,height=500,top=100,left=100";
		window.open(path,'',prop);
	}
	function replyCustomerGeneralMessage(generalMsgId,memberId)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/reply-customer-general-message.php?generalMsgId="+generalMsgId+"&memberId="+memberId;
		prop = "toolbar=no,scrollbars=yes,width=600,height=500,top=100,left=100";
		window.open(path,'',prop);
	}
	function sendingNewGeneralMsg(memberId)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/sending-customer-general-message.php?memberId="+memberId;
		prop = "toolbar=no,scrollbars=yes,width=1100,height=600,top=100,left=100";
		window.open(path,'',prop);
	}
	function downloadGeneralMessageFile(url)
	{
		//window.open(url, "_blank");
		 location.href   = url;
	}
	function replyAllMessageForcefully(messageId,memberId,type)
	{
		path = "<?php echo SITE_URL_EMPLOYEES?>/marked-forcefully-replied.php?messageId="+messageId+"&memberId="+memberId+"&type="+type;
		prop = "toolbar=no,scrollbars=yes,width=800,height=600,top=100,left=100";
		window.open(path,'',prop);
	}
</script>
<table width="100%" align="center" cellpadding="0" cellpadding="0" border="0" style="border:0px solid #e4e4e4">
	<tr height="30">
		<td class="textstyle3"><b>VARIOUS ORDER MESSGAES</b></td>
	</tr>
	<tr>
		<td colspan="10">
			<?php
				include($formSearch);

				$totalNoOfUnrepliedOrders		=  0;//$orderObj->getAllNewUnrepliedMessages();
				//$totalUnrepliedOrdersMsg		=  $orderObj->getTotalUnrepliedOrderMessage();
				$totalUnrepliedRatingMsges		=  $totalUnrepliedRatingMsg;//$orderObj->getAllTotalUnrepliedRatingMessage();
				//$totalUnrepliedGeneralMsg		=  $orderObj->getAllTotalUnrepliedGeneralMessage();

				$unrepliedShowNumber			=	"";
				$unrepliedShowratingNumber		=	"";
				$unRepliedGeneralNumber			=	"";
				$donotCallSearchJquery			=	1;
				if(!empty($totalUnrepliedOrdersMsg))
				{
					$unrepliedShowNumber		=	"-".$totalUnrepliedOrdersMsg;
				}
				if(!empty($totalUnrepliedRatingMsges))
				{
					$unrepliedShowratingNumber	=	"<font color='#ff0000'>($totalUnrepliedRatingMsges)</font>";
				}
				if(!empty($totalUnrepliedGeneralMsg))
				{
					$unRepliedGeneralNumber		=	"<font color='#ff0000'>($totalUnrepliedGeneralMsg)</font>";
				}

				$link_array 		=	array();

				$link_array[]       =   "showAllOrders";
				$link_array[]       =   "unrepliedMsg";
				$link_array[]       =   "unrepliedRatingMsg";
				$link_array[]       =   "unrepliedGeneralMsg";
				$link_array[]       =   "internalMsg";
				$link_array[]       =   "empsent";

				$color1 			=	"#42bcf4";
				$color2 			=	"#42bcf4";
				$color3 			=	"#42bcf4";
				$color4 			=	"#42bcf4";
				$color5 			=	"#42bcf4";
				$color6 			=	"#42bcf4";

				$fontColor 			=	"#ff0000";
				$fontColor1 		=	"#ff0000";
				$fontColor2 		=	"#ff0000";
				$fontColor3 		=	"#ff0000";
				$fontColor4 		=	"#ff0000";
	
				if(isset($_GET['showAllOrders']) && $_GET['showAllOrders'] == 1)
				{
					$color1 			=	"#f44141";
				}
				elseif(isset($_GET['unrepliedMsg']) && $_GET['unrepliedMsg'] == 1)
				{
					$color2 			=	"#f44141";
					$fontColor 			=	"#ffffff";
				}
				elseif(isset($_GET['unrepliedRatingMsg']) && $_GET['unrepliedRatingMsg'] == 1)
				{
					$color3 			=	"#f44141";
					$fontColor1 		=	"#ffffff";
				}
				elseif(isset($_GET['unrepliedGeneralMsg']) && $_GET['unrepliedGeneralMsg'] == 1)
				{
					$color4 			=	"#f44141";
					$fontColor2 		=	"#ffffff";
				}
				elseif(isset($_GET['internalMsg']) && $_GET['internalMsg'] == 1)
				{
					$color5 			=	"#f44141";
					$fontColor3 		=	"#ffffff";
				}
				elseif(isset($_GET['empsent']) && $_GET['empsent'] == 1)
				{
					$color6 			=	"#f44141";
					$fontColor4 		=	"#ffffff";
				}

			?>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<table width="99%" align="center" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="17%" style="border:1px solid #bebebe;background-color:<?php echo $color1;?>;height:33px;">
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?showAllOrders=1" class="link_button_plus1">CUSTOMER'S ALL MESSAGES</a>
					</td>
					<td width="1%">
						&nbsp;
					</td>
					<td width="15%" style="border:1px solid #bebebe;background-color:<?php echo $color2;?>;height:33px;">
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?unrepliedMsg=1" class="link_button_plus1">CUSTOMER MESSAGES
						<?php
							if(!empty($totalUnrepliedOrdersMsg)){
						?>
						(<font color="<?php echo $fontColor;?>"><?php echo $totalUnrepliedOrdersMsg;?></font>)
						<?php
							}
						?>
						</a>
					</td>
					<td width="1%">
						&nbsp;
					</td>
					<td width="15%" style="border:1px solid #bebebe;background-color:<?php echo $color3;?>;height:33px;">
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?unrepliedRatingMsg=1" class="link_button_plus1">UNREPLIED RATINGS
						<?php
							if(!empty($totalUnrepliedRatingMsges)){
						?>
						(<font color="<?php echo $fontColor1;?>"><?php echo $totalUnrepliedRatingMsges;?></font>)
						<?php
							}
						?>
						</a>
					</td>
					<td width="1%">
						&nbsp;
					</td>
					<td width="15%" style="border:1px solid #bebebe;background-color:<?php echo $color4;?>;height:33px;">
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?unrepliedGeneralMsg=1" class="link_button_plus1">GENERAL MESSAGES
						<?php
							if(!empty($totalUnrepliedGeneralMsg)){
						?>
						(<font color="<?php echo $fontColor2;?>"><?php echo $totalUnrepliedGeneralMsg;?></font>)
						<?php
							}
						?>
						</a>
					</td>
					<td width="1%">
						&nbsp;
					</td>
					<td width="20%" style="border:1px solid #bebebe;background-color:<?php echo $color5;?>;height:33px;">
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?internalMsg=1" class="link_button_plus1">INTERNAL EMPLOYEE MESSAGES</a>
					</td>
					<td width="1%">
						&nbsp;
					</td>
					<td width="14%" style="border:1px solid #bebebe;background-color:<?php echo $color6;?>;height:33px;">
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?empsent=1" class="link_button_plus1">SENT BY EMPLOYEE</a>
					</td>
					<td>
						&nbsp;
					</td>
				</tr>
			</table>
			<table width="99%" align="center" cellpadding="0" cellspacing="0" style="border:2px solid ">
				<tr>
					<td>
						<?php
							if(isset($_GET['showAllOrders']) && $_GET['showAllOrders'] == 1)
							{
								include(SITE_ROOT_EMPLOYEES . "/includes/customers-all-messages.php");
							}
							elseif(isset($_GET['unrepliedMsg']) && $_GET['unrepliedMsg'] == 1)
							{
								include(SITE_ROOT_EMPLOYEES . "/includes/customers-unreplied-messages.php");
							}
							elseif(isset($_GET['unrepliedRatingMsg']) && $_GET['unrepliedRatingMsg'] == 1)
							{
								include(SITE_ROOT_EMPLOYEES . "/includes/customers-unreplied-rating-messages.php");
							}
							elseif(isset($_GET['unrepliedGeneralMsg']) && $_GET['unrepliedGeneralMsg'] == 1)
							{
								include(SITE_ROOT_EMPLOYEES . "/includes/customers-unreplied-general-messages.php");
							}
							elseif(isset($_GET['internalMsg']) && $_GET['internalMsg'] == 1)
							{
								include(SITE_ROOT_EMPLOYEES . "/includes/internal-employee-messages.php");
							}
							elseif(isset($_GET['empsent']) && $_GET['empsent'] == 1)
							{
								include(SITE_ROOT_EMPLOYEES . "/includes/employee-sent-messages.php");
							}
						?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>
