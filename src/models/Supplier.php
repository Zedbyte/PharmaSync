<?php

namespace App\Models;

require_once __DIR__ . '/../../config/config.php';

use App\Models\BaseModel;
use \PDO;
use \PDOException;
use \Exception;

class Supplier extends BaseModel
{
    public function getAllSuppliers()
    {
        $sql = "SELECT * FROM suppliers";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

        // Method to add a supplier to the database
        public function addSupplier(string $name, string $email, string $address, string $contact_no): bool
    {
        $sql = "INSERT INTO suppliers (name, email, address, contact_no) 
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
            throw new Exception("Error adding supplier: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function deleteSupplier(int $supplierId): bool
    {
        $sql = "DELETE FROM suppliers WHERE id = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->bindParam(':id', $supplierId, PDO::PARAM_INT);
            return $statement->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Error deleting supplier: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function updateSupplier(int $id, string $name, string $email, string $address, string $contact_no): bool
    {
        $sql = "UPDATE suppliers 
                SET name = :name, email = :email, address = :address, contact_no = :contact_no 
                WHERE id = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            $statement->bindParam(':name', $name);
            $statement->bindParam(':email', $email);
            $statement->bindParam(':address', $address);
            $statement->bindParam(':contact_no', $contact_no);

            return $statement->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Error updating supplier: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getTotalSuppliers(): int
    {
        $sql = "SELECT COUNT(*) as total FROM suppliers";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            return (int) $result['total'];
        } catch (PDOException $e) {
            error_log("Error fetching total suppliers: " . $e->getMessage());
            return 0; // Return 0 if there's an error
        }
    }
}