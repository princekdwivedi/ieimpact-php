<?php
ob_start();
session_start();
error_reporting(E_ALL);
include("../root.php");
include(SITE_ROOT . "/includes/send-mail.php");
include(SITE_ROOT . "/includes/common-array.php");
include(SITE_ROOT . "/classes/common.php");

$commonClass = new common();

// Define modal styles (will be used by all modal outputs)
$modalStyles = '<style>
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
	
	.modal-container {
		background: #ffffff;
		border-radius: 20px;
		box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
		max-width: 500px;
		width: 90%;
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
	
	.modal-header h4 {
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
		text-align: center;
	}
	
	.modal-success-message {
		background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
		border-left: 4px solid #22c55e;
		border-radius: 8px;
		padding: 20px;
		color: #166534;
		font-size: 16px;
		line-height: 1.6;
	}
	
	.modal-success-message .success-icon {
		font-size: 48px;
		margin-bottom: 15px;
		display: block;
		color: #22c55e;
	}
	
	.modal-footer {
		padding: 20px 30px 30px 30px;
		text-align: center;
	}
	
	.modal-btn {
		padding: 12px 30px;
		border: none;
		border-radius: 10px;
		font-size: 16px;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.3s ease;
		font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: #ffffff;
		box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
	}
	
	.modal-btn:hover {
		transform: translateY(-2px);
		box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
	}
	
	@media (max-width: 768px) {
		.modal-container {
			width: 95%;
		}
		
		.modal-header {
			padding: 20px;
		}
		
		.modal-body {
			padding: 20px;
		}
	}
</style>';

// Check if login OTP is required and session data exists
if(!isset($_SESSION['isLoginOtpRequired']) || $_SESSION['isLoginOtpRequired'] != 1) {
	echo $modalStyles;
	?>
	<div class="modal-overlay" id="resendOtpModal">
		<div class="modal-container">
			<div class="modal-header">
				<h4>Error</h4>
				<button type="button" class="modal-close" onclick="closeResendOtpModal()">&times;</button>
			</div>
			<div class="modal-body">
				<font style="color:#ff0000;font-size:16px;">Invalid request. Please login again.</font>
			</div>
			<div class="modal-footer">
				<button type="button" class="modal-btn" onclick="closeResendOtpModal()">Close</button>
			</div>
		</div>
	</div>
	<script>function closeResendOtpModal(){document.getElementById('resendOtpModal').remove();}</script>
	<?php
	exit();
}

// Check if 1 minute has passed since last OTP request
if(isset($_SESSION['lastLoginOtpRequestTime'])) {
	$lastRequestTime = $_SESSION['lastLoginOtpRequestTime'];
	$currentTime = time();
	$timeDiff = $currentTime - $lastRequestTime;
	
	if($timeDiff < 60) {
		$remainingSeconds = 60 - $timeDiff;
		echo $modalStyles;
		?>
		<div class="modal-overlay" id="resendOtpModal">
			<div class="modal-container">
				<div class="modal-header">
					<h4>Resend OTP</h4>
					<button type="button" class="modal-close" onclick="closeResendOtpModal()">&times;</button>
				</div>
				<div class="modal-body">
					<font style="color:#ff0000;font-size:16px;">Please wait <?php echo $remainingSeconds; ?> seconds before requesting a new OTP.</font>
				</div>
				<div class="modal-footer">
					<button type="button" class="modal-btn" onclick="closeResendOtpModal()">Close</button>
				</div>
			</div>
		</div>
		<script>function closeResendOtpModal(){document.getElementById('resendOtpModal').remove();}</script>
		<?php
		exit();
	}
}

// Get pending login data from session
$pendingLoginData = isset($_SESSION['pendingLoginData']) ? $_SESSION['pendingLoginData'] : array();
$loginEmail = isset($pendingLoginData['loginEmail']) ? $pendingLoginData['loginEmail'] : "";
$loginId = isset($pendingLoginData['loginId']) ? $pendingLoginData['loginId'] : 0;

if(empty($loginEmail) || empty($loginId) || $loginId == 0) {
	echo $modalStyles;
	?>
	<div class="modal-overlay" id="resendOtpModal">
		<div class="modal-container">
			<div class="modal-header">
				<h4>Error</h4>
				<button type="button" class="modal-close" onclick="closeResendOtpModal()">&times;</button>
			</div>
			<div class="modal-body">
				<font style="color:#ff0000;font-size:16px;">Session expired. Please login again.</font>
			</div>
			<div class="modal-footer">
				<button type="button" class="modal-btn" onclick="closeResendOtpModal()">Close</button>
			</div>
		</div>
	</div>
	<script>function closeResendOtpModal(){document.getElementById('resendOtpModal').remove();}</script>
	<?php
	exit();
}

// Get employee details
$query = "SELECT fullName, email FROM employee_details WHERE employeeId=$loginId AND email='$loginEmail' AND isActive=1";
$result = dbQuery($query);
if(mysqli_num_rows($result) == 0) {
	echo $modalStyles;
	?>
	<div class="modal-overlay" id="resendOtpModal">
		<div class="modal-container">
			<div class="modal-header">
				<h4>Error</h4>
				<button type="button" class="modal-close" onclick="closeResendOtpModal()">&times;</button>
			</div>
			<div class="modal-body">
				<font style="color:#ff0000;font-size:16px;">Invalid employee data.</font>
			</div>
			<div class="modal-footer">
				<button type="button" class="modal-btn" onclick="closeResendOtpModal()">Close</button>
			</div>
		</div>
	</div>
	<script>function closeResendOtpModal(){document.getElementById('resendOtpModal').remove();}</script>
	<?php
	exit();
}

$row = mysqli_fetch_assoc($result);
$fullName = stripslashes($row['fullName']);
$employeeEmail = $row['email'];

// Generate new OTP
$validTillNext = getPlusCalculatedMinitue(CURRENT_DATE_INDIA, CURRENT_TIME_INDIA, 5);
list($date, $time) = explode("=", $validTillNext);
$otpExpireTime = $date . " " . $time;
$generatedOtpCode = rand(1111, 9999);

// Update session with new OTP
$_SESSION['loginOtpCode'] = $generatedOtpCode;
$_SESSION['loginOtpExpireTime'] = $otpExpireTime;
$_SESSION['lastLoginOtpRequestTime'] = time();

// Send OTP via email
$from = "hr@ieimpact.com";
$fromName = "HR ieIMPACT";
$mailSubject = "OTP confirmation alert for your ieIMPACT login";
$templateId = ADMINISTRATOR_SENDING_EMAIL_EMPLOYEES;
$smsMessage = "You have requested a new One Time Password (OTP) for your ieIMPACT employee login. The OTP has been generated and sent on your registered email on " . showDate(CURRENT_DATE_INDIA) . " at " . CURRENT_TIME_INDIA . " and valid for next 5 minutes. The OTP is - <b><u>" . $generatedOtpCode . "</u></b><br /><br />In case you have not requested this OTP, please call HR department immediately.";

$a_templateData = array("{employeeName}" => $fullName, "{message}" => $smsMessage);
@sendTemplateMail($from, $fromName, $employeeEmail, $mailSubject, $templateId, $a_templateData);

echo $modalStyles;
?>

<div class="modal-overlay" id="resendOtpModal">
	<div class="modal-container">
		<div class="modal-header">
			<h4>Resend OTP</h4>
			<button type="button" class="modal-close" onclick="closeResendOtpModal()" aria-label="Close">&times;</button>
		</div>
		<div class="modal-body">
			<div class="modal-success-message">
				<span class="success-icon">✓</span>
				<strong>OTP successfully sent to your login email. Please check your spam/junk folder if you didn't receive it.</strong>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="modal-btn" onclick="closeResendOtpModal()">Close</button>
		</div>
	</div>
</div>

<script type="text/javascript">
function closeResendOtpModal() {
	var modal = document.getElementById('resendOtpModal');
	if(modal) {
		modal.style.animation = 'fadeOut 0.3s ease';
		setTimeout(function() {
			modal.remove();
		}, 300);
	}
}

// Close modal when clicking outside
var modal = document.getElementById('resendOtpModal');
if(modal) {
	modal.addEventListener('click', function(e) {
		if(e.target === this) {
			closeResendOtpModal();
		}
	});
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
	if(e.key === 'Escape') {
		var modal = document.getElementById('resendOtpModal');
		if(modal) {
			closeResendOtpModal();
		}
	}
});
</script>
