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

	$placedOrders				=	"1";
	$inLastDays					=	"14";
	$zeroOrdersIn				=	"7";
	$isCheckedCallingList		=	0;
	$checkCalling				=	"";
	$currentDateIndia			=	CURRENT_DATE_INDIA;
	$notInClause				=	"";
	$showingOfLastCalled		=	7;
	

	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);

		if(isset($_POST['isCheckedCallingList']))
		{
			$isCheckedCallingList	=	$_POST['isCheckedCallingList'];
		}
		else
		{
			$isCheckedCallingList	=	0;
		}


		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/gone-customers.php?placedOrders=".$placedOrders."&inLastDays=".$inLastDays."&zeroOrdersIn=".$zeroOrdersIn."&isCheckedCallingList=".$isCheckedCallingList."&showingOfLastCalled=".$showingOfLastCalled);
		exit();
	}

	if(isset($_GET['placedOrders']) && $_GET['placedOrders'] != 0 && $_GET['placedOrders'] != "")
	{
		$placedOrders	=	$_GET['placedOrders'];
	}
	if(isset($_GET['inLastDays']) && $_GET['inLastDays'] != 0 && $_GET['inLastDays'] != "")
	{
		$inLastDays		=	$_GET['inLastDays'];
	}
	if(isset($_GET['zeroOrdersIn']) && $_GET['zeroOrdersIn'] != 0 && $_GET['zeroOrdersIn'] != "")
	{
		$zeroOrdersIn			=	$_GET['zeroOrdersIn'];
	}
	if(isset($_GET['showingOfLastCalled']) && $_GET['showingOfLastCalled'] != 0 && $_GET['showingOfLastCalled'] != "")
	{
		$showingOfLastCalled	=	$_GET['showingOfLastCalled'];
	}
	if(isset($_GET['isCheckedCallingList']) && $_GET['isCheckedCallingList'] != 0)
	{
		$isCheckedCallingList	=	$_GET['isCheckedCallingList'];
		$checkCalling			=	"checked";
		$olderDate				=	getPreviousGivenDate($nowDateIndia,$showingOfLastCalled);
		$notInClause			=	" AND lastCalledOn < '$olderDate'";
	}

	$inLastDaysDate				=	getPreviousGivenDate($currentDateIndia,$inLastDays);
	$zeroOrdersInDate			=	getPreviousGivenDate($currentDateIndia,$zeroOrdersIn);

	$fromLimit					=	0;
	$previousShow				=	0;
	$nextShow					=	3;
	$previousLimit				=	0;
	if(isset($_GET['nxl']))
	{
		$nxl			        =	$_GET['nxl'];
		if($nxl		  		   !=	0)
		{
			$fromLimit			=	$nxl;
			$previousShow		=	$nxl-3;
			$nextShow			=	$nxl+3;
		}
	}
	if(isset($_GET['nxl1']))
	{
		$nxl1					=	$_GET['nxl1'];
		if($nxl1		  	   !=	0)
		{
			$fromLimit			=	$nxl1;
			$previousShow		=	$nxl1-3;
			$nextShow			=	$nxl1+3;
		}
	}
	if($fromLimit == 0)
	{
		$previousLimit			=	-1;
	}

	$a_totalCustomersPlacedCust	=	array();
	$totalCustomersPlacedCust	=	"";
	$andMemberInClause			=	"";
	
	$query						=	"SELECT COUNT(orderId) as TotalOrders,memberId FROM members_orders WHERE orderAddedOn >= '$inLastDaysDate' GROUP BY memberId HAVING TotalOrders > $placedOrders";
	$result						=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row			=	mysqli_fetch_assoc($result))
		{
			$memberId		=	$row['memberId'];
			$orders			=	$row['TotalOrders'];
		
			$a_totalCustomersPlacedCust[$memberId]	=	$memberId;
		}
		$totalCustomersPlacedCust	=	implode(",",$a_totalCustomersPlacedCust);
		$andMemberInClause			=	" AND memberId IN ($totalCustomersPlacedCust)";
	}
