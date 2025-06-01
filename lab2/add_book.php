<!-- add_book.php -->
<?php
$conn = new mysqli('localhost', 'root', '12345', 'LibrarySystemDB');
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

$authors = $conn->query("SELECT author_id, author_name FROM Authors");
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Add Book</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        label {
            @apply block text-gray-700 text-sm font-bold mb-2;
        }
        input[type="text"], input[type="number"], select {
            @apply shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50;
        }
        input[type="submit"] {
             @apply bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50;
        }
        .form-group {
            @apply mb-4;
        }
         .container {
             @apply max-w-md mx-auto bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 mt-10;
         }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container">
        <h1 class="text-2xl font-bold mb-6 text-center">Add a New Book</h1>
        <form method="POST" action="process_book.php">
            <div class="form-group">
                <label for="book_title">Title:</label>
                <input type="text" name="book_title" id="book_title" required>
            </div>
            <div class="form-group">
                <label for="author_id">Author:</label>
                <select name="author_id" id="author_id" required>
                     <option value="">-- Select Author --</option>
                    <?php while ($a = $authors->fetch_assoc()): ?>
                        <option value="<?= $a['author_id'] ?>">
                            <?= htmlspecialchars($a['author_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
             <div class="form-group">
                 <label for="genre">Genre:</label>
                 <input type="text" name="genre" id="genre" required>
             </div>
             <div class="form-group">
                 <label for="price">Price:</label>
                 <input type="number" name="price" id="price" step="0.01" min="0" required>
             </div>
            <div class="flex items-center justify-start">
                <input type="submit" value="Add Book">
            </div>
        </form>
    </div>
</body>

</html>
<?php $conn->close(); ?>