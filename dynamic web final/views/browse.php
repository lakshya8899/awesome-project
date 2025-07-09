<?php
session_start();

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

// Check if the user is logged in and is a Member
if (!isset($_SESSION['email']) || $_SESSION['member_type'] !== 'Member') {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../config/database.php';
require_once '../includes/User.php';

$database = new Database();
$db = $database->getConnection();

// Fetch books and their statuses with a LEFT JOIN on book_status table
$stmt = $db->prepare("SELECT b.book_id, b.book_title, b.author, b.publisher, b.language, b.category, b.image, 
                             IFNULL(bs.status, 'Available') AS status,
                             bs.applied_date,
                             bs.member_id
                      FROM books b
                      LEFT JOIN book_status bs ON b.book_id = bs.book_id");
$stmt->execute();

// Handle the borrow request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['borrow'])) {
    $book_id = htmlspecialchars(strip_tags($_POST['book_id']));

    // Retrieve the current status of the book
    $currentStatusQuery = "SELECT * FROM book_status WHERE book_id = :book_id";
    $stmtStatus = $db->prepare($currentStatusQuery);
    $stmtStatus->bindParam(':book_id', $book_id);
    $stmtStatus->execute();
    $statusRow = $stmtStatus->fetch(PDO::FETCH_ASSOC);

    if ($statusRow) {
        $dueDate = new DateTime($statusRow['applied_date']);
        $dueDate->modify('+21 days');
        $currentDate = new DateTime();

        if ($currentDate > $dueDate) {
            echo "<p>This book is overdue. Please return it before borrowing again.</p>";
        } elseif ($statusRow['status'] === 'Available') {
            // Update status to "Onloan" and set member_id
            $updateStatusQuery = "UPDATE book_status SET status = 'Onloan', member_id = :member_id, applied_date = NOW() WHERE book_id = :book_id";
            $stmtUpdate = $db->prepare($updateStatusQuery);
            $stmtUpdate->bindParam(':member_id', $_SESSION['member_id']);
            $stmtUpdate->bindParam(':book_id', $book_id);

            if ($stmtUpdate->execute()) {
                echo "<p>Book successfully borrowed!</p>";
            } else {
                echo "<p>Failed to borrow the book. Please try again.</p>";
                print_r($stmtUpdate->errorInfo());
            }
        } else {
            echo "<p>Book is not available for borrowing.</p>";
        }
    } else {
        // If there is no status entry, create one and mark it as "Onloan"
        $insertStatusQuery = "INSERT INTO book_status (book_id, member_id, status, applied_date) VALUES (:book_id, :member_id, 'Onloan', NOW())";
        $stmtInsert = $db->prepare($insertStatusQuery);
        $stmtInsert->bindParam(':book_id', $book_id);
        $stmtInsert->bindParam(':member_id', $_SESSION['member_id']);

        if ($stmtInsert->execute()) {
            echo "<p>Book successfully borrowed!</p>";
        } else {
            echo "<p>Failed to borrow the book. Please try again.</p>";
            print_r($stmtInsert->errorInfo());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Browse Books</title>
</head>
<body>
    
<?php include '../includes/header.php'; ?>

<br><br><br>

<h1>Browse Books</h1>

<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Publisher</th>
            <th>Language</th>
            <th>Category</th>
            <th>Status</th>
            <th>Image</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <?php 
                $status = htmlspecialchars($row['status']);
                $dueDateMessage = '';
                if ($status === 'Onloan') {
                    $appliedDate = new DateTime($row['applied_date']);
                    $dueDate = clone $appliedDate;
                    $dueDate->modify('+21 days');
                    $currentDate = new DateTime();
                    if ($currentDate > $dueDate) {
                        $dueDateMessage = '<p style="color: red;">Overdue!</p>';
                    } else {
                        $dueDateMessage = '<p>Due Date: ' . $dueDate->format('Y-m-d') . '</p>';
                    }
                }
            ?>
            <tr>
                <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                <td><?php echo htmlspecialchars($row['author']); ?></td>
                <td><?php echo htmlspecialchars($row['publisher']); ?></td>
                <td><?php echo htmlspecialchars($row['language']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td>
                    <?php echo $status; ?>
                    <?php echo $dueDateMessage; ?>
                </td>
                <td><img src="../assets/img/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['book_title']); ?>"></td>
                <td>
                    <?php if (trim(strtolower($status)) === 'available'): ?>
                        <form method="POST" action="browse.php">
                            <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($row['book_id']); ?>">
                            <button type="submit" name="borrow">Borrow</button>
                        </form>
                    <?php else: ?>
                        <p>Borrowed</p>
                    <?php endif; ?>
                    <a href="../auth/logout.php">Logout</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
</body>
</html>
