<?php
// Include database configuration file
require_once 'db_config.php';

// Process delete operation after confirmation
if (isset($_POST["book_id"]) && !empty($_POST["book_id"])) {
    // Prepare a delete statement
    $sql = "DELETE FROM Books WHERE book_id = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_book_id);

        // Set parameters
        $param_book_id = trim($_POST["book_id"]);

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Records deleted successfully. Redirect to landing page
            header("location: read_books.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);

    // Close connection
    mysqli_close($conn);
} else {
    // Check existence of id parameter
    if (empty(trim($_GET["book_id"]))) {
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php"); // You might want to create an error page
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delete Record</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        .btn {
            @apply font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-offset-2;
        }
        .btn-red {
             @apply bg-red-500 hover:bg-red-700 text-white focus:ring-red-500;
        }
        .btn-gray {
             @apply bg-gray-500 hover:bg-gray-700 text-white ml-4 focus:ring-gray-500;
        }
        .container {
             @apply max-w-md mx-auto bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 mt-10 text-center;
         }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container">
        <h2 class="text-2xl font-bold mb-4">Delete Record</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-4">
                <input type="hidden" name="book_id" value="<?php echo trim($_GET["book_id"]); ?>" />
                <p class="text-gray-700 mb-6">Are you sure you want to delete this book record?</p>
                <p>
                    <input type="submit" value="Yes" class="btn btn-red">
                    <a href="read_books.php" class="btn btn-gray">No</a>
                </p>
            </div>
        </form>
    </div>
</body>

</html>