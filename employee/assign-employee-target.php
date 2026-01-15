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
	
	if(!$s_hasManagerAccess)
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	list($currentY,$currentM,$currentD)	=	explode("-",$nowDateIndia);


	$a_employeeDetails			=	array();
	$a_processedTargets			=	array();
	$a_qaTargets				=	array();
	$a_processRatings			=	array();
	$a_qaRatings				=	array();
	$isAlreadyHaveRecords		=	0;
	$showingMonth				=	$currentM;
	$showingYear				=	$currentY;
	$showingMonthText			=	$a_month[$currentM];

	if(isset($_GET['showingMonth']))
	{
		$showingMonth			=	$_GET['showingMonth'];
	}
	if(isset($_GET['showingYear']))
	{
		$showingYear			=	$_GET['showingYear'];
	}

	$nonLeadingZeroMonth		=	$showingMonth;
	if($showingMonth < 10 && strlen($showingMonth) > 1)
	{
		$nonLeadingZeroMonth	=	substr($showingMonth,1);
	}


	
	$query						=	"SELECT * FROM employee_target WHERE targetMonth=$nonLeadingZeroMonth AND targetYear=$showingYear ORDER BY employeeName";
	$result						=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		$isAlreadyHaveRecords	=	1;
		while($row				=	mysqli_fetch_assoc($result))
		{
			$employeeId			=	$row['employeeId'];
			$processedTarget	=	$row['processedTarget'];
			$qaTarget			=	$row['qaTarget'];
			$processedDone		=	$row['processedDone'];
			$qaDone				=	$row['qaDone'];
			$poorRating			=	$row['poorRating'];
			$averageRating		=	$row['averageRating'];	
			$goodRating			=	$row['goodRating'];		
			$veryGoodRating		=	$row['veryGoodRating'];		
			$excellentRating	=	$row['excellentRating'];		
			$qaPoorRating		=	$row['qaPoorRating'];		
			$qaAverageRating	=	$row['qaAverageRating'];		
			$qaGoodRating		=	$row['qaGoodRating'];		
			$qaVeryGoodRating	=	$row['qaVeryGoodRating'];		
			$qaExcellentRating	=	$row['qaExcellentRating'];	



			$a_processedTargets[$employeeId]=	$processedTarget."|".$processedDone;
			$a_qaTargets[$employeeId]		=	$qaTarget."|".$qaDone;

			$a_processRatings[$employeeId]	=	$poorRating."|".$averageRating."|".$goodRating."|".$veryGoodRating."|".$excellentRating;
			$a_qaRatings[$employeeId]		=	$qaPoorRating."|".$qaAverageRating."|".$qaGoodRating."|".$qaVeryGoodRating."|".$qaExcellentRating;
		}
	}
	
	$query						=	"SELECT employeeId,fullName FROM employee_details WHERE isActive=1 AND hasPdfAccess=1 ORDER BY fullName";
	$result						=	dbQuery($query);
	if(mysqli_num_rows($result))
	{
		while($row				=	mysqli_fetch_assoc($result))
		{
			$employeeId			=	$row['employeeId'];
			$employeeName		=	stripslashes($row['fullName']);
			
			$a_employeeDetails[$employeeId]	=	$employeeName;
		}
	}

	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		
		$a_selectedProcessed	=	$_POST['assignedProcessed'];
		$a_selectedQa			=	$_POST['assignedQa'];

		foreach($a_selectedProcessed as $employeeId=>$processedTarget)
		{
			$qaTarget			=	$a_selectedQa[$employeeId];
			$employeeName		=	$a_employeeDetails[$employeeId];

			if(empty($processedTarget))
			{
				$processedTarget=	0;
			}

			if(empty($qaTarget))
			{
				$qaTarget		=	0;
			}

			if(empty($isAlreadyHaveRecords))
			{
				$query			=	"INSERT INTO employee_target SET employeeId=$employeeId,employeeName='$employeeName',processedTarget=$processedTarget,qaTarget=$qaTarget,targetMonth=$nonLeadingZeroMonth,targetYear=$showingYear,addedOn='".CURRENT_DATE_INDIA."',addedTime='".CURRENT_TIME_INDIA."',addedBy=$s_employeeId,adddedFromIP='".VISITOR_IP_ADDRESS."'";
				dbQuery($query);
			}
			else
			{
				$query			=	"UPDATE employee_target SET processedTarget=$processedTarget,qaTarget=$qaTarget,updatedOn='".CURRENT_DATE_INDIA."',updatedTime='".CURRENT_TIME_INDIA."',updatedBy=$s_employeeId,updatedFromIP='".VISITOR_IP_ADDRESS."' WHERE employeeId=$employeeId AND targetMonth=$nonLeadingZeroMonth AND targetYear=$currentY";
				dbQuery($query);
			}
			
		}
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/assign-employee-target.php");
		exit();
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
		else
		{
			return false;
		}
	 }
