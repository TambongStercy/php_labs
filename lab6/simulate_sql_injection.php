<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SQL Injection Simulation</title>
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
    </style>
</head>

<body>
    <div class="container-main">
        <h1>SQL Injection Simulation (Login Bypass)</h1>
        <p>Attempting to log in as 'admin' with a SQL injection payload...</p>

        <?php
        $login_url = 'http://localhost:3000/lab6/login.php'; // Adjusted URL
        $ch = curl_init($login_url);

        // SQL Injection Payload: ' OR '1'='1 -- 
        // This tries to make the WHERE clause always true.
        // The patched login.php uses prepared statements, so this should NOT work.
        $username_or_email_payload = "admin' OR '1'='1' -- ";
        $password_payload = "anypassword"; // Password doesn't matter for this type of bypass if vulnerable
        
        $post_data = [
            'username_or_email' => $username_or_email_payload,
            'password' => $password_payload
        ];

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt'); // Store cookies for session
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt'); // Use cookies
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        ?>

        <h2>Simulation Result:</h2>
        <p>HTTP Status Code: <?php echo $http_code; ?></p>

        <?php
        // Check if redirection occurred (successful login usually redirects to library.php)
        if ($http_code == 302 || $http_code == 303) {
            echo "<p style='color: red;'><strong>Potential SQL Injection vulnerability detected! The login attempt might have bypassed authentication.</strong></p>";
            echo "<p>Check the redirected page for confirmation (e.g., if you are logged in as admin).</p>";
        } else if (strpos($response, 'Login successful') !== false || strpos($response, 'Welcome') !== false) {
            echo "<p style='color: red;'><strong>Potential SQL Injection vulnerability detected! The login attempt might have bypassed authentication.</strong></p>";
            echo "<p>Check the content of the response to see if a user was logged in.</p>";
        } else {
            echo "<p style='color: green;'><strong>SQL Injection attempt FAILED. The application appears secure against this login bypass.</strong></p>";
            echo "<p>This is expected, as prepared statements prevent this type of attack.</p>";
        }

        // Clean up cookie file
        if (file_exists('cookie.txt')) {
            unlink('cookie.txt');
        }
        ?>

        <h3>Login Page Response (partial):</h3>
        <pre><?php echo htmlspecialchars(substr($response, 0, 1000)); ?>...</pre>

        <hr>
        <h2>To run this simulation:</h2>
        <ol>
            <li>Save this code as `simulate_sql_injection.php` in your `lab6` directory.</li>
            <li>Ensure your Apache/Nginx server is running and PHP is configured. (Note: PHP's built-in server may get
                stuck due to single-threaded nature if both app and simulation run on same server and port).</li>
            <li>Open your web browser and navigate to `http://localhost:3000/lab6/simulate_sql_injection.php`.</li>
            <li>Observe the 'Simulation Result' to see if the login bypass was successful or failed.</li>
        </ol>
        <p><strong>Expected Result:</strong> The simulation should report 'SQL Injection attempt FAILED' because the
            `login.php` uses prepared statements.</p>
    </div>
</body>

</html>