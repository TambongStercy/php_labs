<?php
// view_users.php
$conn = new mysqli('localhost', 'root', '12345', 'WebAppDB');
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

$result = $conn->query("SELECT * FROM Users");
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>View Users</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        th {
            @apply px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider;
        }
        td {
            @apply px-6 py-4 whitespace-nowrap text-sm text-gray-900;
        }
        tr:nth-child(even) {
             @apply bg-gray-50;
         }
        .add-link {
            @apply inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded;
        }
        .container {
            @apply max-w-4xl mx-auto mt-10;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Registered Users</h1>
            <a href="user_form.php" class="add-link">Add New User</a>
        </div>
        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Age</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['age']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
<?php
$conn->close();
?>