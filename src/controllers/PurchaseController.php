<?php

namespace App\Controllers;
require_once __DIR__ . '/../../config/config.php';

class PurchaseController {
    protected $twig;

    // Constructor to pass Twig environment for rendering templates
    public function __construct($twig) {
        $this->twig = $twig;
    }

    function display() {
        echo $this->twig->render('purchase-list.html.twig', ['BASE_URL' => BASE_URL]);
    }

    // function addPurchaseDisplay() {
    //     echo $this->twig->render('add-purchase.html.twig', ['BASE_URL' => BASE_URL]);
    // }
}