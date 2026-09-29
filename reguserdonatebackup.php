<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include 'connection.php';  // Database connection file

$consumerKey = 'aGvoHeJcKK0FXug1kDW2trq0eBVa4Uw3tfNcFItqFsxXhU72';
$consumerSecret = 'hycopDIIo9AQ781tIYUAmXpMFyWGHMObeSqMxa5oEV8sD9JrV3DFpuoA7AnA8aON';

function generateAccessToken($consumerKey, $consumerSecret) {
    $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    $credentials = base64_encode($consumerKey . ':' . $consumerSecret);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($curl);
    if ($response === false) {
        die('Curl error: ' . curl_error($curl));
    }
    curl_close($curl);

    $result = json_decode($response);
    if (isset($result->access_token)) {
        return $result->access_token;
    } else {
        die('Error: Unable to generate access token.');
    }
}

function initiatePayment($accessToken, $phoneNumber, $amount) {
    $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $shortCode = '174379'; // Replace with your M-Pesa short code
    $lipaNaMpesaOnlinePasskey = 'your_lipa_na_mpesa_online_passkey'; // Replace with your M-Pesa online passkey
    $timestamp = date('YmdHis');
    $password = base64_encode($shortCode . $lipaNaMpesaOnlinePasskey . $timestamp);

    $curl_post_data = array(
        'BusinessShortCode' => $shortCode,
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phoneNumber,
        'PartyB' => $shortCode,
        'PhoneNumber' => $phoneNumber,
        'CallBackURL' => 'https://giving-sphere.is-great.org/callback_url.php', // Replace with your actual callback URL
        'AccountReference' => 'Donation',
        'TransactionDesc' => 'Charity Donation'
    );

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $accessToken));
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($curl_post_data));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($curl);
    if ($response === false) {
        die('Curl error: ' . curl_error($curl));
    }
    curl_close($curl);

    return json_decode($response);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize inputs
    $amount = $_POST['amount'] ?? '';
    $phoneNumber = $_POST['phone_number'] ?? '';
    $paymentMethod = $_POST['payment_method'] ?? '';

    // Validate amount
    if (!is_numeric($amount) || $amount <= 0) {
        die('Error: Invalid donation amount.');
    }

    // Check if the donor ID is stored in the session
    if (isset($_SESSION["user"])) {
        $donorid = $_SESSION["user"];
        
        // Insert donation into donations table
        $stmt = $database->prepare("INSERT INTO donations (donorid, amount, donation_date, payment_method, created_at, updated_at) VALUES (?, ?, NOW(), ?, NOW(), NOW())");
        if ($stmt === false) {
            die('Prepare error: ' . $database->error);
        }
        $stmt->bind_param("sds", $donorid, $amount, $paymentMethod);

        if ($stmt->execute()) {
            if ($paymentMethod == 'mpesa') {
                // Generate access token
                $accessToken = generateAccessToken($consumerKey, $consumerSecret);
                
                // Initiate M-Pesa payment
                $paymentResponse = initiatePayment($accessToken, $phoneNumber, $amount);

                if ($paymentResponse->ResponseCode == '0') {
                    // Payment request was successful, display thank you message and redirect to donor_dashboard.php
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
                                background-color: #FDF6EC;
                                font-family: Arial, sans-serif;
                                margin: 0;
                                color: #333;
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
                            <p>You will be redirected to the Dashboard shortly.</p>
                        </div>
                        <script>
                            setTimeout(function() {
                                window.location.href = 'donor_dashboard.php';
                            }, 5000); // Redirect after 5 seconds
                        </script>
                    </body>
                    </html>";
                } else {
                    echo "Error: Unable to process M-Pesa payment.";
                }
            } else {
                // Handle other payment methods (e.g., PayPal)
                // ...
            }
        } else {
            echo "Error: Unable to process donation.";
        }

        $stmt->close();
    } else {
        echo "Error: Donor ID not found in session.";
    }

    $database->close();
}
?>
