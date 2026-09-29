<?php
require('fpdf.php'); // Path

// Fetch the beneficiary data based on the search term
include 'connection.php'; // Ensure your database connection is included

if (isset($_GET['search_beneficiary']) && !empty($_GET['search_beneficiary'])) {
    $search_term = $_GET['search_beneficiary'];
    $like_search_term = '%' . $search_term . '%';
    $query = $database->prepare("SELECT * FROM beneficiaries WHERE first_name LIKE ? OR email LIKE ? OR phone_number LIKE ?");
    $query->bind_param('sss', $like_search_term, $like_search_term, $like_search_term);
} else {
    $query = $database->prepare("SELECT * FROM beneficiaries");
}

$query->execute();
$result = $query->get_result();

$beneficiaries = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $beneficiaries[] = $row;
    }
} else {
    die("Error fetching beneficiaries: " . $database->error);
}

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Beneficiaries List', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 10, 'ID', 1);
        $this->Cell(50, 10, 'Name', 1);
        $this->Cell(50, 10, 'Email', 1);
        $this->Cell(60, 10, 'Phone', 1);
        $this->Ln();
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

foreach ($beneficiaries as $beneficiary) {
    $pdf->Cell(30, 10, $beneficiary['beneficiary_id'], 1);
    $pdf->Cell(50, 10, $beneficiary['first_name'], 1);
    $pdf->Cell(50, 10, $beneficiary['email'], 1);
    $pdf->Cell(60, 10, $beneficiary['phone_number'], 1);
    $pdf->Ln();
}

$pdf->Output('D', 'beneficiaries_list.pdf'); // Forces download of the file
?>
