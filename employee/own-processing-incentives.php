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
	include(SITE_ROOT_EMPLOYEES	.  "/includes/check-pdf-login.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	$orderObj					=  new orders();
	$pagingObj					=  new Paging();
    
   	if(isset($_REQUEST['recNo']))
	{
		$recNo		    =	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo			=	0;
	}

	$displayDate		=	"none";
	$displayMonth		=	"";
	$checked			=	"";
	$checked1			=	"checked";

	$searchOn			=	date("d-m-Y");
	$t_searchOn			=	date("Y-m-d");
	$month				=	date("m");
	$year				=	date("Y");
	$employeeId			=	0;
	$searchEmployee		=	"";
	$searchBy			=	0;
	$sortingLink		=	"";
	
	$whereClause		=	"WHERE MONTH(incentiveDate)=$month AND YEAR(incentiveDate)= $year AND employees_incentives_order_calculation.employeeId=$s_employeeId";
	$andClause			=	"";
	$queryString		=	"&searchBy=2&month=".$month."&year=".$year;
	$orderBy			=	"incentiveDate";

	if(isset($_GET['searchBy']))
	{
		$searchBy				=	$_GET['searchBy'];

		if($searchBy			==	1)
		{
			$searchOn			=	$_GET['searchOn'];
			list($day,$month,$year)		=	explode("-",$searchOn);
			$t_searchOn			=	$year."-".$month."-".$day;

			$displayDate		=	"";
			$displayMonth		=	"none";
			$checked			=	"checked";
			$checked1			=	"";


			$whereClause		=	"WHERE incentiveDate='$t_searchOn' AND employees_incentives_order_calculation.employeeId=$s_employeeId";
			$queryString	    =	"&searchBy=1&searchOn=".$searchOn;
		}
		if(isset($_GET['month']) && isset($_GET['year']))
		{
			$month				=	$_GET['month'];
			$year				=	$_GET['year'];
			$whereClause		=	"WHERE MONTH(incentiveDate)=$month AND YEAR(incentiveDate)= $year AND employees_incentives_order_calculation.employeeId=$s_employeeId";
			$queryString		.=	"&month=".$month."&year=".$year;
		}
	}

	$serachByTotalIncentvAsc	=	0;
	$serachByTotalIncentvDesc	=	0;
	$totalIncentiveUpImg		=	"sort_up_green.png";
	$totalIncentiveDnImg		=	"sort_down_grey.png";
	$sortingLink				=	$queryString;
	if(isset($_GET['incentvAsc']))
	{
		$serachByTotalIncentvAsc	=	$_GET['incentvAsc'];
		if(!empty($serachByTotalIncentvAsc))
		{
			$orderBy					=	"totalIncentives";
			$totalIncentiveUpImg		=	"sort_up_green.png";
			$totalIncentiveDnImg		=	"sort_down_grey.png";
			$queryString			   .=	"&incentvAsc=1";
		}
	}
	elseif(isset($_GET['incentvDsc']))
	{
		$serachByTotalIncentvDesc	=	$_GET['incentvDsc'];
		if(!empty($serachByTotalIncentvDesc))
		{
			$orderBy					=	"totalIncentives DESC";
			$totalIncentiveUpImg		=	"sort_up_grey.png";
			$totalIncentiveDnImg		=	"sort_down_green.png";
			$queryString			   .=	"&incentvDsc=1";
		}
	}
?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
	<tr>
		<td height="5"></td>
	</tr>
	<tr>
		<td class="heading3">
			:: VIEW YOUR INCENTIVES ON ORDER RATINGS ::
		</td>
	</tr>
	<tr>
		<td colspan="8" height="5"></td>
	</tr>
</table>
<br>
<script type="text/javascript">
function showSearch(flag)
{
	if(flag == 1)
	{
		document.getElementById('displayDate').style.display = 'inline';
		document.getElementById('displayMonth').style.display = 'none';
	}
	else
	{
		document.getElementById('displayDate').style.display = 'none';
		document.getElementById('displayMonth').style.display = 'inline';
	}
}


function redirectViewPageTo(flag,url)
{
	window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/own-processing-incentives.php?"+flag+url;
}
</script>
<form name="searchIncentives" action="" method="GET">
	<table cellpadding="0" cellspacing="0" width='98%' align="center" border='0'>
		<tr>
			<td width="38%" class="heading3" valign="top">Search Employee's Incentives <input type="radio" name="searchBy" value="1" <?php echo $checked;?> onclick="return showSearch(1)">Date Or By <input type="radio" name="searchBy" value="2" <?php echo $checked1;?> onclick="return showSearch(2)">Month</td>
			<td width="15%" valign="top" class="heading3">
				<div  id="displayDate" style="display:<?php echo $displayDate;?>">
					DATE&nbsp;&nbsp;
					<input type="text" name="searchOn" value="<?php echo $searchOn;?>" class="textbox" id="atOn" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('atOn','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
				</div>
				<div  id="displayMonth" style="display:<?php echo $displayMonth;?>">
					<select name="month">
						<?php
							foreach($a_serachMonths as $key=>$value)
							{
								$select	  =	"";
								if($month == $key)
								{
									$select	  =	"selected";
								}

								echo "<option value='$key' $select>$value</option>";
							}
						?>
					</select>&nbsp;&nbsp;
					<select name="year">
						<?php
							$sYear	=	"2010";
							$eYear	=	date("Y")+1;
							for($i=$sYear;$i<=$eYear;$i++)
							{
								$select			=	"";
								if($year  == $i)
								{
									$select		=	"selected";
								}
								echo "<option value='$i' $select>$i</option>";
							}
						?>
					</select>
				</div>
			</td>
			<td valign="top">
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
		<tr>
			<td colspan="6" height="5"></td>
		</tr>
	</table>
</form>
<br>
<?php
	$start					  =	0;
	$recsPerPage	          =	31;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  =	$recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"employees_incentives_order_calculation INNER JOIN employee_details ON employees_incentives_order_calculation.employeeId=employee_details.employeeId";
	$pagingObj->selectColumns = "totalSingleDayIncentive as totalIncentives,oneStar as totalOneStar,twoStar as totalTwoStar,threeStar as totalThreeStar,fourStar as totalFourStar,fiveStar as totalFiveStar,fullName,employees_incentives_order_calculation.employeeId,incentiveDate";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/own-processing-incentives.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{
		$pagingObj->setPageNo();
		$recordSet			  = $pagingObj->getRecords();

		$i					  =	$recNo;

	?>
	<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
		<tr>
			<td class="heading3" width="9%"><b>Date</b></td>
			<td class="heading3" width="4%" style="text-align:center"><b>Awful</b></td>
			<td class="heading3" width="7%" style="text-align:center"><b>Poor</b></td>
			<td class="heading3" width="6%" style="text-align:center"><b>Fair</b></td>
			<td class="heading3" width="8%" style="text-align:center"><b>Good</b></td>
			<td class="heading3" style="text-align:center"><b>Excellent</b></td>
			<!--<td class="heading3" width="8%" style="text-align:center"><b>Incentives</b></td>
			<td width="10" valign="bottom">
				<img src="<?php echo SITE_URL;?>/images/<?php echo $totalIncentiveUpImg;?>" border="0" title="Total Incentives By Ascending Order" onclick="redirectViewPageTo('incentvAsc=1','<?php echo $sortingLink;?>')" height="20" width="20" style="cursor:pointer;">
			</td>
			<td>
				<img src="<?php echo SITE_URL;?>/images/<?php echo $totalIncentiveDnImg;?>" border="0" title="Total Incentives By Descending Order" onclick="redirectViewPageTo('incentvDsc=1','<?php echo $sortingLink;?>')" height="20" width="20" style="cursor:pointer;">
			</td>-->
		</tr>
		<tr>
			<td colspan="10">
				<hr size="1" width="100%" bgcolor="#bebebe;">
			</td>
		</tr>
		<?php
				while($row	=   mysql_fetch_assoc($recordSet))
				{
					$i++;
					$employeeId			=	$row['employeeId'];
					$fullName			=	stripslashes($row['fullName']);
					$totalIncentives	=	$row['totalIncentives'];
					$totalOneStar		=	$row['totalOneStar'];
					$totalTwoStar		=	$row['totalTwoStar'];
					$totalThreeStar		=	$row['totalThreeStar'];
					$totalFourStar		=	$row['totalFourStar'];
					$totalFiveStar		=	$row['totalFiveStar'];
					$incentiveDate		=	showDate($row['incentiveDate']);
				
		?>
		<tr>
			<td valign="top" width="smalltext2"><?php echo $incentiveDate;?></td>
			<td valign="top" width="smalltext2" style="text-align:center"><?php echo $totalOneStar;?></td>
			<td valign="top" width="smalltext2" style="text-align:center"><?php echo $totalTwoStar;?></td>
			<td valign="top" width="smalltext2" style="text-align:center"><?php echo $totalThreeStar;?></td>
			<td valign="top" width="smalltext2" style="text-align:center"><?php echo $totalFourStar;?></td>
			<td valign="top" width="smalltext2" style="text-align:center"><?php echo $totalFiveStar;?></td>
			<!--<td valign="top" width="smalltext2" colspan="3">&nbsp;&nbsp;<?php echo $totalIncentives;?></td>-->
		</tr>
		<tr>
			<td colspan="10">
				<hr size="1" width="100%" bgcolor="#bebebe;">
			</td>
		</tr>
		<?php
				}
			echo "<tr><td colspan='10' align='center'>";
			$pagingObj->displayPaging($queryString);
			echo "&nbsp;&nbsp;</td></tr>";
		?>
	</table>
	<?php
	}
	else
	{
		echo "<br><br><center><font class='error'><b>No record found !!</b></font></center>";
	}

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>