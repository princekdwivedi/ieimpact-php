<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	."/includes/common-array.php");
	include(SITE_ROOT			. "/classes/validate-fields.php");
	$employeeObj				=	new employee();
	$validator					=	new validate();
	$breakId					=   0;
	include(SITE_ROOT_EMPLOYEES	. "/includes/set-variables.php");
	if(empty($s_employeeId))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}
	else
	{
		if(empty($s_isInBreak) || empty($s_breakId))
		{
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}
	}
	$query		=	"SELECT * FROM employee_breaks WHERE employeeId=$s_employeeId AND breakId=$s_breakId AND breakFinsheddate='0000-00-00'";
	$result		=	dbQuery($query);
	if(mysql_num_rows($result))
	{
		$row			=	mysql_fetch_assoc($result);
		$breakTakingFor	=	stripslashes($row['breakTakingFor']);
		$breakDate		=	showDate($row['breakDate']);
		$breakTime		=	$row['breakTime'];
		$breakTime		=	date("H:i",strtotime($breakTime));

		if(isset($_GET['unm']) && $_GET['unm'] == 1)
		{
			dbQuery("UPDATE employee_breaks SET breakFinsheddate='".CURRENT_DATE_INDIA."',breakFinishedTime='".CURRENT_TIME_INDIA."' WHERE breakId=$s_breakId AND employeeId=$s_employeeId");

			dbQuery("UPDATE employee_details SET isInBreak=0 WHERE employeeId=$s_employeeId AND isInBreak=1");

			unset($_SESSION['isInBreak']);
			unset($_SESSION['breakId']);

			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();

		}
	}
?>
<script type='text/javascript'>
	function unmarkBreak()
	{
		var confirmation = window.confirm("Are you sure to complete your break time?");
		if(confirmation == true)
		{
			window.location.href="<?php echo SITE_URL_EMPLOYEES;?>/complete-break-time.php?unm=1";
		}
	}
</script>
<html>
<head>
<?php
	if(!isset($docTitle))
	{
		$docTitle		=	"Employee Area";
	}
	if(!isset($docKeywords))
	{
		$docKeywords	=	"Employee Area";
	}
	if(!isset($docDescription))
	{
		$docDescription	=	"Employee Area";
	}
	$currentDate	=	date("Y-m-d");
?>
<TITLE><?php echo $docTitle;?></TITLE>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" style="css/text" rel="stylesheet">
</head>
<body topmargin="0">
<div class="mainDiv">
<center>

<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL?>/css/ddlevelsmenu-base.css" />
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/css/ddlevelsmenu-topbar.css" />
<script type="text/javascript" src="<?php echo SITE_URL;?>/script/ddlevelsmenu.js"></script>
<table cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
	<tr bgcolor="#f0f0f0">
		<td width="40%">
			<a href="<?php echo SITE_URL;?>"><img src="<?php echo SITE_URL_EMPLOYEES;?>/images/logo.jpg" border="0" width="200" height="100"></a>
		</td>
		<td width="35%" class="heading">EMPLOYEE AREA</td>
		<td valign="bottom" class="title">
			YOUR IP : <?php echo VISITOR_IP_ADDRESS;?>
			<br>
			CURRENT DATE : <?php echo showDate($nowDateIndia);?>
		</td>
	</tr>
	<tr>
		<td colspan="3" bgcolor="#f0f0f0" height="5"></td>
	</tr>
	<tr height="25" bgcolor="#373737">
		<td colspan="3" align="right">
			<font class="text4">Welcome <?php echo $s_employeeName;?>,</font> <a href="<?php echo SITE_URL_EMPLOYEES;?>/logout.php" class="link_style4">Logout</a>&nbsp;&nbsp;
		</td>
	</tr>
</table>
<table width="98%" border="0" align="center" cellpadding="4" cellspacing="2" valign="top">
	<tr>
		<td colspan="3" class="textstyle1"><b>COMPLETE YOUR BREAK TIME</b></td>
	</tr>
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
	<tr>
		<td colspan="3" class="textstyle1"><b>You are in break from <?php echo $breakTime;?> Hrs of <?php echo $breakDate;?> for the following reason- </b></td>
	</tr>
	<tr>
		<td class="textstyle1">
			<?php echo nl2br($breakTakingFor);?>
		</td>
	</tr>
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
	<tr>
		<td colspan="3">
			<a href="javascript:unmarkBreak()" class="link_style13">Click Here To Complete Your Break Time</a>
		</td>
	</tr>
	<tr>
		<td colspan="3" height="100"></td>
	</tr>
</table>
<?php
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>