<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/check-admin-login.php");
	include(SITE_ROOT_EMPLOYEES	. "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	. "/classes/employee.php");

	$showDetails				=	false;
	if(isset($_GET['employeeId'])){
		$employeeId				=	(int)$_GET['employeeId'];
		if(!empty($employeeId)){
			$query				=	"SELECT fullName,totalTaxExemption,taxRateApproximately,totalTax from employee_details WHERE employeeId=$employeeId";
			$result				=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row					=	mysqli_fetch_assoc($result);
				$totalTaxExemption		=	stripslashes($row['totalTaxExemption']);
				$taxRateApproximately	=	stripslashes($row['taxRateApproximately']);
				$totalTax				=	$row['totalTax'];
				$fullName				=	stripslashes($row['fullName']);

				$a_descriptions			=	array();
				$a_amounts				=	array();
				$a_existsArray			=	array();
				$a_existsFiles			=	array();
				$a_existsPath			=	array();
				$showDetails		    =	true;

				$query					=	"SELECT sectionId,descriptions,amount FROM employee_tax_declaration_details WHERE employeeId=$employeeId";
				$result			=	dbQuery($query);
				if(mysqli_num_rows($result))
				{
					while($row			=	mysqli_fetch_assoc($result)){
						$sectionId		=	$row['sectionId'];
						$descriptions	=	stripslashes($row['descriptions']);
						$amount		    =	$row['amount'];
						if(empty($amount)){
							$amount		=	"";
						}

						$a_descriptions[$sectionId]	=	$descriptions;
						$a_amounts[$sectionId]		=	$amount;
						$a_existsArray[]			=	$sectionId;

					}
				}

				$query			=	"SELECT * FROM employee_tax_declaration_files WHERE employeeId=$employeeId";
				$result			=	dbQuery($query);
				if(mysqli_num_rows($result))
				{
					while($row			=	mysqli_fetch_assoc($result)){
						$sectionId		=	$row['sectionId'];
						$fileName	    =	stripslashes($row['fileName']);
						$fileExt		=	$row['fileExt'];
						$a_existsPath[$sectionId]	=	$row['filePath'];
						
						$completeFileName	=	$fileName.".".$fileExt;

						$a_existsFiles[$sectionId]			=	$completeFileName;
					}
				}
				
			}
		}
	}
?>
<html>
<head>
<TITLE>View Employee Investment Files</TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
<link rel="shortcut icon" href="/new-favicon.ico" type="image/x-icon" />
</head>
<body>

<center>
<?php
	if($showDetails)
	{
?>
<table width="100%" align="center" border="0" cellspacing="3" cellspacing="3">
	<tr>
		<td colspan="10" class='title'>INCOME TAX INVESTMENT DECLARATION - <?php echo $fullName;?></td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td width="40%" class="title"><b>Type</b></td>
		<td width="25%" class="title"><b>Details</b></td>
		<td width="12%" class="title"><b>Under Section</b></td>
		<td width="8%" class="title"><b>Amount</b></td>
		<td class="title"><b>Upload File</font></td>
	</tr>
	<tr>
		<td colspan="6">
			<hr size="1" width="100%" bgcolor="#bebebe;">
		</td>
	</tr>
	<?php
		foreach($a_invesmentDetails as $key=>$value){
			$mainText		=	$a_invesmentDetails[$key];

			$level			=	0;
			$type			=	"";
			$isreqAmount	=	"";

			list($text,$level,$type,$underSection,$isreqAmount) = explode("|",$mainText);

			if($level		==	0){
				$text		=	"<font class='textstyle2'><b>$text</b></font>";
			}
			elseif($level	==	1){
				$text		=	"<font class='smalltext2'>$text</font>";
			}
			else{
				$text		=	"<font class='smalltext7'><font color='#ff0000;'>$text</font></font>";
			}

			//1= Level TA = TYPE 0 IS req U/S 0 Amount
			if($level		==	0 && $key != 1){
		?>
	<tr>
		<td colspan="6">
			<hr size="1" width="100%" bgcolor="#bebebe;">
		</td>
	</tr>
		<?php
			}
		?>
	<tr>
		<td valign="top" class="smalltext22"><?php echo $text;?></td>
		<td valign="top" class="smalltext22">
			<?php
				$existingDescriptions		=	"";
				if(!empty($a_descriptions) && count($a_descriptions) > 0 && array_key_exists($key,$a_descriptions)){
					$existingDescriptions	=	$a_descriptions[$key];
				}
				echo $existingDescriptions;
			?>
		</td>
		
		<td class="smalltext22" valign="top">
			<?php
				if(!empty($underSection)){
					echo "<b>$underSection</b>";
				}	
			?>
		</td>
		<td valign="top">
			<?php
				$existingAmount		=	"";
				if(!empty($a_amounts) && count($a_amounts) > 0 && array_key_exists($key,$a_amounts)){
					$existingAmount	=	$a_amounts[$key];
				}
				if(!empty($isreqAmount) && !empty($existingAmount)){

					echo $existingAmount."/-";
				}
				else{
					echo "-";
				}
			?>
		</td>
		<td valign="top">
			<?php
				if($type == "TF" || $type == "F"){
			
					if(!empty($a_existsFiles) && array_key_exists($key,$a_existsFiles)){
			?>
						<a href="<?php echo SITE_URL_EMPLOYEES;?>/dowanload-invesment.php?ID=<?php echo $key;?>&type=tax-invesment" class='link_style26'><?php echo $a_existsFiles[$key];?></a> 
			<?php
					}
				}	
			?>
		</td>
	</tr>
	<tr>
		<td colspan="6">
			<hr size="1" width="100%" bgcolor="#bebebe;">
		</td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td class="smalltext22"><b>Total Tax Exemption</b></td>
		<td class="smalltext18">&nbsp;</td>
		<td class="smalltext18">&nbsp;</td>
		<td class="smalltext22">&#8377;<?php echo $totalTaxExemption."/-";?></td>
		<td class="smalltext18">&nbsp;</td>
	</tr>
	<tr>
		<td height="3"></td>
	</tr>
	<tr>
		<td class="smalltext22"><b>Taxation Rate Approximately</b></td>
		<td class="smalltext18">&nbsp;</td>
		<td class="smalltext18">&nbsp;</td>
		<td class="smalltext18">&#8377;<?php echo $taxRateApproximately."/-";?></td>
		<td class="smalltext18">&nbsp;</td>
	</tr>
	<tr>
		<td height="3"></td>
	</tr>
	<tr>
		<td class="smalltext22"><b>Total Tax</b></td>
		<td class="smalltext18">&nbsp;</td>
		<td class="smalltext18">&nbsp;</td>
		<td class="smalltext18">&#8377;<?php echo $totalTax."/-";?></td>
		<td class="smalltext18">&nbsp;</td>
	</tr>
	<tr>
		<td height="3"></td>
	</tr>
</table>
<?php
	}
	else
	{
		echo "<table width='90%' align='center' border='1' height='100'><tr><td align='center' align='center' class='error'><b>Oops no record found.</b></td></tr></table>";
	}
?>

	<br><br>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
</center>
</body>
</html>

	