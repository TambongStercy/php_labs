<?php
class Book
{
    // Properties
    public string $title;
    public string $author;
    public int $publication_year;
    public string $genre;
    public float $price;

    // Constructor
    public function __construct(string $title, string $author, int $publication_year, string $genre, float $price)
    {
        $this->title = $title;
        $this->author = $author;
        $this->publication_year = $publication_year;
        $this->genre = $genre;
        $this->price = $price;
    }

    // Method to display book information
    public function displayBookInfo(): void
    {
        echo "<div class='book-info'>";
        echo "<p><strong>Title:</strong> " . htmlspecialchars($this->title) . "</p>";
        echo "<p><strong>Author:</strong> " . htmlspecialchars($this->author) . "</p>";
        echo "<p><strong>Publication Year:</strong> " . htmlspecialchars($this->publication_year) . "</p>";
        echo "<p><strong>Genre:</strong> " . htmlspecialchars($this->genre) . "</p>";
        echo "<p><strong>Price:</strong> FCFA " . htmlspecialchars(number_format($this->price, 0, '.', ',')) . "</p>";
        echo "</div>";
    }
}
?>