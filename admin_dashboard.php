<?php
session_start();
include("connection.php");

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetching statistics from the database
$donors_present = 0;
$total_donations = 0;
$total_amount_donations = 0.0;
$num_employees = 0;
$organized_events = 0;
$num_beneficiaries = 0;
$num_notifications = 0;
$notifications = [];

// Fetch total number of donors
$result = $database->query("SELECT COUNT(*) AS count FROM donors");
if ($result) {
    $row = $result->fetch_assoc();
    $donors_present = $row['count'];
}

// Fetch total number of donations
$result = $database->query("SELECT COUNT(*) AS count FROM donations");
if ($result) {
    $row = $result->fetch_assoc();
    $total_donations = $row['count'];
}

// Fetch total amount of donations
$result = $database->query("SELECT SUM(amount) AS total_amount FROM donations");
if ($result) {
    $row = $result->fetch_assoc();
    $total_amount_donations = $row['total_amount'] ?? 0;
}

// Fetch total number of organized events
$result = $database->query("SELECT COUNT(*) AS count FROM events");
if ($result) {
    $row = $result->fetch_assoc();
    $organized_events = $row['count'];
}

// Fetch total number of beneficiaries
$result = $database->query("SELECT COUNT(*) AS count FROM beneficiaries");
if ($result) {
    $row = $result->fetch_assoc();
    $num_beneficiaries = $row['count'];
}

// Fetch total number of notifications
$result = $database->query("SELECT COUNT(*) AS count FROM notifications");
if ($result) {
    $row = $result->fetch_assoc();
    $num_notifications = $row['count'];
}

// Fetch notifications details
$result = $database->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}

$notification_id = $_GET['notification_id'] ?? 0;
$notification_id = intval($notification_id);

// Fetch unread notifications count
$query = $database->prepare("
    SELECT COUNT(*) AS unread_count 
    FROM notifications 
    WHERE status = 'unread'
");

$query->execute();
$result = $query->get_result();
if ($result && $row = $result->fetch_assoc()) {
    $unread_count = $row['unread_count'];
} else {
    echo "Error fetching unread notifications count: " . $database->error;
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Giving Sphere Charity</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="icon" href="logo.png" type="image/x-icon">
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/03050c7944.js" crossorigin="anonymous"></script>
    
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
        .notification {
            margin-left: 15px; /* Space between username and notification icon */
            position: relative;
            cursor: pointer;
        }
        .notification i {
            font-size: 18px; /* Adjust size as needed */
        }
        .notification-count {
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            position: absolute;
            top: -10px;
            right: -10px;
        }
        .notification-bubble {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 5px;
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            width: 200px;
            z-index: 1000;
        }
        .notification:hover .notification-bubble {
            display: block;
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

        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background: ffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            flex: 1 1 calc(33% - 40px);
            text-align: center;
            min-width: 200px;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card h3 {
            margin-bottom: 15px;
            font-size: 1.5em;
        }
        .card p {
            font-size: 1.2em;
            color: #555;
        }

        /* Table Styling */
        .report-table table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
        }

        .report-table table th,
        .report-table table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .report-table table th {
            background-color: #1A1817; /* Eerie black */
            color: #FDF6EC; /* Off-white */
        }

        .report-table table tbody tr:nth-child(odd) {
            background-color: #F3E9DD; /* Light cream */
        }

        .report-table table tbody tr:nth-child(even) {
            background-color: #FDF6EC; /* Off-white */
        }

        /* Sidebar Styling */
        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            display: block;
            padding: 10px 15px;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .sidebar ul li a:hover {
            background-color: #B06D4Aff; /* Green with transparency */
        }

        .sidebar ul .active a {
            background-color: #B06D4Aff; /* Green with transparency */
            color: #FFF;
        }

        .sidebar .sub-menu {
            display: none;
            padding-left: 20px;
        }

        .sidebar .sub-menu.active {
            display: block;
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
        <span>Admin</span>
        <span id="logout-link" class="logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></span>
    </li>
    <li class="notification">
    <a href="admin_notifications.php" style="text-decoration: none; color: inherit;">
        <i class="fas fa-bell"></i>
        <?php if ($unread_count > 0) { ?>
            <span class="notification-count"><?php echo htmlspecialchars($unread_count); ?></span>
        <?php } ?>
    </a>
        </li>
</ul>
            </nav>
        </div>
    </header>

    <div class="admin-container">
        <aside class="sidebar">
        <ul>
    <li class="active"><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li><a href="admin_users.php"><i class="fas fa-users"></i> Donors</a></li>
    <li><a href="admin_beneficiaries.php"><i class="fas fa-hand-holding-heart"></i> Beneficiaries</a></li>
    <li><a href="viewdonations.php"><i class="fas fa-donate"></i> Donations</a></li>
    <li><a href="admin_events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
    <li><a href="admin_notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
            
    <li>
        <a href="#" class="sub-link-toggle"><i class="fas fa-chart-line"></i> Reports</a>
        <ul class="sub-menu">
            <li><a href="donatedusers.php"><i class="fas fa-user-check"></i> Users who have donated</a></li>
            <li><a href="#"><i class="fas fa-file-alt"></i> Donations Report</a></li>
        </ul>
    </li>
</ul>

        </aside>
        <main class="main-content">
            <h2>Dashboard</h2>
            <p>Welcome to the Admin Dashboard.</p>

            <!-- Cards Section -->
            <div class="card-container">
    <div class="card">
        <h3>Donors Present</h3>
        <p><?php echo htmlspecialchars($donors_present); ?></p>
        <a href="admin_users.php"><i class="fas fa-users" style="color: black;"></i></a>
    </div>
    <div class="card">
        <h3>Total Donations</h3>
        <p><?php echo htmlspecialchars($total_donations); ?></p>
        <a href="viewdonations.php"><i class="fas fa-hand-holding-usd" style="color: black;"></i></a>
    </div>
    <div class="card">
        <h3>Total Amount of Donations</h3>
        <p>Ksh <?php echo htmlspecialchars(number_format($total_amount_donations, 2)); ?></p>
        <i class="fas fa-coins" style="color: black;"></i>
    </div>
    <div class="card">
        <h3>Notifications</h3>
        <p><?php echo htmlspecialchars($num_notifications); ?></p>
        <a href="admin_notifications.php"><i class="fas fa-bell" style="color: black;"></i></a>
    </div>
    <div class="card">
        <h3>Organized Events</h3>
        <p><?php echo htmlspecialchars($organized_events); ?></p>
        <a href="admin_events.php"><i class="fas fa-calendar-alt" style="color: black;"></i></a>
    </div>
    <div class="card">
        <h3>No. of Beneficiaries</h3>
        <p><?php echo htmlspecialchars($num_beneficiaries); ?></p>
        <a href="admin_beneficiaries.php"><i class="fas fa-hands-helping" style="color: black;"></i></a>
    </div>
</div>

        </main>
    </div>

    <footer>
        <div class="container">
            <p>2024 Giving Sphere Charity - Where Compassion Meets Action.</p>
            <a href="https://wa.me/254707962238" class="fa fa-whatsapp" style="font-size: 28px; text-decoration: none;"></a>
        </div>
    </footer>
    <script>
        // Script for handling sidebar sub-menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const subLinkToggle = document.querySelector('.sub-link-toggle');
            const subMenu = document.querySelector('.sub-menu');
            
            subLinkToggle.addEventListener('click', function(event) {
                event.preventDefault();
                subMenu.classList.toggle('active');
            });
        });
    </script>

</body>
</html>
