<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php'; // Ensure you have your database connection in this file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    // Prepare and execute query to check if email exists
    $query = $database->prepare("SELECT * FROM users WHERE email = ?");
    $query->bind_param('s', $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        // Email found, redirect to reset password page
        $_SESSION['email'] = $email;
        header("Location: reset_password.php?email=" . urlencode($email));
        exit();
    } else {
        // Email not found, redirect back with an error
        $_SESSION['error'] = "Email not found. Please try again.";
        header("Location: forgot_password.html");
        exit();
    }
}
?>
