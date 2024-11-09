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

    public function getAllPurchaseMaterial($limit = 10, $startDate = null, $endDate = null, $relativeDate = null) {
        $limit = (int)$limit;
    
        // Set default values if empty
        $startDate = $startDate ?: null; // Default to null if not provided (to handle unset case)
        $endDate = $endDate ?: date('Y-m-d'); // Default to today
        $relativeDate = $relativeDate ?: null; // Default to null if not provided

        // If startDate is set, clear relativeDate (no relativeDate should be used)
        if ($startDate) {
            $relativeDate = null;
        }

        // If relativeDate is set, calculate startDate based on that
        if ($relativeDate) {
            $startDate = date('Y-m-d', strtotime("-$relativeDate days"));
        }

        // If still no startDate, set it to the default earliest date
        $startDate = $startDate ?: '1970-01-01';

        // var_dump($startDate, $endDate, $relativeDate); exit;

        // Create the SQL query
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
                WHERE p.date BETWEEN :startDate AND :endDate
                GROUP BY p.id
                ORDER BY p.date DESC
                LIMIT :limit";
    
        try {
            $statement = $this->db->prepare($sql);
            
            // Bind parameters
            $statement->bindValue(':startDate', $startDate);
            $statement->bindValue(':endDate', $endDate);
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
            
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getPurchaseData($purchaseID)
    {
        $sql = "SELECT 
                    p.id AS purchase_id,
                    p.date AS purchase_date,
                    p.material_count,
                    p.total_cost,
                    p.status AS purchase_status,
                    s.id AS supplier_id,
                    s.name AS supplier_name,
                    s.email AS supplier_email,
                    s.address AS supplier_address,
                    s.contact_no AS supplier_contact_no,
                    pm.quantity,
                    pm.unit_price,
                    pm.total_price AS material_total_price,
                    pm.batch_number,
                    m.id AS material_id,
                    m.name AS material_name,
                    m.description AS material_description,
                    m.material_type,
                    m.expiration_date,
                    m.qc_status,
                    m.inspection_date,
                    m.qc_notes
                FROM 
                    purchases p
                JOIN 
                    suppliers s ON p.p_supplier_id = s.id
                JOIN 
                    purchase_material pm ON p.id = pm.pm_purchase_id
                JOIN 
                    materials m ON pm.pm_material_id = m.id
                WHERE 
                    p.id = :purchase_id";
    
        try {
            $statement = $this->db->prepare($sql);
            $statement->execute(['purchase_id' => $purchaseID]);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function deletePurchaseData($purchaseID)
    {
        try {
            // Start a transaction
            $this->db->beginTransaction();

            // Delete related records from purchase_material first
            $sqlDeleteMaterials = "DELETE FROM purchase_material WHERE pm_purchase_id = :purchase_id";
            $statementMaterials = $this->db->prepare($sqlDeleteMaterials);
            $statementMaterials->execute(['purchase_id' => $purchaseID]);

            // Delete the purchase record
            $sqlDeletePurchase = "DELETE FROM purchases WHERE id = :purchase_id";
            $statementPurchase = $this->db->prepare($sqlDeletePurchase);
            $statementPurchase->execute(['purchase_id' => $purchaseID]);

            // Commit the transaction
            $this->db->commit();
            return $statementPurchase->rowCount(); // Returns the number of deleted rows from purchases

        } catch (PDOException $e) {
            // Rollback the transaction on error
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }
}