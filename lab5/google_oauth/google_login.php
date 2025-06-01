<?php
// google_oauth/google_login.php
require_once 'config.php'; // This initializes $google_client

// Create a state token to prevent CSRF attacks
if (empty($_SESSION['oauth_state'])) {
    $_SESSION['oauth_state'] = bin2hex(string: random_bytes(32));
}
$google_client->setState($_SESSION['oauth_state']);


$login_url = $google_client->createAuthUrl();
header('Location: ' . $login_url);
exit();
?>