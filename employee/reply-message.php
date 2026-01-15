<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/session-vars.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT . "/classes/validate-fields.php");
	include(SITE_ROOT_EMPLOYEES . "/includes/check-login.php");
	include(SITE_ROOT_EMPLOYEES . "/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	."/includes/common-array.php");
	$employeeObj	=	new employee();
	$validator		=	new validate();
	$showForm		=	false;
	$messageId		=	0;
	$title			=	"";
	$message		=	"";
	$errorMsg		=	"";
	$replyText		=	"";
	
	if(isset($_GET['messageId']))
	{
		$messageId	=	(int)$_GET['messageId'];
		
		$query		=	"SELECT * FROM employee_messages where messageId=$messageId AND employeeId=$s_employeeId AND isReplied=0";
		$result	=	dbQuery($query);
		if(mysqli_num_rows($result))
		{
			$row			=	mysqli_fetch_assoc($result);
			$title			=	$row['title'];
			$message		=	$row['message'];
			$addedOn		=	showDate($row['addedOn']);
			
			$title			=	stripslashes($title);
			$message		=	stripslashes($message);
			$message		=	nl2br($message);
			
			$showForm		=	true;
		}
	}

	$form		=	SITE_ROOT_EMPLOYEES . "/forms/reply-message.php";
?>
<script type="text/javascript">
	function reflectChange()
	{
		window.opener.location.reload();
	}
</script>
<html>
<head>
<title>
	Reply Message
</title>
<link rel='stylesheet' type='text/css' href='<?php echo SITE_URL_EMPLOYEES?>/css/style-sheet.css'></link>
</head>
<body>

	<?php
		if($showForm)
		{
			if(isset($_POST['formSubmitted']))
			{
				extract($_POST);
				$replyText	=	trim($replyText);
				$replyText	=	makeDBSafe($replyText);
				if(empty($replyText))
				{
					$errorMsg		=	"Please enter reply text !!";
				}
				if(empty($errorMsg))
				{
				
					dbQuery("UPDATE employee_messages SET isReplied=1,replyText='$replyText',repliedOn='".CURRENT_DATE_INDIA."' WHERE messageId=$messageId AND employeeId=$s_employeeId AND isReplied=0");

					echo "<table width='95%' align='center' border='0' height='70'><tr><td class='text1'>Successfully added replied to notice . </td></tr></table>";

					echo "<script type='text/javascript'>reflectChange();</script>";

					echo "<script>setTimeout('window.close()',2000)</script>";
				}
				else
				{
					include($form);
				}
			}
			else
			{
				include($form);
			}
		}
		else
		{
			echo "<table width='95%' align='center' border='0' height='70 ><tr><td class='error'><b>Trying To Open An Invalid Page.</b> </td></tr></table>";
			
			//echo "<script>setTimeout('window.close()',1000)</script>";
		}
	?>
<br>
<center>
	<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
</center>
</body>
</html>