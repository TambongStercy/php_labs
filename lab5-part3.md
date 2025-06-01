Okay, let's move on to **Lab 5, Exercise 3: Restricting Access to Pages Behind Authentication**.

Much of this exercise involves correctly *using* the `auth_check.php` script we created in Exercise 1 and ensuring our page structure reflects public vs. private access.

**Objective:** Ensure that only authenticated users can access pages like the library catalog, book borrowing (which will be part of the CRUD in Ex 4), and profile. All pages should be protected by the authentication system.

**Steps & Implementation:**

**1. Authentication Check (File: `lab5/auth_check.php`):**
We already created this file in Exercise 1. Let's re-iterate its content and importance.

```php
<?php
// lab5/auth_check.php
if (session_status() === PHP_SESSION_NONE) { // Start session if not already started
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, store the intended page for redirection after login
    // This helps in redirecting the user back to the page they were trying to access.
    if (!empty($_SERVER['REQUEST_URI'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    }

    $_SESSION['message'] = "You must be logged in to access this page.";
    $_SESSION['message_type'] = "error";
    header("Location: login.php"); // Redirect to the login page
    exit(); // Important to stop further script execution
}

// Optional: You can add activity-based session extension here if desired.
// Example: Update session cookie lifetime to keep session active if user is active.
// $new_lifetime = 1800; // 30 minutes
// if (isset($_COOKIE[session_name()])) {
//     setcookie(session_name(), $_COOKIE[session_name()], time() + $new_lifetime, "/");
// }
?>
```
**Key points about `auth_check.php`:**
*   **`session_start()`:** Crucial. It must be called before any access to `$_SESSION`. `session_status()` check prevents multiple calls.
*   **`!isset($_SESSION['user_id'])`:** This is the core check. If the `user_id` isn't set in the session, the user is not considered logged in.
*   **`$_SESSION['redirect_url']`:** Storing the `REQUEST_URI` allows you to redirect the user back to the page they were trying to access *after* they successfully log in. You'd implement this logic in `login.php`.
*   **`header("Location: login.php");`**: Redirects unauthenticated users.
*   **`exit();`**: Essential after a header redirect to prevent the rest of the script on the protected page from executing.

**2. Home Page (File: `lab5/home.php`):**
This page is **publicly accessible**. It doesn't need `auth_check.php`. We already created a version of this. Its primary role here is to provide navigation.

```php
<?php
// lab5/home.php
session_start(); // Still need session for displaying messages or user-specific nav links
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome - Library System</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Library System</h1>
            <nav>
                <a href="home.php">Home</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="library.php">My Library</a>
                    <a href="profile.php">Profile (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </nav>
        </header>
        <main>
            <h2>Welcome to Our Library!</h2>
            <p>Browse our collection, manage your account, and enjoy reading.</p>
            <?php
            // Display any session messages (e.g., from logout, login failure attempts before reaching home)
            if (isset($_SESSION['message'])) {
                echo "<div class='message " . htmlspecialchars($_SESSION['message_type']) . "'><p>" . htmlspecialchars($_SESSION['message']) . "</p></div>";
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            }
            ?>
            <p>
                <?php if (isset($_SESSION['user_id'])): ?>
                    You are logged in. Go to <a href="library.php">your library dashboard</a>.
                <?php else: ?>
                    Please <a href="login.php">login</a> or <a href="register.php">register</a> to access members-only features.
                <?php endif; ?>
            </p>
        </main>
        <footer>
            <p>&copy; <?php echo date("Y"); ?> Library System</p>
        </footer>
    </div>
</body>
</html>
```

**3. Library Page (File: `lab5/library.php`):**
This page **must be protected**. It's where users will see books and (in Ex 4) interact with them.
**Crucial Change:** Add `require_once 'auth_check.php';` at the very top.

