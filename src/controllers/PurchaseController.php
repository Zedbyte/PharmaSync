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
        $supplierObject = new Supplier();
        $data = $supplierObject->getAllSuppliers();
        echo $this->twig->render('purchase-list.html.twig', ['ASSETS_URL' => ASSETS_URL, 'suppliers' => $data]);
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
