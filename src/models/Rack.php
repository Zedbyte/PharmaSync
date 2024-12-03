<?php

namespace App\Models;

require_once __DIR__ . '/../../config/config.php';

use App\Models\BaseModel;
use \PDO;
use \PDOException;
use \Exception;

class Rack extends BaseModel
{

    public function save($data)
    {
        $sql = "INSERT INTO `racks` 
                SET
                    `location` = :location,
                    `temperature_controlled` = :temperature_controlled";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'location' => $data['location'],
                'temperature_controlled' => $data['temperature_controlled']
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function update($rackID, $data)
    {
        $sql = "UPDATE `racks` 
                SET
                    `location` = :location,
                    `temperature_controlled` = :temperature_controlled
                WHERE `id` = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'location' => $data['location'],
                'temperature_controlled' => $data['temperature_controlled'],
                'id' => $rackID
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getAllRacks()
    {
        $sql = "SELECT * FROM racks";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getRack($id) {
        $sql = "SELECT * FROM racks WHERE id = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }


}