```php
<?php
// lab5/library.php
require_once 'auth_check.php'; // <<<--- THIS IS THE PROTECTION
require_once 'db_connect.php';
$conn = connectToDatabase(DB_SERVER_L5, DB_USERNAME_L5, DB_PASSWORD_L5, DB_NAME_L5);

// Fetch books for display (this will be expanded in Exercise 4 for CRUD)
$books = [];
// For now, let's assume all books are viewable by logged-in users.
// Later, you could filter by books added by the user, etc.
$result = $conn->query("SELECT book_id, title, author, publication_year, genre, price FROM Books ORDER BY title");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Catalog - Library System</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Library Catalog</h1>
            <nav>
                <a href="home.php">Home</a>
                <a href="library.php">My Library</a>
                <a href="profile.php">Profile (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
                <a href="add_book.php">Add New Book</a> <!-- Link to add_book.php -->
                <a href="logout.php">Logout</a>
            </nav>
        </header>
        <main>
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
            <?php
            // Display any session messages
            if (isset($_SESSION['message'])) {
                echo "<div class='message " . htmlspecialchars($_SESSION['message_type']) . "'><p>" . htmlspecialchars($_SESSION['message']) . "</p></div>";
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            }
            ?>
            <h3>Our Collection</h3>
            <?php if (!empty($books)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Year</th>
                            <th>Genre</th>
                            <th>Price</th>
                            <th>Actions</th> <!-- For Edit/Delete in Ex 4 -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($book['title']); ?></td>
                                <td><?php echo htmlspecialchars($book['author']); ?></td>
                                <td><?php echo htmlspecialchars($book['publication_year']); ?></td>
                                <td><?php echo htmlspecialchars($book['genre']); ?></td>
                                <td>$<?php echo htmlspecialchars(number_format($book['price'], 2)); ?></td>
                                <td>
                                    <!-- Links for CRUD operations - to be fully implemented in Ex 4 -->
                                    <a href="edit_book.php?id=<?php echo $book['book_id']; ?>">Edit</a>
                                    <a href="delete_book.php?id=<?php echo $book['book_id']; ?>" onclick="return confirm('Are you sure? This action cannot be undone.');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No books currently in the library. Why not <a href="add_book.php">add one</a>?</p>
            <?php endif; ?>
        </main>
        <footer>
            <p>&copy; <?php echo date("Y"); ?> Library System</p>
        </footer>
    </div>
</body>
</html>
```

**4. Profile Page (File: `lab5/profile.php`):**
This page **must be protected**.
**Crucial Change:** Add `require_once 'auth_check.php';` at the very top.
(We already created a version of this in Exercise 2 that included `auth_check.php`, so it should be fine).

**5. Other Protected Pages (to be created/fleshed out in Exercise 4):**
*   `lab5/add_book.php`
*   `lab5/edit_book.php`
*   `lab5/delete_book.php` (This will be a script, not a viewable page, but the action should be protected)
*   Any script that processes forms related to these actions (e.g., `process_add_book.php`, `process_edit_book.php`)

**All these files will start with:**
```php
<?php
require_once 'auth_check.php';
// ... rest of the page logic ...
?>
```

**6. Access Control (File: `lab5/access_control.php` - Conceptual):**
The instruction mentions an `access_control.php` file. In this simple setup, the "access control" is primarily handled by including `auth_check.php` at the beginning of each protected script.

If you wanted a more centralized way *other than* `auth_check.php` itself, you could define an array of protected pages and check against it, but `auth_check.php` serves this purpose directly for individual file protection.

