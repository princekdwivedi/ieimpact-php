<?php
	ob_start();
	session_start();
		ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES     .   "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/check-admin-login.php");
	include(SITE_ROOT_EMPLOYEES		.	"/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/common-array.php");
	include(SITE_ROOT				.	"/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/set-variables.php");

	$month 							=	0;
	$year 							=	0;
	$searchType 					= 	'datewise'; // Default to datewise
	$dateFrom 						= 	'';
	$dateTo 						= 	'';
	$dateCondition					=	'';
	$displayText					=	'';

	// Check search type
	if(isset($_GET['searchType'])){
		$searchType 				=	$_GET['searchType'];
	}

	if($searchType == 'monthwise'){
		if(isset($_GET['month']) && isset($_GET['year'])){
			$month 					=	(int)$_GET['month'];
			$year 					=	(int)$_GET['year'];
			if(!empty($month) && !empty($year)){
				$dateCondition		=	"MONTH(assignToEmployee)=$month AND YEAR(assignToEmployee)=$year";
				$displayText		=	$a_month[$month].", ".$year;
			}
		}
	} else {
		// Datewise - FROM DATE is mandatory, TO DATE is optional
		if(isset($_GET['dateFrom'])){
			$dateFrom 				=	trim($_GET['dateFrom']);
			
			// Validate date format (YYYY-MM-DD)
			if(!empty($dateFrom) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)){
				$dateFrom 			=	addslashes($dateFrom);
				
				// Check if TO DATE is also provided
				if(isset($_GET['dateTo']) && !empty($_GET['dateTo'])){
					$dateTo 		=	trim($_GET['dateTo']);
					// Validate TO DATE format
					if(preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)){
						$dateTo 		=	addslashes($dateTo);
						// Date range search
						$dateCondition	=	"DATE(assignToEmployee) BETWEEN '$dateFrom' AND '$dateTo'";
						$displayText	=	date('M d, Y', strtotime($dateFrom))." - ".date('M d, Y', strtotime($dateTo));
					}
				} else {
					// Single date search (only FROM DATE)
					$dateCondition	=	"DATE(assignToEmployee) = '$dateFrom'";
					$displayText	=	date('M d, Y', strtotime($dateFrom));
				}
			}
		}
	}
?>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<style>
	.search-type-section {
		background: #f8f9fa;
		padding: 15px;
		border-radius: 8px;
		margin-bottom: 15px;
	}
	.radio-group {
		display: inline-block;
		margin-right: 30px;
	}
	.radio-group input[type="radio"] {
		margin-right: 5px;
		cursor: pointer;
	}
	.radio-group label {
		cursor: pointer;
		font-weight: bold;
	}
	.date-inputs {
		display: none;
	}
	.date-inputs.active {
		display: table-row;
	}
	.month-inputs {
		display: none;
	}
	.month-inputs.active {
		display: table-row;
	}
	input[type="text"].datepicker {
		cursor: pointer;
		background: white;
		padding: 5px 10px;
		border: 1px solid #ccc;
		border-radius: 4px;
		width: 150px;
	}
</style>
<script type = "text/javascript">
	$(document).ready(function(){
		// Initialize datepickers
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			yearRange: '2009:' + new Date().getFullYear(),
			maxDate: new Date()
		});

		// Handle search type change
		$('input[name="searchType"]').change(function(){
			var searchType = $(this).val();
			if(searchType == 'datewise'){
				$('.date-inputs').addClass('active');
				$('.month-inputs').removeClass('active');
			} else {
				$('.date-inputs').removeClass('active');
				$('.month-inputs').addClass('active');
			}
		});

		// Trigger initial state
		$('input[name="searchType"]:checked').trigger('change');
	});
	
	function openPrintExcelWindow(pageUrl)
	{
		path = pageUrl;
		prop = "toolbar=no,scrollbars=yes,width=400,height=100,top=200,left=300";
		window.open(path,'',prop);
	}

	function validateForm(){
		var searchType = $('input[name="searchType"]:checked').val();
		if(searchType == 'datewise'){
			var dateFrom = $('input[name="dateFrom"]').val();
			var dateTo = $('input[name="dateTo"]').val();
			
			// FROM DATE is mandatory
			if(dateFrom == ''){
				alert('Please select From Date');
				return false;
			}
			
			// If TO DATE is provided, validate it
			if(dateTo != '' && dateFrom > dateTo){
				alert('From Date cannot be greater than To Date');
				return false;
			}
		} else {
			var month = $('select[name="month"]').val();
			var year = $('select[name="year"]').val();
			if(month == '0' || year == '0'){
				alert('Please select both Month and Year');
				return false;
			}
		}
		return true;
	}
