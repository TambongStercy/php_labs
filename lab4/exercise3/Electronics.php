<?php
require_once 'Discountable.php'; // Include the interface

class Electronics implements Discountable
{
    public string $itemName;
    public float $price;
    private float $fixedDiscountAmount; // e.g., $5 fixed discount

    public function __construct(string $itemName, float $price, float $fixedDiscountAmount = 5.00)
    {
        $this->itemName = $itemName;
        $this->price = $price;
        $this->fixedDiscountAmount = $fixedDiscountAmount;
    }

    public function getItemName(): string
    {
        return $this->itemName;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    // Implementing Discountable interface method
    public function getDiscount(): float
    {
        // Ensure discount doesn't make price negative
        return min($this->fixedDiscountAmount, $this->price);
    }

    // Implementing Discountable interface method
    public function getPriceAfterDiscount(): float
    {
        return $this->price - $this->getDiscount();
    }

    public function displayDetails(): void
    {
        echo "<div class='item-details'>";
        echo "<p><strong>Electronic Item:</strong> " . htmlspecialchars($this->itemName) . "</p>";
        echo "<p><strong>Original Price:</strong> FCFA " . htmlspecialchars(number_format($this->price, 0, '.', ',')) . "</p>";
        echo "<p><strong>Discount Amount:</strong> FCFA " . htmlspecialchars(number_format($this->getDiscount(), 0, '.', ',')) . "</p>";
        echo "<p><strong>Price After Discount:</strong> FCFA " . htmlspecialchars(number_format($this->getPriceAfterDiscount(), 0, '.', ',')) . "</p>";
        echo "</div>";
    }
}
?>