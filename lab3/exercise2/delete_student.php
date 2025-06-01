<?php
session_start();
require_once '../db_connect.php'; // Adjust path
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '12345');
define('DB_NAME', 'StudentDB');
$conn = connectToDatabase(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $student_id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM Students WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "Student deleted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Student not found or already deleted.";
            $_SESSION['message_type'] = "error";
        }
    } else {
        $_SESSION['message'] = "Error deleting student: " . $stmt->error;
        $_SESSION['message_type'] = "error";
    }
    $stmt->close();
} else {
    $_SESSION['message'] = "Invalid student ID for deletion.";
    $_SESSION['message_type'] = "error";
}

$conn->close();
header("Location: view_students.php");
exit();
?>