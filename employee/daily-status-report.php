<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	ini_set('display_errors', 1);
	include(SITE_ROOT_EMPLOYEES		.   "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES		.   "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/common-array.php");
	//include(SITE_ROOT				.   "/classes/pagingclass.php");
	include(SITE_ROOT			    .   "/classes/email-templates.php");
	$employeeObj					=   new employee();
	//$pagingObj					    =   new Paging();
	$emailObj						=	new emails();
	include(SITE_ROOT_EMPLOYEES		.   "/includes/set-variables.php");
	$a_existingCustomerRatings	    =	array("1"=>"Poor","2"=>"Average","3"=>"Good","4"=>"very Good","5"=>"Excellent");


	$showForm						=	true;
	$statusReport					=	"";
	$errorMsg						=	"";
	$reportDate                     =   $nowDateIndia;
	$backDatedCol					=	"";
	$yesterdayDate					=	date('Y-m-d', strtotime("-1 day", strtotime($nowDateIndia)));
	if(isset($_GET['type']) && $_GET['type'] == "Y"){
		$reportDate                 =   $yesterdayDate;
		$backDatedCol               =   ",backDatedEntry=1";
	}

	$isExistsDailyWork				=	$employeeObj->getSingleQueryResult("SELECT statusId FROM employee_daily_status_report WHERE employeeId=$s_employeeId AND reportDate='".$reportDate."'","statusId");
	if(!empty($isExistsDailyWork)){
		$showForm					=	false;
	}

	if(isset($_REQUEST['formSubmitted'])){
		extract($_REQUEST);

		$statusReport	=	trim($statusReport);
		$t_statusReport	=	makeDBSafe($statusReport);

		if(empty($statusReport)){
			$errorMsg   =	"Please enter status report.";
		}
		else{
			$t_isExistsDailyWork =	$employeeObj->getSingleQueryResult("SELECT statusId FROM employee_daily_status_report WHERE employeeId=$s_employeeId AND reportDate='".$reportDate."'","statusId");

			if(!empty($isExistsDailyWork)){
				$errorMsg   =	"You have already added status report for the day.";
			}
			else{
				dbQuery("INSERT INTO employee_daily_status_report SET employeeId=$s_employeeId,reportDate='".$reportDate."',reportTime='".CURRENT_TIME_INDIA."',statusReport='$t_statusReport',ipAddress='".VISITOR_IP_ADDRESS."'".$backDatedCol);

				/////////////// SENDING EMAIL TO MANAGER ///////////////////////
				$mailSubject		=	$s_employeeName." daily status report - ".showDate($reportDate);
				$statusReport		=   nl2br($statusReport);
				$uniqueTemplateName	=	"TEMPLATE_SENDING_EMPLOYEE_STATUS_REPORT";
				$a_templateSubject	=	array("{emailSubject}"=>$mailSubject);

				 $a_templateData=	array("{emailFromName}"=>$s_employeeName,"{reportDate}"=>showDate($nowDateIndia),"{report}"=>$statusReport);

				if(!empty($employeeUnderManager)){
					////////////////// SENDING EMAIL BLOCK ///////////////
					$query	=	"SELECT fullName,email FROM employee_details WHERE employeeId=$employeeUnderManager AND isActive=1";
					$result	=	dbQuery($query);
					if(mysqli_num_rows($result)){
						$row	=	mysqli_fetch_assoc($result);
						$toEmail=	$row['email'];
						$toName	=	$row['fullName'];

						$setThisEmailReplyToo			=	$email;
                        $setThisEmailReplyTooName		=	$s_employeeName;

                        //include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");

					}
				}
				elseif($isManger == 1){
				
					$toEmail						=	"hemant@ieimpact.net";
					$setThisEmailReplyToo			=	$email;
                    $setThisEmailReplyTooName		=	$s_employeeName;

                    //include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
				}

				$_SESSION['successStatus']		=	1;

				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES ."/daily-status-report.php");
				exit();
			}
		}
	}

