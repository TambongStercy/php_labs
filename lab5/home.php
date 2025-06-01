<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Welcome to the Library System</title>
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
         footer {
            @apply text-center text-sm text-gray-500 mt-8 py-4 border-t border-gray-200;
        }
    </style>
</head>

<body class="bg-gray-100">
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
                                class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">Profile
                                (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
                            <a href="logout.php">Logout</a>
                        <?php else: ?>
                            <a href="login.php"
                                class="<?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>">Login</a>
                            <a href="register.php"
                                class="<?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : ''; ?>">Register</a>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Mobile menu button (optional, for future enhancement) -->
            </div>
        </div>
    </nav>

    <div class="container-main">
        <main class="content-area">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Welcome!</h2>
            <p class="text-gray-600 mb-4">This is the public home page of our library system. Browse our collection,
                manage your account, and more!</p>
            <?php
            if (isset($_SESSION['message'])) {
                echo "<div class='message " . htmlspecialchars($_SESSION['message_type']) . "'><p>" . htmlspecialchars($_SESSION['message']) . "</p></div>";
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            }
            ?>
            <p class="text-gray-600">
                <?php if (isset($_SESSION['user_id'])): ?>
                    You are logged in. Go to <a href="library.php" class="text-blue-500 hover:underline">your library
                        dashboard</a>.
                <?php else: ?>
                    Please <a href="login.php" class="text-blue-500 hover:underline">login</a> or <a href="register.php"
                        class="text-blue-500 hover:underline">register</a> to access more features.
                <?php endif; ?>
            </p>
        </main>

        <footer class="text-center text-sm text-gray-500 mt-8 py-6">
            <p>&copy; <?php echo date("Y"); ?> Library System. All rights reserved.</p>
        </footer>
    </div>
</body>

</html>