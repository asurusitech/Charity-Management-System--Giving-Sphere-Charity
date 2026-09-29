<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $event_id = $_GET['id'];

    // Delete event from the database
    $delete_query = $database->prepare("DELETE FROM events WHERE event_id = ?");
    $delete_query->bind_param("i", $event_id);

    if ($delete_query->execute()) {
        // Redirect to events list after successful deletion
        header("Location: admin_events.php");
        exit();
    } else {
        echo "Error deleting event: " . $database->error;
    }
} else {
    echo "Invalid request.";
    exit();
}
?>
