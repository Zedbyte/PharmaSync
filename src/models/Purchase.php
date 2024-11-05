<?php

namespace App\Models;

require_once __DIR__ . '/../../config/config.php';

use App\Models\BaseModel;
use \PDO;
use \PDOException;
use \Exception;

class Purchase extends BaseModel
{
    public function save($data)
    {
        $sql = "INSERT INTO purchases 
                SET
                    `date`=:date,
                    `material_count`=:material_count,
                    `total_cost`=:total_cost,
                    `status`=:status,
                    `p_supplier_id`=:p_supplier_id";

        try {
            $statement = $this->db->prepare($sql);

            $statement->execute([
                'date' => $data['date'],
                'material_count' => $data['material_count'],
                'total_cost' => $data['total_cost'],
                'status' => $data['status'],
                'p_supplier_id' => $data['p_supplier_id']
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getPurchase($id)
    {
        $sql = "SELECT * FROM purchases WHERE id = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute(['id' => $id]);
            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getCount() {
        $sql = "SELECT COUNT(id) AS purchaseCount FROM purchases";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute();
            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

}