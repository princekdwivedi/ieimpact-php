<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);
	include("../root.php");
	$isResetPasswordPage			   =	1;
	include(SITE_ROOT_EMPLOYEES		   . "/includes/top.php");
	
	// Hide the top header section for reset password page
	echo '<style>
		/* Hide the EMPLOYEE AREA header row */
		.mainDiv > center > table[cellpadding="0"][cellspacing="0"] > tbody > tr[bgcolor="#f0f0f0"]:first-child,
		.mainDiv > center > table[cellpadding="0"][cellspacing="0"] > tr[bgcolor="#f0f0f0"]:first-child {
			display: none !important;
		}
		/* Also hide using JavaScript as fallback */
	</style>
	<script type="text/javascript">
		document.addEventListener("DOMContentLoaded", function() {
			var tables = document.querySelectorAll("table[cellpadding=\"0\"][cellspacing=\"0\"][align=\"center\"][width=\"100%\"]");
			if(tables.length > 0) {
				var firstTable = tables[0];
				var rows = firstTable.getElementsByTagName("tr");
				if(rows.length > 0 && rows[0].getAttribute("bgcolor") == "#f0f0f0") {
					rows[0].style.display = "none";
				}
			}
		});
	</script>';
	
	include(SITE_ROOT_EMPLOYEES		   . "/classes/employee.php");
	include(SITE_ROOT				   . "/classes/validate-fields.php");
	$form					=	SITE_ROOT_EMPLOYEES. "/forms/reset-password.php";
	$employeeObj			=	new employee();
	$validator				=	new validate();
	$errorMsg				=	"";
	$resetError				=	"";
	$newPasswordCode		=	"";
	$employeeId				=	0;
	

	// Session validation: Check if user is already logged in
	if(isset($_SESSION['employeeId']) && !empty($_SESSION['employeeId']))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES ."/employee-details.php");
		exit();
	}
	
	// Session validation: Check if MT employee is logged in
	if(isset($_SESSION['mtemployeeId']) && !empty($_SESSION['mtemployeeId']))
	{
		ob_clean();
		header("Location: ".SITE_URL_EMPLOYEES);
		exit();
	}

	if(isset($_SESSION['hasChangedPassword']))
	{
		$hasChangedPassword	=	$_SESSION['hasChangedPassword'];
	}
	else
	{
		$hasChangedPassword	=	0;
	}
	
	// Session validation for reset password code
	if(empty($hasChangedPassword))
	{
		if(isset($_GET['code']) && !empty($_GET['code']))
		{
			$newPasswordCode	=	trim($_GET['code']);
			
			// Validate code format (basic validation)
			if(strlen($newPasswordCode) < 10)
			{
				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES);
				exit();
			}

			// Query with additional security checks
			$query				=	"SELECT employeeId, email, fullName, isActive FROM employee_details WHERE newPasswordCode='$newPasswordCode' AND isRequestForPassword=1 AND isActive=1";
			$result				=	dbQuery($query);
			if(mysqli_num_rows($result))
			{
				$row			=	mysqli_fetch_assoc($result);
				$employeeId		=	$row['employeeId'];
				$email			=	$row['email'];
				
				// Store validated code and employee ID in session for form submission validation
				$_SESSION['resetPasswordCode']		=	$newPasswordCode;
				$_SESSION['resetPasswordEmployeeId']	=	$employeeId;
			}
			else
			{
				// Invalid or expired code - clear any existing session data
				unset($_SESSION['resetPasswordCode']);
				unset($_SESSION['resetPasswordEmployeeId']);
				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES);
				exit();
			}

		}
		else
		{
			// No code provided - redirect to login
			unset($_SESSION['resetPasswordCode']);
			unset($_SESSION['resetPasswordEmployeeId']);
			ob_clean();
			header("Location: ".SITE_URL_EMPLOYEES);
			exit();
		}
	}
	
	$urlRef			    =	SITE_URL_EMPLOYEES."/reset-password.php";
	if(!empty($hasChangedPassword))
	{
?>
<style>
	.success-container {
		position: relative;
		min-height: calc(100vh - 200px);
		display: flex;
		align-items: center;
		justify-content: center;
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		padding: 40px 20px;
		margin: -20px -20px 0 -20px;
		font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
	}

	.success-wrapper {
		width: 100%;
		max-width: 500px;
		background: #ffffff;
		border-radius: 20px;
		box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
		overflow: hidden;
		animation: slideUp 0.5s ease-out;
		padding: 40px 30px;
		text-align: center;
	}

	@keyframes slideUp {
		from {
			opacity: 0;
			transform: translateY(30px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	.success-icon {
		font-size: 64px;
		color: #22c55e;
		margin-bottom: 20px;
	}

	.success-message {
		color: #333;
		font-size: 18px;
		line-height: 1.6;
		margin-bottom: 30px;
	}

	.success-link {
		display: inline-block;
		padding: 14px 32px;
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: #ffffff;
		text-decoration: none;
		border-radius: 10px;
		font-weight: 600;
		font-size: 16px;
		transition: all 0.3s ease;
		box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
	}

	.success-link:hover {
		transform: translateY(-2px);
		box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
	}

	@media (max-width: 768px) {
		.success-wrapper {
			padding: 30px 20px;
		}

		.success-icon {
			font-size: 48px;
		}

		.success-message {
			font-size: 16px;
		}
	}
</style>
<div class="success-container">
	<div class="success-wrapper">
		<div class="success-icon">✓</div>
		<div class="success-message">
			You have successfully reset your password.<br>
			Please <a href="<?php echo SITE_URL_EMPLOYEES;?>" style="color: #667eea; text-decoration: underline;">Click here to login</a> into employee area with your new password.
		</div>
	</div>
</div>
<?php
		unset($_SESSION['hasChangedPassword']);
	}
	else
	{
		if(isset($_REQUEST['formSubmitted']))
		{
			// Session validation: Verify code and employee ID from session
			$sessionCode			=	isset($_SESSION['resetPasswordCode']) ? $_SESSION['resetPasswordCode'] : '';
			$sessionEmployeeId		=	isset($_SESSION['resetPasswordEmployeeId']) ? $_SESSION['resetPasswordEmployeeId'] : 0;
			
			if(empty($sessionCode) || empty($sessionEmployeeId))
			{
				// Session expired or invalid - redirect to login
				unset($_SESSION['resetPasswordCode']);
				unset($_SESSION['resetPasswordEmployeeId']);
				ob_clean();
				header("Location: ".SITE_URL_EMPLOYEES);
				exit();
			}
			
			// Get code from GET parameter or POST hidden field (should still be in URL or form)
			$newPasswordCode	=	isset($_GET['code']) ? trim($_GET['code']) : (isset($_POST['resetCode']) ? trim($_POST['resetCode']) : '');
			
			// Validate that session code matches code from GET/POST
			if(empty($newPasswordCode) || $newPasswordCode != $sessionCode)
			{
				// Ensure code is set for form display
				if(empty($newPasswordCode) && isset($_GET['code']))
				{
					$newPasswordCode = trim($_GET['code']);
				}
				elseif(empty($newPasswordCode) && isset($_POST['resetCode']))
				{
					$newPasswordCode = trim($_POST['resetCode']);
				}
				$validator->setError("Invalid reset code. Please use the link from your email.");
				$errorMsg = $validator->getErrors();
				include($form);
			}
			else
			{
				extract($_REQUEST);
				$password			=	trim($password);
				$rePassword			=	trim($rePassword);
				$passwordLength		=	strlen($password);

				$validator ->checkField($password,"","Please type password !!");
				if(!empty($password) && $passwordLength < 5)
				{
					$validator ->setError("Your password is too short !!");
				}
				$validator ->checkField($rePassword,"","Please re-type password !!");
				if(!empty($password) && !empty($rePassword) && $password != $rePassword)
				{
					$validator ->setError("New password and re-typed password does not match !!");
				}
				$dataValid	 =	$validator ->isDataValid();
				if($dataValid)
				{
					// Final validation: Verify code and employee still match in database
					$verifyQuery	=	"SELECT employeeId FROM employee_details WHERE newPasswordCode='$newPasswordCode' AND employeeId=$sessionEmployeeId AND isRequestForPassword=1 AND isActive=1";
					$verifyResult	=	dbQuery($verifyQuery);
					if(mysqli_num_rows($verifyResult))
					{
						$options 	=   array('cost' => 12);
						$newPassword=	password_hash($password, PASSWORD_BCRYPT, $options);

						dbQuery("UPDATE employee_details SET password='$newPassword',isRequestForPassword=0,newPasswordCode='',passwordChangeOn='".CURRENT_DATE_INDIA."',passwordChangeTime='".CURRENT_TIME_INDIA."' WHERE newPasswordCode='$newPasswordCode' AND employeeId=$sessionEmployeeId");

						dbQuery("DELETE FROM employee_trusted_devices WHERE employeeId=$sessionEmployeeId");
						


						// Clear session variables
						unset($_SESSION['resetPasswordCode']);
						unset($_SESSION['resetPasswordEmployeeId']);
						$_SESSION['hasChangedPassword']	=	1;

						ob_clean();
						header("Location: ".SITE_URL_EMPLOYEES."/reset-password.php");
						exit();
					}
					else
					{
						// Code or employee ID mismatch - security issue
						unset($_SESSION['resetPasswordCode']);
						unset($_SESSION['resetPasswordEmployeeId']);
						// Ensure code is set for form display
						if(empty($newPasswordCode) && isset($_GET['code']))
						{
							$newPasswordCode = trim($_GET['code']);
						}
						elseif(empty($newPasswordCode) && isset($_POST['resetCode']))
						{
							$newPasswordCode = trim($_POST['resetCode']);
						}
						$validator->setError("Invalid or expired reset code. Please request a new password reset.");
						$errorMsg = $validator->getErrors();
						include($form);
					}
				}
				else
				{
					// Ensure code is set for form display
					if(empty($newPasswordCode) && isset($_GET['code']))
					{
						$newPasswordCode = trim($_GET['code']);
					}
					elseif(empty($newPasswordCode) && isset($_POST['resetCode']))
					{
						$newPasswordCode = trim($_POST['resetCode']);
					}
					$errorMsg	 =	$validator ->getErrors();
					include($form);
				}
			}
		}
		else
		{
			// Ensure code and employee ID are set in session for display
			if(isset($_GET['code']) && !empty($_GET['code']))
			{
				$newPasswordCode	=	trim($_GET['code']);
				if(isset($_SESSION['resetPasswordCode']) && $_SESSION['resetPasswordCode'] == $newPasswordCode)
				{
					$employeeId = isset($_SESSION['resetPasswordEmployeeId']) ? $_SESSION['resetPasswordEmployeeId'] : 0;
				}
			}
			include($form);
		}
	}
	include(SITE_ROOT_EMPLOYEES . "/includes/bottom.php");
?>