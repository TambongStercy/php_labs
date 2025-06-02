<?php
// google_oauth/google_auth_callback.php
require_once 'config.php'; // Initializes $google_client and session_start
require_once __DIR__ . '/../db_connect.php'; // For database operations

// Verify the state token to prevent CSRF
if (!isset($_GET['state']) || !isset($_SESSION['oauth_state']) || ($_GET['state'] !== $_SESSION['oauth_state'])) {
    unset($_SESSION['oauth_state']); // Clear the state
    $_SESSION['message'] = "Invalid state parameter. CSRF attack suspected or session expired.";
    $_SESSION['message_type'] = "error";
    header('Location: ../login.php');
    exit();
}
unset($_SESSION['oauth_state']); // State is valid, clear it for next time


if (isset($_GET['code'])) {
    try {
        $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);

        if (isset($token['error'])) {
            // Handle error (e.g., user denied access, invalid code)
            $_SESSION['message'] = 'Google Login Error: ' . htmlspecialchars($token['error_description'] ?? $token['error']);
            $_SESSION['message_type'] = "error";
            header('Location: ../login.php');
            exit();
        }

        $google_client->setAccessToken($token);

        // Get user profile information from Google
        $google_oauth = new Google_Service_Oauth2($google_client);
        $google_user_info = $google_oauth->userinfo->get();

        $google_id = $google_user_info->getId();
        $email = $google_user_info->getEmail();
        $name = $google_user_info->getName();
        // $picture = $google_user_info->getPicture(); // Optional

        // --- Database Operations ---
        $conn = connectToDatabase(DB_SERVER_L5, DB_USERNAME_L5, DB_PASSWORD_L5, DB_NAME_L5);

        // Check if user already exists with this google_id
        $stmt = $conn->prepare("SELECT id, username FROM Users WHERE google_id = ?");
        $stmt->bind_param("s", $google_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User exists, log them in
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username']; // Or use Google name ($name)
            $_SESSION['message'] = "Welcome back, " . htmlspecialchars($name) . "!";
            $_SESSION['message_type'] = "success";
        } else {
            // New user via Google. Check if email exists (for linking or conflict)
            $stmt_email = $conn->prepare("SELECT id, username FROM Users WHERE email = ?");
            $stmt_email->bind_param("s", $email);
            $stmt_email->execute();
            $result_email = $stmt_email->get_result();

            if ($result_email->num_rows > 0) {
                // Email exists. For simplicity, we will update the google_id for this user,
                // effectively linking the Google account to the existing local account.
                // More advanced scenarios might ask the user to verify password first.
                $existing_user = $result_email->fetch_assoc();
                $update_stmt = $conn->prepare("UPDATE Users SET google_id = ? WHERE email = ?");
                $update_stmt->bind_param("ss", $google_id, $email);
                if ($update_stmt->execute()) {
                    $_SESSION['user_id'] = $existing_user['id'];
                    $_SESSION['username'] = $existing_user['username'];
                    $_SESSION['message'] = "Google account linked and logged in as " . htmlspecialchars($existing_user['username']) . "!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Error linking Google account: " . $update_stmt->error;
                    $_SESSION['message_type'] = "error";
                    // Don't exit, let it fall through to redirect to login perhaps or handle error display
                }
                $update_stmt->close();
                $stmt_email->close();
                // If linking failed, redirect to login with error
                if ($_SESSION['message_type'] === 'error') {
                    $conn->close();
                    header('Location: ../login.php');
                    exit();
                }
            } else {
                $stmt_email->close();
                // Create a new user account
                // Generate a unique username if Google name conflicts, or use email prefix
                $username_from_google = preg_replace("/[^a-zA-Z0-9_]/", "", strstr($email, '@', true) . substr($google_id, 0, 4));
                // Check if this generated username already exists, append random numbers if so
                $user_check_stmt = $conn->prepare("SELECT id FROM Users WHERE username = ?");
                $temp_username = $username_from_google;
                $counter = 1;
                while (true) {
                    $user_check_stmt->bind_param("s", $temp_username);
                    $user_check_stmt->execute();
                    $user_check_result = $user_check_stmt->get_result();
                    if ($user_check_result->num_rows == 0) {
                        $username_from_google = $temp_username;
                        break;
                    }
                    $temp_username = $username_from_google . $counter;
                    $counter++;
                }
                $user_check_stmt->close();

                $temp_password = bin2hex(random_bytes(16)); // Generate a secure random password (user won't use it)
                $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);

                $insert_stmt = $conn->prepare("INSERT INTO Users (username, email, password, google_id) VALUES (?, ?, ?, ?)");
                $insert_stmt->bind_param("ssss", $username_from_google, $email, $hashed_password, $google_id);

                if ($insert_stmt->execute()) {
                    $_SESSION['user_id'] = $insert_stmt->insert_id;
                    $_SESSION['username'] = $username_from_google; // Or use Google $name
                    $_SESSION['message'] = "Successfully registered and logged in with Google as " . htmlspecialchars($username_from_google) . "!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Error creating new user: " . $insert_stmt->error;
                    $_SESSION['message_type'] = "error";
                    $insert_stmt->close();
                    $conn->close();
                    header('Location: ../register.php'); // Or login page
                    exit();
                }
                $insert_stmt->close();
            }
        }
        $stmt->close();
        $conn->close();

        // Redirect to intended page or library.php
        $redirect_target = '../library.php'; // Default
        if (isset($_SESSION['redirect_url'])) {
            // Basic validation: Ensure it's a relative path within the app
            if (substr($_SESSION['redirect_url'], 0, 1) === '/' && strpos($_SESSION['redirect_url'], '//') === false) {
                $redirect_target = $_SESSION['redirect_url']; // Assumes redirect_url is relative to domain root
                // If lab5 is in a subdirectory, this might need adjustment or ensure REQUEST_URI was stored appropriately.
                // For now, let's assume REQUEST_URI was like /lab5/profile.php, so it works directly.
            } // More robust validation might be needed for production
            unset($_SESSION['redirect_url']);
        }
        header('Location: ' . $redirect_target);
        exit();

    } catch (Exception $e) {
        // Handle general exceptions from Google API client
        $_SESSION['message'] = 'Google API Client Exception: ' . $e->getMessage();
        $_SESSION['message_type'] = "error";
        error_log('Google OAuth Error: ' . $e->getMessage()); // Log error
        header('Location: ../login.php');
        exit();
    }
} else {
    // No 'code' parameter from Google
    $_SESSION['message'] = "Google login failed or was cancelled.";
    $_SESSION['message_type'] = "error";
    header('Location: ../login.php');
    exit();
}
?>