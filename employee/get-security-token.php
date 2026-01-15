<?php
	ob_start();
	session_start();
	ini_set('display_errors', 1);
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
	$a_managerEmails			=	$commonClass->getMangersEmails();
	$employeeSecurityCode		=	EMPLOYEE_SPECIAL_SECURITY_CODE;
	$formTopText				=	"Enter Employee Registration Email or Login ID To Get Security Token";
	$requestTokenFromIP			=	VISITOR_IP_ADDRESS;
	$requestTokenIpCountry		=	"";
	$requestTokenIpRegion		=	"";
	$requestTokenIpCity			=	"";
	$requestTokenIpZipCode		=	"";
	$requestTokenIpLatitude		=	"";
	$requestTokenIpLongitude	=	"";
	$requestTokenIpISP			=	"";
	
	$requestMadeThroughEmail	=	0;
	$requestMadeThroughId		=	0;
	
	// Check if this is an AJAX request
	$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	
	if(isset($_REQUEST['formSubmitted']))
	{
		extract($_REQUEST);
		$email		=	trim($email);
		
		if(empty($email))
		{
			$validator ->setError("Please enter your email.");
		}
		if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$validator->setError("Your email is invalid.");
		}
		if(!empty($email) && !$result	=	$employeeObj->getForgotPasswordEmail($email,0))
		{
			$validator ->setError("Email entered by you doesn't exists.");
		}
		$dataValid					=	$validator ->isDataValid();
		if($dataValid)
		{
			if($result				=	$employeeObj->getForgotPasswordEmail($email,0))
			{
				if($ipLattitudeLocationCity	=	getIPDetailsWithAlternateFunctions($requestTokenFromIP))
				{
					$requestTokenIpCountry	=	$ipLattitudeLocationCity['country'];
					$requestTokenIpRegion	=	$ipLattitudeLocationCity['region'];
					$requestTokenIpCity		=	$ipLattitudeLocationCity['city'];
					$requestTokenIpZipCode	=	$ipLattitudeLocationCity['zipcode'];
					$requestTokenIpLatitude	=	$ipLattitudeLocationCity['latitude'];
					$requestTokenIpLongitude=	$ipLattitudeLocationCity['longitude'];
					$requestTokenIpISP		=	$ipLattitudeLocationCity['ipisp'];
				}
				
				$row				=	mysqli_fetch_assoc($result);
				$loginId			=	$row['employeeId'];
				$email				=	$row['email'];
				$name				=	$row['fullName'];

				$employeeType		=	$employeeObj->isPDFEmployee($loginId);

				if($employeeType	==	1)
				{
					$employeeSecurityCode =	$employeeObj->getEmployeeOwnSecurityCode($loginId);
				}

				$employeeObj->trackEmployeePasswordSecurityTracking($loginId,1,0,$requestMadeThroughEmail,$requestMadeThroughId,$email,$requestTokenFromIP,$requestTokenIpCity,$requestTokenIpRegion,$requestTokenIpZipCode,$requestTokenIpCountry,$requestTokenIpISP);
				
				///////////////////START OF SENDING EMAIL BLOCK//////////////
				include(SITE_ROOT   .   "/classes/email-templates.php");
				$emailObj			=	new emails();

				$a_templateData		=	array("{name}"=>$name,"{securityToken}"=>$employeeSecurityCode);
		
				$uniqueTemplateName	=	"TEMPLATE_SENDING_EMPLOYEE_SECURITY_TOKEN";
				$toEmail			=	$email;

				include(SITE_ROOT	.	"/includes/sending-dynamic-admin-emails.php");

				if(!empty($a_mainManagerEmail))
				{
					$emailType		=	$name." request for security token";

					$sendingMessageEmailText	=	$name." asking for employee security token while login with ieIMPACT employee area at ".showdate($nowDateIndia)." IST ".$nowTimeIndia." Hrs from IP address : <b>".$requestTokenFromIP."</b>, IP Country : <b>".$requestTokenIpCountry."</b>, IP Region : <b>".$requestTokenIpRegion."</b>, IP City : <b>".$requestTokenIpCity."</b>, IP Zip Code : <b>".$requestTokenIpZipCode."</b>, IP ISP : <b>".$requestTokenIpISP."</b>";

					foreach($a_mainManagerEmail as $k=>$value)
					{
						list($managerEmail,$managerName)	=	explode("|",$value);


						$a_templateSubject	=	array("{emailType}"=>$emailType);

						$a_templateData		=	array("{name}"=>$managerName,"{sendingMessageEmailText}"=>$sendingMessageEmailText,"{emailType}"=>$emailType);

						$uniqueTemplateName	=	"TEMPLATE_SENDING_VARIOUS_INFOMATION_MANAGER";
						$toEmail			=	$managerEmail;

						include(SITE_ROOT."/includes/sending-dynamic-admin-emails.php");
					}
				}

				///////////////////END OF SENDING EMAIL BLOCK////////////////
			}

			// Return success message
			if($isAjax) {
				header('Content-Type: application/json');
				echo json_encode(array(
					'success' => true,
					'message' => 'We are sending you an email with ieIMPACT - Security Token. If you do not find the email in your inbox, please check your spam filter or bulk email folder.'
				));
				exit;
			}
		}
		else
		{
			if($isAjax) {
				header('Content-Type: application/json');
				echo json_encode(array(
					'success' => false,
					'errors' => $validator->getErrors()
				));
				exit;
			}
		}
	}
	
	// If AJAX request without form submission, return modal HTML
	if($isAjax && !isset($_REQUEST['formSubmitted'])) {
		include(SITE_ROOT_EMPLOYEES . "/forms/modal-get-security-token.php");
		exit;
	}
	
	// Legacy support - full page
	if(!$isAjax) {
?>
<!DOCTYPE html>
<html>
<head>
<title>Get Security Token</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="/new-favicon.ico" type="image/x-icon" />
<style>
	body {
		margin: 0;
		padding: 20px;
		font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		min-height: 100vh;
		display: flex;
		align-items: center;
		justify-content: center;
	}
</style>
</head>
<body>
	<?php
		if(isset($_REQUEST['formSubmitted']) && $dataValid) {
			echo '<div style="background: white; padding: 30px; border-radius: 10px; max-width: 500px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">';
			echo '<h2 style="color: #667eea; margin-bottom: 20px;">Email Sent!</h2>';
			echo '<p style="color: #666; line-height: 1.6;">We are sending you an email with ieIMPACT - Security Token. If you do not find the email in your inbox, please check your spam filter or bulk email folder.</p>';
			echo '<button onclick="window.close()" style="margin-top: 20px; padding: 10px 30px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer;">Close</button>';
			echo '</div>';
			echo '<script>setTimeout(function(){ if(window.opener) window.close(); }, 5000);</script>';
		} else {
			include(SITE_ROOT_EMPLOYEES . "/forms/modal-get-security-token.php");
		}
	?>
</body>
</html>
<?php
	}
?>
