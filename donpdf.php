<?php

require 'vendor/autoload.php';
require 'fpdf.php'; // Include FPDF library
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// PayPal API credentials (replace with your actual sandbox credentials)
$clientId = 'AeqPkYMPvhqLxP_zyl1Q2Nk_0qYMaMJYTdeCjVJeI0eFnhDeukFlWDGNVlXjnJC5a8bD5W52iwtarIga';
$clientSecret = 'EItsYnEFngxJ1XvPay29LfeL5hkoG0A43QQ3XqVR_YoBHM1TisE62hhnqPlanut1BXwyGiAWqDnegts7';

// Function to get PayPal access token
function getAccessToken($clientId, $clientSecret) {
    $client = new Client();
    $url = 'https://api.sandbox.paypal.com/v1/oauth2/token';

    // Manually create the base64 encoded Authorization header
    $auth = base64_encode("$clientId:$clientSecret");

    try {
        $response = $client->post($url, [
            'headers' => [
                'Authorization' => "Basic $auth",
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
        ]);
        $data = json_decode((string)$response->getBody());
        return $data->access_token;
    } catch (RequestException $e) {
        echo "RequestException: " . $e->getMessage() . "<br>";
        if ($e->hasResponse()) {
            echo "Response: " . $e->getResponse()->getBody()->getContents() . "<br>";
        }
        return null;
    } catch (Exception $e) {
        echo "Exception: " . $e->getMessage() . "<br>";
        return null;
    }
}

// Function to get transaction status from PayPal
function getTransactionStatus($transactionId, $accessToken) {
    $client = new Client();
    try {
        $response = $client->get("https://api.sandbox.paypal.com/v1/payments/payment/{$transactionId}", [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
            ],
        ]);
        $data = json_decode((string)$response->getBody());
        return $data->state; // Assuming 'state' represents the payment status in PayPal's API
    } catch (RequestException $e) {
        $responseBody = $e->getResponse()->getBody()->getContents();
        $responseBody = json_decode($responseBody, true);
        if ($responseBody['name'] == 'INVALID_RESOURCE_ID') {
            echo "Transaction ID {$transactionId} not found in PayPal sandbox.<br>";
            return null;
        }
        echo "RequestException: " . $e->getMessage() . "<br>";
        if ($e->hasResponse()) {
            echo "Response: " . $responseBody . "<br>";
        }
        return null;
    } catch (Exception $e) {
        echo "Exception: " . $e->getMessage() . "<br>";
        return null;
    }
}

// Get PayPal access token
$accessToken = getAccessToken($clientId, $clientSecret);
if (!$accessToken) {
    echo "Error getting PayPal access token.";
    exit();
}

// Fetch donations details including donor names and payment status from the database
$search = $_GET['search'] ?? '';
$search = '%' . $search . '%';

$query = $database->prepare("
    SELECT d.donation_id, d.amount, d.donation_date, 
           d.created_at, d.updated_at, d.beneficiary_id, d.event_id, 
           d.transaction_id, d.payment_status, donors.first_name, donors.last_name 
    FROM donations d
    JOIN donors ON d.donorid = donors.donorid
    LEFT JOIN transactions t ON d.transaction_id = t.transaction_id
    WHERE d.amount LIKE ? OR d.donation_date LIKE ? OR d.payment_status LIKE ? 
          OR donors.first_name LIKE ? OR donors.last_name LIKE ?
");
$query->bind_param('sssss', $search, $search, $search, $search, $search);
$query->execute();
$result = $query->get_result();

$donations = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if (isset($row['transaction_id']) && !empty($row['transaction_id'])) {
            // Get the updated transaction status from PayPal
            $status = getTransactionStatus($row['transaction_id'], $accessToken);
            if ($status && $status != $row['payment_status']) {
                // Update the status in the database if different
                $stmt = $database->prepare("UPDATE donations SET payment_status = ? WHERE donation_id = ?");
                $stmt->bind_param("si", $status, $row['donation_id']);
                $stmt->execute();
                $row['payment_status'] = $status; // Update the status in the local array
            }
        }
        $donations[] = $row;
    }
} else {
    echo "Error fetching donations: " . $database->error;
    exit();
}


// Generate PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Add table headers
$pdf->Cell(60, 10, 'Donor Name', 1);
$pdf->Cell(40, 10, 'Amount', 1);
$pdf->Cell(40, 10, 'Donation Date', 1);
$pdf->Cell(50, 10, 'Status', 1);
$pdf->Ln();

// Add table data
foreach ($donations as $donation) {
    $pdf->Cell(60, 10, htmlspecialchars($donation['first_name'] . ' ' . $donation['last_name']), 1);
    $pdf->Cell(40, 10, htmlspecialchars($donation['amount']), 1);
    $pdf->Cell(40, 10, htmlspecialchars($donation['donation_date']), 1);
    $pdf->Cell(50, 10, htmlspecialchars($donation['payment_status']), 1);
    $pdf->Ln();
}

// Output the PDF
$pdf->Output('D', 'Donations_List.pdf');
exit();
