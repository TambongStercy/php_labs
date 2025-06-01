<?php
namespace LibrarySystem;

require_once 'Loanable.php';
require_once 'Member.php'; // For type hinting in borrowItem

class Book implements Loanable
{
    protected int $book_id;
    public string $title;
    public string $author;
    public float $price;
    public string $genre;
    public int $publication_year;
    protected bool $is_borrowed_status = false; // Internal status
    protected ?int $borrowed_by_member_id = null;
    protected \mysqli $db; // Database connection

    public function __construct(\mysqli $db_conn, string $title, string $author, float $price, string $genre, int $year)
    {
        $this->db = $db_conn;
        $this->title = $title;
        $this->author = $author;
        $this->price = $price;
        $this->genre = $genre;
        $this->publication_year = $year;
        // book_id would typically be set when loading from DB or after saving a new book
    }

    // Setter for book_id, typically after fetching from DB or saving
    public function setId(int $id): void
    {
        $this->book_id = $id;
    }
    public function getId(): ?int
    {
        return $this->book_id ?? null;
    }

    public function setIsBorrowed(bool $status, ?int $member_id = null): void
    {
        $this->is_borrowed_status = $status;
        $this->borrowed_by_member_id = $member_id;
    }

    public function getBorrowedByMemberId(): ?int
    {
        return $this->borrowed_by_member_id;
    }

    public function borrowItem(Member $member): bool
    {
        if (!$this->isBorrowed() && isset($this->book_id)) {
            $loan_date = date('Y-m-d');
            $member_id = $member->getId();

            $this->db->begin_transaction();
            try {
                // Update Books table
                $stmt_book = $this->db->prepare("UPDATE Books SET is_borrowed = TRUE, borrowed_by_member_id = ? WHERE book_id = ? AND is_borrowed = FALSE");
                $stmt_book->bind_param("ii", $member_id, $this->book_id);
                $stmt_book->execute();

                if ($stmt_book->affected_rows > 0) {
                    // Insert into BookLoans
                    $stmt_loan = $this->db->prepare("INSERT INTO BookLoans (book_id, member_id, loan_date) VALUES (?, ?, ?)");
                    $stmt_loan->bind_param("iis", $this->book_id, $member_id, $loan_date);
                    $stmt_loan->execute();
                    $stmt_loan->close();

                    $this->db->commit();
                    $this->is_borrowed_status = true;
                    $this->borrowed_by_member_id = $member_id;
                    $stmt_book->close();
                    return true;
                } else {
                    $this->db->rollback(); // Book was already borrowed or ID not found
                    $stmt_book->close();
                    return false;
                }
            } catch (\Exception $e) {
                $this->db->rollback();
                // Log error $e->getMessage()
                return false;
            }
        }
        return false;
    }

    public function returnItem(): bool
    {
        if ($this->isBorrowed() && isset($this->book_id) && isset($this->borrowed_by_member_id)) {
            $return_date = date('Y-m-d');
            $this->db->begin_transaction();
            try {
                // Update Books table
                $stmt_book = $this->db->prepare("UPDATE Books SET is_borrowed = FALSE, borrowed_by_member_id = NULL WHERE book_id = ? AND is_borrowed = TRUE");
                $stmt_book->bind_param("i", $this->book_id);
                $stmt_book->execute();

                if ($stmt_book->affected_rows > 0) {
                    // Update BookLoans
                    $stmt_loan = $this->db->prepare("UPDATE BookLoans SET return_date = ? WHERE book_id = ? AND member_id = ? AND return_date IS NULL ORDER BY loan_id DESC LIMIT 1");
                    $stmt_loan->bind_param("sii", $return_date, $this->book_id, $this->borrowed_by_member_id);
                    $stmt_loan->execute();
                    $stmt_loan->close();

                    $this->db->commit();
                    $this->is_borrowed_status = false;
                    $this->borrowed_by_member_id = null;
                    $stmt_book->close();
                    return true;
                } else {
                    $this->db->rollback();
                    $stmt_book->close();
                    return false;
                }
            } catch (\Exception $e) {
                $this->db->rollback();
                // Log error $e->getMessage()
                return false;
            }
        }
        return false;
    }

    public function isBorrowed(): bool
    {
        // Optionally re-fetch from DB for absolute certainty if object state might be stale
        // For this example, we rely on the internal status which should be updated by borrow/return methods
        return $this->is_borrowed_status;
    }

    public function displayInfo(): string
    {
        $status_class = $this->isBorrowed() ? "status-borrowed" : "status-available";
        $status_text = $this->isBorrowed() ? "Borrowed (by Member ID: {$this->borrowed_by_member_id})" : "Available";

        $html = "<div class='book-details'>";
        $html .= "<h3 class='text-lg font-semibold text-blue-700'>" . htmlspecialchars($this->title) . " <span class='text-sm font-normal text-gray-500'> (ID: {$this->book_id})</span></h3>";
        $html .= "<p><strong>Author:</strong> " . htmlspecialchars($this->author) . "</p>";
        $html .= "<p><strong>Genre:</strong> " . htmlspecialchars($this->genre) . "</p>";
        $html .= "<p><strong>Year:</strong> " . htmlspecialchars($this->publication_year) . "</p>";
        $html .= "<p><strong>Price:</strong> FCFA " . htmlspecialchars(number_format($this->price, 0, '.', ',')) . "</p>";
        $html .= "<p><strong>Status:</strong> <span class='{$status_class}'>" . htmlspecialchars($status_text) . "</span></p>";
        $html .= "</div>";
        return $html;
    }

    // Static method to find a book
    public static function findById(\mysqli $db, int $book_id): ?Book
    {
        $stmt = $db->prepare("SELECT title, author, price, genre, publication_year, is_borrowed, borrowed_by_member_id FROM Books WHERE book_id = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $book = new self($db, $row['title'], $row['author'], $row['price'], $row['genre'], $row['publication_year']);
            $book->setId($book_id);
            $book->setIsBorrowed((bool) $row['is_borrowed'], $row['borrowed_by_member_id']);
            $stmt->close();
            return $book;
        }
        $stmt->close();
        return null;
    }

    public static function getAll(\mysqli $db): array
    {
        $books = [];
        $result = $db->query("SELECT book_id, title, author, price, genre, publication_year, is_borrowed, borrowed_by_member_id FROM Books");
        while ($row = $result->fetch_assoc()) {
            $book = new self($db, $row['title'], $row['author'], $row['price'], $row['genre'], $row['publication_year']);
            $book->setId((int) $row['book_id']);
            $book->setIsBorrowed((bool) $row['is_borrowed'], $row['borrowed_by_member_id']);
            $books[] = $book;
        }
        return $books;
    }
}
?>