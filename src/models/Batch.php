<?php

namespace App\Models;

require_once __DIR__ . '/../../config/config.php';

use App\Models\BaseModel;
use \PDO;
use \PDOException;
use \Exception;

class Batch extends BaseModel
{
    public function save($data)
    {
        $sql = "INSERT INTO `batches` 
                SET
                    `production_date` = :production_date,
                    `rack_id` = :rack_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'production_date' => $data['production_date'],
                'rack_id' => $data['rack_id']
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function update($batchId, $data)
    {
        $sql = "UPDATE `batches` 
                SET
                    `production_date` = :production_date,
                    `rack_id` = :rack_id
                WHERE `id` = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'production_date' => $data['production_date'],
                'rack_id' => $data['rack'],
                'id' => $batchId
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function delete($batchId)
    {
        $sqlDeleteMedicineBatch = "DELETE FROM `medicine_batch` WHERE `batch_id` = :id";
        $sqlDeleteBatch = "DELETE FROM `batches` WHERE `id` = :id";
    
        try {
            $this->db->beginTransaction();
    
            // Delete from medicine_batch first
            $statement = $this->db->prepare($sqlDeleteMedicineBatch);
            $statement->execute(['id' => $batchId]);
    
            // Delete from batches
            $statement = $this->db->prepare($sqlDeleteBatch);
            $statement->execute(['id' => $batchId]);
    
            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getBatch($id)
    {
        $sql = "SELECT 
        b.id AS batch_id, 
        b.production_date, 
        b.rack_id AS rack_id_fk,  
        r.id AS rack_id_pk,
        r.location,
        r.temperature_controlled
        FROM batches b JOIN racks r ON b.rack_id = r.id
        WHERE b.id = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute(['id' => $id]);
            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getAllBatches()
    {
        $sql = "SELECT * FROM `batches`";

        try {
            $statement = $this->db->query($sql);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getBatchProductionRate() {
        $sql = "SELECT 
                YEAR(production_date) AS year,
                MONTH(production_date) AS month,
                COUNT(*) AS batch_count
            FROM 
                batches
            GROUP BY 
                YEAR(production_date), MONTH(production_date)
            ORDER BY 
                YEAR(production_date), MONTH(production_date);
            ";

        try {
            $statement = $this->db->query($sql);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }
}