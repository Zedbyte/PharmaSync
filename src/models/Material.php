<?php

namespace App\Models;

require_once __DIR__ . '/../../config/config.php';

use App\Models\BaseModel;
use \PDO;
use \PDOException;
use \Exception;

class Material extends BaseModel
{
    public function save($data)
    {
        $sql = "INSERT INTO materials 
                SET
                    `name`=:name,
                    `description`=:description,
                    `material_type`=:material_type";

        try {
            $statement = $this->db->prepare($sql);

            $statement->execute([
                'name' => $data['name'],
                'description' => $data['description'],
                'material_type' => $data['material_type']
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function update($materialID, $data)
    {   
        $sql = "UPDATE materials 
                SET
                    `name` = :name,
                    `description` = :description,
                    `material_type` = :material_type
                WHERE `id` = :id";

        try {
            $statement = $this->db->prepare($sql);
            
            $statement->execute([
                'name' => $data['material_name'],
                'description' => $data['description'],
                'material_type' => $data['material_type'],
                'id' => $materialID
            ]);

        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function delete($materialID)
    {
        try {
            $this->db->beginTransaction();
    
            // Delete from `purchase_material`
            $sqlPurchaseMaterial = "DELETE FROM purchase_material WHERE pm_material_id = :material_id";
            $stmtPurchaseMaterial = $this->db->prepare($sqlPurchaseMaterial);
            $stmtPurchaseMaterial->execute(['material_id' => $materialID]);
    
            // Delete from `material_lot`
            $sqlMaterialLot = "DELETE FROM material_lot WHERE material_id = :material_id";
            $stmtMaterialLot = $this->db->prepare($sqlMaterialLot);
            $stmtMaterialLot->execute(['material_id' => $materialID]);
    
            // Delete from `materials`
            $sqlMaterials = "DELETE FROM materials WHERE id = :material_id";
            $stmtMaterials = $this->db->prepare($sqlMaterials);
            $stmtMaterials->execute(['material_id' => $materialID]);
    
            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }
    


    public function getMaterial($id)
    {
        $sql = "SELECT * FROM materials WHERE id = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute(['id' => $id]);
            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getAllMaterials()
    {
        $sql = "SELECT * FROM materials";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getAllMaterialsByType($type)
    {
        if (empty($type)) $type='%';

        $sql = "SELECT * FROM `materials` WHERE `material_type` LIKE :type ORDER BY name";

        try {
            $statement = $this->db->prepare($sql);
            $statement->bindValue(':type', $type, PDO::PARAM_STR);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getMaterialLotSupplierData($materialID) {
        // Base SQL query
        $sql = "SELECT ml.*, l.*, pm.*, m.*, s.name AS supplier_name, s.email, s.address, s.contact_no
            FROM 
                material_lot ml
            JOIN 
                lots l ON ml.lot_id = l.id
            JOIN 
                materials m ON m.id = ml.material_id
            JOIN 
                purchase_material pm ON pm.lot_id = l.id
            JOIN
                purchases p ON p.id = pm.pm_purchase_id
            JOIN
                suppliers s ON s.id = p.p_supplier_id
            WHERE 
                ml.material_id = :material_id";

        // Bind parameters
        $params = ['material_id' => $materialID];


        try {
            // Prepare the query
            $statement = $this->db->prepare($sql);

            // Execute with parameters
            $statement->execute($params);

            // Fetch and return results
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int) $e->getCode());
        }
    }

}