<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

// Fetch donor details
$donor_id = $_GET['id'];
$query = $database->prepare("SELECT * FROM donors WHERE donorid = ?");
$query->bind_param('i', $donor_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $donor = $result->fetch_assoc();
} else {
    echo "Donor not found.";
    exit();
}

// Update donor details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $donemail = $_POST['donemail'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $donpassword = password_hash($_POST['donpassword'], PASSWORD_DEFAULT);

    $database->begin_transaction();
    try {
        // Update donors table
        $update_donor_query = $database->prepare("UPDATE donors SET first_name = ?, last_name = ?, donemail = ?, phone = ?, address = ?, dob = ?, gender = ?, donpassword = ? WHERE donorid = ?");
        $update_donor_query->bind_param('ssssssssi', $first_name, $last_name, $donemail, $phone, $address, $dob, $gender, $donpassword, $donor_id);
        $update_donor_query->execute();

        // Update users table
        $update_user_query = $database->prepare("UPDATE users SET email = ? WHERE email = ?");
        $update_user_query->bind_param('ss', $donemail, $donor['donemail']);
        $update_user_query->execute();

        $database->commit();
        echo "Donor details updated successfully.";
        header("Location: admin_users.php");
        exit();
    } catch (Exception $e) {
        $database->rollback();
        echo "Error updating details: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Donor - Giving Sphere Charity</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="icon" href="logo.png" type="image/x-icon">
    <style>
        /* Form Styling */
        .update-form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
        }
        

        .update-form h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .update-form label {
            display: block;
            margin-bottom: 8px;
        }

        .update-form input[type="text"],
        .update-form input[type="email"],
        .update-form input[type="tel"],
        .update-form input[type="date"],
        .update-form select,
        .update-form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .update-form button[type="submit"] {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .update-form button[type="submit"]:hover {
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
            <div class="update-form">
                <h2>Update Donor</h2>
                <form method="POST" action="">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($donor['first_name']); ?>" required>

                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($donor['last_name']); ?>" required>

                    <label for="donemail">Email:</label>
                    <input type="email" id="donemail" name="donemail" value="<?php echo htmlspecialchars($donor['donemail']); ?>" required>

                    <label for="phone">Phone:</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($donor['phone']); ?>" required>

                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($donor['address']); ?>" required>

                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($donor['dob']); ?>" required>

                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender" required>
                        <option value="male" <?php if($donor['gender'] == 'male') echo 'selected'; ?>>Male</option>
                        <option value="female" <?php if($donor['gender'] == 'female') echo 'selected'; ?>>Female</option>
                    </select>

                    <label for="donpassword">Password:</label>
                    <input type="password" id="donpassword" name="donpassword" >

                    <button type="submit">Update Donor</button>
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
