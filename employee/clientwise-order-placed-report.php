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

	$main_admins 	  =	[];
	$main_admins[3]   =   3;
	$main_admins[137] =   137;
	$main_admins[340] =   340;

	if(!in_array($s_employeeId, $main_admins)){
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	// Set default month and year to current month and year
	$currentMonth 					=	date('m');
	$currentYear 					=	date('Y');
	
	$month 							=	$currentMonth;
	$year 							=	$currentYear;
	$displayText					=	"";
	$dateCondition					=	"";

	if(isset($_GET['month']) && isset($_GET['year'])){
		$month 					=	(int)$_GET['month'];
		$year 					=	(int)$_GET['year'];
	}
	
	// If month and year are selected, build the condition and display text
	if(!empty($month) && !empty($year)){
		// Pad month with leading zero if needed
		$monthPadded			=	str_pad($month, 2, '0', STR_PAD_LEFT);
		$dateCondition			=	" AND DATE_FORMAT(mo.orderAddedOn, '%Y-%m') = '$year-$monthPadded'";
		$displayText			=	$a_month[$monthPadded].", ".$year;
	}
?>
<style>
	table {
		font-family: Arial, sans-serif;
	}
	.report-header {
		background: linear-gradient(to bottom, #f0f0f0 0%, #e0e0e0 100%);
		padding: 10px;
		font-weight: bold;
	}
</style>
<script type="text/javascript">
	function validateForm() {
		var month = document.forms["searchEmployeeMonthlyData"]["month"].value;
		var year = document.forms["searchEmployeeMonthlyData"]["year"].value;
		
		if(month == "0" || month == "") {
			alert("Please select a month");
			return false;
		}
		
		if(year == "0" || year == "") {
			alert("Please select a year");
			return false;
		}
		
		return true;
	}
</script>
<form name="searchEmployeeMonthlyData" action="" method="GET" onsubmit="return validateForm();">
	<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
		<tr>
			<td colspan="8" class="textstyle3"><b>CLIENT MONTHLY ORDERS</b></td>
		</tr>
		<tr>
			<td width="12%" class="smalltext23"><b>SELECT MONTH :</b></td>
			<td width="15%">
				<select name="month" class="smalltext23" required>
					<option value="0">Select Month</option>
					<?php
						foreach($a_month as $k=>$v){
							$select		=	"";
							// Convert month to same format for comparison
							$monthPadded = str_pad($month, 2, '0', STR_PAD_LEFT);
							if($k == $monthPadded){
								$select	=	"selected";
							}
					?>
					<option value="<?php echo $k;?>" <?php echo $select;?>><?php echo $v;?></option>
				<?php
					}
				?>
				</select>
			</td>
			<td width="2%">&nbsp;</td>
			<td width="12%" class="smalltext23"><b>SELECT YEAR :</b></td>
			<td width="15%">
				<select name="year" class="smalltext23" required>
					<option value="0">Select Year</option>
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
			<td width="2%">&nbsp;</td>
			<td>
				<input type='image' name='submit' src='<?php echo SITE_URL;?>/images/submit.jpg'>
				<input type='hidden' value='1' name='formSubmitted'>
			</td>
		</tr>	
	</table>	
</form>
<br />
<?php
	// Initialize variables for report
	$reportData = array();
	$totalOrdersOverall = 0;
	
	if(!empty($month) && !empty($year)){
		// Optimized query with JOIN to get customer order data
		// Using indexed columns (memberId) for better performance
		$query = "SELECT 
					m.memberId,
					m.completeName,
					m.appraisalSoftwareType,
					COUNT(mo.orderId) as totalOrders
				FROM members m
				INNER JOIN members_orders mo ON m.memberId = mo.memberId
				WHERE 1=1 $dateCondition
				GROUP BY m.memberId, m.completeName, m.appraisalSoftwareType
				ORDER BY totalOrders DESC, m.completeName ASC";
		
		$result = dbQuery($query);
		
		if($result && $result->num_rows > 0){
			while($row = $result->fetch_assoc()){
				$reportData[] = $row;
				$totalOrdersOverall += $row['totalOrders'];
			}
		}
	}
?>

<?php if(!empty($displayText)): ?>
<table width="98%" border="1" align="center" cellpadding="4" cellspacing="0" style="border-collapse: collapse; border: 1px solid #cccccc;">
	<tr style="background-color: #f0f0f0;">
		<td colspan="4" class="textstyle3" align="center">
			<b>CLIENT WISE ORDER REPORT FOR <?php echo strtoupper($displayText); ?></b>
		</td>
	</tr>
	<tr style="background-color: #e0e0e0;">
		<td width="5%" class="smalltext23" align="center"><b>S.No</b></td>
		<td width="40%" class="smalltext23" align="center"><b>Customer Name</b></td>
		<td width="35%" class="smalltext23" align="center"><b>Appraisal Software Type</b></td>
		<td width="20%" class="smalltext23" align="center"><b>Total Orders</b></td>
	</tr>
	<?php 
		if(count($reportData) > 0){
			$serialNo = 1;
			foreach($reportData as $data){
				$customerName = stripslashes($data['completeName']);
				$customerName = ucwords(strtolower($customerName));
				
				// Get appraisal software type text from array
				$appraisalSoftwareType = $data['appraisalSoftwareType'];
				$appraisalSoftwareText = "N/A";
				
				if(isset($a_appraisalSoftwareRegPage[$appraisalSoftwareType])){
					$appraisalSoftwareText = $a_appraisalSoftwareRegPage[$appraisalSoftwareType];
				}
				
				$totalOrders = $data['totalOrders'];
				
				// Alternate row colors
				$rowColor = ($serialNo % 2 == 0) ? "#ffffff" : "#f9f9f9";
	?>
	<tr style="background-color: <?php echo $rowColor; ?>;">
		<td class="smalltext23" align="center"><?php echo $serialNo; ?></td>
		<td class="smalltext23"><?php echo $customerName; ?></td>
		<td class="smalltext23" align="center"><?php echo $appraisalSoftwareText; ?></td>
		<td class="smalltext23" align="center"><b><?php echo $totalOrders; ?></b></td>
	</tr>
	<?php 
				$serialNo++;
			}
	?>
	<tr style="background-color: #e0e0e0; font-weight: bold;">
		<td colspan="3" class="smalltext23" align="right"><b>TOTAL ORDERS:</b></td>
		<td class="smalltext23" align="center"><b><?php echo $totalOrdersOverall; ?></b></td>
	</tr>
	<?php 
		} else {
	?>
	<tr>
		<td colspan="4" class="smalltext23" align="center" style="color: #ff0000;">
			<b>No orders found for the selected month and year.</b>
		</td>
	</tr>
	<?php 
		}
	?>
</table>
<?php endif; ?>
<br />
<?php
	include(SITE_ROOT_EMPLOYEES		.   "/includes/bottom.php");
?>