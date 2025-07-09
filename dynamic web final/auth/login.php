<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../config/database.php';
require_once '../includes/User.php';


// Create a Database object and get the connection
$db = new Database();
$connection = $db->getConnection();

// Pass the connection to the User object
$user = new User($connection);

$error_message = ""; // Variable to store error messages

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    // Set email and password as public properties of the user object
    $user->email = $_POST['email'];
    $user->password = $_POST['password'];

    // Perform login
    if ($user->login()) {
        // Store necessary data in the session
        $_SESSION['email'] = $user->email;
        $_SESSION['member_type'] = $user->member_type;

        // Redirect based on member type
        if ($user->member_type == 'Admin') {
            header("Location: ../admin/admin_dashboard.php");
        } else {
            header("Location: ../views/browse.php");
        }
        exit();
    } else {
        // Set error message for incorrect credentials
        $error_message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="center-wrapper"> <!-- Flexbox wrapper -->
    <div class="login-container">
        <h1>University Log-in</h1>
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="input-group">
                <input type="email" name="email" required placeholder="Enter your email" class="input-field" 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="input-group">
                <input type="password" name="password" required placeholder="Enter your password" class="input-field">
            </div>
            <button type="submit" name="login" class="login-btn">Log in</button>
            <a href="../auth/logout.php">Click here to Logout</a>
        </form>
        <div class="footer-links">
            <a href="mailto:support@university.edu">Contact us at support@university.edu</a> | 
            <a href="terms_of_service.php">Terms of Service</a>
        </div>
    </div>
</div>


<?php include '../includes/footer.php'; ?>
</body>
</html>
