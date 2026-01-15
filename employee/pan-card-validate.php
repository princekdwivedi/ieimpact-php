<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES .   "/includes/new-top.php");
	include(SITE_ROOT_EMPLOYEES .   "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES .   "/classes/employee.php");
	include(SITE_ROOT			.   "/includes/common-array.php");
	include(SITE_ROOT_EMPLOYEES	.   "/includes/common-array.php");
	$employeeObj				=   new employee();
	include(SITE_ROOT_EMPLOYEES	.   "/includes/set-variables.php");
	include(SITE_ROOT_EMPLOYEES	.   "/includes/check-pdf-login.php");

	
	function validatePan($panNumber){
		$msg = "Not Valid";
		$pattern = '/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/';
		$result  = preg_match($pattern, $panNumber);
		if ($result) {
			$findme = ucfirst(substr($panNumber, 3, 1));
			$mystring = 'CPHFATBLJG';
			$pos = strpos($mystring, $findme);
			if ($pos === false) {
				$msg = "Not valid";
			} else {
				$msg = "Valid";
			}
		} else {
			$msg = "Not Valid";
		}
		return $msg;
	}

	function isAadharValid($num) {
		settype($num, "string");
		$expectedDigit = substr($num, -1);
		$actualDigit = CheckSumAadharDigit(substr($num, 0, -1));
		return ($expectedDigit == $actualDigit) ? $expectedDigit == $actualDigit : 0;
	}
 
	function CheckSumAadharDigit($partial) {
		$dihedral = array(
			array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
			array(1, 2, 3, 4, 0, 6, 7, 8, 9, 5),
			array(2, 3, 4, 0, 1, 7, 8, 9, 5, 6),
			array(3, 4, 0, 1, 2, 8, 9, 5, 6, 7),
			array(4, 0, 1, 2, 3, 9, 5, 6, 7, 8),
			array(5, 9, 8, 7, 6, 0, 4, 3, 2, 1),
			array(6, 5, 9, 8, 7, 1, 0, 4, 3, 2),
			array(7, 6, 5, 9, 8, 2, 1, 0, 4, 3),
			array(8, 7, 6, 5, 9, 3, 2, 1, 0, 4),
			array(9, 8, 7, 6, 5, 4, 3, 2, 1, 0)
		);

		$permutation = array(
			array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
			array(1, 5, 7, 6, 2, 8, 3, 0, 9, 4),
			array(5, 8, 0, 3, 7, 9, 6, 1, 4, 2),
			array(8, 9, 1, 6, 0, 4, 3, 5, 2, 7),
			array(9, 4, 5, 3, 1, 2, 6, 8, 7, 0),
			array(4, 2, 8, 6, 5, 7, 3, 9, 0, 1),
			array(2, 7, 9, 3, 8, 0, 6, 4, 1, 5),
			array(7, 0, 4, 6, 9, 1, 3, 2, 5, 8)
		);
 
		$inverse = array(0, 4, 3, 2, 1, 5, 6, 7, 8, 9);
	
		settype($partial, "string");
		$partial = strrev($partial);
		$digitIndex = 0;
		for ($i = 0; $i < strlen($partial); $i++) {
			$digitIndex = $dihedral[$digitIndex][$permutation[($i + 1) % 8][$partial[$i]]];
		}
		return $inverse[$digitIndex];
	}

?>
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="12" class="title">Employees Whose PAN seems invalid</td>
	</tr>
	<tr>
		<td colspan="12" height="5"></td>
	</tr>
	<tr>
		<td width="2%">&nbsp;</td>
		<td width="15%" class="smalltext2"><b>Employee</b></td>
		<td width="4%" class="smalltext2"><b>Dep.</b></td>
		<td width="12%" class="smalltext2"><b>PAN NUMBER</b></td>
		<td width="2%">&nbsp;</td>
		<td width="15%" class="smalltext2"><b>Employee</b></td>
		<td width="4%" class="smalltext2"><b>Dep.</b></td>
		<td width="12%" class="smalltext2"><b>PAN NUMBER</b></td>
		<td width="2%">&nbsp;</td>
		<td width="15%" class="smalltext2"><b>Employee</b></td>
		<td width="4%" class="smalltext2"><b>Dep.</b></td>
		<td class="smalltext2"><b>PAN NUMBER</b></td>
	</tr>
	<tr>
		<td colspan="12">
			<hr size="1"bgcolor="#bebebe;">
		</td>
	</tr>
	<tr>
<?php
	$query			=	"SELECT employeeId,fullName,panCardNumber,hasPdfAccess FROM employee_details WHERE isActive=1 AND panCardNumber <> '' ORDER BY fullName";
	$result			=	dbQuery($query);
	$i				=	0;
	$k				=	0;
	$l				=	0;
	if(mysql_num_rows($result)){
		
		while($row			=	mysql_fetch_assoc($result)){
			$employeeId     =   $row['employeeId'];
			$fullName       =   $row['fullName'];
			$panCardNumber  =   $row['panCardNumber'];
			$hasPdfAccess   =   $row['hasPdfAccess'];
			$dep			=	"MT";
			if($hasPdfAccess==	1){
				$dep		=	"PDF";
			}

			$validatePan	=	validatePan($panCardNumber);
	
			if($validatePan == "Not Valid"){
				$i++;
				$l++;
				$k++;
?>
				<td><?php echo $i;?>)</td>
				<td class="smalltext2"><?php echo $fullName;?></td>
				<td class="smalltext2"><?php echo $dep;?></td>
				<td class="smalltext2"><?php echo $panCardNumber;?></td>
<?php	
				if($k == 3){
					$k=	0;
					echo "</tr><tr><td height='4'></td></tr><tr>";
				}
			}
		}
	}
