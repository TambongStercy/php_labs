<?php
require_once 'Discountable.php'; // Include the interface

// For simplicity, let's redefine a basic Book class here that implements Discountable
// In a real scenario, you might have the Book class from Ex2 extend Product and implement Discountable
class Book implements Discountable
{
    public string $title;
    public float $price;
    private float $discount_percentage; // e.g., 0.10 for 10%

    public function __construct(string $title, float $price, float $discount_percentage = 0.10)
    {
        $this->title = $title;
        $this->price = $price;
        $this->discount_percentage = $discount_percentage;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    // Implementing Discountable interface method
    public function getDiscount(): float
    {
        return $this->price * $this->discount_percentage;
    }

    // Implementing Discountable interface method
    public function getPriceAfterDiscount(): float
    {
        return $this->price - $this->getDiscount();
    }

    public function displayDetails(): void
    {
        echo "<div class='item-details'>";
        echo "<p><strong>Book:</strong> " . htmlspecialchars($this->title) . "</p>";
        echo "<p><strong>Original Price:</strong> FCFA " . htmlspecialchars(number_format($this->price, 0, '.', ',')) . "</p>";
        echo "<p><strong>Discount Amount:</strong> FCFA " . htmlspecialchars(number_format($this->getDiscount(), 0, '.', ',')) . "</p>";
        echo "<p><strong>Price After Discount:</strong> FCFA " . htmlspecialchars(number_format($this->getPriceAfterDiscount(), 0, '.', ',')) . "</p>";
        echo "</div>";
    }
}
?>