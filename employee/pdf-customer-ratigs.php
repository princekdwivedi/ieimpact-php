<?php
	ob_start();
	session_start();
	ini_set('display_errors', '1');
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/new-top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT			. "/classes/pagingclass1.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	$pagingObj					=  new Paging();
	$memberObj					=  new members();
	$orderObj					=  new orders();
	$a_existingCustomerRatings	=  $orderObj->getFeedbackText();

	$formSearch					=	SITE_ROOT_EMPLOYEES."/forms/search-general-order-form.php";

	if(isset($_SESSION['isSearchPDFByRatings']))
	{
		unset($_SESSION['isSearchPDFByRatings']);
	}
	

	$fromToDate			=	date('Y-m-d', strtotime("-30 days", strtotime($today_year."-".$today_month."-".$today_day)));

	$selectedDisplay	=	0;
	$class				=	"link_style5";
	
	$whereClause		=	"WHERE members_orders.orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND status=2 AND hasQaDone=1 AND rateGiven <> 0 AND members_orders.isVirtualDeleted=0 AND isTestAccount=0 AND isRateCountingEmployeeSide='yes'";
	$orderBy			=	"rateGivenOn DESC,rateGivenTime DESC";
	$queryString		=	"";
	if(isset($_REQUEST['recNo']))
	{
		$recNo		    =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo			=	0;
	}

	$andClause1			=	" AND orderAddedOn >= '$fromToDate' AND orderAddedOn <= '$nowDateIndia'";
	$andClause2			=	"";

	if(isset($_GET['selectedDisplay']))
	{
		$selectedDisplay=	(int)$_GET['selectedDisplay'];

		if(!empty($selectedDisplay) && is_numeric($selectedDisplay))
		{
			$andClause2	=	" AND rateGiven=$selectedDisplay";
			$queryString=	"&selectedDisplay=".$selectedDisplay;
		}
	}

	$a_viewingOfRatings	=	array("0"=>"All Ratings","1"=>"Awful Ratings","2"=>"Poor Ratings","3"=>"Fair Ratings","4"=>"Good Ratings","5"=>"Excellent Ratings");

	if(isset($_GET['customerId']) && isset($_GET['orderId']) && isset($_GET['type']) && isset($_GET['isSelectRating']))
	{
		$customerId			=	$_GET['customerId'];
		$orderId			=	$_GET['orderId'];
		$type				=	$_GET['type'];
		$isSelectRating		=	$_GET['isSelectRating'];

		if(!empty($customerId) && !empty($orderId) && !empty($type) && !empty($isSelectRating))
		{
			$_SESSION['isSearchPDFByRatings']	=	$type;

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=$orderId&customerId=$customerId");
			exit();
		}
	}
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/wz_tooltip.js"></script>
<script type="text/javascript">
function openDisplayMessage(orderId)
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/pop-up-customer-rating-message.php?orderId="+orderId;
	prop = "toolbar=no,scrollbars=yes,width=500,height=300,top=100,left=100";
	window.open(path,'',prop);
}
function openRatingReply(orderId)
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/pop-up-customer-rating-message.php?orderId="+orderId+"&showExplain=1";
	prop = "toolbar=no,scrollbars=yes,width=500,height=300,top=100,left=100";
	window.open(path,'',prop);
}
function addRatingOrderSessionID(customerId,orderId,type)
{
	window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-ratigs.php?customerId="+customerId+"&orderId="+orderId+"&type="+type+"&isSelectRating=1";
}
</script>
<table width='98%' align='center' cellpadding='0' cellspacing='0' border='0'>
<tr>
	<td colspan="10">
		<?php
			include($formSearch);
		?>
	</td>
</tr>
<tr>
	<td colspan="2" class="smalltext4">
		<b>
			DISPLAYING :
		</b>
	</td>
	<td colspan="7">
		<?php
			foreach($a_viewingOfRatings as $k=>$value)
			{
				if($k	==	$selectedDisplay)
				{
					$class				=	"link_style4";
				}
				else
				{
					$class				=	"link_style5";
				}

				$dash					=	"|";
				if($k	==	5)
				{
					$dash				=	"";
				}
				
				echo "<a href='".SITE_URL_EMPLOYEES."/pdf-customer-ratigs.php?selectedDisplay=$k' class='$class' title='Show Me $value'>".strtoupper($value)."</a>&nbsp;<font class='smalltext2'>".$dash."</font>&nbsp;";
			}
		?>
	</td>
</tr>
<tr>
	<td height="10"></td>
</tr>

<?php

	$start					  =	0;
	$recsPerPage	          =	25;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause2;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"members_orders INNER JOIN members_orders_reply ON members_orders.orderId=members_orders_reply.orderId INNER JOIN members ON members_orders.memberId=members.memberId";
	$pagingObj->selectColumns = "members_orders.*,firstName,lastName,appraisalSoftwareType,qaDoneBy,hasQaDone,totalOrdersPlaced,orderCompletedTime";

	$pagingObj->path		  = SITE_URL_EMPLOYEES."/pdf-customer-ratigs.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet =   $pagingObj->getRecords();
		$i	       =	$recNo;
?>
<tr>
	<td colspan="12" height="20"></td>
</tr>
<tr>
	<td colspan="12" class="textstyle1">
		<b>:: VIEW ALL FEEDBACKS(CUSTOMER RATE) ON COMPLETED ORDERS FOR YOU ::</b>
	</td>
