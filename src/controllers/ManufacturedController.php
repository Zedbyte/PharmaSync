<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\Batch;

require_once __DIR__ . '/../../config/config.php';
require_once 'BaseController.php';

class ManufacturedController extends BaseController 
{

    protected $twig;

    // Constructor to initialize Twig and BaseController
    public function __construct($twig) {
        parent::__construct();  // Initializes session management in BaseController
        $this->twig = $twig;
        //$this->db = returnDBCon(new Manufactured()); // Initialize DB connection once
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

        echo $this->twig->render('inventory-manufactured.html.twig', [
            'ASSETS_URL' => ASSETS_URL,
            'medicineData' => $medicineData
        ]);
    }
    

    private function getMedicineBatches($medicineId, $medicineBatchData)
    {
        $filteredBatches = [];

        foreach ($medicineBatchData as $batch) {
            foreach ($batch as $medicine) {
                if ($medicine['medicine_id'] == $medicineId) {
                    $filteredBatches[] = $medicine['batch_id'];
                }
            }
        }

        return $filteredBatches;
    }

}