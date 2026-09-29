<?php
session_start();

include 'connection.php';  // Database connection file


// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
   
    // Insert donation into donations table
    $stmt = $database->prepare("INSERT INTO donations (donorid, amount, donation_date, payment_method, created_at, updated_at) VALUES (?, ?, NOW(), ?, NOW(), NOW())");
    $stmt->bind_param("sds", $donorid, $amount, $payment_method);

    if ($stmt->execute()) {
        // Display thank you message and redirect to index.php
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Thank You</title>
            <style>
                body {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    background-color: var(--off-white);
                    font-family: Arial, sans-serif;
                    margin: 0;
                    color: var(--black);
                    background-color: #FDF6EC;
                }
                .message-box {
                    text-align: center;
                    padding: 20px;
                    border: 1px solid #ccc;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    background-color: #FFF;
                }
            </style>
        </head>
        <body>
            <div class='message-box'>
                <h1>Thank You for Your Generous Donation!</h1>
                <p>Your donation of Ksh$amount has been successfully received.</p>
                <p>You will be redirected to the homepage shortly.</p>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 5000); // Redirect after 5 seconds
            </script>
        </body>
        </html>";
    } else {
        echo "Error: Unable to process donation.";
    }

    $stmt->close();
    $database->close();
}
?>