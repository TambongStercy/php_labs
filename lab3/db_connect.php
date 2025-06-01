<?php
// --- !!IMPORTANT!! ---
// For Lab 3, Exercise 1 (EmployeeDB)
// define('DB_SERVER_L3E1', 'localhost');
// define('DB_USERNAME_L3E1', 'your_db_user'); // e.g., root
// define('DB_PASSWORD_L3E1', 'your_db_password');
// define('DB_NAME_L3E1', 'EmployeeDB');

// For Lab 3, Exercise 2 (StudentDB)
// define('DB_SERVER_L3E2', 'localhost');
// define('DB_USERNAME_L3E2', 'your_db_user');
// define('DB_PASSWORD_L3E2', 'your_db_password');
// define('DB_NAME_L3E2', 'StudentDB');

// For Lab 4, Exercise 4 (LibraryDB_OOP) - Suffix OOP to avoid clash with Lab 1's LibraryDB
// define('DB_SERVER_L4E4', 'localhost');
// define('DB_USERNAME_L4E4', 'your_db_user');
// define('DB_PASSWORD_L4E4', 'your_db_password');
// define('DB_NAME_L4E4', 'LibraryDB_OOP');


// --- General Purpose Connection Function ---
function connectToDatabase($server, $username, $password, $db_name)
{
    $conn = new mysqli($server, $username, $password, $db_name);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Example usage (you'll call this in your specific lab files):
/*
To connect to EmployeeDB:
require_once 'db_connect.php'; // Adjust path if needed
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'your_user');
define('DB_PASSWORD', 'your_password');
define('DB_NAME', 'EmployeeDB');
$conn = connectToDatabase(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

To connect to StudentDB:
require_once 'db_connect.php'; // Adjust path if needed
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'your_user');
define('DB_PASSWORD', 'your_password');
define('DB_NAME', 'StudentDB');
$conn = connectToDatabase(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
*/
?>