<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES   	.   "/classes/employee.php");
	$employeeObj					=	new employee();
	$displayEmployeeTargetDone      =   1;
	include(SITE_ROOT_EMPLOYEES		.   "/includes/new-ai-top.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/check-login.php");

	include(SITE_ROOT_EMPLOYEES		.   "/includes/common-array.php");
	
	include(SITE_ROOT_EMPLOYEES		.   "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES		.   "/classes/orders.php");
	$orderObj						=   new orders();


	////////////////////////// CHECK IS ADDED TODAY/YESTERDAY STATUS ////////////////////////
	$yesterdayDate					=	date('Y-m-d', strtotime("-1 day", strtotime($nowDateIndia)));

	////////////////////////////////////////////////////////////////////////////////////

	$a_officeIPAddress				=	array();
	$visitorIpAddress				=	VISITOR_IP_ADDRESS;
	$employeeImagePath				=	SITE_ROOT."/files/employee-images/";
	$employeeImageUrl				=	SITE_URL."/files/employee-images/";

	list($currentY,$currentM,$currentD)	=	explode("-",$nowDateIndia);

	$nonLeadingZeroMonth		=	$currentM;
	if($currentM < 10 && strlen($currentM) > 1)
	{
		$nonLeadingZeroMonth	=	substr($currentM,1);
	}

	$query						=	"SELECT * FROM office_ip_addresses_list WHERE isActive='yes'";
	$result						=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row				=	mysqli_fetch_assoc($result)){
			$ipAddress			=	stripslashes($row['ipAddress']);
			$isActive			=	stripslashes($row['isActive']);

			$a_officeIPAddress[]		=	$ipAddress;
		}
	}
	$a_officeIPAddress			=	"";
	
	if(isset($_GET['messageId']) && isset($_GET['operation']))
	{
		$messageId		=	(int)$_GET['messageId'];
		$operation		=	(int)$_GET['operation'];
		if(!empty($messageId))
		{
			if($operation	==	1)
			{
				dbQuery("UPDATE employee_messages SET isRead=1,readOn='".CURRENT_DATE_INDIA."' WHERE employeeId=$s_employeeId AND messageId=$messageId AND isDeleted=0 AND isRead=0");
			}
			elseif($operation	==	2)
			{
				dbQuery("UPDATE employee_messages SET isDeleted=1,deletedOn='".CURRENT_DATE_INDIA."' WHERE employeeId=$s_employeeId AND messageId=$messageId AND isDeleted=0 AND isRead=1");
			}

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES ."/employee-details.php");
			exit();
		}
	}

	$allTotalCustomersNewOrders	=	$employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total FROM members_orders WHERE orderId > ".MAX_SEARCH_EMPLOYEE_ORDER_ID." AND status=0 AND orderAddedOn >= '2012-04-01' AND isDeleted=0 AND isVirtualDeleted=0 AND isNotVerfidedEmailOrder=0","total");

	$totalUnrepliedOrdersMsg	=	$orderObj->getTotalUnrepliedOrderMessage();
	$totalUnrepliedRatingMsg	=	$orderObj->getAllTotalUnrepliedRatingMessage();
	$a_unrepliedGeneralMsg		=	$orderObj->getAllUnrepliedGeneralMessageCustomers();
	$totalUnrepliedGeneralMsg   =   count($a_unrepliedGeneralMsg);
	$totalUncheckedOrders		=	$orderObj->getAllTotalUncheckedOrders();
	$totalExceedTatOrders		=	$orderObj->getAllTotalExceedTatOrders();

?>
<script type="text/javascript">


function addWorkStatus(flag)
{
	var msg			=  "Yesterday you didn't added your daily work, add now.";
	var param       =  "Y";
	if(flag == "2"){
		var msg	    =  "You didn't added your daily work, add now.";
		var param   =  "T";
	}
	var confirmation= window.confirm(msg);
	if(confirmation == true)
	{
		window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/daily-status-report.php?param=0h876qwe335432mnddkjf787l4hdr&h=7hytbg577&type='+param;
	}
}

