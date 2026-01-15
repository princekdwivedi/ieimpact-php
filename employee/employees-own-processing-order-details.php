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
	$employeeObj				= new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	.   "/includes/check-pdf-login.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/orders.php");
	$orderObj					=  new orders();

	$searchBy					=	1;
	$display					=	"";
	$display1					=	"none";
	$searchDate					=	date("d-m-Y");
	$t_searchDate				=	date("Y-m-d");
	$searchMonth				=	date("m");
	$searchYear					=	date("Y");
	$employeeId					=	0;
	$checked					=	"checked";
	$checked1					=	"";
	$andClause					=	" AND orderCompletedOn='$t_searchDate'";
	$text						=	" ON ".showDate($t_searchDate);

	$totalEmployeeCompletedOrders	=	0;
	$totalCustomersRatedOrders	=	0;
	$totalQaRatedOrders			=	0;

	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);

		//pr($_REQUEST);
		if($searchBy			==	1)
		{
			list($date,$month,$year)	=	explode("-",$searchDate);

			$t_searchDate		=	$year."-".$month."-".$date;

			$andClause			=	" AND orderCompletedOn='$t_searchDate'";
			
			$text				=	" ON ".showDate($t_searchDate);
			$serachString		=	"searchBy=".$searchBy."&searchDate=".$t_searchDate;

		}
		else
		{
			$andClause			=	" AND MONTH(orderCompletedOn)=$searchMonth AND YEAR(orderCompletedOn)=$searchYear";

			$checked			=	"";
			$checked1			=	"checked";

			$display			=	"none";
			$display1			=	"";

			$text				=	" ON ".$a_month[$searchMonth].",".$searchYear;
			$serachString		=	"searchBy=".$searchBy."&searchMonth=".$searchMonth."&searchYear=".$searchYear;
		}
	}
?>
<table width='98%' align='center' cellpadding='2' cellspacing='2' border='0'>
	<tr>
		<td height="5"></td>
	</tr>
	<tr>
		<td class="heading1">
			:: VIEW YOUR TOTAL COMPLETED ORDERS AND CUSTOMER RATINGS DETAILS <?php echo $text;?>::
		</td>
	</tr>
	<tr>
		<td colspan="8" height="5"></td>
	</tr>
</table>
<br>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/common-ajax.js"></script>

