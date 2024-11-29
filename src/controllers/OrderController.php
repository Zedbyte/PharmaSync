<?php

namespace App\Controllers;

use App\Models\OrderMedicine;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\MedicineBatch;
use \Exception;

require_once __DIR__ . '/../../config/config.php';
require_once LIB_URL . '/get_dbcon.inc.php';
require_once LIB_URL . '/debug_to_console.inc.php';
require_once 'BaseController.php';

class OrderController extends BaseController {
    protected $twig;
    protected $db;

    // Constructor to pass Twig environment for rendering templates
    public function __construct($twig) {
        parent::__construct();
        $this->twig = $twig;
        $this->db = returnDBCon(new OrderMedicine()); // Initialize DB connection once
    }

    public function display($errors = []) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
            // Extract the value from POST, e.g., the selected tab
            $selectedTab = $_POST['selectedTab'] ?? null;

            // Build the query parameters
            $queryParams = array_filter([
                'tab' => $selectedTab, // Add the selected tab
            ]);

            // Redirect to the new URL with the query string
            header('Location: /order-list?' . http_build_query($queryParams));
            exit;
        }

        $status = $_GET['tab'] ?? 'all'; 

        $customerObject = new Customer();
        $customerData = $customerObject->getAllCustomers();

        $orderMedicineObject = new OrderMedicine();
        $orderMedicineData = $orderMedicineObject->getAllOrderMedicines($status);

        $orderObject = new Order();
        $ordersCount = $orderObject->getOrdersCount();
        $fulfilledOrdersCount = $orderObject->getFulfilledOrdersCount();
        $paidOrdersCount = $orderObject->getPaidOrdersCount();
        $unpaidOrdersCount = $orderObject->getUnpaidOrdersCount();
        $monthlyRevenue = $orderObject->getMonthlyRevenue();

        $ordersData = [
            'ordersCount' => $ordersCount,
            'fulfilledOrdersCount' => $fulfilledOrdersCount,
            'paidOrdersCount' => $paidOrdersCount,
            'monthlyRevenue' => $monthlyRevenue,
            'unpaidOrdersCount' => $unpaidOrdersCount
        ];

        echo $this->twig->render('order-list.html.twig', [
            'ASSETS_URL' => ASSETS_URL,
            'orderMedicines' => $orderMedicineData,
            'customers' => $customerData,
            'ordersData' => $ordersData,
            'errors' => $errors
        ]);
    }

    public function viewOrder($data) {

        $orderMedicineObject = new OrderMedicine();

        $orderData = $orderMedicineObject->getOrderData($data[1]);
        
        echo $this->twig->render('view-order.html.twig', [
            'orderData' => $orderData
        ]);
    }

    public function deleteOrder($orderID) {
        $orderMedicineObject = new OrderMedicine();

        $orderMedicineData = $orderMedicineObject->deleteOrderData($orderID);
        
        header('Location: /order-list');
    }

    public function updateOrder($data) {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateUpdatedOrderData($data);

            if (!empty($errors)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'errors' => $errors]);
                return;
            }
            
        
            try {
                $this->db->beginTransaction();
        
                $orderID = $data['order_id'];
                $orderObject = new Order();
                
                if (count($data['quantity']) !== count($data['medicine_id'])) {
                    throw new Exception("Mismatch between quantity and medicine IDs");
                }
                
                $medicineObject = new Medicine();
                $totalCost = 0;
                
                foreach ($data['quantity'] as $index => $quantity) {
                    $medicineID = $data['medicine_name'][$index];
                    $unitPrice = $medicineObject->getMedicineUnitPrice($medicineID)["unit_price"];
                    $totalCost += (int) $quantity * $unitPrice;
                }
                
                
                
                $orderObject->update($data['orderID'], [
                    'date' => $data['order_date'],
                    'product_count' => count($data['medicine_name']),
                    'total_cost' => $totalCost,
                    'payment_status' => $data['payment_status'],
                    'order_status' => $data['order_status'],
                    'customer_id' => $data['customer'],
                ]);

                
                
                $orderMedicineObject = new OrderMedicine();
                $medicineBatchObject = new MedicineBatch();
                
                $updatedMedicineIDs = [];
                foreach ($data['medicine_name'] as $index => $medicineID) {
                    $batchID = $data['batch_number'][$index];
                    $updatedMedicineIDs[] = "{$medicineID}_{$batchID}";
                }
                
                // Construct existing medicine identifiers (medicine_id + batch_id)
                $existingMedicineIDs = [];
                foreach ($orderMedicineObject->getMedicineAndBatchIdsByOrder($orderID) as $medicine) {
                    $existingMedicineIDs[] = "{$medicine['medicine_id']}_{$medicine['batch_id']}";
                }
                
                // Find removed medicines
                $removedMedicineIDs = array_diff($existingMedicineIDs, $updatedMedicineIDs);
                // header('Content-Type: application/json');
                // echo json_encode(["removed" => $removedMedicineIDs]);
                // exit;

            
                
                // Remove medicines that were deleted by the user and update stock levels
                foreach ($removedMedicineIDs as $medicineBatchKey) {
                    // Split composite key into medicine_id and batch_id
                    list($medicineID, $batchID) = explode('_', $medicineBatchKey);
                
                    // Get the quantity for stock adjustment
                    $quantity = $orderMedicineObject->getQuantity($orderID, $medicineID, $batchID);
                
                    // Adjust stock levels
                    $medicineBatchObject->adjustStockLevel($medicineID, $batchID, $quantity);
                
                    // Delete the record
                    $orderMedicineObject->delete([
                        'order_id' => $orderID,
                        'medicine_id' => $medicineID,
                        'batch_id' => $batchID
                    ]);
                }

                
                foreach ($data['medicine_name'] as $index => $medicineName) {
                    $medicineID = $data['medicine_name'][$index] ?? null;
                    $batchID = $data['batch_number'][$index];
                    $quantity = $data['quantity'][$index];
                
                    if ($medicineID) {
                        // Check if this medicine and batch combination already exists
                        $existingOrderMedicine = $orderMedicineObject->getOrderMedicine($orderID, $medicineID, $batchID);
                
                        if ($existingOrderMedicine) {
                            // Update existing record
                            $previousQuantity = $existingOrderMedicine['quantity'];
                            $quantityDifference = $quantity - $previousQuantity;
                
                            // Update stock levels for the batch
                            $medicineBatchObject->adjustStockLevel($medicineID, $batchID, -$quantityDifference);
                
                            // Update the order_medicine record
                            $orderMedicineObject->update([
                                'order_id' => $orderID,
                                'medicine_id' => $medicineID,
                                'batch_id' => $batchID,
                                'quantity' => $quantity,
                                'unit_price' => $medicineObject->getMedicineUnitPrice($medicineID)['unit_price'],
                                'total_price' => $quantity * $medicineObject->getMedicineUnitPrice($medicineID)['unit_price'],
                            ]);
                        } else {
                            // Insert new record for this medicine and batch combination
                            $medicineBatchObject->adjustStockLevel($medicineID, $batchID, -$quantity);
                
                            $orderMedicineObject->updateOrInsert([
                                'order_id' => $orderID,
                                'medicine_id' => $medicineID,
                                'batch_id' => $batchID,
                                'quantity' => $quantity,
                                'unit_price' => $medicineObject->getMedicineUnitPrice($medicineID)['unit_price'],
                                'total_price' => $quantity * $medicineObject->getMedicineUnitPrice($medicineID)['unit_price'],
                            ]);
                        }
                    }
                }
                
        
                $this->db->commit();
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                return;
            } catch (Exception $e) {
                $this->db->rollBack();
                error_log($e->getMessage());
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'errors' => ["Failed to update the order."]]);
                return;
            }
        }

        $orderObject = new Order();
        $orderMedicineObject = new OrderMedicine();
        $customerObject = new Customer();

        $customerData = $customerObject->getAllCustomers();
        $orderData = $orderMedicineObject->getOrderData($data[1]);

        // Used when medicine is deleted leaving the order an empty slate (orderData is empty as well).
        $ordersFallback = $orderObject->getOrder($data[1]);

        $medicineBatchObject = new MedicineBatch();
        foreach ($orderData as &$order) {
            $medicineId = $order["medicine_id"];
            $batchId = $order["batch_id"];
        
            // Fetch stock level for each medicine and add it to the array
            $order["stock_level"] = $medicineBatchObject->getMedicineBatchStock($medicineId, $batchId)["stock_level"];
        }

        echo $this->twig->render('update-order.html.twig', [
            'orderData' => $orderData,
            'customers' => $customerData,
            'orders' => $ordersFallback
        ]);
    }

    public function addOrderMedicine($data) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
            $errors = $this->validateOrderData($data);

            // POST-Redirect-GET Pattern
            if (!empty($errors)) {
                $_SESSION['order_errors'] = $errors;
                // Redirect to the same route with GET (to prevent resubmission)
                header('Location: /add-order');
                exit;
            }

            try {
                $this->db->beginTransaction();
    
                // Step 1: Add the order to the `orders` table
                $orderID = $this->addOrderRecord($data);
    
                // Step 2: Add medicines to `order_medicine` table
                foreach ($data['medicine_name'] as $index => $medicineID) {
                    $quantity = $data['quantity'][$index];
                    $batchNumber = $data['batch_number'][$index];
    
                    // Calculate total price (you might replace this with actual price retrieval)
                    $unitPrice = $this->getMedicinePrice($medicineID); 
                    $totalPrice = $quantity * $unitPrice;
    
                    // Save to order_medicine
                    (new OrderMedicine())->save([
                        'order_id' => $orderID,
                        'medicine_id' => $medicineID,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'batch_id' => $batchNumber
                    ]);
    
                    // Step 3: Update stock level in `medicine_batch`
                    $this->updateStockLevel($medicineID, $batchNumber, $quantity);
                }
    
                $this->db->commit();
    
                // Redirect to the order list
                header("Location: /order-list");
                exit;
    
            } catch (Exception $e) {
                $this->db->rollBack();
                error_log($e->getMessage());
                $errors[] = "Failed to save order: " . $e->getMessage();
                $this->display($errors);
            }
        }

        $errors = isset($_SESSION['order_errors']) ? $_SESSION['order_errors'] : [];
    
        // Render the template with errors, if any
        $this->display($errors);
        
        // Clear the errors from session after they are displayed
        unset($_SESSION['order_errors']);
    }

    private function addOrderRecord($data) {
        $orderObject = new Order();


        $orderID = $orderObject->save([
            'date' => $data['order_date'],
            'product_count' => count($data['medicine_name']),
            'total_cost' => array_sum(array_map(function ($quantity, $medicineID) {
                $unitPrice = $this->getMedicinePrice($medicineID);
                return $quantity * $unitPrice;
            }, $data['quantity'], $data['medicine_name'])),
            'payment_status' => $data['payment_status'],
            'order_status' => $data['order_status'],
            'customer_id' => $data['customer']
        ]);
    
        return $orderID;
    }

    private function updateStockLevel($medicineID, $batchNumber, $quantity) {
        $medicineBatchModel = new MedicineBatch();
    
        $result = $medicineBatchModel->decreaseStockLevel($medicineID, $batchNumber, $quantity);
    
        if (!$result) {
            throw new Exception("Insufficient stock for Medicine ID: $medicineID, Batch: $batchNumber");
        }
    }
    
    private function getMedicinePrice($medicineID) {
        $medicineModel = new Medicine();
        return $medicineModel->getMedicine($medicineID)['unit_price'];
    }

    public function medicineList($type) {

        $medicineObject = new Medicine();
        $medicineData = $medicineObject->getAllMedicinesByType($type);

        header('Content-Type: application/json');
        echo json_encode($medicineData);
        exit;
    }

    public function batchList($medicine_id) {
        $batchObject = new MedicineBatch();
        $batchData = $batchObject->getMedicineBatches($medicine_id);

        header('Content-Type: application/json');
        echo json_encode($batchData);
        exit;
    }

    public function medicineBatchData($medicine_id, $batch_id) {
        try {
            $batchObject = new MedicineBatch();
            $batchData = $batchObject->getMedicineBatchData($medicine_id, $batch_id);
    
            if (!$batchData) {
                throw new Exception('Batch data not found');
            }
    
            $medicineObject = new Medicine();
            $medicineData = $medicineObject->getMedicine($medicine_id);
    
            if (!$medicineData) {
                throw new Exception('Medicine data not found');
            }
    
            $response = [
                'stock_level' => $batchData[0]['stock_level'],
                'expiry_date' => $batchData[0]['expiry_date'],
                'medicine_data' => $medicineData,
            ];

            header('Content-Type: application/json');
            echo json_encode($response);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    public function updateOrderStatus() { 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['order_id']) && isset($_POST['order_status'])) {
                $order_id = (int)$_POST['order_id'];
                $new_status = trim($_POST['order_status']);

                $orderObject = new Order();
    
                // Call the model's updateStatus method
                try {
                    $affectedRows = $orderObject->updateOrderStatus($order_id, $new_status);
    
                    if ($affectedRows > 0) {
                        // Redirect or return a success message
                        header("Location: /order-list");
                        exit();
                    } else {
                        // Handle case where no rows were affected (e.g., invalid order_id)
                        throw new Exception("Order status update failed.");
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage());
                    echo "Error: " . $e->getMessage();
                }
            } else {
                // Handle missing fields in the POST request
                echo "Error: Missing order ID or order status.";
            }
        }
    }

    public function updateOrderPaymentStatus() { 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['order_id']) && isset($_POST['payment_status'])) {
                $order_id = (int)$_POST['order_id'];
                $new_status = trim($_POST['payment_status']);

                $orderObject = new Order();
    
                // Call the model's updateStatus method
                try {
                    $affectedRows = $orderObject->updateOrderPaymentStatus($order_id, $new_status);
    
                    if ($affectedRows > 0) {
                        // Redirect or return a success message
                        header("Location: /order-list");
                        exit();
                    } else {
                        // Handle case where no rows were affected (e.g., invalid order_id)
                        throw new Exception("Order payment status update failed.");
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage());
                    echo "Error: " . $e->getMessage();
                }
            } else {
                // Handle missing fields in the POST request
                echo "Error: Missing order ID or order status.";
            }
        }
    }


    private function validateUpdatedOrderData($data) {
        $errors = [];
    
        // Validate required fields for order data
        if (empty($data['order_date']) || empty($data['customer'])) {
            $errors[] = "Order date and customer are required.";
        }
    
        // Validate order date
        if (!empty($data['order_date'])) {
            $order_date = str_replace(' - ', ' ', $data['order_date']);
            if (!strtotime($order_date)) {
                $errors[] = "Order date is invalid.";
            }
        }
    
        // Validate payment status
        if (empty($data['payment_status'])) {
            $errors[] = "Payment status is required.";
        } elseif (!in_array($data['payment_status'], ['paid', 'unpaid', 'failed'], true)) {
            $errors[] = "Payment status must be 'paid', 'unpaid', or 'failed'.";
        }
    
        // Validate order status
        if (empty($data['order_status'])) {
            $errors[] = "Order status is required.";
        }
    
        // Validate each medicine's data
        foreach ($data['medicine_name'] as $index => $medicineName) {
            // Check if required fields are provided for each medicine entry
            if (empty($medicineName)) {
                $errors[] = "Medicine name for item " . ($index + 1) . " is required.";
            }
    
            if (empty($data['quantity'][$index])) {
                $errors[] = "Quantity for item " . ($index + 1) . " is required.";
            }
    
            if (empty($data['batch_number'][$index])) {
                $errors[] = "Batch number for item " . ($index + 1) . " is required.";
            }
    
            // Validate quantity (must be numeric and positive)
            if (isset($data['quantity'][$index]) && $data['quantity'][$index] !== '') {
                if (!is_numeric($data['quantity'][$index]) || $data['quantity'][$index] <= 0) {
                    $errors[] = "Quantity for item " . ($index + 1) . " must be a positive number.";
                }
            }
    
            // Validate medicine type
            if (empty($data['medicine_type'][$index])) {
                $errors[] = "Medicine type for item " . ($index + 1) . " is invalid.";
            }
        }
    
        return $errors;
    }
    

    private function validateOrderData($data)
    {
        $errors = [];

        // Validate Order Date
        if (empty($data['order_date'])) {
            $errors['order_date'] = 'Order date is required.';
        } 

        // Validate order date
        if (!empty($data['order_date'])) {
            $order_date = str_replace(' - ', ' ', $data['order_date']);
            if (!strtotime($order_date)) {
                $errors['order_date'] = 'Invalid order date format.';
            }
        }

        // Validate Customer
        if (empty($data['customer']) || $data['customer'] === 'new') {
            $errors['customer'] = 'Please select an existing customer or add a new one.';
        }

        // Validate Payment Status
        $validPaymentStatuses = ['paid', 'pending', 'failed'];
        if (empty($data['payment_status']) || !in_array($data['payment_status'], $validPaymentStatuses, true)) {
            $errors['payment_status'] = 'Invalid payment status.';
        }

        // Validate Order Status
        $validOrderStatuses = ['processing', 'completed', 'backordered', 'failed', 'canceled'];
        if (empty($data['order_status']) || !in_array($data['order_status'], $validOrderStatuses, true)) {
            $errors['order_status'] = 'Invalid order status.';
        }

        // Validate Medicine Items
        if (empty($data['medicine_type']) || empty($data['medicine_name']) || empty($data['batch_number']) || empty($data['quantity'])) {
            $errors['items'] = 'All medicine item fields are required.';
        } else {
            foreach ($data['quantity'] as $index => $type) {
                // Medicine Type
                if (empty($type)) {
                    $errors["medicine_type_{$index}"] = "Medicine type is required for item #" . ($index + 1);
                }

                // Medicine Name
                if (empty($data['medicine_name'][$index])) {
                    $errors["medicine_name_{$index}"] = "Medicine name is required for item #" . ($index + 1);
                }

                // Batch Number
                if (empty($data['batch_number'][$index])) {
                    $errors["batch_number_{$index}"] = "Batch number is required for item #" . ($index + 1);
                }

                // Quantity
                $quantity = $data['quantity'][$index];
                if (empty($quantity)) {
                    $errors["quantity_{$index}"] = "Quantity is required for item #" . ($index + 1);
                } elseif (!is_numeric($quantity) || $quantity <= 0) {
                    $errors["quantity_{$index}"] = "Quantity must be a positive number for item #" . ($index + 1);
                }
            }
        }

        return $errors;
    }

}