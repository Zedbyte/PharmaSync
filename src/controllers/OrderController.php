<?php

namespace App\Controllers;

use App\Models\OrderMedicine;
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
        // if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {

        // }

        $orderMedicineObject = new OrderMedicine();
        $orderMedicineData = $orderMedicineObject->getAllOrderMedicines();

        // var_dump($orderMedicineData);exit;

        echo $this->twig->render('order-list.html.twig', [
            'ASSETS_URL' => ASSETS_URL,
            'orderMedicines' => $orderMedicineData
        ]);
    }
}