var employeeDetailsBaseUrl = '<?php echo SITE_URL_EMPLOYEES;?>/employee-details.php';

function readDeleteNotice(messageId,operation)
{
	if(operation == 1)
	{
		// Use modal instead of confirm
		if(typeof confirmMarkAsRead === 'function') {
			confirmMarkAsRead(messageId, employeeDetailsBaseUrl);
		} else {
			var confirmation = window.confirm("Are you sure to mark as read?");
			if(confirmation == true) {
				window.location.href=employeeDetailsBaseUrl+'?messageId='+messageId+'&operation='+operation;
			}
		}
	}
	else
	{
		// Use modal for delete confirmation
		if(typeof confirmDeleteMessage === 'function') {
			confirmDeleteMessage(messageId, employeeDetailsBaseUrl);
		} else {
			var confirmation = window.confirm("Are you sure to delete this message?");
			if(confirmation == true) {
				window.location.href=employeeDetailsBaseUrl+'?messageId='+messageId+'&operation='+operation;
			}
		}
	}
}
function openWindow(messageId)
{
	path = "<?php echo SITE_URL_EMPLOYEES?>/reply-message.php?messageId="+messageId;
	prop = "toolbar=no,scrollbars=yes,width=650,height=450,top=50,left=100";
	window.open(path,'',prop);
}


function addEditMemberProfilePhoto(flag)
{
	path			=	"<?php echo SITE_URL_EMPLOYEES;?>/add-edit-profile-photo.php?P="+flag;
	properties	=	"height=360,width=440,top=150,left=250,scrollbars=yes,top=100,left=200";
	it			=	window.open(path,'',properties);
}

