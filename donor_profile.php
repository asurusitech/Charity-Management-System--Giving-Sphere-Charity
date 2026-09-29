<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

$email_error = $phone_error = $update_message = "";

if (!isset($_SESSION['user']) || $_SESSION['usertype'] != 'd') {
    header('Location: login.php');
    exit();
}

$donor_id = $_SESSION['user'];

// Fetch the donor's details
$query = $database->prepare("SELECT first_name, last_name, donemail, phone, address, dob, gender FROM donors WHERE donorid = ?");
$query->bind_param("s", $donor_id);
$query->execute();
$result = $query->get_result();

if ($result && $result->num_rows == 1) {
    $donor = $result->fetch_assoc();
    $first_name = $donor['first_name'];
} else {
    echo "Error fetching donor details: " . $database->error;
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $donemail = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $donpassword = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check for duplicate email
    $sql_check_email = "SELECT * FROM donors WHERE donemail = ? AND donorid != ?";
    $stmt_check_email = $database->prepare($sql_check_email);
    $stmt_check_email->bind_param("ss", $donemail, $donor_id);
    $stmt_check_email->execute();
    $stmt_check_email->store_result();

    if ($stmt_check_email->num_rows > 0) {
        $email_error = "Email is already taken.";
    } else {
        // Check for duplicate phone
        $sql_check_phone = "SELECT * FROM donors WHERE phone = ? AND donorid != ?";
        $stmt_check_phone = $database->prepare($sql_check_phone);
        $stmt_check_phone->bind_param("ss", $phone, $donor_id);
        $stmt_check_phone->execute();
        $stmt_check_phone->store_result();

        if ($stmt_check_phone->num_rows > 0) {
            $phone_error = "Phone number is already taken.";
        } else {
            // Begin a transaction
            $database->begin_transaction();

            try {
                // Update donor details in the database
                $sql_update = "UPDATE donors SET first_name = ?, last_name = ?, donemail = ?, phone = ?, address = ?, dob = ?, gender = ?, donpassword = ? WHERE donorid = ?";
                $stmt_update = $database->prepare($sql_update);
                $stmt_update->bind_param("sssssssss", $first_name, $last_name, $donemail, $phone, $address, $dob, $gender, $donpassword, $donor_id);
                $stmt_update->execute();

                // Commit the transaction
                $database->commit();

                // Close the statements
                $stmt_update->close();

                $update_message = "Profile updated successfully!";
            } catch (Exception $e) {
                // Rollback the transaction in case of an error
                $database->rollback();
                $update_message = "Error: " . $e->getMessage();
            }
        }

        $stmt_check_phone->close();
    }

    $stmt_check_email->close();
}

