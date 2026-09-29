<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

// Check if beneficiary ID is provided via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Beneficiary ID not provided.";
    exit();
}

// Fetch beneficiary details based on ID
$beneficiary_id = $_GET['id'];
$query = $database->prepare("SELECT * FROM beneficiaries WHERE beneficiary_id = ?");
$query->bind_param("i", $beneficiary_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo "Beneficiary not found.";
    exit();
}

$beneficiary = $result->fetch_assoc();

// Handle form submission for updating beneficiary
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];

    // Update beneficiary details in the database
    $update_query = $database->prepare("UPDATE beneficiaries SET first_name=?, last_name=?, email=?, phone_number=?, address=?, updated_at=current_timestamp() WHERE beneficiary_id=?");
    $update_query->bind_param("sssssi", $first_name, $last_name, $email, $phone_number, $address, $beneficiary_id);

    if ($update_query->execute()) {
        // Redirect to beneficiaries list after successful update
        header("Location: admin_beneficiaries.php");
        exit();
    } else {
        echo "Error updating beneficiary: " . $database->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Beneficiary - Giving Sphere Charity</title>
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
            font-weight: bold;
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
            font-size: 16px;
        }

        .update-form input[type="submit"] {
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

        .update-form input[type="submit"]:hover {
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
                <li ><a href="admin_users.php">Donors</a></li>
                <li class="active"><a href="admin_beneficiaries.php">Beneficiaries</a></li>
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
                <h2>Update Beneficiary</h2>
                <form method="POST" action="">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($beneficiary['first_name']); ?>" required>

                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($beneficiary['last_name']); ?>" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($beneficiary['email']); ?>" required>

                    <label for="phone">Phone:</label>
                    <input type="tel" id="phone" name="phone_number" value="<?php echo htmlspecialchars($beneficiary['phone_number']); ?>" required>

                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($beneficiary['address']); ?>" required>

                    
                    <input type="submit" value="Update Beneficiary">
                </form>
            </div>
        </main>
    </div>

    <footer>
        <div class="container">
        <p><?php echo date('Y'); ?> Giving Sphere Charity - Where Compassion Meets Action.</p>
        <a href="https://wa.me/254707962238" class="fa fa-whatsapp" style="font-size: 28px; text-decoration: none;"></a>
        </div>
    </footer>
</body>
</html>
