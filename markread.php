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
        header("Location: mark_as_read.php?notification_id=" . $notification_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Notification</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://kit.fontawesome.com/03050c7944.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        .notification-content {
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .reply-form {
            margin-top: 20px;
        }
        .reply-form textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: none;
        }
        .reply-form button {
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .reply-form button:hover {
            background-color: #45a049;
        }
        .replies {
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .reply {
            border-bottom: 1px solid #eee;
            padding: 10px;
            margin-bottom: 10px;
        }
        .reply .timestamp {
            font-size: 0.9em;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Notification Details</h1>
        <div class="notification-content">
            <h2>Message</h2>
            <p><?php echo htmlspecialchars($message); ?></p>
        </div>

        <div class="reply-form">
            <h2>Reply</h2>
            <form method="POST">
                <textarea name="reply_message" placeholder="Type your reply here..."></textarea>
                <button type="submit">Send Reply</button>
            </form>
        </div>

        <div class="replies">
            <h2>Previous Replies</h2>
            <?php if (count($replies) > 0): ?>
                <?php foreach ($replies as $reply): ?>
                    <div class="reply">
                        <p><?php echo htmlspecialchars($reply['reply_message']); ?></p>
                        <p class="timestamp">Replied on <?php echo htmlspecialchars($reply['created_at']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No replies yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
