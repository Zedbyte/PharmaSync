<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Inventory;

require_once __DIR__ . '/../../config/config.php';
require_once 'BaseController.php';

class InventoryController extends BaseController 
{

    protected $twig;
    protected $db;

    // Constructor to initialize Twig and BaseController
    public function __construct($twig) {
        parent::__construct();  // Initializes session management in BaseController
        $this->twig = $twig;
    }

    public function getInventoryDistribution() {
        $inventoryObject = new Inventory();
        $inventoryData = $inventoryObject->getInventoryDistribution();
        echo json_encode($inventoryData);
    }

}