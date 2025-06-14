<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>XSS Simulation</title>
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
        <h1>XSS Simulation (Book Title Injection)</h1>
        <p>Attempting to add a book with a malicious title payload...</p>

        <?php
        $login_url = 'http://localhost:3000/lab6/login.php';
        $add_book_url = 'http://localhost:3000/lab6/add_book.php';
        $library_url = 'http://localhost:3000/lab6/library.php';

        $cookie_file = 'xss_cookie.txt';

        // --- Step 1: Log in to get a session --- 
        echo "<h2>Step 1: Logging in...</h2>";
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

        // --- Step 2: Get the CSRF token from the add_book form --- 
        echo "<h2>Step 2: Fetching CSRF token from Add Book page...</h2>";
        $ch = curl_init($add_book_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        $add_book_form_response = curl_exec($ch);
        curl_close($ch);

        $csrf_token = null;
        preg_match('/<input type="hidden" name="csrf_token" value="([a-f0-9]+)">/', $add_book_form_response, $matches);
        if (isset($matches[1])) {
            $csrf_token = $matches[1];
            echo "<p class='success-msg'>CSRF Token found: " . htmlspecialchars($csrf_token) . "</p>";
        } else {
            echo "<p class='error-msg'><strong>CSRF Token not found on add_book.php. Cannot proceed with XSS simulation.</strong></p>";
            echo "<pre>" . htmlspecialchars(substr($add_book_form_response, 0, 1000)) . "...</pre>";
            unlink($cookie_file);
            exit();
        }

        // --- Step 3: Attempt to add a book with XSS payload --- 
        echo "<h2>Step 3: Submitting XSS Payload...</h2>";
        $xss_payload = "<script>alert('XSS Attack!');</script>";
        $book_title = "XSS Test Book " . uniqid();
        $book_author = "Attacker";

        $post_data = [
            'title' => $book_title . $xss_payload,
            'author' => $book_author,
            'publication_year' => '2024',
            'genre' => 'Security',
            'price' => '10000',
            'csrf_token' => $csrf_token
        ];

        $ch = curl_init($add_book_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        $add_response = curl_exec($ch);
        curl_close($ch);

        if (strpos($add_response, 'Book ' . htmlspecialchars($book_title) . ' added successfully!') !== false || strpos($add_response, 'library.php') !== false) {
            echo "<p class='success-msg'>Book addition simulated successfully. Checking for XSS...</p>";

            // --- Step 4: Check if the XSS payload executed on library.php --- 
            echo "<h2>Step 4: Checking Library Page for XSS...</h2>";
            $ch = curl_init($library_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
            $library_page_response = curl_exec($ch);
            curl_close($ch);

            if (strpos($library_page_response, $xss_payload) !== false) {
                echo "<p class='error-msg'><strong>XSS Vulnerability Detected! The payload was found unescaped in the library page.</strong></p>";
            } else if (strpos($library_page_response, htmlspecialchars($xss_payload)) !== false) {
                echo "<p class='success-msg'>XSS attempt FAILED. The payload was properly escaped.</p>";
                echo "<p>This is expected behavior, as `htmlspecialchars()` prevents XSS.</p>";
            } else {
                echo "<p class='warning-msg'>Could not definitively confirm XSS. The payload was not found directly, but also not found escaped. Manual inspection may be needed.</p>";
            }

        } else {
            echo "<p class='error-msg'><strong>Failed to add book. XSS simulation aborted.</strong></p>";
            echo "<pre>" . htmlspecialchars(substr($add_response, 0, 1000)) . "...</pre>";
        }

        // Clean up cookie file
        if (file_exists($cookie_file)) {
            unlink($cookie_file);
        }
        ?>

        <hr>
        <h2>To run this simulation:</h2>
        <ol>
            <li>Save this code as `simulate_xss.php` in your `lab6` directory.</li>
            <li>Ensure your Apache/Nginx server is running and PHP is configured. (Note: PHP's built-in server may get
                stuck due to single-threaded nature if both app and simulation run on same server and port).</li>
            <li><b>IMPORTANT:</b> Ensure you have a user 'testuser' with password 'testpassword' (or change the
                credentials in the script) in your database for the login step.</li>
            <li>Open your web browser and navigate to `http://localhost:3000/lab6/simulate_xss.php`.</li>
            <li>Observe the 'Simulation Result:' to see if the XSS payload executed or was properly escaped.</li>
        </ol>
        <p><strong>Expected Result:</strong> The simulation should report 'XSS attempt FAILED' because `add_book.php`
            and `library.php` use `htmlspecialchars()` to prevent XSS.</p>
    </div>
</body>

</html>