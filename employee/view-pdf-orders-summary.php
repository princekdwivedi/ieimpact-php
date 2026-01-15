<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	."/includes/common-array.php");
	$employeeObj				=	new employee();
	include(SITE_ROOT			. "/admin/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT			. "/classes/pagingclass.php");
	include(SITE_ROOT_MEMBERS	. "/classes/members.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	$pagingObj					=  new Paging();
	$memberObj					=  new members();
	$orderObj					=  new orders();
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	$orderSummaryFor	=	0;
	$showPage			=	false;
	$orderSummaryName	=	"";
	$a_allPdfEmployees	=	array();
	$a_allPdfEmployees	=	$employeeObj->getAllPdfEmployees();
	$searchBy			=	1;
	$searchOn			=	date("d-m-Y");
	$t_searchOn			=	date("Y-m-d");
	$month				=	date("m");
	$year				=	date("Y");
	$text				=	" FOR ".showDate($t_searchOn);
	$text1				=	"";
	$displayDate		=	"";
	$displayMonth		=	"none";
	$checked			=	"checked";
	$checked1			=	"";

	$whereClause		=	"WHERE orderAddedOn='$t_searchOn'";
	$queryString		=	"&searchBy="$searchBy."&searchOn=".$searchOn;
	$orderBy			=	"orderAddedOn DESC,orderAddedTime DESC";
	$andClause			=	"";
	$tables				=	"members_orders";
	if(isset($_GET['searchBy']))
	{
		$searchBy		=	$_GET['searchBy'];
		if($searchBy	==	1)
		{
			$searchOn	=	$_REQUEST['searchOn'];
			if(!empty($searchOn))
			{
				list($day,$month,$year)		=	explode(",",$searchOn);

				$whereClause	=	"WHERE orderAddedOn='$t_searchOn'";
				$queryString	=	"&searchBy="$searchBy."&searchOn=".$searchOn;
				$text			=	" FOR ".showDate($t_searchOn);
			}
		}
		else
		{
			$month			=	$_REQUEST['month'];
			$year			=	$_REQUEST['year'];
			if(!empty($month) && !empty($year))
			{
				$whereClause	=	"WHERE MONTH(orderAddedOn)='$month' AND YEAR(orderAddedOn)=$year";
				$queryString	=	"&searchBy="$searchBy."&month=".$month."&year=".$year;
				$monthText		=	$a_month[$month];
				$text			=	" FOR ".$monthText.",".$year;
			}
		}
	}
	if(isset($_GET['orderSummaryFor']))
	{
		$orderSummaryFor		=	$_GET['orderSummaryFor'];
		if(!empty($orderSummaryFor))
		{
			$andClause	=	" AND members_orders.status <> 0 AND ";
		}
	}
?>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/validate.js"></script>
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
</script>

<form name="searchForm" action=""  method="get">
	<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
		<tr>
			<td class='heading' colspan="6">VIEW PDF EMPLOYEE WORK DONE SUMMARY</td>
		</tr>
		<tr>
			<td colspan="6" height="10"></td>
		</tr>
		<tr>
			<td width="25%" class="smalltext2" valign="top"><b>Search By <input type="radio" name="searchBy" value="1" <?php echo $checked;?> onclick="return showSearch(1)">Date Or By <input type="radio" name="searchBy" value="2" <?php echo $checked1;?> onclick="return showSearch(2)">Month</b></td>
			<td width="2%" class="smalltext2" valign="top"><b>:</b></td>
			<td width="30%" valign="top" class="title1">
				<div  id="displayDate" style="display:<?php echo $displayDate;?>">
					DATE&nbsp;&nbsp;
					<input type="text" name="searchOn" value="<?php echo $searchOn;?>" id="atOn" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('atOn','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
				</div>
				<div  id="displayMonth" style="display:<?php echo $displayMonth;?>">
					MONTH
					<select name="month">
						<?php
							foreach($a_month as $key=>$value)
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
					YEAR
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
			<td width="8%" valign="top" class="title1">
				For Employee
			</td>
			<td width="15%" valign="top">
				<select name="orderSummaryFor">
				<option value="">Select</option>
				<?php
					foreach($a_allPdfEmployees as $key=>$value)
					{
						$select		=	"";
						if($orderSummaryFor	==	$key)
						{
							$select	=	"selected";	
						}
						echo "<option value='$key' $select>$value</option>";
					}
				?>
			</select>
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

<?php
include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>