<?php

namespace App\Controllers;
use App\Models\Purchase;
use App\Models\PurchaseMaterial;
use App\Models\OrderMedicine;
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
        $purchaseMaterialObject = new PurchaseMaterial();
        $purchaseData = $purchaseMaterialObject->getPurchaseData($purchaseID);
    
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
        $pdf->Ln(10);
    
        // Supplier Info
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Bill From:', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, $purchaseData[0]['supplier_name'], 0, 1);
        $pdf->Cell(0, 10, $purchaseData[0]['supplier_address'], 0, 1);
        $pdf->Cell(0, 10, $purchaseData[0]['supplier_email'], 0, 1);
        $pdf->Cell(0, 10, $purchaseData[0]['supplier_contact_no'], 0, 1);
    
        // Purchase Info
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Purchase ID: ' . $purchaseData[0]['purchase_id'], 0, 1, 'R');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Purchase date: ' . $purchaseData[0]['purchase_date'], 0, 1, 'R');
        $pdf->Cell(0, 10, 'Status: ' . ucfirst($purchaseData[0]['purchase_status']), 0, 1, 'R');
        $pdf->Ln(10);
    
        // Table Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(164, 210, 255); // RGB for #2998FF
        $pdf->Cell(70, 10, 'Material Name', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Quantity', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Unit Price', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Expiration', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Amount', 1, 0, 'C', true);
        $pdf->Ln();
    
        // Table Body
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(70, 10, $purchaseData[0]['material_name'], 1, 0, 'C', true);
        $pdf->Cell(30, 10, $purchaseData[0]['quantity'], 1, 0, 'C', true);
        $pdf->Cell(30, 10, '$' . number_format($purchaseData[0]['unit_price'], 2), 1, 0, 'C', true);
        $pdf->Cell(30, 10, $purchaseData[0]['expiration_date'], 1, 0, 'C', true);
        $pdf->Cell(30, 10, '$' . number_format($purchaseData[0]['material_total_price'], 2), 1, 0, 'C', true);
        $pdf->Ln();

        // Additional Details Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Additional Details', 0, 1, 'L');
        $pdf->Ln(5);

        // Additional Details
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 10, 'Lot ID:', 0, 0, 'L');
        $pdf->Cell(0, 10, $purchaseData[0]['lot_id'], 0, 1, 'R');
        $pdf->Cell(50, 10, 'Lot Number:', 0, 0, 'L');
        $pdf->Cell(0, 10, $purchaseData[0]['lot_number'], 0, 1, 'R');
        $pdf->Cell(50, 10, 'Material Description:', 0, 0, 'L');
        $pdf->Cell(0, 10, $purchaseData[0]['material_description'], 0, 1, 'R');
        $pdf->Cell(50, 10, 'QC Status:', 0, 0, 'L');
        $pdf->Cell(0, 10, ucfirst($purchaseData[0]['qc_status']), 0, 1, 'R');
        $pdf->Cell(50, 10, 'QC Notes:', 0, 0, 'L');
        $pdf->Cell(0, 10, $purchaseData[0]['qc_notes'], 0, 1, 'R');
        $pdf->Cell(50, 10, 'Inspection Date:', 0, 0, 'L');
        $pdf->Cell(0, 10, $purchaseData[0]['inspection_date'], 0, 1, 'R');
        $pdf->Ln(10);
    
        // Footer
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, '2024 Pharmasync. All Rights Reserved.', 0, 1, 'C');
    
        // Output the PDF
        $pdf->Output('D', 'Purchase_Report_' . $purchaseID . '.pdf');
    }

    public function exportAllOrder() {
        $orderMedicineObject = new OrderMedicine();
        $orderMedicineData = $orderMedicineObject->getAllOrderMedicines('all');
    
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
        $pdf->Cell(50, 10, 'Customer', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Count', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Date', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Total', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Status', 1, 0, 'C', true);
        $pdf->Ln();
    
        // Table Body
        $pdf->SetFont('Arial', '', 12);
        $fill = false; // Boolean flag for alternating row colors
        foreach ($orderMedicineData as $item) {
            $pdf->SetFillColor($fill ? 230 : 255, $fill ? 230 : 255, $fill ? 230 : 255); // Light grey color for alternate rows
            $pdf->Cell(20, 10, $item['order_id'], 1, 0, 'C', true);
            $pdf->Cell(50, 10, $item['customer_name'], 1, 0, 'R', true);
            $pdf->Cell(30, 10, $item['product_count'], 1, 0, 'R', true);
            $pdf->Cell(30, 10, $item['date_of_order'], 1, 0, 'R', true);
            $pdf->Cell(30, 10, '$' . number_format($item['total_cost'], 2), 1, 0, 'R', true);
            $pdf->Cell(30, 10, ucwords($item['order_status']), 1, 0, 'R', true);
            $pdf->Ln();
            $fill = !$fill; // Toggle the fill flag
        }
    
        // Footer
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, '2024 Pharmasync. All Rights Reserved.', 0, 1, 'C');
    
        // Output the PDF
        $pdf->Output('D', 'Order_Report.pdf');
    }

    // public function exportOrderByID($orderID) {

    // }
}