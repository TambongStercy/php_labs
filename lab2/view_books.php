<?php
// view_books.php
$conn = new mysqli('localhost', 'root', '12345', 'LibrarySystemDB');
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

$sql = "
  SELECT b.book_title, a.author_name, b.genre, b.price
  FROM Books AS b
  JOIN Authors AS a ON b.author_id = a.author_id
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Library Catalog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        th {
            @apply px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider;
        }
        td {
            @apply px-6 py-4 whitespace-nowrap text-sm text-gray-900;
        }
        tr:nth-child(even) {
             @apply bg-gray-50;
         }
        .add-link {
            @apply inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded;
        }
        .container {
            @apply max-w-4xl mx-auto mt-10;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Book Catalog</h1>
            <a href="add_book.php" class="add-link">Add New Book</a>
        </div>
        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Genre</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['book_title']) ?></td>
                                <td><?= htmlspecialchars($row['author_name']) ?></td>
                                <td><?= htmlspecialchars($row['genre']) ?></td>
                                <td>$<?= number_format(htmlspecialchars($row['price']), 2) ?></td> <?php // Format price ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-gray-500 py-4">No books found in the catalog.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
<?php $conn->close(); ?>