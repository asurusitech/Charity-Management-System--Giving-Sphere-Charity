<?php
session_start();
include 'connection.php';

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - Giving Sphere Charity</title>
    <link rel="stylesheet" href="events.css">   
    <link rel="stylesheet" href="events1.css">
    <link rel="stylesheet" href="iconcount.css">
    <script src="https://kit.fontawesome.com/03050c7944.js" crossorigin="anonymous"></script>
    <link rel="icon" href="logo.png" type="image/x-icon">
    <style>
      
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

        function toggleNotifications() {
            var notificationContent = document.getElementById("notification-content");
            if (notificationContent.style.display === "block") {
                notificationContent.style.display = "none";
            } else {
                notificationContent.style.display = "block";
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

    <div class="admin-container">
        <aside class="sidebar">
            <ul>
                
                <li><a href="donor_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="donor_donations.php"><i class="fas fa-hand-holding-usd"></i> My Donations</a></li>
                <li><a href="donor_profile.php"><i class="fas fa-user-cog"></i> Update Profile</a></li>
                <li class="active"><a href="donor_events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                <li ><a href="donor_notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>

                                
            </ul>
        </aside>
        <main class="main-content">
            <div class="header-actions">
                <h2>Events (<?php echo htmlspecialchars($organized_events); ?>)</h2>
            </div>
            <div class="user-table">
                <table>
                    <thead>
                        <tr>
                            <th>Event ID</th>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($events) > 0): ?>
                            <?php foreach ($events as $event): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($event['event_id']); ?></td>
                                    <td><?php echo htmlspecialchars($event['event_name']); ?></td>
                                    <td><?php echo htmlspecialchars($event['event_date']); ?></td>
                                    <td><?php echo htmlspecialchars($event['event_location']); ?></td>
                                    <td><?php echo htmlspecialchars($event['participation_status'] ?? 'Not Participated'); ?></td>
                                    <td class="action-buttons">
                                        <?php if (!isset($event['participation_status'])): ?>
                                            <form action="participate.php" method="POST">
                                                <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                                                <button type="submit" class="update-btn">Participate</button>
                                            </form>
                                        <?php else: ?>
                                            <span>Requested</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No events found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
