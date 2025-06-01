<?php
// process_form.php
$conn = new mysqli('localhost', 'root', '12345', 'WebAppDB');
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

$output_html = ''; // Variable to store output HTML

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simple validation
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $age = (int) $_POST['age'];

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // In a real app, redirect back with an error message
        $output_html = '<p class="text-red-500">Invalid input. Please go back and try again.</p>';
    } else {
        // Prepared statement to prevent SQL injection
        $stmt = $conn->prepare(
            "INSERT INTO Users (name, email, age) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("ssi", $name, $email, $age);
        if ($stmt->execute()) {
            $output_html = '<p class="text-green-600 font-semibold">User added successfully!</p>';
            $output_html .= '<p class="mt-4"><a href="view_users.php" class="text-blue-500 hover:text-blue-700 underline">View all users</a></p>';
        } else {
            $output_html = '<p class="text-red-500">Error adding user: ' . htmlspecialchars($stmt->error) . '</p>';
        }
        $stmt->close();
    }
} else {
    // Optional: Handle GET requests or redirect
    header('Location: user_form.php'); // Redirect if accessed directly via GET
    exit;
}
$conn->close();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Processing...</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow-md text-center">
        <?php echo $output_html; // Output the generated HTML ?>
    </div>
</body>

</html>