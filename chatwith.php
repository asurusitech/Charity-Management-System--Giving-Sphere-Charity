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
        .chat-container {
    width: 100%;
    height: 100%;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
}

.chat-header {
    padding: 5px;
    background-color: black;
    color: #ffffff;
    text-align: center;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}

.chat-box {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.message {
    max-width: 70%;
    padding: 10px;
    border-radius: 8px;
    background-color: #dcf8c6;
}

.message.sent {
    align-self: flex-end;
    background-color: #dcf8c6;
}

.message.received {
    align-self: flex-start;
    background-color: #ffffff;
    border: 1px solid #e6e6e6;
}

.chat-input-container {
    display: flex;
    padding: 10px;
    border-top: 1px solid #e6e6e6;
}

#chat-input {
    flex: 1;
    padding: 10px;
    border: 1px solid #e6e6e6;
    border-radius: 4px;
    outline: none;
}

#send-button {
    background-color: black;
    color: #ffffff;
    border: none;
    padding: 10px 20px;
    margin-left: 10px;
    border-radius: 4px;
    cursor: pointer;
}

#send-button:hover {
    background-color: #064a43;
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
        
       

<div class="chat-container">
        <div class="chat-header">
            <h2>Chat with Admin</h2>
        </div>
        <div class="chat-box" id="chat-box">
            <!-- Chat messages will be dynamically added here -->
        </div>
        <div class="chat-input-container">
            <input type="text" id="chat-input" placeholder="Reply to admin">
            <button id="send-button">Send</button>
        </div>
    </div>        

    <script >
        document.getElementById('send-button').addEventListener('click', sendMessage);
document.getElementById('chat-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendMessage();
    }
});

function sendMessage() {
    const input = document.getElementById('chat-input');
    const message = input.value.trim();

    if (message === '') return;

    const chatBox = document.getElementById('chat-box');
    const messageElement = document.createElement('div');
    messageElement.classList.add('message', 'sent');
    messageElement.textContent = message;
    chatBox.appendChild(messageElement);

    input.value = '';
    chatBox.scrollTop = chatBox.scrollHeight;

    // Simulate admin reply
    setTimeout(() => {
        const adminMessageElement = document.createElement('div');
        adminMessageElement.classList.add('message', 'received');
        adminMessageElement.textContent = 'This is an auto-reply from admin.';
        chatBox.appendChild(adminMessageElement);
        chatBox.scrollTop = chatBox.scrollHeight;
    }, 1000);
}


    </script>

                        <!---->
       
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
