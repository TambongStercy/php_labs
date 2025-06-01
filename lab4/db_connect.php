<?php

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

?>