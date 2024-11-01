<?php

namespace App\Models;

class BaseModel
{
    protected $db;

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    public function fill($payload)
    {
        foreach ($payload as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function getDBCon() {
        return $this->db;
    }
}