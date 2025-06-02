<?php
require_once 'auth_check.php';
require_once 'db_connect.php';

$book_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $book_id > 0) { // Or POST if you prefer for delete actions
    $conn = connectToDatabase(DB_SERVER_L5, DB_USERNAME_L5, DB_PASSWORD_L5, DB_NAME_L5);

    // Optional: Check if the book is associated with the user or if admin, etc.
    // For now, we'll assume any authenticated user can delete.

    $stmt = $conn->prepare("DELETE FROM Books WHERE book_id = ?");
    $stmt->bind_param("i", $book_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "Book (ID: {$book_id}) deleted successfully.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Book (ID: {$book_id}) not found or already deleted.";
            $_SESSION['message_type'] = 'error';
        }
    } else {
        $_SESSION['message'] = "Error deleting book: " . $stmt->error;
        $_SESSION['message_type'] = 'error';
    }
    $stmt->close();
    $conn->close();
} else {
    $_SESSION['message'] = 'Invalid request or book ID for deletion.';
    $_SESSION['message_type'] = 'error';
}

header('Location: library.php');
exit();
?>