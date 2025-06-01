<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add New Student</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        label {
            @apply block text-gray-700 text-sm font-bold mb-2;
        }
        input[type="text"], input[type="email"] {
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
        a {
            @apply text-blue-500 hover:text-blue-700;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container">
        <h2 class="text-2xl font-bold mb-6 text-center">Add New Student</h2>
        <form action="insert_student.php" method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="text" id="phone_number" name="phone_number">
            </div>

            <div class="flex items-center justify-start">
                <input type="submit" value="Add Student">
            </div>
        </form>
        <p class="text-center mt-4"><a href="view_students.php">View All Students</a></p>
    </div>
</body>

</html>