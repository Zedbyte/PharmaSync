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
                    `expiry_date` = :expiry_date,
                    `quantity` = :quantity
                WHERE `id` = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'production_date' => $data['production_date'],
                'expiry_date' => $data['expiry_date'],
                'quantity' => $data['quantity'],
                'id' => $batchId
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function delete($batchId)
    {
        $sql = "DELETE FROM `batches` WHERE `id` = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute(['id' => $batchId]);
        } catch (PDOException $e) {
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
}