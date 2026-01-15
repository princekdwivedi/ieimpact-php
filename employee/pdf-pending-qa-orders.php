<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/includes/send-mail.php");
	$pagingObj					=  new Paging();
	$memberObj					=  new members();
	$orderObj					=  new orders();

	if(isset($_GET['orderId']) && isset($_GET['customerId']) && isset($_GET['replyId']) && isset($_GET['doneQa']))
	{
		$orderId		=	$_GET['orderId'];
		$customerId		=	$_GET['customerId'];
		$doneQa			=	$_GET['doneQa'];
		$replyId		=	$_GET['replyId'];

		if(!empty($orderId) && $doneQa == 1)
		{
			$orderObj->markOrderQaDone($orderId,$replyId,$customerId,$s_employeeId);
			
			$a_managerEmails	=	$orderObj->getAllMangersEmails();

			if($result	=	$orderObj->getOrderDetails($orderId,$customerId))
			{
				$row			=	mysql_fetch_assoc($result);
				$orderAddress			=	stripslashes($row['orderAddress']);
				$orderType		=	$row['orderType'];
				$orderAddedOn	=	showDate($row['orderAddedOn']);
				$firstName		=	stripslashes($row['firstName']);
				$lastName		=	stripslashes($row['lastName']);
				$customerEmail	=	$row['email'];
				$customerSecondaryEmail	=	$row['secondaryEmail'];
				$hasReceiveEmails	=	$row['noEmails'];

				$orderText		=	$a_customerOrder[$orderType];
				$customerName	=   $firstName." ".$lastName;
				$customerName	=	ucwords($customerName);

				$orderAcceptedBy = $orderObj->getOrderAcceptedBY($orderId,$customerId);
				if(!empty($orderAcceptedBy))
				{
					$acceptedByName	=	$employeeObj->getEmployeeName($orderAcceptedBy);
					if(empty($acceptedByName))
					{
						$acceptedByName	=	"Unknown";
					}
				}
				else
				{
					$acceptedByName	=	"Unknown";
				}

				$from			=	ORDER_FROM_EMAIL;
				$fromName		=	"ieIMPACT";
				$to				=	$customerEmail; 
				$mailSubject	=	"Reply of your order No - $orderAddress from ieIMPACT";
				$templateId		=	TEMPLATE_SENDING_REPLY_ORDER;
				if($hasReceiveEmails == 0)
				{
					$a_templateData	=	array("{name}"=>$customerName,"{orderNo}"=>$orderAddress,"{orderType}"=>$orderText);
					sendTemplateMail($from, $fromName, $to, $mailSubject, $templateId, $a_templateData);

					if(!empty($customerSecondaryEmail))
					{
						sendTemplateMail($from, $fromName, $customerSecondaryEmail, $mailSubject, $templateId, $a_templateData);
					}
				}
				if(!empty($a_managerEmails))
				{
					foreach($a_managerEmails as $k=>$value)
					{
						list($managerEmail,$managerName)	=	explode("|",$value);
						
						$to2			=	$managerEmail; 
						$mailSubject2	=	"Reply details of Customer - $customerName on order - $orderAddress";
						$templateId2	=	TEMPLATE_SENDING_REPLY_TO_MANAGER;

						$a_templateData2	=	array("{managerName}"=>$managerName,"{orderNo}"=>$orderAddress,"{orderDate}"=>$orderAddedOn,"{orderType}"=>$orderText,"{customerName}"=>$customerName,"{acceptedBy}"=>$acceptedByName,"{qaDoneBy}"=>$s_employeeName);

						sendTemplateMail($from, $fromName, $to2, $mailSubject2, $templateId2, $a_templateData2);
					}
				}
			}
		}
		
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=$orderId&customerId=$customerId");
		exit();
	}
?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
<?php
	$whereClause	=	"WHERE hasRepliedFileUploaded=1 AND hasQaDone=0 AND status=1";
	$orderBy		=	"replyFileAddedOn DESC,replyFileAddedTime DESC";
	$queryString	=	"";
	if(isset($_REQUEST['recNo']))
	{
		$recNo		    =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo	=	0;
	}
	
	$start					  =	0;
	$recsPerPage	          =	20;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"members_orders_reply INNER JOIN members_orders ON members_orders_reply.orderId=members_orders.orderId INNER JOIN members ON members_orders.memberId=members.memberId";
	$pagingObj->selectColumns = "replyId,members_orders.*,firstName,lastName,appraisalSoftwareType";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/pdf-pending-qa-orders.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
		$i	=	$recNo;
