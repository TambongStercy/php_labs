<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    if (function_exists('random_bytes')) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } elseif (function_exists('openssl_random_pseudo_bytes')) {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32, $strong));
        if (!$strong) {
            // Log this weakness or use a different method if preferred
            error_log('OpenSSL did not produce a cryptographically strong result for CSRF token.');
        }
    } else {
        // Fallback for older PHP versions or systems without strong random functions
        $_SESSION['csrf_token'] = bin2hex(uniqid(mt_rand(), true));
        error_log('Using uniqid with mt_rand for CSRF token generation. Consider upgrading PHP or enabling random_bytes/openssl_random_pseudo_bytes for better security.');
    }
}