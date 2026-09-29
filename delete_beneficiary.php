<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

// Check if beneficiary ID is provided via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Beneficiary ID not provided.";
    exit();
}

// Delete beneficiary from the database
$beneficiary_id = $_GET['id'];
$delete_query = $database->prepare("DELETE FROM beneficiaries WHERE beneficiary_id = ?");
$delete_query->bind_param("i", $beneficiary_id);

if ($delete_query->execute()) {
    // Redirect to beneficiaries list after successful deletion
    header("Location: admin_beneficiaries.php");
    exit();
} else {
    echo "Error deleting beneficiary: " . $database->error;
}
?>
