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
	include(SITE_ROOT_EMPLOYEES	.   "/classes/orders.php");
	$orderObj					=   new orders();
	$employeeObj				=	new employee();
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	$form 						=	SITE_ROOT_EMPLOYEES."/forms/get-clientwise-employee-processed.php";
	
	if(empty($s_hasManagerAccess) || empty($s_hasAdminAccess))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	$selectMonth 	    =	$t_selectMonth = date('m');
	$selectYear 	    =	date('Y');

	$all_employees 	    =	array();
	$all_customers 	    =	array();
	$selectedCustomer	=	0;
	$selctedEmployee	=	0;
	$searchFor 			=	1;//1 for processed and 2 for file checked
	$search_type 		=	array("1"=>"Processed","2"=>"Checked");
	$showResult 		=	false;


	
	///////////////////////GET ALL ACTIVE PDF EMPLOYEES //////////////////
	$allEmployees 		=	$employeeObj->getAllPdfEmployees();
	if(mysqli_num_rows($allEmployees)){
		while($row		=	mysqli_fetch_assoc($allEmployees)){
			$employeeId =	$row['employeeId'];
			$firstName  =	stripslashes($row['firstName']);
			$lastName   =	stripslashes($row['lastName']);

			$all_employees[$employeeId] = $firstName." ".$lastName;
		}
	}

	///////////////////////GET ALL ACTIVE CUSTOMERS //////////////////
	$all_customers 		    =	array();

	$all_customersTimings	=	array();
	$query				    =	"SELECT completeName,memberId,averageTimeTaken FROM members WHERE isJunkMember=0 AND isActiveCustomer=1 ORDER BY firstName";
	$result		=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row				=	mysqli_fetch_assoc($result))
		{
			$t_memberId			=	$row['memberId'];
			$averageTimeTaken   =	$row['averageTimeTaken'];
			$completeName		=	stripslashes($row['completeName']);

			$all_customers[$t_memberId]	      =	$completeName;
			$all_customersTimings[$t_memberId]=	$averageTimeTaken;
		}
	}

	if(isset($_GET['selectedCustomer'])){
		$selectedCustomer=	(int)$_GET['selectedCustomer'];
	}
	if(isset($_GET['selctedEmployee'])){
		$selctedEmployee =	(int)$_GET['selctedEmployee'];
	}
	if(isset($_GET['searchFor'])){
		$searchFor       =	(int)$_GET['searchFor'];
	}
	if(isset($_GET['selectMonth'])){
		$selectMonth       =	$t_selectMonth = (int)$_GET['selectMonth'];
		if($selectMonth < 10  && strlen($selectMonth) < 2){
			$t_selectMonth =  "0".$selectMonth;
		}
	}
	if(isset($_GET['selectYear'])){
		$selectYear      =	(int)$_GET['selectYear'];
	}
	

	if(!empty($selectedCustomer) || !empty($selctedEmployee)){
		$showResult     =	true;		
	}
	

	include($form);

