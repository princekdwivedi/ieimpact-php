<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES     .   "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/check-admin-login.php");
	include(SITE_ROOT_EMPLOYEES		.	"/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/common-array.php");
	include(SITE_ROOT				.	"/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/set-variables.php");
	$a_existingHide					=	array();
	$serachDateTime					=	"";

	$query							=	"SELECT * FROM hide_eta_timings ORDER BY dayId";
	$result							=	dbQuery($query);
	if(mysql_num_rows($result)){
		while($row					=	mysql_fetch_assoc($result)){
			$t_dayId				=	$row['dayId'];
			$etaType				=	$row['etaType'];
			
			for($hrs=0;$hrs<=23;$hrs++){

				$displayHrs			=	$hrs;
				if($hrs < 10){
					$displayHrs		=	"0".$hrs;
				}
				$displayHrs		    =   $displayHrs."Hrs";

				$a_existingHide[$etaType][$t_dayId][$displayHrs] = $row[$displayHrs];
			}

			
		}
	}
	//pr($a_existingHide);

	if(isset($_GET['serachDateTime'])){
		$serachDateTime			=	$_GET['serachDateTime'];

		list($current_date,$current_time) = explode(" ",$serachDateTime);
		list($current_hrs,$current_min)	=	explode(":",$current_time);
		$timestamp	= strtotime($current_date);
		$day		= date("l", $timestamp);
		$day_id		= 0;

		$x_hrs		= array();
		$y_hrs     = array();
		$z_hrs		= array();

		if(in_array($day,$a_weekDaysText)){
			$day_id = array_search($day, $a_weekDaysText); 

			if(!empty($a_existingHide) && count($a_existingHide) > 0){
				
				if(array_key_exists(0,$a_existingHide)){
					$twelve_hrs		=	$a_existingHide[0];
					if(array_key_exists($day_id,$twelve_hrs)){
						$twelve_hrs_sel		=	$twelve_hrs[$day_id];
						if(!empty($twelve_hrs_sel) && count($twelve_hrs_sel) > 0){
							foreach($twelve_hrs_sel as $kk=>$vv){
								if(!empty($vv)){
									$x_hrs[]	=	$kk;
								}
							}
						}
					}
				}

				if(array_key_exists(1,$a_existingHide)){
					$rush_hrs		=	$a_existingHide[1];
					if(array_key_exists($day_id,$rush_hrs)){
						$rush_hrs_sel		=	$rush_hrs[$day_id];
						if(!empty($rush_hrs_sel) && count($rush_hrs_sel) > 0){
							foreach($rush_hrs_sel as $kk1=>$vv1){
								if(!empty($vv1)){
									$y_hrs[]	=	$kk1;
								}
							}
						}
					}
				}

				if(array_key_exists(2,$a_existingHide)){
					$twentyfour_hrs		=	$a_existingHide[2];
					if(array_key_exists($day_id,$twentyfour_hrs)){
						$twentyfour_hrs_sel		=	$twentyfour_hrs[$day_id];
						if(!empty($twentyfour_hrs_sel) && count($twentyfour_hrs_sel) > 0){
							foreach($twentyfour_hrs_sel as $kk2=>$vv2){
								if(!empty($vv2)){
									$z_hrs[]	=	$kk2;
								}
							}
						}
					}
				}
			}
		}
		//pr($x_hrs);
		//pr($y_hrs);
		//pr($z_hrs);
	}

?>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css1.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>
	<form name="testEta" action="" method="GET">
		<table width="98%" align="center" border="1" cellpadding="0" cellspacing="0">
			<tr>
				<td width="25%" class="smalltext24">Test With Selected IST Date & Time : </td>
				<td width="15%">
					<input type="text" name="serachDateTime" value="<?php echo $serachDateTime;?>" onclick="javascript:NewCssCal ('demo3','yyyyMMdd','arrow',true,'24');" id="demo3">
				</td>
				<td>
					<input type="submit" name="submit" value="Submit">
					<input type="hidden" name="formSubmitted" value="1">
				</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
			<?php
				foreach($a_estimatedTimeArray as $k=>$v)
				{
					$curreentlyAvailable	=	"CURRENTLY AVAILABLE";

					if($k					==	0)
					{									
						if(!empty($serachDateTime) && count($x_hrs) > 0){
							
							$check_hrs		=	$current_hrs."Hrs";
							if(in_array($check_hrs,$x_hrs)){
								$curreentlyAvailable	=	"<font color='#ff0000;'>CURRENTLY UN-AVAILABLE</font>";
							}
						}

					}
					elseif($k				==	1)
					{
						if(!empty($serachDateTime) && count($y_hrs) > 0){
							
							$check_hrs		=	$current_hrs."Hrs";
							if(in_array($check_hrs,$y_hrs)){
								$curreentlyAvailable	=	"<font color='#ff0000;'>CURRENTLY UN-AVAILABLE</font>";
							}
						}
					}
					elseif($k				==	2)
					{
						if(!empty($serachDateTime) && count($z_hrs) > 0){
							
							$check_hrs		=	$current_hrs."Hrs";
							if(in_array($check_hrs,$z_hrs)){
								$curreentlyAvailable	=	"<font color='#ff0000;'>CURRENTLY UN-AVAILABLE</font>";
							}
						}
					}
			?>
			<tr>
				<td class="smalltext24"><?php echo $v;?> </td>
				<td>
					<?php echo $curreentlyAvailable;?>
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
			<?php
				}
			?>
		</table>
	</form>
<?php
	

	include(SITE_ROOT_EMPLOYEES		.   "/includes/bottom.php");
?>