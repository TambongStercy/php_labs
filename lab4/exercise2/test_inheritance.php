<?php
require_once 'Book.php'; // This will also include Product.php because Book.php requires it
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Product & Book Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        .product-info p, .book-details p {
            @apply mb-1 text-gray-700;
        }
        .product-info p strong, .book-details p strong {
            @apply font-semibold text-gray-800;
        }
        .container {
             @apply max-w-md mx-auto bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 mt-10;
        }
        h3 {
            @apply text-xl font-semibold mt-4 mb-2 text-gray-800;
        }
        hr {
            @apply my-6 border-gray-300;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container">
        <h2 class="text-2xl font-bold mb-6 text-center">Testing Inheritance:</h2>
        <?php
        echo "<h3>Generic Product:</h3>";
        $genericProduct = new Product("Super Widget", 19.99);
        $genericProduct->displayProduct();

        echo "<hr>";

        echo "<h3>Book Product:</h3>";
        $book = new Book(
            "PHP Objects and Patterns", // title (product_name)
            29.95,                    // price (product_price)
            "Matt Zandstra",          // author
            2010,                     // publication_year
            "Programming"             // genre
        );
        $book->displayProduct();
        ?>
    </div>
</body>

</html>