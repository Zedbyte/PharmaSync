<?php

namespace App\Models;

require_once __DIR__ . '/../../config/config.php';

use App\Models\BaseModel;
use \PDO;
use \PDOException;
use \Exception;

class MedicineBatch extends BaseModel
{
    
    public function save($data)
    {
        $sql = "INSERT INTO `medicine_batch` 
                SET
                    `medicine_id` = :medicine_id,
                    `batch_id` = :batch_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'medicine_id' => $data['medicine_id'],
                'batch_id' => $data['batch_id']
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function delete($medicineId, $batchId)
    {
        $sql = "DELETE FROM `medicine_batch` 
                WHERE `medicine_id` = :medicine_id 
                AND `batch_id` = :batch_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'medicine_id' => $medicineId,
                'batch_id' => $batchId
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function decreaseStockLevel($medicineId, $batchId, $quantity)
    {   

        $sql = "UPDATE `medicine_batch` 
                SET `stock_level` = `stock_level` - :quantity 
                WHERE medicine_id = :medicine_id AND 
                batch_id = :batch_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'quantity' => $quantity,
                'batch_id' => $batchId,
                'medicine_id' => $medicineId
            ]);

            if ($statement->rowCount() === 0) {
                throw new Exception("No rows were updated. Invalid batch ID or insufficient stock.");
            }

            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }


    public function getMedicineBatches($medicineId)
    {
        $sql = "SELECT * FROM `medicine_batch` 
                WHERE `medicine_id` = :medicine_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute(['medicine_id' => $medicineId]);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getBatchMedicines($batchId)
    {
        $sql = "SELECT * FROM `medicine_batch` 
                WHERE `batch_id` = :batch_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute(['batch_id' => $batchId]);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }


    public function getMedicineBatchData($medicineId, $batchId)
    {
        $sql = "SELECT * FROM `medicine_batch`
                WHERE `medicine_id` = :medicine_id 
                AND `batch_id` = :batch_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'medicine_id' => $medicineId,
                'batch_id' => $batchId
            ]);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }


    public function getMedicineBatchStock($medicineId, $batchId)
    {
        $sql = "SELECT stock_level FROM `medicine_batch`
                WHERE `medicine_id` = :medicine_id 
                AND `batch_id` = :batch_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'medicine_id' => $medicineId,
                'batch_id' => $batchId
            ]);
            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }


}