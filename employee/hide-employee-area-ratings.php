<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES     .   "/includes/check-login.php");
	include(SITE_ROOT				.   "/classes/pagingclass.php");
	include(SITE_ROOT_EMPLOYEES		.	"/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/set-variables.php");
	include(SITE_ROOT_MEMBERS		.	"/classes/members.php");
	include(SITE_ROOT_EMPLOYEES		.	"/classes/orders.php");
	include(SITE_ROOT				.	"/classes/common.php");
	$pagingObj						=   new Paging();
	$memberObj						=   new members();
	$orderObj						=   new orders();
	$commonObj						=   new common();
	$employeeObj					=	new employee();
	$link							=	"";
	if(isset($_REQUEST['recNo']))
	{
		$recNo						=	(int)$_REQUEST['recNo'];
		if(!empty($recNo))
		{
			$link					=	"?recNo=".$recNo;
		}
	}
	if(empty($recNo))
	{
		$recNo						=	0;
	}

	$orderId					=	0;
	$orderAddress				=	"";
	$memberId					=	0;
	$checked					=	"checked";
	$checked1					=	"";
	$display					=	"";
	$display1					=	"none";
	$showform					=	false;
	$link						=	"";
	$search						=	0;

	if(empty($s_hasManagerAccess))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	/*if(!in_array($s_employeeId,$a_hardcodeTopManagers)){
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}*/
	
	$a_existingRatings			=	array();
	$query						=	"SELECT * FROM feedback_rate_text WHERE feedbackText <> '' ORDER BY feedbackTextId";
	$result						=  dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row				=	mysqli_fetch_assoc($result))
		{
			$feedbackTextId		=	$row['feedbackTextId'];
			$feedbackText		=	stripslashes($row['feedbackText']);
			$a_existingRatings[$feedbackTextId]	=	$feedbackText;
		}
	}
	$whereClause		=	"WHERE orderId <> 0 AND rateGiven <> 0";
	$andClause			=	"";
	$andClause1			=	"";
	$orderBy			=	"members_orders.orderAddedOn DESC";
	$queryString		=	"";
	$orderCustomerName	=	"";
	$orderProcessBy		=	"";

	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		$redirectTo		=	"";
		$redirectTo1	=	"";
		$orderAddress	=	trim($orderAddress);
		$orderProcessBy	=	trim($orderProcessBy);
		$orderAddress	=	makeDBSafe($orderAddress);
		if(!empty($orderAddress))
		{
			$redirectTo1 =	"&orderAddress=".$orderAddress;
		}
		if(!empty($orderCustomerName))
		{
			$redirectTo1.=	"&orderCustomerName=".$orderCustomerName;
		}
		if(!empty($orderProcessBy))
		{
			$redirectTo1.=	"&orderProcessBy=".$orderProcessBy;
		}
		if(!empty($redirectTo1))
		{
			$redirectTo =	"?search=1".$redirectTo1;
		}

		ob_clean();
		header("Location:".SITE_URL_EMPLOYEES."/hide-employee-area-ratings.php".$redirectTo);
		exit();
	}
	if(isset($_GET['search']))
	{
		$search			=	$_GET['search'];
		if(!empty($search))
		{
			$queryString.=	"&search=1";
		}
	}
	if(isset($_GET['orderAddress']))
	{
		$orderAddress			=	$_GET['orderAddress'];
		if(!empty($orderAddress))
		{
			$showform			=	true;
			if(is_numeric($orderAddress))
			{
				$andClause		.=	" AND orderId=$orderAddress";
			}
			else
			{
				$t_orderAddress	 =	makeDBSafe($orderAddress);
				$andClause		.=	" AND orderAddress LIKE '%$t_orderAddress%'";
			}
			$queryString		.=	"&orderAddress=$orderAddress";
		}
	}
	if(isset($_GET['orderCustomerName']))
	{
		$orderCustomerName		 =	$_GET['orderCustomerName'];
		if(!empty($orderCustomerName))
		{
			$orderCustomerName	 =	trim($orderCustomerName);
			$t_orderCustomerName =	makeDBSafe($orderCustomerName);
			$memberId			 =	$employeeObj->getSingleQueryResult("SELECT memberId FROM members WHERE completeName='$t_orderCustomerName'","memberId");
			if(!empty($memberId))
			{
				$showform		 =	true;
				$andClause		.=	" AND members_orders.memberId=$memberId";
				$queryString	.=	"&orderCustomerName=$orderCustomerName";
			}
		}
	}
	if(isset($_GET['orderProcessBy']))
	{
		$orderProcessBy		 =	$_GET['orderProcessBy'];
		if(!empty($orderProcessBy))
		{
			$orderProcessBy	     =	trim($orderProcessBy);
			$t_orderProcessBy    =	makeDBSafe($orderProcessBy);
			
			$showform			 =	true;
			$andClause			.=	" AND acceeptedByName LIKE '%$t_orderProcessBy%'";
			$queryString		.=	"&orderProcessBy=$orderProcessBy";
			
		}
	}
	
	if(isset($_REQUEST['recNo']))
	{
		$recNo		    =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo			=	0;
	}
	else
	{
		$link			=	"?recNo=".$recNo;
	}

	if(isset($_GET['hideRating']) && $_GET['hideRating'] == 1 && isset($_GET['memberId']) && isset($_GET['orderId'])){
		
		$orderId					=	(int)$_GET['orderId'];
		$memberId					=	(int)$_GET['memberId'];

		if(!empty($orderId) && !empty($memberId)){
		
			$query						=	"SELECT rateGiven FROM members_orders WHERE orderId=$orderId AND memberId=$memberId AND rateGiven <> 0";
			$result						=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				dbQuery("UPDATE members_orders SET isRateCountingEmployeeSide='no' WHERE orderId=$orderId");

				dbQuery("DELETE FROM all_unreplied_rating WHERE orderId=$orderId");
			}
		}

		ob_clean();
		header("Location:".SITE_URL_EMPLOYEES."/hide-employee-area-ratings.php?search=1".$queryString);
		exit();
	}
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
</script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" />
<script type="text/javascript">
$().ready(function() {
	$("#order").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/employees-pdf-orders.php", {width: 290,selectFirst: false});
});

