<?php

require_once "vendor/autoload.php";
require_once "init.php";

// Dependencies

use Klein\Klein as Route;
use Twig\Environment;

// Controllers
use App\Controllers\LoginController;
use App\Controllers\RegistrationController;
use App\Controllers\DashboardController;
use App\Controllers\SettingsController;
use App\Controllers\PurchaseController;
use App\Controllers\OrderController;
use \App\Middleware\AuthMiddleware;

try {

    // Initialize Klein router
    $router = new Route();
    //$loader came from init.php
    $twig = new Environment($loader);

    $loginController = new LoginController($twig);
    $registrationController = new RegistrationController($twig);
    $dashboardController = new DashboardController($twig);
    $settingsController = new SettingsController($twig);
    $purchaseController = new PurchaseController($twig);
    $orderController = new OrderController($twig);

    // Landing Page
    $router->respond('GET', '/', function() use ($loginController) {
        $loginController->display();
    });

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

    // Registration Page [GET]
    $router->respond('GET', '/registration', function() use ($registrationController) {
        $registrationController->display();
    });

    // Registration Page [POST]
    $router->respond('POST', '/register', function() use ($registrationController) {
        $registrationController->register();
    });

    // Dashboard Page [GET]
    $router->respond('GET', '/dashboard', function() use ($dashboardController) {
        AuthMiddleware::checkAuth();
        $dashboardController->display();
    });

    // Settings Page [GET]
    $router->respond('GET', '/settings', function() use ($settingsController) {
        AuthMiddleware::checkAuth();
        $settingsController->display();
    });

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

    // Update Purchase Status [POST]
    $router->respond('POST', '/update-purchase-status', function() use ($purchaseController) {
        AuthMiddleware::checkAuth();
        $purchaseController->updatePurchaseStatus();
    });


    // Order Page [GET]
    $router->respond('GET', '/order-list', function() use ($orderController) {
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


    // Delete Order [POST]
    $router->respond('POST', '/delete-order/[i:orderID]', function($request) use ($orderController) {
        AuthMiddleware::checkAuth();
        $orderID = $request->param('orderID');
        $orderController->deleteOrder($orderID);
    });
    
    $router->dispatch();

} catch (Exception $e) {

    echo $e->getMessage();

}