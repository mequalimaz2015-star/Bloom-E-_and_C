<?php
// session_init.php - Standardized session handling for Bloom Africa
if (session_status() === PHP_SESSION_NONE) {
    // Determine if we are on HTTPS
    $is_secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

    session_set_cookie_params([
        'lifetime' => 0, // Session cookie
        'path' => '/',
        'domain' => '', // Current domain
        'secure' => $is_secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}
?>