?>
<script type="text/javascript">
	function checkValidWork()
	{
		form1	=	document.addStatusWork;
		if(form1.statusReport.value == "" || form1.statusReport.value == " " || form1.statusReport.value == "0"){
			alert("Please enter status report.");
			form1.statusReport.focus();
			return false;
		}
	}
</script>
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='textstyle3' valign="top" colspan="3"><b>DAILY WORK STATUS REPORT</b></td>
	</tr>
</table>
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<td width="40%" valign="top">
		<?php
			if($showForm == true){
		?>
	<form  name='addStatusWork' method='POST' action="" onsubmit="return checkValidWork();">
	<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
		<tr>
			<td class='textstyle2' valign="top" colspan="3"><b>ADD YOUR TODAY STATUS REPORT</b></td>
		</tr>
		<?php
			if(!empty($errorMsg)){
		?>
		<tr>
			<td class='error' valign="top" colspan="3"><b><?php echo $errorMsg;?></b></td>
		</tr>
		<?php
			}
		?>
		<tr>
			<td colspan="3">
				<textarea name="statusReport" style="height:100px;width:500px;border:1px solid #bebebe;font-family:verdana;color:#000000;font-size:12px;"><?php echo $statusReport;?></textarea>
			</td>
		</tr>
		<tr>
			<td class='smalltext1' valign="top" colspan="3">[<u>Note</u><font color="#ff0000;">*</font>:You can add your status once in a day, once submitted cannot revert or deleted. ]</td>
		</tr>
		<tr>
			<td colspan="3">
				<input type='image' name='submit' src='<?php echo SITE_URL;?>/images/submit.jpg'>
				<input type='hidden' value='1' name='formSubmitted'>
			</td>
		</tr>
		<tr>
			<td colspan="3" height="20"></td>
		</tr>
	</table>
</form>
<?php
	}
	else{

		if(isset($_SESSION['successStatus'])){
?>
			<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
				<tr>
					<td class='textstyle2' valign="top" colspan="3" style="text-alugn:center;"><b>SUCCCESSFULLY ADDED TODAY STATUS REPORT</b></td>
				</tr>
				<tr>
					<td colspan="3" height="20"></td>
				</tr>
			</table>

<?php		unset($_SESSION['successStatus']);
		}
		else{
?>
			<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
				<tr>
					<td class='error2' align="center"><b>YOU HAVE ALREADY ADDED STATUS REPORT FOR THE DAY.</b></td>
				</tr>
				<tr>
					<td colspan="3" height="20"></td>
				</tr>
			</table>
<?php
		}
	}
