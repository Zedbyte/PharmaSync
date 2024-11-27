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

    public function update($data) {
        $sql = "UPDATE `medicine_batch` 
        SET
            `stock_level` = :stock_level,
            `expiry_date` = :expiry_date
        WHERE
            `medicine_id` = :medicine_id AND 
            `batch_id` = :batch_id";

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

    public function batchExists($medicineId, $batchId) {
        $sql = "SELECT COUNT(*) FROM `medicine_batch` WHERE `medicine_id` = :medicine_id AND `batch_id` = :batch_id";
        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'medicine_id' => $medicineId,
                'batch_id' => $batchId
            ]);
            return $statement->fetchColumn() > 0;
        } catch (PDOException $e) {
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

    public function getBatchMedicinesAndBatchData($medicineID, $batchID=null)
    {
        // Base SQL query
        $sql = "SELECT * FROM medicine_batch mb 
        JOIN batches b ON mb.batch_id = b.id
        WHERE mb.medicine_id = :medicine_id";

        // Bind parameters
        $params = ['medicine_id' => $medicineID];

        // Add condition for batch_id if it exists
        if (!empty($batchID)) {
            $sql .= " AND mb.batch_id = :batch_id";
            $params['batch_id'] = $batchID;
        }

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


    public function getMedicineBatchData($medicineId, $batchId)
    {
        $sql = "SELECT * FROM `medicine_batch` mb
                JOIN `medicines` m ON m.id = mb.medicine_id
                JOIN `batches` b ON b.id = mb.batch_id
                JOIN `racks` r ON r.id = b.rack_id
                WHERE mb.medicine_id = :medicine_id 
                AND mb.batch_id = :batch_id";

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

    public function getProductCount() {
        $sql = "SELECT COUNT(DISTINCT `medicine_id`) AS product_count FROM `medicine_batch`";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute();
            return $statement->fetch(PDO::FETCH_ASSOC)['product_count'];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getTotalStocks()
    {
        $sql = "SELECT SUM(`stock_level`) AS total_stocks FROM `medicine_batch`";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute();
            return $statement->fetch(PDO::FETCH_ASSOC)['total_stocks'];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getNearingOutOfStock()
    {
        $sql = "SELECT COUNT(DISTINCT `batch_id`) AS out_of_stock FROM `medicine_batch`
                WHERE `stock_level` <= 50";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute();
            return $statement->fetch(PDO::FETCH_ASSOC)['out_of_stock'];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getExpiringSoon()
    {
        $sql = "SELECT COUNT(DISTINCT `medicine_id`) AS expiring_soon FROM `medicine_batch`
                WHERE `expiry_date` BETWEEN CURDATE() AND LAST_DAY(CURDATE())";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute();
            return $statement->fetch(PDO::FETCH_ASSOC)['expiring_soon'];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getMedicineBatchListOrderedByExpiry()
    {
        $sql = "SELECT 
                mb.medicine_id, 
                m.name AS medicine_name,
                mb.batch_id, 
                mb.expiry_date,
                mb.stock_level
                FROM medicine_batch mb
                JOIN medicines m ON mb.medicine_id = m.id
                ORDER BY `expiry_date` ASC LIMIT 10";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);  // Returns an array of medicine and batch pairs
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getMedicineStockDistribution() {
        $sql = "
        SELECT 
            m.id AS medicine_id,
            m.name AS medicine_name, 
            SUM(mb.stock_level) AS total_stock
        FROM 
            medicine_batch mb
        JOIN 
            medicines m ON mb.medicine_id = m.id
        GROUP BY 
            m.id, m.name
        ORDER BY 
            m.name ASC
        ";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC); // Returns an array of medicine names and total stock
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }
}