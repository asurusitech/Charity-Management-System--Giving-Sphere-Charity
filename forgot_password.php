<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    
    // Check if the email exists in the users table
    $stmt = $database->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Generate a unique token
        $token = bin2hex(random_bytes(50));
        $expire = time() + 3600; // Token expires in 1 hour

        // Insert the token into the password_resets table
        $insert_stmt = $database->prepare("INSERT INTO password_resets (email, token, expire) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("ssi", $email, $token, $expire);
        $insert_stmt->execute();

        // Send the reset link to the user's email
        // Note: Replace the following line with actual email sending code
        // mail($email, "Password Reset", "Click on the link to reset your password: http://yourwebsite.com/reset_password.php?token=$token");

        $success = '<label for="promter" class="form-label" style="color:green;text-align:center;">A password reset link has been sent to your email.</label>';
    } else {
        $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">We can\'t find any account with this email.</label>';
    }
} else {
    $error = '<label for="promter" class="form-label">&nbsp;</label>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Giving Sphere Charity</title>
    <link rel="stylesheet" href="login.css">
    <link rel="icon" href="logo.png" type="image/x-icon" style="border-radius: 50%;"> <!-- Favicon link -->
</head>
<body>

<header>
    <div class="container header-content">
        <div class="logo-title">
            <img src="logo.png" alt="Logo" class="logo">
            <h1>Giving Sphere Charity</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="signup.php">Sign Up</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="login-section">
    <div class="container">
        <div class="login-form">
            <h2>Forgot Password</h2>
            <form action="" method="post">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <button type="submit">Submit</button>
                </div>
            </form>
            <?php echo $error; ?>
            <?php echo $success; ?>
            <p class="signup-link">Don't have an account? <a href="signup.php">Sign up here</a></p>
            <p class="login-link"><a href="login.php">Back to Login</a></p>
        </div>
    </div>
</section>

<footer>
    <div class="container">
     
    <p>2024 Giving Sphere Charity - Where Compassion Meets Action.</p>
</div>
</footer>
</body>
</html>
