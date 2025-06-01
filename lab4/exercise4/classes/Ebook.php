<?php
namespace LibrarySystem;

require_once 'Book.php';
require_once 'Discountable.php';

class Ebook extends Book implements Discountable
{
    private float $discount_percentage;

    public function __construct(\mysqli $db_conn, string $title, string $author, float $price, string $genre, int $year, float $discount_percentage = 0.05)
    { // 5% default discount for ebooks
        parent::__construct($db_conn, $title, $author, $price, $genre, $year);
        $this->discount_percentage = $discount_percentage;
        // Ebooks are typically not "borrowed" in the same physical sense,
        // but we inherit the methods. We might override borrowItem/returnItem to do nothing or throw an exception.
        // For this exercise, we'll let them be, but mark them as not borrowable for a member.
    }

    // Ebooks cannot be borrowed in the library system (assumed)
    public function borrowItem(Member $member): bool
    {
        // echo "Ebooks cannot be physically borrowed through this system.<br>";
        return false; // Override to prevent borrowing
    }

    public function returnItem(): bool
    {
        // echo "Ebooks do not need to be returned.<br>";
        return false; // Override
    }

    public function isBorrowed(): bool
    {
        return false; // Ebooks are not considered borrowed in this context
    }

    public function downloadLink(): string
    {
        return "Simulated download link for " . htmlspecialchars($this->title) . "<br>";
    }

    // Implementing Discountable
    public function getDiscountAmount(): float
    {
        return $this->price * $this->discount_percentage;
    }

    public function getPriceAfterDiscount(): float
    {
        return $this->price - $this->getDiscountAmount();
    }

    public function displayInfo(): string
    {
        // Get base book info, but we will reconstruct it to control layout better
        $html = "<div class='ebook-details'>";
        $html .= "<h3 class='text-lg font-semibold text-purple-700'>" . htmlspecialchars($this->title) . " <span class='text-sm font-normal text-gray-500'> (ID: {$this->book_id}) - Ebook</span></h3>";
        $html .= "<p><strong>Author:</strong> " . htmlspecialchars($this->author) . "</p>";
        $html .= "<p><strong>Genre:</strong> " . htmlspecialchars($this->genre) . "</p>";
        $html .= "<p><strong>Year:</strong> " . htmlspecialchars($this->publication_year) . "</p>";
        $html .= "<p><strong>Original Price:</strong> FCFA " . htmlspecialchars(number_format($this->price, 0, '.', ',')) . "</p>";
        $html .= "<p class='text-purple-600'><strong>Discounted Price:</strong> FCFA " . htmlspecialchars(number_format($this->getPriceAfterDiscount(), 0, '.', ','))
            . " <span class='text-xs'>(" . ($this->discount_percentage * 100) . "% discount)</span></p>";
        $html .= "<p class='mt-1'><a href='#' class='text-sm text-blue-500 hover:underline'>" . $this->downloadLink() . "</a></p>";
        // No borrow status for Ebooks in this representation
        $html .= "</div>";
        return $html;
    }

    public static function findById(\mysqli $db, int $book_id): ?Ebook
    { // Override to return Ebook type
        $stmt = $db->prepare("SELECT title, author, price, genre, publication_year FROM Books WHERE book_id = ? AND is_ebook = TRUE");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            // Assuming a standard discount for ebooks fetched this way, or add discount_percentage to DB
            $ebook = new self($db, $row['title'], $row['author'], $row['price'], $row['genre'], $row['publication_year']);
            $ebook->setId($book_id);
            $stmt->close();
            return $ebook;
        }
        $stmt->close();
        return null;
    }

    public static function getAll(\mysqli $db): array
    { // Override to return Ebook type
        $ebooks = [];
        $result = $db->query("SELECT book_id, title, author, price, genre, publication_year FROM Books WHERE is_ebook = TRUE");
        while ($row = $result->fetch_assoc()) {
            $ebook = new self($db, $row['title'], $row['author'], $row['price'], $row['genre'], $row['publication_year']);
            $ebook->setId((int) $row['book_id']);
            $ebooks[] = $ebook;
        }
        return $ebooks;
    }
}
?>