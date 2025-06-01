<?php
require_once 'Book.php';
require_once 'Electronics.php';
// Discountable.php is included by Book.php and Electronics.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Polymorphism Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        .item-details p, .discount-info p {
            @apply mb-1 text-gray-700;
        }
        .item-details p strong, .discount-info p strong {
            @apply font-semibold text-gray-800;
        }
        .container {
            @apply max-w-lg mx-auto bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 mt-10;
        }
        h2 {
            @apply text-2xl font-bold mb-6 text-center text-gray-800;
        }
        h3 {
            @apply text-xl font-semibold mt-6 mb-3 text-gray-700;
        }
        hr {
            @apply my-6 border-gray-300;
        }
        .fallback-info p {
            @apply mb-1 text-sm text-gray-600;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container">
        <h2>Testing Polymorphism:</h2>

        <?php

        $book = new Book("Learning PHP", 30000, 0.15); // 15% discount, price in FCFA
        $laptop = new Electronics("Super Laptop", 750000, 25000); // Price and discount in FCFA
        $headphones = new Electronics("Noise Cancelling Headphones", 90000, 5000); // Price and discount in FCFA
        
        // Array of objects that implement Discountable
        $products = [$book, $laptop, $headphones];

        foreach ($products as $product) {
            echo "<div class='item-details mb-4 p-4 border border-gray-200 rounded-lg'>";
            if (method_exists($product, 'displayDetails')) {
                $product->displayDetails();
            } else { // Fallback if no specific display method
                echo "<div class='fallback-info'>";
                echo "<p><strong>Item:</strong> " . htmlspecialchars(method_exists($product, 'getTitle') ? $product->getTitle() : $product->getItemName()) . "</p>";
                echo "<p><strong>Original Price:</strong> FCFA " . htmlspecialchars(number_format($product->getPrice(), 0, '.', ',')) . "</p>";
                echo "<p><strong>Discount:</strong> FCFA " . htmlspecialchars(number_format($product->getDiscount(), 0, '.', ',')) . "</p>";
                echo "<p><strong>Final Price:</strong> FCFA " . htmlspecialchars(number_format($product->getPriceAfterDiscount(), 0, '.', ',')) . "</p>";
                echo "</div>";
            }
            echo "</div>";
            if (next($products)) {
                echo "<hr>";
            }
        }

        // Function that expects any Discountable object
        function printDiscountInfo(Discountable $item)
        {
            echo "<div class='discount-info mt-4 p-4 bg-gray-50 rounded-lg'>";
            echo "<p class='text-md font-semibold text-gray-700 mb-2'>Processing item for discount details...</p>";
            echo "<p><strong>Original Price:</strong> FCFA " . htmlspecialchars(number_format($item->getPrice(), 0, '.', ',')) . "</p>";
            echo "<p><strong>Calculated Discount:</strong> FCFA " . htmlspecialchars(number_format($item->getDiscount(), 0, '.', ',')) . "</p>";
            echo "<p><strong>Final Price:</strong> FCFA " . htmlspecialchars(number_format($item->getPriceAfterDiscount(), 0, '.', ',')) . "</p>";
            echo "</div>";
        }

        echo "<h3>Using a function with type hinting for Discountable:</h3>";
        printDiscountInfo($book);
        printDiscountInfo($laptop);

        ?>
    </div>
</body>

</html>