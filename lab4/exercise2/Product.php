<?php
class Product
{
    public string $product_name;
    public float $product_price;

    public function __construct(string $name, float $price)
    {
        $this->product_name = $name;
        $this->product_price = $price;
    }

    public function displayProduct(): void
    {
        echo "<div class='product-info'>";
        echo "<p><strong>Product Name:</strong> " . htmlspecialchars($this->product_name) . "</p>";
        echo "<p><strong>Price:</strong> FCFA " . htmlspecialchars(number_format($this->product_price, 0, '.', ',')) . "</p>";
        echo "</div>";
    }
}
?>