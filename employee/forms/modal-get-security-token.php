<style>
	.modal-overlay {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background: rgba(0, 0, 0, 0.6);
		display: flex;
		align-items: center;
		justify-content: center;
		z-index: 10000;
		animation: fadeIn 0.3s ease;
	}
	
	@keyframes fadeIn {
		from { opacity: 0; }
		to { opacity: 1; }
	}
	
	@keyframes fadeOut {
		from { opacity: 1; }
		to { opacity: 0; }
	}
	
	.modal-container {
		background: #ffffff;
		border-radius: 20px;
		box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
		max-width: 500px;
		width: 90%;
		max-height: 90vh;
		overflow-y: auto;
		animation: slideUp 0.3s ease;
		position: relative;
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
	
	.modal-header {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		padding: 25px 30px;
		border-radius: 20px 20px 0 0;
		color: #ffffff;
		display: flex;
		justify-content: space-between;
		align-items: center;
	}
	
	.modal-header h2 {
		margin: 0;
		font-size: 24px;
		font-weight: 600;
		letter-spacing: 0.5px;
	}
	
	.modal-close {
		background: rgba(255, 255, 255, 0.2);
		border: none;
		color: #ffffff;
		font-size: 28px;
		width: 36px;
		height: 36px;
		border-radius: 50%;
		cursor: pointer;
		display: flex;
		align-items: center;
		justify-content: center;
		transition: all 0.3s ease;
		line-height: 1;
		padding: 0;
	}
	
	.modal-close:hover {
		background: rgba(255, 255, 255, 0.3);
		transform: rotate(90deg);
	}
	
	.modal-body {
		padding: 30px;
	}
	
	.modal-form-group {
		margin-bottom: 25px;
	}
	
	.modal-form-group label {
		display: block;
		font-size: 14px;
		font-weight: 600;
		color: #333;
		margin-bottom: 8px;
		letter-spacing: 0.3px;
	}
	
	.modal-form-group input[type="email"],
	.modal-form-group input[type="text"] {
		width: 100%;
		padding: 14px 16px;
		border: 2px solid #e0e0e0;
		border-radius: 10px;
		font-size: 15px;
		transition: all 0.3s ease;
		background: #f8f9fa;
		color: #333;
		font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		box-sizing: border-box;
	}
	
	.modal-form-group input:focus {
		outline: none;
		border-color: #667eea;
		background: #ffffff;
		box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
	}
	
	.modal-error-message {
		background: linear-gradient(135deg, #fff5f5 0%, #ffe5e5 100%);
		border-left: 4px solid #ef4444;
		border-radius: 8px;
		padding: 15px;
		margin-bottom: 20px;
		color: #991b1b;
		font-size: 14px;
		line-height: 1.6;
		animation: shake 0.5s ease;
	}
	
	@keyframes shake {
		0%, 100% { transform: translateX(0); }
		25% { transform: translateX(-10px); }
		75% { transform: translateX(10px); }
	}
	
	.modal-success-message {
		background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
		border-left: 4px solid #22c55e;
		border-radius: 8px;
		padding: 20px;
		margin-bottom: 20px;
		color: #166534;
		font-size: 14px;
		line-height: 1.6;
		text-align: center;
	}
	
	.modal-success-message .success-icon {
		font-size: 48px;
		margin-bottom: 10px;
		display: block;
	}
	
	.modal-button-group {
		display: flex;
		gap: 12px;
		margin-top: 10px;
	}
	
	.modal-btn {
		flex: 1;
		padding: 14px 24px;
		border: none;
		border-radius: 10px;
		font-size: 16px;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.3s ease;
		font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		letter-spacing: 0.5px;
	}
	
	.modal-btn-primary {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: #ffffff;
		box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
	}
	
	.modal-btn-primary:hover {
		transform: translateY(-2px);
		box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
	}
	
	.modal-btn-primary:active {
		transform: translateY(0);
	}
	
	.modal-btn-primary:disabled {
		opacity: 0.6;
		cursor: not-allowed;
		transform: none;
	}
	
	.modal-btn-secondary {
		background: #f0f0f0;
		color: #666;
		border: 2px solid #e0e0e0;
	}
	
	.modal-btn-secondary:hover {
		background: #e0e0e0;
		border-color: #d0d0d0;
	}
	
	.modal-loading {
		display: none;
		text-align: center;
		padding: 20px;
	}
	
	.modal-loading.active {
		display: block;
	}
	
	.spinner {
		border: 3px solid #f3f3f3;
		border-top: 3px solid #667eea;
		border-radius: 50%;
		width: 40px;
		height: 40px;
		animation: spin 1s linear infinite;
		margin: 0 auto;
	}
	
	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}
	
	@media (max-width: 768px) {
		.modal-container {
			width: 95%;
			max-height: 95vh;
		}
		
		.modal-header {
			padding: 20px;
		}
		
		.modal-header h2 {
			font-size: 20px;
		}
		
		.modal-body {
			padding: 20px;
		}
		
		.modal-button-group {
			flex-direction: column;
		}
		
		.modal-btn {
			width: 100%;
		}
	}
</style>

<div class="modal-overlay" id="getSecurityTokenModal">
	<div class="modal-container">
		<div class="modal-header">
			<h2>Get Security Token</h2>
			<button type="button" class="modal-close" onclick="closeSecurityTokenModal()" aria-label="Close">&times;</button>
		</div>
		<div class="modal-body">
			<div id="securityTokenError" class="modal-error-message" style="display: none;"></div>
			<div id="securityTokenSuccess" class="modal-success-message" style="display: none;"></div>
			<div class="modal-loading" id="securityTokenLoading">
				<div class="spinner"></div>
				<p style="margin-top: 10px; color: #666;">Sending email...</p>
			</div>
			
			<form id="securityTokenForm" onsubmit="return submitSecurityToken(event);">
				<div class="modal-form-group">
					<label for="securityTokenEmail">Email Address</label>
					<input type="email" id="securityTokenEmail" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : '';?>" placeholder="Enter your registered email address" maxlength="100" required>
				</div>
				
				<div class="modal-button-group">
					<button type="submit" class="modal-btn modal-btn-primary" id="securityTokenSubmit">Submit</button>
					<button type="button" class="modal-btn modal-btn-secondary" onclick="closeSecurityTokenModal()">Cancel</button>
				</div>
				
				<input type="hidden" name="formSubmitted" value="1">
			</form>
		</div>
	</div>
</div>

<script>
function closeSecurityTokenModal() {
	var modal = document.getElementById('getSecurityTokenModal');
	if(modal) {
		modal.style.animation = 'fadeOut 0.3s ease';
		setTimeout(function() {
			modal.remove();
		}, 300);
	}
}

function submitSecurityToken(event) {
	event.preventDefault();
	
	var form = document.getElementById('securityTokenForm');
	var email = document.getElementById('securityTokenEmail').value;
	var errorDiv = document.getElementById('securityTokenError');
	var successDiv = document.getElementById('securityTokenSuccess');
	var loadingDiv = document.getElementById('securityTokenLoading');
	var submitBtn = document.getElementById('securityTokenSubmit');
	
	// Hide previous messages
	errorDiv.style.display = 'none';
	successDiv.style.display = 'none';
	
	// Validate email
	if(!email || email.trim() === '') {
		errorDiv.innerHTML = 'Please enter your email.';
		errorDiv.style.display = 'block';
		return false;
	}
	
	var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	if(!emailRegex.test(email)) {
		errorDiv.innerHTML = 'Your email is invalid.';
		errorDiv.style.display = 'block';
		return false;
	}
	
	// Show loading
	loadingDiv.classList.add('active');
	submitBtn.disabled = true;
	
	// Submit via AJAX
	var formData = new FormData(form);
	formData.append('email', email);
	
	var xhr = new XMLHttpRequest();
	xhr.open('POST', '<?php echo SITE_URL_EMPLOYEES;?>/get-security-token.php', true);
	xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	
	xhr.onload = function() {
		loadingDiv.classList.remove('active');
		submitBtn.disabled = false;
		
		if(xhr.status === 200) {
			try {
				var response = JSON.parse(xhr.responseText);
				if(response.success) {
					form.style.display = 'none';
					successDiv.innerHTML = '<span class="success-icon">✓</span><strong>Email Sent Successfully!</strong><br>' + response.message;
					successDiv.style.display = 'block';
					
					// Auto close after 5 seconds
					setTimeout(function() {
						closeSecurityTokenModal();
					}, 5000);
				} else {
					errorDiv.innerHTML = response.errors || 'An error occurred. Please try again.';
					errorDiv.style.display = 'block';
				}
			} catch(e) {
				errorDiv.innerHTML = 'An error occurred. Please try again.';
				errorDiv.style.display = 'block';
			}
		} else {
			errorDiv.innerHTML = 'An error occurred. Please try again.';
			errorDiv.style.display = 'block';
		}
	};
	
	xhr.onerror = function() {
		loadingDiv.classList.remove('active');
		submitBtn.disabled = false;
		errorDiv.innerHTML = 'Network error. Please check your connection and try again.';
		errorDiv.style.display = 'block';
	};
	
	xhr.send(formData);
	return false;
}

// Close modal when clicking outside
document.getElementById('getSecurityTokenModal').addEventListener('click', function(e) {
	if(e.target === this) {
		closeSecurityTokenModal();
	}
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
	if(e.key === 'Escape') {
		closeSecurityTokenModal();
	}
});

// Focus on email input
setTimeout(function() {
	document.getElementById('securityTokenEmail').focus();
}, 100);
</script>

