<?php
include 'connection.php';

$notification_id = intval($_GET['notification_id']); 

if ($notification_id > 0) {
    // Prepare the SQL query
    $query = $database->prepare("UPDATE adminnotifications SET status = 'read' WHERE notification_id = ?");
    
    // Bind the notification ID to the query
    $query->bind_param("i", $notification_id);
    
    // Execute the query
    if ($query->execute()) {
        // Success message and redirection
        echo "<script>
                alert('Notification status updated successfully.');
                window.location.href = 'admin_notifications.php';
              </script>";
    } else {
        // Error message and redirection
        echo "<script>
                alert('Error updating notification status: " . $database->error . "');
                window.location.href = 'admin_notifications.php';
              </script>";
    }
} else {
    // Invalid ID message and redirection
    echo "<script>
            alert('Invalid notification ID.');
            window.location.href = 'admin_notifications.php';
          </script>";
}
?>
    