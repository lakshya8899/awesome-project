<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['email']) || $_SESSION['member_type'] !== 'Admin') {
    header("Location: login.php");
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
    <title>Create Book</title>
</head>
<body>

<?php include '../includes/header.php'; ?>

<br>

<br>

<br>


<h2>Create Book</h2>
<form method="POST" action="admin_dashboard.php">
    <input type="text" name="book_title" required placeholder="Title">
    <input type="text" name="author" required placeholder="Author">
    <input type="text" name="publisher" required placeholder="Publisher">
    
    <!-- Dropdown for Category -->
    <select name="category" required>
        <option value="Fiction">Fiction</option>
        <option value="Nonfiction">Nonfiction</option>
        <option value="Reference">Reference</option>
    </select>

    <input type="text" name="image" required placeholder="Image URL">
    
    <!-- Dropdown for Status -->
    <select name="status">
        <?php foreach ($statusOptions as $status): ?>
            <option value="<?php echo htmlspecialchars($status); ?>"><?php echo htmlspecialchars($status); ?></option>
        <?php endforeach; ?>
    </select>
    
    <button type="submit" name="create_book">Create Book</button>
    <a href="admin_dashboard.php">Go back to Admin Dashboard</a>

    
    
</form>




<?php include '../includes/footer.php'; ?>

</body>
</html>