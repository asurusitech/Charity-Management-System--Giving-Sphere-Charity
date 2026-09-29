<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

$organized_events = 0;

// Fetch total number of events
$result = $database->query("SELECT COUNT(*) AS count FROM events");
if ($result) {
    $row = $result->fetch_assoc();
    $organized_events = $row['count'];
}

// Fetch the events details
$query = $database->prepare("SELECT * FROM events");
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
$query = "SELECT pr.request_id, pr.donorid, pr.event_id, d.first_name, e.event_name, pr.status
          FROM participation_requests pr
          INNER JOIN donors d ON pr.donorid = d.donorid
          INNER JOIN events e ON pr.event_id = e.event_id";
$result = $database->query($query);

$participation_requests = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $participation_requests[] = $row;
    }
} else {
    echo "Error fetching participation requests: " . $database->error;
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
    <title>Events - Giving Sphere Charity</title>
    <link rel="stylesheet" href="admin.css">
     <!-- Font Awesome for icons -->
     <script src="https://kit.fontawesome.com/03050c7944.js" crossorigin="anonymous"></script>
     <link rel="icon" href="logo.png" type="image/x-icon">
    <style>
        /* Table Styling */
        .user-table table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
        }

        .user-table table th,
        .user-table table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .user-table table th {
            background-color: #1A1817; /* Eerie black */
            color: #FDF6EC; /* Off-white */
        }

        .user-table table tbody tr:nth-child(odd) {
            background-color: #F3E9DD; /* Light cream */
        }

        .user-table table tbody tr:nth-child(even) {
            background-color: #FDF6EC; /* Off-white */
        }

        .user-table table .action-buttons a {
            margin-right: 10px;
            color: #FFF;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 4px;
        }

        .user-table table .action-buttons .update-btn {
            background-color: #4CAF50; /* Green */
        }

        .user-table table .action-buttons .delete-btn {
            background-color: #f44336; /* Red */
        }

        /* Button Styling */
        .add-event-btn {
            background-color: #4CAF50; /* Green */
            color: #FFF;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .add-event-btn:hover {
            background-color: #45a049; /* Darker green */
        }

        /* Aligning the button to the right of the title */
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header-actions h2 {
            margin: 0;
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

    </style>
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
            <li  ><a href="viewdonations.php"><i class="fas fa-donate"></i> Donations</a></li>
            <li class="active"><a href="admin_events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
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
            <div class="header-actions">
                <h2>Events (<?php echo htmlspecialchars($organized_events); ?>)</h2>
                <a href="add_event.php" class="add-event-btn">Add Event</a>
            </div>
            <div class="user-table">
                <table>
                    <thead>
                        <tr>
                            <th>Event ID</th>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Action</th>
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
                                    <td class="action-buttons">
                                        <a href="update_event.php?id=<?php echo $event['event_id']; ?>" class="update-btn">Update</a>
                                    </td>
                                    <td class="action-buttons">
                                        <a href="delete_event.php?id=<?php echo $event['event_id']; ?>" class="delete-btn">Delete</a>
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
            <div class="header-actions">
                <h2>Event Participants </h2>
                <a href="adevent.php" class="add-event-btn">Participation Requests</a>
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
