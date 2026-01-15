<?php
	ob_start();
	session_start();
	ini_set('display_errors', '1');
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/new-top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				= new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	$orderObj					=  new orders();
	include(SITE_ROOT			.   "/classes/pagingclass.php");
	$pagingObj					=  new Paging();

	 
	
	if(isset($_REQUEST['recNo']))
	{
		$recNo					=	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo					=	0;
	}

	$currentMonth				=  date("m");
	$currentYear				=  date("Y");
	$searchingMonthYear			=  $currentMonth."-".$currentYear;

	$currentDateMonth 			=	date('Y-m-d');
	$lastDateMonth              =   date('Y-m', strtotime("-1 month", strtotime($currentDateMonth)));
	list($lastYear,$lastMonth) =  explode("-",$lastDateMonth);
	$lastToLastDateMonth        =   date('Y-m', strtotime("-2 month", strtotime($currentDateMonth)));
	list($lastToLYear,$lastToLMonth) =  explode("-",$lastToLastDateMonth);

	$a_displayMonthYear         =   array();
	$a_displayMonthYear[$currentMonth."-".$currentYear]       =   $a_month[$currentMonth].",".$currentYear;
	$a_displayMonthYear[$lastMonth."-".$lastYear]          =   $a_month[$lastMonth].",".$lastYear;
	$a_displayMonthYear[$lastToLMonth."-".$lastToLYear]       =   $a_month[$lastToLMonth].",".$lastToLYear;

	if(isset($_GET['searchingMonthYear'])){
		$searchingMonthYear 	    =	trim($_GET['searchingMonthYear']);
		if(!empty($searchingMonthYear) && !array_key_exists($searchingMonthYear,$a_displayMonthYear)){
			ob_clean();
			header("Location:".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php");
		    exit();
		}

	}

	list($checkingForMonth,$checkingForYear) = 	explode("-",$searchingMonthYear);

	$headingText			        =	$a_displayMonthYear[$searchingMonthYear];
	$queryString					=	"&searchingMonthYear=".$searchingMonthYear;
	$pageUrl						=	"";
	$orderBy						=	"processedDone DESC";
	$lastMonthLinkUrl			    =	"&searchingMonthYear=".$searchingMonthYear;
		
   
 	$text							=	"EMPLOYEES TOTAL COMPLETED ORDERS AND RATINGS FOR - ".$headingText;

	$nonLeadingZeroMonth		    =	$checkingForMonth;
	if($checkingForMonth < 10 && strlen($checkingForMonth) > 1)
	{
		$nonLeadingZeroMonth	    =	substr($checkingForMonth,1);
	}

	$whereClause					=	"WHERE targetMonth=".$nonLeadingZeroMonth." AND targetYear=".$checkingForYear." AND isActive=1";
	$andClause						=	"";

	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$employeeOrderBy				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscEmp=1".$lastMonthLinkUrl."' class='linkstyle27'><b>Employee Name</b></a>";

	if(isset($_GET['isAscEmp']) && $_GET['isAscEmp'] == 1)
	{
		$employeeOrderBy			=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isDscEmp=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'><b>Employee Name</b></font></a>";
		$queryString				=	"&isAscEmp=1".$lastMonthLinkUrl;
		$orderBy					=	"employeeName";
	}
	if(isset($_GET['isDscEmp']) && $_GET['isDscEmp'] == 1)
	{
		$employeeOrderBy			=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscEmp=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'><b>Employee Name</b></font></a>";
		$queryString				=	"&isDscEmp=1".$lastMonthLinkUrl;
		$orderBy					=	"employeeName DESC";
	}

	$completedOrderBy				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscCom=1".$lastMonthLinkUrl."' class='linkstyle27'><b>Completed Orders</b></a>";

	if(isset($_GET['isAscCom']) && $_GET['isAscCom'] == 1)
	{
		$completedOrderBy			=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isDscCom=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'><b>Completed Orders</b></font></a>";
		$queryString				=	"&isAscCom=1".$lastMonthLinkUrl;
		$orderBy					=	"processedDone";
	}
	if(isset($_GET['isDscCom']) && $_GET['isDscCom'] == 1)
	{
		$completedOrderBy			=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscCom=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'><b>Completed Orders</b></font></a>";
		$queryString				=	"&isDscCom=1".$lastMonthLinkUrl;
		$orderBy					=	"processedDone DESC";
	}


	$processPoorRating				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscRatPor=1".$lastMonthLinkUrl."' class='linkstyle27'>Awful</a>";

	if(isset($_GET['isAscRatPor']) && $_GET['isAscRatPor'] == 1)
	{
		$processPoorRating				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isDscRatPor=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'>Awful</font></a>";
		$queryString				=	"&isAscRatPor=1".$lastMonthLinkUrl;
		$orderBy					=	"poorRating";
	}

	if(isset($_GET['isDscRatPor']) && $_GET['isDscRatPor'] == 1)
	{
		$processPoorRating				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscRatPor=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'>Awful</font></a>";
		$queryString				=	"&isDscRatPor=1".$lastMonthLinkUrl;
		$orderBy					=	"poorRating DESC";
	}

	$processAverageRating			=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscRatAve=1".$lastMonthLinkUrl."' class='linkstyle27'>Poor</a>";

	if(isset($_GET['isAscRatAve']) && $_GET['isAscRatAve'] == 1)
	{
		$processAverageRating		=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isDscRatAve=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'>Poor</font></a>";
		$queryString				=	"&isAscRatAve=1".$lastMonthLinkUrl;
		$orderBy					=	"averageRating";
	}

	if(isset($_GET['isDscRatAve']) && $_GET['isDscRatAve'] == 1)
	{
		$processAverageRating		=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscRatAve=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'>Poor</font></a>";
		$queryString				=	"&isDscRatAve=1".$lastMonthLinkUrl;
		$orderBy					=	"averageRating DESC";
	}

	$processGoodRating				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscRatGod=1".$lastMonthLinkUrl."' class='linkstyle27'>Fair</a>";

	if(isset($_GET['isAscRatGod']) && $_GET['isAscRatGod'] == 1)
	{
		$processGoodRating				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isDscRatGod=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'>Fair</font></a>";

		$queryString				=	"&isAscRatGod=1".$lastMonthLinkUrl;
		$orderBy					=	"goodRating";
	}

	if(isset($_GET['isDscRatGod']) && $_GET['isDscRatGod'] == 1)
	{
		$processGoodRating				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscRatGod=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'>Fair</font></a>";

		$queryString				=	"&isDscRatGod=1".$lastMonthLinkUrl;
		$orderBy					=	"goodRating DESC";
	}

	$processVgoodRating				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscRatVGod=1".$lastMonthLinkUrl."' class='linkstyle27'>Good</a>";

	if(isset($_GET['isAscRatVGod']) && $_GET['isAscRatVGod'] == 1)
	{
		$processVgoodRating				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isDscRatVGod=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'>Good</font></a>";

		$queryString				=	"&isAscRatVGod=1".$lastMonthLinkUrl;
		$orderBy					=	"veryGoodRating";
	}

	if(isset($_GET['isDscRatVGod']) && $_GET['isDscRatVGod'] == 1)
	{
		$processVgoodRating				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscRatVGod=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'>Good</font></a>";

		$queryString				=	"&isDscRatVGod=1".$lastMonthLinkUrl;
		$orderBy					=	"veryGoodRating DESC";
	}

	$processExeRating				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscRatExe=1".$lastMonthLinkUrl."' class='linkstyle27'>Excellent</a>";

	if(isset($_GET['isAscRatExe']) && $_GET['isAscRatExe'] == 1)
	{
		$processExeRating			=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isDscRatExe=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'>Excellent</font></a>";

		$queryString				=	"&isAscRatExe=1".$lastMonthLinkUrl;
		$orderBy					=	"excellentRating";
	}

	if(isset($_GET['isDscRatExe']) && $_GET['isDscRatExe'] == 1)
	{
		$processExeRating			=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscRatExe=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'>Excellent</font></a>";

		$queryString				=	"&isDscRatExe=1".$lastMonthLinkUrl;
		$orderBy					=	"excellentRating DESC";
	}

	$qaDoneOrderBy					=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscQa=1".$lastMonthLinkUrl."' class='linkstyle27'><b>QA Done</b></a>";

	if(isset($_GET['isAscQa']) && $_GET['isAscQa'] == 1)
	{
		$qaDoneOrderBy					=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isDscQa=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'><b>QA Done</b></font></a>";
		$queryString				=	"&isAscQa=1".$lastMonthLinkUrl;
		$orderBy					=	"qaDone";
	}

	if(isset($_GET['isDscQa']) && $_GET['isDscQa'] == 1)
	{
		$qaDoneOrderBy					=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscQa=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'><b>QA Done</b></font></a>";
		$queryString				=	"&isDscQa=1".$lastMonthLinkUrl;
		$orderBy					=	"qaDone DESC";
	}

	$qaPoorRating					=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscQaRatPor=1".$lastMonthLinkUrl."' class='linkstyle27'>Awful</a>";

	if(isset($_GET['isAscQaRatPor']) && $_GET['isAscQaRatPor'] == 1)
	{
		$qaPoorRating				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isDscQaRatPor=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'>Awful</font></a>";
		$queryString				=	"&isAscQaRatPor=1".$lastMonthLinkUrl;
		$orderBy					=	"qaPoorRating";
	}
	
	if(isset($_GET['isDscQaRatPor']) && $_GET['isDscQaRatPor'] == 1)
	{
		$qaPoorRating				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscQaRatPor=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'>Awful</font></a>";
		$queryString				=	"&isDscQaRatPor=1".$lastMonthLinkUrl;
		$orderBy					=	"qaPoorRating DESC";
	}

	$qaAverageRating				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscQaRatAve=1".$lastMonthLinkUrl."' class='linkstyle27'>Poor</a>";

	if(isset($_GET['isAscQaRatAve']) && $_GET['isAscQaRatAve'] == 1)
	{
		$qaAverageRating				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isDscQaRatAve=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'>Poor</font></a>";
		$queryString				=	"&isAscQaRatAve=1".$lastMonthLinkUrl;
		$orderBy					=	"qaAverageRating";
	}

	if(isset($_GET['isDscQaRatAve']) && $_GET['isDscQaRatAve'] == 1)
	{
		$qaAverageRating				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscQaRatAve=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'>Poor</font></a>";
		$queryString				=	"&isDscQaRatAve=1".$lastMonthLinkUrl;
		$orderBy					=	"qaAverageRating DESC";
	}

	$qaGoodRating					=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscQaRatGod=1".$lastMonthLinkUrl."' class='linkstyle27'>Fair</a>";

	if(isset($_GET['isAscQaRatGod']) && $_GET['isAscQaRatGod'] == 1)
	{
		$qaGoodRating				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isDscQaRatGod=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'>Fair</font></a>";
		$queryString				=	"&isAscQaRatGod=1".$lastMonthLinkUrl;
		$orderBy					=	"qaGoodRating";
	}

	if(isset($_GET['isDscQaRatGod']) && $_GET['isDscQaRatGod'] == 1)
	{
		$qaGoodRating				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscQaRatGod=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'>Fair</font></a>";
		$queryString				=	"&isDscQaRatGod=1".$lastMonthLinkUrl;
		$orderBy					=	"qaGoodRating DESC";
	}

	$qaVgoodRating					=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscQaRatVGod=1".$lastMonthLinkUrl."' class='linkstyle27'>Good</a>";

	if(isset($_GET['isAscQaRatVGod']) && $_GET['isAscQaRatVGod'] == 1)
	{
		$qaVgoodRating				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isDscQaRatVGod=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'>Good</font></a>";
		$queryString				=	"&isAscQaRatVGod=1".$lastMonthLinkUrl;
		$orderBy					=	"qaVeryGoodRating";
	}

	if(isset($_GET['isDscQaRatVGod']) && $_GET['isDscQaRatVGod'] == 1)
	{
		$qaVgoodRating				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscQaRatVGod=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'>Good</font></a>";
		$queryString				=	"&isDscQaRatVGod=1".$lastMonthLinkUrl;
		$orderBy					=	"qaVeryGoodRating DESC";
	}

	$qaExeRating					=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscQaRatExe=1".$lastMonthLinkUrl."' class='linkstyle27'>Excellent</a>";

	if(isset($_GET['isAscQaRatExe']) && $_GET['isAscQaRatExe'] == 1)
	{
		$qaExeRating				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isDscQaRatExe=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'>Excellent</font></a>";

		$queryString				=	"&isAscQaRatExe=1".$lastMonthLinkUrl;
		$orderBy					=	"qaExcellentRating";
	}

	if(isset($_GET['isDscQaRatExe']) && $_GET['isDscQaRatExe'] == 1)
	{
		$qaExeRating				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscQaRatExe=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'>Excellent</font></a>";

		$queryString				=	"&isDscQaRatExe=1".$lastMonthLinkUrl;
		$orderBy					=	"qaExcellentRating DESC";
	}

	$checkOrderBy					=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscChk=1".$lastMonthLinkUrl."' class='linkstyle27'><b>Files Checked</b></a>";

	if(isset($_GET['isAscChk']) && $_GET['isAscChk'] == 1)
	{
		$checkOrderBy				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isDscChk=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'><b>Files Checked</b></font></a>";
		$queryString				=	"&isAscChk=1".$lastMonthLinkUrl;
		$orderBy					=	"totalCheckedOrders";
	}

	if(isset($_GET['isDscChk']) && $_GET['isDscChk'] == 1)
	{
		$checkOrderBy				=	"<a href='".SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php?isAscChk=1".$lastMonthLinkUrl."' class='linkstyle27'><font color='#ff0000;'><b>Files Checked</b></font></a>";
		$queryString				=	"&isDscChk=1".$lastMonthLinkUrl;
		$orderBy					=	"totalCheckedOrders DESC";
	}


	
?>
<script type="text/javascript">
	function pageRedirect(path)
	{
		window.location.href=path;
	}
</script>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
	<tr>
		<td height="5"></td>
	</tr>
	<tr>
		<td class="textstyle3" colspan="2">
			:: <?php echo $text;?> ::
		</td>
	</tr>
	<tr>
		<td height="5"></td>
	</tr>
	<tr>
		<td>

			<?php
				foreach($a_displayMonthYear as $k=>$v){
					$class = "link_style5";
					if($searchingMonthYear == $k){
						$class = "link_style4";
					}
			?>
			<b><font class="textstyle">VIEW DATA OF </font><a href="<?php echo SITE_URL_EMPLOYEES;?>/view-total-completed-order-ratings.php?searchingMonthYear=<?php echo $k;?>" class="<?php echo $class;?>"><?php echo $v;?></a></b>&nbsp;&nbsp;
			<?php

					
				}
			?>
		</td>
	</tr>
</table>
<?php
	$start					  =	0;
	$recsPerPage	          =	55;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"employee_target INNER JOIN employee_details ON employee_target.employeeId=employee_details.employeeId";
	$pagingObj->selectColumns = "employee_target.*,employee_details.fullName as employeeCompleteName";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/view-total-completed-order-ratings.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
		$i					  =	$recNo;
?>
<br>
<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
	<tr bgcolor="#373737" height="30">
		<td width="16%" class="smalltext12" valign="top">&nbsp;<?php echo $employeeOrderBy;?></td>
		<td width="12%" class="smalltext12" valign="top"><?php echo $completedOrderBy;?></td>
		<td width="25%" align="center" valign="top">
			<table cellpadding="0" cellspacing="0" width='100%'align="center" border='0'>
				<tr>
					<td colspan="5" style="text-align:center;" class="smalltext12"><b>Customer Ratings</b></td>
				</tr>
				<tr>
					<td width="15%" style="text-align:center;" class="smalltext12"><?php echo $processPoorRating;?></td>
					<td width="19%" style="text-align:center;" class="smalltext12"><?php echo $processAverageRating;?></td>
					<td width="15%" style="text-align:center;" class="smalltext12"><?php echo $processGoodRating;?></td>
					<td width="25%" style="text-align:center;" class="smalltext12"><?php echo $processVgoodRating;?></td>
					<td style="text-align:center;" class="smalltext12"><?php echo $processExeRating;?></td>
				</tr>
			</table>
		</td>
		<td width="6%" class="smalltext12" valign="top"><?php echo $qaDoneOrderBy;?></td>
		<td width="25%" align="center" valign="top">
			<table cellpadding="0" cellspacing="0" width='100%'align="center" border='0'>
				<tr>
					<td colspan="5" style="text-align:center;" class="smalltext12"><b>Customer Ratings</b></td>
				</tr>
				<tr>
					<td width="15%" style="text-align:center;" class="smalltext12"><?php echo $qaPoorRating;?></td>
					<td width="19%" style="text-align:center;" class="smalltext12"><?php echo $qaAverageRating;?></td>
					<td width="15%" style="text-align:center;" class="smalltext12"><?php echo $qaGoodRating;?></td>
					<td width="25%" style="text-align:center;" class="smalltext12"><?php echo $qaVgoodRating;?></td>
					<td style="text-align:center;" class="smalltext12"><?php echo $qaExeRating;?></td>
				</tr>
			</table>
		</td>
		<td width="6%" class="smalltext12" valign="top"><b>Sketchs</b></td>
		<td class="smalltext12" valign="top"><?php echo $checkOrderBy;?></td>
	</tr>
	<?php
		while($row							=   mysqli_fetch_assoc($recordSet))
		{
			$i++;
			$employeeId						=	$row['employeeId'];
			$employeeName					=	stripslashes($row['employeeCompleteName']);
			$processedDone					=	stripslashes($row['processedDone']);	
			$qaDone							=	stripslashes($row['qaDone']);
			$poorRating					    =	stripslashes($row['poorRating']);
			$averageRating					=	stripslashes($row['averageRating']);	
			$goodRating					    =	stripslashes($row['goodRating']);
			$veryGoodRating					=	stripslashes($row['veryGoodRating']);
			$excellentRating				=	stripslashes($row['excellentRating']);	
			$qaPoorRating					=	stripslashes($row['qaPoorRating']);	
			$qaAverageRating				=	stripslashes($row['qaAverageRating']);	
			$qaGoodRating					=	stripslashes($row['qaGoodRating']);
			$qaVeryGoodRating				=	stripslashes($row['qaVeryGoodRating']);	
			$qaExcellentRating				=	stripslashes($row['qaExcellentRating']);
			$totalCheckedOrders				=	stripslashes($row['totalCheckedOrders']);

			$totalSketchChecked				=	$employeeObj->getSingleQueryResult("SELECT COUNT(orderId) as total FROM members_orders WHERE orderId > ".MAX_SEARCH_MEMBER_ORDERID." AND providedSketch=1 AND sketchDoneBy=$employeeId AND MONTH(sketchDoneOn)=$checkingForMonth AND YEAR(sketchDoneOn)=$checkingForYear","total");
			if(empty($totalSketchChecked))
			{
				$totalSketchChecked			=	0;
			}

			$bgColor				=	"class='rwcolor1'";
			if($i%2==0)
			{
				$bgColor			=   "class='rwcolor2'";
			}
	?>
	<tr height="23" <?php echo $bgColor;?>>
		<td class="smalltext2"><b><?php echo $employeeName;?></td>
		<td class="textstyle1" style="text-align:center;">
			<b><?php echo $processedDone;?></b>
		</td>
		<td class="textstyle1">
			<table cellpadding="0" cellspacing="0" width='100%'align="center" border='0'>
				<tr>
					<td width="15%" style="text-align:center;" class="textstyle1"><?php echo $poorRating;?></td>
					<td width="19%" style="text-align:center;" class="textstyle1"><?php echo $averageRating;?></td>
					<td width="15%" style="text-align:center;" class="textstyle1"><?php echo $goodRating;?></td>
					<td width="25%" style="text-align:center;" class="textstyle1"><?php echo $veryGoodRating;?></td>
					<td style="text-align:center;" class="textstyle1"><?php echo $excellentRating;?></td>
				</tr>
			</table>
		</td>
		<td class="textstyle1" style="text-align:center;">
			<b><?php echo $qaDone;?></b>
		</td>
		<td class="textstyle1">
			<table cellpadding="0" cellspacing="0" width='100%'align="center" border='0'>
				<tr>
					<td width="15%" style="text-align:center;" class="textstyle1"><?php echo $qaPoorRating;?></td>
					<td width="19%" style="text-align:center;" class="textstyle1"><?php echo $qaAverageRating;?></td>
					<td width="15%" style="text-align:center;" class="textstyle1"><?php echo $qaGoodRating;?></td>
					<td width="25%" style="text-align:center;" class="textstyle1"><?php echo $qaVeryGoodRating;?></td>
					<td style="text-align:center;" class="textstyle1"><?php echo $qaExcellentRating;?></td>
				</tr>
			</table>
		</td>
		<td class="textstyle1" style="text-align:center;">
			<b><?php echo $totalSketchChecked;?></b>
		</td>
		<td class="textstyle1" style="text-align:center;">
			<b><?php echo $totalCheckedOrders;?></b>
		</td>
	</tr>
	<?php
		}
		echo "<tr><td height='10'></td></tr><tr><td align='right' colspan='12'>";
		$pagingObj->displayPaging($queryString);
		echo "&nbsp;&nbsp;</td></tr>";
	?>
</table>
<?php
	}
	else
	{
		echo "<br><br><center><font class='error1'><b>No Record Found !!</b></font></center>";
	}

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>