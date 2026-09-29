<?php
// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'connection.php';

$email_error = $phone_error = $address_error = $dob_error = $password_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Client-side validations are handled by JavaScript

    // Server-side validations
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $donemail = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $donpassword = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // Validate First Name and Last Name (letters only)
    $nameRegex = '/^[a-zA-Z]+$/';
    if (!preg_match($nameRegex, $first_name)) {
        $first_name_error = 'Please enter a valid first name (letters only).';
    }
    if (!preg_match($nameRegex, $last_name)) {
        $last_name_error = 'Please enter a valid last name (letters only).';
    }

    // Validate Phone Number (Kenyan format, 10 digits only)
    /*$phoneRegex = '/^[0-9]{10}$/';
    if (!preg_match($phoneRegex, $phone)) {
        $phone_error = 'Please enter a valid Kenyan phone number (10 digits only).';
    }
    */


// Address validation
    if (preg_match('/^[a-zA-Z\s]+$/', $address)) {
        echo "Address is valid.";
        // Proceed with further processing
    } else {
        echo "Address is invalid. Please enter only letters and spaces.";
        // Handle the invalid input case
    }

    $localPhoneRegex = '/^(07|01|02)[0-9]{8}$/';
    $internationalPhoneRegex = '/^254[0-9]{9}$/';

    if (empty($phone)) {
        $phone_error = "Phone number is required";
    } elseif (!preg_match($localPhoneRegex, $phone) && !preg_match($internationalPhoneRegex, $phone)) {
        $phone_error = "Please enter a valid Kenyan phone number (10 digits starting with 07, 01, or 02, or 12 digits starting with 2547).";
    }

    // Validate Date of Birth (at least 18 years ago and not future date)
    $minAge = 18;
    $today = new DateTime();
    $birthdate = new DateTime($dob);
    $age = $today->diff($birthdate)->y;
    if ($birthdate >= $today || $age < $minAge) {
        $dob_error = 'Please enter a valid date of birth (at least 14 years old).';
    }

    // Validate Password (minimum 8 characters, 1 digit, 1 capital letter, 1 special character)
    $passwordRegex = '/^(?=.*\d)(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$/';
    if (!preg_match($passwordRegex, $donpassword)) {
        $password_error = 'Password must be at least 8 characters with 1 digit, 1 capital letter, and 1 special character.';
    }

    // Check if passwords match
    if ($donpassword !== $confirm_password) {
        $password_error = 'Passwords do not match.';
    }

    // Check for duplicate email
    $sql_check_email = "SELECT * FROM donors WHERE donemail = ?";
    $stmt_check_email = $database->prepare($sql_check_email);
    $stmt_check_email->bind_param("s", $donemail);
    $stmt_check_email->execute();
    $stmt_check_email->store_result();

    if ($stmt_check_email->num_rows > 0) {
        $email_error = "Email is already taken.";
    }

    $stmt_check_email->close();

    // Check for duplicate phone
    $sql_check_phone = "SELECT * FROM donors WHERE phone = ?";
    $stmt_check_phone = $database->prepare($sql_check_phone);
    $stmt_check_phone->bind_param("s", $phone);
    $stmt_check_phone->execute();
    $stmt_check_phone->store_result();

    if ($stmt_check_phone->num_rows > 0) {
        $phone_error = "Phone number is already taken.";
    }

    $stmt_check_phone->close();

    // If no validation errors, proceed with insertion
    if (empty($first_name_error) && empty($last_name_error) && empty($email_error) && empty($phone_error) && empty($address_error) && empty($dob_error) && empty($password_error)) {
        // Begin a transaction
        $database->begin_transaction();

        try {
            // Generate a unique donorid
            $donorid = uniqid('donor_');

            /*
            //Weak: MD5 is considered cryptographically broken and unsuitable for further use.
            //Collision Vulnerabilities: MD5 is prone to collisions, where two different inputs produce the same hash output.
            //Fast Processing: MD5's speed makes it vulnerable to brute-force attacks and rainbow table attacks.
                    Algorithm Type: bcrypt is a password hashing function designed specifically for secure password storage.
            Purpose: To securely hash and store passwords with resistance to brute-force attacks.
            Security:
        Strong- bcrypt includes a salt to protect against rainbow table attacks.
        Adaptive Hashing: The hashing process can be made slower (more computationally expensive) to enhance security over time.
        Built-in Salt: Automatically generates a salt for each password, making it unique even if the same password is used by multiple users.
                    */


            // Hash the password
            $donpassword_hashed = password_hash($donpassword, PASSWORD_BCRYPT);

            // Insert into donors table
            $sql_donor = "INSERT INTO donors (donorid, first_name, last_name, donemail, phone, address, dob, gender, donpassword)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_donor = $database->prepare($sql_donor);
            $stmt_donor->bind_param("sssssssss", $donorid, $first_name, $last_name, $donemail, $phone, $address, $dob, $gender, $donpassword_hashed);
            $stmt_donor->execute();

            // Insert into users table
            $sql_user = "INSERT INTO users (email, usertype) VALUES (?, 'd')";
            $stmt_user = $database->prepare($sql_user);
            $stmt_user->bind_param("s", $donemail);
            $stmt_user->execute();

            // Commit the transaction
            $database->commit();

            // Close the statements
            $stmt_donor->close();
            $stmt_user->close();

            // Set session variables
            $_SESSION['user'] = $donorid; // Store donor ID in session
            $_SESSION['usertype'] = 'd';

            // Redirect to donor_dashboard.php
            header("Location: login.php");
            exit();

        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            $database->rollback();
            echo "Error: " . $e->getMessage();
        }
    }

    $database->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Charity Management</title>
    <link rel="stylesheet" href="signup.css">
    <script src="https://kit.fontawesome.com/03050c7944.js" crossorigin="anonymous"></script>
    
    <link rel="icon" href="logo.png" type="image/x-icon"> <!-- Favicon link -->

    <script>
        function validateForm() {
            // Reset error messages
            document.getElementById('first_name_error').textContent = '';
            document.getElementById('last_name_error').textContent = '';
            document.getElementById('phone_error').textContent = '';
            document.getElementById('dob_error').textContent = '';
            document.getElementById('password_error').textContent = '';

            // Validate First Name and Last Name (letters only)
            var firstName = document.forms["signupForm"]["first_name"].value;
            var lastName = document.forms["signupForm"]["last_name"].value;
            var nameRegex = /^[a-zA-Z]+$/;
            if (!nameRegex.test(firstName)) {
                document.getElementById('first_name_error').textContent = 'Please enter a valid first name (letters only).';
                return false;
            }
            if (!nameRegex.test(lastName)) {
                document.getElementById('last_name_error').textContent = 'Please enter a valid last name (letters only).';
                return false;
            }

            // Validate Phone Number (Kenyan format, 10 digits only)
            var phone = document.forms["signupForm"]["phone"].value;
            var localPhoneRegex = /^(07|01|02)[0-9]{8}$/;
            var internationalPhoneRegex = /^254[0-9]{9}$/;
            
            //var phoneRegex = /^[0-9]{10}$/;
            if (!localPhoneRegex.test(phone) && !internationalPhoneRegex.test(phone)) {
                document.getElementById('phone_error').textContent = 'Please enter a valid Phone number ie(0712345678) Digits Only.';
                return false;
            }

            // Validate Date of Birth (at least 14 years ago and not future date)
            var dob = new Date(document.forms["signupForm"]["dob"].value);
            var today = new Date();
            var minAge = 18;
            if (dob >= today || dob.getFullYear() > today.getFullYear() - minAge) {
                document.getElementById('dob_error').textContent = 'Please enter a valid date of birth (at least 18 years old).';
                return false;
            }

            // Validate Password (minimum 8 characters, 1 digit, 1 capital letter, 1 special character)
            var password = document.forms["signupForm"]["password"].value;
            var passwordRegex = /^(?=.*\d)(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$/;
            if (!passwordRegex.test(password)) {
                document.getElementById('password_error').textContent = 'Password must be at least 8 characters with at least 1 digit, 1 capital letter, and 1 special character.';
                return false;
            }

            return true; // Form submission allowed
        }
    </script>
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
            <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
         </ul>
        </nav>
    </div>
</header>

<section class="signup-section">
    <div class="container">
        <div class="signup-form">
            <h2>Sign Up</h2>
            <form action="" method="post" name="signupForm" onsubmit="return validateForm()">
                <div class="form-group">
                 
                    <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required>
                    <span id="first_name_error" class="error"></span>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required>
                    <span id="last_name_error" class="error"></span>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    <?php if ($email_error): ?>
                        <p class="error"><?php echo $email_error; ?></p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" required>
                    <span id="address_error" class="error"></span>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="tel" id="phone" name="phone" required>
                    <span id="phone_error" class="error"></span>
                    <?php if ($phone_error): ?>
                        <p class="error"><?php echo $phone_error; ?></p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" required>
                    <span id="dob_error" class="error"></span>
                    <?php if ($dob_error): ?>
                        <p class="error"><?php echo $dob_error; ?></p>
                    <?php endif; ?>
                </div>
                <div class="form-group radio-group">
                    <label>Gender:</label>
                    <label for="male">
                        <input type="radio" id="male" name="gender" value="male" required> Male
                    </label>
                    <label for="female">
                        <input type="radio" id="female" name="gender" value="female" required> Female
                    </label>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <span id="password_error" class="error"></span>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirm Password:</label>
                    <input type="password" id="confirm-password" name="confirm-password" required>
                </div>
                <div class="form-group">
                    <?php if ($password_error): ?>
                        <p class="error"><?php echo $password_error; ?></p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <button type="submit">Sign Up</button>
                </div>
            </form>
            <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</section>

<footer>
    <div class="container">
        <p> 2024 GIving Sphere Charity. Where Compassion Meets Action.</p>
    </div>
</footer>
</body>
</html>

