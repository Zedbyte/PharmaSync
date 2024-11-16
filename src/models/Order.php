<?php

namespace App\Models;

require_once __DIR__ . '/../../config/config.php';

use App\Models\BaseModel;
use \PDO;
use \PDOException;
use \Exception;

class Order extends BaseModel
{
    public function save($data)
    {
        $sql = "INSERT INTO `orders` 
                SET
                    `date` = :date,
                    `product_count` = :product_count,
                    `total_cost` = :total_cost,
                    `payment_status` = :payment_status,
                    `order_status` = :order_status,
                    `customer_id` = :customer_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'date' => $data['date'],
                'product_count' => $data['product_count'],
                'total_cost' => $data['total_cost'],
                'payment_status' => $data['payment_status'],
                'order_status' => $data['order_status'],
                'customer_id' => $data['customer_id']
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function update($orderId, $data)
    {
        $sql = "UPDATE `orders` 
                SET
                    `date` = :date,
                    `product_count` = :product_count,
                    `total_cost` = :total_cost,
                    `payment_status` = :payment_status,
                    `order_status` = :order_status,
                    `customer_id` = :customer_id
                WHERE `id` = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'date' => $data['date'],
                'product_count' => $data['product_count'],
                'total_cost' => $data['total_cost'],
                'payment_status' => $data['payment_status'],
                'order_status' => $data['order_status'],
                'customer_id' => $data['customer_id'],
                'id' => $orderId
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function delete($orderId)
    {
        $sql = "DELETE FROM `orders` WHERE `id` = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute(['id' => $orderId]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getOrder($id)
    {
        $sql = "SELECT * FROM `orders` WHERE `id` = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute(['id' => $id]);
            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getAllOrders()
    {
        $sql = "SELECT * FROM `orders`";

        try {
            $statement = $this->db->query($sql);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }
}