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

// Fetch notifications
$query = $database->prepare("
    SELECT notification_id, notification_type, details, created_at 
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

// Fetch the donors' details
$search = $_GET['search'] ?? '';
$search = $database->real_escape_string($search);

$queryStr = "SELECT * FROM donors";
if ($search) {
    $queryStr .= " WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR donemail LIKE '%$search%' OR phone LIKE '%$search%'";
}

$query = $database->prepare($queryStr);
$query->execute();
$result = $query->get_result();

$donors = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $donors[] = $row;
    }
} else {
    echo "Error fetching donors: " . $database->error;
    exit();
}

// Fetch total number of notifications
$result = $database->query("SELECT COUNT(*) AS count FROM adminnotifications");
if ($result) {
    $row = $result->fetch_assoc();
    $num_notifications = $row['count'];
}

// Fetch notifications details
$result = $database->query("SELECT * FROM adminnotifications ORDER BY created_at DESC LIMIT 5");
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Giving Sphere Charity</title>
    <link rel="stylesheet" href="events.css">
    <link rel="stylesheet" href="events1.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="donation.css">
    
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
            <li><a href="#" class="sub-link-toggle"><i class="fas fa-chart-line"></i> Reports</a>
            <li class="active"><a href="admin_notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
            <li><a href="donatedusers.php"><i class="fas fa-user-check"></i> Users who have donated</a></li>
            <li><a href="#"><i class="fas fa-file-alt"></i> Donations Report</a></li>
        </ul>
    </li>
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
                            <th>Type</th>
                            <th>Details</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($notifications) > 0): ?>
                            <?php foreach ($notifications as $notification): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($notification['notification_id']); ?></td>
                                    <td><?php echo htmlspecialchars($notification['notification_type']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($notification['details'], 0, 50)) . '...'; ?></td>
                                    <td><?php echo htmlspecialchars($notification['created_at']); ?></td>
                                    <td class="action-buttons">
                                        <a href="adminread.php?notification_id=<?php echo $notification['notification_id']; ?>" class="update-btn">View</a>
                                    </td>
                                </tr>
                                <tr id="full-notification-<?php echo $notification['notification_id']; ?>" class="full-notification">
                                    <td colspan="5"><?php echo htmlspecialchars($notification['details']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No notifications found.</td>
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
