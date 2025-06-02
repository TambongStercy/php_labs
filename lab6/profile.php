<?php
require_once 'auth_check.php'; // Ensures user is logged in
require_once 'db_connect.php';
$conn = connectToDatabase(DB_SERVER_L5, DB_USERNAME_L5, DB_PASSWORD_L5, DB_NAME_L5);

$user_id = $_SESSION['user_id'];
$user_info = null;

$stmt = $conn->prepare("SELECT username, email, google_id, created_at FROM Users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $user_info = $result->fetch_assoc();
}
$stmt->close();
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Profile - Library System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        body {
            @apply bg-gray-100;
        }
        .container-main {
            @apply max-w-5xl mx-auto;
        }
        .navbar {
            @apply bg-white shadow-md;
        }
        .nav-content {
            @apply max-w-5xl mx-auto px-4 sm:px-6 lg:px-8;
        }
        .nav-links a {
            @apply text-gray-700 hover:bg-gray-100 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium;
        }
        .nav-links a.active {
            @apply bg-blue-600 text-white;
        }
        .nav-links .logo-text {
            @apply text-xl font-bold text-gray-800;
        }
        .content-area {
            @apply mt-8 p-6 bg-white shadow-md rounded-lg;
        }
        .message {
            @apply p-3 my-4 rounded-md text-sm;
        }
        .success {
            @apply bg-green-100 text-green-700 border border-green-200;
        }
        .error {
            @apply bg-red-100 text-red-700 border border-red-200;
        }
        h1 {
            @apply text-3xl font-bold text-gray-800;
        }
        h2 {
            @apply text-2xl font-semibold text-gray-700 mt-6 mb-4;
        }
        p {
            @apply text-gray-700 mb-2;
        }
        p strong {
            @apply font-semibold text-gray-800;
        }
        p em {
            @apply text-sm text-gray-600 italic;
        }
        footer {
            @apply text-center text-sm text-gray-500 mt-8 py-4 border-t border-gray-200;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="nav-content">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="home.php" class="flex items-center space-x-2">
                        <img class="h-10 w-10" src="assets/logo.png" alt="Library System Logo">
                        <span class="logo-text">Library System</span>
                    </a>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4 nav-links">
                        <a href="home.php"
                            class="<?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : ''; ?>">Home</a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="library.php"
                                class="<?php echo basename($_SERVER['PHP_SELF']) == 'library.php' ? 'active' : ''; ?>">My
                                Library</a>
                            <a href="profile.php"
                                class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">Profile</a>
                            <a href="logout.php">Logout</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-main">
        <main class="content-area">
            <h2 class="text-2xl font-semibold text-gray-700 mb-6">My Profile</h2>
            <?php if ($user_info): ?>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user_info['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user_info['email']); ?></p>
                <p><strong>Member Since:</strong> <?php echo date("F j, Y", strtotime($user_info['created_at'])); ?></p>
                <?php if (!empty($user_info['google_id'])): ?>
                    <p><em>Authenticated via Google.</em></p>
                <?php else: ?>
                    <p><em>Authenticated via username/password.</em></p>
                    <!-- Add option to link Google account here in a more advanced version -->
                <?php endif; ?>
            <?php else: ?>
                <p>Could not retrieve user information.</p>
            <?php endif; ?>

            <?php
            if (isset($_SESSION['message'])) {
                echo "<div class='message " . htmlspecialchars($_SESSION['message_type']) . "'><p>" . htmlspecialchars($_SESSION['message']) . "</p></div>";
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            }
            ?>
        </main>
        <footer class="text-center text-sm text-gray-500 mt-8 py-6">
            <p>&copy; <?php echo date("Y"); ?> Library System. All rights reserved.</p>
        </footer>
    </div>
</body>

</html>