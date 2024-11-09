<?php

namespace App\Controllers;

use App\Models\PurchaseMaterial;
use App\Models\Purchase;
use App\Models\Material;
use App\Models\Supplier;

use \Exception;

require_once __DIR__ . '/../../config/config.php';
require_once LIB_URL . '/get_dbcon.inc.php';
require_once LIB_URL . '/debug_to_console.inc.php';
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
            $entryValue = isset($_POST['entryValue']) && is_numeric($_POST['entryValue']) && $_POST['entryValue'] > 0 ? (int) $_POST['entryValue'] : 10;
            $purchase_search = $_POST['purchase_search'] ?? null;

            // Advanced Filters
            $minPrice = isset($_POST['min_price']) ? (float) $_POST['min_price'] : 1;
            $maxPrice = isset($_POST['max_price']) ? (float) $_POST['max_price'] : 9999999;

            $categories = isset($_POST['category']) ? $_POST['category'] : ['pending', 'completed', 'backordered', 'failed', 'canceled'];

            // Redirect with the filters as query parameters
            $queryParams = [];
                    
            // Prioritize startDate over relativeDate
            if (($startDate && $endDate)) {
                // Case 1: If start_date and end_date are provided, use them and ignore relativeDate
                $queryParams['start_date'] = $startDate;
                $queryParams['end_date'] = $endDate;
                $queryParams['relativeDate'] = null; // Explicitly clear relativeDate
            } elseif ($relativeDate) {
                // Case 2: If relativeDate is provided, ignore start_date and end_date
                $queryParams['relativeDate'] = $relativeDate;
                $queryParams['start_date'] = null; // Explicitly clear start_date
                $queryParams['end_date'] = null;   // Explicitly clear end_date
            }

            if ($entryValue) $queryParams['entryValue'] = $entryValue;
            if ($purchase_search) $queryParams['purchase_search'] = $purchase_search;

            if ($minPrice) $queryParams['min_price'] = $minPrice;
            if ($maxPrice) $queryParams['max_price'] = $maxPrice;

            if (!empty($categories)) $queryParams['category'] = $categories;

            $relativeDate = $startDate = $endDate = null;
    
            header('Location: /purchase-list?' . http_build_query($queryParams));
            exit;
        }
    
        // Retrieve filter values from query parameters (GET)
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date']  ?? null;

        $relativeDate = ($startDate && $endDate) ? null : ($_GET['relativeDate'] ?? 30);
        $entryValue = isset($_GET['entryValue']) && is_numeric($_GET['entryValue']) ? (int) $_GET['entryValue'] : 10;
        $purchase_search = $_GET['purchase_search'] ?? null;

        $minPrice = isset($_GET['min_price']) ? (float) $_GET['min_price'] : 1;
        $maxPrice = isset($_GET['max_price']) ? (float) $_GET['max_price'] : 9999999;
        $categories = $_GET['category'] ?? ['pending', 'completed', 'backordered', 'failed', 'canceled'];

        // Load data models based on filters
        $supplierObject = new Supplier();
        $supplierData = $supplierObject->getAllSuppliers();
    
        $purchaseMaterialObject = new PurchaseMaterial();
        $purchaseMaterialData = $purchaseMaterialObject->getAllPurchaseMaterial(
            $entryValue, 
            $startDate, 
            $endDate, 
            $relativeDate, 
            $purchase_search,
            $minPrice, 
            $maxPrice, 
            $categories);

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
            'entryValue' => $entryValue,
            'purchase_search' => $purchase_search,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'categories' => $categories
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

    public function viewPurchase($data) {

        $purchaseMaterialObject = new PurchaseMaterial();

        $purchaseData = $purchaseMaterialObject->getPurchaseData($data[1]);
        
        echo $this->twig->render('view-purchase.html.twig', [
            'purchaseData' => $purchaseData
        ]);
    }

    public function deletePurchase($purchaseID) {
        $purchaseMaterialObject = new PurchaseMaterial();

        $purchaseData = $purchaseMaterialObject->deletePurchaseData($purchaseID);
        
        header('Location: /purchase-list');
    }
}
