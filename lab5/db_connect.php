<?php
// --- !!IMPORTANT!! ---
define('DB_SERVER_L5', 'localhost');
define('DB_USERNAME_L5', 'root'); // Replace with your actual DB username e.g., root
define('DB_PASSWORD_L5', '12345'); // Replace with your actual DB password
define('DB_NAME_L5', 'LibraryDB_L5'); // Specific DB for Lab 5

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

// To connect in Lab 5 files:
// require_once 'db_connect.php';
// $conn = connectToDatabase(DB_SERVER_L5, DB_USERNAME_L5, DB_PASSWORD_L5, DB_NAME_L5);
?>