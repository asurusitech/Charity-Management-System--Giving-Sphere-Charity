<?php
require 'vendor/autoload.php';
include 'connection.php';

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payment;

// Set up PayPal API context
$apiContext = new ApiContext(
    new OAuthTokenCredential(
        'YOUR_CLIENT_ID',     // ClientID
        'YOUR_CLIENT_SECRET'  // ClientSecret
    )
);

// Set to 'sandbox' for testing or 'live' for production
$apiContext->setConfig(
    array(
        'mode' => 'sandbox',  // Change to 'live' for production
        'log.LogEnabled' => true,
        'log.FileName' => '../PayPal.log',
        'log.LogLevel' => 'DEBUG', // Change to 'INFO' for production
        'cache.enabled' => true,
    )
);

// Function to get transaction status from PayPal
function getTransactionStatus($transactionId, $apiContext) {
    try {
        $payment = Payment::get($transactionId, $apiContext);
        return $payment->getState();
    } catch (Exception $ex) {
        // Handle error
        return null;
    }
}

// Fetch donations with pending status from your database
$query = $database->query("SELECT donation_id, transaction_id FROM donations WHERE payment_status = 'Pending'");
if ($query) {
    while ($donation = $query->fetch_assoc()) {
        $status = getTransactionStatus($donation['transaction_id'], $apiContext);
        if ($status) {
            // Update donation status in your database
            $stmt = $database->prepare("UPDATE donations SET payment_status = ? WHERE donation_id = ?");
            $stmt->bind_param("si", $status, $donation['donation_id']);
            $stmt->execute();
        }
    }
}
?>
