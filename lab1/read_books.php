<?php
// Include database configuration file
require_once 'db_config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Books</title>
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
        .action-link {
            @apply text-indigo-600 hover:text-indigo-900 mr-3;
        }
        .add-link {
            @apply inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mb-4;
        }
        .container {
            @apply max-w-4xl mx-auto mt-10;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Book List</h2>
            <a href="create_book.php" class="add-link">Add New Book</a>
        </div>
        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Year</th>
                        <th>Genre</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    // Attempt select query execution
                    $sql = "SELECT * FROM Books";
                    if ($result = mysqli_query($conn, $sql)) {
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_array($result)) {
                                echo "<tr>";
                                echo "<td>" . $row['book_id'] . "</td>";
                                echo "<td>" . $row['title'] . "</td>";
                                echo "<td>" . $row['author'] . "</td>";
                                echo "<td>" . $row['publication_year'] . "</td>";
                                echo "<td>" . $row['genre'] . "</td>";
                                echo "<td>$" . number_format($row['price'], 2) . "</td>"; // Format price
                                echo "<td>";
                                echo "<a href='update_book.php?book_id=" . $row['book_id'] . "' title='Update Record' class='action-link'>Update</a>";
                                echo "<a href='delete_book.php?book_id=" . $row['book_id'] . "' title='Delete Record' class='action-link text-red-600 hover:text-red-900'>Delete</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            // Free result set
                            mysqli_free_result($result);
                        } else {
                            echo "<tr><td colspan='7' class='text-center text-gray-500 py-4'>No records were found.</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center text-red-500 py-4'>ERROR: Could not able to execute $sql. " . mysqli_error($conn) . "</td></tr>";
                    }

                    // Close connection
                    mysqli_close($conn);
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>