<?php
/**
 * Trusted Device Token System for Employee Login
 * 
 * This file contains helper functions to manage trusted device tokens
 * for skipping OTP on same browser/device login.
 * 
 * Requirements:
 * - Core PHP only (no frameworks)
 * - Uses existing dbQuery() function for database operations
 * - Secure cookies (HttpOnly, Secure)
 * - SHA256 hashing for tokens
 * - 30-day token expiration
 */

// ============================================
// CONFIGURATION
// ============================================
if (!defined('TRUSTED_DEVICE_COOKIE_NAME')) {
    define('TRUSTED_DEVICE_COOKIE_NAME', 'emp_device_token');
}
if (!defined('TRUSTED_DEVICE_TOKEN_EXPIRY_DAYS')) {
    define('TRUSTED_DEVICE_TOKEN_EXPIRY_DAYS', 30);
}
if (!defined('TRUSTED_DEVICE_TOKEN_LENGTH')) {
    define('TRUSTED_DEVICE_TOKEN_LENGTH', 64); // Bytes (will be base64 encoded)
}

/**
 * Escape string for SQL query
 * Uses makeDBSafe() if available (from common-functions.php), otherwise mysqli_real_escape_string
 * 
 * @param string $string String to escape
 * @return string Escaped string
 */
function trustedDeviceEscape($string) {
    if (function_exists('makeDBSafe')) {
        return makeDBSafe($string);
    }
    // Fallback to mysqli_real_escape_string if makeDBSafe not available
    global $db_conn;
    if ($db_conn && function_exists('mysqli_real_escape_string')) {
        return mysqli_real_escape_string($db_conn, $string);
    }
    // Last resort fallback
    return addslashes($string);
}

/**
 * Generate a secure random device token
 * 
 * @return string Base64-encoded random token
 */
function generateDeviceToken() {
    // Generate cryptographically secure random bytes
    $randomBytes = random_bytes(TRUSTED_DEVICE_TOKEN_LENGTH);
    // Base64 encode for cookie storage
    return base64_encode($randomBytes);
}

/**
 * Hash device token before storing in database
 * 
 * @param string $token Plain token (base64 encoded)
 * @return string SHA256 hash of the token
 */
function hashDeviceToken($token) {
    return hash('sha256', $token);
}

/**
 * Set secure cookie for trusted device token
 * 
 * @param string $token Device token to store
 * @param string $domain Cookie domain (from REMEMBER_PASSWORD_ON_SITE constant)
 * @return bool True on success, false on failure
 */
function setTrustedDeviceCookie($token, $domain = '') {
    $cookieName = TRUSTED_DEVICE_COOKIE_NAME;
    $expiryTime = time() + (TRUSTED_DEVICE_TOKEN_EXPIRY_DAYS * 24 * 60 * 60);
    
    // Secure cookie settings
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? true : false;
    $httpOnly = true; // Prevent JavaScript access
    $sameSite = 'Strict'; // CSRF protection
    
    // Use setcookie with all security flags
    return setcookie(
        $cookieName,
        $token,
        [
            'expires' => $expiryTime,
            'path' => '/',
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httpOnly,
            'samesite' => $sameSite
        ]
    );
}

/**
 * Get device token from cookie
 * 
 * @return string|null Device token or null if not found
 */
function getTrustedDeviceCookie() {
    $cookieName = TRUSTED_DEVICE_COOKIE_NAME;
    
    if (isset($_COOKIE[$cookieName]) && !empty($_COOKIE[$cookieName])) {
        return $_COOKIE[$cookieName];
    }
    
    return null;
}

/**
 * Delete trusted device cookie
 * 
 * @param string $domain Cookie domain
 * @return bool True on success
 */
function deleteTrustedDeviceCookie($domain = '') {
    $cookieName = TRUSTED_DEVICE_COOKIE_NAME;
    return setcookie(
        $cookieName,
        '',
        [
            'expires' => time() - 3600, // Past time to delete
            'path' => '/',
            'domain' => $domain,
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]
    );
}

/**
 * Check if device token exists and is valid for employee
 * 
 * @param int $employeeId Employee ID
 * @param string $deviceToken Token from cookie (base64 encoded)
 * @return bool True if device is trusted and valid, false otherwise
 */
function isTrustedDevice($employeeId, $deviceToken) {
    if (empty($employeeId) || empty($deviceToken)) {
        return false;
    }
    
    // Ensure dbQuery function is available
    if (!function_exists('dbQuery')) {
        error_log("Trusted Device: dbQuery function not available");
        return false; // If DB function fails, require OTP for security
    }
    
    $tokenHash = trustedDeviceEscape(hashDeviceToken($deviceToken));
    $employeeId = (int)$employeeId; // Ensure integer
    
    // Check if token exists, is active, not expired, and belongs to employee
    $query = "SELECT deviceId 
              FROM employee_trusted_devices 
              WHERE employeeId = $employeeId 
              AND deviceTokenHash = '$tokenHash' 
              AND isActive = 1 
              AND deviceExpiresAt > NOW()
              LIMIT 1";
    
    $result = dbQuery($query, "trusted-device-functions.php::isTrustedDevice");
    
    if ($result && mysqli_num_rows($result)) {
        $row = mysqli_fetch_assoc($result);
        $deviceId = (int)$row['deviceId'];
        
        // Update last used timestamp
        updateDeviceLastUsed($deviceId);
        
        return true;
    }
    
    return false;
}

/**
 * Update device last used timestamp
 * 
 * @param int $deviceId Device ID
 * @return bool True on success
 */
