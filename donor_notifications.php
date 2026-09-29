<?php
session_start();
include 'connection.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$query = $database->prepare("
    SELECT notification_id, message, created_at 
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Giving Sphere Charity</title>
    <link rel="stylesheet" href="events.css">
    <link rel="stylesheet" href="iconcount.css">
    <script src="https://kit.fontawesome.com/03050c7944.js" crossorigin="anonymous"></script>
    <link rel="icon" href="logo.png" type="image/x-icon">
    <style>
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

        function viewNotification(id) {
            var fullNotification = document.getElementById('full-notification-' + id);
            if (fullNotification.style.display === "block") {
                fullNotification.style.display = "none";
            } else {
                fullNotification.style.display = "block";
            }
        }

        function refreshNotificationCount() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch_notification_count.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('notification-count').innerText = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Call this function after the page has loaded
        document.addEventListener('DOMContentLoaded', refreshNotificationCount);
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
                <li><a href="donor_events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                <li class="active"><a href="donor_notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <div class="header-actions">
                <h2>Notifications (<?php echo htmlspecialchars(count($notifications)); ?>)</h2>
            </div>
            <div class="user-table">
                <table>
                    <thead>
                        <tr>
                            <th>Notification ID</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($notifications) > 0): ?>
                            <?php foreach ($notifications as $notification): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($notification['notification_id']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($notification['message'], 0, 50)) . '...'; ?></td>
                                    <td><?php echo htmlspecialchars($notification['created_at']); ?></td>
                                    <td class="action-buttons">
                                        <a href="mark_as_read.php?notification_id=<?php echo $notification['notification_id']; ?>" class="update-btn">View</a>
                                    </td>
                                </tr>
                                <tr id="full-notification-<?php echo $notification['notification_id']; ?>" class="full-notification">
                                    <td colspan="4"><?php echo htmlspecialchars($notification['message']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No notifications found.</td>
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
