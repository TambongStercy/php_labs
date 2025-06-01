<?php
session_start();
require_once '../db_connect.php'; // Adjust path
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '12345');
define('DB_NAME', 'StudentDB');
$conn = connectToDatabase(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

$sql = "SELECT student_id, name, email, phone_number FROM Students ORDER BY name";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Students</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        table {
            @apply w-full lg:w-4/5 mx-auto shadow-md rounded my-6;
        }
        th {
            @apply bg-gray-200 text-left px-3 py-2;
        }
        td {
            @apply border px-3 py-2;
        }
        .actions a {
            @apply mr-2;
        }
        .actions a.edit {
            @apply text-green-500 hover:text-green-700;
        }
        .actions a.delete {
            @apply text-red-500 hover:text-red-700;
        }
        .container {
             @apply max-w-4xl mx-auto bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 mt-10;
        }
        .message {
            @apply p-3 mb-4 rounded-md text-center;
        }
        .success {
            @apply bg-green-100 text-green-700 border border-green-300;
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
        <h2 class="text-2xl font-bold mb-6 text-center">Student List</h2>

        <?php
        if (isset($_SESSION['message'])) {
            echo "<div class='message " . htmlspecialchars($_SESSION['message_type']) . "'>" . htmlspecialchars($_SESSION['message']) . "</div>";
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>

        <p class="text-center mb-4"><a href="add_student.php"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Add
                New Student</a></p>

        <?php if ($result->num_rows > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                                <td class="actions">
                                    <a href="edit_student.php?id=<?php echo $row['student_id']; ?>" class="edit">Edit</a>
                                    <a href="delete_student.php?id=<?php echo $row['student_id']; ?>" class="delete"
                                        onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-700">No students found.</p>
        <?php endif; ?>
    </div>
</body>

</html>
<?php $conn->close(); ?>