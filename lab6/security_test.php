<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Security Test Hub - Library System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        body { @apply bg-gray-100 font-sans; }
        .container-main { @apply max-w-4xl mx-auto p-8 mt-10 bg-white shadow-lg rounded-lg; }
        h1 { @apply text-3xl font-bold text-gray-800 mb-6; }
        h2 { @apply text-2xl font-semibold text-gray-700 mt-8 mb-4; }
        .test-section { @apply bg-gray-50 p-6 rounded-md shadow-sm mb-6; }
        .test-section p { @apply text-gray-700 mb-4; }
        .test-section a, .test-section button {
            @apply inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200 ease-in-out;
        }
        .warning-text { @apply text-red-600 font-semibold; }
        .info-text { @apply text-blue-600 font-semibold; }
        .back-link { @apply block mt-8 text-center text-blue-500 hover:underline; }
    </style>
</head>

<body>
    <div class="container-main">
        <h1>Security Test Hub</h1>
        <p class="text-gray-600">This page allows you to run various security simulations against your Library System
            application to verify the implemented protections.</p>
        <p class="info-text"><strong>Important:</strong> Before running CSRF or XSS tests, ensure you have a user
            'testuser' with password 'testpassword' (or change the credentials in the simulation scripts) in your
            database, and at least one book with `book_id` = `1` (or adjust `\$target_book_id` in scripts). Run
            `db_setup.php` if needed.</p>

        <div class="test-section">
            <h2>SQL Injection Simulation (Login Bypass)</h2>
            <p>This test attempts to bypass the login page's authentication using a SQL Injection payload. If
                successful, it indicates a vulnerability.</p>
            <p class="warning-text">Expected Result: The test should FAIL, confirming the login page is secure.</p>
            <a href="simulate_sql_injection.php" target="_blank">Run SQL Injection Test</a>
        </div>

        <div class="test-section">
            <h2>Cross-Site Scripting (XSS) Simulation</h2>
            <p>This test attempts to inject a malicious script into a book's title when adding a new book, then checks
                if the script executes when viewing the library. If successful, it indicates an XSS vulnerability.</p>
            <p class="warning-text">Expected Result: The test should FAIL, confirming XSS protection (content should be
                escaped).</p>
            <a href="simulate_xss.php" target="_blank">Run XSS Test</a>
        </div>

        <div class="test-section">
            <h2>CSRF Simulation (Delete Book)</h2>
            <p>This test attempts to force a logged-in user to delete a book without their consent, by sending a
                malicious request without a valid CSRF token. If successful, it indicates a CSRF vulnerability.</p>
            <p class="warning-text">Expected Result: The test should FAIL, confirming CSRF protection. (The book should
                NOT be deleted).</p>
            <a href="simulate_csrf_delete.php" target="_blank">Run CSRF Delete Test</a>
        </div>

        <div class="test-section">
            <h2>CSRF Simulation (Edit Book)</h2>
            <p>This test attempts to force a logged-in user to edit a book's details without their consent, by sending a
                malicious request without a valid CSRF token. If successful, it indicates a CSRF vulnerability.</p>
            <p class="warning-text">Expected Result: The test should FAIL, confirming CSRF protection. (The book should
                NOT be edited).</p>
            <a href="simulate_csrf_edit.php" target="_blank">Run CSRF Edit Test</a>
        </div>

        <a href="home.php" class="back-link">&larr; Back to Home</a>
    </div>
</body>

</html>