function serachRedirectFileType(addUrl,extraAdd)
{	
	window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php"+addUrl+extraAdd;
}
</script>
<div class="employee-details-container">
	<div class="employee-details-row employee-details-single-row">
		<div class="employee-details-col employee-details-col-quicklinks">
			<div class="quick-links-card">
				<h3>Quick Links</h3>
				<?php
					if(!empty($totalNewPdfOrders))
					{
				?>
				<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&searchOrderType=1&showPageOrders=50<?php echo $addTopUrlExtraLinkTestQ;?>" class="quick-link-item" title='View new orders'>NEW ORDERS - <?php echo $totalNewPdfOrders;?></a>
				<?php
					}
				?>
				<a onclick="serachRedirectFileType('?isSubmittedForm=1&orderOf=<?php echo $s_employeeId;?>&showingEmployeeOrder=1&Olink=1','<?php echo $addTopUrlExtraLinkTestQ;?>')" class="quick-link-item" title='View all of your processed orders' style="cursor:pointer;">ALL YOUR ORDERS</a>
				<a onclick="serachRedirectFileType('?isSubmittedForm=1&orderOf=<?php echo $s_employeeId;?>&showingEmployeeOrder=1&displayTypeCompleted=1&Olink=2','<?php echo $addTopUrlExtraLinkTestQ;?>')" class="quick-link-item" title='View all of your QA orders' style="cursor:pointer;">ALL YOUR QA ORDERS</a>


				<?php
				if(!empty($allTotalCustomersNewOrders) && !empty($s_hasManagerAccess))
				{
					$assignNewUrl	 =	SITE_URL_EMPLOYEES."/assign-all-new-orders.php".$topUrlExtraLinkTestQ;
			?>
			<a href="<?php echo $assignNewUrl;?>" class="quick-link-item">ASSIGN ALL NEW ORDERS - <?php echo $allTotalCustomersNewOrders;?></a>
			<?php
				}
				if(!empty($totalUnrepliedOrdersMsg))
				{
			?>
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?unrepliedMsg=1<?php echo $addTopUrlExtraLinkTestQ;?>#second" class="quick-link-item">UNREPLIED MESSAGES - <?php echo $totalUnrepliedOrdersMsg;?></a>
			<?php
				}
				if(!empty($totalUnrepliedRatingMsg))
				{
			?>
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?unrepliedRatingMsg=1<?php echo $addTopUrlExtraLinkTestQ;?>#third" class="quick-link-item">UNREPLIED RATINGS - <?php echo $totalUnrepliedRatingMsg;?></a>
			<?php
				}
				if(!empty($totalUnrepliedGeneralMsg))
				{
			?>
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/pdf-customer-messages.php?unrepliedGeneralMsg=1<?php echo $addTopUrlExtraLinkTestQ;?>#fifth" class="quick-link-item">GENERAL MESSAGES - <?php echo $totalUnrepliedGeneralMsg;?></a>
			<?php
				}
				if(!empty($totalExceedTatOrders))
				{
			?>
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&searchExceedTat=1<?php echo $addTopUrlExtraLinkTestQ;?>" class="quick-link-item">EXCEEDED TAT - <?php echo $totalExceedTatOrders;?></a>
			<?php
				}
				if(!empty($totalUncheckedOrders))
				{
			?>
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&searchUnchecked=1<?php echo $addTopUrlExtraLinkTestQ;?>" class="quick-link-item">UNCHECKED ORDERS - <?php echo $totalUncheckedOrders;?></a>
			<?php
				}
				if(!empty($s_hasManagerAccess)){
					$totalIncompltedOrders		=	$orderObj->getAllTotalIncompltedOrders();
					if(!empty($totalIncompltedOrders)){
			?>
			<a href="<?php echo SITE_URL_EMPLOYEES;?>/old-incompletd-orders.php" class="quick-link-item">48 Hrs OLD INCOMPLTED ORDERS - <?php echo $totalIncompltedOrders;?></a>
			<?php
					}
				}
			?>
			</div>
		</div>
		<div class="employee-details-col employee-details-col-messages">
			<div class="messages-card">
				<h3>GENERAL & IMPORTANT MESSAGES FOR YOU</h3>
				<?php

					$isMessages			=	0;
					$query	=	"SELECT * FROM employee_messages WHERE displayFrom <= '$nowDateIndia' AND '$nowDateIndia' <= displayTo AND employeeId=0 AND departmentId=0 ORDER BY displayFrom DESC";
					$result			=	dbQuery($query);
					if(mysqli_num_rows($result))
					{
						$isMessages			=	1;
						while($row			=	mysqli_fetch_assoc($result))
						{
							$title			=	$row['title'];
							$message		=	$row['message'];
							$addedOn		=	showDate($row['addedOn']);
							
							$title			=	stripslashes($title);
							$message		=	stripslashes($message);
							$message		=	nl2br($message);
							$addedByName	=	stripslashes($row['addedByName']);

							if(empty($addedByName))
							{
								$addedByName=	"Rishi Jindal";
							}
					?>
					<div class="message-item">
						<div class="message-title"><?php echo $title;?></div>
						<div class="message-content"><?php echo $message;?></div>
						<div class="message-meta">FROM : <?php echo $addedByName;?> On <?php echo $addedOn;?></div>
					</div>
					<div class="message-divider"></div>
					<?php
						}
					}
					$query				=	"SELECT * FROM employee_messages WHERE displayFrom <= '$nowDateIndia' AND '$nowDateIndia' <= displayTo AND employeeId=$s_employeeId AND departmentId=0 AND isDeleted=0 ORDER BY displayFrom DESC";
					$result			=	dbQuery($query);
					if(mysqli_num_rows($result))
					{
						$isMessages			=	1;

						while($row	=	mysqli_fetch_assoc($result))
						{
							$messageId			=	$row['messageId'];
							$title				=	$row['title'];
							$message			=	$row['message'];
							$addedOn			=	showDate($row['addedOn']);
							
							$title				=	stripslashes($title);
							$message			=	stripslashes($message);
							$message			=	nl2br($message);
							$isRead				=	$row['isRead'];
							$isReplied			=	$row['isReplied'];
							$readOn				=	$row['readOn'];

							$addedByName			=	stripslashes($row['addedByName']);

							if(empty($addedByName))
							{
								$addedByName=	"Rishi Jindal";
							}

					?>
					<div class="message-item <?php echo ($isRead == 0) ? 'unread' : ''; ?>">
						<div class="message-title"><?php echo $title;?></div>
						<div class="message-content"><?php echo $message;?></div>
						<div class="message-meta">
							Message On <?php 
								echo $addedOn;
								if($isRead == 1 && $readOn != "0000-00-00")
								{
									echo " | Read On ".showDate($readOn);
								}
							?>
						</div>
						<div class="message-actions">
							<?php 
								if($isRead == 0)
								{
									echo "<a onclick='readDeleteNotice($messageId,1)' class='message-action-btn primary' style='cursor:pointer;'>Mark As Read</a>";
								}
								else
								{
									echo "<a onclick='readDeleteNotice($messageId,2)' class='message-action-btn danger' style='cursor:pointer;'>Delete</a>";
								}
								if($isReplied	==	0)
								{
									echo "<a onclick='openWindow($messageId)' class='message-action-btn secondary' style='cursor:pointer;'>Reply To This Notice</a>";
								}
							?>
						</div>
					</div>
					<div class="message-divider"></div>
					<?php
						}

					}
					$query			=	"SELECT * FROM employee_messages WHERE displayFrom <= '$nowDateIndia' AND '$nowDateIndia' <= displayTo AND employeeId=$s_employeeId AND departmentId=3 AND isDeleted=0 ORDER BY displayFrom DESC";
					$result			=	dbQuery($query);
					if(mysqli_num_rows($result))
					{
						$isMessages			=	1;
						while($row		=	mysqli_fetch_assoc($result))
						{
							$messageId	=	$row['messageId'];
							$title		=	$row['title'];
							$message	=	$row['message'];
							$addedOn	=	showDate($row['addedOn']);
							
							$title		=	stripslashes($title);
							$message	=	stripslashes($message);
							$message	=	nl2br($message);
							$isRead		=	$row['isRead'];
							$isReplied	=	$row['isReplied'];
							$readOn		=	$row['readOn'];
				?>

				<div class="message-item <?php echo ($isRead == 0) ? 'unread' : ''; ?>">
					<div class="message-title"><?php echo $title;?></div>
					<div class="message-content"><?php echo $message;?></div>
					<div class="message-meta">
						Message On <?php 
							echo $addedOn;
							if($isRead == 1 && $readOn != "0000-00-00")
							{
								echo " | Read On ".showDate($readOn);
							}
						?>
					</div>
					<div class="message-actions">
						<?php 
							if($isRead == 0)
							{
								echo "<a onclick='readDeleteNotice($messageId,1)' class='message-action-btn primary' style='cursor:pointer;'>Mark As Read</a>";
							}
							else
							{
								echo "<a onclick='readDeleteNotice($messageId,2)' class='message-action-btn danger' style='cursor:pointer;'>Delete</a>";
							}
							if($isReplied	==	0)
							{
								echo "<a onclick='openWindow($messageId)' class='message-action-btn secondary' style='cursor:pointer;'>Reply To This Notice</a>";
							}
						?>
					</div>
				</div>
				<div class="message-divider"></div>
				<?php
						}

					}
					$query	=	"SELECT * FROM employee_messages WHERE displayFrom <= '$nowDateIndia' AND '$nowDateIndia' <= displayTo AND employeeId=0 AND departmentId=3 ORDER BY displayFrom DESC";
					$result	=	dbQuery($query);
					if(mysqli_num_rows($result))
					{
						$isMessages			=	1;
						while($row	=	mysqli_fetch_assoc($result))
						{
							$title			=	$row['title'];
							$message		=	$row['message'];
							$addedOn		=	showDate($row['addedOn']);
							
							$title			=	stripslashes($title);
							$message		=	stripslashes($message);
							$message		=	nl2br($message);

							$addedByName	=	stripslashes($row['addedByName']);

							if(empty($addedByName))
							{
								$addedByName=	"Rishi Jindal";
							}
					?>
					<div class="message-item">
						<div class="message-title"><?php echo $title;?></div>
						<div class="message-content"><?php echo $message;?></div>
						<div class="message-meta"><b>FROM : <?php echo $addedByName;?> On <?php echo $addedOn;?></b></div>
					</div>
					<div class="message-divider"></div>
					<?php

						}
					}
					if(empty($isMessages)){
				?>
				<div class="no-messages"><b>No Messages for now</b></div>
				<?php
					}
				?>
			</div>
		</div>
		<div class="employee-details-col employee-details-col-ratings">
			<div class="ratings-card">
				<h3>LAST 100 RATINGS</h3>
				<?php
					include(SITE_ROOT		    .   "/classes/paging-class-limit.php");
					$pagingObj					=   new Paging();
					if(isset($_REQUEST['recNo']))
					{
						$recNo					=	(int)$_REQUEST['recNo'];
					}
					if(empty($recNo))
					{
						$recNo					=	0;
					}

					$a_existingCustomerRatings	=	array("1"=>"Poor","2"=>"Average","3"=>"Good","4"=>"very Good","5"=>"Excellent");

				
					$whereClause				=	"WHERE members_orders.orderId > ".MAX_SEARCH_EMPLOYEE_ORDER_ID." AND members_orders.isVirtualDeleted=0 AND isNotVerfidedEmailOrder=0 AND rateGiven <> 0";
					$queryString			    =	"";
					$orderBy                    =   "rateGivenOn DESC, rateGivenTime DESC";

					$start					  =	0;
					$recsPerPage	          =	10;	//	how many records per page
					$showPages		          =	3;	
					$pagingObj->recordNo	  =	$recNo;
					$pagingObj->startRow	  = $recNo;
					$pagingObj->whereClause   =	$whereClause;
					$pagingObj->recsPerPage   =	$recsPerPage;
					$pagingObj->showPages	  =	$showPages;
					$pagingObj->orderBy		  =	$orderBy;
					$pagingObj->table		  =	"members_orders";
					$pagingObj->selectColumns = "orderId,memberId,rateGivenOn,rateGiven,acceeptedByName,orderAddress,memberRateMsg,acceptedBy";
					$pagingObj->path		  = SITE_URL_EMPLOYEES."/employee-details.php";
					$totalRecords = $pagingObj->getTotalRecords();
					if($totalRecords && $recNo <= $totalRecords)
					{	$pagingObj->setPageNo();
						$recordSet = $pagingObj->getRecords();
						while($row			  =   mysqli_fetch_assoc($recordSet))
						{
							$orderId		  =	$row['orderId'];
							$rateGivenOn      = $row['rateGivenOn'];
							$rateGiven        = $row['rateGiven'];
							$customerId		  =	$row['memberId'];
							$orderAddress     =	stripslashes($row['orderAddress']);
							$memberRateMsg    =	stripslashes($row['memberRateMsg']);
							$acceeptedByName  =	stripslashes($row['acceeptedByName']);
							$acceptedBy       =	stripslashes($row['acceptedBy']);
							if(!empty($memberRateMsg) && $memberRateMsg != "."){
								$memberRateMsg= ",".substr($memberRateMsg,0,20);
							}
							else{
								$memberRateMsg=	"";
							}

							$ratingText		  =  $a_existingCustomerRatings[$rateGiven];
				?>
				<div class="rating-item">
					<img src="<?php echo SITE_URL;?>/images/rating/<?php echo $rateGiven;?>.png" title="Rated - <?php echo $ratingText;?>" alt="Rating <?php echo $rateGiven;?>">
					<div class="rating-content">
						<?php if(!empty($memberRateMsg)) { ?>
							<span class="rating-message"><?php echo $memberRateMsg;?></span>
						<?php } ?>
						<a href="<?php echo SITE_URL_EMPLOYEES?>/new-pdf-work.php?isSubmittedForm=1&orderOf=<?php echo $acceptedBy;?>&showingEmployeeOrder=1" class="rating-employee-link"><?php echo substr($acceeptedByName,0,10);?></a>
						<span class="rating-date"><?php echo showDate($rateGivenOn)?></span>
					</div>
				</div>
				<?php
						}
					}
					echo "<div style='text-align:right; margin-top:15px;'>";
					$pagingObj->displayPaging($queryString);
					echo "</div>";	
				?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/new-ai-bottom.php");
?>