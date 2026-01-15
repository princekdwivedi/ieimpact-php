<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES     .   "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/check-admin-login.php");
	include(SITE_ROOT_EMPLOYEES		.	"/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/common-array.php");
	include(SITE_ROOT				.	"/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/set-variables.php");

	$forFromDate 					=	date('d-m-Y');
	$t_forFromDate                  =   "";
	$toDate 						=	"";
	$t_toDate 						=	"";
	$timeZoneWise 					=	"";
	$citywise 				     	=	"";
	$showResult 					=	false;
	$searchTimeZoneState 			=	"";
	$errrorMsg 						=	"";

	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		//pr($_REQUEST);
		if(!empty($citywise) && !empty($timeZoneWise)){
			$errrorMsg 				=	"Please select either citywise or timezone wise.";
		}
		else{
			if(!empty($citywise) || !empty($timeZoneWise)){
				$showResult 		=	true;

				if(!empty($timeZoneWise)){
					$searchTimeZoneState = $a_timeZoneProvincesUSA[$timeZoneWise];
				}
			}
		}
		
	}
		

?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>

<form name="searchEmployeeMonthlyData" action="" method="POST">
	<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
		<tr>
			<td colspan="4" class="textstyle3"><b>GET CITYWISE/TIMEZONEWISE CUSTOMER ORDERS</b></td>
			<td colspan="9" class="error">
				<?php 
					if(!empty($errrorMsg)){
						echo "<b>".$errrorMsg."</b>";
					}
				?>
			</td>
		</tr>
		<tr>
			<td width="6%" class="smalltext23">CITY :</td>
			<td width="20%" >
				<select name="citywise" class="smalltext23">
					<?php
						foreach($a_usaProvinces as $k=>$v){
							$select		=	"";
							if($k		==  $citywise){
								$select	=	"selected";
							}

							list($city,$time)	=	explode("|",$v);

							if(!empty($time)){
								$time 	=	" (".$time.")";
							}
							else{
								$time   =   "";
							}
							
					?>
					<option value="<?php echo $k;?>" <?php echo $select;?>><?php echo $city.$time;?></option>
		
					<?php
						}
					?>
				</select>
			</td>
			<td width="8%" class="smalltext23">TIME ZONE :</td>
			<td width="8%" >
				<select name="timeZoneWise" class="smalltext23">
					<option value="">Select</option>
					<?php
						foreach($a_timeZoneStandardState as $k=>$v){
							$select		=	"";
							if($k		==  $timeZoneWise){
								$select	=	"selected";
							}							
					?>
							<option value="<?php echo $k;?>" <?php echo $select;?>><?php echo $k;?></option>		
					<?php
						}
					?>
				</select>
			</td>
			<td width="12%" class="smalltext23">FROM/FOR DATE :</td>
			<td width="12%" >
				<input type="text" name="forFromDate" value="<?php echo $forFromDate;?>" id="date1" size="8" readonly style="border:1px solid #4d4d4d;height:15px;font-size:15px;">&nbsp;&nbsp;<a href="javascript:NewCssCal('date1','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
			</td>
			<td width="7%" class="smalltext23">TO DATE :</td>
			<td width="12%" >
				<input type="text" name="toDate" value="<?php echo $toDate;?>" id="date3" size="8" readonly style="border:1px solid #4d4d4d;height:15px;font-size:15px;">&nbsp;&nbsp;<a href="javascript:NewCssCal('date3','ddmmyyyy')"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a>
			</td>
			<td>
				<input type='image' name='submit' src='<?php echo SITE_URL;?>/images/submit.jpg'>
				<input type='hidden' value='1' name='formSubmitted'>
			</td>
		</tr>		
	</table>	
