<?php
session_start();
include 'connection.php'; // Database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $replyMessage = $_POST['reply_message'] ?? '';

    // Validate and sanitize the input
    if (empty($replyMessage)) {
        echo "<script>
                alert('Reply message cannot be empty.');
                window.location.href = 'donor_notifications.php';
              </script>";
        exit;
    }

    $replyMessage = htmlspecialchars($replyMessage);

    // Check if the donor ID is stored in the session
    if (isset($_SESSION['user'])) {
        $donorId = $_SESSION['user'];

        // Insert the reply into the adminnotifications table
        $stmt = $database->prepare("INSERT INTO adminnotifications (donor_id, notification_type, details, created_at, status) VALUES (?, 'Reply', ?, NOW(), 'unread')");
        if ($stmt === false) {
            error_log('Prepare error: ' . $database->error);
            echo "<script>
                    alert('Database prepare error.');
                    window.location.href = 'donor_notifications.php';
                  </script>";
            exit;
        }

        $stmt->bind_param("is", $donorId, $replyMessage);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Reply sent successfully.');
                    window.location.href = 'donor_notifications.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Error: Unable to send reply.');
                    window.location.href = 'donor_notifications.php';
                  </script>";
        }

        $stmt->close();
    } else {
        echo "<script>
                alert('Error: Donor ID not found in session.');
                window.location.href = 'donor_notifications.php';
              </script>";
    }

    $database->close();
} else {
    echo "<script>
            alert('Invalid request.');
            window.location.href = 'mark_as_read.php';
          </script>";
}
?>
