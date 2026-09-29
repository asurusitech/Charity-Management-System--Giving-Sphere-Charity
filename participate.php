<?php
session_start();

include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['event_id'])) {
    $donorid = $_SESSION['user'];  // Assuming the donor's ID is stored in the session
    $event_id = $_POST['event_id'];

    // Insert participation request into the database
    $query = $database->prepare("INSERT INTO participation_requests (donorid, event_id, status) VALUES (?, ?, 'Pending')");
    $query->bind_param("ii", $donorid, $event_id);
    
    if ($query->execute()) {
        // Insert notification into adminnotifications
        $notification_type = 'Participation Request';
        $details = "Donor ID: $donorid has requested to participate in Event ID: $event_id.";

        $notify_query = $database->prepare("INSERT INTO adminnotifications (donor_id, notification_type, details) VALUES (?, ?, ?)");
        $notify_query->bind_param("iss", $donorid, $notification_type, $details);
        
        if ($notify_query->execute()) {
            // Redirect back to donor events page after successful participation request and notification
            header("Location: donor_events.php");
            exit;
        } else {
            echo "Error inserting notification: " . $notify_query->error;
        }
    } else {
        echo "Error submitting participation request: " . $query->error;
    }
} else {
    echo "Invalid request.";
}
?>
