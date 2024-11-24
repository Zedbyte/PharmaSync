<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\Batch;
use App\Models\Rack;

require_once __DIR__ . '/../../config/config.php';
require_once 'BaseController.php';

class ManufacturedController extends BaseController 
{

    protected $twig;
    protected $db;

    // Constructor to initialize Twig and BaseController
    public function __construct($twig) {
        parent::__construct();  // Initializes session management in BaseController
        $this->twig = $twig;
        // $this->db = returnDBCon(new MedicineBatch()); // Initialize DB connection once
    }

    public function display($errors = [], $batchSearch = null)
    {
        $medicineObject = new Medicine();
        $medicineBatchObject = new MedicineBatch();
    
        // Fetch all medicines
        $medicineData = $medicineObject->getAllMedicines();
    
        // Merge batches with medicines
        foreach ($medicineData as &$medicine) {
            $medicine['batches'] = $medicineBatchObject->getBatchMedicinesAndBatchData($medicine['id'], $batchSearch);
        }

        /**
         * 
         * FOR OBSERVATION
         * 
         */
                    // $filteredMedicineData = array_filter($medicineData, function($medicine) {
                    //     return !empty($medicine['batches']); // Keep only medicines with non-empty batches
                    // });

                    // $medicineData = array_values($filteredMedicineData);
        /**
         * 
         * FOR OBSERVATION
         * 
         */

        // For Rack details rendering via JS
        $rackObject = new Rack();
        $rackData = $rackObject->getAllRacks();

        // For Add to Existing Batch
        $batchObject = new Batch();
        $batchData = $batchObject->getAllBatches();

        //For KPI
        $totalProducts = $medicineBatchObject->getProductCount();
        $nearingOutOfStock = $medicineBatchObject->getNearingOutOfStock();
        $expiringSoon = $medicineBatchObject->getExpiringSoon();
        $totalStock = $medicineBatchObject->getTotalStocks();

        // Creating a KPI array
        $kpiData = [
            'totalProducts' => $totalProducts,
            'nearingOutOfStock' => $nearingOutOfStock,
            'expiringSoon' => $expiringSoon,
            'totalStock' => $totalStock
        ];

        echo $this->twig->render('inventory-manufactured.html.twig', [
            'ASSETS_URL' => ASSETS_URL,
            'medicineData' => $medicineData,
            'rackData' => $rackData,
            'errors' => $errors,
            'batchData_existing' => $batchData,
            'KPI' => $kpiData
        ]);
    }

    public function addBatch() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            
            // Validate the data
            $errors = $this->validateBatchData($data);
            if (!empty($errors)) {
                $_SESSION['batch_errors'] = $errors;
                // Redirect to the same route with GET (to prevent resubmission)
                header('Location: /add-batch');
                exit;
            }
            
            // Save batch information
            $batchObject = new Batch();
            $batchID = $batchObject->save([
                'production_date' => str_replace(' - ', ' ', $data['production_date']),
                'rack_id' => $data['rack'][0]
            ]);
    
            // Save medicine batch information
            $medicineBatchObject = new MedicineBatch();
            foreach ($data['medicine_name'] as $index => $medicineName) {
                $medicineBatchObject->save([
                    'medicine_id' => $medicineName,
                    'batch_id' => $batchID,
                    'stock_level' => $data['stock_level'][$index],
                    'expiry_date' => str_replace(' - ', ' ', $data['expiry_date'][$index])
                ]);
            }

