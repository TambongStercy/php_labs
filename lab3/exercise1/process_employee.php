<?php
session_start(); // For displaying messages
require_once '../db_connect.php'; // Adjust path
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '12345');
define('DB_NAME', 'EmployeeDB');
$conn = connectToDatabase(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emp_name = trim($_POST['emp_name']);
    $emp_salary = trim($_POST['emp_salary']);
    $emp_dept_id = trim($_POST['emp_dept_id']);

    // Basic Validation
    if (empty($emp_name) || empty($emp_salary) || empty($emp_dept_id)) {
        $_SESSION['message'] = "All fields are required.";
        $_SESSION['message_type'] = "error";
        header("Location: add_employee.php");
        exit();
    }
    if (!is_numeric($emp_salary) || $emp_salary < 0) {
        $_SESSION['message'] = "Salary must be a valid positive number.";
        $_SESSION['message_type'] = "error";
        header("Location: add_employee.php");
        exit();
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO Employee (emp_name, emp_salary, emp_dept_id) VALUES (?, ?, ?)");
    $stmt->bind_param("sdi", $emp_name, $emp_salary, $emp_dept_id); // s for string, d for double/decimal, i for integer

    if ($stmt->execute()) {
        $_SESSION['message'] = "New employee added successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
        $_SESSION['message_type'] = "error";
    }
    $stmt->close();
    $conn->close();
    header("Location: view_employees.php"); // Redirect to view page
    exit();
} else {
    header("Location: add_employee.php");
    exit();
}
?>