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
                'name' => $data['name'],
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
        $sql = "DELETE FROM materials WHERE `id` = :id";

        try {
            $statement = $this->db->prepare($sql);
            
            // Execute the query with the materialID as parameter
            $statement->execute(['id' => $materialID]);

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

}