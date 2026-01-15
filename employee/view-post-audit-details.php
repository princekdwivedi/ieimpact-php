<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/new-top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT			. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	$employeeObj				= new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-pdf-login.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	$pagingObj					=  new Paging();
	$memberObj					=  new members();
	$orderObj					=  new orders();

	$month						=	date("m");
	$year						=	date("Y");
	

	$form						=	SITE_ROOT_EMPLOYEES."/forms/month-year.php";
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		$redirectText			=	"";
		if(!empty($month) && !empty($year))
		{
			$redirectText		=	"?month=".$month."&year=".$year;
		}

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/view-post-audit-details.php".$redirectText);
		exit();
	}

	if(isset($_GET['month']))
	{
		$month					=	$_GET['month'];
	}
	if(isset($_GET['year']))
	{
		$year					=	$_GET['year'];
	}

	$searchingText				=	$a_month[$month].", ".$year;

	$feb						=	"28";

	if($year%2==0)
	{
		$feb					=	"29";
	}
	$a_monthDaysWithLeap		=	array("01"=>"31","02"=>$feb,"03"=>"31","04"=>"30","05"=>"31","06"=>"30","07"=>"31","08"=>"31","09"=>"30","10"=>"31","11"=>"30","12"=>"31");

	$searchFromDate				=	"01-".$month."-".$year;
	$searchToDate				=	$a_monthDaysWithLeap[$month]."-".$month."-".$year;
	$formSearch					=  SITE_ROOT_EMPLOYEES."/forms/search-general-order-form.php";
?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
	<tr>
		<td class="heading3">
			:: POST AUDIT RESULTS OF <?php echo $searchingText;?> ::
		</td>
	</tr>
	<tr>
		<td>
			<?php
				include($formSearch);
			?>
		</td>
	</tr>
	<tr>
		<td height="5"></td>
	</tr>
	<tr>
		<td>
			<?php 
				include($form);
			?>
		</td>
	</tr>
	<tr>
		<td height="5"></td>
	</tr>
</table>
<?php
	$orderByClause					=	"AllTotal DESC";
	$andCaluse						=	"";
	$employeeId						=	0;

	$firstCatgoryAscDesc			=	"firstAsc=1";
	$firstCatgoryAscDescText		=	"Ascending By First Category";

	$secondCatgoryAscDesc			=	"secondAsc=1";
	$secondCatgoryAscDescText		=	"Ascending By Second Category";


	$thirdCatgoryAscDesc			=	"thirdAsc=1";
	$thirdCatgoryAscDescText		=	"Ascending By Second Category";

	$totalCatgoryAscDesc			=	"totalAsc=1";
	$totalCatgoryAscDescText		=	"Ascending By All Total";

	if(isset($_GET['firstAsc']))
	{
		$showFirstCatgoryAsc		=	$_GET['firstAsc'];
		if(!empty($showFirstCatgoryAsc))
		{
			$orderByClause			=	"TotalFirstCategory";
			$firstCatgoryAscDesc	=	"firstDsc=1";
			$firstCatgoryAscDescText=	"Descending By First Category";
		}
	}
	elseif(isset($_GET['firstDsc']))
	{
		$showFirstCatgoryDesc		=	$_GET['firstDsc'];
		if(!empty($showFirstCatgoryDesc))
		{
			$orderByClause			=	"TotalFirstCategory DESC";
			$firstCatgoryAscDesc	=	"firstAsc=1";
			$firstCatgoryAscDescText=	"Ascending By First Category";
		}
	}
	elseif(isset($_GET['secondAsc']))
	{
		$showSecondCatgoryAsc		 =	$_GET['secondAsc'];
		if(!empty($showSecondCatgoryAsc))
		{
			$orderByClause			 =	"TotalSecondCategory";
			$secondCatgoryAscDesc	 =	"secondDsc=1";
			$secondCatgoryAscDescText=	"Descending By Second Category";
		}
	}
	elseif(isset($_GET['secondDsc']))
	{
		$showSecondCatgoryDesc		 =	$_GET['secondDsc'];
		if(!empty($showSecondCatgoryDesc))
		{
			$orderByClause			 =	"TotalSecondCategory DESC";
			$secondCatgoryAscDesc	 =	"secondAsc=1";
			$secondCatgoryAscDescText=	"Ascending By Second Category";
		}
	}
	elseif(isset($_GET['thirdAsc']))
	{
		$showThirdCatgoryAsc		 =	$_GET['thirdAsc'];
		if(!empty($showThirdCatgoryAsc))
		{
			$orderByClause			 =	"TotalThirdCategory";
			$thirdCatgoryAscDesc	 =	"thirdDsc=1";
			$thirdCatgoryAscDescText =	"Descending By Third Category";
		}
	}
	elseif(isset($_GET['thirdDsc']))
	{
		$showThirdCatgoryDesc		 =	$_GET['thirdDsc'];
		if(!empty($showThirdCatgoryDesc))
		{
			$orderByClause			 =	"TotalThirdCategory DESC";
			$thirdCatgoryAscDesc	 =	"thirdAsc=1";
			$thirdCatgoryAscDescText =	"Ascending By Third Category";
		}
	}
	elseif(isset($_GET['totalAsc']))
	{
		$showTotalCatgoryAsc		 =	$_GET['totalAsc'];
		if(!empty($showTotalCatgoryAsc))
		{
			$orderByClause			 =	"TotalAuditedForEmployees";
			$totalCatgoryAscDesc	 =	"totalDsc=1";
			$totalCatgoryAscDescText =	"Descending By Month Total Audit Files";
		}
	}
	elseif(isset($_GET['totalDsc']))
	{
		$showTotalCatgoryDesc		 =	$_GET['totalDsc'];
		if(!empty($showTotalCatgoryDesc))
		{
			$orderByClause			 =	"TotalAuditedForEmployees DESC";
			$totalCatgoryAscDesc	 =	"totalAsc=1";
			$totalCatgoryAscDescText =	"Ascending By Month Total Audit Files";
		}
	}
	$searchMonthYear				 =  "&month=".$month."&year=".$year;

