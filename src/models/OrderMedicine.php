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
                    `medicine_id` = :medicine_id,
                    `quantity` = :quantity,
                    `unit_price` = :unit_price,
                    `total_price` = :total_price,
                    `batch_id` = :batch_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'order_id' => $data['order_id'],
                'medicine_id' => $data['medicine_id'],
                'quantity' => $data['quantity'],
                'unit_price' => $data['unit_price'],
                'total_price' => $data['total_price'],
                'batch_id' => $data['batch_id']
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
                AND `medicine_id` = :medicine_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'quantity' => $data['quantity'],
                'total_price' => $data['total_price'],
                'order_id' => $data['order_id'],
                'medicine_id' => $data['medicine_id']
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
                AND `medicine_id` = :medicine_id";

        $statement = $this->db->prepare($sql);
        $statement->execute([
            'order_id' => $data['order_id'],
            'medicine_id' => $data['medicine_id']
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
                AND `medicine_id` = :medicine_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'order_id' => $data['order_id'],
                'medicine_id' => $data['medicine_id']
            ]);

            return $statement->rowCount() > 0; // Returns true if a row was deleted
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getOrderMedicine($orderId, $medicineId)
    {
        $sql = "SELECT * FROM `order_medicine` 
                WHERE `order_id` = :order_id 
                AND `medicine_id` = :medicine_id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'order_id' => $orderId,
                'medicine_id' => $medicineId
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
        ORDER BY date DESC
        ";

        try {
            $statement = $this->db->query($sql);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }


    public function getOrderData($orderID)
    {
        $sql = "SELECT 
                o.id AS order_id,
                o.date AS order_date,
                o.product_count,
                o.total_cost AS order_total_cost,
                o.payment_status,
                o.order_status,
                c.id AS customer_id,
                c.name AS customer_name,
                c.email AS customer_email,
                c.address AS customer_address,
                c.contact_no AS customer_contact_no,
                om.medicine_id,
                om.quantity AS ordered_quantity,
                om.total_price AS medicine_total_price,
                om.batch_id AS batch_id,
                m.name AS medicine_name,
                m.type AS medicine_type,
                m.composition,
                m.therapeutic_class,
                m.regulatory_class,
                m.manufacturing_details,
                m.formulation_id,
                m.unit_price AS medicine_unit_price,
                mb.expiry_date AS batch_expiry_date,
                b.id AS batch_id,
                b.production_date
            FROM 
                orders o
            JOIN 
                customers c ON o.customer_id = c.id
            JOIN 
                order_medicine om ON o.id = om.order_id
            JOIN 
                medicines m ON om.medicine_id = m.id
            JOIN 
                medicine_batch mb ON om.batch_id = mb.batch_id
                AND om.medicine_id = mb.medicine_id
            JOIN 
                batches b ON mb.batch_id = b.id
            WHERE 
                o.id = :order_id";
    
        try {
            $statement = $this->db->prepare($sql);
            $statement->execute(['order_id' => $orderID]);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }


    public function deleteOrderData($orderID)
    {
        try {
            // Start a transaction
            $this->db->beginTransaction();

            // Delete related records from order_medicine first
            $sqlDeleteOrderMedicine = "DELETE FROM order_medicine WHERE order_id = :order_id";
            $statementOM = $this->db->prepare($sqlDeleteOrderMedicine);
            $statementOM->execute(['order_id' => $orderID]);

            // Delete the order record
            $sqlDeleteOrder = "DELETE FROM orders WHERE id = :order_id";
            $statementOM = $this->db->prepare($sqlDeleteOrder);
            $statementOM->execute(['order_id' => $orderID]);

            // Commit the transaction
            $this->db->commit();
            return $statementOM->rowCount(); // Returns the number of deleted rows from purchases

        } catch (PDOException $e) {
            // Rollback the transaction on error
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

}