?>
	</tr>
</table>
<br /><br />
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="12" class="title">Employees Whose Aadhar seems invalid</td>
	</tr>
	<tr>
		<td colspan="12" height="5"></td>
	</tr>
	<tr>
		<td width="2%">&nbsp;</td>
		<td width="15%" class="smalltext2"><b>Employee</b></td>
		<td width="4%" class="smalltext2"><b>Dep.</b></td>
		<td width="12%" class="smalltext2"><b>Aadhar NUMBER</b></td>
		<td width="2%">&nbsp;</td>
		<td width="15%" class="smalltext2"><b>Employee</b></td>
		<td width="4%" class="smalltext2"><b>Dep.</b></td>
		<td width="12%" class="smalltext2"><b>Aadhar NUMBER</b></td>
		<td width="2%">&nbsp;</td>
		<td width="15%" class="smalltext2"><b>Employee</b></td>
		<td width="4%" class="smalltext2"><b>Dep.</b></td>
		<td class="smalltext2"><b>Aadhar NUMBER</b></td>
	</tr>
	<tr>
		<td colspan="12">
			<hr size="1"bgcolor="#bebebe;">
		</td>
	</tr>
	<tr>
<?php
	$query			=	"SELECT employeeId,fullName,aadhaarNumber,hasPdfAccess FROM employee_details WHERE isActive=1 AND aadhaarNumber <> '' ORDER BY fullName";
	$result			=	dbQuery($query);
	$i				=	0;
	$k				=	0;
	$l				=	0;
	if(mysql_num_rows($result)){
		
		while($row			=	mysql_fetch_assoc($result)){
			$employeeId     =   $row['employeeId'];
			$fullName       =   $row['fullName'];
			$aadhaarNumber  =   $row['aadhaarNumber'];
			$hasPdfAccess   =   $row['hasPdfAccess'];
			$dep			=	"MT";
			if($hasPdfAccess==	1){
				$dep		=	"PDF";
			}

			$validateAadhar	=	isAadharValid($aadhaarNumber);
	
			if($validateAadhar != 1){
				$i++;
				$l++;
				$k++;
?>
				<td><?php echo $i;?>)</td>
				<td class="smalltext2"><?php echo $fullName;?></td>
				<td class="smalltext2"><?php echo $dep;?></td>
				<td class="smalltext2"><?php echo $aadhaarNumber;?></td>
<?php	
				if($k == 3){
					$k=	0;
					echo "</tr><tr><td height='4'></td></tr><tr>";
				}
			}
		}
	}
?>
	</tr>
</table>
<?php

	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>