            header("Location: /inventory/manufactured");
            exit;
        }

        $errors = isset($_SESSION['batch_errors']) ? $_SESSION['batch_errors'] : [];
    
        // Render the template with errors, if any
        $this->display($errors);
        
        // Clear the errors from session after they are displayed
        unset($_SESSION['batch_errors']);
    }

    public function addToExistingBatch() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
    
            // Validate the data
            $errors = $this->validateExistingBatchData($data);
            
            // Check if the medicine and batch pair already exists
            $medicineBatchObject = new MedicineBatch();
            foreach ($data['medicine_name'] as $index => $medicineName) {
                $batchExists = $medicineBatchObject->batchExists($medicineName, $data['batch_number'][0]);
                if ($batchExists) {
                    $errors[] = "The medicine with ID {$medicineName} already exists in batch {$data['batch_number'][0]}.";
                }
            }
            
            if (!empty($errors)) {
                $_SESSION['batch_errors'] = $errors;
                // Redirect to the same route with GET (to prevent resubmission)
                header('Location: /add-existing-batch');
                exit;
            }
    
            // Add medicine batch information
            foreach ($data['medicine_name'] as $index => $medicineName) {
                $medicineBatchObject->save([
                    'medicine_id' => $medicineName,
                    'batch_id' => $data['batch_number'][0], // Use the existing batch number
                    'stock_level' => $data['stock_level'][$index],
                    'expiry_date' => str_replace(' - ', ' ', $data['expiry_date'][$index])
                ]);
            }
    
            // Redirect to the inventory page or another relevant page
            header("Location: /inventory/manufactured");
            exit;
        }
    
        $errors = isset($_SESSION['batch_errors']) ? $_SESSION['batch_errors'] : [];
    
        // Render the template with errors, if any
        $this->display($errors);
    
        // Clear the errors from session after they are displayed
        unset($_SESSION['batch_errors']);
    }

    public function updateBatch($data) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate the data
            $errors = $this->validateBatchUpdateData($data);

            if (!empty($errors)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'errors' => $errors]);
                return;
            }
    
            // Update batch data
            $batchObject = new MedicineBatch();
            foreach ($data['expiry_date'] as $index => $expiryDate) {
                $batchObject->update([
                    'medicine_id' => $data['medicineID'], // Ensure correct medicine ID
                    'batch_id' => $data['batchID'], // Ensure correct batch ID
                    'stock_level' => $data['stock_level'][$index],
                    'expiry_date' => $expiryDate
                ]);
            }
    
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            return;
        }

        $batchObject = new MedicineBatch();
        $batchData = $batchObject->getMedicineBatchData($data['medicineID'], $data['batchID']);

        echo $this->twig->render('update-manufactured.html.twig', [
            'batchData' => $batchData
        ]);
    }

    public function deleteBatch($medicineID, $batchID) {
        $medicineBatchObject = new MedicineBatch();
        $medicineBatchObject->delete($medicineID, $batchID);
        
        header('Location: /inventory/manufactured');
        exit;
    }

    public function viewBatch($medicineID, $batchID) {
        
        $medicineBatchObject = new MedicineBatch();
        $medicineBatchData = $medicineBatchObject->getMedicineBatchData($medicineID, $batchID);
        
        echo $this->twig->render('view-manufactured.html.twig', [
            'medicineBatchData' => $medicineBatchData
        ]);
    }

    public function getRackData($id) {
        $rackObject = new Rack();
        $rackData = $rackObject->getRack($id);

        header('Content-Type: application/json');
        echo json_encode(["rackData" => $rackData]);
        exit;
    }

    public function getBatchDetails($id) {
        $batchObject = new Batch();
        $batchDetails = $batchObject->getBatch($id);

        header('Content-Type: application/json');
        echo json_encode(["batchDetails" => $batchDetails]);
        exit;
    }

    public function medicineListExisting($type, $batchID) {

        $medicineObject = new Medicine();
        $medicineData = $medicineObject->getAllMedicinesByType($type);
        
        $medicineBatchObject = new MedicineBatch();
        $existingBatchPairs = $medicineBatchObject->getBatchMedicines($batchID);
        
        // Extract medicine IDs from $existingBatchPairs
        $existingMedicineIDs = array_column($existingBatchPairs, 'medicine_id');
        
        // Filter medicines to exclude those already paired with the given batch
        $filteredMedicines = array_filter($medicineData, function ($medicine) use ($existingMedicineIDs) {
            return !in_array($medicine['id'], $existingMedicineIDs);
        });
        
        // Reset the array keys to ensure compatibility with JS forEach
        $filteredMedicines = array_values($filteredMedicines);

        header('Content-Type: application/json');
        echo json_encode($filteredMedicines);
        exit;
    }

    public function medicineList($type) {

        $medicineObject = new Medicine();
        $medicineData = $medicineObject->getAllMedicinesByType($type);

        header('Content-Type: application/json');
        echo json_encode($medicineData);
        exit;
    }

    private function validateBatchData($data) {
        $errors = [];
    
        // Validate production date
        if (empty($data['production_date'])) {
            $errors[] = "Production date is required.";
        } elseif (!empty($data['production_date']) && !strtotime(str_replace(' - ', ' ', $data['production_date']))) {
            $errors[] = "Production date is invalid.";
        }
    
        // Validate rack (if applicable)
        if (empty($data['rack'][0] ?? null)) {
            $errors[] = "Rack ID is required.";
        }
    
        // Validate medicine-related data if the keys exist
        $medicineNames = $data['medicine_name'] ?? [];
        $expiryDates = $data['expiry_date'] ?? [];
        $stockLevels = $data['stock_level'] ?? [];
    
        // Ensure all arrays are the same size
        $itemCount = max(count($medicineNames), count($expiryDates), count($stockLevels));
    
        for ($index = 0; $index < $itemCount; $index++) {
            // Medicine name
            if (empty($medicineNames[$index] ?? null)) {
                $errors[] = "Medicine name is required for item " . ($index + 1) . ".";
            }
    
            // Expiry date
            if (empty($expiryDates[$index] ?? null)) {
                $errors[] = "Expiry date is required for item " . ($index + 1) . ".";
            } elseif (!empty($expiryDates[$index] ?? null) && !strtotime(str_replace(' - ', ' ', $expiryDates[$index]))) {
                $errors[] = "Expiry date is invalid for item " . ($index + 1) . ".";
            }
    
            // Stock level
            if (empty($stockLevels[$index] ?? null)) {
                $errors[] = "Stock level is required for item " . ($index + 1) . ".";
            } elseif (!is_numeric($stockLevels[$index] ?? null) || ($stockLevels[$index] ?? 0) <= 0) {
                $errors[] = "Stock level must be a positive number for item " . ($index + 1) . ".";
            }
        }
    
        return $errors;
    }
    

    private function validateExistingBatchData($data) {
        $errors = [];
        
        // Validate batch number
        if (empty($data['batch_number'][0] ?? null)) {
            $errors[] = "Batch number is required.";
        } elseif (!is_numeric($data['batch_number'][0])) {
            $errors[] = "Batch number must be a valid number.";
        }
        
        // Validate medicine-related data
        $medicineNames = $data['medicine_name'] ?? [];
        $expiryDates = $data['expiry_date'] ?? [];
        $stockLevels = $data['stock_level'] ?? [];
    
        // Ensure all arrays are the same size
        $itemCount = max(count($medicineNames), count($expiryDates), count($stockLevels));
    
        for ($index = 0; $index < $itemCount; $index++) {
            // Medicine name
            if (empty($medicineNames[$index] ?? null)) {
                $errors[] = "Medicine name is required for item " . ($index + 1) . ".";
            }
    
            // Expiry date
            if (empty($expiryDates[$index] ?? null)) {
                $errors[] = "Expiry date is required for item " . ($index + 1) . ".";
            } elseif (!empty($expiryDates[$index] ?? null) && !strtotime(str_replace(' - ', ' ', $expiryDates[$index]))) {
                $errors[] = "Expiry date is invalid for item " . ($index + 1) . ".";
            }
    
            // Stock level
            if (empty($stockLevels[$index] ?? null)) {
                $errors[] = "Stock level is required for item " . ($index + 1) . ".";
            } elseif (!is_numeric($stockLevels[$index] ?? null) || ($stockLevels[$index] ?? 0) <= 0) {
                $errors[] = "Stock level must be a positive number for item " . ($index + 1) . ".";
            }
        }
    
        return $errors;
    }


    private function validateBatchUpdateData($data) {
        $errors = [];
    
        // Validate medicine_id
        if (empty($data['medicine_id'])) {
            $errors[] = "Medicine ID is required.";
        } elseif (!is_numeric($data['medicine_id']) || (int)$data['medicine_id'] <= 0) {
            $errors[] = "Medicine ID must be a positive number.";
        }
    
        // Validate batch_id
        if (empty($data['batch_id'])) {
            $errors[] = "Batch ID is required.";
        } elseif (!is_numeric($data['batch_id']) || (int)$data['batch_id'] <= 0) {
            $errors[] = "Batch ID must be a positive number.";
        }
    
        // Validate expiry_date (single value from array)
        $expiryDate = $data['expiry_date'][0] ?? null;
        if (empty($expiryDate)) {
            $errors[] = "Expiry date is required.";
        } elseif (!strtotime($expiryDate)) {
            $errors[] = "Expiry date is invalid.";
        }
    
        // Validate stock_level (single value from array)
        $stockLevel = $data['stock_level'][0] ?? null;
        if (empty($stockLevel)) {
            $errors[] = "Stock level is required.";
        } elseif (!is_numeric($stockLevel) || (int)$stockLevel <= 0) {
            $errors[] = "Stock level must be a positive number.";
        }
    
        return $errors;
    }
    
    
    
    
    

    // private function getMedicineBatches($medicineId, $medicineBatchData)
    // {
    //     $filteredBatches = [];

    //     foreach ($medicineBatchData as $batch) {
    //         foreach ($batch as $medicine) {
    //             if ($medicine['medicine_id'] == $medicineId) {
    //                 $filteredBatches[] = $medicine['batch_id'];
    //             }
    //         }
    //     }

    //     return $filteredBatches;
    // }

}