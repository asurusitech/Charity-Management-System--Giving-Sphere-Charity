<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

// Fetch necessary data for reports
$donors_present = 0;
$total_donations = 0;
$total_amount_donations = 0.0;
$num_employees = 0;
$organized_events = 0;
$num_beneficiaries = 0;

// Fetch total number of donors
$result = $database->query("SELECT COUNT(*) AS count FROM donors");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $donors_present = $row['count'];
}

// Fetch total number of donations and their total amount
$result = $database->query("SELECT COUNT(*) AS count, SUM(amount) AS total_amount FROM donations");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_donations = $row['count'];
    $total_amount_donations = $row['total_amount'];
}

// Fetch total number of employees (if applicable)
// Replace with actual query as per your data structure

// Fetch total number of organized events
$result = $database->query("SELECT COUNT(*) AS count FROM events");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $organized_events = $row['count'];
}

// Fetch total number of beneficiaries
$result = $database->query("SELECT COUNT(*) AS count FROM beneficiaries");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $num_beneficiaries = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Giving Sphere Charity</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="icon" href="logo.png" type="image/x-icon">
    <style>
        /* Table Styling */
        .report-table table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
        }

        .report-table table th,
        .report-table table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .report-table table th {
            background-color: #1A1817; /* Eerie black */
            color: #FDF6EC; /* Off-white */
        }

        .report-table table tbody tr:nth-child(odd) {
            background-color: #F3E9DD; /* Light cream */
        }

        .report-table table tbody tr:nth-child(even) {
            background-color: #FDF6EC; /* Off-white */
        }

        /* Sidebar Styling */
        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            display: block;
            padding: 10px 15px;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .sidebar ul li a:hover {
            background-color: #B06D4Aff; /* Green with transparency */
        }

        .sidebar ul .active a {
            background-color: #B06D4Aff; /* Green with transparency */
            color: #FFF;
        }

        .sidebar .sub-menu {
            display: none;
            padding-left: 20px;
        }

        .sidebar .sub-menu.active {
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
                    <li><a href="index.php">Home</a></li>
                    <li><a href="admin.php">Admin</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="admin-container">
        <aside class="sidebar">
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="admin_users.php">Donors</a></li>
                <li><a href="admin_beneficiaries.php">Beneficiaries</a></li>
                <li><a href="viewdonations.php">Donations</a></li>
                <li><a href="admin_events.php">Events</a></li>
                <li>
                    <a href="#" class="sub-link-toggle">Reports</a>
                    <ul class="sub-menu">
                        <li><a href="#">Donors Report</a></li>
                        <li><a href="#">Donations Report</a></li>
                        <!-- Add more sub-links as needed -->
                    </ul>
                </li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="header-actions">
                <h2>Reports Overview</h2>
            </div>
            <div class="report-table">
                <table>
                    <thead>
                        <tr>
                            <th>Total Donors</th>
                            <th>Total Donations</th>
                            <th>Total Amount Donated</th>
                            <th>Total Employees</th>
                            <th>Organized Events</th>
                            <th>Total Beneficiaries</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo htmlspecialchars($donors_present); ?></td>
                            <td><?php echo htmlspecialchars($total_donations); ?></td>
                            <td><?php echo htmlspecialchars($total_amount_donations); ?></td>
                            <td><?php echo htmlspecialchars($num_employees); ?></td>
                            <td><?php echo htmlspecialchars($organized_events); ?></td>
                            <td><?php echo htmlspecialchars($num_beneficiaries); ?></td>
                        </tr>
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