$().ready(function() {
	
	$("#cutomer").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-customer.php", {width: 220,selectFirst: false});
});

$().ready(function() {
	
	$("#process").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-employee.php", {width: 220,selectFirst: false});
});
function checkValid()
{
	form1	=	document.deleteCustomerOrders;
	if(form1.orderAddress.value == ""  && form1.orderCustomerName.value == "" && form1.orderProcessBy.value === "")
	{
		alert("Please select either an order ID/Address or a customer or an employee.");
		form1.orderAddress.focus();
		return false;
	}
}
function hideCustomerRatings(orderId,memberId,recNo,queryString)
{
	
	//alert(queryString);
	//return false;
	var confirmation = window.confirm("Are you sure to hide this rating for employee and rating calculations?");
	
	if(confirmation == true)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/hide-employee-area-ratings.php?orderId="+orderId+"&memberId="+memberId+"&recNo="+recNo+"&search=1&hideRating=1"+queryString;
	}
}
</script>
<link href="<?php echo SITE_URL;?>/css/facebox.css" media="screen" rel="stylesheet" type="text/css" />
<script src="<?php  echo SITE_URL;?>/script/facebox.js" type="text/javascript"></script>
</script>
<form name="deleteCustomerOrders" action="" method="POST" onsubmit="return checkValid();">
<table width="100%" align="center" border="0" cellpadding="2" cellspacing="2">
<tr>
	<td class="smalltext23" colspan="5">
		<b>HIDE CUSTOMER ORDER RATINGS FOR EMPLOYEES</b>
	</td>
</tr>
<tr>
	<td colspan="5" height="5"></td>
</tr>
<tr>
	<td class="smalltext23" width="8%">
		ID/ADDRESS
	</td>
	<td width="15%">
		<input type="text" name="orderAddress" value="<?php echo $orderAddress;?>" id="order" size="25" style="font-size:18px;font-family:verdana;height:25px;border:1px solid #4d4d4d;">
	</td>
	<td class="smalltext23" width="8%">
		CUSTOMER
	</td>
	<td width="15%">
		<input type="text" name="orderCustomerName" value="<?php echo $orderCustomerName;?>" id="cutomer" size="30" style="font-size:13px;font-family:verdana;height:25px;border:1px solid #4d4d4d;">
	</td>
	<td class="smalltext23" width="8%">
		EMPLOYEE
	</td>
	<td width="15%">
		<input type="text" name="orderProcessBy" value="<?php echo $orderProcessBy;?>" id="process" size="30" style="font-size:13px;font-family:verdana;height:25px;border:1px solid #4d4d4d;">
	</td>
	<td>
		<input type="image" name="name" src="<?php echo SITE_URL;?>/images/submit.jpg" border="0">
		<input type='hidden' name='formSubmitted' value='1'>
	</td>
