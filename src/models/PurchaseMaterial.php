<?php

namespace App\Models;

require_once __DIR__ . '/../../config/config.php';

use App\Models\BaseModel;
use \PDO;
use \PDOException;
use \Exception;

class PurchaseMaterial extends BaseModel
{
    public function save($data)
    {
        $sql = "INSERT INTO purchase_material 
                SET
                    `pm_purchase_id`=:pm_purchase_id,
                    `pm_material_id`=:pm_material_id,
                    `quantity`=:quantity,
                    `unit_price`=:unit_price,
                    `total_price`=:total_price,
                    `batch_number`=:batch_number";

        try {
            $statement = $this->db->prepare($sql);

            $statement->execute([
                'pm_purchase_id' => $data['pm_purchase_id'],
                'pm_material_id' => $data['pm_material_id'],
                'quantity' => $data['quantity'],
                'unit_price' => $data['unit_price'],
                'total_price' => $data['total_price'],
                'batch_number' => $data['batch_number']
            ]);

            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getPurchaseMaterial($purchase_id, $material_id)
    {
        $sql = "SELECT * FROM purchase_material 
                WHERE pm_purchase_id = :pm_purchase_id 
                AND pm_material_id = :pm_material_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'pm_purchase_id' => $purchase_id,
                'pm_material_id' => $material_id
            ]);
            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getAllPurchaseMaterial() {
        $sql = "SELECT 
                p.id AS purchase_id,
                p.date AS date_of_purchase,
                s.name AS vendor_name,
                GROUP_CONCAT(DISTINCT m.material_type ORDER BY m.material_type ASC SEPARATOR ', ') AS material_types,
                p.material_count,
                p.total_cost,
                p.status
                FROM purchases p
                JOIN suppliers s ON p.p_supplier_id = s.id
                JOIN purchase_material pm ON p.id = pm.pm_purchase_id
                JOIN materials m ON pm.pm_material_id = m.id
                GROUP BY p.id
                ORDER BY p.date DESC";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }
}