// Fetch unread notifications count
$query = $database->prepare("
    SELECT COUNT(*) AS unread_count 
    FROM notifications 
    WHERE donor_id = ? AND status = 'unread'
");
$query->bind_param("i", $donorid);
$query->execute();
$result = $query->get_result();
if ($result && $row = $result->fetch_assoc()) {
    $unread_count = $row['unread_count'];
} else {
    echo "Error fetching unread notifications count: " . $database->error;
    exit();
}

// Fetch notifications
$query = $database->prepare("
    SELECT message, created_at 
    FROM notifications 
    WHERE donor_id = ? 
    ORDER BY created_at DESC
");
$query->bind_param("i", $donorid);
$query->execute();
$result = $query->get_result();

$notifications = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
} else {
    echo "Error fetching notifications: " . $database->error;
    exit();
}
// Fetch unread notifications count
$query = $database->prepare("
    SELECT COUNT(*) AS unread_count 
    FROM notifications 
    WHERE donor_id = ? AND status = 'unread'
");
$query->bind_param("i", $donorid);
$query->execute();
$result = $query->get_result();
if ($result && $row = $result->fetch_assoc()) {
    $unread_count = $row['unread_count'];
} else {
    echo "Error fetching unread notifications count: " . $database->error;
    exit();
}

// Count of unread notifications
$notification_count = count($notifications);

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$organized_events = 0;
$donorid = $_SESSION['user'];  

// Fetch donor's first name
$first_name = 'User';
$query = $database->prepare("SELECT first_name FROM donors WHERE donorid = ?");
$query->bind_param("i", $donorid);
$query->execute();
$result = $query->get_result();
if ($result && $row = $result->fetch_assoc()) {
    $first_name = $row['first_name'];
}

// Fetch total number of events
$result = $database->query("SELECT COUNT(*) AS count FROM events");
if ($result) {
    $row = $result->fetch_assoc();
    $organized_events = $row['count'];
}

// Fetch the events details 
$query = $database->prepare("
    SELECT e.*, pr.status AS participation_status
    FROM events e
    LEFT JOIN participation_requests pr ON e.event_id = pr.event_id AND pr.donorid = ?
");
$query->bind_param("i", $donorid);
$query->execute();
$result = $query->get_result();

$events = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
} else {
    echo "Error fetching events: " . $database->error;
    exit();
}

// Fetch notifications
$query = $database->prepare("
    SELECT message, created_at 
    FROM notifications 
    WHERE donor_id = ? 
    ORDER BY created_at DESC
");
$query->bind_param("i", $donorid);
$query->execute();
$result = $query->get_result();

$notifications = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
} else {
    echo "Error fetching notifications: " . $database->error;
    exit();
}
// Fetch unread notifications count
$query = $database->prepare("
    SELECT COUNT(*) AS unread_count 
    FROM notifications 
    WHERE donor_id = ? AND status = 'unread'
");
$query->bind_param("i", $donorid);
$query->execute();
$result = $query->get_result();
if ($result && $row = $result->fetch_assoc()) {
    $unread_count = $row['unread_count'];
} else {
    echo "Error fetching unread notifications count: " . $database->error;
    exit();
}

// Count of unread notifications
$notification_count = count($notifications);

$database->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile - Giving Sphere Charity</title>
    <link rel="stylesheet" href="donor11.css">
    <link rel="stylesheet" href="events1.css">
    <link rel="stylesheet" href="iconcount.css">
 
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <script src="https://kit.fontawesome.com/03050c7944.js" crossorigin="anonymous"></script>
    
    <script>
        function toggleLogout() {
            var logoutLink = document.getElementById("logout-link");
            if (logoutLink.style.display === "block") {
                logoutLink.style.display = "none";
            } else {
                logoutLink.style.display = "block";
            }
        }

        function toggleNotifications() {
            var notificationContent = document.getElementById("notification-content");
            if (notificationContent.style.display === "block") {
                notificationContent.style.display = "none";
            } else {
                notificationContent.style.display = "block";
            }
        }
    </script>

    <style>
        .user-info {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            position: relative;
        }
        .user-info i {
            margin-right: 10px; /* Space between icon and username */
        }
        .logout {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            margin-top: 5px;
        }
        .user-info:hover .logout {
            display: block;
        }
        .user-info {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            position: relative;
        }
        .user-info span {
            margin-right: 15px; /* Adjust spacing as needed */
        }
        .user-info i {
            margin-right: 10px; /* Space between icon and username */
            position: relative;
        }
        .notification-icon {
            position: relative;
            cursor: pointer;
            margin-right: 10px; /* Space between icon and username */
        }
        
        .logout {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            margin-top: 5px;
        }
        .user-info:hover .logout {
            display: block;
        }
        .update-btn {
            background-color: #4CAF50;
        }
        .action-buttons a {
            margin-right: 10px;
            color: #FFF;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 4px;
        }
        .action-buttons .update-btn {
            background-color: #4CAF50; /* Green */
            margin-right: 10px;
            color: #FFF;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 4px;
            border: #4CAF50;
        }
        table .action-buttons .delete-btn {
            background-color: #f44336; /* Red */
        }
        .full-notification {
            display: none;
        }
    </style>
    <script>
        function toggleLogout() {
            var logoutLink = document.getElementById("logout-link");
            if (logoutLink.style.display === "block") {
                logoutLink.style.display = "none";
            } else {
                logoutLink.style.display = "block";
            }
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
                <li class="user-info" onclick="toggleLogout()">
                    <i class="fas fa-user"></i>
                    <span class="name"><?php echo htmlspecialchars($first_name ?? 'User'); ?></span>
                    <span class="notification-icon" onclick="window.location.href='donor_notifications.php'">
                    <i class="fas fa-bell"></i>
                        <?php if ($unread_count > 0): ?>
                            <span id="notification-count" class="notification-count"><?php echo htmlspecialchars($unread_count); ?></span>
                        <?php endif; ?>
                    </span>
                    <span id="logout-link" class="logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></span>
                </li>
            </ul>
            </nav>

        </div>
    </header>

    <div class="donor-container">
        <aside class="sidebar">
            <ul>
            <li><a href="donor_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="donor_donations.php"><i class="fas fa-hand-holding-usd"></i> My Donations</a></li>
                <li class="active"><a href="donor_profile.php"><i class="fas fa-user-cog"></i> Update Profile</a></li>
                <li><a href="donor_events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                <li><a href="donor_notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
                
            </ul>
        </aside>
        <main class="main-content">
            <h2>Update Profile</h2>
            <!-- Profile Update Form Section -->
            <?php if ($update_message): ?>
                <p><?php echo $update_message; ?></p>
            <?php endif; ?>
            <form action="donor_profile.php" method="post">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($donor['first_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($donor['last_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($donor['donemail']); ?>" required>
                    <?php if ($email_error): ?>
                        <p class="error"><?php echo $email_error; ?></p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($donor['phone']); ?>" required>
                    <?php if ($phone_error): ?>
                        <p class="error"><?php echo $phone_error; ?></p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($donor['address']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($donor['dob']); ?>" required>
                </div>
                <div class="form-group radio-group">
                    <label>Gender:</label>
                    <label for="male">
                        <input type="radio" id="male" name="gender" value="male" <?php if ($donor['gender'] == 'male') echo 'checked'; ?> required> Male
                    </label>
                    <label for="female">
                        <input type="radio" id="female" name="gender" value="female" <?php if ($donor['gender'] == 'female') echo 'checked'; ?> required> Female
                    </label>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <button type="submit">Update Profile</button>
                </div>
            </form>
        </main>
    </div>

    <footer>
        <div class="container">
            <p>2024 Giving Sphere Charity - Where Compassion Meets Action.</p>
        </div>
    </footer>
</body>
</html>