?>
<br />
<table width="70%" align="center" border="0" cellpadding="3" cellspacing="3">
	<?php
		if($showResult){
			$resultText 	=	"FILE PROCESSED";
			if($searchFor   == 2){
				$resultText =	"FILE CHECKED";
			}


	?>
		<tr>
			<td width="30%" class="textstyle3">CUSTOMER</td>
			<td width="30%" class="textstyle3">EMPLOYEE</td>
			<td class="textstyle3">TOTAL <?php echo $resultText." ON ".$a_month[$t_selectMonth].",".$selectYear?></td>
		</tr>
	<?php
			if(!empty($selectedCustomer) && !empty($selctedEmployee)){
				///////////////////// GET SINGLE CUSTOMER SINGLE EMPLOYEES ORDER DONE ///////////
				$clientName    =  $all_customers[$selectedCustomer]." (".$all_customersTimings[$selectedCustomer]." Min)";
				$employeeName  =  $all_employees[$selctedEmployee];
				$t_employeeName=  makeDBSafe($employeeName);
				$totalDone 	   =  $employeeObj->getSingleQueryResult("SELECT COUNT(*) AS total FROM members_orders WHERE acceptedBy=$selctedEmployee AND memberId=$selectedCustomer AND status IN (2,4,5) AND MONTH(orderCompletedOn)=$t_selectMonth AND YEAR(orderCompletedOn)=$selectYear","total");

				if($searchFor   == 2){

					$totalDone 	=	$employeeObj->getSingleQueryResult("SELECT COUNT(*) AS total FROM members_orders WHERE orderCheckedBy='$t_employeeName' AND memberId=$selectedCustomer AND status IN (2,4,5) AND MONTH(orderCompletedOn)=$t_selectMonth AND YEAR(orderCompletedOn)=$selectYear","total");
				}
		?>
		<tr>
			<td class="textstyle2"><?php echo $clientName;?></td>
			<td class="textstyle2"><?php echo $employeeName;?></td>
			<td class="textstyle2"><?php echo $totalDone;?></td>
		</tr>
		<?php
			}
			elseif(!empty($selectedCustomer) && empty($selctedEmployee)){
				$clientName    =  $all_customers[$selectedCustomer]." (".$all_customersTimings[$selectedCustomer]." Min)";
				if($searchFor   == 1){
					///////////////////// GET SINGLE CUSTOMER ALL EMPLOYEES ORDER DONE ///////////
					$query = "SELECT COUNT(*) AS total,acceptedBy FROM members_orders WHERE memberId=$selectedCustomer AND status IN (2,4,5) AND MONTH(orderCompletedOn)=$t_selectMonth AND YEAR(orderCompletedOn)=$selectYear GROUP BY acceptedBy ORDER BY total DESC";

					$result		       =	dbQuery($query);
					if(mysqli_num_rows($result))
					{
						while($row	   =	mysqli_fetch_assoc($result))
						{
							$totalDone =   $row['total']; 
							$acceptedBy=   $row['acceptedBy']; 
				?>
				<tr>
					<td class="textstyle2"><?php echo $clientName;?></td>
					<td class="textstyle2"><?php echo $all_employees[$acceptedBy];?></td>
					<td class="textstyle2"><?php echo $totalDone;?></td>
				</tr>				
				<?php
							}
						}
						else{
							echo "<tr><td height='50' class='error' style='text-align:center;' colspan='4'><b>No Record found</b></td></tr>";
						}
					}
					else{
						///////////////////// GET SINGLE CUSTOMER ALL EMPLOYEES ORDER CHECKED ///////////
						$query = "SELECT COUNT(*) AS total,orderCheckedBy FROM members_orders WHERE memberId=$selectedCustomer AND status IN (2,4,5) AND MONTH(orderCompletedOn)=$t_selectMonth AND YEAR(orderCompletedOn)=$selectYear GROUP BY orderCheckedBy ORDER BY total DESC";

						$result		       =	dbQuery($query);
						if(mysqli_num_rows($result))
						{
							while($row	   =	mysqli_fetch_assoc($result))
							{
								$totalDone     =   $row['total']; 
								$orderCheckedBy=   $row['orderCheckedBy']; 
				?>
				<tr>
					<td class="textstyle2"><?php echo $clientName;?></td>
					<td class="textstyle2"><?php echo $orderCheckedBy;?></td>
					<td class="textstyle2"><?php echo $totalDone;?></td>
				</tr>				
				<?php
							}
						}
						else{
							echo "<tr><td height='50' class='error' style='text-align:center;' colspan='4'><b>No Record found</b></td></tr>";
						}
					}

			}
			elseif(empty($selectedCustomer) && !empty($selctedEmployee)){
				
				$employeeName  =  $all_employees[$selctedEmployee];
				$t_employeeName=  makeDBSafe($employeeName);
				if($searchFor   == 1){
					///////////////////// GET SINGLE EMPLOYEE ALL CUSTOMERS ORDER DONE ///////////
					$query = "SELECT COUNT(*) AS total,memberId FROM members_orders WHERE acceptedBy=$selctedEmployee AND status IN (2,4,5) AND MONTH(orderCompletedOn)=$t_selectMonth AND YEAR(orderCompletedOn)=$selectYear GROUP BY memberId ORDER BY total DESC";

					$result		       =	dbQuery($query);
					if(mysqli_num_rows($result))
					{
						while($row	   =	mysqli_fetch_assoc($result))
						{
							$totalDone =   $row['total']; 
							$memberId  =   $row['memberId']; 
				?>
				<tr>
					<td class="textstyle2"><?php echo $all_customers[$memberId]." (".$all_customersTimings[$memberId]." Min)";?></td>
					<td class="textstyle2"><?php echo $employeeName;?></td>
					<td class="textstyle2"><?php echo $totalDone;?></td>
				</tr>				
				<?php
							}
						}
						else{
							echo "<tr><td height='50' class='error' style='text-align:center;' colspan='4'><b>No Record found</b></td></tr>";
						}
					}
					else{
						///////////////////// GET SINGLE CUSTOMER ALL EMPLOYEES ORDER CHECKED ///////////
						$query = "SELECT COUNT(*) AS total,memberId FROM members_orders WHERE orderCheckedBy='$t_employeeName' AND status IN (2,4,5) AND MONTH(orderCompletedOn)=$t_selectMonth AND YEAR(orderCompletedOn)=$selectYear GROUP BY memberId ORDER BY total DESC";

						$result		       =	dbQuery($query);
						if(mysqli_num_rows($result))
						{
							while($row	   =	mysqli_fetch_assoc($result))
							{
								$totalDone     =   $row['total']; 
								$memberId=   $row['memberId']; 
				?>
				<tr>
					<td class="textstyle2"><?php echo $all_customers[$memberId]." (".$all_customersTimings[$memberId]." Min)";?></td>
					<td class="textstyle2"><?php echo $employeeName;?></td>
					<td class="textstyle2"><?php echo $totalDone;?></td>
				</tr>				
				<?php
						}
					}
					else{
						echo "<tr><td height='50' class='error' style='text-align:center;' colspan='4'><b>No Record found</b></td></tr>";
					}
				}

			}
		}
		else{
			echo "<tr><td height='300' class='error' style='text-align:center;'><b>Please Submit The Above Form</b></td></tr>";
		}
	?>
</table>
<?php

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>