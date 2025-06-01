<?php
session_start();
require_once '../db_connect.php'; // Adjust path
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '12345');
define('DB_NAME', 'StudentDB');
$conn = connectToDatabase(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);

    // Basic Validation
    if (empty($name) || empty($email)) {
        $_SESSION['message'] = "Name and Email are required.";
        $_SESSION['message_type'] = "error";
        header("Location: add_student.php"); // Or view_students.php
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format.";
        $_SESSION['message_type'] = "error";
        header("Location: add_student.php");
        exit();
    }

    // Check if email already exists
    $check_stmt = $conn->prepare("SELECT student_id FROM Students WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    if ($check_result->num_rows > 0) {
        $_SESSION['message'] = "Error: Email already exists.";
        $_SESSION['message_type'] = "error";
        $check_stmt->close();
        header("Location: add_student.php");
        exit();
    }
    $check_stmt->close();


    $stmt = $conn->prepare("INSERT INTO Students (name, email, phone_number) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $phone_number);

    if ($stmt->execute()) {
        $_SESSION['message'] = "New student added successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
        $_SESSION['message_type'] = "error";
    }
    $stmt->close();
    $conn->close();
    header("Location: view_students.php");
    exit();
} else {
    header("Location: add_student.php");
    exit();
}
?>