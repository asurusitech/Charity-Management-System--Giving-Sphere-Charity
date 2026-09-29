<?php
session_start();
include 'connection.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the posted data
    $notification_id = $_POST['notification_id'];
    $reply_message = $_POST['reply_message'];

    // Retrieve donor_id based on the notification_id
    $donor_id_query = "SELECT donor_id FROM adminnotifications WHERE notification_id = ?";
    if ($stmt = $database->prepare($donor_id_query)) {
        $stmt->bind_param("i", $notification_id);
        $stmt->execute();
        $stmt->bind_result($donor_id);
        $stmt->fetch();
        $stmt->close();
    } else {
        echo "Error: Could not prepare the donor retrieval query. " . $database->error;
        exit();
    }

    // Check if donor_id is valid
    if ($donor_id) {
        // Insert the admin's reply into the notifications table
        $sql = "INSERT INTO notifications (donor_id, message) VALUES (?, ?)";
        if ($stmt = $database->prepare($sql)) {
            // Bind the variables to the prepared statement as parameters
            $stmt->bind_param("is", $donor_id, $reply_message);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                echo "Reply sent successfully.";
            } else {
                echo "Error: Could not execute the query. " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Error: Could not prepare the query. " . $database->error;
        }
    } else {
        echo "Invalid donor ID.";
    }

    // Close the database connection
    $database->close();
} else {
    echo "Invalid request method.";
}
?>
