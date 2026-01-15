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
	include(SITE_ROOT			    .   "/includes/send-mail.php");
	$employeeObj				    =   new employee();

	////////////////// CUSTOME MODIFICATION ////////////////////////////
	///ORIGINAL $a_estimatedTimeArray	=	array("2"=>"Deliver in 24 Hours","0"=>"Deliver in 12 Hours","1"=>"Deliver in 6 Hours");

	if(isset($_GET['changeTatTypeInto'])){

		$changeTatTypeInto 	=	$_GET['changeTatTypeInto'];//1 means allow employees to pick order TAT wise and 2 means allow employees to pick order from any page
		if(!empty($changeTatTypeInto)){
			dbQuery("UPDATE website_global_settings SET setting=$changeTatTypeInto WHERE type='PICK_FIRST_THIRTY_ORDERS_EMPLOYEE'");
		}		

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/show-hide-orders-eta.php");
		exit();
	}
?>

<script type="text/javascript">
	function changeTatOrderPickUp(change_to)
	{
		if(change_to == 1){
			var confirmation = window.confirm("Are you sure to allow employees to pick order TAT wise?");
		}
		else{
			var confirmation = window.confirm("Are you sure to allow employees to pick order from any page?");
		}
		
		if(confirmation == true)
		{
			window.location.href='<?php echo SITE_URL_EMPLOYEES;?>/show-hide-orders-eta.php?changeTatTypeInto='+change_to;
		}
	}
</script>
<?php

	$a_estimatedTimeArray	=	array("1"=>"Deliver in 12 Hours","2"=>"Deliver in 6 Hours");

	$thirtyOrdersEmployee 				=	$employeeObj->getSingleQueryResult("SELECT setting FROM website_global_settings WHERE type='PICK_FIRST_THIRTY_ORDERS_EMPLOYEE'","setting");//1 means Show Old login for single to both PDF and MT and 2 means new show sarate login for both MT and PDF

	$currentlyShowingThirtyOrdersEmployee=	"Currently allowing employees to accept any orders from any page. (<a onclick='changeTatOrderPickUp(1)' class='linkstyle5' style='cursor:pointer;'>Allow employees to pick order TAT wise</a>)";
	if($thirtyOrdersEmployee 			== 1){
		$currentlyShowingThirtyOrdersEmployee	=	"Currently allowing employees to accept orders according to the tat or ask manager to assign. (<a onclick='changeTatOrderPickUp(2)' class='linkstyle5' style='cursor:pointer;'>Allow employees to pick order from any page</a>)";
	}

	

	$errorMsg						=	"";
	$etaId							=	"";
	$a_existingHide					=	array();

	if(isset($_GET['etaId'])){
		$etaId						=	$_GET['etaId'];
		$mainEta					=	$etaId-1;

		$query						=	"SELECT * FROM hide_eta_timings WHERE etaType=$mainEta ORDER BY dayId";
		$result						=	dbQuery($query);
		if(mysqli_num_rows($result)){
			while($row				=	mysqli_fetch_assoc($result)){
				$t_dayId			=	$row['dayId'];
				
				for($hrs=0;$hrs<=23;$hrs++){

				    $displayHrs	    =	$hrs;
					if($hrs < 10){
						$displayHrs	=	"0".$hrs;
					}
					$displayHrs     =   $displayHrs."Hrs";

					$a_existingHide[$t_dayId][$displayHrs] = $row[$displayHrs];
				}

				
			}
		}
	}

	if(isset($_REQUEST['formSubmitted'])){
		extract($_REQUEST);

		$mainEta					=	$etaId-1;

		dbQuery("DELETE FROM hide_eta_timings WHERE etaType=$mainEta");

		$body						=	"<table width='98%' align='center' border='0' cellpadding='4' cellspacing='4'><tr><td colspan='8'><font style='font-family:verdana;font-size:17px;color:#4d4d4d;'><b>".$s_employeeName." has updated show/hide ETA for - ".$a_estimatedTimeArray[$etaId]."</b></font></td></tr>";

		if(isset($_POST['hideHrs']))
		{
			$body				   .=	"<tr><td colspan='8'><font style='font-family:verdana;font-size:17px;color:#4d4d4d;'><b>Selected Hiding ETA On - </b></font></td></tr>";
			$emailHideEta			=	"";
			$a_hideHrs			    =  $_POST['hideHrs'];
			foreach($a_hideHrs as $dayId=>$selectedTime){
				
				$dayText			=	$a_weekDaysText[$dayId];
				$coulumns			=	"";
				foreach($selectedTime as $time=>$hide){
					$t_time			=	$time+1;
					if($t_time < 10){
						$t_time		=	"0".$t_time;
					}
					$time			=	$time."Hrs";
					$coulumns      .=   ",".$time."=1";

					echo $emailHideEta  .=	$dayText." from ".$time." to ".$t_time."Hrs<br />";
				}

				dbQuery("INSERT INTO hide_eta_timings SET etaType=$mainEta,dayId=$dayId,dayText='$dayText'".$coulumns.",updatedDate='".CURRENT_DATE_INDIA."',updatedTime='".CURRENT_TIME_INDIA."',updatedBy='$s_employeeId',updatedFromIp='".VISITOR_IP_ADDRESS."'");
			}
			$body				   .=	"<tr><td colspan='8'><font style='font-family:verdana;font-size:15px;color:#333333;'>".$emailHideEta."</font></td></tr>";

			///////////////// SENDING EMAIL TO MANAGER WHEN SOMEONE HIDE THE ETA ///////////////
		}
		else{
			$body				   .=	"<tr><td colspan='8' align='center'><font style='font-family:verdana;font-size:17px;color:#ff0000;'><b>Removed all hide ETA</b></font></td></tr>";
		}
		$body				       .=	"</table>";

		/////////////////////////////// SENDING EMAIL //////////////////////////////////
		$from			=	"hr@ieimpact.com";
		$fromName		=	"HR ieIMPACT ";
		$mailSubject	=	$s_employeeName." has updated show/hide ETA for - ".$a_estimatedTimeArray[$etaId];
		$templateId		=	TEMPLATE_SIMPLE_FOR_CUSTOMERS_WITHOUT_THANKS_WITH_LOGO_BELOW;

		$a_templateData	=	array("{memberName}"=>"Admin","{emailBody}"=>$body,"{title}"=>$emailMessagetitle,"{message}"=>$emailMessage);

		sendTemplateMail($from, $fromName, "hemant@ieimpact.net", $mailSubject, $templateId, $a_templateData);
		
		$_SESSION['success_eta']	=	1;
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES ."/show-hide-orders-eta.php?etaId=$etaId");
		exit();
	}

