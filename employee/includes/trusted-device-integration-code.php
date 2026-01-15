<?php
/**
 * TRUSTED DEVICE INTEGRATION CODE
 * 
 * Copy and paste these code snippets into employee/index.php
 * at the locations specified in the comments below.
 * 
 * ============================================================
 * LOCATION 1: At the top of employee/index.php (after includes)
 * ============================================================
 */

// Include trusted device functions
include(SITE_ROOT_EMPLOYEES . "/includes/trusted-device-functions.php");

// ============================================================
// LOCATION 2: In the login flow (around line 672-735)
// REPLACE the section where OTP is sent with this code:
// ============================================================

/**
 * CODE TO REPLACE IN LOGIN FLOW:
 * 
 * Find this block (around line 672):
 * ```php
 * else{
 *     // SIMPLIFIED FLOW: After email, password, and security token are verified, send OTP
 *     if($securityTokenValid && !empty($loginId) && $loginId > 0 && !empty($loginEmail) && !empty($password))
 *     {
 *         // Verify password by attempting login (without creating session)
 *         $passwordCheckResult = $employeeObj->checkEmployeeLogin($loginId,$password,$loginEmail);
 *         
 *         // If password is correct (login successful), clear session and send OTP
 *         if($passwordCheckResult && is_numeric($passwordCheckResult)){
 *             // Clear any existing session data
 *             ...
 *             // Generate OTP
 *             ...
 *         }
 *     }
 * }
 * ```
 * 
 * REPLACE WITH THIS:
 */

else{
    // After email, password, and security token are verified, check trusted device
    if($securityTokenValid && !empty($loginId) && $loginId > 0 && !empty($loginEmail) && !empty($password))
    {
        // Verify password by attempting login (without creating session)
        $passwordCheckResult = $employeeObj->checkEmployeeLogin($loginId,$password,$loginEmail);
        
        // If password is correct (login successful), check for trusted device
        if($passwordCheckResult && is_numeric($passwordCheckResult)){
            
            // ===== TRUSTED DEVICE CHECK =====
            // Check if device is already trusted
            $deviceToken = getTrustedDeviceCookie();
            $isDeviceTrusted = false;
            
            if (!empty($deviceToken)) {
                $isDeviceTrusted = isTrustedDevice($loginId, $deviceToken);
            }
            
            if ($isDeviceTrusted) {
                // TRUSTED DEVICE FOUND - SKIP OTP AND LOGIN DIRECTLY
                // Refresh cookie expiry
                $domain = defined('REMEMBER_PASSWORD_ON_SITE') ? REMEMBER_PASSWORD_ON_SITE : '';
                setTrustedDeviceCookie($deviceToken, $domain);
                
                // Proceed with login (bypass OTP)
                $result = $employeeObj->employeeLoginWithNewPassword($loginId, $password, $loginEmail, $loginIpCountry);
                
                // Continue with normal login success flow (redirect to employee-details.php)
                // This will be handled by the existing code below that checks $result
                // No need to send OTP - just continue as if OTP was verified
            } else {
                // NO TRUSTED DEVICE - SEND OTP AS USUAL
                // Clear any existing session data
                $sessionKeys = array('employeeId', 'employeeName', 'employeeEmail', 'hasPdfAccess', 'hasManagerAccess', 
                    'departmentId', 'employeeLoginSessionTrackId', 'isNightShiftEmployee', 'iasHavingAllQaAccess', 'isHavingVerifyAccess');
                foreach($sessionKeys as $key){
                    if(isset($_SESSION[$key])){
                        unset($_SESSION[$key]);
                    }
                }
                
                // Generate OTP
                $validTillNext = getPlusCalculatedMinitue(CURRENT_DATE_INDIA,CURRENT_TIME_INDIA,5);
                list($date,$time) = explode("=",$validTillNext);
                $otpExpireTime = $date." ".$time;
                $generatedOtpCode = rand(1111,9999);
                
                // Store OTP in session
                $_SESSION['isLoginOtpRequired'] = 1;
                $_SESSION['loginOtpCode'] = $generatedOtpCode;
                $_SESSION['loginOtpExpireTime'] = $otpExpireTime;
                $_SESSION['pendingLoginData'] = array(
                    'loginEmail' => $loginEmail,
                    'password' => $password,
                    'securityToken' => $securityToken,
                    'loginId' => $loginId,
                    'loginIpCountry' => $loginIpCountry
                );
                
                // Send OTP via email
                $fullName = isset($fullName) ? $fullName : "";
                $employeeEmail = isset($employeeEmail) ? $employeeEmail : $loginEmail;
                
                include(SITE_ROOT."/includes/send-mail.php");
                $from = "hr@ieimpact.com";
                $fromName = "HR ieIMPACT";
                $mailSubject = "OTP confirmation alert for your ieIMPACT login";
                $templateId = ADMINISTRATOR_SENDING_EMAIL_EMPLOYEES;
                $smsMessage = "You have accessed ieIMPACT employee login for which One Time Password (OTP) has been generated and sent on your registered email on ".showDate(CURRENT_DATE_INDIA)." at ".CURRENT_TIME_INDIA." and valid for next 5 minutes. The OTP is - <b><u>".$generatedOtpCode."</u></b><br /><br />In case you have not logged in to your ieIMPACT employee account, please call HR department";
                
                $a_templateData = array("{employeeName}"=>$fullName,"{message}"=>$smsMessage);
                @sendTemplateMail($from, $fromName, $employeeEmail, $mailSubject, $templateId, $a_templateData);
                
                // Redirect to show OTP input
                ob_clean();
                header("Location: ".SITE_URL_EMPLOYEES);
                exit();
            }
        }
        else{
            // Password incorrect
            $validator->setError("Incorrect password. Please check and try again.");
            $errorMsg	 =	$validator ->getErrors();
            include($form);
            exit();
        }
    }
    else{
        // Security token not validated or missing data - show form with errors
        // (existing error handling code remains)
        echo "<br />KASE12";
        die();
        // ... rest of error handling ...
    }
}

