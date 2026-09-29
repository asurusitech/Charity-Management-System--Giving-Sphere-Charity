<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $insert_query = $database->prepare("INSERT INTO donors (first_name, last_name, donemail, phone, address, dob, gender, donpassword) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $insert_query->bind_param("ssssssss", $first_name, $last_name, $email, $phone, $address, $dob, $gender, $password);

    if ($insert_query->execute()) {
        // Redirect to donors list after successful insertion
        header("Location: admin_users.php");
        exit();
    } else {
        echo "Error adding donor: " . $database->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Donor - Giving Sphere Charity</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="icon" href="logo.png" type="image/x-icon">
    <style>
        /* Form Styling */
        .add-form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
        }

        .add-form h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .add-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .add-form input[type="text"],
        .add-form input[type="email"],
        .add-form input[type="tel"],
        .add-form input[type="date"],
        .add-form select,
        .add-form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .add-form input[type="submit"] {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .add-form input[type="submit"]:hover {
            background-color: #45a049;
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
                    <li><a href="admin.php">Admin</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="admin-container">
        <aside class="sidebar">
        <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li class="active"><a href="admin_users.php">Donors</a></li>
                <li><a href="admin_beneficiaries.php">Beneficiaries</a></li>
                <li><a href="viewdonations.php">Donations</a></li>
                <li><a href="admin_events.php">Events</a></li>
                <li>
                    <a href="#" class="sub-link-toggle">Reports</a>
                    <ul class="sub-menu">
                        <li><a href="donatedusers.php">Users who have donated</a></li>
                        <li><a href="#">Donations Report</a></li>
                        
                    </ul>
                </li>
          
          
            </ul>
        </aside>
        <main class="main-content">
            <div class="add-form">
                <h2>Add Donor</h2>
                <form method="POST" action="">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required>

                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>

                    <label for="phone">Phone:</label>
                    <input type="tel" id="phone" name="phone" required>

                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" required>

                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" required>

                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender" required>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>

                    <input type="submit" value="Add Donor">
                </form>
            </div>
        </main>
    </div>

    <footer>
        <div class="container">
            
        <p>2024 Giving Sphere Charity - Where Compassion Meets Action.</p>
<a href="https://wa.me/254707962238" class="fa fa-whatsapp" style="font-size: 28px; text-decoration: none;"></a>
        </div>
    </footer>
</body>
</html>
