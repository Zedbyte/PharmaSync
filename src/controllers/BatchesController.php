<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\Batch;
use App\Models\Rack;

require_once __DIR__ . '/../../config/config.php';
require_once 'BaseController.php';

class BatchesController extends BaseController 
{

    protected $twig;
    protected $db;

    // Constructor to initialize Twig and BaseController
    public function __construct($twig) {
        parent::__construct();  // Initializes session management in BaseController
        $this->twig = $twig;
        // $this->db = returnDBCon(new MedicineBatch()); // Initialize DB connection once
    }

    public function display($errors = [], $lotSearch = null)
    {

        // $materialObject = new Material();
        // $materialData = $materialObject->getAllMaterials();

        // $materialLotObject = new MaterialLot();
        
        // foreach ($materialData as &$material) {
        //     $material['lots'] = $materialLotObject->getMaterialLotsAndLotData($material['id'], $lotSearch);
        // }

        // if ($lotSearch) {
        //     $filteredMaterialData = array_filter($materialData, function($material) {
        //         return !empty($material['lots']); // Keep only materials with non-empty lots
        //     });
    
        //     $materialData = array_values($filteredMaterialData);
        // }

        $batchObject = new Batch();
        $batchData = $batchObject->getAllBatches();

        $rackObject = new Rack();
        $rackData = $rackObject->getAllRacks();

        echo $this->twig->render('batch-list.html.twig', [
            'ASSETS_URL' => ASSETS_URL,
            'batchData' => $batchData,
            'rackData' => $rackData,
            'errors' => $errors
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
                header('Location: /add-batch/single');
                exit;
            }
            
            // Save batch information
            $batchObject = new Batch();
            $batchID = $batchObject->save([
                'production_date' => str_replace(' - ', ' ', $data['production_date']),
                'rack_id' => $data['rack']
            ]);
    
            header("Location: /batch-list");
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
            $errors = $this->validateBatchData($data);

            if (!empty($errors)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'errors' => $errors]);
                return;
            }
    
            (new Batch())->update($data['batch_id'],
                [
                'production_date' => $data['production_date'],  
                'rack' => $data['rack']
                ]
            );
            
    
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            return;
        }
        
        $batchObject = new Batch();
        $batchData = $batchObject->getBatch($data['batchID']);

        $rackObject = new Rack();
        $rackData = $rackObject->getAllRacks();

        echo $this->twig->render('update-batch.html.twig', [
            'batchData' => $batchData,
            'rackData' => $rackData
        ]);
    }


    public function addRack() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            // Validate the data
            $errors = $this->validateRackData($data);

            if (!empty($errors)) {
                $_SESSION['rack_errors'] = $errors;
                // Redirect to the same route with GET (to prevent resubmission)
                header('Location: /add-rack/single');
                exit;
            }
            
            // Save batch information
            $rackObject = new Rack();
            $rackID = $rackObject->save([
                'location' => $data['location'],
                'temperature_controlled' => $data['temperature_controlled']
            ]);
    
            header("Location: /batch-list");
            exit;
        }

        $errors = isset($_SESSION['rack_errors']) ? $_SESSION['rack_errors'] : [];
    
        // Render the template with errors, if any
        $this->display($errors);
        
        // Clear the errors from session after they are displayed
        unset($_SESSION['rack_errors']);
    }

    public function updateRack($data) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Validate the data
            // $errors = $this->validateBatchData($data);

            // if (!empty($errors)) {
            //     header('Content-Type: application/json');
            //     echo json_encode(['success' => false, 'errors' => $errors]);
            //     return;
            // }
    
            (new Rack())->update($data['rack_id'],
                [
                'location' => $data['location'],  
                'temperature_controlled' => $data['temperature_controlled']
                ]
            );
            
    
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            return;
        }
        
        $rackObject = new Rack();
        $rackData = $rackObject->getRack($data['rackID']);

        echo $this->twig->render('update-rack.html.twig', [
            'rackData' => $rackData
        ]);
    }

    public function getProductionRate() {
        $batchObject = new Batch();
        $batchData = $batchObject->getBatchProductionRate();
        echo json_encode($batchData);
    }

    public function getMedicineStockDistribution() {
        $medicineBatchObject = new MedicineBatch();
        $medicineBatchData = $medicineBatchObject->getMedicineStockDistribution();
        echo json_encode($medicineBatchData);
    }


    private function validateBatchData($data) {
        $errors = [];
    
        // Validate production date
        if (empty($data['production_date'])) {
            $errors[] = "Production date is required.";
        } elseif (!strtotime(str_replace(' - ', ' ', $data['production_date']))) {
            $errors[] = "Production date is invalid.";
        }
    
        // Validate rack (if applicable)
        if (empty($data['rack'])) {
            $errors[] = "Rack ID is required.";
        }
    
        return $errors;
    }

    private function validateRackData($data) {
        $errors = [];
    
        // Validate production date
        if (empty($data['location'])) {
            $errors[] = "Location is required.";
        }
    
        // Validate rack (if applicable)
        if (empty($data['temperature_controlled'])) {
            $errors[] = "Specify if the rack is temperature controlled.";
        }
    
        return $errors;
    }
    

}