?>
<script type="text/javascript">
	function redirectPageLink(path)
	{
		window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/view-post-audit-details.php?"+path;
	}
</script>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr height='25' bgcolor="#373737">
		<td width="20%" class='smalltext12'>
			&nbsp;<b>EMPLOYEE</b>
		</td>
		<td width="15%"
			<a onclick="redirectPageLink('<?php echo $firstCatgoryAscDesc.$searchMonthYear;?>')" style="cursor:pointer;" title="<?php echo $firstCatgoryAscDescText?>" class="linkstyle25"><b>CAT A ERROR ORDERS</b><a>
		</td>
		<td width="12%">
			<a onclick="redirectPageLink('<?php echo $secondCatgoryAscDesc.$searchMonthYear;?>')" style="cursor:pointer;" title="<?php echo $secondCatgoryAscDescText?>" class="linkstyle25"><b>CAT B ERROR ORDERS</b><a>
		</td>
		<td width="12%">
			<a onclick="redirectPageLink('<?php echo $thirdCatgoryAscDesc.$searchMonthYear;?>')" style="cursor:pointer;" title="<?php echo $thirdCatgoryAscDescText?>" class="linkstyle25"><b>CAT C ERROR ORDERS</b><a>
		</td>
		<td width="25%">
			<a onclick="redirectPageLink('<?php echo $totalCatgoryAscDesc.$searchMonthYear;?>')" style="cursor:pointer;" title="<?php echo $totalCatgoryAscDescText?>" class="linkstyle25"><b>TOTAL ORDERS POST AUDITED</b><a>
		</td>
		<td>&nbsp;</td>
	</tr>
<?php
	$query				=	"SELECT processEmployee,SUM(firstCategory) AS TotalFirstCategory,SUM(secondCategory) AS TotalSecondCategory,SUM(thirdCategory) AS TotalThirdCategory,SUM(firstCategory+secondCategory+thirdCategory) as AllTotal,SUM(totalCount) AS TotalAuditedForEmployees FROM orders_post_audit_details WHERE MONTH(orderAddedOn)='$month' AND YEAR(orderAddedOn)=$year GROUP BY processEmployee ORDER BY ".$orderByClause;
	$result				=	dbQuery($query);
	if(mysql_num_rows($result))
	{
		$i	=	0;
		while($row						=	mysql_fetch_assoc($result))
		{				
			
			$processEmployee			=	$row['processEmployee'];
			$totalFirstCategory			=	$row['TotalFirstCategory'];
			$totalSecondCategory		=	$row['TotalSecondCategory'];
			$totalThirdCategory			=	$row['TotalThirdCategory'];
			$allTotal					=	$row['AllTotal'];
			$totalAuditedForEmployees	=	$row['TotalAuditedForEmployees'];

			if($employeeName			=	$employeeObj->getActiveEmployeeName($processEmployee))
			{
				$i++;

				$bgColor				=	"class='rwcolor1'";
				if($i%2==0)
				{
					$bgColor			=	"class='rwcolor2'";
				}
		?>
		<tr height='23' <?php echo $bgColor;?>>
			<td class='smalltext2' valign="top">&nbsp;<a href="<?php echo SITE_URL_EMPLOYEES;?>/new-pdf-work.php?isSubmittedForm=1&fromDate=<?php echo $searchFromDate;?>&endDate=<?php echo $searchToDate;?>&showPageOrders=50&orderOf=<?php echo $processEmployee;?>&showingEmployeeOrder=1&Olink=1&PAD=1"><?php echo $employeeName;?></td>
			<td class='smalltext2' valign="top" style="text-align:center"><?php echo $totalFirstCategory;?></td>
			<td class='smalltext2' valign="top" style="text-align:center"><?php echo $totalSecondCategory;?></td>
			<td class='smalltext2' valign="top" style="text-align:center"><?php echo $totalThirdCategory;?></td>
			<td class='smalltext2' valign="top" style="text-align:center"><?php echo $totalAuditedForEmployees;?></td>
			<td>&nbsp;</td>
		</tr>
		<?php
			}

		}
	}
	else
	{
?>
<tr>
	<td colspan="8" height="200" class="error" style="text-align:center"><b>NO RECORD FOUND</b></td>
</tr>
<?php
	}
?>
</table>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>