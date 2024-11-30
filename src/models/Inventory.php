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
    
    public function getTopMedicineStockByBatch($limit = 5) {
        $sql = "
            SELECT 
                m.name AS medicine_name,
                b.id AS batch_id,
                MAX(mb.stock_level) AS stock_level,
                mb.expiry_date AS expiry_date
            FROM 
                medicines m
            JOIN 
                medicine_batch mb ON m.id = mb.medicine_id
            JOIN 
                batches b ON mb.batch_id = b.id
            GROUP BY 
                m.name, b.id, mb.expiry_date
            ORDER BY 
                MAX(mb.stock_level) DESC
            LIMIT :limit
        ";
        
        try {
            $statement = $this->db->prepare($sql);
            $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getMostSoldMedicines() {
        $sql = "
            SELECT 
                m.name AS medicine_name,
                SUM(om.quantity) AS total_quantity_sold,
                m.unit_price AS unit_price,
                (SUM(om.quantity) * m.unit_price) AS total_revenue
            FROM 
                medicines m
            JOIN 
                order_medicine om ON m.id = om.medicine_id
            GROUP BY 
                m.id, m.name, m.unit_price
            ORDER BY 
                total_quantity_sold DESC
            LIMIT 10
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