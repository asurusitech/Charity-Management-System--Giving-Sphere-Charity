<?php
// Include the database connection file
include 'connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the donation ID and determine the status based on which button was clicked
    $donation_id = $_POST['donation_id'];
    $status = 'Pending'; // Default status

    if (isset($_POST['approve'])) {
        $status = 'Completed';
    } elseif (isset($_POST['deny'])) {
        $status = 'Canceled';
    }

    // Prepare the SQL statement to update the payment status
    $stmt = $database->prepare("UPDATE donations SET payment_status = ? WHERE donation_id = ?");
    $stmt->bind_param("si", $status, $donation_id);

    // Execute the query and provide feedback
    if ($stmt->execute()) {
        // Get the donor ID and amount to insert a notification
        $donor_query = $database->prepare("SELECT donorid, amount FROM donations WHERE donation_id = ?");
        $donor_query->bind_param("i", $donation_id);
        $donor_query->execute();
        $donor_result = $donor_query->get_result();

        if ($donor_result && $donor_result->num_rows == 1) {
            $donor = $donor_result->fetch_assoc();
            $donor_id = $donor['donorid'];
            $donated_amount = $donor['amount'];

            // Prepare the SQL statement to insert a notification
            $notification_message = "Your donation of Ksh $donated_amount has been $status. Thank you so much for your Generosity";
            $notification_stmt = $database->prepare("
                INSERT INTO notifications (donor_id, message, status, created_at) 
                VALUES (?, ?, 'unread', NOW())
            ");
            $notification_stmt->bind_param("is", $donor_id, $notification_message);

            if ($notification_stmt->execute()) {
                $message = "Payment status updated and notification sent successfully.";
            } else {
                $message = "Error sending notification: " . $notification_stmt->error;
            }

            // Close notification statement
            $notification_stmt->close();
        } else {
            $message = "Error retrieving donor details.";
        }

        // Close donor query statement
        $donor_query->close();
    } else {
        $message = "Error updating payment status: " . $stmt->error;
    }

    // Close update statement and connection
    $stmt->close();
    $database->close();

    // Redirect with alert
    echo "<script>
        alert('$message');
        window.location.href = 'viewdonations.php';
    </script>";
} else {
    echo "<script>
        alert('Invalid request.');
        window.location.href = 'viewdonations.php';
    </script>";
}
?>
