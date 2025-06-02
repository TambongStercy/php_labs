<?php
require_once 'auth_check.php';
// This page could be used for a detailed view of a single book, or an alternative list.
// For now, it can redirect to library.php or show a message.

$_SESSION['message'] = 'Book viewing is handled within the main library page.';
$_SESSION['message_type'] = 'info'; // Using 'info' for neutral messages
header('Location: library.php');
exit();
?>