?>
<script type="text/javascript">
	function checkForNumber()
	{
		k = (document.all)?event.keyCode : arguments.callee.caller.arguments[0].which;
		if(k == 8 || k== 0)
		{
			return true;
		}
		if(k >= 48 && k <= 57 )
		{
			return true;
		}
		else if(k == 46)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	function checkValidSearchGone()
	{
		form1	=	document.serachViewGoneGone;
		if(form1.showingOfLastCalled.value	==	"" || form1.showingOfLastCalled.value	==	"0")
		{
			alert("Please enter called in days.");
			form1.showingOfLastCalled.focus();
			return false;
		}
		
	}	
</script>


<form name="serachViewGoneGone" action="" method="POST" onsubmit="return checkValidSearchGone();">
	<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
		<tr>
			<td class="textstyle3" colspan="8">
				<b>LOST CUSTOMERS COUNT LAST TWO ORDERS DONE BY EMPLOYEES</b>
			</td>
		</tr>
		<tr>
			<td height="5" colspan="8"></td>
		</tr>
		<tr>
			<td width="16%" class="smalltext18">CUSTOMERS PLACED MORE THAN : </td>
			<td width="8%" class="smalltext18">
				<input type="text" name="placedOrders" value="<?php echo $placedOrders;?>" style="width:24px;" maxlength="7" onKeyPress="return checkForNumber()"> ORDER
			</td>
			<td width="30%" class="smalltext18">
				IN LAST&nbsp;
				<input type="text" name="inLastDays" value="<?php echo $inLastDays;?>" style="width:26px;" maxlength="3" onKeyPress="return checkForNumber()"> DAYS BUT <font color="#ff0000">ZERO</font> ORDERS IN LAST <input type="text" name="zeroOrdersIn" value="<?php echo $zeroOrdersIn;?>" style="width:24px;" maxlength="7" onKeyPress="return checkForNumber()"> DYS
			</td>
			<td>
				<input type="image" name="name" src="<?php echo SITE_URL;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
   </table>
</form>
<br>
<?php
	
	if(!empty($a_totalCustomersPlacedCust))
	{
		$query		=	"SELECT memberId,completeName,firstName,lastName,companyName,addedOn,lastCalledOn,totalOrdersPlaced,lastOrderAddedOn FROM members WHERE lastOrderAddedOn < '$zeroOrdersInDate'".$andMemberInClause." ORDER BY totalOrdersPlaced DESC";
		$result		=	dbQuery($query);
		if(mysqli_num_rows($result))
		{

			$a_totalDoneProcessed	=	array();
			$a_totalDoneEmployees	=	array();

?>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td class="textstyle3" colspan="8">
			<b>CUSTOMERS LIST</b>
		</td>
	</tr>
	<!--<tr height='25' class='rwcolor'>
		<td width="4%" class="smalltext12">&nbsp;</td>
		<td width="22%" class="smalltext12">Customer</td>
		<td width="11%" class="smalltext12">Total Orders</td>
		<td width="11%" class="smalltext12">From <?php echo showDate($inLastDaysDate);?></b></td>
		<td class="smalltext12" width="9%">Last Order On</td>
		<td class="smalltext12" width="9%">Register On</td>
		<td class="smalltext12">Last Two Orders Done</td>
	</tr>-->
	<tr>
<?php			
			$k	=	0;
			$l	=	0;
			while($row					=	mysqli_fetch_assoc($result))
			{
				$k++;
				$l++;
				$memberId				=	$row['memberId'];
				$firstName		        =	stripslashes($row['firstName']);
			    $lastName		        =	stripslashes($row['lastName']);
			    $completeName	        =	$firstName." ".substr($lastName, 0, 1);
				$lastOrderAddedOn		=	$row['lastOrderAddedOn'];
				$registredOn			=	showDate($row['addedOn']);
				$totalOrdersPlaced		=	$row['totalOrdersPlaced'];

				if($lastOrderAddedOn   !=	"0000-00-00")
				{
					$lastOrderAddedOn	=	showDate($lastOrderAddedOn);
				}
				else
				{
					$lastOrderAddedOn	=	"-";
				}
				
				$lastCalledOn			=	$row['lastCalledOn'];
				if($lastCalledOn	   !=	"0000-00-00")
				{
					$lastCalledOn		=	showDate($lastCalledOn);
				}
				else
				{
					$lastCalledOn		=	"-";
				}
				$companyName			=	stripslashes($row['companyName']);
				$totalOrders			=	$row['totalOrdersPlaced'];
				
				$bgColor				=	"class='rwcolor1'";
				if($k%2==0)
				{
					$bgColor			=	"class='rwcolor2'";
				}

				$totlOrderPlacedFrom	=	$employeeObj->getSingleQueryResult("SELECT COUNT(*) as total FROM members_orders WHERE memberId=$memberId AND orderAddedOn >= '$inLastDaysDate'","total");
				if(empty($totlOrderPlacedFrom))
				{
					$totlOrderPlacedFrom=	0;
				}

				$customerLinkStyle		=	"link_style16";
				
				if(empty($totalOrders))
				{
					$totalOrders=	0;
				}
				if($totalOrders <= 3)
				{
					$customerOrderText	=	"(New Cus.)";
					$customerLinkStyle	=	"link_style17";
				}
				elseif($totalOrders > 3 && $totalOrders <= 7)
				{
					$customerOrderText	=	"(Trial Cus.)";
					$customerLinkStyle	=	"link_style18";
				}
				elseif($totalOrders >= 100 && $totalOrders < 350)
				{
					$customerOrderText	=	"(Big Cus.)";
					$customerLinkStyle	=	"link_style20";
				}
				elseif($totalOrders >= 350 && $totalOrders < 700)
				{
					$customerOrderText	=	"(VIP Cus.)";
					$customerLinkStyle	=	"link_style21";
				}
				elseif($totalOrders >= 700)
				{
					$customerOrderText	=	"(VVIP Cus.)";
					$customerLinkStyle	=	"link_style22";
				}

				$a_lastOrdersDoneBy		=	array();

				$query1					=	"SELECT orderAddress,acceeptedByName,acceptedBy,rateGiven FROM members_orders WHERE memberId=$memberId AND status IN (2,5,6) ORDER BY orderId DESC limit 2";
				$result1				=	dbQuery($query1);
				if(mysqli_num_rows($result1)){
					while($row1			=	mysqli_fetch_assoc($result1)){
						$acceptedBy				=	$row1['acceptedBy'];
						$rateGiven				=	$row1['rateGiven'];
						$a_lastOrdersDoneBy[]	=	$row1['acceeptedByName']."<=>".$rateGiven;
						$a_totalDoneEmployees[$acceptedBy]  =   $row1['acceeptedByName'];
						
						if(array_key_exists($acceptedBy,$a_totalDoneProcessed)){
							$totalCompleted		=	$a_totalDoneProcessed[$acceptedBy]+1;
						}
						else{
							$totalCompleted		=	1;
						}
						$a_totalDoneProcessed[$acceptedBy] = $totalCompleted;	
					}
				}
		?>
		
		<td width="33%" class="smalltext2">
			<?php
				echo $l.")&nbsp;<a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=$memberId' class='$customerLinkStyle' style='cursor:pointer;'>$completeName</a> - ";
				$countDone	=	0;
				if(!empty($a_lastOrdersDoneBy) && count($a_lastOrdersDoneBy) > 0){
					foreach($a_lastOrdersDoneBy as $kk=>$vv){
						$countDone++;
						list($doneBy,$rating)	=	explode("<=>",$vv);

						$coma	   =	"";
						if($countDone < 2){
							$coma	=	", &nbsp;";
						}						
						echo $doneBy.$coma;

					}
				}	
			?>
			
		</td>
		<?php
			if($k == 3){
				echo "</tr><tr><td colspan='5' height='5'></td></tr><tr>";

				$k = 0;
			}	
		?>
		<!--<tr height='20' <?php echo $bgColor;?>>
			<td valign="top" class="smalltext2">&nbsp;<?php echo $k;?>)</td>
			<td valign="top" class="<?php echo $customerLinkStyle;?>">
				<?php
					echo "<a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=$memberId' class='$customerLinkStyle' style='cursor:pointer;'>$completeName</a>";
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo $totalOrders;
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo $totlOrderPlacedFrom;
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo $lastOrderAddedOn;
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					echo $registredOn;
				?>
			</td>
			<td class="smalltext2" valign="top">
				<?php
					$countDone	=	0;
					if(!empty($a_lastOrdersDoneBy) && count($a_lastOrdersDoneBy) > 0){
						foreach($a_lastOrdersDoneBy as $kk=>$vv){
							$countDone++;
							list($doneBy,$rating)	=	explode("<=>",$vv);

							$coma	   =	"";
							if($countDone < 2){
								$coma	=	", &nbsp;";
							}

							$ratingImage=	"";
							if(!empty($rating)){
								$ratingImage= "&nbsp;(<img src=".SITE_URL."/images/rating/".$rating.".png>)";
							}
							echo $doneBy.$ratingImage.$coma;

						}
					}	
				?>
			</td>
		</tr>-->
	
	<?php
			}
			for($i=$k;$i<=3;$i++){
				echo "<td>&nbsp;</td>";
			}
	echo "</tr>";
			arsort($a_totalDoneProcessed);
			if(!empty($a_totalDoneProcessed) && count($a_totalDoneProcessed) > 0){
	?>
	<tr>
		<td height="10" colspan="8"></td>
	</tr>
	<tr>
		<td class="textstyle2" colspan="7"><b>Total per employee:</b></td>
	</tr>
	<tr>
		<td height="5" colspan="8"></td>
	</tr>
	<tr>
		<?php
			$count1	=	0;
			$count2	=	0;
			foreach($a_totalDoneProcessed as $employeeId=>$totalDone){
				$count1++;
				$count2++;

				echo "<td width='20%' class='.smalltext23'>".$count2.")&nbsp;".$a_totalDoneEmployees[$employeeId]." - ".$totalDone."</td>";

				if($count1 == 3){
					echo "</tr><tr><td colspan='5' height='5'></td></tr><tr>";

					$count1 = 0;
				}
				
			}
			for($i=$count1;$i<=3;$i++){
				echo "<td>&nbsp;</td>";
			}
		?>
	</tr>
	<?php
			}

			echo "</table>";
		}
	}
	else
	{
		echo "<br><center><font class='error'><b>Sorry No Record Found.</b></font></center>";
	}

	

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>