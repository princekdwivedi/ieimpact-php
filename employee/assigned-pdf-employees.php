<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");	
	include(SITE_ROOT			. "/classes/common.php");
	$commonObj					=	new common();

	$showForm		=	false;
	$customersId	=	0;
	$customersName	=	"";
	$forEmployee	=	0;
	$a_employees	=	array();
	if(isset($_GET['ID']) && $_GET['EID'])
	{
		$customersId  =	 $_GET['ID'];
		$forEmployee  =	 $_GET['EID'];
		$customersName=  $commonObj->getMemberName($customersId);
		if(!empty($customersName))
		{
			$showForm	=	true;
		}
	}
?>
<html>
<title>
	View Others Employees Assign For <?php echo $customersName;?>
</title>
<link href="<?php echo SITE_URL;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
<body>
<center>
	<table width="98%" align="center" border="0" cellpadding="2" cellspacing="2">
		<tr>
			<td colspan="4" class="smalltext2"><b>View Others Employees Assign For - <?php echo $customersName;?></b></td>
		</tr>
		<tr>
			<td width="10%" class="smalltext2">Sr No.</td>
			<td width="50%" class="smalltext2">Employee</td>
			<td width="20%" class="smalltext2" align="center">Has Reply Access</td>
			<td class="smalltext2"  align="center">Has Qa Access</td>
		</tr>
		<tr>
			<td colspan="4">
				<hr size="1" width="100%" color="#bebebe">
			</td>
		</tr>
	<?php
		$query	=	"SELECT pdf_clients_employees.employeeId,hasReplyAccess,hasQaAccess,firstName,lastName FROM pdf_clients_employees INNER JOIN employee_details ON pdf_clients_employees.employeeId=employee_details.employeeId WHERE customerId=$customersId AND pdf_clients_employees.employeeId <> $forEmployee";
		$result		=	mysql_query($query);
		if(mysql_num_rows($result))
		{
			$i	=	0;
			while($row			=	mysql_fetch_assoc($result))
			{
				$i++;
				$employeeId		=	$row['employeeId'];
				$firstName		=	stripslashes($row['firstName']);
				$lastName		=	stripslashes($row['lastName']);
				$hasReplyAccess	=	$row['hasReplyAccess'];
				$hasQaAccess	=	$row['hasQaAccess'];

				$employeeName	=	$firstName." ".$lastName;

				$accessImage	=	"<img src='".SITE_URL."/images/cross1.gif'>";
				$qaImage		=	"<img src='".SITE_URL."/images/cross1.gif'>";

				if(!empty($hasReplyAccess))
				{
					$accessImage	=	"<img src='".SITE_URL."/images/yes.gif'>";
				}
				if(!empty($hasQaAccess))
				{
					$qaImage		=	"<img src='".SITE_URL."/images/yes.gif'>";
				}
		?>
		<tr>
			<td class="smalltext2"><b><?php echo $i;?>)</b></td>
			<td class="smalltext2"><b><?php echo ucwords($employeeName);?></b></td>
			<td align="center"><?php echo $accessImage;?></td>
			<td align="center"><?php echo $qaImage;?></td>
		</tr>
		<tr>
			<td colspan="4">
				<hr size="1" width="100%" color="#bebebe">
			</td>
		</tr>
		<?php
			}
		}
	?>
	</table>
<br><br>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
</center>
</body>
</html>
