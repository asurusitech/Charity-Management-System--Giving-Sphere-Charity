<?php

require 'vendor/autoload.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// PayPal API credentials (replace with your actual sandbox credentials)
$clientId = 'AeqPkYMPvhqLxP_zyl1Q2Nk_0qYMaMJYTdeCjVJeI0eFnhDeukFlWDGNVlXjnJC5a8bD5W52iwtarIga';
$clientSecret = 'EItsYnEFngxJ1XvPay29LfeL5hkoG0A43QQ3XqVR_YoBHM1TisE62hhnqPlanut1BXwyGiAWqDnegts7';

// Function to get PayPal access token
function getAccessToken($clientId, $clientSecret) {
    $client = new Client();
    $url = 'https://api.sandbox.paypal.com/v1/oauth2/token';

    // Manually create the base64 encoded Authorization header
    $auth = base64_encode("$clientId:$clientSecret");

    try {
        $response = $client->post($url, [
            'headers' => [
                'Authorization' => "Basic $auth",
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
        ]);
        $data = json_decode((string)$response->getBody());
        return $data->access_token;
    } catch (RequestException $e) {
        echo "RequestException: " . $e->getMessage() . "<br>";
        if ($e->hasResponse()) {
            echo "Response: " . $e->getResponse()->getBody()->getContents() . "<br>";
        }
        return null;
    } catch (Exception $e) {
        echo "Exception: " . $e->getMessage() . "<br>";
        return null;
    }
}

// Function to get transaction status from PayPal
function getTransactionStatus($transactionId, $accessToken) {
    $client = new Client();
    try {
        $response = $client->get("https://api.sandbox.paypal.com/v1/payments/payment/{$transactionId}", [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
            ],
        ]);
        $data = json_decode((string)$response->getBody());
        return $data->state; // Assuming 'state' represents the payment status in PayPal's API
    } catch (RequestException $e) {
        $responseBody = $e->getResponse()->getBody()->getContents();
        $responseBody = json_decode($responseBody, true);
        if ($responseBody['name'] == 'INVALID_RESOURCE_ID') {
            echo "Transaction ID {$transactionId} not found in PayPal sandbox.<br>";
            return null;
        }
        echo "RequestException: " . $e->getMessage() . "<br>";
        if ($e->hasResponse()) {
            echo "Response: " . $responseBody . "<br>";
        }
        return null;
    } catch (Exception $e) {
        echo "Exception: " . $e->getMessage() . "<br>";
        return null;
    }
}

// Get PayPal access token
$accessToken = getAccessToken($clientId, $clientSecret);
if (!$accessToken) {
    echo "Error getting PayPal access token.";
    exit();
}

// Handle search functionality
$search = $_GET['search'] ?? '';
$search = '%' . $search . '%';

// Fetch donations details including donor names and payment status from the database
$query = $database->prepare("
    SELECT d.donation_id, d.amount, d.donation_date, 
           d.created_at, d.updated_at, d.beneficiary_id, d.event_id, 
           d.transaction_id, d.payment_status, donors.first_name, donors.last_name 
    FROM donations d
    JOIN donors ON d.donorid = donors.donorid
    LEFT JOIN transactions t ON d.transaction_id = t.transaction_id
    WHERE d.amount LIKE ? OR d.donation_date LIKE ? OR d.payment_status LIKE ? 
          OR donors.first_name LIKE ? OR donors.last_name LIKE ?
");
$query->bind_param('sssss', $search, $search, $search, $search, $search);
$query->execute();
$result = $query->get_result();

$donations = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if (isset($row['transaction_id']) && !empty($row['transaction_id'])) {
            // Get the updated transaction status from PayPal
            $status = getTransactionStatus($row['transaction_id'], $accessToken);
            if ($status && $status != $row['payment_status']) {
                // Update the status in the database if different
                $stmt = $database->prepare("UPDATE donations SET payment_status = ? WHERE donation_id = ?");
                $stmt->bind_param("si", $status, $row['donation_id']);
                $stmt->execute();
                $row['payment_status'] = $status; // Update the status in the local array
            }
        }
        $donations[] = $row;
    }
} else {
    echo "Error fetching donations: " . $database->error;
    exit();
}
// Handle search functionality
$search = $_GET['search'] ?? '';
$search = '%' . $search . '%';

$amountFilter = $_GET['amount'] ?? '';
$amountCondition = '';
if (!empty($amountFilter) && is_numeric($amountFilter)) {
    $amountCondition = "d.amount > " . floatval($amountFilter);
}

// Fetch donations details including donor names and payment status from the database
$query = $database->prepare("
    SELECT d.donation_id, d.amount, d.donation_date, 
           d.created_at, d.updated_at, d.beneficiary_id, d.event_id, 
           d.transaction_id, d.payment_status, donors.first_name, donors.last_name 
    FROM donations d
    JOIN donors ON d.donorid = donors.donorid
    LEFT JOIN transactions t ON d.transaction_id = t.transaction_id
    WHERE (d.amount LIKE ? OR d.donation_date LIKE ? OR d.payment_status LIKE ? 
          OR donors.first_name LIKE ? OR donors.last_name LIKE ?)
    " . (!empty($amountCondition) ? "AND $amountCondition" : "") . "
");
$query->bind_param('sssss', $search, $search, $search, $search, $search);
$query->execute();
$result = $query->get_result();


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
    <title>Donations - Giving Sphere Charity</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="viewdonations.css">
    <link rel="icon" href="logo.png" type="image/x-icon">
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/03050c7944.js" crossorigin="anonymous"></script>
    <style>

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
                <li class="active"><a href="viewdonations.php"><i class="fas fa-donate"></i> Donations</a></li>
                <li><a href="admin_events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
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
                <h2>Donations (<?php echo htmlspecialchars((string)count($donations)); ?>)</h2>
                
               
                <form method="GET" action="viewdonations.php" style="display: inline;">
    <input type="text" name="search" placeholder="Search donations..." value="<?php echo htmlspecialchars($search); ?>" style="padding: 8px; margin-right: 10px;">
    <button type="submit" style="padding: 8px;">Search</button>
</form>

<a href="donpdf.php?search=<?php echo urlencode($_GET['search'] ?? ''); ?>" class="add-donor-btn">Print PDF</a>

            </div>
            <div class="user-table">
                <table>
                    <thead>
                        <tr>
                            <th>Donor Name</th>
                            <th>Amount</th>
                            <th>Donation Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($donations)): ?>
                            <?php foreach ($donations as $donation): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars((string)$donation['first_name'] . ' ' . $donation['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars((string)$donation['amount']); ?></td>
                                    <td><?php echo htmlspecialchars((string)$donation['donation_date']); ?></td>
                                    <td class="<?php 
                                    if ($donation['payment_status'] == 'Completed') {
                                        echo 'status-completed';
                                    } elseif ($donation['payment_status'] == 'Pending') {
                                        echo 'status-pending';
                                    } elseif ($donation['payment_status'] == 'Canceled') {
                                        echo 'status-canceled';
                                    }
                                    ?>"><?php echo htmlspecialchars((string)$donation['payment_status']); ?></td>
                                    <td class="action-buttons">
                                        <form action="confirmpay.php" method="POST">
                                            <input type="hidden" name="donation_id" value="<?php echo $donation['donation_id']; ?>">
                                            <button type="submit" name="approve" value="1" class="approve-btn">Approve</button>
                                            <button type="submit" name="deny" value="1" class="deny-btn">Mark As Incomplete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No donations found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
