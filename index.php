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

    // Dashboard Page
    $router->respond('GET', '/dashboard', function() use ($dashboardController) {
        AuthMiddleware::checkAuth();
        $dashboardController->display();
    });

    // Settings Page
    $router->respond('GET', '/settings', function() use ($settingsController) {
        AuthMiddleware::checkAuth();
        $settingsController->display();
    });

    // Purchase Page
    $router->respond('GET', '/purchase-list', function() use ($purchaseController) {
        AuthMiddleware::checkAuth();
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