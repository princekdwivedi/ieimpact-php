<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				=	new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	include(SITE_ROOT		    . "/classes/pagingclass.php");
	$orderObj					=  new orders();
	$pagingObj					=  new Paging();
	
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	if(isset($_REQUEST['recNo']))
	{
		$recNo						=	(int)$_REQUEST['recNo'];
	}
	if(empty($recNo))
	{
		$recNo						=	0;
	}

	list($year,$t_month,$days)	=	explode("-",$nowDateIndia);

	$searchMonth		=	$t_month;
	$searchYear			=	$year;
	$searchingDate		=	$days."-".$t_month."-".$year;
	$searchName			=	"";

	if(isset($_GET['searchMonth']) && isset($_GET['searchYear'])){
		$searchMonth	=	$t_month    = $_GET['searchMonth'];
		$searchYear		=	$year = $_GET['searchYear'];
	}

	$month				=	$t_month;
	if($t_month < 10)
	{
		$month			=	substr($t_month,1);
	}

	$monthText			=	$a_month[$t_month];

	$displayMonth		=	"";
	$displayDate		=	"none";
	$check				=	"checked";
	$check1				=	"";

	$whereClause		=   "WHERE MONTH(reportDate)=$t_month AND YEAR(reportDate)=$year AND hasPdfAccess=1";
	$orderBy			=	"reportDate DESC";
	$andClause			=	"";	
	$queryString		=	"&searchMonth=".$searchMonth."&searchYear=".$searchYear;

	if(isset($_GET['displayBy'])){
		$displayBy			=	$_GET['displayBy'];
		$queryString       .=	"&displayBy=".$displayBy;
		if($displayBy		==  2){
			$displayMonth	=	"none";
			$displayDate	=	"";
			$check			=	"";
			$check1			=	"checked";
		}

	}

	

	
	if(isset($_GET['searchingDate']) && $displayBy == 2){
		$searchingDate	=	$_GET['searchingDate'];

		list($d,$m,$y)	=	explode("-",$searchingDate);
		$t_searchingDate=   $y."-".$m."-".$d;
		$whereClause	=   "WHERE (reportDate)='$t_searchingDate' AND hasPdfAccess=1";		
		$queryString	=	"&searchingDate=".$searchingDate;
		
	}

	if(isset($_GET['searchName'])){
		$searchName		=	trim($_GET['searchName']);
		if(!empty($searchName)){
			$searchName =   makeDBSafe($searchName);
			$andClause	=	" AND fullName LIKE '%".$searchName."%'";
			$queryString.=	"&searchName=".$searchName;
		}
	}

	


?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
</script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" />

<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>

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

$().ready(function() {
	$("#searchName1").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-employee.php", {width: 260,selectFirst: false});
});
</script>
<form name="serachRatingByMonth" action="" method="GET">
	<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td class="textstyle3" colspan="6">
				VIEW MONTHWISE EMPLOYEE STATUS REPORT
			</td>
		</tr>
		<tr>
			<td class="textstyle1" width="28%">
				Display Reports <input type="radio" name="displayBy" value="1" onclick="return showSearch(2)" <?php echo $check;?>>Monthwise or <input type="radio" name="displayBy" value="2" onclick="return showSearch(1)" <?php echo $check1;?>> Datewise
			</td>
			<td width="13%">
				<div  id="displayDate" style="display:<?php echo $displayDate;?>">
				&nbsp;&nbsp;
					<input type="text" name="searchingDate" value="<?php echo $searchingDate;?>" id="atOn" readonly size="10" style="border:1px solid #4d4d4d;height:25px;font-size:15px;">&nbsp;&nbsp;<a href="javascript:NewCssCal('atOn','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
				</div>
				<div  id="displayMonth" style="display:<?php echo $displayMonth;?>">
					<select name="searchMonth" style="border:1px solid #4d4d4d;height:25px;font-size:15px;">
						<?php
							foreach($a_serachMonths as $kk=>$vv){
								$select		    =	"";
								if($searchMonth ==	$kk){
									$select		=	"selected";
								}

								echo "<option value='$kk' $select>$vv</option>";
							}
						?>
					</select>--
					<select name="searchYear" style="border:1px solid #4d4d4d;height:25px;font-size:15px;" >
					<?php
						$fromYear	=	"2018";
						$toYear		=	date('Y');
						for($i=$fromYear;$i<=$toYear;$i++){
							$select		    =	"";
							if($searchYear  ==	$i){
								$select		=	"selected";
							}

							echo "<option value='$i' $select>$i</option>";
						}
					?>
					</select>
				</div>


				
			</td>
			<td class="textstyle1" width="11%">
				Employee Name :
			</td>
			<td class="textstyle1" width="22%">
				<input type='text' name="searchName" size="30" value="<?php echo $searchName;?>" id="searchName1" style="border:1px solid #4d4d4d;height:25px;font-size:15px;" tabindex=3>
			</td>			
			<td>
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
	</table>
</form>
<?php
	$start					  =	0;
	$recsPerPage	          =	20;	//	how many records per page
	$showPages		          =	10;	
	$pagingObj->recordNo	  =	$recNo;
	$pagingObj->startRow	  = $recNo;
	$pagingObj->whereClause   =	$whereClause.$andClause;
	$pagingObj->recsPerPage   =	$recsPerPage;
	$pagingObj->showPages	  =	$showPages;
	$pagingObj->orderBy		  =	$orderBy;
	$pagingObj->table		  =	"employee_daily_status_report INNER JOIN employee_details ON employee_daily_status_report.employeeId=employee_details.employeeId";
	$pagingObj->selectColumns = "employee_daily_status_report.*,fullName";
	$pagingObj->path		  = SITE_URL_EMPLOYEES."/employee-status-report.php";
	$totalRecords = $pagingObj->getTotalRecords();
	if($totalRecords && $recNo <= $totalRecords)
	{

		$pagingObj->setPageNo();
		$recordSet = $pagingObj->getRecords();
		$i					  =	$recNo;
?>
<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'> 
	<tr bgcolor="#373737" height="30">
		<td width="2%" style="text-align:left;" class="smalltext12">&nbsp;</td>
		<td width="11%" style="text-align:left;" class="smalltext12">Date</td>
		<td width="15%" style="text-align:left;" class="smalltext12">Employee</td>
		<td class="smalltext12">Status Report</td>	
	</tr>
	<?php
		while($row1					=   mysqli_fetch_assoc($recordSet))
		{
			
			$i++;
			$employeeId				=	$row1['employeeId'];
			$employeeName			=	stripslashes($row1['fullName']);
			$reportDate			    =	stripslashes($row1['reportDate']);
			$statusReport			=	stripslashes($row1['statusReport']);
		?>
		<tr>
			<td class="smalltext2" valign="top"><?php echo $i;?>)</td>
			<td class="smalltext2" valign="top"><?php echo showDate($reportDate);?></td>
			<td class="smalltext2" valign="top"><?php echo $employeeName;?></td>
			<td class="smalltext2" valign="top"><?php echo nl2br($statusReport);?></td>			
		</tr>
		<tr>
			<td colspan="4"><hr size="1" style="color:#bebebe;"></td>
		</tr>
		<?php
		}
	 ?>
	 <tr>
		<td colspan="4" style="text-align:right"><?php echo $pagingObj->displayPaging($queryString);?>&nbsp;&nbsp;</td>
	 </tr>
</table>
<?php
	}
	else{
		echo "<table width='22%' border='0' align='center' height='300'><tr><td align='center' class='error2'><b>No Records Found</b></td></tr></table>";
	}

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>