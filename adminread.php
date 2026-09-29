<?php
session_start();
include 'connection.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Default notification ID to 0
$notification_id = $_GET['notification_id'] ?? 0;
$notification_id = intval($notification_id);

// Fetch unread notifications count
$query = $database->prepare("
    SELECT COUNT(*) AS unread_count 
    FROM adminnotifications 
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

// Fetch notification details, donor's first name, and event name
if ($notification_id > 0) {
    $query = $database->prepare("
        SELECT an.notification_id, an.notification_type, an.details, an.created_at, d.first_name, e.event_name
        FROM adminnotifications an
        JOIN donors d ON an.donor_id = d.donorid
        LEFT JOIN participation_requests pr ON an.notification_id = pr.request_id
        LEFT JOIN events e ON pr.event_id = e.event_id
        WHERE an.notification_id = ?
    ");
    $query->bind_param("i", $notification_id);
    $query->execute();
    $result = $query->get_result();
    
    $notification = $result->fetch_assoc();
    
    if ($notification) {
        // Mark notification as read
        $update_query = $database->prepare("
            UPDATE adminnotifications 
            SET status = 'read' 
            WHERE notification_id = ?
        ");
        $update_query->bind_param("i", $notification_id);
        $update_query->execute();
        
        // Fetch replies
        $query = $database->prepare("
            SELECT * FROM replies 
            WHERE notification_id = ? 
            ORDER BY created_at
        ");
        $query->bind_param("i", $notification_id);
        $query->execute();
        $result = $query->get_result();
        
        $replies = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $replies[] = $row;
            }
        }
    } else {
        echo "Notification not found.";
        exit();
    }
} else {
    echo "Invalid notification ID.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Details - Giving Sphere Charity</title>
    <link rel="stylesheet" href="events.css">
    <link rel="stylesheet" href="events1.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="donation.css">
    
    <script src="https://kit.fontawesome.com/03050c7944.js" crossorigin="anonymous"></script>
    <link rel="icon" href="logo.png" type="image/x-icon">
    <style>
        .notification-icon {
            position: relative;
            cursor: pointer;
            margin-right: 10px;
        }
        .notification-count {
            position: absolute;
            top: -10px;
            right: -10px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 3px 7px;
            font-size: 12px;
            font-weight: bold;
        }
        .full-notification {
            display: none;
        }
    </style>
    <script>
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
                <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="admin_users.php"><i class="fas fa-users"></i> Donors</a></li>
                <li><a href="admin_beneficiaries.php"><i class="fas fa-hand-holding-heart"></i> Beneficiaries</a></li>
                <li><a href="viewdonations.php"><i class="fas fa-donate"></i> Donations</a></li>
                <li><a href="admin_events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                <li><a href="#" class="sub-link-toggle"><i class="fas fa-chart-line"></i> Reports</a></li>
                <li class="active"><a href="admin_notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
                <li><a href="donatedusers.php"><i class="fas fa-user-check"></i> Users who have donated</a></li>
                <li><a href="#"><i class="fas fa-file-alt"></i> Donations Report</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <div class="notification-content">
                <h2>Notification Details</h2>
                <p>
                    <?php 
                    $first_name = $notification['first_name'] ?? 'Unknown Donor';
                    $notification_type = $notification['notification_type'] ?? 'Unknown Type';
                    $details = $notification['details'] ?? '';
                    $event_name = $notification['event_name'] ?? '';

                    $first_name = htmlspecialchars($first_name);
                    $notification_type = htmlspecialchars($notification_type);
                    $details = htmlspecialchars($details);
                    $event_name = htmlspecialchars($event_name);
                    
                    if ($notification_type == 'Participation Request') {
                        echo "$first_name has requested to participate in " . ($event_name ? $event_name : 'an event');
                    } elseif ($notification_type == 'Payment') {
                        echo "$first_name has made a payment.";
                    } elseif ($notification_type == 'Reply') {
                        echo "$first_name has replied: $details";
                    } else {
                        echo "Unknown notification type.";
                    }
                    ?>
                </p>
            </div>

            <div class="user-table">
                <table>
                    <thead>
                        <tr>
                            <th>NID</th>
                            <th>Type</th>
                            <th>Details</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo htmlspecialchars($notification['notification_id']); ?></td>
                            <td><?php echo htmlspecialchars($notification['notification_type']); ?></td>
                            <td><?php echo htmlspecialchars($notification['details']); ?></td>
                            <td><?php echo htmlspecialchars($notification['created_at']); ?></td>
                            <td class="action-buttons">
                                <a href="mark.php?notification_id=<?php echo htmlspecialchars($notification['notification_id']); ?>" class="update-btn">Mark as Read</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="reply-form">
                <h2>Reply</h2>
                <form method="POST" action="adminreplytodonor.php">
                    <input type="hidden" name="notification_id" value="<?php echo htmlspecialchars($notification_id); ?>">
                    <textarea name="reply_message" placeholder="Type your reply here..." required></textarea>
                    <button type="submit">Send Reply</button>
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
