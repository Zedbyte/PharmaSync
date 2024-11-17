<?php

namespace App\Models;

require_once __DIR__ . '/../../config/config.php';

use App\Models\BaseModel;
use \PDO;
use \PDOException;
use \Exception;

class Medicine extends BaseModel
{
    public function save($data)
    {
        $sql = "INSERT INTO `medicines` 
                SET
                    `name` = :name,
                    `type` = :type,
                    `composition` = :composition,
                    `therapeutic_class` = :therapeutic_class,
                    `regulatory_class` = :regulatory_class,
                    `manufacturing_details` = :manufacturing_details,
                    `formulation_id` = :formulation_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'name' => $data['name'],
                'type' => $data['type'],
                'composition' => $data['composition'],
                'therapeutic_class' => $data['therapeutic_class'],
                'regulatory_class' => $data['regulatory_class'],
                'manufacturing_details' => $data['manufacturing_details'],
                'formulation_id' => $data['formulation_id']
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function update($medicineId, $data)
    {
        $sql = "UPDATE `medicines` 
                SET
                    `name` = :name,
                    `type` = :type,
                    `composition` = :composition,
                    `therapeutic_class` = :therapeutic_class,
                    `regulatory_class` = :regulatory_class,
                    `manufacturing_details` = :manufacturing_details,
                    `formulation_id` = :formulation_id
                WHERE `id` = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'name' => $data['name'],
                'type' => $data['type'],
                'composition' => $data['composition'],
                'therapeutic_class' => $data['therapeutic_class'],
                'regulatory_class' => $data['regulatory_class'],
                'manufacturing_details' => $data['manufacturing_details'],
                'formulation_id' => $data['formulation_id'],
                'id' => $medicineId
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function delete($medicineId)
    {
        $sql = "DELETE FROM `medicines` WHERE `id` = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute(['id' => $medicineId]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getMedicine($id)
    {
        $sql = "SELECT * FROM `medicines` WHERE `id` = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute(['id' => $id]);
            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getAllMedicines()
    {
        $sql = "SELECT * FROM `medicines`";

        try {
            $statement = $this->db->query($sql);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getAllMedicinesByType($type)
    {   
        if (empty($type)) $type='%';

        $sql = "SELECT * FROM `medicines` WHERE `type` LIKE :type ORDER BY name";

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
}