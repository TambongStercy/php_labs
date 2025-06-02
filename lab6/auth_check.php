<?php
if (session_status() === PHP_SESSION_NONE) { // Start session if not already started
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, store the intended page for redirection after login
    // This helps in redirecting the user back to the page they were trying to access.
    if (!empty($_SERVER['REQUEST_URI'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    }

    $_SESSION['message'] = "You must be logged in to access this page.";
    $_SESSION['message_type'] = "error";
    header("Location: login.php"); // Redirect to the login page. Adjust if login.php is in a different directory.
    exit(); // Important to stop further script execution
}

// Optional: Activity-based session expiration logic (from lab5-part3.md)
$inactivity_timeout = 1800; // 30 minutes in seconds

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactivity_timeout)) {
    // Session has expired due to inactivity
    session_unset();     // Unset $_SESSION variable for the run-time
    session_destroy();   // Destroy session data in storage
    session_start();     // Start a new session for the message
    $_SESSION['message'] = "Your session has expired due to inactivity. Please log in again.";
    $_SESSION['message_type'] = "error";
    header("Location: login.php"); // Adjust if login.php is in a different directory.
    exit();
}
$_SESSION['last_activity'] = time(); // Update last activity time for current request

// Optional: Regenerate session ID periodically to prevent session fixation
// if (!isset($_SESSION['created'])) {
//     $_SESSION['created'] = time();
// } else if (time() - $_SESSION['created'] > $inactivity_timeout) { // For example, regenerate every 30 minutes
//     session_regenerate_id(true);
//     $_SESSION['created'] = time();
// }
?>