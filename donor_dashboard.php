<?php
session_start();

include("connection.php");

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit; 
}

$donor_id = $_SESSION['user'];

// Prepare and execute the query to prevent SQL injection
$query = $database->prepare("SELECT * FROM donors WHERE donorid=?");
$query->bind_param("i", $donor_id); // Changed "s" to "i" for integer binding
$query->execute();
$result = $query->get_result();

if ($result && $result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $first_name = $row['first_name'];
} else {
    $first_name = "Unknown"; // Default value or handle the error as appropriate
    $donor_id = null;
    echo "Error fetching donor details: " . $database->error;
}

// Fetching total donations
$total_donations = 0;
if ($donor_id) {
    $total_donations_result = $database->query("SELECT SUM(amount) AS total_amount FROM donations WHERE donorid='$donor_id'");
    if ($total_donations_result) {
        $total_donations_row = $total_donations_result->fetch_assoc();
        $total_donations = $total_donations_row['total_amount'] ?? 0;
    }
}

// Fetching recent donations
$recent_donations = [];
if ($donor_id) {
    $recent_donations_result = $database->query("SELECT amount, donation_date FROM donations WHERE donorid='$donor_id' ORDER BY donation_date DESC LIMIT 5");
    if ($recent_donations_result) {
        while ($recent_donation = $recent_donations_result->fetch_assoc()) {
            $recent_donations[] = $recent_donation;
        }
    } else {
        echo "Error fetching recent donations: " . $database->error;
    }
}

// Fetching upcoming events
$events = [];
$events_result = $database->query("SELECT * FROM events ORDER BY event_date ASC LIMIT 3");
if ($events_result) {
    while ($event = $events_result->fetch_assoc()) {
        $events[] = $event;
    }
} else {
    echo "Error fetching events: " . $database->error;
}

// Fetch unread notifications count
$query = $database->prepare("
    SELECT COUNT(*) AS unread_count 
    FROM notifications 
    WHERE donor_id = ? AND status = 'unread'
");
$query->bind_param("i", $donor_id); // Correctly bind the integer donor_id
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
    <title>Donor Dashboard - Giving Sphere Charity</title>
    <link rel="stylesheet" href="don.css">
    <link rel="stylesheet" href="iconcount.css">
    <link rel="icon" href="logo.png" type="image/x-icon">
    <!-- Font Awesome for icons -->
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
    </script>
    <style>
 
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <div class="logo-title">
                <a href="index.php"><img src="logo.png" alt="Logo" class="logo"></a>
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
                <li class="active"><a href="donor_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="donor_donations.php"><i class="fas fa-hand-holding-usd"></i> My Donations</a></li>
                <li><a href="donor_profile.php"><i class="fas fa-user-cog"></i> Update Profile</a></li>
                <li><a href="donor_events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                <li><a href="donor_notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h2>Welcome, <?php echo htmlspecialchars(substr($first_name ?? '', 0, 13)); ?></h2>
            <p>Thank you for your generous support! Here you can manage your donations, view your donation history, and update your profile.</p>
            <div class="donor-overview">
                <div class="card">
                    <h3>Total Donations</h3>
                    <p>Ksh <?php echo number_format($total_donations, 2); ?></p>
                </div>
                <div class="card">
                    <h3>Recent Donations</h3>
                    <ol>
                        <?php
                        foreach ($recent_donations as $donation) {
                            echo "<li>Ksh " . number_format($donation['amount'], 2) . " on " . htmlspecialchars($donation['donation_date']) . "</li>";
                        }
                        ?>
                    </ol>
                </div>
                
                <div class="card">
                    <h3>Next Step</h3>
                    <p><a href="reguserdonate.html" class="button">Make a New Donation</a></p>
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
</body>
</html>