function updateDeviceLastUsed($deviceId) {
    if (empty($deviceId) || !function_exists('dbQuery')) {
        return false;
    }
    
    $deviceId = (int)$deviceId; // Ensure integer
    
    $query = "UPDATE employee_trusted_devices 
              SET deviceLastUsed = NOW() 
              WHERE deviceId = $deviceId";
    
    dbQuery($query, "trusted-device-functions.php::updateDeviceLastUsed", "", false);
    
    return true;
}

/**
 * Register device as trusted after successful OTP verification
 * 
 * IMPORTANT: Each employee can have only ONE trusted device record.
 * If a record already exists, it will be UPDATED with the new device token.
 * If no record exists, a new one will be INSERTED.
 * 
 * @param int $employeeId Employee ID
 * @param string $deviceToken Token to register (base64 encoded)
 * @param string $browserInfo Optional browser user agent info
 * @return bool True on success, false on failure
 */
function registerTrustedDevice($employeeId, $deviceToken, $browserInfo = '') {
    if (empty($employeeId) || empty($deviceToken)) {
        return false;
    }
    
    if (!function_exists('dbQuery')) {
        error_log("Trusted Device: dbQuery function not available");
        return false;
    }
    
    $tokenHash = trustedDeviceEscape(hashDeviceToken($deviceToken));
    $employeeId = (int)$employeeId; // Ensure integer
    $expiryDateTime = date('Y-m-d H:i:s', strtotime('+' . TRUSTED_DEVICE_TOKEN_EXPIRY_DAYS . ' days'));
    $expiryDateTime = trustedDeviceEscape($expiryDateTime);
    
    // Get browser info if not provided
    if (empty($browserInfo) && isset($_SERVER['HTTP_USER_AGENT'])) {
        $browserInfo = substr($_SERVER['HTTP_USER_AGENT'], 0, 100);
    }
    $browserInfo = trustedDeviceEscape($browserInfo);
    
    // Check if employee already has a trusted device record (only one allowed per employee)
    $checkQuery = "SELECT deviceId 
                   FROM employee_trusted_devices 
                   WHERE employeeId = $employeeId 
                   LIMIT 1";
    
    $checkResult = dbQuery($checkQuery, "trusted-device-functions.php::registerTrustedDevice");
    
    if ($checkResult && mysqli_num_rows($checkResult)) {
        // Employee already has a record - UPDATE it with new device token (replace old device)
        $row = mysqli_fetch_assoc($checkResult);
        $deviceId = (int)$row['deviceId'];
        
        $updateQuery = "UPDATE employee_trusted_devices 
                        SET deviceTokenHash = '$tokenHash',
                            isActive = 1,
                            deviceLastUsed = NOW(),
                            deviceExpiresAt = '$expiryDateTime',
                            deviceBrowser = '$browserInfo',
                            deviceCreatedAt = NOW()
                        WHERE deviceId = $deviceId";
        
        dbQuery($updateQuery, "trusted-device-functions.php::registerTrustedDevice", "", false);
    } else {
        // No record exists for this employee - INSERT new trusted device
        $insertQuery = "INSERT INTO employee_trusted_devices 
                        (employeeId, deviceTokenHash, deviceBrowser, deviceCreatedAt, deviceLastUsed, deviceExpiresAt, isActive) 
                        VALUES 
                        ($employeeId, '$tokenHash', '$browserInfo', NOW(), NOW(), '$expiryDateTime', 1)";
        
        dbQuery($insertQuery, "trusted-device-functions.php::registerTrustedDevice");
    }
    
    return true;
}

/**
 * Revoke/delete a trusted device
 * 
 * Note: Since each employee can have only one trusted device,
 * this will revoke the single device record for the employee.
 * 
 * @param int $employeeId Employee ID
 * @param string $deviceToken Optional - kept for backward compatibility but not used (only one device per employee)
 * @return bool True on success
 */
function revokeTrustedDevice($employeeId, $deviceToken = null) {
    if (empty($employeeId) || !function_exists('dbQuery')) {
        return false;
    }
    
    $employeeId = (int)$employeeId; // Ensure integer
    
    // Since each employee can have only one trusted device, just revoke/deactivate it
    // The $deviceToken parameter is kept for backward compatibility but not used
    $query = "UPDATE employee_trusted_devices 
              SET isActive = 0 
              WHERE employeeId = $employeeId";
    
    dbQuery($query, "trusted-device-functions.php::revokeTrustedDevice", "", false);
    
    return true;
}

/**
 * Clean up expired trusted device tokens
 * Run this periodically (e.g., via cron) or on login
 * 
 * @return int Number of records deleted
 */
function cleanupExpiredDevices() {
    if (!function_exists('dbQuery')) {
        return 0;
    }
    
    $query = "DELETE FROM employee_trusted_devices 
              WHERE deviceExpiresAt < NOW() OR isActive = 0";
    
    $affectedRows = dbQuery($query, "trusted-device-functions.php::cleanupExpiredDevices", "", false);
    
    return (int)$affectedRows;
}

/**
 * Get count of trusted devices for an employee
 * Useful for displaying "X trusted devices" in account settings
 * 
 * @param int $employeeId Employee ID
 * @return int Number of active trusted devices
 */
function getTrustedDeviceCount($employeeId) {
    if (empty($employeeId) || !function_exists('dbQuery')) {
        return 0;
    }
    
    $employeeId = (int)$employeeId; // Ensure integer
    
    $query = "SELECT COUNT(*) as deviceCount 
              FROM employee_trusted_devices 
              WHERE employeeId = $employeeId 
              AND isActive = 1 
              AND deviceExpiresAt > NOW()";
    
    $result = dbQuery($query, "trusted-device-functions.php::getTrustedDeviceCount");
    
    if ($result && mysqli_num_rows($result)) {
        $row = mysqli_fetch_assoc($result);
        return isset($row['deviceCount']) ? (int)$row['deviceCount'] : 0;
    }
    
    return 0;
}

?>
