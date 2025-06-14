<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>CSRF Edit Simulation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        body { @apply bg-gray-100 font-sans text-gray-800; }
        .container-main { @apply max-w-3xl mx-auto p-6 mt-10 bg-white shadow-lg rounded-lg; }
        h1 { @apply text-3xl font-bold text-blue-700 mb-4; }
        h2 { @apply text-2xl font-semibold text-gray-700 mt-6 mb-3; }
        h3 { @apply text-xl font-medium text-gray-600 mt-4 mb-2; }
        p { @apply mb-2; }
        pre { @apply bg-gray-50 p-4 rounded-md overflow-x-auto text-sm text-gray-700; }
        hr { @apply border-t border-gray-200 my-8; }
        ol li { @apply mb-1; }
        strong { @apply font-bold; }
        .success-msg { @apply text-green-600; }
        .error-msg { @apply text-red-600; }
        .warning-msg { @apply text-orange-600; }
    </style>
</head>

<body>
    <div class="container-main">
        <h1>CSRF Simulation (Edit Book)</h1>
        <p>Attempting to force edit a book via a malicious request...</p>

        <?php
        $login_url = 'http://localhost:3000/lab6/login.php';
        $edit_book_url = 'http://localhost:3000/lab6/edit_book.php';
        $library_url = 'http://localhost:3000/lab6/library.php';

        $cookie_file = 'csrf_edit_cookie.txt';

        // IMPORTANT: Replace with a book_id that actually exists in your database.
        // You can find book IDs by visiting library.php after logging in.
        $target_book_id = 1; // <--- CHANGE THIS TO AN ACTUAL BOOK ID IN YOUR DB
        $malicious_title = "CSRF Edited Title " . uniqid();
        $malicious_author = "CSRF Attacker";

        // --- Step 1: Log in to get a session (simulating a logged-in victim) --- 
        echo "<h2>Step 1: Logging in as victim user...</h2>";
        $ch = curl_init($login_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'username_or_email' => 'testuser', // Use a valid username/email from your DB
            'password' => 'testpassword' // Use a valid password for the above user
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        $login_response = curl_exec($ch);
        $login_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (strpos($login_response, 'Welcome') === false && ($login_http_code != 302 && $login_http_code != 303)) {
            echo "<p class='error-msg'><strong>Login failed. Please ensure 'testuser' and 'testpassword' are valid credentials.</strong></p>";
            echo "<pre>" . htmlspecialchars(substr($login_response, 0, 500)) . "...</pre>";
            unlink($cookie_file);
            exit();
        }
        echo "<p class='success-msg'>Login successful (or redirected).</p>";

        // --- Step 2: Attempt to edit the book without a valid CSRF token --- 
        echo "<h2>Step 2: Sending malicious edit request without CSRF token...</h2>";
        $post_data = [
            'id' => $target_book_id,
            'title' => $malicious_title,
            'author' => $malicious_author,
            'publication_year' => '2000',
            'genre' => 'Malicious',
            'price' => '1',
            // 'csrf_token' => 'INVALID_TOKEN_OR_MISSING' // Malicious request would omit or have wrong token
        ];

        $ch = curl_init($edit_book_url . '?id=' . $target_book_id); // Ensure ID is in URL for edit_book.php
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        $edit_response = curl_exec($ch);
        $edit_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        ?>

        <h2>Simulation Result:</h2>
        <p>HTTP Status Code: <?php echo $edit_http_code; ?></p>

        <?php
        // After patching, the expected behavior is a redirection back to library.php with an error message
        // indicating invalid CSRF token or invalid request, or the edit simply failing.
        if (strpos($edit_response, 'updated successfully!') === false && strpos($edit_response, 'Invalid CSRF token') !== false) {
            echo "<p class='success-msg'><strong>CSRF Edit attempt FAILED. The application appears secure against this CSRF attack.</strong></p>";
            echo "<p>This is expected, as CSRF protection prevented the unauthorized edit.</p>";
        } else if (strpos($edit_response, 'updated successfully!') !== false) {
            echo "<p class='error-msg'><strong>CSRF Vulnerability Detected! The book (ID: <?php echo $target_book_id; ?>) might have been edited.</strong></p>";
            echo "<p>Check your library.php to confirm. Expected title: '<?php echo htmlspecialchars($malicious_title); ?>'</p>";
        } else {
            echo "<p class='warning-msg'>Could not definitively confirm CSRF protection. Manual inspection of library.php after running may be needed.</p>";
        }

        // Clean up cookie file
        if (file_exists($cookie_file)) {
            unlink($cookie_file);
        }
        ?>

        <h3>Edit Response (partial):</h3>
        <pre><?php echo htmlspecialchars(substr($edit_response, 0, 1000)); ?>...</pre>

        <hr>
        <h2>To run this simulation:</h2>
        <ol>
            <li>Save this code as `simulate_csrf_edit.php` in your `lab6` directory.</li>
            <li>Ensure your Apache/Nginx server is running and PHP is configured. (Note: PHP's built-in server may get
                stuck due to single-threaded nature if both app and simulation run on same server and port).</li>
            <li><b>IMPORTANT:</b> Ensure you have a user 'testuser' with password 'testpassword' (or change credentials
                in the script) in your database, AND a book with `book_id` = `<?php echo $target_book_id; ?>` (or adjust
                the `$target_book_id` variable in the script) for the test.</li>
            <li>Open your web browser and navigate to `http://localhost:3000/lab6/simulate_csrf_edit.php`.</li>
            <li>Observe the 'Simulation Result:' to see if the edit was successful or failed due to CSRF protection.
            </li>
            <li>After running, manually check your `library.php` to see if the book was edited. It should NOT be.</li>
        </ol>
        <p><strong>Expected Result:</strong> The simulation should report 'CSRF Edit attempt FAILED' because
            `edit_book.php` now requires a CSRF token.</p>
    </div>
</body>

</html>