<?php
session_start();
include 'connection.php';

require('fpdf.php'); // Ensure FPDF library is included

// Fetch the donors' details
$search = $_GET['search'] ?? '';
$search = $database->real_escape_string($search);

$queryStr = "SELECT * FROM donors";
if ($search) {
    $queryStr .= " WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR donemail LIKE '%$search%' OR phone LIKE '%$search%' OR gender LIKE '%$search%' OR address LIKE '%$search%' ";
}

$query = $database->prepare($queryStr);
$query->execute();
$result = $query->get_result();

$donors = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $donors[] = $row;
    }
} else {
    echo "Error fetching donors: " . $database->error;
    exit();
}

// Create instance of FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Add a title
$pdf->Cell(0, 10, 'Donor List', 0, 1, 'C');

// Add table headers
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 10, 'ID', 1);
$pdf->Cell(50, 10, 'Name', 1);
$pdf->Cell(50, 10, 'Email', 1);
$pdf->Cell(30, 10, 'Phone', 1);

$pdf->Cell(20, 10, 'address', 1);

$pdf->Cell(20, 10, 'gender', 1);
$pdf->Ln();

// Add donor data
$pdf->SetFont('Arial', '', 10);
foreach ($donors as $donor) {
    $pdf->Cell(10, 10, htmlspecialchars($donor['donorid']), 1);
    $pdf->Cell(50, 10, htmlspecialchars($donor['first_name'] . ' ' . $donor['last_name']), 1);
    $pdf->Cell(50, 10, htmlspecialchars($donor['donemail']), 1);
    $pdf->Cell(30, 10, htmlspecialchars($donor['phone']), 1);
    $pdf->Cell(20, 10, htmlspecialchars($donor['address']), 1);
    $pdf->Cell(20, 10, htmlspecialchars($donor['gender']), 1);
    $pdf->Ln();
}

// Output PDF
$pdf->Output('D', 'Donor_List.pdf'); // 'D' for download
?>
