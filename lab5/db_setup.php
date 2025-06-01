<?php
require_once 'db_connect.php'; // For DB_SERVER_L5, DB_USERNAME_L5, DB_PASSWORD_L5

// --- Connect without specifying a database to create it ---
$conn_init = new mysqli(DB_SERVER_L5, DB_USERNAME_L5, DB_PASSWORD_L5);
if ($conn_init->connect_error) {
    die("Initial connection failed: " . $conn_init->connect_error);
}

// --- Create Database ---
$dbName = DB_NAME_L5; // 'LibraryDB_L5'
$sqlCreateDB = "CREATE DATABASE IF NOT EXISTS `" . $conn_init->real_escape_string($dbName) . "`";
if ($conn_init->query($sqlCreateDB) === TRUE) {
    echo "Database '$dbName' created successfully or already exists.<br>";
} else {
    echo "Error creating database '$dbName': " . $conn_init->error . "<br>";
    $conn_init->close();
    exit;
}
$conn_init->close();

// --- Now connect to the specific database to create tables ---
$conn = connectToDatabase(DB_SERVER_L5, DB_USERNAME_L5, DB_PASSWORD_L5, DB_NAME_L5);

// --- Users Table ---
$sqlUsers = "CREATE TABLE IF NOT EXISTS Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- For hashed passwords
    google_id VARCHAR(255) NULL UNIQUE, -- For Google OAuth users
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);";
if ($conn->query($sqlUsers) === TRUE) {
    echo "Table 'Users' created successfully or already exists.<br>";
} else {
    echo "Error creating table 'Users': " . $conn->error . "<br>";
}

// --- Books Table (similar to Lab 1's or Lab 4's Books table) ---
$sqlBooks = "CREATE TABLE IF NOT EXISTS Books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255),
    publication_year INT,
    genre VARCHAR(100),
    price FLOAT, -- Price in FCFA
    added_by_user_id INT NULL, -- Optional: track who added the book
    FOREIGN KEY (added_by_user_id) REFERENCES Users(id) ON DELETE SET NULL
);";
if ($conn->query($sqlBooks) === TRUE) {
    echo "Table 'Books' created successfully or already exists.<br>";
} else {
    echo "Error creating table 'Books': " . $conn->error . "<br>";
}

// Insert a sample book if table is empty (optional)
$checkBooks = $conn->query("SELECT COUNT(*) as count FROM Books");
$bookCount = $checkBooks->fetch_assoc()['count'];
if ($bookCount == 0) {
    // Prices are in FCFA as per custom instructions
    $conn->query("INSERT INTO Books (title, author, publication_year, genre, price) VALUES ('The Lord of the Rings', 'J.R.R. Tolkien', 1954, 'Fantasy', 15000)");
    echo "Sample book inserted.<br>";
}

echo "Database setup complete!<br>";
$conn->close();
?>