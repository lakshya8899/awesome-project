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

// Initialize $currentBook
$currentBook = null;

// Fetch book data for editing
if (isset($_GET['update_id'])) {
    $book->book_id = $_GET['update_id'];
    $currentBook = $book->getById(); // Assuming this method populates book properties
}

// Handle Update Book
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_book'])) {
    $book->book_id = $_POST['book_id'];
    $book->book_title = $_POST['book_title'] ?? '';
    $book->author = $_POST['author'] ?? '';
    $book->publisher = $_POST['publisher'] ?? '';
    $book->category = $_POST['category'] ?? '';
    $book->image = $_POST['image'] ?? '';
    $status = $_POST['status'] ?? 'Available'; // Default status

    // Update the books table
    $stmt = $connection->prepare("UPDATE books SET book_title = ?, author = ?, publisher = ?, category = ?, image = ? WHERE book_id = ?");
    $stmt->execute([$book->book_title, $book->author, $book->publisher, $book->category, $book->image, $book->book_id]);

    // Update status in the book_status table
    $statusStmt = $connection->prepare("UPDATE book_status SET status = ? WHERE book_id = ?");
    $statusStmt->execute([$status, $book->book_id]);

    if ($stmt && $statusStmt) {
        $feedback = "Book updated successfully!";
        // Redirect to admin_dashboard.php after successful update
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $feedback = "Failed to update book.";
    }
}

// Possible status values for the dropdown
$statusOptions = ['Available', 'Onloan', 'Deleted'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Update Book</title>
</head>
<body>

<?php include '../includes/header.php'; ?>


    <h1>Update Book</h1>
    <?php if ($feedback): ?>
        <p><?php echo htmlspecialchars($feedback); ?></p>
    <?php endif; ?>

    <?php if ($currentBook): ?>
        <form method="POST" action="update.php">
            <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book->book_id); ?>">
            <input type="text" name="book_title" required placeholder="Title" value="<?php echo htmlspecialchars($book->book_title); ?>">
            <input type="text" name="author" required placeholder="Author" value="<?php echo htmlspecialchars($book->author); ?>">
            <input type="text" name="publisher" required placeholder="Publisher" value="<?php echo htmlspecialchars($book->publisher); ?>">
            
            <!-- Dropdown for Category -->
            <select name="category" required>
                <option value="Fiction" <?php echo ($book->category === 'Fiction') ? 'selected' : ''; ?>>Fiction</option>
                <option value="Nonfiction" <?php echo ($book->category === 'Nonfiction') ? 'selected' : ''; ?>>Nonfiction</option>
                <option value="Reference" <?php echo ($book->category === 'Reference') ? 'selected' : ''; ?>>Reference</option>
            </select>

            <input type="text" name="image" required placeholder="Image URL" value="<?php echo htmlspecialchars($book->image); ?>">
            
            <!-- Dropdown for Status -->
            <select name="status">
                <?php foreach ($statusOptions as $option): ?>
                    <option value="<?php echo htmlspecialchars($option); ?>" <?php echo ($book->status === $option) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($option); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" name="update_book">Update Book</button>
            <a href="admin_dashboard.php">Go back to Admin Dashboard</a>

        </form>
    <?php else: ?>
        <p>Book not found for editing.</p>
    <?php endif; ?>


    <?php include '../includes/footer.php'; ?>
</body>
</html>
