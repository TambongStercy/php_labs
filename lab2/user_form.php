<!-- user_form.php -->
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>New User</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        label {
            @apply block text-gray-700 text-sm font-bold mb-2;
        }
        input[type="text"], input[type="email"], input[type="number"] {
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
    </style>
</head>

<body class="bg-gray-100">
    <div class="container">
        <h1 class="text-2xl font-bold mb-6 text-center">Add a New User</h1>
        <form method="POST" action="process_form.php">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" name="age" id="age" min="0" required>
            </div>
            <div class="flex items-center justify-start">
                <input type="submit" value="Submit">
            </div>
        </form>
    </div>
</body>

</html>