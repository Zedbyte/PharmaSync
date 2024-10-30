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
    // $router->respond('GET', '/', function() use ($loginController) {
    //     $loginController->display();
    // });

    // Login Page
    $router->respond('GET', '/', function() use ($loginController) {
        $loginController->display();
    });

    // Dashboard Page
    $router->respond('GET', '/dashboard', function() use ($dashboardController) {
        $dashboardController->display();
    });

    // Settings Page
    $router->respond('GET', '/settings', function() use ($settingsController) {
        $settingsController->display();
    });

    // Purchase Page
    $router->respond('GET', '/purchase-list', function() use ($purchaseController) {
        $purchaseController->display();
    });

    // Add Purchase Page
    // $router->respond('GET', '/add-purchase', function() use ($purchaseController) {
    //     $purchaseController->addPurchaseDisplay();
    // });

    $router->dispatch();

} catch (Exception $e) {

    echo $e->getMessage();

}