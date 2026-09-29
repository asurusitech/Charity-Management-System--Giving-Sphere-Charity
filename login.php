<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['useremail'];
    $password = $_POST['userpassword'];
    
    // Query to get the user by email
    $stmt = $database->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $usertype = $user['usertype'];

        if($usertype == 'a') {
            // Check admin credentials
            $checker = $database->prepare("SELECT * FROM admin WHERE aemail=? AND apassword=?");
            $checker->bind_param("ss", $email, $password);
            $checker->execute();
            $admin_result = $checker->get_result();

            if ($admin_result->num_rows == 1) {
                // Admin dashboard
                $_SESSION['user'] = $email;
                $_SESSION['usertype'] = 'a';
                header('location: admin_dashboard.php');
                exit();
            } else {
                $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Wrong credentials: Invalid email or password</label>';
            }
        } elseif ($usertype == 'd') {
            // Donor login
            $checker = $database->prepare("SELECT * FROM donors WHERE donemail=?");
            $checker->bind_param("s", $email);
            $checker->execute();
            $donor_result = $checker->get_result();

            if ($donor_result->num_rows == 1) {
                $donor = $donor_result->fetch_assoc();
                if (password_verify($password, $donor['donpassword'])) {
                    $_SESSION['user'] = $donor['donorid'];
                    $_SESSION['usertype'] = 'd';
                    header('Location: donor_dashboard.php');
                    exit();
                } else {
                    $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Wrong credentials: Invalid email or password</label>';
                }
            } else {
                $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">We can\'t find any account with this email.</label>';
            }
        }
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
    <title>Login - Giving Sphere Charity</title>
    <link rel="stylesheet" href="login.css">
    <script src="https://kit.fontawesome.com/03050c7944.js" crossorigin="anonymous"></script>
    
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
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="signup.php"><i class="fas fa-user-plus"></i> Sign Up</a></li>
     </ul>
        </nav>
    </div>
</header>

<section class="login-section">
    <div class="container">
        <div class="login-form">
            <h2>Login</h2>
            <form action="" method="post">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="useremail" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="userpassword" required>
                </div>
                <div class="form-group">
                    <button type="submit">Login</button>
                </div>
            </form>
            <?php echo $error; ?>
            <p class="signup-link">Don't have an account? <a href="signup.php">Sign up here</a></p>
            <p class="forgot-password-link"><a href="forgot_password.html">Forgot Password?</a></p>
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
