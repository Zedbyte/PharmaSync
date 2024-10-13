<?php

require_once "vendor/autoload.php";

// Dependencies
use Klein\Klein as Route;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// Controllers
use App\controllers\LoginController;

try {

    // Initialize Klein router
    $router = new Route();

    // Initialize Twig
    $loader = new FilesystemLoader(__DIR__ . '\src\views\pages');
    $twig = new Environment($loader);

    $loginController = new LoginController($twig);

    $router->respond('GET', '/', function() use ($loginController) {
        $loginController->display();  // Calls the method in LoginController to render the login form
    });

    $router->dispatch();

} catch (Exception $e) {

    echo $e->getMessage();

}