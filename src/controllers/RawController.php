<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Material;
use App\Models\MaterialLot;


require_once __DIR__ . '/../../config/config.php';
require_once 'BaseController.php';

class RawController extends BaseController 
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

        $materialObject = new Material();
        $materialData = $materialObject->getAllMaterials();

        $materialLotObject = new MaterialLot();
        
        foreach ($materialData as &$material) {
            $material['lots'] = $materialLotObject->getMaterialLotsAndLotData($material['id']);
        }

        // $filteredMaterialData = array_filter($medicineData, function($medicine) {
        //     return !empty($medicine['batches']); // Keep only medicines with non-empty batches
        // });

        // $medicineData = array_values($filteredMedicineData);

        // var_dump($materialData);



        echo $this->twig->render('inventory-raw.html.twig', [
            'ASSETS_URL' => ASSETS_URL,
            'materialData' => $materialData
        ]);
    }

}