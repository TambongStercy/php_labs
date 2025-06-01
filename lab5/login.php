<?php
session_start();
require_once 'db_connect.php';

// If already logged in, redirect to library page
if (isset($_SESSION['user_id'])) {
    header("Location: library.php");
    exit();
}

$username_or_email = ""; // For repopulating form on error
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = connectToDatabase(DB_SERVER_L5, DB_USERNAME_L5, DB_PASSWORD_L5, DB_NAME_L5);

    $username_or_email = trim($_POST['username_or_email']);
    $password = $_POST['password'];

    if (empty($username_or_email) || empty($password)) {
        $error_message = "Username/Email and Password are required.";
    } else {
        $field_type = filter_var($username_or_email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $sql = "SELECT id, username, password FROM Users WHERE $field_type = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            // Handle prepare error, e.g., log it or set a generic error message
            $error_message = "An error occurred. Please try again later."; // Or $conn->error for debugging
        } else {
            $stmt->bind_param("s", $username_or_email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['message'] = "Login successful! Welcome, " . htmlspecialchars($user['username']) . ".";
                    $_SESSION['message_type'] = "success";

                    // Redirect to intended page or library.php
                    $redirect_url = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'library.php';
                    unset($_SESSION['redirect_url']);

                    $stmt->close();
                    $conn->close();
                    header("Location: " . $redirect_url);
                    exit();
                } else {
                    $error_message = "Invalid username/email or password.";
                }
            } else {
                $error_message = "Invalid username/email or password.";
            }
            $stmt->close();
        }
    }
    if (isset($conn) && $conn instanceof mysqli)
        $conn->close(); // Ensure connection is closed
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - Library System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        label {
            @apply block text-gray-700 text-sm font-bold mb-2;
        }
        input[type="text"], input[type="password"] {
            @apply shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50;
        }
        button[type="submit"] {
             @apply bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50;
        }
        .google-btn {
            @apply inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500;
        }
        .google-btn svg {
            @apply mr-2 -ml-1 h-5 w-5;
        }
        .form-group {
            @apply mb-4;
        }
        .container-main {
             @apply max-w-md mx-auto bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 mt-10;
         }
        .message {
            @apply p-3 mb-4 rounded-md text-sm;
        }
        .success {
            @apply bg-green-100 text-green-700 border border-green-200;
        }
        .error {
            @apply bg-red-100 text-red-700 border border-red-200;
        }
        a {
            @apply text-blue-500 hover:text-blue-700;
        }
    </style>
</head>

<body class="bg-gray-100 flex flex-col justify-center items-center min-h-screen">
    <div class="mb-8 text-center">
        <img src="assets/logo.png" alt="Library System Logo" class="w-24 h-24 mx-auto mb-2">
        <h1 class="text-3xl font-bold text-gray-800">Library System</h1>
    </div>
    <div class="container-main w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-700">Login</h2>
        <?php
        if (!empty($error_message)) {
            echo "<div class='message error'><p>" . htmlspecialchars($error_message) . "</p></div>";
        }
        if (isset($_SESSION['message']) && $_SESSION['message_type'] === 'success') { // Show success message from registration or logout
            echo "<div class='message success'><p>" . htmlspecialchars($_SESSION['message']) . "</p></div>";
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username_or_email">Username or Email:</label>
                <input type="text" name="username_or_email" id="username_or_email"
                    value="<?php echo htmlspecialchars($username_or_email); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="flex items-center justify-start">
                <button type="submit">Login</button>
            </div>
        </form>
        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Or continue with</span>
                </div>
            </div>
            <div class="mt-6">
                <a href="google_oauth/google_login.php" class="google-btn w-full">
                    <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M47.532 24.552c0-1.656-.144-3.252-.42-4.788H24.48v9.036h12.948c-.564 2.928-2.214 5.424-4.662 7.128v5.856h7.524c4.392-4.044 6.93-10.008 6.93-17.232z"
                            fill="#4285F4"></path>
                        <path
                            d="M24.48 48c6.48 0 11.922-2.124 15.894-5.796L32.85 36.36c-2.16.048-4.896 1.44-7.374 1.44-5.616 0-10.362-3.792-12.048-8.88H3.024v6.036C6.966 42.324 15.12 48 24.48 48z"
                            fill="#34A853"></path>
                        <path
                            d="M12.432 28.908c-.432-1.332-.684-2.736-.684-4.188s.252-2.856.672-4.188V14.496H4.584C3.06 17.436 2.25 20.88 2.25 24.72c0 3.852.804 7.296 2.328 10.224l7.86-6.036z"
                            fill="#FBBC05"></path>
                        <path
                            d="M24.48 9.636c3.492 0 6.606 1.212 9.072 3.6l7.02-7.02C36.396 2.268 30.954 0 24.48 0 15.12 0 6.966 5.676 3.024 14.496l7.86 6.036C14.118 13.428 18.864 9.636 24.48 9.636z"
                            fill="#EA4335"></path>
                    </svg>
                    Login with Google
                </a>
            </div>
        </div>
        <p class="text-center mt-8">Don\'t have an account? <a href="register.php"
                class="font-medium text-blue-600 hover:text-blue-500">Register here</a></p>
        <p class="text-center mt-2"><a href="home.php" class="text-sm text-gray-600 hover:text-gray-900">Back to
                Home</a></p>
    </div>
</body>

</html>