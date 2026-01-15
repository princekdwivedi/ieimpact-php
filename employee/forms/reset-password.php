<style>
	/* Modern Reset Password Page Styles */
	.reset-password-container {
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

	.reset-password-wrapper {
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

	.reset-password-header {
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

	.reset-password-logo {
		max-width: 200px;
		height: auto;
		background: rgba(255, 255, 255, 0.1);
		padding: 10px;
		border-radius: 10px;
		box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
	}

	.reset-password-header h1 {
		font-size: 28px;
		font-weight: 600;
		margin-bottom: 10px;
		letter-spacing: 0.5px;
	}

	.reset-password-header p {
		font-size: 14px;
		opacity: 0.9;
	}

	.reset-password-body {
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

	.error-message {
		background: #fee;
		border: 1px solid #fcc;
		border-radius: 8px;
		padding: 12px 16px;
		margin-bottom: 20px;
		color: #c33;
		font-size: 14px;
		line-height: 1.5;
		text-align: center;
	}

	.error-message .error {
		color: #c33;
		font-weight: 500;
	}

	.btn-group {
		display: flex;
		gap: 12px;
		margin-top: 10px;
		justify-content: center;
	}

	.btn {
		padding: 14px 32px;
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

	.btn-primary:disabled {
		opacity: 0.6;
		cursor: not-allowed;
		transform: none;
	}

	.password-hint {
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

	.field-error-message {
		display: inline-block;
		color: #dc3545;
		font-size: 13px;
		margin-top: 6px;
		padding: 8px 12px;
		background-color: #fee;
		border: 1px solid #fcc;
		border-radius: 6px;
		width: 85%;
		max-width: 350px;
		margin-left: auto;
		margin-right: auto;
		text-align: left;
	}

	.form-group input.invalid {
		border-color: #dc3545;
		background-color: #fff5f5;
	}

	/* Responsive Design */
	@media (max-width: 768px) {
		.reset-password-container {
			padding: 15px;
		}

		.reset-password-wrapper {
			max-width: 100%;
			border-radius: 15px;
		}

		.reset-password-header {
			padding: 25px 20px 30px 20px;
		}

		.reset-password-header h1 {
			font-size: 24px;
		}

		.reset-password-logo {
			max-width: 150px;
			padding: 8px;
		}

		.reset-password-body {
			padding: 30px 20px;
		}

		.form-group {
			margin-bottom: 20px;
		}

		.btn-group {
			flex-direction: column;
		}

		.btn {
			width: 100%;
		}
	}

	@media (max-width: 480px) {
		.reset-password-header h1 {
			font-size: 22px;
		}

		.reset-password-header p {
			font-size: 13px;
		}

		.reset-password-logo {
			max-width: 120px;
			padding: 6px;
		}

		.form-group input[type="password"] {
			width: 90%;
			max-width: 100%;
			padding: 12px 14px;
			font-size: 14px;
		}
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
</style>

<script type="text/javascript">
function showError(fieldId, message) {
	// Remove existing error for this field
	removeError(fieldId);
	
	var field = document.getElementById(fieldId);
	if(field) {
		// Add invalid class to input
		field.classList.add('invalid');
		
		// Create error message element
		var errorDiv = document.createElement('div');
		errorDiv.id = fieldId + '_error';
		errorDiv.className = 'field-error-message';
		errorDiv.innerHTML = message;
		
		// Insert error after the input field or password-hint
		var formGroup = field.closest('.form-group');
		if(formGroup) {
			var passwordHint = formGroup.querySelector('.password-hint');
			var insertAfter = passwordHint || field;
			
			if(insertAfter.nextSibling) {
				insertAfter.parentNode.insertBefore(errorDiv, insertAfter.nextSibling);
			} else {
				insertAfter.parentNode.appendChild(errorDiv);
			}
		}
		
		field.focus();
	}
}

function removeError(fieldId) {
	var field = document.getElementById(fieldId);
	if(field) {
		field.classList.remove('invalid');
	}
	
	var errorDiv = document.getElementById(fieldId + '_error');
	if(errorDiv) {
		errorDiv.remove();
	}
}

function checkPassword() {
	form1 = document.resetPassword;
	var hasError = false;
	
	// Clear previous errors
	removeError('password');
	removeError('rePassword');
	
	// Validate password
	if(form1.password.value == "") {
		showError('password', 'Please type password !!');
		hasError = true;
	}
	else if(form1.password.value.length < 5) {
		showError('password', 'Your password is too short !!');
		hasError = true;
	}
	
	// Validate confirm password
	if(form1.rePassword.value == "") {
		showError('rePassword', 'Please re-type password !!');
		hasError = true;
	}
	else if(form1.password.value != "" && form1.password.value != form1.rePassword.value) {
		showError('rePassword', 'New password and re-typed password does not match !!');
		hasError = true;
	}
	
	if(hasError) {
		return false;
	}
	
	// Disable submit button and show processing state
	var submitBtn = document.getElementById('resetPasswordSubmit');
	if(submitBtn) {
		submitBtn.disabled = true;
		submitBtn.style.opacity = '0.6';
		submitBtn.style.cursor = 'not-allowed';
		var originalText = submitBtn.innerHTML;
		submitBtn.innerHTML = 'Processing...';
		
		// Re-enable button after 10 seconds as a safety measure
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

function setFocus() {
	form1 = document.resetPassword;
	if(form1.password) {
		form1.password.focus();
	}
}

// Clear errors when user types
document.addEventListener('DOMContentLoaded', function() {
	var passwordField = document.getElementById('password');
	var rePasswordField = document.getElementById('rePassword');
	
	if(passwordField) {
		passwordField.addEventListener('input', function() {
			removeError('password');
		});
	}
	
	if(rePasswordField) {
		rePasswordField.addEventListener('input', function() {
			removeError('rePassword');
		});
	}
	
	setFocus();
});
</script>

<div class="reset-password-container">
	<div class="reset-password-wrapper">
		<div class="reset-password-header">
			<div class="logo-container">
				<img src="<?php echo SITE_URL;?>/images/logo-bg.gif" alt="ieIMPACT Logo" class="reset-password-logo" title="Innovation. Excellence. i.e. IMPACT">
			</div>
			<h1>Reset Your Password</h1>
			<p>Create a new password for your account</p>
		</div>
		
		<div class="reset-password-body">
			<form name="resetPassword" action="" method="POST" onsubmit="return checkPassword();">
				
				<?php if(!empty($errorMsg)) { ?>
					<div class="error-message">
						<?php echo $errorMsg; ?>
					</div>
				<?php } ?>

				<div class="form-group">
					<label for="password">New Password</label>
					<input type="password" id="password" name="password" value="" placeholder="Enter your new password" maxlength="20" required>
					<div class="password-hint">Password must be at least 5 characters long</div>
				</div>

				<div class="form-group">
					<label for="rePassword">Confirm Password</label>
					<input type="password" id="rePassword" name="rePassword" value="" placeholder="Re-enter your new password" maxlength="20" required>
				</div>

				<input type="hidden" name="formSubmitted" value="1">
				<?php if(isset($newPasswordCode) && !empty($newPasswordCode)) { ?>
				<input type="hidden" name="resetCode" value="<?php echo htmlspecialchars($newPasswordCode); ?>">
				<?php } ?>

				<div class="btn-group">
					<button type="submit" id="resetPasswordSubmit" class="btn btn-primary">Reset Password</button>
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

// Ensure container is properly displayed
window.addEventListener('DOMContentLoaded', function() {
	var container = document.querySelector('.reset-password-container');
	if (container) {
		// Make sure body and html allow full height
		document.body.style.margin = '0';
		document.body.style.padding = '0';
		if (document.documentElement) {
			document.documentElement.style.height = '100%';
		}
	}
});
</script>
