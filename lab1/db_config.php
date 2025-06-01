<?php

// --- Database Connection Details ---
$servername = "localhost";      // Or "127.0.0.1"
$username = "root";             // Your MySQL username (default is often root)
$password = "12345"; // CHANGE THIS to your actual MySQL password
$dbname = "librarydb";          // The database name from your course exercises

// --- Create Connection ---
// Use the mysqli constructor with the connection details
$conn = new mysqli($servername, $username, $password, $dbname);

// --- Check Connection ---
// The connect_error property will contain an error message if the connection failed
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

?>