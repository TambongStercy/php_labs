<?php
namespace LibrarySystem; // Using a namespace

interface Loanable
{
    public function borrowItem(Member $member): bool;
    public function returnItem(): bool;
    public function isBorrowed(): bool;
}
?>