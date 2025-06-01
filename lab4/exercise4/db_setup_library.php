<?php
// This is an optional helper. You can run the SQL above directly in MySQL CLI.
require_once '../db_connect.php'; // Adjust path
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '12345');
// No DB_NAME initially to create the database

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$dbName = 'LibraryDB_OOP';

// Create database
$sqlCreateDB = "CREATE DATABASE IF NOT EXISTS $dbName";
if ($conn->query($sqlCreateDB) === TRUE) {
    echo "Database '$dbName' created successfully or already exists.<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
    $conn->close();
    exit;
}
$conn->close();

// Now connect to the specific database to create tables
define('DB_NAME_L4E4', $dbName);
$conn = connectToDatabase(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME_L4E4);

$sqlBooks = "CREATE TABLE IF NOT EXISTS Books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255),
    price DECIMAL(10, 2),
    genre VARCHAR(100),
    publication_year INT,
    is_ebook BOOLEAN DEFAULT FALSE,
    is_borrowed BOOLEAN DEFAULT FALSE,
    borrowed_by_member_id INT NULL
);";
// Note: The FOREIGN KEY for borrowed_by_member_id requires Members table to exist first.
// For simplicity in this script, we might add it with ALTER TABLE later or ensure order.

$sqlMembers = "CREATE TABLE IF NOT EXISTS Members (
    member_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    membership_date DATE
);";

$sqlBookLoans = "CREATE TABLE IF NOT EXISTS BookLoans (
    loan_id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    member_id INT NOT NULL,
    loan_date DATE NOT NULL,
    return_date DATE NULL,
    FOREIGN KEY (book_id) REFERENCES Books(book_id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES Members(member_id) ON DELETE CASCADE
);";

// Add the foreign key to Books table after Members table is created
$sqlAlterBooksFK = "ALTER TABLE Books
    ADD CONSTRAINT fk_borrowed_by_member
    FOREIGN KEY (borrowed_by_member_id) REFERENCES Members(member_id) ON DELETE SET NULL;";


if ($conn->query($sqlMembers) === TRUE)
    echo "Table 'Members' created successfully or already exists.<br>";
else
    echo "Error creating table Members: " . $conn->error . "<br>";

if ($conn->query($sqlBooks) === TRUE)
    echo "Table 'Books' created successfully or already exists.<br>";
else
    echo "Error creating table Books: " . $conn->error . "<br>";

// Execute ALTER TABLE after both Books and Members exist
$conn->query($sqlAlterBooksFK); // Error checking omitted for brevity here

if ($conn->query($sqlBookLoans) === TRUE)
    echo "Table 'BookLoans' created successfully or already exists.<br>";
else
    echo "Error creating table BookLoans: " . $conn->error . "<br>";


// You can add INSERT statements here as well if you want a fully scripted setup.

echo "Database setup complete.<br>";
$conn->close();
?>