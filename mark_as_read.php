<?php
session_start();
include 'connection.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['notification_id']) && is_numeric($_GET['notification_id'])) {
    $notification_id = intval($_GET['notification_id']);
    $donorid = $_SESSION['user'];

    // Mark the notification as read
    $query = $database->prepare("UPDATE notifications SET status = 'read' WHERE notification_id = ? AND donor_id = ?");
    $query->bind_param("ii", $notification_id, $donorid);
    $query->execute();

    // Check for errors
    if ($database->error) {
        echo json_encode(['error' => "Error updating notification status: " . $database->error]);
        exit();
    }

    // Fetch the full message
    $query = $database->prepare("SELECT message FROM notifications WHERE notification_id = ? AND donor_id = ?");
    $query->bind_param("ii", $notification_id, $donorid);
    $query->execute();
    $result = $query->get_result();
    $message = '';
    if ($result && $row = $result->fetch_assoc()) {
        $message = $row['message'];
    } else {
        echo json_encode(['error' => "Error fetching notification message: " . $database->error]);
        exit();
    }

    // Fetch existing replies if any
    $query = $database->prepare("SELECT reply_id, reply_message, created_at FROM replies WHERE notification_id = ?");
    $query->bind_param("i", $notification_id);
    $query->execute();
    $result = $query->get_result();
    $replies = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $replies[] = $row;
        }
    } else {
        echo json_encode(['error' => "Error fetching replies: " . $database->error]);
        exit();
    }
} else {
    echo json_encode(['error' => "Invalid notification ID."]);
    exit();
}

// Fetch the logged-in user's name
$query = $database->prepare("SELECT first_name, last_name FROM donors WHERE donorid = ?");
$query->bind_param("i", $donorid);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
$first_name = $user['first_name'];
$last_name = $user['last_name'];

// Fetch unread notifications count
$query = $database->prepare("SELECT COUNT(*) AS unread_count FROM notifications WHERE donor_id = ? AND status = 'unread'");
$query->bind_param("i", $donorid);
$query->execute();
$result = $query->get_result();
if ($result && $row = $result->fetch_assoc()) {
    $unread_count = $row['unread_count'];
} else {
    echo "Error fetching unread notifications count: " . $database->error;
    exit();
}

// Handle new reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message'])) {
    $reply_message = trim($_POST['reply_message']);
    if (!empty($reply_message)) {
        $query = $database->prepare("INSERT INTO replies (notification_id, donor_id, reply_message, created_at) VALUES (?, ?, ?, NOW())");
        $query->bind_param("iis", $notification_id, $donorid, $reply_message);
        $query->execute();

        if ($database->error) {
            echo json_encode(['error' => "Error inserting reply: " . $database->error]);
            exit();
        }

        // Redirect to the same page to show the new reply
        header("Location: donor_notifications.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Giving Sphere Charity</title>
    <link rel="stylesheet" href="events.css">
    <link rel="stylesheet" href="mark_as_read.css">
    <link rel="stylesheet" href="iconcount.css">
    <script src="https://kit.fontawesome.com/03050c7944.js" crossorigin="anonymous"></script>
    <link rel="icon" href="logo.png" type="image/x-icon">
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
            <div class="container">
                <div class="notification-content">
                    <h2>Message</h2>
                    <p><?php echo htmlspecialchars($message); ?></p>
                </div>

                <div class="reply-form">
                    <h2>Reply</h2>
                    <form method="POST" action="adminreply.php">
                        <textarea name="reply_message" placeholder="Type your reply here..." required></textarea>
                        <button type="submit">Send Reply</button>
                    </form>
                </div>
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
