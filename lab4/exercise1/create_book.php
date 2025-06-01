<?php
require_once 'Book.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Book Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        .book-info p {
            @apply mb-1 text-gray-700;
        }
        .book-info p strong {
            @apply font-semibold text-gray-800;
        }
        .container {
             @apply max-w-md mx-auto bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 mt-10;
        }
        hr {
            @apply my-4 border-gray-300;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container">
        <h2 class="text-2xl font-bold mb-6 text-center">Book Details:</h2>
        <?php
        // Create (instantiate) an object of the Book class
        $book1 = new Book("The Great Gatsby", "F. Scott Fitzgerald", 1925, "Classic", 10.99);
        $book1->displayBookInfo();

        echo "<hr>";

        // Another book (values could also be set after instantiation if properties are public and no constructor enforced them)
        $book2 = new Book("To Kill a Mockingbird", "Harper Lee", 1960, "Fiction", 7.99);
        // If properties were public and not set via constructor:
// $book2->title = "To Kill a Mockingbird";
// $book2->author = "Harper Lee";
// ...
        $book2->displayBookInfo();

        ?>
    </div>
</body>

</html>