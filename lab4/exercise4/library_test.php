<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Autoloader for classes within the LibrarySystem namespace
spl_autoload_register(function ($class_name) {
    // Check if the class is in our namespace
    if (strpos($class_name, 'LibrarySystem\\') === 0) {
        // Remove the namespace prefix
        $class_file = str_replace('LibrarySystem\\', '', $class_name);
        // Convert to path (e.g., MyClass -> MyClass.php)
        $file = __DIR__ . '/classes/' . $class_file . '.php';
        if (file_exists($file)) {
            require_once $file;
        } else {
            // Fallback for interfaces if not caught by the above
            $interface_file = __DIR__ . '/classes/' . $class_name . '.php';
            if (file_exists($interface_file)) {
                require_once $interface_file;
            } else {
                // echo "Autoloader: Could not find $file or $interface_file for $class_name <br>";
            }
        }
    }
});


require_once '../db_connect.php'; // Adjust path to your db_connect.php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Replace with your MySQL username
define('DB_PASSWORD', '12345'); // Replace with your MySQL password
define('DB_NAME_L4E4', 'LibraryDB_OOP');
$conn = connectToDatabase(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME_L4E4);

use LibrarySystem\Book;
use LibrarySystem\Ebook;
use LibrarySystem\Member;

// --- Fetch Members for dropdown ---
$members_list = [];
$member_res = $conn->query("SELECT member_id, name FROM Members ORDER BY name");
if ($member_res) {
    while ($row = $member_res->fetch_assoc()) {
        $members_list[] = $row;
    }
}

// --- Handle Actions (Borrow/Return) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $book_id_action = (int) $_POST['book_id'];
    $member_id_action = isset($_POST['member_id']) ? (int) $_POST['member_id'] : null;

    $book_to_action = Book::findById($conn, $book_id_action);
    if ($book_to_action && $book_to_action instanceof Ebook) { // Check if it's an Ebook first if Book::findById returns a Book instance for an Ebook ID
        $ebook_instance = Ebook::findById($conn, $book_id_action); // Try to get it as an Ebook
        if ($ebook_instance)
            $book_to_action = $ebook_instance;
    } elseif (!$book_to_action) { // If Book::findById returned null, it might be an Ebook exclusively
        $book_to_action = Ebook::findById($conn, $book_id_action);
    }

    if ($book_to_action) {
        if ($_POST['action'] === 'borrow' && $member_id_action) {
            $member_actor = Member::findById($conn, $member_id_action);
            if ($member_actor) {
                if ($book_to_action->borrowItem($member_actor)) {
                    $_SESSION['message'] = "Book '{$book_to_action->title}' borrowed successfully by {$member_actor->name}.";
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = "Failed to borrow '{$book_to_action->title}'. It might be an Ebook, already borrowed, or an error occurred.";
                    $_SESSION['message_type'] = 'error';
                }
            } else {
                $_SESSION['message'] = "Member not found for borrowing.";
                $_SESSION['message_type'] = 'error';
            }
        } elseif ($_POST['action'] === 'return') {
            if ($book_to_action->returnItem()) {
                $_SESSION['message'] = "Book '{$book_to_action->title}' returned successfully.";
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = "Failed to return '{$book_to_action->title}'. It might not be borrowed or an error occurred.";
                $_SESSION['message_type'] = 'error';
            }
        }
    } else {
        $_SESSION['message'] = "Book ID {$book_id_action} not found for action.";
        $_SESSION['message_type'] = 'error';
    }
    header("Location: library_test.php"); // PRG pattern
    exit;
}

// --- Fetch All Items (Books and Ebooks) ---
$all_items = [];
$physical_books_data = $conn->query("SELECT book_id FROM Books WHERE is_ebook = FALSE");
if ($physical_books_data) {
    while ($row = $physical_books_data->fetch_assoc()) {
        $book_obj = Book::findById($conn, (int) $row['book_id']);
        if ($book_obj)
            $all_items[] = $book_obj;
    }
}
$ebooks_data = $conn->query("SELECT book_id FROM Books WHERE is_ebook = TRUE");
if ($ebooks_data) {
    while ($row = $ebooks_data->fetch_assoc()) {
        $ebook_obj = Ebook::findById($conn, (int) $row['book_id']);
        if ($ebook_obj)
            $all_items[] = $ebook_obj;
    }
}

