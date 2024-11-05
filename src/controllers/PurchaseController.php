<?php

namespace App\Controllers;

use App\Models\PurchaseMaterial;
use App\Models\Purchase;
use App\Models\Material;
use App\Models\Supplier;

use \Exception;

require_once __DIR__ . '/../../config/config.php';
require_once LIB_URL . '/get_dbcon.inc.php';
require_once 'BaseController.php';

class PurchaseController extends BaseController {
    protected $twig;
    protected $db;

    // Constructor to pass Twig environment for rendering templates
    public function __construct($twig) {
        parent::__construct();
        $this->twig = $twig;
        $this->db = returnDBCon(new PurchaseMaterial()); // Initialize DB connection once
    }
    public function display() {
        // POST-Redirect-GET (PRG) PATTERN
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Collect and sanitize POST data
            $startDate = $_POST['start_date'] ?? null;
            $endDate = $_POST['end_date'] ?? null;
            $relativeDate = $_POST['relativeDate'] ?? null;
            $entryValue = isset($_POST['entryValue']) && is_numeric($_POST['entryValue']) && $_POST['entryValue'] > 0 ? (int) $_POST['entryValue'] : 5;

            // Redirect with the filters as query parameters
            $queryParams = [];
            if ($startDate) $queryParams['start_date'] = $startDate;
            if ($endDate) $queryParams['end_date'] = $endDate;
            if ($relativeDate) $queryParams['relativeDate'] = $relativeDate;
            if ($entryValue) $queryParams['entryValue'] = $entryValue;
    
            header('Location: /purchase-list?' . http_build_query($queryParams));
            exit;
        }
    
        // Retrieve filter values from query parameters (GET)
        $startDate = $_GET['start_date'] ?? '2024-01-01';
        $endDate = $_GET['end_date']  ?? '2024-12-31';

        $relativeDate = $_GET['relativeDate'] ?? 7;
        $entryValue = isset($_GET['entryValue']) && is_numeric($_GET['entryValue']) ? (int) $_GET['entryValue'] : 5;

    
        // Load data models based on filters
        $supplierObject = new Supplier();
        $supplierData = $supplierObject->getAllSuppliers();
    
        $purchaseMaterialObject = new PurchaseMaterial();
        $purchaseMaterialData = $purchaseMaterialObject->getAllPurchaseMaterial($entryValue, $startDate, $endDate, $relativeDate);

        $purchaseObject = new Purchase();
        $purchaseCount = $purchaseObject->getCount();
    
        // Pass data and filter values to Twig template for rendering
        echo $this->twig->render('purchase-list.html.twig', [
            'ASSETS_URL' => ASSETS_URL,
            'suppliers' => $supplierData,
            'purchaseMaterials' => $purchaseMaterialData,
            'purchaseCount' => $purchaseCount['purchaseCount'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'relativeDate' => $relativeDate,
            'entryValue' => $entryValue
        ]);
    }
    
    

    public function addPurchaseMaterial($data) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->db->beginTransaction();

                $purchaseID = $this->addPurchase($data);
                $materialIDs = $this->addMaterials($data);

                foreach ($data['material_name'] as $index => $materialName) {
                    (new PurchaseMaterial())->save([
                        'pm_purchase_id' => $purchaseID,
                        'pm_material_id' => $materialIDs[$index],
                        'quantity' => $data['quantity'][$index],
                        'unit_price' => $data['unit_price'][$index],
                        'total_price' => $data['quantity'][$index] * $data['unit_price'][$index],
                        'batch_number' => $data['batch_number'][$index]
                    ]);
                }

                $this->db->commit();

                header("Location: /purchase-list");
                exit();

            } catch (Exception $e) {
                $this->db->rollBack();
                error_log($e->getMessage());
                throw new Exception("Failed to save purchase material: " . $e->getMessage());
            }
        }
    }

    private function addPurchase($data) {
        $purchaseObject = new Purchase();
        
        $purchaseID = $purchaseObject->save([
            'date' => $data['purchase_date'],
            'material_count' => count($data['material_name']),
            'total_cost' => array_sum(array_map(function($quantity, $unitPrice) {
                return $quantity * $unitPrice;
            }, $data['quantity'], $data['unit_price'])),
            'status' => $data['status'],
            'p_supplier_id' => $data['vendor']
        ]);

        return $purchaseID;
    }

    private function addMaterials($data) {
        $materialObject = new Material();
        $materialIDs = [];

        foreach ($data['material_name'] as $index => $materialName) {
            $materialID = $materialObject->save([
                'name' => $materialName,
                'description' => $data['description'][$index],
                'material_type' => $data['material_type'][$index],
                'expiration_date' => $data['expiry_date'][$index],
                'qc_status' => $data['qc_status'][$index],
                'inspection_date' => $data['inspection_date'][$index],
                'qc_notes' => $data['qc_notes'][$index]
            ]);
            $materialIDs[] = $materialID;
        }

        return $materialIDs;
    }
}
