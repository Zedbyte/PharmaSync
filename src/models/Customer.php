<?php

namespace App\Models;

require_once __DIR__ . '/../../config/config.php';

use App\Models\BaseModel;
use \PDO;
use \PDOException;
use \Exception;

class Customer extends BaseModel
{
    public function getAllCustomers()
    {
        $sql = "SELECT * FROM customers";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

        // Method to add a customer to the database
    public function addCustomer(string $name, string $email, string $address, string $contact_no): bool
    {
        $sql = "INSERT INTO customers (name, email, address, contact_no) 
                VALUES (:name, :email, :address, :contact_no)";

        try {
            $statement = $this->db->prepare($sql);
            $statement->bindParam(':name', $name);
            $statement->bindParam(':email', $email);
            $statement->bindParam(':address', $address);
            $statement->bindParam(':contact_no', $contact_no);
            return $statement->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Error adding customer: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function deleteCustomer(int $customerId): bool
    {
        $sql = "DELETE FROM customers WHERE id = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->bindParam(':id', $customerId, PDO::PARAM_INT);
            return $statement->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Error deleting customer: " . $e->getMessage(), (int)$e->getCode());
        }
    }
}