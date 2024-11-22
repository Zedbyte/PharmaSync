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

    public function display($errors = [])
    {
        $medicineObject = new Medicine();
        $medicineBatchObject = new MedicineBatch();
    
        // Fetch all medicines
        $medicineData = $medicineObject->getAllMedicines();
    
        // Merge batches with medicines
        foreach ($medicineData as &$medicine) {
            $medicine['batches'] = $medicineBatchObject->getBatchMedicinesAndBatchData($medicine['id']);
        }

        // For Rack details rendering via JS
        $rackObject = new Rack();
        $rackData = $rackObject->getAllRacks();

        echo $this->twig->render('inventory-manufactured.html.twig', [
            'ASSETS_URL' => ASSETS_URL,
            'medicineData' => $medicineData,
            'rackData' => $rackData,
            'errors' => $errors
        ]);
    }

    public function addBatch() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST; // Assuming data is coming from the POST request
            
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

    public function deleteBatch($medicineID, $batchID) {
        $medicineBatchObject = new MedicineBatch();
        $medicineBatchObject->delete($medicineID, $batchID);
        
        header('Location: /inventory/manufactured');
        exit;
    }

    public function getRackData($id) {
        $rackObject = new Rack();
        $rackData = $rackObject->getRack($id);

        header('Content-Type: application/json');
        echo json_encode(["rackData" => $rackData]);
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