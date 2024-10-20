<?php

require_once "vendor/autoload.php";

// Dependencies

use App\controllers\DashboardController;
use Klein\Klein as Route;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// Controllers
use App\controllers\LoginController;

try {

    // Initialize Klein router
    $router = new Route();

    // Initialize Twig
    $loader = new FilesystemLoader([
        __DIR__ . '\src\views\pages',
        __DIR__ . '\src\views\components',
        __DIR__ . '\src\views'
    ]);
    
    $twig = new Environment($loader);

    $loginController = new LoginController($twig);
    $dashboardController = new DashboardController($twig);

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

    $router->dispatch();

} catch (Exception $e) {

    echo $e->getMessage();

}