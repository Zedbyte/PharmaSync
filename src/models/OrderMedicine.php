<?php

namespace App\Models;

require_once __DIR__ . '/../../config/config.php';

use App\Models\BaseModel;
use \PDO;
use \PDOException;
use \Exception;

class OrderMedicine extends BaseModel
{
    public function save($data)
    {
        $sql = "INSERT INTO `order_medicine` 
                SET
                    `order_id` = :order_id,
                    `purchase_id` = :purchase_id,
                    `quantity` = :quantity,
                    `total_price` = :total_price";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'order_id' => $data['order_id'],
                'purchase_id' => $data['purchase_id'],
                'quantity' => $data['quantity'],
                'total_price' => $data['total_price']
            ]);

            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function update($data)
    {
        $sql = "UPDATE `order_medicine` 
                SET
                    `quantity` = :quantity,
                    `total_price` = :total_price
                WHERE `order_id` = :order_id 
                AND `purchase_id` = :purchase_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'quantity' => $data['quantity'],
                'total_price' => $data['total_price'],
                'order_id' => $data['order_id'],
                'purchase_id' => $data['purchase_id']
            ]);

            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function updateOrInsert($data)
    {
        // Check if the record already exists
        $sql = "SELECT COUNT(*) FROM `order_medicine` 
                WHERE `order_id` = :order_id 
                AND `purchase_id` = :purchase_id";

        $statement = $this->db->prepare($sql);
        $statement->execute([
            'order_id' => $data['order_id'],
            'purchase_id' => $data['purchase_id']
        ]);

        $recordExists = $statement->fetchColumn() > 0;

        // If the record exists, update it; otherwise, insert a new record
        if ($recordExists) {
            return $this->update($data);
        } else {
            return $this->save($data);
        }
    }

    public function delete($data)
    {
        $sql = "DELETE FROM `order_medicine` 
                WHERE `order_id` = :order_id 
                AND `purchase_id` = :purchase_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'order_id' => $data['order_id'],
                'purchase_id' => $data['purchase_id']
            ]);

            return $statement->rowCount() > 0; // Returns true if a row was deleted
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getOrderMedicine($orderId, $purchaseId)
    {
        $sql = "SELECT * FROM `order_medicine` 
                WHERE `order_id` = :order_id 
                AND `purchase_id` = :purchase_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'order_id' => $orderId,
                'purchase_id' => $purchaseId
            ]);

            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getAllOrderMedicines()
    {
        $sql = "
        SELECT 
            o.id AS order_id,
            c.name AS customer_name,
            o.date AS date_of_order,
            o.product_count AS product_count,
            o.total_cost AS total_cost,
            o.payment_status AS payment_status,
            o.order_status AS order_status
        FROM 
            orders o
        INNER JOIN 
            customers c ON o.customer_id = c.id
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