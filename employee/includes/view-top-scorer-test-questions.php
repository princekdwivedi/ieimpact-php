<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	
?>
<html>
	<head>
	<TITLE>View Top 10 Test Question Answer Scorer</TITLE>
	<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
	</head>
	<body>
	<center>
		<?php
			$query	=	"SELECT * FROM employee_details WHERE testScore <> 0 AND employeeId != 3 ORDER BY testScore DESC LIMIT 10";
			$result	=	dbQuery($query);
			if(mysql_num_rows($result)){
	?>
	<table width="98%" align="center" border="0" cellpadding="2" cellspacing="2" style="border:2px solid #bebebe">
		<tr>
			<td colspan="3" class="textstyle3">
				<b>View Top 10 Test Question Answer Scorer</b>
			</td>
		</tr>
		<?php
			$count	=	0;
			while($row		=	mysql_fetch_assoc($result)){
				$count++;
				$employeeId	=	$row['employeeId'];
				$fullName	=	stripslashes($row['fullName']);
				$testScore	=	stripslashes($row['testScore']);
		?>
		<tr>
			<td width="3%" class="smalltext2"><b><?php echo $count;?>)&nbsp;</b></td>
			<td class="textstyle2"><b><?php echo $fullName;?>&nbsp;&nbsp;<?php echo $testScore;?></b></td>
		</tr>
		<?php
			}
		?>
	</table>
	<?php
			}
			else{
				echo "<table width='90%' align='center' border='0' height='100'><tr><td class='error' style='text-align:center;'><b>No Record Found.</b></td></tr></table>";
			}
		?>
		<br><br>
			<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>

	</center>
	</body>
</html>