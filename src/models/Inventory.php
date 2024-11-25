<?php

namespace App\Models;

require_once __DIR__ . '/../../config/config.php';

use App\Models\BaseModel;
use \PDO;
use \PDOException;
use \Exception;

class Inventory extends BaseModel
{

    public function getInventoryDistribution() {
        $medicineDistribution = $this->getMedicineInventoryDistribution();
        $materialDistribution = $this->getMaterialInventoryDistribution();
    
        return [
            'medicine' => $medicineDistribution,
            'material' => $materialDistribution
        ];
    }
    
    private function getMedicineInventoryDistribution() {
        $sql = "
            SELECT 
                YEAR(b.production_date) AS year,
                MONTH(b.production_date) AS month,
                SUM(mb.stock_level) AS total_stock_level
            FROM 
                batches b
            INNER JOIN 
                medicine_batch mb ON b.id = mb.batch_id
            GROUP BY 
                YEAR(b.production_date), MONTH(b.production_date)
            ORDER BY 
                YEAR(b.production_date), MONTH(b.production_date);
        ";
    
        try {
            $statement = $this->db->query($sql);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }


    private function getMaterialInventoryDistribution() {
        $sql = "
            SELECT 
                YEAR(p.date) AS year,
                MONTH(p.date) AS month,
                SUM(pm.quantity * pm.unit_price) AS total_inventory_value,
                SUM(pm.quantity) AS total_stock_level
            FROM 
                purchases p
            INNER JOIN 
                purchase_material pm ON p.id = pm.pm_purchase_id
            GROUP BY 
                YEAR(p.date), MONTH(p.date)
            ORDER BY 
                YEAR(p.date), MONTH(p.date);
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