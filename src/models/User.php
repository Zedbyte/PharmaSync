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
        $sql = "INSERT INTO users 
                SET
                    `name`=:complete_name,
                    username=:username,
                    contact_no=:contact_no,
                    email_address=:email_address,
                    `password_hash`=:password_hash,
                    `role`=:role";

        try {
            $statement = $this->db->prepare($sql);
            
            $password_hash = hashPassword($data['password_hash']);
            
            $statement->execute([
                'complete_name' => $data['complete_name'],
                'username' => $data['username'],
                'contact_no' => $data['contact_no'],
                'email_address' => $data['email_address'],
                'password_hash' => $password_hash,
                'role' => $data['role']
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
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
}