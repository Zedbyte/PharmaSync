<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Material;
use App\Models\MaterialLot;
use App\Models\Lot;


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

    public function updateMaterial($data) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate the data
            $errors = $this->validateMaterialUpdateData($data);

            if (!empty($errors)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'errors' => $errors]);
                return;
            }
    
            // Update material data
            $materialObject = new Material();
            $materialObject->update($data['material_id'], $data);
    
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            return;
        }

        $materialObject = new Material();
        $materialData = $materialObject->getMaterial($data['materialID']);

        echo $this->twig->render('update-raw.html.twig', [
            'materialData' => $materialData
        ]);
    }

    public function deleteMaterial($id) {
        $materialObject = new Material();
        $materialObject->delete($id);
        header("Location: /inventory/raw");
        
    }

    public function viewMaterial($id) {
        $materialObject = new Material();
        $materialData = $materialObject->getMaterialLotSupplierData($id);


        // var_dump($materialData);

        echo $this->twig->render('view-raw.html.twig', [
            'MaterialLotData' => $materialData
        ]);
    }

    public function updateLot($data) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate the data
            $errors = $this->validateLotUpdateData($data);
            if (!empty($errors)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'errors' => $errors]);
                return;
            }
    
            // Update material data
            $materialLotObject = new MaterialLot();
            $data['qc_status'] = $data['qc_status'][0];
            $materialLotObject->update($data['lot_id'], $data['material_id'], $data);

            $lotObject = new Lot();
            $lotObject->update($data['lot_id'], $data);
    
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            return;
        }

        $mateiralLotObjectGET = new MaterialLot();
        $lotData = $mateiralLotObjectGET->getMaterialLot($data['lotID'], $data['materialID']);

        // var_dump($lotData);

        echo $this->twig->render('update-lot.html.twig', [
            'lotData' => $lotData
        ]);
    }

    public function deleteLot($data) {
        $materialLotObject = new MaterialLot();
        $materialLotObject->delete($data['lotID'], $data['materialID']);
        header("Location: /inventory/raw");
    }

    public function viewLot($data) {
        $materialObject = new MaterialLot();
        $LotMaterialData = $materialObject->getLotMaterialData($data['lotID'], $data['materialID']);
        // var_dump($LotMaterialData);
        echo $this->twig->render('view-lot.html.twig', [
            'LotMaterialData' => $LotMaterialData
        ]);
    }


    private function validateMaterialUpdateData($data) {
        $errors = [];
    
        // Validate material ID
        if (empty($data['material_id'])) {
            $errors[] = "Material ID is required.";
        } elseif (!is_numeric($data['material_id']) || $data['material_id'] <= 0) {
            $errors[] = "Material ID must be a positive number.";
        }
    
        // Validate material type
        if (empty($data['material_type'])) {
            $errors[] = "Material type is required.";
        } elseif (!in_array($data['material_type'], ['raw', 'manufactured'], true)) {
            $errors[] = "Material type must be either 'raw' or 'manufactured'.";
        }
    
        // Validate material name
        if (empty($data['material_name'])) {
            $errors[] = "Material name is required.";
        } elseif (strlen($data['material_name']) > 255) {
            $errors[] = "Material name must not exceed 255 characters.";
        }
    
        // Validate description
        if (empty($data['description'])) {
            $errors[] = "Description is required.";
        } elseif (strlen($data['description']) > 500) {
            $errors[] = "Description must not exceed 500 characters.";
        }
    
        return $errors;
    }
    

    private function validateLotUpdateData($data) {
        $errors = [];
    
        // Validate material ID
        if (empty($data['material_id'])) {
            $errors[] = "Material ID is required.";
        } elseif (!is_numeric($data['material_id']) || $data['material_id'] <= 0) {
            $errors[] = "Material ID must be a positive number.";
        }
    
        // Validate lot ID
        if (empty($data['lot_id'])) {
            $errors[] = "Lot ID is required.";
        } elseif (!is_numeric($data['lot_id']) || $data['lot_id'] <= 0) {
            $errors[] = "Lot ID must be a positive number.";
        }
    
        // Validate lot number
        if (empty($data['lot_number'])) {
            $errors[] = "Lot number is required.";
        } elseif (strlen($data['lot_number']) > 50) {
            $errors[] = "Lot number must not exceed 50 characters.";
        }
    
        // Validate production date
        if (empty($data['production_date'])) {
            $errors[] = "Production date is required.";
        } elseif (!strtotime($data['production_date'])) {
            $errors[] = "Production date is invalid.";
        }
    
        // Validate stock level
        if (!isset($data['stock_level'])) {
            $errors[] = "Stock level is required.";
        } elseif (!is_numeric($data['stock_level']) || $data['stock_level'] < 0) {
            $errors[] = "Stock level must be a non-negative number.";
        }
    
        // Validate QC status
        if (empty($data['qc_status'])) {
            $errors[] = "QC status is required.";
        } else {
            foreach ($data['qc_status'] as $index => $status) {
                if (!in_array($status, ['approved', 'rejected', 'pending'], true)) {
                    $errors[] = "QC status at index " . ($index + 1) . " must be either 'accepted', 'rejected', or 'pending'.";
                }
            }
        }
    
        // Validate inspection date
        if (empty($data['inspection_date'])) {
            $errors[] = "Inspection date is required.";
        } elseif (!strtotime($data['inspection_date'])) {
            $errors[] = "Inspection date is invalid.";
        }
    
        return $errors;
    }
    

}