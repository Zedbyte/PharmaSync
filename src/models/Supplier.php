<?php

namespace App\Models;

require_once __DIR__ . '/../../config/config.php';

use App\Models\BaseModel;
use PDO;
use PDOException;
use Exception;

class Supplier extends BaseModel
{
    // Fetch all suppliers
    public function getAllSuppliers(): array {
        $sql = "SELECT * FROM suppliers";

        try {
            $statement = $this->db->query($sql);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->handleDatabaseException($e, "Error fetching suppliers.");
        }
    }

    // Add a new supplier
    public function addSupplier(string $name, string $email, string $address, string $contact_no): void {
        $sql = "INSERT INTO suppliers (name, email, address, contact_no) 
                VALUES (:name, :email, :address, :contact_no)";

        $this->executeStatement($sql, [
            ':name' => $name,
            ':email' => $email,
            ':address' => $address,
            ':contact_no' => $contact_no
        ]);
    }

    // Delete a supplier by ID
    public function deleteSupplier(int $supplierId): bool {
        $sql = "DELETE FROM suppliers WHERE id = :id";

        $this->executeStatement($sql, [':id' => $supplierId]);
        return true;
    }

    // Update an existing supplier's details
    public function updateSupplier(int $id, string $name, string $email, string $address, string $contact_no): bool {
        $sql = "UPDATE suppliers 
                SET name = :name, email = :email, address = :address, contact_no = :contact_no 
                WHERE id = :id";

        $this->executeStatement($sql, [
            ':id' => $id,
            ':name' => $name,
            ':email' => $email,
            ':address' => $address,
            ':contact_no' => $contact_no
        ]);

        return true;
    }

    // General method for executing prepared statements
    private function executeStatement(string $sql, array $params): void {
        try {
            $statement = $this->db->prepare($sql);
            $statement->execute($params);
        } catch (PDOException $e) {
            $this->handleDatabaseException($e, "Error executing query.");
        }
    }

    // Handle database-related exceptions
    private function handleDatabaseException(PDOException $e, string $customMessage): void {
        error_log($e->getMessage());
        throw new Exception($customMessage . " Details: " . $e->getMessage(), (int)$e->getCode());
    }
}