// ============================================================
// LOCATION 3: After OTP verification (around line 647-669)
// REPLACE the section where OTP is verified with this code:
// ============================================================

/**
 * CODE TO REPLACE IN OTP VERIFICATION:
 * 
 * Find this block (around line 647):
 * ```php
 * else{
 *     // OTP verified successfully, use stored login credentials
 *     $pendingLoginData = isset($_SESSION['pendingLoginData']) ? $_SESSION['pendingLoginData'] : array();
 *     ...
 *     // Clear OTP session
 *     unset($_SESSION['isLoginOtpRequired']);
 *     ...
 *     // Proceed with login
 *     $result = $employeeObj->employeeLoginWithNewPassword($loginId,$password,$loginEmail,$loginIpCountry);
 *     ...
 * }
 * ```
 * 
 * REPLACE WITH THIS:
 */

else{
    // OTP verified successfully, use stored login credentials
    $pendingLoginData = isset($_SESSION['pendingLoginData']) ? $_SESSION['pendingLoginData'] : array();
    $loginEmail = isset($pendingLoginData['loginEmail']) ? $pendingLoginData['loginEmail'] : "";
    $password = isset($pendingLoginData['password']) ? $pendingLoginData['password'] : "";
    $loginId = isset($pendingLoginData['loginId']) ? $pendingLoginData['loginId'] : 0;
    $loginIpCountry = isset($pendingLoginData['loginIpCountry']) ? $pendingLoginData['loginIpCountry'] : "";
    
    // Clear OTP session
    unset($_SESSION['isLoginOtpRequired']);
    unset($_SESSION['pendingLoginData']);
    unset($_SESSION['loginOtpCode']);
    unset($_SESSION['loginOtpExpireTime']);
    
    // ===== REGISTER DEVICE AS TRUSTED AFTER SUCCESSFUL OTP VERIFICATION =====
    $deviceToken = getTrustedDeviceCookie();
    
    // If no token exists, generate a new one
    if (empty($deviceToken)) {
        $deviceToken = generateDeviceToken();
    }
    
    // Get browser information
    $browserInfo = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 100) : '';
    
    // Register device as trusted
    $domain = defined('REMEMBER_PASSWORD_ON_SITE') ? REMEMBER_PASSWORD_ON_SITE : '';
    registerTrustedDevice($loginId, $deviceToken, $browserInfo);
    setTrustedDeviceCookie($deviceToken, $domain);
    
    // Proceed with login
    $result = $employeeObj->employeeLoginWithNewPassword($loginId,$password,$loginEmail,$loginIpCountry);

    ob_clean();
    header("Location: ".SITE_URL_EMPLOYEES ."/employee-details.php");
    exit();
}

// ============================================================
// LOCATION 4: Optional - On Logout (employee/logout.php)
// Add this at the beginning if you want to revoke device on logout
// (Recommended: Keep device trusted for better UX)
// ============================================================

/**
 * OPTIONAL: Add to employee/logout.php if you want to revoke device on logout
 * 
 * NOTE: For better user experience, it's recommended to KEEP the device
 * trusted even after logout. Only revoke if security breach is suspected.
 * 
 * If you want to revoke on logout, add this code at the top of logout.php:
 */

/*
<?php
ob_start();
session_start();
error_reporting(E_ALL);
include("../root.php");

// OPTIONAL: Revoke trusted device on logout
if (isset($_SESSION['employeeId']) && !empty($_SESSION['employeeId'])) {
    include(SITE_ROOT_EMPLOYEES . "/includes/trusted-device-functions.php");
    $deviceToken = getTrustedDeviceCookie();
    if ($deviceToken) {
        revokeTrustedDevice($_SESSION['employeeId'], $deviceToken);
        $domain = defined('REMEMBER_PASSWORD_ON_SITE') ? REMEMBER_PASSWORD_ON_SITE : '';
        deleteTrustedDeviceCookie($domain);
    }
}

// Continue with existing logout code...
session_destroy();
...
*/

// ============================================================
// LOCATION 5: On Password Change - Revoke All Devices
// Add this in your password change handler
// ============================================================

/**
 * IMPORTANT: When password changes, revoke ALL trusted devices for security
 * 
 * Add this code in your password change handler (e.g., change-password.php)
 * AFTER the password is successfully changed:
 */

/*
// After password change is successful:
if (isset($_SESSION['employeeId']) && !empty($_SESSION['employeeId'])) {
    include(SITE_ROOT_EMPLOYEES . "/includes/trusted-device-functions.php");
    // Revoke all trusted devices when password changes (for security)
    revokeTrustedDevice($_SESSION['employeeId']); // Revoke all devices
    $domain = defined('REMEMBER_PASSWORD_ON_SITE') ? REMEMBER_PASSWORD_ON_SITE : '';
    deleteTrustedDeviceCookie($domain);
}
*/

?>

