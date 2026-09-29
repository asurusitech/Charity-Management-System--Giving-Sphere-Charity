<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

if (isset($_GET['id'])) {
    $donorid = $_GET['id'];

    $database->begin_transaction();
    try {
        // Delete from donors table
        $delete_donor_query = $database->prepare("DELETE FROM donors WHERE donorid = ?");
        $delete_donor_query->bind_param('i', $donorid);
        $delete_donor_query->execute();

        $database->commit();
        $_SESSION['message'] = "Donor and associated records deleted successfully.";
        header("Location: admin_users.php");
        exit();
    } catch (Exception $e) {
        $database->rollback();
        echo "Error deleting donor: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
    exit();
}
?>
