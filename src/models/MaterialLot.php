<?php

namespace App\Models;

require_once __DIR__ . '/../../config/config.php';

use App\Models\BaseModel;
use \PDO;
use \PDOException;
use \Exception;

class MaterialLot extends BaseModel
{
    public function save($data)
    {
        $sql = "INSERT INTO `material_lot` 
                SET
                    `lot_id` = :lot_id,
                    `material_id` = :material_id,
                    `stock_level` = :stock_level,
                    `qc_status` = :qc_status,
                    `qc_notes` = :qc_notes,
                    `inspection_date` = :inspection_date,
                    `expiration_date` = :expiration_date";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'lot_id' => $data['lot_id'],
                'material_id' => $data['material_id'],
                'stock_level' => $data['stock_level'],
                'qc_status' => $data['qc_status'],
                'qc_notes' => $data['qc_notes'],
                'inspection_date' => $data['inspection_date'],
                'expiration_date' => $data['expiration_date']
            ]);

            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function update($lotId, $materialId, $data)
    {
        $sql = "UPDATE `material_lot` 
                SET
                    `stock_level` = :stock_level,
                    `qc_status` = :qc_status,
                    `qc_notes` = :qc_notes,
                    `inspection_date` = :inspection_date,
                    `expiration_date` = :expiration_date
                WHERE `lot_id` = :lot_id AND `material_id` = :material_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'stock_level' => $data['stock_level'],
                'qc_status' => $data['qc_status'],
                'qc_notes' => $data['qc_notes'],
                'inspection_date' => $data['inspection_date'],
                'expiration_date' => $data['expiration_date'],
                'lot_id' => $lotId,
                'material_id' => $materialId
            ]);

            return $statement->rowCount();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function delete($lotId, $materialId)
    {
        $sql = "DELETE FROM `material_lot` 
                WHERE `lot_id` = :lot_id AND `material_id` = :material_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'lot_id' => $lotId,
                'material_id' => $materialId
            ]);

            return $statement->rowCount();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getMaterialLot($lotId, $materialId)
    {
        $sql = "SELECT * FROM `material_lot` 
                WHERE `lot_id` = :lot_id AND `material_id` = :material_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'lot_id' => $lotId,
                'material_id' => $materialId
            ]);

            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getAllMaterialLot()
    {
        $sql = "SELECT * FROM `material_lot`";

        try {
            $statement = $this->db->query($sql);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

}