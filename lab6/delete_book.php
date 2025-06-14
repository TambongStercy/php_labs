<?php
ob_start(); // Start output buffering
require_once 'auth_check.php';
require_once 'db_connect.php';
require_once 'csrf_token.php'; // Include CSRF token generation

$book_id = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['message'] = "Invalid CSRF token.";
        $_SESSION['message_type'] = 'error';
        error_log("CSRF token validation failed on delete for user ID: " . ($_SESSION['user_id'] ?? 'Not logged in') . ". Session token: " . ($_SESSION['csrf_token'] ?? 'Not set') . ". POST token: " . ($_POST['csrf_token'] ?? 'Not set'));
        header('Location: library.php');
        exit();
    }

    $book_id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($book_id > 0) {
        $conn = connectToDatabase(DB_SERVER_L5, DB_USERNAME_L5, DB_PASSWORD_L5, DB_NAME_L5);

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
        $_SESSION['message'] = 'Invalid book ID for deletion.';
        $_SESSION['message_type'] = 'error';
    }
} else {
    $_SESSION['message'] = 'Invalid request method for deletion.';
    $_SESSION['message_type'] = 'error';
}

header('Location: library.php');
exit();
ob_end_flush(); // Flush the output buffer
?>