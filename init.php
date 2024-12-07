<?php

    use Lib\DatabaseConnection;
    use Twig\Loader\FilesystemLoader;


    // Initialize Twig
    $loader = new FilesystemLoader([
        __DIR__ . '/src/views/pages',
        __DIR__ . '/src/views/pages/dashboard',
        __DIR__ . '/src/views/pages/purchase',
        __DIR__ . '/src/views/pages/order',
        __DIR__ . '/src/views/pages/inventory',
        __DIR__ . '/src/views/pages/inventory/raw',
        __DIR__ . '/src/views/pages/medicine',
        __DIR__ . '/src/views/pages/medicine/batch',
        __DIR__ . '/src/views/pages/medicine/rack',
        __DIR__ . '/src/views/pages/settings',
        __DIR__ . '/src/views/components',
        __DIR__ . '/src/views/pages/customer',
        __DIR__ . '/src/views/pages/supplier',
        __DIR__ . '/src/views/pages/user',
        __DIR__ . '/src/views'
    ]);

    $dotenv = Dotenv\Dotenv::createMutable(__DIR__);
    $dotenv->load();

    // Initialize Database Connection
    $db_type = $_ENV['DB_CONNECTION'];
    $db_host = $_ENV['DB_HOST'];
    $db_port = $_ENV['DB_PORT'];
    $db_name = $_ENV['DB_DATABASE'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];

    $db = new DatabaseConnection(
        $db_type,
        $db_host,
        $db_port,
        $db_name,
        $db_username,
        $db_password
    );
    $conn = $db->connect();