</tr>
<tr>
	<td colspan="12" height="5"></td>
</tr>
<tr bgcolor="#373737" height="20">
	<td class="smalltext12" width="4%">&nbsp;<b>SR. NO</b></td>
	<td class="smalltext12" width="13%"><b>CUSTOMER NAME</b></td>
	<td class="smalltext12" width="23%"><b>ORDER ADDRESS</b></td>
	<td class="smalltext12" width="7%"><b>RATE</b></td>
	<td class="smalltext12" width="16%"><b>COMPLETED ON</b></td>
	<td class="smalltext12" width="16%"><b>RATED ON</b></td>
	<td class="smalltext12" width="10%"><b>PROCESS BY</b></td>
	<td class="smalltext12"><b>QA BY</b></td>
</tr>
<?php
		while($row	=   mysqli_fetch_assoc($recordSet))
		{
			$i++;
			$orderId				=	$row['orderId'];
			$memberId				=	$row['memberId'];
			$orderAddedOn			=	$row['orderAddedOn'];
			$orderType				=	$row['orderType'];
			$orderAddress			=	stripslashes($row['orderAddress']);
			$firstName				=	stripslashes($row['firstName']);
			$lastName				=	stripslashes($row['lastName']);
			$completeName			=	$firstName." ".substr($lastName, 0, 1);
			$appraisalSoftwareType	=	$row['appraisalSoftwareType'];
			$orderAddedOn			=	$row['orderAddedOn'];
			$orderCompletedOn		=	$row['orderCompletedOn'];
			$qaDoneBy				=	$row['qaDoneBy'];
			$rateGiven				=	$row['rateGiven'];
			$rateGivenTime			=	$row['rateGivenTime'];
			$orderCompletedTime		=	$row['orderCompletedTime'];
			$acceptedBy				=	$row['acceptedBy'];
			$rateGivenOn			=	$row['rateGivenOn'];
			$memberRateMsg			=	stripslashes($row['memberRateMsg']);
			$customersOwnOrderText	=	stripslashes($row['customersOwnOrderText']);
			$acceptedByName			=	stripslashes($row['acceeptedByName']);
			$qaByName			    =	stripslashes($row['qaDoneByName']);
			$memberRateMsg			=	getSubstring($memberRateMsg,25);		

	
			$orderText				=	$a_customerOrder[$orderType];
			if($orderType == 6 && !empty($customersOwnOrderText))
			{
				$orderText			=	$orderText." (".$customersOwnOrderText.")";
			}			

			$bgColor				=	"class='rwcolor1'";
			if($i%2==0)
			{
				$bgColor			=	"class='rwcolor2'";
			}
	?>
			<tr height="25" <?php echo $bgColor;?>>
				<td class="textstyle" valign="top">&nbsp;<b><?php echo $i;?>)</b></td>
				<td valign="top">
					<?php 
						echo "<a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=$memberId' class='link_style12' style='cursor:pointer'>".ucwords(strtolower($completeName))."</a>";
					?>
				</td>
				<td valign="top">
					<?php 
						echo "<a onclick='addRatingOrderSessionID($memberId,$orderId,$rateGiven)' title='View this order' class='link_style12' style='cursor:pointer'>$orderAddress</a>";
						//echo "<a href='".SITE_URL_EMPLOYEES."/view-order-others.php?orderId=$orderId&customerId=$memberId' class='link_style12'>$orderAddress</a>";
					?>
					<br>
					<!-- <b>
						<?php echo $orderAddress;?>
					</b> -->
				</td>
				<td valign="top">
					<?php
						if(!empty($rateGiven))
						{
							$tipText1		=	"";
							if(!empty($memberRateMsg))
							{
								$tipText1	=	$memberRateMsg;
								$tipText1	=	stringReplace('"',"",$tipText1);
								$tipText1	=	stringReplace("'","",$tipText1);
								$tipText1	=	stringReplace("#","",$tipText1);
								$tipText1	=	stringReplace("<br />","",$tipText1);
							}
							else
							{
								$tipText1	=	"";
							}
						?>
						<img src="<?php echo SITE_URL;?>/images/rating/<?php echo $rateGiven;?>.png"  onmouseover="Tip('<?php echo $tipText1;?>')" onmouseout="UnTip();">
						<?php
							echo "(<a onclick='openDisplayMessage($orderId)' style='cursor:pointer' class='link_style12' style='cursor:pointer;'>View</a>)";
													
						}
					?>
				</td>
				<td class="textstyle" valign="top"><?php echo showDateTimeFormat($orderCompletedOn,$orderCompletedTime);?></td>
				<td class="textstyle" valign="top"><?php echo showDateTimeFormat($rateGivenOn,$rateGivenTime);?></td>
				<td class="textstyle" valign="top"><b><?php echo $acceptedByName;?></b></td>
				<td class="textstyle" valign="top"><b><?php echo $qaByName;?></b></td>
			</tr>
			
	<?php
			
		}
		echo "<tr><td align='right' colspan='12'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr>";		
	}
	else
	{
		echo "<tr><td align='center' class='error' colspan='8' height='50'><b>No Feedback Available For You !!</b></td></tr><tr><td height='200'></td></tr>";
		
	}
    echo "</table>";
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>