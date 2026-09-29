<?php
session_start();
include 'connection.php';  // Database connection file

// Function to handle PayPal payment and update database
function handlePayPalPayment($donorid, $amount, $database) {
    // Insert into transactions table
    $stmt = $database->prepare("INSERT INTO transactions (donorid, transaction_time, transaction_amount, transaction_status, transaction_currency, transaction_description, paypal_transaction_id, created_at, updated_at) VALUES (?, NOW(), ?, 'Pending', 'USD', 'Donation via PayPal', '', NOW(), NOW())");
    $stmt->bind_param("id", $donorid, $amount);

    if ($stmt->execute()) {
        $transactionId = $stmt->insert_id;

        // Redirect to PayPal payment link
        $paypalLink = "https://www.sandbox.paypal.com/cgi-bin/webscr";
        $returnUrl = "https://giving-sphere.is-great.org/paypal_callback.php"; 
        $query = http_build_query([
            'cmd' => '_xclick',
            'business' => 'sb-npwxh31716001@business.example.com',
            'item_name' => 'Donation',
            'amount' => $amount,
            'currency_code' => 'USD',
            'return' => "{$returnUrl}?transactionId={$transactionId}&amount={$amount}",
            'notify_url' => $returnUrl
        ]);
        header("Location: {$paypalLink}?{$query}");
        exit();
    } else {
        echo "Error: Unable to process donation.";
    }

    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize inputs
    $amount = $_POST['amount'] ?? '';
    $paymentMethod = $_POST['payment_method'] ?? 'paypal';

    // Validate amount
    if (!is_numeric($amount) || $amount <= 0) {
        die('Error: Invalid donation amount.');
    }

    // Check if the donor ID is stored in the session
    if (isset($_SESSION["user"])) {
        $donorid = $_SESSION["user"];

        // Insert donation into donations table
        $stmt = $database->prepare("INSERT INTO donations (donorid, amount, donation_date, payment_status, created_at, updated_at) VALUES (?, ?, NOW(), 'Pending', NOW(), NOW())");
        if ($stmt === false) {
            die('Prepare error: ' . $database->error);
        }
        $stmt->bind_param("id", $donorid, $amount);

        if ($stmt->execute()) {
            // Insert notification into adminnotifications table
            $notificationStmt = $database->prepare("INSERT INTO adminnotifications (donor_id, notification_type, details, created_at, status) VALUES (?, 'Payment', ?, NOW(), 'unread')");
            if ($notificationStmt === false) {
                error_log('Prepare error: ' . $database->error);
                die('Database prepare error.');
            }

            $details = "New donation of $$amount";
            $notificationStmt->bind_param("is", $donorid, $details);

            if ($notificationStmt->execute()) {
                // Handle PayPal payment
                handlePayPalPayment($donorid, $amount, $database);
            } else {
                echo "Error: Unable to send notification.";
            }
            
            $notificationStmt->close();
        } else {
            echo "Error: Unable to process donation.";
        }
        
        $stmt->close();
    } else {
        echo "Error: Donor ID not found in session.";
    }

    $database->close();
} else {
    echo "Invalid request.";
}
?>