</script>
<form name="changeCheckTarget" action="" method="GET">
	<table cellpadding="2" cellspacing="2" width='98%'align="center" border='0'>
		<tr>
			<td class="smalltext24" colspan="5">
				<b>ASSIGN EMPLOYEES THEIR TARGET FOR <?php echo $showingMonthText.",".$showingYear;?></b> 
			</td>
		</tr>
		<tr>
			<td class="smalltext24" width="10%">
				Change It To : 
			</td>
			<td class="smalltext24" width="6%">
				<select name="showingMonth"> 
					<?php
						foreach($a_month as $k=>$v)
						{
							$select		=	"";
							if($k		==	$showingMonth)
							{
								$select	=	"selected";
							}
							echo "<option value='$k' $select>$v</option>";
						}
					?>
				</select>
			</td>
			<td class="smalltext24" width="5%">
				<select name="showingYear"> 
					<?php
						$start			=	"2014";
						$end			=	date('Y');
						for($i=$start;$i<=$end;$i++)
						{
							$select		=	"";
							if($i		==	$showingYear)
							{
								$select	=	"selected";
							}
							echo "<option value='$i' $select>$i</option>";
						}
					?>
				</select>
			</td>
			<td class="smalltext24">
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='searchFormSubmitted' value='1'>
			</td>
		</tr>
	</table>
