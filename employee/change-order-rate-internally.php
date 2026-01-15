<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	ini_set('display_errors', '1');
	include(SITE_ROOT_EMPLOYEES .   "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES .   "/includes/check-login.php");
	include(SITE_ROOT_MEMBERS	.	"/classes/members.php");
	$memberObj					=	new members();

	$topDisplayDiv	=	"";
	if($s_employeeId && !isset($isNotDisplayLoadingDiv))
	{
		require_once (SITE_ROOT . '/classes/loading-div.php');
		$divLoader = new loadingDiv;
		$divLoader->loader($topDisplayDiv);
	}

	$a_existingCustomerRatings	=	array("1"=>"Poor","2"=>"Average","3"=>"Good","4"=>"very Good","5"=>"Excellent");
?>
<html>
<head>
<TITLE>Change Order Rating Internally</TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
<link rel="shortcut icon" href="/new-favicon.ico" type="image/x-icon" />
</head>
<body>
<script type="text/javascript">
	function reflectChange()
	{
		window.opener.location.reload();
	}
</script>
<center>
<?php
	
	include(SITE_ROOT			.  "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	.  "/includes/common-array.php");
	$showForm					=  false;
	$orderId					=  0;
	$customerId					=  0;
	$checkedReason				=  0;
	$errorMessageForm			=  "You are not authorized to view this page !!";
	$errorMsg					=  "";
	
	if(isset($_GET['orderId']) && isset($_GET['customerId']))
	{
		$orderId				=	$_GET['orderId'];
		$customerId				=	$memberId = $_GET['customerId'];
		
		$query					=	"SELECT completeName,totalOrdersPlaced,members_orders.* FROM members_orders INNER JOIN members ON  members_orders.memberId=members.memberId WHERE orderId=$orderId AND members_orders.memberId=$customerId AND isVirtualDeleted=0 AND isDeleted=0 AND members_orders.status IN (2,5,6)";
		$result			=	mysql_query($query);
		if(mysql_num_rows($result))
		{
			$showForm				=	true;
			$row					=	mysql_fetch_assoc($result);			
			$customerId				=	$memberId = $row['memberId'];
			$orderId				=	$row['orderId'];
			$completeName			=	stripslashes($row['completeName']);
			$orderAddress			=	stripslashes($row['orderAddress']);
			$orderType				=	$row['orderType'];
			$orderTypeText			=	$a_customerOrder[$orderType];
			$orderAddedOn			=	showDate($row['orderAddedOn']);
			$orderCompletedOn		=	showDate($row['orderCompletedOn']);
			$isHavingEstimatedTime	=	$row['isHavingEstimatedTime'];
			$employeeWarningDate	=	$row['employeeWarningDate'];
			$employeeWarningTime	=	$row['employeeWarningTime'];
			$totalOrdersPlaced		=	$row['totalOrdersPlaced'];
			$acceptedBy				=	$row['acceptedBy'];
			$acceptedByName			=	stripslashes($row['acceeptedByName']);
			$rateGiven			    =	$row['rateGiven'];
			$isRushOrder			=	$row['isRushOrder'];
			$qaDoneById				=	$row['qaDoneById'];
			
		}

	}
	else
	{
		$showForm					= false;
	}

	if(empty($s_hasManagerAccess))
	{
		$showForm					= false;
	}

	$a_allowChangeRatingInternalAccess	=	array('637','3','8','137','946');
	if(!in_array($s_employeeId,$a_allowChangeRatingInternalAccess)){
		$showForm					= false;
	}


	if($showForm)
	{
		if(isset($_REQUEST['changeRateFormSubmit'])){
			extract($_REQUEST);	
			
			if(!empty($chnageOrderRateInto)){
				dbQuery("INSERT INTO internal_order_rating SET orderId=$orderId,customerId=$customerId,orderProcessedBy=$acceptedBy,orderQaBy=$qaDoneById,rating=$chnageOrderRateInto,addedBy=$s_employeeId,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."'");

				////////////////// CALCULATING ORDERS RATING SCORE FOR EMPLOYEES //////////
				$a_existingRatingMoney	=	$memberObj->getEmployeeIncentiveMoney();

				$calculationId		=	@mysql_result(dbQuery("SELECT calculationId FROM employees_incentives_order_calculation WHERE employeeId=$acceptedBy AND  incentiveDate='$nowDateIndia'"),0);
				if(empty($calculationId))
				{
					$calculationId	=	0;
				}


				if($chnageOrderRateInto		==	1)
				{
					$perOneStarratingMoney	=	$a_existingRatingMoney[1];	
					if(empty($calculationId))
					{
						dbQuery("INSERT INTO employees_incentives_order_calculation SET oneStar=1,perOneStarratingMoney=$perOneStarratingMoney,totalOneStarMoney=$perOneStarratingMoney,totalSingleDayIncentive='$perOneStarratingMoney',employeeId=$acceptedBy,incentiveDate='$nowDateIndia'");
					}
					else
					{
						$totalOneStar	= @mysql_result(dbQuery("SELECT oneStar FROM employees_incentives_order_calculation WHERE employeeId=$acceptedBy AND  incentiveDate='$nowDateIndia' AND calculationId=$calculationId"),0);

						if(empty($totalOneStar))
						{
							$totalOneStar	=	1;
						}

						if($totalOneStar < 2)
						{
							dbQuery("UPDATE employees_incentives_order_calculation SET oneStar=oneStar+$totalOneStar,perOneStarratingMoney=$perOneStarratingMoney,totalOneStarMoney=totalOneStarMoney+($perOneStarratingMoney),totalSingleDayIncentive=totalSingleDayIncentive+($perOneStarratingMoney) WHERE employeeId=$acceptedBy AND  incentiveDate='$nowDateIndia' AND calculationId=$calculationId");
						}
					}
					
				}
				elseif($chnageOrderRateInto		==	2)
				{
					$perTwoStarratingMoney	=	$a_existingRatingMoney[2];	
					if(empty($calculationId))
					{
						dbQuery("INSERT INTO employees_incentives_order_calculation SET twoStar=1,perTwoStarratingMoney=$perTwoStarratingMoney,totalTwoStarMoney=$perTwoStarratingMoney,totalSingleDayIncentive='$perTwoStarratingMoney',employeeId=$acceptedBy,incentiveDate='$nowDateIndia'");
					}
					else
					{
						$totalTwoStar	= @mysql_result(dbQuery("SELECT twoStar FROM employees_incentives_order_calculation WHERE employeeId=$acceptedBy AND  incentiveDate='$nowDateIndia' AND calculationId=$calculationId"),0);

						if(empty($totalTwoStar))
						{
							$totalTwoStar	=	1;
						}

						if($totalTwoStar < 2)
						{
							dbQuery("UPDATE employees_incentives_order_calculation SET twoStar=twoStar+$totalTwoStar,perTwoStarratingMoney=$perTwoStarratingMoney,totalTwoStarMoney=totalTwoStarMoney+$perTwoStarratingMoney,totalSingleDayIncentive=totalSingleDayIncentive+$perTwoStarratingMoney WHERE employeeId=$acceptedBy AND  incentiveDate='$nowDateIndia' AND calculationId=$calculationId");
						}
					}
					
				}
				elseif($chnageOrderRateInto		==	3)
				{
					$perThreeStarratingMoney	=	$a_existingRatingMoney[3];	
					if(empty($calculationId))
					{
						dbQuery("INSERT INTO employees_incentives_order_calculation SET threeStar=1,perThreeStarratingMoney=$perThreeStarratingMoney,totalThreeStarMoney=$perThreeStarratingMoney,totalSingleDayIncentive='$perThreeStarratingMoney',employeeId=$acceptedBy,incentiveDate='$nowDateIndia'");
					}
					else
					{
						$totalThreeStar	= @mysql_result(dbQuery("SELECT threeStar FROM employees_incentives_order_calculation WHERE employeeId=$acceptedBy AND  incentiveDate='$nowDateIndia' AND calculationId=$calculationId"),0);

						if(empty($totalThreeStar))
						{
							$totalThreeStar	=	1;
						}

						if($totalThreeStar < 2)
						{
							dbQuery("UPDATE employees_incentives_order_calculation SET threeStar=threeStar+$totalThreeStar,perThreeStarratingMoney=$perThreeStarratingMoney,totalThreeStarMoney=totalThreeStarMoney+$perThreeStarratingMoney,totalSingleDayIncentive=totalSingleDayIncentive+$perThreeStarratingMoney WHERE employeeId=$acceptedBy AND  incentiveDate='$nowDateIndia' AND calculationId=$calculationId");
						}
					}
					
				}
				elseif($chnageOrderRateInto		==	4)
				{
					$perFourStarratingMoney	=	$a_existingRatingMoney[4];	
					if(empty($calculationId))
					{
						dbQuery("INSERT INTO employees_incentives_order_calculation SET fourStar=1,perFourStarratingMoney=$perFourStarratingMoney,totalFourStarMoney=$perFourStarratingMoney,totalSingleDayIncentive='$perFourStarratingMoney',employeeId=$acceptedBy,incentiveDate='$nowDateIndia'");
					}
					else
					{
						$totalFourStar	= @mysql_result(dbQuery("SELECT fourStar FROM employees_incentives_order_calculation WHERE employeeId=$acceptedBy AND  incentiveDate='$nowDateIndia' AND calculationId=$calculationId"),0);

						if(empty($totalFourStar))
						{
							$totalFourStar	=	1;
						}


						if($totalFourStar < 2)
						{
							dbQuery("UPDATE employees_incentives_order_calculation SET fourStar=fourStar+$totalFourStar,perFourStarratingMoney=$perFourStarratingMoney,totalFourStarMoney=totalFourStarMoney+$perFourStarratingMoney,totalSingleDayIncentive=totalSingleDayIncentive+$perFourStarratingMoney WHERE employeeId=$acceptedBy AND  incentiveDate='$nowDateIndia' AND calculationId=$calculationId");
						}
					}
					
				}
				elseif($chnageOrderRateInto		==	5)
				{
					$perFiveStarratingMoney	=	$a_existingRatingMoney[5];	
					if(empty($calculationId))
					{
						dbQuery("INSERT INTO employees_incentives_order_calculation SET fiveStar=1,perFiveStarratingMoney=$perFiveStarratingMoney,totalFiveStarMoney=$perFiveStarratingMoney,totalSingleDayIncentive='$perFiveStarratingMoney',employeeId=$acceptedBy,incentiveDate='$nowDateIndia'");
					}
					else
					{
						$totalFiveStar	= @mysql_result(dbQuery("SELECT fiveStar FROM employees_incentives_order_calculation WHERE employeeId=$acceptedBy AND  incentiveDate='$nowDateIndia' AND calculationId=$calculationId"),0);

						if(empty($totalFiveStar))
						{
							$totalFiveStar	=	1;
						}

						if($totalFiveStar < 2)
						{
							dbQuery("UPDATE employees_incentives_order_calculation SET fiveStar=fiveStar+$totalFiveStar,perFiveStarratingMoney=$perFiveStarratingMoney,totalFiveStarMoney=totalFiveStarMoney+$perFiveStarratingMoney,totalSingleDayIncentive=totalSingleDayIncentive+$perFiveStarratingMoney WHERE employeeId=$acceptedBy AND  incentiveDate='$nowDateIndia' AND calculationId=$calculationId");
						}
					}
					
				}
				echo "<table width='95%' align='center' border='0' height='70'><tr><td align='center'><font style='font-family:verdana;font-size:17px;color:#333333;'>Successfully updated Rating.</font></td></tr></table>";

				echo "<script type='text/javascript'>reflectChange();</script>";		
				echo "<script>setTimeout('window.close()',10)</script>";
			}
			
		}
?>
<table width="100%" align="center" border="0" cellspacing="3" cellspacing="3">
	<tr>
		<td colspan="3" class="textstyle1"><b>Change Order rating Internally</b></td>
	</tr>
	<tr>
		<td width="20%" class="smalltext22">
			Customer Name
		</td>
		<td width="2%" class="smalltext22">
			:
		</td>
		<td class="smalltext23">
			<?php echo $completeName;?>
		</td>
	</tr>
	<tr>
		<td class="smalltext22">
			Order Address
		</td>
		<td class="smalltext22">
			:
		</td>
		<td class="smalltext23">
			<?php echo $orderAddress;?>
		</td>
	</tr>	
	<tr>
		<td class="smalltext22">
			Order Added On
		</td>
		<td class="smalltext22">
			:
		</td>
		<td class="smalltext23">
			<?php echo $orderAddedOn;?>
		</td>
	</tr>
	<tr>
		<td class="smalltext22">
			Order Complted On
		</td>
		<td class="smalltext22">
			:
		</td>
		<td class="smalltext23">
			<?php echo $orderCompletedOn;?>
		</td>
	</tr>
	<tr>
		<td class="smalltext22">
			Processed By
		</td>
		<td class="smalltext22">
			:
		</td>
		<td class="smalltext23">
			<?php echo $acceptedByName;?>
		</td>
	</tr>	
</table>
<script type="text/javascript">
	function isValidRate(){
		form1	=	document.changeRateByEmp;
		if(form1.chnageOrderRateInto.value == ""){
			alert("Please change rate.");
			return false;
		}
	}
</script>
<form name="changeRateByEmp" action="" method="POST" onsubmit="return isValidRate();">
	<table width="100%" align="center" border="0" cellspacing="3" cellspacing="3">
		<?php
			if(!empty($errorMsg)){
				echo "<tr><td colspan='2'>&nbsp;</td><td class='error2'>".$errorMsg."</td></tr>";
			}
		?>
		<tr>
			<td width="20%" class="smalltext22">
				Change Rate Into
			</td>
			<td width="2%" class="smalltext22">
				:
			</td>
			<td class="smalltext23">
				<select name="chnageOrderRateInto">
					<option value="">Select</option>
				<?php
					foreach($a_existingCustomerRatings as $kk=>$vv){
						
						echo "<option value='$kk'>$vv</option>";
					}
				?>
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td>
				<input type="image" name="submit" src="<?php echo SITE_URL;?>/images/submit.jpg" border="0" style="cursor:pointer;">
				<input type='hidden' name='changeRateFormSubmit' value='1'>
			</td>
		</tr>
	</table>
</form>
<?php
	}
	else
	{
		echo "<table width='90%' align='center' border='1' height='100'><tr><td align='center' align='center' class='error'><b>$errorMessageForm</b></td></tr></table>";
	}	
?>
	<br><br>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
</center>
</body>
</html>

	