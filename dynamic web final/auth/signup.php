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

$first_name = $last_name = $email = '';
$errors = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'password' => '',
    'confirm_password' => ''
];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate first name
    if (!preg_match("/^[a-zA-Z]+$/", $first_name) || strlen($first_name) > 20) {
        $errors['first_name'] = true; // Flag error without showing
    }

    // Validate last name
    if (!preg_match("/^[a-zA-Z]+$/", $last_name) || strlen($last_name) > 20) {
        $errors['last_name'] = true; // Flag error without showing
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    } elseif ($user->isEmailTaken($email)) { // Assuming `isEmailTaken` checks the database
        $errors['email'] = "This email is already registered. Please use a different email.";
    }


    // Validate password
    if (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[0-9]/", $password) || !preg_match("/[\W]/", $password)) {
        $errors['password'] = true; // Flag error without showing
    }

    // Validate confirm password
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = true; // Flag error without showing
    }

    // If no errors, proceed with sign-up
    if (array_filter($errors) === []) {
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->email = $email;
        $user->password = $password;

        if ($user->signUp()) {
            header("Location: login.php"); // Redirect to login
            exit;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <<link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <?php include '../includes/header.php'; ?>

    <div class="center-wrapper">
        <div class="form-container">
            <form id="signupForm" method="POST" action="signup.php">
                <h1>University Sign-Up</h1>

                <div id="general-error" style="color:red;"></div>

                <div>
                    <input type="text" name="first_name" placeholder="First Name">
                    <p class="error-message" style="color:red; display:none;"></p>
                </div>

                <div>
                    <input type="text" name="last_name" placeholder="Last Name">
                    <p class="error-message" style="color:red; display:none;"></p>
                </div>

                <div>
                    <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email) ?>">
                    <?php if (!empty($errors['email'])): ?>
                        <p class="error-message" style="color:red;"><?= $errors['email'] ?></p>
                    <?php endif; ?>
                </div>


                <div>
                    <input type="password" name="password" placeholder="Password">
                    <p class="error-message" style="color:red; display:none;"></p>
                </div>

                <div>
                    <input type="password" name="confirm_password" placeholder="Confirm Password">
                    <p class="error-message" style="color:red; display:none;"></p>
                </div>

                <button type="submit" name="signup">Sign Up</button>
            </form>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>


</body>

</html>

<script src="../assets/js/validation_signup.js" defer></script>