<?php
session_start();
include 'connection.php';  // Database connection file

// Get transaction details from PayPal
$transactionId = $_GET['transactionId'] ?? '';
$paymentStatus = $_GET['status'] ?? '';
$paymentAmount = $_GET['amount'] ?? '';
$paypalTransactionId = $_GET['txn_id'] ?? '';

// Validate received data
if (empty($transactionId) || empty($paymentStatus) || empty($paypalTransactionId)) {
    die('Invalid transaction details.');
}

// Update the transaction in the database
$stmt = $database->prepare("UPDATE transactions SET transaction_status = ?, paypal_transaction_id = ?, transaction_amount = ? WHERE id = ?");
$stmt->bind_param("ssdi", $paymentStatus, $paypalTransactionId, $paymentAmount, $transactionId);

if ($stmt->execute()) {
    echo "Transaction updated successfully.";
} else {
    echo "Error updating transaction: " . $stmt->error;
}

$stmt->close();
$database->close();
?>
