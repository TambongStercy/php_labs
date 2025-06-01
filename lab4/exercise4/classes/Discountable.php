<?php
namespace LibrarySystem;

interface Discountable
{
    public function getDiscountAmount(): float; // Maybe returns the actual amount of discount
    public function getPriceAfterDiscount(): float;
}
?>