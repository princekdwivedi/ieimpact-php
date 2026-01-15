<style>
	/* Modern Login Page Styles */
	.login-container {
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

	.login-wrapper {
		width: 100%;
		max-width: 450px;
		background: #ffffff;
		border-radius: 20px;
		box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
		overflow: hidden;
		animation: slideUp 0.5s ease-out;
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

	.login-header {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		padding: 30px 30px 40px 30px;
		text-align: center;
		color: #ffffff;
	}

	.logo-container {
		margin-bottom: 20px;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.login-logo {
		max-width: 200px;
		height: auto;
		background: rgba(255, 255, 255, 0.1);
		padding: 10px;
		border-radius: 10px;
		box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
	}

	.login-header h1 {
		font-size: 28px;
		font-weight: 600;
		margin-bottom: 10px;
		letter-spacing: 0.5px;
	}

	.login-header p {
		font-size: 14px;
		opacity: 0.9;
	}

	.login-body {
		padding: 40px 30px;
	}

	.form-group {
		margin-bottom: 25px;
		position: relative;
	}

	.form-group label {
		display: block;
		font-size: 14px;
		font-weight: 600;
		color: #333;
		margin-bottom: 8px;
		letter-spacing: 0.3px;
		text-align: center;
		width: 85%;
		max-width: 350px;
		margin-left: auto;
		margin-right: auto;
	}

	.form-group input[type="text"],
	.form-group input[type="email"],
	.form-group input[type="password"] {
		width: 85%;
		max-width: 350px;
		padding: 14px 16px;
		border: 2px solid #e0e0e0;
		border-radius: 10px;
		font-size: 15px;
		transition: all 0.3s ease;
		background: #f8f9fa;
		color: #333;
		font-family: inherit;
		margin: 0 auto;
		display: block;
	}

	.form-group input:focus {
		outline: none;
		border-color: #667eea;
		background: #ffffff;
		box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
	}

	.form-group input::placeholder {
		color: #999;
	}

	.input-icon {
		position: relative;
	}

	.input-icon::before {
		content: '';
		position: absolute;
		left: 16px;
		top: 50%;
		transform: translateY(-50%);
		width: 20px;
		height: 20px;
		background-size: contain;
		opacity: 0.5;
	}

	.form-options {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 25px;
		flex-wrap: wrap;
		gap: 10px;
	}

	.remember-me {
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.remember-me input[type="checkbox"] {
		width: 18px;
		height: 18px;
		cursor: pointer;
		accent-color: #667eea;
	}

	.remember-me label {
		font-size: 14px;
		color: #666;
		cursor: pointer;
		margin: 0;
	}

	.forgot-links {
		display: flex;
		gap: 15px;
		flex-wrap: wrap;
	}

	.forgot-links a {
		font-size: 13px;
		color: #667eea;
		text-decoration: none;
		transition: color 0.3s ease;
		font-weight: 500;
	}

	.forgot-links a:hover {
		color: #764ba2;
		text-decoration: underline;
	}

	.error-message {
		background: #fee;
		border: 1px solid #fcc;
		border-radius: 8px;
		padding: 12px 16px;
		margin-bottom: 20px;
		color: #c33;
		font-size: 14px;
		line-height: 1.5;
	}

	.error-message .error {
		color: #c33;
		font-weight: 500;
	}

	.btn-group {
		display: flex;
		gap: 12px;
		margin-top: 10px;
	}

	.btn {
		flex: 1;
		padding: 14px 24px;
		border: none;
		border-radius: 10px;
		font-size: 16px;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.3s ease;
		font-family: inherit;
		letter-spacing: 0.5px;
		text-transform: uppercase;
	}

	.btn-primary {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: #ffffff;
		box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
	}

	.btn-primary:hover {
		transform: translateY(-2px);
		box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
	}

	.btn-primary:active {
		transform: translateY(0);
	}

	.btn-secondary {
		background: #f0f0f0;
		color: #666;
		border: 2px solid #e0e0e0;
	}

	.btn-secondary:hover {
		background: #e0e0e0;
		border-color: #d0d0d0;
	}

	.otp-section {
		background: #f8f9fa;
		border-radius: 10px;
		padding: 20px;
		margin-bottom: 25px;
		border-left: 4px solid #667eea;
	}

	.captcha-section {
		margin-bottom: 25px;
		display: flex;
		justify-content: center;
	}

	.security-token-hint {
		font-size: 12px;
		color: #666;
		margin-top: 5px;
		font-style: italic;
		text-align: center;
		width: 85%;
		max-width: 350px;
		margin-left: auto;
		margin-right: auto;
	}

	/* Responsive Design */
	@media (max-width: 768px) {
		.login-container {
			padding: 15px;
		}

		.login-wrapper {
			max-width: 100%;
			border-radius: 15px;
		}

		.login-header {
			padding: 25px 20px 30px 20px;
		}

		.login-header h1 {
			font-size: 24px;
		}

		.login-logo {
			max-width: 150px;
			padding: 8px;
		}

		.login-body {
			padding: 30px 20px;
		}

		.form-group {
			margin-bottom: 20px;
		}

		.form-options {
			flex-direction: column;
			align-items: flex-start;
		}

		.forgot-links {
			width: 100%;
			flex-direction: column;
			gap: 8px;
		}

		.btn-group {
			flex-direction: column;
		}

		.btn {
			width: 100%;
		}
	}

	@media (max-width: 480px) {
		.login-header h1 {
			font-size: 22px;
		}

		.login-header p {
			font-size: 13px;
		}

		.login-logo {
			max-width: 120px;
			padding: 6px;
		}

		.form-group input[type="text"],
		.form-group input[type="email"],
		.form-group input[type="password"] {
			width: 90%;
			max-width: 100%;
			padding: 12px 14px;
			font-size: 14px;
		}
	}

	/* Loading state */
	.btn-primary:disabled {
		opacity: 0.6;
		cursor: not-allowed;
	}

	/* Animation for error messages */
	@keyframes shake {
		0%, 100% { transform: translateX(0); }
		25% { transform: translateX(-10px); }
		75% { transform: translateX(10px); }
	}

	.error-message {
		animation: shake 0.5s ease;
	}

	/* Tooltip styles */
	span.dropt {
		border-bottom: thin dotted;
		background: #ffeedd;
		cursor: help;
	}

	span.dropt:hover {
		text-decoration: none;
		background: #ffffff;
		z-index: 6;
	}

	span.dropt span {
		position: absolute;
		left: -9999px;
		margin: 20px 0 0 0px;
		padding: 8px 12px;
		border-style: solid;
		border-color: #333;
		border-width: 1px;
		border-radius: 5px;
		background: #fff;
		box-shadow: 0 2px 10px rgba(0,0,0,0.2);
		z-index: 6;
		max-width: 300px;
		font-size: 12px;
		line-height: 1.4;
	}

	span.dropt:hover span {
		left: auto;
		right: 0;
		margin: 20px 0 0 0px;
	}
</style>

<script type="text/javascript" src="<?php echo SITE_URL_EMPLOYEES;?>/scripts/validate.js"></script>
<script type="text/javascript">
function setFocus() {
	form1 = document.loginEmployee;
	if(form1.loginEmail) {
		form1.loginEmail.focus();
	}
}

function resetField() {
	form1 = document.loginEmployee;
	if(form1.loginEmail) form1.loginEmail.value = "";
	if(form1.password) form1.password.value = "";
	if(form1.securityToken) form1.securityToken.value = "";
	if(form1.rememberCheckPass) form1.rememberCheckPass.checked = false;
	if(form1.otpCode) form1.otpCode.value = "";
	if(form1.loginOtpCode) form1.loginOtpCode.value = "";
	return false;
}

function checkValidLogin() {
	form1 = document.loginEmployee;
	
	// Check if login OTP is required (after email/password/security token validation)
	var isLoginOtpRequired = form1.isLoginOtpRequired && form1.isLoginOtpRequired.value == "1";
	
	// Skip email, password, and security token validation if OTP is required (they're already validated)
	if(!isLoginOtpRequired) {
		if(!form1.loginEmail || form1.loginEmail.value == "") {
			alert("Please enter your email.");
			if(form1.loginEmail) form1.loginEmail.focus();
			return false;
		}
		
		if(form1.loginEmail.value != "") {
			if(typeof isEmail !== 'undefined' && isEmail(form1.loginEmail.value) == false) {
				alert("Entered email is invalid.");
				form1.loginEmail.focus();
				return false;
			}
		}
		
		if(!form1.password || form1.password.value == "") {
			alert("Enter your password.");
			if(form1.password) form1.password.focus();
			return false;
		}
		
		if(!form1.securityToken || form1.securityToken.value == "" || form1.securityToken.value == "0") {
			alert("Please enter security token.");
			if(form1.securityToken) form1.securityToken.focus();
			return false;
		}
		
		if(form1.isRequiredOtp && form1.isRequiredOtp.value == 1) {
			if(!form1.otpCode || form1.otpCode.value == "" || form1.otpCode.value == "0" || form1.otpCode.value == " ") {
				alert("Enter your O.T.P.");
				if(form1.otpCode) form1.otpCode.focus();
				return false;
			}
		}
	}
	
	// Check login OTP if required
	if(isLoginOtpRequired) {
		if(!form1.loginOtpCode || form1.loginOtpCode.value == "" || form1.loginOtpCode.value == "0") {
			alert("Please enter the OTP sent to your email.");
			if(form1.loginOtpCode) form1.loginOtpCode.focus();
			return false;
		}
	}
	
	// Disable submit button and show processing state
	var submitBtn = document.getElementById('submitBtn');
	if(submitBtn) {
		submitBtn.disabled = true;
		submitBtn.style.opacity = '0.6';
		submitBtn.style.cursor = 'not-allowed';
		var originalText = submitBtn.innerHTML;
		submitBtn.innerHTML = 'Processing...';
		
		// Re-enable button after 10 seconds as a safety measure (in case form doesn't submit)
		setTimeout(function() {
			if(submitBtn && submitBtn.disabled) {
				submitBtn.disabled = false;
				submitBtn.style.opacity = '1';
				submitBtn.style.cursor = 'pointer';
				submitBtn.innerHTML = originalText;
			}
		}, 10000);
	}
	
	return true;
}

function forgotSecurityToken() {
	// Remove any existing modal first
	var existingModal = document.getElementById('getSecurityTokenModal');
	if(existingModal) {
		existingModal.remove();
	}
	
	// Load modal via AJAX
	var xhr = new XMLHttpRequest();
	xhr.open('GET', '<?php echo SITE_URL_EMPLOYEES;?>/get-security-token.php', true);
	xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	
	xhr.onload = function() {
		if(xhr.status === 200) {
			// Create modal container
			var modalContainer = document.createElement('div');
			modalContainer.innerHTML = xhr.responseText;
			document.body.appendChild(modalContainer);
			
			// Execute any scripts in the modal
			var scripts = modalContainer.getElementsByTagName('script');
			for(var i = 0; i < scripts.length; i++) {
				var script = scripts[i];
				var newScript = document.createElement('script');
				newScript.textContent = script.textContent;
				document.body.appendChild(newScript);
				script.parentNode.removeChild(script);
			}
		} else {
			alert("Error loading security token form. Please try again.");
		}
	};
	
	xhr.onerror = function() {
		alert("Network error. Please check your connection and try again.");
	};
	
	xhr.send();
}

function forgotPassword() {
	// Remove any existing modal first
	var existingModal = document.getElementById('forgotPasswordModal');
	if(existingModal) {
		existingModal.remove();
	}
	
	// Load modal via AJAX
	var xhr = new XMLHttpRequest();
	xhr.open('GET', '<?php echo SITE_URL_EMPLOYEES;?>/forgot-password.php', true);
	xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	
	xhr.onload = function() {
		if(xhr.status === 200) {
			// Create modal container
			var modalContainer = document.createElement('div');
			modalContainer.innerHTML = xhr.responseText;
			document.body.appendChild(modalContainer);
			
			// Execute any scripts in the modal
			var scripts = modalContainer.getElementsByTagName('script');
			for(var i = 0; i < scripts.length; i++) {
				var script = scripts[i];
				var newScript = document.createElement('script');
				newScript.textContent = script.textContent;
				document.body.appendChild(newScript);
				script.parentNode.removeChild(script);
			}
		} else {
			alert("Error loading forgot password form. Please try again.");
		}
	};
	
	xhr.onerror = function() {
		alert("Network error. Please check your connection and try again.");
	};
	
	xhr.send();
}

function regenerateOtp() {
	path = "<?php echo SITE_URL_EMPLOYEES?>/get-new-otp.php";
	prop = "toolbar=no,scrollbars=yes,width=500,height=300,top=50,left=100";
	window.open(path,'',prop);
}

function checkForNumber() {
	k = (document.all)?event.keyCode : arguments.callee.caller.arguments[0].which;
	if(k == 8 || k == 0) {
		return true;
	}
	if(k >= 48 && k <= 57) {
		return true;
	} else {
		return false;
	}
}

window.onload = setFocus;
</script>

<div class="login-container">
	<div class="login-wrapper">
		<div class="login-header">
			<div class="logo-container">
				<img src="<?php echo SITE_URL;?>/images/logo-bg.gif" alt="ieIMPACT Logo" class="login-logo" title="Innovation. Excellence. i.e. IMPACT">
			</div>
			<h1><?php
				if(isset($loginFormHeadingText) && !empty($loginFormHeadingText)){
					echo htmlspecialchars($loginFormHeadingText);
				} else {
					echo "Employee Login";
				}
			?></h1>
			<p>Welcome to ieIMPACT Employee Section</p>
		</div>
		
		<div class="login-body">
			<form name="loginEmployee" action="" method="POST" onsubmit="return checkValidLogin();">
				
				<?php
					if(!empty($error) && $error == 4) {
				?>
					<div class="error-message">
						<strong>Account Locked</strong><br>
						For your security, we have locked your account due to too many attempts to Log In. Please contact ieIMPACT to unlock your account.
						<span class="dropt" title="" style="cursor:pointer;"> More Info
							<span>After a limited number of failed attempts to sign in to ieIMPACT, you will be temporarily locked out from trying to sign in. When your account is locked, you will not be able to sign in - even with the correct password. This lock lasts about an hour and will then clear on its own.</span>
						</span>
					</div>
				<?php
					} else {
						if(!empty($loginError) || !empty($errorMsg)) {
				?>
					<div class="error-message">
						<?php 
							if(!empty($errorMsg)) echo $errorMsg;
							if(!empty($loginError)) echo "<span class='error'>".$loginError."</span>";
						?>
					</div>
				<?php
						}
					}
				?>

				<?php
					// Only show email, password, and security token fields if OTP is NOT required
					if(!isset($isLoginOtpRequired) || $isLoginOtpRequired != 1) {
				?>
				<div class="form-group">
					<label for="loginEmail">Email Address</label>
					<input type="email" id="loginEmail" name="loginEmail" value="<?php echo htmlspecialchars($rememberEmail);?>" placeholder="Enter your email address" maxlength="100" required>
				</div>

				<div class="form-group">
					<label for="password">Password</label>
					<input type="password" id="password" name="password" value="<?php echo htmlspecialchars($rememberEmployeePass);?>" placeholder="Enter your password" required>
				</div>

				<div class="form-group">
					<label for="securityToken">Security Token</label>
					<input type="text" id="securityToken" name="securityToken" value="<?php echo htmlspecialchars($rememberSecurityToken);?>" placeholder="Enter security token" maxlength="5" onKeyPress="return checkForNumber();" required>
					<div class="security-token-hint">Need help? <a onclick="forgotSecurityToken()" style="color: #667eea; cursor: pointer; text-decoration: none;">How to get it</a></div>
				</div>
				<?php
					} else {
						// When OTP is required, keep the fields hidden but include them as hidden inputs to preserve values
				?>
				<input type="hidden" id="loginEmail" name="loginEmail" value="<?php echo htmlspecialchars(isset($rememberEmail) ? $rememberEmail : '');?>">
				<input type="hidden" id="password" name="password" value="<?php echo htmlspecialchars(isset($rememberEmployeePass) ? $rememberEmployeePass : '');?>">
				<input type="hidden" id="securityToken" name="securityToken" value="<?php echo htmlspecialchars(isset($rememberSecurityToken) ? $rememberSecurityToken : '');?>">
				<?php
					}
				?>

				<?php
					if($isRequiredOtp == 1) {
				?>
				<div class="otp-section">
					<div class="form-group" style="margin-bottom: 0;">
						<label for="otpCode">One-Time Password (O.T.P.)</label>
						<input type="text" id="otpCode" name="otpCode" value="<?php echo htmlspecialchars($otpCode);?>" placeholder="Enter OTP code" maxlength="6" onKeyPress="return checkForNumber();" required>
						<div class="security-token-hint">Didn't receive it? <a onclick="regenerateOtp()" style="color: #667eea; cursor: pointer; text-decoration: none;">Regenerate OTP</a></div>
					</div>
				</div>
				<?php
					}
					
					// Show login OTP field if OTP is required after successful email/password/security token validation
					if(isset($isLoginOtpRequired) && $isLoginOtpRequired == 1) {
				?>
				<div class="otp-section" style="background: #e8f4f8; border-left-color: #667eea;">
					<div class="form-group" style="margin-bottom: 0;">
						<label for="loginOtpCode">Login Verification OTP</label>
						<input type="text" id="loginOtpCode" name="loginOtpCode" value="" placeholder="Enter OTP sent to your email" maxlength="6" onKeyPress="return checkForNumber();" required>
						<div class="security-token-hint">An OTP has been sent to your registered email. Valid for 5 minutes.</div>
					</div>
				</div>
				<?php
					}
					
					if($showCaptcha == 1) {
				?>
				<div class="captcha-section">
					<div class="g-recaptcha" data-sitekey="<?php echo GOOGLE_RECAPTCHA_SECRET_KEY; ?>"></div>
					<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=en"></script>
				</div>
				<?php
					}
				?>

				<div class="form-options">
					<?php if(!isset($isLoginOtpRequired) || $isLoginOtpRequired != 1) { ?>
					<div class="remember-me">
						<input type="checkbox" id="rememberCheckPass" name="rememberCheckPass" value="1" <?php echo $pwdChecked;?>>
						<label for="rememberCheckPass">Remember me</label>
					</div>
					<?php } ?>
					<div class="forgot-links">
						<?php if(isset($isLoginOtpRequired) && $isLoginOtpRequired == 1) { ?>
							<a id="resendOtpLink" onclick="resendLoginOtp()" style="cursor: pointer;">Resend OTP</a>
						<?php } else { ?>
							<a onclick="forgotPassword()" style="cursor: pointer;">Forgot Password?</a>
						<?php } ?>
					</div>
				</div>

				<input type="hidden" name="showCaptcha" value="<?php echo $showCaptcha;?>">
				<input type="hidden" name="isRequiredOtp" value="<?php echo $isRequiredOtp;?>">
				<input type="hidden" name="isLoginOtpRequired" value="<?php echo isset($isLoginOtpRequired) ? $isLoginOtpRequired : 0;?>">
				<input type="hidden" name="formsubmitted" value="1">

				<div class="btn-group">
					<button type="submit" id="submitBtn" class="btn btn-primary"><?php echo (isset($isLoginOtpRequired) && $isLoginOtpRequired == 1) ? 'Submit' : 'Login'; ?></button>
					<button type="button" class="btn btn-secondary" onclick="return resetField();">Reset</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
// Add viewport meta tag for mobile responsiveness if not present
if (!document.querySelector('meta[name="viewport"]')) {
	var viewport = document.createElement('meta');
	viewport.name = 'viewport';
	viewport.content = 'width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes';
	document.getElementsByTagName('head')[0].appendChild(viewport);
}

// Ensure login container is properly displayed
window.addEventListener('DOMContentLoaded', function() {
	var loginContainer = document.querySelector('.login-container');
	if (loginContainer) {
		// Make sure body and html allow full height
		document.body.style.margin = '0';
		document.body.style.padding = '0';
		if (document.documentElement) {
			document.documentElement.style.height = '100%';
		}
	}
});

setFocus();
</script>

<script type="text/javascript">
function resendLoginOtp() {
	// Check if 1 minute has passed since last OTP request
	var lastOtpTime = localStorage.getItem('lastLoginOtpRequestTime');
	var currentTime = new Date().getTime();
	var oneMinute = 60 * 1000; // 1 minute in milliseconds
	
	if(lastOtpTime && (currentTime - parseInt(lastOtpTime)) < oneMinute) {
		var remainingSeconds = Math.ceil((oneMinute - (currentTime - parseInt(lastOtpTime))) / 1000);
		alert("Please wait " + remainingSeconds + " seconds before requesting a new OTP.");
		return false;
	}
	
	// Remove any existing modal first
	var existingModal = document.getElementById('resendOtpModal');
	if(existingModal) {
		existingModal.remove();
	}
	
	// Load modal via AJAX
	var xhr = new XMLHttpRequest();
	xhr.open('GET', '<?php echo SITE_URL_EMPLOYEES;?>/resend-login-otp.php', true);
	xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	
	xhr.onload = function() {
		if(xhr.status === 200) {
			// Create modal container and append to body
			var modalContainer = document.createElement('div');
			modalContainer.innerHTML = xhr.responseText;
			document.body.appendChild(modalContainer);
			
			// Store the time when OTP was requested
			localStorage.setItem('lastLoginOtpRequestTime', new Date().getTime());
			
			// Start countdown timer
			startResendOtpTimer();
			
			// Execute any scripts in the modal
			var scripts = modalContainer.getElementsByTagName('script');
			for(var i = 0; i < scripts.length; i++) {
				var script = scripts[i];
				var newScript = document.createElement('script');
				newScript.textContent = script.textContent;
				document.body.appendChild(newScript);
				script.parentNode.removeChild(script);
			}
		} else {
			alert("Error occurred. Please try again.");
		}
	};
	
	xhr.onerror = function() {
		alert("Network error. Please check your connection and try again.");
	};
	
	xhr.send();
}

var resendOtpTimerInterval = null;
function startResendOtpTimer() {
	// Clear any existing timer
	if(resendOtpTimerInterval) {
		clearInterval(resendOtpTimerInterval);
	}
	
	var resendOtpLink = document.getElementById('resendOtpLink');
	if(!resendOtpLink) return;
	
	var countdown = 60; // 1 minute in seconds
	resendOtpLink.style.pointerEvents = 'none';
	resendOtpLink.style.opacity = '0.5';
	resendOtpLink.style.cursor = 'not-allowed';
	
	var originalText = resendOtpLink.innerHTML;
	
	resendOtpTimerInterval = setInterval(function() {
		countdown--;
		if(countdown > 0) {
			resendOtpLink.innerHTML = 'Resend OTP (' + countdown + 's)';
		} else {
			clearInterval(resendOtpTimerInterval);
			resendOtpLink.innerHTML = originalText;
			resendOtpLink.style.pointerEvents = 'auto';
			resendOtpLink.style.opacity = '1';
			resendOtpLink.style.cursor = 'pointer';
			localStorage.removeItem('lastLoginOtpRequestTime');
		}
	}, 1000);
}

// Initialize timer on page load if needed
window.addEventListener('DOMContentLoaded', function() {
	var lastOtpTime = localStorage.getItem('lastLoginOtpRequestTime');
	if(lastOtpTime) {
		var currentTime = new Date().getTime();
		var oneMinute = 60 * 1000;
		var elapsed = currentTime - parseInt(lastOtpTime);
		
		if(elapsed < oneMinute) {
			startResendOtpTimer();
		} else {
			localStorage.removeItem('lastLoginOtpRequestTime');
		}
	}
});
</script>
