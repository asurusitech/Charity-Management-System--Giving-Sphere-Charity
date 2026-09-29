<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require('fpdf.php');

include 'connection.php';

$donors_present = 0;
$total_donations = 0;
$total_amount_donations = 0.0;
$num_employees = 0;
$organized_events = 0;
$num_beneficiaries = 0;

// Fetch total number of donors
$result = $database->query("SELECT COUNT(*) AS count FROM donors");
if ($result) {
    $row = $result->fetch_assoc();
    $donors_present = $row['count'];
}

// Fetch the donors' details
$search = $_GET['search'] ?? '';
$search = $database->real_escape_string($search);

$queryStr = "SELECT * FROM donors";
if ($search) {
    $queryStr .= " WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR donemail LIKE '%$search%' OR phone LIKE '%$search%' OR gender LIKE '%$search%' OR address LIKE '%$search%'";
}

// Add sorting by created_at in descending order
$queryStr .= " ORDER BY created_at DESC";

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
    <title>Donors - Giving Sphere Charity</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="admin_users.css">
    <link rel="icon" href="logo.png" type="image/x-icon">
    <script src="https://kit.fontawesome.com/03050c7944.js" crossorigin="anonymous"></script>
    
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
             </ul>
            </nav>
    
        </div>
    </header>

    <div class="admin-container">
        <aside class="sidebar">
        <ul>
    <li ><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="active"><a href="admin_users.php"><i class="fas fa-users"></i> Donors</a></li>
    <li><a href="admin_beneficiaries.php"><i class="fas fa-hand-holding-heart"></i> Beneficiaries</a></li>
    <li><a href="viewdonations.php"><i class="fas fa-donate"></i> Donations</a></li>
    <li><a href="admin_events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
    
    <li><a href="admin_notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
    <li>
        <a href="#" class="sub-link-toggle"><i class="fas fa-chart-line"></i> Reports</a>
        <ul class="sub-menu">
            <li><a href="donatedusers.php"><i class="fas fa-user-check"></i> Users who have donated</a></li>
            <li><a href="#"><i class="fas fa-file-alt"></i> Donations Report</a></li>
        </ul>
    </li>
</ul> </aside>
        <main class="main-content">
            <div class="header-actions">
                <h2>Donors (<?php echo htmlspecialchars($donors_present); ?>)</h2>
                <form action="admin_users.php" method="GET" style="display: inline;">
                    <input type="text" name="search" placeholder="Search donors" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" style="padding: 8px; margin-right: 10px;">
                    <button type="submit" style="padding: 8px;">Search</button>
                </form>
                <a href="add_donor.php" class="add-donor-btn">Add Donor</a>
                <a href="generate_pdf.php?search=<?php echo urlencode($_GET['search'] ?? ''); ?>" class="add-donor-btn">Download PDF</a>           </div>
            <p></p>
            <div class="user-table">
                <table>
                    <thead>
                        <tr>
                            <th>Donor ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Action</th>
                            <th>Action2</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($donors) > 0): ?>
                            <?php foreach ($donors as $donor): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($donor['donorid']); ?></td>
                                    <td><?php echo htmlspecialchars($donor['first_name'] . ' ' . $donor['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($donor['donemail']); ?></td>
                                    <td><?php echo htmlspecialchars($donor['phone']); ?></td>
                                    <td class="action-buttons">
                                        <a href="Admin_update_donor.php?id=<?php echo $donor['donorid']; ?>" class="update-btn">Update</a>
                                    </td>
                                    <td class="action-buttons">
                                        <a href="delete_donor.php?id=<?php echo $donor['donorid']; ?>" class="delete-btn">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No donors found.</td>
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

    <script>
        // Script for handling sidebar sub-menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const subLinkToggle = document.querySelector('.sub-link-toggle');
            const subMenu = document.querySelector('.sub-menu');

            subLinkToggle.addEventListener('click', function(event) {
                event.preventDefault();
                subMenu.classList.toggle('active');
            });
        });
    </script>
</body>
</html>
