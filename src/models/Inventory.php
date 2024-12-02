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
    

    public function getCurrentMonthProductionEfficiencyWithComparison()
    {
        $sql = "
            SELECT 
                MONTH(production_date) AS month, 
                YEAR(production_date) AS year, 
                COUNT(id) AS batch_count
            FROM batches
            WHERE production_date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
            GROUP BY YEAR(production_date), MONTH(production_date)
            ORDER BY YEAR(production_date), MONTH(production_date)";
    
        try {
            $statement = $this->db->query($sql);
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
    
            $currentMonth = date('m');
            $currentYear = date('Y');
    
            $currentBatchCount = 0;
            $previousBatchCount = 0;
    
            foreach ($data as $index => $row) {
                if ($row['month'] == $currentMonth && $row['year'] == $currentYear) {
                    $currentBatchCount = $row['batch_count'];
                    if (isset($data[$index - 1])) {
                        $previousBatchCount = $data[$index - 1]['batch_count'];
                    }
                    break;
                }
            }
    
            $percentageChange = ($previousBatchCount > 0)
                ? (($currentBatchCount - $previousBatchCount) / $previousBatchCount) * 100
                : null; // Null if there's no data for the previous month
    
            return [
                'month' => $currentMonth,
                'year' => $currentYear,
                'current_batch_count' => $currentBatchCount,
                'previous_batch_count' => $previousBatchCount,
                'percentage_change' => $percentageChange
            ];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }
    
    public function getCurrentMonthOrdersWithComparison()
{
    $sql = "
        SELECT 
            MONTH(date) AS month, 
            YEAR(date) AS year, 
            COUNT(id) AS total_orders
        FROM `orders`
        WHERE date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
        GROUP BY YEAR(date), MONTH(date)
        ORDER BY YEAR(date), MONTH(date)";

    try {
        $statement = $this->db->query($sql);
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);

        $currentMonth = date('m');
        $currentYear = date('Y');

        $currentOrderCount = 0;
        $previousOrderCount = 0;

        foreach ($data as $index => $row) {
            if ($row['month'] == $currentMonth && $row['year'] == $currentYear) {
                $currentOrderCount = $row['total_orders'];
                if (isset($data[$index - 1])) {
                    $previousOrderCount = $data[$index - 1]['total_orders'];
                }
                break;
            }
        }

        $percentageChange = ($previousOrderCount > 0)
            ? (($currentOrderCount - $previousOrderCount) / $previousOrderCount) * 100
            : null; // Null if there's no data for the previous month

        return [
            'month' => $currentMonth,
            'year' => $currentYear,
            'current_order_count' => $currentOrderCount,
            'previous_order_count' => $previousOrderCount,
            'percentage_change' => $percentageChange
        ];
    } catch (PDOException $e) {
        error_log($e->getMessage());
        throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
    }
}

    
    


    public function getSalesPerformanceForCurrentAndPreviousMonth()
    {
        $sql = "
            SELECT 
                MONTH(date) AS month, 
                YEAR(date) AS year, 
                COUNT(id) AS total_orders, 
                SUM(total_cost) AS total_revenue
            FROM orders
            WHERE date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
            GROUP BY YEAR(date), MONTH(date)
            ORDER BY YEAR(date), MONTH(date)";

        try {
            $statement = $this->db->query($sql);
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);

            $currentMonth = date('m');
            $currentYear = date('Y');

            $currentData = null;
            $previousData = null;

            foreach ($data as $index => $row) {
                if ($row['month'] == $currentMonth && $row['year'] == $currentYear) {
                    $currentData = $row;
                    if (isset($data[$index - 1])) {
                        $previousData = $data[$index - 1];
                    }
                    break;
                }
            }

            $percentageChange = ($previousData && $previousData['total_orders'] > 0)
                ? (($currentData['total_orders'] - $previousData['total_orders']) / $previousData['total_orders']) * 100
                : null;

            return [
                'current' => $currentData,
                'previous' => $previousData,
                'percentage_change' => $percentageChange
            ];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    

    public function getPurchaseFrequencyForCurrentAndPreviousMonth()
    {
        $sql = "
            SELECT 
                MONTH(date) AS month, 
                YEAR(date) AS year, 
                COUNT(id) AS total_purchases, 
                SUM(total_cost) AS total_spending
            FROM purchases
            WHERE date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
            GROUP BY YEAR(date), MONTH(date)
            ORDER BY YEAR(date), MONTH(date)";

        try {
            $statement = $this->db->query($sql);
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);

            $currentMonth = date('m');
            $currentYear = date('Y');

            $currentData = null;
            $previousData = null;

            foreach ($data as $index => $row) {
                if ($row['month'] == $currentMonth && $row['year'] == $currentYear) {
                    $currentData = $row;
                    if (isset($data[$index - 1])) {
                        $previousData = $data[$index - 1];
                    }
                    break;
                }
            }

            $percentageChange = ($previousData && $previousData['total_purchases'] > 0)
                ? (($currentData['total_purchases'] - $previousData['total_purchases']) / $previousData['total_purchases']) * 100
                : null;

            return [
                'current' => $currentData,
                'previous' => $previousData,
                'percentage_change' => $percentageChange
            ];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }




}