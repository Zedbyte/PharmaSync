<?php

namespace App\Models;

require_once __DIR__ . '/../../config/config.php';

use App\Models\BaseModel;
use \PDO;
use \PDOException;
use \Exception;

class Formulation extends BaseModel
{

    public function save($data)
    {
        $sql = "INSERT INTO `product_formulation` 
                SET
                    `medicine_id` = :medicine_id,
                    `material_id` = :material_id,
                    `quantity_required` = :quantity_required,
                    `unit` = :unit,
                    `description` = :description";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'medicine_id' => $data['medicine_id'],
                'material_id' => $data['material_id'],
                'quantity_required' => $data['quantity_required'],
                'unit' => $data['unit'],
                'description' => $data['description'],
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function update($formulationId, $data)
    {
        $sql = "UPDATE `product_formulation` 
                SET
                    `medicine_id` = :medicine_id,
                    `material_id` = :material_id,
                    `quantity_required` = :quantity_required,
                    `unit` = :unit,
                    `description` = :description
                WHERE `id` = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'medicine_id' => $data['medicine_id'],
                'material_id' => $data['material_id'],
                'quantity_required' => $data['quantity_required'],
                'unit' => $data['unit'],
                'description' => $data['description'],
                'id' => $formulationId,
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function delete($formulationId)
    {
        $sql = "DELETE FROM `product_formulation` WHERE `id` = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute(['id' => $formulationId]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getFormulationByMedicine($medicineId)
    {
        $sql = "SELECT
            pf.id AS formulation_id,
            pf.quantity_required,
            pf.unit,
            pf.description,
            mt.id AS material_id,
            mt.name AS material_name,
            mt.material_type,
            md.id AS medicine_id,
            md.name AS medicine_name,
            md.type AS medicine_type,
            md.composition
        FROM 
            product_formulation pf
        JOIN
            materials mt ON mt.id = pf.material_id
        JOIN 
            medicines md ON md.id = pf.medicine_id
        WHERE 
            pf.medicine_id = :medicine_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'medicine_id' => $medicineId
            ]);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getFormulationByID($formulationID)
    {
        $sql = "SELECT
            pf.id AS formulation_id,
            pf.quantity_required,
            pf.unit,
            pf.description,
            mt.id AS material_id,
            mt.name AS material_name,
            mt.material_type,
            md.id AS medicine_id,
            md.name AS medicine_name,
            md.type AS medicine_type,
            md.composition
        FROM 
            product_formulation pf
        JOIN
            materials mt ON mt.id = pf.material_id
        JOIN 
            medicines md ON md.id = pf.medicine_id
        WHERE 
            pf.id = :formulationID";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'formulationID' => $formulationID
            ]);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }
}