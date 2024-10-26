<?php

namespace App\Controllers;

class PurchaseController {
    protected $twig;

    // Constructor to pass Twig environment for rendering templates
    public function __construct($twig) {
        $this->twig = $twig;
    }

    function display() {
        echo $this->twig->render('purchase-list.html.twig');
    }

    function addPurchaseDisplay() {
        echo $this->twig->render('add-purchase.html.twig');
    }
}