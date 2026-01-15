<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/check-site-maintanence.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/check-admin-login.php");
	include(SITE_ROOT				.   "/classes/pagingclass.php");
	include(SITE_ROOT				.	"/includes/send-mail.php");
	include(SITE_ROOT_EMPLOYEES		.	"/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		.	 "/includes/set-variables.php");

	$employeeObj					=	new employee();
	$shiftFrom						=	"";
	$shiftTo						=	"";
	$shiftFromHrs					=	"";
	$shiftFromMinitue				=	"";
	$sfiftToHrs						=	"";
	$shiftToMinitue					=	"";

	if(isset($_GET['ID']))
	{
		$employeeId					=	(int)$_GET['ID'];
		if(!empty($employeeId))
		{
			$query					=	"SELECT isShiftTimeAdded,shiftFrom,shiftTo,fullName,weeklyOff FROM employee_details WHERE employeeId=$employeeId";
			$result					=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row				=	mysqli_fetch_assoc($result);
				$isShiftTimeAdded	=	$row['isShiftTimeAdded'];
				$shiftFrom			=	$row['shiftFrom'];
				$shiftTo			=	$row['shiftTo'];
				$weeklyOff			=	strtolower($row['weeklyOff']);
				$employeeName		=	stripslashes($row['fullName']);
				if($isShiftTimeAdded==  1)
				{
					list($shiftFromHrs,$shiftFromMinitue,$fh)	=	explode(":",$shiftFrom);
					list($sfiftToHrs,$shiftToMinitue,$th)		=	explode(":",$shiftTo);
				}
			}
		}
	}

	
?>
<html>
<head>
<TITLE></TITLE>
<script type="text/javascript">
	function reflectChange()
	{
		parent.location.reload();
	}
</script>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
	<body>
		<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
			<tr>
				<td colspan="2" class='smalltext23'><b>Update Rates For <?php echo $employeeName;?></b></td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
		</table>
		<?php
			if(isset($_REQUEST['searchFormSubmit']))
			{
				extract($_REQUEST);
		
				if(!empty($shiftFromHrs) || !empty($shiftFromMinitue) || !empty($sfiftToHrs) || !empty($shiftToMinitue))
				{
					$shiftFrom		=	$shiftFromHrs.":".$shiftFromMinitue.":00";
					$shiftTo		=	$sfiftToHrs.":".$shiftToMinitue.":00";

					dbQuery("UPDATE employee_details SET isShiftTimeAdded=1,shiftFrom='$shiftFrom',shiftTo='$shiftTo',weeklyOff='$weeklyOff' WHERE employeeId=$employeeId");
				}

				echo "<script type='text/javascript'>reflectChange();</script>";
					
			}
		?>
		<br />
		<form  name='addPdfClent' method='POST' action="">
			<table width="98%" border="0" align="center" cellpadding="2" cellspacing="2" valign="top">
				<tr>
					<td class="smalltext23" width="20%">Shift Time From :</td>
					<td width="5%">
							<select name="shiftFromHrs">
								<?php
									for($i=0;$i<=24;$i++)
									{
										$showFHr	=	$i;
										if($i < 10)
										{
											$showFHr=	"0".$i;
										}

										$select		=	"";
										if($shiftFromHrs == $showFHr)
										{
											$select	=	"selected";
										}

										echo "<option value='$showFHr' $select>$showFHr</option>";
									}
								?>
							</select>
						</td>
						<td width="5%" class="smalltext2">
							Hrs
						</td>
						<td width="5%">
							<select name="shiftFromMinitue">
								<?php
									for($i=0;$i<=59;$i++)
									{
										$showFMin	=	$i;
										if($i < 10)
										{
											$showFMin=	"0".$i;
										}

										$select		=	"";
										if($shiftFromMinitue == $showFMin)
										{
											$select	=	"selected";
										}

										echo "<option value='$showFMin' $select>$showFMin</option>";
									}
								?>
							</select>
						</td>
						<td width="8%" class="smalltext2">
							Minute
						</td>
						<td width="8%" class="smalltext23">To</td>
						<td width="5%">
							<select name="sfiftToHrs">
								<?php
									for($i=0;$i<=24;$i++)
									{
										$showTHr	=	$i;
										if($i < 10)
										{
											$showTHr=	"0".$i;
										}

										$select		=	"";
										if($sfiftToHrs == $showTHr)
										{
											$select	=	"selected";
										}

										echo "<option value='$showTHr' $select>$showTHr</option>";
									}
								?>
							</select>
						</td>
						<td width="5%" class="smalltext2">
							Hrs
						</td>
						<td width="5%" >
							<select name="shiftToMinitue">
								<?php
									for($i=0;$i<=59;$i++)
									{
										$showTMin	=	$i;
										if($i < 10)
										{
											$showTMin=	"0".$i;
										}

										$select		=	"";
										if($shiftToMinitue == $showTMin)
										{
											$select	=	"selected";
										}

										echo "<option value='$showTMin' $select>$showTMin</option>";
									}
								?>
							</select>
						</td>
						<td width="5%" class="smalltext2">
							Minute
						</td>
						<td class="error">
							<b>(Times In IST)</b>
						</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td class="smalltext23">Weekly Off On :</td>
						<td colspan="4">
							<select name="weeklyOff">
								<option value="">No Weekly Off</option>
								<?php
									foreach($a_weekDaysText as $kk=>$vv){
										$matchDay	=	strtolower($vv);
										$select	    =	"";
										if($matchDay== $weeklyOff){
											$select	=	"selected";
										}
										echo "<option value='$matchDay' $select>$vv</option>";
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="image" name="name" src="<?php echo SITE_URL_EMPLOYEES;?>/images/submit.jpg" border="0">
							<input type='hidden' name='searchFormSubmit' value='1'>
						</td>
					</tr>
			</table>
		</form>
	</body>
</html>