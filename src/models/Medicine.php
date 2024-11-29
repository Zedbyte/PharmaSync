<?php

namespace App\Models;

require_once __DIR__ . '/../../config/config.php';

use App\Models\BaseModel;
use \PDO;
use \PDOException;
use \Exception;

class Medicine extends BaseModel
{
    public function save($data)
    {
        $sql = "INSERT INTO `medicines` 
                SET
                    `name` = :name,
                    `type` = :type,
                    `composition` = :composition,
                    `therapeutic_class` = :therapeutic_class,
                    `regulatory_class` = :regulatory_class,
                    `manufacturing_details` = :manufacturing_details,
                    `unit_price` = :unit_price";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'name' => $data['medicine_name'],
                'type' => $data['medicine_type'],
                'composition' => $data['composition'],
                'therapeutic_class' => $data['therapeutic_class'],
                'regulatory_class' => $data['regulatory_class'],
                'manufacturing_details' => $data['manufacturing_details'],
                'unit_price' => $data['unit_price'],
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function update($medicineId, $data)
    {
        $sql = "UPDATE `medicines` 
                SET
                    `name` = :name,
                    `type` = :type,
                    `composition` = :composition,
                    `therapeutic_class` = :therapeutic_class,
                    `regulatory_class` = :regulatory_class,
                    `manufacturing_details` = :manufacturing_details,
                    `unit_price` = :unit_price
                WHERE `id` = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'name' => $data['name'],
                'type' => $data['type'],
                'composition' => $data['composition'],
                'therapeutic_class' => $data['therapeutic_class'],
                'regulatory_class' => $data['regulatory_class'],
                'manufacturing_details' => $data['manufacturing_details'],
                'unit_price' => $data['unit_price'],
                'id' => $medicineId
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function delete($medicineId, $deepDelete)
    {
        try {
            $this->db->beginTransaction();
            
            $errors = [];
            // Delete from intersection and related tables
            $errors[] = $this->deleteOrderMedicine($medicineId, $deepDelete);
            
            $this->deleteProductFormulation($medicineId);
            
            $errors[] = $this->deleteMedicineBatch($medicineId, $deepDelete);   

            // Delete the medicine itself
            $this->deleteMedicine($medicineId);
    
            $this->db->commit();


            $errors = array_filter($errors);

            if (!empty($errors)) {
                return $errors;
            }
            return null;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new Exception("Deletion failed: " . $e->getMessage());
        }
    }
    
    private function deleteOrderMedicine($medicineId, $deleteMedicine)
    {   
        try {
            $errors = null;
            // Step 1: Find the `order_id` for the given `medicine_id`.
            $findOrderSql = "SELECT order_id FROM order_medicine WHERE medicine_id = :medicine_id";
            $stmt = $this->db->prepare($findOrderSql);
            $stmt->execute(['medicine_id' => $medicineId]);
            $orderIds = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $deleteSql = "DELETE FROM order_medicine WHERE medicine_id = :medicine_id";
            $stmt = $this->db->prepare($deleteSql);
            $stmt->execute(['medicine_id' => $medicineId]);

            if (!empty($orderIds)) {
                foreach ($orderIds as $orderIdArray) {
                    error_log(print_r($orderIdArray,true));
                    $orderId = $orderIdArray['order_id'];
                    // Step 2: Check if the `order_id` is associated with other `medicine_id` records.
                    $countSql = "SELECT COUNT(*) FROM order_medicine WHERE order_id = :order_id";
                    $stmt = $this->db->prepare($countSql);
                    $stmt->execute(['order_id' => $orderId]);
                    $count = $stmt->fetchColumn(); // Get the count of records.

                    // Step 4: If there are other `medicine_id` records, only delete the `medicine_id`.

                    if ($deleteMedicine === 'true') {
                        if ($count === 0) {
                            // Step 5: Delete the order if it's empty.
                            $deleteOrderSql = "DELETE FROM orders WHERE id = :order_id";
                            $stmt = $this->db->prepare($deleteOrderSql);
                            $stmt->execute(['order_id' => $orderId]);

                            $errors[] = "Order ID: $orderId deleted";
                        }
                        else {
                            $errors[] = "Order ID: $orderId is still associated with other medicines.";
                        }
                    }
                }   
            } else {
                $errors[] = "No orders found for medicine ID: $medicineId.";
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
        return $errors;
    }
    
    private function deleteProductFormulation($medicineId)
    {
        $sql = "DELETE FROM product_formulation WHERE medicine_id = :medicine_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['medicine_id' => $medicineId]);
    }
    
    private function deleteMedicineBatch($medicineId, $deleteBatch)
    {
        try {
            $errors = null;
            // Step 1: Find all `batch_id` values associated with the given `medicine_id`.
            $findBatchSql = "SELECT batch_id FROM medicine_batch WHERE medicine_id = :medicine_id";
            $stmt = $this->db->prepare($findBatchSql);
            $stmt->execute(['medicine_id' => $medicineId]);
            $batchIds = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Step 2: Delete the record for the given `medicine_id` and `batch_id`.
            $deleteSql = "DELETE FROM medicine_batch WHERE medicine_id = :medicine_id";
            $stmt = $this->db->prepare($deleteSql);
            $stmt->execute(['medicine_id' => $medicineId]);
        
            if (!empty($batchIds)) {
                foreach ($batchIds as $batchIdArray) {
                    $batchId = $batchIdArray['batch_id'];
                    // Step 3: Check if the `batch_id` is associated with other `medicine_id` records.
                    $countSql = "SELECT COUNT(*) FROM medicine_batch WHERE batch_id = :batch_id";
                    $stmt = $this->db->prepare($countSql);
                    $stmt->execute(['batch_id' => $batchId]);
                    $count = $stmt->fetchColumn(); // Get the count of records.
                    // error_log(print_r($count,true));exit;

                    // Step 4: If there are no other medicines associated with the batch and `deleteBatch` is true, delete the batch.
                    if ($deleteBatch === 'true') {
                        if ($count === 0) {
                            $deleteBatchSql = "DELETE FROM batches WHERE id = :batch_id";
                            $stmt = $this->db->prepare($deleteBatchSql);
                            $stmt->execute(['batch_id' => $batchId]);

                            $errors[] = "Batch ID: $batchId deleted";
                        }
                        else {
                            $errors[] = "Batch ID: $batchId is still associated with other medicines.";
                        }
                    }
                }
            }
            else {
                $errors[] = "No batches found for medicine ID: $medicineId.";
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
        return $errors;
    }
    
    
    private function deleteMedicine($medicineId)
    {
        $sql = "DELETE FROM medicines WHERE id = :medicine_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['medicine_id' => $medicineId]);
    }
    

    public function getMedicine($id)
    {
        $sql = "SELECT * FROM `medicines` WHERE `id` = :id";

        try {
            $statement = $this->db->prepare($sql);
            $statement->execute(['id' => $id]);
            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getAllMedicines()
    {
        $sql = "SELECT * FROM `medicines`";

        try {
            $statement = $this->db->query($sql);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getAllMedicinesByType($type)
    {   
        if (empty($type)) $type='%';

        $sql = "SELECT * FROM `medicines` WHERE `type` LIKE :type ORDER BY name";

        try {
            $statement = $this->db->prepare($sql);
            $statement->bindValue(':type', $type, PDO::PARAM_STR);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Database error occurred: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function getMedicineUnitPrice($id)
    {
        $sql = "SELECT unit_price FROM `medicines` WHERE `id` = :id";

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