?>
	</td>
	<td width="30%" valign="top">
		<!----------------- SHOWING CUSTOMER LAST 10 RATINGS FOR SPECIFIC EMPLOYEES -->
		<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
			<tr>
				<td colspan="8" class="textstyle1"><b>Your Customers' Ratings</b></td>
			</tr>
			<?php
				$query 	= "SELECT orderId,memberId,rateGivenOn,rateGiven,acceeptedByName,orderAddress,memberRateMsg,acceptedBy FROM members_orders WHERE members_orders.orderId > ".MAX_SEARCH_EMPLOYEE_ORDER_ID." AND members_orders.isVirtualDeleted=0 AND isNotVerfidedEmailOrder=0 AND rateGiven <> 0 AND acceptedBy=$s_employeeId ORDER BY rateGivenOn DESC, rateGivenTime DESC LIMIT 10";
				$result =	dbQuery($query);
				if(mysqli_num_rows($result)){
					while($row			  =   mysqli_fetch_assoc($result))
					{
						$orderId		  =	$row['orderId'];
						$rateGiven        = $row['rateGiven'];
						$rateGivenOn      = $row['rateGivenOn'];
						$customerId		  =	$row['memberId'];
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
				<tr>
					<td class="smalltext2">
						<img src="<?php echo SITE_URL;?>/images/rating/<?php echo $rateGiven;?>.png" title="Rated - <?php echo $ratingText;?>"><?php echo $memberRateMsg.", "?><a href="<?php echo SITE_URL_EMPLOYEES?>/new-pdf-work.php?isSubmittedForm=1&orderOf=<?php echo $acceptedBy;?>&showingEmployeeOrder=1"><?php echo substr($acceeptedByName,0,10);?></a>(<?php echo showDate($rateGivenOn)?>)
					</td>
				</tr>
				<tr>
					<td height="5"></td>
				</tr>
				<?php
						}
				}
				else{
			?>
			<tr>
				<td colspan="8" class="error"><b>No Record found</b></td>
			</tr>
			<?php
				
				}
			?>
		</table>
	</td>
	<td  valign="top">
		<!----------------- SHOWING CUSTOMER LAST 100 RATINGS FOR ALL EMPLOYEES -->
		<table width='100%' align='center' cellpadding='0' cellspacing='0' border='0'>
			<tr>
				<td colspan="8" class="textstyle1"><b>LAST 100 RATINGS</b></td>
			</tr>
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
				$pagingObj->path		  = SITE_URL_EMPLOYEES."/daily-status-report.php";
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
			<tr>
				<td class="smalltext2">
					<img src="<?php echo SITE_URL;?>/images/rating/<?php echo $rateGiven;?>.png" title="Rated - <?php echo $ratingText;?>"><?php echo $memberRateMsg.", "?><a href="<?php echo SITE_URL_EMPLOYEES?>/new-pdf-work.php?isSubmittedForm=1&orderOf=<?php echo $acceptedBy;?>&showingEmployeeOrder=1"><?php echo substr($acceeptedByName,0,10);?></a>(<?php echo showDate($rateGivenOn)?>)
				</td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<?php
					}
					echo "<tr><td height='7'></td></tr><tr><td align='right' colspan='15'>";
					$pagingObj->displayPaging($queryString);
					echo "&nbsp;&nbsp;</td></tr>";	
				}
				
			?>

		</table>	
	</td>
</table>
<?php
	///////////////////////////////////////////// SHOWING EXISTING REPORT /////////////////////
	/*$whereClause		=	"WHERE employeeId=$s_employeeId";
	$orderBy			=	"reportDate DESC";
	$queryString		=	"";
	if(isset($_REQUEST['recNo']))
	{
		$recNo		    =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo			=	0;
	}

	$start					  =	0;
	$recsPerPage	          =	50;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"employee_daily_status_report";
	$pagingObj->selectColumns = "*";
	$pagingObj->path		  = SITE_URL_EMPLOYEES. "/daily-status-report.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
?>
	<table width='99%' align='center' cellpadding='0' cellspacing='0' border='0'>
		<tr height='25' bgcolor="#373737">
			<td width='5%' class='smalltext12' valign="top">&nbsp;Sr.No</td>
			<td width='15%' class='smalltext12' valign="top">Date</td>
			<td class='smalltext12' valign="top">Report</td>
		</tr>
	<?php
		$i	=	$recNo;
		while($row	=   mysqli_fetch_assoc($recordSet))
		{
			$i++;
			$reportDate			=	showDate($row['reportDate']);
			$statusReport		=	$row['statusReport'];
	?>
	<tr>
		<td class="smalltext2" valign="top">&nbsp;<?php echo $i;?>)</td>
		<td class="smalltext2" valign="top"><?php echo $reportDate;?></td>
		<td class="smalltext2" valign="top"><?php echo nl2br($statusReport);?></td>
	</tr>
	<tr>
		<td colspan="3"><hr size="1" style="color:#bebebe;"></td>
	</tr>
	<?php
		}
		echo "<tr><td colspan='3' align='right'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr>";
	?>
	</table>
<?php
	}
	else
	{
		echo "<br><center><font class='error'><b>No Status Added Yet</b></font></center>";
	}*/

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>