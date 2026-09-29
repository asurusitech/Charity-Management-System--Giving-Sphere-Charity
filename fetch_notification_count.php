<?php
session_start();
include 'connection.php';

if (isset($_SESSION['user'])) {
    $donorid = $_SESSION['user'];

    // Fetch unread notifications count
    $query = $database->prepare("
        SELECT COUNT(*) AS unread_count 
        FROM notifications 
        WHERE donor_id = ? AND status = 'unread'
    ");
    $query->bind_param("i", $donorid);
    $query->execute();
    $result = $query->get_result();
    if ($result && $row = $result->fetch_assoc()) {
        echo htmlspecialchars($row['unread_count']);
    } else {
        echo "Error fetching unread notifications count.";
    }
} else {
    echo "User not logged in.";
}
?>
