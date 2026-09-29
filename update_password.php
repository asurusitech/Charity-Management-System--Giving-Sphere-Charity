<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php'; // Ensure you have your database connection in this file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: reset_password.php?email=" . urlencode($email));
        exit();
    }

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Update the password in the database
    $query = $database->prepare("UPDATE donors SET donpassword = ? WHERE donemail = ?");
    $query->bind_param('ss', $hashed_password, $email);
    if ($query->execute()) {
        $_SESSION['success'] = "Password successfully updated. Please login.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating password. Please try again.";
        header("Location: reset_password.php?email=" . urlencode($email));
        exit();
    }
}
?>
