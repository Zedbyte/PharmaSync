<?php

namespace App\Controllers;
use App\Models\Purchase;
use App\Models\PurchaseMaterial;
use App\Models\OrderMedicine;
use App\Models\Supplier;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\Material;
use App\Models\MaterialLot;
use App\Models\Formulation;
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
        $fill = false; // Boolean flag for alternating row colors
        foreach ($purchaseData as $item) {
            $pdf->SetFillColor($fill ? 230 : 255, $fill ? 230 : 255, $fill ? 230 : 255); // Light grey color for alternate rows
            $pdf->Cell(70, 10, $item['material_name'], 1, 0, 'C', true);
            $pdf->Cell(30, 10, $item['quantity'], 1, 0, 'C', true);
            $pdf->Cell(30, 10, '$' . number_format($item['unit_price'], 2), 1, 0, 'C', true);
            $pdf->Cell(30, 10, $item['expiration_date'], 1, 0, 'C', true);
            $pdf->Cell(30, 10, '$' . number_format($item['material_total_price'], 2), 1, 0, 'C', true);
            $pdf->Ln();
            $fill = !$fill; // Toggle the fill flag
        }

        $pdf->AddPage();

        // Additional Details Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Additional Details', 0, 1, 'L');
        $pdf->Ln(5);

        // Additional Details
        $pdf->SetFont('Arial', '', 12);
        foreach ($purchaseData as $item) {
            $pdf->Cell(50, 10, 'Material:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['material_name'], 0, 1, 'R');
            $pdf->Ln(5);
            $pdf->Cell(50, 10, 'Lot ID:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['lot_id'], 0, 1, 'R');
            $pdf->Ln(5);
            $pdf->Cell(50, 10, 'Lot Number:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['lot_number'], 0, 1, 'R');
            $pdf->Ln(5);
            $pdf->Cell(50, 10, 'Material Description:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['material_description'], 0, 1, 'R');
            $pdf->Ln(5);
            $pdf->Cell(50, 10, 'QC Status:', 0, 0, 'L');
            $pdf->Cell(0, 10, ucfirst($item['qc_status']), 0, 1, 'R');
            $pdf->Ln(5);
            $pdf->Cell(50, 10, 'QC Notes:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['qc_notes'], 0, 1, 'R');
            $pdf->Ln(5);
            $pdf->Cell(50, 10, 'Inspection Date:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['inspection_date'], 0, 1, 'R');
            $pdf->Ln(5);
            $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
            $pdf->Ln(5);
        }
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

    public function exportOrderByID($orderID) {
        $orderMedicineObject = new OrderMedicine();
        $orderData = $orderMedicineObject->getOrderData($orderID);
    
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
    
        // Customer Info
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Bill To:', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, $orderData[0]['customer_name'], 0, 1);
        $pdf->Cell(0, 10, $orderData[0]['customer_address'], 0, 1);
        $pdf->Cell(0, 10, $orderData[0]['customer_email'], 0, 1);
        $pdf->Cell(0, 10, $orderData[0]['customer_contact_no'], 0, 1);
    
        // Order Info
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Order ID: ' . $orderData[0]['order_id'], 0, 1, 'R');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Order date: ' . $orderData[0]['order_date'], 0, 1, 'R');
        $pdf->Cell(0, 10, 'Status: ' . ucfirst($orderData[0]['order_status']), 0, 1, 'R');
        $pdf->Ln(10);
    
        // Table Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(164, 210, 255); // RGB for #2998FF
        $pdf->Cell(70, 10, 'Medicine Name', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Quantity', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Unit Price', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Expiration', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Amount', 1, 0, 'C', true);
        $pdf->Ln();
    
        // Table Body
        $pdf->SetFont('Arial', '', 12);
        $fill = false; // Boolean flag for alternating row colors
        foreach ($orderData as $item) {
            $pdf->SetFillColor($fill ? 230 : 255, $fill ? 230 : 255, $fill ? 230 : 255); // Light grey color for alternate rows
            $pdf->Cell(70, 10, $item['medicine_name'], 1, 0, 'C', true);
            $pdf->Cell(30, 10, $item['ordered_quantity'], 1, 0, 'C', true);
            $pdf->Cell(30, 10, '$' . number_format($item['medicine_unit_price'], 2), 1, 0, 'C', true);
            $pdf->Cell(30, 10, $item['batch_expiry_date'], 1, 0, 'C', true);
            $pdf->Cell(30, 10, '$' . number_format($item['medicine_total_price'], 2), 1, 0, 'C', true);
            $pdf->Ln();
            $fill = !$fill; // Toggle the fill flag
        }
    
        $pdf->AddPage();

        // Additional Details Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Additional Details', 0, 1, 'L');
        $pdf->Ln(5);
    
        // Additional Details
        $pdf->SetFont('Arial', '', 12);
        foreach ($orderData as $item) {
            $pdf->Cell(50, 10, 'Medicine:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['medicine_name'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Batch ID:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['batch_id'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Medicine Type:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['medicine_type'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Composition:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['composition'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Therapeutic Class:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['therapeutic_class'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Regulatory Class:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['regulatory_class'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Manufacturing Details:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['manufacturing_details'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Production Date:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['production_date'], 0, 1, 'R');
            $pdf->Ln(5);
            $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
            $pdf->Ln(5);
        }
        $pdf->Ln(10);
    
        // Footer
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, '2024 Pharmasync. All Rights Reserved.', 0, 1, 'C');
    
        // Output the PDF
        $pdf->Output('D', 'Order_Report_' . $orderID . '.pdf');
    }

    public function exportAllManufactured() {
        $medicineObject = new Medicine();
        $medicineBatchObject = new MedicineBatch();
        // Fetch all medicines
        $medicineData = $medicineObject->getAllMedicines();
    
        // Merge batches with medicines
        foreach ($medicineData as &$medicine) {
            $medicine['batches'] = $medicineBatchObject->getBatchMedicinesAndBatchData($medicine['id'], null);
        }
    
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
    
        // Table Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(164, 210, 255); // RGB for #2998FF
        $pdf->Cell(40, 10, 'Medicine Name', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Type', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Composition', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Therapeutic Class', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Regulatory Class', 1, 0, 'C', true);
        $pdf->Ln();
    
        // Table Body
        $pdf->SetFont('Arial', '', 8);
        $fill = false; // Boolean flag for alternating row colors
        foreach ($medicineData as $medicine) {
            $pdf->SetFillColor($fill ? 230 : 255, $fill ? 230 : 255, $fill ? 230 : 255); // Light grey color for alternate rows
            $pdf->Cell(40, 10, $medicine['name'], 1, 0, 'C', true);
            $pdf->Cell(30, 10, $medicine['type'], 1, 0, 'C', true);
            $pdf->Cell(40, 10, $medicine['composition'], 1, 0, 'C', true);
            $pdf->Cell(40, 10, $medicine['therapeutic_class'], 1, 0, 'C', true);
            $pdf->Cell(40, 10, $medicine['regulatory_class'], 1, 0, 'C', true);
            $pdf->Ln();
            $fill = !$fill; // Toggle the fill flag
        }
    
        $pdf->AddPage();
    
        // Additional Details Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Additional Details', 0, 1, 'L');
        $pdf->Ln(5);
    
        // Additional Details
        $pdf->SetFont('Arial', '', 12);
        foreach ($medicineData as $medicine) {
            foreach ($medicine['batches'] as $batch) {
                $pdf->Cell(50, 10, 'Medicine:', 0, 0, 'L');
                $pdf->Cell(0, 10, $medicine['name'], 0, 1, 'R');
                $pdf->Cell(50, 10, 'Batch ID:', 0, 0, 'L');
                $pdf->Cell(0, 10, $batch['batch_id'], 0, 1, 'R');
                $pdf->Cell(50, 10, 'Stock Level:', 0, 0, 'L');
                $pdf->Cell(0, 10, $batch['stock_level'], 0, 1, 'R');
                $pdf->Cell(50, 10, 'Expiry Date:', 0, 0, 'L');
                $pdf->Cell(0, 10, $batch['expiry_date'], 0, 1, 'R');
                $pdf->Cell(50, 10, 'Production Date:', 0, 0, 'L');
                $pdf->Cell(0, 10, $batch['production_date'], 0, 1, 'R');
                $pdf->Cell(50, 10, 'Rack ID:', 0, 0, 'L');
                $pdf->Cell(0, 10, $batch['rack_id'], 0, 1, 'R');
                $pdf->Ln(5);
                $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Draw line
                $pdf->Ln(5);
            }
        }
        $pdf->Ln(10);
    
        // Footer
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, '2024 Pharmasync. All Rights Reserved.', 0, 1, 'C');
    
        // Output the PDF
        $pdf->Output('D', 'Manufactured_Report.pdf');
    }

    public function exportManufacturedByBatchAndMedicineID($medicineID, $batchID) {
        $medicineBatchObject = new MedicineBatch();
        $medicineBatchData = $medicineBatchObject->getMedicineBatchData($medicineID, $batchID);
    
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
    
        // Medicine Info
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Medicine Info:', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Name: ' . $medicineBatchData[0]['name'], 0, 1);
        $pdf->Cell(0, 10, 'Type: ' . $medicineBatchData[0]['type'], 0, 1);
        $pdf->Cell(0, 10, 'Composition: ' . $medicineBatchData[0]['composition'], 0, 1);
        $pdf->Cell(0, 10, 'Therapeutic Class: ' . $medicineBatchData[0]['therapeutic_class'], 0, 1);
        $pdf->Cell(0, 10, 'Regulatory Class: ' . $medicineBatchData[0]['regulatory_class'], 0, 1);
        $pdf->Cell(0, 10, 'Manufacturing Details: ' . $medicineBatchData[0]['manufacturing_details'], 0, 1);
        $pdf->Ln(10);
    
        // Table Header
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(164, 210, 255); // RGB for #2998FF
        $pdf->Cell(20, 10, 'Batch ID', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Stock Level', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Expiry Date', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Production Date', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Location', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Temperature Controlled', 1, 0, 'C', true);
        $pdf->Ln();
    
        // Table Body
        $pdf->SetFont('Arial', '', 8);
        $fill = false; // Boolean flag for alternating row colors
        foreach ($medicineBatchData as $item) {
            $pdf->SetFillColor($fill ? 230 : 255, $fill ? 230 : 255, $fill ? 230 : 255); // Light grey color for alternate rows
            $pdf->Cell(20, 10, $item['batch_id'], 1, 0, 'C', true);
            $pdf->Cell(30, 10, $item['stock_level'], 1, 0, 'C', true);
            $pdf->Cell(30, 10, $item['expiry_date'], 1, 0, 'C', true);
            $pdf->Cell(30, 10, $item['production_date'], 1, 0, 'C', true);
            $pdf->Cell(40, 10, $item['location'], 1, 0, 'C', true);
            $pdf->Cell(40, 10, $item['temperature_controlled'] ? 'Yes' : 'No', 1, 0, 'C', true);
            $pdf->Ln();
            $fill = !$fill; // Toggle the fill flag
        }
    
        $pdf->AddPage();
    
        // Additional Details Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Additional Details', 0, 1, 'L');
        $pdf->Ln(5);
    
        // Additional Details
        $pdf->SetFont('Arial', '', 12);
        foreach ($medicineBatchData as $item) {
            $pdf->Cell(50, 10, 'Medicine:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['name'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Batch ID:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['batch_id'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Medicine Type:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['type'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Composition:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['composition'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Therapeutic Class:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['therapeutic_class'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Regulatory Class:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['regulatory_class'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Manufacturing Details:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['manufacturing_details'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Production Date:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['production_date'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Location:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['location'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Temperature Controlled:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['temperature_controlled'] ? 'Yes' : 'No', 0, 1, 'R');
            $pdf->Ln(5);
            $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Draw line
            $pdf->Ln(5);
        }
        $pdf->Ln(10);
    
        // Footer
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, '2024 Pharmasync. All Rights Reserved.', 0, 1, 'C');
    
        // Output the PDF
        $pdf->Output('D', 'Manufactured_Report_' . $medicineID . '_' . $batchID . '.pdf');
    }


    public function exportAllRaw() {
        $materialObject = new Material();
        $materialData = $materialObject->getAllMaterials();
    
        $materialLotObject = new MaterialLot();
        
        foreach ($materialData as &$material) {
            $material['lots'] = $materialLotObject->getMaterialLotsAndLotData($material['id'], null);
        }
    
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
    
        // Table Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(164, 210, 255); // RGB for #2998FF
        $pdf->Cell(60, 10, 'Material Name', 1, 0, 'C', true);
        $pdf->Cell(70, 10, 'Description', 1, 0, 'C', true);
        $pdf->Cell(50, 10, 'Type', 1, 0, 'C', true);
        $pdf->Ln();
    
        // Table Body
        $pdf->SetFont('Arial', '', 12);
        $fill = false; // Boolean flag for alternating row colors
        foreach ($materialData as $material) {
            $pdf->SetFillColor($fill ? 230 : 255, $fill ? 230 : 255, $fill ? 230 : 255); // Light grey color for alternate rows
            $pdf->Cell(60, 10, $material['name'], 1, 0, 'C', true);
            $pdf->Cell(70, 10, $material['description'], 1, 0, 'C', true);
            $pdf->Cell(50, 10, ucwords($material['material_type'] . " Material"), 1, 0, 'C', true);
            $pdf->Ln();
            $fill = !$fill; // Toggle the fill flag
        }
    
        $pdf->AddPage();
    
        // Additional Details Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Additional Details', 0, 1, 'L');
        $pdf->Ln(5);
    
        // Additional Details
        $pdf->SetFont('Arial', '', 12);
        foreach ($materialData as $material) {
            foreach ($material['lots'] as $lot) {
                $pdf->Cell(50, 10, 'Material:', 0, 0, 'L');
                $pdf->Cell(0, 10, $material['name'], 0, 1, 'R');
                $pdf->Cell(50, 10, 'Lot ID:', 0, 0, 'L');
                $pdf->Cell(0, 10, $lot['lot_id'], 0, 1, 'R');
                $pdf->Cell(50, 10, 'Stock Level:', 0, 0, 'L');
                $pdf->Cell(0, 10, $lot['stock_level'], 0, 1, 'R');
                $pdf->Cell(50, 10, 'QC Status:', 0, 0, 'L');
                $pdf->Cell(0, 10, ucfirst($lot['qc_status']), 0, 1, 'R');
                $pdf->Cell(50, 10, 'QC Notes:', 0, 0, 'L');
                $pdf->Cell(0, 10, $lot['qc_notes'], 0, 1, 'R');
                $pdf->Cell(50, 10, 'Inspection Date:', 0, 0, 'L');
                $pdf->Cell(0, 10, $lot['inspection_date'], 0, 1, 'R');
                $pdf->Cell(50, 10, 'Expiration Date:', 0, 0, 'L');
                $pdf->Cell(0, 10, $lot['expiration_date'], 0, 1, 'R');
                $pdf->Cell(50, 10, 'Production Date:', 0, 0, 'L');
                $pdf->Cell(0, 10, $lot['production_date'], 0, 1, 'R');
                $pdf->Cell(50, 10, 'Lot Number:', 0, 0, 'L');
                $pdf->Cell(0, 10, $lot['number'], 0, 1, 'R');
                $pdf->Cell(50, 10, 'Supplier Name:', 0, 0, 'L');
                $pdf->Cell(0, 10, $lot['supplier_name'], 0, 1, 'R');
                $pdf->Ln(5);
                $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Draw line
                $pdf->Ln(5);
            }
        }
        $pdf->Ln(10);
    
        // Footer
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, '2024 Pharmasync. All Rights Reserved.', 0, 1, 'C');
    
        // Output the PDF
        $pdf->Output('D', 'Raw_Materials_Report.pdf');
    }

    public function exportRawByMaterialID($materialId) {
        $materialObject = new Material();
        $materialData = $materialObject->getMaterialLotSupplierData($materialId);
    
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
    
        // Material Info
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Material Info:', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Name: ' . $materialData[0]['name'], 0, 1);
        $pdf->Cell(0, 10, 'Description: ' . $materialData[0]['description'], 0, 1);
        $pdf->Cell(0, 10, 'Type: ' . $materialData[0]['material_type'], 0, 1);
        $pdf->Ln(10);
    
        // Supplier Info
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Supplier Info:', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Name: ' . $materialData[0]['supplier_name'], 0, 1);
        $pdf->Cell(0, 10, 'Email: ' . $materialData[0]['email'], 0, 1);
        $pdf->Cell(0, 10, 'Address: ' . $materialData[0]['address'], 0, 1);
        $pdf->Cell(0, 10, 'Contact No: ' . $materialData[0]['contact_no'], 0, 1);
        $pdf->Ln(10);
    
        // Table Header
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(164, 210, 255); // RGB for #2998FF
        $pdf->Cell(30, 10, 'Lot ID', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Stock Level', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'QC Status', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'QC Notes', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Inspection Date', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Expiration Date', 1, 0, 'C', true);
        $pdf->Ln();
    
        // Table Body
        $pdf->SetFont('Arial', '', 10);
        $fill = false; // Boolean flag for alternating row colors
        foreach ($materialData as $item) {
            $pdf->SetFillColor($fill ? 230 : 255, $fill ? 230 : 255, $fill ? 230 : 255); // Light grey color for alternate rows
            $pdf->Cell(30, 10, $item['lot_id'], 1, 0, 'C', true);
            $pdf->Cell(30, 10, $item['stock_level'], 1, 0, 'C', true);
            $pdf->Cell(30, 10, ucfirst($item['qc_status']), 1, 0, 'C', true);
            $pdf->Cell(30, 10, $item['qc_notes'], 1, 0, 'C', true);
            $pdf->Cell(30, 10, $item['inspection_date'], 1, 0, 'C', true);
            $pdf->Cell(30, 10, $item['expiration_date'], 1, 0, 'C', true);
            $pdf->Ln();
            $fill = !$fill; // Toggle the fill flag
        }
    
        $pdf->AddPage();
    
        // Additional Details Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Additional Details', 0, 1, 'L');
        $pdf->Ln(5);
    
        // Additional Details
        $pdf->SetFont('Arial', '', 12);
        foreach ($materialData as $item) {
            $pdf->Cell(50, 10, 'Material:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['name'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Lot ID:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['lot_id'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Stock Level:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['stock_level'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'QC Status:', 0, 0, 'L');
            $pdf->Cell(0, 10, ucfirst($item['qc_status']), 0, 1, 'R');
            $pdf->Cell(50, 10, 'QC Notes:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['qc_notes'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Inspection Date:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['inspection_date'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Expiration Date:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['expiration_date'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Production Date:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['production_date'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Lot Number:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['number'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Supplier Name:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['supplier_name'], 0, 1, 'R');
            $pdf->Ln(5);
            $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Draw line
            $pdf->Ln(5);
        }
        $pdf->Ln(10);
    
        // Footer
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, '2024 Pharmasync. All Rights Reserved.', 0, 1, 'C');
    
        // Output the PDF
        $pdf->Output('D', 'Raw_Material_Report_' . $materialId . '.pdf');
    }

    public function exportRawByMaterialAndLotID($lotID, $materialID) {
        $materialObject = new MaterialLot();
        $LotMaterialData = $materialObject->getLotMaterialData($lotID, $materialID);
    
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
    
        // Material Info
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Material Info:', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Name: ' . $LotMaterialData[0]['name'], 0, 1);
        $pdf->Cell(0, 10, 'Description: ' . $LotMaterialData[0]['description'], 0, 1);
        $pdf->Cell(0, 10, 'Type: ' . $LotMaterialData[0]['material_type'], 0, 1);
        $pdf->Ln(10);
    
        // Supplier Info
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Supplier Info:', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Name: ' . $LotMaterialData[0]['supplier_name'], 0, 1);
        $pdf->Cell(0, 10, 'Email: ' . $LotMaterialData[0]['email'], 0, 1);
        $pdf->Cell(0, 10, 'Address: ' . $LotMaterialData[0]['address'], 0, 1);
        $pdf->Cell(0, 10, 'Contact No: ' . $LotMaterialData[0]['contact_no'], 0, 1);
        $pdf->Ln(10);
    
        // Table Header
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(164, 210, 255); // RGB for #2998FF
        $pdf->Cell(30, 10, 'Lot ID', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Stock Level', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'QC Status', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'QC Notes', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Inspection Date', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Expiration Date', 1, 0, 'C', true);
        $pdf->Ln();
    
        // Table Body
        $pdf->SetFont('Arial', '', 10);
        $fill = false; // Boolean flag for alternating row colors
        foreach ($LotMaterialData as $item) {
            $pdf->SetFillColor($fill ? 230 : 255, $fill ? 230 : 255, $fill ? 230 : 255); // Light grey color for alternate rows
            $pdf->Cell(30, 10, $item['lot_id'], 1, 0, 'C', true);
            $pdf->Cell(30, 10, $item['stock_level'], 1, 0, 'C', true);
            $pdf->Cell(30, 10, ucfirst($item['qc_status']), 1, 0, 'C', true);
            $pdf->Cell(30, 10, $item['qc_notes'], 1, 0, 'C', true);
            $pdf->Cell(30, 10, $item['inspection_date'], 1, 0, 'C', true);
            $pdf->Cell(30, 10, $item['expiration_date'], 1, 0, 'C', true);
            $pdf->Ln();
            $fill = !$fill; // Toggle the fill flag
        }
    
        $pdf->AddPage();
    
        // Additional Details Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Additional Details', 0, 1, 'L');
        $pdf->Ln(5);
    
        // Additional Details
        $pdf->SetFont('Arial', '', 12);
        foreach ($LotMaterialData as $item) {
            $pdf->Cell(50, 10, 'Material:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['name'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Lot ID:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['lot_id'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Stock Level:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['stock_level'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'QC Status:', 0, 0, 'L');
            $pdf->Cell(0, 10, ucfirst($item['qc_status']), 0, 1, 'R');
            $pdf->Cell(50, 10, 'QC Notes:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['qc_notes'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Inspection Date:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['inspection_date'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Expiration Date:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['expiration_date'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Production Date:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['production_date'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Lot Number:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['number'], 0, 1, 'R');
            $pdf->Cell(50, 10, 'Supplier Name:', 0, 0, 'L');
            $pdf->Cell(0, 10, $item['supplier_name'], 0, 1, 'R');
            $pdf->Ln(5);
            $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Draw line
            $pdf->Ln(5);
        }
        $pdf->Ln(10);
    
        // Footer
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, '2024 Pharmasync. All Rights Reserved.', 0, 1, 'C');
    
        // Output the PDF
        $pdf->Output('D', 'Raw_Material_Report_' . $materialID . '_' . $lotID . '.pdf');
    }

    public function exportAllMedicine() {
        $medicineObject = new Medicine();
        $formulationObject = new Formulation();
    
        $medicineData = $medicineObject->getAllMedicines();
    
        foreach ($medicineData as &$medicine) {
            $medicine['formulations'] = $formulationObject->getFormulationByMedicine($medicine['id']);
        }
    
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
    
        // Table Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(164, 210, 255); // RGB for #2998FF
        $pdf->Cell(40, 10, 'Medicine Name', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Type', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Composition', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Therapeutic Class', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Regulatory Class', 1, 0, 'C', true);
        $pdf->Ln();
    
        // Table Body
        $pdf->SetFont('Arial', '', 8);
        $fill = false; // Boolean flag for alternating row colors
        foreach ($medicineData as $medicine) {
            $pdf->SetFillColor($fill ? 230 : 255, $fill ? 230 : 255, $fill ? 230 : 255); // Light grey color for alternate rows
            $pdf->Cell(40, 10, $medicine['name'], 1, 0, 'C', true);
            $pdf->Cell(30, 10, $medicine['type'], 1, 0, 'C', true);
            $pdf->Cell(40, 10, $medicine['composition'], 1, 0, 'C', true);
            $pdf->Cell(40, 10, $medicine['therapeutic_class'], 1, 0, 'C', true);
            $pdf->Cell(40, 10, $medicine['regulatory_class'], 1, 0, 'C', true);
            $pdf->Ln();
            $fill = !$fill; // Toggle the fill flag
        }
    
        $pdf->AddPage();
    
        // Additional Details Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Additional Details', 0, 1, 'L');
        $pdf->Ln(5);
    
        // Additional Details
        $pdf->SetFont('Arial', '', 12);
        foreach ($medicineData as $medicine) {
            foreach ($medicine['formulations'] as $formulation) {
                $pdf->Cell(50, 10, 'Medicine:', 0, 0, 'L');
                $pdf->Cell(0, 10, $medicine['name'], 0, 1, 'R');
                $pdf->Cell(50, 10, 'Formulation ID:', 0, 0, 'L');
                $pdf->Cell(0, 10, $formulation['formulation_id'], 0, 1, 'R');
                $pdf->Cell(50, 10, 'Quantity Required:', 0, 0, 'L');
                $pdf->Cell(0, 10, $formulation['quantity_required'] . ' ' . $formulation['unit'], 0, 1, 'R');
                $pdf->Cell(50, 10, 'Description:', 0, 0, 'L');
                $pdf->Cell(0, 10, $formulation['description'], 0, 1, 'R');
                $pdf->Cell(50, 10, 'Material Name:', 0, 0, 'L');
                $pdf->Cell(0, 10, $formulation['material_name'], 0, 1, 'R');
                $pdf->Cell(50, 10, 'Material Type:', 0, 0, 'L');
                $pdf->Cell(0, 10, ucwords($formulation['material_type']) . " Material", 0, 1, 'R');
                $pdf->Ln(5);
                $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Draw line
                $pdf->Ln(5);
            }
        }
        $pdf->Ln(10);
    
        // Footer
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, '2024 Pharmasync. All Rights Reserved.', 0, 1, 'C');
    
        // Output the PDF
        $pdf->Output('D', 'Medicine_Report.pdf');
    }

    public function exportMedicineByID($medicineID) {
        $medicineObject = new Medicine();
        $medicineData = $medicineObject->getMedicine($medicineID);
    
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
    
        // Medicine Info
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Medicine Info:', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Name: ' . $medicineData['name'], 0, 1);
        $pdf->Cell(0, 10, 'Type: ' . $medicineData['type'], 0, 1);
        $pdf->Cell(0, 10, 'Composition: ' . $medicineData['composition'], 0, 1);
        $pdf->Cell(0, 10, 'Therapeutic Class: ' . $medicineData['therapeutic_class'], 0, 1);
        $pdf->Cell(0, 10, 'Regulatory Class: ' . $medicineData['regulatory_class'], 0, 1);
        $pdf->Cell(0, 10, 'Manufacturing Details: ' . $medicineData['manufacturing_details'], 0, 1);
        $pdf->Ln(10);
    
        // Table Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(164, 210, 255); // RGB for #2998FF
        $pdf->Cell(30, 10, 'Unit Price', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Name', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Type', 1, 0, 'C', true);
        $pdf->Cell(50, 10, 'Therapeutic Class', 1, 0, 'C', true);
        $pdf->Cell(50, 10, 'Regulatory Class', 1, 0, 'C', true);
        $pdf->Ln();
    
        // Table Body
        $pdf->SetFont('Arial', '', 12);
        $fill = false; // Boolean flag for alternating row colors
        $pdf->SetFillColor($fill ? 230 : 255, $fill ? 230 : 255, $fill ? 230 : 255); // Light grey color for alternate rows
        $pdf->Cell(30, 10, '$' . number_format($medicineData['unit_price'], 2), 1, 0, 'C', true);
        $pdf->Cell(30, 10, $medicineData['name'], 1, 0, 'C', true);
        $pdf->Cell(30, 10, $medicineData['type'], 1, 0, 'C', true);
        $pdf->Cell(50, 10, $medicineData['therapeutic_class'], 1, 0, 'C', true);
        $pdf->Cell(50, 10, $medicineData['regulatory_class'], 1, 0, 'C', true);
        $pdf->Ln();
        $fill = !$fill; // Toggle the fill flag
    
        $pdf->AddPage();
    
        // Additional Details Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Additional Details', 0, 1, 'L');
        $pdf->Ln(5);
    
        // Additional Details
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 10, 'Medicine:', 0, 0, 'L');
        $pdf->Cell(0, 10, $medicineData['name'], 0, 1, 'R');
        $pdf->Cell(50, 10, 'Type:', 0, 0, 'L');
        $pdf->Cell(0, 10, $medicineData['type'], 0, 1, 'R');
        $pdf->Cell(50, 10, 'Composition:', 0, 0, 'L');
        $pdf->Cell(0, 10, $medicineData['composition'], 0, 1, 'R');
        $pdf->Cell(50, 10, 'Therapeutic Class:', 0, 0, 'L');
        $pdf->Cell(0, 10, $medicineData['therapeutic_class'], 0, 1, 'R');
        $pdf->Cell(50, 10, 'Regulatory Class:', 0, 0, 'L');
        $pdf->Cell(0, 10, $medicineData['regulatory_class'], 0, 1, 'R');
        $pdf->Cell(50, 10, 'Manufacturing Details:', 0, 0, 'L');
        $pdf->Cell(0, 10, $medicineData['manufacturing_details'], 0, 1, 'R');
        $pdf->Ln(5);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Draw line
        $pdf->Ln(5);
    
        $pdf->Ln(10);
    
        // Footer
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, '2024 Pharmasync. All Rights Reserved.', 0, 1, 'C');
    
        // Output the PDF
        $pdf->Output('D', 'Medicine_Report_' . $medicineID . '.pdf');
    }
}