<?php
require_once 'auth_check.php'; // Ensures user is logged in
require_once 'db_connect.php';
$conn = connectToDatabase(DB_SERVER_L5, DB_USERNAME_L5, DB_PASSWORD_L5, DB_NAME_L5);

// Fetch books for display (this will be expanded in Exercise 4 for CRUD)
$books = [];
$result = $conn->query("SELECT book_id, title, author, publication_year, genre, price FROM Books ORDER BY title");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Library - Library System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        body {
            @apply bg-gray-100;
        }
        .container-main {
            @apply max-w-5xl mx-auto; /* Adjusted to be child of body for nav to be full-width */
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
        table {
            @apply w-full bg-white shadow-sm rounded-lg mt-6;
        }
        th, td {
            @apply px-4 py-3 border-b border-gray-200 text-left text-sm;
        }
        th {
            @apply bg-gray-100 font-semibold text-gray-600 uppercase tracking-wider;
        }
        tr:hover {
            @apply bg-gray-50;
        }
        .action-links a {
            @apply text-xs px-2 py-1 rounded;
        }
        .edit-link {
            @apply text-blue-600 hover:text-blue-800 hover:bg-blue-100;
        }
        .delete-link {
            @apply text-red-600 hover:text-red-800 hover:bg-red-100;
        }
        footer {
            @apply text-center text-sm text-gray-500 mt-8 py-4 border-t border-gray-200;
        }
        .add-book-btn {
            @apply inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm mb-4;
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
                        <?php else: // Should not happen here due to auth_check, but good for template consistency ?>
                            <a href="login.php">Login</a>
                            <a href="register.php">Register</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-main">
        <main class="content-area">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-700">Welcome,
                    <?php echo htmlspecialchars($_SESSION['username']); ?>!
                </h2>
                <a href="add_book.php" class="add-book-btn whitespace-nowrap">Add New Book</a>
            </div>
            <?php
            if (isset($_SESSION['message'])) {
                echo "<div class='message " . htmlspecialchars($_SESSION['message_type']) . "'><p>" . htmlspecialchars($_SESSION['message']) . "</p></div>";
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            }
            ?>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-700">Book Collection</h3>
            </div>

            <?php if (!empty($books)): ?>
                <div class="overflow-x-auto shadow rounded-lg">
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Year</th>
                                <th>Genre</th>
                                <th>Price (FCFA)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                                    <td><?php echo htmlspecialchars($book['publication_year']); ?></td>
                                    <td><?php echo htmlspecialchars($book['genre']); ?></td>
                                    <td>FCFA <?php echo htmlspecialchars(number_format($book['price'], 0, '.', ',')); ?></td>
                                    <td class="action-links">
                                        <a href="edit_book.php?id=<?php echo $book['book_id']; ?>" class="edit-link">Edit</a>
                                        <a href="delete_book.php?id=<?php echo $book['book_id']; ?>" class="delete-link"
                                            onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-600">No books in the library yet. <a href="add_book.php"
                        class="text-green-500 hover:underline">Add one now!</a></p>
            <?php endif; ?>
        </main>
        <footer class="text-center text-sm text-gray-500 mt-8 py-6">
            <p>&copy; <?php echo date("Y"); ?> Library System. All rights reserved.</p>
        </footer>
    </div>
</body>

</html>
<?php
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>