</form>
<?php
	if($showResult            ==	true){
		$andCaluse            = 	"";
		$allMmebers 	      =     array();
		$allMembersDetails    =	    array();
		if(!empty($citywise)){
			$andCaluse        =	" AND state='$citywise'";
		}
		if(!empty($searchTimeZoneState)){
			$andCaluse        =	" AND state IN ($searchTimeZoneState)";
		}

		if(!empty($andCaluse)){
			$query 			  =	"SELECT memberId,completeName,state FROM members WHERE isActiveCustomer=1 AND isJunkMember=0".$andCaluse." ORDER BY completeName";
		
			$result			  =	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				while($row			             =	  mysqli_fetch_assoc($result)){
					$memberId 	                 =	  $row['memberId'];
					$completeName 				 =	  stripslashes($row['completeName']);
					$state 	        			 =	  $row['state'];

					$allMembers[$memberId] 	      =    $memberId;
					$allMembersDetails[$memberId] =	   $completeName."|".$state;
				}
?>
				<table width="98%" align="center" border="0" cellpadding="3" cellspacing="3">	
					<tr>
						<td width="5%" class="textstyle3">SR NO</td>
						<td width="20%" class="textstyle3">CUSTOMER NAME</td>
						<td width="15%" class="textstyle3">STATE</td>
						<td width="15%" class="textstyle3">TIME ZONE</td>
						<td class="textstyle3">TOTAL ORDERS</td>
					</tr>
					<tr>
						<td colspan="6">
							<hr size=1px solid #bebebe;>
						</td>
					</tr>
<?php
					list($d,$m,$y)  =   explode("-",$forFromDate);
					$t_forFromDate  =   $y."-".$m."-".$d;
					$dateCaluse		=	" AND orderAddedOn='".$t_forFromDate."'";
					if(!empty($toDate)){
						list($td,$tm,$ty)  =   explode("-",$toDate);
						$t_toDate	=	$ty."-".$tm."-".$td;

						$dateCaluse	=	" AND orderAddedOn >= '".$t_forFromDate."' AND orderAddedOn <= '".$t_toDate."'";
					}

					$allMembers 		=	implode(",",$allMembers);
					$query1 	    =	"SELECT COUNT(*) as total, memberId FROM members_orders WHERE memberId IN ($allMembers)".$dateCaluse." GROUP BY memberId ORDER BY total DESC";
					
					$result1	    =	dbQuery($query1);
					if(mysqli_num_rows($result1))
					{
						$count 		        =	0;
						while($row1			=	mysqli_fetch_assoc($result1)){
							$count++;
							$n_memberId 	=	$row1['memberId'];
							$total 	        =	$row1['total'];
							$memberDetails 	=	$allMembersDetails[$n_memberId];
							list($completeName,$state) = explode("|",$memberDetails);

							$stateFullNameZone =		$a_usaProvinces[$state];
							list($stateFullname,$zone) = explode("|",$stateFullNameZone);
					?>
					<tr>
						<td class="smalltest22">&nbsp;<?php echo $count;?>) </td>
						<td class="smalltest22"><?php echo $completeName;?></td>
						<td class="smalltest22"><?php echo $stateFullname;?></td>
						<td class="smalltest22"><?php echo $zone;?></td>
						<td class="smalltest22"><?php echo $total;?></td>
					</tr>
					<?php
							
						}
					}
					else{
						echo "<tr><td align='center' class='error2' colspan='8'><b>NO RECORD FOUND.</b></td></tr>";
					}

				}
				else{
					echo "<table width='70%' border='0' align='center' height='300'><tr><td align='center' class='error2'><b>NO RECORD FOUND.</b></td></tr></table>";
				}
		}
		else{
			echo "<table width='70%' border='0' align='center' height='300'><tr><td align='center' class='error2'><b>NO RECORD FOUND.</b></td></tr></table>";
		}	
	}
	else{
		echo "<table width='70%' border='0' align='center' height='300'><tr><td align='center' class='error2'><b>PLEASE SUBMIT THE ABOVE FORM , SELECT AT LEAST CITY or TIMEZONE.</b></td></tr></table>";
	}
	include(SITE_ROOT_EMPLOYEES		.   "/includes/bottom.php");
?>