</tr>
</table>
</form>
<?php
	if($search	==	1)
	{
		$start					  =	0;
		$recsPerPage	          =	50;	//	how many records per page
		$showPages		          =	10;	
		$pagingObj->recordNo	  =	$recNo;
		$pagingObj->startRow	  =	$recNo;
		$pagingObj->whereClause   =	$whereClause.$andClause;
		$pagingObj->recsPerPage   =	$recsPerPage;
		$pagingObj->showPages	  =	$showPages;
		$pagingObj->orderBy		  =	$orderBy;
		$pagingObj->table		  =	"members_orders INNER JOIN members ON members_orders.memberId=members.memberId";
		$pagingObj->selectColumns = "members_orders.*,firstName,lastName";
		$pagingObj->path		  = SITE_URL_EMPLOYEES. "/hide-employee-area-ratings.php";
		$totalRecords = $pagingObj->getTotalRecords();
		if($totalRecords && $recNo <= $totalRecords)
		{
			$pagingObj->setPageNo();
			$recordSet = $pagingObj->getRecords();
			$i		   =	$recNo;
	?>
	<table width='100%' align='center' cellpadding='1' cellspacing='1' border='0'>
		<tr bgcolor="#373737" height="20">
			<td width='2%' class='smalltext8'>&nbsp;</td>
			<td width='25%' class='smalltext8'><b>Order Address</b></td>
			<td width='15%' class='smalltext8'><b>Customer</b></td>
			<td width='15%' class='smalltext8'><b>Process By</b></td>
			<td width='14%' class='smalltext8'><b>Rating</b></td>
			<td width='22%' class='smalltext8'><b>Message</b></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan='9' height="6"></td>
		</tr>
		<?php
			while($row	=   mysqli_fetch_assoc($recordSet))
			{
				$i++;
				$orderId		=	$row['orderId'];
				$memberId		=	$row['memberId'];
				$orderAddedOn	=	showDate($row['orderAddedOn']);
				$orderAddress	=	stripslashes($row['orderAddress']);
				$firstName		=	stripslashes($row['firstName']);
				$lastName		=	stripslashes($row['lastName']);
				$completeName	=	$firstName." ".substr($lastName, 0, 1);
				$status			=	$row['status'];
				$rateGiven		=	$row['rateGiven'];
				$rateGivenDate	=	showDate($row['rateGivenOn']);
				$memberRateMsg	=	stripslashes($row['memberRateMsg']);
				$acceeptedByName=	stripslashes($row['acceeptedByName']);
				$isRateCountingEmployeeSide		=	$row['isRateCountingEmployeeSide'];
				$acceptedBy		=	$row['acceptedBy'];


				if(!empty($rateGiven))
				{
					$ratingGivenBymember	=	$a_existingRatings[$rateGiven];
				}
				else
				{
					$ratingGivenBymember	=	"";
				}

				$bgColor					=	"class='rwcolor1'";
				if($i%2==0)
				{
					$bgColor				=   "class='rwcolor2'";
				}

		?>
		<tr height="23" <?php echo $bgColor;?>>
			<td class='newtext17' valign="top"><?php echo $i;?>)</td>
			<td class='newtext17' valign="top"><a href="<?php echo SITE_URL_EMPLOYEES;?>/view-order-others.php?orderId=<?php echo $orderId;?>&customerId=<?php echo $memberId;?>" class="link_style12"><?php echo $orderAddress;?></a></td>
			<td valign="top"><a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=<?php echo $memberId;?>" class="link_style12"><?php echo $completeName;?></a></td>
			<td valign="top"><a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&showingEmployeeOrder=1&orderOf=<?php echo $acceptedBy;?>" class="link_style12"><?php echo $acceeptedByName;?></a></td>
			<td class='newtext7' valign="top"><?php echo $ratingGivenBymember." on ".$rateGivenDate;?></td>
			<td class='newtext7' valign="top"><?php echo nl2br($memberRateMsg);?></td>
			<td valign="top" class="smalltext1">
				<?php			
					if($isRateCountingEmployeeSide == "yes"){
						echo "<a onClick=\"hideCustomerRatings($orderId,$memberId,$recNo,'$queryString');\" style='cursor:pointer' class='link_style6'>HIDE</a>";	
					}
					else{
						echo "Already Hide";
					}

				?>
			</td>
		</tr>
		<?php
			}
			echo "<tr><td colspan='9'><table width='100%'><tr><td align='right'>";
			$pagingObj->displayPaging($queryString);
			echo "&nbsp;&nbsp;</td></tr></table></td></tr>";
		?>
	</table>
<?php
		}
		else
		{
			echo "<br><center><font class='error'><b>No Record Found</b></font></center>";
		}

	}
	else
	{
		echo "<br><center><font class='error'><b>Please submit the above form</b></font></center><br /><br /><br /><br /><br /><br />";
	}
	include(SITE_ROOT_EMPLOYEES		.   "/includes/bottom.php");
?>