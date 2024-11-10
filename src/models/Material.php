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
                    `material_type`=:material_type,
                    `expiration_date`=:expiration_date,
                    `qc_status`=:qc_status,
                    `inspection_date`=:inspection_date,
                    `qc_notes`=:qc_notes";

        try {
            $statement = $this->db->prepare($sql);

            $statement->execute([
                'name' => $data['name'],
                'description' => $data['description'],
                'material_type' => $data['material_type'],
                'expiration_date' => $data['expiration_date'],
                'qc_status' => $data['qc_status'],
                'inspection_date' => $data['inspection_date'],
                'qc_notes' => $data['qc_notes']
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
                    `material_type` = :material_type,
                    `expiration_date` = :expiration_date,
                    `qc_status` = :qc_status,
                    `inspection_date` = :inspection_date,
                    `qc_notes` = :qc_notes
                WHERE `id` = :id";

        try {
            $statement = $this->db->prepare($sql);
            
            $statement->execute([
                'name' => $data['name'],
                'description' => $data['description'],
                'material_type' => $data['material_type'],
                'expiration_date' => $data['expiration_date'],
                'qc_status' => $data['qc_status'],
                'inspection_date' => $data['inspection_date'],
                'qc_notes' => $data['qc_notes'],
                'id' => $materialID
            ]);

        } catch (PDOException $e) {
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

}