?>
<tr>
	<td colspan="7" height="20"></td>
</tr>
<tr>
	<td colspan="8" class="heading1">
		:: VIEW ALL NEW QA ORDERS FOR YOU ::
	</td>
</tr>
<tr>
	<td colspan="8" height="5"></td>
</tr>
<tr bgcolor="#373737" height="20">
	<td class="smalltext8" width="5%">&nbsp;<b>Sr. No</b></td>
	<td class="smalltext8" width="15%"><b>Customer Name</b></td>
	<td class="smalltext8" width="25%"><b>Order Address</b></td>
	<td class="smalltext8" width="10%"><b>Order Type</b></td>
	<td class="smalltext8" width="6%"><b>File Type</b></td>
	<td class="smalltext8" width="10%"><b>Order On</b></td>
	<!-- <td class="smalltext8" width="15%"><b>Other Employee's Access</b></td> -->
	<td></td>
</tr>
<tr>
	<td colspan="8">
		<hr size="1" width="100%" bgcolor="#e4e4e4">
	</td>
</tr>
<?php
		while($row	=   mysql_fetch_assoc($recordSet))
		{
			$i++;
			$replyId		=	$row['replyId'];
			$orderId		=	$row['orderId'];
			$memberId		=	$row['memberId'];
			$orderAddedOn	=	$row['orderAddedOn'];
			$orderType		=	$row['orderType'];
			$orderAddress	=	stripslashes($row['orderAddress']);
			$firstName		=	stripslashes($row['firstName']);
			$lastName		=	stripslashes($row['lastName']);
			$appraisalSoftwareType	=	$row['appraisalSoftwareType'];
			$orderAddedOn	=	showDate($row['orderAddedOn']);
			$orderAddedTime	=	$row['orderAddedTime'];
			$orderText		=	$a_customerOrder[$orderType];

			$customerName	=   $firstName." ".$lastName;
			if($orderAddedTime != "00:00:00")
			{
				$orderTime		=	", ".date("H:i",strtotime($orderAddedTime))." IST";
			}
			else
			{
				$orderTime		=	"";
			}
			
			$appraisalText	=	$a_appraisalFileTypes[$appraisalSoftwareType];

		?>
			<tr>
				<td class="smalltext2" valign="top"><b><?php echo $i;?>)</b></td>
				<td class="smalltext2" valign="top"><b><?php echo ucwords($customerName);?></b></td>
				<td class="smalltext2" valign="top"><b><?php echo $orderAddress;?></b></td>
				<td class="smalltext2" valign="top"><b><?php echo $orderText;?></b></td>
				<td class="error" valign="top" align="center"><b><?php echo $appraisalText;?></b></td>
				<td class="smalltext2" valign="top"><b><?php echo $orderAddedOn;?></b></td>
				<td class="smalltext2" valign="top"><b>
					<a href="<?php echo SITE_URL_EMPLOYEES;?>/view-qa-order.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $memberId;?>"  class="link_style2">VIEW ORDER</a> | <a href="<?php echo SITE_URL_EMPLOYEES;?>/view-qa-order.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $memberId;?>&doneQa=1#mark"  class="link_style2">MARKED AS QA DONE</a><!-- <a href="javascript:doneQaOrder(<?php echo $orderId;?>,<?php echo $memberId?>,<?php echo $replyId?>)" class="link_style2">MARKED AS QA DONE</a> --></b> 
				</td>
			</tr>
			<tr>
				<td colspan="8">
					<hr size="1" width="100%" bgcolor="#e4e4e4">
				</td>
			</tr>
	<?php
			
		}
		echo "<tr><td align='right' colspan='8'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr>";		
	}
	else
	{
		echo "<tr><td height='50'></td></tr><tr><td align='center' class='error'><b>No New Qa Orders Available For You !!</b></td></tr><tr><td height='200'></td></tr>";
		
	}
    echo "</table>";
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>