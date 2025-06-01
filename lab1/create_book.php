<?php
// Include database configuration file
require_once 'db_config.php';

// Define variables and initialize with empty values
$title = $author = $publication_year = $genre = $price = "";
$title_err = $author_err = $publication_year_err = $genre_err = $price_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate title
    if (empty(trim($_POST["title"]))) {
        $title_err = "Please enter a title.";
    } else {
        $title = trim($_POST["title"]);
    }

    // Validate author
    if (empty(trim($_POST["author"]))) {
        $author_err = "Please enter an author.";
    } else {
        $author = trim($_POST["author"]);
    }

    // Validate publication_year
    if (empty(trim($_POST["year"]))) {
        $publication_year_err = "Please enter a publication year.";
    } else {
        $publication_year = trim($_POST["year"]);
        if (!ctype_digit($publication_year)) {
            $publication_year_err = "Please enter a valid year.";
        }
    }

    // Validate genre
    if (empty(trim($_POST["genre"]))) {
        $genre_err = "Please enter a genre.";
    } else {
        $genre = trim($_POST["genre"]);
    }

    // Validate price
    if (empty(trim($_POST["price"]))) {
        $price_err = "Please enter a price.";
    } else {
        $price = trim($_POST["price"]);
        if (!is_numeric($price)) {
            $price_err = "Please enter a valid price.";
        }
    }


    // Check input errors before inserting in database
    if (empty($title_err) && empty($author_err) && empty($publication_year_err) && empty($genre_err) && empty($price_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO Books (title, author, publication_year, genre, price) VALUES (?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssisd", $param_title, $param_author, $param_publication_year, $param_genre, $param_price);

            // Set parameters
            $param_title = $title;
            $param_author = $author;
            $param_publication_year = $publication_year;
            $param_genre = $genre;
            $param_price = $price;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Records created successfully. Redirect to landing page
                header("location: read_books.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Book</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        .error {
            @apply text-red-500 text-xs italic;
        }
        label {
            @apply block text-gray-700 text-sm font-bold mb-2;
        }
        input[type="text"], input[type="number"] {
            @apply shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50;
        }
        input[type="submit"] {
             @apply bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50;
        }
         a {
            @apply inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800 ml-4;
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
        <h2 class="text-2xl font-bold mb-6 text-center">Add New Book</h2>
        <p class="text-gray-600 text-sm mb-6 text-center">Please fill this form and submit to add a book record to the
            database.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($title_err)) ? 'border border-red-500 rounded' : ''; ?>">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" value="<?php echo $title; ?>">
                <span class="error"><?php echo $title_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($author_err)) ? 'border border-red-500 rounded' : ''; ?>">
                <label for="author">Author:</label>
                <input type="text" name="author" id="author" value="<?php echo $author; ?>">
                <span class="error"><?php echo $author_err; ?></span>
            </div>
            <div
                class="form-group <?php echo (!empty($publication_year_err)) ? 'border border-red-500 rounded' : ''; ?>">
                <label for="year">Year:</label>
                <input type="number" name="year" id="year" value="<?php echo $publication_year; ?>">
                <span class="error"><?php echo $publication_year_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($genre_err)) ? 'border border-red-500 rounded' : ''; ?>">
                <label for="genre">Genre:</label>
                <input type="text" name="genre" id="genre" value="<?php echo $genre; ?>">
                <span class="error"><?php echo $genre_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($price_err)) ? 'border border-red-500 rounded' : ''; ?>">
                <label for="price">Price:</label>
                <input type="text" name="price" id="price" value="<?php echo $price; ?>">
                <span class="error"><?php echo $price_err; ?></span>
            </div>
            <div class="flex items-center justify-between">
                <input type="submit" value="Add Book">
                <a href="read_books.php">Cancel</a>
            </div>
        </form>
    </div>
</body>

</html>