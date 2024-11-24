<?php

require_once "vendor/autoload.php";
require_once "init.php";

// Dependencies

use App\Controllers\BatchesController;
use Klein\Klein as Route;
use Twig\Environment;

// Controllers
use App\Controllers\LoginController;
use App\Controllers\RegistrationController;
use App\Controllers\DashboardController;
use App\Controllers\SettingsController;
use App\Controllers\PurchaseController;
use App\Controllers\OrderController;
use App\Controllers\ManufacturedController;
use App\Controllers\RawController;
use \App\Middleware\AuthMiddleware;

try {

    // Initialize Klein router
    $router = new Route();
    //$loader came from init.php
    $twig = new Environment($loader);

    $loginController = new LoginController($twig);
    $registrationController = new RegistrationController($twig);
    $dashboardController = new DashboardController($twig);
    $batchesController = new BatchesController($twig);
    $settingsController = new SettingsController($twig);
    $purchaseController = new PurchaseController($twig);
    $orderController = new OrderController($twig);
    $manufacturedController = new ManufacturedController($twig);
    $rawController = new RawController($twig);

    // Landing Page
    $router->respond('GET', '/', function() use ($loginController) {
        $loginController->display();
    });

    /**
     * 
     * LOGIN
     * 
     */

    // Login Page [GET]
    $router->respond('GET', '/login', function() use ($loginController) {
        $loginController->display();
    });

    // Login Page [POST]
    $router->respond('POST', '/login', function() use ($loginController) {
        $loginController->login();
    });

    // Login Page [GET]
    $router->respond('GET', '/logout', function() use ($loginController) {
        AuthMiddleware::checkAuth();
        $loginController->logout();
    });

    /**
     * 
     * REGISTRATION
     * 
     */

    // Registration Page [GET]
    $router->respond('GET', '/registration', function() use ($registrationController) {
        $registrationController->display();
    });

    // Registration Page [POST]
    $router->respond('POST', '/register', function() use ($registrationController) {
        $registrationController->register();
    });

    /**
     * 
     * DASHBOARD
     * 
     */

    // Dashboard Page [GET]
    $router->respond('GET', '/dashboard', function() use ($dashboardController) {
        AuthMiddleware::checkAuth();
        $dashboardController->display();
    });


    $router->respond('GET', '/dashboard/production-rate', function() use ($batchesController) {
        AuthMiddleware::checkAuth();
        $batchesController->getProductionRate();
    });

    // $router->respond('GET', '/dashboard/inventory-distribution', function() use ($batchesController) {
    //     AuthMiddleware::checkAuth();
    //     $batchesController->getInventoryDistribution();
    // });

    /**
     * 
     * SETTINGS
     * 
     */

    // Settings Page [GET]
    $router->respond('GET', '/settings', function() use ($settingsController) {
        AuthMiddleware::checkAuth();
        $settingsController->display();
    });

    /** 
     * 
     *  PURCHASES
     * 
    */

    // Purchase Page [GET]
    $router->respond('GET', '/purchase-list', function() use ($purchaseController) {
        AuthMiddleware::checkAuth();
        $purchaseController->display();
    });

    // Purchase List Filter Update [POST]
    $router->respond('POST', '/purchase-list/filter', function() use ($purchaseController) {
        AuthMiddleware::checkAuth();
        $purchaseController->display();
    });

    // Add Purchase Panel [POST]
    $router->respond('POST', '/add-purchase', function($request) use ($purchaseController) {
        AuthMiddleware::checkAuth();
        $data = $request->params();
        $purchaseController->addPurchaseMaterial($data);
    });

    // Add Purchase Panel [GET]
    $router->respond('GET', '/add-purchase', function($request) use ($purchaseController) {
        AuthMiddleware::checkAuth();
        $data = $request->params();
        $purchaseController->addPurchaseMaterial($data);
    });

    // Add Purchase Panel [POST]
    $router->respond('POST', '/add-purchase-existing', function($request) use ($purchaseController) {
        AuthMiddleware::checkAuth();
        $data = $request->params();
        $purchaseController->addPurchaseMaterialExisting($data);
    });

    $router->respond('GET', '/add-purchase-existing/material', function($request) use ($purchaseController) {
        AuthMiddleware::checkAuth();
        $type = $_GET['type'];
        $purchaseController->updateMaterialDropList($type);
    });

    // View Purchase Panel [GET]
    $router->respond('GET', '/view-purchase/[i:purchaseID]', function($purchaseID) use ($purchaseController) {
        AuthMiddleware::checkAuth();
        $data = $purchaseID->params();
        $purchaseController->viewPurchase($data);
    });

    // Update Purchase [GET]
    $router->respond('GET', '/update-purchase/[i:purchaseID]', function($purchaseID) use ($purchaseController) {
        AuthMiddleware::checkAuth();
        $data = $purchaseID->params();
        $purchaseController->updatePurchase($data);
    });

    // Update Purchase [POST]
    $router->respond('POST', '/update-purchase/[i:purchaseID]', function($request) use ($purchaseController) {
        AuthMiddleware::checkAuth();
        $data = $request->params();
        $purchaseController->updatePurchase($data);
    });

    // Delete Purchase [POST]
    $router->respond('POST', '/delete-purchase/[i:purchaseID]', function($request) use ($purchaseController) {
        AuthMiddleware::checkAuth();
        $purchaseID = $request->param('purchaseID');
        $purchaseController->deletePurchase($purchaseID);
    });

    // Delete Purchase [GET]
    $router->respond('GET', '/delete-purchase', function($request) use ($purchaseController) {
        AuthMiddleware::checkAuth();
        $purchaseController->deletePurchase(null);
    });

    // Update Purchase Status [POST]
    $router->respond('POST', '/update-purchase-status', function() use ($purchaseController) {
        AuthMiddleware::checkAuth();
        $purchaseController->updatePurchaseStatus();
    });

    /**
     * 
     * ORDERS
     * 
     */

    // Order Page [GET]
    $router->respond('GET', '/order-list', function() use ($orderController) {
        AuthMiddleware::checkAuth();
        $orderController->display();
    });

    // Order List Filter Update [POST]
    $router->respond('POST', '/order-list/filter', function() use ($orderController) {
        AuthMiddleware::checkAuth();
        $orderController->display();
    });

    // Order Page Medicine Name By Type [GET]
    $router->respond('GET', '/order-list/medicines', function($request) use ($orderController) {
        AuthMiddleware::checkAuth();
        // $type = $request->param('type');
        $type = $_GET['type'] ?? null;
        $orderController->medicineList($type);
    });

    // Order Page Batch Number by Medicine ID [GET]
    $router->respond('GET', '/order-list/medicines/[:medicine_id]/batches', function($request) use ($orderController) {
        AuthMiddleware::checkAuth();
        $medicine_id = $request->param('medicine_id');
        $orderController->batchList($medicine_id);
    });

    // Order Page Batch Number by Medicine ID [GET]
    $router->respond('GET', '/order-list/medicines/[:medicine_id]/batches/[:batch_id]', function($request) use ($orderController) {
        AuthMiddleware::checkAuth();
        $medicine_id = $request->param('medicine_id');
        $batch_id = $request->param('batch_id');
        $orderController->medicineBatchData($medicine_id, $batch_id);
    });

    // Add Order Panel [POST]
    $router->respond('POST', '/add-order', function($request) use ($orderController) {
        AuthMiddleware::checkAuth();
        $data = $request->params();
        $orderController->addOrderMedicine($data);
    });

    // Add Order Panel [GET]
    $router->respond('GET', '/add-order', function($request) use ($orderController) {
        AuthMiddleware::checkAuth();
        $data = $request->params();
        $orderController->addOrderMedicine($data);
    });

    // View Order Panel [GET]
    $router->respond('GET', '/view-order/[i:orderID]', function($orderID) use ($orderController) {
        AuthMiddleware::checkAuth();
        $data = $orderID->params();
        $orderController->viewOrder($data);
    });

    // Update Order [POST]
    $router->respond('POST', '/update-order/[i:orderID]', function($orderID) use ($orderController) {
        AuthMiddleware::checkAuth();
        $data = $orderID->params();
        $orderController->updateOrder($data);
    });

    // Update Order [GET]
    $router->respond('GET', '/update-order/[i:orderID]', function($orderID) use ($orderController) {
        AuthMiddleware::checkAuth();
        $data = $orderID->params();
        $orderController->updateOrder($data);
    });

    // Delete Order [POST]
    $router->respond('POST', '/delete-order/[i:orderID]', function($request) use ($orderController) {
        AuthMiddleware::checkAuth();
        $orderID = $request->param('orderID');
        $orderController->deleteOrder($orderID);
    });

    // Update Order Status [POST]
    $router->respond('POST', '/update-order/status', function() use ($orderController) {
        AuthMiddleware::checkAuth();
        $orderController->updateOrderStatus();
    });

    // Update Order Payment Status [POST]
    $router->respond('POST', '/update-order/payment/status', function() use ($orderController) {
        AuthMiddleware::checkAuth();
        $orderController->updateOrderPaymentStatus();
    });

    /**
     * 
     * INVENTORY MANUFACTURED
     * 
     */

    // Manufactured Page [GET]
    $router->respond('GET', '/inventory/manufactured', function() use ($manufacturedController) {
        AuthMiddleware::checkAuth();
        $batchSearch = $_GET['q'] ?? null;
        $manufacturedController->display(null, $batchSearch);
    });

    // Add Manufactured Batch [POST]
    $router->respond('POST', '/add-batch', function() use ($manufacturedController) {
        AuthMiddleware::checkAuth();
        $manufacturedController->addBatch();
    });

    // Add Manufactured Batch [GET]
    $router->respond('GET', '/add-batch', function() use ($manufacturedController) {
        AuthMiddleware::checkAuth();
        $manufacturedController->addBatch();
    });

    // Get Rack Details [GET]
    $router->respond('GET', '/rack-details/[i:rackID]', function($request) use ($manufacturedController) {
        AuthMiddleware::checkAuth();
        $batchID = $request->param('rackID');
        $manufacturedController->getRackData($batchID);
    });

    // Delete Batch [POST]
    $router->respond('POST', '/delete-batch/[i:medicineID]/[i:batchID]', function($request) use ($manufacturedController) {
        AuthMiddleware::checkAuth();
        $medicineID = $request->param('medicineID');
        $batchID = $request->param('batchID');
        $manufacturedController->deleteBatch($medicineID, $batchID);
    });

    // Get Rack Details [GET]
    $router->respond('GET', '/batch-details/[i:batchID]', function($request) use ($manufacturedController) {
        AuthMiddleware::checkAuth();
        $batchID = $request->param('batchID');
        $manufacturedController->getBatchDetails($batchID);
    });

    // Add Manufactured to Existing Batch [POST]
    $router->respond('POST', '/add-existing-batch', function($request) use ($manufacturedController) {
        AuthMiddleware::checkAuth();
        $manufacturedController->addToExistingBatch();
    });

    // Add Manufactured to Existing Batch [GET]
    $router->respond('GET', '/add-existing-batch', function($request) use ($manufacturedController) {
        AuthMiddleware::checkAuth();
        $manufacturedController->addToExistingBatch();
    });

    $router->respond('GET', '/add-existing-batch/batch/[i:batchID]/medicines', function($request) use ($manufacturedController) {
        AuthMiddleware::checkAuth();
        $type = $_GET['type'] ?? null;
        $batchID = $request->param('batchID');
        $manufacturedController->medicineListExisting($type, $batchID);
    });

    $router->respond('GET', '/add-existing-batch/medicines', function($request) use ($manufacturedController) {
        AuthMiddleware::checkAuth();
        $type = $_GET['type'] ?? null;
        $manufacturedController->medicineList($type);
    });

    // Update Batch [GET]
    $router->respond('GET', '/update-batch/medicine/[i:medicineID]/batch/[i:batchID]', function($request) use ($manufacturedController) {
        AuthMiddleware::checkAuth();
        $data = $request->params();
        $manufacturedController->updateBatch($data);
    });

    // Update Batch [POST]
    $router->respond('POST', '/update-batch/medicine/[i:medicineID]/batch/[i:batchID]', function($request) use ($manufacturedController) {
        AuthMiddleware::checkAuth();
        $data = $request->params();
        $manufacturedController->updateBatch($data);
    });

    // Update Batch [GET]
    $router->respond('GET', '/view-batch/[i:batchID]/medicine/[i:medicineID]', function($request) use ($manufacturedController) {
        AuthMiddleware::checkAuth();
        $medicineID = $request->param('medicineID');
        $batchID = $request->param('batchID');
        $manufacturedController->viewBatch($medicineID, $batchID);
    });

    /**
     * 
     * INVENTORY RAW
     * 
     */

    // Manufactured Page [GET]
    $router->respond('GET', '/inventory/raw', function() use ($rawController) {
        AuthMiddleware::checkAuth();
        $lotSearch = $_GET['q'] ?? null;
        $rawController->display();
    });

    $router->dispatch();

} catch (Exception $e) {

    echo $e->getMessage();

}