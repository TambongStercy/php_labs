<?php
require_once 'auth_check.php';
require_once 'db_connect.php'; // For database connection
require_once 'csrf_token.php'; // Include CSRF token generation

$book_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Initialize variables for form fields and errors
$title = $author = $publication_year = $genre = $price = "";
$errors = [];

if ($book_id === 0) {
    $_SESSION['message'] = 'Invalid book ID.';
    $_SESSION['message_type'] = 'error';
    header('Location: library.php');
    exit();
}

$conn = connectToDatabase(DB_SERVER_L5, DB_USERNAME_L5, DB_PASSWORD_L5, DB_NAME_L5);

// Handle Form Submission (POST request)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "Invalid CSRF token.";
        error_log("CSRF token validation failed for user ID: " . ($_SESSION['user_id'] ?? 'Not logged in') . ". Session token: " . ($_SESSION['csrf_token'] ?? 'Not set') . ". POST token: " . ($_POST['csrf_token'] ?? 'Not set'));
    } else {
        // Sanitize and retrieve form data
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $publication_year = trim($_POST['publication_year']);
        $genre = trim($_POST['genre']);
        $price = trim($_POST['price']);

        // Basic Validation
        if (empty($title)) {
            $errors[] = "Title is required.";
        }
        if (!empty($publication_year) && !filter_var($publication_year, FILTER_VALIDATE_INT)) {
            $errors[] = "Publication year must be a valid integer.";
        }
        if (!empty($price) && !filter_var($price, FILTER_VALIDATE_FLOAT) && $price !== '0') {
            $errors[] = "Price must be a valid number.";
        } elseif (empty($price)) {
            $price = null; // Allow price to be optional, set to NULL if empty
        }

        if (empty($errors)) {
            $sql = "UPDATE Books SET title = ?, author = ?, publication_year = ?, genre = ?, price = ? WHERE book_id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $pub_year_to_update = !empty($publication_year) ? (int) $publication_year : null;
                $stmt->bind_param("ssisdi", $title, $author, $pub_year_to_update, $genre, $price, $book_id);

                if ($stmt->execute()) {
                    $_SESSION['message'] = "Book '" . htmlspecialchars($title) . "' updated successfully!";
                    $_SESSION['message_type'] = "success";
                    $stmt->close();
                    // $conn->close(); // Close connection after redirect
                    header("Location: library.php");
                    exit();
                } else {
                    $errors[] = "Error updating book: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $errors[] = "Error preparing statement: " . $conn->error;
            }
        } // If errors, form will be redisplayed with $title, $author etc. holding submitted values
    }
} else { // This is a GET request, so fetch existing book data
    $stmt_fetch = $conn->prepare("SELECT title, author, publication_year, genre, price FROM Books WHERE book_id = ?");
    if ($stmt_fetch) {
        $stmt_fetch->bind_param("i", $book_id);
        $stmt_fetch->execute();
        $result = $stmt_fetch->get_result();
        if ($result->num_rows === 1) {
            $book = $result->fetch_assoc();
            $title = $book['title'];
            $author = $book['author'];
            $publication_year = $book['publication_year'];
            $genre = $book['genre'];
            $price = $book['price'];
        } else {
            $_SESSION['message'] = 'Book not found.';
            $_SESSION['message_type'] = 'error';
            // $conn->close(); // Close before redirect
            header('Location: library.php');
            exit();
        }
        $stmt_fetch->close();
    } else {
        $_SESSION['message'] = 'Error preparing to fetch book: ' . $conn->error;
        $_SESSION['message_type'] = 'error';
        // $conn->close(); // Close before redirect
        header('Location: library.php');
        exit();
    }
}

// $conn->close(); // Connection should be closed at the very end or after operations are done.
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Book - Library System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/style.css">
    <style type="text/tailwindcss">
        body { @apply bg-gray-100; }
        .container-main { @apply max-w-5xl mx-auto; }
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
            @apply mt-8 p-8 bg-white shadow-md rounded-lg max-w-xl mx-auto; /* Centering the form area */
        }
        h2 { @apply text-2xl font-bold text-gray-800 mb-6; } /* For main form title */
        label { @apply block text-gray-700 text-sm font-bold mb-2; }
        input[type='text'], input[type='number'], textarea, select {
            @apply shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 mb-4;
        }
        button[type='submit'] {
            @apply bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50;
        }
        nav a { @apply text-blue-500 hover:text-blue-700 px-3; }
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
                        <a href="library.php"
                            class="<?php echo basename($_SERVER['PHP_SELF']) == 'library.php' || basename($_SERVER['PHP_SELF']) == 'add_book.php' || basename($_SERVER['PHP_SELF']) == 'edit_book.php' ? 'active' : ''; ?>">My
                            Library</a>
                        <a href="profile.php"
                            class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">Profile</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-main">
        <main class="content-area">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Book (ID: <?php echo htmlspecialchars($book_id); ?>)
            </h2>

            <?php
            if (!empty($errors)) {
                echo "<div class='message error mb-4'>";
                foreach ($errors as $error) {
                    echo "<p>" . htmlspecialchars($error) . "</p>";
                }
                echo "</div>";
            }
            // Display session messages (e.g., from redirect after add/delete or other errors)
            if (isset($_SESSION['message'])) {
                echo "<div class='message " . htmlspecialchars($_SESSION['message_type']) . " mb-4'><p>" . htmlspecialchars($_SESSION['message']) . "</p></div>";
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            }
            ?>

            <form action="edit_book.php?id=<?php echo htmlspecialchars($book_id); ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($title); ?>" required>
                </div>
                <div class="form-group">
                    <label for="author">Author:</label>
                    <input type="text" name="author" id="author" value="<?php echo htmlspecialchars($author); ?>">
                </div>
                <div class="form-group">
                    <label for="publication_year">Year:</label>
                    <input type="number" name="publication_year" id="publication_year"
                        value="<?php echo htmlspecialchars($publication_year); ?>">
                </div>
                <div class="form-group">
                    <label for="genre">Genre:</label>
                    <input type="text" name="genre" id="genre" value="<?php echo htmlspecialchars($genre); ?>">
                </div>
                <div class="form-group">
                    <label for="price">Price (FCFA):</label>
                    <input type="number" step="any" name="price" id="price"
                        value="<?php echo htmlspecialchars($price); ?>">
                </div>
                <div class="mt-6">
                    <button type="submit"
                        class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-opacity-50">
                        Save Changes
                    </button>
                    <a href="library.php" class="ml-4 text-gray-600 hover:text-gray-800">Cancel</a>
                </div>
            </form>

            <p class="mt-8"><a href="library.php" class="text-blue-500 hover:underline">&larr; Back to Library</a></p>
        </main>
    </div>
</body>

</html>
<?php if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
} ?>