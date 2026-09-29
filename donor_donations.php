<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

if (!isset($_SESSION['user']) || $_SESSION['usertype'] != 'd') {
    header('Location: login.php');
    exit();
}

$donor_id = $_SESSION['user'];

// Fetch the donor's details
$query = $database->prepare("SELECT first_name FROM donors WHERE donorid = ?");
$query->bind_param("i", $donor_id);
$query->execute();
$result = $query->get_result();

if ($result && $result->num_rows == 1) {
    $donor = $result->fetch_assoc();
    $first_name = $donor['first_name'];
} else {
    echo "Error fetching donor details: " . $database->error;
    exit();
}

// Fetch the donor's donations with payment status from transactions
$donations = [];
$query = $database->prepare("
    SELECT d.donation_id, d.amount, d.donation_date, d.payment_status, t.transaction_status 
    FROM donations d
    LEFT JOIN transactions t ON d.transaction_id = t.transaction_id
    WHERE d.donorid = ?
    ORDER BY d.donation_date DESC
");
$query->bind_param("i", $donor_id);
$query->execute();
$result = $query->get_result();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $donations[] = $row;
    }
} else {
    echo "Error fetching donations: " . $database->error;
    exit();
}

// Fetch unread notifications count
$query = $database->prepare("
    SELECT COUNT(*) AS unread_count 
    FROM notifications 
    WHERE donor_id = ? AND status = 'unread'
");
$query->bind_param("i", $donor_id);
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
    <title>My Donations - Giving Sphere Charity</title>
    <link rel="stylesheet" href="donor11.css">
    <link rel="stylesheet" href="donation.css">
    <link rel="stylesheet" href="iconcount.css">
    
    <link rel="icon" href="logo.png" type="image/x-icon">
    <script src="https://kit.fontawesome.com/03050c7944.js" crossorigin="anonymous"></script>
    <style>
        /* donor11.css */
       
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
                <li class="active"><a href="donor_donations.php"><i class="fas fa-hand-holding-usd"></i> My Donations</a></li>
                <li><a href="donor_profile.php"><i class="fas fa-user-cog"></i> Update Profile</a></li>
                <li><a href="donor_events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                <li><a href="donor_notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h2>My Donations</h2>
            <div class="donation-history">
                <?php if (count($donations) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Amount (Ksh)</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donations as $donation): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($donation['amount']); ?></td>
                                    <td><?php echo htmlspecialchars($donation['donation_date']); ?></td>
                                    <td class="<?php 
                                    if ($donation['transaction_status'] == 'Completed') {
                                        echo 'status-completed';
                                    } elseif ($donation['transaction_status'] == 'Pending') {
                                        echo 'status-pending';
                                    } elseif ($donation['transaction_status'] == 'Canceled') {
                                        echo 'status-canceled';
                                    }
                                    ?>"><?php echo htmlspecialchars($donation['transaction_status'] ?? $donation['payment_status']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No donations found.</p>
                <?php endif; ?>
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