</script>
<form name="searchEmployeeMonthlyData" action="" method="GET" onsubmit="return validateForm();">
	<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
		<tr>
			<td colspan="8" class="textstyle3"><b>GET PDF WORK STATUS</b></td>
		</tr>
		<tr>
			<td colspan="8" class="search-type-section">
				<div class="radio-group">
					<input type="radio" id="datewise" name="searchType" value="datewise" <?php echo ($searchType == 'datewise') ? 'checked' : ''; ?>>
					<label for="datewise" class="smalltext23">Search by Date Range</label>
				</div>
				<div class="radio-group">
					<input type="radio" id="monthwise" name="searchType" value="monthwise" <?php echo ($searchType == 'monthwise') ? 'checked' : ''; ?>>
					<label for="monthwise" class="smalltext23">Search by Month</label>
				</div>
			</td>
		</tr>
		<tr class="date-inputs <?php echo ($searchType == 'datewise') ? 'active' : ''; ?>">
			<td width="10%" class="smalltext23">FROM DATE <span style="color:red;">*</span>:</td>
			<td width="15%">
				<input type="text" name="dateFrom" class="datepicker smalltext23" value="<?php echo $dateFrom; ?>" placeholder="YYYY-MM-DD" readonly>
			</td>
			<td width="10%" class="smalltext23">TO DATE (Optional):</td>
			<td width="15%">
				<input type="text" name="dateTo" class="datepicker smalltext23" value="<?php echo $dateTo; ?>" placeholder="YYYY-MM-DD" readonly>
			</td>
		</tr>
		<tr class="month-inputs <?php echo ($searchType == 'monthwise') ? 'active' : ''; ?>">
			<td width="10%" class="smalltext23">SELECT MONTH :</td>
			<td width="15%">
				<select name="month" class="smalltext23">
					<option value="0">Select</option>
					<?php
						foreach($a_month as $k=>$v){
							$select		=	"";
							if($k		== $month){
								$select	=	"selected";
							}
					?>
					<option value="<?php echo $k;?>" <?php echo $select;?>><?php echo $v;?></option>
				<?php
					}
				?>
				</select>
			</td>
			<td width="10%" class="smalltext23">SELECT YEAR :</td>
			<td width="15%">
				<select name="year" class="smalltext23">
					<option value="0">Select</option>
					<?php
						for($i=2009;$i<=date('Y');$i++){
							$select 	=	"";
							if($i       == $year){
								$select =	"selected";
							}
							echo "<option value='$i' $select>$i</option>";
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="4" align="center">
				<input type='image' name='submit' src='<?php echo SITE_URL;?>/images/submit.jpg'>
				<input type='hidden' value='1' name='formSubmitted'>
			</td>
		</tr>		
	</table>	
</form>
<?php
	if(!empty($dateCondition)){
?>
	<br />
	<table width="100%" align="center" border="0" cellpadding="3" cellspacing="3">	
		<tr>
			<td colspan="8" class="textstyle3"><b>PDF WORK STATUS - <?php echo $displayText;?></b></td>
		</tr>
		<tr>
			<td width="15%" class="textstyle3">EMPLOYEE</td>
			<td width="15%" class="textstyle3">FILES PROCESSED</td>
			<td width="15%" class="textstyle3">6 HRS</td>
			<td width="15%" class="textstyle3">12 HRS</td>
			<td width="15%" class="textstyle3">24 HRS</td>
			<td width="15%" class="textstyle3">AWFUL RATING</td>
			<td class="textstyle3">POOR RATING</td>
		</tr>
		<?php
			// OPTIMIZED QUERY - Single query with conditional aggregation for better performance
			$query = "SELECT 
				mo.acceptedBy,
				ed.fullName,
				COUNT(*) AS totalDone,
				SUM(CASE WHEN mo.status IN (2,4,5) AND mo.isRushOrder=1 THEN 1 ELSE 0 END) AS rushOrders,
				SUM(CASE WHEN mo.status IN (2,4,5) AND mo.isRushOrder=0 THEN 1 ELSE 0 END) AS all12HoursOrders,
				SUM(CASE WHEN mo.status IN (2,4,5) AND mo.isRushOrder=2 THEN 1 ELSE 0 END) AS all24HoursOrders,
				SUM(CASE WHEN mo.rateGiven=1 THEN 1 ELSE 0 END) AS awfulOrders,
				SUM(CASE WHEN mo.rateGiven=2 THEN 1 ELSE 0 END) AS poorOrders
			FROM members_orders mo
			INNER JOIN employee_details ed ON mo.acceptedBy = ed.employeeId
			WHERE $dateCondition
			GROUP BY mo.acceptedBy, ed.fullName
			ORDER BY totalDone DESC";
			
			$result = dbQuery($query);
			if(mysqli_num_rows($result))
			{
				// Build print URL based on search type
				if($searchType == 'monthwise'){
					$printUrl = SITE_URL_EMPLOYEES."/print-view-employee-monthly-sheet.php?searchType=monthwise&month=".$month."&year=".$year;
				} else {
					$printUrl = SITE_URL_EMPLOYEES."/print-view-employee-monthly-sheet.php?searchType=datewise&dateFrom=".$dateFrom."&dateTo=".$dateTo;
				}
		?>
		<!--<tr>
			<td colspan="8" class="textstyle3"><a onclick="openPrintExcelWindow('<?php echo $printUrl;?>')" class='link_style9' style="cursor:pointer;"><b>Download This Data in Excel</b></a></td>
		</tr>-->
		<?php
				while($row = mysqli_fetch_assoc($result)){
					$employeeId				=	$row['acceptedBy'];
					$fullName 				=	$row['fullName'];
					$totalDone				=	$row['totalDone'];
					$rushOrders 			=	$row['rushOrders'];
					$all12HoursOrders 		=	$row['all12HoursOrders'];
					$all24HoursOrders 		=	$row['all24HoursOrders'];
					$awfulOrders 			=	$row['awfulOrders'];
					$poorOrders 			=	$row['poorOrders'];
		?>
			<tr>
				<td class="textstyle2"><?php echo $fullName;?></td>
				<td class="textstyle2"><?php echo $totalDone;?></td>
				<td class="textstyle2"><?php echo $rushOrders;?></td>
				<td class="textstyle2"><?php echo $all12HoursOrders;?></td>
				<td class="textstyle2"><?php echo $all24HoursOrders;?></td>
				<td class="textstyle2"><?php echo $awfulOrders;?></td>
				<td class="textstyle2"><?php echo $poorOrders;?></td>
			</tr>
		<?php
				}
			}
		?>
	</table>
<?php	
	}
	include(SITE_ROOT_EMPLOYEES		.   "/includes/bottom.php");
?>