<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Set timeout duration (e.g., 2 hours)
$timeout_duration = 7200;

// Check if the session is active
if (isset($_SESSION['LAST_ACTIVITY']) && 
    (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../auth/login.php");
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time


// Track the current page
if (isset($_SESSION['current_page']) && $_SESSION['current_page'] !== 'browse') {
    // Destroy session if user navigated away from the expected page
    session_unset();
    session_destroy();
    header("Location: ../auth/login.php");
    exit();
}
$_SESSION['current_page'] = 'browse'; // Update to current page


session_start();
if (!isset($_SESSION['email']) || $_SESSION['member_type'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../config/database.php';
require_once '../admin/books.php';


$db = new Database();
$connection = $db->getConnection();
$book = new Book($connection);

// User feedback
$feedback = "";

// Handle Create Book
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_book'])) {
    $book->book_title = $_POST['book_title'] ?? '';
    $book->author = $_POST['author'] ?? '';
    $book->publisher = $_POST['publisher'] ?? '';
    $book->category = $_POST['category'] ?? '';
    $book->image = $_POST['image'] ?? '';
    $status = $_POST['status'] ?? 'Available'; // Default status

    // Insert book into the books table
    $stmt = $connection->prepare("INSERT INTO books (book_title, author, publisher, category, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$book->book_title, $book->author, $book->publisher, $book->category, $book->image]);

    // Get the last inserted book_id
    $lastInsertId = $connection->lastInsertId();

    // Insert status into the book_status table
    $statusStmt = $connection->prepare("INSERT INTO book_status (book_id, status) VALUES (?, ?)");
    $statusStmt->execute([$lastInsertId, $status]);

    if ($stmt && $statusStmt) {
        $feedback = "Book created successfully!";
    } else {
        $feedback = "Failed to create book.";
    }
}

// Handle Delete Book
if (isset($_GET['delete_id'])) {
    $book->book_id = $_GET['delete_id'];
    if ($book->delete()) {
        $feedback = "Book deleted successfully!";
    } else {
        $feedback = "Failed to delete book.";
    }
}

// Fetch all books for display with their status
$stmt = $connection->prepare("SELECT b.book_id, b.book_title, b.author, b.publisher, b.category, b.image, 
                                     IFNULL(bs.status, 'Available') AS status 
                              FROM books b
                              LEFT JOIN book_status bs ON b.book_id = bs.book_id");
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Possible status values for the dropdown
$statusOptions = ['Available', 'Onloan', 'Deleted'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Admin Dashboard</title>
</head>
<body>
<?php include '../includes/header.php'; ?>

<br>
<br>
<br>



    <h1>Welcome to the Admin Dashboard</h1>
    <?php if ($feedback): ?>
        <p><?php echo htmlspecialchars($feedback); ?></p>
    <?php endif; ?>

    



    <h2>All Books</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Author</th>
            <th>Publisher</th>
            <th>Category</th>
            <th>Image</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($books as $b): ?>
            <tr>
                <td><?php echo htmlspecialchars($b['book_id'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($b['book_title'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($b['author'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($b['publisher'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($b['category'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($b['image'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($b['status'] ?? 'N/A'); ?></td> <!-- Display the status -->
                <td>
                    <a href="../admin/update.php?update_id=<?php echo $b['book_id']; ?>">Edit</a> | 
                    <a href="admin_dashboard.php?delete_id=<?php echo $b['book_id']; ?>" onclick="return confirm('Are you sure you want to delete this book?');">Delete</a> |
                    <a href="../admin/create.php">Create book</a> |
                    <a href="../auth/logout.php">Logout</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>


    <?php include '../includes/footer.php'; ?>
</body>
</html>
