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
                    `batch_id` = :batch_id,
                    `stock_level` = :stock_level,
                    `expiry_date` = :expiry_date";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'medicine_id' => $data['medicine_id'],
                'batch_id' => $data['batch_id'],
                'stock_level' => $data['stock_level'],
                'expiry_date' => $data['expiry_date']
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function delete($medicineId, $batchId)
    {
        try {
            // Start transaction
            $this->db->beginTransaction();
    
            // Delete the medicine-batch association
            $deleteSql = "DELETE FROM `medicine_batch` 
                            WHERE `medicine_id` = :medicine_id 
                            AND `batch_id` = :batch_id";
            $deleteStmt = $this->db->prepare($deleteSql);
            $deleteStmt->execute([
                'medicine_id' => $medicineId,
                'batch_id' => $batchId
            ]);
    
            // Check and delete the batch if no associations remain
            $this->deleteBatchIfUnused($batchId);
    
            // Commit the transaction
            $this->db->commit();
    
        } catch (PDOException $e) {
            // Rollback transaction on failure
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    private function deleteBatchIfUnused($batchId)
    {
        try {
            // Check if the batch still has any remaining associations
            $checkSql = "SELECT COUNT(*) AS total 
                            FROM `medicine_batch` 
                            WHERE `batch_id` = :batch_id";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute(['batch_id' => $batchId]);
            $row = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($row && $row['total'] == 0) {
                // If no associations remain, delete the batch
                $deleteBatchSql = "DELETE FROM `batches` 
                                    WHERE `id` = :batch_id";
                $deleteBatchStmt = $this->db->prepare($deleteBatchSql);
                $deleteBatchStmt->execute(['batch_id' => $batchId]);

                error_log("Batch $batchId deleted as it has no remaining associations. [from deleteBatchIfUnused()]");
            } else {
                error_log("Batch $batchId still has associations and was not deleted. [from deleteBatchIfUnused()]");
            }
        } catch (PDOException $e) {
            error_log("Error checking or deleting batch $batchId: " . $e->getMessage());
            throw new Exception("Error managing batch $batchId: " . $e->getMessage(), (int)$e->getCode());
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

    public function adjustStockLevel($medicineID, $batchID, $adjustment)
    {
        // Adjustment can be positive (increase) or negative (decrease)
        $sql = "UPDATE `medicine_batch` 
                SET `stock_level` = `stock_level` + :adjustment 
                WHERE `medicine_id` = :medicine_id 
                AND `batch_id` = :batch_id";

        $statement = $this->db->prepare($sql);
        $statement->execute([
            'adjustment' => $adjustment,
            'medicine_id' => $medicineID,
            'batch_id' => $batchID
        ]);

        // Check if the update was successful
        return $statement->rowCount() > 0;
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

    public function getBatchMedicinesAndBatchData($medicineID)
    {
        $sql = "SELECT * FROM medicine_batch mb JOIN batches b ON mb.batch_id = b.id
                WHERE mb.medicine_id = :medicine_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute(['medicine_id' => $medicineID]);
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