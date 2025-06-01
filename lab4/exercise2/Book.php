<?php
require_once 'Product.php'; // Include the parent class

class Book extends Product
{ // 'extends' keyword for inheritance
    public string $author;
    public int $publication_year;
    public string $genre;

    // Constructor for Book, also calls parent constructor
    public function __construct(string $title, float $price, string $author, int $publication_year, string $genre)
    {
        // Call the parent class's constructor
        parent::__construct($title, $price); // $title is product_name for a book

        // Initialize Book-specific properties
        $this->author = $author;
        $this->publication_year = $publication_year;
        $this->genre = $genre;
    }

    // Override the displayProduct() method
    public function displayProduct(): void
    {
        parent::displayProduct(); // Call the parent's method to display name and price
        echo "<div class='book-details'>"; // Added a class for potentially different styling
        echo "<p><strong>Author:</strong> " . htmlspecialchars($this->author) . "</p>";
        echo "<p><strong>Publication Year:</strong> " . htmlspecialchars($this->publication_year) . "</p>";
        echo "<p><strong>Genre:</strong> " . htmlspecialchars($this->genre) . "</p>";
        echo "</div>";
    }
}
?>