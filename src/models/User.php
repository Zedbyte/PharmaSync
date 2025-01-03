<?php

namespace App\Models;

require_once __DIR__ . '/../../config/config.php';
require_once LIB_URL . '/password_hash.inc.php';

use App\Models\BaseModel;
use \PDO;
use \PDOException;
use \Exception;

class User extends BaseModel
{
    public function save($data)
    {   
        $this->db->beginTransaction();
        $sql = "INSERT INTO users 
        SET
            first_name = :first_name,
            last_name = :last_name,
            username = :username,
            contact_no = :contact_no,
            email_address = :email_address,
            password_hash = :password_hash,
            role = :role,
            gender = :gender";

        try {
            $statement = $this->db->prepare($sql);

            // Hash password before saving
            $password_hash = hashPassword($data['password']);
            
            $statement->execute([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'username' => $data['username'],
                'contact_no' => $data['contact_no'],
                'email_address' => $data['email_address'],
                'password_hash' => $password_hash,
                'role' => $data['role'],
                'gender' => $data['gender']
            ]);
            $this->db->commit();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }


    public function verifyAccess($email, $password)
    {
        try {
            $sql = "SELECT * FROM users WHERE email_address = :email_address";
            $statement = $this->db->prepare($sql);
            $statement->execute(['email_address' => $email]);

            $user = $statement->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                // Remove the password hash from the user object before returning
                unset($user['password_hash']);
                return $user;
            }

            return null; // Authentication failed
        }
        catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    // Method to find a user by ID
    public function findById($id)
    {
        try {
            $sql = "SELECT * FROM users WHERE id = :id";
            $statement = $this->db->prepare($sql);
            $statement->execute(['id' => $id]);

            $user = $statement->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Remove sensitive information before returning
                unset($user['password_hash']); // Ensure you do not expose the password hash
                return $user;
            }

            return null; // User not found
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function checkUniqueConstraints($data)
    {
        $errors = [];
        
        // Check if username is unique
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $stmt->execute(['username' => $data['username']]);
        if ($stmt->fetchColumn() > 0) {
            $errors['username'] = 'Username already exists.';
        }

        // Check if contact number is unique
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE contact_no = :contact_no");
        $stmt->execute(['contact_no' => $data['contact_no']]);
        if ($stmt->fetchColumn() > 0) {
            $errors['contact_no'] = 'Contact number already exists.';
        }

        // Check if email address is unique
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email_address = :email_address");
        $stmt->execute(['email_address' => $data['email_address']]);
        if ($stmt->fetchColumn() > 0) {
            $errors['email_address'] = 'Email address already exists.';
        }

        return $errors;
    }

    public function getRoleById($id)
    {
        try {
            $sql = "SELECT role FROM users WHERE id = :id";
            $statement = $this->db->prepare($sql);
            $statement->execute(['id' => $id]);

            $user = $statement->fetch(PDO::FETCH_ASSOC)['role'];

            if ($user) {
                return $user;
            }

            return null; // User not found
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }
}