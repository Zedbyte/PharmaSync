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

}