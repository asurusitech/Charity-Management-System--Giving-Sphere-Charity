<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

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
                // Fetch donor ID and event ID
                $sql = "SELECT donorid, event_id FROM participation_requests WHERE request_id = ?";
                $stmt = $database->prepare($sql);
                $stmt->bind_param("i", $request_id);
                $stmt->execute();
                $stmt->bind_result($donorid, $event_id);
                $stmt->fetch();
                $stmt->close();

                // Insert notification
                $message = "Your participation request for event ID $event_id has been $status.";
                $sql = "INSERT INTO notifications (donor_id, event_id, message, status) VALUES (?, ?, ?, 'unread')";
                $stmt = $database->prepare($sql);
                $stmt->bind_param("iis", $donorid, $event_id, $message);
                $stmt->execute();

                // Fetch user's phone number
                $sql = "SELECT phone FROM donors WHERE donorid = ?";
                $stmt = $database->prepare($sql);
                $stmt->bind_param("i", $donorid);
                $stmt->execute();
                $stmt->bind_result($phone);
                $stmt->fetch();
                $stmt->close();

                if (!empty($phone)) {
                    // Trigger Twilio Studio Flow
                    $twilio_flow_url = 'https://studio.twilio.com/v2/Flows/FWdf2b7cef8282541fbc3100052191adab/Executions';
                    $account_sid = 'ACb5360f64f96cc530ab2643b4e2b5f1fe'; 
                    $auth_token = '4278c680fc40baf6e016cbc5087e4f59'; 

                    $data = [
                        'To' => $phone,
                        'From' => '+12512208145', 
                        'Parameters' => json_encode(['status' => strtolower($status)]),
                    ];

                    $options = [
                        'http' => [
                            'header' => "Authorization: Basic " . base64_encode($account_sid . ":" . $auth_token) . "\r\n" .
                                        "Content-type: application/x-www-form-urlencoded\r\n",
                            'method' => 'POST',
                            'content' => http_build_query($data),
                        ],
                    ];

                    $context = stream_context_create($options);
                    $result = file_get_contents($twilio_flow_url, false, $context);

                    if ($result === FALSE) {
                        $error = error_get_last();
                        $message = "Request has been " . strtolower($status) . " successfully, but there was an error triggering the Twilio flow: " . $error['message'];
                    } else {
                        $message = "Request has been " . strtolower($status) . " and Notification sent successfully.";
                    }
                } else {
                    $message = "Request has been " . strtolower($status) . " successfully, but the user's phone number could not be retrieved.";
                }
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
