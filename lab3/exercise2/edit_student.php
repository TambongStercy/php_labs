<?php
session_start();
require_once '../db_connect.php'; // Adjust path
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '12345');
define('DB_NAME', 'StudentDB');
$conn = connectToDatabase(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

$student_id = null;
$student = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $student_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT name, email, phone_number FROM Students WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();
    } else {
        $_SESSION['message'] = "Student not found.";
        $_SESSION['message_type'] = "error";
        header("Location: view_students.php");
        exit();
    }
    $stmt->close();
} else {
    $_SESSION['message'] = "Invalid student ID.";
    $_SESSION['message_type'] = "error";
    header("Location: view_students.php");
    exit();
}

// Handle form submission for update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['student_id'])) {
    $sid = $_POST['student_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);

    if (empty($name) || empty($email)) {
        $_SESSION['message'] = "Name and Email are required.";
        $_SESSION['message_type'] = "error";
        // To keep the form populated on error, we would typically reload edit_student.php?id=$sid
        // For simplicity, just showing error message here and user has to go back.
        // A better approach would be to show error on the edit form itself.
        header("Location: edit_student.php?id=" . $sid);
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format.";
        $_SESSION['message_type'] = "error";
        header("Location: edit_student.php?id=" . $sid);
        exit();
    }

    // Check if email already exists for ANOTHER student
    $check_stmt = $conn->prepare("SELECT student_id FROM Students WHERE email = ? AND student_id != ?");
    $check_stmt->bind_param("si", $email, $sid);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    if ($check_result->num_rows > 0) {
        $_SESSION['message'] = "Error: Email already exists for another student.";
        $_SESSION['message_type'] = "error";
        $check_stmt->close();
        header("Location: edit_student.php?id=" . $sid);
        exit();
    }
    $check_stmt->close();


    $update_stmt = $conn->prepare("UPDATE Students SET name = ?, email = ?, phone_number = ? WHERE student_id = ?");
    $update_stmt->bind_param("sssi", $name, $email, $phone_number, $sid);

    if ($update_stmt->execute()) {
        $_SESSION['message'] = "Student updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating student: " . $update_stmt->error;
        $_SESSION['message_type'] = "error";
    }
    $update_stmt->close();
    $conn->close();
    header("Location: view_students.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Student</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        label {
            @apply block text-gray-700 text-sm font-bold mb-2;
        }
        input[type="text"], input[type="email"] {
            @apply shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50;
        }
        input[type="submit"] {
             @apply bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50;
        }
        .form-group {
            @apply mb-4;
        }
        .container {
             @apply max-w-md mx-auto bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 mt-10;
        }
        .message {
            @apply p-3 mb-4 rounded-md text-center;
        }
        .error {
            @apply bg-red-100 text-red-700 border border-red-300;
        }
        a {
            @apply text-blue-500 hover:text-blue-700;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container">
        <h2 class="text-2xl font-bold mb-6 text-center">Edit Student</h2>

        <?php
        if (isset($_SESSION['message']) && $_SESSION['message_type'] == 'error') { // Only show errors here
            echo "<div class='message " . htmlspecialchars($_SESSION['message_type']) . "'>" . htmlspecialchars($_SESSION['message']) . "</div>";
            unset($_SESSION['message']); // Clear message after displaying
            unset($_SESSION['message_type']);
        }
        ?>

        <?php if ($student): ?>
            <form action="edit_student.php?id=<?php echo htmlspecialchars($student_id); ?>" method="POST">
                <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($student['name']); ?>"
                        required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>"
                        required>
                </div>
                <div class="form-group">
                    <label for="phone_number">Phone Number:</label>
                    <input type="text" id="phone_number" name="phone_number"
                        value="<?php echo htmlspecialchars($student['phone_number']); ?>">
                </div>
                <div class="flex items-center justify-start">
                    <input type="submit" value="Update Student">
                </div>
            </form>
        <?php else: ?>
            <p class="text-center text-gray-700">Student not found.</p>
        <?php endif; ?>
        <p class="text-center mt-4"><a href="view_students.php">Back to Student List</a></p>
    </div>
</body>

</html>
<?php if ($conn)
    $conn->close(); // Close connection if it wasn't closed by POST handling ?>