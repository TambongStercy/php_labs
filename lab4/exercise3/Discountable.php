<?php
interface Discountable
{
    // All methods in an interface are implicitly public and abstract
    public function getDiscount(): float; // Returns the discount amount or percentage
    public function getPriceAfterDiscount(): float;
    public function getPrice(): float;
}
?>