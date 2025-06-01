<?php
namespace LibrarySystem;

class Member
{
    private int $member_id;
    public string $name;
    public string $email;
    public string $membership_date; // Store as YYYY-MM-DD string or DateTime object
    private \mysqli $db;

    public function __construct(\mysqli $db_conn, string $name, string $email, string $membership_date)
    {
        $this->db = $db_conn;
        $this->name = $name;
        $this->email = $email;
        $this->membership_date = $membership_date;
    }

    public function setId(int $id): void
    {
        $this->member_id = $id;
    }
    public function getId(): ?int
    {
        return $this->member_id ?? null;
    }

    public function viewBorrowedBooks(): array
    {
        $borrowed = [];
        if (!isset($this->member_id))
            return $borrowed;

        $stmt = $this->db->prepare(
            "SELECT b.book_id, b.title, b.author, b.is_ebook, bl.loan_date
             FROM Books b
             JOIN BookLoans bl ON b.book_id = bl.book_id
             WHERE bl.member_id = ? AND bl.return_date IS NULL"
        );
        $stmt->bind_param("i", $this->member_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            // For simplicity, just returning array data. Could return Book objects.
            $borrowed[] = $row;
        }
        $stmt->close();
        return $borrowed;
    }

    public function displayInfo(): string
    {
        $html = "<div class='member-details'>";
        $html .= "<h3 class='text-lg font-semibold text-green-700'>" . htmlspecialchars($this->name) . " <span class='text-sm font-normal text-gray-500'> (ID: {$this->member_id})</span></h3>";
        $html .= "<p><strong>Email:</strong> " . htmlspecialchars($this->email) . "</p>";
        $html .= "<p><strong>Member Since:</strong> " . htmlspecialchars(date('d M Y', strtotime($this->membership_date))) . "</p>";
        $html .= "</div>";
        return $html;
    }

    public static function findById(\mysqli $db, int $member_id): ?Member
    {
        $stmt = $db->prepare("SELECT name, email, membership_date FROM Members WHERE member_id = ?");
        $stmt->bind_param("i", $member_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $member = new self($db, $row['name'], $row['email'], $row['membership_date']);
            $member->setId($member_id);
            $stmt->close();
            return $member;
        }
        $stmt->close();
        return null;
    }
}
?>