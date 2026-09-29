<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['email']) || !isset($_SESSION['email']) || $_GET['email'] !== $_SESSION['email']) {
    header("Location: forgot_password.html");
    exit();
}

$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Giving Sphere Charity</title>
    <link rel="stylesheet" href="login.css">
    <style>
        .alert {
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.alert-danger {
    color: #a94442;
    background-color: #f2dede;
    border-color: #ebccd1;
}

.alert-success {
    color: #3c763d;
    background-color: #dff0d8;
    border-color: #d6e9c6;
}

    </style>
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
            <h2>Reset Password</h2>

            <!-- Display success or error message -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <form action="update_password.php" method="post">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="form-group">
                    <button type="submit">Update Password</button>
                </div>
            </form>
            <p class="signup-link">Remembered your password? <a href="login.php">Login here</a></p>
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
