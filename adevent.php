<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

// Check if a request was processed
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    $action = isset($_POST['approve']) ? 'Approved' : (isset($_POST['deny']) ? 'Denied' : '');

    if ($action) {
        $stmt = $database->prepare("UPDATE participation_requests SET status = ? WHERE request_id = ?");
        $stmt->bind_param('si', $action, $request_id);
        $stmt->execute();
        $stmt->close();

        // Redirect to admin_events.php after processing
        header("Location: admin_events.php");
        exit();
    }
}

// Fetch participation requests
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
    <title>Participation Requests - Giving Sphere Charity</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="iconcount.css">
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

        .user-table table .action-buttons form button {
            margin-right: 10px;
            color: #FFF;
            padding: 8px 12px;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .user-table table .action-buttons form .approve-btn {
            background-color: #4CAF50; /* Green */
        }

        .user-table table .action-buttons form .deny-btn {
            background-color: #f44336; /* Red */
        }

        /* Button Styling */
        .add-donor-btn {
            background-color: #4CAF50; /* Green */
            color: #FFF;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .add-donor-btn:hover {
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
    <li ><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li><a href="admin_users.php"><i class="fas fa-users"></i> Donors</a></li>
    <li><a href="admin_beneficiaries.php"><i class="fas fa-hand-holding-heart"></i> Beneficiaries</a></li>
    <li><a href="viewdonations.php"><i class="fas fa-donate"></i> Donations</a></li>
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
                <h2>Participation Requests</h2>
            </div>
            <div class="user-table">
                <table>
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Donor Name</th>
                            <th>Event Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($participation_requests) > 0): ?>
                            <?php foreach ($participation_requests as $request): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($request['request_id']); ?></td>
                                    <td><?php echo htmlspecialchars($request['first_name']); ?></td>
                                    <td><?php echo htmlspecialchars($request['event_name']); ?></td>
                                    <td><?php echo htmlspecialchars($request['status']); ?></td>
                                    <td class="action-buttons">
                                        <form action="process_request.php" method="POST">
                                            <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                            <button type="submit" name="approve" value="1" class="approve-btn">Approve</button>
                                            <button type="submit" name="deny" value="1" class="deny-btn">Deny</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No participation requests found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
