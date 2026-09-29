<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';
require __DIR__ . '/vendor/autoload.php'; // Make sure to include the Twilio SDK

use Twilio\Rest\Client;

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['request_id'])) {
        $request_id = intval($_POST['request_id']);
        $status = '';

        if (isset($_POST['approve'])) {
            $status = 'Approved';
        } elseif (isset($_POST['deny'])) {
            $status = 'Rejected';
        }

        if (!empty($status)) {
            // Update the status of the participation request
            $sql = "UPDATE participation_requests SET status = ? WHERE request_id = ?";
            $stmt = $database->prepare($sql);
            $stmt->bind_param("si", $status, $request_id);

            if ($stmt->execute()) {
                $message = "Request has been " . strtolower($status) . " successfully.";
                
                // Fetch donor's phone number
                $query = "SELECT d.phone FROM participation_requests pr
                          INNER JOIN donors d ON pr.donorid = d.donorid
                          WHERE pr.request_id = ?";
                $stmt = $database->prepare($query);
                $stmt->bind_param("i", $request_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $phone_number = $row['phone'];

                    // Send SMS notification
                    $account_sid = 'your_twilio_account_sid'; // Replace with your Twilio account SID
                    $auth_token = 'your_twilio_auth_token'; // Replace with your Twilio auth token
                    $twilio_number = 'your_twilio_phone_number'; // Replace with your Twilio phone number

                    $client = new Client($account_sid, $auth_token);

                    $sms_message = "Your participation request has been " . strtolower($status) . ".";

                    try {
                        $client->messages->create(
                            $phone_number,
                            array(
                                'from' => $twilio_number,
                                'body' => $sms_message
                            )
                        );
                    } catch (Exception $e) {
                        $message .= " However, failed to send SMS: " . $e->getMessage();
                    }
                } else {
                    $message .= " However, donor's phone number not found.";
                }

                $stmt->close();
            } else {
                $message = "Error updating request: " . $stmt->error;
            }
        } else {
            $message = "Invalid action.";
        }
    } else {
        $message = "Request ID is missing.";
    }

    echo "<script>
        alert('{$message}');
        window.location.href = 'adevent.php';
    </script>";
    exit();
}

$database->close();
?>
