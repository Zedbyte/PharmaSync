<?php

require_once "vendor/autoload.php";
require_once "init.php";

// Dependencies

use Klein\Klein as Route;
use Twig\Environment;

// Controllers
use App\Controllers\LoginController;
use App\Controllers\DashboardController;
use App\Controllers\SettingsController;
use App\Controllers\PurchaseController;
use \App\Middleware\AuthMiddleware;

try {

    // Initialize Klein router
    $router = new Route();
    //$loader came from init.php
    $twig = new Environment($loader);

    $loginController = new LoginController($twig);
    $dashboardController = new DashboardController($twig);
    $settingsController = new SettingsController($twig);
    $purchaseController = new PurchaseController($twig);

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
    
    $router->dispatch();

} catch (Exception $e) {

    echo $e->getMessage();

}