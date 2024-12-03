<?php

namespace App\Controllers;
use App\Models\Purchase;
use App\Models\PurchaseMaterial;
use App\Models\Supplier;
use Fpdf\Fpdf;

require_once __DIR__ . '/../../config/config.php';

class ExportController extends BaseController
{
    protected $twig;

    public function __construct($twig)
    {
        parent::__construct();  // Ensures session management and authentication
        $this->twig = $twig;
    }

    public function exportAllPurchase() {
        
        $purchaseMaterialObject = new PurchaseMaterial();
        $purchaseMaterialData = $purchaseMaterialObject->getAllPurchaseMaterial();
    
        // Create instance of FPDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
    
        // Header
        $pdf->Image(LOGO_URL, 10, 10, 30);
        $pdf->Cell(0, 10, 'PharmaSync Inc.', 0, 1, 'R');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'sales@pharmasync.com', 0, 1, 'R');
        $pdf->Cell(0, 10, '+63-190-597-235', 0, 1, 'R');
        $pdf->Cell(0, 10, 'ID: 1003', 0, 1, 'R');
        $pdf->Ln(20);
        
        // Table Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(164, 210, 255); // RGB for #2998FF
        $pdf->Cell(20, 10, 'ID', 1, 0, 'C', true);
        $pdf->Cell(50, 10, 'Vendor', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Count', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Date', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Total', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Status', 1, 0, 'C', true);
        $pdf->Ln();
    
        // Table Body
        $pdf->SetFont('Arial', '', 12);
        $fill = false; // Boolean flag for alternating row colors
        foreach ($purchaseMaterialData as $item) {
            $pdf->SetFillColor($fill ? 230 : 255, $fill ? 230 : 255, $fill ? 230 : 255); // Light grey color for alternate rows
            $pdf->Cell(20, 10, $item['purchase_id'], 1, 0, 'C', true);
            $pdf->Cell(50, 10, $item['vendor_name'], 1, 0, 'R', true);
            $pdf->Cell(30, 10, $item['material_count'], 1, 0, 'R', true);
            $pdf->Cell(30, 10, $item['date_of_purchase'], 1, 0, 'R', true);
            $pdf->Cell(30, 10, '$' . number_format($item['total_cost'], 2), 1, 0, 'R', true);
            $pdf->Cell(30, 10, ucwords($item['status']), 1, 0, 'R', true);
            $pdf->Ln();
            $fill = !$fill; // Toggle the fill flag
        }
    
        // Footer
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, '2024 Pharmasync. All Rights Reserved.', 0, 1, 'C');
    
        // Output the PDF
        $pdf->Output('D', 'Purchase_Report.pdf');
    }

    public function exportPurchaseByID($purchaseID) {
            
    }
}