<?php

namespace App\Models;

require_once __DIR__ . '/../../config/config.php';

use App\Models\BaseModel;
use \PDO;
use \PDOException;
use \Exception;

class Rack extends BaseModel
{

    public function save($data)
    {
        $sql = "INSERT INTO `racks` 
                SET
                    `location` = :location,
                    `temperature_controlled` = :temperature_controlled";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'location' => $data['location'],
                'temperature_controlled' => $data['temperature_controlled']
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function update($rackID, $data)
    {
        $sql = "UPDATE `racks` 
                SET
                    `location` = :location,
                    `temperature_controlled` = :temperature_controlled
                WHERE `id` = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'location' => $data['location'],
                'temperature_controlled' => $data['temperature_controlled'],
                'id' => $rackID
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function delete($rackId)
    {
        $sqlSelectBatchIds = "SELECT `id` FROM `batches` WHERE `rack_id` = :rackId";
        $sqlDeleteMedicineBatch = "DELETE FROM `medicine_batch` WHERE `batch_id` = :batchId";
        $sqlDeleteBatch = "DELETE FROM `batches` WHERE `id` = :batchId";
        $sqlDeleteRack = "DELETE FROM `racks` WHERE `id` = :rackId";
    
        try {
            $this->db->beginTransaction();
    
            // Get all batch IDs associated with the rack
            $statement = $this->db->prepare($sqlSelectBatchIds);
            $statement->execute(['rackId' => $rackId]);
            $batchIds = $statement->fetchAll(PDO::FETCH_COLUMN, 0);
    
            // Delete from medicine_batch for each batch
            $statement = $this->db->prepare($sqlDeleteMedicineBatch);
            foreach ($batchIds as $batchId) {
                $statement->execute(['batchId' => $batchId]);
            }
    
            // Delete from batches for each batch
            $statement = $this->db->prepare($sqlDeleteBatch);
            foreach ($batchIds as $batchId) {
                $statement->execute(['batchId' => $batchId]);
            }
    
            // Delete the rack
            $statement = $this->db->prepare($sqlDeleteRack);
            $statement->execute(['rackId' => $rackId]);
    
            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getAllRacks()
    {
        $sql = "SELECT * FROM racks";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getRack($id) {
        $sql = "SELECT * FROM racks WHERE id = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }


}