</form>
<br />
<form name="addEditTraget" action="" method="POST">
	<table cellpadding="2" cellspacing="2" width='98%'align="center" border='0'>
		<tr>
			<td width="2%">&nbsp;</td>
			<td width="19%" class="smalltext2"><b>Employee Name</b></td>
			<td width="10%" class="smalltext2"><b>Processed Target</b></td>
			<td width="10%" class="smalltext2"><b>Target Achieved</b></td>
			<td class="smalltext2" width="8%"><b>QA Target</b></td>
			<td width="9%" class="smalltext2"><b>Target Achieved</b></td>
			<td width="8%" class="smalltext2"><b>Poor<img src="<?php echo SITE_URL;?>/images/rating/1.png"></b></td>
			<td width="8%" class="smalltext2"><b>Average<img src="<?php echo SITE_URL;?>/images/rating/2.png"></b></td>
			<td width="8%" class="smalltext2"><b>Good<img src="<?php echo SITE_URL;?>/images/rating/3.png"></b></td>
			<td width="9%" class="smalltext2"><b>Very Good<img src="<?php echo SITE_URL;?>/images/rating/4.png"></b></td>
			<td class="smalltext2"><b>Excellent<img src="<?php echo SITE_URL;?>/images/rating/5.png"></b></td>
		</tr>
		<?php
			$count		=	0;
			foreach($a_employeeDetails as $employeeId=>$employeeName)
			{
				$count++;
				$processTarget			=	"";
				$processOrders			=	"";
				if(array_key_exists($employeeId,$a_processedTargets))
				{
					$processTargetOrders=	$a_processedTargets[$employeeId];
					list($processTarget,$processOrders)	=	explode("|",$processTargetOrders);
				}
				if(empty($processTarget))
				{
					$processTarget		=	"";
				}
				if(empty($processOrders))
				{
					$processOrders		=	"";
				}
				
				$qaTarget				=	"";
				$qaOrders				=	"";
				if(array_key_exists($employeeId,$a_qaTargets))
				{
					$qaTargetText		=	$a_qaTargets[$employeeId];
					list($qaTarget,$qaOrders)	=	explode("|",$qaTargetText);
				}
				if(empty($qaTarget))
				{
					$qaTarget			=	"";
				}
				if(empty($qaOrders))
				{
					$qaOrders			=	"";
				}

				if(array_key_exists($employeeId,$a_processRatings)){
					$processRatings			=		$a_processRatings[$employeeId];
					list($poorRating,$averageRating,$goodRating,$veryGoodRating,$excellentRating)	=	explode("|",$processRatings);
				}
				else{
					$poorRating       = "N/A";
					$averageRating    = "N/A";
					$goodRating       = "N/A";
					$veryGoodRating   = "N/A";
					$excellentRating  = "N/A";
				}

				if(array_key_exists($employeeId,$a_qaRatings)){
				
					$qaRatings				=		$a_qaRatings[$employeeId];
				
					list($qaPoorRating,$qaAverageRating,$qaGoodRating,$qaVeryGoodRating,$qaExcellentRating)	 =	explode("|",$qaRatings);
				}
				else{
					$qaPoorRating      = "N/A";
					$qaAverageRating   = "N/A";
					$qaGoodRating      = "N/A";
					$qaVeryGoodRating  = "N/A";
					$qaExcellentRating = "N/A";
				}
		?>
		<tr>
			<td class="smalltext2" valign="top"><?php echo $count;?>)</td>
			<td class="smalltext23" valign="top"><?php echo $employeeName;?></td>
			<td class="smalltext23" valign="top">
				<input type="text" name="assignedProcessed[<?php echo $employeeId;?>]" size="10" value="<?php echo $processTarget;?>" onKeyPress="return checkForNumber();" maxlength="4" style="border:1px solid #000000;color:#4d4d4d;font-family:verdana;font-size:12px;height:20px;">
			</td>
			<td class="smalltext23" valign="top">
				<?php
					if(!empty($processOrders) && !empty($processTarget))
					{
						$percentageTarget	=	$processOrders/$processTarget;
						$percentageTarget	=	$percentageTarget*100;
						$percentageTarget	=	"(".round($percentageTarget,2)."%)";
						echo $processOrders."&nbsp;".$percentageTarget;
					}
					else
					{
						echo "&nbsp;";
					}
				?>
			</td>
			<td class="smalltext13" valign="top">
				<input type="text" name="assignedQa[<?php echo $employeeId;?>]" size="10" value="<?php echo $qaTarget;?>" onKeyPress="return checkForNumber();" maxlength="4" style="border:1px solid #000000;color:#4d4d4d;font-family:verdana;font-size:12px;height:20px;">
			</td>
			<td class="smalltext23" valign="top">
				<?php
					if(!empty($qaOrders))
					{
						$percentageQaTarget		=  "";
						if($qaOrders		   >=  $qaTarget)
						{						
							$percentageTarget   =  "(100%)";
						}
						else
						{
							$percentageQaTarget	=	$qaOrders/$qaTarget;
							$percentageQaTarget	=	$percentageQaTarget*100;
							$percentageQaTarget	=	"(".round($percentageQaTarget,2)."%)";
								
						}
						echo $qaOrders."&nbsp;".$percentageQaTarget;
					}
					else
					{
						echo "&nbsp;";
					}
				?>
			</td>
			<td class="smalltext2">
				Process :<?php echo $poorRating;?><br /> 
				Qa : <?php echo $qaPoorRating;?>
			</td>
			<td class="smalltext2">
				Process :<?php echo $averageRating;?><br /> 
				Qa : <?php echo $qaAverageRating;?>
			</td>
			<td class="smalltext2">
				Process :<?php echo $goodRating;?><br /> 
				Qa : <?php echo $qaGoodRating;?>
			</td>
			<td class="smalltext2">
				Process :<?php echo $veryGoodRating;?><br /> 
				Qa : <?php echo $qaVeryGoodRating;?>
			</td>
			<td class="smalltext2">
				Process :<?php echo $excellentRating;?><br /> 
				Qa : <?php echo $qaExcellentRating;?>
			</td>
		</tr>
		<?php
			}
		?>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td>
				<?php
					if($currentM	==	$showingMonth && $currentY   ==  $showingYear)
					{
	
				?>
				<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
				<input type='hidden' name='formSubmitted' value='1'>
				<?php
					}	
				?>
			</td>
		</tr>
	</table>
</form>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>