<script type="text/javascript">
function showSearch(flag)
{
	if(flag == 1)
	{
		document.getElementById('displayDate').style.display  = 'inline';
		document.getElementById('displayMonth').style.display = 'none';
	}
	else
	{
		document.getElementById('displayDate').style.display  = 'none';
		document.getElementById('displayMonth').style.display = 'inline';
	}
}
</script>
<form name="searchPdfForm" action=""  method="POST" onsubmit="return search();">
	<table cellpadding="0" cellspacing="0" width='98%'align="center" border='0'>
		<tr>
			<td width="25%" class="smalltext2" valign="top"><b>View Total By<input type="radio" name="searchBy" value="1" <?php echo $checked;?> onclick="return showSearch(1)">Date Or By <input type="radio" name="searchBy" value="2" <?php echo $checked1;?> onclick="return showSearch(2)">Month</b></td>
			<td width="2%" class="smalltext2" valign="top"><b>:</b></td>
			<td width="20%" valign="top" class="title1">
				<div  id="displayDate" style="display:<?php echo $display;?>">
					DATE&nbsp;&nbsp;
					<input type="text" name="searchDate" value="<?php echo $searchDate;?>" id="atOn" readonly size="10" readonly>&nbsp;&nbsp;<a href="javascript:NewCssCal('atOn','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
				</div>
				<div  id="displayMonth" style="display:<?php echo $display1;?>">
					MONTH
					<select name="searchMonth">
						<?php
							foreach($a_month as $key=>$value)
							{
								$select	  =	"";
								if($searchMonth == $key)
								{
									$select	  =	"selected";
								}

								echo "<option value='$key' $select>$value</option>";
							}
						?>
					</select>&nbsp;&nbsp;
					YEAR
					<select name="searchYear">
						<?php
							$sYear	=	"2010";
							$eYear	=	date("Y")+1;
							for($i=$sYear;$i<=$eYear;$i++)
							{
								$select			=	"";
								if($searchYear  == $i)
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
<?php
	$query	=	"SELECT COUNT(orderId) AS totalEmployeeCompletedOrders FROM members_orders WHERE acceptedBy =$s_employeeId AND status=2".$andClause;
	$result	=	dbQuery($query);
	if(mysql_num_rows($result))
	{
?>
<br>
<table cellpadding="2" cellspacing="2" width='98%'align="center" border='0'>
	<tr>
		<td colspan="20" class="smalltest2">
			<b>VIEW YOUR COMPLETED ORDER DETAILS</b>
		</td>
	</tr>
	<tr>
		<td colspan="20">
			<hr size="1" width="100%" color="#4d4d4d">
		</td>
	</tr>
	<tr>
		<td width="11%" class="smalltext2">Completed Orders</td>
		<td width="14%" class="smalltext2">Customer Rating Orders</td>
		<td width="6%" class="smalltext2">One Star</td>
		<td width="6%" class="smalltext2">Two Star</td>
		<td width="7%" class="smalltext2">Three Star</td>
		<td width="6%" class="smalltext2">Four Star</td>
		<td width="6%" class="smalltext2">Five Star</td>
		<td width="11%" class="smalltext2">QA Rating Orders</td>
		<td width="6%" class="smalltext2">One Star</td>
		<td width="7%" class="smalltext2">Two Star</td>
		<td width="7%" class="smalltext2">Three Star</td>
		<td width="7%" class="smalltext2">Four Star</td>
		<td class="smalltext2">Five Star</td>
	</tr>
	<tr>
		<td colspan="20">
			<hr size="1" width="100%" color="#4d4d4d">
		</td>
	</tr>
	<?php
			$row							=	mysql_fetch_assoc($result);
		
			$totalEmployeeCompletedOrders	=	$row['totalEmployeeCompletedOrders'];

			$totalCustomerRatedOrders		=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders WHERE acceptedBy=$s_employeeId AND status=2 AND rateGiven <> 0".$andClause),0);
			if(empty($totalCustomerRatedOrders))
			{
				$totalCustomerRatedOrders	=	0;
			}

			$oneStarCompletedOrders			=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders WHERE acceptedBy=$s_employeeId AND status=2 AND rateGiven=1".$andClause),0);
			if(empty($oneStarCompletedOrders))
			{
				$oneStarCompletedOrders	=	0;
			}

			$twoStarCompletedOrders			=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders WHERE acceptedBy=$s_employeeId AND status=2 AND rateGiven=2".$andClause),0);
			if(empty($twoStarCompletedOrders))
			{
				$twoStarCompletedOrders	=	0;
			}

			$threeStarCompletedOrders		=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders WHERE acceptedBy=$s_employeeId AND status=2 AND rateGiven=3".$andClause),0);
			if(empty($threeStarCompletedOrders))
			{
				$threeStarCompletedOrders	=	0;
			}

			$fourthStarCompletedOrders		=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders WHERE acceptedBy=$s_employeeId AND status=2 AND rateGiven=4".$andClause),0);
			if(empty($fourthStarCompletedOrders))
			{
				$fourthStarCompletedOrders	=	0;
			}

			$fifthStarCompletedOrders		=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM members_orders WHERE acceptedBy=$s_employeeId AND status=2 AND rateGiven=5".$andClause),0);
			if(empty($fifthStarCompletedOrders))
			{
				$fifthStarCompletedOrders	=	0;
			}

			$totalCompletedOrderIds	=	$orderObj->totalPocessedQAOrderIds($s_employeeId,$andClause);
			if(!empty($totalCompletedOrderIds))
			{

				$totalQaOrders			=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM employee_miscellaneous_details WHERE orderId IN ($totalCompletedOrderIds) AND rateByQa <> 0"),0);

				if(empty($totalQaOrders))
				{
					$totalQaOrders		=	0;
				}

				$oneQaStarQaOrders		=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM employee_miscellaneous_details WHERE orderId IN ($totalCompletedOrderIds) AND rateByQa = 1"),0);
				if(empty($oneQaStarQaOrders))
				{
					$oneQaStarQaOrders	=	0;
				}

				$twoQaStarQaOrders		=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM employee_miscellaneous_details WHERE orderId IN ($totalCompletedOrderIds) AND rateByQa = 2"),0);
				if(empty($twoQaStarQaOrders))
				{
					$twoQaStarQaOrders	=	0;
				}

				$threeQaStarQaOrders		=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM employee_miscellaneous_details WHERE orderId IN ($totalCompletedOrderIds) AND rateByQa = 3"),0);
				if(empty($threeoStarQaOrders))
				{
					$threeoStarQaOrders	=	0;
				}

				$fourQaStarQaOrders		=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM employee_miscellaneous_details WHERE orderId IN ($totalCompletedOrderIds) AND rateByQa = 4"),0);
				if(empty($fourQaStarQaOrders))
				{
					$fourQaStarQaOrders	=	0;
				}

				$fiveQaStarQaOrders		=	@mysql_result(dbQuery("SELECT COUNT(orderId) FROM employee_miscellaneous_details WHERE orderId IN ($totalCompletedOrderIds) AND rateByQa = 5"),0);
				if(empty($fiveQaStarQaOrders))
				{
					$fiveQaStarQaOrders	=	0;
				}
			}
			else
			{
					$totalQaOrders		=	0;	
					$oneQaStarQaOrders	=	0;
					$twoQaStarQaOrders	=	0;
					$threeQaStarQaOrders=	0;
					$fourQaStarQaOrders	=	0;
					$fiveQaStarQaOrders	=	0;
			}
	?>
	<tr>
		<td class="textstyle1">
			<b><?php echo $totalEmployeeCompletedOrders;?></b>
		</td>
		<td class="textstyle1">
			<b><?php echo $totalCustomerRatedOrders;?></b>
		</td>
		<td class="textstyle1">
			<b><?php echo $oneStarCompletedOrders;?></b>
		</td>
		<td class="textstyle1">
			<b><?php echo $twoStarCompletedOrders;?></b>
		</td>
		<td class="textstyle1">
			<b><?php echo $threeStarCompletedOrders;?></b>
		</td>
		<td class="textstyle1">
			<b><?php echo $fourthStarCompletedOrders;?></b>
		</td>
		<td class="textstyle1">
			<b><?php echo $fifthStarCompletedOrders;?></b>
		</td>
		<td class="textstyle1" align="center">
			<b><?php echo $totalQaOrders;?></b>
		</td>
		<td class="textstyle1">
			<b><?php echo $oneQaStarQaOrders;?></b>
		</td>
		<td class="textstyle1">
			<b><?php echo $twoQaStarQaOrders;?></b>
		</td>
		<td class="textstyle1">
			<b><?php echo $threeQaStarQaOrders;?></b>
		</td>
		<td class="textstyle1">
			<b><?php echo $fourQaStarQaOrders;?></b>
		</td>
		<td class="textstyle1">
			<b><?php echo $fiveQaStarQaOrders;?></b>
		</td>
	</tr>
	<tr>
		<td colspan="20">
			<hr size="1" width="100%" color="#4d4d4d">
		</td>
	</tr>
</table>
<br>
<?php
	if(!empty($totalCustomerRatedOrders))	
	{
?>
<table cellpadding="2" cellspacing="2" width='70%' align="left" border='0' style="border:1px solid #4d4d4d">
	<tr>
		<td class="textstyle1" width="75%">
			<b>TOTAL CUSTOMER RATED ORDER ON <?php echo $text;?></b>
		</td>
		<td class="textstyle1" width="2%">
			<b>:</b>
		</td>
		<td class="error2">
			<b><?php echo $totalCustomerRatedOrders;?></b>
		</td>
	</tr>
	<tr>
		<td colspan="3" height="5"></td>
	</tr>
	<tr>
		<td colspan="3" align="center" valign="center">
			<script type="text/javascript" src="https://www.google.com/jsapi"></script>
				<script type="text/javascript">
				  google.load("visualization", "1", {packages:["corechart"]});
				  google.setOnLoadCallback(drawChart);
				  function drawChart() {
					var data = new google.visualization.DataTable();
					data.addColumn('string', 'Year');
					data.addColumn('number', 'Ratings');
					data.addRows(5);
					data.setValue(0, 0, 'One Star (Awful)');
					data.setValue(0, 1, <?php echo $oneStarCompletedOrders;?>);
					data.setValue(1, 0, 'Two Star (Poor)');
					data.setValue(1, 1, <?php echo $twoStarCompletedOrders;?>);
					data.setValue(2, 0, 'Three Star (Fair)');
					data.setValue(2, 1, <?php echo $threeStarCompletedOrders;?>);
					data.setValue(3, 0, 'Four Star (Good)');
					data.setValue(3, 1, <?php echo $fourthStarCompletedOrders;?>);
					data.setValue(4, 0, 'Five Star (Excellent)');
					data.setValue(4, 1, <?php echo $fifthStarCompletedOrders;?>);

					var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
					chart.draw(data, {width: 700, height: 240, title: 'Customer Ratings On Your Completed Orders',
									  hAxis: {title: 'Ratings', titleTextStyle: {color: 'red'}}
									 });
				  }
				</script>
										 
				<div id="chart_div"></div>

		</td>
	</tr>
</table>
<table cellpadding="2" cellspacing="2" height="10">
	<tr>
		<td>&nbsp;</td>
	</tr>
<table>
<?php
	}

	if(!empty($totalQaOrders))	
	{
?>
<br><br><br>
<table cellpadding="2" cellspacing="2" width='70%'align="left" border='0' style="border:1px solid #4d4d4d">
	<tr>
		<td class="textstyle1" width="75%">
			<b>TOTAL QA RATED ORDER ON <?php echo $text;?></b>
		</td>
		<td class="textstyle1" width="2%">
			<b>:</b>
		</td>
		<td class="error2">
			<b><?php echo $totalQaOrders;?></b>
		</td>
	</tr>
	<tr>
		<td colspan="3" height="5"></td>
	</tr>
	<tr>
		<td colspan="3" align="center" valign="center">
			<script type="text/javascript" src="https://www.google.com/jsapi"></script>
				<script type="text/javascript">
				  google.load("visualization", "1", {packages:["corechart"]});
				  google.setOnLoadCallback(drawChart);
				  function drawChart() {
					var data = new google.visualization.DataTable();
					data.addColumn('string', 'Year');
					data.addColumn('number', 'Ratings');
					data.addRows(5);
					data.setValue(0, 0, 'One Star (Poor)');
					data.setValue(0, 1, <?php echo $oneQaStarQaOrders;?>);
					data.setValue(1, 0, 'Two Star (Average)');
					data.setValue(1, 1, <?php echo $twoQaStarQaOrders;?>);
					data.setValue(2, 0, 'Three Star (Good)');
					data.setValue(2, 1, <?php echo $threeQaStarQaOrders;?>);
					data.setValue(3, 0, 'Four Star (Very Good)');
					data.setValue(3, 1, <?php echo $fourQaStarQaOrders;?>);
					data.setValue(4, 0, 'Five Star (Excellent)');
					data.setValue(4, 1, <?php echo $fiveQaStarQaOrders;?>);

					var chart = new google.visualization.ColumnChart(document.getElementById('chart_div1'));
					chart.draw(data, {width: 700, height: 240, title: 'Qa Ratings On Your Completed Orders',
									  hAxis: {title: 'Ratings', titleTextStyle: {color: 'red'}}
									 });
				  }
				</script>
									 
				<div id="chart_div1"></div>

		</td>
	</tr>
</table>
<?php
	}
			
}
else
{
	echo "<br><br><center><font class='error1'><b>No Record Found !!</b></font></center>";
}
include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>






