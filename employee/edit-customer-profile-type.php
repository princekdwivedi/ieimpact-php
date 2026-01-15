<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/top.php");
	include(SITE_ROOT_EMPLOYEES     .   "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES		.	"/classes/employee.php");
	include(SITE_ROOT				.   "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		.   "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES		.	"/includes/set-variables.php");
	$employeeObj					=	new employee();
	$displayUpdate					=	false;
	$t_searchCustomer				=	"";
	$searchCustomer					=	"";
	if(isset($_GET['searchCustomer'])){
		$searchCustomer				=	$_GET['searchCustomer'];
		if(!empty($searchCustomer)){
			$displayUpdate			=	true;
			$t_searchCustomer		=	makeDBSafe($searchCustomer);
		}
	}

	if(empty($s_hasManagerAccess))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	if(isset($_POST['changeTypeFormSubmit'])){
		$a_changeType		=	$_POST['changeType'];
		$a_originalType		=	$_POST['originalType'];
		$searchCustomer		=	$_POST['searchCustomer'];
		//pr($a_changeType);
		//pr($a_originalType);
		//pr($searchCustomer);
		if(!empty($a_changeType) && count($a_changeType) > 0){
			foreach($a_changeType as $memberId => $changeInto){
				if(!empty($changeInto)){
					$existingType	=	$a_originalType[$memberId];
					
					dbQuery("UPDATE members SET appraisalSoftwareType=$changeInto WHERE memberId=$memberId");

					dbQuery("INSERT INTO changed_order_profile_file_type SET memberId=$memberId,changedFileInto=$changeInto,existingType=$existingType,employeeId=$s_employeeId,date='".CURRENT_DATE_INDIA."',time='".CURRENT_TIME_INDIA."'");
				}
			}
		}
		$_SESSION['successChange'] = 1;

		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES."/edit-customer-profile-type.php?searchCustomer=".$searchCustomer);
		exit();
	}

?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/jquery.js"></script>
<script type='text/javascript' src='<?php echo SITE_URL;?>/script/jquery.autocomplete.min.js'></script>
</script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/jquery.autocomplete.css" />


<script type="text/javascript">
$().ready(function() {
	$("#orderAddress").autocomplete("<?php echo SITE_URL_EMPLOYEES?>/search-all-customer.php", {width: 350,selectFirst: false});
});
</script>
<form name="searchDeleteOrderForm" action=""  method="GET">
	<table width="99%" align="center" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="35%" class="nextText">SEARCH CUSTOMER TO CHANGE PROFILE FILE TYPE :</td>
			<td width="35%" class="nextText">
				<input type='text' name="searchCustomer" size="40" value="<?php echo $searchCustomer;?>" id="orderAddress"  style="border:1px solid #4d4d4d;height:25px;font-size:15px;">
			</td>
			<td>
				<input type="image" name="submit" src="<?php echo SITE_URL;?>/images/small-submit.png" border="0" style="cursor:pointer;">
				<input type='hidden' name='searchFormSubmit' value='1'>
			</td>
		</tr>
	</table>
</form>
<br /><br />
<form name="changeTypeInto" action=""  method="POST">
<table width="99%" align="center" border="0" cellpadding="0" cellspacing="0">
	<?php
		if(isset($_SESSION['successChange'])){
	?>
	<tr>
		<td colspan="6" class="smalltext23"><b>Successfully changed customer profile file type.</b></td>
	</tr>
	<tr>
		<td height="3"></td>
	</tr>
	<?php
			unset($_SESSION['successChange']);
		}
		if($displayUpdate	== true){
			$query			=	"SELECT * FROM members WHERE memberType='".CUSTOMERS."' AND completeName LIKE '%$t_searchCustomer%' AND isActiveCustomer=1 ORDER BY completeName";
			$result			=	dbQuery($query);
			if(mysqli_num_rows($result)){
		?>
		<tr bgcolor="#373737" height="20">
			<td width="5%" class="smalltext8">&nbsp;<b>Sr No.</b></td>
			<td width="20%" class="smalltext8"><b>Customer Name</b></td>
			<td width="20%" class="smalltext8"><b>Current Profile File</b></td>
			<td width="12%" class="smalltext8"><b>Total Orders</b></td>
			<td width="12%" class="smalltext8"><b>Last Order On</b></td>
			<td width="12%" class="smalltext8"><b>Registered On</b></td>
			<td class="smalltext8"><b>Change Profile File Into </b></td>
		</tr>
		<?php
				$i	=	0;
				while($row					=	mysqli_fetch_assoc($result)){
					$i++;
					$customerId				=	$memberId = $row['memberId'];
					$firstName				=	stripslashes($row['firstName']);
					$lastName				=	stripslashes($row['lastName']);
					$completeName           =   $firstName." ".substr($lastName, 0, 1);
					$appraisalSoftwareType	=	$row['appraisalSoftwareType'];
					$totalOrdersPlaced		=	$row['totalOrdersPlaced'];
					$lastOrderAddedOn    	=	showDate($row['lastOrderAddedOn']);
					$registeredOn    		=	showDate($row['addedOn']);
					

					$bgColor				=	"class='rwcolor1'";
					if($i%2==0)
					{
						$bgColor			=   "class='rwcolor2'";
					}
					
					
			?>
			<tr height="23" <?php echo $bgColor;?>>
				<td class="smalltext22" valign="top">&nbsp;<?php echo $i;?>)</td>
				<td valign="top">&nbsp;
					<?php 
						echo "<a href='".SITE_URL_EMPLOYEES."/new-pdf-work.php?isSubmittedForm=1&serachCustomerById=$customerId' class='link_style12' style='cursor:pointer;'>$completeName</a>";
					?>
				</td>
				<td class="smalltext22" valign="top">
					<?php 
						
						echo $a_appraisalSoftwareRegPage[$appraisalSoftwareType];		
						
					?>
				</td>
				<td class="smalltext22" valign="top"><?php echo $totalOrdersPlaced;?></td>
				<td class="smalltext22" valign="top"><?php echo $lastOrderAddedOn;?></td>
				<td class="smalltext22" valign="top"><?php echo $registeredOn;?></td>	
				<td class="smalltext22" valign="top">
					<select name="changeType[<?php echo $memberId;?>]">
						<option value="0">Select</option>
						<?php
							foreach($a_appraisalSoftwareRegPage as $k=>$v){
								if($k != $appraisalSoftwareType){
									echo "<option value='$k'>$v</option>";
								}
							}
						?>
					</select>
					<input type="hidden" name="originalType[<?php echo $memberId;?>]" value="<?php echo $appraisalSoftwareType;?>">
				</td>
			</tr>	
			<tr>
				<td height="3"></td>
			</tr>
			<?php

				}
			?>
			<tr>
				<td height="10"></td>
			</tr>
			<tr>
				<td colspan="3">
					<input type="image" name="submit" src="<?php echo SITE_URL;?>/images/small-submit.png" border="0" style="cursor:pointer;">
					<input type='hidden' name='searchCustomer' value='<?php echo $searchCustomer;?>'>
					<input type='hidden' name='changeTypeFormSubmit' value='1'>
				</td>
			</tr>
			<?php
			}
			else{
		?>
		<tr>
			<td height="300" class="error2" style="text-align:center;"><b>No Customer found - <?php echo $searchCustomer;?>.</b></td>
		</tr>
		<?php
			}
		}
		else{
	?>
	<tr>
		<td height="300" class="error2" style="text-align:center;"><b>Please search a customer.</b></td>
	</tr>
	<?php
		}
	?>
</table>
</form>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>