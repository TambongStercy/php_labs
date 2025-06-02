# PHP Lab Exercises - Comprehensive Guide

This repository contains 5 comprehensive PHP lab exercises covering fundamental to advanced web development concepts including database operations, object-oriented programming, authentication, and OAuth integration.

## Table of Contents

- [Prerequisites](#prerequisites)
- [PHP Configuration](#php-configuration)
- [SSL Certificate Setup](#ssl-certificate-setup)
- [Database Setup](#database-setup)
- [Lab Exercises Overview](#lab-exercises-overview)
- [Running and Testing Each Lab](#running-and-testing-each-lab)
- [Troubleshooting](#troubleshooting)
- [Common Issues and Solutions](#common-issues-and-solutions)

## Prerequisites

### Required Software
- **PHP 8.0+** (recommended PHP 8.1 or higher)
- **MySQL 8.0+** or **MariaDB 10.4+**
- **Apache** or **Nginx** web server
- **Composer** (for dependency management in Lab 5)
- **Web browser** (Chrome, Firefox, Safari, Edge)

### Development Environment Options
1. **XAMPP** (Windows/Mac/Linux) - Recommended for beginners
2. **WAMP** (Windows)
3. **MAMP** (Mac)
4. **Docker** with PHP/MySQL containers
5. **Local development server** using `php -S localhost:8000`

## PHP Configuration

### Required PHP Extensions

Edit your `php.ini` file and ensure the following extensions are enabled (uncomment by removing the `;` at the beginning):

```ini
; Core extensions for database connectivity
extension=mysqli
extension=pdo
extension=pdo_mysql

; For secure connections and OAuth
extension=openssl
extension=curl

; For session management
extension=session

; For JSON operations (Lab 5)
extension=json

; For file operations
extension=fileinfo

; For internationalization (recommended)
extension=intl

; For image processing (if needed)
extension=gd

; For XML processing (Google OAuth)
extension=xml
extension=dom
extension=xmlreader
extension=xmlwriter

; For multibyte string handling
extension=mbstring

; For compression (Composer dependencies)
extension=zip
```

### Important PHP Settings

In your `php.ini` file, configure these settings:

```ini
; Error reporting for development
display_errors = On
display_startup_errors = On
error_reporting = E_ALL

; Session settings
session.auto_start = 0
session.use_cookies = 1
session.use_only_cookies = 1
session.cookie_httponly = 1
session.cookie_secure = 0  ; Set to 1 if using HTTPS

; File upload settings (for future exercises)
file_uploads = On
upload_max_filesize = 10M
post_max_size = 10M

; Memory and execution limits
memory_limit = 256M
max_execution_time = 300

; Date and timezone
date.timezone = "UTC"  ; Change to your timezone
```

## SSL Certificate Setup

For Lab 5 (OAuth integration), you may need HTTPS. Here's how to set up SSL certificates:

### Option 1: Download cacert.pem (Recommended for Development)

1. **Download the certificate bundle:**
   ```bash
   curl -o cacert.pem https://curl.se/ca/cacert.pem
   ```
   Or download manually from: https://curl.se/ca/cacert.pem

2. **Place the file in a secure location:**
   - Windows: `C:\php\extras\ssl\cacert.pem`
   - Linux/Mac: `/usr/local/etc/ssl/cacert.pem`

3. **Update php.ini:**
   ```ini
   ; SSL Certificate settings
   curl.cainfo = "C:\php\extras\ssl\cacert.pem"  ; Windows
   ; curl.cainfo = "/usr/local/etc/ssl/cacert.pem"  ; Linux/Mac
   
   openssl.cafile = "C:\php\extras\ssl\cacert.pem"  ; Windows
   ; openssl.cafile = "/usr/local/etc/ssl/cacert.pem"  ; Linux/Mac
   ```

### Option 2: Self-Signed Certificate for Local Development

```bash
# Generate private key
openssl genrsa -out localhost.key 2048

# Generate certificate
openssl req -new -x509 -key localhost.key -out localhost.crt -days 365 -subj "/CN=localhost"

# Configure your web server to use these certificates
```

## Database Setup

### MySQL Configuration

1. **Start MySQL service**
2. **Create a root user or dedicated user:**
   ```sql
   -- If using root (update password in lab files)
   ALTER USER 'root'@'localhost' IDENTIFIED BY '12345';
   
   -- Or create a dedicated user
   CREATE USER 'phplab'@'localhost' IDENTIFIED BY 'phplab123';
   GRANT ALL PRIVILEGES ON *.* TO 'phplab'@'localhost';
   FLUSH PRIVILEGES;
   ```

3. **Update database credentials in each lab's configuration files:**
   - `lab1/db_config.php`
   - `lab3/db_connect.php`
   - `lab4/db_connect.php`
   - `lab5/db_connect.php`

## Lab Exercises Overview

| Lab | Topic | Key Concepts | Database |
|-----|-------|--------------|----------|
| Lab 1 | Basic PHP & MySQL | CRUD operations, Forms, Basic SQL | `librarydb` |
| Lab 2 | Database Relationships | JOINs, Foreign Keys, Normalized design | `LibrarySystemDB` |
| Lab 3 | Advanced Database Operations | Prepared statements, Error handling, Sessions | `EmployeeDB`, `StudentDB` |
| Lab 4 | Object-Oriented Programming | Classes, Inheritance, Polymorphism, Interfaces | `LibraryDB_OOP` |
| Lab 5 | Authentication & OAuth | Sessions, Login/Register, Google OAuth | `LibraryDB_L5` |
| Lab 6 | Web Application Security | SQL Injection, XSS, CSRF Prevention | `LibraryDB_L5` |

## Running and Testing Each Lab

### Lab 1: Basic PHP and MySQL CRUD Operations

**Objective:** Learn basic PHP syntax, MySQL connectivity, and CRUD operations.

**Setup:**
1. **Configure database connection:**
   ```bash
   # Edit lab1/db_config.php
   # Update $password = "your_mysql_password";
   ```

2. **Create database and table:**
   ```sql
   CREATE DATABASE librarydb;
   USE librarydb;
   
   CREATE TABLE Books (
       book_id INT AUTO_INCREMENT PRIMARY KEY,
       title VARCHAR(255) NOT NULL,
       author VARCHAR(255) NOT NULL,
       publication_year INT,
       genre VARCHAR(100),
       price DECIMAL(10,2)
   );
   
   -- Insert sample data
   INSERT INTO Books (title, author, publication_year, genre, price) VALUES
   ('The Great Gatsby', 'F. Scott Fitzgerald', 1925, 'Fiction', 12.99),
   ('To Kill a Mockingbird', 'Harper Lee', 1960, 'Fiction', 14.99),
   ('1984', 'George Orwell', 1949, 'Dystopian', 13.99);
   ```

**Testing:**
1. **Test basic PHP:** `http://localhost/lab1/hello.php`
2. **Test PHP info:** `http://localhost/lab1/info.php`
3. **View books:** `http://localhost/lab1/read_books.php`
4. **Add new book:** `http://localhost/lab1/create_book.php`
5. **Update book:** Click "Update" link from book list
6. **Delete book:** Click "Delete" link from book list

**Expected Results:**
- ✅ Hello World message displays
- ✅ PHP configuration information shows
- ✅ Book list displays with sample data
- ✅ Can add, edit, and delete books
- ✅ Form validation works properly

---

### Lab 2: Database Relationships and JOINs

**Objective:** Learn database normalization, relationships, and JOIN operations.

**Setup:**
1. **Create database:**
   ```sql
   CREATE DATABASE LibrarySystemDB;
   USE LibrarySystemDB;
   
   CREATE TABLE Authors (
       author_id INT AUTO_INCREMENT PRIMARY KEY,
       author_name VARCHAR(255) NOT NULL
   );
   
   CREATE TABLE Books (
       book_id INT AUTO_INCREMENT PRIMARY KEY,
       book_title VARCHAR(255) NOT NULL,
       author_id INT,
       genre VARCHAR(100),
       price DECIMAL(10,2),
       FOREIGN KEY (author_id) REFERENCES Authors(author_id)
   );
   
   -- Insert sample data
   INSERT INTO Authors (author_name) VALUES
   ('J.K. Rowling'),
   ('Stephen King'),
   ('Agatha Christie');
   
   INSERT INTO Books (book_title, author_id, genre, price) VALUES
   ('Harry Potter and the Philosopher\'s Stone', 1, 'Fantasy', 15.99),
   ('The Shining', 2, 'Horror', 12.99),
   ('Murder on the Orient Express', 3, 'Mystery', 11.99);
   ```

**Testing:**
1. **View books with authors:** `http://localhost/lab2/view_books.php`
2. **Add new book:** `http://localhost/lab2/add_book.php`
3. **Process book addition:** Form submission should redirect to `process_book.php`

**Expected Results:**
- ✅ Books display with author names (not IDs)
- ✅ Author dropdown populates from database
- ✅ New books can be added with proper author association
- ✅ JOIN query works correctly

---

### Lab 3: Advanced Database Operations

**Objective:** Learn prepared statements, error handling, and session management.

**Setup:**

**Exercise 1 - Employee Management:**
```sql
CREATE DATABASE EmployeeDB;
USE EmployeeDB;

CREATE TABLE Department (
    dept_id INT AUTO_INCREMENT PRIMARY KEY,
    dept_name VARCHAR(100) NOT NULL,
    dept_location VARCHAR(100)
);

CREATE TABLE Employee (
    emp_id INT AUTO_INCREMENT PRIMARY KEY,
    emp_name VARCHAR(255) NOT NULL,
    emp_salary DECIMAL(10,2),
    emp_dept_id INT,
    FOREIGN KEY (emp_dept_id) REFERENCES Department(dept_id)
);

-- Insert sample departments
INSERT INTO Department (dept_name, dept_location) VALUES
('IT', 'Building A'),
('HR', 'Building B'),
('Finance', 'Building C');
```

**Exercise 2 - Student Management:**
```sql
CREATE DATABASE StudentDB;
USE StudentDB;

CREATE TABLE Students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone_number VARCHAR(20)
);

-- Insert sample students
INSERT INTO Students (name, email, phone_number) VALUES
('John Doe', 'john.doe@email.com', '+1234567890'),
('Jane Smith', 'jane.smith@email.com', '+1234567891');
```

**Testing:**

**Exercise 1:**
1. **Add employee:** `http://localhost/lab3/exercise1/add_employee.php`
2. **View employees:** `http://localhost/lab3/exercise1/view_employees.php`
3. **Process employee:** Form submissions go through `process_employee.php`

**Exercise 2:**
1. **View students:** `http://localhost/lab3/exercise2/view_students.php`
2. **Add student:** `http://localhost/lab3/exercise2/add_student.php`
3. **Edit student:** Click edit links from student list
4. **Delete student:** Click delete links (with confirmation)

**Expected Results:**
- ✅ Prepared statements prevent SQL injection
- ✅ Session messages display success/error feedback
- ✅ Form validation works on both client and server side
- ✅ Employee-Department relationships display correctly
- ✅ Student CRUD operations work seamlessly

---

### Lab 4: Object-Oriented Programming

**Objective:** Learn OOP concepts including classes, inheritance, polymorphism, and interfaces.

**Setup:**
1. **Create database:**
   ```sql
   CREATE DATABASE LibraryDB_OOP;
   USE LibraryDB_OOP;
   
   CREATE TABLE Books (
       book_id INT AUTO_INCREMENT PRIMARY KEY,
       title VARCHAR(255) NOT NULL,
       author VARCHAR(255) NOT NULL,
       publication_year INT,
       genre VARCHAR(100),
       price DECIMAL(10,2),
       is_ebook BOOLEAN DEFAULT FALSE,
       file_size_mb DECIMAL(5,2) NULL,
       download_url VARCHAR(500) NULL,
       copies_available INT DEFAULT 1,
       total_copies INT DEFAULT 1
   );
   
   CREATE TABLE Members (
       member_id INT AUTO_INCREMENT PRIMARY KEY,
       name VARCHAR(255) NOT NULL,
       email VARCHAR(255) UNIQUE NOT NULL,
       membership_date DATE DEFAULT CURRENT_DATE
   );
   
   CREATE TABLE Loans (
       loan_id INT AUTO_INCREMENT PRIMARY KEY,
       book_id INT,
       member_id INT,
       loan_date DATE DEFAULT CURRENT_DATE,
       return_date DATE NULL,
       FOREIGN KEY (book_id) REFERENCES Books(book_id),
       FOREIGN KEY (member_id) REFERENCES Members(member_id)
   );
   
   -- Insert sample data
   INSERT INTO Books (title, author, publication_year, genre, price, is_ebook, file_size_mb, download_url, copies_available, total_copies) VALUES
   ('PHP Basics', 'John Developer', 2023, 'Programming', 29.99, FALSE, NULL, NULL, 3, 3),
   ('Advanced PHP', 'Jane Coder', 2023, 'Programming', 39.99, TRUE, 15.5, 'https://example.com/advanced-php.pdf', 1, 1),
   ('Web Development Guide', 'Bob Builder', 2022, 'Programming', 34.99, FALSE, NULL, NULL, 2, 2);
   
   INSERT INTO Members (name, email) VALUES
   ('Alice Johnson', 'alice@email.com'),
   ('Bob Wilson', 'bob@email.com'),
   ('Carol Davis', 'carol@email.com');
   ```

**Testing:**

**Exercise 1 - Basic Classes:**
1. **Test Product class:** `http://localhost/lab4/exercise1/test_product.php`

**Exercise 2 - Inheritance:**
1. **Test inheritance:** `http://localhost/lab4/exercise2/test_inheritance.php`

**Exercise 3 - Polymorphism:**
1. **Test polymorphism:** `http://localhost/lab4/exercise3/test_polymorphism.php`

**Exercise 4 - Complete Library System:**
1. **Setup database:** Run `http://localhost/lab4/exercise4/db_setup_library.php`
2. **Test library system:** `http://localhost/lab4/exercise4/library_test.php`

**Expected Results:**
- ✅ Product and Book classes demonstrate inheritance
- ✅ Polymorphism works with Discountable interface
- ✅ Library system allows borrowing/returning books
- ✅ Ebooks and physical books behave differently
- ✅ Member management works correctly
- ✅ Database integration with OOP classes functions properly

---

### Lab 5: Authentication and OAuth Integration

**Objective:** Learn session management, user authentication, and Google OAuth integration.

**Setup:**

1. **Install Composer dependencies:**
   ```bash
   cd lab5
   composer install
   ```

2. **Create database:**
   ```bash
   # Run the database setup script
   http://localhost/lab5/db_setup.php
   ```

3. **Configure Google OAuth (Optional):**
   ```bash
   # Create .env file in lab5 directory
   cp .env.example .env
   
   # Edit .env with your Google OAuth credentials
   GOOGLE_CLIENT_ID=your_google_client_id
   GOOGLE_CLIENT_SECRET=your_google_client_secret
   GOOGLE_REDIRECT_URI=http://localhost/lab5/google_oauth/google_auth_callback.php
   ```

4. **Get Google OAuth Credentials:**
   - Go to [Google Cloud Console](https://console.cloud.google.com/)
   - Create a new project or select existing
   - Enable Google+ API
   - Create OAuth 2.0 credentials
   - Add authorized redirect URI: `http://localhost/lab5/google_oauth/google_auth_callback.php`

**Testing:**

**Exercise 1 - Basic Authentication:**
1. **Home page:** `http://localhost/lab5/home.php`
2. **Register:** `http://localhost/lab5/register.php`
3. **Login:** `http://localhost/lab5/login.php`
4. **Logout:** `http://localhost/lab5/logout.php`

**Exercise 2 - Google OAuth:**
1. **Google login:** `http://localhost/lab5/google_oauth/google_login.php`
2. **OAuth callback:** Should redirect automatically after Google auth

**Exercise 3 - Protected Pages:**
1. **Library (protected):** `http://localhost/lab5/library.php`
2. **Profile (protected):** `http://localhost/lab5/profile.php`

**Exercise 4 - CRUD with Authentication:**
1. **Add book:** `http://localhost/lab5/add_book.php`
2. **Edit book:** Links from library page
3. **Delete book:** Links from library page

**Expected Results:**
- ✅ User registration and login work
- ✅ Sessions maintain login state
- ✅ Protected pages redirect to login when not authenticated
- ✅ Google OAuth integration works (if configured)
- ✅ CRUD operations are restricted to authenticated users
- ✅ User-specific data displays correctly

---

### Lab 6: Ensure Web Application Security

**Objective:** Understand common web application security threats and learn how to prevent them.

**Setup:**

1.  **Copy files from Lab 5:** If you haven't already, copy the necessary files from the `lab5` directory to a new `lab6` directory. This lab builds upon the authentication and structure of Lab 5.
    ```bash
    cp -r lab5/* lab6/
    ```
2.  **Ensure Database is Setup:** Lab 6 uses the same database structure as Lab 5. Ensure the `LibraryDB_L5` database and its tables are created and populated with sample data (refer to Lab 5 setup). You can run `http://localhost/lab6/db_setup.php` if needed.

**Exercises:**

**Exercise 1: Preventing SQL Injection**
-   **Objective:** Protect your application from SQL injection attacks using prepared statements.
-   **Task:** Review `add_book.php` and ensure prepared statements with bound parameters are used for database inserts.

**Exercise 2: Preventing Cross-Site Scripting (XSS)**
-   **Objective:** Protect your application from XSS attacks by escaping output.
-   **Task:** Review `library.php` and ensure `htmlspecialchars()` is used for displaying user-provided data.

**Exercise 3: Implementing CSRF Protection**
-   **Objective:** Protect your application from CSRF attacks using CSRF tokens.
-   **Tasks:**
    -   Create `csrf_token.php` to generate and store tokens.
    -   Include `csrf_token.php` in `add_book.php`.
    -   Add a hidden `csrf_token` field to the form in `add_book.php`.
    -   Implement token verification in the POST handling of `add_book.php`.

**Exercise 4: Hosting the Application on a Local Network**
-   **Objective:** Host the PHP application on one computer and access it from another on the same network.
-   **Tasks:**
    -   Ensure web server (Apache) and MySQL are running.
    -   Find the host computer's local IP address (`ipconfig` on Windows, `ifconfig` or `ip addr show` on Linux/macOS).
    -   Access the application from another computer using `http://[HOST_COMPUTER_IP_ADDRESS]/lab6`.

**Testing:**

-   **Test SQL Injection:** Attempt to submit malicious SQL in form inputs in `add_book.php`. The application should prevent execution.
-   **Test XSS:** Submit data containing JavaScript (e.g., `<script>alert('XSS');</script>`) through the form and view it on `library.php`. It should display as plain text.
-   **Test CSRF:** Attempt to submit the `add_book.php` form from a different origin (e.g., a simple HTML page on another domain or even a local file opened in the browser). The request should be rejected due to token mismatch.
-   **Test Local Network Access:** Access all pages and functionalities of the Lab 6 application from another computer on your local network.

**Expected Results:**
- ✅ Application is resistant to common SQL injection attempts.
- ✅ User-provided data is displayed safely, preventing XSS.
- ✅ Form submissions are protected against CSRF.
- ✅ Application is accessible and functional from other devices on the local network.

## Troubleshooting

### Common PHP Errors

**1. "Call to undefined function mysqli_connect()"**
```ini
# Enable mysqli extension in php.ini
extension=mysqli
```

**2. "SSL connection error" or "cURL error 60"**
```ini
# Add to php.ini
curl.cainfo = "path/to/cacert.pem"
openssl.cafile = "path/to/cacert.pem"
```

**3. "Headers already sent" error**
- Check for whitespace or output before `<?php` tags
- Ensure no echo/print statements before header() calls

**4. "Access denied for user 'root'@'localhost'"**
```sql
-- Reset MySQL root password
ALTER USER 'root'@'localhost' IDENTIFIED BY 'your_new_password';
FLUSH PRIVILEGES;
```

### Database Connection Issues

**1. "Connection refused"**
- Ensure MySQL service is running
- Check if MySQL is listening on port 3306
- Verify firewall settings

**2. "Unknown database"**
- Run the database creation scripts for each lab
- Check database names match configuration files

**3. "Table doesn't exist"**
- Run the table creation scripts
- Verify you're connected to the correct database

### Web Server Issues

**1. "404 Not Found"**
- Ensure web server is running
- Check document root configuration
- Verify file paths are correct

**2. "500 Internal Server Error"**
- Check PHP error logs
- Verify file permissions
- Enable error display in php.ini for debugging

### OAuth Issues

**1. "redirect_uri_mismatch"**
- Verify redirect URI in Google Console matches your configuration
- Ensure URI includes protocol (http/https)

**2. "invalid_client"**
- Check Google Client ID and Secret
- Verify .env file is properly configured

## Common Issues and Solutions

### Issue: Composer Dependencies Not Installing

**Solution:**
```bash
# Clear composer cache
composer clear-cache

# Install with verbose output
composer install -v

# If behind proxy, configure proxy settings
composer config -g http-proxy http://proxy.company.com:8080
```

### Issue: Session Not Persisting

**Solution:**
```php
// Check session configuration
ini_set('session.cookie_lifetime', 3600);
ini_set('session.gc_maxlifetime', 3600);

// Ensure session_start() is called before any output
session_start();
```

### Issue: File Upload Errors

**Solution:**
```ini
; Increase upload limits in php.ini
upload_max_filesize = 10M
post_max_size = 10M
max_file_uploads = 20
```

### Issue: Memory Limit Exceeded

**Solution:**
```ini
; Increase memory limit in php.ini
memory_limit = 256M

; Or set in specific script
ini_set('memory_limit', '256M');
```

## Development Best Practices

1. **Always use prepared statements** for database queries
2. **Validate and sanitize** all user input
3. **Use HTTPS** in production environments
4. **Keep sensitive data** in environment variables
5. **Enable error logging** and disable display_errors in production
6. **Use version control** (Git) for your projects
7. **Test thoroughly** on different browsers and devices
8. **Follow PSR standards** for PHP code style

## Additional Resources

- [PHP Official Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Composer Documentation](https://getcomposer.org/doc/)
- [Google OAuth 2.0 Documentation](https://developers.google.com/identity/protocols/oauth2)
- [PSR Standards](https://www.php-fig.org/psr/)

## Support

If you encounter issues not covered in this guide:

1. Check the PHP error logs
2. Verify all prerequisites are installed
3. Ensure database connections are properly configured
4. Test with a simple PHP script first
5. Check file and directory permissions

For additional help, consult the official documentation or seek assistance from your instructor or development community.
