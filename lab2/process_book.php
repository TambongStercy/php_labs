<?php
// process_book.php
$conn = new mysqli('localhost', 'root', '12345', 'LibrarySystemDB');
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

$output_html = ''; // Variable to store output HTML

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['book_title']);
    $authorId = (int) $_POST['author_id'];
    $genre = trim($_POST['genre']);
    $price = (float) $_POST['price'];

    // Basic validation
    if ($title === '' || $authorId <= 0 || $genre === '' || $price <= 0) {
        // In a real app, redirect back with an error message
        $output_html = '<p class="text-red-500">Invalid input. Please check all fields and try again.</p>';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO Books (book_title, author_id, genre, price)
           VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("sisd", $title, $authorId, $genre, $price);
        if ($stmt->execute()) {
            $output_html = '<p class="text-green-600 font-semibold">Book added successfully!</p>';
            $output_html .= '<p class="mt-4"><a href="view_books.php" class="text-blue-500 hover:text-blue-700 underline">View book list</a></p>';
        } else {
            $output_html = '<p class="text-red-500">Error adding book: ' . htmlspecialchars($stmt->error) . '</p>';
        }
        $stmt->close();
    }
} else {
    // Redirect if accessed directly via GET
    header('Location: add_book.php');
    exit;
}
$conn->close();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Processing Book...</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow-md text-center">
        <?php echo $output_html; // Output the generated HTML ?>
    </div>
</body>

</html>