?>
<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
	<tr>
		<td class='smalltext23' valign="top" colspan="4"><b><?php echo $currentlyShowingThirtyOrdersEmployee;?></b></td>
	</tr>
	<tr>
		<td colspan="5">&nbsp;</td>
	</tr>
	<tr>
		<td class='smalltext23' valign="top" colspan="4"><b>STOP NEW ORDERS&nbsp;(CURRENT IST DATE & TIME - <?php echo showDate(CURRENT_DATE_INDIA)." ".CURRENT_TIME_INDIA;?>)</b></td>
	</tr>
	<tr>
		<td colspan="5">&nbsp;</td>
	</tr>
	
	<?php
		if(!empty($errorMsg)){
	?>
	<tr>
		<td colspan="10" class="error"><?php echo $errorMsg;?></td>
	</tr>
	<tr>
		<td colspan="5">&nbsp;</td>
	</tr>
	<?php
		}
		if(isset($_SESSION['success_eta']) && $_SESSION['success_eta'] == 1){
	?>
	<tr>
		<td colspan="10" class="smalltext2"><b>SUCCESSFULLY UPDATED HIDE ETA OPTION</b></td>
	</tr>
	<tr>
		<td colspan="5">&nbsp;</td>
	</tr>
	<?php
			unset($_SESSION['success_eta']);
		}	
	?>
</table>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/datetimepicker_css1.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/calender.js"></script>
<script type="text/javascript">
	function makeShowHide(flag,count){
		if(flag == 1){
			document.getElementById('displayTime'+count).style.display = 'none';
		}
		else{
			document.getElementById('displayTime'+count).style.display = 'inline';
		}
	}
</script>	
<form name="selectOrderEta" action="" method="get">
	<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
		<tr>
			<td width="8%" class="smalltext23">SELECT ETA :</td>
			<td>
				<select name="etaId" onchange="document.selectOrderEta.submit();" class="smalltext23">
					<option value="">Select</option>
					<?php
						foreach($a_estimatedTimeArray as $k=>$v){
							$select		=	"";
							if($k		== $etaId){
								$select	=	"selected";
							}
							
					?>
					<option value="<?php echo $k;?>" <?php echo $select;?>><?php echo $v;?></option>
		
				<?php
					}
				?>
			</td>
		</tr>
		<?php
			if(empty($etaId)){
		?>
		<tr>
			<td height="10">&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="error2"><b>Please select ETA option</b></td>
		</tr>
		<tr>
			<td height="100">&nbsp;</td>
		</tr>
		<?php
			}
		?>
	</table>	
</form>
	
<?php
	if(!empty($etaId)){
?>
<br />
<form name="slectHideEta" action="" method="POST">
	<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" valign="top" style="border:1px solid #bebebe;">
		<tr>
			<td colspan="30" class="smalltext23">&nbsp;Click the hours checkboxes to stop new orders e.g. 0 means stop between 00:00 to 01:00, 1 means stop between 1:00 to 1:59, 2 means 2:00 to 2:59</td>
		</tr>
		<tr>
			<td width="8%">&nbsp;</td>
			<?php
				for($m=0;$m<=23;$m++){
					echo "<td width='2%' style='text-align:center;'><b>".$m."</b></td>";
				}
			?>
			<td>&nbsp;</td>
		</tr>
		<?php
			foreach($a_weekDaysText as $dayId=>$dayText){
		?>
		<tr>
			<td class="smalltext2">&nbsp;<?php echo $dayText;?></td>
			<?php
				for($hrs=0;$hrs<=23;$hrs++){

				    $displayHrs	    =	$hrs;
					if($hrs < 10){
						$displayHrs	=	"0".$hrs;
					}

					$checkBox		=	"";
					$color			=	"";

					if(!empty($a_existingHide) && count($a_existingHide) > 0 && array_key_exists($dayId,$a_existingHide)){
						$time_array			= $a_existingHide[$dayId];
						
						$serachArrayValue	=	$displayHrs."Hrs";
						$serachArrayValue   =   $time_array[$serachArrayValue];
						if(!empty($serachArrayValue)){
							$checkBox		=	"checked";
							$color			=	";background-color:#FF9595'";
						}
						
					}
			?>
			<td style='text-align:center;<?php echo $color;?>'>
				<input type="checkbox" name="hideHrs[<?php echo $dayId;?>][<?php echo $displayHrs;?>]" value="1" <?php echo $checkBox;?>>
			</td>
			<?php
				}
			?>
			<td>&nbsp;</td>
		</tr>
		<?php
			}		
		?>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="5">
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='etaId' value='<?php echo $etaId;?>'>
				<input type='hidden' name='formSubmitted' value='1'>
			</td>
		</tr>
	</table>
<form>
<?php
	}
	include(SITE_ROOT_EMPLOYEES		.   "/includes/bottom.php");
?>