For more complex role-based access control (e.g., admin vs. user), you would expand the logic within `auth_check.php` or a dedicated access control script to check `$_SESSION['user_role']` (which you'd set upon login) against the required role for the current page/action. This lab doesn't explicitly require role-based access, just basic authentication.

The pages `home.php`, `login.php`, and `register.php` (and the Google OAuth flow pages) are explicitly *not* protected by `auth_check.php` because users need to access them to log in or register.

**7. Test the Application:**

*   **Clear Browser Cache/Session or Use Incognito:** This is important to ensure you're testing fresh login states.
*   **Attempt to access protected pages directly (when not logged in):**
    *   Try going to `http://localhost/lab5/library.php`. You should be redirected to `login.php` with the message "You must be logged in...".
    *   Try going to `http://localhost/lab5/profile.php`. Same redirection.
    *   Try going to `http://localhost/lab5/add_book.php` (even if it's empty for now). Same redirection.
*   **Log in:**
    *   Go to `login.php` and log in (either with username/password or Google OAuth if you've set it up).
*   **Access protected pages (when logged in):**
    *   After login, you should be on `library.php` (or `home.php` then navigate to `library.php`).
    *   Navigate to `profile.php`.
    *   Navigate to `add_book.php`. You should be able to see these pages.
*   **Log out:**
    *   Click the "Logout" link.
*   **Attempt to access protected pages again (after logout):**
    *   You should be redirected to `login.php`.
*   **Test the `redirect_url` functionality:**
    *   Log out.
    *   Try to access `http://localhost/lab5/profile.php`. You'll be redirected to `login.php`.
    *   Now, log in. After successful login, you should be redirected to `profile.php` (the page you originally tried to access), not just `library.php` or `home.php` every time. This requires a slight modification in `login.php`:

    **Modify `lab5/login.php` for `redirect_url`:**
    Inside the `if (password_verify(...))` block or after successful Google OAuth login, before the `header("Location: library.php");` line, add:

    ```php
    // ... (inside successful login block)
    if (isset($_SESSION['redirect_url'])) {
        $redirect_to = $_SESSION['redirect_url'];
        unset($_SESSION['redirect_url']); // Clear it after use
        header("Location: " . $redirect_to);
    } else {
        header("Location: library.php"); // Default redirect
    }
    exit();
    ```
    Do this for both traditional login and Google OAuth callback success paths in `google_auth_callback.php`.

    For `google_auth_callback.php`, before `header('Location: ../library.php');`:
    ```php
    // ... (inside successful google login/registration block)
    $redirect_target = '../library.php'; // Default
    if (isset($_SESSION['redirect_url'])) {
        // Ensure redirect_url is a local path, not an external URL for security.
        // A simple check for starting with '/' or being a known local page.
        // For this lab, we'll assume it's always a local path from our app.
        $redirect_target = $_SESSION['redirect_url'];
        unset($_SESSION['redirect_url']);
    }
    header('Location: ' . $redirect_target);
    exit();
    ```

**Questions for Exercise 3:**

1.  **What are the benefits of using session-based authentication in web applications?**
    *   **Statefulness:** It allows the web application to "remember" a user across multiple HTTP requests, which are inherently stateless. This is fundamental for knowing if a user is logged in.
    *   **Personalization:** User-specific data and preferences can be stored in the session and used to tailor the application's content and behavior for that user.
    *   **Access Control:** It provides a mechanism to restrict access to certain pages or features based on whether a user has successfully authenticated.
    *   **Improved User Experience:** Users don't have to re-enter their credentials on every page.
    *   **Data Storage:** Sessions can store temporary data relevant to the user's current interaction (e.g., shopping cart items, form progress) without needing to pass it through URLs or hidden form fields constantly.
    *   **Security (when implemented correctly):** Server-side session data is generally more secure than client-side storage for sensitive information, as the client only holds a session ID.

2.  **How can you prevent unauthorized access to pages and protect sensitive data in a PHP web application?**
    *   **Authentication Check:** Implement a robust authentication check (like `auth_check.php`) at the beginning of every script/page that requires user login. This script should verify session validity and redirect if the user is not authenticated.
    *   **Role-Based Access Control (RBAC):** For applications with different user roles (e.g., admin, editor, user), store the user's role in the session after login. On each protected page or action, check if the logged-in user's role has the necessary permissions.
    *   **Input Validation and Sanitization:**
        *   **Validate all user inputs** on the server-side (type, format, length, range) to prevent invalid data from being processed or stored.
        *   **Sanitize output** (e.g., using `htmlspecialchars()`) when displaying user-generated content to prevent Cross-Site Scripting (XSS) attacks.
    *   **Prevent SQL Injection:** Use prepared statements (parameterized queries) for all database interactions to prevent malicious SQL code from being executed.
    *   **Prevent Cross-Site Scripting (XSS):** Encode/escape user-supplied data before rendering it in HTML. Content Security Policy (CSP) headers can also add a strong layer of defense.
    *   **Prevent Cross-Site Request Forgery (CSRF):** Use CSRF tokens for any state-changing requests (e.g., forms that submit data, delete actions). These tokens ensure the request originated from your application.
    *   **HTTPS:** Enforce HTTPS for the entire application to encrypt all data in transit, protecting session IDs, login credentials, and sensitive data from eavesdropping.
    *   **Secure Session Management:**
        *   Use strong, unpredictable session IDs.
        *   Regenerate session ID upon login (`session_regenerate_id(true)`).
        *   Use `HttpOnly` and `Secure` flags for session cookies.
        *   Implement session timeouts.
    *   **Error Handling:** Configure error reporting to log errors to a file (not display them to users in production) to avoid leaking sensitive system information.
    *   **Directory Traversal Protection:** Ensure user inputs are not used directly in file paths without proper validation and sanitization.
    *   **File Upload Security:** If handling file uploads, validate file types, sizes, and scan for malware. Store uploaded files outside the webroot if possible, or with restricted permissions.
    *   **Principle of Least Privilege:** The web server user and database user should have only the minimum necessary permissions.

3.  **How can you handle session expiration and ensure that users are logged out after a certain period of inactivity?**
    PHP sessions have a built-in mechanism for garbage collection based on `session.gc_maxlifetime` (defined in `php.ini` or via `ini_set()`). However, this is for cleaning up old session *files* on the server and doesn't actively log out a user if their browser still has a valid session cookie.
    To actively manage inactivity timeouts:
    *   **Timestamping Last Activity:**
        *   When a user performs an action on a protected page (or at the start of `auth_check.php`), update a timestamp in their session:
            ```php
            // In auth_check.php or similar
            $_SESSION['last_activity'] = time();
            ```
    *   **Checking Inactivity on Subsequent Requests:**
        *   At the beginning of `auth_check.php` (or any protected page request), compare the current time with `$_SESSION['last_activity']`:
            ```php
            // In auth_check.php
            $inactivity_timeout = 1800; // 30 minutes in seconds

            if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactivity_timeout)) {
                // Session has expired due to inactivity
                session_unset();     // Unset $_SESSION variable for the run-time
                session_destroy();   // Destroy session data in storage
                session_start();     // Start a new session for the message
                $_SESSION['message'] = "Your session has expired due to inactivity. Please log in again.";
                $_SESSION['message_type'] = "error";
                header("Location: login.php");
                exit();
            }
            $_SESSION['last_activity'] = time(); // Update last activity time for current request
            ```
    *   **Client-Side Warnings (Optional JavaScript):** You can use JavaScript to detect inactivity on the client-side and show a warning message before the server-side timeout occurs, giving the user a chance to "stay logged in." If they don't respond, you can then redirect them to a logout page or let the server-side timeout handle it.
    *   **Cookie Lifetime vs. Session Lifetime:**
        *   The session cookie lifetime (set by `session.cookie_lifetime` in `php.ini` or `session_set_cookie_params()`) determines how long the browser keeps the session ID. If it's 0, the cookie expires when the browser closes.
        *   The server-side session data lifetime is controlled by `session.gc_maxlifetime`.
        *   The inactivity timeout logic described above is independent of these but works in conjunction. Even if the cookie is still valid, if the server-side `last_activity` check fails, the user is logged out.

This completes Exercise 3. Next, we'll build the CRUD functionality for book management in Exercise 4.