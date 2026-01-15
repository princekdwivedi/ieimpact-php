<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	include(SITE_ROOT			.   "/classes/validate-fields.php");
	include(SITE_ROOT			.   "/includes/common-array.php");
	include(SITE_ROOT			.   "/includes/send-mail.php");
	include(SITE_ROOT			.	"/classes/common.php");
	include(SITE_ROOT_EMPLOYEES .	"/classes/employee.php");
	include(SITE_ROOT_EMPLOYEES	.	"/includes/common-array.php");
	$employeeObj				=	new employee();
	$validator					=   new validate();
	$commonClass				=	new common();
	$email						=	"";
	$loginId					=	0;
	$formTopText				=	"Enter Employee Registration Email To Get New O.T.P.";
	$requestMadeThroughEmail	=	0;
	$requestMadeThroughId		=	0;

	
	$form						=	SITE_ROOT_EMPLOYEES  . "/forms/forgot-password.php";
?>
<html>
<head>
<title>Get Security Token</title>
<link href="<?php echo SITE_URL_EMPLOYEES;?>/css/style-sheet.css" rel="stylesheet" style="text/css">
<link rel="shortcut icon" href="/new-favicon.ico" type="image/x-icon" />
</head>
<body>
	<center>
		<?php
			if(isset($_REQUEST['formSubmitted']))
			{
				extract($_REQUEST);
				$email		=	trim($email);
				
				if(empty($email))
				{
					$validator ->setError("Please enter your email.");
				}
				if(!empty($email))
				{
					$validator ->checkField($email,"E","Please write a valid email.");
				}
				$dataValid					=	$validator ->isDataValid();
				if($dataValid)
				{
					$currentDateTime	=	CURRENT_DATE_INDIA." ".CURRENT_TIME_INDIA;
					$query				=	"SELECT * FROM employee_details WHERE email='$email' AND isActive=1 AND hasPdfAccess=1";
					$result				=	dbQuery($query);
					if(mysql_num_rows($result)){
						
						$row			=	mysql_fetch_assoc($result);
						$fullName		=	$row['fullName'];
						$employeeEmail	=	$row['email'];
						$employeePhone	=	$row['mobile'];
						$loginId	    =	$row['employeeId'];
						
						if(empty($employeePhone)){
							$employeePhone	=	"";
						}

						include(SITE_ROOT_EMPLOYEES."/includes/sending-otp.php");

						echo "<br><br><table width='95%' align='center' border='0'><tr><td class='smalltext6'> We are sending you an SMS and email with O.T.P. to login in ieIMPACT.<br> If you do not find the email in your inbox,<br> please check your spam filter or bulk email folder. </td></tr></table>";
						echo "<script>setTimeout('window.close()',5500)</script>";
					}
					else{
						echo "<font color='#ff0000;'>You have entered wrong email address</font>";
						include($form);
					}
					
				}
				else
				{
					echo $validator->getErrors();
					include($form);
				}
			}
			else
			{
				include($form);
			}
		?>
		<br><br>
		<a href="javascript:window.close()" style="cursor:hand"><font color="#ff0000"><b>Close</b></font><a>
	</center>
</body>
</html>