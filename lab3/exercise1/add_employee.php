<?php
require_once '../db_connect.php'; // Adjust path if db_connect is elsewhere
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '12345');
define('DB_NAME', 'EmployeeDB');
$conn = connectToDatabase(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Fetch departments for the dropdown
$departments = [];
$sql_dept = "SELECT dept_id, dept_name FROM Department ORDER BY dept_name";
$result_dept = $conn->query($sql_dept);
if ($result_dept->num_rows > 0) {
    while ($row = $result_dept->fetch_assoc()) {
        $departments[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add New Employee (3NF)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        label {
            @apply block text-gray-700 text-sm font-bold mb-2;
        }
        input[type="text"], input[type="number"], select {
            @apply shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50;
        }
        input[type="submit"] {
             @apply bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50;
        }
        .form-group {
            @apply mb-4;
        }
         .container {
             @apply max-w-md mx-auto bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 mt-10;
         }
        .message {
            @apply p-3 mb-4 rounded-md;
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
        <h2 class="text-2xl font-bold mb-6 text-center">Add New Employee</h2>
        <form action="process_employee.php" method="POST">
            <div class="form-group">
                <label for="emp_name">Employee Name:</label>
                <input type="text" id="emp_name" name="emp_name" required>
            </div>

            <div class="form-group">
                <label for="emp_salary">Salary:</label>
                <input type="number" id="emp_salary" name="emp_salary" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="emp_dept_id">Department:</label>
                <select id="emp_dept_id" name="emp_dept_id" required>
                    <option value="">-- Select Department --</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?php echo htmlspecialchars($dept['dept_id']); ?>">
                            <?php echo htmlspecialchars($dept['dept_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex items-center justify-start">
                <input type="submit" value="Add Employee">
            </div>
        </form>
        <p class="text-center mt-4"><a href="view_employees.php">View All Employees</a></p>
    </div>
</body>

</html>
<?php $conn->close(); ?>