<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES     .   "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/check-admin-login.php");
	include(SITE_ROOT				.   "/classes/pagingclass.php");
	include(SITE_ROOT_EMPLOYEES		.	"/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/set-variables.php");
	$pagingObj						=	new Paging();
	

	$employeeId						=	0;
	$month							=	"";
	$year							=	"";
	$type							=	1;
	$a_types						=	array("1"=>"Process Files","2"=>"QA Done Files");
	$a_employeeList					=   array();
	$isDisplayDownloadLink			=	false;
	$employeeObj					=	new employee();
	$link							=	"";

	if($result						=   $employeeObj->getAllPdfEmployees()){
		while($row					=   mysqli_fetch_assoc($result)){
			$t_employeeId			=   $row['employeeId'];
			$t_firstName			=   stripslashes($row['firstName']);
			$t_lastName				=   stripslashes($row['lastName']);

			$a_employeeList[$t_employeeId] = $t_firstName." ".$t_lastName;
		}
	}
	
	
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		$link					=	"employeeId=".$employeeId."&month=".$month."&year=".$year."&type=".$type;

		$isDisplayDownloadLink	=	true;				
	}
?>
<script type = "text/javascript">
	function checkValid()
	{
		form1	=  document.searchEmployees;
		if(form1.month.value	==	0 || form1.year.value	==	0){
			alert("Please select month and year.");
			form1.month.focus();
			return false;
		}
	}
	function openPrintExcelWindow(pageUrl)
	{
		path = pageUrl;
		prop = "toolbar=no,scrollbars=yes,width=400,height=100,top=200,left=300";
		window.open(path,'',prop);
	}
</script>
<form name="searchEmployees" action="" method="GET" onsubmit="return checkValid();">
	<table width="100%" align="center" border="0" cellpadding="2" cellspacing="2">
		<tr>
			<td class="smalltext23" colspan="8">
				<b>PRINT EMPLOYEES MONTHWISE DONE ORDERS</b>
			</td>
		</tr>
		<tr>
			<td colspan="5" height="5"></td>
		</tr>
		<tr>
			<td width="7%" class="smalltext23">
				<b>Employee</b>
			</td>
			<td width="1%" class="smalltext23">
				<b>:</b>
			</td>
			<td class="smalltext23" width="20%">
				<select name="employeeId">
					<option value="0">All</option>
					<?php
						foreach($a_employeeList as $k=>$v){
							$select		= "";
							if($k		== $employeeId){
								$select	= "selected";
							}

							echo "<option value='$k' $select>$v</option>";
						}
					?>
				</select>
			</td>
			<td width="3%" class="smalltext23">
				<b>For</b>
			</td>
			<td width="1%" class="smalltext23">
				<b>:</b>
			</td>
			<td class="smalltext23" width="12%">
				<select name="month">
					<option value="0">Month</option>
					<?php
						foreach($a_month as $k=>$v){
							$select		= "";
							if($k		== $month){
								$select	= "selected";
							}

							echo "<option value='$k' $select>$v</option>";
						}
					?>
				</select>&nbsp;
				<select name="year">
					<option value="0">Year</option>
					<?php
						$cY   = date('Y');

						for($i=2013;$i<=$cY;$i++){
							$select		= "";
							if($year	== $i){
								$select	= "selected";
							}

							echo "<option value='$i' $select>$i</option>";
						}
					?>
				</select>
			</td>
			<td width="4%" class="smalltext23">
				<b>Type</b>
			</td>
			<td width="1%" class="smalltext23">
				<b>:</b>
			</td>
			<td class="smalltext23" width="10%">
				<select name="type">
					<?php
						foreach($a_types as $k=>$v){
							$select		= "";
							if($k		== $type){
								$select	= "selected";
							}

							echo "<option value='$k' $select>$v</option>";
						}
					?>
				</select>
			</td>
			<td>
				<input type="image" name="submit" src="<?php echo SITE_URL;?>/images/submit.jpg">
				<input type="hidden" name="formSubmitted" value="1">
			</td>
		</tr>
		<tr>
			<td colspan="5" height="25"></td>
		</tr>
		<?php
			if($isDisplayDownloadLink	== true)
			{
				$printUrl		=	SITE_URL_EMPLOYEES."/excel-employee-order-done-data.php?".$link;
		?>
		
		<tr>
			<td colspan="8" style='text-align:right' height="100">
				<a onclick="openPrintExcelWindow('<?php echo $printUrl;?>')" class='link_style9' style="cursor:pointer;"><b>Download This Data in Excel</b></a>
			</td>
		</tr>
		<?php
			}
			else{
		?>
		<tr>
			<td colspan="8" class="error2" height='200' align="right" style='text-align:right'><b>Please submit the form.</b></td>
		</tr>		
		<?php
			}
		?>
	</table>
</form>
<?php
	include(SITE_ROOT_EMPLOYEES		.   "/includes/bottom.php");
?>