// --- Fetch All Members ---
$members_data = []; // Renamed to avoid conflict with $members variable name for member objects
$member_res_display = $conn->query("SELECT member_id FROM Members ORDER BY name");
if ($member_res_display) {
    while ($row = $member_res_display->fetch_assoc()) {
        $member_obj = Member::findById($conn, (int) $row['member_id']);
        if ($member_obj)
            $members_data[] = $member_obj;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Library System - Tabular View</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        body {
            @apply bg-gray-100;
        }
        .container {
            @apply max-w-6xl mx-auto p-4;
        }
        h1 {
            @apply text-3xl font-bold text-center text-gray-800 mb-8;
        }
        h2 {
            @apply text-2xl font-semibold text-gray-700 mt-10 mb-5;
        }
        table {
            @apply w-full bg-white shadow-md rounded-lg;
        }
        th, td {
            @apply px-4 py-2 border border-gray-200 text-left text-sm;
        }
        th {
            @apply bg-gray-100 font-semibold text-gray-600 uppercase tracking-wider;
        }
        tr:nth-child(even) {
            @apply bg-gray-50;
        }
        .status-available {
            @apply text-green-600 font-semibold;
        }
        .status-borrowed {
            @apply text-red-600 font-semibold;
        }
        .status-ebook {
            @apply text-purple-600 font-semibold;
        }
        .action-form select, .action-form button {
            @apply py-1 px-2 border border-gray-300 rounded-md text-xs;
        }
        .action-form button {
            @apply bg-blue-500 hover:bg-blue-700 text-white font-medium;
        }
        .action-form .return-button {
            @apply bg-green-500 hover:bg-green-700 text-white font-medium;
        }
        .message {
            @apply p-3 mb-6 rounded-md text-center font-medium text-sm;
        }
        .success {
            @apply bg-green-100 text-green-800 border border-green-300;
        }
        .error {
            @apply bg-red-100 text-red-800 border border-red-300;
        }
        .borrowed-items-list {
            @apply list-disc list-inside text-xs text-gray-600;
        }
        .borrowed-items-list li {
            @apply mb-1;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Library System Test</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message <?php echo htmlspecialchars($_SESSION['message_type']); ?>">
                <?php echo htmlspecialchars($_SESSION['message']); ?>
            </div>
            <?php
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            ?>
        <?php endif; ?>

        <h2>Available Books & Ebooks:</h2>
        <?php if (!empty($all_items)): ?>
            <div class="overflow-x-auto shadow-lg rounded-lg">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Type</th>
                            <th>Genre</th>
                            <th>Year</th>
                            <th>Price (FCFA)</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item->getId()); ?></td>
                                <td><?php echo htmlspecialchars($item->title); ?></td>
                                <td><?php echo htmlspecialchars($item->author); ?></td>
                                <td>
                                    <?php if ($item instanceof Ebook): ?>
                                        <span class="status-ebook">Ebook</span>
                                    <?php else: ?>
                                        Book
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($item->genre); ?></td>
                                <td><?php echo htmlspecialchars($item->publication_year); ?></td>
                                <td>
                                    <?php if ($item instanceof Ebook): ?>
                                        FCFA
                                        <?php echo htmlspecialchars(number_format($item->getPriceAfterDiscount(), 0, '.', ',')); ?>
                                        <span class="text-xs text-purple-600">(<?php
                                        $discountString = 'No discount';
                                        if ($item->getDiscountAmount() > 0) {
                                            $percentage = ($item->price > 0) ? round(($item->getDiscountAmount() / $item->price) * 100) : 0;
                                            $discountString = $percentage . '% off';
                                        }
                                        echo htmlspecialchars($discountString);
                                        ?>)</span>
                                    <?php else: ?>
                                        FCFA <?php echo htmlspecialchars(number_format($item->price, 0, '.', ',')); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($item instanceof Ebook): ?>
                                        <span class="status-ebook">N/A (Digital)</span>
                                    <?php elseif ($item->isBorrowed()): ?>
                                        <span class="status-borrowed">Borrowed by Member ID:
                                            <?php echo htmlspecialchars($item->getBorrowedByMemberId() ?? 'N/A'); ?></span>
                                    <?php else: ?>
                                        <span class="status-available">Available</span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-form">
                                    <?php if ($item instanceof Book && !($item instanceof Ebook)): ?>
                                        <?php if (!$item->isBorrowed()): ?>
                                            <form method='POST' action='library_test.php' class='inline-block'>
                                                <input type='hidden' name='book_id' value='<?php echo $item->getId(); ?>'>
                                                <select name='member_id' required class="mr-1">
                                                    <option value="">Borrower...</option>
                                                    <?php foreach ($members_list as $mem): ?>
                                                        <option value='<?php echo $mem['member_id']; ?>'>
                                                            <?php echo htmlspecialchars($mem['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type='submit' name='action' value='borrow'>Borrow</button>
                                            </form>
                                        <?php else: ?>
                                            <form method='POST' action='library_test.php' class='inline-block'>
                                                <input type='hidden' name='book_id' value='<?php echo $item->getId(); ?>'>
                                                <button type='submit' name='action' value='return' class="return-button">Return</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-600 mt-4">No books found in the library.</p>
        <?php endif; ?>

        <h2>Members & Borrowed Items:</h2>
        <?php if (!empty($members_data)): ?>
            <div class="overflow-x-auto shadow-lg rounded-lg">
                <table>
                    <thead>
                        <tr>
                            <th>Member ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Member Since</th>
                            <th>Borrowed Items</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members_data as $member): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($member->getId()); ?></td>
                                <td><?php echo htmlspecialchars($member->name); ?></td>
                                <td><?php echo htmlspecialchars($member->email); ?></td>
                                <td><?php echo htmlspecialchars(date('d M Y', strtotime($member->membership_date))); ?></td>
                                <td>
                                    <?php
                                    $borrowedItems = $member->viewBorrowedBooks();
                                    if (!empty($borrowedItems)):
                                        ?>
                                        <ul class="borrowed-items-list">
                                            <?php foreach ($borrowedItems as $b_item): ?>
                                                <li>
                                                    <?php echo htmlspecialchars($b_item['title']); ?>
                                                    <span class="text-gray-500">(Loaned:
                                                        <?php echo htmlspecialchars(date('d M Y', strtotime($b_item['loan_date']))); ?>)</span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <span class="text-gray-500 italic">None</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-600 mt-4">No members found.</p>
        <?php endif; ?>

    </div>
</body>

</html>
<?php $conn->close(); ?>