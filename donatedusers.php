<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

// Fetch donors who have made donations and their total donation amount
$sql = "SELECT d.donorid, d.first_name, d.last_name, d.donemail, d.phone, d.address, SUM(dn.amount) AS total_amount
        FROM donors d
        INNER JOIN donations dn ON d.donorid = dn.donorid
        GROUP BY d.donorid";

$result = $database->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    $donors = $result->fetch_all(MYSQLI_ASSOC);
    $unique_donors_count = count($donors);
} else {
    $donors = []; // Empty array if no donors found
    $unique_donors_count = 0;
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
    <title>Donors Who Have Donated - Giving Sphere Charity</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="adben.css">

    <script src="https://kit.fontawesome.com/03050c7944.js" crossorigin="anonymous"></script>
   
    <link rel="icon" href="logo.png" type="image/x-icon">
    <style>
        /* Table Styling */
        .user-list table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
        }

        .user-list table th,
        .user-list table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .user-list table th {
            background-color: #1A1817; /* Eerie black */
            color: #FDF6EC; /* Off-white */
        }

        .user-list table tbody tr:nth-child(odd) {
            background-color: #F3E9DD; /* Light cream */
        }

        .user-list table tbody tr:nth-child(even) {
            background-color: #FDF6EC; /* Off-white */
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

        </aside>
        <main class="main-content">
            <div class="header-actions">
                <h2>Donors Who Have Donated (<?php echo $unique_donors_count; ?>)</h2>
            </div>
            <div class="user-list">
                <table>
                    <thead>
                        <tr>
                            <th>Donor ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Total Amount Donated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($donors as $donor): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($donor['donorid']); ?></td>
                                <td><?php echo htmlspecialchars($donor['first_name'] . ' ' . $donor['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($donor['donemail']); ?></td>
                                <td><?php echo htmlspecialchars($donor['phone']); ?></td>
                                <td><?php echo htmlspecialchars($donor['address']); ?></td>
                                <td><?php echo htmlspecialchars(number_format($donor['total_amount'], 2)); ?></td>
                            </tr>
                        <?php endforeach; ?>
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

    <script>
        // Optional: Add client-side JavaScript if needed
    </script>
</body>
</html>
