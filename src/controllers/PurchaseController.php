<?php

namespace App\Controllers;

use App\Models\Lot;
use App\Models\MaterialLot;
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
    public function display($errors = []) {
        // POST-Redirect-GET (PRG) PATTERN
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
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

        /**
         * 
         * REWRITTEN ADDITIONALS
         * 
         */
        
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
            'categories' => $categories,
            'errors' => $errors
        ]);
    }
    
    

    public function addPurchaseMaterial($data) {
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $errors = $this->validatePurchaseData($data);
            
            // POST-Redirect-GET Pattern
            if (!empty($errors)) {
                $_SESSION['purchase_errors'] = $errors;
                // Redirect to the same route with GET (to prevent resubmission)
                header('Location: /add-purchase');
                exit;
            }

            try {
                $this->db->beginTransaction();

                $purchaseID = $this->addPurchase($data);
                $materialIDs = $this->addMaterials($data);
                $lotIDs = $this->addLots($data);

                foreach ($data['material_name'] as $index => $materialName) {

                    (new MaterialLot())->save([
                        'lot_id' => $lotIDs[$index],
                        'material_id' => $materialIDs[$index],
                        'stock_level' => $data['quantity'][$index],
                        'expiration_date' => $data['expiry_date'][$index],
                        'qc_status' => $data['qc_status'][$index],
                        'inspection_date' => $data['inspection_date'][$index],
                        'qc_notes' => $data['qc_notes'][$index]
                    ]);

                    (new PurchaseMaterial())->save([
                        'pm_purchase_id' => $purchaseID,
                        'pm_material_id' => $materialIDs[$index],
                        'quantity' => $data['quantity'][$index],
                        'unit_price' => $data['unit_price'][$index],
                        'total_price' => $data['quantity'][$index] * $data['unit_price'][$index],
                        'lot_id' => $lotIDs[$index]
                    ]);
                }

                $this->db->commit();

                header("Location: /purchase-list");
                exit();

            } catch (Exception $e) {
                $this->db->rollBack();
                error_log($e->getMessage());
                $errors[] = "Failed to save purchase material: " . $e->getMessage();
                $this->display($errors);
            }
        }

        $errors = isset($_SESSION['purchase_errors']) ? $_SESSION['purchase_errors'] : [];
    
        // Render the template with errors, if any
        $this->display($errors);
        
        // Clear the errors from session after they are displayed
        unset($_SESSION['purchase_errors']);
    }

    public function addPurchaseMaterialExisting($data) {
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $errors = $this->validatePurchaseData($data);
            
            // POST-Redirect-GET Pattern
            if (!empty($errors)) {
                $_SESSION['purchase_errors'] = $errors;
                // Redirect to the same route with GET (to prevent resubmission)
                header('Location: /add-purchase');
                exit;
            }

            try {
                $this->db->beginTransaction();

                $purchaseID = $this->addPurchase($data);
                $lotIDs = $this->addLots($data);

                foreach ($data['material_name'] as $index => $materialName) {

                    (new MaterialLot())->save([
                        'lot_id' => $lotIDs[$index],
                        'material_id' => $data['material_name'][$index],
                        'stock_level' => $data['quantity'][$index],
                        'expiration_date' => $data['expiry_date'][$index],
                        'qc_status' => $data['qc_status'][$index],
                        'inspection_date' => $data['inspection_date'][$index],
                        'qc_notes' => $data['qc_notes'][$index]
                    ]);

                    (new PurchaseMaterial())->save([
                        'pm_purchase_id' => $purchaseID,
                        'pm_material_id' => $data['material_name'][$index],
                        'quantity' => $data['quantity'][$index],
                        'unit_price' => $data['unit_price'][$index],
                        'total_price' => $data['quantity'][$index] * $data['unit_price'][$index],
                        'lot_id' => $lotIDs[$index]
                    ]);
                }

                $this->db->commit();

                header("Location: /purchase-list");
                exit();

            } catch (Exception $e) {
                $this->db->rollBack();
                error_log($e->getMessage());
                $errors[] = "Failed to save purchase material: " . $e->getMessage();
                $this->display($errors);
            }
        }

        $errors = isset($_SESSION['purchase_errors']) ? $_SESSION['purchase_errors'] : [];
    
        // Render the template with errors, if any
        $this->display($errors);
        
        // Clear the errors from session after they are displayed
        unset($_SESSION['purchase_errors']);
    }


    public function updateMaterialDropList($type='%') {
        $materialObject = new Material();
        $materialData = $materialObject->getAllMaterialsByType($type);
        
        header('Content-Type: application/json');
        echo json_encode($materialData);
        exit;
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
            ]);
            $materialIDs[] = $materialID;
        }

        return $materialIDs;
    }

    private function addLots($data) {
        $lotObject = new Lot();
        $lotIDs = [];

        foreach ($data['lot_number'] as $index => $lotNumber) {
            $lotID = $lotObject->save([
                'number' => $lotNumber,
                'production_date' => $data['production_date'][$index],
            ]);
            $lotIDs[] = $lotID;
        }

        return $lotIDs;
    }

    public function viewPurchase($data) {

        $purchaseMaterialObject = new PurchaseMaterial();

        $purchaseData = $purchaseMaterialObject->getPurchaseData($data[1]);
        
        echo $this->twig->render('view-purchase.html.twig', [
            'purchaseData' => $purchaseData
        ]);
    }

    public function updatePurchase($data) {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateUpdatedPurchaseData($data);
    
            if (!empty($errors)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'errors' => $errors]);
                return;
            }

            try {
                $this->db->beginTransaction();
    
                // Update purchase details
                $purchaseID = $data['purchase_id'];
                $purchaseObject = new Purchase();
                $purchaseObject->update($purchaseID, [
                    'date' => $data['purchase_date'],
                    'material_count' => count($data['material_name']),
                    'total_cost' => array_sum(array_map(function($quantity, $unitPrice) {
                        return $quantity * $unitPrice;
                    }, $data['quantity'], $data['unit_price'])),
                    'status' => $data['status'],
                    'p_supplier_id' => $data['vendor']
                ]);
                
                // Get existing materials linked to the purchase
                $purchaseMaterialObject = new PurchaseMaterial();

                // Update each material and link to purchase & Delete materials (if any)
                $materialObject = new Material();

                $existingMaterialIDs = $purchaseMaterialObject->getMaterialIdsByPurchase($purchaseID);

                
                // Find materials that were removed
                $updatedMaterialIDs = array_filter($data['material_id']); // Get non-null material IDs from the form data
                $removedMaterialIDs = array_diff($existingMaterialIDs, $updatedMaterialIDs);
                
                // Remove materials that were deleted by the user
                foreach ($removedMaterialIDs as $materialID) {
                    $purchaseMaterialObject->delete(['pm_purchase_id' => $purchaseID, 'pm_material_id' => $materialID]);
                    $materialObject->delete($materialID);
                }
                
        
    
                foreach ($data['material_name'] as $index => $materialName) {
                    $materialID = $data['material_id'][$index] ?? null; // Use null if material_id is not provided
    
                    if ($materialID) {
                        // Update existing material
                        $materialObject->update($materialID, [
                            'name' => $materialName,
                            'description' => $data['description'][$index],
                            'material_type' => $data['material_type'][$index],
                            'expiration_date' => $data['expiry_date'][$index],
                            'qc_status' => $data['qc_status'][$index],
                            'inspection_date' => $data['inspection_date'][$index],
                            'qc_notes' => $data['qc_notes'][$index]
                        ]);
                    } else {
                        // Insert new material and retrieve the new material ID
                        $materialID = $materialObject->save([
                            'name' => $materialName,
                            'description' => $data['description'][$index],
                            'material_type' => $data['material_type'][$index],
                            'expiration_date' => $data['expiry_date'][$index],
                            'qc_status' => $data['qc_status'][$index],
                            'inspection_date' => $data['inspection_date'][$index],
                            'qc_notes' => $data['qc_notes'][$index]
                        ]);
                    }
    
                    // Update or insert purchase-material association
                    $purchaseMaterialObject->updateOrInsert([
                        'pm_purchase_id' => $purchaseID,
                        'pm_material_id' => $materialID,
                        'quantity' => $data['quantity'][$index],
                        'unit_price' => $data['unit_price'][$index],
                        'total_price' => $data['quantity'][$index] * $data['unit_price'][$index],
                        'lot_number' => $data['lot_number'][$index]
                    ]);
                }
    
                $this->db->commit();
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                // Refresh the purchase list after a successful update
                // header("Location: /purchase-list");
                return;
    
            } catch (Exception $e) {
                $this->db->rollBack();
                error_log($e->getMessage());
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'errors' => ["Failed to update purchase material."]]);
                return;

                // $errors[] = "Failed to update purchase material: " . $e->getMessage();
                // $this->display($errors);
            }
            //header("Location: /purchase-list");
        }

        $purchaseMaterialObject = new PurchaseMaterial();
        $supplierObject = new Supplier();

        $supplierData = $supplierObject->getAllSuppliers();
        $purchaseData = $purchaseMaterialObject->getPurchaseData($data[1]);

        echo $this->twig->render('update-purchase.html.twig', [
            'purchaseData' => $purchaseData,
            'suppliers' => $supplierData
        ]);
    }

    public function deletePurchase($purchaseID) {
        $purchaseMaterialObject = new PurchaseMaterial();

        $purchaseData = $purchaseMaterialObject->deletePurchaseData($purchaseID, $_POST['deleteMaterials']);
        
        header('Location: /purchase-list');
    }

    public function updatePurchaseStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['purchase_id']) && isset($_POST['status'])) {
                $purchase_id = (int)$_POST['purchase_id'];
                $new_status = trim($_POST['status']);

                $purchaseObject = new Purchase();
    
                // Call the model's updateStatus method
                try {
                    $affectedRows = $purchaseObject->updateStatus($purchase_id, $new_status);
    
                    if ($affectedRows > 0) {
                        // Redirect or return a success message
                        header("Location: /purchase-list");
                        exit();
                    } else {
                        // Handle case where no rows were affected (e.g., invalid purchase_id)
                        throw new Exception("Purchase status update failed.");
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage());
                    echo "Error: " . $e->getMessage();
                }
            } else {
                // Handle missing fields in the POST request
                echo "Error: Missing purchase ID or status.";
            }
        }
    }

    private function validatePurchaseData($data) {
        $errors = [];
    
        // Check required fields
        if (empty($data['purchase_date']) || empty($data['vendor'])) {
            $errors[] = "Purchase date and vendor are required.";
        }
    
        // Validate purchase date
        if (!empty($data['purchase_date'])) {
            $purchase_date = str_replace(' - ', ' ', $data['purchase_date']);
            if (!strtotime($purchase_date)) {
                $errors[] = "Purchase date is invalid.";
            }
        }
    
        // Check material fields
        foreach ($data['material_name'] as $index => $materialName) {
            if (empty($materialName) || empty($data['quantity'][$index]) || empty($data['unit_price'][$index]) || empty($data['lot_number'][$index])) {
                $errors[] = "Material name, quantity, unit price, and lot number are required for each item.";
            }
        }
    
        // Validate numeric and positive values for quantity and unit price
        foreach ($data['quantity'] as $quantity) {
            if (!is_numeric($quantity) || $quantity <= 0) {
                $errors[] = "Each quantity must be a positive number.";
            }
        }
    
        foreach ($data['unit_price'] as $unitPrice) {
            if (!is_numeric($unitPrice) || $unitPrice <= 0) {
                $errors[] = "Each unit price must be a positive number.";
            }
        }
    
        return $errors;
    }

    private function validateUpdatedPurchaseData($data) {
        $errors = [];
    
        // Validate required fields for purchase data
        if (empty($data['purchase_date']) || empty($data['vendor'])) {
            $errors[] = "Purchase date and vendor are required.";
        }
    
        // Validate purchase date
        if (!empty($data['purchase_date'])) {
            $purchase_date = str_replace(' - ', ' ', $data['purchase_date']);
            if (!strtotime($purchase_date)) {
                $errors[] = "Purchase date is invalid.";
            }
        }
    
        // Validate each material's data
        foreach ($data['material_name'] as $index => $materialName) {
            // Check if required fields are provided for each material entry
            if (empty($materialName)) {
                $errors[] = "Material name for item " . ($index + 1) . " is required.";
            }
            if (empty($data['quantity'][$index])) {
                $errors[] = "Quantity for item " . ($index + 1) . " is required.";
            }
            if (empty($data['unit_price'][$index])) {
                $errors[] = "Unit price for item " . ($index + 1) . " is required.";
            }
            if (empty($data['lot_number'][$index])) {
                $errors[] = "Lot number for item " . ($index + 1) . " is required.";
            }
    
            // Validate quantity (must be numeric and positive)
            if (isset($data['quantity'][$index]) && $data['quantity'][$index] !== '') {
                if (!is_numeric($data['quantity'][$index]) || $data['quantity'][$index] <= 0) {
                    $errors[] = "Quantity for item " . ($index + 1) . " must be a positive number.";
                }
            }
    
            // Validate unit price (must be numeric and positive)
            if (isset($data['unit_price'][$index]) && $data['unit_price'][$index] !== '') {
                if (!is_numeric($data['unit_price'][$index]) || $data['unit_price'][$index] <= 0) {
                    $errors[] = "Unit price for item " . ($index + 1) . " must be a positive number.";
                }
            }
    
            // Validate expiry date if provided
            if (!empty($data['expiry_date'][$index])) {
                $expiry_date = str_replace(' - ', ' ', $data['expiry_date'][$index]);
                if (!strtotime($expiry_date)) {
                    $errors[] = "Expiry date for item " . ($index + 1) . " is invalid.";
                }
            }
    
            // Validate inspection date if provided
            if (!empty($data['inspection_date'][$index])) {
                $inspection_date = str_replace(' - ', ' ', $data['inspection_date'][$index]);
                if (!strtotime($inspection_date)) {
                    $errors[] = "Inspection date for item " . ($index + 1) . " is invalid.";
                }
            }
        }
    
        return $errors;
    }
    
}
