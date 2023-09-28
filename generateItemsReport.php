<?php

require "../fpdf/fpdf.php"; // Assuming fpdf.php is in the same directory as generateReport.php
require "Database.php";

//create new instance of Database class so that we can access its methods
$db = new Database();

class PDFReport extends FPDF {
    // Add any custom methods or configurations for your PDF reports here

    public function generateReport($data) {
        // Create a new PDF document
        $pageWidth = 350; 
        $pageHeight = 500;

        $this->AddPage('P', array($pageWidth, $pageHeight));

        // Set font and size for the report
        $this->SetFont('Arial', 'B', 16);

        // Add a title to the report
        $this->Cell(0, 10, 'Item Report', 0, 1, 'C');

        // Set font and size for the table header
        $this->SetFont('Arial', 'B', 12);

        // Add table headers
        $this->Cell(30, 10, 'Item ID', 1);
        $this->Cell(40, 10, 'Supplier Name', 1);
        $this->Cell(40, 10, 'Item Name', 1);
        $this->Cell(30, 10, 'Price per Unit', 1);
        $this->Cell(190, 10, 'Item Description', 1);
        $this->Ln();

        // Set font and size for the table content
        $this->SetFont('Arial', '', 12);

        // Add table rows from the data passed to the method
        foreach ($data as $row) {
            $this->Cell(30, 10, $row['supplierItemId'], 1);
            $this->Cell(40, 10, $row['supplierName'], 1);
            $this->Cell(40, 10, $row['supplierItemName'], 1);
            $this->Cell(30, 10, $row['supplierItemPrice'], 1);
            $this->Cell(190, 10, $row['supplierItemDescription'], 1);
            $this->Ln();
        }

    }
}

if (isset($_POST['supplierId'])) {
    $supplierId = $_POST['supplierId'];
    if ($db->dbConnect()) {
        $data = $db->itemReportData("suppliers_items", $supplierId);

        // Instantiate the PDFReport class
        $pdfReport = new PDFReport();

        // Generate the PDF report with the data retrieved from the database
        $pdfReport->generateReport($data);

        // Save the PDF to a file named "Purchasereport.pdf" in the server's directory
        $filePath = 'Itemsreport.pdf';
        $pdfReport->Output('F', $filePath);

        // Get the server domain and protocol dynamically
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $serverDomain = $_SERVER['HTTP_HOST'];

        // Construct the public URL for the PDF with the correct path
        $publicPdfUrl = $protocol . $serverDomain . '/PrototypeApi/Itemsreport.pdf';

        // Return the public URL to the frontend as a JSON response without escaping slashes
        echo json_encode(array(
            'pdf_url' => $publicPdfUrl
        ), JSON_UNESCAPED_SLASHES);

    } else {
        echo "Error: Database connection";
    }
} else {
    echo "Error: 'supplierId' parameter is missing in the request.";
}



?>